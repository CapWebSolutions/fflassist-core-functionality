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

    // Add validation for form 6 User ID checking. 
    add_filter('gform_validation_6',   __NAMESPACE__ . '\validate_wp_user_id_field');
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


function validate_wp_user_id_field( $validation_result ){
    // 
    // validate that entry is an existing WP User ID 
    // For Form ID 6 - validate field 1
    //
        if( !is_wp_user_id_valid( $_POST['input_1']  ) ){
            $validation_result['is_valid'] = false;
            foreach($validation_result['form']['fields'] as &$field){
            // field 1 is the field we are validating  
                if($field['id'] == 1){
                    $field['failed_validation'] = true;
                    $field['validation_message'] = 'The user ID is not valid. Please try again.';
                    break;
                }
            }
        }
        return $validation_result;
    }

    function is_wp_user_id_valid( $user ) {
        global $wpdb;
    
        // Prepare the SQL statement
        $query = $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->users WHERE ID = %d", $user, $user );

        // Execute the query
        $result = $wpdb->get_row($query, ARRAY_A);

        // Check if a result was found
        if ( $result ) {
            return $result;
        } else {
            return false;
        }
    }
