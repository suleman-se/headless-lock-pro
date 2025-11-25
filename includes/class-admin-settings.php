<?php
/**
 * Admin Settings Class
 *
 * Handles the admin settings page and options.
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

class Admin_Settings {


	/**
	 * Initialize the class.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_admin_menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_styles' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( HEADLESS_LOCK_PLUGIN_FILE ), array( __CLASS__, 'add_action_links' ) );
	}

	/**
	 * Add admin menu.
	 *
	 * @return void
	 */
	public static function add_admin_menu() {
		add_options_page(
			__( 'Headless Lock Pro Settings', 'headless-lock-pro' ),
			__( 'Headless Lock Pro', 'headless-lock-pro' ),
			'manage_options',
			'headless-lock-pro',
			array( __CLASS__, 'settings_page' )
		);
	}

	/**
	 * Add action links to plugin page.
	 *
	 * @param  array $links Existing plugin action links.
	 * @return array Modified plugin action links.
	 */
	public static function add_action_links( $links ) {
		$settings_link = '<a href="' . admin_url( 'options-general.php?page=headless-lock-pro' ) . '">' . __( 'Settings', 'headless-lock-pro' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}

	/**
	 * Enqueue admin styles.
	 *
	 * @param string $hook Current admin page hook.
	 * @return void
	 */
	public static function enqueue_admin_styles( $hook ) {
		if ( 'settings_page_headless-lock-pro' !== $hook ) {
			return;
		}

		wp_enqueue_style(
			'headless-lock-admin',
			HEADLESS_LOCK_PLUGIN_URL . 'assets/css/admin-style.css',
			array(),
			HEADLESS_LOCK_VERSION
		);
	}

	/**
	 * Register settings.
	 *
	 * @return void
	 */
	public static function register_settings() {
		register_setting(
			'headless_lock_settings_group',
			'headless_lock_settings',
			array(
				'sanitize_callback' => array( __CLASS__, 'sanitize_settings' ),
			)
		);

		// Redirect Section.
		add_settings_section(
			'headless_lock_redirect_section',
			__( 'Redirect Settings', 'headless-lock-pro' ),
			array( __CLASS__, 'redirect_section_callback' ),
			'headless-lock-pro'
		);

		// Custom Message Section.
		add_settings_section(
			'headless_lock_message_section',
			__( 'Custom 404 Message', 'headless-lock-pro' ),
			array( __CLASS__, 'message_section_callback' ),
			'headless-lock-pro'
		);

		// Security Section.
		add_settings_section(
			'headless_lock_security_section',
			__( 'Security Enhancements', 'headless-lock-pro' ),
			array( __CLASS__, 'security_section_callback' ),
			'headless-lock-pro'
		);

		// Performance Section.
		add_settings_section(
			'headless_lock_performance_section',
			__( 'Performance Optimizations', 'headless-lock-pro' ),
			array( __CLASS__, 'performance_section_callback' ),
			'headless-lock-pro'
		);

		// Add fields - Redirect.
		add_settings_field(
			'redirect_enabled',
			__( 'Enable Redirect', 'headless-lock-pro' ),
			array( __CLASS__, 'checkbox_field' ),
			'headless-lock-pro',
			'headless_lock_redirect_section',
			array(
				'id'          => 'redirect_enabled',
				'description' => __( 'Redirect frontend requests to a custom URL', 'headless-lock-pro' ),
			)
		);

		add_settings_field(
			'redirect_url',
			__( 'Redirect URL', 'headless-lock-pro' ),
			array( __CLASS__, 'text_field' ),
			'headless-lock-pro',
			'headless_lock_redirect_section',
			array(
				'id'          => 'redirect_url',
				'placeholder' => 'https://yourfrontend.com',
			)
		);

		// Add fields - Custom Message.
		add_settings_field(
			'custom_message_title',
			__( 'Page Title', 'headless-lock-pro' ),
			array( __CLASS__, 'text_field' ),
			'headless-lock-pro',
			'headless_lock_message_section',
			array(
				'id'          => 'custom_message_title',
				'placeholder' => __( '404 - Headless Mode', 'headless-lock-pro' ),
			)
		);

		add_settings_field(
			'custom_message_heading',
			__( 'Heading', 'headless-lock-pro' ),
			array( __CLASS__, 'text_field' ),
			'headless-lock-pro',
			'headless_lock_message_section',
			array(
				'id'          => 'custom_message_heading',
				'placeholder' => __( 'This WordPress site is running in Headless Mode', 'headless-lock-pro' ),
			)
		);

		add_settings_field(
			'custom_message_description',
			__( 'Description', 'headless-lock-pro' ),
			array( __CLASS__, 'textarea_field' ),
			'headless-lock-pro',
			'headless_lock_message_section',
			array(
				'id'          => 'custom_message_description',
				'placeholder' => __( 'The public frontend is disabled. Content is available via:', 'headless-lock-pro' ),
			)
		);

		add_settings_field(
			'show_api_url',
			__( 'Show REST API URL', 'headless-lock-pro' ),
			array( __CLASS__, 'checkbox_field' ),
			'headless-lock-pro',
			'headless_lock_message_section',
			array( 'id' => 'show_api_url' )
		);

		add_settings_field(
			'show_admin_link',
			__( 'Show Admin Link', 'headless-lock-pro' ),
			array( __CLASS__, 'checkbox_field' ),
			'headless-lock-pro',
			'headless_lock_message_section',
			array( 'id' => 'show_admin_link' )
		);

		// Add fields - Security.
		$security_fields = array(
			'disable_xmlrpc'       => __( 'Disable XML-RPC', 'headless-lock-pro' ),
			'remove_wp_version'    => __( 'Remove WordPress Version', 'headless-lock-pro' ),
			'disable_feeds'        => __( 'Disable RSS Feeds', 'headless-lock-pro' ),
			'disable_file_editor'  => __( 'Disable File Editor', 'headless-lock-pro' ),
			'add_security_headers' => __( 'Add Security Headers', 'headless-lock-pro' ),
			'limit_rest_api'       => __( 'Limit REST API Access', 'headless-lock-pro' ),
		);

		foreach ( $security_fields as $id => $label ) {
			add_settings_field(
				$id,
				$label,
				array( __CLASS__, 'checkbox_field' ),
				'headless-lock-pro',
				'headless_lock_security_section',
				array( 'id' => $id )
			);
		}

		// Add fields - Performance.
		$performance_fields = array(
			'remove_head_tags'        => __( 'Remove Unnecessary Head Tags', 'headless-lock-pro' ),
			'disable_emojis'          => __( 'Disable Emojis', 'headless-lock-pro' ),
			'disable_embeds'          => __( 'Disable Embeds', 'headless-lock-pro' ),
			'disable_dashicons'       => __( 'Disable Dashicons (Frontend)', 'headless-lock-pro' ),
			'remove_query_strings'    => __( 'Remove Query Strings', 'headless-lock-pro' ),
			'optimize_rest_responses' => __( 'Optimize REST API Responses', 'headless-lock-pro' ),
			'limit_post_revisions'    => __( 'Limit Post Revisions', 'headless-lock-pro' ),
			'disable_heartbeat'       => __( 'Disable Heartbeat API', 'headless-lock-pro' ),
		);

		foreach ( $performance_fields as $id => $label ) {
			add_settings_field(
				$id,
				$label,
				array( __CLASS__, 'checkbox_field' ),
				'headless-lock-pro',
				'headless_lock_performance_section',
				array( 'id' => $id )
			);
		}

		// Add post revisions limit field if limiting is enabled.
		add_settings_field(
			'post_revisions_limit',
			__( 'Post Revisions Limit', 'headless-lock-pro' ),
			array( __CLASS__, 'number_field' ),
			'headless-lock-pro',
			'headless_lock_performance_section',
			array(
				'id'      => 'post_revisions_limit',
				'min'     => 1,
				'max'     => 50,
				'default' => 5,
			)
		);
	}

	/**
	 * Sanitize settings.
	 *
	 * @param  array $input Settings input.
	 * @return array Sanitized settings.
	 */
	public static function sanitize_settings( $input ) {
		$sanitized = array();

		// Sanitize checkbox fields.
		$checkbox_fields = array(
			'redirect_enabled',
			'show_api_url',
			'show_admin_link',
			'disable_xmlrpc',
			'remove_wp_version',
			'disable_feeds',
			'disable_file_editor',
			'add_security_headers',
			'limit_rest_api',
			'remove_head_tags',
			'disable_emojis',
			'disable_embeds',
			'disable_dashicons',
			'remove_query_strings',
			'optimize_rest_responses',
			'limit_post_revisions',
			'disable_heartbeat',
		);

		foreach ( $checkbox_fields as $field ) {
			$sanitized[ $field ] = isset( $input[ $field ] ) ? 1 : 0;
		}

		// Sanitize text fields.
		if ( isset( $input['redirect_url'] ) ) {
			$sanitized['redirect_url'] = esc_url_raw( $input['redirect_url'] );
		}

		if ( isset( $input['custom_message_title'] ) ) {
			$sanitized['custom_message_title'] = sanitize_text_field( $input['custom_message_title'] );
		}

		if ( isset( $input['custom_message_heading'] ) ) {
			$sanitized['custom_message_heading'] = sanitize_text_field( $input['custom_message_heading'] );
		}

		if ( isset( $input['custom_message_description'] ) ) {
			$sanitized['custom_message_description'] = sanitize_textarea_field( $input['custom_message_description'] );
		}

		if ( isset( $input['post_revisions_limit'] ) ) {
			$sanitized['post_revisions_limit'] = absint( $input['post_revisions_limit'] );
		}

		return $sanitized;
	}

	/**
	 * Section callbacks.
	 *
	 * @return void
	 */
	public static function redirect_section_callback() {
		echo '<p>' . esc_html__( 'Configure redirect behavior for frontend requests.', 'headless-lock-pro' ) . '</p>';
	}

	/**
	 * Display message section description.
	 *
	 * @return void
	 */
	public static function message_section_callback() {
		echo '<p>' . esc_html__( 'Customize the 404 message shown when redirect is disabled.', 'headless-lock-pro' ) . '</p>';
	}

	/**
	 * Display security section description.
	 *
	 * @return void
	 */
	public static function security_section_callback() {
		echo '<p>' . esc_html__( 'Enable security features for your headless WordPress installation.', 'headless-lock-pro' ) . '</p>';
	}

	/**
	 * Display performance section description.
	 *
	 * @return void
	 */
	public static function performance_section_callback() {
		echo '<p>' . esc_html__( 'Optimize WordPress performance for headless operation.', 'headless-lock-pro' ) . '</p>';
	}

	/**
	 * Checkbox field callback.
	 *
	 * @param array $args Field arguments.
	 * @return void
	 */
	public static function checkbox_field( $args ) {
		$settings = get_option( 'headless_lock_settings', array() );
		$value    = isset( $settings[ $args['id'] ] ) ? $settings[ $args['id'] ] : 0;
		?>
		<label>
			<input type="checkbox" name="headless_lock_settings[<?php echo esc_attr( $args['id'] ); ?>]" value="1" <?php checked( 1, $value ); ?> />
		<?php if ( isset( $args['description'] ) ) : ?>
				<span class="description"><?php echo esc_html( $args['description'] ); ?></span>
		<?php endif; ?>
		</label>
		<?php
	}

	/**
	 * Text field callback.
	 *
	 * @param array $args Field arguments.
	 * @return void
	 */
	public static function text_field( $args ) {
		$settings    = get_option( 'headless_lock_settings', array() );
		$value       = isset( $settings[ $args['id'] ] ) ? $settings[ $args['id'] ] : '';
		$placeholder = isset( $args['placeholder'] ) ? $args['placeholder'] : '';
		?>
		<input type="text" name="headless_lock_settings[<?php echo esc_attr( $args['id'] ); ?>]" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php echo esc_attr( $placeholder ); ?>" class="regular-text" />
		<?php
	}

	/**
	 * Textarea field callback.
	 *
	 * @param array $args Field arguments.
	 * @return void
	 */
	public static function textarea_field( $args ) {
		$settings    = get_option( 'headless_lock_settings', array() );
		$value       = isset( $settings[ $args['id'] ] ) ? $settings[ $args['id'] ] : '';
		$placeholder = isset( $args['placeholder'] ) ? $args['placeholder'] : '';
		?>
		<textarea name="headless_lock_settings[<?php echo esc_attr( $args['id'] ); ?>]" placeholder="<?php echo esc_attr( $placeholder ); ?>" class="large-text" rows="3"><?php echo esc_textarea( $value ); ?></textarea>
		<?php
	}

	/**
	 * Number field callback.
	 *
	 * @param array $args Field arguments.
	 * @return void
	 */
	public static function number_field( $args ) {
		$settings = get_option( 'headless_lock_settings', array() );
		$value    = isset( $settings[ $args['id'] ] ) ? $settings[ $args['id'] ] : ( isset( $args['default'] ) ? $args['default'] : 0 );
		$min      = isset( $args['min'] ) ? $args['min'] : 0;
		$max      = isset( $args['max'] ) ? $args['max'] : 100;
		?>
		<input type="number" name="headless_lock_settings[<?php echo esc_attr( $args['id'] ); ?>]" value="<?php echo esc_attr( $value ); ?>" min="<?php echo esc_attr( $min ); ?>" max="<?php echo esc_attr( $max ); ?>" class="small-text" />
		<?php
	}

	/**
	 * Settings page callback.
	 *
	 * @return void
	 */
	public static function settings_page() {
		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Show error/update messages.
		settings_errors( 'headless_lock_settings' );
		?>
		<div class="wrap headless-lock-settings">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

			<div class="headless-lock-header">
				<p class="description">
		<?php esc_html_e( 'Transform WordPress into a true headless CMS. Configure redirects, security enhancements, and performance optimizations.', 'headless-lock-pro' ); ?>
				</p>
			</div>

			<form method="post" action="options.php">
		<?php
		settings_fields( 'headless_lock_settings_group' );
		do_settings_sections( 'headless-lock-pro' );
		submit_button();
		?>
			</form>

			<div class="headless-lock-footer">
				<h3><?php esc_html_e( 'About Headless Lock Pro', 'headless-lock-pro' ); ?></h3>
				<p>
		<?php
		printf(
		/* translators: %s: Plugin version */
			esc_html__( 'Version: %s', 'headless-lock-pro' ),
			esc_html( HEADLESS_LOCK_VERSION )
		);
		?>
				</p>
				<p>
		<?php
		printf(
		/* translators: %s: Author name with link */
			esc_html__( 'Developed by %s', 'headless-lock-pro' ),
			'<a href="https://www.linkedin.com/in/m-suleman-khan/" target="_blank">M. Suleman</a>'
		);
		?>
				</p>
			</div>
		</div>
		<?php
	}
}
