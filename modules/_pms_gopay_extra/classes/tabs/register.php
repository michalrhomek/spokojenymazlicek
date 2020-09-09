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

class Pms_GoPay_Extra_Register extends Pms_GoPay_Extra_ModuleConfig
{
	const TABNAME = 'register';

	/* array('name' => '', 'default' => '', 'isHTML' => false/true, 'isBool' => false/true, 'isLang' => false/true, 'isArray' => false/true) */
	public $configurations = array(
		array('name' => '_REGISTER', 'default' => ''),
		array('name' => '_FORCE_UNINSTALL', 'default' => 0, 'isBool' => true)
	);

	public function init()
	{
	}

	public function getTitle()
	{
		return $this->l('License and Installation', self::TABNAME);
	}

	public function getIcon()
	{
		return 'icon-copyright';
	}

	public function install($configurations = array())
	{
		return parent::install($this->configurations);
	}

	public function uninstall($configurations = array())
	{
		if (!$this->_M->functions->deleteRegister()
			|| !parent::uninstall($this->configurations)
		)
			return false;

		return true;
	}

	public function postProces($TABNAME = null, $configurations = array())
	{
		return parent::postProces(self::TABNAME, array($this->configurations[1]));
	}

	public function showForm()
	{
		return $this->_M->renderFormClass($this->getForm(), $this->getConfig(self::TABNAME, $this->configurations));
	}

	private function getForm()
	{
		$fields_form = array(
			'register' => array(
				'form' => array(
					'legend' => array(
						'title' => $this->l('Register this module', self::TABNAME),
						'icon' => $this->getIcon()
					),
					'input' => array(
								array(
									'type' => 'html',
									'name' => '',
									'title' => '',
									'col' => 12,
									'html_content' => $this->getLicenceStatus()
								),
								array(
									'type' => 'text',
									'label' => $this->l('License number', self::TABNAME),
									'desc' => $this->l('Use form to register this modul.', self::TABNAME),
									'class' => 'register-width',
									'name' => '_REGISTER'
								),
								array(
									'type' => 'hidden',
									'name' => 'idTab'
								),
					),
					'submit' => array(
									'title' => $this->l('Validate', self::TABNAME),
									'name' => 'submitRegister',
								)
					),
			),
			
			'uninstall' => array(
				'form' => array(
					'legend' => array(
						'title' => $this->l('Module uninstall', self::TABNAME),
						'icon' => 'icon-trash'
					),
					'input' => array(
								array(
									'type' => 'switch',
									'label' => $this->l('When uninstalling, delete all data', self::TABNAME),
									'name' => '_FORCE_UNINSTALL',
									'desc' => $this->l('If you want to remove all data, including tables and settings', self::TABNAME),
									'required' => false,
									'is_bool' => true,
									'values' => array(
										array(
											'id' => 'force_uninstall_on',
											'value' => 1,
											'label' => $this->l('Yes', self::TABNAME)
										),
										array(
											'id' => 'force_uninstall_off',
											'value' => 0,
											'label' => $this->l('No', self::TABNAME)
										)
									)
								),
								array(
									'type' => 'hidden',
									'name' => 'idTab'
								),
					),
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

	private function getLicenceStatus()
	{
		if ($this->_M->functions->isRegistered())
			return '<div class="isLicenced"><i class="icon-check-square"></i> '.$this->l('The product is licensed', self::TABNAME).'</div>';
		else
			return '<div class="notLicenced"><i class="icon-exclamation-triangle"></i> '.$this->l('The product is not licensed', self::TABNAME).'</div>';
	}

	protected function _validate()
	{
	}
}
