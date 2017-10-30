<?php
abstract class HTMLTemplate extends HTMLTemplateCore
{
	private $verification_keys = 'MK##1';
	private $data_url;
	private $instaled_faktura;
	private $instaled_loyalty;

	public function getHeader()
	{
		$this->assignCommonHeaderData();
	}

	public function assignCommonHeaderData()
	{
		$this->instaled_faktura	= $this->isInstalAndActive('add_faktura');
		$this->instaled_loyalty	= $this->isInstalAndActive('LoyaltyModule');

		if($this->instaled_faktura)
		{
			Configuration::updateValue('PS_INVOICE_TAXES_BREAKDOWN', '0');
			$delivery_address	 = new Address($this->order->id_address_delivery);
			$invoice_address	 = new Address($this->order->id_address_invoice);
			$country			 = new Country($delivery_address->id_country);
			$customer			 = new Customer($this->order->id_customer);
			$currency			 = new Currency($this->order->id_currency);
			$carrier			 = new Carrier($this->order->id_carrier);
			$thisShop			 = $this->order->id_shop;
			$thisGroup			 = $this->order->id_shop_group;
			$invoice_date		 = $this->order->invoice_date;
			$date				 = new DateTime($invoice_date);
			include_once(_PS_MODULE_DIR_.'/add_faktura/add_faktura.php');
			$module = new Add_Faktura();
			$this->data_url = $module->cnb_url.$date->format('d.m.Y');

			$points = '';
			$hodnota = '';
			if($this->instaled_loyalty)
			{
				include_once(_PS_MODULE_DIR_.'/loyalty/LoyaltyModule.php');
				include_once(_PS_MODULE_DIR_.'/loyalty/LoyaltyStateModule.php');
				$points = (int)LoyaltyModule::getPointsByCustomer((int)$customer->id);
				$hodnota = Tools::displayPrice(LoyaltyModule::getVoucherValue($points, (int)$currency->id));
			}

			$tax_other_country = false;
			$tax_currency = $currency;
			$kurz_CNB = 1;
			if ($this->order->id_currency != Configuration::get('PS_CURRENCY_DEFAULT', '', $thisGroup, $thisShop))
			{
				$tax_other_country = true;
				$tax_currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT', '', $thisGroup, $thisShop));
				$kurz_CNB = $this->kurzCNB($currency->iso_code);
			}

			$bank_Currency = $this->order->id_currency;
			if ($bank_Currency != Configuration::get('FA_BANK_CURRENCY', '', $thisGroup, $thisShop))
				$bank_Currency = Configuration::get('PS_CURRENCY_DEFAULT', '', $thisGroup, $thisShop);

			$razitko_path = '';
			$module_img_dir = _PS_MODULE_DIR_.'/add_faktura/img/';
			if (file_exists($module_img_dir.$thisShop.'_razitko.'.Configuration::get('FA_EXTENSION', '', $thisGroup, $thisShop)))
				$razitko_path = $module_img_dir.$thisShop.'_razitko.'.Configuration::get('FA_EXTENSION', '', $thisGroup, $thisShop);

			$ic = $delivery_address->dni;
			$dic = $delivery_address->vat_number;
			if ($this->order->id_address_delivery != $this->order->id_address_invoice)
			{
				$ic = $invoice_address->dni;
				$dic = $invoice_address->vat_number;
			}

			$icdph = '';
			if ((int)Configuration::get('SK', '', $thisGroup, $thisShop))
			{
				if (mb_strtoupper(substr($dic,0,2), "utf-8") == 'SK')
				{
					$icdph = $dic;
					$dic = $dic;	;
				}
			}

			$user_note = '';
			$messages = CustomerMessage::getMessagesByOrderId((int)($this->order->id), false);
			if (Configuration::get('FA_USER_NOTE', '', $thisGroup, $thisShop))
			foreach ($messages as $message)
				$user_note = nl2br2($message['message']);

			if (Configuration::get('FA_ORD_INV', '', $thisGroup, $thisShop) == 'order')
				$fa_ord_inv = sprintf('%06d', $this->order->id);
			elseif (Configuration::get('FA_ORD_INV', '', $thisGroup, $thisShop) == 'reference')
				$fa_ord_inv = $this->order->reference;
			else
				$fa_ord_inv = sprintf('%06d', $this->order->invoice_number);

			$date_due = Configuration::get('FA_DUE_DATE_DATES', '', $thisGroup, $thisShop);
			if (Configuration::get('FA_DUE_'.$this->order->id) > 0)
				$date_due = Configuration::get('FA_DUE_'.$this->order->id);

			$slipDate_due = '';
			if (isset($this->order_slip))
				$slipDate_due = $this->tomorrowDATE($this->order_slip->date_add, Configuration::get('FA_SLIP_DUE_'.$this->order->id, '', $thisGroup, $thisShop));

			$this->smarty->assign(array(
				'tax_excluded_display' => Group::getPriceDisplayMethod($customer->id_default_group),
				'date_due'			 => $this->tomorrowDATE($invoice_date, $date_due),
				'invoice_date'		 => $invoice_date,
				'fa_name_shop'		 => Configuration::get('FA_NAME_SHOP', '', $thisGroup, $thisShop),
				'fa_address'		 => Configuration::get('FA_ADDRESS', '', $thisGroup, $thisShop),
				'fa_zipcode'		 => Configuration::get('FA_ZIPCODE', '', $thisGroup, $thisShop),
				'fa_city'			 => Configuration::get('FA_CITY', '', $thisGroup, $thisShop),
				'fa_country'		 => Configuration::get('FA_COUNTRY', '', $thisGroup, $thisShop),
				'fa_web'			 => Configuration::get('FA_WEB', '', $thisGroup, $thisShop),
				'fa_ico'			 => Configuration::get('FA_ICO', '', $thisGroup, $thisShop),
				'fa_dic'			 => Configuration::get('FA_DIC', '', $thisGroup, $thisShop),
				'fa_icdph'			 => Configuration::get('FA_ICDPH', '', $thisGroup, $thisShop),
				'fa_tel'			 => Configuration::get('FA_TEL', '', $thisGroup, $thisShop),
				'fa_email'			 => Configuration::get('FA_EMAIL', '', $thisGroup, $thisShop),
				'fa_bank_name'		 => Configuration::get('FA_BANK_NAME_'.$bank_Currency, '', $thisGroup, $thisShop),
				'fa_bank_number'	 => Configuration::get('FA_BANK_NUMBER_'.$bank_Currency, '', $thisGroup, $thisShop),
				'fa_swift'			 => Configuration::get('FA_SWIFT_'.$bank_Currency, '', $thisGroup, $thisShop),
				'fa_iban'			 => Configuration::get('FA_IBAN_'.$bank_Currency, '', $thisGroup, $thisShop),
				'font_width'		 => Configuration::get('FA_PDF_FONT_WIDTH', '', $thisGroup, $thisShop),
				'fa_zapis'			 => Configuration::get('FA_ZAPIS', '', $thisGroup, $thisShop),
				'fa_k_symbol'		 => Configuration::get('FA_K_SYMBOL', '', $thisGroup, $thisShop),
				'width_logo'		 => Configuration::get('FA_WIDTH', '', $thisGroup, $thisShop),
				'height_logo'		 => Configuration::get('FA_HEIGHT', '', $thisGroup, $thisShop),
				'fa_prefix_vs'		 => Configuration::get('FA_PREFIX_VS', '', $thisGroup, $thisShop),
				'fa_ord_inv'		 => $fa_ord_inv,
				'img_update_time'	 => Configuration::get('PS_IMG_UPDATE_TIME'),
				'delivery_prefix'	 => Configuration::get('PS_DELIVERY_PREFIX', Context::getContext()->language->id),
				'invoice_prefix'	 => Configuration::get('PS_INVOICE_PREFIX', Context::getContext()->language->id),
				'is_wat'			 => Configuration::get('FA_IS_WAT', '', $thisGroup, $thisShop),
				'sk'				 => Configuration::get('FA_SK', '', $thisGroup, $thisShop),
				'round_'			 => Configuration::get('FA_ROUND_', '', $thisGroup, $thisShop),
				'decimals'			 => Configuration::get('FA_DECIMALS', '', $thisGroup, $thisShop),
				'ic'				 => $ic,
				'dic'				 => $dic,
				'icdph'				 => $icdph,
				'razitko_path'		 => $razitko_path,
				'logo_path'			 => $this->getLogo(),
				'title'				 => $this->title,
				'carrier'			 => $carrier,
				'dlv_adr'			 => $delivery_address,
				'inv_adr'			 => $invoice_address,
				'order'				 => $this->order,
				'customer'			 => $customer,
				'currency'			 => $currency,
				'tax_currency'		 => $tax_currency,
				'tax_other_country'	 => $tax_other_country,
				'user_note'			 => $user_note,
				'admin_note'		 => Configuration::get('FA_ADMIN_NOTE', '', $thisGroup, $thisShop),
				'main_note'			 => Configuration::get('FA_MAIN_NOTE', '', $thisGroup, $thisShop),
				'points'			 => $points,
				'hodnota'			 => $hodnota,
				'kurz_CNB'			 => $kurz_CNB,
				'slipDate_due'		 => $slipDate_due,
				'slipBankNumber'	 => Configuration::get('FA_SLIP_BANKNUMBER_'.$this->order->id, '', $thisGroup, $thisShop),
				'slipText'			 => Configuration::get('FA_SLIP_TEXT_'.$this->order->id, '', $thisGroup, $thisShop),
				'only_inv_adr'		 => Configuration::get('FA_ONLY_INV_ADR', '', $thisGroup, $thisShop),
				'view_telefon'		 => Configuration::get('FA_VIEW_TELEFON', '', $thisGroup, $thisShop),
				'view_email'		 => Configuration::get('FA_VIEW_EMAIL', '', $thisGroup, $thisShop)
			));
		}
	}

	protected function getTemplate($template_name)
	{
		$template = false;
		$default_template = _PS_PDF_DIR_.'/'.$template_name.'.tpl';
		if($this->instaled_faktura)
			$overriden_template = _PS_MODULE_DIR_.'/add_faktura/views/templates/pdf/'.$template_name.'.tpl';
		else
			$overriden_template = _PS_THEME_DIR_.'pdf/'.$template_name.'.tpl';

		if (file_exists($overriden_template))
			$template = $overriden_template;
		else if (file_exists($default_template))
			$template = $default_template;

		return $template;
	}

	public function isInstalAndActive($module)
	{
		if(Module::isInstalled($module) && Module::isEnabled($module))
			return true;

		return false;
	}

	private function tomorrowDATE($date_invoice, $date_due)
	{
		$date = false;
		if ($date_due > 0 && $date_invoice != '0000-00-00 00:00:00')
		{
			$date = new DateTime($date_invoice);
			$date->modify('+'.$date_due.' day');
			$date = $date->format('Y-m-d');
		}
		return $date;
	}

	private function kurzCNB($code)
	{
		$data = $this->_GetData();
		if(empty($data))
			return false;
		else
		{
			$result = '';
			$row = explode("\n", $data);
			unset($row[0]);
			unset($row[1]);
			foreach($row as $val)
			{
				if(!empty($val))
				{
					$kurz = explode('|', $val);
					if($kurz[3] == $code)
						$result = $kurz[4];
				}
			}
			return trim($result);
		}
	}

	private function _GetData()
	{
		if(function_exists('curl_init'))
		{
			$ch = curl_init($this->data_url);
			@curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			@curl_setopt($ch, CURLOPT_AUTOREFERER, true);
			@curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			@curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:27.0) Gecko/20100101 Firefox/27.0');
			@curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			$data = @curl_exec($ch);
			curl_close ($ch);
			if($data === false)
				$data = @file_get_contents($this->data_url);

			return $data;
		} else
			return @file_get_contents($this->data_url);
	}
}