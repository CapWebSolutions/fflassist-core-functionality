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
// namespace capweb;

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
// add_filter( 'logout_redirect','logout_redirect', 10, 3 );

add_filter( 'kadence_blocks_pro_query_loop_query_vars', function( $query, $ql_query_meta, $ql_id ) {

    // if ( $ql_id == 24170 ) {
    //    $query['tax_query'] = array(
    //       array(
    //          'taxonomy' => 'category',
    //          'field' => 'slug',
    //          'terms' => 'subscriber',
    //       )
    //    );
    // }
    // if ( $ql_id == 24170 ) {
    //   $query['category__not_in'] = 37; //Subscriber category	
    // }
    // if ( $ql_id == 24254 ) {
    //     $query['category_in'] = 37; //Subscriber category	
    // }
    if ( $ql_id == 24620 ) {  // query = Subscriber Category Query
        error_log( '$ql_id ' . var_export( $ql_id, true ) );
        $query['category__not_in'] = array( 1, 18, 35, 36, 38, 37) ; //Subscriber category	
        $query['posts_per_page'] = -1;
    }

    return $query;
 }, 10, 3 );
 // Ref: https://www.kadencewp.com/help-center/docs/kadence-blocks/custom-queries-for-advanced-query-loop-block/

 /**
 *  Add nonce to logout URL in navigation
 *  @link https://barebones.dev/articles/add-wordpress-nonce-to-log-out-link/
 * 
 *  This snippet handles issues with the logout link in the navigation menu. 
 *   Without it the visitor is presented with a confirmation screen to log out.  
 */

function cws_add_logout_url_nonce($items){
    foreach($items as $item){
        error_log( print_r( (object)
            [
                'file' => __FILE__,
                'method' => __METHOD__,
                'line' => __LINE__,
                'dump' => [
                    $item,
                ],
            ], true ) );
      if( $item->url == '/wp-login.php?action=logout'){
           $item->url = $item->url . '?redirect_url=/&_wpnonce=' . wp_create_nonce( 'log-out' );
      }
    }
    return $items;
  
  }
  add_filter('wp_nav_menu_objects', 'cws_add_logout_url_nonce', 11 );