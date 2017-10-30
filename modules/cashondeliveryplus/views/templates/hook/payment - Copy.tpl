{*
* PrestaHost.cz / PrestaHost.eu
*
*
*  @author prestahost.eu <info@prestahost.cz>
*  @copyright  2014  PrestaHost.eu, Vaclav Mach
*  @license    http://prestahost.eu/prestashop-modules/en/content/3-terms-and-conditions-of-use
*}
<div class="row">
    <div class="col-xs-12 col-md-6">
        <p class="payment_module">
            <a  class="cash" href="{$link->getModuleLink('cashondeliveryplus', 'validation', [], true)}" title="{l s='Pay with cash on delivery (COD)' mod='cashondeliveryplus'}">
            <img src="{$this_path}cashondelivery.gif" alt="{l s='Pay with cash on delivery (COD)' mod='cashondeliveryplus'}" style="float:left;" />
            <br />{l s='Pay with cash on delivery (COD)' mod='cashondeliveryplus'}
            <br />{l s='You pay for the merchandise upon delivery' mod='cashondeliveryplus'}
            <br />
            {if isset($codfee) && $codfee}
            {l s='The COD fee is' mod='cashondeliveryplus'}  {$codfee}
            {else}
            {l s='The COD fee is' mod='cashondeliveryplus'}
            {/if}
            <br style="clear:both;" />
            </a>
        </p>
    </div>
</div>