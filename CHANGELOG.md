# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.1.0] - 2025-11-24

### Added

- Conditional security features: XML-RPC disable, WordPress version removal, and user REST API endpoint limits are now optional based on settings.
- New security implementations: Added `add_security_headers` to send security headers (X-Frame-Options, X-Content-Type-Options, etc.) when enabled.
- Feed disabling: Added `disable_feeds` to block RSS feeds when the setting is enabled.
- Post revisions limit: Added a number input field for customizing the post revisions limit (default 5) tied to the existing checkbox.
- Project infrastructure: Added `composer.json` for dependency management, `LICENSE` file, `.gitignore`, GitHub Actions CI workflow, and basic PHPUnit tests.
- Changelog: Created this CHANGELOG.md file for tracking changes.

### Changed

- Security Manager: Refactored to respect user settings instead of applying features unconditionally.
- Admin Settings: Enhanced with additional input fields for better customization.
- README.md: Updated with version badges, added changelog link, and improved formatting.
- CI/CD: Added automated testing and linting via GitHub Actions.

### Fixed

- Markdown linting issues in README.md (added blank lines around headings and lists).
- Plugin distribution: Ensured only relevant files are included in the ZIP for distribution.

### Developer Notes

- Added Composer support for easier dependency management.
- Introduced basic unit tests for core functionality.
- Improved code structure and documentation.

## [2.0.0] - 2024-11-24

### Added

- Complete rewrite with OOP architecture
- Frontend blocking with customizable redirects
- Custom 404 page for headless mode
- Security enhancements (XML-RPC disable, version removal, etc.)
- Performance optimizations (remove head tags, disable emojis, etc.)
- Admin settings page with organized sections
- REST API and GraphQL support
- Webhook and WP-CLI compatibility

### Changed

- Improved user experience with better messaging
- Enhanced filter system for customization

### Fixed

- Various bugs and compatibility issues

## [1.0.0] - Initial Release

- Basic headless mode functionality
- Frontend blocking
- Simple redirect options
