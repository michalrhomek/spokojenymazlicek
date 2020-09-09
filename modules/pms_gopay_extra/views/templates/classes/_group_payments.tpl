
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
<div id="errors_ajax"></div>
{assign var='number' value=9}
{if !$_CUSTOM_NAMES}{assign var='number' value=$number+1}{/if}
{if $_PRICE_DIFFERENT && $_PRICE_VIEW}{assign var='number' value=$number-1}{/if}
{if $_CUSTOM_IMAGES}{assign var='number' value=$number-3}{/if}

{foreach $_PAYMENTS as $key => $group}
<div class="form-group">
	<div class="col-lg-2 checkbox">
		<label for="_HIDE_GROUP_{$key}">
			<input type="checkbox"
					name="_HIDE_GROUP_{$key}"
					id="_HIDE_GROUP_{$key}"
					class=""
					value="1"
					{if $_HIDE_GROUP[$key]}checked{/if}
			 />
			{$key}
		</label>
	</div>
	{if $_DISPLAY_IMAGES}
		<div class="col-lg-2">
			<div id="_IMAGE_{$key}"><img src="{$_PAYMENT_IMAGE[$key]}"></div>
		</div>
		{if $_CUSTOM_IMAGES}
			<div class="col-lg-1">
				<input type="file"
						id="_IMAGE_FILE_{$key}"
						style="display:none"
						multiple
						accept="image/gif"
						onchange="handleFiles(this.files, '{$key}')"
				>
				<a href="#" onclick='document.getElementById("_IMAGE_FILE_{$key}").click();'>
					{l s='Upload logo' mod='pms_gopay_extra'}
				</a><br>
				<a href="#" class="delete" data-id="{$key}" data-confirm="{l s='Are you sure to delete this item?' mod='pms_gopay_extra'}">
					{l s='Delete' mod='pms_gopay_extra'}
				</a>
			</div>
		{/if}
	{/if}
	<div class="col-lg-{$number}">
		{if $_CUSTOM_NAMES}
			{foreach $languages as $language}
				{assign var='value_text' value=$_GROUP_NAME[$key][$language.id_lang]}

				{if $languages|count > 1}
					<div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
				{/if}
				<div class="col-lg-11 mk-textbox">
					{l s='Title' mod='pms_gopay_extra'}
					<input type="text"
								id="{$key}_{$language.id_lang}"
								name="_GROUP_NAME_{$key}_{$language.id_lang}"
								class=""
								value="{if $value_text}{$value_text|escape:'html':'UTF-8'}{else}{$group}{/if}"
					 />
					<br />
					{l s='Description' mod='pms_gopay_extra'}
					<textarea class="rte autoload_rte"
								id="{$key}_GROUP_DESC_{$language.id_lang}"
								name="_GROUP_DESC_{$key}_{$language.id_lang}"
						 >{$_GROUP_DESC[$key][$language.id_lang]}</textarea>
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
			{$group}
		{/if}
	</div>
	{if $_PRICE_DIFFERENT && $_PRICE_VIEW}
		<div class="col-lg-2">
			<div class="input-group PRICE-type-VALUES">
				<input type="text"
						name="G_FEE_VALUE_{$key}"
						id="_FEE_VALUE_{$key}"
						value="{$_FEE_VALUE[$key]}"
						class="col-lg-1"
						maxlength="6"
				>
				<span class="input-group-milos">
					<input type="hidden" class="_FEE_TYPE" name="G_FEE_TYPE_{$key}" value="{$_FEE_TYPE[$key]}">
					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
						<span class="_PRICE_ICON">{if $_FEE_TYPE[$key]}%{else}{$default_currency->sign}{/if}</span>
						<i class="icon-caret-down"></i>
					</button>
					<ul class="dropdown-menu">
						<li class="PRICE-option {if $_FEE_TYPE[$key]}active{/if}">
							<a href="#" data-value="1" data-icon="%">
								%
							</a>
						</li>
						<li class="divider"></li>
						<li class="PRICE-option {if !$_FEE_TYPE[$key]}active{/if}">
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
{/foreach}