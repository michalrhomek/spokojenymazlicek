
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
 * ########################################################################### **}{*
Přejmenováním tohoto souboru např. na 'BANK_ACCOUNT_infos.tpl' dojde k přepsání defaultního souboru 'payment_infos.tpl'  takto lze upravit popisy plateb vlastními texty pro každé platební tlačítko.

Kopírováním tohoto souboru a následným přjmenováním lze takto nastavit popisy pro všechny platební metody

Kódy platebních metod najdete na adrese https://doc.gopay.com/cs/?shell#payment_instrument
*}

<section class="{$_PMS_MODULE->name}">
	{if $payment_type.logo}
		<div class="image" style=" float:right;"><img style="max-width:120px;" src="{$payment_type.logo}"></div>
	{/if}
	<h3>{$payment_type.payment_name nofilter}</h3>
	<div id="{$payment_type.payment_code}"></div>
	<p>
		{if $payment_type.price > 0 && $PRICE_VIEW}
			<span class="price_desc">{l s='Surcharge' mod='pms_gopay_extra'}</span>
			<span class="price">
				{$payment_type.price}
			</span></p>
		{/if}
	</p>
	<div>{$payment_type.payment_desc nofilter}</div>
</section>
