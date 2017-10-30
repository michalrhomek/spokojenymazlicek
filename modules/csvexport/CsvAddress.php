<?php
  class CsvAddress {
    
      public function getItems($id_lang) {
         $sql = 'SELECT a.*, c.email FROM `'._DB_PREFIX_.'address` as a,`'._DB_PREFIX_.'customer` c 
         WHERE  a.`id_customer` = c.`id_customer` OR  a.`id_customer` IS NULL
        ';
        $sql.= ' ORDER BY a.`id_address` ASC';
        $addresses = Db::getInstance()->ExecuteS($sql); 
        
        for($i=0; $i<count($addresses); $i++) {
            if(!isset($addresses[$i]['active'])) {
                $addresses[$i]['active']=1;
            }
        }
        return $addresses;  
      }

     
  }
?>
