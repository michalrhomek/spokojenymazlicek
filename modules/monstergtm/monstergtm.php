<?php
if (!defined('_PS_VERSION_'))
  exit;
 
class MonsterGtm extends Module {
  public function __construct() {
    $this->name = 'monstergtm';
    $this->tab = 'analytics_stats';
    $this->version = '1.0.0';
    $this->author = 'Monster Media (prestashopisti.cz)';
    $this->need_instance = 1;
    $this->ps_versions_compliancy = array('min' => '1.5.1', 'max' => '1.7.0'); 
    $this->bootstrap = true;
 
    parent::__construct();
 
    $this->displayName = 'Monster GTM';
    $this->description = $this->l('Kompletní Google Tag Manager implementace včetně datových vrstev pro měření e-commerce a dyn. remarketingu.');
 
    $this->confirmUninstall = $this->l('Opravdu si přejete modul odinstalovat?');
 
    if (!Configuration::get('monstergtm'))      
      $this->warning = $this->l('Je třeba nastavit GTM ID');
  }


  public function install() {
	  if (Shop::isFeatureActive())
	    Shop::setContext(Shop::CONTEXT_ALL);
	 
	  if (!parent::install() ||
	    !$this->registerHook('header') ||
	    !$this->registerHook('top') ||
    	!Configuration::updateValue('MONSTERGTM_GTMID', '')
	  )
	    return false;
	 
	  return true;
	}


	public function uninstall()
	{
	  if (!parent::uninstall() ||
	  	!Configuration::deleteByName('MONSTERGTM_GTMID')
	  )
	    return false;
	  return true;
	}


	public function hookDisplayHeader() {
	  $this->context->smarty->assign(
	      array(
	          'gtmid' => Configuration::get('MONSTERGTM_GTMID')
	      )
	  );
	  $pagetype = Tools::getValue('controller');
	  if ($pagetype=='order' || $pagetype=='payment' || $pagetype=='orderopc' || $pagetype=='supercheckout') $pagetype='order';

	  switch ($pagetype) {
	  	case 'index':
	  		//homepage settings:
	  		$this->context->smarty->assign(
			      array(
			          'ecomm_pagetype' => 'home'
			      )
			  );
	  		break;
	  	case 'category':
	  		//category settings:
	  		//$product = $this->context->controller->getProduct();
 			$category = new Category((int)Tools::getValue( 'id_category' ), (int)$this->context->language->id);
	  		$this->context->smarty->assign(
			      array(
			          'ecomm_pagetype' => 'category',
			          'ecomm_pcat' => "'".$category->name."'"
			      )
			  );
	  		break;
	  	case 'product':
	  		//product settings:
	  		$product = $this->context->controller->getProduct();
 			$category = new Category((int)$product->id_category_default, (int)$this->context->language->id);
	  		$this->context->smarty->assign(
			      array(
			          'ecomm_pagetype' => 'product',
			          'ecomm_pcat' => "'".$category->name."'",
			          'ecomm_prodid' => $product->id,
			          'ecomm_pname' => "'".$product->name."'"
			      )
			  );
	  		break;
	  	case 'order':
	  		//order settings:
	  		$products = Context::getContext()->cart->getProducts();
	  		$products_ids = array();
	  		$products_names = array();
	  		$products_categs = array();
	  		$products_prices = array();
	  		foreach ($products AS $product) {
	  			$products_ids[] = (int)$product['id_product'];
	  			$products_names[] = "'".$product['name']."'";
	  			$category = new Category((int)$product['id_category_default'], (int)$this->context->language->id);
	  			$products_categs[] = "'".$category->name."'";
	  			$products_prices[] = $product['total_wt'];
	  		}

	  		$this->context->smarty->assign(
			      array(
			          'ecomm_pagetype' => 'cart',
			          'ecomm_prodid' => implode(',',$products_ids),
			          'ecomm_pname' => implode(',',$products_names),
			          'ecomm_pcat' => implode(',',$products_categs),
			          'ecomm_pvalue' => implode(',',$products_prices),
			          'ecomm_totalvalue' => Context::getContext()->cart->getOrderTotal(true)
			      )
			  );
	  		break;
	  	case 'orderconfirmation':

	  		//order-confirmation settings:
	  		$order = new Order((int)$this->context->controller->id_order);
	  		$products = $order->getProducts();

	  		$products_ids = array();
	  		$products_names = array();
	  		$products_categs = array();
	  		$products_prices = array();
	  		$ecommerce_products = array();
	  		$i = 0;
	  		foreach ($products AS $product) {
	  			$products_ids[] = (int)$product['id_product'];
	  			$sql ='SELECT name FROM '._DB_PREFIX_.'product_lang WHERE 
	             id_product ='.(int)$product['id_product'] .' AND id_lang='.Configuration::get('PS_LANG_DEFAULT');
	            $product_name = Db::getInstance()->getValue($sql);
	  			$products_names[] = "'".$product_name."'";
	  			$category = new Category((int)$product['id_category_default'], (int)$this->context->language->id);
	  			$products_categs[] = "'".$category->name."'";
	  			$products_prices[] = $product['total_wt'];
	  			//e-commerce array
	  			$ecommerce_products[$i]['sku'] = "'".$product['id_product']."'";
	  			$ecommerce_products[$i]['name'] = "'".$product_name."'";
	  			$ecommerce_products[$i]['category'] = "'".$category->name."'";
	  			$ecommerce_products[$i]['price'] = $product['total_wt'];
	  			$ecommerce_products[$i]['quantity'] = $product['product_quantity'];
	  			$i++;
	  		}
	  		//dynamic remarketing vars
	  		$this->context->smarty->assign(
			      array(
			          'ecomm_pagetype' => 'purchase',
			          'ecomm_prodid' => implode(',',$products_ids),
			          'ecomm_pname' => implode(',',$products_names),
			          'ecomm_pcat' => implode(',',$products_categs),
			          'ecomm_pvalue' => implode(',',$products_prices),
			          'ecomm_totalvalue' => Tools::ps_round($order->total_paid_tax_incl,2)
			      )
			  );

	  		//e-commerce vars
	  		$only_tax = $order->total_paid_tax_incl - $order->total_paid_tax_excl;
	  		$this->context->smarty->assign(
			      array(
			          'transactionId' => $this->context->controller->id_order,
			          'transactionTotal' => Tools::ps_round($order->total_paid_tax_incl,2),
			          'transactionShipping' => Tools::ps_round($order->total_shipping_tax_excl,2),
			          'transactionTax' => Tools::ps_round($only_tax,2),
			          'transactionProducts' => $ecommerce_products
			      )
			  );
	  		break;
	  	default:
	  		$this->context->smarty->assign(
			      array(
			          'ecomm_pagetype' => 'other'
			      )
			  );
	  		break;
	  }

	  return $this->display(__FILE__, 'tagandlayers.tpl');
	}  

	public function hookDisplayTop() {
	  $this->context->smarty->assign(
	      array(
	          'gtmid' => Configuration::get('MONSTERGTM_GTMID')
	      )
	  );
	  return $this->display(__FILE__, 'noscript.tpl');
	}  


	public function getContent(){
    $output = null;
 
    if (Tools::isSubmit('submit'.$this->name)) {
	        $gtmid = strval(Tools::getValue('MONSTERGTM_GTMID'));
	        if (!$gtmid
	          || empty($gtmid)
	          || !Validate::isGenericName($gtmid))
	            $output .= $this->displayError($this->l('Vkládáte neplatné GTM id!'));
	        else
	        {
	            Configuration::updateValue('MONSTERGTM_GTMID', $gtmid);
	            $output .= $this->displayConfirmation($this->l('Nastavení modulu upraveno.'));
	        }
	    }
	    return $output.$this->displayForm();
	}

	public function displayForm(){
	    // Get default language
	    $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
	     
	    // Init Fields form array
	    $fields_form[0]['form'] = array(
	        'legend' => array(
	            'title' => $this->l('Nastavení GTM'),
	        ),
	        'input' => array(
	            array(
	                'type' => 'text',
	                'label' => $this->l('GTM ID:'),
	                'name' => 'MONSTERGTM_GTMID',
	                'size' => 20,
	                'desc' => $this->l('Vložte Vaše GTM ID ve tvaru: GTM-XXXXXXX'),
	                'required' => true
	            )
	        ),
	        'submit' => array(
	            'title' => $this->l('Uložit'),
	            'class' => 'btn btn-default pull-right'
	        )
	    );
	     
	    $helper = new HelperForm();
	     
	    // Module, token and currentIndex
	    $helper->module = $this;
	    $helper->name_controller = $this->name;
	    $helper->token = Tools::getAdminTokenLite('AdminModules');
	    $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
	     
	    // Language
	    $helper->default_form_language = $default_lang;
	    $helper->allow_employee_form_lang = $default_lang;
	     
	    // Title and toolbar
	    $helper->title = $this->displayName;
	    $helper->show_toolbar = true;        // false -> remove toolbar
	    $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
	    $helper->submit_action = 'submit'.$this->name;
	    $helper->toolbar_btn = array(
	        'save' =>
	        array(
	            'desc' => $this->l('Save'),
	            'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
	            '&token='.Tools::getAdminTokenLite('AdminModules'),
	        ),
	        'back' => array(
	            'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
	            'desc' => $this->l('Back to list')
	        )
	    );
	     
	    // Load current value
	    $helper->fields_value['MONSTERGTM_GTMID'] = Configuration::get('MONSTERGTM_GTMID');
	     
	    return $helper->generateForm($fields_form);
	}


}