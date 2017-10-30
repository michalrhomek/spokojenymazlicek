<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @category  PrestaShop Module
 * @author    knowband.com <support@knowband.com>
 * @copyright 2015 Knowband
 * @license   see file: LICENSE.txt
 * 
 * Description
 * Allow admin to configure module settings for shop.
*/

if (!defined('_PS_VERSION_'))
	exit;

include_once dirname(__FILE__).'/classes/supercheckout_configuration.php';

class Supercheckout extends Module
{
	private $supercheckout_settings = array();
	public $submit_action = 'submit';
	private $custom_errors = array();

	public function __construct()
	{
		$this->name = 'supercheckout';
		$this->tab = 'checkout';
		$this->version = '3.0.4';
		$this->author = 'Knowband';
		$this->need_instance = 0;
		$this->module_key = '68a34cdd0bc05f6305874ea844eefa05';
//		$this->ps_versions_compliancy = array('min' => '1.5.0.1', 'max' <= _PS_VERSION_);
		$this->bootstrap = true;

		parent::__construct();

		$this->displayName = $this->l('SuperCheckout');
		$this->description = $this->l('One page Super checkout');

		$this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

		if (!class_exists('MailChimp'))
			include_once dirname(__FILE__).'/libraries/mailchimpl library.php';
	}

	public function getErrors()
	{
		return $this->custom_errors;
	}

	public function install()
	{
		if (Shop::isFeatureActive())
			Shop::setContext(Shop::CONTEXT_ALL);

		if (!parent::install()
			|| !$this->registerHook('displayOrderConfirmation')
			|| !$this->registerHook('displayHeader'))
			return false;

		$create_table = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'velsof_supercheckout_translation` (
            `id_field` int(10) NOT NULL auto_increment,
            `id_lang` int(10) NOT NULL,
            `iso_code` char(4) NOT NULL,
            `key` varchar(255) NOT NULL,
            `key_variable` Text NOT NULL,
            `description` Text NULL,
            PRIMARY KEY (`id_field`),
            INDEX (  `id_lang` )
            ) CHARACTER SET utf8 COLLATE utf8_general_ci';

		Db::getInstance()->execute($create_table);

		$previous_data = array();
		$check_query = 'SELECT * FROM `'._DB_PREFIX_.'velsof_supercheckout_translation`';
		$previous_data = Db::getInstance()->executeS($check_query);
		if (empty($previous_data))
		{
			$languages = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'lang`');
			foreach ($languages as $lang)
			{
				$iso_code = 'en';
				if (file_exists(dirname(__FILE__).'/translations/translation_sql/'.$lang['iso_code'].'.sql'))
					$iso_code = $lang['iso_code'];

$languages = Db::getInstance()->execute('delete FROM `'._DB_PREFIX_.'velsof_supercheckout_translation` where id_lang = '.(int)$lang['id_lang']);
				$sql = Tools::file_get_contents(dirname(__FILE__).'/translations/translation_sql/'.$iso_code.'.sql');
				$sql = str_replace('PREFIX_', _DB_PREFIX_, $sql);
				$sql = str_replace('ID_LANG', $lang['id_lang'], $sql);
				$sql = str_replace('ISO_CODE', $lang['iso_code'], $sql);

				$sql = preg_split("/[\r\n]+/", $sql);
				array_pop($sql);
				$total_query = count($sql);
				for ($i = 1; $i < $total_query; $i++)
				{
					$ins_query = trim($sql[0].$sql[$i], ',');
					Db::getInstance()->execute(trim($ins_query, ';'));
				}
			}
		}

		if (Configuration::get('VELOCITY_SUPERCHECKOUT'))
			Configuration::deleteByName('VELOCITY_SUPERCHECKOUT');

		if (Configuration::get('VELOCITY_SUPERCHECKOUT_HEADFOOTHTML'))
		{
			$data = unserialize((Configuration::get('VELOCITY_SUPERCHECKOUT_HEADFOOTHTML')));
			Configuration::updateValue('VELOCITY_SUPERCHECKOUT_HFHTML', serialize($data));
			Configuration::deleteByName('VELOCITY_SUPERCHECKOUT_HEADFOOTHTML');
		}

		if (Configuration::get('VELOCITY_SUPERCHECKOUT_CUSTOMBUTTON'))
		{
			$data = unserialize((Configuration::get('VELOCITY_SUPERCHECKOUT_CUSTOMBUTTON')));
			Configuration::updateValue('VELOCITY_SUPERCHECKOUT_BUTTON', serialize($data));
			Configuration::deleteByName('VELOCITY_SUPERCHECKOUT_CUSTOMBUTTON');
		}

		if (Configuration::get('VELOCITY_SUPERCHECKOUT_CUSTOMCSS'))
		{
			$data = unserialize((Configuration::get('VELOCITY_SUPERCHECKOUT_CUSTOMCSS')));
			$data = urlencode($data);
			Configuration::updateValue('VELOCITY_SUPERCHECKOUT_CSS', serialize($data));
			Configuration::deleteByName('VELOCITY_SUPERCHECKOUT_CUSTOMCSS');
		}

		if (Configuration::get('VELOCITY_SUPERCHECKOUT_CUSTOMJS'))
		{
			$data = unserialize((Configuration::get('VELOCITY_SUPERCHECKOUT_CUSTOMJS')));
			$data = urlencode($data);
			Configuration::updateValue('VELOCITY_SUPERCHECKOUT_JS', serialize($data));
			Configuration::deleteByName('VELOCITY_SUPERCHECKOUT_CUSTOMJS');
		}

		return true;
	}

	public function uninstall()
	{
		if (!parent::uninstall()
			|| !Configuration::deleteByName('VELOCITY_SUPERCHECKOUT')
			|| !$this->unregisterHook('displayOrderConfirmation')
			|| !$this->unregisterHook('displayHeader'))
			return false;

		if (Configuration::get('VELOCITY_SUPERCHECKOUT_ANALYTIC'))
		{
			$analyticdata = unserialize((Configuration::get('VELOCITY_SUPERCHECKOUT_ANALYTIC')));
			$analyticdata['enable'] = 0;
			Configuration::updateValue('VELOCITY_SUPERCHECKOUT_ANALYTIC', serialize($analyticdata));
		}

		return true;
	}

	public function getContent()
	{
		ini_set('max_input_vars', 2000);
		if (Tools::isSubmit('ajax'))
		{
			if (Tools::isSubmit('tranlationType'))
			{
				switch (Tools::getValue('tranlationType'))
				{
					case 'save':
						{
							$this->saveTranslation();
						}
					case 'saveDownload':
						{
							$this->saveTranslation();
						}
					case 'download':
						{
							$this->generateTmpLanguageFile();
						}
				}
			}
			else if (Tools::isSubmit('method'))
			{
				switch (Tools::getValue('method'))
				{
					case 'validation':
						{
							$this->ajaxHandler();
						}
					case 'getMailChimpList':
						{
							$this->getMailchimpLists(trim(Tools::getValue('key')));
						}
					case 'removeFile':
					{
						$this->removeFile(trim(Tools::getValue('id')));
					}
				}
			}
		}
		else if (Tools::isSubmit('downloadTranslation') && Tools::getValue('downloadTranslation') != '')
		{
			if (Tools::isSubmit('translationTmp'))
				$this->downloadTranslation(Tools::getValue('downloadTranslation'), true);
			else
				$this->downloadTranslation(Tools::getValue('downloadTranslation'));
		}

		$this->addBackOfficeMedia();

		$browser = ($_SERVER['HTTP_USER_AGENT']);
		$is_ie7 = false;
		if (preg_match('/(?i)msie [1-7]/', $browser))
			$is_ie7 = true;

		$output = null;

		$supercheckout_config = new SupercheckoutConfiguration();

		if (Tools::isSubmit($this->submit_action.$this->name))
		{
			$post_data = $supercheckout_config->processPostData(Tools::getValue('velocity_supercheckout'));
			$temp_default = $supercheckout_config->getDefaultSettings();
			$post_data['plugin_id'] = $temp_default['plugin_id'];
			$post_data['version'] = $temp_default['version'];

			$post_data['fb_login']['app_id'] = trim($post_data['fb_login']['app_id']);
			$post_data['fb_login']['app_secret'] = trim($post_data['fb_login']['app_secret']);

			$post_data['google_login']['client_id'] = trim($post_data['google_login']['client_id']);
			$post_data['google_login']['app_secret'] = trim($post_data['google_login']['app_secret']);
			$key_persist_setting = array(
				'fb_login' => array(
				'app_id' => $post_data['fb_login']['app_id'],
				'app_secret' => $post_data['fb_login']['app_secret']
				),
				'google_login' => array(
				'client_id' => $post_data['google_login']['client_id'],
				'app_secret' => $post_data['google_login']['app_secret'],
				),
				'mailchimp' => array(
				'api' => $post_data['mailchimp']['api'],
				'list' => $post_data['mailchimp']['list'],
				)
			);

			if (isset($post_data['enable_guest_checkout']) && $post_data['enable_guest_checkout'] == 1)
				Configuration::updateGlobalValue('PS_GUEST_CHECKOUT_ENABLED', '1');

			Configuration::updateValue('VELOCITY_SUPERCHECKOUT_KEYS', serialize($key_persist_setting));
			$post_data['custom_css'] = urlencode($post_data['custom_css']);
			$post_data['custom_js'] = urlencode($post_data['custom_js']);
			Configuration::updateValue('VELOCITY_SUPERCHECKOUT', serialize($post_data));
			Configuration::updateValue('VELOCITY_SUPERCHECKOUT_CSS', serialize($post_data['custom_css']));
			Configuration::updateValue('VELOCITY_SUPERCHECKOUT_JS', serialize($post_data['custom_js']));
			Configuration::updateValue('VELOCITY_SUPERCHECKOUT_BUTTON', serialize($post_data['customizer']));
			Configuration::updateValue('VELOCITY_SUPERCHECKOUT_HFHTML', serialize($post_data['html_value']));
			Configuration::updateValue('VELOCITY_SUPERCHECKOUT_EXTRAHTML', serialize($post_data['design']['html']));
			if (count($this->custom_errors) > 0)
				$output .= $this->displayError(implode('<br>', $this->custom_errors));
			else
				$output .= $this->displayConfirmation($this->l('Settings has been updated successfully'));
			$payment_post_data = (Tools::getValue('velocity_supercheckout_payment'));
//			d($payment_post_data);
			$payment_error = '';
			foreach (PaymentModule::getInstalledPaymentModules() as $paymethod)
			{

				$id = $paymethod['id_module'];
				if ($_FILES['velocity_supercheckout_payment']['size']['payment_method'][$id]['logo']['name'] == 0)
					$payment_post_data['payment_method'][$id]['logo']['title'] == '';
				else
				{
					$allowed_exts = array('gif', 'jpeg', 'jpg', 'png', 'JPG', 'PNG', 'GIF', 'JPEG');
					$extension = explode('.', $_FILES['velocity_supercheckout_payment']['name']['payment_method'][$id]['logo']['name']);
					$extension = end($extension);
					$extension = trim($extension);
					if ((($_FILES['velocity_supercheckout_payment']['type']['payment_method'][$id]['logo']['name'] == 'image/jpg')
						|| ($_FILES['velocity_supercheckout_payment']['type']['payment_method'][$id]['logo']['name'] == 'image/jpeg')
						|| ($_FILES['velocity_supercheckout_payment']['type']['payment_method'][$id]['logo']['name'] == 'image/gif')
						|| ($_FILES['velocity_supercheckout_payment']['type']['payment_method'][$id]['logo']['name'] == 'image/png'))
						&& ($_FILES['velocity_supercheckout_payment']['size']['payment_method'][$id]['logo']['name'] < 300000)
						&& in_array($extension, $allowed_exts))
					{
						if ($_FILES['velocity_supercheckout_payment']['error']['payment_method'][$id]['logo']['name'] > 0)
							$payment_error .= '* Error in image of '.$paymethod['name'].'<br/>';
						else
						{
							$mask = _PS_MODULE_DIR_.'supercheckout/views/img/admin/uploads/paymethod'.trim($id).'.*';
							$matches = glob($mask);
							if (count($matches) > 0)
								array_map('unlink', $matches);
							if (move_uploaded_file($_FILES['velocity_supercheckout_payment']['tmp_name']['payment_method'][$id]['logo']['name'],
								_PS_MODULE_DIR_.'supercheckout/views/img/admin/uploads/paymethod'.trim($id).'.'.$extension))
								$payment_post_data['payment_method'][$id]['logo']['title'] = 'paymethod'.trim($id).'.'.$extension;
							else
								$payment_error .= '* Error in uploading the image of '.$paymethod['name'].'<br/>';
							if (!version_compare(_PS_VERSION_, '1.6.0.1', '<'))
								Tools::chmodr(_PS_MODULE_DIR_.'supercheckout/views/img/uploads', 0755);
						}
					}
					else
						$payment_error .= '* Error Uploaded file is not a  image  '.$paymethod['name'].'<br/>';
				}
			}

			foreach (Carrier::getCarriers($this->context->language->id, true, false, false, null, Carrier::ALL_CARRIERS) as $deliverymethod)
			{
				$id = $deliverymethod['id_carrier'];
				if ($_FILES['velocity_supercheckout_payment']['size']['delivery_method'][$id]['logo']['name'] == 0)
					$payment_post_data['delivery_method'][$id]['logo']['title'] == '';
				else
				{
					$allowed_exts = array('gif', 'jpeg', 'jpg', 'png', 'JPG', 'PNG', 'GIF', 'JPEG');
					$extension = explode('.', $_FILES['velocity_supercheckout_payment']['name']['delivery_method'][$id]['logo']['name']);
					$extension = end($extension);
					$extension = trim($extension);
					if ((($_FILES['velocity_supercheckout_payment']['type']['delivery_method'][$id]['logo']['name'] == 'image/jpg')
						|| ($_FILES['velocity_supercheckout_payment']['type']['delivery_method'][$id]['logo']['name'] == 'image/jpeg')
						|| ($_FILES['velocity_supercheckout_payment']['type']['delivery_method'][$id]['logo']['name'] == 'image/gif')
						|| ($_FILES['velocity_supercheckout_payment']['type']['delivery_method'][$id]['logo']['name'] == 'image/png'))
						&& ($_FILES['velocity_supercheckout_payment']['size']['delivery_method'][$id]['logo']['name'] < 300000)
						&& in_array($extension, $allowed_exts))
					{
						if ($_FILES['velocity_supercheckout_payment']['error']['delivery_method'][$id]['logo']['name'] > 0)
							$payment_error .= '* Error in image of '.$deliverymethod['name'].'<br/>';
						else
						{
							$mask = _PS_MODULE_DIR_.'supercheckout/views/img/admin/uploads/deliverymethod'.trim($id).'.*';
							$matches = glob($mask);
							if (count($matches) > 0)
								array_map('unlink', $matches);
							if (move_uploaded_file($_FILES['velocity_supercheckout_payment']['tmp_name']['delivery_method'][$id]['logo']['name'],
								_PS_MODULE_DIR_.'supercheckout/views/img/admin/uploads/deliverymethod'.trim($id).'.'.$extension))
								$payment_post_data['delivery_method'][$id]['logo']['title'] = 'deliverymethod'.trim($id).'.'.$extension;
							else
								$payment_error .= '* Error in uploading the image of '.$deliverymethod['name'].'<br/>';
							if (!version_compare(_PS_VERSION_, '1.6.0.1', '<'))
								Tools::chmodr(_PS_MODULE_DIR_.'supercheckout/views/img/uploads', 0755);
						}
					}
					else
						$payment_error .= '* Error Uploaded file is not a  image  '.$deliverymethod['name'].'<br/>';
				}
			}
			Configuration::updateValue('VELOCITY_SUPERCHECKOUT_DATA', serialize($payment_post_data));
			if ($payment_error != '')
				$output .= $this->displayError($payment_error);
		}

		if (!Configuration::get('VELOCITY_SUPERCHECKOUT') || Configuration::get('VELOCITY_SUPERCHECKOUT') == '')
			$this->supercheckout_settings = $supercheckout_config->getDefaultSettings();
		else
			$this->supercheckout_settings = unserialize(Configuration::get('VELOCITY_SUPERCHECKOUT'));

		if (Configuration::get('VELOCITY_SUPERCHECKOUT_CSS') || Configuration::get('VELOCITY_SUPERCHECKOUT_CSS') != '')
		{
			$this->supercheckout_settings['custom_css'] = unserialize((Configuration::get('VELOCITY_SUPERCHECKOUT_CSS')));
			$this->supercheckout_settings['custom_css'] = urldecode($this->supercheckout_settings['custom_css']);
		}

		if (Configuration::get('VELOCITY_SUPERCHECKOUT_JS') || Configuration::get('VELOCITY_SUPERCHECKOUT_JS') != '')
		{
			$this->supercheckout_settings['custom_js'] = unserialize((Configuration::get('VELOCITY_SUPERCHECKOUT_JS')));
			$this->supercheckout_settings['custom_js'] = urldecode($this->supercheckout_settings['custom_js']);
		}
		if (Configuration::get('VELOCITY_SUPERCHECKOUT_KEYS') || Configuration::get('VELOCITY_SUPERCHECKOUT_KEYS') != '')
		{
			$key_details = unserialize(Configuration::get('VELOCITY_SUPERCHECKOUT_KEYS'));
			$this->supercheckout_settings['fb_login']['app_id'] = $key_details['fb_login']['app_id'];
			$this->supercheckout_settings['fb_login']['app_secret'] = $key_details['fb_login']['app_secret'];
			$this->supercheckout_settings['google_login']['client_id'] = $key_details['google_login']['client_id'];
			$this->supercheckout_settings['google_login']['app_secret'] = $key_details['google_login']['app_secret'];
			$this->supercheckout_settings['mailchimp']['api'] = $key_details['mailchimp']['api'];
			$this->supercheckout_settings['mailchimp']['list'] = $key_details['mailchimp']['list'];
		}
		else
		{
			$key_settings = array(
				'fb_login' => array(
				'app_id' => '',
				'app_secret' => ''
				),
				'google_login' => array(
				'client_id' => '',
				'app_secret' => ''
				),
				'mailchimp' => array(
				'api' => '',
				'key' => '',
				'list' => ''
				)
			);
			Configuration::updateValue('VELOCITY_SUPERCHECKOUT_KEYS', serialize($key_settings));
		}

		if (!Configuration::get('VELOCITY_SUPERCHECKOUT_BUTTON') || Configuration::get('VELOCITY_SUPERCHECKOUT_BUTTON') == '')
			$custombutton = array('button_color' => 'F77219', 'button_border_color' => 'EC6723',
				'button_text_color' => 'F9F9F9', 'border_bottom_color' => 'C52F2F');
		else
			$custombutton = unserialize(Configuration::get('VELOCITY_SUPERCHECKOUT_BUTTON'));

		if (!Configuration::get('VELOCITY_SUPERCHECKOUT_DATA') || Configuration::get('VELOCITY_SUPERCHECKOUT_DATA') == '')
			$paymentdata = array();
		else
			$paymentdata = unserialize(Configuration::get('VELOCITY_SUPERCHECKOUT_DATA'));

		$this->supercheckout_settings['customizer']['button_border_color'] = $custombutton['button_border_color'];
		$this->supercheckout_settings['customizer']['button_color'] = $custombutton['button_color'];
		$this->supercheckout_settings['customizer']['button_text_color'] = $custombutton['button_text_color'];
		$this->supercheckout_settings['customizer']['border_bottom_color'] = $custombutton['border_bottom_color'];
		if (!Configuration::get('VELOCITY_SUPERCHECKOUT_HFHTML') || Configuration::get('VELOCITY_SUPERCHECKOUT_HFHTML') == '')
			$headerfooterhtml = array('header' => '', 'footer' => '');
		else
			$headerfooterhtml = unserialize(Configuration::get('VELOCITY_SUPERCHECKOUT_HFHTML'));

		$this->supercheckout_settings['html_value']['header'] = $headerfooterhtml['header'];
		$this->supercheckout_settings['html_value']['footer'] = $headerfooterhtml['footer'];

		//Decode Extra Html
		$this->supercheckout_settings['html_value']['header'] = html_entity_decode($this->supercheckout_settings['html_value']['header']);
		$this->supercheckout_settings['html_value']['footer'] = html_entity_decode($this->supercheckout_settings['html_value']['footer']);

		if (!Configuration::get('VELOCITY_SUPERCHECKOUT_EXTRAHTML') || Configuration::get('VELOCITY_SUPERCHECKOUT_EXTRAHTML') == '')
			$extrahtml = array(
					'0_0' => array(
						'1_column' => array('column' => 0, 'row' => 7, 'column-inside' => 1),
						'2_column' => array('column' => 2, 'row' => 1, 'column-inside' => 4),
						'3_column' => array('column' => 3, 'row' => 4, 'column-inside' => 1),
						'value' => ''
					)
				);
		else
			$extrahtml = unserialize(Configuration::get('VELOCITY_SUPERCHECKOUT_EXTRAHTML'));
		foreach ($extrahtml as $key => $value)
		{
//			$temp = $value;
			$extrahtml_value = $extrahtml[$key]['value'];
			if (isset($this->supercheckout_settings['design']['html'][$key]))
				$this->supercheckout_settings['design']['html'][$key]['value'] = $extrahtml_value;
			else
			{
				$this->supercheckout_settings['design']['html'][$key]['1_column'] = $extrahtml[$key]['1_column'];
				$this->supercheckout_settings['design']['html'][$key]['2_column'] = $extrahtml[$key]['2_column'];
				$this->supercheckout_settings['design']['html'][$key]['3_column'] = $extrahtml[$key]['3_column'];
				$this->supercheckout_settings['design']['html'][$key]['value'] = $extrahtml[$key]['value'];
			}
//			unset($temp);
		}

		foreach ($this->supercheckout_settings['design']['html'] as $key => $value)
		{
			$tmp = $value;
			$html_value = $this->supercheckout_settings['design']['html'][$key]['value'];
			$this->supercheckout_settings['design']['html'][$key]['value'] = html_entity_decode($html_value);
			unset($tmp);
		}

		if (isset($_REQUEST['velsof_layout']) && in_array($_REQUEST['velsof_layout'], array(1, 2, 3)))
			$layout = $_REQUEST['velsof_layout'];
		else
			$layout = $this->supercheckout_settings['layout'];

		$payments = array();
		foreach (PaymentModule::getInstalledPaymentModules() as $pay_method)
		{
			if (file_exists(_PS_MODULE_DIR_.$pay_method['name'].'/'.$pay_method['name'].'.php'))
			{
			require_once( _PS_MODULE_DIR_.$pay_method['name'].'/'.$pay_method['name'].'.php' );
			if (class_exists($pay_method['name'], false))
			{
				$temp = array();
				$temp['id_module'] = $pay_method['id_module'];
				$temp['name'] = $pay_method['name'];
				$pay_temp = new $pay_method['name'];
				$temp['display_name'] = $pay_temp->displayName;
				$payments[] = $temp;
			}
			}
		}

		//Get Default Language Variables
		$curr_lang_code = $this->context->language->iso_code;
		$eng_langs = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'velsof_supercheckout_translation` 
			where iso_code = "'.pSQL($curr_lang_code).'"');
		$current_lang_translation = array();
		foreach ($eng_langs as $eng_lang)
		{
			$keys = explode('_', $eng_lang['key']);
			$labels = $keys[count($keys) - 1];
			array_pop($keys);
			$keys = implode('_', $keys);
			$current_lang_translation[$keys][$labels][0] = $eng_lang['key_variable'];
			$current_lang_translation[$keys][$labels][1] = $eng_lang['description'];
		}

		$selected_lang_translation = array();

		if (isset($_REQUEST['velsof_translate_lang']) && $_REQUEST['velsof_translate_lang'] != '')
		{
			$temp_lang = explode('_', $_REQUEST['velsof_translate_lang']);
			$sel_lang_id = $temp_lang[0];
			$curr_lang_code = $temp_lang[1];
			$default_selected_language = $sel_lang_id;
			$sel_langs = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'velsof_supercheckout_translation` 
				where iso_code = "'.pSQL($curr_lang_code).'"');
			if ($sel_langs && count($sel_langs) > 0)
			{
				foreach ($sel_langs as $cur_lang)
				{
					$keys = explode('_', $cur_lang['key']);
					$labels = $keys[count($keys) - 1];
					array_pop($keys);
					$keys = implode('_', $keys);
					$selected_lang_translation[$keys][$labels][0] = $cur_lang['key_variable'];
					$selected_lang_translation[$keys][$labels][1] = $cur_lang['description'];
				}
			}
			else
				$selected_lang_translation = $current_lang_translation;
		}
		else
		{
			$default_selected_language = $this->context->language->id;
			$selected_lang_translation = $current_lang_translation;
		}
		if (_PS_VERSION_ < '1.6.0')
			$lang_img_dir = _PS_IMG_DIR_.'l/';
		else
			$lang_img_dir = _PS_LANG_IMG_DIR_;
		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
			$custom_ssl_var = 1;
		if ((bool)Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1)
		{
			$ps_base_url = _PS_BASE_URL_SSL_;
			$manual_dir = _PS_BASE_URL_SSL_.__PS_BASE_URI__;
		}
		else
		{
			$ps_base_url = _PS_BASE_URL_;
			$manual_dir = _PS_BASE_URL_.__PS_BASE_URI__;
		}

		$this->_clearCache('supercheckout.tpl');
		//AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules').'&configure='.$this->name,
		$this->smarty->assign(array(
			'root_path' => $this->_path,
			'action' => 'index.php?controller=AdminModules&token='.Tools::getAdminTokenLite('AdminModules').'&configure='.$this->name,
			'cancel_action' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
			'velocity_supercheckout' => $this->supercheckout_settings,
			'highlighted_fields' => array('company', 'address2', 'postcode', 'other', 'phone', 'phone_mobile', 'vat_number', 'dni'),
			'layout' => $layout,
			'manual_dir' => $manual_dir,
			'domain' => $_SERVER['HTTP_HOST'],
			'payment_methods' => $payments,
			'carriers' => Carrier::getCarriers($this->context->language->id, true, false, false, null, Carrier::ALL_CARRIERS),
			'languages' => Language::getLanguages(),
			'submit_action' => $this->submit_action.$this->name,
			'default_selected_language' => $default_selected_language,
			'current_lang_translator_vars' => $current_lang_translation,
			'selected_lang_translator_vars' => $selected_lang_translation,
			'IE7' => $is_ie7,
			'guest_is_enable_from_system' => Configuration::get('PS_GUEST_CHECKOUT_ENABLED'),
			'velocity_supercheckout_payment' => $paymentdata,
			'root_dir' => _PS_ROOT_DIR_,
			'languages' => Language::getLanguages(true),
			'img_lang_dir' => $ps_base_url.__PS_BASE_URI__.str_replace(_PS_ROOT_DIR_.'/', '', $lang_img_dir),
	'module_url' => $this->context->link->getModuleLink('supercheckout', 'supercheckout', array(), (bool)Configuration::get('PS_SSL_ENABLED'))
		));

		//Added to assign current version of prestashop in a new variable
		if (version_compare(_PS_VERSION_, '1.6.0.1', '<'))
			$this->smarty->assign('ps_version', 15);
		else
			$this->smarty->assign('ps_version', 16);

		$output .= $this->display(__FILE__, 'views/templates/admin/supercheckout.tpl');
		return $output;
	}

	/*
	 * Add css and javascript
	 */

	protected function addBackOfficeMedia()
	{
		//CSS files
		$this->context->controller->addCSS($this->_path.'views/css/supercheckout.css');
		$this->context->controller->addCSS($this->_path.'views/css/bootstrap.css');
		$this->context->controller->addCSS($this->_path.'views/css/responsive.css');
		$this->context->controller->addCSS($this->_path.'views/css/jquery-ui/jquery-ui.min.css');
		$this->context->controller->addCSS($this->_path.'views/css/fonts/glyphicons/glyphicons_regular.css');
		$this->context->controller->addCSS($this->_path.'views/css/fonts/font-awesome/font-awesome.min.css');
		$this->context->controller->addCSS($this->_path.'views/css/pixelmatrix-uniform/uniform.default.css');
		$this->context->controller->addCSS($this->_path.'views/css/bootstrap-switch/bootstrap-switch.css');
		$this->context->controller->addCSS($this->_path.'views/css/select2/select2.css');
		$this->context->controller->addCSS($this->_path.'views/css/style-light.css');
		$this->context->controller->addCSS($this->_path.'views/css/bootstrap-select/bootstrap-select.css');
		$this->context->controller->addCSS($this->_path.'views/css/jQRangeSlider/iThing.css');
		$this->context->controller->addCSS($this->_path.'views/css/jquery-miniColors/jquery.miniColors.css');

		$this->context->controller->addJs($this->_path.'views/js/jquery-ui/jquery-ui.min.js');
		$this->context->controller->addJs($this->_path.'views/js/bootstrap.min.js');
		$this->context->controller->addJs($this->_path.'views/js/common.js');
		$this->context->controller->addJs($this->_path.'views/js/system/less.min.js');
		$this->context->controller->addJs($this->_path.'views/js/tinysort/jquery.tinysort.min.js');
		$this->context->controller->addJs($this->_path.'views/js/jquery/jquery.autosize.min.js');
		$this->context->controller->addJs($this->_path.'views/js/uniform/jquery.uniform.min.js');
		$this->context->controller->addJs($this->_path.'views/js/tooltip/tooltip.js');
		$this->context->controller->addJs($this->_path.'views/js/bootbox.js');
		$this->context->controller->addJs($this->_path.'views/js/bootstrap-select/bootstrap-select.js');
		$this->context->controller->addJs($this->_path.'views/js/bootstrap-switch/bootstrap-switch.js');
		$this->context->controller->addJs($this->_path.'views/js/system/jquery.cookie.js');
		$this->context->controller->addJs($this->_path.'views/js/themer.js');
		$this->context->controller->addJs($this->_path.'views/js/admin/jscolor.js');

		$this->context->controller->addJs($this->_path.'views/js/jquery-miniColors/jquery.miniColors.js');

		$this->context->controller->addJs($this->_path.'views/js/supercheckout.js');

		if (!version_compare(_PS_VERSION_, '1.6.0.1', '<'))
			$this->context->controller->addCSS($this->_path.'views/css/supercheckout_16_admin.css');
		else
			$this->context->controller->addCSS($this->_path.'views/css/supercheckout_15_admin.css');
	}

	/*
	 * Handle ajax requests
	 */

	public function ajaxHandler()
	{
		$json = array();
		if (Tools::isSubmit($this->submit_action.$this->name) && Tools::getValue($this->submit_action.$this->name) == '1')
		{
			$errors = $this->validateSettings(Tools::getValue('velocity_supercheckout'));

			if ($errors)
			{
				$json['error'] = $errors;
				$json['error']['request_error'] = $this->l('Please provide required information with valid data.');
			}
			else
				$json['success'] = '1';
		}
		else
			$json['error'] = array('request_error' => $this->l('Please provide required information with valid data.'));
		echo Tools::jsonEncode($json);
		die;
	}

	/*
	 * Handle ajax requests for language translation
	 */

	public function saveTranslation()
	{
		$data = array('velocity_transalator' => Tools::getValue('velocity_transalator'));
		$temp_var = explode('_', $data['velocity_transalator']['selected_language']);
		$language_id = $temp_var[0];
		$language_iso_code = $temp_var[1];
		$json = array();
		$translation_dir = _PS_MODULE_DIR_.'supercheckout/translations/';
		$file_path = $translation_dir.$language_iso_code.'.php';

		unset($data['velocity_transalator']['selected_language']);

		/*$transaltion_sql = $translation_dir.'translation_sql/'.$language_iso_code.'.sql';
		if (is_writable($transaltion_sql))
		{
			$f_trans = fopen($transaltion_sql, 'w+');
			fwrite($f_trans,
				'INSERT INTO `PREFIX_velsof_supercheckout_translation` (`id_lang`, `iso_code`, `key`, `key_variable`, `description`) VALUES'.PHP_EOL);
			$key_count = 1;
			foreach ($data['velocity_transalator'] as $key => $lang_label)
			{
				$sql_end_delimiter = ',';
				if ($key_count == count($data['velocity_transalator']))
					$sql_end_delimiter = '';
				if (isset($lang_label['label']))
					fwrite($f_trans,
						'(ID_LANG, \'ISO_CODE\', \''.$key.'_label\', \''.str_replace("'", "''", $lang_label['label'][0]).'\', \''
						.str_replace("'", "''", $lang_label['label'][1]).'\')'.$sql_end_delimiter.PHP_EOL);
				if (isset($lang_label['tooltip']))
					fwrite($f_trans,
						'(ID_LANG, \'ISO_CODE\', \''.$key.'_tooltip\', \''.str_replace("'", "''", $lang_label['tooltip'][0]).'\', \''
						.str_replace("'", "''", $lang_label['tooltip'][1]).'\')'.$sql_end_delimiter.PHP_EOL);
				$key_count++;
			}
			fclose($f_trans);
			$json['success'] = $this->l('Language successfully translated');
		}
		else
			$json['error'] = $this->l('Permission errorred occur for language file creating');*/

		Db::getInstance()->execute('delete FROM `'._DB_PREFIX_.'velsof_supercheckout_translation` where id_lang = '.(int)$language_id);
		foreach ($data['velocity_transalator'] as $key => $lang_label)
		{
			$ins_query = 'INSERT INTO `'._DB_PREFIX_.'velsof_supercheckout_translation` (`id_lang`, `iso_code`, `key`, `key_variable`, `description`) VALUES ';
			if (isset($lang_label['label']))
			{
				$query = '('.(int)$language_id.', \''.pSQL($language_iso_code).'\', \''.pSQL($key).'_label\', \''
					.str_replace("'", "''", pSQL($lang_label['label'][0]))
					.'\', \''.str_replace("'", "''", pSQL($lang_label['label'][1])).'\')';
				Db::getInstance()->execute($ins_query.$query);
			}
			if (isset($lang_label['tooltip']))
			{
				$query = '('.(int)$language_id.', \''.pSQL($language_iso_code).'\', \''.pSQL($key).'_tooltip\', \''
					.str_replace("'", "''", pSQL($lang_label['tooltip'][0]))
					.'\', \''.str_replace("'", "''", pSQL($lang_label['tooltip'][1])).'\')';
				Db::getInstance()->execute($ins_query.$query);
			}
		}

		$json['success'] = $this->l('Language successfully translated');
		if (is_writable($translation_dir))
			$this->generateLanguageFile($file_path, $data);
		else
			$json['error'] = $this->l('Permission errorred occur for language file creating');

		echo Tools::jsonEncode($json);
		die;
	}

	private function generateLanguageFile($file_path, $data)
	{
		$f = fopen($file_path, 'w+');
		fwrite($f, '<?php '.PHP_EOL.PHP_EOL);
		fwrite($f, 'global $_MODULE;'.PHP_EOL);
		fwrite($f, '$_MODULE = array();'.PHP_EOL.PHP_EOL);

		foreach ($data['velocity_transalator'] as $lang_label)
		{
			$template_files = array();
			if (isset($lang_label['label']))
			{
				if (isset($lang_label['label'][2]))
					$template_files = explode('|', $lang_label['label'][2]);
				array_push($template_files, 'supercheckout');
				foreach ($template_files as $template)
				{
				$language = '$_MODULE[\'<{supercheckout}prestashop>'.$template.'_'.md5($lang_label['label'][0]).'\'] = \''
					.strip_tags(addslashes($lang_label['label'][1])).'\';';
				fwrite($f, $language.PHP_EOL);
				}
			}
			$template_files = array();
			if (isset($lang_label['tooltip']))
			{
				if (isset($lang_label['tooltip'][2]))
					$template_files = explode('|', $lang_label['tooltip'][2]);
				array_push($template_files, 'supercheckout');
				foreach ($template_files as $template)
				{
				$language = '$_MODULE[\'<{supercheckout}prestashop>'.$template.'_'.md5($lang_label['tooltip'][0]).'\'] = \''
					.strip_tags(addslashes($lang_label['tooltip'][1])).'\'; //'.$lang_label['tooltip'][0];
				fwrite($f, $language.PHP_EOL);
				}
			}
		}

		fwrite($f, PHP_EOL);
		fwrite($f, 'return $_MODULE;');
		fclose($f);
	}

	private function generateTmpLanguageFile()
	{
		$data = array('velocity_transalator' => Tools::getValue('velocity_transalator'));
		$temp_var = explode('_', $data['velocity_transalator']['selected_language']);
		$language_iso_code = $temp_var[1];
		unset($data['velocity_transalator']['selected_language']);

		$json = array();
		$translation_dir = _PS_MODULE_DIR_.'supercheckout/translations/tmp/';
		$file_path = $translation_dir.$language_iso_code.'.php';

		if (is_writable($translation_dir))
		{
			$this->generateLanguageFile($file_path, $data, $language_iso_code);
			$json['success'] = $language_iso_code;
		}
		else
			$json['error'] = $this->l('Permission errorred occur for language file creating');

		echo Tools::jsonEncode($json);
		die;
	}

	private function downloadTranslation($file_name, $is_tmp = false)
	{
		$translation_dir = _PS_MODULE_DIR_.'supercheckout/translations/';
		if ($is_tmp)
			$translation_dir .= 'tmp/';
		$file = $translation_dir.$file_name.'.php';
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="'.basename($file).'"');
		header('Content-Length: '.filesize($file));
		readfile($file);
		exit;
	}

	/*
	 * Validate settings as set by Admin
	 */

	protected function validateSettings($settings)
	{
		$errors = array();

		if ($settings['fb_login']['enable'] == '1')
		{
			if (trim($settings['fb_login']['app_id']) == '')
				$errors['fb_login_app_id'] = $this->l('Required Field');
			if (trim($settings['fb_login']['app_secret']) == '')
				$errors['fb_login_app_secret'] = $this->l('Required Field');
		}

		if ($settings['google_login']['enable'] == '1')
		{
			if (trim($settings['google_login']['client_id']) == '')
				$errors['gl_login_client_id'] = $this->l('Required Field');
			if (trim($settings['google_login']['app_secret']) == '')
				$errors['gl_login_app_secret'] = $this->l('Required Field');
		}

		if (count($errors) > 0)
			return $errors;
		else
			return false;
	}

	/*
	 * Creae log of all scratch coupon activity on front end
	 */

	private function writeLog($type, $msg)
	{
		$f = fopen('log.txt', 'a+');
		fwrite($f, $type."\t".date('m-d-Y H:i:s', time())."\t".$msg);
		fwrite($f, "\n");
		fclose($f);
	}

	public function hookDisplayHeader()
	{
		$settings = array();
		$supercheckout_config = new SupercheckoutConfiguration();
		if (!Configuration::get('VELOCITY_SUPERCHECKOUT') || Configuration::get('VELOCITY_SUPERCHECKOUT') == '')
			$settings = $supercheckout_config->getDefaultSettings();
		else
			$settings = unserialize(Configuration::get('VELOCITY_SUPERCHECKOUT'));
		if (!Tools::getValue('klarna_supercheckout'))
		{
			if (isset($settings['super_test_mode']) && $settings['super_test_mode'] != 1)
			{
				if ($this->context->smarty->tpl_vars['page_name']->value == 'order-opc' || $this->context->smarty->tpl_vars['page_name']->value == 'order')
				{
					if ($settings['enable'] == 1)
					{
						$current_page_url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
						$query_string = parse_url($current_page_url);
						$query_params = array();
						if (isset($query_string['query']))
							parse_str($query_string['query'], $query_params);
						Tools::redirect($this->context->link->getModuleLink($this->name, $this->name, $query_params, (bool)Configuration::get('PS_SSL_ENABLED')));
					}
				}
			}
		}

		if (Configuration::get('VELOCITY_SUPERCHECKOUT_CSS') || Configuration::get('VELOCITY_SUPERCHECKOUT_CSS') != '')
		{
			$settings['custom_css'] = unserialize((Configuration::get('VELOCITY_SUPERCHECKOUT_CSS')));
			$settings['custom_css'] = urldecode($settings['custom_css']);
		}

		if (Configuration::get('VELOCITY_SUPERCHECKOUT_JS') || Configuration::get('VELOCITY_SUPERCHECKOUT_JS') != '')
		{
			$settings['custom_js'] = unserialize((Configuration::get('VELOCITY_SUPERCHECKOUT_JS')));
			$settings['custom_js'] = urldecode($settings['custom_js']);
		}

		if (isset($settings['custom_css']))
		$this->smarty->assign($settings['custom_css']); //return '<style type="text/css">'.$settings['custom_css'].'</style>';

		if (isset($settings['custom_js']))
		$this->smarty->assign($settings['custom_js']);
	}

	public function hookDisplayOrderConfirmation($params = null)
	{
		if (Configuration::get('PACZKAWRUCHU_CARRIER_ID'))
		{
			$carrier = Configuration::get('PACZKAWRUCHU_CARRIER_ID');
			$order_carrier_id = $params['objOrder']->id_carrier;
			$cart_id = $params['objOrder']->id_cart;
			if ($order_carrier_id != $carrier)
			{
				$delete_query = 'delete from `'._DB_PREFIX_.'paczkawruchu` WHERE id_cart='.(int)$cart_id;
				Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($delete_query);
			}
		}
		unset($params);
		if (isset($this->context->cookie->supercheckout_temp_address_delivery) && $this->context->cookie->supercheckout_temp_address_delivery)
		{
			if ($this->context->cookie->supercheckout_temp_address_delivery != $this->context->cookie->supercheckout_perm_address_delivery)
				Db::getInstance(_PS_USE_SQL_SLAVE_)->execute('delete from '._DB_PREFIX_.'address 
					where id_address = '.(int)$this->context->cookie->supercheckout_temp_address_delivery);
			$this->context->cookie->supercheckout_temp_address_delivery = 0;
			$this->context->cookie->__unset($this->context->cookie->supercheckout_temp_address_delivery);
		}
		if (isset($this->context->cookie->supercheckout_temp_address_invoice) && $this->context->cookie->supercheckout_temp_address_invoice)
		{
			if ($this->context->cookie->supercheckout_temp_address_invoice != $this->context->cookie->supercheckout_perm_address_invoice)
				Db::getInstance(_PS_USE_SQL_SLAVE_)->execute('delete from '._DB_PREFIX_.'address 
					where id_address = '.(int)$this->context->cookie->supercheckout_temp_address_invoice);
			$this->context->cookie->supercheckout_temp_address_invoice = 0;
			$this->context->cookie->__unset($this->context->cookie->supercheckout_temp_address_invoice);
		}
		$this->context->cookie->supercheckout_perm_address_delivery = 0;
		$this->context->cookie->__unset($this->context->cookie->supercheckout_perm_address_delivery);
		$this->context->cookie->supercheckout_perm_address_invoice = 0;
		$this->context->cookie->__unset($this->context->cookie->supercheckout_perm_address_invoice);
	}

	protected function getMailchimpLists($mailchimp_api)
	{
		try
		{
			$id = $mailchimp_api;
			$mchimp = new MailChimp($id);
			$arrchimp = ($mchimp->call('lists/list'));
			$totallists = $arrchimp['total'];
			if ($totallists >= 1)
			{
				$listchimp = $arrchimp['data'];
				echo Tools::jsonEncode($listchimp);
			}
			else
				echo Tools::jsonEncode(array('false'));
		}
		catch (Exception $e)
		{
			echo Tools::jsonEncode(array('false'));
		}
		die;
	}

	protected function removeFile($id)
	{
		$mask = _PS_MODULE_DIR_.'supercheckout/views/img/admin/uploads/'.trim($id).'.*';
		$matches = glob($mask);
		if (count($matches) > 0)
		{
			array_map('unlink', $matches);
			echo 1;
		}
		die;
	}
}
