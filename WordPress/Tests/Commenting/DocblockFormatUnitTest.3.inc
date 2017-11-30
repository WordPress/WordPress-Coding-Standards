<?php

/*
 * Tests for which the "fixed" test file will be different in PHPCS 2.x, so the auto-fixer is not tested on purpose.
 * Both these tests should throw errors for no space after opener, nor before closer.
 */

/**doc comment - in PHPCS 2.x this is tokenized as a T_COMMENT*/
public $property;

/**#@-*/

// @codingStandardsChangeSetting WordPress.Commenting.DocblockFormat allow_short false

/**doc comment - in PHPCS 2.x this is tokenized as a T_COMMENT*/
public $property;

// @codingStandardsChangeSetting WordPress.Commenting.DocblockFormat allow_short true
