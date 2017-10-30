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
<table cellpadding="1" cellspacing="0" align="left" style="width: 100%; font-size:{$font_width+9}pt;">

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
			{l s='Delivery slip no.' mod='add_faktura'} &nbsp; {$delivery_prefix}{'%06d'|sprintf:$order->delivery_number}
		</td>
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
						<span style="color:#000">{dateFormat date=$order->delivery_date full=0}</span>
					</td>
					<td style="width:34%; height:15px; color:#06C; border-right:0px solid #000;">
					{*if $is_wat}
						{l s='Date taxable supply'  mod='add_faktura'}<br />
						<span style="color:#000">{dateFormat date=$invoice_date full=0}</span>
					{/if*}
					</td>
            		<td style="width:33%; height:10px; color:#C30;">
					{*if $date_due}
						{l s='Due date'  mod='add_faktura'}<br />
						<strong style="color:#000">{dateFormat date=$date_due full=0}</strong>
					{/if*}
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
				<tr style="line-height:4px; background-color: #999; color: #FFF; text-align: left; font-weight: bold;">
					<td style="width: 60%; line-height:6px;"> &nbsp; {l s='Product' mod='add_faktura'}</td>
					<td style="width: 20%;">{l s='Reference' mod='add_faktura'}</td>
					<td style="width: 20%; text-align:center;">{l s='Qty' mod='add_faktura'}</td>
				</tr>
		</table>
	   </td>
	</tr>
<!-- ///////////////  konec záhlaví tabulky produktů   ///////////////   -->


<!-- ///////////////   detaily tabulky produktů   ///////////////   -->
	<tr>
		<td colspan="2" style="border:0px solid #000;">
			<table cellpadding="2" cellspacing="1">
			{foreach $order_details as $product}
				{cycle values='#FFF,#DDD' assign=bgcolor}
				<tr style="line-height:4px;background-color:{$bgcolor};">
					<td style="text-align: left; width: 60%">{$product.product_name}</td>
					<td style="text-align: left; width: 20%">
						{if empty($product.product_reference)}
							---
						{else}
							{$product.product_reference}
						{/if}
					</td>
					<td style="text-align: center; width: 20%">{$product.product_quantity}</td>
				</tr>
			{/foreach}
			<!-- END PRODUCTS -->
			</table>
		</td>
	</tr>
<!-- ///////////////  konec detaily tabulky produktů   ///////////////   -->


<!-- ///////////////   razítka  a  podpisy   ///////////////   -->
	<tr>
	   <td colspan="2" style="border:0px solid #000;">
	   	<table cellpadding="2" cellspacing="1">
			<tr>
				<td>{if $razitko_path}<img src="{$razitko_path}" style="width:120px; height:62px;" />{/if}</td>
				<td colspan="2" style="height:40px;"></td>
			</tr>
			<tr>
				<td style="width:40%;">
					{l s='Created by:' mod='add_faktura'}
				</td>
				<td style="width:40%">
					{l s='Assumed by:' mod='add_faktura'}
				</td>
				<td style="width:20%">
					{l s='Date:' mod='add_faktura'}
				</td>
			</tr>
		</table>
	   </td>
	</tr>
<!-- ///////////////  konec  razítka  a  podpisy   ///////////////   -->
</table>
