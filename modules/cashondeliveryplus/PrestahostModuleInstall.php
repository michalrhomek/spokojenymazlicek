<?php
  class PrestahostModuleInstall {
      protected $module;
      
      public function __construct($module) {
             $this->module=$module;
      }
      
      public function addState($statename, $translations, $color='#FFC3C3', $email=0) {
           
          $values=array(
             'id_order_state'=>null,
             'invoice'=>1,
             'send_email'=>$email,
             'color'=>$color, 
             'unremovable'=>0
             );    
             Db::getInstance()->autoExecuteWithNullValues(_DB_PREFIX_.'order_state',$values,'INSERT');
             
             $lastid=Db::getInstance()->Insert_ID();
             if($lastid) {
                 Configuration::updateValue($statename, $lastid, 0, 0 );
                 $langs=Context::getContext()->language->getLanguages(true);
                 foreach($langs as $lang) {
                   $name=isset($translations[$lang['iso_code']])?$translations[$lang['iso_code']]:$translations[$lang['en']];
                   $values=array(
                 'id_order_state'=>$lastid,
                  'id_lang'=>$lang['id_lang'],
                  'name'=>pSQL($name),
                  'template'=>pSQL($this->module->name), 
                 );
                  Db::getInstance()->AutoExecute(_DB_PREFIX_.'order_state_lang',$values,'INSERT');
                 
                 }  
                 
                return true;
             } 
        return false;
      }
      
      
  public  function installExternalCarrier($config)
    {
        $carrier = new Carrier();
        $carrier->name = $config['name'];
        $carrier->id_tax_rules_group = $config['id_tax_rules_group'];
        $carrier->id_zone = $config['id_zone'];
        $carrier->active = $config['active'];
        $carrier->deleted = $config['deleted'];
        $carrier->delay = $config['delay'];
        $carrier->shipping_handling = $config['shipping_handling'];
        $carrier->range_behavior = $config['range_behavior'];
        $carrier->is_module = $config['is_module'];
        $carrier->shipping_external = $config['shipping_external'];
        $carrier->external_module_name = $config['external_module_name'];
        $carrier->need_range = $config['need_range'];

        $languages = Context::getContext()->language->getLanguages(true);
        foreach ($languages as $language)
        {
            if ($language['iso_code'] == 'cs')
                $carrier->delay[(int)$language['id_lang']] = $config['delay'][$language['iso_code']];
            else
                $carrier->delay[(int)$language['id_lang']] = '2 days';
        }

        if ($carrier->add())
        {
            $groups =   Group::getGroups(true);
            foreach ($groups as $group)
                Db::getInstance()->autoExecute(_DB_PREFIX_.'carrier_group', array('id_carrier' => (int)($carrier->id), 'id_group' => (int)($group['id_group'])), 'INSERT');

            $rangePrice = new RangePrice();
            $rangePrice->id_carrier = $carrier->id;
            $rangePrice->delimiter1 = '0';
            $rangePrice->delimiter2 = '1000000';
            $rangePrice->add();

            $rangeWeight = new RangeWeight();
            $rangeWeight->id_carrier = $carrier->id;
            $rangeWeight->delimiter1 = '0';
            $rangeWeight->delimiter2 = '10000';
            $rangeWeight->add();
            $sql='SELECT DISTINCT id_zone FROM '._DB_PREFIX_.'country WHERE iso_code="CZ" OR iso_code="SK"';
            $zones=Db::getInstance()->executeS($sql);
           if(is_array($zones)) {
            foreach ($zones as $zone)
            {
                Db::getInstance()->autoExecute(_DB_PREFIX_.'carrier_zone', array('id_carrier' => (int)($carrier->id), 'id_zone' => (int)($zone['id_zone'])), 'INSERT');
                Db::getInstance()->autoExecuteWithNullValues(_DB_PREFIX_.'delivery', array('id_carrier' => (int)($carrier->id), 'id_range_price' => (int)($rangePrice->id), 'id_range_weight' => NULL, 'id_zone' => (int)($zone['id_zone']), 'price' => '0'), 'INSERT');
                Db::getInstance()->autoExecuteWithNullValues(_DB_PREFIX_.'delivery', array('id_carrier' => (int)($carrier->id), 'id_range_price' => NULL, 'id_range_weight' => (int)($rangeWeight->id), 'id_zone' => (int)($zone['id_zone']), 'price' => '0'), 'INSERT');
            }
           }
    
        $this->module->copyLogo($carrier->id);
           
            return (int)($carrier->id);
        }

        return false;
    }
   
   
   public function unistallExternalCarrier($id_carrier) {
        if( (int)$id_carrier) {
   $Carrier1 = new Carrier((int)($id_carrier));
     
        // If external carrier is default set other one as default
        if (Configuration::get('PS_CARRIER_DEFAULT') == (int)($Carrier1->id))
        {
            $this->module->_errors[]='Please select different default carrier before uninstall';
            return false;     
        } 
           // Then delete Carrier
        $Carrier1->deleted = 1;
        if (!$Carrier1->update())
            return false;
    }  
    return true;
   }
   
   
   public  function installModuleTab($tabClass, $tabName, $parentName)
{
  $sql='SELECT id_tab FROM '._DB_PREFIX_.'tab WHERE class_name="'.pSQL($parentName).'"';
  $idTabParent=Db::getInstance()->getValue($sql);
  if(!$idTabParent )
     return false;
     
  @copy(_PS_MODULE_DIR_.$this->module->name.'/logo.gif', _PS_IMG_DIR_.'t/'.$tabClass.'.gif');
  $tab = new Tab();
  $tabNames=array();
  foreach (Language::getLanguages(false) as $language) {
     $tabNames[$language['id_lang']] =$tabName; 
  }
  $tab->name = $tabNames;
  $tab->class_name = $tabClass;
  $tab->module = $this->module->name;
  $tab->id_parent = $idTabParent;
  if(!$tab->save())
    return false;
    
  if(!Tab::initAccess($tab->id)) 
  return false;
  
  return true;
} 

public function uninstallModuleTab($tabClass)
{
  $idTab = Tab::getIdFromClassName($tabClass);
  if($idTab != 0)
  {
    $tab = new Tab($idTab);
    $tab->delete();
    return true;
  }
  return true; // true even on failed
} 

public function installSql() {
     $sql="CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."ulozenka` (
  `id_order` int(10) unsigned NOT NULL DEFAULT '0',
  `dobirka` tinyint(3) unsigned DEFAULT '0',
  `date_exp` date DEFAULT NULL,
  `pobocka` varchar(25) COLLATE utf8_czech_ci DEFAULT NULL,
  `pobocka_name` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  `exported` tinyint(3) unsigned DEFAULT '0',
  PRIMARY KEY (`id_order`),
  CONSTRAINT `id_order` FOREIGN KEY (`id_order`) REFERENCES `"._DB_PREFIX_."orders` (`id_order`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci";

return Db::getInstance()->executeS($sql);


}

public function uninstallSql() {
     $sql="DROP TABLE `"._DB_PREFIX_."ulozenka`";

  $retval= Db::getInstance()->executeS($sql);
  return $retval;

} 

  
  }
?>
