<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\WP;

use PHPCSUtils\Utils\MessageHelper;
use WordPressCS\WordPress\AbstractClassRestrictionsSniff;
use WordPressCS\WordPress\Helpers\MinimumWPVersionTrait;

/**
 * Restricts the use of deprecated WordPress classes and suggests alternatives.
 *
 * This sniff will throw an error when usage of a deprecated class is detected
 * if the class was deprecated before the minimum supported WP version;
 * a warning otherwise.
 * By default, it is set to presume that a project will support the current
 * WP version and up to three releases before.
 *
 * @since 0.12.0
 * @since 0.13.0 Class name changed: this class is now namespaced.
 * @since 0.14.0 Now has the ability to handle minimum supported WP version
 *               being provided via the command-line or as as <config> value
 *               in a custom ruleset.
 *
 * @uses \WordPressCS\WordPress\Helpers\MinimumWPVersionTrait::$minimum_wp_version
 */
final class DeprecatedClassesSniff extends AbstractClassRestrictionsSniff {

	use MinimumWPVersionTrait;

	/**
	 * List of deprecated classes with alternative when available.
	 *
	 * To be updated after every major release.
	 *
	 * Version numbers should be fully qualified.
	 *
	 * Last update: July 2023 for WP 6.3 at https://github.com/WordPress/wordpress-develop/commit/6281ce432c50345a57768bf53854d9b65b6cdd52
	 *
	 * @var array
	 */
	private $deprecated_classes = array(

		// WP 3.1.0.
		'WP_User_Search' => array(
			'alt'     => 'WP_User_Query',
			'version' => '3.1.0',
		),

		// WP 3.7.0.
		'WP_HTTP_Fsockopen' => array(
			'alt'     => 'WP_HTTP::request()',
			'version' => '3.7.0',
		),

		// WP 4.9.0.
		'WP_Customize_New_Menu_Section' => array(
			'version' => '4.9.0',
		),
		'WP_Customize_New_Menu_Control' => array(
			'version' => '4.9.0',
		),

		// WP 5.3.0.
		'WP_Privacy_Data_Export_Requests_Table' => array(
			'alt'     => 'WP_Privacy_Data_Export_Requests_List_Table',
			'version' => '5.3.0',
		),
		'WP_Privacy_Data_Removal_Requests_Table' => array(
			'alt'     => 'WP_Privacy_Data_Removal_Requests_List_Table',
			'version' => '5.3.0',
		),
		'Services_JSON' => array(
			'alt'     => 'The PHP native JSON extension',
			'version' => '5.3.0',
		),
		'Services_JSON_Error' => array(
			'alt'     => 'The PHP native JSON extension',
			'version' => '5.3.0',
		),
	);

	/**
	 * Groups of classes to restrict.
	 *
	 * @return array
	 */
	public function getGroups() {
		// Make sure all array keys are lowercase.
		$this->deprecated_classes = array_change_key_case( $this->deprecated_classes, \CASE_LOWER );

		return array(
			'deprecated_classes' => array(
				'classes' => array_keys( $this->deprecated_classes ),
			),
		);
	}

	/**
	 * Process a matched token.
	 *
	 * @param int    $stackPtr        The position of the current token in the stack.
	 * @param string $group_name      The name of the group which was matched. Will
	 *                                always be 'deprecated_classes'.
	 * @param string $matched_content The token content (class name) which was matched
	 *                                in its original case.
	 *
	 * @return void
	 */
	public function process_matched_token( $stackPtr, $group_name, $matched_content ) {

		$this->set_minimum_wp_version();

		$class_name = ltrim( strtolower( $matched_content ), '\\' );

		$message = 'The %s class has been deprecated since WordPress version %s.';
		$data    = array(
			ltrim( $matched_content, '\\' ),
			$this->deprecated_classes[ $class_name ]['version'],
		);

		if ( ! empty( $this->deprecated_classes[ $class_name ]['alt'] ) ) {
			$message .= ' Use %s instead.';
			$data[]   = $this->deprecated_classes[ $class_name ]['alt'];
		}

		MessageHelper::addMessage(
			$this->phpcsFile,
			$message,
			$stackPtr,
			( $this->wp_version_compare( $this->deprecated_classes[ $class_name ]['version'], $this->minimum_wp_version, '<' ) ),
			MessageHelper::stringToErrorcode( $class_name . 'Found' ),
			$data
		);
	}
}
