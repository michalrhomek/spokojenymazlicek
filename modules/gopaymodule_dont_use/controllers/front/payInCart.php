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
class Pms_GoPay_ExtraPayInCartModuleFrontController extends ModuleFrontController
{
	public function postProcess()
	{
		$payment_status = Pms_GoPay_Extra_RestAPI::checkPaymentStatus((int)Tools::getValue('id'));

		if (!empty($payment_status->errors)
			|| (!empty($payment_status->state) && $payment_status->state != 'PAID')
			|| !Tools::getValue('payInCart')
		)
			Tools::redirectLink(__PS_BASE_URI__.'order.php?step=1');

		$cart = new Cart($payment_status->order_number);
		if (!Validate::isLoadedObject($cart))
			Tools::redirectLink(__PS_BASE_URI__.'order.php?step=1');

		if ($cart->OrderExists() == false)
		{
			$customer = new Customer($cart->id_customer);
			if (!Validate::isLoadedObject($customer))
				Tools::redirectLink(__PS_BASE_URI__.'order.php?step=1');

			$extra_vars = array();
			if (isset($payment_status->additional_params))
				foreach ($payment_status->additional_params as $param)
					$extra_vars = array_merge($extra_vars, array(
						$param->name => $param->value
					));

			// castka predavana validateOrder
			$total = $cart->getOrderTotal(true, Cart::BOTH);

			// vytvoreni a nacteni nove objednavky
			$this->module->validateOrder($cart->id, _PS_OS_PAYMENT_, $total, $this->module->displayName, NULL, $extra_vars, $cart->id_currency, false, $cart->secure_key, NULL);

			Tools::redirect('index.php?controller=order-confirmation&id_cart='.$cart->id.'&id_module='.$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$customer->secure_key.'&id='.$payment_status->id);
		} else
		{
			if ($current_order_state->id == _PS_OS_CANCELED_
				|| $current_order_state->id == _PS_OS_PAYMENT_
				|| $current_order_state->id == _PMS_PAYMENT_CREATED_
				|| $order->hasBeenPaid()
				|| $order->hasBeenShipped()
			)
				return;
			else
			{
				$sessionState = $payment_status->state;
				$sessionSubState = isset($payment_status->sub_state) ? $payment_status->sub_state : '';

				$paymentMessage = Pms_GoPay_Extra_Helper::getResultMessage($sessionState, $sessionSubState);
				if(!$paymentMessage)
					return Pms_GoPay_Extra_Tools::getErrors(array('message' => $GoPay->FAILED_MESSAGE));

				if ($sessionState == Pms_GoPay_Extra_Helper::PAID)
				{
					if (isset($payment_status->recurrence))
					{
						$templateVars = array(
							'{recurrence_period}' => $GoPay->mailRepeated_1.$payment_status->recurrence->recurrence_period.' '.$payment_status->recurrence->recurrence_cycle,
							'{recurrence_date_to}' => $GoPay->mailRepeated_2.$payment_status->recurrence->recurrence_date_to
						);
					}
					/* Zaplacena */
					if ($current_order_state->id != _PS_OS_PAYMENT_)
						$this->updateStatus(_PS_OS_PAYMENT_, $order, $templateVars);

					/*$orderPayment->transaction_id = $session['id_session'];
					$orderPayment->update();*/
				}
			}
		}
	}
}
