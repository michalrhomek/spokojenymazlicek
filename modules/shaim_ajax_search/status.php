<?php /*** @license https://psmoduly.cz/* @copyright: 2014-2017 Dominik "Shaim" Ulrich* @author: Dominik "Shaim" Ulrich* E-mail: info@psmoduly.cz / info@openservis.cz* Websites: www.psmoduly.cz / www.openservis.cz* */ if (!in_array($_SERVER['REMOTE_ADDR'], array('212.80.69.87', '89.177.162.34', '37.157.193.173', '89.187.135.94', '89.187.135.166', '77.236.220.152', '90.183.26.11', '90.183.26.37', '90.183.26.13', '81.4.102.117',))) { die('OK'); } echo '<pre>'; $folder = dirname(__FILE__); require_once($folder . '/../../config/config.inc.php'); ini_set("display_errors", 1); error_reporting(E_ALL); $sql = "SELECT value FROM " . _DB_PREFIX_ . "configuration WHERE name = 'PS_SHOP_EMAIL';"; $shop_mail = Db::getInstance()->ExecuteS($sql); if (version_compare(_PS_VERSION_, '1.5', '>=')) { $sql = "SELECT domain FROM " . _DB_PREFIX_ . "shop_url WHERE active = 1;"; } else { $sql = "SELECT value as domain FROM " . _DB_PREFIX_ . "configuration WHERE name = 'PS_SHOP_NAME';"; } $shop_names = Db::getInstance()->ExecuteS($sql); $shops = array(); if ($shop_names) { foreach ($shop_names as $shop_name) { $shops[] = $shop_name['domain']; } } $e = explode('/', $folder); $this_module_name = end($e); echo 'Datum: ' . date("Y-m-d H:i:s") . PHP_EOL; echo 'PS verze: ' . _PS_VERSION_ . PHP_EOL; echo 'PS email: ' . $shop_mail[0]['value'] . PHP_EOL; echo 'Shopy: ' . implode(', ', $shops) . PHP_EOL . PHP_EOL; if (version_compare(_PS_VERSION_, '1.5', '>=')) { $sql = "SELECT id_module, active, name, version FROM " . _DB_PREFIX_ . "module WHERE name LIKE 'shaim_%';"; } else { $sql = "SELECT id_module, active, name FROM " . _DB_PREFIX_ . "module WHERE name LIKE 'shaim_%';"; } $modules = Db::getInstance()->ExecuteS($sql); $installed = false; $db_modules = array(); if ($modules) { foreach ($modules as $m) { if (version_compare(_PS_VERSION_, '1.5', '>=')) { $status = (int)Db::getInstance()->GetValue("SELECT COUNT(*) as pocet FROM " . _DB_PREFIX_ . "module_shop WHERE id_module = " . (int)$m['id_module'] . ";"); } else { $status = (int)$m['active']; } echo '[DB] - ' . $status . ' - ' . $m['name'] . ' - ' . ((isset($m['version']) && !empty($m['version'])) ? $m['version'] : 'Neznámá'); if ($this_module_name == $m['name']) { $installed = true; echo ' - (tento modul)'; } $db_modules[$m['name']] = $m['name']; echo PHP_EOL; } }/*if ($installed === false) {echo 'false' . ' - ' . $this_module_name . ' - (tento modul)' . PHP_EOL;}*/ $shaim_modules = glob(_PS_MODULE_DIR_ . 'shaim_*'); if ($shaim_modules) { foreach ($shaim_modules as $sm) { if (is_file($sm)) { continue; } $sm = str_replace(_PS_MODULE_DIR_, '', $sm); if (isset($db_modules[$sm])) { continue; } echo '[FTP] - false' . ' - ' . $sm; if ($this_module_name == $sm) { $installed = true; echo ' - (tento modul)'; } echo PHP_EOL; } } echo PHP_EOL; echo 'getcwd(): ' . getcwd() . PHP_EOL; echo 'display_errors: ' . (int)ini_get("display_errors") . PHP_EOL; echo 'error_reporting: ' . error_reporting() . PHP_EOL; echo 'memory_limit: ' . ini_get("memory_limit") . PHP_EOL; echo 'max_execution_time: ' . ini_get("max_execution_time") . 's' . PHP_EOL; echo 'curl_init: ' . (int)function_exists("curl_init") . PHP_EOL; echo 'allow_url_fopen: ' . (int)ini_get("allow_url_fopen") . PHP_EOL; echo 'max_input_vars: ' . (int)ini_get("max_input_vars") . PHP_EOL; echo 'php_version: ' . phpversion() . PHP_EOL; echo 'openssl: ' . extension_loaded('openssl') . PHP_EOL; echo 'mcrypt: ' . extension_loaded('mcrypt') . PHP_EOL; if (function_exists('disk_free_space') && function_exists('disk_total_space')) { echo disk_free_space(getcwd()) . '/' . disk_total_space(getcwd()) . PHP_EOL; } else { echo 'disk_free_space/disk_total_space: disabled' . PHP_EOL; } echo '</pre>';/*** @license https://psmoduly.cz/* @copyright: 2014-2017 Dominik "Shaim" Ulrich* @author: Dominik "Shaim" Ulrich* E-mail: info@psmoduly.cz / info@openservis.cz* Websites: www.psmoduly.cz / www.openservis.cz* */