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

class Pms_GoPay_Extra_gatewaysetting extends Pms_GoPay_Extra_ModuleConfig
{
	const TABNAME = 'gatewaysetting';

	/* array('name' => '', 'default' => '', 'isHTML' => false/true, 'isBool' => false/true, 'isLang' => false/true, 'isArray' => false/true) */
	public $configurations = array(
		array('name' => '_GATEWAY_MODE', 'default' => 0, 'isBool' => true),
		array('name' => '_GO_ID', 'default' => ''),
		array('name' => '_CLIENT_ID', 'default' => ''),
		array('name' => '_CLIENT_SECRET', 'default' => ''),
		array('name' => '_GO_ID_TEST', 'default' => '0'),
		array('name' => '_CLIENT_ID_TEST', 'default' => '0'),
		array('name' => '_CLIENT_SECRET_TEST', 'default' => '0'),
		array('name' => '_INLINE_MODE', 'default' => 0, 'isBool' => true),
		array('name' => '_HOOK', 'default' => 'left'),
		array('name' => '_ORDER_DESCRIPTION', 'default' => '', 'isLang' => true)
	);

	public function init()
	{
		$this->context->controller->addJs($this->_M->module_dir.'/views/js/admin/tabs/'.self::TABNAME.'.js');
		$this->context->controller->addCss($this->_M->module_dir.'/views/css/admin/tabs/'.self::TABNAME.'.css');
	}

	public function getTitle()
	{
		return $this->l('Payment gateway', self::TABNAME);
	}

	public function getIcon()
	{
		return 'icon-bank';
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
		$hooks = array(
			array(
				"id" => 'none',
				"name" => $this->l('Disable', self::TABNAME)
			),
			array(
				"id" => 'left',
				"name" => $this->l('Left column', self::TABNAME)
			),
			array(
				"id" => 'right',
				"name" => $this->l('Right column', self::TABNAME)
			),
			array(
				"id" => 'footer',
				"name" => $this->l('Footer', self::TABNAME)
			)
		);
		
		$fields_form = array(
			'operating_data' => array(
				'form' => array(
					'legend' => array(
						'title' => $this->l('Production credentials', self::TABNAME),
						'icon' => 'icon-bank'
					),
					'input' => array(
						array(
							'type' => 'radio',
							'label' => $this->l('Payment Gateway Mode', self::TABNAME),
							'desc' => $this->l('Choose your payment gateway mode. For the functionality test with the test login, select the Test. For normal mode, switch to Full.', self::TABNAME),
							'name' => '_GATEWAY_MODE',
							'required' => false,
							'is_bool' => true,
							'values' => array(
								array(
									'id' => 'mode_on',
									'value' => 1,
									'label' => $this->l('Full', self::TABNAME)
								),
								array(
									'id' => 'mode_off',
									'value' => 0,
									'label' => $this->l('Test', self::TABNAME)
								)
							)
						),
						array(
							'type' => 'text',
							'name' => '_GO_ID',
							'label' => $this->l('GoID', self::TABNAME),
							'desc' => $this->l('GoID is the 10-digit identifier of your eshop, provided by GoPay company.', self::TABNAME),
							'class' => 'mw-200',
							'form_group_class' => 'full_mode '.(!Configuration::get($this->_M->MFIX.'_GATEWAY_MODE') ? 'hide_mode' : '').'',
							'maxlength' => 10,
							'required' => true,
							'lang' => false,
						),
						array(
							'type' => 'text',
							'name' => '_CLIENT_ID',
							'label' => $this->l('ClientID', self::TABNAME),
							'desc' => $this->l('ClientID is the 10-digit secret code in GoPay system, provided by GoPay company.', self::TABNAME),
							'class' => 'mw-200',
							'form_group_class' => 'full_mode '.(!Configuration::get($this->_M->MFIX.'_GATEWAY_MODE') ? 'hide_mode' : '').'',
							'maxlength' => 24,
							'required' => true,
							'lang' => false,
						),
						array(
							'type' => 'text',
							'name' => '_CLIENT_SECRET',
							'label' => $this->l('ClientSecret', self::TABNAME),
							'desc' => $this->l('ClientSecret is the 8-character secret code in GoPay system, provided by GoPay company.', self::TABNAME),
							'class' => 'mw-200',
							'form_group_class' => 'full_mode '.(!Configuration::get($this->_M->MFIX.'_GATEWAY_MODE') ? 'hide_mode' : '').'',
							'maxlength' => 24,
							'required' => true,
							'lang' => false,
						),
						array(
							'type' => 'text',
							'name' => '_GO_ID_TEST',
							'label' => $this->l('Test GoID', self::TABNAME),
							'desc' => $this->l('Test GoID is the 10-digit identifier of your eshop, provided by GoPay company.', self::TABNAME),
							'class' => 'mw-200',
							'form_group_class' => 'test_mode '.(Configuration::get($this->_M->MFIX.'_GATEWAY_MODE') ? 'hide_mode' : '').'',
							'maxlength' => 10,
							'required' => true,
							'lang' => false,
						),
						array(
							'type' => 'text',
							'name' => '_CLIENT_ID_TEST',
							'label' => $this->l('Test ClientID', self::TABNAME),
							'desc' => $this->l('Test ClientID is the 10-digit secret code in GoPay system, provided by GoPay company.', self::TABNAME),
							'class' => 'mw-200',
							'form_group_class' => 'test_mode '.(Configuration::get($this->_M->MFIX.'_GATEWAY_MODE') ? 'hide_mode' : '').'',
							'maxlength' => 24,
							'required' => true,
							'lang' => false,
						),
						array(
							'type' => 'text',
							'name' => '_CLIENT_SECRET_TEST',
							'label' => $this->l('Test ClientSecret', self::TABNAME),
							'desc' => $this->l('Test ClientSecret is the 8-character secret code in GoPay system, provided by GoPay company.', self::TABNAME),
							'class' => 'mw-200',
							'form_group_class' => 'test_mode '.(Configuration::get($this->_M->MFIX.'_GATEWAY_MODE') ? 'hide_mode' : '').'',
							'maxlength' => 24,
							'required' => true,
							'lang' => false,
						),
						array(
							'type' => 'button',
							'name' => '',
							'form_group_class' => 'hide_mode',
							'desc' => '<input type="button"  id="test_config" value="'.$this->l('Test Configuration', self::TABNAME).'" class="btn btn-default button_main">',
							'required' => false,
							'lang' => false,
						),
						array(
							'type' => 'html',
							'name' => '',
							'label' => ' ',
							'title' => '',
							'col' => 12,
							'form_group_class' => 'hide_mode',
							'html_content' => '<div class="alert alert-info" id="_CONFIG_INFO_TEST">'.$this->l('You need to save the configuration and then you can test the configuration.', self::TABNAME).'</div>'
						),
						array(
							'type' => 'html',
							'name' => '',
							'label' => ' ',
							'title' => '',
							'col' => 12,
							'form_group_class' => 'hide_mode',
							'html_content' => '<div class="alert alert-info" id="_CONFIG_INFO_DATA">'.$this->l('You must use the data provided by GoPay.', self::TABNAME).'</div>'
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
			'integration' => array(
				'form' => array(
					'legend' => array(
						'title' => $this->l('Integration with eshop', self::TABNAME),
						'icon' => 'icon-puzzle-piece'
					),
					'input' => array(
						array(
							'type' => 'switch',
							'label' => $this->l('Inline payment gateway', self::TABNAME),
							'name' => '_INLINE_MODE',
							'desc' => $this->l('Fast payments without redirection.', self::TABNAME).' '.$this->l('This option is available only if you have active', self::TABNAME).' <a href="'.Context::getContext()->link->getAdminLink('AdminPreferences').'" target="new"> '.$this->l('deployed SSL', self::TABNAME).'</a>',
							'disabled' => Configuration::get('PS_SSL_ENABLED') ? false : true,
							'values' => array(
								array(
									'id' => 'inline_on',
									'value' => 1,
									'label' => $this->l('Yes', self::TABNAME)
								),
								array(
									'id' => 'inline_off',
									'value' => 0,
									'label' => $this->l('No', self::TABNAME)
								)
							)
						),
						array(
							'type' => 'select',
							'name' => '_HOOK',
							'label' => $this->l('Show payment logo in hook', self::TABNAME),
							'desc' => '',
							'required' => false,
							'lang' => false,
							'options' => array(
								'query' => $hooks,
								'id' => 'id',
								'name' => 'name'
							)
						),
						array(
							'type' => 'text',
							'name' => '_ORDER_DESCRIPTION',
							'label' => $this->l('Payment description', self::TABNAME),
							'desc' => $this->l('Show your text to a customer in the order window on the site of payment gateway.', self::TABNAME),
							'maxlength' => 256,
							'class' => 'col-lg-9',
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
			),
		);

		return $fields_form;
	}

	public function getConfig($TABNAME, $configurations)
	{
		return parent::getConfig($TABNAME, $configurations);
	}

	protected function _validate()
	{
		$condition = array();
		if (Tools::isSubmit('submit_'.self::TABNAME))
		{
			Configuration::updateValue($this->_M->MFIX.'_GATEWAY_MODE', Tools::getValue('_GATEWAY_MODE'));

			if (Configuration::get($this->_M->MFIX.'_GATEWAY_MODE'))
			{
				if (strlen(Tools::getValue('_GO_ID')) != 10)
					$condition[] = $this->l('GoID must be a numeric value and string with length 10', self::TABNAME);
	
				if (strlen(Tools::getValue('_CLIENT_ID')) != 10)
					$condition[] = $this->l('ClientID must be a numeric value and string with length 10', self::TABNAME);
	
				if (strlen(Tools::getValue('_CLIENT_SECRET')) != 8)
					$condition[] = $this->l('ClientSecret code must be a string with length 8', self::TABNAME);
			} else
			{
				if (/*Validate::isInt(Tools::getValue('_GO_ID_TEST')) && */strlen(Tools::getValue('_GO_ID_TEST')) != 10)
					$condition[] = $this->l('Test GoID must be a numeric value and string with length 10', self::TABNAME);
	
				if (strlen(Tools::getValue('_CLIENT_ID_TEST')) != 10)
					$condition[] = $this->l('Test ClientID must be a numeric value and string with length 10', self::TABNAME);
	
				if (strlen(Tools::getValue('_CLIENT_SECRET_TEST')) != 8)
					$condition[] = $this->l('Test ClientSecret must be a string with length 8', self::TABNAME);
			}
		}

		if (count($condition) > 0)
			return implode('<br>', $condition);
	}
	
	public function getTestConfig(){
		$data = include_once(_PS_MODULE_DIR_.$this->_M->name.'/restapi/gopay_test.php');
		die(Tools::jsonEncode($data));
	}
	
}







