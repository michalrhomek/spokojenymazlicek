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
 require_once("./classes/cFeed.php");
  class FeedGoogle extends cFeed {
  protected  $feedname='zbozi_google.xml';
  protected $googleAttributes;
  protected $GoogleCombinations;
  protected $zonemap=array();
  protected $cats_forbidden;
  protected $upname ='GOOGLE';
  protected $doprava;
  
  
  public function __construct() {
  	  // klice jsou skupiny z eshopu hodnoty z google
      parent::__construct();
      if(file_exists(dirname(__FILE__).'/ZboziAttributes.php') && zbozi::version_compare(_PS_VERSION_, '1.5', '>')) {
  	   $googleAttrib=json_decode(Configuration::get('ZBOZI_GATTRIBUTES'), true);
            if(is_array($googleAttrib) && count($googleAttrib)) {
            $this->googleAttributes = $this->expandAttributes($googleAttrib[Zbozi::getDefaultLang()]); 
			}
  	  
  	  
  	  $this->GoogleCombinations = new GoogleCombinations();
  	    }
 
   $sql='SELECT DISTINCT z.id_zone  FROM '._DB_PREFIX_.'carrier_zone z';
   $ret = Db::getInstance()->executeS($sql);
   
	$this->zonemap= $this->zonemap($ret);  
     
     
     
   if(Configuration::get("ZBOZI_DOPRAVAG_ON")) {  
     require_once(_PS_MODULE_DIR_."zbozi/classes/Doprava.php");
     $this->doprava = new Doprava(1, json_decode(Configuration::get("ZBOZI_CARRIERSG"), true), Configuration::get('PS_SHIPPING_FREE_PRICE'), Configuration::get('PS_SHIPPING_FREE_WEIGHT'), null, null, null);  
   }
     
     $cats=Configuration::get('ZBOZI_CATS_FORBIDDENgo');
     if($cats && strlen($cats ))
       $this->cats_forbidden = explode(',', $cats);
  }
  
 
  
  protected function StartFeed($fp) {
         if(zbozi::version_compare(_PS_VERSION_, '1.5.0', '<')) {
            
            $shop_url = Configuration::get("PS_SHOP_DOMAIN")?Configuration::get("PS_SHOP_DOMAIN"):$_SERVER['HTTP_HOST'];
         }
         else {
  	    $shop_url=ShopUrl::getMainShopDomain();
         }
        fputs($fp,  "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n
        <feed xmlns=\"http://www.w3.org/2005/Atom\" xmlns:g=\"http://base.google.com/ns/1.0\">\n
        <title>".$shop_url."</title>\n
        <link href=\"http://".$shop_url."/xml/google.xml\" rel=\"alternate\" type=\"text/html\" />
        <updated>".date('Y-m-d H:m:i')."</updated>\n
        <id>tag:".$shop_url.",".date('Y-m-d')."</id>\n");
  }     
   protected function CloseFeed($fp) {
     fputs($fp,  "</feed>");
   
 }   
     
protected function getItemGroup($product, $url, $cover, $all_images) {
     if($this->isInForbiddenCategory($product['id_category_default'])) {
      return;
      }
	 $itemgroup='';
     foreach($product['attributes'] as $combination) {
             if($this->jen_skladem &&   $combination['quantity'] <=0)
              continue;
            if((float)($product['price'] + $combination['price'] > 0)) {
            if($this->GoogleCombinations->remap($combination, $this->googleAttributes, $product['id_product']) !== false)
                 $itemgroup.=$this->createItemCombination($product, $combination, $url, $cover); 
			} 
        }
     return $itemgroup;
}

protected function createItem($product, $url, $imgurl) {
    if($this->isInForbiddenCategory($product['id_category_default'])) {
      return;
 }
	   $id_exist = 0;
       $item= "\t\t<entry>\n";
       
       $item.=$this->createTag('g:id', $this->unique_item_id($product['id_product'], 0));
        
       $item.=$this->createTag('title', $this->prepareString($product['name']));
       $item.=$this->createTag('description', $this->prepareString($this->getDescription($product)));
       $item.=$this->createTag('g:product_type', $this->prepareString($this->getCategoryText($product['categorytext_seznam'])));
          
       $item.=$this->createTag('link', $this->prepareString($url));  
     //  $item.=$this->createTag('updated', $this->prepareString($product['date_upd']));  
       $item.=$this->createTag('g:image_link', $this->prepareString($imgurl)); 
       $item.=$this->createTag('g:condition', $this->prepareString('new'));  
      
       if($this->isValidEan($product['ean13'])) {
        	      $item.=$this->createTag('g:gtin', $this->prepareString($product['ean13']));
        	      $id_exist++;
		}
        
        $reference = $this->getReference($product);
        if(strlen($reference)) {
             $item.=$this->createTag('g:mpn', $this->prepareString($reference));
             $id_exist++;
        }
		 
        
                
        if (isset($product['weight'])  && (float)($product['weight']) > 0) {
                  $item.=$this->createTag('g:shipping_weight', Tools::ps_round((float)$product['weight'], 2).' kg' );  
        }

		
		if(isset($product['categorytext_google']) && strlen($product['categorytext_google'])) {
			$item.=$this->createTag('g:google_product_category', $this->prepareString($product['categorytext_google'])); 
		}
	   $item.=$this->createTag('g:availability', $this->prepareString($this->getAvailability($product)));
	   global $CurrencyTo;
	   if(!is_null($CurrencyTo))  {
	      $sign=$CurrencyTo->iso_code;
	      $product['price']=Tools::convertPrice($product['price'],  $CurrencyTo); 
	   }
	   else
	      $sign = 'CZK';
	   
       $item.=$this->createTag('g:price', $this->prepareString($product['price']).' '.$sign);
       $item.=$this->createTag('g:brand', $this->prepareString($product['manufacturer_name'])); 
       $item.=$this->getDoprava($product);
       
       if(!empty($product['manufacturer_name']))
        $id_exist++;
        
       if($id_exist < 2   &&  ((int)Configuration::get('ZBOZI_GIDENF') ==1 )) {
       	     $item.=$this->createTag('g:identifier_exists', 'FALSE');
	   }
        
        $item.="\t\t</entry>\n";
     return $item;
}
      
protected function createItemCombination($product, $combination, $url, $imgurl) {
	   $id_exist = 0;
       $item= "\t\t<entry>\n";
        
        $item.=$this->createTag('g:id', $this->unique_item_id($product['id_product'], $combination['id_product_attribute']));
      
       $item.=$this->createTag('g:item_group_id', $product['id_product']);
      
       $item.=$this->createTag('title', $this->prepareString($product['name'].$this->getCombinationName($combination['attributes'])));
       $item.=$this->createTag('description', $this->prepareString($this->getDescription($product)));
       $item.=$this->createTag('g:product_type', $this->prepareString($this->getCategoryText($product['categorytext_seznam'])));
       $item.=$this->createTag('link', $this->prepareString($url));  
    //   $item.=$this->createTag('updated', $this->prepareString($product['date_upd']));  
       $item.=$this->createTag('g:condition', $this->prepareString('new'));  
	
      
      if($combination['id_image']) {
             $name=$this->toUrl($product['name']);
             global $link;
             $imgurl=$link->getImageLink($name, $product['id_product'].'-'.$combination['id_image'], $this->imagetype);  
             $item.=$this->createTag('g:image_link', $this->prepareString($imgurl));   
      }
      elseif($imgurl) {
            $item.=$this->createTag('g:image_link', $this->prepareString($imgurl));   
      }
      
     if(isset($this->cache[$product['id_product']][$combination['id_product_attribute']]) 
       &&  $this->cache[$product['id_product']][$combination['id_product_attribute']]['date_upd'] == $product['date_upd']
       &&  $this->cache[$product['id_product']][$combination['id_product_attribute']]['product_price'] == $product['price'] 
         &&  $this->cache[$product['id_product']][$combination['id_product_attribute']]['attribute_price'] == $combination['price'] 
     ) {
        $price=$this->cache[$product['id_product']][$combination['id_product_attribute']]['price'];  
     }
     else {
      $price=Product::getPriceStatic($product['id_product'], true, $combination['id_product_attribute'],2);
     if((float)$product['unit_price_ratio'] > 0 &&  $this->unitprice) {
                 $price = Tools::ps_round(($price /$product['unit_price_ratio']), 2);
     } 
          
        
        $this->cache[$product['id_product']][$combination['id_product_attribute']]['price']=$price;
        $this->cache[$product['id_product']][$combination['id_product_attribute']]['date_upd']=$product['date_upd']; 
        $this->cache[$product['id_product']][$combination['id_product_attribute']]['product_price']=$product['price']; 
        $this->cache[$product['id_product']][$combination['id_product_attribute']]['attribute_price']=$combination['price']; 
     } 
     global $CurrencyTo;
      if(! is_null($CurrencyTo)) {
         	$price=Tools::convertPrice($price,  $CurrencyTo); 
	  }
      $item.=$this->createTag('g:price', $this->prepareString($price).' CZK');
      $item.=$this->addAttributes($combination['attributes']);
	 
       if($this->isValidEan($combination['ean13'])){ 
         $item.=$this->createTag('g:gtin', $this->prepareString($combination['ean13']));
          $id_exist++;
	  }
      elseif($this->isValidEan($product['ean13'])){ 
         $item.=$this->createTag('g:gtin', $this->prepareString($product['ean13']));
          $id_exist++;
	  }
		
      $reference = $this->getReference($product, $combination);
      if($reference && strlen($reference)) {
           $item.=$this->createTag('g:mpn', $this->prepareString($reference));
           $id_exist++; 
      }
        
        if (isset($combination['weight'])  && (float)($combination['weight']) != 0) {
                  $item.=$this->createTag('g:shipping_weight', Tools::ps_round( (float)$product['weight'] + (float)$combination['weight'], 2).' kg' );  
        }
        elseif (isset($product['weight'])  && (float)($product['weight']) > 0) {
                  $item.=$this->createTag('g:shipping_weight', Tools::ps_round((float)$product['weight'], 2).' kg' );  
        }

		
		if(isset($product['categorytext_google'])) {
			$item.=$this->createTag('g:google_product_category', $this->prepareString($product['categorytext_google'])); 
		}
	   $item.=$this->createTag('g:availability', $this->prepareString($this->getAvailability($product)));
      
       $item.=$this->createTag('g:brand', $this->prepareString($product['manufacturer_name'])); 
       
       $item.=$this->getDoprava($product);
       if($id_exist < 2    &&  ((int)Configuration::get('ZBOZI_GIDENF') ==1 ) ) {
       	     $item.=$this->createTag('g:identifier_exists', 'FALSE');
	   }
        $item.="\t\t</entry>\n";
     return $item;   
   }   
         
  protected function getCategoryText($categorytext)  {
   return str_replace('|', '&gt;', $categorytext);
 } 
 
    

 protected function getAvailability($product) {
           if((int)$this->availability == 32)   // 32 je rezervovano pro heureka
              $this->availability = 31;
              
          // respektuji rizeni skladu
          if($this->availability_mode == 0 || empty($this->availability_mode)) {
               if($this->stock_management) {
                     if(isset($product["quantity"]) && $product["quantity"] > 0) {
                        return 'in stock';
                     }
                     else {
                     	   return 'out of stock';   
                     }
               }
                  else
                 return (int) $this->availability; 
             }
          // parsuje text 
          elseif($this->availability_mode==1) {
               if ( $this->parseAvailability($product['available_now'], 'available_now') > 2)   //todo
                     return 'out of stock';
            return 'in stock'; 
          }
          elseif($this->availability_mode==2) {    // respektuje jen vychozi hodoty
            if ((int) $this->availability > 0)
              return 'out of stock';
            return 'in stock'; 
          }
   }
   
   private function addAttributes($attributes) {
   	   $retval='';
   	   foreach($attributes as $attribute) {
   	   	  		  $retval.=$this->createTag('g:'.$attribute[0],  $attribute[1]);
	   }
   	   return $retval;
   }
   
 

 

protected function getDoprava($product) {
if(!Configuration::get("ZBOZI_DOPRAVAG_ON"))
  return '';
  
$retval = $this->doprava->getDopravaGoogle($product);  
    


   if(count($retval))
     return $this->compile_delivery($retval);  
   
   return '';
  

}

protected function compile_delivery($carriers) {
   $retval=''; 
  
   
    while(list($key,$carrier)=each($carriers)) {
    // name, price, id_zone, id_tax_rules_group
    if(isset($this->zonemap[$carrier[2]][$carrier[3]])) {
    $map =$this->zonemap[$carrier[2]][$carrier[3]];
	}
    elseif($carrier[3] == 0) {
		 $keys=array_keys($this->zonemap[$carrier[2]]);
		 $map =$this->zonemap[$carrier[2]][$keys[0]];
	}
	else
	  continue;
	  
    foreach ($map as $country) {
	    $item=$this->createTag('g:country',$country['iso_code']);
	    $item.=$this->createTag('g:service',$carrier[0]);
	    if($carrier[3] == 0)
	     $price=round($carrier[1],2);
	    else
	    $price=round($carrier[1]*(100+$country['rate'])/100,2);
	    $item.=$this->createTag('g:price',$price);
	    //$retval.=$this->createTag('g:shipping',"\n".$item);
	      $retval.= "\t\t\t<g:shipping>\n$item\t\t\t</g:shipping>\n";  
	}
    }
    
    return $retval;
    
}



protected function zonemap($rows) {
	$ret=array();
	foreach($rows as $row) {
		$sql='SELECT c.id_country, c.iso_code, r.id_tax_rules_group, r.id_tax, t.rate FROM 
		'._DB_PREFIX_.'country c LEFT JOIN 
		('._DB_PREFIX_.'tax_rule r LEFT JOIN '._DB_PREFIX_.'tax t on r.id_tax = t.id_tax )ON
		c.id_country = r.id_country
		WHERE c.active =1 AND c.id_zone='.(int)$row['id_zone'];
		$countries=Db::getInstance()->executeS($sql);
		foreach($countries as $country) {
			$ret[$row['id_zone']][$country['id_tax_rules_group']][]=$country;
		}
	
	}	
	return $ret;
}

 private function expandAttributes($attr) {
  	     $retval = array();
  	     while(list($key, $val) = each($attr)) {
  	        $pos = strpos( $key, '|');	
  	        if($pos === false) {
  	           $retval[trim($key)] = $val;
			}
			else {
			  $parts = explode('|', $key);
			  foreach($parts as $part)   {
			     if(strlen(trim($part)))
			       $retval[trim($part)] = $val;
			  }
			} 
		 }
		 return $retval;
  }
  
  protected function getCombinationName($attributes) {
      $q = array();
   while(list($key, $val) = each($attributes)) {
       if(isset($val[2])) 
          $val[0] = $val[2];
      $q[$key] = $val;
   }
     $ret = parent::getCombinationName($q);
     return $ret; 
  }

  }
?>
