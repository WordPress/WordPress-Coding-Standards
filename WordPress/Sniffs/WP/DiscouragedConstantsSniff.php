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
use PHPCSUtils\Utils\MessageHelper;
use PHPCSUtils\Utils\PassedParameters;
use PHPCSUtils\Utils\TextStrings;
use WordPressCS\WordPress\AbstractFunctionParameterSniff;
use WordPressCS\WordPress\Helpers\ConstantsHelper;

/**
 * Warns against usage of discouraged WP CONSTANTS and recommends alternatives.
 *
 * @since 0.14.0
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
	 * @var array<string, array<string, int|string>> Function name as key, array with target
	 *                                               parameter and name as value.
	 */
	protected $target_functions = array(
		'define' => array(
			'position' => 1,
			'name'     => 'constant_name',
		),
	);

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

		if ( ConstantsHelper::is_use_of_global_constant( $this->phpcsFile, $stackPtr ) === false ) {
			return;
		}

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
	 * @param string $matched_content The token content (function name) which was matched
	 *                                in lowercase.
	 * @param array  $parameters      Array with information about the parameters.
	 *
	 * @return void
	 */
	public function process_parameters( $stackPtr, $group_name, $matched_content, $parameters ) {
		$target_param = $this->target_functions[ $matched_content ];

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
