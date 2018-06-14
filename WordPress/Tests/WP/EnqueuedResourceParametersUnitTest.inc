<?php

wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ) ); // Error - missing $ver, Warning - In Footer is set to a falsy (default) value.

wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), /* comment */ '1.1.1', true ); // Ok.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), '1.1.1', $in_footer ); // Ok, the value is set.

wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), 0, true ); // Error - 0, false or NULL are not allowed
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), false, true ); // Error - 0, false or NULL are not allowed.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), null, true ); // Error - 0, false or NULL are not allowed.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), 0.0, true ); // Error - 0, false or NULL are not allowed
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), 00.00, true ); // Error - 0, false or NULL are not allowed
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), 0x0, true ); // Error - 0, false or NULL are not allowed
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), 100.001, true ); // Ok.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), 0x1, true ); // Ok.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), 1, true ); // Ok.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), 0.1, true ); // Ok.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), 1.0, true ); // Ok.

wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), WP_VERSION, true ); // Ok.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), true, true ); // Ok.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), function() { }, true ); // Ok.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), $version, true ); // Ok.

wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), '1.1.0' ); // Warning - In Footer is set to a falsy (default) value.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), '1.1.0', false ); // Ok.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), '1.1.0', null ); // Ok.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), '1.1.0', 0 ); // Ok.
wp_register_script( 'someScript-js', 'https://example.com/someScript.js' , array( 'jquery' ), '1.1.0', $in_footer ); // Ok.

wp_enqueue_script( 'script-name', 'https://example.com/someScript.js', false, '1.0.0', true ); // Ok.
wp_enqueue_script( 'script-name', 'https://example.com/someScript.js' ); // Error - missing $ver, Warning - missing $in_footer.

wp_register_style( 'script-name', 'https://example.com/someScript.js', false, '1.0.0' ); // Ok.
wp_register_style( 'script-name', 'https://example.com/someScript.js' ); // Error - missing $ver.

wp_enqueue_style( 'script-name', 'https://example.com/someScript.js', false, '1.0.0'); // Ok.
wp_enqueue_style( 'script-name', 'https://example.com/someScript.js' ); // Error - missing $ver.

wp_register_script( 'someScript-js' ); // Ok.
wp_enqueue_script( 'someScript-js' ); // Ok.

wp_register_style( 'someScript-js' ); // Ok.
wp_enqueue_style( 'someScript-js' ); // Ok.
