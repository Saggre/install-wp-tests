<?php

$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

// Forward custom PHPUnit Polyfills configuration to PHPUnit bootstrap file.
$_phpunit_polyfills_path = getenv( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH' );
if ( false !== $_phpunit_polyfills_path ) {
	define( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH', $_phpunit_polyfills_path );
}

if ( ! file_exists( "{$_tests_dir}/includes/functions.php" ) ) {
	echo "Could not find {$_tests_dir}/includes/functions.php, have you run bin/install-wp-tests.sh ?" . PHP_EOL; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	exit( 1 );
}

// Give access to tests_add_filter() function.
require_once "{$_tests_dir}/includes/functions.php";

/**
 * Manually load the plugin being tested.
 *
 * @hooked muplugins_loaded
 */
function _tests_manually_load_plugin() {
	if ( function_exists( '_manually_load_plugin' ) ) {
		call_user_func( '_manually_load_plugin' );
	}
}

tests_add_filter( 'muplugins_loaded', '_tests_manually_load_plugin' );

/**
 * Install WooCommerce if it exists.
 *
 * @hooked setup_theme
 */
function _tests_maybe_install_woocommerce() {
	if ( class_exists( 'WC_Install' ) ) {
		WC_Install::install();
	}
}

tests_add_filter( 'setup_theme', '_tests_maybe_install_woocommerce' );

// Start up the WP testing environment.
require "{$_tests_dir}/includes/bootstrap.php";
