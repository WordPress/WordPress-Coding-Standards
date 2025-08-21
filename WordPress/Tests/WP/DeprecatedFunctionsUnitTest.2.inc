<?php

/*
 * Safeguard correct handling of all types of namespaced function calls.
 */
\the_category_ID();
MyNamespace\the_category_ID();
\MyNamespace\the_category_ID();
namespace\the_category_ID(); // The sniff should start flagging this once it can resolve relative namespaces.
