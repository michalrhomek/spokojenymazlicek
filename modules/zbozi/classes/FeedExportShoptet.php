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
   class FeedExportShoptet extends cFeed {
   protected $indexes;
   protected $feedname;
  public function __construct() {
   	   
     parent::__construct();
     $this->feedname=cFeed::addShopName().getFeedName($_GET['manufacturers']).'.xml';
 		$this->indexes =array();
		$this->indexes[] = array('name', 'string', 'NAME'); 
		$this->indexes[] = array('description_short', 'string','SHORT_DESCRIPTION');
		$this->indexes[] = array('description', 'string','DESCRIPTION');
		$this->indexes[] = array('manufacturer_name', 'string', 'MANUFACTURER');
		$this->indexes[] = array('reference', 'string', 'CODE');
		$this->indexes[] = array('ean13', 'string', 'EAN');  
	
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
    $images = array();
    if($imgurl)
    $images[] = $imgurl;
    if($all_images)
    $images = array_merge($images, $all_images);
    
    $item.=$this->additionalImages($images, $imgurl, 0);
    $item.="\t\t\t<CATEGORIES>\n";

    $item.=$this->createTag('CATEGORY', $product['categorytext']);
     $item.= "\t\t\t</CATEGORIES>\n";    
    global $CurrencyTo;
       if(! is_null($CurrencyTo)) {
         	$product['price']=Tools::convertPrice($product['price'],  $CurrencyTo); 
		 }    
	$item.="\t\t\t<STOCK>\n";
	$item.=$this->createTag('AMOUNT', $product['quantity']);
	$item.=$this->createTag('MINIMAL_AMOUNT', $product['minimal_quantity']);
	$item.="\t\t\t</STOCK>\n";
	
    $item.=$this->createTag('PRICE', $product['price']); 
   
    $item.=$this->createTag('VAT', $this->getVat($product['id_tax_rules_group'])); 
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
			
			$itemgroup.="\t\t\t<VARIANTS>\n";    
			$itemgroup.=$this->createItemCombination($product, $combination, $url, $imgurl);  
			$itemgroup.="\t\t\t</VARIANTS>\n";    
			}
        }
        
    $itemgroup.='</SHOPITEM>'; 
     return $itemgroup;
}  

 function addFeatures($product) {
 	 if(isset($product['features']) || count($product['features']) == 0)
 	   return;
 	   
 	 $item= "\t\t\t\t<TEXT_PROPERTIES>\n";
 	 foreach($product['features'] as $feature) {
 	 	 $item.="\t\t\t\t\t<TEXT_PROPERTY>\n"; 
         $item.=$this->createTag('NAME',$feature['name']);
         $item.=$this->createTag('VALUE',$feature['value']);
      	 $item.="\t\t\t\t\t</TEXT_PROPERTY>\n"; 
	 }
 	  $item= "\t\t\t\t</TEXT_PROPERTIES>\n";
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
	 
	  $price=Product::getPriceStatic($product['id_product'], false, $combination['id_product_attribute'],2);
	   global $CurrencyTo;
      if(! is_null($CurrencyTo)) {
         	$price=Tools::convertPrice($price,  $CurrencyTo); 
	  }
      
	  $item.=$this->createTag('PRICE', $price);
	 //  $item.=$this->createTag('QUANTITY', $combination['quantity']);
	  $item.=$this->createTag('CODE', $this->prepareString($combination['reference']));
	//  $item.=$this->createTag('AVAILABLE_DATE', $this->prepareString($combination['available_date']));
      

      $item.="\t\t\t\t</VARIANT>\n";
        
        return $item;  
       
   }    
 
 
 
 
 
 
  private function  additionalImages($all_images, $cover_url, $id_product_attribute = 0) {
  	  $retval ="\t\t<IMAGES>";
     foreach($all_images as $imgurl)  {
     	  
              $retval.=$this->createTag('IMAGE', $this->prepareString($imgurl)); 
	 }
	 $retval.="</IMAGES>";
     
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
 	   return (int)$rates[$id_tax_rules_group];
 	   
 	 return '';
 	 
 }
  }
?>
