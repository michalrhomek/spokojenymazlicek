<?php
/*
* 2007-2012 Ideal-checkout.nl
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author Ideal-checkout.nl <info@ideal-checkout.nl>
*  @copyright  2007-2012 Ideal-checkout.nl
*  @version  Release: $Revision: 1009 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;
class Modrefchange extends Module
{
	private $_html = '';
	private $_postErrors = array();
	protected $_errors = array();

	public $ref_orderid;
	public $ref_cartid;
	public $ref_prefnulo;
	public $ref_prefnulnro;
	public $ref_prefnulnrc;
	public $ref_prefsigno;
	public $ref_prefnulc;
	public $ref_prefsignc;
	public $ref_prefsign;
	
	public function __construct()
	{
		$this->name = 'modrefchange';

		$this->tab = 'administration';

		$this->version = 1.2;
		$this->author = 'PrestadevNL';

		$config = Configuration::getMultiple(array('ORD_REF_ORDERID', 'ORD_REF_PREFIXNULO', 'ORD_REF_PREFIXNULNRO', 'ORD_REF_PREFIXSIGNO', 'ORD_REF_CARTID', 'ORD_REF_PREFIXNULC', 'ORD_REF_PREFIXNULNRC', 'ORD_REF_PREFIXSIGNC', 'ORD_REF_PREFIXSIGN'));
		if (isset($config['ORD_REF_ORDERID']))
			$this->ref_orderid = $config['ORD_REF_ORDERID'];
		if (isset($config['ORD_REF_CARTID']))
			$this->ref_cartid = $config['ORD_REF_CARTID'];
		if (isset($config['ORD_REF_PREFIXNULO']))
			$this->ref_prefnulo = $config['ORD_REF_PREFIXNULO'];
		if (isset($config['ORD_REF_PREFIXNULNRO']))
			$this->ref_prefnulnro = $config['ORD_REF_PREFIXNULNRO'];
		if (isset($config['ORD_REF_PREFIXSIGNO']))
			$this->ref_prefsigno = $config['ORD_REF_PREFIXSIGNO'];
		if (isset($config['ORD_REF_PREFIXNULC']))
			$this->ref_prefnulc = $config['ORD_REF_PREFIXNULC'];
		if (isset($config['ORD_REF_PREFIXNULNRC']))
			$this->ref_prefnulnrc = $config['ORD_REF_PREFIXNULNRC'];
		if (isset($config['ORD_REF_PREFIXSIGNC']))
			$this->ref_prefsignc = $config['ORD_REF_PREFIXSIGNC'];
		if (isset($config['ORD_REF_PREFIXSIGN']))
			$this->ref_prefsign = $config['ORD_REF_PREFIXSIGN'];
		parent::__construct();

		$this->displayName = $this->l('Order reference change mod');
		$this->description = $this->l('Mod to change the order reference');
	}
	
	public function install()
	{
		if(!$this->installDB())
			return $this->_abortInstall('Error while installing module database settings');
		if(!parent::install())
			return $this->_abortInstall('Error while installing module class');
		if(!$this->registerHook('actionvalidateorder'))
			return $this->_abortInstall('Error while adding module to hook actionValidateOrder');
		if(!Configuration::updateValue('ORD_REF_ORDERID', 0))
			$return = $this->_abortInstall('Error while adding configuration setting ORD_REF_ORDERID');
		if(!Configuration::updateValue('ORD_REF_PREFIXNULO', 0))
			$return = $this->_abortInstall('Error while adding configuration setting ORD_REF_PREFIXNULO');
		if(!Configuration::updateValue('ORD_REF_PREFIXNULNRO', 9))
			$return = $this->_abortInstall('Error while adding configuration setting ORD_REF_PREFIXNULNRO');
		if(!Configuration::updateValue('ORD_REF_PREFIXNULNRC', 9))
			$return = $this->_abortInstall('Error while adding configuration setting ORD_REF_PREFIXNULNRC');
		if(!Configuration::updateValue('ORD_REF_PREFIXSIGNO', ''))
			$return = $this->_abortInstall('Error while adding configuration setting ORD_REF_PREFIXSIGNO');
		if(!Configuration::updateValue('ORD_REF_CARTID', 0))
			$return = $this->_abortInstall('Error while adding configuration setting ORD_REF_PREFIXNULO');
		if(!Configuration::updateValue('ORD_REF_PREFIXNULC', 0))
			$return = $this->_abortInstall('Error while adding configuration setting ORD_REF_PREFIXNULC');
		if(!Configuration::updateValue('ORD_REF_PREFIXSIGNC', ''))
			$return = $this->_abortInstall('Error while adding configuration setting ORD_REF_PREFIXSIGNC');
		if(!Configuration::updateValue('ORD_REF_PREFIXSIGN', ''))
			$return = $this->_abortInstall('Error while adding configuration setting ORD_REF_PREFIXSIGN');

		return true;
	}

	public function installDB(){
		$return = true;
		if(!Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'orders` CHANGE `reference` `reference` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL '))
			$return = $this->_abortInstall('Error while altering `'._DB_PREFIX_.'orders`');
		if(!Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'order_payment` CHANGE `order_reference` `order_reference` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL '))
			$return = $this->_abortInstall('Error while altering `'._DB_PREFIX_.'order_payments`');
		return $return;
	}
	
	public function uninstall()
	{
		$return = true;
		
		if(!parent::uninstall())
			$return = $this->_abortInstall('Error while uninstalling class from modules');
		if(!Configuration::deleteByName('ORD_REF_ORDERID'))
			$return = $this->_abortInstall('Error while removing configuration setting ORD_REF_ORDERID');
		if(!Configuration::deleteByName('ORD_REF_PREFIXNULO'))
			$return = $this->_abortInstall('Error while removing configuration setting ORD_REF_PREFIXNULO');
		if(!Configuration::deleteByName('ORD_REF_PREFIXNULNRO'))
			$return = $this->_abortInstall('Error while removing configuration setting ORD_REF_PREFIXNULNRO');
		if(!Configuration::deleteByName('ORD_REF_PREFIXNULNRC'))
			$return = $this->_abortInstall('Error while removing configuration setting ORD_REF_PREFIXNULNRC');
		if(!Configuration::deleteByName('ORD_REF_PREFIXSIGNO'))
			$return = $this->_abortInstall('Error while removing configuration setting ORD_REF_PREFIXSIGNO');
		if(!Configuration::deleteByName('ORD_REF_CARTID'))
			$return = $this->_abortInstall('Error while removing configuration setting ORD_REF_PREFIXNULO');
		if(!Configuration::deleteByName('ORD_REF_PREFIXNULC'))
			$return = $this->_abortInstall('Error while removing configuration setting ORD_REF_PREFIXNULC');
		if(!Configuration::deleteByName('ORD_REF_PREFIXSIGNC'))
			$return = $this->_abortInstall('Error while removing configuration setting ORD_REF_PREFIXSIGNC');
		if(!Configuration::deleteByName('ORD_REF_PREFIXSIGN'))
			$return = $this->_abortInstall('Error while removing configuration setting ORD_REF_PREFIXSIGN');

		return true;
	}
	
	/**
	* Set installation errors and return false
	*
	* @param string $error Installation abortion reason
	* @return boolean Always false
	*/
	protected function _abortInstall($error)
	{
		if (version_compare(_PS_VERSION_, '1.5.0.0 ', '>='))
			$this->_errors[] = $error;
		else
			echo '<div class="error">'.strip_tags($error).'</div>';

		return false;
	}
	
	private function _postProcess()
	{
		if (Tools::isSubmit('btnSubmit'))
		{
			Configuration::updateValue('ORD_REF_ORDERID', Tools::getValue('ref_orderid'));
			Configuration::updateValue('ORD_REF_PREFIXNULO', Tools::getValue('ref_prefixnulo'));
			Configuration::updateValue('ORD_REF_PREFIXNULNRO', Tools::getValue('ref_prefixnulnro'));
			Configuration::updateValue('ORD_REF_PREFIXSIGNO', Tools::getValue('ref_prefixsigno'));
			Configuration::updateValue('ORD_REF_CARTID', Tools::getValue('ref_cartid'));
			Configuration::updateValue('ORD_REF_PREFIXNULC', Tools::getValue('ref_prefixnulc'));
			Configuration::updateValue('ORD_REF_PREFIXNULNRC', Tools::getValue('ref_prefixnulnrc'));
			Configuration::updateValue('ORD_REF_PREFIXSIGNC', Tools::getValue('ref_prefixsignc'));
			Configuration::updateValue('ORD_REF_PREFIXSIGN', Tools::getValue('ref_prefixsign'));		
		}
		$this->_html .= '<div class="conf confirm"> '.$this->l('Settings updated').'</div>';
	}

	private function _displayForm()
	{
		$this->_html .=
		'<form action="'.Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']).'" method="post">
			<fieldset>
			<legend><img src="../img/admin/cog.gif" />'.$this->l('Order reference settings').'</legend>
				<table border="0" width="700" cellpadding="0" cellspacing="0" id="form">
					<tr>
						<td colspan="2">
							'.$this->l('Please specify the settings for the order reference change').'.<br /><br />
						</td>
					</tr>
					<tr>
						<td width="300" style="height: 35px;">
							'.$this->l('Use Order ID').'
						</td>
						<td>
							&nbsp;&nbsp;
							<input type="radio" name="ref_orderid" id="ref_orderid" value="1" '. ((Tools::getValue('ref_orderid', $this->ref_orderid)) ? 'checked="checked"' : '') .' />
							<label class="t" for="active_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" style="cursor:pointer" /></label>
							&nbsp;&nbsp;
							<input type="radio" name="ref_orderid" id="ref_orderid" value="0" '. ((Tools::getValue('ref_orderid', $this->ref_orderid)) ? '' : 'checked="checked"') .' />
							<label class="t" for="active_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" style="cursor:pointer" /></label>
							<p class="preference_description">'.$this->l('Use the Order ID instead of the random characters as Order reference').'.</p>
						</td>
					</tr>
					<tr>
						<td width="300" style="height: 35px;">
							'.$this->l('Use Zeros to prefix Order ID').'
						</td>
						<td>
							&nbsp;&nbsp;
							<input type="radio" name="ref_prefixnulo" id="ref_prefixnulo" value="1" '. ((Tools::getValue('ref_prefixnulo', $this->ref_prefnulo)) ? 'checked="checked"' : '') .' />
							<label class="t" for="active_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" style="cursor:pointer" /></label>
							&nbsp;&nbsp;
							<input type="radio"name="ref_prefixnulo" id="ref_prefixnulo" value="0" '. ((Tools::getValue('ref_prefixnulo', $this->ref_prefnulo)) ? '' : 'checked="checked"') .' />
							<label class="t" for="active_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" style="cursor:pointer" /></label>
							<p class="preference_description">'.$this->l('Prefix the Order ID with zeros (e.g. \'000000001\', \'000000010\', \'00000000[ORDER_ID]\')').'.</p>
						</td>
					</tr>
					<tr>
						<td width="300" style="height: 35px;">
							'.$this->l('Number of zeros to prefix Order ID').'
						</td>
						<td>
							<input type="text" name="ref_prefixnulnro" value="'.htmlentities(Tools::getValue('ref_prefixnulnro', $this->ref_prefnulnro), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" />
							<p class="preference_description">'.$this->l('Number of zeros to use as padding. Must be between 1 and 10').'.</p>
						</td>
					</tr>
					<tr>
						<td width="300" style="height: 35px;">
							'.$this->l('Use Character(s) to prefix Order ID').'
						</td>
						<td>
							<input type="text" name="ref_prefixsigno" value="'.htmlentities(Tools::getValue('ref_prefixsigno', $this->ref_prefsigno), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" />
							<p class="preference_description">'.$this->l('Prefix the Order ID with one or more characters (e.g. \'O1\', \'ORD_10\')<br>Leave empty to not use prefix').'.</p>
						</td>
					</tr>
					<tr>
						<td width="300" style="height: 35px;">
							'.$this->l('Use Cart ID').'
						</td>
						<td>
							&nbsp;&nbsp;
							<input type="radio" name="ref_cartid" id="ref_cartid" value="1" '. ((Tools::getValue('ref_cartid', $this->ref_cartid)) ? 'checked="checked"' : '') .' />
							<label class="t" for="active_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" style="cursor:pointer" /></label>
							&nbsp;&nbsp;
							<input type="radio" name="ref_cartid" id="ref_cartid" value="0" '. ((Tools::getValue('ref_cartid', $this->ref_cartid)) ? '' : 'checked="checked"') .' />
							<label class="t" for="active_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" style="cursor:pointer" /></label>
							<p class="preference_description">'.$this->l('Use the Cart ID instead of the random characters as Order reference').'.</p>
						</td>
					</tr>
					<tr>
						<td width="300" style="height: 35px;">
							'.$this->l('Use Zeros to prefix Cart ID').'
						</td>
						<td>
							&nbsp;&nbsp;
							<input type="radio" name="ref_prefixnulc" id="ref_prefixnulc" value="1" '. ((Tools::getValue('ref_prefixnulc', $this->ref_prefnulc)) ? 'checked="checked"' : '') .' />
							<label class="t" for="active_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" style="cursor:pointer" /></label>
							&nbsp;&nbsp;
							<input type="radio"name="ref_prefixnulc" id="ref_prefixnulc" value="0" '. ((Tools::getValue('ref_ref_prefixnulc', $this->ref_prefnulc)) ? '' : 'checked="checked"') .' />
							<label class="t" for="active_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" style="cursor:pointer" /></label>
							<p class="preference_description">'.$this->l('Prefix the Cart ID with zeros (e.g. \'000000001\', \'000000010\', \'00000000[CART_ID]\')').'.</p>
						</td>
					</tr>
					<tr>
						<td width="300" style="height: 35px;">
							'.$this->l('Number of zeros to prefix Cart ID').'
						</td>
						<td>
							<input type="text" name="ref_prefixnulnrc" value="'.htmlentities(Tools::getValue('ref_prefixnulnrc', $this->ref_prefnulnrc), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" />
							<p class="preference_description">'.$this->l('Number of zeros to use as padding. Must be between 1 and 10').'.</p>
						</td>
					</tr>
					<tr>
						<td width="300" style="height: 35px;">
							'.$this->l('Use Character(s) to prefix Cart ID').'
						</td>
						<td>
							<input type="text" name="ref_prefixsignc" value="'.htmlentities(Tools::getValue('ref_prefixsignc', $this->ref_prefsignc), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" />
							<p class="preference_description">'.$this->l('Prefix the Cart ID with one or more characters (e.g. \'C1\', \'CID_10\')<br>Leave empty to not use prefix').'.</p>
						</td>
					</tr>
					<tr>
						<td width="300" style="height: 35px;">
							'.$this->l('Use Characters to prefix Order Reference').'
						</td>
						<td>
							<input type="text" name="ref_prefixsign" value="'.htmlentities(Tools::getValue('ref_prefixsign', $this->ref_prefsign), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" />
							<p class="preference_description">'.$this->l('Prefix the Order Reference with one or more characters (e.g. \'O1\', \'ORD_10\')<br>Leave empty to not use prefix').'.</p>
						</td>
					</tr>
					<tr>
						<td colspan="2" align="center">
							<input class="button" name="btnSubmit" value="'.$this->l('Update settings').'" type="submit" />
						</td>
					</tr>
				</table>
			</fieldset>
		</form>';
	}

	public function getContent()
	{
		$this->_html = '<h2>'.$this->displayName.'</h2>';

		if (Tools::isSubmit('btnSubmit'))
		{
			$this->_postProcess();
		}
		else
			$this->_html .= '<br />';

		$this->_displayForm();

		return $this->_html;
	}
	
	public function generateReferenceFromID($id_order = NULL, $id_cart = NULL, $reference = NULL, $sequence = 0)
	{
		$reforder = '';
		$refcart = '';
		$ref = '';

		if(!$id_order)
			return false;
			
		if($this->ref_orderid){
			$reforder = $id_order;
			if($this->ref_prefnulo)
				$reforder = sprintf('%0'.$this->ref_prefnulnro.'d', $reforder);
			if($this->ref_prefsigno)
				$reforder = $this->ref_prefsigno.$reforder;
		}
		if($this->ref_cartid){
			$refcart = $id_cart;
			if($this->ref_prefnulc)
				$refcart = sprintf('%0'.$this->ref_prefnulnrc.'d', $refcart);
			if($this->ref_prefsignc)
				$refcart = $this->ref_prefsignc.$refcart;
		}
		
		if(!$sequence) {
			if($reforder && !$refcart){
				$ref = $reforder;
			} elseif($reforder && $refcart){
				$ref = $reforder.'_'.$refcart;
			} elseif(!$reforder && $refcart){
				$ref = $refcart;
			} elseif(!$reforder && !$refcart){
				$ref = $reference;
			}

			if($this->ref_prefsign)
				$ref = $this->ref_prefsign.$ref;
		}
		else {
			$ref = $id_order.'_'.$sequence;
		}
			
		$sequence++;
			
		// First find if an order reference with the defined Order ID
		if($result = Db::getInstance()->getValue('SELECT reference FROM '._DB_PREFIX_.'orders WHERE reference = \''.$ref.'\' ORDER BY id_order DESC'))
			return $this->generateReferenceFromID($ref, $id_cart, $reference, $sequence);
		else
			return $ref;
	}
	
	public function hookactionValidateOrder($params)
	{
		if (!$this->active)
			return;
		
		if($this->ref_orderid OR $this->ref_cartid OR $this->ref_prefsign){
			$params['order']->reference = $this->generateReferenceFromID($params['order']->id, $params['cart']->id, $params['order']->reference);
			$params['order']->update();
			return;
		} else {
			return;
		}
	}
}