<?php
/**
 * Objednavka dotazniku spokojenosti
 *
 * Ukazka pouziti
 * 
 * Nazvy produktu preferujeme v kódování UTF-8. Pokud název produktu
 * převést nedokážete, poradíme si s WINDOWS-1250 i ISO-8859-2    
 * 
 * <code>  
 * try {
 *     $overeno = new HeurekaOvereno('API_KLIC');
 *     // pro slovenske obchody $overeno = new HeurekaOvereno('API_KLIC', HeurekaOvereno::LANGUAGE_SK);
 *     $overeno->setEmail('ondrej.cech@heureka.cz');
 *     $overeno->addProduct('Nokia N95');
 *     $overeno->send();
 * } catch (Exception $e) {
 *     print $e->getMessage();
 * }
 * </code> 
 * @author Ondrej Cech <ondrej.cech@heureka.cz>
 */
require_once( _PS_MODULE_DIR_.'/overeno/HeurekaOvereno.php');
class HeurekaOverenoCz  extends HeurekaOvereno{

  
    const BASE_URL = 'http://www.heureka.cz/direct/dotaznik/objednavka.php';
    
    /**
     * ID jazykovych mutaci
     *
     * @var int     
     */
    const LANGUAGE = 1;     
  
   
    
    /**
     * Konstruktor tridy
     *
     * @param String $apiKey API klic pro identifikaci obchodu    
     * @param Int $languageId Nastaveni jazykove mutace sluzby spolu se spravnou URL
     */              
    public function __construct ( ) {
        $this->apiKey = Configuration::get('HEUREKA_KEY');
        $this->languageId =  self::LANGUAGE;
    }
    
    /**
     * Vraci URL pro zadanou jazykovou mutaci
     *
     * @return String 
     */ 
    protected function getUrl () {
        return  self::BASE_URL;
    }        

}