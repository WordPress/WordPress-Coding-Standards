<?php

// Bad, no space after opening control structure
while( have_posts() ) { // Okay, comments are okay here
	// Okay, comments are okay here as well
} // Okay, comments are okay here

// See https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/40
if ( true ) {

	// code
} else { //Are we allowed to comment here? If not, message is wrong
	// ...
}

// bad
if  ( 'update' === $option_operation['operation'] )
{
	update_option( $option_operation['option_name'], $option_operation['old_value'] );

}

// good
if ( 'update' === $option_operation['operation'] ) {
	update_option( $option_operation['option_name'], $option_operation['old_value'] );
}

// bad
if(true){}


if ( true ) {
    while ( false ) {

        echo 'OK';

    }

}

if ( false ):
else:
endif;

if ( false ) :
else :
endif;
