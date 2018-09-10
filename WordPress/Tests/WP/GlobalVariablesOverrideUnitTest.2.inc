<?php
/**
 * Unit test cases for variable overrides in the global namespace *without* global statement.
 *
 * This file should contain the same tests as the sister-file GlobalVariablesOverrideUnitTest.3.inc.
 * For this file, all the variables overrides should throw errors, for the sister-file they shouldn't.
 */

// Placeholder for the setting change in the sister file.

// Overrides in the global namespace should be detected no matter what. No need for a `global` statement.
$pagenow = 'abc'; // Bad.
$menu = new WP_Query(); // Bad.

// Variant of issue #1236 - detect overriding WP variables in control structure conditions.
if ( ( $blogname = function_call() ) === true ) {} // Bad.
foreach ( $something as $cat ) {} // Bad.
foreach ( $something as $orderby => $order ) {} // Bad x 2.
while ( ( $posts = function_call() ) === true ) {} // Bad.
for ( $totals = 0; $totals < 10; $totals++ ) {} // Bad.

switch( true ) {
	case ($year = 'abc'): // Bad.
		break;
}

$domain['subkey'] = 'something else'; // Bad.

$GLOBALS['domain']['subkey'] = 'something else'; // Bad.
