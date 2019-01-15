<?php

/*
Plugin Name: Content Parts
Plugin URI: https://wordpress.org/plugins/content-parts/
Description: Divide your post content into parts that you can use in different areas of your theme templates.
Version: 1.8
Author: Ben Huson
Author URI: https://github.com/benhuson/content-parts
Text Domain: content-parts
Domain Path: /languages
License: GPLv2 or later
*/

/**
 * @package     Content Parts
 * @subpackage  Plugin Class
 *
 * Methods to retrieve plugins details such as paths and URLs.
 *
 * @since  1.6
 */
class Content_Parts_Plugin {

	/**
	 * Plugin File
	 *
	 * @since  1.6
	 *
	 * @var  string
	 */
	private static $file = __FILE__;

	/**
	 * Load
	 *
	 * @since  1.6
	 */
	public static function load() {

		include_once( Content_Parts_Plugin::dir( 'includes/content-parts.php' ) );
		include_once( Content_Parts_Plugin::dir( 'includes/template-tags.php' ) );

	}

	/**
	 * Plugin Basename
	 *
	 * @since  1.6
	 *
	 * @return  string  Plugin basename.
	 */
	public static function basename() {

		return plugin_basename( self::$file );

	}

	/**
	 * Plugin Sub Directory
	 *
	 * @since  1.6
	 *
	 * @return  string  Plugin folder name.
	 */
	public static function sub_dir() {

		return trailingslashit( '/' . str_replace( basename( self::$file ), '', self::basename() ) );

	}

	/**
	 * Plugin URL
	 *
	 * @since  1.6
	 *
	 * @return  string  Plugin directory URL.
	 */
	public static function url( $path = '' ) {

		return trailingslashit( plugins_url( self::sub_dir() ) ) . ltrim( $path, '/' );

	}

	/**
	 * Plugin Directory
	 *
	 * @since  1.6
	 * 
	 * @return  string  Plugin directory path.
	 */
	public static function dir( $path = '' ) {

		return trailingslashit( plugin_dir_path( self::$file ) ) . ltrim( $path, '/' );

	}

}

Content_Parts_Plugin::load();
