<?php

$var = 'a';
$trueVariable = $var === 'a' ? true : false;  // Bad.
// Can be written as.
$trueVariable = $var === 'a'; // Ok.

$test_var = $boolean ? true : false;  // Bad.

$test = $var === 'a' ? // Bad.
			true :
			false;

$test = $var === 'a'
			? true // Bad.
			: false;

$isTrue = $var === 4 ? false : true;  // Bad.

$isTrue = $var === 4 ? 'Yes' : 'No';  // Ok.

$isTrue = $var === 4 ? true : 'No';  // Ok.

$isTrue = $var === 4 ? 'True' : false;  // Ok.

$isTrue = $var === 4 ? false : 'True';  // Ok.

$isTrue = $var === 4 ? 'False' : true;  // Ok.

$notFalse = $var !== false; // Ok.
