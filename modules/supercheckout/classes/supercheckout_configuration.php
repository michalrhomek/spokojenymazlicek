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
 */

class SupercheckoutConfiguration
{
	/*
	 * return default settings of the supercheckout page
	 */

	public function getDefaultSettings()
	{
		$settings = array(
			'adv_id' => 0,
			'loginizer_adv' => 0,
			'plugin_id' => 'PS0002',
			'version' => '0.1',
			'enable' => 0,
			'enable_guest_checkout' => 1,
			'enable_guest_register' => 0,
			'checkout_option' => 0,
			'super_test_mode' => 0,
			'qty_update_option' => 0,
			'inline_validation' => array('enable' => 0),
			'social_login_popup' => array('enable' => 1),
			'fb_login' => array('enable' => 0, 'app_id' => '', 'app_secret' => ''),
			'mailchimp' => array('enable' => 0, 'api' => '', 'list' => '', 'default' => 0),
			'google_login' => array('enable' => 0, 'app_id' => '', 'client_id' => '',
				'app_secret' => ''),
			'customer_personal' => array(
				'id_gender' => array('id' => 'id_gender', 'title' => 'Title', 'sort_order' => 1,
					'guest' => array('require' => 1, 'display' => 1), 'logged' => array('require' => 1, 'display' => 1)),
				'dob' => array('id' => 'dob', 'title' => 'DOB', 'sort_order' => 2,
					'guest' => array('require' => 1, 'display' => 1), 'logged' => array('require' => 1, 'display' => 1))
			),
			'customer_subscription' => array(
				'newsletter' => array('id' => 'newsletter', 'title' => 'Sign up for NewsLetter', 'sort_order' => 3,
					'guest' => array('checked' => 0, 'display' => 1)),
				'optin' => array('id' => 'optin', 'sort_order' => 4, 'title' => 'Special Offer',
					'guest' => array('checked' => 0, 'display' => 1))
			),
			'hide_delivery_for_virtual'=> 0,
			'use_delivery_for_payment_add' => array('guest' => 1, 'logged' => 1),
			'show_use_delivery_for_payment_add' => array('guest' => 1, 'logged' => 1),
			'payment_address' => array(
				'firstname' => array('id' => 'firstname', 'title' => 'First Name', 'sort_order' => 1, 'conditional' => 0,
					'guest' => array('require' => 1, 'display' => 1), 'logged' => array('require' => 1, 'display' => 1)),
				'lastname' => array('id' => 'lastname', 'title' => 'Last Name', 'sort_order' => 2, 'conditional' => 0,
					'guest' => array('require' => 1, 'display' => 1), 'logged' => array('require' => 1, 'display' => 1)),
				'company' => array('id' => 'company', 'title' => 'Company', 'sort_order' => 4, 'conditional' => 0,
					'guest' => array('require' => 0, 'display' => 1), 'logged' => array('require' => 0, 'display' => 1)),
				'vat_number' => array('id' => 'vat_number', 'title' => 'Vat Number', 'sort_order' => 5, 'conditional' => 1,
					'guest' => array('require' => 1, 'display' => 1), 'logged' => array('require' => 1, 'display' => 1)),
				'address1' => array('id' => 'address1', 'title' => 'Address Line 1', 'sort_order' => 6, 'conditional' => 0,
					'guest' => array('require' => 1, 'display' => 1), 'logged' => array('require' => 1, 'display' => 1)),
				'address2' => array('id' => 'address2', 'title' => 'Address Line 2', 'sort_order' => 7, 'conditional' => 0,
					'guest' => array('require' => 0, 'display' => 1), 'logged' => array('require' => 0, 'display' => 1)),
				'postcode' => array('id' => 'postcode', 'title' => 'Zip/Postal Code', 'sort_order' => 8, 'conditional' => 1,
					'guest' => array('require' => 1, 'display' => 1), 'logged' => array('require' => 1, 'display' => 1)),
				'city' => array('id' => 'city', 'title' => 'City', 'sort_order' => 9, 'conditional' => 0,
					'guest' => array('require' => 1, 'display' => 1), 'logged' => array('require' => 1, 'display' => 1)),
				'id_country' => array('id' => 'id_country', 'title' => 'Country', 'sort_order' => 10, 'conditional' => 1,
					'guest' => array('require' => 1, 'display' => 1), 'logged' => array('require' => 1, 'display' => 1)),
				'id_state' => array('id' => 'id_state', 'title' => 'State', 'sort_order' => 11, 'conditional' => 1,
					'guest' => array('require' => 1, 'display' => 1), 'logged' => array('require' => 1, 'display' => 1)),
				'dni' => array('id' => 'dni', 'title' => 'Identification Number', 'sort_order' => 12, 'conditional' => 1,
					'guest' => array('require' => 1, 'display' => 1), 'logged' => array('require' => 1, 'display' => 1)),
				'phone' => array('id' => 'phone', 'title' => 'Home Phone', 'sort_order' => 13, 'conditional' => 0,
					'guest' => array('require' => 0, 'display' => 1), 'logged' => array('require' => 0, 'display' => 1)),
				'phone_mobile' => array('id' => 'phone_mobile', 'title' => 'Mobile Phone', 'sort_order' => 14, 'conditional' => 0,
					'guest' => array('require' => 1, 'display' => 1), 'logged' => array('require' => 1, 'display' => 1)),
				'alias' => array('id' => 'alias', 'title' => 'Address Title', 'sort_order' => 15, 'conditional' => 0,
					'guest' => array('require' => 1, 'display' => 1), 'logged' => array('require' => 1, 'display' => 1)),
				'other' => array('id' => 'other', 'title' => 'Other Information', 'sort_order' => 16, 'conditional' => 0,
					'guest' => array('require' => 0, 'display' => 1), 'logged' => array('require' => 0, 'display' => 1)),
			),
			'shipping_address' => array(
				'firstname' => array('id' => 'firstname', 'title' => 'First Name', 'sort_order' => 1, 'conditional' => 0,
					'guest' => array('require' => 1, 'display' => 1), 'logged' => array('require' => 1, 'display' => 1)),
				'lastname' => array('id' => 'lastname', 'title' => 'Last Name', 'sort_order' => 2, 'conditional' => 0,
					'guest' => array('require' => 1, 'display' => 1), 'logged' => array('require' => 1, 'display' => 1)),
				'company' => array('id' => 'company', 'title' => 'Company', 'sort_order' => 3, 'conditional' => 0,
					'guest' => array('require' => 0, 'display' => 1), 'logged' => array('require' => 0, 'display' => 1)),
				'vat_number' => array('id' => 'vat_number', 'title' => 'Vat Number', 'sort_order' => 4, 'conditional' => 1,
					'guest' => array('require' => 1, 'display' => 1), 'logged' => array('require' => 1, 'display' => 1)),
				'address1' => array('id' => 'address1', 'title' => 'Address Line 1', 'sort_order' => 5, 'conditional' => 0,
					'guest' => array('require' => 1, 'display' => 1), 'logged' => array('require' => 1, 'display' => 1)),
				'address2' => array('id' => 'address2', 'title' => 'Address Line 2', 'sort_order' => 6, 'conditional' => 0,
					'guest' => array('require' => 0, 'display' => 1), 'logged' => array('require' => 0, 'display' => 1)),
				'postcode' => array('id' => 'postcode', 'title' => 'Zip/Postal Code', 'sort_order' => 7, 'conditional' => 1,
					'guest' => array('require' => 1, 'display' => 1), 'logged' => array('require' => 1, 'display' => 1)),
				'city' => array('id' => 'city', 'title' => 'City', 'sort_order' => 8, 'conditional' => 0,
					'guest' => array('require' => 1, 'display' => 1), 'logged' => array('require' => 1, 'display' => 1)),
				'id_country' => array('id' => 'id_country', 'title' => 'Country', 'sort_order' => 9, 'conditional' => 1,
					'guest' => array('require' => 1, 'display' => 1), 'logged' => array('require' => 1, 'display' => 1)),
				'id_state' => array('id' => 'id_state', 'title' => 'State', 'sort_order' => 10, 'conditional' => 1,
					'guest' => array('require' => 1, 'display' => 1), 'logged' => array('require' => 1, 'display' => 1)),
				'dni' => array('id' => 'dni', 'title' => 'Identification Number', 'sort_order' => 11, 'conditional' => 1,
					'guest' => array('require' => 1, 'display' => 1), 'logged' => array('require' => 1, 'display' => 1)),
				'phone' => array('id' => 'phone', 'title' => 'Home Phone', 'sort_order' => 12, 'conditional' => 0,
					'guest' => array('require' => 0, 'display' => 1), 'logged' => array('require' => 0, 'display' => 1)),
				'phone_mobile' => array('id' => 'phone_mobile', 'title' => 'Mobile Phone', 'sort_order' => 13, 'conditional' => 0,
					'guest' => array('require' => 1, 'display' => 1), 'logged' => array('require' => 1, 'display' => 1)),
				'alias' => array('id' => 'alias', 'title' => 'Address Title', 'sort_order' => 14, 'conditional' => 0,
					'guest' => array('require' => 1, 'display' => 1), 'logged' => array('require' => 1, 'display' => 1)),
				'other' => array('id' => 'other', 'title' => 'Other Information', 'sort_order' => 15, 'conditional' => 0,
					'guest' => array('require' => 0, 'display' => 1), 'logged' => array('require' => 0, 'display' => 1))
			),
			'payment_method' => array('enable' => 1, 'default' => '', 'display_style' => 0),
			'shipping_method' => array('enable' => 1, 'default' => '', 'display_style' => 0),
			'display_cart' => 1,
			'cart_options' => array(
				'product_image' => array('id' => 'product_image', 'title' => 'Image', 'sort_order' => 2,
					'guest' => array('display' => 1), 'logged' => array('display' => 1)),
				'product_name' => array('id' => 'product_name', 'title' => 'Description', 'sort_order' => 2,
					'guest' => array('display' => 1), 'logged' => array('display' => 1)),
				'product_model' => array('id' => 'product_model', 'title' => 'Model', 'sort_order' => 3,
					'guest' => array('display' => 1), 'logged' => array('display' => 1)),
				'product_qty' => array('id' => 'product_qty', 'title' => 'Quantity', 'sort_order' => 4,
					'guest' => array('display' => 1), 'logged' => array('display' => 1)),
				'product_price' => array('id' => 'product_price', 'title' => 'Price', 'sort_order' => 5,
					'guest' => array('display' => 1), 'logged' => array('display' => 1)),
				'product_total' => array('id' => 'product_total', 'title' => 'Total', 'sort_order' => 6,
					'guest' => array('display' => 1), 'logged' => array('display' => 1))
			),
			'cart_image_size' => array('name' => 'velsof_supercheckout_image', 'width' => 90, 'height' => 90),
			'order_total_option' => array(
				'product_sub_total' => array('guest' => array('display' => 1), 'logged' => array('display' => 1)),
				'voucher' => array('guest' => array('display' => 1), 'logged' => array('display' => 1)),
				'shipping_price' => array('guest' => array('display' => 1), 'logged' => array('display' => 1)),
				'total' => array('guest' => array('display' => 1), 'logged' => array('display' => 1))
			),
			'confirm' => array(
				'order_comment_box' => array('guest' => array('display' => 1), 'logged' => array('display' => 1)),
				'term_condition' => array(
					'guest' => array('checked' => 1, 'require' => 1, 'display' => 1),
					'logged' => array('checked' => 1, 'require' => 1, 'display' => 1)
				)
			),
			'layout' => 3,
			'column_width' => array(
				'1_column' => array(1 => '100', 2 => '0', 3 => '0', 'inside' => array(1 => '0', 2 => '0')),
				'2_column' => array(1 => '30', 2 => '70', 3 => '0', 'inside' => array(1 => '50', 2 => '50')),
				'3_column' => array(1 => '30', 2 => '25', 3 => '45', 'inside' => array(1 => '0', 2 => '0'))
			),
			'modal_value' => 0,
			'design' => array(
				'login' => array(
					'1_column' => array('column' => 0, 'row' => 0, 'column-inside' => 0),
					'2_column' => array('column' => 1, 'row' => 0, 'column-inside' => 1),
					'3_column' => array('column' => 1, 'row' => 0, 'column-inside' => 0)
				),
				'shipping_address' => array(
					'1_column' => array('column' => 0, 'row' => 1, 'column-inside' => 0),
					'2_column' => array('column' => 1, 'row' => 1, 'column-inside' => 1),
					'3_column' => array('column' => 1, 'row' => 1, 'column-inside' => 0)
				),
				'payment_address' => array(
					'1_column' => array('column' => 0, 'row' => 2, 'column-inside' => 0),
					'2_column' => array('column' => 1, 'row' => 2, 'column-inside' => 1),
					'3_column' => array('column' => 1, 'row' => 2, 'column-inside' => 0)
				),
				'shipping_method' => array(
					'1_column' => array('column' => 0, 'row' => 3, 'column-inside' => 0),
					'2_column' => array('column' => 1, 'row' => 0, 'column-inside' => 3),
					'3_column' => array('column' => 2, 'row' => 0, 'column-inside' => 0)
				),
				'payment_method' => array(
					'1_column' => array('column' => 0, 'row' => 4, 'column-inside' => 0),
					'2_column' => array('column' => 2, 'row' => 0, 'column-inside' => 3),
					'3_column' => array('column' => 2, 'row' => 1, 'column-inside' => 0)
				),
				'cart' => array(
					'1_column' => array('column' => 0, 'row' => 5, 'column-inside' => 0),
					'2_column' => array('column' => 2, 'row' => 0, 'column-inside' => 2),
					'3_column' => array('column' => 3, 'row' => 0, 'column-inside' => 0)
				),
				'confirm' => array(
					'1_column' => array('column' => 0, 'row' => 6, 'column-inside' => 0),
					'2_column' => array('column' => 2, 'row' => 1, 'column-inside' => 4),
					'3_column' => array('column' => 3, 'row' => 1, 'column-inside' => 0)
				),
				'html' => array(
					'0_0' => array(
						'1_column' => array('column' => 0, 'row' => 7, 'column-inside' => 1),
						'2_column' => array('column' => 2, 'row' => 1, 'column-inside' => 4),
						'3_column' => array('column' => 3, 'row' => 4, 'column-inside' => 1),
						'value' => ''
					)
				)
			)
		);

		return $settings;
	}

	public function processPostData($data = array())
	{
		$configuration = $data;

		//Customer Personal Information
		if (!isset($configuration['customer_personal']['id_gender']['guest']['require']))
			$configuration['customer_personal']['id_gender']['guest']['require'] = 0;
		if (!isset($configuration['customer_personal']['id_gender']['guest']['display']))
			$configuration['customer_personal']['id_gender']['guest']['display'] = 0;
		if (!isset($configuration['customer_personal']['id_gender']['logged']['require']))
			$configuration['customer_personal']['id_gender']['logged']['require'] = 0;
		if (!isset($configuration['customer_personal']['id_gender']['logged']['display']))
			$configuration['customer_personal']['id_gender']['logged']['display'] = 0;

		if (!isset($configuration['customer_personal']['dob']['guest']['require']))
			$configuration['customer_personal']['dob']['guest']['require'] = 0;
		if (!isset($configuration['customer_personal']['dob']['guest']['display']))
			$configuration['customer_personal']['dob']['guest']['display'] = 0;
		if (!isset($configuration['customer_personal']['dob']['logged']['require']))
			$configuration['customer_personal']['dob']['logged']['require'] = 0;
		if (!isset($configuration['customer_personal']['dob']['logged']['display']))
			$configuration['customer_personal']['dob']['logged']['display'] = 0;

		if (!isset($configuration['customer_subscription']['newsletter']['guest']['checked']))
			$configuration['customer_subscription']['newsletter']['guest']['checked'] = 0;
		if (!isset($configuration['customer_subscription']['newsletter']['guest']['display']))
			$configuration['customer_subscription']['newsletter']['guest']['display'] = 0;

		if (!isset($configuration['customer_subscription']['optin']['guest']['checked']))
			$configuration['customer_subscription']['optin']['guest']['checked'] = 0;
		if (!isset($configuration['customer_subscription']['optin']['guest']['display']))
			$configuration['customer_subscription']['optin']['guest']['display'] = 0;

		if (!isset($configuration['use_delivery_for_payment_add']['guest']))
			$configuration['use_delivery_for_payment_add']['guest'] = 0;
		if (!isset($configuration['use_delivery_for_payment_add']['logged']))
			$configuration['use_delivery_for_payment_add']['logged'] = 0;

		if (!isset($configuration['show_use_delivery_for_payment_add']['guest']))
			$configuration['show_use_delivery_for_payment_add']['guest'] = 0;
		if (!isset($configuration['show_use_delivery_for_payment_add']['logged']))
			$configuration['show_use_delivery_for_payment_add']['logged'] = 0;

		//Payment Address
		if (!isset($configuration['payment_address']['firstname']['guest']['require']))
			$configuration['payment_address']['firstname']['guest']['require'] = 0;
		if (!isset($configuration['payment_address']['firstname']['guest']['display']))
			$configuration['payment_address']['firstname']['guest']['display'] = 0;
		if (!isset($configuration['payment_address']['firstname']['logged']['require']))
			$configuration['payment_address']['firstname']['logged']['require'] = 0;
		if (!isset($configuration['payment_address']['firstname']['logged']['display']))
			$configuration['payment_address']['firstname']['logged']['display'] = 0;

		if (!isset($configuration['payment_address']['lastname']['guest']['require']))
			$configuration['payment_address']['lastname']['guest']['require'] = 0;
		if (!isset($configuration['payment_address']['lastname']['guest']['display']))
			$configuration['payment_address']['lastname']['guest']['display'] = 0;
		if (!isset($configuration['payment_address']['lastname']['logged']['require']))
			$configuration['payment_address']['lastname']['logged']['require'] = 0;
		if (!isset($configuration['payment_address']['lastname']['logged']['display']))
			$configuration['payment_address']['lastname']['logged']['display'] = 0;

		if (!isset($configuration['payment_address']['company']['guest']['require']))
			$configuration['payment_address']['company']['guest']['require'] = 0;
		if (!isset($configuration['payment_address']['company']['guest']['display']))
			$configuration['payment_address']['company']['guest']['display'] = 0;
		if (!isset($configuration['payment_address']['company']['logged']['require']))
			$configuration['payment_address']['company']['logged']['require'] = 0;
		if (!isset($configuration['payment_address']['company']['logged']['display']))
			$configuration['payment_address']['company']['logged']['display'] = 0;

		if (!isset($configuration['payment_address']['vat_number']['guest']['require']))
			$configuration['payment_address']['vat_number']['guest']['require'] = 0;
		if (!isset($configuration['payment_address']['vat_number']['guest']['display']))
			$configuration['payment_address']['vat_number']['guest']['display'] = 0;
		if (!isset($configuration['payment_address']['vat_number']['logged']['require']))
			$configuration['payment_address']['vat_number']['logged']['require'] = 0;
		if (!isset($configuration['payment_address']['vat_number']['logged']['display']))
			$configuration['payment_address']['vat_number']['logged']['display'] = 0;

		if (!isset($configuration['payment_address']['dni']['guest']['require']))
			$configuration['payment_address']['dni']['guest']['require'] = 0;
		if (!isset($configuration['payment_address']['dni']['guest']['display']))
			$configuration['payment_address']['dni']['guest']['display'] = 0;
		if (!isset($configuration['payment_address']['dni']['logged']['require']))
			$configuration['payment_address']['dni']['logged']['require'] = 0;
		if (!isset($configuration['payment_address']['dni']['logged']['display']))
			$configuration['payment_address']['dni']['logged']['display'] = 0;

		if (!isset($configuration['payment_address']['address1']['guest']['require']))
			$configuration['payment_address']['address1']['guest']['require'] = 0;
		if (!isset($configuration['payment_address']['address1']['guest']['display']))
			$configuration['payment_address']['address1']['guest']['display'] = 0;
		if (!isset($configuration['payment_address']['address1']['logged']['require']))
			$configuration['payment_address']['address1']['logged']['require'] = 0;
		if (!isset($configuration['payment_address']['address1']['logged']['display']))
			$configuration['payment_address']['address1']['logged']['display'] = 0;

		if (!isset($configuration['payment_address']['address1']['guest']['require']))
			$configuration['payment_address']['address1']['guest']['require'] = 0;
		if (!isset($configuration['payment_address']['address1']['guest']['display']))
			$configuration['payment_address']['address1']['guest']['display'] = 0;
		if (!isset($configuration['payment_address']['address1']['logged']['require']))
			$configuration['payment_address']['address1']['logged']['require'] = 0;
		if (!isset($configuration['payment_address']['address1']['logged']['display']))
			$configuration['payment_address']['address1']['logged']['display'] = 0;

		if (!isset($configuration['payment_address']['address2']['guest']['require']))
			$configuration['payment_address']['address2']['guest']['require'] = 0;
		if (!isset($configuration['payment_address']['address2']['guest']['display']))
			$configuration['payment_address']['address2']['guest']['display'] = 0;
		if (!isset($configuration['payment_address']['address2']['logged']['require']))
			$configuration['payment_address']['address2']['logged']['require'] = 0;
		if (!isset($configuration['payment_address']['address2']['logged']['display']))
			$configuration['payment_address']['address2']['logged']['display'] = 0;

		if (!isset($configuration['payment_address']['postcode']['guest']['require']))
			$configuration['payment_address']['postcode']['guest']['require'] = 0;
		if (!isset($configuration['payment_address']['postcode']['guest']['display']))
			$configuration['payment_address']['postcode']['guest']['display'] = 0;
		if (!isset($configuration['payment_address']['postcode']['logged']['require']))
			$configuration['payment_address']['postcode']['logged']['require'] = 0;
		if (!isset($configuration['payment_address']['postcode']['logged']['display']))
			$configuration['payment_address']['postcode']['logged']['display'] = 0;

		if (!isset($configuration['payment_address']['city']['guest']['require']))
			$configuration['payment_address']['city']['guest']['require'] = 0;
		if (!isset($configuration['payment_address']['city']['guest']['display']))
			$configuration['payment_address']['city']['guest']['display'] = 0;
		if (!isset($configuration['payment_address']['city']['logged']['require']))
			$configuration['payment_address']['city']['logged']['require'] = 0;
		if (!isset($configuration['payment_address']['city']['logged']['display']))
			$configuration['payment_address']['city']['logged']['display'] = 0;

		if (!isset($configuration['payment_address']['id_state']['guest']['require']))
			$configuration['payment_address']['id_state']['guest']['require'] = 0;
		if (!isset($configuration['payment_address']['id_state']['guest']['display']))
			$configuration['payment_address']['id_state']['guest']['display'] = 0;
		if (!isset($configuration['payment_address']['id_state']['logged']['require']))
			$configuration['payment_address']['id_state']['logged']['require'] = 0;
		if (!isset($configuration['payment_address']['id_state']['logged']['display']))
			$configuration['payment_address']['id_state']['logged']['display'] = 0;

		if (!isset($configuration['payment_address']['id_country']['guest']['require']))
			$configuration['payment_address']['id_country']['guest']['require'] = 0;
		if (!isset($configuration['payment_address']['id_country']['guest']['display']))
			$configuration['payment_address']['id_country']['guest']['display'] = 0;
		if (!isset($configuration['payment_address']['id_country']['logged']['require']))
			$configuration['payment_address']['id_country']['logged']['require'] = 0;
		if (!isset($configuration['payment_address']['id_country']['logged']['display']))
			$configuration['payment_address']['id_country']['logged']['display'] = 0;

		if (!isset($configuration['payment_address']['phone']['guest']['require']))
			$configuration['payment_address']['phone']['guest']['require'] = 0;
		if (!isset($configuration['payment_address']['phone']['guest']['display']))
			$configuration['payment_address']['phone']['guest']['display'] = 0;
		if (!isset($configuration['payment_address']['phone']['logged']['require']))
			$configuration['payment_address']['phone']['logged']['require'] = 0;
		if (!isset($configuration['payment_address']['phone']['logged']['display']))
			$configuration['payment_address']['phone']['logged']['display'] = 0;

		if (!isset($configuration['payment_address']['phone_mobile']['guest']['require']))
			$configuration['payment_address']['phone_mobile']['guest']['require'] = 0;
		if (!isset($configuration['payment_address']['phone_mobile']['guest']['display']))
			$configuration['payment_address']['phone_mobile']['guest']['display'] = 0;
		if (!isset($configuration['payment_address']['phone_mobile']['logged']['require']))
			$configuration['payment_address']['phone_mobile']['logged']['require'] = 0;
		if (!isset($configuration['payment_address']['phone_mobile']['logged']['display']))
			$configuration['payment_address']['phone_mobile']['logged']['display'] = 0;

		if (!isset($configuration['payment_address']['alias']['guest']['require']))
			$configuration['payment_address']['alias']['guest']['require'] = 0;
		if (!isset($configuration['payment_address']['alias']['guest']['display']))
			$configuration['payment_address']['alias']['guest']['display'] = 0;
		if (!isset($configuration['payment_address']['alias']['logged']['require']))
			$configuration['payment_address']['alias']['logged']['require'] = 0;
		if (!isset($configuration['payment_address']['alias']['logged']['display']))
			$configuration['payment_address']['alias']['logged']['display'] = 0;

		if (!isset($configuration['payment_address']['other']['guest']['require']))
			$configuration['payment_address']['other']['guest']['require'] = 0;
		if (!isset($configuration['payment_address']['other']['guest']['display']))
			$configuration['payment_address']['other']['guest']['display'] = 0;
		if (!isset($configuration['payment_address']['other']['logged']['require']))
			$configuration['payment_address']['other']['logged']['require'] = 0;
		if (!isset($configuration['payment_address']['other']['logged']['display']))
			$configuration['payment_address']['other']['logged']['display'] = 0;

		//Shipping Address
		if (!isset($configuration['shipping_address']['firstname']['guest']['require']))
			$configuration['shipping_address']['firstname']['guest']['require'] = 0;
		if (!isset($configuration['shipping_address']['firstname']['guest']['display']))
			$configuration['shipping_address']['firstname']['guest']['display'] = 0;
		if (!isset($configuration['shipping_address']['firstname']['logged']['require']))
			$configuration['shipping_address']['firstname']['logged']['require'] = 0;
		if (!isset($configuration['shipping_address']['firstname']['logged']['display']))
			$configuration['shipping_address']['firstname']['logged']['display'] = 0;

		if (!isset($configuration['shipping_address']['lastname']['guest']['require']))
			$configuration['shipping_address']['lastname']['guest']['require'] = 0;
		if (!isset($configuration['shipping_address']['lastname']['guest']['display']))
			$configuration['shipping_address']['lastname']['guest']['display'] = 0;
		if (!isset($configuration['shipping_address']['lastname']['logged']['require']))
			$configuration['shipping_address']['lastname']['logged']['require'] = 0;
		if (!isset($configuration['shipping_address']['lastname']['logged']['display']))
			$configuration['shipping_address']['lastname']['logged']['display'] = 0;

		if (!isset($configuration['shipping_address']['company']['guest']['require']))
			$configuration['shipping_address']['company']['guest']['require'] = 0;
		if (!isset($configuration['shipping_address']['company']['guest']['display']))
			$configuration['shipping_address']['company']['guest']['display'] = 0;
		if (!isset($configuration['shipping_address']['company']['logged']['require']))
			$configuration['shipping_address']['company']['logged']['require'] = 0;
		if (!isset($configuration['shipping_address']['company']['logged']['display']))
			$configuration['shipping_address']['company']['logged']['display'] = 0;

		if (!isset($configuration['shipping_address']['vat_number']['guest']['require']))
			$configuration['shipping_address']['vat_number']['guest']['require'] = 0;
		if (!isset($configuration['shipping_address']['vat_number']['guest']['display']))
			$configuration['shipping_address']['vat_number']['guest']['display'] = 0;
		if (!isset($configuration['shipping_address']['vat_number']['logged']['require']))
			$configuration['shipping_address']['vat_number']['logged']['require'] = 0;
		if (!isset($configuration['shipping_address']['vat_number']['logged']['display']))
			$configuration['shipping_address']['vat_number']['logged']['display'] = 0;

		if (!isset($configuration['shipping_address']['dni']['guest']['require']))
			$configuration['shipping_address']['dni']['guest']['require'] = 0;
		if (!isset($configuration['shipping_address']['dni']['guest']['display']))
			$configuration['shipping_address']['dni']['guest']['display'] = 0;
		if (!isset($configuration['shipping_address']['dni']['logged']['require']))
			$configuration['shipping_address']['dni']['logged']['require'] = 0;
		if (!isset($configuration['shipping_address']['dni']['logged']['display']))
			$configuration['shipping_address']['dni']['logged']['display'] = 0;

		if (!isset($configuration['shipping_address']['address1']['guest']['require']))
			$configuration['shipping_address']['address1']['guest']['require'] = 0;
		if (!isset($configuration['shipping_address']['address1']['guest']['display']))
			$configuration['shipping_address']['address1']['guest']['display'] = 0;
		if (!isset($configuration['shipping_address']['address1']['logged']['require']))
			$configuration['shipping_address']['address1']['logged']['require'] = 0;
		if (!isset($configuration['shipping_address']['address1']['logged']['display']))
			$configuration['shipping_address']['address1']['logged']['display'] = 0;

		if (!isset($configuration['shipping_address']['address1']['guest']['require']))
			$configuration['shipping_address']['address1']['guest']['require'] = 0;
		if (!isset($configuration['shipping_address']['address1']['guest']['display']))
			$configuration['shipping_address']['address1']['guest']['display'] = 0;
		if (!isset($configuration['shipping_address']['address1']['logged']['require']))
			$configuration['shipping_address']['address1']['logged']['require'] = 0;
		if (!isset($configuration['shipping_address']['address1']['logged']['display']))
			$configuration['shipping_address']['address1']['logged']['display'] = 0;

		if (!isset($configuration['shipping_address']['address2']['guest']['require']))
			$configuration['shipping_address']['address2']['guest']['require'] = 0;
		if (!isset($configuration['shipping_address']['address2']['guest']['display']))
			$configuration['shipping_address']['address2']['guest']['display'] = 0;
		if (!isset($configuration['shipping_address']['address2']['logged']['require']))
			$configuration['shipping_address']['address2']['logged']['require'] = 0;
		if (!isset($configuration['shipping_address']['address2']['logged']['display']))
			$configuration['shipping_address']['address2']['logged']['display'] = 0;

		if (!isset($configuration['shipping_address']['postcode']['guest']['require']))
			$configuration['shipping_address']['postcode']['guest']['require'] = 0;
		if (!isset($configuration['shipping_address']['postcode']['guest']['display']))
			$configuration['shipping_address']['postcode']['guest']['display'] = 0;
		if (!isset($configuration['shipping_address']['postcode']['logged']['require']))
			$configuration['shipping_address']['postcode']['logged']['require'] = 0;
		if (!isset($configuration['shipping_address']['postcode']['logged']['display']))
			$configuration['shipping_address']['postcode']['logged']['display'] = 0;

		if (!isset($configuration['shipping_address']['city']['guest']['require']))
			$configuration['shipping_address']['city']['guest']['require'] = 0;
		if (!isset($configuration['shipping_address']['city']['guest']['display']))
			$configuration['shipping_address']['city']['guest']['display'] = 0;
		if (!isset($configuration['shipping_address']['city']['logged']['require']))
			$configuration['shipping_address']['city']['logged']['require'] = 0;
		if (!isset($configuration['shipping_address']['city']['logged']['display']))
			$configuration['shipping_address']['city']['logged']['display'] = 0;

		if (!isset($configuration['shipping_address']['id_state']['guest']['require']))
			$configuration['shipping_address']['id_state']['guest']['require'] = 0;
		if (!isset($configuration['shipping_address']['id_state']['guest']['display']))
			$configuration['shipping_address']['id_state']['guest']['display'] = 0;
		if (!isset($configuration['shipping_address']['id_state']['logged']['require']))
			$configuration['shipping_address']['id_state']['logged']['require'] = 0;
		if (!isset($configuration['shipping_address']['id_state']['logged']['display']))
			$configuration['shipping_address']['id_state']['logged']['display'] = 0;

		if (!isset($configuration['shipping_address']['id_country']['guest']['require']))
			$configuration['shipping_address']['id_country']['guest']['require'] = 0;
		if (!isset($configuration['shipping_address']['id_country']['guest']['display']))
			$configuration['shipping_address']['id_country']['guest']['display'] = 0;
		if (!isset($configuration['shipping_address']['id_country']['logged']['require']))
			$configuration['shipping_address']['id_country']['logged']['require'] = 0;
		if (!isset($configuration['shipping_address']['id_country']['logged']['display']))
			$configuration['shipping_address']['id_country']['logged']['display'] = 0;

		if (!isset($configuration['shipping_address']['phone']['guest']['require']))
			$configuration['shipping_address']['phone']['guest']['require'] = 0;
		if (!isset($configuration['shipping_address']['phone']['guest']['display']))
			$configuration['shipping_address']['phone']['guest']['display'] = 0;
		if (!isset($configuration['shipping_address']['phone']['logged']['require']))
			$configuration['shipping_address']['phone']['logged']['require'] = 0;
		if (!isset($configuration['shipping_address']['phone']['logged']['display']))
			$configuration['shipping_address']['phone']['logged']['display'] = 0;

		if (!isset($configuration['shipping_address']['phone_mobile']['guest']['require']))
			$configuration['shipping_address']['phone_mobile']['guest']['require'] = 0;
		if (!isset($configuration['shipping_address']['phone_mobile']['guest']['display']))
			$configuration['shipping_address']['phone_mobile']['guest']['display'] = 0;
		if (!isset($configuration['shipping_address']['phone_mobile']['logged']['require']))
			$configuration['shipping_address']['phone_mobile']['logged']['require'] = 0;
		if (!isset($configuration['shipping_address']['phone_mobile']['logged']['display']))
			$configuration['shipping_address']['phone_mobile']['logged']['display'] = 0;

		if (!isset($configuration['shipping_address']['alias']['guest']['require']))
			$configuration['shipping_address']['alias']['guest']['require'] = 0;
		if (!isset($configuration['shipping_address']['alias']['guest']['display']))
			$configuration['shipping_address']['alias']['guest']['display'] = 0;
		if (!isset($configuration['shipping_address']['alias']['logged']['require']))
			$configuration['shipping_address']['alias']['logged']['require'] = 0;
		if (!isset($configuration['shipping_address']['alias']['logged']['display']))
			$configuration['shipping_address']['alias']['logged']['display'] = 0;

		if (!isset($configuration['shipping_address']['other']['guest']['require']))
			$configuration['shipping_address']['other']['guest']['require'] = 0;
		if (!isset($configuration['shipping_address']['other']['guest']['display']))
			$configuration['shipping_address']['other']['guest']['display'] = 0;
		if (!isset($configuration['shipping_address']['other']['logged']['require']))
			$configuration['shipping_address']['other']['logged']['require'] = 0;
		if (!isset($configuration['shipping_address']['other']['logged']['display']))
			$configuration['shipping_address']['other']['logged']['display'] = 0;

		//Cart
		if (!isset($configuration['cart_options']['product_image']['guest']['display']))
			$configuration['cart_options']['product_image']['guest']['display'] = 0;
		if (!isset($configuration['cart_options']['product_image']['logged']['display']))
			$configuration['cart_options']['product_image']['logged']['display'] = 0;

		if (!isset($configuration['cart_options']['product_name']['guest']['display']))
			$configuration['cart_options']['product_name']['guest']['display'] = 0;
		if (!isset($configuration['cart_options']['product_name']['logged']['display']))
			$configuration['cart_options']['product_name']['logged']['display'] = 0;

		if (!isset($configuration['cart_options']['product_model']['guest']['display']))
			$configuration['cart_options']['product_model']['guest']['display'] = 0;
		if (!isset($configuration['cart_options']['product_model']['logged']['display']))
			$configuration['cart_options']['product_model']['logged']['display'] = 0;

		if (!isset($configuration['cart_options']['product_qty']['guest']['display']))
			$configuration['cart_options']['product_qty']['guest']['display'] = 0;
		if (!isset($configuration['cart_options']['product_qty']['logged']['display']))
			$configuration['cart_options']['product_qty']['logged']['display'] = 0;

		if (!isset($configuration['cart_options']['product_price']['guest']['display']))
			$configuration['cart_options']['product_price']['guest']['display'] = 0;
		if (!isset($configuration['cart_options']['product_price']['logged']['display']))
			$configuration['cart_options']['product_price']['logged']['display'] = 0;

		if (!isset($configuration['cart_options']['product_total']['guest']['display']))
			$configuration['cart_options']['product_total']['guest']['display'] = 0;
		if (!isset($configuration['cart_options']['product_total']['logged']['display']))
			$configuration['cart_options']['product_total']['logged']['display'] = 0;

		//Order Total
		if (!isset($configuration['order_total_option']['product_sub_total']['guest']['display']))
			$configuration['order_total_option']['product_sub_total']['guest']['display'] = 0;
		if (!isset($configuration['order_total_option']['product_sub_total']['logged']['display']))
			$configuration['order_total_option']['product_sub_total']['logged']['display'] = 0;

		if (!isset($configuration['order_total_option']['voucher']['guest']['display']))
			$configuration['order_total_option']['voucher']['guest']['display'] = 0;
		if (!isset($configuration['order_total_option']['voucher']['logged']['display']))
			$configuration['order_total_option']['voucher']['logged']['display'] = 0;

		if (!isset($configuration['order_total_option']['shipping_price']['guest']['display']))
			$configuration['order_total_option']['shipping_price']['guest']['display'] = 0;
		if (!isset($configuration['order_total_option']['shipping_price']['logged']['display']))
			$configuration['order_total_option']['shipping_price']['logged']['display'] = 0;

		if (!isset($configuration['order_total_option']['total']['guest']['display']))
			$configuration['order_total_option']['total']['guest']['display'] = 0;
		if (!isset($configuration['order_total_option']['total']['logged']['display']))
			$configuration['order_total_option']['total']['logged']['display'] = 0;

		//Confirm
		if (!isset($configuration['confirm']['term_condition']['guest']['display']))
			$configuration['confirm']['term_condition']['guest']['display'] = 0;
		if (!isset($configuration['confirm']['term_condition']['logged']['display']))
			$configuration['confirm']['term_condition']['logged']['display'] = 0;
		if (!isset($configuration['confirm']['term_condition']['guest']['checked']))
			$configuration['confirm']['term_condition']['guest']['checked'] = 0;
		if (!isset($configuration['confirm']['term_condition']['logged']['checked']))
			$configuration['confirm']['term_condition']['logged']['checked'] = 0;
		if (!isset($configuration['confirm']['term_condition']['guest']['require']))
			$configuration['confirm']['term_condition']['guest']['require'] = 0;
		if (!isset($configuration['confirm']['term_condition']['logged']['require']))
			$configuration['confirm']['term_condition']['logged']['require'] = 0;

		if (!isset($configuration['confirm']['order_comment_box']['guest']['display']))
			$configuration['confirm']['order_comment_box']['guest']['display'] = 0;
		if (!isset($configuration['confirm']['order_comment_box']['logged']['display']))
			$configuration['confirm']['order_comment_box']['logged']['display'] = 0;

		//Encode Html entities
		$configuration['html_value']['header'] = htmlentities($configuration['html_value']['header']);
		$configuration['html_value']['footer'] = htmlentities($configuration['html_value']['footer']);

		foreach ($configuration['design']['html'] as $key => $value)
		{
			$tmp = $value;
			$configuration['design']['html'][$key]['value'] = htmlentities($configuration['design']['html'][$key]['value']);
			unset($tmp);
		}

		return $configuration;
	}

}
