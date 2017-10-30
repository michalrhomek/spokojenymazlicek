<?php
  class PrestahostUpgrade {
      
   
   public static function displayInfo($modulename, $version, $language) {
       $iso=strlen($language->iso)?$language->iso:'en';
      $retval.= <<<UPGRADE
      <iframe width="800" height="200" src='http://www.prestahost.eu/upgrade/info.php?module=$modulename&version=$version&lang=$iso'></iframe>
UPGRADE;
     
     return $retval; 
  }
  }
?>
