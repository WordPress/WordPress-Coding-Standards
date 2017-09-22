<?php
/*
 * Incorrect order and no blank lines.
 */

namespace A\B\C;
use const Vendor\Package\{CONSTANT_A, CONSTANT_B, CONSTANT_C};
use function Vendor\Package\{functionA, functionB, functionC};
use Vendor\Package\{ClassA as A, ClassB, ClassC as C};
use Vendor\Package\Name\ClassD as D;
use const Another\Vendor\CONSTANT_D;
use function Another\Vendor\functionName as D;
use Vendor\Package\AnotherNamespace\ClassE as E;
