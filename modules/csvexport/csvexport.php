<?php
 /*
* 2011 PrestaHost.cz 
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
*  @author PrestaHost.cz  <info@prestahost.cz>
*  @copyright  2007-2011 PrestaHost.cz
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

  class CsvExport extends Module
{
  
   private $config;
   private $id_lang;
   private $_postErrors;
   private $link;
   private $ExpertFunctions;
   protected  $defaultmax=30000;
   protected  $defaultstep=1000;
    
    public function __construct()
    {
       error_reporting(E_ALL);
       ini_set('display_errors', 1);

        
        $this->name = 'csvexport';
        $this->version = '0.3';
        $this->author = 'PrestaHost.cz';
        $this->need_instance = 0;
        if (version_compare(_PS_VERSION_, 1.4) >= 0)
            $this->tab = 'administration';
        else
            $this->tab = 'Products';
        parent::__construct();
        $this->displayName = $this->l('Export in CSV');
        $this->description = $this->l('Export your content in CSV format suitable for import to other Prestashop website.');
        
        $config = Configuration::getMultiple(array('CSV_STEP', 'CSV_MAX', 'CSV_RATE', 'CSV_FEEDDIR', 
        'CSV_IMGDIR'));
        $this->config=$config;
        
        
        if(file_exists(dirname(__FILE__).'/ExpertFunctions.php')) {
             require_once(dirname(__FILE__).'/ExpertFunctions.php');
             $this->ExpertFunctions=new ExpertFunctions($this);
      }
    }

  public  function install()
    {
        if (!parent::install())
            return false;
         Configuration::updateValue('CSV_STEP', 1000); 
         Configuration::updateValue('CSV_MAX',  10000);
         Configuration::updateValue('CSV_RATE',  1);
         Configuration::updateValue('CSV_FEEDDIR',  '../download');
         Configuration::updateValue('CSV_IMGDIR',  '');
         
        return true;
   }
   
 public  function uninstall()
    {
        if (!Configuration::deleteByName('CSV_STEP') OR !Configuration::deleteByName('CSV_MAX') 
            OR !Configuration::deleteByName('CSV_RATE') 
            OR !Configuration::deleteByName('CSV_FEEDDIR')
            OR !Configuration::deleteByName('CSV_IMGDIR')
            OR !Configuration::deleteByName('CSV_NEWSITE_SERVER')
            OR !Configuration::deleteByName('CSV_NEWSITE_DB')
            OR !Configuration::deleteByName('CSV_NEWSITE_PASSWD')
            OR !Configuration::deleteByName('CSV_NEWSITE_USER')
             OR !Configuration::deleteByName('CSV_NEWSITE_PREFIX')
             
               OR !Configuration::deleteByName('CSV_OLDSITE_SERVER')
            OR !Configuration::deleteByName('CSV_OLDSITE_DB')
            OR !Configuration::deleteByName('CSV_OLDSITE_PASSWD')
            OR !Configuration::deleteByName('CSV_OLDSITE_USER')
             OR !Configuration::deleteByName('CSV_OLDSITE_PREFIX')
             
      
            OR !parent::uninstall())
            return false;
        return true;
    }
   
 
function getContent()
    {
        $this->_html = '<h2>'.$this->displayName.'</h2>';

        if (!empty($_POST))
        {
            //$this->_postValidation();
            if (!sizeof($this->_postErrors))         
                $this->_postProcess();
            else
                foreach ($this->_postErrors AS $err)
                    $this->_html .= '<div class="alert error">'. $err .'</div>';
        }
        else
            $this->_html .= '<br />';

        $this->_displayCvsExport();
        $this->_displayForm();

        return $this->_html;
    } 
 
 
   
  private function _displayForm()
    {   
        $this->_html .=
        '<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
            <fieldset>
            <legend>'.$this->l('Settings').'</legend>';
       $this->_html.='<table><tr><td>'.$this->l('Separate products files').'</td><td>'.$this->l('Single file').'</td></tr>
       <tr><td>';
       $this->_html .= '<input type="radio"  value="0" name="CSV_MULTI"';
       if(Tools::getValue("CSV_MULTI") == 0)
        $this->_html.=' checked="checked"';
        
        $this->_html.= '/>' .$this->l('Products per csv file (1000 max)').'<input type="text" name="CSV_STEP" value="'.$this->config['CSV_STEP'].'"></td>';
        
        $this->_html .= '<td><input type="radio"  value="1" name="CSV_MULTI"';
       if(Tools::getValue("CSV_MULTI") == 1)
        $this->_html.=' checked="checked"';  
       $this->_html.= '/></td></tr><table>';  
       
       
      
             
 
        
     
        $this->_html .=$this->l('Max total products (1000 max)').'<input type="text" name="CSV_MAX" value="'.$this->config['CSV_MAX'].'"><br />';   
        $this->_html .=$this->l('Default tax rate').'<input type="text" name="CSV_RATE" value="'.$this->config['CSV_RATE'].'">
        '.$this->l('Tax id used in the target shop').'<br />';  
        $this->_html .=$this->l('Where to save csv files').'<input type="text" name="CSV_FEEDDIR" value="'.$this->config['CSV_FEEDDIR'].'"><br />'; 
          $this->_html .=$this->l('Product images dir').'<input type="text" name="CSV_IMGDIR" value="'.$this->config['CSV_IMGDIR'].'">
        '.$this->l('If left empty, the images will be downloaded via internet from the current website. This is usually preferable. <br />
        Eventually, specify path to a folder on the target shop (e.g. "../upload/p" and copy the images there. 
        This will work only if  the images are not placed into additonal subfolders. Do not 
        use this option if the source eshop uses the new  image storage system ').'<br /><br /><br />';  
        
         $thx='';
       if(Tools::getValue('use_thickbox'))
             $thx= " checked='checked'";   
       $this->_html .=$this->l('Use thickbox').'<input type="checkbox" name="use_thickbox" value="1"'.$thx.'><br /><br /><br />';
  
              
        $this->_html .=$this->l('Settings').'<input type="submit" name="btnSettings" value="'.$this->l('Save settings').'"><br />';     
        $this->_html .= '</fieldset>';   
         
        
       
         $this->_html .= '<br /><fieldset>';  
         $this->_html .= '<legend>'.$this->l('Create csv').'</legend>'; 
         $this->_html .='<input type="checkBox" name="chckDescription"';
         if(Tools::getValue('chckDescription')) {
              $this->_html .=' checked="checked"';
         }
         
         $this->_html .='>' .$this->l('Include column description').'<br /><br />';
         
          $this->_html .='
        <input class="button" type="submit" name="btnCategories" value="'.$this->l('Create categories').'"> 
         <br /><br />'; 
         
          $this->_html .='
         <input type="submit"  class="button"  name="btnManufacturers" value="'.$this->l('Create manufacturers').'"> 
         <br /><br />'; 
         
         $this->_html .='
         <input type="submit"  class="button"  name="btnSuppliers" value="'.$this->l('Create suppliers').'"> 
         <br /><br />';
         
           $this->_html .='
         <input type="submit"  class="button"  name="btnCustomers" value="'.$this->l('Create customers').'"> 
         <br /><br />';
         
         $this->_html .='
         <input type="submit"  class="button"  name="btnAddresses" value="'.$this->l('Create adresses').'"> 
         <br /><br />';
         
         $this->_html .='<input type="submit"  class="button"   name="btnProducts" value="'.$this->l('Create products').'">
         &nbsp;   &nbsp;  <input type="text" name="CSV_STARTAT" size=3 value="'.(int)Tools::getValue('CSV_STARTAT').'" />
         '.$this->l('Start at product');
         
         $this->_html .= $this->l("Sanitize invalid").'<input type="checkbox"  name="sanitise" value="1" ';
       if(Tools::getValue('sanitise'))
               $this->_html .= 'checked"checked"';
         
         $this->_html .='/>
         
         <br />
         use sanitizing if there are frequent  <i>failed to import  product</i> errors when back-importimg csv into prestashop
         
         <br /><br />'; 
         
        $this->_html .='</fieldset>';
        $this->_html .='<br /><br /><br /><fieldset><legend>'.$this->l('CSV files in ').$this->config['CSV_FEEDDIR'].'</legend>'.$this->_listFiles().'</fieldset>';
    
       
    
       
    
      
       $this->_html .='<br /><br />';
       
     if(is_object($this->ExpertFunctions)) {
       $this->_html.=$this->ExpertFunctions->displayForm();
      }

    
      $this->_html .=  '</form>';
    } 
   
   private function _listFiles() {
       $retval='';
    if(is_dir($this->config['CSV_FEEDDIR'])) {
        if ($dh = opendir($this->config['CSV_FEEDDIR'])) {
            while (($file = readdir($dh)) !== false) { 
            if($file !='.' && $file !='..'  && substr($file, strlen($file)-3) =='csv') {
                $kb=number_format(filesize($this->config['CSV_FEEDDIR'].'/'.$file) / 1024,2);
                $retval.= $file.' <small><i>'.date('d.m.Y H:i', filemtime($this->config['CSV_FEEDDIR'].'/'.$file)).' '.$kb.' kB</i></small><br />';
            }
            }
            closedir($dh);
        }
    }
    return $retval;
   }
   
   private function _displayCvsExport()
    {
        $this->_html .= '<img src="../modules/cvsexport/cvsexport.jpg" style="float:left; margin-right:15px;"><b>'.$this->displayName.'</b><br /><br />'.$this->description.'<br />
       česká podpora Prestashopu: <b><a href="http://www.prestahost.cz">PrestaHost.cz </a></b>  <br /> 
       <br /><br />';
    }
   
   
   
   private function _postProcess()
    {
        if (Tools::isSubmit('btnSettings') )
        {
        $feeddir= Tools::getValue('CSV_FEEDDIR');
      
        $step=Tools::getValue('CSV_STEP');
        if($step > $this->defaultstep) 
           $step=$this->defaultstep;
           
        $max=Tools::getValue('CSV_MAX');
        if($max > $this->defaultmax) 
           $max= $this->defaultmax;
           
        $rate=Tools::getValue('CSV_RATE');
      
        if(is_dir($feeddir) &&
         Configuration::updateValue('CSV_STEP', $step) &&
         Configuration::updateValue('CSV_MAX',  $max) &&
         Configuration::updateValue('CSV_RATE',  $rate) &&
         Configuration::updateValue('CSV_FEEDDIR', $feeddir) &&
         Configuration::updateValue('CSV_IMGDIR', Tools::getValue('CSV_IMGDIR'))&&
         Configuration::updateValue('CSV_MULTI', Tools::getValue('CSV_MULTI'))
          
         
        ) {
         $this->_html .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Setting saved').'" />'.$this->l('Setting saved').'</div>'; 
         $this->config['CSV_FEEDDIR']=$feeddir;
         $this->config['CSV_RATE']=$rate;
         $this->config['CSV_MAX']=$max;
         $this->config['CSV_STEP']=$step;
          $this->config['CSV_IMGDIR']=Tools::getValue('CSV_IMGDIR'); 
        }
        else  {
         $this->_html .= '<div class="conf confirm"><img src="../img/admin/forbidden.gif" alt="'.$this->l('Setting not saved').'" />'.$this->l('Setting not saved').'</div>';  
        }
        }
        elseif(Tools::isSubmit('btnCategories') ) {
             $this->csvCategories();
        }
          elseif(Tools::isSubmit('btnManufacturers') ) {
             $this->csvManufacturers();
        }
          elseif(Tools::isSubmit('btnSuppliers') ) {
             $this->csvSuppliers();
        }
          elseif(Tools::isSubmit('btnCustomers') ) {
             $this->csvCustomers();
        }
          elseif(Tools::isSubmit('btnAddresses') ) {
             $this->csvAdresses();
        }
         elseif(Tools::isSubmit('btnProducts') ) {
              $this->csvProducts();
        } 
        elseif(is_object($this->ExpertFunctions)) {
            if(Tools::isSubmit('btnDbSettings') ) {
               Configuration::updateValue('CSV_NEWSITE_SERVER', Tools::getValue('CSV_NEWSITE_SERVER'));
               Configuration::updateValue('CSV_NEWSITE_DB', Tools::getValue('CSV_NEWSITE_DB'));
               Configuration::updateValue('CSV_NEWSITE_USER', Tools::getValue('CSV_NEWSITE_USER'));
               Configuration::updateValue('CSV_NEWSITE_PASSWD', Tools::getValue('CSV_NEWSITE_PASSWD'));
               Configuration::updateValue('CSV_NEWSITE_PREFIX', Tools::getValue('CSV_NEWSITE_PREFIX'));
               
             
                   
                 $link=$this->ExpertFunctions->getRemoteLink();
                 if(!$link)
                  $this->warnings[]=$this->l('Cannoct connect the new site database');  
            
    
               Configuration::updateValue('CSV_OLDSITE_SERVER', Tools::getValue('CSV_OLDSITE_SERVER'));
               Configuration::updateValue('CSV_OLDSITE_DB', Tools::getValue('CSV_OLDSITE_DB'));
               Configuration::updateValue('CSV_OLDSITE_USER', Tools::getValue('CSV_OLDSITE_USER'));
               Configuration::updateValue('CSV_OLDSITE_PASSWD', Tools::getValue('CSV_OLDSITE_PASSWD'));
               Configuration::updateValue('CSV_OLDSITE_PREFIX', Tools::getValue('CSV_OLDSITE_PREFIX'));
                $link=$this->ExpertFunctions->getOldLink();
                 if(!$link)
                  $this->warnings[]=$this->l('Cannoct connect the new site database');  
               
        }
          if(Tools::isSubmit('btnMissing')) {
             if(!$link=$this->ExpertFunctions->getRemoteLink()) {
                $this->_postErrors[]=$this->l("Cannot conntect to remote server"); 
                return;
             }
             $this->link=$link;
             $this->csvProducts(true);
        }
         elseif(Tools::isSubmit('btnOrders') ) {
             if(!$link=$this->ExpertFunctions->getOldLink()) {
                $this->_postErrors[]=$this->l("Cannot conntect to remote server"); 
                return;
             }
             $this->link=$link;
             $this->ExpertFunctions->importOrders();
        }
         elseif(Tools::isSubmit('btnImportProducts') ) {
             $this->ExpertFunctions->saveImportSettings();
        }
         elseif(Tools::isSubmit('btnMatchOnly') ) {
             $this->ExpertFunctions->saveImportMatch();
        }
            
            
        
    
            
        } 
        
         
          
          
    }   
    

    
 private function fetch_assoc($myquery){
        $retval=array();
        $result = mysql_query($myquery, $this->link);
        while($row= mysql_fetch_assoc($result))
          $retval[]=$row;
        return $retval;
}  
    
    private function   csvCategories() {
            $this->csvInit();
            require_once("CsvCategory.php");
              
            $categories=Category::getCategories($this->id_lang, false);
            $Cat=new CsvCategory();
            $categories=$Cat->lineariseCategories($categories);
            $Write=new CsvWrite( $this->config['CSV_FEEDDIR'], $this->id_lang, $this->config['rate']);
            $description=Tools::getValue('chckDescription')?1:0;
          //  $Write->createCategories($categories, $description);  
             $Write->createItems($categories, 'category', $description);   
    }
    
        private function   csvManufacturers() {
             $this->csvInit();
            require_once("CsvManufacturer.php");
             $Fetch=new CsvManufacturer();
            $items= $Fetch->getItems($this->id_lang);
            $Write=new CsvWrite( $this->config['CSV_FEEDDIR'], $this->id_lang, $this->config['rate']);
            $description=Tools::getValue('chckDescription')?1:0;
            $Write->createItems($items, 'manufacturer', $description);   
    }
    
         private function   csvSuppliers() {
             $this->csvInit();
            require_once("CsvSupplier.php");
             $Fetch=new CsvSupplier();
            $items= $Fetch->getItems($this->id_lang);
            $Write=new CsvWrite( $this->config['CSV_FEEDDIR'], $this->id_lang, $this->config['rate']);
            $description=Tools::getValue('chckDescription')?1:0;
            $Write->createItems($items, 'supplier', $description);   
    }
    
         private function   csvCustomers() {
             $this->csvInit();
            require_once("CsvCustomer.php");
             $Fetch=new CsvCustomer();
            $items= $Fetch->getItems($this->id_lang);
            $Write=new CsvWrite( $this->config['CSV_FEEDDIR'], $this->id_lang, $this->config['rate']);
            $description=Tools::getValue('chckDescription')?1:0;
            $Write->createItems($items, 'customer', $description);   
    }
    
        private function   csvAdresses() {
             $this->csvInit();
            require_once("CsvAddress.php");
             $Fetch=new CsvAddress();
            $items= $Fetch->getItems($this->id_lang);
            $Write=new CsvWrite( $this->config['CSV_FEEDDIR'], $this->id_lang, $this->config['rate']);
            $description=Tools::getValue('chckDescription')?1:0;
            $Write->createItems($items, 'address', $description);   
    }
    
    
     private function   csvProducts($missing=false) {
         
        if($missing && is_object($this->ExpertFunctions)) {
             $arr=$this->ExpertFunctions->getRemoteIds();
             $ids=array();
             foreach($arr as $p)
               $ids[$p['id_product']]=$p['id_product'];
        //       $ids = array_map(function($el){ return $el['id_product']; }, $arr);    
        } 
       
        $this->csvInit();
        require_once("CsvPrices.php");
        
        $Write=new CsvWrite( $this->config['CSV_FEEDDIR'], $this->id_lang, $this->config['CSV_RATE'], $this->config['CSV_IMGDIR']);
        $CsvPrices=new CsvPrices();
        $from=0;
       
        
        if(isset($this->config['CSV_MAX']))
        $max =$this->config['CSV_MAX'] < $this->defaultmax?$this->config['CSV_MAX'] : $this->defaultmax;
        else
        $max=$this->defaultmax;
        
       if(isset($this->config['CSV_STEP']))
       $step =$this->config['CSV_STEP'] < $this->defaultstep?$this->config['CSV_STEP'] : $this->defaultstep;
        else
        $step=$this->defaultstep;
        
       
        
        
        $limit =$max>$step?$step:$max; 
        
        $pointer=Tools::getValue('CSV_STARTAT');
        $max=$max + $pointer;
        
        for($j=$pointer; $j< $max; $j+=$step) {
         set_time_limit(60);
                $from++;
              $products =Product::getProducts($this->id_lang,     $j,      $limit,  'id_product', 'asc',      false,                    false); 
              
           
                
           //          public static function getProducts($id_lang, $start, $limit, $orderBy, $orderWay, $id_category = false, $only_active = false)
           
               
                if(empty($products))
                break;

               $copy=array();
                foreach($products as $product) {
                     if($missing === true && isset($ids[$product['id_product']] ) )   
                      continue;
             //        echo $product['id_product'].';';
                      $prices=$CsvPrices->getPriceReduction($product['id_product']);
                     
                        if(is_array($prices)) {
                            while(list($key,$val)=each($prices)) {
                                if(is_string($key)) {
                                    if($key=='reduction_from' || $key=='reduction_to')
                                    $product[$key]=substr($val,0,10); 
                                    else
                                    $product[$key]=$val;
                                }
                            } 

                        }

                        $product['categories']= Db::getInstance()->ExecuteS('
                        SELECT  '._DB_PREFIX_.'category_lang.`id_category`
                        FROM 

                        `'._DB_PREFIX_.'category_product` LEFT JOIN `'._DB_PREFIX_.'category_lang`
                        ON `'._DB_PREFIX_.'category_product`.id_category =  `'._DB_PREFIX_.'category_lang`.id_category
                        WHERE `id_product` = '.$product['id_product'].' AND id_lang ='.$this->id_lang) ;
                 
                 if(strlen( $product['description_short']) > 800) {
                      $products['description_short']=strip_tags($product['description_short']);     
                      if(function_exists('mb_substr'))
                          $product['description_short']=mb_substr($product['description_short'], 0,800, 'UTF-8');
                      else
                         $product['description_short']=substr($product['description_short'], 0,800);
                         
                 } 
                 
                 if(Tools::getValue('sanitise')) {
                    $product=$this->validProduct($product); 
                 }
                 
                 $copy[]=$product;   
              
                  
                }
                $description=Tools::getValue('chckDescription')?1:0; 
            
                $Write->createProducts($copy,$from, $description); 
         
        }
        
    }
    
    
  protected function validProduct($product) {
  require_once(dirname(__FILE__).'/ValidateCsv.php');
   $fieldsValidateLang = array(
        'meta_description' => 'isGenericName', 'meta_keywords' => 'isGenericName',
        'meta_title' => 'isGenericName', 'link_rewrite' => 'isLinkRewrite', 'name' => 'isCatalogName',
        'description' => 'isString', 'description_short' => 'isString', 'available_now' => 'isGenericName', 'available_later' => 'IsGenericName');
      $validate = new Validate();
        foreach ($fieldsValidateLang as $fieldArray => $method)
        {
          
          
                if (method_exists($validate, $method) && isset($product[$fieldArray])) {
                  
                if (!empty($product[$fieldArray]) AND 
                ( !call_user_func(array('Validate', $method), $product[$fieldArray])
                || !$this->validate($fieldArray, $product[$fieldArray])
                ))
                {
                    $product[$fieldArray]=strip_tags($product[$fieldArray]);
                    $product[$fieldArray] = preg_replace('/^[^<>;=#{}]*$/u','', $product[$fieldArray]);
                }
                }
        }
  
   return $product;
  }  


private function validate($method, $s) {
   switch($method) {
        case 'description':  {
            $pos=strpos($s, '<iframe');
            if(!($pos=== false))
              return false;
              
            $pos=strpos($s, '<form');
            if(!($pos=== false))
              return false;
           
           $pos=strpos($s, '<input');
            if(!($pos=== false))
              return false; 
           
             
            return true;
            
        }
        
        default: return true;
       
   }

}    
    private function csvInit() {
        $this->id_lang=Configuration::get("PS_LANG_DEFAULT")?Configuration::get("PS_LANG_DEFAULT"):6; 
        require_once("CsvWrite.php"); 
    }
    
    private  function getProducts($id_lang, $start, $limit, $orderBy, $orderWay, $id_category = false)
    {
        
        Db::getInstance()->Execute('SET NAMES \'utf8\''); 
        if (!Validate::isOrderBy($orderBy) OR !Validate::isOrderWay($orderWay))
            die (Tools::displayError());
        if ($orderBy == 'id_product' OR    $orderBy == 'price' OR    $orderBy == 'date_add')
            $orderByPrefix = 'p';
        elseif ($orderBy == 'name')
            $orderByPrefix = 'pl';
        elseif ($orderBy == 'position')
            $orderByPrefix = 'c';
         $sql='SELECT p.*, pl.* , t.`rate` AS tax_rate, m.`name` AS manufacturer_name, s.`name` AS supplier_name
        FROM `'._DB_PREFIX_.'product` p
        LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product`)
        LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = p.`id_tax`)
        LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
        LEFT JOIN `'._DB_PREFIX_.'supplier` s ON (s.`id_supplier` = p.`id_supplier`)'.
        ($id_category ? 'LEFT JOIN `'._DB_PREFIX_.'category_product` c ON (c.`id_product` = p.`id_product`)' : '').'
        WHERE pl.`id_lang` = '.intval($id_lang).
        ($id_category ? ' AND c.`id_category` = '.$id_category : '').'
        ORDER BY '.(isset($orderByPrefix) ? $orderByPrefix.'.' : '').'`'.pSQL($orderBy).'` '.pSQL($orderWay).
        ($limit > 0 ? ' LIMIT '.intval($start).','.intval($limit) : '');   
        $rq = Db::getInstance()->ExecuteS($sql);
        if($orderBy == 'price')
        {
            Tools::orderbyPrice($rq,$orderWay);
        }
     //  echo $sql.'<br />';
        return ($rq);
    }
}



  

 






