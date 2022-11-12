<?php

// OK.
$a = (float) $b;
$a = (string) $b;
$a = (array) $b;
$a = (object) $b;

// Error: Wrong form.
$a = (double) $b;
$a = (real) $b;

$a = (unset) $b; // Error.
// Warning: Discouraged casts.
$a = (binary) $b; // Warning.
$a = b"binary string"; // Warning.
$a = b"binary $string"; // Warning.

// Test recognition with whitespace within the cast.
// OK.
$a = ( float) $b;
$a = (string ) $b;
$a = (          array) $b;
$a = (object      ) $b;

$a = (double ) $b; // Error.
$a = ( real ) $b; // Error.
$a = (  unset ) $b; // Error.
$a = ( binary ) $b; // Warning.
