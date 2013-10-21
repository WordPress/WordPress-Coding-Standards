<?php
/**
 * Restricts usage of some functions
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Weston Ruter <weston@x-team.com>
 * @link     http://codex.wordpress.org/Data_Validation Data Validation on WordPress Codex
 */
class WordPress_Sniffs_VIP_FunctionRestrictionsSniff implements PHP_CodeSniffer_Sniff
{

	public $groups = array(
		'switch_to_blog' => array(
			'type'      => 'error',
			'message'   => '%s is not something you should ever need to do in a VIP theme context. Instead use an API (XML-RPC, REST) to interact with other sites if needed.',
			'functions' => array( 'switch_to_blog' ),
			),
		'lambda' => array(
			'type' => 'error',
			'message' => '%s is prohibited, please use Anonymous functions instead.',
			'functions' => array( 
				'eval', 
				'create_function',
				),
			),
		);

	/**
	 * Exclude groups
	 * @var string Comma-delimited group list, ie: switch_to_blog,user_meta
	 */
	public $exclude = '';

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register()
	{
		return array(
				T_STRING,
				T_EVAL,
			   );

	}//end register()


	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
	 * @param int                  $stackPtr  The position of the current token
	 *                                        in the stack passed in $tokens.
	 *
	 * @todo Allow T_CONSTANT_ENCAPSED_STRING?
	 *
	 * @return void
	 */
	public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr )
	{
		$tokens = $phpcsFile->getTokens();

		$token = $tokens[$stackPtr];

		$exclude = explode( ',', $this->exclude );


		foreach ( $this->groups as $groupName => $group ) {
			
			if ( in_array( $groupName, $exclude ) ) {
				continue;
			}

			if ( ! in_array( $token['content'], $group['functions'] ) ) {
				continue;
			}

			if ( $group['type'] == 'warning' ) {
				$addWhat = array( $phpcsFile, 'addWarning' );
			} else {
				$addWhat = array( $phpcsFile, 'addError' );
			}

			call_user_func(
				$addWhat,
				$group['message'], 
				$stackPtr, 
				$groupName, 
				array( $token['content'] )
				);

		}

	}//end process()


}//end class

?>
