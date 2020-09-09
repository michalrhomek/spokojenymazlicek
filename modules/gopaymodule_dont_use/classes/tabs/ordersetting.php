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
include_once(dirname(__FILE__).'/moduleconfig.php');

class Pms_GoPay_Extra_ordersetting extends Pms_GoPay_Extra_ModuleConfig
{
	const TABNAME = 'ordersetting';
	private $id_lang;

	/* array('name' => '', 'default' => '', 'isHTML' => false/true, 'isBool' => false/true, 'isLang' => false/true, 'isArray' => false/true) */
	public $configurations = array(
		array('name' => '_ALLOWED_MIN_PRICE', 'default' => 0),
		array('name' => '_PRICE_VIEW', 'default' => 0, 'isBool' => true),
		array('name' => '_PRICE_DIFFERENT', 'default' => 0, 'isBool' => true),
		array('name' => '_FEE_TYPE', 'default' => 0),
		array('name' => '_FEE_VALUE', 'default' => 0),
		array('name' => '_REFUND', 'default' => 0, 'isBool' => true),
		array('name' => '_PREAUTHORIZED', 'default' => 0, 'isBool' => true),
		array('name' => '_RECURRENT', 'default' => 0, 'isBool' => true),
		array('name' => '_RECURRENCE_TYPE', 'default' => 0),
		array('name' => '_RECURRENCE_CYCLE', 'default' => 0),
		array('name' => '_RECURRENCE_DATE_TO', 'default' => 0)
	);

	public function init()
	{
		$this->context->controller->addJs($this->_M->module_dir.'/views/js/admin/tabs/'.self::TABNAME.'.js');
		$this->context->controller->addCss($this->_M->module_dir.'/views/css/admin/tabs/'.self::TABNAME.'.css');
		$this->id_lang = Context::getContext()->language->id;
	}

	public function getTitle()
	{
		return $this->l('Order', self::TABNAME);
	}

	public function getIcon()
	{
		return 'icon-shopping-cart';
	}

	public function install($configurations = array())
	{
		return parent::install($this->configurations);
	}

	public function uninstall($configurations = array())
	{
		return parent::uninstall($this->configurations);
	}

	public function postProces($TABNAME = null, $configurations = array())
	{
		if ($error = $this->_validate())
			return $this->_M->displayError($error);

		if (Tools::isSubmit('submit_'.self::TABNAME))
			foreach (Pms_GoPay_Extra::$newStatuses as $status)
				Configuration::updateValue($this->_M->MFIX.$status['name'], Tools::getValue($status['name']));

		return parent::postProces(self::TABNAME, $this->configurations);
	}

	public function showForm()
	{
		return $this->_M->renderFormClass($this->getForm(), $this->getConfig(self::TABNAME, $this->configurations), self::TABNAME);
	}


	
	private function getForm()
	{
		$currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
		
		$_STATUSES = array();
		foreach (self::getOrderStates($this->id_lang) as $status)
		{
			$_STATUSES[] = array(
				"id" => $status['id_order_state'],
				'val' => $status['id_order_state'],
				"name" => $status['id_order_state'].' - '.$status['name']
			);
		}

		$recurrence_types = array(
			array(
				"id" => 'periodic',
				"name" => $this->l('Periodic recurring payment', self::TABNAME)
			),
			array(
				"id" => 'request',
				"name" => $this->l('Repeated payment on request', self::TABNAME)
			)
		);

		$recurrence_cycles = array(
			array(
				"id" => 'DAY',
				"name" => $this->l('Daily recurring', self::TABNAME)
			),
			array(
				"id" => 'WEEK',
				"name" => $this->l('Weekly recurring', self::TABNAME)
			),
			array(
				"id" => 'MONTH',
				"name" => $this->l('Monthly recurring', self::TABNAME)
			)
		);

		$_RECURRENCE_DATE_TO = array(
			array(
				"id" => '0',
				"name" => $this->l('Never (end of expiration CK)', self::TABNAME)
			),
			array(
				"id" => '3',
				"name" => '3 '.$this->l('cycles', self::TABNAME)
			),
			array(
				"id" => '6',
				"name" => '6 '.$this->l('cycles', self::TABNAME)
			),
			array(
				"id" => '9',
				"name" => '9 '.$this->l('cycles', self::TABNAME)
			),
			array(
				"id" => '18',
				"name" => '18 '.$this->l('cycles', self::TABNAME)
			),
			array(
				"id" => '24',
				"name" => '24 '.$this->l('cycles', self::TABNAME)
			),
			array(
				"id" => '30',
				"name" => '30 '.$this->l('cycles', self::TABNAME)
			),
			array(
				"id" => '36',
				"name" => '36 '.$this->l('cycles', self::TABNAME)
			),
			array(
				"id" => '42',
				"name" => '42 '.$this->l('cycles', self::TABNAME)
			),
			array(
				"id" => '48',
				"name" => '48 '.$this->l('cycles', self::TABNAME)
			)
		);
		
		
		$fields_form = array(
			self::TABNAME => array(
				'form' => array(
					'legend' => array(
						'title' => $this->l('Price rules', self::TABNAME),
						'icon' => 'icon-AdminPriceRule'
					),
					'input' => array(
						array(
							'type' => 'text',
							'name' => '_ALLOWED_MIN_PRICE',
							'label' => $this->l('Minimum order price', self::TABNAME),
							'suffix' => $currency->sign,
							'desc' => $this->l('Minimum order price (without shipping) to display the ability to pay with GoPay (0 means no limit).', self::TABNAME),
							'class' => 'col-lg-2',
							'maxlength' => 6,
							'required' => false,
							'lang' => false,
						),
						array(
							'type' => 'switch',
							'label' => $this->l('Enable fee', self::TABNAME),
							'name' => '_PRICE_VIEW',
							'desc' => $this->l('If a customer has to pay a fee when using a GoPay payment method.', self::TABNAME),
							'required' => false,
							'is_bool' => true,
							'values' => array(
								array(
									'id' => 'view_on',
									'value' => 1,
									'label' => $this->l('Yes', self::TABNAME)
								),
								array(
									'id' => 'view_off',
									'value' => 0,
									'label' => $this->l('No', self::TABNAME)
								)
							)
						),
						array(
							'type' => 'switch',
							'label' => $this->l('Different for each payment button', self::TABNAME),
							'name' => '_PRICE_DIFFERENT',
							'desc' => $this->l('If you want to set a different charge for each payment button. You can setup it on the bookmark Payment buttons.', self::TABNAME),
							'form_group_class' => '_PRICE_VIEW '.(!Configuration::get($this->_M->MFIX.'_PRICE_VIEW') ? 'hide_mode' : ''),
							'required' => false,
							'is_bool' => true,
							'values' => array(
								array(
									'id' => 'different_on',
									'value' => 1,
									'label' => $this->l('Yes', self::TABNAME)
								),
								array(
									'id' => 'different_off',
									'value' => 0,
									'label' => $this->l('No', self::TABNAME)
								)
							)
						),
						array(
							'type' => 'select',
							'name' => '_FEE_TYPE',
							'label' => $this->l('Type of fee', self::TABNAME),
							'required' => false,
							'disabled' => Configuration::get($this->_M->MFIX.'_PRICE_DIFFERENT') ? true : false,
							'form_group_class' => '_PRICE_VIEW '.(!Configuration::get($this->_M->MFIX.'_PRICE_VIEW') ? 'hide_mode' : ''),
							'lang' => false,
							'options' => array(
								'query' => array(
									array(
										'id' => '1',
										'name' => $this->l('Percent', self::TABNAME)
									),
									array(
										'id' => '0',
										'name' => $this->l('Amount', self::TABNAME)
									)
								),
								'id' => 'id',
								'name' => 'name'
							)
						),
						array(
							'type' => 'text',
							'name' => '_FEE_VALUE',
							'label' => $this->l('Fee', self::TABNAME),
							'suffix' => $currency->sign.' / %',
							'desc' => $this->l('The amount of the fee, that will the customer pays for the payment through the GoPay.', self::TABNAME).'<br>'.$this->l('If you want to set a different charge for each payment button. You can setup it on the bookmark Payment buttons.', self::TABNAME),
							'class' => 'col-lg-2',
							'maxlength' => 6,
							'required' => false,
							'disabled' => Configuration::get($this->_M->MFIX.'_PRICE_DIFFERENT') ? true : false,
							'form_group_class' => '_PRICE_VIEW '.(!Configuration::get($this->_M->MFIX.'_PRICE_VIEW') ? 'hide_mode' : ''),
							'lang' => false,
						),
						array(
							'type' => 'html',
							'name' => '',
							'label' => ' ',
							'title' => '',
							'col' => 12,
							'form_group_class' => '_PRICE_VIEW '.(!Configuration::get($this->_M->MFIX.'_PRICE_VIEW') ? 'hide_mode' : ''),
							'html_content' => '<div class="alert alert-info" id="_PRICE_INFO_TEXT">'.$this->l('The tax rate will be calculated according to the selected carrier since the surcharge added to the cost of transport.', self::TABNAME).'</div>'
						),
						array(
							'type' => 'hidden',
							'name' => 'idTab'
						)
					),
					// Submit Button
					'submit' => array(
						'title' => $this->l('Save', self::TABNAME),
						'name' => 'submit_'.self::TABNAME,
						'icon' => 'process-icon-save'
					)
				)
			),
			'payment-preference' => array(
				'form' => array(
					'legend' => array(
						'title' => $this->l('Setting payment preference', self::TABNAME),
						'icon' => 'icon-money'
					),
					'input' => array(
						array(
							'type' => 'html',
							'name' => '',
							'label' => ' ',
							'title' => '',
							'col' => 9,
							'html_content' => '
								<script type="text/javascript">
									var period_days = \''.$this->_displayPeriodes('DAYS', $this->l('days', self::TABNAME)).'\';
									var period_weeks = \''.$this->_displayPeriodes('WEEKS', $this->l('weeks', self::TABNAME)).'\';
									var period_months = \''.$this->_displayPeriodes('MONTHS', $this->l('months', self::TABNAME)).'\';
								</script>'
						),
						array(
							'type' => 'switch',
							'name' => '_REFUND',
							'label' => $this->l('Allow money return', self::TABNAME),
							'desc' => $this->l('In the details of the order, you will be given the option to return the whole or partial payment back to the customer.', self::TABNAME),
							'required' => false,
							'is_bool' => true,
							'values' => array(
								array(
									'id' => 'refund_on',
									'value' => 1,
									'label' => $this->l('Yes', self::TABNAME)
								),
								array(
									'id' => 'refund_off',
									'value' => 0,
									'label' => $this->l('No', self::TABNAME)
								)
							)
						),
						array(
							'type' => 'switch',
							'label' => $this->l('Pre-authorized payments', self::TABNAME),
							'name' => '_PREAUTHORIZED',
							'desc' => $this->l('Pre-authorization is a type of payment that provides blocking funds in the bank account of paying customer. At the moment of completion of payment on the payment gateway funds are not transferred to GoPay business account but is created blocking (pre-authorization) on the bank of paying customer. Blocking can be based on the trader instruction canceled or made a transaction as completed, ie. the transfer of blocked funds to GoPay business account. All operations with pre-authorized payments can be made for a period of 4 days from the establishment of payment. Only payment cards support pre-authorize payment.', self::TABNAME),
							'required' => false,
							'is_bool' => true,
							'values' => array(
								array(
									'id' => 'preauthorized_on',
									'value' => 1,
									'label' => $this->l('Yes', self::TABNAME)
								),
								array(
									'id' => 'preauthorized_off',
									'value' => 0,
									'label' => $this->l('No', self::TABNAME)
								)
							)
						),
						array(
							'type' => 'switch',
							'label' => $this->l('Recurrent payments', self::TABNAME),
							'name' => '_RECURRENT',
							'desc' => $this->l('Recurring payment is a functionality that allows accepting payment from a customer on a regular basis. The customer is at the time of creating payment informed at the payment gateway of its parameters (amount, payment frequency, etc.). After the successful initialization, payment is paid automatically in the defined period or upon request. The customer is informed by e-mail about each completed payment. Merchant is informed through notification about the change in payment. Only payment cards support recurring payment.', self::TABNAME),
							'required' => false,
							'is_bool' => true,
							'values' => array(
								array(
									'id' => 'recurrent_on',
									'value' => 1,
									'label' => $this->l('Yes', self::TABNAME)
								),
								array(
									'id' => 'recurrent_off',
									'value' => 0,
									'label' => $this->l('No', self::TABNAME)
								)
							)
						),
						array(
							'type' => 'select',
							'name' => '_RECURRENCE_TYPE',
							'label' => $this->l('Recurrence type', self::TABNAME),
							'desc' => $this->l('Time period of recurring.', self::TABNAME),
							'form_group_class' => (!Configuration::get($this->_M->MFIX.'_RECURRENT') ? 'hide_mode' : ''),
							'required' => false,
							'lang' => false,
							'options' => array(
								'query' => $recurrence_types,
								'id' => 'id',
								'name' => 'name'
							)
						),
						array(
							'type' => 'select',
							'name' => '_RECURRENCE_CYCLE',
							'label' => $this->l('Recurrence cycle', self::TABNAME),
							'desc' => $this->l('Time period of recurring.', self::TABNAME),
							'class' => 'display_inline',
							'form_group_class' => (!Configuration::get($this->_M->MFIX.'_RECURRENT') ? 'hide_mode' : ''),
							'required' => false,
							'lang' => false,
							'options' => array(
								'query' => $recurrence_cycles,
								'id' => 'id',
								'name' => 'name'
							),							
						),
						array(
							'type' => 'select',
							'name' => '_RECURRENCE_DATE_TO',
							'label' => $this->l('Recurrence date to', self::TABNAME),
							'desc' => $this->l('End of recurrence.', self::TABNAME),
							'form_group_class' => (!Configuration::get($this->_M->MFIX.'_RECURRENT') ? 'hide_mode' : ''),
							'required' => false,
							'lang' => false,
							'options' => array(
								'query' => $_RECURRENCE_DATE_TO,
								'id' => 'id',
								'name' => 'name'
							)
						),
						array(
							'type' => 'hidden',
							'name' => 'idTab'
						)
					),
					// Submit Button
					'submit' => array(
						'title' => $this->l('Save', self::TABNAME),
						'name' => 'submit_'.self::TABNAME,
						'icon' => 'process-icon-save'
					)
				)
			),
			'statuses' => array(
				'form' => array(
					'legend' => array(
						'title' => $this->l('Statuses settings', self::TABNAME),
						'icon' => 'icon-list'
					),
					'input' => $this->status_inputs(),
					'class' => 'display_inline',
					// Submit Button
					'submit' => array(
						'title' => $this->l('Save', self::TABNAME),
						'name' => 'submit_'.self::TABNAME,
						'icon' => 'process-icon-save'
					)
				)
			),
			
		);

		return $fields_form;
	}

	public function getConfig($TABNAME, $configurations)
	{
		$fields =  parent::getConfig($TABNAME, $configurations);
		foreach (Pms_GoPay_Extra::$newStatuses as $status)
			$fields[$status['name']] = Tools::getValue($status['name'], Configuration::get($this->_M->MFIX.$status['name']));

		return $fields;
	}

	protected function _validate()
	{
		$condition = array();
		if (Tools::isSubmit('submit_'.self::TABNAME))
		{/*
			if (strlen(Tools::getValue('_GO_ID')) != 10)
				$condition[] = $this->l('Target GoID must be a numeric value and string length 10!', self::TABNAME);
		*/}

		if (count($condition) > 0)
			return implode('<br>', $condition);
	}

	
	// Přidané funkce
	private function getStatuses()									  
	{
		return $statuses = array(
			array('code' => '_NEW', 'name' => 'GoPay - order created'),
			array('code' => '_CREATED', 'name' => 'GoPay - recurring payment created'),
			array('code' => '_TIMEOUTED', 'name' => 'GoPay - timeout'),
			array('code' => '_PAYMENT_METHOD_CHOSEN', 'name' => 'GoPay - payment chosen'),
			array('code' => '_CANCELED', 'name' => 'GoPay - canceled'),
			array('code' => '_AUTHORIZED', 'name' => 'GoPay - authorized'),
			array('code' => '_REFUNDED', 'name' => 'GoPay - refunded'),
			array('code' => '_PARTIALLY_REFUNDED', 'name' => 'GoPay - partially refunded')
		);
	}

	private function status_inputs()									  
	{
		foreach (OrderState::getOrderStates((int)$this->id_lang) as $status)
		{
			$_ORDER_STATUSES[] = array(
				"id" => $status['id_order_state'],
				'val' => $status['id_order_state'],
				"name" => $status['id_order_state'].' - '.$status['name']
			);
		}

		$inputs[] = array(
						'type' => 'hidden',
						'name' => 'idTab'
		);

		foreach ($this->getStatuses() as $status)
		{
			$inputs[] = array(
							'type' => 'select',
							'name' => $status['code'],
							'label' => $status['name'],
							'desc' => '',
							'required' => false,
							'lang' => false,
							'options' => array(
								'query' => $_ORDER_STATUSES,
								'id' => 'id',
								'name' => 'name'
							)
			);
		}

		return $inputs;
	}

	private function _displayPeriodes($period_type, $period_name)
	{
		$DAYS = 31;
		$WEEKS = 53;
		$MONTHS = 12;
		$module_dir = _PS_MODULE_DIR_.$this->_M->name.'/views/templates/classes';
		$tpl_enable = Context::getContext()->smarty->createTemplate($module_dir.'/_recurrence_period.tpl');

		$tpl_enable->assign(array(
			'RECURRENCE_PERIOD'		 => Configuration::get($this->_M->MFIX.'_RECURRENCE_PERIOD'),
			'recurrent_periodes'	 => ${$period_type},
			'period_name'			 => $period_name,
		));

		return $tpl_enable->fetch();
	}

	private static function getOrderStates($id_lang)
	{
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT *
			FROM `'._DB_PREFIX_.'order_state` os
			LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = '.(int)$id_lang.')
			WHERE deleted = 0
			AND osl.`name` LIKE \'Gopay%\'
			ORDER BY `name` ASC');

		return $result;
	}
}
