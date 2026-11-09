<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\Helpers\WPDBTrait;

use PHP_CodeSniffer\Files\File;
use WordPressCS\WordPress\Helpers\WPDBTrait;

/**
 * Test helper class that exposes the WPDBTrait::is_wpdb_method_call() method for testing.
 *
 * @since 3.4.0
 */
final class WPDBTraitWrapper {

	use WPDBTrait;

	/**
	 * Stack pointer to the method name.
	 *
	 * @var int|null
	 */
	public $methodPtr = null;

	/**
	 * Stack pointer to the opening parenthesis of the method call.
	 *
	 * @var int|null
	 */
	public $i = null;

	/**
	 * Stack pointer to the end of the first parameter.
	 *
	 * @var int|null
	 */
	public $end = null;

	/**
	 * Wrapper for is_wpdb_method_call() for testing purposes.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile     The file being scanned.
	 * @param int                         $stackPtr      The index of the $wpdb variable or wpdb class name token.
	 * @param array                       $targetMethods Array of methods. Key(s) should be method name in lowercase.
	 *
	 * @return bool Whether this is a $wpdb method call.
	 */
	public function invoke_is_wpdb_method_call( File $phpcsFile, $stackPtr, array $targetMethods ) {
		return $this->is_wpdb_method_call( $phpcsFile, $stackPtr, $targetMethods );
	}
}
