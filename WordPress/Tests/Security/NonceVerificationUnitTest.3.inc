<?php

/*
 * Verify a correct nonce check (way) _before_ in the global scope.
 */
wp_verify_nonce( $_POST['prefix_nonce'] );

do_something();
do_something();
do_something();
do_something();
do_something();
do_something();
do_something();
do_something();
do_something();
do_something();
do_something();
do_something();
do_something();
do_something();
do_something();
do_something();
do_something();
do_something();

echo $_POST['prefix_myfield']; // OK.
