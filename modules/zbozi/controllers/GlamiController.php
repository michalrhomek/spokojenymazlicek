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
  class GlamiController extends ZboziController {
  
  private $carriers;
  
  public function postProcess() {
            if(isset($_POST['cmd_glami'][1])){
             $cats = explode(',', Tools::getValue('ZBOZI_CATS_FORBIDDENgl'));
            $all_cats = array();
            foreach($cats as $cat) {
                $all_cats[]=$cat;
                $childs =array();
                $this->childsRecursive($cat, $childs);
                $all_cats=array_merge($all_cats, $childs);
            }
            $all_cats = array_unique($all_cats);
            $s = implode(',',$all_cats);
            Configuration::updateValue('ZBOZI_CATS_FORBIDDENgl',  $s); 
            return;      
           }
         
  	       if(isset($_POST['cmd_glami'][0])){

             Configuration::updateValue('ZBOZI_DOPRAVAGL_ON', intval(Tools::getValue('ZBOZI_DOPRAVAGL_ON'))); 
             Configuration::updateValue('ZBOZI_GLNAME', intval(Tools::getValue('ZBOZI_GLNAME'))); 
             Configuration::updateValue('ZBOZI_GLCATMODE', intval(Tools::getValue('ZBOZI_GLCATMODE'))); 
             
             
             Configuration::updateValue('ZBOZI_CATS_FORBIDDENgl', Tools::getValue('ZBOZI_CATS_FORBIDDENgl')); 
             Configuration::updateValue('ZBOZI_FILTERATR_GLAMI', intval(Tools::getValue('ZBOZI_FILTERATR_GLAMI')));
            
            if(isset($_POST['ZBOZI_USEDATTR_GLAMI']))   
             Configuration::updateValue('ZBOZI_USEDATTR_GLAMI', json_encode($_POST['ZBOZI_USEDATTR_GLAMI'])); 
            else 
             Configuration::updateValue('ZBOZI_USEDATTR_GLAMI', null); 
                
              $this->showSaveResult();
           } 
		    
  	  
  }
  	  
  	  public function getContent($tabnum) {
  	  	  $this->carriers=json_decode(Configuration::get('ZBOZI_CARRIERSG'), true);
		$display='<form action="'.$_SERVER['REQUEST_URI'].'" method="post">';
	    $display.='<input type="hidden" name="currentTab" value="'.$tabnum.'" />';
		
       $display.='<fieldset><legend>Nastavení</legend>';
       
       $display.=$this->displayCategoryMap();
       
       $display.='<input type="radio" name ="ZBOZI_GLCATMODE" value="0"';
       if((int)Configuration::get('ZBOZI_GLCATMODE') == 0)
       $display.=' checked="checked"';
       
       $display.='/>';
       $display.=' pouze předřadit kategorii';
       $display.=' | ';
       $display.='<input type="radio" name ="ZBOZI_GLCATMODE" value="1"';
         if((int)Configuration::get('ZBOZI_GLCATMODE') == 1)
       $display.=' checked="checked"';
       
       $display.='/>';
         $display.=' namapovat celou cestu';
       $display.= '<br /><br />';
       
       $display.='Převzít informace o dopravě z feedu Heureka
       <input type="checkbox" value=1 name="ZBOZI_DOPRAVAGL_ON"';
       if(Configuration::get("ZBOZI_DOPRAVAGL_ON"))
                $display .=' checked=\"checked\"';
        $display.='/><br />';
        $display .= $this->restrictedAttributes('glami'); 
        $display.='Nastavení PRODUCT a PRODUCTNAME podle: ';
        $display.=' <input type="radio" name="ZBOZI_GLNAME" value="0"';
        if((int)Tools::getValue('ZBOZI_GLNAME')  == 0)
        $display.=" checked='checked'";
        $display.='> Heureka | ';
         $display.=' <input type="radio" name="ZBOZI_GLNAME" value="1"';
        if((int)Tools::getValue('ZBOZI_GLNAME')  == 1)
        $display.=" checked='checked'"; 
        $display.='> Zboží ';
        
        
        $display.='<br /></fieldset>'; 
       
        $display.='<fieldset><legend>Zakázané kategorie</legend>'; 
       $display.='<br />zakázané kategorie jen pro tento feed   <br />
        <textarea rows="2" cols="40" name="ZBOZI_CATS_FORBIDDENgl">'.Configuration::get('ZBOZI_CATS_FORBIDDENgl').'</textarea>'; 
        $display.='<input type="submit" name="cmd_glami[1]" value="Přidat podkategorie" />'; 
        
         if((int)(Configuration::get('ZBOZI_CATSPERPRODUCT') == 1)) {
            $display.='Omezení na  úrovni produktu nastavíte v detailu produktu, karta Informace';
        }
        else {
            $display.='Omezení na úrovni produktu povolte   v kartě Rozšíření<br />';
        }
        
		  $display.=  '<br /> <br /><br /><input class="button" name="cmd_glami[0]" value="Uložit změny" type="submit" />';
        $display.= '</form>'; 
        return $display;
	}
    
        protected function displayCategoryMap() {
        $display='';
          if(Shop::isFeatureActive())
            $langs=Language::getLanguages(true, Shop::getContextShopID());
            else
            $langs=Language::getLanguages(true, false);
        
    
         $module_url = $this->instance->getModuleUrl();
         foreach($langs as $lang) {
             if($this->instance->fancy) {  
                   $display.='<a  class="mapbutton" href="#" onclick="fbox(\'glami\','.$lang['id_lang'].');return false">Namapovat kategorie '.$lang['name'].'</a><br />';
             } else {
                   $url = $module_url.'/categorymap.php?function=glami&id_lang='.$lang->id_language.'&id_shop='.Context::getContext()->shop->id.'&nw=1'; 
                   $display.='<a  class="mapbutton" href="'.$url.'" target="_blank" >Namapovat kategorie '.$lang['name'].'</a><br />';
             }
             }
                    $display.='<p>Po kliknutí se objeví centrální  
             
             </p>'
            ;  
    
          return $display;
    }
  	  
  }
