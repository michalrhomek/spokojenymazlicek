<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    return;
}
require_once(dirname(__FILE__) . '/../../config/config.inc.php');
require_once(dirname(__FILE__) . '/../../init.php');
if (!Tools::getValue('q')) {
    return;
}

include_once(dirname(__FILE__) . '/shaim_ajax_search.php');

$module = new shaim_ajax_search();


$query = Tools::replaceAccentedChars(urldecode(Tools::getValue('q')));
$searchResults = Search::find((int)Context::getContext()->cookie->id_lang, $query, 1, (int)Configuration::get('shaim_ajax_search_count'), 'position', 'desc', true);
if (is_array($searchResults)) {
    $link = new Link();
    foreach ($searchResults as &$product) {
        $product['product_link'] = $link->getProductLink($product['id_product'], $product['prewrite'], $product['crewrite']);
        $product['price'] = false;
        if (Configuration::get('shaim_ajax_search_ceny')) {
            $product['price'] = Tools::displayPrice(Product::getPriceStatic($product['id_product'], true, null, 6, null, false, true, 1));
        }
        $product['image'] = false;
        if (Configuration::get('shaim_ajax_search_obrazky')) {
            $id_image = Product::getCover($product['id_product']);
            $id_image = $id_image['id_image'];
            if ($id_image > 0) {
                $product['image'] = $module->full_url . 'img/p/' . Image::getImgFolderStatic($id_image) . $id_image . '-small_default.jpg';
            }
        }
    }
}

die(Tools::jsonEncode($searchResults));


?>

