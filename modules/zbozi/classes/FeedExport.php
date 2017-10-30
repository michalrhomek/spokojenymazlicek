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
   class FeedExport extends cFeed {
   protected $indexes;
   protected $feedname;
  public function __construct() {
   	   
     parent::__construct();
     $this->feedname=cFeed::addShopName().getFeedName($_GET['manufacturers']).'.xml';
		$this->indexes =array();
		$this->indexes[] = array('name', 'string', 'PRODUCTNAME'); 
		$this->indexes[] = array('reference', 'string', 'reference'); 
		$this->indexes[] = array('id_product', 'int', 'ITEM_ID');
		$this->indexes[] = array('supplier_reference','string');
		$this->indexes[] = array('ean13', 'string', 'EAN'); 
		$this->indexes[] = array('categorytext', 'string');
		$this->indexes[] = array('description', 'string');
		$this->indexes[] = array('description_short', 'string');
		$this->indexes[] = array('meta_keywords', 'string');
		$this->indexes[] = array('meta_title', 'string');
		$this->indexes[] = array('meta_decription', 'string');
		$this->indexes[] = array('active', 'int');
		$this->indexes[] = array('on_sale', 'int');
		$this->indexes[] = array('quantity', 'int');
		$this->indexes[] = array('show_price', 'int'); 
        
        $this->indexes[] = array('width', 'string');
        $this->indexes[] = array('height', 'string');
        $this->indexes[] = array('depth', 'string');
        $this->indexes[] = array('weight', 'string');
        $this->indexes[] = array('condition', 'string');
        
		$this->indexes[] = array('manufacturer_name', 'string', 'MANUFACTURER');
		$this->indexes[] = array('supplier_name', 'string', 'SUPPLIER');
		$this->indexes[] = array('available_date', 'string');
     
     
   } 
  
 
 
 
 protected function createItem($product, $url, $imgurl, $all_images) {
    
	if(Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL')) {
	     $url=  $this->cs_utf2ascii($url);
	}   
    $item= "\t\t<SHOPITEM>\n";
    reset($this->indexes);
 
    foreach($this->indexes as $index) {
    	if(isset($index[3]) && $index[3] == false)
    	  continue;
    	$tagname = isset($index[2])?$index[2]:strtoupper($index[0]);
    	switch ($index[1]) {
    		case 'int': $val = (int)$product[$index[0]]; break;
    		default:  
    		$val = $this->prepareString($product[$index[0]]);
		}
        $item.=$this->createTag($tagname, $val);
	}
      if($url) {
            $item.=$this->createTag('URL', $this->prepareString($url));   
      }
    
     if($imgurl) {
            $item.=$this->createTag('IMGURL', $this->prepareString($imgurl));   
      }
    
     
      if($all_images)
           $item.=$this->additionalImages($all_images, $imgurl, 0);
       
    global $CurrencyTo;
       if(! is_null($CurrencyTo)) {
         	$product['price']=Tools::convertPrice($product['price'],  $CurrencyTo);
         	$product['wholesale_price']=Tools::convertPrice($product['wholesale_price'],  $CurrencyTo); 
         	$product['ecotax']=Tools::convertPrice($product['ecotax'],  $CurrencyTo); 
		 }  
	 $item.=$this->specificPrice($product['specific_price']);     
    $item.=$this->createTag('PRICE', $product['price']); 
   
     $item.=$this->createTag('VAT', $this->getVat($product['id_tax_rules_group'])); 
      $item.=$this->createTag('WHOLESALE_PRICE', $product['wholesale_price']); 
      $item.=$this->createTag('ECOTAX', $product['ecotax']); 
    $item.=$this->addFeatures($product);  
    $item.="\t\t</SHOPITEM>\n";

    return $item;
 
 }    
 
 protected function getItemGroup($product, $url, $cover, $all_images) {
 
    $itemgroup='<SHOPITEM>';
    reset($this->indexes);
     foreach($this->indexes as $index) {
       if(isset($index[3]) && $index[3] == false)
    	  continue;
       $tagname = isset($index[2])?$index[2]:strtoupper($index[0]);
    	switch ($index[1]) {
    		case 'int': $val = (int)$product[$index[0]]; break;
    		default:  $val = $this->prepareString($product[$index[0]]);
		}
        $itemgroup.=$this->createTag($tagname, $val);
	}
	 if($url) {
            $itemgroup.=$this->createTag('URL', $this->prepareString($url));   
      }
        $itemgroup.="\t\t\t<IMAGES>\n";
	if($cover) {
            $itemgroup.=$this->createTag('IMGURL', $this->prepareString($cover));   
      }
    
     
      if($all_images)
           $itemgroup.=$this->additionalImages($all_images, $cover, 0);
      $itemgroup.="\t\t\t</IMAGES>\n";     
  
      global $CurrencyTo;
       if(! is_null($CurrencyTo)) {
         	$product['price']=Tools::convertPrice($product['price'],  $CurrencyTo); 
		 }
    $itemgroup.=$this->createTag('PRICE', $product['price']); 
     $itemgroup.=$this->createTag('VAT', $this->getVat($product['id_tax_rules_group'])); 
    
     $itemgroup.=$this->addFeatures($product);        
       global $link;
     $itemgroup.="\t\t\t<VARIANTS>\n";   
     foreach($product['attributes'] as $combination) {
              if($this->jen_skladem && $this->stock_management &&  $combination['quantity'] <= 0)
              continue;
            if((float)($product['price'] + $combination['price'] > 0)) {
            	
          if($combination['id_image']) {
             $name=$this->toUrl($product['name']);
             $imgurl=$link->getImageLink($name, $product['id_product'].'-'.$combination['id_image'], $this->imagetype);      
            }
            else
			$imgurl=$cover;
			
			 
			$itemgroup.=$this->createItemCombination($product, $combination, $url, $imgurl);  
			
			}
        }
     $itemgroup.="\t\t\t</VARIANTS>\n";    
        
    $itemgroup.='</SHOPITEM>'; 
     return $itemgroup;
}  

 function addFeatures($product) {
 	 if(!isset($product['features']) || count($product['features']) == 0)
 	   return;
 	   
 	 $item= "\t\t\t\t<FEATURES>\n";
 	 foreach($product['features'] as $feature) {
 	 	 $item.="\t\t\t\t\t<PARAM>\n"; 
         $item.=$this->createTag('PARAM_NAME',$feature['name'], "\t\t\t\t\t\t");
         $item.=$this->createTag('VAL', $this->prepareString($feature['value']), "\t\t\t\t\t\t");
      	 $item.="\t\t\t\t\t</PARAM>\n"; 
	 }
 	  $item.= "\t\t\t\t</FEATURES>\n";
 	  return $item;
 }

   protected function createItemCombination($product, $combination, $url, $imgurl) {
      $item= "\t\t\t\t<VARIANT>\n";
      foreach($combination['attributes'] as $comb) {
         $item.="\t\t\t\t\t<PARAMETERS>\n"; 
         $item.=$this->createTag('NAME',$comb[0]);
         $item.=$this->createTag('VALUE',$comb[1]);
      	 $item.="\t\t\t\t\t</PARAMETERS>\n";  
	  }
	  $item.="\t\t\t\t\t<HODNOTY>\n";  
	  $price=Product::getPriceStatic($product['id_product'], false, $combination['id_product_attribute'],2);
	   global $CurrencyTo;
      if(! is_null($CurrencyTo)) {
         	$price=Tools::convertPrice($price,  $CurrencyTo); 
	  }
      
	  $item.=$this->createTag('PRICE', $price);
	 
	  $item.=$this->createTag('QUANTITY', $combination['quantity']);
	  $item.=$this->createTag('EAN13', $this->prepareString($combination['ean13']));
	  $item.=$this->createTag('REFERENCE', $this->prepareString($combination['reference']));
	  $item.=$this->createTag('AVAILABLE_DATE', $this->prepareString($combination['available_date']));
      $item.="\t\t\t\t\t</HODNOTY>\n"; 

      $item.="\t\t\t\t</VARIANT>\n";
        
        return $item;  
       
   }    
 
 
 private function specificPrice($specific_price) {
 	 $item ='';
 	if(!is_array($specific_price) || !isset($specific_price['id_specific_price']))
 	  return $item;
      $item= "\t\t\t\t<SPECIFIC_PRICE>\n";
      $item.=$this->createTag('PRICE', $specific_price['price'], "\t\t\t\t\t");
      $item.=$this->createTag('REDUCTION', $specific_price['reduction'], "\t\t\t\t\t");  
      $item.=$this->createTag('REDUCTION_TYPE', $specific_price['reduction_type'], "\t\t\t\t\t");  
      $item.=$this->createTag('FROM', $specific_price['from'], "\t\t\t\t\t");  
      $item.=$this->createTag('TO', $specific_price['to'], "\t\t\t\t\t");  
      $item.= "\t\t\t\t</SPECIFIC_PRICE>\n";
     return $item; 
 }
 
 
 
  private function  additionalImages($all_images, $cover_url, $id_product_attribute = 0) {
  	  $retval ='';
     foreach($all_images as $imgurl)  {
     	 if($imgurl['id_product_attribute'] == $id_product_attribute)
     	   if($cover_url != $imgurl['url'])
              $retval.=$this->createTag('IMGURL_ALTERNATIVE', $this->prepareString($imgurl['url'])); 
	 }
     
     return $retval;  
  }   
  
    protected function StartFeed($fp) {
   	   
    fputs($fp,  "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n");
      fputs($fp,  "<SHOP>");  

  }      
   protected function CloseFeed($fp) {
      fputs($fp,  "</SHOP>");  
     
 }    
 
 private function getVat($id_tax_rules_group) {
 	 global $rates;
 	 
 	 if(isset($rates[$id_tax_rules_group]))
 	   return $rates[$id_tax_rules_group];
 	   
 	 return '';
 	 
 }
  }
?>
