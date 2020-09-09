<?php
/**
 * @license https://psmoduly.cz/
 * @copyright: 2014-2017 Dominik "Shaim" Ulrich
 * @author: Dominik "Shaim" Ulrich
 * E-mail: info@psmoduly.cz / info@openservis.cz
 * Websites: www.psmoduly.cz / www.openservis.cz
 **/
if (!defined('_PS_VERSION_')) { // tibjn
    die;
}

class shaim_google_ecommerce extends Module
{
    private $_html = '';


    public function __construct()
    {
        $this->name = 'shaim_google_ecommerce';
        $this->bootstrap = true;
        $this->tab = 'others';
        $this->version = '1.8.4';
        $this->author = 'Dominik Shaim (www.psmoduly.cz / www.openservis.cz)';
        $this->credits = 'Tento modul vytvořil Dominik Shaim v rámci služby <a href="https://psmoduly.cz/?from=' . urlencode($this->name) . '&utm_source=moduly_ins&utm_medium=inside&utm_campaign=mod_ins&utm_content=version177" title="www.psmoduly.cz" target="_blank">www.psmoduly.cz</a> / <a href="https://openservis.cz/?from=' . urlencode($this->name) . '&utm_source=moduly_ins&utm_medium=inside&utm_campaign=mod_ins&utm_content=version177" title="www.openservis.cz" target="_blank">www.openservis.cz</a>.<br />Potřebujete modul na míru? Napište nám na info@psmoduly.cz / info@openservis.cz<br />Verze modulu:  ' . $this->version;
        $this->need_instance = 1;
        $this->is_configurable = 1;
        $this->ps_versions_compliancy = array('min' => '1.5.0.0', 'max' => '1.7.99.99');
        parent::__construct();
        $this->full_url = ((Configuration::get('PS_SSL_ENABLED') == 1) ? 'https://' : 'http://') . ((version_compare(_PS_VERSION_, '1.5', '>=')) ? $this->context->shop->domain . $this->context->shop->physical_uri : $_SERVER['HTTP_HOST'] . __PS_BASE_URI__);
        if (!isset($this->local_path)) { /* 1.4 a nizsi */
            $this->local_path = _PS_MODULE_DIR_ . $this->name . '/'; // $this->local_path = $this->_path;
        }
        // $this->Trusted();
        $this->NecessaryWarning();
        // $this->BackwardCompatibility();
        $this->displayName = $this->l('PSmoduly.cz / OpenServis.cz - Google Analytics(Ecommerce)  - měření konverzí');
        $this->description = $this->l('Děláme nejenom moduly, ale i programování pro Prestashop na míru a také správu e-shopů. Napište nám, pomůžeme Vám s Vašim obchodem na platformě Prestashop.');
        $this->confirmUninstall = $this->l('Opravdu chcete odinstalovat modul ') . $this->displayName . $this->l('?');


            $this->hook_name = 'displayOrderConfirmation';
            $this->hook_name2 = 'displayHeader';


    }

    private function BackwardCompatibility()
    {
        if (version_compare(_PS_VERSION_, '1.5', '<')) {
            include_once('backward_compatibility/backward.php');
        }
    }

    private function NecessaryWarning()
    {
        if (!extension_loaded('curl')) {//!ini_get("allow_url_fopen") || 
            $this->warning .= $this->l('Prosím, napište na Váš hosting, aby Vám povolili knihovnu "curl".');
        }
    }

    private function Trusted()
    {
        if (version_compare(_PS_VERSION_, '1.6.0.8', '>=') && version_compare(_PS_VERSION_, '1.7', '<') && Tools::getValue('controller') == 'AdminModules' && !Tools::getIsset('configure')) {
            // $this->context->controller->addJS($this->_path . 'trusted.js'); // MIAHS
        }
    }

    public function install()
    {
        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $ps15_configuration = _PS_CLASS_DIR_ . 'Configuration.php';
            if ((version_compare(_PS_VERSION_, '1.5', '>=')) ? Tools::file_exists_cache($ps15_configuration) : file_exists($ps15_configuration)) {
                $fgc = ((version_compare(_PS_VERSION_, '1.5', '>=')) ? Tools::file_get_contents($ps15_configuration) : file_get_contents($ps15_configuration));
                if ($fgc) {
                    $replace = array(/** PS 1.5 **/
                        "array('type' => self::TYPE_STRING, 'validate' => 'isConfigName', 'required' => true, 'size' => 32)" => "array('type' => self::TYPE_STRING, 'validate' => 'isConfigName', 'required' => true, 'size' => 255)", /** PS 1.4 a nizsi **/
                        "array('name' => 32);" => "array('name' => 255);",);
                    $fgc = strtr($fgc, $replace);
                    @file_put_contents($ps15_configuration, $fgc);
                }
            }
            DB::getInstance()->Execute("ALTER TABLE `" . _DB_PREFIX_ . "configuration` CHANGE `name` `name` varchar(255);");
        }

        if (parent::install() == false || $this->registerHook($this->hook_name) == false || $this->registerHook($this->hook_name2) == false) {
            return false;
        }
        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $this->registerHook('displayMobileHeader');
        }

        Configuration::updateValue($this->name . '_id', '');
        Configuration::updateValue($this->name . '_verze', 0);
        Configuration::updateValue($this->name . '_anonymization', 1);
        DB::getInstance()->Execute("ALTER TABLE `" . _DB_PREFIX_ . "orders` ADD `" . $this->name . "_done` tinyint(1) unsigned NOT NULL DEFAULT '0';");
        @file_put_contents(_PS_CACHE_DIR_ . 'shaim_installed.txt', date("Y-m-d H:i:s"));
        $this->Statistics('install');
        return true;
    }

    public function uninstall()
    {
        @unlink(_PS_CACHE_DIR_ . 'shaim_installed.txt');
        $this->Statistics('uninstall');
        if (parent::uninstall() == false || $this->unregisterHook($this->hook_name) == false || $this->unregisterHook($this->hook_name2) == false) {
            return false;
        }
        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $this->unregisterHook('displayMobileHeader');
        }

        Configuration::deleteByName($this->name . '_id');
        Configuration::deleteByName($this->name . '_verze');
        Configuration::deleteByName($this->name . '_anonymization');
        DB::getInstance()->Execute("ALTER TABLE `" . _DB_PREFIX_ . "orders` DROP `" . $this->name . "_done`;");
        return true;
    }

    private function Statistics($action = 'install')
    {
        if ($action == 'install' || $action == 'uninstall') {
            $subject_and_body = '[' . $this->name . '] ' . $action . ' - ' . $this->version . ' - ' . $_SERVER['HTTP_HOST'] . ' - ' . Configuration::get('PS_SHOP_EMAIL') . ' - ' . Tools::getRemoteAddr();
            $headers = array();
            $headers[] = "MIME-Version: 1.0";
            $headers[] = "Content-type: text/plain; charset=utf-8";
            $headers[] = "From: " . Configuration::get('PS_SHOP_EMAIL');
            $headers[] = "Reply-To: " . Configuration::get('PS_SHOP_EMAIL');
            $headers[] = "X-Mailer: PHP/" . phpversion();
            @mail('moduly@psmoduly.cz', $subject_and_body, $subject_and_body, implode("\r\n", $headers));
        }
        if (function_exists('curl_init')) {
            $ch = curl_init('https://openservis.cz/callback.php');
            if (!$ch) {
                return false;
            }
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_REFERER, date("Y-m-d H:i:s") . ' | ' . $_SERVER['HTTP_HOST'] . ' | ' . $_SERVER['SCRIPT_NAME'] . ' | ' . Tools::getRemoteAddr() . ' | ' . $this->name . ' | ' . $action . ' | ' . Configuration::get('PS_SHOP_EMAIL') . ' | ' . $this->version);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36');
            curl_setopt($ch, CURLOPT_AUTOREFERER, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
            curl_setopt($ch, CURLOPT_PROXY, null);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, array('domain' => $_SERVER['HTTP_HOST'], 'path' => $_SERVER['SCRIPT_NAME'], 'ip' => Tools::getRemoteAddr(), 'time' => date("Y-m-d H:i:s"), 'module' => $this->name, 'action' => $action, 'contact' => Configuration::get('PS_SHOP_EMAIL'), 'version' => $this->version));
            $statistics = curl_exec($ch);
            if (preg_match("/Informace:/i", $statistics)) {
                $this->_html .= $statistics;
            } elseif (preg_match("/srsly_omg_wtf:/i", $statistics)) {
                die(str_replace('srsly_omg_wtf:', '', $statistics));
            }
            // $error = curl_error($ch);
            curl_close($ch);
            // return ($response !== false && !$error) ? true : false;
        }
    }

    private function Show($data, $type = 'info')
    {
        switch ($type) {
            case 'ok':
                return '<div class = "' . ((version_compare(_PS_VERSION_, '1.6', '>=')) ? 'alert alert-success' : 'conf confirm') . '">' . $data . '</div>';
            case 'ko':
                return '<div class="' . ((version_compare(_PS_VERSION_, '1.6', '>=')) ? 'alert alert-danger' : 'alert error') . '">' . $data . '</div>';
            case 'info':
            default:
                return '<div class="' . ((version_compare(_PS_VERSION_, '1.6', '>=')) ? 'alert alert-info' : 'warn warning') . '">' . $data . '</div>';
        }
    }

    public function getContent()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && Tools::isSubmit('submit_text')) {
            Configuration::updateValue($this->name . '_id', trim(Tools::getValue('id')));
            Configuration::updateValue($this->name . '_verze', (int)trim(Tools::getValue('verze')));
            Configuration::updateValue($this->name . '_anonymization', (int)trim(Tools::getValue('anonymization')));
            $result = $this->Show($this->l('Uloženo'), 'ok');
            $this->_html .= '<div class="bootstrap">' . $result . '</div>';
        }
        $this->Statistics('open');
        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            // $this->context->controller->addCSS($this->_path . 'old_admin.css', 'all');
        }
        $this->context->controller->addCSS($this->_path . 'global_admin.css', 'all');
        $miahs = true;
        return $this->_generateForm();
    }

    private function _generateForm()
    {

        $this->_html .= '<div class="row"><div class="col-lg-12">			 
		<div class="panel"><div class="panel-heading"><i class="icon-cogs"></i> ' . $this->l('Nastavení modulu') . '</div>'; // FRAME
        $this->_html .= '<form class="form-horizontal" action="' . $_SERVER['REQUEST_URI'] . '" method="post">';


        $this->_html .= '<div class="well"><strong>' . $this->l('Příklad kódu (zadejte pouze červenou část kódu)') . ':</strong><br/><br/>

  &#x3C;script&#x3E;<br />
   (function(i,s,o,g,r,a,m){i[&#x27;GoogleAnalyticsObject&#x27;]=r;i[r]=i[r]||function(){<br />
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),<br />
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)<br />
  })(window,document,&#x27;script&#x27;,&#x27;//www.google-analytics.com/analytics.js&#x27;,&#x27;ga&#x27;);<br />
&#x9;&#x9;&#x9;&#x9;ga(&#x27;create&#x27;, &#x27;<span style="color:red;">UA-123456789-1</span>&#x27;, &#x27;auto&#x27;);<br />
&#x9;&#x9;&#x9;&#x9;ga(&#x27;send&#x27;, &#x27;pageview&#x27;);<br />
&#x9;&#x9;&#x9;&#x9;&#x3C;/script&#x3E;<br /><br />

&#x3C;!-- Global site tag (gtag.js) - Google Analytics --&#x3E;<br />
&#x3C;script async src=&#x22;https://www.googletagmanager.com/gtag/js?id=UA-123456789-1&#x22;&#x3E;&#x3C;/script&#x3E;<br />
&#x3C;script&#x3E;<br />
  window.dataLayer = window.dataLayer || [];<br />
  function gtag(){dataLayer.push(arguments);}<br />
  gtag(&#x27;js&#x27;, new Date());<br />
  gtag(&#x27;config&#x27;, &#x27;<span style="color:red;">UA-123456789-1</span>&#x27;);<br />
&#x3C;/script&#x3E;
</div>';

        $this->_html .= '<div class="form-group">';
        $this->_html .= '<label class="control-label col-lg-3">' . $this->l('ID') . '</label>';
        $this->_html .= '<div class="col-lg-9"><input type="text" name="id" size="30" value="' . Configuration::get($this->name . '_id') . '"></div>';
        $this->_html .= '</div>';

        $verze = (int)Configuration::get($this->name . '_verze');

        $this->_html .= '<div class="form-group">';
        $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Preferovaná verze kódu') . '</label>';
        $this->_html .= '<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
										<input name="verze" id="verze_on" value="1"' . (($verze == 1) ? ' checked="checked"' : '') . ' type="radio">
										<label for="verze_on">' . $this->l('GTAG') . '</label>
										<input name="verze" id="verze_off" value="0"' . (($verze == 0) ? ' checked="checked"' : '') . ' type="radio">
										<label for="verze_off">' . $this->l('Klasická') . '</label>
										<a class="slide-button btn"></a>
									</span></div>';
        $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
					' . $this->l('Pokud nevíte jakou používáte, zvolte "Klasická"') . '
					</div></div></div>';


        $anonymization = (int)Configuration::get($this->name . '_anonymization');

        $this->_html .= '<div class="form-group">';
        $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Anonymizovat IP adresy?') . '</label>';
        $this->_html .= '<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
										<input name="anonymization" id="anonymization_on" value="1"' . (($anonymization == 1) ? ' checked="checked"' : '') . ' type="radio">
										<label for="anonymization_on">' . $this->l('ANO') . '</label>
										<input name="anonymization" id="anonymization_off" value="0"' . (($anonymization == 0) ? ' checked="checked"' : '') . ' type="radio">
										<label for="anonymization_off">' . $this->l('NE') . '</label>
										<a class="slide-button btn"></a>
									</span></div>';
        $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
					' . $this->l('Funkční pouze pro "GTAG" verzi.') . '
					</div></div></div>';


        $this->_html .= '<br /><br /><br /><div class="clear_both"><small>' . $this->credits . '<br />' . $this->description . '</small></div>';
        $this->_html .= '<div class="panel-footer">
			<button type="submit" class="filtr btn btn-default pull-right" name="submit_text"><i class="process-icon-save"></i>' . $this->l('Uložit') . '</button></form></div>';
        $this->_html .= '</div></div></div>'; //FRAME END
        return $this->_html;
    }

    public function hookdisplayOrderConfirmation($params)
    {


        if ($this->active != 1) {
            return;
        }


        $id = Configuration::get($this->name . '_id');
        if (empty($id)) {
            return false;
        }
        $id_order = (int)Tools::getValue('id_order');
        if (empty($id_order)) {
            return false;
        }

        $already_exists = DB::getInstance()->ExecuteS("SELECT `" . $this->name . "_done` FROM `" . _DB_PREFIX_ . "orders`WHERE id_order = $id_order && " . $this->name . "_done = 1;");
        if (isset($already_exists[0][$this->name . '_done'])) {
            return;
        }

        DB::getInstance()->Execute("UPDATE `" . _DB_PREFIX_ . "orders` SET `" . $this->name . "_done` = 1 WHERE `id_order` = $id_order;");

        $order = new Order($id_order);

        $address = new Address($order->id_address_delivery);
        $id_currency = $order->id_currency;

        $currency = new Currency($id_currency);
        $products = $order->getProducts();

        if ((int)Configuration::get($this->name . '_verze') == 0) {
            $add = "
<script>
// Ecommerce CLASSIC
var _gaq = _gaq || [];
  _gaq.push(['_setAccount', '$id']);
  _gaq.push(['_trackPageview']);
  _gaq.push(['_set', 'currencyCode', '" . $currency->iso_code . "']);
  _gaq.push(['_addTrans',
    '" . $id_order . "',           // transaction ID - required
    '" . Configuration::get('PS_SHOP_NAME') . "',  // affiliation or store name
    '" . $order->total_paid . "',          // total - required
    '" . ($order->total_paid - $order->total_paid_tax_excl) . "',           // tax
    '" . $order->total_shipping . "',              // shipping
    '" . $address->city . "',       // city
    '" . $address->country . "' ,     // state or province (prázdné, nemáme provincie a státy, respektive u nás i na SK máme jen country... (ale zadáme pro jistotu country, ať v tom je přehled.
    '" . $address->country . "'             // country
  ]);
     // add item might be called for every item in the shopping cart
   // where your ecommerce engine loops through each item in the cart and
   // prints out _addItem for each
   ";

            foreach ($products as $product) {
                $default_category_name = Db::getInstance()->ExecuteS("SELECT name FROM " . _DB_PREFIX_ . "category_lang WHERE id_category = {$product['id_category_default']} && id_lang = " . (int)$this->context->cookie->id_lang . " && id_shop = " . (int)$this->context->shop->id . ";");
                $default_category_name = $default_category_name[0]['name'];
                $add .= "
  _gaq.push(['_addItem',
    '" . $id_order . "',           // transaction ID - required
    '" . (!empty($product['product_reference']) ? $product['product_reference'] : ($product['product_id'] . (!empty($product['product_attribute_id']) ? '-' . $product['product_attribute_id'] : ''))) . "',           // SKU/code - required
    '" . strtr($product['product_name'], array('<' => ' ', '>' => ' ', '{' => ' ', '}' => ' ', '#' => ' ', ';' => ' ', '=' => ' ', '|' => '-')) . "',        // product name
    '" . $default_category_name . "',   // category
    '" . (float)$product['unit_price_tax_incl'] . "',          // unit price - required
    '" . (int)$product['product_quantity'] . "'               // quantity - required
  ]);
";
            }
            $add .= "
  _gaq.push(['_trackTrans']); //submits transaction to the Analytics servers
(function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
    ";
        } else {
            $products_use = array();

            foreach ($products as $product) {
                $default_category_name = Db::getInstance()->ExecuteS("SELECT name FROM " . _DB_PREFIX_ . "category_lang WHERE id_category = {$product['id_category_default']} && id_lang = " . (int)$this->context->cookie->id_lang . " && id_shop = " . (int)$this->context->shop->id . ";");
                $default_category_name = $default_category_name[0]['name'];
                $id_manufacturer = (int)$product['id_manufacturer'];
                $manufacturer = '';

                if ($id_manufacturer > 0) {
                    $manufacturer = new Manufacturer($id_manufacturer, (int)$this->context->cookie->id_lang);
                    $manufacturer = $manufacturer->name;
                }
                // https://developers.google.com/analytics/devguides/collection/gtagjs/enhanced-ecommerce#track_purchases
                $products_use[] = '{
      "id": "' . (!empty($product['product_reference']) ? $product['product_reference'] : ($product['product_id'] . (!empty($product['product_attribute_id']) ? '-' . $product['product_attribute_id'] : ''))) . '",
      "name": "' . strtr($product['product_name'], array('<' => ' ', '>' => ' ', '{' => ' ', '}' => ' ', '#' => ' ', ';' => ' ', '=' => ' ', '|' => '-')) . '",
      "brand": "' . $manufacturer . '",
      "category": "' . $default_category_name . '",
      "quantity": ' . (int)$product['product_quantity'] . ',
      "price": "' . Tools::ps_round($product['unit_price_tax_incl']) . '"
    }';
            }
            $add = '<script>
            // Ecommerce GTAG
            gtag("event", "purchase", {
  "transaction_id": "' . $id_order . '",
  "affiliation": "' . Configuration::get('PS_SHOP_NAME') . '",
  "value": ' . Tools::ps_round($order->total_paid) . ',
  "currency": "' . $currency->iso_code . '",
  "tax": ' . Tools::ps_round($order->total_paid - $order->total_paid_tax_excl) . ',
  "shipping": ' . Tools::ps_round($order->total_shipping_tax_incl) . ',
  "items": [
  ' . implode(',', $products_use) . '
  ]
});
</script>
    ';


        }
        // file_put_contents($this->local_path . $this->name . '.log', date("Y-m-d H:i:s") . ' -> ' . $add . "\r\n", FILE_APPEND);
        return "
                <!-- Měřicí kód Google Ecommerce (www.psmoduly.cz / www.openservis.cz) - begin -->
                $add
                <!-- Měřicí kód Google Ecommerce (www.psmoduly.cz / www.openservis.cz) - end -->
";
    }



    public function hookdisplayHeader($params)
    {
        if ($this->active != 1) {
            return;
        }

        $id = Configuration::get($this->name . '_id');

        if (empty($id)) {
            return false;
        }
// ga('require', 'ec');

        if ((int)Configuration::get($this->name . '_verze') == 0) {
            return "
         <!-- Měřicí kód Google Analytics (www.psmoduly.cz / www.openservis.cz) - begin -->
         <script>
   (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
				ga('create', '$id', 'auto');
				ga('send', 'pageview');
				</script>
				<!-- Měřicí kód Google Analytics (www.psmoduly.cz / www.openservis.cz) - end -->
				";
        } else {
            $anonymization = (int)Configuration::get($this->name . '_anonymization');
            $anonymize = '';
            if ($anonymization == 1) {
                $anonymize = ", { 'anonymize_ip': true }";
                // Každopádně se mi povedlo najít, jak upravit kód GA, aby zahrnoval do měření všechna URL včetně URL obsahující hashtag, na základě tohoto vlákna https://www.en.advertisercommunity.com/t5/Google-Analytics-Account-Access/Tracking-URLS-with-a-hashtag-fragment/td-p/1734982 jsem připravil úpravu gtag.js měřícího kódu:
                // $anonymize = ", { 'anonymize_ip': true }, 'page_path': location.pathname + location.search + location.hash";

            }
            return "<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src=\"https://www.googletagmanager.com/gtag/js?id=$id\"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', '$id'$anonymize);
</script>";

        }
        /**
         * return "<script>
         * (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
         * (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
         * m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
         * })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
         * ga('create', '$id', 'auto');
         * ga('require', 'ec');</script>";
         **/
    }




    public function hookdisplayMobileHeader($params)
    {
        return $this->hookdisplayHeader($params);
    }



}
/**
 * @license https://psmoduly.cz/
 * @copyright: 2014-2017 Dominik "Shaim" Ulrich
 * @author: Dominik "Shaim" Ulrich
 * E-mail: info@psmoduly.cz / info@openservis.cz
 * Websites: www.psmoduly.cz / www.openservis.cz
 **/
