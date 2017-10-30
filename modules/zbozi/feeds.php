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
 
define("DEBUG", 0);  
if(defined("DEBUG") && DEBUG == 1) {
  error_reporting('E_ALL');
  ini_set('display_errors', '1');
  register_shutdown_function( "fatal_handler" );
}
function fatal_handler() {
  if (($error = error_get_last())) {
   ob_clean();
   $s = json_encode($error);
   header("Location:  error.php?error=".$s);
  }
}
require_once(dirname(__FILE__).'/../../config/config.inc.php'); 
require_once(dirname(__FILE__).'/../../init.php');
require_once("./classes/cFeed.php");
require_once("./classes/FeedLocal.php");
require_once(_PS_MODULE_DIR_.'zbozi/zbozi.php');



 
/**
*  prvni potencialni funkce aplikovana na vystup z database
*  vhodna pokud je cestina v databasi ulozena jako html entity 
*/
 
define('FILTER_CATEGORIES', 0); // 1 pro netypicke nastaveni multishop
 
 
 
$step=200;
$starttime=time();
set_time_limit (600); 
$timelimit = ini_get("max_execution_time");
$podil = (int)Configuration::get('ZBOZI_PODIL');
if($podil < 4 || $podil > 30)
  $podil = 10; 
$runtime = $timelimit-(int)$timelimit/10 -1;
 


$total=Configuration::get('ZBOZI_PERPASS');

if(Configuration::get('ZBOZI_HASH') && strlen(Configuration::get('ZBOZI_HASH'))) {
  $required =   Configuration::get('ZBOZI_HASH');
  $found = $_GET['hash'];
  if(! ($required == $found)) {
      echo ' CHYBA v nastavení modulu máte zadaný hash, ten tedy musí být vložen i do url spouštěcího skriptu  ';
      die();
  }
}

if(!(int)$total)
  $total=1000;
  
  if($total < 500) 
    $step = 50;
  elseif($total < 1000)
    $step = 100;
  else 
    $step = 200;
  
  
  
if($step > $total)
  $step=$total;
 


register_shutdown_function('processEnd');
$lockfile= _PS_MODULE_DIR_.'zbozi/Skkdj'._COOKIE_IV_;
if(!$lock=acquireLock($lockfile)) {
  echo 'existuje lock soubor '.$lockfile.' mladší než jednu hodinu. Pravděpodobně to znamená že běží další
  instance tohoto skriptu.';
}

/**
* druha potencialni funkce odstrani html tagy jeste pred pripadnym kodovanim entit
* 
*/
 
define("DEF_AVAILABLE_LATER", 10);  // při zapnutém skladu pro zboží které není skladem



 
define("ZIP_FILE", 1); 
 
 if(DEBUG == 1) {
   ob_start();
 
 }  

 require_once(dirname(__FILE__).'/zbozi.php');   
$c= new Zbozi();

 
 // 
 $id_lang=(isset($_GET['id_lang']) && (int)$_GET['id_lang'])?(int)$_GET['id_lang']:Zbozi::getDefaultLang();
 


Context::getContext()->language->id=$id_lang;
Context::getContext()->currency->id=Configuration::get('PS_CURRENCY_DEFAULT');

$sql ='SELECT iso_code FROM '._DB_PREFIX_.'lang WHERE id_lang ='.$id_lang;
$iso = Db::getInstance()->getValue($sql);

$group = Configuration::get('ZBOZI_GROUP')?Configuration::get('ZBOZI_GROUP'):Configuration::get('PS_CUSTOMER_GROUP');
if(!$group) {
   $groups = Group::getGroups(Configuration::get('PS_LANG_DEFAULT'));
   $group = $groups[0]['id_group'];   
}
Context::getContext()->customer->id_default_group = (int)$group;

switch($iso) {
	case 'cs': {
        $selcountry = array('CZ', 16, '11000');
	}; break;
	case 'sk': {
        $selcountry = array('SK', 37, '85110');
	}; break;
	case 'en': {
      $selcountry = array('GB', 17, 'SW1W0NY');
	}; break;
	default: {
	   $selcountry = array('CZ', 16, '11000');
	}
}

$sql ='SELECT id_country FROM '._DB_PREFIX_.'country WHERE iso_code = "'.pSQL($selcountry[0]).'"';
$id_country = Db::getInstance()->getValue($sql);
if((int)$id_country)
    Context::getContext()->customer->geoloc_id_country = $id_country;
else
    Context::getContext()->customer->geoloc_id_country = (int)$selcountry[1];

Context::getContext()->customer->postcode = $selcountry[2];
Context::getContext()->customer->id_state = null;
        

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

// todo allow attributes transformation for ps 1.4 (ZBOZI_TRANSFORMED heureka)   
if(file_exists(dirname(__FILE__).'/ZboziAttributes.php')  && zbozi::version_compare(_PS_VERSION_, '1.5', '>') ) {
    $do_attribudes=1;
    require_once(dirname(__FILE__).'/ZboziAttributes.php');
}
else {
    $do_attribudes=0;
    require_once(dirname(__FILE__).'/cMapMini.php');
}

$ff=$c->GetSetting("feeds");
$feeddir= '../../'.$c->GetSetting("feeddir"); 
 if(!is_dir($feeddir)) {
     mkdir($feeddir);
 }  
 
$tmps=array();
$nextround=(int)Configuration::get('ZBOZI_NEXTROUND')*60;
 $state=Configuration::get('ZBOZI_CURRENT_STATE');
foreach($ff as $f) {
$test=Configuration::get('ZBOZI_'.strtoupper($f));
if($test) {
  $feedpath=$feeddir.'/'.cFeed::addShopName().'zbozi_'.$f.'.xml'; 
  $diff=file_exists($feedpath)?($starttime - filemtime($feedpath)):0;
  
   if(file_exists($feedpath) &&  $diff < $nextround && (int)DEBUG != 1) {
    echo 'Detekován dokončený feed '.$feedpath.' který není starší než '.(int)Configuration::get('ZBOZI_NEXTROUND').' minut, <b>exiting</b>';
    exit;
   }
   if($state=='continue' && !file_exists($feeddir.'/'.cFeed::addShopName().'zbozi_'.$f.'.xml.tmp')) {
      $state='start'; 
   }
  $feeds[]=$f;  
}
}


if($state=='end' || !$state)
$state='start';



$heurekaTree=array();
if(feedActive('heureka', $feeds)) {
	  $map=new cMap('heureka');
	  $heurekaTree=$map->buildTaxonomyTree($state, $id_lang);
}

$googleTree=array();
if(feedActive('google', $feeds)) {
	  $map=new cMap('google');
	  $googleTree=$map->buildTaxonomyTree($state, $id_lang);
}
$zboziTree = array(); 
if(feedActive('seznam', $feeds)) {
	  $map=new cMap('zbozi');
	  $zboziTree=$map->buildTaxonomyTree($state, $id_lang);
}
$glamiTree=array();
if(feedActive('glami', $feeds)) {
      $map=new cMap('glami');
      $glamiTree=$map->buildTaxonomyTree($state, $id_lang);
} 

$catTree=array();
$cache_path = Zbozi::getCachePath('cats');

if($state == 'start') {
if(zbozi::version_compare(_PS_VERSION_, '1.5.0', '<')) {
 $sql='SELECT MAX(level_depth) FROM '._DB_PREFIX_.'category c';
 $maxLevel= Db::getInstance()->getValue($sql);
 define('START_CATEGORY_LEVEL', 1);
 $root_category=(int)Context::getContext()->shop->getCategory();
}
else {     
$sql='SELECT MAX(level_depth) FROM '._DB_PREFIX_.'category c LEFT JOIN  '._DB_PREFIX_.'category_shop cs ON
    c.id_category=cs.id_category 
      WHERE cs.id_shop='.$id_shop ; 
 $maxLevel= Db::getInstance()->getValue($sql);      
 $sql='SELECT  level_depth FROM '._DB_PREFIX_.'category c WHERE 
       c.is_root_category = 1 AND c.id_category='.(int)Context::getContext()->shop->id_category;
 $start_level = Db::getInstance()->getRow($sql); 
 if(!(int)$start_level['level_depth']) {
   $root_category=2;
   define('START_CATEGORY_LEVEL', 2); 
 }
 else  {
        $root_category=(int)Context::getContext()->shop->id_category;
        define('START_CATEGORY_LEVEL', ++$start_level['level_depth']); 
 }
}
 

if($maxLevel > 6)
 $maxLevel=6;    
getCategoryTree(START_CATEGORY_LEVEL, $maxLevel, $catTree,'', $root_category);
 file_put_contents($cache_path, json_encode($catTree));
}
else
$catTree =json_decode(file_get_contents($cache_path), true);



 
$forbidden_cats=array();
$zs=Configuration::get('ZBOZI_CATS_FORBIDDEN');
if(strlen($zs)) {
  $a=explode(',',$zs);
  foreach($a as $id_category) {
    if((int) $id_category > 0)
    $forbidden_cats[]=$id_category;
  }     
}

foreach($feeds as $feed) {

$classname= "Feed".ucfirst($feed);

    if(file_exists($classname.".php")) {
     require_once($classname.".php");
     $Feed=new  $classname;
     $Feed->initFeed( $feeddir, $state); 
    
    } 
}

   if($state == 'start') {
      $state='continue'; 
      Configuration::updateValue('ZBOZI_START', 0); 
      Configuration::updateValue('ZBOZI_UNIQUE_ID', 0); 
      if(Configuration::get("ZBOZI_DAREK_HEUREKA")) {
          require_once(_PS_MODULE_DIR_.'/zbozi/classes/Darek.php');
          $Darek = new Darek($id_lang);
          $Darek->resetCache();
	  }
	  
	  if(Configuration::get("ZBOZI_ACCESSORY_HEUREKA")) {
          require_once(_PS_MODULE_DIR_.'/zbozi/classes/Accessory.php');
          $Accessory = new Accessory();
          $Accessory->resetCache();
	  }
     }

$offset=(int)Configuration::get('ZBOZI_START');
$uniqueId=(int)Configuration::get('ZBOZI_UNIQUE_ID');

$counter=0;


 
$unitprice = Configuration::get('ZBOZI_UNITPRICE');

for($start=$offset; $start < ($total + $offset); $start+=$step) {
    
  
 if(time() - $starttime  >= $runtime) {
   exit;
 } 
 
    
	if(isset($_GET['id_product']) && (int)$_GET['id_product'])  
	  $products =getDebugProduct($id_lang,         (int)$_GET['id_product']);
	else
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
 
                
        if($product['available_for_order'] == 0)
              unset($products[$key] );
        
        elseif(in_array($product[$product_default_category], $forbidden_cats) && !(int)Configuration::get('ZBOZI_CATS_FORBIDDEN_REVERSE')) {
        	  unset($products[$key] );
		}
		elseif((int)Configuration::get('ZBOZI_CATS_FORBIDDEN_REVERSE') && !in_array($product[$product_default_category], $forbidden_cats)) {
        	  unset($products[$key] );
		}
        elseif(Shop::isFeatureActive() && (int)FILTER_CATEGORIES == 1 && !verifyCategoryShop(Shop::getContextShopID(true) ,  $product[$product_default_category])){
           unset($products[$key] );
        }
        else {
         $product['quantity'] = Product::getQuantity($product['id_product']);
        if(Configuration::get('ZBOZI_SKLADEM') == 2) {
        	$product['out_of_stock'] = StockAvailable::outOfStock($product['id_product'], $id_shop);
		}
        if($do_attribudes) { 
           $product['attributes'] =ZboziAttributes::getProductAttributes($product['id_product']);// Product::getProductAttributesIds($product['id_product']);
           $product['features'] = ZboziAttributes::getProductFeatures($product['id_product']);
           $product['id_product_attribute']=getAttributeUsed($product['attributes']);
        }  
         $specific_price  =  array();
          
         $product['price'] = Product::getPriceStatic($product['id_product'], true, ((isset($product['id_product_attribute']) AND !empty($product['id_product_attribute'])) ? intval($product['id_product_attribute']) : NULL), 2, null, false, true, 1, false, null, null, null, $specific_price);
         $product['specific_price'] = $specific_price;
         
       if((float)$product['unit_price_ratio'] > 0 &&  $unitprice) {
                 $product['price'] = Tools::ps_round(($product['price'] /$product['unit_price_ratio']), 2);
        }
         

        
         $product['categorytext_seznam'] ='';
         $product['categorytext_heureka'] ='';
         $product['categorytext_glami'] ='';
        
         if(isset($catTree[$product[$product_default_category]])) {
         	 $product['categorytext_seznam'] = $catTree[$product[$product_default_category]];
             $product['categorytext_heureka'] = $catTree[$product[$product_default_category]]; 
             $product['categorytext_glami'] = $catTree[$product[$product_default_category]]; 
		 }
		 
		 
		 if(is_array($heurekaTree) && count($heurekaTree) && ! empty($heurekaTree[$product[$product_default_category]]) ) {
		  	     $product['categorytext_heureka'] = $heurekaTree[$product[$product_default_category]];  
		 }
		 if(is_array($googleTree) && count($googleTree) && ! empty($googleTree[$product[$product_default_category]]) ) {
		  	$product['categorytext_google'] = $googleTree[$product[$product_default_category]];  
		 }
		  
		 if(is_array($zboziTree) && count($zboziTree) && ! empty($zboziTree[$product[$product_default_category]]) ) {
		  	     $product['categorytext_seznam'] =  $zboziTree[$product[$product_default_category]];  
		 }
         if(is_array($glamiTree) && count($glamiTree) && ! empty($glamiTree[$product[$product_default_category]]) ) {  
          if((int)Configuration::get('ZBOZI_GLCATMODE') == 0)
              $product['categorytext_glami'] = $glamiTree[$product[$product_default_category]].' | '.$catTree[$product[$product_default_category]]; 
          else 
               $product['categorytext_glami'] = $glamiTree[$product[$product_default_category]]; 
         }
		  

		
         if((int)Configuration::get('ZBOZI_CATSPERPRODUCT') == 1) {
          $sql= "SELECT p.heureka_category, p.heureka_cpc, p.max_cpc, p.max_cpc_search,  p.videourl,p.productline, p.extramessage, s.skipfeeds, l.zbozi_text, l.heureka_text
           FROM "._DB_PREFIX_."product p 
           LEFT JOIN  "._DB_PREFIX_."product_shop s ON p.id_product = s.id_product AND s.id_shop=".(int)$id_shop."
           LEFT JOIN   "._DB_PREFIX_."product_lang l ON p.id_product = l.id_product AND l.id_lang=".(int)$id_lang." 
           
           WHERE p.id_product=".(int)$product['id_product'];
           
          $data=Db::getInstance()->getRow($sql);
         
           while(list($key, $val) = each($data)) {
           
              $product[$key] = (string)$val; 
          }
           $specific_category=$data['heureka_category'];
           if($specific_category && strlen($specific_category))
                    $product['categorytext_heureka']= $specific_category;
                    
           
         }
        }  
        $counter ++;
    }
 
reset ($feeds);    
foreach($feeds as $feed) {

$classname= "Feed".ucfirst($feed);

    if(file_exists($classname.".php")) {

     require_once($classname.".php");
     $Feed=new  $classname;
     $Feed->createFeed($products, $feeddir);  
     unset($Feed); 
    } 
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
reset ($feeds);
foreach($feeds as $feed) {
 echo 'finishing: '.$feed.'<br />';
$classname= "Feed".ucfirst($feed);

    if(file_exists($classname.".php")) {

     require_once($classname.".php");
     $Feed=new  $classname;
     $Feed->finishFeed( $feeddir);   
    } 
}
}
//Configuration::updateValue('ZBOZI_CURRENT_STATE', $state);


 

 


function  getCategoryTree($level, $maxlevel, &$catTree, $name='', $parent=2) {
    global $id_lang;
    global $id_shop;
    if($level > $maxlevel) {
      return;
    }
    if(zbozi::version_compare(_PS_VERSION_, '1.5.0', '<')) {
        $sql='SELECT cl.name,   c.`id_category`
         FROM 
        `'._DB_PREFIX_.'category` c LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
        ON c.id_category = cl.id_category
     
        WHERE cl.id_lang='.(int)$id_lang.' AND c.level_depth='.(int)$level.' 
        AND c.id_parent='.(int)$parent.' GROUP BY  c.`id_category`
        '; 
    }
    else {
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
    }
    
    $ct= Db::getInstance()->ExecuteS($sql);
    foreach($ct as $cat) {
        if($name == '')
           $catname=$cat['name'];
        else
        $catname=$name.' | '.$cat['name'];
      
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
        if(time() - filemtime($lockfile) < 3600)
          return false;
          
          Configuration::updateValue('ZBOZI_START', 0); 
          unlink($lockfile);    
    }
    $fp=fopen($lockfile, 'w+');
    fputs($fp, time(true));
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

Configuration::updateValue('ZBOZI_START', $offset+$counter); // zdroj chyb pokud dale nedobehne  
Configuration::updateValue('ZBOZI_UNIQUE_ID', $uniqueId); 
Configuration::updateValue('ZBOZI_CURRENT_STATE', $state);  
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
                      
function getDebugProduct($id_lang, $id_product) {
			
			$only_active = true;
			$sql = 'SELECT p.*, product_shop.*, pl.* , m.`name` AS manufacturer_name, s.`name` AS supplier_name
				FROM `'._DB_PREFIX_.'product` p
				'.Shop::addSqlAssociation('product', 'p').'
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` '.Shop::addSqlRestrictionOnLang('pl').')
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
				LEFT JOIN `'._DB_PREFIX_.'supplier` s ON (s.`id_supplier` = p.`id_supplier`)'.
				($id_category ? 'LEFT JOIN `'._DB_PREFIX_.'category_product` c ON (c.`id_product` = p.`id_product`)' : '').'
				WHERE pl.`id_lang` = '.(int)$id_lang.' AND p.id_product ='.(int)$id_product;
					
		$rq = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
		if ($order_by == 'price')
			Tools::orderbyPrice($rq, $order_way);

		foreach ($rq as &$row)
			$row = Product::getTaxesInformations($row);

		return ($rq);
}

