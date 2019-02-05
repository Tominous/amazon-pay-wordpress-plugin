<?php
/*
Plugin Name: Amazon Pay Express Checkout
Plugin URI: https://github.com/amzn/amazon-pay-wordpress-plugin
Description:  A simple WordPress extension that lets you add an Amazon Pay Express Checkout button to your site. Configure your settings <a href="admin.php?page=amzn_settings">here</a>. 
Version: 1.1
Author: Amazon Pay
Author URI: http://payments.amazon.com
*/

/*
Amazon Express Checkout (WordPress Plugin)
*/

/**
 * Amazon Pay
 *
 * @category Amazon
 * @package Amazon_Login
 * @copyright Copyright (c) 2015 Amazon.com
 * @license http://opensource.org/licenses/Apache-2.0 Apache License, Version 2.0
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
