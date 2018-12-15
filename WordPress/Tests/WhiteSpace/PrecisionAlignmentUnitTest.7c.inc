<?php
/**
 * Testing issue as reported upstream in
 * https://github.com/squizlabs/PHP_CodeSniffer/issues/2179#issuecomment-437634372.
 *
 * This is an issue concerning property handling across multiple files, so the
 * 7a, 7b and 7c test files together test (against) the issue.
 *
 * This file should inherit the property settings from the 7a test case file.
 */

  // Bad, but not reported as token type is whitelisted.
	 function exampleFunctionD() {} // Bad, but not reported as token type is (still) whitelisted.
	  function exampleFunctionE() {} // Bad, but not reported as token type is (still) whitelisted.
	   function exampleFunctionF() {} // Bad, but not reported as token type is (still) whitelisted.

// phpcs:set WordPress.WhiteSpace.PrecisionAlignment ignoreAlignmentTokens[]
