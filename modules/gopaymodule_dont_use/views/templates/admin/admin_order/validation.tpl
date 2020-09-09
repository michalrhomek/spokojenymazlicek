
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
	<div id="gopay_preauthorized" class="order_detail_win">
		<div class="panel">
			<div class="panel-heading">
				<img src="{$_MODULE_DIR}/logo.png" alt="" width="16" /> {l s='GoPay Capture Preauthorized Payment' mod='pms_gopay_extra'}
			</div>
{else}
	<br />
	<fieldset {if isset($ps_version) && ($ps_version < '1.5')}style="width: 400px"{/if}>
		<legend><img src="{$_MODULE_DIR}/logo.png" alt="" width="16" />{l s='GoPay Capture Preauthorized Payment' mod='pms_gopay_extra'}</legend>
{/if}
			{if isset($paymentErrors) && is_array($paymentErrors)}
				<div class="module_error alert alert-danger">
					<button type="button" class="close" data-dismiss="alert">×</button>
					{foreach $paymentErrors as $error}
						{l s='Error:' mod='pms_gopay_extra'} {$error->error_code}{if $error->error_name} - {$error->error_name}<br />{/if}
						{if isset($error->message)}{$error->message}<br />{/if}
						{if isset($error->description)}{$error->description}{/if}
					{/foreach}
				</div>
			{/if}
			<form method="post" action="{$smarty.server.REQUEST_URI|escape:'htmlall':'UTF-8'}">
				<input type="hidden" name="id_order" value="{$order->id}" />
				<div class="well hidden-print col-lg-12">
					<button type="submit" class="btn btn-default pull-left" name="submitCancelPreauthorized">
						<i class="icon-remove"></i>
						{l s='Cancel Payment' mod='pms_gopay_extra'}
					</button>
					<button type="submit" class="btn btn-default pull-right" name="submitCapturePreauthorized">
						<i class="icon-check-square-o"></i>
						{l s='Capture Payment' mod='pms_gopay_extra'}
					</button>
				</div>
				<p><b>{l s='Information:' mod='pms_gopay_extra'}</b> {l s='Payment is ready for approval or rejection, select one of the variants.' mod='pms_gopay_extra'}</p>
			</form>
{if $smarty.const._PS_VERSION_ >= 1.6}
		</div>
	</div>
{else}
	</fieldset>
{/if}


