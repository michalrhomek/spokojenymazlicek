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
  class ExtendedController extends ZboziController {
    
     public function postProcess() {
     	 
      if(isset($_POST['cmd_extended'][0])){
		 	
         
            if((int)Configuration::get('ZBOZI_CATSPERPRODUCT') == 0) {
               
                $ZBOZI_CATSPERPRODUCT=1;
                $path= _PS_ADMIN_DIR_.'/themes/default/template/controllers/products/associations.tpl';
                if(file_exists($path)) {
                if(!$this->isAlreadyIncluded($path, 'heureka.tpl')) 
                   file_put_contents($path, "{include file=\"controllers/products/heureka.tpl\"}", FILE_APPEND);
                }
                else  {
                    $ZBOZI_CATSPERPRODUCT=0;
                }
                
                $path= _PS_ADMIN_DIR_.'/themes/default/template/controllers/products/informations.tpl';
                if(file_exists($path)) {
                if(!$this->isAlreadyIncluded($path, 'zbozi_text.tpl')) 
                file_put_contents($path, "{include file=\"controllers/products/zbozi_text.tpl\"}", FILE_APPEND);
                }
                else  {
                $ZBOZI_CATSPERPRODUCT=0;
                }

                if($ZBOZI_CATSPERPRODUCT) {       
                    
                    if(!$this->instance->addSqlField('product','heureka_category')) {
                    $this->warning[]='Nepodařilo se přidat pole Heureka Category k tabulce produkty, proto nebyly instalovány soubory pro práci s tímto polem';   
                    $ZBOZI_CATSPERPRODUCT=0;
                    }
                    
                   $sql='SELECT column_name
                    FROM information_schema.columns 
                    WHERE table_schema = "'._DB_NAME_.'" 
                    AND table_name   = "'._DB_PREFIX_.'product_lang"
                    AND column_name  = "zbozi_text"';
                    $column_exists=Db::getInstance()->getValue($sql);
                   if( $column_exists === false) {
                   $added =1;
                  if(!$this->instance->addSqlField('product_lang','zbozi_text')) {
                     $this->warning[]='Nepodařilo se přidat pole pro rozšiřující text k tabulce product_lang, proto nebyly instalovány soubory pro práci s tímto polem';   
                    $ZBOZI_CATSPERPRODUCT=0;
                    $added--;
                  } 
                  if(!$this->instance->addSqlField('product_lang','heureka_text')) {
                     $this->warning[]='Nepodařilo se přidat pole pro rozšiřující text k tabulce product_lang, proto nebyly instalovány soubory pro práci s tímto polem';   
                    $ZBOZI_CATSPERPRODUCT=0;
                    $added--;
                    }
                    
                    if($added) {
                    $this->instance->consolidateSqlFields(1); 
                    }
                   
                   }
                  if(!$this->instance->addSqlField('product','videourl')) {
                     $this->warning[]='Nepodařilo se přidat pole pro rozšiřující text videourl k tabulce produkty';  
                     $ZBOZI_CATSPERPRODUCT=0;   
                    
                    }
                   if(!$this->instance->addSqlField('product','productline')) {
                     $this->warning[]='Nepodařilo se přidat pole pro rozšiřující text productline k tabulce produkty';  
                     $ZBOZI_CATSPERPRODUCT=0;   
                    
                    }
                  if(!$this->instance->addSqlField('product','extramessage')) {
                     $this->warning[]='Nepodařilo se přidat pole pro  extramessage k tabulce produkty'; 
                     $ZBOZI_CATSPERPRODUCT=0;  
                    }
                  
                  else {                           
                     if(!$this->instance->registerHook('actionAdminProductsControllerSaveBefore')) {
                       $this->warning[]='Nepodařilo se  nainstalovat hook actionAdminProductsControllerSaveBefore'; 
                     $ZBOZI_CATSPERPRODUCT=0;    
                     }
                  }
                  
                   if(!$this->instance->addSqlField('product_shop','skipfeeds')) {
                     $this->warning[]='Nepodařilo se přidat pole pro  pro vynechání produktů k tabulce product_shop'; 
                     $ZBOZI_CATSPERPRODUCT=0;  
                    }
                   if(!$this->instance->addSqlField('product','skipfeeds')) {
                     $this->warning[]='Nepodařilo se přidat pole pro  pro vynechání produktů k tabulce product'; 
                     $ZBOZI_CATSPERPRODUCT=0;  
                    }
                  
                   if(!$this->instance->addSqlField('product','heureka_cpc')) {
                     $this->warning[]='Nepodařilo se přidat pole pro heureka cpc k tabulce produkty, proto nebyly instalovány soubory pro práci s tímto polem';   
                    $ZBOZI_CATSPERPRODUCT=0;
                    }
                     if(!$this->instance->addSqlField('product','max_cpc')) {
                     $this->warning[]='Nepodařilo se přidat pole pro max cpc k tabulce produkty, proto nebyly instalovány soubory pro práci s tímto polem';   
                    $ZBOZI_CATSPERPRODUCT=0;
                    }
                      if(!$this->instance->addSqlField('product','max_cpc_search')) {
                     $this->warning[]='Nepodařilo se přidat pole pro max cpc search k tabulce produkty, proto nebyly instalovány soubory pro práci s tímto polem';   
                    $ZBOZI_CATSPERPRODUCT=0;
                    }
                 
                }
                
                if($ZBOZI_CATSPERPRODUCT) { 
                    if(!$this->instance->installOverridesP(array(
                    0=>array('source'=>_PS_MODULE_DIR_.$this->instance->name.'/install/Product.php',
                    'target'=>_PS_OVERRIDE_DIR_.'classes/Product.php',
                    'targetdir'=>_PS_OVERRIDE_DIR_.'classes/'),
                    1=>array('source'=>_PS_MODULE_DIR_.$this->instance->name.'/install/heureka.tpl',
                    'target'=>_PS_ADMIN_DIR_.'/themes/default/template/controllers/products/heureka.tpl',
                    'targetdir'=>_PS_ADMIN_DIR_.'/themes/default/template/controllers/products/'),
                     2=>array('source'=>_PS_MODULE_DIR_.$this->instance->name.'/install/zbozi_text.tpl',
                    'target'=>_PS_ADMIN_DIR_.'/themes/default/template/controllers/products/zbozi_text.tpl',
                    'targetdir'=>_PS_ADMIN_DIR_.'/themes/default/template/controllers/products/')
                    )) )
                    $ZBOZI_CATSPERPRODUCT=0;
                }   

                if($ZBOZI_CATSPERPRODUCT) {
                    if(file_exists(_PS_ROOT_DIR_.'/cache/class_index.php'))
                    unlink(_PS_ROOT_DIR_.'/cache/class_index.php');           
                    Configuration::updateValue('ZBOZI_CATSPERPRODUCT',1);   
                } 
              
               if(!$this->instance->addSqlField('product_shop','lowest_category', 'int')) {
                $this->warning[]='Nepodařilo se přidat pole pro alternativní výchozí kategorii';   
    
                }
                  $this->instance->installModuleTab('AdminCpc', 'Cpc', 'AdminCatalog');
                 
                  $target=_PS_OVERRIDE_DIR_.'controllers/admin/templates';
                  if(!file_exists($target))
                    mkdir($target);
                  $dirs =array('cpc', 'helpers', 'list');
                  foreach($dirs as $dir) {
                    $target.='/'.$dir;
                     if(!file_exists($target))
                        mkdir($target);
				  }
				  
				  if(file_exists( $target.'/list_footer.tpl'))
				    unlink( $target.'/list_footer.tpl');
				  $source=_PS_MODULE_DIR_.$this->instance->name.'/override/controllers/admin/templates/cpc/helpers/list/list_footer.tpl';   
                  copy($source,  $target.'/list_footer.tpl');
               
            }
            elseif((int)Configuration::get('ZBOZI_CATSPERPRODUCT') ==1 ) {
                 $this->instance->unregisterHook('actionAdminProductsControllerSaveBefore');
                 $this->instance->unistallExtendedProduct();
            } 
            if(is_array($this->instance->_postErrors) && count($this->instance->_postErrors))
              $this->showSaveResult($this->instance->_postErrors);
            return $this->showSaveResult($this->warning); 
            
		 }
		 
		  if(isset($_POST['cmd_extended'][1])){
		 	 Configuration::updateValue('ZBOZI_TEXT_EXT', intval(Tools::getValue('ZBOZI_TEXT_EXT')));  
             Configuration::updateValue('ZBOZI_TEXT_EXTATT', intval(Tools::getValue('ZBOZI_TEXT_EXTATT'))); 
             
		  }
          
  }
  
	public function getContent($tabnum) { 
		$display = '<form action="'.$_SERVER['REQUEST_URI'].'" method="post">';
		$display .= '<fieldset><legend>Rozšířené vlastnosti</legend>';
		$display.='<input type="hidden" name="currentTab" value="'.$tabnum.'" />';
		$display.='Administrace::Katalog::Produkty::Detail produktu:
		<ul>
		<li>Podzáložka Associace: Přidá pole pro <b>Heureka kategorie</b> - párování heureka kategorií pro individuální produkty</li>
		<li>Podzáložka Informace: Přidá pole pro <b>Text do srovnávačů</b> - rozšiřující/nahrazující text do tagu PRODUCT  v xml exportu Heureka a Zboží, individuální
		pro jednotlivé produkty</li>
        <li>Podzáložka Informace: Přidá pole pro Heureka VIDEOURL</li>
        <li>Podzáložka Informace: Přidá pole pro Zboží PRODUCTLINE</li>    
        <li>Podzáložka Informace: Přidá zaškrtávátka pro Zboží EXTRA_MESSAGE </li>
         <li>Podzáložka Informace: Přidá zaškrtávátka k vynechání produktu ve vybraných feedech</li>
		<li>Podzáložka Informace: Přidá samostatné pole pro <b>Heureka CPC</b> a <b>Zboží CPC</b> </li>
		</ul>';
		$display.='Poznámka: Pokud je u produktu zaškrtnuta EXTRA_MESSAGE doprava zdarma, je to zohledněno i ve výpočtu ceny dopravy ve feedu <br />';
		 

		if(!$this->instance->do_attributes) {   
		$display.= '<b>Pro tuto funkci je potřeba placená verze modulu </b><br />';


		        $checkbox="<br /><br />  <input type=\"checkbox\" name=\"ZBOZI_PARTIAL_UNISTALL\" value='1' style=\"color:blue\"";
		if(Configuration::get("ZBOZI_PARTIAL_UNISTALL"))
		 $checkbox.=" checked=\"checked\" ";
		$checkbox.="/>Při odinstalaci modulu nebo zakázání rozšířených vlastností produktů  nemazat uložené hodnoty   &nbsp;"; 
		$display.= $checkbox." &nbsp;<br /><br />";  
		}
		else {
		if((int)Configuration::get('ZBOZI_CATSPERPRODUCT') ==1 )
		$display.='Je již povoleno: <input type="submit" name="cmd_extended[0]" value="Zakázat" />';
		else
		$display.='Není povoleno: <input type="submit" name="cmd_extended[0]" value="Povolit" />';
		
		
		
		if((int)Configuration::get('ZBOZI_CATSPERPRODUCT') ==1 ) {

            $display.='<h4>Rozšiřující text názvu produktu</h4>';
            $display.='Pokud je u produktu vyplněn tak:<br />';
            $ext= (int)Configuration::get('ZBOZI_TEXT_EXT');
            
            $display .= '<input type ="radio" name="ZBOZI_TEXT_EXT" value="0" '.$this->getCheckedExt($ext, 0).' /> Přidat za název produktu(PRODUCT)<br />';
            $display .= '<input type ="radio" name="ZBOZI_TEXT_EXT" value="1" '.$this->getCheckedExt($ext, 1).' /> Použít namísto názvu produktu (PRODUCT i PRODUCTNAME)<br />';
            $display .= '<input type ="radio" name="ZBOZI_TEXT_EXT" value="2" '.$this->getCheckedExt($ext, 2).' /> Přidat před název produktu (PRODUCT) "<br />';


           $checkbox="<br />  <input type=\"checkbox\" name=\"ZBOZI_TEXT_EXTATT\" value='1'";
            if(Configuration::get("ZBOZI_TEXT_EXTATT"))
            $checkbox.=" checked=\"checked\" ";
            $checkbox.="/>I v případě úplné náhrady názvu přidávat attributy"; 
            $display.= $checkbox." &nbsp;<br /><br />";
            $display.=' <input type="submit" name="cmd_extended[1]" value="Nastavit" />';

        }
		$display.='</fieldset></form>';
		}   
		return $display;  
	} 


	private function getCheckedExt($set, $current) {
		if((int)$set == (int)$current)
		  return " checked='checked'";
		return '';
	
	} 
	
	private function clearAttrCache() {
	    $cache_path=dirname(__FILE__).'/cache/'.Context::getContext()->shop->id;
	    if(file_exists($cache_path))
	       unlink($cache_path);
}
}
