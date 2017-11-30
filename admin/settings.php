<?php

/**
 * @package     Content Parts
 * @subpackage  Admin Settings Class
 *
 * @since  1.7
 */

add_action( 'plugins_loaded', array( 'Content_Parts_Admin_Settings', 'load' ) );

class Content_Parts_Admin_Settings extends Content_Parts_Settings {

	/**
	 * Load
	 *
	 * @since  1.7
	 */
	public static function load() {

		add_action( 'admin_init', array( get_class(), 'register_settings' ) );
		add_action( 'admin_menu', array( get_class(), 'admin_page' ) );
		add_action( 'whitelist_options', array( get_class(), 'whitelist_options' ) );
		add_filter( 'plugin_action_links_' . Content_Parts_Plugin::basename(), array( get_class(), 'plugin_action_links' ) );
		add_filter( 'plugin_row_meta', array( get_class(), 'plugin_row_meta' ), 10, 2 );
		add_action( 'admin_notices', array( get_class(), 'admin_notices' ) );

	}

	/**
	 * Register Settings
	 *
	 * @since  1.7
	 *
	 * @internal  Private. Called via the `admin_init` action.
	 */
	public static function register_settings() {

		add_settings_section(
			'content_parts_section',
			'',
			array( get_class(), 'content_parts_settings_section' ),
			'content_parts_page'
		);

		add_settings_field(
			'content_parts_auto_format_post_types',
			__( 'Auto-format HTML for:', 'content-parts' ),
			array( get_class(), 'content_parts_auto_format_post_types_field' ),
			'content_parts_page',
			'content_parts_section'
		);

		register_setting( 'content-parts-group', 'content_parts_auto_format_post_types', array( get_class(), 'validate_post_types' ) );

	}

	/**
	 * Validate Post Types
	 *
	 * @since  1.7
	 *
	 * @param   array  $post_types  Post types.
	 * @return  array               Post types.
	 */
	public static function validate_post_types( $post_types ) {

		if ( ! is_array( $post_types ) ) {
			$post_types = array();
		}

		$post_types = array_map( 'sanitize_text_field', $post_types );

		return $post_types;

	}

	/**
	 * Content Parts Section
	 *
	 * @since  1.7
	 *
	 * @internal  Private. Called via the `add_settings_section()` callback.
	 */
	public static function content_parts_settings_section() {

		echo '<p>' . __( 'By default, content parts will not affect the output of post content.', 'content-parts' ) . '<br />';
		echo __( 'If you would like to automatically output content parts as a series of HTML tags with <code>content-part</code> classes, enable this for specific post types below.', 'content-parts' ) . '</p>';

	}

	/**
	 * Post Types Field
	 *
	 * @since  1.7
	 *
	 * @internal  Private.  Called via the `add_settings_field()` callback.
	 */
	public static function content_parts_auto_format_post_types_field() {

		$post_types = get_post_types( array(
			'public' => true
		), 'objects' );

		unset( $post_types['attachment'] );

		echo '<ul>';
		foreach ( $post_types as $post_type => $data ) {

			echo '<li>';

			$is_filter_added = self::is_filter_added_auto_format_post_type( $post_type );
			$is_filter_removed = self::is_filter_removed_auto_format_post_type( $post_type );

			$description = '';
			$disabled = false;

			if ( $is_filter_added || $is_filter_removed ) {

				// If filtered, use a hidden field to store original value.
				if ( self::is_auto_format_post_type( $post_type, true ) ) {
					echo '<input type="hidden" name="content_parts_auto_format_post_types[]" value="' .  esc_attr( $post_type ) . '" />';
				}

				if ( $is_filter_added ) {
					$description = esc_html__( '(added via the theme or a plugin)', 'content-parts' );
				} elseif ( $is_filter_removed ) {
					$description = esc_html__( '(removed via the theme or a plugin)', 'content-parts' );
				}

				if ( ! empty( $description ) ) {
					$description = ' <span class="description" style="opacity: 0.5;">' . $description . '</span>';
				}

				$disabled = true;

			}

			// Main checkbox field
			printf( '<label><input name="content_parts_auto_format_post_types[]" id="content_parts_auto_format_post_types" type="checkbox" value="%1$s" ' . checked( true, self::is_auto_format_post_type( $post_type ), false ) . disabled( true, $disabled, false ) . ' /> %2$s</label>%3$s', esc_attr( $post_type ), esc_html( $data->label ), $description );

			echo '</li>';

		}
		echo '</ul>';

		self::auto_formatted_html_example();

	}

	/**
	 * Auto-formatted HTML Example
	 *
	 * @since  1.7
	 */
	private static function auto_formatted_html_example() {

		echo '<h4>' . esc_html__( 'Auto-formatted HTML Example', 'content-parts' ) . '</h4>';

		echo '<pre class="code">';
		echo esc_html( '<div class="content-part content-part-1">' . PHP_EOL );
		echo esc_html( '	<!-- ' . _x( 'Part 1 Content', 'Example content', 'content-parts' ) . ' -->' . PHP_EOL );
		echo esc_html( '</div>' . PHP_EOL );
		echo esc_html( '<div class="content-part content-part-2">' . PHP_EOL );
		echo esc_html( '	<!-- ' . _x( 'Part 2 Content', 'Example content', 'content-parts' ) . ' -->' . PHP_EOL );
		echo esc_html( '</div>' . PHP_EOL );
		echo '</pre>';

	}

	/**
	 * Whitelist Options
	 *
	 * Allow use of Settings API from admin theme page.
	 *
	 * @since  1.7
	 *
	 * @param   array  $options  Post types.
	 * @return  array            Post types.
	 *
	 * @internal  Private. Called via the `whitelist_options` action.
	 */
	public static function whitelist_options( $options ) {

		$options['content_parts_page'] = array( 'content_parts_auto_format_post_types' );

		return $options;

	}

	/**
	 * Plugin Action Links
	 *
	 * Adds settings link on the plugins page.
	 *
	 * @since  1.7
	 *
	 * @param   array  $actions  Plugin action links array.
	 * @return  array            Plugin action links array.
	 *
	 * @internal  Private. Called via the `plugin_action_links_{plugin}` filter.
	 */
	public static function plugin_action_links( $actions ) {

		$actions[] = sprintf( '<a href="%s">%s</a>', admin_url( 'themes.php?page=content_parts' ), __( 'Settings', 'content-parts' ) );
		return $actions;

	}

	/**
	 * Plugin Row Meta
	 *
	 * Adds documentation links below the plugin description on the plugins page.
	 *
	 * @since  1.7
	 *
	 * @param   array   $plugin_meta  Plugin meta display array.
	 * @param   string  $plugin_file  Plugin reference.
	 * @return  array                 Plugin meta array.
	 */
	public static function plugin_row_meta( $plugin_meta, $plugin_file ) {

		if ( Content_Parts_Plugin::basename() == $plugin_file ) {
			$plugin_meta[] = sprintf( '<a href="https://github.com/benhuson/content-parts" target="github">%s</a>', __( 'GitHub', 'content-parts' ) );
			$plugin_meta[] = sprintf( '<a href="https://github.com/benhuson/content-parts/wiki" target="github">%s</a>', __( 'Documentation', 'content-parts' ) );
			$plugin_meta[] = sprintf( '<a href="https://translate.wordpress.org/projects/wp-plugins/content-parts" target="wordpress-org">%s</a>', __( 'Translate', 'content-parts' ) );
		}

		return $plugin_meta;

	}

	/**
	 * Admin Page
	 *
	 * @since  1.7
	 *
	 * @internal  Private. Called via the `admin_menu` action.
	 */
	public static function admin_page() {

		add_theme_page( _x( 'Content Parts', 'Admin menu title', 'content-parts' ), _x( 'Content Parts', 'Admin page title', 'content-parts' ), 'manage_options', 'content_parts', array( get_class(), 'settings_page' ) );

	}

	/**
	 * Settings Page
	 *
	 * @since  1.7
	 *
	 * @internal  Private. Called via the `add_theme_page()` callback.
	 */
	public static function settings_page() {

		// Create option if not created when visiting settings screen
		if ( self::is_admin_screen( 'appearance_page_content_parts' ) ) {
			add_option( 'content_parts_auto_format_post_types', array() );
		}

		?>

		<div class="wrap">
			<div id="icon-options-general" class="icon32"><br /></div>
			<h2><?php _ex( 'Content Parts', 'Admin page title', 'content-parts' ) ?></h2>
			<form method="post" action="options.php">
				<?php

				settings_fields( 'content_parts_page' );
				do_settings_sections( 'content_parts_page' );

				submit_button();

				?>
			</form>
		</div>

		<?php
	}

	/**
	 * Admin Notices
	 *
	 * @since  1.7
	 *
	 * @internal  Private. Called via the `admin_notices` action.
	 */
	public static function admin_notices() {

		// Only on dashboard and plugins screens
		if ( ! self::is_admin_screen( array( 'dashboard', 'plugins' ) ) ) {
			return;
		}

		if ( is_null( get_option( 'content_parts_auto_format_post_types', null ) ) ) {

			?>
			<div class="notice notice-info">
				<p><?php printf( __( 'Please visit the Content Parts plugin <a%s>settings page</a> to configure options.', 'content-parts' ), ' href="' . admin_url( 'themes.php?page=content_parts' ) . '"' ); ?></p>
			</div>
			<?php

		}

	}

	/**
	 * If Admin Screen?
	 *
	 * @since  1.7
	 *
	 * @param   array|string  $screen_id  Screen ID.
	 * @return  boolean
	 */
	private static function is_admin_screen( $screen_id ) {

		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		if ( $screen ) {

			// If array of screen IDs
			if ( is_array( $screen_id ) && in_array( $screen->id, $screen_id ) ) {
				return true;
			}

			// If single screen ID
			if ( $screen->id == $screen_id ) {
				return true;
			}

		}

		return false;

	}

}
