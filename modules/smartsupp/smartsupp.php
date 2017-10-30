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

if (!defined('_PS_VERSION_')) {
    exit;
}

class Smartsupp extends Module
{

    public function __construct()
    {
        $this->name = 'smartsupp';
        $this->tab = 'advertising_marketing';
        $this->version = '2.1.5';
        $this->author = 'Smartsupp';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->bootstrap = true;
        $this->module_key = 'da5110815a9ea717be24a57b804d24fb';

        parent::__construct();

        $this->displayName = $this->l('Smartsupp Live Chat');
        $this->description = $this->l('Engage your customers in a faster and more personal way with Smartsupp Live Chat.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall Smartsupp Live Chat? You will lose all the data related to this module.');

        if (version_compare(_PS_VERSION_, '1.5', '<')) {
            require(_PS_MODULE_DIR_ . $this->name . '/backward_compatibility/backward.php');
        }

        if (!Configuration::get('SMARTSUPP_KEY')) {
            $this->warning = $this->l('No Smartsupp key provided.');
        }
    }

    public function install()
    {
        if (version_compare(_PS_VERSION_, '1.6', '>=') && Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminSmartsuppAjax';
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Smartsupp';
        }
        $tab->id_parent = -1;
        $tab->module = $this->name;

        if (!$tab->add() || !parent::install() || !$this->registerHook('footer') || !$this->registerHook('backOfficeHeader') || !Configuration::updateValue('SMARTSUPP_KEY', '') || !Configuration::updateValue('SMARTSUPP_EMAIL', '') || !Configuration::updateValue('SMARTSUPP_CUSTOMER_ID', '1') || !Configuration::updateValue('SMARTSUPP_CUSTOMER_NAME', '1') || !Configuration::updateValue('SMARTSUPP_CUSTOMER_EMAIL', '1') || !Configuration::updateValue('SMARTSUPP_CUSTOMER_PHONE', '1') || !Configuration::updateValue('SMARTSUPP_CUSTOMER_ROLE', '1') || !Configuration::updateValue('SMARTSUPP_CUSTOMER_SPENDINGS', '1') || !Configuration::updateValue('SMARTSUPP_CUSTOMER_ORDERS', '1') || !Configuration::updateValue('SMARTSUPP_OPTIONAL_API', '')
        ) {
            return false;
        }

        return true;
    }

    public function uninstall()
    {
        $id_tab = (int) Tab::getIdFromClassName('AdminSmartsuppAjax');

        if ($id_tab) {
            $tab = new Tab($id_tab);
            $tab->delete();
        }

        if (!parent::uninstall() || !$this->unregisterHook('footer') || !$this->unregisterHook('backOfficeHeader') || !Configuration::deleteByName('SMARTSUPP_KEY') || !Configuration::deleteByName('SMARTSUPP_EMAIL') || !Configuration::deleteByName('SMARTSUPP_CUSTOMER_ID', '') || !Configuration::deleteByName('SMARTSUPP_CUSTOMER_NAME', '') || !Configuration::deleteByName('SMARTSUPP_CUSTOMER_EMAIL', '') || !Configuration::deleteByName('SMARTSUPP_CUSTOMER_PHONE', '') || !Configuration::deleteByName('SMARTSUPP_CUSTOMER_ROLE', '') || !Configuration::deleteByName('SMARTSUPP_CUSTOMER_SPENDINGS', '') || !Configuration::deleteByName('SMARTSUPP_CUSTOMER_ORDERS', '') || !Configuration::deleteByName('SMARTSUPP_OPTIONAL_API', '')
        ) {
            return false;
        }

        return true;
    }

    public function displayForm()
    {
        $default_lang = (int) Configuration::get('PS_LANG_DEFAULT');

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'submit' . $this->name;
        $helper->toolbar_btn = array(
            'save' =>
            array(
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name .
                '&token=' . Tools::getAdminTokenLite('AdminModules'),
            ),
            'back' => array(
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );

        $fields_form = array();
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Settings')
            ),
            'input' => array(
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Optional API'),
                    'name' => 'SMARTSUPP_OPTIONAL_API',
                    'desc' => $this->l('Advanced chat box modifications with #.'),
                    'autoload_rte' => false,
                    'rows' => 5
                )
            ),
            'submit' => array(
                'title' => $this->l('Save')
            )
        );

        $helper->fields_value['SMARTSUPP_OPTIONAL_API'] = Configuration::get('SMARTSUPP_OPTIONAL_API');

        return $helper->generateForm($fields_form);
    }

    public function getContent()
    {
        $output = '';
        if (Tools::isSubmit('submit' . $this->name)) {
            $smartsupp_key = Configuration::get('SMARTSUPP_KEY');
            if ($smartsupp_key) {
                $output .= $this->displayConfirmation($this->l('Settings updated successfully'));
            }
            Configuration::updateValue('SMARTSUPP_OPTIONAL_API', Tools::getValue('SMARTSUPP_OPTIONAL_API'));
        }

        if (version_compare(_PS_VERSION_, '1.6', '>=')) {
            $output .= $this->displayForm();
        }

        $ajax_controller_url = $this->context->link->getAdminLink('AdminSmartsuppAjax');
        $this->context->smarty->assign(array(
            'ajax_controller_url' => $ajax_controller_url,
            'smartsupp_key' => Configuration::get('SMARTSUPP_KEY'),
            'smartsupp_email' => Configuration::get('SMARTSUPP_EMAIL'),
        ));

        return $this->display(__FILE__, 'views/templates/admin/landing_page.tpl') .
                $this->display(__FILE__, 'views/templates/admin/create_account.tpl') .
                $this->display(__FILE__, 'views/templates/admin/connect_account.tpl') .
                $this->display(__FILE__, 'views/templates/admin/configuration.tpl') .
                $output;
    }

    public function hookFooter()
    {
        $smartsupp_key = Configuration::get('SMARTSUPP_KEY');

        if ($smartsupp_key) {
            $smartsupp_cookie_domain = '.' . Tools::getHttpHost(false);

            $optional_api = trim(Configuration::get('SMARTSUPP_OPTIONAL_API'));
            if ($optional_api && !empty($optional_api)) {
                $smartsupp_optional_api = trim($optional_api);
            }

            $customer = $this->context->customer;
            if ($customer->id) {
                $smartsupp_dashboard_name = sprintf("%s %s", $customer->firstname, $customer->lastname);
                $smartsupp_dashboard_email = $customer->email;
                $smartsupp_variables_enabled = 1;

                if ($smartsupp_variables_enabled) {
                    $smartsupp_variables_js = '';
                    if (Configuration::get('SMARTSUPP_CUSTOMER_ID')) {
                        $smartsupp_variables_js .= 'id : {label: "' . $this->l('ID') . '", value: "' . $customer->id . '"},';
                    }
                    if (Configuration::get('SMARTSUPP_CUSTOMER_NAME')) {
                        $smartsupp_variables_js .= 'name : {label: "' . $this->l('Name') . '", value: "' . $customer->firstname . ' ' . $customer->lastname . '"},';
                    }
                    if (Configuration::get('SMARTSUPP_CUSTOMER_EMAIL')) {
                        $smartsupp_variables_js .= 'email : {label: "' . $this->l('Email') . '", value: "' . $customer->email . '"}, ';
                    }
                    if (Configuration::get('SMARTSUPP_CUSTOMER_PHONE')) {
                        $addresses = $this->context->customer->getAddresses($this->context->language->id);
                        $phone = $addresses[0]['phone_mobile'] ? $addresses[0]['phone_mobile'] : $addresses[0]['phone'];
                        $smartsupp_variables_js .= 'phone : {label: "' . $this->l('Phone') . '", value: "' . $phone . '"}, ';
                    }
                    if (Configuration::get('SMARTSUPP_CUSTOMER_ROLE')) {
                        $group = new Group($customer->id_default_group, $this->context->language->id, $this->context->shop->id);
                        $smartsupp_variables_js .= 'role : {label: "' . $this->l('Role') . '", value: "' . $group->name . '"}, ';
                    }
                    if (Configuration::get('SMARTSUPP_CUSTOMER_SPENDINGS') || Configuration::get('SMARTSUPP_CUSTOMER_ORDERS')) {
                        $orders = Order::getCustomerOrders($customer->id, true);
                        $count = 0;
                        $spendings = 0;
                        foreach ($orders as $order) {
                            if ($order['valid']) {
                                $count++;
                                $spendings += $order['total_paid_real'];
                            }
                        }
                        if (Configuration::get('SMARTSUPP_CUSTOMER_SPENDINGS')) {
                            $smartsupp_variables_js .= 'spendings : {label: "' . $this->l('Spendings') . '", value: "' . Tools::displayPrice($spendings, $this->context->currency->id) . '"}, ';
                        }
                        if (Configuration::get('SMARTSUPP_CUSTOMER_ORDERS')) {
                            $smartsupp_variables_js .= 'orders : {label: "' . $this->l('Orders') . '", value: "' . $count . '"}, ';
                        }
                    }
                    $smartsupp_variables_js = trim($smartsupp_variables_js, ', ');
                }
            } else {
                $smartsupp_dashboard_name = '';
                $smartsupp_dashboard_email = '';
                $smartsupp_variables_enabled = '0';
                $smartsupp_variables_js = '';
            }

            $script = '<!-- Smartsupp Live Chat script -->';
            $script .= '<script type="text/javascript">';
            if ($smartsupp_variables_enabled && !empty($smartsupp_variables_js)) {
                $script .= "var prSmartsuppVars = {" . $smartsupp_variables_js . "};";
            }
            $script .= "var _smartsupp = _smartsupp || {};";
            $script .= "_smartsupp.key = '" . $smartsupp_key . "';";
            $script .= "_smartsupp.cookieDomain = '" . $smartsupp_cookie_domain . "';";
            $script .= "window.smartsupp||(function(d) {";
            $script .= "var s,c,o=smartsupp=function(){o._.push(arguments)};o._=[];";
            $script .= "s=d.getElementsByTagName('script')[0];c=d.createElement('script');";
            $script .= "c.type='text/javascript';c.charset='utf-8';c.async=true;";
            $script .= "c.src='//www.smartsuppchat.com/loader.js?';s.parentNode.insertBefore(c,s);";
            $script .= "})(document);";
            $script .= "smartsupp('name', '" . $smartsupp_dashboard_name . "');";
            $script .= "smartsupp('email', '" . $smartsupp_dashboard_email . "');";
            if ($smartsupp_variables_enabled && !empty($smartsupp_variables_js)) {
                $script .= "smartsupp('variables', prSmartsuppVars);";
            }
            if (isset($smartsupp_optional_api)) {
                $script .= $smartsupp_optional_api;
            }
            $script .= '</script>';
            return $script;
        }
        return '';
    }

    public function hookBackOfficeHeader()
    {
        $js = '';
        if (strcmp(Tools::getValue('configure'), $this->name) === 0) {
            if (version_compare(_PS_VERSION_, '1.6', '>=') == true) {
                $this->context->controller->addJquery();                
                $this->context->controller->addJs($this->_path . 'views/js/smartsupp.js');
                $this->context->controller->addCSS($this->_path . 'views/css/smartsupp.css');
                if (version_compare(_PS_VERSION_, '1.6', '<') == true) {
                    $this->context->controller->addCSS($this->_path . 'views/css/smartsupp-nobootstrap.css');
                }
            } else {
                $js .= '<script type="text/javascript" src="' . $this->_path . 'views/js/smartsupp.js"></script>';
                $js .= '<link rel="stylesheet" href="' . $this->_path . 'views/css/smartsupp.css" type="text/css" />' .
                       '<link rel="stylesheet" href="' . $this->_path . 'views/css/smartsupp-nobootstrap.css" type="text/css" />';
            }
        }


        return $js;
    }

    protected function getAdminDir()
    {
        return basename(_PS_ADMIN_DIR_);
    }
}
