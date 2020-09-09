
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
<h2 class="my_version">
	{$displayName} - <span style="color: #FF0000;">{l s='v.' mod='pms_template'}{$version}</span>
</h2>

<div id="core-module-page">
	<div class="bootstrap">
		<nav id="core-menu" role="navigation">
			<ul class="nav">

				{*  extends menu items  *}
				{if $REGISTER && $SHOW_SHOP && isset($tabs)}
					{foreach $tabs as $tab => $class}
						{assign var="show_li" value=false}
						{if is_object($class)}
							{if !$adminTab}
								{assign var="show_li" value=true}
							{else}
								{if $adminTab == $tab}
									{assign var="show_li" value=true}
								{/if}
							{/if}
						{/if}
						{if $show_li}
							<li class="maintab {if $idTab == $tab}active{/if}">
								<a href="#idTab_{$tab|escape:'htmlall':'UTF-8'}" data-toggle="tab">
									<i class='{if $class->getIcon()}{$class->getIcon()|escape:'htmlall':'UTF-8'}{else}icon-cogs{/if}'></i>
									<span>{$class->getTitle()|escape:'htmlall':'UTF-8'}</span>
								</a>
							</li>
						{/if}
					{/foreach}
				{/if}

				{*  static menu items  *}
				{if !$adminTab}
					{foreach $staticTabs as $staticTab => $class}
						{assign var="show_static_li" value=false}
						{if is_object($class)}
							{if $SHOW_SHOP}
								{assign var="show_static_li" value=true}
							{else}
								{if $staticTab != 'translations'}
									{assign var="show_static_li" value=true}
								{/if}
							{/if}
						{/if}
						{if $show_static_li}
							<li class="maintab {if $idTab == $staticTab || (!$idTab && $staticTab == 'register')}active{/if}">
								<a href="#idTab_{$staticTab|escape:'htmlall':'UTF-8'}" data-toggle="tab">
									<i class='{if $class->getIcon()}{$class->getIcon()|escape:'htmlall':'UTF-8'}{else}icon-cogs{/if}'></i>
									<span>{$class->getTitle()|escape:'htmlall':'UTF-8'}</span>
								</a>
							</li>
						{/if}
					{/foreach}
				{/if}
			</ul>
			<span class="text-center" id="core-menu-toggle">
				<i class="icon-align-justify icon-rotate-90"></i>
			</li>
		</nav>
	</div>

		<div id="core-content" class="bootstrap">
			<div class="row ">
				<div class="tab-content col-md-12">

					{*  extends menu tab contens  *}
					{if $REGISTER && $SHOW_SHOP && isset($tabs)}
						{foreach $tabs as $tab => $class}
							{if is_object($class)}
								<div class="tab-pane {if $idTab == $tab || ($idTab == '' && $tab == 'register')}active{/if}" id="idTab_{$tab|escape:'htmlall':'UTF-8'}">
									{if !$adminTab}
										{$class->showForm()}
									{else}{$adminTab} == {$tab}
										{if $adminTab == $tab}
											{$class->showForm()}
										{/if}
									{/if}
								</div>
							{/if}
						{/foreach}
					{/if}

					{*  static menu tab contens  *}
					{if !$adminTab}
						{foreach $staticTabs as $staticTab => $class}
							{if is_object($class)}
								<div class="tab-pane {if $idTab == $staticTab || ($idTab == '' && $staticTab == 'register')}active{/if}" id="idTab_{$staticTab|escape:'htmlall':'UTF-8'}">
									{if $SHOW_SHOP}
										{$class->showForm()}
									{else}
										{if $staticTab != 'translations' && $staticTab != 'register'}
											{$class->showForm()}
										{/if}
									{/if}
									{if $staticTab == 'register'}
										{include file="./licence.tpl"}
									{/if}
								</div>
							{/if}
						{/foreach}
					{/if}
				</div>
			</div>
		</div>
	</div>
</div>