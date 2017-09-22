<?php
/*
 * Deal with multiple namespaces - scoped and non-scoped in one file.
 */

namespace A\B\C {
	use Vendor\Package\{ClassA as A, ClassB, ClassC as C};
	use function Vendor\Package\{functionA, functionB, functionC};

	use Vendor\Package\Name\ClassD as D;
	use const Vendor\Package\{CONSTANT_A, CONSTANT_B, CONSTANT_C};

	use Vendor\Package\AnotherNamespace\ClassE as E;
	use const Another\Vendor\CONSTANT_D;
	use function Another\Vendor\functionName as D;
}

namespace D\E\F;

use D\Vendor\Package\{ClassA as A, ClassB, ClassC as C};
use function D\Vendor\Package\{functionA, functionB, functionC};

use D\Vendor\Package\Name\ClassD as D;
use const D\Vendor\Package\{CONSTANT_A, CONSTANT_B, CONSTANT_C};

use D\Vendor\Package\AnotherNamespace\ClassE as E;
use const D\Another\Vendor\CONSTANT_D;
use function D\Another\Vendor\functionName as D;


namespace G\H\I;

use G\Vendor\Package\{ClassA as A, ClassB, ClassC as C};
use function G\Vendor\Package\{functionA, functionB, functionC};

use G\Vendor\Package\Name\ClassD as D;
use const G\Vendor\Package\{CONSTANT_A, CONSTANT_B, CONSTANT_C};

use G\Vendor\Package\AnotherNamespace\ClassE as E;
use const G\Another\Vendor\CONSTANT_D;
use function G\Another\Vendor\functionName as D;

namespace {
	use GlobalNS\Vendor\Package\{ClassA as A, ClassB, ClassC as C};
	use function GlobalNS\Vendor\Package\{functionA, functionB, functionC};

	use GlobalNS\Vendor\Package\Name\ClassD as D;
	use const GlobalNS\Vendor\Package\{CONSTANT_A, CONSTANT_B, CONSTANT_C};

	use GlobalNS\Vendor\Package\AnotherNamespace\ClassE as E;
	use const GlobalNS\Another\Vendor\CONSTANT_D;
	use function GlobalNS\Another\Vendor\functionName as D;

}
