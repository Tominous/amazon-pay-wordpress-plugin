<?php
/**
 * Pay with Amazon
 *
 * @category Amazon
 * @package Amazon_Login
 * @copyright Copyright (c) 2015 Amazon.com
 * @license http://opensource.org/licenses/Apache-2.0 Apache License, Version 2.0
 */

// adding a page
$new_page_title = 'Purchase Confirmation';
$new_page_content = '[amzn-thank-you]';
$new_page_template = ''; 
//ex. template-custom.php. Leave blank if you don't want a custom page template.

$page_check = get_page_by_path('/amzn-thank-you');
$new_page = array(
	'post_type' => 'page',
	'post_title' => $new_page_title,
	'post_name' => 'amzn-thank-you',
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

function set_html_content_type() {
		return 'text/html';
}

add_shortcode("amzn-thank-you", "thank_you_page_handler");
function thank_you_page_handler($args) {
	$resultCode = get_query_var('resultCode');
	$amount = get_query_var('amount');
	if($resultCode == 'Success'){
		if (get_option('amzn_email_notn') == 'enabled')
		{
			$email = get_option('amzn_email');
			$message = "A payment was completed on " . get_home_url() . " at " . current_time('mysql') . " using Pay with Amazon. Please login to <a href='https://sellercentral.amazon.com/'>Seller Central</a> in order to see the payment details including the buyer's email and address information.";
			//$headers = array('Content-Type: text/html; charset=UTF-8');
			add_filter( 'wp_mail_content_type', 'set_html_content_type' );
			wp_mail($email, 'Payment Completed Notification', $message );
			remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
		}
		return "Thank you for your purchase."; 
	}
	else
	{
		return "Sorry. Something went wrong."; 
	}
}
?>
