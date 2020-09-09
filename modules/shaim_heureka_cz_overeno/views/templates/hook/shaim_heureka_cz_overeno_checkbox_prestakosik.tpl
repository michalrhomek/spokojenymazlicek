      <div class="pk__shaim_heureka_checkboxment">
                <div class="pk__form-group--checkbox{if isset($errors['shaim_heureka_checkbox'])} pk__form-group--error{/if}">
                    <input name="shaim_heureka_checkbox" id="shaim_heureka_checkbox" type="checkbox" {if (isset($smarty.post.shaim_heureka_checkbox) && $smarty.post.shaim_heureka_checkbox == 1) || ($shaim_heureka_cz_overeno_checkbox == 1)} checked="checked"{/if}>
                    <label for="shaim_heureka_checkbox">
                        {l s='Nesouhlasím se zasláním dotazníku spokojenosti v rámci programu Ověřeno zákazníky, který pomáhá zlepšovat vaše služby.' mod='shaim_aio'}

                    </label>
                </div>
                {if isset($errors['shaim_heureka_checkbox'])}
                    <div class="pk__error">
                        {$errors['shaim_heureka_checkbox']} 
                    </div>
                {/if}
            </div>

<input type="hidden" id="shaim_heureka_cz_overeno_id_cart" value="{$shaim_heureka_cz_overeno_id_cart|intval}">
<input type="hidden" id="shaim_heureka_cz_overeno_ajax_url" value="{$shaim_heureka_cz_overeno_ajax_url}">
<input type="hidden" id="shaim_heureka_cz_overeno_ps_version" value="{$shaim_heureka_cz_overeno_ps_version}">

