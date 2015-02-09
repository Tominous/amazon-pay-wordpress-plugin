<?php
add_action('admin_menu', 'amzn_plugin_settings');
function amzn_plugin_settings() {
    add_menu_page('Amzn Settings', 'Amzn Settings', 'administrator', 'amzn_settings', 'amzn_display_settings');
}

function amzn_display_settings() {
    $sellerId = (get_option('amzn_seller_id') != '') ? get_option('amzn_seller_id') : 'A1GV76EFH0T2Y7';
    $lwaClientId = (get_option('amzn_lwa_client_id') != '') ? get_option('amzn_lwa_client_id') : 'amzn1.application-oa2-client.d18b994319bc4c1aa4c79e35995fc2cb';
    $accessKey = (get_option('amzn_access_key') != '') ? get_option('amzn_access_key') : 'AKIAIXYYATQC6HR75ZWA';
    $secretKey = (get_option('amzn_secret_key') != '') ? get_option('amzn_secret_key') : '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';
    $returnURL = (get_option('amzn_return_url') != '') ? get_option('amzn_return_url') : 'https://54.201.197.139';
    $html = '</pre>
			<div class="wrap"><form action="options.php" method="post" name="options">
			<h2>Select Your Settings</h2>
			' . wp_nonce_field('update-options') . '
			<table class="form-table" width="100%" cellpadding="10">
			<tbody>
			<tr>
			<td scope="row" align="left">
			 <label>LWA client ID</label><input type="text" style="width:600px;" name="amzn_lwa_client_id" value="' . $lwaClientId . '" /></td>
			</tr>
			<tr>
			<td scope="row" align="left">
			 <label>Seller ID</label><input type="text" name="amzn_seller_id" value="' . $sellerId . '" /></td>
			</tr>
			<tr>
			<td scope="row" align="left">
			 <label>MWS access key</label><input type="text" style="width:600px;" name="amzn_access_key" value="' . $accessKey . '" /></td>
			</tr>
			<tr>
			<td scope="row" align="left">
			 <label>MWS secret key</label><input type="text" style="width:600px;" name="amzn_secret_key" value="' . $secretKey . '" /></td>
			</tr>
			<tr>
			<td scope="row" align="left">
			 <label>Default return URL</label><input type="text" name="amzn_return_url" value="' . $returnURL . '" /></td>
			</tr>
			</tbody>
			</table>
			 <input type="hidden" name="action" value="update" />
			 <input type="hidden" name="page_options" value="amzn_seller_id,amzn_lwa_client_id,amzn_access_key,amzn_secret_key,amzn_return_url" />
			 <input type="submit" name="Submit" value="Update" /></form></div>
			<pre>';
    echo $html;
}
?>
