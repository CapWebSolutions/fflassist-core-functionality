<?php
/**
 * Tweak and manage media
 *
 * This file includes any customizations to media and library management 
 *
 * @package      Core_Functionality
 * @since        1.0.0
 * @link         https://github.com/capwebsolutions/fflassist-core-functionality
 * @author       Matt Ryan <matt@capwebsolutions.com>
 * @copyright    Copyright (c) 2024, Matt Ryan
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */


 namespace capweb;
 
// Auto Add Alt Tags
/* Automatically set the image Title, Alt-Text, Caption & Description upon upload
--------------------------------------------------------------------------------------*/
add_action( 'add_attachment', __NAMESPACE__ . '\set_image_meta_upon_image_upload' );
function set_image_meta_upon_image_upload( $post_ID ) {
 
	// Check if uploaded file is an image, else do nothing
 
	if ( wp_attachment_is_image( $post_ID ) ) {
 
		$my_image_title = get_post( $post_ID )->post_title;
 
		// Sanitize the title:  remove hyphens, underscores & extra spaces:
		$my_image_title = preg_replace( '%\s*[-_\s]+\s*%', ' ',  $my_image_title );
 
		// Sanitize the title:  capitalize first letter of every word (other letters lower case):
		$my_image_title = ucwords( strtolower( $my_image_title ) );
 
		// Create an array with the image meta (Title, Caption, Description) to be updated
		// Note:  comment out the Excerpt/Caption or Content/Description lines if not needed
		$my_image_meta = array(
			'ID'		=> $post_ID,			// Specify the image (ID) to be updated
			'post_title'	=> $my_image_title,		// Set image Title to sanitized title
			//'post_excerpt'	=> $my_image_title,		// Set image Caption (Excerpt) to sanitized title
			//'post_content'	=> $my_image_title,		// Set image Description (Content) to sanitized title
		);
 
		// Set the image Alt-Text
		update_post_meta( $post_ID, '_wp_attachment_image_alt', $my_image_title );
 
		// Set the image meta (e.g. Title, Excerpt, Content)
		wp_update_post( $my_image_meta );
 
	} 
}

