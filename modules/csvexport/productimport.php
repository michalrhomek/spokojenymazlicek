<?php
error_reporting(E_ALL);
  ini_set('display_errors', 1);
if(!defined('MAX_LINE_SIZE'))
  define('MAX_LINE_SIZE', 0);
 require('../../config/config.inc.php');
  
 if(Tools::getValue('hash') !=substr(_COOKIE_KEY_,3,7))
 die('Invalid hash');
 
 $CsvProductImport=new CsvProductImport(Configuration::get('CSV_IMPORT_FILE'), Configuration::get('CSV_IMPORT_MATCH'));
 
 $CsvProductImport->productImport(Configuration::get('CSV_IMPORT_PERPASS'));
  
  class CsvProductImport {
   public static $default_values = array();  
   
   protected $convert='';
   protected $csv;
   protected $entity=1;
   protected $forceIds=1;
   protected $import=1;
   protected $iso_lang;
   protected $multiple_value_separator=',';
   protected $regenerate=''; 
   protected $separator=';'; 
   protected $admin_dir;
   
   protected $column_mask;
   protected $match_ref=0;
   protected $mask; 
   protected $context;
   protected $skip;
   
   
   public function __construct($filename, $mask, $lang='en') { 
       $this->csv=$filename;
       $this->mask=$mask;
       $this->iso_lang=$lang;
       $this->context=Context::getContext();
   //    $test=$this->context->link;
       $this->admin_dir = Configuration::get('CSV_IMPORT_ADMINDIR');
       $sql='SELECT `match`, skip FROM `'._DB_PREFIX_.'import_match` WHERE name="'.pSQL($this->mask).'"';
       $row=Db::getInstance()->getRow($sql);
       $match=$row['match'];
       $this->skip=(int)$row['skip'];
       $type_value=explode('|', $match);
         foreach ($type_value as $nb => $type)
            if ($type != 'no')
                 $this->column_mask[$type] = $nb;  
   }
   
      public function productImport($step=50)
    {   
     
        $handle = $this->openCsvFile($this->skip); 
      
        $default_language_id = (int)Configuration::get('PS_LANG_DEFAULT');
        $id_lang = Language::getIdByIso($this->iso_lang);
        if (!Validate::isUnsignedId($id_lang))
            $id_lang = $default_language_id;
        AdminImportController::setLocale();
        $shop_ids = Shop::getCompleteListOfShopsID();
        for ($current_line = 0; $line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator); $current_line++)
        {
           
            if($current_line >= $step)
              return;
            $this->skip++;
            if ($this->convert)
                $line = $this->utf8EncodeArray($line);
            $info = $this->getMaskedRow($line);
          
            if($this->forceIds) {
                $_POST['forceIDs'] = 1; // potrebuji v add metode
            }
            
            if ($this->forceIds && isset($info['id']) && (int)$info['id'])
                $product = new Product((int)$info['id']);
           elseif ($this->match_ref && array_key_exists('reference', $info))
            {
                    $datas = Db::getInstance()->getRow('
                        SELECT p.`id_product`
                        FROM `'._DB_PREFIX_.'product` p
                        '.Shop::addSqlAssociation('product', 'p').'
                        WHERE p.`reference` = "'.pSQL($info['reference']).'"
                    ');
                    if (isset($datas['id_product']) && $datas['id_product'])
                        $product = new Product((int)$datas['id_product']);
                    else
                        $product = new Product();
            } 
            elseif (array_key_exists('id', $info) && (int)$info['id'] && Product::existsInDatabase((int)$info['id'], 'product'))
                    $product = new Product((int)$info['id']);
            else
                $product = new Product();

                
            
                
            if (array_key_exists('id', $info) && (int)$info['id'] && Product::existsInDatabase((int)$info['id'], 'product'))
            {
                $product->loadStockData();
                $category_data = Product::getProductCategories((int)$product->id);
                foreach ($category_data as $tmp)
                    $product->category[] = $tmp;
            }

            self::setEntityDefaultValues($product);
            AdminImportController::arrayWalk($info, array('AdminImportController', 'fillInfo'), $product);

            if (!Shop::isFeatureActive())
                $product->shop = 1;
            elseif (!isset($product->shop) || empty($product->shop))
                $product->shop = implode($this->multiple_value_separator, Shop::getContextListShopID());

            if (!Shop::isFeatureActive())
                $product->id_shop_default = 1;
            else
                $product->id_shop_default = (int)Context::getContext()->shop->id;

            // link product to shops
            $product->id_shop_list = array();
            foreach (explode($this->multiple_value_separator, $product->shop) as $shop)
                if (!is_numeric($shop))
                    $product->id_shop_list[] = Shop::getIdByName($shop);
                else
                    $product->id_shop_list[] = $shop;

            if ((int)$product->id_tax_rules_group != 0)
            {
                if (Validate::isLoadedObject(new TaxRulesGroup($product->id_tax_rules_group)))
                {
                    $address = $this->context->shop->getAddress();
                    $tax_manager = TaxManagerFactory::getManager($address, $product->id_tax_rules_group);
                    $product_tax_calculator = $tax_manager->getTaxCalculator();
                    $product->tax_rate = $product_tax_calculator->getTotalRate();
                }
                else
                    $this->addProductWarning(
                        'id_tax_rules_group',
                        $product->id_tax_rules_group,
                        Tools::displayError('Invalid tax rule group ID. You first need to create a group with this ID.')
                    );
            }
            if (isset($product->manufacturer) && is_numeric($product->manufacturer) && Manufacturer::manufacturerExists((int)$product->manufacturer))
                $product->id_manufacturer = (int)$product->manufacturer;
            else if (isset($product->manufacturer) && is_string($product->manufacturer) && !empty($product->manufacturer))
            {
                if ($manufacturer = Manufacturer::getIdByName($product->manufacturer))
                    $product->id_manufacturer = (int)$manufacturer;
                else
                {
                    $manufacturer = new Manufacturer();
                    $manufacturer->name = $product->manufacturer;
                    if (($field_error = $manufacturer->validateFields(UNFRIENDLY_ERROR, true)) === true &&
                        ($lang_field_error = $manufacturer->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true && $manufacturer->add())
                        $product->id_manufacturer = (int)$manufacturer->id;
                    else
                    {
                        $this->errors[] = sprintf(
                            Tools::displayError('%1$s (ID: %2$s) cannot be saved'),
                            $manufacturer->name,
                            (isset($manufacturer->id) && !empty($manufacturer->id))? $manufacturer->id : 'null'
                        );
                        $this->errors[] = ($field_error !== true ? $field_error : '').(isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '').
                            Db::getInstance()->getMsgError();
                    }
                }
            }

            if (isset($product->supplier) && is_numeric($product->supplier) && Supplier::supplierExists((int)$product->supplier))
                $product->id_supplier = (int)$product->supplier;
            else if (isset($product->supplier) && is_string($product->supplier) && !empty($product->supplier))
            {
                if ($supplier = Supplier::getIdByName($product->supplier))
                    $product->id_supplier = (int)$supplier;
                else
                {
                    $supplier = new Supplier();
                    $supplier->name = $product->supplier;
                    $supplier->active = true;

                    if (($field_error = $supplier->validateFields(UNFRIENDLY_ERROR, true)) === true &&
                        ($lang_field_error = $supplier->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true && $supplier->add())
                    {
                        $product->id_supplier = (int)$supplier->id;
                        $supplier->associateTo($product->id_shop_list);
                    }
                    else
                    {
                        $this->errors[] = sprintf(
                            Tools::displayError('%1$s (ID: %2$s) cannot be saved'),
                            $supplier->name,
                            (isset($supplier->id) && !empty($supplier->id))? $supplier->id : 'null'
                        );
                        $this->errors[] = ($field_error !== true ? $field_error : '').(isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '').
                            Db::getInstance()->getMsgError();
                    }
                }
            }

            if (isset($product->price_tex) && !isset($product->price_tin))
                $product->price = $product->price_tex;
            else if (isset($product->price_tin) && !isset($product->price_tex))
            {
                $product->price = $product->price_tin;
                // If a tax is already included in price, withdraw it from price
                if ($product->tax_rate)
                    $product->price = (float)number_format($product->price / (1 + $product->tax_rate / 100), 6, '.', '');
            }
            else if (isset($product->price_tin) && isset($product->price_tex))
                $product->price = $product->price_tex;

            if (isset($product->category) && is_array($product->category) && count($product->category))
            {
                $product->id_category = array(); // Reset default values array
                foreach ($product->category as $value)
                {
                    if (is_numeric($value))
                    {
                        if (Category::categoryExists((int)$value))
                            $product->id_category[] = (int)$value;
                        else
                        {
                            $category_to_create = new Category();
                            $category_to_create->id = (int)$value;
                            $category_to_create->name = AdminImportController::createMultiLangField($value);
                            $category_to_create->active = 1;
                            $category_to_create->id_parent = Configuration::get('PS_HOME_CATEGORY'); // Default parent is home for unknown category to create
                            $category_link_rewrite = Tools::link_rewrite($category_to_create->name[$default_language_id]);
                            $category_to_create->link_rewrite = AdminImportController::createMultiLangField($category_link_rewrite);
                            if (($field_error = $category_to_create->validateFields(UNFRIENDLY_ERROR, true)) === true &&
                                ($lang_field_error = $category_to_create->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true && $category_to_create->add())
                                $product->id_category[] = (int)$category_to_create->id;
                            else
                            {
                                $this->errors[] = sprintf(
                                    Tools::displayError('%1$s (ID: %2$s) cannot be saved'),
                                    $category_to_create->name[$default_language_id],
                                    (isset($category_to_create->id) && !empty($category_to_create->id))? $category_to_create->id : 'null'
                                );
                                $this->errors[] = ($field_error !== true ? $field_error : '').(isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '').
                                    Db::getInstance()->getMsgError();
                            }
                        }
                    }
                    else if (is_string($value) && !empty($value))
                    {
                        $category = Category::searchByName($default_language_id, trim($value), true);
                        if ($category['id_category'])
                            $product->id_category[] = (int)$category['id_category'];
                        else
                        {
                            $category_to_create = new Category();
                            if (!Shop::isFeatureActive())
                                $category_to_create->id_shop_default = 1;
                            else
                                $category_to_create->id_shop_default = (int)Context::getContext()->shop->id;
                            $category_to_create->name = AdminImportController::createMultiLangField(trim($value));
                            $category_to_create->active = 1;
                            $category_to_create->id_parent = (int)Configuration::get('PS_HOME_CATEGORY'); // Default parent is home for unknown category to create
                            $category_link_rewrite = Tools::link_rewrite($category_to_create->name[$default_language_id]);
                            $category_to_create->link_rewrite = AdminImportController::createMultiLangField($category_link_rewrite);
                            if (($field_error = $category_to_create->validateFields(UNFRIENDLY_ERROR, true)) === true &&
                                ($lang_field_error = $category_to_create->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true && $category_to_create->add())
                                $product->id_category[] = (int)$category_to_create->id;
                            else
                            {
                                $this->errors[] = sprintf(
                                    Tools::displayError('%1$s (ID: %2$s) cannot be saved'),
                                    $category_to_create->name[$default_language_id],
                                    (isset($category_to_create->id) && !empty($category_to_create->id))? $category_to_create->id : 'null'
                                );
                                $this->errors[] = ($field_error !== true ? $field_error : '').(isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '').
                                    Db::getInstance()->getMsgError();
                            }
                        }
                    }
                }
            }

            $product->id_category_default = isset($product->id_category[0]) ? (int)$product->id_category[0] : '';
    
            $link_rewrite = (is_array($product->link_rewrite) && isset($product->link_rewrite[$id_lang])) ? trim($product->link_rewrite[$id_lang]) : '';

            $valid_link = Validate::isLinkRewrite($link_rewrite);

            if ((isset($product->link_rewrite[$id_lang]) && empty($product->link_rewrite[$id_lang])) || !$valid_link)
            {
                $link_rewrite = Tools::link_rewrite($product->name[$id_lang]);
                if ($link_rewrite == '')
                    $link_rewrite = 'friendly-url-autogeneration-failed';
            }

            if (!$valid_link)
                $this->warnings[] = sprintf(
                    Tools::displayError('Rewrite link for %1$s (ID: %2$s) was re-written as %3$s.'),
                    $product->name[$id_lang],
                    (isset($info['id']) && !empty($info['id']))? $info['id'] : 'null',
                    $link_rewrite
                );

            if (!$this->match_ref || !(is_array($product->link_rewrite) && count($product->link_rewrite) && !empty($product->link_rewrite[$id_lang])))
                $product->link_rewrite =self::createMultiLangField($link_rewrite);

            // replace the value of separator by coma
            if ($this->multiple_value_separator != ',')
                if (is_array($product->meta_keywords))
                    foreach ($product->meta_keywords as &$meta_keyword)
                        if (!empty($meta_keyword))
                            $meta_keyword = str_replace($this->multiple_value_separator, ',', $meta_keyword);

            // Convert comma into dot for all floating values
            foreach (Product::$definition['fields'] as $key => $array)
                if ($array['type'] == Product::TYPE_FLOAT)
                    $product->{$key} = str_replace(',', '.', $product->{$key});
            
            // Indexation is already 0 if it's a new product, but not if it's an update
            $product->indexed = 0;

            $res = false;
            $field_error = $product->validateFields(UNFRIENDLY_ERROR, true);
            $lang_field_error = $product->validateFieldsLang(UNFRIENDLY_ERROR, true);
          
            if ($field_error === true && $lang_field_error === true)
            {
                // check quantity
                if ($product->quantity == null)
                    $product->quantity = 0;
               
                // If match ref is specified && ref product && ref product already in base, trying to update
                if ($this->match_ref == 1 && $product->reference && $product->existsRefInDatabase($product->reference))
                {
                    $datas = Db::getInstance()->getRow('
                        SELECT product_shop.`date_add`, p.`id_product`
                        FROM `'._DB_PREFIX_.'product` p
                        '.Shop::addSqlAssociation('product', 'p').'
                        WHERE p.`reference` = "'.pSQL($product->reference).'"
                    ');
                    $product->id = (int)$datas['id_product'];
                    $product->date_add = pSQL($datas['date_add']);
                    $res = $product->update();
                } // Else If id product && id product already in base, trying to update
                else if ($product->id && Product::existsInDatabase((int)$product->id, 'product'))
                {
                    $datas = Db::getInstance()->getRow('
                        SELECT product_shop.`date_add`
                        FROM `'._DB_PREFIX_.'product` p
                        '.Shop::addSqlAssociation('product', 'p').'
                        WHERE p.`id_product` = '.(int)$product->id);
                    $product->date_add = pSQL($datas['date_add']);
                    $res = $product->update();
                }
              
                // If no id_product or update failed
                if (!$res)
                {
                  
                    if (isset($product->date_add) && $product->date_add != '')
                        $res = $product->add(false);
                    else
                        $res = $product->add();
                }
            }

            $shops = array();
            $product_shop = explode($this->multiple_value_separator, $product->shop);
            foreach ($product_shop as $shop)
            {
                $shop = trim($shop);
                if (!is_numeric($shop))
                    $shop = Shop::getIdByName($shop);

                if (in_array($shop, $shop_ids))
                    $shops[] = $shop;
                else
                    $this->addProductWarning(Tools::safeOutput($info['name']), $product->id, $this->l('Shop is not valid'));
            }
            if (empty($shops))
                $shops = Shop::getContextListShopID();
            // If both failed, mysql error
            if (!$res)
            {
                $this->errors[] = sprintf(
                    Tools::displayError('%1$s (ID: %2$s) cannot be saved'),
                    (isset($info['name']) && !empty($info['name']))? Tools::safeOutput($info['name']) : 'No Name',
                    (isset($info['id']) && !empty($info['id']))? Tools::safeOutput($info['id']) : 'No ID'
                );
                $this->errors[] = ($field_error !== true ? $field_error : '').(isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '').
                    Db::getInstance()->getMsgError();

            }
            else
            {
                // Product supplier
                if (isset($product->id_supplier) && property_exists($product, 'supplier_reference'))
                {
                    $id_product_supplier = ProductSupplier::getIdByProductAndSupplier((int)$product->id, 0, (int)$product->id_supplier);
                    if ($id_product_supplier)
                        $product_supplier = new ProductSupplier((int)$id_product_supplier);
                    else
                        $product_supplier = new ProductSupplier();

                    $product_supplier->id_product = $product->id;
                    $product_supplier->id_product_attribute = 0;
                    $product_supplier->id_supplier = $product->id_supplier;
                    $product_supplier->product_supplier_price_te = $product->wholesale_price;
                    $product_supplier->product_supplier_reference = $product->supplier_reference;
                    $product_supplier->save();
                }

                // SpecificPrice (only the basic reduction feature is supported by the import)
                if (!Shop::isFeatureActive())
                    $info['shop'] = 1;
                elseif (!isset($info['shop']) || empty($info['shop']))
                    $info['shop'] = implode($this->multiple_value_separator, Shop::getContextListShopID());
    
                // Get shops for each attributes
                $info['shop'] = explode($this->multiple_value_separator, $info['shop']);
                    
                $id_shop_list = array();
                foreach ($info['shop'] as $shop)
                    if (!is_numeric($shop))
                        $id_shop_list[] = (int)Shop::getIdByName($shop);
                    else
                        $id_shop_list[] = $shop;

                    if ((isset($info['reduction_price']) && $info['reduction_price'] > 0) || (isset($info['reduction_percent']) && $info['reduction_percent'] > 0))
                        foreach($id_shop_list as $id_shop)
                        {
                            $specific_price = SpecificPrice::getSpecificPrice($product->id, $id_shop, 0, 0, 0, 1, 0, 0, 0, 0);

                            if (is_array($specific_price))
                                $specific_price = new SpecificPrice((int)$specific_price['id_specific_price']);
                            else
                                $specific_price = new SpecificPrice();
                            $specific_price->id_product = (int)$product->id;
                            $specific_price->id_specific_price_rule = 0;
                            $specific_price->id_shop = $id_shop;
                            $specific_price->id_currency = 0;
                            $specific_price->id_country = 0;
                            $specific_price->id_group = 0;
                            $specific_price->price = -1;
                            $specific_price->id_customer = 0;
                            $specific_price->from_quantity = 1;
                            $specific_price->reduction = (isset($info['reduction_price']) && $info['reduction_price']) ? $info['reduction_price'] : $info['reduction_percent'] / 100;
                            $specific_price->reduction_type = (isset($info['reduction_price']) && $info['reduction_price']) ? 'amount' : 'percentage';
                            $specific_price->from = (isset($info['reduction_from']) && Validate::isDate($info['reduction_from'])) ? $info['reduction_from'] : '0000-00-00 00:00:00';
                            $specific_price->to = (isset($info['reduction_to']) && Validate::isDate($info['reduction_to']))  ? $info['reduction_to'] : '0000-00-00 00:00:00';
                            if (!$specific_price->save())
                                $this->addProductWarning(Tools::safeOutput($info['name']), $product->id, $this->l('Discount is invalid'));
                        }

                if (isset($product->tags) && !empty($product->tags))
                {
                    if (isset($product->id) && $product->id)
                    {
                        $tags = Tag::getProductTags($product->id);
                        if (is_array($tags) && count($tags))
                        {
                            if (!empty($product->tags))
                                $product->tags = explode($this->multiple_value_separator, $product->tags);
                            if (is_array($product->tags) && count($product->tags))
                            {
                                foreach ($product->tags as $key => $tag)
                                    $product->tags[$key] = trim($tag);
                                $tags[$id_lang] = $product->tags;
                                $product->tags = $tags;
                            }
                        }
                    }
                    // Delete tags for this id product, for no duplicating error
                    Tag::deleteTagsForProduct($product->id);
                    if (!is_array($product->tags))
                    {
                        $product->tags = AdminImportController::createMultiLangField($product->tags);
                        foreach ($product->tags as $key => $tags)
                        {
                            $is_tag_added = Tag::addTags($key, $product->id, $tags, $this->multiple_value_separator);
                            if (!$is_tag_added)
                            {
                                $this->addProductWarning(Tools::safeOutput($info['name']), $product->id, $this->l('Tags list is invalid'));
                                break;
                            }
                        }
                    }
                    else
                    {
                        foreach ($product->tags as $key => $tags)
                        {
                            $str = '';
                            foreach ($tags as $one_tag)
                                $str .= $one_tag.$this->multiple_value_separator;
                            $str = rtrim($str, $this->multiple_value_separator);

                            $is_tag_added = Tag::addTags($key, $product->id, $str, $this->multiple_value_separator);
                            if (!$is_tag_added)
                            {
                                $this->addProductWarning(Tools::safeOutput($info['name']), (int)$product->id, 'Invalid tag(s) ('.$str.')');
                                break;
                            }
                        }
                    }
                }
                //delete existing images if "delete_existing_images" is set to 1
                if (isset($product->delete_existing_images))
                    if ((bool)$product->delete_existing_images)
                        $product->deleteImages();
                else if (isset($product->image) && is_array($product->image) && count($product->image))
                    $product->deleteImages();
                    
                if(isset($product->image) && strlen($product->image)  && !is_array($product->image)) {
                  $arr=explode(',',  $product->image);
                  $images=array();
                  foreach($arr as $a) {
                     if(strlen($a) >5)
                     $images[]=$a;
                  }
                  $product->image=$images;
                } 
                    

                if (isset($product->image) && is_array($product->image) && count($product->image))
                {
                
                    $product_has_images = (bool)Image::getImages($this->context->language->id, (int)$product->id);
                    foreach ($product->image as $key => $url)
                    {
                        $url = trim($url);
                        $error = false;
                        if (!empty($url))
                        {
                            $url = str_replace(' ', '%20', $url);

                            $image = new Image();
                            $image->id_product = (int)$product->id;
                            $image->position = Image::getHighestPosition($product->id) + 1;
                            $image->cover = (!$key && !$product_has_images) ? true : false;
                            // file_exists doesn't work with HTTP protocol
                            if (($field_error = $image->validateFields(UNFRIENDLY_ERROR, true)) === true &&
                                ($lang_field_error = $image->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true && $image->add())
                            {
                                // associate image to selected shops
                                $image->associateTo($shops);
                                if (!self::copyImg($product->id, $image->id, $url, 'products', !$this->regenerate))
                                {
                                    $image->delete();
                                    $this->warnings[] = sprintf(Tools::displayError('Error copying image: %s'), $url);
                                }
                            }
                            else
                                $error = true;
                        }
                        else
                            $error = true;

                        if ($error)
                            $this->warnings[] = sprintf(Tools::displayError('Product n°%1$d: the picture cannot be saved: %2$s'), $image->id_product, $url);
                    }
                }
                if (isset($product->id_category))
                    $product->updateCategories(array_map('intval', $product->id_category));

                // Features import
                $features = get_object_vars($product);

                if (isset($features['features']) && !empty($features['features']))
                    foreach (explode($this->multiple_value_separator, $features['features']) as $single_feature)
                    {
                        $tab_feature = explode(':', $single_feature);
                        $feature_name = trim($tab_feature[0]);
                        $feature_value = trim($tab_feature[1]);
                        $position = isset($tab_feature[2]) ? $tab_feature[2]: false;
                        if(!empty($feature_name) && !empty($feature_value))
                        {
                            $id_feature = Feature::addFeatureImport($feature_name, $position);
                            $id_feature_value = FeatureValue::addFeatureValueImport($id_feature, $feature_value, $product->id, $id_lang);
                            Product::addFeatureProductImport($product->id, $id_feature, $id_feature_value);
                        }
                    }
                // clean feature positions to avoid conflict
                Feature::cleanPositions();
            }

            // stock available
            if (Shop::isFeatureActive())
            {
                foreach ($shops as $shop)
                    StockAvailable::setQuantity((int)$product->id, 0, $product->quantity, (int)$shop);
            }
            else
                StockAvailable::setQuantity((int)$product->id, 0, $product->quantity, $this->context->shop->id);

        }
        $this->closeCsvFile($handle);
    }
   
   
   protected function openCsvFile($skip=0)
    {
      $handle = fopen($this->admin_dir.'/import/'.strval(preg_replace('/\.{2,}/', '.', $this->csv)), 'r');

        if (!$handle)
            $this->errors[] = Tools::displayError('Cannot read the .CSV file');

        $this->rewindBomAware($handle);

        for ($i = 0; $i < (int)$skip; ++$i)
            $line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator);
        return $handle;
    }

        protected  function rewindBomAware($handle)
    {
        // A rewind wrapper that skip BOM signature wrongly
        rewind($handle);
        if (($bom = fread($handle, 3)) != "\xEF\xBB\xBF")
            rewind($handle);
    }
    protected function closeCsvFile($handle)
    {
        fclose($handle);
    }
   
   
  protected function utf8EncodeArray($array)
    {
        return (is_array($array) ? array_map('utf8_encode', $array) : utf8_encode($array));
    }

   protected function getMaskedRow($row)
    {
        $res = array();
        if (is_array($this->column_mask))
            foreach ($this->column_mask as $type => $nb)
                $res[$type] = isset($row[$nb]) ? $row[$nb] : null;
/*
        if ($this->forceIds) // tohle maji blbe proto jim to funguje, ma byt forceIDs if you choose to force table before import the column id is remove from the CSV file.
            unset($res['id']);
*/
        return $res;
    } 
  
  // todo ... set defaults  
      protected static function setEntityDefaultValues(&$entity)
    {
        $members = get_object_vars($entity);
        foreach (self::$default_values as $k => $v)
            if ((array_key_exists($k, $members) && $entity->$k === null) || !array_key_exists($k, $members))
                $entity->$k = $v;
    } 
    protected static function createMultiLangField($field)
    {
        $languages = Language::getLanguages(false);
        $res = array();
        foreach ($languages as $lang)
            $res[$lang['id_lang']] = $field;
        return $res;
    }
  
       protected static function copyImg($id_entity, $id_image = null, $url, $entity = 'products', $regenerate = true)
    {
        $tmpfile = tempnam(_PS_TMP_IMG_DIR_, 'ps_import');
        $watermark_types = explode(',', Configuration::get('WATERMARK_TYPES'));

        switch ($entity)
        {
            default:
            case 'products':
                $image_obj = new Image($id_image);
                $path = $image_obj->getPathForCreation();
            break;
            case 'categories':
                $path = _PS_CAT_IMG_DIR_.(int)$id_entity;
            break;
        }
        $url = str_replace(' ', '%20', trim($url));

        // Evaluate the memory required to resize the image: if it's too much, you can't resize it.
        if (!ImageManager::checkImageMemoryLimit($url))
            return false;

        // 'file_exists' doesn't work on distant file, and getimagesize make the import slower.
        // Just hide the warning, the traitment will be the same.
        if (Tools::copy($url, $tmpfile))
        {
            ImageManager::resize($tmpfile, $path.'.jpg');
            $images_types = ImageType::getImagesTypes($entity);

            if ($regenerate)
                foreach ($images_types as $image_type)
                {
                    ImageManager::resize($tmpfile, $path.'-'.stripslashes($image_type['name']).'.jpg', $image_type['width'], $image_type['height']);
                    if (in_array($image_type['id_image_type'], $watermark_types))
                        Hook::exec('actionWatermark', array('id_image' => $id_image, 'id_product' => $id_entity));
                }
        }
        else
        {
            unlink($tmpfile);
            return false;
        }
        unlink($tmpfile);
        return true;
    }
     
    public function __destruct() {
     $sql='UPDATE `'._DB_PREFIX_.'import_match` SET skip = '.(int)$this->skip.' WHERE name="'.pSQL($this->mask).'"';
     Db::getInstance()->execute($sql);
    }  
  }
?>
