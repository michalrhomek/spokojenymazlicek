$(function () {

    if ($("#shaim_recaptcha_contact_form").length) {
        console.log('injecting recaptcha - contact form');
        if ($('#submitMessage').length) {
            $('.g-recaptcha').insertBefore($('#submitMessage').parent()).show(); // 1.6
        } else {
            $('.g-recaptcha').insertBefore($('input[name=submitMessage]').parent()).show(); //1 .7
        }
    } else if ($("#shaim_recaptcha_register").length) {
        var checkExist = setInterval(function () {
            if ($('#submitAccount').length) {
                console.log('injecting recaptcha - register');
                clearInterval(checkExist);
                $('.g-recaptcha').insertBefore($('#submitAccount').parent()).show();
            }
            /* vlozime ji tam, ale uz ji neumime validovat pres php
            else if ($('form#customer-form button[type=submit]').length) { // 1.7
                console.log('injecting recaptcha - register');
                clearInterval(checkExist);
                $('.g-recaptcha').insertBefore($('form#customer-form button[type=submit]').parent()).show();
            }
            */
        }, 1000);

    }
});