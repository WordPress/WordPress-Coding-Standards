<?php

wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ) ); // Warning - missing $ver, Warning - In Footer is set to a falsy (default) value.

wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), /* comment */ '1.1.1', true ); // OK.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), /* comment */ '0' /* another comment */, true ); // Error - a falsy value is not allowed for the $ver parameter.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), '1.1.1', $in_footer ); // OK, the value is set.

wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), 0, true ); // Error - a falsy value is not allowed for the $ver parameter.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), false, true ); // Error - a falsy value is not allowed for the $ver parameter.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), null, true ); // Warning - $ver is NULL.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), 0.0, true ); // Error - a falsy value is not allowed for the $ver parameter.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), 00.00, true ); // Error - a falsy value is not allowed for the $ver parameter.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), 0x0, true ); // Error - a falsy value is not allowed for the $ver parameter.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), 100.001, true ); // OK.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), 0x1, true ); // Hex number, OK.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), 052, true ); // Octal number, OK.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), 1, true ); // OK.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), 0.1, true ); // OK.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), 1.0, true ); // OK.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), 2 * 8, true ); // OK.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), '0', true ); // Error - a falsy value is not allowed for the $ver parameter.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), '0.0.0', true ); // OK.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), '0.1.0', true ); // OK.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), '0' . '0', true ); // OK.

wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), WP_VERSION, true ); // OK.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), true, true ); // OK.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), function() { }, true ); // OK.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), $version, true ); // OK.

wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), '1.1.0' ); // Warning - $in_footer is not explicitly set.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), '1.1.0', false ); // OK.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), '1.1.0', null ); // OK.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), '1.1.0', 0 ); // OK.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), '1.1.0', $in_footer ); // OK.

wp_enqueue_script( 'script-name', 'https://example.com/someScript.js', false, '1.0.0', true ); // OK.
wp_enqueue_script( 'script-name', 'https://example.com/someScript.js' ); // Warning - missing $ver, Warning - missing $in_footer.

wp_register_style( 'script-name', 'https://example.com/someScript.js', false, '1.0.0' ); // OK.
wp_register_style( 'script-name', 'https://example.com/someScript.js' ); // Warning - missing $ver.

wp_enqueue_style( 'script-name', 'https://example.com/someScript.js', false, '1.0.0'); // OK.
WP_ENQUEUE_style( 'script-name', 'https://example.com/someScript.js' ); // Warning - missing $ver.

wp_register_script( 'someScript-js' ); // OK.
wp_enqueue_script( 'someScript-js' ); // OK.

wp_register_style( 'someScript-js' ); // OK.
wp_enqueue_style( 'someScript-js' ); // OK.

wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), (bool) 1, true ); // OK.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), (bool) 0, true ); // OK - a falsy value behind a cast is not flagged.

// Safeguard handling of arithmetic operations.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), 0 + 1, true ); // OK.

// Safeguard support for PHP 8.0+ named parameters.
wp_register_script(
	ver      : 0, // Error - a falsy value is not allowed for the $ver parameter.
	in_footer: false,
	src      : 'https://example.com/someScript.js',
	handle   : 'someScript-js',
);
wp_enqueue_script(
	src: 'https://example.com/someScript.js',
	handle: 'script-name',
); // Warning - missing $ver, Warning - missing $in_footer.

// Safeguard that comments in the parameters are ignored.
wp_register_script(
	'someScript-js',
	'https://example.com/someScript.js',
	array( 'jquery' ),
	// Warning - $ver is NULL.
	null,
	true,
);

// Safeguard handling of PHP 7.4 numeric literals with underscores.
wp_register_script( 'someScript-js', $url, [], 0_0.0_0, true ); // Error - a falsy value is not allowed for the $ver parameter.

// Safeguard handling of PHP 8.1 explicit octals.
wp_register_script( 'someScript-js', $url, [], 0o0, true ); // Error - a falsy value is not allowed for the $ver parameter.













// Safeguard handling of non-lowercase `null`.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), NULL, true ); // Warning - $ver is NULL.

/*
 * Safeguard handling of fully qualified \true, \false and \null.
 * Also safeguard that adding T_NS_SEPARATOR to $false_tokens doesn't cause false positives due to problems in is_falsy().
 */
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), \FALSE, \true ); // Error - a falsy value is not allowed for the $ver parameter.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), \null, \TRUE ); // Warning - $ver is NULL.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), \Null, true ); // Warning - $ver is NULL.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), \true, \False ); // Ok.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), \get_version(), \null ); // OK.

/*
 * Safeguard correct handling of all types of namespaced function calls.
 */
\wp_enqueue_script( 'script-name', 'https://example.com/someScript.js', false, '1.1.0' ); // Warning - missing $in_footer.
\wp_REGISTER_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), '1.1.0' ); // Warning - missing $in_footer.
MyNamespace\wp_register_style( 'style-name', 'https://example.com/style.css' ); // Ok.
\MyNamespace\wp_enqueue_style( 'style-name', 'https://example.com/style.css' ); // Ok.
namespace\wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), '1.1.0' ); // The sniff should start flagging this once it can resolve relative namespaces (once it does, it should be "Warning - missing $in_footer").
namespace\Sub\wp_enqueue_style( 'style-name', 'https://example.com/style.css' ); // Ok.

// Safeguard handling of an array passed as the $ver parameter. Only an empty array evaluates to false.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), [ '1.0.0' ], true ); // OK.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), [] + [ 1 ], true ); // OK - the array is part of a larger expression.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), [ $a ] = array( 1, 2, 3 ), true ); // OK - short list assignment, not an array.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), array(), true ); // Error - a falsy value is not allowed for the $ver parameter.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), [ /* comment */ ], true ); // Error - a falsy value is not allowed for the $ver parameter.

// Safeguard handling of a heredoc/nowdoc passed as the $ver parameter. Only an empty or "0" body evaluates to false.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), <<<'EOD'
1.0.0
EOD
, true ); // OK.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), <<<EOD
$version
EOD
, true ); // OK - contains interpolation, so the value can't be determined.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), <<<"EOD"
EOD
. '.0', true ); // OK - the heredoc is part of a larger expression.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), <<<'EOD'
EOD
, true ); // Error - a falsy value is not allowed for the $ver parameter.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), <<<EOD
0
EOD
, true ); // Error - a falsy value is not allowed for the $ver parameter.

// Safeguard handling of a double-quoted string passed as the $ver parameter.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), "", true ); // Error - a falsy value is not allowed for the $ver parameter.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), "$ver", true ); // Ok.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), "${0}", true ); // Ok.
