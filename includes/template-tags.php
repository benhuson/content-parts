<?php

/**
 * @package     Content Parts
 * @subpackage  Template Tags
 *
 * @since  1.6
 */

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
