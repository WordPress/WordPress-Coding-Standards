<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Sniffs\XSS;

use WordPress\Sniff;
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * Verifies that all outputted strings are escaped.
 *
 * @link    http://codex.wordpress.org/Data_Validation Data Validation on WordPress Codex
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   2013-06-11
 * @since   0.4.0  This class now extends WordPress_Sniff.
 * @since   0.5.0  The various function list properties which used to be contained in this class
 *                 have been moved to the WordPress_Sniff parent class.
 * @since   0.12.0 This sniff will now also check for output escaping when using shorthand
 *                 echo tags `<?=`.
 * @since   0.13.0 Class name changed: this class is now namespaced.
 *
 * @deprecated 1.0.0  This sniff has been moved to the `Security` category.
 *                    This file remains for now to prevent BC breaks.
 */
class EscapeOutputSniff extends \WordPress\Sniffs\Security\EscapeOutputSniff {

	/**
	 * Keep track of whether the warnings have been thrown to prevent
	 * the messages being thrown for every token triggering the sniff.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $thrown = array(
		'DeprecatedSniff'                 => false,
		'FoundPropertyForDeprecatedSniff' => false,
	);

	/**
	 * Don't use.
	 *
	 * @deprecated 1.0.0
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return void|int
	 */
	public function process_token( $stackPtr ) {
		if ( false === $this->thrown['DeprecatedSniff'] ) {
			$this->thrown['DeprecatedSniff'] = $this->phpcsFile->addWarning(
				'The "WordPress.XSS.EscapeOutput" sniff has been renamed to "WordPress.Security.EscapeOutput". Please update your custom ruleset.',
				0,
				'DeprecatedSniff'
			);
		}

		if ( false === $this->thrown['FoundPropertyForDeprecatedSniff']
			&& ( ( array() !== $this->customEscapingFunctions && $this->customEscapingFunctions !== $this->addedCustomFunctions['escape'] )
			|| ( array() !== $this->customSanitizingFunctions && $this->customSanitizingFunctions !== $this->addedCustomFunctions['sanitize'] )
			|| ( array() !== $this->customAutoEscapedFunctions && $this->customAutoEscapedFunctions !== $this->addedCustomFunctions['autoescape'] )
			|| ( array() !== $this->customPrintingFunctions && $this->customPrintingFunctions !== $this->addedCustomFunctions['print'] ) )
		) {
			$this->thrown['FoundPropertyForDeprecatedSniff'] = $this->phpcsFile->addWarning(
				'The "WordPress.XSS.EscapeOutput" sniff has been renamed to "WordPress.Security.EscapeOutput". Please update your custom ruleset.',
				0,
				'FoundPropertyForDeprecatedSniff'
			);
		}

		return parent::process_token( $stackPtr );
	}

}
