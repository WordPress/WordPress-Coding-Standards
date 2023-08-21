<?php

// Live coding/parse error (missing second "else"). The sniff should ignore this.
if ( ( $fop === 1 ) ? print ( $var ? 10 : 20 ) ) {}
