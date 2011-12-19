<?php



/*

Plugin Name: Content Parts
Plugin URI: http://www.benhuson.co.uk/wordpress-plugins/content-parts/
Description: Divide your post content into sections.
Version: 1.2
Author: Ben Huson
Author URI: http://www.benhuson.co.uk
License: GPL

*/



// Admin Includes
if ( is_admin() ) {
	include_once( WP_PLUGIN_DIR . '/content-parts/admin/editor.php' );
}



/**
 * content_parts_query_vars
 */

function content_parts_query_vars( ) {
	
	global $num_content_parts, $content_parts, $post;
	
	if ( !isset( $post ) )
		return;
	
	$content = $post->post_content;
	if ( strpos( $content, '<!--contentpartdivider-->' ) ) {
		$content = str_replace("\n<!--contentpartdivider-->\n", '<!--contentpartdivider-->', $content);
		$content = str_replace("\n<!--contentpartdivider-->", '<!--contentpartdivider-->', $content);
		$content = str_replace("<!--contentpartdivider-->\n", '<!--contentpartdivider-->', $content);
		$content_parts = explode('<!--contentpartdivider-->', $content);
		$num_content_parts = count( $content_parts );
	} else {
		$content_parts = array( $post->post_content );
		$num_content_parts = 1;
	}
	
}

add_action( 'wp', 'content_parts_query_vars' );



/**
 * @method       The Content Part
 * @description  Displays the content of a paged page.
 * @output       The page content HTML
 */

function the_content_part( $page = 1, $before = '', $after = '' ) {
	
	$output = get_the_content_part( $page );
	
	if ( !empty( $output ) ) {
		echo $before . $output . $after;
	}
	
}



/**
 * @method       Get The Content Part
 * @description  Returns the content of a paged page.
 * @returns      (String) The page content HTML
 */

function get_the_content_part( $page = 1 ) {
	
	global $content_parts;
	
	$page = absint($page);
	
	if ( $page > 0 && $page <= count( $content_parts ) ) {
	
		$content = force_balance_tags( $content_parts[$page - 1] );
		$content = apply_filters( 'the_content', $content );
		return $content;
	
	}
	
	return '';
	
}



/**
 * @method       The Content Parts
 * @description  Displays all content of a paged page.
 * @output       The page content HTML
 */

function the_content_parts( $args = null ) {
	
	global $content_parts;

	$defaults = array(
		'before' => '',
		'after'  => '',
		'start'  => 1,
		'limit'  => 0
	);
	
	$pargs = wp_parse_args( $args, $defaults );
	
	$pargs['start'] = absint( $pargs['start'] );
	if ( $pargs['start'] <= 0 )
		$pargs['start'] = 1;
	$pargs['limit'] = absint( $pargs['limit'] );
	
	$count = 1;
	foreach ( $content_parts as $page ) {
		if ( $count >= $pargs['start'] && ( $pargs['limit'] == 0 || $count < $pargs['start'] + $pargs['limit']  ) ) {
			$content = force_balance_tags( $page );
			$content = apply_filters( 'the_content', $content );
			echo $pargs['before'] . $content . $pargs['after'];
		}
		$count++;
	}
	
}



/**
 * @method       Get The Content Parts
 * @description  Returns an array of the content of a paged page.
 * @returns      (Array) The page content HTML
 */

function get_the_content_parts() {
	
	global $content_parts;
	
	return $content_parts;
	
}



/**
 * @method       Has Content Parts
 * @description  Returns true/false depending if there are multiple content parts.
 * @returns      (Boolean)
 */

function has_content_parts() {
	
	global $content_parts;
	
	if ( count( $content_parts ) > 1 ) {
		return true;
	}
	
	return false;
	
}



/**
* @method Count Content Parts
* @description Returns count of available content parts.
* @returns (Int)
*/

function count_content_parts() {
	
	global $content_parts;
	
	return count( $content_parts );
	
}



?>