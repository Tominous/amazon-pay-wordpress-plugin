<?php
/**
 * Pay with Amazon
 *
 * @category Amazon
 * @package Amazon_Login
 * @copyright Copyright (c) 2015 Amazon.com
 * @license http://opensource.org/licenses/Apache-2.0 Apache License, Version 2.0
 */

include(dirname(__FILE__) . '/MarketplaceWebServiceSellers/KeycheckClient.php');
include(dirname(__FILE__) . '/MarketplaceWebServiceSellers/Model/ListMarketplaceParticipationsRequest.php');
  

add_action('admin_menu', 'amzn_plugin_settings');
function amzn_plugin_settings() {
    add_menu_page('Pay with Amazon Express', 'Pay with Amazon Express', 'administrator', 'amzn_settings', 'amzn_display_settings');
}

function amzn_display_settings() {
    $sellerId = get_option('amzn_seller_id', '');
    $lwaClientId = get_option('amzn_lwa_client_id', '');
    $accessKey = get_option('amzn_access_key', '');
    $secretKey = get_option('amzn_secret_key', '');
    $email = get_option('amzn_email', '');
    $emailNotn = (get_option('amzn_email_notn') == 'enabled') ? 'checked' : '';

		$error = '';
		$config = array (
			'ServiceURL' => "https://mws.amazonservices.com/Sellers/2011-07-01",
			'ProxyHost' => null,
			'ProxyPort' => -1,
			'ProxyUsername' => null,
			'ProxyPassword' => null,
			'MaxErrorRetry' => 3,
		);

		$service = new MarketplaceWebServiceSellers_Client(
			$accessKey,
			$secretKey,
			'Login and Pay for Wordpress',
			'1.3',
			$config);

		$request = new MarketplaceWebServiceSellers_Model_ListMarketplaceParticipationsRequest();
		$request->setSellerId($sellerId);
		try {
			$service->ListMarketplaceParticipations($request);
			if(strpos($lwaClientId, "amzn1.application")!==false && strpos($lwaClientId, "client")===false) {
				$error = '<font color="red">It seems like you used your LWA App ID instead of LWA Client ID.</font>';
			}
			else {
				$error = '<font color="green">All of your Amazon API keys are correct!</font>';
			}
		}
		catch (MarketplaceWebServiceSellers_Exception $ex) {
			if ($ex->getErrorCode() == 'InvalidAccessKeyId'){
				$error = '<font color="red">The MWS Access Key is incorrect</font>';
			}
			else if ($ex->getErrorCode() == 'SignatureDoesNotMatch'){
				$error='<font color="red">The MWS Secret Key is incorrect</font>';
			}
			else if ($ex->getErrorCode() == 'InvalidParameterValue'){
				$error='<font color="red">The Seller/Merchant ID is incorrect</font>';
			}
			else if ($ex->getErrorCode() == 'AccessDenied') {
				$error = '<font color="red">The Seller/Merchant ID does not match the MWS keys provided</font>';
			}
			else{
				$error = '<font color="red">Unknown error</font>';
			}
		}


		$adminpage = get_page_by_path('/amzn-thank-you');
		$editorImg = plugins_url( 'images/editor.png', __FILE__ ) ;

    $html = '</pre>
			<div class="wrap"><form action="options.php" method="post" name="options">
			<h2>Select Your Settings</h2>
			<div>1) Set up your account keys <a target="_blank" href="https://sellercentral.amazon.com/hz/me/sp/signup?solutionProviderOptions=lwa%3Bmws-acc%3B&marketplaceId=AGWSWK15IEJJ7&solutionProviderToken=AAAAAQAAAAEAAAAQfpDKmD23VHUqpxUzH0HW%2FAAAAHCQ0U6VJYwOBHfIn0L1TqmSibRl4%2BW4SbPymnIF7NhCXOgIw3%2BgSRNLcFmRX%2FyuTRChUnU8F4AuKoacZG2wKPaqYSSD7WmQz%2FMUDXxnXZTE%2Fr2w1GH3EYl7DC7nkD3b4l2ot7X1%2BXsHsrFDg6%2FWTIb8&solutionProviderId=A3D68VL23XMOV2">here.</a></div>
			<div>2) Add the button to any page by clicking the Amazon logo in the visual page editor.</div>
			<div><img src="' . $editorImg . '"></div>
			<div>3) Modify the Thank You page customers see when they complete checkout <a href="post.php?post=' . $adminpage->ID . '&action=edit">here.</a></div>
			' . wp_nonce_field('update-options') . '
			<table class="form-table" width="100%" cellpadding="10">
			<h3>' . $error . '</h3>
			<colgroup>
			<col span="1" style="width: 15%;">
			<col span="1" style="width: 85%;">
			</colgroup>
			<tbody>
			<tr>
			  <td scope="row" style="width:600px;" align="left">
					Seller ID
				</td>
			  <td>
					<input type="text" style="width:600px;" name="amzn_seller_id" value="' . $sellerId . '" />
				</td>
			</tr>
			<tr>
			  <td>
					MWS access key
				</td>
			  <td>
					<input type="text" style="width:600px;"  name="amzn_access_key" value="' . $accessKey . '" />
				</td>
			</tr>
			<tr>
				<td>
					MWS secret key
				</td>
				<td>
					<input type="text" style="width:600px;" name="amzn_secret_key" value="' . $secretKey . '" />
				</td>
			</tr>
			<tr>
				<td>
					LWA client ID
				</td>
				<td>
				  <input type="text" style="width:600px;" name="amzn_lwa_client_id" value="' . $lwaClientId . '" />
				</td>
			</tr>
			<tr>
				<td>
					Enable email notifications?
				</td>
				<td>
					<input type="checkbox" name="amzn_email_notn" value="enabled" ' . $emailNotn . '/>
				</td>
			</tr>
			<tr>
				<td>
					Email address
				</td>
				<td>
					<input type="text"  style="width:600px;" name="amzn_email" value="' . $email . '" />
				</td>
			</tr>
			</tbody>
			</table>
			<input type="hidden" name="action" value="update" />
			<input type="hidden" name="page_options" value="amzn_seller_id,amzn_lwa_client_id,amzn_access_key,amzn_secret_key,amzn_return_url,amzn_email_notn,amzn_email" />
			<input type="submit" name="Submit" value="Update" /></form></div>
			<pre>';
    echo $html;
}
?>
