{*
* PrestaHost.cz / PrestaHost.eu
*
*
*  @author prestahost.eu <info@prestahost.cz>
*  @copyright  2014  PrestaHost.eu, Vaclav Mach
*  @license    http://prestahost.eu/prestashop-modules/en/content/3-terms-and-conditions-of-use
*}

{capture name=path}{l s='Shipping' mod='cashondeliveryplus'}{/capture}

<h1 class="page-heading">{l s='Order summation' mod='cashondelivery'}>{l s='Order summation' mod='cashondeliveryplus'}</h1>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

<form action="{$link->getModuleLink('cashondeliveryplus', 'validation', [], true)}" method="post">
 <div class='box'>
	<input type="hidden" name="confirm" value="1" />
  <h3>{l s='Cash on delivery (COD) payment' mod='cashondeliveryplus'}</h3>
	<p>
		<img src="{$this_path}cashondelivery.jpg" alt="{l s='Cash on delivery (COD) payment' mod='cashondeliveryplus'}" style="float:left; margin: 0px 10px 5px 0px;" />
		{l s='You have chosen the cash on delivery method.' mod='cashondeliveryplus'}
		<br/><br />
		{l s='The total amount of your order is' mod='cashondeliveryplus'}
		<span id="amount_{$currencies.0.id_currency}" class="price">{displayPrice price=$total}  
        {if $use_taxes == 1}
            {l s='(tax incl.)' mod='cashondeliveryplus'}
        {/if}
         <br />
        {l s='This includes COD fee' mod='cashondeliveryplus'}  {displayPrice price=$dobirecne}   
        </span>
		
	</p>
	<p>
		<br /><br />
		<br /><br />
		<b>{l s='Please confirm your order by clicking \'I confirm my order\'' mod='cashondeliveryplus'}.</b>
	</p>
  </div>
	<p class="cart_navigation" id="cart_navigation">
        <a href="{$link->getPageLink('order', true)}?step=3" class="button-exclusive btn btn-default"><i class="icon-chevron-left"></i>{l s='Other payment methods' mod='cashondelivery'}</a>
        <button type="submit" class="button btn btn-default button-medium"><span>{l s='I confirm my order' mod='cashondelivery'}</span></button>
    </p>
</form>
