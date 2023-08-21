<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\PHP;

use WordPressCS\WordPress\Sniff;

/**
 * Verifies the correct usage of type cast keywords.
 *
 * Type casts should be:
 * - normalized, i.e. (float) not (real).
 *
 * Additionally, the use of the (unset) and (binary) casts is discouraged.
 *
 * @link https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/#space-usage
 *
 * @since 1.2.0
 * @since 2.0.0 No longer checks that type casts are lowercase or short form.
 *              Relevant PHPCS native sniffs have been included in the rulesets instead.
 */
final class TypeCastsSniff extends Sniff {

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return array(
			\T_DOUBLE_CAST,
			\T_UNSET_CAST,
			\T_BINARY_CAST,
		);
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return void
	 */
	public function process_token( $stackPtr ) {

		$token_code  = $this->tokens[ $stackPtr ]['code'];
		$typecast    = str_replace( ' ', '', $this->tokens[ $stackPtr ]['content'] );
		$typecast_lc = strtolower( $typecast );

		switch ( $token_code ) {
			case \T_DOUBLE_CAST:
				if ( '(float)' !== $typecast_lc ) {
					$fix = $this->phpcsFile->addFixableError(
						'Normalized type keywords must be used; expected "(float)" but found "%s"',
						$stackPtr,
						'DoubleRealFound',
						array( $typecast )
					);

					if ( true === $fix ) {
						$this->phpcsFile->fixer->replaceToken( $stackPtr, '(float)' );
					}
				}
				break;

			case \T_UNSET_CAST:
				$this->phpcsFile->addError(
					'Using the "(unset)" cast is forbidden as the type cast is removed in PHP 8.0. Use the "unset()" language construct instead.',
					$stackPtr,
					'UnsetFound'
				);
				break;

			case \T_BINARY_CAST:
				$this->phpcsFile->addWarning(
					'Using binary casting is strongly discouraged. Found: "%s"',
					$stackPtr,
					'BinaryFound',
					array( $typecast )
				);
				break;
		}
	}
}
