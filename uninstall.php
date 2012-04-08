<?php 
if (defined('WP_UNINSTALL_PLUGIN')){
	global $wpdb;
	$table_name = $wpdb->prefix . "subscribers_list";
	$wpdb->query( "DROP TABLE IF EXISTS $table_name" );
}
?>
