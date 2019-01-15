<?php

/**
 * @package     Content Parts
 * @subpackage  Content Parts Class
 *
 * Do not called any methods marks as @internal directly.
 *
 * @since  1.3
 */

class Content_Parts {

	public $editor = '';

	/**
	 * Content Parts
	 *
	 * @since  1.6
	 *
	 * @var  array  Array of content part HTML.
	 */
	public $content_parts;

	/**
	 * Constructor
	 *
	 * @since  1.6  PHP5 constructor added.
	 */
	public function __construct() {

		include_once( Content_Parts_Plugin::dir( 'includes/settings.php' ) );

		add_action( 'wp', array( $this, 'content_parts_query_vars' ) );
		add_action( 'the_post', array( $this, 'the_post' ) );
		add_filter( 'post_class', array( $this, 'post_class' ) );
		add_filter( 'the_content', array( $this, 'the_content' ) );

		// Admin Includes
		if ( is_admin() ) {
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				// Load AJAX functions here...
			} else {
				include_once( Content_Parts_Plugin::dir( 'admin/settings.php' ) );
				include_once( Content_Parts_Plugin::dir( 'admin/editor.php' ) );
			}
		}

	}

	/**
	 * Constructor (deprecated)
	 *
	 * @deprecated  1.6  Replaced by PHP5 style constructor.
	 */
	public function Content_Parts() {

		$this->__construct();

	}

	/**
	 * Post Class
	 *
	 * Adds post classes to help with content part styling.
	 *
	 * @since     1.5
	 * @internal  Called by the `post_class` filter.
	 *
	 * @param   array  $classes  Post classes.
	 * @return  array  $classes  Post classes.
	 */
	public function post_class( $classes ) {

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
	 * The Content
	 *
	 * Automatically output <div> blocks around content parts
	 * in the main content on single posts and pages.
	 *
	 * Use the `content_parts_auto_content` filter to return false
	 * to disable automatically outputting content parts.
	 *
	 * @since     1.6
	 * @internal  Called by `the_content` filter.
	 *
	 * @param   string  $content  Post content.
	 * @return  string            Post content.
	 */
	public function the_content( $content ) {

		// Only for the main content on single posts/pages.
		if ( is_singular() && is_main_query() && $this->has_content_parts() && Content_Parts_Settings::is_auto_format_post_type( get_post_type( get_queried_object() ) ) ) {

			remove_filter( 'the_content', array( $this, 'the_content' ) );

			// Default content parts output
			$content = $this->the_content_parts( array(
				'before' => '<div class="content-part content-part-%%part%%">',
				'after'  => '</div>',
				'wrap'   => '<div class="content-parts content-parts--%%count%%"><div class="content-parts__inner">%s</div></div>',
				'echo'   => false
			) );

			add_filter( 'the_content', array( $this, 'the_content' ) );

		}

		return $content;

	}

	/**
	 * Set up the current post
	 *
	 * Populate the content parts variable when 'in the loop'.
	 *
	 * @since     1.4
	 * @internal  Called by the `the_post` action.
	 *
	 * @param  object  $post  Post object.
	 */
	public function the_post( $post ) {

		$this->content_parts = $this->split_content_parts( $post->post_content );

	}

	/**
	 * WordPress Ready
	 *
	 * Populate the content parts variable when WordPress is ready.
	 * The will be overridden when the loop is initiated but means you can
	 * potential use content part before the loop starts.
	 *
	 * @since     1.3
	 * @internal  Called by the `wp` action.
	 */
	public function content_parts_query_vars() {

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
	 * @since  1.3
	 *
	 * @param   string  $content  Post content.
	 * @return  array             Content parts array.
	 */
	public function split_content_parts( $content ) {

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
	 * @since  1.3
	 *
	 * @param  int     $page        Content part index.
	 * @param  array   $args        array( $post_id => null, $before => '', $after => '' ).
	 * @param  string  $deprecated  Used to be the 'after' string.
	 */
	public function the_content_part( $page = 1, $args = null, $deprecated = '' ) {

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
	 * @since  1.3
	 *
	 * @param   int     $page  Content part index.
	 * @param   array   $args  array( $post_id => null ).
	 * @return  string         The content part HTML.
	 */
	public function get_the_content_part( $page = 1, $args = null ) {

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
	 * @since  1.3
	 *
	 * @param array $args array( $post_id => null, $before => '', $after => '', $start => 1, $limit => 0 ).
	 */
	public function the_content_parts( $args = null ) {

		$content_parts = $this->get_content_parts( $args );
		$pargs = wp_parse_args( $args, array(
			'post_id' => null,
			'before'  => '',
			'after'   => '',
			'wrap'    => '%s',
			'start'   => 1,
			'limit'   => 0,
			'echo'    => true
		) );
		$pargs['start'] = absint( $pargs['start'] );
		if ( $pargs['start'] <= 0 ) {
			$pargs['start'] = 1;
		}
		$pargs['limit'] = absint( $pargs['limit'] );

		$output = '';

		$count = 1;
		$total = 0;
		foreach ( $content_parts as $page ) {
			if ( $count >= $pargs['start'] && ( $pargs['limit'] == 0 || $count < $pargs['start'] + $pargs['limit']  ) ) {
				$content = force_balance_tags( $page );
				$content = apply_filters( 'the_content', $content );
				$my_args = apply_filters( 'content_part_args', $pargs, $count );
				$before = str_replace( '%%part%%', $count, $my_args['before'] );
				$after = str_replace( '%%part%%', $count, $my_args['after'] );
				$output .= $before . $content . $after;
				$total++;
			}
			$count++;
		}

		$wrap = str_replace( '%%count%%', $total, $pargs['wrap'] );
		$output = sprintf( $wrap, $output );

		// Output
		if ( $pargs['echo'] ) {
			echo $output;
		}

		return $output;

	}

	/**
	 * Get The Content Parts
	 *
	 * Returns an array of all the content parts.
	 *
	 * @since  1.3
	 *
	 * @param   array  $args  array( $post_id => null ).
	 * @return  array         Content parts.
	 */
	public function get_the_content_parts( $args = null ) {

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
	 * @since  1.3
	 *
	 * @param   array  $args  array( $post_id => null ).
	 * @return  bool          Has content parts?
	 */
	public function has_content_parts( $args = null ) {

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
	 * @since  1.3
	 *
	 * @param   array  $args  array( $post_id => null ).
	 * @return  int           Number of content parts.
	 */
	public function count_content_parts( $args = null ) {

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
	 * @since  1.3
	 *
	 * @param   array  $args  array( $post_id => null ).
	 * @return  array         Content parts.
	 */
	public function get_content_parts( $args = null ) {

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
