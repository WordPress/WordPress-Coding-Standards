<?php
/**
 * WordPress Coding Standard.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     https://make.wordpress.org/core/handbook/best-practices/coding-standards/
 */

/**
 * Makes sure that no alternative PHP open tags are used.
 *
 * @link     https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/580
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Juliette Reinders Folmer <wpplugins_nospam@adviesenzo.nl>
 */
class WordPress_Sniffs_PHP_DisallowAlternativeOpenTagSniff implements PHP_CodeSniffer_Sniff {

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		$tokens = array(
			T_OPEN_TAG,
			T_OPEN_TAG_WITH_ECHO,
		);

		$asp_enabled   = (boolean) ini_get( 'asp_tags' );
		$short_enabled = (boolean) ini_get( 'short_open_tag' );

		if ( false === $asp_enabled || ( true === $asp_enabled && false === $short_enabled ) ) {
			$tokens[] = T_INLINE_HTML;
		}

		return $tokens;

	} // end register()

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
	 * @param int                  $stackPtr  The position of the current token
	 *                                        in the stack passed in $tokens.
	 *
	 * @return void
	 */
	public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {
		$tokens        = $phpcsFile->getTokens();
		$openTag       = $tokens[ $stackPtr ];
		$asp_enabled   = (boolean) ini_get( 'asp_tags' );
		$short_enabled = (boolean) ini_get( 'short_open_tag' );

		if ( T_OPEN_TAG === $openTag['code'] ) {
			if ( '<%' === $openTag['content'] ) {
				$error = 'ASP style opening tag used; expected "<?php" but found "%s"';
				$data  = array( $openTag['content'] );

				$closer = $this->find_closing_tag( $phpcsFile, $tokens, $stackPtr, '%>' );

				if ( false === $closer ) {
					$phpcsFile->addError( $error, $stackPtr, 'ASPOpenTagFound', $data );
				} else {
					$fix = $phpcsFile->addFixableError( $error, $stackPtr, 'ASPOpenTagFound', $data );
					if ( true === $fix ) {
						$this->add_changeset( $phpcsFile, $stackPtr, $closer );
					}
				}
			}

			if ( '<script language="php">' === $openTag['content'] ) {
				$error = 'Script style opening tag used; expected "<?php" but found "%s"';
				$data  = array( $openTag['content'] );

				$closer = $this->find_closing_tag( $phpcsFile, $tokens, $stackPtr, '</script>' );

				if ( false === $closer ) {
					$phpcsFile->addError( $error, $stackPtr, 'ScriptOpenTagFound', $data );
				} else {
					$fix = $phpcsFile->addFixableError( $error, $stackPtr, 'ScriptOpenTagFound', $data );
					if ( true === $fix ) {
						$this->add_changeset( $phpcsFile, $stackPtr, $closer );
					}
				}
			}
		}

		if ( T_OPEN_TAG_WITH_ECHO === $openTag['code'] && '<%=' === $openTag['content'] ) {
			$error   = 'ASP style opening tag used with echo; expected "<?php echo %s ..." but found "%s %s ..."';
			$nextVar = $tokens[ $phpcsFile->findNext( PHP_CodeSniffer_Tokens::$emptyTokens, ( $stackPtr + 1 ), null, true ) ];
			$data    = array(
				$nextVar['content'],
				$openTag['content'],
				$nextVar['content'],
			);

			$closer = $this->find_closing_tag( $phpcsFile, $tokens, $stackPtr, '%>' );

			if ( false === $closer ) {
				$phpcsFile->addError( $error, $stackPtr, 'ASPShortOpenTagFound', $data );
			} else {
				$fix = $phpcsFile->addFixableError( $error, $stackPtr, 'ASPShortOpenTagFound', $data );
				if ( true === $fix ) {
					$this->add_changeset( $phpcsFile, $stackPtr, $closer );
				}
			}
		}

		if ( ( true === $asp_enabled && false === $short_enabled ) && ( T_INLINE_HTML === $openTag['code'] && 0 === strpos( $openTag['content'], '<%=' ) ) ) {

			$error = 'Possible use of ASP style short opening tags. Needs manual inspection. Found: %s';
			$data  = array( $this->get_snippet( $openTag['content'] ) );
			$phpcsFile->addWarning( $error, $stackPtr, 'MaybeASPShortOpenTagFound', $data );
		}

		if ( false === $asp_enabled && T_INLINE_HTML === $openTag['code'] ) {

			$data  = array( $this->get_snippet( $openTag['content'] ) );

			if ( 0 === strpos( $openTag['content'], '<%=' ) ) {
				$error = 'Possible use of ASP style short opening tags. Needs manual inspection. Found: %s';
				$phpcsFile->addWarning( $error, $stackPtr, 'MaybeASPShortOpenTagFound', $data );
			} elseif ( 0 === strpos( $openTag['content'], '<%' ) ) {
				$error = 'Possible use of ASP style opening tags. Needs manual inspection. Found: %s';
				$phpcsFile->addWarning( $error, $stackPtr, 'MaybeASPOpenTagFound', $data );
			} elseif ( 0 === strpos( $openTag['content'], '<script language="php">' ) ) {
				$error = 'Script style opening tag used; expected "<?php" but found "%s"';
				$phpcsFile->addError( $error, $stackPtr, 'ScriptOpenTagFound', $data );
			}
		}

	} // end process()

	/**
	 * Get a snippet from a HTML token.
	 *
	 * @param string $content The content of the HTML token.
	 * @param int    $length  The target length of the snippet to get. Defaults to 40.
	 * @return string
	 */
	private function get_snippet( $content, $length = 40 ) {
		$snippet = substr( $content, 0, $length );
		if ( strlen( $content ) > $length ) {
			$snippet .= '...';
		}
		return $snippet;
	} // end get_snippet()

	/**
	 * Try and find a matching PHP closing tag.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
	 * @param array                $tokens    The token stack.
	 * @param int                  $stackPtr  The position of the current token
	 *                                        in the stack passed in $tokens.
	 * @param string               $content   The expected content of the closing tag to match the opener.
	 * @return int|false Pointer to the position in the stack for the closing tag or false if not found.
	 */
	private function find_closing_tag( PHP_CodeSniffer_File $phpcsFile, $tokens, $stackPtr, $content ) {
		$closer = $phpcsFile->findNext( T_CLOSE_TAG, ( $stackPtr + 1 ) );

		if ( false !== $closer ) {
			if ( $content === $tokens[ $closer ]['content'] ) {
				return $closer;
			}
		}

		return false;
	} // end find_closing_tag()

	/**
	 * Add a changeset to replace the alternative PHP tags.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile         The file being scanned.
	 * @param int                  $open_tag_pointer  Stack pointer to the PHP open tag.
	 * @param int                  $close_tag_pointer Stack pointer to the PHP close tag.
	 */
	private function add_changeset( $phpcsFile, $open_tag_pointer, $close_tag_pointer ) {
		$phpcsFile->fixer->beginChangeset();
		$phpcsFile->fixer->replaceToken( $open_tag_pointer, '<?php' );
		$phpcsFile->fixer->replaceToken( $close_tag_pointer, '?>' );
		$phpcsFile->fixer->endChangeset();
	} // end add_changeset()

} // end class
