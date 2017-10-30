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
      $function=$_GET['function'];    
      $text=$_GET['text'];
      $locale=$_GET['locale'];
     
  	  $content=file_get_contents($function.'_'.$locale.'_taxonomy.txt');
  	  
  	  if(!$content || strlen($content) == 0)
  	    return;
  	  if(strlen($text) < 2)
  	    return;
  	    
  	  $a=explode("\n",$content);
  	  foreach($a as $line) {
  	  	 if(strlen($line)) {
  	  	    if(! (mb_stripos($line,$text) === false)){ 
  	  	     echo '<option>'.$line.'</option>';
			} 
		 } 
  	  	  
	  }
  	  
 
  
?>
