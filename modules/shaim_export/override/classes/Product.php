<?php

/**
 * @license https://psmoduly.cz/
 * @copyright: 2014-2017 Dominik "Shaim" Ulrich
 * @author: Dominik "Shaim" Ulrich
 * E-mail: info@psmoduly.cz / info@openservis.cz
 * Websites: www.psmoduly.cz / www.openservis.cz
 **/
class Product extends ProductCore
{
    public $shaim_export_name = '';
    public $shaim_export_gifts = '';
    public $shaim_export_active = 1;

    public function __construct($id_product = null, $full = false, $id_lang = null, $id_shop = null, Context $context = null)
    {
        if (Module::isEnabled('shaim_export')) {
            self::$definition['fields']['shaim_export_name'] = array('type' => self::TYPE_STRING, 'shop' => true, 'validate' => 'isCatalogName', 'size' => 128);
            self::$definition['fields']['shaim_export_gifts'] = array('type' => self::TYPE_STRING, 'shop' => true, 'validate' => 'isCatalogName', 'size' => 128);
            self::$definition['fields']['shaim_export_active'] = array('type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isBool');
        }

        parent::__construct($id_product, $full, $id_lang, $id_shop, $context);
    }
}