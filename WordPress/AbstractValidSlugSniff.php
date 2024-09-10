<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress;

use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\Tokens\Collections;
use PHPCSUtils\Utils\PassedParameters;
use PHPCSUtils\Utils\TextStrings;
use WordPressCS\WordPress\AbstractFunctionParameterSniff;

/**
 * Validates names passed to a function call.
 *
 * Checks slugs for the presence of invalid characters, excessive length,
 * and reserved keywords.
 */
abstract class AbstractValidSlugSniff extends AbstractFunctionParameterSniff {

	/**
	 * Slug type. E.g. 'post type' for a post type slug.
	 *
	 * @var string
	 */
	protected $slug_type;

	/**
	 * Plural of the slug type. E.g. 'post types' for a post type slug.
	 *
	 * @var string
	 */
	protected $slug_type_plural;

	/**
	 * Max length of a slug is limited by the SQL field.
	 *
	 * @var int
	 */
	protected $max_length;

	/**
	 * Regex to validate the characters that can be used as the slug.
	 *
	 * @var string
	 */
	protected $valid_characters;

	/**
	 * Array of reserved names for a specific slug type.
	 *
	 * @var array<string, true> Key is reserved name, value irrelevant.
	 */
	protected $reserved_names;

	/**
	 * All valid tokens for the slug parameter.
	 *
	 * Set in `register()`.
	 *
	 * @var array<int|string, int|string>
	 */
	private $valid_tokens = array();

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 */
	public function __construct() {
		$this->target_functions = $this->get_target_functions();
		$this->slug_type        = $this->get_slug_type();
		$this->slug_type_plural = $this->get_slug_type_plural();
		$this->valid_characters = $this->get_valid_characters();
		$this->max_length       = $this->get_max_length();
		$this->reserved_names   = $this->get_reserved_names();
	}

	/**
	 * Retrieve function and parameter(s) pairs this sniff is looking for.
	 *
	 * The parameter or an array of parameters keyed by target function.
	 * An array of names is supported to allow for functions for which the
	 * parameter names have undergone name changes over time.
	 *
	 * @return array<string, string|array<string>> Function parameter(s) pairs.
	 */
	abstract protected function get_target_functions();

	/**
	 * Retrieve the slug type.
	 *
	 * @return string The slug type.
	 */
	abstract protected function get_slug_type();

	/**
	 * Retrieve the plural slug type.
	 *
	 * @return string The plural slug type.
	 */
	abstract protected function get_slug_type_plural();

	/**
	 * Retrieve the regex to validate the characters that can be used as
	 * the slug.
	 *
	 * @return string Regular expression.
	 */
	abstract protected function get_valid_characters();

	/**
	 * Retrieve the max length of a slug.
	 *
	 * The length is limited by the SQL field.
	 *
	 * @return int The maximum length of a slug.
	 */
	abstract protected function get_max_length();

	/**
	 * Retrieve the reserved names which can not be used by themes and plugins.
	 *
	 * @return array<string, true> Key is reserved name, value irrelevant.
	 */
	abstract protected function get_reserved_names();

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		$this->valid_tokens = Tokens::$textStringTokens + Tokens::$heredocTokens + Tokens::$emptyTokens;
		return parent::register();
	}

	/**
	 * Process the parameter of a matched function.
	 *
	 * Errors on invalid names when reserved keywords are used,
	 * the name is too long, or contains invalid characters.
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
		$slug_param = PassedParameters::getParameterFromStack(
			$parameters,
			1,
			$this->target_functions[ $matched_content ]
		);
		if ( false === $slug_param || '' === $slug_param['clean'] ) {
			// Error for using empty slug.
			$this->phpcsFile->addError(
				'%s() called without a %s slug. The slug must be a non-empty string.',
				false === $slug_param ? $stackPtr : $slug_param['start'],
				'Empty',
				array(
					$matched_content,
					$this->slug_type,
				)
			);
			return;
		}

		$string_start = $this->phpcsFile->findNext( Collections::textStringStartTokens(), $slug_param['start'], ( $slug_param['end'] + 1 ) );
		$string_pos   = $this->phpcsFile->findNext( Tokens::$textStringTokens, $slug_param['start'], ( $slug_param['end'] + 1 ) );

		$has_invalid_tokens = $this->phpcsFile->findNext( $this->valid_tokens, $slug_param['start'], ( $slug_param['end'] + 1 ), true );
		if ( false !== $has_invalid_tokens || false === $string_pos ) {
			// Check for non string based slug parameter (we cannot determine if this is valid).
			$this->phpcsFile->addWarning(
				'The %s slug is not a string literal. It is not possible to automatically determine the validity of this slug. Found: %s.',
				$stackPtr,
				'NotStringLiteral',
				array(
					$this->slug_type,
					$slug_param['clean'],
				),
				3
			);
			return;
		}

		$slug = TextStrings::getCompleteTextString( $this->phpcsFile, $string_start );
		if ( isset( Tokens::$heredocTokens[ $this->tokens[ $string_start ]['code'] ] ) ) {
			// Trim off potential indentation from PHP 7.3 flexible heredoc/nowdoc content.
			$slug = ltrim( $slug );
		}

		// Warn for dynamic parts in the slug parameter.
		if ( 'T_DOUBLE_QUOTED_STRING' === $this->tokens[ $string_pos ]['type']
			|| ( 'T_HEREDOC' === $this->tokens[ $string_pos ]['type']
			&& strpos( $this->tokens[ $string_pos ]['content'], '$' ) !== false )
		) {
			$this->phpcsFile->addWarning(
				'The %s slug may, or may not, get too long with dynamic contents and could contain invalid characters. Found: "%s".',
				$string_pos,
				'PartiallyDynamic',
				array(
					$this->slug_type,
					$slug,
				)
			);
			$slug = TextStrings::stripEmbeds( $slug );
		}

		if ( preg_match( $this->valid_characters, $slug ) === 0 ) {
			// Error for invalid characters.
			$this->phpcsFile->addError(
				'%s() called with invalid %s "%s". %s contains invalid characters. Only lowercase alphanumeric characters, dashes, and underscores are allowed.',
				$string_pos,
				'InvalidCharacters',
				array(
					$matched_content,
					$this->slug_type,
					ucfirst( $this->slug_type ),
					$slug,
				)
			);
		}

		if ( isset( $this->reserved_names[ $slug ] ) ) {
			// Error for using reserved slug names.
			$this->phpcsFile->addError(
				'%s() called with reserved %s "%s". Reserved %s should not be used as they interfere with the functioning of WordPress itself.',
				$string_pos,
				'Reserved',
				array(
					$matched_content,
					$this->slug_type,
					$slug,
					$this->slug_type_plural,
				)
			);
		} elseif ( stripos( $slug, 'wp_' ) === 0 ) {
			// Error for using reserved slug prefix.
			$this->phpcsFile->addError(
				'The %s passed to %s() uses a prefix reserved for WordPress itself. Found: "%s".',
				$string_pos,
				'ReservedPrefix',
				array(
					$this->slug_type,
					$matched_content,
					$slug,
				)
			);
		}

		// Error for slugs that are too long.
		if ( strlen( $slug ) > $this->max_length ) {
			$this->phpcsFile->addError(
				'A %s slug must not exceed %d characters. Found: "%s" (%d characters).',
				$string_pos,
				'TooLong',
				array(
					$this->slug_type,
					$this->max_length,
					$slug,
					strlen( $slug ),
				)
			);
		}
	}
}
