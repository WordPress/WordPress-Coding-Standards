<?php

// OK.
$a = (bool) $b;
$a = (int) $b;
$a = (float) $b;
$a = (string) $b;
$a = (array) $b;
$a = (object) $b;

// Error: Wrong form.
$a = (boolean) $b;
$a = (integer) $b;
$a = (double) $b;
$a = (real) $b;

// Warning: Discouraged casts.
$a = (unset) $b; // Warning.
$a = (binary) $b; // Warning.
$a = b"binary string"; // Warning. Currently false negative. Related to PHPCS bug report #1574.
$a = b"binary $string"; // Warning.

// Error: Mixed case.
$a = (Bool) $b;
$a = (Boolean) $b; // + wrong form.
$a = (Int) $b;
$a = (Integer) $b; // + wrong form.
$a = (Float) $b;
$a = (Double) $b; // + wrong form.
$a = (Real) $b; // + wrong form.
$a = (String) $b;
$a = (Array) $b;
$a = (Object) $b;
$a = (Unset) $b; // + discouraged.
$a = (Binary) $b; // + discouraged.

// Error: Uppercase.
$a = (BOOL) $b;
$a = (BOOLEAN) $b; // + wrong form.
$a = (INT) $b;
$a = (INTEGER) $b; // + wrong form.
$a = (FLOAT) $b;
$a = (DOUBLE) $b; // + wrong form.
$a = (REAL) $b; // + wrong form.
$a = (STRING) $b;
$a = (ARRAY) $b;
$a = (OBJECT) $b;
$a = (UNSET) $b; // + discouraged.
$a = (BINARY) $b; // + discouraged.
