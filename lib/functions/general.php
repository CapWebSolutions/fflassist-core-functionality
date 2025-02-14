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


/**
 * call_update_quick_links_menu_on_login
 *
 * @param [type] $user_login
 * @param [type] $user
 * @return void
 */
function call_update_quick_links_menu_on_login($user_login, $user) {
    // Check if the user has the role of 'subscriber'
    if (in_array('subscriber', (array) $user->roles)) {
        // Check if the function has already been called during this session
        if (!get_user_meta($user->ID, 'quick_links_menu_updated', true)) {
            // Call the update_quick_links_menu function
            update_quick_links_menu( $bc_meta);

            // Set a user meta to indicate the function has been called
            update_user_meta($user->ID, 'quick_links_menu_updated', true);
        }
    }
}
add_action('wp_login', __NAMESPACE__ . '\call_update_quick_links_menu_on_login', 10, 2);

function reset_quick_links_menu_flag($user_id) {
    // Reset the flag when the user logs out
    delete_user_meta($user_id, 'quick_links_menu_updated');
}
add_action('wp_logout', __NAMESPACE__ . '\reset_quick_links_menu_flag');

