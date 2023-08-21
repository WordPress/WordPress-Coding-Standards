<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\NamingConventions;

use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\Utils\TextStrings;
use WordPressCS\WordPress\AbstractFunctionParameterSniff;
use WordPressCS\WordPress\Helpers\WPHookHelper;

/**
 * Use lowercase letters in action and filter names. Separate words via underscores.
 *
 * This sniff is only testing the hook invoke functions as when using 'add_action'/'add_filter'
 * you can't influence the hook name.
 *
 * Hook names invoked with `do_action_deprecated()` and `apply_filters_deprecated()` are ignored.
 *
 * @link https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/#naming-conventions
 *
 * @since 0.10.0
 * @since 0.11.0 Extends the WordPressCS native `AbstractFunctionParameterSniff` class.
 * @since 0.13.0 Class name changed: this class is now namespaced.
 */
class ValidHookNameSniff extends AbstractFunctionParameterSniff {

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
	 * Groups of functions to restrict.
	 *
	 * @since 0.11.0
	 *
	 * @return array
	 */
	public function getGroups() {
		// Only retrieve functions which are not used for deprecated hooks.
		$this->target_functions = WPHookHelper::get_functions( false );

		return parent::getGroups();
	}

	/**
	 * Process the parameters of a matched function.
	 *
	 * @since 0.11.0
	 *
	 * @param int    $stackPtr        The position of the current token in the stack.
	 * @param string $group_name      The name of the group which was matched.
	 * @param string $matched_content The token content (function name) which was matched
	 *                                in lowercase.
	 * @param array  $parameters      Array with information about the parameters.
	 *
	 * @return void
	 */
	public function process_parameters( $stackPtr, $group_name, $matched_content, $parameters ) {

		$hook_name_param = WPHookHelper::get_hook_name_param( $matched_content, $parameters );
		if ( false === $hook_name_param ) {
			return;
		}

		$regex = $this->prepare_regex();

		$case_errors    = 0;
		$underscores    = 0;
		$content        = array();
		$expected       = array();
		$last_non_empty = null;

		for ( $i = $hook_name_param['start']; $i <= $hook_name_param['end']; $i++ ) {
			// Skip past comment tokens.
			if ( isset( Tokens::$commentTokens[ $this->tokens[ $i ]['code'] ] ) ) {
				continue;
			}

			$content[ $i ]  = $this->tokens[ $i ]['content'];
			$expected[ $i ] = $this->tokens[ $i ]['content'];

			// Skip past potential variable array access: `$var['key']`.
			if ( \T_VARIABLE === $this->tokens[ $i ]['code'] ) {
				do {
					$open_bracket = $this->phpcsFile->findNext( Tokens::$emptyTokens, ( $i + 1 ), null, true );
					if ( false === $open_bracket
						|| \T_OPEN_SQUARE_BRACKET !== $this->tokens[ $open_bracket ]['code']
						|| ! isset( $this->tokens[ $open_bracket ]['bracket_closer'] )
					) {
						$last_non_empty = $i;
						continue 2;
					}

					$i = $this->tokens[ $open_bracket ]['bracket_closer'];

				} while ( isset( $this->tokens[ $i ] ) && $i <= $hook_name_param['end'] );

				$last_non_empty = $i;
				continue;
			}

			// Skip over parameters passed to function calls.
			if ( \T_OPEN_PARENTHESIS === $this->tokens[ $i ]['code']
				&& ( \T_STRING === $this->tokens[ $last_non_empty ]['code']
				|| \T_VARIABLE === $this->tokens[ $last_non_empty ]['code'] )
				&& isset( $this->tokens[ $i ]['parenthesis_closer'] )
			) {
				$i              = $this->tokens[ $i ]['parenthesis_closer'];
				$last_non_empty = $i;
				continue;
			}

			// Skip past non text string tokens.
			if ( isset( Tokens::$stringTokens[ $this->tokens[ $i ]['code'] ] ) === false ) {
				$last_non_empty = $i;
				continue;
			}

			$last_non_empty = $i;
			$string         = TextStrings::stripQuotes( $this->tokens[ $i ]['content'] );

			/*
			 * Here be dragons - a double quoted string can contain extrapolated variables
			 * which don't have to comply with these rules.
			 */
			if ( \T_DOUBLE_QUOTED_STRING === $this->tokens[ $i ]['code'] ) {
				$transform       = $this->transform_complex_string( $string, $regex );
				$case_transform  = $this->transform_complex_string( $string, $regex, 'case' );
				$punct_transform = $this->transform_complex_string( $string, $regex, 'punctuation' );
			} else {
				$transform       = $this->transform( $string, $regex );
				$case_transform  = $this->transform( $string, $regex, 'case' );
				$punct_transform = $this->transform( $string, $regex, 'punctuation' );
			}

			if ( $string === $transform ) {
				continue;
			}

			if ( \T_DOUBLE_QUOTED_STRING === $this->tokens[ $i ]['code'] ) {
				$expected[ $i ] = '"' . $transform . '"';
			} else {
				$expected[ $i ] = '\'' . $transform . '\'';
			}

			if ( $string !== $case_transform ) {
				++$case_errors;
			}
			if ( $string !== $punct_transform ) {
				++$underscores;
			}
		}

		$first_non_empty = $this->phpcsFile->findNext(
			Tokens::$emptyTokens,
			$hook_name_param['start'],
			( $hook_name_param['end'] + 1 ),
			true
		);

		$data = array(
			trim( implode( '', $expected ) ),
			trim( implode( '', $content ) ),
		);

		if ( $case_errors > 0 ) {
			$error = 'Hook names should be lowercase. Expected: %s, but found: %s.';
			$this->phpcsFile->addError( $error, $first_non_empty, 'NotLowercase', $data );
		}

		if ( $underscores > 0 ) {
			$error = 'Words in hook names should be separated using underscores. Expected: %s, but found: %s.';
			$this->phpcsFile->addWarning( $error, $first_non_empty, 'UseUnderscores', $data );
		}
	}

	/**
	 * Prepare the punctuation regular expression.
	 *
	 * Merges the existing regular expression with potentially provided extra word delimiters to allow.
	 * This is done 'late' and for each found token as otherwise inline `phpcs:set` directives
	 * would be ignored.
	 *
	 * @return string
	 */
	protected function prepare_regex() {
		$extra = '';
		if ( '' !== $this->additionalWordDelimiters && \is_string( $this->additionalWordDelimiters ) ) {
			$extra = preg_quote( $this->additionalWordDelimiters, '`' );
		}

		return sprintf( $this->punctuation_regex, $extra );
	}

	/**
	 * Transform an arbitrary string to lowercase and replace punctuation and spaces with underscores.
	 *
	 * @param string $text_string    The target string.
	 * @param string $regex          The punctuation regular expression to use.
	 * @param string $transform_type Whether to do a partial or complete transform.
	 *                               Valid values are: 'full', 'case', 'punctuation'.
	 * @return string
	 */
	protected function transform( $text_string, $regex, $transform_type = 'full' ) {

		switch ( $transform_type ) {
			case 'case':
				return strtolower( $text_string );

			case 'punctuation':
				return preg_replace( $regex, '_', $text_string );

			case 'full':
			default:
				return preg_replace( $regex, '_', strtolower( $text_string ) );
		}
	}

	/**
	 * Transform a complex string which may contain variable extrapolation.
	 *
	 * @param string $text_string    The target string.
	 * @param string $regex          The punctuation regular expression to use.
	 * @param string $transform_type Whether to do a partial or complete transform.
	 *                               Valid values are: 'full', 'case', 'punctuation'.
	 * @return string
	 */
	protected function transform_complex_string( $text_string, $regex, $transform_type = 'full' ) {
		$plain_text = TextStrings::stripEmbeds( $text_string );
		$embeds     = TextStrings::getEmbeds( $text_string );

		$transformed_text = $this->transform( $plain_text, $regex, $transform_type );

		// Inject the embeds back into the text string.
		foreach ( $embeds as $offset => $embed ) {
			$transformed_text = substr_replace( $transformed_text, $embed, $offset, 0 );
		}

		return $transformed_text;
	}
}
