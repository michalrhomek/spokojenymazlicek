
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
<div class="panel">
	<div class="panel-heading black">{l s='Payment instruments' mod='pms_gopay_extra'}</div>
	<h3>{l s='Select a payment method for your order' mod='pms_gopay_extra'}</h3>
	<hr style="border-bottom: 3px solid #ddd">
	{if isset($_PAYMENTS->enabledPaymentInstruments) && is_array($_PAYMENTS->enabledPaymentInstruments)}
		{foreach $_PAYMENTS->enabledPaymentInstruments as $payment}
			{assign var='payment_code' value=$payment->paymentInstrument}
			{if $payment_code == 'PAYMENT_CARD'}
				{assign var='checked' value=true}
				<div class="radio-inline">
					<label class="icon col-sm-3">
						<input type="checkbox"
								name="_HIDE_PAYMENT_{$payment_code}"
								id="_HIDE_PAYMENT_{$payment_code}"
								class=""
								value="1"
								{if $_HIDE_PAYMENT[$payment_code]}checked="checked"{/if}
						 />
						{if $_CUSTOM_NAMES}
							{foreach $languages as $language}
								{assign var='value_text' value=$_PAYMENT_NAME[$payment_code][$language.id_lang]}
								{if $languages|count > 1}
									<div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
								{/if}
											<input type="text"
													id="{$payment_code}_{$language.id_lang}"
													name="_PAYMENT_NAME_{$payment_code}_{$language.id_lang}"
													class=""
													value="{if $value_text}{$value_text|escape:'html':'UTF-8'}{else}{$payment->label->$gopayDefaultLang}{/if}"
											 />
								{if $languages|count > 1}
										<div class="col-lg-1 lang">
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
							<strong>{$payment->label->$gopayDefaultLang}</strong>
						{/if}
						<img src="{$_PAYMENT_IMAGE[$payment_code]}" style="max-width:100px">
					</label>
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
				</div>
				<hr style="border-bottom: 1px solid #ddd">
			{/if}
		{/foreach}

		{foreach $_PAYMENTS->enabledPaymentInstruments as $payment}
			{if count($payment->enabledSwifts) > 0}
				<h4 class="groupTitle">{l s='Online banking payment' mod='pms_gopay_extra'}</h4>
				<div class="radio-inline">
					{assign var='count' value=0}
					{foreach $payment->enabledSwifts as $swift}
						{assign var='payment_code' value=$swift->swift}
						{if $swift->isOnline}
							{if $count == 0}
								<div class="radio-inline">
							{/if}
							<label class="icon col-sm-3">
								<input type="checkbox"
										name="_HIDE_PAYMENT_{$payment_code}"
										id="_HIDE_PAYMENT_{$payment_code}"
										class=""
										value="1"
										{if $_HIDE_PAYMENT[$payment_code]}checked="checked"{/if}
								 />
								<img src="{$_PAYMENT_IMAGE[$payment_code]}" style="max-width:100px">
							</label>
							{assign var='count' value=1}
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
						{/if}
						{assign var='checked' value=true}
					{/foreach}
				</div>
				<hr style="border-bottom: 1px solid #ddd">
			{/if}
		{/foreach}
		{if $count == 1}
			</div>
			<hr style="border-bottom: 1px solid #ddd">
		{/if}

		{assign var='count' value=0}
		{foreach $_PAYMENTS->enabledPaymentInstruments as $payment}
			{assign var='payment_code' value=$payment->paymentInstrument}
			{if $payment->group == "wallet"}
				{if $count == 0}
					<h4 class="groupTitle">{l s='Electronic wallet' mod='pms_gopay_extra'}</h4>
					<div class="radio-inline">
				{/if}
				<label class="icon col-sm-3">
					<input type="checkbox"
							name="_HIDE_PAYMENT_{$payment_code}"
							id="_HIDE_PAYMENT_{$payment_code}"
							class=""
							value="1"
							{if $_HIDE_PAYMENT[$payment_code]}checked="checked"{/if}
					 />
					<img src="{$_PAYMENT_IMAGE[$payment_code]}" style="max-width:100px">
				</label>
				{assign var='count' value=1}
				{assign var='checked' value=true}
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
			{/if}
		{/foreach}
		{if $count == 1}
			</div>
			<hr style="border-bottom: 1px solid #ddd">
		{/if}

		{foreach $_PAYMENTS->enabledPaymentInstruments as $payment}
			{assign var='payment_code' value=$payment->paymentInstrument}
			{if $payment_code == 'SUPERCASH'}
				<div class="radio-inline">
					<label class="icon col-sm-3">
						<input type="checkbox"
								name="_HIDE_PAYMENT_{$payment_code}"
								id="_HIDE_PAYMENT_{$payment_code}"
								class=""
								value="1"
								{if $_HIDE_PAYMENT[$payment_code]}checked="checked"{/if}
						/>
						{if $_CUSTOM_NAMES}
							{foreach $languages as $language}
								{assign var='value_text' value=$_PAYMENT_NAME[$payment_code][$language.id_lang]}
								{if $languages|count > 1}
									<div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
								{/if}
											<input type="text"
													id="{$payment_code}_{$language.id_lang}"
													name="_PAYMENT_NAME_{$payment_code}_{$language.id_lang}"
													class=""
													value="{if $value_text}{$value_text|escape:'html':'UTF-8'}{else}{$payment->label->$gopayDefaultLang}{/if}"
											 />
								{if $languages|count > 1}
										<div class="col-lg-1 lang">
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
							<strong>{$payment->label->$gopayDefaultLang}</strong>
						{/if}
						<img src="{$_PAYMENT_IMAGE[$payment_code]}" style="max-width:100px">
					</label>
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
				</div>
				<hr style="border-bottom: 1px solid #ddd">
				{assign var='checked' value=true}
			{/if}
		{/foreach}
	{/if}
	<p class="radio-inline">
		<label>
			<input type="checkbox"
					name="_HIDE_GROUP_ACCOUNT"
					id="_HIDE_GROUP_ACCOUNT"
					class=""
					value="1"
					{if $_HIDE_GROUP['ACCOUNT']}checked{/if}
			 />
			<strong>{l s='Choose from payment methods directly at the gate GoPay' mod='pms_gopay_extra'}</strong>
		</label>
	</p>
	<p>{l s='After redirecting the payment gateway you choose your preferred payment method of the many gated GoPay.' mod='pms_gopay_extra'}</p>	<hr style="border-bottom: 1px solid #ddd">

	<p class="radio-inline">
		<button type="submit" class="btn btn-success col-sm-3 pull-right" disabled>
			{l s='Pay my order' mod='pms_gopay_extra'}
		</button>
	</p>
</div>
