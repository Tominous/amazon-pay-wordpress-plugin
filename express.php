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

include(dirname(__FILE__) . '/settings.php');
include(dirname(__FILE__) . '/editor.php');
include(dirname(__FILE__) . '/shortcodes/amzn-thank-you.php');
include(dirname(__FILE__) . '/shortcodes/express-pay.php');

// shortcode for success page
add_filter('query_vars', 'add_my_var');
function add_my_var($public_query_vars) {
	$public_query_vars[] = 'resultCode';
	$public_query_vars[] = 'amount';
	return $public_query_vars;
}

?>
