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
use PHPCSUtils\Utils\Arrays;
use PHPCSUtils\Utils\Numbers;
use PHPCSUtils\Utils\PassedParameters;
use PHPCSUtils\Utils\TextStrings;
use WordPressCS\WordPress\AbstractFunctionParameterSniff;

/**
 * This checks that the 4th ($ver) parameter is set for all enqueued resources and that the 5th ($in_footer) parameter
 * is set for wp_register_script() and wp_enqueue_script().
 *
 * If a source ($src) value is passed, then version ($ver) needs to have non-falsy value.
 * If a source ($src) value is passed, then it is recommended to explicitly set the $in_footer parameter.
 *
 * @link https://developer.wordpress.org/reference/functions/wp_register_script/
 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_script/
 * @link https://developer.wordpress.org/reference/functions/wp_register_style/
 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_style/
 *
 * @since 1.0.0
 */
final class EnqueuedResourceParametersSniff extends AbstractFunctionParameterSniff {

	/**
	 * The group name for this group of functions.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $group_name = 'Enqueued';

	/**
	 * List of enqueued functions that need to be checked for use of the in_footer and version arguments.
	 *
	 * @since 1.0.0
	 *
	 * @var array<string, true> Key is function name, value irrelevant.
	 */
	protected $target_functions = array(
		'wp_register_script' => true,
		'wp_enqueue_script'  => true,
		'wp_register_style'  => true,
		'wp_enqueue_style'   => true,
	);

	/**
	 * False + T_NS_SEPARATOR + the empty tokens array.
	 *
	 * This array is enriched with the $emptyTokens array in the register() method.
	 *
	 * @var array<int|string, int|string>
	 */
	private $false_tokens = array(
		\T_FALSE        => \T_FALSE,
		\T_NS_SEPARATOR => \T_NS_SEPARATOR, // Needed to handle fully qualified \false (PHPCS 3.x).
	);

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * Overloads and calls the parent method to allow for adding additional tokens to the
	 * $false_tokens property.
	 *
	 * @return array
	 */
	public function register() {
		$this->false_tokens += Tokens::$emptyTokens;

		return parent::register();
	}

	/**
	 * Process the parameters of a matched function.
	 *
	 * @since 1.0.0
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
		// Check to see if a source ($src) is specified.
		$src_param = PassedParameters::getParameterFromStack( $parameters, 2, 'src' );
		if ( false === $src_param ) {
			return;
		}

		/*
		 * Version Check: Check to make sure the version is set explicitly.
		 */

		$version_param = PassedParameters::getParameterFromStack( $parameters, 4, 'ver' );

		$error_ptr = $stackPtr;
		if ( false !== $version_param ) {
			$error_ptr = $this->phpcsFile->findNext( Tokens::$emptyTokens, $version_param['start'], ( $version_param['end'] + 1 ), true );
			if ( false === $error_ptr ) {
				$error_ptr = $version_param['start'];
			}
		}

		if ( false === $version_param || strtolower( ltrim( $version_param['clean'], '\\' ) ) === 'null' ) {
			$type = 'script';
			if ( strpos( $matched_content, '_style' ) !== false ) {
				$type = 'style';
			}

			$this->phpcsFile->addWarning(
				'Resource version not set in call to %s(). This means new versions of the %s may not always be loaded due to browser caching.',
				$error_ptr,
				'MissingVersion',
				array( $matched_content, $type )
			);
			// The version argument should have a non-false value.
		} elseif ( $this->is_falsy( $version_param['start'], $version_param['end'] ) ) {
			$this->phpcsFile->addError(
				'Version parameter is not explicitly set or has been set to an equivalent of "false" for %s; ' .
				'This means that the WordPress core version will be used which is not recommended for plugin or theme development.',
				$error_ptr,
				'NoExplicitVersion',
				array( $matched_content )
			);
		}

		/*
		 * In footer Check
		 *
		 * Check to make sure that $in_footer is explicitly set.
		 * Warn the user if it is not set.
		 *
		 * Only wp_register_script and wp_enqueue_script need this check,
		 * as this parameter is not available to wp_register_style and wp_enqueue_style.
		 */
		if ( 'wp_register_script' !== $matched_content && 'wp_enqueue_script' !== $matched_content ) {
			return;
		}

		$infooter_param = PassedParameters::getParameterFromStack( $parameters, 5, 'in_footer' );
		if ( false === $infooter_param ) {
			// If in footer is not set, throw a warning about the default.
			$this->phpcsFile->addWarning(
				'In footer ($in_footer) is not set explicitly %s; ' .
				'It is recommended to load scripts in the footer. Please set this value to `true` to load it in the footer, or explicitly `false` if it should be loaded in the header.',
				$stackPtr,
				'NotInFooter',
				array( $matched_content )
			);
		}
	}

	/**
	 * Determine if a range has a falsy value.
	 *
	 * Only a limited set of values is recognized as falsy:
	 *   - Boolean false.
	 *   - An integer or float equal to zero.
	 *   - A text string with the content `'0'` or `''` (single or double-quoted, heredoc, or nowdoc).
	 *   - An empty array.
	 *
	 * @param int $start The position to start looking from.
	 * @param int $end   The position to stop looking (inclusive).
	 *
	 * @return bool True if the parameter is falsy.
	 *              False if the parameter is not falsy or when it
	 *              couldn't be reliably determined.
	 */
	protected function is_falsy( $start, $end ) {
		// Find anything excluding the false tokens.
		$has_non_false = $this->phpcsFile->findNext( $this->false_tokens, $start, ( $end + 1 ), true );
		// If no non-false tokens are found, we are good.
		if ( false === $has_non_false ) {
			return true;
		}

		$target_ptr = $this->phpcsFile->findNext( Tokens::$emptyTokens, $start, ( $end + 1 ), true );

		// An array only evaluates to false when it is empty.
		if ( isset( Collections::arrayOpenTokensBC()[ $this->tokens[ $target_ptr ]['code'] ] ) ) {
			$open_close = Arrays::getOpenClose( $this->phpcsFile, $target_ptr );
			if ( false === $open_close ) {
				// Short list assignment, not an array.
				return false;
			}

			// Bail if there is any non-empty token in the $ver parameter after the array, as that's a more complex
			// expression which can't be reliably evaluated.
			$next_after_array = $this->phpcsFile->findNext(
				Tokens::$emptyTokens,
				( $open_close['closer'] + 1 ),
				( $end + 1 ),
				true
			);

			if ( false !== $next_after_array ) {
				return false;
			}

			$first_non_empty_in_array = $this->phpcsFile->findNext(
				Tokens::$emptyTokens,
				( $open_close['opener'] + 1 ),
				$open_close['closer'],
				true
			);

			return ( false === $first_non_empty_in_array );
		}

		// Check if it is a '0' or '' string.
		if ( isset( Collections::textStringStartTokens()[ $this->tokens[ $target_ptr ]['code'] ] ) ) {
			if ( \T_DOUBLE_QUOTED_STRING === $this->tokens[ $target_ptr ]['code'] ) {
				// No need to examine as it will never match/can't be determined.
				return false;
			}

			$valid_tokens = array( \T_CONSTANT_ENCAPSED_STRING ) + Tokens::$heredocTokens + Tokens::$emptyTokens;
			if ( false !== $this->phpcsFile->findNext( $valid_tokens, $start, ( $end + 1 ), true ) ) {
				// Bail if the $ver parameter is more than a single text string.
				return false;
			}

			$content = TextStrings::getCompleteTextString( $this->phpcsFile, $target_ptr );

			return '0' === $content || '' === $content;
		}

		// The int/float check below only handles a single literal token, so bail if there is more than one non-empty token.
		if ( false !== $this->phpcsFile->findNext( Tokens::$emptyTokens, ( $target_ptr + 1 ), ( $end + 1 ), true ) ) {
			return false;
		}

		// Check if it is an int or float equal to zero.
		if ( \T_LNUMBER === $this->tokens[ $target_ptr ]['code'] || \T_DNUMBER === $this->tokens[ $target_ptr ]['code'] ) {
			$number_info = Numbers::getCompleteNumber( $this->phpcsFile, $target_ptr );

			return 0.0 === (float) $number_info['decimal'];
		}

		return false;
	}
}
