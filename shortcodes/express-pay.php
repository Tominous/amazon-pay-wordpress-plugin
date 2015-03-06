<?php
/**
 * Pay with Amazon
 *
 * @category Amazon
 * @package Amazon_Login
 * @copyright Copyright (c) 2015 Amazon.com
 * @license http://opensource.org/licenses/Apache-2.0 Apache License, Version 2.0
 */

add_shortcode("express-pay", "express_pay_handler");
function express_pay_handler($args) {
	$sellerId =  get_option('amzn_seller_id');
	$lwaClientId =  get_option('amzn_lwa_client_id');
	$accessKey =  get_option('amzn_access_key'); 
	$secretKey =  get_option('amzn_secret_key');
	$note = ($args["note"] != '') ? $args["note"] : "purchase on " . get_home_url(); 
	$amount = ($args["amount"] != '') ? $args["amount"] : "1"; 

	$parameters = array('returnURL'=> get_home_url() . '/amzn-thank-you',
		'accessKey'=>  $accessKey,
		'lwaClientId'=> $lwaClientId,
		//'platformId'=> 'A3D68VL23XMOV2',
		'sellerId'=> $sellerId,
		'paymentAction'=> 'AuthorizeAndCapture',
		'sellerNote'=> $note,
		'currencyCode'=> 'USD',
		'amount'=> $amount); 

	uksort($parameters, 'strcmp');
	$secretKey = $secretKey;	
	$signature = _urlencode(_signParameters($parameters, $secretKey));
	$parameters['signature'] = $signature;
  $buttonId = uniqid();
	$button = "<script type='text/javascript' src='https://static-na.payments-amazon.com/OffAmazonPayments/us/js/Widgets.js'></script>
		<div id='AmazonPayButton" . $buttonId . "'></div>
		<script type='text/javascript'>
			OffAmazonPayments.Button('AmazonPayButton" . $buttonId . "', 'A1GV76EFH0T2Y7', {
				type: 'hostedPayment',
				hostedParametersProvider: function(done) {
					data =" . json_encode($parameters) . "; 
					done(data); 
				},
				onError: function(errorCode) {
					console.log(errorCode.getErrorCode() + ' ' + errorCode.getErrorMessage());
				}
			});
		</script>";
	return $button;
}

function _signParameters(array $parameters, $key)
{
		$stringToSign = null;
		$algorithm    = "HmacSHA256";
		$stringToSign = _calculateStringToSignV2($parameters);

		return _sign($stringToSign, $key, $algorithm);
}

function _calculateStringToSignV2(array $parameters)
{
		$data = 'POST';
		$data .= "\n";
		$data .= "payments.amazon.com";
		$data .= "\n";
		$data .= "/";
		$data .= "\n";
		$data .= _getParametersAsString($parameters);
		return $data;
}

function _getParametersAsString(array $parameters)
{
		$queryParameters = array();
		foreach ($parameters as $key => $value) {
				$queryParameters[] = $key . '=' . _urlencode($value);
		}
		return implode('&', $queryParameters);
}

function _urlencode($value)
{
		return str_replace('%7E', '~', rawurlencode($value));
}

function _sign($data, $key, $algorithm)
{
		if ($algorithm === 'HmacSHA1') {
				$hash = 'sha1';
		} else if ($algorithm === 'HmacSHA256') {
				$hash = 'sha256';
		} else {
				throw new Exception("Non-supported signing method specified");
		}
		return base64_encode(hash_hmac($hash, $data, $key, true));
}
?>
