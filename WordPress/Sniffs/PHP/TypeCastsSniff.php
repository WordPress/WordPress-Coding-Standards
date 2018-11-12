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
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * Verifies the correct usage of type cast keywords.
 *
 * Type casts should be:
 * - lowercase;
 * - short form, i.e. (bool) not (boolean);
 * - normalized, i.e. (float) not (real).
 *
 * Additionally, the use of the (unset) and (binary) casts is discouraged.
 *
 * @link    https://make.wordpress.org/core/handbook/best-practices/....
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   1.2.0
 */
class TypeCastsSniff extends Sniff {

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return Tokens::$castTokens;
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

		$this->phpcsFile->recordMetric( $stackPtr, 'Typecast encountered', $typecast );

		switch ( $token_code ) {
			case \T_BOOL_CAST:
				if ( '(bool)' !== $typecast_lc ) {
					$fix = $this->phpcsFile->addFixableError(
						'Short form type keywords must be used; expected "(bool)" but found "%s"',
						$stackPtr,
						'LongBoolFound',
						array( $typecast )
					);

					if ( true === $fix ) {
						$this->phpcsFile->fixer->replaceToken( $stackPtr, '(bool)' );
					}
				}
				break;

			case \T_INT_CAST:
				if ( '(int)' !== $typecast_lc ) {
					$fix = $this->phpcsFile->addFixableError(
						'Short form type keywords must be used; expected "(int)" but found "%s"',
						$stackPtr,
						'LongIntFound',
						array( $typecast )
					);

					if ( true === $fix ) {
						$this->phpcsFile->fixer->replaceToken( $stackPtr, '(int)' );
					}
				}
				break;

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
				$this->phpcsFile->addWarning(
					'Using the "(unset)" cast is strongly discouraged. Use the "unset()" language construct or assign "null" as the value to the variable instead.',
					$stackPtr,
					'UnsetFound'
				);
				break;

			case \T_STRING_CAST:
			case \T_BINARY_CAST:
				if ( \T_STRING_CAST === $token_code && '(binary)' !== $typecast_lc ) {
					break;
				}

				$this->phpcsFile->addWarning(
					'Using binary casting is strongly discouraged. Found: "%s"',
					$stackPtr,
					'BinaryFound',
					array( $typecast )
				);
				break;
		}

		/*
		 * {@internal Once the minimum PHPCS version has gone up to PHPCS 3.3.0+, the lowercase
		 * check below can be removed in favour of adding the `Generic.PHP.LowerCaseType` sniff
		 * to the ruleset.
		 * Note: the `register()` function also needs adjusting in that case to only register the
		 * targetted type casts above and the metrics recording should probably be adjusted as well.
		 * The above mentioned Generic sniff records metrics about the case of typecasts, so we
		 * don't need to worry about those no longer being recorded. They will be, just slightly
		 * differently.}}
		 */
		if ( $typecast_lc !== $typecast ) {
			$data = array(
				$typecast_lc,
				$typecast,
			);

			$fix = $this->phpcsFile->addFixableError(
				'PHP type casts must be lowercase; expected "%s" but found "%s"',
				$stackPtr,
				'NonLowercaseFound',
				$data
			);
			if ( true === $fix ) {
				$this->phpcsFile->fixer->replaceToken( $stackPtr, $typecast_lc );
			}
		}
	}

}
