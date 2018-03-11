<?php

add_action( 'widgets_init', create_function( '', // Error.
	'return register_widget( "time_more_on_time_widget" );'
) );
