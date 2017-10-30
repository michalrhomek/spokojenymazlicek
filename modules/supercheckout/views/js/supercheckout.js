/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store. 
 *
 * @category  PrestaShop Module
 * @author    knowband.com <support@knowband.com>
 * @copyright 2015 Knowband
 */

var primaryColor = '#496CAD',
dangerColor = '#bd362f',
successColor = '#609450',
warningColor = '#ab7a4b',
inverseColor = '#45484d';

var themerPrimaryColor = primaryColor;
var previousPoint = null, previousLabel = null;


$(document).ready(function() {
	getMailChimpList();
    $('#velsof_tab_login').on('click',function(){
       $("#google_acc").hide();
        $("#facebook_acc").hide();
        $("#loginizer_link").show();
    });
    $('#slide1_controls').on('click', function(){      
        $('.velsof-adv-panel').removeAttr('style');
        $('#slide1_controls').hide();
        $('#slide2_controls').show();
    });
    $('#slide2_controls').on('click', function(){
        $('.velsof-adv-panel').attr('style','right:-18px;');
        $('#slide1_controls').show();
        $('#slide2_controls').hide();
    });
    
    $('.alternate').each(function() {
        $('tr:odd',  this).addClass('odd').removeClass('even');
        $('tr:even', this).addClass('even').removeClass('odd');
    });

    $('input.input-checkbox-option').click(function(){
        if($(this).is(':checked')){
            $(this).attr('value', 1);
        }else{
            $(this).attr('value', 0);
        }
    });
    
    $('.display_address_field').click(function(event){
        var id = $(this).attr('id').split('_');
        id.pop();
        id = id.join('_');
		if(!$(this).is(':checked')){
			if($('#'+id+'_require').is(':checked')){
				$('#'+id+'_require').parent().removeClass('checked');
                $('#'+id+'_require').removeAttr('checked');
                $('#'+id+'_require').attr('value', 0);
			}
			
		}
		//$(this).parent().removeClass('checked');
//        if($('#'+id+'_require').is(':checked')){
//            if($(this).is(':checked')){
//                alert(uncheckAddressFieldMsg)
//                $(this).parent().addClass('checked');
//                event.preventDefault();
//            }else{
//                $(this).parent().addClass('checked');
//            }            
//        }
    });
    
    $('.require_address_field').click(function(event){
        if($(this).is(':checked')){
            var id = $(this).attr('id').split('_');
            id.pop();
            id = id.join('_');
            if(!$('#'+id+'_display').is(':checked')){
                $('#'+id+'_display').parent().addClass('checked');
                $('#'+id+'_display').attr('checked', 'checked');
                $('#'+id+'_display').attr('value', 1);
            }
        }
        //event.preventDefault();

        //alert(id);
    });
    
    $('.sortable > tr').tsort({attr:'sort-data'});
	
    $( ".sortable" ).sortable({
        revert: true,
        cursor: "move",
        items: "> .sort-item",
        containment: "document",
        distance: 5 ,
        opacity: 0.8,
        stop: function( event, ui ) {
            $(this).find("tr").each(function(i, el){
                //$(this).find("input.sort").val($(el).index());
                $(this).find("input.sort").attr('value',$(el).index()+1);
                $('.alternate').each(function() {
                    $('tr:odd',  this).addClass('odd').removeClass('even');
                    $('tr:even', this).addClass('even').removeClass('odd');
                });
            });
        }
    });
    
    $('.bootbox-design-edit-html').click(function(){
        var id = $(this).attr('id')+'_value';
        var splitId = id.split('_');
        if($("#"+id).length){
            var stored_value = $("#"+id).val();
        }else{
            var hidden_field = '<input type="hidden" id="'+id+'" name="velocity_supercheckout[design][html]['+splitId[splitId.length - 1]+']" value="">';
            $('#tab_design').append(hidden_field);
            var stored_value="";
        }

        bootbox.confirm('<h4>'+$("#modals_bootbox_prompt_header_html").val()+'</h4><textarea id="text_area_html_'+splitId[splitId.length - 1]+'" class="supercheckout_textarea_html" >'+ stored_value +'</textarea>',
        function(result) {
            if(result){
                html_string=$('#text_area_html_'+splitId[splitId.length - 1]).val().replace(/(\r\n|\n|\r)/gm, "<br/>");
                $("#"+id).val(html_string);
            }
        });
    });
    
    /*$('.bootbox-design-extra-html').click(function(){alert('helo');
        var temp = $(this).attr('id');
        var splitId = temp.split('-');
        var id = "modals_bootbox_prompt_"+splitId[splitId.length - 1];
        if($("#"+id).length){
            var stored_value = $("#"+id).val();
        }else{
            var hidden_field = '<input type="hidden" id="'+id+'" name="velocity_supercheckout[design][html]['+splitId[splitId.length - 1]+'][value]" value="">';
            $('#tab_design').append(hidden_field);
            var stored_value="";
        }

        bootbox.confirm('<h4>'+$("#modals_bootbox_prompt_header_html").val()+'</h4><textarea id="text_area_html" class="supercheckout_textarea_html" >'+ stored_value +'</textarea>',
        function(result) {
            if(result){
                html_string=$('#text_area_html').val().replace(/(\r\n|\n|\r)/gm, "<br/>");
                $("#"+id).val(html_string);
            }
        });
    });*/
    
    //2-Column Layout
    $('input[name="velocity_supercheckout[column_width][2_column][1]"]').css('width', $('input[name="velocity_supercheckout[column_width][2_column][1]"]').val()+'%');
    $('input[name="velocity_supercheckout[column_width][2_column][2]"]').css('width', $('input[name="velocity_supercheckout[column_width][2_column][2]"]').val()-1+'%');
    
    //3-Column Layout
    $('input[name="velocity_supercheckout[column_width][3_column][1]"]').css('width', $('input[name="velocity_supercheckout[column_width][3_column][1]"]').val()+'%');
    $('input[name="velocity_supercheckout[column_width][3_column][2]"]').css('width', $('input[name="velocity_supercheckout[column_width][3_column][2]"]').val()+'%');
    $('input[name="velocity_supercheckout[column_width][3_column][3]"]').css('width', $('input[name="velocity_supercheckout[column_width][3_column][3]"]').val()-1+'%');
	
	$("#payment-accordian").accordion({ 
      animated: 'bounceslide',
      autoHeight: false, 
      collapsible: true, 
      event: 'click', 
      active: false,
      animate: 100
    });
	$("#delivery-accordian").accordion({ 
      animated: 'bounceslide',
      autoHeight: false, 
      collapsible: true, 
      event: 'click', 
      active: false,
      animate: 100
    });
    
    
});

function dialogExtraHtml(e){
    var temp = $(e).attr('id');
    var splitId = temp.split('-');
    var id = "modals_bootbox_prompt_"+splitId[splitId.length - 1];
    if($("#"+id).length){
        var stored_value = $("#"+id).val();
    }else{
        var hidden_field = '<input type="hidden" id="'+id+'" name="velocity_supercheckout[design][html]['+splitId[splitId.length - 1]+'][value]" value="">';
        $('#tab_design').append(hidden_field);
        var stored_value="";
    }

    bootbox.confirm('<h4>'+$("#modals_bootbox_prompt_header_html").val()+'</h4><textarea id="text_area_html" class="supercheckout_textarea_html" >'+ stored_value +'</textarea>',
    function(result) {
        if(result){
            html_string=$('#text_area_html').val().replace(/(\r\n|\n|\r)/gm, "<br/>");
            $("#"+id).val(html_string);
        }
    });    
}

function remove_html(e){    
    var data = $(e).attr('data');
    $('#portlet_'+ data).remove();
    $('#modals_bootbox_prompt_'+ data).remove();
    $('#3_col_h_'+data).remove();
    $('#3_row_h_'+data).remove();
    $('#3_col_ins_h_'+data).remove();
    $('#2_col_h_'+data).remove();
    $('#2_row_h_'+data).remove();
    $('#2_col_ins_h_'+data).remove();
    $('#1_col_h_'+data).remove();
    $('#1_row_h_'+data).remove();
    $('#1_col_ins_h_'+data).remove();
}


function validate_data(){
    $('span.error').html('');
    var messgeObj = $('#content').find('.bootstrap').find('.alert');
    $(messgeObj).parent().remove();
    var success = '';
    var errorMsg = '';
    $.ajax( {
        type: "POST",
        url: scp_ajax_action,
        data: $('#supercheckout_configuration_form').serialize()+'&ajax=true&method=validation',
        async: false,
        dataType: 'json',
        beforeSend: function() {
            $('#supercheckout_configuration_form').fadeTo('slow', 0.4);
        },
        success: function( json ) {
            if(json['success'] != undefined && json['success'] != null){
                 $('#supercheckout_configuration_form').submit();
            }else if(json['error'] != undefined){
                $('#supercheckout_configuration_form').fadeTo('slow', 1);
                errorMsg = json['error']['request_error'];
                
                if(json['error']['fb_login_app_id'] != undefined){
                   $('#fb_app_id_error').html(json['error']['fb_login_app_id']); 
                }
                if(json['error']['fb_login_app_secret'] != undefined){
                   $('#fb_app_secret_error').html(json['error']['fb_login_app_secret']); 
                }
                
                if(json['error']['gl_login_app_id'] != undefined){
                   $('#gl_app_id_error').html(json['error']['gl_login_app_id']); 
                }
                if(json['error']['gl_login_client_id'] != undefined){
                   $('#gl_client_id_error').html(json['error']['gl_login_client_id']); 
                }
                if(json['error']['gl_login_app_secret'] != undefined){
                   $('#gl_app_secret_error').html(json['error']['gl_login_app_secret']); 
                }
                
                $('#velsof_supercheckout_container').find('li').removeClass('active');
                $("#velsof_tab_login").trigger('click');
                
                var errorHtml = '<div class="bootstrap supercheckout-message"><div class="alert alert-danger">';
                errorHtml += '<button type="button" class="close" data-dismiss="alert">×</button>';
                errorHtml += errorMsg;
                errorHtml += '</div></div>';
                $('#velsof_supercheckout_container').before(errorHtml);
                setTimeout(function(){$('#velsof_supercheckout_container .supercheckout-message').remove();}, 5000);
            }
        }
    } );       
    
    return success;
}

function setChangedLanguage(url, e){
    location.href= url+'&velsof_translate_lang='+$(e).val();
}

function generate_language(url, type){
    lang_code = $('select[name="velocity_transalator[selected_language]"] option:selected').val().split('_');
    lang_code = lang_code[1];
    requestUrl = url+'&ajax=true&tranlationType='+type;
    $.ajax( {
        type: "POST",
        url: requestUrl,
        data: $('#tab_lang_translator input, #tab_lang_translator select'),
        dataType: 'json',
        beforeSend: function() {
			//$('#velsof_supercheckout_container').parent().find('bootstrap > .alert').parent().remove();
			$("div").remove(".alert");
            $('#velsof-lang-trans-body').hide();
            $('#velsof-lang-trans-progress').show();

        },
        success: function( json ) {
            var classType = 'success';
	    var inlineCss = 'background-color: green;border-color: green;';
            var msg = '';
            var printMsg = false;
            if(type == 'download' && json['success'] != undefined){
                location.href = url+'&downloadTranslation='+json['success']+'&translationTmp=1';
            }else if(type == 'download' && json['error'] != undefined){
                msg = json['error'];
                classType = 'danger';
		inlineCss = 'color: #b94a48';
                printMsg = true;
            }
            
            if(type == 'save' || type == 'saveDownload'){
                if(type == 'saveDownload' && json['success'] != undefined){
                    location.href = url+'&downloadTranslation='+lang_code;
                }else{
                    printMsg = true;
                    if(json['error'] == undefined && json['error'] == null){
                         msg = json['success'];
                    }else if(json['error'] != undefined){                
                         classType = 'danger';
			 inlineCss = 'color: #b94a48';
                         msg += ' '+json['error'];
                    }                    
                }                
            }
            
            if(type == 'saveDownload'){
                
            }
            
            if(printMsg){
                var html = '<div class="bootstrap supercheckout-message"><div class="alert alert-'+classType+'" style="'+inlineCss+'">';
                html += '<button type="button" class="close" data-dismiss="alert">×</button>';
                html += msg;
                html += '</div></div>';
                $('#velsof_supercheckout_container').before(html);
                setTimeout(function(){$('#velsof_supercheckout_container .supercheckout-message').remove();}, 5000);
            }
			$('#velsof-lang-trans-progress').hide();
            $('#velsof-lang-trans-body').show();
        }
    } );   
}


function getMailChimpList()
{
	var key = $("#supercheckout_mailchimp_key").val();
	var listid = $("#supercheckout_mailchimp_list").val();
	$.ajax({
		type: "POST",
		url: scp_ajax_action,
		data: 'ajax=true&method=getMailChimpList&key='+key,
		dataType: 'json',
		beforeSend: function() {
			$("#supercheckout_list").html('');
			$("#mailchimp_loading").show();
		},		
		success: function(json) {
			var html = '';
			
			if (json == 'false')
				html = "<font color='red'>No list exists for this API key!</font>";
			else
			{
				html += '<select name="velocity_supercheckout[mailchimp][list]"';
				if (ps_ver == 15)
					html += 'class="selectpicker vss_sc_ver15"';
				html += 'id="mailchimp_selectlist">';

				for (i in json)
				{
					if (listid == json[i]['id'])
						html += '<option value="' + json[i]['id'] + '" selected>' + json[i]['name'] + '</option>'; 
					else
						html += '<option value="' + json[i]['id'] + '">' + json[i]['name'] + '</option>'; 
				}
				html += '</select>';
			}
			$("#mailchimp_loading").hide();
			$("#supercheckout_list").html(html);
			$('select.vss_sc_ver15#mailchimp_selectlist').selectpicker();
		}
	});
}

function configurationAccordian(id)
{
    if (id == 'facebook')
    {
        $("#facebook_acc").show();
        $("#google_acc").hide();
        $("#loginizer_link").hide();
	$(window).scrollTop($('#facebook_acc').offset().top);
    }
    else if (id == 'google')
    {
        $("#google_acc").show();
        $("#facebook_acc").hide();
        $("#loginizer_link").hide();
	$(window).scrollTop($('#google_acc').offset().top);
    }
$("#"+id+"_accordian").accordion({ 
      animated: 'bounceslide',
      autoHeight: false, 
      collapsible: true, 
      event: 'click', 
      active: false,
      animate: 100
    });
}
function bg_changer(col)
    {
        color = "#"+col;

 document.getElementById("button_preview").style.backgroundColor= color;
    }

   function border_changer(col)
    {
        color = "#"+col;

 document.getElementById("button_preview").style.borderTopColor= color;
 document.getElementById("button_preview").style.borderRightColor= color;
 document.getElementById("button_preview").style.borderLeftColor= color;
    }
    function border_bottom_changer(col)
    {
        color = "#"+col;

 document.getElementById("button_preview").style.borderBottomColor= color;
    }
       function text_changer(col)
    {
        color = "#"+col;
 document.getElementById("button_preview").style.color= color;
    }

function loginizerAdv()
{
    if (loginizer_adv == 0)
        window.open('http://addons.prestashop.com/en/social-commerce-facebook-prestashop-modules/18220-social-login-15-in-1-statistics-and-mailchimp.html');
    loginizer_adv = loginizer_adv + 1;
}

function readPaymentURL(id, imageid){
         $("#"+imageid+"_msg").hide();
		var imgPath = $("#"+imageid+"_file")[0].value;
		var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
		
		if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
		if (typeof (FileReader) != "undefined") {
 
            var image_holder = $("#"+imageid);
	    
            image_holder.empty();
 
            var reader = new FileReader();
            reader.onload = function (e) {
		    
                $('#'+imageid).attr('src', e.target.result);
            }
            image_holder.show();
            reader.readAsDataURL($("#"+imageid+"_file")[0].files[0]);
			$("#payment_image_title_"+id).val("paymethod"+id+extn);
        }
		}
		else
		{	$("#"+imageid+"_msg").css("color", "red");
			$("#"+imageid+"_msg").show();
		}
		
        }
		
function removeFile(id)
{
	if (confirm(remove_cnfrm_msg) == true)
	{
	$.ajax({
		type: "POST",
		url: scp_ajax_action,
		data: 'ajax=true&method=removeFile&id=paymethod'+id,
		dataType: 'json',
		beforeSend: function() {
		},		
		success: function(json) {
			$("#payment_image_title_"+id).val("");
			$('#payment-img-'+id).attr('src', module_path+'views/img/admin/no-image.jpg');
		}
		
	});
	}
}

function readDeliveryURL(id, imageid){
	
         $("#"+imageid+"_msg").hide();
		var imgPath = $("#"+imageid+"_file")[0].value;
		
		var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
		
		if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
		if (typeof (FileReader) != "undefined") {
 
            var image_holder = $("#"+imageid);
	    
            image_holder.empty();
 
            var reader = new FileReader();
            reader.onload = function (e) {
		    
                $('#'+imageid).attr('src', e.target.result);
            }
            image_holder.show();
            reader.readAsDataURL($("#"+imageid+"_file")[0].files[0]);
			$("#delivery_image_title_"+id).val("deliverymethod"+id+extn);
        }
		}
		else
		{	$("#"+imageid+"_msg").css("color", "red");
			$("#"+imageid+"_msg").show();
		}
		
        }
		
		
function removeDeliveryFile(id)
{
	if (confirm(remove_cnfrm_msg) == true)
	{
	$.ajax({
		type: "POST",
		url: scp_ajax_action,
		data: 'ajax=true&method=removeFile&id=deliverymethod' + id,
		dataType: 'json',
		beforeSend: function() {
		},
		success: function(json) {
			$("#delivery_image_title_" + id).val("");
			$('#delivery-img-' + id).attr('src', module_path + 'views/img/admin/no-image.jpg');
		}

	});
	}
}