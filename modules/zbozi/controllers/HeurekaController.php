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
  class HeurekaController extends ZboziController {
  	  private $carriers;
  	  protected $cods;
  	  
  	  public function postProcess() {
  	  	  
           $carriers=array();
           $cods=array();
            while(list($key,$val)=each($_POST['carrier'])){
              $carriers[$key]=$val;
              $cods[$key]=(float)$_POST['carriercod'][$key];
            }  
             $this->carriers=$carriers;
             $this->cods=$cods;
             Configuration::updateValue('ZBOZI_CARRIERS', json_encode($carriers)); 
             Configuration::updateValue('ZBOZI_CARRIERSCOD', json_encode($cods)); 
             if(isset($_POST['ZBOZI_USEDATTR_HEUREKA']))
             Configuration::updateValue('ZBOZI_USEDATTR_HEUREKA', json_encode($_POST['ZBOZI_USEDATTR_HEUREKA'])); 
             else 
             Configuration::updateValue('ZBOZI_USEDATTR_HEUREKA', null);
           
             Configuration::updateValue('ZBOZI_TRANSFORMED_COUNT', intval(Tools::getValue('ZBOZI_TRANSFORMED_COUNT')));  
            $transformed=array();
      
           for($i=0;$i<count($_POST['attr']); $i++) {
                  if(isset($_POST['attr'][$i]) && strlen($_POST['attr'][$i]) && strlen($_POST['param'][$i]) )
                       $transformed[]=array(0=>$_POST['attr'][$i], 1=>$_POST['param'][$i]);
            }  
            Configuration::updateValue('ZBOZI_TRANSFORMED', json_encode($transformed)); 
            Configuration::updateValue('ZBOZI_DAREK_HEUREKANAME', Tools::getValue('ZBOZI_DAREK_HEUREKANAME'));
             
            $checkboxes = array('ZBOZI_ROUND_HEUREKA', 'ZBOZI_ROUND_HEUREKA', 'ZBOZI_HEUREKA_SLEVA', 'ZBOZI_ACCESSORY_HEUREKA','ZBOZI_DOPRAVA_ON','ZBOZI_DAREK_HEUREKA', 'ZBOZI_FILTERATR_HEUREKA', 'ZBOZI_DOPRAVACOD'  );
            while(list($key, $checbox) = each($checkboxes)) {
            if((int)Tools::getValue($checbox) == 1) {
            Configuration::updateValue($checbox, 1); 
			}
            else    {
            Configuration::updateValue($checbox, 0); 
			}
			}
 
		    $this->updateOptim('heureka');
              $this->showSaveResult();
	  }
	  
  	  public function getContent($tabnum) {
		 $display='<form action="'.$_SERVER['REQUEST_URI'].'" method="post">';
	     $display.='<input type="hidden" name="currentTab" value="'.$tabnum.'" />';
	     
	       
	      $display.='<fieldset><legend>Mapovování kategorií Heureka</legend>';
	        $display.='<p class="napoveda">
       	   <a class="napoveda" href="http://prestahost.eu/navody/index.php/7-modul-zbozi-karta-heureka" target="_blank">Nápověda (heureka)</a>
       	   <a class="napoveda" href="http://prestahost.eu/navody/index.php/8-modul-zbozi-mapovani-kategorii" target="_blank">Nápověda (mapování kategorií)</a>
       	   </p>';
       	   
       	   
	           if($this->instance->do_attributes)
             $display.=$this->displayHeurekaMapAdvanced();
           else
              $display.=$this->displayHeurekaMapBasic();
	      $display.='</fieldset>'; 
	      
	      
		 $display.=$this-> displayDoprava();
		 if($this->instance->do_attributes) {
           $display .= $this->displayAttributes().'<br /><br />';
           $display .= $this->restrictedAttributes('heureka');
         }
         
		$display.=$this-> displayOptimisationHeureka();
		
		$display.='<fieldset><legend>Další</legend>';
		$checkbox="<input type=\"checkbox\" name=\"ZBOZI_ROUND_HEUREKA\" value='1' style=\"color:blue\"";
		if(Configuration::get("ZBOZI_ROUND_HEUREKA"))
		 $checkbox.=" checked=\"checked\" ";
		$checkbox.="/>Ceny v celých Kč"; 
		$display .=$checkbox."<br /> ";
		
		$checkbox="<input type=\"checkbox\" name=\"ZBOZI_DAREK_HEUREKA\" value='1' style=\"color:blue\"";
		if(Configuration::get("ZBOZI_DAREK_HEUREKA"))
		 $checkbox.=" checked=\"checked\" ";
		$checkbox.="/>Možnost dárku"; 
		$display .=$checkbox." Přidávat dárky k produktům <br />";
        
        $display.='Společný dárek: <input type ="text" name = "ZBOZI_DAREK_HEUREKANAME" value ="'.Configuration::get('ZBOZI_DAREK_HEUREKANAME').'" />  <br />';
		
        $display.='Dárky se k produktům přidávají podle nastavených pravidel košíku s vazbou na produkt. Navíc pokud vyplníte společný dárek,
        tak se tento dárek objeví u produktů s nastaveným  Zbozi: EXTRA_MESSAGE, viz rozšířené vlastnosti a detail produktu<br /><br />'.
		$checkbox="<input type=\"checkbox\" name=\"ZBOZI_ACCESSORY_HEUREKA\" value='1' style=\"color:blue\"";
		if(Configuration::get("ZBOZI_ACCESSORY_HEUREKA"))
		 $checkbox.=" checked=\"checked\" ";
		$checkbox.="/>Příslušenství"; 
		$display .=$checkbox."<br /><br />";
		

		$display.='</fieldset>';
		 $display.=  '<input class="button" name="cmd_heureka" value="Uložit změny" type="submit" /> 
        </form>';
        return $display;
	}
	
		protected function displayHeurekaMapAdvanced() {
		$display='';
		  if(Shop::isFeatureActive())
		    $langs=Language::getLanguages(true, Shop::getContextShopID());
		    else
		    $langs=Language::getLanguages(true, false);
        
    
         $module_url = $this->instance->getModuleUrl();
		 foreach($langs as $lang) {
             if($this->instance->fancy) {  
		           $display.='<a  class="mapbutton" href="#" onclick="fbox(\'heureka\','.$lang['id_lang'].');return false">Namapovat kategorie '.$lang['name'].'</a><br />';
             } else {
                   $url = $module_url.'/categorymap.php?function=heureka&id_lang='.$lang->id_language.'&id_shop='.Context::getContext()->shop->id.'&nw=1'; 
                   $display.='<a  class="mapbutton" href="'.$url.'" target="_blank" >Namapovat kategorie '.$lang['name'].'</a><br />';
             }
             }
		            $display.='<p>Po kliknutí se objeví centrální editor s našeptávačem.
             Tímto způsobem můžete snadno dosáhnout spárování svého zboží na Heureka.cz.
              Jedná se o pohodlnější alternativu k vyplňování pole Heureka category v  kartě individuálních kategorií (Katalog - Kategorie. 
             </p>
             <p>
            Pokud potřebujete upřesnit párování pro některé produkty, aktivujte nejprve "Rozšířené vlastnosti" v záložce "Rozšíření". Pole se pak objeví v podzáložce "Associace" (Katalog - Produkty - Detail produktu)
             </p>';  
    
          return $display;
	}
    
   
	
	  protected function displayAttributes() {
           $transformed=json_decode(Configuration::get('ZBOZI_TRANSFORMED'), true);
            $pocet=Configuration::get('ZBOZI_TRANSFORMED_COUNT')>0?Configuration::get('ZBOZI_TRANSFORMED_COUNT'):5;
           $display ='<fieldset><legend>Attributy na vlastnosti</legend>';
            $display .='Kombinace zboží se vytvoří automaticky. Nicméně pro Heureka feed může být výhodné přemapovat některé
            Attributy do skupin vlastností. Vytvořené kombinace pak budou mít navíc jeden nebo více tagů PARAM. Více informací najdete v 
            <a href="http://prestahost.eu/navody/index.php/7-modul-zbozi-karta-heureka" target="_blank">manuálu</a>'
            ; 
            
            $skupiny = $this->getSkupiny();
            if($skupiny && is_array($skupiny)) {
                $display.='Skupiny vlastností v eshopu jsou: ';
                foreach($skupiny as $skupina) {
                	$display.=' | '.$skupina['name'];
				}
				$display.='<br />  <a href="http://sluzby.heureka.cz/napoveda/jak-zadat-parametry-do-xml-souboru/" target="_blank">informace k parametrům Heureka</a> a 
				<a href="https://docs.google.com/spreadsheets/d/1bOroHe1jlLabfyLA2WN7ka8Wa940DsXrpE20JBm5zuY/edit?pli=1#gid=0" target="_blank">povinné parametry</a>';
			 }
            $display.='<table><tr><td width="200px">Název attributu v eshopu</td><td width="200px">Požadované jméno parametru pro Heureka</td>';
            for($i=0;$i<$pocet;$i++) {
            $val1=  isset($transformed[$i][0])?(string)$transformed[$i][0]:'';
            $val2=  isset($transformed[$i][1])?(string)$transformed[$i][1]:'';
            $display.='<tr><td><input type="text" name="attr['.$i.']" value="'.$val1.'"/></td>
                           <td><input type="text" name="param['.$i.']" value="'.$val2.'"/></td>
                      </tr>';
            }
            $display.='</table>
           <br />
           Počet řádek v tabulce   <input type="text" size=2 name="ZBOZI_TRANSFORMED_COUNT" value="'.$pocet.'"/>
            
            </fieldset>';
            

            return $display;
    }
	
	protected function displayHeurekaMapBasic() {
		  $display.='
             <p>
             Tímto způsobem můžete snadno dosáhnout spárování svého zboží na Heureka.cz. K tomu využijete pole Heureka category které najdete v administraci (karta Kategorie).
             </p>
             <p>
             Do pole se zadávejte  hotnoty   CATEGORY_FULLNAME podle
             <a href="http://www.heureka.cz/direct/xml-export/shops/heureka-sekce.xml" target="_blank">Specifikaci Heureka</a>. Příklad:
             "Heureka.cz | Oblečení a móda | Dětské oblečení | Dětské plavky"
             </p>';  
             
          $display.='<p><b>V placené verzi navíc</b>: 
          <ul>
           <li> centrální editace pole Heureka category s našeptávačem kategorií Heureky.</li>
            <li> možnost párovat i na úrovni jednotlivých produktů.</li>
          </ul></p>'; 
          return $display;
	}
	
	 protected function displayDoprava() {
           $this->carriers=json_decode(Configuration::get('ZBOZI_CARRIERS'), true);
           $this->cods=json_decode(Configuration::get('ZBOZI_CARRIERSCOD'), true);
         $display ='<fieldset><legend>Doprava</legend>
       Zahrnout informace o dopravě pro feed Heureka: <input type="checkbox" value=1 name="ZBOZI_DOPRAVA_ON"';
       
       if(Configuration::get("ZBOZI_DOPRAVA_ON"))
                $display .=' checked=\"checked\"';
        $display.='/><br />';
       
       $heurekaCarriers=array('CESKA_POSTA','CESKA_POSTA_NA_POSTU','CSAD_LOGISTIK_OSTRAVA');
       if(zbozi::version_compare(_PS_VERSION_, '1.5', '<')) {
         $carriers=Carrier::getCarriers(Zbozi::getDefaultLang(), true,   false,           false,           null,       5 );
       }
       else
         $carriers=Carrier::getCarriers(Zbozi::getDefaultLang(), true,   false,           false,           null,                Carrier::ALL_CARRIERS );
       
        $display.='Do pole Dopravce heureka dopište aktuální <a href="http://sluzby.heureka.cz/napoveda/xml-feed/#DELIVERY" target="_blank">zkratky podporovaných dopravců</a> <br />
        Příklady: <br /> <span style="font:arial">CESKA_POSTA, CESKA_POSTA_NA_POSTU, PPL, DPD, DHL,  VLASTNI_PREPRAVA</span><br />.
        ';
      $display.='<table><td>Dopravce</td><td>Dopravce heureka</td><td>COD</td></tr>'; 
       foreach($carriers as $carrier) {
            $val='';
            $cod=0;
            if(isset($this->carriers[$carrier['id_carrier']])) {
                  $val=  $this->carriers[$carrier['id_carrier']];
            }
             if(isset($this->cods[$carrier['id_carrier']])) {
                  $cod=  $this->cods[$carrier['id_carrier']];
            }
            
             $display.='<tr>'; 
            $display.='<td>'.$carrier['name'].'</td><td><input type="text" name="carrier['.$carrier['id_carrier'].']" value="'.$val.'"></td>';
            $display.='</td><td><input type="text" name="carriercod['.$carrier['id_carrier'].']" value="'.$cod.'"></td>';
            $display.='</tr>'; 
       }
       $display.='</table>'; 
       
        $display.=' 
        <input type="checkbox" value=1 name="ZBOZI_DOPRAVACOD"';
        if(Configuration::get("ZBOZI_DOPRAVACOD"))
                $display .=' checked=\"checked\"';
        $display.='/> Pokud je pro produkt doprava zdarma, je zdarma i dobírečné
        
        <br />';
       
       $display.='</fieldset>';
       return $display;
    }
    
      protected function displayOptimisationHeureka() {
      $optim=json_decode(Configuration::get('ZBOZI_OPTIM'), true); 
               
      $display =' <fieldset><legend>Nastavení tagů PRODUCT a PRODUCTNAME</legend>';
      
     $display.='<table  cellpadding="10"><tr> <td>Feed</td><td>Tag feedu</td><td>Hodnota</td> <td>Pořadí</td><td>Použít?</td> </tr>'; 
     
      $feednames=array('Heureka');
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
      
      $checkbox="</table><br /><input type=\"checkbox\" name=\"ZBOZI_HEUREKA_SLEVA\" value='1' style=\"color:blue\"";
		if(Configuration::get("ZBOZI_HEUREKA_SLEVA"))
		 $checkbox.=" checked=\"checked\" ";
		$checkbox.="/>Přidat k PRODUCT informace o slevě (<i>zahrne pouze slevy které nemají žádné omezení mimo časového</i>)"; 
		$display .=$checkbox."<br /> ";
         
     $display .='
     <h4>Individuální rozšíření názvu produktů</h4>
     <ul>
     <li>V záložce Rozšíření povolte rozšířené vlastnosti produktů</li>
     <li>Katalog - Produkty - Karta produktu - podzáložka Informace - vyplňte rozšiřující text</li>
     <li>Vyplněný text bude přidán  do elementu PRODUCT</li>
     </ul>
     <br />'; 
     
      
      $display .='</fieldset>';
       return $display;
   } 
  }
 
