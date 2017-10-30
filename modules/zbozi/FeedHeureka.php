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
 require_once(_PS_MODULE_DIR_."zbozi/classes/FeedLocal.php");
  class FeedHeureka extends FeedLocal {
protected  $feedname='zbozi_heureka.xml';
protected $heureka_categories;
protected $doprava;
protected $transformed;
protected $ext_behav;
protected $darky = false;
protected $accessory = false;
protected $taxmap = false;
protected $filteredAttributes = false;
protected $FilterCombinations;
protected $upname ='HEUREKA';

 public function __construct() {
   
   parent::__construct();  
   if(strlen($this->heureka_category))
      $this->heureka_categories=$this->getHeurekaCategories();
 
  
   $this->doprava=Configuration::get("ZBOZI_DOPRAVA_ON");
   if($this->doprava) {
         require_once(_PS_MODULE_DIR_."zbozi/classes/Doprava.php");
            if(Configuration::get('PS_LOCALE_COUNTRY') == 'sk') {
            $id_country=Db::getInstance()->getValue('SELECT id_country FROM '._DB_PREFIX_.'country WHERE iso_code="SK"');
            }
            else {
            $id_country=Db::getInstance()->getValue('SELECT id_country FROM '._DB_PREFIX_.'country WHERE iso_code="CZ"');   
            }
          $sql='SELECT id_zone FROM '._DB_PREFIX_.'country WHERE id_country='.$id_country;
          $zone= Db::getInstance()->getValue($sql);
          $this->doprava = new Doprava($zone, json_decode(Configuration::get("ZBOZI_CARRIERS"), true), Configuration::get('PS_SHIPPING_FREE_WEIGHT'), Configuration::get('PS_SHIPPING_FREE_PRICE'), $id_country, json_decode(Configuration::get('ZBOZI_CARRIERSCOD'), true), (int)Configuration::get('ZBOZI_DOPRAVACOD'));
   }
   
     $this->transformed=json_decode(Configuration::get('ZBOZI_TRANSFORMED'), true);
   
    
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
                            
       if(Configuration::get('ZBOZI_FILTERATR_'.$this->upname)) {
                   $this->filteredAttributes = $this->getFilteredAttributes($this->upname);
                   $this->FilterCombinations = new FilterCombinations();
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
      $item= "\t\t<SHOPITEM>\n";
      $item.=$this->createTag('ITEM_ID', $this->unique_item_id($product['id_product'], 0));
      
      $reference = $this->getReference($product);
     
      $item.=$this->addProductAndProductname($product, $reference, $product['ean13']);
      
    
      
      $item.=$this->createTag('DESCRIPTION', $this->prepareString($this->getDescription($product), true));
      if($product['condition'] == 'used') {
       	    $item.=$this->createTag('ITEM_TYPE', 'bazar');  
	   }
      $item.=$this->createTag('URL', $this->prepareString($url));  
      if($imgurl) {
            $item.=$this->createTag('IMGURL', $this->prepareString($imgurl));   
      }
      
      if(Configuration::get('ZBOZI_MULTIPLE_IMAGES') && $all_images)
           $item.=$this->additionalImages($all_images, $imgurl, 0 );
           
     
       $product['price'] = $this->ConvertAndRoundPrice( $product['price']);
       
      $item.=$this->createTag('PRICE_VAT', $product['price']); 
                                                                                              
       $item.=$this->createTag('CATEGORYTEXT', $this->prepareString($this->getCategoryText($product['categorytext_heureka'])));  
      
      $item.=$this->getDoprava($product);
      if(isset($product['videourl']) && strlen($product['videourl'])) {
           $item.=$this->createTag('VIDEOURL', $product['videourl']); 
      }
      
      $item.=$this->getDarek($product['id_product'], $product);
      $item.=$this->getAccessory($product['id_product']);
    
      if(isset($product['features']) && is_array($product['features'])) {
             $item.=$this->addFeatures($product['features']);
      }
          
      $item.=$this->createTag('DELIVERY_DATE', $this->getAvailability($product)); 
         
      if($product['manufacturer_name'])
      $item.=$this->createTag('MANUFACTURER', $this->prepareString($product['manufacturer_name']));
      if($this->isValidEan($product['ean13']))
      $item.=$this->createTag('EAN', $this->prepareString($product['ean13']));
      
   
     if($reference && strlen($reference)) {
      $item.=$this->createTag('PRODUCTNO', $reference);  
     }
      
      if(isset($product['heureka_cpc']) && strlen($product['heureka_cpc'])) 
            $item .=$this->createTag('HEUREKA_CPC', $this->floatFromString($product['heureka_cpc']));
      else if($this->cpc && $product[$this->cpc]) { // defaultne manufacturer_reference
            $item .=$this->createTag('HEUREKA_CPC',  $this->floatFromString($product[$this->cpc]));  
      }
      
      $item.="\t\t</SHOPITEM>\n";
       
        return $item;
 }
 
 protected function getAvailability($item) {
     $availability=parent::getAvailability($item);
    if($availability == 32)   // 32 je rezervovano pro heureka
         return '';
     
     
     return $availability;
 }

protected function getItemGroup($product, $url, $cover, $all_images) {
 
    $itemgroup='';
     foreach($product['attributes'] as $combination) {
             if($this->jen_skladem &&   $combination['quantity'] <=0) {
              continue;
			 }
            if((float)($product['price'] + $combination['price'] > 0)) {
                 if(Configuration::get('ZBOZI_FILTERATR_'.$this->upname)) {
                  if($this->FilterCombinations->remap($combination, $this->filteredAttributes, $product['id_product']) !== false)
                      $itemgroup.=$this->createItemCombination($product, $combination, $url, $cover, $all_images); 
                 } else
                 $itemgroup.=$this->createItemCombination($product, $combination, $url, $cover, $all_images);  
			}
        }
     return $itemgroup;
}

 protected function createItemCombination($product, $combination, $url, $imgurl, $all_images) {
     
       
         
       $item= "\t\t<SHOPITEM>\n";
       $item.=$this->createTag('ITEM_ID', $this->unique_item_id($product['id_product'], $combination['id_product_attribute']));
       $item.=$this->createTag('ITEMGROUP_ID', $product['id_product']);    
       
       $reference = $this->getReference($product, $combination);
     
          
       if(isset($combination['ean13']) && strlen($combination['ean13']))
          $ean13=$combination['ean13'];
       else
          $ean13=$product['ean13'];
      
      $item.=$this->addProductAndProductname($product, $reference, $ean13, $combination);    
      
      
            
      if($this->isValidEan($ean13))
            $item.=$this->createTag('EAN', $ean13); 
            
      if($reference && strlen($reference)) 
            $item.=$this->createTag('PRODUCTNO', $reference);  
          
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
       
       if((float)$product['unit_price_ratio'] > 0 &&  $this->unitprice) {
                 $price = Tools::ps_round(($price /$product['unit_price_ratio']), 2);
       } 
       
        $this->cache[$product['id_product']][$combination['id_product_attribute']]['price']=$price;
        $this->cache[$product['id_product']][$combination['id_product_attribute']]['date_upd']=$product['date_upd']; 
        $this->cache[$product['id_product']][$combination['id_product_attribute']]['product_price']=$product['price']; 
        $this->cache[$product['id_product']][$combination['id_product_attribute']]['attribute_price']=$combination['price']; 
     } 
          
       
      
      
      $item.=$this->createTag('DESCRIPTION', $this->prepareString($this->getDescription($product), true));
      if($product['condition'] == 'used') {
               $item.=$this->createTag('ITEM_TYPE', 'bazar');  
       }
      $url.='#'.$this->getCombinationUrl($combination['attributes']);
       if(isset($combination['removed']) && count($combination['removed'])) {
         $url.= $this->getCombinationUrl($combination['removed']); 
      }
      $item.=$this->createTag('URL', $this->prepareString($url));  
      $imgurlc = '';
      if($combination['id_image']) {
             $imgurlc = $this->combinationCoverUrl($all_images, $combination['id_product_attribute']);
             $item.=$this->createTag('IMGURL', $this->prepareString($imgurlc)); 
             if(Configuration::get('ZBOZI_MULTIPLE_IMAGES') && $all_images) 
            $item.= $this->additionalImages($all_images, $imgurlc, $combination['id_product_attribute']);
      }
      
     if($imgurlc == '' && $imgurl) {
            $item.=$this->createTag('IMGURL', $this->prepareString($imgurl));   
      }
      
     $price =  $this->ConvertAndRoundPrice($price);
      $item.=$this->createTag('PRICE_VAT', $price); 
      $item.=$this->createTag('CATEGORYTEXT', $this->prepareString($this->getCategoryText($product['categorytext_heureka'])));  
      $item.=$this->getDoprava($product);
      $item.=$this->getDarek($product['id_product'], $product);
      $item.=$this->getAccessory($product['id_product']);
      if(isset($product['videourl']) && strlen($product['videourl'])) {
           $item.=$this->createTag('VIDEOURL', $product['videourl']); 
      }
      $features=$this->featuresFromCombination($combination, $product['features']);
      if(is_array($features) && count($features)) {
             $item.=$this->addFeatures($features);
      }
     
      $item.=$this->createTag('DELIVERY_DATE', $this->getAvailability($this->mergeAvailability($combination, $product))); 
         
      if($product['manufacturer_name'])
      $item.=$this->createTag('MANUFACTURER', $this->prepareString($product['manufacturer_name']));
      
      if(isset($product['heureka_cpc']) && strlen($product['heureka_cpc'])) 
            $item .=$this->createTag('HEUREKA_CPC', $this->floatFromString($product['heureka_cpc']));
      else if($this->cpc && $product[$this->cpc])  // defaultne manufacturer_reference
            $item .=$this->createTag('HEUREKA_CPC', $this->floatFromString($product[$this->cpc]) );
            
      $item.="\t\t</SHOPITEM>\n";
        
        return $item;  
       
   }   

   
  
 

protected function getDoprava($product) {
if(!$this->doprava)
  return '';  

    
if(zbozi::version_compare(_PS_VERSION_, '1.5.0', '<')) { 
    
   $retval = $this->doprava->getDoprava($product, 1);
}
else {
   $retval = $this->doprava->getDoprava($product);
}
   if(is_array($retval) && count($retval))  {
   
     return $this->compile_delivery($retval); 
   } 
   
   return '';

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
    	 $val=$this->ConvertAndRoundPrice((float)$arr[0]);
        
    	 $cod=$this->ConvertAndRoundPrice((float) $arr[0] + (float) $arr[1]);
        $retval.="\t\t\t<DELIVERY>\n";
         if((float) $arr[1] < 0) {
            $retval.="\t\t\t\t<DELIVERY_ID>$key</DELIVERY_ID>\n\t\t\t\t<DELIVERY_PRICE>$val</DELIVERY_PRICE>\n";
         }
         else {
            $retval.="\t\t\t\t<DELIVERY_ID>$key</DELIVERY_ID>\n\t\t\t\t<DELIVERY_PRICE>$val</DELIVERY_PRICE>\n\t\t\t\t<DELIVERY_PRICE_COD>$cod</DELIVERY_PRICE_COD>\n";
         }
        $retval.="\t\t\t</DELIVERY>\n";
    }
    
    return $retval;
    
}





 
 protected function getCategoryText($categorytext)  {
 //echo  $categorytext;  
  return $this->prepareString($categorytext);
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
  
  protected function  additionalImages($all_images, $cover_url, $id_product_attribute = 0) {
  	  $retval ='';
     foreach($all_images as $imgurl) {
        if($imgurl['id_product_attribute'] == $id_product_attribute)
          if($cover_url != $imgurl['url'])
             $retval.=$this->createTag('IMGURL_ALTERNATIVE', $this->prepareString($imgurl['url'])); 
	 }
     return $retval;  
  }
  
  protected function getDarek($id_product, $product) {
  	  $retval ='';
  	   if(!Configuration::get("ZBOZI_DAREK_HEUREKA")) 
  	     return $retval;
         
   if(isset($product['extramessage']) && strlen($product['extramessage'])) {
           $key = 3;
           if(isset($product['extramessage'][$key]) && strlen($product['extramessage'][$key] ) &&(int)$product['extramessage'][$key] ) {
               $darek = Configuration::get("ZBOZI_DAREK_HEUREKANAME");
               if($darek && strlen($darek)) {
                $retval.=$this->createTag('GIFT', $darek); 
               }
         }
         
    }
     
  	                                           
  	   if(!is_array($this->darky))
  	     return $retval;
  	     
  	   if(isset($this->darky[$id_product])) {
  	   	  foreach($this->darky[$id_product] as $darek)  {
  	   	  	 $retval.=$this->createTag('GIFT', $darek); 
		  }	   
	   }
	   
	   return $retval;
  }
  
    protected function getAccessory($id_product) {
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
  
  protected function ConvertAndRoundPrice($price) {
       global $CurrencyTo;
       if(! is_null($CurrencyTo)) {
           $price  = Tools::convertPrice($price,  $CurrencyTo);   
         }
       if(Configuration::get("ZBOZI_ROUND_HEUREKA") && (is_null($CurrencyTo) || $CurrencyTo->iso_code == 'CZK'   )) 
            $price =  Tools::ps_round($price,0);
       else  
          $price =  Tools::ps_round($price,2);
          
       return $price;
    }
 
  }
?>
