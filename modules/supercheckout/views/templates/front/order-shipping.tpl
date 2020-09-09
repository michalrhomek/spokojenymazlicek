{if isset($IS_VIRTUAL_CART) && $IS_VIRTUAL_CART}
    <input id="input_virtual_carrier" class="hidden" type="hidden" name="id_carrier" value="0" />
    <div class="supercheckout-checkout-content" style="display:block">
        <div class="permanent-warning" style="display: block;">{l s='No Delivery Method Required' mod='supercheckout'}</div>
    </div>
{else}
        {if isset($shipping_errors) && is_array($shipping_errors)}
            {foreach from=$shipping_errors item='shippig_error'}
                <div class="supercheckout-checkout-content" style="display:block">
                    <div class="permanent-warning" style="display: block;">{$shippig_error|escape:'htmlall':'UTF-8'}</div>
                </div>
            {/foreach}
        {else}
            <div class="supercheckout-checkout-content" style="display:block"></div>
        {/if}

        {if isset($delivery_option_list)}
		{foreach $cart->getDeliveryAddressesWithoutCarriers(true) as $address}
			<input type="hidden" name="no_shipping_method" value="1">
			<div class="supercheckout-checkout-content" style="display:block">
			    <div class="permanent-warning" style="display: block;">
				{if empty($address->alias)}
					{l s='No Delivery Method Available' mod='supercheckout'}
				{else}
					{l s='No Delivery Method Available for this Address' mod='supercheckout'}
				{/if}
			    </div>
			</div>
		{foreachelse}
			{foreach $delivery_option_list as $id_address => $option_list}
				<table class="radio">
				    {foreach $option_list as $key => $option}
					<tr class="highlight">
					    <td>
						{if isset($delivery_option[$id_address]) && $delivery_option[$id_address] == $key}
						    <input class='supercheckout_shipping_option delivery_option_radio' type="radio" name="delivery_option[{$id_address|intval}]" value="{$key|escape:'htmlall':'UTF-8'}" id="shipping_method_{$id_address|intval}_{$option@index|intval}" checked="checked" />
						{else if isset($default_shipping_method) && $key == $default_shipping_method}
						    <input class='supercheckout_shipping_option delivery_option_radio' type="radio" name="delivery_option[{$id_address|intval}]" value="{$key|escape:'htmlall':'UTF-8'}" id="shipping_method_{$id_address|intval}_{$option@index|intval}" checked="checked" />
						{else}
						    <input class='supercheckout_shipping_option delivery_option_radio' type="radio" name="delivery_option[{$id_address|intval}]" value="{$key|escape:'htmlall':'UTF-8'}" id="shipping_method_{$id_address|intval}_{$option@index|intval}" />
						{/if}

					    </td>
					    <td class="shipping_info">
						<label for="shipping_method_{$id_address|intval}_{$option@index|intval}">
						    {assign var='sub_carriers_count' value=count($option.carrier_list)}
						    {if $display_carrier_style neq 0}
							{foreach $option.carrier_list as $carrier}
							    {if $carrier.logo}                                                            
								<img src="{$carrier.logo|escape:'htmlall':'UTF-8'}" alt="{$carrier.instance->name|escape:'htmlall':'UTF-8'}" {if isset($carrier.width) && $carrier.width != ""}width="{$carrier.width|escape:'htmlall':'UTF-8'}"{else}width='95'{/if} {if isset($carrier.height) && $carrier.height != ""}height="{$carrier.height|escape:'htmlall':'UTF-8'}"{else}height='20'{/if}/>{if $display_carrier_style neq 2}<br>{/if}
							    {/if}
							{/foreach}
						    {/if}
						    {if $display_carrier_style neq 2}
							{if $option.unique_carrier}
							    {foreach $option.carrier_list as $carrier}
								{$carrier.instance->name|escape:'htmlall':'UTF-8'}
							    {/foreach}
							{/if}                                                        
							{if !$option.unique_carrier}                                                            
							    {foreach $option.carrier_list as $carrier}
								{$carrier.instance->name|escape:'htmlall':'UTF-8'}
								{$sub_carriers_count = $sub_carriers_count - 1}
								{if ($sub_carriers_count lt $option.carrier_list) && $sub_carriers_count gt 0}&{/if}
							    {/foreach}
							{/if}
							{if count($option_list) > 1}
							    {if $option.is_best_grade}
								{if $option.is_best_price}
								    {l s='The best price and speed' mod='supercheckout'}
								{else}
								    {l s='The Fastest' mod='supercheckout'}
								{/if}
							    {else}
								{if $option.is_best_price}
								    {l s='The Best Price' mod='supercheckout'}
								{/if}
							    {/if}
							{/if}                                                        
						    {/if}
						</label>
						{if $option.unique_carrier && isset($carrier.instance->delay[$cookie->id_lang])}
						    <span class="supercheckout-shipping-small-title">{$carrier.instance->delay[$cookie->id_lang]|escape:'htmlall':'UTF-8'}</span>
						{/if}
						{if count($option_list) > 1}
						    <span class="supercheckout-shipping-small-title">
						    {if $option.is_best_grade}
							{if $option.is_best_price}
							    {l s='The best price and speed' mod='supercheckout'}
							{else}
							    {l s='The Fastest' mod='supercheckout'}
							{/if}
						    {else}
							{if $option.is_best_price}
							    {l s='The Best Price' mod='supercheckout'}
							{/if}
						    {/if}
						    </span>
						{/if}
					    </td>
					    <td class="">
						<label for="shipping_method_{$id_address|intval}_{$option@index|intval}">
						    {if $option.total_price_with_tax && (isset($option.is_free) && $option.is_free == 0) && (!isset($free_shipping) || (isset($free_shipping) && !$free_shipping))}
							{if $use_taxes == 1}
							    {if $priceDisplay == 1}
								    {convertPrice price=$option.total_price_without_tax} {* l s='(Tax excl.)' mod='supercheckout' *}
							    {else}
								    {convertPrice price=$option.total_price_with_tax} {* l s='(Tax incl.)' mod='supercheckout' *}
							    {/if}
							{else}
							    {convertPrice price=$option.total_price_without_tax}
							{/if}
						    {else}
							{l s='Free' mod='supercheckout'}
						    {/if}
						</label>
					    </td>
					</tr>                       
				    {/foreach}
				</table>
			{foreachelse}
				<div class="supercheckout-checkout-content" style="display:block">
				    <div class="permanent-warning" style="display: block;">{l s='No Delivery Method Available' mod='supercheckout'}</div>
				</div>
			{/foreach}
		{/foreach}
		{if isset($HOOK_BEFORECARRIER)}
			{$HOOK_BEFORECARRIER}{*Variable contains html content, escape not required*}
		{/if}
		<div id="hook-extracarrier">
			{if isset($HOOK_EXTRACARRIER)}
			{$HOOK_EXTRACARRIER}{*Variable contains html content, escape not required*}
		{/if}
		</div>
		
	{/if}
        <br />                        
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
