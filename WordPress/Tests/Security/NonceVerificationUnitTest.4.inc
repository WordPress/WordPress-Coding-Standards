<?php

/*
 * Verify a missing nonce check _before_ in the global scope.
 */
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

echo $_GET['prefix_myfield']; // Bad.
