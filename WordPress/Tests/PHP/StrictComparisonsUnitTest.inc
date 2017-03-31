<?php
// Test for 'equals' conditionals.
if ( true == $true ) { // Bad.
	echo 'True';
} elseif ( false === $true ) { // Ok.
	echo 'False';
}

// Test for 'not equals' conditionals.
if ( true != $true ) { // Bad.
	echo 'True';
} elseif ( true <> $true ) { // Bad.
	echo 'False';
} elseif ( false !== $true ) { // Ok.
	echo 'False';
}

// Test for whitelisting.
if ( true == $true ) { // Loose comparison, OK.
	echo 'True';
}

// Test that whitelisting is not too eager.
if ( true == $true ) {
	// The line above has a loose comparison, but no whitelist comment.
	echo 'True';
}

if ( true == $true ) { // Loose comparisons FTW!
	echo 'True';
}
