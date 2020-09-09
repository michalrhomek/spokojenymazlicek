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
class Pms_GoPay_Extra_Callback
{
	public function updateForCallback($id_payment, $id_order)
	{
			$_M = new Pms_GoPay_Extra();
			$failedPayments = array(
						Pms_GoPay_Extra_Helper::CREATED,
						Pms_GoPay_Extra_Helper::CANCELED,
						Pms_GoPay_Extra_Helper::TIMEOUTED,
						Pms_GoPay_Extra_Helper::FAILED
			);

			$payment_status = Pms_GoPay_Extra_RestAPI::checkPaymentStatus($id_payment);

			if (empty($payment_status))
			{
				return Pms_GoPay_Extra_Tools::getErrors(array('message' => $_M->faultyPaymentIdentity));
			}
			elseif (!empty($payment_status->errors))
			{
				return $payment_status;
			}

			$order = new Order((int)$id_order);

			if(!Validate::isLoadedObject($order))
			{
				return Pms_GoPay_Extra_Tools::getErrors(array('message' => $_M->orderNotExist.$id_order));
			}

			$currency = new Currency($order->id_currency);
			$current_order_state = $order->getCurrentOrderState();
			$sessionState = $payment_status->state;
			$sessionSubState = isset($payment_status->sub_state) ? $payment_status->sub_state : '';
			$paymentMessage = Pms_GoPay_Extra_Helper::getResultMessage($sessionState, $sessionSubState);

			$order_payment = new Pms_GoPay_Extra_Order((int)$order->id);
			if(!Validate::isLoadedObject($order_payment))
			{
				return Pms_GoPay_Extra_Tools::getErrors(array('message' => $_M->orderNotExist.$order->id));
			}

			$order_payment->id_session = $payment_status->id;
			$order_payment->currency = $order->id_currency;
			$order_payment->total_paid = (float)$order->total_paid;
			$order_payment->update_date = pSQL(date('Y-m-d H:i:s'));
			$order_payment->payment_status = pSQL($sessionState);
			$order_payment->payment_date = '0000-00-00 00:00:00';

			if(!$paymentMessage)
			{
				return Pms_GoPay_Extra_Tools::getErrors(array('message' => $_M->FAILED_MESSAGE.' '.$sessionState.' '.$sessionSubState));
			}

			if (($current_order_state->id == _PS_OS_CANCELED_
				|| $current_order_state->id == _PS_OS_PAYMENT_
				|| $order->hasBeenPaid()
				|| $order->hasBeenShipped()
				) && $payment_status->state != Pms_GoPay_Extra_Helper::REFUNDED
				  && $payment_status->state != Pms_GoPay_Extra_Helper::PARTIALLY_REFUNDED
			)
			{
				return Pms_GoPay_Extra_Tools::getConfirmations($paymentMessage, false);
			}

			if ($sessionState == Pms_GoPay_Extra_Helper::PAID)
			{
				/* Zaplacena */
				if ($current_order_state->id != _PS_OS_PAYMENT_ && !Pms_GoPay_Extra_Bills::getBill($order->id))
				{
					$detail = Pms_GoPay_Extra_RestAPI::getBillDetail($id_payment);
					if (!empty($detail->errors))
					{
						return $detail;
					}

					if ($detail)
					{
						$date = new DateTime($detail[0]->dat_trzby);
						$new_bill = new Pms_GoPay_Extra_Bills();
						$new_bill->id_order = $order->id;
						$new_bill->id_session = $detail[0]->payment_id;
						$new_bill->dat_trzby = $date->format('Y-m-d H:i:s');
						$new_bill->fik = $detail[0]->fik;
						$new_bill->bkp = $detail[0]->bkp;
						$new_bill->pkp = $detail[0]->pkp;
						$new_bill->save();
					}

					$this->updateStatus(_PS_OS_PAYMENT_, $order);
					$order_payment->payment_date = pSQL(date('Y-m-d H:i:s'));
				}

				/*$orderPayment->transaction_id = $session['id_session'];
				$orderPayment->update();*/

			} elseif ($sessionState == Pms_GoPay_Extra_Helper::CREATED)
			{
				/* Platba nebyla zaplacena */
				if ($current_order_state->id != Configuration::get('PS_OS_OUTOFSTOCK_UNPAID'))
				{
					if (isset($payment_status->recurrence) && $current_order_state->id != _PMS_PAYMENT_CREATED_)
						$this->updateStatus(_PMS_PAYMENT_CREATED_, $order);
					elseif ($current_order_state->id != _PMS_PAYMENT_NEW_)
						$this->updateStatus(_PMS_PAYMENT_NEW_, $order);
				}

			} elseif ($sessionState == Pms_GoPay_Extra_Helper::PAYMENT_METHOD_CHOSEN)
			{
				/* Platba ceka na zaplaceni */
				if ($current_order_state->id != _PMS_PAYMENT_CHOSEN_)
					$this->updateStatus(_PMS_PAYMENT_CHOSEN_, $order);

			} elseif ($sessionState == Pms_GoPay_Extra_Helper::CANCELED)
			{
				/* Platba byla zrusena objednavajicim */
				if ($current_order_state->id != _PMS_PAYMENT_CANCELED_)
					$this->updateStatus(_PMS_PAYMENT_CANCELED_, $order);

			} elseif ($sessionState == Pms_GoPay_Extra_Helper::TIMEOUTED)
			{
				/* Platnost platby vyprsela  */
				if ($current_order_state->id != _PMS_PAYMENT_TIMEOUT_)
					$this->updateStatus(_PMS_PAYMENT_TIMEOUT_, $order);

			} elseif ($sessionState == Pms_GoPay_Extra_Helper::AUTHORIZED)
			{
				/* Platba byla autorizovana, ceka se na dokonceni  */
				if ($current_order_state->id != _PMS_PAYMENT_AUTHORIZE_)
					$this->updateStatus(_PMS_PAYMENT_AUTHORIZE_, $order);

			} elseif ($sessionState == Pms_GoPay_Extra_Helper::REFUNDED)
			{
				/* Platba byla vracena - refundovana  */
				if ($current_order_state->id != _PMS_PAYMENT_REFUND_)
					$this->updateStatus(_PMS_PAYMENT_REFUND_, $order);

			} elseif ($sessionState == Pms_GoPay_Extra_Helper::PARTIALLY_REFUNDED)
			{
				/* Platba byla vracena - částečně refundovana  */
				if ($current_order_state->id != _PMS_PAYMENT_PARTIALLY_REFUNDED_)
					$this->updateStatus(_PMS_PAYMENT_PARTIALLY_REFUNDED_, $order);

			} elseif ($sessionState == Pms_GoPay_Extra_Helper::FAILED)
			{
				/* Chyba ve stavu platby */
				if ($current_order_state->id != _PS_OS_ERROR_)
					$this->updateStatus(_PS_OS_ERROR_, $order);
			}

			if ($order_payment->update())
			{
				if (Configuration::get($_M->MFIX.'_RECURRENT') &&
					$sessionState == Pms_GoPay_Extra_Helper::PAID &&
					$current_order_state->id != _PS_OS_PAYMENT_)
				{
					$recurrent = new Pms_GoPay_Extra_Recurrent();
					$recurrent->id_order = (int)$order->id;
					$recurrent->id_session = $payment_status->id;
					$recurrent->id_parent_session = $payment_status->parent_id ? $payment_status->parent_id : 0;
					$recurrent->recurrence_cycle = $payment_status->recurrence->recurrence_cycle;
					$recurrent->recurrence_period = $payment_status->recurrence->recurrence_period;
					$recurrent->recurrence_date_to = $payment_status->recurrence->recurrence_date_to;
					$recurrent->recurrence_state = $payment_status->recurrence->recurrence_state;
					$recurrent->add();
				}
			}

			$repeatPayment = false;
			if (in_array($sessionState, $failedPayments))
				$repeatPayment = true;

			return Pms_GoPay_Extra_Tools::getConfirmations($paymentMessage, $repeatPayment);
	}
	
	private function updateStatus($new_status, $order)
	{
		$use_existings_payment = false;
		if (!$order->hasInvoice())
			$use_existings_payment = true;

		// Create new OrderHistory
		$history = new OrderHistory();
		$history->id_order = $order->id;
		$history->changeIdOrderState((int)$new_status, $order, $use_existings_payment);
		$history->addWithemail();
	}
}