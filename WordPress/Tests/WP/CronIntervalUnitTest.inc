<?php

Foo::my_add_quicklier( $schedules ); // The old logic would get confused by this.

function my_add_weekly( $schedules ) {
	$schedules['every_6_mins'] = array(
		'interval' => 360,
		'display' => __( 'Once every 6 minutes' )
	);
	return $schedules;
}
add_filter( 'cron_schedules', 'my_add_weekly'); // Warning: 6 min.


class Foo {
	public function __construct() {
		add_filter( 'cron_schedules', array( $this, 'my_add_quickly' ) ); // Warning: 10 min.
	}

	public function my_add_quickly( $schedules ) {
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

add_filter( 'cron_schedules', array( 'Foo', 'my_add_quicklier' ) ); // Warning: 5 min.

add_filter( 'cron_schedules', array( $some_other_place, 'some_other_method' ) ); // Warning: time undetermined.

add_filter( 'cron_schedules', array( $some_other_place, $some_other_method ) ); // Warning: time undetermined.

add_filter( 'cron_schedules', function ( $schedules ) {
	$schedules['every_9_mins'] = array(
		'interval' => 9 * 60,
		'display' => __( 'Once every 9 minutes' )
	);
	return $schedules;
} ); // Warning: 9 min.

add_filter( 'cron_schedules' ); // Ignore, no callback parameter.

add_filter( 'cron_schedules', [ 'Foo', 'my_add_quicklier' ] ); // Warning: 5 min.

// Ignore, not our function.
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
} ); // Warning: 8 min.

// Deal correctly with the function calls for interval.
add_filter( 'cron_schedules', function ( $schedules ) {
	$schedules['every_2_days_and_a_bit'] = [
		'interval' => get_my_interval( 1, 5, 20 ),
		'display' => __( 'Once every 2 days and a bit' )
	];
	return $schedules;
} ); // Warning: time undetermined.

// phpcs:set WordPress.WP.CronInterval min_interval 600
add_filter( 'cron_schedules', function ( $schedules ) {
	$schedules['every_2_mins'] = array(
		'interval' => 2 * 60,
		'display' => __( 'Once every 2 minutes' )
	);
	return $schedules;
} ); // Warning: 2 min.
add_filter( 'cron_schedules', function ( $schedules ) {
	$schedules['every_10_mins'] = array(
		'interval' => 10 * 60,
		'display' => __( 'Once every 10 minutes' )
	);
	return $schedules;
} ); // OK: 10 min.
add_filter( 'cron_schedules', function ( $schedules ) {
	$schedules['every_hour'] = [
		'interval' => HOUR_IN_SECONDS,
		'display' => __( 'Once every hour' )
	];
	return $schedules;
} ); // OK: > 10 min.

// phpcs:set WordPress.WP.CronInterval min_interval 1800
add_filter( 'cron_schedules', function ( $schedules ) {
	$schedules['every_2_mins'] = array(
		'interval' => 2 * 60,
		'display' => __( 'Once every 2 minutes' )
	);
	return $schedules;
} ); // Warning: 2 min.
add_filter( 'cron_schedules', function ( $schedules ) {
	$schedules['every_15_mins'] = array(
		'interval' => 15 * 60,
		'display' => __( 'Once every 15 minutes' )
	);
	return $schedules;
} ); // Warning: 15 min.
add_filter( 'cron_schedules', function ( $schedules ) {
	$schedules['every_hour'] = [
		'interval' => HOUR_IN_SECONDS,
		'display' => __( 'Once every hour' )
	];
	return $schedules;
} ); // Ok: > 30 min.

// phpcs:set WordPress.WP.CronInterval min_interval 900


add_filter( 'cron_schedules', function ( $schedules ) {
	$schedules['every_9_mins'] = array(
		'interval' =>
			// phpcs:ignore Standard.Cat.Sniff -- for reasons
			9 /* minutes */ * 60 /* seconds */,
		'display' => __( 'Once every 9 minutes' )
	);
	return $schedules;
} ); // Warning: 9 min.

Custom::add_filter( 'cron_schedules', array( $class, $method ) ); // OK, not the WP function.
add_filter( 'some_hook', array( $place, 'cron_schedules' ) ); // OK, not the hook we're looking for.
add_filter( function() { return get_hook_name('cron_schedules'); }(), array( $class, $method ) ); // OK, nested in another function call.

// Deal correctly with the time calculations within parentheses.
add_filter( 'cron_schedules', function ( $schedules ) {
	$schedules['every_2_days_and_a_bit'] = [
		'interval' => ( 2 * DAY_IN_SECONDS + 2 * HOUR_IN_SECONDS ),
		'display' => __( 'Once every 2 days and a bit' )
	];
	return $schedules;
} ); // Ok: > 15 min.

add_filter( 'cron_schedules', function ( $schedules ) {
	$schedules['every_8_minutes'] = [
		'interval' => (8 * MINUTE_IN_SECONDS),
		'display' => __( 'Once every 8 minutes' )
	];
	return $schedules;
} ); // Warning: 8 min.

// Correctly handle fully qualified WP time constants.
class FQNConstants {
	public function add_schedules() {
		add_filter( 'cron_schedules', array( $this, 'add_weekly_schedule' ) ); // Ok: > 15 min.
		add_filter( 'cron_schedules', array( $this, 'add_eight_minute_schedule' ) ); // Warning: 8 min.
		add_filter( 'cron_schedules', array( $this, 'add_hundred_minute_schedule' ) ); // Warning: time undetermined.
		add_filter( 'cron_schedules', array( $this, 'sneaky_fake_wp_constant_schedule' ) ); // Warning: time undetermined.
	}

	public function add_weekly_schedule( $schedules ) {
		$schedules['weekly'] = [
			'interval' => \WEEK_IN_SECONDS,
			'display'  => \__( 'Once Weekly', 'text-domain' ),
		];
		return $schedules;
	}

	public function add_eight_minute_schedule( $schedules ) {
		$schedules['every_8_minutes'] = [
			'interval' => (8 * \MINUTE_IN_SECONDS),
			'display' => __( 'Once every 8 minutes' )
		];
		return $schedules;
	}

	public function add_hundred_minute_schedule( $schedules ) {
		$schedules['every_100_minutes'] = [
			'interval' => (100 * My\Name\MINUTE_IN_SECONDS),
			'display' => __( 'Once every 100 minutes' )
		];
		return $schedules;
	}

	public function sneaky_fake_wp_constant_schedule( $schedules ) {
		$schedules['every_100_seconds'] = [
			'interval' => (100 * MINUTE_\IN_\SECONDS),
			'display' => __( 'Once every 100 minutes' )
		];
		return $schedules;
	}
}
