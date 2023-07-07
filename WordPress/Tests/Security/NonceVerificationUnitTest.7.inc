<?php

/*
 * This test is not actually testing anything in the sniff.
 * More than anything, it demonstrates why the cache format change is relevant.
 */

if ( isset ( $_POST['prefix_myfield'] ) { // OK.

	do_something();
	do_something();
	do_something();
	do_something();
	do_something();
	do_something();

	echo $_POST['prefix_myfield']; // Bad.

	do_something();
	do_something();
	do_something();

	echo $_POST['prefix_myfield']; // Bad.
}

function this_would_previously_reset_the_cache() {
	if (
		'newsletter_sign_up' === $_POST['action'] && // OK.
		wp_verify_nonce( $_POST['newsletter_nonce'] )
	) {}
}

// With the change made to the caching, the in-file search will now continue on line 17 instead of start from 0 again.
do_something();
do_something();
do_something();
do_something();
do_something();
do_something();

wp_verify_nonce( $_POST['prefix_nonce'] );
echo $_POST['prefix_myfield']; // OK.

function this_would_previously_reset_the_cache_again() {
	if (
		'newsletter_sign_up' === $_POST['action'] && // OK.
		wp_verify_nonce( $_POST['newsletter_nonce'] )
	) {}
}

// With the change made to the caching, no in-search file search is needed here as we already found a nonce-check on line 35.
if ( isset ( $_POST['prefix_myfield'] ) { // OK.

	do_something();
	do_something();
	do_something();
	do_something();
	do_something();
	do_something();

	echo $_POST['prefix_myfield']; // OK.
}
