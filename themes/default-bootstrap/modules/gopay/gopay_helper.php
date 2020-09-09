<?php 
/**
 * Předpokladem je PHP verze 5.1.2 a vyšší s moduly mhash, mcrypt.
 *
 * Obsahuje pomocne funkcionality pro komunikaci s platebnim rozh-
 * ranim GoPay.
 */
class GopayHelper{

	/**
	 * URL platebni brany pro zakladni integraci
	 *
	 * @return URL
	 */
	function baseIntegrationURL(){
		$baseIntegrationURL = Configuration::get('GOPAY_BASE_URL');
		return $baseIntegrationURL;		
//		return 'https://testgw.gopay.cz/zaplatit-jednoducha-integrace';		
	}

	/**
	 * URL platebni brany pro uplnou integraci
	 *
	 * @return URL
	 */
	function fullIntegrationURL(){
	}

	/**
	 * URL WS
	 *
	 * @return URL - wsdl
	 */
	function ws(){
		$wsURL = Configuration::get('GOPAY_WS_URL');
		return $wsURL;
	}		

	/**
	 * Sestaveni retezce pro podpis platebniho prikazu
	 *
	 * @param long $goId
	 * @param string $productName
	 * @param long $totalPriceInCents
	 * @param string $variableSymbol
	 * @param string $failedURL
	 * @param string $successURL
	 * @param string $key
	 * @return retezec pro podpis 
	 */
  	function concatPaymentCommand(
  		$goId,
  		$productName, 
  		$totalPriceInCents, 
  		$variableSymbol,
  		$failedURL,
  		$successURL, 
  		$key){

        return $goId."|".$productName."|".$totalPriceInCents."|".$variableSymbol."|".$failedURL."|".$successURL."|".$key; 
  	}

  	/**
  	 * Sestaveni retezce pro podpis vysledku platby
  	 *
  	 * @param long $goId
  	 * @param string $productName
  	 * @param long $totalPriceInCents
  	 * @param string $variableSymbol
  	 * @param string $result
  	 * @param string $sessionState
  	 * @param string $key
  	 * @return retezec pro podpis
  	 */
  	function concatPaymentResult(
  		$goId,
  		$productName, 
  		$totalPriceInCents, 
  		$variableSymbol,
  		$result,
  		$sessionState,
  		$key){

        return $goId."|".$productName."|".$totalPriceInCents."|".$variableSymbol."|".$result."|".$sessionState."|".$key; 
  	}

  	/**
  	 * Sestaveni retezce pro podpis sessionInfo
  	 *
  	 * @param long $goId
  	 * @param long $paymentSessionId
  	 * @param string $key
  	 * @return retezec pro podpis
  	 */
  	function concatPaymentSession(
  		$goId,
  	 	$paymentSessionId,  	 	 
  	 	$key){
        return $goId."|".$paymentSessionId."|".$key; 
  	}

  	/**
  	 * Sestaveni retezce pro podpis popisu platby (paymentIdentity)
  	 *
  	 * @param long $goId
  	 * @param long $paymentSessionId
  	 * @param string $variableSymbol
  	 * @param string $key
  	 * @return retezec pro podpis
  	 */
  	function concatPaymentIdentity(
  		$goId,
  	 	$paymentSessionId,
  	 	$variableSymbol, 
  	 	$key){
        return $goId."|".$paymentSessionId."|".$variableSymbol."|".$key; 
  	}

  	/**
  	 * Sifrovani dat 3DES
  	 *
  	 * @param string $data
  	 * @param string $key
  	 * @return sifrovany obsah v HEX forme
  	 */
  	function encrypt($data, $key){
  		$td = mcrypt_module_open (MCRYPT_3DES, '', MCRYPT_MODE_ECB, '');
        $mcrypt_iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init ($td, substr($key, 0, mcrypt_enc_get_key_size($td)), $mcrypt_iv);
        $encrypted_data = mcrypt_generic ($td, $data);
        mcrypt_generic_deinit ($td);
        mcrypt_module_close ($td);

        return bin2hex($encrypted_data);
  	}

  	/**
  	 * desifrovani
  	 *
  	 * @param string $data
  	 * @param string $key
  	 * @return desifrovany retezec
  	 */
  	function decrypt($data, $key){
  		$td = mcrypt_module_open (MCRYPT_3DES, '', MCRYPT_MODE_ECB, '');
        $mcrypt_iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init ($td, substr($key, 0, mcrypt_enc_get_key_size($td)), $mcrypt_iv);

        $decrypted_data = mdecrypt_generic($td, GopayHelper::convert($data));
        mcrypt_generic_deinit ($td);
        mcrypt_module_close ($td);

		return Trim($decrypted_data);

  	}  	

  	/**
  	 * hash SHA1 dat
  	 *
  	 * @param string $data
  	 * @return otisk dat SHA1
  	 */
  	function hash($data){
  		if(function_exists("sha1")==true){
  			$hash = sha1($data, true);

  		} else {
  			$hash = mhash(MHASH_SHA1,$data);
  		}

  		return bin2hex($hash);   		
  	}

  	/**
  	 * konverze z HEX -> string
  	 *
  	 * @param string $hexString
  	 * @return konverze z HEX -> string
  	 */
  	function convert($hexString){

  		$hexLenght = strlen($hexString);
  		// only hex numbers is allowed                
  		if ($hexLenght % 2 != 0 || preg_match("/[^\da-fA-F]/",$hexString)) return FALSE;
  		$binString = "";
  		for ($x = 1; $x <= $hexLenght/2; $x++)                
  		{                        
  			$binString .= chr(hexdec(substr($hexString,2 * $x - 2,2)));

  		}

  		return $binString;

  	}

	/**
	 * Kontrola vysledku zaplacene platby proti
	 * internim udajum objednavky
	 * 
	 * - verifikace podpisu
	 * 
	 *
	 * @param mixed $payment_status - vysledek volani paymentStatus
	 * @param string $session_state - ocekavany stav paymentSession (WAITING, PAYMENT_DONE)
	 * @param long $goId - identifikace eshopu
	 * @param string $variableSymbol - identifikace akt. objednavky
	 * @param long $priceInCents - cena objednavky v centech
	 * @param string $productName - nazev zbozi
	 * @param string $secret - klic urceny k podepisovani komunikace
	 * 
	 * @return true
	 * @return false
	 */
  	function checkPaymentStatus(
  				$payment_status,
  	 			$session_state,
  	 			$goId,
  	 			$variableSymbol,  	 			
  	 			$priceInCents,
  	 			$productName,
  	 			$secret  	 			
  	 			){

		$valid = true;

		if( $payment_status){

			if( $payment_status->result != 'CALL_COMPLETED'){
				$valid = false;
				echo "PS invalid call state state<br>";
			}

			if( $payment_status->sessionState != $session_state){
				$valid = false;
				echo "PS invalid session state<br>";			
			}

			if($payment_status->variableSymbol != $variableSymbol){
				$valid = false;
				echo "PS invalid VS <br>";
			}

			if($payment_status->productName != $productName){
				$valid = false;
				echo "PS invalid PN <br>";
			}

			if($payment_status->eshopGoId != $goId){
				$valid = false;
				echo "PS invalid EID<br>";
			}

			if($payment_status->totalPrice != $priceInCents){
				$valid = false;
				echo "PS invalid price<br>";
			}			

			$hashedSignature=GopayHelper::hash(
					GopayHelper::concatPaymentResult(
						$payment_status->eshopGoId,
						$payment_status->productName,						
						$payment_status->totalPrice,
						$payment_status->variableSymbol,
						$payment_status->result,
						$payment_status->sessionState,
						$secret)
				);
			$decryptedHash = GopayHelper::decrypt($payment_status->encryptedSignature,$secret);

			if($decryptedHash != $hashedSignature){
				$valid = false;
				echo "PS invalid status signature <br>";
			}

		}else{
			$valid = false;
			echo "none payment status <br>";
		}

		return $valid;
	}

	/**
	 * Kontrola parametru predavanych ve zpetnem volani
	 * po potvrzeni/zruseni platby
	 * 
	 * - verifikace podpisu
	 * 
	 *
	 * @param long $returnedGoId - goId vracene v redirectu
	 * @param long $returnedPaymentSessionId - paymentSessionId vracene v redirectu
	 * @param string $returnedVariableSymbol - variableSymbol vracene v redirectu
	 * @param string $returnedEncryptedSignature - encryptedSignature vracene v redirectu 
	 * @param float $goId - identifikace eshopu
	 * @param string $variableSymbol - oznaceni objednavky
	 * @param string $secret - klic urceny k podepisovani komunikace
	 * 
	 * @return true
	 * @return false
	 */
  	function checkPaymentIdentity(
  				$returnedGoId,
  				$returnedPaymentSessionId,  				
  				$returnedVariableSymbol,
  				$returnedEncryptedSignature,
  	 			$goId,
  	 			$variableSymbol,  	 			
  	 			$secret  	 			
  	 			){

		$valid = true;
		if($returnedVariableSymbol != $variableSymbol){
			$valid = false;
			echo "PI invalid VS <br>";
		}

		if($returnedGoId != $goId){
			$valid = false;
			echo "PI invalid EID<br>";
		}

		$hashedSignature=GopayHelper::hash(
				GopayHelper::concatPaymentIdentity(
					$returnedGoId,
					$returnedPaymentSessionId,						
					$returnedVariableSymbol,
					$secret)
			);
		$decryptedHash = GopayHelper::decrypt($returnedEncryptedSignature, $secret);

		if($decryptedHash != $hashedSignature){
			$valid = false;
			echo "PI invalid signature <br>";
		}

		return $valid;
	}


  	/**
	 * Sestaveni formulare platebniho tl.
	 * 
	 *
	 * @param long $buyerGoId - identifikace uzivatele
	 * @param long $totalPrice - cena objednavky v centech
	 * @param string $productName - nazev zbozi
	 * @param string $variableSymbol - identifikace akt. objednavky
	 * @param string $successURL - URL, kam se ma prejit po uspesnem zaplaceni
	 * @param string $failedURL - URL, kam se ma prejit po neuspesnem zaplaceni
	 * @param string $secret - klic urceny k podepisovani komunikace
	 * @param string $iconUrl - URL obrazku tlacitka 
	 * 
	 * @return HTML kod platebniho tlacitka
	 */
  	function createForm(
  			$buyerGoId,
  			$totalPrice,
  			$productName,
  			$variableSymbol,
  			$successURL,
  			$failedURL,
  	 		$secret,
  	 		$iconUrl
  	 		){

  		$encryptedSignature = GopayHelper::encrypt(
  				GopayHelper::hash(
  					GopayHelper::concatPaymentCommand(
  						$buyerGoId,
  						$productName, 
  						$totalPrice,
  						$variableSymbol,
  						$successURL,
  						$failedURL,
  						$secret)
  					), $secret);

  		$formBuffer = "";
  		$formBuffer .= "<form method='post' action='" . buttonURL() . "' target='_blank'>\n";
	  		$formBuffer .= "<input type='hidden' name='paymentCommand.buyerGoId' value='" . $buyerGoId . "' />\n";
	  		$formBuffer .= "<input type='hidden' name='paymentCommand.totalPrice' value='" . $totalPrice . "' />\n";
	  		$formBuffer .= "<input type='hidden' name='paymentCommand.productName' value='" . $productName . "' />\n";
	  		$formBuffer .= "<input type='hidden' name='paymentCommand.variableSymbol' value='" . $variableSymbol . "' />\n";
	  		$formBuffer .= "<input type='hidden' name='paymentCommand.successURL' value='" . $successURL . "' />\n";
	  		$formBuffer .= "<input type='hidden' name='paymentCommand.failedURL' value='" . $failedURL . "' />\n";
	  		$formBuffer .= "<input type='hidden' name='paymentCommand.encryptedSignature' value='" . $encryptedSignature . "' />\n";
	  		$formBuffer .= "<input type='submit' name='submit' value='' style='background:url(" . $iconUrl . ") no-repeat;width:100px;height:30px;border:none;'>\n";
	  	$formBuffer .= "</form>\n";

	  	return $formBuffer;
  	}

  	/**
	 * Sestaveni platebniho tl. jako odkazu
	 * 
	 *
	 * @param long $buyerGoId - identifikace uzivatele
	 * @param long $totalPrice - cena objednavky v centech
	 * @param string $productName - nazev zbozi
	 * @param string $variableSymbol - identifikace akt. objednavky
	 * @param string $successURL - URL, kam se ma prejit po uspesnem zaplaceni
	 * @param string $failedURL - URL, kam se ma prejit po neuspesnem zaplaceni
	 * @param string $secret - klic urceny k podepisovani komunikace
	 * @param string $iconUrl - URL obrazku tlacitka 
	 * 
	 * @return HTML kod platebniho tlacitka
	 */
  	function createHref(
  			$buyerGoId,
  			$totalPrice,
  			$productName,
  			$variableSymbol,
  			$successURL,
  			$failedURL,
  	 		$secret,
  	 		$iconUrl
  	 		){

  		$encryptedSignature = GopayHelper::encrypt(
  				GopayHelper::hash(
  					GopayHelper::concatPaymentCommand(
  						$buyerGoId,
  						$productName, 
  						$totalPrice,
  						$variableSymbol,
  						$failedURL,
  						$successURL,
  						$secret)
  					), $secret);

  		$params = "";
  		$params .= "paymentCommand.buyerGoId=" . $buyerGoId;
  		$params .= "&paymentCommand.totalPrice=" . $totalPrice;
  		$params .= "&paymentCommand.productName=" . urlencode($productName);
  		$params .= "&paymentCommand.variableSymbol=" . urlencode($variableSymbol);
  		$params .= "&paymentCommand.successURL=" . urlencode($successURL);
  		$params .= "&paymentCommand.failedURL=" . urlencode($failedURL);
  		$params .= "&paymentCommand.encryptedSignature=" . urlencode($encryptedSignature);

  		$formBuffer = "";
  		$formBuffer .= "<a target='_blank' href='" . buttonURL() . "?" . $params . "' >";
  		$formBuffer .= " <img src='" . $iconUrl . "' border='0' style='border:none;'/> ";
  		$formBuffer .= "</a>";

	  	return $formBuffer;
  	}
}
?>