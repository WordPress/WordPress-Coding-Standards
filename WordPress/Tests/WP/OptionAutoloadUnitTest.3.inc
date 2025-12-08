<?php

/*
 * Intentional parse error (just a namespace separator as the third parameter).
 * This should be the only test in this file.
 */

update_option('my_option', 'new_value', \);
