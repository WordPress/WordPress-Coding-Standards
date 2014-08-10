<?php
if ( $true == true || $false == false ) {  // Bad x 2 (in an 'if')
	echo 'True';
} elseif ( $true == true && $false == true ) { // Bad x 2 (in an 'elseif')
	echo 'False';
} elseif ( false == $true && true == $false ) { // Good - this is the correct was to do conditional checks
	echo 'False';
}

if ( $true == true ) { // Bax x 1
	echo 'True';
} elseif ( false == $true ) { // Good
	echo 'False';
}
// make sure the test exclues functions on the conditional check
if ( $true == strtolower( $check ) ) { // Good
	echo 'True';
}
// makes sure the test excludes variable casting in the conditional check
if ( true == (bool) $true ) { // Good
	echo 'True';
} elseif ( false == $true ) { // Good
	echo 'False';
}
// testing for string comparison
if ( $true == 'true' ) { // Bad x 1 (the incorrect way)
	echo 'True';
} elseif ( 'false' == $true ) { // Good x 1 (the correct way)
	echo 'False';
}
// testing for integer comparison
if ( $true == 0 ) { // Bad x 1 (the incorrect way)
	echo 'True';
} elseif ( 1 == $false ) { // Good x 1 (the correct way)
	echo 'False';
}
