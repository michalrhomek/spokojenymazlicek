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
class AdminPaymentButtonsController extends AdminController
{
	const TABNAME = 'AdminPaymentButtonsController';
	public $_M;
	public $currentUrl;
	public $id_currency;
    protected $_use_found_rows = false;
 
	/** @var object PAYMENTButtons() instance for navigation*/
	protected $GP_payment_button;

	public function __construct(Module $module, $id_currency)
	{
		$this->_M = $module;
		$this->id_currency = $id_currency;

		$this->bootstrap = true;
		$this->table = $this->_M->name.'_buttons';
		$this->identifier = 'id_payment_button';
		$this->position_identifier = $this->identifier.'_to_move';
		$this->list_id = 'payment_button';
		$this->className = 'PAYMENTButtons';
		$this->lang = true;

		parent::__construct();

		$this->addRowAction('edit');
		/*$this->addRowAction('delete');
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected', self::TABNAME),
                'confirm' => $this->l('Delete selected items?', self::TABNAME),
                'icon' => 'icon-trash'
            )
        );*/

		$this->_orderBy = 'position';
		$this->tpl_list_vars['icon'] = 'icon-folder-close';
		$this->tpl_list_vars['title'] = $this->_M->l('Modules', self::TABNAME);

		$this->_select = '
			bs.`active`,
			bs.`position`,
			bs.`payment_fee`,
			bs.`payment_fee_type`
		';

		$this->_join = '
			LEFT JOIN `'._DB_PREFIX_.$this->table.'_strict` bs ON (bs.`'.$this->identifier.'` = a.`'.$this->identifier.'`)
		';

		$this->_where = '
			AND bs.`id_shop` IN ('.implode(', ', array_map('intval', Shop::getContextListShopID())).')
			AND bs.`id_currency` = '.(int)$this->id_currency.'
			AND bs.`visible` = 1
		';
		if (!Configuration::get($this->_M->MFIX.'_GATEWAY_MODE') || Configuration::get($this->_M->MFIX.'_BUTTONS_MODE') == 1)
			$this->_where .= ' AND a.`isGroup` = 1';

		$this->tabAccess = Profile::getProfileAccess($this->context->employee->id_profile, 1);
		$this->fields_list = array(
			'id_payment_button' => array(
				'title' => $this->l('ID', self::TABNAME),
				'align' => 'center',
				'class' => 'fixed-width-xs'
			),
			'payment_logo' => array(
				'title' => $this->l('Logo', self::TABNAME),
				'callback' => 'getLogo',
				'orderby' => false,
				'search' => false,
				'align' => 'center',
				'class' => 'fixed-width-xs'
			),
			'payment_code' => array(
				'title' => $this->l('Payment code', self::TABNAME),
				'align' => 'center',
				'class' => 'fixed-width-xs'
			),
			'payment_name' => array(
				'title' => $this->l('Name', self::TABNAME),
				'width' => 'auto',
				'callback' => 'hidePAYMENTButtonsPosition',
				'callback_object' => 'PAYMENTButtons'
			),
			'payment_desc' => array(
				'title' => $this->l('Description', self::TABNAME),
				'maxlength' => 90,
				'orderby' => false
			),
			'position' => array(
				'title' => $this->l('Position', self::TABNAME),
				'align' => 'center',
				'class' => 'fixed-width-sm',
				'filter_key' => 'bs!position',
				'position' => 'position'
			),
			'active' => array(
				'title' => $this->l('Displayed', self::TABNAME),
				'class' => 'fixed-width-sm',
				'active' => 'status',
				'align' => 'center',
				'ajax' => true,
				'remove_onclick' => true,
				'filter_key' => 'bs!active',
				'type' => 'bool',
				'orderby' => false
		));

		if (Configuration::get($this->_M->MFIX.'_PRICE_DIFFERENT'))
		{
			foreach ($this->fields_list as $key=>$val)
			{
				$column[$key] = $val;
				if ($key == 'payment_desc')
					$column['payment_fee'] =  array(
						'title' => $this->l('Fee amount', self::TABNAME),
						'align' => 'text-center',
						'class' => 'fixed-width-xs',
						'callback' => 'getFeeType',
						'orderby' => false,
						'hint' => $this->l('Value excluding VAT which is added to the shipping cost, VAT is calculated according to the selected carrier', self::TABNAME)
					);
			}

			$this->fields_list = $column;
		}
	}

	public function init()
	{
		parent::init();
		$this->content_only = true;

		$this->token = Tools::getAdminTokenLite('AdminModules');
		self::$currentIndex = $this->currentUrl;
	}

	public function getLogo($echo, $tr)
	{
		if (file_exists($this->_M->_path.'views/img/payments/'.$echo))
			return '<img src="../modules/'.$this->_M->name.'/views/img/payments/'.$echo.'" class="button_logo_img img-thumbnail" alt="">';
	}

	public function getFeeType($echo, $tr)
	{
		$currency = new Currency($this->id_currency);

		return $tr['payment_fee'].' '.($tr['payment_fee_type'] ? '%' : $currency->sign);
	}

	/* pro v. 1.7  nastavení access přístupu  neměnit!  */
	public function getTabSlug()
	{
		return 'ROLE_MOD_TAB_ADMINCMSCONTENT_';
	}

	public function renderList()
	{
		//$this->initToolbar();
		$this->_group = 'GROUP BY a.`'.$this->identifier.'`';

		return parent::renderList();
	}

	public function postProcess()
	{
		if (Tools::isSubmit('submitAdd'.$this->table)) {
			$this->action = 'save';
			$object = new PAYMENTButtons((int)Tools::getValue($this->identifier), null, null, $this->id_currency);
			$languages = Language::getLanguages(false);

			foreach ($languages as $language)
			{
				$object->payment_name[$language['id_lang']] = Tools::getValue('payment_name_'.$language['id_lang']);
				$object->payment_desc[$language['id_lang']] = Tools::getValue('payment_desc_'.$language['id_lang']);
				$object->payment_logo[$language['id_lang']] = Tools::getValue('payment_logo_'.$language['id_lang']);
			}
			$object->active = Tools::getValue('active');

			if (Configuration::get($this->_M->MFIX.'_PRICE_DIFFERENT'))
			{
				$object->payment_fee_type = Tools::getValue('payment_fee_type');
				$object->payment_fee = Tools::getValue('payment_fee');
			}

			if ($object->update()) {
				Tools::redirectAdmin($this->currentUrl.'&conf=3&'.$this->identifier.'n='.(int)$object->id);
			}
			return $object;
		} elseif (Tools::isSubmit('status'.$this->table) && Tools::getValue($this->identifier) && !Tools::getValue('ajax'))
		{
			// Change object statuts (active, inactive)
			if (Validate::isLoadedObject($object = $this->loadObject())) {
				if ($object->toggleStatus()) {
					Tools::redirectAdmin($this->currentUrl.'&conf=5');
				} else {
					$this->errors[] = $this->l('An error occurred while updating the status.', self::TABNAME);
				}
			} else {
				$this->errors[] = $this->l('An error occurred while updating the status for an object.', self::TABNAME)
					.' <b>'.$this->table.'</b> '.$this->l('(cannot load object)', self::TABNAME);
			}
		} elseif (Tools::isSubmit('submitBulkdisableSelection'.$this->table) || Tools::isSubmit('submitBulkenableSelection'.$this->table))
		{
			// Change object statuts (active, inactive)
			$action = 'enableStatus';
			if (Tools::isSubmit('submitBulkdisableSelection'.$this->table))
				$action = 'disableStatus';

			if ($boxes = Tools::getValue($this->list_id.'Box'))
			{
				$result = true;
				foreach ($boxes as $id_payment_button)
				{
					$object = new PAYMENTButtons((int)$id_payment_button, null, null, $this->id_currency);
					if (Validate::isLoadedObject($object))
					{
						if (!$object->{$action}())
							$this->errors[] = $this->l('An error occurred while updating the status.', self::TABNAME);
					} else {
						$this->errors[] = $this->l('An error occurred while updating the status for an object.', self::TABNAME)
							.' <b>'.$this->table.'</b> '.$this->l('(cannot load object)', self::TABNAME);
					}
				}

				if (!$this->errors)
					Tools::redirectAdmin($this->currentUrl.'&conf=5');
			} else {
				$this->errors[] = $this->l('You must select at least one element to delete.', self::TABNAME);
			}
		} elseif (Tools::isSubmit('position'))
		{
			$id = (int)Tools::getValue($this->identifier, Tools::getValue($this->position_identifier, 1));
			$object = new PAYMENTButtons($id, null, null, $this->id_currency);
			if (!Validate::isLoadedObject($object))
			{
				$this->errors[] = $this->l('An error occurred while updating the status for an object.', self::TABNAME)
					.' <b>'.$this->table.'</b> '.$this->l('(cannot load object)', self::TABNAME);
			} elseif (!$object->updatePosition((int)Tools::getValue('way'), (int)Tools::getValue('position'), (int)$this->id_currency))
			{
				$this->errors[] = $this->l('Failed to update the position.', self::TABNAME);
			} else {
				Tools::redirectAdmin($this->currentUrl.'&'.$this->table.'Orderby=position&'.$this->table.'Orderway=asc&conf=5');
			}
		} elseif (Tools::isSubmit('submitDel'.$this->table) || Tools::isSubmit('submitBulkdelete'.$this->table))
		{
			// Delete multiple objects
			if (Tools::isSubmit($this->list_id.'Box'))
			{
				$result = true;
				$result = PAYMENTButtons::deleteSelections(Tools::getValue($this->list_id.'Box'), $this->id_currency);
				if ($result) {
					PAYMENTButtons::cleanPositions((int)Tools::getValue($this->identifier), $this->id_currency);
					Tools::redirectAdmin($this->currentUrl.'&conf=2&'.$this->identifier.'='.(int)Tools::getValue($this->identifier));
				}
				$this->errors[] = $this->l('An error occurred while deleting this selection.', self::TABNAME);
			} else {
				$this->errors[] = $this->l('You must select at least one element to delete.', self::TABNAME);
			}
		} elseif (Tools::isSubmit('delete'.$this->table))
		{
			// Delete object
			if (Validate::isLoadedObject($object = $this->loadObject()))
			{
				// check if request at least one object with noZeroObject
				if (isset($object->noZeroObject) && count($taxes = call_user_func(array($this->className, $object->noZeroObject))) <= 1)
				{
					$this->errors[] = $this->l('You need at least one object.', self::TABNAME)
						.' <b>'.$this->table.'</b><br />'.$this->l('You cannot delete all of the items.', self::TABNAME);
				} else {
					if ($this->deleted) {
						$object->deleted = 1;
						if ($object->update()) {
							Tools::redirectAdmin($this->currentUrl.'&conf=1&token='.Tools::getValue('token'));
						}
					} elseif ($object->delete()) {
						Tools::redirectAdmin($this->currentUrl.'&conf=1');
					}
					$this->errors[] = $this->l('An error occurred during deletion.', self::TABNAME);
				}
			} else {
				$this->errors[] = $this->l('An error occurred while deleting the object.', self::TABNAME)
					.' <b>'.$this->table.'</b> '.$this->l('(cannot load object)', self::TABNAME);
			}
		}

		parent::postProcess();
	}

	public function setHelperDisplay(Helper $helper)
	{
		parent::setHelperDisplay($helper);
		$helper->module = $this->_M;
		$helper->override_folder = '/';

		$this->helper = $helper;
	}

	protected function loadObject($opt = false)
	{
		$id = (int)Tools::getValue($this->identifier);
		if ($id && Validate::isUnsignedId($id)) {
			if (!$this->object) {
				$this->object = new $this->className($id, null, null, $this->id_currency);
			}
			if (Validate::isLoadedObject($this->object)) {
				return $this->object;
			}
			// throw exception
			$this->errors[] = Tools::displayError('The object cannot be loaded (or found)');
			return false;
		}

		return $this->object;
	}

	public function renderForm()
	{
		if (!$this->loadObject(true)) {
			return;
		}

		if (Validate::isLoadedObject($this->object)) {
			$this->display = 'edit';
		} else {
			$this->display = 'add';
		}

		$this->initToolbar();

		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Edit Payment Button: ', self::TABNAME).$this->object->payment_code,
				'icon' => 'icon-folder-close'
			),
			'input' => array(
				array(
					'type' => 'html',
					'name' => '',
					'col' => 12,
					'html_content' => '<center>'.$this->getLogos().'</center>'
				),
				array(
					'type' => 'text',
					'label' => $this->l('Name', self::TABNAME),
					'name' => 'payment_name',
					'class' => '',
					'required' => true,
					'lang' => true,
					'hint' => $this->l('Invalid characters:', self::TABNAME).' &lt;&gt;;=#{}'
				),
				array(
					'type' => 'logo',
					'label' => $this->l('Logo'),
					'accept' => '.jpg,.jpeg,.png,.gif',
					'lang' => true,
					'name' => 'payment_logo'
				),
				// custom template
				array(
					'type' => 'textarea',
					'label' => $this->l('Description', self::TABNAME),
					'name' => 'payment_desc',
					'lang' => true,
					'autoload_rte' => true,
					'rows' => 5,
					'cols' => 40,
					'hint' => $this->l('Invalid characters:', self::TABNAME).' &lt;&gt;;=#{}'
				),
				array(
					'type' => 'switch',
					'label' => $this->l('Display payment name in button', self::TABNAME),
					'name' => 'active',
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
					'type' => 'hidden',
					'id' => '_PAYMENT_CODE',
					'name' => '_PAYMENT_CODE'
				),
				array(
					'type' => 'hidden',
					'id' => 'id_currency',
					'name' => 'id_currency'
				),
				array(
					'type' => 'hidden',
					'id' => 'id_shop',
					'name' => 'id_shop'
				)
			),
			'submit' => array(
				'title' => $this->l('Save', self::TABNAME),
			)
		);
		
		if (Configuration::get($this->_M->MFIX.'_PRICE_DIFFERENT'))
		{
			$currency = new Currency($this->id_currency);
			$price = array(
				array(
					'type' => 'select',
					'name' => 'payment_fee_type',
					'label' => $this->l('Type of fee', self::TABNAME),
					'required' => false,
					'lang' => false,
					'options' => array(
						'query' => array(
							array(
								'id' => '1',
								'name' => $this->l('Percent', self::TABNAME)
							),
							array(
								'id' => '0',
								'name' => $this->l('Amount', self::TABNAME)
							)
						),
						'id' => 'id',
						'name' => 'name'
					)
				),
				array(
					'type' => 'text',
					'name' => 'payment_fee',
					'label' => $this->l('Fee', self::TABNAME),
					'suffix' => $currency->sign.' / %',
					'desc' => $this->l('The amount of the fee, that will the customer pays for the payment through the GoPay.', self::TABNAME).'<br>'.$this->l('If you want to set a different charge for each payment button. You can setup it on the bookmark Payment buttons.', self::TABNAME),
					'class' => 'col-lg-2',
					'maxlength' => 6,
					'required' => false,
					'lang' => false
				)
			);
			
			$this->fields_form['input'] = array_merge($this->fields_form['input'], $price);
		}

		$this->fields_value['_PAYMENT_CODE'] = $this->object->payment_code;
		$this->fields_value['id_currency'] = $this->id_currency;
		$this->fields_value['id_shop'] = $this->context->shop->id;
		return parent::renderForm();
	}

	public function getLogos()
	{
		$exist = true;
		$content = '';
		$languages = Language::getLanguages(false);
		foreach ($languages as $language)
		{
			if (file_exists(_PS_ROOT_DIR_.$this->_M->module_dir.'views/img/payments/'.$this->object->payment_logo[$language['id_lang']]))
				$img = '../'.$this->_M->module_dir.'/views/img/payments/'.$this->object->payment_logo[$language['id_lang']];
			else
			{
				$exist = false;
				$img = '../img/404.gif';
			}

				$content .= '
<div class="form-group translatable-field lang-'.$language['id_lang'].'"'.($language['id_lang'] != Configuration::get('PS_LANG_DEFAULT') ? 'style="display:none;' : '').'">
	<div class="col-sm-2" style="float:none">
		<div id="button_logo_block" class="panel">
			<div class="panel-heading">
				'.$this->l('Logo', self::TABNAME);
			if ($exist)
				$content .= '
				<div class="panel-heading-action">
					<a id="logo_remove_'.$language['id_lang'].'" class="btn btn-default" style="" href="javascript:removeLogo('.$language['id_lang'].');">
						<i class="icon-trash"></i>
					</a>
				</div>';
			$content .= '
			</div>
			<img id="logo_img_'.$language['id_lang'].'" src="'.$img.'" class="button_logo_img img-thumbnail" alt="">
		</div>
	</div>
</div>';
		}

		return $content;
	}

	/**
	 * Object update
	 *
	 * @   bylo nutno přepsat tyto funkce pro zachování funkčnosti
	 * 
	 */
	public function processAdd()
	{
		if (!isset($this->className) || empty($this->className)) {
			return false;
		}

		$this->validateRules();
		if (count($this->errors) <= 0) {
			$this->object = new $this->className();

			$this->copyFromPost($this->object, $this->list_id);
			$this->beforeAdd($this->object);
			if (method_exists($this->object, 'add') && !$this->object->add()) {
				$this->errors[] = Tools::displayError('An error occurred while creating an object.').
					' <b>'.$this->table.' ('.Db::getInstance()->getMsgError().')</b>';
			} elseif (($_POST[$this->identifier] = $this->object->id /* voluntary do affectation here */) && $this->postImage($this->object->id) && !count($this->errors) && $this->_redirect) {
				PrestaShopLogger::addLog(sprintf($this->l('%s addition', 'AdminTab', false, false), $this->className), 1, null, $this->className, (int)$this->object->id, true, (int)$this->context->employee->id);
				$parent_id = (int)Tools::getValue('id_parent', 1);
				$this->afterAdd($this->object);
				$this->updateAssoShop($this->object->id);
				// Save and stay on same form
				if (empty($this->redirect_after) && $this->redirect_after !== false && Tools::isSubmit('submitAdd'.$this->table.'AndStay')) {
					$this->redirect_after = self::$currentIndex.'&'.$this->identifier.'='.$this->object->id.'&conf=3&update'.$this->table.'&token='.$this->token;
				}
				// Save and back to parent
				if (empty($this->redirect_after) && $this->redirect_after !== false && Tools::isSubmit('submitAdd'.$this->table.'AndBackToParent')) {
					$this->redirect_after = self::$currentIndex.'&'.$this->identifier.'='.$parent_id.'&conf=3&token='.$this->token;
				}
				// Default behavior (save and back)
				if (empty($this->redirect_after) && $this->redirect_after !== false) {
					$this->redirect_after = self::$currentIndex.($parent_id ? '&'.$this->identifier.'='.$this->object->id : '').'&conf=3&token='.$this->token;
				}
			}
		}

		$this->errors = array_unique($this->errors);
		if (!empty($this->errors)) {
			// if we have errors, we stay on the form instead of going back to the list
			$this->display = 'edit';
			return false;
		}

		return $this->object;
	}

	public function processUpdate()
	{
		/* Checking fields validity */
		$this->validateRules();
		if (empty($this->errors)) {

				/** @var ObjectModel $object */
				if (Validate::isLoadedObject($object = $this->loadObject())) {
					/* Specific to objects which must not be deleted */
					if ($this->deleted && $this->beforeDelete($object)) {
						// Create new one with old objet values
						/** @var ObjectModel $object_new */
						$object_new = $object->duplicateObject();
						if (Validate::isLoadedObject($object_new)) {
							// Update old object to deleted
							$object->deleted = 1;
							$object->update();

							// Update new object with post values
							$this->copyFromPost($object_new, $this->list_id);
							$result = $object_new->update();
							if (Validate::isLoadedObject($object_new)) {
								$this->afterDelete($object_new, $object->id);
							}
						}
					} else {
						$this->copyFromPost($object, $this->list_id);
						$result = $object->update();
						$this->afterUpdate($object);
					}

					if ($object->id) {
						$this->updateAssoShop($object->id);
					}

					if (!$result) {
						$this->errors[] = Tools::displayError('An error occurred while updating an object.').
							' <b>'.$this->table.'</b> ('.Db::getInstance()->getMsgError().')';
					} elseif ($this->postImage($object->id) && !count($this->errors) && $this->_redirect) {
						$parent_id = (int)Tools::getValue('id_parent', 1);
						// Specific back redirect
						if ($back = Tools::getValue('back')) {
							$this->redirect_after = urldecode($back).'&conf=4';
						}
						// Specific scene feature
						// @todo change stay_here submit name (not clear for redirect to scene ... )
						if (Tools::getValue('stay_here') == 'on' || Tools::getValue('stay_here') == 'true' || Tools::getValue('stay_here') == '1') {
							$this->redirect_after = self::$currentIndex.'&'.$this->identifier.'='.$object->id.'&conf=4&updatescene&token='.$this->token;
						}
						// Save and stay on same form
						// @todo on the to following if, we may prefer to avoid override redirect_after previous value
						if (Tools::isSubmit('submitAdd'.$this->table.'AndStay')) {
							$this->redirect_after = self::$currentIndex.'&'.$this->identifier.'='.$object->id.'&conf=4&update'.$this->table.'&token='.$this->token;
						}
						// Save and back to parent
						if (Tools::isSubmit('submitAdd'.$this->table.'AndBackToParent')) {
							$this->redirect_after = self::$currentIndex.'&'.$this->identifier.'='.$parent_id.'&conf=4&token='.$this->token;
						}

						// Default behavior (save and back)
						if (empty($this->redirect_after) && $this->redirect_after !== false) {
							$this->redirect_after = self::$currentIndex.($parent_id ? '&'.$this->identifier.'='.$object->id : '').'&conf=4&token='.$this->token;
						}
					}
					PrestaShopLogger::addLog(sprintf($this->l('%s modification', 'AdminTab', false, false), $this->className), 1, null, $this->className, (int)$object->id, true, (int)$this->context->employee->id);
				} else {
					$this->errors[] = Tools::displayError('An error occurred while updating an object.').
						' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
				}
		}
		$this->errors = array_unique($this->errors);
		if (!empty($this->errors)) {
			// if we have errors, we stay on the form instead of going back to the list
			$this->display = 'edit';
			return false;
		}

		if (isset($object)) {
			return $object;
		}
		return;
	}

	public function getList(
		$id_lang,
		$order_by = null,
		$order_way = null,
		$start = 0,
		$limit = null,
		$id_lang_shop = false
	)
	{
		if (version_compare(_PS_VERSION_, '1.7', '>=') === true)
			return parent::getList(
				$id_lang,
				$order_by,
				$order_way,
				$start,
				$limit,
				$id_lang_shop
			);

		$this->dispatchFieldsListingModifierEvent();

		$this->ensureListIdDefinition();

		/* Manage default params values */
		$use_limit = true;
		if (!$limit) {
			$use_limit = false;
		} elseif (empty($limit)) {
			if (isset($this->context->cookie->{$this->list_id.'_pagination'}) && $this->context->cookie->{$this->list_id.'_pagination'}) {
				$limit = $this->context->cookie->{$this->list_id.'_pagination'};
			} else {
				$limit = $this->_default_pagination;
			}
		}

		if (!Validate::isTableOrIdentifier($this->table)) {
			throw new PrestaShopException(sprintf('Table name %s is invalid:', $this->table));
		}
		$prefix = str_replace(array('admin', 'controller'), '', Tools::strtolower(get_class($this)));
		if (empty($order_by)) {
			if ($this->context->cookie->{$prefix.$this->list_id.'Orderby'}) {
				$order_by = $this->context->cookie->{$prefix.$this->list_id.'Orderby'};
			} elseif ($this->_orderBy) {
				$order_by = $this->_orderBy;
			} else {
				$order_by = $this->_defaultOrderBy;
			}
		}

		if (empty($order_way)) {
			if ($this->context->cookie->{$prefix.$this->list_id.'Orderway'}) {
				$order_way = $this->context->cookie->{$prefix.$this->list_id.'Orderway'};
			} elseif ($this->_orderWay) {
				$order_way = $this->_orderWay;
			} else {
				$order_way = $this->_defaultOrderWay;
			}
		}

		$limit = (int)Tools::getValue($this->list_id.'_pagination', $limit);
		if (in_array($limit, $this->_pagination) && $limit != $this->_default_pagination) {
			$this->context->cookie->{$this->list_id.'_pagination'} = $limit;
		} else {
			unset($this->context->cookie->{$this->list_id.'_pagination'});
		}

		/* Check params validity */
		if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)
			|| !is_numeric($start) || !is_numeric($limit)
			|| !Validate::isUnsignedId($id_lang)) {
			throw new PrestaShopException('get list params is not valid');
		}

		if (!isset($this->fields_list[$order_by]['order_key']) && isset($this->fields_list[$order_by]['filter_key'])) {
			$this->fields_list[$order_by]['order_key'] = $this->fields_list[$order_by]['filter_key'];
		}

		if (isset($this->fields_list[$order_by]) && isset($this->fields_list[$order_by]['order_key'])) {
			$order_by = $this->fields_list[$order_by]['order_key'];
		}

		/* Determine offset from current page */
		$start = 0;
		if ((int)Tools::getValue('submitFilter'.$this->list_id)) {
			$start = ((int)Tools::getValue('submitFilter'.$this->list_id) - 1) * $limit;
		} elseif (empty($start) && isset($this->context->cookie->{$this->list_id.'_start'}) && Tools::isSubmit('export'.$this->table)) {
			$start = $this->context->cookie->{$this->list_id.'_start'};
		}

		// Either save or reset the offset in the cookie
		if ($start) {
			$this->context->cookie->{$this->list_id.'_start'} = $start;
		} elseif (isset($this->context->cookie->{$this->list_id.'_start'})) {
			unset($this->context->cookie->{$this->list_id.'_start'});
		}

		/* Cache */
		$this->_lang = (int)$id_lang;
		$this->_orderBy = $order_by;

		if (preg_match('/[.!]/', $order_by)) {
			$order_by_split = preg_split('/[.!]/', $order_by);
			$order_by = bqSQL($order_by_split[0]).'.`'.bqSQL($order_by_split[1]).'`';
		} elseif ($order_by) {
			$order_by = '`'.bqSQL($order_by).'`';
		}

		$this->_orderWay = Tools::strtoupper($order_way);

		/* SQL table : orders, but class name is Order */
		$sql_table = $this->table == 'order' ? 'orders' : $this->table;

		// Add SQL shop restriction
		$select_shop = $join_shop = $where_shop = '';
		if ($this->shopLinkType) {
			$select_shop = ', shop.name as shop_name ';
			$join_shop = ' LEFT JOIN '._DB_PREFIX_.$this->shopLinkType.' shop
							ON a.id_'.$this->shopLinkType.' = shop.id_'.$this->shopLinkType;
			$where_shop = Shop::addSqlRestriction($this->shopShareDatas, 'a', $this->shopLinkType);
		}

		if ($this->multishop_context && Shop::isTableAssociated($this->table) && !empty($this->className)) {
			if (Shop::getContext() != Shop::CONTEXT_ALL || !$this->context->employee->isSuperAdmin()) {
				$test_join = !preg_match('#`?'.preg_quote(_DB_PREFIX_.$this->table.'_shop').'`? *sa#', $this->_join);
				if (Shop::isFeatureActive() && $test_join && Shop::isTableAssociated($this->table)) {
					$this->_where .= ' AND EXISTS (
						SELECT 1
						FROM `'._DB_PREFIX_.$this->table.'_shop` sa
						WHERE a.'.$this->identifier.' = sa.'.$this->identifier.' AND sa.id_shop IN ('.implode(', ', Shop::getContextListShopID()).')
					)';
				}
			}
		}

		/* Query in order to get results with all fields */
		$lang_join = '';
		if ($this->lang) {
			$lang_join = 'LEFT JOIN `'._DB_PREFIX_.$this->table.'_lang` b ON (b.`'.$this->identifier.'` = a.`'.$this->identifier.'` AND b.`id_lang` = '.(int)$id_lang;
			if ($id_lang_shop) {
				if (!Shop::isFeatureActive()) {
					$lang_join .= ' AND b.`id_shop` = '.(int)Configuration::get('PS_SHOP_DEFAULT');
				} elseif (Shop::getContext() == Shop::CONTEXT_SHOP) {
					$lang_join .= ' AND b.`id_shop` = '.(int)$id_lang_shop;
				} else {
					$lang_join .= ' AND b.`id_shop` = a.id_shop_default';
				}
			}
			$lang_join .= ')';
		}

		$having_clause = '';
		if (isset($this->_filterHaving) || isset($this->_having)) {
			$having_clause = ' HAVING ';
			if (isset($this->_filterHaving)) {
				$having_clause .= ltrim($this->_filterHaving, ' AND ');
			}
			if (isset($this->_having)) {
				$having_clause .= $this->_having.' ';
			}
		}

		do {
			$this->_listsql = '';

			if ($this->explicitSelect) {
				foreach ($this->fields_list as $key => $array_value) {
					// Add it only if it is not already in $this->_select
					if (isset($this->_select) && preg_match('/[\s]`?'.preg_quote($key, '/').'`?\s*,/', $this->_select)) {
						continue;
					}

					if (isset($array_value['filter_key'])) {
						$this->_listsql .= str_replace('!', '.`', $array_value['filter_key']).'` AS `'.$key.'`, ';
					} elseif ($key == 'id_'.$this->list_id) {
						$this->_listsql .= 'a.`'.bqSQL($key).'`, ';
					} elseif ($key != 'image' && !preg_match('/'.preg_quote($key, '/').'/i', $this->_select)) {
						$this->_listsql .= '`'.bqSQL($key).'`, ';
					}
				}
				$this->_listsql = rtrim(trim($this->_listsql), ',');
			} else {
				$this->_listsql .= ($this->lang ? 'b.*,' : '').' a.*';
			}

			$this->_listsql .= '
			'.(isset($this->_select) ? ', '.rtrim($this->_select, ', ') : '').$select_shop;

			$sql_from = '
			FROM `'._DB_PREFIX_.$sql_table.'` a ';
			$sql_join = '
			'.$lang_join.'
			'.(isset($this->_join) ? $this->_join.' ' : '').'
			'.$join_shop;
			$sql_where = ' '.(isset($this->_where) ? $this->_where.' ' : '').($this->deleted ? 'AND a.`deleted` = 0 ' : '').
			(isset($this->_filter) ? $this->_filter : '').$where_shop.'
			'.(isset($this->_group) ? $this->_group.' ' : '').'
			'.$having_clause;
			$sql_order_by = ' ORDER BY '.((str_replace('`', '', $order_by) == $this->identifier) ? 'a.' : '').$order_by.' '.pSQL($order_way).
			($this->_tmpTableFilter ? ') tmpTable WHERE 1'.$this->_tmpTableFilter : '');
			$sql_limit = ' '.(($use_limit === true) ? ' LIMIT '.(int)$start.', '.(int)$limit : '');

			if ($this->_use_found_rows || isset($this->_filterHaving) || isset($this->_having)) {
				$this->_listsql = 'SELECT SQL_CALC_FOUND_ROWS
								'.($this->_tmpTableFilter ? ' * FROM (SELECT ' : '').$this->_listsql.$sql_from.$sql_join.' WHERE 1 '.$sql_where.
								$sql_order_by.$sql_limit;
				$list_count = 'SELECT FOUND_ROWS() AS `'._DB_PREFIX_.$this->table.'`';
			} else {
				$this->_listsql = 'SELECT
								'.($this->_tmpTableFilter ? ' * FROM (SELECT ' : '').$this->_listsql.$sql_from.$sql_join.' WHERE 1 '.$sql_where.
								$sql_order_by.$sql_limit;
				$list_count = 'SELECT COUNT(*) AS `'._DB_PREFIX_.$this->table.'` '.$sql_from.$sql_join.' WHERE 1 '.$sql_where;
			}

			$this->_list = Db::getInstance()->executeS($this->_listsql, true, false);

			if ($this->_list === false) {
				$this->_list_error = Db::getInstance()->getMsgError();
				break;
			}

			$this->_listTotal = Db::getInstance()->getValue($list_count, false);

			if ($use_limit === true) {
				$start = (int)$start - (int)$limit;
				if ($start < 0) {
					break;
				}
			} else {
				break;
			}
		} while (!is_array($this->_list));

		$this->_listsql = '';
		Hook::exec('action'.$this->controller_name.'ListingResultsModifier', array(
			'list' => &$this->_list,
			'list_total' => &$this->_listTotal,
		));
	}

    protected function dispatchFieldsListingModifierEvent()
    {
        Hook::exec('action' . $this->controller_name . 'ListingFieldsModifier', array(
            'select' => &$this->_select,
            'join' => &$this->_join,
            'where' => &$this->_where,
            'group_by' => &$this->_group,
            'order_by' => &$this->_orderBy,
            'order_way' => &$this->_orderWay,
            'fields' => &$this->fields_list,
        ));
    }

    protected function ensureListIdDefinition()
    {
        if (!isset($this->list_id)) {
            $this->list_id = $this->table;
        }
    }
}
