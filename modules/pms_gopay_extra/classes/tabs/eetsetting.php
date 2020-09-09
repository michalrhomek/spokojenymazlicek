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

class Pms_GoPay_Extra_eetsetting extends Pms_GoPay_Extra_ModuleConfig
{
	const TABNAME = 'eetsetting';
	private $id_lang;

	/* array('name' => '', 'default' => '', 'isHTML' => false/true, 'isBool' => false/true, 'isLang' => false/true, 'isArray' => false/true) */
	public $configurations = array(
		array('name' => '_DIC', 'default' => 0),
		array('name' => '_EET', 'default' => 0, 'isBool' => true),
		array('name' => '_BILL_PDF', 'default' => 0, 'isBool' => true),
		array('name' => '_EET_MSG', 'isLang' => true, 'default' => array('cs' => 'Tržba registrována v běžném režimu dle zákona č. 112/2016 Sb.')),
		array('name' => '_VAT_OTHER', 'default' => 0, 'isBool' => true)
	);

	public function init()
	{
		$this->context->controller->addJs($this->_M->module_dir.'/views/js/admin/tabs/'.self::TABNAME.'.js');
		$this->context->controller->addCss($this->_M->module_dir.'/views/css/admin/tabs/'.self::TABNAME.'.css');
		$this->id_lang = Context::getContext()->language->id;
	}

	public function getTitle()
	{
		return $this->l('EET', self::TABNAME);
	}

	public function getIcon()
	{
		return 'icon-ticket';
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
		$fields_form = array(
			self::TABNAME => array(
				'form' => array(
					'legend' => array(
						'title' => $this->l('EET Setting', self::TABNAME),
						'icon' => 'icon-ticket'
					),
					'input' => array(
						array(
							'type' => 'switch',
							'label' => $this->l('Use EET', 'EETsettings'),
							'name' => '_EET',
							'desc' => $this->l('If you chose the B option on the GoPay payment gateway, enable this option so that you can sell goods with different VAT rates.', 'EETsettings'),
							'required' => false,
							'is_bool' => true,
							'values' => array(
								array(
									'id' => 'eet_on',
									'value' => 1,
									'label' => $this->l('Yes', 'EETsettings')
								),
								array(
									'id' => 'eet_off',
									'value' => 0,
									'label' => $this->l('No', 'EETsettings')
								)
							)
						),
						array(
							'type' => 'text',
							'name' => '_DIC',
							'label' => $this->l('Shop VAT number', 'EETsettings'),
							'form_group_class' => (!Configuration::get($this->_M->MFIX.'_EET') ? 'hide_mode' : ''),
							'desc' => '',
							'class' => 'mw-200',
							'required' => false,
							'lang' => false,
						),
						array(
							'type' => 'switch',
							'label' => $this->l('I pay VAT in another country', 'EETsettings'),
							'name' => '_VAT_OTHER',
							'desc' => $this->l('If you are a VAT payer and you pay taxes in another country where VAT is also assessed, will be listed that you are not a VAT payer.', 'EETsettings'),
							'form_group_class' => (!Configuration::get($this->_M->MFIX.'_EET') ? 'hide_mode' : ''),
							'required' => false,
							'is_bool' => true,
							'values' => array(
								array(
									'id' => 'eet_on',
									'value' => 1,
									'label' => $this->l('Yes', 'EETsettings')
								),
								array(
									'id' => 'eet_off',
									'value' => 0,
									'label' => $this->l('No', 'EETsettings')
								)
							)
						),
						array(
							'type' => 'switch',
							'label' => $this->l('Show payment information in PDF invoice.', 'EETsettings'),
							'name' => '_BILL_PDF',
							'form_group_class' => (!Configuration::get($this->_M->MFIX.'_EET') ? 'hide_mode' : ''),
							'desc' => '',
							'required' => false,
							'is_bool' => true,
							'values' => array(
								array(
									'id' => 'eet_on',
									'value' => 1,
									'label' => $this->l('Yes', 'EETsettings')
								),
								array(
									'id' => 'eet_off',
									'value' => 0,
									'label' => $this->l('No', 'EETsettings')
								)
							)
						),
						array(
							'type' => 'text',
							'name' => '_EET_MSG',
							'label' => $this->l('EET message in PDF invoice', 'EETsettings'),
							'form_group_class' => (!Configuration::get($this->_M->MFIX.'_EET') ? 'hide_mode' : ''),
							'desc' => '',
							'class' => 'col-lg-7',
							'required' => false,
							'lang' => true,
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
		$fields =  parent::getConfig($TABNAME, $configurations);

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
}
