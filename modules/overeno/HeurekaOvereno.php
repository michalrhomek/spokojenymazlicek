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
class HeurekaOvereno {

       
    
    /**
     * Hlaseni OK
     *
     * @var String     
     */              
    const RESPONSE_OK = 'ok';

    /**
     * API klic pro identifikaci obchodu
     *
     * @var String     
     */              
    protected $apiKey;
    /**
     * Email zakaznika
     *
     * @var String     
     */              
    protected $email;
    /**
     * Pole objednanych produktu
     *
     * @var Array     
     */              
    protected $products = array();
    /**
     * ID jazykove mutace
     *
     * @var int     
     */
    protected $languageId = 1;        
    
  
    
    /**
     * Setter pro email
     *
     * @param String $email Email zakaznika, kteremu bude odeslat dotaznik
     */              
    public function setEmail ($email) {
        $this->email = $email;
    }
    
    /**
     * Pridava objednane produkty do pozadavku
     * 
     * Nazvy produktu preferujeme v kódování UTF-8. Pokud název produktu
     * převést nedokážete, poradíme si s WINDOWS-1250 i ISO-8859-2       
     *
     * @param String $productName Nazev objednaneho produktu
     */                   
    public function addProduct ($productName) {
        $this->products[] = $productName;
    }
    
    /**
     * Provadi HTTP pozadavek na server
     *
     * @param String $url Volana URL adresa
     * @return String Odpoved ze serveru     
     */                   
    protected static function sendRequest ($url) {
        $parsed = parse_url($url);
        $fp = fsockopen($parsed['host'], 80, $errno, $errstr, 5);
        if (!$fp) {
            throw new Exception ($errstr . ' (' . $errno . ')');
        } else {
            $return = '';
            $out = "GET " . $parsed['path'] . "?" . $parsed['query'] . " HTTP/1.1\r\n" . 
                "Host: " . $parsed['host'] . "\r\n" . 
                "Connection: Close\r\n\r\n";
            fputs($fp, $out);
            while (!feof($fp)) {
                $return .= fgets($fp, 128);
            }
            fclose($fp);
            $returnParsed = explode("\r\n\r\n", $return);
            
            return empty($returnParsed[1]) ? '' : trim($returnParsed[1]);
        }
    }
    
            

    /**
     * Odesila pozadavek na objednani dotazniku
     *
     * @return Bool true 
     */                   
    public function send () {
        if (empty($this->email)) {
            throw new Exception('Je nutné vyplnit elektronickou adresu');
        }
        
        // Stavime URL
        $url = $this->getUrl() . '?id=' . $this->apiKey . '&email=' . urlencode($this->email);
        foreach ($this->products as $product) {
            $url .= '&produkt[]=' . urlencode($product);
        }
        
        // Odesilame pozadavek a kontrolujeme stav
        $contents = self::sendRequest($url);
        
       if(Configuration::get('HEUREKA_DEBUG') == 1) {
         $s=date('d.m.Y H:i:s')."\n";
         $s.='url '.$url."\n";

         if (false === $contents) {
              $s.='odpoved: Nepodarilo se odeslat pozadavek'."\n";
        } elseif (self::RESPONSE_OK == $contents) {
              $s.='odpoved: '.self::RESPONSE_OK.' '.$contents."\n";
        } else {
            $s.='odpoved: FAILED '. $contents."\n";
        }
        $path   =_PS_MODULE_DIR_.'overeno/debug.txt';
        $fp=fopen(_PS_MODULE_DIR_.'overeno/debug.txt', 'a+');
        fputs($fp, $s."\n\n");
        fclose($fp);
       } 
       
        if (false === $contents) {
             throw new Exception ('Nepodarilo se odeslat pozadavek');
        } elseif (self::RESPONSE_OK == $contents) {
            return true;
        } else {
            return false;
        }
    }
}