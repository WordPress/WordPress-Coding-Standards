<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\DB;

use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\Tokens\Collections;
use PHPCSUtils\Utils\TextStrings;
use WordPressCS\WordPress\Helpers\ContextHelper;
use WordPressCS\WordPress\Helpers\FormattingFunctionsHelper;
use WordPressCS\WordPress\Helpers\WPDBTrait;
use WordPressCS\WordPress\Sniff;

/**
 * Sniff for prepared SQL.
 *
 * Makes sure that variables aren't directly interpolated into SQL statements.
 *
 * @link https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/#formatting-sql-statements
 *
 * @since 0.8.0
 * @since 0.13.0 Class name changed: this class is now namespaced.
 * @since 1.0.0  This sniff has been moved from the `WP` category to the `DB` category.
 */
final class PreparedSQLSniff extends Sniff {

	use WPDBTrait;

	/**
	 * The lists of $wpdb methods.
	 *
	 * @since 0.8.0
	 * @since 0.11.0 Changed from static to non-static.
	 *
	 * @var array<string, bool>
	 */
	protected $methods = array(
		'get_var'     => true,
		'get_col'     => true,
		'get_row'     => true,
		'get_results' => true,
		'prepare'     => true,
		'query'       => true,
	);

	/**
	 * Functions that escape values for use in SQL queries.
	 *
	 * @since 0.9.0
	 * @since 0.11.0 Changed from public static to protected non-static.
	 * @since 3.0.0  - Moved from the Sniff class to this class.
	 *               - The property visibility has changed from `protected` to `private`.
	 *
	 * @var array<string, bool>
	 */
	private $SQLEscapingFunctions = array(
		'absint'      => true,
		'esc_sql'     => true,
		'floatval'    => true,
		'intval'      => true,
		'like_escape' => true,
	);

	/**
	 * Functions whose output is automatically escaped for use in SQL queries.
	 *
	 * @since 0.9.0
	 * @since 0.11.0 Changed from public static to protected non-static.
	 * @since 3.0.0  - Moved from the Sniff class to this class.
	 *               - The property visibility has changed from `protected` to `private`.
	 *
	 * @var array<string, bool>
	 */
	private $SQLAutoEscapedFunctions = array(
		'count' => true,
	);

	/**
	 * Tokens that we don't flag when they are found in a $wpdb method call.
	 *
	 * This token array is augmented from within the register() method.
	 *
	 * @since 0.9.0
	 * @since 3.0.0 The property visibility has changed from `protected` to `private`.
	 *
	 * @var array
	 */
	private $ignored_tokens = array(
		\T_STRING_CONCAT            => true,
		\T_CONSTANT_ENCAPSED_STRING => true,
		\T_COMMA                    => true,
		\T_LNUMBER                  => true,
		\T_DNUMBER                  => true,
		\T_NS_SEPARATOR             => true,
	);

	/**
	 * A loop pointer.
	 *
	 * It is a property so that we can access it in all of our methods.
	 *
	 * @since 0.9.0
	 *
	 * @var int
	 */
	protected $i;

	/**
	 * The loop end marker.
	 *
	 * It is a property so that we can access it in all of our methods.
	 *
	 * @since 0.9.0
	 *
	 * @var int
	 */
	protected $end;

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @since 0.8.0
	 *
	 * @return array
	 */
	public function register() {
		// Enrich the array of tokens which can be safely ignored.
		$this->ignored_tokens += Tokens::$bracketTokens;
		$this->ignored_tokens += Tokens::$heredocTokens;
		$this->ignored_tokens += Tokens::$castTokens;
		$this->ignored_tokens += Tokens::$arithmeticTokens;
		$this->ignored_tokens += Collections::incrementDecrementOperators();
		$this->ignored_tokens += Collections::objectOperators();
		$this->ignored_tokens += Tokens::$emptyTokens;

		// The contents of heredoc tokens needs to be examined.
		unset( $this->ignored_tokens[ \T_HEREDOC ] );

		return array(
			\T_VARIABLE,
			\T_STRING,
		);
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @since 0.8.0
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return int|void Integer stack pointer to skip forward or void to continue
	 *                  normal file processing.
	 */
	public function process_token( $stackPtr ) {

		if ( ! $this->is_wpdb_method_call( $this->phpcsFile, $stackPtr, $this->methods ) ) {
			return;
		}

		for ( $this->i; $this->i < $this->end; $this->i++ ) {

			if ( isset( $this->ignored_tokens[ $this->tokens[ $this->i ]['code'] ] ) ) {
				continue;
			}

			if ( \T_DOUBLE_QUOTED_STRING === $this->tokens[ $this->i ]['code']
				|| \T_HEREDOC === $this->tokens[ $this->i ]['code']
			) {

				$bad_variables = array_filter(
					TextStrings::getEmbeds( $this->tokens[ $this->i ]['content'] ),
					static function ( $symbol ) {
						return preg_match( '`^\{?\$\{?wpdb\??->`', $symbol ) !== 1;
					}
				);

				foreach ( $bad_variables as $bad_variable ) {
					$this->phpcsFile->addError(
						'Use placeholders and $wpdb->prepare(); found interpolated variable %s at %s',
						$this->i,
						'InterpolatedNotPrepared',
						array(
							$bad_variable,
							$this->tokens[ $this->i ]['content'],
						)
					);
				}
				continue;
			}

			if ( \T_VARIABLE === $this->tokens[ $this->i ]['code'] ) {
				if ( '$wpdb' === $this->tokens[ $this->i ]['content'] ) {
					$this->is_wpdb_method_call( $this->phpcsFile, $this->i, $this->methods );
					continue;
				}

				if ( ContextHelper::is_safe_casted( $this->phpcsFile, $this->i ) ) {
					continue;
				}
			}

			if ( \T_STRING === $this->tokens[ $this->i ]['code'] ) {

				if (
					isset( $this->SQLEscapingFunctions[ $this->tokens[ $this->i ]['content'] ] )
					|| isset( $this->SQLAutoEscapedFunctions[ $this->tokens[ $this->i ]['content'] ] )
				) {

					// Find the opening parenthesis.
					$opening_paren = $this->phpcsFile->findNext( Tokens::$emptyTokens, ( $this->i + 1 ), null, true );

					if ( false !== $opening_paren
						&& \T_OPEN_PARENTHESIS === $this->tokens[ $opening_paren ]['code']
						&& isset( $this->tokens[ $opening_paren ]['parenthesis_closer'] )
					) {
						// Skip past to the end of the function call.
						$this->i = $this->tokens[ $opening_paren ]['parenthesis_closer'];
						continue;
					}
				} elseif ( FormattingFunctionsHelper::is_formatting_function( $this->tokens[ $this->i ]['content'] ) ) {
					continue;
				}
			}

			$this->phpcsFile->addError(
				'Use placeholders and $wpdb->prepare(); found %s',
				$this->i,
				'NotPrepared',
				array( $this->tokens[ $this->i ]['content'] )
			);
		}

		return $this->end;
	}
}
