<?php

/**
 * Uninstall SearchWP completely
 */

global $wpdb;

if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;

include_once 'searchwp.php';

// delete all plugin options
delete_option( SEARCHWP_PREFIX . 'settings' );
delete_option( SEARCHWP_PREFIX . 'license_key' );
delete_option( SEARCHWP_PREFIX . 'license_status' );
delete_option( SEARCHWP_PREFIX . 'total' );
delete_option( SEARCHWP_PREFIX . 'remaining' );
delete_option( SEARCHWP_PREFIX . 'done' );
delete_option( SEARCHWP_PREFIX . 'last_activity' );
delete_option( SEARCHWP_PREFIX . 'running' );
delete_option( SEARCHWP_PREFIX . 'version' );
delete_option( SEARCHWP_PREFIX . 'activated' );
delete_option( SEARCHWP_PREFIX . 'initial' );

delete_transient( 'searchwp' );

// purge the index
$searchwp = new SearchWP();
$searchwp->purgeIndex();

// drop all custom database tables
$tables = array( 'cf', 'index', 'log', 'media', 'tax', 'terms' );

foreach( $tables as $table )
{
	$tableName = $wpdb->prefix . SEARCHWP_DBPREFIX . $table;

	// make sure the table exists
	if( $wpdb->get_var( "SHOW TABLES LIKE '$tableName'") == $tableName )
	{
		// drop it
		$sql = "DROP TABLE $tableName";
		$wpdb->query( $sql );
	}
}
