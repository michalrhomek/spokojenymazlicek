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
  class GoogleController extends ZboziController {
  
  private $carriers;
  
  public function postProcess() {
      
             if(isset($_POST['cmd_google'][1])){
             $cats = explode(',', Tools::getValue('ZBOZI_CATS_FORBIDDENgo'));
            $all_cats = array();
            foreach($cats as $cat) {
                $all_cats[]=$cat;
                $childs =array();
                $this->childsRecursive($cat, $childs);
                $all_cats=array_merge($all_cats, $childs);
            }
            $all_cats = array_unique($all_cats);
            $s = implode(',',$all_cats);
            Configuration::updateValue('ZBOZI_CATS_FORBIDDENgo',  $s); 
            return;      
           }
  	       if(isset($_POST['cmd_google'][0])){
	        $googleAttributes=array();
		   
		   if(Shop::isFeatureActive())
		    $langs=Language::getLanguages(true, Shop::getContextShopID());
		    else
		    $langs=Language::getLanguages(true, false);
		    
		    $from=Zbozi::getDefaultLang(); 
		    $keys = array( 'color', 'material', 'pattern',  'size');
		    foreach($langs as $lang) {
	            for($i=0;$i<count($_POST['gattr']); $i++) {
	            	if(!empty($_POST['gattr'][$i]))
            			$googleAttributes[$lang['id_lang']][$this->translateAttribute($_POST['gattr'][$i], $from, $lang['id_lang'])]=$keys[$i];
            	 
				}
			}
             Configuration::updateValue('ZBOZI_GATTRIBUTES', json_encode($googleAttributes)); 
             Configuration::updateValue('ZBOZI_DOPRAVAG_ON', intval(Tools::getValue('ZBOZI_DOPRAVAG_ON'))); 
             
               $carriers=array();
            if(isset($_POST['carrierG'])) {
            while(list($key,$val)=each($_POST['carrierG'])){
              $carriers[$key]=$val;
            }  
			}
            Configuration::updateValue('ZBOZI_CATS_FORBIDDENgo', Tools::getValue('ZBOZI_CATS_FORBIDDENgo')); 
             Configuration::updateValue('ZBOZI_CARRIERSG', json_encode($carriers)); 
             
             if((int)Tools::getValue('ZBOZI_GIDENF') == 1)
                Configuration::updateValue('ZBOZI_GIDENF', 1); 
             else
                Configuration::updateValue('ZBOZI_GIDENF', 0); 
                
              
                
              $this->showSaveResult();
           } 
		    
  	  
  }
  	  
  	  public function getContent($tabnum) {
  	  	  $this->carriers=json_decode(Configuration::get('ZBOZI_CARRIERSG'), true);
		$display='<form action="'.$_SERVER['REQUEST_URI'].'" method="post">';
		$display.='<input type="hidden" name="currentTab" value="'.$tabnum.'" />';
		if($this->instance->do_attributes) {
		  $display.='<fieldset><legend>Mapování kategorií Google</legend>';
		    $display.='<p class="napoveda">
       	   <a class="napoveda" href="http://prestahost.eu/navody/index.php/9-modul-zbozi-karta-google" target="_blank">Nápověda (google)</a>
       	   <a class="napoveda" href="http://prestahost.eu/navody/index.php/8-modul-zbozi-mapovani-kategorii" target="_blank">Nápověda (mapování kategorií)</a>
       	   </p>';
       	   if(Shop::isFeatureActive())
		    $langs=Language::getLanguages(true, Shop::getContextShopID());
		    else
		    $langs=Language::getLanguages(true, false);
		  
          $module_url = $this->instance->getModuleUrl();
         foreach($langs as $lang) {
             if($this->instance->fancy) {  
                   $display.='<a  class="mapbutton" href="#" onclick="fbox(\'google\','.$lang['id_lang'].');return false">Namapovat kategorie '.$lang['name'].'</a><br />';
             } else {
                   $url = $module_url.'/categorymap.php?function=google&id_lang='.$lang->id_language.'&id_shop='.Context::getContext()->shop->id.'&nw=1'; 
                   $display.='<a  class="mapbutton" href="'.$url.'" target="_blank" >Namapovat kategorie '.$lang['name'].'</a><br />';
             }
             }
         
         
          
          $display.='<p>Po kliknutí se objeví centrální editor s našeptávačem.
             Tímto způsobem můžete snadno dosáhnout spárování svého zboží na Google Nákupy.
             </p></fieldset>';  
    
		
		  $skupiny = $this->getSkupiny();
		 
		  $display.='<fieldset><legend>Kombinace zboží Google</legend>';
             $display.='Google používá pro kombinace pouze následující vlastnosti:
             ‘color’, ‘material’, ‘pattern’, ‘size’. Pokud se odpovídající skupiny vlastností na eshopu jmenují jinak,
             je potřeba je přiřadit níže. Můžete přiřadit více vlastností oddělených <b>|</b> pochopitelně pokud se nikdy
             nevyskytnou u téže kombinace.  <br />';
             
             if($skupiny && is_array($skupiny)) {
                $display.='Skupiny vlastností v eshopu jsou: ';
                foreach($skupiny as $skupina) {
                	$display.=' | '.$skupina['name'];
				}
			 }
         
            $display.='<table><tr><td width="200px">Jméno Google</td><td width="200px">Název attributu v eshopu ('.$this->instance->getDefaultLangIso().')</td>';
            $keys=array('color', 'material', 'pattern', 'size');
            $i=0;
            $googleAttrib=json_decode(Configuration::get('ZBOZI_GATTRIBUTES'), true);
          
           if(is_array($googleAttrib) && isset($googleAttrib[Zbozi::getDefaultLang()])) {
            $googleAttributes = $googleAttrib[Zbozi::getDefaultLang()]; 
            $googleAttributes= array_flip($googleAttributes); 
			}
            foreach($keys as $key) {
            	$val=isset($googleAttributes[$key])?$googleAttributes[$key]:'';
                $display.='<tr><td>'.$key.'</td><td><input type="text" size="50" name="gattr['.$i.']" value="'.$val.'"/></td></tr>';
                $i++;
			}
            $display.='</table></fieldset>';
           
        }
        else {
        	  $display.='Modul umožňuje tvorbu feedu pro Google nákupy';
        	  $display.= 'V placené verzi navíc:<ul>
        	  <li>Export kombinací zboží</li>
        	  <li>Převod vlastností produktů podle požadavků Google</li>
        	  <li>Centrální mapování do kategorií podle Google taxonomie</li>
        	  </ul>';
		}
		
       $display.='<fieldset><legend>Doprava</legend>
       Zahrnout informace o dopravě pro feeed Google
       <input type="checkbox" value=1 name="ZBOZI_DOPRAVAG_ON"';
       if(Configuration::get("ZBOZI_DOPRAVAG_ON"))
                $display .=' checked=\"checked\"';
        $display.='/><br /><br />';
        
        
       if(zbozi::version_compare(_PS_VERSION_, '1.5', '<'))
         $carriers=Carrier::getCarriers(Zbozi::getDefaultLang(), true,   false,           false,           null,       5 );
       else
        $carriers=Carrier::getCarriers(Zbozi::getDefaultLang(), true,   false,           false,           null,                Carrier::ALL_CARRIERS );
        $display.='Dopravci pro export - podle směrnic Google by neměli být exportování
        dopravci typu "osobní odběr"</span><br />.
        ';
      $display.='<table><td>Dopravce</td><td>Povolen</td></tr>'; 
       foreach($carriers as $carrier) {
       	   $checked='';
       	   if(isset($this->carriers[$carrier['id_carrier']])) {
                  $checked=  ' checked="checked"';
            }
           
            
             $display.='<tr>'; 
            $display.='<td>'.$carrier['name'].'</td><td>
            <input type="checkbox" name="carrierG['.$carrier['id_carrier'].']" value="1" '.$checked.'></td>';
  
            $display.='</tr>'; 
       }
       $display.='</table>';
      
      $display .='  <br /></fieldset>'; 
       
        $display.='<fieldset><legend>Zakázané kategorie</legend>'; 
       $display.='<br />zakázané kategorie jen pro tento feed   <br />
        <textarea rows="2" cols="40" name="ZBOZI_CATS_FORBIDDENgo">'.Configuration::get('ZBOZI_CATS_FORBIDDENgo').'</textarea>'; 
        $display.='<input type="submit" name="cmd_google[1]" value="Přidat podkategorie" />'; 
        
     $display.='<fieldset><legend>Identifikátory</legend>'; 
      $checked ='';  
      if((int)Configuration::get('ZBOZI_GIDENF') ==1 ) 
      $checked = ' checked="checked"';
      $display.='<br /><input type="checkbox" name="ZBOZI_GIDENF" value="1" '.$checked.'> Doplňovat identifier_exists: FALSE pokud nejsou k dispozici aspoň 2 povinné identifikátory';

      $display.='</fieldset>'; 
        
		  $display.=  '<input class="button" name="cmd_google[0]" value="Uložit změny" type="submit" />';
        $display.= '</form>'; 
        return $display;
	}
  	  
  }
