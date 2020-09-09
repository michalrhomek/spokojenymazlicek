<div class="checkbox shaim_heureka_checkbox">
    <span class="custom-checkbox">
    <input type="checkbox" name="shaim_heureka_checkbox"
           id="shaim_heureka_checkbox"
           value="1"{if (isset($smarty.post.shaim_heureka_checkbox) && $smarty.post.shaim_heureka_checkbox == 1) || ($shaim_heureka_cz_overeno_checkbox == 1)} checked="checked"{/if} />
        </span>
    <div class="label">
        <label for="shaim_heureka_checkbox">{l s='Nesouhlasím se zasláním dotazníku spokojenosti v rámci programu Ověřeno zákazníky, který pomáhá zlepšovat vaše služby.' mod='shaim_heureka_cz_overeno'}</label>
    </div>
</div>

<input type="hidden" id="shaim_heureka_cz_overeno_id_cart" value="{$cart->id|intval}">
<input type="hidden" id="shaim_heureka_cz_overeno_ajax_url" value="{$shaim_heureka_cz_overeno_ajax_url}">
<input type="hidden" id="shaim_heureka_cz_overeno_ps_version" value="{$shaim_heureka_cz_overeno_ps_version}">

