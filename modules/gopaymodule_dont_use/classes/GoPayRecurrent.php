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

class Pms_GoPay_Extra_Recurrent extends ObjectModel
{
	public $id_gopay_recurrent;
	public $id_order;
	public $id_session;
	public $id_parent_session;
	public $date_add;
	public $recurrence_cycle;
	public $recurrence_period;
	public $recurrence_date_to;
	public $recurrence_state;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
				'table' => 'pms_gopay_extra_recurrent',
				'primary' => 'id_gopay_recurrent',
				'fields' => array(
            		'id_session'		 => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true),
            		'id_parent_session'	 => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true),
            		'id_order'			 => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
					'date_add' 			 => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            		'recurrence_cycle'	 => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true),
            		'recurrence_period'	 => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
					'recurrence_date_to' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            		'recurrence_state'	 => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true)
					)
	);
 
	public function __construct($id = null) 
	{
		$this->date_add = date('Y-m-d H:i:s');
		
		return parent::__construct($id);     
	}

	public static function getReccurenceStarted($id_order)
	{
		if (!self::getReccurenceEnded($id_order))
		{
			$query = new DbQuery();
			$query->select('id_session');
			$query->from(self::$definition['table']);
			$query->where('id_order = '.(int)$id_order);
			$query->where('recurrence_state = "STARTED"');
			return DB::getInstance()->getValue($query);
		}

		return false;
	}

	public static function getReccurencePeriod($id_order)
	{
		if (!self::getReccurenceEnded($id_order))
		{
			$query = new DbQuery();
			$query->select('recurrence_period, recurrence_cycle, recurrence_date_to');
			$query->from(self::$definition['table']);
			$query->where('id_order = '.(int)$id_order);
			$query->where('recurrence_state = "STARTED"');
			return DB::getInstance()->executeS($query);
		}

		return false;
	}

	public static function getReccurenceEnded($id_order)
	{
		$query = new DbQuery();
		$query->select('id_session');
		$query->from(self::$definition['table']);
		$query->where('id_order = '.(int)$id_order);
		$query->where('recurrence_state = "END_RECURRENT"');
		return DB::getInstance()->getValue($query);
	}

	public static function listRecurrents($id_order)
	{
		$query = new DbQuery();
		$query->select('id_session, id_parent_session, date_add, recurrence_state');
		$query->from(self::$definition['table']);
		$query->where('id_order = '.(int)$id_order);
        $query->orderBy('`date_add` ASC');
		return DB::getInstance()->executeS($query);
	}

	public static function getIdGopayRecurrentByIdSession($id_session)
	{
		$query = new DbQuery();
		$query->select('id_gopay_recurrent');
		$query->from(self::$definition['table']);
		$query->where('id_session = '.(int)$id_session);
        $query->orderBy('`date_add` ASC');
		return DB::getInstance()->getValue($query);
	}
}