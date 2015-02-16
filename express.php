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

// shortcode for success page
add_filter('query_vars', 'add_my_var');
function add_my_var($public_query_vars) {
	$public_query_vars[] = 'resultCode';
	return $public_query_vars;
}

add_shortcode("success-page", "success_page_handler");
function success_page_handler($args) {
    return "resultCode" . get_query_var('resultCode');
}
// END shortcode for success page

// adding a page
$new_page_title = 'success';
$new_page_content = 'custom content';
$new_page_template = ''; 
//ex. template-custom.php. Leave blank if you don't want a custom page template.

//don't change the code bellow, unless you know what you're doing
$page_check = get_page_by_title($new_page_title);
$new_page = array(
        'post_type' => 'page',
        'post_title' => $new_page_title,
        'post_content' => $new_page_content,
        'post_status' => 'publish',
        'post_author' => 1,
);
if(!isset($page_check->ID)){
        $new_page_id = wp_insert_post($new_page);
        if(!empty($new_page_template)){
                update_post_meta($new_page_id, '_wp_page_template', $new_page_template);
        }
}
// END adding a page


add_shortcode("express-pay", "express_pay_handler");
function express_pay_handler($args) {
    $sellerId =  get_option('amzn_seller_id');
    $lwaClientId =  get_option('amzn_lwa_client_id');
    $accessKey =  get_option('amzn_access_key'); 
    $secretKey =  get_option('amzn_secret_key');
    $returnURL = ($args["returnurl"] != '') ? $args["returnurl"] : get_option('amzn_return_url');
		$note = ($args["note"] != '') ? $args["note"] : "note"; 
		$amount = ($args["amount"] != '') ? $args["amount"] : "1"; 

	$parameters = array('returnURL'=> $returnURL,
		'accessKey'=>  $accessKey,
		'lwaClientId'=> $lwaClientId,
		'sellerId'=> $sellerId,
		'sellerNote'=> $note,
		'currencyCode'=> 'USD',
		'amount'=> $amount); 

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
