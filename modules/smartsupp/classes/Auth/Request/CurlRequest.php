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

class SmartsuppAuthCurlRequest implements SmartsuppAuthHttpRequest
{
    /**
     * Curl handler resource.
     *
     * @var null|resource
     */
    private $handle = null;

    /**
     * CurlRequest constructor.
     *
     * @param string|null $url URL address to make call for
     */
    public function __construct($url = null)
    {
        $this->init($url);
    }

    /**
     * Init cURL connection object.
     *
     * @param string|null $url
     * @throws Exception
     */
    public function init($url = null)
    {
        $this->handle = curl_init($url);
    }

    /**
     * Set cURL option with given value.
     *
     * @param string $name option name
     * @param string $value option value
     */
    public function setOption($name, $value)
    {
        curl_setopt($this->handle, $name, $value);
    }

    /**
     * Execute cURL request.
     *
     * @return boolean
     */
    public function execute()
    {
        return curl_exec($this->handle);
    }

    /**
     * Get array of information about last request.
     *
     * @param int $opt options
     * @return array info array
     */
    public function getInfo($opt = 0)
    {
        return curl_getinfo($this->handle, $opt);
    }

    /**
     * Close cURL handler connection.
     */
    public function close()
    {
        curl_close($this->handle);
    }

    /**
     * Return last error message as string.
     *
     * @return string formatted error message
     */
    public function getLastErrorMessage()
    {
        $message = sprintf("cURL failed with error #%d: %s", curl_errno($this->handle), curl_error($this->handle));
        return $message;
    }
}
