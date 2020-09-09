{*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{capture name=path}{l s='Order confirmation'}{/capture}

<h1 class="page-heading">{l s='Order confirmation'}</h1>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

{include file="$tpl_dir./errors.tpl"}

{$HOOK_ORDER_CONFIRMATION}

<div class="c-orderCustomText">
	<h2>Vaši objednávku jsme vyčmuchali a brzy ji připravíme k odeslání</h2>
	<p>
		Jsme rádi, že jste si udělali chvíli a zastavili se nakoupit v našem e-shopu. Věříme, že Váš mazlíček to jistě ocení. 
	</p>
	<p>
    Než se k Vaší objednávce dostaneme, abychom ji zabalili a odeslali, podívejte se zatím na další zajímavé produkty z naší široké nabídky kosmetiky a doplňků pro spokojené mazlíčky.  
    </p>
    <p>
    	<a href="https://spokojeny-mazlicek.cz/34-uprava-srsti-pro-psy"><img src="https://spokojeny-mazlicek.cz/modules/themeconfigurator/img/6fc70a89256ea9a215614ae7692879c653a2c7a1_banner2.png" title="Úprava srsti"></a>

    	<a href="https://spokojeny-mazlicek.cz/15-kosmetika-pro-psy"><img src="https://spokojeny-mazlicek.cz/modules/themeconfigurator/img/659df61a063c28548ad25b8ccd2cfdd3f7c057dd_banner232.png" title="Profesionální kosmetika"></a>

    	<a href="https://spokojeny-mazlicek.cz/32-doplnky"><img src="https://spokojeny-mazlicek.cz/modules/themeconfigurator/img/020b780abd51a46366725c825da3a7e41dd31b6f_banner22.png" title="Postroje a obojky"></a>
    </p>



</div>


{$HOOK_PAYMENT_RETURN}
{if $is_guest}
	{*}<p>{l s='Your order ID is:'} <span class="bold">{$id_order_formatted}</span> . {l s='Your order ID has been sent via email.'}</p> {*}
    <p class="cart_navigation exclusive">
	<a class="button-exclusive btn btn-default" href="{$link->getPageLink('guest-tracking', true, NULL, "id_order={$reference_order|urlencode}&email={$email|urlencode}")|escape:'html':'UTF-8'}" title="{l s='Follow my order'}"><i class="icon-chevron-left"></i>{l s='Follow my order'}</a>
    </p>
{else}
<p class="cart_navigation exclusive">
	<a class="button-exclusive btn btn-default" href="{$link->getPageLink('history', true)|escape:'html':'UTF-8'}" title="{l s='Go to your order history page'}"><i class="icon-chevron-left"></i>{l s='View your order history'}</a>
</p>
{/if}
