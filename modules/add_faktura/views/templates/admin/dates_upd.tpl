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
<script type="text/javascript" src="../js/jquery/ui/i18n/jquery.ui.datepicker-cs.js"></script>
<script type="text/javascript" src="../js/jquery/ui/jquery.ui.datepicker.min.js"></script>
<link href="../js/jquery/ui/themes/base/jquery.ui.datepicker.css" rel="stylesheet" type="text/css" media="all"/>

<script type="text/javascript">
	var id_order = '{$order->id}';

	$(document).ready(function() {
		if ($("form#date_updates_form .datepicker").length > 0)
			$("form#date_updates_form .datepicker").datepicker({
								prevText: "",
								nextText: "",
								dateFormat: "yy-mm-dd"
		});
	});
</script>

{if $version_1_6}
<div id="date_updates">
{/if}

<form action="../modules/add_faktura/ajax.php" method="post" id="date_updates_form" style="width:100%">
	<fieldset>
		<legend><img src="../img/admin/date.png" alt="" title="" />{l s='Editing the Date Orders' mod='add_faktura'}</legend>
		<div class="alert alert-success conf_mk" style="display:none">{l s='Dates have been update' mod='add_faktura'}</div>
		<div class="alert alert-success conf_due" style="display:none">{l s='Maturity updated' mod='add_faktura'}</div>
		<div class="alert alert-info warn_mk" style="display:none">{l s='He was selected a new date' mod='add_faktura'}</div>
		<div class="alert alert-error error_mk" style="display:none">{l s='Error saving' mod='add_faktura'}</div>
		<p><label>{l s='Maturity order: ' mod='add_faktura'}</label>
			<input type="text" name="date_due" value="{$date_due}" class="date_due" style="width:35px">
			<input id="submit_date_due" type="submit" class="button btn btn-default" name="submit_date_due" value="{l s='Save maturity' mod='add_faktura'}" />
		</p>
		<p><label>{l s='Creation date of the order: ' mod='add_faktura'}</label>
			<input type="text" name="datum" value="{$order->date_add}" class="datepicker add" style="width:140px">
		</p>
		{if $order->invoice_date != '0000-00-00 00:00:00'}
		<p><label>{l s='Date of invoice: ' mod='add_faktura'}</label>
			<input type="text" name="datum_inv" value="{$order->invoice_date}" class="datepicker inv" style="width:140px">
		</p>
		{/if}
		{if $order->delivery_date != '0000-00-00 00:00:00'}
		<p><label>{l s='Date of delivery note: ' mod='add_faktura'}</label>
			<input type="text" name="datum_dlv" value="{$order->delivery_date}" class="datepicker dlv" style="width:140px">
		</p>
		{/if}
		<center><input id="submit_date" type="submit" class="button btn btn-default" name="submit_date" value="{l s='Save the dates' mod='add_faktura'}" /></center>
	</fieldset>
</form>
{if $version_1_6}
</div>
{/if}