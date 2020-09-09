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

class shaim_export extends Module
{
    private $_html = '';

    private $local = array();
    private $tmp_expand_heureka = array('cs' => array(), 'sk' => array(),);
    private $tmp_expand_glami = array('cs' => array(), 'sk' => array(),);
    private $h = array();
    private $glami = array();

    public function __construct()
    {
        $this->name = 'shaim_export';
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
        $this->export_file = 'export6.php';
        $this->export_folder = 'xml';
        if ($this->name == 'shaim_glami') {
            $this->displayName = $this->l('PSmoduly.cz / OpenServis.cz - XML export - Glami.cz/.sk');
        } elseif ($this->name == 'shaim_pricemania') {
            $this->displayName = $this->l('PSmoduly.cz / OpenServis.cz - XML export - Pricemania.cz/.sk');
        } elseif ($this->name == 'shaim_dostupnost') {
            $this->displayName = $this->l('PSmoduly.cz / OpenServis.cz - XML export - Heureka dostupnostní XML');
        } else {
            $this->displayName = $this->l('PSmoduly.cz / OpenServis.cz - XML export - Zboží/Heureka/Google/Facebook');
        }
        $this->real_name = 'all';
        $this->description = $this->l('Děláme nejenom moduly, ale i programování pro Prestashop na míru a také správu e-shopů. Napište nám, pomůžeme Vám s Vašim obchodem na platformě Prestashop.');
        $this->confirmUninstall = $this->l('Opravdu chcete odinstalovat modul ') . $this->displayName . $this->l('?');

        if (Configuration::get('PS_DISABLE_OVERRIDES') == 1 && $this->name == 'shaim_export') { // override upozornění
            $this->warning .= $this->l('Tento modul vyžaduje ke správné funkčnosti povolené "přepsání" (tzv. overrides), které máte aktuálně zakázané. Povolit override můžete v Nástroje -> Výkon.');
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
        Configuration::updateValue($this->name . '_heureka_pair', 0);
        Configuration::updateValue($this->name . '_google_pair', 0);
        Configuration::updateValue($this->name . '_zbozi_pair', 0);
        Configuration::updateValue($this->name . '_desc', 1);
        Configuration::updateValue($this->name . '_combinations', 1);
        Configuration::updateValue($this->name . '_only_stock', 0);
        // Configuration::updateValue($this->name . '_odber_zdarma', 0);
        Configuration::updateValue($this->name . '_better_pair_manufacturer', 0);
        Configuration::updateValue($this->name . '_better_pair_code', 0);
        Configuration::updateValue($this->name . '_utm', 0);
        Configuration::updateValue($this->name . '_multistore', 0);
        Configuration::updateValue($this->name . '_heureka_cpc', 0);
        Configuration::updateValue($this->name . '_gift', '');
        Configuration::updateValue($this->name . '_gift_price', 0);
        Configuration::updateValue($this->name . '_max_cpc_limit', 0);
        Configuration::updateValue($this->name . '_depot_ids_zbozi', '');
        Configuration::updateValue($this->name . '_depot_ids_heureka', '');
        Configuration::updateValue($this->name . '_max_cpc', 1);
        Configuration::updateValue($this->name . '_max_cpc_search', 1);
        Configuration::updateValue($this->name . '_shipping_price', 0);
        Configuration::updateValue($this->name . '_shipping_price_cod', 0);
        Configuration::updateValue($this->name . '_active_category', 1);
        Configuration::updateValue($this->name . '_token', str_replace('.', '_', preg_replace('/^www./', '', $_SERVER['HTTP_HOST'])));
        Configuration::updateValue($this->name . '_dost_day', 2);
        Configuration::updateValue($this->name . '_dost_time', '12:00');
        Configuration::updateValue($this->name . '_pick_day', 2);
        Configuration::updateValue($this->name . '_pick_time', '14:00');
        Configuration::updateValue($this->name . '_blacklist_product', '');
        Configuration::updateValue($this->name . '_days_stock', 0);
        Configuration::updateValue($this->name . '_days_nostock', 0);
        Configuration::updateValue($this->name . '_real_name', $this->real_name);

        $sluzby = array(
            'active_zbozi_cz' => ((preg_match("/\.sk$/", $_SERVER['HTTP_HOST']) || $this->name == 'shaim_dostupnost') ? 0 : 1),
            'active_heureka_cz' => ((preg_match("/\.sk$/", $_SERVER['HTTP_HOST']) || $this->name == 'shaim_dostupnost') ? 0 : 1),
            'active_heureka_sk' => ((preg_match("/\.sk$/", $_SERVER['HTTP_HOST']) && $this->name != 'shaim_dostupnost') ? 1 : 0),
            'active_heureka_dostupnost' => (($this->name == 'shaim_dostupnost') ? 1 : 0),
            'active_google_com' => 1,
            'active_facebook_com' => 1,
        );

        Configuration::updateValue($this->name . '_aktivni_sluzby', serialize($sluzby));


        if (parent::install() == false) {
            return false;
        }
        if (!Tools::file_exists_cache(_PS_ROOT_DIR_ . '/' . $this->export_folder . "/")) {
            @mkdir(_PS_ROOT_DIR_ . '/' . $this->export_folder . "/");
        }

        if ($this->name == 'shaim_glami') {
            Db::getInstance()->Execute("CREATE TABLE IF NOT EXISTS " . _DB_PREFIX_ . "shaim_glami (local_id int(10) NOT NULL, glami_category_name varchar(255) NOT NULL,
            export tinyint(1) DEFAULT 1, `lang` enum('cz','sk') DEFAULT 'cz', UNIQUE KEY local_id_export_lang (local_id, export, lang)) COLLATE 'utf8_general_ci';");
        } elseif ($this->name == 'shaim_pricemania') {
            Db::getInstance()->Execute("CREATE TABLE IF NOT EXISTS " . _DB_PREFIX_ . "shaim_pricemania (local_id int(10) NOT NULL, pricemania_category_name varchar(255) NOT NULL,
            export tinyint(1) DEFAULT 1, `lang` enum('cz','sk') DEFAULT 'cz', UNIQUE KEY local_id_export_lang (local_id, export, lang)) COLLATE 'utf8_general_ci';");
        } elseif ($this->name == 'shaim_export') {
            Db::getInstance()->Execute("CREATE TABLE IF NOT EXISTS " . _DB_PREFIX_ . "shaim_heureka (local_id int(10) NOT NULL,
        heureka_full_name varchar(255) CHARACTER SET utf8 NOT NULL, lang enum('cz','sk') CHARACTER SET utf8 NOT NULL DEFAULT 'cz', UNIQUE KEY local_id_lang_heureka_full_name (local_id, heureka_full_name, lang)) COLLATE 'utf8_general_ci';");
            Db::getInstance()->Execute("CREATE TABLE IF NOT EXISTS " . _DB_PREFIX_ . "shaim_google (local_id int(10) NOT NULL,
        google_full_name varchar(255) CHARACTER SET utf8 NOT NULL, UNIQUE KEY local_id_google_full_name (local_id, google_full_name)) COLLATE 'utf8_general_ci';");

            Db::getInstance()->Execute("CREATE TABLE IF NOT EXISTS " . _DB_PREFIX_ . "shaim_zbozi (local_id int(10) NOT NULL,
        zbozi_full_name varchar(255) CHARACTER SET utf8 NOT NULL, UNIQUE KEY local_id_zbozi_full_name (local_id, zbozi_full_name)) COLLATE 'utf8_general_ci';");
        }

        if ($this->name == 'shaim_export') {
            $this->registerhook('displayAdminProductsExtra');
            Db::getInstance()->Execute("ALTER TABLE `" . _DB_PREFIX_ . "product` ADD `shaim_export_name` varchar(128);");
            Db::getInstance()->Execute("ALTER TABLE `" . _DB_PREFIX_ . "product` ADD `shaim_export_gifts` varchar(250);");
            Db::getInstance()->Execute("ALTER TABLE `" . _DB_PREFIX_ . "product` ADD `shaim_export_active` tinyint(1) NOT NULL DEFAULT '1';");
            Db::getInstance()->Execute("ALTER TABLE `" . _DB_PREFIX_ . "product_shop` ADD `shaim_export_name` varchar(128);");
            Db::getInstance()->Execute("ALTER TABLE `" . _DB_PREFIX_ . "product_shop` ADD `shaim_export_gifts` varchar(250);");
            Db::getInstance()->Execute("ALTER TABLE `" . _DB_PREFIX_ . "product_shop` ADD `shaim_export_active` tinyint(1) NOT NULL DEFAULT '1';");
        }
        @file_put_contents(_PS_CACHE_DIR_ . 'shaim_installed.txt', date("Y-m-d H:i:s"));
        $this->Statistics('install');
        return true;
    }

    public function uninstall()
    {
        Configuration::deleteByName($this->name . '_heureka_pair');
        Configuration::deleteByName($this->name . '_google_pair');
        Configuration::deleteByName($this->name . '_desc');
        Configuration::deleteByName($this->name . '_combinations');
        Configuration::deleteByName($this->name . '_only_stock');
        // Configuration::deleteByName($this->name . '_odber_zdarma');
        Configuration::deleteByName($this->name . '_better_pair_manufacturer');
        Configuration::deleteByName($this->name . '_better_pair_code');
        Configuration::deleteByName($this->name . '_utm');
        Configuration::deleteByName($this->name . '_multistore');
        Configuration::deleteByName($this->name . '_heureka_cpc');
        Configuration::deleteByName($this->name . '_gift');
        Configuration::deleteByName($this->name . '_gift_price');
        Configuration::deleteByName($this->name . '_max_cpc_limit');
        Configuration::deleteByName($this->name . '_depot_ids_zbozi');
        Configuration::deleteByName($this->name . '_depot_ids_heureka');
        Configuration::deleteByName($this->name . '_max_cpc');
        Configuration::deleteByName($this->name . '_max_cpc_search');
        Configuration::deleteByName($this->name . '_shipping_price');
        Configuration::deleteByName($this->name . '_shipping_price_cod');
        Configuration::deleteByName($this->name . '_active_category');
        Configuration::deleteByName($this->name . '_token');
        Configuration::deleteByName($this->name . '_dost_day');
        Configuration::deleteByName($this->name . '_dost_time');
        Configuration::deleteByName($this->name . '_pick_day');
        Configuration::deleteByName($this->name . '_pick_time');
        Configuration::deleteByName($this->name . '_blacklist_product');
        Configuration::deleteByName($this->name . '_days_stock');
        Configuration::deleteByName($this->name . '_days_nostock');
        Configuration::deleteByName($this->name . '_real_name');

        if ($this->name == 'shaim_glami') {
            Db::getInstance()->Execute("DROP TABLE IF EXISTS " . _DB_PREFIX_ . "shaim_glami;");
        } elseif ($this->name == 'shaim_pricemania') {
            Db::getInstance()->Execute("DROP TABLE IF EXISTS " . _DB_PREFIX_ . "shaim_pricemania;");
        } elseif ($this->name == 'shaim_export') {
            Db::getInstance()->Execute("DROP TABLE IF EXISTS " . _DB_PREFIX_ . "shaim_heureka;");
            Db::getInstance()->Execute("DROP TABLE IF EXISTS " . _DB_PREFIX_ . "shaim_google;");
            Db::getInstance()->Execute("DROP TABLE IF EXISTS " . _DB_PREFIX_ . "shaim_zbozi;");
        }
        if (parent::uninstall() == false) {
            return false;
        }
        if (Tools::file_exists_cache(_PS_ROOT_DIR_ . '/' . $this->export_folder . "/")) {
            @rmdir(_PS_ROOT_DIR_ . '/' . $this->export_folder . "/");
        }
        if ($this->name == 'shaim_export') {
            $this->unregisterhook('displayAdminProductsExtra');
        }
        @unlink(_PS_CACHE_DIR_ . 'shaim_installed.txt');
        $this->Statistics('uninstall');
        return true;
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
        /*
        if (preg_match('/^s-[0-9]$/', $this->context->cookie->shopContext)) {
            $this->selected_id_shop = (int)$this->context->shop->id;
        } else {
            $this->selected_id_shop = false;
        }
        */


        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            if (Tools::isSubmit('glami_submit_text') || Tools::isSubmit('pricemania_submit_text') || Tools::isSubmit('submit_text')) {
                Configuration::updateValue($this->name . '_blacklist_product', preg_replace('/,+/', ',', preg_replace("/[^0-9,]/", "", Tools::getValue('blacklist_product'))));
                Configuration::updateValue($this->name . '_days_stock', (int)Tools::getValue('days_stock'));
                Configuration::updateValue($this->name . '_days_nostock', (int)Tools::getValue('days_nostock'));
                Configuration::updateValue($this->name . '_combinations', (int)Tools::getValue('combinations'));
            }
            if (Tools::isSubmit('glami_submit_text') || Tools::isSubmit('submit_text')) {
                Configuration::updateValue($this->name . '_only_stock', (int)Tools::getValue('only_stock'));
            }


            if ($this->name == 'shaim_glami' && Tools::getValue('glami_cats')) {
                $pair = Tools::getValue('glami_cats');
                // if (is_array($pair) && !empty($pair) && $heureka_pair == 1) {
                Db::getInstance()->Execute('TRUNCATE ' . _DB_PREFIX_ . 'shaim_glami;');
                if (is_array($pair) && !empty($pair)) {

                    foreach ($pair as $local_id => $full_heureka) {
                        if ($full_heureka['cz']) {
                            Db::getInstance()->Execute("INSERT INTO " . _DB_PREFIX_ . "shaim_glami (local_id, glami_category_name, lang) VALUES ($local_id, '" . $full_heureka['cz'] . "', 'cz');");
                        }
                        if ($full_heureka['sk']) {
                            Db::getInstance()->Execute("INSERT INTO " . _DB_PREFIX_ . "shaim_glami (local_id, glami_category_name, lang) VALUES ($local_id, '" . $full_heureka['sk'] . "', 'sk');");
                        }
                    }
                }
                $result = $this->Show($this->l('Uloženo'), 'ok');
            }


            if ($this->name == 'shaim_pricemania' && Tools::isSubmit('pricemania_submit_text')) {
                $pair_both = array('cz' => Tools::getValue('pricemania_cats_cz'), 'sk' => Tools::getValue('pricemania_cats_sk'));

                // if (is_array($pair) && !empty($pair) && $heureka_pair == 1) {
                Db::getInstance()->Execute('TRUNCATE ' . _DB_PREFIX_ . 'shaim_pricemania;');
                foreach ($pair_both as $lang => $pair) {
                    if (is_array($pair) && !empty($pair)) {
                        foreach ($pair as $local_id => $export) {

                            if ($lang == 'sk' && isset($pair_both['cz'][$local_id]['export'])) {
                                $export['export'] = $pair_both['cz'][$local_id]['export'];
                            }

                            if (!isset($export['export'])) {
                                $export['export'] = 0;
                            }
                            $export['category_name'] = addslashes($export['category_name']);
                            Db::getInstance()->Execute("INSERT INTO " . _DB_PREFIX_ . "shaim_pricemania (local_id, export, pricemania_category_name, lang) VALUES ($local_id, {$export['export']}, '{$export['category_name']}', '$lang');");
                        }
                    }
                    $result = $this->Show($this->l('Uloženo'), 'ok');
                }
            }


            if ($this->name == 'shaim_export' && Tools::getValue('heureka_cats')) {
                $pair = Tools::getValue('heureka_cats');
                // if (is_array($pair) && !empty($pair) && $heureka_pair == 1) {
                Db::getInstance()->Execute('TRUNCATE ' . _DB_PREFIX_ . 'shaim_heureka;');
                if (is_array($pair) && !empty($pair)) {

                    foreach ($pair as $local_id => $full_heureka) {
                        if ($full_heureka['cz']) {
                            Db::getInstance()->Execute("INSERT INTO " . _DB_PREFIX_ . "shaim_heureka (local_id, heureka_full_name, lang) VALUES ($local_id, '" . $full_heureka['cz'] . "', 'cz');");
                        }
                        if ($full_heureka['sk']) {
                            Db::getInstance()->Execute("INSERT INTO " . _DB_PREFIX_ . "shaim_heureka (local_id, heureka_full_name, lang) VALUES ($local_id, '" . $full_heureka['sk'] . "', 'sk');");
                        }
                    }
                }
                $result = $this->Show($this->l('Uloženo'), 'ok');
            }
            if ($this->name == 'shaim_export' && Tools::getValue('google_cats')) {
                $pair = Tools::getValue('google_cats');
                Db::getInstance()->Execute('TRUNCATE ' . _DB_PREFIX_ . 'shaim_google;');
                // if (is_array($pair) && !empty($pair) && $heureka_pair == 1) {
                if (is_array($pair) && !empty($pair)) {

                    foreach ($pair as $local_id => $full_google) {
                        Db::getInstance()->Execute("INSERT IGNORE INTO " . _DB_PREFIX_ . "shaim_google (local_id, google_full_name) VALUES ($local_id, '$full_google');");
                    }
                }
                $result = $this->Show($this->l('Uloženo'), 'ok');
            }

            if ($this->name == 'shaim_export' && Tools::getValue('zbozi_cats')) {
                $pair = Tools::getValue('zbozi_cats');
                Db::getInstance()->Execute('TRUNCATE ' . _DB_PREFIX_ . 'shaim_zbozi;');
                // if (is_array($pair) && !empty($pair) && $heureka_pair == 1) {
                if (is_array($pair) && !empty($pair)) {

                    foreach ($pair as $local_id => $full_zbozi) {
                        Db::getInstance()->Execute("INSERT IGNORE INTO " . _DB_PREFIX_ . "shaim_zbozi (local_id, zbozi_full_name) VALUES ($local_id, '$full_zbozi');");
                    }
                }
                $result = $this->Show($this->l('Uloženo'), 'ok');
            }

            if (($this->name == 'shaim_export' || $this->name == 'shaim_glami') && Tools::getValue('carriers')) {
                $carriers = Tools::getValue('carriers');
                $new_carriers = array();
                if (is_array($carriers) && !empty($carriers)) {
                    foreach ($carriers as $carrier) {
                        if (empty($carrier['name'])) {
                            continue;
                        }
                        $carrier['name'] = trim($carrier['name']);
                        $carrier['price'] = str_replace('.', ',', str_replace(' ', '', $carrier['price']));
                        $carrier['price_cod'] = str_replace('.', ',', str_replace(' ', '', $carrier['price_cod']));
                        $carrier['free'] = str_replace('.', ',', str_replace(' ', '', $carrier['free']));
                        $new_carriers[] = $carrier;
                    }
                }

                Configuration::updateValue($this->name . '_carriers', serialize($new_carriers));
            }


            /*
            if (Tools::isSubmit('regenerate_token')) {
                $new_token = $this->ReGenerateToken(10);
                Configuration::updateValue(
                    $this->name . '_token', $new_token
                );
                $result = $this->Show($this->l('Token regenerován'), 'ok');
            } else
                */
            if (Tools::isSubmit('submit_text')) {


                $sluzby = array(
                    'active_zbozi_cz' => (int)Tools::getValue('active_zbozi_cz'),
                    'active_heureka_cz' => (int)Tools::getValue('active_heureka_cz'),
                    'active_heureka_sk' => (int)Tools::getValue('active_heureka_sk'),
                    'active_heureka_dostupnost' => (int)Tools::getValue('active_heureka_dostupnost'),
                    'active_google_com' => (int)Tools::getValue('active_google_com'),
                    'active_facebook_com' => (int)Tools::getValue('active_facebook_com'),
                );
                Configuration::updateValue($this->name . '_aktivni_sluzby', serialize($sluzby));

                Configuration::updateValue($this->name . '_heureka_pair', (int)Tools::getValue('heureka_pair'));
                Configuration::updateValue($this->name . '_google_pair', (int)Tools::getValue('google_pair'));
                Configuration::updateValue($this->name . '_zbozi_pair', (int)Tools::getValue('zbozi_pair'));
                Configuration::updateValue($this->name . '_desc', (int)Tools::getValue('desc'));
                Configuration::updateValue($this->name . '_combinations', (int)Tools::getValue('combinations'));
                // Configuration::updateValue($this->name . '_odber_zdarma', (int)Tools::getValue('odber_zdarma'));
                Configuration::updateValue($this->name . '_better_pair_manufacturer', (int)Tools::getValue('better_pair_manufacturer'));
                Configuration::updateValue($this->name . '_better_pair_code', (int)Tools::getValue('better_pair_code'));
                Configuration::updateValue($this->name . '_utm', (int)Tools::getValue('utm'));
                Configuration::updateValue($this->name . '_multistore', (int)Tools::getValue('multistore'));
                Configuration::updateValue($this->name . '_gift', Tools::getValue('gift'));
                Configuration::updateValue($this->name . '_gift_price', (float)Tools::getValue('gift_price'));
                Configuration::updateValue($this->name . '_max_cpc_limit', Tools::getValue('max_cpc_limit'));
                $heureka_cpc = str_replace('.', ',', trim(Tools::getValue('heureka_cpc')));
                if ($heureka_cpc > 100) {
                    $heureka_cpc = 100;
                } elseif ($heureka_cpc < 0) {
                    $heureka_cpc = -1;
                }
                Configuration::updateValue($this->name . '_heureka_cpc', $heureka_cpc);
                Configuration::updateValue($this->name . '_depot_ids_zbozi', trim(Tools::getValue('depot_ids_zbozi')));
                Configuration::updateValue($this->name . '_depot_ids_heureka', trim(Tools::getValue('depot_ids_heureka')));
                $max_cpc = (float)str_replace(',', '.', trim(Tools::getValue('max_cpc')));
                if ($max_cpc > 500) {
                    $max_cpc = 500;
                } elseif ($max_cpc < 0) {
                    $max_cpc = -1;
                } elseif ($max_cpc < 1) {
                    $max_cpc = 1;
                }
                Configuration::updateValue(
                    $this->name . '_max_cpc', $max_cpc
                );
                $max_cpc_search = (float)str_replace(',', '.', trim(Tools::getValue('max_cpc_search')));
                if ($max_cpc_search > 500) {
                    $max_cpc_search = 500;
                } elseif ($max_cpc_search < 0) {
                    $max_cpc_search = -1;
                } elseif ($max_cpc_search < 1) {
                    $max_cpc_search = 1;
                }
                Configuration::updateValue($this->name . '_max_cpc_search', $max_cpc_search);
                Configuration::updateValue($this->name . '_shipping_price', trim(Tools::getValue('shipping_price')));
                Configuration::updateValue($this->name . '_shipping_price_cod', trim(Tools::getValue('shipping_price_cod')));
                Configuration::updateValue($this->name . '_active_category', trim(Tools::getValue('active_category')));
                Configuration::updateValue($this->name . '_token', trim(Tools::getValue('export_token')));
                Configuration::updateValue($this->name . '_dost_day', (int)Tools::getValue('dost_day'));
                Configuration::updateValue($this->name . '_dost_time', trim(Tools::getValue('dost_time')));
                Configuration::updateValue($this->name . '_pick_day', (int)Tools::getValue('pick_day'));
                Configuration::updateValue($this->name . '_pick_time', trim(Tools::getValue('pick_time')));
                $result = $this->Show($this->l('Uloženo'), 'ok');
            }


            if (isset($result)) {
                $this->_html .= '<div class="bootstrap">' . $result . '</div>';
            }
        }
        $this->Statistics('open');
        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            // $this->context->controller->addCSS($this->_path . 'old_admin.css', 'all');
        }
        $this->context->controller->addCSS($this->_path . 'global_admin.css', 'all');
        $miahs = true;
        return $this->_generateForm();
    }

    private
    function MakeURL($data)
    {
        return "<a href='$data' title='$data' target='_blank'>$data</a>";
    }

    /*
    private function ReGenerateToken($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, Tools::strlen($characters) - 1)];
        }
        return $randomString;
    }
    */

    private
    function Statistics($action = 'install')
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

    private
    function GetLocalCats()
    {
        $active_category = (int)Configuration::get($this->name . '_active_category');
        $add_active = '';
        if ($active_category === 1) {
            $add_active = ' && a.active = 1';
        } elseif ($active_category === 0) {
            $add_active = ' && a.active = 0';
        }
        $tmp = Db::getInstance()->ExecuteS('SELECT a.id_category, a.id_parent, b.name FROM ' . _DB_PREFIX_ . 'category as a INNER JOIN ' . _DB_PREFIX_ . 'category_lang as b ON (a.id_category = b.id_category) WHERE b.id_lang = ' . Configuration::get('PS_LANG_DEFAULT') . ' && a.id_parent IN (SELECT id_category FROM ' . _DB_PREFIX_ . 'category) && a.id_category >= 2' . $add_active . ';');
        $this->local['cats'] = array();
        foreach ($tmp as $v) {
            $this->local['cats'][(int)$v['id_category']] = array('id_parent' => (int)$v['id_parent'], 'name' => (string)$v['name']);
        }
    }

    private
    function GetFullLocalCats()
    {
        $this->local['full'] = array();
        foreach ($this->local['cats'] as $key => $g) {
            $real = array();
            do {
                $real[] = (string)$g['name'];
                $tmp2 = (int)$g['id_parent'];
                if (!isset($this->local['cats'][$tmp2]) && $key > 2) {
                    break 1; // Predtim tu bylo 2, predelano na 1 kvuli pricemanii, snad to bude OK
                    // Dal jsem tam break misto continue, kvuli chybovym kategoriim.
                } elseif ($key == 2) {
                    $g = 0;
                } else {
                    $g = $this->local['cats'][$tmp2];
                }

            } while ($tmp2 > 2);
            $this->local['full'][] = array('name' => implode(' | ', array_reverse($real)), 'last_id' => (int)$key);

        }
        $this->local['full'] = $this->SortArray($this->local['full']);
    }

    private
    function GetHTMLFullLocalCats()
    {
        $this->local['html_heureka'] = '';
        $this->local['html_google'] = '';
        $this->local['html_zbozi'] = '';
        $this->local['html_glami'] = '';
        if ($this->local['full']) {
            $this->local['html_heureka'] .= '<div id="left_heureka_cz">';
            $this->local['html_google'] .= '<div id="left_google">';
            $this->local['html_zbozi'] .= '<div id="left_zbozi">';
            $this->local['html_glami'] .= '<div id="left_glami_cz">';
            foreach ($this->local['full'] as $full) {
                /**
                 * $this->tmp_full_name_for_selected = (string)Db::getInstance()->ExecuteS("SELECT heureka_full_name FROM "._DB_PREFIX_."shaim_heureka WHERE local_id = '{$full['last_id']}' LIMIT 0,1;");
                 * $this->tmp_full_name_for_selected = $this->tmp_full_name_for_selected[0]['heureka_full_name'];
                 **/
                $this->local['html_heureka'] .= '<span id="span-heureka-cz-' . $full['last_id'] . '" onclick="LeftClickHeurekaCZ(this);" name="heureka_cats[' . $full['last_id'] . '][cz]">' . $full['name'] . '</span>';
                $this->local['html_google'] .= '<span id="span-google-' . $full['last_id'] . '" onclick="LeftClickGoogle(this);" name="google_cats[' . $full['last_id'] . ']">' . $full['name'] . '</span>';

                $this->local['html_zbozi'] .= '<span id="span-zbozi-' . $full['last_id'] . '" onclick="LeftClickZbozi(this);" name="zbozi_cats[' . $full['last_id'] . ']">' . $full['name'] . '</span>';
                $this->local['html_glami'] .= '<span id="span-glami-cz-' . $full['last_id'] . '" onclick="LeftClickGlamiCZ(this);" name="glami_cats[' . $full['last_id'] . '][cz]">' . $full['name'] . '</span>';
            }
            $this->local['html_heureka'] .= '</div>';
            $this->local['html_google'] .= '</div>';
            $this->local['html_zbozi'] .= '</div>';
            $this->local['html_glami'] .= '</div>';
        }
    }

    private
    function expandHeureka($PARENT, $lang, $level, $PARENT_LEVEL = array())
    {
        // echo $level . PHP_EOL;
        $array = array('Heureka.cz |' => '', 'Heureka.sk |' => '');
        if (isset($PARENT->CATEGORY_FULL_NAME)) {     // Fix abychom tam neměli ty kategorie hlavní, kam prý nejde párovat
            if ($level == 0) {
                $full = trim(strtr($PARENT->CATEGORY_NAME, $array));
                $tmp = "RightClickHeureka" . strtoupper($lang);
                $this->tmp_expand_heureka[$lang][] = '<span id="' . $lang . '-' . $full . '" onclick="' . $tmp . '(this);">' . $full . '</span>';
            } else {
                $full = implode(' | ', $PARENT_LEVEL) . ' | ' . trim(strtr($PARENT->CATEGORY_NAME, $array));
                $tmp = "RightClickHeureka" . strtoupper($lang);
                $this->tmp_expand_heureka[$lang][] = '<span id="' . $lang . '-' . $full . '" onclick="' . $tmp . '(this);">' . $full . '</span>';
            }
        }

        $PARENT_LEVEL[] = trim(strtr($PARENT->CATEGORY_NAME, $array));

        if (isset($PARENT->CATEGORY)) {
            $level = $level + 1;
            foreach ($PARENT->CATEGORY as $CHILD) {
                if (!empty($CHILD->CATEGORY_FULLNAME)) { // Když se nedá zařadit, nepřidáváme do seznamu
                    $full = trim(strtr($CHILD->CATEGORY_FULLNAME, $array));
                    // if (!preg_match("/Sexuální a erotické/", $full)) {
                    //     continue;
                    // }
                    //$selected = ($this->tmp_full_name_for_selected == $full) ? ' selected = "selected"' : '';
                    // $this->h .= '<option value="'.$full.'" name="'.$full.'"'.$selected.'>'.$full.'</option>';
                    //$this->h .= '<option value="'.$full.'"'.$selected.'>'.$full.'</option>';
                    $tmp = "RightClickHeureka" . strtoupper($lang);
                    $this->tmp_expand_heureka[$lang][] = '<span id="' . $lang . '-' . $full . '" onclick="' . $tmp . '(this);">' . $full . '</span>';
                }

                $this->expandHeureka($CHILD, $lang, $level, $PARENT_LEVEL);
            }
        }
    }

    private
    function expandGlami($PARENT, $lang, $level, $PARENT_LEVEL = array())
    {
        // echo $level . PHP_EOL;
        $array = array('Glami.cz |' => '', 'Glami.sk |' => '');
        if (isset($PARENT->CATEGORY_FULL_NAME)) {     // Fix abychom tam neměli ty kategorie hlavní, kam prý nejde párovat
            if ($level == 0) {
                $full = trim(strtr($PARENT->CATEGORY_NAME, $array));
                $tmp = "RightClickGlami" . strtoupper($lang);
                $this->tmp_expand_glami[$lang][] = '<span id="' . $lang . '-' . $full . '" onclick="' . $tmp . '(this);">' . $full . '</span>';
            } else {
                $full = implode(' | ', $PARENT_LEVEL) . ' | ' . trim(strtr($PARENT->CATEGORY_NAME, $array));
                $tmp = "RightClickGlami" . strtoupper($lang);
                $this->tmp_expand_glami[$lang][] = '<span id="' . $lang . '-' . $full . '" onclick="' . $tmp . '(this);">' . $full . '</span>';
            }
        }

        $PARENT_LEVEL[] = trim(strtr($PARENT->CATEGORY_NAME, $array));

        if (isset($PARENT->CATEGORY)) {
            $level = $level + 1;
            foreach ($PARENT->CATEGORY as $CHILD) {
                if (!empty($CHILD->CATEGORY_FULLNAME)) { // Když se nedá zařadit, nepřidáváme do seznamu
                    $full = trim(strtr($CHILD->CATEGORY_FULLNAME, $array));
                    // if (!preg_match("/Sexuální a erotické/", $full)) {
                    //     continue;
                    // }
                    //$selected = ($this->tmp_full_name_for_selected == $full) ? ' selected = "selected"' : '';
                    // $this->h .= '<option value="'.$full.'" name="'.$full.'"'.$selected.'>'.$full.'</option>';
                    //$this->h .= '<option value="'.$full.'"'.$selected.'>'.$full.'</option>';
                    $tmp = "RightClickGlami" . strtoupper($lang);
                    $this->tmp_expand_glami[$lang][] = '<span id="' . $lang . '-' . $full . '" onclick="' . $tmp . '(this);">' . $full . '</span>';
                }

                $this->expandGlami($CHILD, $lang, $level, $PARENT_LEVEL);
            }
        }
    }


    private
    function LoadBasicZboziCats()
    {
        $url1_tmp = 'https://www.zbozi.cz/static/categories.csv';
        $file = dirname(__FILE__) . "/zbozi-kategorie.csv";
        $data = Tools::file_get_contents($url1_tmp);
        if (!empty($data) && (!is_file($file) || (is_file($file) && filemtime($file) < strtotime("now - 6 hours")))) {
            file_put_contents($file, $data);
        }
        // Nepodařilo se vytvořit/stahnout soubor, načítáme externě
        if (!Tools::file_exists_cache($file)) {
            $file = $url1_tmp;
        }


        $this->zbozi = "<div id='right_zbozi'>";
        if (($xmls = fopen($file, "r")) !== FALSE) {
            $tmp = array();
            while (($x = fgetcsv($xmls, 1000, ";")) !== FALSE) {
                // $id = (int)$x[0];
                $category = iconv('windows-1250', 'utf-8', $x[2]);
                if ($category == 'Celá cesta') {
                    continue;
                }

                $tmp[$category] = '<span id="' . $category . '" onclick="RightClickZbozi(this);">' . $category . '</span>';

            }
            fclose($xmls);
            ksort($tmp);
            $this->zbozi .= implode('', $tmp);
        }

        $this->zbozi .= '</div>';


    }

    private
    function LoadBasicHeurekaCats($lang)
    {
        $url1_tmp = 'https://www.heureka.' . $lang . '/direct/xml-export/shops/heureka-sekce.xml';
        $file = dirname(__FILE__) . "/heureka-sekce-" . $lang . ".xml";
        $data = Tools::file_get_contents($url1_tmp);
        if (!empty($data) && (!is_file($file) || (is_file($file) && filemtime($file) < strtotime("now - 6 hours")))) {
            file_put_contents($file, $data);
        }
        // Nepodařilo se vytvořit/stahnout soubor, načítáme externě
        if (!Tools::file_exists_cache($file)) {
            $file = $url1_tmp;
        }
        $xmls = simplexml_load_file($file);
        $this->h[$lang] = "<div id='right_heureka_$lang'>";

        foreach ($xmls as $x) {
            $this->expandHeureka($x, $lang, 0, array());
        }
        $this->h[$lang] .= implode('', array_filter(array_unique($this->tmp_expand_heureka[$lang])));
        $this->h[$lang] .= '</div>';

        // PHP 7 compatibility, OMG!!! (not needed, zmeneno v private $h
        // $this->h[$lang] = $this->h[$lang] . implode('', array_filter(array_unique($this->tmp_expand_heureka[$lang])));
        // $this->h[$lang] = $this->h[$lang] . '</div>';

    }


    private
    function LoadBasicGlamiCats($lang)
    {
        $url1_tmp = 'https://www.glami.' . $lang . '/category-xml/';
        $file = dirname(__FILE__) . "/glami-sekce-" . $lang . ".xml";
        $data = Tools::file_get_contents($url1_tmp);
        if (!empty($data) && (!is_file($file) || (is_file($file) && filemtime($file) < strtotime("now - 6 hours")))) {
            file_put_contents($file, $data);
        }
        // Nepodařilo se vytvořit/stahnout soubor, načítáme externě
        if (!Tools::file_exists_cache($file)) {
            $file = $url1_tmp;
        }
        $xmls = simplexml_load_file($file);
        $this->glami[$lang] = "<div id='right_glami_$lang'>";

        foreach ($xmls as $x) {
            $this->expandGlami($x, $lang, 0, array());
        }
        $this->glami[$lang] .= implode('', array_filter(array_unique($this->tmp_expand_glami[$lang])));
        $this->glami[$lang] .= '</div>';

        // PHP 7 compatibility, OMG!!! (not needed, zmeneno v private $h
        // $this->h[$lang] = $this->h[$lang] . implode('', array_filter(array_unique($this->tmp_expand_glami[$lang])));
        // $this->h[$lang] = $this->h[$lang] . '</div>';

    }

    private
    function LoadBasicGoogleCats()
    {
        $url1_tmp = 'https://www.google.com/basepages/producttype/taxonomy-with-ids.cs-CZ.txt';
        $file = dirname(__FILE__) . "/google-kategorie.xml";
        $data = Tools::file_get_contents($url1_tmp);
        if (!empty($data) && (!is_file($file) || (is_file($file) && filemtime($file) < strtotime("now - 6 hours")))) {
            file_put_contents($file, $data);
        }
        // Nepodařilo se vytvořit/stahnout soubor, načítáme externě
        if (!Tools::file_exists_cache($file)) {
            $file = $url1_tmp;
        }
        $txts = explode("\n", Tools::file_get_contents($file));
        $txts = array_filter(array_unique(array_map("trim", $txts)));
        $this->g = "<div id='right_google'>";
        foreach ($txts as $t) {
            if (!preg_match("/ - /", $t)) {
                continue;
            }
            $e = explode(" - ", $t);
            // $id = (int)$e[0];
            $category = (string)$e[1];
            $this->g .= '<span id="' . $category . '" onclick="RightClickGoogle(this);">' . $category . '</span>';
        }
        $this->g .= '</div>';
    }

    private
    function SortArray($array)
    {
        if ($array) {
            $sort = array();
            foreach ($array as $key => $row) {
                $sort[$key] = $row['name'];
            }
            array_multisort($sort, SORT_ASC, $array);
            return $array;
        }
    }

    private
    function _generateForm()
    {
        $this->context->controller->addCSS($this->_path . 'shaim.css', 'all');
        /** Local cats **/
        $zbozi_enable_pair = (int)Configuration::get($this->name . '_zbozi_pair');
        $heureka_enable_pair = (int)Configuration::get($this->name . '_heureka_pair');
        $google_enable_pair = (int)Configuration::get($this->name . '_google_pair');
        $glami_enable_pair = false;
        if ($this->name == 'shaim_export' && function_exists('file_get_contents')) {
            if ($zbozi_enable_pair || $heureka_enable_pair || $google_enable_pair) {

                $this->LoadBasicGoogleCats();
                $this->LoadBasicZboziCats();
                $this->LoadBasicHeurekaCats('cz');
                $this->LoadBasicHeurekaCats('sk');
                $this->GetLocalCats();
                $this->GetFullLocalCats(); // + sorted here
                $this->GetHTMLFullLocalCats();
            }
        } elseif ($this->name == 'shaim_glami') {
            $glami_enable_pair = true;
            $this->LoadBasicGlamiCats('cz');
            $this->LoadBasicGlamiCats('sk');
            $this->GetLocalCats();
            $this->GetFullLocalCats(); // + sorted here
            $this->GetHTMLFullLocalCats();

            // print_R($this->local['full']);
            // $this->GetHTMLFullLocalCats();
            // $this->LoadBasicGlamiCats();
            /*
            $glami_html = '';
            $tmp_paired_cats = Db::getInstance()->ExecuteS("SELECT local_id, glami_category_name, export, lang FROM " . _DB_PREFIX_ . "shaim_glami;");
            $paired_cats = array();
            if ($tmp_paired_cats) {
                foreach ($tmp_paired_cats as $pc) {
                    $paired_cats[$pc['local_id']]['local_id'] = (int)$pc['local_id'];
                    $paired_cats[$pc['local_id']][$pc['lang']]['glami_category_name'] = $pc['glami_category_name'];
                    $paired_cats[$pc['local_id']]['export'] = (int)$pc['export'];
                }
            }
            foreach ($this->local['full'] as $local) {
                $checked = (isset($paired_cats[$local['last_id']]['export']) && $paired_cats[$local['last_id']]['export'] == 1 ? ' checked="checked"' : '');
                $glami_html .= '<div class="checkbox">';
                $glami_html .= '<label for="glami_cats_cz[' . $local['last_id'] . '][export]" class="ruka normal_font">';
                $glami_html .= '<input type="checkbox" class="ruka" id="glami_cats_cz[' . $local['last_id'] . '][export]" name="glami_cats_cz[' . $local['last_id'] . '][export]" value="1"' . $checked . '> ' . $local['name'] . '</label>';

                $glami_html .= '<div class="input-group">';
                $glami_html .= '<input type="text" id="glami_cats_cz[' . $local['last_id'] . '][category_name]" name="glami_cats_cz[' . $local['last_id'] . '][category_name]" value="' . ((isset($paired_cats[$local['last_id']]['cz']['glami_category_name']) && !empty($paired_cats[$local['last_id']]['cz']['glami_category_name'])) ? $paired_cats[$local['last_id']]['cz']['glami_category_name'] : $local['name']) . '" placeholder="' . $this->l('Název kategorie, tak, jak má být přenášena do glami CZ, např. MUŽI | Boty | Tenisky (slouží pro napárování)') . '">';
                $glami_html .= '<span class="input-group-addon">' . $this->l('CZ') . '</span></div>';

                $glami_html .= '<div class="input-group">';
                $glami_html .= '<input type="text" id="glami_cats_sk[' . $local['last_id'] . '][category_name]" name="glami_cats_sk[' . $local['last_id'] . '][category_name]" value="' . ((isset($paired_cats[$local['last_id']]['sk']['glami_category_name']) && !empty($paired_cats[$local['last_id']]['sk']['glami_category_name'])) ? $paired_cats[$local['last_id']]['sk']['glami_category_name'] : $local['name']) . '" placeholder="' . $this->l('Název kategorie, tak, jak má být přenášena do glami SK, např. MUŽI | Topánky | Tenisky (slouží pro napárování)') . '">';
                $glami_html .= '<span class="input-group-addon">' . $this->l('SK') . '</span></div>';

                $glami_html .= '</div>';

            }
            */
        } elseif ($this->name == 'shaim_pricemania') {
            $this->GetLocalCats();
            $this->GetFullLocalCats(); // + sorted here

            // print_R($this->local['full']);
            // $this->GetHTMLFullLocalCats();
            // $this->LoadBasicGlamiCats();

            $pricemania_html = '';
            $tmp_paired_cats = Db::getInstance()->ExecuteS("SELECT local_id, pricemania_category_name, export, lang FROM " . _DB_PREFIX_ . "shaim_pricemania;");
            $paired_cats = array();
            if ($tmp_paired_cats) {
                foreach ($tmp_paired_cats as $pc) {
                    $paired_cats[$pc['local_id']]['local_id'] = (int)$pc['local_id'];
                    $paired_cats[$pc['local_id']][$pc['lang']]['pricemania_category_name'] = $pc['pricemania_category_name'];
                    $paired_cats[$pc['local_id']]['export'] = (int)$pc['export'];
                }
            }
            foreach ($this->local['full'] as $local) {
                $checked = (isset($paired_cats[$local['last_id']]['export']) && $paired_cats[$local['last_id']]['export'] == 1 ? ' checked="checked"' : '');
                $pricemania_html .= '<div class="checkbox">';
                $pricemania_html .= '<label for="pricemania_cats_cz[' . $local['last_id'] . '][export]" class="ruka normal_font">';
                $pricemania_html .= '<input type="checkbox" class="ruka" id="pricemania_cats_cz[' . $local['last_id'] . '][export]" name="pricemania_cats_cz[' . $local['last_id'] . '][export]" value="1"' . $checked . '> ' . $local['name'] . '</label>';

                $pricemania_html .= '<div class="input-group">';
                $pricemania_html .= '<input type="text" id="pricemania_cats_cz[' . $local['last_id'] . '][category_name]" name="pricemania_cats_cz[' . $local['last_id'] . '][category_name]" value="' . ((isset($paired_cats[$local['last_id']]['cz']['pricemania_category_name']) && !empty($paired_cats[$local['last_id']]['cz']['pricemania_category_name'])) ? $paired_cats[$local['last_id']]['cz']['pricemania_category_name'] : $local['name']) . '" placeholder="' . $this->l('Název kategorie, tak, jak má být přenášena do pricemania CZ, např. Oblečení > Obuv > Boty do vody (slouží pro napárování)') . '">';
                $pricemania_html .= '<span class="input-group-addon">' . $this->l('CZ') . '</span></div>';

                $pricemania_html .= '<div class="input-group">';
                $pricemania_html .= '<input type="text" id="pricemania_cats_sk[' . $local['last_id'] . '][category_name]" name="pricemania_cats_sk[' . $local['last_id'] . '][category_name]" value="' . ((isset($paired_cats[$local['last_id']]['sk']['pricemania_category_name']) && !empty($paired_cats[$local['last_id']]['sk']['pricemania_category_name'])) ? $paired_cats[$local['last_id']]['sk']['pricemania_category_name'] : $local['name']) . '" placeholder="' . $this->l('Název kategorie, tak, jak má být přenášena do pricemania SK, např. Oblečenie > Obuv > Topánky do mora (slouží pro napárování)') . '">';
                $pricemania_html .= '<span class="input-group-addon">' . $this->l('SK') . '</span></div>';

                $pricemania_html .= '</div>';

            }
        }

        // $export_token = Configuration::get($this->name . '_token');
        // glami nema token proto zamerne to bereme z exportu
        $export_token = Configuration::get('shaim_export_token');
        $this->_html .= '<form class="form-horizontal" action="' . $_SERVER['REQUEST_URI'] . '" method="post">';
        if ($this->real_name == 'heureka_cz') {
            $this->_html .= '<style>
            #heureka_sk_parovani{display: none;}
             </style>';
        } elseif ($this->real_name == 'heureka_sk') {
            $this->_html .= '<style>
            #heureka_cz_parovani{display: none;}
            </style>
            ';
        }


        // SETTINGS BEGIN
        // SETTINGS BEGIN
        // SETTINGS BEGIN

        $add_id_shop_for_url = '';
        if (Shop::isFeatureActive()) {
            $add_id_shop_for_url = '&id_shop=' . (int)$this->context->shop->id;
        }

        if ($this->name == 'shaim_export') {
            $this->_html .= '<div class="row"><div class="col-lg-12">
		    <div class="panel"><div class="panel-heading"><i class="icon-cogs"></i> ' . $this->l('Nastavení exportu') . '</div>'; // FRAME
            $this->_html .= '<fieldset>';
            $this->_html .= '<legend>' . $this->l('Obecné nastavení') . '</legend>';


            $this->_html .= '<div class="form-group">';
            $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Jaký popis používat přednostně') . '</label>';
            $this->_html .= '<div class="col-lg-9">
				<select name="desc" class="form-control fixed-width-xxl">
				<option value="0">' . $this->l('krátký') . '</option>
				<option value="1"' . ((Configuration::get($this->name . '_desc') == 1) ? ' selected = "selected"' : '') . '>' . $this->l('dlouhý') . '</option>
				</select>
				</div>';

            $this->_html .= '</div>';


            $this->_html .= '<div class="form-group">';
            $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Exportovat pouze položky skladem') . '</label>';

            $this->_html .= '<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
										<input name="only_stock" id="only_stock_on" value="1"' . ((Configuration::get($this->name . '_only_stock') == 1) ? ' checked="checked"' : '') . ' type="radio">
										<label for="only_stock_on">' . $this->l('ANO') . '</label>
										<input name="only_stock" id="only_stock_off" value="0"' . ((Configuration::get($this->name . '_only_stock') == 0) ? ' checked="checked"' : '') . ' type="radio">
										<label for="only_stock_off">' . $this->l('NE') . '</label>
										<a class="slide-button btn"></a>
									</span></div>';

            $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
				' . $this->l('Funguje dobře pouze v případě, že máte zapnutý sklad a že chcete exportovat pouze položky, které mají sklad vyšší jak 0.') . '
				</div></div>';
            $this->_html .= '</div>';


            $this->_html .= '<div class="form-group">';
            $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Používat kombinace produktů') . '</label>';

            $this->_html .= '<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
										<input name="combinations" id="combinations_on" value="1"' . ((Configuration::get($this->name . '_combinations') == 1) ? ' checked="checked"' : '') . ' type="radio">
										<label for="combinations_on">' . $this->l('ANO') . '</label>
										<input name="combinations" id="combinations_off" value="0"' . ((Configuration::get($this->name . '_combinations') == 0) ? ' checked="checked"' : '') . ' type="radio">
										<label for="combinations_off">' . $this->l('NE') . '</label>
										<a class="slide-button btn"></a>
									</span></div>';

            $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
				' . $this->l('v 99% případů by mělo zůstat zapnuté.') . '
				</div></div>';
            $this->_html .= '</div>';

            $this->_html .= '<div class="form-group">';
            $this->_html .= '<label class="control-label col-lg-3">' . $this->l('V názvu uvést i výrobce') . '</label>';

            $this->_html .= '<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
										<input name="better_pair_manufacturer" id="better_pair_manufacturer_on" value="1"' . ((Configuration::get($this->name . '_better_pair_manufacturer') == 1) ? ' checked="checked"' : '') . ' type="radio">
										<label for="better_pair_manufacturer_on">' . $this->l('ANO') . '</label>
										<input name="better_pair_manufacturer" id="better_pair_manufacturer_off" value="0"' . ((Configuration::get($this->name . '_better_pair_manufacturer') == 0) ? ' checked="checked"' : '') . ' type="radio">
										<label for="better_pair_manufacturer_off">' . $this->l('NE') . '</label>
										<a class="slide-button btn"></a>
									</span></div>';

            $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
				' . $this->l('Pro potencionální lepší párování.') . '
				</div></div>';
            $this->_html .= '</div>';

            $this->_html .= '<div class="form-group">';
            $this->_html .= '<label class="control-label col-lg-3">' . $this->l('V názvu uvést i kód produktu') . '</label>';

            $this->_html .= '<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
										<input name="better_pair_code" id="better_pair_code_on" value="1"' . ((Configuration::get($this->name . '_better_pair_code') == 1) ? ' checked="checked"' : '') . ' type="radio">
										<label for="better_pair_code_on">' . $this->l('ANO') . '</label>
										<input name="better_pair_code" id="better_pair_code_off" value="0"' . ((Configuration::get($this->name . '_better_pair_code') == 0) ? ' checked="checked"' : '') . ' type="radio">
										<label for="better_pair_code_off">' . $this->l('NE') . '</label>
										<a class="slide-button btn"></a>
									</span></div>';

            $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
				' . $this->l('Pro potencionální lepší párování.') . '
				</div></div>';
            $this->_html .= '</div>';


            /*
            $this->_html .= '<div class="form-group">';
            $this->_html .= '<label class="control-label col-lg-3">' . $this->l('V XML feedu v URL adrese uvádět utm_source') . '</label>';
            $this->_html .= '<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
										<input name="utm" id="utm_on" value="1"' . ((Configuration::get($this->name . '_utm') == 1) ? ' checked="checked"' : '') . ' type="radio">
										<label for="utm_on">' . $this->l('ANO') . '</label>
										<input name="utm" id="utm_off" value="0"' . ((Configuration::get($this->name . '_utm') == 0) ? ' checked="checked"' : '') . ' type="radio">
										<label for="utm_off">' . $this->l('NE') . '</label>
										<a class="slide-button btn"></a>
									</span></div>';

            $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
				' . $this->l('Např utm_source=zbozi, utm_source=heureka atd. Pokud nevíte, jak to používat, co to je apod, nechte volbu vypnutou na "NE".') . '
				</div></div>';
            $this->_html .= '</div>';
            */


            $this->_html .= '<div class="form-group">';
            $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Bezpečnostní token') . '</label>';
            $this->_html .= '<div class="col-lg-9"><input type="text" name="export_token" value="' . $export_token . '"></div>';
            $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
				' . $this->l('Max 32 znaků, pouze pro pokročilé uživatele. Pokud nevíte, k čemu token slouží a jak funguje, prosím, neměňte tuto hodnotu!') . '
				</div></div>';
            $this->_html .= '</div>';

            $this->_html .= '<div class="form-group">';
            $this->_html .= '<label class="control-label col-lg-3">' . $this->l('V párování kategorií zobrazit pouze') . '</label>';
            $this->_html .= '<div class="col-lg-9"><select name="active_category" class="form-control fixed-width-xxl"><option value="-1">' . $this->l('aktivní i neaktivní') . '</option><option value="1"' . ((Configuration::get($this->name . '_active_category') == 1) ? ' selected = "selected"' : '') . '>' . $this->l('aktivní') . '</option><option value="0"' . ((Configuration::get($this->name . '_active_category') == 0) ? ' selected = "selected"' : '') . '>' . $this->l('neaktivní') . '</option></select></div>';
            $this->_html .= '</div>';


            $this->_html .= '<div class="form-group">';
            $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Doba dodání u produktu, který je skladem') . '</label>';
            $this->_html .= '<div class="col-lg-9"><input type="text" name="days_stock" value="' . (int)Configuration::get($this->name . '_days_stock') . '"></div>';
            $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
				' . $this->l('Počet dní, které uvádět v XML v případě, že produkt je skladem (default 0)') . '
				</div></div>';
            $this->_html .= '</div>';

            $this->_html .= '<div class="form-group">';
            $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Doba dodání u produktu, který není skladem') . '</label>';
            $this->_html .= '<div class="col-lg-9"><input type="text" name="days_nostock" value="' . (int)Configuration::get($this->name . '_days_nostock') . '"></div>';
            $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
				' . $this->l('Počet dní, které uvádět v XML v případě, že produkt není skladem (default 7)') . '
				</div></div>';
            $this->_html .= '</div>';


            $this->_html .= '<div class="form-group">';
            $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Dárek (tag GIFT) - globální text pro všechny produkty') . '</label>';
            $this->_html .= '<div class="col-lg-9"><input type="text" name="gift" value="' . Configuration::get($this->name . '_gift') . '"></div>';
            $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
				' . $this->l('Více dárků oddělujte čárkou') . '
				</div></div>';
            $this->_html .= '</div>';


            $this->_html .= '<div class="form-group">';
            $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Dárek (tag GIFT) - globální text pro všechny produkty - od jaké ceny zobrazovat?') . '</label>';
            $this->_html .= '<div class="col-lg-9"><input type="text" name="gift_price" value="' . Configuration::get($this->name . '_gift_price') . '"></div>';
            $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
				' . $this->l('') . '
				</div></div>';
            $this->_html .= '</div>';


            $this->_html .= '<div class="form-group">';
            $this->_html .= '<label class="control-label col-lg-3">' . $this->l('MAX_CPC/HEUREKA_CPC limit Kč') . '</label>';
            $this->_html .= '<div class="col-lg-9"><input type="text" name="max_cpc_limit" value="' . Configuration::get($this->name . '_max_cpc_limit') . '"></div>';
            $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
				' . $this->l('Uveďte zde částku, od které chcete, aby se MAX_CPC/HEUREKA_CPC uplatňovalo (propisovalo do XML). Např. pokud chcete tyto tagy úvádět pouze pro produkty nad 150 Kč, zadejte hodnotu 150. 0 = bez omezení.') . '
				</div></div>';
            $this->_html .= '</div>';


            $this->_html .= '</fieldset>';


            if ($this->real_name == 'zbozi_cz' || $this->real_name == 'all') {
                $this->_html .= '<fieldset class="margin_up">';

                $this->_html .= '<div class="form-group">';
                $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Párovat kategorie') . '</label>';
                $this->_html .= '<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
										<input name="zbozi_pair" id="zbozi_pair_on" value="1"' . (($zbozi_enable_pair == 1) ? ' checked="checked"' : '') . ' type="radio">
										<label for="zbozi_pair_on">' . $this->l('ANO') . '</label>
										<input name="zbozi_pair" id="zbozi_pair_off" value="0"' . (($zbozi_enable_pair == 0) ? ' checked="checked"' : '') . ' type="radio">
										<label for="zbozi_pair_off">' . $this->l('NE') . '</label>
										<a class="slide-button btn"></a>
									</span></div>';


                $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
					' . $this->l(' (Po zapnutí a uložení této možnost se párování nachází níže)') . '
					</div></div>';
                $this->_html .= '</div>';

                /*
                $odber_zdarma = (int)Configuration::get($this->name . '_odber_zdarma');
                $this->_html .= '<div class="form-group">';
                $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Osobní odběr zdarma (pro všechny produkty)') . '</label>';
                $this->_html .= '<div class="col-lg-9">
                <span class="switch prestashop-switch fixed-width-lg">
                                    <input name="odber_zdarma" id="odber_zdarma_on" value="1"' . (($odber_zdarma == 1) ? ' checked="checked"' : '') . ' type="radio">
                                    <label for="odber_zdarma_on">' . $this->l('ANO') . '</label>
                                    <input name="odber_zdarma" id="odber_zdarma_off" value="0"' . (($odber_zdarma == 0) ? ' checked="checked"' : '') . ' type="radio">
                                    <label for="odber_zdarma_off">' . $this->l('NE') . '</label>
                                    <a class="slide-button btn"></a>
                                </span></div>';
                $this->_html .= '</div>';
*/

                $this->_html .= '<legend>' . $this->l('Zboží.cz') . '</legend>';
                $this->_html .= '<div class="form-group">';
                $this->_html .= '<label class="control-label col-lg-3">' . $this->l('MAX_CPC') . '</label>';
                $this->_html .= '<div class="col-lg-9"><input type="text" name="max_cpc" value="' . Configuration::get($this->name . '_max_cpc') . '"></div>';
                $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
					' . $this->l('oddělovač desetinných míst je tečka, 1 - 500 Kč, -1 = neuvádět tento tag v XML') . '
					</div></div>';
                $this->_html .= '</div>';


                $this->_html .= '<div class="form-group">';
                $this->_html .= '<label class="control-label col-lg-3">' . $this->l('MAX_CPC_SEARCH') . '</label>';
                $this->_html .= '<div class="col-lg-9"><input type="text" name="max_cpc_search" value="' . Configuration::get($this->name . '_max_cpc_search') . '"></div>';
                $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
					' . $this->l('oddělovač desetinných míst je tečka, 1 - 500 Kč, -1 = neuvádět tento tag v XML') . '
					</div></div>';
                $this->_html .= '</div>';


                $this->_html .= '<div class="form-group">';
                $this->_html .= '<label class="control-label col-lg-3">' . $this->l('DEPOT_ID (ID výdejního místa)') . '</label>';
                $this->_html .= '<div class="col-lg-9"><input type="text" name="depot_ids_zbozi" value="' . Configuration::get($this->name . '_depot_ids_zbozi') . '"></div>';
                $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
					' . $this->l('Více ID oddělujte čárkou') . '
					</div></div>';
                $this->_html .= '</div>';


                $this->_html .= '<div class="form-group">';
                $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Registrovat se na Zboží.cz (pokud ještě nemáte registraci)') . '</label>';
                $registrace_zbozi_url = 'https://klient.seznam.cz/registration/zbozi/?acquisitions=0&url=' . $this->full_url . '&name=' . $this->context->shop->name . '&feed=' . $this->full_url . $this->export_folder . "/zbozi_cz.xml";
                $this->_html .= '<div class="col-lg-9"><a href="' . $registrace_zbozi_url . '" target="_blank" class="btn btn-default">' . $this->l('Provést registraci') . '</a></div>';

                $this->_html .= '</div>';

                $this->_html .= '</fieldset>';


                $this->_html .= '<fieldset class="margin_up">';
                $this->_html .= '<legend>' . $this->l('Zboží.cz + Heureka.cz/.sk - dopravy') . '</legend>';
                $dopravci = array(
                    /* CZ */
                    'CZ_CESKA_POSTA', 'CZ_CESKA_POSTA_NA_POSTU', 'CZ_CESKA_POSTA_DOPORUCENA_ZASILKA', 'CZ_CSAD_LOGISTIK_OSTRAVA', 'CZ_DPD', 'CZ_DHL', 'CZ_DSV', 'CZ_FOFR', 'CZ_GEBRUDER_WEISS', 'CZ_GEIS', 'CZ_GLS', 'CZ_HDS', 'CZ_HEUREKAPOINT', 'CZ_INTIME', 'CZ_PPL', 'CZ_SEEGMULLER', 'CZ_TNT', 'CZ_TOPTRANS', 'CZ_UPS', 'CZ_FEDEX', 'CZ_RABEN_LOGISTICS', 'CZ_VLASTNI_PREPRAVA', 'CZ_ZASILKOVNA', 'CZ_DPD_PICKUP', 'CZ_ULOZENKA',
                    // ZBOZI SPECIFIC
                    // 'CZ_ZBOZI_GEIS_POINT', 'CZ_ZBOZI_GLS_PARCELSHOP', 'CZ_ZBOZI_PPL_PARCELSHOP', 'CZ_ZBOZI_TOPTRANS_DEPO', 'CZ_ZBOZI_DB_SCHENKER', 'CZ_ZBOZI_MESSENGER', 'CZ_ZBOZI_RHENUS', 'CZ_ZBOZI_VLASTNI_VYDEJNI_MISTA',
                    'CZ_GEIS_POINT', 'CZ_GLS_PARCELSHOP', 'CZ_PPL_PARCELSHOP', 'CZ_TOPTRANS_DEPO', 'CZ_DB_SCHENKER', 'CZ_MESSENGER', 'CZ_RHENUS', 'CZ_VLASTNI_VYDEJNI_MISTA',
                    /* SK */
                    'SK_SLOVENSKA_POSTA', 'SK_SLOVENSKA_POSTA_BALIK_NA_POSTU', 'SK_DPD', 'SK_DHL', 'SK_DSV', 'SK_EXPRES_KURIER', 'SK_GEBRUDER_WEISS', 'SK_GEIS', 'SK_GLS', 'SK_HDS', 'SK_INTIME', 'SK_PPL', 'SK_REMAX', 'SK_TNT', 'SK_TOPTRANS', 'SK_UPS', 'SK_FEDEX', 'SK_RABEN_LOGISTICS', 'SK_VLASTNI_PREPRAVA', 'SK_ZASILKOVNA', 'SK_DPD_PICKUP', 'SK_ULOZENKA',);
                asort($dopravci);
                $carriers = unserialize(Configuration::get($this->name . '_carriers'));
                for ($i = 0; $i <= 6; $i++) {
                    $this->_html .= '<div class="form-group">';
                    $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Dopravci, kteří se propíší do XML #') . ($i + 1) . '</label>';
                    $this->_html .= '<div class="col-lg-9">
                    <select class="select_bigger" name="carriers[' . $i . '][name]">';
                    $this->_html .= '<option value="0">---</option>';

                    foreach ($dopravci as $dopravce) {
                        // $dopravce = str_replace('CZ_ZBOZI_', 'CZ_', $dopravce);
                        $selected = (isset($carriers[$i]['name']) && $carriers[$i]['name'] == $dopravce) ? ' selected="selected"' : '';
                        $this->_html .= '<option value="' . $dopravce . '"' . $selected . '>' . $dopravce . '</option>';
                    }
                    $this->_html .= '</select>
                    <input type="text" placeholder="' . $this->l('Cena bez dobírky') . '" name="carriers[' . $i . '][price]" value="' . (isset($carriers[$i]['price']) ? $carriers[$i]['price'] : '') . '">
                    <input type="text" placeholder="' . $this->l('Cena včetně dobírky') . '" name="carriers[' . $i . '][price_cod]" value="' . (isset($carriers[$i]['price_cod']) ? $carriers[$i]['price_cod'] : '') . '">
                    <input type="text" placeholder="' . $this->l('Zdarma od') . '" name="carriers[' . $i . '][free]" value="' . (isset($carriers[$i]['free']) ? $carriers[$i]['free'] : '') . '">
                    </div>';
                    $this->_html .= '</div>';
                }
                $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
					' . $this->l('Zde zvolené dopravy a ceny za ně se propíší do XML feedu u všech produktů. (tag DELIVERY).') . '<br />' . $this->l('Pokud dobírku nechcete v XML uvádět, vepište do sloupce s cenou za dobírku hodnotu -1 (mínus jedna)') . '
					</div></div>';

                $this->_html .= '</fieldset>';
            }
            if ($this->real_name == 'heureka_cz' || $this->real_name == 'heureka_sk' || $this->real_name == 'all') {
                $heureka_tmp_name = 'Heureka.cz/.sk';
                if ($this->real_name == 'heureka_cz') {
                    $heureka_tmp_name = 'Heureka.cz';
                } elseif ($this->real_name == 'heureka_sk') {
                    $heureka_tmp_name = 'Heureka.sk';
                }
                $this->_html .= '<fieldset  class="margin_up">';
                $this->_html .= '<legend>' . $heureka_tmp_name . '</legend>';
                $this->_html .= '<div>';

                $this->_html .= '<div class="form-group">';
                $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Párovat kategorie') . '</label>';


                $this->_html .= '<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
										<input name="heureka_pair" id="heureka_pair_on" value="1"' . (($heureka_enable_pair == 1) ? ' checked="checked"' : '') . ' type="radio">
										<label for="heureka_pair_on">' . $this->l('ANO') . '</label>
										<input name="heureka_pair" id="heureka_pair_off" value="0"' . (($heureka_enable_pair == 0) ? ' checked="checked"' : '') . ' type="radio">
										<label for="heureka_pair_off">' . $this->l('NE') . '</label>
										<a class="slide-button btn"></a>
									</span></div>';


                $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
					' . $this->l(' (Po zapnutí a uložení této možnost se párování nachází níže)') . '
					</div></div>';
                $this->_html .= '</div>';
                $this->_html .= '</div>';


                $this->_html .= '<div>';
                $this->_html .= '<div class="form-group">';
                $this->_html .= '<label class="control-label col-lg-3">' . $this->l('HEUREKA_CPC') . '</label>';
                $this->_html .= '<div class="col-lg-9"><input type="text" name="heureka_cpc" value="' . Configuration::get($this->name . '_heureka_cpc') . '" ></div>
					';
                $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
					' . $this->l('(oddělovač desetinných míst je čárka, 0 - 100 Kč., -1 = neuvádět tento tag v XML)') . '
					</div></div>';
                $this->_html .= '</div>';


                $this->_html .= '<div class="form-group">';
                $this->_html .= '<label class="control-label col-lg-3">' . $this->l('DEPOT_ID (ID provozovny)') . '</label>';
                $this->_html .= '<div class="col-lg-9"><input type="text" name="depot_ids_heureka" value="' . Configuration::get($this->name . '_depot_ids_heureka') . '" ></div>
					';
                $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
					' . $this->l('Více ID oddělujte čárkou') . '
					</div></div>';
                $this->_html .= '</div>';

                $this->_html .= '</fieldset>';
                /**
                 * $this->_html .= '<label class="nastaveni">'.$this->l('Multistore').':</label>';
                 * $this->_html .= '<div>';
                 * $multistore = (int)Configuration::get($this->name.'_multistore');
                 * $selected_pair = $multistore == 1 ? ' selected = "selected"' : '';
                 * $this->_html .= '<select name="multistore"><option value="0">ne</option><option value="1"'.$selected_pair.'>ano</option></select>';
                 * $this->_html .= '</div>';
                 **/
                $this->_html .= '<fieldset class="margin_up">';
                $this->_html .= '<legend>' . $heureka_tmp_name . $this->l(' Dostupnostní feed') . '</legend>';
                $this->_html .= '<div>';

                $this->_html .= '<div class="form-group">';
                $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Počet dní od objednávky do doručení (delivery_time)') . '</label>';
                $this->_html .= '<div class="col-lg-9"><input type="text" name="dost_day" value="' . Configuration::get($this->name . '_dost_day') . '"></div>';
                $this->_html .= '</div>';

                $this->_html .= '<div>';
                $this->_html .= '<div class="form-group">';
                $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Čas poslední objednávky pro dodržení doby doručení (delivery_time)') . '</label>';
                $this->_html .= '<div class="col-lg-9"><div class="input-group" style="width: 200px;"><input type="text" name="dost_time" value="' . Configuration::get($this->name . '_dost_time') . '" ><span class="input-group-addon">' . $this->l('(HH:mm)') . '</span></div></div>';
                $this->_html .= '</div>';


                $this->_html .= '<div class="form-group">';
                $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Počet dní od objednávky do doručení (pickup_time)') . '</label>';
                $this->_html .= '<div class="col-lg-9"><input type="text" name="pick_day" value="' . Configuration::get($this->name . '_pick_day') . '"></div>';
                $this->_html .= '</div>';

                $this->_html .= '<div>';
                $this->_html .= '<div class="form-group">';
                $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Čas poslední objednávky pro dodržení doby doručení (pickup_time)') . '</label>';
                $this->_html .= '<div class="col-lg-9"><div class="input-group" style="width: 200px;"><input type="text" name="pick_time" value="' . Configuration::get($this->name . '_pick_time') . '" ><span class="input-group-addon">' . $this->l('(HH:mm)') . '</span></div></div>';
                $this->_html .= '</div>';
                $this->_html .= '</fieldset>';

            }

            if ($this->real_name == 'google_com' || $this->real_name == 'facebook_com' || $this->real_name == 'all') {
                $this->_html .= '<fieldset class="margin_up">';
                $this->_html .= '<legend>' . $this->l('Google.com / Facebook.com') . '</legend>';
                $this->_html .= '<div>';

                $this->_html .= '<div class="form-group">';
                $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Párovat kategorie') . '</label>';

                $this->_html .= '<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
										<input name="google_pair" id="google_pair_on" value="1"' . (($google_enable_pair == 1) ? ' checked="checked"' : '') . ' type="radio">
										<label for="google_pair_on">' . $this->l('ANO') . '</label>
										<input name="google_pair" id="google_pair_off" value="0"' . (($google_enable_pair == 0) ? ' checked="checked"' : '') . ' type="radio">
										<label for="google_pair_off">' . $this->l('NE') . '</label>
										<a class="slide-button btn"></a>
									</span></div>';


                $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
					' . $this->l(' (Po zapnutí a uložení této možnost se párování nachází níže)') . '
					</div></div>';
                $this->_html .= '</div>';
                $this->_html .= '</div>';


                $this->_html .= '<div class="form-group">';
                $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Cena dopravy bez dobírky') . '</label>';
                $this->_html .= '<div class="col-lg-9"><input type="text" name="shipping_price" value="' . Configuration::get($this->name . '_shipping_price') . '"></div>';
                $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
				' . $this->l('oddělovač desetinných míst je tečka, -1 = neuvádět tento tag v XML') . '
				</div></div>';
                $this->_html .= '</div>';

                $this->_html .= '<div class="form-group">';
                $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Cena dopravy s dobírkou') . '</label>';
                $this->_html .= '<div class="col-lg-9"><input type="text" name="shipping_price_cod" value="' . Configuration::get($this->name . '_shipping_price_cod') . '"></div>';
                $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
				' . $this->l('oddělovač desetinných míst je tečka, -1 = neuvádět tento tag v XML') . '
				</div></div>';
                $this->_html .= '</div>';


                $this->_html .= '</fieldset>';
            }


            $this->_html .= '<fieldset class="margin_up">';
            $this->_html .= '<legend>' . $this->l('Zvolené služby') . '</legend>';
            $this->_html .= '<div>';
            $this->_html .= '<div>';
            $this->_html .= '</div>';
            $this->_html .= '<div class="form-group">';

            $aktivni_sluzby = unserialize(Configuration::get($this->name . '_aktivni_sluzby'));

            $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Aktivní feedy') . '</label>';
            $this->_html .= '<div class="col-lg-9">';

            $this->_html .= '<div class="checkbox"><label for="active_zbozi_cz" class="ruka normal_font"><input type="checkbox" class="ruka"name="active_zbozi_cz" id="active_zbozi_cz" value="1"' . (($aktivni_sluzby['active_zbozi_cz'] == 1) ? ' checked = "checked"' : '') . '>' . $this->l('Zboží') . '</label></div>';
            $this->_html .= '<div class="checkbox"><label for="active_heureka_cz" class="ruka normal_font"><input type="checkbox" class="ruka"name="active_heureka_cz" id="active_heureka_cz" value="1"' . (($aktivni_sluzby['active_heureka_cz'] == 1) ? ' checked = "checked"' : '') . '>' . $this->l('Heureka CZ') . '</label></div>';
            $this->_html .= '<div class="checkbox"><label for="active_heureka_sk" class="ruka normal_font"><input type="checkbox" class="ruka"name="active_heureka_sk" id="active_heureka_sk" value="1"' . (($aktivni_sluzby['active_heureka_sk'] == 1) ? ' checked = "checked"' : '') . '>' . $this->l('Heureka SK') . '</label></div>';
            $this->_html .= '<div class="checkbox"><label for="active_heureka_dostupnost" class="ruka normal_font"><input type="checkbox" class="ruka"name="active_heureka_dostupnost" id="active_heureka_dostupnost" value="1"' . (($aktivni_sluzby['active_heureka_dostupnost'] == 1) ? ' checked = "checked"' : '') . '>' . $this->l('Heureka Dost. feed') . '</label></div>';
            $this->_html .= '<div class="checkbox"><label for="active_google_com" class="ruka normal_font"><input type="checkbox" class="ruka"name="active_google_com" id="active_google_com" value="1"' . (($aktivni_sluzby['active_google_com'] == 1) ? ' checked = "checked"' : '') . '>' . $this->l('Google') . '</label></div>';
            $this->_html .= '<div class="checkbox"><label for="active_facebook_com" class="ruka normal_font"><input type="checkbox" class="ruka"name="active_facebook_com" id="active_facebook_com" value="1"' . (($aktivni_sluzby['active_facebook_com'] == 1) ? ' checked = "checked"' : '') . '>' . $this->l('Facebook') . '</label></div>';


            $this->_html .= '</div>';
            $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
					' . $this->l('Zvolte opravdu pouze služby, které budete reálně využívat, nemá smysl exportovat všechno, zbytečně by to došlo ke zpomalení Vašeho shopu vlivem generování nadbytečných informací.') . '
					</div></div>';


            $this->_html .= '<div class="form-group">';
            $this->_html .= '<label class="control-label col-lg-3">' . $this->l('ID produktů, které nechcete exportovat do XML (Blacklist)') . '</label>';
            $this->_html .= '<div class="col-lg-9"><textarea name="blacklist_product" rows="10">' . Configuration::get($this->name . '_blacklist_product') . '</textarea></div>';
            $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
					' . $this->l('Produkty oddělte čárkou.') . '
					</div></div>';
            $this->_html .= '</div>';

            $this->_html .= '</div>';
            $this->_html .= '</div>';
            $this->_html .= '</fieldset>';


            $cron_url = $this->full_url . 'modules/' . $this->name . '/' . $this->export_file . "?" . $this->real_name . "&open&close&token=$export_token" . $add_id_shop_for_url;
            $this->_html .= '<div class="panel-footer">

			<a href="' . $cron_url . '" class="btn btn-default" target="_blank">
							<i class="process-icon-download"></i> ' . $this->l('Vyexportovat nyní') . '
			</a>

			<button type="submit" class="filtr btn btn-default pull-right" name="submit_text"><i class="process-icon-save"></i>' . $this->l('Uložit') . '</button></div>';

            $this->_html .= '</form>';
            $this->_html .= '</div></div></div>'; //FRAME END


            // SETTINGS END
            // SETTINGS END
            // SETTINGS END

            // RESULTS BEGIN
            // RESULTS BEGIN
            // RESULTS BEGIN

            $this->_html .= '<div>';
            $this->_html .= '<div class="row"><div class="col-lg-12"><div class="panel"><div class="panel-heading"><i class="icon-list"></i> ' . $this->l('CRON URL') . '</div>'; // FRAME
            /*
            $this->_html .= '<input type="submit" name="regenerate_token" ';
            $this->_html .= 'value="' . $this->l('Regenerovat token') . '" class="button" />';
            $this->_html .= '</div>';
            */

            $this->_html .= "<div><strong>" . $this->l('URL adresa do cronu (plánovače úloh)') . "</strong>:</div>";
            $this->_html .= "<div>" . $this->MakeURL($cron_url) . "</div>";
            $this->_html .= '</div></div></div>'; //FRAME END

        } elseif ($this->name == 'shaim_pricemania' && isset($pricemania_html)) {

            $cron_url = $this->full_url . 'modules/' . $this->name . '/' . $this->export_file . "?pricemania&open&close&token=$export_token" . $add_id_shop_for_url;

            $this->_html .= '<form class="form-horizontal" action="' . $_SERVER['REQUEST_URI'] . '" method="post">';
            $this->_html .= '<div class="row"><div class="col-lg-12"><div class="panel"><div class="panel-heading"><i class="icon-list"></i> ' . $this->l('CRON URL') . '</div>'; // FRAME
            $this->_html .= "<div><strong>" . $this->l('URL adresa do cronu (plánovače úloh)') . "</strong> (pricemania.cz + pricemania.sk):</div>";
            $this->_html .= "<div>" . $this->MakeURL($cron_url) . "</div>";
            $this->_html .= '<div class="form-group">';
            $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Povolené kategorie a párování kategorií') . '</label>';
            $this->_html .= '<div class="col-lg-9">' . $pricemania_html . '</div>';
            $this->_html .= '</div>';

            $this->_html .= '<div class="form-group">';
            $this->_html .= '<label class="control-label col-lg-3">' . $this->l('ID produktů, které nechcete exportovat do XML') . '</label>';
            $this->_html .= '<div class="col-lg-9"><textarea name="blacklist_product" rows="10">' . Configuration::get($this->name . '_blacklist_product') . '</textarea></div>';
            $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
					' . $this->l('Produkty oddělte čárkou.') . '
					</div></div>';
            $this->_html .= '</div>';

            $this->_html .= '<div class="panel-footer">
            		<a href="' . $cron_url . '" class="btn btn-default" target="_blank">
							<i class="process-icon-download"></i> ' . $this->l('Vyexportovat nyní') . '
			</a>
			<button type="submit" class="filtr btn btn-default pull-right" name="pricemania_submit_text"><i class="process-icon-save"></i>' . $this->l('Uložit') . '</button></form></div>';


            $this->_html .= '</div></div></div>'; //FRAME END

        } elseif ($this->name == 'shaim_dostupnost') {

            $this->_html .= '<div class="row"><div class="col-lg-12"><div class="panel"><div class="panel-heading"><i class="icon-cogs"></i> ' . $this->l('Nastavení exportu') . '</div>'; // FRAME
            $this->_html .= '<form class="form-horizontal" action="' . $_SERVER['REQUEST_URI'] . '" method="post">';
            $this->_html .= '<div class="form-group">';
            $this->_html .= '<label class="control-label col-lg-3">' . $this->l('DEPOT_ID (ID provozovny)') . '</label>';
            $this->_html .= '<div class="col-lg-9"><input type="text" name="depot_ids_heureka" value="' . Configuration::get($this->name . '_depot_ids_heureka') . '" ></div>';

            $cron_url = $this->full_url . 'modules/' . $this->name . '/' . $this->export_file . "?heureka_dostupnost&open&close&token=$export_token" . $add_id_shop_for_url;

            $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
					' . $this->l('Více ID oddělujte čárkou') . '
					</div></div>';
            $this->_html .= '</div>';


            $this->_html .= '<div class="form-group">';
            $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Počet dní od objednávky do doručení (delivery_time)') . '</label>';
            $this->_html .= '<div class="col-lg-9"><input type="text" name="dost_day" value="' . Configuration::get($this->name . '_dost_day') . '"></div>';
            $this->_html .= '</div>';

            $this->_html .= '<div>';
            $this->_html .= '<div class="form-group">';
            $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Čas poslední objednávky pro dodržení doby doručení (delivery_time)') . '</label>';
            $this->_html .= '<div class="col-lg-9"><div class="input-group" style="width: 200px;"><input type="text" name="dost_time" value="' . Configuration::get($this->name . '_dost_time') . '" ><span class="input-group-addon">' . $this->l('(HH:mm)') . '</span></div></div>';
            $this->_html .= '</div>';


            $this->_html .= '<div class="form-group">';
            $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Počet dní od objednávky do doručení (pickup_time)') . '</label>';
            $this->_html .= '<div class="col-lg-9"><input type="text" name="pick_day" value="' . Configuration::get($this->name . '_pick_day') . '"></div>';
            $this->_html .= '</div>';

            $this->_html .= '<div>';
            $this->_html .= '<div class="form-group">';
            $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Čas poslední objednávky pro dodržení doby doručení (pickup_time)') . '</label>';
            $this->_html .= '<div class="col-lg-9"><div class="input-group" style="width: 200px;"><input type="text" name="pick_time" value="' . Configuration::get($this->name . '_pick_time') . '" ><span class="input-group-addon">' . $this->l('(HH:mm)') . '</span></div></div>';
            $this->_html .= '</div>';

            $this->_html .= '<div class="panel-footer">
            		<a href="' . $cron_url . '" class="btn btn-default" target="_blank">
							<i class="process-icon-download"></i> ' . $this->l('Vyexportovat nyní') . '
			</a>
			<button type="submit" class="filtr btn btn-default pull-right" name="submit_text"><i class="process-icon-save"></i>' . $this->l('Uložit') . '</button></form></div>';

            $this->_html .= '</div></div></div>'; //FRAME END


            $this->_html .= '<div class="row"><div class="col-lg-12"><div class="panel"><div class="panel-heading"><i class="icon-list"></i> ' . $this->l('CRON URL') . '</div>'; // FRAME
            $this->_html .= "<div><strong>" . $this->l('URL adresa do cronu (plánovače úloh)') . "</strong> (heureka dostupnostní feed):</div>";
            $this->_html .= "<div>" . $this->MakeURL($cron_url) . "</div>";
            $this->_html .= '</div></div></div>'; //FRAME END

        } elseif ($this->name == 'shaim_glami') {


            $this->_html .= '<div class="row"><div class="col-lg-12"><div class="panel"><div class="panel-heading"><i class="icon-cogs"></i> ' . $this->l('Nastavení exportu') . '</div>'; // FRAME

            $this->_html .= '<form class="form-horizontal" action="' . $_SERVER['REQUEST_URI'] . '" method="post">';


            $this->_html .= '<div class="form-group">';
            $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Exportovat pouze položky skladem') . '</label>';

            $this->_html .= '<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
										<input name="only_stock" id="only_stock_on" value="1"' . ((Configuration::get($this->name . '_only_stock') == 1) ? ' checked="checked"' : '') . ' type="radio">
										<label for="only_stock_on">' . $this->l('ANO') . '</label>
										<input name="only_stock" id="only_stock_off" value="0"' . ((Configuration::get($this->name . '_only_stock') == 0) ? ' checked="checked"' : '') . ' type="radio">
										<label for="only_stock_off">' . $this->l('NE') . '</label>
										<a class="slide-button btn"></a>
									</span></div>';

            $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
				' . $this->l('Funguje dobře pouze v případě, že máte zapnutý sklad a že chcete exportovat pouze položky, které mají sklad vyšší jak 0.') . '
				</div></div>';
            $this->_html .= '</div>';


            $this->_html .= '<div class="form-group">';
            $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Používat kombinace produktů') . '</label>';

            $this->_html .= '<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
										<input name="combinations" id="combinations_on" value="1"' . ((Configuration::get($this->name . '_combinations') == 1) ? ' checked="checked"' : '') . ' type="radio">
										<label for="combinations_on">' . $this->l('ANO') . '</label>
										<input name="combinations" id="combinations_off" value="0"' . ((Configuration::get($this->name . '_combinations') == 0) ? ' checked="checked"' : '') . ' type="radio">
										<label for="combinations_off">' . $this->l('NE') . '</label>
										<a class="slide-button btn"></a>
									</span></div>';

            $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
				' . $this->l('v 99% případů mělo zůstat zapnuté.') . '
				</div></div>';
            $this->_html .= '</div>';

            /*
            $this->_html .= '<div class="form-group">';
            $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Povolené kategorie a párování kategorií') . '</label>';
            $this->_html .= '<div class="col-lg-9">' . $glami_html . '</div>';
            $this->_html .= '</div>';
            */


            $this->_html .= '<div class="form-group">';
            $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Doba dodání u produktu, který je skladem') . '</label>';
            $this->_html .= '<div class="col-lg-9"><input type="text" name="days_stock" value="' . (int)Configuration::get($this->name . '_days_stock') . '"></div>';
            $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
				' . $this->l('Počet dní, které uvádět v XML v případě, že produkt je skladem (default 0)') . '
				</div></div>';
            $this->_html .= '</div>';

            $this->_html .= '<div class="form-group">';
            $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Doba dodání u produktu, který není skladem') . '</label>';
            $this->_html .= '<div class="col-lg-9"><input type="text" name="days_nostock" value="' . (int)Configuration::get($this->name . '_days_nostock') . '"></div>';
            $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
				' . $this->l('Počet dní, které uvádět v XML v případě, že produkt není skladem (default 7)') . '
				</div></div>';
            $this->_html .= '</div>';

            $dopravci = array(
                /* CZ */
                'CZ_CESKA_POSTA', 'CZ_CESKA_POSTA_NA_POSTU', 'CZ_CESKA_POSTA_DOPORUCENA_ZASILKA', 'CZ_CSAD_LOGISTIK_OSTRAVA', 'CZ_DPD', 'CZ_DHL', 'CZ_DSV', 'CZ_FOFR', 'CZ_GEBRUDER_WEISS', 'CZ_GEIS', 'CZ_GLS', 'CZ_HDS', 'CZ_HEUREKAPOINT', 'CZ_INTIME', 'CZ_PPL', 'CZ_SEEGMULLER', 'CZ_TNT', 'CZ_TOPTRANS', 'CZ_UPS', 'CZ_FEDEX', 'CZ_RABEN_LOGISTICS', 'CZ_VLASTNI_PREPRAVA', 'CZ_ZASILKOVNA', 'CZ_DPD_PICKUP', 'CZ_ULOZENKA',
                // ZBOZI SPECIFIC
                // 'CZ_ZBOZI_GEIS_POINT', 'CZ_ZBOZI_GLS_PARCELSHOP', 'CZ_ZBOZI_PPL_PARCELSHOP', 'CZ_ZBOZI_TOPTRANS_DEPO', 'CZ_ZBOZI_DB_SCHENKER', 'CZ_ZBOZI_MESSENGER', 'CZ_ZBOZI_RHENUS', 'CZ_ZBOZI_VLASTNI_VYDEJNI_MISTA',
                'CZ_GEIS_POINT', 'CZ_GLS_PARCELSHOP', 'CZ_PPL_PARCELSHOP', 'CZ_TOPTRANS_DEPO', 'CZ_DB_SCHENKER', 'CZ_MESSENGER', 'CZ_RHENUS', 'CZ_VLASTNI_VYDEJNI_MISTA',
                /* SK */
                'SK_SLOVENSKA_POSTA', 'SK_SLOVENSKA_POSTA_BALIK_NA_POSTU', 'SK_DPD', 'SK_DHL', 'SK_DSV', 'SK_EXPRES_KURIER', 'SK_GEBRUDER_WEISS', 'SK_GEIS', 'SK_GLS', 'SK_HDS', 'SK_INTIME', 'SK_PPL', 'SK_REMAX', 'SK_TNT', 'SK_TOPTRANS', 'SK_UPS', 'SK_FEDEX', 'SK_RABEN_LOGISTICS', 'SK_VLASTNI_PREPRAVA', 'SK_ZASILKOVNA',);
            asort($dopravci);
            $carriers = unserialize(Configuration::get($this->name . '_carriers'));
            for ($i = 0; $i <= 6; $i++) {
                $this->_html .= '<div class="form-group">';
                $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Dopravci, kteří se propíší do XML #') . ($i + 1) . '</label>';
                $this->_html .= '<div class="col-lg-9">
                    <select class="select_bigger" name="carriers[' . $i . '][name]">';
                $this->_html .= '<option value="0">---</option>';

                foreach ($dopravci as $dopravce) {
                    // $dopravce = str_replace('CZ_ZBOZI_', 'CZ_', $dopravce);
                    $selected = (isset($carriers[$i]['name']) && $carriers[$i]['name'] == $dopravce) ? ' selected="selected"' : '';
                    $this->_html .= '<option value="' . $dopravce . '"' . $selected . '>' . $dopravce . '</option>';
                }
                $this->_html .= '</select>
                    <input type="text" placeholder="' . $this->l('Cena bez dobírky') . '" name="carriers[' . $i . '][price]" value="' . (isset($carriers[$i]['price']) ? $carriers[$i]['price'] : '') . '">
                    <input type="text" placeholder="' . $this->l('Cena včetně dobírky') . '" name="carriers[' . $i . '][price_cod]" value="' . (isset($carriers[$i]['price_cod']) ? $carriers[$i]['price_cod'] : '') . '">
                    <input type="text" placeholder="' . $this->l('Zdarma od') . '" name="carriers[' . $i . '][free]" value="' . (isset($carriers[$i]['free']) ? $carriers[$i]['free'] : '') . '">
                    </div>';
                $this->_html .= '</div>';
            }
            $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
					' . $this->l('Zde zvolené dopravy a ceny za ně se propíší do XML feedu u všech produktů. (tag DELIVERY).') . '<br />' . $this->l('Pokud dobírku nechcete v XML uvádět, vepište do sloupce s cenou za dobírku hodnotu -1 (mínus jedna)') . '
					</div></div>';


            $this->_html .= '<div class="form-group">';
            $this->_html .= '<label class="control-label col-lg-3">' . $this->l('ID produktů, které nechcete exportovat do XML') . '</label>';
            $this->_html .= '<div class="col-lg-9"><textarea name="blacklist_product" rows="10">' . Configuration::get($this->name . '_blacklist_product') . '</textarea></div>';
            $cron_url = $this->full_url . 'modules/' . $this->name . '/' . $this->export_file . "?glami&open&close&token=$export_token" . $add_id_shop_for_url;
            $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
					' . $this->l('Produkty oddělte čárkou.') . '
					</div></div>';
            $this->_html .= '</div>';

            $this->_html .= '<div class="panel-footer">
            		<a href="' . $cron_url . '" class="btn btn-default" target="_blank">
							<i class="process-icon-download"></i> ' . $this->l('Vyexportovat nyní') . '
			</a>
			<button type="submit" class="filtr btn btn-default pull-right" name="glami_submit_text"><i class="process-icon-save"></i>' . $this->l('Uložit') . '</button></form></div>';
            $this->_html .= '</div></div></div>'; //FRAME END


            $this->_html .= '<div class="row"><div class="col-lg-12"><div class="panel"><div class="panel-heading"><i class="icon-list"></i> ' . $this->l('CRON URL') . '</div>'; // FRAME
            $this->_html .= "<div><strong>" . $this->l('URL adresa do cronu (plánovače úloh)') . "</strong> (glami.cz + glami.sk):</div>";
            $this->_html .= "<div>" . $this->MakeURL($cron_url) . "</div>";
            $this->_html .= '</div></div></div>'; //FRAME END
        }


        // FEEEDS START
        // FEEEDS START
        // FEEEDS START
        // FEEEDS START
        // FEEEDS START


        $add_id_shop = '';
        if (Shop::isFeatureActive()) {
            $add_id_shop = (int)$this->context->shop->id;
        }


        $this->_html .= '<div class="row"><div class="col-lg-12">
		    <div class="panel"><div class="panel-heading"><i class="icon-cloud"></i> ' . $this->l('Výsledné feedy') . '</div>'; // FRAME
        $this->_html .= '<div class="alert alert-info">' . $this->l('Zobrazují se pouze Vaše aktivní feedy. Nejdříve je potřeba spustit cron URL, aby došlo k vygenerování feedů.') . '</div>';
        if ($this->name == 'shaim_glami') {
            $this->_html .= "<br /><div>Glami.cz (.xml):</div>";
            $this->_html .= "<div>" . $this->MakeURL($this->full_url . $this->export_folder . "/glami_cz" . $add_id_shop . ".xml") . "</div>";
            $this->_html .= "<br /><div>Glami.sk (.xml):</div>";
            $this->_html .= "<div>" . $this->MakeURL($this->full_url . $this->export_folder . "/glami_sk" . $add_id_shop . ".xml") . "</div>";


        } elseif ($this->name == 'shaim_pricemania') {
            $this->_html .= "<br /><div>Pricemania.cz (.xml):</div>";
            $this->_html .= "<div>" . $this->MakeURL($this->full_url . $this->export_folder . "/pricemania_cz" . $add_id_shop . ".xml") . "</div>";
            $this->_html .= "<br /><div>Pricemania.sk (.xml):</div>";
            $this->_html .= "<div>" . $this->MakeURL($this->full_url . $this->export_folder . "/pricemania_sk" . $add_id_shop . ".xml") . "</div>";
        } elseif ($this->name == 'shaim_dostupnost') {
            $this->_html .= "<br /><div>Heureka dostupnostní feed (.xml):</div>";
            $this->_html .= "<div>" . $this->MakeURL($this->full_url . $this->export_folder . "/heureka_dostupnost" . $add_id_shop . ".xml") . "</div>";
        }
        // else {

        if (isset($aktivni_sluzby)) {
            if (($this->real_name == 'zbozi_cz' || $this->real_name == 'all') && $aktivni_sluzby['active_zbozi_cz'] == 1) {
                $this->_html .= "<br /><div>Zboží.cz (.xml):</div>";
                $this->_html .= "<div>" . $this->MakeURL($this->full_url . $this->export_folder . "/zbozi_cz" . $add_id_shop . ".xml") . "</div>";
            }
            if (($this->real_name == 'heureka_cz' || $this->real_name == 'all') && $aktivni_sluzby['active_heureka_cz'] == 1) {
                $this->_html .= "<br /><div>Heureka.cz (.xml):</div>";
                $this->_html .= "<div>" . $this->MakeURL($this->full_url . $this->export_folder . "/heureka_cz" . $add_id_shop . ".xml") . "</div>";
            }
            if (($this->real_name == 'heureka_sk' || $this->real_name == 'all') && $aktivni_sluzby['active_heureka_sk'] == 1) {
                $this->_html .= "<br /><div>Heureka.sk (.xml):</div>";
                $this->_html .= "<div>" . $this->MakeURL($this->full_url . $this->export_folder . "/heureka_sk" . $add_id_shop . ".xml") . "</div>";
            }
            if (($this->real_name == 'heureka_cz' || $this->real_name == 'all') && $aktivni_sluzby['active_heureka_dostupnost'] == 1) {
                $this->_html .= "<br /><div>Heureka.cz dostupnostní XML (.xml):</div>";
                $this->_html .= "<div>" . $this->MakeURL($this->full_url . $this->export_folder . "/heureka_dostupnost" . $add_id_shop . ".xml") . "</div>";
            }
            if (($this->real_name == 'google_com' || $this->real_name == 'all') && $aktivni_sluzby['active_google_com'] == 1) {
                $this->_html .= "<br /><div>Google.com (.xml):</div>";
                $this->_html .= "<div>" . $this->MakeURL($this->full_url . $this->export_folder . "/google_com" . $add_id_shop . ".xml") . "</div>";
            }
            if (($this->real_name == 'facebook_com' || $this->real_name == 'all') && $aktivni_sluzby['active_facebook_com'] == 1) {
                $this->_html .= "<br /><div>Facebook.com (.xml):</div>";
                $this->_html .= "<div>" . $this->MakeURL($this->full_url . $this->export_folder . "/facebook_com" . $add_id_shop . ".xml") . "</div>";
            }
        }
        $this->_html .= '</div></div></div>'; //FRAME END


        // FEEDS END
        // FEEDS END
        // FEEDS END

        // PAROVANI BEGIN
        // PAROVANI BEGIN
        // PAROVANI BEGIN

        $this->_html .= '<div class="row" id="pair_row"><div class="col-lg-12">
			<div class="panel"><div class="panel-heading"><i class="icon-resize-small"></i> ' . $this->l('Párování kategorií') . '<div class="panel-heading-action">
						<a id="CompleteFormAndSend" class="save_heureka btn btn-default CompleteFormAndSend" name="save_heureka"><i class="icon-save"></i> ' . $this->l('Uložit napárované kategorie') . '</a>
					</div></div>'; // FRAME

        if ($this->name == 'shaim_glami') {
            $this->_html .= '<div class="alert alert-info">' . $this->l('Pokud nenapárujete nějaké kategorie, produkty zařazené v těchto kategoriích se neexportují do XML. Napárujte tedy pouze ty kategorie u kterých chcete, aby se příslušené produkty propsaly do XML. ') . '</div>';
            // $this->_html .= '<div class="alert alert-info">' . $this->l('Pokud nenapárujete nějaké kategorie, produkty zařazené v těchto kategoriích se exportují do XML se stejnou kategorií jako máte ve Vašem shopu, doporučujeme tedy napárovat všechny kategorie.') . '</div>';
        }
        if ($this->name == 'shaim_glami' || $this->name == 'shaim_export') {
            $this->_html .= '<div class="alert alert-info">' . $this->l('Určující pro párování kategorií je defaultní (výchozí) kategorie produktů. ') . '</div>';
        }

        $this->_html .= '<div><!-- test comment1 -->';
        $heureka_or_google_or_zbozi_or_glami_enabled = false;
        if (isset($this->local['html_heureka']) && isset($this->h['cz']) && isset($this->h['sk']) && $heureka_enable_pair == 1 && ($this->real_name == 'heureka_sk' || $this->real_name == 'heureka_cz' || $this->real_name == 'all')) {
            $heureka_or_google_or_zbozi_or_glami_enabled = true;
            $this->_html .= '<div id="heureka_cz_parovani"><div><h4 class="center">' . $this->l('Heureka.cz - párování kategorií') . '</h4></div>';
            $this->local['html_heureka'] = '<div><a id="odparovat_heureka_cz" style="display: none; cursor: pointer;">' . $this->l('Odpárovat') . '</a>&nbsp;</div>' . $this->local['html_heureka'];
            $this->_html .= $this->local['html_heureka'];
            $this->_html .= '<div id="rightBottom_heureka_cz">' . $this->l('nepárováno') . '</div>';
            $this->_html .= '<div class="center">' . $this->l('Vyhledávání v kategoriích Heureka.cz:') . ' <input type="text" id="search_text_heureka_cz">' . '</div>';
            $this->_html .= $this->h['cz'] . '</div>';
            $this->_html .= '<div id="heureka_sk_parovani"><div class="clear_both heureka_sk_padding"><h4 class="center">' . $this->l('Heureka.sk - párování kategorií') . '</h4></div>';
            // $this->_html .= str_replace('LeftClickHeurekaCZ', 'LeftClickHeurekaSK', str_replace('[cz]', '[sk]', str_replace('left_heureka_cz', 'left_heureka_sk', str_replace('-cz-', '-sk-', $this->local['html_heureka']))));
            $this->_html .= strtr($this->local['html_heureka'], array(
                'LeftClickHeurekaCZ' => 'LeftClickHeurekaSK',
                '[cz]' => '[sk]',
                'left_heureka_cz' => 'left_heureka_sk',
                '-cz-' => '-sk-',
                'odparovat_heureka_cz' => 'odparovat_heureka_sk',
            ));
            $this->_html .= '<div id="rightBottom_heureka_sk">' . $this->l('nepárováno') . '</div>';
            $this->_html .= '<div class="center">' . $this->l('Vyhledávání v kategoriích Heureka.sk:') . ' <input type="text" id="search_text_heureka_sk">' . '</div>';
            $this->_html .= $this->h['sk'] . '</div>';
        }
        $this->_html .= '<div class="clear_both"></div><br /><br />';
        if (isset($this->local['html_zbozi']) && isset($this->zbozi) && $zbozi_enable_pair == 1 && ($this->real_name == 'zbozi_cz' || $this->real_name == 'all')) {
            $heureka_or_google_or_zbozi_or_glami_enabled = true;
            $this->_html .= '<div><h4 class="center">' . $this->l('Zboží.cz - párování kategorií') . '</h4></div>';
            $this->_html .= '<div><a id="odparovat_zbozi" style="display: none; cursor: pointer;">' . $this->l('Odpárovat') . '</a>&nbsp;</div>';
            $this->_html .= $this->local['html_zbozi'];
            $this->_html .= '<div id="rightBottom_zbozi">' . $this->l('nepárováno') . '</div>';
            $this->_html .= '<div class="center">' . $this->l('Vyhledávání v kategoriích Zbozi:') . ' <input type="text" id="search_text_zbozi">' . '</div>';
            $this->_html .= $this->zbozi;
            $this->_html .= '<div class="clear_both"></div><br /><br />';
        }

        if (isset($this->local['html_google']) && isset($this->g) && $google_enable_pair == 1 && ($this->real_name == 'google_com' || $this->real_name == 'facebook_com' || $this->real_name == 'all')) {
            $heureka_or_google_or_zbozi_or_glami_enabled = true;
            $this->_html .= '<div><h4 class="center">' . $this->l('Google.com/Facebook.com - párování kategorií') . '</h4></div>';
            $this->_html .= '<div><a id="odparovat_google" style="display: none; cursor: pointer;">' . $this->l('Odpárovat') . '</a>&nbsp;</div>';
            $this->_html .= $this->local['html_google'];
            $this->_html .= '<div id="rightBottom_google">' . $this->l('nepárováno') . '</div>';
            $this->_html .= '<div class="center">' . $this->l('Vyhledávání v kategoriích Google.com/Facebook.com:') . ' <input type="text" id="search_text_google_com">' . '</div>';
            $this->_html .= $this->g;
        }

        if (isset($this->local['html_glami']) && isset($this->glami['cz']) && isset($this->glami['sk']) & ($this->real_name == 'glami_sk' || $this->real_name == 'glami_cz' || $this->real_name == 'all')) {
            $heureka_or_google_or_zbozi_or_glami_enabled = true;
            $this->_html .= '<div id="glami_cz_parovani"><div><h4 class="center">' . $this->l('Glami.cz - párování kategorií') . '</h4></div>';
            $this->local['html_glami'] = '<div><a id="odparovat_glami_cz" style="display: none; cursor: pointer;">' . $this->l('Odpárovat') . '</a>&nbsp;</div>' . $this->local['html_glami'];
            $this->_html .= $this->local['html_glami'];
            $this->_html .= '<div id="rightBottom_glami_cz">' . $this->l('nepárováno') . '</div>';
            $this->_html .= '<div class="center">' . $this->l('Vyhledávání v kategoriích Glami.cz:') . ' <input type="text" id="search_text_glami_cz">' . '</div>';
            $this->_html .= $this->glami['cz'] . '</div>';
            $this->_html .= '<div id="glami_sk_parovani"><div class="clear_both glami_sk_padding"><h4 class="center">' . $this->l('Glami.sk - párování kategorií') . '</h4></div>';
            // $this->_html .= str_replace('LeftClickGlamiCZ', 'LeftClickGlamiSK', str_replace('[cz]', '[sk]', str_replace('left_glami_cz', 'left_glami_sk', str_replace('-cz-', '-sk-', $this->local['html_glami']))));
            $this->_html .= strtr($this->local['html_glami'], array(
                'LeftClickGlamiCZ' => 'LeftClickGlamiSK',
                '[cz]' => '[sk]',
                'left_glami_cz' => 'left_glami_sk',
                '-cz-' => '-sk-',
                'odparovat_glami_cz' => 'odparovat_glami_sk',
            ));
            $this->_html .= '<div id="rightBottom_glami_sk">' . $this->l('nepárováno') . '</div>';
            $this->_html .= '<div class="center">' . $this->l('Vyhledávání v kategoriích Glami.sk:') . ' <input type="text" id="search_text_glami_sk">' . '</div>';
            $this->_html .= $this->glami['sk'] . '</div>';
        }


        if (!$heureka_or_google_or_zbozi_or_glami_enabled) {

            $this->_html .= '<style>
                #pair_row{display:none;}
                </style>';
        } else {

            $this->_html .= '<div class="clear_both"></div><div class="panel-footer"><button id="CompleteFormAndSend" class="save_heureka btn btn-default pull-right CompleteFormAndSend" name="save_heureka"><i class="process-icon-save"></i> ' . $this->l('Uložit napárované kategorie') . '</button></div>';
            $this->_html .= '<form class="form-horizontal" action="" method="post" id="generatedForm"></form>';

            // $this->_html .= '<pre>'.print_R($paired_cats, true).'</pre>';
            $script = '
                $( "#odparovat_heureka_cz" ).click(function() {
  odparovat_id = $("#left_heureka_cz .active").attr("id");
  $("#right_heureka_cz .active").removeClass("active").addClass("unactive");
  document.getElementById("rightBottom_heureka_cz").innerHTML = "nepárováno";
    $("#" + odparovat_id).removeAttr("style");
  $(this).hide();
  var LeftActiveHeurekaCZ = FunctionLeftActiveHeurekaCZ();
  poleleftSpan_heurekaCZ[LeftActiveHeurekaCZ] = undefined;

});

$( "#odparovat_heureka_sk" ).click(function() {
  odparovat_id = $("#left_heureka_sk .active").attr("id");
  $("#right_heureka_sk .active").removeClass("active").addClass("unactive");
  document.getElementById("rightBottom_heureka_sk").innerHTML = "nepárováno";
  $("#" + odparovat_id).removeAttr("style");
  $(this).hide();
  var LeftActiveHeurekaSK = FunctionLeftActiveHeurekaSK();
  poleleftSpan_heurekaSK[LeftActiveHeurekaSK] = undefined;

});

                $( "#odparovat_glami_cz" ).click(function() {
  odparovat_id = $("#left_glami_cz .active").attr("id");
  $("#right_glami_cz .active").removeClass("active").addClass("unactive");
  document.getElementById("rightBottom_glami_cz").innerHTML = "nepárováno";
    $("#" + odparovat_id).removeAttr("style");
  $(this).hide();
  var LeftActiveGlamiCZ = FunctionLeftActiveGlamiCZ();
  poleleftSpan_glamiCZ[LeftActiveGlamiCZ] = undefined;

});

$( "#odparovat_glami_sk" ).click(function() {
  odparovat_id = $("#left_glami_sk .active").attr("id");
  $("#right_glami_sk .active").removeClass("active").addClass("unactive");
  document.getElementById("rightBottom_glami_sk").innerHTML = "nepárováno";
  $("#" + odparovat_id).removeAttr("style");
  $(this).hide();
  var LeftActiveGlamiSK = FunctionLeftActiveGlamiSK();
  poleleftSpan_glamiSK[LeftActiveGlamiSK] = undefined;

});

$( "#odparovat_google" ).click(function() {
  odparovat_id = $("#left_google .active").attr("id");
  $("#right_google .active").removeClass("active").addClass("unactive");
  document.getElementById("rightBottom_google").innerHTML = "nepárováno";
  $("#" + odparovat_id).removeAttr("style");
  $(this).hide();
        var leftActiveGoogle = FunctionLeftActiveGoogle();
           poleleftSpan_google[leftActiveGoogle] = undefined;

});

$( "#odparovat_zbozi" ).click(function() {
  odparovat_id = $("#left_zbozi .active").attr("id");
  $("#right_zbozi .active").removeClass("active").addClass("unactive");
  document.getElementById("rightBottom_zbozi").innerHTML = "nepárováno";
  $("#" + odparovat_id).removeAttr("style");
  $(this).hide();
        var leftActiveZbozi = FunctionLeftActiveZbozi();
           poleleftSpan_zbozi[leftActiveZbozi] = undefined;

});

$(function() {
$( window ).load(function() {
  $("#odparovat_heureka_cz").hide();
  $("#odparovat_heureka_sk").hide();
  $("#odparovat_glami_cz").hide();
  $("#odparovat_glami_sk").hide();
  $("#odparovat_google").hide();
    $("#odparovat_zbozi").hide();
});
});
';


            $active_category = (int)Configuration::get($this->name . '_active_category');
            $add_active = '';
            if ($active_category === 1) {
                $add_active = ' && c.active = 1';
            } elseif ($active_category === 0) {
                $add_active = ' && c.active = 0';
            }

            if ($heureka_enable_pair) {
                $script .= 'var poleleftSpan_heurekaCZ = new Array(document.getElementById("left_heureka_cz").getElementsByTagName("span").length);';


                // $tmp = Db::getInstance()->ExecuteS('SELECT a.id_category, a.id_parent, b.name FROM ' . _DB_PREFIX_ . 'category as a INNER JOIN ' . _DB_PREFIX_ . 'category_lang as b ON (a.id_category = b.id_category) WHERE b.id_lang = ' . Configuration::get('PS_LANG_DEFAULT') . ' && a.id_parent IN (SELECT id_category FROM ' . _DB_PREFIX_ . 'category) && a.id_category >= 2' . $add_active . ';');

                $paired_cats = Db::getInstance()->ExecuteS("SELECT local_id, heureka_full_name FROM " . _DB_PREFIX_ . "shaim_heureka
                    INNER JOIN " . _DB_PREFIX_ . "category as c ON (local_id = c.id_category$add_active) && c.id_parent IN (SELECT id_category FROM " . _DB_PREFIX_ . "category)
                    WHERE lang = 'cz';");
                if ($paired_cats) {
                    $last_span = false;
                    foreach ($paired_cats as $value) {
                        if ($value["heureka_full_name"] == '') {
                            continue;
                        }
                        $value["heureka_full_name"] = html_entity_decode(addslashes($value["heureka_full_name"]));
                        $script .= '

                if(document.getElementById("cz-' . $value["heureka_full_name"] . '") != null){
 
                LeftClickHeurekaCZ(document.getElementById("span-heureka-cz-' . $value["local_id"] . '"));
                    RightClickHeurekaCZ(document.getElementById("cz-' . $value["heureka_full_name"] . '"));
                    }';
                        $last_span = "span-heureka-cz-" . $value["local_id"];
                    }
                    if ($last_span) {
                        $script .= 'document.getElementById("' . $last_span . '").className = "unactive";';
                    }
                }
                $script .= 'var poleleftSpan_heurekaSK = new Array(document.getElementById("left_heureka_sk").getElementsByTagName("span").length);';
                $paired_cats = Db::getInstance()->ExecuteS("SELECT local_id, heureka_full_name FROM " . _DB_PREFIX_ . "shaim_heureka
                    INNER JOIN " . _DB_PREFIX_ . "category as c ON (local_id = c.id_category$add_active) && c.id_parent IN (SELECT id_category FROM " . _DB_PREFIX_ . "category)
                    WHERE lang = 'sk';");
                if ($paired_cats) {
                    $last_span = false;
                    foreach ($paired_cats as $value) {
                        if ($value["heureka_full_name"] == '') {
                            continue;
                        }
                        $value["heureka_full_name"] = html_entity_decode(addslashes($value["heureka_full_name"]));
                        $script .= '

                if(document.getElementById("sk-' . $value["heureka_full_name"] . '") != null){
                LeftClickHeurekaSK(document.getElementById("span-heureka-sk-' . $value["local_id"] . '"));
                    RightClickHeurekaSK(document.getElementById("sk-' . $value["heureka_full_name"] . '"));
                    }';
                        $last_span = "span-heureka-sk-" . $value["local_id"];
                    }
                    if ($last_span) {
                        $script .= 'document.getElementById("' . $last_span . '").className = "unactive";';
                    }
                }
            }
            if ($glami_enable_pair) {
                $script .= 'var poleleftSpan_glamiCZ = new Array(document.getElementById("left_glami_cz").getElementsByTagName("span").length);';
                $paired_cats = Db::getInstance()->ExecuteS("SELECT local_id, glami_category_name FROM " . _DB_PREFIX_ . "shaim_glami
                    INNER JOIN " . _DB_PREFIX_ . "category as c ON (local_id = c.id_category$add_active) && c.id_parent IN (SELECT id_category FROM " . _DB_PREFIX_ . "category)
                    WHERE lang = 'cz';");
                if ($paired_cats) {
                    $last_span = false;
                    foreach ($paired_cats as $value) {
                        if ($value["glami_category_name"] == '') {
                            continue;
                        }
                        $value["glami_category_name"] = html_entity_decode(addslashes($value["glami_category_name"]));
                        $script .= '

                if(document.getElementById("cz-' . $value["glami_category_name"] . '") != null){

                LeftClickGlamiCZ(document.getElementById("span-glami-cz-' . $value["local_id"] . '"));
                    RightClickGlamiCZ(document.getElementById("cz-' . $value["glami_category_name"] . '"));
                    }';
                        $last_span = "span-glami-cz-" . $value["local_id"];
                    }
                    if ($last_span) {
                        $script .= 'document.getElementById("' . $last_span . '").className = "unactive";';
                    }
                }
                $script .= 'var poleleftSpan_glamiSK = new Array(document.getElementById("left_glami_sk").getElementsByTagName("span").length);';
                $paired_cats = Db::getInstance()->ExecuteS("SELECT local_id, glami_category_name FROM " . _DB_PREFIX_ . "shaim_glami
                    INNER JOIN " . _DB_PREFIX_ . "category as c ON (local_id = c.id_category$add_active) && c.id_parent IN (SELECT id_category FROM " . _DB_PREFIX_ . "category)
                    WHERE lang = 'sk';");
                if ($paired_cats) {
                    $last_span = false;
                    foreach ($paired_cats as $value) {
                        if ($value["glami_category_name"] == '') {
                            continue;
                        }
                        $value["glami_category_name"] = html_entity_decode(addslashes($value["glami_category_name"]));
                        $script .= '

                if(document.getElementById("sk-' . $value["glami_category_name"] . '") != null){
                LeftClickGlamiSK(document.getElementById("span-glami-sk-' . $value["local_id"] . '"));
                    RightClickGlamiSK(document.getElementById("sk-' . $value["glami_category_name"] . '"));
                    }';
                        $last_span = "span-glami-sk-" . $value["local_id"];
                    }
                    if ($last_span) {
                        $script .= 'document.getElementById("' . $last_span . '").className = "unactive";';
                    }
                }
            }
            if ($google_enable_pair) {
                $script .= 'var poleleftSpan_google = new Array(document.getElementById("left_google").getElementsByTagName("span").length);';
                $paired_cats = Db::getInstance()->ExecuteS("SELECT local_id, google_full_name FROM " . _DB_PREFIX_ . "shaim_google
                    INNER JOIN " . _DB_PREFIX_ . "category as c ON (local_id = c.id_category$add_active) && c.id_parent IN (SELECT id_category FROM " . _DB_PREFIX_ . "category)
                    ;");
                if ($paired_cats) {
                    $last_span = false;
                    foreach ($paired_cats as $value) {
                        if ($value["google_full_name"] == '') {
                            continue;
                        }
                        $value["google_full_name"] = html_entity_decode(addslashes($value["google_full_name"]));
                        // $value["google_full_name"] = 'Chovatelství';
                        $script .= '

                if(document.getElementById("' . $value["google_full_name"] . '") != null){
                LeftClickGoogle(document.getElementById("span-google-' . $value["local_id"] . '"));
                    RightClickGoogle(document.getElementById("' . $value["google_full_name"] . '"));
                    }
                    ';
                        $last_span = "span-google-" . $value["local_id"];
                    }
                    if ($last_span) {
                        $script .= 'document.getElementById("' . $last_span . '").className = "unactive";';
                    }
                }
            }

            if ($zbozi_enable_pair) {
                $script .= 'var poleleftSpan_zbozi = new Array(document.getElementById("left_zbozi").getElementsByTagName("span").length);';
                $paired_cats = Db::getInstance()->ExecuteS("SELECT local_id, zbozi_full_name FROM " . _DB_PREFIX_ . "shaim_zbozi
                    INNER JOIN " . _DB_PREFIX_ . "category as c ON (local_id = c.id_category$add_active) && c.id_parent IN (SELECT id_category FROM " . _DB_PREFIX_ . "category)
                    ;");
                if ($paired_cats) {
                    $last_span = false;
                    foreach ($paired_cats as $value) {
                        if ($value["zbozi_full_name"] == '') {
                            continue;
                        }
                        $value["zbozi_full_name"] = html_entity_decode(addslashes($value["zbozi_full_name"]));
                        // $value["zbozi_full_name"] = 'Chovatelství';
                        $script .= '
                LeftClickZbozi(document.getElementById("span-zbozi-' . $value["local_id"] . '"));
                if(document.getElementById("' . $value["zbozi_full_name"] . '") != null)
                    RightClickZbozi(document.getElementById("' . $value["zbozi_full_name"] . '"));';
                        $last_span = "span-zbozi-" . $value["local_id"];
                    }
                    if ($last_span) {
                        $script .= 'document.getElementById("' . $last_span . '").className = "unactive";';
                    }
                }
            }

            $this->_html .= '
        <script>
$( "#search_text_heureka_cz" ).keyup(function() {
search("HCZ");
});
$( "#search_text_heureka_sk" ).keyup(function() {
search("HSK");
});

$( "#search_text_glami_cz" ).keyup(function() {
search("GCZ");
});
$( "#search_text_glami_sk" ).keyup(function() {
search("GSK");
});

$( "#search_text_google_com" ).keyup(function() {
search("GCOM");
});
$( "#search_text_zbozi" ).keyup(function() {
search("ZBOZI");
});
    function search(type) {
    if (type == "HCZ"){
    element = "#right_heureka_cz span";
    searchtext = "search_text_heureka_cz";
    }else if (type == "HSK"){
    element = "#right_heureka_sk span";
        searchtext = "search_text_heureka_sk";
    }else if (type == "GCZ"){
    element = "#right_glami_cz span";
    searchtext = "search_text_glami_cz";
    }else if (type == "GSK"){
    element = "#right_glami_sk span";
        searchtext = "search_text_glami_sk";
    }else if (type == "GCOM"){
    element = "#right_google span";
        searchtext = "search_text_google_com";
    }else if (type == "ZBOZI"){
    element = "#right_zbozi span";
        searchtext = "search_text_zbozi";
    }


// case insensitive pro contains nize (https://gist.github.com/jakebresnehan/2288330)
jQuery.expr[":"].contains = function(a, i, m) {
 return jQuery(a).text().toUpperCase()
     .indexOf(m[3].toUpperCase()) >= 0;
};


        searchtext = document.getElementById(searchtext).value;
        $(element).each(function (index) {
            if ($(this).is(":contains(" + searchtext + ")")) {
                $(this).css("display", "block");
            } else {
                $(this).css("display", "none");
            }
        });
        }
        ' . $script . '
      $( ".CompleteFormAndSend" ).click(function(event) {
       event.preventDefault();
       console.log("sending form");
       var
           form = document.getElementById("generatedForm");
       if ($("#left_heureka_cz").length != 0) {
           var leftSpan_heurekaCZ = document.getElementById("left_heureka_cz").getElementsByTagName("span");
           var rightSpan_heurekaCZ = document.getElementById("right_heureka_cz").getElementsByTagName("span");
           for (i = 0; i < leftSpan_heurekaCZ.length; i++) {
               var input = document.createElement("input");
               input.setAttribute("type", "hidden");
               input.setAttribute("name", leftSpan_heurekaCZ[i].getAttribute("name"));
               if (poleleftSpan_heurekaCZ[i] == undefined) input.setAttribute("value", "");
               else input.setAttribute("value", rightSpan_heurekaCZ[poleleftSpan_heurekaCZ[i]].innerHTML);
               form.appendChild(input);
           }
       }

       if ($("#left_heureka_sk").length != 0) {
           var
               leftSpan_heurekaSK = document.getElementById("left_heureka_sk").getElementsByTagName("span");
           var
               rightSpan_heurekaSK = document.getElementById("right_heureka_sk").getElementsByTagName("span");
           for (i = 0; i < leftSpan_heurekaSK.length; i++) {
               var
                   input = document.createElement("input");
               input.setAttribute("type", "hidden");
               input.setAttribute("name", leftSpan_heurekaSK[i].getAttribute("name"));
               if (poleleftSpan_heurekaSK[i] == undefined) input.setAttribute("value", "");
               else input.setAttribute("value", rightSpan_heurekaSK[poleleftSpan_heurekaSK[i]].innerHTML);
               form.appendChild(input);
           }
       }


            if ($("#left_glami_cz").length != 0) {
           var leftSpan_glamiCZ = document.getElementById("left_glami_cz").getElementsByTagName("span");
           var rightSpan_glamiCZ = document.getElementById("right_glami_cz").getElementsByTagName("span");
           for (i = 0; i < leftSpan_glamiCZ.length; i++) {
               var input = document.createElement("input");
               input.setAttribute("type", "hidden");
               input.setAttribute("name", leftSpan_glamiCZ[i].getAttribute("name"));
               if (poleleftSpan_glamiCZ[i] == undefined) input.setAttribute("value", "");
               else input.setAttribute("value", rightSpan_glamiCZ[poleleftSpan_glamiCZ[i]].innerHTML);
               form.appendChild(input);
           }
       }

       if ($("#left_glami_sk").length != 0) {
           var
               leftSpan_glamiSK = document.getElementById("left_glami_sk").getElementsByTagName("span");
           var
               rightSpan_glamiSK = document.getElementById("right_glami_sk").getElementsByTagName("span");
           for (i = 0; i < leftSpan_glamiSK.length; i++) {
               var
                   input = document.createElement("input");
               input.setAttribute("type", "hidden");
               input.setAttribute("name", leftSpan_glamiSK[i].getAttribute("name"));
               if (poleleftSpan_glamiSK[i] == undefined) input.setAttribute("value", "");
               else input.setAttribute("value", rightSpan_glamiSK[poleleftSpan_glamiSK[i]].innerHTML);
               form.appendChild(input);
           }
       }


       if ($("#left_google").length != 0) {
           var
               leftSpan_google = document.getElementById("left_google").getElementsByTagName("span");
           var
               rightSpan_google = document.getElementById("right_google").getElementsByTagName("span");
           for (i = 0; i < leftSpan_google.length; i++) {
               var
                   input = document.createElement("input");
               input.setAttribute("type", "hidden");
               input.setAttribute("name", leftSpan_google[i].getAttribute("name"));
               if (poleleftSpan_google[i] == undefined) input.setAttribute("value", "");
               else input.setAttribute("value", rightSpan_google[poleleftSpan_google[i]].innerHTML);
               form.appendChild(input);
           }
       }

              if ($("#left_zbozi").length != 0) {
           var
               leftSpan_zbozi = document.getElementById("left_zbozi").getElementsByTagName("span");
           var
               rightSpan_zbozi = document.getElementById("right_zbozi").getElementsByTagName("span");
           for (i = 0; i < leftSpan_zbozi.length; i++) {
               var
                   input = document.createElement("input");
               input.setAttribute("type", "hidden");
               input.setAttribute("name", leftSpan_zbozi[i].getAttribute("name"));
               if (poleleftSpan_zbozi[i] == undefined) input.setAttribute("value", "");
               else input.setAttribute("value", rightSpan_zbozi[poleleftSpan_zbozi[i]].innerHTML);
               form.appendChild(input);
           }
       }

       // form.submit();
       document.forms["generatedForm"].submit();
  });
   function LeftClickHeurekaCZ(span) {

       Oznacit(span);
       var poziceSpanu = PoziceHeureka(span);
       if (poleleftSpan_heurekaCZ[poziceSpanu] != undefined) {
           Oznacit(document.getElementById("right_heureka_cz").getElementsByTagName("span")[poleleftSpan_heurekaCZ[poziceSpanu]]);
           document.getElementById("rightBottom_heureka_cz").innerHTML = document.getElementById("right_heureka_cz").getElementsByTagName("span")[poleleftSpan_heurekaCZ[poziceSpanu]].innerHTML;
           $("#odparovat_heureka_cz").show();
       } else {
           Odznacit(document.getElementById("right_heureka_cz"));
           document.getElementById("rightBottom_heureka_cz").innerHTML = "nepárováno";
           $("#odparovat_heureka_cz").hide();
       }
   }

      function LeftClickGlamiCZ(span) {

       Oznacit(span);
       var poziceSpanu = PoziceGlami(span);
       if (poleleftSpan_glamiCZ[poziceSpanu] != undefined) {
           Oznacit(document.getElementById("right_glami_cz").getElementsByTagName("span")[poleleftSpan_glamiCZ[poziceSpanu]]);
           document.getElementById("rightBottom_glami_cz").innerHTML = document.getElementById("right_glami_cz").getElementsByTagName("span")[poleleftSpan_glamiCZ[poziceSpanu]].innerHTML;
           $("#odparovat_glami_cz").show();
       } else {
           Odznacit(document.getElementById("right_glami_cz"));
           document.getElementById("rightBottom_glami_cz").innerHTML = "nepárováno";
           $("#odparovat_glami_cz").hide();
       }
   }

   function LeftClickGoogle(span) {
       Oznacit(span);
       var poziceSpanu = PoziceGoogle(span);
       if (poleleftSpan_google[poziceSpanu] != undefined) {
           Oznacit(document.getElementById("right_google").getElementsByTagName("span")[poleleftSpan_google[poziceSpanu]]);
           document.getElementById("rightBottom_google").innerHTML = document.getElementById("right_google").getElementsByTagName("span")[poleleftSpan_google[poziceSpanu]].innerHTML;
           $("#odparovat_google").show();
       } else {
           Odznacit(document.getElementById("right_google"));
           document.getElementById("rightBottom_google").innerHTML = "nepárováno";
           $("#odparovat_google").hide();
       }
   }
      function LeftClickZbozi(span) {
       Oznacit(span);
       var poziceSpanu = PoziceZbozi(span);
       if (poleleftSpan_zbozi[poziceSpanu] != undefined) {
           Oznacit(document.getElementById("right_zbozi").getElementsByTagName("span")[poleleftSpan_zbozi[poziceSpanu]]);
           document.getElementById("rightBottom_zbozi").innerHTML = document.getElementById("right_zbozi").getElementsByTagName("span")[poleleftSpan_zbozi[poziceSpanu]].innerHTML;
           $("#odparovat_zbozi").show();
       } else {
           Odznacit(document.getElementById("right_zbozi"));
           document.getElementById("rightBottom_zbozi").innerHTML = "nepárováno";
           $("#odparovat_zbozi").hide();
       }
   }
   function LeftClickHeurekaSK(span) {
       Oznacit(span);
       var poziceSpanu = PoziceHeureka(span);
       if (poleleftSpan_heurekaSK[poziceSpanu] != undefined) {
           Oznacit(document.getElementById("right_heureka_sk").getElementsByTagName("span")[poleleftSpan_heurekaSK[poziceSpanu]]);
           document.getElementById("rightBottom_heureka_sk").innerHTML = document.getElementById("right_heureka_sk").getElementsByTagName("span")[poleleftSpan_heurekaSK[poziceSpanu]].innerHTML;
                   $("#odparovat_heureka_sk").show();
       } else {
           Odznacit(document.getElementById("right_heureka_sk"));
           document.getElementById("rightBottom_heureka_sk").innerHTML = "nepárováno";
                   $("#odparovat_heureka_sk").hide();
       }
   }

      function LeftClickGlamiSK(span) {
       Oznacit(span);
       var poziceSpanu = PoziceGlami(span);
       if (poleleftSpan_glamiSK[poziceSpanu] != undefined) {
           Oznacit(document.getElementById("right_glami_sk").getElementsByTagName("span")[poleleftSpan_glamiSK[poziceSpanu]]);
           document.getElementById("rightBottom_glami_sk").innerHTML = document.getElementById("right_glami_sk").getElementsByTagName("span")[poleleftSpan_glamiSK[poziceSpanu]].innerHTML;
                   $("#odparovat_glami_sk").show();
       } else {
           Odznacit(document.getElementById("right_glami_sk"));
           document.getElementById("rightBottom_glami_sk").innerHTML = "nepárováno";
                   $("#odparovat_glami_sk").hide();
       }
   }

   function Oznacit(span) {
       Odznacit(span.parentElement);
       span.className = "active";
   }
   function Odznacit(parent) {
       spans = parent.getElementsByTagName("span");
       for (i = 0; i < spans.length; i++)
           spans[i].className = "unactive";
   }
   function PoziceHeureka(span) {
       spans = span.parentNode.getElementsByTagName("span");
       for (i = 0; i < spans.length; i++) {
           if (span == spans[i]) return i;
       }
   }
      function PoziceGlami(span) {
       spans = span.parentNode.getElementsByTagName("span");
       for (i = 0; i < spans.length; i++) {
           if (span == spans[i]) return i;
       }
   }
   function PoziceGoogle(span) {
       spans = span.parentNode.getElementsByTagName("span");
       for (i = 0; i < spans.length; i++) {
           if (span == spans[i]) return i;
       }
   }
      function PoziceZbozi(span) {
       spans = span.parentNode.getElementsByTagName("span");
       for (i = 0; i < spans.length; i++) {
           if (span == spans[i]) return i;
       }
   }
   function RightClickHeurekaCZ(span) {
       if (span.className == "active") {
                      $("#odparovat_heureka_cz").hide();
           Odznacit(span.parentElement);
           var LeftActiveHeurekaCZ = FunctionLeftActiveHeurekaCZ();
           poleleftSpan_heurekaCZ[LeftActiveHeurekaCZ] = undefined;
           document.getElementById("left_heureka_cz").getElementsByTagName("span")[LeftActiveHeurekaCZ].style.borderColor = "red";
           document.getElementById("rightBottom_heureka_cz").innerHTML = "nepárováno";
       } else {
                  $("#odparovat_heureka_cz").show();
           Oznacit(span);
           var poziceSpanu = PoziceHeureka(span);
           var LeftActiveHeurekaCZ = FunctionLeftActiveHeurekaCZ();
           poleleftSpan_heurekaCZ[LeftActiveHeurekaCZ] = poziceSpanu;
           if (typeof document.getElementById("left_heureka_cz").getElementsByTagName("span")[LeftActiveHeurekaCZ] === "undefined") {
               alert("' . $this->l('Nejdříve musíte kliknout na CZ kategorii vlevo a až poté párovat na CZ kategorii vpravo') . '");
               return false;
           }
           document.getElementById("left_heureka_cz").getElementsByTagName("span")[LeftActiveHeurekaCZ].style.borderColor = "#0f0";
           document.getElementById("rightBottom_heureka_cz").innerHTML = span.innerHTML;
       }
   }
   function RightClickHeurekaSK(span) {
       if (span.className == "active") {
           $("#odparovat_heureka_sk").hide();
           Odznacit(span.parentElement);
           var LeftActiveHeurekaSK = FunctionLeftActiveHeurekaSK();
           poleleftSpan_heurekaSK[LeftActiveHeurekaSK] = undefined;
           document.getElementById("left_heureka_sk").getElementsByTagName("span")[LeftActiveHeurekaSK].style.borderColor = "red";
           document.getElementById("rightBottom_heureka_sk").innerHTML = "nepárováno";
       } else {
       $("#odparovat_heureka_sk").show();
           Oznacit(span);
           var poziceSpanu = PoziceHeureka(span);
           var LeftActiveHeurekaSK = FunctionLeftActiveHeurekaSK();
           poleleftSpan_heurekaSK[LeftActiveHeurekaSK] = poziceSpanu;
           if (typeof document.getElementById("left_heureka_sk").getElementsByTagName("span")[LeftActiveHeurekaSK] === "undefined") {
               alert("' . $this->l('Nejdříve musíte kliknout na SK kategorii vlevo a až poté párovat na SK kategorii vpravo') . '");
               return false;
           }
           document.getElementById("left_heureka_sk").getElementsByTagName("span")[LeftActiveHeurekaSK].style.borderColor = "#0f0";
           document.getElementById("rightBottom_heureka_sk").innerHTML = span.innerHTML;
       }
   }

      function RightClickGlamiCZ(span) {
       if (span.className == "active") {
                      $("#odparovat_glami_cz").hide();
           Odznacit(span.parentElement);
           var LeftActiveGlamiCZ = FunctionLeftActiveGlamiCZ();
           poleleftSpan_glamiCZ[LeftActiveGlamiCZ] = undefined;
           document.getElementById("left_glami_cz").getElementsByTagName("span")[LeftActiveGlamiCZ].style.borderColor = "red";
           document.getElementById("rightBottom_glami_cz").innerHTML = "nepárováno";
       } else {
                  $("#odparovat_glami_cz").show();
           Oznacit(span);
           var poziceSpanu = PoziceGlami(span);
           var LeftActiveGlamiCZ = FunctionLeftActiveGlamiCZ();
           poleleftSpan_glamiCZ[LeftActiveGlamiCZ] = poziceSpanu;
           if (typeof document.getElementById("left_glami_cz").getElementsByTagName("span")[LeftActiveGlamiCZ] === "undefined") {
               alert("' . $this->l('Nejdříve musíte kliknout na CZ kategorii vlevo a až poté párovat na CZ kategorii vpravo') . '");
               return false;
           }
           document.getElementById("left_glami_cz").getElementsByTagName("span")[LeftActiveGlamiCZ].style.borderColor = "#0f0";
           document.getElementById("rightBottom_glami_cz").innerHTML = span.innerHTML;
       }
   }
   function RightClickGlamiSK(span) {
       if (span.className == "active") {
           $("#odparovat_glami_sk").hide();
           Odznacit(span.parentElement);
           var LeftActiveGlamiSK = FunctionLeftActiveGlamiSK();
           poleleftSpan_glamiSK[LeftActiveGlamiSK] = undefined;
           document.getElementById("left_glami_sk").getElementsByTagName("span")[LeftActiveGlamiSK].style.borderColor = "red";
           document.getElementById("rightBottom_glami_sk").innerHTML = "nepárováno";
       } else {
       $("#odparovat_glami_sk").show();
           Oznacit(span);
           var poziceSpanu = PoziceGlami(span);
           var LeftActiveGlamiSK = FunctionLeftActiveGlamiSK();
           poleleftSpan_glamiSK[LeftActiveGlamiSK] = poziceSpanu;
           if (typeof document.getElementById("left_glami_sk").getElementsByTagName("span")[LeftActiveGlamiSK] === "undefined") {
               alert("' . $this->l('Nejdříve musíte kliknout na SK kategorii vlevo a až poté párovat na SK kategorii vpravo') . '");
               return false;
           }
           document.getElementById("left_glami_sk").getElementsByTagName("span")[LeftActiveGlamiSK].style.borderColor = "#0f0";
           document.getElementById("rightBottom_glami_sk").innerHTML = span.innerHTML;
       }
   }

   function RightClickGoogle(span) {
       if (span.className == "active") {
       $("#odparovat_google").hide();
           Odznacit(span.parentElement);
           var leftActiveGoogle = FunctionLeftActiveGoogle();
           poleleftSpan_google[leftActiveGoogle] = undefined;
           document.getElementById("left_google").getElementsByTagName("span")[leftActiveGoogle].style.borderColor = "red";
           document.getElementById("rightBottom_google").innerHTML = "nepárováno";
       } else {
       $("#odparovat_google").show();
           Oznacit(span);
           var poziceSpanu = PoziceGoogle(span);
           var leftActiveGoogle = FunctionLeftActiveGoogle();
           poleleftSpan_google[leftActiveGoogle] = poziceSpanu;
           if (typeof document.getElementById("left_google").getElementsByTagName("span")[leftActiveGoogle] === "undefined") {
               alert("' . $this->l('Nejdříve musíte kliknout na kategorii vlevo a až poté párovat na kategorii vpravo') . '");
               return false;
           }
           document.getElementById("left_google").getElementsByTagName("span")[leftActiveGoogle].style.borderColor = "#0f0";
           document.getElementById("rightBottom_google").innerHTML = span.innerHTML;
       }
   }

      function RightClickZbozi(span) {
       if (span.className == "active") {
       $("#odparovat_zbozi").hide();
           Odznacit(span.parentElement);
           var leftActiveZbozi = FunctionLeftActiveZbozi();
           poleleftSpan_zbozi[leftActiveZbozi] = undefined;
           document.getElementById("left_zbozi").getElementsByTagName("span")[leftActiveZbozi].style.borderColor = "red";
           document.getElementById("rightBottom_zbozi").innerHTML = "nepárováno";
       } else {
       $("#odparovat_zbozi").show();
           Oznacit(span);
           var poziceSpanu = PoziceZbozi(span);
           var leftActiveZbozi = FunctionLeftActiveZbozi();
           poleleftSpan_zbozi[leftActiveZbozi] = poziceSpanu;
           if (typeof document.getElementById("left_zbozi").getElementsByTagName("span")[leftActiveZbozi] === "undefined") {
               alert("' . $this->l('Nejdříve musíte kliknout na kategorii vlevo a až poté párovat na kategorii vpravo') . '");
               return false;
           }
           document.getElementById("left_zbozi").getElementsByTagName("span")[leftActiveZbozi].style.borderColor = "#0f0";
           document.getElementById("rightBottom_zbozi").innerHTML = span.innerHTML;
       }
   }
   function FunctionLeftActiveHeurekaCZ() {
       spans = document.getElementById("left_heureka_cz").getElementsByTagName("span");
       for (i = 0; i < spans.length; i++)
           if (spans[i].className == "active") return i;
   }
   function FunctionLeftActiveHeurekaSK() {
       spans = document.getElementById("left_heureka_sk").getElementsByTagName("span");
       for (i = 0; i < spans.length; i++)
           if (spans[i].className == "active") return i;
   }

      function FunctionLeftActiveGlamiCZ() {
       spans = document.getElementById("left_glami_cz").getElementsByTagName("span");
       for (i = 0; i < spans.length; i++)
           if (spans[i].className == "active") return i;
   }
   function FunctionLeftActiveGlamiSK() {
       spans = document.getElementById("left_glami_sk").getElementsByTagName("span");
       for (i = 0; i < spans.length; i++)
           if (spans[i].className == "active") return i;
   }

   function FunctionLeftActiveGoogle() {
       spans = document.getElementById("left_google").getElementsByTagName("span");
       for (i = 0; i < spans.length; i++)
           if (spans[i].className == "active") return i;
   }

      function FunctionLeftActiveZbozi() {
       spans = document.getElementById("left_zbozi").getElementsByTagName("span");
       for (i = 0; i < spans.length; i++)
           if (spans[i].className == "active") return i;
   }
</script > ';

        }

        $this->_html .= '<div class="clear_both" ></div > <!-- test -->';
        $this->_html .= '<!-- test3 --></div><!-- test4 -->';
        $this->_html .= '</div></div></div>'; //FRAME END
        // }
        //PAROVANI END
        //PAROVANI END
        //PAROVANI END


        $this->_html .= '<div class="row"><div class="col-lg-12"><div class="panel"><div class="panel-heading"><i class="icon-info"></i> ' . $this->l('Informace') . '</div>'; // FRAME
        if ($this->name == 'shaim_export') {
            $this->_html .= "<div class='well'>" . $this->l('Příklad tvorby xml exportu pro velké množství produktů:') . "<br/>";
            $this->_html .= $this->MakeURL($this->full_url . 'modules/' . $this->name . '/' . $this->export_file . "?" . $this->real_name . "&begin=0&end=5000&open=true&token=$export_token" . $add_id_shop_for_url) . "<br/>";
            $this->_html .= $this->MakeURL($this->full_url . 'modules/' . $this->name . '/' . $this->export_file . "?" . $this->real_name . "&begin=5001&end=10000&token=$export_token" . $add_id_shop_for_url) . "<br/>";
            $this->_html .= $this->MakeURL($this->full_url . 'modules/' . $this->name . '/' . $this->export_file . "?" . $this->real_name . "&begin=15001&end=20000&token=$export_token" . $add_id_shop_for_url) . "<br/>";
            $this->_html .= $this->MakeURL($this->full_url . 'modules/' . $this->name . '/' . $this->export_file . "?" . $this->real_name . "&begin=20001&end=25000&close=true&token=$export_token" . $add_id_shop_for_url) . "<br/></div><!-- TEST -->";

            $languages = Language::getLanguages(true);

            if (count($languages) > 1) {
                if (($this->real_name == 'google_com' || $this->real_name == 'all') && $aktivni_sluzby['active_google_com'] == 1) {
                    $this->_html .= "<h4>" . $this->l('Exporty pro Google v ostatních jazycích:') . "</h4>";
                    foreach ($languages as $l) {
                        if ($l['id_lang'] == Configuration::get('PS_LANG_DEFAULT')) { // Default uz tu nepotrebujeme
                            // continue;
                        };
                        $cron_url = $this->MakeURL($this->full_url . 'modules/' . $this->name . '/' . $this->export_file . "?google_com&open&close&token=$export_token&force_lang=" . $l['iso_code'] . $add_id_shop_for_url);
                        $this->_html .= "<div>Cron " . $l['iso_code'] . ":<br />" . $cron_url . "</div>";
                        $xml_url = $this->MakeURL($this->full_url . $this->export_folder . "/google_com_" . $l['iso_code'] . $add_id_shop . ".xml");
                        $this->_html .= "<div>XML " . $l['iso_code'] . ":<br />" . $xml_url . "</div>";
                    }
                }

                if (($this->real_name == 'facebook_com' || $this->real_name == 'all') && $aktivni_sluzby['active_facebook_com'] == 1) {
                    $this->_html .= "<h4>" . $this->l('Exporty pro Facebook v ostatních jazycích:') . "</h4>";
                    foreach ($languages as $l) {
                        if ($l['id_lang'] == Configuration::get('PS_LANG_DEFAULT')) { // Default uz tu nepotrebujeme
                            // continue;
                        };
                        $cron_url = $this->MakeURL($this->full_url . 'modules/' . $this->name . '/' . $this->export_file . "?facebook_com&open&close&token=$export_token&force_lang=" . $l['iso_code'] . $add_id_shop_for_url);
                        $this->_html .= "<div>Cron " . $l['iso_code'] . ":<br />" . $cron_url . "</div>";
                        $xml_url = $this->MakeURL($this->full_url . $this->export_folder . "/facebook_com_" . $l['iso_code'] . $add_id_shop . ".xml");
                        $this->_html .= "<div>XML " . $l['iso_code'] . ":<br />" . $xml_url . "</div>";
                    }
                }
            }

        }
        $this->_html .= '<br /><br /><br /><div class="clear_both"><!-- test2 --><small>' . $this->credits . '<br />' . $this->description . '</small ></div>';
        // if ($this->name == 'shaim_export') {
        $this->_html .= '</div></div></div>'; //FRAME END
        // }
        return $this->_html;
    }

    public function hookdisplayAdminProductsExtra($params)
    {

        if ($this->name != 'shaim_export') {
            return;
        }

        if (version_compare(_PS_VERSION_, "1.7", ">=")) {
            $product = new Product((int)$params['id_product']);
            $ok = (Validate::isLoadedObject($product) ? true : false);
        } else {
            $product = new Product((int)Tools::getValue('id_product'));
            $ok = (Validate::isLoadedObject($product) ? true : false);
        }
        if ($ok) {
            $show_save = true;
            $hide_save_17 = false;
            if (version_compare(_PS_VERSION_, "1.6", "<")) {
                $show_save = false;
            } elseif (version_compare(_PS_VERSION_, "1.7", ">=")) {
                $hide_save_17 = true;
            }
            $this->context->smarty->assign(array(
                'product' => $product,
                'show_save' => $show_save,
                'hide_save_17' => $hide_save_17,
            ));
            return $this->display(__FILE__, '/' . $this->name . '.tpl');
        } else {
            return $this->l('Před použitím této funkcionality nejdříve uložte produkt (týká se pouze nově vytvářených produktů).');
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
