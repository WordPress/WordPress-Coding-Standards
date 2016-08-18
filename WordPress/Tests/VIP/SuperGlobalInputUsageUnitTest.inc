<?php

foo( $_GET['bar'] );

foo( $_GET['whitelisted'] ); // Input var okay.

foo( $_POST['whitelisted_with_prefix'] ); // WPCS: input var okay.

if ( $_GET['test'] && foo() && $bar ) { // input var okay
	taz();
}

bar( $_POST['foo'] ); // Warning.

quux( $_REQUEST['quux'] ); // Warning.

$_REQUEST['wp_customize'] = 'on'; // Ok.

// Issue: https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/542
if ( isset( $_GET['updated'] ) ) { // input var okay ?>
