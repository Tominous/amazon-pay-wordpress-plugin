<?php
/*
Plugin Name: Amazon Express Checkout
Plugin URI: http://payments.amazon.com
Description: Pay with Amazon
Version: 0.1 BETA
Author: Maxim Kim
Author URI: http://payments.amazon.com
*/

/*
Amazon Express Checkout (Wordpress Plugin)
*/

//define plugin defaults
DEFINE("DEMOLP_CATEGORYLIST", "");

//tell wordpress to register the demolistposts shortcode


include(dirname(__FILE__) . '/settings.php');
include(dirname(__FILE__) . '/editor.php');


add_shortcode("express-pay", "express_pay_handler");
function express_pay_handler($args) {
    $sellerId =  get_option('amzn_seller_id');
    $lwaClientId =  get_option('amzn_lwa_client_id');
    $accessKey =  get_option('amzn_access_key'); 
    $secretKey =  get_option('amzn_secret_key');
    $returnURL = ($args["returnurl"] != '') ? $args["returnurl"] : get_option('amzn_return_url');

	$parameters = array('returnURL'=> $returnURL,
		'accessKey'=>  $accessKey,
		'lwaClientId'=> $lwaClientId,
		'sellerId'=> $sellerId,
		'sellerNote'=> $args["note"],
		'currencyCode'=> 'USD',
		'amount'=> $args["amount"]); 

   uksort($parameters, 'strcmp');
   $secretKey = $secretKey;	
   $signature = _urlencode(_signParameters($parameters, $secretKey));
	 $parameters['signature'] = $signature;

   $button = "<script type='text/javascript' src='https://static-na.payments-amazon.com/OffAmazonPayments/us/sandbox/js/Widgets.js'></script>
    <div id='AmazonPayButton'></div>
    <script type='text/javascript'>
        OffAmazonPayments.Button('AmazonPayButton', 'A1GV76EFH0T2Y7', {
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
