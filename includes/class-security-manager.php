<?php
/**
 * Security Manager Class
 *
 * @package HeadlessLockPro
 */

namespace HeadlessLockPro;

class Security_Manager {
	public static function init() {
		add_filter( 'xmlrpc_enabled', '__return_false' );
		add_filter( 'the_generator', '__return_empty_string' );
		add_filter( 'login_errors', array( __CLASS__, 'sanitize_login_errors' ) );
		add_filter( 'rest_endpoints', array( __CLASS__, 'disable_user_endpoints' ) );
		self::disable_file_editing();
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
}
