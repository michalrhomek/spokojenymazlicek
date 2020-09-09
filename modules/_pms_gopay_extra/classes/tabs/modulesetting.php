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

class Pms_GoPay_Extra_modulesetting extends Pms_GoPay_Extra_ModuleConfig
{
	const TABNAME = 'modulesetting';

	/* array('name' => '', 'default' => '', 'isHTML' => false/true, 'isBool' => false/true, 'isLang' => false/true, 'isArray' => false/true) */
	public $configurations = array(
		array('name' => '_VISIBLE_MODULE', 'default' => 0, 'isBool' => true),
		array('name' => '_VISIBLE_MODULE_IP', 'default' => ''),
		array('name' => '_SKIP_STEP', 'default' => 0, 'isBool' => true),
		array('name' => '_ORDER_REFERENCE', 'default' => 0, 'isBool' => true),
		array('name' => '_ALLOWED_CARR', 'default' => array(), 'isArray' => true),
		array('name' => '_ERRORS_REPORT', 'default' => 0, 'isBool' => true),
		array('name' => '_ERRORS_REPORT_EMAIL', 'default' => ''),
	);

	public function init()
	{
		$this->context->controller->addJs($this->_M->module_dir.'/views/js/admin/tabs/'.self::TABNAME.'.js');
		$this->context->controller->addCss($this->_M->module_dir.'/views/css/admin/tabs/'.self::TABNAME.'.css');
	}

	public function getTitle()
	{
		return $this->l('Module', self::TABNAME);
	}

	public function getIcon()
	{
		return 'icon-cogs';
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

		return parent::postProces(self::TABNAME, $this->configurations);
	}

	public function showForm()
	{
		return $this->_M->renderFormClass($this->getForm(), $this->getConfig(self::TABNAME, $this->configurations), self::TABNAME);
	}

	private function getForm()
	{
		$carriers = Carrier::getCarriers($this->context->language->id, true, false, false, null, Carrier::ALL_CARRIERS);
		$_ALLOWED_CARR = unserialize(Configuration::get($this->_M->MFIX.'_ALLOWED_CARR'));

		$fields_form = array(
			'visible-module' => array(
				'form' => array(
					'legend' => array(
						'title' => $this->l('Module visibility for customer', self::TABNAME),
						'icon' => 'icon-eye'
					),
					'input' => array(
						array(
							'type' => 'switch',
							'label' => $this->l('Visible payment gateway for customers', self::TABNAME),
							'name' => '_VISIBLE_MODULE',
							'desc' => $this->l('You can set up and test the module first and then let it visible for your customers.', self::TABNAME),
							'required' => false,
							'is_bool' => true,
							'values' => array(
								array(
									'id' => 'report_on',
									'value' => 1,
									'label' => $this->l('Yes', self::TABNAME)
								),
								array(
									'id' => 'report_off',
									'value' => 0,
									'label' => $this->l('No', self::TABNAME)
								)
							)
						),
						array(
							'type' => 'maintenance_ip',
							'name' => '_VISIBLE_MODULE_IP',
							'id' => '_VISIBLE_MODULE_IP',
							'label' => $this->l('IP:', self::TABNAME),
							'desc' => $this->l('If you want to see the module just you, you have to put your IP address', self::TABNAME),
							'class' => 'mw-200',
							'form_group_class' => (Configuration::get($this->_M->MFIX.'_VISIBLE_MODULE') ? 'hide_mode' : ''),
							'maxlength' => 80,
							'required' => false,
							'lang' => false,
							'script_ip' => '
								<script type="text/javascript">
									function addRemoteAddr()
									{
										var length = $(\'input[name=_VISIBLE_MODULE_IP]\').attr(\'value\').length;
										if (length > 0)
											$(\'input[name=_VISIBLE_MODULE_IP]\').attr(\'value\',$(\'input[name=_VISIBLE_MODULE_IP]\').attr(\'value\') +\','.Tools::getRemoteAddr().'\');
										else
											$(\'input[name=_VISIBLE_MODULE_IP]\').attr(\'value\',\''.Tools::getRemoteAddr().'\');
									}
								</script>',
							'link_remove_ip' => '<button type="button" class="btn btn-default" onclick="addRemoteAddr();"><i class="icon-plus"></i> '.$this->l('Add my IP', 'Helper').'</button>'
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
			
			'functionality' => array(
				'form' => array(
					'legend' => array(
						'title' => $this->l('Setting the functionality', self::TABNAME),
						'icon' => 'icon-puzzle-piece'
					),
					'input' => array(
						array(
							'type' => (version_compare(_PS_VERSION_, '1.7', '<') === true) ? 'switch' : 'hidden',
							'label' => $this->l('Skip step order confirmation', self::TABNAME),
							'name' => '_SKIP_STEP',
							'desc' => $this->l('The order will be done immediately after selecting the payment method without displaying the confirmation site on the next page.', self::TABNAME),
							'required' => false,
							'is_bool' => true,
							'values' => array(
								array(
									'id' => 'skip_on',
									'value' => 1,
									'label' => $this->l('Yes', self::TABNAME)
								),
								array(
									'id' => 'skip_off',
									'value' => 0,
									'label' => $this->l('No', self::TABNAME)
								)
							)
						),
						array(
							'type' => 'switch',
							'label' => $this->l('Pair payments by order reference', self::TABNAME),
							'name' => '_ORDER_REFERENCE',
							'desc' => $this->l('The Order ID will be used as a variable symbol for payment.', self::TABNAME),
							'required' => false,
							'is_bool' => true,
							'values' => array(
								array(
									'id' => 'reference_on',
									'value' => 1,
									'label' => $this->l('Yes', self::TABNAME)
								),
								array(
									'id' => 'reference_off',
									'value' => 0,
									'label' => $this->l('No', self::TABNAME)
								)
							)
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
			
			'carriers' => array(
				'form' => array(
					'legend' => array(
						'title' => $this->l('Allowed carriers', self::TABNAME),
						'icon' => 'icon-truck'
					),
					'input' => array(
						array(
							'type'	=> 'checkbox',
							'label'   => $this->l('Carriers:', self::TABNAME),
							'desc'	=> $this->l('Allowed carriers, leave empty for all.', self::TABNAME),
							'name'	=> '_ALLOWED_CARR',
							'values' => array(
								'query' => $carriers,
								'id' => 'id_reference',
								'val' => 'id_reference',
								'name' => 'name'
							),
							'expand' => array(
								'print_total' => (count($_ALLOWED_CARR) > 0 ? count($_ALLOWED_CARR) : count($carriers)),
								'default' => 'show',
								'show' => array('text' => $this->l('Show', self::TABNAME), 'icon' => 'plus-sign-alt'),
								'hide' => array('text' => $this->l('Hide', self::TABNAME), 'icon' => 'minus-sign-alt')
							), 
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
			
			'errors-report' => array(
				'form' => array(
					'legend' => array(
						'title' => $this->l('Errors Report', self::TABNAME),
						'icon' => 'icon-shield'
					),
					'input' => array(
						array(
							'type' => 'switch',
							'label' => $this->l('Enable Errors Report', self::TABNAME),
							'name' => '_ERRORS_REPORT',
							'desc' => $this->l('Send me an email alert if there is a problem communicating with your payment gateway. ', self::TABNAME).Configuration::get('PS_SHOP_EMAIL'),
							'required' => false,
							'is_bool' => true,
							'values' => array(
								array(
									'id' => 'report_on',
									'value' => 1,
									'label' => $this->l('Yes', self::TABNAME)
								),
								array(
									'id' => 'report_off',
									'value' => 0,
									'label' => $this->l('No', self::TABNAME)
								)
							)
						),
						array(
							'type' => 'text',
							'name' => '_ERRORS_REPORT_EMAIL',
							'label' => $this->l('Email', self::TABNAME),
							'desc' => $this->l('Use this text field if you want to send error messages to another email than the default.', self::TABNAME),
							'class' => 'mw-200',
							'form_group_class' => 'full_mode '.(!Configuration::get($this->_M->MFIX.'_ERRORS_REPORT') ? 'hide_mode' : '').'',
							'maxlength' => 80,
							'required' => false,
							'lang' => false,
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
			)
		);

		return $fields_form;
	}

	public function getConfig($TABNAME, $configurations)
	{
		return parent::getConfig($TABNAME, $configurations);
	}

	public function _validate()
	{
		$condition = array();
		if (Tools::isSubmit('submit_'.self::TABNAME))
		{/*
			if (strlen(Tools::getValue('_GO_ID')) != 10)
				$condition[] = $this->l('GoID must be a numeric value and string with length 10!', self::TABNAME);
		*/}

		if (count($condition) > 0)
			return implode('<br>', $condition);
	}
}
