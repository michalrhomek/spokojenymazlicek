<?php
/*
/*
* PrestaHost.cz / PrestaHost.eu
*
*
*  @author prestahost.eu <info@prestahost.cz>
*  @copyright  2014  PrestaHost.eu, Vaclav Mach
*  @license    http://prestahost.eu/prestashop-modules/en/content/3-terms-and-conditions-of-use
*/

/**
 * @since 1.5.0
 */
class CashondeliveryplusValidationModuleFrontController extends ModuleFrontController
{
	public $display_column_left = false;
	public $ssl = true;


	public function postProcess()
	{
		if ($this->context->cart->id_customer == 0 || $this->context->cart->id_address_delivery == 0 || $this->context->cart->id_address_invoice == 0 || !$this->module->active)
			Tools::redirectLink(__PS_BASE_URI__.'order.php?step=1');

		// Check that this payment option is still available in case the customer changed his address just before the end of the checkout process
		$authorized = false;
		foreach (Module::getPaymentModules() as $module)
			if ($module['name'] == 'cashondeliveryplus')
			{
				$authorized = true;
				break;
			}
		if (!$authorized)
			die(Tools::displayError('This payment method is not available.'));

		$customer = new Customer($this->context->cart->id_customer);
		if (!Validate::isLoadedObject($customer))
			Tools::redirectLink(__PS_BASE_URI__.'order.php?step=1');

		if (Tools::getValue('confirm'))
		{
			$customer = new Customer((int)$this->context->cart->id_customer);
			$total = $this->context->cart->getOrderTotal(true, Cart::BOTH)+ Tools::convertPrice($this->getDobirecne());
			$this->module->validateOrder((int)$this->context->cart->id, Configuration::get('PS_OS_PREPARATION'), $total, $this->module->displayName, null, array(), null, false, $customer->secure_key);
			Tools::redirectLink(__PS_BASE_URI__.'order-confirmation.php?key='.$customer->secure_key.'&id_cart='.(int)$this->context->cart->id.'&id_module='.(int)$this->module->id.'&id_order='.(int)$this->module->currentOrder);
		}
	}

	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();
        $dobirecne= Tools::convertPrice($this->getDobirecne());
        $total=$this->context->cart->getOrderTotal(true, Cart::BOTH);
		$this->context->smarty->assign(array(
			'totalbezdobirky' => $total,
            'dobirecne' => $dobirecne,
            'total'=>$total + $dobirecne,
			'this_path' => $this->module->getPathUri(),
			'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->module->name.'/'
		));

		$this->setTemplate('validation.tpl');
	}
    
    private function getDobirecne() {
        $id_zone=$this->module->getZoneFromAddress($this->context->cart->id_address_delivery);
        $dobirecne=$this->module->getFee($this->context->cart->id_carrier,$id_zone);
        $free=$this->module->getFeeFree($this->context->cart->id_carrier,$id_zone);
        if((int) $free  > 0 
        && $this->context->cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING) > Tools::convertPrice($free)  ) 
        $dobirecne=0;

        return $dobirecne;
    }
}
