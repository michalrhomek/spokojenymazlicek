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

{if $version_1_6}
<div id="slip_updates">
{/if}

<form action="{$link->getModuleLink($module_name, 'ajax', [], false)}" method="post" id="slip_form" style="width:100%">
	<fieldset>
		<legend><img src="../img/admin/date.png" alt="" title="" />{l s='Credit note - adjustment of parameters' mod='add_faktura'}</legend>
		<div class="conf conf_slip" style="display:none">{l s='Dates have been update' mod='add_faktura'}</div>
		<div class="error error_slip" style="display:none">{l s='Error saving' mod='add_faktura'}</div>
		<p><label>{l s='Date due on order slip: ' mod='add_faktura'}</label>
			<input type="text" name="slipDate_due" value="{$slipDate_due}" class="slipDate_due" style="width:35px">
		</p>
		<p><label>{l s='Account number on order slip: ' mod='add_faktura'}</label>
			<input type="text" name="slipBankNumber" value="{$slipBankNumber}" class="slipBankNumber" style="width:140px">
		</p>
		<p><label>{l s='Custom text on order slip: ' mod='add_faktura'}</label>
			<textarea name="slipText" class="slipText" style="width:240px">{$slipText}</textarea>
		</p>
		<center><input id="submit_slip" type="submit" class="button" name="submit_slip" value="{l s='Save the dates' mod='add_faktura'}" /></center>
	</fieldset>
</form>
{if $version_1_6}
</div>
{/if}