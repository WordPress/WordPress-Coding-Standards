<?php

add_action( 'widgets_init', create_function( '', // Error.
	'return register_widget( "time_more_on_time_widget" );'
) );

$post_data['post_title'] = sprintf( __( 'Draft created on %1$s at %2$s' ), date( __( 'F j, Y' ), $now ), date( __( 'g:i a' ), $now ) ); // Error.
$post_data['post_title'] = sprintf( __( 'Draft created on %1$s at %2$s' ), gmdate( __( 'F j, Y' ), $now ), gmdate( __( 'g:i a' ), $now ) ); // OK.
