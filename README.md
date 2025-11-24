# Headless Lock Pro

![Version](https://img.shields.io/badge/version-2.1.0-blue.svg)
![WordPress](https://img.shields.io/badge/WordPress-5.8+-green.svg)
![PHP](https://img.shields.io/badge/PHP-7.4+-purple.svg)
![License](https://img.shields.io/badge/license-GPL--2.0+-red.svg)

Transform WordPress into a true headless CMS with customizable redirects, security enhancements, and performance optimizations. Perfect for Next.js, React, and Vue.js frontends.

[📋 View Changelog](CHANGELOG.md)

## Features

### Frontend Management

- **Block Frontend Access**: Completely disable public frontend while keeping admin and APIs functional
- **Smart Redirects**: 301 redirect to your headless frontend application
- **Custom 404 Page**: Beautiful, customizable 404 page with developer-friendly messaging
- **JSON Responses**: Automatic JSON responses for API requests
- **Whitelist System**: Allow specific paths through filters

### Security Enhancements

- Disable XML-RPC to prevent attacks
- Remove WordPress version information
- Disable RSS feeds (not needed in headless)
- Disable file editor in WordPress admin
- Add security headers (X-Frame-Options, CSP, etc.)
- Optional REST API access control
- Content Security Policy support

### Performance Optimizations

- Remove unnecessary WordPress head tags
- Disable emoji scripts and styles
- Disable embeds and oEmbed
- Remove dashicons from frontend
- Remove query strings from static resources
- Optimize REST API responses
- Limit post revisions
- Control Heartbeat API behavior

## Installation

### From WordPress.org

1. Go to **Plugins > Add New**
2. Search for **"Headless Lock Pro"**
3. Click **Install Now** and then **Activate**
4. Configure at **Settings > Headless Lock Pro**

### Manual Installation

1. Download the latest release
2. Upload to `/wp-content/plugins/headless-lock-pro/`
3. Activate through the **Plugins** menu
4. Configure at **Settings > Headless Lock Pro**

### Composer

```bash
composer require suleman-se/headless-lock-pro
```

## Quick Start

1. **Install and activate** the plugin
2. **Go to Settings > Headless Lock Pro**
3. **Choose your setup**:
   - Enable redirect and enter your frontend URL, OR
   - Customize the 404 message
4. **Enable security features** (recommended)
5. **Enable performance optimizations** (optional but recommended)
6. **Save settings**

That's it! Your WordPress is now running in headless mode.

## Configuration

### Redirect Settings

Redirect all frontend requests to your headless application:

```php
// In settings or via filter
add_filter( 'headless_lock_default_settings', function( $settings ) {
    $settings['redirect_enabled'] = true;
    $settings['redirect_url'] = 'https://yourfrontend.com';
    return $settings;
} );
```

### Custom 404 Page

Customize the fallback 404 page:

- Page title
- Heading
- Description
- Show/hide REST API URL
- Show/hide admin link

## Developer Hooks

### Whitelist Custom Paths

```php
add_filter( 'headless_lock_whitelist_paths', function( $paths ) {
    $paths[] = '/webhooks/';
    $paths[] = '/api/custom/';
    return $paths;
} );
```

### Customize Public REST API Routes

```php
add_filter( 'headless_lock_public_rest_routes', function( $routes ) {
    $routes[] = '/wp/v2/products';
    $routes[] = '/wc/v3/products';
    return $routes;
} );
```

### Modify Default Settings

```php
add_filter( 'headless_lock_default_settings', function( $settings ) {
    $settings['disable_xmlrpc'] = true;
    $settings['remove_wp_version'] = true;
    $settings['disable_emojis'] = true;
    return $settings;
} );
```

## What Gets Blocked?

The plugin blocks:
- All public frontend pages
- Theme template rendering
- Homepage, posts, pages, archives

## What Stays Accessible?

The plugin allows:
- Admin dashboard (`/wp-admin/`)
- REST API (`/wp-json/`)
- GraphQL endpoint (`/graphql`)
- AJAX requests
- WP-CLI commands
- WP-Cron jobs
- Webhook endpoints
- Custom whitelisted paths

## Use Cases

### Next.js with WordPress

```javascript
// Next.js API route
export async function getStaticProps() {
  const res = await fetch('https://your-wp-site.com/wp-json/wp/v2/posts');
  const posts = await res.json();

  return {
    props: { posts }
  };
}
```

### React Application

```javascript
// Fetch posts in React
useEffect(() => {
  fetch('https://your-wp-site.com/wp-json/wp/v2/posts')
    .then(res => res.json())
    .then(data => setPosts(data));
}, []);
```

### Vue.js

```javascript
// Fetch in Vue
export default {
  async mounted() {
    const response = await fetch('https://your-wp-site.com/wp-json/wp/v2/posts');
    this.posts = await response.json();
  }
}
```

## Requirements

- WordPress 5.8 or higher
- PHP 7.4 or higher
- Modern browser for admin interface

## Compatibility

Works great with:
- WPGraphQL
- Advanced Custom Fields (ACF)
- Yoast SEO
- WooCommerce REST API
- Custom post types and taxonomies
- JWT authentication plugins

## Security Best Practices

When running WordPress in headless mode:

1. **Enable security headers** in plugin settings
2. **Use HTTPS** for all communications
3. **Implement authentication** for sensitive endpoints
4. **Limit REST API access** if not needed publicly
5. **Keep WordPress core and plugins updated**
6. **Use strong admin passwords**
7. **Consider JWT authentication** for API requests

## Performance Tips

1. **Enable all performance optimizations** in settings
2. **Use caching** for REST API responses
3. **Implement CDN** for media files
4. **Optimize images** before uploading
5. **Limit post revisions** to reduce database size
6. **Disable Heartbeat API** if not needed

## Troubleshooting

### Frontend is still accessible

- Ensure plugin is activated
- Check for conflicting plugins
- Clear all caches (WordPress, server, CDN)
- Check if path is whitelisted

### REST API not working

- Check if REST API is disabled by another plugin
- Verify permalink settings (re-save if needed)
- Check `.htaccess` file for conflicts

### Admin dashboard redirecting

- The admin should never be affected
- Clear browser cache and cookies
- Check for theme/plugin conflicts

## Contributing

Contributions are welcome! Please:

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Write/update tests if applicable
5. Submit a pull request

## Support

- **Issues**: [GitHub Issues](https://github.com/suleman-se/headless-lock-pro/issues)
- **Discussions**: [GitHub Discussions](https://github.com/suleman-se/headless-lock-pro/discussions)
- **WordPress Support**: [WordPress.org Support Forums](https://wordpress.org/support/plugin/headless-lock-pro/)

## Changelog

### Version 2.1.0 (2025-11-24)

- Made security features conditional based on settings
- Added security headers and feed disabling options
- Added customizable post revisions limit
- Improved project infrastructure with Composer, CI, and tests
- Fixed various linting and formatting issues

For a full list of changes, see [CHANGELOG.md](CHANGELOG.md).

### Version 2.0.0 (2024-11-24)

- Complete rewrite with OOP architecture
- Added comprehensive security features
- Added performance optimization tools
- Improved admin interface
- Added extensive developer hooks
- Enhanced documentation
- Translation ready

### Version 1.0.0

- Initial release

## Roadmap

- [ ] GraphQL-specific optimizations
- [ ] Advanced caching controls
- [ ] Preview mode for editors
- [ ] Multisite support
- [ ] REST API rate limiting
- [ ] Custom endpoint builder
- [ ] Analytics integration

## License

This plugin is licensed under the [GPL v2 or later](https://www.gnu.org/licenses/gpl-2.0.html).

## Author

**M. Suleman**
- LinkedIn: [linkedin.com/in/m-suleman-khan](https://www.linkedin.com/in/m-suleman-khan/)
- GitHub: [github.com/suleman-se](https://github.com/suleman-se)

## Credits

Built with love for the headless WordPress community.

---

If you find this plugin helpful, please consider:
- Leaving a review on WordPress.org
- Starring the GitHub repository
- Sharing with other developers
- Contributing to the codebase

**Made with ❤️ for headless WordPress**
