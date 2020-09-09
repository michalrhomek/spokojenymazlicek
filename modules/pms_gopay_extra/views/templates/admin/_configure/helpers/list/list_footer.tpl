
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
	
	</table>
</div>
<div class="row">
	<div class="col-lg-6">
		{if $bulk_actions && $has_bulk_actions}
		<div class="btn-group bulk-actions dropup">
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
				{l s='Bulk actions' mod='pms_gopay_extra'} <span class="caret"></span>
			</button>
			<ul class="dropdown-menu">
				<li>
					<a href="#" onclick="javascript:checkDelBoxes($(this).closest('form').get(0), '{$list_id}Box[]', true);return false;">
						<i class="icon-check-sign"></i>&nbsp;{l s='Select all' mod='pms_gopay_extra'}
					</a>
				</li>
				<li>
					<a href="#" onclick="javascript:checkDelBoxes($(this).closest('form').get(0), '{$list_id}Box[]', false);return false;">
						<i class="icon-check-empty"></i>&nbsp;{l s='Deselect all' mod='pms_gopay_extra'}
					</a>
				</li>
				<li class="divider"></li>
				{foreach $bulk_actions as $key => $params}
					<li{if $params.text == 'divider'} class="divider"{/if}>
						{if $params.text != 'divider'}
						<a href="#" onclick="{if isset($params.confirm)}if (confirm('{$params.confirm}')){/if}sendBulkAction($(this).closest('form').get(0), 'submitBulk{$key}{$table}');">
							{if isset($params.icon)}<i class="{$params.icon}"></i>{/if}&nbsp;{$params.text}
						</a>
						{/if}
					</li>
				{/foreach}
			</ul>
		</div>
		{/if}
	</div>
	{if !$simple_header && isset($list_total) && $list_total > $pagination[0]}
	<div class="col-lg-6">
		{* Choose number of results per page *}
		<div class="pagination">
			{l s='Display' mod='pms_gopay_extra'}
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
				{$selected_pagination}
				<i class="icon-caret-down"></i>
			</button>
			<ul class="dropdown-menu">
			{foreach $pagination AS $value}
				<li>
					<a href="javascript:void(0);" class="pagination-items-page" data-items="{$value|intval}" data-list-id="{$list_id}">{$value}</a>
				</li>
			{/foreach}
			</ul>
			/ {$list_total} {l s='result(s)' mod='pms_gopay_extra'}
			<input type="hidden" id="{$list_id}-pagination-items-page" name="{$list_id}_pagination" value="{$selected_pagination|intval}" />
		</div>
		<script type="text/javascript">
			$('.pagination-items-page').on('click',function(e){
				e.preventDefault();
				$('#'+$(this).data("list-id")+'-pagination-items-page').val($(this).data("items")).closest("form").submit();
			});
		</script>
		<ul class="pagination pull-right">
			<li {if $page <= 1}class="disabled"{/if}>
				<a href="javascript:void(0);" class="pagination-link" data-page="1" data-list-id="{$list_id}">
					<i class="icon-double-angle-left"></i>
				</a>
			</li>
			<li {if $page <= 1}class="disabled"{/if}>
				<a href="javascript:void(0);" class="pagination-link" data-page="{$page - 1}" data-list-id="{$list_id}">
					<i class="icon-angle-left"></i>
				</a>
			</li>
			{assign p 0}
			{while $p++ < $total_pages}
				{if $p < $page-2}
					<li class="disabled">
						<a href="javascript:void(0);">&hellip;</a>
					</li>
					{assign p $page-3}
				{elseif $p > $page+2}
					<li class="disabled">
						<a href="javascript:void(0);">&hellip;</a>
					</li>
					{assign p $total_pages}
				{else}
					<li {if $p == $page}class="active"{/if}>
						<a href="javascript:void(0);" class="pagination-link" data-page="{$p}" data-list-id="{$list_id}">{$p}</a>
					</li>
				{/if}
			{/while}
			<li {if $page >= $total_pages}class="disabled"{/if}>
				<a href="javascript:void(0);" class="pagination-link" data-page="{$page + 1}" data-list-id="{$list_id}">
					<i class="icon-angle-right"></i>
				</a>
			</li>
			<li {if $page >= $total_pages}class="disabled"{/if}>
				<a href="javascript:void(0);" class="pagination-link" data-page="{$total_pages}" data-list-id="{$list_id}">
					<i class="icon-double-angle-right"></i>
				</a>
			</li>
		</ul>
		<script type="text/javascript">
			$('.pagination-link').on('click',function(e){
				e.preventDefault();

				if (!$(this).parent().hasClass('disabled'))
					$('#submitFilter'+$(this).data("list-id")).val($(this).data("page")).closest("form").submit();
			});
		</script>
	</div>
	{/if}
</div>
{block name="footer"}
{if isset($toolbar_btn)}
{foreach from=$toolbar_btn item=btn key=k}
	{if $k == 'back'}
		{assign 'back_button' $btn}
		{break}
	{/if}
{/foreach}
{/if}

<div class="panel-footer">
{if isset($back_button)}
	<a id="desc-{$table}-{if isset($back_button.imgclass)}{$back_button.imgclass}{else}{$k}{/if}" class="btn btn-default{if isset($back_button.target) && $back_button.target} _blank{/if}"{if isset($back_button.href)} href="{$back_button.href|escape:'html':'UTF-8'}"{/if}{if isset($back_button.js) && $back_button.js} onclick="{$back_button.js}"{/if}>
		<i class="process-icon-back {if isset($back_button.class)}{$back_button.class}{/if}" ></i> <span {if isset($back_button.force_desc) && $back_button.force_desc == true } class="locked" {/if}>{$back_button.desc}</span>
	</a>
{/if}

{*
* Edited by Miloslav  Kubín
*}

{if isset($toolbar_btn)}
{foreach from=$toolbar_btn item=btn key=k}
	{if isset($btn.main_btn) && $btn.main_btn}
		{if isset($btn.href) && trim($btn.href) != ''}
			<a href="{$btn.href}" {if isset($btn['id'])}id="{$btn['id']}"{/if} class="btn btn-default{if isset($btn['class'])} {$btn['class']}{/if}" {if isset($btn.js) && $btn.js} onclick="{$btn.js}"{/if}>{if isset($btn['icon'])}<i class="{$btn['icon']}" ></i> {/if}{$btn.desc}</a>
		{else}
			<button type="{if isset($btn['type'])}{$btn['type']}{else}button{/if}" {if isset($btn['id'])}id="{$btn['id']}"{/if} class="btn btn-default{if isset($btn['class'])} {$btn['class']}{/if}" name="{if isset($btn['name'])}{$btn['name']}{else}submitOptions{$table}{/if}"{if isset($btn.js) && $btn.js} onclick="{$btn.js}"{/if}>{if isset($btn['icon'])}<i class="{$btn['icon']}" ></i> {/if}{$btn.desc}</button>
		{/if}
	{/if}
{/foreach}
{/if}
{*
* Edited by Miloslav  Kubín
*}

</div>
{/block}
{if !$simple_header}
		<input type="hidden" name="token" value="{$token|escape:'html':'UTF-8'}" />
	</div>
{else}
	</div>
{/if}

{hook h='displayAdminListAfter'}
{if isset($name_controller)}
	{capture name=hookName assign=hookName}display{$name_controller|ucfirst}ListAfter{/capture}
	{hook h=$hookName}
{elseif isset($smarty.get.controller)}
	{capture name=hookName assign=hookName}display{$smarty.get.controller|ucfirst|htmlentities}ListAfter{/capture}
	{hook h=$hookName}
{/if}

{block name="endForm"}
</form>
{/block}

{block name="after"}{/block}
