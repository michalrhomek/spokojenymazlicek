<?php
/**
 * Smartsupp Live Chat integration module.
 * 
 * @package   Smartsupp
 * @author    Smartsupp <vladimir@smartsupp.com>
 * @link      http://www.smartsupp.com
 * @copyright 2016 Smartsupp.com
 * @license   GPL-2.0+
 *
 * Plugin Name:       Smartsupp Live Chat
 * Plugin URI:        http://www.smartsupp.com
 * Description:       Adds Smartsupp Live Chat code to PrestaShop.
 * Version:           2.1.5
 * Author:            Smartsupp
 * Author URI:        http://www.smartsupp.com
 * Text Domain:       smartsupp
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

interface SmartsuppAuthHttpRequest
{
    /**
     * Allows to set request options.
     *
     * @param string $name option name
     * @param string $value option value
     */
    public function setOption($name, $value);

    /**
     * Execute request call.
     *
     * @return boolean execution status
     */
    public function execute();

    /**
     * Get request status info.
     *
     * @param int $opt options
     * @return array status info array
     */
    public function getInfo($opt = 0);

    /**
     * Close request connection.
     *
     * @return boolean close status
     */
    public function close();

    /**
     * Get last error message as formated string.
     *
     * @return string formated error message
     */
    public function getLastErrorMessage();
}
