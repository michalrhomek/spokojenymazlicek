
{** ########################################################################### * 
 *                                                                             * 
 *                      Presta Module Shop | Copyright 2018                    * 
 *                           www.prestamoduleshop.com                          * 
 *                                                                             * 
 *             Please do not change this text, remove the link,                * 
 *          or remove all or any part of the creator copyright notice          * 
 *                                                                             * 
 *    Please also note that although you are allowed to make modifications     * 
 *     for your own personal use, you may not distribute the original or       * 
 *                 the modified code without permission.                       * 
 *                                                                             * 
 *                    SELLING AND REDISTRIBUTION IS FORBIDDEN!                 * 
 *             Download is allowed only from www.prestamoduleshop.com          * 
 *                                                                             * 
 *       This software is provided as is, without warranty of any kind.        * 
 *           The author shall not be liable for damages of any kind.           * 
 *               Use of this software indicates that you agree.                * 
 *                                                                             * 
 *                                    ***                                      * 
 *                                                                             * 
 *              Prosím, neměňte tento text, nemažte odkazy,                    * 
 *      neodstraňujte části a nebo celé oznámení těchto autorských práv        * 
 *                                                                             * 
 *     Prosím vezměte také na vědomí, že i když máte možnost provádět změny    * 
 *        pro vlastní osobní potřebu, nesmíte distribuovat původní nebo        * 
 *                        upravený kód bez povolení.                           * 
 *                                                                             * 
 *                   PRODEJ A DISTRIBUCE JE ZAKÁZÁNA!                          * 
 *          Stažení je povoleno pouze z www.prestamoduleshop.com               * 
 *                                                                             * 
 *   Tento software je poskytován tak, jak je, bez záruky jakéhokoli druhu.    * 
 *          Autor nenese odpovědnost za škody jakéhokoliv druhu.               * 
 *                  Používáním tohoto softwaru znamená,                        * 
 *           že souhlasíte s výše uvedenými autorskými právy.                  * 
 *                                                                             * 
 * ########################################################################### **}{capture name=path}{l s='Shipping' mod='pms_gopay_extra'}{/capture}

<h2>{l s='Order summary' mod='pms_gopay_extra'}</h2>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}
{if $nbProducts <= 0}
	<p class="warning">{l s='Your shopping cart is empty.' mod='pms_gopay_extra'}</p>
{else}
    <div class="tipForm">
		<div id="middlepart">
			<div class="texts">
				<h2>{l s='Check your order before your confirmation' mod='pms_gopay_extra'}</h2>

				<p>
                {l s='The order is ready. Please check your product items and quantities, prices, billing and shipping information are correctly entered.' mod='pms_gopay_extra'}</p>
				<p>
					{l s='Please confirm your order' mod='pms_gopay_extra'} <strong>{l s='I confirm my order' mod='pms_gopay_extra'}</strong>. 
				{if isset($cms)}
					{l s='Once confirmed, the order becomes binding, and any changes must be reported as soon as possible via the.' mod='pms_gopay_extra'} <a href="{$link->getPageLink('cms.php', false)}?id_cms={$cms}&content_only=1" id="contact" class="color iframe">{l s='contact' mod='pms_gopay_extra'}</a>
<script type="text/javascript">
		$(document).ready(function() {
	        $("a.iframe").fancybox({
	            'type' : 'iframe',
	            'width':900,
	            'height':600
	        });
	    });
</script>
				{/if}
				</p>

				<h2>{l s='Check your order before your confirmation' mod='pms_gopay_extra'}</h2>
				<h5>{l s='Information about order' mod='pms_gopay_extra'}</h5>
                
                <table cellspacing="0" class="oinfo">
					<tr>
                    	<td class="tdname">{l s='Date of order:' mod='pms_gopay_extra'}</td>
                    	<td>{$cart->date_upd|date_format:"%d.%m.%Y _ %H:%I:%S"}</td>
                    	<td rowspan="3" style="text-align:right;">
                    	{if $payment->logo_dir}
							<img src="{$payment->logo_dir}" style="">
                       {/if}
                        </td>
                    </tr>
					<tr>
                    	<td class="tdname">{l s='Shipping option:' mod='pms_gopay_extra'}</td>
                    	<td>{$carrier->name}</td>
                    </tr>
					<tr>
                    	<td class="tdname">{l s='Payment method:' mod='pms_gopay_extra'}</td>
                    	<td>
							{$payment->payment_name}
                        </td>
                    </tr>
					<tr>
                    	<td colspan="2" class="tdname">
                        	{l s='Delivery address:' mod='pms_gopay_extra'}
                        </td>
                    </tr>
					<tr>
						<td colspan="2" style="padding-left:120px">
							<b>{$address_delivery->firstname|escape:'htmlall':'UTF-8'} {$address_delivery->lastname|escape:'htmlall':'UTF-8'}</b>
                            <br />
							{$address_delivery->address1|escape:'htmlall':'UTF-8'}, 
                            {if $address_delivery->address2}
                            	{$address_delivery->address2|escape:'htmlall':'UTF-8'}, 
                            {/if}
                            {$address_delivery->postcode|escape:'htmlall':'UTF-8'} {$address_delivery->city|escape:'htmlall':'UTF-8'}, {$address_delivery->country|escape:'htmlall':'UTF-8'}
                            {if $address_delivery->phone_mobile}
                            	<br />{l s='Phone:' mod='pms_gopay_extra'} {$address_delivery->phone_mobile|escape:'htmlall':'UTF-8'}
                            {/if}<br /><br />
                        </td>
                    </tr>
					{if $address_delivery->id != $address_invoice->id}
					<tr>
                    	<td colspan="2" class="tdname">{l s='Billing Address:' mod='pms_gopay_extra'}</td>
                    </tr>
					<tr>
						<td colspan="2" style="padding-left:120px">
                        	{if $address_invoice->company}
                            	{$address_invoice->company|escape:'htmlall':'UTF-8'}<br />
                            {/if}
                        	{if $address_invoice->dni}
                                {l s='DNI:' mod='pms_gopay_extra'}{$address_invoice->dni|escape:'htmlall':'UTF-8'}
                            {/if}
                        	{if $address_invoice->vat_number}
                                <span style="margin-left:20px">
                                	{l s='VAT:' mod='pms_gopay_extra'}{$address_invoice->vat_number|escape:'htmlall':'UTF-8'}
                                </span><br /><br />
                            {/if}
							<b>{$address_invoice->firstname|escape:'htmlall':'UTF-8'} {$address_invoice->lastname|escape:'htmlall':'UTF-8'}</b><br />
							{$address_invoice->address1|escape:'htmlall':'UTF-8'}, 
                            {if $address_invoice->address2}
                            	{$address_invoice->address2|escape:'htmlall':'UTF-8'}, 
                            {/if}
                            {$address_invoice->postcode|escape:'htmlall':'UTF-8'} 
                            {$address_invoice->city|escape:'htmlall':'UTF-8'}, 
                            {$address_invoice->country|escape:'htmlall':'UTF-8'}
                            {if $address_invoice->phone_mobile}
                            	<br />{l s='Mobile phone:' mod='pms_gopay_extra'} {$address_invoice->phone_mobile|escape:'htmlall':'UTF-8'}
                            {/if}
                        </td>
                    </tr>
					{/if}
				</table>

				<h5>{l s='Order' mod='pms_gopay_extra'}</h5>
				<table cellspacing="0" class="oitems">
					<tr>
						<th class="tdcode">{l s='Ref.' mod='pms_gopay_extra'}</th>
						<th>{l s='Description' mod='pms_gopay_extra'}</th>
						<th class="tdqty">{l s='Qty' mod='pms_gopay_extra'}</th>
						<th class="tdprice">{l s='Price' mod='pms_gopay_extra'}</th>
					</tr>
    
					{foreach from=$products item=product name=productLoop}
					{assign var='productId' value=$product.id_product}
					{assign var='productAttributeId' value=$product.id_product_attribute}
					{assign var='quantityDisplayed' value=0}
					{* Display the product line *}    
					<tr>
						<td class="tdcode">{$product.reference}</td>
						<td>
                        	<a style="color:#36C; text-decoration:none" href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category)|escape:'htmlall':'UTF-8'}">
                        	<h4>{$product.name|escape:'htmlall':'UTF-8'}</h4>
							{if isset($product.attributes) && $product.attributes}{$product.attributes|escape:'htmlall':'UTF-8'}{/if}</a>
                        </td>
						<td class="tdqty">{$product.cart_quantity}</td>
						<td class="tdprice">{convertPrice price=$product.total_wt}</td>
					</tr>
					{/foreach}

{* pouze produkty *}
		{if $use_taxes}
			{if $priceDisplay}
				<tr class="cart_total_price">
					<td colspan="2" style="text-align:right;">{if $display_tax_label}{l s='Total products (tax excl.):' mod='pms_gopay_extra'}{else}{l s='Total products:' mod='pms_gopay_extra'}{/if}</td>
					<td colspan="2" class="tdprice">{displayPrice price=$cart_sumary.total_products}</td>
				</tr>
			{else}
				<tr class="cart_total_price">
					<td colspan="2" style="text-align:right;">{if $display_tax_label}{l s='Total products (tax incl.):' mod='pms_gopay_extra'}{else}{l s='Total products:' mod='pms_gopay_extra'}{/if}</td>
					<td colspan="2" class="tdprice">{displayPrice price=$cart_sumary.total_products_wt}</td>
				</tr>
			{/if}
		{else}
			<tr class="cart_total_price">
				<td colspan="2" style="text-align:right;">{l s='Total products:' mod='pms_gopay_extra'}</td>
				<td colspan="2" class="tdprice">{displayPrice price=$cart_sumary.total_products}</td>
			</tr>
		{/if}

{* slevové kupony *}
			<tr class="cart_total_voucher" {if $cart_sumary.total_discounts == 0}style="display:none"{/if}>
				<td colspan="2" style="text-align:right;">
				{if $display_tax_label}
					{if $use_taxes && $priceDisplay == 0}
						{l s='Total vouchers (tax incl.):' mod='pms_gopay_extra'}
					{else}
						{l s='Total vouchers (tax excl.):' mod='pms_gopay_extra'}
					{/if}
				{else}
					{l s='Total vouchers:' mod='pms_gopay_extra'}
				{/if}
				</td>
				<td colspan="2" class="tdprice">
				{if $use_taxes && $priceDisplay == 0}
					{assign var='total_discounts_negative' value=$cart_sumary.total_discounts * -1}
				{else}
					{assign var='total_discounts_negative' value=$cart_sumary.total_discounts_tax_exc * -1}
				{/if}
				{displayPrice price=$total_discounts_negative}
				</td>
			</tr>

{* dárkové balení *}
			<tr{if $cart_sumary.total_wrapping == 0} style="display: none;"{/if}>
				<td colspan="2" style="text-align:right;">
				{if $use_taxes}
					{if $display_tax_label}{l s='Total gift wrapping (tax incl.):' mod='pms_gopay_extra'}{else}{l s='Total gift wrapping:' mod='pms_gopay_extra'}{/if}
				{else}
					{l s='Total gift wrapping:' mod='pms_gopay_extra'}
				{/if}
				</td>
				<td colspan="2" class="tdprice">
				{if $use_taxes}
					{if $priceDisplay}
						{displayPrice price=$cart_sumary.total_wrapping_tax_exc}
					{else}
						{displayPrice price=$cart_sumary.total_wrapping}
					{/if}
				{else}
					{displayPrice price=$cart_sumary.total_wrapping_tax_exc}
				{/if}
				</td>
			</tr>

{* příplatek gopay *}
			<tr {if !$payment->price}style="display: none;"{/if}>
				<td colspan="2" style="text-align:right;">{l s='Surcharge for payment via GoPay:' mod='pms_gopay_extra'}</td>
				<td colspan="2" class="tdprice">
				{if $use_taxes}
					{if $priceDisplay}
						{displayPrice price=$payment->price}
					{else}
						{displayPrice price=$payment->price_wt}
					{/if}
				{else}
					{displayPrice price=$payment->price}
				{/if}
				</td>
			</tr>

{* dopravné *}
			{if $cart_sumary.total_shipping_tax_exc <= 0}
				<tr>
					<td colspan="2" style="text-align:right;">{l s='Shipping:' mod='pms_gopay_extra'}</td>
					<td colspan="2" class="tdprice">{l s='Free Shipping!' mod='pms_gopay_extra'}</td>
				</tr>
			{else}
				{if $use_taxes && $cart_sumary.total_shipping_tax_exc != $cart_sumary.total_shipping}
					{if $priceDisplay}
						<tr {if $cart_sumary.total_shipping_tax_exc <= 0} style="display:none;"{/if}>
							<td colspan="2" style="text-align:right;">{if $display_tax_label}{l s='Total shipping (tax excl.):' mod='pms_gopay_extra'}{else}{l s='Total shipping:' mod='pms_gopay_extra'}{/if}</td>
							<td colspan="2" class="tdprice">{displayPrice price=$cart_sumary.total_shipping_tax_exc}</td>
						</tr>
					{else}
						<tr {if $cart_sumary.total_shipping <= 0} style="display:none;"{/if}>
							<td colspan="2" style="text-align:right;">{if $display_tax_label}{l s='Total shipping (tax incl.):' mod='pms_gopay_extra'}{else}{l s='Total shipping:' mod='pms_gopay_extra'}{/if}</td>
							<td colspan="2" class="tdprice">{displayPrice price=$cart_sumary.total_shipping}</td>
						</tr>
					{/if}
				{else}
					<tr {if $cart_sumary.total_shipping_tax_exc <= 0} style="display:none;"{/if}>
						<td colspan="2" style="text-align:right;">{l s='Total shipping:' mod='pms_gopay_extra'}</td>
						<td colspan="2" class="tdprice">{displayPrice price=$cart_sumary.total_shipping_tax_exc}</td>
					</tr>
				{/if}
			{/if}

{* Celkem *}
					<tr style="height:30px; font-size:1.4em">
        				<td colspan="2" style="text-align:right"><b>{l s='Total price:' mod='pms_gopay_extra'}</b></td>
						{if $use_taxes}
							<td colspan="2" class="tdprice"><b>{displayPrice price=$cart_sumary.total_price+$payment->price_wt}</b></td>
						{else}
							<td colspan="2" class="tdprice"><b>{displayPrice price=$cart_sumary.total_price_without_tax+$payment['price']}</b></td>
						{/if}
    				</tr>
				</table>
			</div>
			<p class="pozor_gopay">
				{if $PAYMENT_MODE}					
					{l s='Click on "' mod='pms_gopay_extra'}<span style="font-weight:bold">{l s='I confirm my order' mod='pms_gopay_extra'}</span>{l s='" and you will be redirected on the payment gateway where you can select the method of payment for your order.' mod='pms_gopay_extra'}
				{else}
					{l s='Click on "' mod='pms_gopay_extra'}<span style="font-weight:bold">{l s='I confirm my order' mod='pms_gopay_extra'}</span>{l s='" and you will be redirected on the payment gateway for instant payment for your order.' mod='pms_gopay_extra'}
				{/if}
            </p>
		</div>
	</div>

	<p class="gopay_error" id="inline_errors" style="display:none"></p>
	{$warning}
	{if $smarty.const._PS_VERSION_ >= 1.6}
        <p class="cart_navigation clearfix" id="cart_navigation">
        	<a 
				class="button-exclusive btn btn-default" data-ajax="false"
				href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}"
			>
                <i class="icon-chevron-left"></i>{l s='Other payment methods' mod='pms_gopay_extra'}
            </a>
            <a class="button btn btn-default button-medium payment-overlay"
				href="{$payment_url}"
				title="{l s='Pay my order' mod='pms_gopay_extra'}"
				{if $INLINE_MODE}onClick="inlineFunction(this.href); return false;"{/if}
			>
				<span>{l s='I confirm my order' mod='pms_gopay_extra'}<i class="icon-chevron-right right"></i></span>
			</a>
        </p>
	{else}
		<p class="cart_navigation">
			<a
			class="button_large" data-ajax="false"
			href="{$link->getPageLink('order.php', true)}?step=3">
				{l s='Other payment methods' mod='pms_gopay_extra'}
			</a>
            <a class="exclusive_large payment-overlay"
				href="{$payment_url}"
				title="{l s='Pay my order' mod='pms_gopay_extra'}"
				{if $INLINE_MODE}onClick="inlineFunction(this.href); return false;"{/if}
			>
				<span>{l s='I confirm my order' mod='pms_gopay_extra'}<i class="icon-chevron-right right"></i></span>
			</a>
		</p>
	{/if}
{/if}