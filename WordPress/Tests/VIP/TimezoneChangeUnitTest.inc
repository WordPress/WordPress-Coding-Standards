<?php

date_default_timezone_set( 'Foo/Bar' ); // Bad.

$date = new DateTime();
$date->setTimezone( new DateTimeZone( 'America/Toronto' ) ); // Yay!
