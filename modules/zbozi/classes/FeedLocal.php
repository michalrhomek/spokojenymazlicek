<?php
  require_once("./classes/cFeed.php");
  class FeedLocal extends cFeed {
      
   
     protected function addFeatures($features) {
         $retval='';
         foreach($features as $feature) {
              $retval.="\t\t\t<PARAM>\n\t\t\t\t<PARAM_NAME>{$this->prepareString($feature['name'])}</PARAM_NAME><VAL>{$this->prepareString($feature['value'])}</VAL>\n\t\t\t</PARAM>\n";
         }
         return $retval;
     }
 
     
     function addProductAndProductname($product, $reference, $ean, $combination = false) 
     { 
      
       /*     
        0 Přidat k názvu produktu(Zbozi i Heureka, PRODUCT)
        1 Přidat k názvu produktu (jen Zboži, PRODUCT)
        2 Přidat k názvu produktu (jen Heureka, PRODUCT)
        3 Použít namísto názvu produktu (Zboži i Heureka, PRODUCT i PRODUCTNAME)
        4 Použít namísto názvu produktu (jen Zboži, PRODUCT i PRODUCTNAME)
        5 Použít namísto názvu produktu (jen Heuréka, PRODUCT i PRODUCTNAME)
        6 Použít namísto názvu produktu odlišně pro Heureka a Zboží. Zadejte ve tvaru "Název pro Heureka|Název pro Zboží"
        7 Přidat k názvu produktu odlišně pro Heureka a Zboží. Zadejte ve tvaru "Dodatek pro Heureka|Dodatek pro Zboží"
        8 Přidat před název produktu odlišně pro Heureka a Zboží. Zadejte ve tvaru "Přidat před Heureka|Přidat před Zboží
        */
        $name = $product['name'];
        $attributename ='';    
   
        if($combination &&  ($this->upname != 'GLAMI' || Configuration::get('ZBOZI_FILTERATR_'.$this->upname))) {
            $attributename =  $this->getCombinationName($combination['attributes']);
            $name .= $attributename;
        }

        $manufacturername =  $product['manufacturer_name'];
        if($this->upname == 'SEZNAM') {
         if(isset($product['productline']) && strlen($product['productline'])) {
            $manufacturername.= ' '.$product['productline']; 
         }
        }
        $optvals=array(
            'name'=>$this->prepareString($name),
            'manufacturer'=>$this->prepareString($manufacturername),
            'reference'=>$this->prepareString($reference),
            'ean'=>$this->prepareString($ean)
        );

        

        $replaceNazev = 0;
        if($this->ext_behav == 1)
            $replaceNazev = 1;

        $extended =$this->getExtendedText($product);
        $extended_noattr = $extended;
        if($replaceNazev && Configuration::get('ZBOZI_TEXT_EXTATT')) {
           $extended .= $attributename;
        }
        
        $sleva = $this->getSleva($product);
        global $feed;
        // PRODUCTNAME je bez rozisireni
        // presny jen nahrazuji
        $feedname = $feed;
        if($feed == 'seznam')
           $feedname = 'zbozi';
           
        if($feed == 'glami') {
           if((int)Configuration::get('ZBOZI_GLNAME') == 0)
                $feedname = 'heureka';
           elseif((int)Configuration::get('ZBOZI_GLNAME') == 1) 
                $feedname ='zbozi';
        }
        $item='';
        if( $replaceNazev && strlen($extended_noattr)) 
            $item.=$this->createTag('PRODUCTNAME', $extended);
        else
            $item.=$this->createTag('PRODUCTNAME', $this->compileOptimisedTag($feedname, 'productname', $optvals));

       
              
        // PRODUCT  obsahuje rozisireni
        // rozsiruji nazev    
        if($this->ext_behav == 0 )  {
            $item.=$this->createTag('PRODUCT', $this->compileOptimisedTag($feedname, 'product', $optvals).$extended.$sleva);
        }
        elseif($this->ext_behav == 2) {
            $item.=$this->createTag('PRODUCT', $extended.$this->compileOptimisedTag($feedname, 'product', $optvals).$sleva);
        } 
        elseif($this->ext_behav == 1) {
        if( $replaceNazev && strlen($extended_noattr)) // 1
            $item.=$this->createTag('PRODUCT', $extended);
        else
            $item.=$this->createTag('PRODUCT', $this->compileOptimisedTag($feedname, 'product', $optvals));  
        }
        return $item;
    }
     
     
     protected function getExtendedText($product) {
        $retval='';
        $text ='';
        
        if($this->upname == 'SEZNAM')
          $text = isset($product['zbozi_text'])?$product['zbozi_text']:'';
        elseif($this->upname == 'HEUREKA')
          $text = isset($product['heureka_text'])?$product['heureka_text']:'';
        
           
        switch($this->ext_behav) {
          case 0: {
            if($text && strlen($text))
              $retval=' '.$text;
          }; break; 
          case 1: {
             $retval = $text; 
          }
             break;
          case 2: {
             if($text && strlen($text))
              $retval=$text.' ';
             
          }; break; 
          default: return ''; 
        }
        
       
        return $this->prepareString($retval);
     }
     
     protected function getSleva($product) {
    
      $retval='';  
    global $feed;  
    
    if(Configuration::get('ZBOZI_'.strtoupper($feed).'_SLEVA') && $product['specific_price']) {
         
         $date = date('Y-d-m H:i:s');
         if((int)$product['specific_price']['id_cart'] == 0 &&
          (int)$product['specific_price']['id_group']  == 0 &&
           (int)$product['specific_price']['id_customer']   == 0   &&
           (int)$product['specific_price']['id_currency']   == 0   &&
           (int)$product['specific_price']['from_quantity']   == 1   &&
           ( $product['specific_price']['from'] == '0000-00-00 00:00:00' || $date >=  $product['specific_price']['from']) &&
           ( $product['specific_price']['to'] == '0000-00-00 00:00:00' || $date <=  $product['specific_price']['to'])  &&
           (int)$product['specific_price']['id_product_attribute'] == 0
          ) 
          {
             
                   $sleva = Context::getContext()->language->iso_code == 'sk'?'Zľava':'Sleva';
                     if($product['specific_price']['reduction_type'] == 'percentage') {
                       $retval.=' '.$sleva .' '.Tools::ps_round($product['specific_price']['reduction']*100, 2).'%';
                   }
                   elseif($product['specific_price']['reduction_type'] == 'amount') {
                           if(! is_null($CurrencyTo)) {
                               $retval.=' '.$sleva.' '.Tools::ps_round(Tools::convertPrice($product['specific_price']['reduction'] ,  $CurrencyTo), 2).' '.$CurrencyTo->sign; 
                         }
                        else {
                          global $currency;
                            $retval.=' '.$sleva.' '.Tools::ps_round($product['specific_price']['reduction'], 2).$currency->sign;
                        }   
                   }
          }
      }
      
    return $retval;
 }  
     
  protected function featuresFromCombination($combination, $features=null) {
      if(!count($this->transformed))
         return $features;
         
       $transformed=$this->transformed;
       $retval=array();
       $keys=array();
       foreach($transformed as $t) {
           
           foreach($combination['attributes'] as $at) {
              if(trim($t[0]) == trim($at[0]))  {
                $keys[]=  $t[0];
                $retval[]=array('name'=>$t[1], 'value'=>$at[1]);
              }
               }
       }
  if(isset($features) && is_array($features) && count($features)) {
      if(count($keys)) {
      while(list($key,$val)=each($features)) {
        if(! in_array($val['name'], $keys))
          $retval[]=array('name'=>$val['name'], 'value'=>$val['value']);   
      }
      }
      else {
        return $features;   
      }
  }
  return $retval;  
 } 
 
 protected function  getFilteredAttributes($feed) {
  $feed = strtoupper($feed);
  $filteredAttributes = array();
  $attr = json_decode(Configuration::get('ZBOZI_USEDATTR_'.$feed), true);
          while(list($key, $val) = each($attr)) {
              if($val != 3) {
                 $filteredAttributes[] = $key;
              } 
          }
  return $filteredAttributes;
  }
  
  } 
