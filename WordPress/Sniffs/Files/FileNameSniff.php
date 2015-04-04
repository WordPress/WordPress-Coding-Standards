<?php

/**
 * WordPress Coding Standard
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   John Godley
 * @link     http://codex.wordpress.org/WordPress_Coding_Standards
 */

/**
 * WordPress_Sniffs_Files_FileNameSniff.
 *
 * Ensures filenames do not contain underscores
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   John Godley
 */
class WordPress_Sniffs_Files_FileNameSniff implements PHP_CodeSniffer_Sniff
{

	/**
	 * A list of files that have already been processed.
	 *
	 * @since 0.4.0
	 *
	 * @var array
	 */
	protected $processed_files;

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array( T_OPEN_TAG );

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {

        $fileName = basename($phpcsFile->getFileName());

		if ( isset( $this->processed_files[ $fileName ] ) ) {
			return;
		}

		$this->processed_files[ $fileName ] = true;

        if (strpos($fileName, '_') !== false) {
                $expected = str_replace('_', '-', $fileName);
                $error    = 'Filename "'.$fileName.'" with underscores found; use '.$expected.' instead';
                $phpcsFile->addError($error, $stackPtr, 'UnderscoresNotAllowed');
        }

    }//end process()


}//end class

?>
