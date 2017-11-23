<?php

// @codingStandardsChangeSetting WordPress.VIP.CronInterval min_interval 600
add_filter( 'cron_schedules', 'my_add_weekly'); // Error: 6 min.

// @codingStandardsChangeSetting WordPress.VIP.CronInterval min_interval 900
