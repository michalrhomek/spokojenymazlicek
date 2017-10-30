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
  class ZboziController {
  	 
  	 
  	 protected $instance;
  	 protected $warning = array();
  	 public function __construct($instance) {
  	   $this->instance = $instance;
  	 
	 }
	 
	 protected function showFeedScripts() {
      $display='';
 
	   $module_url= $this->instance->getModuleUrl();
       $url=$module_url."/feeds.php";
       if(Context::getContext()->shop->isFeatureActive()) {
          	        $url.='?id_shop='.Context::getContext()->shop->id;
          	        $spojka='&';
	   }
	   else
	        $spojka='?';
         
	   $meny=Currency::getCurrencies(false, true, true);

	        
          $display .= 'Pro vytvoření feedu je potřeba spouštět skript <br />';  	        
        if(Shop::isFeatureActive())
		    $langs=Language::getLanguages(true, Shop::getContextShopID());
		    else
		    $langs=Language::getLanguages(true, false);
       
       $jazyk ='';
		 foreach($langs as $lang) {
		 	 $vsuvka='';
             if(count($langs > 1)) {
                $vsuvka=$spojka.'id_lang='.$lang['id_lang'];
                $jazyk=$lang['name'];
			 } 
			 if($lang['iso_code'] == 'sk') {
			 	 foreach($meny as $mena) {
			 	 	  if($mena['iso_code'] == 'EUR') {
			 	 	  if(strlen($vsuvka))
			 	 	     $vsuvka.='&id_currency='.$mena['id_currency'];
			 	 	  else
			 	 	    $vsuvka.='?id_currency='.$mena['id_currency'];
			 	 	   $jazyk.=' '.$mena['iso_code'];
					  }
				 }
			 
			 } 
             if($lang['iso_code'] == 'cs' && is_array($meny) && count($meny) > 1) {
                 foreach($meny as $mena) {
                        if($mena['iso_code'] == 'CZK') {
                        if(strlen($vsuvka))
                           $vsuvka.='&id_currency='.$mena['id_currency'];
                        else
                          $vsuvka.='?id_currency='.$mena['id_currency'];
                          $jazyk.=' '.$mena['iso_code'];
                      }
                 } 
                 
             }
             $hash ='';
             if(Configuration::get('ZBOZI_HASH') && strlen(Configuration::get('ZBOZI_HASH'))) {
               $hash='&hash='.Configuration::get('ZBOZI_HASH');  
             }
             $display .=  '<a href="'.$url.$vsuvka.$hash.' " target="_blank" style="color:blue">'.$url.$vsuvka.$hash.'</a> '.$jazyk. '<br />';   
		 }
		 return $display;
	} 
	
protected function  translateAttribute($name, $from, $to) {
	if($from == $to)
     	return $name;
    if(empty($name))
      return '';
      
    if(Configuration::get('ZBOZI_ATTR_PUBLIC'))
     $select='public_name';
    else
     $select='name';  
     	
    $sql = 'SELECT id_attribute_group FROM '._DB_PREFIX_.'attribute_group_lang WHERE id_lang='.$from.' 
    AND name="'.pSQL($name).'"';
    $id_attribute = Db::getInstance()->getValue($sql);
    if($id_attribute) {
    	$translated=Db::getInstance()->getValue('SELECT name   FROM '._DB_PREFIX_.'attribute_group_lang WHERE id_lang='.$to.' AND id_attribute_group='.(int)$id_attribute); 
	    if($translated && strlen($translated))
	    return $translated;
	}
	
	return $name;
}
	protected function isAlreadyIncluded($path, $needle) {
   $s=file_get_contents($path);
   $pos=strpos($s, $needle);
   if($pos === false) {
      return false; // not included
   }   

    return true; 
} 
	
	protected function getSkupiny() {
	   if(Configuration::get('ZBOZI_ATTR_PUBLIC')) {
		     	$sql='SELECT DISTINCT public_name as name FROM '._DB_PREFIX_.'attribute_group_lang WHERE id_lang='.(int)Configuration::get('PS_LANG_DEFAULT');   
		   }
		   else {
		      $sql='SELECT name FROM '._DB_PREFIX_.'attribute_group_lang WHERE id_lang='.(int)Configuration::get('PS_LANG_DEFAULT');
		  }
		  return Db::getInstance()->executeS($sql);
		  
}


 protected function restrictedAttributes($mode) {
         $mode = strtoupper($mode);
         $display ='<fieldset><legend>Přenášené attributy</legend>';
         $checked=Configuration::get('ZBOZI_FILTERATR_'.$mode)>0?" checked='checked'":"";
         
        
         $display.='<input type="checkbox" name="ZBOZI_FILTERATR_'.$mode.'" value="1" '.$checked.' />';
         
         if($mode == 'GLAMI')
         $display.='Filtrovat attributy, pokud nezaškrtnete, přenáší se všechny skupiny<br /><br />';
         else
         $display.='Filtrovat attributy (pokud nezaškrtnete, přenáší se všechny skupiny, atributy se v názvech vynachají <br /><br />';
         
         
         if( Configuration::get('ZBOZI_FILTERATR_'.$mode)) {
         $display.='Přenášet jen vybrané: <br />';
         $sql='SELECT gl.name, g.id_attribute_group FROM '._DB_PREFIX_.'attribute_group g LEFT JOIN '._DB_PREFIX_.'attribute_group_lang gl
          ON g.id_attribute_group = gl.id_attribute_group 
          WHERE id_lang='.(int)Configuration::get('PS_LANG_DEFAULT');
         $skupiny = Db::getInstance()->executeS($sql);
         
         $selected = json_decode(Configuration::get('ZBOZI_USEDATTR_'.$mode), true);
        
          $display.='<table class="table">';
          $display.='<tr><th>Skupina</td><th>Kompletní export</th> <th>Vynechat skupinu z názvu</th> <th>Vynechat attribut názvu</th> <th>Neexportovat</th></tr>';
         foreach($skupiny as $skupina) {
           $display.='<tr>';
             $display.='<td>'.$skupina['name'].'</td>';
           for($i = 0; $i< 4; $i++) {
           $checked = (isset($selected[$skupina['id_attribute_group']]) && $selected[$skupina['id_attribute_group']] == $i)?' checked="checked"':'';
           $display.='<td><input type="radio" name="ZBOZI_USEDATTR_'.$mode.'['.$skupina['id_attribute_group'].']" value="'.$i.'" '.$checked.'></td>';  
           }
           
           $display.='</tr>';
         }
         $display.="<tr style='font-size:8px'><td>Příklady výsledného PRODUCT<br />   jméno produktu je 'Halenka', skupiny 'barva'
         </td> <td>Halenka barva červená</td><td>Halenka   červená</td><td>Halenka</td><td>&nbsp;</td></td>";
         $display.='</table><br />';
        
         }
          
         $display.= '</fieldset><br />';
         return $display;
    }

protected function updateOptim($feedname) {

				$tagnames=array('productname', 'product');     
				$optim=array(); 
				foreach($tagnames as $tagname) {
				$optr=array();
				$keys=array('name', 'manufacturer','reference','ean', 'custom');
				foreach($keys as $key) {
				$poradiname='optimporadi_'.$feedname.'_'.$tagname.'_'.$key;
				$usename='optimuse_'.$feedname.'_'.$tagname.'_'.$key;
				$poradi=(int)Tools::getValue($poradiname);
				$use=isset($_POST[$usename])?1:0;
				if($key == 'name')
				  $use=1;
				if($key == 'custom') {
				$customname='optimcustom_'.$feedname.'_'.$tagname.'_'.$key;
				$custom=(string)Tools::getValue($customname);
				$optr[$key]=array('poradi'=>$poradi, 'pouzit'=>$use, 'custom'=>$custom);
				}
				else
				$optr[$key]=array('poradi'=>$poradi, 'pouzit'=>$use);
				}


				$optim[$feedname][$tagname]=$optr;   
				}
				
				$stored=json_decode(Configuration::get('ZBOZI_OPTIM'),true);
				$stored[$feedname]=$optim[$feedname];
				Configuration::updateValue('ZBOZI_OPTIM', json_encode($stored));
}

    
    protected function showSaveResult() {
    	 
    	
    	 if(is_array($this->warning) && count($this->warning)) {
           return  '<div class="error">'.implode('<br />'.$this->warning).'</div>';  
        }
        else
         return '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="OK" />Změna byla uložena</div>';
	}
    
    protected function  childsRecursive($cat, &$childs) {
         $sql= 'SELECT id_category FROM '._DB_PREFIX_.'category WHERE id_parent='.(int)$cat;
         $cats = Db::getInstance()->executeS($sql);
         if(is_array($cats) && count($cats)) {
             foreach($cats as $c) {
                  $childs[] = $c['id_category'];
                  $this->childsRecursive($c['id_category'], $childs);
             }
         }
         return; 
    }
  	  
  }
?>
