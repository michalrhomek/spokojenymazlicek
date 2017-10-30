<?php
    require_once(dirname(__FILE__).'/csvexport.php');
    class ExpertFunctions extends CsvExport{
   
   protected $module;     
    public function __construct($module) {
        $this->module=$module;
       return; 
    }

    protected function displayForm(){
        
    $output ='<br /><br /><br /><fieldset><legend>'.$this->module->l('Database settings').'</legend>';
     
     $output .= $this->displayNewSiteDatabase();
     
     $output .= $this->displayOldSiteDatabase();
     
        $output .='
         <input type="submit"  class="button"  name="btnDbSettings" value="'.$this->module->l('Save database settings').'">'; 
         
         '<br /><br />'; 
       
        $output .='</fieldset>';
        $output .='<br /><br /><br /><fieldset><legend>'.$this->module->l('Expert functions').'</legend>';
    
       $output .= '<h4>'.$this->module->l('Run on the old site').'</h4> <input type="submit"  class="button"  name="btnMissing" value="'.$this->module->l('Export missing products').'">'; 

         '<br /><br />'; 
   $output .= '<h4>'.$this->module->l('Run on the new site').'</h4>';
   
   $output .= $this->displayProductImport();

   $output .= ' <br><h4>Orders import</h4>
   <input type="checkbox" name="CSV_AUTOINVOICE" value="1" />
   <input type="submit"  class="button"  name="btnOrders" value="'.$this->module->l('Import orders').'">'; 
       $output .='</fieldset>'; 
       
       return $output;
  
    }
    

protected function displayProductImport() {
 $output = '<h4>'.$this->module->l("Product import settings").'</h4>';
 $output .= $this->module->l("CSV_IMPORT_MATCH").'<input type="text"  name="CSV_IMPORT_MATCH" size="10" value="'.Configuration::get("CSV_IMPORT_MATCH" ).'"> <br />
 '.$this->module->l('Please enter a valid saved configuration for importing products. This must be created in Advanced Parameters /CSV import tab, step 2').'<br />';
 
  $sql='SELECT `match`, skip FROM `'._DB_PREFIX_.'import_match` WHERE name="'.pSQL(Configuration::get("CSV_IMPORT_MATCH" )).'"';
  $row=Db::getInstance()->getRow($sql);
  if(!$row['match'] || !strlen($row['match']) ){
      
    $output .= ' <input type="submit"  class="button"  name="btnMatchOnly" value="'.$this->module->l('Please save first').'">';  
    return $output;
  }

 $output .= $this->module->l("CSV_IMPORT_FILE").'<input type="text"  name="CSV_IMPORT_FILE" size="50" value="'.Configuration::get("CSV_IMPORT_FILE" ).'"> <br />
 '.$this->module->l('CSV products file').'<br />';
 
 $output .= $this->module->l("CSV_IMPORT_SKIP").'<input type="text"  name="CSV_IMPORT_SKIP" size="10" value="'.(int)$row['skip'].'"> <br />
 '.$this->module->l('Records to skip').'<br />';
  $output.=$this->module->l('When the import script runs, the CSV_IMORT_SKIP value is incremented,  do not  change it manually, except for setting the starting value.').'<br />'; 
 
 $pass=Configuration::get("CSV_IMPORT_PERPASS" );
 if(empty($pass))
  $pass=100;
 
 $output .= $this->module->l("CSV_IMPORT_PERPASS").'<input type="text"  name="CSV_IMPORT_PERPASS" size="10" value="'.$pass.'"> <br />
 '.$this->module->l('Products per pass').'<br />';
 
 $iso=Configuration::get("CSV_IMPORT_LANG" );
 if(empty($iso))
  $iso=Context::getContext()->language->iso_code;
 
 $output .= $this->module->l("CSV_IMPORT_LANG").'<input type="text"  name="CSV_IMPORT_LANG" size="10" value="'.$iso.'"> <br />
 '.$this->module->l('Language ISO').'<br />';
 

 $output .= ' <input type="submit"  class="button"  name="btnImportProducts" value="'.$this->module->l('Save settings').'"><br />'; 
 
 $output.=$this->module->l('Test the settings by running manually the following import script, then set-up a cron job:<br />');  
  
 $url= _PS_BASE_URL_.__PS_BASE_URI__.'modules/'.$this->module->name.'/productimport.php?hash='.substr(_COOKIE_KEY_,3,7);
 $output.='<b><a href="'.$url.'"/>'.$url.'</a></b>';
    
 return $output;   
}

protected function saveImportSettings() {
    $this->saveImportMatch();
    $vals=array('CSV_IMPORT_FILE', 'CSV_IMPORT_PERPASS','CSV_IMPORT_LANG');
    foreach($vals as $val) {
         Configuration::updateValue($val, Tools::getValue($val)); 
    }
$sql='UPDATE `'._DB_PREFIX_.'import_match` SET skip ='.Tools::getValue('CSV_IMPORT_SKIP').' WHERE name="'.pSQL(Tools::getValue('CSV_IMPORT_MATCH')).'"';
Db::getInstance()->execute($sql); 
}

protected function saveImportMatch() {
   Configuration::updateValue('CSV_IMPORT_MATCH', Tools::getValue('CSV_IMPORT_MATCH')); 
   Configuration::updateValue('CSV_IMPORT_ADMINDIR', getcwd()); 
}

   
    
 protected function displayOldSiteDatabase() {
 $output ='<h4>'.$this->module->l("Old site database").'</h4>'.$this->module->l("CSV_OLDSITE_SERVER").'<input type="text"  name="CSV_OLDSITE_SERVER" size="20" value="'.Configuration::get("CSV_OLDSITE_SERVER" ).'"> <br />
            '.$this->module->l("CSV_OLDSITE_DB").'<input type="text"  name="CSV_OLDSITE_DB" size="20" value="'.Configuration::get("CSV_OLDSITE_DB" ).'">  <br />
            '.$this->module->l("CSV_OLDSITE_USER").'<input type="text"  name="CSV_OLDSITE_USER" size="20" value="'.Configuration::get("CSV_OLDSITE_USER" ).'">     <br />
            '.$this->module->l("CSV_OLDSITE_PASSWD").'<input type="password"  name="CSV_OLDSITE_PASSWD" size="20" value="'.Configuration::get("CSV_OLDSITE_PASSWD" ).'">   <br />
             '.$this->module->l("CSV_OLDSITE_PREFIX").'<input type="text"  name="CSV_OLDSITE_PREFIX" size="20" value="'.Configuration::get("CSV_OLDSITE_PREFIX" ).'">   <br />
         <br />';       
 return $output;
 }
   
  protected function displayNewSiteDatabase() {
      $output ='<h4>'.
    $this->module->l("New site database").'</h4><br />'.$this->module->l("CSV_NEWSITE_SERVER").'<input type="text"  name="CSV_NEWSITE_SERVER" size="20" value="'.Configuration::get("CSV_NEWSITE_SERVER" ).'"> <br />
            '.$this->module->l("CSV_NEWSITE_DB").'<input type="text"  name="CSV_NEWSITE_DB" size="20" value="'.Configuration::get("CSV_NEWSITE_DB" ).'">  <br />
            '.$this->module->l("CSV_NEWSITE_USER").'<input type="text"  name="CSV_NEWSITE_USER" size="20" value="'.Configuration::get("CSV_NEWSITE_USER" ).'">     <br />
            '.$this->module->l("CSV_NEWSITE_PASSWD").'<input type="password"  name="CSV_NEWSITE_PASSWD" size="20" value="'.Configuration::get("CSV_NEWSITE_PASSWD" ).'">   <br />
             '.$this->module->l("CSV_NEWSITE_PREFIX").'<input type="text"  name="CSV_NEWSITE_PREFIX" size="20" value="'.Configuration::get("CSV_NEWSITE_PREFIX" ).'">   <br />
         <br />'; 
  return $output;
  }
  
  
    
        protected function getRemoteLink() {
        
       if(! $link= mysql_connect(Configuration::get('CSV_NEWSITE_SERVER'),Configuration::get('CSV_NEWSITE_USER'),Configuration::get('CSV_NEWSITE_PASSWD')))
        return false;
        $db=Configuration::get('CSV_NEWSITE_DB');
       if(!mysql_select_db(Configuration::get('CSV_NEWSITE_DB')) )
        return false;
        $query="SET NAMES utf8";    
        $ret=    mysql_query($query, $link);

        return $link;
        
    }
    
     protected  function getOldLink() {
       
       if(! $link= mysql_connect(Configuration::get('CSV_OLDSITE_SERVER'),Configuration::get('CSV_OLDSITE_USER'),Configuration::get('CSV_OLDSITE_PASSWD')))
        return false;
        $db=Configuration::get('CSV_OLDSITE_DB');
       if(!mysql_select_db(Configuration::get('CSV_OLDSITE_DB')) )
        return false;
        $query="SET NAMES utf8";    
        $ret=    mysql_query($query, $link);

        return $link;
        
    }
 
     protected function getRemoteIds() {
          $link=$this->getRemoteLink();
          $sql = 'SELECT id_product FROM '.Configuration::get("CSV_NEWSITE_PREFIX").'product WHERE 1';
          $products=$this->fetch_assoc($sql, $link);
          return $products;
    }
  private function fetch_assoc($myquery, $link=null){
       if(is_null($link))
         $link=$this->getOldLink();
        $retval=array();
        $result = mysql_query($myquery, $link);
        while($row= mysql_fetch_assoc($result))
          $retval[]=$row;
        return $retval;
}    
   public function importOrders($link) {
  /*     TRUNCATE table ps_cart;
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
       
       $auotinvoice=(int)Tools::getValue('CSV_AUTOINVOICE');
       $lastinvoice=1;
       $sql='SELECT * FROM '.Configuration::get("CSV_OLDSITE_PREFIX").'orders WHERE 1';
       
      //  $sql='SELECT * FROM '.Configuration::get("CSV_OLDSITE_PREFIX").'orders LIMIT 580, 3';
       $oldorders=$this->fetch_assoc($sql, $link);
       
        foreach($oldorders as $oldorder) {
      $sql='INSERT INTO '._DB_PREFIX_.'orders SET id_order='.(int)$oldorder['id_order'];
     Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql); 
     
   $sql='SELECT * FROM '.Configuration::get("CSV_OLDSITE_PREFIX").'order_detail WHERE id_order='.(int)$oldorder['id_order'];
   $details=$this->fetch_assoc($sql, $link);
     
    
    $order=new Order();   
    $order->id_shop_group=1;  
    $order->id_shop=1; 
    $order->id_lang=Configuration::get("PS_LANG_DEFAULT");
   
    $order->id_customer=(int)$oldorder['id_customer']; 
    $id_address_delivery=(int)$oldorder['id_address_delivery'];
    $id_address_invoice=(int)$oldorder['id_address_invoice']; 
    
    
    // adreasam se nezavhovala ID
    $sql='SELECT id_address FROM '._DB_PREFIX_.'address WHERE id_customer ='.(int) $oldorder['id_customer']. ' AND
    id_address <='.(int)$id_address_delivery;
    
    $id_address_delivery=Db::getInstance()->getValue($sql);
    
    $sql='SELECT id_address FROM  '._DB_PREFIX_.'address WHERE id_customer ='.(int) $oldorder['id_customer']. ' AND
    id_address <='.(int)$id_address_invoice;
    
     $id_address_invoice=Db::getInstance()->getValue($sql);
    
    $order->id_address_delivery=(int)$id_address_delivery;
    $order->id_address_invoice=(int)$id_address_invoice; 
    

    $order->id_currency=Configuration::get("PS_CURRENCY_DEFAULT");
    $order->conversion_rate =1;
    
     
    $order->id_carrier =$this->getCarrier($oldorder['id_carrier']);
    
    $sql='SELECT  id_order_state  FROM  '.Configuration::get("CSV_OLDSITE_PREFIX").'order_history WHERE id_order='.(int)$oldorder['id_order'].'
    ORDER BY date_add DESC LIMIT 1';
    $ret=$this->fetch_assoc($sql, $link);
    $order->current_state=$ret[0]['id_order_state'];
     $order->invoice_date=$oldorder['invoice_date'];
     $order->delivery_date=$oldorder['delivery_date'];
     
      $order->payment=$oldorder['payment'];
     
      if(Validate::isModuleName($oldorder['module']))
       $order->module=$oldorder['module'];
      else
        $order->module='bankwire';
      
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
     
       $sql='INSERT INTO ps_cart SET  id_cart='.$order->id_cart.',
     id_shop =1, id_shop_group=1, id_currency='.$order->id_currency.',
     id_customer='.$order->id_customer.', date_add="'.$order->date_add.'", date_upd="'.$order->date_add.'"';
     Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql); 
     
    
     $customer = new Customer($order->id_customer);
     $order->secure_key=$customer->secure_key; 
     
     $order->id=$oldorder['id_order'];
     $order->reference=$oldorder['id_order'];
     if($order->update()) {
         $sql='SELECT * FROM  '.Configuration::get("CSV_OLDSITE_PREFIX").'order_detail WHERE id_order= '.(int)$oldorder['id_order'];
         $polozky=$this->fetch_assoc($sql);
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
            if($auotinvoice) {
                $Invoice->number=$lastinvoice++; 
            }
            else {
            $Invoice->number= $order->invoice_number;
            }
            $Invoice->delivery_date=$oldorder['delivery_date'];
            $Invoice->date_add=$oldorder['date_add'];
            $keys=array('total_discount_tax_excl', 'total_discount_tax_incl', 'total_paid_tax_excl', 'total_paid_tax_incl',
            'total_products', 'total_products_wt','total_shipping_tax_excl', 'total_shipping_tax_incl',
            'total_wrapping_tax_excl', 'total_wrapping_tax_incl', 'id_lang', 'id_shop');
           
           if(isset($order->total_discounts_tax_excl))
                $Invoice->total_discount_tax_excl=$order->total_discounts_tax_excl;
           
           if(isset($order->total_discount_tax_incl))
                $Invoice->total_discount_tax_incl=$order->total_discount_tax_incl;  
           

             
           $Invoice->total_paid_tax_excl=$order->total_paid_tax_excl;
           $Invoice->total_paid_tax_incl=$order->total_paid_tax_incl;
           
            $Invoice->total_products=$order->total_products;
            $Invoice->total_products_wt=$order->total_products_wt;
            $Invoice->total_shipping_tax_excl=$order->total_shipping_tax_excl;
            $Invoice->total_shipping_tax_incl=$order->total_shipping_tax_incl;

            $Invoice->total_wrapping_tax_excl=$order->total_wrapping_tax_excl;
            $Invoice->total_wrapping_tax_incl=$order->total_wrapping_tax_incl;
            
             $myquery='INSERT INTO '._DB_PREFIX_.'order_invoice SET id_order_invoice='.(int)$order->invoice_number;
             Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($myquery); 
             $Invoice->id=$order->invoice_number;
         //   $Invoice->id_lang=$order->id_lang;
        //    $Invoice->id_shop=$order->id_shop;   
           $Invoice->update();
           
           
           $myquery='INSERT INTO   '._DB_PREFIX_.'order_payment SET 
                     order_reference="'.$order->reference.'",
                     id_currency=1,     conversion_rate=1,
                     amount='.$order->total_paid .',
                         date_add="'.$oldorder['invoice_date'].'",
                     payment_method="'.$order->payment.'"';
          if(  Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($myquery)) {
              //  $lastid   = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue("LAST_INSERT_ID()");
                $lastid   = Db::getInstance()->Insert_ID(); 
                if($lastid) {
                       $myquery='INSERT INTO   '._DB_PREFIX_.'order_invoice_payment SET 
                     id_order_invoice='.$order->invoice_number.',
                     id_order_payment='.$lastid.',
                     id_order='.$order->id;
                      Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($myquery);
                }
          }  
           
     }
     
     $myquery='INSERT INTO '._DB_PREFIX_.'order_carrier SET    id_order='.  (int)$order->id.', 
                           id_carrier ='.(int)$order->id_carrier.',
                          shipping_cost_tax_excl ='.$order->total_shipping_tax_excl.',
                          shipping_cost_tax_incl ='.$order->total_shipping_tax_incl.', 
                              date_add="'.$oldorder['delivery_date'].'",
                           id_order_invoice='.(int)$order->invoice_number;
     
       Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($myquery);
   }
       
   }
 
 // vstup cislo puvodniho dopravce
 private function getCarrier($id_carrier) {
    $osobni=array(2);
    $balik=array(1,12,14,15,16,21,23,24,25,26,27,28,29,31,33,34,36);
    $ppl=array(3);
    
    $sk=array(42,43,44,45,50);
    $krabice=array(42,45);
     $obalka=array(41,43,44);
    
    if(in_array($id_carrier, $osobni))
      return 16;
      if(in_array($id_carrier, $balik))
      return 2;
      if(in_array($id_carrier, $ppl))
      return 3;
        if(in_array($id_carrier, $krabice))
      return 14;
      
      if(in_array($id_carrier, $obalka))
      return 17;
     
      
      return 2;
      
 
 }
    
 
    
    
    }      
?>
