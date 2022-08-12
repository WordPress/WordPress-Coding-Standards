<?php

/*
 * This test file only safeguards the bit where the sniff may behave differently from the upstream sniff.
 */

echo ObjectName::$prop;
echo ObjectName::functionCall();
echo ObjectName::class;

echo ObjectName  ::   $prop; // Error x 2.
echo ObjectName ::  functionCall(); // Error x 2.
echo ObjectName   :: class; // Error x 2.

echo ObjectName
	::
	$prop; // Error x 2.
echo ObjectName
	::
	functionCall(); // Error x 2.
echo ObjectName
	::
	class; // Error x 2.

echo ObjectName // Comment
	::
	/* comment */
	class; // Error x 2.

// phpcs:set WordPress.WhiteSpace.ObjectOperatorSpacing ignoreNewlines true

echo ObjectName::$prop;
echo ObjectName::functionCall();
echo ObjectName::class;

echo ObjectName  ::   $prop; // Error x 2.
echo ObjectName ::  functionCall(); // Error x 2.
echo ObjectName   :: class; // Error x 2.

echo ObjectName
	::
	$prop;
echo ObjectName
	::
	functionCall();
echo ObjectName
	::
	class; // Error x 2.

echo ObjectName // Comment
	::
	/* comment */
	class; // Error x 2.

// phpcs:set WordPress.WhiteSpace.ObjectOperatorSpacing ignoreNewlines false
