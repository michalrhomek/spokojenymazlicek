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
class Pms_GoPay_ExtraRepeatPaymentModuleFrontController extends ModuleFrontController
{
	public $ssl = true;
	public $paymentErrors = false;
	public $display_column_left = false;
	public $display_column_right = false;

	public function postProcess()
	{
		$inlineMode = !Tools::getValue('repeatInMail') && Configuration::get($this->module->MFIX.'_INLINE_MODE');

		if ($inlineMode)
			@ini_set('display_errors', 'off');

		if (!_PMS_PAYMENT_NEW_)
			die($this->module->configurationFailed);

		if (Tools::getValue('orderId') || Tools::getValue('reference'))
		{
			if ($id_order = Pms_gopay_extra::getOrderByReference(Tools::getValue('reference')))
				$order = new Order($id_order);
			else
				$order = new Order(Tools::getValue('orderId'));

			if (!Validate::isLoadedObject($order) || $order->current_state == _PS_OS_CANCELED_ || $order->current_state == _PS_OS_PAYMENT_)
			{
				if ($inlineMode)
					die(Tools::jsonEncode(array('errors' => $this->module->OrderPaymentClosed)));
				else
					die($this->module->OrderPaymentClosed);
			} else
			{
				if (!$id_payment_button = Tools::getValue('id_payment_button'))
					$id_payment_button = PAYMENTButtons::getIdByCode('ACCOUNT');

				$payment = new PAYMENTButtons($id_payment_button, $order->id_lang, $order->id_shop, $order->id_currency);
				$paymentChannel = $payment->isSwift ? $payment->payment_group : $payment->payment_code;
				$paymentSwift = $payment->isSwift ? $payment->payment_code : '';
				$customer = new Customer($order->id_customer);

				$params = array(
					'key' => $customer->secure_key,
					'id_cart' => $order->id_cart,
					'id_module' => $this->module->id,
					'id_order' => $order->id
				);

				$returnuURL = $this->context->link->getPageLink('order-confirmation', true, $order->id_lang, $params);

				$create_payment = Pms_GoPay_Extra_RestAPI::createPayment(
											$order->id,
											$paymentChannel,
											$paymentSwift,
											$returnuURL,
											$order->total_paid,
											NULL,
											FALSE,
											(Tools::getValue('debug') ? TRUE : False)
				);

				if (Tools::getValue('debug'))
				{
					echo '<pre>';
					print_r($create_payment);
					echo '<pre>';
					return;
				}

				if (empty($create_payment->errors))
				{
					ob_start();
					$history = new OrderHistory();
					$history->id_order = $order->id;
					if ($order->current_state != _PMS_PAYMENT_NEW_)
					{
						$history->changeIdOrderState(_PMS_PAYMENT_NEW_, $order->id);
						$history->addWithemail();
					}

					ob_clean();
					$order_payment = new Pms_GoPay_Extra_Order((int)$order->id);
					$order_payment->id_session = pSQL($create_payment->id);
					$order_payment->currency = $order->id_currency;
					$order_payment->total_paid = (float)$order->total_paid;
					$order_payment->update_date = pSQL(date('Y-m-d H:i:s'));
					$order_payment->payment_status = 'NEW';
					if (!$order_payment->id_order)
						$order_payment->id_order = (int)$order->id;
					if (!$order_payment->id_cart)
						$order_payment->id_cart = (int)$order->id_cart;

					if (!$order_payment->save())
						$this->paymentErrors = array('logs' => $GoPay->errorUpdateOrder);
					else
					{
						if ($inlineMode)
							die(Tools::jsonEncode(array('url' => $create_payment->gw_url)));	
						else
							Tools::redirectLink($create_payment->gw_url);
					}
				} else
				{
					/* načíst vstupní data pro debug chyby */
					$create_payment->errors[0]->inputs = Pms_GoPay_Extra_RestAPI::createPayment(
											$order->id,
											$paymentChannel,
											$paymentSwift,
											$returnuURL,
											$order->total_paid,
											NULL,
											FALSE,
											TRUE
					);

					$paymentErrors = $this->module->getAPIErrors($this->module->l('Order: ').$order->id, $create_payment);

					if ($inlineMode)
						die(Tools::jsonEncode(array('errors' => $paymentErrors['orders'].$paymentErrors['logs'])));	
					else
						$this->paymentErrors = $paymentErrors;
				}
			}
		} else
		{
			if ($inlineMode)
				die(Tools::jsonEncode(array('errors' => $this->module->notOrderId)));	
			else
				$this->paymentErrors = array('logs' => $this->module->notOrderId);
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
		}
	}
}