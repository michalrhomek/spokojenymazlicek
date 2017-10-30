<?php
  class CsvWrite {
 
 private $id_lang;
 private $feeddir;     
 private $rates;
 private $mode;
 private $imgdir;
 
 public function __construct( $feeddir, $id_lang, $rates=null, $imgdir=null) {
     $this->feeddir=$feeddir;
     
     $this->id_lang=$id_lang;
     if(!is_null($rates)) {
     if(is_array($rates)) {
         foreach($rates as $r) {
           $this->rates[$r['rate']]= $r['id_tax'];
         }
     }
     else {
        $this->rates=(int) $rates;    
     }
     }
     $this->imgdir=$imgdir;
    // $this->stamp=date('ymdHi');
 }
 
public function createProducts($products,$from, $description=0) {
    $this->mode='product';
    $path=$this->_getPath($from);
    
     if(Tools::getValue('CSV_MULTI') && (int)Tools::getValue('CSV_MULTI')) {
       if((int)Tools::getValue('CSV_STARTAT') == 0 && $from <= 1) {
         $fp=fopen($path, "w+");
       }
       else {
         $fp=fopen($path, "a+");
       }
     } 
     else {
        $fp=fopen($path, "w+");
     
     } 
    
    
    
  
    if(!$fp) {
    die ("failed to open ".$path);  
    }
 
 
 $keys=$this->_getKeys();
if($description)    
    $this->_collumnDescription($fp);
    
    foreach ($products AS $product)
    { 
    $row=array();
  
    $categories=array();
    foreach($product['categories'] as $category) {
         $categories[] =$category['id_category'];
    }

     $product['categories']=implode(',', $categories);
     $images = Image::getImages(intval($this->id_lang), $product['id_product']); 
     $imageurls=array();
     
        if(file_exists(_PS_CLASS_DIR_.'Context.php') && class_exists("Context")) {
          $link=Context::getContext()->link; 
       }
       else   {
        global $link;
        if(! is_object($link))
         $link=new Link();
       } 
       
     foreach($images as $image) {
         if($this->imgdir =='' || empty($this->imgdir )) {
             
           $name=   strlen($product['link_rewrite']) > 2?$product['link_rewrite']:urlencode(str_replace(' ','-',$this->removeAccents($product['name'])));
          
            
             if(Tools::getValue('use_thickbox'))
             $url=$link->getImageLink($name, $image['id_product']."-".$image['id_image'], 'thickbox');
          else
           $url=$link->getImageLink($name, $image['id_product']."-".$image['id_image']); 
            
            
            $pos=strpos($url, _PS_BASE_URL_);
             if($pos === false) {
              $pos2=strpos($url, __PS_BASE_URI__);
              if($pos2 === 0) {
                $url=substr($url,$pos2+strlen(__PS_BASE_URI__));
              } 
             $url=_PS_BASE_URL_.__PS_BASE_URI__.$url;
             
             
             }
        
            $imageurls[]=$url;
         }
         else
           $imageurls[]=($this->imgdir.'/'.$image['id_product']."-".$image['id_image']).".jpg";
     } 
     if(is_array($imageurls)) {
     $product['imageurls']=implode(',', $imageurls);
     }  
     
     $product['tax_rate']=is_array($this->rates)?$this->rates[$product['tax_rate']]:$this->rates;
    
    $this->_writeData( $product, $fp);
  
    }

fclose($fp);   
         }
      
 
      
/*      
 public function createCategories($categories, $description) {  
 
$this->mode='category';
$path =$this->_getPath();

$fp=fopen($path, "w+");
if(!$fp) {
    die ("failed to open ".$path);  
}
if($description)    
    $this->_collumnDescription($fp);  
    $row=array();
   
   foreach($categories as $category) { 
       $this->_writeData( $category, $fp); 
   }  
   
   fclose($fp);  
  }
 */ 

  
      
 public function createItems($items, $mode, $description) {  
 
$this->mode=$mode;
$path =$this->_getPath();

$fp=fopen($path, "w+");
if(!$fp) {
    die ("failed to open ".$path);  
}
if($description)    
    $this->_collumnDescription($fp);  
    $row=array();
   
   foreach($items as $item) { 
       $this->_writeData( $item, $fp); 
   }  
   
   fclose($fp);  
  }
  
  
  private function   _writeData($array,  $fp) {
      
       $keys=$this->_getKeys();    
     $s='';
      
     $row=array();
       for($i=0; $i<count($keys); $i++) {
        
       if(isset($array[$keys[$i]]) ) {
            $row[$keys[$i]]=$array[$keys[$i]];
           
       }
       else {
          $row[$keys[$i]]=""; 
       }
    }  
    
       foreach($row as $item) {
       if(is_numeric($item)) {
          $s.='"'.$item.'";'; 
       }
       elseif(empty($item)) {
          $s.='"";'; 
       }
       else {
          $item=str_replace(array("\r\n", "\r", "\n", "\t"), ' ',  $item); 
          $item=str_replace('"', '""',   $item);  
          $s.='"'.$item.'";'; 
       }
    
    }
    
    $s=substr($s,0,strlen($s)-1). chr(10);
   
     
         fputs($fp,  $s);    
  
  }
  
  
  private function _getKeys() {
      switch($this->mode) {
         case 'category':
           return   array('id_category', 'active', 'name', 'id_parent',  'is_root_category', 'description', 'meta_title', 'meta_keywords', 'meta_description', 'link_rewrite');         
           case 'manufacturer':
           return   array('id_manufacturer', 'active', 'name', 'description',  'short_description', 'meta_title', 'meta_keywords', 'meta_description');  
             case 'supplier':
           return   array('id_supplier', 'active', 'name', 'description',  'meta_description', 'meta_title', 'meta_keywords');    
            case 'customer':
           return   array('id_customer', 'active', 'id_gender',  'email',  'passwd',  'birthday','lastname', 'firstname','newsletter','optin');    
         case 'address': // maji v ni bordel
           return   array('id_address', 'alias', 'active', 'email',  'id_manufacturer', 'id_supplier', 
           'company',  'lastname',  'firstname', 'address1', 'address2',  'postcode',  'city',  
           'id_country',    'id_state', 'other', 'phone', 'phone_mobile', 'vat_number');        
        case 'product':
           return   array('id_product', 'active','name', 'categories',  'price',  'tax_rate',  'wholesale_price',   'on_sale', 'reduction_amount', 'reduction_percent','reduction_from','reduction_to',   'reference',  'supplier_reference',    'id_supplier',   'id_manufacturer',    'ean13', 'upc', 'ecotax',  'weight', 'quantity','description_short',   'description',  'tags',  'meta_title',    'meta_keywords', 'meta_description',      'link_rewrite', 'instocktext','backordertext','imageurls','features');
 
      }
      
  }
  
  
  private function _collumnDescription($fp) {
     $keys=$this->_getKeys();
     $arr=array();
     while(list($key,$val)=each($keys)) {
        $arr[$val]=$val;   
     }
     $this->_writeData($arr, $fp); 
  }
  
  private function _getPath($from=null) {
      
       $path =$this->mode.'.csv';
       
       if(Tools::getValue('CSV_MULTI') && (int)Tools::getValue('CSV_MULTI'))
         return $this->feeddir."/".$path;   
      
       if($from && is_numeric($from))
           $path =sprintf('%03d',$from).'_'.$path;

       $path =$this->feeddir."/".$path;     
           
            if(file_exists($path))
            unlink($path);
            return $path;
  }

  
  private function  removeAccents($string) {
    if ( !preg_match('/[\x80-\xff]/', $string) )
        return $string;

    $chars = array(
    // Decompositions for Latin-1 Supplement
    chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
    chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
    chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
    chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
    chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
    chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
    chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
    chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
    chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
    chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
    chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
    chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
    chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
    chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
    chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
    chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
    chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
    chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
    chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
    chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
    chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
    chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
    chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
    chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
    chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
    chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
    chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
    chr(195).chr(191) => 'y',
    // Decompositions for Latin Extended-A
    chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
    chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
    chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
    chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
    chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
    chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
    chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
    chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
    chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
    chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
    chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
    chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
    chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
    chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
    chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
    chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
    chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
    chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
    chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
    chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
    chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
    chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
    chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
    chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
    chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
    chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
    chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
    chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
    chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
    chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
    chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
    chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
    chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
    chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
    chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
    chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
    chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
    chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
    chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
    chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
    chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
    chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
    chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
    chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
    chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
    chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
    chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
    chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
    chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
    chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
    chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
    chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
    chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
    chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
    chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
    chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
    chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
    chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
    chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
    chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
    chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
    chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
    chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
    chr(197).chr(190) => 'z', chr(197).chr(191) => 's'
    );

    $string = strtr($string, $chars);

    return $string;
}

  }
?>
