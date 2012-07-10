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
	 * Add Buttons
	 */
	function add_buttons() {
		// Don't bother doing this stuff if the current user lacks permissions
		if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) )
			return;
		
		// Add only in Rich Editor mode
		if ( get_user_option( 'rich_editing' ) == 'true' ) {
			add_filter( 'mce_buttons', array( $this, 'register_content_parts_button' ) );
			add_filter( 'mce_external_plugins', array( $this, 'add_content_parts_plugin' ) );
		}
	}
	
	/**
	 * Register Map Button
	 * This function adds the Content Part button to the editor.
	 */
	function register_content_parts_button( $buttons ) {
		array_push( $buttons, 'separator', 'contentparts' );
		return $buttons;
	}
	
	/**
	 * Load TinyMCE Content Parts Plugin
	 * This function adds the Content Part button to the editor.
	 */
	function add_content_parts_plugin( $plugin_array ) {
		$plugin_array['contentparts'] = WP_PLUGIN_URL . '/content-parts/js/tinymce/plugins/contentparts/editor_plugin.js';
		return $plugin_array;
	}
	
}

?>