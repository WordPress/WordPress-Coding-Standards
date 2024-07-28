<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\WP;

use PHPCSUtils\Utils\PassedParameters;
use WordPressCS\WordPress\AbstractFunctionParameterSniff;

/**
 * Flags calls to get_(comment|post|site|term|user)_meta(), get_metadata(), get_metadata_default()
 * and get_metadata_raw() functions that include the $key/$meta_key parameter, but omit the $single
 * parameter. Omitting $single in this situation can result in unexpected return types and lead to
 * bugs.
 *
 * @link https://github.com/WordPress/WordPress-Coding-Standards/issues/2459
 *
 * @since 3.2.0
 */
final class GetMetaFunctionSingleParameterSniff extends AbstractFunctionParameterSniff {

	/**
	 * The group name for this group of functions.
	 *
	 * @since 3.2.0
	 *
	 * @var string
	 */
	protected $group_name = 'get_meta';

	/**
	 * List of functions this sniff should examine.
	 *
	 * @link https://developer.wordpress.org/reference/functions/get_comment_meta/
	 * @link https://developer.wordpress.org/reference/functions/get_metadata/
	 * @link https://developer.wordpress.org/reference/functions/get_metadata_default/
	 * @link https://developer.wordpress.org/reference/functions/get_metadata_raw/
	 * @link https://developer.wordpress.org/reference/functions/get_post_meta/
	 * @link https://developer.wordpress.org/reference/functions/get_site_meta/
	 * @link https://developer.wordpress.org/reference/functions/get_term_meta/
	 * @link https://developer.wordpress.org/reference/functions/get_user_meta/
	 *
	 * @since 3.2.0
	 *
	 * @var array<string, array> Key is function name, value is an array containing information
	 *                           about the name and position of the relevant parameters.
	 */
	protected $target_functions = array(
		'get_comment_meta'     => array(
			'condition'   => array(
				'parameter' => 'key',
				'position'  => 2,
			),
			'recommended' => array(
				'parameter' => 'single',
				'position'  => 3,
			),
		),
		'get_metadata'         => array(
			'condition'   => array(
				'parameter' => 'meta_key',
				'position'  => 3,
			),
			'recommended' => array(
				'parameter' => 'single',
				'position'  => 4,
			),
		),
		'get_metadata_default' => array(
			'condition'   => array(
				'parameter' => 'meta_key',
				'position'  => 3,
			),
			'recommended' => array(
				'parameter' => 'single',
				'position'  => 4,
			),
		),
		'get_metadata_raw'     => array(
			'condition'   => array(
				'parameter' => 'meta_key',
				'position'  => 3,
			),
			'recommended' => array(
				'parameter' => 'single',
				'position'  => 4,
			),
		),
		'get_post_meta'        => array(
			'condition'   => array(
				'parameter' => 'key',
				'position'  => 2,
			),
			'recommended' => array(
				'parameter' => 'single',
				'position'  => 3,
			),
		),
		'get_site_meta'        => array(
			'condition'   => array(
				'parameter' => 'key',
				'position'  => 2,
			),
			'recommended' => array(
				'parameter' => 'single',
				'position'  => 3,
			),
		),
		'get_term_meta'        => array(
			'condition'   => array(
				'parameter' => 'key',
				'position'  => 2,
			),
			'recommended' => array(
				'parameter' => 'single',
				'position'  => 3,
			),
		),
		'get_user_meta'        => array(
			'condition'   => array(
				'parameter' => 'key',
				'position'  => 2,
			),
			'recommended' => array(
				'parameter' => 'single',
				'position'  => 3,
			),
		),
	);

	/**
	 * Process the parameters of a matched function.
	 *
	 * @since 3.2.0
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
		$condition   = $this->target_functions[ $matched_content ]['condition'];
		$recommended = $this->target_functions[ $matched_content ]['recommended'];

		$meta_key = PassedParameters::getParameterFromStack( $parameters, $condition['position'], $condition['parameter'] );
		if ( false === $meta_key ) {
			return;
		}

		$single = PassedParameters::getParameterFromStack( $parameters, $recommended['position'], $recommended['parameter'] );
		if ( is_array( $single ) ) {
			return;
		}

		$tokens = $this->phpcsFile->getTokens();

		$this->phpcsFile->addWarning(
			'When passing the $%s parameter to %s(), the $%s parameter must also be passed to make it explicit whether an array or a string is expected.',
			$stackPtr,
			'ReturnTypeNotExplicit',
			array( $condition['parameter'], $tokens[ $stackPtr ]['content'], $recommended['parameter'] )
		);
	}
}
