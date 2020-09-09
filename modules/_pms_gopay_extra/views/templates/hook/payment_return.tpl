
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
 * ########################################################################### **}<div id="gopay_confirmation">
{if isset($paymentStatus->errors) && is_array($paymentStatus->errors)}
	<p class="error alert alert-danger">
		{foreach $paymentStatus->errors as $error}
			{l s='Payment status Error:' mod='pms_gopay_extra'} {$error->error_code}
			{if isset($error->error_name)} - {$error->error_name}<br />{/if}
			{if isset($error->message)}{$error->message}<br />{/if}
			{if isset($error->description)}{$error->description}{/if}
		{/foreach}
	</p>
{else}
		{assign var='checked' value=false}
		{assign var='paymentMessage' value=''}
		{assign var='repeatPayment' value=''}
		{if isset($paymentStatus->confirms) && is_array($paymentStatus->confirms)}
			{foreach $paymentStatus->confirms as $confirm}
				{assign var='paymentMessage' value=$confirm->paymentMessage}
				{assign var='repeatPayment' value=$confirm->repeatPayment}
			{/foreach}
		{/if}

		{if $repeatPayment}
			<!-- platba nebyla dokončena -->
			<div id="gopay_error" class="error alert alert-danger">{$paymentMessage}</div>
			{if !$GATEWAY_MODE || $BUTTONS_MODE == 1}
				{include file="{$temlate_folder}/hook/shortened.tpl"}
			{else}
				{if $BUTTONS_MODE == 2}
					{include file="{$temlate_folder}/hook/extended.tpl"}
				{else}
					{include file="{$temlate_folder}/hook/windowed.tpl"}
				{/if}
			{/if}
		{else}

			<!-- Payment accepted -->
			<p class="alert alert-success">{$paymentMessage}</p>
			<div style="margin:40px 0;">
				<p class="info_gopay">{l s='Thank you for your order.' mod='pms_gopay_extra'}</p>
				<p class="info_gopay bold">{l s='Ordered items will be shipped within the next hours (excluding weekends and holidays) from receipt of payment.' mod='pms_gopay_extra'}</p>
				<p class="info_gopay">{l s='Order confirmation with the summary has been sent to your e-mail. In case you do not receive it within the next hour, please' mod='pms_gopay_extra'} &nbsp;
					<a href="{$link->getPageLink('contact', true, NULL)}" title="{l s='contact us' mod='pms_gopay_extra'}">
						{l s='contact us' mod='pms_gopay_extra'}
					</a>.
				</p>
			</div>
		{/if}
	{/if}
</div>