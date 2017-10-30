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
{assign var="product_tax_brDown" value=0}
{foreach $product_tax_breakdown as $rate => $product_tax_infos}
	{assign var="product_tax_brDown" value=$product_tax_brDown+round($product_tax_infos.total_price_tax_excl*{$rate}/100, 2)}
{/foreach}

{assign var="shipping_tax_brDown" value=0}
{foreach $shipping_tax_breakdown as $shipping_tax_infos}
	{assign var="shipping_tax_brDown" value=$shipping_tax_brDown+$shipping_tax_infos.total_amount}
{/foreach}

{assign var="reduction_amnt" value=0}
{foreach $order_details as $order_detail}
	{if (isset($order_detail.reduction_amount) && $order_detail.reduction_amount > 0)}
		{assign var="reduction_amnt" value=1}
	{/if}
	{if (isset($order_detail.reduction_percent) && $order_detail.reduction_percent > 0)}
		{assign var="reduction_amnt" value=1}
	{/if}
{/foreach}

{assign var="sirka" value=43}

{if !$is_wat}
	{assign var="sirka" value=$sirka+27}
{/if}
{if $reduction_amnt}
	{assign var="sirka" value=$sirka-7}
{/if}

{assign var="Price_excl" value=11}
{assign var="Tax_rate" value=7}
{assign var="Tax" value=9}
{assign var="Price_incl" value=11}
{assign var="Discount" value=7}
{assign var="Qty" value=8}
{assign var="Total" value=11}

<table cellpadding="0" cellspacing="0" align="left" style="width: 100%; font-size:{$font_width+9}pt;">

<!-- ///////////////   dodavatel  ///////////////   -->
	<tr>
		<td rowspan="3" style="border:0px solid #000;">
			<table cellpadding="5" style="width: 100%;">
				<tr>
		   			<td style=" font-size:{$font_width+14}pt; font-weight:bold;">{l s='Supplier:'  mod='add_faktura'}</td>
		   			<td rowspan="2" style="text-align:right">
						{if $logo_path}
		  <!-- ///////////////   logo firmy nahrajte v Konfigurace -*->šablony velikost upravte podle potřeby /////////////// -->  
							<img src="{$logo_path}" style="width:{$width_logo}px; height:{$height_logo}px;" />
						{/if}
					</td>
				</tr>
				<tr>
		   			<td style="height:60px">
						<strong style="color:#000">{$fa_name_shop}</strong><br />
						{$fa_address}<br />
						{$fa_city}<br />
						{$fa_zipcode}<br />
						{$fa_country}
					</td>
				</tr>
				<tr>
		   			<td style="width:20%;">
						{l s='DNI:'  mod='add_faktura'}<br />
						{l s='VAT:' mod='add_faktura'}
						{if ($sk)}
							<br />
							{l s='IČ DPH:' mod='add_faktura'}
						{/if}
					</td>
					<td style="width:80%;">
						{$fa_ico}<br />
						{$fa_dic}
						{if ($sk)}
							<br />
							{$fa_icdph}
						{/if}
					</td>
				</tr>
				<tr>
		   			<td colspan="2" style="height:32px;">
						{$fa_zapis}
					</td>
				</tr>
				<tr>
		   			<td style="border-top:0px dotted #000000; width:20%;">
						<br />
						{l s='Web:'  mod='add_faktura'}<br />
						{l s='Email:'  mod='add_faktura'}<br />
						{l s='Tel.:'  mod='add_faktura'}
					</td>
					<td style="border-top:0px dotted #000000; width:80%;">
						<br />
						{$fa_web}<br />
						{$fa_email}<br />
						{$fa_tel}
					</td>
				</tr>
				<tr>
		   			<td style=" text-align:right; border-top:0px dotted #000000; width:38%;">
						<br /><br />
						{l s='Account number:'  mod='add_faktura'}<br />
						{l s='Bank name:'  mod='add_faktura'}<br />
						{l s='SWIFT:'  mod='add_faktura'}<br />
						{l s='IBAN:'  mod='add_faktura'}<br />
						{l s='Variable symbol:'  mod='add_faktura'}<br />
						{l s='Constant symbol:'  mod='add_faktura'}<br />
					</td>
					<td style=" text-align:left; border-top:0px dotted #000000; width:62%;">
						<br /><br />
						<strong style="color:#000">{$fa_bank_number}</strong><br />
						{$fa_bank_name}<br />
						{$fa_swift}<br />
						{$fa_iban}<br />
						<strong style="color:#000">{$fa_prefix_vs}{$fa_ord_inv}</strong><br />
						{$fa_k_symbol}<br />
					</td>
				</tr>
			</table>
		</td>
<!-- ///////////////  konec dodavatel  ///////////////   -->

	
<!--  číslo faktury -->
		<td style="border:0px solid #000; height:20px; font-size:{$font_width+13}pt; text-align:center; background-color:#EEE; font-weight:bold; line-height: 6px;">
		{l s='Order slip no.'  mod='add_faktura'} &nbsp; {$order_slip_prefix}{'%06d'|sprintf:$order_slip->id}</td>
	</tr>
<!-- konec číslo faktury -->


<!-- ///////////////   Odběratel   ///////////////   -->
	<tr>
	   <td style="border-right:0px solid #000;">
			<table cellpadding="4" style="width: 100%;" border="0">
				<tr><td colspan="2"></td></tr><!-- mezera -->
				
				<tr>
		   			<td colspan="2" style="font-size:{$font_width+12}pt; font-weight:bold; line-height:1px;">{l s='Subscriber:'  mod='add_faktura'}</td>
				</tr>

			{if !empty($inv_adr)}
				{if !$only_inv_adr}
					<tr>
		   				<td style="width:80px; height:80px;"></td><!-- mezera -->
						<td>
							{if $dlv_adr->company}{$dlv_adr->company}<br />{/if}
							{$dlv_adr->firstname} {$dlv_adr->lastname}<br />
							{$dlv_adr->address1}<br />
							{if $dlv_adr->address2}{$dlv_adr->address2}<br />{/if}
							{$dlv_adr->postcode} &nbsp; {$dlv_adr->city}<br />
							{$dlv_adr->country}
							{if $view_telefon}
								<br />{l s='Tel.: ' mod='add_faktura'}
								{if $dlv_adr->phone_mobile}{$dlv_adr->phone_mobile}{else}{$dlv_adr->phone}{/if}
							{/if}
							{if $view_email}<br />{$customer->email}{/if}
						</td>
					</tr>
					<tr>
		   				<td colspan="2" style="font-weight:bold; color:#666; line-height:1px;">{l s='Billing address:'  mod='add_faktura'}</td>
					</tr>
				{/if}
				<tr>
		   			<td style="width:90px; height:{if $only_inv_adr}120{else}70{/if}px;"></td><!-- mezera -->
					<td>
						{if $inv_adr->company}{$inv_adr->company}<br />
						{else}{$inv_adr->firstname} {$inv_adr->lastname}<br />{/if}
						{$inv_adr->address1}<br />
						{if $inv_adr->address2}{$inv_adr->address2}<br />{/if}
						{$inv_adr->postcode} &nbsp; {$inv_adr->city}<br />
						{$inv_adr->country}
						{if $only_inv_adr}
							{if $view_telefon}
								<br />{l s='Tel.: ' mod='add_faktura'}
								{if $inv_adr->phone_mobile}{$inv_adr->phone_mobile}{else}{$inv_adr->phone}{/if}
							{/if}
							{if $view_email}<br />{$customer->email}{/if}
						{/if}
					</td>
				</tr>
			{else}
				<tr>
		   			<td style="width:80px; height:120px;"></td><!-- mezera -->
					<td>
						{if $dlv_adr->company}{$dlv_adr->company}<br />
						{else}{$dlv_adr->firstname} {$dlv_adr->lastname}<br />{/if}
						{$dlv_adr->address1}<br />
						{if $dlv_adr->address2}{$dlv_adr->address2}<br />{/if}
						{$dlv_adr->postcode} &nbsp; {$dlv_adr->city}<br />
						{$dlv_adr->country}
							{if $view_telefon}
								<br />{l s='Tel.: ' mod='add_faktura'}
								{if $dlv_adr->phone_mobile}{$dlv_adr->phone_mobile}{else}{$dlv_adr->phone}{/if}
							{/if}
						{if $view_email}<br />{$customer->email}{/if}
					</td>
				</tr>
			{/if}
			</table>
			<table cellpadding="1" cellspacing="1" style="width:100%;">
				<tr>
					<td colspan="2" style="line-height:1px;"></td>
				</tr>
				
					<tr nobr="true">
						<td style="width:4%;">&nbsp;</td>
						<td style="width:96%;">{if $ic}{l s='Identification number:'  mod='add_faktura'} {$ic}{/if}</td>
					</tr>
				
				
					<tr nobr="true">
						<td>&nbsp;</td>
						<td>{if $dic}
								{if $sk}
									{l s='Tax or VAT number:' mod='add_faktura'}
								{else}
									{l s='VAT number:' mod='add_faktura'}
								{/if} {$dic}
							{/if}</td>
					</tr>
				
				<tr nobr="true">
					<td colspan="2" style="line-height:1px;"></td>
				</tr>
			</table>
	   </td>
	</tr>
<!-- ///////////////  konec Odběratel   ///////////////   -->


<!-- ///////////////   datum vystavení  atd.   ///////////////   -->
	<tr>
		<td style="border-right:0px solid #000000;">
			<table cellpadding="0" cellspacing="0" style="width: 100%; border-top:0px solid #000; border-bottom:0px solid #000;">
        		<tr style="text-align:center; line-height:5px;">
        			<td style="width:33%; height:20px; color:#093; border-right:0px solid #000;">
						{l s='Issue date'  mod='add_faktura'}<br />
						<span style="color:#000">{dateFormat date=$order_slip->date_add full=0}</span>
					</td>
					<td style="width:34%; height:15px; color:#06C; border-right:0px solid #000;">
					{if $is_wat}
						{l s='Date taxable supply'  mod='add_faktura'}<br />
						<span style="color:#000">{dateFormat date=$order_slip->date_add full=0}</span>
					{/if}
					</td>
            		<td style="width:33%; height:10px; color:#C30;">
					{if $date_due}
						{l s='Due date'  mod='add_faktura'}<br />
						<strong style="color:#000">{dateFormat date=$slipDate_due full=0}</strong>
					{/if}
					</td>
				</tr>
			</table>

			<table cellpadding="1" cellspacing="1" style="width:100%; line-height:3px; border-top:0px dotted #000000;">
				<tr style="line-height:0pt;">
					<td colspan="2" style="line-height:1px;"></td>
				</tr>
				<tr nobr="true">
					<td style="width:4%;">&nbsp;</td>
					<td style="width:34%;">{l s='Order number:'  mod='add_faktura'}</td>
					<td style="width:62%;">#{'%06d'|sprintf:$order->id}</td>
				</tr>
				<tr nobr="true">
					<td>&nbsp;</td>
					<td>{l s='Payment method:'  mod='add_faktura'}</td>
					<td>{$order->payment}</td>
				</tr>
					<tr nobr="true">
					<td>&nbsp;</td>
					<td>{l s='Shipping method:'  mod='add_faktura'}</td>
					<td>{$carrier->name}</td>
				</tr>
				<tr nobr="true">
					<td colspan="2" style="line-height:1px;"></td>
				</tr>
			</table>
        </td>
    </tr>
<!-- ///////////////  konec datum vystavení  atd.   ///////////////   -->
	
<!--  malá mezera -->
	<tr>
	   <td colspan="2" style="border:0px solid #000; height:2px; font-size:1px;"></td>
	</tr>
<!-- konec malá mezera -->

<!-- ///////////////   záhlaví tabulky produktů   ///////////////   -->
	<tr>
	   <td colspan="2" style="border:0px solid #000;">
			<table cellpadding="2" style="width: 100%; font-size:{$font_width+8}pt;">
				<tr style="line-height:4px; background-color: #999; color: #FFF; text-align: right; font-weight: bold;">
					<td style="text-align: left; width: {$sirka}%; line-height:6px;"> &nbsp; {l s='Product / Reference'  mod='add_faktura'}</td>
					<!-- unit price tax excluded is mandatory -->
					
{if $is_wat}
						<td style=" width: {$Price_excl}%">{l s='Unit Price'  mod='add_faktura'}<br />{l s='(Tax Excl.)'  mod='add_faktura'}</td>
					{/if}
					
{if $is_wat}
						<td style="width: {$Tax_rate}%;">{l s='Tax Rate'  mod='add_faktura'}</td>
						<td style="width: {$Tax}%; text-align:center;">{l s='Tax'  mod='add_faktura'}</td>
					{/if}
					<td style="width: {$Price_incl}%">
						{l s='Unit Price'  mod='add_faktura'}
						
{if !$is_wat}
							 <!--{l s='(Tax Excl.)'  mod='add_faktura'}-->
						{else}
							 <br />{l s='(Tax Incl.)'  mod='add_faktura'}

						{/if}
					</td>
					{if $reduction_amnt}
						<td style="width: {$Discount}%;">{l s='Discount'  mod='add_faktura'}</td>
					{/if}
					<td style="width: {$Qty}%; text-align:center;">{l s='Qty'  mod='add_faktura'}</td>
					<td style="width: {$Total}%;">
						{l s='Total'  mod='add_faktura'}<br />
						
{if !$is_wat}
							<!--{l s='(Tax Excl.)'  mod='add_faktura'}-->
						{else}
							{l s='(Tax Incl.)'  mod='add_faktura'}
						{/if}
					</td>
				</tr>
		</table>
	   </td>
	</tr>
<!-- ///////////////  konec záhlaví tabulky produktů   ///////////////   -->


<!-- ///////////////   detaily tabulky produktů   ///////////////   -->
	<tr>
		<td colspan="2" style="border:0px solid #000;">
			<table cellpadding="3" style="width:100%; font-size:{$font_width+7}pt;">
				<tr>
					<td>
					{if $slipText}
						<p style=" color: #5B7DEB;">
			{l s='Opravný daňový doklad k daňovému dokladu č.'  mod='add_faktura'} &nbsp; {$invoice_prefix}{'%06d'|sprintf:$order->invoice_number}<br>
			{l s='Důvod opravy :'  mod='add_faktura'} &nbsp; {$slipText}
						</p>
					{/if}
					</td>
				</tr>
			</table>
			<table cellpadding="3" style="width:100%; font-size:{$font_width+8}pt; text-align: right;">
			{foreach $order_details as $order_detail}
				{cycle values='#FFF,#DDD' assign=bgcolor}
				<tr style="line-height:4px; background-color:{$bgcolor};">
					<td style="text-align:left; width:{$sirka}%;">
						{$order_detail.product_name}{if $order_detail.product_reference}<br />{$order_detail.product_reference}{/if}
					</td>
					<!-- unit price tax excluded is mandatory -->
					
{if $is_wat}
						<td style=" width:{$Price_excl}%;">
							- {number_format($order_detail.unit_price_tax_excl, 2, ',', ' ')} {$currency->sign}
						</td>
					{/if}
					
{if $is_wat}
					<td style="width:{$Tax_rate}%;">{Tax::getProductTaxRate({$order_detail.id_product})} %</td>
					<td style="width:{$Tax}%;">- {number_format($order_detail.unit_price_tax_incl-$order_detail.unit_price_tax_excl, 2, ',', ' ')} {$currency->sign}</td>
					{/if}
					<td style="width:{$Price_incl}%;">
					
{if !$is_wat}
						- {number_format($order_detail.unit_price_tax_excl, 2, ',', ' ')} {$currency->sign}
					{else}
						- {number_format($order_detail.unit_price_tax_incl, 2, ',', ' ')} {$currency->sign}
					{/if}
					</td>
					{if $reduction_amnt}
						<td style="width:{$Discount}%;">
						{if (isset($order_detail.reduction_amount) && $order_detail.reduction_amount > 0)}
							{number_format($order_detail.reduction_amount, 2, ',', ' ')} {$currency->sign}
						{else if (isset($order_detail.reduction_percent) && $order_detail.reduction_percent > 0)}
							{$order_detail.reduction_percent}%
						{else}
							--
						{/if}
						</td>
					{/if}
					<td style="text-align:center; width:{$Qty}%;">{$order_detail.product_quantity}</td>
					<td style="width:{$Total}%;">
					
{if !$is_wat}
						- {number_format($order_detail.total_price_tax_excl, 2, ',', ' ')} {$currency->sign}
					{else}
						- {number_format($order_detail.total_price_tax_incl, 2, ',', ' ')} {$currency->sign}
					{/if}
					</td>
				</tr>
		{foreach $order_detail.customizedDatas as $customizationPerAddress}
			{foreach $customizationPerAddress as $customizationId => $customization}
				<tr style="line-height:6px;background-color:{$bgcolor}; ">
					<td style="line-height:3px; text-align: left; width: 60%; vertical-align: top">
						<blockquote>
						{if isset($customization.datas[$smarty.const._CUSTOMIZE_TEXTFIELD_]) && count($customization.datas[$smarty.const._CUSTOMIZE_TEXTFIELD_]) > 0}
							{foreach $customization.datas[$smarty.const._CUSTOMIZE_TEXTFIELD_] as $customization_infos}
								{$customization_infos.name}: {$customization_infos.value}
								{if !$smarty.foreach.custo_foreach.last}<br />
								{else}
									<div style="line-height:0.4pt">&nbsp;</div>
								{/if}
							{/foreach}
						{/if}
						{if isset($customization.datas[$smarty.const._CUSTOMIZE_FILE_]) && count($customization.datas[$smarty.const._CUSTOMIZE_FILE_]) > 0}
							{count($customization.datas[$smarty.const._CUSTOMIZE_FILE_])} {l s='image(s)'  mod='add_faktura'}
						{/if}
						</blockquote>
					</td>
					<td style="text-align: right; width: 15%"></td>
					<td style="text-align: center; width: 10%; vertical-align: top">({$customization.quantity})</td>
					<td style="width: 15%; text-align: right;"></td>
				</tr>
			{/foreach}
		{/foreach}
			{/foreach}
				<!-- END PRODUCTS -->
			</table>
		</td>
	</tr>
<!-- ///////////////  konec detaily tabulky produktů   ///////////////   -->


<!-- /////////////// soupis cen   ///////////////  -->
	<tr>
	  <td align="bottom" style="weight:50%;">
	   	<table cellpadding="5" style="font-size:{$font_width+7}pt;">
			<tr>
				<td>
				{if $slipBankNumber}
					<p style=" color: #5B7DEB;">
						<br><br>
						{l s='Dobropis bude uhrazen na Váš účet č.:'  mod='add_faktura'} &nbsp; {$slipBankNumber}
					</p>
				{/if}
				</td>
			</tr>
		</table>
	  </td>
	  <td align="right" style="border-right:0px solid #000; weight:50%;">
	   	<table cellpadding="2" style="line-height:3px; text-align: right; font-weight:bold; font-size:{$font_width+7}pt;">
			<tr style="line-height:0px;">
				<td rowspan="40" style="width:20%;"></td>
				<td style="width:52%;"></td>
				<td style="width:28%;"></td>
			</tr>
			{if $order_slip->shipping_cost_amount}
				<tr style="">
					<td style="">{l s='Shipping'  mod='add_faktura'}</td>
					<td style="">- {displayPrice currency=$order->id_currency price=$order_slip->shipping_cost_amount}</td>
				</tr>
			{/if}
			
			{if (($order->total_paid_tax_incl - $order->total_paid_tax_excl) > 0)}
				<tr style="">
					<td style="">{l s='Product Total (Tax Excl.)'  mod='add_faktura'}</td>
					<td style="">- {displayPrice currency=$order->id_currency price=$order->total_products}</td>
				</tr>
				<tr style="">
					<td style="">{l s='Product Total (Tax Incl.)'  mod='add_faktura'}</td>
					<td style="">- {displayPrice currency=$order->id_currency price=$order->total_products_wt}</td>
				</tr>
			{else}
				<tr style="">
					<td style="">{l s='Product Total'  mod='add_faktura'}</td>
					<td style="">- {displayPrice currency=$order->id_currency price=$order->total_products}</td>
				</tr>
			{/if}

			{if $order->total_wrapping_tax_incl > 0}
				<tr style="">
					<td style="">{l s='Wrapping Cost'  mod='add_faktura'}</td>
					<td style="">
						
{if !$is_wat}
						- {displayPrice currency=$order->id_currency price=$order->total_wrapping_tax_excl}
						{else}
						- {displayPrice currency=$order->id_currency price=$order->total_wrapping_tax_incl}
						{/if}
					</td>
				</tr>
			{/if}

			{if ($order->total_paid_tax_incl - $order->total_paid_tax_excl) > 0}
				<tr style="">
					<td style="">{l s='Total Tax'  mod='add_faktura'}</td>
					<td style="">- {displayPrice currency=$order->id_currency price=($order->total_paid_tax_incl - $order->total_paid_tax_excl)}</td>
				</tr>
			{/if}

			<tr style="background-color: #EEE; font-size:{$font_width+10}pt; line-height:5px;">
				<td style="border-top:0px solid #000; border-left:0px solid #000; border-bottom:0px solid #000;">
					{l s='Total'  mod='add_faktura'}
				</td>
				<td style="border-top:0px solid #000; border-right:0px solid #000; border-bottom:0px solid #000;">
					- {displayPrice currency=$order->id_currency price=$order->total_paid_tax_incl}
				</td>
			</tr>
		</table>
	  </td>
	</tr>
</table>
<!-- /////////////// konec soupis cen   ///////////////  -->



		<div>
		
{if $is_wat}
			{$tax_tab}
		{/if}
		<!-- razítko -->
		{if $razitko_path}
		<table style="width: 100%; line-height:3px; text-align: right; font-weight:bold; font-size:{$font_width+7}pt;">
			<tr>
				<td style="width:90%; height:10px;">{l s='Signature and stamp:'  mod='add_faktura'}</td>
				<td style="width:10%;">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2" height="62px;">
					<img src="{$razitko_path}" style="width:120px; height:62px;" />
				</td>
			</tr>
		</table>
		{/if}
		</div>

{if isset($order_invoice->note) && $order_invoice->note}
<table style="width: 100%">
	<tr>
		<td style="width: 15%"></td>
		<td style="width: 85%">{$order_invoice->note|nl2br}</td>
	</tr>
</table>
{/if}

{if isset($HOOK_DISPLAY_PDF)}
<table style="width: 100%">
	<tr>
		<td style="width: 15%"></td>
		<td style="width: 85%">{$HOOK_DISPLAY_PDF}</td>
	</tr>
</table>
{/if}
