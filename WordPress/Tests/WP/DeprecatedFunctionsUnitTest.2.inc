<?php

/*
 * Safeguard support for PHP 8.0+ tokenization of namespaced "names".
 */
\the_category_ID();
MyNamespace\the_category_ID();
\MyNamespace\the_category_ID();
namespace\the_category_ID(); // The sniff should start flagging this once it can resolve relative namespaces.
