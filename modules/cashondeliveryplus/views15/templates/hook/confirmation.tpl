{*
* PrestaHost.cz / PrestaHost.eu
*
*
*  @author prestahost.eu <info@prestahost.cz>
*  @copyright  2014  PrestaHost.eu, Vaclav Mach
*  @license    http://prestahost.eu/prestashop-modules/en/content/3-terms-and-conditions-of-use
*}

<p>{l s='Your order on' mod='cashondeliveryplus'} <span class="bold">{$shop_name}</span> {l s='is complete.' mod='cashondeliveryplus'}
	<br /><br />
	{l s='You have chosen the cash on delivery method.' mod='cashondeliveryplus'}
	<br /><br /><span class="bold">{l s='Your order will be sent very soon.' mod='cashondeliveryplus'}</span>
	<br /><br />{l s='For any questions or for further information, please contact our' mod='cashondeliveryplus'} <a href="{$link->getPageLink('contact-form.php', true)}">{l s='customer support' mod='cashondeliveryplus'}</a>.
</p>
