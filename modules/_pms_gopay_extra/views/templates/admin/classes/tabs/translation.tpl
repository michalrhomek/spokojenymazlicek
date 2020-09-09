
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
<div id="tab-translate" class="tab-pane">
	{if $mod_security_warning}
	<div class="alert alert-warning">
		{l s='Apache mod_security is activated on your server. This could result in some Bad Request errors' mod='pms_gopay_extra'}
	</div>
	{/if}
	{if !empty($limit_warning)}
	<div class="alert alert-warning">
		{if $limit_warning['error_type'] == 'suhosin'}
			{l s='Warning! Your hosting provider is using the Suhosin patch for PHP, which limits the maximum number of fields allowed in a form:' mod='pms_gopay_extra'}

			<b>{$limit_warning['post.max_vars']}</b> {l s='for suhosin.post.max_vars.' mod='pms_gopay_extra'}<br/>
			<b>{$limit_warning['request.max_vars']}</b> {l s='for suhosin.request.max_vars.' mod='pms_gopay_extra'}<br/>
			{l s='Please ask your hosting provider to increase the Suhosin limit to' mod='pms_gopay_extra'}
		{else}
			{l s='Warning! Your PHP configuration limits the maximum number of fields allowed in a form:' mod='pms_gopay_extra'}<br/>
			<b>{$limit_warning['max_input_vars']}</b> {l s='for max_input_vars.' mod='pms_gopay_extra'}<br/>
			{l s='Please ask your hosting provider to increase this limit to' mod='pms_gopay_extra'}
		{/if}
		{l s='%s at least, or you will have to edit the translation files.' sprintf=$limit_warning['needed_limit'] mod='pms_gopay_extra'}
	</div>
	{else}
		<div class="row panel">
			<div class="col-md-6">
				<p>{l s='Expressions to translate:' mod='pms_gopay_extra'} <span class="badge">{l s='%d' mod='pms_gopay_extra' sprintf=$count}</span></p>
				<p>{l s='Total missing expressions:' mod='pms_gopay_extra'} <span class="badge missing_badge">{l s='%d' mod='pms_gopay_extra' sprintf=$missing_translations}</p>
			</div>
			<div  class="col-md-6">
				{$toggle_button}
				<button type="submit" id="btn-share-translation" class="btn btn-default pull-right">
					<i class="process-icon-export"></i> {l s='Share us your translation' mod='pms_gopay_extra'}
				</button>
			</div>
		</div>

		<div class="row">
			<div id="BoxUseSpecialSyntax_">
				<div class="alert alert-warning">
					<p>
						{l s='Some of these expressions use this special syntax: %s.' mod='pms_gopay_extra' sprintf='%d'}
						<br />
						{l s='You MUST use this syntax in your translations. Here are several examples:' mod='pms_gopay_extra'}
					</p>
					<ul>
						<li>"{l s='There are %s products' mod='pms_gopay_extra' sprintf='<b>%d</b>'}": {l s='"%s" will be replaced by a number.' mod='pms_gopay_extra' sprintf='%d'}</li>
						<li>"{l s='List of pages in %s' mod='pms_gopay_extra' sprintf='<b>%s</b>'}": {l s='"%s" will be replaced by a string.' mod='pms_gopay_extra' sprintf='%s'}</li>
						<li>"{l s='Feature: %1$s (%2$s values)' mod='pms_gopay_extra' sprintf=['<b>%1$s</b>', '<b>%2$d</b>']}": {l s='The numbers enable you to reorder the variables when necessary.' mod='pms_gopay_extra'}</li>
					</ul>
				</div>
			</div>
			<script type="text/javascript">
				$(document).ready(function(){
					$('a.useSpecialSyntax_').click(function(){
						var syntax = $(this).find('img').attr('alt');
						$('#BoxUseSpecialSyntax_ .syntax span').html(syntax+".");
					});
				});
			</script>
		</div>
		{foreach $modules_translations as $theme}
			{foreach $theme as $module_name => $module}
				{foreach $module as $template_name => $newLang}
					{if !empty($newLang)}
						{assign var=occurrences value=0}
						{foreach $newLang as $key => $value}
							{if empty($value['trad'])}{assign var=occurrences value=$occurrences+1}{/if}
						{/foreach}
						{if $occurrences > 0}
							{$missing_translations_module = $occurrences}
						{else}
							{$missing_translations_module = 0}
						{/if}
						<div class="panel content_translations">
							<div class="action-overlay" style="display: none;"></div>
							<h3 onclick="$('#pms_translate_{$template_name}').slideToggle();">
								{$template_name}
								<span class="badge">{$newLang|count}</span> 
								{l s='expressions' mod='pms_gopay_extra'} <span class="label label-danger">{$missing_translations_module}</span>
							</h3>
							<div name="modules_div" id="pms_translate_{$template_name}" style="display:{if $missing_translations_module}block{else}none{/if}" data-template="{$template_name}">
								<table class="table">
									{foreach $newLang as $key => $value}
										<tr>
											<td width="40%">{$key|stripslashes}</td>
											<td>=</td>
											<td>
												{* Prepare name string for md5() *}
												{capture assign="name"}{strtolower($module_name)}_{strtolower($template_name)}_{md5($key)}{/capture}
												{if $key|strlen < $textarea_sized}
													<input type="text"
														style="max-width: 450px;"
														class="{if empty($value.trad)}input-error-translate{/if}"
														data-template="{$template_name}"
														name="{$name|md5}"
														value="{$value.trad|regex_replace:'#"#':'&quot;'|stripslashes}"' />
												{else}
													<textarea rows="{($key|strlen / $textarea_sized)+1|intval}"
														style="max-width: 450px;"
														class="{if empty($value.trad)}input-error-translate{/if}"
														data-template="{$template_name}"
														name="{$name|md5}">{$value.trad|regex_replace:'#"#':'&quot;'|stripslashes|escape:'htmlall':'UTF-8'}</textarea>
												{/if}
											</td>
											<td>
												{if isset($value.use_sprintf) && $value.use_sprintf}
													<a class="useSpecialSyntax" title="{l s='This expression uses a special syntax:' mod='pms_gopay_extra'} {$value.use_sprintf}">
														<img src="{$smarty.const._PS_IMG_}admin/error.png" alt="{$value.use_sprintf}" />
													</a>
												{/if}
											</td>
										</tr>
									{/foreach}
								</table>
							</div>
							<div class="panel-footer">
								<button class="btn btn-default pull-right" name="btn-save-translation-{$template_name|escape:'htmlall':'UTF-8'}" type="button" data-action="save">
									<i class="process-icon-save"></i> {l s='Save' mod='pms_gopay_extra'}
								</button>
							</div>
						</div>
					{/if}
				{/foreach}
			{/foreach}
		{/foreach}
	{/if}
</div>
