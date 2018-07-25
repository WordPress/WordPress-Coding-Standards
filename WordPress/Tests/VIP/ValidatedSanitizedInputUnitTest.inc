<?php

// @codingStandardsChangeSetting WordPress.VIP.ValidatedSanitizedInput customSanitizingFunctions sanitize_color,sanitize_twitter_handle
do_something_with( $_POST['hello'] ); // Error for no validation, Error for no sanitizing, Error for no unslashing.

// @codingStandardsChangeSetting WordPress.VIP.ValidatedSanitizedInput customSanitizingFunctions false
