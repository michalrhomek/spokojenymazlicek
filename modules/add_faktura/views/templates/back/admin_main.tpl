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
<h2>{$name} - <span style="color: #FF0000;">{l s='Ver.' mod='add_faktura'}{$version}</span></h2>

<div class="bootstrap">
{if $need_override}
	<div class="alert error alert-danger">
		<strong>{l s='Incomplete installation' mod='add_faktura'}</strong> - 
		{l s='Overrides from files' mod='add_faktura'}<u> AdminController.php </u>
		{l s='or' mod='add_faktura'}<u> AdminOrdersController.php </u>{l s='are not present in /overrides folder, please reinstall module!' mod='add_faktura'}
	</div>
{/if}
{if $override}
	<div class="alert error alert-danger">
		<strong>{$override}</strong>
	</div>
{/if}

{if $displayConf}
	<div class="conf confirm alert alert-success">{$displayConf}</div>
{/if}

{if $nbErrors}
	<div class="alert error alert-danger">
		<h3>{if $nbErrors > 1}{l s='There are' mod='add_faktura'}{else}{l s='There is' mod='add_faktura'}{/if} {$nbErrors} 
        {if $nbErrors > 1}{l s='errors' mod='add_faktura'}{else}{l s='error' mod='add_faktura'}{/if}</h3>
		<ol>
		{foreach $_postErrors AS $error}
			<li>{$error}</li>
		{/foreach}
		</ol>
	</div>
{/if}
</div>

<div id="add_faktura" class="mar_b2">
	<ul id="more_info_tabs" class="idTabs common_tabs">
		<li><a id="more_info_tab_more_info" href="#idTab1">{l s='Basic settings' mod='add_faktura'}</a></li>
		<li><a id="more_info_tab_more_info" href="#idTab2" {if $idTab == 2}class="selected"{/if}>{l s='Extension settings' mod='add_faktura'}</a></li>
		<li><a id="more_info_tab_more_info" href="#idTab3">{l s='License and Installation' mod='add_faktura'}</a></li>
	</ul>
	<div id="more_info_shadow">
		<div id="more_info_sheets" class="sheets align_justify">
			<div id="idTab1" class="rte product_accordion">
				<form method="post" action="">		
					<fieldset class="shop_inf">
						<legend>
							<img src="../img/admin/contact.gif" />
							{l s='Setting the contact details e-shop' mod='add_faktura'}
						</legend>

						<br><br>
						<label>{l s='Name:' mod='add_faktura'}</label>
						<div class="margin-form">
							<input style="width: 32.5em;" type="text" name="fa_name_shop" value="{Configuration::get('FA_NAME_SHOP')}"/> <sup>*</sup>
							<p class="clear">{l s='Your name or company name.' mod='add_faktura'}</p>
						</div>

						<label>{l s='Web Site:' mod='add_faktura'}</label>
						<div class="margin-form">
							<input style="width: 32.5em;" type="text" name="fa_web" value="{Configuration::get('FA_WEB')}"/>
							<p class="clear">{l s='Enter the URL of your business website.' mod='add_faktura'}</p>
						</div>

						<label>{l s='Address:' mod='add_faktura'}</label>
						<div class="margin-form">
							<input style="width: 32.5em;" type="text" name="fa_address" value="{Configuration::get('FA_ADDRESS')}"/> <sup>*</sup>
							<p class="clear">{l s='Enter the street and house number.' mod='add_faktura'}</p>
						</div>

						<label>{l s='Zip/Postal Code:' mod='add_faktura'}</label>
						<div class="margin-form">
							<input style="width: 50px;" type="text" name="fa_zipcode" value="{Configuration::get('FA_ZIPCODE')}"/> <sup>*</sup>
							<span style="font-size: 1.1em; margin-left:30px; color: #000000; font-weight:bold;">{l s='City:' mod='add_faktura'}</span>
							<input style="width: 165px;" type="text" name="fa_city" value="{Configuration::get('FA_CITY')}"/> <sup>*</sup>
							<p class="clear">{l s='Enter zip code and city.' mod='add_faktura'}</p>
						</div>

						<label>{l s='Country:' mod='add_faktura'}</label>
						<div class="margin-form">
							<input style="width: 165px;" type="text" name="fa_country" value="{Configuration::get('FA_COUNTRY')}"/> <sup>*</sup>
							<p class="clear">{l s='Enter the name of the country.
' mod='add_faktura'}</p>
						</div>	
					<center>
						<input type="submit" name="submitFaktura" value="{l s='Save Settings' mod='add_faktura'}" class="button button_mk" />
					</center>
					</fieldset>
				</form>

				<form method="post" action="">		
					<fieldset class="upd_translat">
						<legend>
							<img src="../img/admin/contact.gif" />
							{l s='Edit translations of texts embedded in PDF' mod='add_faktura'}
						</legend>

						<br><br>

						<label>{l s='Choose a language:' mod='add_faktura'}</label>
						<div class="translat-form">
							<select name="fa_trnsl_lang" style="width: 150px;">
							{foreach $languages as $language}
								<option value="{$language.iso_code}">{$language.name}</option>
							{/foreach}
							</select>
						</div>
						<br><br>
					<center>
						<input type="submit" name="submitTranslate" value="{l s='Translate PDF inputs' mod='add_faktura'}" class="button button_mk" />
					</center>
					</fieldset>
				</form>

				<form action="" method="post" enctype="multipart/form-data">
					<fieldset class="load_stamp">
						<legend><img src="../img/admin/appearance.gif" />{l s='Stamp' mod='add_faktura'}</legend>
						{if $stamp_path}
							<center style="padding:20px; margin-bottom:30px;">
								<img src="{$stamp_path}" alt="{l s='Image:' mod='add_faktura'}" title="{l s='Image:' mod='add_faktura'}" />
								<input type="submit" class="button" value="{l s='Delete' mod='add_faktura'}" name="deleteRazitko">
							</center>
						{/if}

						<label style="width: 300px; margin-right: 30px;">{l s='File a graphical representation of a stamp:' mod='add_faktura'}</label>
						<input class="button" type="file" name="presentation">
						<input type="submit" class="button" value="{l s='Load file' mod='add_faktura'}" name="saveRazitko">
					</fieldset>
				</form>
			</div>

			<div id="idTab2" class="rte product_accordion">
				<form method="post" action="" id="setings_form2">
                	<input type="hidden" value="2" name="idTab">
					<fieldset>
						<legend>
							<img src="../img/admin/unknown.gif" alt="" title="" />
							{l s='Settings for orders status' mod='add_faktura'}
						</legend>
						<div class="advanced_left">
							<table cellpadding="8" cellspacing="0" class="table" style="width:600px;">
								<tr class="positive">
									<td colspan="2">
										<h3>{l s='Basic settings' mod='add_faktura'}</h3>
									</td>
								</tr>
								<tr class="negative">
									<td>
										<br />{l s='We are VAT payers:' mod='add_faktura'}<br /><br />
									</td>
									<td class="center" style="width: 220px;">
										<input type="checkbox" name="is_wat" value="1" {if Configuration::get('FA_IS_WAT')}checked="checked"{/if}/>
									</td>
								</tr>
								<tr class="negative">
									<td>
										<br />{l s='Version for Slovakia:' mod='add_faktura'}<br /><br />
									</td>
									<td class="center">
										<input type="checkbox" name="sk" value="1" {if Configuration::get('FA_SK')}checked="checked"{/if}/>
									</td>
								</tr>
								<tr class="negative">
									<td>
										<br />{l s='Maturity (global):' mod='add_faktura'}<br />
                                        <span style=" font-size:0.8em; font-style:italic;">{l s='For each order separately can change the details of your order' mod='add_faktura'}</span><br />
									</td>
									<td class="center">
										<input style="width: 20px;" type="text" name="due_date_dates" value="{Configuration::get('FA_DUE_DATE_DATES')}"/>
										<span style="margin-left:15px;">{l s='days' mod='add_faktura'}</span>
									</td>
								</tr>
								<tr class="negative">
									<td>
										<br />{l s='The size of the logo' mod='add_faktura'}<br /><br />
									</td>
									<td class="center">
										{l s='width: ' mod='add_faktura'}
										<input style="width: 20px;" type="text" name="fa_width" value="{Configuration::get('FA_WIDTH')}"/>
										<span style=" margin-right:30px;">{l s=' px' mod='add_faktura'}</span>
										{l s='height: ' mod='add_faktura'}
										<input style="width: 20px;" type="text" name="fa_height" value="{Configuration::get('FA_HEIGHT')}"/>
										{l s=' px' mod='add_faktura'}
									</td>
								</tr>
								<tr class="negative">
									<td>
										<br />{l s='Opening PDF files (do not show the option to save):' mod='add_faktura'}<br /><br />
									</td>
									<td class="center">
										<input type="checkbox" name="pdf" value="1" {if Configuration::get('FA_PDF')}checked="checked"{/if}/>
									</td>
								</tr>
								<tr class="negative">
									<td>
										<br />{l s='Round off the total price to an integer:' mod='add_faktura'}<br /><br />
									</td>
									<td class="center">
										<input type="checkbox" name="round_" value="1" {if Configuration::get('FA_ROUND_')}checked="checked"{/if}/>
									</td>
								</tr>
								<tr class="negative">
									<td>
										<br />{l s='Number of decimal amounts on the invoice:' mod='add_faktura'}<br /><br />
									</td>
									<td class="center">
										<select name="fa_decimals" style="width: 50px;">
											<option value="0" {if Configuration::get('FA_DECIMALS') == 0}selected="selected"{/if}>0</option>
											<option value="1" {if Configuration::get('FA_DECIMALS') == 1}selected="selected"{/if}>1</option>
											<option value="2" {if Configuration::get('FA_DECIMALS') == 2}selected="selected"{/if}>2</option>
										<select>
									</td>
								</tr>
								<tr class="negative">
									<td>
										<!--<br />{l s='Im VAT, so that the correct calculations performed PrestaShop, I want to overwrite a file in a folder overrides Cart.php' mod='add_faktura'}<br /><br />-->
									</td>
									<td class="center">
										<!--<input type="checkbox" name="fa_platce" value="1" {if Configuration::get('FA_PLATCE') && $exst_cart_ovrrds}checked="checked"{/if}/>-->
									</td>
								</tr>
								<tr class="negative">
									<td>
										<br />{l s='Show only invoice address (if completed):' mod='add_faktura'}<br />
                                        <span style=" font-size:0.8em; font-style:italic;">{l s='Delivery address will not be displayed on the invoice' mod='add_faktura'}</span><br />
									</td>
									<td class="center">
										<input type="checkbox" value="1" name="fa_only_inv_adr" {if Configuration::get('FA_ONLY_INV_ADR')}checked="checked"{/if}/>
									</td>
								</tr>
							</table>
						
						<br>
						
							<table cellpadding="8" cellspacing="0" class="table" style="width:600px;">
								<tr class="positive">
									<td colspan="2">
										<h3>{l s='Notes invoices' mod='add_faktura'}</h3>
									</td>
								</tr>
								<tr class="negative">
									<td>
										<br />{l s='View administrator note to the order' mod='add_faktura'}<br /><br />
									</td>
									<td class="center">
										<input type="checkbox" name="fa_admin_note" value="1" {if Configuration::get('FA_ADMIN_NOTE')}checked="checked"{/if}/>
									</td>
								</tr>
								<tr class="negative">
									<td>
										<br />{l s='Main note to the order' mod='add_faktura'}<br />
                                        <span style=" font-size:0.8em; font-style:italic;">{l s='The note is displayed at the end of the invoice' mod='add_faktura'}</span><br />
									</td>
									<td class="center">
                                    	<textarea name="fa_main_note" style="width:250px;">{Configuration::get('FA_MAIN_NOTE')}</textarea>
									</td>
								</tr>
								<tr class="negative">
									<td>
										<br />{l s='View customer note to the order' mod='add_faktura'}<br /><br />
									</td>
									<td class="center">
										<input type="checkbox" name="fa_user_note" value="1" {if Configuration::get('FA_USER_NOTE')}checked="checked"{/if}/>
									</td>
								</tr>
							</table>
						</div>

						<div class="advanced_right">
							<table cellpadding="0" cellspacing="0" class="table">
								<tr class="positive">
									<td colspan="2">
										<h3>{l s='Font settings' mod='add_faktura'}</h3>
									</td>
								</tr>
								<tr class="negative">
									<td>
										<br />{l s='Font in the invoice:' mod='add_faktura'}<br /><br />
									</td>
									<td class="center">
										<select name="pdf_font">
										{foreach $pdf_font as $font}
											<option value="{$font}" {if Configuration::get('FA_PDF_FONT') == $font} selected="selected"{/if}>{$font}</option>
										{/foreach}
										</select>
									</td>
								</tr>
								<tr class="negative">
									<td>
										<br />{l s='Font size in the invoice' mod='add_faktura'}<br /><br />
									</td>
									<td class="center">
										<select name="pdf_font_width">
										{foreach $pdf_font_width as $width}
											<option value="{$width}" {if Configuration::get('FA_PDF_FONT_WIDTH') == $width} selected="selected"{/if}>{$width}</option>
										{/foreach}
										</select>
									</td>
								</tr>
							</table>

							<br />

							<table cellpadding="0" cellspacing="0" class="table">
								<tr class="positive">
									<td colspan="2">
										<h3>{l s='Reseller settings' mod='add_faktura'}</h3>
									</td>
								</tr>
								<tr class="negative">
									<td>
										<br />{l s='Phone:' mod='add_faktura'}<br /><br />
									</td>
									<td class="center">
										<input type="text" name="fa_tel" value="{Configuration::get('FA_TEL')}"/>
									</td>
								</tr>
								<tr class="negative">
									<td>
										<br />{l s='DNI:' mod='add_faktura'}<br /><br />
									</td>
									<td class="center">
										<input type="text" name="fa_ico" value="{Configuration::get('FA_ICO')}"/>
									</td>
								</tr>
								<tr class="negative">
									<td>
										<br />
										{l s='VAT number:' mod='add_faktura'}
										<br /><br />
									</td>
									<td class="center">
										<input type="text" name="fa_dic" value="{Configuration::get('FA_DIC')}"/>
									</td>
								</tr>
								{if Configuration::get('FA_SK')}
									<tr class="negative">
										<td>
											<br />
											{l s='IČ DPH:' mod='add_faktura'}
										<br /><br />
										</td>
										<td class="center">
											<input type="text" name="fa_icdph" value="{Configuration::get('FA_ICDPH')}"/>
										</td>
									</tr>
								{/if}
								<tr class="negative">
									<td>
										<br />{l s='Email:' mod='add_faktura'}<br /><br />
									</td>
									<td class="center">
										<input type="text" name="fa_email" value="{Configuration::get('FA_EMAIL')}"/>
									</td>
								</tr>
								<tr class="negative">
									<td>
										<br />{l s='The entry in the register:' mod='add_faktura'}<br /><br />
									</td>
									<td class="center">
										<input type="text" name="fa_zapis" value="{Configuration::get('FA_ZAPIS')}"/>
									</td>
								</tr>
							</table>

							<br />

							<table cellpadding="8" cellspacing="0" class="table">
								<tr class="positive">
									<td colspan="2">
										<h3>{l s='The Bank accounts by currency:' mod='add_faktura'}</h3>
										<input type="checkbox" id="fa_currency_on" name="fa_currency_on" value="1" {if Configuration::get('FA_CURRENCY_ON')}checked="checked"{/if}/>
				<script type="text/javascript">
    				$('#fa_currency_on').click(function() {
        				if($(this).attr('checked'))
							$('#fa_currency_on_show').show(1000);
						else
							$('#fa_currency_on_show').hide(500);
    				})
				</script>
									</td>
								</tr>
								<tr class="negative">
									<td style="line-height:24px;">
                                    	<div id="fa_currency_on_show" style="float:right; margin:8px 0; display:{if Configuration::get('FA_CURRENCY_ON')}block{else}none{/if}">
											<select name="fa_bank_currency" onChange="javascript:$('#setings_form2').submit();">
											{foreach from=$currencies item='currency'}
												<option value="{$currency.id_currency|intval}" {if $currency.id_currency == $this_curr}selected="selected"{/if}>{$currency.iso_code}</option>
											{/foreach}
											</select>
										</div>
										<p>
											{l s='Bank account number:' mod='add_faktura'}<br />
											{l s='Name of bank:' mod='add_faktura'}<br />
											{l s='SWIFT:' mod='add_faktura'}<br />
											{l s='IBAN:' mod='add_faktura'}<br />
										</p>
									</td>
									<td>
										<p class="bank">
		<input style="width: 150px;" type="text" name="fa_bank_number_{$this_curr}" value="{Configuration::get("FA_BANK_NUMBER_{$this_curr}")}"/>
										</p>
										<p class="bank">
		<input style="width: 200px;" type="text" name="fa_bank_name_{$this_curr}" value="{Configuration::get("FA_BANK_NAME_{$this_curr}")}"/>
										</p>
										<p class="bank">
		<input style="width: 80px;" type="text" name="fa_swift_{$this_curr}" value="{Configuration::get("FA_SWIFT_{$this_curr}")}"/>
										</p>
										<p class="bank">
		<input style="width: 200px;" type="text" name="fa_iban_{$this_curr}" value="{Configuration::get("FA_IBAN_{$this_curr}")}"/>
										</p>
									</td>
								</tr>
								<tr class="negative">
									<td>
										<br />{l s='Constant symbol:' mod='add_faktura'}<br /><br />
									</td>
									<td class="center">
										<input style="width: 80px;" type="text" name="fa_k_symbol" value="{Configuration::get('FA_K_SYMBOL')}"/>
									</td>
								</tr>
								<tr class="negative">
									<td>
										<br />{l s='Prefix for VS / Variable symbol as' mod='add_faktura'}<br /><br />
									</td>
									<td class="center">
										<input style="width: 40px;" type="text" name="fa_prefix_vs" value="{Configuration::get('FA_PREFIX_VS')}"/>
										<select name="fa_ord_inv">
											<option value="order"{if Configuration::get('FA_ORD_INV') == 'order'} selected="selected"{/if}>
												{l s='Order number' mod='add_faktura'}
											</option>
											<option value="invoice"{if Configuration::get('FA_ORD_INV') == 'invoice'} selected="selected"{/if}>
												{l s='Invoice number' mod='add_faktura'}
											</option>
											<option value="reference"{if Configuration::get('FA_ORD_INV') == 'reference'} selected="selected"{/if}>
												{l s='Reference' mod='add_faktura'}
											</option>
										</select>
									</td>
								</tr>
							</table>

							<br />

							<table cellpadding="8" cellspacing="0" class="table">
								<tr class="positive">
									<td colspan="2">
										<h3>{l s='View customer information' mod='add_faktura'}</h3>
									</td>
								</tr>
								<tr class="negative">
									<td>
										<br />
										<label for="view_telefon" style="width:150px;">{l s='Show phone' mod='add_faktura'}</label>
										<input type="checkbox" id="view_telefon" name="view_telefon" value="1" {if Configuration::get('FA_VIEW_TELEFON')}checked="checked"{/if}/>
										<br /><br />
									</td>
									<td>
										<label for="view_email" style="width:150px">{l s='Show email' mod='add_faktura'}</label>
										<input type="checkbox" id="view_email" name="view_email" value="1" {if Configuration::get('FA_VIEW_EMAIL')}checked="checked"{/if}/>
									</td>
									</td>
								</tr>
							</table>
						</div>
						<div class="div_cl">
							<input type="hidden" name="submitSettings" value="1">
							<input type="submit" class="button button_mk" value="{l s='Save settings' mod='add_faktura'}" />
						</div>
					</fieldset>
				</form>
			</div>

			<div id="idTab3" class="rte product_accordion">
				<fieldset id="dopravci">
					<legend>
						<img src="../modules/{$module_name}/img/licence.jpg" width="30px" height="30px"/>
						{l s='Licence Module' mod='add_faktura'}
					</legend>
					<div class="div_4">
                        <p>
							{l s='1. Purchase of one licence for a Supplier’s product authorizes the Purchaser to use the module at one website / e-shop in the production regime. The Purchaser is obliged to inform the Supplier of the domain and website for which the licence will be used.' mod='add_faktura'}

						</p>
 <p>
							{l s='2. In the event of purchase of 2-5 licences for one product, the Purchaser is granted a 10 Percent (%) discount off the total price for this product. The Purchaser may use the purchased licences in the corresponding number of websites / e-shops in the production regime; the number of websites / e-shops must not exceed the number of purchased licences. The Purchaser is obliged to inform the Supplier of the domains and websites for which the licences will be used. ' mod='add_faktura'}
						</p>
                        
                         <p>
							{l s='3. In the event of purchase of 6-10 licences for one product, the Purchaser is granted a 20 Percent (%) discount off the total price for this product. The Purchaser may use the purchased licences in the corresponding number of websites / e-shops in the production regime; the number of websites / e-shops must not exceed the number of purchased licences. The Purchaser is obliged to inform the Supplier of the domains and websites for which the licences will be used. ' mod='add_faktura'}
						</p>
                        <p>
							{l s='4. In the event of purchase of a Multilicence, the price of such licence equals to the tenfold price of one licence for the purchased product. The purchase of a Multilicence authorizes the Purchaser to use an unlimited number of copies of the purchased product in all its websites / e-shops in the production regime. ' mod='add_faktura'}
						</p>
                        <p>
							{l s='5. An unlimited number of copies of all the products purchased under items 1 through 4 may be used by the Purchaser in its testing websites / e-shops; the Purchaser may modify all the products and adjust the functions of the products to satisfy its needs.' mod='add_faktura'}
						</p>
                        <p>
							{l s='6. The prices of licences for modules supplied by the Supplier do not include: ' mod='add_faktura'}
							<li>{l s='Any additional individual adjustments;' mod='add_faktura'}</li>
							<li>{l s='Any additional services (implementation, testing, etc.);' mod='add_faktura'}</li>
							<li>{l s='Any updates to the modules if the reason for their non-functioning is a change in the version of the platform for which the modules have been purchased. ' mod='add_faktura'}</li>
						</p>
                        <p>
							{l s='7. The Purchaser acknowledges that it is not authorized to:' mod='add_faktura'}
							<li>{l s='Procure, offer and sell the purchased products in public or individually to any third parties;' mod='add_faktura'}</li>
							<li>{l s='Copy the purchased products.' mod='add_faktura'}</li>
						</p>
                        <p>
							{l s='8. The Supplier is not responsible for the functionality, compatibility and use of modules offered free of charge. Any damage incurred or any possible loss and damage to data will be borne exclusively by the Purchaser. The Supplier does not provide any free-of-charge counselling or individual modifications for modules offered free of charge. The Purchaser may:' mod='add_faktura'}
							<li>{l s='Use the free-of-charge modules in any way at any place whatsoever.' mod='add_faktura'}</li>
							<li>{l s='Modify the functionality and appearance of the free-of-charge modules.' mod='add_faktura'}</li>
                            <li>{l s='Offer the free-of-charge modules to third parties.' mod='add_faktura'}</li>
						</p>
                        <p>
							{l s='9. The Purchaser is not authorized to sell to any third parties any services, modules or websites subject to payment to the Supplier. Upon payment for the services, products and websites, the ownership rights pass from the Supplier to the Purchaser. The Supplier reserves a copyright for the performed graphic and programming work and the individual designs, modules and adjustments.' mod='add_faktura'}
						</p>
                        
                        <p>
							{l s='10. The Purchaser acknowledges that the offered products subject to payment are protected by the Copyright Act and secured against copying and disseminating; the Purchaser undertakes not to copy or abuse the products, provide them to any third parties, disseminate them or take any steps aiming at the interference with the protection of the products provided. The Purchaser is responsible for any damage incurred by such conduct.' mod='add_faktura'}
						</p>
                        
                        <p>
							{l s='11. The Purchaser undertakes not to endeavour to break the copyright protection and breach other regulations governing the protection of intellectual property.' mod='add_faktura'}
						</p>
                        
                        <p>
							{l s='12. Software downloads are permitted only from presta-modul.shopmk.cz or from the file sent by the Supplier to the Purchaser’s e-mail address. Using this software means that the Purchaser agrees with the above-mentioned copyright. ' mod='add_faktura'}
						</p>
                        
                        <p>
							{l s='13. The Purchaser represents that the Supplier has no legal liability for any damage incurred by the Client or any third persons in relation to the use of the module and that any damage incurred or possible loss and damage to data caused by incorrect setting and use of the product or possible modifications to the product by the Purchaser will be borne exclusively by the Purchaser.' mod='add_faktura'}
						</p>
                        
                      
						</div>
						<center>
\|/<span class="licence">~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~</span>\|/<br>
 | <span class="licence"></span> | <br>
 | <span class="licence">{l s='RESALE AND DISTRIBUTION ARE PROHIBITED!' mod='add_faktura'}</span> | <br>
 | <span class="licence"></span> | <br>
 | <span class="licence">{l s='Software downloads are permitted only from' mod='add_faktura'} <a href="http://presta-modul.shopmk.cz">presta-modul.shopmk.cz</a></span> | <br>
 | <span class="licence">{l s='or from the file sent by the Supplier to the Purchaser’s e-mail address.' mod='add_faktura'}</span> | <br>
 | <span class="licence">{l s='Using this software means that the Purchaser agrees with the above-mentioned copyright.' mod='add_faktura'}</span> | <br>
 | <span class="licence"></span> | <br>
/|\<span class="licence">{l s='The Purchaser represents that the Supplier has no legal liability for any damage' mod='add_faktura'}</span>/|\<br>
\|/<span class="licence">{l s='incurred by the Client or any third persons in relation to the use of the module' mod='add_faktura'}</span>\|/<br>
 | <span class="licence">{l s='and that any damage incurred or possible loss and damage to data caused by incorrect setting' mod='add_faktura'}</span> | <br>
 | <span class="licence">{l s='and use of the product or possible modifications to the product by the Purchaser' mod='add_faktura'}</span> | <br>
 
  | <span class="licence">{l s='will be borne exclusively by the Purchaser.' mod='add_faktura'}</span> | <br>
 | <span class="licence">***</span> | <br>
 | <span class="licence"></span> | <br>
/|\<span class="licence">~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~</span>/|\<br>
						</center>
					</fieldset>
			</div>
	</div>
</div>