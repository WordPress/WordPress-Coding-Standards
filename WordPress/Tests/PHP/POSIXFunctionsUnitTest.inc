<?php

// These are ok.
$regex = preg_quote( 'cool', '#' ); // Good.
$title = preg_match( 'cool', get_the_title() ); // Good.
$title = preg_match_all( 'cool', get_the_title(), $matches ); // Good.
$title = preg_replace( 'cool', 'not cool', get_the_title() ); // Good.
$title = preg_replace_callback( 'cool', 'callback_function', get_the_title() ); // Good.
$title = preg_split( 'cool', get_the_title() ); // Good.


// These should all trigger an error.
if ( ereg( '[A-Za-z]+', $title, $regs ) ) // Bad, ereg deprecated. Use preg_match instead.
	die( $regs );

if ( eregi( '[a-z]+', $title, $regs ) ) {} // Bad, eregi deprecated. Use preg_match instead.

$title = ereg_replace( 'cool', 'not cool', get_the_title() ); // Bad, ereg_replace has been deprecated. Use preg_replace instead.

$title = eregi_replace( 'cool', 'not cool', get_the_title() ); // Bad, eregi_replace also deprecated. Use preg_replace instead.

list( $year, $month, $day ) = split( ':', $date ); // Bad, split has been deprecated. Use preg_split or explode instead.

$title_parts = spliti( ' ', get_the_title(), 4 ); // Bad, spliti also deprecated. Use preg_split instead.

sql_regcase( 'Foo - bar.'); // Bad. Deprecated.
