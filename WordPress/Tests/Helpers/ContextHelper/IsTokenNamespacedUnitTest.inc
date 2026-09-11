<?php

/*
 * The below should NOT be recognized as namespaced tokens.
 */

/* testUnqualified */ my_function();
/* testFullyQualified */ $const = \MY_CONSTANT;

/*
 * The below should be recognized as namespaced tokens.
 */

/* testPartiallyQualified */ MyNamespace\my_function();
/* testFullyQualifiedNamespaced */ $var = new \MyNamespace\MyClass();
/* testNamespaceRelative */ $const = namespace\MY_CONSTANT; // This is a false positive. The method should return false for this once it can resolve relative namespaces.
/* testNamespaceRelativeSub */ namespace\Sub\my_function();
