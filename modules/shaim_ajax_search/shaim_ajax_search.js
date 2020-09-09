$(function () {
    if (typeof ajaxsearch === 'undefined' || !ajaxsearch) { // Když je to zapnuto u defautlniho modulu, tak to nechceme pouzivat, aby to tam nebylo 2x
        var input = $(shaim_ajax_search_target);
        if (input.length != 1) {
            var input = $("#search_query_top");
        }
        if (input.length != 1) {
            console.log('Nenašli jsme vyhledávací input, kontaktujte vývojáře modulu.');
        } else {

            var width_ac_results = input.parent('form').outerWidth();
            input.autocomplete(
                hledat_shaim_ajax_search,
                {
                    minChars: shaim_ajax_search_min,
                    max: shaim_ajax_search_count,
                    width: (width_ac_results > 0 ? width_ac_results : 500),
                    selectFirst: false,
                    scroll: false,
                    dataType: "json",
                    resultsClass: "ac_results search-results",
                    formatItem: function (data, i, max, value, term) {
                        return value;
                    },
                    parse: function (data) {

                        var mytab = [];
                        for (var i = 0; i < data.length; i++) {
                            // console.log(data[i]);
                            $tvalue = '<div class="search-item-wrapper">';
                            if (data[i].image) {
                                $tvalue += '<span class="cover"><img src="' + data[i].image + '" />' + '</span>';
                            }
                            $tvalue += '<span class="info">';
                            $tvalue += '<span>' + data[i].cname + ' > ' + data[i].pname + '</span>';
                            if (data[i].price) {
                                $tvalue += '<span class="pprice">' + data[i].price + '</span>';
                            }

                            $tvalue += '</span>';
                            $tvalue += '</div>';

                            mytab[mytab.length] = {data: data[i], value: $tvalue};
                        }

                        return mytab;
                    },
                    extraParams: {
                        asi_nic: '123',
                    }
                }
                )
                .result(function (event, data, formatted) {
                    input.val(data.pname);
                    document.location.href = data.product_link;
                });
        }
    }
});