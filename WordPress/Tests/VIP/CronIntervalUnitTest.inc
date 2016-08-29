<?php

function my_add_weekly( $schedules ) {
	$schedules['every_6_mins'] = array(
		'interval' => 360,
		'display' => __( 'Once every 6 minutes' )
	);
	return $schedules;
}
add_filter( 'cron_schedules', 'my_add_weekly');


class Foo {
	function __construct() {
		add_filter( 'cron_schedules', array( $this, 'my_add_quickly' ) );
	}

	function my_add_quickly( $schedules ) {
		$schedules['every_10_mins'] = array(
			'interval' => 10 * 60,
			'display' => __( 'Once every 10 minutes' )
		);
		return $schedules;
	}

	static function my_add_quicklier( $schedules ) {
		$schedules['every_5_mins'] = array(
			'interval' => 20 * 60 - 15 * 60, // Sneaky 5 minute interval.
			'display' => __( 'Once every 5 minutes' )
		);
		return $schedules;
	}
}

add_filter( 'cron_schedules', array( 'Foo', 'my_add_quicklier' ) );

add_filter( 'cron_schedules', array( $some_other_place, 'some_other_method' ) );

add_filter( 'cron_schedules', function ( $schedules ) {
	$schedules['every_9_mins'] = array(
		'interval' => 9 * 60,
		'display' => __( 'Once every 9 minutes' )
	);
	return $schedules;
} );
