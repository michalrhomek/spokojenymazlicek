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

class Pms_GoPay_Extra_Catalog extends Pms_GoPay_Extra_ModuleConfig
{
	const TABNAME = 'catalog';

	/* array('name' => '', 'default' => '', 'isHTML' => false/true, 'isBool' => false/true, 'isLang' => false/true, 'isArray' => false/true) */
	public $configurations = array(
	);

	public function init()
	{
		$this->context->controller->addJs($this->_M->module_dir.'views/js/admin/tabs/'.self::TABNAME.'.js');
		$this->context->controller->addCss($this->_M->module_dir.'views/css/admin/tabs/'.self::TABNAME.'.css');
	}

	public function getTitle()
	{
		return $this->l('PMS Modules Catalog', self::TABNAME);
	}

	public function getIcon()
	{
		return 'icon-AdminParentModules';
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
		return $this->_M->renderFormClass(array($this->getForm()), $this->getConfig(self::TABNAME, $this->configurations), self::TABNAME);
	}

	private function getForm()
	{
		$fields_form = array(
				'form' => array(
					'legend' => array(
						'title' => $this->l('Modules catalog', self::TABNAME),
						'icon' => $this->getIcon()
					),
					'input' => array(
						array(
							'type' => 'hidden',
							'name' => 'idTab'
						),
						array(
							'type' => 'html',
							'name' => '',
							'label' => ' ',
							'title' => '',
							'col' => 12,
							'html_content' => $this->getHTML()
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

	private function getHTML()
	{
		return '<div id="catalog-content"></div>';
	}

	public function getModulesCatalog()
	{
		$installedModules = array();
		foreach (Module::getModulesInstalled() as $module)
		{
			if (strpos($module['name'], "pms_") !== false)
				$installedModules[] = $module['name'];
		}

		$parent_domain = Tools::getHttpHost(true).substr($_SERVER['REQUEST_URI'], 0, -1 * strlen(basename($_SERVER['REQUEST_URI'])));
		$iso_lang = $this->context->language->iso_code;
		$currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));

		$iframe_url = 'https://prestamoduleshop.com/_checkFrame.php?psVersion='._PS_VERSION_.'&moduleName='.$this->_M->name.'&installedModules='.implode(",", $installedModules).'&moduleVersion='.$this->_M->version.'&isoLang='.$iso_lang.'&isoCurrency='.$currency->iso_code.'&parentUrl='.$parent_domain.'&getModulesFrame';

		die(Tools::file_get_contents($iframe_url));
	}

	protected function _validate()
	{
	}
}
