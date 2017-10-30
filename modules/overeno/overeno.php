<?php
/*
* 2007-2011 PrestaShop 
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_CAN_LOAD_FILES_'))
	exit;

class Overeno extends Module
{
	private $_html = '';
	private $_postErrors = array();


	public  $details;
	

	public function __construct()
	{
		$this->name = 'overeno';
		$this->tab = 'advertising_marketing';
		$this->version = '1.5.3';
		$this->author = 'PrestaHost.cz';
		
		$this->currencies = true;
		$this->currencies_mode = 'checkbox';

	//	$test=Configuration::get('HEUREKA_DEBUG');
		
		parent::__construct();
     
        
		$this->displayName = $this->l('Heuréka - Ověřeno zákazníky');
		$this->description = $this->l('Implementace Heuréka - Ověřeno zákazníky, používá HeurekaOvereno class');
		$this->confirmUninstall = $this->l('Are you sure you want to delete your details?');
		
	 
	}

	public function install()
	{
         if (!parent::install()
            OR !$this->registerHook('displayOrderConfirmation')
            OR !$this->registerHook('actionOrderStatusUpdate') )
            return false;  
            
        Configuration::updateValue('HEUREKA_ACTION', 'OrderConfirmation');     
            
         return true; 
    
      
    }

	public function uninstall()
	{
                 
                 
		if (!Configuration::deleteByName('HEUREKA_KEY')
                OR ! Configuration::deleteByName('HEUREKA_KEY_SK') 
                 OR ! Configuration::deleteByName('HEUREKA_DEBUG') 
                  OR ! Configuration::deleteByName('HEUREKA_ACTION') 
				OR !parent::uninstall())
			return false;
		return true;
	}

	private function _postValidation()
	{
		if (isset($_POST['btnSubmit']))
		{
			if (empty($_POST['HEUREKA_KEY']) && empty($_POST['HEUREKA_KEY_SK']))
				$this->_postErrors[] = $this->l('Vyplňte aspoň jeden klíč.');
		}
	}

	private function _postProcess()
	{
		if (isset($_POST['btnSubmit']))
		{
			Configuration::updateValue('HEUREKA_KEY',  trim(Tools::getValue('HEUREKA_KEY')));
            Configuration::updateValue('HEUREKA_KEY_SK', trim(Tools::getValue('HEUREKA_KEY_SK')));
         //   Configuration::updateValue('HEUREKA_ACTION', trim(Tools::getValue('HEUREKA_ACTION')));
            
            if(Tools::getValue('HEUREKA_DEBUG'))
               Configuration::updateValue('HEUREKA_DEBUG', 1);
            else {
              Configuration::updateValue('HEUREKA_DEBUG', 0);
              if(file_exists(_PS_MODULE_DIR_.'overeno/debug.txt'))
                unlink(_PS_MODULE_DIR_.'overeno/debug.txt');
            }
               
   
		}
		$this->_html .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('ok').'" /> '.$this->l('Settings updated').'</div>';
	}

	private function _displayOvereno()
	{
             require_once(_PS_MODULE_DIR_.$this->name.'/PrestahostUpgrade.php');
             return   PrestahostUpgrade::displayInfo($this->name, $this->version, Context::getContext()->language);
	}

	private function _displayForm()
	{       
		$this->_html .=
		'<form action="'.$_SERVER['REQUEST_URI'].'" method="post">'.$this->_displayOvereno().'
        
			<fieldset>
			<legend><img src="../img/admin/contact.gif" />'.$this->l('Contact details').'</legend>
				<table border="0" width="500" cellpadding="0" cellspacing="0" id="form">
								<tr>
						<td width="130" style="vertical-align: top;">'.$this->l('Heureka key').'</td>
						<td style="padding-bottom:15px;">
                        <table>
                        <tr><td>
                        Klíč CZ:
                        <textarea name="HEUREKA_KEY" rows="4" cols="53">'.htmlentities(Tools::getValue('HEUREKA_KEY', Configuration::get('HEUREKA_KEY')), ENT_COMPAT, 'UTF-8').'</textarea></td><td>
                         Klíč SK:
                        <textarea name="HEUREKA_KEY_SK" rows="4" cols="53">'.htmlentities(Tools::getValue('HEUREKA_KEY_SK',Configuration::get('HEUREKA_KEY_SK')), ENT_COMPAT, 'UTF-8').'</textarea></td></tr>
							
                            
							<p>'.$this->l('Pro nastavení služby dotazník spokojenosti je třeba použít unikátní klíč pro Váš obchod, pokud
vlastníte více obchodů, je nutné použít jiný klíč pro každý z nich. Unikátní klíč naleznete v administraci
obchodů (http://sluzby.heureka.cz/) pod názvem obchodu.').'</p>
						</td>
					</tr>
					';
  
   $this->_html .=  '<tr><td width="130" style="vertical-align: top;" align="center"><input  name="HEUREKA_DEBUG" 
   value="'.$this->l('Debuginng').'" type="checkbox" ';
   if(Configuration::get('HEUREKA_DEBUG'))
      $this->_html .=' checked="checked"';
      
    $this->_html .=' /></td><td>'.$this->l('Logovat komunikaci s heureka?').'</td><tr>';
    
     $this->_html .=  '<tr><td width="130" style="vertical-align: top;" align="center">';
    /* 
      $this->_html .=  '<input  name="HEUREKA_ACTION" value="OrderConfirmation" type="radio" ';
      if(Configuration::get('HEUREKA_ACTION') =="OrderConfirmation" )
      $this->_html .=' checked="checked"';
      $this->_html .=' />'.$this->l('Fire on order confirmation').' &nbsp;</td>';
      
        $this->_html .=  '<td><input  name="HEUREKA_ACTION" value="OrderState" type="radio" ';
      if(Configuration::get('HEUREKA_ACTION') =="OrderState" )
      $this->_html .=' checked="checked"';
      $this->_html .=' />'.$this->l('Fire on order state changed to Shipped ('._PS_OS_SHIPPING_.')');
      */
       $this->_html .=  '</td></tr>';

                  
                    
 $this->_html .=  '<tr><td colspan="2" align="center"><input class="button" name="btnSubmit" value="'.$this->l('Update settings').'" type="submit" /></td></tr>
				</table>
			</fieldset>
		</form>';
	}

	public function getContent()
	{  
		$this->_html = '<h2>'.$this->displayName.'</h2>';

		if (!empty($_POST))
		{
			$this->_postValidation();
			if (!sizeof($this->_postErrors))
				$this->_postProcess();
			else
				foreach ($this->_postErrors AS $err)
					$this->_html .= '<div class="alert error">'. $err .'</div>';
		}
		else
			$this->_html .= '<br />';

		
		$this->_displayForm();
         if(Configuration::get('HEUREKA_DEBUG') == 1) {
               $fp=fopen(_PS_MODULE_DIR_.'overeno/debug.txt', 'r');
               $s=fread($fp, 30000);
               $this->_html.= 'DEBUG:<hr />'.nl2br($s).'<hr />';;
         }
         
		return $this->_html;
	}

	

    public function hookDisplayOrderConfirmation($params)
    {
        if(Configuration::get('HEUREKA_ACTION') =="OrderState" ) {
        return false;
       }
        return $this->executeHook($params);
    }
   
      
   
   public function hookActionOrderStatusUpdate($params)  {
   
       
        if($params['newOrderStatus']->id !=_PS_OS_SHIPPING_)
         return;
       
       
        if(Configuration::get('HEUREKA_ACTION') =="OrderState" )   {
         return $this->executeHook($params);
        }
       
   }
   
	public function hookOrderConfirmation($params)
	{ 
    if(Configuration::get('HEUREKA_ACTION') =="OrderState" ) {
        return false;
    }
        return $this->executeHook($params);
		
	}
	
    protected function executeHook($params) {
         $sql='SELECT `id_country` 
        FROM `'._DB_PREFIX_.'country` WHERE iso_code="SK"';
    $num= Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    $id_sk=$num?$num:37;
    
     if(isset($params['objOrder'])) {  
        $Address=new Address($params['objOrder']->id_address_delivery);  
     }
     elseif(isset($params['cart'])) {
         $Address=new Address($params['cart']->id_address_delivery);   
     }
     else {
         $s=var_export($params);
         $path   =_PS_MODULE_DIR_.'overeno/debug.txt';
        $fp=fopen(_PS_MODULE_DIR_.'overeno/debug.txt', 'a+');
        fputs($fp, $s."\n\n");
        fclose($fp); 
     }
     
     if($Address->id_country   == $id_sk)
       $iso='Sk';
     else
       $iso='Cz';
       
      $path= _PS_MODULE_DIR_.$this->name.'/HeurekaOvereno'.$iso.'.php';
       require_once($path); 
       $classname='HeurekaOvereno'.$iso;
       $overeno = new $classname;
    
      if(isset($params['cart']->id_customer) && $params['cart']->id_customer  ) {
          $Customer=new Customer($params['cart']->id_customer);
           $email =$Customer->email;   
      }
      elseif(isset($params['objOrder']->id_customer)  && $params['objOrder']->id_customer) {
        $Customer=new Customer($params['objOrder']->id_customer);
        $email =$Customer->email;
       }    
      elseif(isset($params['cookie']->email) && !empty($params['cookie']->email)) {
        $email= $params['cookie']->email;
      }
                
        $overeno->setEmail($email);
        
       // $products = $params['cart']->getProducts(true); 
       
        $id_cart=$params['objOrder']->id_cart;
        $this->context->cart = new Cart($id_cart);   

        $products = $this->context->cart->getProducts();
        foreach($products as $product) {                
        $overeno->addProduct($product['name']);
        }
        $overeno->send(); 
        
    }
    
	
}
