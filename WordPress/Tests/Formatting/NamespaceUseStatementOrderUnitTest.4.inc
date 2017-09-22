<?php
/*
 * Incorrect order with multi-line statements, weird whitespace, comments and
 * seriously bad code order.
 */

namespace A\B\C;

use function Vendor\Package\{
	// Some comment.
	functionA, functionB, functionC
	};
use Vendor\Package\AnotherNamespace\{ // Something.
	ClassE as E
	}; /* Else. */
/* Silly*/ use function Another\Vendor\functionName as D;

use const Vendor\Package\{
	/* Another comment. */
	CONSTANT_A,
	CONSTANT_B,
	CONSTANT_C
}; // Testing this.
use Vendor\Package\Name\ClassD
	as D; // More commenting.


class EF {
}

/**
 * Docblock test.
 */
use const Another\Vendor\CONSTANT_D;

function Something() {}

	/*
	 * Importing from package B.
	 */
	use Vendor\Package\{
		ClassA as A
		, ClassB,
		ClassC as C};

// Multiple use statements on the same line.
// Second comment line.
use const Vendor\Package\CONSTANT_E; use Vendor\Package\Something; /* comment */ use function Vendor\Package\functionD;
