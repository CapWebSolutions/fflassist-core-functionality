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

function get_ffl_assist_custom_bc_meta( $user_id ) {

	// Set user to current one if id not provided.
	if ( !$user_id ) $user_id = wp_get_current_user();

	// Set dummy return values
	$bc_meta = [];
	$bc_meta['bc_user_id'] = 'user-id' . $user_id;
	$bc_meta['bc_tenant_id'] = 'tenant-id';
	$bc_meta['bc_database'] = 'bc-database';
	$bc_meta['bc_logon_url'] = 'bc-logon-url';

	$result = rwmb_meta( 'bc_user_id', [ 'object_type' => 'user' ], $user_id );
	$result .= '||';
	$result = rwmb_meta( 'bc_tenant_id', [ 'object_type' => 'user' ], $user_id );
	$result .= '||';
	$result = rwmb_meta( 'bc_database', [ 'object_type' => 'user' ], $user_id );
	$result .= '||';
	$result = rwmb_meta( 'bc_logon_url', [ 'object_type' => 'user' ], $user_id );
	
	return $result;
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
	// var_dump($menu_items);
	do_action( 'qm/alert', 'Menu Items ' . $menu_items );
    // Initialize a flag to check if 'FFL Assist' is found
    $ffl_assist_found = false;

	$url_start = 'https://businesscentral.dynamics.com/';
	if ( $bc_meta['bc_tenant_id'] ) $tenant_id = $bc_meta['bc_tenant_id'] . '/';
	if ( $bc_meta['bc_database'] ) $tenant_db = $bc_meta['bc_database'];
	if ( $bc_meta['bc_user_id'] ) $url_end = '?user_id=' . $bc_meta['bc_user_id'];
	$url_full = $url_start . $tenant_id . $tenant_db . $url_end;

	// print_r( (object)
	// 	[
	// 		'file' => __FILE__,
	// 		'method' => __METHOD__,
	// 		'line' => __LINE__,
	// 		'dump' => [
	// 			$url_full,
	// 		],
	// 	], true );

		// Traverse through the menu items and replace the placeholder with the updated link
    foreach ($menu_items as $menu_item) {
        if ($menu_item->title == 'FFL Assist') {
            // Update the URL if 'FFL Assist' is found
            $menu_item->url = $url_full;
            wp_update_nav_menu_item($menu->term_id, $menu_item->ID, array(
                'menu-item-url' => $menu_item->url,
            ));
			do_action( 'qm/alert', 'Found & replaced menu mtem ' );
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

// Function to create the shortcode
function ffl_assist_shortcode($atts) {
    // Extract the 'userid' attribute
    $atts = shortcode_atts(
        array(
            'userid' => '',
        ), $atts, 'ffl_assist'
    );
    return get_ffl_assist_custom_bc_meta($atts['userid']);
}

// Add the shortcode
add_shortcode('ffl_assist', __NAMESPACE__ . '\ffl_assist_shortcode');

function display_user_meta_listing(){
	ob_start();
	?>
	<table id="Userslist">
		<thead>
			<tr>
				<th>Login</th>
				<th>Email</th>
				<th>BC User ID</th>
				<th>BC Tenant ID</th>
				<th>BC Database</th>
			</tr>
		</thead>
		<tbody> 
			<?php
			$users = get_users();
			foreach ($users as $user) { $user_id = $user->ID; ?>
				<tr>
					<td><?php echo $user->display_name; ?></td>
					<td><?php echo $user->user_email; ?></td>
					<td><?php echo rwmb_meta( 'bc_user_id', [ 'object_type' => 'user' ], $user_id ); ?></td>
					<td><?php echo rwmb_meta( 'bc_tenant_id', [ 'object_type' => 'user' ], $user_id ); ?></td>
					<td><?php echo rwmb_meta( 'bc_database', [ 'object_type' => 'user' ], $user_id ); ?></td>
				</tr>
				<tr>
					<td></td><td></td><td colspan="3"><?php echo "Logon URL: " . rwmb_meta( 'bc_logon_url', [ 'object_type' => 'user' ], $user_id ); ?></td>
				</tr>
			<?php } ?>
		</tbody>
		<tfoot>
			<tr>
				<th>Login</th>
				<th>Email</th>
				<th>BC User ID</th>
				<th>BC Tenant ID</th>
				<th>BC Database</th>
			</tr>
		</tfoot>
	</table>
	<?php
	$result = ob_get_contents();
	ob_end_clean();
	return $result;
}
add_shortcode('ffl_user_meta', __NAMESPACE__ . '\display_user_meta_listing');

/**
 * Call Update Quick Links Menu On Login
 * 
 * Update the Quick Linmks navigation menu with personalized BC system login
 *
 * @param [type] $user_login
 * @param [type] $user
 * @return void
 */
function call_update_quick_links_menu_on_login($user_login, $user) {
    // Check if the user has the role of 'subscriber'
    if (in_array('subscriber', (array) $user->roles)) {
        // Check if the function has already been called during this session
        // if (!get_user_meta($user->ID, 'quick_links_menu_updated', true)) {
            // Call the update_quick_links_menu function
            update_quick_links_menu( $user->ID );

            // Set a user meta to indicate the function has been called
            // update_user_meta($user->ID, 'quick_links_menu_updated', true);
        // }
    }
}
add_action('wp_login', __NAMESPACE__ . '\call_update_quick_links_menu_on_login', 10, 2);

function reset_quick_links_menu_flag($user_id) {
    // Reset the flag when the user logs out
    delete_user_meta($user_id, 'quick_links_menu_updated');
}
add_action('wp_logout', __NAMESPACE__ . '\reset_quick_links_menu_flag');

function greeting() {
    echo '<h1>HIYA!</h1>';
}

function subscriber_login_greeting($user_login, $user) {
    // Check if the user has the role of 'subscriber'
    if (in_array('subscriber', (array) $user->roles)) {
        // Call the greeting function
        greeting();
    }
}
add_action('wp_login', __NAMESPACE__ . '\subscriber_login_greeting', 10, 2);
