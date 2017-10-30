<?php
  class Shop {
  	const SHARE_STOCK = 1; 
     const CONTEXT_ALL = 4;
      protected static $context_id_shop;

    /** @var int ID shop group in the current context (will be empty if context is CONTEXT_ALL) */
    protected static $context_id_shop_group;
  	public function  isFeatureActive() {
  		return false;
	}
    
    public static function getContextShopID() {
       return 1;
	} 
	
	public static function getGroupFromShop() {
		 return 1;
	}
  	public static function getSharedShops($id_shop, $stock) {
  	    return array();
	}
	
	public static function addSqlRestrictionOnLang($alias = null, $id_shop = null)
	{
		 

		return '  ';
	}
    
    public static function getBaseUri() {
       return __PS_BASE_URI__;  
    }
    
    public function getCategory() {
      $retval = Db::getInstance()->getValue('SELECT id_category FROM '._DB_PREFIX_.'category WHERE id_parent = 0');
      return $retval;
    }
    
    public static function setContext($type, $id = null)
    {
         
             
                self::$context_id_shop = null;
                self::$context_id_shop_group = null;
            

         
    }
    
    public static function getCurrentShop() {
        return 1;
    }  
    
  }
?>
