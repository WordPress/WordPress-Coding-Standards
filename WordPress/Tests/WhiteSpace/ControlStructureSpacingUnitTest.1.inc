<?php

// Bad, no space after opening control structure.
while( have_posts() ) { // Okay, comments are okay here.
	// Okay, comments are okay here as well.
} // Okay, comments are okay here.

// See https://github.com/WordPress/WordPress-Coding-Standards/issues/40 .
if ( true ) {

	// code.
} else { // Are we allowed to comment here? If not, message is wrong.
	// ...
}

// Bad.
if  ( 'update' === $option_operation['operation'] )
{
	update_option( $option_operation['option_name'], $option_operation['old_value'] );

}

// Good.
if ( 'update' === $option_operation['operation'] ) {
	update_option( $option_operation['option_name'], $option_operation['old_value'] );
}

// Bad.
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

// Handle try/catch statements as well.
try{ // Bad.
	// Something
} catch(Exception $e){ // Bad.
	// Something
}

// Upstream bug PEAR #20248.
// phpcs:set WordPress.WhiteSpace.ControlStructureSpacing blank_line_check true
// Bad.
if ( $one ) {

}
// phpcs:set WordPress.WhiteSpace.ControlStructureSpacing blank_line_check false

// Upstream bug PEAR #20247.
do {
	echo 'hi';
} while ($blah); // Bad.

// Upstream bug GH #782
// phpcs:set WordPress.WhiteSpace.ControlStructureSpacing blank_line_check true
if ( $foo ) {


    /**
     * Comment
     */
    function foo() {
        // Code here
    }


    /**
     * Comment
     */
    class bar {

    }//end class


}
// phpcs:set WordPress.WhiteSpace.ControlStructureSpacing blank_line_check false

// Check for too many spaces as long as the next non-blank token is on the same line.
if (    'abc' === $test ) { // Bad.
	echo 'hi';
} elseif ( false === $foo    ) { // Bad.
	echo 'bye';
}

do {
	echo 'hi';
} while (   $blah ); // Bad.

while ( $blah   ) { // Bad.
	echo 'bye bye';
}

for (   $i = 0; $i < 1; $i++    ) { // Bad.
	echo 'hi';
}

foreach (   $foo as $bar   ) { // Bad.
	echo 'hi';
}

if (
	'abc' === $test
) { // Ok.
	echo 'hi';
} elseif (
	false === $foo
	&& true === $bar
) { // Ok.
	echo 'bye';
}

// Bug #976 - the case of the disappearing comment.
if ( isset( $submenu_file ) ) {
	if ( $submenu_file == $sub_item[2] ) {
		$class[] = 'current';
	}
// If plugin_page is set the parent must either match the current page or not physically exist.
// This allows plugin pages with the same hook to exist under different parents.
} else {
	$class[] = 'current';
}

// Test finding & fixing blank line after control structure.
if ( $one ) {
}
elseif ( $two ) {
}
// else if something
else if ( $three ) {
} // else do something
else {
}

do {
}
// Comment
while ( $a === $b );

if ( $foo ) {
	try {
		// Something
	} catch ( Exception $e ) {
		// Something
	}


}

if ( $foo ) {
	try {
		// Something
	} catch ( Exception $e ) {
		// Something
	}//end try/catch <- Bad: "blank line after".


}

if ( $foo ) {
	try { // Bad.
		// Something
	} catch ( Exception $e ) {
		// Something
	} // End try/catch <- Bad: "blank line after".


}

// Handle (try/catch/) finally statements as well.
try {
	// Something
} catch ( Exception $e ) {
	// Something
} finally { // OK.
	// Something
}

try {
	// Something
} finally

{ // Bad.
	// Something
}

try {
	// Something
} finally{ // Bad.
	// Something
}

if ( $condition ) {
	try {
		// Something
	} finally {
		// Something
	}

} // Bad: blank line between.

// Handle PHP 8.0+ match expressions.
$expr = match ( $foo ) {
    1 => 1,
    2 => 2,
};

$expr = match($foo){
    1 => 1,
    2 => 2,
} ;

// phpcs:set WordPress.WhiteSpace.ControlStructureSpacing blank_line_check true
$expr = match ( $foo ) {

    1 => 1,
    2 => 2,

};

// phpcs:set WordPress.WhiteSpace.ControlStructureSpacing blank_line_check false

if ( $condition ) {
	$expr = match ( $foo ) {
	    1 => 1,
	    2 => 2,
	};


} // Bad: blank line between.

// Ignore spacing rules in combination with enums as they tend to have their own rules.
// phpcs:set WordPress.WhiteSpace.ControlStructureSpacing blank_line_check true
if ( $foo ) {


	enum MyEnumA {
		// Code here
	}


}
// phpcs:set WordPress.WhiteSpace.ControlStructureSpacing blank_line_check false

enum MyEnumB {
	if ( $foo ) {
		// Code here
	}


} // OK, defer to enum rules.
