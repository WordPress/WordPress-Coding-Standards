<?php
// test for 'equals' conditionals
if ( true == $true ) { // Bad x 1
	echo 'True';
} elseif ( false === $true ) { // Good
	echo 'False';
}

// test for 'not equals' conditionals
if ( true != $true ) { // Bad x 1
	echo 'True';
} elseif ( false !== $true ) { // Good
	echo 'False';
}

// test for whitelisting
if ( true == $true ) { // loose comparison okay
	echo 'True';
}