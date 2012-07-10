<?php

class Content_Parts_Editor {
	
	/**
	 * Constructor
	 */
	function Content_Parts_Editor() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
	}
	
	/**
	 * Admin Init
	 */
	function admin_init() {
		$this->add_buttons();
	}
	
	/**
	 * Add Editor Buttons
	 */
	function add_buttons() {
		// Don't bother doing this stuff if the current user lacks permissions
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) )
			return;
		
		// Add only in Rich Editor mode
		if ( get_user_option( 'rich_editing' ) == 'true' ) {
			add_filter( 'mce_buttons', array( $this, 'register_content_parts_button' ) );
			add_filter( 'mce_external_plugins', array( $this, 'add_content_parts_plugin' ) );
		}
	}
	
	/**
	 * Register Content Parts Button
	 *
	 * This function registers the Content Part button for the editor.
	 *
	 * @param array $buttons Buttons array.
	 * @return array Buttons array.
	 */
	function register_content_parts_button( $buttons ) {
		array_push( $buttons, 'separator', 'contentparts' );
		return $buttons;
	}
	
	/**
	 * Add TinyMCE Content Parts Plugin
	 *
	 * This function adds the Content Part button to the editor.
	 *
	 * @param array $plugin_array TinyMCE plugins array.
	 * @return array Plugins array.
	 */
	function add_content_parts_plugin( $plugin_array ) {
		$plugin_array['contentparts'] = WP_PLUGIN_URL . '/content-parts/js/tinymce/plugins/contentparts/editor_plugin.js';
		return $plugin_array;
	}
	
}

?>