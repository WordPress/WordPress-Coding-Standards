<?php

$posts = get_posts( 'cat=3' );
// Bad, no operator spacing
for ( $i=0; $i<sizeof( $posts ); $i++ ) {
	// ...
}

// Good
for ( $i = 0; $i < sizeof( $posts ); $i++ ) {
	// ...
}
// Good
if ( ! $var ) {
	// ...
}
// Bad
if ( !$var ) {
	// ...
}
