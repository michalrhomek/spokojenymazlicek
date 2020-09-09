
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
{if $smarty.const._PS_VERSION_ >= 1.6}
	<div id="gopay_bill" class="order_detail_win">
		<div class="panel">
			<div class="">
				<img src="{$_MODULE_DIR}/views/img/logo-gopay.png" alt="" /> {l s='Order bill' mod='pms_gopay_extra'}
			</div>
			<div class="table-responsive">
{else}
	<br />
	<fieldset {if isset($ps_version) && ($ps_version < '1.5')}style="width: 400px"{/if}>
		<legend>
			<img src="{$base_url}modules/{$MODULE_NAME}/logo.gif" alt="" />{l s='Order bill' mod='pms_gopay_extra'}
		</legend>
{/if}
				<table class="detail_step_by_step table table-bordered">
					<tr>
						<td class="first_item"><b>{l s='fik:' mod='pms_gopay_extra'}</b></td>
						<td class="last_item">{$bill['fik']}</td>
					</tr>
					<tr>
						<td class="first_item"><b>{l s='bkp:' mod='pms_gopay_extra'}</b></td>
						<td class="last_item">{$bill['bkp']}</td>
					</tr>
					<tr>
						<td class="first_item"><b>{l s='pkp:' mod='pms_gopay_extra'}</b></td>
						<td class="last_item" style="word-break: break-all"><span style="max-width: 90%">{$bill['pkp']}</span></td>
					</tr>
				</table>
{if $smarty.const._PS_VERSION_ >= 1.6}
			</div>
		</div>
	</div>
{else}
	</fieldset>
{/if}
