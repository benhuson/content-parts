<?php

/**
 * @package     Content Parts
 * @subpackage  Settings Class
 *
 * @since  1.7
 */

class Content_Parts_Settings {

	/**
	 * Get Auto-format Post Types
	 *
	 * @since  1.7
	 *
	 * @param   boolean  $supress_filters  Supress filters - get raw data.
	 * @return  array
	 */
	public static function get_auto_format_post_types( $supress_filters = false ) {

		$options = (array) get_option( 'content_parts_auto_format_post_types' );

		if ( ! $supress_filters ) {
			$options = apply_filters( 'content_parts_auto_format_post_types', $options );
		}

		return $options;

	}

	/**
	 * Is Auto-format Post Type
	 *
	 * @since  1.7
	 *
	 * @param   string   $post_type        Post type.
	 * @param   boolean  $supress_filters  Supress filters - get raw data.
	 * @return  boolean
	 */
	public static function is_auto_format_post_type( $post_type, $supress_filters = false ) {

		return in_array( $post_type, self::get_auto_format_post_types( $supress_filters ) );

	}

	/**
	 * Get Filter-added Auto-format Post Types
	 *
	 * @since  1.7
	 *
	 * @return  array
	 */
	protected static function get_filter_added_auto_format_post_types() {

		return apply_filters( 'content_parts_auto_format_post_types', array() );

	}

	/**
	 * Is Filter-added Auto-format Post Type
	 *
	 * @since  1.7
	 *
	 * @param   string   $post_type  Post type.
	 * @return  boolean
	 */
	public static function is_filter_added_auto_format_post_type( $post_type ) {

		return in_array( $post_type, self::get_filter_added_auto_format_post_types() );

	}

	/**
	 * Get Filter-remove Auto-format Post Types
	 *
	 * @since  1.7
	 *
	 * @return  array
	 */
	protected static function get_filter_removed_auto_format_post_types() {

		$options = (array) get_option( 'content_parts_auto_format_post_types' );
		$all_options = self::get_auto_format_post_types();

		return array_diff( $options, $all_options );

	}

	/**
	 * Is Filter-removed Auto-format Post Type
	 *
	 * @since  1.7
	 *
	 * @param   string   $post_type  Post type.
	 * @return  boolean
	 */
	public static function is_filter_removed_auto_format_post_type( $post_type ) {

		return in_array( $post_type, self::get_filter_removed_auto_format_post_types() );

	}

}
