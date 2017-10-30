{* ########################################################################### */
/*                                                                             */
/*                      Copyright 2014     Miloslav Kubín                      */
/*                        http://presta-modul.shopmk.cz                        */
/*                                                                             */
/*             Please do not change this text, remove the link,                */
/*          or remove all or any part of the creator copyright notice          */
/*                                                                             */
/*    Please also note that although you are allowed to make modifications     */
/*     for your own personal use, you may not distribute the original or       */
/*                 the modified code without permission.                       */
/*                                                                             */
/*                    SELLING AND REDISTRIBUTION IS FORBIDDEN!                 */
/*             Download is allowed only from presta-modul.shopmk.cz            */
/*                                                                             */
/*       This software is provided as is, without warranty of any kind.        */
/*           The author shall not be liable for damages of any kind.           */
/*               Use of this software indicates that you agree.                */
/*                                                                             */
/*                                    ***                                      */
/*                                                                             */
/*              Prosím, neměňte tento text, nemazejte odkazy,                  */
/*      neodstraňujte části a nebo celé oznámení těchto autorských práv        */
/*                                                                             */
/*     Prosím vezměte také na vědomí, že i když máte možnost provádět změny    */
/*        pro vlastní osobní potřebu,nesmíte distribuovat původní nebo         */
/*                        upravený kód bez povolení.                           */
/*                                                                             */
/*                   PRODEJ A DISTRIBUCE JE ZAKÁZÁNA!                          */
/*          Download je povolen pouze z presta-modul.shopmk.cz                 */
/*                                                                             */
/*   Tento software je poskytován tak, jak je, bez záruky jakéhokoli druhu.    */
/*          Autor nenese odpovědnost za škody jakéhokoliv druhu.               */
/*                  Používáním tohoto softwaru znamená,                        */
/*           že souhlasíte s výše uvedenými autorskými právy .                 */
/*                                                                             */
/* ########################################################################### *}
<table style="width:100%; background-color:#EEE">
		<tr>
			<td style="font-size:{$font_width+20}px; width:100%; text-align:center; line-height:5px;"></td>
		</tr>
	</table>

<table>
	<tr>
		<td style="text-align: center; font-size:{$font_width+6}pt; color: #444">
			{if $available_in_your_account}
				{l s='An electronic version of this invoice is available in your account. To access it, log in to our website using your e-mail address and password (which you created when placing your first order).'  mod='add_faktura'}			 
				<br />
			{/if}
			{$shop_address|escape:'htmlall':'UTF-8'}<br />

			{if !empty($shop_phone) OR !empty($shop_fax)}
				{l s='For more assistance, contact Support:'  mod='add_faktura'}<br />
				{if !empty($shop_phone)}
					Tel: {$shop_phone|escape:'htmlall':'UTF-8'}
				{/if}

				{if !empty($shop_fax)}
					Fax: {$shop_fax|escape:'htmlall':'UTF-8'}
				{/if}
				<br />
			{/if}
			
			{if isset($shop_details)}
				{$shop_details|escape:'htmlall':'UTF-8'}<br />
			{/if}


			{if isset($free_text)}
				{$free_text|escape:'htmlall':'UTF-8'}<br />
			{/if}
		</td>
	</tr>
</table>