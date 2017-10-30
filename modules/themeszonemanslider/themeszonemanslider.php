<?php

/*
 * ThemesZoneManSlider
 * 
 * @author Themes Zone <contacts@themes.zone>
 * @copyright 2014 Themes Zone
 * @version 0.8
 * @license http://creativecommons.org/licenses/by/3.0/ CC BY 3.0
 */

if (!defined('_PS_VERSION_'))
    exit;

class ThemesZoneManSlider extends Module {

    protected static $cache_products;

    public function __construct() {
        $this->name = 'themeszonemanslider';
        $this->tab = '';
        $this->version = '0.8';
        $this->author = 'Themes Zone';
        $this->need_instance = 0;
        $this->secure_key = Tools::encrypt($this->name);
        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Themes Zone Manufacturers Slider'); // public name
        $this->description = $this->l('Manufacturers Logo Slider by Themes Zone '); // public description
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->module_path = _PS_MODULE_DIR_.$this->name.'/';
        $this->uploads_path = _PS_MODULE_DIR_.$this->name.'/img/';
        $this->admin_tpl_path = _PS_MODULE_DIR_.$this->name.'/views/templates/admin/';
        $this->hooks_tpl_path = _PS_MODULE_DIR_.$this->name.'/views/templates/hook/';

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?'); // confirmation message at uninstall
    }

    /**
     * Install this module
     * @return boolean
     */
    public function install() {

        return  parent::install() &&
                $this->initConfig() &&
                $this->registerHook('displayHeader') &&
                $this->registerHook('displayCarousel') &&
                $this->registerHook('displayHome');
    }

    /**
     * Uninstall this module
     * @return boolean
     */
    public function uninstall() {
        return  Configuration::deleteByName($this->name) &&
                parent::uninstall();
    }
    
    /**
     * Set the default configuration
     * @return boolean
     */
    protected function initConfig() {
        $languages = Language::getLanguages(false);
        $config = array();

        $config['tzc_man_items_wide'] = 4;
        $config['tzc_man_items_desktop'] = 4;
        $config['tzc_man_items_desktop_small'] = 2;
        $config['tzc_man_items_tablet'] = 2;
        $config['tzc_man_items_mobile'] = 1;
        $config['tzc_man_items_number'] = 12;
        $config['tzc_man_autoplay'] = 1;
        $config['tzc_man_nav'] = 1;
        $config['tzc_man_slide_speed'] = 200;

        return Configuration::updateValue($this->name, json_encode($config));
    }

    /**
     * Header of pages hook (Technical name: displayHeader)
     */
    public function hookHeader() {
        $this->context->controller->addCSS($this->_path .'/css/'. 'owl.carousel.css');
        $this->context->controller->addCSS($this->_path .'/css/'. 'owl.theme.css');
        $this->context->controller->addCSS($this->_path .'/css/'. 'owl.transitions.css');
        $this->context->controller->addCSS($this->_path .'/css/'. 'font-awesome.min.css');
        $this->context->controller->addCSS($this->_path .'/css/'. 'style.css');
        $this->context->controller->addJS($this->_path .'/js/'. 'owl.carousel.min.js');
        $this->context->controller->addJS($this->_path .'/js/'. 'script.js');
    }

    /**
     * Homepage content hook (Technical name: displayHome)
     */
    public function hookDisplayHome($params) {
        $config = json_decode(Configuration::get($this->name), true);

        $manuf = $this->getManufacturers();

        $this->smarty->assign(array(
            'items_wide' => $config['tzc_man_items_wide'],
            'items_desktop' => $config['tzc_man_items_desktop'],
            'items_desktop_small' => $config['tzc_man_items_desktop_small'],
            'items_tablet' => $config['tzc_man_items_tablet'],
            'items_mobile' => $config['tzc_man_items_mobile'],
            'slide_speed' => $config['tzc_man_slide_speed'],
            'title' => $config['title'][$this->context->language->id],
            'manufacturers' => $manuf,
            'tzc_autoplay' => $config['tzc_man_autoplay'] ? 'true' : 'false',
            'tzc_nav' => $config['tzc_man_nav'] ? 'true' : 'false',
            'manSize' => Image::getSize(ImageType::getFormatedName('medium')),
        ));

        return $this->display(__FILE__, 'hook.tpl');

    }



    private function getManufacturers()
    {
        return $mancarousel_items = Manufacturer::getManufacturers(true, $this->context->language->id,
            true, false, false, false, Shop::getContextShopGroupID());
    }


    /**
     * Configuration page
     */
    public function getContent() {
        return $this->postProcess() . $this->renderForm();
    }
    
    /*
     * Configuration page form builder
     */
    public function renderForm() {

        $config['tzc_man_items_wide'] = 4;
        $config['tzc_man_items_desktop'] = 4;
        $config['tzc_man_items_desktop_small'] = 2;
        $config['tzc_man_items_tablet'] = 2;
        $config['tzc_man_items_mobile'] = 1;
        $config['tzc_man_items_number'] = 12;
        $config['tzc_man_autoplay'] = 1;
        $config['tzc_man_nav'] = 1;
        $config['tzc_man_slide_speed'] = 200;


        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Themes Zone Manufacturers Slider'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(

                    array(
                        'label' => $this->l('Number of items in the carousel for wide screens'),
                        'type'  => 'text',
                        'name'  => 'tzc_man_items_wide',
                        'class' => 'fixed-width-xs',
                        'desc' => $this->l('Set the number of items to show in a view port on wide screens'),
                    ),
                    array(
                        'label' => $this->l('Number of items in the carousel for desktop screens'),
                        'type'  => 'text',
                        'name'  => 'tzc_man_items_desktop',
                        'class' => 'fixed-width-xs',
                        'desc' => $this->l('Set the number of items to show in a view port on regular screens'),
                    ),
                    array(
                        'label' => $this->l('Number of items in the carousel for desktop small screens'),
                        'type'  => 'text',
                        'name'  => 'tzc_man_items_desktop_small',
                        'class' => 'fixed-width-xs',
                        'desc' => $this->l('Set the number of items to show in a view port on wide tablets'),
                    ),
                    array(
                        'label' => $this->l('Number of items in the carousel for tablets'),
                        'type'  => 'text',
                        'name'  => 'tzc_man_items_tablet',
                        'class' => 'fixed-width-xs',
                        'desc' => $this->l('Set the number of items to show in a view port on regular tablets'),
                    ),
                    array(
                        'label' => $this->l('Number of items in the carousel for mobile'),
                        'type'  => 'text',
                        'name'  => 'tzc_man_items_mobile',
                        'class' => 'fixed-width-xs',
                        'desc' => $this->l('Set the number of items to show in a view port on mobile devices'),
                    ),
                    array(
                        'label' => $this->l('Slide Speed'),
                        'type'  => 'text',
                        'name'  => 'tzc_man_slide_speed',
                        'class' => 'fixed-width-xs',
                        'desc' => $this->l('Set the slide\'s speed'),
                    ),


                    array(
                        'type' => 'switch',
                        'label' => $this->l('Autoplay'),
                        'name' => 'tzc_man_autoplay',
                        'is_bool' => true,
                        'desc' => $this->l('Should the slides autoplay at start?'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                    ),

                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show Navigation'),
                        'name' => 'tzc_man_nav',
                        'is_bool' => true,
                        'desc' => $this->l('Should the nuvigation buttons be displayed?'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                    ),



                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'button pull-right'
                )
            )
        );

        
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->module = $this;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'saveBtn';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
                . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($fields_form));
    }
    
    /*
     * Process data from Configuration page after form submition.
     */
    public function postProcess() {
        if (Tools::isSubmit('saveBtn')) {
            $languages = Language::getLanguages();
            $config = array();



            $config['tzc_man_items_wide'] = Tools::getValue('tzc_man_items_wide');
            $config['tzc_man_items_desktop'] = Tools::getValue('tzc_man_items_desktop');
            $config['tzc_man_items_desktop_small'] = Tools::getValue('tzc_man_items_desktop_small');
            $config['tzc_man_items_tablet'] = Tools::getValue('tzc_man_items_tablet');
            $config['tzc_man_items_mobile'] = Tools::getValue('tzc_man_items_mobile');
            $config['tzc_man_slide_speed'] = Tools::getValue('tzc_man_slide_speed');
            $config['tzc_man_autoplay'] = Tools::getValue('tzc_man_autoplay');
            $config['tzc_man_nav'] = Tools::getValue('tzc_man_nav');

            Configuration::updateValue($this->name, json_encode($config));
            
            return $this->displayConfirmation($this->l('Settings updated'));
        }
    }
    
    /**
     *  Display input values into the form after process
     */
    public function getConfigFieldsValues() {
        return json_decode(Configuration::get($this->name), true);
    }

}
