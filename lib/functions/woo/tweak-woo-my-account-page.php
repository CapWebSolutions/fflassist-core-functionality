<?php
/**
 * WooCommerce Customize My Account page. 
 *
 * This file adds customizations to the My Account page. 
 *
 * @package      Core_Functionality
 * @since        1.0.0
 * @link         https://github.com/capwebsolutions/fflassist-core-functionality
 * @author       Matt Ryan <matt@capwebsolutions.com>
 * @copyright    Copyright (c) 2025, Matt Ryan
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */


/**
 * @snippet       Remove My Account Menu Links
 * @author        Misha Rudrastyh
 * @url           https://rudrastyh.com/woocommerce/my-account-menu.html#remove_items
 */
add_filter( 'woocommerce_account_menu_items', 'capweb_remove_my_account_links' );
function capweb_remove_my_account_links( $menu_links ){
	
	unset( $menu_links[ 'edit-address' ] ); // Addresses
	//unset( $menu_links[ 'dashboard' ] ); // Remove Dashboard
	//unset( $menu_links[ 'payment-methods' ] ); // Remove Payment Methods
	//unset( $menu_links[ 'orders' ] ); // Remove Orders
	//unset( $menu_links[ 'downloads' ] ); // Disable Downloads
	//unset( $menu_links[ 'edit-account' ] ); // Remove Account details tab
	//unset( $menu_links[ 'customer-logout' ] ); // Remove Logout link
	
	return $menu_links;
	
}

 /**
 * @snippet       Hide Downloads Tab @ My Account Page
 * @how-to        businessbloomer.com/woocommerce-customization
 * @author        Rodolfo Melogli, Business Bloomer
 * @compatible    WooCommerce 9
 * @community     https://businessbloomer.com/club/
 */
 
// add_filter( 'woocommerce_account_menu_items','capweb_hide_downloads_tab_my_account', 9999 );
 
function capweb_hide_downloads_tab_my_account( $items ) {
   $downloads = ! empty( WC()->customer ) ? WC()->customer->get_downloadable_products() : false;
   $has_downloads = (bool) $downloads;
   if ( ! $has_downloads ) unset( $items['downloads'] );
   return $items;
}

/**
* @snippet       WooCommerce Add New Tab @ My Account
* @how-to        businessbloomer.com/woocommerce-customization
* @author        Rodolfo Melogli, Business Bloomer
* @compatible    WooCommerce 5.0
* @community     https://businessbloomer.com/club/
*/
 
// ------------------
// 1. Register new endpoint (URL) for My Account page
// Note: Re-save Permalinks or it will give 404 error
 
function capweb_add_fflassist_portal_endpoint() {
  add_rewrite_endpoint( 'fflassist-portal', EP_ROOT | EP_PAGES );
}

add_action( 'init','capweb_add_fflassist_portal_endpoint' );

// ------------------
// 2. Add new query var

function capweb_fflassist_portal_query_vars( $vars ) {
  $vars[] = 'fflassist-portal';
  return $vars;
}

add_filter( 'query_vars','capweb_fflassist_portal_query_vars', 0 );

// ------------------
// 3. Insert the new endpoint into the My Account menu

function capweb_add_fflassist_portal_link_my_account( $items ) {
  $items['fflassist-portal'] = 'FFLAssist Portal';
  return $items;
}

add_filter( 'woocommerce_account_menu_items','capweb_add_fflassist_portal_link_my_account' );

// ------------------
// 4. Add content to the new tab. This function is also used to print button elsewhere on My Account. 

function capweb_fflassist_portal_content() {
  $url = rwmb_meta( 'bc_logon_url', [ 'object_type' => 'user' ], get_current_user_id() );
  if ( empty( $url ) ) {
    $url = home_url( '/contact?logon-url-not-set' );
  }
  ?>
  <a class="button kb-btn-global-inherit" href="<?php echo esc_url( $url ); ?>" target='_blank' rel="noopener noreferrer"><?php esc_html_e( 'Access FFLAssist Portal Here', 'fflassist-core-functionality' ); ?></a>
  <?php
}
add_action( 'woocommerce_account_fflassist-portal_endpoint', 'capweb_fflassist_portal_content' );

/**
* @snippet       Reorder tabs @ My Account
* @how-to        businessbloomer.com/woocommerce-customization
* @author        Rodolfo Melogli, Business Bloomer
* @compatible    WooCommerce 6
* @community     https://businessbloomer.com/club/
*/

add_filter( 'woocommerce_account_menu_items', 'capweb_add_link_my_account' );

function capweb_add_link_my_account( $items ) {
   $newitems = array(
     'dashboard' => __( 'Dashboard', 'fflassist-core-functionality' ),
     'edit-account' => __( 'Account details', 'fflassist-core-functionality' ),
     'orders' => __( 'Order History', 'fflassist-core-functionality' ),
     'subscriptions' => __( 'Subscriptions', 'fflassist-core-functionality' ),
     'payment-methods' => __( 'Payment Methods', 'fflassist-core-functionality' ),
     'fflassist-portal' => __( 'FFLAssist Portal', 'fflassist-core-functionality' ),
     'customer-logout' => __( 'Logout', 'fflassist-core-functionality' )
   ); 
   return $newitems;
}

