<?php

// Bad, no space after opening control structure
while( have_posts() ) { // Okay, comments are okay here
	// Okay, comments are okay here as well
} // Okay, comments are okay here

// See https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/40
if ( true ) {

	// code
}
