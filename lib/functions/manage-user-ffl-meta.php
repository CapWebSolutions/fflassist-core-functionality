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
	if ( !$user_id ) $user_id = get_current_user_id();

	// Set dummy return values
	$bc_meta = [];
	$bc_meta['bc_user_id']   = rwmb_meta( 'bc_user_id', [ 'object_type' => 'user' ], get_current_user_id() );
	$bc_meta['bc_tenant_id'] = rwmb_meta( 'bc_tenant_id', [ 'object_type' => 'user' ], get_current_user_id() );
	$bc_meta['bc_database']  = rwmb_meta( 'bc_database', [ 'object_type' => 'user' ], get_current_user_id() );
	$bc_meta['bc_logon_url'] = rwmb_meta( 'bc_logon_url', [ 'object_type' => 'user' ], get_current_user_id() );
	if ( strlen( $bc_meta['bc_logon_url'] ) < 20 ) {
		// Set to default 
		$bc_meta['bc_logon_url'] = site_url( '/membership-account/system-status/', 'https:');
	}

	// $result = rwmb_meta( 'bc_user_id', [ 'object_type' => 'user' ], get_current_user_id() );
	// $result .= '|';
	// $result = rwmb_the_value( 'bc_tenant_id', [ 'object_type' => 'user' ], get_current_user_id() );
	// $result .= '|';
	// $result = rwmb_meta( 'bc_database', [ 'object_type' => 'user' ], get_current_user_id() );
	// $result .= '|';
	// $result = rwmb_meta( 'bc_logon_url', [ 'object_type' => 'user' ], get_current_user_id() );
	error_log( print_r( (object)
		[
			'file' => __FILE__,
			'method' => __METHOD__,
			'line' => __LINE__,
			'dump' => [
				$bc_meta,
			],
		], true ) );
	return $bc_meta;
}

function update_quick_links_menu( $user_id ) {
		// Get the menu object by name
    $menu_name = 'Quick Links';
    $menu = wp_get_nav_menu_object($menu_name);

    // Check if the menu exists
    if (!$menu) {
        return 'QL0';
    }

	// Get all the meta for current user and set the personal logon URL. 
	$bc_meta = get_ffl_assist_custom_bc_meta( $user_id );
	$url_full = $bc_meta['bc_logon_url'];

    // Get the menu items
    $menu_items = wp_get_nav_menu_items($menu->term_id);

    // Initialize a flag to check if 'FFL Assist' is found
    $needle_title_found = false;
	error_log( '$url_full ' . var_export( $url_full, true ) );

		// Traverse through the menu items and replace the placeholder with the updated link
	$needle_menu_item_title = 'FFL Assist System';
	foreach ($menu_items as $menu_item) {
		error_log( '$menu_item ' . var_export( $menu_item, true ) );
        if ($menu_item->title == $needle_menu_item_title ) {

            // Update the URL destination if target text found
            wp_update_nav_menu_item(
				$menu->term_id, 
				$menu_item->ID, 
				array(
					'menu-item-url' => $url_full,
				)
			);
            $needle_title_found = true;
            break;
        }
    }

    // If target text nav item is not found, add it to the end of the menu
    if (!$needle_title_found) {
		$menu_item_data = [];
		$menu_item_data['menu-item-title'] = $needle_menu_item_title;
		$menu_item_data['menu-item-url'] = $url_full;
		$menu_item_data['menu-item-status'] = 'publish';
		$menu_item_data['menu-item-type'] = 'custom';
		// 0 -> create new menu item. 
        wp_update_nav_menu_item( $menu->term_id, 0, $menu_item_data );
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

/**
 * My PMPro Account Profile Action Links
 *
 * Add new action link on Account Profile Page. 
 * @link https://github.com/strangerstudios/pmpro-snippets-library/blob/d5091b6d3e90939ecb06bac36d05db6a87f42252/frontend-pages/change-profile-action-links.php#L16
 * @param [type] $pmpro_profile_action_links
 * @return void
 */
function my_pmpro_account_profile_action_links( $pmpro_profile_action_links ) {

	// Set user id to current user.
	$user_id = get_current_user_id();
	$bc_meta = get_ffl_assist_custom_bc_meta( $user_id );

	// set your custom links here
	$my_edit_profile_url    = home_url( 'my-edit-profile-page-slug' );

	/* Uncomment the relative line by removing the comment ("//" double forward slash before the variable) if you would like to change the url for password reset or logout. */

	// $my_change_password_url = home_url( 'my-change-password-page-slug' );
	// $my_logout_url          = home_url( 'my-logout-page-slug' );

	if ( ! empty( $my_edit_profile_url ) ) {
		$pmpro_profile_action_links['edit-profile'] = sprintf( '<a id="pmpro_actionlink-profile" href="%s">%s</a>', esc_url( $my_edit_profile_url ), esc_html__( 'Edit Profile', 'paid-memberships-pro' ) );
	}

	if ( ! empty( $my_change_password_url ) ) {
		$pmpro_profile_action_links['change-password'] = sprintf( '<a id="pmpro_actionlink-change-password" href="%s">%s</a>', esc_url( $my_change_password_url ), esc_html__( 'Change Password', 'paid-memberships-pro' ) );
	}

	if ( ! empty( $my_logout_url ) ) {
		$pmpro_profile_action_links['logout'] = sprintf( '<a id="pmpro_actionlink-logout" href="%s">%s</a>', esc_url( $my_logout_url ), esc_html__( 'Log Out', 'paid-memberships-pro' ) );
	}

	// Pull out all personal FFL Assist BC login parts. 
	// $pmpro_profile_action_links['bc_user_id'] = sprintf( "BC User ID: %s", $bc_meta['bc_user_id'] );
	// $pmpro_profile_action_links['bc_tenant_id'] = sprintf( "BC Tenant ID: %s", $bc_meta['bc_tenant_id'] );
	// $pmpro_profile_action_links['bc_database_id'] =sprintf( "BC Database: %s", $bc_meta['bc_database_id'] );
	$pmpro_profile_action_links['bc_logon_url'] = sprintf(  '<a id="pmpro_actionlink-logon-url" href="%s">%s</a>', esc_url( $bc_meta['bc_logon_url'] ), esc_html__( 'FLL Assist System Logon', 'paid-memberships-pro' ) );

	return $pmpro_profile_action_links;

}
add_filter( 'pmpro_account_profile_action_links', __NAMESPACE__ . '\my_pmpro_account_profile_action_links' );