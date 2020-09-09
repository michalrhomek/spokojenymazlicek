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
class PAYMENTButtons extends ObjectModel
{
	public $id;
	public $payment_code;
	public $payment_group;
	public $isOnline;
	public $isGroup;
	public $isSwift;

	public $payment_name;
	public $payment_desc;
	public $payment_logo;

	public $id_currency;
	public $payment_fee;
	public $payment_fee_type;
	public $position;
	public $active;

	protected static $_links = array();

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'pms_gopay_extra_buttons',
		'primary' => 'id_payment_button',
		'multilang' => true,
		'multilang_shop' => true,
		'fields' => array(
			'payment_code' =>	array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 30),
			'payment_group' =>	array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 100),
			'isOnline' =>		array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'isGroup' =>		array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'isSwift' =>		array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),


			/* Lang fields */
			'payment_name' =>	array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCatalogName', 'required' => true, 'size' => 64),
			'payment_desc' =>	array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 3999999999999),
			'payment_logo' =>	array('type' => self::TYPE_STRING, 'lang' => true, 'size' => 256),
		),
	);

	public function __construct($id = null, $id_lang = null, $id_shop = null, $id_currency = null)
	{
		parent::__construct($id, $id_lang, $id_shop);

		if ($id_currency !== null) {
			$this->id_currency = (Currency::getCurrency($id_currency) !== false) ? $id_currency : Configuration::get('PS_CURRENCY_DEFAULT');
		}

		if ($id)
		{
			$restricts =  Db::getInstance()->getRow('
				SELECT *
				FROM `'._DB_PREFIX_.self::$definition['table'].'_strict`
				WHERE `id_payment_button` = '.(int)$id.'
				AND `id_shop` = '.(int)$this->id_shop.'
				AND `id_currency` = '.(int)$this->id_currency
			);

			$fields = array('payment_fee', 'payment_fee_type', 'position', 'active', 'visible');
			foreach ($fields as $key)
				$this->{$key} = isset($restricts[$key]) ? $restricts[$key] : '';
		}

		if ($id && $id_lang)
		{
			$this->logo_dir = '';
			$logo_file = Pms_GoPay_Extra::$moduleDir.'/views/img/payments/'.$this->payment_logo;
			if (file_exists(_PS_ROOT_DIR_.$logo_file))
				$this->logo_dir = $logo_file;
		}
	}

	public function add($autodate = true, $null_values = false)
	{
		$ret = parent::add($autodate, $null_values);
		$ret &= self::setPaymentStrict($this->id, $this->id_shop, $this->id_currency);
		return $ret;
	}

	public function update($null_values = false)
	{
		$ret = parent::update($null_values);
		$sql = 'UPDATE `'._DB_PREFIX_.bqSQL($this->def['table']).'_strict`
				SET `active` = '.(int)$this->active.',
					`visible` = '.(int)$this->visible.',
					`payment_fee` = "'.(float)$this->payment_fee.'",
					`payment_fee_type` ='.(int)$this->payment_fee_type.'
				WHERE `'.bqSQL($this->def['primary']).'` = '.(int)$this->id.'
				AND `id_shop` = '.(int)$this->id_shop.'
				AND `id_currency` = '.(int)$this->id_currency;

		$ret &= Db::getInstance()->execute($sql);

		return $ret;
	}

	public static function getIdByCode($code)
	{
		return Db::getInstance()->getValue('
			SELECT id_payment_button
			FROM `'._DB_PREFIX_.self::$definition['table'].'`
			WHERE `payment_code` = \''.$code.'\'');
	}

	public function getName($id_lang = null)
	{
		$context = Context::getContext();
		if (!$id_lang) {
			if (isset($this->name[$context->language->id])) {
				$id_lang = $context->language->id;
			} else {
				$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
			}
		}
		return isset($this->name[$id_lang]) ? $this->name[$id_lang] : '';
	}

	public static function setPaymentStrict($id, $id_shop, $id_currency)
	{
		$restrict =  Db::getInstance()->getValue('
			SELECT `id_payment_button`
			FROM `'._DB_PREFIX_.self::$definition['table'].'_strict`
			WHERE `id_payment_button` = '.(int)$id.'
			AND `id_shop` = '.(int)$id_shop.'
			AND `id_currency` = '.(int)$id_currency);

		if (!$restrict)
			Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.self::$definition['table'].'_strict`
				(`id_payment_button`, `id_currency`, `id_shop`, `position`, `visible`, `active`) 
					VALUES (
						'.(int)$id.',
						'.(int)$id_currency.',
						'.(int)$id_shop.',
						'.(int)self::getLastPosition((int)$id_currency).',
						1,
						1
					)
			');

		self::cleanPositions((int)$id_currency);
	}

	public static function setIsVisible($id_payment_button, $id_shop, $id_currency, $visible = 1)
	{
		return Db::getInstance()->execute('
		UPDATE `'._DB_PREFIX_.self::$definition['table'].'_strict`
		SET `visible` = '.(int)$visible.'
		WHERE `'.self::$definition['primary'].'` = '.(int)$id_payment_button.'
		AND `id_shop` = '.(int)$id_shop.'
		AND `id_currency` = '.(int)$id_currency);
	}

	public static function getAllPaymentButtons($id_lang, $id_shop, $id_currency, $group = false, $active = 1, $visible = 1)
	{
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT b.*, bl.*, bs.*
			FROM `'._DB_PREFIX_.self::$definition['table'].'` b
			LEFT JOIN `'._DB_PREFIX_.self::$definition['table'].'_lang` bl ON b.`id_payment_button` = bl.`id_payment_button`
			LEFT JOIN `'._DB_PREFIX_.self::$definition['table'].'_strict` bs ON b.`id_payment_button` = bs.`id_payment_button`
			WHERE bl.`id_lang` = '.(int)$id_lang.'
			AND bl.`id_shop` = '.(int)$id_shop.'
			AND bs.`id_shop` = '.(int)$id_shop.'
			AND bs.`id_currency` = '.(int)$id_currency.'
			AND bs.`active` = '.(int)$active.'
			AND bs.`visible` = '.(int)$visible.'
			'.(!Configuration::get(Pms_GoPay_Extra::$SFIX.'_GATEWAY_MODE') || !$group ? 'AND `isGroup` = 1' : '').'
			ORDER BY bs.`position` ASC
		');

		foreach ($result as $key=>$val) {
			$result[$key]['payment_logo'] = '';
			$logo_file = Pms_GoPay_Extra::$moduleDir.'views/img/payments/'.$val['payment_logo'];
			if (file_exists(_PS_ROOT_DIR_.$logo_file))
				$result[$key]['payment_logo'] = $logo_file;
		}

		return $result;
	}

	public static function getPaymentButtonDetail($id_payment_button, $id_lang, $id_shop, $id_currency)
	{
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT b.*, bl.*, bs.*
			FROM `'._DB_PREFIX_.self::$definition['table'].'` b
			LEFT JOIN `'._DB_PREFIX_.self::$definition['table'].'_lang` bl ON bl.`id_payment_button` = b.`id_payment_button`
			LEFT JOIN `'._DB_PREFIX_.self::$definition['table'].'_strict` bs ON bs.`id_payment_button` = b.`id_payment_button`
			WHERE b.`id_payment_button` = '.(int)$id_payment_button.'
			AND bl.`id_lang` = '.(int)$id_lang.'
			AND bl.`id_shop` = '.(int)$id_shop.'
			AND bs.`id_shop` = '.(int)$id_shop.'
			AND bs.`id_currency` = '.(int)$id_currency.'
		');

		if ($result)
		{
			$button = new stdClass();
			foreach ($result as $key=>$val)
			{
				if ($key == 'id_payment_button')
					$button->id = $val;

				$button->{$key} = $val;

				if ($key == 'payment_logo')
				{
					$button->logo_dir = '';
					$logo_file = Pms_GoPay_Extra::$moduleDir.'views/img/payments/'.$val;
					if (file_exists(_PS_ROOT_DIR_.$logo_file))
						$button->logo_dir = $logo_file;
				}
			}

			return $button;
		}

		return false;
	}

	public function toggleStatus()
	{
		/* Change status to active/inactive */
		return Db::getInstance()->execute('
		UPDATE `'._DB_PREFIX_.bqSQL($this->def['table']).'_strict`
		SET `active` = active XOR 1
		WHERE `'.bqSQL($this->def['primary']).'` = '.(int)$this->id.'
		AND `id_shop` IN ('.implode(', ', array_map('intval', Shop::getContextListShopID())).')
		AND `id_currency` = '.(int)$this->id_currency);
	}

	public function enableStatus()
	{
		/* Change status to active/inactive */
		return Db::getInstance()->execute('
		UPDATE `'._DB_PREFIX_.bqSQL($this->def['table']).'_strict`
		SET `active` = 1
		WHERE `'.bqSQL($this->def['primary']).'` = '.(int)$this->id.'
		AND `id_shop` IN ('.implode(', ', array_map('intval', Shop::getContextListShopID())).')
		AND `id_currency` = '.(int)$this->id_currency);
	}

	public function disableStatus()
	{
		/* Change status to active/inactive */
		return Db::getInstance()->execute('
		UPDATE `'._DB_PREFIX_.bqSQL($this->def['table']).'_strict`
		SET `active` = 0
		WHERE `'.bqSQL($this->def['primary']).'` = '.(int)$this->id.'
		AND `id_shop` IN ('.implode(', ', array_map('intval', Shop::getContextListShopID())).')
		AND `id_currency` = '.(int)$this->id_currency);
	}

	public function delete()
	{
		$this->clearCache();

		// Get children categories
		$to_delete = array((int)$this->id);
		$to_delete = array_unique($to_delete);

		// Delete QUESTION Category and its child from database
		$list = count($to_delete) > 1 ? implode(',', $to_delete) : (int)$this->id;
		$id_shop_list = Shop::getContextListShopID();
		if (count($this->id_shop_list)) {
			$id_shop_list = $this->id_shop_list;
		}

		Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.self::$definition['table'].'_strict`
			WHERE `id_payment_button` IN ('.$list.')
			AND id_shop IN ('.implode(', ', array_map('intval', $id_shop_list)).')
			AND id_currency = '.(int)$this->id_currency.'
		');

		self::cleanPositions($this->id_currency);

		return true;
	}

	public static function deleteSelections($buttons, $id_currency)
	{
		$return = 1;
		foreach ($buttons as $id_payment_button) {
			$payment_button = new PAYMENTButtons($id_payment_button);
			$payment_button->id_currency = $id_currency;
			$return &= $payment_button->delete();
		}
		return $return;
	}

	public static function hidePAYMENTButtonsPosition($name)
	{
		return preg_replace('/^[0-9]+\./', '', $name);
	}

	public function updatePosition($way, $position)
	{
		if (!$res = Db::getInstance()->executeS('
			SELECT `id_payment_button`, `position`
			FROM `'._DB_PREFIX_.self::$definition['table'].'_strict`
			WHERE `id_currency` = '.(int)$this->id_currency.'
			AND `id_shop` IN ('.implode(', ', array_map('intval', Shop::getContextListShopID())).')
			AND `visible` = 1
			ORDER BY `position` ASC'
		)) {
			return false;
		}
		foreach ($res as $input) {
			if ((int)$input['id_payment_button'] == (int)$this->id) {
				$moved_input = $input;
			}
		}
		if (!isset($moved_input) || !isset($position)) {
			return false;
		}

		// < and > statements rather than BETWEEN operator
		// since BETWEEN is treated differently according to databases
		return (Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.self::$definition['table'].'_strict`
			SET `position`= `position` '.($way ? '- 1' : '+ 1').'
			WHERE `position`
			'.($way
				? '> '.(int)$moved_input['position'].' AND `position` <= '.(int)$position
				: '< '.(int)$moved_input['position'].' AND `position` >= '.(int)$position).'
			AND `id_shop` IN ('.implode(', ', array_map('intval', Shop::getContextListShopID())).')
			AND `visible` = 1
			AND `id_currency` = '.(int)$this->id_currency)
		&& Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.self::$definition['table'].'_strict`
			SET `position` = '.(int)$position.'
			WHERE `id_payment_button`='.(int)$moved_input['id_payment_button'].'
			AND `id_shop` IN ('.implode(', ', array_map('intval', Shop::getContextListShopID())).')
			AND `visible` = 1
			AND `id_currency` = '.(int)$this->id_currency)
		);
	}

	public static function cleanPositions($id_currency)
	{
		$result = Db::getInstance()->executeS('
		SELECT `id_payment_button`
		FROM `'._DB_PREFIX_.self::$definition['table'].'_strict`
		WHERE `id_currency` = '.(int)$id_currency.'
		AND `id_shop` IN ('.implode(', ', array_map('intval', Shop::getContextListShopID())).')
		AND `visible` = 1
		ORDER BY `position`');
		$sizeof = count($result);
		for ($i = 0; $i < $sizeof; ++$i) {
			$sql = '
			UPDATE `'._DB_PREFIX_.self::$definition['table'].'_strict`
			SET `position` = '.(int)$i.'
			WHERE `id_payment_button` = '.(int)$result[$i]['id_payment_button'].'
			AND `id_shop` IN ('.implode(', ', array_map('intval', Shop::getContextListShopID())).')
			AND `visible` = 1
			AND `id_currency` = '.(int)$id_currency;
			Db::getInstance()->execute($sql);
		}
		return true;
	}

	public static function getLastPosition($id_currency)
	{
		return Db::getInstance()->getValue('
			SELECT MAX(position)+1
			FROM `'._DB_PREFIX_.self::$definition['table'].'_strict`
			WHERE `id_currency` = '.(int)$id_currency.'
			AND `visible` = 1
			AND `id_shop` IN ('.implode(', ', array_map('intval', Shop::getContextListShopID())).')');
	}
}
