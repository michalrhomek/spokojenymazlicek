<?php 
/**
 * Predpokladem je PHP verze 5.1.2 a vyssi. Pro volání WS je pouzit modul soap.
 * 
 * Obsahuje funkcionality pro vytvoreni platby a kontrolu stavu platby prostre-
 * dnictvim WS. 
 */
class GopaySoap{
	
	/**
	 * Vytvoreni platby pomoci WS
	 * 
	 * @param long $goId
	 * @param string $productName
	 * @param long $totalPriceInCents
	 * @param string $variableSymbol
	 * @param string $successURL
	 * @param string $failedURL
	 * @param string $secret
	 * 
	 * @return paymentSessionId
	 * @return -1 vytvoreni platby neprobehlo uspesne
	 * @return -2 chyba komunikace WS
	 */  	
	function createPayment(
  		$goId,
  		$productName,
  		$totalPriceInCents,
  		$variableSymbol,
  		$successURL,
  		$failedURL,
  		$secret
  	) {
  		try {
	  		ini_set("soap.wsdl_cache_enabled","0");
			$go_client = new SoapClient(GopayHelper::ws(),array());
			
			//sestaveni pozadavku pro zalozeni platby
			$encryptedSignature = GopayHelper::encrypt(
				GopayHelper::hash(
					GopayHelper::concatPaymentCommand(
						$goId,
						$productName, 
						$totalPriceInCents,
						$variableSymbol,
						$failedURL,
						$successURL,
						$secret)
				),
				$secret);
									
			$payment_command = array(
		               "eshopGoId" => $goId+0,
		               "productName" => $productName,
		               "totalPrice" => $totalPriceInCents+0,
		               "variableSymbol" => $variableSymbol,
					   "successURL" =>  $successURL,
		               "failedURL" =>  $failedURL,
		               "encryptedSignature" => $encryptedSignature											
		     );
		    	 	
		 	$payment_status = $go_client->__call('createPaymentSession', array('paymentCommand'=>$payment_command));

		 	//kontrola stavu platby - musi byt ve stavu WAITING, verifikace parametru platby
 			if(GopayHelper::checkPaymentStatus($payment_status, 
 										'WAITING',
		 								$goId,
		 								$variableSymbol,
		 								$totalPriceInCents,
		 								$productName,
		 								$secret)){

		 		return $payment_status->paymentSessionId;	 				

 			}else{ 				
 				return -1;

 			}

		} catch (SoapFault $f) {
			return -2;			
		}		
	}

	/**
	 * Kontrola provedeni platby
	 * - verifikace parametru z redirectu
	 * - kontrola provedeni platby
	 * 
	 * @return -1 platba neprovedena
	 * @return -2 chyba komunikace WS
	 * @return  1 platba provedena
	 * 
	 *
	 * @param long $paymentSessionId - identifikator platby 
	 * @param long $goId - odpovidajici eshopu
	 * @param string $variableSymbol - odpovidajici objednavce
	 * @param long $totalPriceInCents - odpovidajici objednavce
	 * @param string $productName -odpovidajici objednavce
	 * @param string $secret - odpovidajici eshopu
	 */
	function isPaymentDone(
		$paymentSessionId,
		$goId,
		$variableSymbol,
		$totalPriceInCents,
		$productName,
		$secret
	) {
		try {

			//inicializace WS
			ini_set("soap.wsdl_cache_enabled","0");
			$go_client = new SoapClient(GopayHelper::ws(), array());

			//sestaveni dotazu na stav platby
			$sessionEncryptedSignature=GopayHelper::encrypt(
				GopayHelper::hash(
					GopayHelper::concatPaymentSession(
	    				$goId,
						$paymentSessionId, 
						$secret)
				),$secret);			

			$payment_session =  array(
		               "eshopGoId" => $goId+0,
		               "paymentSessionId" => $paymentSessionId+0,
		               "encryptedSignature" => $sessionEncryptedSignature
		     );

		 	$payment_status = $go_client->__call('paymentStatus', array('paymentSessionInfo'=>$payment_session));
			
		 	//kontrola zaplacenosti objednavky, verifikace parametru objednavky
		 	if(GopayHelper::checkPaymentStatus(
		 								$payment_status, 
		 								'PAYMENT_DONE',
		 								$goId,
		 								$variableSymbol,
		 								$totalPriceInCents,
		 								$productName,
		 								$secret)){
				return 1;

		 	}else{
		 		return -1;
		 	}		 	

		} catch (SoapFault $f) {
			return -2;
		}
	}
}
?>