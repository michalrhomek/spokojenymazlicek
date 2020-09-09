<?php

/**
 * @license https://psmoduly.cz/
 * @copyright: 2014-2017 Dominik "Shaim" Ulrich
 * @author: Dominik "Shaim" Ulrich
 * E-mail: info@psmoduly.cz / info@openservis.cz
 * Websites: www.psmoduly.cz / www.openservis.cz
 **/

require_once(dirname(__FILE__) . '/../../config/config.inc.php'); require_once(dirname(__FILE__) . '/../../init.php');
if (preg_match("/\//",  __DIR__)){ $module_name = explode('/', __DIR__);}else{$module_name = explode('\\', __DIR__);}
$module_name = end($module_name);
require_once($module_name . ".php");

$shaim = new $module_name();
$shaim->Cron();

/**
 * @license https://psmoduly.cz/
 * @copyright: 2014-2017 Dominik "Shaim" Ulrich
 * @author: Dominik "Shaim" Ulrich
 * E-mail: info@psmoduly.cz / info@openservis.cz
 * Websites: www.psmoduly.cz / www.openservis.cz
 **/


