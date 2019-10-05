<?php

date_default_timezone_set( 'Foo/Bar' ); // Bad.

// phpcs:set WordPress.WP.TimezoneChange exclude[] timezone_change
date_default_timezone_set( 'Foo/Bar' ); // OK.

// phpcs:set WordPress.WP.TimezoneChange exclude[]
