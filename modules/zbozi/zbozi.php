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

if (!defined('_PS_VERSION_'))
    exit;
 

class Zbozi extends Module
{

  protected $_html = '';
    public $_postErrors = array();
    public $feeds=array("seznam", "heureka", "google" , "glami" );
    public $feedsUsed=array();
    public $feeddir= 'xml';    
    public $availability=0; 
    public $availability_later=10;
    public $availability_mode =0;
    public $use_ssl = false;
    protected $text_fields=array('ZBOZI_IMG', 'ZBOZI_DESCRIPTION',
     'ZBOZI_CPC',  'ZBOZI_CATS_FORBIDDEN');
    protected $text_defaults=array('medium', 'description_short',  '', '','');
    protected $config=array();
    public $do_attributes;
    protected $carriers;
    protected $carriersG;
    protected $cods;
    private $currentTab =0;
    public $fancy = true;
    
 
 public function __construct()
    {     
        $this->version = 3.41;
          $this->name = 'zbozi';
          $this->do_attributes=0; 
          if(file_exists(dirname(__FILE__).'/ZboziAttributes.php')) {
               $this->do_attributes=1; 
          }
          if(!isset($this->context) || !is_object($this->context)) {
          $this->context = self::initContext14();
          }
        
        
        $this->tab = 'advertising_marketing';
                $this->author = 'PrestaHost.eu';
        
        $config = Configuration::getMultiple(array(
         'ZBOZI_SEZNAM', 'ZBOZI_HEUREKA', 
        'ZBOZI_GOOGLE', 'ZBOZI_GLAMI', 'ZBOZI_AVAILABILITY', 'ZBOZI_AVAILABILITY_LATER',
         'ZBOZI_IMG', 'ZBOZI_DESCRIPTION',   'ZBOZI_CPC', 'ZBOZI_AVAILABILITY_MODE', 'ZBOZI_FORBIDDEN', 'ZBOZI_CATS_FORBIDDEN', 'ZBOZI_CATS_FORBIDDEN_REVERSE', 'ZBOZI_ATTR_PUBLIC',
        'ZBOZI_SKLADEM'));
        $this->feedsUsed["seznam"]=empty($config['ZBOZI_SEZNAM'])?0:1; 
        $this->feedsUsed["heureka"]=empty($config['ZBOZI_HEUREKA'])?0:1;    
      
        $this->feedsUsed["google"]=empty($config['ZBOZI_GOOGLE'])?0:1;  
        $this->feedsUsed["glami"]=empty($config['ZBOZI_GLAMI'])?0:1;   
        $this->availability= intval($config['ZBOZI_AVAILABILITY']);
        if(isset($config['ZBOZI_AVAILABILITY_LATER']))
        $this->availability_later= intval($config['ZBOZI_AVAILABILITY_LATER']);
        
        $this->availability_mode= intval($config['ZBOZI_AVAILABILITY_MODE']);
      
      
       foreach($this->text_fields as $field) {
           $this->config[$field] = $config[$field];
       }
     

        parent::__construct();

        $this->displayName = 'Zboži';
        if($this->do_attributes)
             $this->displayName .=' s variantami produktů';     
        $this->description = 'Modul pro export zboží do služby  zbozi.cz a dalších';
        $this->confirmUninstall ='Odinstalovat ?';
        $val=0;
       
        foreach($this->feedsUsed as $feed) {
         $val+=$feed;   
        }
       
        if(!$val && self::version_compare(_PS_VERSION_, '1.5', '>') && Module::isEnabled($this->name))
        $this->warning[] = 'není nastavena tvorba  žádného feedu'; 
           
        $this->carriers=json_decode(Configuration::get('ZBOZI_CARRIERS'), true);
        $this->carriersG=json_decode(Configuration::get('ZBOZI_CARRIERSG'), true);
        $this->cods=json_decode(Configuration::get('ZBOZI_CARRIERSCOD'), true);
        $this->currentTab=(int)Tools::getValue('currentTab');
        $this->use_ssl = $this->use_ssl();
        
        $enabled = false;
        $ff=$this->GetSetting("feeds");
        foreach($ff as $f) {
        $test=Configuration::get('ZBOZI_'.strtoupper($f));
        if($test) { 
            $enabled = true;
            break;
        }
        }
        
        if(!$enabled) {
        $this->warning = $this->l('Není nastavena tvorba žádného feedu.');
        }
    }
    
   
    public function hookUpdateCarrier($params) {
      return $this->hookActionCarrierUpdate($params);  
    }
    public function hookActionCarrierUpdate($params)
{
    // Update the id for carrier 1
    
    $carriers=array();
    while(list($key,$val)=each($this->carriers)) {
         if ((int)($params['id_carrier']) == $key && (int)$params['carrier']->id) {
          $carriers[$params['carrier']->id] = $val;
          $cods=json_decode(Configuration::get('ZBOZI_CARRIERSCOD'), true);
          $val2 = $cods[$key];
          unset($cods[$key]);
          $cods[$params['carrier']->id] = $val2;
         }
         else
          $carriers[$key] = $val;
    }
    Configuration::updateValue('ZBOZI_CARRIERS', json_encode($carriers));
    Configuration::updateValue('ZBOZI_CARRIERSCOD', json_encode($cods));
      $carriers=array();
    while(list($key,$val)=each($this->carriersG)) {
         if ((int)($params['id_carrier']) == $key && (int)$params['carrier']->id)
          $carriers[$params['carrier']->id] = $val;
         else
          $carriers[$key] = $val;
    }
    Configuration::updateValue('ZBOZI_CARRIERSG', json_encode($carriers));
}    
    
public  function GetSetting($key) {
     switch($key) {
         case "feeds":  return $this->feeds; 
         case "feeddir": return $this->feeddir;
         case "avilability": return $this->availability;   
         case "avilability_later": return $this->availability_later;     
     }
       
    }

  public  function install()
    {   
 
   if(! $this->check_feeddir()) {
          return false;
   } 
   
   
     if(!$this->addSqlField('category_lang','heureka_category' )  ||
        !$this->addSqlField('category_lang','google_category' ) ||
        !$this->addSqlField('category_lang','zbozi_category' ) || 
        !$this->addSqlField('category_lang','glami_category' )  
     ) {
         $this->warning[]='Nepodařilo se přidat pole Heureka Category resp. Google Category, proto nebyly instalovány soubory pro práci s tímto polem';   
     }
     else {
         $this->consolidateSqlFields(); 
         
        
   
     }
  
    
    if(is_array($this->warning) && count($this->warning) ) {
       $protocol =implode("\n", $this->warning);    
        Configuration::updateValue('ZBOZI_PROTOCOL', $protocol);
    }
   if(! (int)Configuration::get('ZBOZI_PARTIAL_UNISTALL') == 1) {
        Configuration::updateValue(  'ZBOZI_SEZNAM', 1);
        Configuration::updateValue(  'ZBOZI_SEZNAM_FEED', 1);

        Configuration::updateValue(  'ZBOZI_HEUREKA', 1);
        Configuration::updateValue(  'ZBOZI_PERPASS', 10000);
        Configuration::updateValue(  'ZBOZI_NEXTROUND', 0);

        Configuration::updateValue(  'ZBOZI_PARTIAL_UNISTALL', 1);
        Configuration::updateValue(  'ZBOZI_ATT_SEPARATOR', ',');
        Configuration::updateValue(  'ZBOZI_GIUNI', 1);

        Configuration::updateValue('ZBOZI_GROUP',  Configuration::get('PS_CUSTOMER_GROUP')); 


        Configuration::updateValue(  'ZBOZI_MULTIPLE_IMAGES', 0);
        
        $use_html_purifier = Configuration::get('PS_USE_HTMLPURIFIER');
        Configuration::updateValue('PS_USE_HTMLPURIFIER', 0); 
        for($i=0; $i<count($this->text_fields); $i++) {
        Configuration::updateValue($this->text_fields[$i], $this->text_defaults[$i], true);    
        }
        Configuration::updateValue('PS_USE_HTMLPURIFIER', $use_html_purifier); 
        
        if(self::version_compare(_PS_VERSION_, '1.6.1', '>')) {
          Configuration::updateValue('ZBOZI_ATTR_IDS', 1);  
      }
      
        $optr=array('name'=>array('poradi'=>1,'pouzit'=>1), 
                  'manufacturer'=>array('poradi'=>2,'pouzit'=>1), 
                  'reference'=>array('poradi'=>3,'pouzit'=>1),
                  'ean'=>array('poradi'=>4,'pouzit'=>0),
                  'custom'=>array('poradi'=>5,'pouzit'=>0,'custom'=>''));
                  
       $optim['heureka']['productname']=$optr;
       $optim['zbozi']['productname']=$optr;
       $optim['heureka']['product']=$optr;
       $optim['zbozi']['product']=$optr;
       
       
       Configuration::updateValue('ZBOZI_OPTIM', json_encode($optim)); 
        
   }
        if (!parent::install())
            return false;  
      if(self::version_compare(_PS_VERSION_, '1.5', '<')) {
         $this->registerHook('updateCarrier');
         $this->registerHook('backOfficeHeader');
      
      } else  {    
      if(! $this->registerHook('actionCarrierUpdate'))
       return false;
      }
      
       
        $images=ImageType::getImagesTypes('products');
		 
			foreach($images as $image) {
		  if( $image['height'] > 300  && $image['height'] < 1000 &&  $image['width'] > 300 &&  $image['width'] < 1000  && !empty($image['name'])) {
		      Configuration::updateValue('ZBOZI_IMG', $image['name']);
		      return true; 
		  }
		}   
  
        return true;
   }

 
   
   public function uninstall()
    {
        
         for($i=0; $i<count($this->text_fields); $i++) {
             if (!Configuration::deleteByName($this->text_fields[$i]))
               return false; 
          }
   if((int)Configuration::get("ZBOZI_PARTIAL_UNISTALL") == 0) { 
       $this->removeSqlField('category_lang', "heureka_category");  
       $this->removeSqlField('category_lang', "google_category");  
       $this->removeSqlField('category_lang', "zbozi_category");  
       $this->removeSqlField('category_lang', "glami_category");        
   }          
                          
        if (!$this->unregisterHook('actionCarrierUpdate')  
            OR !parent::uninstall())
            return false;
   

      
    if((int)Configuration::get('ZBOZI_CATSPERPRODUCT') ==1 ) {
       $this->unistallExtendedProduct();  
     }       
        
    
    if(file_exists(_PS_ROOT_DIR_.'/cache/class_index.php'))
      unlink(_PS_ROOT_DIR_.'/cache/class_index.php');
    if((int)Configuration::get("ZBOZI_PARTIAL_UNISTALL") == 0) {  
    $confarr=array('ZBOZI_AVAILABILITY','ZBOZI_AVAILABILITY_MODE','ZBOZI_AVAILABILITY_LATER',
   'ZBOZI_CARRIERSCOD','ZBOZI_CURRENT_STATE',
    'ZBOZI_DOPRAVA_ON','ZBOZI_DOPRAVAG_ON',  'ZBOZI_CATS_FORBIDDEN', 'ZBOZI_CATS_FORBIDDEN_REVERSE', 'ZBOZI_ATTR_PUBLIC', 'ZBOZI_GATTRIBUTES','ZBOZI_GOOGLE','ZBOZI_HEUREKA', 'ZBOZI_NEXTROUND',
    'ZBOZI_OPTIM','ZBOZI_PARTIAL_UNISTALL','ZBOZI_PROTOCOL','ZBOZI_PERPASS','ZBOZI_ROUND_PRICES',
    'ZBOZI_SEZNAM', 'ZBOZI_SKLADEM','ZBOZI_SKLADzb','ZBOZI_START',    
    'ZBOZI_TRANSFORMED','ZBOZI_TRANSFORMED_COUNT',  'ZBOZI_TRANSFORMEDzb','ZBOZI_TRANSFORMED_COUNTzb','ZBOZI_UNIQUE_ID','ZBOZI_LOWESTCATEGORY','ZBOZI_CATS_FORBIDDENzb','ZBOZI_MULTIPLE_IMAGES',
     'ZBOZI_DESCRIPTION_MAX', 'ZBOZI_EXPORT', 'ZBOZI_START_EXP', 'ZBOZI_UNIQUE_ID_EXP', 'ZBOZI_CURRENT_STATE_EXP', 'ZBOZI_TEXT_EXT','ZBOZI_GIDENF','ZBOZI_DOSTUPNOST_CUSTOM', 'ZBOZI_IMG','ZBOZI_HEUREKA_SLEVA', 'ZBOZI_ACCESSORY_HEUREKA', 'ZBOZI_DAREK_HEUREKA', 'ZBOZI_ROUND_HEUREKA', 'ZBOZI_UNITPRICE', 
     'ZBOZI_ATTR_IDS',    'ZBOZI_ATT_SEPARATOR',
     'ZBOZI_SEZNAM_FEED', 'ZBOZI_GIUNI',   'ZBOZI_GROUP', 
     'ZBOZI_CATS_FORBIDDENgo','ZBOZI_ROUND_ZBOZI','ZBOZI_SEZNAM_SLEVA','ZBOZI_TEXT_EXTATT','ZBOZI_CATS_EROTIC','ZBOZI_GLATTRIBUTES','ZBOZI_DOPRAVAGL_ON','ZBOZI_CATS_FORBIDDENgl','ZBOZI_GLAMI','ZBOZI_GLNAME','ZBOZI_FILTERATR_HEUREKA','ZBOZI_USEDATTR_HEUREKA','ZBOZI_USEDATTR_SEZNAM','ZBOZI_FILTERATR_SEZNAM','ZBOZI_FILTERATR_GLAMI','ZBOZI_USEDATTR_GLAMI','ZBOZI_VISIBILITY' 
   ); 
   

   foreach($confarr as $conf) {
   	    Configuration::deleteByName($conf);
   }
   }

 return true;
    }
    
    
  
   
    
    protected function check_feeddir() {
      $dir="../".$this->feeddir;
      if(!is_dir($dir)) {
         mkdir($dir);
      } 
     if(!is_dir($dir)) {
         $this->_errors[]=$this->l('Důvod selhání: nepodařilo se vytvořit adresář ').$this->feeddir .'.';
         return 0;
     } 
     if(!is_writable($dir)) {
         chmod($dir, 0755);
     }
     if(!is_writable($dir)) {
                $this->_errors[]=$this->l('Důvod selhání: nelze zapisovat do adresáře ').$this->feeddir.'.';
            return 0;
     }
        return 1;
    /* chmod(
     $fp=fopen($dir."/test.txt", "w+");
     if(!
    */    
    }
    
 public function hookActionAdminProductsControllerSaveBefore($params) {
    $keys = array('extramessage'=>7,'skipfeeds'=>4);
   while(list($key,$to) = each($keys)) {
    $s = '';
 
    for($i = 0; $i <= $to; $i++) {
       if(isset($_POST[$key][$i])) {
          $s.= '1'; 
          unset($_POST[$key][$i]);
       }
       else {
          $s.= '0';  
       }
         
    }
    $_POST[$key] =   $s;
    }
 }
    
 public   function getContent()
    {   
        $this->fancy = $this->canUseFancy();
        if(self::version_compare(_PS_VERSION_, '1.4.9.9', '>') )
    	    $this->context->controller->addCSS($this->_path.'css/zbozi.css', 'all');
        $this->_html = '<h2>'.$this->displayName.'</h2>';
        if(Shop::isFeatureActive() && Shop::getContextShopID(true) === null) {
    		$this->_html = '<h2>'.$this->displayName.'</h2>';
    		$this->_html .='Používáte multishop. 
    		<b>Pro nastavení specifické pro konkrétní shop do něj přepněte.</b>';
    		
		}
        $this->_html .= '<br />';
      

       $module_url = $this->getModuleUrl();
       if(self::version_compare(_PS_VERSION_, '1.5.0', '<')) {
          $tabs=array('general','feeds', 'dostupnost','heureka','seznam','google','glami','export' );
          $tabnames=array('Základní nastavení','Feedy', 'Dostupnosti','Heureka','Zbozi','Google','Glami','Export eshopu' );
       }
       else {
	       $tabs=array('general','feeds', 'extended','dostupnost','heureka','seznam','google','glami','export', 'utils');
           $tabnames=array('Základní nastavení','Feedy','Rozšíření','Dostupnosti','Heureka','Zbozi','Google','Glami','Export eshopu',  'Utility');
       } 
        $this->_html .=  '<script language="JavaScript">
        <!--
        var currentTab='.(int)$this->currentTab.';    
        window.onload=function(){showTab(currentTab);}
          function showTab(num) {
              currentTab=num;
              var tabcount='. count($tabs).'
              for(i=0;i< tabcount;i++) {
                  var idtab= "#zbozitab"+i;
                  $(idtab).hide();
                   var navtab= "#navtab"+i;
                   $(navtab).removeClass("red"); 
              }
               var idtab= "#zbozitab"+num;
                $(idtab).show(); 
                 var navtab= "#navtab"+num;
                   $(navtab).addClass("red"); 
               return false;
          }
       
        function fbox(funkce, id_lang) {
  
        if(funkce) {
        var url = "'.$module_url.'/categorymap.php?function="+funkce+"&id_lang="+id_lang+"&id_shop='.Context::getContext()->shop->id.'"; 
        $.fancybox({
        type: "iframe",
        href: url,
        "width": "90%",
        "height": "75%",
        "autoScale" : false,
        "fitToView" :  false
        });
        }
        }
        //-->
        </script>';    
        
          require_once(_PS_MODULE_DIR_.$this->name.'/controllers/ZboziController.php'); 
          
        $this->_html.='<div id="navcontainer"><ul id="navlist">';
       $class='red';
        for($i=0; $i < count($tabs); $i ++) {
 
           $this->_html.='<li style="float:left;padding-right:20px;" ><a  class="'.$class.'" id=navtab'.$i.' href="#" onClick="showTab('.$i.'); return false;">'.$tabnames[$i].'</a></li>'; 
           $class='';
        }
        $this->_html.='</ul></div><div style="clear:left"></div><br /><br />';
        $display='block';    
      
$output ='';		
 $tabnum = 0;		
 foreach($tabs as $tab) {
      $classname=ucfirst($tab).'Controller';
     if(file_exists(_PS_MODULE_DIR_.$this->name.'/controllers/'.$classname.'.php')) {
        require_once(_PS_MODULE_DIR_.$this->name.'/controllers/'.$classname.'.php');
    	$controller = new $classname($this);
    	 $output.='<div id="zbozitab'.$tabnum.'" style="display:'.$display.'">';
    	if(Tools::isSubmit('cmd_'.$tab)) {
    	
    	  $output .= $controller->postProcess($this);	
		}
		 
		  $output .= $controller->getContent($tabnum++);
		 $output.='</div>'."\n";
           $display='none';
	 }
	}
    
    $error ='';
      
      
      if (sizeof($this->_postErrors))
                 foreach ($this->_postErrors AS $err)
                    $error .= '<div style="color:red">'. $err .'</div>';

        return $this->_html.$error.$output;
    }    
    
    
 public function hookBackOfficeHeader() {
     $path = $this->getModuleUrl().'/css/zbozi.css';
     $retval = '<link type="text/css" rel="stylesheet" href="'.$path.'" />';
     return $retval;
 }
    
public function installOverridesP($overrides) {
        $retval=true;
        foreach($overrides as $override) {
            
            if(file_exists($override['target']) && filesize($override['target']) > 86 ) {
                if(! $this->canOverride($override['target'], $override['source'])) {
                    $this->_postErrors[]='Soubor '.$override['target'].' již existuje a namůže být nahrazen, prosím upravte jej ručně';
                    return false;
                }     
            }
       
                if(!is_writable($override['targetdir'])) { 
                    $this->_postErrors[]='Adresář '.$override['targetdir'].' není zapisovatelný'; 
                    $retval=false; 
                }
                elseif(!copy($override['source'],$override['target'])) {
                    $this->_postErrors[]='Nepodařilo se překopírovat '.$override['source'].' do '.$override['target']; 
                    $retval=false; 
                }
            
        }
        return $retval;
}

// check if it is older version of module own override
private function canOverride($target, $source) {
  $filename = substr($target, strrpos($target, '/')+1);  
  if(in_array($filename, array('zbozi_text.tpl', 'heureka.tpl'))) 
     return true;
    
  if(crc32(file_get_contents($target)) == crc32(file_get_contents($source))) {
  	  return true;
  }

 
  return false;
}

	private function unistallOverrides($overrides) {
		foreach($overrides as $override) {
				if(file_exists($override['target'])) {
					if($this->canOverride($override['target'],$override['source'])) {
					unlink($override['target']);
				}
			}
		} 
	}

public function addSqlField($tablename, $columnname, $type = null) {
      $tablename=pSQL($tablename);
      $columnname=pSQL($columnname);
        $sql='SELECT column_name
                    FROM information_schema.columns 
                    WHERE table_schema = "'._DB_NAME_.'" 
                    AND table_name   = "'._DB_PREFIX_.$tablename.'"
                    AND column_name  = "'.$columnname.'"';
                    $column_exists=Db::getInstance()->getValue($sql);

                    if($column_exists == false && $type == null) {
                    $sql='SELECT collation_name
					 FROM information_schema.`COLUMNS` C
					  WHERE table_schema = "'._DB_NAME_.'" 
                    AND table_name   = "'._DB_PREFIX_.'category_lang"
                    AND column_name  = "name"';
                    $collation=Db::getInstance()->getValue($sql);
                    if(!$collation)
                      $collation='utf8_general_ci'; 



                    $sql='ALTER TABLE '._DB_PREFIX_.$tablename.' ADD '.$columnname.' VARCHAR (250) COLLATE '.$collation.' DEFAULT NULL';
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql);
                    } 
                    elseif($column_exists == false && $type == 'int') {
                    $sql='ALTER TABLE '._DB_PREFIX_.$tablename.' ADD '.$columnname.' int(10) unsigned DEFAULT NULL';
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql);
                    
					}


                    $sql='SELECT    '.$columnname.' FROM '._DB_PREFIX_.$tablename.'   WHERE 1 LIMIT 1';
                    $test=Db::getInstance()->Execute($sql); 


                    if(!$test) {
                     return false;
                    }
     return true;
}
	
private function removeSqlField($tablename, $columname) {  
           $tablename=pSQL($tablename);
            $columname=pSQL($columname);
  
                   $sql='SELECT column_name
                    FROM information_schema.columns 
                    WHERE table_schema = "'._DB_NAME_.'" 
                    AND table_name   = "'._DB_PREFIX_.$tablename.'"
                    AND column_name  = "'.$columname.'"';
                    $column_exists=Db::getInstance()->getValue($sql);
                
                    if($column_exists == $columname) {
                   $sql='ALTER TABLE '._DB_PREFIX_.$tablename.' DROP COLUMN '.$columname;
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql);
                    }
    
}


public function  unistallExtendedProduct() {
    if((int)$this->do_attributes == 0)
       return;
                   $path= _PS_ADMIN_DIR_.'/themes/default/template/controllers/products/associations.tpl';
                   
                if(file_exists($path)) {
                    $content=file_get_contents($path);
                    $content = str_replace("{include file=\"controllers/products/heureka.tpl\"}","", $content);
                   file_put_contents($path, $content);
                }
                $path= _PS_ADMIN_DIR_.'/themes/default/template/controllers/products/informations.tpl';
                   
                if(file_exists($path)) {
                    $content=file_get_contents($path);
                    $content = str_replace("{include file=\"controllers/products/zbozi_text.tpl\"}","", $content);
                   file_put_contents($path, $content);
                }
                
                if((int)Configuration::get("ZBOZI_PARTIAL_UNISTALL") == 0) {    
                    $this->removeSqlField('product','heureka_category');   
                    $this->removeSqlField('product_lang','zbozi_text'); 
                    $this->removeSqlField('product_lang','heureka_text'); 
                    $this->removeSqlField('product','videourl'); 
                    $this->removeSqlField('product','productline'); 
                    $this->removeSqlField('product','extramessage'); 
                    $this->removeSqlField('product_shop','skipfeeds'); 
                    $this->removeSqlField('product','skipfeeds');
                    $this->removeSqlField('product','heureka_cpc'); 
                    $this->removeSqlField('product','max_cpc'); 
                    $this->removeSqlField('product','max_cpc_search'); 
                    $this->removeSqlField('product_shop','lowest_category'); 
                    Configuration::updateValue('ZBOZI_LOWESTCATEGORY',0);    
                } 
                
                  $this->unistallOverrides(array(
                    0=>array('source'=>_PS_MODULE_DIR_.$this->name.'/install/Product.php',
                    'target'=>_PS_OVERRIDE_DIR_.'classes/Product.php',
                    'targetdir'=>_PS_OVERRIDE_DIR_.'classes/'),
                    1=>array('source'=>_PS_MODULE_DIR_.$this->name.'/install/heureka.tpl',
                    'target'=>_PS_ADMIN_DIR_.'/themes/default/template/controllers/products/heureka.tpl',
                    'targetdir'=>_PS_ADMIN_DIR_.'/themes/default/template/controllers/products/'),
                     2=>array('source'=>_PS_MODULE_DIR_.$this->name.'/install/zbozi_text.tpl',
                    'target'=>_PS_ADMIN_DIR_.'/themes/default/template/controllers/products/zbozi_text.tpl',
                    'targetdir'=>_PS_ADMIN_DIR_.'/themes/default/template/controllers/products/')
                    ));
                
               $this->uninstallModuleTab('AdminCpc', 'Cpc', 'AdminCatalog');     
                 if(file_exists(_PS_ROOT_DIR_.'/cache/class_index.php'))
                unlink(_PS_ROOT_DIR_.'/cache/class_index.php');           
                Configuration::updateValue('ZBOZI_CATSPERPRODUCT',0);
}




/**
* for upgrading from versions below 2.72
* move heureka_category and google_category to shop table
* 
*/
public function consolidateSqlFields($product = 0) {
     if($product) {
         $columnames= array('zbozi_text');
         $tablename='product';
         $tablelangname='product_lang'; 
     }
     else {
	     $columnames= array('heureka_category', 'google_category');
	     $tablename='category';
	     $tablelangname='category_lang';
     }
	 foreach($columnames as $columname) {
	  $sql='SELECT column_name
                    FROM information_schema.columns 
                    WHERE table_schema = "'._DB_NAME_.'" 
                    AND table_name   = "'._DB_PREFIX_.$tablename.'"
                    AND column_name  = "'.$columname.'"';
                    $column_exists=Db::getInstance()->getValue($sql);

                    if(!($column_exists == false)) {
                     if($product) {
                          $sql = 'SELECT id_product, zbozi_text FROM '._DB_PREFIX_.'product WHERE zbozi_text is not null';
                          $items= Db::getInstance()->executeS($sql);
                          foreach($items as $item) {
                             if(empty($item['zbozi_text'])) {
                                continue; 
                             }
                             $pos = strpos($item['zbozi_text'],'|');
                             if($pos === false) {
                                $sql ='UPDATE '._DB_PREFIX_.'product_lang SET heureka_text ="'.pSQL($item['zbozi_text']).'" WHERE id_product = '.(int)$item['id_product'].' AND id_lang='.(int)Context::getContext()->language->id;
                                Db::getInstance()->execute($sql); 
                             }
                             else {
                                 $a = explode('|', $item['zbozi_text']);   
                                 if(isset($a[0]) && strlen($a[0])) {
                                      $sql ='UPDATE '._DB_PREFIX_.'product_lang SET heureka_text ="'.pSQL($a[0]).'" WHERE id_product = '.(int)$item['id_product'].' AND id_lang='.(int)Context::getContext()->language->id;
                                      Db::getInstance()->execute($sql); 
                                  }
                                 if(isset($a[1]) && strlen($a[1])) {
                                      $sql ='UPDATE '._DB_PREFIX_.'product_lang SET zbozi_text ="'.pSQL($a[1]).'" WHERE id_product = '.(int)$item['id_product'].' AND id_lang='.(int)Context::getContext()->language->id;
                                      Db::getInstance()->execute($sql); 
                                 }
                             }
                          }
                           $this->removeSqlField($tablename, $columname);
                     }
                     else {
                      $sql='UPDATE '._DB_PREFIX_.$tablelangname.' s LEFT JOIN '._DB_PREFIX_.$tablename.' c ON
                       c.id_category=s.id_category   AND
                       s.id_lang='.self::getDefaultLang().'
                       SET s.'.$columname.'=c.'.$columname;
                       if(Db::getInstance()->execute($sql)) {
                       	   $this->removeSqlField($tablename, $columname);
					   }
                     }
                    }

	 }
}


public static function getDefaultLang() {
	  $id_lang=(int)Configuration::get('PS_LANG_DEFAULT');

if(!$id_lang) {
        $sql='SELECT id_lang FROM '._DB_PREFIX_.'_lang  WHERE iso_code="cs"';
       $id_lang = (int)Db::getInstance()->getValue($sql);
}
if(!$id_lang) {
        $sql='SELECT id_lang FROM '._DB_PREFIX_.'_lang  WHERE iso_code="sk"';
       $id_lang = (int)Db::getInstance()->getValue($sql);
}
return $id_lang;
}

public function getDefaultLangIso() {
	$id_lang=self::getDefaultLang();
	$sql='SELECT iso_code FROM  '._DB_PREFIX_.'lang  WHERE id_lang='.(int)$id_lang;
	return Db::getInstance()->getValue($sql);
}





public   function installModuleTab($tabClass, $tabName, $parentName)
{
  $sql='SELECT id_tab FROM '._DB_PREFIX_.'tab WHERE class_name="'.pSQL($tabClass).'"';
  $idTab=Db::getInstance()->getValue($sql);
   if($idTab){
     $this->messages[]='Tab already installed '.$tabName;
     return false;
  }
  
  $sql='SELECT id_tab FROM '._DB_PREFIX_.'tab WHERE class_name="'.pSQL($parentName).'"';
  $idTabParent=Db::getInstance()->getValue($sql);
  if(!$idTabParent ) {
   $this->messages[]='Failed to find parent tab '.$parentName;
     return false;
  }
  
  if(file_exists(_PS_MODULE_DIR_.$this->name.$tabName.'.gif'));   
  @copy(_PS_MODULE_DIR_.$this->name.'/'.$tabName.'.gif', _PS_IMG_DIR_.'t/'.$tabClass.'.gif');
  $tab = new Tab();
  $tabNames=array();
  foreach (Language::getLanguages(false) as $language) {
     $tabNames[$language['id_lang']] =$tabName; 
  }
  $tab->name = $tabNames;
  $tab->class_name = $tabClass;
  $tab->module = $this->name;
  $tab->id_parent = $idTabParent;
  if(!$tab->save()) {
    $this->messages[]='Failed save Tab '.implode(',',$tabNames);
     return false;
  }
 $sql='DELETE FROM '._DB_PREFIX_.'access WHERE id_tab='.(int)$tab->id;
 Db::getInstance()->execute($sql);   
  if(!Tab::initAccess($tab->id)) {
  $this->messages[]='Failed save init access '.implode(',',$tabNames);
  return false;
  } 
  return true;
} 


public function uninstallModuleTab($tabClass)
{
  $idTab = (int)Tab::getIdFromClassName($tabClass);
  if($idTab != 0)
  {
    $tab = new Tab($idTab);
    $tab->delete();
    return true;
  }
  return true; // true even on failed
} 

public function use_ssl() {
	if( Configuration::get('PS_SSL_ENABLED'))
	  return true;
	  
	if(!empty($_SERVER['HTTPS'])	&& Tools::strtolower($_SERVER['HTTPS']) != 'off')
	    return true;
	    
	return false;
			
}

public static function  getCachePath($path, $id_lang=null) {
if(is_null($id_lang)) {
 $id_lang ='';	
}
else
$id_lang ='_'.$id_lang;
if(Shop::isFeatureActive() && (int)Shop::getContextShopID(true)) 
return  dirname(__FILE__).'/cache/'.$path.'_'.Shop::getContextShopID(true).$id_lang ;
else 
return dirname(__FILE__).'/cache/'.$path.$id_lang;

 

}

public function getModuleUrl() {
        $module_url = $this->getBaseUrl().'modules/'.$this->name;
        
        return $module_url;
}

public function getBaseUrl() {
   if(self::version_compare(_PS_VERSION_, '1.5', '<')) { 
          $protocol = $this->use_ssl?'https://':'http://'; 
          $url =  Configuration::get('PS_SHOP_DOMAIN').__PS_BASE_URI__.'/';
          $module_url = $protocol.str_replace('//','/',$url);
        }
        else {
         if($_SERVER['HTTP_HOST'] == 'localhost:8080')
            $module_url='/';
        else {
          $sql='SELECT id_shop_url FROM '._DB_PREFIX_.'shop_url
           WHERE id_shop='.(int)Context::getContext()->shop->id.' AND active=1 AND main="1"';
          $shopUrl =new ShopUrl(Db::getInstance()->getValue($sql));
          if(!$shopUrl || empty($shopUrl)) {
             $sql='SELECT id_shop_url FROM '._DB_PREFIX_.'shop_url
           WHERE id_shop='.(int)Context::getContext()->shop->id.' AND active=1';
          $shopUrl =new ShopUrl(Db::getInstance()->getValue($sql));  
          }
          $module_url=  $shopUrl->getURL($this->use_ssl);
          
        }
        }
        return $module_url;  
    
}

  private function canUseFancy() {
  if(self::version_compare(_PS_VERSION_, '1.5.4', '<'))
        return false;
         
       $url = $this->getModuleUrl().'/categorymap.php';
       $curl = curl_init($url); 
       curl_setopt($curl, CURLOPT_HEADER, 1);
       curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
       curl_setopt($curl, CURLOPT_VERBOSE, 1);
       $response = curl_exec($curl);
       $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
       $header_string = substr($response, 0, $header_size);
       $header_rows = explode(PHP_EOL, $header_string);
       $header_rows = array_filter($header_rows, "trim");
       
       
           foreach ($header_rows as $key => $value)
        {
          
        
        $pos = strpos(strtolower($value), strtolower('X-Frame-Options'));
        if(!($pos === false)) {
          $pos2 =  strpos(strtolower($value), strtolower('DENY'));
            if($pos2) {
             
              return false;
            }
        }
       } 
      
       return true;
    }
    
     public static function initContext14() {
  
      if(class_exists('Context'))  {
           return Context::getContext();;
     } 
        
      require_once(_PS_MODULE_DIR_.'/zbozi/classes/1.4/Context.php');
      require_once(_PS_MODULE_DIR_.'/zbozi/classes/1.4/Shop.php');
      global $smarty, $cookie;
     
     Context::getContext()->cookie = $cookie;
     Context::getContext()->smarty = $smarty;   
     $shop = new Shop();
      Context::getContext()->shop = $shop;
     return Context::getContext();

  }
  
  

  
 public static function version_compare($v1, $v2, $operator = '<')
    {
        self::alignVersionNumber($v1, $v2);
        return version_compare($v1, $v2, $operator);
    }
    
        public static function alignVersionNumber(&$v1, &$v2)
    {
        $len1 = count(explode('.', trim($v1, '.')));
        $len2 = count(explode('.', trim($v2, '.')));
        $len = 0;
        $str = '';

        if ($len1 > $len2)
        {
            $len = $len1 - $len2;
            $str = &$v2;
        }
        else if ($len2 > $len1)
        {
            $len = $len2 - $len1;
            $str = &$v1;
        }

        for ($len; $len > 0; $len--)
            $str .= '.0';
    }
}
