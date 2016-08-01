<?php

my_mysql_info(); // Ok.
wrap_mysql_info(); // Ok.
mysqlnd_msinfo(); // Ok.

class Foo {
	function mysql_info() {} // Ok.
}
class Bar {
	static function mysql_info() {} // Ok.
}

$x = new Foo();
$x->mysql_info(); // Ok.
$y = Bar::mysql_info(); // Ok.
prefix_mysql_info(); // Ok.


/**
 * All the below should give an error.
 */

// MYSQL Extension.
mysql_affected_rows();
mysql_connect();
mysql_close();
mysql_fetch_row();
mysql_info();
mysql_numrows();
mysql_pconnect();
mysql_query();
mysql_result();

// MYSQLI Extension.
mysqli_client_encoding();
mysqli_connect();
mysqli_escape_string();
mysqli_execute();
mysqli_fetch();
mysqli_get_metadata();
mysqli_init();
mysqli_options();
mysqli_real_connect();

// MYSQLND_MS Extension.
mysqlnd_ms_fabric_select_global();
mysqlnd_ms_get_stats();
mysqlnd_ms_match_wild();
mysqlnd_ms_xa_begin();
mysqlnd_ms_xa_rollback();

// MYSQLND_QC Extension.
mysqlnd_qc_clear_cache();
mysqlnd_qc_get_cache_info();
mysqlnd_qc_get_query_trace_log();
mysqlnd_qc_set_cache_condition();

// MYSQLND_UH Extension.
mysqlnd_uh_convert_to_mysqlnd();

// MYSQLND_MEMCACHE Extension.
mysqlnd_memcache_set();

// MAXDB Extension.
maxdb_affected_rows();
maxdb_close();
maxdb_connect();
maxdb_errno();
maxdb_escape_string();
maxdb_fetch_assoc();
maxdb_init();
maxdb_num_fields();
maxdb_prepare();
maxdb_real_query();
maxdb_stat();


/**
 * And these shouldn't give an error.
 */

// WP Native function which was named a bit unfortunately.
mysql_to_rfc3339(); // Ok.

// Other WP Native functions which shouldn't give a problem anyway.
mysql2date(); // Ok.
wp_check_mysql_version(); // Ok.
wp_check_php_mysql_version(); // Ok.
WP_Date_Query::build_mysql_datetime(); // Ok.
