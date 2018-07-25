<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Sniffs\NamingConventions;

use PHP_CodeSniffer_Standards_AbstractVariableSniff as PHPCS_AbstractVariableSniff;
use PHP_CodeSniffer_File as File;
use PHP_CodeSniffer_Tokens as Tokens;
use WordPress\Sniff;

/**
 * Checks the naming of variables and member variables.
 *
 * @link    https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/#naming-conventions
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.9.0
 * @since   0.13.0 Class name changed: this class is now namespaced.
 *
 * Last synced with base class June 2018 at commit 78ddbae97cac078f09928bf89e3ab9e53ad2ace0.
 * @link    https://github.com/squizlabs/PHP_CodeSniffer/blob/master/src/Standards/Squiz/Sniffs/NamingConventions/ValidVariableNameSniff.php
 * One change from upstream deferred till later (PHPCS 3.3.0+):
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1048#issuecomment-364282100
 */
class ValidVariableNameSniff extends PHPCS_AbstractVariableSniff {

	/**
	 * PHP Reserved Vars.
	 *
	 * @since 0.9.0
	 * @since 0.11.0 Changed visibility from public to protected.
	 *
	 * @var array
	 */
	protected $php_reserved_vars = array(
		'_SERVER'              => true,
		'_GET'                 => true,
		'_POST'                => true,
		'_REQUEST'             => true,
		'_SESSION'             => true,
		'_ENV'                 => true,
		'_COOKIE'              => true,
		'_FILES'               => true,
		'GLOBALS'              => true,
		'http_response_header' => true,
		'HTTP_RAW_POST_DATA'   => true,
		'php_errormsg'         => true,
	);

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
		'user_ID'           => true,
	);

	/**
	 * List of member variables that can have mixed case.
	 *
	 * @since 0.9.0
	 * @since 0.11.0 Changed from public to protected.
	 *
	 * @var array
	 */
	protected $whitelisted_mixed_case_member_var_names = array(
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
	 *
	 * @var string|string[]
	 */
	public $customPropertiesWhitelist = array();

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
		'variables'  => null,
	);

	/**
	 * Custom list of properties which can have mixed case.
	 *
	 * @since 0.10.0
	 * @deprecated 0.11.0 Use $customPropertiesWhitelist instead.
	 *
	 * @var string|string[]
	 */
	public $customVariablesWhitelist = array();

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

		$tokens   = $phpcs_file->getTokens();
		$var_name = ltrim( $tokens[ $stack_ptr ]['content'], '$' );

		// If it's a php reserved var, then its ok.
		if ( isset( $this->php_reserved_vars[ $var_name ] ) ) {
			return;
		}

		// Merge any custom variables with the defaults.
		$this->mergeWhiteList( $phpcs_file );

		// Likewise if it is a mixed-case var used by WordPress core.
		if ( isset( $this->wordpress_mixed_case_vars[ $var_name ] ) ) {
			return;
		}

		$obj_operator = $phpcs_file->findNext( Tokens::$emptyTokens, ( $stack_ptr + 1 ), null, true );
		if ( \T_OBJECT_OPERATOR === $tokens[ $obj_operator ]['code'] ) {
			// Check to see if we are using a variable from an object.
			$var = $phpcs_file->findNext( Tokens::$emptyTokens, ( $obj_operator + 1 ), null, true );
			if ( \T_STRING === $tokens[ $var ]['code'] ) {
				$bracket = $phpcs_file->findNext( Tokens::$emptyTokens, ( $var + 1 ), null, true );
				if ( \T_OPEN_PARENTHESIS !== $tokens[ $bracket ]['code'] ) {
					$obj_var_name = $tokens[ $var ]['content'];

					// There is no way for us to know if the var is public or
					// private, so we have to ignore a leading underscore if there is
					// one and just check the main part of the variable name.
					$original_var_name = $obj_var_name;
					if ( '_' === substr( $obj_var_name, 0, 1 ) ) {
						$obj_var_name = substr( $obj_var_name, 1 );
					}

					if ( ! isset( $this->whitelisted_mixed_case_member_var_names[ $obj_var_name ] ) && self::isSnakeCase( $obj_var_name ) === false ) {
						$error = 'Object property "%s" is not in valid snake_case format';
						$data  = array( $original_var_name );
						$phpcs_file->addError( $error, $var, 'NotSnakeCaseMemberVar', $data );
					}
				}
			}
		}

		$in_class     = false;
		$obj_operator = $phpcs_file->findPrevious( Tokens::$emptyTokens, ( $stack_ptr - 1 ), null, true );
		if ( \T_DOUBLE_COLON === $tokens[ $obj_operator ]['code'] || \T_OBJECT_OPERATOR === $tokens[ $obj_operator ]['code'] ) {
			// The variable lives within a class, and is referenced like
			// this: MyClass::$_variable or $class->variable.
			$in_class = true;
		}

		// There is no way for us to know if the var is public or private,
		// so we have to ignore a leading underscore if there is one and just
		// check the main part of the variable name.
		$original_var_name = $var_name;
		if ( '_' === substr( $var_name, 0, 1 ) && true === $in_class ) {
			$var_name = substr( $var_name, 1 );
		}

		if ( self::isSnakeCase( $var_name ) === false ) {
			if ( $in_class && ! isset( $this->whitelisted_mixed_case_member_var_names[ $var_name ] ) ) {
				$error      = 'Object property "%s" is not in valid snake_case format';
				$error_name = 'NotSnakeCaseMemberVar';
			} elseif ( ! $in_class ) {
				$error      = 'Variable "%s" is not in valid snake_case format';
				$error_name = 'NotSnakeCase';
			}

			if ( isset( $error, $error_name ) ) {
				$data = array( $original_var_name );
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

		$tokens = $phpcs_file->getTokens();

		$var_name     = ltrim( $tokens[ $stack_ptr ]['content'], '$' );
		$member_props = $phpcs_file->getMemberProperties( $stack_ptr );
		if ( empty( $member_props ) ) {
			// Couldn't get any info about this variable, which
			// generally means it is invalid or possibly has a parse
			// error. Any errors will be reported by the core, so
			// we can ignore it.
			return;
		}

		// Merge any custom variables with the defaults.
		$this->mergeWhiteList( $phpcs_file );

		$error_data = array( $var_name );
		if ( ! isset( $this->whitelisted_mixed_case_member_var_names[ $var_name ] ) && false === self::isSnakeCase( $var_name ) ) {
			$error = 'Member variable "%s" is not in valid snake_case format.';
			$phpcs_file->addError( $error, $stack_ptr, 'MemberNotSnakeCase', $error_data );
		}
	}

	/**
	 * Processes the variable found within a double quoted string.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcs_file The file being scanned.
	 * @param int                         $stack_ptr  The position of the double quoted
	 *                                                string.
	 *
	 * @return void
	 */
	protected function processVariableInString( File $phpcs_file, $stack_ptr ) {

		$tokens = $phpcs_file->getTokens();

		if ( preg_match_all( '|[^\\\]\${?([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)|', $tokens[ $stack_ptr ]['content'], $matches ) > 0 ) {

			// Merge any custom variables with the defaults.
			$this->mergeWhiteList( $phpcs_file );

			foreach ( $matches[1] as $var_name ) {
				// If it's a php reserved var, then its ok.
				if ( isset( $this->php_reserved_vars[ $var_name ] ) ) {
					continue;
				}

				// Likewise if it is a mixed-case var used by WordPress core.
				if ( isset( $this->wordpress_mixed_case_vars[ $var_name ] ) ) {
					return;
				}

				if ( false === self::isSnakeCase( $var_name ) ) {
					$error = 'Variable "%s" is not in valid snake_case format';
					$data  = array( $var_name );
					$phpcs_file->addError( $error, $stack_ptr, 'StringNotSnakeCase', $data );
				}
			}
		}
	}

	/**
	 * Return whether the variable is in snake_case.
	 *
	 * @param string $var_name Variable name.
	 * @return bool
	 */
	public static function isSnakeCase( $var_name ) {
		return (bool) preg_match( '/^[a-z0-9_]+$/', $var_name );
	}

	/**
	 * Merge a custom whitelist provided via a custom ruleset with the predefined whitelist,
	 * if we haven't already.
	 *
	 * @since 0.10.0
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcs_file The file being scanned.
	 *
	 * @return void
	 */
	protected function mergeWhiteList( File $phpcs_file ) {
		if ( $this->customPropertiesWhitelist !== $this->addedCustomProperties['properties']
			|| $this->customVariablesWhitelist !== $this->addedCustomProperties['variables']
		) {
			// Fix property potentially passed as comma-delimited string.
			$customProperties = Sniff::merge_custom_array( $this->customPropertiesWhitelist, array(), false );

			if ( ! empty( $this->customVariablesWhitelist ) ) {
				$customProperties = Sniff::merge_custom_array(
					$this->customVariablesWhitelist,
					$customProperties,
					false
				);

				$phpcs_file->addWarning(
					'The customVariablesWhitelist property is deprecated in favor of customPropertiesWhitelist.',
					0,
					'DeprecatedCustomVariablesWhitelist'
				);
			}

			$this->whitelisted_mixed_case_member_var_names = Sniff::merge_custom_array(
				$customProperties,
				$this->whitelisted_mixed_case_member_var_names
			);

			$this->addedCustomProperties['properties'] = $this->customPropertiesWhitelist;
			$this->addedCustomProperties['variables']  = $this->customVariablesWhitelist;
		}
	}

}
