<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\PHP;

use PHPCSUtils\Utils\PassedParameters;
use WordPressCS\WordPress\AbstractFunctionParameterSniff;

/**
 * Flag calling preg_quote() without the second ($delimiter) parameter.
 *
 * @since 1.0.0
 */
final class PregQuoteDelimiterSniff extends AbstractFunctionParameterSniff {

	/**
	 * The group name for this group of functions.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $group_name = 'preg_quote';

	/**
	 * List of functions this sniff should examine.
	 *
	 * @link https://www.php.net/preg_quote
	 *
	 * @since 1.0.0
	 *
	 * @var array<string, true> Key is function name, value irrelevant.
	 */
	protected $target_functions = array(
		'preg_quote' => true,
	);

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

		$delimiter = PassedParameters::getParameterFromStack( $parameters, 2, 'delimiter' );
		if ( false !== $delimiter ) {
			return;
		}

		$this->phpcsFile->addWarning(
			'Passing the $delimiter parameter to preg_quote() is strongly recommended.',
			$stackPtr,
			'Missing'
		);
	}
}
