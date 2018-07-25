<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Sniffs\PHP;

use WordPress\Sniff;

/**
 * Discourage the use of the PHP `goto` language construct.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.14.0
 *
 * {@internal This sniff is a duplicate of an upstream PR. Once the minimum PHPCS
 * requirement for WPCS goes up beyond the version in which the upstream PR
 * is merged, this sniff can be safely removed.
 * {@link https://github.com/squizlabs/PHP_CodeSniffer/pull/1664} }}
 */
class DiscourageGotoSniff extends Sniff {

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return array(
			\T_GOTO,
			\T_GOTO_LABEL,
		);
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 */
	public function process_token( $stackPtr ) {

		$this->phpcsFile->addWarning( 'Using the "goto" language construct is discouraged', $stackPtr, 'Found' );
	}

}
