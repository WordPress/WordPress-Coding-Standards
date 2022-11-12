<?php

// Test correct handling of PHPCS annotations.
// phpcs:set WordPress.WhiteSpace.ControlStructureSpacing blank_line_check true
if ( $foo ) {
	if ( $bar ) {


		// phpcs:disable Standard.Category.Sniff -- too much blank space at start of structure.
		if ( $baz ) {
			do_something();
		}

		// phpcs:enable Standard.Category.Sniff -- too much blank space at end of structure.


	} // phpcs:ignore Standard.Category.Sniff -- this comment should be skipped over for the "blank line after" check.


}
// phpcs:set WordPress.WhiteSpace.ControlStructureSpacing blank_line_check false
