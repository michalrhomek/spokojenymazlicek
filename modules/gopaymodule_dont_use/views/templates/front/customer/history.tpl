
{** ########################################################################### * 
 *                                                                             * 
 *                      Presta Module Shop | Copyright 2018                    * 
 *                           www.prestamoduleshop.com                          * 
 *                                                                             * 
 *             Please do not change this text, remove the link,                * 
 *          or remove all or any part of the creator copyright notice          * 
 *                                                                             * 
 *    Please also note that although you are allowed to make modifications     * 
 *     for your own personal use, you may not distribute the original or       * 
 *                 the modified code without permission.                       * 
 *                                                                             * 
 *                    SELLING AND REDISTRIBUTION IS FORBIDDEN!                 * 
 *             Download is allowed only from www.prestamoduleshop.com          * 
 *                                                                             * 
 *       This software is provided as is, without warranty of any kind.        * 
 *           The author shall not be liable for damages of any kind.           * 
 *               Use of this software indicates that you agree.                * 
 *                                                                             * 
 *                                    ***                                      * 
 *                                                                             * 
 *              Prosím, neměňte tento text, nemažte odkazy,                    * 
 *      neodstraňujte části a nebo celé oznámení těchto autorských práv        * 
 *                                                                             * 
 *     Prosím vezměte také na vědomí, že i když máte možnost provádět změny    * 
 *        pro vlastní osobní potřebu, nesmíte distribuovat původní nebo        * 
 *                        upravený kód bez povolení.                           * 
 *                                                                             * 
 *                   PRODEJ A DISTRIBUCE JE ZAKÁZÁNA!                          * 
 *          Stažení je povoleno pouze z www.prestamoduleshop.com               * 
 *                                                                             * 
 *   Tento software je poskytován tak, jak je, bez záruky jakéhokoli druhu.    * 
 *          Autor nenese odpovědnost za škody jakéhokoliv druhu.               * 
 *                  Používáním tohoto softwaru znamená,                        * 
 *           že souhlasíte s výše uvedenými autorskými právy.                  * 
 *                                                                             * 
 * ########################################################################### **}{*  verze 1.7   *}


{extends file='customer/page.tpl'}

{block name='page_title'}
  {l s='Order history' d='Shop.Theme.CustomerAccount'}
{/block}

{block name='page_content'}
  <h6>{l s='Here are the orders you\'ve placed since your account was created.' d='Shop.Theme.CustomerAccount'}</h6>

  {if $orders}
    <table class="table table-striped table-bordered table-labeled hidden-sm-down">
      <thead class="thead-default">
        <tr>
          <th>{l s='Order reference' d='Shop.Theme.Checkout'}</th>
          <th>{l s='Date' d='Shop.Theme.Checkout'}</th>
          <th>{l s='Total price' d='Shop.Theme.Checkout'}</th>
          <th class="hidden-md-down">{l s='Payment' d='Shop.Theme.Checkout'}</th>
          <th class="hidden-md-down">{l s='Status' d='Shop.Theme.Checkout'}</th>
          <th>{l s='Invoice' d='Shop.Theme.Checkout'}</th>
          <th>&nbsp;</th>
        </tr>
      </thead>
      <tbody>
        {foreach from=$orders item=order}
          <tr>
            <th scope="row">{$order.details.reference}</th>
            <td>{$order.details.order_date}</td>
            <td class="text-xs-right">{$order.totals.total.value}</td>
            <td class="hidden-md-down">{$order.details.payment}</td>
            <td>
              <span
                class="label label-pill {$order.history.current.contrast}"
                style="background-color:{$order.history.current.color}"
              >
                {$order.history.current.ostate_name}
              </span>
					{if $order.history.current.module_name == $_PMS_MODULE->name
						&& $_PMS_MODULE->functions->isRegistered()
						&& ($order.history.current.id_order_state == $_PMS_PAYMENT_NEW_
							|| $order.history.current.id_order_state == $_PMS_PAYMENT_CHOSEN_
							|| $order.history.current.id_order_state == $_PMS_PAYMENT_TIMEOUT_
							|| $order.history.current.id_order_state == $_PS_OS_ERROR_
							|| $order.history.current.id_order_state == $_PMS_PAYMENT_CANCELED_)
					}
{assign var="repeat_url" value=$link->getModuleLink('pms_gopay_extra', 'repeatPayment', [], true)|escape:'html':'UTF-8'}
<form action="{$repeat_url}" method="post" id="form-{$order.details.id}" class="text-sm-center" style="margin-top: 10px;">
	<input type="hidden" name="orderId" value="{$order.details.id}">
	<input type="hidden" name="paymentChannel" value="ACCOUNT">
	<button type="submit" id="payment-repeat"
			class="btn-success  payment-overlay"
			{if $INLINE_MODE}
				onClick="inlineFunction('{$repeat_url}', $('#form-{$order.details.id}').serialize()); return false;"
			{/if}
	>
		{$_PMS_MODULE->payAgain}
	</button>
</form>
					{/if}
					{assign var='id_session' value=$RECURRENT->getReccurenceStarted($order.details.id)}
					{if $id_session}
						<div class="conf" id="conf_{$order.details.id}" style="display:none">{l s='Recurring payments were terminated' mod='pms_gopay_extra'}</div>
						<div class="error" id="error_{$order.details.id}" style="display:none"></div>
						<p style="margin-top:10px;">
							<a  href="#"
								title="{l s='Cancel recurrent' mod='pms_gopay_extra'}"
								class="btn btn-default button button-small"
								id="cancel_recurr_{$order.details.id}"
								onclick="if (confirm('{l s='Update selected items?' mod='pms_gopay_extra'}'))cancelRecurrent({$order.details.id}, {$id_session}); return false;"
								
							>
								<span>{l s='Cancel recurrent' mod='pms_gopay_extra'}</span>
							</a>
						</p>
					{/if}
            </td>
            <td class="text-xs-center hidden-md-down">
              {if $order.details.invoice_url}
                <a href="{$order.details.invoice_url}"><i class="material-icons">&#xE415;</i></a>
              {else}
                -
              {/if}
            </td>
            <td class="text-xs-center order-actions">
              <a href="{$order.details.details_url}" data-link-action="view-order-details">
                {l s='Details' d='Shop.Theme.CustomerAccount'}
              </a>
              {if $order.details.reorder_url}
                <a href="{$order.details.reorder_url}">{l s='Reorder' d='Shop.Theme.Actions'}</a>
              {/if}
            </td>
          </tr>
        {/foreach}
      </tbody>
    </table>

    <div class="orders hidden-md-up">
      {foreach from=$orders item=order}
        <div class="order">
          <div class="row">
            <div class="col-xs-10">
              <a href="{$order.details.details_url}"><h3>{$order.details.reference}</h3></a>
              <div class="date">{$order.details.order_date}</div>
              <div class="total">{$order.totals.total.value}</div>
              <div class="status">
                <span
                  class="label label-pill {$order.history.current.contrast}"
                  style="background-color:{$order.history.current.color}"
                >
                  {$order.history.current.ostate_name}
                </span>
              </div>
            </div>
            <div class="col-xs-2 text-xs-right">
                <div>
                  <a href="{$order.details.details_url}" data-link-action="view-order-details" title="{l s='Details' d='Shop.Theme.CustomerAccount'}">
                    <i class="material-icons">&#xE8B6;</i>
                  </a>
                </div>
                {if $order.details.reorder_url}
                  <div>
                    <a href="{$order.details.reorder_url}" title="{l s='Reorder' d='Shop.Theme.Actions'}">
                      <i class="material-icons">&#xE863;</i>
                    </a>
                  </div>
                {/if}
            </div>
          </div>
        </div>
      {/foreach}
    </div>

{if $INLINE_MODE}
<script src="https://gate.gopay.cz/gp-gw/js/embed.js"></script>
{/if}

  {/if}
{/block}
