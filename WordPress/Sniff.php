<?php

/**
 * Represents a PHP_CodeSniffer sniff for sniffing WordPress coding standards.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 */

/**
 * Represents a PHP_CodeSniffer sniff for sniffing WordPress coding standards.
 *
 * Provides a bootstrap for the sniffs, to reduce code duplication.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @version   0.4.0
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
abstract class WordPress_Sniff implements PHP_CodeSniffer_Sniff {

	/**
	 * The current file being sniffed.
	 *
	 * @since 0.4.0
	 *
	 * @var PHP_CodeSniffer_File
	 */
	protected $phpcsFile;

	/**
	 * The list of tokens in the current file being sniffed.
	 *
	 * @since 0.4.0
	 *
	 * @var array
	 */
	protected $tokens;

	/**
	 * A list of superglobals that incorporate user input.
	 *
	 * @since 0.4.0
	 *
	 * @var string[]
	 */
	protected static $input_superglobals = array( '$_COOKIE', '$_GET', '$_FILE', '$_POST', '$_REQUEST', '$_SERVER' );

	/**
	 * Initialize the class for the current process.
	 *
	 * This method must be called by child classes before using many of the methods
	 * below.
	 *
	 * @since 0.4.0
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile The file currently being processed.
	 */
	protected function init( PHP_CodeSniffer_File $phpcsFile ) {
		$this->phpcsFile = $phpcsFile;
		$this->tokens = $phpcsFile->getTokens();
	}

	/**
	 * Get the last pointer in a line.
	 *
	 * @since 0.4.0
	 *
	 * @param integer $stackPtr The position of the current token in the stack passed
	 *                          in $tokens.
	 *
	 * @return integer Position of the last pointer on that line.
	 */
	protected function get_last_ptr_on_line( $stackPtr ) {

		$tokens = $this->tokens;
		$currentLine = $tokens[ $stackPtr ]['line'];
		$nextPtr = $stackPtr + 1;

		while ( isset( $tokens[ $nextPtr ] ) && $tokens[ $nextPtr ]['line'] === $currentLine ) {
			$nextPtr++;
			// Do nothing, we just want the last token of the line.
		}

		// We've made it to the next line, back up one to the last in the previous line.
		// We do this for micro-optimization of the above loop.
		$lastPtr = $nextPtr - 1;

		return $lastPtr;
	}

	/**
	 * Find whitelisting comment.
	 *
	 * Comment must be at the end of the line, and use // format.
	 * It can be prefixed or suffixed with anything e.g. "foobar" will match:
	 * ... // foobar okay
	 * ... // WPCS: foobar whitelist.
	 *
	 * There is an exception, and that is when PHP is being interspersed with HTML.
	 * In that case, the comment should come at the end of the statement (right
	 * before the closing tag, ?>). For example:
	 *
	 * <input type="text" id="<?php echo $id; // XSS OK ?>" />
	 *
	 * @since 0.4.0
	 *
	 * @param string  $comment  Comment to find.
	 * @param integer $stackPtr The position of the current token in the stack passed
	 *                          in $tokens.
	 *
	 * @return boolean True if whitelisting comment was found, false otherwise.
	 */
	protected function has_whitelist_comment( $comment, $stackPtr ) {

		$end_of_line = $lastPtr = $this->get_last_ptr_on_line( $stackPtr );

		// There is a findEndOfStatement() method, but it considers more tokens than
		// we need to here.
		$end_of_statement = $this->phpcsFile->findNext(
			array( T_CLOSE_TAG, T_SEMICOLON )
			, $stackPtr
		);

		// Check at the end of the statement if it comes before the end of the line.
		if ( $end_of_statement < $end_of_line ) {

			// If the statement was ended by a semicolon, we find the next non-
			// whitespace token. If the semicolon was left out and it was terminated
			// by an ending tag, we need to look backwards.
			if ( T_SEMICOLON === $this->tokens[ $end_of_statement ]['code'] ) {
				$lastPtr = $this->phpcsFile->findNext( T_WHITESPACE, $end_of_statement + 1, null, true );
			} else {
				$lastPtr = $this->phpcsFile->findPrevious( T_WHITESPACE, $end_of_statement - 1, null, true );
			}
		}

		$last = $this->tokens[ $lastPtr ];

		if ( T_COMMENT === $last['code'] ) {
			return preg_match( '#' . preg_quote( $comment ) . '#i', $last['content'] );
		} else {
			return false;
		}
	}
}

// EOF
