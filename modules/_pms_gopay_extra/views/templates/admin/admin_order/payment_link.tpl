
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
	<div id="pms_gopay_extra_link" class="order_detail_win">
		<div class="panel">
			<div class="panel-heading">
				<img src="{$_MODULE_DIR}/logo.png" alt="" width="20" /> {l s='Repeat payment Gopay for this order' mod='pms_gopay_extra'}
			</div>
{else}
	<br />
	<fieldset {if $smarty.const._PS_VERSION_ < 1.5}style="width: 400px"{/if}>
		<legend>
			<img src="{$_MODULE_DIR}/logo.gif" alt="" />{l s='Repeat payment Gopay for this order' mod='pms_gopay_extra'}
		</legend>
{/if}
			<button class="button btn btn-default pull-left" id="paymentDebug">
				{l s='Debug payment' mod='pms_gopay_extra'}
			</button>
			<button class="button btn btn-default pull-right" id="paymentCopy">
				<img src="{$_MODULE_DIR}/views/img/admin/clippy.svg" width="13" alt="{l s='Copy to clipboard' mod='pms_gopay_extra'}">
				{l s='Copy to clipboard' mod='pms_gopay_extra'}
			</button>
			<div class="well hidden-print">
				<textarea id="paymentLink" style="display: inline-block; margin-top: 15px;" rows="5">{$paymentLink}</textarea>
			</div>
			<div class="well hidden-print">
				<center>
					<a class="btn btn-primary" id="paymentHref" href="{$paymentLink}" target="new">
						{l s='Repeat payment Gopay' mod='pms_gopay_extra'}
					</a>
				</center>
			</div>
{if $smarty.const._PS_VERSION_ >= 1.6}
		</div>
	</div>
{else}
	</fieldset>
{/if}


				<script>
					function copyText() {
					  var copyText = document.querySelector("#paymentLink");
					  copyText.select();
					  document.execCommand("Copy");
					}
					function debugText() {
						var debugText = $("#paymentLink");
						var href = document.getElementById('paymentHref');
						debugText.val(debugText.val() + "&debug=1");
						href.setAttribute("href", debugText.val());
					}

					document.querySelector("#paymentCopy").addEventListener("click", copyText);
					document.querySelector("#paymentDebug").addEventListener("click", debugText);
				</script>