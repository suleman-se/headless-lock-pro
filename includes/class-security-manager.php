<?php
/**
 * Security Manager Class
 *
 * @package HeadlessLockPro
 */

namespace HeadlessLockPro;

class Security_Manager {
	public static function init() {
		$settings = get_option( 'headless_lock_settings', array() );

		// Disable XML-RPC if enabled.
		if ( ! empty( $settings['disable_xmlrpc'] ) ) {
			add_filter( 'xmlrpc_enabled', '__return_false' );
		}

		// Remove WordPress version if enabled.
		if ( ! empty( $settings['remove_wp_version'] ) ) {
			add_filter( 'the_generator', '__return_empty_string' );
		}

		// Sanitize login errors.
		add_filter( 'login_errors', array( __CLASS__, 'sanitize_login_errors' ) );

		// Disable user endpoints if limiting REST API.
		if ( ! empty( $settings['limit_rest_api'] ) ) {
			add_filter( 'rest_endpoints', array( __CLASS__, 'disable_user_endpoints' ) );
		}

		// Disable file editing if enabled.
		if ( ! empty( $settings['disable_file_editor'] ) ) {
			self::disable_file_editing();
		}

		// Add security headers if enabled.
		if ( ! empty( $settings['add_security_headers'] ) ) {
			add_action( 'send_headers', array( __CLASS__, 'add_security_headers' ) );
		}

		// Disable feeds if enabled.
		if ( ! empty( $settings['disable_feeds'] ) ) {
			add_action( 'do_feed', array( __CLASS__, 'disable_feeds' ), 1 );
			add_action( 'do_feed_rdf', array( __CLASS__, 'disable_feeds' ), 1 );
			add_action( 'do_feed_rss', array( __CLASS__, 'disable_feeds' ), 1 );
			add_action( 'do_feed_rss2', array( __CLASS__, 'disable_feeds' ), 1 );
			add_action( 'do_feed_atom', array( __CLASS__, 'disable_feeds' ), 1 );
			add_action( 'do_feed_rss2_comments', array( __CLASS__, 'disable_feeds' ), 1 );
			add_action( 'do_feed_atom_comments', array( __CLASS__, 'disable_feeds' ), 1 );
		}
	}

	private static function disable_file_editing() {
		if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
			define( 'DISALLOW_FILE_EDIT', true );
		}
	}

	public static function sanitize_login_errors() {
		return __( 'Invalid credentials. Please try again.', 'headless-lock-pro' );
	}

	public static function disable_user_endpoints( $endpoints ) {
		if ( isset( $endpoints['/wp/v2/users'] ) ) {
			unset( $endpoints['/wp/v2/users'] );
		}
		if ( isset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] ) ) {
			unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
		}
		return $endpoints;
	}

	/**
	 * Add security headers.
	 */
	public static function add_security_headers() {
		if ( ! headers_sent() ) {
			header( 'X-Frame-Options: SAMEORIGIN' );
			header( 'X-Content-Type-Options: nosniff' );
			header( 'Referrer-Policy: strict-origin-when-cross-origin' );
			header( 'Permissions-Policy: geolocation=(), microphone=(), camera=()' );
		}
	}

	/**
	 * Disable feeds.
	 */
	public static function disable_feeds() {
		wp_die( __( 'Feeds are disabled in headless mode.', 'headless-lock-pro' ), '', array( 'response' => 403 ) );
	}
}
