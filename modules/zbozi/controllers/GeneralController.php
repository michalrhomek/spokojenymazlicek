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
  class GeneralController extends ZboziController {
    
     public function postProcess() {
     	 
    if(isset($_POST['cmd_general'][0])){
             
        
        
    	 	 $keys=array('ZBOZI_PARTIAL_UNISTALL', 'ZBOZI_SKLADEM', 'ZBOZI_VISIBILITY',  'ZBOZI_MULTIPLE_IMAGES',  'ZBOZI_GROUP');
			  foreach($keys as $key) {
			  	   Configuration::updateValue($key,(int) Tools::getValue($key)); 
			  }
			  $keys=array('ZBOZI_DESCRIPTION','ZBOZI_DESCRIPTION_MAX','ZBOZI_IMG','ZBOZI_CPC', 'ZBOZI_CATS_FORBIDDEN', 'ZBOZI_ATT_SEPARATOR');
			  foreach($keys as $key) {
			  	   Configuration::updateValue($key, Tools::getValue($key)); 
			  }
              
              $keys = array('ZBOZI_PARTIAL_UNISTALL', 'ZBOZI_CATS_FORBIDDEN_REVERSE', 'ZBOZI_ATTR_PUBLIC', 'ZBOZI_ATTR_IDS', 
              'ZBOZI_UNITPRICE');
               $old_attr=Configuration::get('ZBOZI_ATTR_PUBLIC');  
              foreach($keys as $key) {
              Configuration::updateValue($key, intval(Tools::getValue($key))); 
              }
			  
			      
			  $new_attr=Configuration::get('ZBOZI_ATTR_PUBLIC'); 
			  
			  if($old_attr != $new_attr) {
			     $this->clearAttrCache();
			  } 
              

			  $keys=array( 'ZBOZI_ALLOWTAGS' );
               $use_html_purifier = Configuration::get('PS_USE_HTMLPURIFIER');
             Configuration::updateValue('PS_USE_HTMLPURIFIER', 0); 
			  foreach($keys as $key) {
			  	   Configuration::updateValue($key, Tools::getValue($key), true); 
			  }
              Configuration::updateValue('PS_USE_HTMLPURIFIER', $use_html_purifier); 
              
			  return  $this->showSaveResult();    
		 }
		 
		 
  }
  
	public function getContent($tabnum) { 
		$display=$this->displayIntro().'<br />';  
		$display.=$this->showFeedScripts().' - viz. záložka feedy <br /><br />';  
		$display.='<form action="'.$_SERVER['REQUEST_URI'].'" method="post">';
		$display.='<input type="hidden" name="currentTab" value="'.$tabnum.'" />';
		$display.=' <fieldset><legend>Základní nastavení</legend>';  
		$display.='<p class="napoveda">
		<a class="napoveda" href="http://prestahost.eu/navody/index.php/4-karta-zakladni-nastaveni" target="_blank">Nápověda</a>
		</p>';
		
        $stav = (int)Configuration::get("ZBOZI_SKLADEM");  
		$display.='Exportovat zboží které není skladem: <select name="ZBOZI_SKLADEM">
		<option value="0"';
		if($stav == 0)
		$display .= ' selected ="selected"';
		$display.='>vždy</option>
		<option value="1"';
		if($stav == 1)
		$display .= ' selected ="selected"';

		$display.='>nikdy</option>
		<option value="2"';
		if($stav == 2)
		$display .= ' selected ="selected"';

		$display.=' >jen pokud lze objednat</option></select><br />';
        if(!(int)Configuration::get('PS_STOCK_MANAGEMENT')) {
          
        $display .= " <b>POZOR řízení skladu je vypnuto. Zaškrtněte pouze pokud opravdu udržujete číselné zásob</b> &nbsp; ";
        } 
        
        $stav = (int)Configuration::get("ZBOZI_VISIBILITY");  
        $display.='Exportovat zboží s viditelností: <select name="ZBOZI_VISIBILITY">
        <option value="0"';
        if($stav == 0)
        $display .= ' selected ="selected"';
        $display.='>všude</option>
        <option value="1"';
        if($stav == 1)
        $display .= ' selected ="selected"';

        $display.='>Pouze katalog</option>';
        

        $display.='</select> (nastavená viditelnost v kartě produktu, záložka Informace)<br />';

		

		



		$display .='<br />
		Velikosti obrázků: <select name ="ZBOZI_IMG">';

		$images=ImageType::getImagesTypes('products');
		$selimage=Configuration::get("ZBOZI_IMG");
		if(empty($selimage))
		$selimage=$images[0]['name'];
		foreach($images as $image) {
		if($image['height'] < 1000 &&  $image['width'] < 1000) {
		$display .="<option value=\"".$image['name']."\"";
		if($image['name'] == $selimage)
		$display.=" selected=\"selected\"";
		$display.=">".$image['name'].' ('.$image['width'].'x'.$image['height'].")</option>";
		}
		}    
		$display .='</select>
		vyberte jakou velikost obrázků ve feedu chcete použít
		<br />';
		
		$checkbox="<input type=\"checkbox\" name=\"ZBOZI_MULTIPLE_IMAGES\" value='1' style=\"color:blue\"";
		if(Configuration::get("ZBOZI_MULTIPLE_IMAGES"))
		$checkbox.=" checked=\"checked\" ";
		$checkbox.="/>Exportovat všechny obrázky produktu"; 
		$display .=$checkbox."<br /><br />";
		  
		$display .='
		Pole pro popis: <select name ="ZBOZI_DESCRIPTION">';
		$keys=array('description_short', 'description');
		foreach($keys as $key) {
		$display .="<option value=\"$key\"";
		if($key == Configuration::get('ZBOZI_DESCRIPTION'))
		$display.=" selected=\"selected\"";
		$display.=">$key</option>";
		}    
		$display .='</select><br />';
        
        

		$max=(int)Configuration::get('ZBOZI_DESCRIPTION_MAX') > 100?(int)Configuration::get('ZBOZI_DESCRIPTION_MAX'):510;
		$display.= '<input  type="text" name="ZBOZI_DESCRIPTION_MAX"  value="'.$max.'" /> (min 100, max 5000)';

        
      $display .=' <br />
        Zákaznická skupina: <select name ="ZBOZI_GROUP">';
        $groups = Group::getGroups(Context::getContext()->language->id);
        $selected = Configuration::get('ZBOZI_GROUP')?Configuration::get('ZBOZI_GROUP'):Configuration::get('PS_CUSTOMER_GROUP');
        foreach($groups as $group) {
        $display .="<option value=\"{$group['id_group']}\"";
        if($group['id_group'] == $selected)
        $display.=" selected=\"selected\"";
        $display.=">{$group['name']}</option>";
        }    
        $display .='</select><br />';  
        

		$display.='Vynechané kategorie - ID kategorií oddělené čárkou: <br />';
		$display.='<textarea rows="4" cols="60" name="ZBOZI_CATS_FORBIDDEN">'.Configuration::get('ZBOZI_CATS_FORBIDDEN').'</textarea>'; 
		$display.='<input type="checkbox"  name="ZBOZI_CATS_FORBIDDEN_REVERSE" value="1"';
		if(Configuration::get('ZBOZI_CATS_FORBIDDEN_REVERSE'))
		$display.=' checked="checked"';
		$display.='/>Opačné chování - použít jen tyto kategorie<br />';
       if((int)(Configuration::get('ZBOZI_CATSPERPRODUCT') == 1)) {
            $display.='Omezení na  úrovni produktu nastavíte v detailu produktu, karta Informace';
        }
        else {
            $display.='Omezení na úrovni produktu povolte   v kartě Rozšíření<br />';
        }
    
		if(!$this->instance->do_attributes) {  
		$display .='
		Heureka CPC: <select name ="ZBOZI_CPC">';
		$keys=array('', 'wholesale_price' , 'manufacturer_reference', 'reference', 'ean13' , 'upc');
		$vals=array('nic', 'nákupní cena ' , 'kód zboží dodavatele', 'kód zboží', 'EAN13 nebo JAN', 'UPC');
		$counter=0;
		foreach($keys as $key) {
		$display .="<option value=\"$key\"";
		if($key == Configuration::get("ZBOZI_CPC"))
		$display.=" selected=\"selected\"";
		$display.=">{$vals[$counter]}</option>";
		$counter++;
		}    
		$display .='</select>  <br />
		Pokud používáte heureka CPC, můžete pro ně využít některé z polí určených pro jiné účely, pokud to neovlivní funkci eshopu.
		Pokud je dané pole u produktu vyplněno, bude hodnota dosazena za <a href="http://sluzby.heureka.cz/napoveda/xml-feed/">HEUREKA_CPC</a>.

		<br />  <br />';   
		}  
		else {
		$display .='<input type="hidden" name="ZBOZI_CPC" value="0" />';    

		$display.=' <br /><br />Používat veřejné názvy pro attributy produktů  
		<input type="checkbox"  name="ZBOZI_ATTR_PUBLIC" value="1"';
		if(Configuration::get('ZBOZI_ATTR_PUBLIC'))
		$display.=' checked="checked"';
		$display.='/>';
        
        $display.=' <br />Url attributů obsahuje ID  
        <input type="checkbox"  name="ZBOZI_ATTR_IDS" value="1"';
        if(Configuration::get('ZBOZI_ATTR_IDS'))
        $display.=' checked="checked"';
        $display.='/> (PS verze > 1.6.1)';   
		}
        
      
        
        $display.=' <br />Separátor názvu produktu a attributu '; 
         $display.= '<input type="text" name="ZBOZI_ATT_SEPARATOR" value="'.Configuration::get('ZBOZI_ATT_SEPARATOR').'" />';

        $display.=' <br />Exportovat jednotkové ceny (např. za m2) 
        <input type="checkbox"  name="ZBOZI_UNITPRICE" value="1"';
        if(Configuration::get('ZBOZI_UNITPRICE'))
        $display.=' checked="checked"';
        $display.='/>';   

        $checkbox="<br /><br />  <input type=\"checkbox\" name=\"ZBOZI_PARTIAL_UNISTALL\" value='1' style=\"color:blue\"";
        if(Configuration::get("ZBOZI_PARTIAL_UNISTALL"))
         $checkbox.=" checked=\"checked\" ";
        $checkbox.="/>Při odinstalaci modulu nebo zakázání rozšířených vlastností produktů  nemazat uložené hodnoty   &nbsp;"; 
        $display.= $checkbox." &nbsp;<br /><br />";
        
        
        
		$display .='

		</fieldset>
		<br />';
	 
		$display.=  '<input class="button" name="cmd_general[0]" value="Uložit změny" type="submit" /> 
		</form>';
		return $display;
	} 
  


	protected  function displayIntro() {
		$display='<table><tr>';
		$display .= '<td>
		Modul pro export zboží do služby  heureka.cz, zbozi.cz a Google nákupy.  Feed Heureka je bez úprav použitelný
		i pro většinu dalších srovnávačů.<br />  <br />
		<ul>
		<li>použitelný i pro velké eshopy s tisíci kusů zboží</li>
		<li>podpora multishop</li>
		<li>vynechání vybraných produktů</li>
		<li>přesné párování Heureka</li>
		<li>export dopravy</li>
		<li>podrobné nastavení dostupnosti</li>';

		if($this->instance->do_attributes==1)
		$display .= '<li><b>varianty zboží</b></li>  
		<li><b>cpc pro Heureka i Zbozi</b></li>  
		<li><b>zjednodušené mapování do Google a Heureka kategorií</b></li>  
		</ul> ';
		else
		$display .= '<li style="color:red"><b>nelze exportovat varianty zboží</b>. 
		<br /> Pro export variant a pohodlnější párování je potřeba modul 
		<b> <a href="http://prestahost.eu/prestashop-modules/cs/import-export/20-export-heureka-zbozi-varianty-produktu.html" target="_blank" style="color:blue">Zbožíplus</a></b>

		</li>  </ul></td></tr> ';

		$display .='<td><a href="http://www.prestahost.cz" target="_blank"><img src="../modules/'.$this->instance->name.'/prestahost.gif"></a> 
		<br />  
		<b><a href="http://www.prestahost.cz" target="_blank">Prestahost.cz</a> </b>: 
		<ul>
		<li>česká podpora Prestashopu</li>
		<li>specializovaný hosting</li>
		<li>moduly na míru</li>
		<li><b>import xml feedů dodavatelů</b> (pokročilý modul zajišťující importy a pravidelné update zboží, informace na 
		<a href="mailto:info@prestahost.cz">info@prestahost.cz</a></li>
		</ul>
		</td></tr> ';  
		return $display.'</table>';
	}

	
	private function clearAttrCache() {
	    $cache_path=dirname(__FILE__).'/cache/'.Context::getContext()->shop->id;
	    if(file_exists($cache_path))
	       unlink($cache_path);
}
}
