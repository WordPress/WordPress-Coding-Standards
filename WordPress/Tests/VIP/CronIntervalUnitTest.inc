<?php

Foo::my_add_quicklier( $schedules ); // The old logic would get confused by this.

function my_add_weekly( $schedules ) {
	$schedules['every_6_mins'] = array(
		'interval' => 360,
		'display' => __( 'Once every 6 minutes' )
	);
	return $schedules;
}
add_filter( 'cron_schedules', 'my_add_weekly'); // Error: 6 min.


class Foo {
	function __construct() {
		add_filter( 'cron_schedules', array( $this, 'my_add_quickly' ) ); // Error: 10 min.
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

add_filter( 'cron_schedules', array( 'Foo', 'my_add_quicklier' ) ); // Error: 5 min.

add_filter( 'cron_schedules', array( $some_other_place, 'some_other_method' ) ); // Warning: time undetermined.

add_filter( 'cron_schedules', array( $some_other_place, $some_other_method ) ); // Warning: time undetermined.

add_filter( 'cron_schedules', function ( $schedules ) {
	$schedules['every_9_mins'] = array(
		'interval' => 9 * 60,
		'display' => __( 'Once every 9 minutes' )
	);
	return $schedules;
} ); // Error: 9 min.

add_filter( 'cron_schedules' ); // Ignore, no callback parameter.

add_filter( 'cron_schedules', [ 'Foo', 'my_add_quicklier' ] ); // Error: 5 min.

// Ignore, not our function. Currently gives false positive, will be fixed later when function call detection utility functions are added.
My_Custom::add_filter( 'cron_schedules', [ 'Foo', 'my_add_quicklier' ] );

// Deal correctly with the WP time constants.
add_filter( 'cron_schedules', function ( $schedules ) {
	$schedules['every_2_days_and_a_bit'] = [
		'interval' => 2 * DAY_IN_SECONDS + 2 * HOUR_IN_SECONDS,
		'display' => __( 'Once every 2 days and a bit' )
	];
	return $schedules;
} ); // Ok: > 15 min.

add_filter( 'cron_schedules', function ( $schedules ) {
	$schedules['every_8_minutes'] = [
		'interval' => 8 * MINUTE_IN_SECONDS,
		'display' => __( 'Once every 8 minutes' )
	];
	return $schedules;
} ); // Error: 8 min.

// Deal correctly with the function calls for interval.
add_filter( 'cron_schedules', function ( $schedules ) {
	$schedules['every_2_days_and_a_bit'] = [
		'interval' => get_my_interval( 1, 5, 20 ),
		'display' => __( 'Once every 2 days and a bit' )
	];
	return $schedules;
} ); // Warning: time undetermined.
