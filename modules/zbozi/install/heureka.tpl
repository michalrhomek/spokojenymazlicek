{*
 * Modul Zboží: Srovnávače zboží - export xml pro Prestashop
 *
 * PHP version 5
 *
 * LICENSE: The buyer can free use/edit/modify this software in anyway
 * The buyer is NOT allowed to redistribute this module in anyway or resell it 
 * or redistribute it to third party
 *
 * @package    orderpreview
 * @author    Vaclav Mach <info@prestahost.cz>
 * @copyright 2014 Vaclav Mach
 * @license   EULA
 * @version    1.0
 * @link       http://www.prestahost.eu
 *}
<div class="separation"></div><span>{l s='Heureka kategorie'}:</span> <input type="text" size="40" name="heureka_category" value="{if isset($product->heureka_category)}{$product->heureka_category}{/if}" /><small><a href='http://www.heureka.cz/direct/xml-export/shops/heureka-sekce.xml' target='_blank'>CATEGORY_FULLNAME</a></small>