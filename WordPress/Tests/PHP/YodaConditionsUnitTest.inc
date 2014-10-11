<?php
if ( $true == true || $false == false ) {  // Bad x 2 (in an 'if')
	echo 'True';
} elseif ( $true == true && $false == true ) { // Bad x 2 (in an 'elseif')
	echo 'False';
} elseif ( false == $true && true == $false ) { // Good - this is the correct was to do conditional checks
	echo 'False';
}

// test for 'equals' conditional
if ( $true == true ) { // Bad x 1
	echo 'True';
} elseif ( false == $true ) { // Good
	echo 'False';
}

// test for 'not equals' conditional
if ( $true != true ) { // Bad x 1
	echo 'True';
} elseif ( false != $true ) { // Good
	echo 'False';
}

// test for 'exactly equals' conditional
if ( $true === true ) { // Bad x 1
	echo 'True';
} elseif ( false === $true ) { // Good
	echo 'False';
}

// test for 'not exactly equals' conditional
if ( $true !== true ) { // Bad x 1
	echo 'True';
} elseif ( false !== $true ) { // Good
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
if ( $true == 'true' ) { // Bad x 1
	echo 'True';
} elseif ( 'false' == $true ) { // Good x 1
	echo 'False';
}
// testing for integer comparison
if ( $true == 0 ) { // Bad x 1
	echo 'True';
} elseif ( 1 == $false ) { // Good x 1
	echo 'False';
}
