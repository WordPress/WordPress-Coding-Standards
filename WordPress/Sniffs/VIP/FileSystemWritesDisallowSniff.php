<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

if ( ! class_exists( 'Generic_Sniffs_PHP_ForbiddenFunctionsSniff', true ) ) {
	throw new PHP_CodeSniffer_Exception( 'Class Generic_Sniffs_PHP_ForbiddenFunctionsSniff not found' );
}

/**
 * Disallow Filesystem writes.
 *
 * @link    https://vip.wordpress.com/documentation/vip/code-review-what-we-look-for/#filesystem-writes
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.3.0
 */
class WordPress_Sniffs_VIP_FileSystemWritesDisallowSniff extends Generic_Sniffs_PHP_ForbiddenFunctionsSniff {

	/**
	 * A list of forbidden functions with their alternatives.
	 *
	 * The value is NULL if no alternative exists. IE, the
	 * function should just not be used.
	 *
	 * @var array(string => string|null)
	 */
	public $forbiddenFunctions = array(
		'file_put_contents' => null,
		'fwrite'            => null,
		'fputcsv'           => null,
		'fputs'             => null,
		'ftruncate'         => null,
		'link'              => null,
		'symlink'           => null,
		'mkdir'             => null,
		'rename'            => null,
		'rmdir'             => null,
		'tempnam'           => null,
		'touch'             => null,
		'unlink'            => null,
		'is_writable'       => null,
		'is_writeable'      => null,
		'lchgrp'            => null,
		'lchown'            => null,
		'fputcsv'           => null,
		'delete'            => null,
		'chmod'             => null,
		'chown'             => null,
		'chgrp'             => null,
		'chmod'             => null,
		'chmod'             => null,
		'flock'             => null,
	);

	/**
	 * If true, an error will be thrown; otherwise a warning.
	 *
	 * @var bool
	 */
	public $error = true;

	/**
	 * Generates the error or warning for this sniff.
	 *
	 * Overloads parent addError method.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
	 * @param int                  $stackPtr  The position of the forbidden function
	 *                                        in the token array.
	 * @param string               $function  The name of the forbidden function.
	 * @param string               $pattern   The pattern used for the match.
	 *
	 * @return void
	 */
	protected function addError( $phpcsFile, $stackPtr, $function, $pattern = null ) {
		$data  = array( $function );
		$error = 'Filesystem writes are forbidden, you should not be using %s()';

		if ( true === $this->error ) {
			$phpcsFile->addError( $error, $stackPtr, 'FileWriteDetected', $data );
		} else {
			$phpcsFile->addWarning( $error, $stackPtr, 'FileWriteDetected', $data );
		}

	}

} // End class.
