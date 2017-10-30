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
 
 /**
 * export pro dodavatele
 */
 require_once(dirname(__FILE__).'/../../config/config.inc.php'); 
require_once(dirname(__FILE__).'/../../init.php');
require_once("./classes/cFeed.php");
require_once(_PS_MODULE_DIR_.'zbozi/zbozi.php');
/**
*  prvni potencialni funkce aplikovana na vystup z database
*  vhodna pokud je cestina v databasi ulozena jako html entity 
*/
 
define('FILTER_CATEGORIES', 0); // 1 pro netypicke nastaveni multishop
if($_SERVER['HTTP_HOST'] == 'localhost:8080xxx') {
define ("DEBUG", 1);
$step=10;
$total=40; 

}
else   {
define ("DEBUG", 0);
$step=200;
$total=Configuration::get('ZBOZI_PERPASS');
if(!(int)$total)
  $total=1000;
  
if($step > $total)
  $step=$total;
}

if(isset($_GET['new']) && $_GET['new'] == 1) {
	Configuration::updateValue('ZBOZI_START_EXP', 0); // zdroj chyb pokud dale nedobehne  
	Configuration::updateValue('ZBOZI_UNIQUE_ID_EXP', 0); 
	Configuration::updateValue('ZBOZI_CURRENT_STATE_EXP', 'start');  
}

$catAdd = isset($_GET['cat'])?urldecode($_GET['cat']).' > ':'';

register_shutdown_function('processEnd');
$lockfile= _PS_MODULE_DIR_.'zbozi/Skkdjexp'._COOKIE_IV_;
if(!$lock=acquireLock($lockfile)) {
  echo 'existuje lock soubor '.$lockfile.' mladší než jednu hodinu. Pravděpodobně to znamená že běží další
  instance tohoto skriptu.';
}
 

define("DEF_AVAILABLE_LATER", 10);  // při zapnutém skladu pro zboží které není skladem



 
define("ZIP_FILE", 1); 
 
 if(DEBUG == 1) {
   ob_start();
 
 }  


 $starttime=time();
 // 
 $id_lang=(isset($_GET['id_lang']) && (int)$_GET['id_lang'])?(int)$_GET['id_lang']:Zbozi::getDefaultLang();
 


Context::getContext()->language->id=$id_lang;
Context::getContext()->currency->id=Configuration::get('PS_CURRENCY_DEFAULT');

$sql ='SELECT iso_code FROM '._DB_PREFIX_.'lang WHERE id_lang ='.$id_lang;
$iso = Db::getInstance()->getValue($sql);
switch($iso) {
	case 'cs': {
		Context::getContext()->customer->geoloc_id_country = 16;
		Context::getContext()->customer->id_state = null;
		Context::getContext()->customer->postcode = '11000';
	}; break;
	case 'sk': {
	   Context::getContext()->customer->geoloc_id_country = 37;
		Context::getContext()->customer->id_state = null;
		Context::getContext()->customer->postcode = '85110';
	}; break;
	case 'en': {
	    Context::getContext()->customer->geoloc_id_country = 17;
		Context::getContext()->customer->id_state = null;
		Context::getContext()->customer->postcode = 'SW1W0NY';
	}; break;
	default: {
	     Context::getContext()->customer->geoloc_id_country = 16;
		Context::getContext()->customer->id_state = null;
		Context::getContext()->customer->postcode = '11000';
	}
}


$id_shop=0;
if((int)Tools::getValue('id_shop')) {
	Context::getContext()->shop->setContext(Shop::CONTEXT_SHOP, (int)Tools::getValue('id_shop') ); // not necessary?
	$id_shop=  (int)Tools::getValue('id_shop');
}
else {
	Context::getContext()->shop->setContext(Shop::CONTEXT_ALL);
	$id_shop =Context::getContext()->shop->id;
}

$customer = Context::getContext()->customer;
if (Validate::isLoadedObject($customer)){
     Context::getContext()->customer->id_default_group  = (int)Configuration::get('PS_UNIDENTIFIED_GROUP');
}		

$CurrencyTo=null;
if(isset($_GET['id_currency']) && (int)$_GET['id_currency']) {
	if($_GET['id_currency'] != (int)  Configuration::get('PS_CURRENCY_DEFAULT'))
	      $CurrencyTo = new  Currency((int)$_GET['id_currency']);
}


if(file_exists(dirname(__FILE__).'/ZboziAttributes.php')) {
    $do_attribudes=1;
    require_once(dirname(__FILE__).'/ZboziAttributes.php');
}
else {
    $do_attribudes=0;
    require_once(dirname(__FILE__).'/cMapMini.php');
}
require_once(dirname(__FILE__).'/zbozi.php');   
$c= new Zbozi();
 
 if(!$f = getFeedName($_GET['manufacturers'])) {
 	 die('no manufacturers specified');
 }
 
$feeddir= '../../'.$c->GetSetting("feeddir"); 
 if(!is_dir($feeddir)) {
     mkdir($feeddir);
 }  
 
$tmps=array();
$nextround=(int)Configuration::get('ZBOZI_NEXTROUND_EXP')*60;
 $state=Configuration::get('ZBOZI_CURRENT_STATE_EXP');
 

 if(isset($_GET['subclass']) && strlen($_GET['subclass'])) {
  $f.=$_GET['subclass']; 
 } 
   $feedpath=$feeddir.'/'.cFeed::addShopName().$f.'.xml';   
  $diff=file_exists($feedpath)?($starttime - filemtime($feedpath)):0;
  
   if(file_exists($feedpath) &&  $diff < $nextround && (int)DEBUG != 1) {
    echo 'Detekován dokončený feed '.$feedpath.' který není starší než '.(int)Configuration::get('ZBOZI_NEXTROUND_EXP').' minut, <b>exiting</b>';
    exit;
   }
   if($state=='continue' && !file_exists($feeddir.'/'.cFeed::addShopName().$f.'.xml.tmp')) {
      $state='start'; 
   }

if($state=='end' || !$state)
$state='start';



 



$catTree=array();

if(Shop::isFeatureActive() && (int)Shop::getContextShopID(true)) 
$cache_path=dirname(__FILE__).'/cache/cats_'.Shop::getContextShopID(true) ;
else 
$cache_path=dirname(__FILE__).'/cache/cats';

if($state == 'start') {
$sql='SELECT MAX(level_depth) FROM '._DB_PREFIX_.'category c LEFT JOIN  '._DB_PREFIX_.'category_shop cs ON
    c.id_category=cs.id_category 
      WHERE cs.id_shop='.$id_shop ;
 $maxLevel= Db::getInstance()->getValue($sql);

 $sql='SELECT  level_depth FROM '._DB_PREFIX_.'category c WHERE 
       c.is_root_category = 1 AND c.id_category='.(int)Context::getContext()->shop->id_category;
 $start_level = Db::getInstance()->getRow($sql);
 if(!(int)$start_level) {
   $root_category=2;
   define('START_CATEGORY_LEVEL', 2); 
 }
 else  {
 	 $root_category=(int)Context::getContext()->shop->id_category;
 	   define('START_CATEGORY_LEVEL', ++$start_level['level_depth']); 
 }


if($maxLevel > 6)
 $maxLevel=6;    
getCategoryTree(START_CATEGORY_LEVEL, $maxLevel, $catTree,'', $root_category);
 file_put_contents($cache_path, json_encode($catTree));
}
else
$catTree =json_decode(file_get_contents($cache_path), true);

$classname= "FeedExport";

if(isset($_GET['subclass']) && strlen($_GET['subclass']))
 $classname.=urldecode($_GET['subclass']);
 


    if(file_exists('./classes/'.$classname.".php")) {
     require_once('./classes/'.$classname.".php");
     $Feed=new  $classname;
     $Feed->initFeed( $feeddir, $state); 
	}
	else die("subclass nenalezen"); 
 

   if($state == 'start') {
      $state='continue'; 
      Configuration::updateValue('ZBOZI_START_EXP', 0); 
      Configuration::updateValue('ZBOZI_UNIQUE_ID_EXP', 0); 
     }

$offset=(int)Configuration::get('ZBOZI_START_EXP');
$uniqueId=(int)Configuration::get('ZBOZI_UNIQUE_ID_EXP');

$counter=0;
set_time_limit (600); 

 $manufacturers = getManufacturers($_GET['manufacturers']);
$sql = 'SELECT a.id_tax_rules_group, a.id_tax, b.rate FROM '._DB_PREFIX_.'tax_rule a LEFT JOIN '._DB_PREFIX_.'tax b ON
a.id_tax = b.id_tax WHERE a.id_country ='.(int)Configuration::get('PS_COUNTRY_DEFAULT');
$taxes = Db::getInstance()->executeS($sql);
$rates = array();
foreach($taxes as $tax) {
	$rates[$tax['id_tax_rules_group']] = $tax['rate'];
}

for($start=$offset; $start < ($total + $offset); $start+=$step) {
	 
    $products =Product::getProducts(      $id_lang,         $start,      $step,  'id_product', 'asc',      false,      true);
     if(count($products) < $step) {
         $state='end';
     }    
    foreach($products as $key=>&$product) {
        
        $product_default_category ='id_category_default';
			if( (int)Configuration::get('ZBOZI_LOWESTCATEGORY') == 1) {
				if(!is_null($product['lowest_category'])) {
			        $product_default_category = 'lowest_category';
				}
			} 
	   /* if($product['id_product'] == 4612) {
	      $a = 1;
		}  */
        if(count($manufacturers) && !in_array($product['id_manufacturer'], $manufacturers)) {
         	unset($products[$key] );
        }
        else {
         $product['quantity'] = Product::getQuantity($product['id_product']);
        
        if($do_attribudes) { 
           $product['attributes'] =ZboziAttributes::getProductAttributes($product['id_product']);// Product::getProductAttributesIds($product['id_product']);
           $product['features'] = ZboziAttributes::getProductFeatures($product['id_product']);
           $product['id_product_attribute']=getAttributeUsed($product['attributes']);
        }  
       $product['specific_price']  = SpecificPrice::getSpecificPrice(
			(int)$product['id_product'],
			$id_shop,
			Context::getContext()->currency->id,
			Context::getContext()->customer->geoloc_id_country,
			(int)Configuration::get('PS_CUSTOMER_GROUP'),
			0,
			$id_product_attribute,
			null,
			null,
			1
		);

   
        
         if(isset($catTree[$product[$product_default_category]])) {
         	 $product['categorytext'] = $catAdd.$catTree[$product[$product_default_category]];
		 }
		 
  
        }  
        $counter ++;
    }
 
  
 


    if(file_exists('./classes/'.$classname.".php")) {

     require_once('./classes/'.$classname.".php");
     $Feed=new  $classname;
     $Feed->createFeed($products, $feeddir);  
    
    } 
 
if(DEBUG == 1) {
    echo $start.': '.time()-$starttime.' sec  MEM:'.memory_get_usage()."\n";
    ob_flush();
    flush();
 } 
 unset($products);
 if(isset($_GET['id_product']) && (int)$_GET['id_product']) {
 $state='end';
  break;
 }
} 

  
if($state=='end') {
 
 
 echo 'finishing';
 
 
     $Feed->finishFeed( $feeddir);   
    
}
  unset($Feed);  
//Configuration::updateValue('ZBOZI_CURRENT_STATE_EXP', $state);


 function getFeedName($man) {
 	 
 	 $code = $man;
 	 $s = substr($man,4);
 	 $id_manufacturer = (int)$s;
 	 if($id_manufacturer){
 	  $sql = 'SELECT name FROM '._DB_PREFIX_.'manufacturer WHERE id_manufacturer = '.(int)$id_manufacturer;
      $name = Db::getInstance()->getValue($sql);
      if(empty($name))
        return false;
	 }
	 elseif($man == 'all') {
	 	 $name = $man;
	 }
	 else
	  return false;
        
      iconv("utf-8", "us-ascii//TRANSLIT", $name);    
      $name = str_replace(' ','-',$name);  
 	 
 	 $name.='_'.substr(md5($code),0,5); 
 	 
 	 return $name;
 }

 function getManufacturers($man) {
 	  $s = substr($man,4);
 	  $mans = explode('_',$man);
 	  $retval = array();
 	  
 	  if(is_array($mans)) {
 	  	  foreach($mans as $m)
 	  	    if((int)$m)
 	  	     $retval[] = (int)$m;
	  }
 	  
 	  return $retval;
 }


function  getCategoryTree($level, $maxlevel, &$catTree, $name='', $parent=2) {
    global $id_lang;
    global $id_shop;
    if($level > $maxlevel) {
      return;
    }
 
    $sql='SELECT cl.name,   c.`id_category`
         FROM 
        `'._DB_PREFIX_.'category` c LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
        ON c.id_category = cl.id_category
           LEFT JOIN `'._DB_PREFIX_.'category_shop` cs
        ON c.id_category =  cs.id_category
        WHERE cl.id_lang='.(int)$id_lang.' AND c.level_depth='.(int)$level.
        ' AND cs.id_shop='.(int)$id_shop .' 
        AND c.id_parent='.(int)$parent.' GROUP BY  c.`id_category`
        ';
  
    
    $ct= Db::getInstance()->ExecuteS($sql);
    foreach($ct as $cat) {
        if($name == '')
           $catname=$cat['name'];
        else
        $catname=$name.' > '.$cat['name'];
      
        $catTree[$cat['id_category']]= $catname;
        getCategoryTree($level+1,$maxlevel, $catTree, $catname, $cat['id_category']); 
    }
     
}


 
  function friendly_url($nadpis) {
    $prevodni_tabulka = Array(
  'ä'=>'a',
  'Ä'=>'A',
  'á'=>'a',
  'Á'=>'A',
  'à'=>'a',
  'À'=>'A',
  'ã'=>'a',
  'Ã'=>'A',
  'â'=>'a',
  'Â'=>'A',
  'č'=>'c',
  'Č'=>'C',
  'ć'=>'c',
  'Ć'=>'C',
  'ď'=>'d',
  'Ď'=>'D',
  'ě'=>'e',
  'Ě'=>'E',
  'é'=>'e',
  'É'=>'E',
  'ë'=>'e',
  'Ë'=>'E',
  'è'=>'e',
  'È'=>'E',
  'ê'=>'e',
  'Ê'=>'E',
  'í'=>'i',
  'Í'=>'I',
  'ï'=>'i',
  'Ï'=>'I',
  'ì'=>'i',
  'Ì'=>'I',
  'î'=>'i',
  'Î'=>'I',
  'ľ'=>'l',
  'Ľ'=>'L',
  'ĺ'=>'l',
  'Ĺ'=>'L',
  'ń'=>'n',
  'Ń'=>'N',
  'ň'=>'n',
  'Ň'=>'N',
  'ñ'=>'n',
  'Ñ'=>'N',
  'ó'=>'o',
  'Ó'=>'O',
  'ö'=>'o',
  'Ö'=>'O',
  'ô'=>'o',
  'Ô'=>'O',
  'ò'=>'o',
  'Ò'=>'O',
  'õ'=>'o',
  'Õ'=>'O',
  'ő'=>'o',
  'Ő'=>'O',
  'ř'=>'r',
  'Ř'=>'R',
  'ŕ'=>'r',
  'Ŕ'=>'R',
  'š'=>'s',
  'Š'=>'S',
  'ś'=>'s',
  'Ś'=>'S',
  'ť'=>'t',
  'Ť'=>'T',
  'ú'=>'u',
  'Ú'=>'U',
  'ů'=>'u',
  'Ů'=>'U',
  'ü'=>'u',
  'Ü'=>'U',
  'ù'=>'u',
  'Ù'=>'U',
  'ũ'=>'u',
  'Ũ'=>'U',
  'û'=>'u',
  'Û'=>'U',
  'ý'=>'y',
  'Ý'=>'Y',
  'ž'=>'z',
  'Ž'=>'Z',
  'ź'=>'z',
  'Ź'=>'Z'
  
);
    
    $nadpis =strtolower(( strtr($nadpis, $prevodni_tabulka)));   
    $url = $nadpis;
    $url=str_replace(' ', '_', $url);
    $url = preg_replace('~[^\\pL0-9_]+~u', '-', $url);
    $url = trim($url, "-");
    $url = preg_replace('~[^-a-z0-9_]+~', '', $url);
    return $url;
}

function verifyCategoryShop($id_shop, $id_category) {
     $sql='SELECT id_category FROM '._DB_PREFIX_.'category_shop WHERE id_shop='.(int)$id_shop .' AND id_category='.(int)$id_category;
     return  (bool)Db::getInstance()->getValue($sql);
       
}

function acquireLock($lockfile) {
    if(file_exists($lockfile)) {
        if(mktime() - filemtime($lockfile) < 3600)
          return false;
          
          Configuration::updateValue('ZBOZI_START_EXP', 0); 
          unlink($lockfile);    
    }
    $fp=fopen($lockfile, 'w+');
    fputs($fp, mktime(true));
    return true;
}

function processEnd() {
 global $offset;
 global $counter;
 global $uniqueId;
 global $state;
 global $lockfile;
 
 if(isset($_GET['id_product']) && (int)$_GET['id_product']) {
	if(file_exists($lockfile))
     unlink($lockfile); 
  return;
}

Configuration::updateValue('ZBOZI_START_EXP', $offset+$counter); // zdroj chyb pokud dale nedobehne  
Configuration::updateValue('ZBOZI_UNIQUE_ID_EXP', $uniqueId); 
Configuration::updateValue('ZBOZI_CURRENT_STATE_EXP', $state);  
if(file_exists($lockfile))
  unlink($lockfile);  
}

function feedActive($feedname, $feeds) {
	 while(list($key,$val)=each($feeds)) {
	 	if($feedname == $val)
	 	    return true; 
	 }
	 return false;
}

/**
* put your comment there...
* 
* @param mixed $attributes
* todo - case where all effects are != 0
*/
function getAttributeUsed($attributes) {
	foreach($attributes as $attribute) {
		if($attribute['price'] == 0)  {
		  return $attribute['id_product_attribute'];
		}
		
	}
	
}

function parse_cat_num($s) {
	if(strlen($s))
	$arr = explode('@', $s);
	
	if((int)$arr[1])
	  return (int)$arr[1];
	  
	return 0;
}

