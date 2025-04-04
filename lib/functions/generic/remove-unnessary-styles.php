<?php
/**
 * Remove unnessary styles and scripts from the site
 */
function capweb_remove_woocommerce_assets() {
    // Ref: https://wpbeaches.com/override-woocommerce-css-styles-conditionally/

    if ( function_exists( 'is_woocommerce' ) ) {
        if ( ! is_woocommerce() && ! is_cart() && ! is_checkout() ) {
            // Deregister WooCommerce scripts
            wp_dequeue_script( 'wc_price_slider' );
            wp_dequeue_script( 'wc-single-product' );
            wp_dequeue_script( 'wc-add-to-cart' );
            wp_dequeue_script( 'wc-cart-fragments' );
            wp_dequeue_script( 'wc-checkout' );
            wp_dequeue_script( 'wc-add-to-cart-variation' );
            wp_dequeue_script( 'wc-single-product' );
            wp_dequeue_script( 'wc-cart' );
            wp_dequeue_script( 'wc-chosen' );
            wp_dequeue_script( 'woocommerce' );
            wp_dequeue_script( 'prettyPhoto' );
            wp_dequeue_script( 'prettyPhoto-init' );
            wp_dequeue_script( 'jquery-blockui' );
            wp_dequeue_script( 'jquery-placeholder' );
            wp_dequeue_script( 'fancybox' );
            wp_dequeue_script( 'jqueryui' );
            
            // Deregister WooCommerce styles
            wp_dequeue_style( 'woocommerce-general' );
            wp_dequeue_style( 'woocommerce-layout' );
            wp_dequeue_style( 'woocommerce-smallscreen' );
            wp_dequeue_style( 'woocommerce_frontend_styles' );
            wp_dequeue_style( 'woocommerce_fancybox_styles' );
            wp_dequeue_style( 'woocommerce_chosen_styles' );
            wp_dequeue_style( 'woocommerce_prettyPhoto_css' );
        }
    }
}
add_action('wp_enqueue_scripts', 'capweb_remove_woocommerce_assets', 20);

/**
 * Remove Evenst Manager Styles and Scripts from homer page
 */
function capweb_remove_events_manager_assets_on_homepage() {

        // home page
        if ( is_front_page() ) {
            wp_dequeue_script('events-manager');
            wp_dequeue_script('events-manager-gmap');
            
            // Deregister Events Manager styles
            wp_dequeue_style('events-manager');
            wp_dequeue_style('events-manager-css');
    
            return;
        }
}
add_action('wp_enqueue_scripts', 'capweb_remove_events_manager_assets_on_homepage');