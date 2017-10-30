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
  class SeznamController extends ZboziController {
  	  
     public function postProcess() {
     	  if(isset($_POST['cmd_seznam'][0])){
     	  	     $this->updateOptim('zbozi');
    	    	 Configuration::updateValue('ZBOZI_SKLADzb', intval(Tools::getValue('ZBOZI_SKLADzb'))); 
    	    	 Configuration::updateValue('ZBOZI_CATS_FORBIDDENzb', Tools::getValue('ZBOZI_CATS_FORBIDDENzb'));
          
          if((int)Tools::getValue('ZBOZI_ROUND_ZBOZI') == 1)
            Configuration::updateValue('ZBOZI_ROUND_ZBOZI', 1); 
            else
            Configuration::updateValue('ZBOZI_ROUND_ZBOZI', 0); 
             Configuration::updateValue('ZBOZI_CATS_EROTIC', intval(Tools::getValue('ZBOZI_CATS_EROTIC'))); 
             Configuration::updateValue('ZBOZI_TRANSFORMED_COUNTzb', intval(Tools::getValue('ZBOZI_TRANSFORMED_COUNTzb')));  
            $transformed=array();
      
           for($i=0;$i<count($_POST['attr']); $i++) {
                  if(isset($_POST['attr'][$i]) && strlen($_POST['attr'][$i]) && strlen($_POST['param'][$i]) )
                       $transformed[]=array(0=>$_POST['attr'][$i], 1=>$_POST['param'][$i]);
            }  
            Configuration::updateValue('ZBOZI_TRANSFORMEDzb', json_encode($transformed)); 
          
           $transformed=array();  
          for($i=0;$i<count($_POST['features']); $i++) {
                  if(isset($_POST['features'][$i]) && (int)($_POST['features'][$i]['id']) && strlen($_POST['features'][$i]['unit']) )
                       $transformed[]=array(0=>$_POST['features'][$i]['id'], 1=>$_POST['features'][$i]['unit']);
            }  
            Configuration::updateValue('ZBOZI_FEATURESzb', json_encode($transformed)); 
            
    	    	 $this->showSaveResult();
		  }
          
          
		  
		   if(isset($_POST['cmd_seznam'][1])){
		   	  	$cats = explode(',', Tools::getValue('ZBOZI_CATS_FORBIDDENzb'));
	    	$all_cats = array();
	    	foreach($cats as $cat) {
	    	    $all_cats[]=$cat;
	    		$childs =array();
	    		$this->childsRecursive($cat, $childs);
	    		$all_cats=array_merge($all_cats, $childs);
			}
			$all_cats = array_unique($all_cats);
			$s = implode(',',$all_cats);
			  Configuration::updateValue('ZBOZI_CATS_FORBIDDENzb',  $s); 
		   	   
		   }
		   
		   if(isset($_POST['cmd_seznam'][2])){
		   	   if((int)Tools::getValue('ZBOZI_SEZNAM_FEED') == 0) {
		   	   	   if(file_exists(_PS_MODULE_DIR_.$this->instance->name.'/FeedSeznam.php.old')) {
		   	   	   	  rename(_PS_MODULE_DIR_.$this->instance->name.'/FeedSeznam.php', _PS_MODULE_DIR_.$this->instance->name.'/FeedSeznam.php.new'); 
		   	   	   	  rename(_PS_MODULE_DIR_.$this->instance->name.'/FeedSeznam.php.old', _PS_MODULE_DIR_.$this->instance->name.'/FeedSeznam.php'); 
				   }
			   }
			   if((int)Tools::getValue('ZBOZI_SEZNAM_FEED') == 1) {
		   	   	    if(file_exists(_PS_MODULE_DIR_.$this->instance->name.'/FeedSeznam.php.new')) {
		   	   	   	  rename(_PS_MODULE_DIR_.$this->instance->name.'/FeedSeznam.php', _PS_MODULE_DIR_.$this->instance->name.'/FeedSeznam.php.old'); 
		   	   	   	  rename(_PS_MODULE_DIR_.$this->instance->name.'/FeedSeznam.php.new', _PS_MODULE_DIR_.$this->instance->name.'/FeedSeznam.php'); 
				   }
			   }
		   }
            if(isset($_POST['ZBOZI_USEDATTR_SEZNAM'])) 
            Configuration::updateValue('ZBOZI_USEDATTR_SEZNAM', json_encode($_POST['ZBOZI_USEDATTR_SEZNAM'])); 
            else 
             Configuration::updateValue('ZBOZI_USEDATTR_SEZNAM', null); 
             
            Configuration::updateValue('ZBOZI_UTM_SEZNAM', Tools::getValue('ZBOZI_UTM_SEZNAM'));  
             
             
		   $checkboxes = array('ZBOZI_SEZNAM_SLEVA','ZBOZI_FILTERATR_SEZNAM'); 
		    
            while(list($key, $checbox) = each($checkboxes)) {
            if((int)Tools::getValue($checbox) == 1) {
            Configuration::updateValue($checbox, 1); 
            }
            else    {
            Configuration::updateValue($checbox, 0); 
            }
            }
	 }
	 
	  
	 
	public function getContent ($tabnum) {
		 $display='<form action="'.$_SERVER['REQUEST_URI'].'" method="post">';
		 $display.='<input type="hidden" name="currentTab" value="'.$tabnum.'" />';
		 
		  $display.='<fieldset><legend>Specifikace feedu</legend>';
	        $display.='<p class="napoveda">';
       	    
       	     $display.='V této verzi je k dispozici již jen nová specifikace feedu  <br />';
       	     
       	     
       	    $display.= '</p></fieldset>';
		 
		    
		    $display.='<fieldset><legend>Mapovování kategorií Zboží</legend>';
	        $display.='<p class="napoveda">';
       	        if($this->instance->do_attributes)
            $display.=$this->displayZboziMapAdvanced();
       	   
       	    $display.= '</p></fieldset>';
		    
		if($this->instance->do_attributes) {
           $display .= $this->displayAttributes().'<br /><br />';
           $display .= $this->restrictedAttributes('seznam');
        }
            
		$display.=$this-> displayOptimisationZbozi();
		$appr2=(int)Configuration::get('ZBOZI_SKLADzb');
		$labels=array('Nepoužívat','1 den jako ihned','Text skladem vždy jako ihned');
		$display.='<fieldset><legend>Rozšířená interpretace skladu:</legend>';
		for($i=0;$i<count($labels);$i++) {
			$display.='<input type="radio" name="ZBOZI_SKLADzb" value="'.$i.'"';
			if($i == $appr2)
			  $display.=' checked="checked"';
			$display.='/> '.$labels[$i].'<br />';
		}
		$display.='upravuje číslo nalezené v řetězci "available_now"</fieldset>';
        
        $display.=$this->parameterUnits();

		$display.='<fieldset><legend>Nastavení kategorií jen pro tento feed</legend>
		<textarea rows="2" cols="40" name="ZBOZI_CATS_FORBIDDENzb">'.Configuration::get('ZBOZI_CATS_FORBIDDENzb').'</textarea>'; 
		$display.='<input type="submit" name="cmd_seznam[1]" value="Přidat podkategorie" />';
        $erotic = (int) Configuration::get('ZBOZI_CATS_EROTIC');
         
        $display.='<br /><input type="radio" name="ZBOZI_CATS_EROTIC" value="0" ';
        if($erotic == 0) $display.= ' checked="checked"';
        $display.='/>Zboží v kategoriích  zakázat<br />';
        
        
        $display.='<input type="radio" name="ZBOZI_CATS_EROTIC" value="1" ';
        if($erotic == 1) $display.= ' checked="checked"';
        $display.='/>Zboží v kategoriích označit EROTIC <br />';
        
		$display.='<input type="radio" name="ZBOZI_CATS_EROTIC" value="2" ';
        if($erotic == 2) $display.= ' checked="checked"';
        $display.='/>Zboží v kategoriích  zakázat   všechno ostatní označit EROTIC<br />';
        $display.='</fieldset>';
		
		$display.='<fieldset><legend>Další</legend>';
		$checkbox="<input type=\"checkbox\" name=\"ZBOZI_ROUND_ZBOZI\" value='1' style=\"color:blue\"";
		if(Configuration::get("ZBOZI_ROUND_ZBOZI"))
		 $checkbox.=" checked=\"checked\" ";
		$checkbox.="/>Ceny v celých Kč"; 
		$display .=$checkbox."<br /><br />";
        $display .= 'Přidat utm k url: <input type="text" name ="ZBOZI_UTM_SEZNAM" value="'.Configuration::get('ZBOZI_UTM_SEZNAM').'" />';
        if(Configuration::get('PS_REWRITING_SETTINGS')) {
           $display .= 'Např. ?utm_source=zbozi.cz&utm_medium=ppc';  
        }else
         $display .= 'Např. &utm_source=zbozi.cz&utm_medium=ppc';
		$display.='</fieldset>';
		 
 
		 $display.=  '<input class="button" name="cmd_seznam[0]" value="Uložit změny" type="submit" /> 
        </form>';
        return $display;
	}
	
    protected function parameterUnits() { 
        $display ='';
        $display.='<fieldset><legend>Jednotky parametrů</legend>';
        $features = json_decode(Configuration::get('ZBOZI_FEATURESzb'), true);
        $celkem = 4;
        if(count($features))
          $celkem += count($features);
     
          
        $display.='<table><tr><td>ID parametru </td><td>jednotka</td></tr>';
        for($i = 0; $i<$celkem; $i++) {
          $id ='';
          $unit = '';
          if(isset($features[$i]) && (int)$features[$i][0]) {
              $id = $features[$i][0]; 
              $unit = $features[$i][1]; 
          }
         $display.='<tr><td><input type="text" name="features['.$i.'][id]" value="'.$id.'" size = "3" /></td><td><input type="text" name="features['.$i.'][unit]" value="'.$unit.'" /></td>';   
        }
        
         $display.='</table>';
       $sql = 'SELECT id_feature, name FROM 
        '._DB_PREFIX_.'feature_lang WHERE id_lang = '.Context::getContext()->language->id.' ORDER BY id_feature LIMIT 10';
        $params = Db::getInstance()->executeS($sql);
        $display.='ID všech parametrů najdete v Katalog - Vlastnosti produktů. ';
        $display.='Prvních 10 parametrů jsou: <br />'; 
        foreach($params as $param) {
          $display.=$param['id_feature'].': '.$param['name']. ' | ';  
        }
        $display.='</fieldset>';
        return $display;
    
    }

   protected function displayZboziMapAdvanced() {
		$display='';
		  if(Shop::isFeatureActive())
		    $langs=Language::getLanguages(true, Shop::getContextShopID());
		    else
		    $langs=Language::getLanguages(true, false);
		 
           $module_url = $this->instance->getModuleUrl();
         foreach($langs as $lang) {
             if($this->instance->fancy) {  
                   $display.='<a  class="mapbutton" href="#" onclick="fbox(\'zbozi\','.$lang['id_lang'].');return false">Namapovat kategorie '.$lang['name'].'</a><br />';
             } else {
                   $url = $module_url.'/categorymap.php?function=zbozi&id_lang='.$lang->id_language.'&id_shop='.Context::getContext()->shop->id.'&nw=1'; 
                   $display.='<a  class="mapbutton" href="'.$url.'" target="_blank" >Namapovat kategorie '.$lang['name'].'</a><br />';
             }
             }
            
            
            
          return $display;
	}
	
  
 
     protected function displayOptimisationZbozi() {
      $optim=json_decode(Configuration::get('ZBOZI_OPTIM'), true); 
               
      $display =' <fieldset><legend>Nastavení tagů PRODUCT a PRODUCTNAME</legend>';
      
     $display.='<table  cellpadding="10"><tr> <td>Feed</td><td>Tag feedu</td><td>Hodnota</td> <td>Pořadí</td><td>Použít?</td> </tr>'; 
     
      $feednames=array('Zbozi');
      $tagnames=array('PRODUCTNAME', 'PRODUCT');
      
      for($i=0;$i<count($feednames);$i++) {
            $fname=  strtolower($feednames[$i]);
              for($j=0;$j<count($tagnames);$j++) {
             $tagname=strtolower($tagnames[$j]);
            $optr=$optim[$fname][$tagname];
            
            $checked=(isset($optr['name']['pouzit']) && (int)$optr['name']['pouzit'])?' checked="checked"':'';
            $disabled='';
            if($i == 0) {
               $checked=' checked="checked"';
               $disabled=' DISABLED';
			} 
            
            
            $display.='<tr> <td style="padding:10px">'.$feednames[$i].'</td><td style="padding:10px">'.$tagnames[$j].'</td><td>jméno produktu</td><td><input type="text" size="4" name="optimporadi_'.$fname.'_'.$tagname.'_name" value="'.(int)$optr['name']['poradi'].'" /></td> <td><input type="checkbox" name="optimuse_'.$fname.'_'.$tagname.'_name" value="1" '.$checked.$disabled.'/></td> </tr>'; 

            $checked=(isset($optr['manufacturer']['pouzit']) && (int)$optr['manufacturer']['pouzit'])?' checked="checked"':'';
            $display.='<tr> <td style="padding:10px">'.$feednames[$i].'</td><td style="padding:10px">'.$tagnames[$j].'</td><td>jméno výrobce</td><td><input type="text" size="4" name="optimporadi_'.$fname.'_'.$tagname.'_manufacturer" value="'.(int)$optr['manufacturer']['poradi'].'" /></td> <td><input type="checkbox" name="optimuse_'.$fname.'_'.$tagname.'_manufacturer" value="1" '.$checked.'"/></td> </tr>'; 

            $checked= (isset($optr['reference']['pouzit']) && (int)$optr['reference']['pouzit'])?' checked="checked"':'';
            
            $display.='<tr> <td style="padding:10px">'.$feednames[$i].'</td><td style="padding:10px">'.$tagnames[$j].'</td><td>kód produktu</td><td><input type="text" size="4" name="optimporadi_'.$fname.'_'.$tagname.'_reference" value="'.(int)$optr['reference']['poradi'].'" /></td> <td><input type="checkbox" name="optimuse_'.$fname.'_'.$tagname.'_reference" value="1" '.$checked.'"/></td> </tr>'; 

            $checked=(isset($optr['ean']['pouzit']) &&(int)$optr['ean']['pouzit'])?' checked="checked"':'';
            $display.='<tr> <td style="padding:10px">'.$feednames[$i].'</td><td style="padding:10px">'.$tagnames[$j].'</td><td>EAN 13</td><td><input type="text" size="8" name="optimporadi_'.$fname.'_'.$tagname.'_ean" value="'.(int)$optr['ean']['poradi'].'" /></td> <td><input type="checkbox" name="optimuse_'.$fname.'_'.$tagname.'_ean" value="1"'.$checked.'"/></td> </tr>'; 

            $checked=(isset($optr['custom']['pouzit']) && (int)$optr['custom']['pouzit'])?' checked="checked"':'';
            $display.='<tr> <td style="padding:10px">'.$feednames[$i].'</td><td style="padding:10px">'.$tagnames[$j].'</td><td>Vlastní hodnota:<br /> <input type="text" size="22" name="optimcustom_'.$fname.'_'.$tagname.'_custom" value="'.(string)$optr['custom']['custom'].'" /> </td><td><input type="text" size="8" name="optimporadi_'.$fname.'_'.$tagname.'_custom" value="'.(int)$optr['custom']['poradi'].'" /></td> <td><input type="checkbox" name="optimuse_'.$fname.'_'.$tagname.'_custom" value="1" '.$checked.'"/></td> </tr>'; 
              }
      
      }
       $checkbox="</table><br /><input type=\"checkbox\" name=\"ZBOZI_SEZNAM_SLEVA\" value='1' style=\"color:blue\"";
		if(Configuration::get("ZBOZI_SEZNAM_SLEVA"))
		 $checkbox.=" checked=\"checked\" ";
		$checkbox.="/>Přidat k PRODUCT informace o slevě (<i>zahrne pouze slevy které nemají žádné omezení mimo časového</i>)"; 
		$display .=$checkbox."<br /> "; 
         
     $display .=' 
     <h4>Individuální rozšíření názvu produktů</h4>
     <ul>
     <li>V záložce Základní nastavení povolte rozšířené vlastnosti produktů</li>
     <li>Katalog - Produkty - Karta produktu - podzáložka Informace - vyplňte rozšiřující text</li>
     <li>Vyplněný text bude přidán  do elementu PRODUCT</li>
     </ul>
     <br />';
      
      $display .='</fieldset>';
       return $display;
   } 
  	  
     protected function displayAttributes() {
           $transformed=json_decode(Configuration::get('ZBOZI_TRANSFORMEDzb'), true);
            $pocet=Configuration::get('ZBOZI_TRANSFORMED_COUNTzb')>0?Configuration::get('ZBOZI_TRANSFORMED_COUNTzb'):5;
           $display ='<fieldset><legend>Kombinace Zboží  </legend>';
            $display .='Kombinace zboží se vytvoří automaticky. Nicméně pro  feed Zboží může být výhodné přemapovat některé
            Attributy do skupin vlastností. 
            Vytvořené kombinace pak budou mít navíc jeden nebo více tagů PARAM. Je nicméně potřeba řídit se
            názvy parametrů podle  
            <a href="http://napoveda.seznam.cz/soubory/Zbozi.cz/parametry_kategorii.csv" target="_blank">csv souboru</a>'
            ; 
            
            $skupiny = $this->getSkupiny();
            if($skupiny && is_array($skupiny)) {
                $display.='Skupiny vlastností v eshopu jsou: ';
                foreach($skupiny as $skupina) {
                    $display.=' | '.$skupina['name'];
                }
               
             }
            $display.='<table><tr><td width="200px">Název attributu v eshopu</td><td width="200px">Požadované jméno parametru pro Zboží.cz</td>';
            for($i=0;$i<$pocet;$i++) {
            $val1=  isset($transformed[$i][0])?(string)$transformed[$i][0]:'';
            $val2=  isset($transformed[$i][1])?(string)$transformed[$i][1]:'';
            $display.='<tr><td><input type="text" name="attr['.$i.']" value="'.$val1.'"/></td>
                           <td><input type="text" name="param['.$i.']" value="'.$val2.'"/></td>
                      </tr>';
            }
            $display.='</table>
           <br />
           Počet řádek v tabulce   <input type="text" size=2 name="ZBOZI_TRANSFORMED_COUNTzb" value="'.$pocet.'"/>
            
            </fieldset>';
            

            return $display;
    }
  
  
  }
