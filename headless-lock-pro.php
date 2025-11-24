<?php
/**
 * Plugin Name: Headless Lock Pro
 * Plugin URI: https://github.com/suleman-se/headless-lock-pro
 * Description: Transform WordPress into a true headless CMS with customizable redirects, security enhancements, and performance optimizations. Perfect for Next.js, React, and Vue.js frontends.
 * Version: 2.1.0
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Author: M. Suleman
 * Author URI: https://www.linkedin.com/in/m-suleman-khan/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: headless-lock-pro
 * Domain Path: /languages
 *
 * @package HeadlessLockPro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants.
define( 'HEADLESS_LOCK_VERSION', '2.1.0' );
define( 'HEADLESS_LOCK_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'HEADLESS_LOCK_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'HEADLESS_LOCK_PLUGIN_FILE', __FILE__ );

// Include required files.
require_once HEADLESS_LOCK_PLUGIN_DIR . 'includes/class-frontend-blocker.php';
require_once HEADLESS_LOCK_PLUGIN_DIR . 'includes/class-security-manager.php';
require_once HEADLESS_LOCK_PLUGIN_DIR . 'includes/class-performance-optimizer.php';
require_once HEADLESS_LOCK_PLUGIN_DIR . 'includes/class-admin-settings.php';
require_once HEADLESS_LOCK_PLUGIN_DIR . 'includes/class-plugin-activator.php';
require_once HEADLESS_LOCK_PLUGIN_DIR . 'includes/class-plugin-deactivator.php';

/**
 * Initialize the plugin.
 */
function headless_lock_pro_init() {
	// Load text domain for translations.
	load_plugin_textdomain( 'headless-lock-pro', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	// Initialize components.
	HeadlessLockPro\Frontend_Blocker::init();
	HeadlessLockPro\Security_Manager::init();
	HeadlessLockPro\Performance_Optimizer::init();
	HeadlessLockPro\Admin_Settings::init();
}

// Initialize the plugin if not in tests
if ( ! defined( 'WP_TESTS_DOMAIN' ) ) {
	headless_lock_pro_init();
}
add_action( 'plugins_loaded', 'headless_lock_pro_init' );

/**
 * Activation hook.
 */
function activate_headless_lock_pro() {
	HeadlessLockPro\Plugin_Activator::activate();
}
register_activation_hook( __FILE__, 'activate_headless_lock_pro' );

/**
 * Deactivation hook.
 */
function deactivate_headless_lock_pro() {
	HeadlessLockPro\Plugin_Deactivator::deactivate();
}
register_deactivation_hook( __FILE__, 'deactivate_headless_lock_pro' );

/**
 * Uninstall hook.
 */
function uninstall_headless_lock_pro() {
	// Clean up plugin options.
	delete_option( 'headless_lock_settings' );
	delete_option( 'headless_lock_version' );
}
register_uninstall_hook( __FILE__, 'uninstall_headless_lock_pro' );
