<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/gopay.php');

$errors = '';
$result = false;
$gopay = new Gopay();
		
		//parametry obsazene v redirectu po potvrzeni platby - zruseni platby 
		$returnedPaymentSessionId = $_GET['paymentSessionId'];
		$returnedGoId = $_GET['eshopGoId'];
		$returnedVariableSymbol = $_GET['variableSymbol'];
		$returnedEncryptedSignature = $_GET['encryptedSignature'];
		
		//parametry zadane v konfiguraci
		$goId = Configuration::get('GOID');
		$gopayKey = Configuration::get('GOPAY_KEY');
		$configStore = Configuration::get('GOPAY_CONFIG_STORE');
		$historyUrl = Configuration::get('GOPAY_HISTORY_URL');
		
		$cart = new Cart(intval($returnedVariableSymbol));
		
		if (!$cart->id)
			$errors = $paypal->getL('cart').'<br />';
		elseif (Order::getOrderByCartId(intval($_POST['custom'])))
			$errors = $paypal->getL('order').'<br />';
		
		$amount = $cart->getOrderTotal(true, 4)*100; // castka v centech
		$productsAmount = $cart->getOrderTotal(true, 4);
		$shipping = $cart->getOrderShippingCost();
		$amount1 = $productsAmount+$shipping;
		$amount = (int)(round($amount1,2)*100);
				
		$products = $cart->getProducts();
		
		foreach ($products as $key => $product)
		{
			$products[$key]['name'] = str_replace('"', '\'', $product['name']);
			if (isset($product['attributes']))
				$products[$key]['attributes'] = str_replace('"', '\'', $product['attributes']);
			$products[$key]['name'] = htmlentities(utf8_decode($product['name']));
		}

		//variable symbol from cart
		$required = Db::getInstance()->getValue('
		SELECT `id_cart` FROM `'._DB_PREFIX_.'cart`
		WHERE id_cart = \''.intval($returnedVariableSymbol).'\'');
													
		//kontrola validity parametru v redirectu, opatreni proti podvrzeni potvrzeni, zruseni platby			
		if(GopayHelper::checkPaymentIdentity(
									$returnedGoId,
		 							$returnedPaymentSessionId,
		 							$returnedVariableSymbol,
		 							$returnedEncryptedSignature,
		 							
		 							$goId,
		 							$required,
		 							$gopayKey)
		 ){	
			
			//zpracovani objednavky, prezentace uspesne platby
			//pred distribuci zbozi je nutne provest kontrolu
			//prostrednictvim GoPay monitoru
					 									 	
		 // overeni platby u Gopay
		 	$result = GopaySoap::isPaymentDone(
					$returnedPaymentSessionId,
					$goId,
					$required,
					$amount,
					$product['name'],
					$gopayKey
		);
		
				if( $result > 0){
																				
				}else{
					$errors .= $gopay->getL('gopay_verified');
					exit;
				}

		}else{		
 			
 			$errors .= $gopay->getL('gopay_connect');
			exit;
		}
		
		if(empty($errors)) {
			$gopay->validateOrder($returnedVariableSymbol,_PS_OS_PAYMENT_,$amount/100,$gopay->displayName);
			
			header("Location: $historyUrl");
			header("Connection: close");
			exit;
			}
			else{
				echo $errors;
			}
?>