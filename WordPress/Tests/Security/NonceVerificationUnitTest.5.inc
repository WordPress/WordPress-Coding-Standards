<?php

/*
 * Verify an allowed nonce check _after_ in the global scope.
 */

if ( isset ( $_POST['prefix_myfield'] ) { // OK.
	
	do_something();
	do_something();
	do_something();
	do_something();
	do_something();
	do_something();

	wp_verify_nonce( $_POST['prefix_nonce'] );
	echo $_POST['prefix_myfield']; // OK.
}
