function desintifier(i, delimiter) { // Pro 1.5 funkce
    i += ''; // cast as string
    delimiter = typeof delimiter !== 'undefined' ? delimiter : ',';
    delimiter_len = parseInt(i.substr(0, 1));
    i = i.substr(1); // removes the delim length
    var str = '';
    for (var j = 0; j <= delimiter_len; j++) str += '0';
    var nums = i.split(str); // splits
    // return nums.join(',');
    return nums.join('');
}

function LoadNowBalikovna() {
    if (exists_opc != 'onepagecheckoutps' && exists_opc != 'onepagecheckout' && exists_opc != 'spstepcheckout') { // tento modul uz nemusime resit takhle, protoze to nacitame pres ajax
        if (typeof ps_version != 'undefined' && typeof wh_balikovna != 'undefined') {
            if (wh_balikovna) {
                console.log('Load #0');
                MoveInputToCarrierBalikovna();
                IsAlreadySelectedBalikovna();
            }
        }
    }

}

$(function () {
    // First load, pokud to je defaultni J
    if (typeof exists_opc != 'undefined' && (exists_opc == 'supercheckout' || exists_opc == 'spstepcheckout' || exists_opc == 'onepagecheckout' || exists_opc == 'onepagecheckoutps' || exists_opc == 'steasycheckout' || exists_opc == 'thecheckout')) {
        $(window).load(function () {// Nutno pro supercheckout a dalsi vyjemnovane kosiky
            LoadNowBalikovna();
        });
    } else {
        LoadNowBalikovna();
    }
});

// 1.5 pro vicekrokovou obj
$(document).on("click", "input[name=id_carrier]", function () {
    if (typeof ps_version != 'undefined' && ps_version == '1.5' && $("#carrierTable input[name=id_carrier]:checked").length && ($("body#order").length || $("body#order-opc").length)) { // detekce vicekrokove obj

        checked_carrier_balikovna = desintifier(parseInt($("#carrierTable input[name=id_carrier]:checked").val()));

        if (checked_carrier_balikovna > 0 && checked_carrier_balikovna == dopravce_cz_balikovna) {
            MoveInputToCarrierBalikovna();
        } else {
            DeleteMessageBalikovna();
        }
    }
});

// 1.5 pro vicekrokovou obj (strasi PS typu 1.5.4.0)
$(document).on('click', 'input.delivery_option_radio, div.delivery_option_logo, div.carrier_delay', function () {
    if (typeof ps_version != 'undefined' && (ps_version == '1.5' || (typeof exists_opc != 'undefined' && exists_opc == 'threepagecheckout') && $("#carrier_area input.delivery_option_radio:checked").length && ($("body#order").length || $("body#order-opc").length))) { // detekce vicekrokove obj

        checked_carrier_balikovna = parseInt($("#carrier_area input.delivery_option_radio:checked").val());

        if (checked_carrier_balikovna > 0 && checked_carrier_balikovna == dopravce_cz_balikovna) {
            MoveInputToCarrierBalikovna();
        } else {
            DeleteMessageBalikovna();
        }
    } else if (typeof ps_version != 'undefined' && ps_version == '1.7' && $("body#checkout").length) { // 1.7
        checked_carrier_balikovna = parseInt($(".delivery-options input.delivery_option_radio:checked").val());

        if (checked_carrier_balikovna > 0 && checked_carrier_balikovna == dopravce_cz_balikovna) {
            $("body").addClass('click3');
            console.log('Click #3');
            MoveInputToCarrierBalikovna();
        } else {
            DeleteMessageBalikovna();
        }
    } else if (typeof ps_version != 'undefined' && ps_version == '1.7' && $("body#module-supercheckout-supercheckout").length) { // 1.7 supercheckout
        checked_carrier_balikovna = parseInt($(".delivery-option input.delivery_option_radio:checked").val());

        if (checked_carrier_balikovna > 0 && checked_carrier_balikovna == dopravce_cz_balikovna) {
            $("body").addClass('click3');
            console.log('Click #3');
            MoveInputToCarrierBalikovna();
        } else {
            DeleteMessageBalikovna();
        }
    }
});

$(document).ajaxComplete(function (event, xhr, settings) {
    if (typeof ps_version != 'undefined') {
        good_ajax = false;
        if (ps_version == '1.7' && (settings.url.match(/selectDeliveryOption/) || settings.url.match(/selectDeliveryOption/))) {
            good_ajax = true;
            checked_carrier_balikovna = parseInt($(".delivery-option input:checked").val());
        } else if (ps_version == '1.5' && $("#carrierTable input[name=id_carrier]:checked").length && (settings.url.match(/updateCarrierAndGetPayments/) || (typeof settings.data != 'undefined' && settings.data.match(/updateCarrierAndGetPayments/)))) { // Pro jednostránkovou obj
            good_ajax = true;
            checked_carrier_balikovna = desintifier(parseInt($("#carrierTable input[name=id_carrier]:checked").val()));
        } else if (ps_version == '1.5' && $("#carrier_area input.delivery_option_radio:checked").length && (settings.url.match(/updateCarrierAndGetPayments/) || (typeof settings.data != 'undefined' && settings.data.match(/updateCarrierAndGetPayments/)))) { // Pro jednostránkovou obj
            good_ajax = true;
            checked_carrier_balikovna = parseInt($("#carrier_area input.delivery_option_radio:checked").val());
        } else if (ps_version == '1.6' && $("#carrierTable input[name=id_carrier]:checked").length && exists_opc == 'onepagecheckout' && typeof settings.data != 'undefined' && (settings.data.match(/updateCarrierAndGetPayments/) || settings.data.match(/updateAddressesSelected/))) {
            good_ajax = true;
            checked_carrier_balikovna = desintifier(parseInt($("#carrierTable input[name=id_carrier]:checked").val()));
        } else if (ps_version == '1.6' && $("input.delivery_option_radio:checked").length && exists_opc == 'onepagecheckout' && typeof settings.data != 'undefined' && (settings.data.match(/updateCarrierAndGetPayments/) || settings.data.match(/updateAddressesSelected/))) {
            good_ajax = true;
            checked_carrier_balikovna = parseInt($("input.delivery_option_radio:checked").val());
        } else if (ps_version == '1.7' && exists_opc == 'thecheckout' && typeof settings.data != 'undefined' && settings.data.match(/modifyAccountAndAddress/)) {
            good_ajax = true;
            checked_carrier_balikovna = parseInt($(".delivery-option input[type='radio']:checked").val());
        } else if (
            (exists_opc == 'supercheckout' && settings.url.match(/supercheckout/) && typeof settings.data !== 'undefined' && settings.data.match(/updateCarrier/)) ||
            (exists_opc == 'supercheckout' && settings.url.match(/supercheckout/) && typeof settings.data !== 'undefined' && settings.data.match(/loadCarriers/)) ||
            (exists_opc == 'onepagecheckoutps' && typeof settings.data !== 'undefined' && settings.data.match(/updateCarrier/)) ||
            (exists_opc == 'spstepcheckout' && typeof settings.data !== 'undefined' && settings.data.match(/updateCarrier/)) ||
            (exists_opc == 'spstepcheckout' && typeof settings.url !== 'undefined' && settings.url.match(/addressForm/))
        ) { // supercheckout + onepagecheckoutps

            good_ajax = true;
            checked_carrier_balikovna = parseInt($("input.delivery_option_radio:checked").val());
        } else if (exists_opc == 'steasycheckout' && typeof settings.url !== 'undefined' && settings.url.match(/steasycheckout/) && settings.url.match(/action=update/)) {
            good_ajax = true;
            checked_carrier_balikovna = parseInt($(".delivery-option input[type='radio']:checked").val());
        }
        if (good_ajax) {
            if (typeof dopravce_cz_balikovna != 'undefined' && checked_carrier_balikovna == dopravce_cz_balikovna) {
                // U 1.7 u modulu spstepcheckout nepotrebujeme tenhle ajax, protoze to dela Click #3
                // if ((exists_opc == 'spstepcheckout' || exists_opc == 'onepagecheckoutps') && $("body#checkout").length && $(".delivery-options input.delivery_option_radio:checked").length && $("body").hasClass('click3')) {
                if (
                    (exists_opc == 'onepagecheckoutps' && $("body#checkout").length && $(".delivery-options input.delivery_option_radio:checked").length && $("body").hasClass('click3'))
                    ||
                    (exists_opc == 'supercheckout' && $("body#module-supercheckout-supercheckout").length && $(".delivery-option input.delivery_option_radio:checked").length && $("body").hasClass('click3'))
                ) {
                    $("body").removeClass('click3');
                    console.log(('Skipujeme tento ajax, resi to Click #3'));
                    MoveInputToCarrierBalikovna();
                } else {
                    console.log('Ajax #4');
                    MoveInputToCarrierBalikovna();
                    IsAlreadySelectedBalikovna();
                }
            } else {
                DeleteMessageBalikovna();
            }
        }
        // kdyz menime zemi, tak se zmeni i dopravci, tak at to reloadneme :) Tim se tam zasilkovna propise J.
        if (exists_opc == 'advancedcheckout' && typeof settings.data != 'undefined' && settings.data.match(/loadcarrier/)) {
            setTimeout(function () {
                // updcountry - realne to je tohle, ale bezi to moc brzy, takze no way.
                console.log('Změna státu - advancedcheckout');
                updcarrieraddress(1);
            }, 2000);
        }
    }
});

function IsAlreadySelectedBalikovna() {
    console.log('IsAlreadySelectedBalikovna');

    if (!exists_opc) {
        timeout_for_alreadyselected = 500;
    } else {
        timeout_for_alreadyselected = 1000;
    }

    setTimeout(function () {
        $.ajax
        ({
            async: true,
            cache: false,
            url: hledat_balikovna_ajax,
            data: {'id_cart': id_cart, 'already_selected': 1},
            type: 'post',
            tryCount: 0,
            retryLimit: 1,
            success: function (result) {
                if ($("#find_balikovna_zip_city").is(":visible")) {
                    console.log('AlreadySelected - ' + result);
                    if (result > 0) {
                        $("#find_balikovna_zip_city").val(result).keyup();
                        loop_fix = 0;
                        var checkExist = setInterval(function () {
                            loop_fix++;
                            if ($('.vybrat_balikovna').length) {
                                $(".vybrat_balikovna").click();
                                clearInterval(checkExist);
                            } else if (loop_fix > 20) {
                                clearInterval(checkExist);
                            }
                        }, 100); // check every 100ms
                    }
                }
            }, error: function (result, status) {
                if (status == 'timeout') { // 408 Request Timeout prevent apod.
                    this.tryCount++;
                    if (this.tryCount <= this.retryLimit) {
                        //try again
                        console.log('run again');
                        $.ajax(this);
                        return;
                    }
                }
            }
        });
    }, timeout_for_alreadyselected);
}


$(document).on("keyup", "input#find_balikovna_zip_city", function () {
    var hodnota_z_inputu = $(this).val();
    if (hodnota_z_inputu.length < 2) {
        $("#result_balikovna_zip_city").html(shaim_balikovna_prazdne).show();
    } else {
        $.ajax({
            type: "POST",
            url: hledat_balikovna_ajax,
            data: 'naz_prov=' + hodnota_z_inputu + '&text_balikovna=' + text_balikovna + '&text_adresa=' + text_adresa + '&text_zvolit_balikovna=' + text_zvolit_balikovna + '&text_vybrat_balikovna=' + text_vybrat_balikovna,
            async: true,
            cache: false,
            tryCount: 0,
            retryLimit: 1,
            success: function (html) {
                if (!html || html == 0) {
                    html = shaim_balikovna_nic;
                    $("#result_balikovna_zip_city").html(html).show();
                } else {
                    $("#result_balikovna_zip_city").html(html).show();
                }
            }, error: function (result, status) {
                if (status == 'timeout') { // 408 Request Timeout prevent apod.
                    this.tryCount++;
                    if (this.tryCount <= this.retryLimit) {
                        //try again
                        console.log('run again');
                        $.ajax(this);
                        return;
                    }
                }
            }
        });
    }

    return false;
});


/** Ukládáme pobočku **/
$(document).on("click", "input.vybrat_balikovna", function () {
    var psc = $(this).data("psc");
    var full = $(this).data("full");
    var text = zvolena_balikovna + ': ' + full;
    $('div#result_balikovna_zip_city').text(text);


    $.ajax
    ({
        async: true,
        cache: false,
        url: hledat_balikovna_ajax,
        data: {'text': text, 'id_cart': id_cart, 'psc': psc, 'id_customer': id_customer, 'submit_message': 1},
        type: 'post',
        tryCount: 0,
        retryLimit: 1,
        success: function (result) {
            console.log('submit_message - balikovna - ' + result);
        }, error: function (result, status) {
            if (status == 'timeout') { // 408 Request Timeout prevent apod.
                this.tryCount++;
                if (this.tryCount <= this.retryLimit) {
                    //try again
                    console.log('run again');
                    $.ajax(this);
                    return;
                }
            }
        }
    });
});

/* 1.7 pri kliknuti zpet na zpusob prepravy z plateb */
$(document).on("click", "#checkout-delivery-step > h1", function () {
    IsAlreadySelectedBalikovna();
});

$(document).on("click", "#opc_payment_methods-content .payment_module > a, form .cart_navigation > input[name=processCarrier], #carrier_area button[name=processCarrier], #js-delivery button[name=confirmDeliveryOption], #summary-confirm-order", function () {
    return CheckHardBalikovna();
});

function DefiniceCheckedBalikovna() {
    if ($("#carrier_area input.delivery_option_radio:checked").length) { // 1.6
        checked_carrier_balikovna = parseInt($("#carrier_area input.delivery_option_radio:checked").val());
    } else if ($("#carrierTable input[name=id_carrier]:checked").length && (ps_version == '1.5' || exists_opc == 'onepagecheckout')) { // 1.5
        checked_carrier_balikovna = desintifier(parseInt($("#carrierTable input[name=id_carrier]:checked").val()));
    } else if ($(".delivery-option input[type='radio']:checked").length) { // 1.7
        checked_carrier_balikovna = parseInt($(".delivery-option input[type='radio']:checked").val());
    } else if ($("#opc_delivery_methods input.delivery_option_radio:checked").length) { // advancedcheckout
        checked_carrier_balikovna = parseInt($("#opc_delivery_methods input.delivery_option_radio:checked").val());
    } else if ($("#opc_checkout input.delivery_option_radio:checked").length) { // onepagecheckout
        checked_carrier_balikovna = parseInt($("#opc_checkout input.delivery_option_radio:checked").val());
    } else if ($("#module-supercheckout-supercheckout input.delivery_option_radio:checked").length) { // supercheckout
        checked_carrier_balikovna = parseInt($("#module-supercheckout-supercheckout input.delivery_option_radio:checked").val());
    } else if ($("#shipping_container input.delivery_option_radio:checked").length) { // spstepcheckout
        checked_carrier_balikovna = parseInt($("#shipping_container input.delivery_option_radio:checked").val());
    } else { // 1.5
        checked_carrier_balikovna = parseInt($("input[name=id_carrier]:checked").val());
    }

    // DESINTIFIER FIX
    if (checked_carrier_balikovna >= 100000) {
        checked_carrier_balikovna = desintifier(checked_carrier_balikovna);
    }
}

function CheckHardBalikovna() {

    DefiniceCheckedBalikovna();


    if (typeof checked_carrier_balikovna != 'undefined' && checked_carrier_balikovna > 0 && ((typeof dopravce_cz_balikovna != 'undefined' && checked_carrier_balikovna == dopravce_cz_balikovna) || (typeof dopravce_sk_balikovna != 'undefined' && checked_carrier_balikovna == dopravce_sk_balikovna))) {
        if (
            ($('div#result_balikovna_zip_city').text() == shaim_balikovna_nic)
            || ($('div#result_balikovna_zip_city').text() == shaim_balikovna_prazdne)
            || ($('div#result_balikovna_zip_city').text() != '' && !$('div#result_balikovna_zip_city').text().match(zvolena_balikovna))
        ) {
            if (ps_version == '1.5' || ps_version == '1.7') {
                alert(shaim_balikovna_nejdrive);
            } else {
                $.fancybox.open([
                    {
                        type: 'inline',
                        autoScale: true,
                        minHeight: 30,
                        content: '<p class="fancybox-error">' + shaim_balikovna_nejdrive + '</p>'
                    }
                ], {
                    padding: 0
                });
            }
            // event.preventDefault();
            return false;
        }
    }
    return true;
}

function MoveInputToCarrierBalikovna() {
    console.log('MoveInputToCarrierBalikovna');
    if (exists_opc == 'spstepcheckout') {
        $("#vyhledejte_pobocku_balikovna").hide();
        $("#vyhledejte_pobocku_balikovna").addClass("col-xs-12");
    } else if (exists_opc == 'onepagecheckout' && $(".delete_srsly_balikovna").length) {
        $(".delete_srsly_balikovna").hide();
    }
    nalezeno = false;

    if (exists_opc == 'onepagecheckout' && $("input.delivery_option_radio:checked").parents("tr").find("td:nth-child(2)").length && !$("input.delivery_option_radio:checked").parents("tr").find("td:nth-child(2)").is(":hidden")) {
        $("#vyhledejte_pobocku_balikovna").appendTo($("input.delivery_option_radio:checked").parents("tr").find("td:nth-child(2):first"));
        nalezeno = true;
    } else if (exists_opc == 'onepagecheckout' && $("input.delivery_option_radio:checked").parents("tr").find("td:nth-child(3)").length) {
        $("#vyhledejte_pobocku_balikovna").appendTo($("input.delivery_option_radio:checked").parents("tr").find("td:nth-child(3):first"));
        nalezeno = true;
    } else if (exists_opc == 'onepagecheckout' && $("input[name='id_carrier']:checked").parents("tr").find('.carrier_name').length) {
        $("#vyhledejte_pobocku_balikovna").insertAfter($("input[name='id_carrier']:checked").parents("tr")).wrap("<tr class='delete_srsly_balikovna'><td colspan='4'></td></tr>");
        nalezeno = true;
    } else if (exists_opc == 'onepagecheckoutps' && ps_version == '1.7' && $("input.delivery_option_radio:checked").parents("div.pts-vcenter").length) {
        $("#vyhledejte_pobocku_balikovna").insertAfter($("input.delivery_option_radio:checked").parents("div.pts-vcenter"));
        nalezeno = true;
    } else if (exists_opc == 'onepagecheckoutps' && $("input.delivery_option_radio:checked").parents("div.pts-vcenter").length) {
        $("#vyhledejte_pobocku_balikovna").appendTo($("input.delivery_option_radio:checked").parents("div.pts-vcenter"));
        nalezeno = true;
    } else if (exists_opc == 'thecheckout' && $(".delivery-option input[type='radio']:checked").parents(".delivery-option").length) { // 1.7
        $("#vyhledejte_pobocku_balikovna").appendTo($(".delivery-option input[type='radio']:checked").parents(".delivery-option").next());
        $("#vyhledejte_pobocku_balikovna").parent(".carrier-extra-content").show();
        nalezeno = true;
    } else if (ps_version == '1.5' && $("input.delivery_option_radio:checked").parents("tr").find("td:nth-child(2)").length) { // 1.5 older
        $("#vyhledejte_pobocku_balikovna").appendTo($("input.delivery_option_radio:checked").parents("tr").find("td:nth-child(2):first"));
        nalezeno = true;
    } else if (ps_version == '1.5' && $("input.delivery_option_radio:checked").parents("div.delivery_option").find("td:nth-child(2)").length) { // 1.5 older
        $("#vyhledejte_pobocku_balikovna").appendTo($("input.delivery_option_radio:checked").parents("div.delivery_option").find("td:nth-child(2)"));
        nalezeno = true;
    } else if (exists_opc == 'supercheckout' && $("input.delivery_option_radio:checked").parents("tr").find("td:nth-child(2)").length) { // 1.6
        $("#vyhledejte_pobocku_balikovna").appendTo($("input.delivery_option_radio:checked").parents("tr").find("td:nth-child(2):first"));
        nalezeno = true;
    } else if (exists_opc == 'supercheckout' && $("input.delivery_option_radio:checked").parents("li").find("div.radio").length) { // 1.7
        $("#vyhledejte_pobocku_balikovna").insertAfter($("input.delivery_option_radio:checked").parents("li").find("div.radio"));
        $("#vyhledejte_pobocku_balikovna").find("input").removeClass("btn-primary");
        nalezeno = true;
    } else if (exists_opc == 'spstepcheckout' && $("input.delivery_option_radio:checked").parents("div.delivery-option").find("div.row").length) { // 1.7
        $("#vyhledejte_pobocku_balikovna").appendTo($("input.delivery_option_radio:checked").parents("div.delivery-option").find("div.row"));
        nalezeno = true;
    } else if ($("input.delivery_option_radio:checked").parents("tr").find("td:nth-child(3)").length) { // 1.6
        $("#vyhledejte_pobocku_balikovna").appendTo($("input.delivery_option_radio:checked").parents("tr").find("td:nth-child(3):first"));
        nalezeno = true;
    } else if (ps_version == '1.7' && $(".delivery-option input[type='radio']:checked").parents(".delivery-option").length && window.matchMedia("only screen and (max-width: 760px)")) { // 1.7 mobil
        $("#vyhledejte_pobocku_balikovna").insertAfter($(".delivery-option input[type='radio']:checked").parents(".delivery-option"));
        nalezeno = true;
    } else if ($(".delivery-option input[type='radio']:checked").parents(".delivery-option").length) { // 1.7 PC
        $("#vyhledejte_pobocku_balikovna").appendTo($(".delivery-option input[type='radio']:checked").parents(".delivery-option"));
        nalezeno = true;
    } else if ($("input[name='id_carrier']:checked").parents(".alternate_item").find('.carrier_name').length) { // 1.5
        $("#vyhledejte_pobocku_balikovna").appendTo($("input[name='id_carrier']:checked").parents(".alternate_item").find('.carrier_name'));
        nalezeno = true;
    } else if ($("input[name='id_carrier']:checked").parents(".item").find('.carrier_name').length) { // 1.5 jiny zpusob
        $("#vyhledejte_pobocku_balikovna").appendTo($("input[name='id_carrier']:checked").parents(".item").find('.carrier_name'));
        nalezeno = true;
    }

    if (nalezeno) {
        if (typeof ps_version != 'undefined') {
            $("#vyhledejte_pobocku_balikovna").show(); // PS 1.7
        }
        $(".hook_extracarrier > #vyhledejte_pobocku_balikovna").remove();
    }
}

function DeleteMessageBalikovna() {

    $.ajax
    ({
        async: true,
        cache: false,
        url: hledat_balikovna_ajax,
        data: {'id_cart': id_cart, 'delete_message': 1},
        type: 'post',
        tryCount: 0,
        retryLimit: 1,
        success: function (result) {
            $("#result_balikovna_zip_city").text($("#result_balikovna_zip_city").data("title"));
            $("#find_balikovna_zip_city").val('');

            if (typeof ps_version != 'undefined') {
                if (exists_opc == 'spstepcheckout') {
                    $("#vyhledejte_pobocku_balikovna").remove(); // PS 1.7
                } else {
                    $("#vyhledejte_pobocku_balikovna").hide(); // PS 1.7
                }
                if (exists_opc == 'onepagecheckout' && $(".delete_srsly_balikovna").length) {
                    $(".delete_srsly_balikovna").hide();
                }
            } else {
                $("#vyhledejte_pobocku_balikovna").remove();
            }

            console.log('delete_message - balikovna - ' + result);
        }, error: function (result, status) {
            if (status == 'timeout') { // 408 Request Timeout prevent apod.
                this.tryCount++;
                if (this.tryCount <= this.retryLimit) {
                    //try again
                    console.log('run again');
                    $.ajax(this);
                    return;
                }
            }
        }
    });
}
