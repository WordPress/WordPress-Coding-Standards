<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Sniffs\WP;

use WordPress\Sniff;

/**
 * Sniff for prepared SQL.
 *
 * Makes sure that variables aren't directly interpolated into SQL statements.
 *
 * @link    https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/#formatting-sql-statements
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.8.0
 * @since   0.13.0 Class name changed: this class is now namespaced.
 */
class PreparedSQLSniff extends Sniff {

	/**
	 * The lists of $wpdb methods.
	 *
	 * @since 0.8.0
	 * @since 0.11.0 Changed from static to non-static.
	 *
	 * @var array
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
	 * Tokens that we don't flag when they are found in a $wpdb method call.
	 *
	 * @since 0.9.0
	 *
	 * @var array
	 */
	protected $ignored_tokens = array(
		T_OBJECT_OPERATOR          => true,
		T_OPEN_PARENTHESIS         => true,
		T_CLOSE_PARENTHESIS        => true,
		T_WHITESPACE               => true,
		T_STRING_CONCAT            => true,
		T_CONSTANT_ENCAPSED_STRING => true,
		T_OPEN_SQUARE_BRACKET      => true,
		T_CLOSE_SQUARE_BRACKET     => true,
		T_COMMA                    => true,
		T_LNUMBER                  => true,
		T_START_HEREDOC            => true,
		T_END_HEREDOC              => true,
		T_START_NOWDOC             => true,
		T_NOWDOC                   => true,
		T_END_NOWDOC               => true,
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
		return array(
			T_VARIABLE,
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

		// Check for $wpdb variable.
		if ( '$wpdb' !== $this->tokens[ $stackPtr ]['content'] ) {
			return;
		}

		if ( ! $this->is_wpdb_method_call( $stackPtr ) ) {
			return;
		}

		if ( $this->has_whitelist_comment( 'unprepared SQL', $stackPtr ) ) {
			return;
		}

		for ( $this->i; $this->i < $this->end; $this->i++ ) {

			if ( isset( $this->ignored_tokens[ $this->tokens[ $this->i ]['code'] ] ) ) {
				continue;
			}

			if ( T_DOUBLE_QUOTED_STRING === $this->tokens[ $this->i ]['code']
				|| T_HEREDOC === $this->tokens[ $this->i ]['code']
			) {

				$bad_variables = array_filter(
					$this->get_interpolated_variables( $this->tokens[ $this->i ]['content'] ),
					function ( $symbol ) {
						return ( 'wpdb' !== $symbol );
					}
				);

				foreach ( $bad_variables as $bad_variable ) {
					$this->phpcsFile->addError(
						'Use placeholders and $wpdb->prepare(); found interpolated variable $%s at %s',
						$this->i,
						'NotPrepared',
						array(
							$bad_variable,
							$this->tokens[ $this->i ]['content'],
						)
					);
				}
				continue;
			}

			if ( T_VARIABLE === $this->tokens[ $this->i ]['code'] ) {
				if ( '$wpdb' === $this->tokens[ $this->i ]['content'] ) {
					$this->is_wpdb_method_call( $this->i );
					continue;
				}
			}

			if ( T_STRING === $this->tokens[ $this->i ]['code'] ) {

				if (
					isset( $this->SQLEscapingFunctions[ $this->tokens[ $this->i ]['content'] ] )
					|| isset( $this->SQLAutoEscapedFunctions[ $this->tokens[ $this->i ]['content'] ] )
				) {

					// Find the opening parenthesis.
					$opening_paren = $this->phpcsFile->findNext( T_WHITESPACE, ( $this->i + 1 ), null, true, null, true );

					if (
						$opening_paren
						&& T_OPEN_PARENTHESIS === $this->tokens[ $opening_paren ]['code']
						&& isset( $this->tokens[ $opening_paren ]['parenthesis_closer'] )
					) {
						// Skip past the end of the function.
						$this->i = $this->tokens[ $opening_paren ]['parenthesis_closer'];
						continue;
					}
				} elseif ( isset( $this->formattingFunctions[ $this->tokens[ $this->i ]['content'] ] ) ) {
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

	} // End process_token().

	/**
	 * Checks whether this is a call to a $wpdb method that we want to sniff.
	 *
	 * The $i and $end properties are automatically set to correspond to the start
	 * and end of the method call. The $i property is also set if this is not a
	 * method call but rather the use of a $wpdb property.
	 *
	 * @since 0.8.0
	 * @since 0.9.0 The return value is now always boolean. The $end and $i member
	 *              vars are automatically updated.
	 *
	 * @param int $stackPtr The index of the $wpdb variable.
	 *
	 * @return bool Whether this is a $wpdb method call.
	 */
	protected function is_wpdb_method_call( $stackPtr ) {

		// Check that this is a method call.
		$is_object_call = $this->phpcsFile->findNext( T_OBJECT_OPERATOR, ( $stackPtr + 1 ), null, false, null, true );
		if ( false === $is_object_call ) {
			return false;
		}

		$methodPtr = $this->phpcsFile->findNext( array( T_WHITESPACE ), ( $is_object_call + 1 ), null, true, null, true );
		$method = $this->tokens[ $methodPtr ]['content'];

		// Find the opening parenthesis.
		$opening_paren = $this->phpcsFile->findNext( T_WHITESPACE, ( $methodPtr + 1 ), null, true, null, true );

		if ( false === $opening_paren ) {
			return false;
		}

		$this->i = $opening_paren;

		if ( T_OPEN_PARENTHESIS !== $this->tokens[ $opening_paren ]['code'] ) {
			return false;
		}

		// Check that this is one of the methods that we are interested in.
		if ( ! isset( $this->methods[ $method ] ) ) {
			return false;
		}

		// Find the end of the first parameter.
		$this->end = $this->phpcsFile->findEndOfStatement( $opening_paren + 1 );

		if ( T_COMMA !== $this->tokens[ $this->end ]['code'] ) {
			$this->end += 1;
		}

		return true;
	} // End is_wpdb_method_call().

} // End class.
