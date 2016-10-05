<?php

$good = array( 'value1', 'value2' ); // Ok.

$query_vars = array('post_type' => 'food'); // Bad, no spaces after opening and before closing parenthesis.

// Test for fixing of extra whitespace.
$test = array(   1, 2     );
