<?php
/**
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author    PrestaShop SA <contact@prestashop.com>mime
* @copyright 2007-2013 PrestaShop SA
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

include_once(dirname(__FILE__).'/../../config/config.inc.php');
include_once(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/totalimportpro.php');

$key = 'Hostjars_Cron_1234'; /* Change to your cron secret */

define('CLI_INITIATED', true);

if (!isset($argc) || $argc < 1)
{
	if (Tools::getValue('secure_key') !== $key)
		die('Invalid Key');
	$profile = Tools::getValue('profile');
	$range = Tools::getValue('range');
}
else
{
	if ($argc > 1 && $argv[1])
	{
		$profile = $argv[1];
		if (isset($argv[2]))
			$range = $argv[2];
	}
}
/* Set defines */
if (isset($profile))
	define ('PROFILE_NAME', $profile);
else
	define ('PROFILE_NAME', 'default');
if (isset($range))
{
	/* Partial Import */
	$exploded_range = explode('-', $range);
	if (count($exploded_range) == 2)
	{
		define('START', $exploded_range[0]);
		define('END', $exploded_range[1]);
	}
}

/* Create lock file to prevent multiple runs */
$lock_file = 'import_lock';
$run = false;
if (!file_exists($lock_file))
{
	$our_file_handle = fopen($lock_file, 'w');
	if ($our_file_handle === false)
		die("can't open file");
	else
	{
		fclose($our_file_handle);
		$run = true;
	}
}
else
{
	/* Remove a lock file over one hour old */
	$file_age = time() - filemtime($lock_file);
	if ($file_age >= 3600)
		$run = true;
	else
		echo "import_lock file present. An import is already running. (Lock file is cleared after 1 hour or you can remove import_lock in your admin folder)\n";
}
/* Run the import */
if ($run)
{
	$action = new TotalImportPRO();
	$action->cron();
	unlink($lock_file);
}
?>