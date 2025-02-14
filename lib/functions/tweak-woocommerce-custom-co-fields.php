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

 namespace capweb;

/**
 * @snippet       Add Custom Field @ WooCommerce Checkout Page
 * @how-to        businessbloomer.com/woocommerce-customization
 * @author        Rodolfo Melogli, Business Bloomer
 * @testedwith    WooCommerce 6
 * @community     https://businessbloomer.com/club/
 */
  
// add_action( 'woocommerce_before_order_notes',  __NAMESPACE__ . '\bbloomer_add_custom_checkout_field' );
// add_action( 'woocommerce_after_checkout_billing_form',  __NAMESPACE__ . '\bbloomer_add_custom_checkout_field' );
add_action( 'woocommerce_before_checkout_billing_form',  __NAMESPACE__ . '\bbloomer_add_custom_checkout_field' );
  
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
 
add_action( 'woocommerce_checkout_process',  __NAMESPACE__ . '\bbloomer_validate_new_checkout_field' );
  
function bbloomer_validate_new_checkout_field() {    
   if ( ! $_POST['_license_no'] ) {
      wc_add_notice( 'Please enter your FFL Licence Number', 'error' );
   }

   // Reformat and validate the license number entered. 
   if ( ! license_manager\is_ffl_code_valid( $_POST['_license_no'] ) ) {
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
 
 add_action( 'woocommerce_checkout_update_order_meta',  __NAMESPACE__ . '\bbloomer_save_new_checkout_field' );
  
 function bbloomer_save_new_checkout_field( $order_id ) { 
     if ( $_POST['_license_no'] ) update_post_meta( $order_id, '_license_no', esc_attr( $_POST['license_no'] ) );
 }
  
 add_action( 'woocommerce_thankyou', __NAMESPACE__ . '\bbloomer_show_new_checkout_field_thankyou' );
    
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
 
// Add ATF license to user's meta data.
 add_action( 'woocommerce_checkout_update_order_meta',  __NAMESPACE__ . '\save_ffl_license_to_user_meta', 11 );
 function save_ffl_license_to_user_meta( $order_id ) {
   $user_id = $order->get_user_id();
   $license_no = get_post_meta( $order_id, '_license_no', true );
   if ( $license_no ) update_user_meta( $user_id, $license_no );
}

 /**
 * @snippet       Edit Custom Field @ Woo Edit Order Page
 * @how-to        businessbloomer.com/woocommerce-customization
 * @author        Rodolfo Melogli, Business Bloomer
 */
 
add_action( 'woocommerce_admin_order_data_after_billing_address', __NAMESPACE__ . '\bbloomer_order_custom_field_input' );
 
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
 
add_action( 'woocommerce_process_shop_order_meta', __NAMESPACE__ . '\bbloomer_order_custom_field_save');
 
function bbloomer_order_custom_field_save( $order_id ) {
   $atf_license_no = sanitize_text_field( $_POST['_license_no'] );
   if ( isset( $_POST['_license_no'] ) ) {
      $order = wc_get_order( $order_id );
      $atf_license_no = sanitize_text_field( $_POST['_license_no'] );
      $order->update_meta_data( '_license_no', $atf_license_no );
      $order->save();
   }
}


