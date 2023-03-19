<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\WP;

use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\Tokens\Collections;
use PHPCSUtils\Utils\Namespaces;
use PHPCSUtils\Utils\ObjectDeclarations;
use WordPressCS\WordPress\Helpers\ContextHelper;
use WordPressCS\WordPress\Sniff;

/**
 * Capital P Dangit!
 *
 * Verify the correct spelling of `WordPress` in text strings, comments and OO and namespace names.
 *
 * @since 0.12.0
 * @since 0.13.0 Class name changed: this class is now namespaced.
 * @since 3.0.0  Now also checks namespace names.
 */
final class CapitalPDangitSniff extends Sniff {

	/**
	 * Regex to match a large number or spelling variations of WordPress in text strings.
	 *
	 * Prevents matches on:
	 * - URLs for wordpress.org/com/net/test/tv.
	 * - `@...` usernames starting with `wordpress`
	 * - email addresses with a domain starting with `wordpress`
	 * - email addresses with a user name ending with `wordpress`
	 * - (most) variable names.
	 * - directory paths containing a folder starting or ending with `wordpress`.
	 * - file names containing `wordpress` for a limited set of extensions.
	 * - `wordpress` prefixed or suffixed with dashes as those are indicators that the
	 *   term is probably used as part of a CSS class, such as `fa-wordpress`
	 *   or filename/path like `class-wordpress-importer.php`.
	 * - back-tick quoted `wordpress`.
	 *
	 * @var string
	 */
	const WP_REGEX = '#(?<![\\\\/\$@`-])\b(Word[ _-]*Pres+)\b(?![@/`-]|\.(?:org|com|net|test|tv)|[^\s<>\'"()]*?\.(?:php|js|css|png|j[e]?pg|gif|pot))#i';

	/**
	 * Regex to match a large number or spelling variations of WordPress in class names.
	 *
	 * @var string
	 */
	const WP_CLASSNAME_REGEX = '`(?:^|_)(Word[_]*Pres+)(?:_|$)`i';

	/**
	 * Comment tokens we want to listen for as they contain text strings.
	 *
	 * @var array
	 */
	private $comment_text_tokens = array(
		\T_DOC_COMMENT        => \T_DOC_COMMENT,
		\T_DOC_COMMENT_STRING => \T_DOC_COMMENT_STRING,
		\T_COMMENT            => \T_COMMENT,
	);

	/**
	 * Combined text string and comment tokens array.
	 *
	 * This property is set in the register() method and used for lookups.
	 *
	 * @var array
	 */
	private $text_and_comment_tokens = array();

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @since 0.12.0
	 *
	 * @return array
	 */
	public function register() {
		// Union the arrays - keeps the array keys.
		$this->text_and_comment_tokens = ( Tokens::$textStringTokens + $this->comment_text_tokens );

		$targets                 = $this->text_and_comment_tokens;
		$targets                += Tokens::$ooScopeTokens;
		$targets[ \T_NAMESPACE ] = \T_NAMESPACE;

		// Also sniff for array tokens to make skipping anything within those more efficient.
		$targets                          += Collections::arrayOpenTokensBC();
		$targets                          += Collections::listTokens();
		$targets[ \T_OPEN_SQUARE_BRACKET ] = \T_OPEN_SQUARE_BRACKET;

		return $targets;
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @since 0.12.0
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return int|void Integer stack pointer to skip forward or void to continue
	 *                  normal file processing.
	 */
	public function process_token( $stackPtr ) {
		/*
		 * Ignore tokens within array and list definitions as well as within
		 * array keys as this is a false positive in 80% of all cases.
		 *
		 * The return values skip to the end of the array.
		 * This prevents the sniff "hanging" on very long configuration arrays.
		 */
		if ( ( \T_ARRAY === $this->tokens[ $stackPtr ]['code']
			|| \T_LIST === $this->tokens[ $stackPtr ]['code'] )
			&& isset( $this->tokens[ $stackPtr ]['parenthesis_closer'] )
		) {
			return $this->tokens[ $stackPtr ]['parenthesis_closer'];
		}

		if ( ( \T_OPEN_SHORT_ARRAY === $this->tokens[ $stackPtr ]['code']
			|| \T_OPEN_SQUARE_BRACKET === $this->tokens[ $stackPtr ]['code'] )
			&& isset( $this->tokens[ $stackPtr ]['bracket_closer'] )
		) {
			return $this->tokens[ $stackPtr ]['bracket_closer'];
		}

		/*
		 * Deal with misspellings in namespace names.
		 * These are not auto-fixable, but need the attention of a developer.
		 */
		if ( \T_NAMESPACE === $this->tokens[ $stackPtr ]['code'] ) {
			$ns_name = Namespaces::getDeclaredName( $this->phpcsFile, $stackPtr );
			if ( empty( $ns_name ) ) {
				// Namespace operator or declaration without name.
				return;
			}

			$levels = explode( '\\', $ns_name );
			foreach ( $levels as $level ) {
				if ( preg_match_all( self::WP_CLASSNAME_REGEX, $level, $matches, \PREG_PATTERN_ORDER ) > 0 ) {
					$misspelled = $this->retrieve_misspellings( $matches[1] );

					if ( ! empty( $misspelled ) ) {
						$this->phpcsFile->addWarning(
							'Please spell "WordPress" correctly. Found: "%s" as part of the namespace name.',
							$stackPtr,
							'MisspelledNamespaceName',
							array( implode( ', ', $misspelled ) )
						);
					}
				}
			}

			return;
		}

		/*
		 * Deal with misspellings in class/interface/trait/enum names.
		 * These are not auto-fixable, but need the attention of a developer.
		 */
		if ( isset( Tokens::$ooScopeTokens[ $this->tokens[ $stackPtr ]['code'] ] ) ) {
			$classname = ObjectDeclarations::getName( $this->phpcsFile, $stackPtr );
			if ( empty( $classname ) ) {
				return;
			}

			if ( preg_match_all( self::WP_CLASSNAME_REGEX, $classname, $matches, \PREG_PATTERN_ORDER ) > 0 ) {
				$misspelled = $this->retrieve_misspellings( $matches[1] );

				if ( ! empty( $misspelled ) ) {
					$this->phpcsFile->addWarning(
						'Please spell "WordPress" correctly. Found: "%s" as part of the class/interface/trait/enum name.',
						$stackPtr,
						'MisspelledClassName',
						array( implode( ', ', $misspelled ) )
					);
				}
			}

			return;
		}

		/*
		 * Deal with misspellings in text strings and documentation.
		 */

		// Ignore content of docblock @link tags.
		if ( \T_DOC_COMMENT_STRING === $this->tokens[ $stackPtr ]['code']
			|| \T_DOC_COMMENT === $this->tokens[ $stackPtr ]['code']
		) {

			$comment_tag = $this->phpcsFile->findPrevious(
				array( \T_DOC_COMMENT_TAG, \T_DOC_COMMENT_OPEN_TAG ),
				( $stackPtr - 1 )
			);
			if ( false !== $comment_tag
				&& \T_DOC_COMMENT_TAG === $this->tokens[ $comment_tag ]['code']
				&& '@link' === $this->tokens[ $comment_tag ]['content']
			) {
				// @link tag, so ignore.
				return;
			}
		}

		// Ignore constant declarations via define().
		if ( ContextHelper::is_in_function_call( $this->phpcsFile, $stackPtr, array( 'define' => true ), true, true ) ) {
			return;
		}

		// Ignore constant declarations using the const keyword.
		$stop_points = array(
			\T_CONST,
			\T_SEMICOLON,
			\T_OPEN_TAG,
			\T_CLOSE_TAG,
			\T_OPEN_CURLY_BRACKET,
		);
		$maybe_const = $this->phpcsFile->findPrevious( $stop_points, ( $stackPtr - 1 ) );
		if ( false !== $maybe_const && \T_CONST === $this->tokens[ $maybe_const ]['code'] ) {
			return;
		}

		$content = $this->tokens[ $stackPtr ]['content'];

		if ( preg_match_all( self::WP_REGEX, $content, $matches, ( \PREG_PATTERN_ORDER | \PREG_OFFSET_CAPTURE ) ) > 0 ) {
			/*
			 * Prevent some typical false positives.
			 */
			if ( isset( $this->text_and_comment_tokens[ $this->tokens[ $stackPtr ]['code'] ] ) ) {
				$offset = 0;
				foreach ( $matches[1] as $key => $match_data ) {
					$next_offset = ( $match_data[1] + \strlen( $match_data[0] ) );

					// Prevent matches on part of a URL.
					if ( preg_match( '`http[s]?://[^\s<>\'"()]*' . preg_quote( $match_data[0], '`' ) . '`', $content, $discard, 0, $offset ) === 1 ) {
						unset( $matches[1][ $key ] );
					} elseif ( preg_match( '`[a-z]+=(["\'])' . preg_quote( $match_data[0], '`' ) . '\1`', $content, $discard, 0, $offset ) === 1 ) {
						// Prevent matches on html attributes like: `value="wordpress"`.
						unset( $matches[1][ $key ] );
					} elseif ( preg_match( '`\\\\\'' . preg_quote( $match_data[0], '`' ) . '\\\\\'`', $content, $discard, 0, $offset ) === 1 ) {
						// Prevent matches on xpath queries and such: `\'wordpress\'`.
						unset( $matches[1][ $key ] );
					} elseif ( preg_match( '`(?:\?|&amp;|&)[a-z0-9_]+=' . preg_quote( $match_data[0], '`' ) . '(?:&|$)`', $content, $discard, 0, $offset ) === 1 ) {
						// Prevent matches on url query strings: `?something=wordpress`.
						unset( $matches[1][ $key ] );
					}

					$offset = $next_offset;
				}

				if ( empty( $matches[1] ) ) {
					return;
				}
			}

			$misspelled = $this->retrieve_misspellings( $matches[1] );

			if ( empty( $misspelled ) ) {
				return;
			}

			$code = 'MisspelledInText';
			if ( isset( Tokens::$commentTokens[ $this->tokens[ $stackPtr ]['code'] ] ) ) {
				$code = 'MisspelledInComment';
			}

			$fix = $this->phpcsFile->addFixableWarning(
				'Please spell "WordPress" correctly. Found %s misspelling(s): %s',
				$stackPtr,
				$code,
				array(
					\count( $misspelled ),
					implode( ', ', $misspelled ),
				)
			);

			if ( true === $fix ) {
				// Apply fixes based on offset to ensure we don't replace false positives.
				$replacement = $content;
				foreach ( $matches[1] as $match ) {
					$replacement = substr_replace( $replacement, 'WordPress', $match[1], \strlen( $match[0] ) );
				}

				$this->phpcsFile->fixer->replaceToken( $stackPtr, $replacement );
			}
		}
	}

	/**
	 * Retrieve a list of misspellings based on an array of matched variations on the target word.
	 *
	 * @param array $match_stack Array of matched variations of the target word.
	 * @return array Array containing only the misspelled variants.
	 */
	protected function retrieve_misspellings( $match_stack ) {
		$misspelled = array();
		foreach ( $match_stack as $match ) {
			// Deal with multi-dimensional arrays when capturing offset.
			if ( \is_array( $match ) ) {
				$match = $match[0];
			}

			if ( 'WordPress' !== $match ) {
				$misspelled[] = $match;
			}
		}

		return $misspelled;
	}
}
