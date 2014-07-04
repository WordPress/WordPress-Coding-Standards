<?php

echo "Foo bar"; // Bad, use double quote but not evaluating anything
echo '<a href="/static/link" title="Yeah yeah!">Link name</a>'; // Good
echo "<a href='". esc_url( $link ) ."' title='". esc_attr( $linktitle ) ."'>" . esc_html( $linkname ) . '</a>'; // Good
