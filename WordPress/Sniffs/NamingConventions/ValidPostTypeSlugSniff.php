<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\NamingConventions;

use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\Tokens\Collections;
use PHPCSUtils\Utils\PassedParameters;
use PHPCSUtils\Utils\TextStrings;
use WordPressCS\WordPress\AbstractFunctionParameterSniff;

/**
 * Validates post type names.
 *
 * Checks the post type slug for invalid characters, long function names
 * and reserved names.
 *
 * @link https://developer.wordpress.org/reference/functions/register_post_type/
 *
 * @since 2.2.0
 */
final class ValidPostTypeSlugSniff extends AbstractFunctionParameterSniff {

	/**
	 * Max length of a post type name is limited by the SQL field.
	 *
	 * @since 2.2.0
	 *
	 * @var int
	 */
	const POST_TYPE_MAX_LENGTH = 20;

	/**
	 * Regex to validate the characters that can be used as the post type slug.
	 *
	 * @link https://developer.wordpress.org/reference/functions/register_post_type/
	 * @since 2.2.0
	 * @since 3.0.0 Renamed from `POST_TYPE_CHARACTER_WHITELIST` to `VALID_POST_TYPE_CHARACTERS`.
	 *
	 * @var string
	 */
	const VALID_POST_TYPE_CHARACTERS = '/^[a-z0-9_-]+$/';

	/**
	 * Array of functions that must be checked.
	 *
	 * @since 2.2.0
	 *
	 * @var array<string, true> Key is function name, value irrelevant.
	 */
	protected $target_functions = array(
		'register_post_type' => true,
	);

	/**
	 * Array of reserved post type names which can not be used by themes and plugins.
	 *
	 * Source: {@link https://developer.wordpress.org/reference/functions/register_post_type/#reserved-post-types}
	 *
	 * {@internal To be updated after every major release. Last updated for WordPress 6.5-RC3.}
	 *
	 * @since 2.2.0
	 *
	 * @var array<string, true> Key is reserved post type name, value irrelevant.
	 */
	protected $reserved_names = array(
		'action'              => true, // Not a WP post type, but prevents other problems.
		'attachment'          => true,
		'author'              => true, // Not a WP post type, but prevents other problems.
		'custom_css'          => true,
		'customize_changeset' => true,
		'nav_menu_item'       => true,
		'oembed_cache'        => true,
		'order'               => true, // Not a WP post type, but prevents other problems.
		'page'                => true,
		'post'                => true,
		'revision'            => true,
		'theme'               => true, // Not a WP post type, but prevents other problems.
		'user_request'        => true,
		'wp_block'            => true,
		'wp_font_face'        => true,
		'wp_font_family'      => true,
		'wp_global_styles'    => true,
		'wp_navigation'       => true,
		'wp_template'         => true,
		'wp_template_part'    => true,
	);

	/**
	 * All valid tokens for in the first parameter of register_post_type().
	 *
	 * Set in `register()`.
	 *
	 * @since 2.2.0
	 *
	 * @var array<int|string, int|string>
	 */
	private $valid_tokens = array();

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @since 2.2.0
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
	 * Errors on invalid post type names when reserved keywords are used,
	 * the post type is too long, or contains invalid characters.
	 *
	 * @since 2.2.0
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
		$post_type_param = PassedParameters::getParameterFromStack( $parameters, 1, 'post_type' );
		if ( false === $post_type_param || '' === $post_type_param['clean'] ) {
			// Error for using empty slug.
			$this->phpcsFile->addError(
				'register_post_type() called without a post type slug. The slug must be a non-empty string.',
				false === $post_type_param ? $stackPtr : $post_type_param['start'],
				'Empty'
			);
			return;
		}

		$string_start = $this->phpcsFile->findNext( Collections::textStringStartTokens(), $post_type_param['start'], ( $post_type_param['end'] + 1 ) );
		$string_pos   = $this->phpcsFile->findNext( Tokens::$textStringTokens, $post_type_param['start'], ( $post_type_param['end'] + 1 ) );

		$has_invalid_tokens = $this->phpcsFile->findNext( $this->valid_tokens, $post_type_param['start'], ( $post_type_param['end'] + 1 ), true );
		if ( false !== $has_invalid_tokens || false === $string_pos ) {
			// Check for non string based slug parameter (we cannot determine if this is valid).
			$this->phpcsFile->addWarning(
				'The post type slug is not a string literal. It is not possible to automatically determine the validity of this slug. Found: %s.',
				$stackPtr,
				'NotStringLiteral',
				array(
					$post_type_param['clean'],
				),
				3
			);
			return;
		}

		$post_type = TextStrings::getCompleteTextString( $this->phpcsFile, $string_start );
		if ( isset( Tokens::$heredocTokens[ $this->tokens[ $string_start ]['code'] ] ) ) {
			// Trim off potential indentation from PHP 7.3 flexible heredoc/nowdoc content.
			$post_type = ltrim( $post_type );
		}

		$data = array(
			$post_type,
		);

		// Warn for dynamic parts in the slug parameter.
		if ( 'T_DOUBLE_QUOTED_STRING' === $this->tokens[ $string_pos ]['type']
			|| ( 'T_HEREDOC' === $this->tokens[ $string_pos ]['type']
			&& strpos( $this->tokens[ $string_pos ]['content'], '$' ) !== false )
		) {
			$this->phpcsFile->addWarning(
				'The post type slug may, or may not, get too long with dynamic contents and could contain invalid characters. Found: "%s".',
				$string_pos,
				'PartiallyDynamic',
				$data
			);
			$post_type = TextStrings::stripEmbeds( $post_type );
		}

		if ( preg_match( self::VALID_POST_TYPE_CHARACTERS, $post_type ) === 0 ) {
			// Error for invalid characters.
			$this->phpcsFile->addError(
				'register_post_type() called with invalid post type "%s". Post type contains invalid characters. Only lowercase alphanumeric characters, dashes, and underscores are allowed.',
				$string_pos,
				'InvalidCharacters',
				$data
			);
		}

		if ( isset( $this->reserved_names[ $post_type ] ) ) {
			// Error for using reserved slug names.
			$this->phpcsFile->addError(
				'register_post_type() called with reserved post type "%s". Reserved post types should not be used as they interfere with the functioning of WordPress itself.',
				$string_pos,
				'Reserved',
				$data
			);
		} elseif ( stripos( $post_type, 'wp_' ) === 0 ) {
			// Error for using reserved slug prefix.
			$this->phpcsFile->addError(
				'The post type passed to register_post_type() uses a prefix reserved for WordPress itself. Found: "%s".',
				$string_pos,
				'ReservedPrefix',
				$data
			);
		}

		// Error for slugs that are too long.
		if ( strlen( $post_type ) > self::POST_TYPE_MAX_LENGTH ) {
			$this->phpcsFile->addError(
				'A post type slug must not exceed %d characters. Found: "%s" (%d characters).',
				$string_pos,
				'TooLong',
				array(
					self::POST_TYPE_MAX_LENGTH,
					$post_type,
					strlen( $post_type ),
				)
			);
		}
	}
}
