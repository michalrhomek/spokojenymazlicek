{*
*  Module TOTAL IMPORT PRO for PrestaShop 1.5+ From HostJars hostjars.com
*
* @author    HostJars
* @copyright HostJars
* @license   HostJars
*}

{extends file="helpers/form/form.tpl"}
	{if $step == 'step1'}
		{block name="field"}
		{if $input.type == 'update_text'}
			{foreach $input.fields as $field}
				{if count($input.fields) > 1}
				<div class="form-group {$field.class|escape:'htmlall':'UTF-8'}">
				{/if}
				<label class="control-label col-lg-3 {$field.class|escape:'htmlall':'UTF-8'}">{$field.label|escape:'htmlall':'UTF-8'}</label>
				<div class="margin-form col-lg-6 {$field.class|escape:'htmlall':'UTF-8'}">
					{if $input.input == 'textarea'}
						<textarea name="{$field.name|escape:'htmlall':'UTF-8'}" id="{if isset($field.id)}{$field.id|escape:'htmlall':'UTF-8'}{else}{$field.name|escape:'htmlall':'UTF-8'}{/if}" cols="{$field.cols|escape:'htmlall':'UTF-8'}" rows="{$field.rows|escape:'htmlall':'UTF-8'}" {if isset($field.autoload_rte) AND $field.autoload_rte}class="rte autoload_rte {if isset($field.class)}{$field.class|escape:'htmlall':'UTF-8'}{/if}"{/if}>{$fields_value[$field.name]|escape:'htmlall':'UTF-8'}</textarea>
					{elseif $input.input == 'file'}
						<input type="file" name="{$field.id|escape:'htmlall':'UTF-8'}" id="{$field.id|escape:'htmlall':'UTF-8'}" class="hide" />
						{if $smarty.const._PS_VERSION_ >= '1.6'}
						<div class="dummyfile input-group">
							<span class="input-group-addon"><i class="icon-file"></i></span>
							<input id="{$field.id|escape:'htmlall':'UTF-8'}-name" type="text" class="disabled" name="filename" readonly />
							<span class="input-group-btn">
								<button id="{$field.id|escape:'htmlall':'UTF-8'}-selectbutton" type="button" name="submitAddAttachments" class="button btn btn-success">
									<i class="icon-folder-open"></i> {l s='Choose a file' mod='totalimportpro'}
								</button>
							</span>
						</div>
						<script>
						$(document).ready(function(){
							$('#{$field.id|escape:'htmlall':'UTF-8'}-selectbutton').click(function(e){
								$('#{$field.id|escape:"htmlall":"UTF-8"}').trigger('click');
							});
							$('#{$field.id|escape:"htmlall":"UTF-8"}').change(function(e){
								var val = $(this).val();
								var file = val.split(/[\\/]/);
								$('#{$field.id|escape:"htmlall":"UTF-8"}-name').val(file[file.length-1]);
							});
							$('.intercom-tracked').each(function () {
								var self = this;
								$(this).attr('class').split(' ').map(function (e, i) {
									if (e.indexOf('i-t:') !== -1) {
										$(self).attr('intercom-tracked', e.replace('i-t:', ''));
									}
								});
							});
						});
						</script>
						{/if}
					{elseif $input.input == 'select'}
						<select name="{$field.name|escape:'htmlall':'UTF-8'}" class="{if isset($field.class)}{$field.class|escape:'htmlall':'UTF-8'}{/if}"
								id="{if isset($field.id)}{$field.id|escape:'htmlall':'UTF-8'}{else}{$field.name|escape:'htmlall':'UTF-8'}{/if}">
							{foreach $field.options.query AS $option}
								<option value="{$option[$field.options.id]|escape:'htmlall':'UTF-8'}"
										{if $fields_value[$field.name] == $option[$field.options.id]}
											selected="selected"
										{/if}
										>{$option[$field.options.name]|escape:'htmlall':'UTF-8'}</option>
							{/foreach}
						</select>
					{/if}
					{if isset($field.desc)}
						<p class="help-block">{$field.desc|escape:'htmlall':'UTF-8'}</p>
					{/if}
				</div>
				{if count($input.fields) > 1}
				</div>
				{/if}
			{/foreach}
		{elseif $input.type == 'advanced'}
			<div class="accordion">
				<div class="advanced">
					<h4><a href="#">{l s='Advanced Settings (optional)' mod='totalimportpro'}</a></h4>
				</div>
				<div class="advance_fields">
				{foreach $input.fields as $field}
					<div class="form-group {if isset($field.class)}{$field.class|escape:'htmlall':'UTF-8'}{/if}">
					{if $field.input == 'checkbox'}
					<label class="control-label col-lg-3">{$field.label|escape:'htmlall':'UTF-8'}</label>
						<div class="col-lg-6 margin-form">
						<input type="checkbox"
							   name="{$field.name|escape:'htmlall':'UTF-8'}"
							   id="{$field.id|escape:'htmlall':'UTF-8'}"
							   class="{if isset($field.class)}{$field.class|escape:'htmlall':'UTF-8'}{/if}"
								{if isset($fields_value[$field.id]) AND $fields_value[$field.id]}checked="checked"{/if} />
								{if isset($field.desc)}
									<p class="help-block">{$field.desc|escape:'htmlall':'UTF-8'}</p>
								{/if}
						</div>
					{elseif $field.input == 'select'}
						<label class="control-label col-lg-3">{$field.label|escape:'htmlall':'UTF-8'}</label>
						<div class="col-lg-6 margin-form">
						<select name="{$field.name|escape:'htmlall':'UTF-8'}" class="{if isset($field.class)}{$field.class|escape:'htmlall':'UTF-8'}{/if}"
								id="{if isset($field.id)}{$field.id|escape:'htmlall':'UTF-8'}{else}{$field.name|escape:'htmlall':'UTF-8'}{/if}">
							{foreach $field.options.query AS $option}
								<option value="{$option[$field.options.id]|escape:'htmlall':'UTF-8'}"
										{if $fields_value[$field.name] == $option[$field.options.id]}
											selected="selected"
										{/if}
										>{$option[$field.options.name]|escape:'htmlall':'UTF-8'}</option>
							{/foreach}
						</select>
						{if isset($field.desc)}
						<p class="help-block">{$field.desc|escape:'htmlall':'UTF-8'}</p>
						{/if}
						</div>
					{/if}
					</div>
				{/foreach}
				</div>
			</div>
		{else}
			{$smarty.block.parent}
		{/if}
		{/block}
	{elseif $step == 'step2'}
		{block name="field"}
			{if $input.type == 'hint'}
				<p><div class="hint" style="display:block; position:auto; width:500px">{$input.info|escape:'htmlall':'UTF-8'}</div></p>
			{elseif $input.type == 'multi_tree'}
				<div class="col-lg-9">
					<div id="multishop-tree" style="margin-right:100px; font-size:1.2em;">
						<ul>
							{assign var=group_num value=1}
							{assign var=shop_ids value=0}
							{foreach $input.shop_groups as $group}
								<li class ="tree-group">
								<div class="checkbox">
								<label class="check-text">
									<input type="checkbox" onclick="toggleChecked(this.checked, {$group_num|escape:'htmlall':'UTF-8'})" name="shop_group[]" value="{$group.id|escape:'htmlall':'UTF-8'}"
										   {if $fields_value.group_id == $group.id}checked="checked"{/if} />
									{$group.name|escape:'htmlall':'UTF-8'}</label></div>
									{foreach $group.shops as $shop}
										<li class="tree-shop">
										<div class="checkbox">
										<label class="check-text">
											<input class="group{$group_num|escape:'htmlall':'UTF-8'}" type="checkbox" name="id_shop_list[]" value="{$shop.id_shop|escape:'htmlall':'UTF-8'}"
											{if isset($fields_value.id_shop_list.$shop_ids)}
													{if $fields_value.id_shop_list.$shop_ids == $shop.id_shop}checked="checked"{/if}
											{/if}
											/>
											{$shop.name|escape:'htmlall':'UTF-8'}</label>
											</div>
										</li>
										{assign var=shop_ids value=$shop_ids+1}
									{/foreach}
								</li>
								{assign var=group_num value=$group_num+1}
							{/foreach}
						</ul>
						{if isset($input.desc)}
							<p class="help-block">{$input.desc|escape:'htmlall':'UTF-8'}</p>
						{/if}
					</div>
				</div>
			{else}
				{$smarty.block.parent}
			{/if}
		{/block}

	{elseif $field.step == 'step3'}
		{block name="field"}
			{if $input.type == 'feed_sample'}
				<div class="stepInfo row">{l s='If you need to adjust any data from your feed before importing it you can use the Operations below on the fields in your database. You can also leave this screen open and use phpmyadmin to directly adjust the database table hj_import and then return here to complete the import.' mod='totalimportpro'}</div>
					<!-- Sample Fields -->
				<div id="sampleFields" class="row">
					<p class="h3">{l s='Feed Sample' mod='totalimportpro'}</p>
					<table class="table table-bordered">
						{assign var=sample_headings value=$input.values.headings}
						<thead>
							<tr class="nodrag nodrop">
								{foreach $sample_headings as $heading}
								<th>
									{$heading|escape:'htmlall':'UTF-8'}
								</th>
								{/foreach}
							</tr>
						</thead>
						{assign var=sample_row value=$input.values.rows}
						<tbody>
							<tr>
								{foreach $sample_row as $sample}
									<td class="center">
										{$sample|strip_tags|truncate:90:"..."|escape:'htmlall':'UTF-8'}
									</td>
								{/foreach}
							</tr>
						</tbody>
					</table>
				</div>
				<button type="button" id="nextRow" value="1" class="button btn btn-info" intercom-tracked="clicked-nextRow-step3">View Next Row</button>
			{elseif $input.type == 'operations'}
				</br>
				<!--Operations-->
				<table class="table" cellspacing="0" cellpadding="0">
					<thead>
						<tr class="nodrag nodrop">
							<th class="left">{l s='Operation Type' mod='totalimportpro'}</th>
							<th class="left" style="min-width:40em;">{l s='Description' mod='totalimportpro'}</th>
							<th colspan="2"></th>
						</tr>
					</thead>
					<tbody id="operations">
						{if (isset($fields_value.adjust))}
							{assign var=operations value=$fields_value.adjust}
							{foreach $operations as $row => $previous_data}
								{if isset($previous_data.name) AND isset($previous_data.inputs[0])}
									<tr id="adjustment_row_{$row|escape:'htmlall':'UTF-8'}">
										<td class="left">
											{$previous_data.name|escape:'htmlall':'UTF-8'}
											<input type="hidden" name="adjust[{$row|escape:'htmlall':'UTF-8'}][]" value="{$previous_data.values[0]|escape:'htmlall':'UTF-8'}">
										</td>
										<td class="left">
											{assign var=field_num value=1}
											{foreach $previous_data.inputs as $field => $field_val}
												{* Prepend Text *}
												{if isset($field_val.prepend)}
													{$field_val.prepend|escape:'htmlall':'UTF-8'}
												{/if}
												{* Fields *}
												{if isset($previous_data.values[$field_num])}
													{if $field_val.type == 'text'}
														<input type="text" name="adjust[{$row|escape:'htmlall':'UTF-8'}][]"
														{if isset($previous_data.values[$field_num])}value="{$previous_data.values[$field_num]|escape:'htmlall':'UTF-8'}"{/if}
														/>
													{elseif $field_val.type == 'field'}
														<select name="adjust[{$row|escape:'htmlall':'UTF-8'}][]">
															{foreach $fields_value.adjust.field_list as $key => $value}
																<option value="{$value|escape:'htmlall':'UTF-8'}" {if $previous_data.values[$field_num] == $value}selected="selected"{/if}>
																	{$value|escape:'htmlall':'UTF-8'}
																</option>
															{/foreach}
														</select>
														{if isset($field_val.option)}
															<a onclick="removeSelectBefore(this);">x</a>
														{/if}
													{/if}
												{/if}
												{assign var=field_num value=$field_num+1}
											{/foreach}
											{while isset($previous_data.values[$field_num])}
												<select name="adjust[{$row|escape:'htmlall':'UTF-8'}][]">
													{foreach $fields_value.adjust.field_list as $key => $value}
														<option value="{$value|escape:'htmlall':'UTF-8'}" {if $previous_data.values[$field_num] == $value}selected="selected"{/if}>
															{$value|escape:'htmlall':'UTF-8'}
														</option>
													{/foreach}
												</select>
												{assign var=field_num value=$field_num+1}
											{/while}
											{if isset($field_val.option) AND $field_val.option == 'addMore'}
												<a onclick="return addSub(this);" class="button btn btn-default" intercom-tracked="more_fields_clicked"><span>More&nbsp;&rarr;&nbsp;</span></a>
											{/if}
											</td>
										</td>
										<td class="left"><a onclick="$('#adjustment_row_{$row|escape:'htmlall':'UTF-8'}').remove();" class="button btn btn-danger">{l s='Remove'|escape:'htmlall':'UTF-8' mod='totalimportpro'}</a></td>
									</tr>
								{/if}
							{/foreach}
						{/if}
					</tbody>
					<tfoot>
						<tr>
							<td class="center" colspan="2">
								<select id="operationToAdd">
								{foreach $input.labels as $label}
									<optgroup label="{$label|escape:'htmlall':'UTF-8'}">
									{foreach $input.operations as $key => $value}
										{if $value.label == $label}
											<option value="{$key|escape:'htmlall':'UTF-8'}">{$value.name|escape:'htmlall':'UTF-8'}</option>
										{/if}
									{/foreach}
									</optgroup>
								{/foreach}
								</select>
							</td>
							<td class="center">
								<a class="button btn btn-info" id="addOperation" intercom-tracked="added-operation" onclick="addOperation();">{l s='Add Operation' mod='totalimportpro'}</a>
							</td>
						</tr>
					</tfoot>
				</table>
			{else}
				{$smarty.block.parent}
			{/if}
		{/block}

	{elseif $field.step == 'step4'}
		{block name="field"}
		{if $input.type == 'feed_sample_map'}
			<script>
			$(document).ready(function(){
				if ($.fn.tooltip) {
					$('[data-toggle="tooltip"]').tooltip();
				}
			});
			</script>
			<div class="stepInfo">{l s='The PrestaShop field column contains the name of the field in PrestaShop, the Feed Field is where you must enter the heading name of the column that you want to import to each PrestaShop field. You can set each field to \"None\" if you do not have anything to import there. None of the fields are required, but the more you can provide the better your import will be.' mod='totalimportpro'}</div>
			<div id="sampleFields" class="row">
				<p class="h3">{l s='Feed Sample' mod='totalimportpro'}</p>
				<table class="table table-bordered">
					{assign var=sample_headings value=$input.values.headings}
					<thead>
						<tr class="nodrag nodrop">
							{foreach $sample_headings as $heading}
							<th>
								{$heading|escape:'htmlall':'UTF-8'}
							</th>
							{/foreach}
						</tr>
					</thead>
					{assign var=sample_row value=$input.values.rows}
					<tbody>
						<tr>
							{foreach $sample_row as $sample}
								<td class="center">
									{$sample|strip_tags|truncate:90:"..."|escape:'htmlall':'UTF-8'}
								</td>
							{/foreach}
						</tr>
					</tbody>
				</table>
			</div>
			<button type="button" id="nextRow" value="1" class="button btn btn-info" intercom-tracked="clicked-nextRow-step4">View Next Row</button>
		{elseif $input.type == 'tabs'}
			<table class="table">
			<p>{l s='Simple Stock Update' mod='totalimportpro'}
			<select style="width: 16.6%" name="simple" id="simple" onchange="updateText(this, 'simple')">
				<option value="0" {if isset($fields_value.simple) && !$fields_value.simple}selected="true"{/if}>No</option>
				<option value="1" {if isset($fields_value.simple) && $fields_value.simple}selected="true"{/if}>Yes</option>
			</select>
			</p>
			<tr id="simple_update" style="display:none;">
				<td colspan="3">
				<ul id="simple_tabs" class="fieldsTab" style="clear:both">
					<li id="fieldsTabSimple" class="fieldsTabButton selected">{l s='Simple Fields' mod='totalimportpro'}</li>
					<li id="fieldsTabMatching" class="fieldsTabButton">{l s='Matching Field' mod='totalimportpro'}</li>
				</ul>
				<div id="fieldsTabSimpleSheet" class="tabItem selected" style="clear:both;">
					<table class="table">
						<tr>
							<td class="mapping_field col-left"><h3>{l s='PrestaShop Field' mod='totalimportpro'}</h3></td>
							<td colspan="3"><h3>{l s='Feed Field' mod='totalimportpro'}</h3></td>
						</tr>
							{foreach $input.simple as $simple_field}
							{if $simple_field == 'specific_price'}
								<!-- START Specific Price Vert Field -->
								{if isset($fields_value['simple_names']['specific_price']) AND (count($fields_value['simple_names']['specific_price']) > 1)
								AND !empty($fields_value['simple_names']['specific_price'][0])}
									{assign var=field_count value=count($fields_value['simple_names']['specific_price'])}
								{else}
									{assign var=field_count value=1}
								{/if}
								{for $i=0 to $field_count-1}
									{if isset($fields_value['simple_names']['specific_price'][$i]) || $i == 0}
										<tr class="vert">
											<td class="mapping_field col-left">{l s='Specific Price' mod='totalimportpro'}
												{if $i == $field_count-1}
													<a style="float:right;" onclick="return addVert(this, false);" class="button btn btn-default" intercom-tracked="more_fields_clicked">More&nbsp;&darr;&nbsp;</a>
												{/if}
											</td>
											<td class="source_field">
												<select name="simple_names[specific_price][]">
													<option value=''>{l s='None' mod='totalimportpro'}</option>
													{foreach $input.feed_fields as $field}
														<option value="{$field|escape:'htmlall':'UTF-8'}"
																{if isset($fields_value['simple_names']['specific_price'][$i]) AND $fields_value['simple_names']['specific_price'][$i] == $field}
																	selected="true"
																{/if}
																>{$field|escape:'htmlall':'UTF-8'}
														</option>
													{/foreach}
												</select>
											</td>
										</tr>
									{/if}
								{/for}
								<!-- END Specific Price Vert Field -->
							{else}
								<!-- START Simple Single Field -->
								<tr>
									<td class="mapping_field col-left">{$input.field_map[$simple_field]|escape:'htmlall':'UTF-8'}</td>
									<td class="source_field">
										<select name="simple_names[{$simple_field|escape:'htmlall':'UTF-8'}]">
											<option value=''>{l s='None' mod='totalimportpro'}</option>
											{foreach $input.feed_fields as $field}
												<option value="{$field|escape:'htmlall':'UTF-8'}"
													{if isset($fields_value['simple_names'][$simple_field]) AND $fields_value['simple_names'][$simple_field] == $field}
														selected = "true"
													{/if}
													>{$field|escape:'htmlall':'UTF-8'}
												</option>
											{/foreach}
										</select>
									</td>
								</tr>
								<!-- END Simple Single Field -->
							{/if}
							{/foreach}
						</table>
					</div>
				<div id="fieldsTabMatchingSheet" class="tabItem" style="clear:both;">
						<table class="table">
							<tr>
								<td class="mapping_field col-left"><h3>{l s='PrestaShop Field' mod='totalimportpro'}</h3></td>
								<td colspan="3"><h3>{l s='Feed Field' mod='totalimportpro'}</h3></td>
							</tr>
							<!-- Simple Matching Field -->
							{foreach $input.matching as $matching_field}
							<tr>
								<td class="mapping_field col-left">{$input.field_map[$matching_field]|escape:'htmlall':'UTF-8'}</td>
								<td class="source_field">
									<select name="simple_names[{$matching_field|escape:'htmlall':'UTF-8'}]">
										<option value=''>{l s='None' mod='totalimportpro'}</option>
										{foreach $input.feed_fields as $field}
										<option value="{$field|escape:'htmlall':'UTF-8'}"
											{if (isset($fields_value['simple_names'][$matching_field])) AND $fields_value['simple_names'][$matching_field] == $field}
												selected="true"
											{/if}
											>{$field|escape:'htmlall':'UTF-8'}
										</option>
										{/foreach}
									</select>
								</td>
							</tr>
							{/foreach}
							<!-- END Simple Matching Field -->
						</table>
					</div>
				</td>
			</tr>
			<tr id="full">
				<td colspan="3">
					<ul id="full_tabs" class="fieldsTab" style="clear:both">
						{assign var=tab_count value=0}
						{foreach $input.tab_fields as $tab => $value}
							{assign var=tab_count value=$tab_count+1}
							<li id="fieldsTab{$tab|escape:'htmlall':'UTF-8'}" class="fieldsTabButton {if $tab_count == 1}selected{/if}">{$tab|escape:'htmlall':'UTF-8'}</li>
						{/foreach}
					</ul>
					{assign var=tab_count value=0}
					{foreach $input.tab_fields as $tab => $value}
						{assign var=tab_count value=$tab_count+1}
						<div id="fieldsTab{$tab|escape:'htmlall':'UTF-8'}Sheet" class="tabItem {if $tab_count == 1}selected{/if}" style="clear:both;">
							<table class="table">
								<tr>
									<td class="mapping_field col-left"><h3>{l s='PrestaShop Field' mod='totalimportpro'}</h3></td>
									<td colspan="3"><h3>{l s='Feed Field' mod='totalimportpro'}</h3></td>
								</tr>
								{foreach $input.field_map as $input_name => $pretty_name}
									{if in_array($input_name, $value)}
										{if !is_array($pretty_name)}
											{if in_array($input_name, $input.multi_language_fields)}
												 <!--Multilanguage Field-->
												<tr>
													<td class="mapping_field col-left">
														{$pretty_name|escape:'htmlall':'UTF-8'}
													</td>
													<td class="source_field multi-lang">
													<div class="lang-select-group">
													{assign var=lang_counter value=1}
													{foreach $input.languages as $lang}
														<select {if $lang_counter > 1}style="display:none;"{/if} class="lang-select lang-{$lang['id_lang']|escape:'htmlall':'UTF-8'}" name="field_names[{$input_name|escape:'htmlall':'UTF-8'}][{$lang['id_lang']|escape:'htmlall':'UTF-8'}]">
															<option value=''>{l s='None' mod='totalimportpro'}</option>
															{foreach $input.feed_fields as $field}
																<option value="{$field|escape:'htmlall':'UTF-8'}"
																		{if isset($fields_value['field_names'][$input_name][{$lang['id_lang']}]) AND $fields_value['field_names'][$input_name][{$lang['id_lang']}] == $field}
																			selected="true"
																		{/if}
																		>{$field|escape:'htmlall':'UTF-8'}
																</option>
															{/foreach}
														</select>
														{assign var=lang_counter value=$lang_counter+1}
													{/foreach}
													<!--Language choice dropdown-->
													<span class="dropdown">
													   <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" tabindex="-1" intercom-tracked="lang-change">
													   {$input.languages[0]['iso_code']|escape:'htmlall':'UTF-8'}<span class="caret"></span>
														</button>
														<ul class="dropdown-menu">
															{foreach $input.languages as $langdrop}
															<li>
																<a onclick="hideOtherLanguages($(this), {$langdrop['id_lang']|escape:'htmlall':'UTF-8'}, '{$langdrop['iso_code']|escape:'htmlall':'UTF-8'}')">{$langdrop['name']|escape:'htmlall':'UTF-8'}</a>
															</li>
															{/foreach}
														</ul>
													</span>
													</div>
													</td>
												</tr>
											{else}
											<!--Normal Field-->
												<tr>
													<td class="mapping_field col-left">{$pretty_name|escape:'htmlall':'UTF-8'}
													{if $pretty_name == "Combination Supplier"}
														<img class="info_image" src="{$smarty.const._PS_ADMIN_IMG_|escape:'htmlall':'UTF-8'}information.png" data-toggle="tooltip" data-placement="right" title="" data-original-title="(Supplier Name:Combination Supplier Reference:Combination Supplier Price:Combination Supplier Currency)">
												   {/if}
													</td></td>
													<td class="source_field">
														<select name="field_names[{$input_name|escape:'htmlall':'UTF-8'}]">
															<option value=''>{l s='None' mod='totalimportpro'}</option>
															{foreach $input.feed_fields as $field}
																<option value="{$field|escape:'htmlall':'UTF-8'}"
																		{if (isset($fields_value['field_names'][$input_name]) AND $fields_value['field_names'][$input_name] == $field)}
																			selected="true"
																		{/if}
																		>{$field|escape:'htmlall':'UTF-8'}
																</option>
															{/foreach}
														</select>
													</td>
												</tr>
											{/if}
										 {elseif $pretty_name[1] == 'combo'}
											<!--
											Combo Row

											Format:
												array(
													array(array(Field Name 1, field_name_1), ..),
													'combo',
													'X for "Add X" button',
												)

												Add any number of fields to the first array, include display name and name of input.
											-->

											<!--
											Check for exisiting combos

											$pretty_name[0][0][1] is the field_name of the first field in the combo
											array_filter removes empty fields, reset chooses the first lang

											-->
											{if isset($fields_value['field_names'][$pretty_name[0][0][1]]) AND count(array_filter(reset($fields_value['field_names'][$pretty_name[0][0][1]]))) > 1}
												{assign field_count count(array_filter(reset($fields_value['field_names'][$pretty_name[0][0][1]])))}
											{else}
												{assign var=field_count value=1}
											{/if}
											{for $i=0 to $field_count-1}
												<tr class="combo">
													<td class="mapping_field">
														<!-- Display all Field Names -->
														{foreach $pretty_name[0] as $combo_names}
															<div>{$combo_names[0]|escape:'htmlall':'UTF-8'}</div>
														{/foreach}
														{if $i==$field_count-1}
															<a onclick="return addCombo(this);" class="combo_button button btn btn-default">Add {$pretty_name[2]|escape:'htmlall':'UTF-8'}&nbsp;&darr;&nbsp;</a>
														{/if}
													</td>
													<td class="source_field">
														{foreach $pretty_name[0] as $combo_names}
															{if in_array($input_name, $input.multi_language_fields)}
																<div class="multi-lang">
																<!-- Combo Multi-Language -->
																<div class="lang-select-group">
																{foreach $input.languages as $lang name=LangLoop}
																	<select {if !$smarty.foreach.LangLoop.first}style="display:none;"{/if} class="lang-select lang-{$lang['id_lang']|escape:'htmlall':'UTF-8'}"name="field_names[{$combo_names[1]|escape:'htmlall':'UTF-8'}][{$lang['id_lang']|escape:'htmlall':'UTF-8'}][]">
																		<option value=''>{l s='None' mod='totalimportpro'}</option>
																		{foreach $input.feed_fields as $field}
																			<option value="{$field|escape:'htmlall':'UTF-8'}"
																					{if isset($fields_value['field_names'][$combo_names[1]][$lang['id_lang']][$i]) AND $fields_value['field_names'][$combo_names[1]][$lang['id_lang']][$i] == $field}
																						selected="true"
																					{/if}
																					>{$field|escape:'htmlall':'UTF-8'}
																			</option>
																		{/foreach}
																	</select>
																{/foreach}
																<!--Language choice dropdown-->
																	<span class="dropdown">
																	   <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" tabindex="-1" intercom-tracked="lang-change">
																	   {$input.languages[0]['iso_code']|escape:'htmlall':'UTF-8'}<span class="caret"></span>
																		</button>
																		<ul class="dropdown-menu">
																			{foreach $input.languages as $langdrop}
																			<li>
																				<a onclick="hideOtherLanguages($(this), {$langdrop['id_lang']|escape:'htmlall':'UTF-8'}, '{$langdrop['iso_code']|escape:'htmlall':'UTF-8'}')">{$langdrop['name']|escape:'htmlall':'UTF-8'}</a>
																			</li>
																			{/foreach}
																		</ul>
																	</span>
																</div>
																<!-- Combo Multi-Language END -->
															{else}
																<div>
																<!-- Combo Single-Language -->
																<select name="field_names[{$combo_names[1]|escape:'htmlall':'UTF-8'}][]">
																<option value=''>{l s='None' mod='totalimportpro'}</option>
																{foreach $input.feed_fields as $field}
																	<option value="{$field|escape:'htmlall':'UTF-8'}"
																			{if (isset($fields_value['field_names'][$combo_names[0]]) AND $fields_value['field_names'][$combo_names[0]] == $field)}
																				selected="true"
																			{/if}
																			>{$field|escape:'htmlall':'UTF-8'}
																	</option>
																{/foreach}
																</select>
															{/if}
															</div>
														{/foreach}
													</td>
												</tr>
											{/for}
										{elseif $pretty_name[1] == 'vert'}
											{if in_array($input_name, $input.multi_language_fields)}
												<!-- Multi downwards Field -->
													{if isset($fields_value['field_names'][$input_name][$lang['id_lang']]) AND (count($fields_value['field_names'][$input_name][$lang['id_lang']]) > 1)
														AND !empty($fields_value['field_names'][$input_name][$lang['id_lang']][0])}
															{assign var=field_count value=count($fields_value['field_names'][{$input_name|escape:'htmlall':'UTF-8'}][$lang['id_lang']])|escape:'htmlall':'UTF-8'}
													{else}
														{assign var=field_count value=1}
													{/if}
													{for $i=0 to $field_count-1}
														<!--Multilanguage Field-->
														{if isset($fields_value['field_names'][$input_name][$lang['id_lang']][$i]) || $i == 0}
															<tr class="vert">
																<td class="mapping_field col-left">{$pretty_name[0]|escape:'htmlall':'UTF-8'}
																{if $pretty_name[0] == "Tag"}
																<img class="info_image" src="{$smarty.const._PS_ADMIN_IMG_|escape:'htmlall':'UTF-8'}information.png" data-toggle="tooltip" data-placement="right" title="" data-original-title="This field accepts tags delimited by a comma. e.g. Tag1,Tag2,Tag3.">
															   {/if}
																{if $i == $field_count-1}
																	<a style="float:right;" onclick="return addVert(this, false);" class="button btn btn-default" intercom-tracked="more_fields_clicked">More&nbsp;&darr;&nbsp;</a>
																{/if}
																</td>
																<td class="source_field multi-lang">
																<div class="lang-select-group">
																{foreach $input.languages as $lang name=LangLoop}
																		<select {if !$smarty.foreach.LangLoop.first}style="display:none;"{/if} class="lang-select lang-{$lang['id_lang']|escape:'htmlall':'UTF-8'}"name="field_names[{$input_name|escape:'htmlall':'UTF-8'}][{$lang['id_lang']|escape:'htmlall':'UTF-8'}][]">
																		<option value=''>{l s='None'|escape:'htmlall':'UTF-8' mod='totalimportpro'}</option>
																		{foreach $input.feed_fields as $field}
																			<option value="{$field|escape:'htmlall':'UTF-8'}"
																				{if isset($fields_value['field_names'][$input_name][$lang['id_lang']][$i]) AND $fields_value['field_names'][$input_name][$lang['id_lang']][$i] == $field}
																					selected="true"
																				{/if}
																				>{$field|escape:'htmlall':'UTF-8'}
																			</option>
																		{/foreach}
																	</select>
																{/foreach}
																<!--Language choice dropdown-->
																<span class="dropdown">
																   <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" tabindex="-1" intercom-tracked="lang-change">
																   {$input.languages[0]['iso_code']|escape:'htmlall':'UTF-8'}<span class="caret"></span>
																	</button>
																	<ul class="dropdown-menu">
																		{foreach $input.languages as $langdrop}
																		<li>
																			<a onclick="hideOtherLanguages($(this), {$langdrop['id_lang']|escape:'htmlall':'UTF-8'}, '{$langdrop['iso_code']|escape:'htmlall':'UTF-8'}')">{$langdrop['name']|escape:'htmlall':'UTF-8'}</a>
																		</li>
																		{/foreach}
																	</ul>
																</span>
																</div>
																</td>
															</tr>
														{/if}
													{/for}
											{else}
												{if isset($fields_value['field_names'][$input_name]) AND (count($fields_value['field_names'][$input_name]) > 1)
												AND !empty($fields_value['field_names'][$input_name][0])}
														{assign var=field_count value=count($fields_value['field_names'][$input_name])}
												{else}
													{assign var=field_count value=1}
												{/if}
												{for $i=0 to $field_count-1}
													{if isset($fields_value['field_names'][$input_name][$i]) || $i == 0}
														<!-- Multi downwards Normal Field -->
														<tr class="vert">
															<td class="mapping_field col-left">
																{$pretty_name[0]|escape:'htmlall':'UTF-8'}
																{if $i == $field_count-1}
																	<a style="float:right;" onclick="return addVert(this, false);" class="button btn btn-default" intercom-tracked="more_fields_clicked">More&nbsp;&darr;&nbsp;</a>
																{/if}
															</td>
															<td class="source_field">
																<select name="field_names[{$input_name|escape:'htmlall':'UTF-8'}][]">
																	<option value=''>{l s='None' mod='totalimportpro'}</option>
																	{foreach $input.feed_fields as $field}
																		<option value="{$field|escape:'htmlall':'UTF-8'}"
																			{if isset($fields_value['field_names'][$input_name][$i]) AND $fields_value['field_names'][$input_name][$i] == $field}
																				selected="true"
																			{/if}
																			>{$field|escape:'htmlall':'UTF-8'}
																		</option>
																	{/foreach}
																</select>
															</td>
														</tr>
													{/if}
												{/for}
											{/if}
										{elseif $pretty_name[1] == 'cat'}
											{if in_array($input_name, $input.multi_language_fields)}
												<!-- Multi downwards Field -->

												{* Default number of Categories *}
												{assign var=field_count value=1}
												{* Figure out how many vertical rows of categories there are *}
												{if !empty($fields_value['field_names'][$input_name])}
													{* First key should be default language *}
													{assign var=first_cat_lang value = $fields_value['field_names'][$input_name]|@key}
													{if count($fields_value['field_names'][$input_name][$first_cat_lang]) > 1}
														{assign var=field_count value=count($fields_value['field_names'][$input_name][$first_cat_lang])}
													{/if}
												{/if}

												{for $i=0 to $field_count-1}
													<!--Multilanguage Field-->
													<tr class="hori vert">
														<td class="mapping_field col-left">
															{$pretty_name[0]|escape:'htmlall':'UTF-8'}
															{if $i+1 == $field_count}
																<a style="float:right;" onclick="return addVert(this, true);" class="button btn btn-default" intercom-tracked="more_fields_clicked">Add Category&nbsp;&darr;&nbsp;</a>
															{/if}
														</td>
														{* Default number of sub-categories *}
														{assign var=cat_count value=1}
														{* Figure out how many sub categories there are *}
														{if !empty($fields_value['field_names'][$input_name])}
															{assign var=cat_count value=count($fields_value['field_names'][$input_name][$first_cat_lang][$i])}
														{/if}
														<td class="source_field multi-lang">
															{for $j=0 to $cat_count-1}
																{assign var=lang_mapped value=false}
																{foreach $input.languages as $lang}
																	{if !empty($fields_value['field_names'][$input_name][$lang['id_lang']][$i][$j])}
																		{assign var=lang_mapped value=true}
																		{break}
																	{/if}
																{/foreach}
																{if $lang_mapped || $j == 0}
																<div class="lang-select-group">
																{foreach $input.languages as $lang name=LangLoop}
																	<select {if !$smarty.foreach.LangLoop.first}style="display:none;"{/if} class="lang-select lang-{$lang['id_lang']|escape:'htmlall':'UTF-8'}"name="field_names[{$input_name|escape:'htmlall':'UTF-8'}][{$lang['id_lang']|escape:'htmlall':'UTF-8'}][{$i|escape:'htmlall':'UTF-8'}][]">
																	<option value=''>{l s='None'|escape:'htmlall':'UTF-8' mod='totalimportpro'}</option>
																	{foreach $input.feed_fields as $field}
																		<option value="{$field|escape:'htmlall':'UTF-8'}"
																			{if isset($fields_value['field_names'][$input_name][$lang['id_lang']][$i][$j]) AND $fields_value['field_names'][$input_name][$lang['id_lang']][$i][$j] == $field}
																				selected="true"
																			{/if}
																			>{$field|escape:'htmlall':'UTF-8'}
																		</option>
																	{/foreach}
																	</select>
																{/foreach}
																<!--Language choice dropdown-->
																<span class="dropdown">
																   <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" tabindex="-1" intercom-tracked="lang-change">
																   {$input.languages[0]['iso_code']|escape:'htmlall':'UTF-8'}<span class="caret"></span>
																	</button>
																	<ul class="dropdown-menu">
																		{foreach $input.languages as $langdrop}
																		<li>
																			<a onclick="hideOtherLanguages($(this), {$langdrop['id_lang']|escape:'htmlall':'UTF-8'}, '{$langdrop['iso_code']|escape:'htmlall':'UTF-8'}')">{$langdrop['name']|escape:'htmlall':'UTF-8'}</a>
																		</li>
																		{/foreach}
																	</ul>
																</span>
																</div>
																{/if}
																{if $j+1 == $cat_count}
																	&nbsp;<a onclick="return addSub(this);" class="button btn btn-default" intercom-tracked="more_fields_clicked"><span>Add Sub-Category&nbsp;&rarr;&nbsp;</span></a>
																{/if}
															{/for}
														</td>
													</tr>
												{/for}
											{/if}
										{/if}
									{/if}
								{/foreach}
							</table>
						</div>
					{/foreach}
				</td>
			</tr>
			</table>
		{else}
			{$smarty.block.parent}
		{/if}
		{/block}
	{elseif $field.step == 'step5'}
		{block name="field"}
			{if $input.type == 'import_select'}
				<div class="{if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{/if}">
					<label class="control-label col-lg-3">{$input.title|escape:'htmlall':'UTF-8'}</label>
					<div class="margin-form col-lg-9">
						<select class="fixed-width-xl" name="{$input.name|escape:'htmlall':'UTF-8'}" id="{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}">
								{foreach $input.options.query AS $option}
									<option value="{$option.value|escape:'htmlall':'UTF-8'}"
											{if $fields_value[$input.name] == $option.value}
												selected="selected"
											{/if}
											>{$option.label|escape:'htmlall':'UTF-8'}</option>
								{/foreach}
						</select>
						{if isset($input.desc)}
							<p class="help-block">{$input.desc|escape:'htmlall':'UTF-8'}</p>
						{/if}
					</div>
				 </div>
			{elseif $input.type == 'range'}
				<div class="{if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{/if}">
					<label class="control-label col-lg-3">{$input.title|escape:'htmlall':'UTF-8'}</label>
					<div class="{if $smarty.const._PS_VERSION_ >= '1.6'}col-lg-9{else}margin-form range-container{/if}">
						<label class="control-label" {if isset($input.class)}class="{$input.class|escape:'htmlall':'UTF-8'}"{/if} for="{$input.all.id|escape:'htmlall':'UTF-8'}">
						<input type="radio" name="{$input.name|escape:'htmlall':'UTF-8'}" id="{$input.all.id|escape:'htmlall':'UTF-8'}" onclick="javascript:enableRange(false);" value="{$input.all.value|escape:'htmlall':'UTF-8'}"
								{if isset($fields_value[$input.name]) && $fields_value[$input.name] == 'all'} checked="checked"{/if}/>
							{$input.all.label|escape:'htmlall':'UTF-8'}
						</label>
						<label class="control-label" {if isset($input.class)}class="{$input.class|escape:'htmlall':'UTF-8'}"{/if} for="{$input.partial.id|escape:'htmlall':'UTF-8'}">
						<input type="radio" name="{$input.name|escape:'htmlall':'UTF-8'}" id="{$input.partial.id|escape:'htmlall':'UTF-8'}" onclick="javascript:enableRange(true);" value="{$input.partial.value|escape:'htmlall':'UTF-8'}"
							   {if isset($fields_value[$input.name]) && $fields_value[$input.name] == 'partial'}checked="checked"{/if}/>
							{$input.partial.label|escape:'htmlall':'UTF-8'}
							{assign var=values value=$input.partial.values}
						</label>
						<span id="display_import_range">
							{if isset($values.start.prepend)}{$values.start.prepend|escape:'htmlall':'UTF-8'}{/if}
							<input class="range_input" type="text" name="{$values.start.name|escape:'htmlall':'UTF-8'}" size="5" id="{$values.start.id|escape:'htmlall':'UTF-8'}" value="{$fields_value[$values.start.name]|escape:'htmlall':'UTF-8'}"/>
							{if isset($values.end.prepend)}{$values.end.prepend|escape:'htmlall':'UTF-8'}{/if}
							<input class="range_input" type="text" name="{$values.end.name|escape:'htmlall':'UTF-8'}" size="5" id="{$values.end.id|escape:'htmlall':'UTF-8'}" value="{$fields_value[$values.end.name]|escape:'htmlall':'UTF-8'}"/>
						</span>
						{if isset($input.desc)}
							<p class="help-block">{$input.desc|escape:'htmlall':'UTF-8'}</p>
						{/if}
					</div>
				</div>
				<div id="settings_profile" title="Settings Profile" style="display:none;">
					<p>{l s='Enter a name to save a new settings profile:' mod='totalimportpro'}</p>
					<p><input type="text" name="save_settings_name" id="save_settings_name" value=""></p>
				</div>
			{else}
				{$smarty.block.parent}
			{/if}
		{/block}
	{else}
		{$smarty.block.parent}
	{/if}
