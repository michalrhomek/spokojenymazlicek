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
    class FeedsController extends ZboziController {
    	
    public function postProcess() {
    	 if(isset($_POST['cmd_feeds'][0])){
    	 	  Configuration::updateValue('ZBOZI_PROTOCOL', '');  
		 }
		 if(isset($_POST['cmd_feeds'][1])){
		  	 reset ($this->instance->feeds);
            
            foreach($this->instance->feeds as $feed) {
               $this->instance->feedsUsed[$feed]= intval(Tools::getValue($feed));
               $key="ZBOZI_".strtoupper($feed);
               Configuration::updateValue($key, $this->instance->feedsUsed[$feed]);  
            }
            $keys=array('ZBOZI_NEXTROUND','ZBOZI_PERPASS', 'ZBOZI_CLEANURL', 'ZBOZI_NAMECURRENCY');
            foreach($keys as $key) { 
			  	   Configuration::updateValue($key, (int)Tools::getValue($key)); 
			}
            $podil = (int)Tools::getValue('ZBOZI_PODIL');
            if($podil < 4 || $podil > 30)
                $podil = 10; 
            Configuration::updateValue('ZBOZI_PODIL', (int)Tools::getValue($podil)); 
            Configuration::updateValue('ZBOZI_HASH', trim(Tools::getValue('ZBOZI_HASH'))); 
             
			return  $this->showSaveResult();  
		 }
	}
    	
    public function getContent($tabnum) {
 	    $url=$_SERVER['HTTP_HOST']."/modules/".$this->instance->name."/feeds.php";
 	     $display='<form action="'.$_SERVER['REQUEST_URI'].'" method="post">';
 	     $display.='<input type="hidden" name="currentTab" value="'.$tabnum.'" />';
 	   
        $display .='
            <fieldset>';
          $protocol=Configuration::get('ZBOZI_PROTOCOL');
          if($protocol && strlen($protocol)){
               $display .= '<span style="color:red"><h4>Instalace není úplná</h4>'.nl2br($protocol).'</span></br>
               Více informací najdete v <a href="http://prestahost.eu/navody/index.php/3-instalace" target="_blank">manuálu</a> </br>
               <input type="submit" name="cmd_feeds[0]" value="Vyčistit instalační protokol"><br /><br />';
               
          }  
  
            $display .= '<legend><img src="../img/admin/contact.gif" />Feedy</legend>';
              $display.='<p class="napoveda">
       	   <a class="napoveda" href="http://prestahost.eu/navody/index.php/5-feedy" target="_blank">Nápověda</a>
       	   </p>';
                  $display.=$this->showFeedScripts();
                $display .= ' Požádejte svého poskytovatele o instalaci kronu spouštějícího  uvedené url. U velkých eshopů může být potřeba
               feedy vytvářet postupně opakovaným spouštěním skriptu.  
                <br />   
               
                 <br /> 
                Pokud hostujete na Prestahost.cz, zapněte si kron v záložce Hosting (hlavní lišta záložek)  <br />   
                <br />';
                
    
           $display.='<br /> Počet produktů ke zpracování při jednom spuštění <input type="text" size=4 name="ZBOZI_PERPASS" value="'.Configuration::get('ZBOZI_PERPASS').'" />';   
           $display.='<br /> Minimmální časový odstup mezi dokončením feedů <input type="text" size=4 name="ZBOZI_NEXTROUND" value="'.Configuration::get('ZBOZI_NEXTROUND').'" /> minut'; 
           $display.='<br /> Rezerva pro ukončení skriptu <input type="text" size=4 name="ZBOZI_PODIL" value="'.Configuration::get('ZBOZI_PODIL').'" /> (default 10, rozsah 4 až 30, viz nápověda)';  
           
           $display.='<br />Přidat hash do spouštěcího url <input type="text" size="16" name="ZBOZI_HASH" value="'.Configuration::get('ZBOZI_HASH').'" /> '; 
           $display.='<br />Odstranit diakritiku v url feedu <input type ="checkbox" name="ZBOZI_CLEANURL"  value="1"';
           if((int)Configuration::get('ZBOZI_CLEANURL'))
           $display.=' checked="checked"';
           $display .=' /> <br />';  
           
            $display.='<br />Pokud se feed tvoří s id měny, přidat ji do názvu <input type ="checkbox" name="ZBOZI_NAMECURRENCY"  value="1"';
           if((int)Configuration::get('ZBOZI_NAMECURRENCY'))
           $display.=' checked="checked"';
           $display .=' /> <br /> <br />';  
           
           $display.='<h4>Vytvářené feedy</h4>';  
      
           reset($this->instance->feeds);
           
           foreach($this->instance->feeds as $feed) {
            $file="zbozi_".$feed.".xml";
            $path="../".$this->instance->feeddir."/$file";
            
            $checkbox="$feed - <input type=\"checkbox\" name=\"$feed\" value='1' style=\"color:blue\"";
            if(isset($this->instance->feedsUsed[$feed]) && $this->instance->feedsUsed[$feed])
                 $checkbox.=" checked=\"checked\" ";
            $checkbox.="/>"; 
            
        
          
              $display .=$checkbox."<br />"; 
           }
           
           
           
             $shopName='';
             if(Context::getContext()->shop->isFeatureActive()) {
                require_once(_PS_MODULE_DIR_.'zbozi/classes/cFeed.php');
             	$shopName = cFeed::addShopName(Context::getContext()->shop->id);
             	$shopName=Tools::substr($shopName,0,strlen($shopName)-1);
			 }
             
             $display .=  '<h4>Existující feedy '.$shopName.'</h4>Pokud je nějaký feed již vytvořen, zobrazuje se odkaz níže, kliknutím na něj se feed otevře v prohlížeči. 
               Tím zjistíte url které je potřeba zadat do administrace zboží nebo heureka.<br />';
             $path =  _PS_ROOT_DIR_.'/'.$this->instance->feeddir;
            
              $feedscreated=scandir(_PS_ROOT_DIR_.'/'.$this->instance->feeddir);  
            
              foreach($feedscreated as $created) {
                //  $display.=$created;
                  $koncovka=substr($created, strrpos($created, '.'));
                  if($koncovka == '.xml') {
                  if (!(strpos($created, 'zbozi') === false)) {
                    if($this->testForShopname($shopName, $created)) {
           
                    $url=$this->instance->getBaseUrl().$this->instance->feeddir."/".$created;
                
                    $display.="<a href=\"$url\" target='_blank' style='color:blue'>$url</a> ".date('d.m.Y H:i', filemtime("../".$this->instance->feeddir.'/'.$created));
                    if(file_exists(_PS_ROOT_DIR_.'/'.$this->instance->feeddir.'/'.$created.'.zip')) {
                    	   $url.='.zip';
                    $display.=" zip:<i><a href=\"$url\" target='_blank' style='color:blue'>$url</a> ".date('d.m.Y H:i', filemtime("../".$this->instance->feeddir.'/'.$created.'.zip')).'</i>';	   
					}
                    $display.='<br />';  
					}   
				  }
                  }
              } 
      
                    $display.='</fieldset><br />';  
        
       
     
      
       
      
       $display.='<br /> <input type="submit" class="button" name="cmd_feeds[1]" value="Uložit" /></form>';
       return $display;
	}
	
	private function testForShopname($shopName, $created) {
		
		if(empty($shopName))
		  return true;
		  
		if(strpos($created, $shopName) === false)
		  return false;
		  
		return true;
		
	}
    	
	}
