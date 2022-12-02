<?php

if ( author_can( $post, 'read' ) ) { } // OK.

/*
 * Deprecated capabilities - checking if setting from the CLI will work as well.
 *
 * WordPress version: 6.1.0
 */
if ( author_can( $post, 'level_3' ) ) { } // Error.

if ( author_can( $post, 'level_5' ) ) { } // Error.

add_options_page( 'page_title', 'menu_title', 'level_10', 'menu_slug', 'function' ); // Error.
