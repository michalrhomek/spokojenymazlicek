<?php
/** ########################################################################### * 
 *                                                                             * 
 *                      Presta Module Shop | Copyright 2018                    * 
 *                           www.prestamoduleshop.com                          * 
 *                                                                             * 
 *             Please do not change this text, remove the link,                * 
 *          or remove all or any part of the creator copyright notice          * 
 *                                                                             * 
 *    Please also note that although you are allowed to make modifications     * 
 *     for your own personal use, you may not distribute the original or       * 
 *                 the modified code without permission.                       * 
 *                                                                             * 
 *                    SELLING AND REDISTRIBUTION IS FORBIDDEN!                 * 
 *             Download is allowed only from www.prestamoduleshop.com          * 
 *                                                                             * 
 *       This software is provided as is, without warranty of any kind.        * 
 *           The author shall not be liable for damages of any kind.           * 
 *               Use of this software indicates that you agree.                * 
 *                                                                             * 
 *                                    ***                                      * 
 *                                                                             * 
 *              Prosím, neměňte tento text, nemažte odkazy,                    * 
 *      neodstraňujte části a nebo celé oznámení těchto autorských práv        * 
 *                                                                             * 
 *     Prosím vezměte také na vědomí, že i když máte možnost provádět změny    * 
 *        pro vlastní osobní potřebu, nesmíte distribuovat původní nebo        * 
 *                        upravený kód bez povolení.                           * 
 *                                                                             * 
 *                   PRODEJ A DISTRIBUCE JE ZAKÁZÁNA!                          * 
 *          Stažení je povoleno pouze z www.prestamoduleshop.com               * 
 *                                                                             * 
 *   Tento software je poskytován tak, jak je, bez záruky jakéhokoli druhu.    * 
 *          Autor nenese odpovědnost za škody jakéhokoliv druhu.               * 
 *                  Používáním tohoto softwaru znamená,                        * 
 *           že souhlasíte s výše uvedenými autorskými právy.                  * 
 *                                                                             * 
 * ########################################################################### **/
use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

if (!defined('_PS_VERSION_')) {
	exit;
}

include_once(dirname(__FILE__).'/classes/moduleCore.php');

class Pms_GoPay_Extra extends Pms_GoPay_Extra_Core
{
	public static $moduleDir;
	public static $gopayDefaultLang = 'CS';
	public static $gopayDefaultCurrency = 'CZK';
	public static $gopayLanguages = array('CS', 'EN', 'SK', 'DE', 'RU', 'PL', 'HU', 'FR', 'RO');
	public static $gopayCurrencies = array('CZK', 'EUR', 'PLN', 'HUF', 'GBP', 'USD', 'RON', 'HRK', 'BGN');

	public static $hooks = array(
		'paymentOptions',	/* ver >= 1.7 */
		'displayHeader',
		'displayFooter',
		'displayPayment',
		'displayPaymentEU',
		'displayPDFInvoice',
		'displayAdminOrder',
		'displayLeftColumn',
		'displayRihgtColumn',
		'displayPaymentReturn',
		'displayOverrideTemplate',
		'actionAdminControllerSetMedia',
		'actionGetExtraMailTemplateVars'
	);

	public static $mainMenuTabs = array(
		'modulesetting' => '_ModuleSetting',
		'gatewaysetting' => '_GatewaySetting',
		'paymentssettings' => '_PaymentsSettings',
		'ordersetting' => '_OrderSetting',
		'eetsetting' => '_EetSetting',
		
	);
	
	public static $basePayments = array(
		'PAYMENT_CARD' => 'Platební karty',
		'BANK_ACCOUNT' => 'Bankovní převody',
		'ACCOUNT' => 'Platbu vyberu na platební bráně'
	);

	public static $newStatuses = array(
		array(
			'name' => '_NEW',
			'color' => '#029AD6',
			'mail' => false,
			'lang_cs' => 'Gopay – objednávka vytvořena',
			'lang_en' => 'GoPay - order created'
		),
		array(
			'name' => '_CREATED',
			'color' => '#4E3D99',
			'mail' => true,
			'lang_cs' => 'Gopay – opakovaná platba založena',
			'lang_en' => 'GoPay - recurring payment created'
		),
		array(
			'name' => '_TIMEOUTED',
			'color' => '#D61101',
			'mail' => true,
			'lang_cs' => 'Gopay – časový limit vypršel',
			'lang_en' => 'GoPay - timeout'
		),
		array(
			'name' => '_PAYMENT_METHOD_CHOSEN',
			'color' => '#26A942',
			'mail' => true,
			'lang_cs' => 'Gopay – platba byla vybrána',
			'lang_en' => 'GoPay - payment chosen'
		),
		array(
			'name' => '_CANCELED',
			'color' => '#C8073C',
			'mail' => true,
			'lang_cs' => 'Gopay – zrušeno uživatelem',
			'lang_en' => 'GoPay - canceled'
		),
		array('name' => '_AUTHORIZED',
			'color' => '#CB6803',
			'mail' => true,
			'lang_cs' => 'Gopay – autorizováno',
			'lang_en' => 'GoPay - authorized'
		),
		array('name' => '_REFUNDED',
			'color' => '#766861',
			'mail' => true,
			'lang_cs' => 'Gopay – vrácená platba',
			'lang_en' => 'GoPay - refunded'
		),
		array('name' => '_PARTIALLY_REFUNDED',
			'color' => '#766861',
			'mail' => true,
			'lang_cs' => 'Gopay – částečně vrácená platba',
			'lang_en' => 'GoPay - partially refunded'
		)
	);

	/*  vytvoření nových tabs pro administrační menu */
	public static $adminTabs = array(
		array(
			'class_name' => 'Account_',	// za název se automaticky přidává název classu
			'parent' => 'AdminParentCustomer',
			'name' => array(
				'en' => 'Payment Listing',	// 'en'  musí být vždy ! 
				'cs' => 'Výpis plateb', 
				'sk' => 'Výpis platieb'
			)
		)
	);

	public function __construct()
	{	
		$this->name = 'pms_gopay_extra';
		$this->tab = 'payments_gateways';
		$this->version = '17.80622';
		$this->TEMPLATE_PMS = '2.0.0';
		$this->author = 'PrestaModuleShop';
		$this->authormail = 'info@prestamoduleshop.com';
		$this->need_instance = 1;
		$this->is_eu_compatible = 1;
		$this->bootstrap = true;
		$this->ps_versions_compliancy = array('min' => '1.5', 'max' => _PS_VERSION_);

		parent::__construct();

		$this->currencies = true;
		$this->currencies_mode = 'checkbox';

		$this->displayName = $this->l('PMS GoPay Extra');
			  
		/* tečku nemazat !!!  */
		$this->description .= $this->l('Online payments with GoPay services.');
		$this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

		if(Module::isInstalled($this->name))
		{
			include_once(dirname(__FILE__).'/classes/PAYMENTButtons.php');

			include_once(dirname(__FILE__).'/restapi/callback.php');
			include_once(dirname(__FILE__).'/restapi/gopay_restapi.php');

			include_once(dirname(__FILE__).'/classes/GoPayBills.php');
			include_once(dirname(__FILE__).'/classes/GoPayOrders.php');
			include_once(dirname(__FILE__).'/classes/GoPayRefund.php');
			include_once(dirname(__FILE__).'/classes/GoPayRecurrent.php');
			include_once(dirname(__FILE__).'/classes/GoPayRefundProducts.php');
		}
		
		$this->translations();
		self::$moduleDir = $this->module_dir;
	}

	public function install()
	{
		$override_dir = dirname(__FILE__).'/override/classes';
		if ($this->isPatched("classes/Mail.php", "/MK##PMS_Mail/") || $this->isPatched("classes/Mail.php", "/##Mail/"))
		{
			if (file_exists($override_dir.'/Mail.php'))
				rename($override_dir.'/Mail.php', $override_dir.'/.Mail.php');
		} else
			if (file_exists($override_dir.'/.Mail.php'))
				rename($override_dir.'/.Mail.php', $override_dir.'/Mail.php');

		/* set other install settings only for this module */
		if (!$this->createOrderStatuses(self::$newStatuses)
			|| !$this->installModuleTab(self::$adminTabs)	/* povolit dle potřeby */
			|| !parent::install()
			|| !$this->registerHooks(self::$hooks)
		)
			return false;

		return true;
	}

	public function uninstall()
	{
		$override_dir = dirname(__FILE__).'/override/classes';
		if ((!Module::isInstalled('pms_email_attachments') && $this->isPatched("classes/Mail.php", "/MK##PMS_Mail/"))
		   || (!Module::isInstalled('add_email_attachments') && $this->isPatched("classes/Mail.php", "/##Mail/"))
		   || (!Module::isInstalled('add_gopay_new') && $this->isPatched("classes/Mail.php", "/##Mail/")))
		{
			if (file_exists($override_dir.'/.Mail.php'))
				rename($override_dir.'/.Mail.php', $override_dir.'/Mail.php');
		} else
			if (file_exists($override_dir.'/Mail.php'))
				rename($override_dir.'/Mail.php', $override_dir.'/.Mail.php');

		/* set other uninstall settings only for this module */
		if ((boolean)Configuration::get($this->MFIX.'_FORCE_UNINSTALL')
				&& (!$this->deleteStatuses(self::$newStatuses)
					|| !$this->deleteMails(self::$newStatuses)
			)
			|| !$this->uninstallSettings(self::$mainMenuTabs)
			|| !$this->uninstallModuleTab(self::$adminTabs)
			|| !parent::uninstall()
		)
			return false;

		return true;
	}

	public function hookActionAdminControllerSetMedia()
	{
		parent::hookActionAdminControllerSetMedia();
		if (Tools::getValue('controller') == 'AdminOrders' && Tools::isSubmit('vieworder'))
		{
			$this->context->controller->addJS($this->_path.'views/js/admin/order_detail/js.js');
			$this->context->controller->addCSS($this->_path.'views/css/admin/order_detail/css.css');
		}
	}

	public function getMainMenuTabs()
	{
		if(Module::isInstalled($this->name))
		{
			if(Tools::getValue('configure') == $this->name || Tools::getValue('module_name') == $this->name)
			{
				foreach(self::$mainMenuTabs as $name => $class)
				{
					$class = self::_CLASS_NAME.$class;
					if(!is_object($class))
					{
						include_once($this->_path.'/classes/tabs/'.$name.'.php');
						self::$mainMenuTabs[$name] = new $class($this);
					}
				}
			}
		}
	}

	public function getContent($adminTab = NULL)
	{
		$this->getMainMenuTabs();
		$tabs = self::$mainMenuTabs;

		foreach ($tabs as $tab)
			$this->_html .= $tab->postProces();

		$this->context->smarty->assign(array(
			'tabs'		 => $tabs,
			'adminTab'	 => $adminTab,
		));

		parent::getContent($adminTab);
		return $this->_html.$this->display($this->name, '/views/templates/admin/admin_main.tpl');
	}

	public function hookDisplayHeader($params)
	{
		/* lze definovat globalní JS proměnné potřebné pro front scripty modulu  */
		if (Tools::getValue('id_module') == $this->id
			|| in_array(Tools::getValue('controller'), array('order', 'orderopc', 'order-opc', 'history', 'orderdetail'))
			|| (Tools::getValue('fc') == 'module' && Tools::getValue('module') == $this->name)
		)
		{
			$this->context->controller->addjqueryPlugin('fancybox');

			if (version_compare(_PS_VERSION_, '1.6', '<') === true) 
				$this->context->controller->addCSS($this->module_dir.'views/css/front/css_15.css');

			if (version_compare(_PS_VERSION_, '1.7', '<') === true)
			{
				$this->context->controller->addJS($this->module_dir.'/views/js/front/js.js');
				$this->context->controller->addCSS($this->module_dir.'/views/css/front/css.css');
			} else
			{
				$this->context->controller->registerStylesheet('modules-'.$this->name, 'modules/'.$this->name.'/views/css/front/css.css', array('media' => 'all', 'priority' => 150));
				$this->context->controller->registerJavascript('modules-'.$this->name, 'modules/'.$this->name.'/views/js/front/js.js', array('position' => 'bottom', 'priority' => 150));
			}

			$this->_prepVariables();

			return $this->display(__FILE__, '/views/templates/front/header_infos.tpl');
		}
	}

	public function hookActionObjectOrderAddBefore($params)
	{
		return $this->functions->hookActionObjectOrderAddBefore($params);
	}
	
	/* ------------------------------------------------------------- */
	/*  HOOKS
	/* ------------------------------------------------------------- */

	public function hookDisplayLeftColumn($params)
	{
		if (Configuration::get($this->MFIX.'_HOOK') == 'left')
			return $this->functions->displayPaymentLogo();

		return false;
	}

	public function hookDisplayRightColumn($params)
	{
		if (Configuration::get($this->MFIX.'_HOOK') == 'right')
			return $this->functions->displayPaymentLogo();

		return false;
	}

	public function hookDisplayFooter($params)
	{
		if (Configuration::get($this->MFIX.'_HOOK') == 'footer')
			return $this->functions->displayPaymentLogo();

		return false;
	}

	public function hookDisplayPDFInvoice($params)
	{
		if (isset($params['object']->id_order)
			&& Configuration::get($this->MFIX.'_BILL_PDF')
			&& $bill = Pms_GoPay_Extra_Bills::getBill($params['object']->id_order))
		{
			$detail = Pms_GoPay_Extra_RestAPI::getBillDetail($bill['id_session']);
			if (isset($detail->errors))
				return;

			$date = new DateTime($detail[0]->dat_trzby);

			$tab = '
<table width="100%">
	<tr><td colspan="2"> </td></tr>
	<tr>
		<td width="50%" style="font-size:7pt !important;"><b>'.$this->l('fik:').'</b> '.$detail[0]->fik.'</td>
		<td width="50%" style="font-size:7pt !important;"><b>'.$this->l('bkp:').'</b> '.$detail[0]->bkp.'</td>
	</tr>
	<tr>
		<td style="font-size:7pt !important;">
			<b>'.$this->l('Date and time of issue receipts:').'</b> '.$date->format('Y-m-d H:i:s').'
		</td>
		<td style="font-size:7pt !important;">
			<b>'.$this->l('Business location:').'</b> '.$detail[0]->id_provoz.'
			<b>'.$this->l('Cash register:').'</b> '.$detail[0]->id_pokl.'
		</td>
	</tr>
	<tr><td colspan="2" style="font-size:8pt !important;">'.Configuration::get($this->MFIX.'_EET_MSG').'</td></tr>
</table>';
			return $tab;
		}
	}

	public function hookActionGetExtraMailTemplateVars($params)
	{
		if (!isset($params['template_vars']['{id_order}']))
			return;

		$id_order = $params['template_vars']['{id_order}'];

		$params['extra_template_vars']['{recurrence_period}'] = '';
		$params['extra_template_vars']['{recurrence_date_to}'] = '';
		$params['extra_template_vars']['{repeat_payment_url}'] = Context::getContext()->link->getModuleLink($this->name, 'repeatPayment', array('paymentChannel' => 'ACCOUNT', 'orderId' => $id_order, 'repeatInMail' => 1), true);

		if ($recurrence = Pms_GoPay_Extra_Recurrent::getReccurencePeriod($id_order))
		{
			$params['extra_template_vars']['{recurrence_period}'] = $this->mailRepeated_1.$recurrence[0]['recurrence_period'].' '.$recurrence[0]['recurrence_cycle'];
			$params['extra_template_vars']['{recurrence_date_to}'] = $this->mailRepeated_2.$recurrence[0]['recurrence_date_to'];
		}

		return $params;
	}

	public function hookPaymentOptions($params)
	{
		if (!$this->active
			|| !$this->checkCurrency($params['cart'])
			|| !$this->isAllowedCarrier($this->getCarrierId($params))
		)
			return;

		return $this->functions->hookPaymentOptions($params);
	}

	public function hookDisplayPayment($params)
	{	
		if (!$this->active
			|| !$this->checkCurrency($params['cart'])
			|| !$this->isAllowedCarrier($this->getCarrierId($params))
		)
			return;

		return $this->functions->hookDisplayPayment($params);
	}

	public function hookDisplayPaymentEU($params)
	{
		if (!$this->active
			|| !$this->checkCurrency($params['cart'])
			|| !$this->isAllowedCarrier($this->getCarrierId($params))
		)
			return array();

		return $this->functions->hookDisplayPaymentEU($params);
	}

	public function hookDisplayOverrideTemplate($params)
	{
		$tpl = '';
		if (version_compare(_PS_VERSION_, '1.7', '>=') === true)
		{
			if (isset($params['template_file']))
				$tpl = $this->name.'/views/templates/front/'.$params['template_file'].'.tpl';
		} else
		{
			if (isset($params['controller']->php_self))
				$tpl = $this->name.'/views/templates/front/'.$params['controller']->php_self.'.tpl';
		}

		if (Tools::file_exists_cache(_PS_THEME_DIR_.'modules/'.$tpl))
			return _PS_THEME_DIR_.'modules/'.$tpl;
		elseif (Tools::file_exists_cache(_PS_MODULE_DIR_.$tpl))
			return _PS_MODULE_DIR_.$tpl;
	}

	public function hookDisplayPaymentReturn($params)
	{
		if (!$this->active)
			return;

		$Callback	 = new Pms_GoPay_Extra_Callback();
		if (version_compare(_PS_VERSION_, '1.7', '>=') === true)
			$order	 = $params['order'];
		else
			$order	 = $params['objOrder'];

		$cart		 = new Cart($order->id_cart);
		$currency	 = new Currency($order->id_currency);
		$state		 = $order->getCurrentState();

		$all = Configuration::get($this->MFIX.'_BUTTONS_MODE') == 1 ? false : true;
		$_PAYMENTS	 = $this->functions->getPaymentInstruments($cart, $all);

		$this->smarty->assign(array(
				'status'		 => 'ok',
				'shop_name'		 => $this->context->shop->name,
				'temlate_folder' => _PS_MODULE_DIR_.$this->name.'/views/templates',
				'paymentStatus'	 => $Callback->updateForCallback(Tools::getValue('id'), Tools::getValue('id_order')),
				'typeG'			 => 'repeatPayment',
				'_PAYMENTS'		 => $_PAYMENTS,
				'_REPEAT'		 => 1,
				'order'			 => $order
		));

		if (isset($order->reference) && !empty($order->reference))
			$this->smarty->assign('reference', $order->reference);

		return $this->_warnings().$this->display(__FILE__, 'payment_return.tpl');
	}

	public function hookDisplayAdminOrder($params)
	{
		$_html = '';
		$paymentErrors = '';
		$order = new Order($params['id_order']);
		$token = Tools::getAdminTokenLite('AdminOrders');
		$order_payment = Pms_GoPay_Extra_Order::getOrderById($order->id);

		if (Tools::isSubmit('submitGoPayRefund'))
		{
			if (Tools::getValue('refund_type') == 1)
			{
				if (!$error = $this->_doRefund($order, 1))
					Tools::redirectAdmin('index.php?controller=AdminOrders&id_order='.$order->id.'&vieworder&conf=4&token='.$token);
			}
			elseif(Tools::getValue('refund_type') == 2)
			{
				$products = Tools::getValue('sel_products');
				$shipping = Tools::getValue('sel_shipping');
				$quantity = Tools::getValue('sel_quantity');
				$pricing = Tools::getValue('sel_price');
				$discount = Tools::getValue('sel_discounts');

				if (!$error = $this->_doRefund($order, 2, $products, $quantity, $pricing, $shipping, $discount))
					Tools::redirectAdmin('index.php?controller=AdminOrders&id_order='.$order->id.'&vieworder&conf=4&token='.$token);
			}

			$paymentErrors = $error;
		}
		elseif (Tools::isSubmit('submitCancelPreauthorized'))
		{
			$create_payment = Pms_GoPay_Extra_RestAPI::cancelPreauthorizedPayment($order_payment['id_session']);
			if (empty($create_payment->errors))
			{
				$Callback	 = new Pms_GoPay_Extra_Callback();
				$Callback->updateForCallback($create_payment->id, $order->id);

				Tools::redirectAdmin('index.php?controller=AdminOrders&id_order='.$order->id.'&vieworder&conf=4&token='.$token);
			} else
				$paymentErrors = $create_payment->errors;
		}
		elseif (Tools::isSubmit('submitCapturePreauthorized'))
		{
			$create_payment = Pms_GoPay_Extra_RestAPI::capturePreauthorizedPayment($order_payment['id_session']);
			if (empty($create_payment->errors))
			{
				$Callback	 = new Pms_GoPay_Extra_Callback();
				$Callback->updateForCallback($create_payment->id, $order->id);

				Tools::redirectAdmin('index.php?controller=AdminOrders&id_order='.$order->id.'&vieworder&conf=4&token='.$token);
			} else
				$paymentErrors = $create_payment->errors;
		}

		$admin_templates = array();

		if ($this->_needPaymentButton($order))
			$admin_templates[] = 'payment_link';

		if ($this->_needValidation($order))
			$admin_templates[] = 'validation';

		if ($this->_needRefund($order))
		{
			$admin_templates[] = 'refund';
		}

		if ($bill = Pms_GoPay_Extra_Bills::getBill((int)$order->id))
		{
			$admin_templates[] = 'bill';
			$this->context->smarty->assign(
				array(
					'bill'	 => $bill
				)
			);
		}

		if (count($admin_templates) > 0)
		{
			$currency = new Currency($order->id_currency);
			$carrier = new Carrier($order->id_carrier);
			$refund_amount = Pms_GoPay_Extra_Refund::getTotalAmountRefundByIdOrder($order->id);
			$products = $order->getProducts();
			$refunded_products = Pms_GoPay_Extra_Refund_Products::getListRefundProducts($order->id);
			$refunded_shipping = false;

			foreach ($products as $key=>&$product)
			{
				foreach ($refunded_products as $refund)
				{
					if ($product['id_order_detail'] == $refund['id_order_detail'])
					{
						if ($product['product_quantity'] > $refund['quantity'])
						{
							$product['product_quantity'] = $product['product_quantity'] - $refund['quantity'];
						}
						else
							unset($products[$key]);
					}

					if ($refund['refund_shipping'])
						$refunded_shipping = true;
				}
			}

			$partialy_refund = false;
			if (Pms_GoPay_Extra_Refund::getStatus($order->id) != Pms_GoPay_Extra_Helper::REFUNDED
			   && $refund_amount > 0
			   && $refund_amount < $order->total_paid)
				$partialy_refund = true;

			if (version_compare(_PS_VERSION_, '1.5', '>='))
				$order_state = $order->current_state;
			else
				$order_state = OrderHistory::getLastOrderState($order->id);

			$this->_prepVariables();
			$this->context->smarty->assign(
				array(
					'order_state'	 => $order_state,
					'order'			 => $order,
					'carrier'		 => $carrier,
					'currency'		 => $currency,
					'paymentErrors'	 => $paymentErrors,
					'paymentLink'	 => $this->context->link->getModuleLink($this->name, 'repeatPayment', array('repeatInMail'=>1, 'orderId'=>$order->id), true),

					'refund_amount'	 => $order->total_paid - $refund_amount,
					'partialy_refund' => $partialy_refund,
					'refunded_shipping'	 => $refunded_shipping,
					'discounts'		 => $order->getCartRules(),
					'products'		 => $products,
					'list_refunds'	 => Pms_GoPay_Extra_Refund::getListRefund($order->id)
				)
			);

			foreach ($admin_templates as $admin_template)
			{
				$_html .= $this->display(__FILE__, '/views/templates/admin/admin_order/'.$admin_template.'.tpl');
			}
		}

		return $_html;
	}
	
	
	/* ------------------------------------------------------------- */
	/*  PRIVATE FUNCTIONS
	/* ------------------------------------------------------------- */
	private function _warnings()
	{
		if (!Configuration::get($this->MFIX.'_GATEWAY_MODE') && !Configuration::get($this->MFIX.'_VISIBLE_MODULE')
			  && in_array(Tools::getRemoteAddr(), explode(',', Configuration::get($this->MFIX.'_VISIBLE_MODULE_IP'))))
			return $this->displayError($this->l('Payment gateway is running in test mode , orders will not actually been paid.'));

		return ;
	}

	private function isPatched($filename, $pattern)
	{
		$file   = _PS_OVERRIDE_DIR_.$filename;
		$result = false;
		if (file_exists($file))
		{
			$file_content = file_get_contents($file);
			$result = preg_match($pattern, $file_content) > 0;
		}

		return $result;
	}

	private function _needValidation(Order $order)
	{
		$row = Pms_GoPay_Extra_Order::getOrderById((int)$order->id);

		return $row && $row['preauthorized'] && $row['payment_status'] == Pms_GoPay_Extra_Helper::AUTHORIZED;
	}

	private function _needPaymentButton(Order $order)
	{
		$row = Pms_GoPay_Extra_Order::getOrderById((int)$order->id);

		return $row && !$order->hasBeenPaid() && $row['payment_status'] != Pms_GoPay_Extra_Helper::AUTHORIZED;
	}

	private function _needRefund(Order $order)
	{
		$row = Pms_GoPay_Extra_Order::getOrderById((int)$order->id);

		return $row && Configuration::get($this->MFIX.'_REFUND') && $order->hasBeenPaid();
	}

	private function _doRefund(Order $order, $type, $products = false, $quantity = false, $pricing = false, $shipping = false, $discount = false)
	{
		$order_payment = Pms_GoPay_Extra_Order::getOrderById((int)$order->id);

		if (!$order_payment)
			return sprintf($this->l('Order id %s not exist'), $order->id);
		elseif (!in_array($order_payment['payment_status'], array(Pms_GoPay_Extra_Helper::PARTIALLY_REFUNDED, Pms_GoPay_Extra_Helper::PAID)))
			return $this->l('Payment for this order can not be refunded');

		if ($type == 2)
		{
			if (!is_array($products) && ($order->total_shipping_tax_incl > 0 && !$shipping))
				return $this->l('Nothing is selected');

			$order_products = $order->getProducts();
			foreach ($order_products as $product)
			{
				$id_product = $product['id_order_detail'];
				if (in_array($id_product, $products))
				{
					if ($pricing[$id_product] <= 0)
						return $this->l('Amount must be higher than 0');
					elseif ($pricing[$id_product] > Tools::ps_round($product['unit_price_tax_incl'], 2))
						return sprintf($this->l('The amount you entered %1$s is higher than the original product price %2$s'),  '<b>'.$pricing[$id_product].'</b>',  '<b>'.Tools::ps_round($product['unit_price_tax_incl'], 2).'</b>');

					if ($quantity[$id_product] <= 0)
						return $this->l('Quantity must be higher than 0');
					elseif ($quantity[$id_product] > $product['product_quantity'])
						return sprintf($this->l('The quantities you enter %1$s are higher than the number of pieces in your order %2$s'),  '<b>'.$quantity[$id_product].'</b>',  '<b>'.$product['product_quantity'].'</b>');
				}
			}
		}

	
		$id_session = $order_payment['id_session'];
		$total_paid = Tools::ps_round((float)$order->total_paid, 2);

		$message = $this->l('Refund operation result:').'<br>';
		$create_refund = Pms_GoPay_Extra_RestAPI::refundPayment($id_session, $order, $total_paid, $type, $products, $quantity, $pricing, $shipping, $discount);

		$refund = new Pms_GoPay_Extra_Refund();
		$refund->id_order = (int)$order->id;
		$refund->refund_amount = (float)$create_refund['data']['amount']/100;

		if (!empty($create_refund['status']->errors))
		{
			foreach ($create_refund['status']->errors as $error)
			{
				$error_code = isset($error->error_code) ? $error->error_code : '';
				$error_name = isset($error->error_name) ? $error->error_name : '';
				$error_message = isset($error->message) ? $error->message : '';
				$error_description = isset($error->description) ? $error->description : '';
			}
			$refund->result = $this->l('Error').': '.($error_name ? $error_name : $error_code);

			$message .= $this->l('Transaction error!').'<br>
						Error_code: '.$error_code.'<br>
						Error_name: '.$error_name.'<br>
						Message: '.$error_message.'<br>
						Description: '.$error_description.'<br>
			';
		} else
		{
			$payment_status = Pms_GoPay_Extra_RestAPI::checkPaymentStatus($id_session);

			$order_status = new Pms_GoPay_Extra_Order($order->id);
			$order_status->payment_status = $payment_status->state;
			if (!$order_status->update())
				return $this->l('Error updating order state from Gopay Order table');

			$currency = new Currency((int)$order->id_currency);
			$message .= 'AMOUNT: '.$refund->refund_amount.'<br>
					AUTHORIZATIONID: '.$order_payment['id_session'].'<br>
					CURRENCYCODE: '.$currency->iso_code.'<br>
					COMPLETETYPE: '.$refund->result.'<br>
			';

			if ($type == 2)
			{
				foreach ($products as $id_order_detail)
				{
					$refunded = new Pms_GoPay_Extra_Refund_Products();
					$refunded->id_order = $order->id;
					$refunded->id_order_detail = $id_order_detail;
					$refunded->quantity = $quantity[$id_order_detail];
					$refunded->save();
				}

				if ($shipping)
				{
					$refunded = new Pms_GoPay_Extra_Refund_Products();
					$refunded->id_order = $order->id;
					$refunded->refund_shipping = 1;
					$refunded->save();
				}
			}

			$refund->result = $create_refund['status']->result;
		}

		if (!$refund->save())
			return $this->l('Payment refund not saved in datebase');

		if (!$this->_addNewPrivateMessage((int)$order->id, $message))
			return $this->l('Not send the private message');
	}

	private function generateForm($payment_type)
	{
		$context = Context::getContext();
		$module_dir = _PS_MODULE_DIR_.$this->name.'/views/templates';

		$code = $payment_type['isSwift'] ? $payment_type['payment_group'] : $payment_type['payment_code'];

		if (file_exists($module_dir.'/front/payment_infos/'.$code.'_infos.tpl'))
			$tpl_enable = $context->smarty->createTemplate($module_dir.'/front/payment_infos/'.$code.'_infos.tpl');
		else
			$tpl_enable = $context->smarty->createTemplate($module_dir.'/front/payment_infos.tpl');

		$tpl_enable->assign(array(
			'_PMS_MODULE'	 => $this,
			'PRICE_VIEW'	 => Configuration::get($this->MFIX.'_PRICE_VIEW'),
			'payment_type'	 => $payment_type
		));

		return $tpl_enable->fetch();
	}
	
	
	/* ------------------------------------------------------------- */
	/*  PUBLIC FUNCTIONS
	/* ------------------------------------------------------------- */

	public function _prepVariables()
	{
		return $this->context->smarty->assign(array(
				'_PMS_MODULE'				 => $this,
				'_PMS_PAYMENT_NEW_'			 => _PMS_PAYMENT_NEW_,
				'_PMS_PAYMENT_CHOSEN_'		 => _PMS_PAYMENT_CHOSEN_,
				'_PMS_PAYMENT_TIMEOUT_'		 => _PMS_PAYMENT_TIMEOUT_,
				'_PMS_PAYMENT_CANCELED_'	 => _PMS_PAYMENT_CANCELED_,
				'_PMS_PAYMENT_AUTHORIZE_'	 => _PMS_PAYMENT_AUTHORIZE_,
				'_PS_OS_ERROR_'				 => _PS_OS_ERROR_,
				'_MODULE_DIR'				 => Tools::getShopDomainSsl(true).'/modules/'.$this->name,
				'RECURRENT'					 => new Pms_GoPay_Extra_Recurrent(),
				'GATEWAY_MODE'				 => Configuration::get($this->MFIX.'_GATEWAY_MODE'),
				'INLINE_MODE'				 => Configuration::get($this->MFIX.'_INLINE_MODE'),
				'SKIP_STEP'					 => Configuration::get($this->MFIX.'_SKIP_STEP'),
				'ORDER_MODE'				 => Configuration::get($this->MFIX.'_ORDER_MODE'),
				'PAYMENT_MODE'				 => Configuration::get($this->MFIX.'_PAYMENT_MODE'),
				'BUTTONS_MODE'				 => Configuration::get($this->MFIX.'_BUTTONS_MODE'),
				'_PREAUTHORIZED'			 => Configuration::get($this->MFIX.'_PREAUTHORIZED'),
				'_RECURRENT'				 => Configuration::get($this->MFIX.'_RECURRENT'),
				'_GP_DEF_LANG'				 => strtolower(self::$gopayDefaultLang),
				'_HIDE_GROUP_ACCOUNT'		 => Configuration::get($this->MFIX.'_HIDE_GROUP_ACCOUNT')
		));
	}

	public static function getOrderByReference($reference)
	{
		$sql = 'SELECT `id_order`
				FROM `'._DB_PREFIX_.'orders`
				WHERE `reference` = \''.$reference.'\'
					'.Shop::addSqlRestriction();
		$result = Db::getInstance()->getRow($sql);

		return isset($result['id_order']) ? $result['id_order'] : false;
	}

	public function getPaymentOption($payment_type)
	{
		if (version_compare(_PS_VERSION_, '1.7', '<') === true)
			return;

		$paymentOption = new PaymentOption();
		$paymentOption->setCallToActionText($payment_type['payment_name'])
						->setAction($this->context->link->getModuleLink($this->name, 'validation', array(), true))
						->setModuleName($this->name)
						->setAdditionalInformation($this->generateForm($payment_type))
						->setLogo($payment_type['payment_logo'])
						->setInputs(array(
							'paymentChannel' => array(
								'name' => 'id_payment_button',
								'type' => 'hidden',
								'value' => $payment_type['id_payment_button'],
							),
							'inline_mode' => array(
								'name' => 'inline_mode',
								'type' => 'hidden',
								'value' => Configuration::get($this->MFIX.'_INLINE_MODE'),
							),
						));

		return $paymentOption;
	}

	public function getEmbeddedPaymentOption($params)
	{
		if (version_compare(_PS_VERSION_, '1.7', '<') === true)
			return;

		$paymentOption = new PaymentOption();
		$paymentOption->setCallToActionText($this->l('Fast Payment - GoPay payment gateway'))
						->setModuleName($this->name)
						->setForm($this->displayFormPS7($params));

		return $paymentOption;
    }

	public function getImage($key, $default = false)
	{
		if (!Configuration::get($this->MFIX.'_DISPLAY_IMAGES') && version_compare(_PS_VERSION_, '1.7', '<') === true)
			return;

		$_PAYMENT_IMAGE = $default;
		if (Configuration::get($this->MFIX.'_CUSTOM_IMAGES'))
			$_PAYMENT_IMAGE = __PS_BASE_URI__.'modules/'.$this->name.'/views/images/payments/'.$key.'.gif';

		return $_PAYMENT_IMAGE;
	}

	public function displayForm($params, $templateName)
	{
		$this->_prepVariables();
		$this->context->smarty->assign($params);

		return $this->_warnings().$this->display(__FILE__, '/views/templates/hook/'.$templateName.'.tpl');
	}

	protected function displayFormPS7($cart)
	{
		$this->context->smarty->assign(array(
			'typeG'		 => 'validation',
			'_PAYMENTS'	 => $this->functions->getPaymentInstruments($cart, true)
		));

		return $this->context->smarty->fetch('module:pms_gopay_extra/views/templates/hook/windowed.tpl');
    }

	public function _addNewPrivateMessage($id_order, $message)
	{
		$new_message = new Message();
		$message = strip_tags($message, '<br>');

		if (!Validate::isCleanHtml($message))
			$message = $this->l('Payment message is not valid, please check your module.');

		$new_message->message = $message;
		$new_message->id_order = (int)$id_order;
		$new_message->id_employee = $this->context->employee->id;
		$new_message->private = 1;

		return $new_message->add();
	}

	protected function isAllowedCarrier($id_carrier)
	{
		$allowed_carriers = unserialize(Configuration::get($this->MFIX.'_ALLOWED_CARRIERS'));
		// no restriction if  allowed_carriers is empty
		if (!is_array($allowed_carriers) || !count($allowed_carriers))
			return true;
	  
		if (in_array($id_carrier, $allowed_carriers))
			return true;
	  
		return false;
	}

	protected function getCarrierId($params)
	{
		if ((int)$params['cart']->id_carrier)
			$carrier = new Carrier((int)$params['cart']->id_carrier);

		$option = $this->context->cart->getDeliveryOption(null, false); 
		if (is_array($option))
		{
			$vals = array_values($option);
			if (isset($vals[0]))
				$carrier = new Carrier((int)$vals[0]);
		}

		if (Validate::isLoadedObject($carrier))
			return $carrier->id_reference;
	}

	public function checkCurrency($cart)
	{
		$currency_order = new Currency($cart->id_currency);
		$currencies_module = $this->getCurrency($cart->id_currency);

		if (is_array($currencies_module))
			foreach ($currencies_module as $currency_module)
				if ($currency_order->id == $currency_module['id_currency'])
					return true;

		return false;
	}

	public static function getCost(Cart $cart, $withTax = true, $payment_fee = null, $payment_fee_type = null)
	{
		$orderTotal = $cart->getOrderTotal(true, Cart::BOTH);
		$carrier = new Carrier($cart->id_carrier);
		$currency = new Currency($cart->id_currency);
		$carrier_tax_rate = $carrier->getTaxesRate(new Address((int)$cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}));

		if (!Configuration::get(self::$SFIX.'_PRICE_DIFFERENT'))
		{
			$payment_fee = Configuration::get(self::$SFIX.'_FEE');
			$payment_fee_type = Configuration::get(self::$SFIX.'_FEE_TYPE');
		}

		if($payment_fee_type)
		{
			$percent = $payment_fee/100;
			$fee = $orderTotal * $percent;
   		}  else
			$fee = Tools::convertPrice($payment_fee, $currency);

		if ($withTax)
		{
   			$fee = $fee*(1+($carrier_tax_rate/100));
		}

		if (Configuration::get(self::$SFIX.'_PRICE_VIEW') && $fee > 0)
		{
   			return $fee;
		} else
   			return false;
	}

	public function _displayPeriodes($period_type, $period_name)
	{
		$this->DAYS = 31;
		$this->WEEKS = 53;
		$this->MONTHS = 12;

		$this->context->smarty->assign(array(
			'RECURRENCE_PERIOD'		 => Configuration::get($this->MFIX.'_RECURRENCE_PERIOD'),
			'recurrent_periodes'	 => $this->{$period_type},
			'period_name'			 => $period_name,
		));

		return $this->display(__FILE__, '/views/templates/back/recurrence_period.tpl');
	}

	public function getAPIErrors($id, $log = array())
	{
		$logs = '<ol style="padding:20px;">';
		foreach ($log->errors as $error)
		{
			if (isset($error->scope))
				$logs .= '<li><b>'.$this->l('Error scope: ').'</b> '.$error->scope.'</li>';
			if (isset($error->field))
				$logs .= '<li><b>'.$this->l('Error field: ').'</b> '.$error->field.'</li>';
			if (isset($error->error_code))
				$logs .= '<li><b>'.$this->l('Error code: ').'</b> '.$error->error_code.'</li>';
			if (isset($error->error_name))
				$logs .= '<li><b>'.$this->l('Error name: ').'</b> '.$error->error_name.'</li>';
			if (isset($error->message))
				$logs .= '<li><b>'.$this->l('Error message: ').'</b> '.$error->message.'</li>';
			if (isset($error->description))
				$logs .= '<li><b>'.$this->l('Error description: ').'</b> '.$error->description.'</li><br>';
			if (isset($error->inputs))
				$logs .= '<li><b>'.$this->l('Error inputs: ').'</b> <pre>'.var_export($error->inputs, true).'</pre></li><br>';
		}
		$logs .= '</ol>';

		$orders = $this->l('Your order id is:').' <b>'.$id.'</b>';

		if (Configuration::get($this->MFIX.'_ERRORS_REPORT'))
		{
			$id_lang = (int)$this->context->language->id;
			$iso_lang = Language::getIsoById($id_lang);

			if (!is_dir(dirname(__FILE__).'/mails/'.Tools::strtolower($iso_lang)))
				$id_lang = Language::getIdByIso('en');

			Mail::Send(
						$id_lang,
						'gopay_error_reporting',
						Mail::l('Error reporting from your GoPay module',
						(int)$this->context->language->id),
						array('{logs}' => $logs,
						'{orders}' => $orders),
						Configuration::get('PS_SHOP_EMAIL'),
						null, null, null, null, null,
						_PS_MODULE_DIR_.$this->name.'/mails/'
			);
		}

		return array('orders' => $orders, 'logs' => $logs);
	}

	/* ------------------------------------------------------------- */
	/*  GET TRANSLATIONS
	/* ------------------------------------------------------------- */
	private function translations()
	{
		$this->PAID_MESSAGE = $this->l('The payment was successful. Thank you for using our services.');
		$this->CANCELED_MESSAGE = $this->l('Payment was canceled. Repeat the payment again, please.');
		$this->AUTHORIZED_MESSAGE = $this->l('The payment was authorized, pending completion. We will notify you by email about the payment confirmation.');
		$this->REFUNDED_MESSAGE = $this->l('The payment was refunded.');
		$this->PARTIALLY_REFUNDED_MESSAGE = $this->l('The payment was partially refunded.');
		$this->PAYMENT_METHOD_CHOSEN_ONLINE_MESSAGE = sprintf($this->l('Payment has not been made. We will notify you by email about the payment confirmation. If you do not receive the confirmation email the next working day, please contact support: %s'), Configuration::get('PS_SHOP_EMAIL'));
		$this->PAYMENT_METHOD_CHOSEN_OFFLINE_MESSAGE = $this->l('Payment has not been made. Your email has been sent to you to make a payment via the GoPay gateway. We will notify you by email about the payment confirmation.');
		$this->PAYMENT_METHOD_CHOSEN_MESSAGE = $this->l('Payment has not been made. We will notify you by email about the payment confirmation.');
		$this->FAILED_MESSAGE = sprintf($this->l('During the payment was an error. Contact support by email:  %s'), Configuration::get('PS_SHOP_EMAIL'));
		$this->TIMEOUTED_MESSAGE = $this->l('Payment error - expired payment.');
		$this->CREATED_MESSAGE = $this->l('You have been redirected to the payment gateway, but you have not completed the payment.');
		$this->faultyPaymentIdentity = $this->l('Unable to verify the identity of the payment. Contact e-shop.');
		$this->orderNotExist = $this->l('Unknown order with the number:');
		$this->paymentCreationFailed = $this->l('Failed to create the payment with GoPay. Check the configuration of the GoPay payment module.');
		$this->paymentNotVerified = $this->l('Payment has not been verified.');
		$this->notCorrectOrderPayment = $this->l('Payment can not be made, paymentSessionId does not match the order number.');
		$this->undefinedOrderFaultyState = $this->l('The order cannot be found or you have chosen the wrong state.');
		$this->alreadyClosed = $this->l('The order has been closed. Select the product again.');
		$this->OrderPaymentClosed = $this->l('The order is either paid or closed. You need to create a new order.');
		$this->created = $this->l('You have been redirected to the payment gateway, but you have not completed the payment.');
		$this->configurationFailed = $this->l('The parameters are not set, check the settings in the module.');
		$this->emailIncorrect = $this->l('Incorrect email format:');
		$this->errorSaveOrder = $this->l('Failed to save data to database');
		$this->errorUpdateOrder = $this->l('Failed to update data to database');
		$this->mailRepeated_1 = $this->l('Repeated payments will be repeated in regular cycles every: ');
		$this->mailRepeated_2 = $this->l('The last payment was made: ');
		$this->payAgain = $this->l('Pay again with Gopay');
		$this->payGopay = $this->l('Pay with Gopay');
		$this->notOrderId = $this->l('Order id not exist');
		$this->notAllowedCurrency = $this->l('Currency not allowed:');
	}

	public function validateOrder($id_cart, $id_order_state, $amount_paid, $payment_method = 'Unknown',
		$message = null, $extra_vars = array(), $currency_special = null, $dont_touch_amount = false,
		$secure_key = false, Shop $shop = null)
	{
		if (!Configuration::get($this->MFIX.'_PRICE_VIEW'))
			return parent::validateOrder($id_cart, $id_order_state, $amount_paid, $payment_method, $message, $extra_vars, $currency_special, $dont_touch_amount, $secure_key, $shop);

		if (version_compare(_PS_VERSION_, '1.5', '>=') === true && version_compare(_PS_VERSION_, '1.5.5', '<') === true)
			include_once(dirname(__FILE__).'/validateOrder/1.5.php');

		if (version_compare(_PS_VERSION_, '1.5.5', '>=') === true && version_compare(_PS_VERSION_, '1.6', '<') === true)
			include_once(dirname(__FILE__).'/validateOrder/1.5.5.php');

		if (version_compare(_PS_VERSION_, '1.6', '>=') === true && version_compare(_PS_VERSION_, '1.6.0.8', '<') === true)
			include_once(dirname(__FILE__).'/validateOrder/1.6.php');

		if (version_compare(_PS_VERSION_, '1.6.0.8', '>=') === true && version_compare(_PS_VERSION_, '1.6.1', '<') === true)
			include_once(dirname(__FILE__).'/validateOrder/1.6.0.8.php');

		if (version_compare(_PS_VERSION_, '1.6.1', '>=') === true && version_compare(_PS_VERSION_, '1.7', '<') === true)
			include_once(dirname(__FILE__).'/validateOrder/1.6.1.php');

		if (version_compare(_PS_VERSION_, '1.7', '>=') === true)
			include_once(dirname(__FILE__).'/validateOrder/1.7.php');
		
		$override = new Pms_GoPay_ExtraOverride();
		$override->validateOrder($id_cart, $id_order_state, $amount_paid, $payment_method, $message, $extra_vars, $currency_special, $dont_touch_amount, $secure_key, $shop);

		$this->currentOrder = (int)$override->currentOrder;
	}
}
