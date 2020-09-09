<?php
/*
 * Author: Dominik "Shaim" Ulrich
 * E-mail: info@psmoduly.cz / info@openservis.cz
 * Websites: www.psmoduly.cz / www.openservis.cz
 *
 */

header('Content-type: text/html;charset=UTF-8');

(file_exists('./libs_prestashop.php')) ? require_once './libs_prestashop.php' : die("can't load file!");
if (!class_exists('Export') && class_exists('LibsPSOC')) {

    Class Export extends LibsPSOC
    {

        private $count = 0;
        private $variant_count = 0;
        private $skipped = 0;
        protected $physical_uri = '/';
        private $condition = array('heureka_cz' => 'new', 'google_com' => 'new', 'zbozi_cz' => 'new');

        private $taxes = array();
        private $glami_category_whitelist = array();
        private $glami_category_pair = array();
        private $pricemania_category_whitelist = array();
        private $pricemania_category_pair = array();
        private $depot_ids_zbozi = array();
        private $depot_ids_heureka = array();

        public function __construct()
        {
#1 = PS, 2 = OC, 3 = WP #


            parent::__construct();

            $this->add_id_shop = '';
            if (isset($_GET['id_shop']) && !empty($_GET['id_shop'])) {
                $this->add_id_shop = (int)$_GET['id_shop'];
            }

            $this->force_lang = false;
            $this->force_id_currency = false;
            $this->force_currency = false;
            $this->force_lang_add = '';
            if (isset($_GET['force_lang']) && !empty($_GET['force_lang']) && strlen($_GET['force_lang']) == 2) {
                $this->force_lang = $_GET['force_lang'];
                $this->force_lang_add = '_' . $this->force_lang;
                if ($this->force_lang == 'en') {
                    $this->force_currency = 'USD';
                    $force_id_currency = (int)$this->QueryR("SELECT id_currency FROM currency WHERE iso_code = '{$this->force_currency}';");
                    if ($force_id_currency > 0) {
                        $this->force_id_currency = $force_id_currency;
                    } else {
                        $this->force_currency = false;
                    }
                } elseif ($this->force_lang == 'sk') {
                    $this->force_currency = 'EUR';
                    $force_id_currency = (int)$this->QueryR("SELECT id_currency FROM currency WHERE iso_code = '{$this->force_currency}';");
                    if ($force_id_currency > 0) {
                        $this->force_id_currency = $force_id_currency;
                    } else {
                        $this->force_currency = false;
                    }
                } elseif ($this->force_lang == 'cz') {
                    $this->force_currency = 'CZK';
                    $force_id_currency = (int)$this->QueryR("SELECT id_currency FROM currency WHERE iso_code = '{$this->force_currency}';");
                    if ($force_id_currency > 0) {
                        $this->force_id_currency = $force_id_currency;
                    } else {
                        $this->force_currency = false;
                    }
                } elseif ($this->force_lang == 'de') {
                    $this->force_currency = 'EUR';
                    $force_id_currency = (int)$this->QueryR("SELECT id_currency FROM currency WHERE iso_code = '{$this->force_currency}';");
                    if ($force_id_currency > 0) {
                        $this->force_id_currency = $force_id_currency;
                    } else {
                        $this->force_currency = false;
                    }
                } elseif ($this->force_lang == 'gb') {
                    $this->force_currency = 'GBP';
                    $force_id_currency = (int)$this->QueryR("SELECT id_currency FROM currency WHERE iso_code = '{$this->force_currency}';");
                    if ($force_id_currency > 0) {
                        $this->force_id_currency = $force_id_currency;
                    } else {
                        $this->force_currency = false;
                    }
                }
            }

            $tmp = explode('/', getcwd());
            $module_name = end($tmp);
            defined('XML_FOLDER') || define('XML_FOLDER', $this->config_path . '/xml/');
            defined('PROGRESS_FILE') OR define('PROGRESS_FILE', 'progress' . $this->add_id_shop . '_' . $module_name . '.txt');
            defined('LOCK_FILE') OR define('LOCK_FILE', XML_FOLDER . $this->add_id_shop . '_' . $module_name . '.lock');

            if (!file_exists($this->config_path . '/xml/')) {
                @mkdir($this->config_path . '/xml/', 0777, true);
            }
            @chmod($this->config_path . '/xml/', 0777);

            $legacy_images = 1;
            $cod_fee = 0;
            $heureka_pair = 0;
            $google_pair = 0;
            $zbozi_pair = 0;
            $shaim_desc = 1;
            $shaim_combinations = 1;
            $shaim_only_stock = 0;
            $shaim_better_pair_manufacturer = 0;
            $shaim_better_pair_code = 0;
            $multistore = 0;
            $heureka_cpc = 0;
            $gift = '';
            $gift_price = 0;
            $max_cpc_limit = 0;
            // $odber_zdarma = 0;
            $max_cpc = 1;
            $max_cpc_search = 1;
            $shipping_price = 0;
            $shipping_price_cod = 0;
            $out_of_stock_order = 1;
            $tax_enabled = true;
            $https = false;
            $seo_url = false;
            $allow_accented_chars = 0;
            $depot_ids_zbozi = '';
            $depot_ids_heureka = '';
            $dost_day = 2;
            $dost_time = '12:00';
            $pick_day = 2;
            $pick_time = '14:00';
            $free_shipping = 0;
            $free_weight = 0;
            $stock_management = 1;
            $conversion_rate_cz = 1;
            $conversion_rate_sk = 1;
            $conversion_rate_us = 1;
            $decimals_default = 0;
            $decimal_count = 2;
            $active_zbozi_cz = 1;
            $active_heureka_cz = 1;
            $active_heureka_sk = 1;
            $active_heureka_dostupnost = 1;
            $active_google_com = 1;
            $active_facebook_com = 1;
            $this->blacklist_product = array();
            $this->carriers = array();
            $this->days_stock = 0;
            $this->days_nostock = 7;
            $this->id_currency_default = 0;

            $add = '(id_shop = ' . $this->id_shop . ' || id_shop IS NULL) && ';


            // || name = 'shaim_export_odber_zdarma'
            $tmp_all = $this->QueryFA("SELECT name, value FROM configuration WHERE $add(name = 'PS_LEGACY_IMAGES' || name = 'COD_FEE' || name = 'PS_PRICE_ROUND_MODE' || name = 'PS_ROUTE_product_rule' || name = 'PS_SHIPPING_FREE_PRICE' || name = 'PS_SHIPPING_FREE_WEIGHT' || name = 'PS_SHIPPING_HANDLING' || name = 'shaim_export_heureka_pair' || name = 'shaim_export_google_pair'  || name = 'shaim_export_zbozi_pair' || name = 'shaim_export_multistore' || name = 'shaim_export_heureka_cpc' || name = 'shaim_export_gift' || name = 'shaim_export_gift_price' || name = 'shaim_export_max_cpc_limit' || name = 'shaim_export_desc' || name = '{$module_name}_combinations' || name = '{$module_name}_only_stock' || name = 'shaim_export_better_pair_manufacturer' || name = 'shaim_export_better_pair_code' || name = '{$module_name}_utm' || name = 'shaim_export_max_cpc' || name = 'shaim_export_max_cpc_search' || name = 'shaim_export_shipping_price' || name = 'shaim_export_shipping_price_cod' || name = '{$module_name}_dost_day' || name = '{$module_name}_dost_time' || name = '{$module_name}_pick_day' || name = '{$module_name}_pick_time' || name = 'PS_ORDER_OUT_OF_STOCK' || name = 'PS_TAX'  || name = 'PS_REWRITING_SETTINGS' || name = 'PS_CURRENCY_DEFAULT' || name = 'PS_SSL_ENABLED' || name = 'PS_STOCK_MANAGEMENT' || name = 'PS_COUNTRY_DEFAULT' || name = 'PS_ALLOW_ACCENTED_CHARS_URL' || name = 'PS_PRICE_DISPLAY_PRECISION' || name = 'shaim_export_depot_ids_zbozi' ||  name = 'shaim_export_depot_ids_heureka' ||  name = 'shaim_dostupnost_depot_ids_heureka' || name = 'shaim_export_token'
|| name = 'shaim_export_aktivni_sluzby' || name = '{$module_name}_blacklist_product' || name = '{$module_name}_carriers' || name = 'shaim_export_days_stock' || name = 'shaim_export_days_nostock' || name = 'shaim_glami_days_stock' || name = 'shaim_glami_days_nostock')
ORDER BY id_shop ASC, id_configuration ASC
;", false, 'assoc');


            foreach ($tmp_all as $tmp) {
                Switch ($tmp['name']) {
                    case 'shaim_export_aktivni_sluzby':
                        $aktivni_sluzby = unserialize($tmp['value']);
                        $active_zbozi_cz = (int)$aktivni_sluzby['active_zbozi_cz'];
                        $active_heureka_cz = (int)$aktivni_sluzby['active_heureka_cz'];
                        $active_heureka_sk = (int)$aktivni_sluzby['active_heureka_sk'];
                        $active_heureka_dostupnost = (int)$aktivni_sluzby['active_heureka_dostupnost'];
                        $active_google_com = (int)$aktivni_sluzby['active_google_com'];
                        $active_facebook_com = (int)$aktivni_sluzby['active_facebook_com'];
                        break;
                    case 'COD_FEE':
                        $cod_fee = $tmp['value'];
                        break;
                    case 'PS_LEGACY_IMAGES':
                        $legacy_images = (int)$tmp['value'];
                        break;
                    case 'PS_PRICE_ROUND_MODE':
                        $rt = $tmp['value'];
                        break;
                    case 'PS_ROUTE_product_rule':
                        $rr = $tmp['value'];
                        break;
                    case 'PS_SHIPPING_FREE_PRICE':
                        $free_shipping = $tmp['value'];
                        break;
                    case 'PS_SHIPPING_FREE_WEIGHT':
                        $free_weight = $tmp['value'];
                        break;
                    case 'PS_SHIPPING_HANDLING':
                        $sh = $tmp['value'];
                        break;
                    case 'shaim_export_heureka_pair':
                        $heureka_pair = (int)$tmp['value'];
                        break;
                    case 'shaim_export_google_pair':
                        $google_pair = (int)$tmp['value'];
                        break;
                    case 'shaim_export_zbozi_pair':
                        $zbozi_pair = (int)$tmp['value'];
                        break;
                    case 'shaim_export_desc':
                        $shaim_desc = (int)$tmp['value'];
                        break;
                    case 'shaim_export_better_pair_manufacturer':
                        $shaim_better_pair_manufacturer = (int)$tmp['value'];
                        break;
                    case 'shaim_export_combinations':
                    case 'shaim_glami_combinations':
                        $shaim_combinations = (int)$tmp['value'];
                        break;
                    case 'shaim_export_only_stock':
                    case 'shaim_glami_only_stock':
                        $shaim_only_stock = (int)$tmp['value'];
                        break;
                    case 'shaim_export_better_pair_code':
                        $shaim_better_pair_code = (int)$tmp['value'];
                        break;
                    case 'shaim_export_utm':
                    case 'shaim_glami_utm':
                        $shaim_utm = (int)$tmp['value'];
                        break;
                    case 'shaim_export_multistore':
                        $multistore = (int)$tmp['value'];
                        break;
                    case 'shaim_export_heureka_cpc':
                        $heureka_cpc = $tmp['value'];
                        break;
                    case 'shaim_export_gift':
                        $gift = $tmp['value'];
                        break;
                    case 'shaim_export_gift_price':
                        $gift_price = (float)$tmp['value'];
                        break;
                    case 'shaim_export_max_cpc_limit':
                        $max_cpc_limit = $tmp['value'];
                        break;
                    // case 'shaim_export_odber_zdarma':
                    //     $odber_zdarma = (int)$tmp['value'];
                    //    break;
                    case 'shaim_export_max_cpc':
                        $max_cpc = $tmp['value'];
                        break;
                    case 'shaim_export_max_cpc_search':
                        $max_cpc_search = $tmp['value'];
                        break;
                    case 'shaim_export_shipping_price':
                        $shipping_price = $tmp['value'];
                        break;
                    case 'shaim_export_shipping_price_cod':
                        $shipping_price_cod = $tmp['value'];
                        break;
                    case 'shaim_export_dost_day':
                    case 'shaim_dostupnost_dost_day':
                        if (!empty($tmp['value'])) {
                            $dost_day = (int)$tmp['value'];
                        }
                        break;
                    case 'shaim_export_dost_time':
                    case 'shaim_dostupnost_dost_time':
                        if (!empty($tmp['value'])) {
                            $dost_time = $tmp['value'];
                        }
                        break;
                    case 'shaim_export_pick_day':
                    case 'shaim_dostupnost_pick_day':
                        if (!empty($tmp['value'])) {
                            $pick_day = (int)$tmp['value'];
                        }
                        break;
                    case 'shaim_export_pick_time':
                    case 'shaim_dostupnost_pick_time':
                        if (!empty($tmp['value'])) {
                            $pick_time = $tmp['value'];
                        }
                        break;
                    case 'PS_ORDER_OUT_OF_STOCK':
                        $out_of_stock_order = $tmp['value'];
                        break;
                    case 'PS_TAX':
                        $tax_enabled = (bool)$tmp['value'];
                        break;
                    case 'PS_CURRENCY_DEFAULT':
                        $this->id_currency_default = (int)$tmp['value'];
                        break;
                    case 'PS_SSL_ENABLED':
                        $https = (bool)$tmp['value'];
                        break;
                    case 'PS_STOCK_MANAGEMENT':
                        $stock_management = (int)$tmp['value'];
                        break;
                    case 'PS_REWRITING_SETTINGS':
                        $seo_url = (int)$tmp['value'];
                        break;
                    case 'PS_COUNTRY_DEFAULT':
                        $country_default = (int)$tmp['value'];
                        break;
                    case 'PS_ALLOW_ACCENTED_CHARS_URL':
                        $allow_accented_chars = (int)$tmp['value'];
                        break;
                    case 'PS_PRICE_DISPLAY_PRECISION':
                        $decimal_count = (int)$tmp['value'];
                        break;
                    case 'shaim_export_depot_ids_zbozi':

                        if (!empty($tmp['value'])) {
                            $depot_ids_zbozi = explode(',', preg_replace("/[^0-9,]/", "", $tmp['value']));
                        }
                        break;
                    case 'shaim_export_depot_ids_heureka':
                        if (!empty($tmp['value'])) {
                            $depot_ids_heureka = explode(',', preg_replace("/[^0-9,]/", "", $tmp['value']));
                        }
                        break;
                    case 'shaim_dostupnost_depot_ids_heureka':
                        if (!empty($tmp['value'])) {
                            $depot_ids_heureka = explode(',', preg_replace("/[^0-9,]/", "", $tmp['value']));
                        }
                        break;
                    case 'shaim_export_token':
                        if (isset($_GET['all']) && $tmp['value'] != filter_input(INPUT_GET, 'token')) {
                            die('Špatný token!');
                        }
                        break;
                    case 'shaim_export_blacklist_product':
                    case 'shaim_glami_blacklist_product':
                    case 'shaim_pricemania_blacklist_product':
                    case 'shaim_dostupnost_blacklist_product':
                        if (!empty($tmp['value'])) {
                            $this->blacklist_product = array_filter(array_unique(explode(',', $tmp['value'])));
                        }
                        break;
                    case 'shaim_export_carriers':
                    case 'shaim_glami_carriers':
                        if (!empty($tmp['value'])) {
                            $this->carriers = unserialize($tmp['value']);
                        }
                        break;

                    case 'shaim_export_days_stock':
                    case 'shaim_glami_days_stock':
                        if ($tmp['value'] != '') {
                            $this->days_stock = (int)$tmp['value'];
                        }
                        break;
                    case 'shaim_export_days_nostock':
                    case 'shaim_glami_days_nostock':
                        if ($tmp['value'] != '') {
                            $this->days_nostock = (int)$tmp['value'];
                        }
                        break;


                }

            }


            $exists_remove_id = (bool)$this->QueryR("SELECT m.id_module FROM module as m
            INNER JOIN module_shop as ms ON (m.id_module = ms.id_module && ms.id_shop = {$this->id_shop})
            WHERE (m.name = 'faktiva_cleanurls' || m.name = 'cleanurls' || m.name = 'purls' || m.name = 'sturls' || m.name = 'friendlyurl' || m.name = 'prettyurls' || m.name = 'fsadvancedurl' || m.name = 'vipadvancedurl')
            && m.active = 1 LIMIT 0,1;");


            if (empty($rr)) {
                if (version_compare(_PS_VERSION_, '1.7.2', '>=')) {
                    $route_rule = '{category:/}{id}{-:id_product_attribute}-{rewrite}{-:ean13}.html';
                } else {
                    $route_rule = '{category:/}{id}-{rewrite}{-:ean13}.html';
                }
            } else {
                $route_rule = $rr;
            }
            $shipping_manipulate = (empty($sh)) ? 0 : $sh;
            // PS 1.2 and lower fix
            if (!isset($rt[0])) {
                $rt = array(2);
            }

            $roundtype = $this->GetRoundType($rt[0]);

//                     $add = ' && decimals = 1';
            // $decimals = (bool)$this->QueryR("SELECT COUNT(*) as pocet FROM currency WHERE id_currency = $currency_default$add LIMIT 0,1;");


            // $conversion_rate_cz = (float)$this->QueryR("SELECT conversion_rate FROM currency WHERE iso_code = 'CZK' LIMIT 0,1;");
            // $conversion_rate_sk = (float)$this->QueryR("SELECT conversion_rate FROM currency WHERE iso_code = 'EUR' LIMIT 0,1;");

            $iso_code = $this->QueryR("SELECT iso_code FROM currency WHERE id_currency = {$this->id_currency_default} LIMIT 0,1;");

            if (version_compare(_PS_VERSION_, '1.7', '>=')) {
                $currencies = $this->QueryFA("SELECT a.id_currency, b.conversion_rate, a.iso_code FROM currency as a INNER JOIN currency_shop as b ON (a.id_currency = b.id_currency && b.id_shop = " . $this->id_shop . ");", false, 'assoc');

            } else {
                $currencies = $this->QueryFA("SELECT a.id_currency, a.decimals, b.conversion_rate, a.iso_code FROM currency as a INNER JOIN currency_shop as b ON (a.id_currency = b.id_currency && b.id_shop = " . $this->id_shop . ");", false, 'assoc');
            }

            $decimals_cz = $decimals_sk = $decimals_us = $decimals_gb = false;
            if (version_compare(_PS_VERSION_, '1.7', '>=')) {
                $decimals_cz = $decimals_sk = $decimals_us = $decimals_gb = $decimals_default = $decimal_count;
            }
            $this->currency_default = '';
            foreach ($currencies as $c) {
                if ($c['iso_code'] == 'CZK') {
                    if (isset($c['decimals']) && $c['decimals'] == 1) {
                        $decimals_cz = $decimal_count;
                    }

                    $conversion_rate_cz = (float)$c['conversion_rate'];
                } elseif ($c['iso_code'] == 'EUR') {
                    if (isset($c['decimals']) && $c['decimals'] == 1) {
                        $decimals_sk = $decimal_count;
                    }
                    $conversion_rate_sk = (float)$c['conversion_rate'];
                } elseif ($c['iso_code'] == 'USD') {
                    if (isset($c['decimals']) && $c['decimals'] == 1) {
                        $decimals_us = $decimal_count;
                    }
                    $conversion_rate_us = (float)$c['conversion_rate'];
                } elseif ($c['iso_code'] == 'GBP') {
                    if (isset($c['decimals']) && $c['decimals'] == 1) {
                        $decimals_gb = $decimal_count;
                    }
                    $conversion_rate_gb = (float)$c['conversion_rate'];
                }
                if ($c['id_currency'] == $this->id_currency_default) {
                    $this->currency_default = $c['iso_code'];
                    $iso_code = $c['iso_code'];
                    if (isset($c['decimals']) && $c['decimals'] == 1) {
                        $decimals_default = $decimal_count;
                    }
                }
            }


            if ($iso_code == 'CZK') {
                $conversion_rate_cz = 1;
                if (empty($conversion_rate_sk)) {
                    $conversion_rate_sk = $this->GetRateEUR();
                } else {
                    //    $conversion_rate_sk *= 1000;
                }
            } elseif ($iso_code == 'EUR') {
                $conversion_rate_sk = 1;
                if (empty($conversion_rate_cz)) {
                    $conversion_rate_cz = $this->GetRateCZK();
                }

            } elseif ($iso_code == 'USD') {
                $conversion_rate_us = 1;
                if (empty($conversion_rate_cz)) {
                    $conversion_rate_cz = $this->GetRateCZK();
                }

            } elseif ($iso_code == 'GBP') {
                $conversion_rate_gb = 1;
                if (empty($conversion_rate_cz)) {
                    $conversion_rate_cz = $this->GetRateCZK();
                }

            } else {
                $conversion_rate_gb = 1;
                $conversion_rate_us = 1;
                $conversion_rate_sk = 1;
                $conversion_rate_cz = 1;
            }


            $velikonoce_month = 0;
            $velikonoce_day = 0;
            if (function_exists('easter_date')) {
                $velikonoce = (easter_date());
                $velikonoce_month = date("m", $velikonoce);
                $velikonoce_day = date("d", $velikonoce) + 1;
            }


            if (date("Y-m-d H:i") > date("Y-m-d") . ' ' . $dost_time) {
                if ($dost_day == 1) {
                    $dost_day = 2;
                }
                $this->orderdeadlineDost = $this->CheckSvatky(date("Y-m-d", strtotime("+1 weekdays")), $velikonoce_month, $velikonoce_day) . ' ' . $dost_time;
                $this->deliverytimeDost = $this->CheckSvatky(date("Y-m-d", strtotime("+" . ($dost_day + $this->svatek_add) . " weekdays")), $velikonoce_month, $velikonoce_day) . ' ' . $dost_time;
            } else {
                if ($dost_day == 0) {
                    $dost_day = 1;
                }
                $this->orderdeadlineDost = $this->CheckSvatky(date("Y-m-d", strtotime("+0 weekdays")), $velikonoce_month, $velikonoce_day) . ' ' . $dost_time;
                $this->deliverytimeDost = $this->CheckSvatky(date("Y-m-d", strtotime("+" . ($dost_day + $this->svatek_add) . " weekdays")), $velikonoce_month, $velikonoce_day) . ' ' . $dost_time;
            }


            if (date("Y-m-d H:i") > date("Y-m-d") . ' ' . $pick_time) {
                if ($pick_day == 1) {
                    $pick_day = 2;
                }
                $this->orderdeadlinePick = $this->CheckSvatky(date("Y-m-d", strtotime("+1 weekdays")), $velikonoce_month, $velikonoce_day) . ' ' . $pick_time;
                $this->deliverytimePick = $this->CheckSvatky(date("Y-m-d", strtotime("+" . ($pick_day + $this->svatek_add) . " weekdays")), $velikonoce_month, $velikonoce_day) . ' ' . $pick_time;
            } else {
                if ($pick_day == 0) {
                    $pick_day = 1;
                }
                $this->orderdeadlinePick = $this->CheckSvatky(date("Y-m-d", strtotime("+0 weekdays")), $velikonoce_month, $velikonoce_day) . ' ' . $pick_time;
                $this->deliverytimePick = $this->CheckSvatky(date("Y-m-d", strtotime("+" . ($pick_day + $this->svatek_add) . " weekdays")), $velikonoce_month, $velikonoce_day) . ' ' . $pick_time;
            }


            defined('TAX_ENABLED') OR define('TAX_ENABLED', $tax_enabled); # Aktivní DPH
            if (!$decimals_us) {
                $decimals_us = 0;
            }
            if (!$decimals_gb) {
                $decimals_gb = 0;
            }
            if (!$decimals_sk) {
                $decimals_sk = 0;
            }
            if (!$decimals_cz) {
                $decimals_cz = 0;
            }
            if (!$decimals_default) {
                $decimals_default = 0;
            }
            defined('EXISTS_REMOVE_ID') OR define('EXISTS_REMOVE_ID', $exists_remove_id);
            defined('DECIMALS_CZ') OR define('DECIMALS_CZ', $decimals_cz);
            defined('DECIMALS_SK') OR define('DECIMALS_SK', $decimals_sk);
            defined('DECIMALS_US') OR define('DECIMALS_US', $decimals_us);
            defined('DECIMALS_GB') OR define('DECIMALS_GB', $decimals_gb);
            defined('DECIMALS_DEFAULT') OR define('DECIMALS_DEFAULT', $decimals_default);
            defined('HTTPS') OR define('HTTPS', ($https == true) ? 'https://' : 'http://');
            defined('STOCK_MANAGEMENT') OR define('STOCK_MANAGEMENT', $stock_management);
            defined('SEO_URL') OR define('SEO_URL', $seo_url);
            defined('ALLOW_ACCENTED_CHARS') OR define('ALLOW_ACCENTED_CHARS', $allow_accented_chars);
            defined('ACTIVE_ZBOZI_CZ') OR define('ACTIVE_ZBOZI_CZ', $active_zbozi_cz);
            defined('ACTIVE_HEUREKA_CZ') OR define('ACTIVE_HEUREKA_CZ', $active_heureka_cz);
            defined('ACTIVE_HEUREKA_SK') OR define('ACTIVE_HEUREKA_SK', $active_heureka_sk);
            defined('ACTIVE_HEUREKA_DOSTUPNOST') OR define('ACTIVE_HEUREKA_DOSTUPNOST', $active_heureka_dostupnost);
            defined('ACTIVE_GOOGLE_COM') OR define('ACTIVE_GOOGLE_COM', $active_google_com);
            defined('ACTIVE_FACEBOOK_COM') OR define('ACTIVE_FACEBOOK_COM', $active_facebook_com);


            defined('COUNTRY_DEFAULT') OR define('COUNTRY_DEFAULT', $country_default);


            $this->depot_ids_zbozi = $depot_ids_zbozi;
            $this->depot_ids_heureka = $depot_ids_heureka;


            if ($max_cpc > 500) {
                $max_cpc = 500;
            } elseif ($max_cpc < 0) {
                $max_cpc = -1;
            } elseif ($max_cpc < 1) {
                $max_cpc = 1;
            }

            if ($max_cpc_search > 500) {
                $max_cpc_search = 500;
            } elseif ($max_cpc < 0) {
                $max_cpc_search = -1;
            } elseif ($max_cpc < 1) {
                $max_cpc_search = 1;
            }

            if ($heureka_cpc > 100) {
                $heureka_cpc = 100;
            } elseif ($heureka_cpc < 0) {
                $heureka_cpc = -1;
            }


            if ($heureka_pair == 1 && $google_pair == 1 && $zbozi_pair == 1) {
                defined('PAIR_TYPE') OR define('PAIR_TYPE', 'heureka_google_zbozi_pair');
            } elseif ($heureka_pair == 1 && $google_pair == 1 && $zbozi_pair == 0) {
                defined('PAIR_TYPE') OR define('PAIR_TYPE', 'heureka_google_pair');
            } elseif ($heureka_pair == 1 && $google_pair == 0 && $zbozi_pair == 1) {
                defined('PAIR_TYPE') OR define('PAIR_TYPE', 'heureka_zbozi_pair');
            } elseif ($heureka_pair == 0 && $google_pair == 1 && $zbozi_pair == 1) {
                defined('PAIR_TYPE') OR define('PAIR_TYPE', 'google_zbozi_pair');
            } elseif ($heureka_pair == 1 && $google_pair == 0 && $zbozi_pair == 0) {
                defined('PAIR_TYPE') OR define('PAIR_TYPE', 'heureka_pair');
            } elseif ($heureka_pair == 0 && $google_pair == 1 && $zbozi_pair == 0) {
                defined('PAIR_TYPE') OR define('PAIR_TYPE', 'google_pair');
            } elseif ($heureka_pair == 0 && $google_pair == 0 && $zbozi_pair == 1) {
                defined('PAIR_TYPE') OR define('PAIR_TYPE', 'zbozi_pair');
            } else {
                defined('PAIR_TYPE') OR define('PAIR_TYPE', 'full');
            }

            defined('CONVERSION_RATE_CZ') OR define('CONVERSION_RATE_CZ', $conversion_rate_cz);
            defined('CONVERSION_RATE_SK') OR define('CONVERSION_RATE_SK', $conversion_rate_sk);
            defined('CONVERSION_RATE_US') OR define('CONVERSION_RATE_US', $conversion_rate_us);
            defined('CONVERSION_RATE_GB') OR define('CONVERSION_RATE_GB', $conversion_rate_gb);
            defined('MULTISTORE') OR define('MULTISTORE', $multistore); # Párovat s heurekou
            defined('MAX_CPC') OR define('MAX_CPC', $max_cpc); # Zboží platba za klik, minimal 1 #
            defined('MAX_CPC_SEARCH') OR define('MAX_CPC_SEARCH', $max_cpc_search); # Zboží platba za klik, minimal 1 #
            defined('SHIPPING_PRICE') OR define('SHIPPING_PRICE', (float)$shipping_price);
            defined('SHIPPING_PRICE_COD') OR define('SHIPPING_PRICE_COD', (float)$shipping_price_cod);

            defined('HEUREKA_CPC') OR define('HEUREKA_CPC', (isset($_GET['glami'])) ? -1 : $heureka_cpc);
            $this->gifts_global = $gift;
            $this->gifts_price_global = $gift_price;
            defined('MAX_CPC_LIMIT') OR define('MAX_CPC_LIMIT', $max_cpc_limit);
            // defined('ODBER_ZDARMA') OR define('ODBER_ZDARMA', $odber_zdarma);
            defined('SHAIM_DESC') OR define('SHAIM_DESC', $shaim_desc);
            defined('SHAIM_COMBINATIONS') OR define('SHAIM_COMBINATIONS', $shaim_combinations);
            defined('SHAIM_ONLY_STOCK') OR define('SHAIM_ONLY_STOCK', $shaim_only_stock);
            defined('SHAIM_BETTER_PAIR_MANUFACTURER') OR define('SHAIM_BETTER_PAIR_MANUFACTURER', $shaim_better_pair_manufacturer);
            defined('SHAIM_BETTER_PAIR_CODE') OR define('SHAIM_BETTER_PAIR_CODE', $shaim_better_pair_code);
            defined('SHAIM_UTM') OR define('SHAIM_UTM', $shaim_utm);
            // defined('UNFEATURED') OR define('UNFEATURED', 0); # Zboží zvýhodnění #
            defined('FREE_SHIPPING') OR define('FREE_SHIPPING', $free_shipping); # Poštovné zdarma #
            defined('FREE_WEIGHT') OR define('FREE_WEIGHT', $free_weight); # Poštovné zdarma #
            defined('ROUND_TYPE') OR define('ROUND_TYPE', $roundtype); # Zaokrouhlování #
            defined('ROUTE_RULE') OR define('ROUTE_RULE', $route_rule);

            $sql = 'SELECT virtual_uri FROM shop_url WHERE id_shop = ' . $this->id_shop . ';';
            $this->virtual_uri = $this->QueryR($sql);

            defined('SHIPPING_MANIPULATE') OR define('SHIPPING_MANIPULATE', $shipping_manipulate); # Manipulační poplatek poštovné/balné #
            /*
            if ($this->force_lang == 'sk') {
                $this->currency_default = 'EUR';
                defined('MENA') OR define('MENA', 'EUR');
            } else {
                defined('MENA') OR define('MENA', $iso_code);
            }
            */
            defined('MENA') OR define('MENA', $iso_code);
            defined('COD_FEE') OR define('COD_FEE', $cod_fee);
            defined('LEGACY_IMAGES') OR define('LEGACY_IMAGES', $legacy_images);

            defined('OUT_OF_STOCK_ORDER') OR define('OUT_OF_STOCK_ORDER', $out_of_stock_order);


            defined('FORCE_ADD_BEFORE_CATEGORY') OR define('FORCE_ADD_BEFORE_CATEGORY', '');
            defined('FORCE_CATEGORY') OR define('FORCE_CATEGORY', false);
            defined('FORCE_CATEGORY_HEUREKA') OR define('FORCE_CATEGORY_HEUREKA', '');
            defined('FORCE_CATEGORY_ZBOZI') OR define('FORCE_CATEGORY_ZBOZI', '');
            defined('FORCE_ADD_AFTER_CATEGORY') OR define('FORCE_ADD_AFTER_CATEGORY', '');
            // default "", ale můžeme tam dát "\r\n", aby se to lépe četlo. Kvůli velikosti to ale řešíme takto.
            defined('NEW_LINE') OR define('NEW_LINE', "\r\n");


            // Add lang to URL


            $add_lang_to_url = '';
            $langs = $this->QueryFA("SELECT a.iso_code FROM lang as a INNER JOIN lang_shop as b ON (a.id_lang = b.id_lang && b.id_shop = " . $this->id_shop . ") WHERE a.active = 1;", false, 'assoc');


            if (count($langs) > 1) {

                $iso_code = $this->QueryR("SELECT iso_code FROM lang WHERE id_lang = " . $this->id_lang . ";");
                if ($iso_code) {
                    $add_lang_to_url = $iso_code . '/';
                    /*
                                            $sk_exists = false;
                                            $cs_exists = false;
                                            foreach ($langs as $lang) {
                                                if ($lang['iso_code'] == 'sk') {
                                                    $sk_exists = true;
                                                } elseif ($lang['iso_code'] == 'cs') {
                                                    $cs_exists = true;
                                                }
                                            }
                                            //  Pokud existují oba tyto jazyky, tak to raději dáváme pryč, jelikož pri SK heurece apod by to mohlo presmerovavat na spatne jazyky, tezko resit..
                                            if ($sk_exists && $cs_exists) {
                                                $add_lang_to_url = '';
                                            }
                                            */
                }
            }

            defined('ADD_LANG_TO_URL') OR define('ADD_LANG_TO_URL', $add_lang_to_url); # % #


            if (file_exists(LOCK_FILE)) {
                die('already locked - ' . LOCK_FILE . ' - ' . file_get_contents(LOCK_FILE));
            }
            file_put_contents(LOCK_FILE, date("Y-m-d H:i:s"));

            // $this->alternative_main_image = HTTPS . WEB . str_replace(basename($_SERVER['PHP_SELF']), 'no-image.png', $_SERVER['PHP_SELF']);

            $id_product = (isset($_GET['id_product']) && !empty($_GET['id_product'])) ? (int)preg_replace('/[^0-9]/', '', $_GET['id_product']) : 0;
            $this->debug_id_product = false;
            if (!empty($id_product)) {
                $this->debug_id_product = $id_product;
            }


            $this->LoadSpecificPrice();

            $real_sk = false;
            if ($this->id_lang_sk > 0 && $this->id_lang_sk != $this->id_lang) {
                if ((isset($_GET['glami'])) || (isset($_GET['pricemania'])) || ((isset($_GET['heureka_sk']) || isset($_GET['all']) && ACTIVE_HEUREKA_SK == 1))) {
                    $real_sk = true;
                }
            }
            defined('REAL_SK') || define('REAL_SK', $real_sk);

            if (SHAIM) {
                echo 'Begin Caching- ' . ceil(memory_get_usage(false) / 1000000) . 'MB / ' . ceil(microtime(true) - $this->start) . 's' . PHP_EOL;
            }
            $this->CacheAvailability();
            if (SHAIM) {
                echo 'CacheAvailability - ' . ceil(memory_get_usage(false) / 1000000) . 'MB / ' . ceil(microtime(true) - $this->start) . 's' . PHP_EOL;
            }
            $this->CacheAltImages();
            if (SHAIM) {
                echo 'CacheAltImages - ' . ceil(memory_get_usage(false) / 1000000) . 'MB / ' . ceil(microtime(true) - $this->start) . 's' . PHP_EOL;
            }
            $this->CacheCategories();
            if (SHAIM) {
                echo 'CacheCategories - ' . ceil(memory_get_usage(false) / 1000000) . 'MB / ' . ceil(microtime(true) - $this->start) . 's' . PHP_EOL;
            }
            if (REAL_SK) {
                $this->CacheSKCategories();
                if (SHAIM) {
                    echo 'CacheSKCategories - ' . ceil(memory_get_usage(false) / 1000000) . 'MB / ' . ceil(microtime(true) - $this->start) . 's' . PHP_EOL;
                }
            }
            if (REAL_SK) {
                $this->CacheSKNames();

                if (SHAIM) {
                    echo 'CacheSKNames - ' . ceil(memory_get_usage(false) / 1000000) . 'MB / ' . ceil(microtime(true) - $this->start) . 's' . PHP_EOL;
                }
            }
            $this->CachePair();
            if (SHAIM) {
                echo 'CachePair - ' . ceil(memory_get_usage(false) / 1000000) . 'MB / ' . ceil(microtime(true) - $this->start) . 's' . PHP_EOL;
            }
            $this->CacheParams();
            if (SHAIM) {
                echo 'CacheParams - ' . ceil(memory_get_usage(false) / 1000000) . 'MB / ' . ceil(microtime(true) - $this->start) . 's' . PHP_EOL;
            }
            if (REAL_SK) {
                $this->CacheSKParams();
                if (SHAIM) {
                    echo 'CacheSKParams - ' . ceil(memory_get_usage(false) / 1000000) . 'MB / ' . ceil(microtime(true) - $this->start) . 's' . PHP_EOL;
                }
            }
            // memory_issue_fix (vyhledej si "CacheVariants()"
            $this->CacheVariants();
            if (SHAIM) {
                echo 'CacheVariants - ' . ceil(memory_get_usage(false) / 1000000) . 'MB / ' . ceil(microtime(true) - $this->start) . 's' . PHP_EOL;
            }
            if (REAL_SK) {
                $this->CacheSKVariants();
                if (SHAIM) {
                    echo 'CacheSKVariants - ' . ceil(memory_get_usage(false) / 1000000) . 'MB / ' . ceil(microtime(true) - $this->start) . 's' . PHP_EOL;
                }
            }
            $this->list_all_products = $this->ListAllProducts();
        }

        private
        function GetRateCZK()
        {
            $xml = simplexml_load_file("https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml");
            if ($xml->Cube->Cube->Cube) {

                foreach ($xml->Cube->Cube->Cube as $x) {


                    if ($x['currency'] == 'CZK') {

                        $conversion_rate_cz = number_format(str_replace(",", ".", (float)$x['rate']), 6) / 1000;

                        return $conversion_rate_cz = $conversion_rate_cz * 1000;

                    }
                }

            }
        }

        private
        function GetRateEUR()
        {
            $obsah = file_get_contents("https://www.cnb.cz/cs/financni_trhy/devizovy_trh/kurzy_devizoveho_trhu/denni_kurz.txt");
            if ($obsah) {
                $lines = explode("\n", $obsah);
                $lines = array_filter(array_map("trim", $lines));

                $i = 0;
                foreach ($lines as $line) {
                    $i++;
                    if ($i < 3) {
                        continue;
                    }

                    $parse = explode("|", $line);
                    if ($parse[3] == 'EUR') {
                        $conversion_rate_sk = (float)str_replace(',', '.', $parse[4]);
                        return $conversion_rate_sk = 1 / $conversion_rate_sk;

                    }

                }

            }
        }

        private
        function CheckSvatky($date, $velikonoce_month, $velikonoce_day)
        {
            $this->svatek_add = 0;
            $svatky = array(array('name' => 'Den obnovy samostatného českého státu', 'month' => '01', 'day' => '01'),
                array('name' => 'Svátek práce', 'month' => '05', 'day' => '01'),
                array('name' => 'Den vítězství', 'month' => '05', 'day' => '08'),
                array('name' => 'Den slovanských věrozvěstů Cyrila a Metoděje', 'month' => '07', 'day' => '05'),
                array('name' => 'Den upálení mistra Jana Husa', 'month' => '07', 'day' => '06'),
                array('name' => 'Den české státnosti', 'month' => '09', 'day' => '28'),
                array('name' => 'Den vzniku samostatného československého státu', 'month' => '10', 'day' => '28'),
                array('name' => 'Den boje za svobodu a demokracii', 'month' => '11', 'day' => '17'),
                array('name' => 'Štědrý den', 'month' => '12', 'day' => '24'),
                array('name' => '1. svátek vánoční', 'month' => '12', 'day' => '25'),
                array('name' => '2. svátek vánoční', 'month' => '12', 'day' => '26'),
                array('name' => 'Velikonoční pondělí', 'month' => $velikonoce_month, 'day' => $velikonoce_day),
            );
            list($year, $month, $day) = explode("-", $date);

            foreach ($svatky as $s) {
                if ($s['month'] == $month && $s['day'] == $day) {
                    $this->svatek_add++;
                    $date = date('Y-m-d', strtotime($date . "+1 weekdays"));
                }
            }
            return $date;
        }

        private
        function ListAllProducts()
        {
            $debug_query = (isset($_GET['debug'])) ? true : false;
            $this->id = (isset($_GET['id'])) ? (int)preg_replace('/[^0-9]/', '', $_GET['id']) : 0;
            $this->begin = (isset($_GET['begin'])) ? (int)preg_replace('/[^0-9]/', '', $_GET['begin']) : 0;
            $this->end = (isset($_GET['end'])) ? (int)preg_replace('/[^0-9]/', '', $_GET['end']) : 100000;

            if (!empty($this->begin)) {
                $this->end -= $this->begin;
            }
            $this->open = (isset($_GET['open'])) ? true : false;


            if (isset($_GET['glami'])) {
                $sql = 'SELECT local_id, glami_category_name, lang FROM shaim_glami WHERE export = 1;';
                $tmp_glami_category_whitelist = $this->QueryFA($sql, false, 'assoc');
                foreach ($tmp_glami_category_whitelist as $tmp) {
                    $this->glami_category_whitelist[$tmp['local_id']] = $tmp['local_id'];
                    $this->glami_category_pair[$tmp['local_id']][$tmp['lang']] = $tmp['glami_category_name'];
                }
            } elseif (isset($_GET['pricemania'])) {
                $sql = 'SELECT local_id, pricemania_category_name, lang FROM shaim_pricemania WHERE export = 1;';
                $tmp_pricemania_category_whitelist = $this->QueryFA($sql, false, 'assoc');
                foreach ($tmp_pricemania_category_whitelist as $tmp) {
                    $this->pricemania_category_whitelist[$tmp['local_id']] = $tmp['local_id'];
                    $this->pricemania_category_pair[$tmp['local_id']][$tmp['lang']] = $tmp['pricemania_category_name'];
                }
            }


            // tohle a.active = 1 obcas na MT nevyexportovalo vsechno, kdyz to bylo v aLL SHOPS VYPNUTO.
            // $where = "WHERE c.name NOT LIKE '%bazar%' && c.name NOT LIKE '%použit%' && c.name NOT LIKE '%rozbalen%' && c.name != '' && c.name NOT LIKE '%e-cigaret%' && c.name NOT LIKE '%ecigaret%' && a.active = 1";
            $where = "WHERE c.name NOT LIKE '%bazar%' && c.name NOT LIKE '%použit%' && c.name NOT LIKE '%rozbalen%' && c.name != '' && c.name NOT LIKE '%e-cigaret%' && c.name NOT LIKE '%ecigaret%'";

            $where .= ($this->debug_id_product) ? " && a.id_product = {$this->debug_id_product}" : '';


            if (!empty($this->blacklist_product)) {
                $where .= ' && (a.id_product != ' . implode(' && a.id_product != ', $this->blacklist_product) . ')';
            }
// 1.6.x / 1.7.x


            if (version_compare(_PS_VERSION_, '1.6', '>=')) {
                $where .= ' && a.available_for_order = 1 && a.visibility != "none"';

                // LEFT JOIN stock_available as f ON (f.id_product = a.id_product && f.id_product_attribute = 0 && f.id_shop = {$this->id_shop})
                // u MT to zlobilo, protoze sklad je vetsinou na ID 0...


                $sql = "SELECT version FROM module WHERE name = 'shaim_export';";
                $module_version = $this->QueryR($sql);
                if (!isset($_GET['glami']) && !empty($module_version) && version_compare($module_version, '1.7.7', '>=') && file_exists(_PS_OVERRIDE_DIR_ . 'classes/Product.php') && preg_match("/shaim_export_active/", file_get_contents(_PS_OVERRIDE_DIR_ . 'classes/Product.php'))) {
                    $where .= ' && ps.shaim_export_active = 1';
                    $add_override_columns = 'a.shaim_export_name, a.shaim_export_gifts,';
                } else {
                    $add_override_columns = '';
                }


                if (version_compare(_PS_VERSION_, '1.6.1.0', '>=')) {
                    $sql = "
SELECT
a.ean13 as ean, a.reference, a.reference, a.supplier_reference, a.weight, a.is_virtual, a.available_date, c.available_later,$add_override_columns
b.name as manufacturer,
c.name, c.description, c.description_short, c.link_rewrite,
is.id_image as main_image,
tax.rate as vat,
ps.price, ps.active, ps.id_product, ps.id_category_default, ps.wholesale_price, ps.id_tax_rules_group, ps.condition
FROM product as a
LEFT JOIN manufacturer as b ON (b.id_manufacturer = a.id_manufacturer)
INNER JOIN product_lang as c ON (c.id_product = a.id_product && c.id_lang = {$this->id_lang} && c.id_shop = {$this->id_shop})
LEFT JOIN image_shop as `is` ON (is.id_product = a.id_product && is.cover = 1 && is.id_shop = {$this->id_shop})
INNER JOIN product_shop as ps ON (ps.id_product = a.id_product && ps.id_shop = {$this->id_shop} && ps.active = 1)
LEFT JOIN tax_rule as e ON (e.id_tax_rules_group = ps.id_tax_rules_group && e.id_country = " . COUNTRY_DEFAULT . ")
LEFT JOIN tax as tax ON (e.id_tax = tax.id_tax && tax.active = 1 && tax.deleted = 0)
$where
ORDER BY
a.id_product ASC
LIMIT {$this->begin},{$this->end}
                ";
                } else {
                    $sql = "
SELECT
a.ean13 as ean, a.reference, a.reference, a.supplier_reference, a.weight, a.is_virtual, a.available_date, c.available_later,$add_override_columns
b.name as manufacturer,
c.name, c.description, c.description_short, c.link_rewrite,
is.id_image as main_image,
tax.rate as vat,
ps.price, ps.active, ps.id_product, ps.id_category_default, ps.wholesale_price, ps.id_tax_rules_group, ps.condition
FROM product as a
LEFT JOIN manufacturer as b ON (b.id_manufacturer = a.id_manufacturer)
INNER JOIN product_lang as c ON (c.id_product = a.id_product && c.id_lang = {$this->id_lang} && c.id_shop = {$this->id_shop})
LEFT JOIN image as d ON (d.id_product = a.id_product && d.cover = 1)
LEFT JOIN image_shop as `is` ON (is.id_image = d.id_image && is.cover = 1 && is.id_shop = {$this->id_shop})
INNER JOIN product_shop as ps ON (ps.id_product = a.id_product && ps.id_shop = {$this->id_shop} && ps.active = 1)
LEFT JOIN tax_rule as e ON (e.id_tax_rules_group = ps.id_tax_rules_group && e.id_country = " . COUNTRY_DEFAULT . ")
LEFT JOIN tax as tax ON (e.id_tax = tax.id_tax && tax.active = 1 && tax.deleted = 0)
$where
ORDER BY
a.id_product ASC
LIMIT {$this->begin},{$this->end}
                ";
                }

                // f.quantity, f.out_of_stock as out_of_stock_real,
                // away, zlobilo to na MT
                // LEFT JOIN stock_available as f ON (f.id_product = a.id_product && f.id_product_attribute = 0)
            } elseif (version_compare(_PS_VERSION_, '1.5', '>=')) {
                $where .= ' && a.available_for_order = 1 && a.visibility != "none"';
                $sql = "SELECT version FROM module WHERE name = 'shaim_export';";
                $module_version = $this->QueryR($sql);
                if (!isset($_GET['glami']) && !empty($module_version) && version_compare($module_version, '1.7.7', '>=') && file_exists(_PS_OVERRIDE_DIR_ . 'classes/Product.php') && preg_match("/shaim_export_active/", file_get_contents(_PS_OVERRIDE_DIR_ . 'classes/Product.php'))) {
                    $where .= ' && ps.shaim_export_active = 1';
                    $add_override_columns = 'a.shaim_export_name, a.shaim_export_gifts,';
                } else {
                    $add_override_columns = '';
                }

                $sql = "
SELECT
a.ean13 as ean, a.reference, a.reference, a.supplier_reference, a.weight, a.is_virtual, a.available_date, c.available_later,$add_override_columns
b.name as manufacturer,
c.name, c.description, c.description_short, c.link_rewrite,
`is`.id_image as main_image,
e.rate as vat,
ps.price, ps.active, ps.id_product, ps.id_category_default, ps.wholesale_price, ps.id_tax_rules_group, ps.condition
FROM product as a
LEFT JOIN manufacturer as b ON (b.id_manufacturer = a.id_manufacturer)
INNER JOIN product_lang as c ON (c.id_product = a.id_product && c.id_lang = {$this->id_lang} && c.id_shop = {$this->id_shop})
LEFT JOIN image as d ON (d.id_product = a.id_product && d.cover = 1)
LEFT JOIN image_shop as `is` ON (is.id_image = d.id_image && is.cover = 1 && is.id_shop = {$this->id_shop})
INNER JOIN product_shop as ps ON (ps.id_product = a.id_product && ps.id_shop = {$this->id_shop} && ps.active = 1)
LEFT JOIN tax as e ON (e.id_tax = ps.id_tax_rules_group && e.active = 1)
$where
ORDER BY
a.id_product ASC
LIMIT {$this->begin},{$this->end}
                ";
                // f.quantity, f.out_of_stock as out_of_stock_real,
                // away, zlobilo to na MT
                // LEFT JOIN stock_available as f ON (f.id_product = a.id_product && f.id_product_attribute = 0)
// 1.4.x
            } else {
                die("Unsupported version!");
            }


// Přepsat na numrows (mby later)

            $find = array("/SELECT(.*)FROM/ims", "/LIMIT(.*);/ims");
            $replace = array("SELECT COUNT(*) as pocet FROM", "LIMIT 0,1000000;");
            $sql2 = preg_replace($find, $replace, $sql);

            echo PHP_EOL . 'Celkem dle query: ' . $this->QueryR($sql2) . PHP_EOL;

            return $this->QueryFA($sql, $debug_query, 'assoc');

        }

        public
        function FirstStep($custom = array())
        {
            if (!empty($custom)) {
                $this->feeds = $custom;
            }
            file_put_contents(XML_FOLDER . PROGRESS_FILE, '');
            foreach ($this->feeds as $feed) {

                switch ($feed) {
                    case 'google_com':
                        $header = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>
<feed xmlns=\"http://www.w3.org/2005/Atom\" xmlns:g=\"http://base.google.com/ns/1.0\" encoding=\"UTF-8\">
<title>" . WEB . "</title>
<link href=\"" . HTTPS . WEB . "/\" rel=\"alternate\" type=\"text/html\"/>
<modified>" . date("Y-m-d H:i:s") . "</modified>
<updated>" . date("Y-m-d H:i:s") . "</updated>
<author><name>" . WEB . "</name></author>" . NEW_LINE;
                        break;
                    case 'facebook_com':
                        $header = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>
<rss xmlns:g=\"http://base.google.com/ns/1.0\" version=\"2.0\">
<channel>
<title>" . WEB . "</title>
<link href=\"" . HTTPS . WEB . "/\" rel=\"alternate\" type=\"text/html\"/>
<modified>" . date("Y-m-d H:i:s") . "</modified>
<updated>" . date("Y-m-d H:i:s") . "</updated>
<author><name>" . WEB . "</name></author>" . NEW_LINE;
                        break;

                    case 'glami_cz':
                    case 'glami_sk':
                    case 'hledejceny_cz':
                    case 'shopalike_sk':
                    case 'shopalike_cz':
                    case 'heureka_cz':
                    case 'heureka_sk':
                        $header = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>" . NEW_LINE . "<SHOP>" . NEW_LINE;
                        break;
                    case 'pricemania_cz':
                    case 'pricemania_sk':
                        $header = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>" . NEW_LINE . "<products>" . NEW_LINE;
                        break;
                    case 'zbozi_cz':
                        $header = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>" . NEW_LINE . '<SHOP xmlns="http://www.zbozi.cz/ns/offer/1.0">' . NEW_LINE;
                        break;
                    case 'heureka_dostupnost':
                        $header = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>" . NEW_LINE . "<item_list>" . NEW_LINE;
                        break;
                }

                /*
                $this->force_lang_add = '';
                if ($this->force_lang) {
                    $this->force_lang_add = '_' . $this->force_lang;
                }
                */

                for ($i = 1; $i <= 5; $i++) {
                    file_put_contents(XML_FOLDER . $feed . $this->force_lang_add . $this->add_id_shop . '-' . $i . '.xml', '');
                }

                file_put_contents(XML_FOLDER . $feed . $this->force_lang_add . $this->add_id_shop . '-tmp.xml', $header);


                file_put_contents(XML_FOLDER . PROGRESS_FILE, $feed . NEW_LINE, FILE_APPEND);

            }
        }

        private
        function SecondStep($feed)
        {

            switch ($feed) {
                case 'google_com':

                    $tag = 'entry';
                    $close_tag = '';
                    break;

                case 'facebook_com':
                    $tag = 'item';
                    $close_tag = '';
                    break;
                case 'glami_cz':
                case 'glami_sk':
                case 'zbozi_cz':
                case 'heureka_cz':
                case 'hledejceny_cz':
                case 'shopalike_sk':
                case 'shopalike_cz':
                case 'heureka_sk':
                    $tag = 'SHOPITEM';
                    $close_tag = '';
                    break;
                case 'pricemania_cz':
                case 'pricemania_sk':
                    $tag = 'product';
                    $close_tag = '';
                    break;
                case 'heureka_dostupnost':
                    $tag = 'item id="' . $this->item_id . '"';
                    $close_tag = 'item';
                    break;
            }

            $this->xml = NEW_LINE . $this->AddTag($tag, $this->xml, $close_tag);
            /*
            $this->force_lang_add = '';
            if ($this->force_lang) {
                $this->force_lang_add = '_' . $this->force_lang;
            }
            */

            if (!file_exists(XML_FOLDER . $feed . $this->force_lang_add . $this->add_id_shop . '-1.xml')) {
                die('Chyba: Nejdříve musíte otevřít soubory!');
            }


            if (empty($this->id)) {

                for ($i = 1; $i <= 5; $i++) {

                    clearstatcache();
                    if (filesize(XML_FOLDER . $feed . $this->force_lang_add . $this->add_id_shop . '-' . $i . '.xml') == 0) {
                        $this->id = $i;
                        break;
                    }
                }
                if (empty($this->id)) {
                    die('Chyba: Neočekávaná chyba, nesprávné použití modulu!');
                }
            }


            file_put_contents(XML_FOLDER . $feed . $this->force_lang_add . $this->add_id_shop . '-' . $this->id . '.xml', $this->xml, FILE_APPEND);
            unset($this->xml);

        }

        public
        function ThirdStep($custom = array())
        {

            if (!empty($custom)) {
                $this->feeds = $custom;
            }
            foreach ($this->feeds as $feed) {

                switch ($feed) {
                    case 'google_com':
                        $footer = "</feed>";
                        break;
                    case 'facebook_com':
                        $footer = "</channel></rss>";
                        break;
                    case 'glami_cz':
                    case 'glami_sk':
                    case 'zbozi_cz':
                    case 'hledejceny_cz':
                    case 'shopalike_sk':
                    case 'shopalike_cz':
                    case 'heureka_cz':
                    case 'heureka_sk':
                        $footer = "</SHOP>";
                        break;
                    case 'pricemania_cz':
                    case 'pricemania_sk':
                        $footer = "</products>";
                        break;
                    case 'heureka_dostupnost':
                        $footer = "</item_list>";
                        break;
                }
                /*
                $this->force_lang_add = '';
                if ($this->force_lang) {
                    $this->force_lang_add = '_' . $this->force_lang;
                }
                */

                if (!file_exists(XML_FOLDER . $feed . $this->force_lang_add . $this->add_id_shop . '-1.xml')) {
                    die('Chyba: Nejdříve musíte otevřít soubory!');
                }


                for ($i = 1; $i <= 5; $i++) {
                    clearstatcache();

                    if (filesize(XML_FOLDER . $feed . $this->force_lang_add . $this->add_id_shop . '-' . $i . '.xml') > 0) {

                        // file_get_contents může umírat na memory_limit, když se jedná o moc velké soubory, proto již není používáno.
                        // file_put_contents(XML_FOLDER . $feed .  $this->add_id_shop.'.xml', file_get_contents(XML_FOLDER . $feed .  $this->add_id_shop. '-' . $i .'.xml'), FILE_APPEND);
                        $handle = fopen(XML_FOLDER . $feed . $this->force_lang_add . $this->add_id_shop . '-' . $i . '.xml', 'r');

                        while (!feof($handle)) {
                            file_put_contents(XML_FOLDER . $feed . $this->force_lang_add . $this->add_id_shop . '-tmp.xml', fread($handle, 8192), FILE_APPEND);
                        }
                        fclose($handle);
                    }

                    unlink(XML_FOLDER . $feed . $this->force_lang_add . $this->add_id_shop . '-' . $i . '.xml');

                }


                file_put_contents(XML_FOLDER . $feed . $this->force_lang_add . $this->add_id_shop . '-tmp.xml', $footer, FILE_APPEND);

                rename(XML_FOLDER . $feed . $this->force_lang_add . $this->add_id_shop . '-tmp.xml', XML_FOLDER . $feed . $this->force_lang_add . $this->add_id_shop . '.xml');
                // Zakomentováno, neustále s tím měla heureka_cz a zboží problémy
                //     $this->GzipXML($feed);

            }
        }

        /*
                private function GzipXML($feed)
                {

                    // Tento feed není akceptovatelný pro dostupnostní xml
                    if ($feed == 'heureka_dostupnost') {
                        return;
                    }
                    $fp = gzopen(XML_FOLDER . $feed .  $this->add_id_shop.'.xml' . '.gz', 'w9');
                    // file_get_contents může umírat na memory_limit, když se jedná o moc velké soubory, proto již není používáno.
                    //   gzwrite($fp, file_get_contents(XML_FOLDER . $feed .  $this->add_id_shop.'.xml'));

                    $handle = fopen(XML_FOLDER . $feed .$this->force_lang_add. $this->add_id_shop. '.xml', "r");

                    while (!feof($handle)) {
                        gzwrite($fp, fread($handle, 8192));
                    }
                    fclose($handle);

                    gzclose($fp);
                }
        */
        private
        function ShortProductNameFix()
        {
            return $this->add = (strlen($this->row['name']) <= 2) ? false : true;
        }

        private
        function GetMainImage()
        {


            if (empty($this->row['main_image'])) {
                //    return $this->alternative_main_image;
                return false;
            }

            /*
            $image = '';
            for ($i = 0; $i < strlen($this->row['main_image']); $i++) {
                $image .= $this->row['main_image'][$i] . '/';
            }
            */

            return $this->GetImagePath($this->row['main_image']);

        }

        private
        function AddParams()
        {
            $params = '';

            if (isset($this->cache_params[$this->row['id_product']])) {
                $glami_exists_size = false;
                $glami_type_size = false;
                foreach ($this->cache_params[$this->row['id_product']] as $row) {
                    $name = trim((substr($row['name'], -1) == ':') ? substr($row['name'], 0, -1) : $row['name']); #  Delete char : at end of string #
                    $value = trim($row['value']);

                    if (isset($_GET['glami']) && ($name == 'Varianta' || preg_match("/Velikost/i", $name))) {
                        $name = 'Velikost';
                    } elseif (isset($_GET['glami']) && preg_match("/materiál/i", $name)) {
                        $name = 'Materiál';
                    }

                    if (isset($_GET['glami']) && ($name == 'Velikost' || $name == 'Veľkosť')) {
                        $glami_exists_size = true;

                        if (preg_match("/[^0-9.]/", $value)) {
                            $glami_type_size = 'INT';
                        } elseif ($value >= 30) {
                            $glami_type_size = 'EU';
                        } elseif ($value <= 15) {
                            // US nebo UK, nemame jak poznat
                            $glami_type_size = 'US';
                            // $glami_type_size = 'UK';
                        }
                    }

                    /* google extra tagy */
                    $name_tmp = mb_strtolower($name);
                    if ($name_tmp == 'pohlaví' || $name_tmp == 'pohlavie') {
                        $this->x->google_extra .= $this->AddTag('g:gender', $this->AddCDATA($value));
                    } else if ($name_tmp == 'barva' || $name_tmp == 'farba') {
                        $this->x->google_extra .= $this->AddTag('g:color', $this->AddCDATA($value));
                    } elseif (preg_match("/materiál/i", $name_tmp)) {
                        $this->x->google_extra .= $this->AddTag('g:material', $this->AddCDATA($value));
                    }

                    if (isset($_GET['glami']) && $name == 'Materiál') {
                        $values = explode(',', $value);
                        $values = array_map('trim', $values);
                        foreach ($values as $value_new) {
                            $params .= "<PARAM>" . NEW_LINE .
                                $this->AddTag('PARAM_NAME', $this->AddCDATA($name)) .
                                $this->AddTag('VAL', $this->AddCDATA($value_new)) .
                                "</PARAM>" . NEW_LINE;
                        }
                    } else {
                        $params .= "<PARAM>" . NEW_LINE .
                            $this->AddTag('PARAM_NAME', $this->AddCDATA($name)) .
                            $this->AddTag('VAL', $this->AddCDATA($value)) .
                            "</PARAM>" . NEW_LINE;
                    }
                }

                // Podporované hodnoty jsou: AU, BR, KN, DE, EU, FR, INT, IT, JP, MEX, RU, UK, USA. Vždy uveďte v parametru jen jednu hodnotu. Pro mezinárodní velikost XS, S, M, L, atd. použijte v parametru hodnotu INT.
                if ($glami_exists_size && $glami_type_size) {
                    $params .= "<PARAM>" . NEW_LINE .
                        $this->AddTag('PARAM_NAME', 'SIZE_SYSTEM') .
                        $this->AddTag('VAL', $glami_type_size) .
                        "</PARAM>" . NEW_LINE;
                }
            }

            return $params;
        }

        private
        function AddSKParams()
        {
            $params = '';

            if (isset($this->cache_params_sk[$this->row['id_product']])) {
                $use = $this->cache_params_sk[$this->row['id_product']];
            } elseif (isset($this->cache_params[$this->row['id_product']])) {
                $use = $this->cache_params[$this->row['id_product']];
            } else {
                return $params;
            }


            $glami_exists_size = false;
            $glami_type_size = false;
            foreach ($use as $row) {
                $name = trim((substr($row['name'], -1) == ':') ? substr($row['name'], 0, -1) : $row['name']); #  Delete char : at end of string #
                $value = trim($row['value']);

                if (isset($_GET['glami']) && ($name == 'Varianta' || preg_match("/Velikost/i", $name))) {
                    $name = 'Veľkosť';
                } elseif (isset($_GET['glami']) && preg_match("/materiál/i", $name)) {
                    $name = 'Materiál';
                }

                if (isset($_GET['glami']) && ($name == 'Velikost' || $name == 'Veľkosť')) {
                    $glami_exists_size = true;

                    if (preg_match("/[^0-9.]/", $value)) {
                        $glami_type_size = 'INT';
                    } elseif ($value >= 30) {
                        $glami_type_size = 'EU';
                    } elseif ($value <= 15) {
                        // US nebo UK, nemame jak poznat
                        $glami_type_size = 'US';
                        // $glami_type_size = 'UK';
                    }
                }
                if (isset($_GET['glami']) && $name == 'Materiál') {
                    $values = explode(',', $value);
                    $values = array_map('trim', $values);
                    foreach ($values as $value_new) {
                        $params .= "<PARAM>" . NEW_LINE .
                            $this->AddTag('PARAM_NAME', $this->AddCDATA($name)) .
                            $this->AddTag('VAL', $this->AddCDATA($value_new)) .
                            "</PARAM>" . NEW_LINE;
                    }
                } else {
                    $params .= "<PARAM>" . NEW_LINE .
                        $this->AddTag('PARAM_NAME', $this->AddCDATA($name)) .
                        $this->AddTag('VAL', $this->AddCDATA($value)) .
                        "</PARAM>" . NEW_LINE;
                }
            }

            // Podporované hodnoty jsou: AU, BR, KN, DE, EU, FR, INT, IT, JP, MEX, RU, UK, USA. Vždy uveďte v parametru jen jednu hodnotu. Pro mezinárodní velikost XS, S, M, L, atd. použijte v parametru hodnotu INT.
            if ($glami_exists_size && $glami_type_size) {
                $params .= "<PARAM>" . NEW_LINE .
                    $this->AddTag('PARAM_NAME', 'SIZE_SYSTEM') .
                    $this->AddTag('VAL', $glami_type_size) .
                    "</PARAM>" . NEW_LINE;
            }


            return $params;
        }

        private
        function AddVariants()
        {
            $this->variants_add = array();


            // memory_issue_fix
            // $this->CacheVariants($this->row['id_product']);
            if (isset($this->cache_variants[$this->row['id_product']])) {
                foreach ($this->cache_variants[$this->row['id_product']] as $row) {


                    if (DECIMALS_DEFAULT == 0) {
                        $rt = ROUND_TYPE;
                        $variant_price = $rt(($this->row['vat'] != NULL) ? parent::AddVat($row['price'], $this->row['vat']) : $row['price']);
                        $variant_price_without_vat = $rt($row['price']);
                    } else {
                        $variant_price = number_format(($this->row['vat'] != NULL) ? parent::AddVat($row['price'], $this->row['vat']) : $row['price'], DECIMALS_DEFAULT, '.', '');
                        $variant_price_without_vat = number_format($row['price'], DECIMALS_DEFAULT, '.', '');
                    }


                    if (!empty($variant_price)) {
                        // For price fix, aby nebyla nula
                        $this->variant_exists = true;
                    }


                    if (version_compare(_PS_VERSION_, '1.6', '>=')) {
                        $variant_url_part = '/' . $row['id_attribute'] . '-' . $this->str2url($row['name'], '_') . '-' . $this->str2url($row['value'], '-');
                    } else {
                        $variant_url_part = '/' . $this->str2url($row['name'], '_') . '-' . $this->str2url($row['value'], '-');
                    }

                    if (isset($_GET['glami']) && ($row['name'] == 'Varianta' || preg_match("/Velikost/i", $row['name']))) {
                        $row['name'] = 'Velikost';
                    } elseif (isset($_GET['glami']) && preg_match("/materiál/i", $row['name'])) {
                        $row['name'] = 'Materiál';
                    }

                    /* google extra tagy */
                    $name_tmp = mb_strtolower($row['name']);
                    if ($name_tmp == 'pohlaví' || $name_tmp == 'pohlavie') {
                        $this->x->google_extra .= $this->AddTag('g:gender', $this->AddCDATA($row['value']));
                    } else if ($name_tmp == 'barva' || $name_tmp == 'farba') {
                        $this->x->google_extra .= $this->AddTag('g:color', $this->AddCDATA($row['value']));
                    } elseif (preg_match("/materiál/i", $name_tmp)) {
                        $this->x->google_extra .= $this->AddTag('g:material', $this->AddCDATA($row['value']));
                    }

                    if (!isset($this->variants_add[$row['id_product_attribute']])) {
                        $this->variants_add[$row['id_product_attribute']] = array(
                            'reference' => $row['reference'],
                            'name' => $row['name'],
                            'value' => $row['value'],
                            'weight' => $row['weight'] * 1000,
                            'ean' => $row['ean13'],
                            'price' => $variant_price,
                            'price_without_vat' => $variant_price_without_vat,
                            'id_product_attribute' => $row['id_product_attribute'],
                            'url_add' => $variant_url_part,
                            'available_date' => $row['available_date'],
                        );
                    } else {
                        $this->variants_add[$row['id_product_attribute']]['reference'] = $row['reference'];
                        $this->variants_add[$row['id_product_attribute']]['name'] .= '###' . $row['name'];
                        $this->variants_add[$row['id_product_attribute']]['value'] .= '###' . $row['value'];
                        $this->variants_add[$row['id_product_attribute']]['weight'] = $row['weight'];
                        $this->variants_add[$row['id_product_attribute']]['ean'] = $row['ean13'];
                        $this->variants_add[$row['id_product_attribute']]['url_add'] .= $variant_url_part;
                        $this->variants_add[$row['id_product_attribute']]['available_date'] = $row['available_date'];
                    }


                }

                $this->variants_add = array_values($this->variants_add);
            }


        }

        private
        function AddSKVariants()
        {
            $this->variants_sk_add = array();


            // memory_issue_fix
            // $this->CacheVariants($this->row['id_product']);
            if (isset($this->cache_sk_variants[$this->row['id_product']])) {
                foreach ($this->cache_sk_variants[$this->row['id_product']] as $row) {


                    if (DECIMALS_DEFAULT == 0) {
                        $rt = ROUND_TYPE;
                        $variant_price = $rt(($this->row['vat'] != NULL) ? parent::AddVat($row['price'], $this->row['vat']) : $row['price']);
                        $variant_price_without_vat = $rt($row['price']);
                    } else {
                        $variant_price = number_format(($this->row['vat'] != NULL) ? parent::AddVat($row['price'], $this->row['vat']) : $row['price'], DECIMALS_DEFAULT, '.', '');
                        $variant_price_without_vat = number_format($row['price'], DECIMALS_DEFAULT, '.', '');
                    }


                    if (!empty($variant_price)) {
                        // For price fix, aby nebyla nula
                        $this->variant_exists = true;
                    }


                    if (version_compare(_PS_VERSION_, '1.6', '>=')) {
                        $variant_url_part = '/' . $row['id_attribute'] . '-' . $this->str2url($row['name'], '_') . '-' . $this->str2url($row['value'], '-');
                    } else {
                        $variant_url_part = '/' . $this->str2url($row['name'], '_') . '-' . $this->str2url($row['value'], '-');
                    }

                    if (isset($_GET['glami']) && ($row['name'] == 'Varianta' || preg_match("/Velikost/i", $row['name']))) {
                        $row['name'] = 'Velikost';
                    } elseif (isset($_GET['glami']) && preg_match("/materiál/i", $row['name'])) {
                        $row['name'] = 'Materiál';
                    }

                    if (!isset($this->variants_sk_add[$row['id_product_attribute']])) {
                        $this->variants_sk_add[$row['id_product_attribute']] = array(
                            'reference' => $row['reference'],
                            'name' => $row['name'],
                            'value' => $row['value'],
                            'weight' => $row['weight'] * 1000,
                            'ean' => $row['ean13'],
                            'price' => $variant_price,
                            'price_without_vat' => $variant_price_without_vat,
                            'id_product_attribute' => $row['id_product_attribute'],
                            'url_add' => $variant_url_part,
                            'available_date' => $row['available_date'],
                        );
                    } else {
                        $this->variants_sk_add[$row['id_product_attribute']]['reference'] = $row['reference'];
                        $this->variants_sk_add[$row['id_product_attribute']]['name'] .= '###' . $row['name'];
                        $this->variants_sk_add[$row['id_product_attribute']]['value'] .= '###' . $row['value'];
                        $this->variants_sk_add[$row['id_product_attribute']]['weight'] = $row['weight'];
                        $this->variants_sk_add[$row['id_product_attribute']]['ean'] = $row['ean13'];
                        $this->variants_sk_add[$row['id_product_attribute']]['url_add'] .= $variant_url_part;
                        $this->variants_sk_add[$row['id_product_attribute']]['available_date'] = $row['available_date'];
                    }


                }

                $this->variants_sk_add = array_values($this->variants_sk_add);
            }


        }


        /*
        private
        function DeliveryToHeurekaFormat($data)
        {

            if (preg_match("/posta/i", $data) || preg_match("/pošta/i", $data)) {
                $delivery_id = 'CESKA_POSTA';
            } elseif (preg_match("/poštu/i", $data) || preg_match("/postu/i", $data)) {
                $delivery_id = 'CESKA_POSTA_NA_POSTU';
            } elseif (preg_match("/ČSAD/i", $data) || preg_match("/CSAD/i", $data)) {
                $delivery_id = 'CSAD_LOGISTIK_OSTRAVA';
            } elseif (preg_match("/DPD/i", $data)) {
                $delivery_id = 'DPD';
            } elseif (preg_match("/DHL/i", $data)) {
                $delivery_id = 'DHL';
            } elseif (preg_match("/EMS/i", $data)) {
                $delivery_id = 'EMS';
            } elseif (preg_match("/FOFR/i", $data)) {
                $delivery_id = 'FOFR';
            } elseif (preg_match("/Weiss/i", $data)) {
                $delivery_id = 'GEBRUDER_WEISS';
            } elseif (preg_match("/Geis/i", $data)) {
                $delivery_id = 'GEIS';
            } elseif (preg_match("/General/i", $data)) {
                $delivery_id = 'GENERAL_PARCEL';
            } elseif (preg_match("/GLS/i", $data)) {
                $delivery_id = 'GLS';
            } elseif (preg_match("/HDS/i", $data)) {
                $delivery_id = 'HDS';
            } elseif (preg_match("/Heureka/i", $data)) {
                $delivery_id = 'HEUREKAPOINT';
            } elseif (preg_match("/Time/i", $data)) {
                $delivery_id = 'INTIME';
            } elseif (preg_match("/PPL/i", $data)) {
                $delivery_id = 'PPL';
            } elseif (preg_match("/Radiálka/i", $data)) {
                $delivery_id = 'RADIALKA';
            } elseif (preg_match("/Seeg/i", $data)) {
                $delivery_id = 'SEEGMULLER';
            } elseif (preg_match("/TNT/i", $data)) {
                $delivery_id = 'TNT';
            } elseif (preg_match("/TOPTRANS/i", $data)) {
                $delivery_id = 'TOPTRANS';
            } elseif (preg_match("/UPS/i", $data)) {
                $delivery_id = 'UPS';
            } elseif (preg_match("/VLASTNI_PREPRAVA/i", $data)) {
                $delivery_id = 'VLASTNI_PREPRAVA';
            } else {
                $delivery_id = false;
            }
            return $delivery_id;
        }
*/
        private
        function AddDelivery($price_of_product = 0, $weight = 0)
        {

            $this->x->google_delivery = '';
            $this->x->zbozi_delivery_cz = '';
            $this->x->heureka_delivery_cz = '';
            $this->x->heureka_delivery_sk = '';


            $zbozi_specific_dopravci = array(
                'CZ_GEIS_POINT', 'CZ_GLS_PARCELSHOP', 'CZ_PPL_PARCELSHOP', 'CZ_TOPTRANS_DEPO', 'CZ_DB_SCHENKER', 'CZ_MESSENGER', 'CZ_RHENUS', 'CZ_VLASTNI_VYDEJNI_MISTA',
            );
            // Heureka delivery
            if (!empty($this->carriers)) {
                $delivery = array('cz' => '', 'sk' => '');
                $delivery_zbozi = array('cz' => '');
                foreach ($this->carriers as $carrier) {
                    $name = $carrier['name'];
                    $price = $carrier['price'];
                    $price_cod = $carrier['price_cod'];
                    $free = (isset($carrier['free']) ? $carrier['free'] : 0);
                    if (($price == '-1' && $price_cod == '-1') || empty($name)) {
                        continue;
                    }
                    $lang_version = 'cz';
                    if (preg_match("/SK_/", $name)) {
                        $lang_version = 'sk';
                    }

                    $zbozi_only = false;

                    if (in_array($name, $zbozi_specific_dopravci)) {
                        $zbozi_only = true;
                    }

                    $name = strtr($name, array('CZ_' => '', 'CZ_ZBOZI_' => '', 'SK_' => ''));
                    $tmp_delivery = '<DELIVERY>';
                    $tmp_delivery .= $this->AddTag('DELIVERY_ID', $name);
                    if ($price != '-1') {
                        if (FREE_SHIPPING > 0 && $price_of_product >= FREE_SHIPPING) {
                            $price = 0;
                        } elseif ($free > 0 && $price_of_product >= $free) {
                            $price = 0;
                        } elseif (FREE_WEIGHT > 0 && $weight >= FREE_WEIGHT) {
                            $price = 0;
                        }
                        $tmp_delivery .= $this->AddTag('DELIVERY_PRICE', $price);
                        /*
                                                $this->x->google_delivery .= "<g:shipping>" .
                                                    $this->AddTag('g:country', 'CZ') .
                                                    $this->AddTag('g:region', '') .
                                                    $this->AddTag('g:service', $name) .
                                                    $this->AddTag('g:price', $price . ' ' . MENA) .
                                                    "</g:shipping>" . NEW_LINE;
                        */
                    }
                    if ($price_cod != '-1') {
                        if (FREE_SHIPPING > 0 && $price_of_product >= FREE_SHIPPING) {
                            $price_cod = 0;
                        } elseif ($free > 0 && $price_of_product >= $free) {
                            $price_cod = 0;
                        } elseif (FREE_WEIGHT > 0 && $weight >= FREE_WEIGHT) {
                            $price_cod = 0;
                        }
                        $tmp_delivery .= $this->AddTag('DELIVERY_PRICE_COD', $price_cod);
                        /*
                        $this->x->google_delivery .= "<g:shipping>" .
                            $this->AddTag('g:country', 'CZ') .
                            $this->AddTag('g:region', '') .
                            $this->AddTag('g:service', $name . ' - dobírka') .
                            $this->AddTag('g:price', $price_cod . ' ' . MENA) .
                            "</g:shipping>" . NEW_LINE;
                        */

                    }
                    $tmp_delivery .= '</DELIVERY>';
                    if ($zbozi_only === true) {
                        $delivery_zbozi[$lang_version] .= $tmp_delivery;
                    } else {
                        $delivery[$lang_version] .= $tmp_delivery;
                        $delivery_zbozi[$lang_version] .= $tmp_delivery;
                    }

                }
                $this->x->heureka_delivery_cz = $delivery['cz'];
                $this->x->heureka_delivery_sk = $delivery['sk'];
                $this->x->zbozi_delivery_cz = $delivery_zbozi['cz'];

            }

            // Google delivery

            if (SHIPPING_PRICE >= 0 || SHIPPING_PRICE_COD >= 0) {
                $country = 'CZ';
                if (MENA == 'CZK') {
                    $country = 'CZ';
                } elseif (MENA == 'EUR') {
                    $country = 'SK';
                } elseif (MENA == 'USD') {
                    $country = 'US';
                }

                if (SHIPPING_PRICE >= 0) {
                    $postovne = str_replace(',', '.', SHIPPING_PRICE);
                    $postovne_sk = round(str_replace(',', '.', SHIPPING_PRICE) * CONVERSION_RATE_SK, 2);
                    $postovne_us = round(str_replace(',', '.', SHIPPING_PRICE) * CONVERSION_RATE_US, 2);
                    $postovne_gb = round(str_replace(',', '.', SHIPPING_PRICE) * CONVERSION_RATE_GB, 2);

                    if (FREE_SHIPPING > 0 && $price_of_product >= FREE_SHIPPING) {
                        $postovne = 0;
                        $postovne_sk = 0;
                        $postovne_gb = 0;
                        $postovne_us = 0;
                    } elseif (FREE_WEIGHT > 0 && $weight >= FREE_WEIGHT) {
                        $postovne = 0;
                        $postovne_sk = 0;
                        $postovne_gb = 0;
                        $postovne_us = 0;
                    }

                    if ($this->force_lang == 'sk') {
                        $this->x->google_delivery .= '<g:shipping>
   <g:country>SK</g:country>
   <g:region></g:region>
   <g:service>Standard</g:service>
   <g:price>' . $postovne_sk . ' EUR</g:price>
</g:shipping> ';
                    } elseif ($this->force_lang == 'de') {
                        $this->x->google_delivery .= '<g:shipping>
   <g:country>DE</g:country>
   <g:region></g:region>
   <g:service>Standard</g:service>
   <g:price>' . $postovne_sk . ' EUR</g:price>
</g:shipping> ';
                    } elseif ($this->force_lang == 'gb') {
                        $this->x->google_delivery .= '<g:shipping>
   <g:country>GB</g:country>
   <g:region></g:region>
   <g:service>Standard</g:service>
   <g:price>' . $postovne_gb . ' GBP</g:price>
</g:shipping> ';
                    } elseif ($this->force_lang == 'en') {
                        $this->x->google_delivery .= '<g:shipping>
   <g:country>US</g:country>
   <g:region></g:region>
   <g:service>Standard</g:service>
   <g:price>' . $postovne_us . ' USD</g:price>
</g:shipping> ';
                    } else {
                        $this->x->google_delivery .= '<g:shipping>
   <g:country>' . $country . '</g:country>
   <g:region></g:region>
   <g:service>Standard</g:service>
   <g:price>' . $postovne . ' ' . MENA . '</g:price>
</g:shipping> ';
                    }
                }

                if (SHIPPING_PRICE_COD >= 0) {
                    $postovne_cod = str_replace(',', '.', SHIPPING_PRICE_COD);
                    $postovne_cod_sk = round(str_replace(',', '.', SHIPPING_PRICE_COD) * CONVERSION_RATE_SK, 2);
                    $postovne_cod_us = round(str_replace(',', '.', SHIPPING_PRICE_COD) * CONVERSION_RATE_US, 2);
                    $postovne_cod_gb = round(str_replace(',', '.', SHIPPING_PRICE_COD) * CONVERSION_RATE_GB, 2);

                    if (FREE_SHIPPING > 0 && $price_of_product >= FREE_SHIPPING) {
                        $postovne_cod = 0;
                        $postovne_cod_sk = 0;
                        $postovne_cod_us = 0;
                        $postovne_cod_gb = 0;
                    } elseif (FREE_WEIGHT > 0 && $weight >= FREE_WEIGHT) {
                        $postovne_cod = 0;
                        $postovne_cod_sk = 0;
                        $postovne_cod_us = 0;
                        $postovne_cod_gb = 0;
                    }
                    if ($this->force_lang == 'sk') {
                        $this->x->google_delivery .= '<g:shipping>
   <g:country>SK</g:country>
   <g:region></g:region>
   <g:service>Standard - cash on delivery</g:service>
   <g:price>' . $postovne_cod_sk . ' EUR</g:price>
</g:shipping> ';
                    } elseif ($this->force_lang == 'de') {
                        $this->x->google_delivery .= '<g:shipping>
   <g:country>DE</g:country>
   <g:region></g:region>
   <g:service>Standard - cash on delivery</g:service>
   <g:price>' . $postovne_cod_sk . ' EUR</g:price>
</g:shipping> ';
                    } elseif ($this->force_lang == 'gb') {
                        $this->x->google_delivery .= '<g:shipping>
   <g:country>GB</g:country>
   <g:region></g:region>
   <g:service>Standard - cash on delivery</g:service>
   <g:price>' . $postovne_cod_gb . ' GBP</g:price>
</g:shipping> ';
                    } elseif ($this->force_lang == 'en') {
                        $this->x->google_delivery .= '<g:shipping>
   <g:country>US</g:country>
   <g:region></g:region>
   <g:service>Standard - cash on delivery</g:service>
   <g:price>' . $postovne_cod_us . ' USD</g:price>
</g:shipping> ';
                    } else {
                        $this->x->google_delivery .= '<g:shipping>
   <g:country>' . $country . '</g:country>
   <g:region></g:region>
   <g:service>Standard - cash on delivery</g:service>
   <g:price>' . $postovne_cod . ' ' . MENA . '</g:price>
</g:shipping> ';
                    }
                }

            }

            /*
            if (FREE_SHIPPING > 0 && $this->price_round >= FREE_SHIPPING) {
                $delivery .= ' <DELIVERY>
      <DELIVERY_ID>CESKA_POSTA</DELIVERY_ID>
      <DELIVERY_PRICE>0</DELIVERY_PRICE>
      <DELIVERY_PRICE_COD>0</DELIVERY_PRICE_COD>
    </DELIVERY>';
                         $delivery .= ' <DELIVERY>
      <DELIVERY_ID>DPD</DELIVERY_ID>
      <DELIVERY_PRICE>0</DELIVERY_PRICE>
      <DELIVERY_PRICE_COD>0</DELIVERY_PRICE_COD>
    </DELIVERY>';
            } else {
                $delivery .= ' <DELIVERY>
      <DELIVERY_ID>CESKA_POSTA</DELIVERY_ID>
      <DELIVERY_PRICE>89</DELIVERY_PRICE>
      <DELIVERY_PRICE_COD>134</DELIVERY_PRICE_COD>
    </DELIVERY>';
                     $delivery .= ' <DELIVERY>
      <DELIVERY_ID>DPD</DELIVERY_ID>
      <DELIVERY_PRICE>99</DELIVERY_PRICE>
      <DELIVERY_PRICE_COD>144</DELIVERY_PRICE_COD>
    </DELIVERY>';
            }
            */


//  return;

        }

        private
        function GetRoundType($roundtype = '')
        {

            if ($roundtype == 0) {
                return 'ceil';
            } elseif ($roundtype == 1) {
                return 'floor';
            } elseif ($roundtype == 2 || $roundtype == 3 || $roundtype == 4 || $roundtype == 5) {
                return 'round';
            } else {
                die('BAD ROUND TYPE!!');
            }

        }

        private
        function LoadSpecificPrice()
        {
            $date_now = date("Y-m-d H:i:s");
            $this->specific_price = array();
            $this->customer_group_reduction = 0;
            $reduction = (int)$this->QueryR("SELECT `reduction` FROM group WHERE (`id_group` = 1) && `reduction` > 0 ORDER BY `id_group` DESC;");
            if ($reduction > 0) {
                $this->customer_group_reduction = $reduction;
            }


            $add = '';
            // $add2 = '';
            if ($this->id_groups_wholesale) {
                $add = array();
                foreach ($this->id_groups_wholesale as $id_groups_wholesale) {
                    $add[] = "id_group != $id_groups_wholesale";
                }
                $add = '(' . implode(' && ', $add) . ') &&';
            }
            /*

                                    $add2 = ' id_product_attribute = 0 && ';

            */
            // $tmp = $this->QueryFA("SELECT id_product, reduction_type, reduction, price, id_currency FROM specific_price WHERE$add$add2 from_quantity <= 1 && id_cart = 0 && id_group = 0 && (id_shop = " . $this->id_shop . " || id_shop = 0) && ((`to` >= '$date_now' && `from` <= '$date_now') || (`to` >= '$date_now' && `from` = '0000-00-00 00:00:00')  || (`to` = '0000-00-00 00:00:00' && `from` <= '$date_now') || (`to` = '0000-00-00 00:00:00' && `from` = '0000-00-00 00:00:00')) ORDER BY reduction ASC;", false, 'assoc');

            //  && id_specific_price_rule = 0 - tohle jsme dali pryc, protoze to delalo to, ze kdyz nekdo mel cenu v katalogu, tak se to nepropsalo pak, nevim proc to tady bylo, uvidime....
            if (version_compare(_PS_VERSION_, '1.6.1.0', '>=')) { // Tady je reduction_tax navic
                $tmp = $this->QueryFA("SELECT id_specific_price_rule, id_product, id_product_attribute, reduction_type, reduction, price, id_currency, reduction_tax FROM specific_price WHERE$add from_quantity <= 1 && id_cart = 0 && (id_group = 0 || id_group = 1) && (id_shop = " . $this->id_shop . " || id_shop = 0) && ((`to` >= '$date_now' && `from` <= '$date_now') || (`to` >= '$date_now' && `from` = '0000-00-00 00:00:00')  || (`to` = '0000-00-00 00:00:00' && `from` <= '$date_now') || (`to` = '0000-00-00 00:00:00' && `from` = '0000-00-00 00:00:00')) ORDER BY id_specific_price_rule DESC, reduction ASC;", false, 'assoc');
            } else {
                $tmp = $this->QueryFA("SELECT id_specific_price_rule, id_product, id_product_attribute, reduction_type, reduction, price, id_currency FROM specific_price WHERE$add from_quantity <= 1 && id_cart = 0 && (id_group = 0 || id_group = 1) && (id_shop = " . $this->id_shop . " || id_shop = 0) && ((`to` >= '$date_now' && `from` <= '$date_now') || (`to` >= '$date_now' && `from` = '0000-00-00 00:00:00')  || (`to` = '0000-00-00 00:00:00' && `from` <= '$date_now') || (`to` = '0000-00-00 00:00:00' && `from` = '0000-00-00 00:00:00')) ORDER BY id_specific_price_rule DESC, reduction ASC;", false, 'assoc');
            }
            foreach ($tmp as $t) {
                if (!isset($this->specific_price[(int)$t['id_product']])) {
                    $this->specific_price[(int)$t['id_product']] = array();
                }

                $this->specific_price[(int)$t['id_product']][(int)$t['id_product_attribute']][] = array(
                    'id_specific_price_rule' => $t['id_specific_price_rule'],
                    'reduction_type' => $t['reduction_type'],
                    'reduction' => $t['reduction'],
                    'price' => $t['price'],
                    'id_currency' => (int)$t['id_currency'],
                    'reduction_tax' => isset($t['reduction_tax']) ? $t['reduction_tax'] : 1, //  s DPH nebo bez DPH
                );
            }


        }

        private
        function CountSpecificPrice($id_product, $price_with_vat, $price_without_vat, $id_product_attribute = 0)
        {

            // Kvuli specialnim cenam pro katalog
// koberec hint
            /*
            if (isset($this->specific_price[$id_product][0][0]['id_specific_price_rule']) && $this->specific_price[$id_product][0][0]['id_specific_price_rule'] > 0) {
                $id_product_attribute = 0;
            }
            */
            $this->use_old_price = true;

            if (!isset($this->specific_price[$id_product][$id_product_attribute]) || !$this->specific_price[$id_product][$id_product_attribute]) {

                // Pokud jsme nenasli slevu pro kombinaci, divame se, jestli jeste neni globalne zadana nejaka sleva.
                if (isset($this->specific_price[$id_product][0])) {
                    $id_product_attribute = 0;
                } else {
                    return 0;
                }
            }

            $discount_price = 0;
            foreach ($this->specific_price[$id_product][$id_product_attribute] as $sp) {

                if ($sp['price'] > 0) {
                    $price_with_vat = parent::AddVat($sp['price'], $this->row['vat']);
                    $price_without_vat = $sp['price'];
                }

                if ($sp['reduction_type'] == 'amount' && $sp['reduction'] > 0) {

                    if ($sp['reduction_tax'] == 1) {
                        $discount_price = $price_with_vat - $sp['reduction'];
                    } else {
                        $discount_price = $price_without_vat - $sp['reduction'];
                        if ($this->row['vat'] > 0) {
                            $discount_price = parent::AddVat($discount_price, $this->row['vat']);
                        }
                    }

                } elseif ($sp['reduction_type'] == 'percentage' && $sp['reduction'] > 0) {


                    $discount_vypocet = parent::AddVat(parent::RemovePercent($price_without_vat, $sp['reduction'] * 100), $this->row['vat']);

                    if (!empty($discount_price)) {
                        if ($discount_vypocet < $discount_price) {
                            $discount_price = $discount_vypocet;
                        }
                    } else {
                        $discount_price = $discount_vypocet;
                    }

                } else { /* pevná cena */
                    if ($this->id_currency_default == $sp['id_currency']
                        ||
                        $this->force_id_currency == $sp['id_currency']
                    ) {
                        $this->use_old_price = false;
                        $discount_price = $price_with_vat;
                    }
                }

            }


            return $discount_price;
        }

        private
        function GetFullPrice($type = '', $price_with_vat_force = false, $id_product_attribute = 0)
        {
            $discount_price_use = 0;

            if (!isset($this->specific_price[$this->row['id_product']][$id_product_attribute]) && $this->customer_group_reduction > 0) {
                $this->specific_price[$this->row['id_product']][$id_product_attribute][] = array(
                    'reduction_type' => 'percentage',
                    'reduction' => $this->customer_group_reduction / 100,
                    'price' => '-1.000000',
                );
            }

            $price_with_vat = parent::AddVat($this->row['price'], $this->row['vat']);
            $this->price_with_vat_original = $price_with_vat;
            $price_without_vat = $this->row['price'];
            if ($price_with_vat_force) {
                $price_with_vat = $price_with_vat_force;
                $price_without_vat = parent::RemoveVat($price_with_vat_force, $this->row['vat']);
            }


            $this->x->old_price = $price_with_vat;
            $rt = ROUND_TYPE;
            if (DECIMALS_DEFAULT == 0) {
                $this->x->old_price = $rt($this->x->old_price);
            } else {
                $this->x->old_price = (float)number_format($this->x->old_price, DECIMALS_DEFAULT, '.', '');
            }
            if ($this->use_old_price == false) {
                $this->x->old_price = 0;
            }


            // koberec hint
            if (isset($this->specific_price[$this->row['id_product']])) {
                $discount_price_use = $this->CountSpecificPrice($this->row['id_product'], $price_with_vat, $price_without_vat, $id_product_attribute);
            } elseif (isset($this->specific_price[0][0][0]['id_specific_price_rule']) && $this->specific_price[0][0][0]['id_specific_price_rule'] > 0) {
                $discount_price_use = $this->CountSpecificPrice(0, $price_with_vat, $price_without_vat);
            }

            // Pravidla pro katalog - begin //
            /*
            if ($discount_price_use > 0) {
                $pravidla_pro_katalog = $this->QueryFA("SELECT a.reduction, a.reduction_type FROM specific_price_rule as a
            INNER JOIN specific_price_rule_condition_group as b ON (a.id_specific_price_rule = b.id_specific_price_rule_condition_group)
            INNER JOIN specific_price_rule_condition as c ON (b.id_specific_price_rule_condition_group = c.id_specific_price_rule_condition_group && c.type = 'category')
            INNER JOIN category_product as d ON (d.id_category = c.value && d.id_product = {$this->row['id_product']})
            ", false, 'assoc');
                if ($pravidla_pro_katalog) {
                    foreach ($pravidla_pro_katalog as $pravidla) {

                        $reduction = (float)$pravidla['reduction'];
                        $reduction_type = $pravidla['reduction_type'];
                        if ($reduction_type == 'amount' && $reduction > 0) {
                            //   $discount_price_use -= $reduction;
                        }
                    }
                }
            }
            */

            // Pravidla pro katalog - end //
            $rt = ROUND_TYPE;
            if (DECIMALS_DEFAULT == 0) {
                $discount_price = $rt($discount_price_use);
            } else {
                $discount_price = (float)number_format($discount_price_use, DECIMALS_DEFAULT, '.', '');
            }

            $used_price = (empty($discount_price) || $discount_price == '-1') ? $price_with_vat : $discount_price;


            // $clean_price = ($this->row['vat'] != NULL) ? parent::AddVat($used_price, $this->row['vat']) : $used_price;

            // $clean_price2 = ($this->row['vat'] != NULL) ? parent::AddVat($this->row['price'], $this->row['vat']) : $this->row['price'];

            $price = (float)$used_price;

//$this->add = ($price > 0) ? true : false;

            if ($type == 'sleva') {
                $price = (float)$used_price - ($discount_price * 1.21);
            } elseif ($type == 'real') {
                $price = (float)$used_price;
            }

            if (empty($price)) {
                $this->null_price = true;
                return 0;
            }

            $price_no_round = $price;
            if (DECIMALS_DEFAULT == 0) {
                $price = $rt($price);
            } else {
                $price = (float)number_format($price, DECIMALS_DEFAULT, '.', '');
            }


            return array('price_round' => $price, 'price_no_round' => $price_no_round);
        }


        private
        function GetDelivery($quantity = 0, $available_date = '', $google = false)
        {

            $dodani_do = $this->days_nostock;
            /*
            $available_later = trim(str_replace('dní', '', $this->row['available_later']));
            $available_later = explode('-', $available_later);
            $available_later = (int)trim($available_later[0]);

            if ($available_later > 0) {
                $dodani_do = $available_later;
            }
            */
            if ($google === false) {

                if ($available_date != '' && $available_date != '0000-00-00' && $available_date > date("Y-m-d")) {
                    return (strtotime($available_date) - strtotime(date("Y-m-d"))) / 86400;
                }

                return ($quantity > 0 || STOCK_MANAGEMENT == 0) ? $this->days_stock : $dodani_do;
            } else {
                return ($quantity > 0 || STOCK_MANAGEMENT == 0 || $this->out_of_stock[$this->row['id_product']] == 1) ? 0 : 1;
            }


        }

        private
        function toArray($obj)
        {
            if (is_object($obj)) {
                $obj = (array)$obj;
            }
            if (is_array($obj)) {
                $new = array();
                foreach ($obj as $key => $val) {
                    $new[$key] = $this->toArray($val);
                }
            } else {
                $new = $obj;
            }

            return $new;
        }

        private
        function GetCategoryPS()
        {
            // PAIR_TYPE
            $category = array();
            $this->lr = '';
            $this->lr_sk = '';


            if (empty($this->row['id_category_default'])) {
                echo PHP_EOL . 'Notice: No default category (' . $this->row['id_category_default'] . ') for product ID: ' . $this->row['id_product'] . PHP_EOL;
                return false;
            }

            if (isset($this->cache_pair['heureka_cz'][$this->row['id_product']])) {
                $this->lr = $this->cache_pair['heureka_cz'][$this->row['id_product']]['link_rewrite'];
                $category['heureka_cz'] = 'Heureka.cz | ' . $this->cache_pair['heureka_cz'][$this->row['id_product']]['heureka_full_name'];
            }

            if (isset($this->cache_pair['heureka_sk'][$this->row['id_product']])) {
                $this->lr_sk = $this->cache_pair['heureka_sk'][$this->row['id_product']]['link_rewrite'];
                $category['heureka_sk'] = 'Heureka.sk | ' . $this->cache_pair['heureka_sk'][$this->row['id_product']]['heureka_full_name'];
            }

            if (isset($this->cache_pair['zbozi'][$this->row['id_product']])) {
                $this->lr = $this->cache_pair['zbozi'][$this->row['id_product']]['link_rewrite'];
                $category['zbozi_cz'] = html_entity_decode($this->cache_pair['zbozi'][$this->row['id_product']]['zbozi_full_name']);
            }

            if (isset($this->cache_pair['google'][$this->row['id_product']])) {
                $this->lr = $this->cache_pair['google'][$this->row['id_product']]['link_rewrite'];
                $category['facebook_com'] = $category['google_com'] = html_entity_decode($this->cache_pair['google'][$this->row['id_product']]['google_full_name']);
            }

            if (!isset($this->cache_categories[$this->row['id_category_default']])) {
                // echo 'Notice: Default category ID: ' . $this->row['id_category_default'] . ' for product ID: ' . $this->row['id_product'] . ' not exists' . PHP_EOL;
                return false;
            }


            $default_category = $this->cache_categories[$this->row['id_category_default']];


            $full_name_cats = $default_category['name'];
            if (empty($this->lr)) {
                $this->lr = $default_category['link_rewrite'];
            }
            if (empty($this->lr_sk) && isset($this->cache_categories_sk[$this->row['id_category_default']]['link_rewrite'])) {
                $this->lr_sk = $this->cache_categories_sk[$this->row['id_category_default']]['link_rewrite'];
            } else {
                $this->lr_sk = $this->lr;
            }

            $done[$default_category['id_category']] = array('id' => $default_category['id_category'], 'name' => $default_category['name']);

            if ($default_category['id_parent'] > 2) {
                while ($default_category['id_parent'] > 2) {

                    // $tmp = $this->QueryFA("SELECT id_parent FROM category WHERE id_category = {$rows[0]['id_parent']};", false, 'assoc');
                    $done[$default_category['id_parent']] = array('id' => $default_category['id_parent']);
                    // $rows[0]['id_parent'] = $tmp[0]['id_parent'];

                    if (!isset($this->cache_categories[$default_category['id_parent']]['id_parent'])) {
                        break 1;
                    }

                    $default_category['id_parent'] = $this->cache_categories[$default_category['id_parent']]['id_parent'];

                }
            }


// Sestavujeme textové vyjádření kategorií

            $fullway = array();
            foreach ($done as $k => $w) {


                if (!isset($w['name'])) {
                    $fullway[] = $this->cache_categories[$w['id']]['name'];
                } else {
                    $fullway[] = $w['name'];
                }
//                }
                $full_name_cats = implode(' | ', array_reverse($fullway));
            }


            if (!isset($full_name_cats)) {
                echo 'Notice: No category for product ID: ' . $this->row['id_product'] . PHP_EOL;
                return false;
            }
            if (!isset($category['heureka_cz'])) {
                $category['heureka_cz'] = $full_name_cats;
            }
            if (!isset($category['heureka_sk'])) {
                $category['heureka_sk'] = $full_name_cats;
            }
            if (!isset($category['google_com'])) {
                $category['google_com'] = $full_name_cats;
            }

            if (!isset($category['facebook_com'])) {
                $category['facebook_com'] = $full_name_cats;
            }

            if (!isset($category['zbozi_cz'])) {
                $category['zbozi_cz'] = $full_name_cats;
            }
            $category['full'] = $full_name_cats;
            $category['glami_cz'] = $category['glami_sk'] = '';
            $category['pricemania_cz'] = $category['pricemania_sk'] = '';
            # glami - begin #
            if (isset($_GET['glami'])) {


                if (!isset($this->glami_category_whitelist[$this->row['id_category_default']])) {
                    // Nulujeme
                    $category = array('heureka_cz' => '', 'heureka_sk' => '', 'google_com' => '', 'facebook_com' => '', 'zbozi_cz' => '', 'full' => '',);
                }

                $full_name_cats_glami = array();
                $full_name_cats_glami_sk = array();
                $done2 = array();
                if (isset($this->cache_categories_glami_only[$this->row['id_product']])) {
                    foreach ($this->cache_categories_glami_only[$this->row['id_product']] as $rows) {
                        $fullway2 = false;
                        $fullway2_sk = false;
                        if (isset($this->glami_category_pair[$rows['id_category']]) && !empty($this->glami_category_pair[$rows['id_category']])) {
                            if (isset($this->glami_category_pair[$rows['id_category']]['cz'])) {
                                $fullway2 = array('Glami.cz | ' . $this->glami_category_pair[$rows['id_category']]['cz']);
                            } else {
                                // continue;
                                // $fullway2 = array($rows['name']);
                            }
                            if (isset($this->glami_category_pair[$rows['id_category']]['sk'])) {
                                $fullway2_sk = array('Glami.sk | ' . $this->glami_category_pair[$rows['id_category']]['sk']);
                            } else {
                                // continue;
                                // $fullway2_sk = array($rows['name']);
                            }
                            // Nulujeme
                            $category = array('heureka_cz' => '', 'heureka_sk' => '', 'google_com' => '', 'facebook_com' => '', 'zbozi_cz' => '', 'full' => '',);
                        } else {
                            continue;
                            /*
                            $fullway2 = array($rows['name']);

                            if ($rows['id_parent'] > 2) {

                                while ($rows['id_parent'] > 2) {

                                    // $tmp = $this->QueryFA("SELECT id_parent FROM category WHERE id_category = {$rows['id_parent']};", false, 'assoc');
                                    $done2[$rows['id_parent']] = array('id' => $rows['id_parent']);
                                    // $rows['id_parent'] = $tmp[0]['id_parent'];
                                    $rows['id_parent'] = $this->cache_categories[$rows['id_parent']]['id_parent'];
                                }
                                // print_R($done2);


                                foreach ($done2 as $k => $w) {


                                    if (!isset($w['name'])) {
                                        $fullway2[] = $this->cache_categories[$w['id']]['name'];
                                    } else {
                                        $fullway2[] = $w['name'];
                                    }
//                }

                                }
                            }
                            $fullway2_sk = $fullway2;
                            */
                        }
                        if ($fullway2) {
                            $tmp = implode(' | ', array_reverse($fullway2));
                            $full_name_cats_glami[$tmp] = $tmp;
                        }
                        if ($fullway2_sk) {
                            $tmp_sk = implode(' | ', array_reverse($fullway2_sk));
                            $full_name_cats_glami_sk[$tmp_sk] = $tmp_sk;
                        }

                    }
                    // aby tam nebyla duplicita

                    // aby tam nebyla duplicita
                    // if (isset($full_name_cats_glami[$full_name_cats]) && count($full_name_cats_glami) > 1) {
                    if (isset($full_name_cats_glami[$full_name_cats]) && !isset($_GET['glami'])) {
                        unset($full_name_cats_glami[$full_name_cats]);
                        unset($full_name_cats_glami_sk[$full_name_cats]);
                    }
                    $category['glami_cz'] = $full_name_cats_glami;
                    $category['glami_sk'] = $full_name_cats_glami_sk;
                }
            }
            # glami - end #


            # pricemania - begin #
            if (isset($_GET['pricemania'])) {


                if (!isset($this->pricemania_category_whitelist[$this->row['id_category_default']])) {
                    // Nulujeme
                    $category = array('heureka_cz' => '', 'heureka_sk' => '', 'google_com' => '', 'facebook_com' => '', 'zbozi_cz' => '', 'full' => '',);
                }

                $full_name_cats_pricemania = array();
                $full_name_cats_pricemania_sk = array();
                $done2 = array();
                if (isset($this->cache_categories_pricemania_only[$this->row['id_product']])) {

                    foreach ($this->cache_categories_pricemania_only[$this->row['id_product']] as $rows) {

                        if (isset($this->pricemania_category_pair[$rows['id_category']]) && !empty($this->pricemania_category_pair[$rows['id_category']])) {
                            $fullway2 = array($this->pricemania_category_pair[$rows['id_category']]['cz']);
                            $fullway2_sk = array($this->pricemania_category_pair[$rows['id_category']]['sk']);
                            // Nulujeme
                            $category = array('heureka_cz' => '', 'heureka_sk' => '', 'google_com' => '', 'facebook_com' => '', 'zbozi_cz' => '', 'full' => '',);
                        } else {
                            $fullway2 = array($rows['name']);

                            if ($rows['id_parent'] > 2) {

                                while ($rows['id_parent'] > 2) {

                                    // $tmp = $this->QueryFA("SELECT id_parent FROM category WHERE id_category = {$rows['id_parent']};", false, 'assoc');
                                    $done2[$rows['id_parent']] = array('id' => $rows['id_parent']);
                                    // $rows['id_parent'] = $tmp[0]['id_parent'];
                                    $rows['id_parent'] = $this->cache_categories[$rows['id_parent']]['id_parent'];
                                }
                                // print_R($done2);


                                foreach ($done2 as $k => $w) {


                                    if (!isset($w['name'])) {
                                        $fullway2[] = $this->cache_categories[$w['id']]['name'];
                                    } else {
                                        $fullway2[] = $w['name'];
                                    }
//                }

                                }
                            }
                            $fullway2_sk = $fullway2;
                        }
                        $tmp = implode(' | ', array_reverse($fullway2));
                        $tmp_sk = implode(' | ', array_reverse($fullway2_sk));
                        $full_name_cats_pricemania[$tmp] = $tmp;
                        $full_name_cats_pricemania_sk[$tmp_sk] = $tmp_sk;


                    }
                    // aby tam nebyla duplicita

                    // aby tam nebyla duplicita
                    // if (isset($full_name_cats_pricemania[$full_name_cats]) && count($full_name_cats_pricemania) > 1) {
                    if (isset($full_name_cats_pricemania[$full_name_cats]) && !isset($_GET['pricemania'])) {
                        unset($full_name_cats_pricemania[$full_name_cats]);
                        unset($full_name_cats_pricemania_sk[$full_name_cats]);
                    }
                    $category['pricemania_cz'] = $full_name_cats_pricemania;
                    $category['pricemania_sk'] = $full_name_cats_pricemania_sk;

                }
            }
            # pricemania - end #


            return $category;
            // return ($type == 'default') ? $full_name_cats[$idcs[0]] : $full_name_cats;
        }

        private
        function GetCategory()
        {
            $cats = array();


            if (empty($this->row['id_category_default'])) {
                $cats['zbozi_cz'] = $cats['heureka_cz'] = $cats['google_com'] = '';
                return $cats;
            }
            $sql1 = "
SELECT
a.id_parent, b.name, b.link_rewrite
FROM category as a
LEFT JOIN category_lang as b ON (b.id_category = a.id_category)
WHERE
a.id_category = {$this->row['id_category_default']} && b.id_lang = {$this->id_lang}
LIMIT 0, 1
";
            $cat0 = $this->QueryFA($sql1);

            if (!isset($cat0[0])) {
                $cats['zbozi_cz'] = $cats['heureka_cz'] = $cats['google_com'] = '';
                return $cats;
            }
            $cat = (array)$cat0[0];

            $this->lr = $cat['link_rewrite'];


            /*
              if (defined('FORCE_CATEGORY') && FORCE_CATEGORY) {
              return $this->AddCDATA(FORCE_CATEGORY);
              }

             */


            $cats[] = $cat['name'];
            $stopper = 0;
            while ($cat['id_parent'] > 2) {
                $stopper++;
                $sql2 = "SELECT
                            a.id_parent, a.id_category, b.name
                            FROM category as a
                            LEFT JOIN category_lang as b ON (b.id_category = a.id_category)
                            WHERE a.id_category = {$cat['id_parent']} LIMIT 0, 1;";
                $cat0 = $this->FetchAll($this->Query($sql2));

                if (!isset($cat0[0])) {
                    break;
                }
                $cat = (array)$cat0[0];

                if ($stopper >= 100 || $cat['id_category'] == $cat['id_parent']) {
                    break;
                }
                $cats[] = $cat['name'];
            }

            if (defined('FORCE_ADD_BEFORE_CATEGORY') && FORCE_ADD_BEFORE_CATEGORY) {
                $cats[] = FORCE_ADD_BEFORE_CATEGORY;
            }
            if (defined('FORCE_ADD_AFTER_CATEGORY') && FORCE_ADD_AFTER_CATEGORY) {
                $cats[] = FORCE_ADD_AFTER_CATEGORY;
            }
            $cats['zbozi_cz'] = $cats['heureka_cz'] = $cats['google_com'] = $cats;
            if (defined('FORCE_CATEGORY') && 'FORCE_CATEGORY' == true) {
                if (defined('FORCE_CATEGORY_HEUREKA') && FORCE_CATEGORY_HEUREKA) {
                    $cats['heureka_cz'] = FORCE_CATEGORY_HEUREKA;
                } else {
                    $cats['heureka_cz'] = implode(" | ", $cats['heureka_cz']);
                }
                if (defined('FORCE_CATEGORY_ZBOZI') && FORCE_CATEGORY_ZBOZI) {
                    $cats['zbozi_cz'] = FORCE_CATEGORY_ZBOZI;
                } else {
                    $cats['zbozi_cz'] = implode(" | ", $cats['zbozi_cz']);
                }

#return array($this->AddCDATA($cats['heureka_cz']), $this->AddCDATA($cats['zbozi_cz']));
                return $cats;
            } else {
                return array($this->AddCDATA(implode(" | ", $cats['heureka_cz'])), $this->AddCDATA(implode(" | ", $cats['zbozi_cz'])));
            }
        }

        private
        function GetURL($lang = 'cz')
        {

            if (!REAL_SK) {
                $lang = 'cz';
            }
            if (SEO_URL) {

                $add_lang_to_url = ($lang == 'sk') ? 'sk/' : ADD_LANG_TO_URL;
                $url = HTTPS . WEB . $this->physical_uri . $this->virtual_uri . $add_lang_to_url . ROUTE_RULE; # Jen část Zatím

                $id = (!empty($this->row['id_product'])) ? $this->row['id_product'] : '';
                $rewrite = (!empty($this->row['link_rewrite'])) ? $this->row['link_rewrite'] : '';
                if ($lang == 'sk') {
                    $rewrite = $this->cache_sk_names[$this->row['id_product']]['link_rewrite'];
                }
                $ean = (!empty($this->row['ean'])) ? '-' . $this->row['ean'] : '';

                // Frantisek.Mateju@firma.seznam.cz Mon, Jul 18, 2016 at 9:52 AM
                $rewrite = urlencode($rewrite);


                $pomlcka = '';
                $lomitko = '';
                if ($ean) {
                    $pomlcka = '-';
                }
                $lr = ($lang == 'sk') ? $this->lr_sk : $this->lr;


                if (!empty($lr)) {
                    $lomitko = '/';
                }


                if (EXISTS_REMOVE_ID) {

                    $url = str_replace('{id}-{rewrite}', '{rewrite}', str_replace('{-:ean13}.html', '', $url));
                    // $url = str_replace('{id}-{rewrite}', '{rewrite}', str_replace('{-:ean13}', '', $url));
                }
                $reference = mb_strtolower($this->row['reference']);
                $url = strtr($url, array(
                    '{category}' => $reference,
                    '{/:reference}' => $lomitko . $reference,
                    '{reference:/}' => $reference . $lomitko,
                    '{reference}' => $lr,
                    '{/:category}' => $lomitko . $lr,
                    '{category:/}' => $lr . $lomitko,
                    '{categories}' => $lr,
                    '{/:categories}' => $lomitko . $lr,
                    '{categories:/}' => $lr . $lomitko,
                    '{id}' => $id,
                    '{rewrite}' => $rewrite,
                    '{ean13}' => $ean,
                    '{-:ean13}' => $pomlcka . $ean,
                    // '{-:id_product_attribute}' => $pomlcka2 . $this->variants_add[$this->variant_count_tmp]['id_product_attribute'],
                    // '{id_product_attribute}' => $this->variants_add[$this->variant_count_tmp]['id_product_attribute'],
                ));
                if (version_compare(_PS_VERSION_, '1.7.2', '>=') && preg_match("/id_product_attribute/", $url)) {
                    $id_varianty = '';
                    $pomlcka2 = '';
                    if (!empty($this->variants_add[$this->variant_count_tmp]['id_product_attribute'])) {
                        $id_varianty = $this->variants_add[$this->variant_count_tmp]['id_product_attribute'];
                        $pomlcka2 = '-';
                    }
                    $url = strtr($url, array(
                        '{-:id_product_attribute}' => $pomlcka2 . $id_varianty,
                        '{id_product_attribute}' => $id_varianty,
                    ));
                }


                // fix na dvojitou pomlčku
                $url = str_replace('--', '-', $url);

                return $url;


            } else {
                return HTTPS . WEB . $this->physical_uri . $this->virtual_uri . "index.php?id_product=" . $this->row['id_product'] . "&amp;controller=product"; # Jen část Zatím

            }

#return 'http://' . WEB . $this->physical_uri . $this->lr . '/' . $this->row['id_product'] . '-' . $this->row['link_rewrite'] . '.html'; # Jen část Zatím

        }

        private
        function GetManufacturer()
        {


            // Glami info - "Feed je v poriadku, už stačí len doplniť pole MANUFACTURER. Ak je výrobca neznámy, uveďte názov obchodu."
            if (isset($_GET['glami']) && empty($this->row['manufacturer'])) {
                return $_SERVER['HTTP_HOST'];
            }
            return (!empty($this->row['manufacturer']) ? $this->row['manufacturer'] : '');
        }

        private
        function AddCDATA($data)
        {
            return (isset($data) && $data != '') ? "<![CDATA[" . $data . "]]>" : '';
        }

        /*
                protected
                function GetEAN($barcode)
                {
                    // DONE
                    $barcode = trim((string)$barcode);
        // check to see if barcode is 13 digits long
                    if (!preg_match("/^[0-9]{13}$/", $barcode)) {
                        return 0;
                    }

        //$ean_tmp = $this->Sanitize('', $ean, 's');
        //if (preg_match("/[0-9a-z]\+.[0-9a-z]/i", $ean_tmp)) {// Vědecké konvertování
        //    return (int) number_format(strtr($ean_tmp, array(',' => '.', '+' => '')), 0, '.', '');
        //}
        //$ean = preg_replace("/[^0-9]/", "", $ean_tmp);
        //return (!empty($ean) && preg_match("/^[0-9]+$/", (int) $ean)) ? $ean : 0;

                    $digits = $barcode;

        // 1. Add the values of the digits in the
        // even-numbered positions: 2, 4, 6, etc.
                    $even_sum = $digits[1] + $digits[3] + $digits[5] +
                        $digits[7] + $digits[9] + $digits[11];

        // 2. Multiply this result by 3.
                    $even_sum_three = $even_sum * 3;

        // 3. Add the values of the digits in the
        // odd-numbered positions: 1, 3, 5, etc.
                    $odd_sum = $digits[0] + $digits[2] + $digits[4] +
                        $digits[6] + $digits[8] + $digits[10];

        // 4. Sum the results of steps 2 and 3.
                    $total_sum = $even_sum_three + $odd_sum;

        // 5. The check character is the smallest number which,
        // when added to the result in step 4, produces a multiple of 10.
                    $next_ten = (ceil($total_sum / 10)) * 10;
                    $check_digit = $next_ten - $total_sum;

        // if the check digit and the last digit of the
        // barcode are OK return true;
                    return ($check_digit == $digits[12]) ? (int)$barcode : 0;
                }
        */


        private
        function AddImageAlternative($only_one = false, $count = false)
        {

            $img_alt = array(
                'heureka_cz' => '',
                'zbozi_cz' => '',
                'shopalike' => '',
                'google_com' => array(),
            );

            if (isset($this->cache_alt_images[$this->row['id_product']])) {
                $tmp_shopalike = 0;
                foreach ($this->cache_alt_images[$this->row['id_product']] as $row) {
                    $tmp_shopalike++;
                    // Nemusíme porovnávat protože první obrázek přeskakujeme (je to main)
                    //       if ($this->row['main_image'] == $row->id_image) {
                    //     continue;
                    //}
                    $path = $this->GetImagePath($row);
                    $img_alt['shopalike'] .= $this->AddTag('IMGURL_' . $tmp_shopalike, $path);
                    $img_alt['heureka_cz'] .= $this->AddTag('IMGURL_ALTERNATIVE', $path);
                    $img_alt['zbozi_cz'] .= $this->AddTag('IMGURL', $path);
                    $img_alt['google_com'][] = $this->AddTag('IMGURL_ALTERNATIVE', $path);
                }
            }
            if ($only_one == false) {
                return $img_alt;
            } else {
                if ($count > 0 && $img_alt['google_com']) {
                    $img_alt['google_com'] = array_slice($img_alt['google_com'], 0, $count);
                }
                return implode('', $img_alt['google_com']);
            }
        }

        private
        function GetImagePath($id_image)
        {

            if (LEGACY_IMAGES == 0) {
                $image = '';
                for ($i = 0; $i < strlen($id_image); $i++) {
                    $image .= $id_image[$i] . '/';
                }
                return HTTPS . WEB . $this->physical_uri . 'img/p/' . $image . $id_image . '.jpg';
            } else {
                return HTTPS . WEB . $this->physical_uri . 'img/p/' . $this->row['id_product'] . '-' . $id_image . '.jpg';
            }

        }

        private
        function AddVideo()
        {
            // Zakomentováno, tohle bylo asi custom a je to zbytečná query navíc (asi ten modul product video, nepouziva se prakticky nidke //
            return false;
            /*

                if (mysqli_num_rows(mysqli_query($this->link, "SHOW TABLES LIKE '" . _DB_PREFIX_ . "product_video'")) > 0) {
                    $video = $this->QueryR("SELECT content FROM product_video WHERE id_product = {$this->row['id_product']} LIMIT 0,1;");

                    return (!empty($video)) ? $this->AddTag('VIDEO_URL', "https://www.youtube.com/watch?v=$video") : '';
                }


            return false;
            */
        }

        private
        function CacheAvailability()
        {
            $this->stock = array();
            $this->out_of_stock = array();

            // Mby by tu nemělo to id_shop vůbec být.. don't know (něco s multistore fail)
            // $tmp = $this->QueryFA("SELECT quantity, id_product, id_product_attribute FROM stock_available WHERE id_shop = {$this->id_shop};", false, 'assoc');


            $query_id_product = '';
            if ($this->debug_id_product) {
                $query_id_product = " && sa.id_product = {$this->debug_id_product}";
            }

            $query = "SELECT sa.quantity, sa.id_product, sa.id_product_attribute, sa.out_of_stock FROM stock_available as sa WHERE sa.id_product IN (SELECT ps.id_product FROM product_shop as ps WHERE ps.id_shop = {$this->id_shop} && ps.active = 1 && ps.visibility != 'none')$query_id_product;";
            /*
            if (count($this->id_shops) == 1) { // NENI MT
                $query = "SELECT quantity, id_product, id_product_attribute FROM stock_available WHERE id_shop = {$this->id_shop};";
            } else { // JE MT
                $id_shop_group = false;
                foreach ($this->id_shops as $shop) {
                    if ($this->id_shop == $shop['id_shop']) {
                        $id_shop_group = $shop['id_shop_group'];
                    }
                }
                if ($id_shop_group === false) { // Nejaka chyba, asi by nemelo nastat, ale pro jistotu...
                    $query = "SELECT quantity, id_product, id_product_attribute FROM stock_available;";
                } else {
                    $query = "SELECT quantity, id_product, id_product_attribute FROM stock_available WHERE id_shop_group = $id_shop_group;";
                }
                $query = "SELECT sa.quantity, sa.id_product, sa.id_product_attribute FROM stock_available as sa WHERE sa.id_product IN (SELECT ps.id_product FROM product_shop as ps WHERE ps.id_shop = {$this->id_shop} && ps.active = 1);";
            }
            echo $query;
            */

            $tmp = $this->QueryFA($query, false, 'assoc');

            foreach ($tmp as $t) {
                if (!isset($this->stock[(int)$t['id_product']])) {
                    $this->stock[(int)$t['id_product']] = array();
                    // $this->out_of_stock[(int)$t['id_product']] = 0;
                }

                if ((int)$t['quantity'] > 0) {
                    $this->stock[(int)$t['id_product']][(int)$t['id_product_attribute']] = (int)$t['quantity'];
                    $this->out_of_stock[(int)$t['id_product']] = (int)$t['out_of_stock'];
                } elseif (STOCK_MANAGEMENT == 0) {
                    $this->stock[(int)$t['id_product']][(int)$t['id_product_attribute']] = 100;
                    $this->out_of_stock[(int)$t['id_product']] = OUT_OF_STOCK_ORDER;
                } else {
                    $this->stock[(int)$t['id_product']][(int)$t['id_product_attribute']] = 0;
                    $this->out_of_stock[(int)$t['id_product']] = (int)$t['out_of_stock'];
                }


            }


            /*
            if ($type == 'number') {
                return $count;
            } elseif ($type == 'text') {
                return ($count > 0) ? 'skladem' : '3-4 dny';
            }
            */

        }


        private
        function AddTag($tag, $data, $close_tag = '')
        {
            $data = (string)$data;

            if (empty($close_tag)) {
                return (isset($data) && $data != '') ? "<$tag>$data</$tag>" . NEW_LINE : '';
            } else {
                return (isset($data) && $data != '') ? "<$tag>$data</$close_tag>" . NEW_LINE : '';
            }
        }

        private
        function ReplaceTag($old_tag, $new_tag, $data)
        {
            return strtr($data, array("<$old_tag>" => "<$new_tag>", "</$old_tag>" => "</$new_tag>"));
        }


        private
        function AddFreeDelivery($price = 0)
        {
            if (
                (FREE_SHIPPING > 0 && $price >= FREE_SHIPPING)
                ||
                (FREE_WEIGHT > 0 && $this->row['weight'] >= FREE_WEIGHT)
                ||
                (isset($this->row['is_virtual']) && $this->row['is_virtual'] == 1)
            ) {
// Zbozi only
                return '';
                // return $this->AddTag('EXTRA_MESSAGE', 'free_delivery');
            } else {
                return '';
            }
        }

        private
        function SanitizeDescription($desc_use)
        {
            $from = array('/[\x00-\x08\x0B\x0C\x0E-\x1F\x80-\x9F]/u', '/\<\!\[CDATA/', '/]]>/');
            $to = array('', '', '');
            // Omezujeme na 5000 znaku
            return @mb_substr(htmlspecialchars(trim(preg_replace('/\s+$/m', '', preg_replace($from, $to, parent::html_entity_replace(html_entity_decode(strip_tags($desc_use), ENT_QUOTES | ENT_XML1, 'UTF-8')))))), 0, 5000);
        }


        public
        function LoadAll()
        {
            /* Global */


            $this->add = true;

            // Možná by šlo nějak i šachovat s OUT_OF_STOCK_ORDER;
            // Pokud nemá zapnutý sklad
            /*
            if (isset($this->row['out_of_stock_real']) && $this->row['out_of_stock_real'] != NULL) {
                $this->row['out_of_stock'] = $this->row['out_of_stock_real'];
            }
            */

            // OC fix
            if (!isset($this->row['available_date'])) {
                $this->row['available_date'] = '';
            }

            if (TAX_ENABLED == false) {
                $this->row['vat'] = null;
            }

            $this->variant_exists = $this->null_price = false;

            $this->x = new stdClass();


            $tmp = $this->GetFullPrice('', false);

            $this->price_round = $tmp['price_round'];
            $this->real_price_no_round = $tmp['price_no_round'];

            if
            (((!isset($this->out_of_stock[$this->row['id_product']]))
                    ||
                    (
                        $this->stock[$this->row['id_product']][0] <= 0 &&
                        ($this->out_of_stock[$this->row['id_product']] == 0 ||
                            ($this->out_of_stock[$this->row['id_product']][0] == 2 && OUT_OF_STOCK_ORDER == 0))
                    )
                ) ||
                ($this->ShortProductNameFix() == false
                )
            ) { // 0 = zakázáno, 1 = povoleno, 2 = dle nastavení shopu
                $this->add = false;
                return;
            }
            if (SHAIM_ONLY_STOCK && STOCK_MANAGEMENT && (!isset($this->stock[$this->row['id_product']][0]) || empty($this->stock[$this->row['id_product']][0]))) {
                $this->add = false;
                return;
            }


            $desc_use = '';

            if (SHAIM_DESC == 1) {
                if (!empty($this->row['description'])) {
                    $desc_use = $this->row['description'];
                } elseif (!empty($this->row['description_short'])) {
                    $desc_use = $this->row['description_short'];
                }
            } else {
                if (!empty($this->row['description_short'])) {
                    $desc_use = $this->row['description_short'];
                } elseif (!empty($this->row['description'])) {
                    $desc_use = $this->row['description'];
                }
            }
            $desc_use = $this->SanitizeDescription($desc_use);

            if (empty($desc_use)) {
                $desc_use = 'Bez popisu.';
            } else {
                $desc_use = $this->AddCDATA($desc_use);
            }


            $this->x->desc = $this->AddTag('DESCRIPTION', $desc_use);


            $getmainimage = $this->GetMainImage();
            $this->x->main_image = '';
            if ($getmainimage) {
                $this->x->main_image = $this->AddTag('IMGURL', $getmainimage);
            }


            $rt = ROUND_TYPE;

            if ($this->force_currency == 'CZK') {
                $conversion_rate_cz_tmp = 1;
            } else {
                $conversion_rate_cz_tmp = CONVERSION_RATE_CZ;
            }
            if (DECIMALS_CZ == 0) {
                $this->x->full_price = $this->AddTag('PRICE_VAT', number_format($rt($this->real_price_no_round * $conversion_rate_cz_tmp), DECIMALS_CZ, ',', ''));
            } else {
                $this->x->full_price = $this->AddTag('PRICE_VAT', number_format($this->real_price_no_round * $conversion_rate_cz_tmp, DECIMALS_CZ, ',', ''));
            }

            if ($this->force_currency == 'EUR') {
                $conversion_rate_sk_tmp = 1;
            } else {
                $conversion_rate_sk_tmp = CONVERSION_RATE_SK;
            }
            if (DECIMALS_SK == 0) {
                $this->x->full_price_sk = $this->AddTag('PRICE_VAT', number_format($rt($this->real_price_no_round * $conversion_rate_sk_tmp), DECIMALS_SK, ',', ''));
            } else {
                $this->x->full_price_sk = $this->AddTag('PRICE_VAT', number_format($this->real_price_no_round * $conversion_rate_sk_tmp, DECIMALS_SK, ',', ''));
            }

            if ($this->force_currency == 'USD') {
                $conversion_rate_us_tmp = 1;
            } else {
                $conversion_rate_us_tmp = CONVERSION_RATE_US;
            }
            if (DECIMALS_US == 0) {
                $this->x->full_price_us = $this->AddTag('PRICE_VAT', number_format($rt($this->real_price_no_round * $conversion_rate_us_tmp), DECIMALS_US, ',', ''));
            } else {
                $this->x->full_price_us = $this->AddTag('PRICE_VAT', number_format($this->real_price_no_round * $conversion_rate_us_tmp, DECIMALS_US, ',', ''));
            }

            if ($this->force_currency == 'GBP') {
                $conversion_rate_gb_tmp = 1;
            } else {
                $conversion_rate_gb_tmp = CONVERSION_RATE_GB;
            }
            if (DECIMALS_GB == 0) {
                $this->x->full_price_gb = $this->AddTag('PRICE_VAT', number_format($rt($this->real_price_no_round * $conversion_rate_gb_tmp), DECIMALS_GB, ',', ''));
            } else {
                $this->x->full_price_gb = $this->AddTag('PRICE_VAT', number_format($this->real_price_no_round * $conversion_rate_gb_tmp, DECIMALS_GB, ',', ''));
            }


            $tmp_man = $this->GetManufacturer();
            $this->x->manufacturer = $this->AddTag('MANUFACTURER', $this->AddCDATA($tmp_man));

            $this->x->reference = '';
            if (!empty($this->row['reference'])) {
                $this->x->reference = $this->AddTag('PRODUCTNO', $this->AddCDATA($this->row['reference']));
            }


            $this->first_full = $first = array();


            $tmp = $this->GetCategoryPS();

            $this->first_full = $tmp;
            $first['full'] = $first['pricemania_cz'] = $first['pricemania_sk'] = $first['glami_cz'] = $first['glami_sk'] = $first['heureka_cz'] = $first['google_com'] = $first['facebook_com'] = $first['zbozi_cz'] = $first['heureka_sk'] = '';


            if ($tmp !== false) {
                /*
                if (isset($_GET['glami'])) {
                    $first['glami_cz'] = array_merge(array($tmp['heureka_cz']), $tmp['glami_cz']);

                    $first['glami_cz'] = array_map(function ($val) {
                        return $this->AddTag('CATEGORYTEXT', $this->AddCDATA($val));
                    }, $first['glami_cz']);
                    $first['glami_sk'] = array_merge(array($tmp['heureka_cz']), $tmp['glami_sk']);
                    $first['glami_sk'] = array_map(function ($val) {
                        return $this->AddTag('CATEGORYTEXT', $this->AddCDATA($val));
                    }, $first['glami_sk']);
                }
                if (isset($_GET['pricemania'])) {
                    $first['pricemania_cz'] = array_merge(array($tmp['heureka_cz']), $tmp['pricemania_cz']);

                    $first['pricemania_cz'] = array_map(function ($val) {
                        return $this->AddTag('CATEGORYTEXT', $this->AddCDATA($val));
                    }, $first['pricemania_cz']);
                    $first['pricemania_sk'] = array_merge(array($tmp['heureka_cz']), $tmp['pricemania_sk']);
                    $first['pricemania_sk'] = array_map(function ($val) {
                        return $this->AddTag('CATEGORYTEXT', $this->AddCDATA($val));
                    }, $first['pricemania_sk']);
                };
                */
                if (isset($_GET['glami'])) {
                    if (empty($tmp['glami_cz']) && empty($tmp['glami_sk'])) {
                        $this->add = false;
                        return;
                    }
                    $this->glami_cz_add = true;
                    $this->glami_sk_add = true;
                    if (empty($tmp['glami_cz'])) {
                        $this->glami_cz_add = false;
                    }
                    if (empty($tmp['glami_sk'])) {
                        $this->glami_sk_add = false;
                    }

                    $first['glami_cz'] = $this->AddTag('CATEGORYTEXT', $this->AddCDATA(reset($tmp['glami_cz'])));

                    $first['glami_sk'] = $this->AddTag('CATEGORYTEXT', $this->AddCDATA(reset($tmp['glami_sk'])));
                } elseif (isset($_GET['pricemania'])) {

                    $first['pricemania_cz'] = $this->AddTag('CATEGORYTEXT', $this->AddCDATA(reset($tmp['pricemania_cz'])));
                    $first['pricemania_sk'] = $this->AddTag('CATEGORYTEXT', $this->AddCDATA(reset($tmp['pricemania_sk'])));
                }

                $first['heureka_cz'] = $this->AddTag('CATEGORYTEXT', $this->AddCDATA($tmp['heureka_cz']));
                $first['heureka_sk'] = $this->AddTag('CATEGORYTEXT', $this->AddCDATA($tmp['heureka_sk']));
                $first['facebook_com'] = $first['google_com'] = $this->AddTag('CATEGORYTEXT', $this->AddCDATA($tmp['google_com']));
                $first['zbozi_cz'] = $this->AddTag('CATEGORYTEXT', $this->AddCDATA($tmp['zbozi_cz']));
                $first['full'] = $this->AddTag('CATEGORYTEXT', $this->AddCDATA($tmp['full']));
            }


            $this->x->categories['glami_cz'] = $first['glami_cz'];
            $this->x->categories['glami_sk'] = $first['glami_sk'];

            $this->x->categories['pricemania_cz'] = $first['pricemania_cz'];
            $this->x->categories['pricemania_sk'] = $first['pricemania_sk'];

            $this->x->categories['heureka_cz'] = $first['heureka_cz'];
            $this->x->categories['heureka_sk'] = $first['heureka_sk'];
            $this->x->categories['zbozi_cz'] = $first['zbozi_cz'];
            $this->x->categories['full'] = $first['full'];
            $this->x->categories['facebook_com'] = $this->x->categories['google_com'] = $first['google_com'];
            $this->x->categories['heureka_dostupnost'] = '';

            $this->x->ean = '';


            if (isset($this->row['ean']) && !empty($this->row['ean'])) {
                // $this->row['ean'] = $this->GetEAN($this->row['ean']);
                $tmp_ean = $this->row['ean'];

                // prestavame kontrolovat delku ean, radeji :)
                $strlen = strlen($tmp_ean);
                // EAN-8, UPC-12, EAN-13 a ITF-14

                if ($strlen == 8 || $strlen == 12 || $strlen == 13 || $strlen == 14) {

                    //  $gtin = $this->AddTag('g:gtin', $tmp_ean);
                    $this->x->ean = $this->AddTag('EAN', $tmp_ean);
                }


            }


            /* Heureka */
            $alt = $this->AddImageAlternative();
            $this->x->shopalike_alt_image = $alt['shopalike'];
            $this->x->heureka_alt_image = $alt['heureka_cz'];
            $this->x->zbozi_alt_image = $alt['zbozi_cz'];
            $this->x->heureka_videa = $this->AddVideo();
            $this->x->google_extra = '';
            $this->x->params = $this->AddParams();
            $this->x->params_sk = $this->AddSKParams();
            $this->AddVariants();
            $this->AddSKVariants();


            if ($this->null_price == true && $this->variant_exists == false) {
                $this->add = false;
                return;
            }


            $google_weight = 0;
            if (isset($this->row['weight']) && (int)$this->row['weight'] > 0) {
                $this->x->params .= $this->AddTag('PARAM', NEW_LINE . $this->AddTag('PARAM_NAME', 'Hmotnost') . $this->AddTag('VAL', (float)$this->row['weight'] . ' kg'));
                $this->x->params_sk .= $this->AddTag('PARAM', NEW_LINE . $this->AddTag('PARAM_NAME', 'Hmotnosť') . $this->AddTag('VAL', (float)$this->row['weight'] . ' kg'));
                $google_weight = $this->row['weight'] * 1000;
            }
            /* Zbozi */

            $this->x->zbozi_cz = $this->AddFreeDelivery($this->price_round);


            // Heureka i zbozi_cz to maji uplne obracene (PRODUCT vs PRODUCTNAME) - omg, why
            $this->better_pair_manufacturer = $this->better_pair_code = '';
            if (SHAIM_BETTER_PAIR_MANUFACTURER && !empty($tmp_man)) {
                $this->better_pair_manufacturer = $tmp_man . ' ';
            }

            if (SHAIM_BETTER_PAIR_CODE && !empty($this->row['reference'])) {
                $this->better_pair_code = ' ' . $this->row['reference'];
            }

            $name = $this->AddCDATA($this->better_pair_manufacturer . $this->row['name'] . $this->better_pair_code);
            $name_bonus_cz = $this->AddCDATA($this->better_pair_manufacturer . $this->row['name'] . $this->better_pair_code);
            $name_bonus_sk = $this->AddCDATA($this->better_pair_manufacturer . $this->row['name'] . $this->better_pair_code);
            $this->x->name_heureka = $this->AddTag('PRODUCT', $name);
            $this->x->name2_heureka = $this->AddTag('PRODUCTNAME', $name_bonus_cz);
            $this->x->name_zbozi = $this->AddTag('PRODUCT', $name_bonus_cz);
            $this->x->name2_zbozi = $this->AddTag('PRODUCTNAME', $name);
            $this->x->name_glami = $this->AddTag('PRODUCTNAME', $name);


            // Heureka + google_com
            // zbozi_cz nepovoluje zbozi_cz, ktere neni nove, my to neresime.

            if (isset($this->row['condition']) && !empty($this->row['condition'])) {
                $this->condition['heureka_cz'] = ($this->row['condition'] == 'new') ? 'new' : 'bazar';
                $this->condition['google_com'] = $this->condition['zbozi_cz'] = $this->row['condition'];
            } else {
                $this->condition['heureka_cz'] = $this->condition['google_com'] = $this->condition['zbozi_cz'] = 'new';
            }


            if (!REAL_SK) {
                $this->row['name_sk'] = $this->row['name'] . $this->better_pair_code;
                $this->x->name_heureka_sk = $this->AddTag('PRODUCT', $name);
                $this->x->name2_heureka_sk = $this->AddTag('PRODUCTNAME', $name_bonus_sk);
                $this->x->desc_sk = $this->AddTag('DESCRIPTION', $desc_use);
                $this->x->name_glami_sk = $this->x->name_glami;
            } else {
                $this->row['name_sk'] = $this->cache_sk_names[$this->row['id_product']]['name'];
                $this->x->name_heureka_sk = $this->AddTag('PRODUCT', $this->AddCDATA($this->better_pair_manufacturer . $this->row['name_sk'] . $this->better_pair_code));
                $this->x->name2_heureka_sk = $this->AddTag('PRODUCTNAME', $this->AddCDATA($this->better_pair_manufacturer . $this->row['name_sk'] . $this->better_pair_code));
                $this->x->desc_sk = $this->AddTag('DESCRIPTION', $this->cache_sk_names[$this->row['id_product']]['desc_use']);
                $this->x->name_glami_sk = $this->AddTag('PRODUCTNAME', $this->AddCDATA($this->row['name_sk']));
            }


            /* Google */
            if (in_array('google_com', $this->feeds) || in_array('facebook_com', $this->feeds)) {
                $dd = ($this->GetDelivery($this->stock[$this->row['id_product']][0], $this->row['available_date'], true) == 0) ? 'in stock' : 'out of stock';

                // $identifier_exists = (empty($this->x->ean) && empty($this->row['manufacturer'])) ? 'FALSE' : 'TRUE';
                // identifier_exists není nijak závislý na parametru BRAND (ten je povinný vždy). Potřebovali bychom ho mít vyplněný v případě, že není vyplnění GTIN (EAN) či MPN parametr.
                // https://support.google.com/merchants/answer/6324478?hl=en
                // Required only for new products that don’t have gtin and brand or mpn and brand.
                $identifier_exists = 'no';
                if (!empty($this->x->manufacturer)) {
                    if (!empty($this->x->ean) || !empty($this->row['reference'])) {
                        // if (!empty($this->x->ean)) {
                        $identifier_exists = 'yes';
                    }
                }
                $this->identifier_exists = $this->AddTag('g:identifier_exists', $identifier_exists);
                $google_product_category = '';
                if ($this->x->categories['google_com'] != $this->x->categories['full']) {
                    $google_product_category = $this->ReplaceTag('CATEGORYTEXT', 'g:google_product_category', str_replace('|', '>', $this->x->categories['google_com']));
                }
// predtim bylo g:title
                $this->x->google_name = $this->ReplaceTag('PRODUCT', 'title', $this->x->name_heureka);
                $this->x->google_price = $this->ReplaceTag('PRICE_VAT', 'g:price', str_replace('</PRICE_VAT>', ' ' . MENA . '</PRICE_VAT>', (($this->currency_default == 'EUR') ? $this->x->full_price_sk : (($this->currency_default == 'USD') ? $this->x->full_price_us : (($this->currency_default == 'GBP') ? $this->x->full_price_gb : $this->x->full_price)))));
                $this->x->google_price_sk = $this->ReplaceTag('PRICE_VAT', 'g:price', str_replace('</PRICE_VAT>', ' EUR</PRICE_VAT>', $this->x->full_price_sk));
                $this->x->google_price_us = $this->ReplaceTag('PRICE_VAT', 'g:price', str_replace('</PRICE_VAT>', ' USD</PRICE_VAT>', $this->x->full_price_us));
                $this->x->google_price_gb = $this->ReplaceTag('PRICE_VAT', 'g:price', str_replace('</PRICE_VAT>', ' GBP</PRICE_VAT>', $this->x->full_price_gb));
                $this->x->google_weight = $this->AddTag('g:shipping_weight', $google_weight . ' g');
                $this->x->google_ean = $this->ReplaceTag('EAN', 'g:gtin', $this->x->ean);
                $this->x->google_id = $this->AddTag('g:id', (int)$this->row['id_product']);

                $this->x->google_mpn = $this->AddTag('g:mpn', $this->AddCDATA($this->row['reference']));
                $this->x->itemgroup_id = '';
                $this->x->google_com_images = (($this->x->main_image) ? $this->ReplaceTag('IMGURL', 'g:image_link', $this->x->main_image) : '')
                    . $this->ReplaceTag('IMGURL_ALTERNATIVE', 'g:additional_image_link', $this->AddImageAlternative(true, 10));
                // Predtim tam bylo g:description
                $this->x->google_com = $this->ReplaceTag('DESCRIPTION', 'description', $this->x->desc)


                    . $this->ReplaceTag('MANUFACTURER', 'g:brand', $this->x->manufacturer)

                    . $this->ReplaceTag('CATEGORYTEXT', 'g:product_type', str_replace(' | ', ' > ', $this->x->categories['full']))
                    // . $gtin
//. $this->AddTag('mobile_link', '') // devel mobilní link
                    . $google_product_category
//. $this->AddTag('g:availability_date', '') // devel (předobjednávky
                    . $this->AddTag('g:availability', $dd)
                    . $this->AddTag('g:condition', $this->condition['google_com'])
                    . $this->AddTag('g:adult', (preg_match("/eroti/i", $this->x->name_heureka) || preg_match("/vagi/i", $this->x->name_heureka) || preg_match("/sex/i", $this->x->name_heureka) || preg_match("/penis/i", $this->x->name_heureka) || preg_match("/prsa/i", $this->x->name_heureka) ? 'TRUE' : 'FALSE'));
            }

            if (in_array('pricemania', $this->feeds)) {
                $this->x->pricemania_cz =
                    $this->ReplaceTag('DESCRIPTION', 'description', $this->x->desc)
                    . $this->ReplaceTag('MANUFACTURER', 'manufacturer', $this->x->manufacturer)
//                . $this->AddTag('url', $this->GetUrl())
                    . (($this->x->main_image) ? $this->ReplaceTag('IMGURL', 'picture', $this->x->main_image) : '')
                    // . $this->AddTag('part_number', '')
                    . $this->AddTag('shipping', '0')
                    . $this->AddTag('availability', $this->GetDelivery($this->stock[$this->row['id_product']][0], $this->row['available_date'], false))
                    . (($this->x->params) ? '<params>' . $this->ReplaceTag('PARAM', 'param', $this->ReplaceTag('PARAM_NAME', 'param_name', $this->ReplaceTag('VAL', 'param_value', $this->x->params))) . ' </params>' : '');

                $this->x->pricemania_sk =
                    $this->ReplaceTag('DESCRIPTION', 'description', $this->x->desc)
                    . $this->ReplaceTag('MANUFACTURER', 'manufacturer', $this->x->manufacturer)
//                . $this->AddTag('url', $this->GetUrl())
                    . (($this->x->main_image) ? $this->ReplaceTag('IMGURL', 'picture', $this->x->main_image) : '')
                    // . $this->AddTag('part_number', '')
                    . $this->AddTag('shipping', '0')
                    . $this->AddTag('availability', $this->GetDelivery($this->stock[$this->row['id_product']][0], $this->row['available_date'], false))
                    . (($this->x->params_sk) ? '<params>' . $this->ReplaceTag('PARAM', 'param', $this->ReplaceTag('PARAM_NAME', 'param_name', $this->ReplaceTag('VAL', 'param_value', $this->x->params_sk))) . ' </params>' : '');

                $this->x->pricemania_cz .= $this->ReplaceTag('CATEGORYTEXT', 'category', str_replace(' | ', ' > ', $this->x->categories['pricemania_cz']));
                // $this->x->pricemania_cz .= $this->ReplaceTag('PRICE_VAT', 'price', $this->x->full_price);
                $this->x->pricemania_sk .= $this->ReplaceTag('CATEGORYTEXT', 'category', str_replace(' | ', ' > ', $this->x->categories['pricemania_sk']));
                // $this->x->pricemania_sk .= $this->ReplaceTag('PRICE_VAT', 'price', $this->x->full_price_sk);
            }
        }


        private
        function LoadProductAttributeImages()
        {

            $this->variants_images = array();

            if (!SHAIM_COMBINATIONS) {
                return;
            }

            $all = $this->QueryFA("
            SELECT a.id_product_attribute, a.id_image, b.cover FROM product_attribute_image as a
            INNER JOIN image_shop as b ON (a.id_image = b.id_image && b.id_shop = " . $this->id_shop . ")
            ORDER BY b.cover DESC
            ;", false, 'assoc');
            if ($all) {
                foreach ($all as $a) {
                    // Záměrně potřebuje id_image jako string!
                    $this->variants_images[(int)$a['id_product_attribute']][] = array(
                        'image' => $this->GetImagePath($a['id_image']),
                        'id_image' => $a['id_image'],
                        'cover' => (int)$a['cover'],
                    );

                }

            }
        }

        public
        function ExportAll($feeds)
        {

            $this->feeds = $feeds; # Zadávejte vždy ne s tečkou, ale s pomlčkou


            $this->LoadProductAttributeImages();

            if (isset($_GET['glami'])) {
                if (empty($this->glami_category_whitelist)) {
                    echo 'Nejsou žádné povolené kategorie pro export do ' . $feeds[0] . PHP_EOL;
                    return false;
                } else {
                    $sql = 'SELECT DISTINCT cp.id_product FROM category_product as cp
                    INNER JOIN product_shop as ps ON (cp.id_product = ps.id_product && ps.active = 1 && ps.visibility != "none" && ps.available_for_order = 1)
                    INNER JOIN category as c ON (cp.id_category = c.id_category && c.active = 1)
                    WHERE (cp.id_category = ' . implode(' || cp.id_category = ', $this->glami_category_whitelist) . ')';
                    $whitelist_tmp = $this->QueryFA($sql, false, 'assoc');
                    $whitelist = array();
                    foreach ($whitelist_tmp as $w) {
                        $id_product = (int)$w['id_product'];
                        $whitelist[$id_product] = $id_product;
                    }

                    foreach ($this->list_all_products as $id => $row) {
                        if (!isset($whitelist[$row['id_product']])) {
                            $this->skipped++;
                            unset($this->list_all_products[$row['id_product']]);
                        }
                        /*
                        $whitelist_ok = false;
                        foreach ($whitelist as $whitelist_id => $whitelist_row) {

                            // if ($whitelist_id == $id) {
                            if ($whitelist_row['id_product'] == $row['id_product']) {
                                $whitelist_ok = true;
                                break 1;
                            }
                        }
                        if ($whitelist_ok == false) {
                            $this->skipped++;

                            unset($this->list_all_products[$id]);
                        }
                        */
                    }
                    // Optimalizace pameti
                    // unset($whitelist);
                    if (empty($this->list_all_products)) {
                        return false;
                    }
                }
            } elseif (isset($_GET['pricemania'])) {
                if (empty($this->pricemania_category_whitelist)) {
                    echo 'Nejsou žádné povolené kategorie pro export do ' . $feeds[0] . PHP_EOL;
                    return false;
                } else {
                    $sql = 'SELECT DISTINCT cp.id_product FROM category_product as cp
                    INNER JOIN product_shop as ps ON (cp.id_product = ps.id_product && ps.active = 1 && ps.visibility != "none" && ps.available_for_order = 1)
                    INNER JOIN category as c ON (cp.id_category = c.id_category && c.active = 1)
                    WHERE (cp.id_category = ' . implode(' || cp.id_category = ', $this->glami_category_whitelist) . ')';
                    $whitelist_tmp = $this->QueryFA($sql, false, 'assoc');
                    $whitelist = array();
                    foreach ($whitelist_tmp as $w) {
                        $id_product = (int)$w['id_product'];
                        $whitelist[$id_product] = $id_product;
                    }

                    foreach ($this->list_all_products as $id => $row) {
                        if (!isset($whitelist[$row['id_product']])) {
                            $this->skipped++;
                            unset($this->list_all_products[$row['id_product']]);
                        }
                        /*
                        $whitelist_ok = false;
                        foreach ($whitelist as $whitelist_id => $whitelist_row) {

                            // if ($whitelist_id == $id) {
                            if ($whitelist_row['id_product'] == $row['id_product']) {
                                $whitelist_ok = true;
                                break 1;
                            }
                        }
                        if ($whitelist_ok == false) {
                            $this->skipped++;

                            unset($this->list_all_products[$id]);
                        }
                        */
                    }
                    // Optimalizace pameti
                    // unset($whitelist);
                    if (empty($this->list_all_products)) {
                        return false;
                    }
                }
            }


            $total = count($this->list_all_products);
            $now = 0;

            if ($this->open == true) {
                $this->FirstStep();
            }


            foreach ($this->list_all_products as $this->row) {

                if ($this->row['vat'] == NULL) {
                    $this->row['vat'] = 0;
                }
                if (!isset($this->row['shaim_export_name'])) {
                    $this->row['shaim_export_name'] = '';
                } elseif (!empty($this->row['shaim_export_name'])) {
                    $this->row['name'] = $this->row['shaim_export_name'];
                }


                if (!isset($this->row['shaim_export_gifts'])) {
                    $this->row['shaim_export_gifts'] = '';
                }


                if (!isset($this->row['id_tax_rules_group']) || $this->row['id_tax_rules_group'] == NULL) {
                    $this->row['id_tax_rules_group'] = 0;
                }
                // Vat fix
                if ($this->row['vat'] == 0 && $this->row['id_tax_rules_group'] > 0) {
                    if (!isset($this->taxes[$this->row['id_tax_rules_group']])) {
                        $this->taxes[$this->row['id_tax_rules_group']] = $this->row['vat'] = $this->QueryR("SELECT b.rate FROM tax_rule as a INNER JOIN tax as b ON (a.id_tax = b.id_tax && a.id_tax_rules_group = {$this->row['id_tax_rules_group']} && b.active = 1 && b.deleted = 0 && a.id_country = " . COUNTRY_DEFAULT . ")");
                    } else {
                        $this->row['vat'] = $this->taxes[$this->row['id_tax_rules_group']];
                    }

                }


                $this->LoadAll();


# If price is 0, don't add to xml #
                if ($this->add == false) {


                    $this->skipped++;
                    continue;
                }

                $now++;

                // Pro každý produkt jiné
                $this->variant_count_tmp = 0;
                // $this->x->extra_params = '';
                $this->item_id = (int)$this->row['id_product'];
                $this->count++;

                // Pro produkty bez variant jeste gift:
                if ($this->real_price_no_round > $this->gifts_price_global) {
                    $this->gifts = $this->gifts_global;
                    if (!empty($this->row['shaim_export_gifts'])) {
                        $this->gifts = $this->row['shaim_export_gifts'];
                    }
                    if (!empty($this->gifts)) {
                        $this->gifts = explode(',', $this->gifts);
                        $this->gifts = array_map('trim', $this->gifts);
                    }
                }


                do {


// Varianty pro heureku
                    $this->x->extra_params = $this->x->extra_params_sk = '';
                    // $this->glami_sk_size = '';

                    if (count($this->variants_add) > 0) {
                        if (SHAIM_ONLY_STOCK && STOCK_MANAGEMENT && (!isset($this->stock[$this->row['id_product']][$this->variants_add[$this->variant_count_tmp]['id_product_attribute']]) || empty($this->stock[$this->row['id_product']][$this->variants_add[$this->variant_count_tmp]['id_product_attribute']]))) {
                            $this->variant_count_tmp++;
                            continue;
                        }

                        $use_for_name = str_replace('###', ' / ', $this->variants_add[$this->variant_count_tmp]['value']);
                        if (isset($this->variants_sk_add[$this->variant_count_tmp]['value'])) {
                            $use_for_name_sk = str_replace('###', ' / ', $this->variants_sk_add[$this->variant_count_tmp]['value']);
                        } else {
                            $use_for_name_sk = $use_for_name;
                        }


                        $name = $this->AddCDATA($this->better_pair_manufacturer . $this->row['name'] . ' - ' . $use_for_name . $this->better_pair_code);
                        $name_sk = $this->AddCDATA($this->better_pair_manufacturer . $this->row['name_sk'] . ' - ' . $use_for_name_sk . $this->better_pair_code);
                        $name_bonus_cz = $this->AddCDATA($this->better_pair_manufacturer . $this->row['name'] . ' - ' . $use_for_name . $this->better_pair_code);
                        $name_bonus_sk = $this->AddCDATA($this->better_pair_manufacturer . $this->row['name_sk'] . ' - ' . $use_for_name_sk . $this->better_pair_code);

                        // Heureka to má úplně obráceně než zboží, omg why.
                        // CZ
                        $this->x->name_heureka = $this->AddTag('PRODUCT', $name);
                        $this->x->name2_heureka = $this->AddTag('PRODUCTNAME', $name_bonus_cz);
                        $this->x->name_zbozi = $this->AddTag('PRODUCT', $name_bonus_cz);
                        $this->x->name2_zbozi = $this->AddTag('PRODUCTNAME', $name);

                        // SK
                        $this->x->name_heureka_sk = $this->AddTag('PRODUCT', $name_sk);
                        $this->x->name2_heureka_sk = $this->AddTag('PRODUCTNAME', $name_bonus_sk);
                        // $this->x->name_zbozi_sk = $this->AddTag('PRODUCT', $name_bonus_sk);
                        // $this->x->name2_zbozi_sk = $this->AddTag('PRODUCTNAME', $name_sk);


                        // Hlavní produkt nemá cenu, pouze varianty, tak tam hodíme slevičku
                        /* Tohle už řešíme asi v GetFullPrice();
                        if (isset($this->specific_price[$this->row['id_product']]) && $this->variants_add[$this->variant_count_tmp]['price'] > 0) {

                            $discount_price = $this->CountSpecificPrice($this->row['id_product'], $this->variants_add[$this->variant_count_tmp]['price'], $this->variants_add[$this->variant_count_tmp]['price_without_vat']);
                            // var_dump($discount_price);
                            // die;

                        $this->variants_add[$this->variant_count_tmp]['price'] = $discount_price;

                        }
                    */
                        $real_price_no_round_orig = $this->real_price_no_round;
                        // $this->variants_add[$this->variant_count_tmp]['price'] = 200;
                        if ($this->variants_add[$this->variant_count_tmp]['price'] > 0) {
                            // $price_with_vat = parent::AddVat($this->row['price'], $this->row['vat']);
                            // $this->real_price_no_round = $price_with_vat + $this->variants_add[$this->variant_count_tmp]['price'];

                            $this->real_price_no_round = $this->real_price_no_round + $this->variants_add[$this->variant_count_tmp]['price'];

                            // $tmp = $this->GetFullPrice('', $this->real_price_no_round);
                        } else {
                            // $tmp = array('price_no_round' => $this->real_price_no_round);
                        }


                        if ($this->real_price_no_round > $this->gifts_price_global) {
                            $this->gifts = $this->gifts_global;
                            if (!empty($this->row['shaim_export_gifts'])) {
                                $this->gifts = $this->row['shaim_export_gifts'];
                            }
                            if (!empty($this->gifts)) {
                                $this->gifts = explode(',', $this->gifts);
                                $this->gifts = array_map('trim', $this->gifts);
                            }
                        }

                        // díky za info. Poslední verze zčásti pomohla. Problém byl v tom, že ta předchozí načítala jen id_group = 0 a nepočítala s reduction_tax. Musel jsem však ještě upravit řádek 3755 v export6.php kvůli tomu, že se sčítaly specifické ceny jak z hlavního produktu (id_attribute = 0) tak i z jednotlivých kombinací. Na eshopu se totiž bere jen ta konkrétní specifická cena a nekumulují se. Mohl byste to prosím ještě prověřit, jestli tu změnu tam mám nechat, či to musím vyřešit nějak jinak. Jde mi o to, zda to pak zahrnete do dalších verzí (abychom o tuto změnu nepřišli při další aktualizaci).
                        // $tmp = $this->GetFullPrice('', $this->price_with_vat_original, $this->variants_add[$this->variant_count_tmp]['id_product_attribute']);
                        // $tmp = $this->GetFullPrice('', $this->real_price_no_round, $this->variants_add[$this->variant_count_tmp]['id_product_attribute']);
                        // koberec hint
                        $tmp = $this->GetFullPrice('', parent::AddVat($this->row['price'], $this->row['vat']) + $this->variants_add[$this->variant_count_tmp]['price'], $this->variants_add[$this->variant_count_tmp]['id_product_attribute']);

                        // $this->price_round = $tmp['price_round'];
                        $this->real_price_no_round = $tmp['price_no_round'];
                        $real_price_with_attributes = $this->real_price_no_round;
                        $this->real_price_no_round = $real_price_no_round_orig;

                        if (!empty($this->x->manufacturer)) {
                            // if (!empty($this->variants_add[$this->variant_count_tmp]['ean'])) {
                            if (!empty($this->variants_add[$this->variant_count_tmp]['ean']) || !empty($this->x->ean) || !empty($this->variants_add[$this->variant_count_tmp]['reference']) || !empty($this->row['reference'])) {
                                $identifier_exists = 'yes';
                                $this->identifier_exists = $this->AddTag('g:identifier_exists', $identifier_exists);
                            }
                        }


                        // $this->x->full_price = $this->AddTag('PRICE_VAT', $real_price_with_attributes);

                        $rt = ROUND_TYPE;
                        if ($this->force_currency == 'CZK') {
                            $conversion_rate_cz_tmp = 1;
                        } else {
                            $conversion_rate_cz_tmp = CONVERSION_RATE_CZ;
                        }
                        if (DECIMALS_CZ == 0) {
                            $this->x->full_price = $this->AddTag('PRICE_VAT', number_format($rt($real_price_with_attributes * $conversion_rate_cz_tmp), DECIMALS_CZ, ',', ''));
                        } else {
                            $this->x->full_price = $this->AddTag('PRICE_VAT', number_format($real_price_with_attributes * $conversion_rate_cz_tmp, DECIMALS_CZ, ',', ''));
                        }

                        if ($this->force_currency == 'EUR') {
                            $conversion_rate_sk_tmp = 1;
                        } else {
                            $conversion_rate_sk_tmp = CONVERSION_RATE_SK;
                        }
                        if (DECIMALS_SK == 0) {
                            $this->x->full_price_sk = $this->AddTag('PRICE_VAT', number_format($rt($real_price_with_attributes * $conversion_rate_sk_tmp), DECIMALS_SK, ',', ''));
                        } else {
                            $this->x->full_price_sk = $this->AddTag('PRICE_VAT', number_format($real_price_with_attributes * $conversion_rate_sk_tmp, DECIMALS_SK, ',', ''));
                        }

                        if ($this->force_currency == 'USD') {
                            $conversion_rate_us_tmp = 1;
                        } else {
                            $conversion_rate_us_tmp = CONVERSION_RATE_US;
                        }
                        if (DECIMALS_US == 0) {
                            $this->x->full_price_us = $this->AddTag('PRICE_VAT', number_format($rt($real_price_with_attributes * $conversion_rate_us_tmp), DECIMALS_US, ',', ''));
                        } else {
                            $this->x->full_price_us = $this->AddTag('PRICE_VAT', number_format($real_price_with_attributes * $conversion_rate_us_tmp, DECIMALS_US, ',', ''));
                        }

                        if ($this->force_currency == 'GBP') {
                            $conversion_rate_gb_tmp = 1;
                        } else {
                            $conversion_rate_gb_tmp = CONVERSION_RATE_GB;
                        }
                        if (DECIMALS_GB == 0) {
                            $this->x->full_price_gb = $this->AddTag('PRICE_VAT', number_format($rt($real_price_with_attributes * $conversion_rate_gb_tmp), DECIMALS_GB, ',', ''));
                        } else {
                            $this->x->full_price_gb = $this->AddTag('PRICE_VAT', number_format($real_price_with_attributes * $conversion_rate_gb_tmp, DECIMALS_GB, ',', ''));
                        }

                        $this->x->itemgroup_id = $this->AddTag('ITEMGROUP_ID', (int)$this->row['id_product']);
                        $this->x->extra_params .= $this->x->itemgroup_id;
                        $this->x->extra_params_sk .= $this->x->itemgroup_id;


                        // $reference_add = (!empty($this->row['reference']) ? '_' . $this->row['reference'] : '');
                        // $url_variant_add = '#' . $this->variants_add[$this->variant_count_tmp]['url_add'] . urlencode($reference_add);
                        $url_variant_add = '#' . $this->variants_add[$this->variant_count_tmp]['url_add'];
                        if (isset($this->variants_sk_add[$this->variant_count_tmp]['url_add'])) {
                            $url_variant_add_sk = '#' . $this->variants_sk_add[$this->variant_count_tmp]['url_add'];
                        } else {
                            $url_variant_add_sk = $url_variant_add;
                        }


                        if (isset($_GET['glami']) && $this->variants_add[$this->variant_count_tmp]['name'] == 'Varianta') {
                            $this->variants_add[$this->variant_count_tmp]['name'] = 'Velikost';
                        }

                        if (isset($_GET['glami']) && isset($this->variants_sk_add[$this->variant_count_tmp]['name']) && $this->variants_sk_add[$this->variant_count_tmp]['name'] == 'Varianta') {
                            $this->variants_sk_add[$this->variant_count_tmp]['name'] = 'Veľkosť';
                        }

                        if (isset($_GET['glami']) && ($this->variants_add[$this->variant_count_tmp]['name'] == 'Velikost' || $this->variants_add[$this->variant_count_tmp]['name'] == 'Veľkosť' || preg_match("/Velikost/i", $this->variants_add[$this->variant_count_tmp]['name']))) {
                            $glami_type_size = false;
                            if (preg_match("/[^0-9.]/", $this->variants_add[$this->variant_count_tmp]['value'])) {
                                $glami_type_size = 'INT';
                            } elseif ($this->variants_add[$this->variant_count_tmp]['value'] >= 30) {
                                $glami_type_size = 'EU';
                            } elseif ($this->variants_add[$this->variant_count_tmp]['value'] <= 15) {
                                // US nebo UK, nemame jak poznat
                                $glami_type_size = 'US';
                                // $glami_type_size = 'UK';
                            }

                            // Podporované hodnoty jsou: AU, BR, KN, DE, EU, FR, INT, IT, JP, MEX, RU, UK, USA. Vždy uveďte v parametru jen jednu hodnotu. Pro mezinárodní velikost XS, S, M, L, atd. použijte v parametru hodnotu INT.
                            if ($glami_type_size) {
                                $this->x->extra_params .= "<PARAM>" . NEW_LINE .
                                    $this->AddTag('PARAM_NAME', 'SIZE_SYSTEM') .
                                    $this->AddTag('VAL', $glami_type_size) .
                                    "</PARAM>" . NEW_LINE;
                                $this->x->extra_params_sk .= "<PARAM>" . NEW_LINE .
                                    $this->AddTag('PARAM_NAME', 'SIZE_SYSTEM') .
                                    $this->AddTag('VAL', $glami_type_size) .
                                    "</PARAM>" . NEW_LINE;
                            }
                        }


                        if (preg_match("/###/", $this->variants_add[$this->variant_count_tmp]['name'])) {
                            $tmp_name = explode("###", $this->variants_add[$this->variant_count_tmp]['name']);

                            $tmp_val = explode("###", $this->variants_add[$this->variant_count_tmp]['value']);
                            $tmp_i = 0;
                            foreach ($tmp_name as $e) {
                                $this->x->extra_params .= $this->AddTag('PARAM', $this->AddTag('PARAM_NAME', $this->AddCDATA($tmp_name[$tmp_i])) . $this->AddTag('VAL', $this->AddCDATA($tmp_val[$tmp_i])));
                                $tmp_i++;
                            }
                        } else {
                            $this->x->extra_params .= $this->AddTag('PARAM', NEW_LINE . $this->AddTag('PARAM_NAME', $this->AddCDATA($this->variants_add[$this->variant_count_tmp]['name'])) . $this->AddTag('VAL', $this->AddCDATA($this->variants_add[$this->variant_count_tmp]['value'])));
                        }


                        if (isset($this->variants_sk_add[$this->variant_count_tmp]['name'])) {
                            if (preg_match("/###/", $this->variants_sk_add[$this->variant_count_tmp]['name'])) {
                                $tmp_name = explode("###", $this->variants_sk_add[$this->variant_count_tmp]['name']);

                                $tmp_val = explode("###", $this->variants_sk_add[$this->variant_count_tmp]['value']);
                                $tmp_i = 0;
                                foreach ($tmp_name as $e) {
                                    $this->x->extra_params_sk .= $this->AddTag('PARAM', $this->AddTag('PARAM_NAME', $this->AddCDATA($tmp_name[$tmp_i])) . $this->AddTag('VAL', $this->AddCDATA($tmp_val[$tmp_i])));
                                    $tmp_i++;
                                }
                            } else {
                                $this->x->extra_params_sk .= $this->AddTag('PARAM', NEW_LINE . $this->AddTag('PARAM_NAME', $this->AddCDATA($this->variants_sk_add[$this->variant_count_tmp]['name'])) . $this->AddTag('VAL', $this->AddCDATA($this->variants_sk_add[$this->variant_count_tmp]['value'])));
                            }
                        } else {
                            $this->x->extra_params_sk = $this->x->extra_params;
                        }

                        /*
                        if ($this->variants_add[$this->variant_count_tmp]['name'] == 'Velikost' || $this->variants_add[$this->variant_count_tmp]['name'] == 'Varianta' || $this->variants_add[$this->variant_count_tmp]['name'] == 'Veľkosť' || $this->variants_add[$this->variant_count_tmp]['name'] == 'Rozmiar' || $this->variants_add[$this->variant_count_tmp]['name'] == 'Rozmiar (bras)') {
                            // $this->glami_sk_size = $this->variants_add[$this->variant_count_tmp]['value'];
                        }
                        */


                        $this->x->zbozi_cz = $this->AddFreeDelivery($real_price_with_attributes);

                        if (isset($this->variants_images[$this->variants_add[$this->variant_count_tmp]['id_product_attribute']])) {
                            $first_add = true;
                            $this->x->main_image = '';
                            $this->x->google_com_images = '';
                            $tmp_shopalike = 0;
                            foreach ($this->variants_images[$this->variants_add[$this->variant_count_tmp]['id_product_attribute']] as $vi) {

                                if ($vi['cover'] == 1) {
                                    $this->x->main_image = $this->AddTag('IMGURL', $vi['image']);
                                    $this->x->google_com_images = $this->AddTag('g:image_link', $vi['image']);
                                }

                                if ($first_add == true) {
                                    $this->x->shopalike_alt_image = $this->x->heureka_alt_image = $this->x->zbozi_alt_image = '';
                                    $first_add = false;
                                }

                                if (empty($this->x->main_image)) {
                                    $this->x->main_image = $this->AddTag('IMGURL', $vi['image']);
                                }
                                if (empty($this->x->google_com_images)) {
                                    $this->x->google_com_images = $this->AddTag('g:image_link', $vi['image']);
                                }

                                if ($vi['id_image'] != $this->row['main_image'] && $this->AddTag('IMGURL', $vi['image']) != $this->x->main_image) {
                                    $tmp_shopalike++;
                                    $this->x->shopalike_alt_image .= $this->AddTag('IMGURL_' . $tmp_shopalike, $vi['image']);
                                    $this->x->heureka_alt_image .= $this->AddTag('IMGURL_ALTERNATIVE', $vi['image']);
                                    $this->x->zbozi_alt_image .= $this->AddTag('IMGURL', $vi['image']);
                                    $this->x->google_com_images .= $this->AddTag('g:additional_image_link', $vi['image']);

                                }

                            }

                        }


                        $progress = $this->item_id = $this->row['id_product'] . '-' . $this->variants_add[$this->variant_count_tmp]['id_product_attribute'];
                        $this->x->delivery_date = $this->AddTag('DELIVERY_DATE', $this->GetDelivery((isset($this->stock[$this->row['id_product']][$this->variants_add[$this->variant_count_tmp]['id_product_attribute']]) ? $this->stock[$this->row['id_product']][$this->variants_add[$this->variant_count_tmp]['id_product_attribute']] : 0), $this->variants_add[$this->variant_count_tmp]['available_date']), false);
                        $this->x->google_weight = $this->AddTag('g:shipping_weight', $this->variants_add[$this->variant_count_tmp]['weight'] . ' g');
                        $this->x->google_id = $this->AddTag('g:id', $this->item_id);
                        if ($this->variants_add[$this->variant_count_tmp]['ean']) {
                            $this->x->google_ean = $this->AddTag('g:gtin', $this->variants_add[$this->variant_count_tmp]['ean']);
                        } else {
                            $this->x->google_ean = $this->ReplaceTag('EAN', 'g:gtin', $this->x->ean);
                        }
                        if ($this->variants_add[$this->variant_count_tmp]['reference']) {
                            $this->x->google_mpn = $this->AddTag('g:mpn', $this->AddCDATA($this->variants_add[$this->variant_count_tmp]['reference']));
                        } else {
                            $this->x->google_mpn = $this->AddTag('g:mpn', $this->AddCDATA($this->row['reference']));
                        }

                        // Zbozi a heureka
                        $strlen = strlen($this->variants_add[$this->variant_count_tmp]['ean']);
                        if ($strlen == 8 || $strlen == 12 || $strlen == 13 || $strlen == 14) {
                            $this->x->ean = $this->AddTag('EAN', $this->variants_add[$this->variant_count_tmp]['ean']);
                        }

                        // Toto je zde kvůli správné ceně poštovného (varianty / produkt s variant)
                        $this->AddDelivery($real_price_with_attributes, $this->row['weight'] + $this->variants_add[$this->variant_count_tmp]['weight']);
                    } else {
                        $progress = $this->item_id;
                        $url_variant_add = '';
                        $url_variant_add_sk = '';
                        $this->variants_add = array();
                        $this->x->delivery_date = $this->AddTag('DELIVERY_DATE', $this->GetDelivery($this->stock[$this->row['id_product']][0], $this->row['available_date']), false);
                        // Toto je zde kvůli správné ceně poštovného (varianty / produkt bez variant)
                        $this->AddDelivery($this->real_price_no_round, $this->row['weight']);
                    }

                    file_put_contents(XML_FOLDER . PROGRESS_FILE, ($now + $this->begin) . "(" . ($this->variant_count_tmp + 1) . ")/" . ($total + $this->begin) . "(" . count($this->variants_add) . ") ($progress)" . "\n", FILE_APPEND);


                    $utm_source = '';
                    if (SHAIM_UTM) {
                        $utm_source = '?utm_source=###SOURCE_SROVNAVAC###&utm_medium=cpc&utm_campaign=xml_export';
                    }
                    $this->x->orig_url = $this->GetURL();
                    $this->x->url = $this->AddTag('URL', $this->AddCDATA($this->x->orig_url . $url_variant_add . $utm_source));
                    $this->x->orig_url_sk = $this->GetURL('sk');
                    $this->x->url_sk = $this->AddTag('URL', $this->AddCDATA($this->x->orig_url_sk . $url_variant_add_sk . $utm_source));

                    $this->x->orig_url = $this->AddTag('URL', $this->AddCDATA($this->x->orig_url));
                    // predtim bylo g:link
                    $this->x->google_url = $this->ReplaceTag('URL', 'link', $this->x->url);
                    // predtim bylo g:title
                    $this->x->google_name = $this->ReplaceTag('PRODUCT', 'title', $this->x->name_heureka);
                    $this->x->google_price = $this->ReplaceTag('PRICE_VAT', 'g:price', str_replace('</PRICE_VAT>', ' ' . MENA . '</PRICE_VAT>', (($this->currency_default == 'EUR') ? $this->x->full_price_sk : (($this->currency_default == 'USD') ? $this->x->full_price_us : (($this->currency_default == 'GBP') ? $this->x->full_price_gb : $this->x->full_price)))));
                    $this->x->google_price_sk = $this->ReplaceTag('PRICE_VAT', 'g:price', str_replace('</PRICE_VAT>', ' EUR</PRICE_VAT>', $this->x->full_price_sk));
                    $this->x->google_price_us = $this->ReplaceTag('PRICE_VAT', 'g:price', str_replace('</PRICE_VAT>', ' USD</PRICE_VAT>', $this->x->full_price_us));
                    $this->x->google_price_gb = $this->ReplaceTag('PRICE_VAT', 'g:price', str_replace('</PRICE_VAT>', ' GBP</PRICE_VAT>', $this->x->full_price_gb));


                    foreach ($this->feeds as $feed) {

                        if (!isset($this->variants_add[$this->variant_count_tmp]['id_product_attribute'])) {
                            $this->variants_add[$this->variant_count_tmp]['id_product_attribute'] = 0;
                        }

                        // Toto resime pres visibility, ale vlastne uplne zbytecne.
                        if ($feed == 'zbozi_cz' && $this->condition['zbozi_cz'] != 'new') {
                            continue;
                        }

                        if ($feed == 'glami_cz' && $this->glami_cz_add == false) {
                            continue;
                        } elseif ($feed == 'glami_sk' && $this->glami_sk_add == false) {
                            continue;
                        }


                        if (($feed != 'heureka_dostupnost') ||
                            ($feed == 'heureka_dostupnost' && !empty($this->stock[$this->row['id_product']][$this->variants_add[$this->variant_count_tmp]['id_product_attribute']]))

                        ) {
                            /*
                            if ((isset($_GET['glami']) && $feed == 'glami_sk')
                                ||
                                (isset($_GET['all']) && $feed == 'heureka_sk')
                            ) {
                                if ($this->id_lang_sk != $this->id_lang) {
                                    $this->x->url = str_replace('/cs/', '/sk/', $this->x->url);
                                    $this->x->orig_url = str_replace('/cs/', '/sk/', $this->x->orig_url);
                                }
                            }
                            */

                            $this->x->category = (isset($this->x->categories[$feed]) ? $this->x->categories[$feed] : $this->x->categories['full']);
                            if (isset($_GET['glami'])) {
                                $this->x->category .= $this->AddTag('CATEGORY_ID', $this->row['id_category_default']);
                            }
                            $this->Export($feed);
                            $this->SecondStep($feed);
                        }

                    }

                    $this->variant_count_tmp++;

                } while (count($this->variants_add) > $this->variant_count_tmp);
                $this->variant_count += $this->variant_count_tmp;
            }
            if (isset($_GET['close'])) {
                $this->ThirdStep();


                if (($this->debug_id_product || $this->end == 1) && isset($this->row)) {
                    print_R($this->row);
                }
            }
        }

        public
        function Export($type)
        {

            Switch ($type) {
                case 'zbozi_cz':
                    $this->ExportZboziCz();
                    break;
                case 'heureka_cz':
                    $this->ExportHeurekaCz();
                    break;
                case 'hledejceny_cz':
                    $this->ExportHledejCenyCz();
                    break;
                case 'shopalike_sk':
                    $this->ExportShopAlikeSk();
                    break;
                case 'shopalike_cz':
                    $this->ExportShopAlikeCz();
                    break;
                case 'glami_cz':
                    $this->ExportGlamiCz();
                    break;
                case 'glami_sk':
                    $this->ExportGlamiSk();
                    break;
                case 'pricemania_cz':
                    $this->ExportPricemaniaCz();
                    break;
                case 'pricemania_sk':
                    $this->ExportPricemaniaSK();
                    break;
                case 'heureka_sk':
                    $this->ExportHeurekaSk();
                    break;
                case 'google_com':
                    $this->ExportGoogleCom();
                    break;
                case 'facebook_com':
                    $this->ExportFacebookCom();
                    break;
                case 'heureka_dostupnost':
                    $this->ExportHeurekaCzDoplnek();
                    break;
            }
        }

        public
        function ExportZboziCz()
        {

            $this->xml = NEW_LINE .
                $this->AddTag('ITEM_ID', $this->item_id) .
                $this->x->name_zbozi .
                $this->x->name2_zbozi .
                $this->x->desc .
                str_replace('###SOURCE_SROVNAVAC###', 'zbozi_cz', $this->x->url) .
                $this->x->main_image .
                $this->x->zbozi_alt_image .
                $this->x->full_price .
                $this->x->manufacturer . // Vyrobce
                $this->ReplaceTag('MANUFACTURER', 'BRAND', $this->x->manufacturer) . // Znacka
                $this->x->delivery_date .
                $this->x->category .
                $this->x->reference .
                $this->x->ean .

                $this->x->extra_params .
                $this->x->params;

            if (MAX_CPC >= 0 && (!MAX_CPC_LIMIT || MAX_CPC_LIMIT >= strip_tags($this->x->full_price))) {
                $this->xml .= $this->AddTag('MAX_CPC', MAX_CPC);
            }
            if (MAX_CPC_SEARCH >= 0 && (!MAX_CPC_LIMIT || MAX_CPC_LIMIT >= strip_tags($this->x->full_price))) {
                $this->xml .= $this->AddTag('MAX_CPC_SEARCH', MAX_CPC_SEARCH);
            }
            if ($this->condition['zbozi_cz'] != 'new') {
                /* tag ITEM_TYPE již Zboží.cz nepodporuje. Sloužilo k rozpoznání nové verz. bazarové položky. Bazarové položky již není možné na Zboží.cz inzerovat.



         // Tohle vlastne nebude uz asi fungovat, protoze nakonec zbozi uplne preskakujeme, neni duvod, aby to tam bylo.
Tag VISIBILITY slouží k tomu, zda se bude, nebo nebude položka ne webu Zboží.cz zobrazovat. */
                // $this->xml .= $this->AddTag('ITEM_TYPE', $this->condition['zbozi_cz']);
                $this->xml .= $this->AddTag('VISIBILITY', 0);
            }
            if (preg_match("/erotic/i", $this->x->name_zbozi)) {
                $this->xml .= $this->AddTag('EROTIC', 1);
            }
            if (!empty($this->gifts)) {
                $this->xml .= $this->AddTag('EXTRA_MESSAGE', 'free_gift');
                foreach ($this->gifts as $tmp_gift) {
                    $this->xml .= '<FREE_GIFT_TEXT>' . $this->AddCDATA($tmp_gift) . '</FREE_GIFT_TEXT>';
                }
            }
            // if (ODBER_ZDARMA == 1) {
            //     $this->xml .= $this->AddTag('EXTRA_MESSAGE', 'free_store_pickup');
            // }

            if (!empty($this->depot_ids_zbozi)) {
                foreach ($this->depot_ids_zbozi as $depot_id) {
                    if ($depot_id > 0) {
                        $this->xml .= $this->AddTag('SHOP_DEPOTS', $depot_id);
                    }
                }
            }
            $this->xml .= $this->x->zbozi_cz;
            $this->xml .= $this->x->zbozi_delivery_cz;
        }

        public
        function ExportHeurekaCz()
        {

            $this->xml = NEW_LINE .
                $this->AddTag('ITEM_ID', $this->item_id) .
                $this->x->name_heureka .
                $this->x->name2_heureka .
                $this->x->desc .
                str_replace('###SOURCE_SROVNAVAC###', 'heureka_cz', $this->x->url) .
                $this->x->main_image .
                $this->x->heureka_alt_image .
                $this->x->heureka_videa .
                $this->x->full_price .
                // kvuli heureka kosiku
                $this->AddTag('VAT', (int)$this->row['vat']) .
                $this->x->manufacturer .
                $this->x->delivery_date .
                $this->x->category .
                $this->x->ean .
                $this->x->reference .
                $this->x->extra_params .
                $this->x->params;
            if (HEUREKA_CPC >= 0 && (!MAX_CPC_LIMIT || MAX_CPC_LIMIT >= strip_tags($this->x->full_price))) {
                $this->xml .= $this->AddTag('HEUREKA_CPC', number_format(HEUREKA_CPC * CONVERSION_RATE_CZ, DECIMALS_CZ, ',', ''));
            }
            if ($this->condition['heureka_cz'] != 'new') {
                $this->xml .= $this->AddTag('ITEM_TYPE', $this->condition['heureka_cz']);
            }
            if (!empty($this->gifts)) {
                foreach ($this->gifts as $tmp_gift) {
                    // $this->xml .= '<GIFT ID="' . crc32($tmp_gift) . '">' . $this->AddCDATA($tmp_gift) . '</GIFT>';
                    $this->xml .= '<GIFT>' . $this->AddCDATA($tmp_gift) . '</GIFT>';
                }
            }
            $this->xml .= $this->x->heureka_delivery_cz;
        }

        public
        function ExportHledejCenyCz()
        {

            $this->xml = NEW_LINE .
                $this->AddTag('ID', $this->item_id) .
                $this->x->name_heureka .
                //   $this->x->name2_heureka .
                $this->x->desc .
                str_replace('###SOURCE_SROVNAVAC###', 'hledejceny_cz', $this->x->url) .
                $this->x->main_image .
                //    $this->x->heureka_alt_image .
                //    $this->x->heureka_videa .
                $this->x->full_price .
                // kvuli heureka kosiku
                //     $this->AddTag('VAT', (int)$this->row['vat']) .
                $this->x->manufacturer .
                // $this->x->delivery_date .
                $this->AddTag('DELIVERY_COST', 0) .
                $this->AddTag('DELIVERY_DATE', 2) .
                $this->AddTag('WARRANTY', 2) .

                $this->x->category .
                $this->x->ean .
                $this->x->reference .
                $this->x->extra_params .
                $this->x->params;
            //   if (HEUREKA_CPC >= 0) {
            //       $this->xml .= $this->AddTag('HEUREKA_CPC', number_format(HEUREKA_CPC * CONVERSION_RATE_CZ, DECIMALS_CZ, ',', ''));
            //   }
            //   if ($this->condition['heureka_cz'] != 'new') {
            //       $this->xml .= $this->AddTag('ITEM_TYPE', $this->condition['heureka_cz']);
            //   }
            //   $this->xml .= $this->x->heureka_delivery_cz;
        }


        public
        function ExportShopAlikeCz()
        {
            $gender = '';
            if (preg_match("/Pánsk/", $this->first_full['full'])) {
                $gender = $this->AddTag('GENDER', 'Pánské');
            } elseif (preg_match("/Dámsk/", $this->first_full['full'])) {
                $gender = $this->AddTag('GENDER', 'Dámské');
            }
            $cats_shopalike = explode(' | ', $this->first_full['full']);
            $cats_add = '';
            if ($cats_shopalike) {
                foreach ($cats_shopalike as $key => $tmp_cat) {
                    if ($key == 0) {
                        $cats_add .= $this->AddTag('TOPCATEGORY', $this->AddCDATA($tmp_cat));
                    } elseif ($key == 1) {
                        $cats_add .= $this->AddTag('CATEGORY', $this->AddCDATA($tmp_cat));
                    } elseif ($key == 2) {
                        $cats_add .= $this->AddTag('SUBCATEGORY', $this->AddCDATA($tmp_cat));
                    }

                }
            }
            $this->xml = NEW_LINE .
                $this->AddTag('ID', $this->item_id) .
                $this->x->name_heureka .
                //   $this->x->name2_heureka .
                $this->x->desc .
                str_replace('###SOURCE_SROVNAVAC###', 'shopalike_cz', $this->x->url) .
                $this->x->main_image .
                $this->x->shopalike_alt_image .
                //    $this->x->heureka_videa .
                (($this->price_with_vat_original != $this->real_price_no_round) ? $this->AddTag('OLDPRICE', number_format($this->price_with_vat_original * CONVERSION_RATE_CZ, DECIMALS_CZ, ',', '')) : '') .
                $this->x->full_price .
                // kvuli heureka kosiku
                //     $this->AddTag('VAT', (int)$this->row['vat']) .
                $this->x->manufacturer .
                $this->x->delivery_date .
                $cats_add .
                $this->x->ean .
                $this->x->reference .
                $this->x->extra_params .
                $this->x->params . $gender;
            //   if (HEUREKA_CPC >= 0) {
            //       $this->xml .= $this->AddTag('HEUREKA_CPC', number_format(HEUREKA_CPC * CONVERSION_RATE_CZ, DECIMALS_CZ, ',', ''));
            //   }
            //   if ($this->condition['heureka_cz'] != 'new') {
            //       $this->xml .= $this->AddTag('ITEM_TYPE', $this->condition['heureka_cz']);
            //   }
            //   $this->xml .= $this->x->heureka_delivery_cz;
        }


        public
        function ExportShopAlikeSk()
        {
            $gender = '';
            if (preg_match("/Pánsk/", $this->first_full['full'])) {
                $gender = $this->AddTag('GENDER', 'Pánske');
            } elseif (preg_match("/Dámsk/", $this->first_full['full'])) {
                $gender = $this->AddTag('GENDER', 'Dámske');
            }

            $cats_shopalike = explode(' | ', $this->first_full['full']);
            $cats_add = '';
            if ($cats_shopalike) {
                foreach ($cats_shopalike as $key => $tmp_cat) {
                    if ($key == 0) {
                        $cats_add .= $this->AddTag('TOPCATEGORY', $this->AddCDATA($tmp_cat));
                    } elseif ($key == 1) {
                        $cats_add .= $this->AddTag('CATEGORY', $this->AddCDATA($tmp_cat));
                    } elseif ($key == 2) {
                        $cats_add .= $this->AddTag('SUBCATEGORY', $this->AddCDATA($tmp_cat));
                    }

                }
            }

            $this->xml = NEW_LINE .
                $this->AddTag('ID', $this->item_id) .
                $this->x->name_heureka .
                //   $this->x->name2_heureka .
                $this->x->desc .
                str_replace('###SOURCE_SROVNAVAC###', 'shopalike_sk', $this->x->url) .
                $this->x->main_image .
                $this->x->shopalike_alt_image .
                //    $this->x->heureka_videa .
                (($this->price_with_vat_original != $this->real_price_no_round) ? $this->AddTag('OLDPRICE', number_format($this->price_with_vat_original * CONVERSION_RATE_SK, DECIMALS_SK, ',', '')) : '') .
                $this->x->full_price_sk .
                // kvuli heureka kosiku
                //     $this->AddTag('VAT', (int)$this->row['vat']) .
                $this->x->manufacturer .
                $this->x->delivery_date .
                $cats_add .
                $this->x->ean .
                $this->x->reference .
                $this->x->extra_params_sk .
                $this->x->params_sk . $gender;
            //   if (HEUREKA_CPC >= 0) {
            //       $this->xml .= $this->AddTag('HEUREKA_CPC', number_format(HEUREKA_CPC * CONVERSION_RATE_CZ, DECIMALS_CZ, ',', ''));
            //   }
            //   if ($this->condition['heureka_cz'] != 'new') {
            //       $this->xml .= $this->AddTag('ITEM_TYPE', $this->condition['heureka_cz']);
            //   }
            //   $this->xml .= $this->x->heureka_delivery_cz;
        }


        public
        function ExportGlamiCz()
        {
            $this->xml = NEW_LINE .
                $this->AddTag('ITEM_ID', $this->item_id) .
                $this->x->name_glami .
                $this->x->desc .
                str_replace('###SOURCE_SROVNAVAC###', 'glami_cz', $this->x->url) .
                $this->x->main_image .
                $this->x->heureka_alt_image .
                str_replace('</PRICE_VAT>', ' CZK</PRICE_VAT>', $this->x->full_price) .
                $this->x->manufacturer .
                $this->x->delivery_date .
                // implode('', $this->x->category) .
                $this->x->category .
                $this->x->ean .
                $this->x->reference .
                $this->x->extra_params .
                $this->x->params .
                $this->x->heureka_delivery_cz;
        }

        public
        function ExportGlamiSk()
        {

            /*
         $this->xml = NEW_LINE .
             $this->AddTag('item', $this->item_id) .
             $this->ReplaceTag('PRODUCT', 'title', $this->ReplaceTag('PRODUCTNAME', 'title', $this->x->name_glami_sk)) .
             $this->ReplaceTag('DESCRIPTION', 'description', $this->x->desc_sk) .
             $this->ReplaceTag('URL', 'link', $this->x->orig_url) .
             $this->ReplaceTag('URL', 'mobile_link', $this->x->orig_url) .
             $this->ReplaceTag('IMGURL', 'image_link', $this->x->main_image) .
             $this->ReplaceTag('IMGURL_ALTERNATIVE', 'additional_image_link', $this->x->heureka_alt_image) .
             $this->ReplaceTag('PRICE_VAT', 'price', $this->x->full_price_sk) .
             $this->ReplaceTag('MANUFACTURER', 'brand', $this->x->manufacturer) .
             $this->AddTag('availability', 'in stock') .
             $this->ReplaceTag('CATEGORYTEXT', 'product_category', $this->x->category) .
             $this->ReplaceTag('EAN', 'gtin', $this->x->ean) .
             $this->ReplaceTag('PRODUCTNO', 'mpn', $this->x->reference) .
             $this->ReplaceTag('ITEMGROUP_ID', 'item_group_id', $this->x->extra_params_sk) .
             $this->AddTag('size_system', 'EU') .
             $this->AddTag('size', $this->glami_sk_size);
             */

            $this->xml = NEW_LINE .
                $this->AddTag('ITEM_ID', $this->item_id) .
                $this->x->name_glami_sk .
                $this->x->desc_sk .
                str_replace('###SOURCE_SROVNAVAC###', 'glami_sk', $this->x->url_sk) .
                $this->x->main_image .
                $this->x->heureka_alt_image .
                str_replace('</PRICE_VAT>', ' EUR</PRICE_VAT>', $this->x->full_price_sk) .
                $this->x->manufacturer .
                $this->x->delivery_date .
                // implode('', $this->x->category) .
                $this->x->category .
                $this->x->ean .
                $this->x->reference .
                str_replace('Velikost', 'Veľkosť', str_replace('Barva', 'Farba', $this->x->extra_params_sk)) .
                str_replace('Velikost', 'Veľkosť', str_replace('Barva', 'Farba', $this->x->params_sk)) .
                $this->x->heureka_delivery_sk;

        }

        public
        function ExportPricemaniaCz()
        {

            $this->xml = $this->AddTag('id', $this->item_id) . $this->ReplaceTag('PRODUCT', 'name', $this->x->name_heureka) . $this->ReplaceTag('PRICE_VAT', 'price', $this->x->full_price) . $this->ReplaceTag('URL', 'url', str_replace('###SOURCE_SROVNAVAC###', 'pricemania_cz', $this->x->url)) . $this->x->pricemania_cz;
        }


        public
        function ExportPricemaniaSK()
        {
            $this->xml = $this->AddTag('id', $this->item_id) . $this->ReplaceTag('PRODUCT', 'name', $this->x->name_heureka) . $this->ReplaceTag('PRICE_VAT', 'price', $this->x->full_price_sk) . $this->ReplaceTag('URL', 'url', str_replace('###SOURCE_SROVNAVAC###', 'pricemania_sk', $this->x->url_sk)) . $this->x->pricemania_sk;
        }


        public
        function ExportHeurekaSK()
        {

            $this->xml = NEW_LINE .
                $this->AddTag('ITEM_ID', $this->item_id) .
                $this->x->name_heureka_sk .
                $this->x->name2_heureka_sk .
                $this->x->desc_sk .
                str_replace('###SOURCE_SROVNAVAC###', 'heureka_sk', $this->x->url_sk) .
                $this->x->main_image .
                $this->x->heureka_alt_image .
                $this->x->heureka_videa .
                $this->x->full_price_sk .
                // kvuli heureka kosiku
                $this->AddTag('VAT', (int)$this->row['vat']) .
                $this->x->manufacturer . $this->x->delivery_date .
                $this->x->category .
                $this->x->ean .
                $this->x->reference .
                $this->x->extra_params_sk .
                $this->x->params_sk;
            if (HEUREKA_CPC >= 0 && (!MAX_CPC_LIMIT || MAX_CPC_LIMIT >= strip_tags($this->x->full_price))) {
                $this->xml .= $this->AddTag('HEUREKA_CPC', number_format(HEUREKA_CPC * CONVERSION_RATE_SK, DECIMALS_SK, ',', ''));
            }

            if ($this->condition['heureka_cz'] != 'new') {
                $this->xml .= $this->AddTag('ITEM_TYPE', $this->condition['heureka_cz']);
            }
            if (!empty($this->gifts)) {
                foreach ($this->gifts as $tmp_gift) {
                    // $this->xml .= '<GIFT ID="' . crc32($tmp_gift) . '">' . $this->AddCDATA($tmp_gift) . '</GIFT>';
                    $this->xml .= '<GIFT>' . $this->AddCDATA($tmp_gift) . '</GIFT>';
                }
            }
            $this->xml .= $this->x->heureka_delivery_sk;
        }

        public
        function ExportGoogleCom()
        {


            $this->xml = NEW_LINE . $this->ReplaceTag('ITEMGROUP_ID', 'g:item_group_id', $this->x->itemgroup_id) . $this->x->google_com . $this->identifier_exists . $this->x->google_com_images . $this->x->google_id . $this->x->google_name . $this->x->google_weight . $this->x->google_ean . $this->x->google_mpn . str_replace('###SOURCE_SROVNAVAC###', 'google_com', $this->x->google_url) . $this->x->google_delivery . $this->x->google_extra;
            if ($this->force_lang == 'sk') {
                if (trim(str_replace(',', '.', str_replace(MENA, '', str_replace('<g:price>', '', str_replace('</g:price>', '', $this->x->google_price))))) == $this->x->old_price || empty($this->x->old_price)) {
                    $this->xml .= str_replace(',', '.', $this->x->google_price_sk);
                } else {
                    $this->xml .= $this->addTag('g:price', round(str_replace(',', '.', $this->x->old_price) * CONVERSION_RATE_SK, 2) . ' EUR') . $this->replaceTag('g:price', 'g:sale_price', str_replace(',', '.', $this->x->google_price_sk));
                }
            } elseif ($this->force_lang == 'de') {
                if (trim(str_replace(',', '.', str_replace(MENA, '', str_replace('<g:price>', '', str_replace('</g:price>', '', $this->x->google_price))))) == $this->x->old_price || empty($this->x->old_price)) {
                    $this->xml .= str_replace(',', '.', $this->x->google_price_sk);
                } else {
                    $this->xml .= $this->addTag('g:price', round(str_replace(',', '.', $this->x->old_price) * CONVERSION_RATE_SK, 2) . ' EUR') . $this->replaceTag('g:price', 'g:sale_price', str_replace(',', '.', $this->x->google_price_sk));
                }
            } elseif ($this->force_lang == 'en') {
                if (trim(str_replace(',', '.', str_replace(MENA, '', str_replace('<g:price>', '', str_replace('</g:price>', '', $this->x->google_price))))) == $this->x->old_price || empty($this->x->old_price)) {
                    $this->xml .= str_replace(',', '.', $this->x->google_price_us);
                } else {
                    $this->xml .= $this->addTag('g:price', round(str_replace(',', '.', $this->x->old_price) * CONVERSION_RATE_US, 2) . ' USD') . $this->replaceTag('g:price', 'g:sale_price', str_replace(',', '.', $this->x->google_price_us));
                }
            } elseif ($this->force_lang == 'gb') {
                if (trim(str_replace(',', '.', str_replace(MENA, '', str_replace('<g:price>', '', str_replace('</g:price>', '', $this->x->google_price))))) == $this->x->old_price || empty($this->x->old_price)) {
                    $this->xml .= str_replace(',', '.', $this->x->google_price_gb);
                } else {
                    $this->xml .= $this->addTag('g:price', round(str_replace(',', '.', $this->x->old_price) * CONVERSION_RATE_GB, 2) . ' GBP') . $this->replaceTag('g:price', 'g:sale_price', str_replace(',', '.', $this->x->google_price_gb));
                }
            } else {
                if (trim(str_replace(',', '.', str_replace(MENA, '', str_replace('<g:price>', '', str_replace('</g:price>', '', $this->x->google_price))))) == $this->x->old_price || empty($this->x->old_price)) {
                    $this->xml .= str_replace(',', '.', $this->x->google_price);
                } else {
                    $this->xml .= $this->addTag('g:price', str_replace(',', '.', $this->x->old_price) . ' ' . MENA) . $this->replaceTag('g:price', 'g:sale_price', str_replace(',', '.', $this->x->google_price));
                }
            }
            // sale_price_effective_date 

        }


        public
        function ExportFacebookCom()
        {


            $this->xml = NEW_LINE . $this->x->google_com . $this->identifier_exists . $this->x->google_com_images . $this->x->google_id . $this->x->google_name . $this->x->google_weight . $this->x->google_ean . $this->x->google_mpn . str_replace('###SOURCE_SROVNAVAC###', 'facebook_com', $this->x->google_url) . $this->x->google_delivery;
            if ($this->force_lang == 'sk') {
                if (trim(str_replace(',', '.', str_replace(MENA, '', str_replace('<g:price>', '', str_replace('</g:price>', '', $this->x->google_price))))) == $this->x->old_price || empty($this->x->old_price)) {
                    $this->xml .= str_replace(',', '.', $this->x->google_price_sk);
                } else {
                    $this->xml .= $this->addTag('g:price', round(str_replace(',', '.', $this->x->old_price) * CONVERSION_RATE_SK, 2) . ' EUR') . $this->replaceTag('g:price', 'g:sale_price', str_replace(',', '.', $this->x->google_price_sk));
                }
            } elseif ($this->force_lang == 'de') {
                if (trim(str_replace(',', '.', str_replace(MENA, '', str_replace('<g:price>', '', str_replace('</g:price>', '', $this->x->google_price))))) == $this->x->old_price || empty($this->x->old_price)) {
                    $this->xml .= str_replace(',', '.', $this->x->google_price_sk);
                } else {
                    $this->xml .= $this->addTag('g:price', round(str_replace(',', '.', $this->x->old_price) * CONVERSION_RATE_SK, 2) . ' EUR') . $this->replaceTag('g:price', 'g:sale_price', str_replace(',', '.', $this->x->google_price_sk));
                }
            } elseif ($this->force_lang == 'en') {
                if (trim(str_replace(',', '.', str_replace(MENA, '', str_replace('<g:price>', '', str_replace('</g:price>', '', $this->x->google_price))))) == $this->x->old_price || empty($this->x->old_price)) {
                    $this->xml .= str_replace(',', '.', $this->x->google_price_us);
                } else {
                    $this->xml .= $this->addTag('g:price', round(str_replace(',', '.', $this->x->old_price) * CONVERSION_RATE_US, 2) . ' USD') . $this->replaceTag('g:price', 'g:sale_price', str_replace(',', '.', $this->x->google_price_us));
                }
            } elseif ($this->force_lang == 'gb') {
                if (trim(str_replace(',', '.', str_replace(MENA, '', str_replace('<g:price>', '', str_replace('</g:price>', '', $this->x->google_price))))) == $this->x->old_price || empty($this->x->old_price)) {
                    $this->xml .= str_replace(',', '.', $this->x->google_price_gb);
                } else {
                    $this->xml .= $this->addTag('g:price', round(str_replace(',', '.', $this->x->old_price) * CONVERSION_RATE_GB, 2) . ' GBP') . $this->replaceTag('g:price', 'g:sale_price', str_replace(',', '.', $this->x->google_price_gb));
                }
            } else {
                if (trim(str_replace(',', '.', str_replace(MENA, '', str_replace('<g:price>', '', str_replace('</g:price>', '', $this->x->google_price))))) == $this->x->old_price || empty($this->x->old_price)) {
                    $this->xml .= str_replace(',', '.', $this->x->google_price);
                } else {
                    $this->xml .= $this->addTag('g:price', str_replace(',', '.', $this->x->old_price) . ' ' . MENA) . $this->replaceTag('g:price', 'g:sale_price', str_replace(',', '.', $this->x->google_price));
                }
            }
        }

        public
        function ExportHeurekaCzDoplnek()
        {

            if ($this->stock[$this->row['id_product']][$this->variants_add[$this->variant_count_tmp]['id_product_attribute']] > 0 || STOCK_MANAGEMENT == 0) {
                $this->xml = NEW_LINE . $this->AddTag('stock_quantity', $this->stock[$this->row['id_product']][$this->variants_add[$this->variant_count_tmp]['id_product_attribute']]) .
                    $this->AddTag('delivery_time orderDeadline="' . $this->orderdeadlineDost . '"', $this->deliverytimeDost, 'delivery_time');
                if (!empty($this->depot_ids_heureka)) {
                    foreach ($this->depot_ids_heureka as $depot_id) {
                        if ($depot_id > 0) {
                            $this->xml .= $this->AddTag('depot id="' . $depot_id . '"', NEW_LINE . $this->AddTag('stock_quantity', $this->stock[$this->row['id_product']][$this->variants_add[$this->variant_count_tmp]['id_product_attribute']]) . $this->AddTag('pickup_time orderDeadline="' . $this->orderdeadlinePick . '"', $this->deliverytimePick, 'pickup_time'), 'depot');
                        }
                    }
                }
            }

        }


        private
        function CacheAltImages()
        {


            $query_id_product = '';
            if ($this->debug_id_product) {
                $query_id_product = " && i.id_product = {$this->debug_id_product}";
            }

            $sql = "SELECT i.id_image, i.id_product FROM image as i
                      INNER JOIN image_shop as `is` ON (`is`.id_image = i.id_image && `is`.id_shop = {$this->id_shop})
                      WHERE (i.cover = 0 || i.cover IS NULL)$query_id_product ORDER BY i.position ASC;";


            if (isset($sql)) {
                $rows = $this->QueryFA($sql, false, 'assoc');

                $this->cache_alt_images = array();
                foreach ($rows as $row) {
                    $this->cache_alt_images[(int)$row['id_product']][] = (string)$row['id_image']; // id_image must be string
                }
            }
        }

        private
        function CacheSKNames()
        {


            $query_id_product = '';
            if ($this->debug_id_product) {
                $query_id_product = " && pl.id_product = {$this->debug_id_product}";
            }


            $tmps = $this->QueryFA("SELECT pl.name, pl.description, pl.description_short, pl.id_product, pl.link_rewrite FROM product_lang as pl
            INNER JOIN product_shop as ps ON (ps.id_product = pl.id_product && ps.active = 1 && ps.visibility != 'none' && pl.id_shop = {$this->id_shop})
            WHERE pl.id_lang = {$this->id_lang_sk}$query_id_product;", false, 'assoc');

            // Poslední INNER JOIN tam je kvůli optimalizaci paměťi
            $this->cache_sk_names = array();
            foreach ($tmps as $tmp) {

                $desc_use = false;
                if (SHAIM_DESC == 1) {
                    if (!empty($tmp['description'])) {
                        $desc_use = $tmp['description'];
                    } elseif (!empty($tmp['description_short'])) {
                        $desc_use = $tmp['description_short'];
                    }
                } else {
                    if (!empty($tmp['description_short'])) {
                        $desc_use = $tmp['description_short'];
                    } elseif (!empty($tmp['description'])) {
                        $desc_use = $tmp['description'];
                    }
                }
                if (empty($desc_use)) {
                    $desc_use = 'Bez popisu.';
                } else {
                    $desc_use = $this->SanitizeDescription($desc_use);
                    if (empty($desc_use)) {
                        $desc_use = 'Bez popisu.';
                    }
                    $desc_use = $this->AddCDATA($desc_use);
                }

                $this->cache_sk_names[(int)$tmp['id_product']] = array(
                    'name' => $tmp['name'],
                    // 'description' => $tmp['description'], // not used
                    // 'description_short' => $tmp['description_short'],// not used
                    'desc_use' => $desc_use,
                    'link_rewrite' => $tmp['link_rewrite'],
                );

            }

        }


        private
        function CacheCategories()
        {


            /* neni treba, chceme vsechny kategorie J.

                            $add = ' && c.id_shop_default = ' . $this->id_shop;
                            $add2 = ' && cl.id_shop = ' . $this->id_shop;

            */

//  && c.active = 1
            $sql = "SELECT
            cl.id_category, cl.name, c.id_parent, cl.link_rewrite
            FROM category_lang as cl
            INNER JOIN category as c ON (c.id_category = cl.id_category)
            WHERE id_lang = {$this->id_lang}
            ORDER BY c.level_depth ASC
            ;";
            $rows = $this->QueryFA($sql, false, 'assoc');

            $this->cache_categories = array();
            foreach ($rows as $row) {
                $this->cache_categories[(int)$row['id_category']] = array(
                    'id_category' => (int)$row['id_category'],
                    'name' => trim($row['name']),
                    'id_parent' => (int)$row['id_parent'],
                    'link_rewrite' => $row['link_rewrite'],
                );
            }


            if (isset($_GET['glami'])) {
                $whitelist_where = '';
                if (!empty($this->glami_category_whitelist)) {
                    $whitelist_where = 'WHERE (cp.id_category = ' . implode(' || cp.id_category = ', $this->glami_category_whitelist) . ')';
                }
                $sql = "
SELECT
cl.name, cl.link_rewrite, c.id_category, c.id_parent, p.id_product
FROM product as p
INNER JOIN product_shop as ps ON (ps.id_product = p.id_product && ps.active = 1 && ps.visibility != 'none' && ps.id_shop = {$this->id_shop})
INNER JOIN category_product as cp ON (cp.id_product = p.id_product && ps.id_category_default = cp.id_category)
INNER JOIN category_lang as cl ON (cl.id_category >= 2 && cl.id_category = cp.id_category && cl.id_lang = {$this->id_lang})
INNER JOIN category as c ON (cl.id_category = c.id_category)
$whitelist_where
ORDER BY c.level_depth ASC
";
                $rows = $this->QueryFA($sql, false, 'assoc');


                $this->cache_categories_glami_only = array();
                foreach ($rows as $row) {
                    $this->cache_categories_glami_only[(int)$row['id_product']][] = array(
                        'id_category' => (int)$row['id_category'],
                        'name' => trim($row['name']),
                        'id_parent' => (int)$row['id_parent'],
                        'link_rewrite' => $row['link_rewrite'],
                    );
                }

            } elseif (isset($_GET['pricemania'])) {
                $whitelist_where = '';
                if (!empty($this->pricemania_category_whitelist)) {
                    $whitelist_where = 'WHERE (cp.id_category = ' . implode(' || cp.id_category = ', $this->pricemania_category_whitelist) . ')';
                }
                $sql = "
SELECT
cl.name, cl.link_rewrite, c.id_category, c.id_parent, p.id_product
FROM product as p
INNER JOIN category_product as cp ON (cp.id_product = p.id_product && ps.id_category_default = cp.id_category)
INNER JOIN category_lang as cl ON (cl.id_category >= 2 && cl.id_category = cp.id_category && cl.id_lang = {$this->id_lang})
INNER JOIN category as c ON (cl.id_category = c.id_category)
$whitelist_where
ORDER BY c.level_depth ASC
";
                $rows = $this->QueryFA($sql, false, 'assoc');


                $this->cache_categories_pricemania_only = array();
                foreach ($rows as $row) {
                    $this->cache_categories_pricemania_only[(int)$row['id_product']][] = array(
                        'id_category' => (int)$row['id_category'],
                        'name' => trim($row['name']),
                        'id_parent' => (int)$row['id_parent'],
                        'link_rewrite' => $row['link_rewrite'],
                    );
                }


            }
        }

        private
        function CacheSKCategories()
        {


            /* neni treba, chceme vsechny kategorie J.

                            $add = ' && c.id_shop_default = ' . $this->id_shop;
                            $add2 = ' && cl.id_shop = ' . $this->id_shop;

            */

//  && c.active = 1
            $sql = "SELECT
            cl.id_category, cl.name, c.id_parent, cl.link_rewrite
            FROM category_lang as cl
            INNER JOIN category as c ON (c.id_category = cl.id_category)
            WHERE id_lang = {$this->id_lang_sk}
            ORDER BY c.level_depth ASC
            ;";
            $rows = $this->QueryFA($sql, false, 'assoc');

            $this->cache_categories_sk = array();
            foreach ($rows as $row) {
                $this->cache_categories_sk[(int)$row['id_category']] = array(
                    'id_category' => (int)$row['id_category'],
                    'name' => trim($row['name']),
                    'id_parent' => (int)$row['id_parent'],
                    'link_rewrite' => $row['link_rewrite'],
                );
            }

        }

        private
        function CachePair()
        {
            $this->cache_pair = array(
                'heureka_cz' => array(),
                'heureka_sk' => array(),
                'zbozi' => array(),
                'google' => array(),
            );

            if (PAIR_TYPE == 'full' || !isset($_GET['all'])) {
                return;
            }

            // $add = ' && a.id_shop_default = ' . $this->id_shop . ' && c.id_shop = ' . $this->id_shop;
            $add = ' && c.id_shop = ' . $this->id_shop;


            if (preg_match("/heureka/", PAIR_TYPE) && (ACTIVE_HEUREKA_CZ == 1 || ACTIVE_HEUREKA_SK == 1)) {
                $sql = "SELECT
a.id_product, b.heureka_full_name, c.link_rewrite, b.lang
FROM product_shop as a
INNER JOIN shaim_heureka as b ON (a.id_category_default = b.local_id)
INNER JOIN category_lang as c ON (c.id_category = b.local_id && c.id_lang = {$this->id_lang} && c.id_shop = {$this->id_shop})
WHERE b.heureka_full_name != '' && a.id_shop = {$this->id_shop}$add";


                $rows = $this->QueryFA($sql, false, 'assoc');

                foreach ($rows as $row) {
                    $this->cache_pair['heureka_' . $row['lang']][(int)$row['id_product']] = array('heureka_full_name' => $row['heureka_full_name'], 'link_rewrite' => $row['link_rewrite']);
                }

            }

            if (preg_match("/zbozi/", PAIR_TYPE) && ACTIVE_ZBOZI_CZ == 1) {
                $sql = "SELECT
a.id_product, b.zbozi_full_name, c.link_rewrite
FROM product_shop as a
INNER JOIN shaim_zbozi as b ON (a.id_category_default = b.local_id)
INNER JOIN category_lang as c ON (c.id_category = b.local_id && c.id_lang = {$this->id_lang} && c.id_shop = {$this->id_shop})
WHERE b.zbozi_full_name != '' && a.id_shop = {$this->id_shop}$add";

                $rows = $this->QueryFA($sql, false, 'assoc');

                foreach ($rows as $row) {
                    $this->cache_pair['zbozi'][(int)$row['id_product']] = array('zbozi_full_name' => $row['zbozi_full_name'], 'link_rewrite' => $row['link_rewrite']);
                }
            }

            if (preg_match("/google/", PAIR_TYPE) && (ACTIVE_GOOGLE_COM == 1 || ACTIVE_FACEBOOK_COM == 1)) {
                $sql = "SELECT
a.id_product, b.google_full_name, c.link_rewrite
FROM product_shop as a
INNER JOIN shaim_google as b ON (a.id_category_default = b.local_id)
INNER JOIN category_lang as c ON (c.id_category = b.local_id && c.id_lang = {$this->id_lang} && c.id_shop = {$this->id_shop})
WHERE b.google_full_name != '' && a.id_shop = {$this->id_shop}$add";

                $rows = $this->QueryFA($sql, false, 'assoc');

                foreach ($rows as $row) {
                    $this->cache_pair['google'][(int)$row['id_product']] = array('google_full_name' => $row['google_full_name'], 'link_rewrite' => $row['link_rewrite']);
                }
            }

        }

        private
        function CacheParams()
        {
            $this->cache_params = array();
            $sql = "SELECT value FROM configuration WHERE name = 'PS_FEATURE_FEATURE_ACTIVE' && value = 1;";
            if (!$this->QueryR($sql)) {
                return;
            }

            $query_id_product = '';
            if ($this->debug_id_product) {
                $query_id_product = " && ps.id_product = {$this->debug_id_product}";
            }


            $sql = "SELECT
                        a.id_product, b.name, c.value
                        FROM feature_product as a
                        LEFT JOIN feature_lang as b ON (b.id_feature = a.id_feature && b.id_lang = {$this->id_lang})
                        LEFT JOIN feature_value_lang as c ON (c.id_feature_value = a.id_feature_value && c.id_lang = {$this->id_lang})
                        INNER JOIN product_shop as ps ON (a.id_product = ps.id_product && ps.active = 1 && ps.visibility != 'none' && ps.id_shop = {$this->id_shop})
                        WHERE
                        b.name != '' && c.value != ''$query_id_product;";
            // Poslední INNER JOIN tam je kvůli optimalizaci paměťi


            $rows = $this->QueryFA($sql, false, 'assoc');

            foreach ($rows as $row) {
                $this->cache_params[(int)$row['id_product']][] = array('name' => $row['name'], 'value' => $row['value'],);
            }

        }

        private
        function CacheSKParams()
        {
            $this->cache_params_sk = array();
            $sql = "SELECT value FROM configuration WHERE name = 'PS_FEATURE_FEATURE_ACTIVE' && value = 1;";
            if (!$this->QueryR($sql)) {
                return;
            }

            $query_id_product = '';
            if ($this->debug_id_product) {
                $query_id_product = " && ps.id_product = {$this->debug_id_product}";
            }

            $sql = "SELECT
                        a.id_product, b.name, c.value
                        FROM feature_product as a
                        LEFT JOIN feature_lang as b ON (b.id_feature = a.id_feature && b.id_lang = {$this->id_lang_sk})
                        LEFT JOIN feature_value_lang as c ON (c.id_feature_value = a.id_feature_value && c.id_lang = {$this->id_lang_sk})
                        INNER JOIN product_shop as ps ON (a.id_product = ps.id_product && ps.active = 1 && ps.visibility != 'none' && ps.id_shop = {$this->id_shop})
                        WHERE
                        b.name != '' && c.value != ''$query_id_product;";
            // Poslední INNER JOIN tam je kvůli optimalizaci paměťi


            $rows = $this->QueryFA($sql, false, 'assoc');

            foreach ($rows as $row) {
                $this->cache_params_sk[(int)$row['id_product']][] = array('name' => $row['name'], 'value' => $row['value'],);
            }
        }


        private
        function CacheVariants()
        {
            $this->cache_variants = array();

            if (!SHAIM_COMBINATIONS) {
                return;
            }
            $sql = "SELECT value FROM configuration WHERE name = 'PS_COMBINATION_FEATURE_ACTIVE' && value = 1;";
            if (!$this->QueryR($sql)) {
                return;
            }


            // Nefunguje tak, jak jsme chteli... :-(
            $one_loop_limit = 500000;
            $offset = 0;

            $query_id_product = '';
            if ($this->debug_id_product) {
                $query_id_product = " && ps.id_product = {$this->debug_id_product}";
            }

            do {


                $sql = "SELECT
              pa.id_product, pa.ean13, pa.reference,
              ats.price, ats.id_product_attribute, ats.weight, ats.available_date,
              pac.id_attribute,
              al.name as value,
              agl.name as name
              FROM product_attribute as pa
              INNER JOIN product_attribute_combination as pac ON (pac.id_product_attribute = pa.id_product_attribute)
              INNER JOIN attribute_lang as al ON (al.id_attribute = pac.id_attribute && al.id_lang = {$this->id_lang})
              INNER JOIN attribute as a ON (a.id_attribute = al.id_attribute)
              INNER JOIN attribute_group as ag ON (ag.id_attribute_group = a.id_attribute_group)
              INNER JOIN attribute_group_lang as agl ON (agl.id_attribute_group = a.id_attribute_group && agl.id_lang = {$this->id_lang})
              INNER JOIN product_attribute_shop as ats ON (ats.id_product_attribute = pa.id_product_attribute && ats.id_shop = {$this->id_shop})
              INNER JOIN product_shop as ps ON (pa.id_product = ps.id_product && ps.active = 1 && ps.visibility != 'none' && ps.id_shop = {$this->id_shop}$query_id_product)
              ORDER BY pa.id_product ASC, ats.id_product_attribute ASC, ag.position ASC
LIMIT $offset,$one_loop_limit
              ;";
                // Poslední INNER JOIN tam je kvůli optimalizaci paměťi
                // INNER JOIN stock_available as sa ON (sa.id_product = ps.id_product && sa.id_product_attribute = ats.id_product_attribute)

                $rows = $this->QueryFA($sql, false, 'assoc');
                if ($rows) {
                    foreach ($rows as $row) {
                        // 1 = povolit objednávky
                        // 2 =  standardní
                        // 0 = zakazat objednavky

                        if (
                            (isset($this->stock[$row['id_product']][$row['id_product_attribute']]) && $this->stock[$row['id_product']][$row['id_product_attribute']] > 0)
                            || (STOCK_MANAGEMENT == 0)
                            || (OUT_OF_STOCK_ORDER == 1)
                            || ($this->out_of_stock[$row['id_product']] == 1)
                        ) {
                            $this->cache_variants[(int)$row['id_product']][] = array(
                                'reference' => $row['reference'],
                                'ean13' => $row['ean13'],
                                'price' => (float)$row['price'],
                                'id_product_attribute' => (int)$row['id_product_attribute'],
                                'weight' => (float)$row['weight'],
                                'available_date' => isset($row['available_date']) ? $row['available_date'] : '',
                                'id_attribute' => (int)$row['id_attribute'],
                                'value' => $row['value'],
                                'name' => $row['name'],
                            );
                        }
                    }

                    $offset++;
                    /*
                    echo PHP_EOL . count($rows) . PHP_EOL . $sql . PHP_EOL . PHP_EOL;
                    if ($offset == 3) {
                        die('Y');
                    }
                    */

                }


            } while (count($rows) == $one_loop_limit);


        }

        private
        function CacheSKVariants()
        {
            $this->cache_sk_variants = array();

            if (!SHAIM_COMBINATIONS) {
                return;
            }
            $sql = "SELECT value FROM configuration WHERE name = 'PS_COMBINATION_FEATURE_ACTIVE' && value = 1;";
            if (!$this->QueryR($sql)) {
                return;
            }


            // Nefunguje tak, jak jsme chteli... :-(
            $one_loop_limit = 500000;
            $offset = 0;

            $query_id_product = '';
            if ($this->debug_id_product) {
                $query_id_product = " && ps.id_product = {$this->debug_id_product}";
            }


            do {


                $sql = "SELECT DISTINCT
              pa.id_product, pa.ean13, pa.reference,
              ats.price, ats.id_product_attribute, ats.weight, ats.available_date,
              pac.id_attribute,
              al.name as value,
              agl.name as name
              FROM product_attribute as pa
              INNER JOIN product_attribute_combination as pac ON (pac.id_product_attribute = pa.id_product_attribute)
              INNER JOIN attribute_lang as al ON (al.id_attribute = pac.id_attribute && al.id_lang = {$this->id_lang_sk})
              INNER JOIN attribute as a ON (a.id_attribute = al.id_attribute)
              INNER JOIN attribute_group as ag ON (ag.id_attribute_group = a.id_attribute_group)
              INNER JOIN attribute_group_lang as agl ON (agl.id_attribute_group = a.id_attribute_group && agl.id_lang = {$this->id_lang_sk})
              INNER JOIN product_attribute_shop as ats ON (ats.id_product_attribute = pa.id_product_attribute && ats.id_shop = {$this->id_shop})
              INNER JOIN product_shop as ps ON (pa.id_product = ps.id_product && ps.active = 1 && ps.visibility != 'none' && ps.id_shop = {$this->id_shop}$query_id_product)
              ORDER BY pa.id_product ASC, ats.id_product_attribute ASC, ag.position ASC
LIMIT $offset,$one_loop_limit
              ;";
                // Poslední INNER JOIN tam je kvůli optimalizaci paměťi
                // INNER JOIN stock_available as sa ON (sa.id_product = ps.id_product && sa.id_product_attribute = ats.id_product_attribute)

                $rows = $this->QueryFA($sql, false, 'assoc');
                if ($rows) {
                    foreach ($rows as $row) {
                        // 1 = povolit objednávky
                        // 2 =  standardní
                        // 0 = zakazat objednavky

                        if (
                            (isset($this->stock[$row['id_product']][$row['id_product_attribute']]) && $this->stock[$row['id_product']][$row['id_product_attribute']] > 0)
                            || (STOCK_MANAGEMENT == 0)
                            || (OUT_OF_STOCK_ORDER == 1)
                            || ($this->out_of_stock[$row['id_product']] == 1)
                        ) {
                            $this->cache_sk_variants[(int)$row['id_product']][] = array(
                                'reference' => $row['reference'],
                                'ean13' => $row['ean13'],
                                'price' => (float)$row['price'],
                                'id_product_attribute' => (int)$row['id_product_attribute'],
                                'weight' => (float)$row['weight'],
                                'available_date' => isset($row['available_date']) ? $row['available_date'] : '',
                                'id_attribute' => (int)$row['id_attribute'],
                                'value' => $row['value'],
                                'name' => $row['name'],
                            );
                        }
                    }

                    $offset++;
                    /*
                    echo PHP_EOL . count($rows) . PHP_EOL . $sql . PHP_EOL . PHP_EOL;
                    if ($offset == 3) {
                        die('Y');
                    }
                    */

                }


            } while (count($rows) == $one_loop_limit);


        }


        // Tools.php (copied)
        private
        function str2url($str, $delimiter = '-')
        {

            $str = trim($str);

            if (function_exists('mb_strtolower'))
                $str = mb_strtolower($str, 'utf-8');
            if (!ALLOW_ACCENTED_CHARS)
                $str = $this->replaceAccentedChars($str);


            // Remove all non-whitelist chars.
            if (ALLOW_ACCENTED_CHARS)
                $str = preg_replace('/[^a-zA-Z0-9\s\'\:\/\[\]\-\pL]/u', '', $str);
            else
                $str = preg_replace('/[^a-zA-Z0-9\s\'\:\/\[\]\-]/', '', $str);

            $str = preg_replace('/[\s\'\:\/\[\]\-]+/', ' ', $str);
            $str = str_replace(array(' ', '/'), $delimiter, $str);

            // If it was not possible to lowercase the string with mb_strtolower, we do it after the transformations.
            // This way we lose fewer special chars.
            if (function_exists('mb_strtolower')) {
                $str = mb_strtolower($str, 'utf-8');
            } else {
                $str = strtolower($str);
            }

            return $str;
        }

        // Tools.php (copied)
        private
        function replaceAccentedChars($str)
        {
            /* One source among others:
                http://www.tachyonsoft.com/uc0000.htm
                http://www.tachyonsoft.com/uc0001.htm
                http://www.tachyonsoft.com/uc0004.htm
            */
            $patterns = array(

                /* Lowercase */
                /* a  */
                '/[\x{00E0}\x{00E1}\x{00E2}\x{00E3}\x{00E4}\x{00E5}\x{0101}\x{0103}\x{0105}\x{0430}\x{00C0}-\x{00C3}\x{1EA0}-\x{1EB7}]/u',
                /* b  */
                '/[\x{0431}]/u',
                /* c  */
                '/[\x{00E7}\x{0107}\x{0109}\x{010D}\x{0446}]/u',
                /* d  */
                '/[\x{010F}\x{0111}\x{0434}\x{0110}]/u',
                /* e  */
                '/[\x{00E8}\x{00E9}\x{00EA}\x{00EB}\x{0113}\x{0115}\x{0117}\x{0119}\x{011B}\x{0435}\x{044D}\x{00C8}-\x{00CA}\x{1EB8}-\x{1EC7}]/u',
                /* f  */
                '/[\x{0444}]/u',
                /* g  */
                '/[\x{011F}\x{0121}\x{0123}\x{0433}\x{0491}]/u',
                /* h  */
                '/[\x{0125}\x{0127}]/u',
                /* i  */
                '/[\x{00EC}\x{00ED}\x{00EE}\x{00EF}\x{0129}\x{012B}\x{012D}\x{012F}\x{0131}\x{0438}\x{0456}\x{00CC}\x{00CD}\x{1EC8}-\x{1ECB}\x{0128}]/u',
                /* j  */
                '/[\x{0135}\x{0439}]/u',
                /* k  */
                '/[\x{0137}\x{0138}\x{043A}]/u',
                /* l  */
                '/[\x{013A}\x{013C}\x{013E}\x{0140}\x{0142}\x{043B}]/u',
                /* m  */
                '/[\x{043C}]/u',
                /* n  */
                '/[\x{00F1}\x{0144}\x{0146}\x{0148}\x{0149}\x{014B}\x{043D}]/u',
                /* o  */
                '/[\x{00F2}\x{00F3}\x{00F4}\x{00F5}\x{00F6}\x{00F8}\x{014D}\x{014F}\x{0151}\x{043E}\x{00D2}-\x{00D5}\x{01A0}\x{01A1}\x{1ECC}-\x{1EE3}]/u',
                /* p  */
                '/[\x{043F}]/u',
                /* r  */
                '/[\x{0155}\x{0157}\x{0159}\x{0440}]/u',
                /* s  */
                '/[\x{015B}\x{015D}\x{015F}\x{0161}\x{0441}]/u',
                /* ss */
                '/[\x{00DF}]/u',
                /* t  */
                '/[\x{0163}\x{0165}\x{0167}\x{0442}]/u',
                /* u  */
                '/[\x{00F9}\x{00FA}\x{00FB}\x{00FC}\x{0169}\x{016B}\x{016D}\x{016F}\x{0171}\x{0173}\x{0443}\x{00D9}-\x{00DA}\x{0168}\x{01AF}\x{01B0}\x{1EE4}-\x{1EF1}]/u',
                /* v  */
                '/[\x{0432}]/u',
                /* w  */
                '/[\x{0175}]/u',
                /* y  */
                '/[\x{00FF}\x{0177}\x{00FD}\x{044B}\x{1EF2}-\x{1EF9}\x{00DD}]/u',
                /* z  */
                '/[\x{017A}\x{017C}\x{017E}\x{0437}]/u',
                /* ae */
                '/[\x{00E6}]/u',
                /* ch */
                '/[\x{0447}]/u',
                /* kh */
                '/[\x{0445}]/u',
                /* oe */
                '/[\x{0153}]/u',
                /* sh */
                '/[\x{0448}]/u',
                /* shh*/
                '/[\x{0449}]/u',
                /* ya */
                '/[\x{044F}]/u',
                /* ye */
                '/[\x{0454}]/u',
                /* yi */
                '/[\x{0457}]/u',
                /* yo */
                '/[\x{0451}]/u',
                /* yu */
                '/[\x{044E}]/u',
                /* zh */
                '/[\x{0436}]/u',

                /* Uppercase */
                /* A  */
                '/[\x{0100}\x{0102}\x{0104}\x{00C0}\x{00C1}\x{00C2}\x{00C3}\x{00C4}\x{00C5}\x{0410}]/u',
                /* B  */
                '/[\x{0411}]]/u',
                /* C  */
                '/[\x{00C7}\x{0106}\x{0108}\x{010A}\x{010C}\x{0426}]/u',
                /* D  */
                '/[\x{010E}\x{0110}\x{0414}]/u',
                /* E  */
                '/[\x{00C8}\x{00C9}\x{00CA}\x{00CB}\x{0112}\x{0114}\x{0116}\x{0118}\x{011A}\x{0415}\x{042D}]/u',
                /* F  */
                '/[\x{0424}]/u',
                /* G  */
                '/[\x{011C}\x{011E}\x{0120}\x{0122}\x{0413}\x{0490}]/u',
                /* H  */
                '/[\x{0124}\x{0126}]/u',
                /* I  */
                '/[\x{0128}\x{012A}\x{012C}\x{012E}\x{0130}\x{0418}\x{0406}]/u',
                /* J  */
                '/[\x{0134}\x{0419}]/u',
                /* K  */
                '/[\x{0136}\x{041A}]/u',
                /* L  */
                '/[\x{0139}\x{013B}\x{013D}\x{0139}\x{0141}\x{041B}]/u',
                /* M  */
                '/[\x{041C}]/u',
                /* N  */
                '/[\x{00D1}\x{0143}\x{0145}\x{0147}\x{014A}\x{041D}]/u',
                /* O  */
                '/[\x{00D3}\x{014C}\x{014E}\x{0150}\x{041E}]/u',
                /* P  */
                '/[\x{041F}]/u',
                /* R  */
                '/[\x{0154}\x{0156}\x{0158}\x{0420}]/u',
                /* S  */
                '/[\x{015A}\x{015C}\x{015E}\x{0160}\x{0421}]/u',
                /* T  */
                '/[\x{0162}\x{0164}\x{0166}\x{0422}]/u',
                /* U  */
                '/[\x{00D9}\x{00DA}\x{00DB}\x{00DC}\x{0168}\x{016A}\x{016C}\x{016E}\x{0170}\x{0172}\x{0423}]/u',
                /* V  */
                '/[\x{0412}]/u',
                /* W  */
                '/[\x{0174}]/u',
                /* Y  */
                '/[\x{0176}\x{042B}]/u',
                /* Z  */
                '/[\x{0179}\x{017B}\x{017D}\x{0417}]/u',
                /* AE */
                '/[\x{00C6}]/u',
                /* CH */
                '/[\x{0427}]/u',
                /* KH */
                '/[\x{0425}]/u',
                /* OE */
                '/[\x{0152}]/u',
                /* SH */
                '/[\x{0428}]/u',
                /* SHH*/
                '/[\x{0429}]/u',
                /* YA */
                '/[\x{042F}]/u',
                /* YE */
                '/[\x{0404}]/u',
                /* YI */
                '/[\x{0407}]/u',
                /* YO */
                '/[\x{0401}]/u',
                /* YU */
                '/[\x{042E}]/u',
                /* ZH */
                '/[\x{0416}]/u');

            // ö to oe
            // å to aa
            // ä to ae

            $replacements = array(
                'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 'ss', 't', 'u', 'v', 'w', 'y', 'z', 'ae', 'ch', 'kh', 'oe', 'sh', 'shh', 'ya', 'ye', 'yi', 'yo', 'yu', 'zh',
                'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'V', 'W', 'Y', 'Z', 'AE', 'CH', 'KH', 'OE', 'SH', 'SHH', 'YA', 'YE', 'YI', 'YO', 'YU', 'ZH'
            );

            return preg_replace($patterns, $replacements, $str);
        }


        public
        function __destruct()
        {


            if (SHAIM) {
                echo PHP_EOL . 'Počet produktů bez kombinací: ' . $this->count . PHP_EOL;
                $pocet_kombinaci = $this->variant_count;
                if ($this->count == $this->variant_count) {
                    $pocet_kombinaci = 0;
                }
                echo PHP_EOL . 'Počet kombinací: ' . $pocet_kombinaci . PHP_EOL;
                echo PHP_EOL . 'Počet přeskočených produktů (nejdou objednat, mají nulovou cenu, apod.): ' . $this->skipped . PHP_EOL;
            }
            $path = HTTPS . WEB . $this->physical_uri . 'xml/';


            // $glami_cz_gz = $glami_cz . '.gz';
            // $heureka_cz_gz = $heureka_cz . '.gz';
            // $heureka_gz_sk = $heureka_sk . '.gz';
            // $zbozi_gz = $zbozi . '.gz';
            // $google_gz = $google . '.gz';

            if (file_exists(XML_FOLDER . PROGRESS_FILE) && filesize(XML_FOLDER . PROGRESS_FILE) == 0) {
                unlink(XML_FOLDER . PROGRESS_FILE);
            }
            if ((isset($_GET['heureka_cz']) || isset($_GET['all']) && ACTIVE_HEUREKA_CZ == 1)) {
                $heureka_cz = $path . 'heureka_cz' . $this->force_lang_add . $this->add_id_shop . '.xml';
                echo PHP_EOL . "Heureka CZ XML: <a href='$heureka_cz' target='_blank'>$heureka_cz</a> (" . $this->human_filesize('heureka_cz' . $this->force_lang_add . $this->add_id_shop . '.xml') . ')';
            }
            if ((isset($_GET['heureka_sk']) || isset($_GET['all']) && ACTIVE_HEUREKA_SK == 1)) {
                $heureka_sk = $path . 'heureka_sk' . $this->force_lang_add . $this->add_id_shop . '.xml';
                echo PHP_EOL . "Heureka SK XML: <a href='$heureka_sk' target='_blank'>$heureka_sk</a> (" . $this->human_filesize('heureka_sk' . $this->force_lang_add . $this->add_id_shop . '.xml') . ')';
            }
            if ((isset($_GET['zbozi_cz']) || isset($_GET['all']) && ACTIVE_ZBOZI_CZ == 1)) {
                $zbozi = $path . 'zbozi_cz' . $this->force_lang_add . $this->add_id_shop . '.xml';
                echo PHP_EOL . "Zbozi XML: <a href='$zbozi' target='_blank'>$zbozi</a> (" . $this->human_filesize('zbozi_cz' . $this->force_lang_add . $this->add_id_shop . '.xml') . ')';
            }

            /*
            $this->force_lang_add = '';
            if ($this->force_lang) {
                $this->force_lang_add = '_' . $this->force_lang;
            }
            */

            if ((isset($_GET['google_com']) || isset($_GET['all']) && ACTIVE_GOOGLE_COM == 1)) {
                $google = $path . 'google_com' . $this->force_lang_add . $this->add_id_shop . '.xml';
                echo PHP_EOL . "Google XML: <a href='$google' target='_blank'>$google</a> (" . $this->human_filesize('google_com' . $this->force_lang_add . $this->add_id_shop . '.xml') . ')';
            }

            if ((isset($_GET['facebook_com']) || isset($_GET['all']) && ACTIVE_FACEBOOK_COM == 1)) {
                $facebook = $path . 'facebook_com' . $this->force_lang_add . $this->add_id_shop . '.xml';
                echo PHP_EOL . "Facebook XML: <a href='$facebook' target='_blank'>$facebook</a> (" . $this->human_filesize('facebook_com' . $this->force_lang_add . $this->add_id_shop . '.xml') . ')';
            }
            if ((isset($_GET['heureka_dostupnost']) || isset($_GET['all']) && ACTIVE_HEUREKA_DOSTUPNOST == 1)) {
                $heureka_doplnek = $path . 'heureka_dostupnos' . $this->force_lang_add . $this->add_id_shop . '.xml';
                echo PHP_EOL . "Heureka doplňek XML: <a href='$heureka_doplnek' target='_blank'>$heureka_doplnek</a> (" . $this->human_filesize('heureka_dostupnost' . $this->force_lang_add . $this->add_id_shop . '.xml') . ')' . PHP_EOL;
            }
            if (isset($_GET['glami'])) {
                $glami_cz = $path . 'glami_cz' . $this->force_lang_add . $this->add_id_shop . '.xml';
                echo PHP_EOL . "Glami CZ XML: <a href='$glami_cz' target='_blank'>$glami_cz</a> (" . $this->human_filesize('glami_cz' . $this->force_lang_add . $this->add_id_shop . '.xml') . ')';
                $glami_sk = $path . 'glami_sk' . $this->force_lang_add . $this->add_id_shop . '.xml';
                echo PHP_EOL . "Glami SK XML: <a href='$glami_sk' target='_blank'>$glami_sk</a> (" . $this->human_filesize('glami_sk' . $this->force_lang_add . $this->add_id_shop . '.xml') . ')';
            }

            if (isset($_GET['pricemania'])) {
                $pricemania_cz = $path . 'pricemania_cz' . $this->force_lang_add . $this->add_id_shop . '.xml';
                echo PHP_EOL . "Pricemania CZ XML: <a href='$pricemania_cz' target='_blank'>$pricemania_cz</a> (" . $this->human_filesize('pricemania_cz' . $this->force_lang_add . $this->add_id_shop . '.xml') . ')';
                $pricemania_sk = $path . 'pricemania_sk' . $this->force_lang_add . $this->add_id_shop . '.xml';
                echo PHP_EOL . "Pricemania SK XML: <a href='$pricemania_sk' target='_blank'>$pricemania_sk</a> (" . $this->human_filesize('pricemania_sk' . $this->force_lang_add . $this->add_id_shop . '.xml') . ')';
            }

            if (SHAIM) {
                $progress = $path . PROGRESS_FILE;
                echo PHP_EOL . "Progress soubor: <a href='$progress' target='_blank'>$progress</a> (" . $this->human_filesize(PROGRESS_FILE) . ')' . PHP_EOL . PHP_EOL;

                if (isset($_GET['all'])) {
                    echo 'ACTIVE_ZBOZI_CZ: ' . ACTIVE_ZBOZI_CZ . PHP_EOL;
                    echo 'ACTIVE_HEUREKA_CZ: ' . ACTIVE_HEUREKA_CZ . PHP_EOL;
                    echo 'ACTIVE_HEUREKA_SK: ' . ACTIVE_HEUREKA_SK . PHP_EOL;
                    echo 'ACTIVE_HEUREKA_DOSTUPNOST: ' . ACTIVE_HEUREKA_DOSTUPNOST . PHP_EOL;
                    echo 'ACTIVE_GOOGLE_COM: ' . ACTIVE_GOOGLE_COM . PHP_EOL;
                    echo 'ACTIVE_FACEBOOK_COM: ' . ACTIVE_FACEBOOK_COM . PHP_EOL;
                }
            }
            parent::__destruct();

            if (file_exists(LOCK_FILE)) {
                unlink(LOCK_FILE);
            }
        }

    }

}

$e = new Export();

if (isset($_GET['all'])) {
    $what_to_export = array();

    // $what_to_export[] = 'hledejceny_cz';
    // $what_to_export[] = 'shopalike_sk';

    if (ACTIVE_ZBOZI_CZ == 1) {
        $what_to_export[] = 'zbozi_cz';
    }
    if (ACTIVE_HEUREKA_CZ == 1) {
        $what_to_export[] = 'heureka_cz';
    }
    if (ACTIVE_HEUREKA_SK == 1) {
        $what_to_export[] = 'heureka_sk';
    }
    if (ACTIVE_HEUREKA_DOSTUPNOST == 1) {
        $what_to_export[] = 'heureka_dostupnost';
    }
    if (ACTIVE_GOOGLE_COM == 1) {
        $what_to_export[] = 'google_com';
    }
    if (ACTIVE_FACEBOOK_COM == 1) {
        $what_to_export[] = 'facebook_com';
    }

    if (empty($what_to_export)) {
        die('Nic není zaškrnuto, končím tedy export');
    }
    // $e->ExportAll(array('zbozi_cz', 'heureka_cz', 'heureka_sk', 'google_com', 'heureka_dostupnost', 'facebook_com'));
    $e->ExportAll($what_to_export);
} elseif (isset($_GET['heureka_cz'])) {
    $e->ExportAll(array('heureka_cz'));
} elseif (isset($_GET['glami'])) {
    $e->ExportAll(array('glami_cz', 'glami_sk'));
} elseif (isset($_GET['pricemania'])) {
    $e->ExportAll(array('pricemania_cz', 'pricemania_sk'));
} elseif (isset($_GET['heureka_sk'])) {
    $e->ExportAll(array('heureka_sk'));
} elseif (isset($_GET['zbozi_cz'])) {
    $e->ExportAll(array('zbozi_cz'));
} elseif (isset($_GET['google_com'])) {
    $e->ExportAll(array('google_com'));
} elseif (isset($_GET['facebook_com'])) {
    $e->ExportAll(array('facebook_com'));
} elseif (isset($_GET['heureka_dostupnost'])) {
    $e->ExportAll(array('heureka_dostupnost'));
} elseif (isset($_GET['open'])) {
    $e->FirstStep(array('zbozi_cz', 'heureka_cz', 'heureka_sk', 'google_com', 'heureka_dostupnost', 'facebook_com'));
} elseif (isset($_GET['close'])) {
    $e->ThirdStep(array('zbozi_cz', 'heureka_cz', 'heureka_sk', 'google_com', 'heureka_dostupnost', 'facebook_com'));
} else {
    echo 'fail';
}


exit;
/*
 * Author: Dominik "Shaim" Ulrich
 * E-mail: info@psmoduly.cz / info@openservis.cz
 * Websites: www.psmoduly.cz / www.openservis.cz
 *
 */
?>
































