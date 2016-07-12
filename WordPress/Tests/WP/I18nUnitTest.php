<?php
/**
 * Unit test class for the EnqueuedResources sniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Shady Sharaf <shady@x-team.com>
 */
class WordPress_Tests_WP_I18nUnitTest extends AbstractSniffUnitTest {

	protected function setUp() {
		// @todo Should be possible via self::$phpcs->setSniffProperty( 'WordPress_Sniffs_WP_I18nSniff', 'text_domain', 'my-slug' );
		WordPress_Sniffs_WP_I18nSniff::$text_domain_override = 'my-slug';
		parent::setUp();
	}

	/**
	 * Returns the lines where errors should occur.
	 *
	 * The key of the array should represent the line number and the value
	 * should represent the number of errors that should occur on that line.
	 *
	 * @return array(int => int)
	 */
	public function getErrorList() {
		return array(
			3 => 1,
			6 => 1,
			9 => 1,
			11 => 1,
			13 => 1,
			15 => 1,
			17 => 1,
			19 => 1,
			21 => 1,
			23 => 1,
			25 => 1,
			27 => 1,
			33 => 1,
			35 => 1,
			37 => 1,
			39 => 1,
			41 => 1,
			43 => 1,
			45 => 1,
			47 => 1,
			48 => 1,
			50 => 1,
			52 => 1,
			53 => 1,
			55 => 1,
			56 => 2,
			58 => 1,
			59 => 1,
			60 => 1,
			62 => 1,
			63 => 2,
			65 => 1,
			66 => 1,
			67 => 1,
			72 => 1,
			74 => 1,
			75 => 1,
			76 => 1,
			77 => 1,
			78 => 1,
			93 => 1,
			95 => 2,
			100 => 1,
			101 => 1,
			102 => 1,
			103 => 1,
			105 => 1,
			106 => 1,
			107 => 1,
		);
	} // end getErrorList()

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * The key of the array should represent the line number and the value
	 * should represent the number of warnings that should occur on that line.
	 *
	 * @return array(int => int)
	 */
	public function getWarningList() {
		return array(
			69 => 1,
			70 => 1,
			100 => 1,
			101 => 1,
			102 => 1,
			103 => 1,
		);
	} // end getWarningList()

} // end class.
