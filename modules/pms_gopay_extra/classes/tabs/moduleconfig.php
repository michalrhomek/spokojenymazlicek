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
abstract class Pms_GoPay_Extra_ModuleConfig
{
	public $_M;

	abstract public function init();
	abstract public function getIcon();
	abstract public function getTitle();
	abstract public function showForm();
	abstract protected function _validate();

	public function __construct(Module $module = null)
	{
		if(!is_null($module))
		{
			$this->_M = $module;
		} else
			$this->_M = new Pms_GoPay_Extra();

		$this->context = Context::getContext();
		$this->currentUrl = $this->context->link->getAdminLink('AdminModules').'&configure='.$this->_M->name;

		$this->init();
	}

	public function install($configurations)
	{
		$result = true;

		if (is_array($configurations) && count($configurations) > 0)
		{
			foreach ($configurations as $config)
			{
				if (isset($config['name']) && $config['name'])
				{
					$value = isset($config['default']) ? $config['default'] : '';
					$value = isset($config['ps_default']) && $config['ps_default'] ? Configuration::get($config['ps_default']) : $value;
					$isHTML = isset($config['isHTML']) && $config['isHTML'] ? true : false;

					if (isset($config['isBool']) && $config['isBool'])
						$value = (bool)$value;
					elseif (isset($config['isArray']) && $config['isArray'])
					{
						if (is_array($value))
							$value = serialize($value);
						else
							$value = serialize(array());
					}
					elseif (isset($config['isLang']) && $config['isLang'])
					{
						$new_value = array();
						$languages = Language::getLanguages(false);
						foreach ($languages as $language)
						{
							$id_lang = $language['id_lang'];
							$new_value[$id_lang] = isset($value[$language['iso_code']]) ? $value[$language['iso_code']] : '';
						}

						$value = $new_value;
					}

					$result &= (bool)Configuration::updateValue($this->_M->MFIX.$config['name'], $value, $isHTML);
				}
			}
		}

		return $result;
	}

	public function uninstall($configurations)
	{
		if (is_array($configurations) && count($configurations) > 0)
			foreach ($configurations as $config)
				if (isset($config['name']) && $config['name'])
					if (!Configuration::deleteByName($this->_M->MFIX.$config['name']))
						return false;

		return true;
	}

	public function postProces($TABNAME, $configurations)
	{
		if ($error = $this->_validate())
			return $this->_M->displayError($error);

		if (Tools::isSubmit('submit_'.$TABNAME))
		{
			/* pokud se předávají hodnoty lang  musí být zasílána hodnota Tools::getValue($config['name']) ve tvaru [id_lang] => hodnota  */
			$result = true;
			foreach ($configurations as $config)
			{
				if (isset($config['name']) && $config['name'])
				{
					$value = Tools::getValue($config['name']);
					$isHTML = isset($config['isHTML']) && $config['isHTML'] ? true : false;

					if (isset($config['isBool']) && $config['isBool'])
						$value = (bool)$value;
					elseif (isset($config['isArray']) && $config['isArray'])
					{
						if (is_array($value))
							$value = serialize($value);
						else
							$value = serialize(array());
					}
					elseif (isset($config['isLang']) && $config['isLang'])
					{
						$languages = Language::getLanguages(false);
						foreach ($languages as $language)
						{
							$id_lang = $language['id_lang'];
							$value[$id_lang] = Tools::getValue($config['name'].'_'.$id_lang);
						}
					}

					$result &= Configuration::updateValue($this->_M->MFIX.$config['name'], $value, $isHTML);
				}

			}

			if (!$result)
				return $this->_M->displayError($this->l('Error update config settings', $TABNAME));

			return Tools::redirectAdmin($this->currentUrl.'&idTab='.$TABNAME.'&conf=6');
		}
	}

	public function getConfig($TABNAME, $configurations)
	{
		$fields['idTab'] = $TABNAME;
		$languages = Language::getLanguages(false);

		if (is_array($configurations) && count($configurations) > 0)
		{
			foreach ($configurations as $config)
			{
				if (isset($config['name']) && $config['name'])
				{
					$value = Tools::getValue($config['name'], Configuration::get($this->_M->MFIX.$config['name']));

					if (isset($config['isBool']) && $config['isBool'])
						$value = (bool)$value;
					elseif (isset($config['isArray']) && $config['isArray'])
						$value = unserialize($value);

					if (isset($config['isLang']) && $config['isLang'])
					{
						foreach ($languages as $language)
						{
							$id_lang = $language['id_lang'];
							$fields[$config['name']][$id_lang] = Tools::getValue($config['name'].'_'.$id_lang, Configuration::get($this->_M->MFIX.$config['name'], $id_lang));
						}
					} else
						$fields[$config['name']] = $value;
				}
			}
		}

		return $fields;
	}

	public function initPageHeaderToolbar()
	{
		$this->context->smarty->assign(array(
			'toolbar_btn' => $this->toolbar_btn
		));

		return $this->_M->display($this->_M->name, '/views/templates/admin/header_toolbar.tpl');
	}

	protected function l($string, $source = null)
	{
		if(is_object($this->_M))
		{
			if(is_null($source))
			{
				$source = Tools::strtolower(get_class($this));
			}
				
			return $this->_M->l($string, $source);
		}

		return $string;
	}

	protected function getOrderSettings($id_order, $setting = false)
	{
		if(is_object($this->_M))
		{
			return $this->_M->getOrderSettings($id_order, $setting);
		}

		return ;
	}
}
