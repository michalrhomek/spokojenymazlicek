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
  class UtilsController extends ZboziController { 
  	  private $debugInfo = '';
      private $debugSql;
  	  
    public function postProcess() {
    	 $display ='';
    	 if(isset($_POST['cmd_utils'][1])){
        	 $this->setLowestProductCategory();
        	  Configuration::updateValue('ZBOZI_LOWESTCATEGORY',1); 
		}
		
		if(isset($_POST['cmd_utils'][2])){
        	  $sql = 'DROP TABLE    `'._DB_PREFIX_.'zb_default_cats`';
        	  Db::getInstance()->execute($sql); 
        	    $sql = 'DROP TABLE    `'._DB_PREFIX_.'zb_default_cats2`';
        	  Db::getInstance()->execute($sql); 
        	  Configuration::updateValue('ZBOZI_LOWESTCATEGORY',0); 
		}
        
        if(isset($_POST['cmd_utils'][3])){ 
          $keys = array_keys($_POST['cmd_utils'][3]);
          $tablename = $keys[0];
          $keys = array_keys($_POST['cmd_utils'][3][$tablename]);
          $columname =   $keys[0];
          if(strlen($tablename) && strlen($columname)) {
              if ($this->instance->addSqlField($tablename, $columname) == false)
                 $display ='Přidání slupce se nepodařilo, použijte phpMyAdmin';
          }
          $_POST['cmd_utils'][0] = true;
          return $display;
        } 
    }	  
   
	public function getContent ($tabnum)
    {   
		$display='<form action="'.$_SERVER['REQUEST_URI'].'" method="post">';
		$display.='<input type="hidden" name="currentTab" value="'.$tabnum.'" />';
		$display.=' <fieldset><legend>Nejnižší kategorie produktu</legend>';  
		$display.='Umožní mapování kategorií na základě nejhlubší kategorii do které je zařazen <br />';
		if( Configuration::get('ZBOZI_CATSPERPRODUCT') == 1) {
			if(Configuration::get('ZBOZI_LOWESTCATEGORY') == 1) {
				 $display.='<input type="submit" name="cmd_utils[1]" value="Přepočítat" /> &nbsp; &nbsp;';	
		        $display.='<input type="submit" name="cmd_utils[2]" value="Vypnout" />';
			}
			else {
			  $display.='<input type="submit" name="cmd_utils[1]" value="Zapnout " />';	
			}
		}
		else {
		 $display.=' <b>nejprve povolte rozšířené vlastnosti v záložce "Rozšíření"</b>';
		}
		$display.='</fieldset></form>';
		
		$display.='<form action="'.$_SERVER['REQUEST_URI'].'" method="post">';
		$display.='<input type="hidden" name="currentTab" value="'.$tabnum.'" />';
		$display.=' <fieldset><legend>Kontrola instalace</legend>';  
		$display.='Je modul správně instalován?';
        
         if(isset($_POST['cmd_utils'][0])){
             $this->checkInstall();
         
            $display .='<br /><b>';
               $display.= $this->debugSql.'<br />';
            if(strlen(  $this->debugInfo)) {
                $display.= '<span style="color:red">'. $this->debugInfo.'</span>';
                
                $display.='<br /><br /> <a href="http://prestahost.eu/navody/index.php/10-reseni-problemu" target="_blank">nápověda</a>';
            }
            else  {
                $display.= "<span style='color:green'>Žádný problém nebyl nalezen</span>";
            }
            $display.='</b>';
            
         } 
        
        
		 
		$display.=' <input type="submit" name="cmd_utils[0]" value="Zkontrolovat" />';
		
		$display.='</fieldset></form>';
		
		
		
		return $display;
	
	}
	
	 private function  checkInstall() {
      $this->debugSql .= $this->checkSql();
      $this->debugInfo .= $this->checkOverrides();
      $this->debugInfo .= $this->checkProductsInCategory();
      $this->debugInfo .= $this->checkLibraries();
	}
	
	private function   checkLibraries () {
		     $output='';
		if(! function_exists('zip_open')) {
			 $output.='Na hostingu  zřejmě chybí knihovna ZIP - nebudou vytvářeny ZIP verze feedů';
		}
		if(!function_exists('curl_version')) {
			$output.='Na hostingu  zřejmě chybí knihovna cURL -  nebude možné centrálně mapovat kategorie';
		}
		
		return $output;
	}
	
	private function  checkProductsInCategory() {
		$output='';
	      if(Shop::isFeatureActive()) {
        		$id_shop= Shop::getContextShopID();
			}
			if(empty($id_shop))
			    $id_shop=Context::getContext()->shop->id;
	      if((int)Configuration::get('ZBOZI_LOWESTCATEGORY') ==1 ) {
	      	    $sql ='SELECT count(*) FROM '._DB_PREFIX_.'product_shop WHERE  lowest_category is   null
	      	     AND id_shop='.$id_shop;
	      	    $pocet  = Db::getInstance()->getValue($sql);
	      	     if((int)$pocet > 0) {
                    	 $output .= $pocet.' produktů nemá vyplněnu nejhlubší použitou kategorii, klikněte na nastavit/přepočítat <br />';
				 } 
		  }
		  else {
		  $sql='SELECT id_category FROM '._DB_PREFIX_.'category WHERE id_shop_default ='.$id_shop.' AND 
		  is_root_category =1';
		  $id_category_root = Db::getInstance()->getValue($sql);
		  
		  $sql ='SELECT count(*) FROM '._DB_PREFIX_.'product WHERE id_category_default ='.$id_category_root;
		  $count3 =   Db::getInstance()->getValue($sql);
		  
		  if($count3) {
		  	  $output .= $count3.' produktů je má jako výchozí kategorii nejvyšší úrovně, to může dělat problémy při jejich mapování';
		  }
		  }
		  
		  
		  	  	  return $output;
	} 
	
	private function checkOverrides(){
		$output='';

           
  if((int)Configuration::get('ZBOZI_CATSPERPRODUCT') ==1 ) {
  	$checks[2] = array( 
  	'source'=>_PS_MODULE_DIR_.$this->instance->name.'/install/heureka.tpl',
  	'target'=>_PS_ADMIN_DIR_.'/themes/default/template/controllers/products/heureka.tpl');  
  	$checks[3] = array( 
  	'source'=>_PS_MODULE_DIR_.$this->instance->name.'/install/zbozi_text.tpl',
  	'target'=>_PS_ADMIN_DIR_.'/themes/default/template/controllers/products/zbozi_text.tpl');
  	  
  }
  if(isset($checks) && is_array($checks))
  foreach($checks as $check) {
  	  $a = filesize($check['target']);
  	  $b = filesize($check['source']);
  	  if(!file_exists($check['target'])) {
  	  	  $output .= 'Soubor '.$check['target'].' neexistuje<br />';
	  }
	  elseif(filesize($check['target']) != filesize($check['source'] )) {
	  	   $output .=  'Soubor '.$check['target'].' se liší verze dodávané  s touto verzí modulu.<br />';
	  }
  	  
  }
     
      if((int)Configuration::get('ZBOZI_CATSPERPRODUCT') ==1 ) {
      	  $path= _PS_ADMIN_DIR_.'/themes/default/template/controllers/products/associations.tpl';
           if(!$this->isAlreadyIncluded($path, 'heureka.tpl'))
             $output .= 'šablona '.$path.' není modifikována (chybí {include file="controllers/products/heureka.tpl"}';
             
             $path= _PS_ADMIN_DIR_.'/themes/default/template/controllers/products/informations.tpl';
           if(!$this->isAlreadyIncluded($path, 'zbozi_text.tpl'))
             $output .= 'šablona '.$path.' není modifikována (chybí {include file="controllers/products/zbozi_text.tpl"}';
	  }
return $output;           
}
	
	private function checkSql(){
	$output ='';
	$checks   =  array();
	$checks[] =  array(0=>'category_lang',1=>'heureka_category', 2=> 'VARCHAR (250)');
	$checks[] =  array(0=>'category_lang',1=>'google_category', 2=> 'VARCHAR (250)');
    $checks[] =  array(0=>'category_lang',1=>'zbozi_category', 2=> 'VARCHAR (250)');
    $checks[] =  array(0=>'category_lang',1=>'glami_category', 2=> 'VARCHAR (250)');
	   if((int)Configuration::get('ZBOZI_CATSPERPRODUCT') ==1 ) {
        $keys = array('heureka_category', 'videourl','productline', 'extramessage', 'heureka_cpc', 'max_cpc', 'max_cpc_search', 'skipfeeds');
		foreach($keys as $key) {
          $checks[] =   array(0=>'product',1=>$key, 2=> 'VARCHAR (250)'); 
        }
      
		$checks[] =  array(0=>'product_shop',1=>'lowest_category', 2=> 'VARCHAR (int)');
        $checks[] =  array(0=>'product_shop',1=>'skipfeeds', 2=> 'VARCHAR (250)');
	   }  
      $output .= "tabulka : sloupec <br />";              
      foreach($checks as $check) {
          $tablename=pSQL($check[0]);
          $columnname=pSQL($check[1]);
        $sql='SELECT column_name
                    FROM information_schema.columns 
                    WHERE table_schema = "'._DB_NAME_.'" 
                    AND table_name   = "'._DB_PREFIX_.$tablename.'"
                    AND column_name  = "'.$columnname.'"';
                    $column_exists=Db::getInstance()->getValue($sql);
         
        if(!$column_exists) {
        	$output .= $tablename.' :  '.$columnname.' '.$check[2].'<span style="color:red"> Chybí !</span><input type="submit" name="cmd_utils[3]['.$tablename.']['.$columnname.']" value="Opravit"><br /><br />';
		}
        else {
            $output .= $tablename.' :  '.$columnname.' '.$check[2].'<span style="color:green"> OK</span><br />';
        }
	  }          
	    
    return $output;
}
             
    
    private function   setLowestProductCategory() {  
    	
    	   
        	if(Shop::isFeatureActive()) {
        		$id_shop= Shop::getContextShopID();
			}
			if(empty($id_shop))
			    $id_shop=Context::getContext()->shop->id;
		 
    	
    	
    	  //   echo $sql.'<br />';
    	$sql="CREATE    TABLE IF NOT EXISTS `"._DB_PREFIX_."zb_default_cats` (
		`id_product` int(10) unsigned NOT NULL DEFAULT '0',
		`id_category` int(10) unsigned DEFAULT NULL,
		`level_depth` int(10) unsigned DEFAULT NULL,
		PRIMARY KEY (`id_product`, `id_category`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
        Db::getInstance()->execute($sql);
        $sql="TRUNCATE TABLE      `"._DB_PREFIX_."zb_default_cats` ";
    	 Db::getInstance()->execute($sql);
          
        $sql='INSERT INTO   `'._DB_PREFIX_.'zb_default_cats` (id_product, id_category, level_depth)  
          SELECT cp.id_product, cp.id_category,  level_depth FROM 
          '._DB_PREFIX_.'category_product cp 
          LEFT JOIN  
          ( '._DB_PREFIX_.'category c    LEFT JOIN   '._DB_PREFIX_.'category_shop cs
          ON c.id_category=cs.id_category AND cs.id_shop='.(int)$id_shop.'
          ) ON
          cp.id_category = c.id_category WHERE 1';
         Db::getInstance()->execute($sql);
          // echo $sql.'<br />';
           
           
       
         $sql="CREATE   TABLE  IF NOT EXISTS  `"._DB_PREFIX_."zb_default_cats2` (
		`id_product` int(10) unsigned NOT NULL DEFAULT '0',
		`id_category` int(10) unsigned DEFAULT NULL,
		`level_depth` int(10) unsigned DEFAULT NULL,
		PRIMARY KEY (`id_product`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
        Db::getInstance()->execute($sql);
        $sql="TRUNCATE TABLE  `"._DB_PREFIX_."zb_default_cats2`";
         Db::getInstance()->execute($sql);   
    	 
    	 $sql="
INSERT INTO   `"._DB_PREFIX_."zb_default_cats2` (id_product, id_category, level_depth)  
select id_product, id_category, level_depth
from `"._DB_PREFIX_."zb_default_cats`
where level_depth = (select max(level_depth) from `"._DB_PREFIX_."zb_default_cats` as f where f.id_product = `"._DB_PREFIX_."zb_default_cats`.id_product)
group by `"._DB_PREFIX_."zb_default_cats`.id_product";
          

        if( Db::getInstance()->execute($sql)) {
         
          
        $sql='UPDATE '._DB_PREFIX_.'product_shop s LEFT JOIN `'._DB_PREFIX_.'zb_default_cats2` d ON
         s.id_product = d.id_product SET 
         s.lowest_category = d.id_category WHERE  s.id_shop='.(int)$id_shop;
        Db::getInstance()->execute($sql); 
           //  echo $sql.'<br />';
		}
	}
  }