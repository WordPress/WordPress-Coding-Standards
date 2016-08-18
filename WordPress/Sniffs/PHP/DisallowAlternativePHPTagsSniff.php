<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Verifies that no alternative PHP open tags are used.
 *
 * If alternative PHP open tags are found, this sniff can fix both the open and close tags.
 *
 * @link      https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/580
 *
 * @package   WPCS\WordPressCodingStandards
 *
 * @since     0.10.0
 *
 * {@internal If and when the upstream PR https://github.com/squizlabs/PHP_CodeSniffer/pull/1084
 *            would be merged and the WPCS minimum PHPCS version would be upped to the version
 *            that PR is contained in, this sniff and associated unit tests can be replaced by
 *            the upstream sniff Generic.PHP.DisallowAlternativePHPTags.}}
 */
class WordPress_Sniffs_PHP_DisallowAlternativePHPTagsSniff implements PHP_CodeSniffer_Sniff {

	/**
	 * Whether ASP tags are enabled or not.
	 *
	 * @var bool
	 */
	private $asp_tags = false;

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		if ( version_compare( PHP_VERSION, '7.0.0alpha1', '<' ) ) {
			$this->asp_tags = (bool) ini_get( 'asp_tags' );
		}

		return array(
			T_OPEN_TAG,
			T_OPEN_TAG_WITH_ECHO,
			T_INLINE_HTML,
		);

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
		$tokens  = $phpcsFile->getTokens();
		$openTag = $tokens[ $stackPtr ];
		$content = $openTag['content'];

		if ( '' === trim( $content ) ) {
			return;
		}

		if ( T_OPEN_TAG === $openTag['code'] ) {

			if ( '<%' === $content ) {
				$error    = 'ASP style opening tag used; expected "<?php" but found "%s"';
				$closer   = $this->find_closing_tag( $phpcsFile, $tokens, $stackPtr, '%>' );
				$error_id = 'ASPOpenTagFound';

			} elseif ( false !== strpos( $content, '<script ' ) ) {
				$error    = 'Script style opening tag used; expected "<?php" but found "%s"';
				$closer   = $this->find_closing_tag( $phpcsFile, $tokens, $stackPtr, '</script>' );
				$error_id = 'ScriptOpenTagFound';
			}

			if ( isset( $error, $closer, $error_id ) ) {
				$data = array( $content );

				if ( false === $closer ) {
					$phpcsFile->addError( $error, $stackPtr, $error_id, $data );
				} else {
					$fix = $phpcsFile->addFixableError( $error, $stackPtr, $error_id, $data );
					if ( true === $fix ) {
						$this->add_changeset( $phpcsFile, $tokens, $stackPtr, $closer );
					}
				}
			}

			return;
		}

		if ( T_OPEN_TAG_WITH_ECHO === $openTag['code'] && '<%=' === $content ) {
			$error   = 'ASP style opening tag used with echo; expected "<?php echo %s ..." but found "%s %s ..."';
			$nextVar = $phpcsFile->findNext( T_WHITESPACE, ( $stackPtr + 1 ), null, true );
			$snippet = $this->get_snippet( $tokens[ $nextVar ]['content'] );
			$data    = array(
				$snippet,
				$content,
				$snippet,
			);

			$closer = $this->find_closing_tag( $phpcsFile, $tokens, $stackPtr, '%>' );

			if ( false === $closer ) {
				$phpcsFile->addError( $error, $stackPtr, 'ASPShortOpenTagFound', $data );
			} else {
				$fix = $phpcsFile->addFixableError( $error, $stackPtr, 'ASPShortOpenTagFound', $data );
				if ( true === $fix ) {
					$this->add_changeset( $phpcsFile, $tokens, $stackPtr, $closer, true );
				}
			}

			return;
		}

		// Account for incorrect script open tags. The "(?:<s)?" in the regex is to work-around a bug in PHP 5.2.
		if ( T_INLINE_HTML === $openTag['code'] && 1 === preg_match( '`((?:<s)?cript (?:[^>]+)?language=[\'"]?php[\'"]?(?:[^>]+)?>)`i', $content, $match ) ) {
			$error   = 'Script style opening tag used; expected "<?php" but found "%s"';
			$snippet = $this->get_snippet( $content, $match[1] );
			$data    = array( $match[1] . $snippet );

			$phpcsFile->addError( $error, $stackPtr, 'ScriptOpenTagFound', $data );

			return;
		}

		if ( T_INLINE_HTML === $openTag['code'] && false === $this->asp_tags ) {
			if ( false !== strpos( $content, '<%=' ) ) {
				$error   = 'Possible use of ASP style short opening tags detected. Needs manual inspection. Found: %s';
				$snippet = $this->get_snippet( $content, '<%=' );
				$data    = array( '<%=' . $snippet );

				$phpcsFile->addWarning( $error, $stackPtr, 'MaybeASPShortOpenTagFound', $data );

			} elseif ( false !== strpos( $content, '<%' ) ) {
				$error   = 'Possible use of ASP style opening tags detected. Needs manual inspection. Found: %s';
				$snippet = $this->get_snippet( $content, '<%' );
				$data    = array( '<%' . $snippet );

				$phpcsFile->addWarning( $error, $stackPtr, 'MaybeASPOpenTagFound', $data );
			}
		}
	} // end process()

	/**
	 * Get a snippet from a HTML token.
	 *
	 * @param string $content  The content of the HTML token.
	 * @param string $start_at Partial string to use as a starting point for the snippet.
	 * @param int    $length   The target length of the snippet to get. Defaults to 40.
	 * @return string
	 */
	private function get_snippet( $content, $start_at = '', $length = 40 ) {
		$start_pos = 0;

		if ( '' !== $start_at ) {
			$start_pos = strpos( $content, $start_at );
			if ( false !== $start_pos ) {
				$start_pos += strlen( $start_at );
			}
		}

		$snippet = substr( $content, $start_pos, $length );
		if ( ( strlen( $content ) - $start_pos ) > $length ) {
			$snippet .= '...';
		}

		return $snippet;
	}

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

		if ( false !== $closer && trim( $tokens[ $closer ]['content'] ) === $content ) {
			return $closer;
		}

		return false;
	}

	/**
	 * Add a changeset to replace the alternative PHP tags.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile         The file being scanned.
	 * @param array                $tokens            The token stack.
	 * @param int                  $open_tag_pointer  Stack pointer to the PHP open tag.
	 * @param int                  $close_tag_pointer Stack pointer to the PHP close tag.
	 * @param bool                 $echo              Whether to add 'echo' or not.
	 */
	private function add_changeset( PHP_CodeSniffer_File $phpcsFile, $tokens, $open_tag_pointer, $close_tag_pointer, $echo = false ) {
		// Build up the open tag replacement and make sure there's always whitespace behind it.
		$open_replacement = '<?php';
		if ( true === $echo ) {
			$open_replacement .= ' echo';
		}
		if ( T_WHITESPACE !== $tokens[ ( $open_tag_pointer + 1 ) ]['code'] ) {
			$open_replacement .= ' ';
		}

		// Make sure we don't remove any line breaks after the closing tag.
		$regex             = '`' . preg_quote( trim( $tokens[ $close_tag_pointer ]['content'] ) ) . '`';
		$close_replacement = preg_replace( $regex, '?>', $tokens[ $close_tag_pointer ]['content'] );

		$phpcsFile->fixer->beginChangeset();
		$phpcsFile->fixer->replaceToken( $open_tag_pointer, $open_replacement );
		$phpcsFile->fixer->replaceToken( $close_tag_pointer, $close_replacement );
		$phpcsFile->fixer->endChangeset();
	}

} // End class.
