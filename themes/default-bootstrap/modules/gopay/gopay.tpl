<p class="payment_module">
	<a href="javascript:$('#gopay_form').submit();" title="{l s='Platí Gopay' mod='gopay'}">
		<img src="{$module_template_dir}logo.gif" alt="{l s='Platí Gopay' mod='gopay'}" />
		{l s='Platí Gopay' mod='gopay'}
	</a>
</p>

<form action="{$gopayUrl}" method="post" id="gopay_form" class="hidden">
	
	<input type="hidden" name="paymentCommand.eshopGoId" value="{$goId}" />
	<input type="hidden" name="paymentCommand.productName" value="{$productName}" />
	<input type="hidden" name="paymentCommand.totalPrice" value="{$amount}" />
	<input type="hidden" name="paymentCommand.variableSymbol" value="{$reference}" />
	<input type="hidden" name="paymentCommand.successURL" value="{$successUrl}" />
	<input type="hidden" name="paymentCommand.failedURL" value="{$failedUrl}" />
	<input type="hidden" name="paymentCommand.encryptedSignature" value="{$encryptedSignature}" />
		
</form>