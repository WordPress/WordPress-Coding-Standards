<?php
// test for 'equals' conditionals
if ( true == $true ) { // Bad
	echo 'True';
} elseif ( false === $true ) { // OK
	echo 'False';
}

// test for 'not equals' conditionals
if ( true != $true ) { // Bad
	echo 'True';
} elseif ( true <> $true ) { // Bad
	echo 'False';
} elseif ( false !== $true ) { // OK
	echo 'False';
}

// test for whitelisting
if ( true == $true ) { // loose comparison, OK
	echo 'True';
}