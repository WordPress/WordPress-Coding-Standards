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

// Test some edge case/invalid code situations.
add_filter( 'cron_schedules', array() ); // Warning: time undetermined. Callback is an array, but can't determined method name.
add_filter( 'cron_schedules', function ( $schedules ) {
	$schedules['every_8_minutes'] = [
		'interval',
	];
	return $schedules;
} ); // Warning: time undetermined. Found 'interval', but no value.

// Safeguard fix to prevent an "Parse error: Unmatched ')'" bug in the eval-ed code.
add_filter( 'cron_schedules', function ( $schedules ) {
	$schedules['every_8_minutes'] = array(
		'interval' => \WEEK_IN_SECONDS
	);
	return $schedules;
} ); // OK.

// Safeguard fix to prevent an "Parse error: syntax error, unexpected token ";"" bug in the eval-ed code.
add_filter( 'cron_schedules', function ( $schedules ) {
	$schedules['every_8_minutes'] = array(
		'interval' => ( 2 * ( \WEEK_IN_SECONDS + 4 ) )
	);
	return $schedules;
} ); // OK.

// Allow for PHP 7.4+ arrow functions as callbacks.
add_filter( 'cron_schedules', fn ( $schedules ) => $schedules + array( 'every_9_mins' => array( 'interval' => 9 * 60, ) ) ); // Warning: 9 min.
add_filter( 'cron_schedules', fn ( $schedules ) => array_merge( $schedules, array( 'weekly' => array( 'interval' => WEEK_IN_SECONDS ) ) ) ); // OK: > 10 min.

// Allow for PHP 7.4+ numeric literals with underscores.
add_filter( 'cron_schedules', function ( $schedules ) {
	$schedules['every_day'] = array(
		'interval' => 86_400, // 24 hours.
		'display' => __( 'Once every day' )
	);
	return $schedules;
} ); // OK

add_filter( 'cron_schedules', function ( $schedules ) {
	$schedules['every_12_mins'] = array(
		'interval' => 7_2_0, // 12 minutes.
		'display' => __( 'Once every 12 minutes' )
	);
	return $schedules;
} ); // Warning: 12 min.

// Allow for PHP 8.1 explicit octal notation.
add_filter( 'cron_schedules', function ( $schedules ) {
	$schedules['every_six_hours'] = array(
		'interval' => 0o6 * HOUR_IN_SECONDS, // 6 hours.
		'display' => __( 'Once every six hours' )
	);
	return $schedules;
} ); // OK

add_filter( 'cron_schedules', function ( $schedules ) {
	$schedules['every_83 seconds'] = array(
		'interval' => 0O123, // 83 second.
		'display' => __( 'Once every 83 seconds' )
	);
	return $schedules;
} ); // Warning: 83 seconds.

// Safeguard support for PHP 8.0+ named parameters.
add_filter( callback: array( $place, 'cron_schedules' ), hook_name: 'some_hook', ); // OK, not the hook we're looking for.

function prefix_cron_schedules( $schedules ) {
	$schedules['every_2_mins'] = array(
		'interval' => 120,
		'display' => __( 'Once every 2 minutes' )
	);
	return $schedules;
}
add_filter(priority: 8, callback : 'prefix_cron_schedules', hook_name : 'cron_schedules', accepted_args: 1 ); // Warning: 2 min.
add_filter( callback: 'prefix_cron_schedules', hook_name: 'cron_schedules' ); // Warning: 2 min.

// Allow for PHP 8.1+ first class callables as callbacks.
class FirstClassCallables {
	public function add_schedules() {
		add_filter( 'cron_schedules', $this->cron_weekly_schedule(...) ); // Ok: > 15 min.
		add_filter( 'cron_schedules', $this->cron_eight_minute_schedule(...) ); // Warning: 8 min.
		add_filter( 'cron_schedules', self::cron_weekly_schedule(...) ); // Ok: > 15 min.
		add_filter( 'cron_schedules', static::cron_eight_minute_schedule(...) ); // Warning: 8 min.
		add_filter( 'cron_schedules', [$this, 'cron_weekly_schedule'](...) ); // Ok: > 15 min.
		add_filter( 'cron_schedules', array($this, 'cron_eight_minute_schedule')(...) ); // Warning: 8 min.
	}

	public static function cron_weekly_schedule( $schedules ) {
		$schedules['weekly'] = [
			'interval' => \WEEK_IN_SECONDS,
			'display'  => \__( 'Once Weekly', 'text-domain' ),
		];
		return $schedules;
	}

	public static function cron_eight_minute_schedule( $schedules ) {
		$schedules['every_8_minutes'] = [
			'interval' => (8 * \MINUTE_IN_SECONDS),
			'display' => __( 'Once every 8 minutes' )
		];
		return $schedules;
	}
}

function first_class_weekly_schedule( $schedules ) {
	$schedules['weekly'] = array(
		'interval' => WEEK_IN_SECONDS,
		'display' => __( 'Once Weekly' )
	);
	return $schedules;
}
add_filter( 'cron_schedules', 'first_class_weekly_schedule'(...)); // Ok: > 15 min.
add_filter( 'cron_schedules', first_class_weekly_schedule(...)); // Ok: > 15 min.

function first_class_six_min_schedule( $schedules ) {
	$schedules['every_6_mins'] = array(
		'interval' => 360,
		'display' => __( 'Once every 6 minutes' )
	);
	return $schedules;
}
add_filter( 'cron_schedules', first_class_six_min_schedule(...)); // Warning: 6 min.
add_filter( 'cron_schedules', 'first_class_six_min_schedule'(...)); // Warning: 6 min.
