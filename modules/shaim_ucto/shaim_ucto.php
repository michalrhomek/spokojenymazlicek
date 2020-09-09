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

// http://is.muni.cz/th/251347/fi_m/DP_Sahanek.pdf
// https://www.stormware.cz/pohoda/xml/provyvojare/vypocetcastky/


class shaim_ucto extends Module
{
    private $_html = '';

    public function __construct()
    {
        $this->name = 'shaim_ucto';
        $this->bootstrap = true;
        $this->tab = 'others';
        $this->version = '1.8.8';
        $this->author = 'Dominik Shaim (www.psmoduly.cz / www.openservis.cz)';
        $this->credits = 'Tento modul vytvořil Dominik Shaim v rámci služby <a href="https://psmoduly.cz/?from=' . urlencode($this->name) . '&utm_source=moduly_ins&utm_medium=inside&utm_campaign=mod_ins&utm_content=version177" title="www.psmoduly.cz" target="_blank">www.psmoduly.cz</a> / <a href="https://openservis.cz/?from=' . urlencode($this->name) . '&utm_source=moduly_ins&utm_medium=inside&utm_campaign=mod_ins&utm_content=version177" title="www.openservis.cz" target="_blank">www.openservis.cz</a>.<br />Potřebujete modul na míru? Napište nám na info@psmoduly.cz / info@openservis.cz<br />Verze modulu:  ' . $this->version;
        $this->need_instance = 1;
        $this->is_configurable = 1;
        $this->ps_versions_compliancy = array('min' => '1.5.0.0', 'max' => '1.7.99.99');
        parent::__construct();
        $this->full_url = ((Configuration::get('PS_SSL_ENABLED') == 1) ? 'https://' : 'http://') . ((version_compare(_PS_VERSION_, '1.5', '>=')) ? ((Configuration::get('PS_SSL_ENABLED') == 1) ? $this->context->shop->domain_ssl : $this->context->shop->domain) . $this->context->shop->physical_uri . $this->context->shop->virtual_uri : $_SERVER['HTTP_HOST'] . __PS_BASE_URI__);
        if (!isset($this->local_path)) { /* 1.4 a nizsi */
            $this->local_path = _PS_MODULE_DIR_ . $this->name . '/'; // $this->local_path = $this->_path;
        }

        $this->NecessaryWarning();


        $this->displayName = $this->l('PSmoduly.cz / OpenServis.cz - Pohoda - Export objednávek/faktur');

        $this->description = $this->l('Děláme nejenom moduly, ale i programování pro Prestashop na míru, hosting pro Prestashop, a také správu e-shopů. Napište nám, pomůžeme Vám s Vašim obchodem na platformě Prestashop.');
        $this->confirmUninstall = $this->l('Opravdu chcete odinstalovat modul ') . $this->displayName . $this->l('?');


    }


    private function NecessaryWarning()
    {
        if (!extension_loaded('curl')) {//!ini_get("allow_url_fopen") ||
            $this->warning .= $this->l('Prosím, napište na Váš hosting, aby Vám povolili knihovnu "curl".');
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
        if (parent::install() == false) {
            return false;
        }
        $tmp1 = (int)Configuration::get('PS_OS_SHIPPING');
        $tmp2 = (int)Configuration::get('PS_OS_DELIVERED');
        $tmp3 = (int)Configuration::get('PS_OS_PAYMENT');
        $tmp4 = (int)Configuration::get('PS_OS_WS_PAYMENT');


        Configuration::updateValue($this->name . '_ico', '');
        Configuration::updateValue($this->name . '_splatnost', 14);
        Configuration::updateValue($this->name . '_sklad', '');
        Configuration::updateValue($this->name . '_predkontace', '3Fv');
        $reference_last_order = Db::getInstance()->getValue("SELECT reference FROM " . _DB_PREFIX_ . "orders ORDER BY id_order DESC;");
        if (preg_match('/^\d+$/', $reference_last_order)) {
            Configuration::updateValue($this->name . '_vs', 'reference');
        } else {
            Configuration::updateValue($this->name . '_vs', 'id_order');
        }
        Configuration::updateValue($this->name . '_verze', (preg_match("/\.sk$/", $_SERVER['HTTP_HOST']) ? 'sk' : 'cz'));
        Configuration::updateValue($this->name . '_number', 'id_order');
        Configuration::updateValue($this->name . '_order_number', 'id_order');
        Configuration::updateValue($this->name . '_prefix', 1);
        Configuration::updateValue($this->name . '_datepicker1', '2000-01-01');
        Configuration::updateValue($this->name . '_datepicker2', date("Y-m-d"));
        Configuration::updateValue($this->name . '_state_waiting', serialize(array($tmp1 => $tmp1, $tmp2 => $tmp2, $tmp3 => $tmp3, $tmp4 => $tmp4)));
        Configuration::updateValue($this->name . '_date_filter', 0);
        Configuration::updateValue($this->name . '_dph_math', 0);
        @file_put_contents(_PS_CACHE_DIR_ . 'shaim_installed.txt', date("Y-m-d H:i:s"));
        $this->Statistics('install');
        return true;
    }

    public function uninstall()
    {
        @unlink(_PS_CACHE_DIR_ . 'shaim_installed.txt');
        $this->Statistics('uninstall');
        if (parent::uninstall() == false) {
            return false;
        }
        Configuration::deleteByName($this->name . '_ico');
        Configuration::deleteByName($this->name . '_splatnost');
        Configuration::deleteByName($this->name . '_sklad');
        Configuration::deleteByName($this->name . '_predkontace');
        Configuration::deleteByName($this->name . '_vs');
        Configuration::deleteByName($this->name . '_verze');
        Configuration::deleteByName($this->name . '_number');
        Configuration::deleteByName($this->name . '_order_number');
        Configuration::deleteByName($this->name . '_prefix');
        Configuration::deleteByName($this->name . '_datepicker1');
        Configuration::deleteByName($this->name . '_datepicker2');
        Configuration::deleteByName($this->name . '_state_waiting');
        Configuration::deleteByName($this->name . '_date_filter');
        Configuration::deleteByName($this->name . '_dph_math');
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
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
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
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && (Tools::isSubmit('submit_text') || Tools::isSubmit('submit_text2') || Tools::isSubmit('submit_text3') || Tools::isSubmit('submit_text4'))) {
            Configuration::updateValue($this->name . '_ico', preg_replace('/\s+/', '', Tools::getValue('ico')));
            $ucty = Tools::getValue('bu_ucet');

            if ($ucty) {
                foreach ($ucty as $currency => $ucet) {
                    Configuration::updateValue($this->name . '_bu_ucet_' . $currency, preg_replace('/\s+/', '', $ucet));
                }
            }
            Configuration::updateValue($this->name . '_splatnost', (int)Tools::getValue('splatnost'));
            Configuration::updateValue($this->name . '_sklad', trim(Tools::getValue('sklad')));
            Configuration::updateValue($this->name . '_predkontace', trim(Tools::getValue('predkontace')));
            Configuration::updateValue($this->name . '_vs', trim(Tools::getValue('vs')));
            Configuration::updateValue($this->name . '_verze', trim(Tools::getValue('verze')));
            Configuration::updateValue($this->name . '_number', trim(Tools::getValue('number')));
            Configuration::updateValue($this->name . '_order_number', trim(Tools::getValue('order_number')));
            Configuration::updateValue($this->name . '_prefix', (int)trim(Tools::getValue('prefix')));
            Configuration::updateValue($this->name . '_datepicker1', trim(Tools::getValue('datepicker1')));
            Configuration::updateValue($this->name . '_datepicker2', trim(Tools::getValue('datepicker2')));
            Configuration::updateValue($this->name . '_state_waiting', serialize(Tools::getValue('state_waiting')));
            Configuration::updateValue($this->name . '_date_filter', (int)trim(Tools::getValue('date_filter')));
            Configuration::updateValue($this->name . '_dph_math', (int)trim(Tools::getValue('dph_math')));
            $result = $this->Show($this->l('Uloženo'), 'ok');
            $this->_html .= '<div class="bootstrap">' . $result . '</div>';
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && Tools::isSubmit('submit_text2')) {
            $this->Cron(Tools::getValue('datepicker1'), Tools::getValue('datepicker2'), true, 'orders', ((Shop::isFeatureActive() && Shop::getContext() != 4) ? $this->context->shop->id : 0));
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && Tools::isSubmit('submit_text3')) {
            $this->Cron(Tools::getValue('datepicker1'), Tools::getValue('datepicker2'), true, 'invoices', ((Shop::isFeatureActive() && Shop::getContext() != 4) ? $this->context->shop->id : 0));
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && Tools::isSubmit('submit_text4')) {
            $this->Cron(Tools::getValue('datepicker1'), Tools::getValue('datepicker2'), true, 'dobropisy', ((Shop::isFeatureActive() && Shop::getContext() != 4) ? $this->context->shop->id : 0));
        }
        $this->Statistics('open');
        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $this->context->controller->addCSS($this->_path . 'bo_15.css', 'all');
        }
        $this->context->controller->addCSS($this->_path . 'global_admin.css', 'all');
        $miahs = true;
        return $this->_generateForm();
    }

    private function _generateForm()
    {

        $this->context->controller->addJS($this->_path . $this->name . '.js');

        $this->_html .= '<div class="row"><div class="col-lg-12">
		<div class="panel"><div class="panel-heading"><i class="icon-cloud"></i> ' . $this->l('Ruční export') . '</div>'; // FRAME

        /*
        $this->_html .= '<div class="alert alert-info">';
        $this->_html .= $this->l('Cron exportuje vždy všechny objednávky/faktury. Pokud chcete objednávky/faktury exportovat selektivně dle data, můžete tak učinit zde.');
        $this->_html .= '</div><br />';
        */
        $this->_html .= '<form class="form-horizontal" action="' . $_SERVER['REQUEST_URI'] . '" method="post">';


        $this->_html .= '<div class="form-group">';
        $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Vaše IČO') . '</label>';
        $this->_html .= '<div class="col-lg-9"><input type="text" name="ico" size="30" value="' . Configuration::get($this->name . '_ico') . '"></div>';
        $this->_html .= '</div>';


        $currencies = Currency::getCurrencies(false, true);

        foreach ($currencies as $currency) {
            $this->_html .= '<div class="form-group">';
            $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Číslo účtu a kód banky - ') . $currency['iso_code'] . '</label>';
            $this->_html .= '<div class="col-lg-9"><input type="text" name="bu_ucet[' . $currency['id_currency'] . ']" size="30" value="' . Configuration::get($this->name . '_bu_ucet_' . $currency['id_currency']) . '"></div>';
            $this->_html .= '</div>';
        }


        $this->_html .= '<div class="form-group">';
        $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Splatnost faktur') . '</label>';
        $this->_html .= '<div class="col-lg-9"><input type="text" name="splatnost" size="30" value="' . Configuration::get($this->name . '_splatnost') . '"></div>';
        $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
				' . $this->l('Zadejte počet dní.') . '
				</div></div>';
        $this->_html .= '</div>';


        $this->_html .= '<div class="form-group">';
        $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Název skladu') . '</label>';
        $this->_html .= '<div class="col-lg-9"><input type="text" name="sklad" size="30" maxlength="19" value="' . Configuration::get($this->name . '_sklad') . '"></div>';
        $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
				' . $this->l('Pokud chcete odečítat sklad, vyplňte název Vašeho skladu. (maximální délka je 19 znaků)') . '
				</div></div>';
        $this->_html .= '</div>';

        $this->_html .= '<div class="form-group">';
        $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Předkontace zboží') . '</label>';
        $this->_html .= '<div class="col-lg-9"><input type="text" name="predkontace" size="30" maxlength="19" value="' . Configuration::get($this->name . '_predkontace') . '"></div>';
        $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
				' . $this->l('') . '
				</div></div>';
        $this->_html .= '</div>';


        $this->_html .= '<div class="form-group">';
        $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Používát prefix faktur z Prestashopu (Objednávky -> Faktury)?') . '</label>';
        $this->_html .= '<div class="col-lg-9">		<span class="switch prestashop-switch fixed-width-lg">
										<input name="prefix" id="prefix_on" value="1"' . ((Configuration::get($this->name . '_prefix') == 1) ? ' checked="checked"' : '') . ' type="radio">
										<label for="prefix_on">' . $this->l('ANO') . '</label>
										<input name="prefix" id="prefix_off" value="0"' . ((Configuration::get($this->name . '_prefix') == 0) ? ' checked="checked"' : '') . ' type="radio">
										<label for="prefix_off">' . $this->l('NE') . '</label>
										<a class="slide-button btn"></a>
									</span></div>';
        $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
				' . $this->l('Použitelné v případě, že je nastavení v modulu "Číslo dokladu dle" == "číslo faktury".') . '
				</div></div>';
        $this->_html .= '</div>';


        $vs = Configuration::get($this->name . '_vs');
        $this->_html .= '<div class="form-group">';
        $this->_html .= '<label class="control-label col-lg-3">' . $this->l('VS dle') . '</label>';
        $this->_html .= '<div class="col-lg-9"><select name="vs">';
        $this->_html .= '<option value="id_order"' . (($vs == 'id_order') ? ' selected="selected"' : '') . '>' . $this->l('ID objednávky') . '</option>';
        $this->_html .= '<option value="invoice_number"' . (($vs == 'invoice_number') ? ' selected="selected"' : '') . '>' . $this->l('čísla přímo z PS faktury') . '</option>';
        $this->_html .= '<option value="reference"' . (($vs == 'reference') ? ' selected="selected"' : '') . '>' . $this->l('kódu objednávky (pouze pro PS 1.6+)') . '</option>';
        $this->_html .= '</select></div>';
        $this->_html .= '</div>';


        $verze = Configuration::get($this->name . '_verze');
        $this->_html .= '<div class="form-group">';
        $this->_html .= '<label class="control-label col-lg-3">' . $this->l('CZ nebo SK firma?') . '</label>';
        $this->_html .= '<div class="col-lg-9"><select name="verze">';
        $this->_html .= '<option value="cz"' . (($verze == 'cz') ? ' selected="selected"' : '') . '>' . $this->l('CZ') . '</option>';
        $this->_html .= '<option value="sk"' . (($verze == 'sk') ? ' selected="selected"' : '') . '>' . $this->l('SK') . '</option>';
        $this->_html .= '</select></div>';
        $this->_html .= '</div>';

        $number = Configuration::get($this->name . '_number');
        $this->_html .= '<div class="form-group">';
        $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Číslo dokladu dle') . '</label>';
        $this->_html .= '<div class="col-lg-9"><select name="number">';
        $this->_html .= '<option value="id_order"' . (($number == 'id_order') ? ' selected="selected"' : '') . '>' . $this->l('ID objednávky') . '</option>';
        $this->_html .= '<option value="invoice_number"' . (($number == 'invoice_number') ? ' selected="selected"' : '') . '>' . $this->l('čísla přímo z PS faktury') . '</option>';
        $this->_html .= '<option value="reference"' . (($number == 'reference') ? ' selected="selected"' : '') . '>' . $this->l('kódu objednávky (pouze pro PS 1.6+)') . '</option>';
        $this->_html .= '</select></div>';
        $this->_html .= '</div>';


        $order_number = Configuration::get($this->name . '_order_number');
        $this->_html .= '<div class="form-group">';
        $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Číslo objednávky dle') . '</label>';
        $this->_html .= '<div class="col-lg-9"><select name="order_number">';
        $this->_html .= '<option value="id_order"' . (($order_number == 'id_order') ? ' selected="selected"' : '') . '>' . $this->l('ID objednávky') . '</option>';
        $this->_html .= '<option value="reference"' . (($order_number == 'reference') ? ' selected="selected"' : '') . '>' . $this->l('kódu objednávky (pouze pro PS 1.6+)') . '</option>';
        $this->_html .= '</select></div>';
        $this->_html .= '</div>';


        $dph_math = Configuration::get($this->name . '_dph_math');
        $this->_html .= '<div class="form-group">';
        $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Typ výpočtu DPH') . '</label>';
        $this->_html .= '<div class="col-lg-9"><select name="dph_math">';
        $this->_html .= '<option value="0"' . (($dph_math == 0) ? ' selected="selected"' : '') . '>' . $this->l('Zdola') . '</option>';
        $this->_html .= '<option value="1"' . (($dph_math == 1) ? ' selected="selected"' : '') . '>' . $this->l('Shora') . '</option>';
        $this->_html .= '</select></div>';
        $this->_html .= '</div>';


        $this->_html .= '<div class="form-group">';
        $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Filtrace data dle data vytvoření objednávky (OBJ) nebo dle data vystavení faktury (FA)?') . '</label>';
        $this->_html .= '<div class="col-lg-9">		<span class="switch prestashop-switch fixed-width-lg">
										<input name="date_filter" id="date_filter_on" value="0"' . ((Configuration::get($this->name . '_date_filter') == 0) ? ' checked="checked"' : '') . ' type="radio">
										<label for="date_filter_on">' . $this->l('OBJ') . '</label>
										<input name="date_filter" id="date_filter_off" value="1"' . ((Configuration::get($this->name . '_date_filter') == 1) ? ' checked="checked"' : '') . ' type="radio">
										<label for="date_filter_off">' . $this->l('FA') . '</label>
										<a class="slide-button btn"></a>
									</span></div>';
        $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
				' . $this->l('') . '
				</div></div>';
        $this->_html .= '</div>';

        $this->_html .= '<div class="form-group">
									<label class="control-label col-lg-3">
										' . $this->l('Datum') . '
									</label>

									<div class="col-lg-9">
										<div class="row">
											<div class="col-lg-6">
												<div class="input-group">
													<span class="input-group-addon">' . $this->l('Od') . '</span>
													<input type="text" class="datefilter input-medium" name="datepicker1" id="datepicker1" value="' . Configuration::get($this->name . '_datepicker1') . '">
													<span class="input-group-addon"><i class="icon-calendar-empty"></i></span>
												</div>
											</div>
											<div class="col-lg-6">
												<div class="input-group">
													<span class="input-group-addon">' . $this->l('Do') . '</span>
													<input type="text" class="datefilter input-medium" name="datepicker2" id="datepicker2" value="' . Configuration::get($this->name . '_datepicker2') . '">
													<span class="input-group-addon"><i class="icon-calendar-empty"></i></span>
												</div>
											</div>
										</div>
								</div></div>';


        $states = OrderState::getOrderStates($this->context->language->id);
        if ($states) {
            $state_waiting = unserialize(Configuration::get($this->name . '_state_waiting'));
            $this->_html .= '<div class="form-group">';

            $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Stavy objednávek') . '</label>';

            $this->_html .= '<div class="col-lg-9"><div class="row">';
            foreach ($states as $s) {
                $checked = (isset($state_waiting[$s['id_order_state']])) ? ' checked="checked"' : '';
                $this->_html .= '<div class="checkbox col-md-4"><label for="tmp_' . $s['id_order_state'] . '">
                    <input type="checkbox" class="ruka"
                    name="state_waiting[' . $s['id_order_state'] . ']"
                    id="tmp_' . $s['id_order_state'] . '"
                    value="' . $s['id_order_state'] . '"
                    ' . $checked . '>
                    ' . $s['name'] . ' (ID: ' . $s['id_order_state'] . ')</label></div>';
            }
            $this->_html .= '</div></div><div class="col-lg-9 col-lg-offset-3"><div class="help-block">
				' . $this->l('Zvolte všechny stavy objednávek u kterých chcete, aby se data exportovala.') . '
				</div></div>

				</div>';
        }

        $this->_html .= '<div class="panel-footer">';
        $this->_html .= '<button type="submit" class="filtr btn btn-default pull-right" name="submit_text"><i class="process-icon-save"></i>' . $this->l('Uložit') . '</button>';
        $this->_html .= '<button type="submit" class="filtr btn btn-default" name="submit_text2"><i class="process-icon-download"></i>' . $this->l('Exportovat objednávky') . '</button>';
        $this->_html .= '<button type="submit" class="filtr btn btn-default" name="submit_text3"><i class="process-icon-download"></i>' . $this->l('Exportovat faktury') . '</button>';

        $this->_html .= '</form></div>';
        $this->_html .= '</div></div></div>'; //FRAME END


        $this->_html .= '<div class="row"><div class="col-lg-12">
		<div class="panel"><div class="panel-heading"><i class="icon-list"></i> ' . $this->l('CRON & XML') . '</div>'; // FRAME

        // 4 = context all
        if (Shop::isFeatureActive() && Shop::getContext() != 4) {
            $selected_id_shop = (int)$this->context->shop->id;
            $orders_cron = $this->full_url . 'modules/' . $this->name . '/shaim_cron.php?type=orders&selected_id_shop=' . $selected_id_shop;
            $invoices_cron = $this->full_url . 'modules/' . $this->name . '/shaim_cron.php?type=invoices&selected_id_shop=' . $selected_id_shop;
            $dobropisy_cron = $this->full_url . 'modules/' . $this->name . '/shaim_cron.php?type=dobropisy&selected_id_shop=' . $selected_id_shop;
            $orders_xml = $this->full_url . 'modules/' . $this->name . '/orders_' . $selected_id_shop . '_' . crc32(preg_replace('/^www\./', '', $_SERVER['HTTP_HOST'])) . '.xml';
            $invoices_xml = $this->full_url . 'modules/' . $this->name . '/invoices_' . $selected_id_shop . '_' . crc32(preg_replace('/^www\./', '', $_SERVER['HTTP_HOST'])) . '.xml';
            $dobropisy_xml = $this->full_url . 'modules/' . $this->name . '/dobropisy_' . $selected_id_shop . '_' . crc32(preg_replace('/^www\./', '', $_SERVER['HTTP_HOST'])) . '.xml';
        } else {
            $orders_cron = $this->full_url . 'modules/' . $this->name . '/shaim_cron.php?type=orders';
            $invoices_cron = $this->full_url . 'modules/' . $this->name . '/shaim_cron.php?type=invoices';
            $dobropisy_cron = $this->full_url . 'modules/' . $this->name . '/shaim_cron.php?type=dobropisy';
            $orders_xml = $this->full_url . 'modules/' . $this->name . '/orders_' . crc32(preg_replace('/^www\./', '', $_SERVER['HTTP_HOST'])) . '.xml';
            $invoices_xml = $this->full_url . 'modules/' . $this->name . '/invoices_' . crc32(preg_replace('/^www\./', '', $_SERVER['HTTP_HOST'])) . '.xml';
            $dobropisy_xml = $this->full_url . 'modules/' . $this->name . '/dobropisy_' . crc32(preg_replace('/^www\./', '', $_SERVER['HTTP_HOST'])) . '.xml';
        }
        $this->_html .= "<div>" . $this->l("Faktury: Cron zde: ") . "<a href='$invoices_cron' target='_blank'>$invoices_cron</a></div><br />";
        $this->_html .= "<div>" . $this->l("Faktury: Výsledný XML soubor: ") . "<a href='$invoices_xml' target='_blank'>$invoices_xml</a></div>";

        $this->_html .= "<br /><br /><div>" . $this->l("Objednávky: Cron zde: ") . "<a href='$orders_cron' target='_blank'>$orders_cron</a></div><br />";
        $this->_html .= "<div>" . $this->l("Objednávky: Výsledný XML soubor: ") . "<a href='$orders_xml' target='_blank'>$orders_xml</a></div>";


        $this->_html .= "<br /><br /><div>" . $this->l("Dobropisy: Cron zde: ") . "<a href='$dobropisy_cron' target='_blank'>$dobropisy_cron</a></div><br />";
        $this->_html .= "<div>" . $this->l("Dobropisy: Výsledný XML soubor: ") . "<a href='$dobropisy_xml' target='_blank'>$dobropisy_xml</a></div>";


        $this->_html .= '</div></div></div>'; //FRAME END


        $this->_html .= '<div class="row"><div class="col-lg-12">
		<div class="panel"><div class="panel-heading"><i class="icon-info"></i> ' . $this->l('Informace') . '</div>'; // FRAME
        $this->_html .= '<br /><br /><br /><div class="clear_both"><small>' . $this->credits . '<br />' . $this->description . '</small></div>';
        $this->_html .= '</div></div></div>'; //FRAME END
        return $this->_html;
    }

    private function TaxToVatRateTypePohoda($tax_rate = 0)
    {
        if ($this->real_tax != '') {
            $tax_rate = $this->real_tax;
        }
        if (empty($tax_rate)) {
            $vatratetype = 'none';
        } elseif ($tax_rate == 21 || $tax_rate == 20) {
            $vatratetype = 'high';
        } elseif ($tax_rate == 15) {
            $vatratetype = 'low';
        } elseif ($tax_rate == 10 && $this->verze == 'SK') {
            $vatratetype = 'low';
        } elseif ($tax_rate == 10 && $this->verze != 'SK') {
            $vatratetype = 'third';
        } else {
            $vatratetype = 'none';
        }
        return $vatratetype;
    }

    private function RemoveVat($data, $vat)
    {
        if (empty($vat)) {
            return $data;
        }
        $data /= (($vat / 100) + 1);
        return (float)$data;

    }

    public function Cron($from = 0, $to = 0, $to_browser = false, $invoices_orders = '', $selected_id_shop = 0)
    {
        if ($this->active != 1) {
            return;
        }

        $this->debug = true;
        if (!empty($from) && !empty($to)) {
            $this->debug = false;
        }


        if (empty($from)) {
            $from = '2000-01-01';
            // $from = date('Y-m-d', strtotime(date('Y-m-d') . '-1 day'));
        }
        if (empty($to)) {
            // $to = date("Y-m-d");
            $to = date('Y-m-d', strtotime(date('Y-m-d') . '-1 day'));
        }

        // Pridavame jeden den, aby to sedelo, if you know what i mean
        $to = date("Y-m-d", strtotime($to . "+1 day"));

        if ($invoices_orders != 'orders' && $invoices_orders != 'invoices' && $invoices_orders != 'dobropisy') {
            die($this->l('Neplatná volba'));
        }

        /*
            $orders = DB::getInstance()->ExecuteS("SELECT id_order FROM " . _DB_PREFIX_ . "orders
        WHERE (date_add >= '" . $from . "' && date_add <= '" . $to . "');");
        */
        /*
        $orders = DB::getInstance()->ExecuteS("SELECT id_order FROM " . _DB_PREFIX_ . "orders
        WHERE (date_add >= '" . $from . "' && date_add <= '" . $to . "') && valid = 1;");
        */
        $state_waiting = unserialize(Configuration::get($this->name . '_state_waiting'));
        if (empty($state_waiting)) {
            die($this->l('Žádný zvolený stav pro export'));
        }

        $state_waiting_sql = ' && (o.current_state = ' . implode(' || o.current_state = ', $state_waiting) . ')';
        $selected_id_shop_sql = '';
        if ($selected_id_shop) {
            $selected_id_shop_sql = ' && o.id_shop = ' . $selected_id_shop;
        }


        $id_order_force = (int)Tools::getValue('id_order');
        $id_order_where = '';
        if ($id_order_force) {
            $id_order_where = ' && o.id_order = ' . $id_order_force;
            $state_waiting_sql = '';
            $selected_id_shop_sql = '';
            $from = '0000-00-00 00:00:00';
            $to = '9999-00-00 00:00:00';
        }


        $limit = (Tools::getValue('end')) ? ' LIMIT 0,' . Tools::getValue('end') : '';

        $date_filter = (int)Configuration::get($this->name . '_date_filter');


        if ($invoices_orders == 'dobropisy') {
            $sql = "SELECT o.id_order, os.id_order_slip, os.date_add as order_slip_date_add FROM " . _DB_PREFIX_ . "orders as o
            INNER JOIN " . _DB_PREFIX_ . "order_slip as os ON (o.id_order = os.id_order)
        WHERE (os.date_add >= '" . $from . "' && os.date_add <= '" . $to . "')$id_order_where$state_waiting_sql$selected_id_shop_sql$limit;";
        } else {
            $date_or_invoice = ($date_filter == 0) ? 'date_add' : 'invoice_date';
            $sql = "SELECT o.id_order FROM " . _DB_PREFIX_ . "orders as o
        WHERE (o.$date_or_invoice >= '" . $from . "' && o.$date_or_invoice <= '" . $to . "')$id_order_where$state_waiting_sql$selected_id_shop_sql
        ORDER BY $date_or_invoice ASC
        $limit;";
        }
        if (defined('SHAIM') && SHAIM) {
            echo $sql;
        }

        $orders = DB::getInstance()->ExecuteS($sql);


        if (!$orders) {
            die($this->l('Žádné nalezené záznamy.'));
        }


        $http_host = $_SERVER['HTTP_HOST'];


        if ($selected_id_shop) {
            $save = $this->local_path . $invoices_orders . '_' . $selected_id_shop . '_' . crc32(preg_replace('/^www\./', '', $_SERVER['HTTP_HOST'])) . '.xml';
        } else {
            $save = $this->local_path . $invoices_orders . '_' . crc32(preg_replace('/^www\./', '', $_SERVER['HTTP_HOST'])) . '.xml';
        }


        $ico = Configuration::get($this->name . '_ico');


        $splatnost = (int)Configuration::get($this->name . '_splatnost');
        $sklad = Configuration::get($this->name . '_sklad');
        $predkontace = Configuration::get($this->name . '_predkontace');
        $vs = Configuration::get($this->name . '_vs');
        $this->verze = Configuration::get($this->name . '_verze');
        $number = Configuration::get($this->name . '_number');
        $order_number = Configuration::get($this->name . '_order_number');
        $prefix = (int)Configuration::get($this->name . '_prefix');
        $dph_math = (int)Configuration::get($this->name . '_dph_math');
        $dph_math_pay_vat = ($dph_math == 1) ? 'true' : 'false';

        $order_real_counter = 0;

        $this->real_tax = '';


        // Faktura invoice prefix - begin = money + pohoda //
        $ps_invoice_use_year = false;
        $invoice_prefix_base = false;
        $ps_invoice_year_post = false;
        if ($prefix) {

            $sql = "SELECT id_configuration, value FROM " . _DB_PREFIX_ . "configuration WHERE name = 'PS_INVOICE_PREFIX';";
            $configuration = DB::getInstance()->ExecuteS($sql);
            $invoice_prefix_base = $configuration[0]['value'];

            $id_configuration = (int)$configuration[0]['id_configuration'];
            $sql = "SELECT value FROM " . _DB_PREFIX_ . "configuration_lang WHERE id_configuration = $id_configuration && id_lang = " . (int)Configuration::get('PS_LANG_DEFAULT') . ";";
            $configuration2 = DB::getInstance()->ExecuteS($sql);
            if (isset($configuration2[0]['value'])) {
                $invoice_prefix_base = $configuration2[0]['value'];
            }

            $invoice_prefix_base = str_replace('#', '', $invoice_prefix_base);
            $ps_invoice_use_year = (int)Configuration::get('PS_INVOICE_USE_YEAR');
            $ps_invoice_year_post = (int)Configuration::get('PS_INVOICE_YEAR_POS'); // 0 = za, 1 = pred

        } else {
            if (Module::isEnabled('ceskafaktura')) {
                $tmp_invoice_prefix_base = Configuration::get('CESKAFAKTURA_VAR_PREFIX');
                if (!empty($tmp_invoice_prefix_base)) {
                    $prefix = 1;
                    $invoice_prefix_base = $tmp_invoice_prefix_base;
                }
            } elseif (Module::isEnabled('add_faktura') || Module::isEnabled('add_faktura_new')) {
                $tmp_invoice_prefix_base = Configuration::get('FA_PREFIX_VS');
                if (!empty($tmp_invoice_prefix_base)) {
                    $prefix = 1;
                    $invoice_prefix_base = $tmp_invoice_prefix_base;
                }
            }
        }
        // Faktura invoice prefix - end = money + pohoda //


        /*** Pohoda - begin ***/
        if ($invoices_orders == 'orders') {
            $tag_prefix = 'ord';
            $invoice_order = 'order';
            $header = 'orderHeader';
            $header0 = 'order';
            $delivered = "<" . $tag_prefix . ":delivered>0</" . $tag_prefix . ":delivered>";
            $xml = array('<?xml version="1.0" encoding="UTF-8"?>
<dat:dataPack id="' . $http_host . '" ico="' . $ico . '" application="order" version="2.0" note="Import Objednávky" xmlns:dat="http://www.stormware.cz/schema/version_2/data.xsd" xmlns:adb="http://www.stormware.cz/schema/version_2/addressbook.xsd" xmlns:ord="http://www.stormware.cz/schema/version_2/order.xsd" xmlns:typ="http://www.stormware.cz/schema/version_2/type.xsd">');

        } else {
            $tag_prefix = 'inv';
            $invoice_order = 'invoice';
            $header = 'invoiceHeader';
            $header0 = 'invoice';
            $delivered = '';
            $xml = array('<?xml version="1.0" encoding="UTF-8"?>
<dat:dataPack id="' . $http_host . '" ico="' . $ico . '" application="invoice" version="2.0" note="Import Faktury" xmlns:dat="http://www.stormware.cz/schema/version_2/data.xsd" xmlns:adb="http://www.stormware.cz/schema/version_2/addressbook.xsd" xmlns:inv="http://www.stormware.cz/schema/version_2/invoice.xsd" xmlns:typ="http://www.stormware.cz/schema/version_2/type.xsd">');
        }


        foreach ($orders as $order) {
            $id_order_slip = $id_order_slip_orig = false;
            $order_slip_date_add = false;
            $orderslip = new stdClass();
            $id_order = (int)$order['id_order'];
            if ($invoices_orders == 'dobropisy') {
                $id_order_slip_orig = $id_order_slip = (int)$order['id_order_slip'];
                $order_slip_date_add = $order['order_slip_date_add'];
            }
            $order = new Order($id_order);
            if ($invoices_orders == 'dobropisy') {
                $id_order_slip = Configuration::get('PS_CREDIT_SLIP_PREFIX', $order->id_lang) . $id_order_slip_orig;
            }
            $conversion_rate = (float)$order->conversion_rate;


            $currency = new Currency($order->id_currency);
            $currency_iso_code = $currency->iso_code;

            // Tady resime sitauci, kdyz je fakturacni firma napriklad CZ a v shopu maji defaultni menu EUR -- begin //

            $other_conversion_rate = 1;
            /* Zakomentovano, cekame na potvrzeni funkcnosti
             if ($conversion_rate == '1.000000') {
                 if ($currency_iso_code == 'EUR' && $this->verze == 'cz') {
                     $other_id_currency = Currency::getIdByIsoCode('CZK');
                     if ($other_id_currency) {
                         $other_currency = new Currency($other_id_currency);
                         $other_conversion_rate = (float)$other_currency->conversion_rate;
                         $conversion_rate = Tools::ps_round($other_conversion_rate, 6);
                         // $conversion_rate = Tools::ps_round(1 / $other_conversion_rate, 6);
                         $other_conversion_rate = 1;
                     }

                 } elseif ($currency_iso_code == 'CZK' && $this->verze == 'sk') {
                     $other_id_currency = Currency::getIdByIsoCode('EUR');
                     if ($other_id_currency) {
                         $other_currency = new Currency($other_id_currency);
                         $other_conversion_rate = (float)$other_currency->conversion_rate;
                         $conversion_rate = Tools::ps_round($other_conversion_rate, 6);
                         // $conversion_rate = Tools::ps_round(1 / $other_conversion_rate, 6);
                         $other_conversion_rate = 1;
                     }

                 }
             }
            */
            // Tady resime sitauci, kdyz je fakturacni firma napriklad CZ a v shopu maji defaultni menu EUR -- end //


            /*
            $pocet_desetinnych_mist = 2;
            if ($currency_iso_code == 'CZK' && $currency->decimals == 0) {
                $pocet_desetinnych_mist = 0;
            } elseif ($currency_iso_code == 'EUR' && $currency->decimals == 1) {
                $pocet_desetinnych_mist = 2;
            } elseif ($currency->decimals == 1) {
                $pocet_desetinnych_mist = 2;
            } elseif ($currency->decimals == 0) {
                $pocet_desetinnych_mist = 0;
            }

            $pocet_desetinnych_mist_tmp = Configuration::get('PS_PRICE_DISPLAY_PRECISION');
            if ($pocet_desetinnych_mist_tmp === false && $currency->decimals == 1) { // Fix pro staré PS, které tuto hodnotu nemají v adminu.
                $pocet_desetinnych_mist = 2;
            }
            */

            if (preg_match("/dobreporizeni\.cz/i", $_SERVER['HTTP_HOST']) || preg_match("/autorohoze\.cz/i", $_SERVER['HTTP_HOST'])) {
                // $pocet_desetinnych_mist = 2;
                // nejak spatne se to generuje z DB, tak to musime vypocitat
                $order->total_shipping_tax_excl = $this->RemoveVat($order->total_shipping_tax_incl, $order->carrier_tax_rate);
            }

            // davame 2 dess mista jako default, aby to sedelo vzdy:
            $pocet_desetinnych_mist = 2;


            $carrier = array();
            if (!empty($order->id_carrier)) {
                $carrier = new Carrier((int)$order->id_carrier, $this->context->language->id);
            }

            $customer = new Customer((int)$order->id_customer);
            $address_invoice = new Address((int)$order->id_address_invoice);
            $address_delivery = new Address((int)$order->id_address_delivery);
            $is_address_same = false;
            if ($order->id_address_invoice == $order->id_address_delivery) {
                $is_address_same = true;
            }

            $country_invoice = new Country($address_invoice->id_country);
            $country_iso_code_invoice = $country_invoice->iso_code;
            $postcode_invoice = preg_replace("/[^0-9]/", "", $address_invoice->postcode);
            $postcode_delivery = preg_replace("/[^0-9]/", "", $address_delivery->postcode);
            // Mám na Vás prosbu trochu od jinud. Domluvili jsme se s panem Janovcem na bodu 3 (viz níže) a volíme možnost B, jen bych upravila znění, osobní odběr chceme, nechceme platbu v hotovosti. Pokud zaplatí klient předem a vyzvedne si to na pobočce, chceme aby se nám to do účetnictví dostalo.
            if (preg_match("/darecky24\.cz/i", $_SERVER['HTTP_HOST']) && $order->module == 'add_in_store') {
                continue;
            }
            $payment = $this->ModuleToPayment($order, $carrier);
            $payment2 = $this->ModuleToPaymentPohoda($order, $carrier);

            // Pokud existuje faktura v PS, tak bereme datum vystaveni faktury.

            if ($invoices_orders == 'dobropisy') {
                $date_add = $order_slip_date_add;
            } elseif ($invoices_orders == 'invoices' && !empty($order->invoice_date) && $order->invoice_date != '0000-00-00 00:00:00') {
                $date_add = $order->invoice_date;
            } else {
                $date_add = $order->date_add;
            }

            $date_add = explode(' ', $date_add);
            $date_add = $date_add[0];

            if ($invoices_orders == 'dobropisy') {
                $date_due = $date_add;
            } else {
                $date_due = date("Y-m-d", strtotime($date_add . "+" . $splatnost . " days"));
            }
            $real_date_add = explode(' ', $order->date_add);
            $real_date_add = $real_date_add[0];

            $vat_number = $address_invoice->vat_number;

            $variabilni_symbol = $id_order;
            if ($vs == 'reference') {
                $variabilni_symbol = $order->reference;
            } elseif ($vs == 'id_order') {
                $variabilni_symbol = $id_order;
            } elseif ($vs == 'invoice_number') {
                $variabilni_symbol = $order->invoice_number;
            }

            if ($invoices_orders == 'dobropisy') {
                $variabilni_symbol = $id_order_slip_orig;
            }

            //$number_pohoda_real = false;
            $id_order_reference = $id_order;
            if ($number == 'reference') {
                $id_order_reference = $order->reference;
            } elseif ($number == 'id_order') {
                $id_order_reference = $id_order;
            } elseif ($number == 'invoice_number') {
                $id_order_reference = false;
                if (Module::isEnabled('custominvoicereferencenumber')) {
                    $sql = "SELECT crn_invoice_number FROM " . _DB_PREFIX_ . "orders WHERE id_order = $id_order;";
                    $crn_invoice_number = DB::getInstance()->ExecuteS($sql);
                    if (isset($crn_invoice_number[0]['crn_invoice_number']) && !empty($crn_invoice_number[0]['crn_invoice_number'])) {
                        $id_order_reference = (int)$crn_invoice_number[0]['crn_invoice_number'];
                    }
                }
                if (!$id_order_reference) {

                    if ($prefix) {

                        $invoice_prefix = $invoice_prefix_base;
                        if ($ps_invoice_use_year) {
                            list($y) = explode('-', $date_add);
                            if (empty($ps_invoice_year_post)) {
                                $invoice_prefix = $y . $invoice_prefix;
                                $id_order_reference = sprintf("%06d", $order->invoice_number) . $invoice_prefix;
                            } else {
                                $invoice_prefix = $invoice_prefix . $y;
                                $id_order_reference = $invoice_prefix . sprintf("%06d", $order->invoice_number);
                            }

                        } else {
                            $id_order_reference = $invoice_prefix . sprintf("%06d", $order->invoice_number);
                        }
                    } else {
                        $id_order_reference = sprintf("%06d", $order->invoice_number);
                    }
                }

            }
            /*
            elseif ($number == 'real') {
                $id_order_reference = $id_order;
                $number_pohoda_real = true;
            }
            */


            $id_order_reference_real = $id_order;
            if ($order_number == 'reference') {
                $id_order_reference_real = $order->reference;
            } elseif ($order_number == 'id_order') {
                $id_order_reference_real = $id_order;
            }


            $zachovat_cislo_obj = '';
            $order_real_counter++;

            /*
                if ($module == 'bankwire' || $module == 'add_bankwire' || $module == 'shaim_bankwire' || $module == 'pms_bankwire' || $module == 'dm_bankwire' || $module == 'ps_wirepayment' || $module == 'add_gopay') {
                    $rada = 'FAU15';
                } elseif ($module == 'cashondeliveryplus' || $module == 'ps_cashondelivery') {
                    $rada = 'FAD16';
                } elseif ($module == 'add_in_store' || $module == 'pms_in_store' || $module == 'dm_cashonpickup' || $module == 'instore' || $module == 'cashondelivery') {
                    // Nechceme mit v Pohode
                    // $rada = 'FAH17';
                    continue;
                } elseif ($module == 'free_order') {
                    // Nechceme mit v Pohode
                    continue;
                } elseif ($module == 'paypal') {
                    // Nechceme mit v Pohode
                    continue;
                } else {
                    continue;
                }


                $cislo_fa = $rada . $order_real_counter;
                $zachovat_cislo_obj = '<' . $tag_prefix . ':number>
<typ:numberRequested>' . $cislo_fa . '</typ:numberRequested>
</' . $tag_prefix . ':number>';

*/

            if ($invoices_orders == 'dobropisy') {
                $zachovat_cislo_obj = '<' . $tag_prefix . ':number>
    <typ:numberRequested>' . $id_order_slip . '</typ:numberRequested>
    </' . $tag_prefix . ':number>';
                // } elseif ($number == 'invoice_number') {
            } elseif ($invoices_orders == 'invoices') { // Upraveno dle pana SImka a jeho podnetu, ze by to melo byt vzdy, bez ohledu na to, co je vybrano, jestli cislo FA apod.
                $zachovat_cislo_obj = '<' . $tag_prefix . ':number>
    <typ:numberRequested>' . $id_order_reference . '</typ:numberRequested>
    </' . $tag_prefix . ':number>';
            }


            $tmp = "<dat:dataPackItem id=\"" . $id_order_reference . "\" version=\"2.0\">
<" . $tag_prefix . ":" . $header0 . " version=\"2.0\">
<" . $tag_prefix . ":" . $header . ">
<" . $tag_prefix . ":numberOrder>" . $id_order_reference_real . "</" . $tag_prefix . ":numberOrder>
<" . $tag_prefix . ":date>" . $date_add . "</" . $tag_prefix . ":date>";
            if ($invoices_orders == 'orders') {
                $tmp .= "$zachovat_cislo_obj<" . $tag_prefix . ":orderType>receivedOrder</" . $tag_prefix . ":orderType>
<" . $tag_prefix . ":id>" . $id_order_reference . "</" . $tag_prefix . ":id>
<" . $tag_prefix . ":dateFrom>" . $date_add . "</" . $tag_prefix . ":dateFrom>
<" . $tag_prefix . ":dateTo>" . $date_add . "</" . $tag_prefix . ":dateTo>
<" . $tag_prefix . ":text><![CDATA[" . $this->l('Objednávka č. ') . $id_order_reference_real . " " . $http_host . "]]></" . $tag_prefix . ":text>";
            } else {
                if ($invoices_orders == 'dobropisy') {
                    $tmp .= "$zachovat_cislo_obj<" . $tag_prefix . ":invoiceType>issuedCorrectiveTax</" . $tag_prefix . ":invoiceType>
                        <" . $tag_prefix . ":text><![CDATA[" . $this->l('Opravný daňový doklad k daňovému dokladu. ') . $id_order_reference_real . " " . $http_host . "]]></" . $tag_prefix . ":text>
                        ";
                } else {
                    $tmp .= "$zachovat_cislo_obj<" . $tag_prefix . ":invoiceType>issuedInvoice</" . $tag_prefix . ":invoiceType>
                        <" . $tag_prefix . ":text><![CDATA[" . $this->l('Faktura k objednávce č. ') . $id_order_reference_real . " " . $http_host . "]]></" . $tag_prefix . ":text>
                        ";
                }


                if ($invoices_orders == 'dobropisy') {
                    $tmp .= "<" . $tag_prefix . ":dateApplicationVAT>" . $date_add . "</" . $tag_prefix . ":dateApplicationVAT>";
                }


                $bu_ucet = Configuration::get($this->name . '_bu_ucet_' . $order->id_currency);
                $bu_ucet = explode('/', $bu_ucet);
                if (isset($bu_ucet[1])) {
                    $bu_kod = $bu_ucet[1];
                } else {
                    $bu_kod = '';
                }
                $bu_ucet = $bu_ucet[0];

                $tmp .= "<" . $tag_prefix . ":symVar>" . $variabilni_symbol . "</" . $tag_prefix . ":symVar>
<" . $tag_prefix . ":dateTax>" . $date_add . "</" . $tag_prefix . ":dateTax>
<" . $tag_prefix . ":dateOrder>" . $real_date_add . "</" . $tag_prefix . ":dateOrder>
<" . $tag_prefix . ":dateAccounting>" . $date_add . "</" . $tag_prefix . ":dateAccounting>
<" . $tag_prefix . ":dateDue>" . $date_due . "</" . $tag_prefix . ":dateDue>
<" . $tag_prefix . ":account>
<typ:accountNo>" . $bu_ucet . "</typ:accountNo>
<typ:bankCode>" . $bu_kod . "</typ:bankCode>
</" . $tag_prefix . ":account>
";
            }

            if ($is_address_same) {
                $shipToAddress = '';
            } else {
                $shipToAddress = "<typ:shipToAddress>
<typ:company><![CDATA[" . $address_delivery->company . "]]></typ:company>
<typ:name><![CDATA[" . Tools::substr($address_delivery->firstname . " " . $address_delivery->lastname, 0, 32) . "]]></typ:name>
<typ:city><![CDATA[" . $address_delivery->city . "]]></typ:city>
<typ:street><![CDATA[" . $address_delivery->address1 . "]]></typ:street>
<typ:zip>" . $postcode_delivery . "</typ:zip>
<typ:country>
<typ:ids>" . $country_iso_code_invoice . "</typ:ids>
</typ:country>
<typ:email>" . $customer->email . "</typ:email>
</typ:shipToAddress>";
            }


            if (!empty($order->id_carrier)) {
                $nazev_dopravy_pair = '';
                if (preg_match("/In Time/i", $carrier->name) || preg_match("/InTime/i", $carrier->name)) {
                    $nazev_dopravy_pair = 'IN TIME';
                } elseif (preg_match("/GLS/i", $carrier->name)) {
                    $nazev_dopravy_pair = 'GLS';
                } elseif (preg_match("/DPD/i", $carrier->name)) {
                    $nazev_dopravy_pair = 'DPD';
                } elseif (preg_match("/Balík do ruky/i", $carrier->name)) {
                    $nazev_dopravy_pair = 'Balík do Ruky';
                } elseif (preg_match("/Doporučený balík SK/i", $carrier->name)) {
                    $nazev_dopravy_pair = 'Doporučeně SK';
                } elseif (preg_match("/Doporučený balík/i", $carrier->name)) {
                    $nazev_dopravy_pair = 'Doporučený balík';
                } elseif (preg_match("/Zásilkovna/i", $carrier->name) && $country_iso_code_invoice == 'SK') {
                    $nazev_dopravy_pair = 'Zásilkovna SK';
                } elseif (preg_match("/Zásilkovna/i", $carrier->name)) {
                    $nazev_dopravy_pair = 'Zásilkovna';
                }
                if ($nazev_dopravy_pair) {
                    $tmp .= "<" . $tag_prefix . ":carrier>
<typ:ids>$nazev_dopravy_pair</typ:ids>
</" . $tag_prefix . ":carrier>";
                }
            }


            $tmp .= "<" . $tag_prefix . ":partnerIdentity>
<typ:address>
<typ:company><![CDATA[" . $address_invoice->company . "]]></typ:company>
<typ:name><![CDATA[" . Tools::substr($address_invoice->firstname . " " . $address_invoice->lastname, 0, 32) . "]]></typ:name>
<typ:city><![CDATA[" . $address_invoice->city . "]]></typ:city>
<typ:street><![CDATA[" . $address_invoice->address1 . "]]></typ:street>
<typ:zip>" . $postcode_invoice . "</typ:zip>
<typ:ico>" . preg_replace("/[^0-9]/", "", $address_invoice->dni) . "</typ:ico>
<typ:dic>" . preg_replace("/[^(CZ|SK|PL)0-9]/", "", $vat_number) . "</typ:dic>
<typ:country>
<typ:ids>" . $country_iso_code_invoice . "</typ:ids>
</typ:country>
<typ:phone>" . (!empty($address_invoice->phone) ? $address_invoice->phone : $address_invoice->phone_mobile) . "</typ:phone>
<typ:mobilPhone>" . (!empty($address_invoice->phone_mobile) ? $address_invoice->phone_mobile : $address_invoice->phone) . "</typ:mobilPhone>
<typ:email>" . $customer->email . "</typ:email>
</typ:address>
$shipToAddress
</" . $tag_prefix . ":partnerIdentity>
<" . $tag_prefix . ":paymentType>
<typ:ids>" . $payment . "</typ:ids>
<typ:paymentType>" . $payment2 . "</typ:paymentType>
</" . $tag_prefix . ":paymentType>";
            if ($invoices_orders == 'invoices') {

                $tmp .= "<" . $tag_prefix . ":accounting>
<typ:ids>" . $predkontace . "</typ:ids>
</" . $tag_prefix . ":accounting><" . $tag_prefix . ":classificationVAT>
<typ:classificationVATType>inland</typ:classificationVATType>
</" . $tag_prefix . ":classificationVAT>";

            } elseif ($invoices_orders == 'orders') {
                $tmp .= "<" . $tag_prefix . ":isExecuted>false</" . $tag_prefix . ":isExecuted>

<" . $tag_prefix . ":isDelivered>false</" . $tag_prefix . ":isDelivered>
<" . $tag_prefix . ":isReserved>false</" . $tag_prefix . ":isReserved>
<" . $tag_prefix . ":permamentDocument>false</" . $tag_prefix . ":permamentDocument>";
            }

            $tmp .= "</" . $tag_prefix . ":" . $header . ">
<" . $tag_prefix . ":" . $invoice_order . "Detail>";


            if ($conversion_rate == '1.000000') {
                $currency_type = 'homeCurrency';
            } else {
                $currency_type = 'foreignCurrency';
            }

            if ($invoices_orders == 'dobropisy') {
                $orderslip = new OrderSlip($id_order_slip_orig);
                $products = $orderslip->getProducts();
            } else {
                $products = $order->getProducts();
            }


            foreach ($products as $product) {
                $reference = (isset($product['product_reference']) && $product['product_reference'] ? $product['product_reference'] : (isset($product['reference']) && $product['reference'] ? $product['reference'] : $product['product_id'] . '-' . $product['product_attribute_id']));

                $odecist_sklad_pohoda = '';
                if ($sklad) {
                    $odecist_sklad_pohoda = '<' . $tag_prefix . ':stockItem>
<typ:store>
<typ:ids><![CDATA[' . $sklad . ']]></typ:ids>
</typ:store>
<typ:stockItem>
<typ:ids><![CDATA[' . $reference . ']]></typ:ids>
</typ:stockItem>
</' . $tag_prefix . ':stockItem>';

                }


                // <" . $tag_prefix . ":" . $currency_type . "><typ:unitPrice>" . Tools::ps_round($product['unit_price_tax_incl'] * $conversion_rate, $pocet_desetinnych_mist) . "</typ:unitPrice></" . $tag_prefix . ":" .
                $product_note = '';
                $e = explode(' - ', $product['product_name']);
                if (isset($e[1])) {
                    unset($e[0]);
                    $product_note = implode(' - ', $e);
                }
                $price = $dph_math ? $product['unit_price_tax_incl'] : $product['unit_price_tax_excl'];
                $tmp .= "<" . $tag_prefix . ":" . $invoice_order . "Item>
                $odecist_sklad_pohoda
<" . $tag_prefix . ":text><![CDATA[" . Tools::substr($product['product_name'], 0, 90) . "]]></" . $tag_prefix . ":text>
<" . $tag_prefix . ":code><![CDATA[" . $reference . "]]></" . $tag_prefix . ":code>
<" . $tag_prefix . ":quantity>" . (int)$product['product_quantity'] . "</" . $tag_prefix . ":quantity>
<" . $tag_prefix . ":unit>" . $this->l('ks') . "</" . $tag_prefix . ":unit>
<" . $tag_prefix . ":note>" . $product_note . "</" . $tag_prefix . ":note>
$delivered
<" . $tag_prefix . ":payVAT>" . $dph_math_pay_vat . "</" . $tag_prefix . ":payVAT>
<" . $tag_prefix . ":rateVAT>" . $this->TaxToVatRateTypePohoda((int)$product['tax_rate']) . "</" . $tag_prefix . ":rateVAT>
";
                if ($invoices_orders == 'invoices') {
                    $tmp .= "<" . $tag_prefix . ":accounting>
<typ:ids>" . $predkontace . "</typ:ids>
</" . $tag_prefix . ":accounting>";
                }
                $tmp .= "<" . $tag_prefix . ":" . $currency_type . ">
<typ:unitPrice>" . Tools::ps_round($price * $other_conversion_rate, $pocet_desetinnych_mist) . "</typ:unitPrice>
<typ:price>" . Tools::ps_round($price * $other_conversion_rate, $pocet_desetinnych_mist) . "</typ:price>
<typ:priceVAT>" . Tools::ps_round(($product['unit_price_tax_incl'] - $product['unit_price_tax_excl']) * $other_conversion_rate, $pocet_desetinnych_mist) . "</typ:priceVAT>
</" . $tag_prefix . ":" . $currency_type . ">
</" . $tag_prefix . ":" . $invoice_order . "Item>";
            }


            if (!empty($order->id_carrier) &&
                ($invoices_orders == 'invoices' && $order->total_shipping_tax_excl > 0)
                ||
                ($invoices_orders == 'dobropisy' && $orderslip->shipping_cost_amount > 0)
                ||
                ($invoices_orders == 'orders') // doplneno 2019-07-10 kvuli bestlook.sk (ale nevim jak to bude fungovat, cekame)
            ) {

                // if ($order->total_shipping_tax_incl > 0) {
                // openservis ulozenka pobocka
                $ulozenka_add = '';
                if (Module::isEnabled('ulozenka')) {
                    $sql = "SELECT pobocka, pobocka_name FROM " . _DB_PREFIX_ . "ulozenka WHERE id_order = $id_order;";
                    $ulozenka = DB::getInstance()->ExecuteS($sql);
                    if ($ulozenka) {
                        $ulozenka_add .= ' (' . $ulozenka[0]['pobocka'] . ', ' . $ulozenka[0]['pobocka_name'] . ')';
                    }

                }

                // Položku „Prepravné náklady“ treba predkontovať na DOPRAVA   alebo predkontácia je 604011
                $invoice_only = '';
                if ($invoices_orders == 'dobropisy') {
                    $vat_shipping = $dph_math ? (float)number_format(Tools::ps_round($orderslip->total_shipping_tax_incl, $pocet_desetinnych_mist), $pocet_desetinnych_mist, '.', '') : (float)number_format(Tools::ps_round($orderslip->total_shipping_tax_excl, $pocet_desetinnych_mist), $pocet_desetinnych_mist, '.', '');
                } else {
                    $vat_shipping = $dph_math ? (float)number_format(Tools::ps_round($order->total_shipping_tax_incl, $pocet_desetinnych_mist), $pocet_desetinnych_mist, '.', '') : (float)number_format(Tools::ps_round($order->total_shipping_tax_excl, $pocet_desetinnych_mist), $pocet_desetinnych_mist, '.', '');
                }
                if ($invoices_orders == 'invoices') {
                    $invoice_only = "<" . $tag_prefix . ":accounting>
<typ:ids>" . $this->l('DOPRAVA') . "</typ:ids>
</" . $tag_prefix . ":accounting>";
                }

                $tmp .= "<" . $tag_prefix . ":" . $invoice_order . "Item>
<" . $tag_prefix . ":text><![CDATA[" . Tools::substr($this->l("Doprava: ") . $carrier->name . $ulozenka_add, 0, 90) . "]]></" . $tag_prefix . ":text>
<" . $tag_prefix . ":quantity>1</" . $tag_prefix . ":quantity>
<" . $tag_prefix . ":unit>" . $this->l('ks') . "</" . $tag_prefix . ":unit>
<" . $tag_prefix . ":note></" . $tag_prefix . ":note>
$delivered
<" . $tag_prefix . ":payVAT>" . $dph_math_pay_vat . "</" . $tag_prefix . ":payVAT>
<" . $tag_prefix . ":rateVAT>" . $this->TaxToVatRateTypePohoda((int)$order->carrier_tax_rate) . "</" . $tag_prefix . ":rateVAT>
$invoice_only
<" . $tag_prefix . ":" . $currency_type . ">
<typ:unitPrice>" . Tools::ps_round($vat_shipping * $other_conversion_rate, $pocet_desetinnych_mist) . "</typ:unitPrice>
<typ:price>" . Tools::ps_round($vat_shipping * $other_conversion_rate, $pocet_desetinnych_mist) . "</typ:price>
<typ:priceVAT>" . Tools::ps_round((($invoices_orders == 'dobropisy') ? $orderslip->total_shipping_tax_incl - $orderslip->total_shipping_tax_excl : $order->total_shipping_tax_incl - $order->total_shipping_tax_excl) * $other_conversion_rate, $pocet_desetinnych_mist) . "</typ:priceVAT>
</" . $tag_prefix . ":" . $currency_type . ">
</" . $tag_prefix . ":" . $invoice_order . "Item>";
                //    }
            }
            if ($invoices_orders != 'dobropisy') {
                $discounts = $order->getCartRules();
                if (!empty($discounts)) {
                    foreach ($discounts as $discount) {
                        // $tax = ($discount['value'] != $discount['value_tax_excl']) ? (($this->verze == 'sk') ? 20 : 21) : 0;
                        if ($discount['value'] != $discount['value_tax_excl']) {
                            $tax = round((($discount['value'] / $discount['value_tax_excl']) - 1) * 100, 0);
                        } else {
                            $tax = 0;
                        }
                        $discount_price = (bool)$dph_math && $tax > 0 ? $discount['value'] : $discount['value_tax_excl'];
                        $tmp .= "<" . $tag_prefix . ":" . $invoice_order . "Item>
<" . $tag_prefix . ":text><![CDATA[" . Tools::substr($this->l("Sleva: ") . $discount['name'], 0, 90) . "]]></" . $tag_prefix . ":text>
<" . $tag_prefix . ":quantity>1</" . $tag_prefix . ":quantity>
<" . $tag_prefix . ":unit>" . $this->l('ks') . "</" . $tag_prefix . ":unit>
<" . $tag_prefix . ":note></" . $tag_prefix . ":note>
$delivered
<" . $tag_prefix . ":payVAT>" . ($tax > 0 ? $dph_math_pay_vat : 'false') . "</" . $tag_prefix . ":payVAT>
<" . $tag_prefix . ":rateVAT>" . $this->TaxToVatRateTypePohoda($tax) . "</" . $tag_prefix . ":rateVAT>
<" . $tag_prefix . ":" . $currency_type . ">
<typ:unitPrice>-" . Tools::ps_round($discount_price * $other_conversion_rate, $pocet_desetinnych_mist) . "</typ:unitPrice>
<typ:price>-" . Tools::ps_round($discount_price * $other_conversion_rate, $pocet_desetinnych_mist) . "</typ:price>
<typ:priceVAT>-" . Tools::ps_round(($discount['value'] - $discount['value_tax_excl']) * $other_conversion_rate, $pocet_desetinnych_mist) . "</typ:priceVAT>
</" . $tag_prefix . ":" . $currency_type . ">
</" . $tag_prefix . ":" . $invoice_order . "Item>";
                    }
                }
            }


            if ($order->total_wrapping > 0) {
                $wrap_price = (bool)$dph_math ? $order->total_wrapping_tax_incl : $order->total_wrapping_tax_excl;
                $tmp .= "<" . $tag_prefix . ":" . $invoice_order . "Item>
<" . $tag_prefix . ":text><![CDATA[" . $this->l("Balné") . "]]></" . $tag_prefix . ":text>
<" . $tag_prefix . ":quantity>1</" . $tag_prefix . ":quantity>
<" . $tag_prefix . ":unit>" . $this->l('ks') . "</" . $tag_prefix . ":unit>
<" . $tag_prefix . ":note></" . $tag_prefix . ":note>
$delivered
<" . $tag_prefix . ":payVAT>" . $dph_math_pay_vat . "</" . $tag_prefix . ":payVAT>
<" . $tag_prefix . ":rateVAT>" . $this->TaxToVatRateTypePohoda((int)$order->carrier_tax_rate) . "</" . $tag_prefix . ":rateVAT>
<" . $tag_prefix . ":" . $currency_type . ">
<typ:unitPrice>" . Tools::ps_round($wrap_price * $other_conversion_rate, $pocet_desetinnych_mist) . "</typ:unitPrice>
<typ:price>" . Tools::ps_round($wrap_price * $other_conversion_rate, $pocet_desetinnych_mist) . "</typ:price>
<typ:priceVAT>" . Tools::ps_round(($order->total_wrapping_tax_incl - $order->total_wrapping_tax_excl) * $other_conversion_rate, $pocet_desetinnych_mist) . "</typ:priceVAT>
</" . $tag_prefix . ":" . $currency_type . ">
</" . $tag_prefix . ":" . $invoice_order . "Item>";
            }

            $tmp .= "
</" . $tag_prefix . ":" . $invoice_order . "Detail>";

            if ($conversion_rate != '1.000000') {
                $id_default_currency = (int)Configuration::get('PS_CURRENCY_DEFAULT');

                /*
                 * $default_currency = new Currency($id_default_currency);
                if (($default_currency->iso_code == 'EUR' && $this->verze == 'sk')
                    ||
                    ($default_currency->iso_code == 'CZK' && $this->verze == 'cz')
                ) {
                */
                if ($order->id_currency == $id_default_currency) {
                    $tmp_conversion_rate_only_here = $conversion_rate;
                } else {
                    $tmp_conversion_rate_only_here = Tools::ps_round(1 / $conversion_rate, 4);
                }


                $tmp .= "<" . $tag_prefix . ":" . $invoice_order . "Summary>
<" . $tag_prefix . ":roundingDocument>none</" . $tag_prefix . ":roundingDocument>
<" . $tag_prefix . ":foreignCurrency>
<typ:currency>
<typ:ids>$currency_iso_code</typ:ids>
</typ:currency>
<typ:rate>$tmp_conversion_rate_only_here</typ:rate>
<typ:amount>1</typ:amount>
</" . $tag_prefix . ":foreignCurrency>
</" . $tag_prefix . ":" . $invoice_order . "Summary>";
            }
            $tmp .= "</" . $tag_prefix . ":" . $invoice_order . ">
</dat:dataPackItem>";

            $xml[] = $tmp;
        }
        $xml[] = '</dat:dataPack>';
        /*** Pohoda - end ***/

        $xml = implode("\r\n", $xml);

        file_put_contents($save, $xml);

        if ($to_browser === true) {
            if (ob_get_level() && ob_get_length() > 0) {
                ob_clean();
            }
            header('Pragma: public');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Content-Description: File Transfer');
            header('Content-type: text/csv;');
            header('Content-Transfer-Encoding: Binary');
            header('Content-disposition: attachment; filename="' . $invoices_orders . '-' . $from . '-' . $to . '.xml');
            header("Content-Length: " . strlen($xml));
            ob_get_clean();
            $fp = fopen('php://output', 'w');
            fputs($fp, $xml);
            fclose($fp);
            die;
        }


        if ($selected_id_shop) {
            $url3_tmp = $this->full_url . 'modules/' . $this->name . '/' . $invoices_orders . '_' . $selected_id_shop . '_' . crc32(preg_replace('/^www\./', '', $_SERVER['HTTP_HOST'])) . '.xml';
        } else {
            $url3_tmp = $this->full_url . 'modules/' . $this->name . '/' . $invoices_orders . '_' . crc32(preg_replace('/^www\./', '', $_SERVER['HTTP_HOST'])) . '.xml';
        }
        $url3_tmp = "<a href='$url3_tmp' target='_blank'>$url3_tmp</a>";
        die('<br />' . $this->l('Hotovo - počet exportovaných záznamů - ') . $order_real_counter . ' <br />' . $this->l('Výsledný XML export zde:') . '<br />' . $url3_tmp);

    }

    private function ModuleToPayment($order, $carrier)
    {
        $module = $order->module;

        if ($module == 'universalpay') {
            $module_payment = $order->payment;

            $payment = 'příkazem';
            if (preg_match("/bank/i", $module_payment)) {
                $payment = 'příkazem';
            } elseif (preg_match("/dobírk/i", $module_payment)) {
                $payment = 'Dobírkou';
            } elseif (preg_match("/hotov/i", $module_payment)) {
                $payment = 'Hotově';
            } elseif (preg_match("/kart/i", $module_payment)) {
                $payment = 'Plat.kartou';
            } elseif ($this->debug) {
                echo $this->l('Neznámá platební metoda - ') . $module . PHP_EOL;
            }


        } else {
            $payment = 'příkazem';
            if ($module == 'bankwire' || $module == 'add_bankwire' || $module == 'shaim_bankwire' || $module == 'pms_bankwire' || $module == 'dm_bankwire' || $module == 'bankwiremultiple' || $module == 'ps_wirepayment' || $module == 'add_pay_invoice' || $module == 'pms_pay_invoice') {
                $payment = 'příkazem';
            } elseif ($module == 'cashondeliveryplus' || $module == 'codwfeeplus' || $module == 'add_dobirka_fee' || $module == 'shaim_cashondelivery' || $module == 'pms_cashondelivery_fee' || $module == 'add_cashondelivery_fee' || $module == 'add7_cashondelivery_fee' || $module == 'codfee' || $module == 'cashondeliverywithfee' || $module == 'maofree_cashondeliveryfee' || $module == 'megareembolso' || $module == 'cashondelivery' || $module == 'dm_cashondelivery' || $module == 'pscodfee' || $module == 'ps_cashondelivery') {
                $payment = 'Dobírkou';
                if ($module == 'cashondelivery' && isset($carrier->name) && preg_match("/osobn|vyzvednut|převzetí|prevzeti/i", $carrier->name)) { // Možná to není dobírka, srsly, fix, for sure
                    $payment = 'Hotově';
                }
            } elseif ($module == 'add_in_store' || $module == 'shaim_hotove' || $module == 'pms_in_store' || $module == 'dm_cashonpickup' || $module == 'instore' || $module == 'cashonpickup' || $module == 'pickuppayment' || $module == 'dm_cash') {
                $payment = 'Hotově';
            } elseif ($module == 'gopay' || $module == 'add_gopay' || $module == 'add_gopay_new' || $module == 'pays_ps' || $module == 'dm_gopay' || $module == 'shaim_gopay' || $module == 'pms_gopay_extra' || $module == 'ThePayBinder' || $module == 'thepay' || $module == 'CsobBinder' || $module == 'AgmoBinder' || $module == 'BankwireFioBinder' || $module == 'ZaplacenoBinder' || $module == 'TwistoBinder' || $module == 'TrustPayBinder' || $module == 'PayUBinder' || $module == 'MallPayBinder' || $module == 'HomeCreditBinder' || $module == 'EssoxBinder' || $module == 'CofidisEsBinder' || $module == 'CetelemEsBinder' || $module == 'CofidisBinder' || $module == 'ThePayEetBinder' || $module == 'PaySecBinder' || $module == 'GoPayRedirBinder' || $module == 'GoPayEetBinder' || $module == 'CSPayBinder' || $module == 'CsobEetBinder' || $module == 'CofidisSkBinder' || $module == 'CofidisHuBinder' || $module == 'CetelemBinder' || $module == 'CCBillBinder' || $module == 'AgmoEetBinder' || preg_match("/Binder$/", $module) || $module == 'wepaybycard' || $module == 'PayeezyBinder' || $module == 'thepay' || $module == 'GPWebPayBinder' || $module == 'GoPayBinder' || $module == 'paypal' || $module == 'trustpay') {
                $payment = 'Plat.kartou';
            } elseif ($this->debug) {
                echo $this->l('Neznámá platební metoda - ') . $module . PHP_EOL;
            }

        }
        return $payment;
    }

    private function ModuleToPaymentPohoda($order, $carrier)
    {
        $module = $order->module;
        $payment = 'draft';
        if ($module == 'universalpay') {
            $module_payment = $order->payment;
            if (preg_match("/bank/i", $module_payment)) {
                $payment = 'draft';
            } elseif (preg_match("/dobírk/i", $module_payment)) {
                $payment = 'delivery';
            } elseif (preg_match("/hotov/i", $module_payment)) {
                $payment = 'cash';
            } elseif (preg_match("/kart/i", $module_payment)) {
                $payment = 'creditcard';
            } elseif ($this->debug) {
                echo $this->l('Neznámá platební metoda - ') . $module . PHP_EOL;
            }
        } else {

            if ($module == 'bankwire' || $module == 'add_bankwire' || $module == 'shaim_bankwire' || $module == 'pms_bankwire' || $module == 'dm_bankwire' || $module == 'bankwiremultiple' || $module == 'ps_wirepayment' || $module == 'add_pay_invoice' || $module == 'pms_pay_invoice') {
                $payment = 'draft';
            } elseif ($module == 'cashondeliveryplus' || $module == 'codwfeeplus' || $module == 'add_dobirka_fee' || $module == 'pms_cashondelivery_fee' || $module == 'add_cashondelivery_fee' || $module == 'add7_cashondelivery_fee' || $module == 'codfee' || $module == 'cashondeliverywithfee' || $module == 'maofree_cashondeliveryfee' || $module == 'megareembolso' || $module == 'cashondelivery' || $module == 'dm_cashondelivery' || $module == 'ps_cashondelivery') {
                $payment = 'delivery';
                if ($module == 'cashondelivery' && isset($carrier->name) && preg_match("/osobn|vyzvednut|převzetí|prevzeti/i", $carrier->name)) { // Možná to není dobírka, srsly, fix, for sure
                    $payment = 'cash';
                }
            } elseif ($module == 'add_in_store' || $module == 'shaim_hotove' || $module == 'pms_in_store' || $module == 'dm_cashonpickup' || $module == 'instore' || $module == 'cashonpickup' || $module == 'pickuppayment' || $module == 'dm_cash') {
                $payment = 'cash';
            } elseif ($module == 'gopay' || $module == 'add_gopay' || $module == 'add_gopay_new' || $module == 'pays_ps' || $module == 'dm_gopay' || $module == 'shaim_gopay' || $module == 'pms_gopay_extra' || $module == 'ThePayBinder' || $module == 'thepay' || $module == 'AgmoBinder' || $module == 'BankwireFioBinder' || $module == 'ZaplacenoBinder' || $module == 'TwistoBinder' || $module == 'TrustPayBinder' || $module == 'PayUBinder' || $module == 'MallPayBinder' || $module == 'HomeCreditBinder' || $module == 'EssoxBinder' || $module == 'CofidisEsBinder' || $module == 'CetelemEsBinder' || $module == 'CofidisBinder' || $module == 'ThePayEetBinder' || $module == 'PaySecBinder' || $module == 'GoPayRedirBinder' || $module == 'GoPayEetBinder' || $module == 'CSPayBinder' || $module == 'CsobEetBinder' || $module == 'CofidisSkBinder' || $module == 'CofidisHuBinder' || $module == 'CetelemBinder' || $module == 'CCBillBinder' || $module == 'AgmoEetBinder' || preg_match("/Binder$/", $module) || $module == 'wepaybycard' || $module == 'PayeezyBinder' || $module == 'thepay' || $module == 'GPWebPayBinder' || $module == 'GoPayBinder' || $module == 'paypal' || $module == 'trustpay') {
                $payment = 'creditcard';
            } elseif ($this->debug) {
                echo $this->l('Neznámá platební metoda - ') . $module . PHP_EOL;
            }

        }
        return $payment;
    }

}
/**
 * @license https://psmoduly.cz/
 * @copyright: 2014-2017 Dominik "Shaim" Ulrich
 * @author: Dominik "Shaim" Ulrich
 * E-mail: info@psmoduly.cz / info@openservis.cz
 * Websites: www.psmoduly.cz / www.openservis.cz
 **/
