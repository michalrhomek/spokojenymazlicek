<?php
  class AdminCpcController  extends AdminProductsControllerCore {
  	  
  public function renderList()
	{
		$this->context->smarty->assign('heureka_cpc', Tools::getValue('heureka_cpc')); 
		$this->context->smarty->assign('max_cpc', Tools::getValue('max_cpc')); 
		$this->context->smarty->assign('max_cpc_search', Tools::getValue('max_cpc_search')); 
		$this->context->smarty->assign('exp_sel', (int)Tools::getValue('exp_sel')); 
		
		return AdminController::renderList();
	}	  
  
  public function __construct() {
  	 parent::__construct(); 
  	  unset(	  $this->fields_list['price']);
  	  
	
		$this->fields_list['heureka_cpc'] = array(
			'title' => $this->l('Heureka cpc'),
			'type' => 'price',
			'align' => 'text-right',
			'havingFilter' => true,
			'orderby' => false,
			'search' => false
		);
		$this->fields_list['max_cpc'] = array(
			'title' => $this->l('Zboží CPC'),
			'type' => 'price',
			'align' => 'text-right',
			'havingFilter' => true,
			'orderby' => false,
			'search' => false
		);
		$this->fields_list['max_cpc_search'] = array(
			'title' => $this->l('Zboží CPC Search'),
			'type' => 'price',
			'align' => 'text-right',
			'havingFilter' => true,
			'orderby' => false,
			'search' => false
		);
  }
  
  
  public function postProcess()
	{
		parent::postProcess();
	   if(Tools::isSubmit('cmd_cpc')) {
	   	   
	        $set='';
	        $carka='';
	        $heureka_cpc = Tools::getValue("heureka_cpc");
	        $max_cpc   = Tools::getValue("max_cpc");
	        $max_cpc_search = Tools::getValue("max_cpc_search");
			if($heureka_cpc ||  $heureka_cpc === '0') {
			$heureka_cpc = str_replace(',', '.', $heureka_cpc);
			$set.= $carka.'heureka_cpc ='.$heureka_cpc;
			   $carka =',';
			}
			if($max_cpc ||  $max_cpc === '0') {
			$max_cpc = str_replace(',', '.', $max_cpc);
			$set.=  $carka.'max_cpc ='.$max_cpc;
			  $carka =',';
			}
			if($max_cpc_search ||  $max_cpc_search === '0') {
			$max_cpc_search = str_replace(',', '.', $max_cpc_search);	
			$set.=  $carka.'max_cpc_search ='.$max_cpc_search;
			  $carka =',';
			}
	        if(strlen($set)) {
	        	
	        if((int) Tools::getValue('exp_sel') == 1) {
	          $checkboxes = $_POST['productBox'];
	          if(!count($checkboxes))
	            $this->errors ='Nejsou vybrány žádné položky';
	          else {
	          	 $ids=array_values($checkboxes);
	          	 $in=implode(',', $ids); 
	          	 $sql= 'UPDATE  '._DB_PREFIX_.'product SET '.$set.' WHERE id_product IN ('.$in.')';
	          	 Db::getInstance()->execute($sql);
			  }
	        
			}
			else { 
	   	    $sql= 
	   	    'UPDATE '._DB_PREFIX_.'product a 
	   	    LEFT JOIN '._DB_PREFIX_.'product_lang b on a.id_product = b.id_product AND b.id_lang='.Configuration::get('PS_LANG_DEFAULT').' '.$this->_join.  ' SET '.$set.' '.$this->_where;
	   	    Db::getInstance()->execute($sql);
			}
	   	    
			}
	   	    
	   	   
	   }
	}  	  
  }

