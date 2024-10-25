<?php
/**
 * General
 *
 * This file contains any general functions
 *
 * @package      Core_Functionality
 * @since        1.1.0
 * @link         https://github.com/capwebsolutions/fflassist-core-functionality
 * @author       Matt Ryan <matt@capwebsolutions.com>
 * @copyright    Copyright (c) 2024, Matt Ryan
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
namespace capweb;

/**
 * Don't Update Plugin
 *
 * @since 1.0.0
 *
 * This prevents you being prompted to update if there's a public plugin
 * with the same name.
 *
 * @author Mark Jaquith
 * @link http://markjaquith.wordpress.com/2009/12/14/excluding-your-plugin-or-theme-from-update-checks/
 *
 * @param array  $r, request arguments
 * @param string $url, request url
 * @return array request arguments
 */
function core_functionality_hidden( $r, $url ) {
	if ( 0 !== strpos( $url, 'http://api.wordpress.org/plugins/update-check' ) ) {
		return $r; // Not a plugin update request. Bail immediately.
	}
	$plugins = unserialize( $r['body']['plugins'] );
	unset( $plugins->plugins[ plugin_basename( __FILE__ ) ] );
	unset( $plugins->active[ array_search( plugin_basename( __FILE__ ), $plugins->active ) ] );
	$r['body']['plugins'] = serialize( $plugins );
	return $r;
}
add_filter( 'http_request_args', __NAMESPACE__ . '\core_functionality_hidden', 5, 2 );

/** 
 * Move Yoast to the Bottom of editor screen
 */
function move_yoast_to_bottom() {
    return 'low';
}
add_filter( 'wpseo_metabox_prio', __NAMESPACE__ . '\move_yoast_to_bottom');


/**	
 * Redirect non-admin users to home page on logout. 
 */
function logout_redirect( $redirect_to, $requested_redirect, $user ) {
    if ( ! is_wp_error( $user ) && ! current_user_can( 'administrator' ) ) {
        // Redirect non-admin users to the home page after logout
        $redirect_to = home_url();
    }
    return $redirect_to;
}
add_filter( 'logout_redirect', __NAMESPACE__ . '\logout_redirect', 10, 3 );