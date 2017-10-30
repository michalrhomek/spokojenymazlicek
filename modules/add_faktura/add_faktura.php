<?php
/* ########################################################################### */
/*                                                                             */
/*                      Copyright 2014     Miloslav Kubín                      */
/*                        http://presta-modul.shopmk.cz                        */
/*                                                                             */
/*             Please do not change this text, remove the link,                */
/*          or remove all or any part of the creator copyright notice          */
/*                                                                             */
/*    Please also note that although you are allowed to make modifications     */
/*     for your own personal use, you may not distribute the original or       */
/*                 the modified code without permission.                       */
/*                                                                             */
/*                    SELLING AND REDISTRIBUTION IS FORBIDDEN!                 */
/*             Download is allowed only from presta-modul.shopmk.cz            */
/*                                                                             */
/*       This software is provided as is, without warranty of any kind.        */
/*           The author shall not be liable for damages of any kind.           */
/*               Use of this software indicates that you agree.                */
/*                                                                             */
/*                                    ***                                      */
/*                                                                             */
/*              Prosím, neměňte tento text, nemazejte odkazy,                  */
/*      neodstraňujte části a nebo celé oznámení těchto autorských práv        */
/*                                                                             */
/*     Prosím vezměte také na vědomí, že i když máte možnost provádět změny    */
/*        pro vlastní osobní potřebu,nesmíte distribuovat původní nebo         */
/*                        upravený kód bez povolení.                           */
/*                                                                             */
/*                   PRODEJ A DISTRIBUCE JE ZAKÁZÁNA!                          */
/*          Download je povolen pouze z presta-modul.shopmk.cz                 */
/*                                                                             */
/*   Tento software je poskytován tak, jak je, bez záruky jakéhokoli druhu.    */
/*          Autor nenese odpovědnost za škody jakéhokoliv druhu.               */
/*                  Používáním tohoto softwaru znamená,                        */
/*           že souhlasíte s výše uvedenými autorskými právy .                 */
/*                                                                             */
/* ########################################################################### */
class Add_Faktura extends Module
{
	private	$_html = '';
	private	$shop;
	private $_postErrors = array();
	private $displayConf;
    private $need_override = false;
    private $need_override_instructions = false;
	public $cnb_url  = 'http://www.cnb.cz/cs/financni_trhy/devizovy_trh/kurzy_devizoveho_trhu/denni_kurz.txt?date=';
   	
	public function __construct()
	{
		$this->name = 'add_faktura';
		$this->version = '16.50814';
		$this->tab = 'others';
		$this->author = 'presstashop';
		$this->authormail = 'presstashop@gmail.com';
		$this->page = basename(__FILE__, '.php');

		$this->pdf_fonts = array(
					'aealarabiya', 'aefurat', 'courier', 'dejavusans',
					'dejavusanscondensed', 'dejavusansextralight',
					'dejavusansmono', 'dejavuserifcondensed', 'dejavuserif', 'freemono',
					'freesans', 'freeserif', 'helvetica',
					'pdfacourier', 'pdfahelvetica',
					'pdfasymbol', 'pdfatimes',
					'stsongstdlight', 'symbol',
					'times');

		parent::__construct();

		$this->displayName = $this->l('New Invoice');
		if (is_object(Context::getContext()->employee))
		$this->message = $this->version.' *** '.$_SERVER['SERVER_NAME'].'//ver.'._PS_VERSION_.Context::getContext()->employee->email;
		$this->description = $this->l('Invoice with a clear breakdown of data and attractive appearance.');
		$this->confirmUninstall = $this->l('Do you want to uninstall this module ?');

		$this->descriptions();
		$this->translations();
		$this->shop = Context::getContext()->shop->id;
	}

	protected function descriptions()
	{
		if(Configuration::get('PS_DISABLE_OVERRIDES') && Module::isInstalled($this->name) && _PS_VERSION_ >= '1.6.0.0')
		{
			$this->need_override = '<br><span style="color: red">
				'.$this->l('You are prohibited overrides, the module will not correct work!').' &nbsp; &nbsp; 
				<a href="'.$this->context->link->getAdminLink('AdminPerformance').'">'.$this->l('Enable it').'</a>
								</span>';
            $this->description .= $this->need_override;
		}

		if (Module::isInstalled($this->name) && (
			!$this->isPatched("classes/pdf/HTMLTemplate.php", "/MK##1/") ||
            !$this->isPatched("classes/pdf/PDFGenerator.php", "/MK##1/"))
		) {
			$this->description .= '<br><span style="color: red">
										'.$this->l('Incomplete installation, overrides are not correct, please reinstall module!').'
								</span>';
			$this->need_override_instructions = true;
		}
    }

    public function isPatched($filename, $pattern)
    {
        $file   = _PS_OVERRIDE_DIR_.$filename;
        $result = false;
        if (file_exists($file)) {
            $file_content = file_get_contents($file);
            $result = (preg_match($pattern, $file_content) > 0);
        }
        return $result;
    }

	public function install()
	{
		$this_curr = Configuration::get('PS_CURRENCY_DEFAULT');

		if (!$this->extractArchive(dirname(__FILE__).'/install/fonts.zip') ||
			!parent::install() ||
			!$this->registerHook('adminOrder') ||
			!Configuration::updateValue('FA_NAME_SHOP', 'např. Miloslav Kubín', false, false, false) ||
			!Configuration::updateValue('FA_WEB', 'např. www.shopmk.cz', false, false, false) ||
			!Configuration::updateValue('FA_ADDRESS', 'např. Palackého 84', false, false, false) ||
			!Configuration::updateValue('FA_ZIPCODE', '741 01', false, false, false) ||
			!Configuration::updateValue('FA_CITY', 'např. Nový Jičín', false, false, false) ||
			!Configuration::updateValue('FA_COUNTRY', 'Česká Republika', false, false, false) ||
			!Configuration::updateValue('FA_ICO', '1234567', false, false, false) ||
			!Configuration::updateValue('FA_DIC', 'nejsem plátce DPH', false, false, false) ||
			!Configuration::updateValue('FA_ICDPH', 'SK12345678', false, false, false) ||
			!Configuration::updateValue('FA_TEL', '+420 603 224460', false, false, false) ||
			!Configuration::updateValue('FA_EMAIL', 'miloslavkubin@centrum.cz', false, false, false) ||
			!Configuration::updateValue('FA_BANK_NAME_'.$this_curr, 'GE Money bank a.s.', false, false, false) ||
			!Configuration::updateValue('FA_BANK_NUMBER_'.$this_curr, '123456789 /0600', false, false, false) ||
			!Configuration::updateValue('FA_SWIFT_'.$this_curr, 'AB12CD', false, false, false) ||
			!Configuration::updateValue('FA_IBAN_'.$this_curr, 'CZ74 1155 0000 0026 4819 4503', false, false, false) ||
			!Configuration::updateValue('FA_K_SYMBOL', '555', false, false, false) ||
			!Configuration::updateValue('FA_ZAPIS', 'Okr. soud NJ 1, odd. SRO, vl. č 61516/B', false, false, false) ||
			!Configuration::updateValue('FA_WIDTH', '100', false, false, false) ||
			!Configuration::updateValue('FA_HEIGHT', '32', false, false, false) ||
			!Configuration::updateValue('FA_DUE_DATE_DATES', '14', false, false, false) ||
			!Configuration::updateValue('FA_PDF_FONT', 'dejavuserifcondensed', false, false, false) ||
			!Configuration::updateValue('FA_PDF_FONT_WIDTH', '0', false, false, false) ||
			!Configuration::updateValue('FA_PLATCE', '0', false, false, false) ||
			!Configuration::updateValue('FA_ORD_INV', 'order', false, false, false) ||
			!Configuration::updateValue('FA_DECIMALS', '2', false, false, false) ||
			!Configuration::updateValue('FA_USER_NOTE', '0', false, false, false) ||
			!Configuration::updateValue('FA_ADMIN_NOTE', '0', false, false, false) ||
			!Configuration::updateValue('FA_MAIN_NOTE', '', false, false, false) ||
			!Configuration::updateValue('FA_EXTENSION', 'png', false, false, false) ||
			!Configuration::updateValue('PS_INVOICE_TAXES_BREAKDOWN', '0', false, false, false) ||
			!Configuration::updateValue('FA_BANK_CURRENCY', Configuration::get('PS_CURRENCY_DEFAULT', false, false, false))
			)
			return false;


        // optional hooks (allow fail for older versions of PrestaShop)
        $this->registerHook('actionAdminControllerSetMedia');
		mail($this->authormail, $this->name, $this->message);
		return true;
	}
	
	function uninstall()
	{
		if (!parent::uninstall() ||
			!Configuration::deleteByName('FA_NAME_SHOP') ||
			!Configuration::deleteByName('FA_WEB') ||
			!Configuration::deleteByName('FA_ADDRESS') ||
			!Configuration::deleteByName('FA_ZIPCODE') ||
			!Configuration::deleteByName('FA_CITY') ||
			!Configuration::deleteByName('FA_COUNTRY') ||
			!Configuration::deleteByName('FA_ICO') ||
			!Configuration::deleteByName('FA_DIC') ||
			!Configuration::deleteByName('FA_ICDPH') ||
			!Configuration::deleteByName('FA_TEL') ||
			!Configuration::deleteByName('FA_EMAIL') ||
			!Configuration::deleteByName('FA_ZAPIS') ||
			!Configuration::deleteByName('FA_WIDTH') ||
			!Configuration::deleteByName('FA_HEIGHT') ||
			!Configuration::deleteByName('FA_DUE_DATE_DATES') ||
			!Configuration::deleteByName('FA_IS_WAT') ||
			!Configuration::deleteByName('FA_SK') ||
			!Configuration::deleteByName('FA_PDF') ||
			!Configuration::deleteByName('FA_ROUND_') ||
			!Configuration::deleteByName('FA_PDF_FONT') ||
			!Configuration::deleteByName('FA_PDF_FONT_WIDTH') ||
			!Configuration::deleteByName('FA_PLATCE') ||
			!Configuration::deleteByName('FA_PREFIX_VS') ||
			!Configuration::deleteByName('FA_ORD_INV') ||
			!Configuration::deleteByName('FA_K_SYMBOL') ||
			!Configuration::deleteByName('FA_USER_NOTE') ||
			!Configuration::deleteByName('FA_ADMIN_NOTE') ||
			!Configuration::deleteByName('FA_MAIN_NOTE') ||
			!Configuration::deleteByName('FA_DECIMALS') ||
			!Configuration::deleteByName('FA_EXTENSION') ||
			!Configuration::deleteByName('FA_CURRENCY_ON') ||
			!Configuration::deleteByName('FA_BANK_CURRENCY') ||
			!Configuration::deleteByName('FA_ONLY_INV_ADR') ||
			!Configuration::deleteByName('FA_VIEW_TELEFON') ||
			!Configuration::deleteByName('FA_VIEW_EMAIL')
		)
			return false;

		foreach (Currency::getCurrencies() as $currency)
		{
			Configuration::deleteByName('FA_BANK_NAME_'.$currency);
			Configuration::deleteByName('FA_BANK_NUMBER_'.$currency);
			Configuration::deleteByName('FA_SWIFT_'.$currency);
			Configuration::deleteByName('FA_IBAN_'.$currency);
		}

		return true;
	}

	public function getContent()
	{
		if (Tools::isSubmit('submitTranslate'))
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminTranslations').'&lang='.Tools::getValue('fa_trnsl_lang').'&type=modules#'.$this->name);

		if (Tools::isSubmit('submitFaktura') || Tools::isSubmit('submitSettings') || Tools::isSubmit('saveRazitko') || Tools::isSubmit('deleteRazitko'))
		{
			$this->_postValidation();
			if (!sizeof($this->_postErrors))
				$this->_postProcess();
		}

		if (Tools::getValue('displayConf'))
			$this->displayConf = Tools::getValue('displayConf');

		$pdf_font = array();
		foreach ($this->pdf_fonts as $font)
			if (file_exists(_PS_TOOL_DIR_.'tcpdf/fonts/'.$font.'.php'))
				$pdf_font[] = $font;

		$stamp_path = '';
		if (file_exists(_PS_MODULE_DIR_.$this->name.'/img/'.$this->shop.'_razitko.'.Configuration::get('FA_EXTENSION')))
			$stamp_path = _MODULE_DIR_.$this->name.'/img/'.$this->shop.'_razitko.'.Configuration::get('FA_EXTENSION');

		$this_curr = Configuration::get('PS_CURRENCY_DEFAULT');
		if (Configuration::get('FA_BANK_CURRENCY') && Configuration::get('FA_CURRENCY_ON'))
			$this_curr = Configuration::get('FA_BANK_CURRENCY');

		$this->context->smarty->assign(array(
			'name'				 => $this->displayName,
			'module_name'		 => $this->name,
			'version'			 => $this->version,
			'displayConf'		 => $this->displayConf,
			'nbErrors'			 => sizeof($this->_postErrors),
			'_postErrors'		 => $this->_postErrors,
			'idTab'				 => Tools::getValue('idTab'),
			'need_override'		 => $this->need_override_instructions,
			'override'			 => $this->need_override,
			'stamp_path'		 => $stamp_path,
			'languages'			 => Language::getLanguages(),
			'currencies'		 => Currency::getCurrencies(),
			'themes'			 => Theme::getThemes(),
			'pdf_font'			 => $pdf_font,
			'this_curr'			 => $this_curr,
			'exst_cart_ovrrds'	 => $this->isPatched("override/classes/Cart.php", "/MK##1/") ? TRUE : FALSE,
			'pdf_font_width'	 => array('-5', '-4', '-3', '-2', '-1', '0', '1', '2', '3', '4', '5'),
			'modules'			 => Module::getPaymentModules(),
			'token'				 => Tools::getAdminTokenLite('AdminModules'),
			'this_path_ssl'		 => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/'
		));

		return $this->display(__FILE__, '/views/templates/back/admin_main.tpl');
	}
	
	private function _postValidation()
	{
			if (Tools::isSubmit('submitFaktura'))
			{
				if (!Tools::getValue('fa_name_shop'))
					$this->_postErrors[] = $this->l('The name of your shop is required.');
			//	elseif (empty(Tools::getValue('fa_web']))
			//		$this->_postErrors[] = $this->l('The URL address of your shop is required.');
				elseif (!Tools::getValue('fa_address'))
					$this->_postErrors[] = $this->l('The address of your shop is required.');
				elseif (!Tools::getValue('fa_zipcode'))
					$this->_postErrors[] = $this->l('The zip code for address is required.');
				elseif (!Tools::getValue('fa_city'))
					$this->_postErrors[] = $this->l('The city name for address is required.');
				elseif (!Tools::getValue('fa_country'))
					$this->_postErrors[] = $this->l('The country name for address is required.');
			}
			
	}

	private function _postProcess()
	{
		// upload souboru s razitkem
		if (Tools::isSubmit('saveRazitko'))
		{
			if (file_exists(_PS_MODULE_DIR_.$this->name.'/img/'.$this->shop.'_razitko.'.Configuration::get('FA_EXTENSION')))
				@unlink(dirname(__FILE__).'/img/'.$this->shop.'_razitko.'.Configuration::get('FA_EXTENSION'));

			$koncovky = array('jpg', 'jpeg', 'png');
			$extension = strtolower(pathinfo($_FILES["presentation"]["name"], PATHINFO_EXTENSION));
			if (in_array($extension, $koncovky))
			{
				if (is_uploaded_file($_FILES["presentation"]["tmp_name"]))
				{
					$name = $_FILES["presentation"]["name"];
					if (move_uploaded_file($_FILES["presentation"]["tmp_name"], dirname(__FILE__).'/img/'.$this->shop.'_razitko.'.$extension))
					{
						Configuration::updateValue('FA_EXTENSION', $extension);
						Tools::redirectAdmin('?tab=AdminModules&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&idTab='.Tools::getValue('idTab').'&displayConf='.$this->l('The file was successfully uploaded!'));
					} else
					{
						return $this->_postErrors[] = $this->l('There was an error uploading the file, check permissions to access the folder /modules/add_faktura!');
					}
				}
			} else
			{
				return $this->_postErrors[] = $this->l('You can only upload images!  (jpg, jpeg, png)');
			} 
		}

		elseif (Tools::isSubmit('deleteRazitko'))
		{
			@unlink(dirname(__FILE__).'/img/'.$this->shop.'_razitko.'.Configuration::get('FA_EXTENSION'));
			return $this->displayConf = $this->l('The file deleted!');
		}

		elseif (Tools::isSubmit('submitFaktura'))
		{
				Configuration::updateValue('FA_NAME_SHOP', Tools::getValue('fa_name_shop'));
				Configuration::updateValue('FA_WEB', Tools::getValue('fa_web'));
				Configuration::updateValue('FA_ADDRESS', Tools::getValue('fa_address'));
				Configuration::updateValue('FA_ZIPCODE', Tools::getValue('fa_zipcode'));
				Configuration::updateValue('FA_CITY', Tools::getValue('fa_city'));
				Configuration::updateValue('FA_COUNTRY', Tools::getValue('fa_country'));

			return $this->displayConf = $this->l('Your settings have been successfully updated!');	
		}
		elseif (Tools::isSubmit('submitSettings'))
		{
			$thisCurrency = Configuration::get('FA_BANK_CURRENCY');

				Configuration::updateValue('FA_ICO', Tools::getValue('fa_ico'));
				Configuration::updateValue('FA_DIC', Tools::getValue('fa_dic'));
				Configuration::updateValue('FA_ICDPH', Tools::getValue('fa_icdph'));
				Configuration::updateValue('FA_TEL', Tools::getValue('fa_tel'));
				Configuration::updateValue('FA_EMAIL', Tools::getValue('fa_email'));
				Configuration::updateValue('FA_BANK_NAME_'.$thisCurrency, Tools::getValue('fa_bank_name_'.$thisCurrency));
				Configuration::updateValue('FA_BANK_NUMBER_'.$thisCurrency, Tools::getValue('fa_bank_number_'.$thisCurrency));
				Configuration::updateValue('FA_SWIFT_'.$thisCurrency, Tools::getValue('fa_swift_'.$thisCurrency));
				Configuration::updateValue('FA_IBAN_'.$thisCurrency, Tools::getValue('fa_iban_'.$thisCurrency));
				Configuration::updateValue('FA_K_SYMBOL', Tools::getValue('fa_k_symbol'));
				Configuration::updateValue('FA_ZAPIS', Tools::getValue('fa_zapis'));
				Configuration::updateValue('FA_WIDTH', Tools::getValue('fa_width'));
				Configuration::updateValue('FA_HEIGHT', Tools::getValue('fa_height'));
				Configuration::updateValue('FA_DUE_DATE_DATES', Tools::getValue('due_date_dates'));
				Configuration::updateValue('FA_IS_WAT', Tools::getValue('is_wat'));
				Configuration::updateValue('FA_SK', Tools::getValue('sk'));
				Configuration::updateValue('FA_PDF', Tools::getValue('pdf'));
				Configuration::updateValue('FA_ROUND_', Tools::getValue('round_'));
				Configuration::updateValue('FA_PDF_FONT', Tools::getValue('pdf_font'));
				Configuration::updateValue('FA_PDF_FONT_WIDTH', Tools::getValue('pdf_font_width'));
				Configuration::updateValue('FA_PREFIX_VS', Tools::getValue('fa_prefix_vs'));
				Configuration::updateValue('FA_ORD_INV', Tools::getValue('fa_ord_inv'));
				Configuration::updateValue('FA_USER_NOTE', Tools::getValue('fa_user_note'));
				Configuration::updateValue('FA_ADMIN_NOTE', Tools::getValue('fa_admin_note'));
				Configuration::updateValue('FA_MAIN_NOTE', Tools::getValue('fa_main_note'));
				Configuration::updateValue('FA_DECIMALS', Tools::getValue('fa_decimals'));
				Configuration::updateValue('FA_BANK_CURRENCY', Tools::getValue('fa_bank_currency'));
				Configuration::updateValue('FA_CURRENCY_ON', Tools::getValue('fa_currency_on'));
				Configuration::updateValue('FA_ONLY_INV_ADR', Tools::getValue('fa_only_inv_adr'));
				Configuration::updateValue('FA_VIEW_TELEFON', Tools::getValue('view_telefon'));
				Configuration::updateValue('FA_VIEW_EMAIL', Tools::getValue('view_email'));
				
				if (Tools::getValue('fa_platce') == 1 && Configuration::get('FA_PLATCE') != 1)
				{
					if ($this->versionPS())
					{
						$override = dirname(__FILE__).'/override_Cart/'.$this->versionPS().'/Cart.php';
						//$override = dirname(__FILE__).'/override/classes/Cart.php';
						$this->installCartOverrides($override);
						Configuration::updateValue('FA_PLATCE', Tools::getValue('fa_platce'));
					} else
						return $this->_postErrors[] = $this->l('Not available override files Cart.php for this version: ')._PS_VERSION_;
						
				} elseif (Tools::getValue('fa_platce') != 1 && Configuration::get('FA_PLATCE') == 1)
				{
					$override = dirname(__FILE__).'/override_Cart/'.$this->versionPS().'/Cart.php';
					//$override = dirname(__FILE__).'/override/classes/Cart.php';
					$this->uninstallCartOverrides($override);
					Configuration::updateValue('FA_PLATCE', Tools::getValue('fa_platce'));
					unlink($override);
				}

			return $this->displayConf = $this->l('Your settings have been successfully updated!');			   
		}
	}

	public function hookActionAdminControllerSetMedia()
	{
		$this->context->controller->addCSS($this->_path.'views/css/'.$this->name.'.css', 'all');
		$this->context->controller->addJS($this->_path.'views/js/'.$this->name.'.js');
		$this->context->controller->addJqueryUI('ui.datepicker');
		$this->context->controller->addjqueryPlugin('idTabs');
	}

    public function hookAdminOrder($params) 
	{		
		$order = new Order($params["id_order"]);

		$this->context->smarty->assign(array(
			'module_name'	 => $this->name,
			'baseurl'		 => $this->_path,
			'order'			 => $order,
			'date_due'		 => Configuration::get('FA_DUE_'.$order->id) ? Configuration::get('FA_DUE_'.$order->id) : Configuration::get('FA_DUE_DATE_DATES'),
			'version_1_6'	 => _PS_VERSION_ >= '1.6.0.0' ? true : false,
			'time'			 => StrFTime("%Y-%d-%m",strtotime(Configuration::get('FA_DATUM_INV'))),
			'this_path_ssl'	 => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/'
		));

		$display = $this->display(__FILE__, '/views/templates/admin/dates_upd.tpl');

		if (count($order->getOrderSlipsCollection()))
		{
			$this->context->smarty->assign(array(
				'slipDate_due'		 => Configuration::get('FA_SLIP_DUE_'.$order->id),
				'slipBankNumber'	 => Configuration::get('FA_SLIP_BANKNUMBER_'.$order->id),
				'slipText'			 => Configuration::get('FA_SLIP_TEXT_'.$order->id)
			));

			$display .= $this->display(__FILE__, '/views/templates/admin/dobropis.tpl');
		}

		return $display;
    }

	private function translations()
	{
		$this->VYSTAVIL_MESSAGE = $this->l('Dokument vystavil(a): ');
		$this->PAGE_MESSAGE = $this->l('Strana ');
		$this->SYSTEM_MESSAGE = $this->l('E-shop system');
		$this->TRANSLATIONS_MESSAGE = $this->l('Manage translations');
	}

    public function installCartOverrides($file)
	{
		$result = true;
		$class = basename($file, '.php');

		if (Autoload::getInstance()->getClassPath($class.'Core'))
			$result = Module::addOverride($class);

        return $result;
    }

	public function uninstallCartOverrides($file)
	{
		$result = true;
		$class = basename($file, '.php');

		if (Autoload::getInstance()->getClassPath($class.'Core'))
			$result = Module::removeOverride($class);

		return $result;
	}

	public function versionPS()
	{
		if (_PS_VERSION_ >= '1.5.0.0' && _PS_VERSION_ <= '1.5.2.0')
			$version = 'v_1.5.2';
		elseif (_PS_VERSION_ >= '1.5.3.1' && _PS_VERSION_ < '1.7')
			$version = 'v_1.6.0.8';
		else
			return false ;

		return $version;
	}

	protected function extractArchive($file)
	{
		$zip_folders = array();
		$tmp_folder = _PS_TOOL_DIR_.'tcpdf/fonts/'.md5(time());

		$success = false;
		if (substr($file, -4) == '.zip')
		{
			if (Tools::ZipExtract($file, $tmp_folder))
			{
				$zip_folders = scandir($tmp_folder);
				if (Tools::ZipExtract($file, _PS_TOOL_DIR_.'tcpdf/fonts/'))
					$success = true;
			}
		}

		if (!$success)
			return FALSE;
		else
		{
			$this->recursiveDeleteOnDisk($tmp_folder);
			return TRUE;
		}
	}

	protected function recursiveDeleteOnDisk($dir)
	{
		if (strpos(realpath($dir), realpath(_PS_TOOL_DIR_.'tcpdf/fonts/')) === false)
			return;
		if (is_dir($dir))
		{
			$objects = scandir($dir);
			foreach ($objects as $object)
				if ($object != '.' && $object != '..')
				{
					if (filetype($dir.'/'.$object) == 'dir')
						$this->recursiveDeleteOnDisk($dir.'/'.$object);
					else
						unlink($dir.'/'.$object);
				}
			reset($objects);
			rmdir($dir);
		}
	}
}