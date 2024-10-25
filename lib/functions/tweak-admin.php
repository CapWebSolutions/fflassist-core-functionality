<?php
/**
 * Tweak and manage admin dashboard
 *
 * This file includes any customizations to the WordPress admin dash 
 *
 * @package      Core_Functionality
 * @since        1.0.0
 * @link         https://github.com/capwebsolutions/fflassist-core-functionality
 * @author       Matt Ryan <matt@capwebsolutions.com>
 * @copyright    Copyright (c) 2024, Matt Ryan
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
namespace capweb;

/* ********************** Contents **********************
  01. Change color of admin bar if using dev or staging site
  02. Customize Menu Order
  03. Remove Menu Items
  04. Customize Admin Bar Items
  05. Change Howdy in admin area.
  09. Add thumbnail column to post listing

  ******************End of Contents ******************** */


 /**
 * 01. Change color of admin bar if using dev site. 
 */
function devsite_admin_bar() {
	$site_url = site_url();
	// Local Dev site
	if ( strpos( $site_url, '.local' ) !== false || 
		strpos($site_url, '.test') !== false || 
		strpos($site_url, '.dev') !== false) {
			echo '<style>#wpadminbar { background-color: #996a29; }</style>';
		}

	// Live Staging site 
	if ( strpos( $site_url, 'staging.') !== false || 
		strpos($site_url, 'instawp.xyz') !== false) {
			echo '<style>#wpadminbar { background-color: #ff0000; }</style>';
		}
}
add_action('admin_head', __NAMESPACE__ . '\devsite_admin_bar');
add_action('wp_head', __NAMESPACE__ . '\devsite_admin_bar');



/**
 * 02. Customize Menu Order
 *
 * @since 1.0.0
 *
 * @param array $menu_ord. Current order.
 * @return array $menu_ord. New order.
 */
function custom_menu_order( $menu_ord ) {
	if ( ! $menu_ord ) { return true;
	}
	return array(
		'index.php', // this represents the dashboard link
		'edit.php?post_type=page', // the page tab
		'edit.php', // the posts tab
		'upload.php', // the media manager
		'plugins.php', // the media manager
		'tools.php', // the media manager
		'options-general.php', // the media manager
        'themes.php', // Appearance manager
		'users.php', // the media manager
	);
}
add_filter( 'custom_menu_order', __NAMESPACE__ . '\custom_menu_order' );
add_filter( 'menu_order', __NAMESPACE__ . '\custom_menu_order' );


/**
 * 03. Remove Menu Items
 *
 * @since 1.0.0
 *
 * Remove unused menu items by adding them to the array.
 * See the commented list of menu items for reference.
 */
function remove_menus() {
	global $menu;
	$restricted = array( __( 'Links' ) );
	// Example:
	// $restricted = array(__('Dashboard'), __('Posts'), __('Media'), __('Links'), __('Pages'), __('Appearance'), __('Tools'), __('Users'), __('Settings'), __('Comments'), __('Plugins'));
	end( $menu );
	while ( prev( $menu ) ) {
		$value = explode( ' ',$menu[ key( $menu ) ][0] );
		if ( in_array( $value[0] != null?$value[0]:'' , $restricted ) ) {unset( $menu[ key( $menu ) ] );}
	}
}
add_action( 'admin_menu', __NAMESPACE__ . '\remove_menus' );

/**
 * 04. Customize Admin Bar Items
 *
 * @since 1.0.0
 * @link http://wp-snippets.com/addremove-wp-admin-bar-links/
 */
function admin_bar_items() {
	global $wp_admin_bar;
	$wp_admin_bar->remove_menu( 'new-link', 'new-content' );
}
add_action( 'wp_before_admin_bar_render', __NAMESPACE__ . '\admin_bar_items' );


// Remove theme and plugin editor links
add_action( 'admin_init', __NAMESPACE__ . '\hide_editor_and_tools' );
function hide_editor_and_tools() {
	remove_submenu_page( 'themes.php','theme-editor.php' );
	remove_submenu_page( 'plugins.php','plugin-editor.php' );
}

// 05. Change Howdy in admin area. 

function change_howdy_greeting( $wp_admin_bar ) {
    $user_id = get_current_user_id();
    $user = get_userdata( $user_id );
    
    if ( $user ) {
        $greeting = 'Hello, you are logged in as ' . $user->display_name;
        $wp_admin_bar->add_menu( array(
            'id'    => 'my-account',
            'title' => $greeting,
        ) );
    }
}
add_action( 'admin_bar_menu', __NAMESPACE__ . '\change_howdy_greeting', 10 );

/**
 * 09. Add thumbnail column to post listing
 */

 add_image_size( 'admin-list-thumb', 80, 80, false );

 function add_thumbnail_columns( $columns ) {
        if ( !is_array( $columns ) )
            $columns = array();
        $new = array();
    
        foreach( $columns as $key => $title ) {
            if ( $key == 'title' ) // Put the Thumbnail column before the Title column
                $new['featured_thumb'] = __( 'Image');
            $new[$key] = $title;
        }
        return $new;
 }
 
 function add_thumbnail_columns_data( $column, $post_id ) {
     switch ( $column ) {
     case 'featured_thumb':
         echo '<a href="' . $post_id . '">';
         echo the_post_thumbnail( 'admin-list-thumb' );
         echo '</a>';
         break;
     }
 }

 /**
  * on_specific_admin_page
  *
  * @return boolean    True if we are on one of the selected admin pages, flase if not. 
  * @author Matt Ryan <matt@capwebsolutions.com>
  * @since  2024-02-21
  */
function on_specific_admin_page() {
 
    // Check if we are inside the WordPress administration interface
    if (is_admin()) {

        // Get the current page URL
        $current_url = $_SERVER['REQUEST_URI'];

        // Define the target URLs
        $target_urls = array(
            '/wp-admin/edit.php',    // post editor
            '/wp-admin/edit.php?post_type=testimonial'  // testimonials editor
        );

        // Check if the current URL matches any of the target URLs
        foreach ($target_urls as $url) {
            if ($current_url === $url) {
                return true;
            }
        }
    }

    return false;
}
    if ( function_exists( 'add_theme_support' ) && ( on_specific_admin_page() ) ) {
        add_filter( 'manage_posts_columns' , __NAMESPACE__ . '\add_thumbnail_columns' );
        add_action( 'manage_posts_custom_column' , __NAMESPACE__ . '\add_thumbnail_columns_data', 10, 2 );
    }

