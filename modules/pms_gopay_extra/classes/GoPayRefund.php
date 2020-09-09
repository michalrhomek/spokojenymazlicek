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

class Pms_GoPay_Extra_Refund extends ObjectModel
{
	public $id_gopay_refund;
	public $id_order;
	public $result;		
	public $refund_amount;
	public $date_add;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
				'table' => 'pms_gopay_extra_refund',
				'primary' => 'id_gopay_refund',
				'fields' => array(
					'id_order' 			=>   array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
					'result' 			=>   array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
					'refund_amount' 	=>   array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat', 'required' => true),
					'date_add' 			=>   array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
					)
	);
 
	public function __construct($id = null) 
	{
		return parent::__construct($id);     
	}

	public static function getTotalAmountRefundByIdOrder($id_order)
	{
		$query = new DbQuery();
		$query->select('SUM(refund_amount)');
		$query->from(self::$definition['table']);
		$query->where('id_order = '.(int)$id_order);
		$query->where('result = "FINISHED"');

		return Tools::ps_round(DB::getInstance()->getValue($query), 2);
	}

	public static function getListRefund($id_order)
	{
		$query = new DbQuery();
		$query->from(self::$definition['table']);
		$query->where('id_order = '.$id_order);
		$query->orderBy('date_add DESC');

		return DB::getInstance()->executeS($query);;
	}

	public static function getStatus($id_order)
	{
		$query = new DbQuery();
		$query->select('result');
		$query->from(self::$definition['table']);
		$query->where('id_order = '.$id_order);

		return DB::getInstance()->getValue($query);
	}

	public static function parsePrice($price)
	{
		$price = str_replace(",", ".", $price);
		$regexp = "/^([0-9\s]{0,10})((\.|,)[0-9]{0,2})?$/isD";

		if (preg_match($regexp, $price))
		{
			$array_regexp = array("#,#isD", "# #isD");
			$array_replace = array(".", "");
			$price = preg_replace($array_regexp, $array_replace, $price);

			return Tools::ps_round($price, 2);
		}
		else
			return false;
	}
}