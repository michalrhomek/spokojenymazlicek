<?php
/** ########################################################################### * 
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
 * ########################################################################### **/
		Pms_GoPay_Extra_Config::init();
	echo '
<html>
	<head>
    	<title>GoPay - testovací skript</title>
    	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	</head>
	<body>
		<h4 style="color:red">Slouží pouze pro otestování vložených údajů a funkčnost spojení s bránou GoPay, nelze použít pro implementační testovací platby</h3>
		<b>Pokud se u kterékoliv položky zobrazuje stav "CHYBA", zašlete na GoPay podporu kompletní výpis či přímo printscreen obrazovky.</b>
		<br><br>

		goID: '.Pms_GoPay_Extra_Config::GO_ID().'<br>
		clientID: '.Pms_GoPay_Extra_Config::CLIENT_ID().'<br>
		clientSecret: '.Pms_GoPay_Extra_Config::CLIENT_SECRET().'<br><br><br>
		URL: '.Pms_GoPay_Extra_Config::getURL().'payments/payment<br><br><br>';

	if(!function_exists('curl_version'))
		echo "<h2 class=\"modal_h2\">----------------- &nbsp; &nbsp; &nbsp; PHP funkce CURL není povolene, povolte ji v nastavení Vašeho hostingu &nbsp; &nbsp; &nbsp; ---------------------</h2>";
    else
	{
		$admin = !empty($_GET["admin"]) ? $_GET["admin"] : false;
  		GopayTester::setAdmin($admin);

		GopayTester::runTest(Pms_GoPay_Extra_Config::GO_ID(), Pms_GoPay_Extra_Config::CLIENT_ID(), Pms_GoPay_Extra_Config::CLIENT_SECRET());
	}
	echo '
  		<br><br>
  		'.GopayTester::showPhpinfo().'
	</body>
</html>';

class GopayTester
{
	static $admin = false;

	public static function runTest($goID, $clientID, $clientSecret)
	{
		$gopay = new Pms_GoPay_Extra();

		if (self::$admin == true) {
			error_reporting(E_ALL|E_STRICT);
			ini_set('display_errors', 1);
		}

		echo "<br>";
		echo "<h2 class=\"modal_h2\">----------------- &nbsp; &nbsp; &nbsp; start test &nbsp; &nbsp; &nbsp; ---------------------</h2>";
		echo "<br>";
		if (!empty($goID) && !empty($clientID) && !empty($clientSecret))
		{
			echo "<br><br>";
			echo "<h3 class=\"modal_h3\">***** &nbsp; &nbsp; &nbsp; getAccessToken &nbsp; &nbsp; &nbsp; *******</h3>";
			$payment_token = Pms_GoPay_Extra_RestAPI::getAccessToken('payment-create');

			if (isset($payment_token['errors']))
			{
				echo self::koResult();
				echo "<div class=\"content\"><pre>";
				print_r($payment_token);
				echo "</pre></div>";
				echo self::errorToken('payment-create');
			} else {
				echo self::okResult();
				echo "<div class=\"content\"><pre>";
				print_r($payment_token);
				echo "</pre></div>";
			
				echo "<br><br>";
				echo "<h3 class=\"modal_h3\">***** &nbsp; &nbsp; &nbsp; testCreatePayment &nbsp; &nbsp; &nbsp; *******</h3>";


		  		$create_payment = self::testCreatePayment();

				if (!empty($create_payment->errors))
				{
					echo self::koResult();
					echo "<div class=\"content\"><pre>";
					print_r($create_payment);
					echo "</pre></div>";
					echo self::errorCreatePayment();
				} else {
					echo self::okResult();
					echo "<div class=\"content\"><pre>";
					print_r($create_payment);
					echo "</pre></div>";

					echo "<br><br>";
					echo "<h3 class=\"modal_h3\">***** &nbsp; &nbsp; &nbsp; checkPaymentStatus &nbsp; &nbsp; &nbsp; *******</h3>";


					$payment_status = Pms_GoPay_Extra_RestAPI::checkPaymentStatus($create_payment->id);

					if (!empty($payment_status->errors))
					{
						echo self::koResult();
						echo "<div class=\"content\"><pre>";
						print_r($payment_status);
						echo "</pre></div>";
						echo self::errorPaymentStatus($create_payment->id);
					} else {
						echo self::okResult();
						echo "<div class=\"content\"><pre>";
						print_r($payment_status);
						echo "</pre></div>";
					}
				}
			}
		} else
			echo self::koResult().' - Chybí některý z údajů  GoID, ClientID nebo ClientSecret';

		echo "<br><br>";
		echo "<h2 class=\"modal_h2\">----------------- &nbsp; &nbsp; &nbsp; end test &nbsp; &nbsp; &nbsp; ---------------------</h2>";

		
	}
	
	private static Function okResult() {
		return "Status: <span style='color:green'>OK</span><br>";
	}
	
	private static function koResult() {
		return "Status: <span style='color:red'>CHYBA</span><br>";
	}
	
	public static function setAdmin($new_admin) {
		self::$admin = $new_admin;
	}
	
	public static function testCreatePayment()
	{
		$ch = curl_init();
		$data = array(
			'payer' => array(
				'default_payment_instrument' => 'PAYMENT_CARD',
				'contact' => array(
					'first_name' => 'Petr',
					'last_name' => 'Testík',
					'email' => 'email@email.cz',
					'phone_number' => '+420603558899',
					'city' => 'Testovanov',
					'street' => 'Testová 155',
					'postal_code' => '74235',
					'country_code' => 'CZE'
				),
			),
			'target' => array(
				'type' => 'ACCOUNT',
				'goid' => Pms_GoPay_Extra_Config::GO_ID()
			),
			'amount' => 15550,
			'currency' => 'CZK',
			'order_number' => 666,
			'order_description' => Configuration::get(Pms_GoPay_Extra::$SFIX.'_ORDER_DESCRIPTION'),
			'items' => array(
				array(
					'name' => 'Product name 1',
					'amount' => 10000
				),
				array(
					'name' => 'Product name 2',
					'amount' => 5550
				)
			),
			'callback' => array(
				'return_url' => "http://testPlatby.zde/je/predavan/parametr/return_url",
				'notification_url' => "http://testPlatby.zde/je/predavan/parametr/notification_url"
			),
			'lang' => 'en'
		);

		if (Configuration::get(Pms_GoPay_Extra::$SFIX.'_PREAUTHORIZED'))
			$data = array_merge($data, array(
				'preauthorization' => 'true'
			));

		if (Configuration::get(Pms_GoPay_Extra::$SFIX.'_RECURRENT'))
			$data = array_merge($data, array(
				'recurrence' => array(
					'recurrence_cycle' => Configuration::get(Pms_GoPay_Extra::$SFIX.'_RECURRENCE_CYCLE'),
					'recurrence_period' => Configuration::get(Pms_GoPay_Extra::$SFIX.'_RECURRENCE_PERIOD'),
					'recurrence_date_to' => Pms_GoPay_Extra_RestAPI::getDate()
				)
			));

		if(Configuration::get(Pms_GoPay_Extra::$SFIX.'_EET'))
			$data = array_merge($data, array(
				'eet' => array(
						'celk_trzba' => 15550,
						'cest_sluz' => '',
						'urceno_cerp_zuct' => '',
						'cerp_zuct' => '',
						'mena' => 'CZK',
						'zakl_dan1' => 10000,
						'dan1' => 1736,
						'zakl_nepodl_dph' => 0,
						'zakl_dan3' => 0,
						'dan3' => 0,
						'zakl_dan2' => 5550,
						'dan2' => 724
				)
			));

		return Pms_GoPay_Extra_RestAPI::paymentCeate($data);
	}

	public static function showPhpinfo()
	{
		if (self::$admin == true) {
			phpinfo();
		}
	}

	public static function errorToken($scope = 'payment-all')
	{
		return "<div class=\"content\"><pre><code class=\"highlight php\" style=\"display: inline;\"><span class=\"cp\">&lt;?php</span>
<span class=\"nv\">&amp;ch</span> <span class=\"o\">=</span> <span class=\"nb\">curl_init</span><span class=\"p\">();</span>

<span class=\"nb\">curl_setopt</span><span class=\"p\">(</span><span class=\"nv\">&amp;ch</span><span class=\"p\">,</span> <span class=\"nx\">CURLOPT_URL</span><span class=\"p\">,</span> <span class=\"s2\">\"</span><span class=\"red\">".Pms_GoPay_Extra_Config::getURL()."</span><span class=\"s2\">oauth2/token\"</span><span class=\"p\">);</span>
<span class=\"nb\">curl_setopt</span><span class=\"p\">(</span><span class=\"nv\">&amp;ch</span><span class=\"p\">,</span> <span class=\"nx\">CURLOPT_HTTPHEADER</span><span class=\"p\">,</span>
  <span class=\"k\">array</span><span class=\"p\">(</span><span class=\"s1\">'Accept: application/json'</span><span class=\"p\">,</span>
        <span class=\"s1\">'Accept-Language: <span class=\"red\">".Context::getContext()->language->language_code."</span><span class=\"s1\">'</span><span class=\"p\">,</span>
        <span class=\"s1\">'Content-Type: application/x-www-form-urlencoded'</span><span class=\"p\">));</span>
<span class=\"nb\">curl_setopt</span><span class=\"p\">(</span><span class=\"nv\">&amp;ch</span><span class=\"p\">,</span> <span class=\"nx\">CURLOPT_POST</span><span class=\"p\">,</span> <span class=\"kc\">true</span><span class=\"p\">);</span>
<span class=\"nb\">curl_setopt</span><span class=\"p\">(</span><span class=\"nv\">&amp;ch</span><span class=\"p\">,</span> <span class=\"nx\">CURLOPT_RETURNTRANSFER</span><span class=\"p\">,</span> <span class=\"kc\">true</span><span class=\"p\">);</span>
<span class=\"nb\">curl_setopt</span><span class=\"p\">(</span><span class=\"nv\">&amp;ch</span><span class=\"p\">,</span> <span class=\"nx\">CURLOPT_USERPWD</span><span class=\"p\">,</span> <span class=\"red\">".Pms_GoPay_Extra_Config::CLIENT_ID()."</span><span class=\"s2\">:</span><span class=\"red\">".Pms_GoPay_Extra_Config::CLIENT_SECRET()."</span><span class=\"p\">);</span> 

<span class=\"nb\">curl_setopt</span><span class=\"p\">(</span><span class=\"nv\">&amp;ch</span><span class=\"p\">,</span> <span class=\"nx\">CURLOPT_POSTFIELDS</span><span class=\"p\">,</span> <span class=\"s2\">\"grant_type=client_credentials&scope=</span><span class=\"red\">".$scope."</span><span class=\"s2\">\"</span><span class=\"p\">);</span>

<span class=\"nv\">&amp;result</span> <span class=\"o\">=</span> <span class=\"nb\">curl_exec</span><span class=\"p\">(</span><span class=\"nv\">&amp;ch</span><span class=\"p\">);</span>
<span class=\"cp\">?&gt;</span>
</code></pre></div>";
	}

	public static function errorCreatePayment()
	{
		$data = "<div class=\"content\"><pre><code class=\"highlight php\" style=\"display: inline;\"><span class=\"cp\">&lt;?php</span>
<span class=\"nv\">&amp;ch</span> <span class=\"o\">=</span> <span class=\"nb\">curl_init</span><span class=\"p\">();</span>

<span class=\"nv\">&amp;data</span> <span class=\"o\">=</span> <span class=\"k\">array</span><span class=\"p\">(</span>
  <span class=\"s1\">'payer'</span> <span class=\"o\">=&gt;</span> <span class=\"k\">array</span><span class=\"p\">(</span>
      <span class=\"s1\">'contact'</span> <span class=\"o\">=&gt;</span> <span class=\"k\">array</span><span class=\"p\">(</span>
          <span class=\"s1\">'first_name'</span> <span class=\"o\">=&gt;</span> <span class=\"s1\">'Petr'</span><span class=\"p\">,</span>
          <span class=\"s1\">'last_name'</span> <span class=\"o\">=&gt;</span> <span class=\"s1\">'Testík'</span><span class=\"p\">,</span>
          <span class=\"s1\">'email'</span> <span class=\"o\">=&gt;</span> <span class=\"s1\">'email@email.cz'</span><span class=\"p\">,</span>
          <span class=\"s1\">'phone_number'</span> <span class=\"o\">=&gt;</span> <span class=\"s1\">'+420603558899'</span><span class=\"p\">,</span>
          <span class=\"s1\">'city'</span> <span class=\"o\">=&gt;</span> <span class=\"s1\">'Testovanov'</span><span class=\"p\">,</span>
          <span class=\"s1\">'street'</span> <span class=\"o\">=&gt;</span> <span class=\"s1\">'Testová 155'</span><span class=\"p\">,</span>
          <span class=\"s1\">'postal_code'</span> <span class=\"o\">=&gt;</span> <span class=\"s1\">'742 35'</span><span class=\"p\">,</span>
          <span class=\"s1\">'country_code'</span> <span class=\"o\">=&gt;</span> <span class=\"s1\">'CZE'</span><span class=\"p\">,</span>
      <span class=\"p\">),</span>
  <span class=\"p\">),</span>
  <span class=\"s1\">'target'</span> <span class=\"o\">=&gt;</span> <span class=\"k\">array</span><span class=\"p\">(</span><span class=\"s1\">'type'</span> <span class=\"o\">=&gt;</span> <span class=\"s1\">'ACCOUNT'</span><span class=\"p\">,</span>
                    <span class=\"s1\">'goid'</span> <span class=\"o\">=&gt;</span> <span class=\"s1\">'</span><span class=\"red\">".Pms_GoPay_Extra_Config::GO_ID()."</span><span class=\"s1\">'</span><span class=\"p\">),</span>
  <span class=\"s1\">'amount'</span> <span class=\"o\">=&gt;</span> <span class=\"s1\">'15550'</span><span class=\"p\">,</span>
  <span class=\"s1\">'currency'</span> <span class=\"o\">=&gt;</span> <span class=\"s1\">'CZK'</span><span class=\"p\">,</span>
  <span class=\"s1\">'order_number'</span> <span class=\"o\">=&gt;</span> <span class=\"s1\">'666'</span><span class=\"p\">,</span>
  <span class=\"s1\">'order_description'</span> <span class=\"o\">=&gt;</span> <span class=\"s1\">'</span><span class=\"red\">".Configuration::get(Pms_GoPay_Extra::$SFIX.'_ORDER_DESCRIPTION')."</span><span class=\"s1\">'</span><span class=\"p\">,</span>
  <span class=\"s1\">'items'</span> <span class=\"o\">=&gt;</span> <span class=\"k\">array</span><span class=\"p\">(</span>
      <span class=\"k\">array</span><span class=\"p\">(</span><span class=\"s1\">'name'</span> <span class=\"o\">=&gt;</span> <span class=\"s1\">'Product name 1'</span><span class=\"p\">,</span> <span class=\"s1\">'amount'</span> <span class=\"o\">=&gt;</span> <span class=\"s1\">'10000'</span><span class=\"p\">),</span>
      <span class=\"k\">array</span><span class=\"p\">(</span><span class=\"s1\">'name'</span> <span class=\"o\">=&gt;</span> <span class=\"s1\">'Product name 2'</span><span class=\"p\">,</span> <span class=\"s1\">'amount'</span> <span class=\"o\">=&gt;</span> <span class=\"s1\">'5550'</span><span class=\"p\">)</span>
  <span class=\"p\">),</span>
  ";
if (Configuration::get(Pms_GoPay_Extra::$SFIX.'_PREAUTHORIZED'))
  $data .= "<span class=\"s1\">'preauthorization'</span> <span class=\"o\">=&gt;</span> <span class=\"s1\">'true'</span><span class=\"p\">,</span>
  ";
if (Configuration::get(Pms_GoPay_Extra::$SFIX.'_RECURRENT'))
  $data .= "<span class=\"s1\">'recurrence'</span> <span class=\"o\">=&gt;</span> <span class=\"k\">array</span><span class=\"p\">(</span>
      <span class=\"s1\">'recurrence_cycle'</span> <span class=\"o\">=&gt;</span> <span class=\"s1\">'</span><span class=\"red\">".Configuration::get(Pms_GoPay_Extra::$SFIX.'_RECURRENCE_CYCLE')."</span><span class=\"s1\">'</span><span class=\"p\">,</span>
      <span class=\"s1\">'recurrence_period'</span> <span class=\"o\">=&gt;</span> <span class=\"s1\">'</span><span class=\"red\">".Configuration::get(Pms_GoPay_Extra::$SFIX.'_RECURRENCE_PERIOD')."</span><span class=\"s1\">'</span><span class=\"p\">,</span>
      <span class=\"s1\">'recurrence_date_to'</span> <span class=\"o\">=&gt;</span> <span class=\"s1\">'</span><span class=\"red\">".Pms_GoPay_Extra_RestAPI::getDate()."</span><span class=\"s1\">'</span><span class=\"p\">
  ),</span>
  ";

$payment_token = Pms_GoPay_Extra_RestAPI::getAccessToken('payment-all');
$data .= "<span class=\"s1\">'callback'</span> <span class=\"o\">=&gt;</span> <span class=\"k\">array</span><span class=\"p\">(</span>
      <span class=\"s1\">'return_url'</span> <span class=\"o\">=&gt;</span> <span class=\"s1\">'http://testPlatby.zde/je/predavan/parametr/return_url'</span><span class=\"p\">,</span>
      <span class=\"s1\">'notification_url'</span> <span class=\"o\">=&gt;</span> <span class=\"s1\">'http://testPlatby.zde/je/predavan/parametr/notification_url'</span>
  <span class=\"p\">)</span>
<span class=\"p\">);</span>

<span class=\"nv\">&amp;data_send</span> <span class=\"o\">=</span> <span class=\"nb\">json_encode</span><span class=\"p\">(</span><span class=\"nv\">&amp;data</span><span class=\"p\">);</span>

<span class=\"nb\">curl_setopt</span><span class=\"p\">(</span><span class=\"nv\">&amp;ch</span><span class=\"p\">,</span> <span class=\"nx\">CURLOPT_URL</span><span class=\"p\">,</span> <span class=\"s2\">\"</span><span class=\"red\">".Pms_GoPay_Extra_Config::getURL()."</span><span class=\"s2\">payments/payment\"</span><span class=\"p\">);</span>
<span class=\"nb\">curl_setopt</span><span class=\"p\">(</span><span class=\"nv\">&amp;ch</span><span class=\"p\">,</span> <span class=\"nx\">CURLOPT_HTTPHEADER</span><span class=\"p\">,</span> 
  <span class=\"k\">array</span><span class=\"p\">(</span><span class=\"s1\">'Accept: application/json'</span><span class=\"p\">,</span>
        <span class=\"s1\">'Content-Type: application/json'</span><span class=\"p\">,</span>
        <span class=\"s1\">'Accept-Language: <span class=\"red\">".Context::getContext()->language->language_code."</span><span class=\"s1\">'</span><span class=\"p\">,</span>
        <span class=\"s1\">'Authorization: Bearer</span> <span class=\"red\">".$payment_token['access_token']."</span><span class=\"s1\">'</span> <span class=\"p\">));</span>

<span class=\"nb\">curl_setopt</span><span class=\"p\">(</span><span class=\"nv\">&amp;ch</span><span class=\"p\">,</span> <span class=\"nx\">CURLOPT_POST</span><span class=\"p\">,</span> <span class=\"kc\">true</span><span class=\"p\">);</span>
<span class=\"nb\">curl_setopt</span><span class=\"p\">(</span><span class=\"nv\">&amp;ch</span><span class=\"p\">,</span> <span class=\"nx\">CURLOPT_RETURNTRANSFER</span><span class=\"p\">,</span> <span class=\"kc\">true</span><span class=\"p\">);</span>
<span class=\"nb\">curl_setopt</span><span class=\"p\">(</span><span class=\"nv\">&amp;ch</span><span class=\"p\">,</span> <span class=\"nx\">CURLOPT_POSTFIELDS</span><span class=\"p\">,</span> <span class=\"nv\">&amp;data_send</span><span class=\"p\">);</span>

<span class=\"nv\">&amp;result</span> <span class=\"o\">=</span> <span class=\"nb\">curl_exec</span><span class=\"p\">(</span><span class=\"nv\">&amp;ch</span><span class=\"p\">);</span>
<span class=\"cp\">?&gt;</span>
</code></pre></div>";

		return $data;
	}

	public static function errorPaymentStatus($id)
	{
		$payment_token = Pms_GoPay_Extra_RestAPI::getAccessToken('payment-all');
		return "<div class=\"content\"><pre><code class=\"highlight php\" style=\"display: inline;\"><span class=\"cp\">&lt;?php</span>
<span class=\"nv\">&amp;ch</span> <span class=\"o\">=</span> <span class=\"nb\">curl_init</span><span class=\"p\">();</span>

<span class=\"nb\">curl_setopt</span><span class=\"p\">(</span><span class=\"nv\">&amp;ch</span><span class=\"p\">,</span> <span class=\"nx\">CURLOPT_URL</span><span class=\"p\">,</span> <span class=\"s2\">\"</span><span class=\"red\">".Pms_GoPay_Extra_Config::getURL()."</span><span class=\"s2\">payments/payment/</span><span class=\"nv\">".$id."</span><span class=\"p\">);</span>

<span class=\"nb\">curl_setopt</span><span class=\"p\">(</span><span class=\"nv\">&amp;ch</span><span class=\"p\">,</span> <span class=\"nx\">CURLOPT_HTTPHEADER</span><span class=\"p\">,</span> 
  <span class=\"k\">array</span><span class=\"p\">(</span><span class=\"s1\">'Accept: application/json'</span><span class=\"p\">,</span>
        <span class=\"s1\">'Content-Type: application/x-www-form-urlencoded'</span><span class=\"p\">,</span>
        <span class=\"s1\">'Accept-Language: <span class=\"red\">".Context::getContext()->language->language_code."</span><span class=\"s1\">'</span>
        <span class=\"s1\">'Authorization: Bearer </span> <span class=\"red\">".$payment_token['access_token']."</span><span class=\"p\">));</span>

<span class=\"nb\">curl_setopt</span><span class=\"p\">(</span><span class=\"nv\">&amp;ch</span><span class=\"p\">,</span> <span class=\"nx\">CURLOPT_HTTPGET</span><span class=\"p\">,</span> <span class=\"kc\">true</span><span class=\"p\">);</span>
<span class=\"nb\">curl_setopt</span><span class=\"p\">(</span><span class=\"nv\">&amp;ch</span><span class=\"p\">,</span> <span class=\"nx\">CURLOPT_RETURNTRANSFER</span><span class=\"p\">,</span> <span class=\"kc\">true</span><span class=\"p\">);</span>

<span class=\"nv\">&amp;result</span> <span class=\"o\">=</span> <span class=\"nb\">curl_exec</span><span class=\"p\">(</span><span class=\"nv\">&amp;ch</span><span class=\"p\">);</span>
<span class=\"cp\">?&gt;</span>
</code></pre></div>";
	}
}