
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

//  for  debuging  function
//console.log(print_r(data.data));

function isEmpty() {
	var count = 0;
	$.each(arguments, function (i, data) {
		if (typeof data !== 'undefined' && data !== null && data !== '' && parseInt(data) !== 0) {
			count++;
		} else
			return false
	});
	return (arguments).length == count ? false : true;
}

function showMessage(message_code, message) {
	if (typeof $.growl !== 'undefined') {
		var data = {
			title: "",
			message: message,
			close: '&times;',
			duration: 10000
		};
		if (message_code == SUCCESS_CODE) {
			data.icon = 'pms pms-check pms-2x pull-left';
			$.growl.notice(data);
		} else {
			data.icon = 'pms pms-times pms-2x pull-left';
			$.growl.error(data);
		}
	}
}

function makeRequest(params) {
	if (typeof params.data.dataType === 'undefined')
		params.data.dataType = 'json';

	if (typeof params.data.fileElementId === typeof undefined)
		params.data.fileElementId = false;

	if (typeof params.data.async === typeof undefined)
		params.data.async = true;

	if (typeof params.data.processData === typeof undefined)
		params.data.async = true;

	if (typeof params.data.contentType === typeof undefined)
		params.data.async = true;

	if (typeof params.data.url_call === typeof undefined)
		params.data.url_call = actions_controller_url;

	$.each(params.data, function (i, d) {
		if (typeof d === 'boolean' && i != 'async') {
			params.data[i] = d ? 1 : 0;
		}
	});

	params.data.navigator = navigator.userAgent;
	(params.data.ajaxType == 'ajaxFileUpload' ? $.ajaxFileUpload : $.ajax)({
		type: 'POST',
		url: params.data.url_call,
		async: params.data.async,
		cache: false,
		fileElementId: params.data.fileElementId,
		dataType: params.data.dataType,
		processData: params.data.processData,
		contentType: params.data.contentType,
		data: params.data,
		beforeSend: function (request) {

			$('.has-action').addClass('disabled');

			if (typeof params.beforeSend === 'function')
				params.beforeSend();

			if (typeof params.e !== 'undefined' && typeof params.e.target !== 'undefined') {
				if ($(params.e.target).hasClass('spinnable')) {
					var $span = $('<span/>');
					$span.addClass('spinner');
					var $i = $('<i/>');
					$i.addClass('icon-spin icon-refresh');
					$i.appendTo($span);
					$span.appendTo($(params.e.target));
				}

				$(params.e.target).blur();
			}
		},
		success: function (data) {
			//write error log
			if (params.data.dataType == 'json' && typeof data != 'object') {
				if (!!$.prototype.fancybox && typeof $.fancybox.open !== 'undefined')
					$.fancybox.open([{
						type: 'inline',
						autoScale: true,
						minHeight: 30,
						content: '<p class="fancybox-error">' + data.message + '</p>'
					}], {
						padding: 0
					});
				else
					alert('Chyba: ' + data.message);

				return;
			}

			if (typeof params.success === 'function')
				params.success(data);

			if (typeof data !== 'undefined')
				if (typeof data.message !== 'undefined')
					showMessage(data.message_code, data.message);
		},
		complete: function (jqXHR, textStatus) {
			$('.has-action').removeClass('disabled');
			if (typeof params.complete === 'function')
				params.complete(jqXHR, textStatus);

			//remove spinner
			if (typeof params.e !== 'undefined' && typeof params.e.target !== 'undefined') {
				if ($(params.e.target).hasClass('spinnable'))
					$(params.e.target).find('.spinner').remove();
			}

			if (typeof callbackExtraFunctions == 'function') {
				callbackExtraFunctions(params.data.action);
			}
		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			if (XMLHttpRequest.readyState == 0 || (XMLHttpRequest.readyState === 4 && XMLHttpRequest.status === 403 && XMLHttpRequest.statusText === 'Forbidden')) {
				location.reload();
				return false;
			}
			if (XMLHttpRequest.status != 0) {
				if (!!$.prototype.fancybox && typeof $.fancybox.open !== 'undefined')
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

function print_r(arr, level) {
	var dumped_text = "";
	if (!level) level = 0;

	//The padding given at the beginning of the line.
	var level_padding = "";
	for (var j = 0; j < level + 1; j++) level_padding += "    ";

	if (typeof (arr) == 'object') { //Array/Hashes/Objects 
		for (var item in arr) {
			var value = arr[item];

			if (typeof (value) == 'object') { //If it is an array,
				dumped_text += level_padding + "'" + item + "' ...\n";
				dumped_text += print_r(value, level + 1);
			} else {
				dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
			}
		}
	} else { //Stings/Chars/Numbers etc.
		dumped_text = "===>" + arr + "<===(" + typeof (arr) + ")";
	}
	return dumped_text;
}

function getReviews (){
	var data = {
		action: 'getReviews',
		tabName: 'core',
		dataType: 'html'
	};

	var _json = {
		data: data,
		beforeSend: function () {
		},
		success: function(htmlData) {
			if (!!$.prototype.fancybox && typeof $.fancybox.open !== 'undefined'){
				$.fancybox.open([{
					type: 'inline',
					autoScale: true,
					minHeight: 30,
					content: htmlData
				}], {
					padding: 0
				});

				
				$('input.star').rating();

				$('#submitNewMessage').click(function (e) {
					// Kill default behaviour
					e.preventDefault();
					var data = {
						action: 'addNewReview',
						tabName: 'core',
						posts: $('#id_new_comment_form').serialize()
					};

					var _json = {
						data: data,
						beforeSend: function () {
						},
						success: function(data) {
							if (typeof data.errors !== 'undefined' && data.errors.length > 0) {
								$('#new_comment_form_error ul').html('');
								$.each(data.errors, function (index, value) {
									$('#new_comment_form_error ul').append('<li>' + value + '</li>');
								});
								$('#new_comment_form_error').slideDown('slow');
							}
							else {
								if (typeof data.result !== typeof undefined)
									if (typeof data.result.message !== typeof undefined) {
										$.fancybox.close();
										showMessage(data.result.message_code, data.result.message);
									}
							}
						},
						complete: function () {
						}
					};
					makeRequest(_json);
				});

				$('#notAnymore').click(function (e) {
					e.preventDefault();
					var data = {
						action: 'addNewReview',
						tabName: 'core',
						notAnymore: 1
					};

					var _json = {
						data: data,
						beforeSend: function () {
						},
						success: function(data) {
							$.fancybox.close();
						},
						complete: function () {
						}
					};
					makeRequest(_json);
				});
			}
		},
		complete: function () {
		}
	};
	makeRequest(_json);
}

$(function() {
	if (typeof SHOW_REVIEW !== 'undefined' && SHOW_REVIEW == 1) {
		setTimeout(function(){
			getReviews();
		}, DELAY_TIME);
	}
});

/*********************************/
// vlastní javascript mimo šablonu

// pomocné proměnné pro PRODUCTION CREDENTIALS
var mode_status = "";
var initial_mode_status = "";
var mode_change_full = 0;
var mode_change_test = 0;

$(document).ready(function () {
	
	// EET - skrytí ostatních řádků při off
	$("#_EET_on").click(function(){$("#_DIC, #_VAT_OTHER_on, #_BILL_PDF_on").closest('div.form-group').removeClass("hide_mode");});
	$("#_EET_off").click(function(){$("#_DIC, #_VAT_OTHER_on, #_BILL_PDF_on").closest('div.form-group').addClass("hide_mode");});
	$("#_EET_on").click(function(){$("#_EET_MSG_1").closest('div.form-group').parent().closest('div.form-group').removeClass("hide_mode");});
	$("#_EET_off").click(function(){$("#_EET_MSG_1").closest('div.form-group').parent().closest('div.form-group').addClass("hide_mode");});
	
	// ERRORS REPORT - skrytí pole pro mail
	$("#_ERRORS_REPORT_on").click(function(){$("#_ERRORS_REPORT_EMAIL").closest('div.form-group').removeClass("hide_mode");});
	$("#_ERRORS_REPORT_off").click(function(){$("#_ERRORS_REPORT_EMAIL").closest('div.form-group').addClass("hide_mode");});
	
	// MODULE VISIBILITY FOR CUSTOMER
	$("#_VISIBLE_MODULE_off").click(function(){$("#_VISIBLE_MODULE_IP").closest('div.form-group').removeClass("hide_mode");});
	$("#_VISIBLE_MODULE_on").click(function(){$("#_VISIBLE_MODULE_IP").closest('div.form-group').addClass("hide_mode");});
	
	// PRICE RULES
	$("#_PRICE_VIEW_on").click(function(){
		$("#_PRICE_DIFFERENT_on, #_FEE_TYPE, #_FEE_VALUE, #_PRICE_INFO_TEXT").closest('div.form-group').removeClass("hide_mode");
	});
	$("#_PRICE_VIEW_off").click(function(){
		$("#_PRICE_DIFFERENT_on, #_FEE_TYPE, #_FEE_VALUE, #_PRICE_INFO_TEXT").closest('div.form-group').addClass("hide_mode");
	});
	
	// MODULE VISIBILITY FOR CUSTOMER
	$("#_VISIBLE_MODULE_IP_SHORTCUT").click(function(){$("#_VISIBLE_MODULE_IP").val($("#_VISIBLE_MODULE_IP_SHORTCUT").html());});
	
	// SETTING PAYMENT PREFERENCE
	$("#_RECURRENT_on").click(function(){
		$("#_RECURRENCE_TYPE").closest('div.form-group').removeClass("hide_mode");
		if($("#_RECURRENCE_TYPE").val() === "periodic") {
			$("#_RECURRENCE_CYCLE, #_RECURRENCE_DATE_TO").closest('div.form-group').removeClass("hide_mode");
		}
		selected_cycles();
	});
	
	$("#_RECURRENT_off").click(function(){
		$("#_RECURRENCE_TYPE, #_RECURRENCE_CYCLE, #_RECURRENCE_DATE_TO").closest('div.form-group').addClass("hide_mode");	
	});
	
	$("#_RECURRENCE_TYPE").change(function(){
		if($("#_RECURRENCE_TYPE").val() === "periodic") {
			$("#_RECURRENCE_CYCLE, #_RECURRENCE_DATE_TO").closest('div.form-group').removeClass("hide_mode");
		} 
		else {
			$("#_RECURRENCE_CYCLE, #_RECURRENCE_DATE_TO").closest('div.form-group').addClass("hide_mode");
		}
	});
	
	$("#_RECURRENCE_CYCLE").change(function(){selected_cycles();});
	
	selected_cycles();
	
	
	// PRODUCTION CREDENTIALS
	$("#mode_on").click(function(){
		$("#_GO_ID, #_CLIENT_ID, #_CLIENT_SECRET").closest('div.form-group').removeClass("hide_mode");		
		$("#_GO_ID_TEST, #_CLIENT_ID_TEST, #_CLIENT_SECRET_TEST").closest('div.form-group').addClass("hide_mode");
	});
	$("#mode_off").click(function(){
		$("#_GO_ID, #_CLIENT_ID, #_CLIENT_SECRET").closest('div.form-group').addClass("hide_mode");
		$("#_GO_ID_TEST, #_CLIENT_ID_TEST, #_CLIENT_SECRET_TEST").closest('div.form-group').removeClass("hide_mode");
	});

	$("#_GO_ID, #_CLIENT_ID, #_CLIENT_SECRET").change(function(){$("#test_config").closest('div.form-group').addClass("hide_mode");});
	$("#_GO_ID, #_CLIENT_ID, #_CLIENT_SECRET").change(function(){$("#_CONFIG_INFO_TEST").closest('div.form-group').removeClass("hide_mode");});
	
	// určí, jestli se nacházím v modu test nebo full
	if($("#mode_on").is(":checked")) mode_status = "full";
	if($("#mode_off").is(":checked")) mode_status = "test";
	
	initial_mode_status = mode_status;
	
	// zobrazí příslušené hlášky a tlačítka
	productCredentialsChange(); 
	
	// v případě změny si změnu uloží a vyhodnotí opět stav tlačítek
	$("#_GO_ID, #_CLIENT_ID, #_CLIENT_SECRET").change(function(){
		mode_change_full = 1;
		productCredentialsChange();
	});
	$("#_GO_ID_TEST, #_CLIENT_ID_TEST, #_CLIENT_SECRET_TEST").change(function(){
		mode_change_test = 1;
		productCredentialsChange();
	});
	
	// při přepnutí 
	$("#mode_on").click(function(){
		mode_status = "full";
		if(initial_mode_status == "test") mode_change_full = 1;
		productCredentialsChange();
	});
		
	$("#mode_off").click(function(){
		mode_status = "test";
		if(initial_mode_status == "full") mode_change_test = 1;
		productCredentialsChange();
	});
});


// nejprve mě zajímá, jestli data mají správný tvar, 
// pokud ano tak mě zajímá, jestli nedošlo ke změně, 
// pokdu ano, musí se nejprve uložit, 
// nakonec se zobrazí tlačítko otestovat konfiguraci

function productCredentialsChange(change){

	if(mode_status == "full"){
		if(($("#_GO_ID").val().length == 10) && ($("#_CLIENT_ID").val().length == 10) && ($("#_CLIENT_SECRET").val().length == 8)){
			if(mode_change_full === 1){ // mají správný tvar, ale došlo ke změně
				$("#test_config").closest('div.form-group').addClass("hide_mode");
				$("#_CONFIG_INFO_TEST").closest('div.form-group').removeClass("hide_mode"); 
				$("#_CONFIG_INFO_DATA").closest('div.form-group').addClass("hide_mode");
			}
			else { // mají správný tvar a nedošlo ke změně
				$("#test_config").closest('div.form-group').removeClass("hide_mode"); 
				$("#_CONFIG_INFO_TEST").closest('div.form-group').addClass("hide_mode"); 
				$("#_CONFIG_INFO_DATA").closest('div.form-group').addClass("hide_mode");
			}
		} 
		else{ // data nemají správný tvar
			$("#test_config").closest('div.form-group').addClass("hide_mode");
			$("#_CONFIG_INFO_TEST").closest('div.form-group').addClass("hide_mode"); 
			$("#_CONFIG_INFO_DATA").closest('div.form-group').removeClass("hide_mode"); 
		}
	}
	if(mode_status == "test"){
		if(($("#_GO_ID_TEST").val().length == 10) && ($("#_CLIENT_ID_TEST").val().length == 10) && ($("#_CLIENT_SECRET_TEST").val().length == 8)){
			if(mode_change_test === 1){ 
				$("#test_config").closest('div.form-group').addClass("hide_mode");
				$("#_CONFIG_INFO_TEST").closest('div.form-group').removeClass("hide_mode");
				$("#_CONFIG_INFO_DATA").closest('div.form-group').addClass("hide_mode");
			}
			else { 
				$("#test_config").closest('div.form-group').removeClass("hide_mode");
				$("#_CONFIG_INFO_TEST").closest('div.form-group').addClass("hide_mode"); 
				$("#_CONFIG_INFO_DATA").closest('div.form-group').addClass("hide_mode");
			}
		} 
		else{ 
			$("#test_config").closest('div.form-group').addClass("hide_mode");
			$("#_CONFIG_INFO_TEST").closest('div.form-group').addClass("hide_mode"); 
			$("#_CONFIG_INFO_DATA").closest('div.form-group').removeClass("hide_mode");
		}
	}
}


// Recurrence cycle: every	
function selected_cycles() {
	
	$('#recurrence_period').remove();
	$('#span_period').remove();
	$('#span_every').remove();
	
	if($("#_RECURRENCE_CYCLE").val() == 'DAY'){
		$("#_RECURRENCE_CYCLE").after(period_days);
	}
	else if($("#_RECURRENCE_CYCLE").val() == 'WEEK'){
		$("#_RECURRENCE_CYCLE").after(period_weeks);
	}
	else if($("#_RECURRENCE_CYCLE").val() == 'MONTH'){
		$("#_RECURRENCE_CYCLE").after(period_months);
	}
}
