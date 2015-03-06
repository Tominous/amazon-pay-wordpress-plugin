<?php
/**
 * Pay with Amazon
 *
 * @category Amazon
 * @package Amazon_Login
 * @copyright Copyright (c) 2015 Amazon.com
 * @license http://opensource.org/licenses/Apache-2.0 Apache License, Version 2.0
 */

function get_button_options(){
	$sbscb_btn = '[express-pay note="purchase on ' . get_home_url() . '" amount=1 ]';
	$stscb_btn_img = plugins_url( 'image.png', __FILE__ ) ;
	echo '<script type="text/javascript">'."\n";
	echo "var sc_text = '". $sbscb_btn ."'\n";
	echo "var sc_img = '". $stscb_btn_img ."'\n";
	echo '</script>'."\n";
}

if(preg_match("/(post-new|post)\.php/", basename(getenv('SCRIPT_FILENAME')))){
	add_filter('admin_head', 'get_button_options');
}

add_filter('mce_external_plugins', "amznexpressplugin_register");
add_filter('mce_buttons', 'amznexpressplugin_add_button', 0);

function amznexpressplugin_add_button($button){
    array_push($button, "", "amznexpressplugin");
    return $button;
}

function amznexpressplugin_register($plugin_array){
    $plugin_array['amznexpressplugin'] = plugins_url( 'express.js', __FILE__ );
    return $plugin_array;
}

function wp_gear_manager_admin_scripts() {
	wp_enqueue_script('media-upload');
	wp_enqueue_script('thickbox');
	wp_enqueue_script('jquery');
}
add_action('admin_print_scripts', 'wp_gear_manager_admin_scripts');

function wp_gear_manager_admin_styles() {
	wp_enqueue_style('thickbox');
}
add_action('admin_print_styles', 'wp_gear_manager_admin_styles');

?>
