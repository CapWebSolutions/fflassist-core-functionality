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

