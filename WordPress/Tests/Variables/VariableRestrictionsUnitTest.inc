<?php

bar( $taz ); // Matches: '$taz'.

test( $foo->bar ); // Matches: '$foo->bar'.

$foo->bar(); // Ignored, this is a function not a variable.

$foo->bar_method(); // Ignored.

FOO::var; // Matches: 'FOO::var'.

FOO::var_test;

FOO::reg; // Matches: 'FOO::reg*'.

FOO::regex; // Matches: 'FOO::reg*'.

FOO::var(); // Ignored.

FOO::$static; // Matches: 'FOO::$static'.

$foo['test']; // Matches: '$foo['test']'.

$foo["test"]; // Matches: '$foo['test']' AND $foo['test'].
