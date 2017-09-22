<?php
/*
 * Incorrect order and blank lines.
 */

namespace A\B\C;

use Vendor\Package\{ClassA as A, ClassB, ClassC as C};
use function Vendor\Package\{functionA, functionB, functionC};

use Vendor\Package\Name\ClassD as D;
use const Vendor\Package\{CONSTANT_A, CONSTANT_B, CONSTANT_C};

use Vendor\Package\AnotherNamespace\ClassE as E;
use const Another\Vendor\CONSTANT_D;
use function Another\Vendor\functionName as D;
// Not a blank line.
