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

// Create myloginbutton shortcode. 
// Shortcode [myloginbutton] it adds a login / out button.

add_shortcode( 'myloginbutton','shortcode_forstuff' );

function shortcode_forstuff() {
    ob_start();

    ?>
    <?php if (is_user_logged_in()) : ?>
        <div class="kt-inside-inner-col">
            <div class="wp-block-kadence-advancedbtn kb-buttons-wrap">
				<a class="kb-button kt-button button kt-btn-size-standard kt-btn-width-type-auto kb-btn-global-inherit kt-btn-has-text-true kt-btn-has-svg-false wp-block-button__link wp-block-kadence-singlebtn" href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>">
                    <span class="kt-btn-inner-text">User logged in. Click to log out.</span>
                </a>
            </div>
        </div>
    <!-- remove from here -->
    <?php else : ?>
        <div class="kt-inside-inner-col">
            <div class="wp-block-kadence-advancedbtn kb-buttons-wrap">
            <a class="kb-button kt-button button kt-btn-size-standard kt-btn-width-type-auto kb-btn-global-fill kt-btn-has-text-true kt-btn-has-svg-false wp-block-kadence-singlebtn" href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>" alt="<?php esc_attr_e( 'Log into FFLAssist to read content.', 'fflassist-core-functionality' ); ?>">
			<?php echo esc_html__( 'Log into FFLAssist to read content.', 'fflassist-core-functionality' ); ?></a>
            </div>
        </div>
    <?php endif;?>
    <!-- to here to not show anything to logged in users -->

    <?php
    return ob_get_clean(); // this resets things so the page can get back to displaying its other content

}

/**
 * Change the wording from 'Membership' to 'Subscription' in Paid Memberships Pro
 *
 * This filter will search your codebase for translatable strings and replace when an exact match is found.
 *
 * title: Change the wording from 'Membership' to 'Subscription' in Paid Memberships Pro
 * layout: snippet
 * collection: misc
 * category: localization
 *
 */

/**
 * Here we're changing 'Membership' to 'Subscription' for Paid Memberships Pro.
 *
 * @param  string $output_text     this represents the end result
 * @param  string $input_text      what is written in the code that we want to change
 * @param  string $domain          text-domain of the plugin/theme that contains the code
 *
 * @return string                  the result of the text transformation
 */
function my_gettext_membership( $output_text, $input_text, $domain ) {
	if ( ! is_admin() && ( 'paid-memberships-pro' === $domain || strpos( $domain, 'pmpro-' ) !== false ) ) {
		$output_text = str_replace( 'Membership Level', 'Subscription Level', $output_text );
		$output_text = str_replace( 'membership level', 'subscription level', $output_text );
		$output_text = str_replace( 'membership', 'subscription', $output_text );
		$output_text = str_replace( 'Membership', 'Subscription', $output_text );
	}
	return $output_text;
}
// add_filter( 'gettext','my_gettext_membership', 10, 3 );

// Support _n calls.
function my_ngettext_membership( $output_text, $single, $plural, $number, $domain ) {
	if ( $number == 1 ) {
		return my_gettext_membership( $output_text, $single, $domain );
	} else {
		return my_gettext_membership( $output_text, $plural, $domain );
	}
}
// add_filter( 'ngettext','my_ngettext_membership', 10, 5 );




function my_pmpro_member_action_links( $links, $level_id ){
    if ( $level_id == 1 ) {
        //Add an upgrade link
            $links['upgrade'] = '<a id="pmpro_actionlink-upgrade" href="' . esc_url( add_query_arg( 'pmpro_level', 3, pmpro_url('checkout' ) ) ) . '">' . esc_html__( 'Upgrade to Annual', 'fflassist-core-functionality' ) . '</a>';
    }
    // if ( $level_id == 1 || $level_id == 2 ) {
    // $links['bonuses'] = '<a id="pmpro_actionlink-bonuses" href="/bonuses">' . esc_html__( 'View Your Exclusive Bonuses', 'fflassist-core-functionality') . '</a>';
    // }
    if ( $level_id == 1 || $level_id == 2 || $level_id == 3 ) {
        $links['nextsteps'] = '<a id="pmpro_actionlink-nextsteps" href="/assist-content/public-blog/">' . esc_html__( 'Next Steps', 'fflassist-core-functionality') . '</a>';
        $links['fflassist-logon'] = '<a id="pmpro_actionlink-fflassist-logon" href="/assist-content/fflassist-splash/">' . esc_html__( 'Logon to FFLAssist', 'fflassist-core-functionality') . '</a>';
    }
    return $links;    
    }
    add_filter( 'pmpro_member_action_links','my_pmpro_member_action_links', 10, 2 );

    
/**
 * Offer Trial Memberships That Can Only Be Used Once
 *
 * This will allow users to use the trial level once.
 *
 * Note: This does not affect pre-existing members that had a level before this code is implemented.
 *
 * Ensure line 26 is changed to the trial Level ID's that should only be allowed to be used once.
 *
 * title: Offer Trial Memberships That Can Only Be Used Once
 * layout: snippet
 * collection: membership-levels
 * category: trials
 * link: https://www.paidmembershipspro.com/offer-trial-memberships-that-can-only-be-used-once/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

// record when users gain the trial level
function my_pmpro_after_change_membership_level( $level_id, $user_id ) {

	// set this to the id of your trial level
	$trial_levels = array( 1 );

	if ( in_array( $level_id, $trial_levels ) ) {
		// add user meta to record the fact that this user has had this level before
		update_user_meta( $user_id, "pmpro_trial_level_used_{$level_id}", '1' );
	}
}
add_action( 'pmpro_after_change_membership_level','my_pmpro_after_change_membership_level', 10, 2 );

// check at checkout if the user has used the trial level already
function my_pmpro_registration_checks( $value ) {

	global $current_user;

	$level = pmpro_getLevelAtCheckout();
    
    	$level_id = $level->id;

	if ( $current_user->ID ) {
		// check if the current user has already used the trial level
		$already = get_user_meta( $current_user->ID, "pmpro_trial_level_used_{$level_id}", true );

		// yup, don't let them checkout
		if ( $already ) {
			global $pmpro_msg, $pmpro_msgt;
			$pmpro_msg  = 'You have already used up your trial subscription. Please select a full subscription to checkout.';
			$pmpro_msgt = 'pmpro_error';

			$value = false;
		}
	}

	return $value;
}
add_filter( 'pmpro_registration_checks','my_pmpro_registration_checks' );

// swap the expiration text if the user has used the trial
function my_pmpro_level_expiration_text( $text, $level ) {
	global $current_user;

	// has user used trial level already.
	if ( $current_user->ID ) {
		$used_trial = get_user_meta( $current_user->ID, "pmpro_trial_level_used_{$level->id}", true );

		if ( ! empty( $used_trial ) ) {
			$text = 'You have already used up your trial subscription. Please select a full subscription to checkout.';
		}
	}

	return $text;
}
add_filter( 'pmpro_level_expiration_text','my_pmpro_level_expiration_text', 10, 2 );

/**
 * Redirect members to a specific page when logging in.
 * 
 * title: Redirect members on login.
 * layout: snippet
 * collection: misc
 * category: login redirect
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_login_redirect_url( $redirect_to, $request, $user ) {
	//if logged in and a member, send to members page
	if ( pmpro_hasMembershipLevel( NULL, $user->ID ) ) {
		$redirect_to = '/assist-content/fflassist-splash/';
    } 

    return $redirect_to;
}
// add_filter( 'pmpro_login_redirect_url','my_pmpro_login_redirect_url', 10, 3 );


/**
 * Redirect members and non-members on logout to specified pages
 * Learn more at https://www.paidmembershipspro.com/redirect-logout/
 *
 * title: Redirect member on logout
 * layout: snippet
 * collection: misc
 * category: logout, redirect
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

 function my_pmpro_redirect_members_on_logout( $user_id ) {
	if ( function_exists( 'pmpro_hasMembershipLevel' ) ) {
		error_log( 'Running lo redirect ' . var_export( $user_id, true ) );
		// Redirect users with membership level 1 to /level-1 page
		if ( pmpro_hasMembershipLevel( 1, $user_id ) ) {
			wp_redirect( '/assist-shop/my-account' );
			exit;
		} else {
			// Redirect non-members and members with other levels to /other-levels page.
			wp_redirect( '/' );
			exit;
		}
	}
}
add_action( 'wp_logout', 'my_pmpro_redirect_members_on_logout' );


/**
 * This recipe removes the "Join Now" registration link on the login page.
 *
 * This link is shown only when the "Membership  [ ] Anyone can register" option was set
 * on the WordPress dashboard under Settings > General. To remove the "Join Now" link
 * may also be achieved through deactivating this option if all users are required
 * to be a member.
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_login_forms_handler_nav( $links ) {
	global $pmpro_pages;

	// bail if not on login page.
	if ( ! is_page( $pmpro_pages['login'] ) ) {
		return $links;
	}

	unset( $links['register'] );
	return $links;
}
add_filter( 'pmpro_login_forms_handler_nav', 'my_pmpro_login_forms_handler_nav' );