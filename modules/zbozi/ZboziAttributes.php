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
 * @version    1.0
 * @link       http://www.prestahost.eu
 */
  class ZboziAttributes  {
      public static function getProductAttributes($id_product) {
       global $id_lang;
      
       
     if(zbozi::version_compare(_PS_VERSION_, '1.5.0', '<')) {
         $sql = 'SELECT pa.id_product_attribute, pa.id_product, null as available_date, pa.price,
                  pa.reference, pa.ean13,
          ag.`id_attribute_group`,  agl.`name` AS group_name,     al.`name` AS attribute_name,   agl.`public_name` AS group_pname, 
                    a.`id_attribute`,pa.quantity, ai.id_image 
                FROM `'._DB_PREFIX_.'product_attribute` pa
                
                LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
                LEFT JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute` = pac.`id_attribute`
                LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
                LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)$id_lang.')
                LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)$id_lang.')
                 
                LEFT JOIN '._DB_PREFIX_.'product_attribute_image ai ON ai.`id_product_attribute` = pa.`id_product_attribute` 
                WHERE pa.`id_product` = '.(int)$id_product.'
                GROUP BY pa.`id_product_attribute`, ag.`id_attribute_group`
                ORDER BY pa.`id_product_attribute` LIMIT 100';
     }
     else {  
         $id_shop = Context::getContext()->shop->id;
         $id_shop_group = Shop::getGroupFromShop($id_shop);
         $Group = new ShopGroup($id_shop_group);
         
           
        $sql = 'SELECT pa.id_product_attribute, pa.id_product,pa.available_date, pa.price,
                  pa.reference, pa.ean13,  pa.weight,
          ag.`id_attribute_group`,  agl.`name` AS group_name,     al.`name` AS attribute_name,   agl.`public_name` AS group_pname, 
                    a.`id_attribute`,s.quantity, ai.id_image 
                FROM `'._DB_PREFIX_.'product_attribute` pa
                '.Shop::addSqlAssociation('product_attribute', 'pa').'
                LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
                LEFT JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute` = pac.`id_attribute`
                LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
                LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)$id_lang.')
                LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)$id_lang.')
                LEFT JOIN '._DB_PREFIX_.'stock_available s on (pa.id_product=s.id_product AND pa.id_product_attribute=s.id_product_attribute AND ';
                
                $sql.= $Group->share_stock == 1?'s.id_shop_group='.$id_shop_group:'s.id_shop='.$id_shop;
                $sql.= ') LEFT JOIN '._DB_PREFIX_.'product_attribute_image ai ON ai.`id_product_attribute` = pa.`id_product_attribute` 
                WHERE pa.`id_product` = '.(int)$id_product.'
                GROUP BY pa.`id_product_attribute`, ag.`id_attribute_group`
                ORDER BY pa.`id_product_attribute` LIMIT 100';
     }
        $combinations = Db::getInstance()->executeS($sql);
	 
        $comb_array = array();
       
        if(is_array($combinations)) {
    
        $layered = false;
       	if (Module::isInstalled('blocklayered') && Module::isEnabled('blocklayered'))
		{
			$in ='';
			$carka ='';
			foreach($combinations as $combination) {
                if((int)$combination['id_attribute']){
				$in.= $carka.$combination['id_attribute'];
				$carka=',';
                }
			}
		$layered = array();
		if(strlen($in)) {
		$sql ='SELECT	id_attribute, url_name FROM '._DB_PREFIX_.'layered_indexable_attribute_lang_value  WHERE 
		       id_lang ='.(int)$id_lang.' AND id_attribute IN ('.$in.')';
		$res = Db::getInstance()->executeS($sql);
		if($res && is_array($res))
		  foreach($res as $atr)
		      $layered[$atr['id_attribute']] = $atr['url_name'];
		}
		      
		reset($combinations);
		} 

        foreach ($combinations as  $combination)
                {
                   // mod 2.77
                    $attribute_url = (is_array($layered) && isset($layered[$combination['id_attribute']]) && strlen($layered[$combination['id_attribute']]))?$layered[$combination['id_attribute']]:self::friendlyAttribute($combination['attribute_name']);
                   
                  
                    $comb_array[$combination['id_product_attribute']]['id_product_attribute'] = $combination['id_product_attribute'];
                  if(Configuration::get('ZBOZI_ATTR_PUBLIC')) {
                         $comb_array[$combination['id_product_attribute']]['attributes'][] = array($combination['group_pname'], $combination['attribute_name'], self::friendlyAttribute($combination['group_name']), $attribute_url, $combination['id_attribute'],  $combination['id_attribute_group']);
                    }
                    else {
                          $comb_array[$combination['id_product_attribute']]['attributes'][] = array($combination['group_name'], $combination['attribute_name'], self::friendlyAttribute($combination['group_name']), $attribute_url, $combination['id_attribute'],  $combination['id_attribute_group']);
                    }
                     
                    $comb_array[$combination['id_product_attribute']]['price'] = $combination['price'];
                    $comb_array[$combination['id_product_attribute']]['reference'] = $combination['reference'];
                    $comb_array[$combination['id_product_attribute']]['ean13'] = $combination['ean13'];
                    $comb_array[$combination['id_product_attribute']]['weight'] = $combination['weight'];
                //    $comb_array[$combination['id_product_attribute']]['id_image'] = isset($combination_images[$combination['id_product_attribute']][0]['id_image']) ? $combination_images[$combination['id_product_attribute']][0]['id_image'] : 0;
                    $comb_array[$combination['id_product_attribute']]['available_date'] = strftime($combination['available_date']);
                    $comb_array[$combination['id_product_attribute']]['quantity']=$combination['quantity'];
                    $comb_array[$combination['id_product_attribute']]['id_product']=$combination['id_product'];
                    $comb_array[$combination['id_product_attribute']]['id_image']=$combination['id_image'];
                   
                }    
         }  
        
        
        return $comb_array; 
     
 }
 
 
 
 private static function friendlyAttribute($val) {
 			$val = str_replace(Configuration::get('PS_ATTRIBUTE_ANCHOR_SEPARATOR'), '_', Tools::link_rewrite(str_replace(array(',', '.'), '-', $val))); 
 			return $val;
 }
 
 public static function getProductFeatures($id_product) {
     global $id_lang;
      $features=Product::getFrontFeaturesStatic($id_lang, $id_product);
      return $features;
 }
  }

 class cMap {
	
	 private $function;
     
	 
	 public function __construct($function) {
	 	$this->function=$function;	 
	 }
	 
	 /**
	 * get eshop category tree
	 * 
	 */
	 public function getTree($maxdepth, $id_lang) { 
	        $field=$this->function.'_category';
	         $resultIds = array();
			$resultParents = array();
            if(zbozi::version_compare(_PS_VERSION_, '1.5.0', '<')) 
            {  
			$sql='
			SELECT c.id_parent, c.id_category, cl.name, cl.description, cl.link_rewrite, cl.'.$field.' as mapped 
			FROM `'._DB_PREFIX_.'category` c
			INNER JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` AND cl.`id_lang` = '.$id_lang.')
			WHERE (c.`active` = 1)
			
			AND c.`id_category` != '.(int)Configuration::get('PS_ROOT_CATEGORY').'
			'.((int)$maxdepth != 0 ? ' AND `level_depth` <= '.(int)$maxdepth : '').'
			GROUP BY c.id_category
			ORDER BY `level_depth` ASC, '.(Configuration::get('BLOCK_CATEG_SORT') ? 'cl.`name`' : 'c.`position`').' '.(Configuration::get('BLOCK_CATEG_SORT_WAY') ? 'DESC' : 'ASC');
			}
            else {
            $sql='
            SELECT c.id_parent, c.id_category, cl.name, cl.description, cl.link_rewrite, cl.'.$field.' as mapped 
            FROM `'._DB_PREFIX_.'category` c
            INNER JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` AND cl.`id_lang` = '.$id_lang.')
            INNER JOIN `'._DB_PREFIX_.'category_shop` cs ON (cs.`id_category` = c.`id_category` AND cs.`id_shop` = '.(int)Shop::getContextShopID().')
            WHERE (c.`active` = 1)
            
            AND c.`id_category` != '.(int)Configuration::get('PS_ROOT_CATEGORY').'
            '.((int)$maxdepth != 0 ? ' AND `level_depth` <= '.(int)$maxdepth : '').'
            GROUP BY c.id_category
            ORDER BY `level_depth` ASC, '.(Configuration::get('BLOCK_CATEG_SORT') ? 'cl.`name`' : 'cs.`position`').' '.(Configuration::get('BLOCK_CATEG_SORT_WAY') ? 'DESC' : 'ASC');
            }
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
			foreach ($result as &$row)
			{
				$resultParents[$row['id_parent']][] = &$row;
				$resultIds[$row['id_category']] = &$row;
			}

			return $this-> getTreeRecursive($resultParents, $resultIds, $maxdepth, (isset($category) ? $category->id : null)); 
	
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
				if(Shop::isFeatureActive() && (int)Shop::getContextShopID())
				      $cache_path=dirname(__FILE__).'/cache/'.$this->function.'_'.(int)Shop::getContextShopID();
				else
				      $cache_path=dirname(__FILE__).'/cache/'.$this->function;
			}
			 
			return json_decode(file_get_contents($cache_path), true);
			} 
	 }
	 
	
	 
	private  function getTreeRecursive($resultParents, $resultIds, $maxDepth, $id_category = null, $currentDepth = 0)
	{
		if (is_null($id_category))
			$id_category =Context::getContext()->shop->getCategory();

		$children = array();
		if (isset($resultParents[$id_category]) && count($resultParents[$id_category]) && ($maxDepth == 0 || $currentDepth < $maxDepth))
			foreach ($resultParents[$id_category] as $subcat)
				$children[] =  $this->getTreeRecursive($resultParents, $resultIds, $maxDepth, $subcat['id_category'], $currentDepth + 1);

		if (!isset($resultIds[$id_category])) {
			$return = array(
			'id' => $id_category,
			'name' =>  '',
			'children' => $children,
			'level'=> $currentDepth,
			'mapped'=> '',
		);
			 
		}
        else {
		$return = array(
			'id' => $id_category,
			'name' =>  $resultIds[$id_category]['name'],
			'children' => $children,
			'level'=> $currentDepth,
			'mapped'=> $resultIds[$id_category]['mapped'],
		);
		}

		return $return;
	}
}
class GoogleCombinations {
	protected  $id_item_group=0;
	protected $cache=array();
	
	public function remap(&$combination, $googleAttributes, $id_item_group) {
		  if($id_item_group != $this->id_item_group) {
		     $this->cache = array();
		     $this->id_item_group=$id_item_group; 
		  }
		  $attributes=$this->renameAttributes($combination['attributes'], $googleAttributes);
		  if($attributes == false)
		     return false;
		     
		  $combination['attributes']= $attributes;
		  return   $combination;
		  
	}
	
	private function renameAttributes($attributes, $googleAttributes) {
   	   $retval='';
   	   $cacheLine='';
   	   foreach($attributes as $attribute) {
   	   	  if(isset($googleAttributes[$attribute[0]])) {
   	   	  		  $retval[]= array(0=>$googleAttributes[$attribute[0]], 1=>$attribute[1], 2=>$attribute[2], 3=>$attribute[3]);
   	   	  		  $cacheLine.=$googleAttributes[$attribute[0]].$attribute[1];
		  }
	   }
	
	   if(isset($this->cache[$cacheLine])) {
	      return false;
	   }
	      
	   $this->cache[$cacheLine] =1;
   	   return $retval;
	}
	

}


class FilterCombinations {
    protected  $id_item_group=0;
    protected $cache=array();
    
    public function remap(&$combination, $filterAttributes, $id_item_group) {
          if($id_item_group != $this->id_item_group) {
             $this->cache = array();
             $this->id_item_group=$id_item_group; 
          }
          $removed = array();
          $attributes=$this->filterAttributes($combination['attributes'], $filterAttributes, $removed);
          if($attributes == false)
             return false;
             
          $combination['attributes']= $attributes;
          $combination['removed'] = $removed;
          return   $combination;
          
    }
    
    private function filterAttributes($attributes, $filterAttributes, &$removed) {
          $retval='';
          $cacheLine='';
          foreach($attributes as $attribute) {
                if(in_array($attribute[5], $filterAttributes)) {
                          $retval[]= array(0=>$attribute[0], 1=>$attribute[1], 2=>$attribute[2], 3=>$attribute[3], 4=>$attribute[4], 5=>$attribute[5]);
                          $cacheLine.=$attribute[0].$attribute[1];
          }
          else {
            $removed[] = array(0=>$attribute[0], 1=>$attribute[1], 2=>$attribute[2], 3=>$attribute[3], 4=>$attribute[4], 5=>$attribute[5]);  
          }
       }
    
       if(isset($this->cache[$cacheLine])) {
         $removed = array();
          return false;
       }
          
       $this->cache[$cacheLine] =1;
          return $retval;
    }
    

}