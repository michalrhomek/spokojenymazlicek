<?php

if (class_exists('LibsPSOC')) { # If loaded, return true #
    return true;
}

(file_exists('./libs_global.php')) ? require_once './libs_global.php' : die('can\'t load file!');

Class LibsPSOC extends LibsGlobal
{

    public function __construct()
    {
        parent::__construct();
        defined('SHORT_DESC') OR define('SHORT_DESC', 300); # Short desc #

        if (SHOP == 1 && !defined('_PS_VERSION_')) { // 1.7
            $tmp = $this->QueryR("SELECT value FROM configuration WHERE name = 'PS_VERSION_DB' LIMIT 0,1;");
            define('_PS_VERSION_', $tmp);
        }
        $this->GetShopId();
        $this->GetLanguageId();
        $this->GetPhysicalUri();
        $this->GetGroups();
        $this->GetWeb();


    }

    protected function GetLanguageId()
    {
        // OK

        if (SHOP == 1) {
            if (basename(__DIR__) == 'shaim_glami') {
                $this->id_lang = $this->QueryR("SELECT id_lang FROM lang WHERE active = 1 && iso_code = 'cs';");
                if (empty($this->id_lang)) {
                    // $this->id_lang = (int)$this->QueryR("SELECT id_lang FROM lang WHERE active = 1;");
                    $this->id_lang = (int)$this->QueryR("SELECT value FROM configuration WHERE name = 'PS_LANG_DEFAULT';");
                }
            } else {
                // $this->id_lang = (int)$this->QueryR("SELECT id_lang FROM lang WHERE active = 1;");

                $this->id_lang = (int)$this->QueryR("SELECT value FROM configuration WHERE name = 'PS_LANG_DEFAULT' && id_shop = {$this->id_shop};");
                if (empty($this->id_lang)) {
                    $this->id_lang = (int)$this->QueryR("SELECT value FROM configuration WHERE name = 'PS_LANG_DEFAULT';");
                }
            }

            $this->id_lang_sk = (int)$this->QueryR("SELECT id_lang FROM lang WHERE active = 1 && iso_code = 'sk' && id_lang != {$this->id_lang};");
        } elseif (SHOP == 2) {
            $this->id_lang = (int)$this->QueryR("SELECT language_id FROM language WHERE status = 1;");
            $this->id_lang_sk = (int)$this->QueryR("SELECT language_id FROM language WHERE status = 1 && code = 'sk' && language_id != {$this->id_lang};");
        }
        if (isset($_GET['force_lang']) && !empty($_GET['force_lang'])) {
            $force_id_lang = (int)$this->QueryR("SELECT id_lang FROM lang WHERE iso_code = '" . $_GET['force_lang'] . "';");
            if ($force_id_lang) {
                $this->id_lang = $force_id_lang;
            }
        }
    }

    protected function GetGroups()
    {
        // OK
        if (SHOP == 1) {
            $this->id_groups_wholesale = array();
            if (version_compare(_PS_VERSION_, '1.6', '>=')) {
                $groups = $this->QueryFA("SELECT a.id_group FROM group_lang as a INNER JOIN group_shop as b ON (a.id_group = b.id_group) WHERE a.name LIKE '%wholesale%' || a.name LIKE '%velkoobchod%';", false, 'assoc');
            } else {
                $groups = $this->QueryFA("SELECT id_group FROM group_lang WHERE name LIKE '%wholesale%' || name LIKE '%velkoobchod%';", false, 'assoc');
            }

            if ($groups) {

                foreach ($groups as $g) {
                    $this->id_groups_wholesale[$g['id_group']] = $g['id_group'];
                }
            }

        } elseif (SHOP == 2) {


        }
    }

    protected function GetShopId()
    {


        if (SHOP == 1 && version_compare(_PS_VERSION_, '1.5', '>=')) {
            $this->id_shop = (int)$this->QueryR("SELECT value FROM configuration WHERE name = 'PS_SHOP_DEFAULT' LIMIT 0,1;");
            // $this->id_shops = $this->QueryFA("SELECT id_shop, id_shop_group FROM shop WHERE active = 1;", false, 'assoc');
            $this->id_shops = $this->QueryFA("SELECT id_shop, id_shop_group FROM shop;", false, 'assoc');
        } elseif (SHOP == 2) {
            $this->id_shop = (int)$this->QueryR("SELECT store_id FROM store;");
            $this->id_shops = $this->QueryFA("SELECT store_id FROM store;", false, 'assoc');
        } else {
            $this->id_shops = array();
            $this->id_shop = 0;
        }
        if (isset($_GET['id_shop']) && $_GET['id_shop'] > 0) {
            if (SHOP == 1 && version_compare(_PS_VERSION_, '1.5', '>=')) {
                $exists = false;
                foreach ($this->id_shops as $id_shop) {
                    if ($_GET['id_shop'] == $id_shop['id_shop']) {
                        $exists = true;
                        break;
                    }
                }
                if (!$exists) {
                    die('id_shop neexistuje, končím!');
                }
            }
            $this->id_shop = (int)$_GET['id_shop'];
        }
    }

    protected function GetPhysicalUri()
    {
        if (SHOP == 1 && version_compare(_PS_VERSION_, '1.5', '>=')) {
            $this->physical_uri = $this->QueryR("SELECT physical_uri FROM shop_url WHERE id_shop = {$this->id_shop};");
            // $this->physical_uri = $this->QueryR("SELECT physical_uri FROM shop_url WHERE main = 1 && active = 1 LIMIT 0,1;");
        } elseif (SHOP == 1) {
            // $this->physical_uri = $this->QueryR("SELECT value FROM configuration WHERE name = '__PS_BASE_URI__' LIMIT 0,1;");
            $this->physical_uri = __PS_BASE_URI__;
        }

    }

    protected function GetWeb()
    {

        if (SHOP == 1 && version_compare(_PS_VERSION_, '1.5', '>=')) {
            // $web = $this->QueryR("SELECT domain FROM shop_url WHERE main = 1 && active = 1 LIMIT 0,1;");

            $web = $this->QueryR("SELECT domain FROM shop_url WHERE id_shop = " . $this->id_shop . " && main = 1 && active = 1;");
        } elseif (SHOP == 1) {
            $web = $this->QueryR("SELECT value FROM configuration WHERE name = 'PS_SHOP_DOMAIN' LIMIT 0,1;");
        }
        if (empty($web)) {
            $web = preg_replace('/^www\./i', '', $this->Sanitize('SER', 'HTTP_HOST', 's'));
        }
        // $web = $this->Sanitize('SER', 'HTTP_HOST', 's');


        defined('WEB') OR define('WEB', $web); # Remove start www. because duplicates #
    }

    public function __destruct()
    {
        parent::__destruct();
    }

}
