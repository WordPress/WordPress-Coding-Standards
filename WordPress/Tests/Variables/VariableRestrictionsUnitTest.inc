<?php

bar( $taz ); // Matches: '$taz'.

test( $foo->bar ); // Matches: '$foo->bar'.

$foo->bar(); // Ignored, this is a function not a variable.

$foo->bar_method(); // Ignored.

FOO::vars; // Matches: 'FOO::var'.

FOO::var_test;

FOO::reg; // Matches: 'FOO::reg*'.

FOO::regex; // Matches: 'FOO::reg*'.

FOO::vars(); // Ignored.

FOO::$static; // Matches: 'FOO::$static'.

$foo['test']; // Matches: '$foo['test']'.

$foo["test"]; // Matches: '$foo['test']' AND $foo['test'].

bar( $tallyho ); // Matches: '$tallyho'.
test( $bar->bar ); // Matches: '$bar->bar'.
BAR::vars; // Matches: 'BAR::var'.

/*
 * Test exclude property.
 */
// Exclude one group:
// @codingStandardsChangeSetting WordPress.Variables.VariableRestrictions exclude test
bar( $taz ); // Ok - within excluded group.
test( $foo->bar ); // Ok - within excluded group.
FOO::regex; // Ok - within excluded group.

bar( $tallyho ); // Error.
test( $bar->bar ); // Error.
BAR::vars; // Error.

// Exclude all groups:
// @codingStandardsChangeSetting WordPress.Variables.VariableRestrictions exclude test,another
bar( $taz ); // Ok - within excluded group.
test( $foo->bar ); // Ok - within excluded group.
FOO::regex; // Ok - within excluded group.

bar( $tallyho ); // Ok - within excluded group.
test( $bar->bar ); // Ok - within excluded group.
BAR::vars; // Ok - within excluded group.

// Reset group exclusions.
// @codingStandardsChangeSetting WordPress.Variables.VariableRestrictions exclude false
bar( $taz ); // Error.
test( $foo->bar ); // Error.
FOO::regex; // Error.

bar( $tallyho ); // Error.
test( $bar->bar ); // Error.
BAR::vars; // Error.
