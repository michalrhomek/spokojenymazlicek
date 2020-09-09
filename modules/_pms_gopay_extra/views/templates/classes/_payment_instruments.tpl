
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
 * ########################################################################### **}{if isset($_PAYMENTS->enabledPaymentInstruments) && is_array($_PAYMENTS->enabledPaymentInstruments)}
{assign var='number' value=7}
{if !$_CUSTOM_NAMES}{assign var='number' value=$number+1}{/if}
{if $_PRICE_DIFFERENT && $_PRICE_VIEW}{assign var='number' value=$number-1}{/if}
{if $_CUSTOM_IMAGES}{assign var='number' value=$number-1}{/if}

<div class="panel">
	<div class="panel-heading black">{l s='Payment instruments' mod='pms_gopay_extra'}</div>
	{foreach $_PAYMENTS->groups as $key => $group}
		{foreach $_PAYMENTS->enabledPaymentInstruments as $payment}
			{if $key == $payment->group}
				{assign var='payment_code' value=$payment->paymentInstrument}

				<div class="form-group">
					<div class="col-lg-2 checkbox">
						<label for="_HIDE_PAYMENT_{$payment_code}">
							<input type="checkbox"
									name="_HIDE_PAYMENT_{$payment_code}"
									id="_HIDE_PAYMENT_{$payment_code}"
									class=""
									value="1"
									{if $_HIDE_PAYMENT[$payment_code]}checked{/if}
							 />
							{$payment_code}
						</label>
					</div>
					<div class="col-lg-2">
						<div id="_IMAGE_{$payment_code}"><img src="{$_PAYMENT_IMAGE[$payment_code]}" style="max-width:100px"></div>
					</div>
					{if $_CUSTOM_IMAGES}
						<div class="col-lg-1">
							<input type="file"
									id="_IMAGE_FILE_{$payment_code}"
									style="display:none"
									multiple
									accept="image/gif"
									onchange="handleFiles(this.files, '{$payment_code}')"
							>
							<a href="#" onclick='document.getElementById("_IMAGE_FILE_{$payment_code}").click();'>
								{l s='Upload logo' mod='pms_gopay_extra'}
							</a><br>
							<a href="#" class="delete" data-id="{$payment_code}" data-confirm="{l s='Are you sure to delete this item?' mod='pms_gopay_extra'}">
								{l s='Delete' mod='pms_gopay_extra'}
							</a>
						</div>
					{/if}
					<div class="col-lg-{$number}">
						{if $_CUSTOM_NAMES}
							{foreach $languages as $language}
								{assign var='value_text' value=$_PAYMENT_NAME[$payment_code][$language.id_lang]}
								{if $languages|count > 1}
									<div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
								{/if}
								<div class="col-lg-11 mk-textbox">
									{l s='Title' mod='pms_gopay_extra'}
									<input type="text"
												id="{$payment_code}_{$language.id_lang}"
												name="_PAYMENT_NAME_{$payment_code}_{$language.id_lang}"
												class=""
												value="{if $value_text}{$value_text|escape:'html':'UTF-8'}{else}{$payment->label->$gopayDefaultLang}{/if}"
									 />
									<br />
									{l s='Description' mod='pms_gopay_extra'}
									<textarea  class="rte autoload_rte"
												id="{$payment_code}_DESC_{$language.id_lang}"
												name="_PAYMENT_DESC_{$payment_code}_{$language.id_lang}"
									 >{$_PAYMENT_DESC[$payment_code][$language.id_lang]}</textarea>
								</div>
								{if $languages|count > 1}
										<div class="col-lg-1">
											<button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
												{$language.iso_code}
												<i class="icon-caret-down"></i>
											</button>
											<ul class="dropdown-menu">
												{foreach from=$languages item=language}
													<li><a href="javascript:hideOtherLanguage({$language.id_lang});" tabindex="-1">{$language.name}</a></li>
												{/foreach}
											</ul>
										</div>
									</div>
								{/if}
							{/foreach}
						{else}
							{$payment->label->$gopayDefaultLang}
						{/if}
					</div>
					{if $_PRICE_DIFFERENT && $_PRICE_VIEW}
						<div class="col-lg-2">
							<div class="input-group PRICE-type-VALUES">
								<input type="text"
										name="_FEE_VALUE_{$payment_code}"
										id="_FEE_VALUE_{$payment_code}"
										value="{$_FEE_VALUE[$payment_code]}"
										class="col-lg-1"
										maxlength="6"
								>
								<span class="input-group-milos">
								<input type="hidden" class="_FEE_TYPE" name="_FEE_TYPE_{$payment_code}" value="{$_FEE_TYPE[$payment_code]}">
								<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
									<span class="_PRICE_ICON">{if $_FEE_TYPE[$payment_code]}%{else}{$default_currency->sign}{/if}</span>
									<i class="icon-caret-down"></i>
								</button>
								<ul class="dropdown-menu">
									<li class="PRICE-option {if $_FEE_TYPE[$payment_code]}active{/if}">
										<a href="#" data-value="1" data-icon="%">
											%
										</a>
									</li>
									<li class="divider"></li>
									<li class="PRICE-option {if !$_FEE_TYPE[$payment_code]}active{/if}">
										<a href="#" data-value="0" data-icon="{$default_currency->sign}">
											{$default_currency->sign}
										</a>
									</li>
								</ul>
								</span>
							</div>
							<p class="help-block">
								{l s='Without VAT' mod='pms_gopay_extra'}
							</p>
						</div>
					{/if}
				</div>
			{/if}
		{/foreach}
	{/foreach}
</div>


	{foreach $_PAYMENTS->groups as $key => $group}
		{foreach $_PAYMENTS->enabledPaymentInstruments as $payment}
			{if $key == $payment->group}
				{if count($payment->enabledSwifts) > 0}
					<div class="panel">
						<div class="panel-heading silver">{l s='Online payment buttons' mod='pms_gopay_extra'}</div>
						{foreach $payment->enabledSwifts as $swift}
							{if $swift->isOnline}
								{assign var='swift_code' value=$swift->swift}
								<div class="form-group">
									<div class="col-lg-2 checkbox">
										<label for="_HIDE_PAYMENT_{$swift_code}">
											<input type="checkbox"
													name="_HIDE_PAYMENT_{$swift_code}"
													id="_HIDE_PAYMENT_{$swift_code}"
													class=""
													value="1"
													{if $_HIDE_PAYMENT[$swift_code]}checked{/if}
											 />
											{$swift_code}
										</label>
									</div>
									<div class="col-lg-2">
										<div id="_IMAGE_{$swift_code}"><img src="{$_PAYMENT_IMAGE[$swift_code]}" style="max-width:100px"></div>
									</div>
									{if $_CUSTOM_IMAGES}
										<div class="col-lg-1">
											<input type="file"
													id="_IMAGE_FILE_{$swift_code}"
													style="display:none"
													multiple
													accept="image/gif"
													onchange="handleFiles(this.files, '{$swift_code}')"
											>
											<a href="#" onclick='document.getElementById("_IMAGE_FILE_{$swift_code}").click();'>
												{l s='Upload logo' mod='pms_gopay_extra'}
											</a><br>
											<a href="#" class="delete" data-id="{$swift_code}" data-confirm="{l s='Are you sure to delete this item?' mod='pms_gopay_extra'}">
												{l s='Delete' mod='pms_gopay_extra'}
											</a>
										</div>
									{/if}
									<div class="col-lg-{$number}">
										{if $_CUSTOM_NAMES}
											{foreach $languages as $language}
												{assign var='value_text' value=$_PAYMENT_NAME[$swift_code][$language.id_lang]}
												{if $languages|count > 1}
													<div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
												{/if}
												<div class="col-lg-11 mk-textbox">
													{l s='Title' mod='pms_gopay_extra'}
													<input type="text"
																id="{$swift_code}_{$language.id_lang}"
																name="_PAYMENT_NAME_{$swift_code}_{$language.id_lang}"
																class=""
																value="{if $value_text}{$value_text|escape:'html':'UTF-8'}{else}{$swift->label->$gopayDefaultLang}{/if}"
													 />
													<br />
													{l s='Description' mod='pms_gopay_extra'}
													<textarea class="rte autoload_rte"
																id="{$swift_code}_DESC_{$language.id_lang}"
																name="_PAYMENT_DESC_{$swift_code}_{$language.id_lang}"
										 			>{$_PAYMENT_DESC[$swift_code][$language.id_lang]}</textarea>
												</div>
												{if $languages|count > 1}
														<div class="col-lg-1">
															<button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
																{$language.iso_code}
																<i class="icon-caret-down"></i>
															</button>
															<ul class="dropdown-menu">
																{foreach from=$languages item=language}
																	<li><a href="javascript:hideOtherLanguage({$language.id_lang});" tabindex="-1">{$language.name}</a></li>
																{/foreach}
															</ul>
														</div>
													</div>
												{/if}
											{/foreach}
										{else}
											{$swift->label->$gopayDefaultLang}
										{/if}
									</div>
									{if $_PRICE_DIFFERENT && $_PRICE_VIEW}
										<div class="col-lg-2">
											<div class="input-group PRICE-type-VALUES">
												<input type="text"
														name="_FEE_VALUE_{$swift_code}"
														id="_FEE_VALUE_{$swift_code}"
														value="{$_FEE_VALUE[$swift_code]}"
														class="col-lg-1"
														maxlength="6"
												>
												<span class="input-group-milos">
												<input type="hidden" class="_FEE_TYPE" name="_FEE_TYPE_{$swift_code}" value="{$_FEE_TYPE[$swift_code]}">
												<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
													<span class="_PRICE_ICON">{if $_FEE_TYPE[$swift_code]}%{else}{$default_currency->sign}{/if}</span>
													<i class="icon-caret-down"></i>
												</button>
												<ul class="dropdown-menu">
													<li class="PRICE-option {if $_FEE_TYPE[$swift_code]}active{/if}">
														<a href="#" data-value="1" data-icon="%">
															%
														</a>
													</li>
													<li class="divider"></li>
													<li class="PRICE-option {if !$_FEE_TYPE[$swift_code]}active{/if}">
														<a href="#" data-value="0" data-icon="{$default_currency->sign}">
															{$default_currency->sign}
														</a>
													</li>
												</ul>
												</span>
											</div>
											<p class="help-block">
												{l s='Without VAT' mod='pms_gopay_extra'}
											</p>
										</div>
									{/if}
								</div>
							{/if}
						{/foreach}
					</div>
					<div class="panel">
						<div class="panel-heading brown">{l s='Offline payment buttons' mod='pms_gopay_extra'}</div>
						{foreach $payment->enabledSwifts as $swift_code => $swift}
							{if !$swift->isOnline}
								{assign var='swift_code' value=$swift->swift}
								<div class="form-group _EXTENDED_MODE">
									<div class="col-lg-2 checkbox">
										<label for="_HIDE_PAYMENT_{$swift_code}">
											<input type="checkbox"
													name="_HIDE_PAYMENT_{$swift_code}"
													id="_HIDE_PAYMENT_{$swift_code}"
													class=""
													value="1"
													{if $_HIDE_PAYMENT[$swift_code]}checked{/if}
											 />
											{$swift_code}
										</label>
									</div>
									<div class="col-lg-2">
										<div id="_IMAGE_{$swift_code}"><img src="{$_PAYMENT_IMAGE[$swift_code]}" style="max-width:100px"></div>
									</div>
									{if $_CUSTOM_IMAGES}
										<div class="col-lg-1">
											<input type="file"
													id="_IMAGE_FILE_{$swift_code}"
													style="display:none"
													multiple
													accept="image/gif"
													onchange="handleFiles(this.files, '{$swift_code}')"
											>
											<a href="#" onclick='document.getElementById("_IMAGE_FILE_{$swift_code}").click();'>
												{l s='Upload logo' mod='pms_gopay_extra'}
											</a><br>
											<a href="#" class="delete" data-id="{$swift_code}" data-confirm="{l s='Are you sure to delete this item?' mod='pms_gopay_extra'}">
												{l s='Delete' mod='pms_gopay_extra'}
											</a>
										</div>
									{/if}
									<div class="col-lg-{$number}">
										{if $_CUSTOM_NAMES}
											{foreach $languages as $language}
												{assign var='value_text' value=$_PAYMENT_NAME[$swift_code][$language.id_lang]}
												{if $languages|count > 1}
													<div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
												{/if}
												<div class="col-lg-11 mk-textbox">
													{l s='Title' mod='pms_gopay_extra'}
													<input type="text"
																id="{$swift_code}_{$language.id_lang}"
																name="_PAYMENT_NAME_{$swift_code}_{$language.id_lang}"
																class=""
																value="{if $value_text}{$value_text|escape:'html':'UTF-8'}{else}{$swift->label->$gopayDefaultLang}{/if}"
													 />
													<br />
													{l s='Description' mod='pms_gopay_extra'}
													<textarea class="rte autoload_rte"
																id="{$swift_code}_DESC_{$language.id_lang}"
																name="_PAYMENT_DESC_{$swift_code}_{$language.id_lang}"
										 			>{$_PAYMENT_DESC[$swift_code][$language.id_lang]}</textarea>
												</div>
												{if $languages|count > 1}
														<div class="col-lg-1">
															<button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
																{$language.iso_code}
																<i class="icon-caret-down"></i>
															</button>
															<ul class="dropdown-menu">
																{foreach from=$languages item=language}
																	<li><a href="javascript:hideOtherLanguage({$language.id_lang});" tabindex="-1">{$language.name}</a></li>
																{/foreach}
															</ul>
														</div>
													</div>
												{/if}
											{/foreach}
										{else}
											{$swift->label->$gopayDefaultLang}
										{/if}
									</div>
									{if $_PRICE_DIFFERENT && $_PRICE_VIEW}
										<div class="col-lg-2">
											<div class="input-group PRICE-type-VALUES">
												<input type="text"
														name="_FEE_VALUE_{$swift_code}"
														id="_FEE_VALUE_{$swift_code}"
														value="{$_FEE_VALUE[$swift_code]}"
														class="col-lg-1"
														maxlength="6"
												>
												<span class="input-group-milos">
												<input type="hidden" class="_FEE_TYPE" name="_FEE_TYPE_{$swift_code}" value="{$_FEE_TYPE[$swift_code]}">
												<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
													<span class="_PRICE_ICON">{if $_FEE_TYPE[$swift_code]}%{else}{$default_currency->sign}{/if}</span>
													<i class="icon-caret-down"></i>
												</button>
												<ul class="dropdown-menu">
													<li class="PRICE-option {if $_FEE_TYPE[$swift_code]}active{/if}">
														<a href="#" data-value="1" data-icon="%">
															%
														</a>
													</li>
													<li class="divider"></li>
													<li class="PRICE-option {if !$_FEE_TYPE[$swift_code]}active{/if}">
														<a href="#" data-value="0" data-icon="{$default_currency->sign}">
															{$default_currency->sign}
														</a>
													</li>
												</ul>
												</span>
											</div>
											<p class="help-block">
												{l s='Without VAT' mod='pms_gopay_extra'}
											</p>
										</div>
									{/if}
								</div>
							{/if}
						{/foreach}
					</div>
				{/if}
			{/if}
		{/foreach}
	{/foreach}
{else}
<table class="col-lg-12">
	<tr>
		<td class="list-empty">
			<div class="list-empty-msg">
				<i class="icon-warning-sign list-empty-icon"></i>
				{l s='Not enabled any payments for this currency' mod='pms_gopay_extra'}
			</div>
		</td>
	</tr>
</table>
{/if}
