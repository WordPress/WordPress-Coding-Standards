<?php

wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ) ); // Error - missing $ver, Warning - In Footer is set to a falsy (default) value.

wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), /* comment */ '1.1.1', true ); // OK.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), /* comment */ '0' /* another comment */, true ); // Error - 0, false or NULL are not allowed.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), '1.1.1', $in_footer ); // OK, the value is set.

wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), 0, true ); // Error - 0, false or NULL are not allowed.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), false, true ); // Error - 0, false or NULL are not allowed.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), null, true ); // Error - 0, false or NULL are not allowed.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), 0.0, true ); // Error - 0, false or NULL are not allowed.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), 00.00, true ); // Error - 0, false or NULL are not allowed.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), 0x0, true ); // Error - 0, false or NULL are not allowed.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), 100.001, true ); // OK.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), 0x1, true ); // Hex number, OK.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), 052, true ); // Octal number, OK.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), 1, true ); // OK.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), 0.1, true ); // OK.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), 1.0, true ); // OK.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), 2 * 8, true ); // OK.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), '0', true ); // Error - 0, false or NULL are not allowed.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), '0.0.0', true ); // OK.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), '0.1.0', true ); // OK.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), '0' . '0', true ); // OK.

wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), WP_VERSION, true ); // OK.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), true, true ); // OK.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), function() { }, true ); // OK.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), $version, true ); // OK.

wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), '1.1.0' ); // Warning - In Footer is set to a falsy (default) value.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), '1.1.0', false ); // OK.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), '1.1.0', null ); // OK.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), '1.1.0', 0 ); // OK.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), '1.1.0', $in_footer ); // OK.

wp_enqueue_script( 'script-name', 'https://example.com/someScript.js', false, '1.0.0', true ); // OK.
wp_enqueue_script( 'script-name', 'https://example.com/someScript.js' ); // Error - missing $ver, Warning - missing $in_footer.

wp_register_style( 'script-name', 'https://example.com/someScript.js', false, '1.0.0' ); // OK.
wp_register_style( 'script-name', 'https://example.com/someScript.js' ); // Error - missing $ver.

wp_enqueue_style( 'script-name', 'https://example.com/someScript.js', false, '1.0.0'); // OK.
wp_enqueue_style( 'script-name', 'https://example.com/someScript.js' ); // Error - missing $ver.

wp_register_script( 'someScript-js' ); // OK.
wp_enqueue_script( 'someScript-js' ); // OK.

wp_register_style( 'someScript-js' ); // OK.
wp_enqueue_style( 'someScript-js' ); // OK.

wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), (bool) 1, true ); // OK.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), (bool) 0, true ); // Error - 0, false or NULL are not allowed.

wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), (binary) 123, true ); // OK.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), b'0', true ); // Error - 0, false or NULL are not allowed.
