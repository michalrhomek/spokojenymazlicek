<div id="shaim_balikovna_order_conf" class="box" data-title-id="{$shaim_balikovna_psc}">
    <div class="clearfix"></div>

    <div class="odberne_misto col-xs-12 col-md-8">
        <span class="nazev">{l s='Odběrné místo' mod='shaim_balikovna'}: </span><span
                class="hodnota bold">{$shaim_balikovna_naz_prov}</span>
    </div>
    <div class="pondeli oteviracka col-md-4 hidden-xs">
        <span class="nazev">{l s='Pondělí' mod='shaim_balikovna'}: </span><span
                class="hodnota">{$shaim_balikovna_pondeli}</span>
    </div>

    <div class="adresa col-xs-12 col-md-8">
        <span class="nazev">{l s='Adresa' mod='shaim_balikovna'}: </span><span
                class="hodnota">{$shaim_balikovna_adresa}</span>
    </div>
    <div class="utery oteviracka col-md-4 hidden-xs">
        <span class="nazev">{l s='Úterý' mod='shaim_balikovna'}: </span><span
                class="hodnota">{$shaim_balikovna_utery}</span>
    </div>

    <div class="prazdno col-xs-12 col-md-8">
        <span class="nazev">&nbsp;</span><span
                class="hodnota">&nbsp;</span>
    </div>
    <div class="streda oteviracka col-md-4 hidden-xs">
        <span class="nazev">{l s='Středa' mod='shaim_balikovna'}: </span><span
                class="hodnota">{$shaim_balikovna_streda}</span>
    </div>

    <div class="prazdno col-xs-12 col-md-8">
        <span class="nazev">&nbsp;</span><span
                class="hodnota">&nbsp;</span>
    </div>
    <div class="ctvrtek oteviracka col-md-4 hidden-xs">
        <span class="nazev">{l s='Čtvrtek' mod='shaim_balikovna'}: </span><span
                class="hodnota">{$shaim_balikovna_ctvrtek}</span>
    </div>

    <div class="prazdno col-xs-12 col-md-8">
        <span class="nazev">&nbsp;</span><span
                class="hodnota">&nbsp;</span>
    </div>
    <div class="patek oteviracka col-md-4 hidden-xs">
        <span class="nazev">{l s='Pátek' mod='shaim_balikovna'}: </span><span
                class="hodnota">{$shaim_balikovna_patek}</span>
    </div>

    <div class="prazdno col-xs-12 col-md-8">
        <span class="nazev">&nbsp;</span><span
                class="hodnota">&nbsp;</span>
    </div>
    <div class="sobota oteviracka col-md-4 hidden-xs">
        <span class="nazev">{l s='Sobota' mod='shaim_balikovna'}: </span><span
                class="hodnota">{$shaim_balikovna_sobota}</span>
    </div>

    <div class="prazdno col-xs-12 col-md-8">
        <span class="nazev">&nbsp;</span><span
                class="hodnota">&nbsp;</span>
    </div>
    <div class="nedele oteviracka col-md-4 hidden-xs">
        <span class="nazev">{l s='Neděle' mod='shaim_balikovna'}: </span><span
                class="hodnota">{$shaim_balikovna_nedele}</span>
    </div>

    {* oteviracka mobil *}
    <div id="oteviraci_doba_mobil" class="col-xs-12 hidden-sm hidden-md hidden-lg">
        <span class="nazev">{l s='Otevírací doba' mod='shaim_balikovna'}: </span>
    </div>

    <div class="pondeli_mobil oteviracka_mobil col-xs-12 hidden-sm hidden-md hidden-lg">
        <span class="nazev">{l s='Pondělí' mod='shaim_balikovna'}: </span><span
                class="hodnota">{$shaim_balikovna_pondeli}</span>
    </div>

    <div class="utery_mobil oteviracka_mobil col-xs-12 hidden-sm hidden-md hidden-lg">
        <span class="nazev">{l s='Úterý' mod='shaim_balikovna'}: </span><span
                class="hodnota">{$shaim_balikovna_utery}</span>
    </div>

    <div class="streda_mobil oteviracka_mobil col-xs-12 hidden-sm hidden-md hidden-lg">
        <span class="nazev">{l s='Středa' mod='shaim_balikovna'}: </span><span
                class="hodnota">{$shaim_balikovna_streda}</span>
    </div>

    <div class="ctvrtek_mobil oteviracka_mobil col-xs-12 hidden-sm hidden-md hidden-lg">
        <span class="nazev">{l s='Čtvrtek' mod='shaim_balikovna'}: </span><span
                class="hodnota">{$shaim_balikovna_ctvrtek}</span>
    </div>

    <div class="patek_mobil oteviracka_mobil col-xs-12 hidden-sm hidden-md hidden-lg">
        <span class="nazev">{l s='Pátek' mod='shaim_balikovna'}: </span><span
                class="hodnota">{$shaim_balikovna_patek}</span>
    </div>

    <div class="sobota_mobil oteviracka_mobil col-xs-12 hidden-sm hidden-md hidden-lg">
        <span class="nazev">{l s='Sobota' mod='shaim_balikovna'}: </span><span
                class="hodnota">{$shaim_balikovna_sobota}</span>
    </div>

    <div class="nedele_mobil oteviracka_mobil col-xs-12 hidden-sm hidden-md hidden-lg">
        <span class="nazev">{l s='Neděle' mod='shaim_balikovna'}: </span><span
                class="hodnota">{$shaim_balikovna_nedele}</span>
    </div>


    <div class="clearfix"></div>


    {* https://api.mapy.cz/view?page=simple *}
    {if $shaim_balikovna_mapa_zobrazit}
        <script type="text/javascript" src="https://api.mapy.cz/loader.js"></script>
        <div id="shaim_balikovna_map" class="col-xs-12 col-md-12"></div>
        <script type="text/javascript">
            Loader.load();
            var naz_prov = '{$shaim_balikovna_naz_prov}';
            var adresa = '{$shaim_balikovna_adresa}';
        </script>
        <div class="clearfix"></div>
    {/if}

</div>