<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Sniffs\Arrays;

use PHP_CodeSniffer_File as File;

/**
 * Enforces WordPress array format, based upon Squiz code.
 *
 * @link    https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/#indentation
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since      0.1.0
 * @since      0.5.0  Now extends `Squiz_Sniffs_Arrays_ArrayDeclarationSniff`.
 * @since      0.11.0 The additional single-line array checks have been moved to their own
 *                    sniff WordPress.Arrays.ArrayDeclarationSpacing.
 *                    This class now only contains a slimmed down version of the upstream sniff.
 * @since      0.13.0 Class name changed: this class is now namespaced.
 *
 * @deprecated 0.13.0 This sniff has now been deprecated. Most checks which were previously
 *                    contained herein had recently been excluded in favour of dedicated
 *                    sniffs with higher precision. The last remaining checks which were not
 *                    already covered elsewhere have been moved to the `ArrayDeclarationSpacing`
 *                    sniff.
 *                    This class is left here to prevent breaking custom rulesets which refer
 *                    to this sniff.
 */
class ArrayDeclarationSniff {

	/**
	 * Don't use.
	 *
	 * @deprecated 0.13.0
	 *
	 * @return int[]
	 */
	public function register() {
		return array();
	}

	/**
	 * Don't use.
	 *
	 * @deprecated 0.13.0
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile A PHP_CodeSniffer file.
	 * @param int                         $stackPtr  The position of the token.
	 *
	 * @return void
	 */
	public function process( File $phpcsFile, $stackPtr ) {}

} // End class.
