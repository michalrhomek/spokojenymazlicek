{* ########################################################################### */
/*                                                                             */
/*                      Copyright 2014     Miloslav Kubín                      */
/*                        http://presta-modul.shopmk.cz                        */
/*                                                                             */
/*             Please do not change this text, remove the link,                */
/*          or remove all or any part of the creator copyright notice          */
/*                                                                             */
/*    Please also note that although you are allowed to make modifications     */
/*     for your own personal use, you may not distribute the original or       */
/*                 the modified code without permission.                       */
/*                                                                             */
/*                    SELLING AND REDISTRIBUTION IS FORBIDDEN!                 */
/*             Download is allowed only from presta-modul.shopmk.cz            */
/*                                                                             */
/*       This software is provided as is, without warranty of any kind.        */
/*           The author shall not be liable for damages of any kind.           */
/*               Use of this software indicates that you agree.                */
/*                                                                             */
/*                                    ***                                      */
/*                                                                             */
/*              Prosím, neměňte tento text, nemazejte odkazy,                  */
/*      neodstraňujte části a nebo celé oznámení těchto autorských práv        */
/*                                                                             */
/*     Prosím vezměte také na vědomí, že i když máte možnost provádět změny    */
/*        pro vlastní osobní potřebu,nesmíte distribuovat původní nebo         */
/*                        upravený kód bez povolení.                           */
/*                                                                             */
/*                   PRODEJ A DISTRIBUCE JE ZAKÁZÁNA!                          */
/*          Download je povolen pouze z presta-modul.shopmk.cz                 */
/*                                                                             */
/*   Tento software je poskytován tak, jak je, bez záruky jakéhokoli druhu.    */
/*          Autor nenese odpovědnost za škody jakéhokoliv druhu.               */
/*                  Používáním tohoto softwaru znamená,                        */
/*           že souhlasíte s výše uvedenými autorskými právy .                 */
/*                                                                             */
/* ########################################################################### *}

{if $tax_exempt || ((isset($product_tax_breakdown) && $product_tax_breakdown|@count > 0) || (isset($ecotax_tax_breakdown) && $ecotax_tax_breakdown|@count > 0))}
<!--  TAX DETAILS -->

			{if $tax_exempt}
			<p style="font-size:{$font_width+7}pt;">
				{l s='Exempt of VAT according section 259B of the General Tax Code.'  mod='add_faktura'}
				<br><br><br>
				{l s='Current exchange rate of the Czech National Bank'  mod='add_faktura'} {$kurz_CNB} {$tax_currency->sign} {l s='of'  mod='add_faktura'} {$kurz_datum}
			</p>
			{else}
			{assign var='total_tax' value=0}
			{assign var='total_price' value=0}
			{assign var='_kurz_CNB_' value=$kurz_CNB|replace:',':'.'}
			<table style="width: 55%; font-size:{$font_width+6}pt;">
				<tr style="line-height:4px; background-color: #4D4D4D; color: #FFFFFF; font-weight: bold;">
					<td style="text-align: left; width: 30%">{l s='Tax Detail'  mod='add_faktura'} ({$tax_currency->iso_code})</td>
					<td style="text-align: right; width: 23%">{l s='Tax Rate'  mod='add_faktura'}</td>
					{if !$use_one_after_another_method}
						<td style="text-align: right; width: 23%">{l s='Total Tax Excl'  mod='add_faktura'}</td>
					{/if}
					<td style="text-align: right; width: 23%">{l s='Total Tax'  mod='add_faktura'}</td>
				</tr>

				{if isset($product_tax_breakdown)}
					{foreach $product_tax_breakdown as $rate => $product_tax_infos}
					<tr style="line-height:4px;">
					 <td style="width: 30%">
						{if !isset($pdf_product_tax_written)}
							{l s='Products:'  mod='add_faktura'}
							{assign var=pdf_product_tax_written value=1}
						{/if}
					</td>
					 <td style="width: 23%; text-align: right;">{$rate} %</td>
					{if !$use_one_after_another_method}
					 <td style="width: 23%; text-align: right;">
						{if isset($is_order_slip) && $is_order_slip}- {/if}
						{number_format($product_tax_infos.total_price_tax_excl * $_kurz_CNB_, 2, ',', ' ')} {$tax_currency->sign}
						{assign var='total_tax' value=$total_tax + $product_tax_infos.total_price_tax_excl}
					 </td>
					{/if}
					 <td style="width: 23%; text-align: right;">
					 	{if isset($is_order_slip) && $is_order_slip}- {/if}
						{number_format($product_tax_infos.total_price_tax_excl * {$rate}/100 * $_kurz_CNB_, 2, ',', ' ')} {$tax_currency->sign}
						{assign var='total_price' value=$total_price + ($product_tax_infos.total_price_tax_excl * {$rate}/100)}
					</td>
					</tr>
					{/foreach}
				{/if}

				{if isset($shipping_tax_breakdown)}
					{foreach $shipping_tax_breakdown as $shipping_tax_infos}
					<tr style="line-height:4px;">
					 <td style="width: 30%">
						{if !isset($pdf_shipping_tax_written)}
							{l s='Shipping:'  mod='add_faktura'}
							{assign var=pdf_shipping_tax_written value=1}
						{/if}
					 </td>
					 <td style="width: 23%; text-align: right;">{$shipping_tax_infos.rate} %</td>
					{if !$use_one_after_another_method}
						<td style="width: 23%; text-align: right;">
							{if isset($is_order_slip) && $is_order_slip}- {/if}
							{number_format($shipping_tax_infos.total_tax_excl * $_kurz_CNB_, 2, ',', ' ')} {$tax_currency->sign}
							{assign var='total_tax' value=$total_tax + $shipping_tax_infos.total_tax_excl}
						 </td>
					{/if}
					<td style="width: 23%; text-align: right;">
						{if isset($is_order_slip) && $is_order_slip}- {/if}
						{number_format($shipping_tax_infos.total_amount * $_kurz_CNB_, 2, ',', ' ')} {$tax_currency->sign}
						{assign var='total_price' value=$total_price + $shipping_tax_infos.total_amount}
					</td>
					</tr>
					{/foreach}
				{/if}

				{if isset($ecotax_tax_breakdown)}
					{foreach $ecotax_tax_breakdown as $ecotax_tax_infos}
						{if $ecotax_tax_infos.ecotax_tax_excl > 0}
						<tr style="line-height:4px;">
							<td style="width: 30%">{l s='Ecotax:'  mod='add_faktura'}</td>
							<td style="width: 23%; text-align: right;">{$ecotax_tax_infos.rate  } %</td>
							{if !$use_one_after_another_method}
								<td style="width: 23%; text-align: right;">
									{if isset($is_order_slip) && $is_order_slip}- {/if}
									{number_format($ecotax_tax_infos.ecotax_tax_excl * $_kurz_CNB_, 2, ',', ' ')} {$tax_currency->sign}
									{assign var='total_tax' value=$total_tax + $ecotax_tax_infos.ecotax_tax_excl}
								</td>
							{/if}
							<td style="width: 23%; text-align: right;">
								{if isset($is_order_slip) && $is_order_slip}- {/if}
								{number_format(($ecotax_tax_infos.ecotax_tax_incl - $ecotax_tax_infos.ecotax_tax_excl) * $_kurz_CNB_, 2, ',', ' ')} {$tax_currency->sign}
								{assign var='total_price' value=$total_price + ($ecotax_tax_infos.ecotax_tax_incl - $ecotax_tax_infos.ecotax_tax_excl)}
							</td>
						</tr>
						{/if}
					{/foreach}
				{/if}

				{if $order_invoice->total_discount_tax_incl > 0}
						<tr style="line-height:4px;">
							<td style="width: 30%">{l s='Total Vouchers:'  mod='add_faktura'}</td>
							<td style="width: 23%; text-align: right;"></td>
							{if !$use_one_after_another_method}
								<td style="width: 23%; text-align: right;">
									{if !isset($is_order_slip) && !$is_order_slip}- {/if}
									{displayPrice currency=$order->id_currency price=($order_invoice->total_discount_tax_excl)}
									{assign var='total_tax' value=$total_tax - $order_invoice->total_discount_tax_excl}
								</td>
							{/if}
							<td style="width: 23%; text-align: right;">
								{if !isset($is_order_slip) && !$is_order_slip}- {/if}
								{number_format(($order_invoice->total_discount_tax_incl - $order_invoice->total_discount_tax_excl) * $_kurz_CNB_, 2, ',', ' ')} {$tax_currency->sign}
								{assign var='total_price' value=$total_price - ($order_invoice->total_discount_tax_incl - $order_invoice->total_discount_tax_excl)}
							</td>
						</tr>
				{/if}
				<tr style="line-height:4px; border:#121111 solid 1px;">
					<td style="width: 30%; border-top:0px solid #000;">{l s='Total:'  mod='add_faktura'}</td>
					<td style="width: 23%; border-top:0px solid #000;"></td>
					<td style="width: 23%; text-align: right; border-top:0px solid #000;">{number_format($total_tax * $_kurz_CNB_, 2, ',', ' ')} {$tax_currency->sign}</td>
					<td style="width: 23%; text-align: right; border-top:0px solid #000;">{number_format($total_price * $_kurz_CNB_, 2, ',', ' ')} {$tax_currency->sign}
					</td>
				</tr>
			</table>
			<p style="font-size:{$font_width+7}pt;">
				{if $tax_other_country}
					<br><br><br>
					{l s='Current exchange rate of the Czech National Bank'  mod='add_faktura'} {$kurz_CNB} {$tax_currency->sign} {l s='of'  mod='add_faktura'} {$kurz_datum}
				{/if}
			</p>
			{/if}
<!--  / TAX DETAILS -->
{/if}