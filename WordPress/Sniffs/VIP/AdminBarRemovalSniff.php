<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Discourages removal of the admin bar.
 *
 * @link    https://vip.wordpress.com/documentation/vip/code-review-what-we-look-for/#removing-the-admin-bar
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.3.0
 * @since   0.11.0 - Extends the WordPress_AbstractFunctionParameterSniff class.
 *                 - Added the $remove_only property.
 */
class WordPress_Sniffs_VIP_AdminBarRemovalSniff extends WordPress_AbstractFunctionParameterSniff {

	/**
	 * Whether or not the sniff only checks for removal of the admin bar
	 * or any manipulation to the visibility of the admin bar.
	 *
	 * Defaults to true: only check for removal of the admin bar.
	 * Set to false to check for any form of manipulation of the visibility
	 * of the admin bar.
	 *
	 * @since 0.11.0
	 *
	 * @var bool
	 */
	public $remove_only = true;

	/**
	 * Functions this sniff is looking for.
	 *
	 * @since 0.11.0
	 *
	 * @var array
	 */
	protected $target_functions = array(
		'show_admin_bar' => true,
		'add_filter'     => true,
	);
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		// Set up all string targets.
		$targets                  = PHP_CodeSniffer_Tokens::$stringTokens;

		// Add function call targets.
		$parent = parent::register();
		if ( ! empty( $parent ) ) {
			$targets[] = T_STRING;
		}

		return $targets;
	}


	/**
	 * Process the parameters of a matched function.
	 *
	 * @since 0.11.0
	 *
	 * @param int    $stackPtr        The position of the current token in the stack.
	 * @param array  $group_name      The name of the group which was matched.
	 * @param string $matched_content The token content (function name) which was matched.
	 * @param array  $parameters      Array with information about the parameters.
	 *
	 * @return void
	 */
	public function process_parameters( $stackPtr, $group_name, $matched_content, $parameters ) {
		$error = false;
		switch ( $matched_content ) {
			case 'show_admin_bar':
				$error = true;
				if ( true === $this->remove_only ) {
					if ( 'true' === $parameters[1]['raw'] ) {
						$error = false;
					}
				}
				break;

			case 'add_filter':
				$filter_name = $this->strip_quotes( $parameters[1]['raw'] );
				if ( 'show_admin_bar' !== $filter_name ) {
					break;
				}

				$error = true;
				if ( true === $this->remove_only && isset( $parameters[2]['raw'] ) ) {
					if ( '__return_true' === $this->strip_quotes( $parameters[2]['raw'] ) ) {
						$error = false;
					}
				}
				break;

			default:
				// Left empty on purpose.
				break;
		}

		if ( true === $error ) {
			$this->phpcsFile->addError( 'Removal of admin bar is prohibited.', $stackPtr, 'RemovalDetected' );
		}
	}
} // End class.
