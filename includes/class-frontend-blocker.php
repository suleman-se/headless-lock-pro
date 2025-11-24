<?php
/**
 * Frontend Blocker Class
 *
 * Handles blocking of frontend access and redirects.
 *
 * @package HeadlessLockPro
 */

namespace HeadlessLockPro;

/**
 * Class Frontend_Blocker
 */
class Frontend_Blocker {

	/**
	 * Initialize the class.
	 */
	public static function init() {
		add_action( 'template_redirect', array( __CLASS__, 'block_frontend_access' ), 1 );
	}

	/**
	 * Block frontend access.
	 */
	public static function block_frontend_access() {
		// Allow Admin Dashboard.
		if ( is_admin() ) {
			return;
		}

		// Allow REST API requests.
		if ( strpos( $_SERVER['REQUEST_URI'], '/wp-json/' ) === 0 || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
			return;
		}

		// Allow AJAX requests.
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		// Allow WP-CLI.
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			return;
		}

		// Allow WP-Cron.
		if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
			return;
		}

		// Allow GraphQL endpoint.
		if ( strpos( $_SERVER['REQUEST_URI'], '/graphql' ) !== false ) {
			return;
		}

		// Allow webhooks endpoint.
		if ( strpos( $_SERVER['REQUEST_URI'], '/wp-webhooks/' ) === 0 ) {
			return;
		}

		// Allow custom whitelisted paths.
		$whitelisted_paths = apply_filters( 'headless_lock_whitelist_paths', array() );
		foreach ( $whitelisted_paths as $path ) {
			if ( strpos( $_SERVER['REQUEST_URI'], $path ) === 0 ) {
				return;
			}
		}

		// Get plugin settings.
		$settings = get_option( 'headless_lock_settings', array() );

		// Option 1: Redirect to custom URL.
		if ( ! empty( $settings['redirect_enabled'] ) && ! empty( $settings['redirect_url'] ) ) {
			wp_redirect( esc_url( $settings['redirect_url'] ), 301 );
			if ( ! defined( 'WP_TESTS_DOMAIN' ) ) {
				exit;
			}
		}

		// Option 2: Show custom 404 message.
		self::show_404_page( $settings );
	}

	/**
	 * Show custom 404 page.
	 *
	 * @param array $settings Plugin settings.
	 */
	private static function show_404_page( $settings ) {
		status_header( 404 );
		nocache_headers();

		// Return JSON for API requests.
		if ( strpos( $_SERVER['HTTP_ACCEPT'], 'application/json' ) !== false ) {
			header( 'Content-Type: application/json' );
			echo wp_json_encode(
				array(
					'error'   => 'Not Found',
					'message' => __( 'This WordPress installation is running in headless mode. Please use the REST API at /wp-json/', 'headless-lock-pro' ),
					'code'    => 'headless_mode_active',
				)
			);
			if ( ! defined( 'WP_TESTS_DOMAIN' ) ) {
				exit;
			}
		}

		// Get custom message settings.
		$title           = isset( $settings['custom_message_title'] ) ? $settings['custom_message_title'] : __( '404 - Headless Mode', 'headless-lock-pro' );
		$heading         = isset( $settings['custom_message_heading'] ) ? $settings['custom_message_heading'] : __( 'This WordPress site is running in Headless Mode', 'headless-lock-pro' );
		$description     = isset( $settings['custom_message_description'] ) ? $settings['custom_message_description'] : __( 'The public frontend is disabled. Content is available via:', 'headless-lock-pro' );
		$show_api_url    = isset( $settings['show_api_url'] ) ? $settings['show_api_url'] : true;
		$show_admin_link = isset( $settings['show_admin_link'] ) ? $settings['show_admin_link'] : true;

		// Output HTML.
		?>
		<!DOCTYPE html>
		<html lang="en">
		<head>
			<meta charset="UTF-8">
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<meta name="robots" content="noindex,nofollow">
			<title><?php echo esc_html( $title ); ?></title>
			<style>
				body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
					   display: flex; align-items: center; justify-content: center; min-height: 100vh;
					   margin: 0; background: #f5f5f5; color: #333; padding: 1rem; }
				.container { text-align: center; max-width: 600px; padding: 2rem; }
				h1 { font-size: 4rem; margin: 0 0 1rem 0; color: #0073aa; }
				p { font-size: 1.2rem; color: #666; margin: 0.5rem 0; }
				.code { background: #fff; padding: 1rem; border-radius: 4px;
						margin: 1rem 0; font-family: monospace; word-break: break-all; }
				small { color: #999; }
				a { color: #0073aa; text-decoration: none; }
				a:hover { text-decoration: underline; }
			</style>
		</head>
		<body>
			<div class="container">
				<h1>404</h1>
				<p><strong><?php echo esc_html( $heading ); ?></strong></p>
				<?php if ( ! empty( $description ) ) : ?>
					<p><?php echo esc_html( $description ); ?></p>
				<?php endif; ?>
				<?php if ( $show_api_url ) : ?>
					<div class="code"><?php echo esc_html__( 'REST API:', 'headless-lock-pro' ); ?> <?php echo esc_url( rest_url() ); ?></div>
				<?php endif; ?>
				<?php if ( $show_admin_link ) : ?>
					<p><small><?php echo esc_html__( 'If you are an administrator, visit', 'headless-lock-pro' ); ?> <a href="<?php echo esc_url( admin_url() ); ?>"><?php echo esc_html__( 'WP Admin', 'headless-lock-pro' ); ?></a></small></p>
				<?php endif; ?>
			</div>
		</body>
		</html>
		<?php
		if ( ! defined( 'WP_TESTS_DOMAIN' ) ) {
			exit;
		}
	}
}
