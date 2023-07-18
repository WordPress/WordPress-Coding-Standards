<?php

/*
 * Verify that a nonce check in the global scope will not be accepted as valid for superglobal use in function local scope.
 */

// Test anonymous function - Bad, needs nonce check.
check_ajax_referer( 'something' ); // Nonce check is not in function scope.
$b = function () {
	if ( ! isset( $_POST['abc'] ) ) { // Bad.
		return;
	}

	do_something( $_POST['abc'] ); // Bad.
};
