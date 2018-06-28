<?php

wp_register_script( 'someScript-js', 'https://domain.com/someScript.js' , array( 'jquery' ), '1.1.1', true ); // Ok.
wp_register_script( 'someScript-js', 'https://domain.com/someScript.js' , array( 'jquery' ), '1.1.1', $in_footer ); // Warning - $in_footer is not a boolean
wp_register_script( 'someScript-js', 'https://domain.com/someScript.js' , array( 'jquery' ) ); // Error - missing $in_footer.

wp_register_script( 'someScript-js', 'https://domain.com/someScript.js' , array( 'jquery' ), true ); // Warning.
wp_register_script( 'someScript-js', 'https://domain.com/someScript.js' , array( 'jquery' ), $in_footer ); // Warning - $in_footer is not a boolean

wp_enqueue_script( 'script-name', 'https://domain.com/someScript.js', false, '1.0.0', true ); // Ok.
wp_enqueue_script( 'script-name', 'https://domain.com/someScript.js', false, '1.0.0', false ); // Warning.
wp_enqueue_script( 'script-name', 'https://domain.com/someScript.js' ); // Error.

wp_register_style( 'script-name', 'https://domain.com/someScript.js', false, '1.0.0' ); // Ok.
wp_register_style( 'script-name', 'https://domain.com/someScript.js' ); // Error.

wp_enqueue_style( 'script-name', 'https://domain.com/someScript.js', false, '1.0.0'); // Ok.
wp_enqueue_style( 'script-name', 'https://domain.com/someScript.js' ); // Error.