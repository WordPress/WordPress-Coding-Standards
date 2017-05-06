<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Enforces WordPress array indentation for multi-line arrays.
 *
 * @link    https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/#indentation
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.12.0
 */
class WordPress_Sniffs_Arrays_ArrayIndentationSniff extends WordPress_Sniff {

	/**
	 * The --tab-width CLI value that is being used.
	 *
	 * @var int
	 */
	private $tab_width;


	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return array(
			T_ARRAY,
			T_OPEN_SHORT_ARRAY,
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
		if ( ! isset( $this->tab_width ) ) {
			$cli_values = $this->phpcsFile->phpcs->cli->getCommandLineValues();
			if ( ! isset( $cli_values['tabWidth'] ) || 0 === $cli_values['tabWidth'] ) {
				// We have no idea how wide tabs are, so assume 4 spaces for fixing.
				$this->tab_width = 4;
			} else {
				$this->tab_width = $cli_values['tabWidth'];
			}
		}

		/*
		 * Determine the array opener & closer.
		 */
		if ( T_ARRAY === $this->tokens[ $stackPtr ]['code'] ) {
			if ( ! isset( $this->tokens[ $stackPtr ]['parenthesis_opener'] ) ) {
				return; // Live coding.
			}
			$opener = $this->tokens[ $stackPtr ]['parenthesis_opener'];

			if ( ! isset( $this->tokens[ $opener ]['parenthesis_closer'] ) ) {
				return; // Live coding.
			}
			$closer = $this->tokens[ $opener ]['parenthesis_closer'];
		} else {
			// Short array syntax.
			$opener = $stackPtr;

			if ( ! isset( $this->tokens[ $stackPtr ]['bracket_closer'] ) ) {
				return; // Live coding.
			}
			$closer = $this->tokens[ $stackPtr ]['bracket_closer'];
		}

		if ( $this->tokens[ $opener ]['line'] === $this->tokens[ $closer ]['line'] ) {
			// Not interested in single line arrays.
			return;
		}

		/*
		 * Determine the indentation of the line containing the array opener.
		 */
		$indentation = '';
		$column      = 1;
		for ( $i = $stackPtr; $i >= 0; $i-- ) {
			if ( $this->tokens[ $i ]['line'] === $this->tokens[ $stackPtr ]['line'] ) {
				continue;
			}

			if ( T_WHITESPACE === $this->tokens[ ( $i + 1 ) ]['code'] ) {
				// Something weird going on with tabs vs spaces, but this fixes it.
				$indentation = str_replace( '    ', "\t", $this->tokens[ ( $i + 1 ) ]['content'] );
				$column      = $this->tokens[ ( $i + 2 ) ]['column'];
			}
			break;
		}
		unset( $i );

		/*
		 * Check the closing bracket is lined up with the start of the content on the line
		 * containing the array opener.
		 */
		if ( $this->tokens[ $closer ]['column'] !== $column ) {
			$expected = ( $column - 1 );
			$found    = ( $this->tokens[ $closer ]['column'] - 1 );
			$error    = 'Array closer not aligned correctly; expected %s space(s) but found %s';
			$data     = array(
				$expected,
				$found,
			);

			$fix = $this->phpcsFile->addFixableError( $error, $closer, 'CloseBraceNotAligned', $data );
			if ( true === $fix ) {
				if ( 0 === $found ) {
					$this->phpcsFile->fixer->addContent( ( $closer - 1 ), $indentation );
				} else {
					$this->phpcsFile->fixer->replaceToken( ( $closer - 1 ), $indentation );
				}
			}
		}

		$array_items = $this->get_function_call_parameters( $stackPtr );
		if ( empty( $array_items ) ) {
			// Strange, no array items found.
			return;
		}

		$expected_indent = "\t" . $indentation;
		$expected_column = ( $column + $this->tab_width );

		foreach ( $array_items as $item ) {
			// Find the line on which the item starts.
			$first_content = $this->phpcsFile->findNext( PHP_CodeSniffer_Tokens::$emptyTokens, $item['start'], ( $item['end'] + 1 ), true );
			if ( false === $first_content ) {
				continue;
			}

			$whitespace = '';
			if ( 1 !== $this->tokens[ $first_content ]['column'] ) {
				$whitespace = $this->tokens[ ( $first_content - 1 ) ]['content'];

				// If tabs are being converted to spaces by the tokenizer, the
				// original content should be checked instead of the converted content.
				if ( isset( $this->tokens[ ( $first_content - 1 ) ]['orig_content'] ) ) {
					$whitespace = $this->tokens[ ( $first_content - 1 ) ]['orig_content'];
				}
			}

			if ( $whitespace !== $expected_indent ) {
				$expected = ( $expected_column - 1 );
				$found    = ( $this->tokens[ $first_content ]['column'] - 1 );
				$error    = 'Array item not aligned correctly; expected %s spaces but found %s';
				$data     = array(
					$expected,
					$found,
				);

				$fix = $this->phpcsFile->addFixableError( $error, $first_content, 'ItemNotAligned', $data );
				if ( true === $fix ) {
					if ( 0 === $found ) {
						$this->phpcsFile->fixer->addContent( ( $first_content - 1 ), $expected_indent );
					} else {
						$this->phpcsFile->fixer->replaceToken( ( $first_content - 1 ), $expected_indent );
					}
				}
			}
		} // End foreach().

	} // End process_token().

} // End class.
