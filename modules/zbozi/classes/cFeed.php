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
  class cFeed {
      protected $imagetype;
      protected $descrition_field;
      protected $availability=0;
      protected $availability_later=10;
      protected $availability_mode=0;
      protected $stock_management=0;
      protected $cpc; // nazev pole ktere bude brano jako cena pro cpc
      protected $heureka_category;
      protected $cache;
      protected $cache_path;
      protected $jen_skladem;
      protected $visibility;
      protected $decimals = null;
      protected $optim=null;
      protected $carrierCache;
      protected $dostupnostCustom;
      protected $maxLen;
      protected $unitprice;
      protected $usedGroupnames;
      protected $available_date;
   
      public function __construct() {
       $config = Configuration::getMultiple(array('ZBOZI_IMG', 'ZBOZI_SKLADEM','ZBOZI_VISIBILITY','ZBOZI_DESCRIPTION',  'ZBOZI_AVAILABILITY','ZBOZI_AVAILABILITY_LATER',  'PS_STOCK_MANAGEMENT', 'ZBOZI_CPC', 'ZBOZI_AVAILABILITY_MODE', 'ZBOZI_HEUREKA_CATEGORY'));
       $this->imagetype=$config['ZBOZI_IMG'];
      
       $this->unitprice = Configuration::get('ZBOZI_UNITPRICE');
       if(empty($this->imagetype) )   
            $this->imagetype='medium';
            
         
       
       	   global $CurrencyTo;
       	   if(!is_null($CurrencyTo))
       	     $this->decimals = (int)$CurrencyTo->decimals * _PS_PRICE_DISPLAY_PRECISION_;
       	   else
             $this->decimals = (int)Context::getContext()->currency->decimals * _PS_PRICE_DISPLAY_PRECISION_;
           
      $this->descrition_field = $config['ZBOZI_DESCRIPTION'];  
      $this->availability = (int)$config['ZBOZI_AVAILABILITY'];   
      $this->availability_later = (int)$config['ZBOZI_AVAILABILITY_LATER'];    
      $this->stock_management = (int)$config['PS_STOCK_MANAGEMENT'];  
      $this->jen_skladem  = (int)$config['ZBOZI_SKLADEM']; 
      $this->visibility  = (int)$config['ZBOZI_VISIBILITY'];  
      $this->availability_mode= (int)$config['ZBOZI_AVAILABILITY_MODE'];     
      $this->cpc = $config['ZBOZI_CPC'];  
      $this->heureka_category  = $config['ZBOZI_HEUREKA_CATEGORY'];
      $this->feedname=self::addShopName().$this->feedname;
      $this->available_date = date('Y-m-d');
     
      $this->cache_path=_PS_MODULE_DIR_.'zbozi/cache/'.Context::getContext()->shop->id;
      
      $this->cache=$this->loadCache();   
      $optim=json_decode(Configuration::get('ZBOZI_OPTIM'), true);
      $this->optim=$this->clearOptim($optim);
      $this->carrierCache=array();
      
      $this->dostupnostCustom=json_decode(Configuration::get('ZBOZI_DOSTUPNOST_CUSTOM'), true);
      if(!is_array($this->dostupnostCustom) || !count($this->dostupnostCustom)) {
      	  $this->dostupnostCustom = false;
	  }
	  
	  $this->maxLen=  (int)Configuration::get('ZBOZI_DESCRIPTION_MAX');
	  if($this->maxLen < 100 ||  $this->maxLen > 5000)
	   $this->maxLen = 510; 
       
       $usedGroupnames = Configuration::get('ZBOZI_USED_GROUPNAMES'); 
       
      if($usedGroupnames && strlen($usedGroupnames)) {
         $arr = explode(',', $usedGroupnames);
         foreach($arr as $gid) {
             if(strlen($gid)) {
                  $this->usedGroupnames[$gid] = 1;
             }
         }  
      }
  }
  
 
      
   public function __destruct() {
       if(is_array($this->cache)) {
            if(file_exists($this->cache_path) && is_file($this->cache_path))
              unlink($this->cache_path);
             file_put_contents($this->cache_path, json_encode($this->cache));
      }
   }   
      
protected function getAvailability($product) {
        
          // respektuji rizeni skladu
          if($this->availability_mode == 0 || empty($this->availability_mode)) {
               if($this->stock_management) {
                     if(isset($product["quantity"]) && $product["quantity"] > 0) {
                        if(isset($product['available_now']))
                            return $this->parseAvailability($product['available_now'], 'available_now'); 
                        else
                          return (int) $this->availability;   
                     }
                     else {
                       if(!empty($product['available_date']) && $product['available_date'] > $this->available_date) { 
                         return  $product['available_date'];
                       }
                       elseif(isset($product['available_later'])) {
                         return $this->parseAvailability($product['available_later'], 'available_later');  
                       } 
                       else {
                       return (int) $this->availability_later; 
                       }
                     }
               }
                  else
                 return (int) $this->availability; 
             }
          // parsuje text 
          elseif($this->availability_mode==1) {  
          	   if(isset($product["quantity"]) && $product["quantity"] > 0) { 
                      return $this->parseAvailability($product['available_now'], 'available_now'); 
			   }
               else   {
                      return $this->parseAvailability($product['available_later'], 'available_later');   
			   }
          }
          elseif($this->availability_mode==2) {
          	  if(isset($product["quantity"]) && $product["quantity"] > 0) 
          	  	  return (int) $this->availability;   
          	  else
                 return (int) $this->availability_later; 
          }
   }
      
        
     

        
 protected function parseAvailability($text, $mode) {
    
     if($this->dostupnostCustom) {
     	  reset   ($this->dostupnostCustom);
     	   while(list($key, $val) = each( $this->dostupnostCustom)) {
     	   	   if($val[0] ==  $text) {
     	   	     return $val[1];
     	   	     
			   }
		   }
	 }
     
     
     $c=preg_replace('/[^0-9-]/', '', $text);
     $c=trim($c);
 
     $koef=1;
     $text=mb_strtolower($text, 'UTF-8');
     if(strpos($text, 'týdn') > 0) 
     $koef=7;
     if(strpos($text, 'týžd') > 0)
     $koef=7;
     if(strlen($c) && strpos($c, '-')) {
         return (int)$c*$koef;
     }
     
      
     
      
    $c=str_replace('-','', $c);
    
    if($c==24) // napr do 24 hodin
      return 0;
      
       
    if((int)$c == 0) {
    	$searches = array('tři'=>3, 'tří'=>3,   'dva'=>2, 'dvou'=>2,    'jeden'=>1, 'jednoho'=>1);
    	while(list($key, $val) = each($searches)) {
    		if(strpos($text, $key) === false) {
    			 ;// echo $text.'  '.$key.'<br />';
			}
			else {
    			$c = $val;
	      //     echo  $text.' '.$c.'!!!!!!!!!!<br />';
    			break;
			}
		}
	}
	
       
    if((int)($c) > 0 )
     return (int) $c*$koef;
    
    if($c===0)
       return (int) $c;
    
    
	 
      
    if($mode == 'available_now') {
  
     $pos=strpos($text, 'ihned');
      if($pos===0 || (int) $pos > 0)
        return 0;
        
     if($text  == 'skladem')
       return 0;
    return (int) $this->availability;     
    }    
    
     if($mode == 'available_later') {
  
     $pos=strpos($text, 'ihned');
      if($pos===0 || (int) $pos > 0)
        return 0;
        
     if($text  == 'skladem')
       return 0;
    return (int) $this->availability_later;     
    }    
     
    
 } 
 
public function initFeed($feeddir, $state) {
 $feedpath =$feeddir.'/'.$this->feedname.'.tmp';

if(file_exists($feedpath) && $state == 'start')
unlink($feedpath);
$fp=fopen($feedpath, "a+");
if(!$fp) {
  echo "failed to open ".$feedpath;  
}
if($state=='start')
   $this->StartFeed($fp);
fclose($fp); 
} 

public function finishFeed($feeddir) {
 $feedpath=$feeddir.'/'.$this->feedname; 
 $source=$feeddir.'/'.$this->feedname.'.tmp';
 if(file_exists($feedpath))
 unlink($feedpath);
 copy($source, $feedpath);
 unlink($source);
 
 $fp=fopen($feedpath, "a+");
  $this->CloseFeed($fp);
  
 
 if($fp)
   fclose($fp); 
  
  if(defined("ZIP_FILE") &&   ZIP_FILE == 1) {
      $this->toZip($feeddir, $this->feedname);
  }
}

public function toZip($feeddir, $feedname) {
	if (!extension_loaded('zip')) {
	  echo 'zip extension not loaded, skipping zip file ';
	  return;
	}
    $feedpath =$feeddir.'/'.$feedname;  
    $zip = new ZipArchive();
    if(file_exists($feedpath.'.zip'))
      unlink ($feedpath.'.zip');
      
    if ($zip->open($feedpath.'.zip', ZIPARCHIVE::CREATE)!==TRUE) {
        exit("cannot open <$filename>\n");
    }
   if( !$zip->addFile($feedpath,$feedname)) {
     echo 'failed to zip '.$feedname;   
   }
    $zip->close();                 
 }  
        
public function createFeed($products, $feeddir) {
  global $link;    
  global $id_lang; 
  global $feed;
   
 $feedpath =$feeddir.'/'.$this->feedname.'.tmp';
 $fp=fopen($feedpath, "a+");

 switch($feed) {
    case 'heureka': $skip = 0; break;
    case 'seznam': $skip = 1; break;
    case 'google': $skip = 2; break;
    case 'glami': $skip = 3; break;
 }
    foreach ($products AS $product)
    {  
     if($this->jen_skladem  == 1  &&  $product['quantity'] <=0)
       continue;
     if($this->visibility == 1 && (!($product['visibility'] == 'both' || $product['visibility'] =='catalog' )) )
     continue;
     
    if(isset($product['skipfeeds']) && isset($product['skipfeeds'][$skip]) && $product['skipfeeds'][$skip] == 1) { 
       continue;
    }   
     if($this->jen_skladem  == 2  &&  $product['quantity'] <=0) {
     	if($product['out_of_stock'] == 2) {
     	    if(Configuration::get('PS_ORDER_OUT_OF_STOCK') == 0)
     	      continue;
     	    
		}
		elseif($product['out_of_stock'] == 0) {  //  zakazat objednavky vzdy
			continue;
		} 
	 }
    
      
      $all_images = array();
       $cover=$this->getCoverUrl($product['id_product'], $id_lang, $product, $all_images); 
       $url=$link->getproductLink($product['id_product'], $product['link_rewrite'], Tools::getValue('id_category'));
       if(isset($product['attributes']) && $product['attributes'] && count($product['attributes']) 
      
       ) {
          $itemgroup=$this->getItemGroup($product, $url, $cover, $all_images); 
          if(strlen($itemgroup))
             fputs ($fp, $itemgroup); 
          else // kombinace vsechny vylouceny ... feed google 
             fputs ($fp, $this->createItem($product, $url, $cover, $all_images));   
      }
      else {
      if((float)$product['price'] > 0)
        fputs ($fp, $this->createItem($product, $url, $cover, $all_images));   
      }
       
       
      
  
    }
    fclose($fp); 

    }
  

  
 protected function compileOptimisedTag($feedname, $paramname, $vals) {
   if(isset($this->optim[$feedname][$paramname]) ){
       $opt= $this->optim[$feedname][$paramname];
        if(isset($opt['custom']['custom']))
            $vals['custom'] = $opt['custom']['custom'];
       $retval='';
       while(list($key,$val)=each($opt)) {
       	   if(isset($vals[$key]))
              $retval.= $vals[$key].' ';
       }
    
       
       return trim($retval);

   }
   return $vals['name'] .' '.$vals['manufacturer'] .' '.$vals['reference'];
 } 
    
   protected function getCombinationName($attributes) {
   
 $retval='';
 $filter = Configuration::get('ZBOZI_FILTERATR_'.$this->upname);
 if($filter) {
    $filterattr= json_decode(Configuration::get('ZBOZI_USEDATTR_'.$this->upname), true);
    foreach($attributes as $attribute)  {
        //$a   = $filterattr[$attribute[5]];
       switch($filterattr[$attribute[5]]) {
         case 0:
         $retval.=Configuration::get('ZBOZI_ATT_SEPARATOR').' '.$this->prepareString($attribute[0]).'  '.$this->prepareString($attribute[1]);  break;
          case 1:
         $retval.=Configuration::get('ZBOZI_ATT_SEPARATOR').' '.$this->prepareString($attribute[1]);  break;
         case 2:
          $retval.=''; break;
       } 
    }
 }
 else {
    foreach($attributes as $attribute) {
      $retval.=Configuration::get('ZBOZI_ATT_SEPARATOR').' '.$this->prepareString($attribute[0]).'  '.$this->prepareString($attribute[1]);  
    } 
 }
  return $retval;
 
 }
 
 // see Product.php :: getAnchor();
  protected function getCombinationUrl($attributes) {
     $retval='';
   
   
  
  foreach($attributes as $attribute)  {
         if(Configuration::get('ZBOZI_ATTR_IDS')  && $attribute[4]) {
             $retval.='/'.$attribute[4].Configuration::get('PS_ATTRIBUTE_ANCHOR_SEPARATOR').$attribute[2].Configuration::get('PS_ATTRIBUTE_ANCHOR_SEPARATOR').$attribute[3];       
         }
         else {
             $retval.='/'.$attribute[2].Configuration::get('PS_ATTRIBUTE_ANCHOR_SEPARATOR').$attribute[3]; 
         }
      
  }
 // $combination['group_name'].'-'.$combination['attribute_name'];
 return $retval;
     
 }  
  
 protected function getCoverUrl($id_product, $id_lang, $product, &$all_images) {
   global $link;    
           $images= $this->getImages(intval($id_lang),$id_product);
            
          if(Configuration::get('ZBOZI_MULTIPLE_IMAGES') || (isset($product['attributes']) && count($product['attributes']))) {
			foreach($images as $image) {
					$name=$this->toUrl(empty($image['legend'])?$product['name']:$image['legend']);
					if($image['cover']) {   
					$imgurl=$link->getImageLink($name, $product['id_product'].'-'.$image['id_image'], $this->imagetype); 
				}
				 
					$all_images[] = array('url'=> $link->getImageLink($name, $product['id_product'].'-'.$image['id_image'], $this->imagetype),   'id_product_attribute'=>(int)$image['id_product_attribute']);
				   
			}
         }
		  else {
				foreach($images as $image) {
				$name=$this->toUrl(empty($image['legend'])?$product['name']:$image['legend']);
				if($image['cover']) {   
				$imgurl=$link->getImageLink($name, $product['id_product'].'-'.$image['id_image'], $this->imagetype);
				break;
				}     
				} 
		  }
         
        if(empty($imgurl)) {
             $name=$this->toUrl(empty($images[0]['legend'])?$product['name']:$images[0]['legend']);
             if(isset($images[0] ))
                 $imgurl=$link->getImageLink($name, $product['id_product'].'-'.$images[0]['id_image'], $this->imagetype);
               else
                $imgurl=''; 
        }
       return $imgurl;
  }   
  
  	protected   function getImages($id_lang, $id_product)
	{
		$sql = 'SELECT i.id_image, i.id_product, i.cover,il.legend,ai.id_product_attribute
			FROM `'._DB_PREFIX_.'image` i
			LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image`)';

	 
			$sql .= ' LEFT JOIN `'._DB_PREFIX_.'product_attribute_image` ai ON (i.`id_image` = ai.`id_image`)';

		$sql .= ' WHERE i.`id_product` = '.(int)$id_product.' AND il.`id_lang` = '.(int)$id_lang .'
			ORDER BY i.`position` ASC';
		$retval = Db::getInstance()->executeS($sql);
		return $retval;
	} 
  
  protected function getDescription($product) {
     $key=$this->descrition_field=='description'?'description':'description_short';
     $s=$product[$key];
     if(mb_strlen($s, 'utf-8') > $this->maxLen) {
        $s=mb_substr($s,0,  $this->maxLen, 'utf-8'); 
     }
     return $s;
  }   
  
  
      protected function prepareString($s, $keep_some = false) {
             $keep_some = false; // html znacky odstranit
               // ze zakodovanych entit na tagy
               $s=html_entity_decode($s, null, 'UTF-8'); 
               $s = str_replace("\n\n", "\n", $s);
               $s = str_replace('>','> ', $s);
               $s = str_replace("  ", " ", $s);
              
             
               if($keep_some) {
                  $s=strip_tags($s, '<strong><em>' ); 
               //   $s = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $s); // remove style
                  $s = $this->closeUnclosedTags($s); 
               }
               else {
               $s=strip_tags($s);
               }
 
               $s=htmlspecialchars($s); 
                if($keep_some) {
                 $s = strtr($s, array(
                 '&lt;strong&gt;'=>'<strong>', 
                 '&lt;/strong&gt;'=>'</strong>',
                 '&lt;em&gt;'=>'<em>', 
                 '&lt;/em&gt;'=>'</em>') );
                }
           // august 2012 
           $s = preg_replace('/[\x00-\x1F]/', '', $s);
           return $s; 
        }
        
  
        
        private function closeUnclosedTags($unclosedString){ 
            // created by Adam Gundry, http://www.agbs.co.uk 
            preg_match_all("/<([^\/]\w*)>/", $closedString = $unclosedString, $tags); 
            for ($i=count($tags[1])-1;$i>=0;$i--){ 
            $tag = $tags[1][$i]; 
            $closed = substr_count($closedString, "</$tag>");
            $unclosed =  substr_count($closedString, "<$tag>");
            if ($closed < $unclosed) { 
                $closedString .= "</$tag>"; 
            }
            elseif($closed > $unclosed) { //give up
               $closedString = strip_tags($closedString); 
               return $closedString;
            }
            } 
            return $closedString; 
        } 

    
      protected function  getCategoryText($product) {
      global $id_lang; 
         $cats= Db::getInstance()->ExecuteS('
        SELECT '._DB_PREFIX_.'category_lang.name  
          
        FROM
          '._DB_PREFIX_.'category_product LEFT JOIN  '._DB_PREFIX_.'category_lang ON
        '._DB_PREFIX_.'category_product.id_category =  '._DB_PREFIX_.'category_lang.id_category
        LEFT JOIN  '._DB_PREFIX_.'category ON
        '._DB_PREFIX_.'category_product.id_category =  '._DB_PREFIX_.'category.id_category
        
            WHERE '._DB_PREFIX_.'category_product.id_product = '.intval($product["id_product"])
          .' AND    '._DB_PREFIX_.'category_lang.id_lang= '.$id_lang
           .' AND    '._DB_PREFIX_.'category.active= 1 ORDER BY  '._DB_PREFIX_.'category.level_depth ASC '
            ); 
      $retval="";
      foreach($cats as $cat) {
        $retval.=$cat["name"]." ";  
      }
     return $retval;
  } 
  
  protected function createTag($key, $value, $spacer ="\t\t\t" ){
        if( ($key == 'PRICE_VAT' ||  $key == 'PRICE' || $key=='g:price') && !is_null($this->decimals)) {
           $value = Tools::ps_round($value, $this->decimals); 
        }
        return "$spacer<$key>$value</$key>\n";  
  }
  
  
  protected function floatFromString($s) {
        $s=str_replace(',','.',$s);
        $s=(float)$s;
         return ($s);
  }
  

   protected function toUrl($s) {
            if(empty($s))
              return '';
             $s=$this->cs_utf2ascii($s);
             $s=strtolower($s);
             $s= preg_replace('~[^-a-z0-9_ ]+~', '', $s);
             return str_replace(" ", "-", $s);
       }
       
     protected function cs_utf2ascii($s) { 
        static $tbl = array("\xc3\xa1"=>"a","\xc3\xa4"=>"a","\xc4\x8d"=>"c","\xc4\x8f"=>"d","\xc3\xa9"=>"e","\xc4\x9b"=>"e","\xc3\xad"=>"i","\xc4\xbe"=>"l","\xc4\xba"=>"l","\xc5\x88"=>"n","\xc3\xb3"=>"o","\xc3\xb6"=>"o","\xc5\x91"=>"o","\xc3\xb4"=>"o","\xc5\x99"=>"r","\xc5\x95"=>"r","\xc5\xa1"=>"s","\xc5\xa5"=>"t","\xc3\xba"=>"u","\xc5\xaf"=>"u","\xc3\xbc"=>"u","\xc5\xb1"=>"u","\xc3\xbd"=>"y","\xc5\xbe"=>"z","\xc3\x81"=>"A","\xc3\x84"=>"A","\xc4\x8c"=>"C","\xc4\x8e"=>"D","\xc3\x89"=>"E","\xc4\x9a"=>"E","\xc3\x8d"=>"I","\xc4\xbd"=>"L","\xc4\xb9"=>"L","\xc5\x87"=>"N","\xc3\x93"=>"O","\xc3\x96"=>"O","\xc5\x90"=>"O","\xc3\x94"=>"O","\xc5\x98"=>"R","\xc5\x94"=>"R","\xc5\xa0"=>"S","\xc5\xa4"=>"T","\xc3\x9a"=>"U","\xc5\xae"=>"U","\xc3\x9c"=>"U","\xc5\xb0"=>"U","\xc3\x9d"=>"Y","\xc5\xbd"=>"Z"); 
        return strtr($s, $tbl); 
        }  
        
      protected function loadCache(){
         if(file_exists($this->cache_path)){
            $s=file_get_contents($this->cache_path);
            if(strlen($s))
              return json_decode($s,true); 
         }
         return array();
      } 
      
      
      private function clearOptim($optim) {
          $feednames=array('heureka', 'zbozi');
         $tagnames=array('productname', 'product');  
         $retval=array();
         foreach($feednames as $feedname) {
             foreach($tagnames as $tagname) {
                $retval[$feedname][$tagname] = $this->clearOptimRow($optim[$feedname][$tagname]);
             }
             
         }
          return $retval;
      }
      
      private function   clearOptimRow($optim) {
      $data=array();
        while(list($key,$val)=each($optim)) {
            if((int)$val['pouzit'] == 1) {
              $data[$key] =$val;  
            }
        }
        foreach ($data as $key => $row) {
             $sort[$key]  = $row['poradi'];
    
          }
    if(isset($sort) && is_array($sort))
      array_multisort($sort, SORT_ASC,  $data);
        return $data;
      }
      
      protected function mergeAvailability($combination, $product) {
            if(isset($product['available_now']))
              $combination['available_now']=$product['available_now'];
             if(isset($product['available_later']))
              $combination['available_later']=$product['available_later'];
              
              return $combination;
      }
      
      public static function addShopName($id_shop=null) {
      	  $retval='';
      	  if(is_null($id_shop))
      	  $id_shop =(int)Tools::getValue('id_shop');
      	  $shop='';
          if($id_shop && Context::getContext()->shop->isFeatureActive() ) {
          $sql='SELECT name FROM `'._DB_PREFIX_.'shop` WHERE id_shop='.(int)$id_shop;
          $shop=  Db::getInstance()->getValue($sql);
          if(Configuration::get('ZBOZI_CLEANURL')) {
             $shop = Tools::str2url($shop);
             if(Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL')) {
               $shop =   preg_replace('/[^a-zA-Z0-9\s\'\:\/\[\]\-]/', '', $shop);
             } 
          }
          $retval.=$shop.'_';
		  }
		  $retval.=self::addLangName($id_shop);
		  
       return $retval;
	  } 
	  
	  private static function addLangName($id_shop=null) {
	    $retval='';
	  	if( $id_lang =(int)Tools::getValue('id_lang')) {
          	   $sql='SELECT iso_code FROM `'._DB_PREFIX_.'lang` WHERE id_lang='.(int)$id_lang;
              $lang=  Db::getInstance()->getValue($sql);
           	$retval.=$lang.'_';    
	    }
	    return $retval;  
	  }
	  
	   protected function combinationCoverUrl($all_images, $id_product_attribute) {
       if(is_array($all_images)) {
         foreach($all_images as $imgurl) {
            if($imgurl['id_product_attribute'] == $id_product_attribute)
               return $imgurl['url'];  
	     }
       }
      
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
 
 
 protected function getReference($product, $combination = null) {
     
     if(!is_null($combination) && isset($combination['reference']) && strlen($combination['reference'])) {
          return $this->prepareString($combination['reference']);
     }
        
     
    if(isset($product['supplier_reference'])  && !empty($product['supplier_reference'])) {
        return $this->prepareString($product['supplier_reference']);
    }
    
    if (isset($product['reference'])  && !empty($product['reference'])) {
        return $this->prepareString($product['reference']);
    }

    return '';
     
 }
 
 protected function isValidEan($ean) {
      if(empty($ean))
        return false;
      $a = strlen($ean);  
      if(strlen($ean) < 8) // schvalne
        return false;
        
      return true;
     
 }
 
   protected function unique_item_id  ($x, $y) {
     if((int)$y == 0) {
        return $x;
     }
     return $x.'-'.$y; 
    }
  }
?>
