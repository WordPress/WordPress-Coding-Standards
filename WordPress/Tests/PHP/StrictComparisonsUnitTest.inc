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

// Bad - whitelisted via old-style whitelist comment.
if ( true == $true ) { // Loose comparison, OK.
	echo 'True';
}
