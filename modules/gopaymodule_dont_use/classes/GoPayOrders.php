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
if (!defined('_PS_VERSION_'))
	exit;

class Pms_GoPay_Extra_Order extends ObjectModel
{
	public $id_order;
	public $id_cart;
	public $id_session;
	public $currency;
	public $total_paid;
	public $recurrent;
	public $preauthorized;
	public $payment_date;
	public $update_date;
	public $payment_status;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
				'table' => 'pms_gopay_extra_order',
				'primary' => 'id_order',
				'fields' => array(
            		'id_order'		 => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            		'id_cart'		 => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            		'id_session'	 => array('type' => self::TYPE_STRING),
					'currency' 		 => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            		'total_paid'	 => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            		'recurrent'		 => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            		'preauthorized'	 => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
					'payment_date'	 => array('type' => self::TYPE_DATE),
            		'update_date'	 => array('type' => self::TYPE_DATE),
            		'payment_status' => array('type' => self::TYPE_STRING, 'validate' => 'isString')
					)
	);
 
	public function __construct($id = null) 
	{
		return parent::__construct($id);     
	}

	public static function getOrderById($id_order)
	{
		$query = new DbQuery();
		$query->select('a.*');
		$query->from(self::$definition['table'], 'a');
		$query->where('`id_order` = \''.(int)$id_order.'\'');

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
	}

	public static function getIdOrderByTransactionId($id_session)
	{
		$query = new DbQuery();
		$query->select('a.`id_order`');
		$query->from(self::$definition['table'], 'a');
		$query->where('`id_session` = \''.(int)$id_session.'\'');

		if ($result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query))
			return (int)$result;

		return 0;
	}
}
