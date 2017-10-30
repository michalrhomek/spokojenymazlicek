<?php
/**
*  Module TOTAL IMPORT PRO for PrestaShop 1.5+ From HostJars hostjars.com
*
* @author    HostJars
* @copyright HostJars
* @license   HostJars
*/

if (!defined('_PS_VERSION_'))
	exit;

define('HJ_DEV', 0);
define('CRON_FETCH_NUM', 3);

class TotalImportPRO extends Module
{
	public $total_items_added = 0;
	public $total_combinations_added = 0;
	public $total_items_updated = 0;
	public $total_combinations_updated = 0;
	public $total_items_missed = 0;
	public $total_items_ready = 0;
	public $total_items_invalid = 0;

	private $_html;
	private $errors;
	private $file_encoding = 'UTF-8';

	private $combination_fieldlist = array(
		array(
			'name' => 'combination_ean13',
			'validation' => 'isEan13'
		),
		array(
			'name' => 'combination_upc',
			'validation' => 'isUpc'
		),
		'combination_quantity',
		'combination_weight',
		'combination_minimal_quantity',
		array(
			'name' => 'combination_wholesale_price',
			'validation' => 'getValidCombinationPrice'
		),
		array(
			'name' => 'combination_price',
			'validation' => 'getValidCombinationPrice',
		),
		array(
			'name' => 'combination_unit_price_impact',
			'validation' => 'getValidCombinationPrice',
		),
		array(
			'name' => 'combination_reference',
			'validation' => 'getValidReference',
		),
		array(
			'name' => 'combination_default_on',
			'validation' => 'getBool',
		),
	);

	public function __construct()
	{
		$this->name = 'totalimportpro';
		$this->tab = 'quick_bulk_update';
		$this->version = '2.0.3';
		$this->author = 'HostJars';
		$this->module_key = 'e9e945ee2e9564220e671f1575fed8b5';
		$this->need_instance = 0;
		$this->bootstrap = true;

		parent::__construct();

		$this->displayName = $this->l('Total Import PRO');
		$this->description = $this->l('Import product data to the product catalog.');
		$this->confirmUninstall = $this->l('Are you sure you want to uninstall? You will lose your Import Settings Profiles');
		if (!Configuration::get('TOTAL_IMPORT_PRO'))
			$this->warning = $this->l('No name provided');

	}

	public function install()
	{
		$tip_quickaccess = new QuickAccess();

		// Make sure Total Import PRO isn't already added to quick access menu
		$lang = Configuration::get('PS_LANG_DEFAULT');
		$existing_qa = $tip_quickaccess->getQuickAccesses($lang);
		$found = false;
		foreach ($existing_qa as $quick_access)
		{
			if ($quick_access['name'] == 'Total Import PRO')
			{
				$found = true;
				break;
			}
		}
		if (!$found)
		{
			$tip_quickaccess->link = 'index.php?controller=AdminModules&configure=totalimportpro';
			$tip_quickaccess->new_window = 1;
			$tip_quickaccess->name[$lang] = $this->l('Total Import PRO');
			$tip_quickaccess->add();
		}
		if (parent::install() == true && $this->createTables()
			&& Configuration::updateValue('TOTAL_IMPORT_PRO', 'Total Import PRO'))
			return true;
		return false;
	}

	public function uninstall()
	{
		if (parent::uninstall() && Configuration::deleteByName('TOTAL_IMPORT_PRO')
			&& $this->deleteTables()) //add step config values
			return true;
		return false;
	}

	/**
	 * Display content of admin page
	 *
	 * @return string
	 */
	public function getContent()
	{
		if (version_compare(_PS_VERSION_, '1.6', '<'))
		{
			// Check if this is an ajax call, required for PS1.5.x
			if (Tools::getIsset('ajax') && Tools::getValue('ajax') == 'true' && Tools::getValue('action'))
			{
				call_user_func(array($this, 'ajaxProcess'.Tools::getValue('action')));
				die();
			}
		}

		if (Tools::getIsset('importSettings') && Tools::getValue('importSettings') == '1')
			$this->importSettings();

		if (Tools::getIsset('exportSettings') && Tools::getValue('exportSettings') == '1')
			$this->exportSettings();

		$this->_setBaseUrl();

		$this->_html = '';

		if (version_compare(_PS_VERSION_, '1.6', '<'))
		{
			$this->_html .= '
			<style>
				.form-group {
					margin: 15px 0;
				}
			</style>';
		}

		$this->_html .= '
		<script type="text/javascript" src="'._MODULE_DIR_.'/totalimportpro/views/js/selectize/selectize.js"></script>
		<link rel="stylesheet" href="'._MODULE_DIR_.'/totalimportpro/views/css/selectize.css'.'">
		<link rel="stylesheet" href="'._MODULE_DIR_.'/totalimportpro/views/css/selectize_bootstrap3.css'.'">
		<script type="text/javascript" src="'._MODULE_DIR_.'/totalimportpro/views/js/totalimportpro.js"></script>
		<style>
			.btn-reset, .btn-reset i {
				color: white !important;
				box-shadow: none !important;
				-webkit-box-shadow: none !important;
			}
			.hori.vert .selectize-control
			{
				display: inline-block;
				width: 250px;
			}
		</style>

<!-- Start of Intercom Script -->
<script>

  window.intercomSettings = {
    app_id: "rtb0b21o",
    store_domain: "'.$_SERVER['HTTP_HOST'].'",
    name: $(".employee_name").text(), // Full name
    email: "'.$this->context->employee->email.'", // Email address
    import_profiles: "'.count($this->getSavedSettingNames()).'",
  };
(function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic("reattach_activator");ic("update",intercomSettings);}else{var d=document;var i=function(){i.c(arguments)};i.q=[];i.c=function(args){i.q.push(args)};w.Intercom=i;function l(){var s=d.createElement("script");s.type="text/javascript";s.async=true;s.src="https://widget.intercom.io/widget/rtb0b21o";var x=d.getElementsByTagName("script")[0];x.parentNode.insertBefore(s,x);}if(w.attachEvent){w.attachEvent("onload",l);}else{w.addEventListener("load",l,false);}}})()
</script>
<!-- End of Intercom Script -->

<!-- Start of HostJars Support Zendesk Widget script -->
<script>/*<![CDATA[*/window.zEmbed||function(e,t){var n,o,d,i,s,a=[],r=document.createElement("iframe");window.zEmbed=function(){a.push(arguments)},window.zE=window.zE||window.zEmbed,r.src="javascript:false",r.title="",r.role="presentation",(r.frameElement||r).style.cssText="display: none",d=document.getElementsByTagName("script"),d=d[d.length-1],d.parentNode.insertBefore(r,d),i=r.contentWindow,s=i.document;try{o=s}catch(c){n=document.domain,r.src=\'javascript:var d=document.open();d.domain="\'+n+\'";void(0);\',o=s}o.open()._l=function(){var o=this.createElement("script");n&&(this.domain=n),o.id="js-iframe-async",o.src=e,this.t=+new Date,this.zendeskHost=t,this.zEQueue=a,this.body.appendChild(o)},o.write(\'<body onload="document._l();">\'),o.close()}("//assets.zendesk.com/embeddable_framework/main.js","hostjars.zendesk.com");/*]]>*/</script>
<!-- End of HostJars Support Zendesk Widget script -->
		';

		$this->_postProcess();

		if (Tools::getValue('submitStep') == 1 && !empty($this->errors))
			$this->displayStep1();
		elseif ($step = Tools::getValue('step'))
		{
			if ($step == 1) //GET variable
				$this->displayStep1();
			elseif ($step == 2)
				$this->displayStep2();
			elseif ($step == 3)
				$this->displayStep3();
			elseif ($step == 4)
				$this->displayStep4();
			elseif ($step == 5)
				$this->displayStep5();
		}
		else
			$this->displayForm();

		return $this->_html;
	}

	public function cron()
	{
		if (defined('CLI_INITIATED'))
		{
			if (PROFILE_NAME != 'default')
				$this->loadSettings(PROFILE_NAME);

			$this->initFileLogger();
			$this->rotateLogs();
			$this->logger->logInfo('Cron Import initializing, Profile: '.PROFILE_NAME);

			$settings = unserialize(Configuration::get('IMPORT_STEP1'));

			$filename = $this->fetchFeed($settings, isset($settings['unzip_feed']));

			if ($filename)
				$this->importFile($filename, $settings);

			$this->logger->logInfo('Feed fetched.');

			$settings = unserialize(Configuration::get('IMPORT_STEP3'));

			if (isset($settings['adjust']) && is_array($settings['adjust']))
			{
				$this->logger->logInfo('Running feed adjustments.');
				$this->runAdjustments($settings['adjust']);
			}

			$settings = array_merge(unserialize(Configuration::get('IMPORT_STEP1')),
									unserialize(Configuration::get('IMPORT_STEP2')),
									unserialize(Configuration::get('IMPORT_STEP3')),
									unserialize(Configuration::get('IMPORT_STEP4')),
									unserialize(Configuration::get('IMPORT_STEP5')));

			//for partial cron imports
			if (defined('START') && defined('END'))
			{
				$settings['import_range'] = 'partial';
				$settings['import_range_start'] = START;
				$settings['import_range_end'] = END;
			}

			$this->import($settings);
			$this->ajaxProcessRegenCatTree();
			print_r(sprintf($this->l('Success: Added %s products, Updated %s products, Added %s combinations, Updated %s combinations'),
				$this->total_items_added, $this->total_items_updated, $this->total_combinations_added, $this->total_combinations_updated));
		}
	}

	/** Function import
	 *
	 * Initiates the import, looping over the database, checking for updates, and adding/editing the products.
	 *
	 * @author    HostJars
	 * @param $settings The settings required for this import, from Step 1-5 of admin.
	 * @param int $product_num The current product number to start the import
	 * @param int $limit The amount of products that can be imported per run
	 */
	public function import(&$settings, &$product_num = 0, &$limit = 0)
	{
		$this->validate = HJ_DEV ? true : false;

		$this->default_language = (int)Configuration::get('PS_LANG_DEFAULT');
		$this->languages = Language::getLanguages(false);
		$this->file_encoding = $settings['file_encoding'];
		if (isset($settings['id_shop_list']))
		{
			foreach ($settings['id_shop_list'] as $key => $shop_id)
			{
				$settings['id_shop_list'][$key] = (int)$shop_id;
			}
		}
		$this->shops = isset($settings['id_shop_list']) ? $settings['id_shop_list'] : array_unique(Shop::getContextListShopID());

		$this->associations = array(); //fields to add after all products are added to the db

		$this->update_products = array(); //filled with product ids of updated items
		$this->add_products = array(); //filled with product ids of added items

		$this->associate_shops = array(); //to associate specific fields to shops
		//remove shops in the context to associate
		foreach ($this->shops as $shop)
		{
			if ($this->context->shop->id != $shop)
				$this->associate_shops[] = $shop;
		}

		if (!$this->associate_shops)
			$this->associate_shops = $this->shops;

		//Empty store if this is a reset
		if ($settings['reset_store'])
			$this->emptyTables();
		else if ($settings['reset_combinations'])
			$this->resetCombinations();

		$delete_diff = !empty($settings['delete_diff']) ? $settings['delete_diff'] : 'ignore';
		if (!$settings['reset_store'] && $delete_diff != 'ignore' && isset($settings['first_run']))
			$this->createDeleteDiffTable($settings['update_field'], $settings);

		//Check for partial imports
		$partial = -1;
		if (isset($settings['import_range']) && $settings['import_range'] == 'partial')
		{
			// Product num will be set on ajax imports
			if ($product_num == 0)
			{
				if (isset($settings['import_range_start']))
				{
					if ($settings['import_range_start'] <= 0)
						$product_num = 0;
					else
						$product_num = $settings['import_range_start'] - 1;
				}
				if (isset($settings['import_range_end']))
				{
					if ($settings['import_range_start'] > $settings['import_range_end'])
						$partial = -1;
					else
						$partial = $settings['import_range_end'] - $product_num;
				}
			}
		}

		$field_groups = array('field_names');
		if (isset($settings['simple']) && $settings['simple'] == 1)
			$field_groups = array('simple_names');

		while (($raw_prod = $this->getNextProduct($product_num)))
		{
			$product_num++;
			$this->resetDefaultValues($settings);
			//limits for Ajax import
			if ($limit)
			{
				$sum_products = $this->total_items_added + $this->total_items_updated;
				if ($sum_products >= $limit)
					break;
			}

			//if we reached product import range, stop importing products
			if (($partial != -1) && (($product_num - $settings['import_range_start']) >= $partial))
				break;

			//format fields for import
			foreach ($field_groups as $field_group)
			{
				//price - remove leading $ or pound or euro symbol, remove any commas.
				foreach (array('price', 'wholesale_price', 'unit_price', 'ecotax', 'additional_shipping_cost') as $price)
				{
					if (isset($settings[$field_group][$price]) && isset($raw_prod[$settings[$field_group][$price]]))
					{
						$raw_prod[$settings[$field_group][$price]] = preg_replace('/^[^\d]+/', '', $raw_prod[$settings[$field_group][$price]]);
						$raw_prod[$settings[$field_group][$price]] = str_replace(',', '.', $raw_prod[$settings[$field_group][$price]]);
					}
				}

				//Allow for true/false, on/off, enable/disable and yes/no in the below fields
				$binary_fields = array('active');
				foreach ($binary_fields as $binary_field)
				{
					if (isset($settings[$field_group][$binary_field]) && isset($raw_prod[$settings[$field_group][$binary_field]]))
						$raw_prod[$settings[$field_group][$binary_field]] = preg_match('/(^no$|^n$|false|off|disabled|disable|^0$)/is', $raw_prod[$settings[$field_group][$binary_field]]) ? 0 : 1;
				}

				//allow for condition values
				if (isset($settings[$field_group]['condition']) && isset($raw_prod[$settings[$field_group]['condition']]))
				{
					$field_val = $raw_prod[$settings[$field_group]['condition']];
					$condition_field = preg_match('/^new|used|refurbished|^$/i', $field_val) ? Tools::strtolower($field_val) : 'new';
					$raw_prod[$settings[$field_group]['condition']] = $condition_field;
				}
				//allow for visibility values
				if (isset($settings[$field_group]['visibility']) && isset($raw_prod[$settings[$field_group]['visibility']]))
				{
					$field_val = $raw_prod[$settings[$field_group]['visibility']];
					$visibility_field = preg_match('/^everywhere|catalog|search|^$/i', $field_val) ? Tools::strtolower($field_val) : 'everywhere';
					$raw_prod[$settings[$field_group]['visibility']] = $visibility_field;
				}

			}

			$update_id = 0;
			$combination_id = 0;
			// Is this an update?
			if ($settings['update_field'] == 'name')
			{
				if ($settings['simple'])
				{
					//no lang for simple
					if (!empty($settings['simple_names'][$settings['update_field']]))
						$update_value = $raw_prod[$settings['simple_names'][$settings['update_field']]];
				}
				else
				{
					//Multi language update field
					foreach ($this->languages as $lang)
					{
						if (!empty($raw_prod[$settings['field_names'][$settings['update_field']][$lang['id_lang']]]))
						{
							$update_value = $raw_prod[$settings['field_names'][$settings['update_field']][$lang['id_lang']]];
							$update_id = $this->getProductId($settings['update_field'], $update_value);
							break;
						}
					}
				}
			}
			else
			{
				// Else the update field will be either UPC, Reference, or EAN13/JAN
				$field_prefix = $settings['simple'] ? 'simple' : 'field';
				if (!empty($raw_prod[$settings[$field_prefix.'_names'][$settings['update_field']]]))
				{
					$update_value = $raw_prod[$settings[$field_prefix.'_names'][$settings['update_field']]];
					$update_id = $this->getProductId($settings['update_field'], $update_value);
				}
			}

			//check for combinations
			if ($settings['combination_field'] !== 'none')
			{
				if (isset($raw_prod[$settings['field_names']['combination_'.$settings['combination_field']]]) && $raw_prod[$settings['field_names']['combination_'.$settings['combination_field']]] != '')
				{
					$combination_value = $raw_prod[$settings['field_names']['combination_'.$settings['combination_field']]];
					$combination_id = $this->getCombinationId($settings['combination_field'], $combination_value);

					//combination update? find product id
					if ($combination_id && !$update_id)
						$update_id = $this->getProductId('id_product_attribute', $combination_id);
				}
			}

			if ($update_id && $settings['existing_items'] != 'skip')
			{
				// Is this a simple update?
				if ($settings['simple'])
				{
					$simple_update = array();
					$simple_fields = array('quantity', 'wholesale_price', 'price', 'active');
					foreach ($simple_fields as $field)
					{
						if (isset($raw_prod[$settings['simple_names'][$field]]))
							$simple_update[$field] = $raw_prod[$settings['simple_names'][$field]];
					}
					//add specific_price field to simple update
					if (isset($raw_prod[$settings['simple_names']['specific_price'][0]]))
						$simple_update['specific_price'] = $this->setSpecificPrices($raw_prod, $settings, $update_id, $combination_id, $settings['simple']);

					if (!empty($simple_update))
						$this->simpleUpdate($update_id, $simple_update);
				}
				else
				{
					//regular update and, or combination update
					$this->importProduct($raw_prod, $settings, $update_id, $combination_id);
				}
			}

			if ($settings['new_items'] != 'skip' && !$update_id && !$settings['simple'])
			{
				//New Product
				$this->importProduct($raw_prod, $settings);
			}

			//delete from existing_prods hash, so this product doesn't get deleted post-import
			if (!$settings['reset_store'] && $delete_diff != 'ignore')
			{
				if (isset($settings['field_names'][$settings['update_field']]))
				{
					if ($settings['update_field'] == 'name')
					{
						foreach ($this->languages as $lang)
						{
							if (!empty($raw_prod[$settings['field_names'][$settings['update_field']][$lang['id_lang']]]))
							{
								$update_value = $raw_prod[$settings['field_names'][$settings['update_field']][$lang['id_lang']]];
								$prod_id = $this->getProductId('name', $update_value);
								$this->deleteExistingProdHash($prod_id);
								break;
							}
						}
					}
					elseif (isset($raw_prod[$settings['field_names'][$settings['update_field']]]))
						$this->deleteExistingProdHash($raw_prod[$settings['field_names'][$settings['update_field']]]);
				}
			}
			$this->total_items_added = count($this->add_products);
			$this->total_items_updated = count($this->update_products);
		}

		//associate products after they've been added from the feed
		if ($this->associations)
			$this->associateFields($this->associations, $settings);

		//update totals for added/updated products
		$this->total_items_added = count(array_unique($this->add_products));
		$this->total_items_updated = count(array_unique($this->update_products));
	}


	private function isFeedFieldSet($field_name, $feed_row, $settings)
	{
		$field_set = false;
		if (isset($settings['field_names'][$field_name]))
		{
			$column_name = $settings['field_names'][$field_name];
			if (isset($feed_row[$column_name]) && $feed_row[$column_name] != '')
				$field_set = true;
		}
		return $field_set;
	}

	/*
	* Function import
	*
	* Adds or updates one product at a time
	*
	*@author 	HostJars
	*@param (mixed) The settings required for this import, from Step 1-5 of admin.
	*@return (none)
	*/
	public function importProduct(&$raw_prod, &$settings, &$update_id = 0, &$combination_id = 0)
	{
		if ($update_id)
			$product = new Product((int)$update_id);
		else
		{
			$product = new Product();
			$product->id_shop_list = $this->shops;
			//global settings for new products only
			foreach ($this->global_data as $field => $default_value)
				$product->{$field} = $default_value;
		}

		//Necessary Import Fields, add to product obj if not in default product field array, otherwise add to raw product
		foreach ($this->languages as $lang)
		{
			if (!empty($raw_prod[$settings['field_names']['name'][$lang['id_lang']]]))
			{
				$raw_prod[$settings['field_names']['name'][$lang['id_lang']]] = Tools::substr($raw_prod[$settings['field_names']['name'][$lang['id_lang']]], 0, 127);
				if (!empty($raw_prod[$settings['field_names']['link_rewrite'][$lang['id_lang']]]))
				{
					if (Validate::isLinkRewrite($raw_prod[$settings['field_names']['link_rewrite'][$lang['id_lang']]]))
					{
						$raw_prod['link_rewrite'.$lang['id_lang']] = $raw_prod[$settings['field_names']['link_rewrite'][$lang['id_lang']]];
						$settings['field_names']['link_rewrite'][$lang['id_lang']] = 'link_rewrite'.$lang['id_lang'];
					}
					else
					{
						//make valid
						$raw_prod['link_rewrite'.$lang['id_lang']] = Tools::substr($raw_prod[$settings['field_names']['link_rewrite'][$lang['id_lang']]], 0, 127);
						$settings['field_names']['link_rewrite'][$lang['id_lang']] = 'link_rewrite'.$lang['id_lang'];
					}
				}
				else
				{
					//make valid friendly url from name
					$raw_prod['link_rewrite'.$lang['id_lang']] = Tools::substr($raw_prod[$settings['field_names']['name'][$lang['id_lang']]], 0, 127);
					$settings['field_names']['link_rewrite'][$lang['id_lang']] = 'link_rewrite'.$lang['id_lang'];
				}
			}
			//handled as string separated by comma delimiter
			$keywords = array();
			if (!empty($raw_prod[$settings['field_names']['meta_keywords'][$lang['id_lang']][0]]))
			{
				foreach ($settings['field_names']['meta_keywords'][$lang['id_lang']] as $key_field)
				{
					if (!empty($raw_prod[$key_field]))
						$keywords[] = Tools::strtolower(trim($raw_prod[$key_field]));
				}
				if ($keywords)
					$product->meta_keywords[$lang['id_lang']] = implode(',', $keywords); //add to prod since not default value
			}
			if (!empty($raw_prod[$settings['field_names']['meta_description'][$lang['id_lang']]]))
			{
				//limit 255 characters
				Tools::substr($raw_prod[$settings['field_names']['meta_description'][$lang['id_lang']]], 0, 254);
			}
		}
		//Unit Price
		if (isset($raw_prod[$settings['field_names']['unit_price']]))
		{
			$product->unit_price = $raw_prod[$settings['field_names']['unit_price']];
			if (isset($settings['unity']))
				$product->unity = $settings['unity'];
		}

		//Tax Rule needs to exist before import
		if (isset($raw_prod[$settings['field_names']['id_tax_rules_group']]))
		{
			$tax_rule_groups = TaxRulesGroup::getTaxRulesGroups();
			//add by id tax rules group
			foreach ($tax_rule_groups as $tax_group)
			{
				if ($raw_prod[$settings['field_names']['id_tax_rules_group']] == $tax_group['id_tax_rules_group'])
					$product->id_tax_rules_group = $tax_group['id_tax_rules_group'];
			}
		}

		//Manufacturer
		if (isset($raw_prod[$settings['field_names']['id_manufacturer']]))
		{
			//check to see if manufacturer exists, otherwise just assign to an existing manufacturer
			if ($manufacturer = Manufacturer::getIdByName((string)$raw_prod[$settings['field_names']['id_manufacturer']]))
			{
				$raw_prod[$settings['field_names']['id_manufacturer']] = (int)$manufacturer;
				//$settings['field_names']['id_manufacturer'] = 'id_manufacturer';
			}
			else
			{
				$manufacturer = new Manufacturer();
				$manufacturer->name = $raw_prod[$settings['field_names']['id_manufacturer']];
				$manufacturer->active = true;
				//add new manufacturer to correct shops
				$field_error = $manufacturer->validateFields($this->validate);
				$lang_field_error = $manufacturer->validateFieldsLang($this->validate);
				if ($field_error === true && $lang_field_error === true)
				{
					if ($manufacturer->add())
						$raw_prod[$settings['field_names']['id_manufacturer']] = (int)$manufacturer->id;
				}
			}
		}
		//Shop List
		if ($this->shops)
		{
			$raw_prod['id_shop_list'] = $this->shops;
			$settings['field_names']['id_shop_list'] = 'id_shop_list';
		}

		//Virtual Product
		$product->is_virtual = 0;
		if (!empty($raw_prod[$settings['field_names']['file']]))
			$product->is_virtual = 1;

		//Fill in remaining product data & overwrite product data with imported data from csv
		$prod_vars = get_object_vars($product);
		// loop over prod_data array adding product table data
		foreach ($this->prod_data as $field => $default_value)
		{
			if (array_key_exists($field, $prod_vars))
			{
				//don't add default value for update
				if ($this->isFeedFieldSet($field, $raw_prod, $settings))
					$product->$field = $raw_prod[$settings['field_names'][$field]];
				elseif (!$update_id && !$combination_id)
					$product->$field = $default_value;
			}
		}

		// loop over desc_data array adding description table data
		foreach ($this->desc_data as $field => $value)
		{
			if (array_key_exists($field, $prod_vars))
			{
				//multi language
				foreach ($value as $id_lang => $lang_value)
				{
					//don't add default value for update
					if (!empty($raw_prod[$settings['field_names'][$field][$id_lang]]))
						$product->{$field}[$id_lang] = html_entity_decode($raw_prod[$settings['field_names'][$field][$id_lang]]);
					elseif (!$update_id)
						$product->{$field}[$id_lang] = html_entity_decode($lang_value);
				}
			}
		}

		//overwrite any global setting fields that were mapped in step 4
		$global_mappings = array('active', 'condition', 'visibility', 'minimal_quantity');
		foreach ($global_mappings as $global)
		{
			if (array_key_exists($global, $prod_vars))
			{
				if (!empty($settings['field_names'][$global]) && (!empty($raw_prod[$settings['field_names'][$global]])
						|| $raw_prod[$settings['field_names'][$global]] == 0))
					$product->$global = $raw_prod[$settings['field_names'][$global]];
			}
		}

		//multi language globals
		$global_multi = array('available_now', 'available_later');
		foreach ($global_multi as $global)
		{
			if (array_key_exists($global, $prod_vars))
			{
				foreach ($this->languages as $lang)
				{
					if (isset($raw_prod[$settings['field_names'][$global][$lang['id_lang']]]))
						$product->{$global}[$lang['id_lang']] = $raw_prod[$settings['field_names'][$global][$lang['id_lang']]];
				}
			}
		}

		if (!empty($raw_prod[$settings['field_names']['options']]))
		{
			if (strstr($raw_prod[$settings['field_names']['options']], ':') !== false)
			{
				$options = explode(':', $raw_prod[$settings['field_names']['options']]);
				if (count($options) == 3)
				{
					if (isset($options[0]))
						$product->available_for_order = $options[0];
					if (isset($options[1]))
						$product->online_only = $options[1];
					if (isset($options[2]))
						$product->show_price = $options[2];
				}
			}
		}

		$category_mapped = false;
		foreach ($this->languages as $lang)
		{
			$first_cat_column_name = $settings['field_names']['category'][$lang['id_lang']][0][0];
			if (!empty($first_cat_column_name))
			{
				if (isset($raw_prod[$first_cat_column_name]) && $raw_prod[$first_cat_column_name] != '')
				{
					$category_mapped = true;
					break;
				}
			}
		}

		//advanced stock management
		if (!empty($raw_prod[$settings['field_names']['warehouse'][0]]))
			$product->advanced_stock_management = 1;

		//Validate Fields
		$valid = true;
		if ($settings['validate_product'] == 0)
		{
			$field_error = $product->validateFields($this->validate);
			$lang_field_error = $product->validateFieldsLang($this->validate);
			if ($field_error === true || $lang_field_error === true)
				$valid = false;
		}
		else //Auto validate
		{
			//loop through and auto validate
			foreach (Product::$definition['fields'] as $field => $data)
			{
				$lang = isset($data['lang']) ? $data['lang'] : false;
				if (isset($data['validate']))
				{
					if ($lang)
					{
						foreach ($this->languages as $language)
						{
							//make exception for required
							if (isset($data['required']) && empty($product->{$field}[$language['id_lang']]))
								$product->{$field}[$language['id_lang']] = $this->desc_data[$field][$language['id_lang']];
							elseif (isset($product->{$field}[$language['id_lang']]))
							{
								$new_value = $this->autoValidate($product->{$field}[$language['id_lang']], $data);
								if ($new_value != false)
									$product->{$field}[$language['id_lang']] = ($new_value == 'empty') ? '' : $new_value;
							}
						}
					}
					else
					{
						//make exception for required
						if (isset($data['required']) && empty($product->{$field}))
							$product->$field = $this->prod_data[$field];
						elseif (isset($product->{$field}))
						{
							$new_value = $this->autoValidate($product->{$field}, $data);
							if ($new_value != false)
								$product->$field = ($new_value == 'empty') ? '' : $new_value;
						}
					}

				}
			}
		}

		//Add or Update products
		if ($valid)
		{
			if ($update_id)
			{
				$action = $product->update();
				if ($action)
				{
					$this->update_products[] = $update_id;
					$log_text = 'Updated Product, Name: '.current($product->name).', ID: '.$update_id;
					if (isset($product->reference))
						$log_text .= ', Reference: '.$product->reference;
					$this->logger->logInfo($log_text);
				}
			}
			else
			{
				$action = $product->add();
				if ($action)
				{
					$this->add_products[] = $product->id;
					$log_text = 'Added Product, Name: '.current($product->name).', ID: '.$product->id;
					if (isset($product->reference))
						$log_text .= ', Reference: '.$product->reference;
					$this->logger->logInfo($log_text);
				}
			}
		}
		else
		{
			$action = false;
			$this->total_items_invalid++;
			if (isset($product->name))
				$this->logger->logInfo('Invalid Product, Name: '.current($product->name).'. Product will be skipped.');
		}

		//Fields below need to be added with product id
		if ($action)
		{
			//Tags
			$tag_mapped = false;
			foreach ($this->languages as $lang)
			{
				if (!empty($raw_prod[$settings['field_names']['tag'][$lang['id_lang']][0]]))
				{
					$tag_mapped = true;
					break;
				}
			}
			if ($tag_mapped)
			{
				Tag::deleteTagsForProduct($product->id);
				foreach ($this->languages as $lang)
				{
					$product_tags = array();
					foreach ($settings['field_names']['tag'][$lang['id_lang']] as $tag_field)
					{
						if (!empty($raw_prod[$tag_field]))
						{
							if (strpos($raw_prod[$tag_field], ','))
								$product_tags = array_filter(array_merge($product_tags, explode(',', $raw_prod[$tag_field])));
							else
								$product_tags[] = Tools::strtolower(trim($raw_prod[$tag_field]));
						}
					}
					foreach ($product_tags as $key => $tag) {
						$product_tags[$key] = preg_replace('/!<;>;\?=\+#"°{}_\$%/i', '', $tag);
					}
					if ($product_tags)
						Tag::addTags($lang['id_lang'], $product->id, array_unique($product_tags));
				}
			}

			//Quantity
			if (isset($raw_prod[$settings['field_names']['quantity']]))
			{
				foreach ($this->shops as $shop)
					StockAvailable::setQuantity((int)$product->id, 0, $raw_prod[$settings['field_names']['quantity']], (int)$shop);
			}

			//Specific Prices
			if (isset($raw_prod[$settings['field_names']['specific_price'][0]]))
				$this->setSpecificPrices($raw_prod, $settings, $product->id);

			$isCombinationUpdate = ($combination_id || !empty($settings['field_names']['attribute']))
				&& !$product->is_virtual;

			if ($category_mapped)
			{
				if ($settings['category_match'] == 'name')
					$categories = $this->setCategories($settings['field_names']['category'], $raw_prod);
				else
				{
					$categories = array();
					foreach ($settings['field_names']['category'] as $cat_lang_groups)
					{
						foreach ($cat_lang_groups as $cat_groups)
						{
							if (!empty($raw_prod[$cat_groups[0]]) && is_numeric($raw_prod[$cat_groups[0]]))
								$categories[] = $raw_prod[$cat_groups[0]];
						}
					}
				}
				if (!empty($categories))
				{
					if ($settings['delete_cats'])
					{
						$product->deleteCategories();
						$this->logger->logInfo('Deleted Category Associations for Product ID: '.$product->id);
					}
					Cache::clean('Product::getProductCategories_'.(int)$product->id);
					$product->addToCategories($categories);
					$this->assignDefaultCategory(end($categories), $product->id);
				}
			}
			elseif (!$update_id)
			{
				$product->addToCategories(array(Configuration::get('PS_HOME_CATEGORY')));
				$this->assignDefaultCategory(Configuration::get('PS_HOME_CATEGORY'), $product->id);
			}

			//Carriers format-> <carrier>:<delay_message>
			if (isset($raw_prod[$settings['field_names']['carriers'][0]]))
				$this->setCarriers($raw_prod, $settings, $product->id);

			if (!empty($settings['field_names']['def_supplier']) || !empty($settings['field_names']['supplier'][0]))
			{
				$product->deleteFromSupplier();
				$id_def_supplier = $this->setSuppliers($raw_prod, $settings, $product->id);
				$this->assignDefaultSupplier($id_def_supplier, $product->id);
			}

			//Features
			if (Configuration::get('PS_FEATURE_FEATURE_ACTIVE')	&& !empty($settings['field_names']['feature'][$this->languages[0]['id_lang']][0]))
				$this->setFeatures($raw_prod, $settings, $product->id);

			if (isset($raw_prod[$settings['field_names']['image'][0]]) || !empty($raw_prod[$settings['field_names']['cover_image']]))
			{
				if ($update_id && $settings['delete_images'])
						$this->deleteImages($product->id);

				$field_images = array_merge(array($settings['field_names']['cover_image']), $settings['field_names']['image']);
				$i = 1;
				foreach ($field_images as $img)
				{
					if (!empty($raw_prod[$img]))
					{
						$isCoverImage = ($i == 1);
						$this->fetchImage($raw_prod[$img], $product->id, $isCoverImage, $settings['thumbnail'], $settings['remote_images']);
					}
					$i++;
				}
			}

			//Combinations
			if ($isCombinationUpdate)
			{

				$attribute_ids = array();
				//Prepare Attributes first
				if (!empty($settings['field_names']['attribute']))
				{
					$attributes = $this->getAttributes($settings, $raw_prod);
					$attribute_ids = $this->createAttributes($attributes, $settings);
					//see if combination exists for the product by attribute values
					if (!$combination_id && $update_id)
						$combination_id = $this->getCombinationByAttributes($attribute_ids, $product->id);
				}
				$this->setCombinations($raw_prod, $settings, $product->id, $combination_id, $attribute_ids);
			}

			//Customization Fields
			if (isset($raw_prod[$settings['field_names']['custom_file'][$this->languages[0]['id_lang']][0]])
				|| isset($raw_prod[$settings['field_names']['custom_text'][$this->languages[0]['id_lang']][0]]))
			{
				$labels = $this->setCustomizationFields($raw_prod, $settings, $product->id, $combination_id);
				if ($labels)
				{
					$product_label = new Product($product->id);
					$product_label->customizable = 1;
					$product_label->deleteCustomization();

					$product_label->uploadable_files = count($labels['custom_file']);
					$product_label->text_fields = count($labels['custom_text']);

					if ((int)$product_label->uploadable_files > 0)
					{
						for ($i = 0; $i < (int)$product_label->uploadable_files; $i++)
						{
							if ($custom_id = $this->getLabelId($labels['custom_file'][$i], Product::CUSTOMIZE_FILE, $product->id))
							{
								//add to product
								$this->addProductToLabel($product->id, $custom_id, Product::CUSTOMIZE_FILE);
							}
							else
								$this->createLabel($labels['custom_file'][$i], Product::CUSTOMIZE_FILE, $product->id);
						}
					}

					if ((int)$product_label->text_fields > 0)
					{
						for ($i = 0; $i < (int)$product_label->text_fields; $i++)
						{
							if ($custom_id = $this->getLabelId($labels['custom_text'][$i], Product::CUSTOMIZE_TEXTFIELD, $product->id))
							{
								//add to product
								$this->addProductToLabel($product->id, $custom_id, Product::CUSTOMIZE_TEXTFIELD);
							}
							else
								$this->createLabel($labels['custom_text'][$i], Product::CUSTOMIZE_TEXTFIELD, $product->id);

						}
					}
					$product_label->update();
					// Set cache of feature detachable to true
					Configuration::updateGlobalValue('PS_CUSTOMIZATION_FEATURE_ACTIVE', '1');
				}
			}

			//Downloads
			if (isset($raw_prod[$settings['field_names']['file']]))
			{
				//activate feature
				if (!Configuration::get('PS_VIRTUAL_PROD_FEATURE_ACTIVE'))
					Configuration::updateGlobalValue('PS_VIRTUAL_PROD_FEATURE_ACTIVE', '1');
				$this->setProductDownload($raw_prod, $settings, $product->id);
			}

			//Attachments
			if (isset($raw_prod[$settings['field_names']['attachment'][$this->languages[0]['id_lang']][0]]))
				$this->setAttachments($raw_prod, $settings, $product->id);

			//Advanced Stock Management
			if ($product->advanced_stock_management && isset($settings['field_names']['warehouse'][0]))
			{
				foreach ($settings['field_names']['warehouse'] as $warehouse)
				{
					//check for warehouse
					$warehouse_id = 0;
					if (isset($raw_prod[$warehouse]))
						$warehouse_id = $this->getWarehouseId($raw_prod[$warehouse]);
					if ($warehouse_id)
					{
						Warehouse::setProductLocation($product->id, $combination_id, $warehouse_id, '');
						$stock_id = $this->getStockByProductId($product->id, $warehouse_id);
						if (isset($raw_prod[$settings['field_names']['physical_quantity']]))
						{
							StockAvailable::setProductDependsOnStock($product->id, true);
							if ($stock_id)
							{
								$stock = new StockCore($stock_id);
								$stock->id_product = $product->id;
								$stock->id_product_attribute = $combination_id;
								$stock->id_warehouse = $warehouse_id;
								$stock->reference = isset($product->reference) ? $product->reference : '';
								$stock->ean13 = isset($product->ean13) ? $product->ean13 : '';
								$stock->physical_quantity = $raw_prod[$settings['field_names']['physical_quantity']];
								$stock->price_te = isset($product->unit_price) ? $product->unit_price : '0';

								$field_error = $stock->validateFields($this->validate);
								$lang_field_error = $stock->validateFieldsLang($this->validate);

								if ($field_error === true && $lang_field_error === true)
									$stock->update();
							}
							else
							{
								$stock = new StockCore();
								$stock->id_product = $product->id;
								$stock->id_product_attribute = $combination_id;
								$stock->id_warehouse = $warehouse_id;
								$stock->physical_quantity = $raw_prod[$settings['field_names']['physical_quantity']];
								$stock->usable_quantity = $raw_prod[$settings['field_names']['physical_quantity']];
								$stock->price_te = isset($product->unit_price) ? $product->unit_price : '0';

								$field_error = $stock->validateFields($this->validate);
								$lang_field_error = $stock->validateFieldsLang($this->validate);

								if ($field_error === true && $lang_field_error === true)
								{
									$stock->add();
									$stock_id = $stock->id;
								}
							}
						}
						//stock movement
						if (isset($raw_prod[$settings['field_names']['physical_quantity']])
							&& $stock_id)
						{
							//context currency, unit price
							$stock_mvt = new StockMvtCore();
							$stock_mvt->id_stock = $stock_id;
							$stock_mvt->id_employee = $this->context->employee->id;
							$stock_mvt->date_add = date('Y-m-d H:i:s');
							$stock_mvt->physical_quantity = abs((int)$raw_prod[$settings['field_names']['physical_quantity']]);
							$stock_mvt->id_stock_mvt_reason = $settings['id_stock_mvt_reason'];
							$stock_mvt->sign = ((int)$raw_prod[$settings['field_names']['physical_quantity']] > 0) ? 1 : 0;
							$stock_mvt->price_te = !empty($product->unit_price) ? $product->unit_price : '';

							$field_error = $stock_mvt->validateFields($this->validate);
							$lang_field_error = $stock_mvt->validateFieldsLang($this->validate);

							if ($field_error === true && $lang_field_error === true)
								$stock_mvt->add();
						}
					}
				}
				StockAvailable::synchronize($product->id);
			}

			//add pack products & accessories to association array, but not if they are virtual products
			if ($product->getType() !== Product::PTYPE_VIRTUAL)
			{
				$associations = array('pack_products', 'accessories');
				foreach ($associations as $association)
				{
					if (isset($raw_prod[$settings['field_names'][$association][0]]))
					{
						foreach ($settings['field_names'][$association] as $field)
						{
							if (isset($raw_prod[$field]))
								$this->associations[$association][$product->id][] = $raw_prod[$field];
						}
					}
				}
			}
		}
	}

	/* Update simple fields for product table & specials
	 *
	 * @param int $update_id
	 * @param array $fields field name and field value
	 */
	public function simpleUpdate($update_id, $fields)
	{
		$specific_prices = isset($fields['specific_price']) ? $fields['specific_price'] : '';
		unset($fields['specific_price']);
		$field_amount = count($fields);

		if (!empty($specific_prices))
		{
			$specific_shops = $this->getSpecificPriceShops($update_id);
			if ($specific_shops)
				$this->deleteSpecificPriceByShop($update_id, $specific_shops);
			foreach ($specific_prices as $specific_price)
			{
				if (is_object($specific_price))
				{
					$specific_fields = array('id_specific_price_rule', 'id_cart', 'id_product_attribute',
						'id_currency', 'id_country', 'id_group', 'id_customer', 'price', 'from_quantity', 'reduction',
						'reduction_type', 'from', 'to', 'id_product_attribute');
					foreach ($this->shops as $shop)
					{
						$sql = 'INSERT '._DB_PREFIX_.'specific_price SET id_product = \''.(int)$update_id.'\', ';
						foreach ($specific_fields as $field)
						{
							if (isset($specific_price->{$field}))
								$sql .= ' `'.bqSQL($field).'` = \''.bqSQL($specific_price->{$field}).'\', ';
						}
						$sql .= '`id_shop` = '.(int)$shop;
						Db::getInstance()->execute($sql);
					}
				}
			}
		}

		if ($fields)
		{
			$shop_count = count($this->shops);
			$field_update = 0;
			$sql = '
			UPDATE '._DB_PREFIX_.'product_shop AS ps
			LEFT JOIN '._DB_PREFIX_.'product AS p
			ON ps.`id_product` = p.`id_product`
			SET ';
			foreach ($fields as $field_name => $field_value)
			{
				$field_update++;
				//quantity only in product table, not product_shop
				if ($field_name == 'quantity')
				{
					$sql .= 'p.`'.bqSQL($field_name)."` = '".bqSQL((float)$field_value)."'";
					$sql_updatestock = 'UPDATE '._DB_PREFIX_.'stock_available SET `quantity` = \''.bqSQL($field_value).'\' WHERE `id_product` = \''.bqSQL((float)$update_id).'\' AND `id_product_attribute` = 0';
					Db::getInstance()->execute($sql_updatestock);
				}
				else
				{
					$sql .= 'ps.`'.bqSQL($field_name)."` = '".bqSQL((float)$field_value)."'";
					//only add rest to product table if there's only 1 shop
					if ($shop_count == 1)
						$sql .= ', p.`'.bqSQL($field_name)."` = '".bqSQL((float)$field_value)."'";
				}
				if ($field_update != $field_amount)
					$sql .= ', ';
			}
			$sql .= " WHERE ps.`id_product` = '".(int)$update_id."' AND ";
			$sql .= ($shop_count > 1) ? '(': '';
			$s = 1;
			foreach ($this->shops as $shop)
			{
				$sql .= 'ps.`id_shop` = '.(int)$shop;
				if ($s != $shop_count)
					$sql .= ' OR ';
				$s++;
			}
			$sql .= ($shop_count > 1) ? ')': '';
			Db::getInstance()->execute($sql);

			$field_update++;
		}
		$this->update_products[] = $update_id;
	}

	public function fetchFeed(&$settings, $unzip_feed = false)
	{
		$success = false;
		$filename = _PS_MODULE_DIR_.'totalimportpro/feed.txt';
		if (isset($settings['source']))
		{
			if ($settings['source'] == 'feed_file')
			{

				if (defined('CLI_INITIATED'))
					$success = true; //we will do it with whatever feed is on the filesystem, no need to fetch.

				elseif (is_uploaded_file($_FILES['feed_file']['tmp_name']))
				{
					//GET FEED FROM POSTED FILE
					if ($_FILES['feed_file']['error'] == UPLOAD_ERR_OK)
						$success = move_uploaded_file($_FILES['feed_file']['tmp_name'], $filename);
					else
						$success = false;
				}
			}
			elseif ($settings['source'] == 'url')
			{
				if (in_array  ('curl', get_loaded_extensions()))
				{
					//GET FEED WITH CURL AND PARSE
					$ch = curl_init();
					$fp = fopen($filename, 'w');
					$url = str_replace('&amp;', '&', $settings['feed_url']);
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_FILE, $fp);
					if (ini_get('open_basedir') == '')
						curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
					curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
					curl_exec($ch);
					$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
					curl_close($ch);
					fclose($fp);
					$success = ($http_code != '404');
				}
				else
					$success = false;
			}
			elseif ($settings['source'] == 'ftp' && $settings['feed_ftpuser']
				&& $settings['feed_ftpserver'] && $settings['feed_ftppass'] && $settings['feed_ftppath'])
				$success = $this->fetchFtp($settings['feed_ftpserver'],
											$settings['feed_ftpuser'],
											$settings['feed_ftppass'],
											$settings['feed_ftppath'],
											$filename);
			elseif ($settings['source'] == 'filepath')
			{
				if (file_exists($settings['feed_filepath']))
					$success = Tools::copy($settings['feed_filepath'], $filename);
			}
		}

		$has_content = Tools::file_get_contents($filename);

		if ($unzip_feed && $has_content)
		{
			$temp_file = $this->unzip($filename);
			rename($temp_file, $filename);
		}
		return ($success && $has_content) ? $filename : false;
	}

	public function importFile($filename, &$settings)
	{
		$error = array();
		if (isset($settings['file_encoding']) && $settings['file_encoding'] != 'UTF-8')
			$this->file_encoding = $settings['file_encoding'];
		if ($settings['format'] == 'csv')
		{
			//delimiter
			if ($settings['feed_csv'] == '\t')
				$settings['feed_csv'] = "\t";
			elseif ($settings['feed_csv'] == '')
				$settings['feed_csv'] = ',';

			$csv_options = array();
			if (!empty($settings['safe_headers']))
				$csv_options['safe_headers'] = $settings['safe_headers'];
			if (!empty($settings['has_headers']))
				$csv_options['has_headers'] = $settings['has_headers'];
			if (!empty($settings['cron_feed']) && !defined('CLI_INITIATED'))
				$csv_options['cron_feed'] = $settings['cron_feed']; //only imports 1 line

			$this->importCSV($filename, $settings['feed_csv'], $csv_options);
		}
		elseif ($settings['format'] == 'xml')
		{
			$this->table_created = false;
			$xml_options = array();
			if (!empty($settings['cron_feed']) && !defined('CLI_INITIATED'))
				$xml_options['cron_feed'] = $settings['cron_feed']; //only imports 1 line
			$xml_errors = $this->importXML($filename, $settings['feed_xml'], $xml_options);
			if (!empty($xml_errors['error']))
				$error = array_merge($error, $xml_errors['error']);
			if ($this->total_items_ready === 0)
				$error[] = $this->l('No products found, check your product tag');
		}

		if ($settings['format'] == 'csv' && $this->total_items_ready == 1)
			$error[] = $this->l('Warning: CSV file contains only one line: If you create your CSV file with Mac you need to save it as "CSV (Windows)"!');
		if ($this->total_items_ready == 0)
			$error[] = $this->l('Warning: Your Feed contained no valid lines, please check your feed and try again');

		return array(
			'total_items_ready' => $this->total_items_ready,
			'total_items_missed' => $this->total_items_missed,
			'error' => $error,
		);
	}

	public function importXML($filename, $product_tag, $xml_options)
	{
		$this->product_tag = $product_tag;
		if (!empty($xml_options['cron_feed']))
			$this->xml_cron_fetch = $xml_options['cron_feed'];
		$this->xml_data = '';
		$fh = fopen($filename, 'r');
		$xml_parser = xml_parser_create($this->file_encoding);
		xml_set_object($xml_parser, $this);
		xml_set_element_handler($xml_parser, 'startTag', 'endTag');
		xml_set_character_data_handler($xml_parser, 'cData');
		xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, false);
		while ($data = fread($fh, 4096))
		{
			if (!xml_parse($xml_parser, $data, feof($fh)))
			{
				xml_parser_free($xml_parser);
				return false;
			}
			if (!empty($xml_options['cron_feed']) && $this->total_items_ready >= CRON_FETCH_NUM)
			{
				xml_parser_free($xml_parser);
				return true;
			}
		}
		xml_parser_free($xml_parser);
		return true;
	}

	public function importCSV($filename, $delimiter, $csv_options)
	{
		$headings = array();
		$fh = fopen($filename, 'r');
		if (!empty($csv_options['safe_headers']) || empty($csv_options['has_headers']))
		{
			$count = count(fgetcsv($fh, 0, $delimiter));
			//if there are no file headers, reset the file read after doing the count
			if (empty($csv_options['has_headers']))
				$fh = fopen($filename, 'r');
			for ($i = 0; $i < $count; $i++)
				$headings[$i] = 'column_'.$i;
		}
		else
		{
			$headings = array_map('trim', fgetcsv($fh, 0, $delimiter)); //trim white space from all headings for db insertion.
			$headings = array_map(array($this, 'validateHeaderForSql'), $headings);
		}

		$row_count = count($headings);
		for ($i = 0; $i < $row_count; $i++)
		{
			if (empty($headings[$i]))
				$headings[$i] = 'column_'.($i + 1);
			$row_count = count($headings);
		}
		if ($this->file_encoding != 'UTF-8')
		{
			foreach ($headings as $i => $heading)
				$headings[$i] = iconv($this->file_encoding, 'UTF-8//TRANSLIT', $heading);
		}
		$this->createEmptyTable($headings);
		$num_cols = count($headings);
		//most complicated do-while ever written.
		do {
			//miss items that have incorrect column count:
			while (($row = fgetcsv($fh, 0, $delimiter)) !== false)
			{
				if (count($row) != $num_cols)
					$this->total_items_missed++;
				else
					break;
			}
			if ($row)
			{
				if (!empty($csv_options['cron_feed']))
				{
					if ($this->total_items_ready >= CRON_FETCH_NUM)
						break;
				}
				$this->insertProduct(array_combine($headings, $row));
				$this->total_items_ready++;
			}
		} while ($row);
	}

	/**
	 * Apply operations to product data
	 *
	 */
	public function runAdjustments(&$adjustments)
	{
		$operations = $this->getOperations();
		foreach ($adjustments as $adjustment)
		{
			//ensure all adjustment values are decoder for operations
			$adjustment = array_map('html_entity_decode', $adjustment);
			$op_name = array_shift($adjustment);
			//run each adjustment
			if (is_callable(array($this, $operations[$op_name]['function'])))
			{
				$adjustment_fields = array();
				$adjustment_fields[] = $adjustment;
				if (!in_array($this->l('-- Select --'), $adjustment))
					call_user_func_array(array($this, $operations[$op_name]['function']), $adjustment_fields);
			}
		}
	}

	/*
	 * Functions to adjust data on the way in
	 *
	 * @return
	 */
	public function getOperations()
	{
		$operations = array(
			'multiplyPrice' => array(
				'name' => $this->l('Adjust Price (Multiply)'),
				'function' => 'multiply',
				'inputs'=>array(
					array('type'=>'field', 'prepend'=>$this->l('Multiply')),
					array('type'=>'text', 'prepend'=>$this->l('by'))
				),
				'label' => $this->l('Most Popular'),
			),
			'addPrice' => array(
				'name' => $this->l('Adjust Price (Add)'),
				'function' => 'add',
				'inputs'=>array(
					array('type'=>'text', 'prepend'=>$this->l('Add')),
					array('type'=>'field', 'prepend'=>$this->l('to')),
				),
				'label' => $this->l('Most Popular'),
			),
			'splitFieldsCategory' => array(
				'name' => $this->l('Split Category on Delimiter'),
				'function' => 'splitFields',
				'inputs'=>array(
					array('type'=>'field', 'prepend'=>$this->l('Split')),
					array('type'=>'text', 'prepend'=>$this->l('on')),
				),
				'label' => $this->l('Most Popular'),
			),
			'appendImage' => array(
				'name' => $this->l('Append Text to Image'),
				'function' => 'appendText',
				'inputs'=>array(
					array('type'=>'text', 'prepend'=>$this->l('Append')),
					array('type'=>'field', 'prepend'=>$this->l('after'))
				),
				'label' => $this->l('Most Popular'),
			),
			'prependImage' => array(
				'name' => $this->l('Prepend Text to Image'),
				'function' => 'prependText',
				'inputs'=>array(
					array('type'=>'text', 'prepend'=>$this->l('Prepend')),
					array('type'=>'field', 'prepend'=>$this->l('to'))
				),
				'label' => $this->l('Most Popular'),
			),
			'append' => array(
				'name' => $this->l('Append Text to Any Field'),
				'function' => 'appendText',
				'inputs'=>array(
					array('type'=>'text', 'prepend'=>$this->l('Append')),
					array('type'=>'field', 'prepend'=>$this->l('after'))
				),
				'label' => $this->l('Advanced'),
			),
			'prepend' => array(
				'name' => $this->l('Prepend Text to Any Field'),
				'function' => 'prependText',
				'inputs'=>array(
					array('type'=>'text', 'prepend'=>$this->l('Prepend')),
					array('type'=>'field', 'prepend'=>$this->l('to'))
				),
				'label' => $this->l('Advanced'),
			),
			'multiply' => array(
				'name' => $this->l('Multiply any Field'),
				'function' => 'multiply',
				'inputs'=>array(
					array('type'=>'field', 'prepend'=>$this->l('Multiply')),
					array('type'=>'text', 'prepend'=>$this->l('by'))
				),
				'label' => $this->l('Advanced'),
			),
			'add' => array(
				'name' => $this->l('Add to any Field'),
				'function' => 'add',
				'inputs'=>array(
					array('type'=>'text', 'prepend'=>$this->l('Add'),),
					array('type'=>'field', 'prepend'=>$this->l('to'),)
				),
				'label' => $this->l('Advanced'),
			),
			'splitFields' => array(
				'name' => $this->l('Split Any Field'),
				'function' => 'splitFields',
				'inputs'=>array(
					array('type'=>'field', 'prepend'=>$this->l('Split')),
					array('type'=>'text', 'prepend'=>$this->l('on')),
				),
				'label' => $this->l('Advanced'),
			),
			'replace' => array(
				'name'=> $this->l('Replace Text'),
				'function' => 'replaceText',
				'inputs'=>array(
					array('type'=>'text', 'prepend'=>$this->l('Replace')),
					array('type'=>'text', 'prepend'=>$this->l('with')),
					array('type'=>'field', 'prepend'=>$this->l('in'))
				),
				'label' => $this->l('Advanced'),
			),
			'replaceNewLines' => array(
				'name'=> $this->l('Convert Newlines to HTML'),
				'function' => 'replaceNewLines',
				'inputs'=>array(
					array('type'=>'field', 'prepend'=>$this->l('in'))
				),
				'label' => $this->l('Advanced'),
			),
			'remove' => array(
				'name'=> $this->l('Remove Text'),
				'function' => 'removeText',
				'inputs'=>array(
					array('type'=>'text', 'prepend'=>$this->l('Remove')),
					array('type'=>'field', 'prepend'=>$this->l('in'))
				),
				'label' => $this->l('Advanced'),
			),
			'deleteRow' => array(
				'name' => $this->l('Filter Products (equals)'),
				'function' => 'deleteRowsWhere',
				'inputs'=>array(
					array('type'=>'field', 'prepend'=>$this->l('Exclude products where')),
					array('type'=>'text', 'prepend'=>$this->l('equals'))
				),
				'label' => $this->l('Most Popular'),
			),
			'deleteRowWhereNot' => array(
				'name' => $this->l('Filter Products (not equals)'),
				'function' => 'deleteRowsWhereNot',
				'inputs'=>array(
					array('type'=>'field', 'prepend'=>$this->l('Exclude products where')),
					array('type'=>'text', 'prepend'=>$this->l('does not equal'))
				),
				'label' => $this->l('Most Popular'),
			),
			'deleteRowWhereContains' => array(
				'name' => $this->l('Filter Products (containing)'),
				'function' => 'deleteRowsWhereContains',
				'inputs'=>array(
					array('type'=>'field', 'prepend'=>$this->l('Exclude products where')),
					array('type'=>'text', 'prepend'=>$this->l('contains'))
				),
				'label' => $this->l('Advanced'),
			),
			'deleteRowWhereNotContains' => array(
				'name' => $this->l('Filter Products (not containing)'),
				'function' => 'deleteRowsWhereNotContains',
				'inputs'=>array(
					array('type'=>'field', 'prepend'=>$this->l('Exclude products where')),
					array('type'=>'text', 'prepend'=>$this->l('does not contain')),
				),
				'label' => $this->l('Advanced'),
			),
			'duplicateField' => array(
				'name' => $this->l('Clone field'),
				'function' => 'duplicateField',
				'inputs'=>array(
					array('type'=>'field', 'prepend'=>$this->l('Duplicate')),
					array('type'=>'text', 'prepend'=>$this->l('to'))
				),
				'label' => $this->l('Advanced'),
			),
			'mergeColumns' => array(
				'name' => $this->l('Append Field to Field'),
				'function' => 'mergeColumns',
				'inputs'=>array(
					array('type'=>'field', 'prepend'=>$this->l('Append')),
					array('type'=>'field', 'prepend'=>$this->l('to')),
					array('type'=>'text', 'prepend'=>$this->l('separated by'))
				),
				'label' => $this->l('Advanced'),
			),
			'mergeRows' => array(
				'name' => $this->l('Merge multi-row products'),
				'function' => 'mergeRows',
				'inputs'=>array(
					array('type'=>'field', 'prepend'=>$this->l('Common field')),
					array('type'=>'field', 'prepend'=>$this->l('Merge the following fields'), 'option' => 'addMore'),
				),
				'label' => $this->l('Advanced'),
			),
			'splitCombinations' => array(
				'name' => $this->l('Split combinations into separate rows'),
				'function' => 'splitCombinations',
				'inputs' => array(
					array('type'=>'field', 'prepend'=>$this->l('Product Identifier')),
					array('type'=>'text', 'prepend'=>$this->l('Split combination fields on')),
					array('type'=>'field', 'prepend'=>$this->l('Split the following combination fields'), 'option' => 'addMore'),
				),
				'label' => $this->l('Advanced'),
			),
			'customColumn' => array(
				'name' => $this->l('Create Custom Column'),
				'function' => 'customColumn',
				'inputs' => array(
					array('type'=>'text', 'prepend'=>$this->l('Column Name')),
					array('type'=>'text', 'prepend'=>$this->l('Column Value')),
				),
				'label' => $this->l('Advanced'),
			),
		);
		return $operations;
	}

	/**
	 * Update the configuration value in all shops
	 *
	 * @param $config_name string
	 * @param $value string
	 */
	public function updateConfig($config_name, $value)
	{
		$shop_tree = Shop::getTree();
		foreach ($shop_tree as $shop_group)
		{
			foreach ($shop_group['shops'] as $shop)
				Configuration::updateValue($config_name, $value, false, $shop_group['id'], $shop['id_shop']);
		}
		Configuration::updateValue($config_name, $value);
	}


	/**
	 * Move old logs and clear current log
	 */
	private function rotateLogs()
	{
		Tools::copy(_PS_MODULE_DIR_.'totalimportpro/log/tip.log', _PS_MODULE_DIR_.'totalimportpro/log/old_log1.log');
		file_put_contents(_PS_MODULE_DIR_.'totalimportpro/log/tip.log', '');
	}

	/**
	 * Delete/Disable/Set Quantity to 0 for products remaining in existing_prods db table
	 *
	 * @param  string $delete_diff Specifices what to do with the existing_prods. (delete, disable, qtytozero)
	 * @param  string $update_field Name of the field being used to identify products for logging
	 * @return int Number of products updated
	 */
	private function deleteExistingProds($delete_diff, $update_field)
	{
		//delete/disable items that were in the store but not in the import file
		$existing_prods = $this->getExistingProds();
		$total_prods_updated = 0;

		$this->initFileLogger();
		$this->logger->logInfo('Updating products in store but not in feed');
		foreach ($existing_prods as $item_to_delete)
		{
			if ($delete_diff == 'delete')
			{
				$prod_delete = new Product ($item_to_delete['product_id']);
				$prod_delete->delete();
			}
			elseif ($delete_diff == 'disable')
				$this->disableProduct($item_to_delete['product_id']);
			else
				$this->zeroQuantityProduct($item_to_delete['product_id']);
			$this->logger->logInfo('Product '.$update_field.': '.$item_to_delete['product_id_field'].' '.$delete_diff);
			$total_prods_updated++;

		}

		return $total_prods_updated;
	}

	public function createDeleteDiffTable($update_field, $settings)
	{
		Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'hj_existing_prods');

		$sql = 'CREATE TABLE '._DB_PREFIX_.'hj_existing_prods (product_id INT(11) AUTO_INCREMENT, product_id_field VARCHAR(64), PRIMARY KEY (product_id))';
		Db::getInstance()->execute($sql);

		$sql = 'SELECT p.id_product '.($update_field == 'name' ? '' : ', '.$update_field).' FROM `'._DB_PREFIX_.'product` p';

		if (isset($settings['store']))
		{
			$sql .= ' JOIN  `'._DB_PREFIX_.'product_shop` ps ON p.id_product = ps.id_product WHERE ps.shop_id IN (';
			$store_id_array = array();
			foreach ($settings['store'] as $key => $store_id)
				$store_id_array[] = $store_id;
			$sql .= implode(', ', $store_id_array);
			$sql .= ')';
		}

		$existing = Db::getInstance()->executeS($sql);
		if ($update_field == 'name')
			$update_field = 'id_product';
		if (!empty($existing))
		{
			$sql = 'INSERT INTO '._DB_PREFIX_.'hj_existing_prods (product_id, product_id_field)';
			$sql .= ' VALUES ('.$existing[0]['id_product'].', \''.$existing[0][$update_field].'\')';

			$num_prods = count($existing);
			for ($prod_num = 1; $prod_num < $num_prods; $prod_num++)
			{
				$sql .= ', ('.$existing[$prod_num]['id_product'].', \''.$existing[$prod_num][$update_field].'\')';
				// The maximum number of rows allowed in one VALUES clause is 1000 so run the query every 1000 prods
				if ($prod_num % 999 == 0)
				{
					Db::getInstance()->execute($sql);
					$prod_num++;
					$sql = 'INSERT INTO '._DB_PREFIX_.'hj_existing_prods (product_id, product_id_field)';
					$sql .= ' VALUES ('.$existing[$prod_num]['product_id'].', \''.$existing[$prod_num][$update_field].'\')';
				}
			}
			Db::getInstance()->execute($sql);
		}
	}

	public function deleteExistingProdHash($prod_hash)
	{
		Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'hj_existing_prods WHERE product_id_field=\''.$prod_hash.'\'');
	}

	public function getExistingProds()
	{
		$query = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'hj_existing_prods');
		return $query;
	}


	private function initFileLogger()
	{
		$this->logger = new FileLogger(0);
		$this->logger->setFileName(_PS_MODULE_DIR_.'totalimportpro/log/tip.log');
	}

	/**
	 * Ajax Calls
	 *
	 * PrestaShop sends ajax calls to these appropriate functions if you use the ajax url:
	 * $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name;
	 *
	 * and use the data params to specify ajax=true and action=YourAction
	 * (YourAction would invoke ajaxProcessYourAction)
	 */

	public function ajaxProcessImportEnd()
	{
		$this->initFileLogger();
		$this->logger->logInfo('Import finished, running cleanup tasks.');

		$this->ajaxProcessRegenCatTree();

		$json = array();
		if (Tools::getValue('delete_diff') != 'ignore')
			$json['affected_products'] = $this->deleteExistingProds(Tools::getValue('delete_diff'), Tools::getValue('update_field'));

		echo Tools::jsonEncode($json);
	}

	public function ajaxProcessRegenCatTree()
	{
		$this->initFileLogger();
		$this->logger->logInfo('Regenerating Category Tree..');
		Category::regenerateEntireNtree();
	}

	public function ajaxProcessImport()
	{
		unset($_POST['step']);
		$this->updateConfig('IMPORT_STEP5', serialize($_POST));

		$settings = array_merge(unserialize(Configuration::get('IMPORT_STEP1')),
								unserialize(Configuration::get('IMPORT_STEP2')),
								unserialize(Configuration::get('IMPORT_STEP3')),
								unserialize(Configuration::get('IMPORT_STEP4')),
								unserialize(Configuration::get('IMPORT_STEP5')));

		if (isset($settings['ajax_limit']))
			$limit = $settings['ajax_limit'];
		else
			$limit = 25;

		$total_prod = Tools::getValue('total_prod');
		$prod_num = Tools::getValue('prod_num');
		$items_left = $total_prod - $prod_num;
		if ($items_left < $limit)
			$limit = $items_left;

		$this->initFileLogger();

		if (!Tools::getIsset('first_run'))
		{
			$settings['reset_store'] = 0;
			$settings['reset_combinations'] = 0;
		}
		else
		{
			$this->rotateLogs();
			$this->logger->logInfo('Import Starting');
		}

		$this->logger->logInfo('Importing Products '.($prod_num + 1).' to '.min($prod_num + $limit, $total_prod).' of '.$total_prod);
		$this->import($settings, $prod_num, $limit);
		$this->logger->logInfo('Updated '.$this->total_items_updated.' and Added '.$this->total_items_added.' Products');

		echo Tools::jsonEncode(array('limit' => $limit, 'added' => $this->total_items_added, 'updated' => $this->total_items_updated));
	}

	public function ajaxProcessGetNextRow()
	{
		$query = 'SELECT * FROM '._DB_PREFIX_.'hj_import LIMIT '.(int)Tools::getValue('nextRow').', 1';
		$product = Db::getInstance()->executeS($query);
		$fields = array();
		if (isset($product[0]))
		{
			foreach ($product[0] as $field => $value)
				$fields[$field] = html_entity_decode($value);
		}

		echo ($fields) ? Tools::jsonEncode($fields) : 0;
	}

	public function ajaxProcessLoadProfile()
	{
		$profile_name = Tools::getValue('settings_group', '');
		for ($i = 1; $i <= 5; $i++)
		{
			$settings = array();
			$query = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'hj_import_settings WHERE `group` = \''.bqSQL($profile_name).'\' AND `step` = '.(int)$i);
			foreach ($query as $result)
				$settings[$result['name']] = unserialize($result['value']);
			$this->updateConfig('IMPORT_STEP'.$i, serialize($settings));
		}
		$output = sprintf("Settings Successfully Loaded: '%s'", $profile_name);
		echo $output;
	}

	public function ajaxProcessDeleteProfile()
	{
		$profile_name = Tools::getValue('settings_group', '');
		if ($profile_name)
		{
			Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'hj_import_settings WHERE `group` = \''.bqSQL($profile_name).'\'');
			$output = sprintf('Success: Deleted profile \'%s\'', $profile_name);
			echo $output;
		}
	}

	public function ajaxProcessSaveSettings()
	{
		unset($_POST['method']);
		if (Tools::getIsset('step'))
		{
			$step = Tools::getValue('step', 1);
			$_POST['submitStep'.$step] = 1;
			unset($_POST['step']);
			$this->updateConfig('IMPORT_STEP'.$step, serialize($_POST));
			if ($step == 5)
			{
				if (Tools::getIsset('profile'))
				{
					$settings = array(
					unserialize(Configuration::get('IMPORT_STEP1')),
					unserialize(Configuration::get('IMPORT_STEP2')),
					unserialize(Configuration::get('IMPORT_STEP3')),
					unserialize(Configuration::get('IMPORT_STEP4')),
					unserialize(Configuration::get('IMPORT_STEP5')),
					);
					Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'hj_import_settings WHERE `group` = \''.bqSQL(Tools::getValue('profile')).'\'');

					$settings_count = count($settings);
					for ($i = 0; $i < $settings_count; $i++)
					{
						foreach ($settings[$i] as $key => $value)
						{
							$step = $i + 1;
							Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'hj_import_settings SET
								`group` = \''.bqSQL(Tools::getValue('profile')).'\',
								`step` = '.(int)$step.',
								`name` = \''.bqSQL($key).'\',
								`value` = \''.bqSQL(serialize($value)).'\'');
						}
					}
				}
			}
		}
	}

	/*
	 * Function importSettings
	 *
	 * Responsible for importing csv of settings from step 1
	 *
	 * @param (none)
	 * @return (none)
	 */

	public function importSettings() {

		$file = $_FILES['settings_file'];
		$settings_added = 0;
		$settings_skipped = 0;

		if ($file['type'] !== 'text/csv') {
			$this->errors[] = 'Error Importing Settings: File supplied wasn\'t CSV';
		} else {
			$l = $file['tmp_name'];
			$csv = Tools::file_get_contents($l);
			$csv = explode(PHP_EOL, $csv);
			$headers = explode(',', array_shift($csv));
			$settings = array();

			if ($headers[0] !== 'setting_name' || $headers[1] !== 'setting_value') {
				$this->errors[] = 'Error Importing Settings: Incorrect CSV format (try exporting again)';
			} else {
				foreach ($csv as $key => $value) {
					if ($setting_split = $this->validateSetting($value)) {
						$step_num = $setting_split[0];
						$settings_serialzed = $setting_split[1];

						$this->updateConfig($step_num, $settings_serialzed);
						$settings_added++;
					} else {
						$settings_skipped++;
					}
				}
				$this->context->cookie->tip_success = 'TIP Settings Imported Successfully!';
				$this->context->cookie->was_settings_upload = true;
			}
		}
	}

	public function validateSetting($setting) {
		$setting = explode(',', $setting, 2);
		if (count($setting) === 2) {
			if (strpos($setting[0], 'IMPORT_STEP') !== false) {
				return $setting;
			}
		}
		return false;
	}

	/*
	 * Function exportSettings
	 *
	 * Responsible for returning csv of settings to step 5
	 *
	 * @param (none)
	 * @return (none)
	 */
	public function exportSettings() {

		$settings = array(
			'IMPORT_STEP1' => Configuration::get('IMPORT_STEP1'),
			'IMPORT_STEP2' => Configuration::get('IMPORT_STEP2'),
			'IMPORT_STEP3' => Configuration::get('IMPORT_STEP3'),
			'IMPORT_STEP4' => Configuration::get('IMPORT_STEP4'),
			'IMPORT_STEP5' => Configuration::get('IMPORT_STEP5'),
		);

		$csv_headers = "setting_name,setting_value\n";
		$csv_body = '';
		foreach ($settings as $index => $settings_array) {
			$csv_body .= $index.','.$settings_array."\n";
		}

		header('Pragma:  public');
	    header('Expires:  0');
	    header('Cache-Control:  must-revalidate, pre-check=0');
	    header('Cache-Control:  private', false);
	    header('Content-Type:  text/csv');
	    header('Content-Disposition:  attachment; filename="tip_settings.csv";');
	    header('Content-Transfer-Encoding:  binary');
	    header('Content-Length:  '.Tools::strlen($csv_headers.$csv_body));

		echo $csv_headers.$csv_body;
	}

	/**
	 * Process the submitted form's post variables
	 */
	private function _postProcess()
	{
		$token = Tools::getAdminTokenLite('AdminModules');
		//display confirmation messages, errors, redirects
		if (Tools::isSubmit('submitStep'))
		{
			unset($_POST['tab']);
			if (Tools::getValue('submitStep1'))
			{
				$this->updateConfig('IMPORT_STEP1', serialize($_POST));
				$filename = $this->fetchFeed($_POST, Tools::getValue('unzip_feed'));
				if ($this->validateStep1($filename, $_POST))
				{
					$import_status = $this->importFile($filename, $_POST);
					if (!empty($import_status['error']))
					{
						//Add csv message
						if (!$this->errors)
							$this->errors = array();
						$this->errors = array_merge($this->errors, $import_status['error']);
					}
					else
					{
						$success_message = $this->l(sprintf('Success: Feed fetched and parsed. Ready for import: %s.', $import_status['total_items_ready']));

						if (Tools::getValue('format') == 'csv')
							$success_message .= $this->l(sprintf(' Invalid CSV rows: %s', $import_status['total_items_missed']));
						$this->updateConfig('SUCCESS_STEP1', $success_message);
						Tools::redirectAdmin('index.php?tab=AdminModules&configure='.$this->name.'&token='.$token.'&step=2&step1Confirmation');
					}
				}
			}
			elseif (Tools::getValue('submitStep2'))
			{
				$this->updateConfig('IMPORT_STEP2', serialize($_POST));
				$success_message = $this->l('Success: Global Settings saved.');
				$this->updateConfig('SUCCESS_STEP2', $success_message);
				Tools::redirectAdmin('index.php?tab=AdminModules&configure='.$this->name.'&token='.$token.'&step=3&step2Confirmation');
			}
			elseif (Tools::getValue('submitStep3'))
			{
				$this->updateConfig('IMPORT_STEP3', serialize($_POST));
				if ($this->validateStep3())
				{
					//Adjust product data in DB.
					if (Tools::getValue('adjust') && is_array(Tools::getValue('adjust')))
					{
						$adjustments = Tools::getValue('adjust');
						$this->runAdjustments($adjustments);
					}
					$success_message = $this->l('Success: Operations saved.');
					$this->updateConfig('SUCCESS_STEP3', $success_message);
				}
				if (!$this->errors)
					Tools::redirectAdmin('index.php?tab=AdminModules&configure='.$this->name.'&token='.$token.'&step=4&step3Confirmation');
			}
			elseif (Tools::getValue('submitStep4'))
			{
				if (!Tools::getIsset('simple') || Tools::getValue('simple') != 1)
				{
					$categories = Tools::getValue('field_names');
					$this->cleanCategories($categories);
				}

				$this->updateConfig('IMPORT_STEP4', serialize($_POST));
				$success_message = $this->l('Success: Field Mappings saved.');
				$this->updateConfig('SUCCESS_STEP4', $success_message);
				Tools::redirectAdmin('index.php?tab=AdminModules&configure='.$this->name.'&token='.$token.'&step=5&step4Confirmation');
			}
			elseif (Tools::getValue('submitStep5')) //only active with HJ_DEV or for disabling products in store but not file
			{
				$this->updateConfig('IMPORT_STEP5', serialize($_POST));
				$settings = array_merge(unserialize(Configuration::get('IMPORT_STEP1')),
										unserialize(Configuration::get('IMPORT_STEP2')),
										unserialize(Configuration::get('IMPORT_STEP3')),
										unserialize(Configuration::get('IMPORT_STEP4')),
										unserialize(Configuration::get('IMPORT_STEP5')));

				$this->initFileLogger();
				$this->import($settings);
				$this->ajaxProcessRegenCatTree();

				$success_message = sprintf($this->l('Success: Added %s products, Updated %s products, Added %s combinations, Updated %s combinations'),
					$this->total_items_added, $this->total_items_updated, $this->total_combinations_added, $this->total_combinations_updated);
				$this->updateConfig('SUCCESS_IMPORT', $success_message);
				//redirect to product page.
				Tools::redirectAdmin($this->context->link->getAdminLink('AdminProducts'));
			}
		}
		elseif (Tools::isSubmit('step1Confirmation'))
			$this->_html .= $this->displayConfirmation(Configuration::get('SUCCESS_STEP1'));
		elseif (Tools::isSubmit('step2Confirmation'))
			$this->_html .= $this->displayConfirmation(Configuration::get('SUCCESS_STEP2'));
		elseif (Tools::isSubmit('step3Confirmation'))
			$this->_html .= $this->displayConfirmation(Configuration::get('SUCCESS_STEP3'));
		elseif (Tools::isSubmit('step4Confirmation'))
			$this->_html .= $this->displayConfirmation(Configuration::get('SUCCESS_STEP4'));
		elseif (Tools::isSubmit('step4Confirmation'))
			$this->_html .= $this->displayConfirmation(Configuration::get('SUCCESS_STEP5'));

		if ($this->errors)
		{
			foreach ($this->errors as $err)
				$this->_html .= '<div class="alert alert-danger error"><button type="button" class="close" data-dismiss="alert">×</button>'.$err.'</div>';
		}
	}

	private function getJsAddSave($text, $class = 'configuration_form')
	{
		return 'function addSave(result)
			{
				$(\'#'.$class.'\').prepend(\'<div class="bootstrap"><div class="module_confirmation conf confirm alert alert-success"><button type="button" '.((version_compare(_PS_VERSION_, '1.6', '<'))?'style="float:right;cursor:pointer;background:transparent;border:0;" onclick="$(\\\'.alert-success\\\').hide();" ':'').'class="close" data-dismiss="alert">×</button>'.$this->l($text).'</div></div>\');
			}';
	}

	/** Render form for Settings
	 *
	 */
	protected function displayForm()
	{
		//Step Links
		$pages = array(
			'1' => $this->l('Fetch Feed'),
			'2' => $this->l('Global Settings'),
			'3' => $this->l('Adjust Import Data'),
			'4' => $this->l('Field Mapping'),
			'5' => $this->l('Import'),
		);

		$page_buttons = array();
		foreach ($pages as $page => $title)
		{
			$page_buttons[$page] = array(
				'link'   => $this->_baseUrl.'&step='.$page,
				'title'  => $title,
				'button' => 'Step '.$page,
			);
		}

		if (version_compare(_PS_VERSION_, '1.6', '<'))
		{
			$this->_html .= '
			<style type="text/css">
				.row {
					margin-bottom: 15px;
				}
			</style>
			';
		}

		$this->_html .= '<style>
			#settingsUpload,
			#settingsUpload input {
				display: none;
			}
		</style>
		';


		$settings_form_link = $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&importSettings=1';

		//END Step Links
		$this->_html .= '
		<!-- HIDDEN UPLOAD SETTINGS FORM -->
		<form id="settingsUpload" enctype="multipart/form-data" action="'.$settings_form_link.'" method="POST">
			<input name="settings_file" type="file">
		</form>

		<form action="'.Tools::safeOutput($this->_baseUrl).'&submitSettings" method="post" name="settings_form" enctype="multipart/form-data" id="total_import">
			<fieldset>
			<div class="panel">
			<legend>'.$this->l('Total Import PRO').' <small>v'.$this->version.'</small></legend>';

		$saved_settings = $this->getSavedSettingNames();

		$this->_html .= '
			<div class="separation"></div>
			<div class="row">
			<div class="alert alert-info import_products_categories">
				<p>If you are using this module for the first time, you should run all steps in order from Step 1.</p>
			</div>';
		$this->_html .= '<div class="col-md-6">';

		if (isset($this->context->cookie->tip_success)) {
			$this->_html .= '
			<div class="alert alert-success">
				<p>'.$this->context->cookie->tip_success.'</p>
			</div>
			';
			unset($this->context->cookie->tip_success);
		}

		$this->_html .= '
				<div class="row">
					<label class="control-label col-lg-4" for="settings_groupname">'.$this->l('Load Settings Profile: ').'
					</label>
					<div class="col-lg-7">
					<div class="form-group">
					<select name="settings_group">
					<option value="">Select a profile to load</option>
		';
		foreach ($saved_settings as $setting)
			$this->_html .= '<option value="'.$setting.'">'.$setting.'</option>';

		$this->_html .= '
				</select>

				<span class="help-block">Use Import Profiles to save your import settings</span>
				<a href="#" class="button btn btn-info" id="loadProfile" intercom-tracked="loaded-profile"><span>Load</span></a>
				<a href="#" class="button btn btn-info" id="deleteProfile" intercom-tracked="deleted-profile"><span>Delete</span></a>

				<a href="#" class="button btn btn-success" id="uploadProfile" onclick="startSettingsUpload();return false;"><span>Upload Settings</span></a>

			</div>
			</div>
		</div>';
		$this->_html .= '<div class="form-group">
			<h2>New Import? Start at Step 1!</h2>';

		foreach ($page_buttons as $page => $page_info)
		{
			$this->_html .= '<div class="row">';
			$this->_html .= '<label class="control-label col-md-4">';
			$page_btn_class = 'btn-info';
			if ($page == '1')
				$page_btn_class = 'btn-success';
			$this->_html .= '<a href="'.$page_info['link'].'" class="button btn '.$page_btn_class.'" intercom-tracker="step_navigation_'.$page.'"><span>'.$page_info['button'].'</span></a>';
			$this->_html .= '</label>';
			$this->_html .= '<span style="font-size:12px;">'.$page_info['title'].'</span>';
			$this->_html .= '</div>';
		}
		$this->_html .= '</div></div>';

		$this->_html .= '<div class="col-md-6">';
		$this->_html .= '
		<div class="panel">
			<div class="panel-heading">
				<i class="icon-download"></i>
				Download sample feeds
			</div>

			<div class="list-group">
				<a class="list-group-item _blank" href="'._MODULE_DIR_.$this->name.'/sample_feeds/complete_1_ps.csv" target="_blank" intercom-tracker="sample_feed_downloaded">
					Sample Products file (CSV)
				</a>
				<a class="list-group-item _blank" href="'._MODULE_DIR_.$this->name.'/sample_feeds/complete_1_ps.xml" target="_blank" intercom-tracker="sample_feed_downloaded">
					Sample Products file (XML)
				</a>
			</div>
		</div>';

		$this->_html .= '</div></fieldset></form>';
		$ajax_action = $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name;
		$this->_html .= '
			<script type="text/javascript">
				$("#settingsUpload input").on("change", function (e) {
			      $("#settingsUpload").submit();
			    });

			    function startSettingsUpload() {
			      $("#settingsUpload input").click();
			    }

				$("#deleteProfile").click(function(e)
				{
					if ($(\'[name="settings_group"]\').val() != \'\') {
						var data = $("#total_import").serialize();
						var url = "'.$ajax_action.'";
							$.ajax({
								type: "POST",
								url: url,
								data: \'ajax=true&action=DeleteProfile&\' + data,
								success: function(output)
								{
									$(\'[name="settings_group"] option[value="\' + $(\'[name="settings_group"]\').val() + \'"]\').remove()
									addSave(output);
								}
						});
					}
					e.preventDefault();
					return false;
				});
				$("#loadProfile").click(function(e)
				{
					if ($(\'[name="settings_group"]\').val() != \'\') {
						var data = $("#total_import").serialize();
						var url = "'.$ajax_action.'";
							$.ajax({
								type: "POST",
								url: url,
								data: \'ajax=true&action=LoadProfile&\' + data,
								success: function(output)
								{
									addSave(output);
								}
						});
					}
					e.preventDefault();
					return false;
				});

				function addSave(result)
				{
					$(\'#total_import\').prepend(\'<div class="bootstrap"><div class="module_confirmation conf confirm alert alert-success"><button type="button" class="close" data-dismiss="alert">×</button>\' + result + \'</div></div>\');
				}
			</script>
		';

		return $this->_html;
	}

	/** Render form for Step 1
	 *
	 */
	protected function displayStep1()
	{
		$this->_display = 'step1';

		$this->context->controller->getLanguages();

		//add js links
		$this->context->controller->addJqueryUI('ui.accordion');

		if (version_compare(_PS_VERSION_, '1.6', '<'))
		{
			$this->_html .= '
			<style type="text/css">
				.form-group {
					margin-bottom: 15px;
				}
			</style>
			';
		}

		//arrays for select options
		$feed_source = array(
			array(
				'value' => 'feed_file',
				'label' => $this->l('File Upload'),
			),
			array(
				'value' => 'url',
				'label' => $this->l('URL'),
			),
			array(
				'value' => 'ftp',
				'label' => $this->l('FTP'),
			),
			array(
				'value' => 'filepath',
				'label' => $this->l('File System'),
			),
		);

		$feed_format = array(
			array(
				'value' => 'csv',
				'label' => $this->l('CSV'),
			),
			array(
				'value' => 'xml',
				'label' => $this->l('XML'),
			),
		);

		$file_encodings = array(
			array(
				'value' => 'UTF-8',
				'label' => 'UTF-8',
			),
			array(
				'value' => 'ISO-8859-1',
				'label' => 'ISO-8859-1',
			),
			array(
				'value' => 'ASCII',
				'label' => 'ASCII',
			),
			array(
				'value' => 'GBK',
				'label' => 'GBK',
			),
		);

		$delimiters = array(
			array(
				'value' => ',',
				'label' => ',',
			),
			array(
				'value' => ';',
				'label' => ';',
			),
			array(
				'value' => '\t',
				'label' => $this->l('Tab'),
			),
			array(
				'value' => '|',
				'label' => '|',
			),
			array(
				'value' => '^',
				'label' => '^',
			),
			array(
				'value' => '~',
				'label' => '~',
			),
		);

		//Create forms
		$this->fields_form[0]['form'] = array(
			'step' => $this->_display, //determines which step we're on
			'legend' => array(
				'title' => $this->l('Step 1: Fetch Feed'),
				'image' => _PS_ADMIN_IMG_.'information.png'
			),
			'input' => array(
				//FEED SOURCE
				array(
					'type' => 'select',
					'name' => 'source',
					'id' => 'source',
					'label' => $this->l('Feed Source:'),
					'desc' => $this->l('Choose a feed source'),
					'required' => true,
					'options' => array(
						'query' => $feed_source,
						'id' => 'value',
						'name' => 'label',
					),
				),
				//Feed Source fields start
				array(
					'type' => 'update_text', //for updateText js function
					'input' => 'file', //input type
					'name' => 'feed_file',
					'fields' => array(
						array(
							'label' => $this->l('Import Feed File: '),
							'class' => 'feed_file',
							'name' => $this->l('feed_file'),
							'desc' => $this->l('Max Size: ').ini_get('upload_max_filesize'),
							'id' => 'feed_file',
						)
					),
				),
				array(
					'type' => 'update_text',
					'input' => 'textarea',
					'name' => 'feed_file',
					'fields' => array(
						array(
							'class' => 'url',
							'label' => $this->l('Import Feed URL: '),
							'name' => 'feed_url',
							'rows' => 1,
							'cols' => 60,
							'id' => 'feed_url',
						),
					),
				),
				array(
					'type' => 'update_text',
					'input' => 'textarea',
					'name' => 'ftp',
					'fields' => array(
						array(
							'class' => 'ftp',
							'label' => $this->l('FTP Server: '),
							'name' => 'feed_ftpserver',
							'id' => 'feed_ftpserver',
							'rows' => 1,
							'cols' => 60,
						),
						array(
							'class' => 'ftp',
							'label' => $this->l('Username: '),
							'name' => 'feed_ftpuser',
							'id' => 'feed_ftpuser',
							'rows' => 1,
							'cols' => 60,
						),
						array(
							'class' => 'ftp',
							'label' => $this->l('Password: '),
							'name' => 'feed_ftppass',
							'id' => 'feed_ftppass',
							'rows' => 1,
							'cols' => 60,
						),
						array(
							'class' => 'ftp',
							'label' => $this->l('Absolute path to file: '),
							'name' => 'feed_ftppath',
							'id' => 'feed_ftppath',
							'rows' => 1,
							'cols' => 60,
						),
					),
				),
				//Feed Format fields start
				array(
					'type' => 'update_text',
					'input' => 'textarea',
					'name' => 'filepath',
					'fields' => array(
						array(
							'class' => 'filepath',
							'label' => $this->l('Import Feed Local File Path: '),
							'name' => 'feed_filepath',
							'rows' => 1,
							'cols' => 60,
							'id' => 'feed_filepath',
						),
					),
				),
				//Feed Format fields end
				array(
					'type' => 'select',
					'name' => 'format',
					'id' => 'format',
					'label' => $this->l('Feed Format:'),
					'required' => true,
					'options' => array(
						'query' => $feed_format,
						'id' => 'value',
						'name' => 'label',
					),
				),
				//Feed Source fields start
				array(
					'type' => 'update_text',
					'input' => 'textarea',
					'name' => 'xml',
					'fields' => array(
						array(
							'class' => 'xml',
							'label' => $this->l('XML Product Tag: '),
							'name' => 'feed_xml',
							'rows' => 1,
							'cols' => 20,
							'id' => 'feed_xml',
						),
					),
				),
				array(
					'type' => 'update_text',
					'input' => 'select',
					'name' => 'csv',
					'fields' => array(
						array(
							'class' => 'csv',
							'label' => $this->l('CSV Field Delimiter: '),
							'name' => 'feed_csv',
							'id' => 'feed_csv',
							'options' => array(
								'query' => $delimiters,
								'id' => 'value',
								'name' => 'label',
							),
						),
					),
				),
				//Feed Source end
				//Advanced Settings, include all the advanced fields here
				array(
					'type' => 'advanced',
					'name' => 'advanced',
					'fields' => array(
						array(
							'label' => $this->l('First Row is Headings: '),
							'input' => 'checkbox',
							'name' => 'has_headers',
							'id' => 'has_headers',
							'class' => 'csv',
							'desc' => $this->l('This should be ticked if your CSV has column names in the first row')
						),
						array(
							'label' => $this->l('Use Safe Headings: '),
							'input' => 'checkbox',
							'name' => 'safe_headers',
							'id' => 'safe_headers',
							'class' => 'csv',
							'desc' => $this->l('If there are blank heading rows, or invalid characters in your headings this will use default heading values instead.'),
						),
						array(
							'label' => $this->l('Unzip Feed: '),
							'input' => 'checkbox',
							'name' => 'unzip_feed',
							'id' => 'unzip_feed',
						),
						array(
							'label' => $this->l('File Encoding: '),
							'input' => 'select',
							'name' => 'file_encoding',
							'id' => 'file_encoding',
							'desc' => $this->l('If you are unsure, use UTF-8'),
							'options' => array(
								'query' => $file_encodings,
								'id' => 'value',
								'name' => 'label',
							),
						),
						array(
							'label' => $this->l('Cron Import Fetch: '),
							'input' => 'checkbox',
							'name' => 'cron_feed',
							'id' => 'cron_feed',
							'desc' => $this->l('Recommended for large feeds. Only loads the first 3 products to configure the settings with'),
						),
					),
				),
			),
			'submit' => array(
				'name' => 'submitStep',
				'title' => $this->l('Save & Continue'), //make sure same name as toolbar save btn
				'class' => 'button btn btn-success'
			)
		);

		//post vars
		$post_vars = array(
			'source' => 'file',         'feed_url' => '',           'feed_filepath' => '',
			'feed_ftpserver' => '',     'feed_ftpuser' => '',       'feed_ftppass' => '',
			'feed_ftppath' => '',       'format' => 'csv',          'feed_xml' => '',
			'feed_csv' => ',',          'file_encoding' => 'UTF-8', 'unzip_feed' => 0,
			'has_headers' => 1,         'safe_headers' => 0,        'cron_feed' => 0,
		);

		$settings = unserialize(Configuration::get('IMPORT_STEP1'));
		//for post vars
		foreach ($post_vars as $post_var => $default_val)
		{
			if (isset($settings[$post_var]))
				$this->fields_value[$post_var] = $settings[$post_var];
			else
				$this->fields_value[$post_var] = $default_val;
		}

		//initiate form
		$helper = $this->initForm();
		$helper->step = 'step1';
		$helper->fields_value = isset($this->fields_value) ? $this->fields_value : array();
		$helper->submit_action = 'submitStep1';
		$helper->title = $this->l('Total Import PRO');
		$this->_html .= $helper->generateForm($this->fields_form);

		$ajax_url = $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name;

		$this->_html .= '
		<script type="text/javascript">
		function updateText(el, name)
		{
			var action = el.value;
			if (typeof action != \'undefined\')
			{
				if (name == \'source\')
				{
					$(".feed_file, .url, .filepath, .ftp").hide();
				}
				else
				{
					$(".xml, .csv").hide();
				}
				$("#"+action+", ."+action).show();
			}
		}

		function saveSettings()
		{
			var data = $(\'form.totalimportpro\').serialize();
			var url = \''.$ajax_url.'\';
			$.ajax({
				type: "POST",
				url: url,
				data: \'ajax=true&action=SaveSettings&step=1&\' + data,
				success: function(result)
				{
					addSave(result);
				}
			});
		}

		'.$this->getJsAddSave('Step 1 Settings Saved').'

		$(document).ready(function()
		{
			$(\'#source\').change(function()
			{
				updateText(document.getElementById("source"), \'source\');
			});
			$(\'#format\').change(function()
			{
				updateText(document.getElementById("format"), \'format\');
			});
			updateText(document.getElementById("source"), \'source\');
			updateText(document.getElementById("format"), \'format\');

			$(".accordion").accordion({
				header: \'> div.advanced \',
				collapsible: true, active: false, heightStyle: "content",
			});
		});
		</script>';

	}

	/** Render form for Step 2
	 *
	 */
	protected function displayStep2()
	{
		$this->_display = 'step2';
		$this->context->controller->getLanguages();
		//Add any jquery links
		//$this->context->controller->addJqueryUI();

		//CSS
		$this->_html .= '<style type="text/css">';
		if (version_compare(_PS_VERSION_, '1.6', '<'))
		{
			$this->_html .= '
				li.tree-group {
					min-height: 20px;
				}
				.checkbox label {
					display: inline;
					width: auto;
				}
				#multishop-tree {
					background-color: rgba(0, 0, 0, 0) !important;
				}';
		}
		$this->_html .= '
			/*Multi-shop Trees*/
			.tree-shop{
				padding-left: 15px;
				padding-top: 2px;
				padding-bottom: 2px;
			}
			#multishop-tree ul {
				padding-left: 0;
				list-style-type: none;
			}
		</style>
		';

		$out_of_stock = array(
			array(
				'value' => 1,
				'label' => $this->l('Allow Orders'),
			),
			array(
				'value' => 2,
				'label' => $this->l('Deny Orders'),
			),
			array(
				'value' => 3,
				'label' => $this->l('Default'),
			),
		);

		$options = array(
			array(
				'value' => 'available_order',
				'label' => $this->l('Available for Order'),
			),
			array(
				'value' => 'show_price',
				'label' => $this->l('Show Price'),
			),
			array(
				'value' => 'online_only',
				'label' => $this->l('Online Only (not sold in store)'),
			),
		);

		$visibility = array(
			array(
				'value' => 'both',
				'label' => $this->l('Everywhere'),
			),
			array(
				'value' => 'catalog',
				'label' => $this->l('Catalog Only'),
			),
			array(
				'value' => 'search',
				'label' => $this->l('Search Only'),
			),
			array(
				'value' => 'none',
				'label' => $this->l('Nowhere'),
			),
		);

		$condition = array(
			array(
				'value' => 'new',
				'label' => $this->l('New'),
			),
			array(
				'value' => 'used',
				'label' => $this->l('Used'),
			),
			array(
				'value' => 'refurbished',
				'label' => $this->l('Refurbished'),
			),
		);
		//grab available tax rules
		$no_tax = array(
			array('id_tax_rules_group' => '0', 'name' => 'No Tax')
		);

		$tax_rules_groups = TaxRulesGroup::getTaxRulesGroups();

		$tax_rules = array_merge($no_tax, $tax_rules_groups);

		$discount = array(
			array(
				'value' => 'amount',
				'label' => $this->l('Amount'),
			),
			array(
				'value' => 'percentage',
				'label' => $this->l('Percentage'),
			),
		);

		$matching_fields = array(
			array(
				'value' => 'name',
				'label' => $this->l('Name'),
			),
			array(
				'value' => 'id',
				'label' => $this->l('Product Id'),
			),
			array(
				'value' => 'reference',
				'label' => $this->l('Reference'),
			),
			array(
				'value' => 'upc',
				'label' => $this->l('UPC'),
			),
			array(
				'value' => 'ean13',
				'label' => $this->l('EAN13'),
			),
		);

		$category_match = array(
			array(
				'value' => 'name',
				'label' => $this->l('Name'),
			),
			array(
				'value' => 'id',
				'label' => $this->l('Category Id'),
			),
		);

		$attr_type = array(
			array(
				'value' => 'select',
				'label' => $this->l('Drop-down list'),
			),
			array(
				'value' => 'radio',
				'label' => $this->l('Radio button'),
			),
			array(
				'value' => 'color',
				'label' => $this->l('Color'),
			),
		);

		$stock_labels = array(
			array(
				'value' => '1',
				'label' => $this->l('Increase'),
			),
			array(
				'value' => '5',
				'label' => $this->l('Regulation following an inventory of stock'),
			),
			array(
				'value' => '8',
				'label' => $this->l('Supply Order'),
			),
		);

		//Create Form
		$this->fields_form[0]['form'] = array(
			'step' => $this->_display, //determines which step we're on
			'legend' => array(
				'title' => $this->l('Step 2: Global Settings'),
				'image' => _PS_ADMIN_IMG_.'information.png'
			),
			'input' => array(
				//New Products
				array(
					'type' => 'hint',
					'info' => $this->l('These settings are applied to New Products Only'),
					'name' => 'global_hint',
				),
				//Availability Settings
				array(
					'type' => 'text',
					'label' => $this->l('Availability Settings:'),
					'name' => 'text_available_now',
					'desc' => $this->l('Displayed text when in-stock.'),
				),
				array(
					'type' => 'text',
					'name' => 'text_available_later',
					'label' => $this->l(''),
					'desc' => $this->l('Displayed text when allowed to be back-ordered.'),
				),
				array(
					'type' => 'select',
					'label' => '',
					'name' => 'out_of_stock',
					'desc' => $this->l('When out stock'),
					'options' => array(
						'query' => $out_of_stock,
						'id' => 'value',
						'name' => 'label',
					),
				),
				array(
					'type' => 'text',
					'name' => 'minimal_quantity',
					'label' => $this->l('Minimal Quantity'),
					'desc' => $this->l('The minimum quantity to buy this product (set to 1 to disable this feature).'),
				),
				//product status
				array(
					'type' => 'radio',
					'label' => $this->l('Default Product Status: '),
					'name' => 'product_status',
					'class' => 't',
					'values' => array(
						array(
							'id' => 'status_enabled',
							'value' => 1,
							'label' => $this->l('Enabled')),
						array(
							'id' => 'status_disabled',
							'value' => 0,
							'label' => $this->l('Disabled'),
						),
					),
				),
				//tax rules
				array(
					'type' => 'select',
					'label' => $this->l('Default Tax Rule Group: '),
					'name' => 'id_tax_rules_group',
					'options' => array(
						'query' => $tax_rules,
						'id' => 'id_tax_rules_group',
						'name' => 'name',
					),
				),
				//options
				array(
					'type' => 'checkbox',
					'label' => $this->l('Options: '),
					'name' => 'options',
					'values' => array(
						'query' => $options,
						'id' => 'value',
						'name' => 'label',
					),
				),
				//visibility
				array(
					'type' => 'select',
					'label' => $this->l('Visibility: '),
					'name' => 'visibility',
					'options' => array(
						'query' => $visibility,
						'id' => 'value',
						'name' => 'label',
					),
				),
				//condition
				array(
					'type' => 'select',
					'label' => $this->l('Condition: '),
					'name' => 'condition',
					'options' => array(
						'query' => $condition,
						'id' => 'value',
						'name' => 'label',
					),
				),
				//shops
				array(
					'type' => 'multi_tree',
					'label' => $this->l('Shops: '),
					'name' => 'shops',
					'shop_groups' => Shop::getTree(),
					'desc' => $this->l('Products will be associated with these ticked stores.'),
					'url' => '',
				),
			),
			'submit' => array(
				'name' => 'submitStep',
				'title' => $this->l('Save & Continue'), //make sure same name as toolbar save btn
				'class' => 'button btn btn-success'
			),
		);

		//Field Specific Settings
		$this->fields_form[1]['form'] = array(
			'step' => $this->_display, //determines which step we're on
			'legend' => array(
				'title' => $this->l('Step 2: Global Settings (Optional)'),
				'image' => _PS_ADMIN_IMG_.'information.png'
			),
			'input' => array(
				array(
					'type' => 'hint',
					'info' => $this->l(''),
					'name' => 'global_hint',
				),
				//Download Remote Images
				array(
					'type' => 'radio',
					'label' => $this->l('Download Remote Images: '),
					'name' => 'remote_images',
					'id' => 'remote_images',
					'class' => 't',
					'desc' => 'Downloads remote images from a url',
					'values' => array(
						array(
							'id' => 'remote_enabled',
							'value' => 1,
							'label' => $this->l('Yes')),
						array(
							'id' => 'remote_disabled',
							'value' => 0,
							'label' => $this->l('No'),
						),
					),
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Generate Thumbnails: '),
					'name' => 'thumbnail',
					'id' => 'thumbnail',
					'class' => 't',
					'values' => array(
						array(
							'id' => 'thumbnail_enabled',
							'value' => 1,
							'label' => $this->l('Yes')),
						array(
							'id' => 'thumbnail_disabled',
							'value' => 0,
							'label' => $this->l('No'),
						),
					),
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Delete Images: '),
					'name' => 'delete_images',
					'id' => 'delete_images',
					'class' => 't',
					'desc' => $this->l('Delete a product\'s pre-existing images when updating a product.'),
					'values' => array(
						array(
							'id' => 'image_delete',
							'value' => 1,
							'label' => $this->l('Yes')),
						array(
							'id' => 'image_save',
							'value' => 0,
							'label' => $this->l('No'),
						),
					),
				),
				// array(
				// 	'type' => 'radio',
				// 	'label' => $this->l('Delete Attributes: '),
				// 	'name' => 'delete_attributes',
				// 	'id' => 'delete_attributes',
				// 	'class' => 't',
				// 	'desc' => $this->l('Delete a product\'s pre-existing attributes when updating a product.'),
				// 	'values' => array(
				// 		array(
				// 			'id' => 'attribute_delete',
				// 			'value' => 1,
				// 			'label' => $this->l('Yes')),
				// 		array(
				// 			'id' => 'attribute_save',
				// 			'value' => 0,
				// 			'label' => $this->l('No'),
				// 		),
				// 	),
				// ),
				array(
					'type' => 'select',
					'label' => $this->l('Identify Categories by: '),
					'name' => 'category_match',
					'desc' => $this->l('You can identify existing categories in your store by name or by their category ID.'),
					'options' => array(
						'query' => $category_match,
						'id' => 'value',
						'name' => 'label',
					),
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Remove Categories: '),
					'name' => 'delete_cats',
					'id' => 'delete_cats',
					'class' => 't',
					'desc' => $this->l('Removes categories associated to product prior to adding new category associations.'),
					'values' => array(
						array(
							'id' => 'remove_cat',
							'value' => 1,
							'label' => $this->l('Yes')),
						array(
							'id' => 'save_cat',
							'value' => 0,
							'label' => $this->l('No'),
						),
					),
				),
				//Specific Prices
				array(
					'type' => 'radio',
					'label' => $this->l('Remove Specific Prices (discount): '),
					'name' => 'delete_spec_pri',
					'id' => 'delete_spec_pri',
					'class' => 't',
					'desc' => $this->l('Removes current discounts when updating exsisting products.'),
					'values' => array(
						array(
							'id' => 'remove_spec_pri',
							'value' => 1,
							'label' => $this->l('Yes')),
						array(
							'id' => 'save_spec_pri',
							'value' => 0,
							'label' => $this->l('No'),
						),
					),
				),
				//Discount
				array(
					'type' => 'select',
					'label' => $this->l('Discount: '),
					'name' => 'discount_type',
					'desc' => $this->l('If discount is mapped, select if value is a percentage or amount.'),
					'options' => array(
						'query' => $discount,
						'id' => 'value',
						'name' => 'label',
					),
				),
				//Unity
				array(
					'type' => 'text',
					'label' => $this->l('Unit for Unit Price field: '),
					'name' => 'unity',
					'desc' => $this->l('Example: kg, lb, m, inch, etc.'),
				),
				//Accessories
				array(
					'type' => 'select',
					'label' => $this->l('Accessories Matching Field: '),
					'name' => 'accessory_match',
					'desc' => $this->l('This field will be used to match existing Accessories in your store.'),
					'options' => array(
						'query' => $matching_fields,
						'id' => 'value',
						'name' => 'label',
					),
				),
				array(
					'type' => 'select',
					'label' => $this->l('Product Pack Matching Field: '),
					'name' => 'pack_match',
					'desc' => $this->l('This field will be used to match existing Product Packs in your store.'),
					'options' => array(
						'query' => $matching_fields,
						'id' => 'value',
						'name' => 'label',
					),
				),
				array(
					'type' => 'select',
					'label' => $this->l('Attribute Group Type'),
					'desc' => $this->l('For new Attribute Groups created for Combinations'),
					'name' => 'group_type',
					'options' => array(
						'query' => $attr_type,
						'id' => 'value',
						'name' => 'label',
					),
				),
				array(
					'type' => 'select',
					'label' => $this->l('Stock Movement Label'),
					'desc' => $this->l('For Advanced Warehouse Stock Management'),
					'name' => 'id_stock_mvt_reason',
					'options' => array(
						'query' => $stock_labels,
						'id' => 'value',
						'name' => 'label',
					),
				),
			),
		);

		//post vars
		$post_vars = array(
			'text_available_now' => '',
			'text_available_later' => '',
			'minimal_quantity' => 1,
			'out_of_stock' => 2,
			'product_status' => 1,
			'id_tax_rules_group' => 0,
			'options_available_order' => 0,
			'options_online_only' => 0,
			'options_show_price' => 0,
			'visibility' => 'both',
			'condition' => 'new',
			'discount_type' => 'amount',
			'unity' => 'Kg',
			'group_type' => 'select',
			'remote_images' => 1,
			'delete_images' => 0,
			'delete_attributes' => 0,
			'delete_cats' => 0,
			'delete_spec_pri' => 0,
			'thumbnail' => 1,
			'category_match' => 'name',
			'pack_match' => 'name',
			'accessory_match' => 'name',
			'group_id' => '',
			'id_shop_list' => array(array()),
			'id_stock_mvt_reason' => 1,
		);

		$settings = unserialize(Configuration::get('IMPORT_STEP2'));

		//add post vars
		foreach ($post_vars as $post_var => $default_val)
		{
			if (isset($settings[$post_var]))
				$this->fields_value[$post_var] = $settings[$post_var];
			else
				$this->fields_value[$post_var] = $default_val;
		}

		//for shop values only
		//if

		//initiate form
		$helper = $this->initForm();
		$helper->step = 'step2';
		$helper->fields_value = isset($this->fields_value) ? $this->fields_value : array();
		$helper->submit_action = 'submitStep2';
		$helper->title = $this->l('Total Import PRO');
		$this->_html .= $helper->generateForm($this->fields_form);

		//add JS
		$ajax_url = $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name;
		$this->_html .= '
		<script type="text/javascript">
			function toggleChecked(status, group)
			{
				$(".tree-shop .group" + group).each( function()
				{
					$(this).attr("checked",status)
				})
			}
			function saveSettings()
			{
				var data = $(\'form.totalimportpro\').serialize();
				var url = \''.$ajax_url.'\';
				$.ajax({
					type: "POST",
					url: url,
					data: \'ajax=true&action=SaveSettings&step=2&\' + data,
					success: function(result)
			{
						addSave(result);
					}
				});
			}

			'.$this->getJsAddSave('Step 2 Settings Saved').'

		</script>
		';

	}

	/** Render form for Step 3
	 *
	 */
	protected function displayStep3()
	{
		$this->_display = 'step3';
		$this->context->controller->getLanguages();
		$ajax_url = $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name;
		//Add any jquery links
		//$this->context->controller->addJqueryUI();

		//add CSS
		$this->_html .= '
		<style>
		select.error,
		input.error {
			border: 1px solid red !important;
		}
		.panel {
			overflow: auto;
		}
		.stepInfo {
			padding: 5px;
		}
		#operations td select, #operations td input {
			width: auto !important;
			display: inline-block;
			margin: 0 5px;
		}
		#sampleFields th {
			border-top: 1px solid rgb(221, 211, 211) !important;
			border-left: 1px solid rgb(221, 211, 211) !important;
			border-right: 1px solid rgb(221, 211, 211) !important;
		}

		#sampleFields {
			overflow-x: scroll;
		}
		</style>';

		//grab first row from feed
		$feed_sample = $this->getNextProduct();

		unset($feed_sample['hj_id']);
		$operations = $this->getOperations();
		$this->fields_form[0]['form'] = array(
			'step' => $this->_display, //determines which step we're on
			'legend' => array(
				'title' => $this->l('Step 3: Adjust Import Data'),
				'image' => _PS_ADMIN_IMG_.'information.png'
			),
			'input' => array(
				array(
					'type' => 'feed_sample',
					'name' => 'feed_sample',
					'values' => array(
						'headings' => array_keys($feed_sample), //headings
						'rows' => array_values($feed_sample), //first row from feed

					),
				),
				array(
					'type' => 'operations',
					'name' => 'operations',
					'labels' => array(
						$this->l('Most Popular'),
						$this->l('Advanced'),
					),
					'operations' => $operations,
				),
			),
			'submit' => array(
				'name' => 'submitStep',
				'title' => $this->l('Save & Continue'), //make sure same name as toolbar save btn
				'class' => 'button btn btn-success'
			),
		);

		//Get settings into fields_value format
		$settings = unserialize(Configuration::get('IMPORT_STEP3'));
		if (!empty($settings['adjust']))
		{
			$settings_adjust = array();
			foreach ($settings['adjust'] as $operation)
			{
				//grab input details for selected function from operations list
				$operation_details = ($operations[$operation[0]]);
				$operation_details['values'] = $operation;
				$settings_adjust[] = $operation_details;
			}
		}

		$this->fields_value['adjust'] = '';
		if (isset($settings_adjust))
			$this->fields_value['adjust'] = $settings_adjust;
		$this->fields_value['adjust']['field_list'] = array_keys($feed_sample);

		//initiate form
		$helper = $this->initForm();
		$helper->step = 'step3';
		$helper->submit_action = 'submitStep3';
		$helper->fields_value = $this->fields_value;
		$helper->title = $this->l('Total Import PRO');
		$this->_html .= $helper->generateForm($this->fields_form);

		$ajax_url = $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name;

		//Add JS after form
		//$adjust = (isset($this->fields_value['adjust'])) ? count($this->fields_value['adjust']) : '0';
		//add tools get value for that too
		$adjust = isset($this->fields_value['adjust']) ? count($this->fields_value['adjust']) : 0;
		$operations = Tools::jsonEncode($operations);
		$fields = array_keys($feed_sample);

		$this->_html .= '
		<script type="text/javascript">
		var operation_row = '.$adjust.';
		var operations = '.$operations.';

		function addOperation()
		{
			selected_op = $("#operationToAdd option:selected").val();
			if (operations[selected_op])
			{
				ops = operations[selected_op];
				inputs = ops[\'inputs\'];
				html = \'<tr id="adjustment_row_\' + operation_row + \'">\';
				html += \'<td class="left">\'+ops[\'name\'];
				html += \'<input type="hidden" name="adjust[\' + operation_row + \'][]" value="\' + selected_op + \'"/></td><td class="left">\';
				for (i in inputs)
				{
					if (inputs[i]["prepend"])
					{
					html += \'&nbsp;\' + inputs[i]["prepend"] + \'&nbsp;\';
					}
					if (inputs[i]["type"] == \'text\')
					{
						html += \'<input type="text" name="adjust[\' + operation_row + \'][]" />\';
					}
					else if (inputs[i]["type"] == \'field\')
					{
						html += \'<select name="adjust[\' + operation_row + \'][]">;\';
						html += \'<option value="">'.$this->l('-- Select --').'</option>\';';
		foreach ($fields as $field)
			$this->_html .= ' html += \'<option value="'.$field.'">'.$field.'</option>\';';

		$this->_html .= '
						html += \'</select>\';
					}
					if (inputs[i]["option"] == \'addMore\')
					{
							html += \'<a onclick="removeSelectBefore(this);">x</a>\';
							html += \'<a onclick="return addSub(this);" class="button btn btn-default"><span>More&nbsp;&rarr;&nbsp;</span></a>\';
					}
				}
				html += "</td><td class=\"left\"><a onclick=\"$(\'#adjustment_row_" + operation_row + "\').remove();\" class=\"button btn btn-danger\"> '.$this->l('Remove').'</a></td>";
				html += "</tr>";

				$(\'#operations\').append(html);
					operation_row++;
				}
		}
		function addSub(el)
		{
			sub = $(el).closest(\'.left\').children(\'select\').last().clone();
			$(el).before(sub);
			$(el).before(\'<a class="a" onclick="removeSelectBefore(this);">x</a>\');
			return false;
		}

		function saveSettings()
		{
			var data = $(\'form.totalimportpro\').serialize();
			var url = \''.$ajax_url.'\';
			$.ajax({
				type: "POST",
				url: url,
				data: \'ajax=true&action=SaveSettings&step=3&\' + data,
				success: function(result)
				{
					addSave(result);
				}
			});
		}

		'.$this->getJsAddSave('Step 3 Settings Saved').'

		window.ajaxUrl = \''.$ajax_url.'\';

		</script>';
	}

	/** Render form for Step 4
	 *
	 */
	protected function displayStep4()
	{
		$this->_display = 'step4';

		$this->context->controller->getLanguages();
		//Add any jquery links

		//add CSS
		$this->_html .= '
			<style type="text/css">
			#sampleFields {
				overflow-x: scroll;
			}
			#sampleFields th {
				border: 1px solid rgb(221, 211, 211) !important;
			}
			.info_image{
				vertical-align: middle;
				padding-bottom: 3px;
			}
			.mapping_field {
				width: 25%;
			}
			.source_field {
				width: 70%;
			}
			/* Tabs */
			.fieldsTab {
				float: left;
				margin: 0;
				padding: 10px 0;
				text-align: left;
				min-height: 27px;
				line-height: 16px;
			 }
			.fieldsTab li {
				text-align: left;
				float: left;
				display: inline;
				padding: 5px 8px;
				font-weight: bold;
				cursor: pointer;
				border-bottom: 1px solid #CCCCCC;
			 }
			 .fieldsTab li:hover {
				background-color: #EEE;
			 }
			.fieldsTab li.fieldsTabButton.selected {
				border: none;
				border-left: 1px solid #CCCCCC;
				border-right: 1px solid #CCCCCC;
				border-top: 1px solid #CCCCCC;
			}
			h3 {
				border: none !important;
				margin: 2px 0 !important;
			}
			.tabItem{
				display:none;
			}

			 .tabItem.selected{
				 display: block;
				 padding: 0;
			}
			.stepInfo {
				padding: 5px;
			}
			.panel {
				overflow: auto;
			}
			#full .hori select {
			width: 25%;
		}
			#full select, #simple_update select {
				display: inline-block;
			}
			.combo td {
				position: relative;
			}
			.combo_button {
				position: absolute;
				right: 5px;
				bottom: 5px;
			}
			.lang-select-group {
				display: inline;
			}
			// .combo select {
			// 	display: block !important;
			// }
			</style>
		';

		$feed_sample_map = $this->getNextProduct();

		unset($feed_sample_map['hj_id']);
		$feed_fields = array_keys($feed_sample_map);
		// Fields to map
		$field_map = array(
			//Information
			'name' => $this->l('Name'),
			'reference' => $this->l('Reference'),
			'ean13' => $this->l('EAN13 or JAN'),
			'upc' => $this->l('UPC'),
			'active' => $this->l('Status'),
			'description_short' => $this->l('Short Description'),
			'description' => $this->l('Description'),
			'tag' => array($this->l('Tag'), 'vert'),
			'condition' => $this->l('Condition'),
			'visibility' => $this->l('Visibility'),
			'options' => $this->l('Options'),
			'pack_products' => array($this->l('Pack Product'), 'vert'),

			//Prices
			'wholesale_price' => $this->l('Wholesale Price'),
			'price' => $this->l('Retail Price'),
			'unit_price' => $this->l('Unit Price'),
			'specific_price'=> array($this->l('Specific Price'), 'vert'),
			'id_tax_rules_group' => $this->l('Tax Rule Group'),

			//SEO
			'meta_title' => $this->l('Meta Title'),
			'meta_description' => $this->l('Meta Description'),
			'meta_keywords' => array($this->l('Meta Keywords'),'vert'),
			'link_rewrite' => $this->l('Friendly Url'),

			//Associations
			'category' => array($this->l('Category'), 'cat'),
			'id_manufacturer' => $this->l('Manufacturer'),
			'accessories' => array($this->l('Accessory'), 'vert'),

			//Shipping
			'width' => $this->l('Width'),
			'height' => $this->l('Height'),
			'depth' => $this->l('Depth'),
			'weight' => $this->l('Weight'),
			'additional_shipping_cost' => $this->l('Additional Shipping'),
			'carriers' => array($this->l('Carrier'), 'vert'),

			//Combinations
			'attrib_pair' => array(
								array(
									array($this->l('Attribute Name'), 'attribute'),
									array($this->l('Attribute Value'), 'attribute_value'),
								),
								'combo',
								'Attribute'),
			//'attribute' => array($this->l('Attribute Name'), 'vert'),
			//'attribute_value' => array($this->l('Attribute Value'), 'vert'),
			'combination_reference'=> $this->l('Combination Reference'),
			'combination_ean13' => $this->l('Combination EAN13'),
			'combination_upc' => $this->l('Combination UPC'),
			'combination_weight' => $this->l('Impact Weight'),
			'combination_quantity' => $this->l('Quantity'),
			'combination_wholesale_price' => $this->l('Product Cost'),
			'combination_price' => $this->l('Impact on Price'),
			'combination_unit_price_impact' => $this->l('Impact Unit Price'),
			'combination_specific_price'=> array($this->l('Specific Price'), 'vert'),
			'combination_image'=> $this->l('Image'),
			'combination_minimal_quantity' => $this->l('Minimum Quantity'),
			'combination_default_on'=> $this->l('Default'),
			'combination_supplier' => $this->l('Combination Supplier'),

			//Quantities
			'quantity' => $this->l('Quantity'),
			'available_now' => $this->l('Text in Stock'),
			'available_later' => $this->l('Text Back Order'),
			'minimal_quantity' => $this->l('Minimal Quantity'),

			//Images
			'cover_image' => $this->l('Cover Image'),
			'image' => array($this->l('Image'), 'vert'),

			//Features
			'feature' => array($this->l('Feature'), 'vert'),

			//Customization
			'custom_file' => array($this->l('File Field'),'vert'),
			'custom_text' => array($this->l('Text Field'), 'vert'),

			//Attachments
			'attachment' => array($this->l('Attachment'),'vert'),

			//Suppliers
			'def_supplier' => $this->l('Default Supplier'),
			'supplier' => array($this->l('Supplier'), 'vert'),

			//Virtual Product/Download
			'file' => $this->l('File'),
			'filename'=> $this->l('Filename'),
			'number_downloads' => $this->l('Number of Downloads'),
			'expiration_date' => $this->l('Expiration Date'),
			'number_days' => $this->l('Number of Days'),

			//Advanced Stock Management
			'warehouse' => array($this->l('Warehouse'), 'vert'),
			'physical_quantity' => $this->l('Quantity to Add'),
		);

		$tab_fields = array(
			'Information' => array(
				'name',
				'reference',
				'ean13',
				'upc',
				'active',
				'description_short',
				'description',
				'tag',
				'condition',
				'visibility',
				'options',
				'pack_products',
			),
			'Prices' => array(
				'wholesale_price',
				'price',
				'unit_price',
				'specific_price',
				'id_tax_rules_group',
			),
			'SEO' => array(
				'meta_title',
				'meta_description',
				'meta_keywords',
				'link_rewrite',
			),
			'Associations' => array(
				'category',
				'id_manufacturer',
				'accessories',
			),
			'Shipping' => array(
				'width',
				'height',
				'depth',
				'weight',
				'additional_shipping_cost',
				'carriers',
			),
			'Combinations' => array(
				'attrib_pair',
				//'attribute',
				//'attribute_value',
				'combination_reference',
				'combination_ean13',
				'combination_upc',
				'combination_quantity',
				'combination_price',
				'combination_wholesale_price',
				'combination_unit_price_impact',
				'combination_weight',
				'combination_minimal_quantity',
				'combination_specific_price',
				'combination_image',
				'combination_default_on',
				'combination_supplier',
			),
			'Quantities' => array(
				'quantity',
				'available_now',
				'available_later',
				'minimal_quantity',
			),
			//append : for position, combination image, cover image, description image
			'Images' => array(
				'cover_image',
				'image',
			),
			'Features' => array(
				'feature',
			),
			'Customization' => array(
				'custom_file',
				'custom_text',
			),
			'Attachments' => array(
				'attachment',
			),
			'Suppliers' => array(
				'def_supplier',
				'supplier',
			),
			'Download' => array(
				'file',
				'filename',
				'number_downloads',
				'expiration_date',
				'number_days',
			),
			'Warehouse' => array(
				'warehouse',
				'physical_quantity',
			),
		);

		$multi_language_fields = array(
			'name',
			'description_short',
			'description',
			'meta_title',
			'meta_description',
			'meta_keywords',
			'tag',
			'category',
			'custom_file',
			'custom_text',
			'link_rewrite',
			'feature',
			'available_now',
			'available_later',
			'attribute',
			//'attribute_value',
			'attrib_pair',
			'attachment',
			//'carriers',
		);

		$this->languages = Language::getLanguages(false);

		//Simple Fields
		$matching_fields = array(
			'name',
			'reference',
			'ean13',
			'upc',
		);
		$simple_fields = array(
			'quantity',
			'active',
			'wholesale_price',
			'price',
			'specific_price',
		);

		$this->fields_form[0]['form'] = array(
			'step' => $this->_display, //determines which step we're on
			'legend' => array(
				'title' => $this->l('Step 4: Field Mapping'),
				'image' => _PS_ADMIN_IMG_.'information.png'
			),
			'input' => array(
				array(
					'type' => 'feed_sample_map',
					'name' => 'feed_sample_map',
					'values' => array(
						'headings' => array_keys($feed_sample_map), //headings
						'rows' => array_values($feed_sample_map), //first row from feed
					),
				),
				array(
					'type' => 'tabs',
					'name' => 'tabs',
					'field_map' => $field_map,
					'tab_fields' => $tab_fields,
					'simple' => $simple_fields,
					'matching' => $matching_fields,
					'feed_fields' => $feed_fields,
					'multi_language_fields' => $multi_language_fields,
					'languages' => $this->languages,
				),
			),
			'submit' => array(
				'name' => 'submitStep',
				'title' => $this->l('Save & Continue'), //make sure same name as toolbar save btn
				'class' => 'button btn btn-success'
			),
		);

		//Get settings into fields_value format
		$settings = unserialize(Configuration::get('IMPORT_STEP4'));

		// Make sure there is no bad data from previous runs in category list
		$settings = $this->cleanCategories($settings, $feed_fields);

		if (isset($settings))
			$this->fields_value = $settings;
		else
			$this->fields_value = $_POST;

		//initiate form
		$helper = $this->initForm();
		$helper->step = 'step4';
		$helper->fields_value = $this->fields_value;
		$helper->submit_action = 'submitStep4';
		$helper->title = $this->l('Total Import PRO');
		$this->_html .= $helper->generateForm($this->fields_form);

		//Add JS after form
		$ajax_url = $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name;
		$this->_html .= '
		 <script type="text/javascript"><!--

			if ($(\'#simple option:selected\').text() == \'No\')
			{
				$(\'#full\').show();
				$(\'#full\').attr("disabled",false);
				$(\'#simple_update\').hide();
				$(\'#simple_update\').attr("disabled",true);
			} else {
				$(\'#simple_update\').show();
				$(\'#simple_update\').attr("disabled",false);
				$(\'#full\').hide();
				$(\'#full\').attr("disabled",true);
			}

			function updateText(el, name)
			{
				var action = el.value;
				var simple = ["Matching"];
				var full = ["Prices", "SEO", "Associations", "Shipping", "Combinations", "Quantities", "Images", "Features", "Customization", "Attachments", "Suppliers", "Warehouse"];
				if (name == \'simple\')
				{
					if ( action == 1)
					{
						$(\'#simple_update\').show();
						$(\'#simple_update\').attr("disabled",false);
						$(\'#full\').hide();
						$(\'#full\').attr("disabled",true);

						$("#fieldsTabSimpleSheet").addClass("selected");
						$("#fieldsTabSimple").addClass("selected");

						//remove selection for rest of them
						$.each(simple, function( index, value )
						{
							$("#fieldsTab" + value).removeClass("selected");
							$("#fieldsTab" + value + "Sheet").removeClass("selected");
						});

					} else {
						$(\'#full\').show();
						$(\'#full\').attr("disabled",false);
						$(\'#simple_update\').hide();
						$(\'#simple_update\').attr("disabled",true);

						$("#fieldsTabInformationSheet").addClass("selected");
						$("#fieldsTabInformation").addClass("selected");
						//remove selection for rest of them
						$.each(full, function( index, value )
						{
							$("#fieldsTab" + value).removeClass("selected");
							$("#fieldsTab" + value + "Sheet").removeClass("selected");
						});
					}
				}
			}

			function saveSettings()
			{
				var data = $(\'form.totalimportpro\').serialize();
				var url = \''.$ajax_url.'\';
				$.ajax({
					type: "POST",
					url: url,
					data: \'ajax=true&action=SaveSettings&step=4&\' + data,
					success: function(result)
					{
						addSave(result);
					}
				});
			}

			'.$this->getJsAddSave('Step 4 Settings Saved').'

			$(document).ready(function()
			{
				$(".fieldsTabButton").click(function ()
				{
					$(".fieldsTabButton.selected").removeClass("selected");
					$(this).addClass("selected");
					$(".tabItem.selected").removeClass("selected");
					$("#" + this.id + "Sheet").addClass("selected");
				});
			});

			window.ajaxUrl = \''.$ajax_url.'\';
			</script>
		';
	}

	private function cleanCategories($settings, $include_array = array())
	{
		$categories = isset($settings['field_names']) ? $settings['field_names'] : '';
		if (!empty($categories['category']))
		{
			$categories = $categories['category'];
			$cat_lang_keys = array_keys($categories);
			$first_lang = reset($cat_lang_keys);
			// Number of Categories
			$cat_count = count($categories[$first_lang]);
			for ($cat_num = 0; $cat_num < $cat_count; $cat_num++)
			{
				$delete_cat = true;
				foreach ($cat_lang_keys as $lang_key)
				{
					if (!empty($categories[$lang_key][$cat_num][0]))
					{
						// If the include array isn't empty, check if item is in it
						if ((!empty($include_array) && in_array($categories[$lang_key][$cat_num][0], $include_array)) || empty($include_array))
						{
							$delete_cat = false;
							break;

						}
					}
				}
				if ($delete_cat)
				{
					foreach ($cat_lang_keys as $lang_key)
					{
						unset($settings['field_names']['category'][$lang_key][$cat_num]);
						if (empty($settings['field_names']['category'][$lang_key]))
						{
							// Unset the language array if there are no categories saved
							unset($settings['field_names']['category'][$lang_key]);
						}
					}
				}
			}
		}
		return $settings;
	}

	/** Render form for Step 5
	 *
	 */
	protected function displayStep5()
	{
		$this->_display = 'step5';
		$this->context->controller->getLanguages();

		//add any CSS
		$settings = unserialize(Configuration::get('IMPORT_STEP5'));
		$post_range = Tools::getValue('import_range');

		if (isset($post_range))
			$import_range = $post_range;
		elseif (isset($settings['import_range']))
			$import_range = $settings['import_range'];

		$this->_html .= "
			<style>
				.range-container label {
					display: inline;
					width: auto;
				}
				.non_reset {
					overflow: auto;
				}
				.range_input {
					width: 16.6% !important;
					display: inline-block !important;
					margin-left: 5px;
					margin-right: 5px;
				}
			</style>

			<link rel=\"stylesheet\" href=\"//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css\">
			<script src=\"//code.jquery.com/ui/1.10.4/jquery-ui.js\"></script>
			<script type=\"text/javascript\">
			function updateText()
			{
				$(\".non_reset\").show();
				var reset = $('#reset_store').val();
				if (reset == '1')
				{
					$(\".non_reset\").hide();
				}
			}
			function enableRange(check)
			{
				if (check)
				{
					$(\"#import_range_start\").removeAttr(\"disabled\");
					$(\"#import_range_end\").removeAttr(\"disabled\");
				} else {
					$(\"#import_range_start\").attr(\"disabled\",true);
					$(\"#import_range_end\").attr(\"disabled\",true);
				}
			}

			$(document).ready(function()
			{
				updateText();
			 ";
		if (isset($import_range) && $import_range == 'partial')
			$this->_html .= 'enableRange(true);';
		elseif (isset($import_range) && $import_range == 'all')
			$this->_html .= 'enableRange(false);';
		else
		{
			$this->_html .= 'if ($(\'input:checked\').val() == \'all\') {
				enableRange(false);
			} else {
				enableRange(true);
			}';
		}
		$this->_html .= '
			});
			</script>
		';

		$reset = array(
			array(
				'value' => 0,
				'label' => $this->l('No'),
			),
			array(
				'value' => 1,
				'label' => $this->l('Yes'),
			),
		);

		$new_items = array(
			array(
				'value' => 'add',
				'label' => $this->l('Add'),
			),
			array(
				'value' => 'skip',
				'label' => $this->l('Skip'),
			),
		);

		$existing_items = array(
			array(
				'value' => 'update',
				'label' => $this->l('Update'),
			),
			array(
				'value' => 'skip',
				'label' => $this->l('Skip'),
			),
		);

		$matching_fields = array(
			array(
				'value' => 'name',
				'label' => $this->l('Name'),
			),
			array(
				'value' => 'reference',
				'label' => $this->l('Reference'),
			),
			array(
				'value' => 'upc',
				'label' => $this->l('UPC'),
			),
			array(
				'value' => 'ean13',
				'label' => $this->l('EAN13 or JAN'),
			),
		);

		$combination_fields = array(
			array(
				'value' => 'none',
				'label' => $this->l('None'),
			),
			array(
				'value' => 'reference',
				'label' => $this->l('Combination Reference'),
			),
			array(
				'value' => 'upc',
				'label' => $this->l('Combination UPC'),
			),
			array(
				'value' => 'ean13',
				'label' => $this->l('Combination EAN13 or JAN'),
			),
		);

		$store_items = array(
			array(
				'value' => 'ignore',
				'label' => $this->l('Ignore'),
			),
			array(
				'value' => 'delete',
				'label' => $this->l('Delete'),
			),
			array(
				'value' => 'disable',
				'label' => $this->l('Disable'),
			),
			array(
				'value' => 'zero_quantity',
				'label' => $this->l('Quantity to Zero'),
			),
		);

		$this->fields_form[0]['form'] = array(
			'step' => $this->_display, //determines which step we're on
			'legend' => array(
				'title' => $this->l('Step 5: Global Settings'),
				'image' => _PS_ADMIN_IMG_.'information.png'
			),
			'input' => array(
				array(
					'type' => 'select',
					'label' => $this->l('Reset Store:'),
					'name' => 'reset_store',
					'desc' => $this->l('This will remove all products and related fields from your store.'),
					'options' => array(
						'query' => $reset,
						'id' => 'value',
						'name' => 'label',
					),
					'onchange' => 'updateText();',
				),
				array(
					'type' => 'import_select',
					'class' => 'non_reset',
					'title' => $this->l('Reset Combinations/Attributes:'),
					'desc' => $this->l('This will delete all existing combinations and attributes in your store.'),
					'name' => 'reset_combinations',
					'options' => array(
						'query' => $reset,
						'id' => 'value',
						'name' => 'label',
					),
				),
				array(
					'type' => 'import_select',
					'title' => $this->l('New Products:'),
					'name' => 'new_items',
					'desc' => $this->l('New products can be added to your store or skipped.'),
					'options' => array(
						'query' => $new_items,
						'id' => 'value',
						'name' => 'label',
					),
				),
				array(
					'type' => 'import_select',
					'title' => $this->l('Existing Products:'),
					'name' => 'existing_items',
					'desc' => $this->l('Existing products in your store can be updated or skipped.'),
					'options' => array(
						'query' => $existing_items,
						'id' => 'value',
						'name' => 'label',
					),
				),
				array(
					'type' => 'import_select',
					'title' => $this->l('Identify Existing Products by Matching Field:'),
					'desc' => $this->l('Check if a product exists in your store by matching this field.'),
					'name' => 'update_field',
					'options' => array(
						'query' => $matching_fields,
						'id' => 'value',
						'name' => 'label',
					),
				),
				array(
					'type' => 'import_select',
					'class' => 'non_reset',
					'title' => $this->l('Products in store but not in file:'),
					'desc' => $this->l('If your product feed contains a list of all products that should be in your store, use this setting to delete anything not in your file after your import completes.'),
					'name' => 'delete_diff',
					'options' => array(
						'query' => $store_items,
						'id' => 'value',
						'name' => 'label',
					),
				),
				array(
					'type' => 'import_select',
					'title' => $this->l('Identify Existing Combinations by Matching Field:'),
					'desc' => $this->l('Check if a combination exists in your store by matching this field.'),
					'name' => 'combination_field',
					'options' => array(
						'query' => $combination_fields,
						'id' => 'value',
						'name' => 'label',
					),
				),
				array(
					'type' => 'range',
					'id' => 'import_range_all',
					'title' => $this->l('Items to Import:'),
					'desc' => $this->l('Import only part of your product feed eg: From 1 to 100 will import the first 100 items in your file'),
					'name' => 'import_range',
					'all' => array(
						'id'    => 'import_range_all',
						'value' => 'all',
						'label' => $this->l('All'),
					),
					'partial' => array(
						'id'    => 'import_range_partial',
						'value' => 'partial',
						'label' => $this->l('Range'),
						'values' => array(
							'start' => array(
								'input' => 'text',
								'name' => 'import_range_start',
								'id' => 'import_range_start',
								'prepend' => $this->l('- From product')
							),
							'end' => array(
								'input' => 'text',
								'name' => 'import_range_end',
								'id' => 'import_range_end',
								'prepend' => 'to',
							),
						),
					),
				),
				array(
					'type' => 'import_select',
					'id' => 'validate_product',
					'title' => $this->l('Auto-validate:'),
					'name' => 'validate_product',
					'desc' => $this->l('
						This will automatically modify or remove product fields that do not pass PrestaShop\'s validation standards.
						Selecting "No" will skip any product with invalid fields.
					'),
					'options' => array(
						'query' => $reset,
						'id' => 'value',
						'name' => 'label',
					),
				),
				array(
					'type' => 'text',
					'id' => 'ajax_limit',
					'label' => $this->l('Maximum products to read on each import step:'),
					'name' => 'ajax_limit',
					'desc' => $this->l('
						Default Value: 100. If your server is timing out or you experience any memory issues then set this value lower.
					'),
				),
			),
		);

		if (HJ_DEV)
		{
			$this->fields_form[0]['form']['submit'] = array(
				'name' => 'submitStep',
				'title' => $this->l('Save & Import'), //make sure same name as toolbar save btn
				'class' => 'button btn btn-default'
			);
		}

		$post_vars = array(
			'new_items' => '',
			'reset_store' => '',
			'reset_combinations' => 0,
			'existing_items' => '',
			'update_field' => '',
			'combination_field' => 'none',
			'delete_diff' => '',
			'import_range' => 'all',
			'import_range_start' => 1,
			'import_range_end' => 100,
			'validate_product' => 1,
			'ajax_limit' => 100,
		);

		//add post vars
		foreach ($post_vars as $post_var => $default_val)
		{
			if (isset($settings[$post_var]))
				$this->fields_value[$post_var] = $settings[$post_var];
			else
				$this->fields_value[$post_var] = $default_val;
		}

		//initiate form
		$helper = $this->initForm();
		$helper->step = 'step5';
		$helper->fields_value = isset($this->fields_value) ? $this->fields_value : array();
		$helper->submit_action = 'submitStep5';
		$helper->title = $this->l('Total Import PRO');
		$this->_html .= $helper->generateForm($this->fields_form);

		//$ajax_url = _PS_BASE_URL_.__PS_BASE_URI__.'modules/totalimportpro/ajax-call.php';
		$ajax_url = $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name;
		$total_products = $this->getProductRows();

		//redirect to product page
		$redirect = $this->context->link->getAdminLink('AdminProducts');
		$this->_html .= '
		<style>
		.ui-progressbar-value {
			background-image: none;
			background-color: #428bca;
		}
		.progress-label {
			text-shadow: 1px 1px 0 #fff;
		}
		#import_info {
			padding: 5px 0;
		}
		.progress-wrap {
			padding: 20px;
		}
		</style>
		<script type="text/javascript">
			var importing = false;
			$(\'#ajaxImportBtn\').on(\'click\', function () {
				importing = true;
			});

			// Settings shouldnt be changed while an import is running
			$("[name=\'delete_diff\'], [name=\'update_field\'], [name=\'existing_items\'], [name=\'new_items\'], [name=\'combination_field\'], [name=\'validate_product\'], [name=\'reset_store\'], [name=\'reset_combinations\']").on(\'mousedown\', function (e) {
				if (importing)
					e.preventDefault();
			});

			function ajaxImport()
			{
				// Don\'t import if import has started
				if (!importing) {
					Intercom("trackEvent", "run-import");
					// Regular import if delete_diff setting not default
					// Delete diff should not happen if store is being reset
					var store_reset = $(\'#reset_store\').val() == 1;
					if (store_reset) {
						Intercom("trackEvent", "reset-store");
					}
					if ($(\'[name="delete_diff"]\').val() != "ignore") {
						Intercom("trackEvent", "delete-prods-not-in-file");
					}

					if ('.HJ_DEV.') {
						$(\'form.totalimportpro\').append(\'<input type="hidden" name="submitStep" value="1">\')
						$(\'form.totalimportpro\').submit();
					} else {
						$("#import_range_start").attr("disabled", true)
						$("#import_range_end").attr("disabled", true)
						$("#import_range_all").attr("disabled", true)
						$("#import_range_partial").attr("disabled", true);
						$(".progress-wrap").remove();';
			if (version_compare(_PS_VERSION_, '1.6', '<'))
				$this->_html .= '$(\'legend\')';
			else
				$this->_html .= '$(\'.panel-heading\')';
			$this->_html .= '.after(\'<div class="progress-wrap" style="display:none"><div id="progressbar"></div><div id="import_info"><span class="progress-label">Importing... </span><span>Added: <span id="prod_added">0</span> Updated: <span id="prod_updated">0</span></span></div></div>\');
						$(\'.progress-wrap\').slideDown();
						var current = 0;
						if ($(\'#import_range_partial\').is(\':checked\')) {
							current = parseInt($(\'#import_range_start\').val() - 1); // first prod is 0, not 1
							current = Math.max(0, current);
						}
						$( "#progressbar" ).progressbar({
							value: 0,
							complete: function()
							{
								$(".progress-label").text("Complete! ");
								showAlert(\'Success: \'+parseInt($(\'#prod_updated\').text())+\' products updated, \'+parseInt($(\'#prod_added\').text())+\' products added. Now running post-import cleanup..\');
								$.ajax({
									url: \''.$ajax_url.'\',
									type: \'POST\',
									dataType: \'json\',
									data: {
										ajax: true,
										action: \'ImportEnd\',
										delete_diff: $(\'[name="delete_diff"]\').val(),
										update_field: $(\'[name="update_field"]\').val(),
										total_update: $(\'#prod_update\').text(),
										total_added: $(\'#prod_add\').text(),
										reset_products: $(\'[name="reset_products"]\').is(\':checked\'),
									},
									success: function(json) {
										if (typeof json[\'affected_products\'] !== \'undefined\') {
											verb = \'affected\';
											switch ($(\'[name="delete_diff"]\').val()) {
												case \'delete\':
													verb = \'deleted\';
													break;
												case \'zero_quantity\':
													verb = \'set to quantity zero\';
													break;
												case \'disable\':
													verb = \'disabled\';
													break;
											}
											showAlert(json[\'affected_products\'] + \' items in store but not in file have been \' + verb + \'.\');
										}
										showAlert(\'Post-import cleanup finished.\');
									},
									error: function(xhr, ajaxOptions, thrownError) {
										if (xhr.responseText) {
											showAlert(\'The server returned an error during the post import cleanup. If you believe this is a bug then get in touch and attach the following error message:<br />\'+ xhr.responseText, \'warning\');
										} else {
											showAlert("Didn\'t recieve response from server, 500 Internal Server Error", "warning");
										}
									}
								});
							}
						});
						progress(current, 1);
					}
				}
			}

			function showAlert(msg, alert_type) {
				alert_type = typeof alert_type !== \'undefined\' ? alert_type : \'success\';

				$(\'form.totalimportpro\').prepend(\'<div class="bootstrap"><div class="module_confirmation conf confirm alert alert-\' + alert_type + \'"><button type="button" '.((version_compare(_PS_VERSION_, '1.6', '<'))?'style="float:right;cursor:pointer;background:transparent;border:0;" onclick="$(\\\'.alert-success\\\').hide();" ':'').'class="close" data-dismiss="alert">×</button>\' + msg + \'</div></div>\');
			}

			function progress(current, first_run){
				first_run = typeof first_run !== \'undefined\' ? 1 : 0;

				var data = $(\'form.totalimportpro\').serialize();
				var url = "'.$ajax_url.'";
				var total_products = "'.$total_products.'";
				if ($(\'#import_range_partial\').is(\':checked\')) {
					total_products = parseInt($(\'#import_range_end\').val());
					// Watch out for negative numbers, or end lower than start
					total_products = Math.max(0, total_products, parseInt($(\'#import_range_start\').val()));
				}
				var total = 0;
				$.ajax({
					type: "POST",
					url: url,
					dataType: "json",
					data: \'ajax=true&action=Import&prod_num=\' + current + \'&total_prod=\' + total_products + \'&\' + data + (first_run ? \'&first_run=1\' : \'\'),
					success: function(result)
					{
						$("#prod_added").text(result[\'added\'] + parseInt($("#prod_added").text()));
						$("#prod_updated").text(result[\'updated\'] + parseInt($("#prod_updated").text()));
						var progressbar = $("#progressbar");
						current = parseInt(current) + parseInt(result[\'limit\']);
						total = (current/total_products)*100;
						$("#progressbar").progressbar("value", total);
						if (current < total_products)
							progress(current);
						else
						{
							importing = false;
							if (!$(\'#import_range_all\').is(\':checked\')) {
								$("#import_range_start").removeAttr("disabled", "disabled");
								$("#import_range_end").removeAttr("disabled", "disabled");
							}
							$("#import_range_partial").removeAttr("disabled", "disabled");
							$("#import_range_all").removeAttr("disabled", "disabled");
							$(\'#import_info\').append(" - <a href=\''.$redirect.'\'>View Products</a>");
						}
					},
					error: function(xhr, ajaxOptions, thrownError) {
						$(".progress-label").text("Error! ");
						showAlert("The server returned an error. If you believe this is a bug then get in touch and attach the following error message:<br />" + xhr.responseText, "warning");
						importing = false;
						enableInputs();
						console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			}

			function enableInputs() {
				if (!$(\'#import_range_all\').is(\':checked\')) {
					$("#import_range_start").removeAttr("disabled", "disabled");
					$("#import_range_end").removeAttr("disabled", "disabled");
				}
				$("#import_range_partial").removeAttr("disabled", "disabled");
				$("#import_range_all").removeAttr("disabled", "disabled");
			}

			function saveSettings(profile)
			{
				var data = $(\'form.totalimportpro\').serialize();
				var url = "'.$ajax_url.'";
				$.ajax({
					type: "POST",
					url: url,
					data: \'ajax=true&action=SaveSettings&step=5&profile=\'+ profile + \'&\' + data,
					success: function(result)
					{
						addSave(result);
					}
				});
			}

			function settingsProfile()
			{
				$("#settings_profile").dialog({
					draggable: true,
					modal: true,
					resizable: false,
					buttons: [
						{
						  text: "OK",
						  click: function()
							{
							var profile = $("#save_settings_name").get(0).value;
							saveSettings(profile);
							showAlert(\'Saved Profile: \'+profile);
							Intercom("trackEvent", "saved-profile");

							$(this).dialog("close");
						  }
						}
					]
				});
			}

			function showAlert(text)
			{
				$(\'#configuration_form\').prepend(\'<div class="bootstrap"><div class="module_confirmation conf confirm alert alert-success"><button type="button" '.((version_compare(_PS_VERSION_, '1.6', '<'))?'style="float:right;cursor:pointer;background:transparent;border:0;" onclick="$(\\\'.alert-success\\\').hide();" ':'').'class="close" data-dismiss="alert">×</button>\'+text+\'</div></div>\');
			}'.$this->getJsAddSave('Step 5 Settings Saved').'
		</script>';
	}

	/*Create the form for the step
	 *
	 */
	private function initForm()
	{
		$helper = new HelperForm();
		$helper->module = $this;
		$helper->name_controller = 'totalimportpro';
		$helper->identifier = $this->identifier;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->languages = $this->context->controller->_languages;
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		$helper->default_form_language = $this->context->controller->default_form_language;
		$helper->allow_employee_form_lang = $this->context->controller->allow_employee_form_lang;
		$helper->show_toolbar = true;
		$helper->toolbar_scroll = true;
		if (version_compare(_PS_VERSION_, '1.6', '<'))
			$helper->toolbar_btn = $this->initToolbar();
		else
			$this->initToolbar();

		return $helper;
	}

	/*Add toolbar for each template (settings, step1, step2, step3, step4, step5)
	 *
	 */
	private function initToolbar()
	{
		$current_index = AdminController::$currentIndex;
		$token = Tools::getAdminTokenLite('AdminModules');

		$export_settings_link = $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&exportSettings=1';

		$back = $this->_baseUrl;
		switch ($this->_display)
		{
			case 'step1':
				$back = str_replace('&step=1', '', $current_index.'&configure='.$this->name.'&token='.$token);
				$stepnum = 1;
				break;
			case 'step2':
				$back = str_replace('&step=2', '', $current_index.'&configure='.$this->name.'&token='.$token);
				$stepnum = 2;
				break;
			case 'step3':
				$back = str_replace('&step=3', '', $current_index.'&configure='.$this->name.'&token='.$token);
				$stepnum = 3;
				break;
			case 'step4':
				$back = str_replace('&step=4', '', $current_index.'&configure='.$this->name.'&token='.$token);
				$stepnum = 4;
				break;
			case 'step5':
				$back = str_replace('&step=5', '', $current_index.'&configure='.$this->name.'&token='.$token);
				if (version_compare(_PS_VERSION_, '1.6', '<'))
				{
					$this->toolbar_btn['back'] = array(
						'href' => '',
						'title' => $this->l('Back'),
						'desc' => $this->l('Back'),
						'js' => 'javascript:window.location.href=\''.$back.'&step=4\';return false;',
						'imgclass' => 'back',
					);
					$this->toolbar_btn['import'] = array(
						'href' => '',
						'id' => 'ajaxImportBtn',
						'js' => 'javascript:ajaxImport();return false;',
						'title' => $this->l('Import'),
						'desc' => $this->l('Import'),
						'imgClass' => 'import'
					);
					$this->toolbar_btn['save_settings'] = array(
						'title' => $this->l('Save Settings Profile'),
						'href' => '',
						'js' => 'javascript:settingsProfile();return false;', //onclick
						'desc' => $this->l('Save Settings Profile'),
						'imgclass' => 'save-and-stay',
					);
					$this->toolbar_btn['export_settings'] = array(
						'title' => $this->l('Export Settings'),
						'href' => $export_settings_link,
						'desc' => $this->l('Export Settings Profile'),
						'imgclass' => 'save-and-stay',
					);
					$this->toolbar_btn['cancel'] = array(
						'href' => '',
						'js' => 'javascript:window.location.href = \''.$back.'\';return false;',
						'title' => $this->l('Cancel'),
						'desc' => $this->l('Cancel'),
						'icon' => 'cancel'
					);
				}
				$this->fields_form[0]['form']['buttons'] = array(
					array(
						'href' => '',
						'js' => 'javascript:window.location.href = \''.$back.'&step=4\';',
						'title' => $this->l('Back'),
						'desc' => $this->l('Back'),
						'class' => 'btn-reset btn-info',
						'icon' => 'process-icon-back'
					),
					array(
						'href' => $export_settings_link,
						'title' => $this->l('Export Settings'),
						'desc' => $this->l('Export Settings Profile'),
						'class' => 'btn-reset btn-info',
						'icon' => 'process-icon-download',
					),
					array(
						'href' => '',
						'id' => 'ajaxImportBtn',
						'js' => 'javascript:ajaxImport();return false;',
						'title' => $this->l('Run Import'),
						'desc' => $this->l('Run Import'),
						'class' => 'btn-reset btn-success',
						'icon' => 'process-icon-import'
					),
					array(
						'title' => $this->l('Save Settings Profile'),
						'href' => '',
						'js' => 'javascript:settingsProfile();return false;', //onclick
						'desc' => $this->l('Save Settings Profile'),
						'class' => 'btn-reset btn-info',
						'icon' => 'process-icon-save'
					),
					array(
						'href' => '',
						'js' => 'javascript:window.location.href = \''.$back.'\'',
						'title' => $this->l('Cancel'),
						'class' => 'btn-reset btn-danger intercom-tracked i-t:step-canceled',
						'icon' => 'process-icon-cancel'
					)
				);
				if (HJ_DEV)
				{
					$this->toolbar_btn['save'] = array(
						'href' => '',
						'title' => $this->l('Save & Import'),
						'desc' => $this->l('Import'),
						//'js' => '$(\'form.totalimportpro\').submit();', //onclick
					);
				}
				break;
			default:
				break;
		}
		if (!isset($this->fields_form[0]['form']['buttons']))
		{
			if (version_compare(_PS_VERSION_, '1.6', '<'))
			{
				$this->toolbar_btn['save_settings'] = array(
					'title' => $this->l('Save Settings'),
					'href' => '',
					'js' => 'javascript:saveSettings();return false;', //onclick
					'desc' => $this->l('Save Settings'),
					'imgclass' => 'save',
				);
				$this->toolbar_btn['back'] = array(
					'href' => '',
					'title' => $this->l('Back'),
					'desc' => $this->l('Back'),
					'js' => 'javascript:window.location.href=\''.$back.'&step='.($stepnum - 1).'\';return false;',
					'imgclass' => 'back',
				);
				$this->toolbar_btn['skip'] = array(
					'href' => '',
					'js' => 'javascript:window.location.href=\''.$back.'&step='.($stepnum + 1).'\';return false;',
					'title' => $this->l('Skip'),
					'desc' => $this->l('Skip'),
					'imgclass' => 'new',
				);
				$this->toolbar_btn['cancel'] = array(
					'href' => '',
					'js' => 'javascript:window.location.href = \''.$back.'\';return false;',
					'title' => $this->l('Cancel'),
					'desc' => $this->l('Cancel'),
					'icon' => 'cancel'
				);
			}
			else
			{
				$this->fields_form[0]['form']['buttons'] = array(
					array(
						'title' => $this->l('Save Settings'),
						'href' => '',
						'js' => 'javascript:saveSettings();return false;', //onclick
						'desc' => $this->l('Save Settings'),
						'class' => 'btn-reset btn-info',
						'icon' => 'process-icon-save',
					),
					array(
						'href' => '',
						'title' => $this->l('Back'),
						'desc' => $this->l('Back'),
						'js' => 'javascript:window.location.href=\''.$back.'&step='.($stepnum - 1).'\'',
						'class' => 'btn-reset btn-info',
						'icon' => 'process-icon-back'
					),
					array(
						'href' => '',
						'js' => 'javascript:window.location.href=\''.$back.'&step='.($stepnum + 1).'\'',
						'title' => $this->l('Skip'),
						'desc' => $this->l('Skip'),
						'class' => 'btn-reset btn-info',
						'icon' => 'process-icon-next'
					),
					array(
						'href' => '',
						'js' => 'javascript:window.location.href = \''.$back.'\'',
						'title' => $this->l('Cancel'),
						'class' => 'btn-reset btn-danger intercom-tracked i-t:step-canceled',
						'icon' => 'process-icon-cancel'
					)
				);
			}
		}
		if (version_compare(_PS_VERSION_, '1.6', '<'))
			return $this->toolbar_btn;
	}
	/**Validate Step 1
	 *
	 */
	private function validateStep1($filename, &$settings)
	{
		if (!$filename)
		{
			if (Tools::getValue('source') == 'feed_file' && $_FILES['feed_file']['error'] !== UPLOAD_ERR_OK)
				$this->errors[] = $this->fileUploadErrorMessage($this->l($_FILES['feed_file']['error']));
			else
				$this->errors[] = $this->l('Warning: No file or empty file!');
		}
		else
		{
			$fp = fopen($filename, 'r');
			if ($settings['format'] == 'csv')
			{
				//test delimiters
				if ($settings['feed_csv'] == '\t')
					$settings['feed_csv'] = "\t";
				elseif ($settings['feed_csv'] == '')
					$settings['feed_csv'] = ',';
				$first_line = fgetcsv($fp, 0, $settings['feed_csv']);
				if (!empty($first_line))
				{
					if (count($first_line) < 2) //only one item in first row (probably wrong delimiter)
						$this->errors[] = $this->l('The delimiter you have chosen does not seem to be correct!');

					if (feof($fp)) //only one line in file (probably Mac CSV)
						$this->errors[] = $this->l('CSV file contains only one line: If you create your CSV file with Mac you need to save it as "CSV (Windows)"!');

					if (empty($settings['safe_headers']) || !empty($settings['has_headers']))
					{
						$existing = array();
						$blank_columns = 0;
						foreach ($first_line as $heading)
						{
							if ($heading === '')
							{ //empty column heading
								if ($blank_columns > 0)
									$heading_error = sprintf($this->l('Your headings are not suitable for direct usage please check every column has a %s heading, or use Safe Headings! (Blank Columns Found)'), '<strong>non-empty</strong>');
								else
									$blank_columns++;
							}
							if (isset($existing[Tools::strtolower($heading)]))
							{ //non-unique column heading (case insensitive)
								$heading_error = sprintf($this->l('Your headings are not suitable for direct usage please check every column has a %s heading, or use Safe Headings! (Duplicate Columns: %s)'),
									'<strong>unique</strong>', $heading);
							}
							$existing[Tools::strtolower($heading)] = 1;
						}
						if (isset($heading_error))
							$this->errors[] = $heading_error;
					}
				}
				else
					$this->errors[] = $this->l('The feed is empty or does not exist. Please check your feed!');
			} elseif ($settings['format'] == 'xml')
			{
				if (!$settings['feed_xml'])
					$this->errors[] = $this->l('You must specify a Product Tag for XML files!');
			}
		}

		return (!$this->errors);

	}

	/**Validates Step 3
	 *
	 */
	private function validateStep3()
	{
		if ($adjustments = Tools::getValue('adjust'))
		{
			foreach ($adjustments as $adjustment)
			{
				foreach ($adjustment as $value)
				{
					if ($value == '-- Select --')
						$this->errors[] = '"-- Select --" is an invalid field, please pick an existing field from your feed';
				}
			}
		}
		return (!$this->errors);
	}

	/**
	 * Associate product packs, accessories, and combination images to products/combinations
	 *
	 * (can only be added after products imported)
	 *
	 * @param (array) $associations
	 * @param (array) $settings
	 * @return array
	 */
	private function associateFields($associations, $settings)
	{
		foreach ($associations as $type => $item)
		{
			//default quantity to 1 of each product
			$qty = 1;
			if ($type == 'pack_products')
			{
				$this->updateConfig('PS_PACK_FEATURE_ACTIVE', 1);
				foreach ($item as $prod_id => $pack)
				{
					if (Pack::isPack($prod_id))
						Pack::deleteItems($prod_id);
					foreach ($pack as $item)
					{
						if ($settings['pack_match'] !== 'id')
							$pack_prod_id = $this->getProductId($settings['pack_match'], $item);
						else
							$pack_prod_id = (int)$item;
						Pack::addItem($prod_id, $pack_prod_id, $qty);
					}
				}
			}
			elseif ($type == 'accessories')
			{
				foreach ($item as $prod_id => $accessory)
				{
					$associate_product = new Product($prod_id);
					$associate_product->deleteAccessories();
					$accessory_prod_ids = array();
					foreach ($accessory as $item)
					{
						if ($settings['accessory_match'] !== 'id')
							$accessory_prod_ids[] = $this->getProductId($settings['accessory_match'], $item);
						else
							$accessory_prod_ids[] = (int)$accessory;
					}
					if ($accessory_prod_ids)
						$associate_product->changeAccessories($accessory_prod_ids);
				}
			}
			elseif ($type == 'combination_image')
			{
				$prod_ids = array();
				foreach ($item as $prod_id => $combination_image)
				{
					if (!in_array($prod_id, $prod_ids))
					{
						//remove combination images
						$this->deleteCombinationImages($prod_id);
					}
					foreach ($combination_image as $combo_id => $combo_image)
					{
						$isCoverImage = false;
						$image_id = $this->fetchImage($combo_image, $prod_id, $isCoverImage, $settings['thumbnail'], $settings['remote_images']);
						if ($image_id)
						{
							$image = new Image($image_id);
							$image->associateTo($this->shops);
							$combination = new Combination($combo_id);
							$combination->setImages(array($image->id));
						}
					}
					$prod_ids[] = $prod_id;
				}
			}
		}
	}

	/**
	 * Create attributes and attribute values
	 *
	 * @param $attributes array
	 * @param $settings array
	 *
	 * @return array
	 */
	private function createAttributes($attributes, $settings)
	{
		$attribute_ids = array();
		foreach ($attributes as $group)
		{
			//unset at beginning of each loop
			$attribute_group_id = null;
			$attribute_value_id = null;
			//search for attribute group id in all set store languages
			foreach ($group as $lang => $attr_group)
			{
				foreach ($attr_group as $group_name => $value)
				{
					if ($group_id = $this->getAttributeGroupId($group_name, $lang))
					{
						$attribute_group_id = $group_id; //attribute group id exists
						//check for attribute value for attr_group
						if ($value_id = $this->getAttributeValueId($value, $group_id, $lang))
							$attribute_value_id = $value_id; //attribute exists for attribute value
					}
				}
			}
			if (!isset($attribute_group_id))
			{
				//new Attribute
				$attribute_group = new AttributeGroup();
				$attribute_group->is_color_group = false;
				$attribute_group->group_type = $settings['group_type'];

				foreach ($group as $lang => $attr_group_name)
				{
					foreach ($attr_group_name as $name => $value)
					{
						$attribute_group->name[$lang] = $name;
						$attribute_group->public_name[$lang] = $name;
					}
				}
				$field_error = $attribute_group->validateFields($this->validate);
				$lang_field_error = $attribute_group->validateFieldsLang($this->validate);

				if ($field_error === true && $lang_field_error === true)
				{
					$attribute_group->add();
					$attribute_group->associateTo($this->shops);
					$attribute_group_id = $attribute_group->id;
				}
			}
			if (!isset($attribute_value_id))
			{
				//new Attribute Value for group
				$attribute = new Attribute();
				$attribute->id_attribute_group = $attribute_group_id;
				foreach ($group as $lang => $attr_value_name)
				{
					foreach ($attr_value_name as $name => $value)
						$attribute->name[$lang] = $value;
				}

				$field_error = $attribute->validateFields($this->validate);
				$lang_field_error = $attribute->validateFieldsLang($this->validate);

				if ($field_error === true && $lang_field_error === true)
				{
					$attribute->add();
					$attribute->id_shop_list = $this->shops;
					$attribute_value_id = $attribute->id;
				}
			}
			if (isset($attribute_value_id))
				$attribute_ids[] = $attribute_value_id;
		}
		return $attribute_ids;
	}

	/**
	 * Set attachments for a product
	 *
	 * @param $raw_prod array
	 * @param $settings array
	 * @param $product_id int
	 */
	private function setAttachments(&$raw_prod, &$settings, &$product_id)
	{
		//re-arrange attributes by field so languages are lumped together
		$total_attach = array();
		$languages = array_keys($settings['field_names']['attachment']);
		foreach ($languages as $lang)
		{
			$subs = array_keys($settings['field_names']['attachment'][$lang]);
			foreach ($subs as $key)
			{
				if (!empty($settings['field_names']['attachment'][$lang][$key]) && $raw_prod[$settings['field_names']['attachment'][$lang][$key]])
				{
					if (strstr($raw_prod[$settings['field_names']['attachment'][$lang][$key]], ':') == false) //advanced format
						$total_attach[$key][$lang] = $raw_prod[$settings['field_names']['attachment'][$lang][$key]];
					else
						$total_attach[$key][$lang] = explode(':', $raw_prod[$settings['field_names']['attachment'][$lang][$key]]);
				}
			}
		}

		$attachments = array();
		foreach ($total_attach as $field)
		{
			//unset at beginning of each loop
			$attachment_id = null;
			//search for attribute group id in all set store languages
			foreach ($field as $lang => $attachment_name)
			{
				//search for attribute group id in all set store languages
				$attachment_id = (is_array($attachment_name)) ? $this->getAttachmentId($attachment_name[0]) : $this->getAttachmentId($attachment_name);
				if (isset($attachment_id))
					break;
			}
			if (!isset($attachment_id))
			{
				$attach_details = array();
				$attachment = new Attachment();
				//fetch attachment for both lang names, only return one that exists
				foreach ($field as $lang => $name)
				{
					$source = $name;
					//grab source
					if (is_array($source))
						$source = $source[0];
					//grab name
					if (is_array($name) && isset($name[1]))
					{
						if (strstr($name[1], '/') !== false)
						{
							$name[1] = explode('/', $name[1]);
							$name = end($name[1]);
						}
					}
					else
					{
						if (strstr($name, '/') !== false)
						{
							$name = explode('/', $name);
							$name = end($name);
						}
					}

					$attachment->name[$lang] = (is_array($name) && isset($name[1])) ? $name[1] : $name;
					$attachment->description[$lang] = (is_array($name) && isset($name[2])) ? $name[2] : '';
					$attach_details = $this->fetchAttachment($source);
					if ($attach_details)
						break;
				}
				if ($attach_details)
				{
					$attachment->file = $attach_details['file'];
					$attachment->file_name = $attach_details['file_name'];
					$attachment->mime = $attach_details['mime'];

					$field_error = $attachment->validateFields($this->validate);
					$lang_field_error = $attachment->validateFieldsLang($this->validate);

					if ($field_error === true && $lang_field_error === true)
					{
						if ($attachment->add())
							$attachments[] = $attachment->id;
					}
				}
			}
			else
				$attachments[] = $attachment_id;
		}
		if (!empty($attachments[0]))
			Attachment::attachToProduct($product_id, $attachments);
	}

	/**
	 * Set carriers to a product.
	 *
	 * @param $raw_prod array
	 * @param $settings array
	 * @param $product_id int
	 */
	private function setCarriers(&$raw_prod, &$settings, &$product_id)
	{
		$carrier_shops = array();
		$carr_ids = array();
		$carr_refs = array();
		$store_carriers = $this->getCarriers();
		if (!empty($store_carriers))
		{
			foreach ($store_carriers as $store_carrier)
			{
				//default store name as carrier is 0 in database
				if ($store_carrier['name'] == 0)
				{
					$default_shop = Shop::getShop(Configuration::get('PS_SHOP_DEFAULT'));
					$store_carrier['name'] == $default_shop['name'];
				}
				$carrier_shops[$store_carrier['id_carrier']][] = $store_carrier['id_shop'];
				$carr_ids[$store_carrier['name']] = $store_carrier['id_carrier'];
				$carr_refs[$store_carrier['name']] = $store_carrier['id_reference'];
			}
		}

		$carriers = array();
		foreach ($settings['field_names']['carriers'] as $carrier)
		{
			if (!empty($raw_prod[$carrier]))
			{
				//detect delay message
				if (strstr($raw_prod[$carrier], ':') == true)
				{
					$carrier_parts = explode(':', $raw_prod[$carrier]);
					$raw_prod[$carrier] = $carrier_parts[0];
					$carrier_delay = $carrier_parts[1];
				}
				if (array_key_exists($raw_prod[$carrier], $carr_ids))
				{
					//add carrier to correct shops
					foreach ($this->shops as $shop)
					{
						if (!in_array($shop, $carrier_shops[$carr_ids[$raw_prod[$carrier]]]))
							$this->addCarrierToShop($carr_ids[$raw_prod[$carrier]], $shop);
					}
					$carriers[] = $carr_refs[$raw_prod[$carrier]];
				}
				else
				{
					$carr = new Carrier();
					$carr->position = Carrier::getHigherPosition() + 1;
					$carr->name = (string)$raw_prod[$carrier];
					$carr->delay = (isset($carrier_delay)) ? $this->createMultiLangField($carrier_delay) : $this->createMultiLangField($raw_prod[$carrier]);
					$carr->active = 1;

					$field_error = $carr->validateFields($this->validate);
					$lang_field_error = $carr->validateFieldsLang($this->validate);

					if ($field_error === true && $lang_field_error === true)
					{
						if ($carr->add())
						{
							//default links context shop, unlink it
							foreach (Shop::getContextListShopID() as $this->context_shop)
								$this->unlinkCarrierShop($carr->id, $this->context_shop);
							//link to shops
							foreach ($this->shops as $shop)
								$this->addCarrierToShop($carr->id, $shop);
							$carriers[] = $carr->id_reference;
						}
					}
				}
			}
			//reset delay message;
			$carrier_delay = null;
		}

		if ($carriers)
			$this->assignCarriersToProduct($carriers, $product_id);
	}

	/**
	 *	@return string Category Name with invalid characters removed
	 */
	private function validateCategoryName($cat_name)
	{
		$valid_cat_name = trim($cat_name);
		$valid_cat_name = str_replace('&amp;', '&', $valid_cat_name);
		$valid_cat_name = str_replace('&amp;amp;', '&', $valid_cat_name);

		return $valid_cat_name;
	}

	/**
	 * Takes column names from category mapping in step 4 and returns validated category names
	 *
	 * @param $cat_column_names Array of feed column names for categories
	 * @param $raw_prod Product Feed Data
	 *
	 *	@return array Array of validated MultiLanguage Category Names
	 */
	private function getCategoryNameArray($cat_column_names, $raw_prod, $language_ids)
	{
		$total_fields = array();
		foreach ($language_ids as $lang)
		{
			$mapped_cats = array_keys($cat_column_names[$lang]);
			foreach ($mapped_cats as $cat_num)
			{
				$cats = array();
				foreach ($cat_column_names[$lang][$cat_num] as $key => $cat_column)
				{
					if (!empty($raw_prod[$cat_column]))
						$cats[$key] = $this->validateCategoryName($raw_prod[$cat_column]);
				}
				$total_fields[$cat_num][$lang] = $cats;
			}
		}

		return $total_fields;
	}

	/**
	 * Takes Category names, returns list of Category IDs. Any Categories that don't exist will be created
	 *
	 * @param array $category_names Mapped Fields for categories
	 * @param array $raw_prod Product Feed data
	 *
	 * @return array List of Category IDs
	 */
	private function setCategories($category_names, &$raw_prod)
	{
		//re-arrange categories by field so languages are lumped together (multi languages = 1 cat)
		$languages = array_keys($category_names);
		$total_fields = $this->getCategoryNameArray($category_names, $raw_prod, $languages);
		$category_ids = array(Configuration::get('PS_HOME_CATEGORY'));

		foreach ($total_fields as $category_tree_num => $category_languages)
		{
			//each iteration will have home category as first parent
			$parent_id = Configuration::get('PS_HOME_CATEGORY');
			$home = new Category($parent_id);

			$parent_name = array();
			foreach ($languages as $lang)
			{
				if (isset($home->name[$lang]))
					$parent_name[$lang] = $home->name[$lang];
			}
			//keep a record of cats added
			$fields_added = array();
			foreach ($category_languages as $lang => $subs)
			{
				$parent = $parent_id; //use parent id
				$parent_level = $this->getCategoryLevel($parent_id);
				$sub_cat_num = 1;
				foreach ($subs as $cat_name)
				{
					$level = $parent_level + $sub_cat_num;
					if ($cat_name != $parent_name[$lang] && $cat_name != '')
					{
						if (isset($fields_added[$sub_cat_num]))
						{
							$existing_cat = new Category($fields_added[$sub_cat_num]);
							$existing_cat->name[$lang] = $cat_name;

							$field_error = $existing_cat->validateFields($this->validate);
							$lang_field_error = $existing_cat->validateFieldsLang($this->validate);

							if ($field_error === true && $lang_field_error === true)
								$existing_cat->update();
						}
						else
						{
							//hasn't been imported yet, check to see if cat exists at level, parent id, language, $shop
							$cat_id = $this->getCategoryId($cat_name, $level, $parent, $lang);
							//it doesn't exist, let's add it at level_depth, parent
							if (!$cat_id)
								$cat_id = $this->createNewCategory($cat_name, $lang, $parent, $level);
							// Associate with category
							$parent = $cat_id;
							$fields_added[$sub_cat_num] = $parent;
							if (!in_array($cat_id, $category_ids))
								$category_ids[] = $cat_id; //add to return cats
						}
					}
					$sub_cat_num++;
				}
			}
		}
		return $category_ids;
	}

	private function createNewCategory($cat_name, $lang, $parent, $level)
	{
		$new_cat_id = 0;

		$category = new Category();
		$category->name = $this->createMultiLangField($cat_name);
		$category->name[$lang] = $cat_name;
		$category->link_rewrite[$this->default_language] = Tools::link_rewrite($cat_name);
		$category->id_parent = $parent;
		$category->level_depth = $level;
		$category->active = 1;
		$category->is_root_category = 0;
		$category->id_shop_default = $this->shops[0];
		$category->doNotRegenerateNTree = true;
		$field_error = $category->validateFields($this->validate);
		$lang_field_error = $category->validateFieldsLang($this->validate);
		if ($field_error === true && $lang_field_error === true)
		{
			if ($category->add())
			{
				$this->logger->logInfo('Created Category: '.$cat_name);
				//add to correct shop
				foreach ($this->shops as $shop)
					$category->addShop((int)$shop);
				$new_cat_id = $category->id;
			}
		}

		return $new_cat_id;
	}

	private function getValidCombinationPrice($price)
	{
		$price = preg_replace('/^[^\d]+/', '', $price);
		$price = str_replace(',', '.', $price);

		return $price;
	}

	private function getValidReference($reference)
	{
		$reference = preg_replace('/^[<>;={}]*$/i', '', $reference);

		return $reference;
	}

	private function getBool($value)
	{
		return (bool)$value;
	}

	private function buildCombinationDataArray($raw_prod, $settings)
	{
		$combination_data = array();

		foreach ($this->combination_fieldlist as $field)
		{
			$validation_required = is_array($field);
			if ($validation_required)
				$field_name = $field['name'];
			else
				$field_name = $field;

			$real_field_name = str_replace('combination_', '', $field_name);
			if (isset($raw_prod[$settings['field_names'][$field_name]]))
			{
				$field_value = $raw_prod[$settings['field_names'][$field_name]];
				if ($validation_required)
					$field_value = call_user_func(array($this, $field['validation']), $field_value);

				$combination_data[$real_field_name] = $field_value;
			}
		}
		return $combination_data;
	}

	private function validateCombination($combination_obj)
	{
		$field_error = $combination_obj->validateFields($this->validate);
		$lang_field_error = $combination_obj->validateFieldsLang($this->validate);

		return ($field_error === true && $lang_field_error === true);
	}

	private function updateExistingCombination($id_combination, $id_product, $raw_prod, $settings)
	{
		//attribute group & combination exists, update the fields
		$combination = new Combination($id_combination);
		$combination->id_product = $id_product;

		$combination_data = $this->buildCombinationDataArray($raw_prod, $settings);
		foreach ($combination_data as $name => $value)
			if ($name != 'supplier_reference') {
				if ($value != '')
					$combination->{$name} = $value;
			} else
				$combination->{$name} = $value;

		if ($this->validateCombination($combination) && $combination->save())
		{
			$this->logger->logInfo(sprintf('Updated Combination: %s, for Product ID: %s', $combination->reference, $id_product));
			$this->total_combinations_updated++;
			if (isset($combination_data['quantity'])) {
				StockAvailable::setQuantity($id_product, $id_combination, (int)$combination_data['quantity']);
			}
		}
	}

	private function addCombinationToProduct($product_id, $combination_data)
	{
		$product = new Product($product_id);
		$product_combination_id = $product->addCombinationEntity(
			(float)$combination_data['wholesale_price'],
			(float)$combination_data['price'],
			(float)$combination_data['weight'],
			$combination_data['unit_price_impact'],
			0, // Ecotax
			(int)$combination_data['quantity'], // This has been deprecated
			0, // Image ID - associate after all products added
			(string)$combination_data['reference'],
			0, // id_supplier is deprecated
			(string)$combination_data['ean13'],
			(int)$combination_data['default_on'],
			0, //location = null
			(string)$combination_data['upc'],
			(int)$combination_data['minimal_quantity'],
			$this->shops
			//available_date = null
		);

		return $product_combination_id;
	}

	/**
	 * Set combinations for a product
	 *
	 * @param $raw_prod array
	 * @param $settings array
	 * @param $product_id int
	 * @param $combination_id int
	 * @param $attribute_ids
	 */
	private function setCombinations(&$raw_prod, &$settings, &$product_id, &$combination_id, &$attribute_ids)
	{
		if ($combination_id)
			$this->updateExistingCombination($combination_id, $product_id, $raw_prod, $settings);
		elseif ($attribute_ids) //create new combination only if the attribute group name is set
		{
			$combination = array(
				'default_on' => 0,
				'weight' => 0,
				'minimal_quantity' => 1,
				'quantity' => 0,
				'unit_price_impact' => 0,
				'price' => 0,
				'wholesale_price' => 0,
				'upc' => 0,
				'reference' => '',
				'ean13' => '',
				'id_supplier' => '',
			);

			$combination_data = $this->buildCombinationDataArray($raw_prod, $settings);

			foreach ($combination as $name => $value)
			{
				if (isset($combination_data[$name]))
					$combination[$name] = $combination_data[$name];
				elseif (isset($raw_prod[$name]))
					$combination[$name] = $raw_prod[$name];
			}

			$combination_id = $this->addCombinationToProduct($product_id, $combination);
			if ($combination_id)
			{
				$this->logger->logInfo(sprintf('Added Combination (ID : %s), for Product (ID: %s)', $combination_id, $product_id));
				$this->total_combinations_added++;

				Product::updateDefaultAttribute($product_id);
				$this->logger->logInfo(sprintf('Setting default attributes, product (ID: %s)', $product_id));

				StockAvailable::setQuantity($product_id, $combination_id, (int)$combination['quantity']);

			}
		}
		$combination = new Combination($combination_id);

		//Specific Prices
		if (isset($raw_prod[$settings['field_names']['combination_specific_price'][0]]))
		{
			//remove previous specific prices
			$specific_shops = $this->getSpecificPriceShops($product_id, $combination_id);
			if ($specific_shops)
				$this->deleteSpecificPriceByShop($product_id, $combination_id);
			$this->setSpecificPrices($raw_prod, $settings, $product_id, $combination_id);
		}

		//add to associations array to associate to products after they are imported;
		if (isset($raw_prod[$settings['field_names']['combination_image']]))
			$this->associations['combination_image'][$product_id][$combination_id] = $raw_prod[$settings['field_names']['combination_image']];

		if (isset($raw_prod[$settings['field_names']['combination_supplier']]))
			$this->setCombinationSupplierReference($raw_prod[$settings['field_names']['combination_supplier']], $product_id, $combination_id);

		//update attributes if they exist
		if ($attribute_ids)
			$combination->setAttributes($attribute_ids);
	}

	/**
	 * 	(Supplier Name:Supplier Reference:Supplier Price:Supplier Currency)
	 */
	private function setCombinationSupplierReference($combination_field, $product_id, $combination_id)
	{
		$exploded = explode(':', $combination_field);
		if (count($exploded) >= 2)
		{
			$supplier_name = $exploded[0];
			$supplier_reference = $exploded[1];
			if (!empty($supplier_name)) {
				$supplier_id = $this->getSupplierIDByName($supplier_name);
				if (!$supplier_id)
					$supplier_id = $this->createNewSupplier($supplier_name);
			}

			if (isset($supplier_id)) {
				$product = new Product($product_id);

				if (empty($product->id_supplier))
					$this->assignDefaultSupplier($supplier_id, $product_id);

				$supplier_price = null;
				if (count($exploded) >= 3)
					$supplier_price = $exploded[2];

				$id_supplier_currency = null;
				if (count($exploded) >= 4)
					$id_supplier_currency = $this->findCurrencyId($exploded[3]);

				$product->addSupplierReference($supplier_id, $combination_id, $supplier_reference, $supplier_price, $id_supplier_currency);
				$this->logger->logInfo(sprintf('Updated Combination Supplier for Product ID: %s, Supplier Reference: %s, Supplier Price: %s, Supplier ID: %s, Combination ID: %s', $product_id, $supplier_reference, $supplier_price, $supplier_id, $combination_id));
			}
		}
	}

	private function findCurrencyId($search_string)
	{
		$id_supplier_currency = null;
		if (Currency::getCurrency($search_string)) // Is this an existing currency ID?
			$id_supplier_currency = $search_string;
		elseif ($currency_id = Currency::getIdByIsoCode($search_string)) // Is this an existing currency ISO Code?
			$id_supplier_currency = $currency_id;
		elseif ($currency_id = $this->getCurrencyIdByName($search_string)) // Is this an existing currency Name?
			$id_supplier_currency = $currency_id;

		return $id_supplier_currency;
	}

	private function getCurrencyIdByName($name)
	{
		$currencies = Currency::getCurrencies();
		foreach ($currencies as $currency)
		{
			if ($currency['name'] === $name)
				return $currency['id'];
		}
		return false;
	}

	private function getSupplierIDByName($name)
	{
		$supplier_id = 0;

		$query = new DbQuery();
		$query->select('id_supplier');
		$query->from('supplier');
		$query->where('`name` = \''.$name.'\'');
		$res = Db::getInstance()->getValue($query);
		if (!empty($res))
			$supplier_id = (int)$res;

		return $supplier_id;
	}

	/**
	 * Set customization fields for a product
	 *
	 * format - <label>:<required> (string:bool)
	 *
	 * @param $raw_prod array
	 * @param $settings array
	 * @return array
	 */
	private function setCustomizationFields($raw_prod, $settings)
	{
		//re-arrange attributes by field so languages are lumped together
		$total_custom = array();
		$custom_fields = array('custom_file', 'custom_text');
		foreach ($custom_fields as $custom)
		{
			$languages = array_keys($settings['field_names'][$custom]);
			foreach ($languages as $lang)
			{
				$subs = array_keys($settings['field_names'][$custom][$lang]);
				foreach ($subs as $key)
				{
					if (isset($raw_prod[$settings['field_names'][$custom][$lang][$key]]))
					{
						if (strstr($raw_prod[$settings['field_names'][$custom][$lang][$key]], ':') == false) //advanced format
							$total_custom[$custom][$key][$lang] = $raw_prod[$settings['field_names'][$custom][$lang][$key]];
						else
							$total_custom[$custom][$key][$lang] = explode(':', $raw_prod[$settings['field_names'][$custom][$lang][$key]]);
					}
				}
			}
		}

		return $total_custom;
	}

	/**
	 * Set features to a product
	 *
	 * @param $raw_prod array
	 * @param $settings array
	 * @param $product_id int
	 */
	private function setFeatures($raw_prod, $settings, $product_id)
	{
		//re-arrange attributes by field so languages are lumped together
		$total_feat = array();
		$feat_array = array();
		$languages = array_keys($settings['field_names']['feature']);
		foreach ($languages as $lang)
		{
			$subs = array_keys($settings['field_names']['feature'][$lang]);
			foreach ($subs as $key)
			{
				if (isset($raw_prod[$settings['field_names']['feature'][$lang][$key]]))
				{
					//check what kind of import is occuring
					if (preg_match('/^(.{1,}:){2}([0-9]\,)/i', $raw_prod[$settings['field_names']['feature'][$lang][$key]]))
					{
						$feat_array[$lang] = explode(',', $raw_prod[$settings['field_names']['feature'][$lang][$key]]);
						$complex_import = true;
					}
					else
					{
						$total_feat[$key][$lang] = $settings['field_names']['feature'][$lang][$key];
						$complex_import = false;
					}
				}
			}
		}

		if ($complex_import)
		{
			$features = array();
			//split the features into <name>, <value>, <position>
			foreach ($languages as $lang)
			{
				foreach ($feat_array[$lang] as $feature_string)
				{
					$_feature = explode(':', $feature_string);
					//search for attribute group id in all set store languages
					if ($feature_id = $this->getFeatureId($_feature[0], $lang))
					{
						if (!empty($_feature[1]))
							$feature_value_id = $this->getFeatureValueId($_feature[1], $feature_id, $lang);
					}
					if (!isset($feature_id))
						$feature_id = $this->createFeatureId($_feature[0], $lang);
					if (!isset($feature_value_id))
						$feature_value_id = $this->createFeatureValue($feature_id, $_feature[1], $lang);
					$features[$feature_id][] = $feature_value_id;
				}
			}
			//associate features to product
			if ($features)
				$this->addFeaturesToDB($features, $product_id);
			Feature::cleanPositions();
		}
		else
			$this->simpleFeatureImport($raw_prod, $settings, $product_id, $total_feat);
	}

	private function simpleFeatureImport($raw_prod, $settings, $product_id, $total_feat)
	{
		$features = array();
		foreach ($total_feat as $field)
		{
			//unset at beginning of each loop
			$feature_id = null;
			$feature_value_id = null;
			//search for attribute group id in all set store languages
			foreach ($field as $lang => $feature_name)
			{
				//search for attribute group id in all set store languages
				if ($feature_id = $this->getFeatureId($feature_name, $lang))
				{
					if (!empty($raw_prod[$feature_name]))
						$feature_value_id = $this->getFeatureValueId($raw_prod[$feature_name], $feature_id, $lang);
				}
			}

			if (!isset($feature_id))
			{
				//new feature
				$feature = new Feature();
				foreach ($field as $lang => $feature_name)
					$feature->name[$lang] = $feature_name;

				$field_error = $feature->validateFields($this->validate);
				$lang_field_error = $feature->validateFieldsLang($this->validate);

				if ($field_error === true && $lang_field_error === true)
				{
					if ($feature->add())
					{
						$feature_id = $feature->id;
						//associate to all shops
					}
				}
			}
			if (!isset($feature_value_id))
			{
				//new feature value
				$feature_value = new FeatureValue();
				$feature_value->id_feature = $feature_id;
				$feature_value->custom = 0;
				foreach ($field as $lang => $feature_name)
					$feature_value->value[$lang] = $raw_prod[$feature_name];

				$field_error = $feature_value->validateFields($this->validate);
				$lang_field_error = $feature_value->validateFieldsLang($this->validate);

				if ($field_error === true && $lang_field_error === true)
				{
					if ($feature_value->add())
						$feature_value_id = $feature_value->id;
				}
			}
			$features[$feature_id][] = $feature_value_id;
		}
		//associate features to product
		if ($features)
			$this->addFeaturesToDB($features, $product_id);
		Feature::cleanPositions();
	}

	private function createFeatureId($name, $lang_id)
	{
		//new feature
		$feature = new Feature();
		$feature->name[$lang_id] = $name;

		$field_error = $feature->validateFields($this->validate);
		$lang_field_error = $feature->validateFieldsLang($this->validate);

		if ($field_error === true && $lang_field_error === true)
		{
			if ($feature->add())
				return $feature->id;
		}
	}

	private function createFeatureValue($feature_id, $featureValue, $lang_id)
	{
		//new feature value
		$feature_value = new FeatureValue();
		$feature_value->id_feature = $feature_id;
		$feature_value->custom = 0;
		$feature_value->value[$lang_id] = $featureValue;

		$field_error = $feature_value->validateFields($this->validate);
		$lang_field_error = $feature_value->validateFieldsLang($this->validate);

		if ($field_error === true && $lang_field_error === true)
		{
			if ($feature_value->add())
				return $feature_value->id;
		}
	}

	private function addFeaturesToDB($features, $product_id)
	{
		foreach ($features as $feature_id => $feature_value)
		{
			$this->associateFieldToShop($feature_id, 'feature');
			foreach ($feature_value as $feature_value_id)
			{
				Db::getInstance()->execute('
					INSERT INTO `'._DB_PREFIX_.'feature_product` (`id_feature`, `id_product`, `id_feature_value`)
					VALUES ('.(int)$feature_id.', '.(int)$product_id.', '.(int)$feature_value_id.')
					ON DUPLICATE KEY UPDATE `id_feature_value` = '.(int)$feature_value_id
				);
			}
		}
	}

	/**
	 * Set downloads to a product
	 *
	 * @param $raw_prod array
	 * @param $settings array
	 * @param $product_id int
	 */
	private function setProductDownload(&$raw_prod, &$settings, &$product_id)
	{
		$product_download = new ProductDownload();
		$file = $raw_prod[$settings['field_names']['file']];
		if (file_exists(realpath(_PS_DOWNLOAD_DIR_).'/'.$file))
		{
			$new_name = $product_download->getNewFilename();
			if (rename(realpath(_PS_DOWNLOAD_DIR_).'/'.$file, realpath(_PS_DOWNLOAD_DIR_).'/'.$new_name))
			{
				if (strstr($file, '/') !== false)
				{
					$file = explode('/', $file);
					$file = end($file);
				}
				$product_download->filename = $new_name;
				if (!empty($raw_prod[$settings['field_names']['filename']]))
				{
					if (strstr($file, '/') !== false)
					{
						$display = explode('/', $raw_prod[$settings['field_names']['filename']]);
						$display = end($display);
					}
					else
						$display = $raw_prod[$settings['field_names']['filename']];
					$product_download->display_filename = $display;
				}
				else
				{
					//use filename from file path instead
					$product_download->display_filename = $file;
				}
				$product_download->id_product = $product_id;
				$product_download->date_add = date('Y-m-d H:i:s');
				$product_download->date_expiration = !empty($raw_prod[$settings['field_names']['expiration_date']]) ? date('Y-m-d H:i:s', strtotime($raw_prod[$settings['field_names']['expiration_date']])) : ''; //no expiration date
				$product_download->nb_days_accessible = !empty($raw_prod[$settings['field_names']['number_days']]) ? $raw_prod[$settings['field_names']['number_days']] : 0; //unlimited
				$product_download->nb_downloadable = !empty($raw_prod[$settings['field_names']['number_downloads']]) ? $raw_prod[$settings['field_names']['number_downloads']] : 0; //unlimited
				$product_download->active = 1;

				$field_error = $product_download->validateFields($this->validate);
				$lang_field_error = $product_download->validateFieldsLang($this->validate);

				if ($field_error === true && $lang_field_error === true)
					$product_download->save();
			}
		}
	}

	/*
	Requires these formats:

	a) Simple
	<discount_value>

	b) Advanced
	discount_value:base_price:start_date:end_date:from_quantity:shop:currency:country:customer_group

	param1: discount_value, 0 for no discount value (required)
	param2: base_price, 0 if not using new base price (required)
	param3: start_date 0000-00-00 (required)
	param4: end_date 0000-00-00 (required)
	param5: from_quantity, Starting at # units, 1 for default (required)
	param6-9: rule shop:currency:country:customer_groups, 0 default to indicate All, otherwise use id (not required)

	i.e.

	12.00:0:2014-02-05:2014-02-06:2:2:1:1

	*/
	private function setSpecificPrices(&$raw_prod, &$settings, &$product_id, &$combination_id = 0, &$simple = 0)
	{
		$specific_prices = array();
		$default_values = array(
			'reduction' => (int)0, //discount_value
			'reduction_type' => $settings['discount_type'], //from global settings
			'price' => -1, //base price
			'id_currency' => (int)0, //all currencies
			'id_country' => (int)0, //all countries
			'id_customer' => (int)0, //all customers
			'id_shop' => (int)0, //all shops
			'from' => '0000-00-00 00:00:00', //start_date
			'to' => '0000-00-00 00:00:00', //end_date
			'from_quantity' => (int)1,
			'id_group' => (int)0,
		);

		//specify field group, simple or normal
		$field_group = ($simple) ? $settings['simple_names'] : $settings['field_names'];
		//combination or regular
		$specific_name = ($combination_id) ? 'combination_specific_price' : 'specific_price';

		//Delete specific prices if setting from step2 true
		if ($settings['delete_spec_pri'])
			$this->deleteSpecificPriceByShop($product_id);

		foreach ($field_group[$specific_name] as $discount_field)
		{
			if (isset($raw_prod[$discount_field]))
			{
				if (strstr($raw_prod[$discount_field], ':') !== false) //advanced format
				{
					$discounts = (strstr($raw_prod[$discount_field], '|') !== false ) ? explode('|', $raw_prod[$discount_field]) : array($raw_prod[$discount_field]);
					if (isset($discounts[0]))
					{
						foreach ($discounts as $discount)
						{
							//price - remove leading $ or pound or euro symbol, remove any commas.
							$discount_parts = explode(':', $discount);
							//check for required fields
							if ($discount_parts >= 5)
							{
								if (isset($discount_parts[0]))
								{
									$discount_parts[0] = preg_replace('/^[^\d]+/', '', $discount_parts[0]);
									$discount_parts[0] = str_replace(',', '', $discount_parts[0]);
								}
								if ($discount_parts[0] != 0 && $discount_parts[0] != -0) {
									$specific_price = new SpecificPrice();
									$specific_price->id_product = $product_id;

									$specific_price->reduction = ($default_values['reduction_type'] == 'amount') ? $discount_parts[0]
										: $discount_parts[0] / 100;
									$specific_price->reduction_type = $default_values['reduction_type'];
									$specific_price->price = (isset($discount_parts[1])) ? $discount_parts[1] : $default_values['price'];

									//dates
									$specific_price->from = (isset($discount_parts[2])) ? date('Y-m-d H-i-s', strtotime($discount_parts[2])) : $default_values['from'];
									$specific_price->to = (isset($discount_parts[3])) ? date('Y-m-d H-i-s', strtotime($discount_parts[3])) : $default_values['to'];

									$specific_price->from_quantity = (isset($discount_parts[4])) ? (int)$discount_parts[4] : $default_values['from_quantity'];

									//priorities
									$specific_price->id_shop = (isset($discount_parts[5])) ? (int)$discount_parts[5] : $default_values['id_shop'];
									$specific_price->id_currency = (isset($discount_parts[6])) ? (int)$discount_parts[6] : $default_values['id_currency'];

									$specific_price->id_country = (isset($discount_parts[7])) ? (int)$discount_parts[7] : $default_values['id_country'];
									$specific_price->id_customer = $default_values['id_customer'];
									$specific_price->id_group = (isset($discount_parts[8])) ? (int)$discount_parts[8] : $default_values['id_group'];

									//combinations
									if ($combination_id)
										$specific_price->id_product_attribute = $combination_id;

									if (!$simple) //don't add for simple update
									{
										$field_error = $specific_price->validateFields($this->validate);
										$lang_field_error = $specific_price->validateFieldsLang($this->validate);

										if ($field_error === true && $lang_field_error === true)
											$specific_price->add();
									}
									$specific_prices[] = $specific_price;
								}
							}
						}
					}
				}
				else //simple format
				{
					$specific_price = new SpecificPrice();
					$specific_price->id_product = $product_id;
					//reset defaults
					foreach ($default_values as $field => $value)
					{
						if (array_key_exists($field, $specific_price))
							$specific_price->$field = $value;
					}
					if ($raw_prod[$discount_field] != 0 && $raw_prod[$discount_field] != -0) {
						//add feed fields
						$specific_price->reduction = ($default_values['reduction_type'] == 'amount') ? $raw_prod[$discount_field]
							: $raw_prod[$discount_field] / 100;
						$specific_price->reduction_type = $default_values['reduction_type'];

						//combinations
						if ($combination_id)
							$specific_price->id_product_attribute = $combination_id;

						if (!$simple) //don't add for simple update
						{
							$field_error = $specific_price->validateFields($this->validate);
							$lang_field_error = $specific_price->validateFieldsLang($this->validate);

							if ($field_error === true && $lang_field_error === true)
								$specific_price->add();
						}
						$specific_prices[] = $specific_price;
					}
				}
			}
		}
		return $specific_prices;
	}

		/**
		* Create a new Product Supplier
		* @param $productId
		* @param $supplierId
		*
		*/
		private function createNewProductSupplier($productId, $supplierId, $supplier_reference)
		{
				$productSuppId = 0;
				$product_supplier = new ProductSupplier();
				$product_supplier->id_product = $productId;
				$product_supplier->id_product_attribute = 0;
				$product_supplier->id_supplier = $supplierId;
				if (isset($supplier_reference))
					$product_supplier->product_supplier_reference = $supplier_reference;

				$field_valid = $product_supplier->validateFields($this->validate);
				$lang_field_valid = $product_supplier->validateFieldsLang($this->validate);

				if ($field_valid === true && $lang_field_valid === true)
					if ($product_supplier->save())
						$productSuppId = $product_supplier->id;

				return $productSuppId;
		}

		private function createNewSupplier($supplierName)
		{
			$suppId = 0;
			$supp = new Supplier();
			$supp->name = $supplierName;
			$supp->link_rewrite = Tools::link_rewrite((string)$supplierName);
			$supp->active = 1;

			$field_valid = $supp->validateFields($this->validate);
			$lang_field_valid = $supp->validateFieldsLang($this->validate);

			if ($field_valid === true && $lang_field_valid === true)
				if ($supp->add())
				{
					$suppId = $supp->id;
					$this->logger->logInfo("Created new Supplier - Name: ".$supplierName.", ID: ".$suppId);
				}
			return $suppId;
		}

	/**
	 * Set Suppliers to a product
	 *
	 * <supplier_name>:<supplier_reference>
	 *
	 * @param $raw_prod array
	 * @param $settings array
	 * @param $product_id int
	 * @return int
	 */
	private function setSuppliers(&$raw_prod, &$settings, &$product_id)
	{
		$default_supplier = 0;
		if (isset($settings['field_names']['supplier'][0]) || !empty($settings['field_names']['supplier'][0]))
			$supplier_fields = array_merge(array($settings['field_names']['def_supplier']), $settings['field_names']['supplier']);
		else
			$supplier_fields = array($settings['field_names']['def_supplier']);
		$supplier_fields = array_unique($supplier_fields);
		$mappedSupplierIndex = 1; //?
		$suppliers = $this->getSuppliers(); //get suppliers

		foreach ($supplier_fields as $supplier_field_name)
		{
			if (!empty($raw_prod[$supplier_field_name]))
			{
				//check for supplier reference
				if (strstr($raw_prod[$supplier_field_name], ':'))
				{
					$raw_prod[$supplier_field_name] = explode(':', $raw_prod[$supplier_field_name]);

					$supplier_reference = $raw_prod[$supplier_field_name][1];
					$raw_prod[$supplier_field_name] = $raw_prod[$supplier_field_name][0];
				}
				$supplier_name = $raw_prod[$supplier_field_name];

				//check if supplier exists in store already
				if (array_key_exists($supplier_name, $suppliers))
				{
					$assoc_id = $this->getProductSupplier($product_id, $suppliers[$supplier_name]);
					if (!$assoc_id)
					{
							$this->createNewProductSupplier($product_id, $suppliers[$supplier_name], $supplier_reference);
							//don't link/unlink existing suppliers to shop, only for new suppliers
							$assoc_id = $suppliers[$supplier_name];
					}
					//default supplier
					if ($mappedSupplierIndex == 1)
						$default_supplier = $assoc_id;
				}
				else
				{
						$suppId = $this->createNewSupplier($supplier_name);

						if ($mappedSupplierIndex == 1)
								$default_supplier = $suppId;

						//create new product supplier
						$this->createNewProductSupplier($product_id, $suppId, $supplier_reference);
				}
				$supplier_reference = null; //reset supplier_reference for loop
			}
			$mappedSupplierIndex++;
		}

		return $default_supplier;
	}

	/* Create field for each language
	 *
	 */
	private function createMultiLangField($field)
	{
		$res = array();
		foreach ($this->languages as $lang)
			$res[$lang['id_lang']] = $field;
		return $res;
	}

	private function _setBaseUrl()
	{
		$this->_baseUrl = 'index.php?tab=AdminModules&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules');
	}

	private function fileUploadErrorMessage($error_code)
	{
		switch ($error_code)
		{
			case UPLOAD_ERR_INI_SIZE:
				return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
			case UPLOAD_ERR_FORM_SIZE:
				return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
			case UPLOAD_ERR_PARTIAL:
				return 'The uploaded file was only partially uploaded';
			case UPLOAD_ERR_NO_FILE:
				return 'No file was uploaded';
			case UPLOAD_ERR_NO_TMP_DIR:
				return 'Missing a temporary folder';
			case UPLOAD_ERR_CANT_WRITE:
				return 'Failed to write file to disk';
			case UPLOAD_ERR_EXTENSION:
				return 'File upload stopped by extension';
			default:
				return 'Unknown upload error';
		}
	}

	private function fetchFtp($server, $user, $pass, $remote_file, $local_file)
	{
		$success = false;
		$server = str_replace('ftp://', '', $server);
		if (strpos($server, ':') === false)
			$conn_id = ftp_connect($server);
		else
		{
			$host = explode(':', $server);
			$conn_id = ftp_connect($host[0], (int)$host[1]);
		}
		if ($conn_id)
			$login_result = ftp_login($conn_id, $user, $pass);
		if ((!$conn_id) || (!$login_result))
		{
			$this->errors[] = 'Ftp connection has failed! Attempted to connect to '.$server.'
				for user '.$user.'.';
		}
		else
		{
			//ftp_pasv($conn_id, true);
			$success = ftp_get($conn_id, $local_file, $remote_file, FTP_BINARY);
			ftp_close($conn_id);
			return $success;
		}
	}

	private function resetDefaultValues(&$settings)
	{
		//prepare options for global settings
		$options = array('options_available_order', 'options_show_price', 'options_online_only');

		foreach ($options as $option)
		{
			if (!empty($settings[$option]))
				$settings[$option] = ($settings[$option] == 'on') ? true : false;
			else
				$settings[$option] = false;
		}

		$this->global_data = array(
			'out_of_stock' => $settings['out_of_stock'],
			'active' => $settings['product_status'],
			'minimal_quantity' => $settings['minimal_quantity'],
			'id_tax_rules_group' => $settings['id_tax_rules_group'],
			'available_for_order' => $settings['options_available_order'],
			'show_price' => $settings['options_show_price'],
			'online_only' => $settings['options_online_only'],
			'visibility' => $settings['visibility'],
			'condition' => $settings['condition'],
			'available_now' => $this->createMultiLangField($settings['text_available_now']),
			'available_later' => $this->createMultiLangField($settings['text_available_later']),
		);

		$this->prod_data = array(
			'id_category_default' => (int)Configuration::get('PS_HOME_CATEGORY'),
			'id_shop_default' => (int)Configuration::get('PS_SHOP_DEFAULT'),
			'id_shop_list' => array((int)Configuration::get('PS_SHOP_DEFAULT')),

			'reference' => '',
			//'id_supplier' => 0,
			'id_manufacturer' => 0,
			'ean13' => '',
			'upc' => '',

			'width' => 0,
			'depth' => 0,
			'height' => 0,
			'weight' => 0,

			'date_add' => date('Y-m-d H:i:s'),

			'quantity' => 1,
			'price' => 0,
			'additional_shipping_cost' => '',
			'wholesale_price' => '',
			'ecotax' => 0,

			'indexed' => 1,
		);

		$this->desc_data = array(
			'name' => $this->createMultiLangField('Product '.($this->total_items_added + 1)),
			'description' => $this->createMultiLangField(''),
			'description_short' => $this->createMultiLangField(''),
			'meta_title' => $this->createMultiLangField(''),
			'meta_description' => $this->createMultiLangField(''),
			//'meta_keywords' => $this->createMultiLangField(''),
			'link_rewrite' => $this->createMultiLangField('product'.($this->total_items_added + 1)),
		);
	}

	/*XML parser support functions:
	*
	* startTag
	* endTag
	* cData
	*
	*/
	private function startTag($parser, $name, $attr)
	{
		if (strcmp($name, $this->product_tag) == 0)
			$this->xml_product = array();
		//Get attributes
		foreach ($attr as $key => $value)
		{
			if (!isset($this->xml_product[$name.'_attr_'.$key]))
				$this->xml_product[$name.'_attr_'.$key] = $value;
			else
				$this->xml_product[$name.'_attr_'.$key] .= '^'.$value;
		}
	}

	private function endTag($parser, $name)
	{
		if (strcmp($name, $this->product_tag) == 0)
		{
			if (!$this->table_created)
			{
				$this->createEmptyTable(array_keys($this->xml_product));
				$this->xml_existing_fields = array_keys($this->xml_product);
				$this->table_created = true;
			}
			$new_columns = array_diff(array_keys($this->xml_product), $this->xml_existing_fields);
			//make sure new columns aren't just existing columns with different case:
			$not_new_columns = array();
			foreach ($new_columns as $new_col)
			{
				foreach ($this->xml_existing_fields as $existing_col)
				{
					if (Tools::strtolower($new_col) == Tools::strtolower($existing_col))
					{
						$not_new_columns[] = $new_col;
						$col_data = $this->xml_product[$new_col];
						unset($this->xml_product[$new_col]);
						$this->xml_product[$existing_col] = $col_data;
					}
				}
			}
			$new_columns = array_diff($new_columns, $not_new_columns);
			if (!empty($new_columns))
			{
				$this->alterImportTable($new_columns);
				$this->xml_existing_fields = array_unique(array_merge($this->xml_existing_fields, $new_columns));
			}
			if (!(!empty($this->xml_cron_fetch) && $this->total_items_ready >= CRON_FETCH_NUM))
			{
				$this->insertProduct($this->xml_product);
				$this->total_items_ready++;
			}
		}
		else
		{
			if (isset($this->xml_product[$name]))
				$this->xml_product[$name] .= '^'.$this->xml_data;
			else
				$this->xml_product[$name] = $this->xml_data;
		}
		$this->xml_data = '';
	}

	private function cData($parser, $content)
	{
		$this->xml_data .= $content;
	}


	/* MODEL FUNCTIONS */

	public function alterImportTable($new_fields)
	{
		if (!empty($new_fields))
		{
			$sql = 'ALTER TABLE '._DB_PREFIX_.'hj_import ADD COLUMN ';
			$fields_sql = array();
			foreach ($new_fields as $field)
				$fields_sql[] = '`'.bqSQL($field).'` BLOB NOT null';
			$sql .= '('.implode(', ', $fields_sql).')';
			$sql = str_replace(', )', ')', $sql);
			Db::getInstance()->execute($sql);
		}
	}

	private function validateHeaderForSql($heading_name)
	{
		// Must start with a letter or an underscore
		$heading_name = preg_replace('/^[^a-zA-Z_]*/', '', $heading_name);
		// Replace whitespace with an underscore
		$heading_name = preg_replace('/\s/', '_', $heading_name);
		// Names should only contain letters, digits, and underscores
		$heading_name = preg_replace('/[^a-zA-Z0-9_]/', '', $heading_name);

		return $heading_name;
	}

	public function createEmptyTable($headings)
	{
		Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'hj_import');
		$sql = 'CREATE TABLE '._DB_PREFIX_.'hj_import (hj_id INT(11) AUTO_INCREMENT, ';
		foreach ($headings as $heading)
			$sql .= '`'.bqSQL($this->validateHeaderForSql($heading)).'` BLOB, ';
		$sql .= 'PRIMARY KEY (hj_id))';

		Db::getInstance()->execute($sql);
	}


	/**
	 * Fetches an attachment from a URL or folder.
	 *
	 * format: <file>:<filename>:<description>
	 *
	 * @param $attachment_source
	 * @param $settings array
	 * @return bool
	 */
	public function fetchAttachment($attachment_source)
	{
		$attachment = array();
		//see if file exists in store directory
		if (file_exists(realpath(_PS_ROOT_DIR_).'/'.$attachment_source))
		{
			$new_name = sha1(microtime());
			$mimetype = mime_content_type(realpath(_PS_ROOT_DIR_).'/'.$attachment_source);
			if (rename(realpath(_PS_ROOT_DIR_).'/'.$attachment_source, realpath(_PS_DOWNLOAD_DIR_).'/'.$new_name))
			{
				if (strstr($new_name, '/') !== false)
					$new_name = explode('/', $new_name);
				if (strstr($attachment_source, '/') !== false)
					$attachment_source = explode('/', $attachment_source);
				$attachment = array(
					'mime' => $mimetype,
					'file' => is_array($new_name) ? end($new_name) : $new_name,
					'file_name' => is_array($attachment_source) ? end($attachment_source) : $attachment_source,
				);
			}
		}
		return $attachment;
	}

	private function humanifyFileSize($bytes, $decimals = 2)
	{
		$sz = 'BKMGTP';
		$factor = floor((Tools::strlen($bytes) - 1) / 3);
		return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)).@$sz[$factor];
	}

	private function getFilenameFromURL($image_source)
	{
		$filename = '';

		if (strstr($image_source, '?'))
			$filename = md5($image_source).'.jpg';
		else
		{
			$url_parts = explode('/', $image_source);
			// Decode html space for image filename
			$filename = str_replace('%20', '_', end($url_parts));
		}

		return $filename;

	}

	/**
	 * Attempts to fetch image from remote url
	 *
	 * @param  string $image_source Remote Image URL
	 * @return (mixed) False if image fetch failed, otherwise the full path to the fetched image
	 */
	private function fetchRemoteImage($image_source)
	{
		$image_source = trim($image_source);
		if (!$image_source)
		{
			$this->logger->logWarning('Image URL was empty, skipping.');
			return false;
		}

		//create a temp file if remote
		if (strpos($image_source, 'http') !== 0)
		{
			$this->logger->logWarning($image_source.' is not a valid URL, skipping image.');
			return false;
		}

		if (!function_exists('curl_version'))
		{
			$this->logger->logWarning('CURL is not enabled on your server, unable to fetch remote images.');
			return false;
		}

		$filename = $this->getFilenameFromURL($image_source);

		if (!$filename)
		{
			$this->logger->logWarning(sprintf('Unable to get valid filename from url: %s.', $image_source));
			return false;
		}

		if (file_exists(_PS_TMP_IMG_DIR_.$filename))
		{
			$this->logger->logInfo(sprintf('Image: %s already exists, using local copy.', $filename));
			return _PS_TMP_IMG_DIR_.$filename;
		}
		else
		{
			$fp = fopen(_PS_TMP_IMG_DIR_.$filename, 'w');
			$ch = curl_init();
			$ports = array();
			if (preg_match('/:(\d+)/', $image_source, $ports))
			{
				$image_source = preg_replace('/:\d+/', '', $image_source);
				curl_setopt($ch, CURLOPT_PORT, (int)$ports[1]);
			}
			curl_setopt($ch, CURLOPT_URL, $image_source);
			curl_setopt($ch, CURLOPT_FILE, $fp);
			if (ini_get('open_basedir') == "")
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_exec($ch);
			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$info = curl_getinfo($ch);
			curl_close($ch);
			fclose($fp);
			if ($this->isValidImageDownload($filename, $http_code))
			{
				$human_filesize = $this->humanifyFileSize($info['size_download']);
				$this->logger->logInfo(sprintf('Image downloaded: %s, Size: %s, Time Taken: %s secs', $image_source, $human_filesize, $info['total_time']));
				return _PS_TMP_IMG_DIR_.$filename;
			}
			else
				unlink(_PS_TMP_IMG_DIR_.$filename);
		}

		return false;
	}

	/**
	 * Check if the fetched remote image is a valid file
	 * @param  string  $filename
	 * @param  string  $http_code
	 * @return boolean
	 */
	private function isValidImageDownload($filename, $http_code)
	{
		if ($http_code == 404)
		{
			$this->logger->logWarning($filename.' not found (404), skipping image.');
			return false;
		}

		$file_info = '';
		if (filesize(_PS_TMP_IMG_DIR_.$filename) > 0)
			$file_info = getimagesize(_PS_TMP_IMG_DIR_.$filename);

		if (empty($file_info))
		{
			$this->logger->logWarning($filename.' was empty, skipping image.');
			return false;
		}

		if (isset($file_info['mime']) && strpos($file_info['mime'], 'image/') !== 0)
		{
			$this->logger->logWarning($filename.' returned invalid mimetype: '.$file_info['mime'].', skipping image.');
			return false;
		}

		return true;
	}

	/**
	 * Checks if image_source is valid, fetches remote images if enabled.
	 * If no valid image is found then false will be returned, otherwise the valid filename is returned
	 * @param  string $image_source Path to image or image URL
	 * @return (mixed) Valid image name or false
	 */
	public function getValidImage($image_source, $isRemoteURL)
	{
		$image_types = array('png', 'jpeg', 'jpg', 'gif');

		$valid_image = false;
		if ($isRemoteURL)
			$valid_image = $this->fetchRemoteImage($image_source);
		else
		{
			foreach ($image_types as $type)
			{
				if (strstr($image_source, $type) !== false)
				{
					//image located in store directory
					$file_path = _PS_ROOT_DIR_.'/'.$image_source;
					if (file_exists($file_path))
						$valid_image = $file_path;
					else
						$this->logger->logWarning($file_path.' does not exist, skipping image.');
					break;
				}
			}
		}

		return $valid_image;
	}

	/**
	 * Fetches an image from a URL or folder.
	 *
	 * @param $image_source string the image path/url to fetch
	 * @param $id_image int
	 * @param $id_product int
	 * @param $settings array
	 * @return (mixed) false if failure, otherwise image ID
	*/
	public function fetchImage($image_source, $id_product, $isCoverImage, $isThumbNail, $isRemoteURL)
	{
		$valid_filename = $this->getValidImage($image_source, $isRemoteURL);
		$success = false;
		if ($valid_filename)
		{
			//fetch image
			$image = new Image();

			$image->id_product = $id_product;
			$image->position = Image::getHighestPosition($id_product) + 1;
			$image->cover = $isCoverImage;
			if ($isCoverImage)
			{
				// We can't have two cover images, so clear any existing covers
				$this->clearCoverImage($id_product);
			}

			$image->add();

			if ($this->addImage($valid_filename, $image->getPathForCreation(), $image->id, $id_product, $isThumbNail))
				$success = $image->id;
			else
				$this->logger->logError($image_source.' caused an error trying to associate with product.');
		}

		return $success;
	}

	private function clearCoverImage($id_product)
	{
		$sql = 'UPDATE `'._DB_PREFIX_.'image` SET `cover`=NULL WHERE `id_product`=\''.bqSQL($id_product).'\'';
		Db::getInstance()->execute($sql);

		if (version_compare(_PS_VERSION_, '1.6', '>='))
		{
			$sql = 'UPDATE `'._DB_PREFIX_.'image_shop` SET `cover`=NULL WHERE `id_product`=\''.bqSQL($id_product).'\'';
			Db::getInstance()->execute($sql);
		}
	}

	/**
	 * Get attachment id based on attachment name
	 *
	 */
	public function getAttachmentId($attachment_name)
	{
		if (strstr($attachment_name, '/') !== false)
		{
			$attachment_name = explode('/', $attachment_name);
			$attachment_name = end($attachment_name);
		}
		$query = '
			SELECT al.`id_attachment`
			FROM '._DB_PREFIX_.'attachment_lang as al
			WHERE al.`name` = \''.bqSQL($attachment_name).'\'';

		$query = Db::getInstance()->executeS($query);

		return (isset($query[0]['id_attachment'])) ? $query[0]['id_attachment'] : null;
	}

	private function makeValidAttributeName($name)
	{
		$invalid_chars = array('<', '>', '=', '{', '}');
		$valid_name = str_replace($invalid_chars, '', $name);

		if (empty($valid_name))
			$valid_name = 'No Name';

		return $valid_name;
	}

	/**
	 * Arrange attributes into attribute and value by language from feed
	 *
	 * @param $settings array
	 * @param $raw_prod array
	 *
	 * @return array
	 */
	public function getAttributes($settings, $raw_prod)
	{
		$total_attr = array();
		$languages = array_keys($settings['field_names']['attribute']);
		foreach ($languages as $lang)
		{
			$subs = array_keys($settings['field_names']['attribute'][$lang]);
			foreach ($subs as $key)
			{
				$attribute_field = $settings['field_names']['attribute'][$lang][$key];
				if (!empty($attribute_field)
					&& !empty($raw_prod[$attribute_field]))
				{
					$value_format = !empty($settings['field_names']['attribute_value'][$lang][$key]) ? true : false;

					$attribute = ($value_format) ? $raw_prod[$attribute_field] : $attribute_field;
					if ($value_format)
						$value = !empty($raw_prod[$settings['field_names']['attribute_value'][$lang][$key]]) ? $raw_prod[$settings['field_names']['attribute_value'][$lang][$key]] : false;
					else
						$value = !empty($raw_prod[$attribute_field]) ? $raw_prod[$attribute_field] : false;

					if ($value)
						$total_attr[$key][$lang][$attribute] = $this->makeValidAttributeName($value);
				}
			}
		}
		return $total_attr;
	}

	/**
	 * Get Attribute Group ID based on name, lang, and shop
	 *
	 * @param $name string
	 * @param $id_lang int
	 * @return int
	 */
	public function getAttributeGroupId($name, $id_lang)
	{
		$shop_count = count($this->shops);
		$query = '
			SELECT ag.`id_attribute_group` FROM '._DB_PREFIX_.'attribute_group AS ag
			LEFT JOIN '._DB_PREFIX_.'attribute_group_lang AS agl
				ON ag.`id_attribute_group` = agl.`id_attribute_group`
			LEFT JOIN '._DB_PREFIX_.'attribute_group_shop AS ags
				ON ag.`id_attribute_group` = ags.`id_attribute_group`
			WHERE LOWER(agl.`name`) = \''.bqSQL(Tools::strtolower($name)).'\'
			AND agl.`id_lang` = \''.(int)$id_lang.'\'
			AND ';
		//add shops
		$query .= ($shop_count > 1) ? '(': '';
		$s = 1;
		foreach ($this->shops as $shop)
		{
			$query .= 'ags.`id_shop` = '.(int)$shop;
			if ($s != $shop_count)
				$query .= ' OR ';
			$s++;
		}
		$query .= ($shop_count > 1) ? ')': '';

		$query = Db::getInstance()->executeS($query);

		return (isset($query[0]['id_attribute_group'])) ? $query[0]['id_attribute_group'] : null;
	}

	/**
	 * Get Attribute Value ID based on name, lang, and shop
	 *
	 * @param $name string
	 * @param $value_id
	 * @param $id_lang int
	 * @return int
	 */
	public function getAttributeValueId($name, $value_id, $id_lang)
	{
		$shop_count = count($this->shops);
		$query = '
			SELECT a.`id_attribute` FROM '._DB_PREFIX_.'attribute AS a
			LEFT JOIN '._DB_PREFIX_.'attribute_lang AS al
				ON a.`id_attribute` = al.`id_attribute`
			LEFT JOIN '._DB_PREFIX_.'attribute_shop AS s
				ON a.`id_attribute` = s.`id_attribute`
			WHERE LOWER(al.`name`) = \''.bqSQL(Tools::strtolower($name)).'\'
			AND a.`id_attribute_group` = \''.(int)$value_id.'\'
			AND al.`id_lang` = \''.(int)$id_lang.'\'
			AND ';

		//add shops
		$query .= ($shop_count > 1) ? '(': '';
		$s = 1;
		foreach ($this->shops as $shop)
		{
			$query .= 's.`id_shop` = '.(int)$shop;
			if ($s != $shop_count)
				$query .= ' OR ';
			$s++;
		}
		$query .= ($shop_count > 1) ? ')': '';

		$query = Db::getInstance()->executeS($query);

		return (isset($query[0]['id_attribute'])) ? $query[0]['id_attribute'] : null;
	}

	/**
	 * Gets active carriers
	 */
	public function getCarriers()
	{
		return Db::getInstance()->executeS('
			SELECT DISTINCT c.`id_carrier`, c.`name`, c.`id_reference`, cs.`id_shop`
			FROM `'._DB_PREFIX_.'carrier` as c
			INNER JOIN `'._DB_PREFIX_.'carrier_shop` as cs
				ON  cs.`id_carrier` = c.`id_carrier`
			INNER JOIN `'._DB_PREFIX_.'carrier_lang` cl
				ON c.`id_carrier` = cl.`id_carrier` AND cl.`id_lang` = '.(int)$this->default_language.'
			WHERE  c.`deleted` = 0
			AND c.`active` = 1
			ORDER BY cs.`id_shop`'
		);
	}

	/**
	 * Grab the id of the category based on level_depth, language, and parent
	 *
	 * @param $name (string)
	 * @param $level (int)
	 * @param $id_parent (int)
	 * @param $lang (int)
	 *
	 * @return bool|mixed
	 */
	public function getCategoryId($name, $level, $id_parent, $lang)
	{
		$sql = '
				SELECT cl.`id_category`
				FROM `'._DB_PREFIX_.'category_lang` cl LEFT JOIN `'._DB_PREFIX_.'category` c
					ON cl.`id_category` = c.`id_category`
				WHERE LOWER(cl.`name`) = "'.bqSQL(Tools::strtolower($name)).'"
				AND cl.`id_lang` = '.(int)$lang.'
				AND c.`level_depth` ='.(int)$level.'
				AND c.`id_parent` ='.(int)$id_parent;

		return ($res = Db::getInstance()->getValue($sql)) ? $res : false;
	}

	/*Get the feature id based on name
	 *
	 * @param $feature_name string
	 * @param $lang int
	 */
	public function getFeatureId($feature_name, $lang)
	{
		$shop_count = count($this->shops);
		$query = '
			SELECT fl.`id_feature`
			FROM '._DB_PREFIX_.'feature_lang as fl
			LEFT JOIN '._DB_PREFIX_.'feature_shop AS fs
			ON fl.`id_feature` = fs.`id_feature`
			WHERE fl.`name` = \''.bqSQL($feature_name).'\'
			AND fl.`id_lang` = '.(int)$lang.'
			AND ';
		//add shops
		$query .= ($shop_count > 1) ? '(': '';
		$s = 1;
		foreach ($this->shops as $shop)
		{
			$query .= 'fs.`id_shop` = '.(int)$shop;
			if ($s != $shop_count)
				$query .= ' OR ';
			$s++;
		}
		$query .= ($shop_count > 1) ? ')': '';

		$query = Db::getInstance()->executeS($query);

		return (isset($query[0]['id_feature'])) ? $query[0]['id_feature'] : null;
	}

	/*Get the feature value id based on name
	 *
	 * @param $feature_value string
	 * @param $feature_id int
	 * @param $lang int
	 */
	public function getFeatureValueId($feature_value, $feature_id, $lang)
	{
		$shop_count = count($this->shops);
		$query = '
			SELECT fl.`id_feature_value`
			FROM '._DB_PREFIX_.'feature_value_lang as fl
			LEFT JOIN '._DB_PREFIX_.'feature_value AS f
				ON fl.`id_feature_value` = f.`id_feature_value`
			LEFT JOIN '._DB_PREFIX_.'feature_shop AS fs
				ON f.`id_feature` = fs.`id_feature`
			WHERE fl.`value` = \''.bqSQL($feature_value).'\'
			AND f.`id_feature` = \''.(int)$feature_id.'\'
			AND fl.`id_lang` = '.(int)$lang.'
			AND ';
		//add shops
		$query .= ($shop_count > 1) ? '(': '';
		$s = 1;
		foreach ($this->shops as $shop)
		{
			$query .= 'fs.`id_shop` = '.(int)$shop;
			if ($s != $shop_count)
				$query .= ' OR ';
			$s++;
		}
		$query .= ($shop_count > 1) ? ')': '';

		$query = Db::getInstance()->executeS($query);

		return (isset($query[0]['id_feature_value'])) ? $query[0]['id_feature_value'] : null;
	}

	/* Get label id for customization field
	 *
	 * @param (string) id_field
	 * @param (string) `ue
	 */
	public function getLabelId($label, $type)
	{
		$label_id = false;
		foreach ($label as $lang => $name)
		{
			$customization = Db::getInstance()->executeS('
				SELECT l.`id_customization_field`
				FROM `'._DB_PREFIX_.'customization_field_lang` as l
				LEFT JOIN `'._DB_PREFIX_.'customization_field` as c
				ON l.`id_customization_field` = c.`id_customization_field`
				WHERE l.`name` = "'.bqSQL($name).'"
				AND c.`type` = '.$type.'
				AND l.`id_lang` = '.$lang.'
			');

			if ($customization)
			{
				$label_id = $customization[0]['id_customization_field'];
				break;
			}
		}
		return $label_id;
	}

	/*
	 * Get product id
	 *
	 * @param (string) id_field
	 * @param (string) field_value
	 */
	public function getProductId($id_field, $field_value)
	{
		$shop_count = count($this->shops);
		$lang_count = count($this->languages);

		if ($id_field == 'name')
		{
			$query = 'SELECT id_product FROM '._DB_PREFIX_.'product_lang
			WHERE `name` = \''.bqSQL(Tools::strtolower($field_value)).'\' AND ';
			//add shops and language
			$query .= ($shop_count > 1) ? '(': '';
			$s = 1;
			foreach ($this->shops as $shop)
			{
				$query .= '`id_shop` = '.(int)$shop;
				if ($s != $shop_count)
					$query .= ' OR ';
				$s++;
			}
			$query .= ($shop_count > 1) ? ')': '';

			$query .= ' AND ';
			$query .= ($lang_count > 1) ? '(': '';
			$l = 1;
			foreach ($this->languages as $lang)
			{
				$query .= '`id_shop` = '.(int)$lang;
				if ($l != $lang_count)
					$query .= ' OR ';
				$l++;
			}
			$query .= ($lang_count > 1) ? ')': '';
			$query = Db::getInstance()->executeS($query);
		}
		elseif ($id_field == 'id_product_attribute')
		{
			$query = '
			SELECT pa.`id_product` FROM '._DB_PREFIX_.'product_attribute AS pa
			LEFT JOIN '._DB_PREFIX_.'product_attribute_shop AS pas
				ON pa.`id_product_attribute` = pas.`id_product_attribute`
			WHERE pa.`'.bqSQL($id_field).'` = \''.bqSQL($field_value).'\' AND ';
			//add shops
			$query .= ($shop_count > 1) ? '(': '';
			$s = 1;
			foreach ($this->shops as $shop)
			{
				$query .= 'pas.`id_shop` = '.(int)$shop;
				if ($s != $shop_count)
					$query .= ' OR ';
				$s++;
			}
			$query .= ($shop_count > 1) ? ')': '';
			$query = Db::getInstance()->executeS($query);
		}
		else
		{
			$query = '
			SELECT p.id_product FROM '._DB_PREFIX_.'product AS p
			LEFT JOIN '._DB_PREFIX_.'product_shop AS ps
			ON p.`id_product` = ps.`id_product`
			WHERE p.`'.bqSQL($id_field).'` = \''.bqSQL($field_value).'\' AND ';
			//add shops
			$query .= ($shop_count > 1) ? '(': '';
			$s = 1;
			foreach ($this->shops as $shop)
			{
				$query .= 'ps.`id_shop` = '.(int)$shop;
				if ($s != $shop_count)
					$query .= ' OR ';
				$s++;
			}
			$query .= ($shop_count > 1) ? ')': '';
			$query = Db::getInstance()->executeS($query);
		}

		return (isset($query[0]['id_product'])) ? $query[0]['id_product'] : 0;
	}

	/* Get combination id using attributes
	 *
	 * @param $attributes array of attribute IDs
	 * @param $product_id int
	 */
	public function getCombinationByAttributes($attributes, $product_id)
	{
		$combination_id = 0; //default
		//get all combinations for a product
		$combination_ids = $this->getCombinationId('id_product', $product_id);
		$attribute_list = array();
		foreach ($combination_ids as $combination_id)
		{
			$shop_count = count($this->shops);
			$query = '
			SELECT DISTINCT pac.id_attribute FROM '._DB_PREFIX_.'product_attribute_combination AS pac
			LEFT JOIN '._DB_PREFIX_.'product_attribute_shop AS pas
				ON pac.`id_product_attribute` = pas.`id_product_attribute`
			WHERE pac.`id_product_attribute` = \''.(int)$combination_id['id_product_attribute'].'\' AND ';
			//add shops
			$query .= ($shop_count > 1) ? '(': '';
			$s = 1;
			foreach ($this->shops as $shop)
			{
				$query .= 'pas.`id_shop` = '.(int)$shop;
				if ($s != $shop_count)
					$query .= ' OR ';
				$s++;
			}
			$query .= ($shop_count > 1) ? ')': '';
			$query = Db::getInstance()->executeS($query);
			foreach ($query as $attribute_id)
				$attribute_list[$combination_id['id_product_attribute']][] = $attribute_id['id_attribute'];
		}
		foreach ($attribute_list as $comb_id => $attr)
		{
			//check to see if attribute list for a combination matches feed attribute list, then use that combination id to update
			if ($attr == $attributes)
				return $comb_id;
		}
		// Return 0 if no matches are found
		return 0;
	}

	/* Get combination id
	 *
	 * @param (string) id_field
	 * @param (string) field_value
	 */
	public function getCombinationId($id_field, $field_value)
	{
		$shop_count = count($this->shops);
		$query = '
			SELECT DISTINCT pa.id_product_attribute FROM '._DB_PREFIX_.'product_attribute AS pa
			LEFT JOIN '._DB_PREFIX_.'product_attribute_shop AS pas
				ON pa.`id_product_attribute` = pas.`id_product_attribute`
			WHERE pa.`'.bqSQL($id_field).'` = \''.bqSQL($field_value).'\' AND ';
		//add shops
		$query .= ($shop_count > 1) ? '(': '';
		$s = 1;
		foreach ($this->shops as $shop)
		{
			$query .= 'pas.`id_shop` = '.(int)$shop;
			if ($s != $shop_count)
				$query .= ' OR ';
			$s++;
		}
		$query .= ($shop_count > 1) ? ')': '';

		$query = Db::getInstance()->executeS($query);

		//need all for id product
		if ($id_field == 'id_product')
			return $query;

		return (isset($query[0]['id_product_attribute'])) ? $query[0]['id_product_attribute'] : 0;
	}

	/**
	 * Get level depth of a category
	 *
	 * @param int $category_id
	 * @return bool|mixed
	 */
	public function getCategoryLevel($category_id)
	{
		$query = new DbQuery();
		$query->select('c.`level_depth`');
		$query->from('category', 'c');
		$query->where('c.`id_category` = '.(int)$category_id
		);

		return ($res = Db::getInstance()->getValue($query)) ? $res : false;
	}

	/**
	 * For a given product and supplier, gets corresponding ProductSupplier ID
	 *
	 * @param int $id_product
	 * @param int $id_supplier
	 * @return bool|mixed
	 */
	public function getProductSupplier($id_product, $id_supplier)
	{
		$query = new DbQuery();
		$query->select('ps.id_product_supplier');
		$query->from('product_supplier', 'ps');
		$query->where('ps.id_product = '.(int)$id_product.'
			AND ps.id_supplier = '.(int)$id_supplier
		);
		return ($res = Db::getInstance()->getValue($query)) ? $res : false;
	}

	/** Get amount of products from hj_import table
	 *
	 */
	public function getProductRows()
	{
		return Db::getInstance()->getValue('SELECT count(*) FROM '._DB_PREFIX_.'hj_import');
	}

	/** Gets active shops for a product with a specific price
	 *
	 * @param $product_id
	 * @param int $combination_id
	 * @return array $price_shops
	 */
	public function getSpecificPriceShops($product_id, $combination_id = 0)
	{
		$price_shops = array();
		//for all shops id_shop = 0
		$shops = array_merge($this->shops, array(0));

		foreach ($shops as $shop)
		{
			$query = '
				SELECT DISTINCT s.`id_specific_price`
				FROM `'._DB_PREFIX_.'specific_price` as s
				WHERE  s.`id_product` = '.(int)$product_id.'
				AND s.`id_shop` = '.(int)$shop;

			$query .= ' AND s.`id_product_attribute` = '.(int)$combination_id;

			if ($price = Db::getInstance()->executeS($query))
			{
				if ($price[0]['id_specific_price'])
					$price_shops[] = $shop;
			}
		}

		return ($price_shops) ? $price_shops : 0;
	}

	public function getStockByProductId($product_id, $warehouse_id)
	{
		$query = new DbQuery();
		$query->select('id_stock');
		$query->from('stock');
		$query->where('id_product = "'.(int)$product_id.'"');
		$query->where('id_warehouse = "'.(int)$warehouse_id.'"');
		return (Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query));
	}

	/**
	 * Gets active suppliers
	 */
	public function getSuppliers()
	{
			$suppliers = array();
			$store_suppliers = Db::getInstance()->executeS('
				SELECT DISTINCT s.`id_supplier`, s.`name`, ss.`id_shop`
				FROM `'._DB_PREFIX_.'supplier` as s
				INNER JOIN `'._DB_PREFIX_.'supplier_shop` as ss
					ON  ss.`id_supplier` = s.`id_supplier`
				WHERE  s.`active` = 1
				ORDER BY ss.`id_shop`'
			);
			if (!empty($store_suppliers))
			{
					foreach ($store_suppliers as $store_supplier)
							$suppliers[$store_supplier['name']] = $store_supplier['id_supplier'];
			}
			return $suppliers;
	}

	/**
	 * Get Warehouse Id by reference
	 */
	public function getWarehouseId($reference)
	{
		$query = new DbQuery();
		$query->select('id_warehouse');
		$query->from('warehouse');
		$query->where('reference = "'.Db::getInstance(_PS_USE_SQL_SLAVE_)->escape($reference).'"');
		$query->where('deleted = 0');
		return (Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query));
	}

	public function unzip($file)
	{
		$filename = $file;
		$zip = zip_open($file);
		if (is_resource($zip))
		{
			$zip_entry = zip_read($zip);
			$filename = zip_entry_name($zip_entry);
			$fp = fopen($filename, 'w');
			if (zip_entry_open($zip, $zip_entry, 'r'))
			{
				$buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
				fwrite($fp, "$buf");
				zip_entry_close($zip_entry);
				fclose($fp);
			}
			zip_close($zip);
		}
		return $filename;
	}

	/* Auto-format invalid values to meet PS's validation standards
	 *
	 * @param $value, mixed
	 * @param $data,mixed
	 *
	 * @return bool
	 */
	public function autoValidate($value, $data)
	{
		//validation functions
		$validate_func = array('ValidateCore', $data['validate']);
		if (is_callable($validate_func))
		{
			$valid = call_user_func_array($validate_func, array($value));
			if (!$valid)
			{
				//p($value);
				$validate_prod_func = array($this, $data['validate']);
				if (is_callable($validate_prod_func))
					$new_value = call_user_func_array($validate_prod_func, array($value));
			}
		}
		//size
		// if (isset($data['size']))
		// 	$new_value = (isset($new_value)) ? Tools::substr($new_value, 0, $data['size'] - 1) : Tools::substr($value, 0, $data['size'] - 1);
		//match values
		if (isset($data['values']))
		{
			if (!in_array($value, $data['values']))
				$new_value = $data['default']; //set to default
		}
		return isset($new_value) ? $new_value : false;
	}

	/** Associating carrier with a shop
	 *
	 * @param $carrier int, carrier id
	 * @param $shop int, shop id
	 */
	private function addCarrierToShop($carrier, $shop)
	{
		Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'carrier_shop` (`id_carrier`, `id_shop`)
			VALUES ('.(int)$carrier.', '.(int)$shop.')
		');
	}

	/**
	 * Sets carriers assigned to the product
	 *
	 * @param $carrier_list array
	 * @param $product_id int
	 */
	private function assignCarriersToProduct($carrier_list, $product_id)
	{
		foreach ($this->shops as $shop)
		{
			$data = array();
			foreach ($carrier_list as $carrier)
			{
				$data[] = array(
					'id_product' => (int)$product_id,
					'id_carrier_reference' => (int)$carrier,
					'id_shop' => (int)$shop
				);
			}
			Db::getInstance()->execute(
				'DELETE FROM `'._DB_PREFIX_.'product_carrier`
				WHERE id_product = '.(int)$product_id.'
				AND id_shop = '.(int)$shop
			);
			if ($data)
				Db::getInstance()->insert('product_carrier', $data);
		}
	}

	/**
	 * Assign default category to a product
	 *
	 * @param $category_id int
	 * @param $product_id int
	 */
	private function assignDefaultCategory($category_id, $product_id)
	{
		$this->logger->logInfo(sprintf('Assigning default Category ID: %s, for Product ID: %s', $category_id, $product_id));
		$query = 'UPDATE '._DB_PREFIX_.'product_shop AS ps
			SET ps.`id_category_default` = '.(int)$category_id.'
			WHERE ps.`id_product` = '.(int)$product_id.'
			AND';

		$shop_count = count($this->shops);
		$query .= ($shop_count > 1) ? '(': '';
		$shop_num = 1;
		foreach ($this->shops as $shop)
		{
			$query .= '`id_shop` = '.(int)$shop;
			if ($shop_num != $shop_count)
				$query .= ' OR ';
			$shop_num++;
		}
		$query .= ($shop_count > 1) ? ')': '';

		Db::getInstance()->execute($query);

		$query = 'UPDATE '._DB_PREFIX_.'product AS p
			SET p.`id_category_default` = '.(int)$category_id.'
			WHERE p.`id_product` = '.(int)$product_id;

		Db::getInstance()->execute($query);
	}

	/** Set default supplier to product
	 *
	 * @param (int) supplier_id
	 * @param (int) product_id
	 */
	private function assignDefaultSupplier($supplier_id, $product_id)
	{
		$this->logger->logInfo(sprintf('Assigning default Supplier ID: %s, for Product ID: %s', $supplier_id, $product_id));
		Db::getInstance()->update('product', array(
				'id_supplier' => (int)$supplier_id,
			), 'id_product = '.(int)$product_id);
	}

	/** Associate a field to a shop
	 *
	 * @param (int) $field_id
	 * @param (int) $field
	 */
	private function associateFieldToShop($field_id, $field)
	{
		foreach ($this->associate_shops as $shop)
		{
			$query = '
			   INSERT INTO `'._DB_PREFIX_.bqSQL($field).'_shop` (`id_'.bqSQL($field).'`, `id_shop`)
				VALUES ('.(int)$field_id.', '.(int)$shop.')';

			if ($field == 'feature')
				$query .= 'ON DUPLICATE KEY UPDATE `id_feature` = '.(int)$field_id;

			Db::getInstance()->execute($query);
		}
	}

	/** Resize Image
	 *
	 * @param $file string
	 * @param $path string
	 * @param $id_image int
	 * @param $id_product int
	 * @param $thumbnail bool
	 */
	private function addImage($file, $path, $id_image, $id_product, $thumbnail = true)
	{
		$image_created = ImageManager::resize($file, $path.'.jpg');
		$images_types = ImageType::getImagesTypes('products');
		$watermark_types = explode(',', Configuration::get('WATERMARK_TYPES'));

		if ($thumbnail)
			foreach ($images_types as $image_type)
			{
				ImageManager::resize($file, $path.'-'.Tools::stripslashes($image_type['name']).'.jpg', $image_type['width'], $image_type['height']);
				if (in_array($image_type['id_image_type'], $watermark_types))
					Hook::exec('actionWatermark', array('id_image' => $id_image, 'id_product' => $id_product));
			}
		return $image_created;
	}

	/*
	 * Add Product to customization field
	 *
	 * @param $product_id int
	 * @param $custom_id intf
	 * @param $type int
	 *
	 */
	private function addProductToLabel($product_id, $custom_id, $type)
	{
		Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'customization_field` (`id_customization_field`, `id_product`, `type`, `required`)
			VALUES ('.(int)$custom_id.', '.(int)$product_id.', '.(int)$type.', 0)');
	}

	private function createLabel($names, $type, $product_id)
	{
		// Label insertion
		if (!Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'customization_field` (`id_product`, `type`, `required`)
			VALUES ('.(int)$product_id.', '.(int)$type.', 0)')
			|| !$id_customization_field = (int)Db::getInstance()->Insert_ID())
			return false;

		// Multilingual label name creation
		$values = '';
		foreach ($names as $lang => $name)
			$values .= '('.(int)$id_customization_field.', '.(int)$lang.', "'.$name.'"), ';

		$values = rtrim($values, ', ');
		if (!Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'customization_field_lang` (`id_customization_field`, `id_lang`, `name`)
			VALUES '.$values))
			return false;

		return true;
	}

	/**
	 * Create the import settings table
	 */
	private function createTables()
	{
		$sql = '
			CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'hj_import_settings (
				`id` INT(11) AUTO_INCREMENT,
				`group` VARCHAR(255),
				`step` INT(11),
				`name` BLOB,
				`value` BLOB,
				PRIMARY KEY (id))
			';

		return Db::getInstance()->execute($sql);
	}

	/**
	 * Delete product images per combination from database
	 *
	 * @param $id_product
	 * @return bool success
	 */
	private function deleteCombinationImages($id_product)
	{
		$query = '
			SELECT pi.`id_image`
			FROM '._DB_PREFIX_.'product_attribute_image as pi
			LEFT JOIN '._DB_PREFIX_.'product_attribute as pa
				ON pi.`id_product_attribute` = pa.`id_product_attribute`
			LEFT JOIN '._DB_PREFIX_.'product_attribute_shop AS pas
				ON pa.`id_product_attribute` = pas.`id_product_attribute`
			WHERE pa.`id_product` = '.(int)$id_product.' AND ';

		$shop_count = count($this->shops);
		$query .= ($shop_count > 1) ? '(': '';
		$s = 1;
		foreach ($this->shops as $shop)
		{
			$query .= 'pas.`id_shop` = '.(int)$shop;
			if ($s != $shop_count)
				$query .= ' OR ';
			$s++;
		}
		$query .= ($shop_count > 1) ? ')': '';

		$result = Db::getInstance()->executeS($query);

		$status = true;
		if ($result)
		{
			foreach ($result as $row)
			{
				$image = new Image($row['id_image']);
				$status &= $image->delete();
			}
		}

		return $status;
	}

	private function deleteTables()
	{
		$hj_import = Db::getInstance()->execute('
			DROP TABLE IF EXISTS
			`'._DB_PREFIX_.'hj_import`
		');

		$hj_import_settings = Db::getInstance()->execute('
			DROP TABLE IF EXISTS
			`'._DB_PREFIX_.'hj_import_settings`
		');

		if ($hj_import_settings && $hj_import)
			return true;
		else
			return false;
	}

	private function resetCombinations()
	{
		Db::getInstance()->delete('attribute_impact');
		Db::getInstance()->delete('product_attribute');
		Db::getInstance()->delete('product_attribute_shop');
		Db::getInstance()->delete('product_attribute_combination');
		Db::getInstance()->delete('product_attribute_image');

		Db::getInstance()->delete('attribute');
		Db::getInstance()->delete('attribute_impact');
		Db::getInstance()->delete('attribute_lang');
		Db::getInstance()->delete('attribute_group');
		Db::getInstance()->delete('attribute_group_lang');
		Db::getInstance()->delete('attribute_group_shop');
		Db::getInstance()->delete('attribute_shop');
		Db::getInstance()->delete('product_attribute');
		Db::getInstance()->delete('product_attribute_shop');
		Db::getInstance()->delete('product_attribute_combination');
		Db::getInstance()->delete('product_attribute_image');
		Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'stock_available` WHERE id_product_attribute !=0');
	}

	private function emptyTables()
	{
		//product tables
		Db::getInstance()->delete('product');
		Db::getInstance()->delete('product_shop');
		Db::getInstance()->delete('feature_product');
		Db::getInstance()->delete('product_lang');
		Db::getInstance()->delete('category_product');
		Db::getInstance()->delete('product_tag');
		Db::getInstance()->delete('image');
		Db::getInstance()->delete('image_lang');
		Db::getInstance()->delete('image_shop');
		Db::getInstance()->delete('specific_price');
		Db::getInstance()->delete('specific_price_priority');
		Db::getInstance()->delete('product_carrier');
		Db::getInstance()->delete('cart_product');
		Db::getInstance()->delete('compare_product');
		if (count(Db::getInstance()->executeS('SHOW TABLES LIKE \''._DB_PREFIX_.'favorite_product\' '))) //check if table exist
			Db::getInstance()->delete('favorite_product');
		Db::getInstance()->delete('product_attachment');
		Db::getInstance()->delete('product_country_tax');
		Db::getInstance()->delete('product_download');
		Db::getInstance()->delete('product_group_reduction_cache');
		Db::getInstance()->delete('product_sale');
		Db::getInstance()->delete('product_supplier');
		Db::getInstance()->delete('scene_products');
		Db::getInstance()->delete('warehouse_product_location');
		Db::getInstance()->delete('stock');
		Db::getInstance()->delete('stock_available');
		Db::getInstance()->delete('stock_mvt');
		Db::getInstance()->delete('customization');
		Db::getInstance()->delete('customization_field');
		Db::getInstance()->delete('supply_order_detail');
		Db::getInstance()->delete('attribute_impact');
		Db::getInstance()->delete('product_attribute');
		Db::getInstance()->delete('product_attribute_shop');
		Db::getInstance()->delete('product_attribute_combination');
		Db::getInstance()->delete('product_attribute_image');
		Image::deleteAllImages(_PS_PROD_IMG_DIR_);
		if (!file_exists(_PS_PROD_IMG_DIR_))
			mkdir(_PS_PROD_IMG_DIR_);

		//categories, keep root and home
		Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'category`
			WHERE id_category NOT IN ('.(int)Configuration::get('PS_HOME_CATEGORY').
			', '.(int)Configuration::get('PS_ROOT_CATEGORY').')');
		Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'category_lang`
			WHERE id_category NOT IN ('.(int)Configuration::get('PS_HOME_CATEGORY').
			', '.(int)Configuration::get('PS_ROOT_CATEGORY').')');
		Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'category_shop`
			WHERE `id_category` NOT IN ('.(int)Configuration::get('PS_HOME_CATEGORY').
			', '.(int)Configuration::get('PS_ROOT_CATEGORY').')');
		Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'category` AUTO_INCREMENT = 3');
		foreach (scandir(_PS_CAT_IMG_DIR_) as $d)
		{
			if (preg_match('/^[0-9]+(\-(.*))?\.jpg$/', $d))
				unlink(_PS_CAT_IMG_DIR_.$d);
		}
		//combinations
		Db::getInstance()->delete('attribute');
		Db::getInstance()->delete('attribute_impact');
		Db::getInstance()->delete('attribute_lang');
		Db::getInstance()->delete('attribute_group');
		Db::getInstance()->delete('attribute_group_lang');
		Db::getInstance()->delete('attribute_group_shop');
		Db::getInstance()->delete('attribute_shop');
		Db::getInstance()->delete('product_attribute');
		Db::getInstance()->delete('product_attribute_shop');
		Db::getInstance()->delete('product_attribute_combination');
		Db::getInstance()->delete('product_attribute_image');
		Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'stock_available` WHERE id_product_attribute !=0');
		//manufacturer
		Db::getInstance()->delete('manufacturer');
		Db::getInstance()->delete('manufacturer_lang');
		Db::getInstance()->delete('manufacturer_shop');
		foreach (scandir(_PS_MANU_IMG_DIR_) as $d)
		{
			if (preg_match('/^[0-9]+(\-(.*))?\.jpg$/', $d))
				unlink(_PS_MANU_IMG_DIR_.$d);
		}
		//supplier
		Db::getInstance()->delete('supplier');
		Db::getInstance()->delete('supplier_lang');
		Db::getInstance()->delete('supplier_shop');
		foreach (scandir(_PS_SUPP_IMG_DIR_) as $d)
		{
			if (preg_match('/^[0-9]+(\-(.*))?\.jpg$/', $d))
				unlink(_PS_SUPP_IMG_DIR_.$d);
		}
		Image::clearTmpDir();
	}

	/**
	 * Delete image(s) from database
	 *
	 * @param $product_id
	 * @param int $image_id
	 * @return bool success
	 */
	private function deleteImages($product_id, $image_id = 0)
	{
		if ($image_id)
			$result = array(array('id_image' => $image_id));
		else
		{
			$result = Db::getInstance()->executeS('
			SELECT `id_image`
			FROM `'._DB_PREFIX_.'image`
			WHERE `id_product` = '.(int)$product_id
			);
		}

		$status = true;
		if ($result && !$image_id)
			foreach ($result as $row)
			{
				$image = new Image($row['id_image']);
				$status &= $image->deleteImage(); //delete from disk
				//delete from db
				$query = '
					DELETE i, l, s
					FROM '._DB_PREFIX_.'image AS i
					LEFT JOIN '._DB_PREFIX_.'image_lang AS l
						ON i.`id_image` = l.`id_image`
					LEFT JOIN '._DB_PREFIX_.'image_shop AS s
						ON i.`id_image` = s.`id_image`
					WHERE i.`id_image` = '.$row['id_image'].'
					AND
				';
				$shop_count = count($this->associate_shops);
				$query .= ($shop_count > 1) ? '(': '';
				$s = 1;
				foreach ($this->associate_shops as $shop)
				{
					$query .= 's.`id_shop` = '.(int)$shop;
					if ($s != $shop_count)
						$query .= ' OR ';
					$s++;
				}
				$query .= ($shop_count > 1) ? ')': '';
				Db::getInstance()->execute($query);
			}

		// update positions
		$result = Db::getInstance()->executeS('
			SELECT *
			FROM `'._DB_PREFIX_.'image`
			WHERE `id_product` = '.(int)$product_id.'
			ORDER BY `position`
		');

		$i = 1;
		if ($result)
			foreach ($result as $row)
			{
				$row['position'] = $i++;
				Db::getInstance()->update($this->def['table'], $row, '`id_image` = '.(int)$row['id_image'], 1);
			}

		return $status;
	}

	/* Delete specific prices for a product by shop
	 *
	 * @param $product_id int
	 * @param $shops array
	 */
	private function deleteSpecificPriceByShop($product_id, $combination_id = 0)
	{
		//for all shops id_shop = 0
		$shops = array_merge($this->shops, array(0));

		foreach ($shops as $shop_id)
		{
			$query = '
				DELETE FROM `'._DB_PREFIX_.'specific_price`
				WHERE `id_product` = '.(int)$product_id.'
				AND `id_shop` = '.(int)$shop_id;
			$query .= ' AND `id_product_attribute` = '.(int)$combination_id; //no combination id will be 0

			Db::getInstance()->execute($query);
		}
	}

	/* Disable a product's status
	 *
	 * @param $product_id int
	 */
	private function disableProduct($product_id)
	{
		//update product shop
		$shop_count = count($this->shops);
		$query = 'UPDATE '._DB_PREFIX_.'product_shop SET `active` = 0 WHERE `id_product` = '.(int)$product_id;
		//add shops and language
		if ($shop_count > 1)
		{
			$query .= ' AND (';
			$shop_num = 1;
			foreach ($this->shops as $shop)
			{
				$query .= '`id_shop` = '.(int)$shop;
				if ($shop_num != $shop_count)
					$query .= ' OR ';
				$shop_num++;
			}
			$query .= ')';
		}

		Db::getInstance()->execute($query);

		//if id_shop_default is in shop list update product table
		$id_shop_default = Db::getInstance()->executeS('SELECT id_shop_default FROM '._DB_PREFIX_.'product WHERE id_product = '.(int)$product_id);
		if (in_array($id_shop_default[0]['id_shop_default'], $this->shops))
			Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'product_shop SET `active` = 0 WHERE `id_product` = '.(int)$product_id);
	}

	/* Insert product from feeds into the hj_import table
	 *
	 * @param $product array
	 */
	private function insertProduct($product)
	{
		$sql = 'INSERT INTO '._DB_PREFIX_.'hj_import SET ';
		$values = array();
		foreach ($product as $key => $value)
		{
			if ($this->file_encoding != 'UTF-8')
			{
				//headings already converted
				$value = iconv($this->file_encoding, 'UTF-8//TRANSLIT', $value);
			}
			$key = trim($key);
			$value = htmlentities(trim($value)); //escape removes html
			$values[] = '`'.$key."`='".bqSQL($value)."'";
		}
		$sql .= implode(',', $values);
		Db::getInstance()->execute($sql);
	}

	private function getExistingProducts($identifier = 'reference')
	{
		$product_table = array('ean13', 'upc', 'reference');
		$shop_count = count($this->shops);
		if (in_array($identifier, $product_table))
		{
			$sql = 'SELECT prod.'.$identifier.' FROM '._DB_PREFIX_.'product prod JOIN '._DB_PREFIX_.'product_shop p_shop ON prod.id_product = p_shop.id_product WHERE ';
			$s = 1;
			foreach ($this->shops as $shop)
			{
				$sql .= 'p_shop.id_shop = '.(int)$shop;
				if ($s != $shop_count)
					$sql .= ' OR ';
				$s++;
			}
			$query = Db::getInstance()->executeS($sql);
		}
		else
		{
			foreach ($this->languages as $language)
			{
				$sql = 'SELECT '.$identifier.' FROM '._DB_PREFIX_.'product_lang as pl
					LEFT JOIN '._DB_PREFIX_.'product_shop AS ps
					ON pl.`id_product` = ps.`id_product`
					WHERE pl.`id_lang` = '.(int)$language.' AND ';
				$sql .= ($shop_count > 1) ? '(': '';
				$s = 1;
				foreach ($this->shops as $shop)
				{
					$sql .= 'ps.`id_shop` = '.(int)$shop;
					if ($s != $shop_count)
						$sql .= ' OR ';
					$s++;
				}
				$sql .= ($shop_count > 1) ? ')': '';
				$query[] = Db::getInstance()->executeS($sql);
			}
		}
		$prod_array = array();
		foreach ($query[0] as $row)
			$prod_array[$row[$identifier]] = 0;
		return $prod_array;
	}

	/* Get the next product to import
	 *
	 */
	private function getNextProduct($start = 0)
	{
		$query = 'SELECT * FROM '._DB_PREFIX_.'hj_import LIMIT '.(int)$start.', 1';
		$product = Db::getInstance()->executeS($query);
		$fields = array();
		if (isset($product[0]))
		{
			foreach ($product[0] as $field => $value)
				$fields[$field] = html_entity_decode($value);
		}

		return ($fields) ? $fields : 0;
	}

	private function getSavedSettingNames()
	{
		$groups = Db::getInstance()->executeS('SELECT DISTINCT(`group`) FROM '._DB_PREFIX_.'hj_import_settings');
		$names = array();
		foreach ($groups as $row)
			$names[] = $row['group'];
		return $names;
	}

	private function loadSettings($name)
	{
		for ($i = 1; $i <= 5; $i++)
		{
			$settings = array();
			$query = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'hj_import_settings WHERE `group` = \''.bqSQL($name).'\' AND `step` = '.$i);

			foreach ($query as $result)
				$settings[$result['name']] = unserialize($result['value']);
			$this->updateConfig('IMPORT_STEP'.$i, serialize($settings));
		}
	}

	/**Removes carrier association to shop
	 *
	 * @param $carrier int, carrier id
	 * @param $shop int, shop id
	 */
	private function unlinkCarrierShop($carrier, $shop)
	{
		Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'carrier_shop`
			WHERE `id_carrier` = '.(int)$carrier.'
			AND `id_shop` = '.(int)$shop);
	}

	/** Set default supplier to product
	 *
	 * @param (int) product_id
	 */
	private function zeroQuantityProduct($product_id)
	{
		//if id_shop_default is in shop list update product table
		$id_shop_default = Db::getInstance()->executeS('SELECT id_shop_default FROM '._DB_PREFIX_.'product WHERE id_product = '.(int)$product_id);
		if (in_array($id_shop_default[0], $this->shops))
			Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'product SET `quantity` = 0 WHERE `id_product` = '.(int)$product_id);
	}

	public function columnExists($table, $column_name)
	{
		$sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS
		WHERE TABLE_NAME = '".$table."' AND TABLE_SCHEMA = '"._DB_NAME_."'
		AND COLUMN_NAME = '".$column_name."'";

		$query = Db::getInstance()->executeS($sql);
		return (isset($query[0]['COLUMN_NAME']) && $query[0]['COLUMN_NAME'] == $column_name) ? true : false;
	}

	/** OPERATIONS */

	/**
	 * @param (mixed) array(text to append to, field to adjust)
	 */
	public function appendText($adjustment)
	{
		$append_text = $adjustment[0];
		$field = $adjustment[1];
		Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'hj_import SET `'.$field.'` = CONCAT( `'.$field.'`, \''.bqSQL($append_text).'\' ) WHERE `'.$field.'` != \'\'');
	}

	/**
	 * @param (mixed) array(text to prepend to, field to adjust)
	 */
	public function prependText($adjustment)
	{
		$prepend_text = $adjustment[0];
		$field = $adjustment[1];
		Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'hj_import SET `'.$field.'` = CONCAT( \''.bqSQL($prepend_text).'\', `'.$field.'` ) WHERE `'.$field.'` != \'\'');
	}

	/**
	 * @param (mixed) array(text to remove, field to adjust)
	 */
	public function removeText($adjustment)
	{
		$remove_text = $adjustment[0];
		$field = $adjustment[1];
		Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'hj_import SET `'.$field.'` = REPLACE( `'.$field.'`, \''.bqSQL($remove_text).'\', \'\' )');
	}

	/**
	 * @param (mixed) array(text to find, text to replace with, field to adjust)
	 */
	public function replaceText($adjustment)
	{
		$str = $adjustment[0];
		$replacement = $adjustment[1];
		$field = $adjustment[2];
		Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'hj_import SET `'.$field.'` = REPLACE( `'.$field.'`, \''.bqSQL($str).'\', \''.bqSQL($replacement).'\' )');
	}

	/**
	 * @param (mixed) array(field to adjust)
	 */
	public function replaceNewLines($adjustment)
	{
		$new_lines = array("\r\n", "\n", "\r");
		$replacement = '<br />';
		$field = $adjustment[0];
		foreach ($new_lines as $str)
			Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'hj_import SET `'.$field.'` = REPLACE( `'.$field.'`, \''.bqSQL($str).'\', \''.bqSQL($replacement).'\' )');
	}

	/**
	 * @param (mixed) array(field to adjust, multiplication factor)
	 */
	public function multiply($adjustment)
	{
		$field = $adjustment[0];
		$multiplier = $adjustment[1];
		Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'hj_import SET `'.$field.'` = (`'.$field.'` * '.(float)$multiplier.' )');
	}

	/**
	 * @param (mixed) array(field to  add value, adjust)
	 */
	public function add($adjustment)
	{
		$add = $adjustment[0];
		$field = $adjustment[1];
		Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'hj_import SET `'.$field.'` = (`'.$field.'` + '.(float)$add.' )');
	}

	/**
	 * @param (mixed) array(field to  adjust, new field)
	 */
	public function duplicateField($adjustment)
	{
		$field = $adjustment[0];
		$newfield = $adjustment[1];
		if (!$this->columnExists(_DB_PREFIX_.'hj_import', $newfield))
		{
			Db::getInstance()->execute('ALTER TABLE '._DB_PREFIX_.'hj_import ADD `'.$newfield.'` BLOB');
			Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'hj_import SET `'.$newfield.'` = (`'.$field.'`)');
		}
	}

	/**
	 * @param (mixed) array(field to adjust)
	 */
	public function lowerCase($adjustment)
	{
		$field = $adjustment[0];
		Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'hj_import SET `'.$field.'` = LCASE( `'.$field.'` )');
	}

	/**
	 * @param (mixed) array(field to adjust)
	 */
	public function upperCase($adjustment)
	{
		$field = $adjustment[0];
		Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'hj_import SET `'.$field.'` = UCASE( '.$field.' )');
	}


	/**
	 * @param (mixed) array(field to adjust, text to look for)
	 */
	public function deleteRowsWhereContains($adjustment)
	{
		$field = $adjustment[0];
		$value = $adjustment[1];
		Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'hj_import WHERE `'.$field.'` LIKE \'%'.bqSQL($value).'%\'');
	}

	/**
	 * @param (mixed) array(field to adjust, text to look for)
	 */
	public function deleteRowsWhereNotContains($adjustment)
	{
		$field = $adjustment[0];
		$value = $adjustment[1];
		Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'hj_import WHERE `'.$field.'` NOT LIKE \'%'.bqSQL($value).'%\'');
	}
	/**
	 * @param (mixed) array(field to adjust, text to look for)
	 */
	public function deleteRowsWhere($adjustment)
	{
		$field = $adjustment[0];
		$value = $adjustment[1];
		Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'hj_import WHERE `'.$field.'` = \''.bqSQL($value).'\'');
	}

	/**
	 * @param (mixed) array(field to adjust, text to look for)
	 */
	public function deleteRowsWhereNot($adjustment)
	{
		$field = $adjustment[0];
		$value = $adjustment[1];
		Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'hj_import WHERE `'.$field.'` != \''.bqSQL($value).'\'');
	}

	/**
	 * @param (mixed) array(field to adjust, text to look for)
	 */
	public function mergeColumns($adjustment)
	{
		$field1 = $adjustment[0];
		$field2 = $adjustment[1];
		$separator = $adjustment[2];
		Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'hj_import SET `'.$field2.'` = CONCAT(`'.$field2.'`, \''.bqSQL($separator).'\', `'.$field1.'`)');
	}

	/**
	 * @param (mixed) array(field to adjust, text to look for)
	 */
	public function mergeRows($adjustment)
	{
		$common_field = array_shift($adjustment);

		//get all unique product id's
		$sql = 'SELECT DISTINCT `'.bqSQL($common_field).'` FROM '._DB_PREFIX_.'hj_import ORDER BY `'.bqSQL($common_field).'` DESC';
		$query = Db::getInstance()->executeS($sql);
		$unique_products = array();
		foreach ($query as $model)
			$unique_products[] = $model[$common_field];
		foreach ($unique_products as $unique)
		{
			//get each of the adjustment values and concatonate
			$sql = 'SELECT *, ';
			foreach ($adjustment as $adjust)
				$sql .= 'GROUP_CONCAT(`'.bqSQL($adjust).'` SEPARATOR \'|\') as `'.bqSQL($adjust).'`, ';
			$sql = Tools::substr($sql, 0, -2);
			$sql .= ' FROM `'._DB_PREFIX_.'hj_import` WHERE  `'.bqSQL($common_field).'`=\''.bqSQL($unique).'\' GROUP BY `'.bqSQL($common_field).'`';
			$query = Db::getInstance()->executeS($sql);

			$current_products = $query[0];

			//update the first product with the set sku
			$sql = 'UPDATE `'._DB_PREFIX_.'hj_import` SET ';
			foreach ($adjustment as $adjust)
				$sql .= '`'.bqSQL($adjust).'` = \''.bqSQL($current_products[$adjust]).'\', ';
			$sql = Tools::substr($sql, 0, -2);
			$sql .= " WHERE `hj_id` = '".bqSQL($current_products['hj_id'])."'";
			$query = Db::getInstance()->execute($sql);

			//remove all additional products
			$sql = 'DELETE FROM `'._DB_PREFIX_.'hj_import` WHERE ';
			$sql .= '`hj_id` != \''.bqSQL($current_products['hj_id']).'\' AND `'.bqSQL($common_field).'` = \''.bqSQL($unique).'\'';
			$query = Db::getInstance()->execute($sql);
		}

	}

	public function customColumn($adjustment)
	{
		$column_name = str_replace("'", '', $adjustment[0]);
		$column_value = $adjustment[1];
		if (!$this->columnExists(_DB_PREFIX_.'hj_import', $column_name))
			$this->alterImportTable(array($column_name));
		$sql = 'UPDATE '._DB_PREFIX_.'hj_import SET `'.$column_name.'` = \''.$column_value.'\'';
		Db::getInstance()->execute($sql);
	}

	public function splitCombinations($adjustment)
	{
		$product_id_field = array_shift($adjustment);
		$split_delim = array_shift($adjustment);

		// Make Combination Field Names unique, must not contain product ID field either
		$combination_field_names = array_unique($adjustment);
		if (empty($combination_field_names))
			return false;
		$pos = array_search($product_id_field, $combination_field_names);
		if ($pos !== false)
			unset($combination_field_names[$pos]);

		$sql = 'SELECT `hj_id`, `'.$product_id_field.'`';
		// Select all combination fields that need to be split
		foreach ($combination_field_names as $combination_field)
			$sql .= ', `'.$combination_field.'`';
		$sql .= ' FROM `'._DB_PREFIX_.'hj_import`';
		$feed_rows = Db::getInstance()->executeS($sql);


		foreach ($feed_rows as $product_row)
		{
			$product_id_field_val = $product_row[$product_id_field];
			$exploded_fields = array();

			// First update the original product row
			$sql = 'UPDATE `'._DB_PREFIX_.'hj_import` SET ';

			$max_split_num = 0;
			foreach ($combination_field_names as $combination_field)
			{
				$exploded_fields[$combination_field] = array_map('trim', explode($split_delim, $product_row[$combination_field]));

				if (count($exploded_fields[$combination_field]) > $max_split_num)
					$max_split_num = count($exploded_fields[$combination_field]);

				$sql .= '`'.$combination_field.'` = \''.$exploded_fields[$combination_field][0].'\', ';
			}
			$sql = rtrim($sql, ', ').' WHERE `hj_id` = '.$product_row['hj_id'];
			Db::getInstance()->execute($sql);

			// then insert a new row for each remaining combinations
			for ($combination_num = 1; $combination_num < $max_split_num; $combination_num++)
			{
				$sql = 'INSERT INTO `'._DB_PREFIX_.'hj_import` SET `'.$product_id_field.'` = \''.$product_id_field_val.'\'';
				foreach ($combination_field_names as $combination_field)
					$sql .= ', `'.$combination_field.'` = \''.$exploded_fields[$combination_field][$combination_num].'\'';
				Db::getInstance()->execute($sql);
			}
		}

	}

	public function getNextProductOrdered($start, $update_column)
	{
		$query = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'hj_import ORDER BY '.bqSQL($update_column).' DESC LIMIT '.(int)$start.', 1');
		return (isset($query[0])) ? $query[0] : 0;
	}

	/**
	 * @param (mixed) array(field name, separator to split on, and new column prefix)
	 */
	public function splitFields($adjustment)
	{
		$field1 = $adjustment[0];
		$separator = $adjustment[1];
		//get the max number of columns required
		$sql = 'SELECT MAX(LENGTH(`'.$field1.'`) - LENGTH(REPLACE(`'.$field1.'`, \''.$separator.'\', \'\'))) AS \'new_columns\' FROM `'._DB_PREFIX_.'hj_import`';

		$query = Db::getInstance()->executeS($sql);

		$new_columns = $query[0]['new_columns'] + 1;

		//create the new columns
		for ($i = 1; $i <= $new_columns; $i++)
		{
			$new_field = $field1.'_split_'.$i;
			if (!$this->columnExists(_DB_PREFIX_.'hj_import', $new_field))
			{
				$new_field = array($new_field);
				$this->alterImportTable($new_field);
			}
		}

		//update each column with the correct values
		$sql = 'SELECT `hj_id`, `'.$field1.'` FROM `'._DB_PREFIX_.'hj_import`';
		$query = Db::getInstance()->executeS($sql);

		foreach ($query as $row)
		{
			$values = explode($separator, $row[$field1]);
			//create the new columns
			$values_count = count($values);
			for ($i = 1; $i <= $values_count; $i++)
			{
				$new_field = $field1.'_split_'.$i;
				$sql = 'UPDATE `'._DB_PREFIX_.'hj_import` SET `'.bqSQL($new_field).'` = \''.bqSQL($values[$i - 1]).'\' WHERE `hj_id` = \''.bqSQL($row['hj_id']).'\'';
				$query = Db::getInstance()->execute($sql);
			}
		}
	}

	/** VALIDATION FUNCTIONS */

	/**
	 * Convert to integer
	 *
	 * @param string $ids
	 * @return bool
	 */
	public function isBool($ids)
	{
		return (bool)$ids;
	}

	/**
	 * Fix product or category name validity
	 *
	 * @param string $name Product or category name to validate
	 * @return string
	 */
	public function isCatalogName($name)
	{
		$special_chars = array('<', '>', ';' , '=', '#', '{', '}');
		foreach ($special_chars as $char)
			$name = str_replace($char, '', $name);
		return $name;
	}

	/**
	 * Check for clean html
	 *
	 * @param string $html HTML field to validate
	 * @return boolean Validity is ok or not
	 */
	public function isCleanHtml($html)
	{
		//set to blank if invalid so it can pass validation rules
		$html = 'empty';
		return $html;
	}

	/**
	 * Convert to date format
	 *
	 * @param string $date Date to validate
	 * @return string
	 */
	public function isDateFormat($date)
	{
		return date('Y-m-d H:i:s', strtotime($date));
	}

	/**
	 * Check for standard name validity
	 *
	 * @param string $name Name to validate
	 * @return boolean Validity is ok or not
	 */
	public function isGenericName($name)
	{
		if (empty($name))
			$name = 'No Title';

		$special_chars = array('<', '>', '=', '{', '}');
		foreach ($special_chars as $char)
			$name = str_replace($char, '', $name);

		return $name;
	}

	/**
	 * Check for barcode validity (EAN-13)
	 *
	 * @param string $ean13 Barcode to validate
	 * @return string
	 */
	public function isEan13($ean13)
	{
		return (preg_match('/^[0-9]{0,13}$/', $ean13)) ? $ean13 : '';
	}

	/**
	 * Check for a link (url-rewriting only) validity
	 *
	 * @param string $link Link to validate
	 * @return boolean Validity is ok or not
	 */
	public function isLinkRewrite($link)
	{
		return Tools::link_rewrite($link);
	}

	/**
	 * Check for price validity
	 *
	 * @param string $price Price to validate
	 * @return float
	 */
	public function isPrice($price)
	{
		return (float)number_format($price, 2, '.', '');
	}

	/**
	 * Check for product visibility
	 *
	 * @param string $visibility visibility to check
	 * @return string
	 */
	public function isProductVisibility($visibility)
	{
		//if invalid just set to both
		$visibility = 'both';
		return $visibility;
	}

	/**
	 * Make reference valid
	 *
	 * @param string $reference Product reference to validate
	 * @return boolean Validity is ok or not
	 */
	public function isReference($reference)
	{
		$special_chars = array('<', '>', ';' , '=', '#', '{', '}');
		foreach ($special_chars as $char)
			$reference = str_replace($char, '', $reference);
		return $reference;
	}

	/**
	 * Convert price display method validity
	 *
	 * @param string $data Data to validate
	 * @return string
	 */
	public function isString($data)
	{
		return $data;
	}

	/**
	 * Convert to float
	 *
	 * @param $float float
	 * @return float
	 */
	public function isUnsignedFloat($float)
	{
		return (float)$float;
	}

	/**
	 * Convert to int
	 *
	 * @param integer $id Integer to validate
	 * @return boolean Validity is ok or not
	 */
	public function isUnsignedId($id)
	{
		return $this->isUnsignedInt($id); /* Because an id could be equal to zero when there is no association */
	}

	/**
	 * Convert to integer
	 *
	 * @param int $value
	 * @return int
	 */
	public function isUnsignedInt($value)
	{
		$value = (int)$value;
		if ($value >= 4294967296)
			$value = 0;

		return $value;
	}

	/**
	 * Make UPC valid
	 *
	 * @param string $upc Barcode to validate
	 * @return string cleaned upc
	 */
	public function isUpc($upc)
	{
		return (preg_match('/^[0-9]{0,12}$/', $upc)) ? $upc : '';
	}
}
