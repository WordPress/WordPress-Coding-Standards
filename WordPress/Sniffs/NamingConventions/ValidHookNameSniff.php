<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Use lowercase letters in action and filter names. Separate words via underscores.
 *
 * This sniff is only testing the hook invoke functions as when using 'add_action'/'add_filter'
 * you can't influence the hook name.
 *
 * Hook names invoked with `do_action_deprecated()` and `apply_filters_deprecated()` are ignored.
 *
 * @link    https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/#naming-conventions
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.10.0
 */
class WordPress_Sniffs_NamingConventions_ValidHookNameSniff implements PHP_CodeSniffer_Sniff {

	/**
	 * Additional word separators.
	 *
	 * This public variable allows providing additional word separators which
	 * will be allowed in hook names via a property in the phpcs.xml config file.
	 *
	 * Example usage:
	 * <rule ref="WordPress.NamingConventions.ValidHookName">
	 *   <properties>
	 *     <property name="additionalWordDelimiters" value="-"/>
	 *   </properties>
	 * </rule>
	 *
	 * Provide several extra delimiters as one string:
	 * <rule ref="WordPress.NamingConventions.ValidHookName">
	 *   <properties>
	 *     <property name="additionalWordDelimiters" value="-/."/>
	 *   </properties>
	 * </rule>
	 *
	 * @var string
	 */
	public $additionalWordDelimiters = '';

	/**
	 * Regular expression to test for correct punctuation of a hook name.
	 *
	 * The placeholder will be replaced by potentially provided additional
	 * word delimiters in the `prepare_regex()` method.
	 *
	 * @var string
	 */
	protected $punctuation_regex = '`[^\w%s]`';

	/**
	 * Register this sniff.
	 *
	 * Prepares the punctuation regex and returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return array(
			T_STRING,
		);

	}

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
		$tokens = $phpcsFile->getTokens();
		$token  = $tokens[ $stackPtr ];
		$regex  = $this->prepare_regex();

		// Check if one of the hook functions was found.
		if ( ! isset( WordPress_Sniff::$hookInvokeFunctions[ $tokens[ $stackPtr ]['content'] ] ) ) {
			return;
		}

		// Ignore deprecated hook names.
		if ( strpos( $tokens[ $stackPtr ]['content'], '_deprecated' ) > 0 ) {
			return;
		}

		$prev = $phpcsFile->findPrevious( T_WHITESPACE, ( $stackPtr - 1 ), null, true );

		if ( false !== $prev ) {
			// Skip sniffing if calling a same-named method, or on function definitions.
			if ( in_array( $tokens[ $prev ]['code'], array( T_FUNCTION, T_DOUBLE_COLON, T_OBJECT_OPERATOR ), true ) ) {
				return;
			}

			// Skip namespaced functions, ie: \foo\bar() not \bar().
			$pprev = $phpcsFile->findPrevious( T_WHITESPACE, ( $prev - 1 ), null, true );
			if ( false !== $pprev && T_NS_SEPARATOR === $tokens[ $prev ]['code'] && T_STRING === $tokens[ $pprev ]['code'] ) {
				return;
			}
		}
		unset( $prev, $pprev );

		/*
		   Ok, so we have a proper hook call, let's find the position of the tokens
		   which together comprise the hook name.
		 */
		$start = $phpcsFile->findNext( array( T_WHITESPACE, T_OPEN_PARENTHESIS ), ( $stackPtr + 1 ), null, true, null, true );
		$open  = $phpcsFile->findNext( T_OPEN_PARENTHESIS, ( $stackPtr + 1 ), null, false, null, true );
		$end   = $phpcsFile->findNext( T_COMMA, ( $start + 1 ), null, false, null, true );
		if ( false === $end || $end > $tokens[ $open ]['parenthesis_closer'] ) {
			$end = $tokens[ $open ]['parenthesis_closer'];
		}
		if ( T_WHITESPACE === $tokens[ ( $end - 1 ) ]['code'] ) {
			$end--;
		}

		$case_errors = 0;
		$underscores = 0;
		$content     = array();
		$expected    = array();

		for ( $i = $start; $i < $end; $i++ ) {
			$content[ $i ]  = $tokens[ $i ]['content'];
			$expected[ $i ] = $tokens[ $i ]['content'];

			if ( in_array( $tokens[ $i ]['code'], array( T_CONSTANT_ENCAPSED_STRING, T_DOUBLE_QUOTED_STRING ), true ) ) {
				/*
				   Here be dragons - a double quoted string can contain extrapolated variables
				   which don't have to comply with these rules.
				 */
				if ( T_DOUBLE_QUOTED_STRING === $tokens[ $i ]['code'] ) {
					$string          = trim( $tokens[ $i ]['content'], '"' );
					$transform       = $this->transform_complex_string( $string, $regex );
					$case_transform  = $this->transform_complex_string( $string, $regex, 'case' );
					$punct_transform = $this->transform_complex_string( $string, $regex, 'punctuation' );
				} else {
					$string          = trim( $tokens[ $i ]['content'], '\'"' );
					$transform       = $this->transform( $string, $regex );
					$case_transform  = $this->transform( $string, $regex, 'case' );
					$punct_transform = $this->transform( $string, $regex, 'punctuation' );
				}

				if ( $string === $transform ) {
					continue;
				}

				if ( T_DOUBLE_QUOTED_STRING === $tokens[ $i ]['code'] ) {
					$expected[ $i ] = '"' . $transform . '"';
				} else {
					$expected[ $i ] = '\'' . $transform . '\'';
				}

				if ( $string !== $case_transform ) {
					$case_errors++;
				}
				if ( $string !== $punct_transform ) {
					$underscores++;
				}
			}
		}

		$data = array(
			implode( '', $expected ),
			implode( '', $content ),
		);

		if ( $case_errors > 0 ) {
			$error = 'Hook names should be lowercase. Expected: %s, but found: %s.';
			$phpcsFile->addError( $error, $stackPtr, 'NotLowercase', $data );
		}
		if ( $underscores > 0 ) {
			$error = 'Words in hook names should be separated using underscores. Expected: %s, but found: %s.';
			$phpcsFile->addWarning( $error, $stackPtr, 'UseUnderscores', $data );
		}

	} // end process()

	/**
	 * Prepare the punctuation regular expression.
	 *
	 * Merges the existing regular expression with potentially provided extra word delimiters to allow.
	 * This is done 'late' and for each found token as otherwise inline `@codingStandardsChangeSetting`
	 * directives would be ignored.
	 *
	 * @return string
	 */
	protected function prepare_regex() {
		$extra = '';
		if ( '' !== $this->additionalWordDelimiters && is_string( $this->additionalWordDelimiters ) ) {
			$extra = preg_quote( $this->additionalWordDelimiters, '`' );
		}

		return sprintf( $this->punctuation_regex, $extra );

	} // end prepare_regex()

	/**
	 * Transform an arbitrary string to lowercase and replace punctuation and spaces with underscores.
	 *
	 * @param string $string         The target string.
	 * @param string $regex          The punctuation regular expression to use.
	 * @param string $transform_type Whether to a partial or complete transform.
	 *                               Valid values are: 'full', 'case', 'punctuation'.
	 * @return string
	 */
	protected function transform( $string, $regex, $transform_type = 'full' ) {

		switch ( $transform_type ) {
			case 'case':
				return strtolower( $string );

			case 'punctuation':
				return preg_replace( $regex, '_', $string );

			case 'full':
			default:
				return preg_replace( $regex, '_', strtolower( $string ) );
		}
	} // end transform()

	/**
	 * Transform a complex string which may contain variable extrapolation.
	 *
	 * @param string $string         The target string.
	 * @param string $regex          The punctuation regular expression to use.
	 * @param string $transform_type Whether to a partial or complete transform.
	 *                               Valid values are: 'full', 'case', 'punctuation'.
	 * @return string
	 */
	protected function transform_complex_string( $string, $regex, $transform_type = 'full' ) {
		$output = preg_split( '`([\{\}\$\[\] ])`', $string, -1, PREG_SPLIT_DELIM_CAPTURE );

		$is_variable = false;
		$has_braces  = false;
		$braces      = 0;

		foreach ( $output as $i => $part ) {
			if ( in_array( $part, array( '$', '{' ), true ) ) {
				$is_variable = true;
				if ( '{' === $part ) {
					$has_braces = true;
					$braces++;
				}
				continue;
			}

			if ( true === $is_variable ) {
				if ( '[' === $part ) {
					$has_braces = true;
					$braces++;
				}
				if ( in_array( $part, array( '}', ']' ), true ) ) {
					$braces--;
				}
				if ( false === $has_braces && ' ' === $part ) {
					$is_variable  = false;
					$output[ $i ] = $this->transform( $part, $regex, $transform_type );
				}

				if ( ( true === $has_braces && 0 === $braces ) && false === in_array( $output[ ( $i + 1 ) ], array( '{', '[' ), true ) ) {
					$has_braces  = false;
					$is_variable = false;
				}
				continue;
			}

			$output[ $i ] = $this->transform( $part, $regex, $transform_type );
		}

		return implode( '', $output );
	} // end transform_complex_string()

} // End class.
