<table class="supercheckout-form" id="shipping_address_table">
    {assign var='display_row' value=''}
    {foreach from=$settings['shipping_address'] key='p_address_key' item='p_address_field'}
        {$display_row = ''}
        {if $settings['shipping_address'][$p_address_key][$user_type]['display'] eq 1 || (isset($settings['shipping_address'][$p_address_key]['conditional']) && $settings['shipping_address'][$p_address_key]['conditional'] eq 1)}
            {if $p_address_key eq 'dni' && !$need_dni}
                {$display_row = 'display:none;'}
            {/if}
            {if $p_address_key eq 'vat_number' && !$need_vat}
                {$display_row = 'display:none;'}
            {/if}
            {if ($p_address_key eq 'postcode' || $p_address_key eq 'id_country' || $p_address_key eq 'id_state' || $p_address_key eq 'alias') && !$settings['shipping_address'][$p_address_key][$user_type]['require'] && !$settings['shipping_address'][$p_address_key][$user_type]['display']}
                {$display_row = 'display:none;'}
            {/if}
            <tr class="sort_data"  data-percentage="{$settings['shipping_address'][$p_address_key]['sort_order']|intval}" style="{$display_row|escape:'htmlall':'UTF-8'}" >
                <td>{l s={$settings['shipping_address'][$p_address_key]['title']|escape:'htmlall':'UTF-8'} mod='supercheckout'}:<span style="display:{if $settings['shipping_address'][$p_address_key][$user_type]['require'] eq 1}inline{else}none{/if};" class="supercheckout-required">*</span>
                    {if $p_address_key eq 'id_country' || $p_address_key eq 'id_state'}
                        <select name="shipping_address[{$p_address_key|escape:'htmlall':'UTF-8'}]" class="supercheckout-large-field">
                            {if $p_address_key eq 'id_country'}
                                {foreach from=$countries item='country'}
                                    <option value="{$country['id_country']|intval}" {if $country['id_country'] == $default_country} selected="selected"{/if}>{$country['name']|escape:'html':'UTF-8'}</option>                                        
                                {/foreach}
                            {else}
                                <option value="0">{l s='Select State' mod='supercheckout'}</option>
                            {/if}                            
                        </select>
                    {else if  $p_address_key eq 'other'}
                        <textarea name="shipping_address[{$p_address_key|escape:'htmlall':'UTF-8'}]" value="" class="supercheckout-large-field" style="width: 96%;"></textarea>
                    {else}
                        <input type="text" name="shipping_address[{$p_address_key|escape:'htmlall':'UTF-8'}]" value="" class="supercheckout-large-field" />
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