<?php

global $wp_filesystem;

if( !defined( 'ABSPATH' ) ) die();

include_once ABSPATH . 'wp-admin/includes/file.php';

/**
 * Class SearchWPDebug is responsible for generating the search index
 */
class SearchWPDebug extends SearchWP
{
	public $active;
	private $logfile;

	function __construct()
	{
		global $wp_filesystem;

		// determine whether we are active
		$this->active = apply_filters( 'searchwp_debug', false );

		// if we're not active, don't do anything
		if( $this->active )
		{
			$this->logfile = trailingslashit( $this->instance()->dir ) . 'debug.log';

			// init environment
			if( !file_exists( $this->logfile ) )
			{
				WP_Filesystem();
				if( !$wp_filesystem->put_contents( $this->logfile, '' ) );
				{
					$this->active = false;
				}
			}

			if( $this->active )
				add_action( 'searchwp_log', array( $this, 'log' ), 1, 2 );
		}

	}

	function log( $message = '', $type = 'notice' )
	{
		global $wp_filesystem;
		WP_Filesystem();

		// if we're not active, don't do anything
		if( !$this->active || !file_exists( $this->logfile ) ) return false;

		// get the existing log
		$existing = $wp_filesystem->get_contents( $this->logfile );

		// format our entry
		$entry = '[' . current_time( 'timestamp' ) . '][' . sanitize_text_field( $type ) . '] ' . sanitize_text_field( $message );
		$log = $existing . "\n" . $entry;

		// write log
		$wp_filesystem->put_contents( $this->logfile, $log );
	}
}

new SearchWPDebug();
