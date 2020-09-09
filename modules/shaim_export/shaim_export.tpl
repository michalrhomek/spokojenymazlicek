<div id="product-shaim-export" class="panel product-tab">
    <h3 class="tab"><i
                class="icon-info"></i> {l s='XML export - nastavení pro specifický produkt (je nadřazené defaultnímu nastavení modulu)'}
    </h3>


    <div class="form-group">
        <div class="col-lg-1"><span
                    class="pull-right">{* {include file="controllers/products/multishop/checkbox.tpl" field="shaim_export_name" type="default" multilang="true"} *}</span>
        </div>
        <label class="control-label col-lg-2" for="shaim_export_name">

            {l s='Alternativní název pro export do XML'}

        </label>
        <div class="col-lg-5">
            <input maxlength="128" type="text" id="shaim_export_name" name="shaim_export_name"
                   value="{$product->shaim_export_name}">
        </div>
    </div>


    <div class="form-group">
        <div class="col-lg-1"><span
                    class="pull-right">{* {include file="controllers/products/multishop/checkbox.tpl" field="shaim_export_gifts" type="default" multilang="true"} *}</span>
        </div>
        <label class="control-label col-lg-2" for="shaim_export_gifts">

            {l s='Dárek k produktu (více dárků oddělujte čárkou)'}

        </label>
        <div class="col-lg-5">
            <input maxlength="250" type="text" id="shaim_export_gifts" name="shaim_export_gifts"
                   value="{$product->shaim_export_gifts}">
        </div>
    </div>


    <div class="form-group">
        <div class="col-lg-1"><span
                    class="pull-right">{* {include file="controllers/products/multishop/checkbox.tpl" field="shaim_export_active" type="radio" onclick=""} *}</span>
        </div>
        <label class="control-label col-lg-2">
            {l s='Propisovat produkt do XML feedů'}
        </label>
        <div class="col-lg-9">
            <span class="switch prestashop-switch fixed-width-lg">
                <input type="radio" name="shaim_export_active" id="shaim_export_active_on" value="1"
                       {if $product->shaim_export_active}checked="checked" {/if} />
                <label for="shaim_export_active_on" class="radioCheck">
                    {l s='Ano'}
                </label>
                <input type="radio" name="shaim_export_active" id="shaim_export_active_off" value="0"
                       {if !$product->shaim_export_active}checked="checked"{/if} />
                <label for="shaim_export_active_off" class="radioCheck">
                    {l s='Ne'}
                </label>
                <a class="slide-button btn"></a>
            </span>
        </div>
    </div>


    {if $show_save}
        <div class="panel-footer">
            <button type="submit" name="submitAddproduct" class="btn btn-default pull-right"><i
                        class="process-icon-save"></i> {l s='Uložit'}</button>
            {if $hide_save_17}
                <button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right"><i
                            class="process-icon-save"></i> {l s='Uložit a zůstat'}</button>
            {/if}
        </div>
    {/if}

</div>