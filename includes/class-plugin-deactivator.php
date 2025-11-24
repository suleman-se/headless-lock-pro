<?php
/**
 * Plugin Deactivator Class
 *
 * Handles plugin deactivation tasks.
 *
 * @package HeadlessLockPro
 */

namespace HeadlessLockPro;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Plugin_Deactivator
 */
class Plugin_Deactivator {

	/**
	 * Deactivate the plugin.
	 */
	public static function deactivate() {
		// Flush rewrite rules to clean up.
		flush_rewrite_rules();

		// Clear any scheduled cron jobs if any.
		$timestamp = wp_next_scheduled( 'headless_lock_pro_daily_task' );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, 'headless_lock_pro_daily_task' );
		}

		// Clear transients.
		self::clear_plugin_transients();

		// Note: We don't delete options here as users might want to reactivate later.
		// Options are only deleted on uninstall (see uninstall hook in main plugin file).
	}

	/**
	 * Clear all plugin-related transients.
	 */
	private static function clear_plugin_transients() {
		global $wpdb;

		// Delete transients with our prefix.
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
				$wpdb->esc_like( '_transient_headless_lock_' ) . '%',
				$wpdb->esc_like( '_transient_timeout_headless_lock_' ) . '%'
			)
		);

		// Clean up expired transients.
		delete_expired_transients();
	}
}
