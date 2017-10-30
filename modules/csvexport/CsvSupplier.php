<?php
  class CsvSupplier {
    
      public function getItems($id_lang) {
         $sql = 'SELECT m.*, ml.`description`, ml.`meta_title`, ml.`meta_keywords`, ml.`meta_description`';
        $sql.= ' FROM `'._DB_PREFIX_.'supplier` as m
        LEFT JOIN `'._DB_PREFIX_.'supplier_lang` ml ON (m.`id_supplier` = ml.`id_supplier` AND ml.`id_lang` = '.intval($id_lang).')';
        $sql.= ' ORDER BY m.`name` ASC';
        $suppliers = Db::getInstance()->ExecuteS($sql); 
        
        for($i=0; $i<count($suppliers); $i++) {
            if(!isset($suppliers[$i]['active'])) {
                $suppliers[$i]['active']=1;
            }
        }
        return $suppliers;  
      }

     
  }
?>
