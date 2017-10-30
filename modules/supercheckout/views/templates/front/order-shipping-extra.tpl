{if !$IS_VIRTUAL_CART}
    {if $recyclablePackAllowed}
        <div id="supercheckout_recyclepack_container" class='order-shipping-extra' style="display:padding-bottom: 0 !important;">
            <input type="checkbox" name="recyclable" class="supercheckout-delivery-extra" id="recyclable" value="1" {if $recyclable == 1}checked="checked"{/if} />                        
            {l s='I would like to receive my order in recycled packaging.' mod='supercheckout'}                        
        </div>
    {/if}

    {if $giftAllowed}
    <div id="supercheckout-gift_container" class='order-shipping-extra' style="display:padding-bottom: 0 !important;">
        <input type="checkbox" class="supercheckout-delivery-extra" name="gift" id="gift" value="1" {if $cartGiftChecked == 1}checked="checked"{/if} />                        
        {l s='I would like my order to be gift wrapped.' mod='supercheckout'}
        {if $gift_wrapping_price > 0}
            {l s='Additional cost of' mod='supercheckout'}
            {if $priceDisplay == 1}
                {convertPrice price=$total_wrapping_tax_exc_cost}
            {else}
                {convertPrice price=$total_wrapping_cost}
            {/if}
            {if $use_taxes}
                {if $priceDisplay == 1}
                    {l s='(Tax excl.)' mod='supercheckout'}
                {else}
                    {l s='(Tax incl.)' mod='supercheckout'}
                {/if}
            {/if}
        {/if}
    </div>
    <div id="supercheckout-gift-comments" style="display:none; margin-top: 0; margin-bottom: 15px;">
        <b>{l s='If you\'d like, you can add a note to the gift:' mod='supercheckout'}</b>
        <textarea id="gift_message" name="gift_comment" rows="8" >{$cart->gift_message|escape:'html':'UTF-8'}</textarea>
    </div>
    {/if}
{/if}   
{if $conditions AND $cms_id AND $show_TOS}
<div id="supercheckout-agree">
	<label><input type="checkbox" name="cgv" value="1" {if $checkedTOS}checked="checked"{/if} />
	{l s='I agree to the terms of service and will adhere to them unconditionally. ' mod='supercheckout'}        
	</label>
	(<a href="{$link_conditions|escape:'html':'UTF-8'}" target="_blank" class="iframe various fancybox.ajax" rel="nofollow">{l s='Read the term of services' mod='supercheckout'}</a>)
</div>
<script> 
	$(document).ready(function() {
		$(".various").fancybox({
			 scrolling: 'auto',
			width: '65%',
			height: '60%',
			fitToView: false,
			autoSize: false,
			'type': 'ajax',
			'ajax': {
			    dataFilter: function(data) {
				return $(data).find('#center_column')[0];
			}
		    }
		});
	});
</script>
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