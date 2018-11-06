<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Sniffs\VIP;

use WordPress\AbstractFunctionRestrictionsSniff;

/**
 * Disallow Filesystem writes.
 *
 * @link https://vip.wordpress.com/documentation/vip-go/code-review-blockers-warnings-notices/#filesystem-operations
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.3.0
 * @since   0.11.0 Extends the WordPress_AbstractFunctionRestrictionsSniff instead of the
 *                 Generic_Sniffs_PHP_ForbiddenFunctionsSniff.
 * @since   0.13.0 Class name changed: this class is now namespaced.
 *
 * @deprecated 1.0.0  This sniff has been deprecated.
 *                    This file remains for now to prevent BC breaks.
 */
class FileSystemWritesDisallowSniff extends AbstractFunctionRestrictionsSniff {

	/**
	 * If true, an error will be thrown; otherwise a warning.
	 *
	 * @var bool
	 */
	public $error = true;

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
	 * Groups of functions to restrict.
	 *
	 * Example: groups => array(
	 *  'lambda' => array(
	 *      'type'      => 'error' | 'warning',
	 *      'message'   => 'Use anonymous functions instead please!',
	 *      'functions' => array( 'file_get_contents', 'create_function' ),
	 *  )
	 * )
	 *
	 * @return array
	 */
	public function getGroups() {
		$groups = array(
			'file_ops' => array(
				'type'      => 'error',
				'message'   => 'Filesystem writes are forbidden, you should not be using %s()',
				'functions' => array(
					'delete',
					'file_put_contents',
					'flock',
					'fputcsv',
					'fputs',
					'fwrite',
					'ftruncate',
					'is_writable',
					'is_writeable',
					'link',
					'rename',
					'symlink',
					'tempnam',
					'touch',
					'unlink',
				),
			),
			'directory' => array(
				'type'      => 'error',
				'message'   => 'Filesystem writes are forbidden, you should not be using %s()',
				'functions' => array(
					'mkdir',
					'rmdir',
				),
			),
			'chmod' => array(
				'type'      => 'error',
				'message'   => 'Filesystem writes are forbidden, you should not be using %s()',
				'functions' => array(
					'chgrp',
					'chown',
					'chmod',
					'lchgrp',
					'lchown',
				),
			),
		);

		/*
		 * Maintain old behaviour - allow for changing the error type from the ruleset
		 * using the `error` property.
		 */
		if ( false === $this->error ) {
			foreach ( $groups as $group_name => $details ) {
				$groups[ $group_name ]['type'] = 'warning';
			}
		}

		return $groups;
	}

	/**
	 * Process the token and handle the deprecation notices.
	 *
	 * @since 1.0.0 Added to allow for throwing the deprecation notices.
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return void|int
	 */
	public function process_token( $stackPtr ) {
		if ( false === $this->thrown['DeprecatedSniff'] ) {
			$this->thrown['DeprecatedSniff'] = $this->phpcsFile->addWarning(
				'The "WordPress.VIP.FileSystemWritesDisallow" sniff has been deprecated. Please update your custom ruleset.',
				0,
				'DeprecatedSniff'
			);
		}

		if ( ( ! empty( $this->exclude ) || true !== $this->error )
			&& false === $this->thrown['FoundPropertyForDeprecatedSniff']
		) {
			$this->thrown['FoundPropertyForDeprecatedSniff'] = $this->phpcsFile->addWarning(
				'The "WordPress.VIP.FileSystemWritesDisallow" sniff has been deprecated. Please update your custom ruleset.',
				0,
				'FoundPropertyForDeprecatedSniff'
			);
		}

		return parent::process_token( $stackPtr );
	}

}
