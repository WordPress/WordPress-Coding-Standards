<?php
if ( $true == true || $false == false ) {  // Bad x 2 (in an 'if').
	echo 'True';
} elseif ( $true == true && $false == true ) { // Bad x 2 (in an 'elseif').
	echo 'False';
} elseif ( false == $true && true == $false ) { // Good - this is the correct way to do conditional checks.
	echo 'False';
}

// Test for 'equals' conditional.
if ( $true == true ) { // Bad x 1.
	echo 'True';
} elseif ( false == $true ) { // Good.
	echo 'False';
}

// Test for 'not equals' conditional.
if ( $true != true ) { // Bad x 1.
	echo 'True';
} elseif ( false != $true ) { // Good.
	echo 'False';
}

// Test for 'exactly equals' conditional.
if ( $true === true ) { // Bad x 1.
	echo 'True';
} elseif ( false === $true ) { // Good.
	echo 'False';
}

// Test for 'not exactly equals' conditional.
if ( $true !== true ) { // Bad x 1.
	echo 'True';
} elseif ( false !== $true ) { // Good.
	echo 'False';
}

// Make sure the test excludes functions on the conditional check.
if ( strtolower( $check ) == $true  ) { // Good.
	echo 'True';
}
// Makes sure the test excludes variable casting in the conditional check.
if ( true == (bool) $true ) { // Good.
	echo 'True';
} elseif ( false == $true ) { // Good.
	echo 'False';
}
// Testing for string comparison.
if ( $true == 'true' ) { // Bad x 1.
	echo 'True';
} elseif ( 'false' == $true ) { // Good x 1.
	echo 'False';
}
// Testing for integer comparison.
if ( $true == 0 ) { // Bad x 1.
	echo 'True';
} elseif ( 1 == $false ) { // Good x 1.
	echo 'False';
}

// Testing constant comparison.
if ( $taxonomy === MyClass::TAXONOMY_SLUG ) { // Bad.
	$link = true;
} elseif ( MyClass::TAXONOMY_SLUG === $taxonomy ) { // Ok.
	$link = false;
}

if ( $foo === FOO_CONSTANT ) { // Bad.
	$link = true;
} elseif ( FOO_CONSTANT === $foo ) { // Ok.
	$link = false;
}

if ( $foo == $bar ) {} // Ok.

$accessibility_mode = ( 'on' === sanitize_key( $_GET['accessibility-mode'] ) ) ? 'on' : 'off'; // Ok.

if ( $on !== self::$network_mode ) { // Ok.
	self::$network_mode = (bool) $on;
}

return 0 === strpos( $foo, 'a' ); // Ok.
return 0 == $foo; // Ok.
return $foo == 0; // Bad.

if ( (int) $a['interval'] === (int) $b['interval'] ) {} // Ok.

if ( $GLOBALS['wpdb']->num_rows === 0 ) {} // Bad.

if ( $true == strtolower( $check ) ) {} // Bad.

$update = 'yes' === strtolower( $this->from_post( 'update' ) ); // Ok.
