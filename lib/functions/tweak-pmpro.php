<?php
/**
 * PMPRO Tweaks
 *
 * This file contains any PMPro custom functions
 *
 * @package      Core_Functionality
 * @since        1.1.0
 * @link         https://github.com/capwebsolutions/fflassist-core-functionality
 * @author       Matt Ryan <matt@capwebsolutions.com>
 * @copyright    Copyright (c) 2024, Matt Ryan
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
namespace capweb;

// Create myloginbutton shortcode. 
// Shortcode [myloginbutton] it adds a login / out button.

add_shortcode( 'myloginbutton', __NAMESPACE__ . '\shortcode_forstuff' );

function shortcode_forstuff() {
    ob_start();

    ?>
    <?php if (is_user_logged_in()) : ?>
        <div class="kt-inside-inner-col">
            <div class="wp-block-kadence-advancedbtn kb-buttons-wrap">
                <a class="kb-button kt-button button kt-btn-size-standard kt-btn-width-type-auto kb-btn-global-inherit kt-btn-has-text-true kt-btn-has-svg-false wp-block-button__link wp-block-kadence-singlebtn" href="<?php echo wp_logout_url( home_url() ); ?>">
                    <span class="kt-btn-inner-text">User logged in. Click to log out.</span>
                </a>
            </div>
        </div>
    <!-- remove from here -->
    <?php else : ?>
        <div class="kt-inside-inner-col">
            <div class="wp-block-kadence-advancedbtn kb-buttons-wrap">
            <a class="kb-button kt-button button kt-btn-size-standard kt-btn-width-type-auto kb-btn-global-fill kt-btn-has-text-true kt-btn-has-svg-false wp-block-kadence-singlebtn" href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>" alt="<?php esc_attr_e( 'Log into FFLAssist to read content.', 'fflassist' ); ?>">
            <?php _e( 'Log into FFLAssist to read content.', 'fflassist' ); ?></a>
            </div>
        </div>
    <?php endif;?>
    <!-- to here to not show anything to logged in users -->

    <?php
    return ob_get_clean(); // this resets things so the page can get back to displaying its other content

}