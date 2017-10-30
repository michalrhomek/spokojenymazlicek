<?php
/*
* PrestaHost.cz / PrestaHost.eu
*
*
*  @author prestahost.eu <info@prestahost.cz>
*  @copyright  2014  PrestaHost.eu, Vaclav Mach
*  @license    http://prestahost.eu/prestashop-modules/en/content/3-terms-and-conditions-of-use
*/
  class PrestahostUpgrade {
      
   
   public static function displayInfo($modulename, $version, $language) {
if (! extension_loaded('soap')) {
  return 'Prestahost.cz';
    
}
$soap = new SoapClient(null, array(
    "location" => "http://www.prestahost.eu/upgrade/server.php",
    "uri" => "http://test/",
));


$retval='<table><tr>
<td>'.$soap->getContact($language).'</td>
<td>'.$soap->getInfo($language).'</td>
<td>'.$soap->getModuleMessage($modulename, $version).'</td>
<td>'.$soap->getOtherMessages($modulename, $version).'</td>
</tr></table>';

return $retval.'<br />';
  }
 
  }