<?php
/**
 * Manage User FFL Meta
 *
 * This file contains any functions to manage the custom user level FFL-related meta data. 
 *
 * @package      Core_Functionality
 * @since        1.1.0
 * @link         https://github.com/capwebsolutions/fflassist-core-functionality
 * @author       Matt Ryan <matt@capwebsolutions.com>
 * @copyright    Copyright (c) 2024, Matt Ryan
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
namespace capweb;

function create_ffl_assist_nav_item() {

}

function get_ffl_assist_custom_bc_meta( $user_id, $bc_meta ) {

	// Exit false if no user_id provided or if user_id not valid
	if ( !$user_id ) return false;

	// Set return values
	$bc_meta['bc_user_id'] = 'user-id';
	$bc_meta['bc_tenant_id'] = 'tenant-id';
	$bc_meta['bc_database'] = 'bc-database';
	error_log( print_r( (object)
		[
			'file' => __FILE__,
			'method' => __METHOD__,
			'line' => __LINE__,
			'dump' => [
				$bc_meta,
			],
		], true ) );
	return;
}

function create_nav_item( $bc_meta ) {

	// We need to add this custom entry to x different nav menu locations
	//  header account link
	//  footer Quick Link | FFL Assist System

	// Check if the menu exists
	$menu_name   = 'Quick Links';
	$menu_exists = wp_get_nav_menu_object( $menu_name );

	// If it doesn't exist, let's exit with code 'QL0'.
	if ( ! $menu_exists ) return 'QL0';
	
	// Menu does exist, find 'FFL Assist System' nav item
	{
		$menu_id = wp_create_nav_menu($menu_name);

		// Set up default menu items
		wp_update_nav_menu_item( $menu_id, 0, array(
			'menu-item-title'   =>  __( 'Home', 'textdomain' ),
			'menu-item-classes' => 'home',
			'menu-item-url'     => home_url( '/' ), 
			'menu-item-status'  => 'publish'
		) );

		wp_update_nav_menu_item( $menu_id, 0, array(
			'menu-item-title'  =>  __( 'Custom Page', 'textdomain' ),
			'menu-item-url'    => home_url( '/custom/' ), 
			'menu-item-status' => 'publish'
		) );
	}
}

function update_quick_links_menu( $bc_meta ) {
    // Get the menu object by name
    $menu_name = 'Quick Links';
    $menu = wp_get_nav_menu_object($menu_name);

    // Check if the menu exists
    if (!$menu) {
        return 'QL0';
    }

    // Get the menu items
    $menu_items = wp_get_nav_menu_items($menu->term_id);

    // Initialize a flag to check if 'FFL Assist' is found
    $ffl_assist_found = false;

	$url_start = 'https://businesscentral.dynamics.com/';
	if ( $bc_meta['bc_tenant_id'] ) $tenant_id = $bc_meta['bc_tenant_id'] . '/';
	if ( $bc_meta['bc_database'] ) $tenant_db = $bc_meta['bc_database'];
	if ( $bc_meta['bc_user_id'] ) $url_end = '?user_id=' . $bc_meta['bc_user_id'];
	$url_full = $url_start . $tenant_id . $tenant_db . $url_end;

	error_log( print_r( (object)
		[
			'file' => __FILE__,
			'method' => __METHOD__,
			'line' => __LINE__,
			'dump' => [
				$url_full,
			],
		], true ) );

		// Traverse through the menu items and replace the placeholder with the updated link
    foreach ($menu_items as $menu_item) {
        if ($menu_item->title == 'FFL Assist') {
            // Update the URL if 'FFL Assist' is found
            $menu_item->url = $url_full;
            wp_update_nav_menu_item($menu->term_id, $menu_item->ID, array(
                'menu-item-url' => $menu_item->url,
            ));
            $ffl_assist_found = true;
            break;
        }
    }

    // If 'FFL Assist' is not found, add it to the end of the menu
    if (!$ffl_assist_found) {
        wp_update_nav_menu_item($menu->term_id, 0, array(
            'menu-item-title' => 'FFL Assist',
            'menu-item-url' => $url_full,	
            'menu-item-status' => 'publish',
        ));
    }
}

// Call the function
// get_ffl_assist_custom_bc_meta();
// $user_id = '1';
// $bc_meta = '';
// get_ffl_assist_custom_bc_meta( $user_id, $bc_meta );

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
            update_quick_links_menu();

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

