
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
{if $smarty.const._PS_VERSION_ >= 1.6}
	<div id="gopay_refund" class="order_detail_win">
			<div class="panel-heading"><img src="{$_MODULE_DIR}/logo.png" alt="" width="16" /> {l s='GoPay Refund' mod='pms_gopay_extra'}</div>
{else}
	<br />
	<fieldset {if isset($ps_version) && ($ps_version < '1.5')}style="width: 400px"{/if}>
		<legend><img src="{$_MODULE_DIR}/logo.png" alt="" width="16" />{l s='GoPay Refund' mod='pms_gopay_extra'}</legend>
		<p><b>{l s='Information:' mod='pms_gopay_extra'}</b> {l s='Funds are ready to be refunded before confirmation' mod='pms_gopay_extra'}</p>
{/if}
			{if isset($paymentErrors) && $paymentErrors}
				<div class="module_error alert alert-danger">
					<button type="button" class="close" data-dismiss="alert">×</button>
					{$paymentErrors}
				</div>
			{/if}
			{if  $list_refunds|@count gt 0} 
				<table class="table" width="100%" cellspacing="0" cellpadding="0">
				  <tr>
				    <th>{l s='Refund date' mod='pms_gopay_extra'}</th>
				    <th>{l s='Refund amount' mod='pms_gopay_extra'}</th> 
				    <th>{l s='Refund result' mod='pms_gopay_extra'}</th>
				  </tr>
				{foreach from=$list_refunds item=list}
				  <tr>
				    <td>{Tools::displayDate($list.date_add, $smarty.const.null,true)}</td>
				    <td>{displayPrice currency=$order->id_currency price=$list.refund_amount}</td> 
				    <td>{$list.result}</td>
				  </tr>
				{/foreach}
				</table>
			{/if}
			<form method="post" action="{$smarty.server.REQUEST_URI|escape:'htmlall':'UTF-8'}">
				<input type="hidden" name="id_order" value="{$order->id}" />
				<div class="well hidden-print">
					<div class="form-group" style="display: inline-block;">
						{l s='It is still' mod='pms_gopay_extra'} {displayPrice currency=$order->id_currency price=$refund_amount} {l s='to refund.' mod='pms_gopay_extra'}
					</div>
					{if $refund_amount > 0}
						<center>
							<select name="refund_type" onchange="displayTypeRefund(this.value)" style="width: 150px;">
								{if !$partialy_refund}
									<option value="1" selected="selected">{l s='Refund whole amount' mod='pms_gopay_extra'}</option>
								{/if}
								<option value="2" {if $partialy_refund}selected="selected"{/if}>{l s='Partially refund' mod='pms_gopay_extra'}</option>
							</select>
						</center>
					{/if}
				</div>
				<div id="_PARTIALY" style="display:{if $partialy_refund}block{else}none{/if}">
					<div class="row">
						{foreach $products as $product}
							<div class="form-group col-lg-12">
								<input type="checkbox" name="sel_products[]" value="{$product['id_order_detail']}" id="input_{$product['id_order_detail']}">
								<label for="input_{$product['id_order_detail']}" class="control-label">
									{$product['product_name']}
								</label>
								<div class="col-lg-12">
									<span class="col-lg-3">
										<input type="text" name="sel_quantity[{$product['id_order_detail']}]" value="{$product['product_quantity']}">
									</span>
									<span class="col-lg-2">
										{l s='pcs' mod='pms_gopay_extra'}
									</span>
									<span class="col-lg-4">
										<input type="text" name="sel_price[{$product['id_order_detail']}]" value="{$product['unit_price_tax_incl']|round:2}">
									</span>
									<span class="col-lg-3">
										{$currency->sign}<br>({l s='with VAT' mod='pms_gopay_extra'})
									</span>
								</div>
							</div>
						{/foreach}
					</div>
					{if !$refunded_shipping && $order->total_shipping_tax_incl>0}
						<div class="row hr">
							<div class="form-group col-lg-12">
								<input type="checkbox" name="sel_shipping" value="1" id="input_shipping" class="col-lg-1">
								<label for="input_shipping" class="control-label col-lg-5">
										{$carrier->name}
								</label>
								<span class="col-lg-3">
									<input type="text" name="sel_price[{$product['id_order_detail']}]" value="{$order->total_shipping_tax_incl|round:2}">
								</span>
								<span class="col-lg-3">
									{$currency->sign}<br>({l s='with VAT' mod='pms_gopay_extra'})
								</span>
							</div>
						</div>
					{/if}
					{if count($discounts)>0}
						<div class="row hr">
							<div class="form-group col-lg-12">
								<label for="input_discount" class="control-label col-lg-12">
									<span class="col-lg-2">
										<input type="checkbox" name="sel_discounts" value="1" id="input_discount">
									</span>
									<span class="col-lg-10">
										{l s='Subtract discount coupons' mod='pms_gopay_extra'}
									</span>
								</label>
							</div>
						</div>
					{/if}
				</div>

				{if $refund_amount > 0}
					<div class="well hidden-print">
						<p><b>{l s='Information:' mod='pms_gopay_extra'}</b> {l s='Funds are ready to be refunded before confirmation' mod='pms_gopay_extra'}</p>
					</div>
					<center>
						<button type="submit" class="btn btn-default" name="submitGoPayRefund" onclick="if (!confirm('{l s='Are you sure you want to refund?' mod='pms_gopay_extra'}'))return false;">
							<i class="icon-save"></i>
							<span style="margin-left: 15px;">{l s='Refund transaction' mod='pms_gopay_extra'}</span>
						</button>
					</center>
				{/if}
			</form>
{if $smarty.const._PS_VERSION_ >= 1.6}
	</div>
{else}
	</fieldset>
{/if}
