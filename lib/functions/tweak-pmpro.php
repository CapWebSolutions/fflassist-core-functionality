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

// this creates a shortcode, which is called 'myloginbutton'
// when you add the shortcode [myloginbutton] it adds a login / out button.
add_shortcode( 'myloginbutton', __NAMESPACE__ . '\shortcode_forstuff' );
function shortcode_forstuff() {
ob_start(); // this let's us easily inject HTML instead of having to echo it out, or return a large string
?>
<div style="background: green; pdding: 6px;">
<?php if (is_user_logged_in()) : ?>
<a href="<?php echo wp_logout_url(get_permalink()); ?>">Login to read content.</a>
<!-- remove from here -->
<?php else : ?>
<a href="<?php echo wp_login_url(get_permalink()); ?>">Logout message</a>
<?php endif;?>
<!-- to here to not show anything to logged in users -->
</div>
<?php
return ob_get_clean(); // this resets things so the page can get back to displaying its other content
}