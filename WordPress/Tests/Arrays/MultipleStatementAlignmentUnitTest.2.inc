<?php

/*
 * Test reporting and fixing "too much space before" errors in single line arrays.
 */
$array = [ 'b', 'd' ]; // OK.
$array = [ 'a' => 'b', 'c' => 'd' ]; // OK.
$array = [ 'a'    => 'b', 'c'               => 'd' ]; // Bad x 2.

$array = [ 'a' /* Comment. */ => /* Comment. */ 'b' ]; // OK.
$array = [ 'a' /* Comment. */    => /* Comment. */ 'b' ]; // Bad.

$array = [[ 'a' => 'b', 'c' => 'd' ]]; // OK.
$array = [[ 'a'   => [ 'c'     => 'd' ]]]; // Bad x 2.

/*
 * Test reporting errors and fixing double arrow alignment in a multi-line arrays.
 */
$array = [
    'b',
    'd',
    // Something.
    'f',
];
$array = [
    'a'   => 'b',
    'ccc' => 'd',
    'ee'  => 'f',
    'g'
        => 'h',
];
$array = [
    'a'   => 'b',
    123   => 'd',
    56958 => 'f',
    'g'
        => 'h',
];

// Test no space before.
$array = [
    'a'=> 'b', // Bad.
];
$array = [
    'a'=> 'b', // Bad.
    'ccc'=> 'd', // Bad.
    'ee'=> 'f', // Bad.
    'g'
        => 'h',
];

// Test too much space before.
$array = [
    'a'        => 'b', // Bad.
];
$array = [
    'a'             => 'b', // Bad.
    'ccc' => 'd',
    'ee'    => 'f', // Bad.
    'g'
        => 'h',
];

// Test exact number of spaces before vs minimum.
$array = [
    'a'     => 'b', // Bad.
    'ccc'   => 'd', // Bad.
    'ee'    => 'f', // Bad.
    'g'
        => 'h',
];

// @codingStandardsChangeSetting WordPress.Arrays.MultipleStatementAlignment exact false
$array = [
    'a'        => 'b',
];
$array = [
    'a'     => 'b',
    'ccc'   => 'd',
    'ee'    => 'f',
    'g'
        => 'h',
];
$array = [
    'a'  => 'b', // Bad.
    'ccc' => 'd',
    'ee'    => 'f', // Bad.
    'g'
            => 'h',
];
$array = [
    'a'  => 'b', // Bad.
    'ccc'   => 'd',
    'ee'    => 'f',
    'g'
              => 'h',
];
// @codingStandardsChangeSetting WordPress.Arrays.MultipleStatementAlignment exact true

// Test dealing with new lines.
$array = [
    'a'
        => 'b',
    'ccc'
        => 'd',
];
// @codingStandardsChangeSetting WordPress.Arrays.MultipleStatementAlignment ignoreNewline false
$array = [
    'a'
        => 'b', // Bad.
    'ccc'
        => 'd', // Bad.
];
// @codingStandardsChangeSetting WordPress.Arrays.MultipleStatementAlignment ignoreNewline true

// @codingStandardsChangeSetting WordPress.Arrays.MultipleStatementAlignment exact false
// @codingStandardsChangeSetting WordPress.Arrays.MultipleStatementAlignment ignoreNewline false
// Test combined ignore new lines false + exact false.
$array = [
    'a'     => 'b',
    'ccc'   => 'd',
    'ee'    => 'f',
    'g'
        => 'h', // Bad.
];
$array = [
    'a'  => 'b', // Bad.
    'ccc' => 'd',
    'ee'    => 'f', // Bad.
    'g'
                => 'h', // Bad.
];
$array = [
    'a'  => 'b', // Bad.
    'ccc' => 'd', // Bad.
    'ee'      => 'f',
    'g'
              => 'h', // Bad.
];
$array = [
    'a'  => 'b', // Bad.
    'ccc'   => 'd',
    'ee'    => 'f',
    'g'
              => 'h', // Bad.
];
// @codingStandardsChangeSetting WordPress.Arrays.MultipleStatementAlignment exact true
// @codingStandardsChangeSetting WordPress.Arrays.MultipleStatementAlignment ignoreNewline true


/*
 * Test with maxColumn value set.
 */
// @codingStandardsChangeSetting WordPress.Arrays.MultipleStatementAlignment maxColumn 12
$array = [
    'a'   => 'b',
    'ccc' => 'd',
    'ee'  => 'f',
    'gggggggggg' => 'h',
    'i'
        => 'j',
    'kkkkkkkkkkkk'
                 => 'l',
];
$array = [
    'a' => 'b', // Bad.
    'ccc' => 'd',
    'ee' => 'f', // Bad.
    'gggggggggg'=> 'h', // Bad - no space before.
];
$array = [
    'a'            => 'b', // Bad.
    'ccc'          => 'd', // Bad.
    'ee'           => 'f', // Bad.
    'gggggggggg'   => 'h', // Bad - too much space before.
];
// @codingStandardsChangeSetting WordPress.Arrays.MultipleStatementAlignment maxColumn 1000

// @codingStandardsChangeSetting WordPress.Arrays.MultipleStatementAlignment exact false
// @codingStandardsChangeSetting WordPress.Arrays.MultipleStatementAlignment maxColumn 12
// Test combined maxColumn value set + exact false.
$array = [
    'a' => 'b',
];
$array = [
    'a'    => 'b',
];
$array = [
    'a'    => 'b',
    'ccc'  => 'd',
    'ee'   => 'f',
    'gggggggggg' => 'h',
    'i'
        => 'j',
    'kkkkkkkkkkkk'
                 => 'l',
];

$array = [
    'a'        => 'b', // Bad.
];
$array = [
    'a'    => 'b',
    'ccc'  => 'd',
    'ee' => 'f', // Bad.
    'gggggggggg' => 'h',
];
$array = [
    'a'    => 'b',
    'ccc'  => 'd',
    'ee'       => 'f', // Bad.
    'gggggggggg' => 'h',
];
$array = [
    'a'        => 'b', // Bad.
    'ccc'      => 'd', // Bad.
    'ee'       => 'f', // Bad.
    'gggggggggg' => 'h',
];
// @codingStandardsChangeSetting WordPress.Arrays.MultipleStatementAlignment exact true
// @codingStandardsChangeSetting WordPress.Arrays.MultipleStatementAlignment maxColumn 1000

// @codingStandardsChangeSetting WordPress.Arrays.MultipleStatementAlignment exact false
// @codingStandardsChangeSetting WordPress.Arrays.MultipleStatementAlignment maxColumn 12
// @codingStandardsChangeSetting WordPress.Arrays.MultipleStatementAlignment ignoreNewline false
// Test combined maxColumn value set + exact false + ignore new lines false.
$array = [
    'a'    => 'b',
    'ccc'  => 'd',
    'ee'   => 'f',
    'gggggggggg' => 'h',
    'i'
        => 'j', // Bad.
    'kkkkkkkkkkkk'
                 => 'l', // Bad.
];
$array = [
    'a'    => 'b',
    'ccc'  => 'd',
    'ee' => 'f', // Bad.
    'gggggggggg' => 'h',
    'i'
        => 'j', // Bad.
    'kkkkkkkkkkkk'
                 => 'l', // Bad.
];
$array = [
    'a'    => 'b',
    'ccc'  => 'd',
    'ee'       => 'f', // Bad.
    'gggggggggg'=> 'h', // Bad.
    'i'
        => 'j', // Bad.
    'kkkkkkkkkkkk'
                 => 'l', // Bad.
];
$array = [
    'a'        => 'b', // Bad.
    'ccc'      => 'd', // Bad.
    'ee'       => 'f', // Bad.
    'gggggggggg' => 'h',
    'i'
        => 'j', // Bad.
    'kkkkkkkkkkkk'
                 => 'l', // Bad.
];
// @codingStandardsChangeSetting WordPress.Arrays.MultipleStatementAlignment exact true
// @codingStandardsChangeSetting WordPress.Arrays.MultipleStatementAlignment maxColumn 1000
// @codingStandardsChangeSetting WordPress.Arrays.MultipleStatementAlignment ignoreNewline true


// Test mixed complex arrays.
$array = [
    'a'   => 'b', // Bad.
    12345 => 'd', // Bad.
    'f',
    'g'
        => 'h',
        [ 'something' ],
    [ 'a' => 'something' ],
    [
        'a' => 'something',
    ],
    'i' => [ 'something' ], // Bad.
            'jjjjjj' => [ 'a' => 'something' ],
    'kk' => [ // Bad.
        'a'     => 'something', // Bad.
    ],
    $ab . 'l' => [ 'a' => 'something' ], // Bad.
    // Compound multi-line key.
    $ab .
    'l' => [ 'a' => 'something' ], // Bad.
        $ab . 'l' => [ 'a' => 'something' ], // Bad.
];
