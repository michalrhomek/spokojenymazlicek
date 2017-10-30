<?php
  class Doprava  {
       private $zone;
       private $carriers;
       private $free_shipping_weight;
       private $free_shipping_price;
       private $id_country;
       private $cods;
       private  $codpodledopravy;
       
       public function __construct($zone, $carriers, $free_shipping_weight, $free_shipping_price, $id_country, $cods, $codpodledopravy) {
           $this->zone = $zone;
           $this->carriers = $carriers;
           $this->free_shipping_weight = $free_shipping_weight;
           $this->free_shipping_price  = $free_shipping_price;
           $this->id_country = $id_country;
           $this->cods = $cods;
           $this->codpodledopravy = $codpodledopravy;
       }
      
public function getDoprava($product, $old = 0) {
    
    
    $kupon = 0;
    if($old == 1) {
       $result=  Carrier::getCarriers(Configuration::get('PS_LANG_DEFAULT'), true, false, (int)$this->zone, null, 4); 
    }
    else 
    {
    $sql='
    SELECT c.*
    FROM `'._DB_PREFIX_.'product_carrier` pc
    INNER JOIN `'._DB_PREFIX_.'carrier` c
    ON (c.`id_reference` = pc.`id_carrier_reference` AND c.`deleted` = 0)
    WHERE pc.`id_product` = '.(int)$product['id_product'].'
    AND pc.`id_shop` = '.(int)Context::getContext()->shop->id;   
    $result= Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
     if(!$result || !count($result)) {
        $result=  Carrier::getCarriers(Configuration::get('PS_LANG_DEFAULT'), true, false, (int)$this->zone, array(Configuration::get('PS_UNIDENTIFIED_GROUP')), Carrier::PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE);
    } 
    
    if(isset($product['extramessage']) && strlen($product['extramessage'])) {
        $kupon = $product['extramessage'][7];
    }
    }
   
  
    
    if(is_array($result) && count($result)) {
        $retval=array();
        foreach ($result as $k => $row) {
            $heureka_carrier= $this->carrierMap($row['id_carrier']);
            if($heureka_carrier === false)
            continue;

            $carrier=new Carrier($row['id_carrier']);
            
            $price=false;
             
            $shipping_method = $carrier->getShippingMethod();  
            
            if($this->skipCarrier($carrier, $shipping_method, $this->zone, $product['price']))
              continue;
         
            $price = $this->getCarrierPrice($carrier, $shipping_method, $this->zone, $kupon, $product);
      

            $sql='SELECT t.rate FROM '._DB_PREFIX_.'tax_rule r LEFT JOIN  '._DB_PREFIX_.'tax t ON
            t.id_tax = r.id_tax WHERE r.id_tax_rules_group='.(int)$carrier->id_tax_rules_group.' AND
            r.id_country='.$this->id_country;
            $rate= Db::getInstance()->getValue($sql);

            if($rate)
            $price=$price*(100+$rate)/100; 

            if(isset($this->cods[$row['id_carrier']])) {
            if((int)$this->codpodledopravy  && $price == 0)
                $retval[]=array($heureka_carrier, $price, 0);  
            else
                $retval[]=array($heureka_carrier, $price, $this->cods[$row['id_carrier']]);  
            } 
            else 
            $retval[]=array($heureka_carrier, $price);   
        }

        return $retval;
    }
} 


public function getDopravaGoogle ($product) {
        $kupon = 0;
        if(isset($product['extramessage']) && strlen($product['extramessage'])) {
           $kupon = $product['extramessage'][7];
        }
   $sql='SELECT c.*
            FROM `'._DB_PREFIX_.'product_carrier` pc
            INNER JOIN `'._DB_PREFIX_.'carrier` c
                ON (c.`id_reference` = pc.`id_carrier_reference` AND c.`deleted` = 0)
            WHERE pc.`id_product` = '.(int)$product['id_product'].'
                AND pc.`id_shop` = '.(int)Context::getContext()->shop->id;
 $result= Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
 if(!$result || !count($result)) {
     $result=  Carrier::getCarriers(Configuration::get('PS_LANG_DEFAULT'), true, false, null, array(Configuration::get('PS_UNIDENTIFIED_GROUP')), Carrier::PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE);
 }

 
     $retval=array();
  foreach ($result as $k => $row) {
        
        if($this->carrierAllowed($row['id_carrier']) === false)
          continue;
        
           $sql='SELECT id_zone FROM '._DB_PREFIX_.'carrier_zone WHERE id_carrier='.(int)$row['id_carrier'];
            $zones=Db::getInstance()->executeS($sql);
       
         $carrier=new Carrier($row['id_carrier']);
       
         $shipping_method = $carrier->getShippingMethod();
         
        foreach($zones as $zone) {
        
        $id_zone = $zone['id_zone'];
        $id_country=Db::getInstance()->getValue('SELECT id_country FROM '._DB_PREFIX_.'country WHERE id_zone='.(int)$id_zone);
        if(isset($this->carrierCache[$row['id_carrier']][$id_zone][$product['id_product']])) {
            if(!($this->carrierCache[$row['id_carrier']][$id_zone][$product['id_product']] === false)) {
                 $retval[]=array($carrier->name, $this->carrierCache[$row['id_carrier']][$id_zone][$product['id_product']], $id_zone, $carrier->id_tax_rules_group);
            }
            continue;
        }
        
        
        
        
         if($this->skipCarrier($carrier, $shipping_method, $id_zone, $product['price'])) 
            continue;
            
         $price = $this->getCarrierPrice($carrier, $shipping_method, $id_zone, $kupon, $product);
   

     global $CurrencyTo;
       if(! is_null($CurrencyTo)) {
             $price=Tools::convertPrice($price, $CurrencyTo); 
       }
        
           $this->carrierCache[$row['id_carrier']][$id_zone][$product['id_product']]=$price;
           $retval[]=array($carrier->name, $price, $id_zone, $carrier->id_tax_rules_group);
  
    }
  }
  return $retval;  
}


protected function  getCarrierPrice($carrier, $shipping_method,$id_zone, $kupon, $product) {
             
            if($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT) {
            if($this->free_shipping_weight > 0 && $product['weight'] >=  $this->free_shipping_weight)
            $price=0;
            else
            $price=$carrier->getDeliveryPriceByWeight($product['weight'], $id_zone);

            }
            elseif ($shipping_method == Carrier::SHIPPING_METHOD_PRICE) {
            if($this->free_shipping_price > 0 && $product['price'] >=  $this->free_shipping_price)
            $price=0;
            else  {
            $price=$carrier->getDeliveryPriceByPrice($product['price'], $id_zone);
            }
            } 
            else {
              $price = 0;  
            }

            if($kupon) {
                 $price = 0;  
            }
            
            $price=(float)$price;
            return $price;
            
}

protected function   skipCarrier($carrier, $shipping_method, $id_zone, $product_price_tax) {
   if ($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT ||  $shipping_method== Carrier::SHIPPING_METHOD_PRICE )
   {
            // Get only carriers that are compliant with shipping method
            if (($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT && $carrier->getMaxDeliveryPriceByWeight($id_zone) === false)
            || ($shipping_method == Carrier::SHIPPING_METHOD_PRICE && $carrier->getMaxDeliveryPriceByPrice($id_zone) === false))
            {
            return true;
            }

            // If out-of-range behavior carrier is set on "Desactivate carrier"
            if ($carrier->range_behavior)
            {
            // Get only carriers that have a range compatible with cart
            if (($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT
            && (!Carrier::checkDeliveryPriceByWeight($carrier->id, $product['weight'], $id_zone)))
            || ($shipping_method == Carrier::SHIPPING_METHOD_PRICE
            && (!Carrier::checkDeliveryPriceByPrice($carrier->id, $product_price_tax, $id_zone, Context::getContext()->currency->id))))
            {
            return true;
            }
            }
   }
   return false;
  } 

protected function carrierMap($id_carrier) {
    if(!is_array($this->carriers) ||
       !count($this->carriers) || 
       !isset($this->carriers[$id_carrier]) ||
        empty($this->carriers[$id_carrier])
     )
      return false;
    
  
   
     return   $this->carriers[$id_carrier];
     
  
}

protected function carrierAllowed($id_carrier) {
    if(!is_array($this->carriers) ||
       !count($this->carriers) || 
       !isset($this->carriers[$id_carrier]) ||
        empty($this->carriers[$id_carrier])
     )
      return false;
    
  
   
     return  true;

}
      
}
 
