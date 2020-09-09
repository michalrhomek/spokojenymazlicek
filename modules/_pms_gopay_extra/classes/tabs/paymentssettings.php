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
include_once(dirname(__FILE__).'/../../controllers/admin/tabs/AdminPaymentButtonsController.php');

class Pms_GoPay_Extra_PaymentsSettings extends Pms_GoPay_Extra_ModuleConfig
{
	const TABNAME = 'paymentssettings';
	protected $admin_payment_button;
	protected $display;
	private $currency;
	private $missingPayments = array();

	/* array('name' => '', 'default' => '', 'isHTML' => false/true, 'isBool' => false/true, 'isLang' => false/true, 'isArray' => false/true) */
	public $configurations = array(
		array('name' => '_DISPLAY_IMAGES', 'default' => 1, 'isBool' => true),
		array('name' => '_DISPLAY_NAMES', 'default' => 1, 'isBool' => true),
		array('name' => '_DISPLAY_DESC', 'default' => 1, 'isBool' => true),
		array('name' => '_BUTTONS_MODE', 'default' => 1),
		array('name' => '_ID_CURRENCY', 'ps_default' => 'PS_CURRENCY_DEFAULT')
	);

	public function init()
	{
		$this->currentUrl = $this->currentUrl.'&idTab='.self::TABNAME;

		$this->context->controller->addjQueryPlugin(array('ajaxfileupload'));
		$this->context->controller->addJqueryUi('ui.widget');
		$this->context->controller->addJS(array(
			_PS_JS_DIR_.'tiny_mce/tiny_mce.js',
			_PS_JS_DIR_.'admin/tinymce.inc.js',
		));

		$this->context->controller->addJs($this->_M->module_dir.'/views/js/admin/tabs/'.self::TABNAME.'.js');
		$this->context->controller->addCss($this->_M->module_dir.'/views/css/admin/tabs/'.self::TABNAME.'.css');

		$this->currency = new Currency((int)Tools::getValue('_ID_CURRENCY', Configuration::get($this->_M->MFIX.'_ID_CURRENCY')));
	}

	public function getTitle()
	{
		return $this->l('Payment buttons', self::TABNAME);
	}

	public function getIcon()
	{
		return 'icon-credit-card';
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
		$this->missingPayments = $this->getMissingPayments();

		if (Tools::getValue('_ID_CURRENCY'))
		{
			Configuration::updateValue($this->_M->MFIX.'_ID_CURRENCY', (int)Tools::getValue('_ID_CURRENCY'));
		}
		
		if (Tools::isSubmit('submitImportPayments'))
		{
			if ($this->setNewPayments())
				Tools::redirectAdmin($this->currentUrl.'&conf=5');
		}
		elseif (Tools::isSubmit('status'.$this->_M->name.'_buttons') && Tools::getValue('id_payment_button') && Tools::getValue('ajax'))
		{
			// Change object statuts (active, inactive)
			$object = new PAYMENTButtons(Tools::getValue('id_payment_button'), null, null, $this->currency->id);

			if (Validate::isLoadedObject($object))
			{
				if ($object->toggleStatus())
					die (Tools::jsonEncode(array(
						'success' => 1,
						'text' => $this->l('Status updated.', self::TABNAME)
					)));
				else
					die (Tools::jsonEncode(array(
						'success' => 0,
						'text' => $this->l('An error occurred while updating the status.', self::TABNAME)
					)));
			}
			else
				die (Tools::jsonEncode(array(
					'success' => 0,
					'text' => $this->l('Status object not loaded.', self::TABNAME)
				)));
		}

		if ($error = $this->_validate())
			return $this->_M->displayError($error);

		return parent::postProces(self::TABNAME, $this->configurations);
	}

	public function showForm()
	{
		$addJsDefs = $this->_M->hookDisplayBackOfficeHeader(array(
				'addJsDefs' => array(
					'confirmDelete' => $this->l('Are you sure you want to delete the logo?', self::TABNAME)
			)
		));

		$this->admin_payment_button = new AdminPaymentButtonsController($this->_M, $this->currency->id);
		$this->admin_payment_button->currentUrl = $this->currentUrl;
		$this->admin_payment_button->init();

		$content = $this->admin_payment_button->postProcess();

		if (Tools::isSubmit('updatepms_gopay_extra_buttons'))
		{
			if (count($this->admin_payment_button->errors))
				$content .= $this->_M->displayError($this->admin_payment_button->errors);

			$content .= $this->admin_payment_button->renderForm();
		} else
		{
			if (count($this->missingPayments) > 0)
			{
				$addJsDefs .= $this->_M->hookDisplayBackOfficeHeader(array(
						'addJsDefs' => array(
							'missingPayments' => true
					)
				));
			}

			$content .= $this->_M->renderFormClass($this->getForm(), $this->getConfig(self::TABNAME, $this->configurations), self::TABNAME);
		}

		return $addJsDefs.$content;
	}

	private function getForm()
	{
		$missing_codes = array();
		foreach ($this->missingPayments as $button)
			$missing_codes[] = $button->paymentInstrument;

		$fields_form = array(
			'basic-buttons-settings' => array(
				'form' => array(
					'legend' => array(
						'title' => $this->l('Setting for payment buttons', self::TABNAME),
						'icon' => 'icon-tasks'
					),
					'input' => array(
						array(
							'type' => 'switch',
							'label' => $this->l('Display logo in payment button', self::TABNAME),
							'name' => '_DISPLAY_IMAGES',
							'desc' => '',
							'required' => false,
							'is_bool' => true,
							'values' => array(
								array(
									'id' => 'display_on',
									'value' => 1,
									'label' => $this->l('Yes', self::TABNAME)
								),
								array(
									'id' => 'display_on',
									'value' => 0,
									'label' => $this->l('No', self::TABNAME)
								)
							)
						),
						array(
							'type' => 'switch',
							'label' => $this->l('Display payment name in button', self::TABNAME),
							'name' => '_DISPLAY_NAMES',
							'desc' => '',
							'required' => false,
							'is_bool' => true,
							'values' => array(
								array(
									'id' => 'names_on',
									'value' => 1,
									'label' => $this->l('Yes', self::TABNAME)
								),
								array(
									'id' => 'names_off',
									'value' => 0,
									'label' => $this->l('No', self::TABNAME)
								)
							)
						),
						array(
							'type' => 'switch',
							'label' => $this->l('Display payment description in button', self::TABNAME),
							'name' => '_DISPLAY_DESC',
							'desc' => '',
							'required' => false,
							'is_bool' => true,
							'values' => array(
								array(
									'id' => 'desc_on',
									'value' => 1,
									'label' => $this->l('Yes', self::TABNAME)
								),
								array(
									'id' => 'desc_off',
									'value' => 0,
									'label' => $this->l('No', self::TABNAME)
								)
							)
						),
						array(
							'type' => 'hidden',
							'name' => 'idTab'
						)
					),
					// Submit Button
					'submit' => array(
						'title' => $this->l('Save', self::TABNAME),
						'name' => 'submit_'.self::TABNAME,
						'icon' => 'process-icon-save'
					)
				)
			),
			'buttons-settings' => array(
				'form' => array(
					'legend' => array(
						'title' => $this->l('Payment buttons template', self::TABNAME),
						'icon' => 'icon-list'
					),
					'input' => array(
						array(
							'type' => 'html',
							'name' => '',
							'label' => ' ',
							'title' => '',
							'col' => 12,
							'html_content' => $this->getGroupPayments()
						),
						array(
							'type' => 'hidden',
							'name' => 'idTab'
						)
					),
					// Submit Button
					'submit' => array(
						'title' => $this->l('Save', self::TABNAME),
						'name' => 'submit_'.self::TABNAME,
						'icon' => 'process-icon-save'
					)
				)
			),
			'buttons-list' => array(
				'form' => array(
					'legend' => array(
						'title' => $this->l('Payment buttons preference', self::TABNAME),
						'icon' => 'icon-pencil'
					),
					'input' => array(
						array(
							'type' => 'select',
							'name' => '_ID_CURRENCY',
							'label' => $this->l('Settings for currency', self::TABNAME),
							'desc' => '',
							'required' => false,
							'lang' => false,
							'onchange' => 'this.form.submit()',
							'options' => array(
								'query' => Currency::getCurrencies(),
								'id' => 'id_currency',
								'name' => 'name'
							)
						),
						array(
							'type' => 'html',
							'name' => '',
							'label' => ' ',
							'title' => '',
							'col' => 12,
							'html_content' => count($missing_codes) > 0 ?$this->_M->displayWarning('<p>'.sprintf($this->_M->l('For this currency missing %1$s payment buttons!', self::TABNAME), '<b>'.count($missing_codes).'</b>').'<br>'.sprintf($this->_M->l('Payment codes: %1$s', self::TABNAME), '<b>'.implode(", ", $missing_codes).'</b>').'</p><center id="newPayments"><button type="submit" name="submitImportPayments" class="mx-1 btn btn-primary"><i class="process-icon-download"></i>'.$this->_M->l('Import all new payments').'</button></center>') : '')
						,
						array(
							'type' => 'html',
							'name' => '',
							'label' => ' ',
							'title' => '',
							'col' => 12,
							'html_content' => $this->admin_payment_button->renderList()
						)
					)
				)
			),
		);

		return $fields_form;
	}

	private function getGroupPayments()
	{
		$this->context->smarty->assign(array(
			'moduleName'	 => $this->_M->name,
			'fullMode'		 => Configuration::get($this->_M->MFIX.'_GATEWAY_MODE'),
			'_BUTTONS_MODE'	 => Configuration::get($this->_M->MFIX.'_BUTTONS_MODE')
		));

		return $this->_M->display($this->_M->name, '/views/templates/admin/classes/tabs/paymentsTypes.tpl');
	}

	public function getMissingPayments()
	{
		$allPaymentCodes = array();
		$id_shop = $this->context->shop->id;
		$id_lang = $this->context->language->id;
		$payments = Pms_GoPay_Extra_RestAPI::enabledPaymentInstruments($this->currency->iso_code);
		$allPayments = PAYMENTButtons::getAllPaymentButtons($id_lang, $id_shop, $this->currency->id);

		if (isset($payments->enabledPaymentInstruments) && count($payments->enabledPaymentInstruments) > 0)
		{
			foreach ($payments->enabledPaymentInstruments as $key=>$payment)
			{
				$allPaymentCodes[] = $payment->paymentInstrument;
				$id_button = PAYMENTButtons::getIdByCode($payment->paymentInstrument, $id_shop);
				$button = PAYMENTButtons::getPaymentButtonDetail($id_button, $id_lang, $id_shop, $this->currency->id);

				if(Validate::isLoadedObject($button))
				{
					unset($payments->enabledPaymentInstruments[$key]);
				}

				if (count($payment->enabledSwifts) > 0)
				{
					foreach ($payment->enabledSwifts as $swift)
					{
						$id_button = PAYMENTButtons::getIdByCode($swift->swift, $id_shop);
						$button = new PAYMENTButtons($id_button, null, $id_shop, $this->currency->id);
						if(!Validate::isLoadedObject($button))
						{
							$new_swift = new stdClass();
							$new_swift->paymentInstrument = $swift->swift;
							$new_swift->label = $swift->label;
							$new_swift->image = $swift->image;
							$new_swift->isOnline = $swift->isOnline;
							$new_swift->group = $payment->paymentInstrument;
							$new_swift->isSwift = 1;
							$payments->enabledPaymentInstruments[] = $new_swift;
						}
					}

					unset($payment->enabledSwifts);
				}
			}

			foreach ($allPayments as $button)
				if ($button['payment_code'] != 'ACCOUNT' && !in_array($button['payment_code'], $allPaymentCodes))
					PAYMENTButtons::setIsVisible($button['id_payment_button'], $id_shop, $this->currency->id, false);
				else
					PAYMENTButtons::setIsVisible($button['id_payment_button'], $id_shop, $this->currency->id);

			return $payments->enabledPaymentInstruments;
		}

		return array();
	}


	public function setNewPayments()
	{
		$id_shop = $this->context->shop->id;
		$id_lang = $this->context->language->id;
		$languages = Language::getLanguages(false);
		if (isset($this->missingPayments) && count($this->missingPayments) > 0)
		{
			foreach ($this->missingPayments as $payment)
			{
				if ($id_button = PAYMENTButtons::getIdByCode($payment->paymentInstrument))
				{
					PAYMENTButtons::setPaymentStrict($id_button, $id_shop, $this->currency->id);
				}
				else
				{
					$new_payment = new PAYMENTButtons();
					$new_payment->payment_code = $payment->paymentInstrument;
					$new_payment->payment_group = $payment->group;
					$new_payment->id_currency = $this->currency->id;
					$new_payment->isSwift = isset($payment->isSwift) && $payment->isSwift ? 1: 0;
					$new_payment->isOnline = isset($payment->isOnline) && $payment->isOnline ? 1: 0;
					$new_payment->isGroup = isset($payment->isSwift) && $payment->isSwift ? 0: 1;

					foreach ($payment->label as $key=>$label)
						$new_payment->payment_name[Language::getIdByIso($key)] = $label;

					foreach ($languages as $lang)
					{
						$file_name = $lang['id_lang'].'_'.$payment->paymentInstrument.'.png';
						$file = file($payment->image->normal);
						if ($file && file_put_contents(_PS_MODULE_DIR_.$this->_M->name.'/views/img/payments/'.$file_name, $file))
							$new_payment->payment_logo[$lang['id_lang']] = $file_name;
					}

					$new_payment->add();
				}
			}
		}

		return true;
	}

	public function updatePositions()
	{
		$way = (int)(Tools::getValue('way'));
		$id_payment_button = (int)(Tools::getValue('id_payment'));
		$positions = Tools::getValue('payment_button');
		$id_currency = Tools::getValue('id_currency');

		foreach ($positions as $position => $value)
		{
			$pos = explode('_', $value);

			if (isset($pos[2]) && (int)$pos[2] === $id_payment_button)
			{
				if ($PAYMENTButton = new PAYMENTButtons((int)$pos[2], null, null, $id_currency))
				{
					if (isset($position) && $PAYMENTButton->updatePosition($way, $position))
					{
						return array(
							'message_code' => Pms_GoPay_Extra::CODE_SUCCESS,
							'message' => $this->l('Positions updated', self::TABNAME)
						);
					} else
					{
						return array(
							'message_code' => Pms_GoPay_Extra::CODE_ERROR,
							'message' => sprintf($this->_M->l('Unable to update payment %1$s to position %2$s', self::TABNAME), '<b>'.(int)$id_payment_button.'</b>', '<b>'.(int)$position.'</b>')
						);
					}
				} else
				{
					return array(
						'message_code' => Pms_GoPay_Extra::CODE_ERROR,
						'message' => sprintf($this->_M->l('This payment %1$s can\'t be loaded', self::TABNAME), '<b>'.(int)$id_payment_button.'</b>')
					);
				}

				break;
			}
		}
	}

	public function uploadLogo()
	{
		$allowedExtensions = array('jpeg', 'gif', 'png', 'jpg');
		$id_lang = Tools::getValue('id_lang');

		$logo = (isset($_FILES['logo_input_'.$id_lang]) ? $_FILES['logo_input_'.$id_lang] : false);
		if ($logo && !empty($logo['tmp_name']) && $logo['tmp_name'] != 'none'
			&& (!isset($logo['error']) || !$logo['error'])
			&& $logo['name']
			&& is_uploaded_file($logo['tmp_name']))
		{
			$extension = strtolower(pathinfo($logo['name'], PATHINFO_EXTENSION));
			if (in_array($extension, $allowedExtensions))
			{
				$file_name = $id_lang.'_'.Tools::getValue('payment_code').'.'.$extension;
				if (move_uploaded_file($logo["tmp_name"], _PS_MODULE_DIR_.$this->_M->name.'/views/img/payments/'.$file_name))
				{
					@unlink($file);
					return array(
						'message_code' => Pms_GoPay_Extra::CODE_SUCCESS,
						'message' => $this->l('Logo uploaded', self::TABNAME),
						'logo_url' => _MODULE_DIR_.$this->_M->name.'/views/img/payments/'.$file_name
					);
						
				} else
					return array(
						'message_code' => Pms_GoPay_Extra::CODE_ERROR,
						'message' => sprintf($this->_M->l('There was an error uploading the file, check permissions to access the folder %1$s', self::TABNAME), '<b>'.$this->_M->name.'</b>')
					);
			} else
				return array(
					'message_code' => Pms_GoPay_Extra::CODE_ERROR,
					'message' => sprintf($this->_M->l('You can only upload images %1$s! Your file is %2$s', self::TABNAME), '<b>'.implode(", ",$allowedExtensions).'</b>', '<b>'.$extension.'</b>')
				);
		} else
			return array(
				'message_code' => Pms_GoPay_Extra::CODE_ERROR,
				'message' => $this->_M->l('Cannot upload any file', self::TABNAME)
			);
	}

	public function getConfig($TABNAME, $configurations)
	{
		return parent::getConfig($TABNAME, $configurations);
	}

	public function _validate()
	{
		$condition = array();
		if (Tools::isSubmit('submit_'.self::TABNAME))
		{/*
			if (strlen(Tools::getValue('_GO_ID')) != 10)
				$condition[] = $this->l('GoID must be a numeric value and string length 10!', self::TABNAME);
		*/}

		if (count($condition) > 0)
			return implode('<br>', $condition);
	}
}
