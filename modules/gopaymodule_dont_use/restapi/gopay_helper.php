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
class Pms_GoPay_Extra_Helper {
	/**
	 * Kody stavu platby 
	 */
	const CREATED = "CREATED";
	const PAYMENT_METHOD_CHOSEN = "PAYMENT_METHOD_CHOSEN";
	const PAID = "PAID";
	const AUTHORIZED = "AUTHORIZED";
	const CANCELED = "CANCELED";
	const TIMEOUTED = "TIMEOUTED";
	const REFUNDED = "REFUNDED";
	const PARTIALLY_REFUNDED = "PARTIALLY_REFUNDED";
	const FAILED = "FAILED";
	
	const CALL_COMPLETED = "CALL_COMPLETED";
	const CALL_FAILED = "CALL_FAILED";
	
	
	/**
	 * Konstanty pro opakovanou platbu
	 */
	const RECURRENCE_CYCLE_MONTH = "MONTH";
	const RECURRENCE_CYCLE_WEEK = "WEEK";
	const RECURRENCE_CYCLE_DAY = "DAY";
	const RECURRENCE_CYCLE_ON_DEMAND = "ON_DEMAND";
	
	/**
	 * Konstanty pro zruseni opakovani platby
	 */
	const CALL_RESULT_ACCEPTED = "ACCEPTED";
	const CALL_RESULT_FINISHED = "FINISHED";
	const CALL_RESULT_FAILED = "FAILED";

	/**
	 * URL obrazku tlacitek pro platebni formulare a odkazy 
	 */
	const iconRychloplatba = "https://www.gopay.cz/download/PT_rychloplatba.png";		
	const iconDaruj = "https://www.gopay.cz/download/PT_daruj.png";		
	const iconBuynow = "https://www.gopay.cz/download/PT_buynow.png";		
	const iconDonate = "https://www.gopay.cz/download/PT_donate.png";		
	

	/**
	 * Ziskani korektniho hlaseni o stavu platby - po volani (GopaySoap::isPaymentDone)
	 *
	 * @param String $sessionState - stav platby. Hodnoty viz konstanty Pms_GoPay_Extra_Helper
	 * @param String $sessionSubState - detailnejsi popis stavu platby
	 * 
	 * @return String retezec popisujici stav platby
	 */
	public static function getResultMessage($sessionState, $sessionSubState) {

		$result = "";
		$add_gopay = new Pms_GoPay_Extra();

		if ($sessionState == Pms_GoPay_Extra_Helper::PAID) {
			$result = $add_gopay->PAID_MESSAGE;

		} else if ($sessionState == Pms_GoPay_Extra_Helper::CANCELED) {
			$result = $add_gopay->CANCELED_MESSAGE;

		} else if ($sessionState == Pms_GoPay_Extra_Helper::TIMEOUTED) {
			$result = $add_gopay->TIMEOUTED_MESSAGE;

		} else if ($sessionState == Pms_GoPay_Extra_Helper::CREATED) {
			$result = $add_gopay->CREATED_MESSAGE;

		} else if ($sessionState == Pms_GoPay_Extra_Helper::AUTHORIZED) {
			$result = $add_gopay->AUTHORIZED_MESSAGE;

		} else if ($sessionState == Pms_GoPay_Extra_Helper::REFUNDED) {
			$result = $add_gopay->REFUNDED_MESSAGE;

		} else if ($sessionState == Pms_GoPay_Extra_Helper::PARTIALLY_REFUNDED) {
			$result = $add_gopay->PARTIALLY_REFUNDED_MESSAGE;

		} else if ($sessionState == Pms_GoPay_Extra_Helper::PAYMENT_METHOD_CHOSEN) {
			if (! empty($sessionSubState) && $sessionSubState == 101) {
				$result = $add_gopay->PAYMENT_METHOD_CHOSEN_ONLINE_MESSAGE;

			} else if (! empty($sessionSubState) && $sessionSubState == 102) {
				$result = $add_gopay->PAYMENT_METHOD_CHOSEN_OFFLINE_MESSAGE;

			} else {
				$result = $add_gopay->PAYMENT_METHOD_CHOSEN_MESSAGE;
				
			}
			
		} else {
			$result = false;
		}
		
		return $result;
	}

	public static function getVatRates()
	{
		return array(
			0 => 0,
			10 => 3,
			15 => 2,
			21 => 1
		);
	}
}