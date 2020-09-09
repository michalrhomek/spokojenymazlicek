
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
<div class="alert alert-info col-lg-12 {if $smarty.const._PS_VERSION_ < 1.6}info-background info-border{/if}">
<div class="row {if $smarty.const._PS_VERSION_ < 1.6}info-background{/if}">
	<div class="col-xs-12 col-sm-6 col-md-8 col-lg-8">
		<p><strong>{l s="This module allows payments via the GoPay payment gateway" mod='pms_gopay_extra'}</strong></p>
		<ul>
			<li>{l s="The payment gateway can be used for pay by payment card and fast bank transfer." mod='pms_gopay_extra'}</li>
			<li>{l s="The module uses the REST API to communicate with the GoPay payment gateway. Payments can be made directly in your store without redirecting to another page." mod='pms_gopay_extra'}</li>
			<li>{l s="The module offers an enhanced test mode where it can be used but the customer does not see it." mod='pms_gopay_extra'}</li>
			<li>{l s="Advanced options for setting payment buttons will allow you to fine-tune payment methods." mod='pms_gopay_extra'}</li>
		</ul>
	</div>
	<div class="col-xs-12 col-sm-4 col-md-3 col-lg-3" id="productCommentsBlock">
		{if $comment_grade && $comment_grade < 6}
			<div class="comment_author" > 
				<p><strong>{l s='Your module rating' mod='pms_gopay_extra'}</strong></p>
				<div class="star_content" itemprop="reviewRating" itemscope itemtype="https://schema.org/Rating"> {section name="i" start=0 loop=5 step=1}
					{if $comment_grade le $smarty.section.i.index}
					<div class="star"></div>
					{else}
					<div class="star star_on"></div>
					{/if}
					{/section}
					<meta itemprop="worstRating" content = "0" />
					<meta itemprop="ratingValue" content = "{$comment_grade}" />
					<meta itemprop="bestRating" content = "5" />
				</div>
			</div>
		{/if}
	</div>
	<div class="col-xs-12 col-sm-2 col-md-1 col-lg-1 text-right">
		<img src="../modules/pms_gopay_extra/logo.png" style="float:right; margin-left:15px;" height="60">
	</div>
	</div>
</div>
