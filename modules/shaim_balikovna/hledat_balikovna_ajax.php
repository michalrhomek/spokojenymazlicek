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
include_once(dirname(__FILE__) . '/shaim_balikovna.php');

$module = new shaim_balikovna();

$id_cart = (int)Tools::getValue('id_cart');


if (Tools::getIsset('already_selected')) {
    $sql = "SELECT `psc` FROM `" . _DB_PREFIX_ . "shaim_balikovna_data` WHERE `id_cart` = " . $id_cart . ";";
    $already_selected = Db::getInstance()->ExecuteS($sql);
    if (isset($already_selected[0]['psc']) && !empty($already_selected[0]['psc'])) {
        die($already_selected[0]['psc']);
    } else {
        die('0');
    }
}


if (Tools::getIsset('submit_message')) {
    $sql = "INSERT INTO `" . _DB_PREFIX_ . "shaim_balikovna_data` SET `id_cart` = " . $id_cart . ", `psc` = '" . Tools::getValue('psc') . "' ON DUPLICATE KEY UPDATE `psc` = '" . Tools::getValue('psc') . "';";
    Db::getInstance()->Execute($sql);

    die('1');
}
if (Tools::getIsset('delete_message')) {

    $sql = "DELETE FROM `" . _DB_PREFIX_ . "shaim_balikovna_data` WHERE `id_cart` = " . $id_cart . ";";
    Db::getInstance()->Execute($sql);

    die('1');
}


$naz_prov = pSQL(Tools::getValue('naz_prov'));

// Nejdrive checkujeme ID jestli nahodou tam neni (pres IsAlreadySelected)
$sql = "SELECT  `naz_prov`, `adresa`, `psc` FROM `" . _DB_PREFIX_ . "shaim_balikovna` WHERE `psc` = '$naz_prov' && `exists` = 1;";
$baliky = DB::GetInstance()->ExecuteS($sql);
if (!$baliky) {
    $where = '(`naz_prov` LIKE "%' . $naz_prov . '%" || `adresa` LIKE "%' . $naz_prov . '%") && `exists` = 1';
    $sql = "SELECT `naz_prov`, `adresa`, `psc` FROM `" . _DB_PREFIX_ . "shaim_balikovna` WHERE $where ORDER BY `naz_prov` ASC;";
    $baliky = DB::GetInstance()->ExecuteS($sql);
}

if ($baliky) {
    $html = '<table id="vypis_balikovna">';
    $html .= '<tr class="balikovna_tr_line"><td class="vypis_balikovna_td_nadpis">' . pSQL(Tools::getValue('text_balikovna')) . '</td><td class="vypis_balikovna_td_nadpis">' . pSQL(Tools::getValue('text_adresa')) . '</td><td class="vypis_balikovna_td_nadpis">' . pSQL(Tools::getValue('text_zvolit_balikovna')) . '</td>';
    foreach ($baliky as $b) {
        $html .= '<tr class="balikovna_tr_line"><td class="vypis_balikovna_td">' . $b['naz_prov'] . '</td><td class="vypis_balikovna_td">' . $b['adresa'] . '</td><td class="vypis_balikovna_td"><input type="button" class="vybrat_balikovna button btn" value="' . pSQL(Tools::getValue('text_vybrat_balikovna')) . '" data-psc="' . $b['psc'] . '" data-full="' . str_replace("'", "", str_replace("'", "", str_replace('"', '', $b['naz_prov']))) . ', ' . $b['adresa'] . '"></td>
        ';
    }
    $html .= '</table>';
    die($html);
} else {
    die('0');
}

?>

