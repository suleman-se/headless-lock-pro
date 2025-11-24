=== Headless Lock Pro ===
Contributors: msulemandev
Tags: headless, cms, rest-api, nextjs, react, vue, decoupled, jamstack, security, performance
Requires at least: 5.8
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 2.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Transform WordPress into a true headless CMS with customizable redirects, security enhancements, and performance optimizations. Perfect for Next.js, React, and Vue.js frontends.

== Description ==

**Headless Lock Pro** is the ultimate solution for running WordPress as a headless CMS. It blocks frontend access while keeping the REST API, admin dashboard, and GraphQL endpoints fully functional. Perfect for developers building modern frontends with Next.js, React, Vue.js, or any other JavaScript framework.

= Key Features =

**Frontend Management**
* Block all frontend access to WordPress
* Customizable 301 redirects to your headless frontend
* Beautiful custom 404 page with configurable messaging
* Automatic JSON responses for API requests
* Smart whitelist system for specific paths

**Security Enhancements**
* Disable XML-RPC to prevent attacks
* Remove WordPress version information
* Disable RSS feeds (not needed in headless mode)
* Disable file editor in admin
* Add security headers (X-Frame-Options, X-Content-Type-Options, etc.)
* Optional REST API access control
* Content Security Policy support

**Performance Optimizations**
* Remove unnecessary WordPress head tags
* Disable emojis and emoji scripts
* Disable embeds and oEmbed
* Remove dashicons from frontend
* Remove query strings from static resources
* Optimize REST API responses
* Limit post revisions
* Control Heartbeat API

**Developer Friendly**
* Extensive filter hooks for customization
* Allow REST API, GraphQL, and webhook endpoints
* Support for WP-CLI and WP-Cron
* Translation ready
* Clean, well-documented code
* PSR-4 autoloading compatible

= Perfect For =

* Next.js websites using WordPress as a data source
* React applications with WordPress backend
* Vue.js frontends powered by WordPress
* Gatsby and other static site generators
* Mobile apps using WordPress REST API
* Headless WordPress development

= How It Works =

1. Install and activate the plugin
2. Configure your redirect URL or customize the 404 message
3. Enable security and performance features as needed
4. Your WordPress site is now a headless CMS!

The plugin automatically allows:
* Admin dashboard (`/wp-admin/`)
* REST API (`/wp-json/`)
* GraphQL endpoint (`/graphql`)
* AJAX requests
* WP-CLI commands
* WP-Cron tasks
* Custom whitelisted paths (via filters)

= Filters for Developers =

`
// Add custom whitelisted paths
add_filter( 'headless_lock_whitelist_paths', function( $paths ) {
    $paths[] = '/custom-endpoint/';
    return $paths;
} );

// Customize public REST API routes
add_filter( 'headless_lock_public_rest_routes', function( $routes ) {
    $routes[] = '/wp/v2/custom-post-type';
    return $routes;
} );

// Customize default settings
add_filter( 'headless_lock_default_settings', function( $settings ) {
    $settings['redirect_enabled'] = 1;
    $settings['redirect_url'] = 'https://yourfrontend.com';
    return $settings;
} );
`

= Requirements =

* WordPress 5.8 or higher
* PHP 7.4 or higher
* A headless frontend application (optional)

== Installation ==

= Automatic Installation =

1. Log in to your WordPress admin panel
2. Navigate to Plugins > Add New
3. Search for "Headless Lock Pro"
4. Click "Install Now" and then "Activate"
5. Go to Settings > Headless Lock Pro to configure

= Manual Installation =

1. Download the plugin ZIP file
2. Log in to your WordPress admin panel
3. Navigate to Plugins > Add New > Upload Plugin
4. Choose the downloaded ZIP file and click "Install Now"
5. Activate the plugin
6. Go to Settings > Headless Lock Pro to configure

= From GitHub =

1. Clone or download from GitHub repository
2. Upload the `headless-lock-pro` folder to `/wp-content/plugins/`
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Go to Settings > Headless Lock Pro to configure

== Frequently Asked Questions ==

= Will this break my WordPress admin? =

No! The admin dashboard (`/wp-admin/`) remains fully accessible. Only the public frontend is blocked.

= Can I still use the REST API? =

Yes! The REST API (`/wp-json/`) is fully accessible. You can optionally enable access control for specific endpoints.

= Does this work with GraphQL? =

Yes! GraphQL endpoints are automatically whitelisted and remain accessible.

= Can I redirect to my Next.js or React app? =

Absolutely! Just enable the redirect option and enter your frontend URL in the settings.

= How do I whitelist custom paths? =

Use the `headless_lock_whitelist_paths` filter hook:

`
add_filter( 'headless_lock_whitelist_paths', function( $paths ) {
    $paths[] = '/my-custom-path/';
    return $paths;
} );
`

= Will this affect my WP-Cron jobs? =

No! WP-Cron, AJAX requests, and WP-CLI commands continue to work normally.

= Can I customize the 404 page? =

Yes! You can customize the title, heading, description, and choose what information to display.

= Is this compatible with caching plugins? =

Yes! Since you're running headless, frontend caching becomes less relevant. Focus on caching your REST API responses instead.

= What happens to my site if I deactivate the plugin? =

Your WordPress site returns to normal frontend operation immediately. Settings are preserved if you reactivate later.

= Does this plugin delete any data on uninstall? =

Yes, plugin settings are removed on uninstall. Content (posts, pages, media) is never affected.

== Screenshots ==

1. Main settings page with all options
2. Redirect configuration
3. Custom 404 message settings
4. Security enhancements panel
5. Performance optimization options
6. Custom 404 page example

== Changelog ==

= 2.1.0 =
* Made security features conditional based on settings
* Added security headers and feed disabling options
* Added customizable post revisions limit
* Improved project infrastructure with Composer, CI, and tests
* Fixed various linting and formatting issues

= 2.0.0 - 2024-11-24 =
* Complete rewrite with object-oriented architecture
* Added comprehensive security features
* Added performance optimization tools
* Improved admin settings interface
* Added extensive filter hooks for developers
* Added support for custom whitelisted paths
* Added REST API access control options
* Added security headers support
* Added Content Security Policy support
* Improved code organization and documentation
* Added translation support
* Performance improvements

= 1.0.0 =
* Initial release
* Basic frontend blocking
* Simple redirect functionality
* Custom 404 page

== Upgrade Notice ==

= 2.0.0 =
Major update with new features and improved architecture. Backup your settings before upgrading. Previous settings will be migrated automatically.

== Support ==

For support, feature requests, or bug reports:
* GitHub: https://github.com/suleman-se/headless-lock-pro
* WordPress Support Forums

== Contributing ==

Contributions are welcome! Please visit our GitHub repository to:
* Report bugs
* Suggest features
* Submit pull requests

== Privacy Policy ==

Headless Lock Pro does not collect, store, or transmit any user data. All settings are stored locally in your WordPress database.

== Credits ==

Developed by M. Suleman
LinkedIn: https://www.linkedin.com/in/m-suleman-khan/

== License ==

This plugin is licensed under the GPLv2 or later.
