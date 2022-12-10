<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\WP;

use WordPressCS\WordPress\AbstractFunctionParameterSniff;
use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\Tokens\Collections;
use PHPCSUtils\Utils\MessageHelper;
use PHPCSUtils\Utils\PassedParameters;
use PHPCSUtils\Utils\Scopes;
use PHPCSUtils\Utils\TextStrings;

/**
 * Warns against usage of discouraged WP CONSTANTS and recommends alternatives.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.14.0
 */
final class DiscouragedConstantsSniff extends AbstractFunctionParameterSniff {

	/**
	 * List of discouraged WP constants and their replacements.
	 *
	 * @since 0.14.0
	 *
	 * @var array
	 */
	protected $discouraged_constants = array(
		'STYLESHEETPATH'      => 'get_stylesheet_directory()',
		'TEMPLATEPATH'        => 'get_template_directory()',
		'PLUGINDIR'           => 'WP_PLUGIN_DIR',
		'MUPLUGINDIR'         => 'WPMU_PLUGIN_DIR',
		'HEADER_IMAGE'        => 'add_theme_support( \'custom-header\' )',
		'NO_HEADER_TEXT'      => 'add_theme_support( \'custom-header\' )',
		'HEADER_TEXTCOLOR'    => 'add_theme_support( \'custom-header\' )',
		'HEADER_IMAGE_WIDTH'  => 'add_theme_support( \'custom-header\' )',
		'HEADER_IMAGE_HEIGHT' => 'add_theme_support( \'custom-header\' )',
		'BACKGROUND_COLOR'    => 'add_theme_support( \'custom-background\' )',
		'BACKGROUND_IMAGE'    => 'add_theme_support( \'custom-background\' )',
	);

	/**
	 * Array of functions to check.
	 *
	 * @since 0.14.0
	 * @since 3.0.0  The format of the value has changed from an integer parameter
	 *               position to an array with the parameter position and name.
	 *
	 * @var array<string, <string, in|string>> Function name as key, array with target
	 *                                         parameter and name as value.
	 */
	protected $target_functions = array(
		'define' => array(
			'position' => 1,
			'name'     => 'constant_name',
		),
	);

	/**
	 * Array of tokens which if found preceding the $stackPtr indicate that a T_STRING is not a constant.
	 *
	 * Additional tokens are added from within the contructor.
	 *
	 * @var array
	 */
	private $preceding_tokens_to_ignore = array(
		\T_NAMESPACE       => true,
		\T_USE             => true,
		\T_EXTENDS         => true,
		\T_IMPLEMENTS      => true,
		\T_NEW             => true,
		\T_FUNCTION        => true,
		\T_INSTANCEOF      => true,
		\T_GOTO            => true,
	);

	/**
	 * Constructor to enrich a property.
	 */
	public function __construct() {
		$this->preceding_tokens_to_ignore += Tokens::$ooScopeTokens;
		$this->preceding_tokens_to_ignore += Collections::objectOperators();
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @since 0.14.0
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return int|void Integer stack pointer to skip forward or void to continue
	 *                  normal file processing.
	 */
	public function process_token( $stackPtr ) {
		if ( isset( $this->target_functions[ strtolower( $this->tokens[ $stackPtr ]['content'] ) ] ) ) {
			// Disallow excluding function groups for this sniff.
			$this->exclude = array();

			return parent::process_token( $stackPtr );

		} else {
			return $this->process_arbitrary_tstring( $stackPtr );
		}
	}

	/**
	 * Process an arbitrary T_STRING token to determine whether it is one of the target constants.
	 *
	 * @since 0.14.0
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return void
	 */
	public function process_arbitrary_tstring( $stackPtr ) {
		$content = $this->tokens[ $stackPtr ]['content'];

		if ( ! isset( $this->discouraged_constants[ $content ] ) ) {
			return;
		}

		$next = $this->phpcsFile->findNext( Tokens::$emptyTokens, ( $stackPtr + 1 ), null, true );
		if ( false !== $next && \T_OPEN_PARENTHESIS === $this->tokens[ $next ]['code'] ) {
			// Function call or declaration.
			return;
		}

		$prev = $this->phpcsFile->findPrevious( Tokens::$emptyTokens, ( $stackPtr - 1 ), null, true );
		if ( false !== $prev && isset( $this->preceding_tokens_to_ignore[ $this->tokens[ $prev ]['code'] ] ) ) {
			// Not the use of a constant.
			return;
		}

		if ( $this->is_token_namespaced( $stackPtr ) === true ) {
			// Namespaced constant of the same name.
			return;
		}

		if ( false !== $prev
			&& \T_CONST === $this->tokens[ $prev ]['code']
			&& true === Scopes::isOOConstant( $this->phpcsFile, $prev )
		) {
			// Class constant of the same name.
			return;
		}

		/*
		 * Deal with a number of variations of use statements.
		 */
		for ( $i = $stackPtr; $i > 0; $i-- ) {
			if ( $this->tokens[ $i ]['line'] !== $this->tokens[ $stackPtr ]['line'] ) {
				break;
			}
		}

		$first_on_line = $this->phpcsFile->findNext( Tokens::$emptyTokens, ( $i + 1 ), null, true );
		if ( false !== $first_on_line && \T_USE === $this->tokens[ $first_on_line ]['code'] ) {
			$next_on_line = $this->phpcsFile->findNext( Tokens::$emptyTokens, ( $first_on_line + 1 ), null, true );
			if ( false !== $next_on_line ) {
				if ( \T_STRING === $this->tokens[ $next_on_line ]['code']
					&& 'const' === $this->tokens[ $next_on_line ]['content']
				) {
					$has_ns_sep = $this->phpcsFile->findNext( \T_NS_SEPARATOR, ( $next_on_line + 1 ), $stackPtr );
					if ( false !== $has_ns_sep ) {
						// Namespaced const (group) use statement.
						return;
					}
				} else {
					// Not a const use statement.
					return;
				}
			}
		}

		// Ok, this is really one of the discouraged constants.
		$this->phpcsFile->addWarning(
			'Found usage of constant "%s". Use %s instead.',
			$stackPtr,
			MessageHelper::stringToErrorcode( $content . 'UsageFound' ),
			array(
				$content,
				$this->discouraged_constants[ $content ],
			)
		);
	}

	/**
	 * Process the parameters of a matched `define` function call.
	 *
	 * @since 0.14.0
	 *
	 * @param int    $stackPtr        The position of the current token in the stack.
	 * @param string $group_name      The name of the group which was matched.
	 * @param string $matched_content The token content (function name) which was matched.
	 * @param array  $parameters      Array with information about the parameters.
	 *
	 * @return void
	 */
	public function process_parameters( $stackPtr, $group_name, $matched_content, $parameters ) {
		$function_name = strtolower( $matched_content );
		$target_param  = $this->target_functions[ $function_name ];

		// Was the target parameter passed ?
		$found_param = PassedParameters::getParameterFromStack( $parameters, $target_param['position'], $target_param['name'] );
		if ( false === $found_param ) {
			return;
		}

		$clean_content = TextStrings::stripQuotes( $found_param['clean'] );

		if ( isset( $this->discouraged_constants[ $clean_content ] ) ) {
			$first_non_empty = $this->phpcsFile->findNext(
				Tokens::$emptyTokens,
				$found_param['start'],
				( $found_param['end'] + 1 ),
				true
			);

			$this->phpcsFile->addWarning(
				'Found declaration of constant "%s". Use %s instead.',
				$first_non_empty,
				MessageHelper::stringToErrorcode( $clean_content . 'DeclarationFound' ),
				array(
					$clean_content,
					$this->discouraged_constants[ $clean_content ],
				)
			);
		}
	}

}
