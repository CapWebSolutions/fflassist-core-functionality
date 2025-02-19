<?php
/**
 * WooCommerce Custom Checkout Fields. 
 *
 * This file adds custom checkout fields to classic checkout screen. 
 *
 * @package      Core_Functionality
 * @since        1.0.0
 * @link         https://github.com/capwebsolutions/fflassist-core-functionality
 * @author       Matt Ryan <matt@capwebsolutions.com>
 * @copyright    Copyright (c) 2024, Matt Ryan
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

//  // namespace capweb;

/**
 * @snippet       Add Custom Field @ WooCommerce Checkout Page
 * @how-to        businessbloomer.com/woocommerce-customization
 * @author        Rodolfo Melogli, Business Bloomer
 * @testedwith    WooCommerce 6
 * @community     https://businessbloomer.com/club/
 */
  
// add_action( 'woocommerce_before_order_notes', 'bbloomer_add_custom_checkout_field' );
// add_action( 'woocommerce_after_checkout_billing_form', 'bbloomer_add_custom_checkout_field' );
add_action( 'woocommerce_before_checkout_billing_form', 'bbloomer_add_custom_checkout_field' );
  
 function bbloomer_add_custom_checkout_field( $checkout ) { 
    $current_user = wp_get_current_user();
    $saved_license_no = $current_user->_license_no;
    woocommerce_form_field( '_license_no', array(        
       'type' => 'text',        
       'class' => array( 'form-row-wide' ),        
       'label' => 'FFL License Number',        
       'placeholder' => 'x-xx-xxx-xx-xx-xxxx',        
       'required' => true,        
       'default' => $saved_license_no,
       'description' => "Enter license number with or without '-'. All characters required."        
    ), $checkout->get_value( '_license_no' ) ); 
 }

 /**
 * @snippet       Validate Custom Field @ WooCommerce Checkout Page
 * @how-to        businessbloomer.com/woocommerce-customization
 * @author        Rodolfo Melogli, Business Bloomer
 * @testedwith    WooCommerce 6
 * @community     https://businessbloomer.com/club/
 */
 
add_action( 'woocommerce_checkout_process', 'bbloomer_validate_new_checkout_field' );
  
function bbloomer_validate_new_checkout_field() {    
   if ( ! $_POST['_license_no'] ) {
      wc_add_notice( 'Please enter your FFL Licence Number', 'error' );
   }

   // Reformat and validate the license number entered. 
   if ( ! is_ffl_code_valid( $_POST['_license_no'] ) ) {
      wc_add_notice( 'Invalid FFL Number entered or license is not available to FFLAssist. Please enter a valid FFL Licence Number or <a href="/contact" _target=_blank">Contact us</a>.', 'error' );
   }
}

/**
 * @snippet       Save & Display Custom Field @ WooCommerce Order
 * @how-to        businessbloomer.com/woocommerce-customization
 * @author        Rodolfo Melogli, Business Bloomer
 * @testedwith    WooCommerce 6
 * @community     https://businessbloomer.com/club/
 */
 
 add_action( 'woocommerce_checkout_update_order_meta', 'bbloomer_save_new_checkout_field' );
  
 function bbloomer_save_new_checkout_field( $order_id ) { 
   $license_no = $_POST['_license_no'];
     if ( $license_no ) {
      $add_license_no = update_post_meta( $order_id, '_license_no', $license_no );
     }
 }
  
 add_action( 'woocommerce_thankyou','bbloomer_show_new_checkout_field_thankyou' );
    
 function bbloomer_show_new_checkout_field_thankyou( $order_id ) {    
    if ( get_post_meta( $order_id, '_license_no', true ) ) echo '<p><strong>FFL License Number:</strong> ' . get_post_meta( $order_id, '_license_no', true ) . '</p>';
 }
   
 add_action( 'woocommerce_admin_order_data_after_billing_address', 'bbloomer_show_new_checkout_field_order' );
    
 function bbloomer_show_new_checkout_field_order( $order ) {    
    $order_id = $order->get_id();
    if ( get_post_meta( $order_id, '_license_no', true ) ) echo '<p><strong>License Number:</strong> ' . get_post_meta( $order_id, '_license_no', true ) . '</p>';
 }
  
 add_action( 'woocommerce_email_after_order_table', 'bbloomer_show_new_checkout_field_emails', 20, 4 );
   
 function bbloomer_show_new_checkout_field_emails( $order, $sent_to_admin, $plain_text, $email ) {
     if ( get_post_meta( $order->get_id(), '_license_no', true ) ) echo '<p><strong>FFL License Number:</strong> ' . get_post_meta( $order->get_id(), '_license_no', true ) . '</p>';
 }
 
// Add ATF license to user's meta data.
//  add_action( 'woocommerce_checkout_update_order_meta', 'save_ffl_license_to_user_meta', 11 );
//  function save_ffl_license_to_user_meta( $order_id ) {
//    $user_id = $order->get_user_id();
//    $license_no = get_post_meta( $order_id, '_license_no', true );
//    if ( $license_no ) update_user_meta( $user_id, $license_no );
// }

 /**
 * @snippet       Edit Custom Field @ Woo Edit Order Page
 * @how-to        businessbloomer.com/woocommerce-customization
 * @author        Rodolfo Melogli, Business Bloomer
 */
 
add_action( 'woocommerce_admin_order_data_after_billing_address','bbloomer_order_custom_field_input' );
 
function bbloomer_order_custom_field_input( $order ) {
   echo '<div class="address">'; // REQUIRED!
   echo '<p><b>ATF FFL Number:</b> ' . $order->get_meta( '_license_no' ) . '</p>';
   echo '</div>';
   echo '<div class="edit_address">'; // REQUIRED!
   woocommerce_wp_text_input(
      array(
         'id'    => '_license_no',
         'label' => 'ATF License Number',
         'value' => $order->get_meta( '_license_no' ),
      ),
      $order
   );
    echo '</div>';
}
 
add_action( 'woocommerce_process_shop_order_meta','bbloomer_order_custom_field_save');
 
function bbloomer_order_custom_field_save( $order_id ) {
   $atf_license_no = sanitize_text_field( $_POST['_license_no'] );
   if ( isset( $_POST['_license_no'] ) ) {
      $order = wc_get_order( $order_id );
      $atf_license_no = sanitize_text_field( $_POST['_license_no'] );
      $order->update_meta_data( '_license_no', $atf_license_no );
      $order->save();
   }
}


// New snippet with copilot's help. 
add_action('woocommerce_checkout_update_order_meta','save_license_no_to_user_meta');

function save_license_no_to_user_meta($order_id) {
    // Get the order object
    $order = wc_get_order($order_id);

    // Get the user ID from the order
    $user_id = $order->get_user_id();

    // Get the custom order field '_license_no'
    $license_no = get_post_meta($order_id, '_license_no', true);

    // Save the custom order field to the user's custom metabox field 'bc_atf_ffl_number'
    if ($user_id && $license_no) {
        update_user_meta($user_id, 'bc_atf_ffl_number', $license_no);
    }
}


// New snippet to add custom field to my-account page on woocommerce.
add_filter('woocommerce_account_dashboard', 'cws_add_ffl_license_no_to_my_account');
function cws_add_ffl_license_no_to_my_account( $user_id ) {
   $atf_ffl_number = 'Unassigned';
   if ( is_user_logged_in() ) {
      $user_id = get_current_user_id();
      $atf_ffl_number = get_user_meta( $user_id, 'bc_atf_ffl_number', true );
   }
   echo '<p><strong>ATF FFL Number:</strong> ' . $atf_ffl_number . '</p>';
}