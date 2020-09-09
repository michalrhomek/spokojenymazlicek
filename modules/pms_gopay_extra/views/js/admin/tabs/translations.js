
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

/**
* Get all div with specified name and for each one (by id), toggle their visibility
*/
function openCloseAllDiv(name, option)
{
	var tab = $('*[name='+name+']');
	for (var i = 0; i < tab.length; ++i)
		toggle(tab[i], option);
}

function toggleDiv(name, option)
{
	$('*[name='+name+']').each(function(){
		if (option == 'open')
		{
			$('#buttonall').data('status', 'close');
			$(this).hide();
		}
		else
		{
			$('#buttonall').data('status', 'open');
			$(this).show();
		}
	})
}

function toggleButtonValue(id_button, text1, text2)
{
	if ($('#'+id_button).find('i').first().hasClass('process-icon-compress'))
	{
		$('#'+id_button).find('i').first().removeClass('process-icon-compress').addClass('process-icon-expand');
		$('#'+id_button).find('span').first().html(text1);
	}
	else
	{
		$('#'+id_button).find('i').first().removeClass('process-icon-expand').addClass('process-icon-compress');
		$('#'+id_button).find('span').first().html(text2);
	}
}

/**
* Get 
*/
function shareTranslation(e) {
	e.preventDefault();
	var lang = $('#_TRANS_LANG').val();
	var data = {
		action: 'shareTranslation',
		tabName: 'translations',
		tabFolder: 'classes/tabs/',
		iso_code: lang
	};

	var _json = {
		data: data
	};
	makeRequest(_json);
}

function saveTranslations(e) {
	var array_data = {};

	var $elements_key_translations = $('div[id*=pms_translate_]');
	$.each($elements_key_translations, function (i, element) {
		var file_translation = $(element).attr('data-template');
		array_data[file_translation] = [];
	});

	var $data_elements = $('div#tab-translate div.content_translations table tr');
	$.each($data_elements, function (i, element) {
		var input = $(element).find('input[type="text"]');

		if (typeof input.attr('name') === 'undefined')
		{
			input = $(element).find('textarea');
		}

		var object = {
			key_translation: input.attr('name'),
			value_translation: input.val()
		};

		array_data[input.attr('data-template')].push(object);

	});

	var lang = $('#_TRANS_LANG').val();
	var data = {
		dataType: 'json',
		action: 'saveTranslations',
		tabName: 'translations',
		tabFolder: 'classes/tabs/',
		submitTransModules: 1,
		array_translation: array_data,
		iso_code: lang
	};

	var _json = {
		data: data,
		beforeSend: function () {
			$('div#tab-translate div.action-overlay').fadeIn('slow');
		},
		success: function (data) {
			var $parent = $('div#tab-translate');
			if (data.message_code == SUCCESS_CODE) {
					if (Object.keys(data.data).length > 0) {
						$('#tab-translate').find('span.missing_badge').html(data.data.total_missing_translations);
						if (typeof data.data.templates !== 'undefined') {
							$.each(data.data.templates, function(i, data_file) {
								var $content_translation = $('#pms_translate_'+i);
								if (typeof data_file.missing_translations !== 'undefined') {

									$content_translation.parent('div').find('span.label-danger').html(data_file.missing_translations);
									$content_translation.show();
								} else {

									$content_translation.parent('div').find('span.label-danger').html(0);
									$content_translation.hide();
								}
								if (typeof data_file.translations !== 'undefined') {
									$.each(data_file.translations, function(key, value){
										var $content_inputs = $content_translation.find('table tr td');
										var $input = $content_inputs.find('input[name="'+key+'"]');

										if (typeof $input.attr('name') === 'undefined')
										{
											$input = $content_inputs.find('textarea[name="'+key+'"]');
										}

										$input.attr('value', value);
										if (isEmpty(value)) {
											$input.addClass('input-error-translate');
										} else {
											if ($input.hasClass('input-error-translate')) {
											   $input.removeClass('input-error-translate');
											}
										}
									});
								}
							});
						}
					}}
		},
		complete: function () {
			$('div#tab-translate div.action-overlay').fadeOut('slow');
		}
	};
	makeRequest(_json);
}

$(document).ready(function () {
	$('button#btn-share-translation').on('click', function(e){shareTranslation(e);});
	$('button[name*="btn-save-translation-"]').on('click', function(e){saveTranslations(e);});
});
