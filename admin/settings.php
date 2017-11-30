<?php

/**
 * @package     Content Parts
 * @subpackage  Admin Settings Class
 */

add_action( 'plugins_loaded', array( 'Content_Parts_Admin_Settings', 'load' ) );

class Content_Parts_Admin_Settings extends Content_Parts_Settings {

	/**
	 * Load
	 */
	public static function load() {

		add_action( 'admin_init', array( get_class(), 'register_settings' ) );
		add_action( 'admin_menu', array( get_class(), 'admin_page' ) );
		add_action( 'whitelist_options', array( get_class(), 'whitelist_options' ) );
		add_filter( 'plugin_action_links_' . Content_Parts_Plugin::basename(), array( get_class(), 'plugin_action_links' ) );
		add_action( 'admin_notices', array( get_class(), 'admin_notices' ) );

	}

	/**
	 * Register Settings
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
	 * @param   array  $post_types  Post types.
	 * @return  array               Post types.
	 */
	public static function validate_post_types( $post_types ) {

		array_map( 'sanitize_text_field', $post_types );

		return $post_types;

	}

	/**
	 * Content Parts Section
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
	 */
	private static function auto_formatted_html_example() {

		echo '<h4>' . esc_html__( 'Auto-formatted HTML Example', 'content-parts' ) . '</h4>';

		echo '<pre class="code">';
		echo esc_html( '<div class="content-part content-part-1">' . PHP_EOL );
		echo esc_html( '	<!-- Part 1 Content -->' . PHP_EOL );
		echo esc_html( '</div>' . PHP_EOL );
		echo esc_html( '<div class="content-part content-part-2">' . PHP_EOL );
		echo esc_html( '	<!-- Part 2 Content -->' . PHP_EOL );
		echo esc_html( '</div>' . PHP_EOL );
		echo '</pre>';

	}

	/**
	 * Whitelist Options
	 *
	 * Allow use of Settings API from admin theme page.
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
	 * Admin Page
	 *
	 * @internal  Private. Called via the `admin_menu` action.
	 */
	public static function admin_page() {

		add_theme_page( __( 'Content Parts', 'content-parts' ), __( 'Content Parts', 'content-parts' ), 'manage_options', 'content_parts', array( get_class(), 'settings_page' ) );

	}

	/**
	 * Settings Page
	 *
	 * @internal  Private. Called via the `add_theme_page()` callback.
	 */
	public function settings_page() {

		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		// Create option if not created when visiting settings screen
		if ( $screen && 'appearance_page_content_parts' == $screen->id ) {
			add_option( 'content_parts_auto_format_post_types', array() );
		}

		?>

		<div class="wrap">
			<div id="icon-options-general" class="icon32"><br /></div>
			<h2><?php _e( 'Content Parts', 'content-parts' ) ?></h2>
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
	 * @internal  Private. Called via the `admin_notices` action.
	 */
	public function admin_notices() {

		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		// Only on dashboard and plugins screens
		if ( ! $screen || ! in_array( $screen->id, array( 'dashboard', 'plugins' ) ) ) {
			return;
		}

		if ( is_null( get_option( 'content_parts_auto_format_post_types', null ) ) ) {

			?>
			<div class="notice notice-info">
				<p><?php printf( 'Please visit the Content Parts plugin <a%s>settings page</a> to configure options.', ' href="' . admin_url( 'themes.php?page=content_parts' ) . '"' ); ?></p>
			</div>
			<?php

		}

	}

}
