<?php

add_action( 'widgets_init', create_function( '', // Warning.
	'return register_widget("time_more_on_time_widget");'
) );

serialize(); // Warning.
unserialize(); // Warning.

urlencode(); // Warning.
rawurlencode(); // Ok.
