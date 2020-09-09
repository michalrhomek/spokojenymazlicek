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

class Account_Pms_GoPay_ExtraController extends ModuleAdminController
{
	public function __construct()
	{
		parent::__construct();
    	$this->bootstrap = true;
		$this->context->controller->addCSS($this->module->module_dir.'views/css/admin/css.css', 'all');
	}

	public function display()
	{
		parent::display();
	}

	public function initContent()
	{
		$shop_context = Shop::getContext();
		if ((Shop::isFeatureActive() && $shop_context == Shop::CONTEXT_ALL)
			|| (Shop::isFeatureActive() && $shop_context == Shop::CONTEXT_GROUP)
		)
			return $this->errors[] = $this->module->l('You must select the store for which you want to set up parameters.');

		if (!$this->module->functions->isRegistered())
			return $this->errors[] = '<b>'.$this->module->l('Module is not registered').'</b>';

		$this->context->smarty->assign(array(
			'content' => $this->content . $this->renderListAccount().'<br><br>'. $this->renderListBills()
		));
	}

	public function renderListBills()
	{
		$ids_provozovny = array(11, 21, 31, 41, 51, 61, 71, 81, 91, 101, 111);

		$prefix = 'Bills';

		if (Tools::isSubmit('submitReset'.$prefix))
			unset($_POST);

		$date = new DateTime();
		$post_date = Tools::getValue($prefix.'Filter_dat_trzby');
		$date_to = ($to = $post_date[1]) ? $to : $date->format('Y-m-d');
		$date->modify('-'.($date->format('d')-1).' day');
		$date_from = ($from = $post_date[0]) ? $from : $date->format('Y-m-d');

		$datetime2 = new DateTime($date_to);
		$datetime1 = new DateTime($date_from);
		$interval = $datetime1->diff($datetime2);
		if ($interval->format('%a') > 30)
			$this->errors[] = '<b> '.$this->l('EET bills receipts').'</b>:: '.$this->l('The date range must be within 30 days, now it is:').'<b> '.$interval->format('%a').'</b> ';

		$id_provozovny = ($id_provozovny = Tools::getValue($prefix.'Filter_provozovna')) ? $id_provozovny : 11;

		$_POST[$prefix.'Filter_dat_trzby'][0] = $date_from;
		$_POST[$prefix.'Filter_dat_trzby'][1] = $date_to;
		$_POST[$prefix.'Filter_provozovna'] = $id_provozovny;

		$fieldList = array(
				'payment_id' => array(
							'title' => $this->l('ID platby'),
							'type' => 'text',
							'search' => false,
							'orderby' => false
				),
				'dat_trzby' => array(
							'title' => $this->l('Datum tržby'),
							'type' => 'datetime',
							'width' => 100,
							'search' => true,
							'orderby' => false
				),
				'state' => array(
							'title' => $this->l('Stav'),
							'type' => 'text',
							'search' => false,
							'orderby' => false
				),
				'celk_trzba' => array(
							'title' => $this->l('Částka'),
							'type' => 'price',
							'search' => false,
							'orderby' => false
				),
				'eet_mode' => array(
							'title' => $this->l('EET mode'),
							'type' => 'text',
							'search' => false,
							'orderby' => false
				),
				'uuid_zprava' => array(
							'title' => $this->l('UUID zpráva'),
							'type' => 'text',
							'search' => false,
							'orderby' => false
				),
				'dic_popl' => array(
							'title' => $this->l('Počáteční stav'),
							'type' => 'text',
							'search' => false,
							'orderby' => false
				),
				'fik' => array(
							'title' => $this->l('FIK'),
							'type' => 'datetime',
							'search' => false,
							'orderby' => false
				),
				'bkp' => array(
							'title' => $this->l('BKP'),
							'type' => 'datetime',
							'search' => false,
							'orderby' => false
				),
				'pkp' => array(
							'title' => $this->l('PKP'),
							'type' => 'datetime',
							'class' => 'break_word',
							'search' => false,
							'orderby' => false
				),
				'id_pokl' => array(
							'title' => $this->l('ID pokladny'),
							'type' => 'text',
							'search' => false,
							'orderby' => false
				),
				'id_provoz' => array(
							'title' => $this->l('ID provozovny'),
							'type' => 'select',
							'list' => array_combine($ids_provozovny, $ids_provozovny),
							'filter_key' => 'provozovna',
							'filter_type' => 'int',
							'orderby' => false
				)
		);

		$inputs = array();
		$datas = Pms_GoPay_Extra_RestAPI::getBillsByDate($date_from, $date_to, $id_provozovny);

		if (isset($datas->errors))
		{
			$this->errors[] = '<b>'.$this->l('Error:').'</b> '.$datas->errors[0]->error_code.' - '.$datas->errors[0]->message.' :: '.$datas->errors[0]->field;
		} else
		{
			foreach ($datas as $key=>$data)
			{
				$inputs[$key]['celk_trzba'] = $data->celk_trzba/100;
				$inputs[$key]['payment_id'] = $data->payment_id;
				$inputs[$key]['state'] = $data->state;
				$inputs[$key]['date_last_attempt'] = self::convertDate($data->date_last_attempt);
				$inputs[$key]['date_next_attempt'] = self::convertDate($data->date_next_attempt);
				$inputs[$key]['eet_mode'] = $data->eet_mode;
				$inputs[$key]['uuid_zprava'] = $data->uuid_zprava;
				$inputs[$key]['date_odesl'] = $data->date_odesl;
				$inputs[$key]['dic_popl'] = $data->dic_popl;
				$inputs[$key]['id_provoz'] = $data->id_provoz;
				$inputs[$key]['id_pokl'] = $data->id_pokl;
				$inputs[$key]['dat_trzby'] = self::convertDate($data->dat_trzby);
				$inputs[$key]['porad_cis'] = $data->porad_cis;
				$inputs[$key]['fik'] = $data->fik;
				$inputs[$key]['bkp'] = $data->bkp;
				$inputs[$key]['pkp'] = $data->pkp;
			}
			
		}

		$this->toolbar_btn['export'] = array(
			'js' => "sendBulkAction($(this).closest('form').get(0), 'submitBulkExport');",
			'desc' => $this->l('Export'),
			'icon' => 'process-icon-export'
		);

		$helper_list = new HelperList();
		$helper_list->module = $this->module;
		$helper_list->title = $this->l('EET bills receipts');
		$helper_list->shopLinkType = '';
		$helper_list->no_link = true;
		$helper_list->show_toolbar = false;
		$helper_list->simple_header = false;
		$helper_list->actions = array();
		$helper_list->toolbar_btn = $this->toolbar_btn;
		$helper_list->identifier = 'payment_id';
		$helper_list->table = $prefix;
		$helper_list->token = Tools::getAdminTokenLite('Account_Pms_GoPay_Extra');
		$helper_list->currentIndex = AdminController::$currentIndex.'&configure='.$this->module->name;
	    
		$helper_list->listTotal = count($inputs);
		$page = ($page = Tools::getValue('submitFilter'.$helper_list->table)) ? $page : 1;
		$pagination = ($pagination = Tools::getValue($helper_list->table.'_pagination')) ? $pagination : 50;
		$value_list = $this->paginateValueList($inputs, $page, $pagination);
            
		return $helper_list->generateList($value_list, $fieldList);
	}

	private static function convertDate($datum)
	{
		$date = new DateTime($datum);
		return $date->format('Y-m-d H:i:s');
	}

	public function renderListAccount()
	{
		$prefix = 'Statement';

		if (Tools::isSubmit('submitReset'.$prefix))
			unset($_POST);

		$date = new DateTime();
		$post_date = Tools::getValue($prefix.'Filter_datum');
		$date_to = ($to = $post_date[1]) ? $to : $date->format('Y-m-d');
		$date->modify('-'.($date->format('d')-1).' day');
		$date_from = ($from = $post_date[0]) ? $from : $date->format('Y-m-d');

		$sel_currency = 'CZK';
		if ($id_currency = Tools::getValue($prefix.'Filter_currency'))
		{
			$currency = new Currency($id_currency);
			$sel_currency = $currency->iso_code;
		}

		$_POST[$prefix.'Filter_datum'][0] = $date_from;
		$_POST[$prefix.'Filter_datum'][1] = $date_to;
		$_POST[$prefix.'Filter_currency'] = Currency::getIdByIsoCode($sel_currency);

		$context = Context::getContext();
		$currencies = Currency::getCurrencies();
		foreach ($currencies as $currency)
			$currency_array[$currency['id_currency']] = $currency['iso_code'];

		$fieldList = array(
				'id_pohybu' => array(
							'title' => $this->l('ID pohybu'),
							'type' => 'text',
							'search' => false,
							'orderby' => false
				),
				'datum' => array(
							'title' => $this->l('Datum'),
							'type' => 'datetime',
							'width' => 100,
							'search' => true,
							'orderby' => false
				),
				'typ' => array(
							'title' => $this->l('Typ'),
							'type' => 'text',
							'search' => false,
							'orderby' => false
				),
				'protistrana' => array(
							'title' => $this->l('Protistrana'),
							'type' => 'text',
							'search' => false,
							'orderby' => false
				),
				'id_objednavky_vs' => array(
							'title' => $this->l('ID objednávky / VS'),
							'type' => 'text',
							'search' => false,
							'orderby' => false
				),
				'castka' => array(
							'title' => $this->l('Částka'),
							'type' => 'text',
							'search' => false,
							'orderby' => false
				),
				'pocatecni_stav' => array(
							'title' => $this->l('Počáteční stav'),
							'type' => 'text',
							'search' => false,
							'orderby' => false
				),
				'koncovy_stav' => array(
							'title' => $this->l('Koncový stav'),
							'type' => 'price',
							'search' => false,
							'orderby' => false
				),
				'mena' => array(
							'title' => $this->l('Měna'),
							'type' => 'select',
							'list' => $currency_array,
							'filter_key' => 'currency',
							'filter_type' => 'int',
							'orderby' => false
				),
				'id_referencniho_pohybu' => array(
							'title' => $this->l('ID ref. pohybu'),
							'type' => 'datetime',
							'search' => false,
							'orderby' => false
				)
		);

		$datas = Pms_GoPay_Extra_RestAPI::getAccountByDate($date_from, $date_to, $sel_currency);
		if (isset($datas->errors))
		{
			$this->errors[] = '<b>'.$this->l('Error:').'</b> '.$datas->errors[0]->error_code.' - '.$datas->errors[0]->message.' :: '.$datas->errors[0]->field;
			$datas = array();
		}

		$this->toolbar_btn['export'] = array(
			'js' => "sendBulkAction($(this).closest('form').get(0), 'submitBulkExport');",
			'desc' => $this->l('Export'),
			'icon' => 'process-icon-export'
		);

		$helper_list = new HelperList();
		$helper_list->module = $this->module;
		$helper_list->title = $this->l('Account statement');
		$helper_list->shopLinkType = '';
		$helper_list->no_link = true;
		$helper_list->show_toolbar = false;
		$helper_list->simple_header = false;
		$helper_list->actions = array();
		$helper_list->toolbar_btn = $this->toolbar_btn;
		$helper_list->identifier = 'id_pohybu';
		$helper_list->table = $prefix;
		$helper_list->token = Tools::getAdminTokenLite('Account_Pms_GoPay_Extra');
		$helper_list->currentIndex = AdminController::$currentIndex.'&configure='.$this->module->name;
	    
		$helper_list->listTotal = count($datas);
		$page = ($page = Tools::getValue('submitFilter'.$helper_list->table)) ? $page : 1;
		$pagination = ($pagination = Tools::getValue($helper_list->table.'_pagination')) ? $pagination : 50;
		$value_list = $this->paginateValueList($datas, $page, $pagination);
            
		return $helper_list->generateList($value_list, $fieldList);
	}

	public function paginateValueList($value_list, $page = 1, $pagination = 50)
	{
		if(count($value_list) > $pagination)
			$value_list = array_slice($value_list, $pagination * ($page - 1), $pagination);

		return $value_list;
	}
}