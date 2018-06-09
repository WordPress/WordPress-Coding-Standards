<?php

preg_quote($keywords, '/'); // OK.
preg_quote( $keywords, '`' ); // OK.

preg_quote($keywords); // Warning.
$textbody = preg_replace ( "/" . preg_quote($word) . "/", // Warning
                          "<i>" . $word . "</i>",
                          $textbody );
