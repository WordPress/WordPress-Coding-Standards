<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\Commenting;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the SinceTag sniff.
 *
 * @since 3.1.1
 *
 * @covers \WordPressCS\WordPress\Sniffs\Commenting\SinceTagSniff
 */
final class SinceTagUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array<int, int> Key is the line number, value is the number of expected errors.
	 */
	public function getErrorList() {
		return array(
			2   => 1,
			3   => 1,
			4   => 1,
			5   => 1,
			6   => 1,
			9   => 1,
			15  => 1,
			26  => 1,
			33  => 1,
			35  => 1,
			36  => 1,
			42  => 1,
			49  => 1,
			62  => 1,
			67  => 1,
			69  => 1,
			70  => 1,
			79  => 1,
			82  => 1,
			88  => 1,
			97  => 1,
			99  => 1,
			105 => 1,
			107 => 1,
			108 => 1,
			112 => 1,
			113 => 1,
			114 => 1,
			115 => 1,
			116 => 1,
			119 => 1,
			125 => 1,
			136 => 1,
			142 => 1,
			145 => 1,
			152 => 1,
			165 => 1,
			174 => 1,
			178 => 1,
			180 => 1,
			181 => 1,
			188 => 1,
			195 => 1,
			208 => 1,
			213 => 1,
			215 => 1,
			216 => 1,
			221 => 1,
			223 => 1,
			229 => 1,
			238 => 1,
			240 => 1,
			246 => 1,
			248 => 1,
			249 => 1,
			253 => 1,
			254 => 1,
			255 => 1,
			256 => 1,
			257 => 1,
			260 => 1,
			266 => 1,
			277 => 1,
			283 => 1,
			286 => 1,
			293 => 1,
			306 => 1,
			315 => 1,
			319 => 1,
			321 => 1,
			322 => 1,
			329 => 1,
			336 => 1,
			349 => 1,
			354 => 1,
			356 => 1,
			357 => 1,
			362 => 1,
			365 => 1,
			371 => 1,
			380 => 1,
			382 => 1,
			388 => 1,
			390 => 1,
			391 => 1,
			395 => 1,
			396 => 1,
			397 => 1,
			398 => 1,
			399 => 1,
			402 => 1,
			408 => 1,
			419 => 1,
			426 => 1,
			433 => 1,
			446 => 1,
			455 => 1,
			459 => 1,
			461 => 1,
			462 => 1,
			469 => 1,
			476 => 1,
			489 => 1,
			492 => 1,
			493 => 1,
			496 => 1,
			502 => 1,
			513 => 1,
			517 => 1,
			519 => 1,
			520 => 1,
			526 => 1,
			533 => 1,
			546 => 1,
			549 => 1,
			550 => 1,
			551 => 1,
			552 => 1,
			555 => 1,
			561 => 1,
			572 => 1,
			579 => 1,
			581 => 1,
			582 => 1,
			587 => 1,
			592 => 1,
			597 => 1,
			602 => 1,
			607 => 1,
			612 => 1,
		);
	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array<int, int> Key is the line number, value is the number of expected warnings.
	 */
	public function getWarningList() {
		return array();
	}
}
