<?php
/*
 * Test that various combinations of the PHPCS 3.2+ annotations are being respected.
 */

// phpcs:disable WordPress.WhiteSpace.PrecisionAlignment
  echo 'this line has precision alignment but shouldn\'t be reported';
// phpcs:enable WordPress.WhiteSpace.PrecisionAlignment
  echo 'this line has precision alignment and should be reported again';

// phpcs:disable WordPress.WhiteSpace
  echo 'this line has precision alignment but shouldn\'t be reported';
// phpcs:enable WordPress.WhiteSpace
  echo 'this line has precision alignment and should be reported again';

// phpcs:disable WordPress
  echo 'this line has precision alignment but shouldn\'t be reported';
// phpcs:enable WordPress
  echo 'this line has precision alignment and should be reported again';

// phpcs:disable WordPress.WhiteSpace.PrecisionAlignment
  echo 'this line has precision alignment but shouldn\'t be reported';
// phpcs:enable WordPress
  echo 'this line has precision alignment and should be reported again';

// phpcs:disable WordPress
  echo 'this line has precision alignment but shouldn\'t be reported';
// phpcs:enable WordPress.WhiteSpace.PrecisionAlignment
  echo 'this line has precision alignment and should be reported again';

// phpcs:disable WordPress.WhiteSpace.PrecisionAlignment
  echo 'this line has precision alignment but shouldn\'t be reported';
// phpcs:enable
  echo 'this line has precision alignment and should be reported again';

// phpcs:disable WordPress
  echo 'this line has precision alignment but shouldn\'t be reported';
// phpcs:enable
  echo 'this line has precision alignment and should be reported again';

// phpcs:disable
  echo 'this line has precision alignment but shouldn\'t be reported';
// phpcs:enable
  echo 'this line has precision alignment and should be reported again';

// phpcs:ignore WordPress.WhiteSpace.PrecisionAlignment
  echo 'this line has precision alignment but shouldn\'t be reported';

  echo 'this line has precision alignment but shouldn\'t be reported'; // phpcs:ignore WordPress.WhiteSpace.PrecisionAlignment

// phpcs:disable WordPress.WhiteSpace
  echo 'this line has precision alignment but shouldn\'t be reported';
// phpcs:enable WordPress.WhiteSpace.PrecisionAlignment
  echo 'this line has precision alignment and should be reported again';
// phpcs:enable WordPress.WhiteSpace.SomeThingElse
  echo 'this line has precision alignment and should be reported again';
// phpcs:enable WordPress
  echo 'this line has precision alignment and should be reported again';
