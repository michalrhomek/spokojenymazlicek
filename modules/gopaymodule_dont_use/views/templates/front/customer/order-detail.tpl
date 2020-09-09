
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
  {l s='Order details' d='Shop.Theme.CustomerAccount'}
{/block}

{block name='page_content'}

{assign var=list_recurrents value=Pms_GoPay_Extra_Recurrent::listRecurrents($order.details.id)}
{assign var=id_session value=Pms_GoPay_Extra_Recurrent::getReccurenceStarted($order.details.id)}
{assign var=bill value=Pms_GoPay_Extra_Bills::getBill($order.details.id)}
  {block name='order_infos'}
    <div id="order-infos">
      <div class="box">
          <div class="row">
            <div class="col-xs-{if $order.details.reorder_url}9{else}12{/if}">
              <strong>
                {l
                  s='Order Reference %reference% - placed on %date%'
                  d='Shop.Theme.CustomerAccount'
                  sprintf=['%reference%' => $order.details.reference, '%date%' => $order.details.order_date]
                }
              </strong>
            </div>
            {if $order.details.reorder_url}
              <div class="col-xs-3 text-xs-right">
                <a href="{$order.details.reorder_url}" class="button-primary">{l s='Reorder' d='Shop.Theme.Actions'}</a>
              </div>
            {/if}
            <div class="clearfix"></div>
          </div>
      </div>

      <div class="box">
          <ul>
            <li><strong>{l s='Carrier' d='Shop.Theme.Checkout'}</strong> {$order.carrier.name}</li>
            <li><strong>{l s='Payment method' d='Shop.Theme.Checkout'}</strong> {$order.details.payment}</li>

            {if $order.details.invoice_url}
              <li>
                <a href="{$order.details.invoice_url}">
                  {l s='Download your invoice as a PDF file.' d='Shop.Theme.CustomerAccount'}
                </a>
              </li>
            {/if}

            {if $order.details.recyclable}
              <li>
                {l s='You have given permission to receive your order in recycled packaging.' d='Shop.Theme.CustomerAccount'}
              </li>
            {/if}

            {if $order.details.gift_message}
              <li>{l s='You have requested gift wrapping for this order.' d='Shop.Theme.CustomerAccount'}</li>
              <li>{l s='Message' d='Shop.Theme.CustomerAccount'} {$order.details.gift_message nofilter}</li>
            {/if}
          </ul>
      </div>
    </div>
  {/block}

  {block name='order_history'}
    <section id="order-history" class="box">
      <h3>{l s='Follow your order\'s status step-by-step' d='Shop.Theme.CustomerAccount'}</h3>
      <table class="table table-striped table-bordered table-labeled hidden-xs-down">
        <thead class="thead-default">
          <tr>
            <th>{l s='Date' d='Shop.Theme'}</th>
            <th>{l s='Status' d='Shop.Theme'}</th>
          </tr>
        </thead>
        <tbody>
		{assign var="count_history" value=count($order.history)}
		{assign var="count" value=1}
          {foreach from=$order.history item=state}
            <tr>
              <td>{$state.history_date}</td>
              <td>
                <span class="label label-pill {$state.contrast}" style="background-color:{$state.color}">
                  {$state.ostate_name}
                </span>
				{if $order.history.current.module_name == $_PMS_MODULE->name
					&& $count_history == $count
					&& $_PMS_MODULE->functions->isRegistered()
					&& ($order.history.current.id_order_state == $_PMS_PAYMENT_NEW_
						|| $order.history.current.id_order_state == $_PMS_PAYMENT_CHOSEN_
						|| $order.history.current.id_order_state == $_PMS_PAYMENT_TIMEOUT_
						|| $order.history.current.id_order_state == $_PS_OS_ERROR_
						|| $order.history.current.id_order_state == $_PMS_PAYMENT_CANCELED_)
				}
					{assign var="repeat_url" value=$link->getModuleLink($_PMS_MODULE->name, 'repeatPayment', [], true)|escape:'html':'UTF-8'}
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
				{assign var="count" value=$count+1}
              </td>
            </tr>
          {/foreach}
        </tbody>
      </table>
      <div class="hidden-sm-up history-lines">
        {foreach from=$order.history item=state}
          <div class="history-line">
            <div class="date">{$state.history_date}</div>
            <div class="state">
              <span class="label label-pill {$state.contrast}" style="background-color:{$state.color}">
                {$state.ostate_name}
              </span>
            </div>
          </div>
        {/foreach}
      </div>
    </section>
  {/block}
{*  moje uprava  *}

{if is_array($list_recurrents) && $list_recurrents|@count gt 0}
	<div class="box">
		<h1 class="page-heading">{l s='Instructions for recurring payment' mod='pms_gopay_extra'}</h1>
		<table class="detail_step_by_step table table-bordered">
			<thead>
				<tr>
					<th class="first_item">{l s='Payment date' mod='pms_gopay_extra'}</th>
					<th class="first_item">{l s='Payment ID' mod='pms_gopay_extra'}</th> 
					<th class="first_item">{l s='Payment result' mod='pms_gopay_extra'}</th>
				</tr>
			</thead>
			<tbody>
			{foreach from=$list_recurrents item=list name="listRecurrents"}
				<tr class="{if $smarty.foreach.listRecurrents.first}first_item{elseif $smarty.foreach.listRecurrents.last}last_item{/if} {if $smarty.foreach.listRecurrents.index % 2}alternate_item{else}item{/if}">
					<td class="step-by-step-date">{Tools::displayDate($list.date_add, $smarty.const.null,true)}</td>
					<td>{$list.id_session}</td>
					<td>{$list.recurrence_state}</td> 
				</tr>
			{/foreach}
			{if $id_session}
				<tr>
					<td colspan="3" style="text-align:right">
						<div class="conf" id="conf_{$order->id}" style="display:none">{l s='Recurring payments were terminated' mod='pms_gopay_extra'}</div>
						<div class="error" id="error_{$order->id}" style="display:none"></div>
						<p style="margin-top:10px;">
							<a  href="#"
								title="{l s='Cancel recurrent' mod='pms_gopay_extra'}"
								class="btn btn-default button button-small"
								id="cancel_recurr_{$order->id}"
								onclick="if (confirm('{l s='Update selected items?' mod='pms_gopay_extra'}'))cancelRecurrent({$order->id|intval}, {$id_session}); return false;"
								
							>
								<span>{l s='Cancel recurrent' mod='pms_gopay_extra'}</span>
							</a>
						</p>
					</td>
				</tr>
			{/if}
			</tbody>
		</table>
	</div>
{/if}

{if isset($bill) && $bill}
	<div class="box">
			<div class="panel-heading">
				<img src="{$base_dir}modules/{$_PMS_MODULE->name}/logo.png" alt=""/> {l s='Bill for order' mod='pms_gopay_extra'}
			</div>
			<table class="detail_step_by_step table table-bordered">
				<tr>
					<td class="first_item"><b>{l s='fik:' mod='pms_gopay_extra'}</b></td>
					<td class="last_item">{$bill['fik']}</td>
				</tr>
				<tr>
					<td class="first_item"><b>{l s='bkp:' mod='pms_gopay_extra'}</b></td>
					<td class="last_item">{$bill['bkp']}</td>
				</tr>
				<tr>
					<td class="first_item"><b>{l s='pkp:' mod='pms_gopay_extra'}</b></td>
					<td class="last_item" style="word-break: break-all"><span style="max-width: 90%">{$bill['pkp']}</span></td>
				</tr>
			</table>
	</div>
{/if}
{*  konec moje uprava  *}

  {if $order.follow_up}
    <div class="box">
      <p>{l s='Click the following link to track the delivery of your order' d='Shop.Theme.CustomerAccount'}</p>
      <a href="{$order.follow_up}">{$order.follow_up}</a>
    </div>
  {/if}

  {block name='addresses'}
    <div class="addresses">
      {if $order.addresses.delivery}
        <div class="col-lg-6 col-md-6 col-sm-6">
          <article id="delivery-address" class="box">
            <h4>{l s='Delivery address %alias%' d='Shop.Theme.Checkout' sprintf=['%alias%' => $order.addresses.delivery.alias]}</h4>
            <address>{$order.addresses.delivery.formatted nofilter}</address>
          </article>
        </div>
      {/if}

      <div class="col-lg-6 col-md-6 col-sm-6">
        <article id="invoice-address" class="box">
          <h4>{l s='Invoice address %alias%' d='Shop.Theme.Checkout' sprintf=['%alias%' => $order.addresses.invoice.alias]}</h4>
          <address>{$order.addresses.invoice.formatted nofilter}</address>
        </article>
      </div>
      <div class="clearfix"></div>
    </div>
  {/block}

  {$HOOK_DISPLAYORDERDETAIL nofilter}

  {block name='order_detail'}
    {if $order.details.is_returnable}
      {include file='customer/_partials/order-detail-return.tpl'}
    {else}
      {include file='customer/_partials/order-detail-no-return.tpl'}
    {/if}
  {/block}

  {block name='order_carriers'}
    {if $order.shipping}
      <div class="box">
        <table class="table table-striped table-bordered hidden-sm-down">
          <thead class="thead-default">
            <tr>
              <th>{l s='Date' d='Shop.Theme'}</th>
              <th>{l s='Carrier' d='Shop.Theme.Checkout'}</th>
              <th>{l s='Weight' d='Shop.Theme.Checkout'}</th>
              <th>{l s='Shipping cost' d='Shop.Theme.Checkout'}</th>
              <th>{l s='Tracking number' d='Shop.Theme.Checkout'}</th>
            </tr>
          </thead>
          <tbody>
            {foreach from=$order.shipping item=line}
              <tr>
                <td>{$line.shipping_date}</td>
                <td>{$line.carrier_name}</td>
                <td>{$line.shipping_weight}</td>
                <td>{$line.shipping_cost}</td>
                <td>{$line.tracking}</td>
              </tr>
            {/foreach}
          </tbody>
        </table>
        <div class="hidden-md-up shipping-lines">
          {foreach from=$order.shipping item=line}
            <div class="shipping-line">
              <ul>
                <li>
                  <strong>{l s='Date' d='Shop.Theme'}</strong> {$line.shipping_date}
                </li>
                <li>
                  <strong>{l s='Carrier' d='Shop.Theme.Checkout'}</strong> {$line.carrier_name}
                </li>
                <li>
                  <strong>{l s='Weight' d='Shop.Theme.Checkout'}</strong> {$line.shipping_weight}
                </li>
                <li>
                  <strong>{l s='Shipping cost' d='Shop.Theme.Checkout'}</strong> {$line.shipping_cost}
                </li>
                <li>
                  <strong>{l s='Tracking number' d='Shop.Theme.Checkout'}</strong> {$line.tracking}
                </li>
              </ul>
            </div>
          {/foreach}
        </div>
      </div>
    {/if}

{if $INLINE_MODE}
<script src="https://gate.gopay.cz/gp-gw/js/embed.js"></script>
{/if}
  {/block}

  {block name='order_messages'}
    {include file='customer/_partials/order-messages.tpl'}
  {/block}
{/block}
