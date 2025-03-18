<?php
/**
 * WooCommerce Tweaks - Replace Add to Cart with Request Quote and Form
 *
 * This file includes any custom WooCommerce tweaks
 *
 * @package      Core_Functionality
 * @since        1.0.0
 * @link         https://github.com/capwebsolutions/fflassist-core-functionality
 * @author       Matt Ryan <matt@capwebsolutions.com>
 * @copyright    Copyright (c) 2025, Matt Ryan
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */


 add_action( 'woocommerce_after_add_to_cart_form','fflassist_woocommerce_gf_single_product', 30 );
 function fflassist_woocommerce_gf_single_product() {
    global $product;
    $products_ids = array( 21274 ); // Update this array with IDs of quote products
    if ( is_product()  && $product->get_id() == in_array( $product->get_id(), $products_ids ) ) {
       echo '<button type="submit" id="trigger_gf" class="single_add_to_cart_button button alt">Request a Quote</button>';
       echo '<div id="product_inq" style="display:none">';
       echo do_shortcode('[gravityform id="5" title="true"]');
       echo '</div>';

       // Enque JS for form display toggle. 
       wc_enqueue_js( "
          $('#trigger_gf').on('click', function(){
             if ( $(this).text() == 'Request a Quote' ) {
                $('#product_inq').css('display','block');
                $('input[name=\'your-subject\']').val('" . get_the_title() . "');
                $('#trigger_gf').html('Close');
             } else {
                $('#product_inq').hide();
                $('#trigger_gf').html('Request a Quote');
             }
          });
       " );

     }
   }
   
function remove_add_to_cart_button() {
   global $product;
   $products_ids = array( 21274 ); // Update this array with IDs of quote products
   if (is_product() && $product->get_id() == in_array( $product->get_id(), $products_ids ) ) {
      // remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
      // This above is taking out the entire section includeing the newly added Request a quote. 
      // @TODO need different hook to remove just the add to cart button
   }
}
add_action( 'woocommerce_before_single_product','remove_add_to_cart_button' );