<?php

/*
 * Separate test file to isolate the parse error test.
 */

// Verify skipping over rest of the function when live coding/parse error in nested scope structure.
function global_vars() {
	global $pagenow;

	$closure = function ( $pagenow ) {
		global $feeds;

		$nested_closure_with_parse_error = function ( $feeds )

		$feeds = 'something'; // Bad, but ignored because of the parse error in the closure.
	};

	$pagenow = 'something'; // Bad, should be picked up. Tests that skipping on parse error doesn't skip too far.
}
