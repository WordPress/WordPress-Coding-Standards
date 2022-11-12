<?php
/**
 * Unit test cases for variable overrides in the global namespace *without* global statement.
 *
 * This file should contain the same tests as the sister-file GlobalVariablesOverrideUnitTest.2.inc.
 * For this file, none of the variables overrides should throw errors, for the sister-file they all should.
 */

// phpcs:set WordPress.WP.GlobalVariablesOverride treat_files_as_scoped true

// Overrides in the global namespace should be detected no matter what. No need for a `global` statement.
$pagenow = 'abc'; // OK.
$menu = new WP_Query(); // OK.

// Variant of issue #1236 - detect overriding WP variables in control structure conditions.
if ( ( $blogname = function_call() ) === true ) {} // OK.
foreach ( $something as $cat ) {} // OK.
foreach ( $something as $orderby => $order ) {} // Bad x 2.
while ( ( $posts = function_call() ) === true ) {} // OK.
for ( $totals = 0; $totals < 10; $totals++ ) {} // OK.

switch( true ) {
	case ($year = 'abc'): // OK.
		break;
}

$domain['subkey'] = 'something else'; // OK.

$GLOBALS['domain']['subkey'] = 'something else'; // Still bad.

// phpcs:set WordPress.WP.GlobalVariablesOverride treat_files_as_scoped false
