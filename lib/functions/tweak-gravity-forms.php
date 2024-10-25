<?php
/**
 * Gravity Forms Tweaks
 *
 * This file includes any custom Gravity Forms settings 
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
 * Detect if Gravity Forms plugin active. 
 */
if ( is_plugin_active( 'gravityforms/gravityforms.php' ) ) {
    // Turn_off_gravity_forms_admin_notice
    add_action(	'gform_loaded', __NAMESPACE__ . '\turn_off_gravity_forms_admin_notice');
}

/**
 * turn_off_gravity_forms_admin_notice
 * 
 * Turn off admin notice stating there is a update for Gravity Forms
 *
 * @return void
 * @author Matt Ryan <matt@capwebsolutions.com>
 * @since  2023-11-21
 * @link https://legacy.forums.gravityhelp.com/topic/disable-there-is-an-update-available-for-gravity-forms-notification
 */
function turn_off_gravity_forms_admin_notice() {
    if( is_admin() ) {
        $dismissed = get_option( "gf_dismissed_upgrades" );
        $version_info = \GFCommon::get_version_info();
        if(!$dismissed || !in_array($version_info["version"], $dismissed)){
            update_option("gf_dismissed_upgrades", array($version_info["version"]));
        }
    }
}

// http://www.gravityhelp.com/forums/topic/database-look-up-validation
// validate 9 digit code 
// For Form ID 1
add_filter('gform_validation_1',   __NAMESPACE__ . '\validate_code');
function validate_code($validation_result){

    // validate field 8
    if( !is_ffl_code_valid( $_POST['input_8']  ) ){
        $validation_result['is_valid'] = false;
        foreach($validation_result['form']['fields'] as &$field){
        // field 8 is the field we are validating  
            if($field['id'] == 8){
                $field['failed_validation'] = true;
                $field['validation_message'] = 'The license number you entered is not found. Please try again.';
                break;
            }
        }
    }
    return $validation_result;
}