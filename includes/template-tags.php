<?php

/**
 * @package     Content Parts
 * @subpackage  Template Tags
 *
 * @since  1.6
 */

/**
 * The Content Part
 *
 * Outputs a single content part.
 *
 * @since  1.2
 *
 * @param  int     $page        Content part index.
 * @param  array   $args        array( $post_id => null, $before => '', $after => '' ).
 * @param  string  $deprecated  Used to be the 'after' string.
 */
function the_content_part( $page = 1, $args = null, $deprecated = '' ) {

	global $Content_Parts;

	$Content_Parts->the_content_part( $page, $args, $deprecated );

}

/**
 * Get The Content Part
 *
 * Gets the content for a single content part.
 *
 * @since  1.2
 *
 * @param   int     $page  Content part index.
 * @param   array   $args  array( $post_id => null ).
 * @return  string         The content part HTML.
 */
function get_the_content_part( $page = 1, $args = null ) {

	global $Content_Parts;

	return $Content_Parts->get_the_content_part( $page, $args );

}

/**
 * The Content Parts
 *
 * Displays multiple content parts.
 *
 * @since  1.2
 *
 * @param array $args array( $post_id => null, $before => '', $after => '', $start => 1, $limit => 0 ).
 */
function the_content_parts( $args = null ) {

	global $Content_Parts;

	$Content_Parts->the_content_parts( $args );

}

/**
 * Get The Content Parts
 *
 * Returns an array of all the content parts.
 *
 * @since  1.2
 *
 * @param   array  $args  array( $post_id => null ).
 * @return  array         Content parts.
 */
function get_the_content_parts( $args = null ) {

	global $Content_Parts;

	return $Content_Parts->get_the_content_parts( $args );

}

/**
 * Has Content Parts
 *
 * Returns true/false depending on wether there are multiple content parts.
 *
 * @since  1.2
 *
 * @param   array  $args  array( $post_id => null ).
 * @return  bool          Has content parts?
 */
function has_content_parts( $args = null ) {

	global $Content_Parts;

	return $Content_Parts->has_content_parts( $args );

}

/**
 * Count Content Parts
 *
 * Returns the number of available content parts.
 *
 * @since  1.2
 *
 * @param   array  $args  array( $post_id => null ).
 * @return  int           Number of content parts.
 */
function count_content_parts( $args = null ) {

	global $Content_Parts;

	return $Content_Parts->count_content_parts( $args );

}
