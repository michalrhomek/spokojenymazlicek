<?php
// place to root
require('./config/config.inc.php');
$sql='SELECT id_product_attribute, id_product FROM ps_product_attribute';
 $attributes= Db::getInstance()->executeS($sql);
 foreach($attributes as $at) {
        $sql='SELECT id_image FROM ps_image WHERE id_product='.(int)$at['id_product'];
        $id_image  =    Db::getInstance()->getValue($sql);
        if($id_image) {
           $sql='DELETE FROM ps_product_attribute_image WHERE id_product_attribute='.(int) $at['id_product_attribute'];
            Db::getInstance()->executeS($sql);
           $sql='INSERT INTO ps_product_attribute_image SET 
            id_product_attribute= '.(int) $at['id_product_attribute'].', id_image='.(int)$id_image;
          Db::getInstance()->executeS($sql);  
        }
 }

//$attributes= Db::getInstance()->getRow($sql);
