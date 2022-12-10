<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\NamingConventions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractVariableSniff as PHPCS_AbstractVariableSniff;
use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\Tokens\Collections;
use PHPCSUtils\Utils\Scopes;
use PHPCSUtils\Utils\TextStrings;
use PHPCSUtils\Utils\Variables;
use WordPressCS\WordPress\Helpers\RulesetPropertyHelper;
use WordPressCS\WordPress\Helpers\SnakeCaseHelper;

/**
 * Checks the naming of variables and member variables.
 *
 * @link    https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/#naming-conventions
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.9.0
 * @since   0.13.0 Class name changed: this class is now namespaced.
 * @since   2.0.0  Now offers name suggestions for variables in violation.
 *
 * Last synced with base class January 2022 at commit 4b49a952bf0e2c3863d0a113256bae0d7fe63d52.
 * @link    https://github.com/squizlabs/PHP_CodeSniffer/blob/master/src/Standards/Squiz/Sniffs/NamingConventions/ValidVariableNameSniff.php
 */
final class ValidVariableNameSniff extends PHPCS_AbstractVariableSniff {

	/**
	 * Mixed-case variables used by WordPress.
	 *
	 * @since 0.11.0
	 *
	 * @var array
	 */
	protected $wordpress_mixed_case_vars = array(
		'EZSQL_ERROR'       => true,
		'GETID3_ERRORARRAY' => true,
		'is_IE'             => true,
		'is_IIS'            => true,
		'is_macIE'          => true,
		'is_NS4'            => true,
		'is_winIE'          => true,
		'PHP_SELF'          => true,
		'post_ID'           => true,
		'tag_ID'            => true,
		'user_ID'           => true,
	);

	/**
	 * List of member variables that can have mixed case.
	 *
	 * @since 0.9.0
	 * @since 0.11.0 Changed from public to protected.
	 * @since 3.0.0  Renamed from `$whitelisted_mixed_case_member_var_names` to `$allowed_mixed_case_member_var_names`.
	 *
	 * @var array
	 */
	protected $allowed_mixed_case_member_var_names = array(
		'ID'                => true,
		'comment_ID'        => true,
		'comment_post_ID'   => true,
		'post_ID'           => true,
		'comment_author_IP' => true,
		'cat_ID'            => true,
	);

	/**
	 * Custom list of properties which can have mixed case.
	 *
	 * @since 0.11.0
	 * @since 3.0.0  Renamed from `$customPropertiesWhitelist` to `$allowed_custom_properties`.
	 *
	 * @var string|string[]
	 */
	public $allowed_custom_properties = array();

	/**
	 * Cache of previously added custom functions.
	 *
	 * Prevents having to do the same merges over and over again.
	 *
	 * @since 0.10.0
	 * @since 0.11.0 - Name changed from $addedCustomVariables.
	 *               - Changed the format from simple bool to array.
	 *
	 * @var array
	 */
	protected $addedCustomProperties = array(
		'properties' => null,
	);

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcs_file The file being scanned.
	 * @param int                         $stack_ptr  The position of the current token in the
	 *                                                stack passed in $tokens.
	 *
	 * @return void
	 */
	protected function processVariable( File $phpcs_file, $stack_ptr ) {
		$tokens = $phpcs_file->getTokens();

		// If it's a php reserved var, then its ok.
		if ( Variables::isPHPReservedVarName( $tokens[ $stack_ptr ]['content'] ) ) {
			return;
		}

		// Merge any custom variables with the defaults.
		$this->merge_allow_lists();

		$var_name = ltrim( $tokens[ $stack_ptr ]['content'], '$' );

		// Likewise if it is a mixed-case var used by WordPress core.
		if ( isset( $this->wordpress_mixed_case_vars[ $var_name ] ) ) {
			return;
		}

		$obj_operator = $phpcs_file->findNext( Tokens::$emptyTokens, ( $stack_ptr + 1 ), null, true );
		if ( \T_OBJECT_OPERATOR === $tokens[ $obj_operator ]['code']
			|| \T_NULLSAFE_OBJECT_OPERATOR === $tokens[ $obj_operator ]['code']
		) {
			// Check to see if we are using a variable from an object.
			$var = $phpcs_file->findNext( Tokens::$emptyTokens, ( $obj_operator + 1 ), null, true );
			if ( \T_STRING === $tokens[ $var ]['code'] ) {
				$bracket = $phpcs_file->findNext( Tokens::$emptyTokens, ( $var + 1 ), null, true );
				if ( \T_OPEN_PARENTHESIS !== $tokens[ $bracket ]['code'] ) {
					$obj_var_name = $tokens[ $var ]['content'];

					if ( isset( $this->allowed_mixed_case_member_var_names[ $obj_var_name ] ) ) {
						return;
					}

					$suggested_name = SnakeCaseHelper::get_suggestion( $obj_var_name );
					if ( $suggested_name !== $obj_var_name ) {
						$error = 'Object property "$%s" is not in valid snake_case format, try "$%s"';
						$data  = array(
							$obj_var_name,
							$suggested_name,
						);
						$phpcs_file->addError( $error, $var, 'UsedPropertyNotSnakeCase', $data );
					}
				}
			}
		}

		$in_class     = false;
		$obj_operator = $phpcs_file->findPrevious( Tokens::$emptyTokens, ( $stack_ptr - 1 ), null, true );
		if ( isset( Collections::objectOperators()[ $tokens[ $obj_operator ]['code'] ] ) ) {
			// The variable lives within a class, and is referenced like
			// this: MyClass::$_variable or $class->variable.
			$in_class = true;
		}

		$suggested_name = SnakeCaseHelper::get_suggestion( $var_name );
		if ( $suggested_name !== $var_name ) {
			if ( $in_class && ! isset( $this->allowed_mixed_case_member_var_names[ $var_name ] ) ) {
				$error      = 'Object property "$%s" is not in valid snake_case format, try "$%s"';
				$error_name = 'UsedPropertyNotSnakeCase';
			} elseif ( ! $in_class ) {
				$error      = 'Variable "$%s" is not in valid snake_case format, try "$%s"';
				$error_name = 'VariableNotSnakeCase';
			}

			if ( isset( $error, $error_name ) ) {
				$data = array(
					$var_name,
					$suggested_name,
				);
				$phpcs_file->addError( $error, $stack_ptr, $error_name, $data );
			}
		}
	}

	/**
	 * Processes class member variables.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcs_file The file being scanned.
	 * @param int                         $stack_ptr  The position of the current token in the
	 *                                                stack passed in $tokens.
	 *
	 * @return void
	 */
	protected function processMemberVar( File $phpcs_file, $stack_ptr ) {
		// Make sure this is actually an OO property and not an OO method parameter or illegal property declaration.
		if ( Scopes::isOOProperty( $phpcs_file, $stack_ptr ) === false ) {
			return;
		}

		// Merge any custom variables with the defaults.
		$this->merge_allow_lists();

		$tokens   = $phpcs_file->getTokens();
		$var_name = ltrim( $tokens[ $stack_ptr ]['content'], '$' );

		if ( isset( $this->allowed_mixed_case_member_var_names[ $var_name ] ) ) {
			return;
		}

		$suggested_name = SnakeCaseHelper::get_suggestion( $var_name );
		if ( $suggested_name !== $var_name ) {
			$error = 'Member variable "$%s" is not in valid snake_case format, try "$%s"';
			$data  = array(
				$var_name,
				$suggested_name,
			);
			$phpcs_file->addError( $error, $stack_ptr, 'PropertyNotSnakeCase', $data );
		}
	}

	/**
	 * Processes the variables found within a double quoted string.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcs_file The file being scanned.
	 * @param int                         $stack_ptr  The position of the double quoted
	 *                                                string.
	 *
	 * @return void
	 */
	protected function processVariableInString( File $phpcs_file, $stack_ptr ) {
		$tokens = $phpcs_file->getTokens();

		// There will always be embeds if the processVariableInString() was called.
		$embeds = TextStrings::getEmbeds( $tokens[ $stack_ptr ]['content'] );

		// Merge any custom variables with the defaults.
		$this->merge_allow_lists();

		foreach ( $embeds as $embed ) {
			// Grab any variables contained in the embed.
			if ( preg_match_all( '`\$(\{)?(?<name>[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*)(?(1)\})`', $embed, $matches ) === 0 ) {
				continue;
			}

			foreach ( $matches['name'] as $var_name ) {
				// If it's a php reserved var, then its ok.
				if ( Variables::isPHPReservedVarName( $var_name ) ) {
					continue;
				}

				// Likewise if it is a mixed-case var used by WordPress core.
				if ( isset( $this->wordpress_mixed_case_vars[ $var_name ] ) ) {
					continue;
				}

				$suggested_name = SnakeCaseHelper::get_suggestion( $var_name );
				if ( $suggested_name !== $var_name ) {
					$error = 'Variable "$%s" is not in valid snake_case format, try "$%s"';
					$data  = array(
						$var_name,
						$suggested_name,
					);
					$phpcs_file->addError( $error, $stack_ptr, 'InterpolatedVariableNotSnakeCase', $data );
				}
			}
		}
	}

	/**
	 * Merge a custom allow list provided via a custom ruleset with the predefined allow list,
	 * if we haven't already.
	 *
	 * @since 0.10.0
	 * @since 2.0.0  Removed unused $phpcs_file parameter.
	 * @since 3.0.0  Renamed from `mergeWhiteList()` to `merge_allow_lists()`.
	 *
	 * @return void
	 */
	protected function merge_allow_lists() {
		if ( $this->allowed_custom_properties !== $this->addedCustomProperties['properties'] ) {
			// Fix property potentially passed as comma-delimited string.
			$customProperties = RulesetPropertyHelper::merge_custom_array( $this->allowed_custom_properties, array(), false );

			$this->allowed_mixed_case_member_var_names = RulesetPropertyHelper::merge_custom_array(
				$customProperties,
				$this->allowed_mixed_case_member_var_names
			);

			$this->addedCustomProperties['properties'] = $this->allowed_custom_properties;
		}
	}
}
