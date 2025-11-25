<?php
/**
 * Plugin Activator Class
 *
 * Handles plugin activation tasks.
 *
 * @package    HeadlessLockPro
 * @subpackage HeadlessLockPro/includes
 * @author     M. Suleman <suleman192@gmail.com>
 * @license    GPL-2.0+ http://www.gnu.org/licenses/gpl-2.0.txt
 * @link       https://github.com/suleman-se/headless-lock-pro
 * @since      2.1.0
 */

namespace HeadlessLockPro;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Plugin_Activator {


	/**
	 * Activate the plugin.
	 *
	 * @return void
	 */
	public static function activate() {
		// Check if WordPress version is compatible.
		if ( version_compare( get_bloginfo( 'version' ), '5.8', '<' ) ) {
			wp_die(
				esc_html__( 'Headless Lock Pro requires WordPress 5.8 or higher. Please update WordPress.', 'headless-lock-pro' ),
				esc_html__( 'Plugin Activation Error', 'headless-lock-pro' ),
				array( 'back_link' => true )
			);
		}

		// Check if PHP version is compatible.
		if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
			wp_die(
				esc_html__( 'Headless Lock Pro requires PHP 7.4 or higher. Please update PHP.', 'headless-lock-pro' ),
				esc_html__( 'Plugin Activation Error', 'headless-lock-pro' ),
				array( 'back_link' => true )
			);
		}

		// Set default options.
		self::set_default_options();

		// Store plugin version.
		update_option( 'headless_lock_version', HEADLESS_LOCK_VERSION );

		// Flush rewrite rules.
		flush_rewrite_rules();
	}

	/**
	 * Set default options on activation.
	 *
	 * @return void
	 */
	private static function set_default_options() {
		// Check if settings already exist.
		$existing_settings = get_option( 'headless_lock_settings' );

		if ( false !== $existing_settings ) {
			return; // Settings already exist, don't override.
		}

		// Default settings.
		$default_settings = array(
			// Redirect settings.
			'redirect_enabled'           => 0,
			'redirect_url'               => '',

			// Custom message settings.
			'custom_message_title'       => __( '404 - Headless Mode', 'headless-lock-pro' ),
			'custom_message_heading'     => __( 'This WordPress site is running in Headless Mode', 'headless-lock-pro' ),
			'custom_message_description' => __( 'The public frontend is disabled. Content is available via:', 'headless-lock-pro' ),
			'show_api_url'               => 1,
			'show_admin_link'            => 1,

			// Security settings.
			'disable_xmlrpc'             => 1,
			'remove_wp_version'          => 1,
			'disable_feeds'              => 1,
			'disable_file_editor'        => 1,
			'add_security_headers'       => 1,
			'limit_rest_api'             => 0,

			// Performance settings.
			'remove_head_tags'           => 1,
			'disable_emojis'             => 1,
			'disable_embeds'             => 1,
			'disable_dashicons'          => 1,
			'remove_query_strings'       => 1,
			'optimize_rest_responses'    => 1,
			'limit_post_revisions'       => 0,
			'disable_heartbeat'          => 0,
		);

		// Apply filters to allow customization.
		$default_settings = apply_filters( 'headless_lock_default_settings', $default_settings );

		// Save default settings.
		update_option( 'headless_lock_settings', $default_settings );
	}
}
