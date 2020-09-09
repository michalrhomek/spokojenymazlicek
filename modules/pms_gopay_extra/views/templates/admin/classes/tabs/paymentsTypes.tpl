
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
 * ########################################################################### **}<div class="col-lg-4">
	<div class="radio ">
		<label>
			<input type="radio" name="_BUTTONS_MODE" id="shortened" value="1" {if $_BUTTONS_MODE == 1}checked="checked"{/if}>
			{l s='Shortened template' mod='pms_gopay_extra'}<br>
			<img style="margin-top: 8px;" src="../modules/{$moduleName}/views/img/tabs/Shortened.png" alt="">
		</label>
	</div>
</div>
<div class="col-lg-4">
	<div class="radio ">
		<label>
			<input type="radio" name="_BUTTONS_MODE" id="extended" value="2" {if $_BUTTONS_MODE == 2}checked="checked"{/if} {if !$fullMode}disabled="disabled"{/if}>
			{l s='Extended template' mod='pms_gopay_extra'}<br>
			<img style="margin-top: 8px;" src="../modules/{$moduleName}/views/img/tabs/Extended.gif" alt=""></label>
	</div>
</div>
<div class="col-lg-4">
	<div class="radio ">
		<label>
			<input type="radio" name="_BUTTONS_MODE" id="window" value="3" {if $_BUTTONS_MODE == 3}checked="checked"{/if} {if !$fullMode}disabled="disabled"{/if}>
			{l s='Windowed template' mod='pms_gopay_extra'}<br>
			<img style="margin-top: 8px;" src="../modules/{$moduleName}/views/img/tabs/Window.gif" alt=""></label>
	</div>
</div>
<p class="help-block">
	{l s='Shortened template - choosing of payment method is done through one universal button. The payment method is selected only on the payment gateway page.' mod='pms_gopay_extra'}<br>
	{l s='Extended template - choosing of payment method is done through several paying methods which you allowed. These methods are displayed as separate buttons. ' mod='pms_gopay_extra'}<br>
	{l s='Windowed template - choosing of payment method is done through several paying methods which you allowed. These methods are grouped by type (same as the Extended template, but with the different style).' mod='pms_gopay_extra'}<br>
	<span style="color:red">
		{l s='If you use a test mode an only Shortened template is available. After switching to normal mode, all types of payments will be available that you have enabled at GoPay company.' mod='pms_gopay_extra'}
	</span>
</p>
