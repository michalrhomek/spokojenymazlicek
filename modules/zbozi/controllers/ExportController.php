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
 require_once(_PS_MODULE_DIR_.'/zbozi/classes/cFeed.php');
  class ExportController extends ZboziController { 
  	  private $debugInfo = '';
  	  private $code ='all';
  	  private $name = 'all';

  	  
    public function postProcess() {
          if(isset($_POST['cmd_export'][0])){
          	 
        	 if(isset($_POST['manufacturers']) && is_array($_POST['manufacturers'])) {
        	 	  $this->code ='man';
                  
                $subclass = Tools::getValue('subclass');
                if($subclass && strlen($subclass)) {
                   $subclass = '_'.strtolower($subclass); 
                }
                else {
                    $subclass='';
                }
        	 	while(list($key,$val) = each($_POST['manufacturers'])) {
        	 	if($val == 0) {
        	 		unset ($_POST['manufacturers']);
        	 		$_POST['manufacturers'] = array(0=>'0');
        	 		$this->name = 'all';
        	 		$this->code = 'all';
        	 		break;
				}
        	 	 if($this->name == 'all') {
        	 	 	 $sql = 'SELECT name FROM '._DB_PREFIX_.'manufacturer WHERE id_manufacturer = '.(int)$val;
        	 	 	 $this->name =iconv("utf-8", "us-ascii//TRANSLIT", Db::getInstance()->getValue($sql));    
        	 	 	 $this->name = str_replace(' ','-',$this->name);
				 }
        	 	  $this->code.='_'.(int)$val;
        	 	 //  $this->name.='_'.(int)$val;
				}
			    $this->name.='_'.substr(md5($this->code),0,5).$subclass; 
			 }
		 } 
    }	  
   
	public function getContent ($tabnum)
    {   
		$display='<form action="'.$_SERVER['REQUEST_URI'].'" method="post">';
		$display.='<input type="hidden" name="currentTab" value="'.$tabnum.'" />';
		$display.=' <fieldset><legend>Export do jiného eshopu</legend>'; 
		$display.='<h1>Toto je experimentální funkce, bez nároku na podporu</h1>';
		 $manufacturers = Manufacturer::getManufacturers();
		 $display.= '<select size="10" name="manufacturers[]" multiple="multiple"><option value="0" '.$this->getSelected(0).'>Vše</option>';
		 foreach($manufacturers as $manufacturer) {
		 	$display.='<option value="'.$manufacturer['id_manufacturer'].'" '.$this->getSelected($manufacturer['id_manufacturer']).'>'.$manufacturer['name'].'</option>'; 
		 }
		 $display.='</select>';
		 $subclass ='<br />';
		 $subclass .= '<select size = "3" name="subclass">';
		
		 $subclass .= '<option value=""';
		 if(Tools::getValue('subclass') == '')
		  $subclass.=' selected="selected"';
		 $subclass .= '>základní</option>';
		 
		  $subclass .= '<option value="Shoptet"';
		 if(Tools::getValue('subclass') == 'Shoptet')
		  $subclass.=' selected="selected"';
		 $subclass .= '>Shoptet</option>';
		 
		  $subclass .= '<option value="Heureka"';
		 if(Tools::getValue('subclass') == 'Heureka')
		  $subclass.=' selected="selected"';
		 $subclass .= '>Heureka</option>'; 
		  
		 $subclass.="</select><br />";
		 
		 $display.=$subclass;
		 
		 $display.='Podkategorie cílového eshopu';
		 $display.='<input type ="text" size = "55" name="cat" value ="'.Tools::getValue('cat').'" />';
		 $display.='</br >';
		 
		 if(strlen($this->code)) {
                $instance = Module::getInstanceByName('zbozi');   
	  			$module_url=  $instance->getModuleUrl();
       			$url=$module_url."/exports.php";
       			 if(Context::getContext()->shop->isFeatureActive()) {
          	        $url.='?id_shop='.Context::getContext()->shop->id;
          	        $spojka='&';
	   				}
	   				else
	        		$spojka='?';
       			
       			$url.=$spojka.'manufacturers='.$this->code;
       			if(strlen(Tools::getValue('subclass')))
       			$url.='&subclass='.Tools::getValue('subclass');
       			if(strlen(Tools::getValue('cat')))
       			$url.='&cat='.urlencode(Tools::getValue('cat'));
       
       			$display.= 'Pro pravidelnou tvorbu zadejte do kronu toto url:<br /> 
       			<input type="text" size="180" value="'.$url.'" /><br />';
       			$display.='Při tvorbě feedů po částech přidejte do prvního spuštění k url "&new=1" tedy
       			';
       			$display.='<br /><input type="text" size="180" value="'.$url.'&new=1" /><br />';
       			
       			$display.='Vytvořený feed najdete na adrese: ';  
       			$display.=$this->instance->getBaseUrl();
       			$display.=$this->instance->feeddir."/".cFeed::addShopName().$this->name.'.xml';
       			$display.='<br >Viz záložka feedy';  
       			
		 }
		$display.=' <input type="submit" name="cmd_export[0]" value="Vygenerovat odkaz" />';
		
		$display.='</fieldset></form>';
		
		
		
		return $display;
	
	}
	
	function getSelected($val) {
	if(!isset($_POST['manufacturers']))
	   return '';
	 $manufacturers = $_POST['manufacturers'];
	 while(list($key,$val2) = each($manufacturers)) {
	 	 if($val2 == $val)
	   	 	 return ' selected="selected"';
	  }	 
	}
  }