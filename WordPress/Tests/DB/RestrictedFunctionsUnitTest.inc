<?php

my_mysql_info(); // ok
wrap_mysql_info(); // ok
mysqlnd_msinfo(); // ok

class Foo {
	function mysql_info() {} // ok
}
class Bar {
	static function mysql_info() {} // ok
}

$x = new Foo();
$x->mysql_info(); // ok
$y = Bar::mysql_info(); // ok
\SomeNamespace\mysql_info(); // ok


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
maxdb_fetch_assoc
maxdb_init();
maxdb_num_fields();
maxdb_prepare();
maxdb_real_query
maxdb_stat();
