
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
 * ########################################################################### **/
//  for  debuging  function
//console.log(print_r(data.data));

$(document).ready(function() {
	initTablePayments();

	if (typeof missingPayments !== 'undefined' && missingPayments)
		$('a[href="#idTab_paymentssettings"]').append('<i class="icon-warning warning_payment"></i>')

	$('button[name="submitAddAttachments"]').click(function(e) {
		$('#logo_input_' + this.getAttribute('data-id_lang')).trigger('click');
	});

	$('button[name="submitImportPayments"]').click(function(e) {
		$('.tab-content').append('<div class="action-overlay" style="display: none;"><div id="loader"></div></div>');
		$('.tab-content div.action-overlay').fadeIn('slow');
	});

	$("input[id*='logo_input_']").each(function(e) {
		$(this).change(function(e) {			
			var id_lang = this.getAttribute('data-id_lang');
			var payment_logo = $('#payment_logo_' + id_lang);
			var attachement_filename = $('#logo_name_' + id_lang);

			var name  = '';
			if ($(this)[0].files !== undefined)
			{
				var files = $(this)[0].files;
				var uploaded_logo = files[0].name;

				$.each(files, function(index, value) {
					name += value.name+', ';
				});

				// název nahraného souboru
				attachement_filename.val(name.slice(0, -2));

				// název uložený v DB
				payment_logo.val(id_lang + '_' + $('#_PAYMENT_CODE').val() + '.' + uploaded_logo.split('.').pop());
			}
			else // Internet Explorer 9 Compatibility
			{
				// nutné upravit dle výše použitého i pro IE
				name = $(this).val().split(/[\\/]/);
				attachement_filename.val(name[name.length-1]);
			}
			
		});
	});
});

function objToStringQ(obj) {
    var str = '';
    for (var p in obj) {
        if (obj.hasOwnProperty(p)) {
            str += p + '=' + obj[p] + '&';
        }
    }
    return str;
}

function removeLogo(id_lang)
{
	if (confirm(confirmDelete))
	{
		$('#logo_img_' + id_lang).attr('src', '../img/404.gif');
		$('#logo_name_' + id_lang).val('');
		$('#payment_logo_' + id_lang).val('-');
		$('#logo_remove_' + id_lang).hide();
	}
}

function uploadLogo (id_lang){
	var data = {
		ajaxType: 'ajaxFileUpload',
		action: 'uploadLogo',
		tabName: 'paymentssettings',
		tabFolder: 'classes/tabs/',
		dataType: 'json',
		fileElementId: 'logo_input_' + id_lang,
		id_lang: id_lang,
		payment_code: $('#_PAYMENT_CODE').val(),
		processData: false
	};

	var _json = {
		data: data,
		beforeSend: function () {
			$('.tab-content').append('<div class="action-overlay" style="display: none;"><div id="loader"></div></div>');
			$('.tab-content div.action-overlay').fadeIn('slow');
		},
		success: function(data) {
			if (data.message_code === 0) {
				$('#logo_img_' + id_lang).attr('src', data.logo_url);
				$('#logo_remove_' + id_lang).show();
			}
		},
		complete: function () {
			$('.tab-content div.action-overlay').fadeOut('slow');
		}
	};
	makeRequest(_json);
}


function initTablePayments()
{
	var table = 'table.tableDnD_GP';

	if (!$(table).length)
		return;

	$(table).tableDnD({
		onDragStart: function(table, row) {
			originalOrder = $.tableDnD.serialize();
			reOrder = ':even';
			if (table.tBodies[0].rows[1] && $('#' + table.tBodies[0].rows[1].id).hasClass('alt_row'))
				reOrder = ':odd';
			$(table).find('#' + row.id).parent('tr').addClass('myDragClass');
		},
		dragHandle: 'dragHandle',
		onDragClass: 'myDragClass',
		onDrop: function(table, row) {
			if (originalOrder != $.tableDnD.serialize()) {
				var way = (originalOrder.indexOf(row.id) < $.tableDnD.serialize().indexOf(row.id))? 1 : 0;
				var ids = row.id.split('_');
				var tableDrag = table;
				var tableId = table.id.replace('table-', '');

				var params = '';
				if (tableId == 'payment_button')
					params = {
						dataType: 'json',
						tabName: 'paymentssettings',
						tabFolder: 'classes/tabs/',
						action: 'updatePositions',
						id_payment: ids[2],
						id_currency: $('#_ID_CURRENCY').val(),
						way: way
					};

				params['ajax'] = 1;
				params['page'] = parseInt($('input[name=page]').val());
				params['selected_pagination'] = parseInt($('input[name=selected_pagination]').val());

				var data = $.tableDnD.serialize().replace(/table-/g, '');
				if ((tableId == 'category') && (data.indexOf('_0&') != -1))
					data += '&found_first=1';

				$.ajax({
					type: 'POST',
					headers: { "cache-control": "no-cache" },
					async: false,
					url: actions_controller_url + '&rand=' + new Date().getTime(),
					dataType: params.dataType,
					data:  data + '&' + objToStringQ(params) ,
					success: function(data) {
						var nodrag_lines = $(tableDrag).find('tr:not(".nodrag")');
						var new_pos;
						if (come_from == 'AdminModulesPositions')
						{
							nodrag_lines.each(function(i) {
								$(this).find('.positions').html(i+1);
							});
						}
						else
						{
							if (tableId == 'product' || tableId.indexOf('attribute') != -1 || tableId == 'attribute_group' || tableId == 'feature')
								var reg = /_[0-9][0-9]*$/g;
							else
								var reg = /_[0-9]$/g;

							var up_reg  = new RegExp('position=[-]?[0-9]+&');
							nodrag_lines.each(function(i) {

								if (params['page'] > 1)
									new_pos = i + ((params['page'] - 1) * params['selected_pagination']);
								else
									new_pos = i;

								$(this).attr('id', $(this).attr('id').replace(reg, '_' + new_pos));
								$(this).find('.positions').text(new_pos + 1);
							});
						}

						nodrag_lines.removeClass('odd');
						nodrag_lines.filter(':odd').addClass('odd');
						nodrag_lines.children('td.dragHandle').find('a').attr('disabled',false);

						if (typeof alternate !== 'undefined' && alternate) {
							nodrag_lines.children('td.dragHandle:first').find('a:odd').attr('disabled',true);
							nodrag_lines.children('td.dragHandle:last').find('a:even').attr('disabled',true);
						}
						else {
							nodrag_lines.children('td.dragHandle:first').find('a:even').attr('disabled',true);
							nodrag_lines.children('td.dragHandle:last').find('a:odd').attr('disabled',true);
						}

						if (typeof data !== 'undefined')
							if (typeof data.message !== 'undefined')
								showMessage(data.message_code, data.message);
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
		}
	});
}

