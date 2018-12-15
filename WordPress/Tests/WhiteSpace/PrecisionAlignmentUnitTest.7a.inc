phpcs:set WordPress.WhiteSpace.PrecisionAlignment ignoreAlignmentTokens[] T_COMMENT,T_FUNCTION

<?php
/**
 * Testing issue as reported upstream in
 * https://github.com/squizlabs/PHP_CodeSniffer/issues/2179#issuecomment-437634372.
 *
 * This is an issue concerning property handling across multiple files, so the
 * 7a, 7b and 7c test files together test (against) the issue.
 */

  // Bad, but not reported as token type is whitelisted.
	 function exampleFunctionD() {} // Bad, but not reported as token type is whitelisted.
	  function exampleFunctionE() {} // Bad, but not reported as token type is whitelisted.
	   function exampleFunctionF() {} // Bad, but not reported as token type is whitelisted.
