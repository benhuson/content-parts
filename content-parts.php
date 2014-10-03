<?php

/*
Plugin Name: Content Parts
Plugin URI: https://wordpress.org/plugins/content-parts/
Description: Divide your post content into parts that you can use in different areas of your theme templates.
Version: 1.5
Author: Ben Huson
Author URI: https://github.com/benhuson/content-parts
License: GPLv2 or later
*/

class Content_Parts {

	var $editor = '';
	var $content_parts;

	/**
	 * Constructor
	 */
	function Content_Parts() {

		add_action( 'wp', array( $this, 'content_parts_query_vars' ) );
		add_action( 'the_post', array( $this, 'the_post' ) );
		add_filter( 'post_class', array( $this, 'post_class' ) );

		// Admin Includes
		if ( is_admin() ) {
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				// Load AJAX functions here...
			} else {
				include_once( WP_PLUGIN_DIR . '/content-parts/admin/editor.php' );
			}
		}

	}

	/**
	 * Post Class
	 *
	 * Adds post classes to help with content part styling.
	 *
	 * @param   array  $classes  Post classes.
	 * @return  array  $classes  Post classes.
	 */
	function post_class( $classes ) {

		if ( $this->has_content_parts() ) {
			if ( ! in_array( 'no-content-parts', $classes ) ) {
				$classes[] = 'has-content-parts';
				$classes[] = 'content-parts-' . $this->count_content_parts();
			}
		} elseif ( ! in_array( 'has-content-parts', $classes ) ) {
			$classes[] = 'no-content-parts';
		}
		return $classes;

	}

	/**
	 * Set up the current post
	 *
	 * Populate the content parts variable when 'in the loop'.
	 *
	 * @param  object  $post  Post object.
	 */
	function the_post( $post ) {

		$this->content_parts = $this->split_content_parts( $post->post_content );

	}

	/**
	 * WordPress Ready
	 *
	 * Populate the content parts variable when WordPress is ready.
	 * The will be overridden when the loop is initiated but means you can
	 * potential use content part before the loop starts.
	 */
	function content_parts_query_vars() {

		global $post;

		if ( ! isset( $post ) ) {
			return;
		}

		$this->content_parts = $this->split_content_parts( $post->post_content );

	}

	/**
	 * Split Content Parts
	 *
	 * Split the content into an array of content parts.
	 *
	 * @param   string  $content  Post content.
	 * @return  array             Content parts array.
	 */
	function split_content_parts( $content ) {

		if ( strpos( $content, '<!--contentpartdivider-->' ) !== false ) {
			$content = str_replace( "\n<!--contentpartdivider-->\n", '<!--contentpartdivider-->', $content );
			$content = str_replace( "\n<!--contentpartdivider-->", '<!--contentpartdivider-->', $content );
			$content = str_replace( "<!--contentpartdivider-->\n", '<!--contentpartdivider-->', $content );
			$content_parts = explode( '<!--contentpartdivider-->', $content );
		} else {
			$content_parts = array( $content );
		}

		return $content_parts;

	}

	/**
	 * The Content Part
	 *
	 * Outputs a single content part.
	 *
	 * @param  int     $page        Content part index.
	 * @param  array   $args        array( $post_id => null, $before => '', $after => '' ).
	 * @param  string  $deprecated  Used to be the 'after' string.
	 */
	function the_content_part( $page = 1, $args = null, $deprecated = '' ) {

		$defaults = array(
			'post_id' => null,
			'before'  => '',
			'after'   => ''
		);

		// Deprecate multiple args and move to $args array
		// @todo Add deprecated message
		if ( ! empty( $deprecated ) ) {
			_deprecated_argument( __FUNCTION__, '1.4' );
		}
		if ( ! is_array( $args ) && $args != null ) {
			$defaults['before'] = $args;
			$defaults['after']  = $deprecated;
			_deprecated_argument( __FUNCTION__, '1.4' );
		}

		$args = wp_parse_args( $args, $defaults );
		$my_args = apply_filters( 'content_part_args', $args, $page );

		$output = $this->get_the_content_part( $page, $args );
		$before = str_replace( '%%part%%', $page, $my_args['before'] );
		$after = str_replace( '%%part%%', $page, $my_args['after'] );
		if ( ! empty( $output ) ) {
			echo $before . $output . $after;
		}

	}

	/**
	 * Get The Content Part
	 *
	 * Gets the content for a single content part.
	 *
	 * @param   int     $page  Content part index.
	 * @param   array   $args  array( $post_id => null ).
	 * @return  string         The content part HTML.
	 */
	function get_the_content_part( $page = 1, $args = null ) {

		$args = wp_parse_args( $args, array(
			'post_id' => null
		) );

		$content_parts = $this->get_content_parts( $args );
		$page = absint( $page );

		if ( $page > 0 && $page <= count( $content_parts ) ) {
			$content = force_balance_tags( $content_parts[$page - 1] );
			$content = apply_filters( 'the_content', $content );
			return $content;
		}

		return '';

	}

	/**
	 * The Content Parts
	 *
	 * Displays multiple content parts.
	 *
	 * @param array $args array( $post_id => null, $before => '', $after => '', $start => 1, $limit => 0 ).
	 */
	function the_content_parts( $args = null ) {

		$content_parts = $this->get_content_parts( $args );
		$pargs = wp_parse_args( $args, array(
			'post_id' => null,
			'before'  => '',
			'after'   => '',
			'start'   => 1,
			'limit'   => 0
		) );
		$pargs['start'] = absint( $pargs['start'] );
		if ( $pargs['start'] <= 0 ) {
			$pargs['start'] = 1;
		}
		$pargs['limit'] = absint( $pargs['limit'] );

		$count = 1;
		foreach ( $content_parts as $page ) {
			if ( $count >= $pargs['start'] && ( $pargs['limit'] == 0 || $count < $pargs['start'] + $pargs['limit']  ) ) {
				$content = force_balance_tags( $page );
				$content = apply_filters( 'the_content', $content );
				$my_args = apply_filters( 'content_part_args', $pargs, $count );
				$before = str_replace( '%%part%%', $count, $my_args['before'] );
				$after = str_replace( '%%part%%', $count, $my_args['after'] );
				echo $before . $content . $after;
			}
			$count++;
		}

	}

	/**
	 * Get The Content Parts
	 *
	 * Returns an array of all the content parts.
	 *
	 * @param   array  $args  array( $post_id => null ).
	 * @return  array         Content parts.
	 */
	function get_the_content_parts( $args = null ) {

		$args = wp_parse_args( $args, array(
			'post_id' => null
		) );

		return $this->get_content_parts( $args );

	}

	/**
	 * Has Content Parts
	 *
	 * Returns true/false depending on wether there are multiple content parts.
	 *
	 * @param   array  $args  array( $post_id => null ).
	 * @return  bool          Has content parts?
	 */
	function has_content_parts( $args = null ) {

		$args = wp_parse_args( $args, array(
			'post_id' => null
		) );

		$content_parts = $this->get_content_parts( $args );
		if ( count( $content_parts ) > 1 ) {
			return true;
		}

		return false;

	}

	/**
	 * Count Content Parts
	 *
	 * Returns the number of available content parts.
	 *
	 * @param   array  $args  array( $post_id => null ).
	 * @return  int           Number of content parts.
	 */
	function count_content_parts( $args = null ) {

		$args = wp_parse_args( $args, array(
			'post_id' => null
		) );

		$content_parts = $this->get_content_parts( $args );

		return count( $content_parts );

	}

	/**
	 * Get Content Parts
	 *
	 * Returns the array of content parts.
	 *
	 * @param   array  $args  array( $post_id => null ).
	 * @return  array         Content parts.
	 */
	function get_content_parts( $args = null ) {

		$args = wp_parse_args( $args, array(
			'post_id' => null
		) );

		if ( absint( $args['post_id'] ) > 0 ) {
			$post = get_post( $args['post_id'] );
			return $this->split_content_parts( $post->post_content );
		}

		return $this->content_parts;

	}

}

global $Content_Parts;
$Content_Parts = new Content_Parts();

// The Content Part
function the_content_part( $page = 1, $args = null, $deprecated = '' ) {
	global $Content_Parts;
	$Content_Parts->the_content_part( $page, $args, $deprecated );
}

// Get The Content Part
function get_the_content_part( $page = 1, $args = null ) {
	global $Content_Parts;
	return $Content_Parts->get_the_content_part( $page, $args );
}

// The Content Parts
function the_content_parts( $args = null ) {
	global $Content_Parts;
	$Content_Parts->the_content_parts( $args );
}

// Get The Content Parts
function get_the_content_parts( $args = null ) {
	global $Content_Parts;
	return $Content_Parts->get_the_content_parts( $args );
}

// Has Content Parts
function has_content_parts( $args = null ) {
	global $Content_Parts;
	return $Content_Parts->has_content_parts( $args );
}

// Count Content Parts
function count_content_parts( $args = null ) {
	global $Content_Parts;
	return $Content_Parts->count_content_parts( $args );
}
