<?php
/**
 * PHPUnit bootstrap file
 */

// Define test constants
define( 'WP_TESTS_DIR', getenv( 'WP_TESTS_DIR' ) ?: '/tmp/wordpress-tests-lib' );

// Load WordPress test functions
require_once WP_TESTS_DIR . '/includes/functions.php';

// Load the plugin
require_once dirname( __DIR__ ) . '/headless-lock-pro.php';

// Start up the WP testing environment
require WP_TESTS_DIR . '/includes/bootstrap.php';