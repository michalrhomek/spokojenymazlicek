<?php
  class CsvCustomer {
    
      public function getItems($id_lang) {
         $sql = 'SELECT c.*';
        $sql.= ' FROM `'._DB_PREFIX_.'customer` as c';
        $sql.= ' ORDER BY c.`id_customer` ASC';
        $customers = Db::getInstance()->ExecuteS($sql); 
        
        for($i=0; $i<count($customers); $i++) {
            if(!isset($customers[$i]['active'])) {
                $customers[$i]['active']=1;
            }
        }
        return $customers;  
      }

     
  }
?>
