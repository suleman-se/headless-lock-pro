<?php
/**
 * Plugin Deactivator Class
 *
 * Handles plugin deactivation tasks.
 *
 * @package    HeadlessLockPro
 * @subpackage HeadlessLockPro/includes
 * @author     M. Suleman <your-email@example.com>
 * @license    GPL-2.0+ http://www.gnu.org/licenses/gpl-2.0.txt
 * @link       https://github.com/suleman-se/headless-lock-pro
 * @since      2.1.0

namespace HeadlessLockPro;

// Exit if accessed directly.
if (! defined('ABSPATH') ) {
	exit;
}

/**
 * Class Plugin_Deactivator
 *
 * @package    HeadlessLockPro
 * @subpackage HeadlessLockPro/includes
 * @author     M. Suleman <your-email@example.com>
 * @license    GPL-2.0+ http://www.gnu.org/licenses/gpl-2.0.txt
 * @link       https://github.com/suleman-se/headless-lock-pro
 */
class Plugin_Deactivator {


	/**
	 * Deactivate the plugin.
	 *
	 * @return void
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
	 *
	 * @return void
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
