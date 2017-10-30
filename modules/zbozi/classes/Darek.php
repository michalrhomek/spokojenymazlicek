<?php
class Darek {
	
	protected $id_lang;
	
	public function __construct($id_lang) {
		 $this->id_lang = $id_lang;
	}
	
	public function resetCache() {
		 require_once(_PS_MODULE_DIR_.'/zbozi/zbozi.php');
		 $path = Zbozi::getCachePath('darek', $this->id_lang);
		 if(file_exists($path))
		   unlink($path);
		 $content = $this->loadDarek();
		 $content = json_encode($content);
		 file_put_contents($path, $content);
	}
	
	public function loadFromCache() {
		 require_once(_PS_MODULE_DIR_.'/zbozi/zbozi.php');
		 $path = Zbozi::getCachePath('darek', $this->id_lang);
		  if(file_exists($path)) {
		  	  $content = file_get_contents($path);
		    	return  json_decode($content, true);
		  	   
		  	  
		  }
		  return array();
	}
	
	
	protected function loadDarek() {
		$retval = array();
		
		if( Shop::isFeatureActive() && (int)Shop::getContextShopID(true))
		       $sql ='SELECT crpcrv.id_product_rule, gift_product, gift_product_attribute, name  FROM
'._DB_PREFIX_.'cart_rule_product_rule_group crprg 
 LEFT JOIN 
('._DB_PREFIX_.'cart_rule_product_rule crpr LEFT JOIN '._DB_PREFIX_.'cart_rule_product_rule_value crpcrv ON crpr.id_product_rule =
crpcrv. id_product_rule
) ON crprg.id_product_rule_group = crprg.id_product_rule_group 
LEFT JOIN 
( '._DB_PREFIX_.'cart_rule cr  LEFT JOIN '._DB_PREFIX_.'product_lang pl on cr.gift_product = pl.id_product 
LEFT JOIN '._DB_PREFIX_.'cart_rule_shop crs ON cr.id_cart_rule = crs.id_cart_rule AND crs.id_shop ='.
 (int)Shop::getContextShopID(true).'
)
on  crprg.id_cart_rule = cr.id_cart_rule
 where  crpr.type ="products" AND pl.id_lang ='.(int)$this->id_lang.' GROUP BY cr.id_cart_rule';
		else
		    $sql ='SELECT crpcrv.id_product_rule, gift_product, gift_product_attribute, name  FROM
'._DB_PREFIX_.'cart_rule_product_rule_group crprg 
 LEFT JOIN 
('._DB_PREFIX_.'cart_rule_product_rule crpr LEFT JOIN '._DB_PREFIX_.'cart_rule_product_rule_value crpcrv ON crpr.id_product_rule =
crpcrv. id_product_rule
) ON crprg.id_product_rule_group = crprg.id_product_rule_group 
LEFT JOIN 
( '._DB_PREFIX_.'cart_rule cr  LEFT JOIN '._DB_PREFIX_.'product_lang pl on cr.gift_product = pl.id_product )
on  crprg.id_cart_rule = cr.id_cart_rule
 where  crpr.type ="products" AND pl.id_lang ='.(int)$this->id_lang.' GROUP BY cr.id_cart_rule';
		
		 $rules = Db::getInstance()->executeS($sql);
		 foreach($rules as $rule) {
		 	  $sql ='SELECT id_item FROM '._DB_PREFIX_.'cart_rule_product_rule_value WHERE
		 	  id_product_rule ='.(int)$rule['id_product_rule'];
		 	  $items = Db::getInstance()->executeS($sql);
		 	  
		 	  foreach($items as $item) {
		 	  	   $retval[$item['id_item']][] =   $rule['name'];
			  }
		 	 
		 }
			return $retval;
	}

	
}