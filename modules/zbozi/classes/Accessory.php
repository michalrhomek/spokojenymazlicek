<?php
class Accessory {
	
	 
	
	public function resetCache() {
		 require_once(_PS_MODULE_DIR_.'/zbozi/zbozi.php');
		 $path = Zbozi::getCachePath('accessory');
		 if(file_exists($path))
		   unlink($path);
		 $content = $this->loadAccessory();
		 $content = json_encode($content);
		 file_put_contents($path, $content);
	}
	
	public function loadFromCache() {
		 require_once(_PS_MODULE_DIR_.'/zbozi/zbozi.php');
		 $path = Zbozi::getCachePath('accessory');
		  if(file_exists($path)) {
		  	  $content = file_get_contents($path);
		    	return  json_decode($content, true);
		  	   
		  	  
		  }
		  return array();
	}
	
	
	protected function loadAccessory() {
		
		$sql ='SELECT DISTINCT id_product_2 FROM  '._DB_PREFIX_.'accessory';
		$acc  = Db::getInstance()->executeS($sql);
		$accessories = array();
		foreach($acc  as  $ac) {
			$sql ='SELECT id_product_attribute FROM '._DB_PREFIX_.'product_attribute WHERE id_product='.(int) $ac['id_product_2'] .'
			AND default_on = 1';
			$id_attribute = Db::getInstance()->getValue($sql);
			if($id_attribute && (int)$id_attribute)
			$id =  $ac['id_product_2'].'-'.$id_attribute;
			else
			 $id = $ac['id_product_2'];
			$accessories[$ac['id_product_2']] = $id;
		}
		
	    $sql ='SELECT  * FROM '._DB_PREFIX_.'accessory';
	    $rows = Db::getInstance()->executeS($sql);
	    $retval = array();
	    foreach($rows as $row) {
	    	 $retval[$row['id_product_1']][] =   isset($accessories[$row['id_product_2']])?$accessories[$row['id_product_2']]:false;
		}
		
	   return $retval;
	}

	
}