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
if (!defined('_PS_DEFAULT_THEME_NAME_'))
	define('_PS_DEFAULT_THEME_NAME_', 'default');

include_once(dirname(__FILE__).'/moduleconfig.php');
include_once(dirname(__FILE__).'/../../controllers/admin/tabs/TranslationsPms_GoPay_ExtraController.php');

class Pms_GoPay_Extra_Translations extends Pms_GoPay_Extra_ModuleConfig
{
	const TABNAME = 'translations';

	private $controllerClass = 'TranslationsPms_GoPay_ExtraController';

	private $Translations;
	private $translation_dir;
	public $JqueryPlugin = array();

	/* array('name' => '', 'default' => '', 'isHTML' => false/true, 'isBool' => false/true, 'isLang' => false/true, 'isArray' => false/true) */
	public $configurations = array(
		//array('name' => '_TRANS_LANG', 'default' => ''),
	);

	public function init()
	{
		$this->context->controller->addJs($this->_M->module_dir.'views/js/admin/tabs/'.self::TABNAME.'.js');
		$this->context->controller->addCss($this->_M->module_dir.'views/css/admin/tabs/'.self::TABNAME.'.css');

		$this->Translations = new $this->controllerClass($this->_M);
		$this->translation_dir = _PS_MODULE_DIR_.$this->_M->name.'/'.self::TABNAME.'/';
	}

	public function getTitle()
	{
		return $this->l('Translations', self::TABNAME);
	}

	public function getIcon()
	{
		return 'icon-AdminParentLocalization';
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
			$this->_M->name.'-general_0' => array(
				'form' => array(
					'legend' => array(
						'title' => $this->l('Translations', self::TABNAME),
						'icon' => $this->getIcon()
					),
					'input' => array(
						array(
							'type' => 'hidden',
							'name' => 'idTab'
						),
						array(
							'type' => 'select',
							'label' => $this->l('Select language', self::TABNAME),
							'desc' => '',
							'name' => '_TRANS_LANG',
							'onchange' => 'this.form.submit();',
							'options' => array(
									'query' => Language::getLanguages(false),
									'id' => 'iso_code',
									'name' => 'name'
							)
						)
					)
				)
			),
			$this->_M->name.'-general_1' => array(
				'form' => array(
					'legend' => array(
						'title' => '',
						'icon' => ''
					),
					'input' => array(
						array(
							'type' => 'html',
									'name' => '',
							'title' => '',
							'col' => 12,
							'html_content' => $this->Translations->initFormModules()
						)
					)
				)
			)
		);
		
		return $fields_form;
	}

	public function getConfig($TABNAME, $configurations)
	{
		$fields =  parent::getConfig($TABNAME, $configurations);
		$fields['_TRANS_LANG'] = Tools::getValue('_TRANS_LANG', $this->context->language->iso_code);

		return $fields;
	}

	public function saveTranslations()
	{
		if ($return_trans = $this->Translations->postProcess())
			return array(
				'data' => $return_trans,
				'message_code' => Pms_GoPay_Extra::CODE_SUCCESS,
				'message' => $this->l('The translations have been successfully saved', self::TABNAME)
			);
		else
			return array(
				'message_code' => Pms_GoPay_Extra::CODE_ERROR,
				'message' => $this->l('An error has occurred while attempting to save the translations', self::TABNAME)
			);
	}

	public function shareTranslation()
	{
		$iso_code = Tools::getValue('iso_code');
		$file_name = basename($this->translation_dir.'/'.$iso_code.'.php');
		$file_path = $this->translation_dir.'/'.$file_name;

		if (file_exists($file_path))
		{
			$file_attachment = array();
			$file_attachment['content'] = Tools::file_get_contents($file_path);
			$file_attachment['name'] = $iso_code.'.php';
			$file_attachment['mime'] = 'application/octet-stream';

			$sql = 'SELECT id_lang FROM '._DB_PREFIX_.'lang WHERE iso_code = "en"';
			$id_lang = DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);

			if (empty($id_lang)) {
				$id_lang = $this->context->language->id;
			}

			$data = Mail::Send(
				$id_lang,
				'test',
				' - '.$iso_code.' - '.strtoupper($this->_M->name).' -- '.$_SERVER['SERVER_NAME'].' -- '.$this->l('he shared a translation with you', self::TABNAME),
				array(),
				'info@prestamoduleshop.com',
				null,
				null,
				null,
				$file_attachment,
				null,
				_PS_MAIL_DIR_,
				null,
				$this->context->shop->id
			);

			if ($data)
				return array(
					'message_code' => Pms_GoPay_Extra::CODE_SUCCESS,
					'message' => $this->l('Translation has been sent, thank you for your support.')
				);
		}

		return array(
			'message_code' => Pms_GoPay_Extra::CODE_ERROR,
			'message' => $this->l('An error has occurred to attempt to send the translation.')
		);
	}

	protected function _validate()
	{
	}
}
