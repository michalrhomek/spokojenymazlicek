
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
<div id="productCommentsBlock">
	<div class="tabs">
		<div class="container p-30">
			<div class="row">
				<div id="new_comment_form_ok" class="alert alert-success" style="display:none;padding:15px 25px"></div>
			</div>
			<div class="row">
				<h2 class="my_version">
					{l s='Write your review' mod='pms_gopay_extra'}
				</h2>
			</div>
			<div class="row">
				<p>{l s='Are you satisfied with our module? We are glad if you rate us and share your experience with other customers.' mod='pms_gopay_extra'}</p>
				
				<div class="hr-line"></div>
			</div>
			
			<div class="row">
				<form id="id_new_comment_form" action="#">
	
					<div class="col-md-12 mb-25" id="comments-stars">
						<p class="comments-title mb-0">
							{l s='Your module rating' mod='pms_gopay_extra'}
						</p>	
						
						<ul id="criterions_list" >
							<li>
								<div class="star_content">
									<input class="star" type="radio" name="criterion" value="1"/>
									<input class="star" type="radio" name="criterion" value="2"/>
									<input class="star" type="radio" name="criterion" value="3"/>
									<input class="star" type="radio" name="criterion" value="4"/>
									<input class="star" type="radio" name="criterion" value="5" checked="checked"/>
								</div>
							</li>
						</ul>
						<br>
					</div>
					

					<div class="col-md-12">
						<p class="comments-title mb-0">
							{l s='Review title' mod='pms_gopay_extra'}
						</p>		
						<input class="comments-input" id="comment_title" name="title" type="text" value=""/>										
					</div>
					<div class="col-md-12">
						<p class="comments-title mb-0">
							{l s='Your review' mod='pms_gopay_extra'}
						</p>		
						<textarea class="comments-input" id="content" name="content" rows="4"></textarea>								
					</div>
					<div class="col-md-12">
						<p class="comments-title mb-0">
							{l s='Your name' mod='pms_gopay_extra'}
						</p>		
						<input class="comments-input" id="commentCustomerName" name="customer_name" type="text" value="{$shopURL}"/>	
					</div>
					
	
			
					<div class="col-md-12">
						<div id="new_comment_form_footer">
							<div id="new_comment_form_error" class="error" style="display:none;">
								<ul></ul>
							</div>
							<input id="id_product_comment_send" name="id_product" type="hidden" value='{$id_product_comment_form}'/>
							<p class="fr">
								<button class="mx-1 btn btn-primary" id="submitNewMessage" name="submitNewReview" type="submit">{l s='Send' mod='pms_gopay_extra'}</button>&nbsp;
								{l s='or' mod='pms_gopay_extra'}&nbsp;<a href="#" id="notAnymore">{l s='Do not show anymore' mod='pms_gopay_extra'}</a>
							</p>
						</div>
					</div>


				</form><!-- /end new_comment_form_content -->
			</div>
		</div>
	</div>
</div>
		
		{*
		<div id="product_comments_block_tab">
			<div id="new_comment_form">
				<form id="id_new_comment_form" action="#">
					<h2 class="title">{l s='Write your review' mod='pms_gopay_extra'}</h2>
						<div class="product clearfix">
							<div class="product_desc">
								{l s='Are you satisfied with our module? We are glad if you rate us and share your experience with other customers.' mod='pms_gopay_extra'}
							</div>
						</div>
					<div class="new_comment_form_content">
							
						
					</div>
				</form><!-- /end new_comment_form_content -->
			</div>
		</div>
	</div>
</div>*}