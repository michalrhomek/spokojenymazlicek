<?php
/**
 * Modul Zboží: Srovnávače zboží - export xml pro Prestashop
 *
 * PHP version 5
 *
 * LICENSE: The buyer can free use/edit/modify this software in anyway
 * The buyer is NOT allowed to redistribute this module in anyway or resell it 
 * or redistribute it to third party
 *
 * @package    zbozi
 * @author    Vaclav Mach <info@prestahost.cz>
 * @copyright 2014,2015 Vaclav Mach
 * @license   EULA
 * @version    2.9.3
 * @link       http://www.prestahost.eu
 */
/**
* used in free module version only
*/
  class cMap {
	
	 private $function;
	 
	 public function __construct($function) {
	 	$this->function=$function;	 
	 }
	 
	
	 /**
	 * builds heureka tree from stored map
	 * 
	 */
		public function buildTaxonomyTree($state, $id_lang) {
	        $field=$this->function.'_category';
			
			$sql='SHOW COLUMNS FROM  '._DB_PREFIX_.'category_lang LIKE "'.$field.'"';
			$test= Db::getInstance()->executeS($sql);
			if($test && is_array($test) ) {
		
			if($state == 'start') {   
				$retval=array();
				 
			if(Shop::isFeatureActive() && (int)Shop::getContextShopID( )){
			    $cache_path=dirname(__FILE__).'/cache/'.$this->function.'_'.(int)Shop::getContextShopID();
				$sql='SELECT cl.'.$field.',   c.`id_category`
				FROM 
				`'._DB_PREFIX_.'category` c 
				LEFT JOIN `'._DB_PREFIX_.'category_shop` cs
				ON c.id_category =  cs.id_category
				LEFT JOIN   `'._DB_PREFIX_.'category_lang` cl
				ON c.id_category=cl.id_category AND cl.id_lang='.(int)$id_lang.'
				WHERE cs.id_shop='.(int)Shop::getContextShopID().' 
				GROUP BY  c.`id_category`
				'; 
				}
			else {
				$cache_path=dirname(__FILE__).'/cache/'.$this->function;
				$sql='SELECT cl.'.$field.',   c.`id_category`
				FROM 
				`'._DB_PREFIX_.'category` c 
				 
				LEFT JOIN   `'._DB_PREFIX_.'category_lang` cl
				ON c.id_category=cl.id_category AND cl.id_lang='.(int)$id_lang.'
				GROUP BY  c.`id_category`
				'; 
				}
				$ct= Db::getInstance()->ExecuteS($sql);

				foreach($ct as $cat) {
				$retval[$cat['id_category']]= $cat[$field]; 
				} 
				file_put_contents($cache_path, json_encode($retval)); 
			}
			else {
				if(Shop::isFeatureActive() && (int)Shop::getContextShopID(true) === null)
				      $cache_path=dirname(__FILE__).'/cache/'.$this->function.'_'.(int)Shop::getContextShopID();
				else
				      $cache_path=dirname(__FILE__).'/cache/'.$this->function;
			}
			 
			return json_decode(file_get_contents($cache_path), true);
			} 
	 }
	 
	
	 
	
}
?>
