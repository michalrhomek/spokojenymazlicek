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
class Pms_GoPay_Extra_Core extends PaymentModule
{
	public $MFIX = 'PMS_GP_EX';
	public static $SFIX;

	const CODE_SUCCESS	 = 1;
	const CODE_ERROR	 = -1;
	const DELAY_TIME	 = 5000;
	const _NAME			 = 'moduleCore';
	const _CLASS_NAME	 = 'Pms_GoPay_Extra';

	public $_path;
	public $functions;
	public $module_dir;

	protected $_html		 = '';
	protected $errors		 = array();
	protected $warnings		 = array();
	protected $success;
	protected $params_back	 = array();
	protected $funcNotExist	 = false;

	private static $staticHooks = array(
		'displayBackOfficeHeader',
		'actionGetPmsModuleList',
		'actionAdminControllerSetMedia'
	);

	private static $staticMainMenuTabs = array(
		'questions' => '_Questions',
		'register' => '_Register',
		'catalog' => '_Catalog',
		'translations' => '_Translations'
	);

	/* module admin ajax tab */
	private static $staticAdminTabs = array(
		array(
			'class_name' => 'Admin',
			'parent' => -1,
			'name' => array(
				'en' => 'Pms_GoPay_Extra_Ajax'	/* 'en'  musí být vždy ! */
			)
		)
	);

	public function __construct()
	{
		self::$SFIX = $this->MFIX;
		parent::__construct();

		$this->module_dir = __PS_BASE_URI__.'modules/'.$this->name.'/';
		$this->_path = _PS_ROOT_DIR_.'/modules/'.$this->name.'/';

		$this->checkFunctions();
		if ($this->funcNotExist === false)
		{
			include_once(dirname(__FILE__).'/functions.php');
			$object = self::_CLASS_NAME.'_Functions';
			if(!is_object($object))
				$this->functions = new $object($this);
		}

		if(Tools::getValue('configure') == $this->name || Tools::getValue('module_name') == $this->name)
		{
			$this->setPaymentOptionStatus();
			$this->hookActionAdminControllerSetMedia();
		}
	}

	public function install()
	{
		if (Shop::isFeatureActive()) {
			Shop::setContext(Shop::CONTEXT_ALL);
		}

		if ($this->funcNotExist !== false
			|| !$this->installSettings(self::$staticMainMenuTabs)
			|| !parent::install()
			|| !$this->installModuleTab(self::$staticAdminTabs)
			|| !$this->registerHooks(self::$staticHooks)
			//|| !$this->functions->setData()
		)
			return false;

		// Install SQL
		$sql = array();
		include(dirname(__FILE__).'/../sql/db_install.php');
		foreach ($sql as $s) {
			try {
				Db::getInstance()->execute($s);
			} catch (PrestaShopDatabaseException $e) {
				//
			}
		}

		// Update SQL
		$sql = array();
		include(dirname(__FILE__).'/../sql/db_update.php');
		foreach ($sql as $s) {
			try {
				Db::getInstance()->execute($s);
			} catch (PrestaShopDatabaseException $e) {
				//
			}
		}

		// zvětší počet znaků pro string
		$find = "'size' => 32";
		$replace = "'size' => 254";
		$path = dirname(__FILE__)."/../../../classes/Configuration.php";
		$content = file_get_contents($path);
		$content_chunks = explode($find, $content);
		if(count($content_chunks) == 2){
			$content = implode($replace, $content_chunks);
			file_put_contents($path, $content);
		}

		return true;
	}

	public function uninstall()
	{
		// Uninstall SQL
		if (Configuration::get($this->MFIX.'_FORCE_UNINSTALL'))
		{
			$sql = array();
			include(dirname(__FILE__).'/../sql/db_uninstall.php');
			foreach ($sql as $s) {
				try {
					Db::getInstance()->execute($s);
				} catch (PrestaShopDatabaseException $e) {
					//
				}
			}
		}

		if (!$this->uninstallSettings(self::$staticMainMenuTabs)
			|| !parent::uninstall()
			|| !$this->uninstallModuleTab(self::$staticAdminTabs)
			|| !$this->cleanConfigurationTable()
			|| !Configuration::deleteByName($this->MFIX.'_GRADE')
			|| !Configuration::deleteByName($this->MFIX.'_DATE_ADD')
		)
			return false;

		//clear compile templates
		$this->context->smarty->clearCompiledTemplate();

		return true;
	}

	public function renderFormClass($fields_form, $fields_value, $table = null)
	{
		$helper = new HelperForm();
		$helper->module = $this;
		$helper->table = $table ? $table : count($fields_form) + count($fields_value);
		$helper->name_controller = $this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

		$lang = new Language(Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;

		$helper->title = $this->displayName;
		$helper->show_toolbar = true;
		$helper->toolbar_scroll = true;
		$helper->submit_action = 'submit_'.$this->name;

		$helper->tpl_vars = array(
				'fields_value' => $fields_value,
				'languages' => $this->context->controller->getLanguages(),
				'id_language' => $this->context->language->id
			);

		return $helper->generateForm($fields_form);
	}

	public function hookDisplayBackOfficeHeader($params)
	{
		if (Tools::getValue('configure') == $this->name)
		{
			if (isset($params['addJsDefs']) && is_array($params['addJsDefs']))
			{
				$this->context->smarty->assign($params);
			} else {
				$register = new DateTime(Configuration::get($this->MFIX.'_DATE_ADD'));
				$register->modify('+5 day');

				$this->context->smarty->assign(array(
					'addJsDefs' => array(
						'actions_controller_url' => $this->context->link->getAdminLink('Admin'.self::_CLASS_NAME),
						'full_language_code'	 => $this->context->language->language_code,
						'SUCCESS_CODE'			 => self::CODE_SUCCESS,
						'DELAY_TIME'			 => self::DELAY_TIME,
						'SHOW_REVIEW'			 => Configuration::get($this->MFIX.'_GRADE') < 1 && date('Y-m-d H:i:s') > $register->format('Y-m-d H:i:s') ? 1 : 0
					)
				));
			}

			return $this->display($this->name, '/views/templates/admin/set_media.tpl');
		}
	}

	public function hookActionAdminControllerSetMedia()
	{
		if (Tools::getValue('configure') == $this->name && Tools::getValue('controller') != 'Account_Pms_GoPay_Extra')
		{
			$this->context->controller->addJquery();

			$this->context->controller->addjqueryPlugin(array('idTabs'));
			$this->context->controller->addJS($this->module_dir.'views/js/admin/jquery.rating.pack.js');
			$this->context->controller->addCSS($this->module_dir.'_libraries/font-awesome/css/font-awesome.css', 'all');
			

			$this->context->controller->addJS($this->module_dir.'views/js/admin/menu.js');
			$this->context->controller->addCSS($this->module_dir.'views/css/admin/menu.css', 'all');

			$this->context->controller->addJS($this->module_dir.'views/js/admin/js.js');
			$this->context->controller->addCSS($this->module_dir.'views/css/admin/css.css', 'all');

			if (version_compare(_PS_VERSION_, '1.6', '<') === true)
			{
				$this->context->controller->addCSS($this->module_dir.'_libraries/css/admin-theme.css', 'all');
				$this->context->controller->addCSS($this->module_dir.'views/css/admin/css_15.css', 'all');
				$this->context->controller->addJS($this->module_dir.'_libraries/js/admin.js');
				$this->context->controller->addJS($this->module_dir.'_libraries/js/bootstrap.min.js');
				$this->context->controller->addJS($this->module_dir.'_libraries/js/tinymce.inc.js');
				$this->context->controller->addJS($this->module_dir.'_libraries/js/jquery.autosize.js');
				$this->context->controller->addCSS($this->module_dir.'_libraries/growl/jquery.growl.css', 'all');
				$this->context->controller->addJS($this->module_dir.'_libraries/growl/jquery.growl.js');
			}
		}
	}

	public function setPaymentOptionStatus()
	{
		// kompatibilita s 1.6 a 1.7 - pro 1.6 zakomentovat
		$path = dirname(__FILE__)."/../".$this->name.".php";
		$content = file_get_contents($path);
		$content_chunks = explode("//use PrestaShop\PrestaShop\Core\Payment\PaymentOption;", $content);
		if(count($content_chunks) == 2) { // pro 1.7
			if (version_compare(_PS_VERSION_, '1.7', '>=') === true) {
				$content = implode("use PrestaShop\PrestaShop\Core\Payment\PaymentOption;", $content_chunks);
				file_put_contents($path, $content);
			}
		} 
		else {
			if (version_compare(_PS_VERSION_, '1.7', '<') === true) { // pro 1.6
				$content_chunks = explode("use PrestaShop\PrestaShop\Core\Payment\PaymentOption;", $content);
				if(count($content_chunks) == 2){
					$content = implode("//use PrestaShop\PrestaShop\Core\Payment\PaymentOption;", $content_chunks);
					file_put_contents($path, $content);
				}
			}
		}
	}

	public function hookActionGetPmsModuleList($params)
	{
		return $this->functions->hookActionGetPmsModuleList($params);
	}

	protected function _displayInfos()
	{
		return $this->display($this->name, '/views/templates/admin/info.tpl');
	}

	public function getContent($adminTab = NULL)
	{
		$this->getStaticMainTabs();
		$staticTabs = self::$staticMainMenuTabs;

		if ($errors = $this->functions->_postProces())
			$this->_html .= $this->displayError($errors);

		if ($error = Tools::getValue('error'))
			$this->_html .= $this->displayError($error);

		foreach ($staticTabs as $staticTab)
			$this->_html .= $staticTab->postProces();

		$show_shop = true;
		$shop_context = Shop::getContext();

		if ((Shop::isFeatureActive() && $shop_context == Shop::CONTEXT_ALL)
			|| (Shop::isFeatureActive() && $shop_context == Shop::CONTEXT_GROUP)
		)
			$show_shop = false;

		if (!$show_shop)
			$this->_html .= $this->displayError($this->l('You must select the store for which you want to set up parameters.', self::_NAME));

		$this->context->smarty->assign(array(
			'name'			 => $this->name,
			'version'		 => $this->version,
			'displayName'	 => $this->displayName,
			'REGISTER'		 => $this->functions->isRegistered(),
			'idTab'			 => Tools::getValue('idTab'),
			'staticTabs'	 => $staticTabs,
			'comment_grade'	 => Configuration::get($this->MFIX.'_GRADE'),
			'SHOW_SHOP'		 => $show_shop,
			'adminTab'		 => $adminTab
		));

		$this->_html .= $this->_displayInfos();
		return $this->_html.$this->display($this->name, '/views/templates/admin/admin_main.tpl');
	}

	private function checkFunctions()
	{
		$errors = '';
		$functions = array('base64_decode', 'str_rot13', 'gzuncompress', 'gzinflate', 'curl_init', 'curl_setopt', 'curl_exec', 'curl_close');

		foreach ($functions as $f)
		{
			if (!$this->suhosin_function_exists($f))
				$errors .= '<br><span style="color: red">'.$this->l('PHP function is disabled! Enable it at your hosting: ', self::_NAME).'<b>'.$f.'</b></span>';
		}

		if ($errors)
		{
			$this->funcNotExist = true;
			$this->description .= $errors;
		}
	}

	private function suhosin_function_exists($func)
	{
		if (extension_loaded('suhosin'))
		{
			$suhosin = @ini_get("suhosin.executor.func.blacklist");
			$suhosin_eval = @ini_get("suhosin.executor.eval.blacklist");
			if (empty($suhosin) == false)
			{
				$suhosin = explode(',', $suhosin);
				$suhosin = array_map('trim', $suhosin);
				$suhosin = array_map('strtolower', $suhosin);
				return (function_exists($func) == true && array_search($func, $suhosin) === false);
			}
			elseif (empty($suhosin_eval) == false)
			{
				
				$suhosin_eval = explode(',', $suhosin_eval);
				$suhosin_eval = array_map('trim', $suhosin_eval);
				$suhosin_eval = array_map('strtolower', $suhosin_eval);
				return (function_exists($func) == true && array_search($func, $suhosin_eval) === false);
			}
		}

		return function_exists($func);
	}

	public function installModuleTab($menuTabs)
	{
		foreach ($menuTabs as $newTab)
		{
			$tab = new Tab();
			$tab->name = $newTab['name'];
			$tab->class_name = $newTab['class_name'].self::_CLASS_NAME;
			$tab->module = $this->name;
			$tab->id_parent = $newTab['parent'] != -1 ? Tab::getIdFromClassName($newTab['parent']) : -1;
			$tab->active = 1;
			$languages = Language::getLanguages(false);
			if (is_array($languages))
			{
				foreach ($languages as $language)
				{
					foreach ($newTab['name'] as $key=>$name)
					{
						$id_lang = Language::getIdByIso($key);
						if ($id_lang && $id_lang == $language['id_lang'])
							$tab->name[$language['id_lang']] = $name;
						else
							$tab->name[$language['id_lang']] = $newTab['name']['en'];
					}
				}
			}

			if (!$tab->add())
			{
				$this->_errors[] = sprintf($this->l('Failed to install hook %1$s', self::_NAME), '<b>'.$hook.'</b>');
				return false;
			}
		}

		return true;
	}

	public function uninstallModuleTab($delTabs)
	{
		$result = true;

		foreach ($delTabs as $delTab)
		{
			$idTab = Tab::getIdFromClassName($delTab['class_name'].self::_CLASS_NAME);

			if($idTab != 0)
			{
				$tab = new Tab($idTab);
				if (!$tab->delete())
				{
					$this->_errors[] = sprintf($this->l('Failed to delete admin tab %1$s', self::_NAME), '<b>'.$hook.'</b>');
					return false;
				}
			}
		}

		return true;
	}

	private function cleanConfigurationTable()
	{
		/*  dořešit  funkčnost  !!!!!!   */
		//if (Db::$tools()->Execute('DELETE FROM `'._DB_PREFIX_.'configuration` WHERE `name` LIKE \''.$this->MFIX.'%\''))
			return true;
	}
	
	public function registerHooks(array $hooks)
	{
		foreach($hooks as $hook)
			if(!$this->isRegisteredInHook($hook))
				if (!$this->registerHook($hook))
				{
					$this->_errors[] = sprintf($this->l('Failed to install hook %1$s', self::_NAME), '<b>'.$hook.'</b>');
					return false;
				}

		return true;
	}

	public function getStaticMainTabs()
	{
		foreach(self::$staticMainMenuTabs as $name => $class)
		{
			$class = self::_CLASS_NAME.$class;
			if(!is_object($class))
			{
				include_once($this->_path.'/classes/tabs/'.$name.'.php');
				self::$staticMainMenuTabs[$name] = new $class($this);
			}
		}
	}

	public function installSettings(array $settings)
	{
		foreach($settings as $name => $class)
		{
			if(!is_object($class))
			{
				$class = self::_CLASS_NAME.$class;
				include_once($this->_path.'/classes/tabs/'.$name.'.php');
				$class = new $class($this);
			}

			if (!$class->install(array()))
			{
				$this->_errors[] = sprintf($this->l('Failed to install tab %1$s', self::_NAME), '<b>'.$name.'</b>');
				return false;
			}
		}

		return true;
	}
	
	public function uninstallSettings(array $settings)
	{
		foreach($settings as $name => $class)
		{
			if(!is_object($class))
			{
				$class = self::_CLASS_NAME.$class;
				include_once($this->_path.'/classes/tabs/'.$name.'.php');
				$class = new $class($this);
			}

			if (!$class->uninstall(array()))
			{
				$this->_errors[] = sprintf($this->l('Failed to uninstall tab %1$s', self::_NAME), '<b>'.$name.'</b>');
				return false;
			}
		}

		return true;
	}

	/* ------------------------------------------------------------- */
	/*  CREATE NEW ORDER STATUSES
	/* ------------------------------------------------------------- */
	public function createOrderStatuses($statuses)
	{
		$result = true;

		foreach ($statuses as $status)
		{
			$status['icon'] = strtolower($status['name']);
			$status['name'] = $this->MFIX.$status['name'];
			$result &= $this->createStatus($status);
		}

		return $result;
	}

	private function createStatus($status)
	{
		if (Configuration::get($status['name']))
		{
			$order_state = new OrderState(Configuration::get($status['name']));
			if (Validate::isLoadedObject($order_state))
				return true;
		}

		return $this->addStatus($status);
	}

	private function addStatus($status)
	{
		$orderState = new OrderState();
		$orderState->name		 = array();
		$orderState->invoice	 = false;
		$orderState->send_email	 = $status['mail'];
		$orderState->module_name = $this->name;
		$orderState->unremovable = true;
		$orderState->hidden		 = false;
		$orderState->logable	 = false;
		$orderState->delivery	 = false;
		$orderState->shipped	 = false;
		$orderState->paid		 = false;
		$orderState->color		 = $status['color'];

		foreach (Language::getLanguages(false) as $language)
		{
			if (isset($status['mail']) && $status['mail'])
				$orderState->template[$language['id_lang']] = $this->MFIX.$status['icon'];

			if (strtolower($language['iso_code']) == 'cs')
				$orderState->name[$language['id_lang']] = $status['lang_cs'];
			else
				$orderState->name[$language['id_lang']] = $status['lang_en'];
		}

		if ($orderState->add())
		{
			if (!$this->copyIcon($orderState->id, $status['icon']))
			{
				$orderState->delete();
				$this->_errors[] = sprintf($this->l('Failed to copy icon file for %1$s', self::_NAME), '<b>'.$status['icon'].'</b>');
				return false;
			} else
			{
				if (isset($status['mail']) && $status['mail'])
				{
					if (!$this->copyMails($status['icon']))
					{
						$orderState->delete();
						$this->_errors[] = sprintf($this->l('Failed to copy mail files for %1$s', self::_NAME), '<b>'.$status['name'].'</b>');
						return false;
					} else
						return Configuration::updateValue($status['name'], (int)$orderState->id, false, false, false);
				}

				return Configuration::updateValue($status['name'], (int)$orderState->id, false, false, false);
			}
		} else {
			$this->_errors[] = sprintf($this->l('Failed to save order status %1$s', self::_NAME), '<b>'.$status['name'].'</b>');
			return false;
		}
	}

	private function copyIcon($status, $type)
	{
		$source = _PS_MODULE_DIR_.$this->name.'/views/img/payments/icons/'.$type.'.gif';
		$destination = _PS_IMG_DIR_.'os/'.$status.'.gif';

		return copy($source, $destination);
	}

	/* ------------------------------------------------------------- */
	/*  COPY MAIL FILES FOR STATUSES
	/* ------------------------------------------------------------- */
	public function copyMails($file)
	{
		if (!is_writable(_PS_ROOT_DIR_))
			die("Prestashop base dir is not writable. Please set permissions using: \"sudo chmod g+rw "._PS_ROOT_DIR_." -R\", if it does not help, use: \"sudo chmod o+rw "._PS_ROOT_DIR_." -R\". You can revoke these permissions by replacig \"+\" sign with \"-\"");

		foreach (Language::getLanguages() as $language)
		{
			$iso			 = strtolower($language['iso_code']);
			$source			 = _PS_MODULE_DIR_.$this->name.'/install_mails/'.$iso;
			$destination	 = _PS_MAIL_DIR_.$iso;

			if(!is_dir($source))
				$source = _PS_MODULE_DIR_.$this->name.'/install_mails/en';

			if(is_dir($destination))
				if (!$this->_copy_file($source, $destination, $file.'.html') || !$this->_copy_file($source, $destination, $file.'.txt'))
					return false;
		}

		return true;
	}

	private function _copy_file($source, $destination, $file)
	{
		$destination = $destination.'/'.$this->MFIX.$file;
		$source = $source.'/'.$file;
		$dH = 0;
		$dB = 0;
		if(file_exists($destination))
			$dH = filesize($destination);

		if(file_exists($source))
			$dH = filesize($source);

		if ($dH != $dB)
		{
			if(file_exists($destination))
				if($dH > 100000)
					if(!rename($destination, $destination.".opbak"))
						return false;

			if (!copy($source, $destination))
				return false;
		}

		return true;
	}

	/* ------------------------------------------------------------- */
	/*  DELETE ORDER STATUSES
	/* ------------------------------------------------------------- */
	public function deleteStatuses($statuses)
	{
		$result = true;
		foreach ($statuses as $status)
		{
			$result &= $this->deleteStatus($this->MFIX.$status['name']);
		}

		return $result;
	}

	private function deleteStatus($name)
	{
		if (Configuration::get($name))
		{
			$orderState = new OrderState(Configuration::get($name));

			if ($orderState && (!$orderState->delete() || !Configuration::deleteByName($name)))
				return false;
		}

		return true;
	}

	/* ------------------------------------------------------------- */
	/*  DELETE MAIL FILES FOR STATUSES
	/* ------------------------------------------------------------- */
	public function deleteMails($statuses)
	{
		$result = true;
		foreach ($statuses as $status)
		{
			foreach (Language::getLanguages() as $language)
			{
				$iso			 = strtolower($language['iso_code']);
				$destination	 = _PS_MAIL_DIR_.$iso;
				$result &= $this->_remove_file($destination, $this->MFIX.strtolower($status['name']).'.html');
				$result &= $this->_remove_file($destination, $this->MFIX.strtolower($status['name']).'.txt');
			}
		}

		return $result;
	}

	private function _remove_file($destination, $file)
	{
		$destination = $destination.'/'.$file;

		if (file_exists($destination) && !unlink($destination))
			return false;

		if (file_exists($destination.".opbak"))
			if (!rename($destination.".opbak", $destination))
				return false;

		return true;
	}

	public function getReviews()
	{
		$this->context->smarty->assign(array(
			'id_product_comment_form' => $this->name,
			'shopURL' => Configuration::get('PS_SHOP_NAME')
		));

		return $this->display($this->name, '/views/templates/admin/productcomments.tpl');
	}

	public function addNewReview()
	{
		$context = Context::getContext();
		$notAnymore = Tools::getValue('notAnymore');
		$protocols = array('http://', 'https://');
		$domains = array('prestamoduleshop.com', 'dev.prestamoduleshop.com');

		$ch = curl_init();
		foreach ($protocols as $http)
		{
			foreach ($domains as $domain)
			{
				$posts = Tools::getValue('posts');
				$url_store = version_compare(_PS_VERSION_, '1.5') >= 0 ? $context->shop->getBaseURL() : Tools::getShopDomain(true).__PS_BASE_URI__;
				curl_setopt($ch, CURLOPT_URL, $http.$domain.'/_checkFrame.php');
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, ($notAnymore ? 'addNotAnymore' : $posts.'&'.'addNewReview').
														'&moduleName='.$this->name.
														'&moduleVersion='.$this->version.
														'&psVersion='._PS_VERSION_.
														'&shopURL='.$url_store.
														'&employe='.$this->context->employee->email);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$output = curl_exec($ch);

				$response = curl_getinfo($ch);
				if ($response['http_code'] == 200)
				{
					$status = Tools::jsonDecode($output);
					if(isset($status->result->criterion) && $status->result->criterion)
					{
						Configuration::updateValue($this->MFIX.'_GRADE', $status->result->criterion);

						return array(
							'message_code' => self::CODE_SUCCESS,
							'message' => $frame->l('Thank you for your review', self::_NAME),
						);
					}
					elseif(isset($status->notAnymore) && $status->notAnymore)
					{
						Configuration::updateValue($this->MFIX.'_GRADE', 10);
						return array(
							'message_code' => self::CODE_SUCCESS,
							'message' => $this->l('Thank you for using our module. The window with rating will be no longer displayed.', self::_NAME)
						);
					} 
					elseif(isset($status->errors) && $status->errors)
					{
						die ($output);
					} else
						return array(
							'message_code' => self::CODE_ERROR,
							'message' => $this->l('Not specified error', self::_NAME)
						);

				} else
					return array(
						'message_code' => self::CODE_ERROR,
						'message' => serialize($response)
					);
			}
		}

		return array(
			'message_code' => self::CODE_ERROR,
			'message' => $this->l('Error with connecting to server', self::_NAME)
		);
	}

	public static function xmlToArray(SimpleXMLElement $xml)
	{
		$parser = function (SimpleXMLElement $xml, array $collection = array()) use (&$parser)
		{
			$nodes = $xml->children();
			$attributes = $xml->attributes();

			if (0 !== count($attributes))
			{
				foreach ($attributes as $attrName => $attrValue)
				{
					$collection['@attributes'][$attrName] = strval($attrValue);
				}
			}

			if (0 === $nodes->count())
			{
				if($xml->attributes())
				{
					$collection['value'] = strval($xml);
				}
				else
				{
					$collection = strval($xml);
				}
				return $collection;
			}

			foreach ($nodes as $nodeName => $nodeValue)
			{
				if (count($nodeValue->xpath('../' . $nodeName)) < 2)
				{
					$collection[$nodeName] = $parser($nodeValue);
					continue;
				}

				$collection[$nodeName][] = $parser($nodeValue);
			}

			return $collection;
		};

		return array($xml->getName() => $parser($xml));
	}
}
