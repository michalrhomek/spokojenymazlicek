{if isset($payment_methods['not_required'])}
	<div class='supercheckout-checkout-content' style='display:block'>
	    <div class='permanent-warning'>{$payment_methods['not_required']|escape:'htmlall':'UTF-8'}</div>
	</div>
{else}
	<div class='supercheckout-checkout-content' style='display:block'>
	{if isset($payment_methods['warning']) && $payment_methods['warning'] neq ''}    
	    <div class='permanent-warning'>{$payment_methods['warning']|escape:'htmlall':'UTF-8'}</div>
	{/if}
	</div>
	{if isset($payment_methods['methods']) && count($payment_methods['methods']) gt 0}                 
	<table class='radio'>
	    {foreach from=$payment_methods['methods'] item='payment_method'}
		{if $payment_method['name'] eq 'codr_klarnacheckout'}
			<tr class="highlight">
			    <td style="display: inline;">
				<a href="{$link->getPageLink('order-opc', null, null, 'klarna_supercheckout=true')|escape:'htmlall':'UTF-8'}" class="orangebuttonapply">Klarna Checkout</a>
			    </td>
			</tr>
		{else}
		<tr class="highlight">
		    <td>
			<input type="radio" name="payment_method" value="{$payment_method['id_module']|intval}" id="{$payment_method['name']|escape:'htmlall':'UTF-8'}" {if $payment_method['id_module'] == $selected_payment_method}checked="checked"{/if} />
			<input type="hidden" id="{$payment_method['id_module']|intval}_name" value="{$payment_method['payment_module_url']|escape:'htmlall':'UTF-8'}" />
		    </td>
		    <td>
			<label id="payment_lbl_{$payment_method['id_module']|intval}" for="{$payment_method['name']|escape:'htmlall':'UTF-8'}">
			    {if $display_payment_style neq 0}
				{if $payment_method['payment_image_url'] neq ''}
				    <img src='{$payment_method['payment_image_url']|escape:'htmlall':'UTF-8'}' alt='{$payment_method['display_name']|escape:'htmlall':'UTF-8'}' {if isset($payment_method['width']) && $payment_method['width'] !=""}width='{$payment_method['width']|escape:'htmlall':'UTF-8'}'{else} width="92"{/if} {if isset($payment_method['height']) && $payment_method['height'] !=""}height='{$payment_method['height']|escape:'htmlall':'UTF-8'}'{else} height="20"{/if}/>{if $display_payment_style neq 2}<br>{/if}
				{/if}
			    {/if}

			    {if $display_payment_style neq 2}
				<span id='payment_method_name_{$payment_method['id_module']|intval}'>{$payment_method['display_name']|escape:'htmlall':'UTF-8'}</span>
			    {/if}
			</label>
		    </td>
		</tr>
		{/if}
	    {/foreach}
	</table>
	<div id="selected_payment_method_html"> </div>
	<div id='payment_method_html' style='display:none;'>
	    {foreach from=$payment_methods['methods'] item='payment_method'}
	    <div id='payment_method_{$payment_method['id_module']|intval}'>
		{$payment_method['html']}{*Variable contains html content, escape not required*}
	    </div>
	    {/foreach}
	</div>
	{else}
	    <div class='supercheckout-checkout-content' style='display:block'>
		<div class='permanent-warning'>{$payment_methods['warning']|escape:'htmlall':'UTF-8'}</div>
	    </div>
	{/if}
{/if}
{*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer tohttp://www.prestashop.com for more information.
* We offer the best and most useful modules PrestaShop and modifications for your online store.
*
* @category  PrestaShop Module
* @author    knowband.com <support@knowband.com>
* @copyright 2015 Knowband
*}