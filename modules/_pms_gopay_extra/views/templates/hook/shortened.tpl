
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
 * ########################################################################### **}

{assign var="REPEAT" value=''}
{if isset($_REPEAT)}
	{assign var="REPEAT" value=$_REPEAT}
{/if}

{assign var="orderId" value=''}
{if isset($order->id)}
	{assign var="orderId" value=$order->id}
{/if}

<!-- zakladni varianta platby (single choice) -->
<div id="{$_PMS_MODULE->name}">	
{if isset($_PAYMENTS) && is_array($_PAYMENTS)}
	{foreach $_PAYMENTS as $payment}
		{assign var="payment_url" value=$link->getModuleLink($_PMS_MODULE->name, $typeG, ['id_payment_button'=>$payment['id_payment_button'], 'orderId'=>$orderId, "confirm_{$_PMS_MODULE->name}"=>$SKIP_STEP], true)|escape:'html':'UTF-8'}	
		{if $smarty.const._PS_VERSION_ >= 1.6}
			<div class="row">
				<div class="col-xs-12">
					<p class="payment_module bootstrap_button">
						<a class="{$payment['payment_code']} {if $SKIP_STEP || $REPEAT}payment-overlay{/if}"
							href="{$payment_url}"
							title="{l s='Pay my order' mod='pms_gopay_extra'}"
							{if $INLINE_MODE && ($SKIP_STEP || $REPEAT)}
								onClick="inlineFunction(this.href); return false;"
							{/if}
						>
							{if isset($payment['price']) && $payment['price'] && !$REPEAT}
								<span class="price">{Tools::displayPrice($payment['price'])}</span>
							{/if}
							{$payment['payment_name']}
							{if $payment['payment_desc']}
								<br>
								{assign "letters" array('<div', '/div>', '<p', '/p>')}
								{assign "fruit" array('<span', '/span>', '<span', '/span>')}
								<span>({$payment['payment_desc']|replace:$letters:$fruit nofilter})</span>
							{/if}
						</a>
					</p>
				</div>
			</div>
			<style type="text/css">
				p.payment_module a.{$payment['payment_code']} {
					background: url({if $_REPEAT}{$payment['logo']}{else}{$payment['payment_logo']}{/if}) 15px 50% no-repeat #fbfbfb;
								background-size: 60px;
				}
				p.payment_module a.{$payment['payment_code']}:after {
								display: block;
								content: "\f054";
								position: absolute;
								right: 15px;
								margin-top: -11px;
								top: 50%;
								font-family: "FontAwesome";
								font-size: 25px;
								height: 22px;
								width: 14px;
								color: #777;
				}
			</style>
		{else}
			<p class="payment_module">
				<a class="{if $SKIP_STEP || $REPEAT}payment-overlay{/if}"
					href="{$payment_url}"
					title="{l s='Pay my order' mod='pms_gopay_extra'}"
					{if $INLINE_MODE && ($SKIP_STEP || $REPEAT)}
						onClick="inlineFunction(this.href); return false;"
					{/if}
				>
					{if $payment['payment_desc']}
						<img src="{if $_REPEAT}{$payment['logo']}{else}{$payment['payment_logo']}{/if}" style="max-width:100px">
					{/if}
					<span class="popis_dob">
						{$payment['payment_name']}
						{if $payment['payment_desc']}
							<br>
							<span>({$payment['payment_desc']})</span>
						{/if}
					</span>
					{if isset($payment['price']) && !$REPEAT}
						<span class="price">{Tools::displayPrice($payment['price'])}</span>
					{/if}
					<br style="clear:both;" />
				</a>
			</p>
		{/if}
	{/foreach}
{/if}
</div>
