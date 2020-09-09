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
class Pms_GoPay_ExtraValidationModuleFrontController extends ModuleFrontController
{
	public $ssl = true;
	public $paymentErrors = false;
	public $display_column_left = false;
	public $display_column_right = false;

	public function postProcess()
	{
		if (Configuration::get($this->module->MFIX.'_INLINE_MODE'))
			@ini_set('display_errors', 'off');

		// stavy objednávky
		if (!_PMS_PAYMENT_NEW_)
			die($this->module->configurationFailed);

		$cart = $this->context->cart;
		$customer = new Customer($cart->id_customer);
		$currency = new Currency($cart->id_currency);

		if ($cart->id_customer == 0
			|| $cart->id_address_delivery == 0
			|| $cart->id_address_invoice == 0
			|| !$this->module->active
			|| !$this->module->checkCurrency($cart)
			|| !Validate::isLoadedObject($customer)
			|| !in_array(strtoupper($currency->iso_code), Pms_GoPay_Extra::$gopayCurrencies)
			|| !Tools::getValue('id_payment_button')
		 )
			Tools::redirectLink(__PS_BASE_URI__.'order.php?step=1');

		$authorized = false;
		foreach (Module::getPaymentModules() as $module)
			if ($module['name'] == $this->module->name)
			{
				$authorized = true;
				break;
			}

		if (!$authorized)
			die($this->module->l('This payment method is not available.', 'validation'));

		if (!filter_var($customer->email, FILTER_VALIDATE_EMAIL))
			die($this->module->emailIncorrect.' '.$customer->email);


		if ((Tools::getValue('confirm_'.$this->module->name)
			|| Configuration::get($this->module->MFIX.'_SKIP_STEP')
			|| version_compare(_PS_VERSION_, '1.7', '>=') === true
			) && $id_payment_button = Tools::getValue('id_payment_button'))
		{
			$total = $cart->getOrderTotal(true, Cart::BOTH);
			$payment = new PAYMENTButtons($id_payment_button, $cart->id_lang, $cart->id_shop, $cart->id_currency);
			$fee = Pms_GoPay_Extra::getCost($cart, false, $payment->payment_fee, $payment->payment_fee_type);
			$feeWithTax = Pms_GoPay_Extra::getCost($cart, true, $payment->payment_fee, $payment->payment_fee_type);
			$paymentChannel = $payment->isSwift ? $payment->payment_group : $payment->payment_code;
			$paymentSwift = $payment->isSwift ? $payment->payment_code : '';

			/* vytvořit nejdříve objednávku */
			if(!Configuration::get($this->module->MFIX.'_ORDER_MODE'))
			{
				$extra_vars = array(
					'fee'		 => $fee,
					'feeWithTax' => $feeWithTax
				);

				// vytvoreni a nacteni nove objednavky
				$this->module->validateOrder($cart->id, _PMS_PAYMENT_NEW_, $total, $this->module->displayName, NULL, $extra_vars, $cart->id_currency, false, $cart->secure_key, NULL);

				$order = new Order((int)$this->module->currentOrder);

				$total_paid = $order->total_paid;
				$id = $order->id;
				$params = array(
					'key' => $customer->secure_key,
					'id_cart' => $cart->id,
					'id_module' => $this->module->id,
					'id_order' => $order->id
				);

				$returnuURL = $this->context->link->getPageLink('order-confirmation', true, $cart->id_lang, $params);
				$additional_params = '';
			}
			/* nejdříve provést platbu až poté vytvořit objednávku */
			else
			{
				$id = $cart->id;
				$total_paid = (float)Tools::ps_round((float)$cart->getOrderTotal(true, Cart::BOTH)+$feeWithTax, 2);
				$returnuURL = $this->context->link->getModuleLink($this->module->name, 'payInCart', array('payInCart' => 1));
				$additional_params = array(
					array('name' => 'fee', 'value' => $fee),
					array('name' => 'feeWithTax', 'value' => $feeWithTax)
				);
			}

			// vytvorit platbu dle parametru cart
			$create_payment = Pms_GoPay_Extra_RestAPI::createPayment(
					$id,
					$paymentChannel,
					$paymentSwift,
					$returnuURL,
					$total_paid,
					$additional_params,
					true
			);
ob_clean();

			if (empty($create_payment->errors))
			{
				$order_payment = new Pms_GoPay_Extra_Order();
				$order_payment->id_order = (int)$this->module->currentOrder;
				$order_payment->id_cart = $cart->id;
				$order_payment->id_session = $create_payment->id;
				$order_payment->currency = $cart->id_currency;
				$order_payment->total_paid = (float)$total;
				$order_payment->recurrent = (int)Configuration::get($this->module->MFIX.'_RECURRENT');
				$order_payment->preauthorized = (int)Configuration::get($this->module->MFIX.'_PREAUTHORIZED');
				$order_payment->payment_date = pSQL(date('Y-m-d H:i:s'));
				$order_payment->update_date = '0000-00-00 00:00:00';
				$order_payment->payment_status = 'NEW';

				if (!$order_payment->save())
					$this->paymentErrors = array('logs' => $this->module->errorSaveOrder);
				else
				{
					if (Configuration::get($this->module->MFIX.'_INLINE_MODE'))
						die(Tools::jsonEncode(array('url' => $create_payment->gw_url)));
					else
						Tools::redirectLink($create_payment->gw_url);
				}
			} else
			{
				/* načíst vstupní data pro debug chyby */
				$create_payment->errors[0]->inputs = Pms_GoPay_Extra_RestAPI::createPayment(
						$id,
						$paymentChannel,
						$paymentSwift,
						$returnuURL,
						$total_paid,
						NULL,
						FALSE,
						TRUE
				);
	
				if(!Configuration::get($this->module->MFIX.'_ORDER_MODE'))
					$paymentErrors	 = $this->module->getAPIErrors($this->module->l('Order: ').$order->id, $create_payment);
				else
					$paymentErrors	 = $this->module->getAPIErrors($this->module->l('Cart: ').$cart->id, $create_payment);

				$order_payment = new Pms_GoPay_Extra_Order();
				$order_payment->id_order = (int)$this->module->currentOrder;
				$order_payment->id_cart = $cart->id;
				$order_payment->id_session = 'ERROR';
				$order_payment->currency = $cart->id_currency;
				$order_payment->total_paid = (float)$total;
				$order_payment->recurrent = (int)Configuration::get($this->module->MFIX.'_RECURRENT');
				$order_payment->preauthorized = (int)Configuration::get($this->module->MFIX.'_PREAUTHORIZED');
				$order_payment->payment_date = pSQL(date('Y-m-d H:i:s'));
				$order_payment->update_date = '0000-00-00 00:00:00';
				$order_payment->payment_status = 'ERROR';

				if (!$order_payment->save())
					$this->paymentErrors = array('logs' => $this->module->errorSaveOrder);

				if (Configuration::get($this->module->MFIX.'_INLINE_MODE'))
					die(Tools::jsonEncode(array('errors' => $paymentErrors['orders'].$paymentErrors['logs'])));
				else
					$this->paymentErrors = $paymentErrors;
			}
		}
	}

	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();
		if (!empty($this->paymentErrors))
		{
			$this->context->smarty->assign(array(
				'errorsAPI' => $this->paymentErrors
			));

			if (version_compare(_PS_VERSION_, '1.7', '>=') === true)
				$this->setTemplate('module:pms_gopay_extra/views/templates/front/errorPS7.tpl');
			else
				$this->setTemplate('error.tpl');
		} else
		{
			$warning = '';
			$cart = $this->context->cart;
			$currency = new Currency($cart->id_currency);

			$payment = new PAYMENTButtons(Tools::getValue('id_payment_button'), $cart->id_lang, $cart->id_shop, $cart->id_currency);
			$payment->price = Pms_GoPay_Extra::getCost($cart, true, $payment->payment_fee, $payment->payment_fee_type);
			$payment->price_wt = Pms_GoPay_Extra::getCost($cart, false, $payment->payment_fee, $payment->payment_fee_type);

			$params = array(
				'id_payment_button' => $payment->id,
				'confirm_'.$this->module->name => 1
			);

			$this->context->smarty->assign(array(
				'warning'			 => $warning,
				'payment'			 => $payment,
				'address_delivery'	 => new Address(intval($cart->id_address_delivery)),
				'address_invoice'	 => new Address(intval($cart->id_address_invoice)),
				'carrier'			 => new Carrier((int)$cart->id_carrier, (int)$cart->id_lang),
				'payment_url'		 => Context::getContext()->link->getModuleLink($this->module->name, 'validation', $params, true),
				'nbProducts'		 => $cart->nbProducts(),
				'products'			 => $cart->getProducts(),
				'cart_sumary'		 => $cart->getSummaryDetails(),
				'customizedDatas'	 => Product::getAllCustomizedDatas(intval($cart->id)),
				'currency'			 => $currency
			));

			$this->setTemplate('validation.tpl');
		}
	}
}