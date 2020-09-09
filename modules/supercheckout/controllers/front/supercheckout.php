<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store. 
 *
 * @category  PrestaShop Module
 * @author    knowband.com <support@knowband.com>
 * @copyright 2015 Knowband
 * @license   see file: LICENSE.txt
 */

class FreeOrder extends PaymentModule
{
	public $active = 1;
	public $name = 'free_order';
	public $displayName = 'free_order';
}

class SupercheckoutSupercheckoutModuleFrontController extends ModuleFrontController
{
	public $ssl = true;
	private $name = 'supercheckout';
	private $supercheckout_settings = '';
	private $supercheckout_default_data = array();
	private $module_dir = '';
	private $is_logged;
	protected $json = array();
	protected $nb_products;
	private $selected_payment_method = 0;
	private $social_login_type = '';
	protected $error = array();
	private $shipping_error = array();
	private $password_length = 5;
	private $image_extensions = array('.gif', '.png', '.jpg', '.jpeg');
	private $address_cookie;

	public function init()
	{
		parent::init();
		// Added below code to close popup when user click cancel while login with social buttons
		if (Tools::getValue('error') == 'access_denied' && ((Tools::getValue('login_type') == 'fb') || (Tools::getValue('login_type') == 'google')))
			echo '<script>window.close();</script>';

		$this->supercheckout_settings = unserialize(Configuration::get('VELOCITY_SUPERCHECKOUT'));
		if ($this->context->cart->isVirtualCart() && $this->supercheckout_settings['hide_delivery_for_virtual'] == 0)
		{
			foreach (array_keys($this->supercheckout_settings['shipping_address']) as $key)
			{
				$this->supercheckout_settings['shipping_address'][$key]['guest']['require'] = 0;
				$this->supercheckout_settings['shipping_address'][$key]['logged']['require'] = 0;
			}
		}
		$this->context->smarty->assign(array('isvirtualcart' => false));
		// 0 means that admin don't want to show delivery address
		if ($this->context->cart->isVirtualCart() && $this->supercheckout_settings['hide_delivery_for_virtual'] == 0)
			$this->context->smarty->assign(array('isvirtualcart' => true));

		//Decode Extra Html
		$this->supercheckout_settings['html_value']['header'] = html_entity_decode($this->supercheckout_settings['html_value']['header']);
		$this->supercheckout_settings['html_value']['footer'] = html_entity_decode($this->supercheckout_settings['html_value']['footer']);
		foreach ($this->supercheckout_settings['design']['html'] as $key => $value)
		{
			$tmp = $value;
			$html_value = $this->supercheckout_settings['design']['html'][$key]['value'];
			$this->supercheckout_settings['design']['html'][$key]['value'] = html_entity_decode($html_value);
			unset($tmp);
		}
		//Check for plugin is enable or disable
		if ($this->supercheckout_settings['enable'] == 0)
		{
			if (Configuration::get('PS_ORDER_PROCESS_TYPE') == 1)
				Tools::redirect('index.php?controller=order-opc');
			else
				Tools::redirect('index.php?controller=order');
		}

		if (version_compare(_PS_VERSION_, '1.6.0.1', '<'))
			$this->module_dir = __PS_BASE_URI__.'modules/'.$this->name.'/';
		else
			$this->module_dir = _PS_MODULE_DIR_.'supercheckout/';

		if ($this->context->customer->isLogged())
			$this->is_logged = true;
		else
			$this->is_logged = false;

		$this->nb_products = $this->context->cart->nbProducts();

		$this->default_payment_selected = $this->supercheckout_settings['payment_method']['default'];
		$this->default_shipping_selected = $this->supercheckout_settings['shipping_method']['default'];
	}

	public function setMedia()
	{
		parent::setMedia();
		//add css
		$this->addCSS($this->module_dir.'views/css/front/reset_supercheckout.css');
		$this->addCSS($this->module_dir.'views/css/front/supercheckout.css');
		$this->addCSS($this->module_dir.'views/css/front/notifications/jquery.notyfy.css');
		$this->addCSS($this->module_dir.'views/css/front/notifications/default.css');
		$this->addCSS($this->module_dir.'views/css/front/notifications/jquery.gritter.css');
		$this->addCSS($this->module_dir.'views/css/front/colorbox.css');

		//add Js
		$this->addJS($this->module_dir.'views/js/front/jquery.tinysort.min.js');
		$this->addJS($this->module_dir.'views/js/front/bootstrap.js');
		$this->addJS($this->module_dir.'views/js/front/jquery.colorbox.js');
		$this->addJS($this->module_dir.'views/js/front/jquery-ui-1.8.16.custom.min.js');
		$this->addJS($this->module_dir.'views/js/front/jquery.ui.progressbar.min.js');
		$this->addJS($this->module_dir.'views/js/front/notifications/jquery.gritter.min.js');
		$this->addJS($this->module_dir.'views/js/front/notifications/jquery.notyfy.js');
		$this->addJS($this->module_dir.'views/js/front/supercheckout_notifications.js');
		$this->addJS($this->module_dir.'views/js/front/supercheckout.js');
		$this->addJS($this->module_dir.'views/js/front/supercheckout_common.js');

		$custom_ssl_var = 0;
		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
			$custom_ssl_var = 1;
		if (!version_compare(_PS_VERSION_, '1.6.0.1', '<'))
		{
			if ((bool)Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1)
				$this->addJS(_PS_BASE_URL_SSL_.__PS_BASE_URI__.'/themes/default-bootstrap/js/modules/blockcart/ajax-cart.js');
			else
				$this->addJS(_PS_BASE_URL_.__PS_BASE_URI__.'/themes/default-bootstrap/js/modules/blockcart/ajax-cart.js');

			$this->addCSS($this->module_dir.'views/css/supercheckout_16.css');
		}
		else
			$this->addCSS($this->module_dir.'views/css/supercheckout_15.css');

		if ((bool)Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1)
		{
			$this->addJS(_PS_BASE_URL_SSL_.__PS_BASE_URI__.'js/jquery/plugins/fancybox/jquery.fancybox.js');
			$this->addCSS(_PS_BASE_URL_SSL_.__PS_BASE_URI__.'js/jquery/plugins/fancybox/jquery.fancybox.css');
		}
		else
		{
			$this->addJS(_PS_BASE_URL_.__PS_BASE_URI__.'js/jquery/plugins/fancybox/jquery.fancybox.js');
			$this->addCSS(_PS_BASE_URL_.__PS_BASE_URI__.'js/jquery/plugins/fancybox/jquery.fancybox.css');
		}
	}

	public function postProcess()
	{
		parent::postProcess();

		//Handle Ajax request
		if (Tools::isSubmit('ajax'))
		{
			$this->json = array();
			if (Tools::isSubmit($this->name.'PlaceOrder'))
				$this->json = $this->confirmOrder();
			else if (Tools::isSubmit('SubmitLogin'))
				$this->processSubmitLogin();
			else if (Tools::isSubmit('submitDiscount'))
			{
				if ($this->nb_products)
					$this->json = $this->addCartRule();
				else
					$this->json['error'] = $this->module->l('Your cart is empty.');
			}
			else if (Tools::isSubmit('deleteDiscount'))
			{
				if ($this->nb_products)
					$this->json = $this->removeDiscount();
				else
					$this->json['error'] = $this->module->l('Your cart is empty.');
			}
			else if (Tools::isSubmit('method'))
			{
				switch (Tools::getValue('method'))
				{
					case 'checkDniandVat':
						{
							$this->json = $this->checkForDniandVat(Tools::getValue('id_country'));
							break;
						}
					case 'isValidDni':
						{
							$this->json = $this->isValidDni(Tools::getValue('dni'));
							break;
						}
					case 'isValidVatNumber':
						{
							$this->json = $this->isValidVatNumber(Tools::getValue('vat_number'));
							break;
						}
					case 'loadInvoiceAddress':
						{
							$this->json = $this->loadInvoiceAddress(Tools::getValue('id_country'),
								Tools::getValue('id_state'),
								Tools::getValue('postcode'),
								Tools::getValue('city'),
								Tools::getValue('id_address_invoice'));
							break;
						}
					case 'loadCarriers':
						{
							$this->json = $this->loadCarriers(Tools::getValue('id_country'),
								Tools::getValue('id_state'),
								Tools::getValue('postcode'),
								Tools::getValue('city'),
								Tools::getValue('id_address_delivery'));
							break;
						}
					case 'setSameInvoice':
						{
							$this->context->cookie->isSameInvoiceAddress = Tools::getValue('use_for_invoice') == 1 ? 1 : 0;
							$this->context->cookie->write();
							$this->json = array();
							break;
						}
					case 'updateCarrier':
						{
							$this->json = $this->updateCarrier();
							break;
						}
					case 'loadPayment':
						{
							$this->json = $this->loadPaymentMethods(Tools::getValue('id_country'),
								Tools::getValue('id_address_delivery'), Tools::getValue('selected_payment_method_id'));
							break;
						}
					case 'getPaymentInformation':
						{
							$this->json = $this->getPaymentInformation(Tools::getValue('payment_module_name'));
							break;
						}
					case 'updateDeliveryExtra':
						{
							$this->json = $this->updateDeliveryExtra();
							break;
						}
					case 'loadCart':
						{
							$this->json = $this->loadCart();
							break;
						}
					case 'getCarrierList':
						{
							$this->json = $this->getCarrierList();
							break;
						}
					case 'checkZipCode':
						{
							$this->json = $this->checkZipCode(Tools::getValue('id_country'), Tools::getValue('postcode'));
							break;
						}
					case 'createFreeOrder':
						{
							$this->json = $this->createFreeOrder();
							break;
						}
					case 'addEmailToList':
						{
							$this->addEmailToList(Tools::getValue('email'));
						}
				}
			}
			if (Tools::getValue('paypal_ec_canceled') == 1 || Tools::isSubmit('paypal_ec_canceled'))
				Tools::redirect($this->context->link->getModuleLink('supercheckout', 'supercheckout', array(),
					(bool)Configuration::get('PS_SSL_ENABLED')));

			ob_end_clean();
			echo Tools::jsonEncode($this->json);
			die;
		}
		else if (Tools::isSubmit('mylogout'))
		{
			$this->context->customer->mylogout();
			Tools::redirect('index.php');
		}
		else if (Tools::isSubmit('myfbLogin') || Tools::isSubmit('myGoogleLogin'))
		{
			if (Tools::isSubmit('myfbLogin'))
				$this->social_login_type = 'fb';
			else if (Tools::isSubmit('myGoogleLogin'))
				$this->social_login_type = 'google';

			$user_data_from_social = $this->socialLogin();

			if (count($user_data_from_social) > 0)
			{
				if ($this->loggedInCustomer($user_data_from_social))
				{
					if ($this->supercheckout_settings['social_login_popup']['enable'] == 1)
						echo '<script>window.opener.location.reload(true);window.close();</script>';
					else
						Tools::redirect($this->context->link->getModuleLink('supercheckout', 'supercheckout', array(), (bool)Configuration::get('PS_SSL_ENABLED')));
				}
			}
		}
		else if (Tools::isSubmit('code'))
		{
			if (Tools::isSubmit('login_type') && Tools::getValue('login_type') == 'fb')
				$this->social_login_type = 'fb';
			else if (Tools::isSubmit('login_type') && Tools::getValue('login_type') == 'google')
				$this->social_login_type = 'google';

			$user_data_from_social = $this->socialLogin();

			if (count($user_data_from_social) > 0)
			{
				if ($this->loggedInCustomer($user_data_from_social))
				{
					if ($this->supercheckout_settings['social_login_popup']['enable'] == 1)
						echo '<script>window.opener.location.reload(true);window.close();</script>';
					else
						Tools::redirect($this->context->link->getModuleLink('supercheckout', 'supercheckout', array(), (bool)Configuration::get('PS_SSL_ENABLED')));
				}
			}
		}
	}

	public function initContent()
	{
		parent::initContent();
		if (!$this->context->cart->nbProducts())
			$this->context->smarty->assign(array('empty' => true));
		else
		{
			$page_data = array();

			//Addresses
			$default_country = (int)Configuration::get('PS_COUNTRY_DEFAULT');

			$countries = Country::getCountries((int)$this->context->cookie->id_lang, true);
			$page_data = array_merge($page_data, array('countries' => $countries));
			if ($this->is_logged)
			{
				$addresses = $this->context->customer->getAddresses($this->context->cookie->id_lang);
				if (count($addresses) > 0)
				{
					$page_data = array_merge($page_data, array('addresses' => $this->context->customer->getAddresses($this->context->cookie->id_lang)));
					$formatted_addresses = $this->getFormattedAddress();
					$page_data = array_merge($page_data, array('formatedAddressFieldsValuesList' => $formatted_addresses));
				}
				else
					$page_data = array_merge($page_data, array('addresses' => false, 'formatedAddressFieldsValuesList' => null));
			}
			else
				$page_data = array_merge($page_data, array('addresses' => false, 'formatedAddressFieldsValuesList' => null));

			$translated_months = array();
			$months = Tools::dateMonths();
			foreach ($months as $i => $m)
				$translated_months[$i] = $this->module->l($m);

			//Load plugin Settings
			$custom_ssl_var = 0;
			if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
				$custom_ssl_var = 1;

			if (version_compare(_PS_VERSION_, '1.6.0.1', '<'))
				$this->supercheckout_settings['column_width']['1_column']['1'] = 96;

			if (version_compare(_PS_VERSION_, '1.6.0.1', '<') && $this->supercheckout_settings['layout'] == 2)
			{
				$column1_val = $this->supercheckout_settings['column_width']['2_column']['inside']['1'];
				$column2_val = $this->supercheckout_settings['column_width']['2_column']['inside']['2'];
				$this->supercheckout_settings['column_width']['2_column']['inside']['1'] = ($column1_val - 4.2);
				$this->supercheckout_settings['column_width']['2_column']['inside']['2'] = ($column2_val - 4.2);
			}

			if ((bool)Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1)
				$plugin_settings = array(
					'plugin_name' => $this->name,
					'settings' => $this->supercheckout_settings,
					'module_image_path' => _PS_BASE_URL_SSL_._MODULE_DIR_.'supercheckout/views/img/front/',
					'module_url' => $this->context->link->getModuleLink('supercheckout', 'supercheckout', array(), (bool)Configuration::get('PS_SSL_ENABLED')),
					'supercheckout_url' => __PS_BASE_URI__.'index.php?fc=module&module=supercheckout&controller=supercheckout',
					'addon_url' => __PS_BASE_URI__.'index.php?fc=module&module=supercheckoutpaymentaddon&controller=paymentaddon',
					'analytic_url' => __PS_BASE_URI__.'index.php?fc=module&module=supercheckoutanalyticaddon&controller=analyticaddon',
					'forgotten_link' => $this->context->link->getPageLink('password'),
					'my_account_url' => $this->context->link->getPageLink('my-account'),
					'payment_method_url' => $this->context->link->getModuleLink('', 'payment', array(), (bool)Configuration::get('PS_SSL_ENABLED')),
					'module_tpl_dir' => $this->module_dir.'views/templates/front/',
					'logged' => $this->is_logged,
					'default_country' => $default_country,
					'user_type' => ($this->is_logged) ? 'logged' : 'guest',
					'default_payment_selected' => $this->supercheckout_settings['payment_method']['default'],
					'genders' => Gender::getGenders(),
					'years' => Tools::dateYears(),
					'months' => $translated_months,
					'days' => Tools::dateDays(),
					'need_dni' => Country::isNeedDniByCountryId($default_country),
					'need_vat' => $this->isNeedVat(),
					'guest_enable_by_system' => Configuration::get('PS_GUEST_CHECKOUT_ENABLED'),
					'iso_code' => $this->context->language->iso_code
				);
			else
				$plugin_settings = array(
					'plugin_name' => $this->name,
					'settings' => $this->supercheckout_settings,
					'module_image_path' => _PS_BASE_URL_._MODULE_DIR_.'supercheckout/views/img/front/',
					'module_url' => $this->context->link->getModuleLink('supercheckout', 'supercheckout', array(), (bool)Configuration::get('PS_SSL_ENABLED')),
					'supercheckout_url' => __PS_BASE_URI__.'index.php?fc=module&module=supercheckout&controller=supercheckout',
					'addon_url' => __PS_BASE_URI__.'index.php?fc=module&module=supercheckoutpaymentaddon&controller=paymentaddon',
					'analytic_url' => __PS_BASE_URI__.'index.php?fc=module&module=supercheckoutanalyticaddon&controller=analyticaddon',
					'forgotten_link' => $this->context->link->getPageLink('password'),
					'my_account_url' => $this->context->link->getPageLink('my-account'),
					'payment_method_url' => $this->context->link->getModuleLink('', 'payment', array(), (bool)Configuration::get('PS_SSL_ENABLED')),
					'module_tpl_dir' => $this->module_dir.'views/templates/front/',
					'logged' => $this->is_logged,
					'default_country' => $default_country,
					'user_type' => ($this->is_logged) ? 'logged' : 'guest',
					'default_payment_selected' => $this->supercheckout_settings['payment_method']['default'],
					'genders' => Gender::getGenders(),
					'years' => Tools::dateYears(),
					'months' => $translated_months,
					'days' => Tools::dateDays(),
					'need_dni' => Country::isNeedDniByCountryId($default_country),
					'need_vat' => $this->isNeedVat(),
					'guest_enable_by_system' => Configuration::get('PS_GUEST_CHECKOUT_ENABLED'),
					'iso_code' => $this->context->language->iso_code
				);

			$page_data = array_merge($page_data, $plugin_settings);

			$id_address_delivery = 0;
			$id_address_invoice = 0;
			if ($this->is_logged)
			{
				$customer_name = $this->context->customer->firstname.' '.$this->context->customer->lastname;
				$page_data = array_merge($page_data, array('customer_name' => $customer_name));
				$id_address_delivery = $this->context->cart->id_address_delivery;
				$id_address_invoice = $this->context->cart->id_address_delivery;
			}
			else
				$this->context->cart->id_customer = 0;

			$this->loadCarriers($default_country, 0, 0, '', $id_address_delivery, $this->supercheckout_settings['shipping_method']['default']);

			//Payment Methods
			$this->loadPaymentMethods($default_country, $id_address_delivery, $this->supercheckout_settings['payment_method']['default']);

			$page_data = array_merge($page_data, array('id_address_delivery' => $id_address_delivery, 'id_address_invoice' => $id_address_invoice,));

			//Set Same Invoice Address in cookie for later use
			$this->context->cookie->isSameInvoiceAddress = 1;
			$this->context->cookie->write();

			$cart_summary = $this->loadCart();

			$page_data = array_merge($page_data, $cart_summary);

			//Message Titles
			$messages = array(
				'notification' => $this->module->l('Notification'),
				'warning' => $this->module->l('Warning'),
				'product_remove_success' => $this->module->l('Products successfully removed'),
				'product_qty_update_success' => $this->module->l('Products quantity successfully updated')
			);

			$page_data = array_merge($page_data, $messages);

			$this->context->smarty->assign($page_data);

			$velsof_errors = array();
			if (isset($this->context->cookie->velsof_error) && $this->context->cookie->velsof_error)
			{
				$velsof_errors = unserialize($this->context->cookie->velsof_error);
				$this->context->cookie->velsof_error = null;
				$this->context->cookie->__unset($this->context->cookie->velsof_error);
			}

			if (isset($_REQUEST['message']) && Tools::getValue('message'))
				$velsof_errors[] = Tools::getValue('message');

			if (isset($_REQUEST['firstdataError']) && Tools::getValue('firstdataError'))
				$velsof_errors[] = Tools::getValue('firstdataError'); //fetching errors of first data payment method

			$this->context->smarty->assign(array('velsof_errors' => $velsof_errors));
		}

		$this->context->smarty->assign(array(
			'HOOK_LEFT_COLUMN' => null,
			'HOOK_RIGHT_COLUMN' => null
		));

		//Added to assign current version of prestashop in a new variable
		if (version_compare(_PS_VERSION_, '1.6.0.1', '<'))
			$this->context->smarty->assign('ps_version', 15);
		else
			$this->context->smarty->assign('ps_version', 16);
		$this->setTemplate('supercheckout.tpl');
	}

	private function processSubmitLogin()
	{
		$email = trim(Tools::getValue('supercheckout_email'));
		$passwd = trim(Tools::getValue('supercheckout_password'));

		if (empty($email))
			$this->json['error']['email'] = $this->module->l('An email address required.');
		elseif (!Validate::isEmail($email))
			$this->json['error']['email'] = $this->module->l('Invalid email address.');

		if (empty($passwd))
			$this->json['error']['password'] = $this->module->l('Password is required.');
		elseif (!Validate::isPasswd($passwd))
			$this->json['error']['password'] = sprintf($this->module->l('Invalid Password'), Validate::PASSWORD_LENGTH);
		if (empty($this->json['error']))
		{
			$customer = new Customer();
			$authentication = $customer->getByEmail(trim($email), trim($passwd));
			if (!$authentication || !$customer->id)
				$this->json['error']['general'] = $this->module->l('Authentication failed.');
			else
			{
				$this->context->cookie->id_compare = isset($this->context->cookie->id_compare)
					? $this->context->cookie->id_compare
					: CompareProduct::getIdCompareByIdCustomer($customer->id);
				$this->context->cookie->id_customer = (int)$customer->id;
				$this->context->cookie->customer_lastname = $customer->lastname;
				$this->context->cookie->customer_firstname = $customer->firstname;
				$this->context->cookie->logged = 1;
				$customer->logged = 1;
				$this->context->cookie->is_guest = $customer->isGuest();
				$this->context->cookie->passwd = $customer->passwd;
				$this->context->cookie->email = $customer->email;

				if ($customer->newsletter == 1)
				{
					if ($this->supercheckout_settings['mailchimp']['enable'] == 1)
						$this->addEmailToList($customer->email, $customer->firstname, $customer->lastname);
				}
				// Add customer to the context
				$this->context->customer = $customer;

				if (Configuration::get('PS_CART_FOLLOWING')
					&& (empty($this->context->cookie->id_cart)
					|| Cart::getNbProducts($this->context->cookie->id_cart) == 0)
					&& $id_cart = (int)Cart::lastNoneOrderedCart($this->context->customer->id))
					$this->context->cookie->id_cart = (int)$id_cart;

				// Update cart address
				$this->context->cart->id_customer = (int)$customer->id;
				$this->context->cart->id = $this->context->cookie->id_cart;
				$this->context->cart->id_address_delivery = Address::getFirstCustomerAddressId((int)$customer->id);

				$this->context->cart->id_address_invoice = Address::getFirstCustomerAddressId((int)$customer->id);
				$this->context->cart->secure_key = $customer->secure_key;
				$this->context->cart->update();
				$this->context->cart->autosetProductAddress();

				Hook::exec('actionAuthentication');

				// Login information have changed, so we check if the cart rules still apply
				CartRule::autoRemoveFromCart($this->context);
				CartRule::autoAddToCart($this->context);

				$this->json['success'] = $this->context->link->getModuleLink('supercheckout', 'supercheckout',
					array(), (bool)Configuration::get('PS_SSL_ENABLED'));
			}
		}
	}

	protected function socialLogin()
	{
		require_once(_PS_MODULE_DIR_.'supercheckout/libraries/http.php');
		require_once(_PS_MODULE_DIR_.'supercheckout/libraries/oauth_client.php');
		$client = new oauth_client_class;
		$custom_ssl_var = 0;
		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
			$custom_ssl_var = 1;

		if ((bool)Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1)
			$client->redirect_uri = _PS_BASE_URL_SSL_.__PS_BASE_URI__.'index.php?fc=module&module=supercheckout&controller=supercheckout';
		else
			$client->redirect_uri = _PS_BASE_URL_.__PS_BASE_URI__.'index.php?fc=module&module=supercheckout&controller=supercheckout';

		if ($this->social_login_type == 'fb')
		{
			$client->redirect_uri .= '&login_type=fb';
			$client->server = 'Facebook';
			$client->client_id = $this->supercheckout_settings['fb_login']['app_id'];
			$client->client_secret = $this->supercheckout_settings['fb_login']['app_secret'];
			$client->scope = 'email';
		}
		else if ($this->social_login_type == 'google')
		{
			$client->redirect_uri .= '&login_type=google';
			$client->offline = true;
			$client->server = 'Google';
			//$client->api_key = $this->supercheckout_settings['google_login']['app_id'];
			$client->client_id = $this->supercheckout_settings['google_login']['client_id'];
			$client->client_secret = $this->supercheckout_settings['google_login']['app_secret'];
			$client->scope = 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile';
		}
		$user = array();
		if (($success = $client->Initialize()))
		{
			if (($success = $client->Process()))
			{
				if ($this->social_login_type == 'fb')
				{
					if (Tools::strlen($client->access_token))
						$success = $client->CallAPI(
						'https://graph.facebook.com/me?fields=email,first_name,last_name,gender', 'GET', array(), array('FailOnAccessError' => true), $user);
						//		'https://graph.facebook.com/me', 'GET', array(), array('FailOnAccessError' => true), $user);
				}
				else if ($this->social_login_type == 'google')
				{
					if (Tools::strlen($client->authorization_error))
					{
						$client->error = $client->authorization_error;
						$success = false;
					}
					elseif (Tools::strlen($client->access_token))
						$success = $client->CallAPI(
							'https://www.googleapis.com/oauth2/v1/userinfo', 'GET', array(), array('FailOnAccessError' => true), $user);
				}
			}
			$success = $client->Finalize($success);
		}
		if ($client->exit)
		{
			exit;
			//$this->context->cookie->velsof_error = serialize(array($this->module->l('Not able to connect with social site')));
			//Tools::redirect($this->context->link->getModuleLink('supercheckout', 'supercheckout'));
		}

		$social_customer_array = array();
		if ($success)
		{
			if ($this->social_login_type == 'fb')
			{
				$social_customer_array['first_name'] = $user->first_name;
				$social_customer_array['last_name'] = $user->last_name;
			}
			else if ($this->social_login_type == 'google')
			{
				$social_customer_array['first_name'] = $user->given_name;
				$social_customer_array['last_name'] = $user->family_name;
			}
			$social_customer_array['gender'] = ($user->gender == 'male') ? 0 : 1;
			$social_customer_array['email'] = $user->email;
			$this->addEmailToList($social_customer_array['first_name'], $social_customer_array['email']);
		}
		else
		{
			$this->context->cookie->velsof_error = serialize(array($this->module->l('Not able to login with social site')));
			Tools::redirect($this->context->link->getModuleLink('supercheckout', 'supercheckout', array(), (bool)Configuration::get('PS_SSL_ENABLED')));
		}

		return $social_customer_array;
	}

	private function checkForDniandVat($id_country = 0)
	{
		$vars = array();
		$vars['is_need_dni'] = Country::isNeedDniByCountryId($id_country);
		$vars['is_need_vat'] = $this->isNeedVat();
		$vars['is_need_states'] = Country::containsStates($id_country);
		$vars['is_need_zip_code'] = Country::getNeedZipCode($id_country);
		return $vars;
	}

	private function isValidDni($dni)
	{
		$response = array();
		if ($dni == '' || !Validate::isDniLite($dni))
			$response['error'] = $this->module->l('DNI Error');
		else
			$response['success'] = true;
		return $response;
	}

	private function isNeedVat()
	{
		if (Module::isInstalled('vatnumber') && Module::getInstanceByName('vatnumber')->active && Configuration::get('VATNUMBER_MANAGEMENT'))
			return true;
		return false;
	}

	private function isValidVatNumber($vat_number)
	{
		$response = array();
		if (Module::isInstalled('vatnumber') && Module::getInstanceByName('vatnumber')->active && Configuration::get('VATNUMBER_CHECKING'))
		{
			include_once (_PS_MODULE_DIR_.'vatnumber/vatnumber.php');

			$service_response = VatNumber::WebServiceCheck($vat_number);
			if (count($service_response) > 0)
				$response['error'] = $service_response;
			else
				$response['success'] = true;
		}
		else
			$response['success'] = true;

		return $response;
	}

	private function loadInvoiceAddress($id_country = 0, $id_state = 0, $postcode = 0, $city = '', $id_address_invoice = 0)
	{
		if ($this->context->cart->isVirtualCart())
		{
			if (isset($this->context->cookie->isSameInvoiceAddress) && $this->context->cookie->isSameInvoiceAddress == 1)
			{
				$this->context->cart->id_address_invoice = $this->context->cart->id_address_delivery;
				return true;
			}

			if (empty($id_country))
				$id_country = Configuration::get('PS_COUNTRY_DEFAULT');

			if (empty($id_address_invoice))
			{
				if (isset($this->context->cookie->supercheckout_temp_address_invoice) && $this->context->cookie->supercheckout_temp_address_invoice > 0)
					$id_address_invoice = $this->context->cookie->supercheckout_temp_address_invoice;
			}
			if ($id_address_invoice == 0)
			{
				$invoice_address = new Address($id_address_invoice);
				$invoice_address->firstname = ' ';
				$invoice_address->lastname = ' ';
				$invoice_address->company = ' ';
				$invoice_address->address1 = ' ';
				$invoice_address->address2 = ' ';
				$invoice_address->phone_mobile = ' ';
				$invoice_address->vat_number = '';
				$invoice_address->city = $city;
				$invoice_address->id_country = $id_country;
				$invoice_address->id_state = $id_state;
				$invoice_address->postcode = $postcode;
				$invoice_address->other = '';
				$invoice_address->alias = $this->module->l('Title Delivery Alias').' - '.date('s').rand(0, 9);
				if ($invoice_address->save())
				{
					$this->context->cart->id_address_invoice = $invoice_address->id;
					$this->context->cookie->supercheckout_temp_address_invoice = $this->context->cart->id_address_invoice;
					$this->context->cart->update();
				}
			}
		}
		return true;
	}

	private function loadCarriers($id_country = 0, $id_state = 0, $postcode = '', $city = '', $id_address_delivery = 0, $default_carrier = null)
	{
		//Start - New Code
		if (empty($id_country))
			$id_country = Configuration::get('PS_COUNTRY_DEFAULT');

		$carriers = array();
		$deliveries = array();
		if ($this->context->cart->isVirtualCart())
		{
			if (empty($this->context->cart->id_address_invoice))
			{
				if (isset($this->context->cookie->supercheckout_temp_address_invoice) && $this->context->cookie->supercheckout_temp_address_invoice > 0)
					$id_address_invoice = $this->context->cookie->supercheckout_temp_address_invoice;
				else
					$id_address_invoice = 0;
				$invoice_address = new Address($id_address_invoice);
				$invoice_address->firstname = ' ';
				$invoice_address->lastname = ' ';
				$invoice_address->company = ' ';
				$invoice_address->address1 = ' ';
				$invoice_address->address2 = ' ';
				$invoice_address->phone_mobile = ' ';
				$invoice_address->vat_number = '';
				$invoice_address->city = ' ';
				$invoice_address->id_country = Configuration::get('PS_COUNTRY_DEFAULT');
				$invoice_address->id_state = 0;
				$invoice_address->postcode = 0;
				$invoice_address->other = '';
				$invoice_address->alias = $this->module->l('Title Delivery Alias').' - '.date('s').rand(0, 9);
				if ($invoice_address->save())
				{
					$this->context->cart->id_address_invoice = $invoice_address->id;
					$this->context->cart->update();
				}
				$this->context->cookie->supercheckout_temp_address_invoice = $this->context->cart->id_address_invoice;
			}
			$this->context->smarty->assign('carriers_count', count($carriers));
		}
		else
		{
			$delivery_address = null;
			$country = new Country($id_country);

			if ((!$this->is_logged || $this->is_logged) && empty($id_address_delivery))
			{
				$id_zone = $country->id_zone;

				if (isset($this->context->cookie->supercheckout_temp_address_delivery) && $this->context->cookie->supercheckout_temp_address_delivery > 0)
					$this->context->cart->id_address_delivery = $this->context->cookie->supercheckout_temp_address_delivery;
				else
					$this->context->cart->id_address_delivery = 0;

				$delivery_address = new Address($this->context->cart->id_address_delivery);

				if ($this->context->cart->id_address_delivery == 0)
				{
					if ($country->need_identification_number)
						$delivery_address->dni = '-';

					$delivery_address->firstname = ' ';
					$delivery_address->lastname = ' ';
					$delivery_address->company = ' ';
					$delivery_address->address1 = ' ';
					$delivery_address->address2 = ' ';
					$delivery_address->phone_mobile = ' ';
					$delivery_address->vat_number = ' ';
					$delivery_address->city = ' ';
					$delivery_address->postcode = 0;
					$delivery_address->phone = ' ';
					$delivery_address->alias = $this->module->l('Title Delivery Alias').' - '.date('s').rand(0, 9);
					$delivery_address->other = '';
				}
				$delivery_address->id_country = (int)$id_country;
				$delivery_address->id_state = (int)$id_state;
				if (!empty($postcode))
					$delivery_address->postcode = $postcode;

				if (!empty($city))
					$delivery_address->city = $city;

				if (!$delivery_address->save())
					$this->shipping_error[] = $this->module->l('Error occurred while creating new address');

				$this->context->cookie->supercheckout_temp_address_delivery = $delivery_address->id;
			}
			else
			{
				$this->context->cart->id_address_delivery = $id_address_delivery;
				$delivery_address = new Address($id_address_delivery);
				$delivery_address->deleted = 0;
				if (!$delivery_address->save())
					$this->shipping_error[] = $this->module->l('Error occurred while updating address');
			}

			if (Validate::isLoadedObject($delivery_address) && count($this->shipping_error) == 0)
			{
				$this->context->cart->id_address_delivery = $delivery_address->id;
				if ($this->context->cookie->isSameInvoiceAddress)
					$this->context->cart->id_address_invoice = $delivery_address->id;

				$this->context->cart->update();

				// Address has changed, so we check if the cart rules still apply
				CartRule::autoRemoveFromCart($this->context);
				CartRule::autoAddToCart($this->context);

				//As there is no multishipping, set each product delivery address with main delivery address
				$this->context->cart->setNoMultishipping();

				$id_zone = Address::getZoneById((int)$delivery_address->id);

				$this->context->country->id_zone = $id_zone;

				if (!Address::isCountryActiveById((int)$delivery_address->id))
					$this->shipping_error[] = $this->module->l('No Delivery Method Available for this Address');
				elseif (!Validate::isLoadedObject($delivery_address) || $delivery_address->deleted)
					$this->shipping_error[] = $this->module->l('No Delivery Method Available for this Address');

				if (!count($this->shipping_error))
				{
					$address = new Address($this->context->cart->id_address_delivery);
					$id_zone = Address::getZoneById($address->id);
					$carriers = $this->context->cart->simulateCarriersOutput();

					$checked = $this->context->cart->simulateCarrierSelectedOutput();
					$delivery_option_list = $this->context->cart->getDeliveryOptionList();

					$deliverdata = unserialize(Configuration::get('VELOCITY_SUPERCHECKOUT_DATA'));
					foreach ($delivery_option_list as $id_address => $option_list)
					{
						foreach ($option_list as $key => $option)
						{
							foreach ($option['carrier_list'] as $cid => $carrier)
							{
								foreach ($deliverdata['delivery_method'] as $did => $deliveryid)
								{
									if ($did == $carrier['instance']->id)
									{
										if ($deliveryid['logo']['title'] != '')
										{
											$custom_ssl_var = 0;
											if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
												$custom_ssl_var = 1;
											if ((bool)Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1)
												$delivery_logo_path = _PS_BASE_URL_SSL_.__PS_BASE_URI__.'modules/supercheckout/views/img/admin/uploads/'.
												$deliveryid['logo']['title'];
											else
												$delivery_logo_path = _PS_BASE_URL_.__PS_BASE_URI__.'modules/supercheckout/views/img/admin/uploads/'.$deliveryid['logo']['title'];
											$delivery_path = _PS_ROOT_DIR_.'/modules/supercheckout/views/img/admin/uploads/'.$deliveryid['logo']['title'];
											if (file_exists($delivery_path))
											{
												$delivery_option_list[$id_address][$key]['carrier_list'][$cid]['logo'] = $delivery_logo_path;
												$delivery_option_list[$id_address][$key]['carrier_list'][$cid]['width'] = $deliveryid['logo']['resolution']['width'];
												$delivery_option_list[$id_address][$key]['carrier_list'][$cid]['height'] = $deliveryid['logo']['resolution']['height'];
											}
										}
										$lid = $this->context->language->id;
										$delivery_option_list[$id_address][$key]['carrier_list'][$cid]['instance']->name = $deliveryid['title'][$lid];
									}
								}
							}
						}
					}
					if (!empty($default_carrier)
						&& isset($delivery_option_list[$id_address_delivery])
						&& array_key_exists($default_carrier.',', $delivery_option_list[$id_address_delivery]))
						$this->context->cart->setDeliveryOption(array($id_address_delivery => $default_carrier.','));
					else
						$this->setDefaultCarrierSelection();

					$delivery_option = $this->context->cart->getDeliveryOption(null, false, false);
					$deliveries = array(
						'address_collection' => $this->context->cart->getAddressCollection(),
						'delivery_option_list' => $delivery_option_list,
						'carriers' => $carriers,
						'carriers_count' => count($carriers),
						'checked' => $checked,
						'delivery_option' => $delivery_option,
						'display_carrier_style' => $this->supercheckout_settings['shipping_method']['display_style'],
						'default_shipping_method' => $this->default_shipping_selected.','
					);

					if (!count($carriers))
						$this->shipping_error[] = $this->module->l('No Delivery Method Available for this Address');

					$_POST['id_address_delivery'] = $this->context->cart->id_address_delivery;
					if (!count($this->shipping_error))
					{
						$vars = array(
							'HOOK_BEFORECARRIER' => Hook::exec('displayBeforeCarrier', array(
								'carriers' => $carriers,
								'checked' => $checked,
								'delivery_option_list' => $delivery_option_list,
								'delivery_option' => $delivery_option
							))
						);

						Cart::addExtraCarriers($vars);

						$this->context->smarty->assign($vars);
					}
				}
			}
		}

		if (!count($this->shipping_error))
			$arr = array('IS_VIRTUAL_CART' => $this->context->cart->isVirtualCart());
		else
		{
			$arr = array(
				'IS_VIRTUAL_CART' => $this->context->cart->isVirtualCart(),
				'hasError' => !empty($this->shipping_error),
				'shipping_error' => $this->shipping_error,
			);
		}

		$page_data = array_merge($deliveries, $arr, $this->assignWrappingTOS());

		$this->context->smarty->assign($page_data);

		// Add checking for all addresses
		$address_without_carriers = $this->context->cart->getDeliveryAddressesWithoutCarriers();
		if (count($address_without_carriers) && !$this->context->cart->isVirtualCart())
		{
			if (count($address_without_carriers) > 1)
				$this->shipping_error[] = $this->module->l('No Delivery Method Available for this Address');
			elseif ($this->context->cart->isMultiAddressDelivery())
				$this->shipping_error[] = $this->module->l('No Delivery Method Available for this Address');
			else
				$this->shipping_error[] = $this->module->l('No Delivery Method Available for this Address');
		}

		if (Tools::isSubmit('ajax') && Tools::isSubmit('method'))
		{
			$temp_vars = array(
				'hasError' => !empty($this->shipping_error),
				'carriers_count' => count($carriers),
				'is_cart_virtual' => $this->context->cart->isVirtualCart(),
				'errors' => $this->shipping_error,
				'carrier_block' => $this->context->smarty->fetch(_PS_MODULE_DIR_.'supercheckout/views/templates/front/order-shipping.tpl'),
				'order-shipping-extra' => $this->context->smarty->fetch(_PS_MODULE_DIR_.'supercheckout/views/templates/front/order-shipping-extra.tpl'),
				'HOOK_EXTRACARRIER' => Hook::exec('displayCarrierList', array('address' => Context::getContext()->cart->getAddressCollection()))
			);

			return $temp_vars;
		}
	}

	protected function setDefaultCarrierSelection()
	{
		if (!$this->context->cart->getDeliveryOption(null, true))
			$this->context->cart->setDeliveryOption($this->context->cart->getDeliveryOption());
	}

	private function getCarrierList()
	{
		$carriers = $this->context->cart->simulateCarriersOutput();
		$old_message = Message::getMessageByCartId((int)$this->context->cart->id);
		$checked = $this->context->cart->simulateCarrierSelectedOutput();
		$delivery_option_list = $this->context->cart->getDeliveryOptionList();
		$delivery_option = $this->context->cart->getDeliveryOption(null, false, false);
		$deliveries = array();

		$deliveries = array(
			'HOOK_EXTRACARRIER' => Hook::exec('displayCarrierList', array('address' => Context::getContext()->cart->getAddressCollection())),
			'HOOK_EXTRACARRIER_ADDR' => null,
			'delivery_option_list' => $delivery_option_list,
			'oldMessage' => isset($old_message['message']) ? $old_message['message'] : '',
			'carriers' => $carriers,
			'checked' => $checked,
			'delivery_option_list' => $delivery_option_list,
			'delivery_option' => $delivery_option,
			'HOOK_BEFORECARRIER' => Hook::exec('displayBeforeCarrier', array(
				'carriers' => $carriers,
				'checked' => $checked,
				'delivery_option_list' => $delivery_option_list,
				'delivery_option' => $delivery_option
			))
		);

		$arr = array(
			'IS_VIRTUAL_CART' => $this->context->cart->isVirtualCart(),
		);
		$page_data = array_merge($deliveries, $arr);

		$this->context->smarty->assign($page_data);

		$temp_vars = array(
			'carrier_block' => $this->context->smarty->fetch(_PS_MODULE_DIR_.'supercheckout/views/templates/front/order-shipping.tpl'),
			'order-shipping-extra' => $this->context->smarty->fetch(_PS_MODULE_DIR_.'supercheckout/views/templates/front/order-shipping-extra.tpl')
		);

		return $temp_vars;
	}

	private function updateCarrier()
	{
		$error = array();
		if (Tools::getIsset('delivery_option'))
		{
			if ($this->validateDeliveryOption(Tools::getValue('delivery_option')))
				$this->context->cart->setDeliveryOption(Tools::getValue('delivery_option'));
		}
		elseif (Tools::getIsset('id_carrier'))
		{
			$delivery_option_list = $this->context->cart->getDeliveryOptionList();
			if (count($delivery_option_list) == 1)
			{
				$key = Cart::desintifier(Tools::getValue('id_carrier'));
				foreach ($delivery_option_list as $id_address => $options)
					if (isset($options[$key]))
						$this->context->cart->setDeliveryOption(array($id_address => $key));
			}
		}

		Hook::exec('actionCarrierProcess', array('cart' => $this->context->cart));

		if (!$this->context->cart->update())
			$error[] = 'Error occurred updating cart.';

		// Carrier has changed, so we check if the cart rules still apply
		CartRule::autoRemoveFromCart($this->context);
		CartRule::autoAddToCart($this->context);

		return array('hasError' => !empty($error), 'errors' => $error);
	}

	/**
	 * Validate get/post param delivery option
	 * @param array $delivery_option
	 */
	protected function validateDeliveryOption($delivery_option)
	{
		if (!is_array($delivery_option))
			return false;

		foreach ($delivery_option as $option)
			if (!preg_match('/(\d+,)?\d+/', $option))
				return false;

		return true;
	}

	protected function loadPaymentMethods($id_country = 0, $id_address_delivery = 0, $selected_payment_method = 0)
	{
		$payment_methods = array();

		$context = Context::getContext();
		$id_country = $id_country;
		if (isset($context->cart) && $id_address_delivery)
		{
			$billing = new Address((int)$id_address_delivery);
			$id_country = $billing->id_country;
		}

		$use_groups = Group::isFeatureActive();

		$frontend = true;
		$groups = array();
		if (isset($context->employee))
			$frontend = false;
		elseif (isset($context->customer) && $use_groups)
		{
			$groups = $context->customer->getGroups();
			if (!count($groups))
				$groups = array(Configuration::get('PS_UNIDENTIFIED_GROUP'));
		}

		$hook_payment = 'Payment';
		if (Db::getInstance()->getValue('SELECT `id_hook` FROM `'._DB_PREFIX_.'hook` WHERE `name` = \'displayPayment\''))
			$hook_payment = 'displayPayment';

		$paypal_condition = '';
		$iso_code = Country::getIsoById((int)$id_country);
		$paypal_countries = array('ES', 'FR', 'PL', 'IT');
		if (Context::getContext()->getMobileDevice() && Context::getContext()->shop->getTheme() == 'default' && in_array($iso_code, $paypal_countries))
			$paypal_condition = ' AND m.`name` = \'paypal\'';

		$list = Shop::getContextListShopID();

		$methods = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT DISTINCT m.`id_module`, h.`id_hook`, m.`name`, hm.`position`
			FROM `'._DB_PREFIX_.'module` m
			'.($frontend ? 'LEFT JOIN `'._DB_PREFIX_.'module_country` mc
			ON (m.`id_module` = mc.`id_module` AND mc.id_shop = '.(int)$context->shop->id.')' : '').'
			'.($frontend && $use_groups ? 'INNER JOIN `'._DB_PREFIX_.'module_group` mg 
			ON (m.`id_module` = mg.`id_module` AND mg.id_shop = '.(int)$context->shop->id.')' : '').'
			'.($frontend && $this->is_logged && $use_groups ? 'INNER JOIN `'._DB_PREFIX_.'customer_group` cg 
			on (cg.`id_group` = mg.`id_group`AND cg.`id_customer` = '.(int)$context->customer->id.')' : '').'
			LEFT JOIN `'._DB_PREFIX_.'hook_module` hm ON hm.`id_module` = m.`id_module`
			LEFT JOIN `'._DB_PREFIX_.'hook` h ON hm.`id_hook` = h.`id_hook`
			WHERE h.`name` = \''.pSQL($hook_payment).'\'
			'.(($frontend) ? 'AND mc.id_country = '.(int)$id_country : '').'
			AND (SELECT COUNT(*) FROM '._DB_PREFIX_.'module_shop ms 
				WHERE ms.id_module = m.id_module AND ms.id_shop IN('.implode(', ', $list).')) = '.(int)count($list).'
			AND hm.id_shop IN('.implode(', ', $list).')
			'.((count($groups) && $frontend && $use_groups) ? 'AND (mg.`id_group` IN ('.implode(', ', $groups).'))' : '').pSQL($paypal_condition).'
			GROUP BY hm.id_hook, hm.id_module
			ORDER BY hm.`position`, m.`name` DESC');

		$payment_number = 0;
		$hook_args = array('cookie' => $this->context->cookie, 'cart' => $this->context->cart);
		$counter = 0;
		if ($methods)
		{
			foreach ($methods as $module)
			{
				//Code for Ship2pay start here
				$skip = 0;
				if (!$this->context->cart->isVirtualCart())
				{
					foreach ($this->context->cart->getDeliveryOption() as $options)
					{
						$options = str_replace(',', '', $options);	//to replace comma after shipping method id eg: 23, => 23
						if (isset($this->supercheckout_settings[$options][$module['id_module']]))
							$skip = 1;
					}
				}
				if ($skip == 1)
					continue;
				// Code for ship2pay ends here

				if (($module_instance = Module::getInstanceByName($module['name']))
					&& ( is_callable(array($module_instance, 'hookpayment'))
					|| is_callable(array($module_instance, 'hookDisplayPayment'))))
				{
					$currency_ids = array();
					$currency_data = Currency::checkPaymentCurrencies($module_instance->id);
					foreach ($currency_data as $currency)
						$currency_ids[] = $currency['id_currency'];

					if (!in_array($this->context->currency->id, $currency_ids))
					{
						if (!in_array(-1, $currency_ids) && !in_array(-2, $currency_ids))
							continue;
					}

					if (!$module_instance->currencies || ( $module_instance->currencies && count(Currency::checkPaymentCurrencies($module_instance->id))))
					{
						if (is_callable(array($module_instance, 'hookPayment')) || is_callable(array($module_instance, 'hookDisplayPayment')))
						{
							$is_hook = false;
							$html = '';
							$display_name = '';
							if ($module['name'] == 'twenga')
							{
								if (is_callable(array($module_instance, 'hookPayment')))
									$html .= call_user_func(array($module_instance, 'hookPayment'), $hook_args);
								if (empty($html) && is_callable(array($module_instance, 'hookDisplayPayment')))
									$html = call_user_func(array($module_instance, 'hookDisplayPayment'), $hook_args);
								$is_hook = true;
							}
							else
							{
								if ($module['name'] == 'brinkscheckout')
								{
									$this->context->controller->addCSS(_PS_MODULE_DIR_.'brinkscheckout/css/2checkout.css', 'all');
									$key_config = Configuration::get('TWOCHECKOUT_SID');
									if (Configuration::get('TWOCHECKOUT_SANDBOX'))
										$this->context->controller->addJS('https://sandbox.2checkout.com/checkout/api/script/publickey/'.$key_config);
									else
										$this->context->controller->addJS('https://www.2checkout.com/checkout/api/script/publickey/'.$key_config);
								}
								else if ($module['name'] == 'twocheckout')
								{
									if (Configuration::get('TWOCHECKOUT_SANDBOX'))
										$this->context->controller->addJS('https://sandbox.2checkout.com/checkout/api/script/publickey/'.Configuration::get('TWOCHECKOUT_SID').'');
									else
										$this->context->controller->addJS('https://www.2checkout.com/checkout/api/script/publickey/'.Configuration::get('TWOCHECKOUT_SID').'');
								$this->smarty->assign('twocheckout_sid', Configuration::get('TWOCHECKOUT_SID'));
								$this->smarty->assign('twocheckout_public_key', Configuration::get('TWOCHECKOUT_PUBLIC'));
								}
							}

							$is_hook = true;
							if (is_callable(array($module_instance, 'hookPayment')))
								$html = call_user_func(array($module_instance, 'hookPayment'), $hook_args);
							if (empty($html) && is_callable(array($module_instance, 'hookDisplayPayment')))
								$html = call_user_func(array($module_instance, 'hookDisplayPayment'), $hook_args);
							$html = str_replace('&amp;', '&', $html);
//							if($module['name']== 'braintreejs')
//							$html = '<p class="payment_module"><a href="http://wwww.testsstite.com/index.php?fc=module&module=braintreejs&controller=payment"'
//								. ' title="Pay by Credit Card or Paypal"><img rel="Visa" alt="" src="/modules/braintreejs/views/img/cc-visa-logo.png" />'
//								. '<img rel="MasterCard" alt="" src="/modules/braintreejs/views/img/cc-mastercard-logo.png" />'
//								. '<img rel="American Express" alt="" src="/modules/braintreejs/views/img/cc-amex_logo.png" />'
//								. 'Pay by Credit Card or Paypal</a></p>';
							$additional_payment_methods = array();

							preg_match_all('/<a.*?>.*?<img.*?src="(.*?)".*?\/?>(.*?)<\/a>/ms', $html, $matches_1, PREG_SET_ORDER);
							preg_match_all('/<input .*?type="image".*?src="(.*?)".*?>.*?<span.*?>(.*?)<\/span>/ms', $html, $matches_2, PREG_SET_ORDER);
							$matches = array_merge($matches_1, $matches_2);

							foreach ($matches as $match)
							{
								$additional_payment_methods[$module_instance->id.'_'.$payment_number]['img'] = preg_replace('/(\r)?\n/m', ' ', trim($match[1]));
								$additional_payment_methods[$module_instance->id.'_'.$payment_number]['description'] = preg_replace('/\s/m', ' ', trim($match[2]));

								$payment_number++;
							}
						}
						$html = trim(preg_replace('/\s\s+/', ' ', $html)); // adding to fix bankwire payment methods
						if ($is_hook && !empty($html))
						{
							preg_match('/<a\s+(.*)href=\"(.*?)\"/i', $html, $fetch_module_url);

							if (isset($fetch_module_url[2]))
								$payment_module_url = $fetch_module_url[2];
							else
								$payment_module_url = '';

							if ($module['name'] == 'payu') // @Nitin Jain, 7-October-2015, Payu was geting css path in href link instead of php file.
								$payment_module_url = str_replace('themes/default/css/global.css', 'modules/payu/payment.php', $payment_module_url);

							if (($module['name'] == 'zipcheck' && $this->is_logged && $payment_module_url == '')
								|| ($module['name'] == 'zipcheck' && $payment_module_url == '' && Tools::isSubmit('ajax')))
								continue;

							//Get Image
							$payment_image_url = '';
							if ($this->supercheckout_settings['payment_method']['display_style'])
							{
								foreach ($this->image_extensions as $img_ext)
								{
									if (file_exists(_PS_MODULE_DIR_.$module['name'].'/'.$module['name'].$img_ext))
									{
										$custom_ssl_var = 0;
										if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
											$custom_ssl_var = 1;
										if ((bool)Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1)
											$payment_image_url = _PS_BASE_URL_SSL_._MODULE_DIR_.$module['name'].'/'.$module['name'].$img_ext;
										else
											$payment_image_url = _PS_BASE_URL_._MODULE_DIR_.$module['name'].'/'.$module['name'].$img_ext;
										break;
									}
								}
							}

							require_once( _PS_MODULE_DIR_.$module['name'].'/'.$module['name'].'.php' );
							if (class_exists($module['name'], false))
							{
								$pay_temp = new $module['name'];
								$display_name = $pay_temp->displayName;
							}
							else
								$display_name = $module['name'];

							// @Nitin Jain 23 July to fix bankwire issue on https url
							if ($module['name'] == 'bankwire')
							{
								if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
									$custom_ssl_var = 1;
								if ((bool)Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1)
								{
									if ((strpos($payment_module_url, 'https') !== false))
										$payment_module_url = $payment_module_url;
									else
										$payment_module_url = str_replace('http', 'https', $payment_module_url);
								}
							}

							$payment_methods['methods'][] = array(
								'id_module' => (int)$module_instance->id,
								'name' => $module['name'],
								'display_name' => $display_name,
								'payment_module_url' => $payment_module_url,
								'html' => $html, //base64_encode(($html)),
								'payment_image_url' => $payment_image_url,
								'additional' => $additional_payment_methods
							);
						}
					}
				}
			}
			$counter++;
		}
		else
			$payment_methods['warning'] = $this->module->l('No payment modules have been installed.');
		if ($counter == 0)  // added for ship2pay
		$payment_methods['not_required'] = $this->module->l('No payment method is available for your selected delivery method.');
		if ($this->context->cart->getOrderTotal(true) == 0)
			$payment_methods['not_required'] = $this->module->l('No payment method required.');
		$paymentdata = unserialize(Configuration::get('VELOCITY_SUPERCHECKOUT_DATA'));
		foreach ($payment_methods['methods'] as $payment_id => $values)
		{
			foreach ($paymentdata['payment_method'] as $key => $payid)
			{
				if ($payment_methods['methods'][$payment_id]['id_module'] == $key)
				{
					if ($payid['logo']['title'] != '')
					{
						$custom_ssl_var = 0;
						$temp = $values;
						unset($temp);
						if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
							$custom_ssl_var = 1;
						if ((bool)Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1)
							$logo_path = _PS_BASE_URL_SSL_.__PS_BASE_URI__.'modules/supercheckout/views/img/admin/uploads/'.$payid['logo']['title'];
						else
							$logo_path = _PS_BASE_URL_.__PS_BASE_URI__.'modules/supercheckout/views/img/admin/uploads/'.$payid['logo']['title'];
						$pay_path = _PS_ROOT_DIR_.'/modules/supercheckout/views/img/admin/uploads/'.$payid['logo']['title'];
						if (file_exists($pay_path))
						{
							$payment_methods['methods'][$payment_id]['payment_image_url'] = $logo_path;
							$payment_methods['methods'][$payment_id]['width'] = $payid['logo']['resolution']['width'];
							$payment_methods['methods'][$payment_id]['height'] = $payid['logo']['resolution']['height'];
						}
					}
					$lid = $this->context->language->id;
					$payment_methods['methods'][$payment_id]['display_name'] = $payid['title'][$lid];
				}
			}
		}

		$this->context->smarty->assign(array(
			'selected_payment_method' => $selected_payment_method,
			'display_payment_style' => $this->supercheckout_settings['payment_method']['display_style'],
			'payment_methods' => $payment_methods));

		if (Tools::isSubmit('ajax') && Tools::isSubmit('method'))
		{
			$temp_vars = array(
				'payment_method' => $this->context->smarty->fetch(_PS_MODULE_DIR_.'supercheckout/views/templates/front/payment-methods.tpl'),
				'payment_method_list' => $payment_methods
			);

			return $temp_vars;
		}
	}

	public function getPaymentInformation($payment_module_name)
	{
		$payment_method = array();
		$hook_args = array('cookie' => $this->context->cookie, 'cart' => $this->context->cart);
		if (($module_instance = Module::getInstanceByName($payment_module_name))
			&& ( is_callable(array($module_instance, 'hookpayment'))
			|| is_callable(array($module_instance, 'hookDisplayPayment'))))
		{
			if (!$module_instance->currencies || ( $module_instance->currencies && count(Currency::checkPaymentCurrencies($module_instance->id))))
			{

				if (is_callable(array($module_instance, 'hookPayment')) || is_callable(array($module_instance, 'hookDisplayPayment')))
				{
					$is_hook = false;
					$html = '';
					$display_name = '';
					if ($payment_module_name == 'twenga')
					{
						if (is_callable(array($module_instance, 'hookPayment')))
							$html .= call_user_func(array($module_instance, 'hookPayment'), $hook_args);
						if (empty($html) && is_callable(array($module_instance, 'hookDisplayPayment')))
							$html = call_user_func(array($module_instance, 'hookDisplayPayment'), $hook_args);
						$is_hook = true;
					}
					else
					{
						$is_hook = true;
						if (is_callable(array($module_instance, 'hookPayment')))
							$html = call_user_func(array($module_instance, 'hookPayment'), $hook_args);
						if (empty($html) && is_callable(array($module_instance, 'hookDisplayPayment')))
							$html = call_user_func(array($module_instance, 'hookDisplayPayment'), $hook_args);
					}

					if ($is_hook && !empty($html))
					{
						preg_match('/<a\s+(.*)href=\"(.*?)\"/i', $html, $fetch_module_url);
						if (isset($fetch_module_url[2]))
							$payment_module_url = $fetch_module_url[2];
						else
							$payment_module_url = '';

						require_once( _PS_MODULE_DIR_.$payment_module_name.'/'.$payment_module_name.'.php' );
						if (class_exists($payment_module_name, false))
						{
							$pay_temp = new $payment_module_name;
							$display_name = $pay_temp->displayName;
						}
						else
							$display_name = $payment_module_name;

						$payment_method = array(
							'id_module' => (int)$module_instance->id,
							'name' => $payment_module_name,
							'display_name' => $display_name,
							'payment_module_url' => $payment_module_url,
							'html' => $html
						);
					}
					else
						$payment_method['error'] = 'html empty';
				}
				else
					$payment_method['error'] = 'hook not callable';
			}
			else
				$payment_method['error'] = 'currency mismatch';
		}
		else
			$payment_method['error'] = 'not found';

		return $payment_method;
	}

	private function updateDeliveryExtra()
	{
		$error = array();
		$this->context->cart->recyclable = (int)Tools::getValue('recycle');
		$this->context->cart->gift = (int)Tools::getValue('gift');
		$gift_message = Tools::getValue('gift_message');
		if ($this->context->cart->gift && !empty($gift_message))
		{
			$gift_message = Tools::getValue('gift_message');
			if (!Validate::isMessage($gift_message))
				$error[] = $this->module->l('An error occurred while updating your cart');
			else
				$this->context->cart->gift_message = strip_tags($gift_message);
		}
		else if (!$this->context->cart->gift)
			$this->context->cart->gift_message = '';

		if (!$this->context->cart->update())
			$error[] = $this->module->l('An error occurred while updating your cart');

		// Carrier has changed, so we check if the cart rules still apply
		CartRule::autoRemoveFromCart($this->context);
		CartRule::autoAddToCart($this->context);

		return array('hasError' => !empty($this->errors), 'errors' => $this->errors);
	}

	private function loadCart()
	{
		$result = array();
		if (Configuration::get('PS_ORDER_PROCESS_TYPE') == 1)
			Cart::addExtraCarriers($result);

		$summary = $this->context->cart->getSummaryDetails(null, true);

		$customized_datas = Product::getAllCustomizedDatas($this->context->cart->id, null, true);

		$cart_product_context = Context::getContext()->cloneContext();
		foreach ($summary['products'] as &$product)
		{
			$product['quantity_without_customization'] = $product['quantity'];
			if ($customized_datas && isset($customized_datas[(int)$product['id_product']][(int)$product['id_product_attribute']]))
			{
				foreach ($customized_datas[(int)$product['id_product']][(int)$product['id_product_attribute']] as $addresses)
					foreach ($addresses as $customization)
						$product['quantity_without_customization'] -= (int)$customization['quantity'];
			}

			if ($cart_product_context->shop->id != $product['id_shop'])
				$cart_product_context->shop = new Shop((int)$product['id_shop']);

//			$product['price_without_quantity_discount'] = Product::getPriceStatic(
//				$product['id_product'],
//				!Product::getTaxCalculationMethod(),
//				$product['id_product_attribute'],
//				6,
//				null,
//				false,
//				false
//			);

			$null = false;
			$product['price_without_quantity_discount'] = Product::getPriceStatic(
				$product['id_product'],
				!Product::getTaxCalculationMethod(),
				$product['id_product_attribute'], 2, null, false, false, 1, false, null, null, null,
				$null, true, true, $cart_product_context
			);

//			$product['quantity'] = $product['cart_quantity']; // for compatibility with 1.2 themes

//			$null = false;
//			$product['price_without_specific_price'] = Product::getPriceStatic(
//					$product['id_product'],
//					!Product::getTaxCalculationMethod(),
//					$product['id_product_attribute'], 2, null, false, false, 1, false, null, null, null,
//					$null, true, true, $cart_product_context);

			if (Product::getTaxCalculationMethod())
				$product['is_discounted'] = $product['price_without_quantity_discount'] != $product['price'];
			else
				$product['is_discounted'] = $product['price_without_quantity_discount'] != $product['price_wt'];
		}

		// override customization tax rate with real tax (tax rules)
		if ($customized_datas)
		{
			foreach ($summary['products'] as &$product_update)
			{
				$product_id = (int)isset($product_update['id_product']) ? $product_update['id_product'] : $product_update['product_id'];
				$product_attribute_id = (int)isset($product_update['id_product_attribute'])
					? $product_update['id_product_attribute']
					: $product_update['product_attribute_id'];

				if (isset($customized_datas[$product_id][$product_attribute_id]))
					$product_update['tax_rate'] = Tax::getProductTaxRate($product_id, $this->context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
			}

			Product::addCustomizationPrice($summary['products'], $customized_datas);
		}

		// Get available cart rules and unset the cart rules already in the cart
		$available_cart_rules = $this->getCartRules();

		$show_option_allow_separate_package = (!$this->context->cart->isAllProductsInStock(true) && Configuration::get('PS_SHIP_WHEN_AVAILABLE'));

		$this->context->smarty->assign($summary);

		$cart_summary_extra = array(
			'token_cart' => Tools::getToken(false),
			'voucherAllowed' => CartRule::isFeatureActive(),
			'shippingCost' => $this->context->cart->getOrderTotal(true, Cart::ONLY_SHIPPING),
			'shippingCostTaxExc' => $this->context->cart->getOrderTotal(false, Cart::ONLY_SHIPPING),
			'customizedDatas' => $customized_datas,
			'CUSTOMIZE_FILE' => Product::CUSTOMIZE_FILE,
			'CUSTOMIZE_TEXTFIELD' => Product::CUSTOMIZE_TEXTFIELD,
			'lastProductAdded' => $this->context->cart->getLastProduct(),
			'displayVouchers' => $available_cart_rules,
			'currencySign' => $this->context->currency->sign,
			'currencyRate' => $this->context->currency->conversion_rate,
			'currencyFormat' => $this->context->currency->format,
			'currencyBlank' => $this->context->currency->blank,
			'show_option_allow_separate_package' => $show_option_allow_separate_package,
			'empty_cart_warning' => $this->module->l('Your cart is empty')
		);

		$summary = array_merge($summary, $cart_summary_extra, $this->assignWrappingTOS());

		$summary = array_merge($summary, array(
			'HOOK_SHOPPING_CART' => Hook::exec('displayShoppingCartFooter', $summary),
			'HOOK_SHOPPING_CART_EXTRA' => Hook::exec('displayShoppingCart', $summary),
			'customizedDatas' => Product::getAllCustomizedDatas($this->context->cookie->id_cart)
		));

		return $summary;
	}

	private function assignWrappingTOS()
	{
		// Wrapping fees
//		$wrapping_fees = $this->context->cart->getGiftWrappingPrice(false);
		$wrapping_fees_tax_inc = $wrapping_fees = $this->getGiftWrappingPrice();

		// TOS
		$cms = new CMS(Configuration::get('PS_CONDITIONS_CMS_ID'), $this->context->language->id);
		$this->link_conditions = $this->context->link->getCMSLink($cms, $cms->link_rewrite, (bool)Configuration::get('PS_SSL_ENABLED'));
		/*if (!strpos($this->link_conditions, '?'))
			$this->link_conditions .= '?content_only=1';
		else
			$this->link_conditions .= '&content_only=1';*/

		$free_shipping = false;
		foreach ($this->context->cart->getCartRules() as $rule)
		{
			if ($rule['free_shipping'] && !$rule['carrier_restriction'])
			{
				$free_shipping = true;
				break;
			}
		}

		$delivery_extras = array(
			'free_shipping' => $free_shipping,
			'show_TOS' => $this->supercheckout_settings['confirm']['term_condition'][($this->is_logged) ? 'logged' : 'guest']['display'],
			'checkedTOS' => $this->supercheckout_settings['confirm']['term_condition'][($this->is_logged) ? 'logged' : 'guest']['checked'],
			'recyclablePackAllowed' => (int)Configuration::get('PS_RECYCLABLE_PACK'),
			'giftAllowed' => (int)Configuration::get('PS_GIFT_WRAPPING'),
			'cms_id' => (int)Configuration::get('PS_CONDITIONS_CMS_ID'),
			'conditions' => (int)Configuration::get('PS_CONDITIONS'),
			'link_conditions' => $this->link_conditions,
			'cartGiftChecked' => $this->context->cart->gift,
			'recyclable' => (int)$this->context->cart->recyclable,
			'gift_wrapping_price' => (float)$wrapping_fees,
			'total_wrapping_cost' => Tools::convertPrice($wrapping_fees_tax_inc, $this->context->currency),
			'total_wrapping_tax_exc_cost' => Tools::convertPrice($wrapping_fees, $this->context->currency));

		return $delivery_extras;
	}

	public function getGiftWrappingPrice($with_taxes = true, $id_address = null)
	{
		static $address = null;

		$wrapping_fees = (float)Configuration::get('PS_GIFT_WRAPPING_PRICE');
		if ($with_taxes && $wrapping_fees > 0)
		{
			if ($address === null)
			{
				if ($id_address === null)
					$id_address = (int)$this->context->cart->id_address_delivery; //$id_address = (int)$this->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
				try {
					$address = Address::initialize($id_address);
				} catch (Exception $e) {
					$address = new Address();
					$address->id_country = Configuration::get('PS_COUNTRY_DEFAULT');
				}
			}

			$tax_manager = TaxManagerFactory::getManager($address, (int)Configuration::get('PS_GIFT_WRAPPING_TAX_RULES_GROUP'));
			$tax_calculator = $tax_manager->getTaxCalculator();
			$wrapping_fees = $tax_calculator->addTaxes($wrapping_fees);
		}

		return $wrapping_fees;
	}

	protected function addCartRule()
	{
		$discountarr = array();
		if (CartRule::isFeatureActive())
		{
			if (!($code = trim(Tools::getValue('discount_name'))))
				$discountarr['error'] = $this->module->l('You must enter a voucher code');
			elseif (!Validate::isCleanHtml($code))
				$discountarr['error'] = $this->module->l('The voucher code is invalid');
			else
			{
				if (($cart_rule = new CartRule(CartRule::getIdByCode($code))) && Validate::isLoadedObject($cart_rule))
				{
					if ($error = $cart_rule->checkValidity($this->context, false, true))
					{
						if (is_array($error))
							$discountarr['error'] = implode('<br>', $error);
						else
							$discountarr['error'] = $error;
					}
					else
					{
						$this->context->cart->addCartRule($cart_rule->id);
						$discountarr['success'] = $this->module->l('Voucher successfully applied');
					}
				}
				else
					$discountarr['error'] = $this->module->l('The voucher code is invalid');
			}
		}
		else
			$discountarr['error'] = $this->module->l('This feature is not active for this voucher');

		/* Is there only virtual product in cart */
		if ($this->context->cart->isVirtualCart())
		{
			//Set id_carrier to 0 (no shipping price)
			$this->context->cart->setDeliveryOption(null);
			$this->context->cart->update();
		}
		$available_cart_rules = $this->getCartRules();
		$cart_html = '';
		if ($available_cart_rules)
		{
			$msg = $this->module->l('Take advantage of our exclusive offers:');
			$cart_html .= '<p id="title" class="title-offers" style="font-weight: 600;color: black!important;">'.$msg.'</p>';
			foreach ($available_cart_rules as $cart_rule)
			{
				if ($cart_rule['code'] != '')
					$cart_html .= '<span onclick="$(\'#discount_name\').val(\''.$cart_rule['code'].
					'\');return false;" class="voucher_name" data-code="'.$cart_rule['code'].'">'.$cart_rule['code'].'</span> - ';
				$cart_html .= $cart_rule['name'].'<br />';
			}
		}
		$discountarr['cart_rule'] = $cart_html;
		return $discountarr;
	}

	protected function removeDiscount()
	{
		$discountarr = array();
		if (CartRule::isFeatureActive())
		{
			if (($id_cart_rule = (int)Tools::getValue('deleteDiscount')) && Validate::isUnsignedId($id_cart_rule))
			{
				$this->context->cart->removeCartRule($id_cart_rule);
				$discountarr['success'] = $this->module->l('Voucher successfully removed');
			}
			else
				$discountarr['error'] = $this->module->l('Error occured while removing voucher');
		}
		else
			$discountarr['error'] = $this->module->l('This feature is not active for this voucher');

		/* Is there only virtual product in cart */
		if ($this->context->cart->isVirtualCart())
		{
			$this->context->cart->setDeliveryOption(null);
			$this->context->cart->update();
		}
		$available_cart_rules = $this->getCartRules();
		$cart_html = '';
		if ($available_cart_rules)
		{
			$msg = $this->module->l('Take advantage of our exclusive offers:');
			$cart_html .= '<p id="title" class="title-offers" style="font-weight: 600;color: black!important;">'.$msg.'</p>';
			foreach ($available_cart_rules as $cart_rule)
			{
				if ($cart_rule['code'] != '')
					$cart_html .= '<span onclick="$(\'#discount_name\').val(\''.$cart_rule['code'].
						'\');return false;" class="voucher_name" data-code="'.$cart_rule['code'].'">'.$cart_rule['code'].'</span> - ';
				$cart_html .= $cart_rule['name'].'<br />';
			}
		}
		$discountarr['cart_rule'] = $cart_html;
		return $discountarr;
	}

	protected function getFormatedSummaryDetail()
	{
		$result = array('summary' => $this->context->cart->getSummaryDetails(null, true),
			'customizedDatas' => Product::getAllCustomizedDatas($this->context->cart->id, null, true));

		foreach ($result['summary']['products'] as &$product)
		{
			$product['quantity_without_customization'] = $product['quantity'];
			if ($result['customizedDatas'])
			{
				if (isset($result['customizedDatas'][(int)$product['id_product']][(int)$product['id_product_attribute']]))
					foreach ($result['customizedDatas'][(int)$product['id_product']][(int)$product['id_product_attribute']] as $addresses)
						foreach ($addresses as $customization)
							$product['quantity_without_customization'] -= (int)$customization['quantity'];
			}
		}

		if ($result['customizedDatas'])
			Product::addCustomizationPrice($result['summary']['products'], $result['customizedDatas']);
		return $result;
	}

	private function loggedInCustomer($customer_from_ocial)
	{
		$customer_obj = new Customer();
		$customer_tmp = $customer_obj->getByEmail($customer_from_ocial['email']);
		if (isset($customer_tmp->id) && $customer_tmp->id > 0)
			$customer = new Customer($customer_tmp->id);
		else
		{
			$is_guest = 0;
			$insertion_time = date('Y-m-d H:i:s', time());

			$original_passd = $this->generateRandomPassword(); //uniqid(rand(), true);

			$passd = Tools::encrypt($original_passd);

			$secure_key = md5(uniqid(rand(), true));

			$gender = Db::getInstance()->getRow('select id_gender from '._DB_PREFIX_.'gender where type = '.pSQL($customer_from_ocial['gender']));
			if (empty($gender))
				$gender['id_gender'] = 0;

			$sql = 'INSERT INTO '._DB_PREFIX_.'customer SET 
				id_shop_group = '.(int)$this->context->shop->id_shop_group.', 
				id_shop = '.(int)$this->context->shop->id.', 
				id_gender = '.(int)$gender['id_gender'].', 
				id_default_group = '.(int)Configuration::get('PS_CUSTOMER_GROUP').', 
				id_lang = '.(int)$this->context->language->id.', 
				id_risk = 0, firstname = "'.pSQL($customer_from_ocial['first_name']).'", 
				lastname = "'.pSQL($customer_from_ocial['last_name']).'", 
				email = "'.pSQL($customer_from_ocial['email']).'", 
				passwd = "'.pSQL($passd).'", max_payment_days = 0, secure_key = "'.pSQL($secure_key).'", 
				active = 1, is_guest = '.(int)$is_guest.', date_add = "'.pSQL($insertion_time).'", date_upd = "'.pSQL($insertion_time).'"';

			Db::getInstance()->execute($sql);
			$id_customer = Db::getInstance()->Insert_ID();

			$customer = new Customer();
			$customer->id = $id_customer;
			$customer->firstname = ucwords($customer_from_ocial['first_name']);
			$customer->lastname = ucwords($customer_from_ocial['last_name']);
			$customer->passwd = $passd;
			$customer->email = $customer_from_ocial['email'];
			$customer->secure_key = $secure_key;
			$customer->birthday = '';
			$customer->is_guest = (int)$is_guest;
			$customer->active = 1;
			$customer->logged = 1;

			$customer->cleanGroups();
			$customer->addGroups(array((int)Configuration::get('PS_CUSTOMER_GROUP')));

			$this->sendConfirmationMail($customer, $original_passd);
		}

		//Update Context
		$this->context->customer = $customer;
		$this->context->smarty->assign('confirmation', 1);
		$this->context->cookie->id_customer = (int)$customer->id;
		$this->context->cookie->customer_lastname = $customer->lastname;
		$this->context->cookie->customer_firstname = $customer->firstname;
		$this->context->cookie->passwd = $customer->passwd;
		$this->context->cookie->logged = 1;
		$this->context->cookie->email = $customer->email;
		$this->context->cookie->is_guest = $customer->is_guest;

		// Update cart
		if (Configuration::get('PS_CART_FOLLOWING')
			&& (empty($this->context->cookie->id_cart)
			|| Cart::getNbProducts($this->context->cookie->id_cart) == 0)
			&& $id_cart = (int)Cart::lastNoneOrderedCart($this->context->customer->id))
			$this->context->cart = new Cart($id_cart);
		else
		{
			$id_carrier = (int)$this->context->cart->id_carrier;
			$this->context->cart->id_carrier = 0;
			$this->context->cart->setDeliveryOption(null);
			$this->context->cart->id_address_delivery = (int)Address::getFirstCustomerAddressId((int)$customer->id);
			$this->context->cart->id_address_invoice = (int)Address::getFirstCustomerAddressId((int)$customer->id);
		}
		$this->context->cart->secure_key = $customer->secure_key;

		if (isset($id_carrier) && $id_carrier && Configuration::get('PS_ORDER_PROCESS_TYPE'))
		{
			$delivery_option = array($this->context->cart->id_address_delivery => $id_carrier.',');
			$this->context->cart->setDeliveryOption($delivery_option);
		}
		$this->context->cart->save();
		$this->context->cookie->id_cart = (int)$this->context->cart->id;
		$this->context->cookie->write();
		$this->context->cart->autosetProductAddress();

		return true;
	}

	protected function sendConfirmationMail($customer, $passd)
	{
		if (!Configuration::get('PS_CUSTOMER_CREATION_EMAIL'))
			return true;

		return Mail::Send(
			$this->context->language->id,
			'account',
			Mail::l('Welcome!'),
			array(
				'{firstname}' => $customer->firstname,
				'{lastname}' => $customer->lastname,
				'{email}' => $customer->email,
				'{passwd}' => $passd
			),
			$customer->email,
			$customer->firstname.' '.$customer->lastname
		);
	}

	public function checkZipCode($id_country, $postcode)
	{
		$arr = array();
		$zip_code_format = Country::getZipCodeFormat((int)$id_country);
		if (Country::getNeedZipCode((int)$id_country))
		{
			if ($zip_code_format)
			{
				$zip_regexp = '/^'.$zip_code_format.'$/ui';
				$zip_regexp = str_replace(' ', '( |)', $zip_regexp);
				$zip_regexp = str_replace('-', '(-|)', $zip_regexp);
				$zip_regexp = str_replace('N', '[0-9]', $zip_regexp);
				$zip_regexp = str_replace('L', '[a-zA-Z]', $zip_regexp);
				$zip_regexp = str_replace('C', Country::getIsoById((int)$id_country), $zip_regexp);

				if (!preg_match($zip_regexp, $postcode))
					$arr['error'] = $this->module->l('Invalid Zip Code').'<br />'
					.$this->module->l('Must be typed as follows:').' '
					.str_replace('C', Country::getIsoById((int)$id_country), str_replace('N', '0', str_replace('L', 'A', $zip_code_format)));
				else
					$arr['success'] = true;
			}
			elseif ($zip_code_format)
				$arr['error'] = $this->module->l('Required Field');
			elseif ($postcode && ! preg_match('/^[0-9a-zA-Z -]{4,9}$/ui', $postcode))
				$arr['error'] = $this->module->l('Invalid Zip Code');
			else
				$arr['success'] = true;
		}
		else
			$arr['success'] = true;

		return $arr;
	}

	public function createFreeOrder()
	{
//		return $this->context->controller->getController('ParentOrderController')->supercheckoutFreeOrder();
		$order = new FreeOrder();
		$order->free_order_class = true;
		$order->validateOrder($this->context->cart->id,
			Configuration::get('PS_OS_PAYMENT'), 0, Tools::displayError('Free order', false),
			null, array(), null, false, $this->context->cart->secure_key);
		$order_id = (int)Order::getOrderByCartId($this->context->cart->id);

		$order1 = new Order((int)$order_id);
		$email = $this->context->customer->email;
		if ($this->context->customer->is_guest)
			$this->context->customer->logout();

		return array('order_reference' => $order1->reference, 'email' => $email);
	}

	private function confirmOrder()
	{
		$response = array();
		$posted_data = $_POST;
		if (!isset($posted_data['payment_method']) && $this->context->cart->getOrderTotal(false, Cart::BOTH) != 0)
		{
			$response['error']['general'][] = $this->module->l('No payment method is selected.');
			return $response;
		}
		unset($_POST);

		if ($this->nb_products > 0)
		{
			if ($this->is_logged)
				$id_customer = $this->context->customer->id;
			else
				$id_customer = 0;

			$delivery_address = null;
			$invoice_address = null;

			if (isset($posted_data['checkout_option']) && $posted_data['checkout_option'] == 0)
			{
				if (!$this->is_logged)
				{
					$response['error']['general'][] = $this->module->l('Please login first');
					return $response;
				}
			}

			if (isset($posted_data['no_shipping_method']) && $posted_data['no_shipping_method'] == 1)
			{
				$response['error']['checkout_option'][] = array('key' => 'shipping_method_error',
						'error' => $this->module->l('No Shipping Method Selected.'));
				return $response;
			}

			if (!$this->context->cart->checkQuantities())
			{
				$msg = 'An item in your cart is no longer available in this quantity. You cannot proceed with your order until the quantity is adjusted.';
				$response['error']['general'][] = $this->module->l($msg);
				return $response;
			}

			$currency = Currency::getCurrency((int)$this->context->cart->id_currency);
			$minimal_purchase = Tools::convertPrice((float)Configuration::get('PS_PURCHASE_MINIMUM'), $currency);
			if ($this->context->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS) < $minimal_purchase)
			{
				$response['error']['general'][] = sprintf(
$this->module->l('A minimum purchase total of %1s (tax excl.) is required in order to validate your order, current purchase is %2s (tax excl.).'),
Tools::displayPrice($minimal_purchase, $currency), Tools::displayPrice($this->context->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS), $currency)
				);
				return $response;
			}

			$id_delivery_address = 0;
			if ((isset($posted_data['shipping_address_value']) && $posted_data['shipping_address_value'] == 1)
				|| !isset($posted_data['shipping_address_value']))
			{
				if (isset($this->context->cookie->supercheckout_temp_address_delivery)
					&& $this->context->cookie->supercheckout_temp_address_delivery > 0)
					$id_delivery_address = $this->context->cookie->supercheckout_temp_address_delivery;
			}
			else if (isset($posted_data['shipping_address_value']) && $posted_data['shipping_address_value'] == 0
				&& isset($posted_data['shipping_address_id']))
				$id_delivery_address = $posted_data['shipping_address_id'];

			$id_invoice_address = 0;
			if (isset($posted_data['use_for_invoice']))
				$id_invoice_address = $id_delivery_address;
			else if (((isset($posted_data['payment_address_value']) && $posted_data['payment_address_value'] == 1)
				|| !isset($posted_data['payment_address_value'])))
			{
				if (isset($this->context->cookie->supercheckout_temp_address_invoice)
					&& $this->context->cookie->supercheckout_temp_address_invoice > 0)
					$id_invoice_address = $this->context->cookie->supercheckout_temp_address_invoice;
				else
				{
					$temp_invoice_address = new Address();
					$temp_country_var = new Country((int)Configuration::get('PS_COUNTRY_DEFAULT'));
					if ($temp_country_var->need_identification_number)
						$temp_invoice_address->dni = '-';

					$temp_invoice_address->firstname = ' ';
					$temp_invoice_address->lastname = ' ';
					$temp_invoice_address->company = ' ';
					$temp_invoice_address->address1 = ' ';
					$temp_invoice_address->address2 = ' ';
					$temp_invoice_address->phone_mobile = ' ';
					$temp_invoice_address->vat_number = '';
					$temp_invoice_address->city = ' ';
					$temp_invoice_address->postcode = 0;
					$temp_invoice_address->phone = ' ';
					$temp_invoice_address->alias = $this->module->l('Title Delivery Alias').' - '.date('s').rand(0, 9);
					$temp_invoice_address->other = '';
					$temp_invoice_address->id_country = (int)Configuration::get('PS_COUNTRY_DEFAULT');
					$temp_invoice_address->id_state = 0;

					if (!$temp_invoice_address->save())
						$response['error']['general'][] = $this->module->l('Error occurred while creating new address');
					$id_invoice_address = $temp_invoice_address->id;
					$this->context->cookie->supercheckout_temp_address_invoice = $id_invoice_address;
				}
			}
			else if (!isset($posted_data['use_for_invoice']) && isset($posted_data['payment_address_value'])
				&& $posted_data['payment_address_value'] == 0)
				$id_invoice_address = $posted_data['payment_address_id'];

			//////////////////////////Start - Plugin Validations //////////////////////////

			//Set User Type and password according to user type
			$check_new_password = 0;
			if (isset($posted_data['checkout_option']) && $posted_data['checkout_option'] != 0)
			{
				$checkout_option = 1;
				$check_new_password = $posted_data['checkout_option'];
			}
			else
				$checkout_option = 0;

			$user_type = ($checkout_option == 0) ? 'logged' : 'guest';

			if (!$this->is_logged)
			{
				$email = $posted_data['supercheckout_email'];

				if ($email == '')
					$response['error']['checkout_option'][] = array('key' => 'supercheckout_email',
						'error' => $this->module->l('An email address required.'));
				else if (!Validate::isEmail($email))
					$response['error']['checkout_option'][] = array('key' => 'supercheckout_email',
						'error' => $this->module->l('Invalid email address.'));
				else if (Customer::customerExists($email) && isset($posted_data['checkout_option']) && $posted_data['checkout_option'] != 1)
					$response['error']['checkout_option'][] = array('key' => 'supercheckout_email',
						'error' => $this->module->l('This customer is already exist'));

				//Customer Personal Information
				foreach ($posted_data['customer_personal'] as $key => $value)
				{
					if ($key != 'dob_days' && $key != 'dob_months' && $key != 'dob_years')
					{
						if ($key == 'password')
						{
							if ($check_new_password == 2)
							{
								$new_password = $posted_data['customer_personal'][$key];
								if ($new_password == '')
									$response['error']['customer_personal'][] = array('key' => $key,
										'error' => $this->module->l('Password is required.'));
								else if (!(Tools::strlen($new_password) >= $this->password_length && Tools::strlen($new_password) < 255))
									$response['error']['customer_personal'][] = array('key' => $key,
										'error' => sprintf($this->module->l('Invalid Password'), Validate::PASSWORD_LENGTH));
							}
						}
						else
						{
							if (isset($this->supercheckout_settings['customer_personal'][$key][$user_type]['require'])
								&& $this->supercheckout_settings['customer_personal'][$key][$user_type]['require'] == 1
								&& !isset($posted_data['customer_personal'][$key]))
								$response['error']['customer_personal'][] = array('key' => $key,
									'error' => $this->module->l('Required Field'));
						}
					}
				}
				$check_dob = false;
				if (isset($posted_data['customer_personal']['dob_days'])
					&& isset($posted_data['customer_personal']['dob_months'])
					&& isset($posted_data['customer_personal']['dob_years']))
				{
					if ($this->supercheckout_settings['customer_personal']['dob'][$user_type]['require'] == 1 && $checkout_option == 1)
					{
						$check_dob = true;
						$birthday = (((empty($posted_data['customer_personal']['dob_years'])) ? '' : (int)$posted_data['customer_personal']['dob_years'])
							.'-'.((empty($posted_data['customer_personal']['dob_months'])) ? '' : (int)$posted_data['customer_personal']['dob_months'])
							.'-'.((empty($posted_data['customer_personal']['dob_days'])) ? '' : (int)$posted_data['customer_personal']['dob_days']));
						if (empty($birthday))
							$response['error']['customer_personal'][] = array('key' => 'dob', 'error' => $this->module->l('Required Field'));
						else if (!Validate::isBirthDate($birthday))
							$response['error']['customer_personal'][] = array('key' => 'dob', 'error' => $this->module->l('Invalid date of birth'));
					}
				}

			}
			else
				$checkout_option = 0;

			$shipping_address_value = 1;
			if (isset($posted_data['shipping_address_value']))
				$shipping_address_value = $posted_data['shipping_address_value'];

			$loop_index = 0;
			if ($shipping_address_value == 1)
			{
				foreach ($posted_data['shipping_address'] as $key => $value)
				{
					$add_plugin_config = $this->supercheckout_settings['shipping_address'][$key];
					if ($add_plugin_config['conditional'] == 0 && $add_plugin_config[$user_type]['require'] == 1 && $posted_data['shipping_address'][$key] == '')
						$response['error']['shipping_address'][$loop_index] = array('key' => $key, 'error' => $this->module->l('Required Field'));
					if (($key == 'phone_mobile' || $key == 'phone') && !empty($posted_data['shipping_address'][$key])
						&& !(boolean)Validate::isPhoneNumber($posted_data['shipping_address'][$key]))
						$response['error']['shipping_address'][$loop_index] = array('key' => $key, 'error' => $this->module->l('Invalid phone number'));
					if ($key == 'id_country')
					{
						$country = new Country($posted_data['shipping_address'][$key]);

						if ($posted_data['shipping_address'][$key] == 0)
							$response['error']['shipping_address'][$loop_index] = array('key' => $key,
								'error' => $this->module->l('Required Field'));
						else if (!$country->active)
							$response['error']['shipping_address'][$loop_index] = array('key' => $key,
								'error' => $this->module->l('This country is not active'));
						else if ((int)$country->contains_states && (isset($posted_data['shipping_address']['id_state'])
							&& !(int)$posted_data['shipping_address']['id_state']))
							$response['error']['shipping_address'][$loop_index] = array('key' => $key,
								'error' => $this->module->l('This country requires you to chose a State'));

						if (isset($posted_data['shipping_address']['postcode'])
							&& $postcode_error = $this->checkZipForCountry($country, $posted_data['shipping_address']['postcode'], true))
							$response['error']['shipping_address'][$loop_index] = $postcode_error;

						if ($this->supercheckout_settings['shipping_address']['dni'][$user_type]['require'] == 1 && $country->isNeedDni())
						{
						if (isset($posted_data['shipping_address']['dni']) && $country->isNeedDni()
							&& ($posted_data['shipping_address']['dni'] == '' || !Validate::isDniLite($posted_data['shipping_address']['dni'])))
							$response['error']['shipping_address'][$loop_index] = array('key' => 'dni', 'error' => $this->module->l('DNI Error'));
						}
					}
					if ($key == 'id_state' && $posted_data['shipping_address'][$key] == 0)
					{
						if (Country::containsStates((int)$posted_data['shipping_address']['id_country']))
							$response['error']['shipping_address'][$loop_index] = array('key' => $key, 'error' => $this->module->l('Required Field'));
					}
					if ($key == 'alias' && !empty($posted_data['shipping_address'][$key]))
					{
						$is_alias_onsame_id = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('select * from '._DB_PREFIX_.'address 
							where id_address = '.(int)$id_delivery_address.' AND alias = "'.pSQL($posted_data['shipping_address'][$key]).'"');
						if (!count($is_alias_onsame_id))
						{
							if ($this->aliasExistOveridden($posted_data['shipping_address'][$key], (int)$id_delivery_address, $id_customer))
								$response['error']['shipping_address'][$loop_index] = array('key' => $key,
									'error' => $this->module->l('This title has already taken'));
						}
					}
					$loop_index++;
				}
			}

			$payment_address_value = 1;
			if (isset($posted_data['payment_address_value']))
				$payment_address_value = $posted_data['payment_address_value'];

			if (!isset($posted_data['use_for_invoice']))
			{
				$loop_index = 0;
				if ($payment_address_value == 1)
				{
					foreach ($posted_data['payment_address'] as $key => $value)
					{
						$add_plugin_config = $this->supercheckout_settings['payment_address'][$key];
						if ($add_plugin_config['conditional'] == 0 && $add_plugin_config[$user_type]['require'] == 1
							&& $posted_data['payment_address'][$key] == '')
							$response['error']['payment_address'][$loop_index] = array('key' => $key,
								'error' => $this->module->l('Required Field'));
						if (($key == 'phone_mobile' || $key == 'phone') && !empty($posted_data['payment_address'][$key])
							&& !(boolean)Validate::isPhoneNumber($posted_data['payment_address'][$key]))
							$response['error']['payment_address'][$loop_index] = array('key' => $key,
								'error' => $this->module->l('Invalid phone number'));
						if ($key == 'id_country')
						{
							$country = new Country($posted_data['payment_address'][$key]);

							if ($posted_data['payment_address'][$key] == 0)
								$response['error']['payment_address'][$loop_index] = array('key' => $key, 'error' => $this->module->l('Required Field'));
							else if ((int)$country->contains_states && (isset($posted_data['payment_address']['id_state'])
								&& !(int)$posted_data['payment_address']['id_state']))
								$response['error']['payment_address'][$loop_index] = array('key' => $key,
									'error' => $this->module->l('This country requires you to chose a State'));
							else if (!$country->active)
								$response['error']['payment_address'][$loop_index] = array('key' => $key,
									'error' => $this->module->l('This country is not active'));
							if (isset($posted_data['payment_address']['postcode'])
								&& $postcode_error = $this->checkZipForCountry($country, $posted_data['payment_address']['postcode']))
								$response['error']['payment_address'][$loop_index] = $postcode_error;

							if (($this->supercheckout_settings['payment_address']['dni'][$user_type]['require'] == 1) && $country->isNeedDni())
							{
							if (isset($posted_data['payment_address']['dni']) && $country->isNeedDni()
								&& ($posted_data['payment_address']['dni'] == '' || !Validate::isDniLite($posted_data['payment_address']['dni'])))
								$response['error']['payment_address'][$loop_index] = array('key' => 'dni',
									'error' => $this->module->l('DNI Error'));
							}
						}
						if ($key == 'id_state' && $posted_data['payment_address'][$key] == 0)
						{
							if (Country::containsStates((int)$posted_data['payment_address']['id_country']))
								$response['error']['payment_address'][$loop_index] = array('key' => $key,
									'error' => $this->module->l('Required Field'));
						}
						if ($key == 'alias' && !empty($posted_data['payment_address'][$key]))
						{
							$is_alias_onsame_id = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('select * from '._DB_PREFIX_.'address 
								where id_address = '.(int)$id_invoice_address.' AND alias = "'.pSQL($posted_data['payment_address'][$key]).'"');
							if (!count($is_alias_onsame_id))
							{
								if ($this->aliasExistOveridden($posted_data['payment_address'][$key], (int)$id_invoice_address, $id_customer))
									$response['error']['payment_address'][$loop_index] = array('key' => $key,
										'error' => $this->module->l('This title has already taken'));
							}
						}
						$loop_index++;
					}
				}
			}

			if (isset($response['error']) && count($response['error']) > 0)
				return $response;

			//////////////////////////End - Plugin Validations //////////////////////////

			if ((isset($posted_data['shipping_address_value']) && $posted_data['shipping_address_value'] == 1)
				|| !isset($posted_data['shipping_address_value']))
			{
				$delivery_address = new Address($id_delivery_address);

				$delivery_address->firstname = (!empty($posted_data['shipping_address']['firstname'])) ? $posted_data['shipping_address']['firstname']: ' ';

				$delivery_address->lastname = (!empty($posted_data['shipping_address']['lastname'])) ? $posted_data['shipping_address']['lastname'] : ' ';

	$delivery_address->company = (!empty($posted_data['shipping_address']['company'])) ? $posted_data['shipping_address']['company'] : ' ';

				$delivery_address->address1 = (!empty($posted_data['shipping_address']['address1'])) ? $posted_data['shipping_address']['address1'] : ' ';

				$delivery_address->address2 = (!empty($posted_data['shipping_address']['address2'])) ? $posted_data['shipping_address']['address2'] : ' ';

				$delivery_address->city = (!empty($posted_data['shipping_address']['city'])) ? $posted_data['shipping_address']['city'] : ' ';

				$delivery_address->phone = (!empty($posted_data['shipping_address']['phone'])) ? $posted_data['shipping_address']['phone'] : ' ';

				$delivery_address->phone_mobile = (!empty($posted_data['shipping_address']['phone_mobile']))
						? $posted_data['shipping_address']['phone_mobile']
						: ' ';

				$delivery_address->id_country = (!empty($posted_data['shipping_address']['id_country']))
							? $posted_data['shipping_address']['id_country']
							: (int)Configuration::get('PS_COUNTRY_DEFAULT');

				$delivery_address->postcode = (!empty($posted_data['shipping_address']['postcode'])) ? $posted_data['shipping_address']['postcode'] : 0;
				if (!Country::getNeedZipCode($delivery_address->id_country))
					$delivery_address->postcode = 0;

				$delivery_address->id_state = (!empty($posted_data['shipping_address']['id_state'])) ? $posted_data['shipping_address']['id_state'] : 0;
				if (!Country::containsStates($delivery_address->id_country))
					$delivery_address->id_state = 0;

				$delivery_address->vat_number = (!empty($posted_data['shipping_address']['vat_number'])) ? $posted_data['shipping_address']['vat_number'] : '';

				$delivery_address->dni = (!empty($posted_data['shipping_address']['dni'])) ? $posted_data['shipping_address']['dni'] : '-';
				if (!Country::isNeedDniByCountryId($delivery_address->id_country))
					$delivery_address->dni = '-';

				$delivery_address->alias = (isset($posted_data['shipping_address']['alias']))
						? (empty($posted_data['shipping_address']['alias']))
							? $this->module->l('Title Delivery Alias').' - '.date('s').rand(0, 9)
							: $posted_data['shipping_address']['alias']
						: $this->module->l('Title Delivery Alias').' - '.date('s').rand(0, 9);
				$delivery_address->other = (!empty($posted_data['shipping_address']['other'])) ? $posted_data['shipping_address']['other'] : '';

				$delivery_address->id_customer = $id_customer;

				$validate_address = $delivery_address->validateController();
				if ($validate_address && count($validate_address) > 0)
				{
					$response['error']['shipping_address'] = array();
					foreach ($validate_address as $key => $value)
					{
						if ($key == '0')
							$response['error']['shipping_address'][] = array('key' => 'vat_number', 'error' => $value);
						else
							$response['error']['shipping_address'][] = array('key' => $key, 'error' => $value);
					}

				}
				else
				{
					if (!$delivery_address->save())
						$response['error']['general'][] = $this->module('Error occurred while creating new address');
					else
						$id_delivery_address = $delivery_address->id;
				}
			}
			else if (isset($posted_data['shipping_address_value']) && $posted_data['shipping_address_value'] == 0
				&& isset($posted_data['shipping_address_id']))
				$id_delivery_address = $posted_data['shipping_address_id'];

			if (isset($posted_data['use_for_invoice']) && ((isset($posted_data['shipping_address_value']) && $posted_data['shipping_address_value'] == 1)
				|| !isset($posted_data['shipping_address_value'])))
			{
				$invoice_address = $delivery_address;
				$id_invoice_address = $id_delivery_address;
			}
			else if (isset($posted_data['use_for_invoice']) && isset($posted_data['shipping_address_value'])
				&& $posted_data['shipping_address_value'] == 0)
				$id_invoice_address = $id_delivery_address;

			if (!isset($posted_data['use_for_invoice']) && ((isset($posted_data['payment_address_value']) && $posted_data['payment_address_value'] == 1)
				|| !isset($posted_data['payment_address_value'])))
			{
				$invoice_address = new Address($id_invoice_address);
				$invoice_address->firstname = (!empty($posted_data['payment_address']['firstname']))
								? $posted_data['payment_address']['firstname'] : ' ';
				$invoice_address->lastname = (!empty($posted_data['payment_address']['lastname']))
								? $posted_data['payment_address']['lastname'] : ' ';
				$invoice_address->company = (!empty($posted_data['payment_address']['company']))
								? $posted_data['payment_address']['company'] : ' ';
				$invoice_address->address1 = (!empty($posted_data['payment_address']['address1']))
								? $posted_data['payment_address']['address1'] : ' ';
				$invoice_address->address2 = (!empty($posted_data['payment_address']['address2']))
								? $posted_data['payment_address']['address2'] : ' ';
				$invoice_address->city = (!empty($posted_data['payment_address']['city']))
								? $posted_data['payment_address']['city'] : ' ';
				$invoice_address->phone = (!empty($posted_data['payment_address']['phone']))
								? $posted_data['payment_address']['phone'] : ' ';
				$invoice_address->phone_mobile = (!empty($posted_data['payment_address']['phone_mobile']))
								? $posted_data['payment_address']['phone_mobile'] : ' ';
				$invoice_address->id_country = (!empty($posted_data['payment_address']['id_country']))
								? $posted_data['payment_address']['id_country'] : (int)Configuration::get('PS_COUNTRY_DEFAULT');
				$invoice_address->postcode = (!empty($posted_data['payment_address']['postcode']))
								? $posted_data['payment_address']['postcode'] : 0;
				if (!Country::getNeedZipCode($invoice_address->id_country))
					$invoice_address->postcode = 0;
				$invoice_address->id_state = (!empty($posted_data['payment_address']['id_state']))
								? $posted_data['payment_address']['id_state'] : 0;
				if (!Country::containsStates($invoice_address->id_country))
					$invoice_address->id_state = 0;
				$invoice_address->vat_number = (!empty($posted_data['payment_address']['vat_number']))
								? $posted_data['payment_address']['vat_number'] : '';
				$invoice_address->dni = (!empty($posted_data['payment_address']['dni']))
								? $posted_data['payment_address']['dni'] : '-';
				if (!Country::isNeedDniByCountryId($invoice_address->id_country))
					$invoice_address->dni = '-';
				$invoice_address->alias = (isset($posted_data['payment_address']['alias']))
								? (empty($posted_data['payment_address']['alias']))
									? $this->module->l('Title Delivery Alias').' - '.date('s').rand(0, 9)
									: $posted_data['payment_address']['alias']
								: $this->module->l('Title Delivery Alias').' - '.date('s').rand(0, 9);
				$invoice_address->other = (!empty($posted_data['payment_address']['other'])) ? $posted_data['payment_address']['other'] : '';
				$invoice_address->id_customer = $id_customer;

				$validate_address = $invoice_address->validateController();
				if ($validate_address && count($validate_address) > 0)
				{
					$response['error']['payment_address'] = array();
					foreach ($validate_address as $key => $value)
						if ($key == '0')
							$response['error']['payment_address'][] = array('key' => 'vat_number', 'error' => $value);
						else
							$response['error']['payment_address'][] = array('key' => $key, 'error' => $value);
				}
				else
				{
					if (!$invoice_address->save())
						$response['error']['general'][] = $this->module('Error occurred while creating new address');
					else
						$id_invoice_address = $invoice_address->id;
				}
			}
			else if (!isset($posted_data['use_for_invoice']) && isset($posted_data['payment_address_value'])
				&& $posted_data['payment_address_value'] == 0)
				$id_invoice_address = $posted_data['payment_address_id'];

			//If any Error return
			if (isset($response['error']) && count($response['error']) > 0)
				return $response;

			$customer = null;

			if (!$this->is_logged)
			{
				$original_password = '';
				if ($posted_data['checkout_option'] == 2)
				{
					$_POST['is_new_customer'] = 1;
					$_POST['passwd'] = $posted_data['customer_personal']['password'];
					$original_password = Tools::getValue('passwd');
				}
				else
				{
					$_POST['is_new_customer'] = 0;
					$_POST['passwd'] = $this->generateRandomPassword(); //uniqid(rand(), true);
					if ($this->supercheckout_settings['enable_guest_register'])
					{
						$_POST['is_new_customer'] = 1;
						$original_password = Tools::getValue('passwd');
					}
				}
				$_POST['email'] = $posted_data['supercheckout_email'];
				$_POST['id_gender'] = (isset($posted_data['customer_personal']['id_gender']))?$posted_data['customer_personal']['id_gender']:0;

				if (empty($posted_data['shipping_address']['firstname'])
					&& $this->supercheckout_settings['shipping_address']['firstname'][$user_type]['require'] == 0)
				{
					if (isset($posted_data['payment_address']['firstname']) && !empty($posted_data['payment_address']['firstname']))
						$_POST['customer_firstname'] = $posted_data['payment_address']['firstname'];
					else
						$_POST['customer_firstname'] = ' ';
				}
				else
				{
					$_POST['customer_firstname'] = (isset($posted_data['shipping_address']['firstname']))
								? $posted_data['shipping_address']['firstname'] : '';
				}

				if (empty($posted_data['shipping_address']['lastname'])
					&& $this->supercheckout_settings['shipping_address']['lastname'][$user_type]['require'] == 0)
				{
						if (isset($posted_data['payment_address']['lastname']) && !empty($posted_data['payment_address']['lastname']))
							$_POST['customer_lastname'] = $posted_data['payment_address']['lastname'];
						else
							$_POST['customer_lastname'] = ' ';
				}
				else
				{
					$_POST['customer_lastname'] = (isset($posted_data['shipping_address']['lastname']))
								? $posted_data['shipping_address']['lastname'] : '';
				}
//				$_POST['newsletter'] = (isset($posted_data['customer_personal']['newsletter'])) ? 1 : 0;
				$newsletter = (isset($posted_data['customer_personal']['newsletter'])) ? 1 : 0;
				$_POST['optin'] = (isset($posted_data['customer_personal']['optin'])) ? 1 : 0;
				if ($check_dob)
				{
					$_POST['days'] = (isset($posted_data['customer_personal']['dob_days'])) ? $posted_data['customer_personal']['dob_days'] : '';
					$_POST['months'] = (isset($posted_data['customer_personal']['dob_months'])) ? $posted_data['customer_personal']['dob_months'] : '';
					$_POST['years'] = (isset($posted_data['customer_personal']['dob_years'])) ? $posted_data['customer_personal']['dob_years'] : '';
				}

				Hook::exec('actionBeforeSubmitAccount');

				$customer = new Customer();

				$customer->id_gender = Tools::getValue('id_gender');
				$customer->firstname = Tools::getValue('customer_firstname');
				$customer->lastname = Tools::getValue('customer_lastname');
				$customer->email = Tools::getValue('email');
				$customer->passwd = Tools::encrypt(Tools::getValue('passwd'));
				$customer->newsletter = $newsletter;
				$customer->optin = Tools::getValue('optin');
				$customer->secure_key = md5(uniqid(rand(), true));

				if (isset($posted_data['customer_personal']['newsletter']))
				{
					$this->processCustomerNewsletter($customer);
					if ($this->supercheckout_settings['mailchimp']['enable'] == 1)
						$this->addEmailToList($customer->email, $customer->firstname, $customer->lastname);
				}

				if ($check_dob)
					$customer->birthday = (int)Tools::getValue('years').'-'.(int)Tools::getValue('months').'-'.Tools::getValue('days');
				else
					$customer->birthday = '';

				$customer->active = 1;
				// New Guest customer
				if (Tools::isSubmit('is_new_customer'))
					$customer->is_guest = !Tools::getValue('is_new_customer', 1);
				else
					$customer->is_guest = 0;

				if (!$customer->add())
					$response['error']['general'][] = $this->module->l('An error occurred while creating your account.');
				else
				{
					$customer->cleanGroups();
					if (!$customer->is_guest)
					{
						// we add the guest customer in the default customer group
						$customer->addGroups(array((int)Configuration::get('PS_CUSTOMER_GROUP')));

						if (!$this->sendConfirmationMail($customer, $original_password))
							$response['warning'][] = $this->module->l('An error ocurred while sending account confirmation email');
					}
					else
						$customer->addGroups(array((int)Configuration::get('PS_GUEST_GROUP')));

					Hook::exec('actionCustomerAccountAdd', array(
						'_POST' => $_POST,
						'newCustomer' => $customer
					));
					$id_customer = $customer->id;
				}
			}
			else
				$id_customer = $this->context->customer->id;

			if (!isset($response['error']))
			{
				if (Validate::isLoadedObject($delivery_address) && $delivery_address != null)
				{
					$delivery_address->id_customer = $id_customer;
					if (!$delivery_address->save())
						$response['error']['general'][] = $this->module('Error occurred while updating address');
					else
						$id_delivery_address = $delivery_address->id;
				}

				if (Validate::isLoadedObject($invoice_address) && $invoice_address != null)
				{
					$invoice_address->id_customer = $id_customer;
					if (!$invoice_address->save())
						$response['error']['general'][] = $this->module('Error occurred while updating address');
					else
						$id_invoice_address = $invoice_address->id;
				}
			}

			if (isset($response['error']))
				return $response;

			if (!isset($response['error']))
			{
				// Add customer to the context
				if (!$this->is_logged)
				{
					$this->context->customer = $customer;
					$this->context->cookie->id_compare = isset($this->context->cookie->id_compare)
								? $this->context->cookie->id_compare : CompareProduct::getIdCompareByIdCustomer($customer->id);
					$this->context->cookie->id_customer = (int)$customer->id;
					$this->context->cookie->customer_lastname = $customer->lastname;
					$this->context->cookie->customer_firstname = $customer->firstname;
					$this->context->cookie->logged = 1;
					$customer->logged = 1;
					$this->context->cookie->is_guest = $customer->isGuest();
					$this->context->cookie->passwd = $customer->passwd;
					$this->context->cookie->email = $customer->email;
				}

				$this->context->cart->recyclable = (isset($posted_data['recyclable'])) ? 1 : 0;
				$this->context->cart->gift = (isset($posted_data['gift'])) ? 1 : 0;
				if (isset($posted_data['gift']))
					$this->context->cart->gift_message = strip_tags($posted_data['gift_comment']);

				if (isset($posted_data['comment']))
					$this->supercheckoutUpdateMsg($posted_data['comment']);

				if (Configuration::get('PS_CART_FOLLOWING') && (empty($this->context->cookie->id_cart)
					|| Cart::getNbProducts($this->context->cookie->id_cart) == 0))
					$this->context->cookie->id_cart = (int)Cart::lastNoneOrderedCart($this->context->customer->id);

				// Update cart address
				//$delivery_option = $this->context->cart->getDeliveryOption();
//				$this->context->cart->id_carrier = (isset($delivery_option[$id_delivery_address]))
//							? trim($delivery_option[$id_delivery_address], ',') : 0;
				$this->updateCarrier();
				$this->context->cart->id_customer = (int)$id_customer;
				$this->context->cart->id_address_delivery = $id_delivery_address;
				$this->context->cart->id_address_invoice = $id_invoice_address;
				$this->context->cart->secure_key = $this->context->customer->secure_key;

				$this->context->cart->save();
				$this->context->cookie->id_cart = (int)$this->context->cart->id;
				$this->context->cookie->write();
				//As there is no multishipping, set each product delivery address with main delivery address
				$this->context->cart->setNoMultishipping();
				$this->context->cart->autosetProductAddress();
			}
		}
		else
			$response['error']['general'][] = $this->module->l('Your Cart is Empty');

		if (!isset($response['error']))
		{
			$this->context->cookie->supercheckout_perm_address_delivery = $id_delivery_address;
			$this->context->cookie->supercheckout_perm_address_invoice = $id_invoice_address;
			$response['success'] = true;
		}

		return $response;
	}

	private function checkZipForCountry(Country $country, $postcode, $isshippingzipcode = false)
	{
		if ($this->context->cart->isVirtualCart() && $isshippingzipcode == true && $this->supercheckout_settings['hide_delivery_for_virtual'] == 0)
			return false;
		if ($country->zip_code_format && !$country->checkZipCode($postcode))
			return array('key' => 'postcode',
				'error' => $this->module->l('Invalid Zip Code').'<br>'
				.$this->module->l('Must be typed as follows:')
				.str_replace('C', $country->iso_code, str_replace('N', '0', str_replace('L', 'A', $country->zip_code_format))));
		else if (empty($postcode) && $country->need_zip_code)
			return array('key' => 'postcode', 'error' => $this->module->l('Required Field'));
		else if ($postcode && !Validate::isPostCode($postcode))
			return array('key' => 'postcode', 'error' => $this->module->l('Invalid Zip Code'));
		else
			return false;
	}

	private function getFormattedAddress()
	{
		// Getting a list of formated address fields with associated values
		$formated_adrress_values_list = array();
		$customer = $this->context->customer;
		if (Validate::isLoadedObject($customer))
		{
			/* Getting customer addresses */
			$customer_addresses = $customer->getAddresses($this->context->language->id);

			foreach ($customer_addresses as $address)
			{
				$tmp_address = new Address($address['id_address']);
				$formated_adrress_values_list[$address['id_address']]['ordered_fields'] = AddressFormat::getOrderedAddressFields($address['id_country']);
				$formated_adrress_values_list[$address['id_address']]['formated_fields_values'] = AddressFormat::getFormattedAddressFieldsValues(
						$tmp_address, $formated_adrress_values_list[$address['id_address']]['ordered_fields']);

				unset($tmp_address);
			}
		}
		return $formated_adrress_values_list;
	}

	/**
	 * Process the newsletter settings and set the customer infos.
	 *
	 * @param Customer $customer Reference on the customer Object.
	 *
	 * @note At this point, the email has been validated.
	 */
	protected function processCustomerNewsletter(&$customer)
	{
		if (Tools::getValue('newsletter'))
		{
			$customer->ip_registration_newsletter = pSQL(Tools::getRemoteAddr());
			$customer->newsletter_date_add = pSQL(date('Y-m-d H:i:s'));

			if ($module_newsletter = Module::getInstanceByName('blocknewsletter'))
				if ($module_newsletter->active)
					$module_newsletter->confirmSubscription(Tools::getValue('email'));
		}
	}

	private function generateRandomPassword()
	{
		$length = 8;
		$code = '';
		$chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ0123456789';
		$maxlength = Tools::strlen($chars);
		if ($length > $maxlength)
			$length = $maxlength;
		$i = 0;
		while ($i < $length)
		{
			$char = Tools::substr($chars, mt_rand(0, $maxlength - 1), 1);
			if (!strstr($code, $char))
			{
				$code .= $char;
				$i++;
			}
		}
		return $code;
	}

	/*Added the function as it is not there in Prestashop version 1.6.0.6 (By Raghu)*/
	public static function aliasExistOveridden($alias, $id_address, $id_customer)
	{
		$query = new DbQuery();
		$query->select('count(*)');
		$query->from('address');
		$query->where('alias = \''.pSQL($alias).'\'');
		$query->where('id_address != '.(int)$id_address);
		$query->where('id_customer = '.(int)$id_customer);
		$query->where('deleted = 0');

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
	}

	private function supercheckoutUpdateMsg($message_content)
	{
		if ($message_content)
		{
			if (!Validate::isMessage($message_content))
				$this->errors[] = Tools::displayError('Invalid message');
			else if ($old_message = Message::getMessageByCartId((int)$this->context->cart->id))
			{
				$message = new Message((int)$old_message['id_message']);
				$message->message = $message_content;
				$message->update();
			}
			else
			{
				$message = new Message();
				$message->message = $message_content;
				$message->id_cart = (int)$this->context->cart->id;
				$message->id_customer = (int)$this->context->cart->id_customer;
				$message->add();
			}
		}
		else
		{
			if ($old_message = Message::getMessageByCartId($this->context->cart->id))
			{
				$message = new Message($old_message['id_message']);
				$message->delete();
			}
		}
		return true;
	}

	public function addEmailToList($email, $fname = '', $lname = '')
	{
		$apikey = $this->supercheckout_settings['mailchimp']['api'];
		$mailchimp = new MailChimp($apikey);
		$listid = $this->supercheckout_settings['mailchimp']['list'];
		try
		{
			$mailchimp->call('lists/subscribe', array(
				'id' => $listid,
				'email' => array('email' => $email),
				'merge_vars' => array('FNAME' => $fname, 'LNAME' => $lname),
				'double_optin' => false, // use true if you want to send confirmation mail to customer like, click here to confirm your subscription.
				'update_existing' => true,
				'replace_interests' => false,
				'send_welcome' => false, // use true to send subscription success mail to customer on subscription
			));
		}
		catch (Exception $e)
		{
			return;
		}
	}

	protected function getCartRules()
	{
		$available_cart_rules = CartRule::getCustomerCartRules($this->context->language->id,
			(isset($this->context->customer->id) ? $this->context->customer->id : 0),
			true,
			true,
			true,
			$this->context->cart);
		$cart_cart_rules = $this->context->cart->getCartRules();
		foreach ($available_cart_rules as $key => $available_cart_rule)
		{
			if ((isset($available_cart_rule['highlight']) && !$available_cart_rule['highlight']) || strpos($available_cart_rule['code'], 'BO_ORDER_') === 0)
			{
				unset($available_cart_rules[$key]);
				continue;
			}
			foreach ($cart_cart_rules as $cart_cart_rule)
				if ($available_cart_rule['id_cart_rule'] == $cart_cart_rule['id_cart_rule'])
				{
					unset($available_cart_rules[$key]);
					continue 2;
				}
		}
		return $available_cart_rules;
	}

}
