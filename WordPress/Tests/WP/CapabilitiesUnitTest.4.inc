<?php

if ( author_can( $post, 'read' ) ) { } // OK.

/*
 * Deprecated capabilities - just making sure the CLI option is reset to the default.
 */
if ( author_can( $post, 'level_3' ) ) { } // Error.

if ( author_can( $post, 'level_5' ) ) { } // Error.

add_options_page( 'page_title', 'menu_title', 'level_10', 'menu_slug', 'function' ); // Error.
