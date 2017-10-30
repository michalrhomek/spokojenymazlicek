{if isset($gtmid)}
<!-- Datalayers -->
<script>
  var remarketing_params = {
  	/*dynamic remarketing*/
	{if isset($ecomm_pagetype)}'ecomm_pagetype': '{$ecomm_pagetype}',{/if}
	{if isset($ecomm_pcat)}'ecomm_pcat': [{$ecomm_pcat}],{/if}
	{if isset($ecomm_prodid)}'ecomm_prodid': [{$ecomm_prodid}],{/if}
	{if isset($ecomm_pname)}'ecomm_pname': [{$ecomm_pname}],{/if}
	{if isset($ecomm_pvalue)}'ecomm_pvalue': [{$ecomm_pvalue}],{/if}
	{if isset($ecomm_pvalue)}'ecomm_totalvalue': '{$ecomm_totalvalue}',{/if}
  };


dataLayer = [];
dataLayer.push ({   
 'remarketing_params': window.remarketing_params,
 {if isset($transactionId)}'transactionId': '{$transactionId}',{/if}
  	{if isset($transactionTotal)}'transactionTotal': '{$transactionTotal}',{/if}
  	{if isset($transactionShipping)}'transactionShipping': '{$transactionShipping}',{/if}
  	{if isset($transactionTax)}'transactionTax': '{$transactionTax}',{/if}
  	{if isset($transactionProducts)}
  		'transactionProducts': [
  			{foreach from=$transactionProducts item=item key=key name=name}
  				{literal}{{/literal}'sku': {$item.sku}, 'name': {$item.name}, 'category': {$item.category}, 'price': {$item.price}, 'quantity': {$item.quantity}{literal}},{/literal}
  			{/foreach}
  		]
  	{/if}
});
</script>
<!-- End Datalayers -->


<!-- Google Tag Manager -->
	<script>
	gtmid='{$gtmid}';
	{literal}
		(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
		new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
		j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
		'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
		})(window,document,'script','dataLayer',gtmid);
	{/literal}
	</script>
<!-- End Google Tag Manager -->
{else}
{/if}