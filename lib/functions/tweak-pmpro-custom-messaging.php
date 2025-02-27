<?php 
/**
 * Tweak PMPro Custom Messaging
 *
 * This file contains any functions that tweak the PMPro Custom Messaging
 *
 * @package      FFL_Assist_Core_Functionality
 * @since        1.0.0
 * @link
 */
// namespace capweb;

/**
 * Filter the header of the restricted message shown on protected content.
 * 
 * title: Filter the header of the restricted message shown on protected content.
 * layout: snippet
 * collection: restricting-content
 * category: content, restriction, non-member
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_custom_no_access_message_header( $header, $level_ids ) {
	$header = "FFLAssist Subscriber Only Content";
	return $header;
}
add_filter( 'pmpro_no_access_message_header', 'my_pmpro_custom_no_access_message_header', 10, 2 );


/**
 * Filter the body of the restricted message shown on protected content.
 * 
 * title: Filter the body of the restricted message shown on protected content.
 * layout: snippet
 * collection: restricting-content
 * category: content, restriction, non-member
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_custom_no_access_message_body( $body, $level_ids ) {
	$body = "<p>You must be a <a href='/assist-shop/'>subscriber</a> to access this content.</p>";
	return $body;
}
add_filter( 'pmpro_no_access_message_body','my_pmpro_custom_no_access_message_body', 10, 2 );


/**
 * Filter the login message shown on the login page.
 * 
 * title: Filter the login message shown on the login page.
 * layout: snippet
 * collection: restricting-content
 * category: content, restriction, non-member
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function capweb_custom_login_page_actions( $links ) {
	unset($links['register']);
	return $links;
}
apply_filters( 'pmpro_show_register_link', 'capweb_custom_login_page_actions' );
