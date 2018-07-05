<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Sniffs\WhiteSpace;

use WordPress\Sniff;
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * Check & fix whitespace on the inside of arbitrary parentheses.
 *
 * Arbitrary parentheses are those which are not owned by a function (call), array or control structure.
 * Spacing on the outside is not checked on purpose as this would too easily conflict with other spacing rules.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.14.0
 *
 * {@internal This sniff is a duplicate of the same sniff as pulled upstream.
 * Once the upstream sniff has been merged and the minimum WPCS PHPCS requirement has gone up to
 * the version in which the sniff was merged, this version can be safely removed.
 * {@link https://github.com/squizlabs/PHP_CodeSniffer/pull/1701} }}
 */
class ArbitraryParenthesesSpacingSniff extends Sniff {

	/**
	 * The number of spaces desired on the inside of the parentheses.
	 *
	 * @since 0.14.0
	 *
	 * @var integer
	 */
	public $spacingInside = 0;

	/**
	 * Allow newlines instead of spaces.
	 *
	 * @since 0.14.0
	 *
	 * @var boolean
	 */
	public $ignoreNewlines = false;

	/**
	 * Tokens which when they precede an open parenthesis indicate
	 * that this is a type of structure this sniff should ignore.
	 *
	 * @since 0.14.0
	 *
	 * @var array
	 */
	private $ignoreTokens = array();

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @since 0.14.0
	 *
	 * @return array
	 */
	public function register() {

		$this->ignoreTokens                            = Tokens::$functionNameTokens;
		$this->ignoreTokens[ \T_VARIABLE ]             = \T_VARIABLE;
		$this->ignoreTokens[ \T_CLOSE_PARENTHESIS ]    = \T_CLOSE_PARENTHESIS;
		$this->ignoreTokens[ \T_CLOSE_CURLY_BRACKET ]  = \T_CLOSE_CURLY_BRACKET;
		$this->ignoreTokens[ \T_CLOSE_SQUARE_BRACKET ] = \T_CLOSE_SQUARE_BRACKET;
		$this->ignoreTokens[ \T_CLOSE_SHORT_ARRAY ]    = \T_CLOSE_SHORT_ARRAY;
		$this->ignoreTokens[ \T_ANON_CLASS ]           = \T_ANON_CLASS;
		$this->ignoreTokens[ \T_USE ]                  = \T_USE;
		$this->ignoreTokens[ \T_LIST ]                 = \T_LIST;
		$this->ignoreTokens[ \T_DECLARE ]              = \T_DECLARE;

		// The below two tokens have been added to the Tokens::$functionNameTokens array in PHPCS 3.1.0,
		// so they can be removed once the minimum PHPCS requirement of WPCS has gone up.
		$this->ignoreTokens[ \T_SELF ]   = \T_SELF;
		$this->ignoreTokens[ \T_STATIC ] = \T_STATIC;

		// Language constructs where the use of parentheses should be discouraged instead.
		$this->ignoreTokens[ \T_THROW ]      = \T_THROW;
		$this->ignoreTokens[ \T_YIELD ]      = \T_YIELD;
		$this->ignoreTokens[ \T_YIELD_FROM ] = \T_YIELD_FROM;
		$this->ignoreTokens[ \T_CLONE ]      = \T_CLONE;

		return array(
			\T_OPEN_PARENTHESIS,
			\T_CLOSE_PARENTHESIS,
		);
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @since 0.14.0
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return int|void Integer stack pointer to skip forward or void to continue
	 *                  normal file processing.
	 */
	public function process_token( $stackPtr ) {

		if ( isset( $this->tokens[ $stackPtr ]['parenthesis_owner'] ) ) {
			// This parenthesis is owned by a function/control structure etc.
			return;
		}

		// More checking for the type of parenthesis we *don't* want to handle.
		$opener = $stackPtr;
		if ( \T_CLOSE_PARENTHESIS === $this->tokens[ $stackPtr ]['code'] ) {
			if ( ! isset( $this->tokens[ $stackPtr ]['parenthesis_opener'] ) ) {
				// Parse error.
				return;
			}

			$opener = $this->tokens[ $stackPtr ]['parenthesis_opener'];
		}

		$preOpener = $this->phpcsFile->findPrevious( Tokens::$emptyTokens, ( $opener - 1 ), null, true );
		if ( false !== $preOpener && isset( $this->ignoreTokens[ $this->tokens[ $preOpener ]['code'] ] ) ) {
			// Function or language construct call.
			return;
		}

		/*
		 * Check for empty parentheses.
		 */
		if ( \T_OPEN_PARENTHESIS === $this->tokens[ $stackPtr ]['code']
			&& isset( $this->tokens[ $stackPtr ]['parenthesis_closer'] )
		) {
			$nextNonEmpty = $this->phpcsFile->findNext( \T_WHITESPACE, ( $stackPtr + 1 ), null, true );
			if ( $nextNonEmpty === $this->tokens[ $stackPtr ]['parenthesis_closer'] ) {
				$this->phpcsFile->addWarning( 'Empty set of arbitrary parentheses found.', $stackPtr, 'FoundEmpty' );

				return ( $this->tokens[ $stackPtr ]['parenthesis_closer'] + 1 );
			}
		}

		/*
		 * Check the spacing on the inside of the parentheses.
		 */
		$this->spacingInside = (int) $this->spacingInside;

		if ( \T_OPEN_PARENTHESIS === $this->tokens[ $stackPtr ]['code']
			&& isset( $this->tokens[ ( $stackPtr + 1 ) ], $this->tokens[ ( $stackPtr + 2 ) ] )
		) {
			$nextToken = $this->tokens[ ( $stackPtr + 1 ) ];

			if ( \T_WHITESPACE !== $nextToken['code'] ) {
				$inside = 0;
			} else {
				if ( $this->tokens[ ( $stackPtr + 2 ) ]['line'] !== $this->tokens[ $stackPtr ]['line'] ) {
					$inside = 'newline';
				} else {
					$inside = $nextToken['length'];
				}
			}

			if ( $this->spacingInside !== $inside
				&& ( 'newline' !== $inside || false === $this->ignoreNewlines )
			) {
				$error = 'Expected %s space after open parenthesis; %s found';
				$data  = array(
					$this->spacingInside,
					$inside,
				);
				$fix   = $this->phpcsFile->addFixableError( $error, $stackPtr, 'SpaceAfterOpen', $data );

				if ( true === $fix ) {
					$expected = '';
					if ( $this->spacingInside > 0 ) {
						$expected = str_repeat( ' ', $this->spacingInside );
					}

					if ( 0 === $inside ) {
						if ( '' !== $expected ) {
							$this->phpcsFile->fixer->addContent( $stackPtr, $expected );
						}
					} elseif ( 'newline' === $inside ) {
						$this->phpcsFile->fixer->beginChangeset();
						for ( $i = ( $stackPtr + 2 ); $i < $this->phpcsFile->numTokens; $i++ ) {
							if ( \T_WHITESPACE !== $this->tokens[ $i ]['code'] ) {
								break;
							}
							$this->phpcsFile->fixer->replaceToken( $i, '' );
						}
						$this->phpcsFile->fixer->replaceToken( ( $stackPtr + 1 ), $expected );
						$this->phpcsFile->fixer->endChangeset();
					} else {
						$this->phpcsFile->fixer->replaceToken( ( $stackPtr + 1 ), $expected );
					}
				}
			}
		}

		if ( \T_CLOSE_PARENTHESIS === $this->tokens[ $stackPtr ]['code']
			&& isset( $this->tokens[ ( $stackPtr - 1 ) ], $this->tokens[ ( $stackPtr - 2 ) ] )
		) {
			$prevToken = $this->tokens[ ( $stackPtr - 1 ) ];

			if ( \T_WHITESPACE !== $prevToken['code'] ) {
				$inside = 0;
			} else {
				if ( $this->tokens[ ( $stackPtr - 2 ) ]['line'] !== $this->tokens[ $stackPtr ]['line'] ) {
					$inside = 'newline';
				} else {
					$inside = $prevToken['length'];
				}
			}

			if ( $this->spacingInside !== $inside
				&& ( 'newline' !== $inside || false === $this->ignoreNewlines )
			) {
				$error = 'Expected %s space before close parenthesis; %s found';
				$data  = array(
					$this->spacingInside,
					$inside,
				);
				$fix   = $this->phpcsFile->addFixableError( $error, $stackPtr, 'SpaceBeforeClose', $data );

				if ( true === $fix ) {
					$expected = '';
					if ( $this->spacingInside > 0 ) {
						$expected = str_repeat( ' ', $this->spacingInside );
					}

					if ( 0 === $inside ) {
						if ( '' !== $expected ) {
							$this->phpcsFile->fixer->addContentBefore( $stackPtr, $expected );
						}
					} elseif ( 'newline' === $inside ) {
						$this->phpcsFile->fixer->beginChangeset();
						for ( $i = ( $stackPtr - 2 ); $i > 0; $i-- ) {
							if ( \T_WHITESPACE !== $this->tokens[ $i ]['code'] ) {
								break;
							}
							$this->phpcsFile->fixer->replaceToken( $i, '' );
						}
						$this->phpcsFile->fixer->replaceToken( ( $stackPtr - 1 ), $expected );
						$this->phpcsFile->fixer->endChangeset();
					} else {
						$this->phpcsFile->fixer->replaceToken( ( $stackPtr - 1 ), $expected );
					}
				}
			}
		}
	}

}
