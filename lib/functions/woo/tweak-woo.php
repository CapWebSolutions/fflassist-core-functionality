<?php
/**
 * WooCommerce Tweaks
 *
 * This file includes any custom WooCommerce tweaks
 *
 * @package      Core_Functionality
 * @since        1.0.0
 * @link         https://github.com/capwebsolutions/fflassist-core-functionality
 * @author       Matt Ryan <matt@capwebsolutions.com>
 * @copyright    Copyright (c) 2024, Matt Ryan
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

  
// Remove the product count from the shop page
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

// Remove the category count for WooCommerce categories
add_filter( 'woocommerce_subcategory_count_html', '__return_null' );

// Turn off background image regeneration
// Ref: https://developer.woocommerce.com/docs/thumbnail-image-regeneration/
add_filter( 'woocommerce_background_image_regeneration', '__return_false' );


/**
 * @snippet       Remove WooCommerce Edit Product Page Boxes
 * @how-to        Get CustomizeWoo.com FREE
 * @author        Rodolfo Melogli
 * @compatible    WooCommerce 5
 * @community     https://businessbloomer.com/club/
*/
 
add_action( 'add_meta_boxes_product','bbloomer_remove_metaboxes_edit_product', 9999 );
 
function bbloomer_remove_metaboxes_edit_product() {
 
   // e.g. remove short description
//    remove_meta_box( 'postexcerpt', 'product', 'normal' );
 
   // e.g. remove product tags
   remove_meta_box( 'tagsdiv-product_tag', 'product', 'side' );
   // e.g. remove post attributes
   remove_meta_box( 'pageparentdiv', 'product', 'side' );
   // e.g. remove post settings
   remove_meta_box( '_kad_classic_meta_control', 'product', 'side' );
}

/**
 * @snippet       Remove Coupon Form @ WooCommerce Cart 
 *                This removes UI AND functionality. 
 */
function disable_coupon_field_on_checkout( $enabled ) {
   if ( is_checkout() ) {
      $enabled = false;
   }
   return $enabled;
   }
add_filter( 'woocommerce_coupons_enabled','disable_coupon_field_on_checkout' );

/**
 * @snippet       Display Coupon Form @ WooCommerce Checkout (Bottom)
 */
 
add_action( 'woocommerce_review_order_after_submit','bbloomer_checkout_coupon_below_payment_button' );
function bbloomer_checkout_coupon_below_payment_button() {
   echo '<hr>';
   woocommerce_checkout_coupon_form();
}

/**
 * @snippet       Apply WooCommerce Coupon On Link Click
 * 
 */
 
 // 1) Need to decide the URL parameter you want to use. 
 //    In the snippet below I use “qcode”. 
 //    The URL will need to contain the parameter in order to apply the coupon: 
 //    example.com/?code=xyz
 // 2) Need to create a coupon <coupon_code> before adding it to the URL! 
 //    Consider whether you want a random code, or something prettier. 
 //    Once you create the coupon, check the “Individual use only” checkbox 
 //    in the coupon usage restriction settings 
 //    (“Check this box if the coupon cannot be used in conjunction with other coupons“) 
 //    if you don’t want people to add multiple coupons to cart with different URLs
 // 
 add_action( 'wp_loaded','bbloomer_add_coupon_to_session' );
 
 function bbloomer_add_coupon_to_session() {  
    if ( empty( $_GET['qcode'] ) ) return;
    if ( ! WC()->session || ( WC()->session && ! WC()->session->has_session() ) ) {
       WC()->session->set_customer_session_cookie( true );
    }
    $coupon_code = esc_attr( $_GET['qcode'] );
    WC()->session->set( 'coupon_code', $coupon_code );
    if ( WC()->cart && ! WC()->cart->has_discount( $coupon_code ) ) {
       WC()->cart->calculate_totals();
       WC()->cart->add_discount( $coupon_code );
       WC()->session->__unset( 'coupon_code' );
    }
 }
  
add_action( 'woocommerce_add_to_cart','bbloomer_add_coupon_to_cart' );
  
 function bbloomer_add_coupon_to_cart() {
    $coupon_code = WC()->session ? WC()->session->get( 'coupon_code' ) : false;
    if ( ! $coupon_code || empty( $coupon_code ) ) return;
    if ( WC()->cart && ! WC()->cart->has_discount( $coupon_code ) ) {
       WC()->cart->calculate_totals();
       WC()->cart->add_discount( $coupon_code );
       WC()->session->__unset( 'coupon_code' );
    }
 }


/**
 * @snippet       Hide Fields if Virtual @ WooCommerce Checkout
 * @how-to        businessbloomer.com/woocommerce-customization
 * @author        Rodolfo Melogli, Business Bloomer
 * @compatible    WooCommerce 8
 * @community     https://businessbloomer.com/club/
 */
 
 add_filter( 'woocommerce_checkout_fields','remove_checkout_fields' );
  
 function remove_checkout_fields( $fields ) {
   unset($fields['billing']['billing_country']);
   return $fields;
 }


/**
 * Add product short description, aka excerpt, after product title. 
 *   This is only applied to W/C defined shop page. 
 *   As of 1/2/25 we are using a custom page to display the shop.
 */
 add_filter('woocommerce_after_shop_loop_item_title', 'add_short_description', 2 );
 function add_short_description( ) {
   global $product;
   echo esc_html($product->post->post_excerpt);
 }

/**
 * @snippet       Set Free Trial Product Price to Zero so no payment methods offered at checkout. 
 */
add_action('woocommerce_before_calculate_totals', 'cws_set_free_trial_product_price', 10, 1);

function cws_set_free_trial_product_price($cart) {
    if (is_admin() && !defined('DOING_AJAX')) {
        return;
    }

    foreach ($cart->get_cart() as $cart_item) {
        if ($cart_item['product_id'] == 24666) { // 24666 is the Demo product
            $cart_item['data']->set_price(0);
        }
    }
}
