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
  class FeedExportHeureka extends cFeed {
protected $feedname;
protected $heureka_categories;
protected $zone;
protected $id_country;
protected $doprava;
protected $carriers;
protected $cods;
protected $transformed;
protected $free_shipping_price=0;
protected $free_shipping_weight=0;
private $ext_behav;
private $darky = false;
private $accessory = false;

 public function __construct() {
   
   parent::__construct();  
    $this->feedname=cFeed::addShopName().getFeedName($_GET['manufacturers']).'_heureka.xml';
   if(strlen($this->heureka_category))
      $this->heureka_categories=$this->getHeurekaCategories();
   if(Configuration::get('PS_LOCALE_COUNTRY') == 'sk') {
    $this->id_country=Db::getInstance()->getValue('SELECT id_country FROM '._DB_PREFIX_.'country WHERE iso_code="SK"');
   }
   else {
   	 $this->id_country=Db::getInstance()->getValue('SELECT id_country FROM '._DB_PREFIX_.'country WHERE iso_code="CZ"');   
   }
   $sql='SELECT id_zone FROM '._DB_PREFIX_.'country WHERE id_country='.$this->id_country;
   $this->zone= Db::getInstance()->getValue($sql);
   $this->doprava=Configuration::get("ZBOZI_DOPRAVA_ON");
   $this->carriers=json_decode(Configuration::get("ZBOZI_CARRIERS"), true);
   $this->cods=json_decode(Configuration::get('ZBOZI_CARRIERSCOD'), true);
   $this->transformed=json_decode(Configuration::get('ZBOZI_TRANSFORMED'), true);
   
     $this->free_shipping_price =Configuration::get('PS_SHIPPING_FREE_PRICE');
   
     $this->free_shipping_weight =Configuration::get('PS_SHIPPING_FREE_WEIGHT');
     $this->ext_behav = (int)Configuration::get('ZBOZI_TEXT_EXT');
     if(Configuration::get("ZBOZI_DAREK_HEUREKA")) {
        require_once(_PS_MODULE_DIR_.'/zbozi/classes/Darek.php');
        global $id_lang; 
        $Darek = new Darek($id_lang);
        $this->darky = $Darek->loadFromCache();
	 }
	  if(Configuration::get("ZBOZI_ACCESSORY_HEUREKA")) {
        require_once(_PS_MODULE_DIR_.'/zbozi/classes/Accessory.php');
        $Accessory = new Accessory();
        $this->accessory= $Accessory->loadFromCache();
	 }
 }
 
 protected function StartFeed($fp) {
    fputs($fp,  "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n");
    fputs($fp,  "<SHOP>\n"); 
  }         
      
 protected function CloseFeed($fp) {
      fputs($fp,  "</SHOP>");  
 }     
 protected function createItem($product, $url, $imgurl, $all_images) {
      $this->getUniqueId();  // retrocomp
      $item= "\t\t<SHOPITEM>\n";
      $item.=$this->createTag('ITEM_ID', $this->unique_item_id($product['id_product'], 0));
   
       $name  = $this->prepareString($product['name']);
       
       $item.=$this->createTag('PRODUCTNAME', $name);
       $item.=$this->createTag('PRODUCT', $name); 
      $item.=$this->createTag('REFERENCE', $product['reference']);
      $item.=$this->createTag('QUANTITY', $product['quantity']);
      
      $item.=$this->createTag('DESCRIPTION', $this->prepareString($this->getDescription($product)));
      if($product['condition'] == 'used') {
       	    $item.=$this->createTag('ITEM_TYPE', 'bazar');  
	   }
      $item.=$this->createTag('URL', $this->prepareString($url));  
      if($imgurl) {
            $item.=$this->createTag('IMGURL', $this->prepareString($imgurl));   
      }
      
      if(Configuration::get('ZBOZI_MULTIPLE_IMAGES') && $all_images)
           $item.=$this->additionalImages($all_images, $imgurl, 0 );
           
      global $CurrencyTo;
       $price=Product::getPriceStatic($product['id_product'], false); 
     $puvodni_cena = $product['price'];
       if(! is_null($CurrencyTo)) {
         	$product['price']=Tools::convertPrice($product['price'],  $CurrencyTo); 
            $puvodni_cena=Tools::convertPrice($puvodni_cena,  $CurrencyTo); 
		 }
	   if(Configuration::get("ZBOZI_ROUND_HEUREKA") && (is_null($CurrencyTo) || $CurrencyTo.iso_code == 'CZK'   )) {
	   	 $product['price'] =  Tools::ps_round($product['price'],0);
	   }
      $item.=$this->createTag('PRICE', $product['price']); 
      $item.=$this->createTag('PUVODNI_CENA', $puvodni_cena);                                                                                         
    
      $item.=$this->createTag('CATEGORYTEXT', $this->prepareString($product['categorytext']));  
      $item.=$this->getDoprava($product);
      
      $item.=$this->getDarek($product['id_product']);
      $item.=$this->getAccessory($product['id_product']);
    
      if(isset($product['features']) && is_array($product['features'])) {
             $item.=$this->addFeatures($product['features']);
      }
          
      $item.=$this->createTag('PRICE_VAT', $product['price']);
      $item.=$this->createTag('DELIVERY_DATE', $this->getAvailability($product)); 
         
      if($product['manufacturer_name'])
      $item.=$this->createTag('MANUFACTURER', $this->prepareString($product['manufacturer_name']));
      if($product['ean13'])
      $item.=$this->createTag('EAN', $this->prepareString($product['ean13']));
      
      if(isset($product['heureka_cpc']) && strlen($product['heureka_cpc'])) 
            $item .=$this->createTag('HEUREKA_CPC', $this->floatFromString($product['heureka_cpc']));
      else if($this->cpc && $product[$this->cpc]) { // defaultne manufacturer_reference
            $item .=$this->createTag('HEUREKA_CPC',  $this->floatFromString($product[$this->cpc]));  
      }
      
      $item.="\t\t</SHOPITEM>\n";
       
        return $item;
 }
 
 protected function addFeatures($features) {
         $retval='';
         foreach($features as $feature) {
              $retval.="\t\t\t<PARAM>\n\t\t\t\t<PARAM_NAME>{$this->prepareString($feature['name'])}</PARAM_NAME><VAL>{$this->prepareString($feature['value'])}</VAL>\n\t\t\t</PARAM>\n";
         }
         return $retval;
 }

protected function getItemGroup($product, $url, $cover, $all_images) {
 
    $itemgroup='';
     foreach($product['attributes'] as $combination) {
             if($this->jen_skladem &&   $combination['quantity'] <=0) {
              continue;
			 }
            if((float)($product['price'] + $combination['price'] > 0)) {
                 $itemgroup.=$this->createItemCombination($product, $combination, $url, $cover, $all_images);  
			}
			else {
				;
				//  echo '<br />'.$product['price'] .'  '. $combination['price'];
				//    echo '<br />';echo '<br />';
				//    var_dump($combination);
             // echo '<hr />';
			}
        }
     return $itemgroup;
}

protected function getDoprava($product) {
if(!$this->doprava)
  return '';
 $sql='
            SELECT c.*
            FROM `'._DB_PREFIX_.'product_carrier` pc
            INNER JOIN `'._DB_PREFIX_.'carrier` c
                ON (c.`id_reference` = pc.`id_carrier_reference` AND c.`deleted` = 0)
            WHERE pc.`id_product` = '.(int)$product['id_product'].'
                AND pc.`id_shop` = '.(int)Context::getContext()->shop->id;
 $result= Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
 if(!$result || !count($result)) {
     $result=  Carrier::getCarriers(Configuration::get('PS_LANG_DEFAULT'), true, false, (int)$this->zone, array(Configuration::get('PS_UNIDENTIFIED_GROUP')), Carrier::PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE);
 }
$product_price_tax=$product['price']*(100 + $product['rate'])/100; 
 if(is_array($result) && count($result)) {
     $retval=array();
  foreach ($result as $k => $row) {
        $heureka_carrier= $this->heurekaCarrierMap($row['id_carrier']);
        if($heureka_carrier === false)
          continue;
          
        $carrier=new Carrier($row['id_carrier']);
     
        $price=false;
        
            $shipping_method = $carrier->getShippingMethod();
            if ($shipping_method != Carrier::SHIPPING_METHOD_FREE)
            {
                // Get only carriers that are compliant with shipping method
                if (($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT && $carrier->getMaxDeliveryPriceByWeight($this->zone) === false)
                    || ($shipping_method == Carrier::SHIPPING_METHOD_PRICE && $carrier->getMaxDeliveryPriceByPrice($this->zone) === false))
                {
                    continue;
                }

                // If out-of-range behavior carrier is set on "Desactivate carrier"
                if ($row['range_behavior'])
                {
                
                $id_zone = $this->zone;

                    // Get only carriers that have a range compatible with cart
                    if (($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT
                        && (!Carrier::checkDeliveryPriceByWeight($row['id_carrier'], $product['weight'], $id_zone)))
                        || ($shipping_method == Carrier::SHIPPING_METHOD_PRICE
                        && (!Carrier::checkDeliveryPriceByPrice($row['id_carrier'], $product_price_tax, $id_zone, Context::getContext()->currency->id))))
                    {
                        continue;
                    }
                }
            }
            if($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT) {
                if($this->free_shipping_weight > 0 && $product['weight'] >  $this->free_shipping_weight)
                   $price=0;
                else
                  $price=$carrier->getDeliveryPriceByWeight($product['weight'], $this->zone);
                
            }
           elseif ($shipping_method == Carrier::SHIPPING_METHOD_PRICE) {
               if($this->free_shipping_price > 0 && $product_price_tax >  $this->free_shipping_price)
                   $price=0;
               else  {
                 $price=$carrier->getDeliveryPriceByPrice($product_price_tax, $this->zone);
               }
           }
        
     $price=(float)$price;
    
     
     global $CurrencyTo;
       if(! is_null($CurrencyTo)) {
         	$price=Tools::convertPrice($price,  $CurrencyTo); 
	   }
           $sql='SELECT t.rate FROM '._DB_PREFIX_.'tax_rule r LEFT JOIN  '._DB_PREFIX_.'tax t ON
           t.id_tax = r.id_tax WHERE r.id_tax_rules_group='.(int)$carrier->id_tax_rules_group.' AND
           r.id_country='.$this->id_country;
     $rate= Db::getInstance()->getValue($sql);
    
           if($rate)
             $price=round($price*(100+$rate)/100, 2);
           else
              $price=round($price, 2);
             
           if(isset($this->cods[$row['id_carrier']]))
             $retval[]=array($heureka_carrier, $price, $this->cods[$row['id_carrier']]);
          else 
             $retval[]=array($heureka_carrier, $price);
  
        
  
    }
   if(count($retval))
     return $this->compile_delivery($retval);  
   
   return '';
 }

} 

// array
protected function compile_delivery($carriers) {
    $output=array();
    
    foreach($carriers as $carrier) {
        if(!isset($output[$carrier[0]]))
           $output[$carrier[0]] = array(0=> $carrier[1], 1=>$carrier[2]);
        elseif($output[$carrier[0]] > $carrier[1])
           $output[$carrier[0]] = array(0=> $carrier[1], 1=>$carrier[2]);
    }
   $retval=''; 
    while(list($key,$arr)=each($output)) {
    	 $val=(float)$arr[0];
    	 $cod=(float) $arr[0] + (float) $arr[1];
        $retval.="\t\t\t<DELIVERY>\n";
        $retval.="\t\t\t\t<DELIVERY_ID>$key</DELIVERY_ID>\n\t\t\t\t<DELIVERY_PRICE>$val</DELIVERY_PRICE>\n\t\t\t\t<DELIVERY_PRICE_COD>$cod</DELIVERY_PRICE_COD>\n";
        $retval.="\t\t\t</DELIVERY>\n";
    }
    
    return $retval;
    
}


protected function heurekaCarrierMap($id_carrier) {
    if(!is_array($this->carriers) ||
       !count($this->carriers) || 
       !isset($this->carriers[$id_carrier]) ||
        empty($this->carriers[$id_carrier])
     )
      return false;
    
  
   
     return   $this->carriers[$id_carrier];
     
  
}

 protected function createItemCombination($product, $combination, $url, $imgurl, $all_images) {
      $item= "\t\t<SHOPITEM>\n";
         $this->getUniqueId(); // retrocomp
       $item.=$this->createTag('ITEM_ID', $this->unique_item_id($product['id_product'], $combination['id_product_attribute']));
       $item.=$this->createTag('ITEMGROUP_ID', $product['id_product']);    
       
       if(isset($combination['reference']) && strlen($combination['reference']))
          $reference=$combination['reference'];
       else
          $reference=$product['reference'];
          
      if(isset($this->cache[$product['id_product']][$combination['id_product_attribute']]) 
       &&  $this->cache[$product['id_product']][$combination['id_product_attribute']]['date_upd'] == $product['date_upd']
       &&  $this->cache[$product['id_product']][$combination['id_product_attribute']]['product_price'] == $product['price'] 
         &&  $this->cache[$product['id_product']][$combination['id_product_attribute']]['attribute_price'] == $combination['price'] 
     ) {
        $price=$this->cache[$product['id_product']][$combination['id_product_attribute']]['price'];  
     }
     else {
      $price=Product::getPriceStatic($product['id_product'], true, $combination['id_product_attribute'],2, null, false, true, 1, false, null, null, null, $specific_price);
       $product['specific_price'] = $specific_price;
        $this->cache[$product['id_product']][$combination['id_product_attribute']]['price']=$price;
        $this->cache[$product['id_product']][$combination['id_product_attribute']]['date_upd']=$product['date_upd']; 
        $this->cache[$product['id_product']][$combination['id_product_attribute']]['product_price']=$product['price']; 
        $this->cache[$product['id_product']][$combination['id_product_attribute']]['attribute_price']=$combination['price']; 
     } 
          
       
        $name = $this->prepareString($product['name'].$this->getCombinationName($combination['attributes']));
       
      
       $item.=$this->createTag('PRODUCTNAME', $name);
       $item.=$this->createTag('PRODUCT', $name);
      
      $item.=$this->createTag('REFERENCE', $reference);
      $item.=$this->createTag('QUANTITY', $combination['quantity']);
       
      $item.=$this->createTag('DESCRIPTION', $this->prepareString($this->getDescription($product)));
      if($product['condition'] == 'used') {
       	    $item.=$this->createTag('ITEM_TYPE', 'bazar');  
	   }
      $url.='#'.$this->getCombinationUrl($combination['attributes']);
      $item.=$this->createTag('URL', $this->prepareString($url));  
      $imgurlc = '';
      if($combination['id_image']) {
             $imgurlc = $this->combinationCoverUrl($all_images, $combination['id_product_attribute']);
             $item.=$this->createTag('IMGURL', $this->prepareString($imgurlc)); 
             
            $item.= $this->additionalImages($all_images, $imgurlc, $combination['id_product_attribute']);
      }
      
     if($imgurlc == '' && $imgurl) {
            $item.=$this->createTag('IMGURL', $this->prepareString($imgurl));   
      }
      
     $price=Product::getPriceStatic($product['id_product'], false, $combination['id_product_attribute'],2); 
     $puvodni_cena = $product['price'];
      global $CurrencyTo;
      if(! is_null($CurrencyTo)) {
         	$price=Tools::convertPrice($price,  $CurrencyTo);
            $puvodni_cena=Tools::convertPrice($price,  $CurrencyTo); 
		 }
     if(Configuration::get("ZBOZI_ROUND_HEUREKA") && (is_null($CurrencyTo) || $CurrencyTo.iso_code == 'CZK'   )) {
	   	 $price=  Tools::ps_round($price,0);
	   }
      $item.=$this->createTag('PRICE', $price); 
      $item.=$this->createTag('PUVODNI_CENA', $puvodni_cena); 
      
   
      $item.=$this->createTag('CATEGORYTEXT', $this->prepareString($product['categorytext']));  
      $item.=$this->getDoprava($product);
      $item.=$this->getDarek($product['id_product']);
      $item.=$this->getAccessory($product['id_product']);
      
      $features=$this->featuresFromCombination($combination, $product['features']);
      if(is_array($features) && count($features)) {
             $item.=$this->addFeatures($features);
      }
     
      $item.=$this->createTag('DELIVERY_DATE', $this->getAvailability($this->mergeAvailability($combination, $product))); 
         
      if($product['manufacturer_name'])
      $item.=$this->createTag('MANUFACTURER', $this->prepareString($product['manufacturer_name']));
      if($combination['ean13'])
         $item.=$this->createTag('EAN', $this->prepareString($combination['ean13']));
      elseif($product['ean13'])
         $item.=$this->createTag('EAN', $this->prepareString($product['ean13']));
      
      if(isset($product['heureka_cpc']) && strlen($product['heureka_cpc'])) 
            $item .=$this->createTag('HEUREKA_CPC', $this->floatFromString($product['heureka_cpc']));
      else if($this->cpc && $product[$this->cpc])  // defaultne manufacturer_reference
            $item .=$this->createTag('HEUREKA_CPC', $this->floatFromString($product[$this->cpc]) );
            
      $item.="\t\t</SHOPITEM>\n";
        
        return $item;  
       
   }   

   
  protected function featuresFromCombination($combination, $features=null) {
      if(!count($this->transformed))
         return $features;
         
       $transformed=$this->transformed;
       $retval=array();
       $keys=array();
       foreach($transformed as $t) {
           
           foreach($combination['attributes'] as $at) {
              if($t[0] == $at[0])  {
                $keys[]=  $t[0];
                $retval[]=array('name'=>$t[1], 'value'=>$at[1]);
              }
               }
       }
  if(isset($features) && is_array($features) && count($features) && count($keys)) {
      while(list($key,$val)=each($features)) {
        if(! in_array($val['name'], $keys))
          $retval[]=array('name'=>$val['name'], 'value'=>$val['value']);   
      }
  }
  return $retval;  
 }
 
 protected function getUniqueId() {
   global $uniqueId;
   return ++$uniqueId;   
 }
 
 protected function getCategoryText($categorytext)  {
 //echo  $categorytext;  
  return $this->prepareString($categorytext);
 }         
 private function unique_item_id  ($x, $y) {
 	if((int)$y == 0) {
		return $x;
	}
	return $x.'-'.$y; 
 }
 
 private function cantor($x, $y)
{
    // ((x + y) * (x + y + 1)) / 2 + y;
    if(function_exists('bcadd'))
    return bcadd(bcdiv(bcmul(bcadd($x, $y), bcadd(bcadd($x, $y), 1)), 2), $y);
    
    return (($x + $y) * ($x + $y + 1)) / 2 + $y;
}
 
     protected function getHeurekaCategories() {
        $retval=array();
    //   $xml=simplexml_load_file("http://www.heureka.cz/direct/xml-export/shops/heureka-sekce.xml");
     $s= file_get_contents("http://www.heureka.cz/direct/xml-export/shops/heureka-sekce.xml");  
     $arr=explode("<CATEGORY_ID>", $s);
     foreach($arr as $chunk) {
         if((int)$chunk > 0) {
               $chunk=str_replace('</CATEGORY>', '' , $chunk);
               $chunk=str_replace('<CATEGORY>', '' , $chunk);
               $chunk=str_replace('</HEUREKA>', '' , $chunk);
               $xml=simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><CHUNK><CATEGORY_ID>".$chunk.'</CHUNK>');
               if($xml && strlen((string)$xml->CATEGORY_FULLNAME) > 6   && (int)$xml->CATEGORY_ID ) {
                 $retval[ (int)$xml->CATEGORY_ID] =(string)$xml->CATEGORY_FULLNAME; 
               }
         }
     }
     return $retval;
  }
  
  private function  additionalImages($all_images, $cover_url, $id_product_attribute = 0) {
  	  $retval ='';
     foreach($all_images as $imgurl) {
        if($imgurl['id_product_attribute'] == $id_product_attribute)
          if($cover_url != $imgurl['url'])
             $retval.=$this->createTag('IMGURL_ALTERNATIVE', $this->prepareString($imgurl['url'])); 
	 }
     return $retval;  
  }
  
  private function getDarek($id_product) {
  	  $retval ='';
  	   if(!Configuration::get("ZBOZI_DAREK_HEUREKA")) 
  	     return $retval;
  	                                           
  	   if(!is_array($this->darky))
  	     return $retval;
  	     
  	   if(isset($this->darky[$id_product])) {
  	   	  foreach($this->darky[$id_product] as $darek)  {
  	   	  	 $retval.=$this->createTag('GIFT', $darek); 
		  }	   
	   }
	   
	   return $retval;
  }
  
    private function getAccessory($id_product) {
  	  $retval ='';
  	   if(!Configuration::get("ZBOZI_ACCESSORY_HEUREKA")) 
  	     return $retval;
  	                                           
  	   if(!is_array($this->accessory))
  	     return $retval;
  	     
  	   if(isset($this->accessory[$id_product])) {
  	   	  foreach($this->accessory[$id_product] as $accessory)  {
  	   	  	 $retval.=$this->createTag('ACCESSORY', $accessory); 
		  }	   
	   }
	   
	   return $retval;
  }
 
  }
?>
