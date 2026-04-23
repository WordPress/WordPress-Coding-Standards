<?php

preg_quote($keywords, '/'); // OK.
preg_quote( $keywords, '`' ); // OK.

preg_quote($keywords); // Warning.
$textbody = preg_replace ( "/" . \PREG_quote($word) . "/", // Warning
                          "<i>" . $word . "</i>",
                          $textbody );

// Safeguard support for PHP 8.0+ named parameters.
preg_quote(delimiter: '#', str: $keywords); // OK.
preg_quote(str: $keywords); // Warning.
Preg_QUOTE(str: $keywords, delimitter: '#'); // Warning (typo in param name).
preg_quote(delimiter: '#'); // OK. Invalid function call, but that's not the concern of this sniff.

/*
 * Safeguard correct handling of all types of namespaced function calls.
 */
\preg_quote($keywords); // Warning.
MyNamespace\preg_quote($keywords); // Ok.
\MyNamespace\preg_quote($keywords); // Ok.
namespace\preg_quote($keywords); // The sniff should start flagging this once it can resolve relative namespaces.
namespace\Sub\preg_quote($keywords); // Ok.
