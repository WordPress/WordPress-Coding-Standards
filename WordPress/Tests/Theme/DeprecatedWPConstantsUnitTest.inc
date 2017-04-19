<?php

define( 'STYLESHEETPATH', 'something' ); // Ok - will throw an error about already defined constant anyway.

if ( defined( 'STYLESHEETPATH' ) ) { // Ok.
	// Do something unrelated.
}

echo \mynamespace\STYLESHEETPATH; // Ok, not WP constant.

echo My_Class::STYLESHEETPATH; // Ok, not WP constant.

echo STYLESHEETPATH; // Bad.
$folder = basename( TEMPLATEPATH ); // Bad.
$file = PLUGINDIR . '/js/myfile.js'; // Bad.
echo MUPLUGINDIR; // Bad.
echo HEADER_IMAGE; // Bad.
echo NO_HEADER_TEXT; // Bad.
echo HEADER_TEXTCOLOR; // Bad.
echo HEADER_IMAGE_WIDTH; // Bad.
echo HEADER_IMAGE_HEIGHT; // Bad.
echo BACKGROUND_COLOR; // Bad.
echo BACKGROUND_IMAGE; // Bad.
