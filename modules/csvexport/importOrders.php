<?php

require_once('./classes/Database.php');
require('./config/config.inc.php');
$database=Database::getInstance();
/*
TRUNCATE table ps_cart;
TRUNCATE table ps_order_carrier;
TRUNCATE table ps_order_detail;
TRUNCATE table ps_order_detail_tax;
TRUNCATE table ps_order_history;
TRUNCATE table ps_order_invoice;
TRUNCATE table ps_order_invoice_payment;
TRUNCATE table ps_order_invoice_tax;
TRUNCATE table ps_order_payment;
TRUNCATE table ps_orders;
*/ 
 
 $link=$database->getLink();

// $myquery='SELECT * FROM ptrial_prectene_knihy_old.ps_orders WHERE id_order =111';
 //  $myquery='SELECT * FROM ptrial_prectene_knihy_old.ps_orders WHERE id_order > 2 AND id_order <= 1000';
 
 //  $myquery='SELECT * FROM ptrial_prectene_knihy_old.ps_orders WHERE id_order > 1000 AND id_order <= 2000';
 $myquery='SELECT * FROM ptrial_prectene_knihy_old.ps_orders WHERE id_order > 2000';
 
  
 $oldorders=$database->fetch_assoc($myquery);
 
 mysql_select_db(trim('ptrial_prectene'), $link);
  

 foreach($oldorders as $oldorder) {
   $myquery='INSERT INTO ptrial_prectene.ps_orders SET id_order='.(int)$oldorder['id_order'];
     Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($myquery); 
     
   $myquery='SELECT * FROM ptrial_prectene_knihy_old.ps_order_detail WHERE id_order='.(int)$oldorder['id_order'];
   $details=$database->fetch_assoc($myquery);
     
    
    $order=new Order();   
    $order->id_shop_group=1;  
    $order->id_shop=1; 
    $order->id_lang=2;
   
    $order->id_customer=(int)$oldorder['id_customer']; 
    $id_address_delivery=(int)$oldorder['id_address_delivery'];
    $id_address_invoice=(int)$oldorder['id_address_invoice']; 
    
    
    // adreasam se nezavhovala ID
    $id_address_delivery=$database->get_single('SELECT id_address FROM ps_address WHERE id_customer ='.(int) $oldorder['id_customer']. ' AND
    id_address <='.(int)$id_address_delivery);
    
     $id_address_invoice=$database->get_single('SELECT id_address FROM ps_address WHERE id_customer ='.(int) $oldorder['id_customer']. ' AND
     id_address <='.(int)$id_address_invoice);
    
    $order->id_address_delivery=(int)$id_address_delivery;
    $order->id_address_invoice=(int)$id_address_invoice; 
    

    $order->id_currency=1; 
    $order->conversion_rate =1;
    
     
    $order->id_carrier =getCarrier($oldorder['id_carrier']);
    
    $sql='SELECT   ptrial_prectene_knihy_old.ps_order_history.id_order_state  FROM   ptrial_prectene_knihy_old.ps_order_history WHERE id_order='.(int)$oldorder['id_order'].'
    ORDER BY date_add DESC LIMIT 1';
    $order->current_state=$database->get_single($sql);
     $order->invoice_date=$oldorder['invoice_date'];
    $order->delivery_date=$oldorder['delivery_date'];
  
      $order->payment=$oldorder['payment'];
      $order->module=$oldorder['module'];
      $order->date_add=$oldorder['date_add'];
      $order->date_upd=$oldorder['date_upd'];
       $order->total_shipping    =        $oldorder['total_shipping'];
      $order->total_shipping_tax_incl =   $oldorder['total_shipping'];
      $order->total_shipping_tax_excl  =  $oldorder['total_shipping'];
      
      $order->invoice_number= $oldorder['invoice_number'];
     //  $order->invoice_number=0;
      $order->total_paid = $oldorder['total_paid'];
      $order->total_paid_real= $oldorder['total_paid_real'];
      

     $order->total_products=$oldorder['total_products'];
     $order->total_products_wt= $oldorder['total_products_wt'];
     $order->total_paid_tax_incl = $order->total_paid;
     $dan= $order->total_products- $order->total_products_wt;
     $order->total_paid_tax_excl  = $order->total_paid -$dan;
     $order->valid=true;
  
    
     $order->id_cart=$oldorder['id_cart'];
     
       $myquery='INSERT INTO ptrial_prectene.ps_cart SET  id_cart='.$order->id_cart.',
     id_shop =1, id_shop_group=1, id_currency='.$order->id_currency.',
     id_customer='.$order->id_customer.', date_add="'.$order->invoice_date.'", date_upd="'.$order->invoice_date.'"';
     $database->insert($myquery);
     
    
     $customer = new Customer($order->id_customer);
     $order->secure_key=$customer->secure_key; 
     
     $order->id=$oldorder['id_order'];
     $order->reference=$oldorder['id_order'];
     if($order->update()) {
         $sql='SELECT * FROM ptrial_prectene_knihy_old.ps_order_detail WHERE id_order= '.(int)$oldorder['id_order'];
         $polozky=$database->fetch_assoc($sql);
        foreach($polozky as $polozka) {
          
           $Detail=new OrderDetail();
           $Detail->id_order =$order->id;
           $Detail->product_id=$polozka['product_id'];
           $Detail->product_name=$polozka['product_name'];
           $Detail->id_shop=1;
           $Detail->product_quantity=$polozka['product_quantity'];
           $Detail->product_price=$polozka['product_price']* $Detail->product_quantity;
           $Detail->total_price_tax_excl  =$Detail->product_price;
           if($polozka['tax_rate']) {
                $Detail->total_price_tax_incl   =$Detail->product_price *((100 + $polozka['tax_rate'])/100);
                $Detail->unit_price_tax_incl= $polozka['product_price'] *((100 + $polozka['tax_rate'])/100); 
           }
          else  {
              $Detail->total_price_tax_incl =$Detail->product_price;
              $Detail->unit_price_tax_incl= $polozka['product_price'];
          }
          $Detail->unit_price_tax_excl = $polozka['product_price'];
           $Detail->tax_rate=  $polozka['tax_rate'];
         $Detail->id_order_invoice =$order->invoice_number;
           $Detail->id_warehouse=1;
             $Detail->add();
         
        } 
         
     }
     
   
     if($order->invoice_number) {
            $Invoice=new OrderInvoice();
            $Invoice->id_order=$order->id;
            $Invoice->number= $order->invoice_number;
            $Invoice->delivery_date=$oldorder['delivery_date'];
            $Invoice->date_add=$oldorder['date_add'];
            $keys=array('total_discount_tax_excl', 'total_discount_tax_incl', 'total_paid_tax_excl', 'total_paid_tax_incl',
            'total_products', 'total_products_wt','total_shipping_tax_excl', 'total_shipping_tax_incl',
            'total_wrapping_tax_excl', 'total_wrapping_tax_incl', 'id_lang', 'id_shop');
            
           $Invoice->total_discount_tax_excl=$order->total_discounts_tax_excl;
           $Invoice->total_discount_tax_incl=$order->total_discount_tax_incl;    
           $Invoice->total_paid_tax_excl=$order->total_paid_tax_excl;
           $Invoice->total_paid_tax_incl=$order->total_paid_tax_incl;
           
            $Invoice->total_products=$order->total_products;
            $Invoice->total_products_wt=$order->total_products_wt;
            $Invoice->total_shipping_tax_excl=$order->total_shipping_tax_excl;
            $Invoice->total_shipping_tax_incl=$order->total_shipping_tax_incl;

            $Invoice->total_wrapping_tax_excl=$order->total_wrapping_tax_excl;
            $Invoice->total_wrapping_tax_incl=$order->total_wrapping_tax_incl;
            
             $myquery='INSERT INTO ptrial_prectene.ps_order_invoice SET id_order_invoice='.(int)$order->invoice_number;
             Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($myquery); 
             $Invoice->id=$order->invoice_number;
         //   $Invoice->id_lang=$order->id_lang;
        //    $Invoice->id_shop=$order->id_shop;   
           $Invoice->update();
           
           
           $myquery='INSERT INTO   ps_order_payment SET 
                     order_reference="'.$order->reference.'",
                     id_currency=1,     conversion_rate=1,
                     amount='.$order->total_paid .',
                         date_add="'.$oldorder['invoice_date'].'",
                     payment_method="'.$order->payment.'"';
          if(  Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($myquery)) {
              //  $lastid   = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue("LAST_INSERT_ID()");
                $lastid   = Db::getInstance()->Insert_ID(); 
                if($lastid) {
                       $myquery='INSERT INTO   ps_order_invoice_payment SET 
                     id_order_invoice='.$order->invoice_number.',
                     id_order_payment='.$lastid.',
                     id_order='.$order->id;
                      Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($myquery);
                }
          }  
           
     }
     
     $myquery='INSERT INTO ps_order_carrier SET    id_order='.  (int)$order->id.', 
                           id_carrier ='.(int)$order->id_carrier.',
                          shipping_cost_tax_excl ='.$order->total_shipping_tax_excl.',
                          shipping_cost_tax_incl ='.$order->total_shipping_tax_incl.', 
                              date_add="'.$oldorder['delivery_date'].'",
                           id_order_invoice='.(int)$order->invoice_number;
     
       Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($myquery);
   }
   
 
 
 function get_total_products($polozky, $tax=false) {
      $retval='';
       foreach($polozky as $polozka) {
           $retval +=$polozka['upravena_cena'];
           if($tax)
           $retval +=$polozka['marse'];  
       }
       if(empty($retval))
         return 1;
         
         
       return $retval;
     
 }
 
 function getCarrier($id_carrier) {
    $osobni=array(3,23,24,25,26,26,28,29,30,32,32,33,34,35,40,41,49);
    $ppl=array(4,8,10,16,19,46);
    $sk=array(42,43,44,45,50);
    $zdarma=array(11,12,13,15,18,20,21,22,37,48);
    
    if(in_array($id_carrier, $osobni))
      return 1;
      if(in_array($id_carrier, $ppl))
      return 13;
      if(in_array($id_carrier, $sk))
      return 14;
      if(in_array($id_carrier, $zdarma))
      return 19;
      
      return 11;
      
 
 } 
  
   

 