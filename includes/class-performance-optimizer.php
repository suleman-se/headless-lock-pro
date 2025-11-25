<?php
/**
 * Performance Optimizer Class
 *
 * Handles performance optimizations for headless WordPress setup.
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

class Performance_Optimizer {


	/**
	 * Initialize the class.
	 *
	 * @return void
	 */
	public static function init() {
		$settings = get_option( 'headless_lock_settings', array() );

		// Remove unnecessary WordPress head tags.
		if ( ! empty( $settings['remove_head_tags'] ) ) {
			add_action( 'init', array( __CLASS__, 'remove_head_tags' ) );
		}

		// Disable emojis.
		if ( ! empty( $settings['disable_emojis'] ) ) {
			add_action( 'init', array( __CLASS__, 'disable_emojis' ) );
		}

		// Disable embeds.
		if ( ! empty( $settings['disable_embeds'] ) ) {
			add_action( 'init', array( __CLASS__, 'disable_embeds' ), 9999 );
		}

		// Disable dashicons on frontend.
		if ( ! empty( $settings['disable_dashicons'] ) ) {
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'disable_dashicons' ) );
		}

		// Remove query strings from static resources.
		if ( ! empty( $settings['remove_query_strings'] ) ) {
			add_filter( 'script_loader_src', array( __CLASS__, 'remove_query_strings' ), 15, 1 );
			add_filter( 'style_loader_src', array( __CLASS__, 'remove_query_strings' ), 15, 1 );
		}

		// Optimize REST API responses.
		if ( ! empty( $settings['optimize_rest_responses'] ) ) {
			add_filter( 'rest_prepare_post', array( __CLASS__, 'optimize_rest_response' ), 10, 3 );
			add_filter( 'rest_prepare_page', array( __CLASS__, 'optimize_rest_response' ), 10, 3 );
		}

		// Limit post revisions.
		if ( ! empty( $settings['limit_post_revisions'] ) && ! defined( 'WP_POST_REVISIONS' ) ) {
			$revision_limit = isset( $settings['post_revisions_limit'] ) ? absint( $settings['post_revisions_limit'] ) : 5;
			define( 'WP_POST_REVISIONS', $revision_limit );
		}

		// Disable heartbeat API.
		if ( ! empty( $settings['disable_heartbeat'] ) ) {
			add_action( 'init', array( __CLASS__, 'disable_heartbeat' ), 1 );
		}
	}

	/**
	 * Remove unnecessary WordPress head tags.
	 *
	 * @return void
	 */
	public static function remove_head_tags() {
		// Remove REST API link tag.
		remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );

		// Remove oEmbed discovery links.
		remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );

		// Remove rel=prev and rel=next.
		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );

		// Remove Windows Live Writer manifest link.
		remove_action( 'wp_head', 'wlwmanifest_link' );

		// Remove WordPress version from head.
		remove_action( 'wp_head', 'wp_generator' );

		// Remove DNS prefetch.
		remove_action( 'wp_head', 'wp_resource_hints', 2 );
	}

	/**
	 * Disable emojis.
	 *
	 * @return void
	 */
	public static function disable_emojis() {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

		add_filter( 'tiny_mce_plugins', array( __CLASS__, 'disable_emojis_tinymce' ) );
		add_filter( 'wp_resource_hints', array( __CLASS__, 'disable_emojis_remove_dns_prefetch' ), 10, 2 );
	}

	/**
	 * Filter function used to remove the tinymce emoji plugin.
	 *
	 * @param  array $plugins TinyMCE plugins.
	 * @return array Filtered TinyMCE plugins.
	 */
	public static function disable_emojis_tinymce( $plugins ) {
		if ( is_array( $plugins ) ) {
			return array_diff( $plugins, array( 'wpemoji' ) );
		}
		return array();
	}

	/**
	 * Remove emoji CDN hostname from DNS prefetching hints.
	 *
	 * @param  array  $urls          URLs to print for resource hints.
	 * @param  string $relation_type The relation type the URLs are printed for.
	 * @return array Difference betwen the two arrays.
	 */
	public static function disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {
		if ( 'dns-prefetch' === $relation_type ) {
			$emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );
			$urls          = array_diff( $urls, array( $emoji_svg_url ) );
		}
		return $urls;
	}

	/**
	 * Disable embeds.
	 *
	 * @return void
	 */
	public static function disable_embeds() {
		// Remove the REST API endpoint.
		remove_action( 'rest_api_init', 'wp_oembed_register_route' );

		// Turn off oEmbed auto discovery.
		add_filter( 'embed_oembed_discover', '__return_false' );

		// Don't filter oEmbed results.
		remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );

		// Remove oEmbed discovery links.
		remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );

		// Remove oEmbed-specific JavaScript from the front-end and back-end.
		remove_action( 'wp_head', 'wp_oembed_add_host_js' );

		// Remove all embeds rewrite rules.
		add_filter( 'rewrite_rules_array', array( __CLASS__, 'disable_embeds_rewrites' ) );
	}

	/**
	 * Remove embeds rewrite rules.
	 *
	 * @param  array $rules WordPress rewrite rules.
	 * @return array Filtered rewrite rules.
	 */
	public static function disable_embeds_rewrites( $rules ) {
		foreach ( $rules as $rule => $rewrite ) {
			if ( false !== strpos( $rewrite, 'embed=true' ) ) {
				unset( $rules[ $rule ] );
			}
		}
		return $rules;
	}

	/**
	 * Disable dashicons on frontend for non-logged-in users.
	 *
	 * @return void
	 */
	public static function disable_dashicons() {
		if ( ! is_user_logged_in() ) {
			wp_dequeue_style( 'dashicons' );
			wp_deregister_style( 'dashicons' );
		}
	}

	/**
	 * Remove query strings from static resources.
	 *
	 * @param  string $src Resource URL.
	 * @return string Filtered resource URL.
	 */
	public static function remove_query_strings( $src ) {
		if ( strpos( $src, '?ver=' ) ) {
			$src = remove_query_arg( 'ver', $src );
		}
		return $src;
	}

	/**
	 * Optimize REST API responses by removing unnecessary data.
	 *
	 * @param  WP_REST_Response $response The response object.
	 * @return WP_REST_Response Modified response object.
	 */
	public static function optimize_rest_response( $response ) {
		$settings = get_option( 'headless_lock_settings', array() );

		// Get response data.
		$data = $response->get_data();

		// Remove _links if enabled.
		if ( ! empty( $settings['remove_rest_links'] ) ) {
			unset( $data['_links'] );
		}

		// Remove specific fields if configured.
		$fields_to_remove = array();

		if ( ! empty( $settings['remove_rest_guid'] ) ) {
			$fields_to_remove[] = 'guid';
		}

		if ( ! empty( $settings['remove_rest_ping_status'] ) ) {
			$fields_to_remove[] = 'ping_status';
		}

		if ( ! empty( $settings['remove_rest_comment_status'] ) ) {
			$fields_to_remove[] = 'comment_status';
		}

		foreach ( $fields_to_remove as $field ) {
			unset( $data[ $field ] );
		}

		// Set modified data.
		$response->set_data( $data );

		return $response;
		// phpcs:enable VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	}

	/**
	 * Disable heartbeat API.
	 *
	 * @return void
	 */
	public static function disable_heartbeat() {
		$settings = get_option( 'headless_lock_settings', array() );

		if ( ! empty( $settings['heartbeat_location'] ) ) {
			$location = $settings['heartbeat_location'];

			// Completely disable heartbeat.
			if ( 'disable' === $location ) {
				wp_deregister_script( 'heartbeat' );
			}

			// Allow only in post editor.
			if ( 'allow_posts' === $location && ! ( 'post.php' === $GLOBALS['pagenow'] || 'post-new.php' === $GLOBALS['pagenow'] ) ) {
				wp_deregister_script( 'heartbeat' );
			}
		}
	}
}
