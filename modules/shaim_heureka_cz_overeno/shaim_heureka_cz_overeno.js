function HeurekaLoadNow() {
    if ($("#shaim_heureka_cz_overeno_ajax_url").length && $("#shaim_heureka_cz_overeno_id_cart").length) {
        $(document).on('click', 'input#shaim_heureka_checkbox', function () {
            $.ajax
            ({
                async: true,
                cache: false,
                url: $("#shaim_heureka_cz_overeno_ajax_url").val(),
                data: {
                    'id_cart': parseInt($("#shaim_heureka_cz_overeno_id_cart").val()),
                    'checkbox': ($('#shaim_heureka_checkbox').is(':checked') == true ? 1 : 0)
                },
                type: 'post',
                success: function (result) {
                    // console.log(result);
                    if (result == '1') {
                        // console.log('OK - ' + $("#shaim_heureka_cz_overeno_id_cart").val() + ' - ' + $('#shaim_heureka_checkbox').is(':checked'))
                    } else {
                        //  console.log('KO - ' + $("#shaim_heureka_cz_overeno_id_cart").val() + ' - ' + $('#shaim_heureka_checkbox').is(':checked'))
                    }
                }

            });
        });

    }
}
$(function () {
    if ($("#module-supercheckout-supercheckout").length) {
        $(window).load(function () {// Nutno pro supercheckout
            HeurekaLoadNow();
        });
    } else {
        HeurekaLoadNow();
    }
});