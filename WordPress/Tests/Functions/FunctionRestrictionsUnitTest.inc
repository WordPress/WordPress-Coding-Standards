<?php

// Warnings.
add_filter( '', 'foobar' );
add_action( '', 'foobar' );
call_user_func( 'foobar' );
call_user_func_array( 'foobar' );
forward_static_call( 'foobar' );
forward_static_call_array( 'foobar' );
array_diff_uassoc( '', '', 'foobar' );
array_diff_ukey( '', '', 'foobar' );
array_filter( '', 'foobar' );
array_intersect_uassoc( '', 'foobar' );
array_intersect_ukey( '', 'foobar' );
array_map( 'foobar' );
array_reduce( '', 'foobar' );
array_udiff_assoc( array(), 'foobar' );
array_udiff_uassoc( array(), 'foobar', 'barfoo' );
array_udiff( '', 'foobar' );
array_uintersect_assoc( '', 'foobar' );
array_uintersect_uassoc( '', 'foobar', 'barfoo' );
array_uintersect( '', 'foobar' );
array_walk( '', 'foobar' );
array_walk_recursive( '', 'foobar' );
iterator_apply( '', 'foobar' );
usort( '', 'foobar' );
uasort( '', 'foobar' );
uksort( '', 'foobar' );
preg_replace_callback( '', 'foobar' );
mb_ereg_replace_callback( '', 'foobar' );
header_register_callback( 'foobar' );
ob_start( 'foobar' );
set_error_handler( 'foobar' );
set_exception_handler( 'foobar' );
register_shutdown_function( 'foobar' );
register_tick_function( 'foobar' );
