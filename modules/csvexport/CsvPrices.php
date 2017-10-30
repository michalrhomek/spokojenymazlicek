<?php
  class CsvPrices {
   
   public  function getPriceReduction($id_product) {
     $version=$this->getVersion();
     $method='getPriceReduction'.$version;
      if(method_exists($this, $method))
        return $this->$method($id_product);
        
      return $this->getPriceReductionDefault($id_product);
       
   }
   
   private function getVersion() {
       $s=str_replace('.','', _PS_VERSION_); 
       return substr( $s, 0,2);
   }
      
  private   function    getPriceReduction13($id_product) {
     $retval=  Db::getInstance()->getRow(
     'SELECT  `price`,`reduction_price`,`reduction_percent`,`reduction_from`,`reduction_to`
                FROM '._DB_PREFIX_.'product WHERE id_product='.(int) $id_product);
     return $retval;
                
       
   }
   
     private   function    getPriceReduction12($id_product) {
     $retval=  Db::getInstance()->getRow(
     'SELECT  `price`,`reduction_price`,`reduction_percent`,`reduction_from`,`reduction_to`
                FROM '._DB_PREFIX_.'product WHERE id_product='.(int) $id_product);
     return $retval;
                
       
   }
   
 private   function    getPriceReductionDefault($id_product) { 
                           // getSpecificPrice($id_product, $id_shop, $id_currency, $id_country, $id_group, $quantity)
       $prices=SpecificPrice::getSpecificPrice($id_product, 0,          0,             0,           0,        1);
       $retval=array('reduction_from'=>'', 'reduction_to'=>'', 'reduction_percent'=>'', 'reduction_price'=>'' );
       if($prices) {
            
            $retval['reduction_from']=$prices['from'];  
            $retval['reduction_to']=$prices['to'];   
            if($prices['reduction_type']=='percentage') {
               $retval['reduction_percent'] =$prices['reduction']*100; 
            }
            elseif($prices['reduction_type']=='amount') {
                $retval['reduction_price'] =$prices['reduction']; 
            }
            return $retval;
       }
 
 }
  }
?>
