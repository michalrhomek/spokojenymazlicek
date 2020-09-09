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

var calledFromShipping = 0;
$(document).ready(function(){
		
	if (typeof cart_empty != 'undefined' && cart_empty == true)
	{
		return;
	}
	checkout_option('input:radio[name=checkout_option]:checked'); //@Nitin Jain, 24-Aug-2015 - when customer come back from Paypal like methods, he wasn't getting selected checkout option
	shipping_address_value($('input[name="shipping_address_value"]:checked'));// @Nitin Jain - to show address form for logged in customer, if new address is selected.
	    if(iscartvirtual == true){
	    $('#use_for_invoice').prop('checked', false);
	    $('#checkoutBillingAddress').slideDown();
	    updateInvoiceAddress();
	    }
	//to hide Delivery address block when by default login checkout is selected
	if($('input:radio[name=checkout_option]:checked').val() == 0){
	    $('#checkoutShippingAddress').hide();
	    $('#checkoutBillingAddress').hide();
	}

	// to update shipping methods list based on default address when customer login on supercheckout page.
        buildAddressBlock($('#shipping-existing select[name="shipping_address_id"]').val(), 'delivery');
	
        if($('#use_for_invoice').is(':checked')){
            updateInvoiceAddress();
        }
        if($('input[name="shipping_address_value"]:checked').val() == 0){
            loadCarriers();            
        }       

//to hide social login block when by default guest checkout is selected
	if($('input:radio[name=checkout_option]:checked').val() == 1)    
	        $('#social_login_block').hide();

    // Create State list
    statelist(default_country, 0, 'select[name="shipping_address[id_state]"]');
    statelist(default_country, 0, 'select[name="payment_address[id_state]"]');
    
    //Display Selected Address detail
    displayAddressDetail();
    loadCarriers();      //@Nitin Jain, 17-Aug-2015 - added function call here to update shipping method list based upon selected state on page load.
    if($('#gift').is(':checked')){
        $('#supercheckout-gift-comments').show();
    }
    
    //Get Payment Method Form
    if($('input:radio[name="payment_method"]:checked').length){
        actionOnPaymentSelect($('input:radio[name="payment_method"]:checked'));
    }else{
        $('input:radio[name="payment_method"]').first().attr('checked', 'checked');
        $('input:radio[name="payment_method"]').first().parent().addClass('checked');
        actionOnPaymentSelect($('input:radio[name="payment_method"]:checked'));
    }
    
    
    $('input:radio[name="payment_method"]').live('click', function(){
        actionOnPaymentSelect(this);
    });
    
    //Display password field, based on checkout option
    $('input[name="checkout_option"]').on('click', function(){
       checkout_option(this);
    });
    
    //Create shipping state list based on selected shipping country
    $('select[name="shipping_address[id_country]"]').change(function(){
        var selected_country = $(this).find('option:selected').attr('value');
        var selected_state = 0;
        statelist(selected_country, selected_state, 'select[name="shipping_address[id_state]"]');
        checkDniandVatNumber('delivery');
        if($('input[name="shipping_address[postcode]"]').length && $('input[name="shipping_address[postcode]"]').val() != ''){
            checkZipCode(this, true);
        }else{
            loadCarriers();
        }
    });

    
    //Change shipping state list, if shipping address is same as payment address
    $('select[name="shipping_address[id_state]"]').live('change', function(){
        if($('#use_for_invoice').is(':checked')){
            var selected_state = $(this).find('option:selected').attr('value');
            $('select[name="payment_address[id_state]"] option').removeAttr('selected');
            $('select[name="payment_address[id_state]"] option').each(function(){
                if($(this).val() == selected_state){
                    $(this).attr('selected', 'selected');
                }
            });   
        }
        loadCarriers();
    });
    

    // Display or hide Shipping address container, based on same address or another address for shipping status
    $('#use_for_invoice').live('click', function(){
        if($(this).is(':checked')){
            $('#checkoutBillingAddress').slideUp();
            updateInvoiceAddress();
        }else{
            $('#checkoutBillingAddress').slideDown();
            checkDniandVatNumber('invoice');
        }
        updateInvoiceStatus(this);
    });
    
    //Update cart on shipping change
    $('.supercheckout_shipping_option').live('change', function(){
	calledFromShipping = 1;
        updateCarrierOnDeliveryChange();
//	checkToDisplayRelayList();		//Uncomment for Mondial Relay Shipping Method
    });
    
    //Update cart summary on changing delivery extra parameters
    $('.supercheckout-delivery-extra').live('change', function(){
        updateDeliveryExtraChange();
    });
    $('#gift_message').live('blur', function(){
        updateDeliveryExtraChange();
    });
    
    
    //Change shipping methods and payment methods
    $('#shipping-existing select[name="shipping_address_id"]').live('change', function(e){
        buildAddressBlock($(this).val(), 'delivery');
        if($('#use_for_invoice').is(':checked')){
            updateInvoiceAddress();
        }
        if($('input[name="shipping_address_value"]:checked').val() == 0){
            loadCarriers();            
        }        
    });

    $('input[name="shipping_address_value"]').live('change', function(){
        shipping_address_value(this);
    });
    
    //Create payment state list based on selected payment country
    $('select[name="payment_address[id_country]"]').live('change', function(){
        var selected_country = $(this).find('option:selected').attr('value');
        var selected_state = 0;
        statelist(selected_country, selected_state,  'select[name="payment_address[id_state]"]');
        _loadInvoiceAddress();
        checkDniandVatNumber('invoice');
        checkZipCode(this, false);
    });
    $('select[name="shipping_address[id_state]"]').live('change', function(){
        _loadInvoiceAddress();
    });


    //Show or hide payment address form 
    $('input[name="payment_address_value"]').live('click', function(){
        if($(this).val() == 0){
            $('#payment-new').slideUp();
        }else if($(this).val() == 1){
            $('#payment-new').slideDown();
            checkDniandVatNumber('invoice');
            checkZipCode(this, false);
        }
        _loadInvoiceAddress();
    });

    //Show or hide gift comment 
    $('#gift').live('click', function(){
        if($(this).is(':checked')){
            $('#supercheckout-gift-comments').slideDown();
        }else{
            $('#supercheckout-gift-comments').slideUp();
        }
    });
    
    $('#payment-existing select[name="payment_address_id"]').live('change', function(e){
        buildAddressBlock($(this).val(), 'invoice');
        _loadInvoiceAddress();
        checkDniandVatNumber('invoice');
    });
    
    //Check Dni Number
    $('input[name="shipping_address[dni]"]').on('blur',function(){
        isValidDni('delivery');
    });
    $('input[name="payment_address[dni]"]').on('blur',function(){
        isValidDni('invoice');
    });
    
    //Check Vat Number
    $('input[name="shipping_address[vat_number]"]').on('blur',function(){
        isValidVatNumber('delivery');
    });
    $('input[name="payment_address[vat_number]"]').on('blur',function(){
        isValidVatNumber('invoice');
    });    
    
    
    //Check Zip/Postal Code required
    $('input[name="shipping_address[postcode]"]').on('blur',function(){
        checkZipCode(this, true);
    });
    $('input[name="payment_address[postcode]"]').on('blur',function(){
        checkZipCode(this, false);
    });
    
    //Remove Field Errors on active input of addresses
    $('#checkoutBillingAddress input, #checkoutShippingAddress input').on('focus', function(){
        $(this).parent().find('span.errorsmall').remove();
    });
    
    //Remove Field Errors on active input of checkout options
    $('input[name="supercheckout_email"], input[name="supercheckout_password"], input[name="customer_personal[password]"]').on('focus', function(){
        $(this).parent().find('span.errorsmall').remove();
    });
    $('.supercheckout_personal_dob select').on('focus', function(){
        $('.supercheckout_personal_dob').find('span.errorsmall').remove();
    });
    $('.supercheckout_personal_id_gender input').on('focus', function(){
        $('.supercheckout_personal_id_gender').find('span.errorsmall').remove();
    });
    $('.supercheckout_offers_option input').on('click', function(){
        $('.supercheckout_personal_id_gender').parent().parent().parent().parent().find('span.errorsmall').remove();
    });
    $('textarea[name="payment_address[other]"], textarea[name="shipping_address[other]"]').on('focus', function(){
        $(this).parent().find('span.errorsmall').remove();
    });
    
    //Confirm Order
    $("#supercheckout_confirm_order").click(function() {

    	/* openservis - Balikovna - begin */
		if (typeof CheckHardBalikovna === 'function' && CheckHardBalikovna() === false) {
		return false;
		}

        placeOrder();       
    });
    
    //trigger confirm order after confirming payment in dialog
    $("#velsof_payment_dialog #supercheckout_dialog_proceed").click(function() {
        confirmOrder();
    });
    
    //To close payment dialog box
    $('#velsof_payment_dialog .velsof_dialog_close, #velsof_payment_dialog #supercheckout_dialog_back').on('click', function(){
        hide_progress();
	proceed_to_payment=false; //important to set it to false else payment methods will be triggered on selection even without click on place order.
        $('#velsof_payment_dialog').hide();
    });
    
    // Login Action
    $('#button-login').live('click',function(){
        supercheckoutlogin();
    });
    
    $("#supercheckout-fieldset .orangebutton").mouseover(function() {
    if (button_background != 'F77219')
        $(this).css("background", ColorLuminance(button_background, -0.2));
    });
    $("#supercheckout-fieldset .orangebuttonsmall").mouseover(function() {
        if (button_background != 'F77219')
            $(this).css("background", ColorLuminance(button_background, -0.2));
    });
    $("#supercheckout-fieldset .orangebuttonapply").mouseover(function() {
        if (button_background != 'F77219')
            $(this).css("background", ColorLuminance(button_background, -0.2));
    });
    $("#supercheckout-fieldset .orangebutton").mouseout(function() {
    if (button_background != 'F77219')
        $(this).css("background", button_background);
    });
    $("#supercheckout-fieldset .orangebuttonsmall").mouseout(function() {
        if (button_background != 'F77219')
            $(this).css("background", button_background);
    });
    $("#supercheckout-fieldset .orangebuttonapply").mouseout(function() {
        if (button_background != 'F77219')
            $(this).css("background", button_background);
    });
    //Need to change the payment method label, if any has additional cost
    changePaymentMethodLabel();
    
    //quantitty change on blur
    $('.quantitybox').blur(function(){
	var name = $(this).attr("name");
	updateQty(name);
	
    });
    
    //on blur validation
    if (inline_validation == 1)
    {
	$('input[name="supercheckout_password"], input[name="customer_personal[password]"]').on('blur', function(){
	    if ($(this).val() == '')
	    {
		    $(this).removeClass('error-form');
		    $(this).removeClass('ok-form');
		    $(this).addClass('error-form');
		    $(this).parent().append('<span class="errorsmall">'+required_error+'</span>');
	    }
	    else if (!validatePasswd($(this).val()))
	    {
		    $(this).removeClass('error-form');
		    $(this).removeClass('ok-form');
		    $(this).addClass('error-form');
			    $(this).parent().append('<span class="errorsmall">'+pwd_error+'</span>');
	    }
	    else
	    {
		    $(this).removeClass('error-form');
		    $(this).removeClass('ok-form');
		    $(this).addClass('ok-form');
	    }
	});
	$('input[name="supercheckout_email"]').on('blur', function(){

	    if ($(this).val() == '')
	    {
		    $(this).removeClass('error-form');
		    $(this).removeClass('ok-form');
		    $(this).addClass('error-form');
		    $('input[name="supercheckout_email"]').parent().append('<span class="errorsmall">'+required_error+'</span>');
	    }else if (!validateEmail($(this).val())){
		    $(this).removeClass('error-form');
		    $(this).removeClass('ok-form');
		    $(this).addClass('error-form');
		    $('input[name="supercheckout_email"]').parent().append('<span class="errorsmall">'+invalid_email+'</span>');
	    }
	    else
	    {
		    $(this).removeClass('error-form');
		    $(this).removeClass('ok-form');
		    $(this).addClass('ok-form');
	    }
	});
	$('input[name="shipping_address[firstname]"], input[name="shipping_address[lastname]"], input[name="payment_address[firstname]"], input[name="payment_address[lastname]"] ').on('blur', function(){
		 if ($(this).siblings('.supercheckout-required').css('display') == "none" && $(this).val() == '')
		{
			$(this).removeClass('ok-form error-form');
		}
		else if ($(this).val() == ''){
			$(this).removeClass('ok-form').addClass('error-form');
			$(this).parent().append('<span class="errorsmall">'+required_error+'</span>');
		}
		else if (!validateName($(this).val()))
		{
			$(this).removeClass('ok-form').addClass('error-form');
			if (validateOnlyNumber($(this).val()))
			{
				$(this).parent().append('<span class="errorsmall">'+number_error+'</span>');
			}
			else
			{
				$(this).parent().append('<span class="errorsmall">'+splchar_error+'</span>');
			}
			
		}
		else if (validateName($(this).val()))
		{
			$(this).removeClass('error-form').addClass('ok-form');
		}
	});
	$('input[name="shipping_address[address1]"], input[name="payment_address[address1]"], input[name="shipping_address[address2]"], input[name="payment_address[address2]"]').on('blur', function(){
		if ($(this).siblings('.supercheckout-required').css('display') == "none" && $(this).val() == '')
		{
			$(this).removeClass('ok-form error-form');
		}
		else if ($(this).val() == ''){
			$(this).removeClass('ok-form').addClass('error-form');
			$(this).parent().append('<span class="errorsmall">'+required_error+'</span>');
		}
		else if (!validateAddress($(this).val()))
		{
			$(this).removeClass('ok-form').addClass('error-form');
			$(this).parent().append('<span class="errorsmall">'+invalid_address+'</span>');
		}
		else if (validateAddress($(this).val()))
		{
			$(this).removeClass('error-form').addClass('ok-form');
		}
	});
	$('input[name="shipping_address[city]"], input[name="payment_address[city]"]').on('blur', function(){
		if ($(this).siblings('.supercheckout-required').css('display') == "none" && $(this).val() == '')
		{
			$(this).removeClass('ok-form error-form');
		}
		else if ($(this).val() == ''){
			$(this).removeClass('ok-form').addClass('error-form');
			$(this).parent().append('<span class="errorsmall">'+required_error+'</span>');
		}
		else if (!validateCityName($(this).val()))
		{
			$(this).removeClass('ok-form').addClass('error-form');
			$(this).parent().append('<span class="errorsmall">'+invalid_city+'</span>');
		}
		else if (validateCityName($(this).val()))
		{
			$(this).removeClass('error-form').addClass('ok-form');
		}
	});
	$('input[name="payment_address[alias]"], input[name="shipping_address[alias]"]').on('blur', function(){

		if ($(this).siblings('.supercheckout-required').css('display') == "none" && $(this).val() == '')
		{
			$(this).removeClass('ok-form error-form');
		}
		else if ($(this).val() == ''){
			$(this).removeClass('ok-form').addClass('error-form');
			$(this).parent().append('<span class="errorsmall">'+required_error+'</span>');
		}
		else if (!validateAddressTitle($(this).val()))
		{
			$(this).removeClass('ok-form').addClass('error-form');
			$(this).parent().append('<span class="errorsmall">'+invalid_title+'</span>');
		}
		else if (validateAddressTitle($(this).val()))
		{
			$(this).removeClass('error-form').addClass('ok-form');
		}
	});
	$('input[name="shipping_address[company]"], input[name="payment_address[company]"]').on('blur', function(){
		if ($(this).siblings('.supercheckout-required').css('display') == "none" && $(this).val() == '')
		{
			$(this).removeClass('ok-form error-form');
		}
		else if ($(this).siblings('.supercheckout-required').css('display') != "none" && $(this).val() == '')
		{
			$(this).removeClass('ok-form').addClass('error-form');
			$(this).parent().append('<span class="errorsmall">'+required_error+'</span>');
		}
		else if ($(this).val() != '')
		{
			$(this).removeClass('error-form').addClass('ok-form');
		}

	});
	$('input[name="shipping_address[phone]"], input[name="shipping_address[phone_mobile]"], input[name="payment_address[phone]"], input[name="payment_address[phone_mobile]"]').on('blur', function(){
	      if ($(this).siblings('.supercheckout-required').css('display') == "none" && $(this).val() == '')
		{
			$(this).removeClass('ok-form error-form');
		}
		else if ($(this).val() == ''){
			$(this).removeClass('ok-form').addClass('error-form');
			$(this).parent().append('<span class="errorsmall">'+required_error+'</span>');
		}
		else if (!validatePhoneNumber($(this).val()))
		{
			$(this).removeClass('ok-form').addClass('error-form');
			$(this).parent().append('<span class="errorsmall">'+invalid_number+'</span>');
		}
		else if (validatePhoneNumber($(this).val()))
		{
			$(this).removeClass('error-form').addClass('ok-form');
		}   
	});
	$('textarea[name="payment_address[other]"], textarea[name="shipping_address[other]"]').on('blur', function(){
		 if ($(this).siblings('.supercheckout-required').css('display') == "none" && $(this).val() == '')
		{
			$(this).removeClass('ok-form error-form');
		}
		else if ($(this).val() == ''){
			$(this).removeClass('ok-form').addClass('error-form');
			$(this).parent().append('<span class="errorsmall">'+required_error+'</span>');
		}
		else if (!validateMessage($(this).val()))
		{
			$(this).removeClass('ok-form').addClass('error-form');
			$(this).parent().append('<span class="errorsmall">'+invalid_other_info+'</span>');
		}
		else if (validateMessage($(this).val()))
		{
			$(this).removeClass('error-form').addClass('ok-form');
		}
	});
	$('.supercheckout_personal_dob > div > select').on('change', function(){
		var flag = 0;
		$('.supercheckout_personal_dob > div > select').each(function(){
			if (this.value == '')
			{
				$(this).addClass('dob-error-form').removeClass('dob-ok-form');
				flag = 1;

			}
			else
			{
			      $(this).addClass('dob-ok-form').removeClass('dob-error-form');

			}
		})
		if (flag == 1)
		{
		       $('.supercheckout_personal_dob > div').css("width","240px").addClass('dob-div-error-form').removeClass('dob-div-ok-form');
		       $('.supercheckout_personal_dob').append('<span class="errorsmall">'+invalid_dob+'</span>');
		}
		else
		{
		       $('.supercheckout_personal_dob > div').css("width","240px").addClass('dob-div-ok-form').removeClass('dob-div-error-form');
		}
	})
    }
    $('.colorbox').colorbox({
	width: 640,
	height: 480
});
});



function shipping_address_value(e){ // @Nitin Jain - to show address form for logged in customer, if new address is selected.
    
    var loadcarriers = false;
        if($(e).val() == 0){
            loadcarriers = true;
            $('#shipping-new').slideUp();            
        }else if($(e).val() == 1){
            $('#shipping-new').slideDown();
            checkDniandVatNumber('delivery');
            if($('input[name="shipping_address[postcode]"]').length && $('input[name="shipping_address[postcode]"]').val() != ''){
                checkZipCode(this, true);
            }else{
                loadcarriers = true;
            }
        }
        if(loadcarriers){
            loadCarriers();
        }
}
function checkout_option(e){
	
    if(iscartvirtual != true){
	    if($(e).val() == 0){
            $('#supercheckout-login-box').show();
            $('#supercheckout-new-customer-form').hide();
            $('#social_login_block').show();
            $('#new_customer_password').hide();
	    $('#checkoutShippingAddress').hide();
	    $('#checkoutBillingAddress').hide();
        }else if($(e).val() == 1){
	    
	    if(!$('#use_for_invoice').is(':checked')){
		$('#checkoutBillingAddress').show();
	    }
            $('#supercheckout-login-box').hide();
            $('#new_customer_password').hide();
            $('#social_login_block').hide();
            $('#supercheckout-new-customer-form').show();
	    $('#checkoutShippingAddress').show();
        }else{
	    if(!$('#use_for_invoice').is(':checked')){
		$('#checkoutBillingAddress').show();
	    }
            $('#supercheckout-login-box').hide();
            $('#new_customer_password').show();
            $('#social_login_block').show();
            $('#supercheckout-new-customer-form').show();
	    $('#checkoutShippingAddress').show();
        }
    }
    else // because in case of virtual cart we need to hide delivery address block
     if(iscartvirtual == true){
	if($(e).val() == 0){ 
            $('#supercheckout-login-box').show();
            $('#supercheckout-new-customer-form').hide();
            $('#social_login_block').show();
            $('#new_customer_password').hide();
	    $('#checkoutShippingAddress').hide();
	    $('#checkoutBillingAddress').hide();
        }else if($(e).val() == 1){
            $('#supercheckout-login-box').hide();
            $('#new_customer_password').hide();
            $('#social_login_block').hide();
            $('#supercheckout-new-customer-form').show();
	    $('#checkoutShippingAddress').hide();
	    $('#use_for_invoice').prop('checked', false);
	    $('#checkoutBillingAddress').slideDown();
        }else{
            $('#supercheckout-login-box').hide();
            $('#new_customer_password').show();
            $('#social_login_block').show();
            $('#supercheckout-new-customer-form').show();
	    $('#checkoutShippingAddress').hide();
	    $('#use_for_invoice').prop('checked', false);
	    $('#checkoutBillingAddress').slideDown();
        }
    }
    
}

function checkAction(e)
{
    if (typeof e == 'undefined' && window.event) { e = window.event; }
    if (e.keyCode == 13)
    {
        supercheckoutlogin();
    }
}

function supercheckoutlogin()
{
    $.ajax( {
        type: "POST",
        url: $('#module_url').val()+'&ajax=true',
        data: $('input:text[name="supercheckout_email"], #supercheckout-login-box input'),
        dataType: 'json',
        beforeSend: function() {
            $('#button-login').parent().find('img').show();
            $('#checkoutLogin .supercheckout-checkout-content .permanent-warning').remove();
            $('.errorsmall').remove();
        },
        complete: function() {

        },
        success: function( json ) {
            if(json['success'] != undefined){
                location.href = json['success'];
            }else if(json['error']['general'] != undefined){
                $('#button-login').parent().find('img').hide();
               $('#checkoutLogin .supercheckout-checkout-content').html('<div class="permanent-warning">'+json['error']['general']+'</div>');
            }else{
                $('#button-login').parent().find('img').hide();
                if(json['error']['email'] != undefined){
                   $('#checkoutLogin input:text[name="supercheckout_email"]').parent().append('<span class="errorsmall">'+json['error']['email']+'</span>');
		   if (inline_validation == 1)
			$('#checkoutLogin input:text[name="supercheckout_email"]').addClass('error-form').removeClass('ok-form');
                }
                if(json['error']['password'] != undefined){
                   $('#supercheckout-login-box input:password[name="supercheckout_password"]').parent().append('<span class="errorsmall">'+json['error']['password']+'</span>');
		   if (inline_validation == 1)
			$('#supercheckout-login-box input:password[name="supercheckout_password"]').addClass('error-form').removeClass('ok-form');
                }   
            }            
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            var errors = sprintf(ajaxRequestFailedMsg, XMLHttpRequest, textStatus);
            $('#checkoutLogin .supercheckout-checkout-content').html('<div class="permanent-warning">'+errors+'</div>');
        }
    } );    
}

function checkStateVisibility(selected_country, element){
    var state_html = ''; //<option value="0">Select State</option>
    var has_states = false;
    var show_state = false;
    for (var id_country in countries){
        if(id_country == selected_country){
            if(countries[id_country]['contains_states'] == 1){
                has_states = true;                   
            }
        }        
    }
    if(element.indexOf("shipping")>=0 && show_shipping_state == 1){
        show_state = true;
    }
    else if(element.indexOf("payment")>=0 && show_payment_state == 1){
        show_state = true;
    }
    
    if(has_states && show_state){
        return true;
    }else{
        return false;
    }
}

function statelist(selected_country, selected_state, element){
    var state_html = ''; //<option value="0">Select State</option>
    var has_states = false;
    var show_state = false;
    for (var id_country in countries){
        if(id_country == selected_country){
            if(countries[id_country]['contains_states'] == 1){
                has_states = true;
                for (var i in countries[id_country]['states']){
                    if(countries[id_country]['states'][i]['id_state'] == selected_state){
                        state_html += '<option value="'+countries[id_country]['states'][i]['id_state']+'" selected="selected" >'+countries[id_country]['states'][i]['name']+'</option>';
                    }else{
                        state_html += '<option value="'+countries[id_country]['states'][i]['id_state']+'">'+countries[id_country]['states'][i]['name']+'</option>';
                    }

                }    
            }
        }
        
    }
    if(element.indexOf("shipping")>=0 && show_shipping_state == 1){
        show_state = true;
    }
    else if(element.indexOf("payment")>=0 && show_payment_state == 1){
        show_state = true;
    }
    
    if(has_states && show_state){
        $(element).html(state_html);
        $(element).parent().parent().show();
    }else{
        $(element).parent().parent().hide();
    }
    
}

function updateInvoiceAddress(){
    $('select[name="payment_address_id"] option').removeAttr('selected');
    $('select[name="payment_address_id"] option').each(function(){
        if($(this).val() == $('select[name="shipping_address_id"]').find('option:selected').attr('value')){
            $(this).attr('selected', 'selected');
        }
    });
    buildAddressBlock($('select[name="payment_address_id"] option:selected').val(), 'invoice');
    $('input[name="payment_address_value"]').removeAttr('checked');
        $('input[name="payment_address_value"]').parent().removeClass('checked');
        $('input[name="payment_address_value"]').each(function(){
        if($(this).val() == $('input[name="shipping_address_value"]:checked').val()){
            $(this).attr('checked', 'checked');
            $(this).parent().addClass('checked');
        }
    });
    if($('input[name="payment_address_value"]:checked').val() == 0){
        $('#payment-new').slideUp();
    }

    $('select[name="payment_address[id_country]"] option').removeAttr('selected');
    $('select[name="payment_address[id_country]"] option').each(function(){
        if($(this).val() == $('select[name="shipping_address[id_country]"]').find('option:selected').attr('value')){
            $(this).attr('selected', 'selected');
        }
    });

    var selected_country = $('select[name="shipping_address[id_country]"]').find('option:selected').attr('value');
    var selected_state = 0;
    statelist(selected_country, selected_state, 'select[name="payment_address[id_state]"]');

    $('select[name="payment_address[id_state]"] option').removeAttr('selected');
    $('select[name="payment_address[id_state]"] option').each(function(){
        if($(this).val() == $('select[name="shipping_address[id_state]"]').find('option:selected').attr('value')){
            $(this).attr('selected', 'selected');
        }
    });    
}

function displayAddressDetail(){
    if (typeof formatedAddressFieldsValuesList !== 'undefined' && formatedAddressFieldsValuesList != null){
        buildAddressBlock($('select[name="payment_address_id"] option:selected').val(),'delivery');
        buildAddressBlock($('select[name="shipping_address_id"] option:selected').val(),'invoice');
    }
}

function buildAddressBlock(id_address, type){
    if((typeof formatedAddressFieldsValuesList != 'undefined') && formatedAddressFieldsValuesList != null){
        var html = '';
        var reg = /[\s,]+/;
        var field = '';
        var keys = '';
        for (var i in formatedAddressFieldsValuesList[id_address]['ordered_fields']){
            field = formatedAddressFieldsValuesList[id_address]['ordered_fields'][i];
            if(reg.test(field)){
                keys = field.split(reg);
                var values = '';
                for (var j in keys){
                    values += formatedAddressFieldsValuesList[id_address]['formated_fields_values'][keys[j]]+' ';                
                }
                html += '<span>'+values+'</span>';
            }else{
                html += '<span>'+formatedAddressFieldsValuesList[id_address]['formated_fields_values'][field]+'</span>';
            }  
        }
        $('#'+type+'_address_detail').html(html);        
    }
}


function checkDniandVatNumber(type){
    
    var id_country = $('select[name="shipping_address[id_country]"] option:selected').val();
    if(type == 'invoice'){
        id_country = $('select[name="payment_address[id_country]"] option:selected').val();
    }
    $.ajax({
        type: 'POST',
        headers: { "cache-control": "no-cache" },
        url: $('#module_url').val() + '&rand=' + new Date().getTime(),
        async: true,
        cache: false,
        dataType : "json",
        data: 'ajax=true'
            +'&method=checkDniandVat'
            +'&id_country='+id_country
            +'&token=' + static_token,
        beforeSend: function() {
            hideGeneralError();
        },
        complete: function() {},
        success: function(jsonData){
            if(type == 'delivery'){
                if(jsonData['is_need_dni']){
                    $('input[name="shipping_address[dni]"]').parent().parent().show();
                }else{
                    $('input[name="shipping_address[dni]"]').attr('value', '');
                    $('input[name="shipping_address[dni]"]').parent().parent().hide();
                }
                if(jsonData['is_need_vat']){
                    $('input[name="shipping_address[vat_number]"]').parent().parent().show();
                }else{
                    $('input[name="shipping_address[vat_number]"]').attr('value', '');
                    $('input[name="shipping_address[vat_number]"]').parent().parent().hide();
                }
                if(jsonData['is_need_states'] && show_shipping_state == 1){
                    $('select[name="shipping_address[id_state]"]').parent().parent().show();
                }else{
                    $('select[name="shipping_address[id_state]"]').removeAttr('selected');
                    $('select[name="shipping_address[id_state]"]').parent().parent().hide();
                }
                if(jsonData['is_need_zip_code'] != 0 && show_shipping_postcode==1){
                    $('input[name="shipping_address[postcode]"]').parent().parent().show();
                }else{
                    $('input[name="shipping_address[postcode]"]').attr('value', '');
                    $('input[name="shipping_address[postcode]"]').parent().parent().hide();
                }
            }
            if(type == 'invoice'){
                if(jsonData['is_need_dni']){
                    $('input[name="payment_address[dni]"]').parent().parent().show();
                }else{
                    $('input[name="payment_address[dni]"]').attr('value', '');
                    $('input[name="payment_address[dni]"]').parent().parent().hide();
                }
                if(jsonData['is_need_vat']){
                    $('input[name="payment_address[vat_number]"]').parent().parent().show();
                }else{
                    $('input[name="payment_address[vat_number]"]').attr('value', '');
                    $('input[name="payment_address[vat_number]"]').parent().parent().hide();
                }
                if(jsonData['is_need_states'] && show_payment_state == 1){
                    $('select[name="payment_address[id_state]"]').parent().parent().show();
                }else{
                    $('select[name="payment_address[id_state]"]').removeAttr('selected');
                    $('select[name="payment_address[id_state]"]').parent().parent().hide();
                }
                if(jsonData['is_need_zip_code'] != 0 && show_payment_postcode==1){
                    $('input[name="payment_address[postcode]"]').parent().parent().show();
                }else{
                    $('input[name="payment_address[postcode]"]').attr('value', '');
                    $('input[name="payment_address[postcode]"]').parent().parent().hide();
                }
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            var errors = sprintf(ajaxRequestFailedMsg, XMLHttpRequest, textStatus);
            $('#checkoutShippingAddress .supercheckout-checkout-content .permanent-warning').html(errors);
        }
    });    
}

function isValidDni(type){    
    var id_country = $('select[name="shipping_address[id_country]"] option:selected').val();
    var dni = $('input[name="shipping_address[dni]"]').val();
    if(type == 'invoice'){
        id_country = $('select[name="payment_address[id_country]"] option:selected').val();
        dni = $('input[name="payment_address[dni]"]').val();
    }
    $.ajax({
        type: 'POST',
        headers: { "cache-control": "no-cache" },
        url: $('#module_url').val() + '&rand=' + new Date().getTime(),
        async: true,
        cache: false,
        dataType : "json",
        data: 'ajax=true'
            +'&method=isValidDni'
            +'&id_country='+id_country
            +'&dni='+dni
            +'&token=' + static_token,
        beforeSend: function() {
            hideGeneralError();
	    if (inline_validation == 1)
	    {
		    if(type == 'delivery'){
		    $('input[name="shipping_address[dni]"]').removeClass('ok-form error-form');
                }
                if(type == 'invoice'){
		    $('input[name="payment_address[dni]"]').removeClass('ok-form error-form');
                }
	    }
	    
        },
        complete: function() {},
        success: function(jsonData){
            if(jsonData['error'] != undefined){
                if(type == 'delivery'){
                    $('input[name="shipping_address[dni]"]').parent().append('<span class="errorsmall">'+jsonData['error']+'</span>');
		    if (inline_validation == 1)
			$('input[name="shipping_address[dni]"]').addClass('error-form');
                }
                if(type == 'invoice'){
                    $('input[name="payment_address[dni]"]').parent().append('<span class="errorsmall">'+jsonData['error']+'</span>');
		    if (inline_validation == 1)
			$('input[name="payment_address[dni]"]').addClass('error-form');
                }
            }else
	    {
		if (inline_validation == 1)
		{
			if (type == 'delivery') {
				$('input[name="shipping_address[dni]"]').addClass('ok-form');
			}
			if (type == 'invoice') {
				$('input[name="payment_address[dni]"]').addClass('ok-form');
			}
		}
		    
	    }
            
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            var errors = sprintf(ajaxRequestFailedMsg, XMLHttpRequest, textStatus);
            displayGeneralError(errors)
        }
    });    
}

function isValidVatNumber(type){
    var id_country = $('select[name="shipping_address[id_country]"] option:selected').val();
    var vat_number = $('input[name="shipping_address[vat_number]"]').val();
    if(type == 'invoice'){
        id_country = $('select[name="payment_address[id_country]"] option:selected').val();
        vat_number = $('input[name="payment_address[vat_number]"]').val();
    }
    $.ajax({
        type: 'POST',
        headers: { "cache-control": "no-cache" },
        url: $('#module_url').val() + '&rand=' + new Date().getTime(),
        async: true,
        cache: false,
        dataType : "json",
        data: 'ajax=true'
            +'&method=isValidVatNumber'
            +'&id_country='+id_country
            +'&vat_number='+vat_number
            +'&token=' + static_token,
        beforeSend: function() {
            hideGeneralError();
	    if (inline_validation == 1)
	    {
		    if(type == 'delivery'){
		    $('input[name="shipping_address[vat_number]"]').removeClass('ok-form error-form');
                }
                if(type == 'invoice'){
		    $('input[name="payment_address[vat_number]"]').removeClass('ok-form error-form');
                }
	    }
	    
        },
        complete: function() {},
        success: function(jsonData){
		console.log(jsonData);
            if(jsonData['error'] != undefined){
                var errors = jsonData['error'].join('<br>');
                if(type == 'delivery'){
                    $('input[name="shipping_address[vat_number]"]').parent().append('<span class="errorsmall">'+errors+'</span>');
		    if (inline_validation == 1)
			$('input[name="shipping_address[vat_number]"]').addClass('error-form');
                }
                if(type == 'invoice'){
                    $('input[name="payment_address[vat_number]"]').parent().append('<span class="errorsmall">'+errors+'</span>');
		    if (inline_validation == 1)
			$('input[name="payment_address[vat_number]"]').addClass('error-form');
                }
            } else {
				if (inline_validation == 1)
				{
					if (type == 'delivery')
					{
						if ($('input[name="shipping_address[vat_number]"]').siblings('.supercheckout-required').css('display') == "none" && $('input[name="shipping_address[vat_number]"]').val() == '')
						{
							$('input[name="shipping_address[vat_number]"]').removeClass('ok-form error-form');
						}
						else
						{
							$('input[name="shipping_address[vat_number]"]').addClass('ok-form');
						}

					}
					if (type == 'invoice')
					{
						if ($('input[name="payment_address[vat_number]"]').siblings('.supercheckout-required').css('display') == "none" && $('input[name="payment_address[vat_number]"]').val() == '')
						{
							$('input[name="payment_address[vat_number]"]').removeClass('ok-form error-form');
						}
						else
						{
							$('input[name="payment_address[vat_number]"]').addClass('ok-form');
						}

					}
				}

			}
            
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            var errors = sprintf(ajaxRequestFailedMsg, XMLHttpRequest, textStatus);
            displayGeneralError(errors)
        }
    });
}


function updateInvoiceStatus(element){
    $.ajax({
        type: 'POST',
        headers: { "cache-control": "no-cache" },
        url: $('#module_url').val() + '&rand=' + new Date().getTime(),
        async: true,
        cache: false,
        dataType : "json",
        data: 'ajax=true'
            +'&method=setSameInvoice'
            +'&use_for_invoice='+(($(element).is(':checked'))?'1':'0')
            +'&token=' + static_token,
        beforeSend: function() {
            $('.input-different-shipping').parent().find('.errorsmall').remove();
        },
        complete: function() {},
        success: function(jsonData){},
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            var errors = sprintf(ajaxRequestFailedMsg, XMLHttpRequest, textStatus);
            $('.input-different-shipping').parent().append('<div class="errorsmall">'+errors+'</div>');
        }
    });    
}

function _loadInvoiceAddress(){
    var id_country = 0;
    var id_address_invoice = '';
    if($('input[name="payment_address_value"]').length){
        if($('input[name="payment_address_value"]:checked').val() == 1){
            id_country = $('select[name="payment_address[id_country]"] option:selected').val();
        }else if($('input[name="payment_address_value"]:checked').val() == 0){
            id_address_invoice = $('select[name="payment_address_id"] option:selected').val();
        }
    }else{
        id_country = $('select[name="payment_address[id_country]"] option:selected').val();
    }
    var id_state = $('select[name="payment_address[id_state]"]').val();
    var postcode = $('input[name="payment_address[postcode]"]').val();
    var city = $('input[name="payment_address[city]"]').val();
    $.ajax({
        type: 'POST',
        headers: { "cache-control": "no-cache" },
        url: $('#module_url').val() + '&rand=' + new Date().getTime(),
        async: true,
        cache: false,
        dataType : "json",
        data: 'ajax=true'
            +'&method=loadInvoiceAddress'
            +'&id_country='+id_country
            +'&id_state='+id_state
            +'&postcode='+postcode
            +'&city='+city
            +'&id_address_invoice='+id_address_invoice
            +'&token=' + static_token,
        beforeSend: function() {
            hideGeneralError();
        },
        complete: function() {},
        success: function(jsonData){},
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            var errors = sprintf(ajaxRequestFailedMsg, XMLHttpRequest, textStatus);
            displayGeneralError(errors);
        }
    });    
}

function getCounrtryAndIdDelivery(){
    var id_country = 0;
    var id_address_delivery = '';
    if($('input[name="shipping_address_value"]').length){
        if($('input[name="shipping_address_value"]:checked').val() == 1){
            id_country = $('select[name="shipping_address[id_country]"] option:selected').val();
        }else if($('input[name="shipping_address_value"]:checked').val() == 0){
            id_address_delivery = $('select[name="shipping_address_id"] option:selected').val();
        }
    }else{
        id_country = $('select[name="shipping_address[id_country]"] option:selected').val();
    }
    
    var arr = [];
    arr.push(id_country);
    arr.push(id_address_delivery);
    return arr;
}

var shipping_error_found_on_load = false;
function loadCarriers(){
    var requestParam = getCounrtryAndIdDelivery();
    var id_country = requestParam[0];
    var id_state = 0;
    if(checkStateVisibility(id_country, 'select[name="shipping_address[id_state]"]')) {
        id_state = $('select[name="shipping_address[id_state]"]').val();
    }
    var postcode = $('input[name="shipping_address[postcode]"]').val();
    var city = $('input[name="shipping_address[city]"]').val();
    var id_address_delivery = requestParam[1];
    shipping_error_found_on_load = false;
    $.ajax({
            type: 'POST',
            headers: { "cache-control": "no-cache" },
            url: $('#module_url').val() + '&rand=' + new Date().getTime(),
            async: true,
            cache: false,
            dataType : "json",
            data: 'ajax=true'
                +'&id_country='+id_country
                +'&id_state='+id_state
                +'&postcode='+postcode
                +'&city='+city
                +'&id_address_delivery='+id_address_delivery
                +'&method=loadCarriers&token=' + static_token,
            beforeSend: function() {
                $('#shippingMethodLoader').show();
                $('#shipping-method .supercheckout-checkout-content').find('.permanent-warning').html('');
            },
            complete: function() {
                //$('#shippingMethodLoader').hide();
            },
            success: function(jsonData)
            {
                carriers_count = jsonData['carriers_count'];
                is_cart_virtual = jsonData['is_cart_virtual'];   
		$('#hook-extracarrier').html(jsonData['HOOK_EXTRACARRIER']);
                if(jsonData['hasError']){
                    $('#shipping-method .supercheckout-checkout-content').html('<div class="permanent-warning">'+jsonData['errors'][0]+'</div>');
                    shipping_error_found_on_load = true;
                }else{
                    shipping_error_found_on_load = false;
                }
                if (calledFromShipping == 0)
			$('#shipping-method').html(jsonData['carrier_block']);

		calledFromShipping = 0;

                updateShippingExtra(jsonData);                    
                set_column_inside_height();
                //updateCartSummary(jsonData[0].summary);
                updateCarriers();
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                var errors = sprintf(ajaxRequestFailedMsg, XMLHttpRequest, textStatus);
                $('#shipping-method .supercheckout-checkout-content').html('<div class="permanent-warning">'+errors+'</div>');
            }
    });    
}


function updateCarriers(){
    var delivery_option = ($('.supercheckout_shipping_option').length)? '&'+$('.supercheckout_shipping_option:checked').attr('name')+'='+$('.supercheckout_shipping_option:checked').attr('value') : '';
    $.ajax({
            type: 'POST',
            headers: { "cache-control": "no-cache" },
            url: $('#module_url').val() + '&rand=' + new Date().getTime(),
            async: true,
            cache: false,
            dataType : "json",
            data: 'ajax=true'
                +delivery_option
                +'&method=updateCarrier&token=' + static_token,
            beforeSend: function() {
                if(!shipping_error_found_on_load && !is_cart_virtual){
                    $('#shipping-method .supercheckout-checkout-content').find('.permanent-warning').remove();
                }
                $('#shippingMethodLoader').show();
            },
            complete: function() {
                $('#shippingMethodLoader').hide();
            },
            success: function(jsonData)
            {
                if(jsonData['hasError']){
                    if(jsonData['errors'][0] != undefined && jsonData['errors'][0] != ''){
                        $('#shipping-method .supercheckout-checkout-content').html('<div class="permanent-warning">'+jsonData['errors'][0]+'</div>');
                    }
                }
                loadPayments();
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                var errors = sprintf(ajaxRequestFailedMsg, XMLHttpRequest, textStatus);
                $('#shipping-method .supercheckout-checkout-content').html('<div class="permanent-warning">'+errors+'</div>');
            }
    });    
}

function loadPayments(){
    var requestParam = getCounrtryAndIdDelivery();
    var selected_payment_method_id=$('input:radio[name="payment_method"]:checked').val(); // getting value of selected payment methods
    $.ajax({
        type: 'POST',
        headers: { "cache-control": "no-cache" },
        url: $('#module_url').val() + '&rand=' + new Date().getTime(),
        async: true,
        cache: false,
        dataType : "json",
        data: 'ajax=true'
            +'&id_country='+requestParam[0]
            +'&id_address_delivery='+requestParam[1]
		+'&selected_payment_method_id='+selected_payment_method_id
            +'&method=loadPayment&token=' + static_token,
        beforeSend: function() {
            $('#payment-method .supercheckout-checkout-content').find('.permanent-warning').html('');            
            $('#paymentMethodLoader').show();
        },
        complete: function() {
            $('#paymentMethodLoader').hide();
        },
        success: function(jsonData)
        {
            $('#payment-method').html(jsonData['payment_method']);
            
            if(jsonData['payment_method_list']['methods'] != undefined && jsonData['payment_method_list']['methods'].length){
                $('#payment_method_default_description').html('');
                var description_html = '';
                for (var i in jsonData['payment_method_list']['methods']){
                    description_html += '<div id="paymentmodule_'+jsonData['payment_method_list']['methods'][i]['id_module']+'_'+jsonData['payment_method_list']['methods'][i]['name']+'">';
                    description_html += jsonData['payment_method_list']['methods'][i]['html'];
                    description_html += '</div>';
                }
                $('#payment_method_default_description').html(description_html);
            }
            
            if($('input:radio[name="payment_method"]').length && !$('input:radio[name="payment_method"]:checked').length){
                $('#payment_display_block .supercheckout-payment-info').hide();
                $('#display_payment').html('');
            }
			if (typeof changePaymentMethodFee == 'function') {
				changePaymentMethodFee();
            }
            changePaymentMethodLabel();
            set_column_inside_height();
            loadCart();
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            var errors = sprintf(ajaxRequestFailedMsg, XMLHttpRequest, textStatus);
            $('#payment-method .supercheckout-checkout-content').html('<div class="permanent-warning">'+errors+'</div>');
        }
    });    
}

function updateCarrierOnDeliveryChange(){
    var delivery_option = ($('.supercheckout_shipping_option').length)? '&'+$('.supercheckout_shipping_option:checked').attr('name')+'='+$('.supercheckout_shipping_option:checked').attr('value') : '';

    $.ajax({
            type: 'POST',
            headers: { "cache-control": "no-cache" },
            url: $('#module_url').val() + '&rand=' + new Date().getTime(),
            async: true,
            cache: false,
            dataType : "json",
            data: 'ajax=true'
                +delivery_option
                +'&method=updateCarrier&token=' + static_token,
            beforeSend: function() {
                $('#shipping-method .supercheckout-checkout-content').find('.permanent-warning').html('');
                $('#shippingMethodLoader').show();
            },
            complete: function() {
                $('#shippingMethodLoader').hide();
            },
            success: function(jsonData)
            {
                if(jsonData['hasError']){
                    $('#shipping-method .supercheckout-checkout-content').html('<div class="permanent-warning">'+jsonData['errors'][0]+'</div>');
                }
                loadCarriers();
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                var errors = sprintf(ajaxRequestFailedMsg, XMLHttpRequest, textStatus);
                $('#shipping-method .supercheckout-checkout-content').html('<div class="permanent-warning">'+errors+'</div>');
            }
    });    
}

function updateDeliveryExtraChange(){
    var messagePattern = /[<>{}]/i;
    var gift_message = '';
    var extrasError = false;
    if($('#gift').length && $('#gift').is(':checked')){
        gift_message = $('#gift_message').val();
        if(messagePattern.test(gift_message)){
            extrasError = true;
            $('#gift_message').parent().append('<span class="errorsmall">'+commentInvalid+'</span>');
        }
    }
    
    if(!extrasError){
        var recycle = ($('#recyclable').length && $('#recyclable').is(':checked'))?1:0;
        var gift = ($('#gift').length && $('#gift').is(':checked'))?1:0;
        gift_message = $('#gift_message').val();
        $.ajax({
                type: 'POST',
                headers: { "cache-control": "no-cache" },
                url: $('#module_url').val() + '&rand=' + new Date().getTime(),
                async: true,
                cache: false,
                dataType : "json",
                data: 'ajax=true'
                    +'&recycle='+recycle
                    +'&gift='+gift
                    +'&gift_message='+gift_message
                    +'&method=updateDeliveryExtra&token=' + static_token,
                beforeSend: function() {
                    $('#supercheckout-empty-page-content').find('.permanent-warning').html('');
                },
                success: function(jsonData)
                {
                    if(jsonData['hasError']){
                        var arr = jsonData['errors'];
                        $('#supercheckout-empty-page-content').html('<div class="permanent-warning">'+arr.join('<br>')+'</div>');
                        $("html, body").animate({ scrollTop: 0 }, "fast");
                    }
                    loadCart();
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    var errors = sprintf(ajaxRequestFailedMsg, XMLHttpRequest, textStatus);
                    displayGeneralError(errors);
                }
        });        
    }
}

function loadCart(){
    $.ajax({
            type: 'POST',
            headers: { "cache-control": "no-cache" },
            url: $('#module_url').val() + '&rand=' + new Date().getTime(),
            async: true,
            cache: false,
            dataType : "json",
            data: 'ajax=true'
                +'&method=loadCart&token=' + static_token,
            beforeSend: function() {
                $('#cart_update_warning .permanent-warning').remove();
                $('#confirmLoader').show();
            },
            success: function(jsonData)
            {
                $('#confirmLoader').hide();
                updateCartSummary(jsonData);
                
                //Update Payment Information
                if($('input:radio[name="payment_method"]:checked').length){
                    actionOnPaymentSelect($('input:radio[name="payment_method"]:checked'));
                }else{
                    $('input:radio[name="payment_method"]').first().attr('checked', 'checked');
                    $('input:radio[name="payment_method"]').first().parent().addClass('checked');
                    actionOnPaymentSelect($('input:radio[name="payment_method"]:checked'));
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                var errors = sprintf(ajaxRequestFailedMsg, XMLHttpRequest, textStatus);
                $('#cart_update_warning').html('<div class="permanent-warning">'+errors+'</div>');
            }
    });    
}

//Update Cart Quantity
function updateQty(element){
    $('#cart_update_warning .permanent-warning').remove();
    var exp = new RegExp("^[0-9]+$");
    var hidden = $('#confirmCheckout input[name=' + element + '_hidden]').val();
    var input = $('#confirmCheckout  input[name=' + element + ']').val();
    if (exp.test(input) == true){
        var QtyToUpDate = parseInt(input) - parseInt(hidden);
        var qty = QtyToUpDate;
        var updateQuantity = true;
        var id = element.replace('quantity_', '');
        if(parseInt(QtyToUpDate) < 0){
            var val = $('input[name=' + element + ']').val();
            var newVal = val;
            if(typeof(qty) == 'undefined' || !qty){
                    qty = 1;
                    newVal = val - 1;
            }else if (qty < 0)
                    qty = -qty;
            
            if (newVal > 0 || $('#product_' + id + '_gift').length){
                updateQuantity = true; //downQuantity(id.replace('quantity_', ''), QtyToUp);
            }else{
                updateQuantity = false;
            }
        }
        //console.log(input+'====='+hidden);
        if(updateQuantity){
            if (qty != 0){
                var customizationId = 0;
                var productId = 0;
                var productAttributeId = 0;
                var id_address_delivery = 0;
                var ids = 0;
                ids = id.split('_');
                productId = parseInt(ids[0]);
                var errors = '';

                if (typeof(ids[1]) !== 'undefined')
                        productAttributeId = parseInt(ids[1]);
                if (typeof(ids[2]) !== 'undefined' && ids[2] !== 'nocustom')
                        customizationId = parseInt(ids[2]);
                if (typeof(ids[3]) !== 'undefined')
                        id_address_delivery = parseInt(ids[3]);

                $.ajax( {
                   type: "POST",
                   headers: { "cache-control": "no-cache" },
                   url: baseUri + '?rand=' + new Date().getTime(),
                   data: 'controller=cart'
                                    + '&ajax=true'
                                    + '&add=true'
                                    + '&getproductprice=true'
                                    + '&summary=true'
                                    + '&id_product='+productId
                                    + '&ipa='+productAttributeId
                                    + '&id_address_delivery='+id_address_delivery
                                    + ((parseInt(QtyToUpDate) < 0) ? '&op=down': '')
                                    //+ '&op=down'
                                    + ((customizationId !== 0) ? '&id_customization='+customizationId : '')
                                    + '&qty='+qty
                                    + '&token='+static_token
                                    + '&allow_refresh=1',
                   async: true,
                   cache: false,
                   dataType: 'json',
                   beforeSend: function() {
                       $('#cart_update_warning .permanent-warning').remove();
                       $('#confirmLoader').show();
                   },
                   complete: function() {
                       $('#confirmLoader').hide();
                   },
                   success: function( jsonData ) {                   
                       if (jsonData.hasError){
                            for(var error in jsonData.errors){
                                if(error !== 'indexOf')
                                    errors += jsonData.errors[error] + "<br>";
                            }
                            $('#cart_update_warning').html('<div class="permanent-warning">'+errors+'</div>');//alert(errors);
                            $('input[name='+ element +']').val($('input[name='+ element +'_hidden]').val());
                        }else{
                            $.gritter.add({
                                title: notification,
                                text: product_qty_update_success,
                                class_name:'gritter-success',
                                sticky: false,
                                time: '3000'
                            });
                                if (jsonData.refresh)
                                        location.reload();
                                updateCartSummary(jsonData.summary);

                                if (customizationId !== 0)
                                        updateCustomizedDatas(jsonData.customizedDatas);
                                loadCarriers();					
                        }
                   },
                   error: function(XMLHttpRequest, textStatus, errorThrown) {
                        errors = sprintf(ajaxRequestFailedMsg, XMLHttpRequest, textStatus);
                        $('#cart_update_warning').html('<div class="permanent-warning">'+errors+'</div>');
                   }
                } );    
            }else if(hidden == input){
                $('#cart_update_warning').html('<div class="permanent-warning">'+updateSameQty+'</div>');
            }else{
                $('#cart_update_warning').html('<div class="permanent-warning">'+scInvalidQty+'</div>');
            }
        }else{
            deleteProductFromSummary(id);
        }
        
    }else{console.log('inv');
        $('#cart_update_warning').html('<div class="permanent-warning">'+scInvalidQty+'</div>');
    }
}


function updateCartSummary(json){
    ajaxCart.refresh();
    var i;
    var nbrProducts = 0;
    var product_list = new Array();

    if (typeof json == 'undefined')
            return;		

    for (i=0;i<json.products.length;i++)
            product_list[json.products[i].id_product + '_' + json.products[i].id_product_attribute + '_' + json.products[i].id_address_delivery] = json.products[i];

    if (!$('.multishipping-cart:visible').length)
    {
            for (i=0;i<json.gift_products.length;i++)
                    if (typeof(product_list[json.gift_products[i].id_product + '_' + json.gift_products[i].id_product_attribute + '_' + json.gift_products[i].id_address_delivery]) !== 'undefined')
                            product_list[json.gift_products[i].id_product + '_' + json.gift_products[i].id_product_attribute + '_' + json.gift_products[i].id_address_delivery].quantity -= json.gift_products[i].cart_quantity;
    }
    else
            for (i=0;i<json.gift_products.length;i++)
                    if (typeof(product_list[json.gift_products[i].id_product + '_' + json.gift_products[i].id_product_attribute + '_' + json.gift_products[i].id_address_delivery]) == 'undefined')
                            product_list[json.gift_products[i].id_product + '_' + json.gift_products[i].id_product_attribute + '_' + json.gift_products[i].id_address_delivery] = json.gift_products[i];

    for (i in product_list)
    {
            // if reduction, we need to show it in the cart by showing the initial price above the current one
            var reduction = product_list[i].reduction_applies;
            var initial_price_text = '';
            initial_price = '';
            if (typeof(product_list[i].price_without_quantity_discount) !== 'undefined'){
                initial_price = formatCurrency(product_list[i].price_without_quantity_discount, currencyFormat, currencySign, currencyBlank);
                var tmp1 = ps_round(product_list[i].price_without_quantity_discount) - product_list[i].price_wt;
                var tmp2 = ps_round(product_list[i].price_without_quantity_discount);
                priceReductionPercent = -ps_round(ps_round(tmp1/tmp2) * 100);
            }

            var current_price = '';
            if (priceDisplayMethod !== 0)
                    current_price = formatCurrency(product_list[i].price, currencyFormat, currencySign, currencyBlank);
            else
                    current_price = formatCurrency(product_list[i].price_wt, currencyFormat, currencySign, currencyBlank);

            if (priceReductionPercent && typeof(initial_price) !== 'undefined')
                    if (initial_price !== '' && product_list[i].price_without_quantity_discount > product_list[i].price)
                            initial_price_text = '<br><span class="price-percent-reduction-description">'+priceReductionPercent+'%</span><span class="supercheckout-old-price">' + initial_price + '</span>';

            var key_for_blockcart = product_list[i].id_product + '_' + product_list[i].id_product_attribute + '_' + product_list[i].id_address_delivery;
            var key_for_blockcart_nocustom = product_list[i].id_product + '_' + product_list[i].id_product_attribute + '_' + ((product_list[i].id_customization && product_list[i].quantity_without_customization != product_list[i].quantity)? 'nocustom' : '0') + '_' + product_list[i].id_address_delivery;

            if (priceDisplayMethod !== 0)
            {
                    $('#product_price_' + key_for_blockcart).html('<span class="price">' + current_price + '</span>' + initial_price_text);
                    if (typeof(product_list[i].customizationQuantityTotal) !== 'undefined' && product_list[i].customizationQuantityTotal > 0)			
                            $('#total_product_price_' + key_for_blockcart).html(formatCurrency(product_list[i].total_customization, currencyFormat, currencySign, currencyBlank));
                    else
                            $('#total_product_price_' + key_for_blockcart).html(formatCurrency(product_list[i].total, currencyFormat, currencySign, currencyBlank));
                    if (product_list[i].quantity_without_customization != product_list[i].quantity)
                            $('#total_product_price_' + key_for_blockcart_nocustom).html(formatCurrency(product_list[i].total, currencyFormat, currencySign, currencyBlank));				
            }
            else
            {	
                    $('#product_price_' + key_for_blockcart).html('<span class="price">' + current_price + '</span>' + initial_price_text);
                    if (typeof(product_list[i].customizationQuantityTotal) !== 'undefined' && product_list[i].customizationQuantityTotal > 0)
                            $('#total_product_price_' + key_for_blockcart).html(formatCurrency(product_list[i].total_customization_wt, currencyFormat, currencySign, currencyBlank));
                    else
                            $('#total_product_price_' + key_for_blockcart).html(formatCurrency(product_list[i].total_wt, currencyFormat, currencySign, currencyBlank));
                    if (product_list[i].quantity_without_customization != product_list[i].quantity)
                            $('#total_product_price_' + key_for_blockcart_nocustom).html(formatCurrency(product_list[i].total_wt, currencyFormat, currencySign, currencyBlank));									
            }
            $('input[name=quantity_' + key_for_blockcart_nocustom + ']').val(product_list[i].id_customization? product_list[i].quantity_without_customization : product_list[i].cart_quantity);
            $('input[name=quantity_' + key_for_blockcart_nocustom + '_hidden]').val(product_list[i].id_customization? product_list[i].quantity_without_customization : product_list[i].cart_quantity);
            if (typeof(product_list[i].customizationQuantityTotal) !== 'undefined' && product_list[i].customizationQuantityTotal > 0)
                    $('#cart_quantity_custom_' + key_for_blockcart).html(product_list[i].customizationQuantityTotal);
            nbrProducts += parseInt(product_list[i].quantity);			
    }

    // Update discounts
    var discount_count = 0;
    for(var e in json.discounts)
    {
            discount_count++;
            break;
    }

    $('.cart_discount').each(function(){$(this).remove();});
    $('.cart_total_voucher').remove();
    
    if (discount_count){

        //Update Discounts
        var total_discount_html = '';
        var show_total_discount = (json.total_discounts <= 0)?'style="display:none"':'';
        var total_discount_value = 0;
        var total_voucher_label = totalVoucherText;
        if(priceDisplayMethod !== 0){            
            total_discount_value = '-' + formatCurrency(json.total_discounts_tax_exc, currencyFormat, currencySign, currencyBlank);
            total_voucher_label += ' '+tax_excl_text;
        }else{
            total_discount_value = '-' + formatCurrency(json.total_discounts, currencyFormat, currencySign, currencyBlank);
            total_voucher_label += ' '+tax_incl_text;
        }
         
        total_discount_html += '<tr class="cart_total_voucher" '+show_total_discount+'>'
                                + '<td class="title"><b>'+total_voucher_label+'</b></td>'
                                + '<td class="value"><span class="price" id="total_discount">'+total_discount_value+'</span></td>'
                                + '</tr>';
                        
        var individual_discount_html = '';

        for (var i in json.discounts){
            var discount_value = 0;
            if(priceDisplayMethod == 0){
                discount_value = formatCurrency(json.discounts[i].value_real * -1, currencyFormat, currencySign, currencyBlank);
            }else{
                discount_value = formatCurrency(json.discounts[i].value_tax_exc*-1, currencyFormat, currencySign, currencyBlank);
            }
            individual_discount_html += '<tr id="cart_discount_'+json.discounts[i].id_discount+'" class="cart_discount" >'
                            +'<td class="title"><b>'+json.discounts[i].name+'<a href="javascript:void(0)" onclick="removeDiscount('+json.discounts[i].id_discount+')"><div title="Redeem" class="removeProduct"></div></a></td></b></td>'
                            +'<td class="value"><span class="price">'+discount_value+'</span> </td>'                               
                            +'</tr>';
        }
        
        var discount_html= total_discount_html + individual_discount_html;

        $('#supercheckout_voucher_input_row').before(discount_html);
        
        if (priceDisplayMethod !== 0)
                $('#total_discount').html('-' + formatCurrency(json.total_discounts_tax_exc, currencyFormat, currencySign, currencyBlank));
        else
                $('#total_discount').html('-' + formatCurrency(json.total_discounts, currencyFormat, currencySign, currencyBlank));            
    }

    // Block cart
    $('#cart_block_shipping_cost').show();
    $('#cart_block_shipping_cost').next().show();
    if (json.total_shipping > 0)
    {
            if (priceDisplayMethod !== 0)
            {
                    $('#cart_block_shipping_cost').html(formatCurrency(json.total_shipping_tax_exc, currencyFormat, currencySign, currencyBlank));
                    $('#cart_block_wrapping_cost').html(formatCurrency(json.total_wrapping_tax_exc, currencyFormat, currencySign, currencyBlank));
                    $('#cart_block_total').html(formatCurrency(json.total_price_without_tax, currencyFormat, currencySign, currencyBlank));
            }
            else
            {
                    $('#cart_block_shipping_cost').html(formatCurrency(json.total_shipping, currencyFormat, currencySign, currencyBlank));
                    $('#cart_block_wrapping_cost').html(formatCurrency(json.total_wrapping, currencyFormat, currencySign, currencyBlank));
                    $('#cart_block_total').html(formatCurrency(json.total_price, currencyFormat, currencySign, currencyBlank));
            }
    }
    else
    {
            if (json.carrier.id == null)
            {
                    $('#cart_block_shipping_cost').hide();
                    $('#cart_block_shipping_cost').next().hide();
            }
    }

    $('#cart_block_tax_cost').html(formatCurrency(json.total_tax, currencyFormat, currencySign, currencyBlank));
    $('.ajax_cart_quantity').html(nbrProducts);

    // Cart summary
    //$('#summary_products_quantity').html(nbrProducts + ' ' + (nbrProducts > 1 ? txtProducts : txtProduct));
    if (priceDisplayMethod !== 0)
            $('#total_product').html(formatCurrency(json.total_products, currencyFormat, currencySign, currencyBlank));
    else
            $('#total_product').html(formatCurrency(json.total_products_wt, currencyFormat, currencySign, currencyBlank));
    $('#total_price').html(formatCurrency(json.total_price, currencyFormat, currencySign, currencyBlank));
    $('#total_price_wfee').val(json.total_price);
    $('#total_price_without_tax').html(formatCurrency(json.total_price_without_tax, currencyFormat, currencySign, currencyBlank));
    $('#total_tax').html(formatCurrency(json.total_tax, currencyFormat, currencySign, currencyBlank));
    
    if (scp_use_taxes)
    {
        scp_order_total_price = json.total_price;
    }else{
        scp_order_total_price = json.total_price_without_tax;
    }
    
    $('#cart_total_delivery').show();
    if (json.total_shipping > 0)
    {
            if (priceDisplayMethod !== 0)
                    $('#total_shipping').html(formatCurrency(json.total_shipping_tax_exc, currencyFormat, currencySign, currencyBlank));
            else
                    $('#total_shipping').html(formatCurrency(json.total_shipping, currencyFormat, currencySign, currencyBlank));
    }
    else
    {
        if(!shipping_error_found_on_load){
            if (json.carrier.id != null)
                        $('#total_shipping').html(freeShippingTranslation);
                else
                        $('#cart_total_delivery').hide();    
        }else{
            $('#cart_total_delivery').hide(); 
        }
    }

    if (json.free_ship > 0 && !json.is_virtual_cart)
    {
            $('.cart_free_shipping').fadeIn();
		if (typeof (json.free_ship) == "boolean")
			$('#free_shipping').html(json.free_ship);
		else
			$('#free_shipping').html(formatCurrency(json.free_ship, currencyFormat, currencySign, currencyBlank));

    }
    else
            $('.cart_free_shipping').hide();

    if (json.total_wrapping > 0)
    {
            $('#total_wrapping').html(formatCurrency(json.total_wrapping, currencyFormat, currencySign, currencyBlank));
            $('#total_wrapping').parent().parent().show();
    }
    else
    {
            $('#total_wrapping').html(formatCurrency(json.total_wrapping, currencyFormat, currencySign, currencyBlank));
            $('#total_wrapping').parent().parent().hide();
    }
    if (json.HOOK_SHOPPING_CART)
	$('#loyalty_text_holder').html(json.HOOK_SHOPPING_CART);
}

function deleteProductFromSummary(id){
    var customizationId = 0;
    var productId = 0;
    var productAttributeId = 0;
    var id_address_delivery = 0;
    var ids = 0;
    ids = id.split('_');
    var errors = '';
    productId = parseInt(ids[0]);
    if (typeof(ids[1]) !== 'undefined')
            productAttributeId = parseInt(ids[1]);
    if (typeof(ids[2]) !== 'undefined' && ids[2] !== 'nocustom')
            customizationId = parseInt(ids[2]);
    if (typeof(ids[3]) !== 'undefined')
            id_address_delivery = parseInt(ids[3]);
    $.ajax({
            type: 'POST',
            headers: { "cache-control": "no-cache" },
            url: baseUri + '?rand=' + new Date().getTime(),
            async: true,
            cache: false,
            dataType: 'json',
            data: 'controller=cart'
                    + '&ajax=true&delete=true&summary=true'
                    + '&id_product='+productId
                    + '&ipa='+productAttributeId
                    + '&id_address_delivery='+id_address_delivery 
                    + ((customizationId !== 0) ? '&id_customization=' + customizationId : '')
                    + '&token=' + static_token
                    + '&allow_refresh=1',
            beforeSend: function(){
                $('#cart_update_warning .permanent-warning').remove();
            },
            success: function(jsonData)
            {
                    if (jsonData.hasError)
                    {                            
                            for(var error in jsonData.errors)
                            //IE6 bug fix
                            if (error !== 'indexOf')
                                    errors += jsonData.errors[error] + "\n";
                            $('#cart_update_warning').html('<div class="permanent-warning">'+errors+'</div>');                            
                    }
                    else
                    {
                        $.gritter.add({
                            title: notification,
                            text: product_remove_success,
                            class_name:'gritter-success',
                            sticky: false,
                            time: '3000'
                        });
                            if (jsonData.refresh)
                                    location.reload();
                            if (parseInt(jsonData.summary.products.length) == 0){
                                    location.reload();			//Code to reload the page in case cart is empty instead of removing the blocks.
                            }else{
                                    $('#product_' + id).fadeOut('slow', function() {
                                            $(this).remove();
                                            //cleanSelectAddressDelivery();
                                            if (!customizationId){
                                                    //refreshOddRow();
                                            }
                                    });
                                    var exist = false;
                                    for (i=0;i<jsonData.summary.products.length;i++)
                                    {
                                            if (jsonData.summary.products[i].id_product == productId
                                                    && jsonData.summary.products[i].id_product_attribute == productAttributeId
                                                    && jsonData.summary.products[i].id_address_delivery == id_address_delivery
                                                    && (parseInt(jsonData.summary.products[i].customization_quantity) > 0))
                                                            exist = true;
                                    }
                                    // if all customization removed => delete product line
                                    if (!exist && customizationId)
                                            $('#product_' + productId + '_' + productAttributeId + '_0_' + id_address_delivery).fadeOut('slow', function() {
                                                    $(this).remove();
                                                    var line = $('#product_' + productId + '_' + productAttributeId + '_nocustom_' + id_address_delivery);
                                                    if (line.length > 0)
                                                    {
                                                            line.find('td.supercheckout-qty input[name^=quantity_], td.supercheckout-qty input[name^=quantity_], td.supercheckout-qty a.supercheckout-product-delete').each(function(){
                                                                    if (typeof($(this).attr('name')) != 'undefined')
                                                                            $(this).attr('name', $(this).attr('name').replace(/nocustom/, '0'));
                                                                    if (typeof($(this).attr('id')) != 'undefined')
                                                                            $(this).attr('id', $(this).attr('id').replace(/nocustom/, '0'));
                                                            });
                                                            line.find('span[id^=total_product_price_]').each(function(){
                                                                    $(this).attr('id', $(this).attr('id').replace(/_nocustom/, ''));
                                                            });
                                                            line.attr('id', line.attr('id').replace(/nocustom/, '0'));
                                                    }
                                                    //refreshOddRow();
                                            });
                            }
                            updateCartSummary(jsonData.summary);

                            updateCustomizedDatas(jsonData.customizedDatas);
                            loadCarriers();
                    }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                errors = sprintf(ajaxRequestFailedMsg, XMLHttpRequest, textStatus);
                $('#cart_update_warning').html('<div class="permanent-warning">'+errors+'</div>');
            }
    });
}


function updateCustomizedDatas(json){
	for(var i in json)
		for(var j in json[i])
			for(var k in json[i][j])
				for(var l in json[i][j][k])
				{
					var quantity = json[i][j][k][l]['quantity'];
					$('input[name=quantity_' + i + '_' + j + '_' + l + '_' + k + '_hidden]').val(quantity);
					$('input[name=quantity_' + i + '_' + j + '_' + l + '_' + k + ']').val(quantity);
				}
}


function callCoupon(){
    $.ajax( {
        type: "POST",
        headers: { "cache-control": "no-cache" },
        url: $('#module_url').val() + '&rand=' + new Date().getTime()+'&ajax=true',
        async: true,
        cache: false,
        data: $('#voucher-form input'),
        dataType: 'json',
        beforeSend: function() {
            $('#cart_update_warning .permanent-warning').remove();
            $('#confirmLoader').show();
        },
        complete: function() {
            $('#confirmLoader').hide();
        },
        success: function( json ) {
            if(json['success'] != undefined){
                $.gritter.add({
                        title: notification,
                        text: json['success'],
                //	image: '',
                        class_name:'gritter-success',
                        sticky: false,
                        time: '3000'
                });
                $('#discount_name').attr('value','');
                loadCarriers();
            }else if(json['error'] != undefined){
               $('#cart_update_warning').html('<div class="permanent-warning">'+json['error']+'</div>');
            }
			$('#highlighted_cart_rules').html(json['cart_rule']);
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            var error = sprintf(ajaxRequestFailedMsg, XMLHttpRequest, textStatus);
            $('#cart_update_warning').html('<div class="permanent-warning">'+error+'</div>');
        }
    } );    
}

function removeDiscount(voucher_id){
    $.ajax( {
        type: "POST",
        headers: { "cache-control": "no-cache" },
        url: $('#module_url').val() + '&rand=' + new Date().getTime(),
        async: true,
        cache: false,
        data: '&ajax=true&deleteDiscount='+voucher_id,
        dataType: 'json',
        beforeSend: function() {
            $('#cart_update_warning .permanent-warning').remove();
            $('#confirmLoader').show();
        },
        complete: function() {
            $('#confirmLoader').hide();
        },
        success: function( json ) {
            if(json['success'] != undefined){
                $.gritter.add({
                        title: notification,
                        text: json['success'],
                //	image: '',
                        class_name:'gritter-success',
                        sticky: false,
                        time: '3000'
                });
                $('#discount_name').attr('value','');
                loadCarriers();
            }else if(json['error'] != undefined){
               $('#cart_update_warning').html('<div class="permanent-warning">'+json['error']+'</div>');
            }
			$('#highlighted_cart_rules').html(json['cart_rule']);
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            var error = sprintf(ajaxRequestFailedMsg, XMLHttpRequest, textStatus);
            $('#cart_update_warning').html('<div class="permanent-warning">'+error+'</div>');
        }
    } );
}

function selectShippingOption(selected_value){
    //$('.supercheckout_shipping_option').removeAttr('checked');

    $('.supercheckout_shipping_option').each(function(){
        if(selected_value == $(this).val()){
            $(this).attr('checked','checked');
        }
    });
    
}

function updateShippingExtra(jsonData){
                    
    var recyclable = (($('#supercheckout_recyclepack_container input[type=checkbox]').is(':checked'))? 1 : 0);
    var checked_gift = (($('#supercheckout-gift_container input[type=checkbox]').is(':checked'))? 1 : 0);
    var comment_gift = $('#gift_message').val();
    var checked_termCondition = (($('#supercheckout-agree input[type=checkbox]').is(':checked'))? 1 : 0);
    var isAlreadyDisplayed = ($('#supercheckout-gift-comments').is(':visible'))? 1 : 0;

    $('#order-shipping-extra').html(jsonData['order-shipping-extra']);

    if(recyclable == 1){
        $('#supercheckout_recyclepack_container input[type=checkbox]').attr('checked', 'checked');
    }
    if(checked_gift == 1){
        $('#supercheckout-gift_container input[type=checkbox]').attr('checked', 'checked');
    }
    $('#supercheckout-gift-comments textarea').html(comment_gift);
    if(isAlreadyDisplayed == 1){
        $('#supercheckout-gift-comments').show();
    }

    if(checked_termCondition == 1){
        $('#supercheckout-agree input[type=checkbox]').attr('checked', 'checked');
    }    
}


function set_column_inside_height(){
    var col_1_inside = $('#column-1-inside').height();
    var col_2_inside = $('#column-2-inside').height();

    if(col_1_inside > col_2_inside){
        $('#column-2-inside').css('height', col_1_inside+'px');
    }else if(col_1_inside < col_2_inside){
        $('#column-1-inside').css('height', col_2_inside+'px');
    }
}

function checkZipCode(e, isCarrierLoad){
    var checkZip = false;
    var address_type = $(e).attr('name').split('[');
    address_type = address_type[0];
    var container = 'checkoutShippingAddress';
    if(address_type == 'payment_address'){
        container = 'checkoutBillingAddress';
    }
    
    if($('#'+container+' input[name="'+address_type+'[postcode]"]').length){
        checkZip = true;
    }
    if(checkZip){
        var checkData = {
            'ajax'      : true,
            'method'    : 'checkZipCode',
            'id_country': ($('select[name="'+address_type+'[id_country]"]').length > 0) ? $('select[name="'+address_type+'[id_country]"] option:selected').val() : '',
            'postcode'  : ($('input[name="'+address_type+'[postcode]"]').length > 0) ? $('input[name="'+address_type+'[postcode]"]').val() : '',
        } 
        $.ajax({
            type: 'POST',
            headers: { "cache-control": "no-cache" },
            url: $('#module_url').val() + '&rand=' + new Date().getTime(),
            async: true,
            cache: false,
            dataType : "json",
            data: checkData,
            beforeSend: function() {
                $('#'+container+' input[name="'+address_type+'[postcode]"]').parent().find('span.errorsmall').remove();
		if (inline_validation == 1)
		{
			$('#'+container+' input[name="'+address_type+'[postcode]"]').removeClass('error-form');
		$('#'+container+' input[name="'+address_type+'[postcode]"]').removeClass('ok-form');
		}
		
            },
            complete: function() {

            },
            success: function(jsonData)
            {
                if(jsonData['error'] != undefined){
                    $('#'+container+' input[name="'+address_type+'[postcode]"]').parent().append('<span class="errorsmall">'+jsonData['error']+'</span>');
		    if (inline_validation == 1)
			$('#'+container+' input[name="'+address_type+'[postcode]"]').addClass('error-form');
                }
		else
		{
			if (inline_validation == 1)
				$('#'+container+' input[name="'+address_type+'[postcode]"]').addClass('ok-form');
		}
                if(isCarrierLoad){
                    loadCarriers();
                }               
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                var errors = sprintf(ajaxRequestFailedMsg, XMLHttpRequest, textStatus);
                $('#'+container+' .supercheckout-checkout-content').html('<div class="permanent-warning">'+errors+'</div>');
            }
        });    
    }        
}


function display_progress(value){
    $('#supercheckout_confirm_order').attr('disabled', true);
    $('#submission_progress_overlay').css('height', $('#supercheckout-fieldset').height());
    $('#supercheckout_order_progress_status_text').html(value + '%');
    $('#submission_progress_overlay').show();
    $('#supercheckout_order_progress_bar').show();    
}

function hide_progress(){
    $('#supercheckout_confirm_order').removeAttr('disabled');
    $('#submission_progress_overlay').hide();
    $('#supercheckout_order_progress_bar').hide();
    $('#supercheckout_order_progress_status_text').html('0%');    
}

var proceed_to_payment = false;

function actionOnPaymentSelect(element){
    var payment_module_name = $('input:radio[name="payment_method"]:checked').attr('id');
    var id = $('input:radio[name="payment_method"]:checked').val();    
    
    if (payment_module_name != 'epay')
	{
		$('#supercheckout_dialog_proceed').show();
		$('#velsof_payment_container .velsof_content_section').css('height', '200px');
	}
    
    var redirectHtml = '';
    //to fix stripjs method you need to edit its stripjs.php file too.. check pmsheet wiki for more information
    //to fix braintreejs method you need to edit its braintreejs.php file too.. check pmsheet wiki for more information
    if(payment_module_name != 'stripejs' && payment_module_name != 'firstdata' && payment_module_name != 'braintreejs_backup' && payment_module_name != 'twocheckout' && payment_module_name != 'brinkscheckout' && payment_module_name != 'ewayrapid' && payment_module_name != 'npaypalpro' && payment_module_name != 'authorizeaim'){
			$('#selected_payment_method_html').html(''); // to hide form if customer select any other payment method later
		}
    if(payment_module_name == 'librapay' || payment_module_name == 'cashondelivery' || payment_module_name == 'westernunion' || payment_module_name == 'billmateinvoice' || payment_module_name == 'billmatepartpayment' || payment_module_name == 'mercadoc' ||payment_module_name == 'boletosantanderpro' || payment_module_name == 'payu' || payment_module_name == 'payulatam' || payment_module_name == 'zipcheck' || payment_module_name == 'megareembolso' || payment_module_name == 'payinstore' || payment_module_name == 'codfee' || payment_module_name == 'finanziamento' || payment_module_name == 'megashoppay' ){
        redirectHtml += '<input type="hidden" id="payment_redirect" value="'+$('#'+id+'_name').val()+'" />';
        $('#velsof_payment_dialog .velsof_content_section').html(redirectHtml);
    }else if(payment_module_name == 'bankwire' || payment_module_name == 'invoicepayment' || payment_module_name == 'pagofacil' || payment_module_name == 'postepay' || payment_module_name == 'paysera' || payment_module_name == 'offlinecreditcard' || payment_module_name == 'trustly' || payment_module_name == 'cheque' || payment_module_name == 'deluxecodfees'){
        getPaymentForm(element);
    }else if(payment_module_name == 'stripejs' || payment_module_name == 'firstdata' || payment_module_name == 'braintreejs_backup' || payment_module_name == 'twocheckout' || payment_module_name == 'brinkscheckout' || payment_module_name == 'ewayrapid' || payment_module_name == 'npaypalpro' ||  payment_module_name == 'mobilpay_cc' || payment_module_name == 'authorizeaim' || payment_module_name == 'khipupayment' || payment_module_name == 'paynl_paymentmethods' || payment_module_name == 'mollie' || payment_module_name == 'quickpay' || payment_module_name == 'moneybookers' || payment_module_name == 'paynlpaymentmethods' || payment_module_name == 'add_gopay_new' || payment_module_name == 'pms_gopay_extra' || payment_module_name == 'paypal' || payment_module_name == 'parspalpayment' || payment_module_name == 'pronesis_bancasella' || payment_module_name == 'paypalmx' || payment_module_name == 'cmcic_tbweb' || payment_module_name == 'sisoweb' || payment_module_name == 'citrus' || payment_module_name == 'banc_sabadell' || payment_module_name == 'ccavenue' || payment_module_name == 'ogone' || payment_module_name == 'epay' || payment_module_name == 'creditcardpaypal' || payment_module_name == 'paypalusa' || payment_module_name == 'sisowideal' || payment_module_name == 'paypalwithfee' || payment_module_name == 'sisowmc'){
        getPaymentForm1(element);
    }else{
        getPaymentForm1(element);
    }
    if (typeof changePaymentMethodFeeCart == 'function') {
        changePaymentMethodFeeCart();
    }
}

function getPaymentForm(element){
    var url = $('#'+$('input:radio[name="payment_method"]:checked').attr('value')+'_name').val();
    var payment_module_name = $('input:radio[name="payment_method"]:checked').attr('id');
    var setErrorResponse = '<input type="hidden" id="payment_fetch_error" value="0" />';
    $.ajax({
        type: 'GET',
        headers: { "cache-control": "no-cache" },
        url: url,
        async: true,
        //cache: false,
        dataType : "html",
        beforeSend: function() {
            $('#paymentMethodLoader').show();
            $('#velsof_payment_dialog .velsof_content_section').html(setErrorResponse);
        },
        complete: function() {
            $('#paymentMethodLoader').hide();
        },
        success: function(dataHtml)
        {
            try{
                var payment_info_html = $(dataHtml).find('#'+payment_content_id);
                $(payment_info_html).find('#order_step').remove();
                $('h1', payment_info_html).remove();
                $('#cart_navigation', payment_info_html).remove();                
                $('.cart_navigation', payment_info_html).remove();      // Added for Prestashop 1.5 for removing the buttons in the payment method html         
                $('#amount', payment_info_html).removeClass('price');
                $(payment_info_html).find('form:first').find('div:first, div.box').find('p:last-child').remove();
                $(payment_info_html).find('form:first').find('div:first, div.box').find('#currency_payement').parent().hide();
                $('#velsof_payment_dialog .velsof_content_section').html(payment_info_html.html());
            }catch(err){
                $('#velsof_payment_dialog .velsof_content_section').html(setErrorResponse);
            }
            if(proceed_to_payment){
                moveToPayment();
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            var errors = sprintf(ajaxRequestFailedMsg, XMLHttpRequest, textStatus);
            displayGeneralError(errors);
        }
    });
}

function getPaymentForm1(element){
    $('#display_payment').html('');
    $('#display_payment').parent().find('.supercheckout-checkout-content .permanent-warning').remove();
    $('#payment_display_block .supercheckout-payment-info').hide();
    var url = $('#'+$('input:radio[name="payment_method"]:checked').attr('value')+'_name').val();
    var payment_module_name = $('input:radio[name="payment_method"]:checked').attr('id');
    var payment_module_id = $('input:radio[name="payment_method"]:checked').val();
    var setErrorResponse = '<input type="hidden" id="payment_fetch_error" value="0" />';
    $.ajax({
        type: 'GET',
        headers: { "cache-control": "no-cache" },
        url: $('#module_url').val() + '&rand=' + new Date().getTime(),
        async: true,
        //cache: false,
        dataType : "json",
        data: 'ajax=true'
            +'&method=getPaymentInformation'
            +'&id_module='+payment_module_id
            +'&payment_module_name='+payment_module_name
            +'&token=' + static_token,
        beforeSend: function() {
            $('#paymentMethodLoader').show();
            $('#velsof_payment_dialog .velsof_content_section').html(setErrorResponse);
        },
        complete: function() {
            $('#paymentMethodLoader').hide();
        },
        success: function(json)
        {
            var html = '';
            if(json['error'] != undefined){
                html = '<input type="hidden" id="payment_fetch_error" value="0" />';
                $('#velsof_payment_dialog .velsof_content_section').html(html);
            }else{
                $('#velsof_payment_dialog .velsof_content_section').html(json['html']);                
            }
            $('#velsof_payment_dialog .velsof_action_section').show();
            if(payment_module_name == 'authorizeaim'){
		 $('#selected_payment_method_html').html($('.velsof_content_section').html());
		$('#click_authorizeaim').trigger('click');
		$('#x_exp_date_m').children('option').each(function() { // to fix issue of extra 0 prefix to month drop down value
			if ($(this).val() < 10) { 
			var value=$(this).val();
			value=value.slice(-1); // to get last character
			$(this).html('0'+value); 
			$(this).val('0'+value); 
			}
		});
		$('#asubmit').hide();
            }else if(payment_module_name == 'stripejs' || payment_module_name == 'firstdata' || payment_module_name == 'braintreejs_backup' || payment_module_name == 'brinkscheckout' || payment_module_name == 'twocheckout'){
		    //$('.velsof_content_section').appendTo('#selected_payment_method_html');
		$('#selected_payment_method_html').html($('.velsof_content_section').html());
		$('#firstdata_submit').hide();
		$('.stripe-submit-button').hide();
		//$('#braintree-submit-button').hide();
		$('#twocheckoutCCForm #submit_payment').hide();
		$('#twocheckoutCCForm input.button').hide();
            }else if(payment_module_name == 'ewayrapid'){
		    //$('.velsof_content_section').appendTo('#selected_payment_method_html');
		$('#selected_payment_method_html').html($('.velsof_content_section').html());
		$('#processPayment').hide();
            }else if(payment_module_name == 'npaypalpro'){
		    //$('.velsof_content_section').appendTo('#selected_payment_method_html');
		$('#selected_payment_method_html').html($('.velsof_content_section').html());
		$('.paypalpro-submit-button').hide();
            }else if(payment_module_name == 'moneybookers' || payment_module_name == 'paynlpaymentmethods' ||  payment_module_name == 'add_gopay_new' ||  payment_module_name == 'quickpay' ||  payment_module_name == 'pms_gopay_extra' || payment_module_name == 'mollie' || payment_module_name == 'paynl_paymentmethods'){
                $('#velsof_payment_dialog .velsof_action_section').hide();
            }else if(payment_module_name == 'khipupayment'){
                $('#velsof_payment_dialog .velsof_action_section').hide();
            }else if(payment_module_name == 'epay'){
		$('#velsof_payment_container .velsof_content_section').css('height', '340px');
		$('#supercheckout_dialog_proceed').hide();
            }
            if(proceed_to_payment){
                moveToPayment();
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            var errors = sprintf(ajaxRequestFailedMsg, XMLHttpRequest, textStatus);
            displayGeneralError(errors);
        }
    });
}

var custom_epay = 0;
function placeOrder(){
    var errors = '';
    $.ajax({
        type: 'POST',
        headers: { "cache-control": "no-cache" },
        url: $('#module_url').val() + '&ajax=true&rand=' + new Date().getTime(),
        async: true,
        cache: false,
        dataType : "json",
        data: $('#velsof_supercheckout_form').serialize(),
        beforeSend: function() {            
            $('.errorsmall').remove();
            hideGeneralError();
            display_progress(20);
        },
        complete: function() {

        },
        success: function(jsonData)
        {
            if(jsonData['error'] != undefined){
                var has_validation_error = false;
                var i=0;
                if(jsonData['error']['checkout_option'] != undefined){
                    has_validation_error = true;
                    for(i in jsonData['error']['checkout_option']){
                        $('input[name="'+jsonData['error']['checkout_option'][i]['key']+'"]').parent().append('<span class="errorsmall">'+jsonData['error']['checkout_option'][i]['error']+'</span>');
			if (inline_validation == 1)
				$('input[name="'+jsonData['error']['checkout_option'][i]['key']+'"]').addClass('error-form').removeClass('ok-form');
                    }
                }

                var i=0;
                var key = '';
                if(jsonData['error']['customer_personal'] != undefined){
                    has_validation_error = true;
                    for(i in jsonData['error']['customer_personal']){
                        key = jsonData['error']['customer_personal'][i]['key'];
                        if(key == 'dob' || key == 'id_gender'){
                            $('.supercheckout_personal_'+key).append('<span class="errorsmall">'+jsonData['error']['customer_personal'][i]['error']+'</span>');
                        }else if(key == 'password'){
                            $('input[name="customer_personal['+key+']"]').parent().append('<span class="errorsmall">'+jsonData['error']['customer_personal'][i]['error']+'</span>');
			    if (inline_validation == 1)
				$('input[name="customer_personal['+key+']"]').addClass('error-form').removeClass('ok-form');
                        }else{
                            $('input[name="customer_personal['+key+']"]').parent().parent().parent().parent().append('<span class="errorsmall">'+jsonData['error']['customer_personal'][i]['error']+'</span>');
			    if (inline_validation == 1)
				$('input[name="customer_personal['+key+']"]').addClass('error-form').removeClass('ok-form');
                        }                        
                    }
                }

                var tmp_index;
                if(jsonData['error']['shipping_address'] != undefined){
                    has_validation_error = true;
                   for(tmp_index in jsonData['error']['shipping_address']){
		       $('input[name="shipping_address['+jsonData['error']['shipping_address'][tmp_index]['key']+']"]').parent().append('<span class="errorsmall">'+jsonData['error']['shipping_address'][tmp_index]['error']+'</span>');
		       if (inline_validation == 1)
				$('input[name="shipping_address['+jsonData['error']['shipping_address'][tmp_index]['key']+']"]').addClass('error-form').removeClass('ok-form');
			if(jsonData['error']['shipping_address'][tmp_index]['key']=='postcode')
			    $('#shipping_post_code').css("display","block");// helpful when postcode is hidden from our module but is equired for some country
                    }
                }

                
                var tmp_index;
                if(jsonData['error']['payment_address'] != undefined){
                    has_validation_error = true;
                   for(tmp_index in jsonData['error']['payment_address']){
                        $('input[name="payment_address['+jsonData['error']['payment_address'][tmp_index]['key']+']"]').parent().append('<span class="errorsmall">'+jsonData['error']['payment_address'][tmp_index]['error']+'</span>'); 
			if (inline_validation == 1)
				$('input[name="payment_address['+jsonData['error']['payment_address'][tmp_index]['key']+']"]').addClass('error-form').removeClass('ok-form');
			if(jsonData['error']['payment_address'][tmp_index]['key']=='postcode')
			    $('#payment_post_code').css("display","block"); // helpful when postcode is hidden from our module but is equired for some country
                    } 
                }
                i=0;
                if(jsonData['error']['general'] != undefined){
                    errors = '';
                    for(var i in jsonData['error']['general']){
                        errors += jsonData['error']['general'][i]+'<br>';
                    }                        
                }else if(has_validation_error){
                    errors = validationfailedMsg;
                }else{
                    errors = scOtherError;
                }
                displayGeneralError(errors);                                                   
                hide_progress();
                $("html, body").animate({ scrollTop: 0 }, "fast");
            }else{
                if(jsonData['warning'] != undefined){
                    //handle warning here
                }
                display_progress(30);
                var is_carrier_selected = true;

                //validate Methods
                $('#shipping-method .supercheckout-checkout-content .permanent-warning').remove();
                if($('#shipping-method .supercheckout_shipping_option').length){
                   if(!$('#shipping-method .supercheckout_shipping_option:checked').length){
                       is_carrier_selected = false;
                   } 
                }

                var is_payment_selected = true;
                $('#payment-method .supercheckout-checkout-content .permanent-warning').remove();
                if($('#payment-method input[name="payment_method"]').length){
                   if(!$('#payment-method input[name="payment_method"]:checked').length){
                       is_payment_selected = false;
                   } 
                }

                if(carriers_count == 0 && !(is_cart_virtual))
                    is_carrier_selected = false;
                
                if(!is_carrier_selected){
                    $('#shipping-method .supercheckout-checkout-content').html('<div class="permanent-warning">'+ShippingRequired+'</div>');
                }
                if(!is_payment_selected){
                    $('#payment-method .supercheckout-checkout-content').html('<div class="permanent-warning">'+paymentRequired+'</div>');
                }

                if(!is_carrier_selected || !is_payment_selected){
                    hide_progress();
                    displayGeneralError('Please provide required Information');
                    $("html, body").animate({ scrollTop: 0 }, "fast");
                }else{

                    display_progress(50);
                    //Validate Order Extras
                    var messagePattern = /[<>{}]/i;
                    var message = '';
                    var extrasError = false;
                    if($('#supercheckout-comment_order').length){
                        message = $('#supercheckout-comment_order').val();
                        if(messagePattern.test(message)){
                            extrasError = true;
                            $('#supercheckout-comment_order').parent().append('<span class="errorsmall">'+commentInvalid+'</span>');
                        }
                    }

                    if($('#gift').length && $('#gift').is(':checked')){
                        message = $('#gift_message').val();
                        if(messagePattern.test(message)){
                            extrasError = true;
                            $('#gift_message').parent().append('<span class="errorsmall">'+commentInvalid+'</span>');
                        }
                    }

                    if($('#supercheckout-agree input[name="cgv"]').length && (!$('#supercheckout-agree input[name="cgv"]').is(':checked') && scp_required_tos == 1)){
                        extrasError = true;
                        $('#supercheckout-agree').after('<span class="errorsmall">'+tosRequire+'</span>');
                    }

                    if(extrasError){
                        hide_progress();
                    }else{
                        display_progress(80);
                        var is_free_order = false;
                        if (scp_use_taxes && scp_order_total_price <= 0){
                            is_free_order = true;
                        }else if(!scp_use_taxes && scp_order_total_price_wt <= 0){
                            is_free_order = true;
                        }
                        if(is_free_order){
                            createFreeOrder();
                        }else{
                            proceed_to_payment = true;
                            if($('input:radio[name="payment_method"]:checked').length){
                                var p_m_name = $('input:radio[name="payment_method"]:checked').attr('id');
                                if(p_m_name == 'stripejs' || p_m_name == 'firstdata' || p_m_name == 'braintreejs_backup' || p_m_name == 'twocheckout' || p_m_name == 'brinkscheckout' || p_m_name == 'ewayrapid' || p_m_name == 'npaypalpro' ||  p_m_name == 'authorizeaim' || p_m_name == 'librapay' || p_m_name == 'cashondelivery' || p_m_name == 'westernunion' || p_m_name == 'billmatepartpayment' || p_m_name == 'billmateinvoice' || p_m_name == 'paysondirect' || p_m_name == 'mercadoc' || p_m_name == 'boletosantanderpro' || p_m_name == 'payu' || p_m_name == 'megashoppay' || p_m_name == 'zipcheck' || p_m_name == 'megareembolso' || p_m_name == 'payinstore' || p_m_name == 'codfee' || p_m_name == 'obsredsys' || p_m_name == 'hipay' || p_m_name == 'psphipay' || p_m_name == 'finanziamento' || p_m_name == 'cecatpv' || p_m_name == 'dineromail' || p_m_name == 'payulatam' || p_m_name == 'cuatrob'){
                                    moveToPayment();
                                }else{
                                    actionOnPaymentSelect($('input:radio[name="payment_method"]:checked'));
                                }
				if (p_m_name == 'epay' && custom_epay == 0)
				{
					if($('#velsof_payment_container').is(':visible')) {
						$('#velsof_payment_container .velsof_dialog_close').click();
					}
					custom_epay = 1;
					$('#supercheckout_confirm_order').click();
				}
                            }else{
                                $('input:radio[name="payment_method"]').first().attr('checked', 'checked');
                                $('input:radio[name="payment_method"]').first().parent().addClass('checked');
                                actionOnPaymentSelect($('input:radio[name="payment_method"]:checked'));
                            }
                        }
                    }

                }
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            errors = sprintf(ajaxRequestFailedMsg, XMLHttpRequest, textStatus);
            displayGeneralError(errors);
            hide_progress();
            $("html, body").animate({ scrollTop: 0 }, "fast");
        }
    });
}

function createFreeOrder(){
    $.ajax({
            type: 'POST',
            headers: { "cache-control": "no-cache" },
            url: $('#module_url').val() + '&rand=' + new Date().getTime(),
            async: true,
            cache: false,
            dataType : "json",
            data: 'ajax=true'
                +'&method=createFreeOrder&token=' + static_token,
            beforeSend: function() {
            },
            success: function(jsonData)
            {
                if (typeof isGuest != 'undefined')
                    document.location.href = scp_guest_tracking_url+'?id_order='+encodeURIComponent(jsonData['order_reference'])+'&email='+encodeURIComponent(jsonData['email']);
                else
                    document.location.href = scp_history_url;
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                var errors = sprintf(ajaxRequestFailedMsg, XMLHttpRequest, textStatus);
                displayGeneralError(errors);                                                   
                hide_progress();
            }
    });
}

function moveToPayment(){
    var url = $('#'+$('input:radio[name="payment_method"]:checked').attr('value')+'_name').val();
    var p_m_name = $('input:radio[name="payment_method"]:checked').attr('id');
    var dialogContainer = '#velsof_payment_dialog .velsof_content_section ';
    var payment_method_html = ''
    if($('#payment_method_html').length){
        payment_method_html = $('#payment_method_html').html();
        $('#payment_method_html').html('');
    }
    
    
    if($(dialogContainer+'#payment_fetch_error').length){
        window.reload();
    }else{
        if(p_m_name == 'paypal' && url == 'javascript:void(0)'){
            $(dialogContainer+'#paypal_process_payment').trigger('click');
            $('#paypal_payment_form').submit(); // above statement was not working for Prestashop 1.6.1.0
        }else if(p_m_name == 'parspalpayment'){
		var form_action = $('#parspalpayment_form').attr('action');
		$('#parspalpayment_form').attr('action', '/'+form_action);
	    $('#parspalpayment_form').submit();
	}else if(p_m_name == 'pronesis_bancasella'){
            $('#bancasella_process_payment').trigger('click');
        }else if(p_m_name == 'deluxeservired'){
            $('#deluxeservired_form').submit();
        }else if(p_m_name == 'plationline'){
            location.href = $('#'+$('input:radio[name="payment_method"]:checked').attr('value')+'_name').val();
        }else if(p_m_name == 'bmsboletobancario'){
            location.href = $('#'+$('input:radio[name="payment_method"]:checked').attr('value')+'_name').val();
        }else if(p_m_name == 'paypalmx'){
	    $('#paypal-express-checkout-form').submit(); 
        }else if(p_m_name == 'cmcic_tbweb'){
            javascript:$('#PaymentRequest1').submit();
        }else if(p_m_name == 'sisoweb'){
	    $('#sisow_ebill_form').submit();
	}else if(p_m_name == 'sisowob'){
	    $('#sisow_overboeking_form').submit();
	}else if(p_m_name == 'sisowpp'){
	    $('#sisow_paypalec_form').submit();
	}else if(p_m_name == 'citrus'){
	    javascript:$('#citrus_form').submit();
	}else if(p_m_name == 'banc_sabadell'){
            javascript:$('#SabadellTPVForm').submit();
        }
	else if(p_m_name == 'ogone'){
	    document.forms['ogone_form'].submit();
        }else if(p_m_name == 'creditcardpaypal' && url == 'javascript:void(0)'){
            $('#paypal_payment_form_credit_card input[name=\'express_checkout\']').val('payment_cart');
		$(dialogContainer+'#paypal_process_payment_credit_card').trigger('click');
        }else if(p_m_name == 'paypalusa'){
            $(dialogContainer+'#paypal-standard-btn').trigger('click');
	    $('#velsof_payment_dialog #paypal-express-checkout-btn-product').click(); //for mexico paypalusa
        }else if(p_m_name == 'ccavenue'){
		javascript:document.redirect.submit();
        }else if(p_m_name == 'paypalwithfee'){
            $(dialogContainer+'#paypal_process_payment_').trigger('click');
        }else if(p_m_name == 'sisowideal'){
            $(dialogContainer+'#sisowideal_process_payment').trigger('click');
        }else if(p_m_name == 'sisowmc'){
            $(dialogContainer+'#sisowmistercash_process_payment').trigger('click');
        }else if(p_m_name == 'dineromail'){
            location.href = $('#'+$('input:radio[name="payment_method"]:checked').attr('value')+'_name').val();
        }else if(p_m_name == 'add_gopay_new'){
	    createPaymentPop();
	    $('#velsof_payment_dialog .velsof_action_section').show(); // @nitin 27 July - Important to show proceed button if it is hide in previous payment method selection	    
	    $('#gopay-payment-form .payment_module').css('display','none');
        }else if(p_m_name == 'pms_gopay_extra'){
	    createPaymentPop();
		$('#supercheckout_dialog_proceed').hide();
	    $('#velsof_payment_dialog .velsof_action_section').show(); // @nitin 27 July - Important to show proceed button if it is hide in previous payment method selection	    
	    $('#gopay-payment-form .payment_module').css('display','none');
	}else if(p_m_name == 'khipupayment' || p_m_name == 'paynl_paymentmethods' || p_m_name == 'mollie' || p_m_name == 'moneybookers' || p_m_name == 'paynlpaymentmethods' || p_m_name == 'epay' || p_m_name == 'quickpay'){
            createPaymentPop();
	    $('#velsof_payment_dialog .velsof_action_section').show(); // @nitin 27 July - Important to show proceed button if it is hide in previous payment method selection	    
        }else if(p_m_name == 'offlinecreditcard'){
	location.href = $('#'+$('input:radio[name="payment_method"]:checked').attr('value')+'_name').val();
        }else if(p_m_name == 'mobilpay_cc'){
        $('#mobilpay_cc_form').submit();
        }
        else if(p_m_name == 'paysera'  || p_m_name == 'pagofacil'){
	location.href = $('#'+$('input:radio[name="payment_method"]:checked').attr('value')+'_name').val();
	}else if(p_m_name == 'bankwire' || p_m_name == 'mercadopago' || p_m_name == 'add_bankwire' || p_m_name == 'edinar' || p_m_name == 'clictopay' || p_m_name == 'allpay' || p_m_name == 'pay2go' || p_m_name == 'cash' || p_m_name == 'postfinance' || p_m_name == 'pagseguro' || p_m_name == 'braintreejs' || p_m_name == 'bcash' || p_m_name == 'invoicepayment' || p_m_name == 'przelewy24' || p_m_name == 'prestalia_cashondelivery' ||  p_m_name == 'virtpaypayment' || p_m_name == 'cashondeliveryfeeplus' || p_m_name == 'pagonlineimprese' || p_m_name == 'mokejimai' || p_m_name == 'payplug' || p_m_name == 'seurcashondelivery' || p_m_name == 'cashondeliveryplus' || p_m_name == 'universalpay' || p_m_name == 'mandiri' || p_m_name == 'bni' || p_m_name == 'bca' || p_m_name == 'veritranspay' || p_m_name == 'przelewy24' || p_m_name == 'transbancaria' || p_m_name == 'cashondeliveryplusmax' || p_m_name == 'multibanco' || p_m_name == 'ceca' || p_m_name == 'dotpay' || p_m_name == 'postepay' || p_m_name == 'paypaladvanced' || p_m_name == 'trustly' || p_m_name == 'billmateinvoice' || p_m_name == 'billmatepartpayment' || p_m_name == 'cheque' || p_m_name == 'westernunion' ||  p_m_name == 'paysondirect' || p_m_name == 'mercadoc' || p_m_name == 'boletosantanderpro' || p_m_name == 'payu' ||  p_m_name == 'librapay' || p_m_name == 'cashondelivery' || p_m_name == 'megashoppay' || p_m_name == 'payulatam' || p_m_name == 'zipcheck' || p_m_name == 'megareembolso' || p_m_name == 'deluxecodfees' || p_m_name == 'payinstore' || p_m_name == 'codfee' || p_m_name == 'obsredsys' || p_m_name == 'hipay' || p_m_name == 'psphipay' || p_m_name == 'finanziamento'){
            if($(dialogContainer+'form').length){
                createPaymentPop();
		$('#velsof_payment_dialog .velsof_action_section').show(); // @nitin 27 July - Important to show proceed button if it is hide in previous payment method selection
            }
            else if(p_m_name == 'paysondirect'){ 
                 disableBtn(); } 
            else { 
                location.href = $('#'+$('input:radio[name="payment_method"]:checked').attr('value')+'_name').val();
            }        
        }else if(p_m_name == 'redsys'){
            $('#redsys_form').submit();
        }else if(p_m_name == 'cecatpv'){
            $('#cecatpv_form').submit();
	}else if(p_m_name == 'firstdata'){
	    hide_progress(); // to hide progress bar in case some error occur in first data payment form
	    $('#firstdata_submit').trigger('click');
	}else if(p_m_name == 'braintreejs_backup'){
	    hide_progress(); // to hide progress bar in case some error occur in first data payment form
	    //$('#braintree-dropin-form').submit();
	}else if(p_m_name == 'stripejs'){
	    hide_progress(); // to hide progress bar in case some error occur in first data payment form
	    $('.stripe-submit-button').trigger('click');
	}else if(p_m_name == 'twocheckout'){
		hide_progress(); // to hide progress bar in case some error occur in first data payment form
	    //('#twocheckoutCCForm input.button').trigger('click');
	    $('#twocheckoutCCForm').submit();
	}else if(p_m_name == 'brinkscheckout'){
		hide_progress(); // to hide progress bar in case some error occur in first data payment form
	    $('#twocheckoutCCForm #submit_payment').trigger('click');
	}else if(p_m_name == 'ewayrapid'){
	    hide_progress(); // to hide progress bar in case some error occur in first data payment form
	    $('#processPayment').trigger('click');
	}else if(p_m_name == 'npaypalpro'){
	    hide_progress(); // to hide progress bar in case some error occur in first data payment form
	    $('.paypalpro-submit-button').trigger('click');
	}else if(p_m_name == 'authorizeaim'){
	    hide_progress(); // to hide progress bar in case some error occur in authorizeaim payment form
	     $('#asubmit').trigger('click');
        }else if(p_m_name == 'iupay'){
            $('#iupay_form').submit();
        }else if(p_m_name == 'cuatrob'){
            $('#cuatrob_form').submit();
        }else if(p_m_name == 'gopay'){
            $('#gopay_form').submit();
        }else if($(dialogContainer+'button').length){
            createPaymentPop();
	    $('#velsof_payment_dialog .velsof_action_section').show(); // @nitin 27 July - Important to show proceed button if it is hide in previous payment method selection
        }else{
            if($(dialogContainer+'button').length){
                $(dialogContainer+'button').trigger('click');
            }else if($(dialogContainer+'form').length){
                $(dialogContainer+'form').trigger('click');
            }else if($(dialogContainer+'a').length){
                $(dialogContainer+'a').trigger('click');
            }else{
                if($('#payment_method_html').length){
                    $('#payment_method_html').html(payment_method_html);
                }
                alert('Payment Processing Error');
            }
        }        
    }
}

function createPaymentPop(){
    $('#velsof_payment_dialog').show();
}

function confirmOrder(){
    var payment_module_name = $('input:radio[name="payment_method"]:checked').attr('id');
    var dialogContainer = '#velsof_payment_dialog .velsof_content_section ';
    if(payment_module_name == 'bankwire' || payment_module_name == 'invoicepayment'){
	$('#velsof_payment_container .velsof_action_section').css('display','none'); //@Nitin Jain, 1-Oct-2015, to hide proceed button on click, because if clickd twice it was showing error.
    }else if(payment_module_name == 'add_gopay_new'){
	document.getElementById('gopay-payment-form').submit(); return false;
    }else if(payment_module_name == 'pms_gopay_extra'){
	document.getElementById('gopay-payment-form').submit(); return false;
    }
    if(payment_module_name == 'bankwire' ||  payment_module_name == 'mercadopago' || payment_module_name == 'add_bankwire' || payment_module_name == 'edinar' || payment_module_name == 'clictopay' || payment_module_name == 'allpay' || payment_module_name == 'pay2go' || payment_module_name == 'cash' || payment_module_name == 'postfinance' || payment_module_name == 'pagseguro' || payment_module_name == 'bcash' || payment_module_name == 'braintreejs' || payment_module_name == 'invoicepayment' || payment_module_name == 'przelewy24' || payment_module_name == 'prestalia_cashondelivery' ||  payment_module_name == 'virtpaypayment' || payment_module_name == 'cashondeliveryfeeplus' || payment_module_name == 'pagonlineimprese' || payment_module_name == 'mokejimai' || payment_module_name == 'payplug' || payment_module_name == 'seurcashondelivery' || payment_module_name == 'cashondeliveryplus' || payment_module_name == 'universalpay' || payment_module_name == 'mandiri' || payment_module_name == 'bni' || payment_module_name == 'bca' || payment_module_name == 'veritranspay' || payment_module_name == 'przelewy24' || payment_module_name == 'transbancaria' || payment_module_name == 'cashondeliveryplusmax' || payment_module_name == 'multibanco' || payment_module_name == 'ceca' || payment_module_name == 'dotpay' || payment_module_name == 'pagofacil' || payment_module_name == 'postepay' || payment_module_name == 'paysera' || payment_module_name == 'offlinecreditcard' || payment_module_name == 'paypaladvanced' || payment_module_name == 'trustly' || payment_module_name == 'cheque' || payment_module_name == 'deluxecodfees'){
        if($(dialogContainer+'form').length){
            $(dialogContainer+'form').submit();
        }else{
            location.href = $('#'+$('input:radio[name="payment_method"]:checked').attr('value')+'_name').val();
        }        
    }else{
        if($(dialogContainer+'button').length){
            $(dialogContainer+'button').trigger('click');
        }else if($(dialogContainer+'form').length){
            $(dialogContainer+'form').trigger('click');
        }else if($(dialogContainer+'a').length){
            $(dialogContainer+'a').trigger('click');
        }else{
            alert('Payment Processing Error');
        }
    }    
}


function displayGeneralError(errors){
    if($('#supercheckout-empty-page-content .permanent-warning').length){
        $('#supercheckout-empty-page-content .permanent-warning').html(errors);
    }else{
        $('#supercheckout-empty-page-content').html('<div class="permanent-warning">'+errors+'</div>');
    }     
}

function hideGeneralError(){
    $('#supercheckout-empty-page-content .permanent-warning').remove();
}

function changePaymentMethodLabel(){
    if($('input:radio[name="payment_method"]').length){
        $('input:radio[name="payment_method"]').each(function(){
            var id = $(this).val();
            if($('#payment_method_'+id+' .payment_method_lbl').length){
                if($('#payment_method_'+id+' .payment_method_lbl a').length){
                    var label = $('#payment_method_'+id+' .payment_method_lbl a').html();
                }else{
                    var label = $('#payment_method_'+id+' .payment_method_lbl').html();
                }
		var current_name = $('#payment_method_name_'+id).html();
		$('#payment_method_name_'+id).after('<span class="fee_lbl"> ('+label+')</span>');
//                $('#payment_lbl_'+id).html(label);
             } 
        });
    }    
}

function subscribeCustomer(email)
{
	$.ajax({
            type: 'POST',
            url: $('#module_url').val() + '&email=' + email,
            async: true,
            cache: false,
            dataType : "json",
            data: 'ajax=true'
                +'&method=addEmailToList',
            beforeSend: function() {
            },
            success: function(jsonData)
            {   
           }
    });
}

function ColorLuminance(hex, lum) {

	// validate hex string
	hex = String(hex).replace(/[^0-9a-f]/gi, '');
	if (hex.length < 6) {
		hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
	}
	lum = lum || 0;

	// convert to decimal and change luminosity
	var rgb = "#", c, i;
	for (i = 0; i < 3; i++) {
		c = parseInt(hex.substr(i*2,2), 16);
		c = Math.round(Math.min(Math.max(0, c + (c * lum)), 255)).toString(16);
		rgb += ("00"+c).substr(c.length);
	}

	return rgb;
}

function upQty(element)
{
	var hidden = parseInt($('#confirmCheckout input[name=' + element + '_hidden]').val());

	hidden = hidden - 1;
	$('#confirmCheckout input[name=' + element + '_hidden]').val(hidden);
//	$('#confirmCheckout input[name=' + element + ']').val(hidden);
	
	updateQty(element);
}
function downQty(element)
{
	var hidden = parseInt($('#confirmCheckout input[name=' + element + '_hidden]').val());
	if (hidden == 1)
	{
		var id = element.replace('quantity_', '');
		deleteProductFromSummary(id);
	}
	else
	{
		hidden = hidden + 1;
		$('#confirmCheckout input[name=' + element + '_hidden]').val(hidden);
	//	$('#confirmCheckout input[name=' + element + ']').val(hidden);
		updateQty(element);
	}
	
}

function validateName(s)
{
	var reg = /^[^0-9!<>,;?=+()@#"°{}_$%:]+$/;
	return reg.test(s);
}

function validateAddress(s)
{
	var reg = /^[^!<>?=+@{}_$%]+$/;
	return reg.test(s);
}

function validateCityName(s)
{
	var reg = /^[^!<>;?=+@#"°{}_$%]+$/;
	return reg.test(s);
}

function validateMessage(s)
{
	var reg = /^[^<>{}]+$/;
	return reg.test(s);
}

function validatePhoneNumber(s)
{
	var reg = /^[+0-9. ()-]+$/;
	return reg.test(s);
}
function validateEmail(s)
{
	var reg = unicode_hack(/^[a-z\p{L}0-9!#$%&'*+\/=?^`{}|~_-]+[.a-z\p{L}0-9!#$%&'*+\/=?^`{}|~_-]*@[a-z\p{L}0-9]+[._a-z\p{L}0-9-]*\.[a-z\p{L}0-9]+$/i, false);
	return reg.test(s);	
}

function validatePasswd(s)
{
	return (s.length >= 5 && s.length < 255);
}

function validateAddressTitle(s)
{
	var reg = /^[^<>={}]+$/;
	return reg.test(s);
}

function validateOnlyNumber(s)
{
	var reg = /^[a-zA-Z0-9]*$/;
	return reg.test(s);
}
var unicode_hack = (function() {
    /* Regexps to match characters in the BMP according to their Unicode category.
       Extracted from Unicode specification, version 5.0.0, source:
       http://unicode.org/versions/Unicode5.0.0/
    */
	var unicodeCategories = {
		Pi:'[\u00ab\u2018\u201b\u201c\u201f\u2039\u2e02\u2e04\u2e09\u2e0c\u2e1c]',
		Sk:'[\u005e\u0060\u00a8\u00af\u00b4\u00b8\u02c2-\u02c5\u02d2-\u02df\u02e5-\u02ed\u02ef-\u02ff\u0374\u0375\u0384\u0385\u1fbd\u1fbf-\u1fc1\u1fcd-\u1fcf\u1fdd-\u1fdf\u1fed-\u1fef\u1ffd\u1ffe\u309b\u309c\ua700-\ua716\ua720\ua721\uff3e\uff40\uffe3]',
		Sm:'[\u002b\u003c-\u003e\u007c\u007e\u00ac\u00b1\u00d7\u00f7\u03f6\u2044\u2052\u207a-\u207c\u208a-\u208c\u2140-\u2144\u214b\u2190-\u2194\u219a\u219b\u21a0\u21a3\u21a6\u21ae\u21ce\u21cf\u21d2\u21d4\u21f4-\u22ff\u2308-\u230b\u2320\u2321\u237c\u239b-\u23b3\u23dc-\u23e1\u25b7\u25c1\u25f8-\u25ff\u266f\u27c0-\u27c4\u27c7-\u27ca\u27d0-\u27e5\u27f0-\u27ff\u2900-\u2982\u2999-\u29d7\u29dc-\u29fb\u29fe-\u2aff\ufb29\ufe62\ufe64-\ufe66\uff0b\uff1c-\uff1e\uff5c\uff5e\uffe2\uffe9-\uffec]',
		So:'[\u00a6\u00a7\u00a9\u00ae\u00b0\u00b6\u0482\u060e\u060f\u06e9\u06fd\u06fe\u07f6\u09fa\u0b70\u0bf3-\u0bf8\u0bfa\u0cf1\u0cf2\u0f01-\u0f03\u0f13-\u0f17\u0f1a-\u0f1f\u0f34\u0f36\u0f38\u0fbe-\u0fc5\u0fc7-\u0fcc\u0fcf\u1360\u1390-\u1399\u1940\u19e0-\u19ff\u1b61-\u1b6a\u1b74-\u1b7c\u2100\u2101\u2103-\u2106\u2108\u2109\u2114\u2116-\u2118\u211e-\u2123\u2125\u2127\u2129\u212e\u213a\u213b\u214a\u214c\u214d\u2195-\u2199\u219c-\u219f\u21a1\u21a2\u21a4\u21a5\u21a7-\u21ad\u21af-\u21cd\u21d0\u21d1\u21d3\u21d5-\u21f3\u2300-\u2307\u230c-\u231f\u2322-\u2328\u232b-\u237b\u237d-\u239a\u23b4-\u23db\u23e2-\u23e7\u2400-\u2426\u2440-\u244a\u249c-\u24e9\u2500-\u25b6\u25b8-\u25c0\u25c2-\u25f7\u2600-\u266e\u2670-\u269c\u26a0-\u26b2\u2701-\u2704\u2706-\u2709\u270c-\u2727\u2729-\u274b\u274d\u274f-\u2752\u2756\u2758-\u275e\u2761-\u2767\u2794\u2798-\u27af\u27b1-\u27be\u2800-\u28ff\u2b00-\u2b1a\u2b20-\u2b23\u2ce5-\u2cea\u2e80-\u2e99\u2e9b-\u2ef3\u2f00-\u2fd5\u2ff0-\u2ffb\u3004\u3012\u3013\u3020\u3036\u3037\u303e\u303f\u3190\u3191\u3196-\u319f\u31c0-\u31cf\u3200-\u321e\u322a-\u3243\u3250\u3260-\u327f\u328a-\u32b0\u32c0-\u32fe\u3300-\u33ff\u4dc0-\u4dff\ua490-\ua4c6\ua828-\ua82b\ufdfd\uffe4\uffe8\uffed\uffee\ufffc\ufffd]',
		Po:'[\u0021-\u0023\u0025-\u0027\u002a\u002c\u002e\u002f\u003a\u003b\u003f\u0040\u005c\u00a1\u00b7\u00bf\u037e\u0387\u055a-\u055f\u0589\u05be\u05c0\u05c3\u05c6\u05f3\u05f4\u060c\u060d\u061b\u061e\u061f\u066a-\u066d\u06d4\u0700-\u070d\u07f7-\u07f9\u0964\u0965\u0970\u0df4\u0e4f\u0e5a\u0e5b\u0f04-\u0f12\u0f85\u0fd0\u0fd1\u104a-\u104f\u10fb\u1361-\u1368\u166d\u166e\u16eb-\u16ed\u1735\u1736\u17d4-\u17d6\u17d8-\u17da\u1800-\u1805\u1807-\u180a\u1944\u1945\u19de\u19df\u1a1e\u1a1f\u1b5a-\u1b60\u2016\u2017\u2020-\u2027\u2030-\u2038\u203b-\u203e\u2041-\u2043\u2047-\u2051\u2053\u2055-\u205e\u2cf9-\u2cfc\u2cfe\u2cff\u2e00\u2e01\u2e06-\u2e08\u2e0b\u2e0e-\u2e16\u3001-\u3003\u303d\u30fb\ua874-\ua877\ufe10-\ufe16\ufe19\ufe30\ufe45\ufe46\ufe49-\ufe4c\ufe50-\ufe52\ufe54-\ufe57\ufe5f-\ufe61\ufe68\ufe6a\ufe6b\uff01-\uff03\uff05-\uff07\uff0a\uff0c\uff0e\uff0f\uff1a\uff1b\uff1f\uff20\uff3c\uff61\uff64\uff65]',
		Mn:'[\u0300-\u036f\u0483-\u0486\u0591-\u05bd\u05bf\u05c1\u05c2\u05c4\u05c5\u05c7\u0610-\u0615\u064b-\u065e\u0670\u06d6-\u06dc\u06df-\u06e4\u06e7\u06e8\u06ea-\u06ed\u0711\u0730-\u074a\u07a6-\u07b0\u07eb-\u07f3\u0901\u0902\u093c\u0941-\u0948\u094d\u0951-\u0954\u0962\u0963\u0981\u09bc\u09c1-\u09c4\u09cd\u09e2\u09e3\u0a01\u0a02\u0a3c\u0a41\u0a42\u0a47\u0a48\u0a4b-\u0a4d\u0a70\u0a71\u0a81\u0a82\u0abc\u0ac1-\u0ac5\u0ac7\u0ac8\u0acd\u0ae2\u0ae3\u0b01\u0b3c\u0b3f\u0b41-\u0b43\u0b4d\u0b56\u0b82\u0bc0\u0bcd\u0c3e-\u0c40\u0c46-\u0c48\u0c4a-\u0c4d\u0c55\u0c56\u0cbc\u0cbf\u0cc6\u0ccc\u0ccd\u0ce2\u0ce3\u0d41-\u0d43\u0d4d\u0dca\u0dd2-\u0dd4\u0dd6\u0e31\u0e34-\u0e3a\u0e47-\u0e4e\u0eb1\u0eb4-\u0eb9\u0ebb\u0ebc\u0ec8-\u0ecd\u0f18\u0f19\u0f35\u0f37\u0f39\u0f71-\u0f7e\u0f80-\u0f84\u0f86\u0f87\u0f90-\u0f97\u0f99-\u0fbc\u0fc6\u102d-\u1030\u1032\u1036\u1037\u1039\u1058\u1059\u135f\u1712-\u1714\u1732-\u1734\u1752\u1753\u1772\u1773\u17b7-\u17bd\u17c6\u17c9-\u17d3\u17dd\u180b-\u180d\u18a9\u1920-\u1922\u1927\u1928\u1932\u1939-\u193b\u1a17\u1a18\u1b00-\u1b03\u1b34\u1b36-\u1b3a\u1b3c\u1b42\u1b6b-\u1b73\u1dc0-\u1dca\u1dfe\u1dff\u20d0-\u20dc\u20e1\u20e5-\u20ef\u302a-\u302f\u3099\u309a\ua806\ua80b\ua825\ua826\ufb1e\ufe00-\ufe0f\ufe20-\ufe23]',
		Ps:'[\u0028\u005b\u007b\u0f3a\u0f3c\u169b\u201a\u201e\u2045\u207d\u208d\u2329\u2768\u276a\u276c\u276e\u2770\u2772\u2774\u27c5\u27e6\u27e8\u27ea\u2983\u2985\u2987\u2989\u298b\u298d\u298f\u2991\u2993\u2995\u2997\u29d8\u29da\u29fc\u3008\u300a\u300c\u300e\u3010\u3014\u3016\u3018\u301a\u301d\ufd3e\ufe17\ufe35\ufe37\ufe39\ufe3b\ufe3d\ufe3f\ufe41\ufe43\ufe47\ufe59\ufe5b\ufe5d\uff08\uff3b\uff5b\uff5f\uff62]',
		Cc:'[\u0000-\u001f\u007f-\u009f]',
		Cf:'[\u00ad\u0600-\u0603\u06dd\u070f\u17b4\u17b5\u200b-\u200f\u202a-\u202e\u2060-\u2063\u206a-\u206f\ufeff\ufff9-\ufffb]',
		Ll:'[\u0061-\u007a\u00aa\u00b5\u00ba\u00df-\u00f6\u00f8-\u00ff\u0101\u0103\u0105\u0107\u0109\u010b\u010d\u010f\u0111\u0113\u0115\u0117\u0119\u011b\u011d\u011f\u0121\u0123\u0125\u0127\u0129\u012b\u012d\u012f\u0131\u0133\u0135\u0137\u0138\u013a\u013c\u013e\u0140\u0142\u0144\u0146\u0148\u0149\u014b\u014d\u014f\u0151\u0153\u0155\u0157\u0159\u015b\u015d\u015f\u0161\u0163\u0165\u0167\u0169\u016b\u016d\u016f\u0171\u0173\u0175\u0177\u017a\u017c\u017e-\u0180\u0183\u0185\u0188\u018c\u018d\u0192\u0195\u0199-\u019b\u019e\u01a1\u01a3\u01a5\u01a8\u01aa\u01ab\u01ad\u01b0\u01b4\u01b6\u01b9\u01ba\u01bd-\u01bf\u01c6\u01c9\u01cc\u01ce\u01d0\u01d2\u01d4\u01d6\u01d8\u01da\u01dc\u01dd\u01df\u01e1\u01e3\u01e5\u01e7\u01e9\u01eb\u01ed\u01ef\u01f0\u01f3\u01f5\u01f9\u01fb\u01fd\u01ff\u0201\u0203\u0205\u0207\u0209\u020b\u020d\u020f\u0211\u0213\u0215\u0217\u0219\u021b\u021d\u021f\u0221\u0223\u0225\u0227\u0229\u022b\u022d\u022f\u0231\u0233-\u0239\u023c\u023f\u0240\u0242\u0247\u0249\u024b\u024d\u024f-\u0293\u0295-\u02af\u037b-\u037d\u0390\u03ac-\u03ce\u03d0\u03d1\u03d5-\u03d7\u03d9\u03db\u03dd\u03df\u03e1\u03e3\u03e5\u03e7\u03e9\u03eb\u03ed\u03ef-\u03f3\u03f5\u03f8\u03fb\u03fc\u0430-\u045f\u0461\u0463\u0465\u0467\u0469\u046b\u046d\u046f\u0471\u0473\u0475\u0477\u0479\u047b\u047d\u047f\u0481\u048b\u048d\u048f\u0491\u0493\u0495\u0497\u0499\u049b\u049d\u049f\u04a1\u04a3\u04a5\u04a7\u04a9\u04ab\u04ad\u04af\u04b1\u04b3\u04b5\u04b7\u04b9\u04bb\u04bd\u04bf\u04c2\u04c4\u04c6\u04c8\u04ca\u04cc\u04ce\u04cf\u04d1\u04d3\u04d5\u04d7\u04d9\u04db\u04dd\u04df\u04e1\u04e3\u04e5\u04e7\u04e9\u04eb\u04ed\u04ef\u04f1\u04f3\u04f5\u04f7\u04f9\u04fb\u04fd\u04ff\u0501\u0503\u0505\u0507\u0509\u050b\u050d\u050f\u0511\u0513\u0561-\u0587\u1d00-\u1d2b\u1d62-\u1d77\u1d79-\u1d9a\u1e01\u1e03\u1e05\u1e07\u1e09\u1e0b\u1e0d\u1e0f\u1e11\u1e13\u1e15\u1e17\u1e19\u1e1b\u1e1d\u1e1f\u1e21\u1e23\u1e25\u1e27\u1e29\u1e2b\u1e2d\u1e2f\u1e31\u1e33\u1e35\u1e37\u1e39\u1e3b\u1e3d\u1e3f\u1e41\u1e43\u1e45\u1e47\u1e49\u1e4b\u1e4d\u1e4f\u1e51\u1e53\u1e55\u1e57\u1e59\u1e5b\u1e5d\u1e5f\u1e61\u1e63\u1e65\u1e67\u1e69\u1e6b\u1e6d\u1e6f\u1e71\u1e73\u1e75\u1e77\u1e79\u1e7b\u1e7d\u1e7f\u1e81\u1e83\u1e85\u1e87\u1e89\u1e8b\u1e8d\u1e8f\u1e91\u1e93\u1e95-\u1e9b\u1ea1\u1ea3\u1ea5\u1ea7\u1ea9\u1eab\u1ead\u1eaf\u1eb1\u1eb3\u1eb5\u1eb7\u1eb9\u1ebb\u1ebd\u1ebf\u1ec1\u1ec3\u1ec5\u1ec7\u1ec9\u1ecb\u1ecd\u1ecf\u1ed1\u1ed3\u1ed5\u1ed7\u1ed9\u1edb\u1edd\u1edf\u1ee1\u1ee3\u1ee5\u1ee7\u1ee9\u1eeb\u1eed\u1eef\u1ef1\u1ef3\u1ef5\u1ef7\u1ef9\u1f00-\u1f07\u1f10-\u1f15\u1f20-\u1f27\u1f30-\u1f37\u1f40-\u1f45\u1f50-\u1f57\u1f60-\u1f67\u1f70-\u1f7d\u1f80-\u1f87\u1f90-\u1f97\u1fa0-\u1fa7\u1fb0-\u1fb4\u1fb6\u1fb7\u1fbe\u1fc2-\u1fc4\u1fc6\u1fc7\u1fd0-\u1fd3\u1fd6\u1fd7\u1fe0-\u1fe7\u1ff2-\u1ff4\u1ff6\u1ff7\u2071\u207f\u210a\u210e\u210f\u2113\u212f\u2134\u2139\u213c\u213d\u2146-\u2149\u214e\u2184\u2c30-\u2c5e\u2c61\u2c65\u2c66\u2c68\u2c6a\u2c6c\u2c74\u2c76\u2c77\u2c81\u2c83\u2c85\u2c87\u2c89\u2c8b\u2c8d\u2c8f\u2c91\u2c93\u2c95\u2c97\u2c99\u2c9b\u2c9d\u2c9f\u2ca1\u2ca3\u2ca5\u2ca7\u2ca9\u2cab\u2cad\u2caf\u2cb1\u2cb3\u2cb5\u2cb7\u2cb9\u2cbb\u2cbd\u2cbf\u2cc1\u2cc3\u2cc5\u2cc7\u2cc9\u2ccb\u2ccd\u2ccf\u2cd1\u2cd3\u2cd5\u2cd7\u2cd9\u2cdb\u2cdd\u2cdf\u2ce1\u2ce3\u2ce4\u2d00-\u2d25\ufb00-\ufb06\ufb13-\ufb17\uff41-\uff5a]',
		Lm:'[\u02b0-\u02c1\u02c6-\u02d1\u02e0-\u02e4\u02ee\u037a\u0559\u0640\u06e5\u06e6\u07f4\u07f5\u07fa\u0e46\u0ec6\u10fc\u17d7\u1843\u1d2c-\u1d61\u1d78\u1d9b-\u1dbf\u2090-\u2094\u2d6f\u3005\u3031-\u3035\u303b\u309d\u309e\u30fc-\u30fe\ua015\ua717-\ua71a\uff70\uff9e\uff9f]',
		Lo:'[\u01bb\u01c0-\u01c3\u0294\u05d0-\u05ea\u05f0-\u05f2\u0621-\u063a\u0641-\u064a\u066e\u066f\u0671-\u06d3\u06d5\u06ee\u06ef\u06fa-\u06fc\u06ff\u0710\u0712-\u072f\u074d-\u076d\u0780-\u07a5\u07b1\u07ca-\u07ea\u0904-\u0939\u093d\u0950\u0958-\u0961\u097b-\u097f\u0985-\u098c\u098f\u0990\u0993-\u09a8\u09aa-\u09b0\u09b2\u09b6-\u09b9\u09bd\u09ce\u09dc\u09dd\u09df-\u09e1\u09f0\u09f1\u0a05-\u0a0a\u0a0f\u0a10\u0a13-\u0a28\u0a2a-\u0a30\u0a32\u0a33\u0a35\u0a36\u0a38\u0a39\u0a59-\u0a5c\u0a5e\u0a72-\u0a74\u0a85-\u0a8d\u0a8f-\u0a91\u0a93-\u0aa8\u0aaa-\u0ab0\u0ab2\u0ab3\u0ab5-\u0ab9\u0abd\u0ad0\u0ae0\u0ae1\u0b05-\u0b0c\u0b0f\u0b10\u0b13-\u0b28\u0b2a-\u0b30\u0b32\u0b33\u0b35-\u0b39\u0b3d\u0b5c\u0b5d\u0b5f-\u0b61\u0b71\u0b83\u0b85-\u0b8a\u0b8e-\u0b90\u0b92-\u0b95\u0b99\u0b9a\u0b9c\u0b9e\u0b9f\u0ba3\u0ba4\u0ba8-\u0baa\u0bae-\u0bb9\u0c05-\u0c0c\u0c0e-\u0c10\u0c12-\u0c28\u0c2a-\u0c33\u0c35-\u0c39\u0c60\u0c61\u0c85-\u0c8c\u0c8e-\u0c90\u0c92-\u0ca8\u0caa-\u0cb3\u0cb5-\u0cb9\u0cbd\u0cde\u0ce0\u0ce1\u0d05-\u0d0c\u0d0e-\u0d10\u0d12-\u0d28\u0d2a-\u0d39\u0d60\u0d61\u0d85-\u0d96\u0d9a-\u0db1\u0db3-\u0dbb\u0dbd\u0dc0-\u0dc6\u0e01-\u0e30\u0e32\u0e33\u0e40-\u0e45\u0e81\u0e82\u0e84\u0e87\u0e88\u0e8a\u0e8d\u0e94-\u0e97\u0e99-\u0e9f\u0ea1-\u0ea3\u0ea5\u0ea7\u0eaa\u0eab\u0ead-\u0eb0\u0eb2\u0eb3\u0ebd\u0ec0-\u0ec4\u0edc\u0edd\u0f00\u0f40-\u0f47\u0f49-\u0f6a\u0f88-\u0f8b\u1000-\u1021\u1023-\u1027\u1029\u102a\u1050-\u1055\u10d0-\u10fa\u1100-\u1159\u115f-\u11a2\u11a8-\u11f9\u1200-\u1248\u124a-\u124d\u1250-\u1256\u1258\u125a-\u125d\u1260-\u1288\u128a-\u128d\u1290-\u12b0\u12b2-\u12b5\u12b8-\u12be\u12c0\u12c2-\u12c5\u12c8-\u12d6\u12d8-\u1310\u1312-\u1315\u1318-\u135a\u1380-\u138f\u13a0-\u13f4\u1401-\u166c\u166f-\u1676\u1681-\u169a\u16a0-\u16ea\u1700-\u170c\u170e-\u1711\u1720-\u1731\u1740-\u1751\u1760-\u176c\u176e-\u1770\u1780-\u17b3\u17dc\u1820-\u1842\u1844-\u1877\u1880-\u18a8\u1900-\u191c\u1950-\u196d\u1970-\u1974\u1980-\u19a9\u19c1-\u19c7\u1a00-\u1a16\u1b05-\u1b33\u1b45-\u1b4b\u2135-\u2138\u2d30-\u2d65\u2d80-\u2d96\u2da0-\u2da6\u2da8-\u2dae\u2db0-\u2db6\u2db8-\u2dbe\u2dc0-\u2dc6\u2dc8-\u2dce\u2dd0-\u2dd6\u2dd8-\u2dde\u3006\u303c\u3041-\u3096\u309f\u30a1-\u30fa\u30ff\u3105-\u312c\u3131-\u318e\u31a0-\u31b7\u31f0-\u31ff\u3400\u4db5\u4e00\u9fbb\ua000-\ua014\ua016-\ua48c\ua800\ua801\ua803-\ua805\ua807-\ua80a\ua80c-\ua822\ua840-\ua873\uac00\ud7a3\uf900-\ufa2d\ufa30-\ufa6a\ufa70-\ufad9\ufb1d\ufb1f-\ufb28\ufb2a-\ufb36\ufb38-\ufb3c\ufb3e\ufb40\ufb41\ufb43\ufb44\ufb46-\ufbb1\ufbd3-\ufd3d\ufd50-\ufd8f\ufd92-\ufdc7\ufdf0-\ufdfb\ufe70-\ufe74\ufe76-\ufefc\uff66-\uff6f\uff71-\uff9d\uffa0-\uffbe\uffc2-\uffc7\uffca-\uffcf\uffd2-\uffd7\uffda-\uffdc]',
		Co:'[\ue000\uf8ff]',
		Nd:'[\u0030-\u0039\u0660-\u0669\u06f0-\u06f9\u07c0-\u07c9\u0966-\u096f\u09e6-\u09ef\u0a66-\u0a6f\u0ae6-\u0aef\u0b66-\u0b6f\u0be6-\u0bef\u0c66-\u0c6f\u0ce6-\u0cef\u0d66-\u0d6f\u0e50-\u0e59\u0ed0-\u0ed9\u0f20-\u0f29\u1040-\u1049\u17e0-\u17e9\u1810-\u1819\u1946-\u194f\u19d0-\u19d9\u1b50-\u1b59\uff10-\uff19]',
		Lt:'[\u01c5\u01c8\u01cb\u01f2\u1f88-\u1f8f\u1f98-\u1f9f\u1fa8-\u1faf\u1fbc\u1fcc\u1ffc]',
		Lu:'[\u0041-\u005a\u00c0-\u00d6\u00d8-\u00de\u0100\u0102\u0104\u0106\u0108\u010a\u010c\u010e\u0110\u0112\u0114\u0116\u0118\u011a\u011c\u011e\u0120\u0122\u0124\u0126\u0128\u012a\u012c\u012e\u0130\u0132\u0134\u0136\u0139\u013b\u013d\u013f\u0141\u0143\u0145\u0147\u014a\u014c\u014e\u0150\u0152\u0154\u0156\u0158\u015a\u015c\u015e\u0160\u0162\u0164\u0166\u0168\u016a\u016c\u016e\u0170\u0172\u0174\u0176\u0178\u0179\u017b\u017d\u0181\u0182\u0184\u0186\u0187\u0189-\u018b\u018e-\u0191\u0193\u0194\u0196-\u0198\u019c\u019d\u019f\u01a0\u01a2\u01a4\u01a6\u01a7\u01a9\u01ac\u01ae\u01af\u01b1-\u01b3\u01b5\u01b7\u01b8\u01bc\u01c4\u01c7\u01ca\u01cd\u01cf\u01d1\u01d3\u01d5\u01d7\u01d9\u01db\u01de\u01e0\u01e2\u01e4\u01e6\u01e8\u01ea\u01ec\u01ee\u01f1\u01f4\u01f6-\u01f8\u01fa\u01fc\u01fe\u0200\u0202\u0204\u0206\u0208\u020a\u020c\u020e\u0210\u0212\u0214\u0216\u0218\u021a\u021c\u021e\u0220\u0222\u0224\u0226\u0228\u022a\u022c\u022e\u0230\u0232\u023a\u023b\u023d\u023e\u0241\u0243-\u0246\u0248\u024a\u024c\u024e\u0386\u0388-\u038a\u038c\u038e\u038f\u0391-\u03a1\u03a3-\u03ab\u03d2-\u03d4\u03d8\u03da\u03dc\u03de\u03e0\u03e2\u03e4\u03e6\u03e8\u03ea\u03ec\u03ee\u03f4\u03f7\u03f9\u03fa\u03fd-\u042f\u0460\u0462\u0464\u0466\u0468\u046a\u046c\u046e\u0470\u0472\u0474\u0476\u0478\u047a\u047c\u047e\u0480\u048a\u048c\u048e\u0490\u0492\u0494\u0496\u0498\u049a\u049c\u049e\u04a0\u04a2\u04a4\u04a6\u04a8\u04aa\u04ac\u04ae\u04b0\u04b2\u04b4\u04b6\u04b8\u04ba\u04bc\u04be\u04c0\u04c1\u04c3\u04c5\u04c7\u04c9\u04cb\u04cd\u04d0\u04d2\u04d4\u04d6\u04d8\u04da\u04dc\u04de\u04e0\u04e2\u04e4\u04e6\u04e8\u04ea\u04ec\u04ee\u04f0\u04f2\u04f4\u04f6\u04f8\u04fa\u04fc\u04fe\u0500\u0502\u0504\u0506\u0508\u050a\u050c\u050e\u0510\u0512\u0531-\u0556\u10a0-\u10c5\u1e00\u1e02\u1e04\u1e06\u1e08\u1e0a\u1e0c\u1e0e\u1e10\u1e12\u1e14\u1e16\u1e18\u1e1a\u1e1c\u1e1e\u1e20\u1e22\u1e24\u1e26\u1e28\u1e2a\u1e2c\u1e2e\u1e30\u1e32\u1e34\u1e36\u1e38\u1e3a\u1e3c\u1e3e\u1e40\u1e42\u1e44\u1e46\u1e48\u1e4a\u1e4c\u1e4e\u1e50\u1e52\u1e54\u1e56\u1e58\u1e5a\u1e5c\u1e5e\u1e60\u1e62\u1e64\u1e66\u1e68\u1e6a\u1e6c\u1e6e\u1e70\u1e72\u1e74\u1e76\u1e78\u1e7a\u1e7c\u1e7e\u1e80\u1e82\u1e84\u1e86\u1e88\u1e8a\u1e8c\u1e8e\u1e90\u1e92\u1e94\u1ea0\u1ea2\u1ea4\u1ea6\u1ea8\u1eaa\u1eac\u1eae\u1eb0\u1eb2\u1eb4\u1eb6\u1eb8\u1eba\u1ebc\u1ebe\u1ec0\u1ec2\u1ec4\u1ec6\u1ec8\u1eca\u1ecc\u1ece\u1ed0\u1ed2\u1ed4\u1ed6\u1ed8\u1eda\u1edc\u1ede\u1ee0\u1ee2\u1ee4\u1ee6\u1ee8\u1eea\u1eec\u1eee\u1ef0\u1ef2\u1ef4\u1ef6\u1ef8\u1f08-\u1f0f\u1f18-\u1f1d\u1f28-\u1f2f\u1f38-\u1f3f\u1f48-\u1f4d\u1f59\u1f5b\u1f5d\u1f5f\u1f68-\u1f6f\u1fb8-\u1fbb\u1fc8-\u1fcb\u1fd8-\u1fdb\u1fe8-\u1fec\u1ff8-\u1ffb\u2102\u2107\u210b-\u210d\u2110-\u2112\u2115\u2119-\u211d\u2124\u2126\u2128\u212a-\u212d\u2130-\u2133\u213e\u213f\u2145\u2183\u2c00-\u2c2e\u2c60\u2c62-\u2c64\u2c67\u2c69\u2c6b\u2c75\u2c80\u2c82\u2c84\u2c86\u2c88\u2c8a\u2c8c\u2c8e\u2c90\u2c92\u2c94\u2c96\u2c98\u2c9a\u2c9c\u2c9e\u2ca0\u2ca2\u2ca4\u2ca6\u2ca8\u2caa\u2cac\u2cae\u2cb0\u2cb2\u2cb4\u2cb6\u2cb8\u2cba\u2cbc\u2cbe\u2cc0\u2cc2\u2cc4\u2cc6\u2cc8\u2cca\u2ccc\u2cce\u2cd0\u2cd2\u2cd4\u2cd6\u2cd8\u2cda\u2cdc\u2cde\u2ce0\u2ce2\uff21-\uff3a]',
		Cs:'[\ud800\udb7f\udb80\udbff\udc00\udfff]',
		Zl:'[\u2028]',
		Nl:'[\u16ee-\u16f0\u2160-\u2182\u3007\u3021-\u3029\u3038-\u303a]',
		Zp:'[\u2029]',
		No:'[\u00b2\u00b3\u00b9\u00bc-\u00be\u09f4-\u09f9\u0bf0-\u0bf2\u0f2a-\u0f33\u1369-\u137c\u17f0-\u17f9\u2070\u2074-\u2079\u2080-\u2089\u2153-\u215f\u2460-\u249b\u24ea-\u24ff\u2776-\u2793\u2cfd\u3192-\u3195\u3220-\u3229\u3251-\u325f\u3280-\u3289\u32b1-\u32bf]',
		Zs:'[\u0020\u00a0\u1680\u180e\u2000-\u200a\u202f\u205f\u3000]',
		Sc:'[\u0024\u00a2-\u00a5\u060b\u09f2\u09f3\u0af1\u0bf9\u0e3f\u17db\u20a0-\u20b5\ufdfc\ufe69\uff04\uffe0\uffe1\uffe5\uffe6]',
		Pc:'[\u005f\u203f\u2040\u2054\ufe33\ufe34\ufe4d-\ufe4f\uff3f]',
		Pd:'[\u002d\u058a\u1806\u2010-\u2015\u2e17\u301c\u3030\u30a0\ufe31\ufe32\ufe58\ufe63\uff0d]',
		Pe:'[\u0029\u005d\u007d\u0f3b\u0f3d\u169c\u2046\u207e\u208e\u232a\u2769\u276b\u276d\u276f\u2771\u2773\u2775\u27c6\u27e7\u27e9\u27eb\u2984\u2986\u2988\u298a\u298c\u298e\u2990\u2992\u2994\u2996\u2998\u29d9\u29db\u29fd\u3009\u300b\u300d\u300f\u3011\u3015\u3017\u3019\u301b\u301e\u301f\ufd3f\ufe18\ufe36\ufe38\ufe3a\ufe3c\ufe3e\ufe40\ufe42\ufe44\ufe48\ufe5a\ufe5c\ufe5e\uff09\uff3d\uff5d\uff60\uff63]',
		Pf:'[\u00bb\u2019\u201d\u203a\u2e03\u2e05\u2e0a\u2e0d\u2e1d]',
		Me:'[\u0488\u0489\u06de\u20dd-\u20e0\u20e2-\u20e4]',
		Mc:'[\u0903\u093e-\u0940\u0949-\u094c\u0982\u0983\u09be-\u09c0\u09c7\u09c8\u09cb\u09cc\u09d7\u0a03\u0a3e-\u0a40\u0a83\u0abe-\u0ac0\u0ac9\u0acb\u0acc\u0b02\u0b03\u0b3e\u0b40\u0b47\u0b48\u0b4b\u0b4c\u0b57\u0bbe\u0bbf\u0bc1\u0bc2\u0bc6-\u0bc8\u0bca-\u0bcc\u0bd7\u0c01-\u0c03\u0c41-\u0c44\u0c82\u0c83\u0cbe\u0cc0-\u0cc4\u0cc7\u0cc8\u0cca\u0ccb\u0cd5\u0cd6\u0d02\u0d03\u0d3e-\u0d40\u0d46-\u0d48\u0d4a-\u0d4c\u0d57\u0d82\u0d83\u0dcf-\u0dd1\u0dd8-\u0ddf\u0df2\u0df3\u0f3e\u0f3f\u0f7f\u102c\u1031\u1038\u1056\u1057\u17b6\u17be-\u17c5\u17c7\u17c8\u1923-\u1926\u1929-\u192b\u1930\u1931\u1933-\u1938\u19b0-\u19c0\u19c8\u19c9\u1a19-\u1a1b\u1b04\u1b35\u1b3b\u1b3d-\u1b41\u1b43\u1b44\ua802\ua823\ua824\ua827]'
	};
	/* Also supports the general category (only the first letter) */
	var firstLetters = {};
	for (var p in unicodeCategories)
	{
		if (firstLetters[p[0]])
			firstLetters[p[0]] = unicodeCategories[p].substring(0,unicodeCategories[p].length-1) + firstLetters[p[0]].substring(1);
		else
			firstLetters[p[0]] = unicodeCategories[p];
	}
	for (var p in firstLetters)
		unicodeCategories[p] = firstLetters[p];

	/* Gets a regex written in a dialect that supports unicode categories and
	   translates it to a dialect supported by JavaScript. */
	return function(regexpString, classes) 
	{
		var modifiers = "";
		if ( regexpString instanceof RegExp ) {
			modifiers = (regexpString.global ? "g" : "") +
						(regexpString.ignoreCase ? "i" : "") +
						(regexpString.multiline ? "m" : "");
			regexpString = regexpString.source;
		}
		regexpString = regexpString.replace(/\\p\{(..?)\}/g, function(match,group) {
		var unicode_categorie = unicodeCategories[group];
		if (!classes)
			unicode_category = unicode_categorie.replace(/\[(.*?)\]/g,"$1")
			return unicode_category || match;
		});
		return new RegExp(regexpString,modifiers);
	};

})();
