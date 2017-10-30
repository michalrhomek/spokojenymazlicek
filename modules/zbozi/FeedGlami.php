<?php
  
require_once("FeedHeureka.php");
  class FeedGlami extends FeedHeureka {
   protected  $feedname='zbozi_glami.xml';
   protected $cats_forbidden;
   protected $filteredAttributes = false;
   protected $FilterCombinations;
   protected $upname ='GLAMI';
   
    public function __construct() {
    
   FeedLocal::__construct();  
  
   if(Configuration::get('PS_LOCALE_COUNTRY') == 'sk') {
    $this->id_country=Db::getInstance()->getValue('SELECT id_country FROM '._DB_PREFIX_.'country WHERE iso_code="SK"');
   }
   else {
        $this->id_country=Db::getInstance()->getValue('SELECT id_country FROM '._DB_PREFIX_.'country WHERE iso_code="CZ"');   
   }
   $sql='SELECT id_zone FROM '._DB_PREFIX_.'country WHERE id_country='.$this->id_country;
   $this->zone= Db::getInstance()->getValue($sql);
   $this->doprava=Configuration::get("ZBOZI_DOPRAVAGL_ON");
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
   
     $this->free_shipping_price =Configuration::get('PS_SHIPPING_FREE_PRICE');
   
     $this->free_shipping_weight =Configuration::get('PS_SHIPPING_FREE_WEIGHT');
     $this->ext_behav = (int)Configuration::get('ZBOZI_TEXT_EXT');
     
      $cats=Configuration::get('ZBOZI_CATS_FORBIDDENgl');
     if($cats && strlen($cats ))
       $this->cats_forbidden = explode(',', $cats);
       
       if(Configuration::get('ZBOZI_FILTERATR_'.$this->upname)) {
               $this->filteredAttributes = $this->getFilteredAttributes($this->upname);
               $this->FilterCombinations = new FilterCombinations();
       }
 }  
 
   protected function featuresFromCombination($combination, $features=null) {

       $retval=array();
       $keys=array();
  
           foreach($combination['attributes'] as $at) {
                $keys[]=  $at[0];
                $retval[]=array('name'=>$at[0], 'value'=>$at[1]);
              
           }
   
  if(isset($features) && is_array($features) && count($features) && count($keys)) {
      while(list($key,$val)=each($features)) {
        if(! in_array($val['name'], $keys))
          $retval[]=array('name'=>$val['name'], 'value'=>$val['value']);   
      }
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
                 if(Configuration::get('ZBOZI_FILTERATR_'.$this->upname)) {
                  if($this->FilterCombinations->remap($combination, $this->filteredAttributes, $product['id_product']) !== false) {
                      $itemgroup.=$this->createItemCombination($product, $combination, $url, $cover, $all_images); 
                  }
                 } else
                 $itemgroup.=$this->createItemCombination($product, $combination, $url, $cover, $all_images);  
            }
            else {
                
            }
        }
     return $itemgroup;
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
                                                                                              
       $item.=$this->createTag('CATEGORYTEXT', $this->prepareString($this->getCategoryText($product['categorytext_glami'])));  
      
      $item.=$this->getDoprava($product);
      if(isset($product['videourl']) && strlen($product['videourl'])) {
           $item.=$this->createTag('VIDEOURL', $product['videourl']); 
      }
      
      $item.=$this->getDarek($product['id_product']);
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
      $item.=$this->createTag('CATEGORYTEXT', $this->prepareString($this->getCategoryText($product['categorytext_glami'])));  
      $item.=$this->getDoprava($product);
      $item.=$this->getDarek($product['id_product']);
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

 }