<?php
/*
 * Correct order and blank lines.
 */

namespace A\B\C;

use Vendor\Package\{ClassA as A, ClassB, ClassC as C};
use Vendor\Package\Name\ClassD as D;
use Vendor\Package\AnotherNamespace\ClassE as E;

use function Vendor\Package\{functionA, functionB, functionC};
use function Another\Vendor\functionName as D;

use const Vendor\Package\{CONSTANT_A, CONSTANT_B, CONSTANT_C};
use const Another\Vendor\CONSTANT_D;

/*
 * Make sure the sniff does not get confused with trait use statements.
 * and closure use statements.
 */
class MyHelloWorld {
	use \Hello, World;
	use \HelloWorld { sayHello as protected; }
	use HelloMoreWorld {
		sayHello as private myPrivateHello;
	}

	public function something() {
		$example = function ( $arg ) use ( $message ) {};
	}
}
