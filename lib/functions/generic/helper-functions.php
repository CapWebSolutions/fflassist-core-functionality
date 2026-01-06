<?php
/**
 * Helper Functions
 *
 * @category  General_Core_Utility_Functions
 * @package   Core_Functionality
 * @author    Matt Ryan <matt@capwebsolutions.com>
 * @copyright 2024 Matt Ryan
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @link      https://github.com/capwebsolutions/fflassist-core-functionality
 */

// Remove Kadence license options on init
add_action(
    'init',
    function () {
        if (get_option('kadence_licenses_removed') ) {
            return;
        }

        $kadence_options = [
        'kadence-blocks-pro-license-updated',
        'kadence_pro_theme_config',
        'stellarwp_uplink_license_key_kadence-blocks-pro',
        'stellarwp_uplink_license_key_kadence-shop-kit',
        'stellarwp_uplink_license_key_kadence-starter-templates',
        'stellarwp_uplink_license_key_kadence-theme-pro',
        'stellarwp_uplink_license_key_status_kadence-blocks-pro_ffl-assist.com',
        'stellarwp_uplink_update_status_kadence-blocks-pro',
        'stellarwp_uplink_update_status_kadence-shop-kit',
        'stellarwp_uplink_update_status_kadence-theme-pro'
        ];

        foreach ( $kadence_options as $option ) {
            if (get_option($option) ) {
                delete_option($option);
                error_log("Deleted Kadence license: $option");
            }
        }

        update_option('kadence_licenses_removed', true);
    }
);


