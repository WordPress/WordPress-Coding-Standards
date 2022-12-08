<?php

preg_quote($keywords, '/'); // OK.
preg_quote( $keywords, '`' ); // OK.

preg_quote($keywords); // Warning.
$textbody = preg_replace ( "/" . preg_quote($word) . "/", // Warning
                          "<i>" . $word . "</i>",
                          $textbody );

// Safeguard support for PHP 8.0+ named parameters.
preg_quote(delimiter: '#', str: $keywords); // OK.
preg_quote(str: $keywords); // Warning.
preg_quote(str: $keywords, delimitter: '#'); // Warning (typo in param name).
preg_quote(delimiter: '#'); // OK. Invalid function call, but that's not the concern of this sniff.
