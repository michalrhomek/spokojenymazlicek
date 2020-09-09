<?php
/**

* NOTICE OF LICENSE

*

* This file is licenced under the Software License Agreement.

* With the purchase or the installation of the software in your application

* you accept the licence agreement.

*

* You must not modify, adapt or create derivative works of this source code

*

*  @author    Carlos García Vega

*  @copyright 2010-2018 CleverPPC

*  @license   LICENSE.txt

*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Cleverppc extends Module
{

    const CLEVERPPC_BASE_URL = 'https://prestashop.cleverecommerce.com/api/prestashop/';

    public function __construct()
    {
        $this->name = 'cleverppc';
        $this->tab = 'advertising_marketing';
        $this->version = '1.4.0';
        $this->author = 'Clever Ecommerce';
        $this->ps_version_compliancy = array('min'=> '1.5.3.0', 'max' => _PS_VERSION_);


        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = 'Ads on Google (Google Shopping + Dynamic Remarketing)';
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall? 
            You will lose all your Clever Google Adwords campaigns.');
        $this->description = 'Get your Google Ads campaigns (Search, Display, Dynamic Remarketing and Google Shopping) 
            all in one module! In addition, if your account complies with Google’s requirements, 
            you will receive a promotional code for up to 120€ to spend on your campaigns!';
        $this->module_key = '4cef3cb22b145038002cd58d5e709840';
        $this->iframe = "https://prestashop.cleverecommerce.com/?hmac=".Configuration::get('CLEVERPPC_HMAC');
    }

    public function install()
    {

        if (!parent::install() ||
            !Configuration::updateValue('PS_WEBSERVICE', true) || !$this->genAccessToken(64) ||
            !$this->generateHmac() || !$this->createWebserviceKey() || !$this-> sendInstallRequest()) {
            return false;
        }
        // Install Tabs
        $parent_tab = new Tab();
        // Need a foreach for the language
        $parent_tab->module = $this->name;
        $languages = Language::getLanguages(false);
        $name = array();
        foreach ($languages as $lang) {
            $name[$lang['id_lang']] = 'Ads on Google';
        }
        $parent_tab->name = $name;
        if (version_compare(_PS_VERSION_, '1.7.0', '>=') === true) {
            $parent_tab->class_name = 'GoogleAds';
            $parent_tab->icon = 'spellcheck';
            $parent_tab->id_parent = 2;
            $parent_tab->save();
        } else {
            $parent_tab->class_name = 'MainGoogleAds';
            $parent_tab->id_parent = 0; // Home tab
            $parent_tab->add();
        }
        
        
        if (version_compare(_PS_VERSION_, '1.7.0', '<') === true) {
            $this->registerHook('displayBackOfficeHeader');
            $tab = new Tab();
            // Need a foreach for the language
            $name2 = array();
            $languages = Language::getLanguages(false);
            foreach ($languages as $lang) {
                $name2[$lang['id_lang']] = 'Configure';
            }
            $tab->name = $name2;
            $tab->class_name = 'GoogleAds';
            $tab->id_parent = $parent_tab->id;
            $tab->module = $this->name;
            $tab->add();
        }
        return true;
    }

    public function uninstall()
    {
        $obj = new WebserviceKey(Configuration::get('CLEVERPPC_WEBSERVICE_ACCOUNT_ID'));
        if (!parent::uninstall() ||
            !$this->cancelSubscription() ||
            !Configuration::deleteByName('CLEVERPPC_WEBSERVICE_ACCOUNT_ID') ||
            !Configuration::deleteByName('CLEVERPPC_SHOP_REFERENCE') ||
            !Configuration::deleteByName('CLEVERPPC_HMAC') || !$obj->delete()) {
            return false;
        }

        if (version_compare(_PS_VERSION_, '1.7.0', '<') === true) {
            $this->unregisterHook('displayBackOfficeHeader');
            //delete tabs
            $tab = new Tab((int)Tab::getIdFromClassName('GoogleAds'));
            $tab->delete();
            $tab = new Tab((int)Tab::getIdFromClassName('MainGoogleAds'));
            $tab->delete();
        }
        return true;
    }

    public function hookDisplayBackOfficeHeader()
    {
        $this->context->controller->addCss($this->_path.'views/css/menuTabIcon.css');
    }


    private function cancelSubscription()
    {
// Create the access token that will be used to validate the subscription cancellation
// Set the request params
        $data = $this->getInformation();
// Init and configure curl
        try {
// Getting auth header
            $auth =  $this->getAuthenticationToken();
            $_headers = array("Authorization: {$auth}");
// Perform request
            $_response = $this->request('uninstall_shop', $data, $_headers);
            $_decoded_data = $this->decodeResponse($_response);
            array('result' => $_decoded_data, 'code' => $_response);
        } catch (RequestException $e) {
// Call to Roll-bar, later on
            array('result' => 'error', 'code' => $e->getCode(), 'message' => $e->getMessage() );
            return false;
        }
        return true;
    }

    private function sendInstallRequest()
    {
// Create the access token that will be used to validate the subscription cancellation
// Set the request params
        $data = $this->getInformation();
// Init and configure curl
        try {
// Getting auth header
            $auth = $this->getAuthenticationToken();
            $_headers = array("Authorization: {$auth}");
// Perform request
            $_response = $this->request('create_shop', $data, $_headers);
            $_decoded_data = $this->decodeResponse($_response);
            array('result' => $_decoded_data, 'code' => $_response);
        } catch (RequestException $e) {
// Call to Roll-bar, later on
            array('result' => 'error', 'code' => $e->getCode(), 'message' => $e->getMessage() );
            return false;
        }
        return true;
    }

    private function createShopReference()
    {
        return md5(uniqid(rand(), true));
    }

    protected function decodeResponse($response)
    {
        return json_decode($response, false);
    }

    private function createWebserviceKey()
    {
// Instantiate the WebserviceKey object
        $obj = new WebserviceKey();
// Generate an unique webservice key
        $key =  Tools::passwdGen(32);
        while ($obj->keyExists($key)) {
            $key = Tools::passwdGen(32);
        }
// Set the WebserviceKey object properties
        $obj->key = $key;
        $obj->description = 'CleverPPC webservice key';
// Save the webservice key

        if (!$obj->add() ||
            !Configuration::updateValue('CLEVERPPC_WEBSERVICE_ACCOUNT_ID', $obj->id) ||
            !Configuration::updateValue('CLEVERPPC_WEBSERVICE_ACCOUNT', $obj->key) ) {
            $this->context->controller->errors[] =
            $this->l('It was not possible to install the CleverPPC module: webservice key creation error.');
            return false;
        }
        Tools::generateHtaccess();
// Set the webservice key permissions
        if (!$obj->setPermissionForAccount($obj->id, $this->getWebservicePermissions())) {
            $this->context->controller->errors[] =
            $this->l('It was not possible to install the CleverPPC module: webservice key permissions setup error.');
            return false;
        }
        return true;
    }

    private function genAccessToken($size)
    {
        if (!Configuration::get('CLEVERPPC_ACCESS_CODE')) {
            Configuration::updateValue('CLEVERPPC_ACCESS_CODE', Tools::passwdGen($size));
        }
        return true;
    }

    public function getAuthData()
    {
        return array('email' => 'prestashop@cleverppc.com', 'password' => 'cleverppc');
    }

    public function getAuthenticationToken()
    {
        try {
// Prepare auth data
            $_data = $this->getAuthData();
// Perform request and get raw response object
            $_response = $this->request('authenticate', $_data);
// Decoding response data
            $_decoded_data = $this->decodeResponse($_response);
// Setting result
            $_result = $_decoded_data->auth_token;
        } catch (RequestException $e) {
// Call to Roll-bar, later on
            $_result = 'error';
        }
        return $_result;
    }

    protected function request($endPoint, $data, $headers = array())
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://prestashop.cleverecommerce.com/api/prestashop/{$endPoint}");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        
        var_dump($response);
        return $response;
    }


    public function getContent()
    {
// If the CleverPPC webservice key was deleted for any reason, create a new one
        Tools::redirect($this->iframe);
    }

    private function getWebservicePermissions()
    {
        $webservice_permissions = array(
            'cart_rules' => array('GET' => 'on'),
            'categories' => array('GET' => 'on'),
            'configurations' => array('GET' => 'on'),
            'content_management_system' => array('GET' => 'on'),
            'countries' => array('GET' => 'on'),
            'currencies' => array('GET' => 'on'),
            'customizations' => array('GET' => 'on'),
            'deliveries' => array('GET' => 'on'),
            'employees' => array('GET' => 'on'),
            'groups' => array('GET' => 'on'),
            'guests' => array('GET' => 'on'),
            'image_types' => array('GET' => 'on'),
            'images' => array('GET' => 'on'),
            'languages' => array('GET' => 'on'),
            'order_carriers' => array('GET' => 'on'),
            'order_details' => array('GET' => 'on'),
            'order_discounts' => array('GET' => 'on'),
            'order_histories' => array('GET' => 'on'),
            'orders' => array('GET' => 'on'),
            'price_ranges' => array('GET' => 'on'),
            'product_customization_fields' => array('GET' => 'on'),
            'product_feature_values' => array('GET' => 'on'),
            'product_features' => array('GET' => 'on'),
            'product_option_values' => array('GET' => 'on'),
            'product_options' => array('GET' => 'on'),
            'product_suppliers' => array('GET' => 'on'),
            'products' => array('GET' => 'on'),
            'shop_groups' => array('GET' => 'on'),
            'shop_urls' => array('GET' => 'on'),
            'shops' => array('GET' => 'on'),
            'specific_price_rules' => array('GET' => 'on'),
            'specific_prices' => array('GET' => 'on'),
            'states' => array('GET' => 'on'),
            'stock_availables' => array('GET' => 'on'),
            'stock_movement_reasons' => array('GET' => 'on'),
            'stock_movements' => array('GET' => 'on'),
            'stocks' => array('GET' => 'on'),
            'stores' => array('GET' => 'on'),
            'suppliers' => array('GET' => 'on'),
            'supply_order_histories' => array('GET' => 'on'),
            'tags' => array('GET' => 'on'),
            'translated_configurations' => array('GET' => 'on'),
            'weight_ranges' => array('GET' => 'on'),
            'zones' => array('GET' => 'on')
        );
        return $webservice_permissions;
    }

//This function will display the admin module configuration pannel
    private function showConfig()
    {
        $this->_html .= "
        <button class='btn btn-success' onclick='window.open('http://google.com','_blank')'> Google</button>";
    }

    public function generateHmac()
    {
        $this->generatePayload();
        $_encoded = json_encode($this->_payload);
        $_encoded_payload = base64_encode($_encoded);
        $_hash_mac = hash_hmac($this->getHashMacAlgorithm(), $_encoded, $this->getHashSecret());
        $_payload_signature = base64_encode($_hash_mac);
        $this->_hmac = "{$_encoded_payload}.{$_payload_signature}";
        Configuration::updateValue('CLEVERPPC_HMAC', $this->_hmac);
        return true;
    }

    public static function getHashMacAlgorithm()
    {
        return 'sha256';
    }

    public static function getHashSecret()
    {
        return '4n7fdidvdrzvwe5hb0i4blohf4d8crc';
    }

    public function generatePayload()
    {
        $this->_payload = array('store_hash' => Configuration::get('CLEVERPPC_ACCESS_CODE'),
            'timestamp' => time(),
            'email' => Configuration::get('PS_SHOP_EMAIL'));
    }

    private function getInformation()
    {
        $languages = Language::getLanguages(true, $this->context->shop->id);
        $shop_languages = array();
        foreach ($languages as $lang) {
//$values[] = Tools::getValue('SOMETEXT_TEXT_'.$lang['id_lang']);
            array_push($shop_languages, "{".$lang['id_lang']."=>".$lang['iso_code']."}");
        }
        $_store = array(
            'name' => Configuration::get('PS_SHOP_NAME'),
            'domain' => Configuration::get('PS_SHOP_DOMAIN'),
            'email' => Configuration::get('PS_SHOP_EMAIL'),
            'countries' => Configuration::get('PS_ALLOWED_COUNTRIES'),
            'logo_url' => Configuration::get('PS_LOGO'),
            'platform' => 'prestashop',
            'currency' => Currency::getCurrencyInstance((int)(Configuration::get('PS_CURRENCY_DEFAULT')))->iso_code,
            'language' => implode(',', $shop_languages),
            'access_token' => Configuration::get('CLEVERPPC_WEBSERVICE_ACCOUNT'),
            'client_id' => Configuration::get('CLEVERPPC_ACCESS_CODE'),
            'address' => Configuration::get('BLOCKCONTACTINFOS_ADDRESS'),
            'timezone' => Configuration::get('PS_TIMEZONE'),
            'phone' => Configuration::get('BLOCKCONTACTINFOS_PHONE'),
            'multistore' => Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE'),
            'shop_country' => Configuration::get('PS_LOCALE_COUNTRY')
        );
        return $_store;
    }
}
