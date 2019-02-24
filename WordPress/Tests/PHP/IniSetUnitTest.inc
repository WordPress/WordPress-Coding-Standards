<?php

ini_set('auto_detect_line_endings', true); // Ok.
ini_set( 'auto_detect_line_endings',true) ; // Ok.
ini_set('highlight.bg', '#FFFFFF'); // Ok.
ini_set('highlight.comment', '#FFFFFF'); // Ok.
ini_set('highlight.default', '#FFFFFF'); // Ok.
ini_set('highlight.html', '#FFFFFF'); // Ok.
ini_set('highlight.keyword', '#FFFFFF'); // Ok.
ini_set('highlight.string', '#FFFFFF'); // Ok.
ini_set('short_open_tag', true); // Ok.
ini_set('short_open_tag', 1); // Ok.
ini_set('short_open_tag', 'On'); // Ok.
ini_set('short_open_tag', 'on'); // Ok.

ini_set('bcmath.scale', 0); // Error.
ini_set( 'bcmath.scale' ,0 ); // Error.
ini_set('display_errors', 0); // Error.
ini_set('error_reporting', 0); // Error.
ini_set('filter.default', 'full_special_chars'); // Error.
ini_set('filter.default_flags', 0); // Error.
ini_set('iconv.input_encoding', ''); // Error.
ini_set('iconv.internal_encoding', ''); // Error.
ini_set('iconv.output_encoding', ''); // Error.
ini_set('iconv.output_encoding', ''); // Error.
ini_set('ignore_user_abort', true); // Error.
ini_set('log_errors', true); // Error.
ini_set('max_execution_time', 60); // Error.
ini_set('memory_limit', -1); // Error.
ini_set('short_open_tag', false); // Error.
ini_set('short_open_tag', FALSE); // Error.
ini_set('short_open_tag', FaLsE); // Error.
ini_set('short_open_tag', 0); // Error.
ini_set('short_open_tag', 'Off'); // Error.

ini_set('report_memleaks', true); // Warning.
ini_set('report_memleaks',true); // Warning.
ini_set('short_open_tag', 1230); // Warning.
ini_set($test, 1230); // Warning.

ini_alter('auto_detect_line_endings', true); // Ok.
ini_alter('display_errors', false); // Error.
ini_alter('report_memleaks', 1230); // Warning.
