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
 require_once("classes/FeedLocal.php");
  class FeedSeznam extends FeedLocal {
   protected  $feedname='zbozi_seznam.xml';
   protected  $cats_forbidden=array();
   protected $ext_behav;
   protected $transformed;
   protected $filteredAttributes;
   protected $FilterCombinations;
   protected $unitFeatures;
   protected $upname ='SEZNAM';
   
public function __construct() {
    parent::__construct();
    $cats=Configuration::get('ZBOZI_CATS_FORBIDDENzb');
    if($cats && strlen($cats ))
    $this->cats_forbidden = explode(',', $cats);
    $features = json_decode(Configuration::get('ZBOZI_FEATURESzb'), true);   
    if($features && is_array($features)) {
        foreach($features as $feature) {
          $this->unitFeatures[$feature[0]] =  $feature[1]; 
        }
    }
    $this->transformed=json_decode(Configuration::get('ZBOZI_TRANSFORMEDzb'), true);    
    $this->ext_behav = (int)Configuration::get('ZBOZI_TEXT_EXT');

    if(Configuration::get('ZBOZI_FILTERATR_'.$this->upname)) {
       $this->filteredAttributes = $this->getFilteredAttributes($this->upname);
       $this->FilterCombinations = new FilterCombinations();
    }
}
   
   protected function StartFeed($fp) {
   	   
    fputs($fp,  "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n");
    
    fputs($fp,  "<SHOP xmlns=\"http://www.zbozi.cz/ns/offer/1.0\">\n"); 
  }      
   protected function CloseFeed($fp) {
      fputs($fp,  "</SHOP>");  
     
 } 
 
 protected function createItem($product, $url, $imgurl, $all_images) {
    if($this->isInForbiddenCategory($product['id_category_default']) && (int) Configuration::get('ZBOZI_CATS_EROTIC') != 1)  
      return;
	 
	if(Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL')) {
	     $url=  $this->cs_utf2ascii($url);
	}   
    $url.=trim(Configuration::get('ZBOZI_UTM_'.$this->upname));
    $item= "\t\t<SHOPITEM>\n";
    $item.=$this->createTag('ITEM_ID', $this->unique_item_id($product['id_product'], 0));
    $reference = $this->getReference($product);
    
    $item.=$this->addProductAndProductname($product, $reference, $product['ean13']);
    
    if(isset($product['productline']) && strlen($product['productline'])) {
         $item.=$this->createTag('PRODUCT_LINE', $productline);
      } 
    
    $item.=$this->getCategoryText($product['categorytext_seznam'])."\n";
    
    if(isset($product['extramessage']) && strlen($product['extramessage'])) {
         $messages = array('extended_warranty','free_accessories','free_case','free_gift','free_installation','free_store_pickup','voucher', 'free_delivery');
         while(list($key,$val) = each($messages)) {
         if(isset($product['extramessage'][$key]) && strlen($product['extramessage'][$key] ) &&(int)$product['extramessage'][$key] ) {
             $item.=$this->createTag('EXTRA_MESSAGE',  $val); 
         }
         }
    }
     
      $features = $this->prefilterFeatures($product['features'], $product['categorytext_seznam']);
      if(is_array($features) && count($features)) {
             $item.=$this->addFeatures($features);
      }
    $item.= "\t\t\t<DESCRIPTION>".$this->prepareString($this->getDescription($product))."</DESCRIPTION>\n";
    
    $item.= "\t\t\t<URL>".$this->prepareString($url)."</URL>\n";
   // $item.= "\t\t\t<SHOP_DEPOTS>dets2908</SHOP_DEPOTS>\n";
 
     if($imgurl) {
            $item.=$this->createTag('IMGURL', $this->prepareString($imgurl));   
      }
     
      if(Configuration::get('ZBOZI_MULTIPLE_IMAGES') && $all_images)
           $item.=$this->additionalImages($all_images, $imgurl, 0);
    
    
	
	 global $CurrencyTo;
      if(! is_null($CurrencyTo)) {
         	 $product['price'] =Tools::convertPrice( $product['price'] ,  $CurrencyTo); 
		 }
    if(Configuration::get("ZBOZI_ROUND_ZBOZI") && (is_null($CurrencyTo) || $CurrencyTo->iso_code == 'CZK'   )) {
	   	 $product['price'] =  Tools::ps_round($product['price'],0);
	}   
	       
    $item.=$this->createTag('PRICE_VAT', $product['price']); 
    $item.= "\t\t\t<DELIVERY_DATE>".$this->getAvailability($product)."</DELIVERY_DATE>\n";
  
    if($this->isValidEan($product['ean13']))
      $item.=$this->createTag('EAN', $this->prepareString($product['ean13']));
    if($reference && strlen($reference)) {
       $item.=$this->createTag('PRODUCTNO', $reference);
    }  
  
      
     
      
      if(isset($product['max_cpc']) && strlen($product['max_cpc'])) 
            $item .=$this->createTag('MAX_CPC', $this->floatFromString($product['max_cpc']));
      if(isset($product['max_cpc_search']) && strlen($product['max_cpc_search'])) 
            $item .=$this->createTag('MAX_CPC_SEARCH', $this->floatFromString($product['max_cpc_search']));
    
     if(($this->isInForbiddenCategory($product['id_category_default']) && (int) Configuration::get('ZBOZI_CATS_EROTIC') == 1) 
     || Configuration::get('ZBOZI_CATS_EROTIC') == 2
     )
       $item.=$this->createTag('EROTIC', 1);
    
    if($product['manufacturer_name'])
      $item.=$this->createTag('MANUFACTURER', $this->prepareString($product['manufacturer_name']));
      
    $item.="\t\t</SHOPITEM>\n";

    return $item;
 
 }    
 
 protected function getItemGroup($product, $url, $cover, $all_images) {
 if($this->isInForbiddenCategory($product['id_category_default'])) {
      return;
 }
      $itemgroup='';
     foreach($product['attributes'] as $combination) {
             if($this->jen_skladem &&   $combination['quantity'] <=0) {
              continue;
			 }
            if((float)($product['price'] + $combination['price'] > 0)) {
                 if(Configuration::get('ZBOZI_FILTERATR_'.$this->upname)) {
                  if($this->FilterCombinations->remap($combination, $this->filteredAttributes, $product['id_product']) !== false)
                      $itemgroup.=$this->createItemCombination($product, $combination, $url, $cover, $all_images); 
                 }
                 else
                 $itemgroup.=$this->createItemCombination($product, $combination, $url, $cover, $all_images);  
			}
			else {
				;
			 
			}
        }
     return $itemgroup;
}  


protected function createItemCombination($product, $combination, $url, $imgurl, $all_images) {
     
    
     if($this->isInForbiddenCategory($product['id_category_default']) && (int) Configuration::get('ZBOZI_CATS_EROTIC') != 1)  
      return;
      $item= "\t\t<SHOPITEM>\n";
       
       $item.=$this->createTag('ITEM_ID', $this->unique_item_id($product['id_product'], $combination['id_product_attribute']));
       $item.=$this->createTag('ITEMGROUP_ID', $product['id_product']);    
       $reference = $this->getReference($product, $combination);
       
          
       if(isset($combination['ean13']) && strlen($combination['ean13']))
          $ean13=$combination['ean13'];
       else
          $ean13=$product['ean13'];
          
      
      $item.=$this->addProductAndProductname($product, $reference, $ean13, $combination);    
      
      if(isset($product['productline']) && strlen($product['productline'])) {
         $item.=$this->createTag('PRODUCT_LINE', $productline);
      } 
      
      if($this->isValidEan($ean13))
            $item.=$this->createTag('EAN', $ean13);
            
      if($reference && strlen($reference)) 
            $item.=$this->createTag('PRODUCTNO', $reference);
      
      $item.=$this->createTag('DESCRIPTION', $this->prepareString($this->getDescription($product)));
      if($product['condition'] == 'used') {
       	    $item.=$this->createTag('ITEM_TYPE', 'bazar');  
	   }
      $url.=trim(Configuration::get('ZBOZI_UTM_'.$this->upname));
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
    if(Configuration::get("ZBOZI_ROUND_ZBOZI") && (is_null($CurrencyTo) || $CurrencyTo->iso_code == 'CZK'   )) {
	   	 $price =  Tools::ps_round($price,0);
	}  
      $item.=$this->createTag('PRICE_VAT', $price); 
      $item.=$this->getCategoryText($product['categorytext_seznam'])."\n";
      
       if(isset($product['extramessage']) && strlen($product['extramessage'])) {
         $messages = array('extended_warranty','free_accessories','free_case','free_gift','free_installation','free_store_pickup','voucher', 'free_delivery');
         while(list($key,$val) = each($messages)) {
         if(isset($product['extramessage'][$key]) && strlen($product['extramessage'][$key] ) &&(int)$product['extramessage'][$key] ) {
             $item.=$this->createTag('EXTRA_MESSAGE',  $val); 
         }
         }
    }
   
    // $features = $product['features'];
      $features=$this->featuresFromCombination($combination, $product['features']);
       $features = $this->prefilterFeatures($features, $product['categorytext_seznam']);
      if(is_array($features) && count($features)) {
             $item.=$this->addFeatures($features);
      }
      
      $item.=$this->createTag('DELIVERY_DATE', $this->getAvailability($this->mergeAvailability($combination, $product))); 
      
      if(isset($product['max_cpc']) && strlen($product['max_cpc'])) 
            $item .=$this->createTag('MAX_CPC', $this->floatFromString($product['max_cpc']));
      if(isset($product['max_cpc_search']) && strlen($product['max_cpc_search'])) 
            $item .=$this->createTag('MAX_CPC_SEARCH', $this->floatFromString($product['max_cpc_search']));
      
      
      
       if(($this->isInForbiddenCategory($product['id_category_default']) && (int) Configuration::get('ZBOZI_CATS_EROTIC') == 1) 
     || Configuration::get('ZBOZI_CATS_EROTIC') == 2
     )
       $item.=$this->createTag('EROTIC', 1);
          
      if($product['manufacturer_name'])
      $item.=$this->createTag('MANUFACTURER', $this->prepareString($product['manufacturer_name']));
    
      
      if(isset($product['max_cpc']) && strlen($product['max_cpc'])) 
            $item .=$this->createTag('MAX_CPC', $this->floatFromString($product['max_cpc']));
      if(isset($product['max_cpc_search']) && strlen($product['max_cpc_search'])) 
            $item .=$this->createTag('MAX_CPC_SEARCH', $this->floatFromString($product['max_cpc_search']));
            
      $item.="\t\t</SHOPITEM>\n";
        
        return $item;  
       
   }   
      
 protected function getCategoryText($categorytext)  {
     $item='';
      if(!empty($categorytext)  && is_array($categorytext)) {
    foreach($categorytext as $category) {
     $item.='<CATEGORYTEXT>'.$this->prepareString($category).'</CATEGORYTEXT>';
    }
    } 
    elseif(!empty($categorytext)) {
        $item.='<CATEGORYTEXT>'.$this->prepareString($categorytext).'</CATEGORYTEXT>';  
    }
    
    return $item; 
 }
 
 protected function getAvailability($item) {
 	$availability=parent::getAvailability($item);
    if(strlen($availability) == 10 && substr($availability,0,1) == 2)
        return $availability;
    
    if($availability == 32)   // 32 je rezervovano pro heureka
          $availability = 31;
 	
 	if((int)Configuration::get('ZBOZI_SKLADzb')  
 	 && (int)$this->availability_mode == 0
 	 && !empty($item['available_now'])) {
 	 	if((int)Configuration::get('ZBOZI_SKLADzb') == 1) {
 	 	  if($availability == 1)
 	 	    return 0;
		}
 	 	elseif((int)Configuration::get('ZBOZI_SKLADzb') == 2) {
 	 	  $text=mb_strtolower($item['available_now'], 'UTF-8');
 	 	  $pos=strpos($text, 'skladem');
 	 	  if(!($pos === false))
 	 	    return 0;
		}  
	 }
 	return $availability;
 }
 
 protected function isInForbiddenCategory($id_category_default) {
      if(!is_array($this->cats_forbidden))
        return false;
        
      if(!count($this->cats_forbidden))
        return false;
        
      if(in_array($id_category_default, $this->cats_forbidden))
        return true;
        
      return false;
 }
 
  private function  additionalImages($all_images, $cover_url, $id_product_attribute = 0) {
  	  $retval ='';
     foreach($all_images as $imgurl)  {
     	 if($imgurl['id_product_attribute'] == $id_product_attribute)
     	   if($cover_url != $imgurl['url'])
              $retval.=$this->createTag('IMGURL', $this->prepareString($imgurl['url'])); 
	 }
     
     return $retval;  
  } 
  
  //todo filter and remap features viz samostatne soubory ve 3.14
  private function prefilterFeatures($features, $categorytext) {
      return $features;
  }
  
     protected function addFeatures($features) {
         $retval='';
         foreach($features as $feature) {
              $retval.="\t\t\t<PARAM>\n\t\t\t\t<PARAM_NAME>{$this->prepareString($feature['name'])}</PARAM_NAME>";
             
              $retval.="\t\t\t\t<VAL>{$this->prepareString($feature['value'])}</VAL>\n";
               if(is_array($this->unitFeatures) && isset($this->unitFeatures[$feature['id_feature']])) {
                  $retval.="\n\t\t\t\t<UNIT>{$this->prepareString($this->unitFeatures[$feature['id_feature']])}</UNIT>\n"; 
              }
              $retval.="\t\t\t</PARAM>\n";
         }
         return $retval;
     }
      
  }
?>
