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

class shaim_heureka_cz_overeno extends Module
{
    private $_html = '';

    public function __construct()
    {
        $this->name = 'shaim_heureka_cz_overeno';
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
        $this->displayName = $this->l('PSmoduly.cz / OpenServis.cz - Heureka.cz/.sk - ověřeno zákazníky + GDPR checkbox');
        $this->description = $this->l('Děláme nejenom moduly, ale i programování pro Prestashop na míru a také správu e-shopů. Napište nám, pomůžeme Vám s Vašim obchodem na platformě Prestashop.');
        $this->confirmUninstall = $this->l('Opravdu chcete odinstalovat modul ') . $this->displayName . $this->l('?');


        $this->log_name = 'log_' . crc32(preg_replace('/^www\./', '', $_SERVER['HTTP_HOST'])) . '.txt';

        $this->hook_name = 'displayOrderConfirmation';
        $this->hook_name2 = 'displayHeurekaNeSouhlas';
        $this->hook_name3 = 'displayHeader';
        $this->hook_name4 = 'displayAdminOrder';


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
        if (parent::install() == false || $this->registerHook($this->hook_name) == false || $this->registerHook($this->hook_name2) == false || $this->registerHook($this->hook_name3) == false || $this->registerHook($this->hook_name4) == false) {
            return false;
        }
        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $this->registerHook('displayMobileHeader');
        }

        Configuration::updateValue($this->name . '_overeno_code_cz', '');
        Configuration::updateValue($this->name . '_overeno_code_sk', '');

        $is_enabled_shaim_export = (int)Module::isEnabled('shaim_export');
        Configuration::updateValue($this->name . '_overeno_enable_combinations', $is_enabled_shaim_export);

        Configuration::updateValue($this->name . '_overeno_separator', '-');
        Configuration::updateValue($this->name . '_overeno_blacklist', '');
        Configuration::updateValue($this->name . '_overeno_checkbox', 1);


        $exists = DB::getInstance()->ExecuteS("SHOW COLUMNS FROM `" . _DB_PREFIX_ . "cart` LIKE '" . $this->name . "_checkbox';");

        if (!$exists) {
            DB::getInstance()->Execute("ALTER TABLE `" . _DB_PREFIX_ . "cart` ADD `" . $this->name . "_checkbox` tinyint(1) unsigned NOT NULL DEFAULT '0';");
            DB::getInstance()->Execute("ALTER TABLE `" . _DB_PREFIX_ . "cart` ADD `" . $this->name . "_ip` varchar(45) DEFAULT NULL;");
        }

        file_put_contents($this->local_path . $this->log_name, "log\n");
        @file_put_contents(_PS_CACHE_DIR_ . 'shaim_installed.txt', date("Y-m-d H:i:s"));
        $this->Statistics('install');
        return true;
    }

    public function uninstall()
    {
        @unlink(_PS_CACHE_DIR_ . 'shaim_installed.txt');
        $this->Statistics('uninstall');
        if (parent::uninstall() == false || $this->unregisterHook($this->hook_name) == false || $this->unregisterHook($this->hook_name2) == false || $this->unregisterHook($this->hook_name3) == false || $this->unregisterHook($this->hook_name4) == false) {
            return false;
        }
        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $this->unregisterHook('displayMobileHeader');
        }

        Configuration::deleteByName($this->name . '_overeno_code_cz');
        Configuration::deleteByName($this->name . '_overeno_code_sk');
        Configuration::deleteByName($this->name . '_overeno_enable_combinations');
        Configuration::deleteByName($this->name . '_overeno_separator');
        Configuration::deleteByName($this->name . '_overeno_blacklist');
        Configuration::deleteByName($this->name . '_overeno_checkbox');

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
            Configuration::updateValue($this->name . '_overeno_code_cz', trim(Tools::getValue('overeno_code_cz')));
            Configuration::updateValue($this->name . '_overeno_code_sk', trim(Tools::getValue('overeno_code_sk')));
            Configuration::updateValue($this->name . '_overeno_separator', trim(Tools::getValue('overeno_separator')));
            Configuration::updateValue($this->name . '_overeno_blacklist', preg_replace('/[^0-9,]/', '', Tools::getValue('overeno_blacklist')));
            Configuration::updateValue($this->name . '_overeno_enable_combinations', (int)trim(Tools::getValue('overeno_enable_combinations')));
            Configuration::updateValue($this->name . '_overeno_checkbox', (int)trim(Tools::getValue('overeno_checkbox')));
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

        $this->_html .= '<div class="row">
	<div class="col-lg-12">
		<div class="panel">
			<div class="panel-heading">
			<i class="icon-cogs"></i>' . $this->l('Nastavení modulu') . '
			</div>';

        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $theme_directory = DB::getInstance()->ExecuteS("SELECT `theme_name` as directory FROM `" . _DB_PREFIX_ . "shop` as s WHERE s.id_shop = " . (int)$this->context->shop->id . ";");
        } else {
            $theme_directory = DB::getInstance()->ExecuteS("SELECT `directory` FROM `" . _DB_PREFIX_ . "shop` as s
INNER JOIN `" . _DB_PREFIX_ . "theme` as t ON (s.id_theme = t.id_theme)
WHERE s.id_shop = " . (int)$this->context->shop->id . ";");
        }
        $theme_directory = $theme_directory[0]['directory'];


        $overeno_checkbox = (int)Configuration::get($this->name . '_overeno_checkbox');
        if ($overeno_checkbox == 1) {
            if (Module::isEnabled('advancedcheckout')) {
                if (!preg_match("/displayHeurekaNeSouhlas/i", Tools::file_get_contents(_PS_MODULE_DIR_ . 'advancedcheckout/views/templates/front/order.tpl'))) {
                    $this->_html .= '<div class="form-group">';
                    $this->_html .= '<div>' . $this->l('Používáte OPC modul "advancedcheckout" - v tomto modulu je pro plnou funkčnost třeba provést úpravu, viz odkaz:') . '</div>';
                    $this->_html .= '<div><a href="https://psmoduly.cz/content/10-heureka-overeno-zakazniky-gdpr-upravy-checkboxu#advancedcheckout" target="_blank">Úpravy "advancedcheckout"</a></div>';
                    $this->_html .= '<div>' . $this->l('V případě zájmu o úpravu nám napište, úprava je v rámci ceny modulu zdarma. Až bude úprava provedena, tato hláška z modulu zmizí.') . '</div>';
                    $this->_html .= '</div>';
                }
            } elseif (Module::isEnabled('onepagecheckout')) {
                if (!preg_match("/displayHeurekaNeSouhlas/i", Tools::file_get_contents(_PS_MODULE_DIR_ . 'onepagecheckout/views/templates/front/order-carrier-def.tpl'))) {
                    $this->_html .= '<div class="form-group">';
                    $this->_html .= '<div>' . $this->l('Používáte OPC modul "onepagecheckout" - v tomto modulu je pro plnou funkčnost třeba provést úpravu, viz odkaz:') . '</div>';
                    $this->_html .= '<div><a href="https://psmoduly.cz/content/10-heureka-overeno-zakazniky-gdpr-upravy-checkboxu#onepagecheckout" target="_blank">Úpravy "onepagecheckout"</a></div>';
                    $this->_html .= '<div>' . $this->l('V případě zájmu o úpravu nám napište, úprava je v rámci ceny modulu zdarma. Až bude úprava provedena, tato hláška z modulu zmizí.') . '</div>';
                    $this->_html .= '</div>';
                }
            } elseif (Module::isEnabled('supercheckout')) {
                if (!preg_match("/displayHeurekaNeSouhlas/i", Tools::file_get_contents(_PS_MODULE_DIR_ . 'supercheckout/views/templates/front/supercheckout.tpl')) && !preg_match("/displayZboziSouhlas/i", Tools::file_get_contents(_PS_MODULE_DIR_ . 'supercheckout/views/templates/front/cart_summary.tpl'))) {
                    $this->_html .= '<div class="form-group">';
                    $this->_html .= '<div>' . $this->l('Používáte OPC modul "supercheckout" - v tomto modulu je pro plnou funkčnost třeba provést úpravu, viz odkaz:') . '</div>';
                    $this->_html .= '<div><a href="https://psmoduly.cz/content/10-heureka-overeno-zakazniky-gdpr-upravy-checkboxu#supercheckout" target="_blank">Úpravy "supercheckout"</a></div>';
                    $this->_html .= '<div>' . $this->l('V případě zájmu o úpravu nám napište, úprava je v rámci ceny modulu zdarma. Až bude úprava provedena, tato hláška z modulu zmizí.') . '</div>';
                    $this->_html .= '</div>';
                }
            } elseif (Module::isEnabled('onepagecheckoutps')) {
                if (!preg_match("/displayHeurekaNeSouhlas/i", Tools::file_get_contents(_PS_MODULE_DIR_ . 'onepagecheckoutps/views/templates/front/review_footer.tpl')) && !preg_match("/displayHeurekaNeSouhlas/i", Tools::file_get_contents(_PS_MODULE_DIR_ . 'onepagecheckoutps/views/templates/front/review_footer.tpl'))) {
                    $this->_html .= '<div class="form-group">';
                    $this->_html .= '<div>' . $this->l('Používáte OPC modul "onepagecheckoutps" - v tomto modulu je pro plnou funkčnost třeba provést úpravu, viz odkaz:') . '</div>';
                    $this->_html .= '<div><a href="https://psmoduly.cz/content/10-heureka-overeno-zakazniky-gdpr-upravy-checkboxu#onepagecheckoutps" target="_blank">Úpravy "onepagecheckoutps"</a></div>';
                    $this->_html .= '<div>' . $this->l('V případě zájmu o úpravu nám napište, úprava je v rámci ceny modulu zdarma. Až bude úprava provedena, tato hláška z modulu zmizí.') . '</div>';
                    $this->_html .= '</div>';
                }
            } elseif (Module::isEnabled('spstepcheckout')) {
                if (!preg_match("/displayHeurekaNeSouhlas/i", Tools::file_get_contents(_PS_MODULE_DIR_ . 'spstepcheckout/views/templates/front/review_footer.tpl')) && !preg_match("/displayHeurekaNeSouhlas/i", Tools::file_get_contents(_PS_MODULE_DIR_ . 'spstepcheckout/views/templates/front/review_footer.tpl'))) {
                    $this->_html .= '<div class="form-group">';
                    $this->_html .= '<div>' . $this->l('Používáte OPC modul "spstepcheckout" - v tomto modulu je pro plnou funkčnost třeba provést úpravu, viz odkaz:') . '</div>';
                    $this->_html .= '<div><a href="https://psmoduly.cz/content/10-heureka-overeno-zakazniky-gdpr-upravy-checkboxu#spstepcheckout" target="_blank">Úpravy "spstepcheckout"</a></div>';
                    $this->_html .= '<div>' . $this->l('V případě zájmu o úpravu nám napište, úprava je v rámci ceny modulu zdarma. Až bude úprava provedena, tato hláška z modulu zmizí.') . '</div>';
                    $this->_html .= '</div>';
                }
            } elseif (Module::isEnabled('thecheckout')) {
                if (!preg_match("/displayHeurekaNeSouhlas/i", Tools::file_get_contents(_PS_MODULE_DIR_ . 'thecheckout/views/templates/front/blocks/confirm.tpl')) && !preg_match("/displayHeurekaNeSouhlas/i", Tools::file_get_contents(_PS_MODULE_DIR_ . 'thecheckout/views/templates/front/blocks/confirm.tpl'))) {
                    $this->_html .= '<div class="form-group">';
                    $this->_html .= '<div>' . $this->l('Používáte OPC modul "thecheckout" - v tomto modulu je pro plnou funkčnost třeba provést úpravu, viz odkaz:') . '</div>';
                    $this->_html .= '<div><a href="https://psmoduly.cz/content/10-heureka-overeno-zakazniky-gdpr-upravy-checkboxu#thecheckout" target="_blank">Úpravy "thecheckout"</a></div>';
                    $this->_html .= '<div>' . $this->l('V případě zájmu o úpravu nám napište, úprava je v rámci ceny modulu zdarma. Až bude úprava provedena, tato hláška z modulu zmizí.') . '</div>';
                    $this->_html .= '</div>';
                }
            } elseif (Module::isEnabled('prestakosik')) {
                if (!preg_match("/displayHeurekaNeSouhlas/i", Tools::file_get_contents(_PS_MODULE_DIR_ . 'prestakosik/views/templates/front/checkout/steps/delivery-payment.tpl'))) {
                    $this->_html .= '<div class="form-group">';
                    $this->_html .= '<div>' . $this->l('Používáte OPC modul "prestakosik" - v tomto modulu je pro plnou funkčnost třeba provést úpravu, viz odkaz:') . '</div>';
                    $this->_html .= '<div><a href="https://psmoduly.cz/content/10-heureka-overeno-zakazniky-gdpr-upravy-checkboxu#prestakosik" target="_blank">Úpravy "prestakosik"</a></div>';
                    $this->_html .= '<div>' . $this->l('V případě zájmu o úpravu nám napište, úprava je v rámci ceny modulu zdarma. Až bude úprava provedena, tato hláška z modulu zmizí.') . '</div>';
                    $this->_html .= '</div>';
                }
            }elseif (version_compare(_PS_VERSION_, '1.7', '>=')) {
                if (!preg_match("/displayHeurekaNeSouhlas/i", Tools::file_get_contents(_PS_ALL_THEMES_DIR_ . $theme_directory . '/templates/checkout/_partials/steps/payment.tpl'))) {
                    $this->_html .= '<div class="form-group">';
                    $this->_html .= '<div>' . $this->l('Pro plnou funkčnost checkboxu je třeba provést úpravu (pokud budete využívat GDPR checkbox), viz odkaz:') . '</div>';
                    $this->_html .= '<div><a href="https://psmoduly.cz/content/10-heureka-overeno-zakazniky-gdpr-upravy-checkboxu#ps17" target="_blank">Úpravy Prestashop 1.7</a></div>';
                    $this->_html .= '<div>' . $this->l('V případě zájmu o úpravu nám napište, úprava je v rámci ceny modulu zdarma. Až bude úprava provedena, tato hláška z modulu zmizí.') . '</div>';
                    $this->_html .= '</div>';
                }
            } elseif (version_compare(_PS_VERSION_, '1.6', '>=')) {
                if (!preg_match("/displayHeurekaNeSouhlas/i", Tools::file_get_contents(_PS_ALL_THEMES_DIR_ . $theme_directory . '/order-carrier.tpl'))) {
                    $this->_html .= '<div class="form-group">';
                    $this->_html .= '<div>' . $this->l('Pro plnou funkčnost checkboxu je třeba provést úpravu (pokud budete využívat GDPR checkbox), viz odkaz:') . '</div>';
                    $this->_html .= '<div><a href="https://psmoduly.cz/content/10-heureka-overeno-zakazniky-gdpr-upravy-checkboxu#ps16" target="_blank">Úpravy Prestashop 1.6</a></div>';
                    $this->_html .= '<div>' . $this->l('V případě zájmu o úpravu nám napište, úprava je v rámci ceny modulu zdarma. Až bude úprava provedena, tato hláška z modulu zmizí.') . '</div>';
                    $this->_html .= '</div>';
                }
            } else {
                if (!preg_match("/displayHeurekaNeSouhlas/i", Tools::file_get_contents(_PS_ALL_THEMES_DIR_ . $theme_directory . '/order-carrier.tpl'))) {
                    $this->_html .= '<div class="form-group">';
                    $this->_html .= '<div>' . $this->l('Pro plnou funkčnost checkboxu je třeba provést úpravu (pokud budete využívat GDPR checkbox), viz odkaz:') . '</div>';
                    $this->_html .= '<div><a href="https://psmoduly.cz/content/10-heureka-overeno-zakazniky-gdpr-upravy-checkboxu#ps15" target="_blank">Úpravy Prestashop 1.5</a></div>';
                    $this->_html .= '<div>' . $this->l('V případě zájmu o úpravu nám napište, úprava je v rámci ceny modulu zdarma. Až bude úprava provedena, tato hláška z modulu zmizí.') . '</div>';
                    $this->_html .= '</div>';
                }
            }
        }

        $this->_html .= '<div class="well">';
        $this->_html .= '<b>' . $this->l('Tajný klíč pro Ověřeno zákazníky je k nalezení zde:') . '</b> <a href="https://sluzby.heureka.cz/sluzby/certifikat-spokojenosti/" target="_blank">https://sluzby.heureka.cz/sluzby/certifikat-spokojenosti/</a><br /><br /><b>' . $this->l('Příklad kódu') . ':</b> 123e7003114c48g325b124a5d1c645ef<br /><br />';
        $this->_html .= $this->l('Pokud používáte pouze CZ verzi heureky, vyplňte pouze tajný klíč pro CZ verzi. Pokud využíváte i SK verzi heureky, vyplňte i tajný klíč pro SK verzi.') . '<br /><br />';
        $this->_html .= $this->l('Pokud by nastal nějaký problém, veškerá komunikace s Heurekou je zaznamenávána do tohoto souboru -> ') . '<a href="' . $this->full_url . 'modules/' . $this->name . '/' . $this->log_name . '" target="_blank">' . $this->log_name . '</a>';
        $this->_html .= '</div>';
        $this->_html .= '<form action="' . $_SERVER['REQUEST_URI'] . '" method="post">';

        $this->_html .= '<div class="form-group">';
        $this->_html .= '<label class="control-label col-lg-3">' . $this->l('CZ verze - Ověřeno zákazníky - Tajný klíč') . '</label>';
        $this->_html .= '<div class="col-lg-9"><input type="text" name="overeno_code_cz" size="30" value="' . Configuration::get($this->name . '_overeno_code_cz') . '"></div>';
        $this->_html .= '</div>';

        $this->_html .= '<div class="form-group">';
        $this->_html .= '<label class="control-label col-lg-3">' . $this->l('SK verze - Ověřeno zákazníky - Tajný klíč') . '</label>';
        $this->_html .= '<div class="col-lg-9"><input type="text" name="overeno_code_sk" size="30" value="' . Configuration::get($this->name . '_overeno_code_sk') . '"></div>';
        $this->_html .= '</div>';

        $this->_html .= '<div class="form-group">';
        $overeno_enable_combinations = (int)Configuration::get($this->name . '_overeno_enable_combinations');
        $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Povolit ID kombinací') . '</label>';
        $this->_html .= '<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
										<input name="overeno_enable_combinations" id="overeno_enable_combinations_on" value="1"' . (($overeno_enable_combinations == 1) ? ' checked="checked"' : '') . ' type="radio">
										<label for="overeno_enable_combinations_on">' . $this->l('ANO') . '</label>
										<input name="overeno_enable_combinations" id="overeno_enable_combinations_off" value="0"' . (($overeno_enable_combinations == 0) ? ' checked="checked"' : '') . ' type="radio">
										<label for="overeno_enable_combinations_off">' . $this->l('NE') . '</label>
										<a class="slide-button btn"></a>
									</span></div>';
        $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
					' . $this->l('Pokud toto povolíte, bude se uvádět jak ID produktu, tak ID kombinace. Pokud toto vypnete, bude se uvádět pouze ID produktu.') . '
					</div></div>';
        $this->_html .= '</div><div class="clear_both"></div>';


        $this->_html .= '<div class="form-group">';
        $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Oddělovač ID produktu a ID varianty') . '</label>';
        $this->_html .= '<div class="col-lg-9"><input type="text" name="overeno_separator" size="30" value="' . Configuration::get($this->name . '_overeno_separator') . '"></div>';
        $this->_html .= '</div><div class="clear_both"></div>';

        if (preg_match("/prusa3d/", $_SERVER['HTTP_HOST'])) {
            $this->_html .= '<div class="form-group">';
            $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Blacklist ID produktů (oddělovat čárkou)') . '</label>';
            $this->_html .= '<div class="col-lg-9"><input type="text" name="overeno_blacklist" size="30" value="' . Configuration::get($this->name . '_overeno_blacklist') . '"></div>';
            $this->_html .= '</div>';
        }

        $this->_html .= '<div class="form-group">';
        $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Povolit zobrazení checkboxu GDPR v objednávce') . '</label>';
        $this->_html .= '<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
										<input name="overeno_checkbox" id="overeno_checkbox_on" value="1"' . (($overeno_checkbox == 1) ? ' checked="checked"' : '') . ' type="radio">
										<label for="overeno_checkbox_on">' . $this->l('ANO') . '</label>
										<input name="overeno_checkbox" id="overeno_checkbox_off" value="0"' . (($overeno_checkbox == 0) ? ' checked="checked"' : '') . ' type="radio">
										<label for="overeno_checkbox_off">' . $this->l('NE') . '</label>
										<a class="slide-button btn"></a>
									</span></div>';
        $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
					' . $this->l('Tato funkce určujte zobrazení checkboxu v objednávce (možnost nesouhlasit se zasíláním dotazníku) v souvislosti s GDPR. Platné od 25. 5. 2018. Viz: ') . '<a href="https://blog.heureka.cz/2018/04/06/heureka-a-gdpr/" target="_blank">https://blog.heureka.cz/2018/04/06/heureka-a-gdpr/</a>
					</div></div>';

        $this->_html .= '<br /><br /><br /><br /><br /><div class="clear_both"><small>' . $this->credits . '<br />' . $this->description . '</small></div>';
        $this->_html .= '<div class="panel-footer">
				<button type="submit" class="btn btn-default pull-right" name="submit_text">
					<i class="process-icon-save"></i>' . $this->l('Uložit') . '
				</button>
			</div></form>
		</div>
	</div>	</div>
</div>';
        return $this->_html;

    }

    public function hookdisplayOrderConfirmation($params)
    {


        if ($this->active != 1) {
            return;
        }
        $id_order = (int)Tools::getValue('id_order');
        if (!$id_order) {
            return false;
        }


        /** Heureka - ověřeno zákazníky **/


        if ((version_compare(_PS_VERSION_, '1.5', '>=') ? Tools::file_exists_cache($this->local_path . 'HeurekaOvereno.php') : file_exists($this->local_path . 'HeurekaOvereno.php'))) {
            $overeno_checkbox = (int)Configuration::get($this->name . '_overeno_checkbox');
            if (!class_exists('HeurekaOvereno')) {
                require_once $this->local_path . 'HeurekaOvereno.php';
            }

            $order = new Order($id_order);
            $checkbox = DB::getInstance()->ExecuteS("SELECT `" . $this->name . "_checkbox` FROM `" . _DB_PREFIX_ . "cart` WHERE id_cart = " . (int)$order->id_cart . ";");
            if (($overeno_checkbox == 1 && $checkbox[0][$this->name . "_checkbox"] == 0) || ($overeno_checkbox == 0)) {

                $address = new Address($order->id_address_invoice);

                if ($address->id_country != 16 && $address->id_country != 37) {
                    // Nechceme nikoho mimo CZ a SK
                    return "
                <!-- Heureka Overeno - neni CZ ani SK objednavka - preskakuji (www.psmoduly.cz / www.openservis.cz) -->";
                }
                $overeno_code_cz = Configuration::get($this->name . '_overeno_code_cz');
                $overeno_code_sk = Configuration::get($this->name . '_overeno_code_sk');


                // Default
                $lang_id = 1;
                $overeno_lang = 'cz';
                $overeno_code = '';

                if (empty($overeno_code_cz) && !empty($overeno_code_sk)) { // Pokud mame zadane pouze SK
                    $overeno_code = $overeno_code_sk;
                    $lang_id = 2;
                    $overeno_lang = 'sk';
                } elseif (empty($overeno_code_sk) && !empty($overeno_code_cz)) { // Pokud mame zadane pouze CZ
                    $overeno_code = $overeno_code_cz;
                    $lang_id = 1;
                    $overeno_lang = 'cz';
                }

                // Pokud mame zadane oboje
                if ($overeno_code_sk && $address->id_country == 37) {
                    $lang_id = 2;
                    $overeno_lang = 'sk';
                    $overeno_code = $overeno_code_sk;
                } elseif ($overeno_code_cz && $address->id_country == 16) {
                    $lang_id = 1;
                    $overeno_lang = 'cz';
                    $overeno_code = $overeno_code_cz;
                }

                if (!empty($overeno_code)) {

                    $heureka = new HeurekaOvereno($overeno_code, $lang_id, $this->local_path . $this->log_name);
                    if ($heureka->return === true) {

                        $overeno_enable_combinations = (int)Configuration::get($this->name . '_overeno_enable_combinations');
                        $overeno_separator = Configuration::get($this->name . '_overeno_separator');
                        $overeno_blacklist = explode(',', Configuration::get($this->name . '_overeno_blacklist'));

                        if (!empty($order->id_customer)) {
                            $customer = new Customer($order->id_customer);
                            $email = $customer->email;
                        } elseif (isset($params['cookie']->email) && !empty($params['cookie']->email)) {
                            // Toto by asi nemělo nastat, ale nejsem si jistý, tak to raději nechávám.
                            $email = $params['cookie']->email;
                        } else {
                            return false;
                        }
                        if (!Validate::isEmail($email)) {
                            return false;
                        }
                        $heureka->setEmail($email);
                        $heureka->addOrderId($id_order);

                        $products = $order->getProducts();
                        foreach ($products as $product) {
                            $product_id = $product['product_id'];
                            if (in_array($product_id, $overeno_blacklist)) {
                                return "
                <!-- Heureka.$overeno_lang - ověřeno zákazníky - BL (www.psmoduly.cz / www.openservis.cz) -->";
                            }

                            if ($overeno_enable_combinations && $product['product_attribute_id'] > 0) {
                                $product_id .= $overeno_separator . $product['product_attribute_id'];
                            }

                            $heureka->addProductItemId($product_id);

                        }

                        $heureka->send();

                        // Loguje se primo v tride heureky
                        return "
                <!-- Heureka.$overeno_lang - ověřeno zákazníky - odesláno (www.psmoduly.cz / www.openservis.cz) -->";
                    } else {
                        file_put_contents($this->local_path . $this->log_name, '[' . date("Y-m-d H:i:s") . '] ' . 'spatny api klic - ' . $overeno_code . ' - ' . $overeno_lang . PHP_EOL, FILE_APPEND);
                        return "
                <!-- Heureka.$overeno_lang - ověřeno zákazníky - špatný api klíč (" . $overeno_code . " - " . $overeno_lang . ") (www.psmoduly.cz / www.openservis.cz) -->";
                    }
                }
            } elseif ($overeno_checkbox == 1 && $checkbox[0][$this->name . "_checkbox"] == 1) {
                file_put_contents($this->local_path . $this->log_name, '[' . date("Y-m-d H:i:s") . '] ' . $id_order . ' - NEsouhlas' . PHP_EOL, FILE_APPEND);
            }
        }

    }


    public function hookdisplayHeader($params)
    {

        if (Configuration::get($this->name . '_overeno_checkbox') == 1 && (Configuration::get($this->name . '_overeno_code_cz') || Configuration::get($this->name . '_overeno_code_sk'))) {
            if ((isset($this->context->controller->php_self) && $this->context->controller->php_self == 'order') || // 1.6 vicekrokova obj
                (isset($this->context->controller->php_self) && ($this->context->controller->php_self == 'order-opc' || $this->context->controller->php_self == 'orderopc')) || // Toto je treba u "onepagecheckout" OPC, PS 1.6
                (isset($this->context->controller->php_self) && $this->context->controller->php_self == 'adv_order') || // Toto je treba u "advancedcheckout" OPC, PS 1.6
                (isset($this->context->controller->page_name) && $this->context->controller->page_name == 'module-supercheckout-supercheckout') || // Toto je treba u "supercheckout" OPC, PS 1.6
                (isset($this->context->controller->page_name) && $this->context->controller->page_name == 'module-onepagecheckoutps-main') || // Toto je treba u "onepagecheckoutps" OPC, PS 1.5
                (isset($this->context->controller->page_name) && $this->context->controller->page_name == 'module-thecheckout-order') ||// Toto je treba u "thecheckout" OPC, PS 1.7
                (isset($this->context->controller->page_name) && $this->context->controller->page_name == 'module-steasycheckout-default')|| // Toto je treba u "steasycheckout" OPC, PS 1.7
                (isset($this->context->controller->page_name) && $this->context->controller->page_name == 'module-prestakosik-order') // Toto je treba u "prestakosik" OPC, PS 1.7
            ) {
                if (version_compare(_PS_VERSION_, '1.7', '>=')) {
                    $this->context->controller->registerJavascript('modules-' . $this->name, 'modules/' . $this->name . '/' . $this->name . '.js', ['position' => 'bottom', 'priority' => 50]);
                } else {
                    $this->context->controller->addJS($this->_path . $this->name . '.js');
                }
                if ((isset($this->context->controller->php_self) && ($this->context->controller->php_self == 'order' || $this->context->controller->php_self == 'order-opc' || $this->context->controller->php_self == 'orderopc'))
                    ||
                    (isset($this->context->controller->page_name) && ($this->context->controller->page_name == 'module-onepagecheckoutps-main' || $this->context->controller->page_name == 'module-thecheckout-order' || $this->context->controller->page_name == 'module-supercheckout-supercheckout'))
                ) {
                    if (version_compare(_PS_VERSION_, '1.7', '>=')) {
                        $this->context->controller->registerStylesheet('modules-' . $this->name, 'modules/' . $this->name . '/' . $this->name . '.css', ['media' => 'all', 'priority' => 50]);
                    } else {
                        $this->context->controller->addCSS($this->_path . $this->name . '.css', 'all');
                    }

                }
            }
        }
    }

    public function hookdisplayAdminOrder($params)
    {


        if ($this->active != 1) {
            return;
        }

        if (Configuration::get($this->name . '_overeno_code_cz') || Configuration::get($this->name . '_overeno_code_sk')) {
            $id_order = (isset($params['id_order']) && !empty($params['id_order']) ? (int)$params['id_order'] : (int)$_GET['id_order']);
            $order = new Order($id_order);
            if (Validate::isLoadedObject($order)) {
                $checkbox = DB::getInstance()->ExecuteS("SELECT `" . $this->name . "_checkbox`, `" . $this->name . "_ip`  FROM `" . _DB_PREFIX_ . "cart` WHERE id_cart = " . $order->id_cart . ";");

                $html = '<div id="souhlasy_' . $this->name . '" style="clear: both"; class="panel">';
                $html .= '<div id="' . $this->name . '_checkbox">
					' . $this->l('Heureka ověřeno zákazníky - souhlas - ');
                if (isset($checkbox[0][$this->name."_checkbox"]) && $checkbox[0][$this->name."_checkbox"] == 1) {
                    $html .= $this->l('ANO - ') . $checkbox[0][$this->name."_ip"];
                } else {
                    $html .= $this->l('NE');
                }
                $html .= '</div>';
                $html .= '</div>';
                return $html;
            }
        }
    }

    public function hookdisplayMobileHeader($params)
    {
        return $this->hookdisplayHeader($params);
    }


    public function hookdisplayHeurekaNeSouhlas($params)
    {

        if ($this->active != 1) {
            return;

        }


        if (Configuration::get($this->name . '_overeno_checkbox') == 1 && (Configuration::get($this->name . '_overeno_code_cz') || Configuration::get($this->name . '_overeno_code_sk'))) {

            $checkbox = DB::getInstance()->ExecuteS("SELECT `" . $this->name . "_checkbox` FROM `" . _DB_PREFIX_ . "cart` WHERE id_cart = " . $params['cart']->id . ";");


            if (version_compare(_PS_VERSION_, '1.7', '>=')) {
                $version = '1.7';
            } elseif (version_compare(_PS_VERSION_, '1.6', '>=')) {
                $version = '1.6';
            } else {
                $version = '1.5';
            }

            $this->context->smarty->assign(array(
                $this->name . '_checkbox' => (isset($checkbox[0][$this->name . "_checkbox"]) ? (int)$checkbox[0][$this->name . "_checkbox"] : 0),
                $this->name . '_ajax_url' => $this->full_url . 'modules/' . $this->name . '/' . $this->name . '_ajax.php',
                $this->name . '_id_cart' => (int)$params['cart']->id,
                $this->name . '_ps_version' => $version,
            ));

            if (Module::isEnabled('onepagecheckout')) {
                if (version_compare(_PS_VERSION_, "1.6", "<")) {
                    return $this->display(__FILE__, $this->name . '_checkbox_onepagecheckout_15.tpl');
                } else {
                    return $this->display(__FILE__, $this->name . '_checkbox_onepagecheckout.tpl');

                }
            } elseif (Module::isEnabled('advancedcheckout')) {
                return $this->display(__FILE__, $this->name . '_checkbox_advancedcheckout.tpl');
            } elseif (Module::isEnabled('supercheckout')) {
                if (version_compare(_PS_VERSION_, "1.7", "<")) {
                    return $this->display(__FILE__, $this->name . '_checkbox_supercheckout.tpl');
                } else {
                    return $this->display(__FILE__, $this->name . '_checkbox_supercheckout_17.tpl');
                }
            } elseif (Module::isEnabled('onepagecheckoutps')) {
                return $this->display(__FILE__, $this->name . '_checkbox_onepagechecktoups.tpl');
            } elseif (Module::isEnabled('spstepcheckout')) {
                return $this->display(__FILE__, $this->name . '_checkbox_spstepcheckout.tpl');
            } elseif (Module::isEnabled('thecheckout')) {
                return $this->display(__FILE__, $this->name . '_checkbox_thecheckout.tpl');
            } elseif (Module::isEnabled('prestakosik')) {
                return $this->display(__FILE__, $this->name . '_checkbox_prestakosik.tpl');
            } elseif (Module::isEnabled('steasycheckout')) {
                return $this->display(__FILE__, $this->name . '_checkbox_steasycheckout.tpl');
            } elseif (version_compare(_PS_VERSION_, "1.7", ">=")) {
                return $this->display(__FILE__, $this->name . '_checkbox_17.tpl');
            } elseif (version_compare(_PS_VERSION_, "1.6", ">=")) {
                return $this->display(__FILE__, $this->name . '_checkbox.tpl');
            } else {
                return $this->display(__FILE__, $this->name . '_checkbox_15.tpl');
            }
        }
    }


}
/**
 * @license https://psmoduly.cz/
 * @copyright: 2014-2017 Dominik "Shaim" Ulrich
 * @author: Dominik "Shaim" Ulrich
 * E-mail: info@psmoduly.cz / info@openservis.cz
 * Websites: www.psmoduly.cz / www.openservis.cz
 **/
