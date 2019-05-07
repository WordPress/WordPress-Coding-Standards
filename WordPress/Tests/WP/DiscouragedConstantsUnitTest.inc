<?php

/*
 * Make sure that global constants are correctly identified.
 *
 * The below are all ok.
 */
// Ok, not a (global) constant.
namespace STYLESHEETPATH {}
namespace MY\OTHER\STYLESHEETPATH\NS {}
use STYLESHEETPATH;
use Something, STYLESHEETPATH, SomethingElse;
class STYLESHEETPATH {
	const STYLESHEETPATH = 'something';
	private function STYLESHEETPATH() {}
}
class ABC extends STYLESHEETPATH {}
class DEF implements STYLESHEETPATH {}
interface STYLESHEETPATH {}
trait STYLESHEETPATH {}
$a = new STYLESHEETPATH;
$a = new STYLESHEETPATH();
function STYLESHEETPATH() {}
echo STYLESHEETPATH();
echo My\UsedAsNamespace\STYLESHEETPATH\something;
My\UsedAsNamespace\STYLESHEETPATH\something::something_else();
if ( $abc instanceof STYLESHEETPATH ) {}

goto STYLESHEETPATH;
// Something.
STYLESHEETPATH:
// Something.

echo \mynamespace\STYLESHEETPATH;
echo My_Class::STYLESHEETPATH;
echo $this->STYLESHEETPATH;
use const SomeNamespace\STYLESHEETPATH as SSP; // PHP 5.6+
use const SomeNamespace\{STYLESHEETPATH, TEMPLATEPATH}; // PHP 7.0+
define( 'My\STYLESHEETPATH', 'something' );

// Ok, not usage of the constant as such.
if ( defined( 'STYLESHEETPATH' ) ) { // Ok.
	// Do something unrelated.
}
echo namespace\STYLESHEETPATH; // "Magic" namespace operator.

/*
 * These are all bad.
 */
echo STYLESHEETPATH;
echo \STYLESHEETPATH; // Global constant.
$folder = basename( TEMPLATEPATH );
include PLUGINDIR . '/js/myfile.js';
echo MUPLUGINDIR;
echo HEADER_IMAGE;
echo NO_HEADER_TEXT;
echo HEADER_TEXTCOLOR;
echo HEADER_IMAGE_WIDTH;
echo HEADER_IMAGE_HEIGHT;
echo BACKGROUND_COLOR;
echo BACKGROUND_IMAGE;

use const STYLESHEETPATH as SSP;
use const ABC as STYLESHEETPATH;

switch(	STYLESHEETPATH ) {
	case STYLESHEETPATH:
		break;
}

define( 'STYLESHEETPATH', 'something' );
const STYLESHEETPATH = 'something';
