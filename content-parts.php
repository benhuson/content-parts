<?php

/*
Plugin Name: Content Parts
Plugin URI: http://www.benhuson.co.uk/wordpress-plugins/content-parts/
Description: Divide your post content into sections.
Version: 1.3-dev
Author: Ben Huson
Author URI: http://www.benhuson.co.uk
License: GPL
*/

class Content_Parts {
	
	var $editor = '';
	
	/**
	 * Constructor
	 */
	function Content_Parts() {
		add_action( 'wp', array( $this, 'content_parts_query_vars' ) );
		// @todo Maybe also hook onto the_post?
		
		// Admin Includes
		if ( is_admin() ) {
			include_once( WP_PLUGIN_DIR . '/content-parts/admin/editor.php' );
			$this->editor = new Content_Parts_Editor();
		}
	}
	
	/**
	 * content_parts_query_vars
	 */
	function content_parts_query_vars() {
		global $num_content_parts, $content_parts, $post;
		
		if ( ! isset( $post ) )
			return;
		
		$content_parts = $this->split_content_parts( $post->post_content );
		$num_content_parts = count( $content_parts );
	}
	
	/**
	 * Split Content Parts
	 */
	function split_content_parts( $content ) {
		if ( strpos( $content, '<!--contentpartdivider-->' ) ) {
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
	 * Outputs the content part.
	 */
	function the_content_part( $page = 1, $args = null, $deprecated = '' ) {
		$defaults = array(
			'post_id' => null,
			'before'  => '',
			'after'   => ''
		);
		// Deprecate multiple args and move to $args array
		// @todo Add deprecated message
		if ( ! is_array( $args ) && $args != null ) {
			$defaults['before'] = $args;
			$defaults['after']  = $deprecated;
		}
		$args = wp_parse_args( $args, $defaults );
		$my_args = apply_filters( 'content_part_args', $args, $page );
		$output = get_the_content_part( $page, $args );
		$before = str_replace( '%%part%%', $page, $my_args['before'] );
		$after = str_replace( '%%part%%', $page, $my_args['after'] );
		if ( ! empty( $output ) ) {
			echo $before . $output . $after;
		}
	}
	
	/**
	 * Get The Content Part
	 * Returns the content of a paged page.
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
	 * Displays all content of a paged page.
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
		if ( $pargs['start'] <= 0 )
			$pargs['start'] = 1;
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
	 * Returns an array of the content of a paged page.
	 */
	function get_the_content_parts( $args = null ) {
		$args = wp_parse_args( $args, array(
			'post_id' => null
		) );
		$content_parts = $this->get_content_parts( $args );
		return $content_parts;
	}
	
	/**
	 * Has Content Parts
	 * Returns true/false depending if there are multiple content parts.
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
	 * Returns count of available content parts.
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
	 */
	function get_content_parts( $args = null ) {
		global $content_parts;
		$args = wp_parse_args( $args, array(
			'post_id' => null
		) );
		if ( absint( $args['post_id'] ) > 0 ) {
			$post = get_post( $args['post_id'] );
			return $this->split_content_parts( $post->post_content );
		}
		return $content_parts;
	}

}

global $Content_Parts, $num_content_parts, $content_parts, $post;
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

?>