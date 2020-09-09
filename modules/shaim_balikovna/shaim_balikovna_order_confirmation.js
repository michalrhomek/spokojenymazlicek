$(function () {

    if (typeof SMap === 'undefined' || !$.isFunction(SMap) || typeof adresa === 'undefined' || typeof odpoved === 'undefined') {
        return false;
    }

    new SMap.Geocoder(adresa, odpoved);

    function odpoved(geocoder) {
        if (!geocoder.getResults()[0].results.length) {
            $("#shaim_balikovna_map").hide();
            return;
        }

        var vysledky = geocoder.getResults()[0].results;
        while (vysledky.length) { /* Zobrazit všechny výsledky hledání */
            var item = vysledky.shift();
        }

        var center = SMap.Coords.fromWGS84(item.coords.x, item.coords.y);

        var m = new SMap(JAK.gel("shaim_balikovna_map"), center, 15);
        m.addDefaultLayer(SMap.DEF_BASE).enable();
        m.addDefaultControls();

        var layer = new SMap.Layer.Marker();
        m.addLayer(layer);
        layer.enable();

        var card = new SMap.Card();
        card.getHeader().innerHTML = "<strong>" + naz_prov + "</strong>";
        card.getBody().innerHTML = adresa;

        var options = {};
        var shaim_balikovna_marker = new SMap.Marker(center, "shaim_balikovna_marker", options);
        shaim_balikovna_marker.decorate(SMap.Marker.Feature.Card, card);
        layer.addMarker(shaim_balikovna_marker);

    }
});