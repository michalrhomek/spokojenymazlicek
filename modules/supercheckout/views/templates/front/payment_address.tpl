<table id="payment_address_table" class="supercheckout-form">
    {assign var='display_row' value=''}
    {foreach from=$settings['payment_address'] key='p_address_key' item='p_address_field'}
        {$display_row = ''}
        {if $settings['payment_address'][$p_address_key][$user_type]['display'] eq 1 || (isset($settings['payment_address'][$p_address_key]['conditional']) && $settings['payment_address'][$p_address_key]['conditional'] eq 1)}
            {if $p_address_key eq 'dni' && !$need_dni}
                {$display_row = 'display:none;'}
            {/if}
            {if $p_address_key eq 'vat_number' && !$need_vat}
                {$display_row = 'display:none;'}
            {/if}
            {if ($p_address_key eq 'postcode' || $p_address_key eq 'id_country' || $p_address_key eq 'id_state' || $p_address_key eq 'alias') && !$settings['payment_address'][$p_address_key][$user_type]['require'] && !$settings['payment_address'][$p_address_key][$user_type]['display']}
                {$display_row = 'display:none;'}
            {/if}
            <tr class="sort_data"  data-percentage="{$settings['payment_address'][$p_address_key]['sort_order']|intval}" style="{$display_row|escape:'htmlall':'UTF-8'}" >
                <td>{l s={$settings['payment_address'][$p_address_key]['title']|escape:'htmlall':'UTF-8'} mod='supercheckout'}:<span style="display:{if $settings['payment_address'][$p_address_key][$user_type]['require'] eq 1}inline{else}none{/if};" class="supercheckout-required">*</span>
                    {if $p_address_key eq 'id_country' || $p_address_key eq 'id_state'}
                        <select name="payment_address[{$p_address_key|escape:'htmlall':'UTF-8'}]" class="supercheckout-large-field">
                            {if $p_address_key eq 'id_country'}
                                {foreach from=$countries item='country'}
                                    <option value="{$country['id_country']|intval}" {if $country['id_country'] == $default_country} selected="selected"{/if}>{$country['name']|escape:'html':'UTF-8'}</option>                                        
                                {/foreach}
                            {else}
                                <option value="0">{l s='Select State' mod='supercheckout'}</option>
                            {/if}                            
                        </select>
                    {else if $p_address_key eq 'dob'}
                        <div class="supercheckout_dob_box supercheckout-large-field">
                            <select name="payment_address[dob_days]">
                              <option value="">--</option>
                              {foreach from=$days item='day'}
                                  <option value="{$day|intval}">{$day|intval}</option>
                              {/foreach}
                            </select>
                            <select name="payment_address[dob_months]">
                              <option value="">--</option>
                              {foreach from=$months item='month'}
                                  <option value="{$month|escape:'htmlall':'UTF-8'}">{$month|escape:'htmlall':'UTF-8'}</option>
                              {/foreach}
                            </select>
                            <select name="payment_address[dob_years]">
                              <option value="">--</option>
                              {foreach from=$years item='year'}
                                  <option value="{$year|escape:'htmlall':'UTF-8'}">{$year|escape:'htmlall':'UTF-8'}</option>
                              {/foreach}
                            </select>
                        </div>
                    {else if  $p_address_key eq 'other'}
                        <textarea name="payment_address[{$p_address_key|escape:'htmlall':'UTF-8'}]" value="" class="supercheckout-large-field" style="width: 96%;"></textarea>
                    {else}
                        <input type="text" name="payment_address[{$p_address_key|escape:'htmlall':'UTF-8'}]" value="" class="supercheckout-large-field" />
                    {/if}
                    
                </td>
            </tr>
        {/if}
    {/foreach}                            
</table>
{*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer tohttp://www.prestashop.com for more information.
* We offer the best and most useful modules PrestaShop and modifications for your online store.
*
* @category  PrestaShop Module
* @author    knowband.com <support@knowband.com>
* @copyright 2015 Knowband
*}