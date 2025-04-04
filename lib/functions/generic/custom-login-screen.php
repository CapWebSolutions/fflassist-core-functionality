<?php
/**
 * Custom Login
 *
 * This file customizes the WordPress default login screen to client branding.
 *
 * @package      Core_Functionality
 * @since        1.0.0
 * @link         https://github.com/capwebsolutions/fflassist-core-functionality
 * @author       Matt Ryan <matt@capwebsolutions.com>
 * @copyright    Copyright (c) 2024, Matt Ryan
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

 /**
 * Customize login screen with site logo replacing WordPress logo.
 *   New logo placed in the assets/images folder within Core Functionality plugin 
 *   Image file named site-login-logo.png 
 *   Image recommended size  540 x 125 pixels and compressed. 
 */
function login_logo() { 

    // Image file path possibilities for site logo. In plugin or in theme. 
    $default_image_plugin = trailingslashit(CORE_FUNCTIONALITY_PLUGIN_URI) .'assets/images/site-login-logo.png';
    $default_image_plugin_svg = trailingslashit(CORE_FUNCTIONALITY_PLUGIN_URI) .'assets/images/site-login-logo.svg';


    // $default_image_theme = trailingslashit(CORE_FUNCTIONALITY_THEME_URI) .'assets/images/site-login-logo.png';

    // This background is from the media library. 
    $default_image_media_library = '/wp-content/uploads/2024/10/AdobeColorGradient-gradient_fflassist-with-checkmark.png';

    //Set preferred logo image and background image
    $default_image = $default_image_plugin_svg;
    $default_image_background = $default_image_media_library;

    ?>
    <style type="text/css">
        #login h1 a, 
        .login h1 a {
            background-image: url(<?php echo esc_url($default_image); ?>);
            height:65px;
            width:320px;
            background-size: 320px 65px;
            background-repeat: no-repeat;
            background-color: rgba(237,242,247,0.5);
            border-radius: 8px;
        }
        body.login {
            /* background: rgb(84,110,145); */
            /* background: radial-gradient(circle, rgba(84,110,145,1) 0%, rgba(156,156,156,1) 100%); */
            background-image: url(<?php echo esc_url($default_image_background); ?>);
        }
        #login p#nav a,
        #login p#backtoblog a,
        #login .privacy-policy-page-link a {
            color: #ffffff;
        }
        .login form {
            border-radius: 10px;
            background-color: #29A632 !important;
        }
    </style>
    <?php 
}

add_action( 'login_enqueue_scripts','login_logo' );

/**
 * Grab the address of the site to connect to new Logo 
 * 
 */
function login_logo_url() {
    return esc_url(home_url());
}
add_filter( 'login_headerurl','login_logo_url' );
  
/** 
 * Grab the name of the site. 
 */
function login_logo_url_text() {
    return esc_html(get_bloginfo( 'name', 'raw' ));
}
add_filter( 'login_headertext','login_logo_url_text' );