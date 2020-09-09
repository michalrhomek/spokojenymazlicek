
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
 * ########################################################################### **}{*  verze 1.5 - 1.6   *}


{assign var="repeat_url" value=$link->getModuleLink('pms_gopay_extra', 'repeatPayment', [], true)|escape:'html':'UTF-8'}

{if $smarty.const._PS_VERSION_ < 1.6}
{capture name=path}<a href="{$link->getPageLink('my-account', true)}">{l s='My account' mod='pms_gopay_extra'}</a><span class="navigation-pipe">{$navigationPipe}</span>{l s='Order history' mod='pms_gopay_extra'}{/capture}
{include file="$tpl_dir./errors.tpl"}

<h1>{l s='Order history' mod='pms_gopay_extra'}</h1>
<p>{l s='Here are the orders you\'ve placed since your account was created.' mod='pms_gopay_extra'}.</p>

{if $slowValidation}<p class="warning">{l s='If you have just placed an order, it may take a few minutes for it to be validated. Please refresh this page if your order is missing.' mod='pms_gopay_extra'}</p>{/if}

<div class="block-center" id="block-history">
	{if $orders && count($orders)}
	<table id="order-list" class="std">
		<thead>
			<tr>
				<th class="first_item">{l s='Order Reference'}</th>
				<th class="item">{l s='Date'}</th>
				<th class="item">{l s='Total price'}</th>
				<th class="item">{l s='Payment'}</th>
				<th class="item">{l s='Status'}</th>
				<th class="item">{l s='Invoice'}</th>
				<th class="last_item" style="width:65px">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
		{foreach from=$orders item=order name=myLoop}
			<tr class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if} {if $smarty.foreach.myLoop.index % 2}alternate_item{/if}">
				<td class="history_link bold">
					{if isset($order.invoice) && $order.invoice && isset($order.virtual) && $order.virtual}<img src="{$img_dir}icon/download_product.gif" class="icon" alt="{l s='Products to download'}" title="{l s='Products to download'}" />{/if}
					<a class="color-myaccount" href="javascript:showOrder(1, {$order.id_order|intval}, '{$link->getPageLink('order-detail', true)}');">{Order::getUniqReferenceOf($order.id_order)}</a>
				</td>
				<td class="history_date bold">{dateFormat date=$order.date_add full=0}</td>
				<td class="history_price"><span class="price">{displayPrice price=round($order.total_paid) currency=$order.id_currency no_utf8=false convert=false}</span></td>
				<td class="history_method">{$order.payment|escape:'htmlall':'UTF-8'}</td>
				<td class="history_state">
                	{if isset($order.order_state)}{$order.order_state|escape:'htmlall':'UTF-8'}{/if}

{*  moje uprava  *}
					{if $order.module == $_PMS_MODULE->name
						&& $_PMS_MODULE->functions->isRegistered()
						&& ($order.current_state == $_PMS_PAYMENT_NEW_
							|| $order.current_state == $_PMS_PAYMENT_CHOSEN_
							|| $order.current_state == $_PMS_PAYMENT_TIMEOUT_
							|| $order.current_state == $_PS_OS_ERROR_
							|| $order.current_state == $_PMS_PAYMENT_CANCELED_)
					}
						<form action="{$repeat_url}" method="post" id="form-{$order.id_order}" class="text-sm-center" style="margin-top: 10px;">
							<input type="hidden" name="orderId" value="{$order.id_order}">
							<button type="submit"
									class="btn-success payment-overlay"
									{if $INLINE_MODE}
										onClick="inlineFunction('{$repeat_url}', $('#form-{$order.id_order}').serialize()); return false;"
									{/if}
							>
								{$_PMS_MODULE->payAgain}
							</button>
						</form>
					{/if}

					{assign var='id_session' value=$RECURRENT->getReccurenceStarted($order.id_order|intval)}
					{if $id_session}
						<div class="conf" id="conf_{$order.id_order}" style="display:none">{l s='Recurring payments were terminated' mod='pms_gopay_extra'}</div>
						<div class="error" id="error_{$order.id_order}" style="display:none"></div>
						<p style="margin-top:10px;">
							<a  href="#"
								title="{l s='Cancel recurrent' mod='pms_gopay_extra'}"
								class="btn btn-default button button-small"
								id="cancel_recurr_{$order.id_order}"
								onclick="if (confirm('{l s='Update selected items?' mod='pms_gopay_extra'}'))cancelRecurrent({$order.id_order|intval}, {$id_session}); return false;"
								
							>
								<span>{l s='Cancel recurrent' mod='pms_gopay_extra'}</span>
							</a>
						</p>
					{/if}
{* konec moje uprava  *}
 
                </td>
				<td class="history_invoice">
				{if (isset($order.invoice) && $order.invoice && isset($order.invoice_number) && $order.invoice_number) && isset($invoiceAllowed) && $invoiceAllowed == true}
					<a href="{$link->getPageLink('pdf-invoice', true, NULL, "id_order={$order.id_order}")}" title="{l s='Invoice'}" target="_blank"><img src="{$img_dir}icon/pdf.gif" alt="{l s='Invoice'}" class="icon" /></a>
					<a href="{$link->getPageLink('pdf-invoice', true, NULL, "id_order={$order.id_order}")}" title="{l s='Invoice'}" target="_blank">{l s='PDF'}</a>
				{else}-{/if}
				</td>
				<td class="history_detail">
					<a class="color-myaccount" href="javascript:showOrder(1, {$order.id_order|intval}, '{$link->getPageLink('order-detail', true)}');">{l s='details'}</a>
					{if isset($opc) && $opc}
					<a href="{$link->getPageLink('order-opc', true, NULL, "submitReorder&id_order={$order.id_order}")}" title="{l s='Reorder'}">
					{else}
					<a href="{$link->getPageLink('order', true, NULL, "submitReorder&id_order={$order.id_order}")}" title="{l s='Reorder'}">
					{/if}
						<img src="{$img_dir}arrow_rotate_anticlockwise.png" alt="{l s='Reorder'}" title="{l s='Reorder'}" class="icon" />
					</a>
				</td>
			</tr>
		{/foreach}
		</tbody>
	</table>
	<div id="block-order-detail" class="hidden">&nbsp;</div>
	{else}
		<p class="warning">{l s='You have not placed any orders.'}</p>
	{/if}
</div>

<ul class="footer_links clearfix">
	<li><a href="{$link->getPageLink('my-account', true)}"><img src="{$img_dir}icon/my-account.gif" alt="" class="icon" /> {l s='Back to Your Account'}</a></li>
	<li class="f_right"><a href="{$base_dir}"><img src="{$img_dir}icon/home.gif" alt="" class="icon" /> {l s='Home'}</a></li>
</ul>

{else}
	{* ------------------------------------------------------------- */
	/*  PRO VERZE 1.6
	/* ------------------------------------------------------------- *}

{capture name=path}
	<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
		{l s='My account' mod='pms_gopay_extra'}
	</a>
	<span class="navigation-pipe">{$navigationPipe}</span>
	<span class="navigation_page">{l s='Order history' mod='pms_gopay_extra'}</span>
{/capture}
{include file="$tpl_dir./errors.tpl"}
<h1 class="page-heading bottom-indent">{l s='Order history' mod='pms_gopay_extra'}</h1>
<p class="info-title">{l s='Here are the orders you\'ve placed since your account was created.' mod='pms_gopay_extra'}</p>
{if $slowValidation}
	<p class="alert alert-warning">{l s='If you have just placed an order, it may take a few minutes for it to be validated. Please refresh this page if your order is missing.' mod='pms_gopay_extra'}</p>
{/if}
<div class="block-center" id="block-history">
	{if $orders && count($orders)}
		<table id="order-list" class="table table-bordered footab">
			<thead>
				<tr>
					<th class="first_item" data-sort-ignore="true">{l s='Order reference' mod='pms_gopay_extra'}</th>
					<th class="item">{l s='Date' mod='pms_gopay_extra'}</th>
					<th data-hide="phone" class="item">{l s='Total price' mod='pms_gopay_extra'}</th>
					<th data-sort-ignore="true" data-hide="phone,tablet" class="item">{l s='Payment' mod='pms_gopay_extra'}</th>
					<th class="item">{l s='Status' mod='pms_gopay_extra'}</th>
					<th data-sort-ignore="true" data-hide="phone,tablet" class="item">{l s='Invoice' mod='pms_gopay_extra'}</th>
					<th data-sort-ignore="true" data-hide="phone,tablet" class="last_item">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$orders item=order name=myLoop}
					<tr class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if} {if $smarty.foreach.myLoop.index % 2}alternate_item{/if}">
						<td class="history_link bold">
							{if isset($order.invoice) && $order.invoice && isset($order.virtual) && $order.virtual}
								<img class="icon" src="{$img_dir}icon/download_product.gif"	alt="{l s='Products to download'}" title="{l s='Products to download'}" />
							{/if}
							<a class="color-myaccount" href="javascript:showOrder(1, {$order.id_order|intval}, '{$link->getPageLink('order-detail', true)|escape:'html':'UTF-8'}');">
								{Order::getUniqReferenceOf($order.id_order)}
							</a>
						</td>
						<td data-value="{$order.date_add|regex_replace:"/[\-\:\ ]/":""}" class="history_date bold">
							{dateFormat date=$order.date_add full=0}
						</td>
						<td class="history_price" data-value="{$order.total_paid}">
							<span class="price">
								{displayPrice price=$order.total_paid currency=$order.id_currency no_utf8=false convert=false}
							</span>
						</td>
						<td class="history_method">{$order.payment|escape:'html':'UTF-8'}</td>
						<td data-value="{$order.id_order_state}" class="history_state">
							{if isset($order.order_state)}
								<span class="label{if $order.id_order_state == 1 || $order.id_order_state == 10 || $order.id_order_state == 11} label-info{elseif $order.id_order_state == 5 || $order.id_order_state == 2 || $order.id_order_state == 12} label-success{elseif $order.id_order_state == 6 || $order.id_order_state == 7 || $order.id_order_state == 8} label-danger{elseif $order.id_order_state == 3 || $order.id_order_state == 9 || $order.id_order_state == 4} label-warning{/if}" {if $order.id_order_state > 12}style="background-color:{$order.order_state_color};"{/if}>
									{$order.order_state|escape:'html':'UTF-8'}
								</span>

{*  moje uprava  *}
								{if $order.module == $_PMS_MODULE->name
									&& $_PMS_MODULE->functions->isRegistered()
									&& ($order.current_state == $_PMS_PAYMENT_NEW_
										|| $order.current_state == $_PMS_PAYMENT_CHOSEN_
										|| $order.current_state == $_PMS_PAYMENT_TIMEOUT_
										|| $order.current_state == $_PS_OS_ERROR_
										|| $order.current_state == $_PMS_PAYMENT_CANCELED_)
								}
									<form action="{$repeat_url}" method="post" id="form-{$order.id_order}" class="text-sm-center" style="margin-top: 10px;">
										<input type="hidden" name="orderId" value="{$order.id_order}">
										<button type="submit"
												class="btn-success payment-overlay"
												{if $INLINE_MODE}
													onClick="inlineFunction('{$repeat_url}', $('#form-{$order.id_order}').serialize()); return false;"
												{/if}
										>
											{$_PMS_MODULE->payAgain}
										</button>
									</form>
								{/if}

								{assign var='id_session' value=$RECURRENT->getReccurenceStarted($order.id_order|intval)}
								{if $id_session}
									<div class="conf" id="conf_{$order.id_order}" style="display:none">{l s='Recurring payments were terminated' mod='pms_gopay_extra'}</div>
									<div class="error" id="error_{$order.id_order}" style="display:none"></div>
									<p style="margin-top:10px;">
										<a  href="#"
											title="{l s='Cancel recurrent' mod='pms_gopay_extra'}"
											class="btn btn-default button button-small"
											id="cancel_recurr_{$order.id_order}"
											onclick="if (confirm('{l s='Update selected items?' mod='pms_gopay_extra'}'))cancelRecurrent({$order.id_order|intval}, {$id_session}); return false;"

										>
											<span>{l s='Cancel recurrent' mod='pms_gopay_extra'}</span>
										</a>
									</p>
								{/if}
{* konec moje uprava  *}

							{/if}
						</td>
						<td class="history_invoice">
							{if (isset($order.invoice) && $order.invoice && isset($order.invoice_number) && $order.invoice_number) && isset($invoiceAllowed) && $invoiceAllowed == true}
								<a class="link-button" href="{$link->getPageLink('pdf-invoice', true, NULL, "id_order={$order.id_order}")|escape:'html':'UTF-8'}" title="{l s='Invoice'}" target="_blank">
									<i class="icon-file-text large"></i>{l s='PDF'}
								</a>
							{else}
								-
							{/if}
						</td>
						<td class="history_detail">
							<a class="btn btn-default button button-small" href="javascript:showOrder(1, {$order.id_order|intval}, '{$link->getPageLink('order-detail', true)|escape:'html':'UTF-8'}');">
								<span>
									{l s='Details'}<i class="icon-chevron-right right"></i>
								</span>
							</a>
							{if isset($opc) && $opc}
								<a class="link-button" href="{$link->getPageLink('order-opc', true, NULL, "submitReorder&id_order={$order.id_order}")|escape:'html':'UTF-8'}" title="{l s='Reorder'}">
							{else}
								<a class="link-button" href="{$link->getPageLink('order', true, NULL, "submitReorder&id_order={$order.id_order}")|escape:'html':'UTF-8'}" title="{l s='Reorder'}">
							{/if}
		                        <i class="icon-refresh"></i>{l s='Reorder'}
							</a>
						</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
		<div id="block-order-detail" class="unvisible">&nbsp;</div>
	{else}
		<p class="alert alert-warning">{l s='You have not placed any orders.'}</p>
	{/if}
</div>
<ul class="footer_links clearfix">
	<li>
		<button class="btn btn-default button button-small" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
			<span>
				<i class="icon-chevron-left"></i> {l s='Back to Your Account'}
			</span>
		</button>
	</li>
	<li>
		<a class="btn btn-default button button-small" href="{$base_dir}">
			<span><i class="icon-chevron-left"></i> {l s='Home'}</span>
		</a>
	</li>
</ul>
{/if}
