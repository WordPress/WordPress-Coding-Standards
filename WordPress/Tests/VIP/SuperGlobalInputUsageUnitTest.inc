<?php

foo( $_GET['bar'] );

foo( $_GET['whitelisted'] ); // input var okay

foo( $_POST['whitelisted_with_prefix'] ); // WPCS: input var okay.

if ( $_GET['test'] && foo() && $bar ) { // input var okay
	taz();
}

bar( $_POST['foo'] ); // warning

quux( $_REQUEST['quux'] ); // warning

$_REQUEST['wp_customize'] = 'on'; // ok

// Issue: https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/542
if ( isset( $_GET['updated'] ) ) { // input var okay ?>

<?php
