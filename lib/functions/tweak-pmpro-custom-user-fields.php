<?php 
/**
 * Tweak PMPro Custom User Fields
 *
 * This file contains any functions that tweak the PMPro Custom User Fields
 *
 * @package      FFL_Assist_Core_Functionality
 * @since        1.0.0
 * @link
 */

function my_pmpro_get_user_meta($user_id){
    // Don't break if PMPro is out of date or not loaded.
	if ( ! function_exists( 'pmpro_add_user_field' ) ) {
		return false;
	}
}
add_action( 'init','my_pmpro_get_user_meta' );

