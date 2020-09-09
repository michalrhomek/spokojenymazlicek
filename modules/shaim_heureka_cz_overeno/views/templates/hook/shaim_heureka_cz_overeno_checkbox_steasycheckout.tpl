<label class="js-terms" for="shaim_heureka_checkbox">
    <input id="shaim_heureka_checkbox" name="shaim_heureka_checkbox" type="checkbox"
           value="1"{if (isset($smarty.post.shaim_heureka_checkbox) && $smarty.post.shaim_heureka_checkbox == 1) || ($shaim_heureka_cz_overeno_checkbox == 1)} checked="checked"{/if}
           class="ps-shown-by-js">
    {l s='Nesouhlasím se zasláním dotazníku spokojenosti v rámci programu Ověřeno zákazníky, který pomáhá zlepšovat vaše služby.' mod='shaim_heureka_cz_overeno'}
</label>

<input type="hidden" id="shaim_heureka_cz_overeno_id_cart" value="{$shaim_heureka_cz_overeno_id_cart|intval}">
<input type="hidden" id="shaim_heureka_cz_overeno_ajax_url" value="{$shaim_heureka_cz_overeno_ajax_url}">
<input type="hidden" id="shaim_heureka_cz_overeno_ps_version" value="{$shaim_heureka_cz_overeno_ps_version}">