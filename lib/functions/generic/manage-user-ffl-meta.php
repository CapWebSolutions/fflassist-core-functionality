<?php
/**
 * Manage User FFL Meta
 *
 * This file contains any functions to manage the custom user level FFL-related meta data. 
 *
 * @package      Core_Functionality
 * @since        1.1.0
 * @link         https://github.com/capwebsolutions/fflassist-core-functionality
 * @author       Matt Ryan <matt@capwebsolutions.com>
 * @copyright    Copyright (c) 2024, Matt Ryan
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

function get_ffl_assist_custom_bc_meta( $user_id, $bc_meta ) {

	// Exit false if no user_id provided or if user_id not valid
	if ( !$user_id ) return false;

	// Set return values
	$bc_meta['bc_user_id'] = rwmb_meta( 'bc_user_id', [ 'object_type' => 'user' ], $user_id );
	$bc_meta['bc_tenant_id'] = rwmb_meta( 'bc_tenant_id', [ 'object_type' => 'user' ], $user_id);
	$bc_meta['bc_database'] = rwmb_meta( 'bc_database', [ 'object_type' => 'user' ], $user_id );
	$bc_meta['bc_logon_url'] = rwmb_meta( 'bc_logon_url', [ 'object_type' => 'user' ], $user_id );
	$bc_meta['bc_atf_ffl_number'] = rwmb_meta( 'bc_atf_ffl_number', [ 'object_type' => 'user' ], $user_id );
	return;
}

add_filter( 'rwmb_meta_boxes', 'capweb_create_user_meta_fields' );

function capweb_create_user_meta_fields( $meta_boxes ) {
    $prefix = '';
	// $temp_bc_tenant_id = rwmb_the_value( 'bc_tenant_id', [ 'object_type' => 'user' ], get_current_user_id() );
	$bc_logon_url_place = '';
	$temp_bc_tenant_id = rwmb_meta( 'bc_tenant_id', [ 'object_type' => 'user' ], get_current_user_id() );
	$temp_bc_database = rwmb_meta( 'bc_database', [ 'object_type' => 'user' ], get_current_user_id() );
	if ( !empty ( $temp_bc_tenant_id ) && !empty ( $temp_bc_database ) ) {
		$bc_logon_url_place = 'https://businesscentral.dynamics.com/'. $temp_bc_tenant_id . '/' . $temp_bc_database;
	}

    $meta_boxes[] = [
        'title'   => __( 'BC Access', 'fflassist-core-functionality' ),
        'id'      => 'bc-access',
        'type'    => 'user',
        'include' => [
            'relation'  => 'OR',
            'user_role' => ['administrator'],
        ],
        'fields'  => [
            [
                'name'              => __( 'BC_User_ID', 'fflassist-core-functionality' ),
                'id'                => $prefix . 'bc_user_id',
                'type'              => 'text',
                'desc' 				=> __( 'User ID', 'fflassist-core-functionality' ),
                'size'              => 25,
                'required'          => false,
                'disabled'          => false,
                'readonly'          => false,
                'clone'             => false,
                'clone_empty_start' => false,
                'hide_from_rest'    => false,
                'hide_from_front'   => false,
                'limit_type'        => 'character',
            ],
            [
                'name'              => __( 'BC_Tenant_ID', 'fflassist-core-functionality' ),
                'id'                => $prefix . 'bc_tenant_id',
                'type'              => 'text',
                'desc' => __( 'Tenant ID', 'fflassist-core-functionality' ),
                'size'              => 40,
                'required'          => false,
                'disabled'          => false,
                'readonly'          => false,
                'clone'             => false,
                'clone_empty_start' => false,
                'hide_from_rest'    => false,
                'hide_from_front'   => false,
                'limit_type'        => 'character',
            ],
            [
                'name'              => __( 'BC_Database', 'fflassist-core-functionality' ),
                'id'                => $prefix . 'bc_database',
                'type'              => 'text',
                'desc' => __( 'Database, eg. Production', 'fflassist-core-functionality' ),
                'size'              => 20,
                'required'          => false,
                'disabled'          => false,
                'readonly'          => false,
                'clone'             => false,
                'clone_empty_start' => false,
                'hide_from_rest'    => false,
                'hide_from_front'   => false,
                'limit_type'        => 'character',
            ],
            [
                'name'              => __( 'BC Logon Url', 'fflassist-core-functionality' ),
                'id'                => $prefix . 'bc_logon_url',
                'type'              => 'url',
                'desc' 				=> __( 'Generated Business Central access URL. <a href="#submit">Update Profile</a> to view URL.', 'fflassist-core-functionality' ),
				'size'              => 100,
				'placeholder'       => __( $bc_logon_url_place, 'fflassist-core-functionality' ),
				// 'std'       => __( 'https://businesscentral.dynamics.com/'.['bc_tenant_id'].'/' . rwmb_the_value( 'bc_database', [ 'object_type' => 'user' ], get_current_user_id() ), 'fflassist-core-functionality' ),
                'required'          => false,
                'disabled'          => false,
                'readonly'          => true,
                'clone'             => false,
                'clone_empty_start' => false,
                'hide_from_rest'    => false,
                'hide_from_front'   => false,
				'class'             => 'bc_logon_url',
                'visible'           => [
                    'when'     => [['bc_tenant_id', '!=', ''],[ 'bc_database', '!=', '']],
                    'relation' => 'and',
                ],
            ],
            [
                'name'              => __( 'ATF FFL Number', 'fflassist-core-functionality' ),
                'id'                => $prefix . 'bc_atf_ffl_number',
                'type'              => 'text',
                'size'              => 20,
                'required'          => false,
                'disabled'          => false,
                'readonly'          => false,
                'clone'             => false,
                'clone_empty_start' => false,
                'hide_from_rest'    => false,
                'hide_from_front'   => false,
                'limit_type'        => 'character',
            ],
        ],
    ];

    return $meta_boxes;
}
