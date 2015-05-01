<?php
if ( $true == true || $false == false ) {  // Bad x 2 (in an 'if')
	echo 'True';
} elseif ( $true == true && $false == true ) { // Bad x 2 (in an 'elseif')
	echo 'False';
} elseif ( false == $true && true == $false ) { // Good - this is the correct way to do conditional checks
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

// make sure the test excludes functions on the conditional check
if ( strtolower( $check ) == $true  ) { // Good
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

// Testing constant comparison.
if ( $taxonomy === MyClass::TAXONOMY_SLUG ) { // Bad
	$link = true;
} elseif ( MyClass::TAXONOMY_SLUG === $taxonomy ) { // OK
	$link = false;
}

if ( $foo === FOO_CONSTANT ) { // Bad
	$link = true;
} elseif ( FOO_CONSTANT === $foo ) { // OK
	$link = false;
}

if ( $foo == $bar ) {} // OK

$accessibility_mode = ( 'on' === sanitize_key( $_GET['accessibility-mode'] ) ) ? 'on' : 'off'; // OK

if ( $on !== self::$network_mode ) { // OK
	self::$network_mode = (bool) $on;
}

return 0 === strpos( $foo, 'a' ); // OK
return 0 == $foo; // OK
return $foo == 0; // Bad
