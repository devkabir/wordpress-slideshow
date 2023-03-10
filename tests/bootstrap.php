<?php

// Autoload everything for unit tests.
$ds = DIRECTORY_SEPARATOR;
require_once dirname( __FILE__, 2 ) . $ds . 'vendor' . $ds . 'autoload.php';

/**
 * Include core bootstrap for an integration test suite
 *
 * This will only work if you run the tests from the command line.
 * Running the tests from IDE such as PhpStorm will require you to
 * add additional argument to the test run command if you want to run
 * integration tests.
 */
if ( ! file_exists( dirname( __FILE__, 2 ) . '/wp/tests/phpunit/wp-tests-config.php' ) ) {
	// We need to set up core config details and test details
	copy(
		dirname( __FILE__, 2 ) . '/wp/wp-tests-config-sample.php',
		dirname( __FILE__, 2 ) . '/wp/tests/phpunit/wp-tests-config.php'
	);

	// Change certain constants from the test's config file.
	$testConfigPath     = dirname( __FILE__, 2 ) . '/wp/tests/phpunit/wp-tests-config.php';
	$testConfigContents = file_get_contents( $testConfigPath );

	$testConfigContents = str_replace(
		"dirname( __FILE__ ) . '/src/'",
		"dirname(__FILE__, 3) . '/src/'",
		$testConfigContents
	);
	$testConfigContents = str_replace( 'youremptytestdbnamehere', $_SERVER['DB_NAME'], $testConfigContents );
	$testConfigContents = str_replace( 'yourusernamehere', $_SERVER['DB_USER'], $testConfigContents );
	$testConfigContents = str_replace( 'yourpasswordhere', $_SERVER['DB_PASSWORD'], $testConfigContents );
	$testConfigContents = str_replace( 'localhost', $_SERVER['DB_HOST'], $testConfigContents );

	file_put_contents( $testConfigPath, $testConfigContents );
}

// Give access to tests_add_filter() function.
require_once dirname( __FILE__, 2 ) . '/wp/tests/phpunit/includes/functions.php';

define( 'WP_PLUGIN_DIR', dirname( __DIR__, 2 ) );
$GLOBALS['wp_tests_options'] = array(
	'active_plugins' => array( 'wordpress-slideshow/wordpress-slideshow.php' ),
);


require_once dirname( __FILE__, 2 ) . '/wp/tests/phpunit/includes/bootstrap.php';
