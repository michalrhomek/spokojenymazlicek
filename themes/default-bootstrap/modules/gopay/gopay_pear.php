<?php 
/**
 * Predpokladem je PHP verze 5.1.2 a vyssi. Pro volání WS je pouzit modul soap.
 * 
 * Obsahuje funkcionality pro vytvoreni platby a kontrolu stavu platby prostre-
 * dnictvim WS. 
 */

require_once('classes/api/seller/gopay_helper.php');

class GopayPear{
	
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
	 * @return payment_status
	 * @return null vytvoreni platby neprobehlo uspesne ci chyba komunikace WS
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

			$wsdl = new SOAP_WSDL(GopayHelper::ws());
			$go_client = $wsdl->getProxy();
			$go_client->setOpt('curl', CURLOPT_SSL_VERIFYPEER, 0);
			$go_client->setOpt('curl', CURLOPT_SSL_VERIFYHOST, 0);
			$go_client->setStyle('rpc');
			$go_client->setUse('literal');

			$encryptedSignature=GopayHelper::encrypt(
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
					"successURL" => $successURL,
					"failedURL" => $failedURL,
					"encryptedSignature" => $encryptedSignature,												
			);

			$payment_status = $go_client->call('createPaymentSession', 
												$param=array('payment_command'=>$payment_command),
												$options=array());

		 	//kontrola stavu platby - musi byt ve stavu WAITING, verifikace parametru platby
 			if(GopayHelper::checkPaymentStatus($payment_status, 
 										'WAITING',
		 								$goId,
		 								$variableSymbol,
		 								$totalPriceInCents,
		 								$productName,
		 								$secret)){

		 		return $payment_status;	 				

 			}else{ 				
 				return null;

 			}

		} catch (SoapFault $f) {
			return null;
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

			$wsdl = new SOAP_WSDL(GopayHelper::ws());
			$go_client = $wsdl->getProxy();
			$go_client->setOpt('curl', CURLOPT_SSL_VERIFYPEER, 0);
			$go_client->setOpt('curl', CURLOPT_SSL_VERIFYHOST, 0);
			$go_client->setStyle('rpc');
			$go_client->setUse('literal');

			$encryptedSignature=GopayHelper::encrypt(
					GopayHelper::hash(
							GopayHelper::concatPaymentSession(
									$goId,
									$returnedPaymentSessionId, 
									$secret)
					),
					$secret);			

			$payment_session =  array(
					"eshopGoId" => $goId+0,
					"paymentSessionId" => $returnedPaymentSessionId+0,
					"encryptedSignature" => $encryptedSignature
			);

			$payment_status = $go_client->call('paymentStatus',
					$param=array('paymentStatus'=>$payment_session),
					$options=array());

			if(GopayHelper::checkPaymentStatus($payment_status, 
					'PAYMENT_DONE', 
					$goId,
					$variableSymbol,
					$totalPriceInCents,
					$productName,
					$secret
			)){
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