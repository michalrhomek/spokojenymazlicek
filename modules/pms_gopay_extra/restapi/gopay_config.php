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

define('_PMS_PAYMENT_NEW_', Configuration::get(Pms_GoPay_Extra::$SFIX.'_NEW'));
define('_PMS_PAYMENT_CREATED_', Configuration::get(Pms_GoPay_Extra::$SFIX.'_CREATED'));
define('_PMS_PAYMENT_TIMEOUT_', Configuration::get(Pms_GoPay_Extra::$SFIX.'_TIMEOUTED'));
define('_PMS_PAYMENT_CHOSEN_', Configuration::get(Pms_GoPay_Extra::$SFIX.'_PAYMENT_METHOD_CHOSEN'));
define('_PMS_PAYMENT_CANCELED_', Configuration::get(Pms_GoPay_Extra::$SFIX.'_CANCELED'));
define('_PMS_PAYMENT_AUTHORIZE_', Configuration::get(Pms_GoPay_Extra::$SFIX.'_AUTHORIZED'));
define('_PMS_PAYMENT_REFUND_', Configuration::get(Pms_GoPay_Extra::$SFIX.'_REFUNDED'));
define('_PMS_PAYMENT_PARTIALLY_REFUNDED_', Configuration::get(Pms_GoPay_Extra::$SFIX.'_PARTIALLY_REFUNDED'));

class Pms_GoPay_Extra_Config
{
	const TEST = "TEST";
	const PROD = "PROD";

	/**
	 * Parametr specifikujici, pracuje-li se na testovacim ci provoznim prostredi
	 */
	static $version = self::TEST;
	
	/**
	 * Nastaveni testovaciho ci provozniho prostredi prostrednictvim parametru
	 * 
	 * @param $new_version
	 * TEST - Testovaci prostredi
	 * PROD - Provozni prostredi
	 *
	 */
	public static function init()
	{
		if(Configuration::get(Pms_GoPay_Extra::$SFIX.'_GATEWAY_MODE') == 1)
			self::$version = self::PROD;
	}
	
	/**
	 * URL platebni brany pro uplnou integraci
	 *
	 * @return URL
	 */
	public static function getURL()
	{
		if (self::$version == self::PROD)
			return "https://gate.gopay.cz/api/";
		 else
			return "https://gw.sandbox.gopay.com/api/";
	}

	/**
	 * GO_ID
	 *
	 * @return GO_ID
	 */
	public static function GO_ID()
	{
		if (self::$version == self::PROD)
			return Configuration::get(Pms_GoPay_Extra::$SFIX.'_GO_ID');
		 else
			return Configuration::get(Pms_GoPay_Extra::$SFIX.'_GO_ID_TEST');
	}

	/**
	 * CLIENT_ID
	 *
	 * @return CLIENT_ID
	 */
	public static function CLIENT_ID()
	{
		if (self::$version == self::PROD)
			return Configuration::get(Pms_GoPay_Extra::$SFIX.'_CLIENT_ID');
		 else
			return Configuration::get(Pms_GoPay_Extra::$SFIX.'_CLIENT_ID_TEST');
	}

	/**
	 * CLIENT_SECRET
	 *
	 * @return CLIENT_SECRET
	 */
	public static function CLIENT_SECRET()
	{
		if (self::$version == self::PROD)
			return Configuration::get(Pms_GoPay_Extra::$SFIX.'_CLIENT_SECRET');
		 else
			return Configuration::get(Pms_GoPay_Extra::$SFIX.'_CLIENT_SECRET_TEST');
	}

	/**
	 * URL webove sluzby GoPay
	 *
	 * @return URL - wsdl
	 */
	public static function ws() {
		
		if (self::$version == self::PROD) {
			return "https://gate.gopay.cz/axis/EPaymentServiceV2?wsdl";		
			
		} else {
			return "https://testgw.gopay.cz/axis/EPaymentServiceV2?wsdl";	
			
		}
	}
}