<?php
namespace capweb;
/**
 * @snippet       Purchase History Tab | WooCommerce My Account
 * @tutorial      Get CustomizeWoo.com FREE
 * @author        Rodolfo Melogli, Business Bloomer
 * @compatible    WooCommerce 9
 * @community     https://businessbloomer.com/club/
 */
 
 add_action( 'init', __NAMESPACE__ . '\bbloomer_add_purchase_history_endpoint' );
 
 function bbloomer_add_purchase_history_endpoint() {
     add_rewrite_endpoint( 'purchase-history', EP_ROOT | EP_PAGES );
 }
  
 add_filter( 'query_vars', __NAMESPACE__ . '\bbloomer_purchase_history_query_vars', 0 );
  
 function bbloomer_purchase_history_query_vars( $vars ) {
     $vars[] = 'purchase-history';
     return $vars;
 }
  
 add_filter( 'woocommerce_get_query_vars', __NAMESPACE__ . '\bbloomer_add_wc_query_vars' );
  
 function bbloomer_add_wc_query_vars( $vars ) {
     $vars['purchase-history'] = 'purchase-history';
     return $vars;
 }
 
add_filter( 'woocommerce_endpoint_purchase-history_title', __NAMESPACE__ . '\bbloomer_purchase_history_title' );
function bbloomer_purchase_history_title( $title ) {
    return 'FFLAssist Purchase History';
}

/**
 * @snippet       Purchase History Tab Content | WooCommerce My Account
 * @tutorial      Get CustomizeWoo.com FREE
 * @author        Rodolfo Melogli, Business Bloomer
 * @compatible    WooCommerce 9
 * @community     https://businessbloomer.com/club/
 */
 
add_action( 'woocommerce_account_purchase-history_endpoint', __NAMESPACE__ . '\bbloomer_display_purchase_history' );
 
function bbloomer_display_purchase_history() {
    
    $customer_orders = wc_get_orders( array(
        'customer_id' => get_current_user_id(),
        'status' => array_map( 'wc_get_order_status_name', wc_get_is_paid_statuses() ),
    ));
    
    $customer = new WC_Customer( get_current_user_id() );
    $reviewed_products = [];
    $comments = get_comments( array(
        'author_email' => $customer->get_billing_email(),
        'type' => 'review',
        'status' => 'approve',
    ));    
    foreach ( $comments as $comment ) {
        $reviewed_products[] = $comment->comment_post_ID;
    }
 
    if ( $customer_orders ) {
        echo '<table class="woocommerce-table shop_table shop_table_responsive">';
        echo '<thead><tr><th>Product</th><th>Date</th><th>Actions</th></tr></thead><tbody>';
        foreach ( $customer_orders as $order ) {
            foreach ( $order->get_items() as $item_id => $item ) {
                $product = $item->get_product();
                echo '<tr>';
                echo '<td><a href="' . esc_url( $product->get_permalink() ) . '">' . esc_html( $item->get_name() ) . '</a></td>';
                echo '<td>' . esc_html( wc_format_datetime( $order->get_date_created() ) ) . '</td>';
                echo '<td>';
                if ( ! in_array( $product->get_id(), $reviewed_products ) ) {
                    echo '<a class="button alt" href="' . esc_url( get_permalink( $product->get_id() ) . '#tab-reviews' ) . '">' . esc_html__( 'Add a review', 'woocommerce' ) . '</a> ';
                }
                echo '</td>';
                echo '</tr>';
            }
        }
        echo '</tbody></table>';
    } else {
        echo '<p>No purchases found.</p>';
    }
 
}
