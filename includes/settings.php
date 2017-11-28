<?php

/**
 * @package     Content Parts
 * @subpackage  Settings Class
 */

class Content_Parts_Settings {

	/**
	 * Get Auto-format Post Types
	 *
	 * @return  array
	 */
	public static function get_auto_format_post_types() {

		return (array) get_option( 'content_parts_auto_format_post_types' );

	}

	/**
	 * Is Auto-format Post Type
	 *
	 * @param   string   $post_type  Post type.
	 * @return  boolean
	 */
	public static function is_auto_format_post_type( $post_type ) {

		return in_array( $post_type, self::get_auto_format_post_types() );

	}

}
