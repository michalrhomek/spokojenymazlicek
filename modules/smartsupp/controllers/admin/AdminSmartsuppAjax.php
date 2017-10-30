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
 * Text Domain:       smartsupp
 * Author:            Smartsupp
 * Author URI:        http://www.smartsupp.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

class AdminSmartsuppAjaxController extends ModuleAdminController
{
    public $ssl = true;
    private $partnerKey = 'h4w6t8hln9';
    private $languageMap = array(
        'ag' => 'es',
        'mx' => 'es',
        'qc' => 'fr',
        'dh' => 'de',
        'gb' => 'en',
    );

    public function init()
    {
        require_once _PS_MODULE_DIR_ . 'smartsupp/classes/Auth/Api.php';
        require_once _PS_MODULE_DIR_ . 'smartsupp/classes/Auth/Request/HttpRequest.php';
        require_once _PS_MODULE_DIR_ . 'smartsupp/classes/Auth/Request/CurlRequest.php';        
                
        $api = new SmartsuppAuthApi();
                
        switch (Tools::getValue('action')) {
            case 'login':
                $response = $api->login(array('email' => Tools::getValue('email'), 'password' => Tools::getValue('password')));
                Configuration::updateValue('SMARTSUPP_KEY', $response['account']['key']);
                Configuration::updateValue('SMARTSUPP_EMAIL', Tools::getValue('email'));
                break;
            case 'create':
                $language = strtolower($this->context->language->iso_code);
                if (array_key_exists($language, $this->languageMap)) {
                    $language = $this->languageMap[$language];
                }
                $response = $api->create(array('email' => Tools::getValue('email'), 'password' => Tools::getValue('password'), 'partnerKey' => $this->partnerKey, 'lang' => $language));
                Configuration::updateValue('SMARTSUPP_KEY', $response['account']['key']);
                Configuration::updateValue('SMARTSUPP_EMAIL', Tools::getValue('email'));
                break;
            case 'deactivate':
                Configuration::updateValue('SMARTSUPP_KEY', '');
                Configuration::updateValue('SMARTSUPP_EMAIL', '');
                break;
        }
                
        if (isset($response) && isset($response['error'])) {
            Configuration::updateValue('SMARTSUPP_KEY', '');
            Configuration::updateValue('SMARTSUPP_EMAIL', '');
        }

        header('Content-Type: application/json');
        die(Tools::jsonEncode(array(
                    'key' => Configuration::get('SMARTSUPP_KEY'),
                    'email' => Configuration::get('SMARTSUPP_EMAIL'),
                    'error' => $response['error'],
                    'message' => $response['message'],
                    'hint' => $response['hint']
                )));
    }
}
