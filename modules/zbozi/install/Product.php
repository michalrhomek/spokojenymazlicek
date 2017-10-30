<?php
  class Product extends ProductCore
{
    public $heureka_category;
    public $zbozi_text;
    public $heureka_text;
    public $videourl;
    public $productline;
    public $extramessage;
    public $skipfeeds;
    public $heureka_cpc;
    public $max_cpc;
    public $max_cpc_search;
    

  public function __construct($id_product = null, $full = false, $id_lang = null, $id_shop = null, Context $context = null)
    {
    self::$definition['fields']['heureka_category'] = array('type' => self::TYPE_STRING,  'validate' => 'isString', 'required' => false, 'size' => 255);
    self::$definition['fields']['zbozi_text'] = array('type' => self::TYPE_STRING,  'validate' => 'isString', 'required' => false, 'size' => 255, 'lang' => true);
    self::$definition['fields']['heureka_text'] = array('type' => self::TYPE_STRING,  'validate' => 'isString', 'required' => false, 'size' => 255, 'lang' => true);
    self::$definition['fields']['videourl'] = array('type' => self::TYPE_STRING,  'validate' => 'isString', 'required' => false, 'size' => 255);
    self::$definition['fields']['productline'] = array('type' => self::TYPE_STRING,  'validate' => 'isString', 'required' => false, 'size' => 255);
    self::$definition['fields']['extramessage'] = array('type' => self::TYPE_STRING,  'validate' => 'isString', 'required' => false, 'size' => 255);
    self::$definition['fields']['skipfeeds'] = array('type' => self::TYPE_STRING,  'shop' => true, 'validate' => 'isString', 'required' => false, 'size' => 255);
    self::$definition['fields']['heureka_cpc'] = array('type' => self::TYPE_STRING,  'validate' => 'isString', 'required' => false, 'size' => 20);
    self::$definition['fields']['max_cpc'] = array('type' => self::TYPE_STRING,  'validate' => 'isString', 'required' => false, 'size' => 20);
    self::$definition['fields']['max_cpc_search'] = array('type' => self::TYPE_STRING,  'validate' => 'isString', 'required' => false, 'size' => 20);

   
   parent::__construct($id_product , $full , $id_lang, $id_shop,$context );
  }
  
    
}
