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
  class DostupnostController extends ZboziController {
  	  
  public function postProcess() {
  	 
			  $keys=array('ZBOZI_AVAILABILITY_MODE','ZBOZI_AVAILABILITY_LATER','ZBOZI_AVAILABILITY');
			  foreach($keys as $key) {
			  	   Configuration::updateValue($key, (int)Tools::getValue($key)); 
			  }
			  $this->instance->availability =intval($_POST['ZBOZI_AVAILABILITY']); 
			  $this->instance->availability_later =intval($_POST['ZBOZI_AVAILABILITY_LATER']);
			  $this->instance->availability_mode= intval($_POST['ZBOZI_AVAILABILITY_MODE']); 
			  
			     $transformed=array();
      
              for($i=0;$i<count($_POST['ZBOZI_DOSTUPNOST_KEY']); $i++) {
                  if(isset($_POST['ZBOZI_DOSTUPNOST_KEY'][$i]) && strlen($_POST['ZBOZI_DOSTUPNOST_KEY'][$i]) && strlen($_POST['ZBOZI_DOSTUPNOST_VAL'][$i]) )
                       $transformed[]=array(0=>$_POST['ZBOZI_DOSTUPNOST_KEY'][$i], 1=>$_POST['ZBOZI_DOSTUPNOST_VAL'][$i]);
              }  
               Configuration::updateValue('ZBOZI_DOSTUPNOST_CUSTOM', json_encode($transformed)); 

			   return $this->showSaveResult();   
  }
    	
  public function getContent($tabnum) {
   	   $display='<form action="'.$_SERVER['REQUEST_URI'].'" method="post">';
   	   	 $display.='<input type="hidden" name="currentTab" value="'.$tabnum.'" />';
      $display.=' <fieldset><legend>Dostupnost</legend>'; 
        $display.='<p class="napoveda">
       	   <a class="napoveda" href="http://prestahost.eu/navody/index.php/6-dostupnosti" target="_blank">Nápověda</a>
       	   </p>'; 
        $display .='Některé srovnávače cen, například Heuréka manuálně kontrolují obchody a
         porovnávají dostupnost z feedu s údaji u zboží nebo v dodacích podmínkách. Určitě se tedy vyplatí
         věnovat se správnému nastavení v této sekci<br /><br />';

          $display .='<input type="radio" name="ZBOZI_AVAILABILITY_MODE" value="0" '.$this->optAvailabilityMode(0).'/> 
           Zohlednit řízení skladu: Modul bude respektovat zda je nastaveno řízení skladu.
          
           <br />';
                    
           if((int)Configuration::get('PS_STOCK_MANAGEMENT')) {
             $display .=' <small> Řízení skladu je zapnuto   použije se číslovka z textu zobrazovaného pokud <b>je nebo respektive není zboží skladem</b>. Pokud v textu není číslovka, použije se výchozí hodnota </b></small></br>';   
           }
           else {
              $display .=' <small> Řízení skladu je vypnuto   použije se vždy  výchozí hodnota. Modul se nebude pokoušet hledat čísla v textech o dostupnosti.  Rízení skladu lze zapnout v Nastavení - Produkty </small></br>';   
           }
           
          $display .='<input type="radio" name="ZBOZI_AVAILABILITY_MODE" value="1" '.$this->optAvailabilityMode(1).'/> Vždy hledat číslo.
          <small>
           bez ohledu na řízení je hledána číselná hodnota v textu. V závislosti na počtu kusů
           se 
            zjišťuje se číslo z textu zadávaného <b> pokud je nebo není zboží skladem</b>. Pokud  v textu chybí číslo, použije se výchozí hodnota. Aby bylo možné texty dopsat (Detail produktu - Množství), je potřeba řízení skladu
           na chvíli zapnout</small><br />';
          $display .='<input type="radio" name="ZBOZI_AVAILABILITY_MODE" value="2" '.$this->optAvailabilityMode(2).'/> Nikdy nehledat číslo.  
           <small>Vždy se použije výchozí hodnota.</small><br /><br />';
        
        
        $display .=' Výchozí hodnota  <input  type="text" name="ZBOZI_AVAILABILITY" value="'.$this->instance->availability.'" />    dnů (0 = skladem)  
          použije se pokud je nenulový počet kusů, nebo je zvoleno "Nikdy nehledat číslo".
         <br />   <br />';
         $display .=' Výchozí hodnota 2 <input  type="text" name="ZBOZI_AVAILABILITY_LATER" value="'.$this->instance->availability_later.'" />    dnů  
           použije se pokud je nula kusů (nikoliv ale při nastavení "Nikdy nehledat číslo")
         <br />   <br />';
         
         
         $display .='Rezervovaná hodnota: číslo 32 se vyhodnotí pro Heureka jako "Informace v obchodu",
         pro Zbozi jako 31 tedy "více než měsíc", pro Google nákupy jako 31. <br />  <br /> ';
       
       
         $display .='Výjimky pro zpracování textu  pro zboží skladem, které se vyhodnotí jako okamžitě k odběru:<br /> 
         <ul>
         <li>text je roven "skladem"</li>
         <li>obsahuje "ihned" a neobsahuje číslovku</li> 
         <li>obsahuje číslovku 24</li>
         </ul>';
            $transformed=json_decode(Configuration::get('ZBOZI_DOSTUPNOST_CUSTOM'), true);
         $display .='Nastavitelné výjimky:<br />'; 
         for($i=0; $i < 3; $i++) {
           $val1=  isset($transformed[$i][0])?(string)$transformed[$i][0]:'';
           $val2=  isset($transformed[$i][1])?(string)$transformed[$i][1]:'';
         $display .='Přesný text: <input type="text" name="ZBOZI_DOSTUPNOST_KEY['.$i.']" value="'.$val1.'">   
         výsledná číslovka: <input type="text" name="ZBOZI_DOSTUPNOST_VAL['.$i.']" size=3 value="'.$val2.'"> <br />';
         
         }
       
       $display .=' </fieldset>';
             $display.=  '<input class="button" name="cmd_dostupnost" value="Uložit změny" type="submit" /> 
        </form>';
        return $display;

   }
   
   protected function optAvailabilityMode($value) {
       if($value==0 && ($value == $this->instance->availability_mode || empty($this->instance->availability_mode)))
         return " checked='checked'";
         
       if($value == $this->instance->availability_mode)
           return " checked='checked'";
           
       return '';
           
   } 
  }