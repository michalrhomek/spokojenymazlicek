<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    return;
}
require_once(dirname(__FILE__) . '/../../config/config.inc.php');
require_once(dirname(__FILE__) . '/../../init.php');

if (Tools::getValue('id_cart') > 0 && Tools::getIsset('checkbox')) {
    $sql = "UPDATE `" . _DB_PREFIX_ . "cart` SET `shaim_heureka_cz_overeno_checkbox` = " . (int)Tools::getValue('checkbox') . ", `shaim_heureka_cz_overeno_ip` = '" . Tools::getRemoteAddr() . "' WHERE `id_cart` = " . (int)Tools::getValue('id_cart') . ";";
    if (Db::getInstance()->Execute($sql)) {
        die('1');
    }
}
die('0');

?>

