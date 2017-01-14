<?php

$foo = array( 'bar' => 1 ); // Bad.

$foo['bar'] = 1; // Bad.

$foo = 'test' . 'bar=1&taz=5&bar=2'; // Bad.

/*
 * Test exclude property.
 */
// Exclude one group:
// @codingStandardsChangeSetting WordPress.Arrays.ArrayAssignmentRestrictions exclude posts_per_page
$foo = array( 'bar' => 1 ); // Ok.
$foo['bar'] = 1; // Ok.
$foo = 'test' . 'bar=1&taz=5&bar=2'; // Ok.

// Reset group exclusions.
// @codingStandardsChangeSetting WordPress.Arrays.ArrayAssignmentRestrictions exclude null
$foo = array( 'bar' => 1 ); // Bad.
