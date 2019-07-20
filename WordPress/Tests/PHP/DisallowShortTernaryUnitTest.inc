<?php

$long = $compare ? 'a' : 'b';

$short = $compare ? : 0; // Bad.
$short = $compare ?: 0; // Bad.
$short = $compare ?
	: 0; // Bad.
$short = $compare ? /* intentionally left empty */ : 0; // Bad.

/* Intentional parse error. This should be the last test in the file. */
$unfinished = $compare ? /* comment */
