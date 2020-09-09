<?php

/**
  * Payment module class, Gopay.php
  
  * @category modules
  *
  * @author Gopay <integrace@gopay.cz>
  * @version 1.0
  *
  */

require_once(_PS_MODULE_DIR_.'gopay/gopay_helper.php');
require_once(_PS_MODULE_DIR_.'gopay/gopay_soap.php');

class Gopay extends PaymentModule
{
	private	$_html = '';
	private $_postErrors = array();

	public function __construct()
	{
		$this->name = 'gopay';
		$this->tab = 'Payment';
		$this->version = '1.0';
		
		$this->currencies = true;
		$this->currencies_mode = 'radio';

        parent::__construct();

		$this->page = basename(__FILE__, '.php');
        $this->displayName = $this->l('GoPay');
        $this->description = $this->l('Platí GoPay');
		$this->confirmUninstall = $this->l('Are you sure you want to delete your details?');
	}

	public function getGopayUrl()
	{
		return GopayHelper::baseIntegrationURL();				
	}

	public function install()
	{
		if (!parent::install()
			OR !Configuration::updateValue('GOID', '1234567890')
			OR !Configuration::updateValue('GOPAY_KEY', 'abcdefgh12345678abcdefgh')
			OR !Configuration::updateValue('GOPAY_HEADER', 'http://www.gopay.cz/images/logo.png')
			OR !Configuration::updateValue('GOPAY_CONFIG_STORE', 'eshop')
			OR !Configuration::updateValue('GOPAY_SUCCESS_URL', 'http://localhost/shop/modules/gopay/validation.php')
			OR !Configuration::updateValue('GOPAY_FAILED_URL', 'http://localhost/shop/modules/gopay/failed.php')
			OR !Configuration::updateValue('GOPAY_BASE_URL', 'https://www.gopay.cz/zaplatit-jednoducha-integrace')
			OR !Configuration::updateValue('GOPAY_WS_URL', 'https://www.gopay.cz/axis/EPaymentService?wsdl')
			OR !Configuration::updateValue('GOPAY_HISTORY_URL', 'http://localhost/shop/history.php')
			OR !$this->registerHook('payment')
			OR !$this->registerHook('paymentReturn'))
				
		return false;
	return true;
	}
		
	public function uninstall()
	{
		if (!Configuration::deleteByName('GOID')
			OR !Configuration::deleteByName('GOID')
			OR !Configuration::deleteByName('GOPAY_KEY')
			OR !Configuration::deleteByName('GOPAY_HEADER')
			OR !Configuration::deleteByName('GOPAY_CONFIG_STORE')
			OR !Configuration::deleteByName('GOPAY_SUCCESS_URL')
			OR !Configuration::deleteByName('GOPAY_FAILED_URL')
			OR !Configuration::deleteByName('GOPAY_BASE_URL')
			OR !Configuration::deleteByName('GOPAY_WS_URL')
			OR !Configuration::deleteByName('GOPAY_HISTORY_URL')
			
			OR !parent::uninstall())
			return false;
		return true;
	}

	public function getContent()
	{
		$this->_html = '<h2>Gopay</h2>';
		if (isset($_POST['submitGopay']))
		{
			if (empty($_POST['goId']))
				$this->_postErrors[] = $this->l('GoID is required.');
			elseif (!Validate::isInt($_POST['goId']))
				$this->_postErrors[] = $this->l('GoID must be an integer.');
			elseif (empty($_POST['gopayKey']))
				$this->_postErrors[] = $this->l('Secret is required.');
			elseif (empty($_POST['configStore']))
				$this->_postErrors[] = $this->l('Shop description is required.');
			elseif (empty($_POST['successUrl']))
				$this->_postErrors[] = $this->l('Success URL is required.');
			elseif (!Validate::isUrl($_POST['successUrl']))
				$this->_postErrors[] = $this->l('Incorrect format of URL.');
			elseif (empty($_POST['successUrl']))
				$this->_postErrors[] = $this->l('Failed URL is required.');
			elseif (!Validate::isUrl($_POST['failedUrl']))
				$this->_postErrors[] = $this->l('Incorrect format of URL.');
			elseif (empty($_POST['baseUrl']))
				$this->_postErrors[] = $this->l('Gopay gate URL is required.');
			elseif (!Validate::isUrl($_POST['baseUrl']))
				$this->_postErrors[] = $this->l('Incorrect format of URL.'); 
			elseif (empty($_POST['wsUrl']))
				$this->_postErrors[] = $this->l('Web service URL is required.');
			elseif (!Validate::isUrl($_POST['wsUrl']))
				$this->_postErrors[] = $this->l('Incorrect format of URL.');
			elseif (empty($_POST['historyUrl']))
				$this->_postErrors[] = $this->l('Order history URL is required.');
			elseif (!Validate::isUrl($_POST['historyUrl']))
				$this->_postErrors[] = $this->l('Incorrect format of URL.');
			
				
			if (!sizeof($this->_postErrors))
			{
				Configuration::updateValue('GOID', strval($_POST['goId']));
				Configuration::updateValue('GOPAY_KEY', strval($_POST['gopayKey']));
				Configuration::updateValue('GOPAY_HEADER', strval($_POST['header']));
				Configuration::updateValue('GOPAY_CONFIG_STORE', strval($_POST['configStore']));
				Configuration::updateValue('GOPAY_SUCCESS_URL', strval($_POST['successUrl']));
				Configuration::updateValue('GOPAY_FAILED_URL', strval($_POST['failedUrl']));
				Configuration::updateValue('GOPAY_BASE_URL', strval($_POST['baseUrl']));
				Configuration::updateValue('GOPAY_WS_URL', strval($_POST['wsUrl']));
				Configuration::updateValue('GOPAY_HISTORY_URL', strval($_POST['historyUrl']));
				$this->displayConf();
			}
			else
				$this->displayErrors();
		}

		$this->displayGopay();
		$this->displayFormSettings();
		return $this->_html;
	}

	public function displayConf()
	{
		$this->_html .= '
		<div class="conf confirm">
			<img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />
			'.$this->l('Settings updated').'
		</div>';
	}

	public function displayErrors()
	{
		$nbErrors = sizeof($this->_postErrors);
		$this->_html .= '
		<div class="alert error">
			<h3>'.($nbErrors > 1 ? $this->l('There are') : $this->l('There is')).' '.$nbErrors.' '.($nbErrors > 1 ? $this->l('errors') : $this->l('error')).'</h3>
			<ol>';
		foreach ($this->_postErrors AS $error)
			$this->_html .= '<li>'.$error.'</li>';
		$this->_html .= '
			</ol>
		</div>';
	}
	
	
	public function displayGopay()
	{
		$this->_html .= '
		<div style="float: right; width: 440px; height: 150px; border: dashed 1px #666; padding: 8px; margin-left: 12px;">
			<h2>'.$this->l('Create your Gopay account').'</h2>
			<div style="clear: both;"></div>
			<p style="text-align: center;"><a href="https://www.gopay.cz/nova-registrace"><img src="../modules/gopay/logo_gopay_190x90.png" alt="PrestaShop & Gopay" style="margin-top: 12px;" /></a></p>
			<div style="clear: right;"></div>
		</div>
		<img src="../modules/gopay/logo_gopay_190x90.png" style="float:left; margin-right:15px;" />
		<div style="clear:both;">&nbsp;</div>';
	}

	public function displayFormSettings()
	{
		$conf = Configuration::getMultiple(array('GOID', 'GOPAY_KEY', 'GOPAY_HEADER', 'GOPAY_CONFIG_STORE', 'GOPAY_SUCCESS_URL','GOPAY_FAILED_URL','GOPAY_BASE_URL','GOPAY_WS_URL','GOPAY_HISTORY_URL'));
		$goId = array_key_exists('goId', $_POST) ? $_POST['goId'] : (array_key_exists('GOID', $conf) ? $conf['GOID'] : '');
		$gopayKey = array_key_exists('gopayKey', $_POST) ? $_POST['gopayKey'] : (array_key_exists('GOPAY_KEY', $conf) ? $conf['GOPAY_KEY'] : '');
		$header = array_key_exists('header', $_POST) ? $_POST['header'] : (array_key_exists('GOPAY_HEADER', $conf) ? $conf['GOPAY_HEADER'] : '');
		$configStore = array_key_exists('configStore', $_POST) ? $_POST['configStore'] : (array_key_exists('GOPAY_CONFIG_STORE', $conf) ? $conf['GOPAY_CONFIG_STORE'] : '');
		$successUrl = array_key_exists('successUrl', $_POST) ? $_POST['successUrl'] : (array_key_exists('GOPAY_SUCCESS_URL', $conf) ? $conf['GOPAY_SUCCESS_URL'] : '');
		$failedUrl = array_key_exists('failedUrl', $_POST) ? $_POST['failedUrl'] : (array_key_exists('GOPAY_FAILED_URL', $conf) ? $conf['GOPAY_FAILED_URL'] : '');
		$baseUrl = array_key_exists('baseUrl', $_POST) ? $_POST['baseUrl'] : (array_key_exists('GOPAY_BASE_URL', $conf) ? $conf['GOPAY_BASE_URL'] : '');
		$wsUrl = array_key_exists('wsUrl', $_POST) ? $_POST['wsUrl'] : (array_key_exists('GOPAY_WS_URL', $conf) ? $conf['GOPAY_WS_URL'] : '');
		$historyUrl = array_key_exists('historyUrl', $_POST) ? $_POST['historyUrl'] : (array_key_exists('GOPAY_HISTORY_URL', $conf) ? $conf['GOPAY_HISTORY_URL'] : '');
			
		$this->_html .= '
		<form action="'.$_SERVER['REQUEST_URI'].'" method="post" style="clear: both;">
		<fieldset>
			<legend><img src="../img/admin/contact.gif" />'.$this->l('Settings').'</legend>
			<label>'.$this->l('GoID').'</label>
			<div class="margin-form"><input type="text" size="33" name="goId" value="'.htmlentities($goId, ENT_COMPAT, 'UTF-8').'" /></div>
			<label>'.$this->l('Gopay key').'</label>
			<div class="margin-form">
			<input type="text" size="33" name="gopayKey" value="'.htmlentities($gopayKey, ENT_COMPAT, 'UTF-8').'" /></div>
			<label>'.$this->l('Gopay header URL').'</label>
			<div class="margin-form"><input type="text" size="82" name="header" value="'.htmlentities($header, ENT_COMPAT, 'UTF-8').'" />
			<p class="hint clear" style="display: block; width: 501px;">'.$this->l('The image should be host on a securised server in order to avoid security warnings. Size should be limited at 750x90px.').'</p></div><br /><br /><br />
			<label>'.$this->l('Config store').'</label>
			<div class="margin-form"><input type="text" size="33" name="configStore" value="'.htmlentities($configStore, ENT_COMPAT, 'UTF-8').'" /></div>
			<label>'.$this->l('Success URL').'</label>
			<div class="margin-form"><input type="text" size="33" name="successUrl" value="'.htmlentities($successUrl, ENT_COMPAT, 'UTF-8').'" /></div>
			<label>'.$this->l('Failed URL').'</label>
			<div class="margin-form"><input type="text" size="33" name="failedUrl" value="'.htmlentities($failedUrl, ENT_COMPAT, 'UTF-8').'" /></div>
			<label>'.$this->l('BaseIntegration URL').'</label>
			<div class="margin-form"><input type="text" size="33" name="baseUrl" value="'.htmlentities($baseUrl, ENT_COMPAT, 'UTF-8').'" /></div>
			<label>'.$this->l('Webservice URL').'</label>
			<div class="margin-form"><input type="text" size="33" name="wsUrl" value="'.htmlentities($wsUrl, ENT_COMPAT, 'UTF-8').'" /></div>
			<label>'.$this->l('Order history URL').'</label>
			<div class="margin-form"><input type="text" size="33" name="historyUrl" value="'.htmlentities($historyUrl, ENT_COMPAT, 'UTF-8').'" /></div>
		
			
			<br /><center><input type="submit" name="submitGopay" value="'.$this->l('Save').'" class="button" /></center>
		</fieldset>
		</form><br /><br />
		<fieldset class="width3">
			<legend><img src="../img/admin/warning.gif" />'.$this->l('Information').'</legend>
			<a href="https://www.gopay.cz">více informací...</a>
		</fieldset>';
	}

	public function hookPayment($params)
	{
		global $smarty;

		$address = new Address(intval($params['cart']->id_address_invoice));
		$customer = new Customer(intval($params['cart']->id_customer));
		$goId = Configuration::get('GOID');
		$gopayKey = Configuration::get('GOPAY_KEY');
		$header = Configuration::get('GOPAY_HEADER');
		$failedUrl = Configuration::get('GOPAY_FAILED_URL');
		$successUrl = Configuration::get('GOPAY_SUCCESS_URL');
		$configStore = Configuration::get('GOPAY_CONFIG_STORE');
				
		//castka v centech
		$productsAmount = $params['cart']->getOrderTotal(true, 4);
		$shipping = $params['cart']->getOrderShippingCost();
		$amount1 = $productsAmount+$shipping;
		$amount = round($amount1,2)*100;
		
		//variabilni symbol a zaroven reference na kosik
		$reference = intval($params['cart']->id);
		
		$currency = $this->getCurrency();
					
		if (!Validate::isInt($goId))
			return $this->l('Incorrect format of GoID.');
		if (!Validate::isLoadedObject($address) OR !Validate::isLoadedObject($customer) OR !Validate::isLoadedObject($currency))
			return $this->l('Invalid customer.');
			
		$products = $params['cart']->getProducts();
		
		foreach ($products as $key => $product)
		{
			$products[$key]['name'] = str_replace('"', '\'', $product['name']);
			if (isset($product['attributes']))
				$products[$key]['attributes'] = str_replace('"', '\'', $product['attributes']);
			$products[$key]['name'] = htmlentities(utf8_decode($product['name']));
		}
		
		// sifrovani podpisu pomoci GopayHelperu
		$encryptedSignature=GopayHelper::encrypt(
			GopayHelper::hash(
				GopayHelper::concatPaymentCommand(
						$goId,
						$product['name'], 
						$amount,
						$reference,
						$failedUrl,
						$successUrl,
						$gopayKey
					)
			),
			$gopayKey);
			
			$smarty->assign(array(
	
			'gopayUrl' => $this->getGopayUrl(),
			'amount' => $amount,
			'goId' => $goId,
			'productName' => $product['name'],
			'reference' => $reference,
			'failedUrl' => $failedUrl,
			'successUrl' => $successUrl,
			'encryptedSignature' => $encryptedSignature
		));
									
		return $this->display(__FILE__, 'gopay.tpl');
	}
	
	public function hookPaymentReturn($params)
	{
		return $this->display(__FILE__, 'confirmation.tpl');
	}

	public function getL($key)
	{
		$translations = array(
			
			'cart' => $this->l('Cart not found'),
			'order' => $this->l('Order has already been placed'),
			'gopay_connect' => $this->l('Problem connecting to the Gopay server.'),
			'gopay_verified' => $this->l('The Gopay transaction could not be VERIFIED.'),	
		);
		return $translations[$key];
	}
}
