
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
{assign var='checked' value=false}
{if isset($order->id)}
	{assign var="orderId" value=$order->id}
{/if}

{if isset($_PAYMENTS) && is_array($_PAYMENTS)}
	{assign var="payment_url" value=$link->getModuleLink({$_PMS_MODULE->name}, $typeG, [], true)|escape:'html':'UTF-8'}
	<form action="{$payment_url}" method="post" id="{$_PMS_MODULE->name}-window-form">
		<input type="hidden" name="orderId" value="{$orderId}">
		<input type="hidden" name="confirm_{$_PMS_MODULE->name}" value="{$SKIP_STEP}">
		<div class="col-sm-12 col-xs-space">
			<h3>{l s='Select the payment method for your order' mod='pms_gopay_extra'}</h3>
			<hr style="border-bottom: 3px solid #ddd">
				{foreach $_PAYMENTS as $payment}
					{if $payment['payment_code'] == 'PAYMENT_CARD'}
						<div class="radio-inline">
							<label class="icon col-sm-8">
								<input type="radio"
										name="id_payment_button"
										value="{$payment['id_payment_button']}"
										checked="checked"
								>
								<strong>{$payment['payment_name']}</strong>
								{if $payment['logo']}
									<img class="large" src="{$payment['logo']}" alt="" title="{$payment['payment_name']}">
								{/if}
								{if $payment['price'] && !$REPEAT}
									<span class="win_price">{Tools::displayPrice($payment['price'])}</span>
								{/if}
							</label>
						</div>
						{assign var='checked' value=true}
						<hr style="border-bottom: 1px solid #ddd">
					{/if}
				{/foreach}

				{if !$_PREAUTHORIZED && !$_RECURRENT}
					{assign var='newLine' value=0}
					{foreach $_PAYMENTS as $payment}
						{if $payment['isSwift']}
								{if $payment['isOnline']}
									{if $newLine == 0}
										<h4 class="groupTitle">{l s='Online bank payment' mod='pms_gopay_extra'}</h4>
										<div class="radio-inline col-sm-12">
									{/if}
									<label class="icon col-sm-4">
										<input type="radio"
												name="id_payment_button"
												value="{$payment['id_payment_button']}"
												{if !$checked}checked{/if}
										>
										{if $payment['logo']}
											<img class="large" src="{$payment['logo']}" alt="" title="{$payment['payment_name']}">
										{else}
											{$payment['payment_name']}
										{/if}
										{if $payment['price'] && !$REPEAT}
											<span class="win_price">{Tools::displayPrice($payment['price'])}</span>
										{/if}
									</label>
									{assign var='newLine' value=1}
									{assign var='checked' value=true}
								{/if}
						{/if}
					{/foreach}
					{if $newLine == 1}
						</div>
						<hr style="border-bottom: 1px solid #ddd">
					{/if}

					{assign var='count' value=0}
					{foreach $_PAYMENTS as $payment}
						{if $payment['payment_group'] == "wallet"}
						{if $count == 0}
							<h4 class="groupTitle">{l s='Electronic wallet' mod='pms_gopay_extra'}</h4>
							<div class="radio-inline">
						{/if}
							<label class="icon col-sm-4">
								<input type="radio"
										name="id_payment_button"
										value="{$payment['id_payment_button']}"
										{if !$checked}checked="checked"{/if}
								>
								{if $payment['logo']}
									<img class="large" src="{$payment['logo']}" alt="" title="{$payment['payment_name']}">
								{else}
									{$payment['payment_name']}
								{/if}
								{if $payment['price'] && !$REPEAT}
									<span class="win_price">{Tools::displayPrice($payment['price'])}</span>
								{/if}
							</label>
							{assign var='count' value=1}
							{assign var='checked' value=true}
						{/if}
					{/foreach}
					{if $count == 1}
						</div>
						<hr style="border-bottom: 1px solid #ddd">
					{/if}

					{foreach $_PAYMENTS as $payment}
						{if $payment['payment_code'] == 'SUPERCASH'}
							<div class="radio-inline">
								<label class="icon col-sm-8">
									<input type="radio"
											name="id_payment_button"
											value="{$payment['id_payment_button']}"
											{if !$checked}checked="checked"{/if}
									>
									<strong>{l s='Coupon payment' mod='pms_gopay_extra'}</strong>
									{if $payment['logo']}
										<img class="large" src="{$payment['logo']}" alt="" title="{$payment['payment_name']}">
									{else}
										{$payment['payment_name']}
									{/if}
									{if $payment['price'] && !$REPEAT}
										<span class="win_price">{Tools::displayPrice($payment['price'])}</span>
									{/if}
								</label>
							</div>
							<hr style="border-bottom: 1px solid #ddd">
							{assign var='checked' value=true}
						{/if}
					{/foreach}
				{/if}

			{if !$_HIDE_GROUP_ACCOUNT}
				{foreach $_PAYMENTS as $payment}
					{if $payment['payment_code'] == 'ACCOUNT'}
						<p class="radio-inline">
							<label>
								<input type="radio"
										name="id_payment_button"
										value="{$payment['id_payment_button']}"
										{if !$checked}checked="checked"{/if}
								>
								<strong>{l s='Choose from payment methods directly on the payment gateway' mod='pms_gopay_extra'}</strong>
								<img style="margin-top: 8px;" src="../modules/{$_PMS_MODULE->name}/views/img/tabs/Shortened.png" alt="">
							</label>
							<br /><br />
							<span>
								{l s='Once you are redirected to a payment gateway page, you will be able to choose the payment method you want to pay for.' mod='pms_gopay_extra'}
							</span>
						</p>
					{/if}
				{/foreach}
				<hr style="border-bottom: 1px solid #ddd">
			{/if}

			{if $smarty.const._PS_VERSION_ < 1.7 || $REPEAT}
				<p class="radio-inline">
					<button type="submit"
						class="btn btn-success col-sm-3 pull-right{if $SKIP_STEP || $REPEAT} payment-overlay{/if}"
						{if ($INLINE_MODE && $SKIP_STEP) || (isset($REPEAT) && ($INLINE_MODE && $REPEAT))}
							onClick="inlineFunction('{$payment_url}', $('#{$_PMS_MODULE->name}-window-form').serialize()); return false;"
						{/if}
					>
						{l s='Pay my order' mod='pms_gopay_extra'}
					</button>
				</p>
			{/if}
		</div>
	</form>
{/if}
