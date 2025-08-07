<?php

/*
 * Intentional parse error (unclosed string).
 * This should be the only test in this file.
 */

wpdb::prepare( "SELECT * FROM $wpdb->posts
