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

 namespace capweb;

/**
 * Detect if Woo plugin active. 
 */
if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) return;

//plugin is activated - set up all filters
    

remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

// Remove the category count for WooCommerce categories
add_filter( 'woocommerce_subcategory_count_html', '__return_null' );


/**
 * @snippet       Remove WooCommerce Edit Product Page Boxes
 * @how-to        Get CustomizeWoo.com FREE
 * @author        Rodolfo Melogli
 * @compatible    WooCommerce 5
 * @community     https://businessbloomer.com/club/
*/
 
add_action( 'add_meta_boxes_product', __NAMESPACE__ . '\bbloomer_remove_metaboxes_edit_product', 9999 );
 
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
 * @snippet       Add Custom Field @ WooCommerce Checkout Page
 * @how-to        businessbloomer.com/woocommerce-customization
 * @author        Rodolfo Melogli, Business Bloomer
 * @testedwith    WooCommerce 6
 * @community     https://businessbloomer.com/club/
 */
  
//  add_action( 'woocommerce_before_order_notes',  __NAMESPACE__ . '\bbloomer_add_custom_checkout_field' );
// add_action( 'woocommerce_after_checkout_billing_form',  __NAMESPACE__ . '\bbloomer_add_custom_checkout_field' );
add_action( 'woocommerce_before_checkout_billing_form',  __NAMESPACE__ . '\bbloomer_add_custom_checkout_field' );
  
 function bbloomer_add_custom_checkout_field( $checkout ) { 
    $current_user = wp_get_current_user();
    $saved_license_no = $current_user->license_no;
    woocommerce_form_field( 'license_no', array(        
       'type' => 'text',        
       'class' => array( 'form-row-wide' ),        
       'label' => 'FFL License Number',        
       'placeholder' => 'x-xx-xxx-xx-xx-xxxx',        
       'required' => true,        
       'default' => $saved_license_no,
       'description' => "Enter license number with or without '-'. All characters required."        
    ), $checkout->get_value( 'license_no' ) ); 
 }

 /**
 * @snippet       Validate Custom Field @ WooCommerce Checkout Page
 * @how-to        businessbloomer.com/woocommerce-customization
 * @author        Rodolfo Melogli, Business Bloomer
 * @testedwith    WooCommerce 6
 * @community     https://businessbloomer.com/club/
 */
 
add_action( 'woocommerce_checkout_process',  __NAMESPACE__ . '\bbloomer_validate_new_checkout_field' );
  
function bbloomer_validate_new_checkout_field() {    
   if ( ! $_POST['license_no'] ) {
      wc_add_notice( 'Please enter your FFL Licence Number', 'error' );
   }

   // Reformat and validate the license number entered. 
   if ( ! is_ffl_code_valid( $_POST['license_no'] ) ) {
      wc_add_notice( 'Invalid FFL Number entered. Please enter a valid FFL Licence Number', 'error' );
   }
}

/**
 * @snippet       Save & Display Custom Field @ WooCommerce Order
 * @how-to        businessbloomer.com/woocommerce-customization
 * @author        Rodolfo Melogli, Business Bloomer
 * @testedwith    WooCommerce 6
 * @community     https://businessbloomer.com/club/
 */
 
 add_action( 'woocommerce_checkout_update_order_meta',  __NAMESPACE__ . '\bbloomer_save_new_checkout_field' );
  
 function bbloomer_save_new_checkout_field( $order_id ) { 
     if ( $_POST['license_no'] ) update_post_meta( $order_id, '_license_no', esc_attr( $_POST['license_no'] ) );
 }
  
 add_action( 'woocommerce_thankyou', 'bbloomer_show_new_checkout_field_thankyou' );
    
 function bbloomer_show_new_checkout_field_thankyou( $order_id ) {    
    if ( get_post_meta( $order_id, '_license_no', true ) ) echo '<p><strong>FFL License Number:</strong> ' . get_post_meta( $order_id, '_license_no', true ) . '</p>';
 }
   
 add_action( 'woocommerce_admin_order_data_after_billing_address',  __NAMESPACE__ . '\bbloomer_show_new_checkout_field_order' );
    
 function bbloomer_show_new_checkout_field_order( $order ) {    
    $order_id = $order->get_id();
    if ( get_post_meta( $order_id, '_license_no', true ) ) echo '<p><strong>License Number:</strong> ' . get_post_meta( $order_id, '_license_no', true ) . '</p>';
 }
  
 add_action( 'woocommerce_email_after_order_table',  __NAMESPACE__ . '\bbloomer_show_new_checkout_field_emails', 20, 4 );
   
 function bbloomer_show_new_checkout_field_emails( $order, $sent_to_admin, $plain_text, $email ) {
     if ( get_post_meta( $order->get_id(), '_license_no', true ) ) echo '<p><strong>FFL License Number:</strong> ' . get_post_meta( $order->get_id(), '_license_no', true ) . '</p>';
 }
 

 /**
 * @snippet       Hide Downloads Tab @ My Account Page
 * @how-to        businessbloomer.com/woocommerce-customization
 * @author        Rodolfo Melogli, Business Bloomer
 * @compatible    WooCommerce 9
 * @community     https://businessbloomer.com/club/
 */
 
add_filter( 'woocommerce_account_menu_items', __NAMESPACE__ . '\bbloomer_hide_downloads_tab_my_account', 9999 );
 
function bbloomer_hide_downloads_tab_my_account( $items ) {
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
  
function bbloomer_add_access_ffl_assist_endpoint() {
   add_rewrite_endpoint( 'access-ffl-assist', EP_ROOT | EP_PAGES );
}
 
add_action( 'init', __NAMESPACE__ . '\bbloomer_add_access_ffl_assist_endpoint' );
 
// ------------------
// 2. Add new query var
 
function bbloomer_access_ffl_assist_query_vars( $vars ) {
   $vars[] = 'access-ffl-assist';
   return $vars;
}
 
add_filter( 'query_vars', __NAMESPACE__ . '\bbloomer_access_ffl_assist_query_vars', 0 );
 
// ------------------
// 3. Insert the new endpoint into the My Account menu
 
function bbloomer_add_access_ffl_assist_link_my_account( $items ) {
   $items['access-ffl-assist'] = 'Access FFL Assist';
   return $items;
}
 
add_filter( 'woocommerce_account_menu_items', __NAMESPACE__ . '\bbloomer_add_access_ffl_assist_link_my_account' );
 
// ------------------
// 4. Add content to the new tab
 
function bbloomer_access_ffl_assist_content() {
  echo '<h3>Access FFL Assist System</h3><p>Welcome to the FFL Assist launch page.</p>';
  ?> <a href="#">Access Ssytem Here</a><?php
}
 
add_action( 'woocommerce_account_access-ffl-assist_endpoint', __NAMESPACE__ . '\bbloomer_access_ffl_assist_content' );
// Note: add_action must follow 'woocommerce_account_{your-endpoint-slug}_endpoint' format


/* ======================================================================================================== */
/* https://www.businessbloomer.com/woocommerce-separate-login-registration/ */
/**
 * @snippet       WooCommerce User Registration Shortcode
 * @how-to        businessbloomer.com/woocommerce-customization
 * @author        Rodolfo Melogli, Business Bloomer
 * @compatible    WooCommerce 9
 * @community     https://businessbloomer.com/club/
 */

// 12/02/2024 - 10:38 -> Remove this stuff in favor of using PMPro for all this membership 
//    functionality. 


//  add_shortcode( 'wc_reg_form_bbloomer',  __NAMESPACE__ . '\bbloomer_separate_registration_form' );
     
 function bbloomer_separate_registration_form() {
    if ( is_user_logged_in() ) return '<p>You are already registered.</p>';
    ob_start();
    do_action( 'woocommerce_before_customer_login_form' );
    $html = wc_get_template_html( 'myaccount/form-login.php' );
    $dom = new DOMDocument();
    $dom->encoding = 'utf-8';
    $dom->loadHTML( utf8_decode( $html ) );
    $xpath = new DOMXPath( $dom );
    $form = $xpath->query( '//form[contains(@class,"register")]' );
    $form = $form->item( 0 );
    echo $dom->saveHTML( $form );
    return ob_get_clean();
 }
 
/**
 * @snippet       WooCommerce User Login Shortcode
 * @how-to        businessbloomer.com/woocommerce-customization
 * @author        Rodolfo Melogli, Business Bloomer
 * @compatible    WooCommerce 9
 * @community     https://businessbloomer.com/club/
 */
  
//  add_shortcode( 'wc_login_form_bbloomer',  __NAMESPACE__ . '\bbloomer_separate_login_form' );
  
 function bbloomer_separate_login_form() {
    if ( is_user_logged_in() ) return '<p>You are already logged in</p>'; 
    ob_start();
    do_action( 'woocommerce_before_customer_login_form' );
    woocommerce_login_form( array( 'redirect' => wc_get_page_permalink( 'myaccount' ) ) );
    return ob_get_clean();
 }
 

 /**
 * @snippet       Redirect Login/Registration to My Account
 * @how-to        businessbloomer.com/woocommerce-customization
 * @author        Rodolfo Melogli, Business Bloomer
 * @compatible    WooCommerce 9
 * @community     https://businessbloomer.com/club/
 * @description   Optionally Redirect Login & Registration Pages to My Account Page If Customer Is Logged In
 */
 
// add_action( 'template_redirect',  __NAMESPACE__ . '\bbloomer_redirect_login_registration_if_logged_in' );
 
function bbloomer_redirect_login_registration_if_logged_in() {
    if ( is_page() && is_user_logged_in() && ( has_shortcode( get_the_content(), 'wc_login_form_bbloomer' ) || has_shortcode( get_the_content(), 'wc_reg_form_bbloomer' ) ) ) {
        wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
        exit;
    }
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
add_filter( 'woocommerce_coupons_enabled', __NAMESPACE__ . '\disable_coupon_field_on_checkout' );

/**
 * @snippet       Display Coupon Form @ WooCommerce Checkout (Bottom)
 */
 
add_action( 'woocommerce_review_order_after_submit', __NAMESPACE__ . '\bbloomer_checkout_coupon_below_payment_button' );
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
 add_action( 'wp_loaded', __NAMESPACE__ . '\bbloomer_add_coupon_to_session' );
 
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
  
add_action( 'woocommerce_add_to_cart', __NAMESPACE__ . '\bbloomer_add_coupon_to_cart' );
  
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
 * @snippet       Show CF7 Form @ WooCommerce Single Product
 * @how-to        businessbloomer.com/woocommerce-customization
 * @author        Rodolfo Melogli, Business Bloomer
 * @compatible    WooCommerce 5
 * @community     https://businessbloomer.com/club/
 */
  
add_action( 'woocommerce_after_add_to_cart_form', __NAMESPACE__ . '\bbloomer_woocommerce_gf_single_product', 30 );
  
function bbloomer_woocommerce_gf_single_product() {
   global $product;
   if (is_product() && $product->get_id() == 2127) {

      echo '<button type="submit" id="trigger_gf" class="single_add_to_cart_button button alt">Product Inquiry</button>';
      echo '<div id="product_inq" style="display:none">';
      echo do_shortcode('[gravityform id="5" title="true"]');
      echo '</div>';
      wc_enqueue_js( "
         $('#trigger_gf').on('click', function(){
            if ( $(this).text() == 'Product Inquiry' ) {
               $('#product_inq').css('display','block');
               $('input[name=\'your-subject\']').val('" . get_the_title() . "');
               $('#trigger_gf').html('Close');
            } else {
               $('#product_inq').hide();
               $('#trigger_gf').html('Product Inquiry');
            }
         });
      " );
   }
}

/**
 * @snippet       Hide Fields if Virtual @ WooCommerce Checkout
 * @how-to        businessbloomer.com/woocommerce-customization
 * @author        Rodolfo Melogli, Business Bloomer
 * @compatible    WooCommerce 8
 * @community     https://businessbloomer.com/club/
 */
 
 add_filter( 'woocommerce_checkout_fields', __NAMESPACE__ . '\remove_checkout_fields' );
  
 function remove_checkout_fields( $fields ) {
   unset($fields['billing']['billing_country']);
   return $fields;
 }


/**
 * Add product short description, aka excerpt, after product title. 
 *   This is only applied to W/C defined shop page. 
 *   As of 1/2/25 we are using a custom page to display the shop.
 */
 add_filter('woocommerce_after_shop_loop_item_title',  __NAMESPACE__ . '\add_short_description', 2 );
 function add_short_description( ) {
   global $product;
   echo $product->post->post_excerpt;
 }