
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
<div class="bootstrap">
	<div class="tab-head">
		<div class="page-bar toolbarBox">
			<div class="btn-toolbar">
				<a href="#" class="toolbar_btn dropdown-toolbar navbar-toggle" data-toggle="collapse" data-target="#toolbar-nav">
					<i class="process-icon-dropdown"></i>
					<div>{l s='Menu' mod='pms_service_module'}</div>
				</a>
				<ul id="toolbar-nav" class="nav nav-pills pull-right collapse navbar-collapse">
				{foreach from=$toolbar_btn item=btn key=k}
					{if $k != 'back' && $k != 'modules-list'}
						<li>
							<a id="page-header-desc-{$table}-{if isset($btn.imgclass)}{$btn.imgclass|escape}{else}{$k}{/if}"
								class="toolbar_btn {if isset($btn.target) && $btn.target} _blank{/if} pointer"
								{if isset($btn.href)} href="{$btn.href|escape}"{/if}
								title="{if isset($btn.help)}{$btn.help}{else}{$btn.desc|escape}{/if}"
								{if isset($btn.js) && $btn.js} onclick="{$btn.js}"{/if}
								{if isset($btn.modal_target) && $btn.modal_target} data-target="{$btn.modal_target}" data-toggle="modal"{/if}
								{if isset($btn.help)} data-toggle="tooltip" data-placement="bottom"{/if}
							>
								<i class="{if isset($btn.icon)}{$btn.icon|escape}{else}process-icon-{if isset($btn.imgclass)}{$btn.imgclass|escape}{else}{$k}{/if}{/if}{if isset($btn.class)} {$btn.class|escape}{/if}"></i>
								<div {if isset($btn.force_desc) && $btn.force_desc == true }class="locked"{/if}>{$btn.desc|escape}</div>
							</a>
						</li>
					{/if}
				{/foreach}
				</ul>
			</div>
		</div>
	</div>
</div>
{if isset($question_breadcrumb)}
	<ul class="breadcrumb cat_bar">
		{$question_breadcrumb}
	</ul>
{/if}
