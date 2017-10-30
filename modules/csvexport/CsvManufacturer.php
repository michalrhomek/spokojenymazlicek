<?php
  class CsvManufacturer {
    
      public function getItems($id_lang) {
         $sql = 'SELECT m.*, ml.`description`, ml.`short_description`, ml.`meta_title`, ml.`meta_keywords`, ml.`meta_description`';
        $sql.= ' FROM `'._DB_PREFIX_.'manufacturer` as m
        LEFT JOIN `'._DB_PREFIX_.'manufacturer_lang` ml ON (m.`id_manufacturer` = ml.`id_manufacturer` AND ml.`id_lang` = '.intval($id_lang).')';
        $sql.= ' ORDER BY m.`name` ASC';
        $manufacturers = Db::getInstance()->ExecuteS($sql); 
        
        for($i=0; $i<count($manufacturers); $i++) {
            if(!isset($manufacturers[$i]['active'])) {
                $manufacturers[$i]['active']=1;
            }
        }
        return $manufacturers;  
      }

     
  }
?>
