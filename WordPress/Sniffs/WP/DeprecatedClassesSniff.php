<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Sniffs\WP;

use WordPress\AbstractClassRestrictionsSniff;

/**
 * Restricts the use of deprecated WordPress classes and suggests alternatives.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.12.0
 * @since   0.13.0 Class name changed: this class is now namespaced.
 */
class DeprecatedClassesSniff extends AbstractClassRestrictionsSniff {

	/**
	 * Minimum WordPress version.
	 *
	 * This sniff will throw an error when usage of a deprecated class is
	 * detected if the class was deprecated before the minimum supported
	 * WP version; a warning otherwise.
	 * By default, it is set to presume that a project will support the current
	 * WP version and up to three releases before.
	 * This variable allows changing the minimum supported WP version used by
	 * this sniff by setting a property in a custom phpcs.xml ruleset.
	 *
	 * Example usage:
	 * <rule ref="WordPress.WP.DeprecatedClasses">
	 *  <properties>
	 *   <property name="minimum_supported_version" value="4.3"/>
	 *  </properties>
	 * </rule>
	 *
	 * @var string WordPress versions.
	 */
	public $minimum_supported_version = '4.5';

	/**
	 * List of deprecated classes with alternative when available.
	 *
	 * To be updated after every major release.
	 *
	 * Version numbers should be fully qualified.
	 *
	 * @var array
	 */
	private $deprecated_classes = array(

		// WP 3.1.0.
		'WP_User_Search' => array(
			'alt'     => 'WP_User_Query',
			'version' => '3.1.0',
		),
	);


	/**
	 * Groups of classes to restrict.
	 *
	 * @return array
	 */
	public function getGroups() {
		// Make sure all array keys are lowercase.
		$keys = array_keys( $this->deprecated_classes );
		$keys = array_map( 'strtolower', $keys );
		$this->deprecated_classes = array_combine( $keys, $this->deprecated_classes );

		return array(
			'deprecated_classes' => array(
				'classes' => $keys,
			),
		);

	} // End getGroups().

	/**
	 * Process a matched token.
	 *
	 * @param int    $stackPtr        The position of the current token in the stack.
	 * @param array  $group_name      The name of the group which was matched. Will
	 *                                always be 'deprecated_functions'.
	 * @param string $matched_content The token content (class name) which was matched.
	 *
	 * @return void
	 */
	public function process_matched_token( $stackPtr, $group_name, $matched_content ) {
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

		$this->addMessage(
			$message,
			$stackPtr,
			( version_compare( $this->deprecated_classes[ $class_name ]['version'], $this->minimum_supported_version, '<' ) ),
			$this->string_to_errorcode( $matched_content . 'Found' ),
			$data
		);

	} // End process_matched_token().

} // End class.
