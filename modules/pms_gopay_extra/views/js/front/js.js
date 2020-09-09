
/** ########################################################################### * 
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
 * ########################################################################### **//* js  pms_gopay_extra  */


function inlineFunction(url, form) {
	$("button.payment-overlay").attr('disabled', true);
	
		//console.log(url);
		//console.log(datas);
	$.ajax({
		url: url,
		type: 'POST',
		dataType: 'json',
		data: form,
		success: function(data){
			if(!data.errors)
			{
				_gopay.checkout({
					gatewayUrl: data.url,
					inline: true
				});

				$('#gopay-payment-iframe').on('load', function(){
					$('#loadGoPay').hide();
				});
			} else {
			if (!!$.prototype.fancybox)
				$.fancybox.open([
					{
						type: 'inline',
						autoScale: true,
						minHeight: 30,
						content: '<div class="error alert alert-danger"><p class="fancybox-error">' + data.errors + '</p></div>'
					}
				], {
					padding: 0
				});
			else
 				alert('Chyba: ' + data.errors);
			}
		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			if (XMLHttpRequest.readyState == 0 || (XMLHttpRequest.readyState === 4 && XMLHttpRequest.status === 403 && XMLHttpRequest.statusText === 'Forbidden')) {
				location.reload();
				return false;
			}
			if (XMLHttpRequest.status != 0) {
				if (!!$.prototype.fancybox)
					$.fancybox.open([{
						type: 'inline',
						autoScale: true,
						minHeight: 30,
						content: '<p class="fancybox-error">' + XMLHttpRequest.status + '</p><p class="fancybox-error">' + XMLHttpRequest.statusText + '</p><p class="fancybox-error">' + XMLHttpRequest.responseText + '</p>'
					}], {
						padding: 0
					});
				else
					alert('Chyba: ' + XMLHttpRequest.status + " / " + XMLHttpRequest.statusText + " / " + XMLHttpRequest.responseText + "<br>");
			}
		}
	});
}

function display_payment_desc()
{
	$('._payment_desc').each( function(index, elem) {
		//console.log(this);
		var ID = $(this).data('id');
		var value = $(this).data('value');

		value = value.replace(/</g, "<").replace(/>/g, ">").replace(/"/g, "\"").replace("\/", "/");
		$('#' + ID).html(value);
	});
}


function cancelRecurrent(id_order, id_session)
{
	$.ajax({
		url: "../modules/" + pms_gopay_extra + "/ajax.php",
		type:"POST",
		dataType: "json",
		data : {
				submit_cancel_recurrent	: 1,
				id_order				: id_order,
				id_session				: id_session
		},
		success: function(data){
			if(data.errors == 1)
			{
				$("#error_" + id_order).html(data.message);
				$("#error_" + id_order).fadeIn(200).show();
					$("#error_" + id_order).delay(3000).slideUp();
			} else
			{
				$("#cancel_recurr_" + id_order).hide();
				$("#conf_" + id_order).fadeIn(200).show();
					$("#conf_" + id_order).delay(9000).slideUp();
			}
		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			if (XMLHttpRequest.readyState == 0 || (XMLHttpRequest.readyState === 4 && XMLHttpRequest.status === 403 && XMLHttpRequest.statusText === 'Forbidden')) {
				location.reload();
				return false;
			}
			if (XMLHttpRequest.status != 0) {
				if (!!$.prototype.fancybox)
					$.fancybox.open([{
						type: 'inline',
						autoScale: true,
						minHeight: 30,
						content: '<p class="fancybox-error">' + XMLHttpRequest.status + '</p><p class="fancybox-error">' + XMLHttpRequest.statusText + '</p><p class="fancybox-error">' + XMLHttpRequest.responseText + '</p><p class="fancybox-error">' + JSON.stringify(params.data) + '</p>'
					}], {
						padding: 0
					});
				else
					alert('Chyba: ' + XMLHttpRequest.status + " / " + XMLHttpRequest.statusText + " / " + XMLHttpRequest.responseText + "<br>" + JSON.stringify(params.data));
			}
		}
	});
}

$(document).ready(function ()
{
	/* zobrazit popis platební metody v HTML */
	display_payment_desc();

	$('.payment-overlay').click(function (e)
	{
		if (typeof INLINE_MODE !== 'undefined' && INLINE_MODE == 1)
		{
			e.preventDefault();
		}

		$('body').append('<div id="loadGoPay" class="fancybox-overlay fancybox-overlay-fixed" style="overflow: auto; overflow-y: scroll; display:block;"><div id="loader"></div></div>');
		return true;
	});

	$('#gopay-payment-iframe').on('load', function(){
		$('#loadGoPay').hide();
	});

	/* 1.7 */
	/* inline platební brána */
	if (typeof INLINE_MODE !== 'undefined' && INLINE_MODE == 1)
	{
		var id_button = $('button .btn-primary');
		var id_checkbox = $('#conditions-to-approve input[type="checkbox"]');

		id_checkbox.click(function (checkbox)
		{
			$("#submit_payment button").attr('disabled', true);
			if (id_checkbox.is(":checked"))
				$("#submit_payment button").attr('disabled', false);
		});


		$('input[name="payment-option"]').click(function (e)
		{
			if (this.getAttribute('data-module-name') == pms_gopay_extra)
			{
				var h1 = document.getElementById("payment-confirmation");
				if (typeof h1 !== 'undefined' && h1)
					h1.setAttribute('id', 'submit_payment');
			} else
			{
				var h1 = document.getElementById("submit_payment");
				if (typeof h1 !== 'undefined' && h1)
					h1.setAttribute('id', 'payment-confirmation');
			}

			$("#submit_payment button").attr('disabled', true);
			if (id_checkbox.is(":checked"))
				$("#submit_payment button").attr('disabled', false);
		});

		$("#payment-confirmation").click(function (e)
		{
			e.preventDefault();
			this.disabled = true;
			$('body').append('<div id="loadGoPay" class="fancybox-overlay fancybox-overlay-fixed" style="overflow: auto; overflow-y: scroll; display:block;"><div id="loader"></div></div>');

			var input = $('input[name="payment-option"]:checked');
			if (input.attr('data-module-name') == pms_gopay_extra)
			{
				var form = $("#pay-with-" + input.attr('id') + "-form form");

				inlineFunction(form.attr("action"), form.serialize());
			}
		});
	}
});