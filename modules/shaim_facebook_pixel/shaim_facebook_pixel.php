<?php
/**
 * @license https://psmoduly.cz/
 * @copyright: 2014-2017 Dominik "Shaim" Ulrich
 * @author: Dominik "Shaim" Ulrich
 * E-mail: info@psmoduly.cz / info@openservis.cz
 * Websites: www.psmoduly.cz / www.openservis.cz
 **/
if (!defined('_PS_VERSION_')) {
    die;
}

// https://developers.facebook.com/docs/marketing-api/dynamic-product-ads/product-audiences/v2.7
class shaim_facebook_pixel extends Module
{
    private $_html = '';

    public function __construct()
    {
        $this->name = 'shaim_facebook_pixel';
        $this->bootstrap = true;
        $this->tab = 'others';
        $this->version = '1.8.1';
        $this->author = 'Dominik Shaim (www.psmoduly.cz / www.openservis.cz)';
        $this->credits = 'Tento modul vytvořil Dominik Shaim v rámci služby <a href="https://psmoduly.cz/?from=' . urlencode($this->name) . '&utm_source=moduly_ins&utm_medium=inside&utm_campaign=mod_ins&utm_content=version177" title="www.psmoduly.cz" target="_blank">www.psmoduly.cz</a> / <a href="https://openservis.cz/?from=' . urlencode($this->name) . '&utm_source=moduly_ins&utm_medium=inside&utm_campaign=mod_ins&utm_content=version177" title="www.openservis.cz" target="_blank">www.openservis.cz</a>.<br />Potřebujete modul na míru? Napište nám na info@psmoduly.cz / info@openservis.cz<br />Verze modulu:  ' . $this->version;
        $this->need_instance = 1;
        $this->is_configurable = 1;
        $this->ps_versions_compliancy = array('min' => '0.9.7', 'max' => '1.7.99.99');
        parent::__construct();
        $this->full_url = ((Configuration::get('PS_SSL_ENABLED') == 1) ? 'https://' : 'http://') . ((version_compare(_PS_VERSION_, '1.5', '>=')) ? $this->context->shop->domain . $this->context->shop->physical_uri : $_SERVER['HTTP_HOST'] . __PS_BASE_URI__);
        if (!isset($this->local_path)) { /* 1.4 a nizsi */
            $this->local_path = _PS_MODULE_DIR_ . $this->name . '/'; // $this->local_path = $this->_path;
        }
        // $this->Trusted();
        $this->NecessaryWarning();
        $this->BackwardCompatibility();
        $this->displayName = $this->l('PSmoduly.cz / OpenServis.cz - Facebook.com - měření konverzí (Pixel + Eventy)');
        $this->description = $this->l('Děláme nejenom moduly, ale i programování pro Prestashop na míru a také správu e-shopů. Napište nám, pomůžeme Vám s Vašim obchodem na platformě Prestashop.');
        $this->confirmUninstall = $this->l('Opravdu chcete odinstalovat modul ') . $this->displayName . $this->l('?');

        if (version_compare(_PS_VERSION_, '1.5', '>=')) {
            $this->hook_name = 'displayHeader';
            $this->hook_name2 = 'displayOrderConfirmation';
            $this->hook_name3 = 'displayFooter';
            $this->hook_name4 = 'actionCartSave';
            $this->hook_name5 = 'actionSearch';
            $this->hook_name6 = 'displayFooterProduct';
            $this->hook_name7 = 'gopayhook';
        } else {
            $this->hook_name = 'header';
            $this->hook_name2 = 'orderConfirmation';
            $this->hook_name3 = 'footer';
            $this->hook_name4 = 'cart';
            $this->hook_name5 = 'search';
            $this->hook_name6 = 'productfooter';
            $this->hook_name7 = 'gopayhook';
        }
        $this->separator = Configuration::get($this->name . '_separator');
        $this->enable_combinations = (int)Configuration::get($this->name . '_enable_combinations');
        $ev = Configuration::get($this->name . '_ev');
        if (empty($ev) && !isset($this->context->cookie->id_employee)) {
            $this->active = 0;
        }


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
            $this->context->controller->addJS($this->_path . 'trusted.js'); // MIAHS
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
        if (parent::install() == false || $this->registerHook($this->hook_name) == false || $this->registerHook($this->hook_name2) == false || $this->registerHook($this->hook_name3) == false || $this->registerHook($this->hook_name4) == false || $this->registerHook($this->hook_name5) == false || $this->registerHook($this->hook_name6) == false) {
            return false;
        }
        $this->registerHook($this->hook_name7);
        $is_enabled_shaim_export = (version_compare(_PS_VERSION_, '1.5', '>=') ? (int)Module::isEnabled('shaim_export') : (int)DB::getInstance()->getValue("SELECT COUNT(*) as is_enabled_shaim_export FROM " . _DB_PREFIX_ . "module WHERE active = 1 && name = 'shaim_export';"));

        Configuration::updateValue($this->name . '_ev', '');
        Configuration::updateValue($this->name . '_separator', '-');
        Configuration::updateValue($this->name . '_enable_combinations', $is_enabled_shaim_export);

        Configuration::updateValue($this->name . '_initiatecheckout', 1);
        Configuration::updateValue($this->name . '_completeregistration', 1);
        Configuration::updateValue($this->name . '_pageview', 1);
        Configuration::updateValue($this->name . '_purchase', 1);
        Configuration::updateValue($this->name . '_viewcontent', 1);
        Configuration::updateValue($this->name . '_search', 1);
        Configuration::updateValue($this->name . '_addtocart', 1);
        Configuration::updateValue($this->name . '_addtowishlist', 1);
        Configuration::updateValue($this->name . '_viewcategory', 0);

        @file_put_contents(_PS_CACHE_DIR_ . 'shaim_installed.txt', date("Y-m-d H:i:s"));
        $this->Statistics('install');
        return true;
    }

    public function uninstall()
    {
        @unlink(_PS_CACHE_DIR_ . 'shaim_installed.txt');
        $this->Statistics('uninstall');
        if (parent::uninstall() == false || $this->unregisterHook($this->hook_name) == false || $this->unregisterHook($this->hook_name2) == false || $this->unregisterHook($this->hook_name3) == false || $this->unregisterHook($this->hook_name4) == false || $this->unregisterHook($this->hook_name5) == false || $this->unregisterHook($this->hook_name6) == false) {
            return false;
        }
        $this->unregisterHook($this->hook_name7);
        Configuration::deleteByName($this->name . '_ev');
        Configuration::deleteByName($this->name . '_separator');
        Configuration::deleteByName($this->name . '_enable_combinations');

        Configuration::deleteByName($this->name . '_initiatecheckout');
        Configuration::deleteByName($this->name . '_completeregistration');
        Configuration::deleteByName($this->name . '_pageview');
        Configuration::deleteByName($this->name . '_purchase');
        Configuration::deleteByName($this->name . '_viewcontent');
        Configuration::deleteByName($this->name . '_search');
        Configuration::deleteByName($this->name . '_addtocart');
        Configuration::deleteByName($this->name . '_addtowishlist');
        Configuration::deleteByName($this->name . '_viewcategory');

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
            Configuration::updateValue($this->name . '_ev', preg_replace('/\s+/', '', Tools::getValue('ev')));
            Configuration::updateValue($this->name . '_separator', trim(Tools::getValue('separator')));
            Configuration::updateValue($this->name . '_enable_combinations', (int)trim(Tools::getValue('enable_combinations')));

            Configuration::updateValue($this->name . '_initiatecheckout', (int)trim(Tools::getValue('initiatecheckout')));
            Configuration::updateValue($this->name . '_completeregistration', (int)trim(Tools::getValue('completeregistration')));
            Configuration::updateValue($this->name . '_pageview', (int)trim(Tools::getValue('pageview')));
            Configuration::updateValue($this->name . '_purchase', (int)trim(Tools::getValue('purchase')));
            Configuration::updateValue($this->name . '_viewcontent', (int)trim(Tools::getValue('viewcontent')));
            Configuration::updateValue($this->name . '_search', (int)trim(Tools::getValue('search')));
            Configuration::updateValue($this->name . '_addtocart', (int)trim(Tools::getValue('addtocart')));
            Configuration::updateValue($this->name . '_addtowishlist', (int)trim(Tools::getValue('addtowishlist')));
            Configuration::updateValue($this->name . '_viewcategory', (int)trim(Tools::getValue('viewcategory')));

            $result = $this->Show($this->l('Uloženo'), 'ok');
            $this->_html .= '<div class="bootstrap">' . $result . '</div>';
        }
        $this->Statistics('open');
        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $this->context->controller->addCSS($this->_path . 'old_admin.css', 'all');
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


        $this->_html .= '<div class="form-group">';
        $this->_html .= '<label class="control-label col-lg-3">' . $this->l('ID Pixelu') . '</label>';
        $this->_html .= '<div class="col-lg-9"><input type="text" name="ev" size="30" value="' . Configuration::get($this->name . '_ev') . '"></div>';
        $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
					' . $this->l('Dávejte si pozor, abyste ID zkopírovali bez mezer na začátku/konci.') . '
					</div></div>';


        $this->_html .= '</div>';

        $this->_html .= '<div class="form-group">';
        $enable_combinations = (int)Configuration::get($this->name . '_enable_combinations');
        $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Povolit ID kombinací') . '</label>';
        $this->_html .= '<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
										<input name="enable_combinations" id="enable_combinations_on" value="1"' . (($enable_combinations == 1) ? ' checked="checked"' : '') . ' type="radio">
										<label for="enable_combinations_on">' . $this->l('ANO') . '</label>
										<input name="enable_combinations" id="enable_combinations_off" value="0"' . (($enable_combinations == 0) ? ' checked="checked"' : '') . ' type="radio">
										<label for="enable_combinations_off">' . $this->l('NE') . '</label>
										<a class="slide-button btn"></a>
									</span></div>';
        $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
					' . $this->l('Pokud toto povolíte, bude se uvádět jak ID produktu, tak ID kombinace. Pokud toto vypnete, bude se uvádět pouze ID produktu.') . '
					</div></div>';
        $this->_html .= '</div>';


        $this->_html .= '<div class="form-group">';
        $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Oddělovač ID produktu a ID varianty') . '</label>';
        $this->_html .= '<div class="col-lg-9"><input type="text" name="separator" size="30" value="' . Configuration::get($this->name . '_separator') . '"></div>';
        $this->_html .= '</div>';


        $this->_html .= '<div class="form-group">';
        $tmp = (int)Configuration::get($this->name . '_pageview');
        $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Event PageView') . '</label>';
        $this->_html .= '<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
										<input name="pageview" id="pageview_on" value="1"' . (($tmp == 1) ? ' checked="checked"' : '') . ' type="radio">
										<label for="pageview_on">' . $this->l('ANO') . '</label>
										<input name="pageview" id="pageview_off" value="0"' . (($tmp == 0) ? ' checked="checked"' : '') . ' type="radio">
										<label for="pageview_off">' . $this->l('NE') . '</label>
										<a class="slide-button btn"></a>
									</span></div>';
        $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
					' . $this->l('Zobrazení stránek') . '
					</div></div>';
        $this->_html .= '</div>';


        $this->_html .= '<div class="form-group">';
        $tmp = (int)Configuration::get($this->name . '_viewcontent');
        $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Event ViewContent') . '</label>';
        $this->_html .= '<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
										<input name="viewcontent" id="viewcontent_on" value="1"' . (($tmp == 1) ? ' checked="checked"' : '') . ' type="radio">
										<label for="viewcontent_on">' . $this->l('ANO') . '</label>
										<input name="viewcontent" id="viewcontent_off" value="0"' . (($tmp == 0) ? ' checked="checked"' : '') . ' type="radio">
										<label for="viewcontent_off">' . $this->l('NE') . '</label>
										<a class="slide-button btn"></a>
									</span></div>';
        $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
					' . $this->l('Zobrazení produktové stránky') . '
					</div></div>';
        $this->_html .= '</div>';


        $this->_html .= '<div class="form-group">';
        $tmp = (int)Configuration::get($this->name . '_initiatecheckout');
        $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Event InitiateCheckout') . '</label>';
        $this->_html .= '<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
										<input name="initiatecheckout" id="initiatecheckout_on" value="1"' . (($tmp == 1) ? ' checked="checked"' : '') . ' type="radio">
										<label for="initiatecheckout_on">' . $this->l('ANO') . '</label>
										<input name="initiatecheckout" id="initiatecheckout_off" value="0"' . (($tmp == 0) ? ' checked="checked"' : '') . ' type="radio">
										<label for="initiatecheckout_off">' . $this->l('NE') . '</label>
										<a class="slide-button btn"></a>
									</span></div>';
        $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
					' . $this->l('Proces objednávky') . '
					</div></div>';
        $this->_html .= '</div>';


        $this->_html .= '<div class="form-group">';
        $tmp = (int)Configuration::get($this->name . '_purchase');
        $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Event Purchase') . '</label>';
        $this->_html .= '<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
										<input name="purchase" id="purchase_on" value="1"' . (($tmp == 1) ? ' checked="checked"' : '') . ' type="radio">
										<label for="purchase_on">' . $this->l('ANO') . '</label>
										<input name="purchase" id="purchase_off" value="0"' . (($tmp == 0) ? ' checked="checked"' : '') . ' type="radio">
										<label for="purchase_off">' . $this->l('NE') . '</label>
										<a class="slide-button btn"></a>
									</span></div>';
        $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
					' . $this->l('Dokončení objednávky (order confirmation)') . '
					</div></div>';
        $this->_html .= '</div>';


        $this->_html .= '<div class="form-group">';
        $tmp = (int)Configuration::get($this->name . '_search');
        $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Event Search') . '</label>';
        $this->_html .= '<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
										<input name="search" id="search_on" value="1"' . (($tmp == 1) ? ' checked="checked"' : '') . ' type="radio">
										<label for="search_on">' . $this->l('ANO') . '</label>
										<input name="search" id="search_off" value="0"' . (($tmp == 0) ? ' checked="checked"' : '') . ' type="radio">
										<label for="search_off">' . $this->l('NE') . '</label>
										<a class="slide-button btn"></a>
									</span></div>';
        $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
					' . $this->l('Vyhledání zboží na webu') . '
					</div></div>';
        $this->_html .= '</div>';


        $this->_html .= '<div class="form-group">';
        $tmp = (int)Configuration::get($this->name . '_addtocart');
        $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Event AddToCart') . '</label>';
        $this->_html .= '<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
										<input name="addtocart" id="addtocart_on" value="1"' . (($tmp == 1) ? ' checked="checked"' : '') . ' type="radio">
										<label for="addtocart_on">' . $this->l('ANO') . '</label>
										<input name="addtocart" id="addtocart_off" value="0"' . (($tmp == 0) ? ' checked="checked"' : '') . ' type="radio">
										<label for="addtocart_off">' . $this->l('NE') . '</label>
										<a class="slide-button btn"></a>
									</span></div>';
        $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
					' . $this->l('Přidání produktu do košíku, využívá defaultní modul blockcart/ps_shoppingcart, který používá ID #add_to_cart a třídu .ajax_add_to_cart_button)') . '
					</div></div>';
        $this->_html .= '</div>';


        $this->_html .= '<div class="form-group">';
        $tmp = (int)Configuration::get($this->name . '_addtowishlist');
        $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Event AddToWishlist') . '</label>';
        $this->_html .= '<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
										<input name="addtowishlist" id="addtowishlist_on" value="1"' . (($tmp == 1) ? ' checked="checked"' : '') . ' type="radio">
										<label for="addtowishlist_on">' . $this->l('ANO') . '</label>
										<input name="addtowishlist" id="addtowishlist_off" value="0"' . (($tmp == 0) ? ' checked="checked"' : '') . ' type="radio">
										<label for="addtowishlist_off">' . $this->l('NE') . '</label>
										<a class="slide-button btn"></a>
									</span></div>';
        $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
					' . $this->l('Přidání produktu do wishlistu (dle defaultního modulu blockwishlist, který používá ID #wishlist_button_nopop)') . '
					</div></div>';
        $this->_html .= '</div>';


        $this->_html .= '<div class="form-group">';
        $tmp = (int)Configuration::get($this->name . '_completeregistration');
        $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Event CompleteRegistration') . '</label>';
        $this->_html .= '<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
										<input name="completeregistration" id="completeregistration_on" value="1"' . (($tmp == 1) ? ' checked="checked"' : '') . ' type="radio">
										<label for="completeregistration_on">' . $this->l('ANO') . '</label>
										<input name="completeregistration" id="completeregistration_off" value="0"' . (($tmp == 0) ? ' checked="checked"' : '') . ' type="radio">
										<label for="completeregistration_off">' . $this->l('NE') . '</label>
										<a class="slide-button btn"></a>
									</span></div>';
        $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
					' . $this->l('Dokončení registrace zákaznického účtu') . '
					</div></div>';
        $this->_html .= '</div>';


        $this->_html .= '<div class="form-group">';
        $tmp = (int)Configuration::get($this->name . '_viewcategory');
        $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Event Custom ViewCategory') . '</label>';
        $this->_html .= '<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
										<input name="viewcategory" id="viewcategory_on" value="1"' . (($tmp == 1) ? ' checked="checked"' : '') . ' type="radio">
										<label for="viewcategory_on">' . $this->l('ANO') . '</label>
										<input name="viewcategory" id="viewcategory_off" value="0"' . (($tmp == 0) ? ' checked="checked"' : '') . ' type="radio">
										<label for="viewcategory_off">' . $this->l('NE') . '</label>
										<a class="slide-button btn"></a>
									</span></div>';
        $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
					' . $this->l('Zobrazení kategorie') . '
					</div></div>';
        $this->_html .= '</div>';


        $this->_html .= '<br /><br /><div class="clear_both"><small>' . $this->credits . '<br />' . $this->description . '</small></div>';
        $this->_html .= '<div class="panel-footer"><button type="submit" class="btn btn-default pull-right" name="submit_text">
<i class="process-icon-save"></i>' . $this->l('Uložit') . '</button></div></form></div></div></div>';
        return $this->_html;
    }

    private function OnlyPixel($params)
    {

        $ev = Configuration::get($this->name . '_ev');
        // $ev = explode(',', $evv);
        // $ev = array($evv);
        $add = '';
        $pageview = '';
        if (Configuration::get($this->name . '_pageview') == 1) {
            $pageview = "fbq('track', 'PageView');";
        }
        // foreach ($ev as $e) {
        $add .= "<!-- Facebook Pixel Code -->
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','//connect.facebook.net/en_US/fbevents.js');
fbq('init', '" . $ev . "');
$pageview
</script>
<noscript>
<img height='1' width='1' style='display:none' src='https://www.facebook.com/tr?id=" . $ev . "&ev=PageView&noscript=1'/>
</noscript>
<!-- End Facebook Pixel Code -->";
        // }
        return "
                <!-- Měřicí kód Pixel Facebook.com (www.psmoduly.cz / www.openservis.cz) - begin -->
                $add
                <!-- Měřicí kód Pixel Facebook.com (www.psmoduly.cz / www.openservis.cz) - end -->
";
    }

    private function EventPurchase($params)
    {


        if ($this->active != 1) {
            return;
        }
        if (Configuration::get($this->name . '_purchase') != 1) {
            return false;
        }

        $id_order = (int)Tools::getValue('id_order');
        if (!$id_order) {
            return false;
        }
        $order = new Order($id_order);
        $currency = new Currency($order->id_currency);
        $products = $order->getProducts();
        $content_ids = array();

        // $total = $order->total_products_tax_excl - $order->total_discounts_tax_excl;
        $total = $order->total_paid_tax_excl - $order->total_shipping_tax_excl - $order->total_wrapping_tax_excl;
        if (empty($total)) {
            $total = $order->total_paid;
        }
        // $total = $order->total_paid_tax_excl;
        // if ($currency->iso_code != 'CZK') { // Cizí měna
        //     $total /= $currency->conversion_rate;
        // }

        if ($products) {
            foreach ($products as $product) {
                $id_product = $product['product_id'];

                $product_attribute_id = (int)$product['product_attribute_id'];
                if ($this->enable_combinations == 1 && $product_attribute_id > 0) {
                    $id_product .= $this->separator . $product_attribute_id;
                }

                $content_ids[] = $id_product;
            }
        }
        $num_items = count($content_ids);
        $content_ids = "'" . implode("', '", $content_ids) . "'";

        $add = "<!-- Facebook Conversion Page -->
<script>
fbq('track', 'Purchase', {
content_ids: [" . $content_ids . "],
content_type: 'product',
value: " . Tools::ps_round(number_format($total, 6, '.', ''), 2) . ",
currency: '" . $currency->iso_code . "',
num_items: " . $num_items . "
}); 
</script>";
        return "
            <!-- Měřicí kód Pixel (Event Purchase) Facebook.com (www.psmoduly.cz / www.openservis.cz) - begin -->
                $add
             <!-- Měřicí kód Pixel (Event Purchase) Facebook.com (www.psmoduly.cz / www.openservis.cz) - end -->
";
    }

    private function EventInitiateCheckout($params)
    {


        if ($this->active != 1) {
            return;
        }

        $id_cart = $this->context->cart->id;

        if (!$id_cart) {
            return false;
        }
        $cart = new Cart($id_cart);
        $currency = new Currency($cart->id_currency);
        $products = $cart->getProducts();
        $content_ids = array();

        $total = 0;
        if ($products) {
            foreach ($products as $product) {

                $id_product = $product['id_product'];

                $product_attribute_id = (int)$product['id_product_attribute'];
                if ($this->enable_combinations == 1 && $product_attribute_id > 0) {
                    $id_product .= $this->separator . $product_attribute_id;
                }

                $total += Product::getPriceStatic($product['id_product'], true, ((isset($product['id_product_attribute'])) ? $product['id_product_attribute'] : null), 6);

                $content_ids[] = $id_product;
            }
        }

        $num_items = count($content_ids);
        $content_ids = "'" . implode("', '", $content_ids) . "'";

        $add = "<!-- Facebook Conversion Page -->
<script>
fbq('track', 'InitiateCheckout', {
content_ids: [" . $content_ids . "],
content_type: 'product',
value: " . Tools::ps_round(number_format($total, 6, '.', ''), 2) . ",
currency: '" . $currency->iso_code . "',
num_items: " . $num_items . "
});
</script>";
        return "
            <!-- Měřicí kód Pixel (Event InitiateCheckout) Facebook.com (www.psmoduly.cz / www.openservis.cz) - begin -->
                $add
             <!-- Měřicí kód Pixel (Event InitiateCheckout) Facebook.com (www.psmoduly.cz / www.openservis.cz) - end -->
";
    }


    private function EventPurchaseGopay($params)
    {


        if ($this->active != 1) {
            return;
        }
        if (Configuration::get($this->name . '_purchase') != 1) {
            return false;
        }

        $id_order = (int)Tools::getValue('orderNumber'); // add_gopay
        if (empty($id_order)) {
            $id_order = (int)Tools::getValue('id_order');
            if (empty($id_order)) {
                $id_order = (int)$params['objOrder']->id; // gopay detect
                if (empty($id_order)) {
                    $cart = new Cart($params['cart']->id);
                    $id_order = (int)$cart->id_order;
                }
            }
        }

        if (empty($id_order)) {
            return false;
        }

        $order = new Order($id_order);
        $currency = new Currency($order->id_currency);
        $products = $order->getProducts();
        $content_ids = array();

        $total = $order->total_paid_tax_excl - $order->total_shipping_tax_excl - $order->total_wrapping_tax_excl;
        // PS 1.4 and lower fix
        if (empty($total)) {
            $total = $order->total_paid;
        }
        // $total = $order->total_paid_tax_excl;
        // if ($currency->iso_code != 'CZK') { // Cizí měna
        //     $total /= $currency->conversion_rate;
        // }

        if ($products) {
            foreach ($products as $product) {
                $id_product = $product['product_id'];
                $product_attribute_id = (int)$product['product_attribute_id'];
                if ($this->enable_combinations == 1 && $product_attribute_id > 0) {
                    $id_product .= $this->separator . $product_attribute_id;
                }

                $content_ids[] = $id_product;
            }
        }
        $num_items = count($content_ids);
        $content_ids = "'" . implode("', '", $content_ids) . "'";

        $add = "<!-- Facebook Conversion Page -->
<script>
fbq('track', 'Purchase', {
content_ids: [" . $content_ids . "],
content_type: 'product',
value: " . Tools::ps_round(number_format($total, 6, '.', ''), 2) . ",
currency: '" . $currency->iso_code . "',
num_items: " . $num_items . "
});
</script>";
        return "
            <!-- gopayhook - Měřicí kód Pixel (Event Purchase) Facebook.com (www.psmoduly.cz / www.openservis.cz) - begin -->
                $add
             <!-- gopayhook - Měřicí kód Pixel (Event Purchase) Facebook.com (www.psmoduly.cz / www.openservis.cz) - end -->
";
    }

    private function EventViewContentProduct($params)
    {


        if ($this->active != 1) {
            return;
        }
        if (Configuration::get($this->name . '_viewcontent') != 1) {
            return false;
        }


        // V 1.7 to je array totiz zrejme
        $params['product'] = (object)$params['product'];
        if (empty($params['product']->id)) {
            return false;
        }


        $default_cat = new Category($params['product']->id_category_default, $params['cookie']->id_lang);
        $currency = new Currency((int)$params['cookie']->id_currency);


        $id_product = $params['product']->id;
        $product_attribute_id = (int)Product::getDefaultAttribute($id_product);
        if ($this->enable_combinations == 1 && $product_attribute_id > 0) {
            $id_product .= $this->separator . $product_attribute_id;
        }

        // 1.7. fix
        // $product = new Product($params['product']->id, false, $this->context->cookie->id_lang);
        // $price = $product->getPrice(true);


        $price = Product::getPriceStatic($params['product']->id, true, ((isset($params['product']->id_product_attribute)) ? $params['product']->id_product_attribute : null), 6);

        $add = "
<script>
fbq('track', 'ViewContent', {
content_name: '" . $params['product']->name . "',
content_category: '" . $default_cat->name . "',
content_ids: ['" . $id_product . "'],
content_type: 'product',
value: " . Tools::ps_round(number_format($price, 6, '.', ''), 2) . ",
currency: '" . $currency->iso_code . "'
});
</script>";
        return "
            <!-- Měřicí kód Pixel (Event ViewContent) Facebook.com (www.psmoduly.cz / www.openservis.cz) - begin -->
                $add
             <!-- Měřicí kód Pixel (Event ViewContent) Facebook.com (www.psmoduly.cz / www.openservis.cz) - end -->
";
    }

    private function EventSearch($params)
    {


        if ($this->active != 1) {
            return;
        }
        if (Configuration::get($this->name . '_search') != 1) {
            return false;
        }
        $search_query = trim(Tools::getValue('search_query'));
        if (!$search_query) {
            return false;
        }

        // TODO, jak tam dostat ty produkty?
        $products = Search::find((int)$params['cookie']->id_lang, $search_query, 1, 10, 'position', 'asc', false, true);


        $full_products = array();
        if (!empty($products['result'])) {
            foreach ($products['result'] as $key => $product) {
                $id_product = $product['id_product'];
                $product_attribute_id = (int)$product['id_product_attribute'];
                if ($this->enable_combinations == 1 && $product_attribute_id > 0) {
                    $id_product .= $this->separator . $product_attribute_id;
                }
                $full_products[] = $id_product;
            }
        }
        $full_products = "'" . implode("', '", $full_products) . "'";

        $add = "
<script>
fbq('track', 'Search', {
  search_string: '$search_query',
  content_ids: [$full_products], // top 5-10 search results
  content_type: 'product'
});
</script>";
        return "
            <!-- Měřicí kód Pixel (Event Search) Facebook.com (www.psmoduly.cz / www.openservis.cz) - begin -->
                $add
             <!-- Měřicí kód Pixel (Event Search) Facebook.com (www.psmoduly.cz / www.openservis.cz) - end -->
";
    }


    private function EventCategory($params)
    {


        if ($this->active != 1) {
            return;
        }
        if (Configuration::get($this->name . '_viewcategory') != 1) {
            return false;
        }
        $id_category = (int)Tools::getValue('id_category');
        if (empty($id_category)) {
            return false;
        }

        $category = new Category($id_category, (int)$params['cookie']->id_lang);
        $products = $category->getProducts((int)$params['cookie']->id_lang, 1, 10, 'position', 'ASC', false, true, false);
        $parent = new Category($category->id_parent, (int)$params['cookie']->id_lang);

        $full_products = array();
        if ($products) {
            foreach ($products as $key => $product) {
                $id_product = $product['id_product'];

                $product_attribute_id = (int)$product['id_product_attribute'];
                if ($this->enable_combinations == 1 && $product_attribute_id > 0) {
                    $id_product .= $this->separator . $product_attribute_id;
                }
                $full_products[] = $id_product;
            }
        }

        $full_products = "'" . implode("', '", $full_products) . "'";

        $add = "
<script>
fbq('trackCustom', 'ViewCategory', {
  content_name: '{$category->name}',
  content_category: '{$parent->name}',
  content_ids: [$full_products], // top 5-10 results
  content_type: 'product'
});
</script>";
        return "
            <!-- Měřicí kód Pixel (Event Category) Facebook.com (www.psmoduly.cz / www.openservis.cz) - begin -->
                $add
             <!-- Měřicí kód Pixel (Event Category) Facebook.com (www.psmoduly.cz / www.openservis.cz) - end -->
";
    }

    private function EventAddToCart($params)
    {
        // Už by nemělo být třeba
        return;
        if ($this->active != 1) {
            return;
        }
        // if (Configuration::get($this->name . '_addtocart') != 1 || (!Module::isEnabled('blockcart') && !Module::isEnabled('ps_shoppingcart'))) {
        if (Configuration::get($this->name . '_addtocart') != 1) {
            return false;
        }


        // Tuto logiku jsem okoukal od ganalytics, hadam, ze lepe to nejde, kdyz ani ofiko analytics modul to nema lepe vyreseno.

        if (!isset($this->context->cart))
            return;

        if (!Tools::getValue('id_product')) {
            return;
        }

        $cart_products = $this->context->cart->getProducts();
        if (!isset($cart_products) && !count($cart_products)) {
            return;
        }
        foreach ($cart_products as $cart_product) {
            if ($cart_product['id_product'] == Tools::getValue('id_product')) {
                $add_product = $cart_product;
            }
        }

        if (!isset($add_product)) {
            return;
        }


        $default_cat = new Category($add_product['id_category_default'], $params['cookie']->id_lang);
        $currency = new Currency((int)$params['cookie']->id_currency);

        $id_product = $add_product['id_product'];
        $product_attribute_id = (int)$add_product['id_product_attribute'];
        if ($this->enable_combinations == 1 && $product_attribute_id > 0) {
            $id_product .= $this->separator . $product_attribute_id;
        }

        $data_for_js = array(
            'name' => urlencode($add_product['name']),
            'category' => urlencode($default_cat->name),
            'content_ids' => $id_product,
            'value' => $add_product['price'],
            'currency' => $currency->iso_code,
        );

        $this->context->cookie->shaim_facebook_event_add_to_cart = serialize($data_for_js);


    }

    private function EventAddToCartHelper($params)
    {
        // Už by nemělo být třeba
        return;
        if ($this->active != 1) {
            return;
        }
        // if (Configuration::get($this->name . '_addtocart') != 1 || (!Module::isEnabled('blockcart') && !Module::isEnabled('ps_shoppingcart'))) {
        if (Configuration::get($this->name . '_addtocart') != 1) {
            return false;
        }


        if (!isset($this->context->cookie->shaim_facebook_event_add_to_cart)) {
            return;
        }

        $data_for_js = unserialize($this->context->cookie->shaim_facebook_event_add_to_cart);

        $add = "
<script>
fbq('track', 'AddToCart', {
content_name: '" . urldecode($data_for_js['name']) . "',
content_category: '" . urldecode($data_for_js['category']) . "',
content_ids: ['" . $data_for_js['content_ids'] . "'],
content_type: 'product',
value: " . Tools::ps_round(number_format($data_for_js['value'], 6, '.', ''), 2) . ",
currency: '" . $data_for_js['currency'] . "'
});
</script>";

        unset($this->context->cookie->shaim_facebook_event_add_to_cart);

        return "
            <!-- Měřicí kód Pixel (Event AddToCart) Facebook.com (www.psmoduly.cz / www.openservis.cz) - begin -->
                $add
             <!-- Měřicí kód Pixel (Event AddToCart) Facebook.com (www.psmoduly.cz / www.openservis.cz) - end -->
";


    }

    public function hookdisplayOrderConfirmation($params)
    {
        return $this->EventPurchase($params);
    }

    public function hookorderConfirmation($params)
    {
        return $this->hookdisplayOrderConfirmation($params);
    }


    public function hookdisplayHeader($params)
    {
        if ($this->active != 1) {
            return;
        }

        $return = $this->OnlyPixel($params);
        if (version_compare(_PS_VERSION_, '1.5', '>=')) {
            if (Tools::getValue('controller') == 'search') {
                $return .= $this->EventSearch($params);
            } elseif (Tools::getValue('controller') == 'category') {
                $return .= $this->EventCategory($params);
            } elseif (Tools::getValue('controller') == 'product') {

            }
            if (version_compare(_PS_VERSION_, '1.7', '>=')) {
                $this->context->controller->registerJavascript('modules-' . $this->name, 'modules/' . $this->name . '/' . $this->name . '.js', ['position' => 'bottom', 'priority' => 50]);
            } elseif (version_compare(_PS_VERSION_, '1.5', '>=')) {
                $this->context->controller->addJS($this->_path . $this->name . '.js');
            }
        } else {
            $return .= '<script src="' . $this->_path . $this->name . '.js"></script>';
        }
        return $return;
    }

    public function hookheader($params)
    {
        return $this->hookdisplayHeader($params);
    }

    public function hookdisplayFooter($params)
    {
        if ($this->active != 1) {
            return;
        }

        $is_enabled_blockwishlist = (version_compare(_PS_VERSION_, '1.5', '>=') ? (int)Module::isEnabled('blockwishlist') : (int)DB::getInstance()->getValue("SELECT COUNT(*) as is_enabled_blockwishlist FROM " . _DB_PREFIX_ . "module WHERE active = 1 && name = 'blockwishlist';"));
        $add = '';
        if (Configuration::get($this->name . '_addtowishlist') == 1 && $is_enabled_blockwishlist) {
            $add .= "<script>
        var fb_wishlist = 1;
            </script>";
        }

        if (
            ((version_compare(_PS_VERSION_, '1.7', '>=') && Tools::getValue('controller') == 'cart')
                || (Tools::getValue('controller') == 'order' || Tools::getValue('controller') == 'orderopc' || Tools::getValue('controller') == 'order-opc')
                || (version_compare(_PS_VERSION_, '1.5', '<') && preg_match("/^\/order\.php/", $_SERVER['SCRIPT_NAME'])))
            && Configuration::get($this->name . '_initiatecheckout') == 1
        ) {

            $add .= $this->EventInitiateCheckout($params);
        }
        if (isset($this->context->cookie->account_created) && Configuration::get($this->name . '_completeregistration') == 1) {

            $add .= "
<script>
fbq('track', 'CompleteRegistration');
</script>";
        }

        // if (Configuration::get($this->name . '_addtocart') == 1 && (Module::isEnabled('blockcart') || Module::isEnabled('ps_shoppingcart'))) {
        if (Configuration::get($this->name . '_addtocart') == 1) {
            $id_product = (int)Tools::getValue('id_product');
            if ($id_product > 0) {
                $product = new Product($id_product, false, $this->context->cookie->id_lang);
                if (Validate::isLoadedObject($product)) {
                    $currency = new Currency((int)$params['cookie']->id_currency);
                    $default_cat = new Category($product->id_category_default, $params['cookie']->id_lang);
                    $content_ids = $product->id;
                    $product_attribute_id = (int)Product::getDefaultAttribute($id_product);
                    if ($this->enable_combinations == 1 && $product_attribute_id > 0) {
                        $content_ids .= $this->separator . $product_attribute_id;
                    }

                    $add .= "<script>
var fb_product_page = 1;
var fb_content_name = '" . urldecode($product->name) . "';
var fb_content_category = '" . urldecode($default_cat->name) . "';
var fb_content_ids = ['" . $content_ids . "'];
var fb_content_type = 'product';
var fb_value = " . Tools::ps_round(number_format($product->getPrice(true), 6, '.', ''), 2) . ";
var fb_currency = '" . $currency->iso_code . "';
                </script>";
                }
            }
        }

        return $add . $this->EventAddToCartHelper($params);
    }

    public function hookfooter($params)
    {
        return $this->hookdisplayFooter($params);
    }

    public function hookactionCartSave($params)
    {
        return $this->EventAddToCart($params);
    }

    public function hookcart($params)
    {
        return $this->hookactionCartSave($params);
    }


    public function hooksearch($params)
    {
        return false;
    }

    public function hookactionSearch($params) // Není dostupné v 1.7
    {
        return $this->hooksearch($params);
    }

    public function hookproductfooter($params)
    {
        return $this->hookdisplayFooterProduct($params);
    }

    public function hookdisplayFooterProduct($params)
    {

        return $this->EventViewContentProduct($params);
    }

    public function hookgopayhook($params)
    {
        return $this->EventPurchaseGopay($params);
    }


}
/**
 * @license https://psmoduly.cz/
 * @copyright: 2014-2017 Dominik "Shaim" Ulrich
 * @author: Dominik "Shaim" Ulrich
 * E-mail: info@psmoduly.cz / info@openservis.cz
 * Websites: www.psmoduly.cz / www.openservis.cz
 **/
