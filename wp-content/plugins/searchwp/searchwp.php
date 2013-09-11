<?php
/*
Plugin Name: SearchWP
Plugin URI: https://searchwp.com/
Description: The best WordPress search you can find
Version: 1.0.10
Author: Jonathan Christopher
Author URI: https://searchwp.com/

Copyright 2013 Jonathan Christopher

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, see <http://www.gnu.org/licenses/>.
*/

// exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// store whether or not we're in the admin
if( !defined( 'IS_ADMIN' ) ) define( 'IS_ADMIN',  is_admin() );

// minimum WordPress version requirement
$wp_version = get_bloginfo( 'version' );
if( version_compare( $wp_version, '3.5', '<' ) )
{
	require_once ABSPATH . '/wp-admin/includes/plugin.php';
	deactivate_plugins( __FILE__ );
	wp_die( esc_attr( __( 'SearchWP requires WordPress 3.5 or higher. Please upgrade before activating this plugin.' ) ) );
}

define( 'SEARCHWP_VERSION', 			'1.0.10' );
define( 'SEARCHWP_PREFIX', 				'searchwp_' );
define( 'SEARCHWP_DBPREFIX', 			'swp_' );
define( 'EDD_SEARCHWP_STORE_URL', 'http://searchwp.com' );
define( 'EDD_SEARCHWP_ITEM_NAME', 'SearchWP' );

if( !class_exists( 'EDD_SL_Plugin_Updater' ) )
{
	// load our custom updater
	include( dirname( __FILE__ ) . '/vendor/EDD_SL_Plugin_Updater.php' );
}

// retrieve our license key from the DB
$license_key = trim( get_option( SEARCHWP_PREFIX . 'license_key' ) );

// setup the updater
$edd_updater = new EDD_SL_Plugin_Updater( EDD_SEARCHWP_STORE_URL, __FILE__, array(
		'version' 	=> SEARCHWP_VERSION, 				// current version number
		'license' 	=> $license_key, 						// license key (used get_option above to retrieve from DB)
		'item_name' => EDD_SEARCHWP_ITEM_NAME, 	// name of this plugin
		'author' 		=> 'Jonathan Christopher' 	// author of this plugin
	)
);

global $searchwp;

/**
 * Class SearchWP
 * @since 1.0
 */
class SearchWP
{
	/**
	 * @var SearchWP The SearchWP singleton
	 * @since 1.0
	 */
	private static $instance;

	/**
	 * @var string License key
	 */
	public $license;

	/**
	 * @var string License status
	 */
	public $status;

	/**
	 * @var string The plugin directory
	 * @since 1.0
	 */
	public $dir;

	/**
	 * @var string The plugin URL
	 * @since 1.0
	 */
	public $url;

	/**
	 * @var string The plugin version
	 * @since 1.0
	 */
	public $version;

	/**
	 * @var bool Whether a search is taking place right now
	 * @since 1.0
	 */
	public $active = false;

	/**
	 * @var bool Whether indexing is taking place right now
	 * @since 1.0.6
	 */
	public $indexing = false;

	/**
	 * @var bool Whether we're in WordPress' main query
	 * @since 1.0
	 */
	public $isMainQuery = false;

	/**
	 * @var string Plugin name
	 * @since 1.0
	 */
	public $pluginName = 'SearchWP';

	/**
	 * @var string Plugin textdomain, used in localization
	 * @since 1.0
	 */
	public $textDomain = 'searchwp';

	/**
	 * @var array Stores custom field keys
	 * @since 1.0
	 */
	public $keys;

	/**
	 * @var array Stores all SearchWP settings
	 * @since 1.0
	 */
	public $settings;

	/**
	 * @var array Stores registered post types
	 */
	public $postTypes = array();

	/**
	 * @var array Common words as specified by Ando Saabas in Sphider http://www.sphider.eu/
	 * @since 1.0
	 */
	public $common = array(
		"a", "about", "after", "ago", "all", "am", "an", "and", "any", "are", "aren't", "as", "at", "be", "but", "by",
		"for", "from", "get", "go", "how", "if", "in", "into", "is", "isn't", "it", "its", "me", "more", "most", "must",
		"my", "new", "news", "no", "none", "not", "nothing", "of", "off", "often", "old", "on", "once", "only", "or",
		"other", "our", "ours", "out", "over", "page", "she", "should", "small", "so", "some", "than", "true", "thank",
		"that", "the", "their", "theirs", "them", "then", "there", "these", "they", "this", "those", "thus", "time",
		"times", "to", "too", "under", "until", "up", "upon", "use", "users", "version", "very", "via", "want", "was",
		"way", "web", "were", "what", "when", "where", "which", "who", "whom", "whose", "why", "wide", "will", "with",
		"within", "without", "would", "yes", "yet", "you", "your", "yours",
	);

	/**
	 * @var array Stores valid weight types
	 */
	public $validTypes = array( 'content', 'title', 'comment', 'tax', 'excerpt', 'cf', 'slug' );

	/**
	 * @var array Stores valid search engine option keys
	 */
	public $validOptions = array( 'exclude', 'attribute_to', 'stem', 'parent' );

	/**
	 * @var int Number of posts found in a query
	 */
	public $foundPosts 	= 0;

	/**
	 * @var int Number of pages in paginated results
	 */
	public $maxNumPages = 0;

	/**
	 * @var array Stores a purge queue
	 * @since 1.0.7
	 */
	private $purgeQueue = array();


	/**
	 * Singleton
	 *
	 * @return SearchWP
	 * @since 1.0
	 */
	public static function instance()
	{
		if( !isset( self::$instance ) && !( self::$instance instanceof SearchWP ) )
		{
			self::$instance = new SearchWP;
			self::$instance->init();

			// we want to purge a post from the index when comments are manipulated
			// TODO: make this more elaborate to only update what's necessary
			add_action( 'comment_post', 	array( self::$instance, 'purgePostViaComment' ) );
			add_action( 'edit_comment', 	array( self::$instance, 'purgePostViaComment' ) );
			add_action( 'trash_comment', 	array( self::$instance, 'purgePostViaComment' ) );
			add_action( 'delete_comment', array( self::$instance, 'purgePostViaComment' ) );

			// purge a post from the index when a related term is deleted
			add_action( 'set_object_terms', array( self::$instance, 'purgePostViaTerm'), 10, 6 );

			// process the purge queue once everything is said and done
			add_action( 'shutdown', array( self::$instance, 'processPurgeQueue' ) );
		}
		return self::$instance;
	}


	function processPurgeQueue()
	{
		do_action( 'searchwp_log', 'processPurgeQueue() ' . count( $this->purgeQueue ) );

		if( is_array( $this->purgeQueue) && !empty( $this->purgeQueue ) )
		{
			foreach( $this->purgeQueue as $object_id )
				$this->purgePost( $object_id );

			do_action( 'searchwp_log', 'Purge queue processed, triggerIndex()' );
			$this->triggerIndex();
		}
	}


	/**
	 * Initialization routine. Sets version, directory, url, adds WordPress hooks, includes includes, triggers index
	 *
	 * @uses get_post_types to determine which post types are in use
	 * @since 1.0
	 */
	function init()
	{
		$this->version 	= SEARCHWP_VERSION;
		$this->dir 			= dirname( __FILE__ );
		$this->url 			= plugins_url( 'searchwp' );
		$this->settings = get_option( SEARCHWP_PREFIX . 'settings' );

		$this->license 	= get_option( SEARCHWP_PREFIX . 'license_key' );
		$this->status 	= get_option( SEARCHWP_PREFIX . 'license_status' );

		$this->postTypes = array_merge(
			array(
				'post' 					=> 'post',
				'page' 					=> 'page',
				'attachment' 		=> 'attachment'
			),
			get_post_types(
				array(
					'exclude_from_search' 	=> false,
					'_builtin' 							=> false
				)
			)
		);

		// allow filtration of what SearchWP considers common words (i.e. ignores)
		$this->common = apply_filters( 'searchwp_common_words', $this->common );

		// hooks
		add_action( 'admin_menu', 						array( $this, 'adminMenu' ) );
		add_action( 'admin_init', 						array( $this, 'initSettings' ) );
		add_action( 'admin_init', 						array( $this, 'activateLicense' ) );
		add_action( 'admin_init', 						array( $this, 'deactivateLicense' ) );
		add_action( 'init', 									array( $this, 'textdomain' ) );
		add_action( 'admin_notices', 					array( $this, 'activation' ) ) ;
		add_action( 'wp', 										array( $this, 'scheduleMaintenance' ) );
		add_filter( 'cron_schedules', 				array( $this, 'addCustomCronInterval' ) );
		add_action( 'swp_maintenance', 				array( $this, 'doMaintenance' ) );
		add_action( 'swp_cron_indexer', 			array( $this, 'doCron' ) );
		add_action( 'admin_enqueue_scripts', 	array( $this, 'assets' ) );
		add_action( 'wp_ajax_swp_progress', 	array( $this, 'getIndexProgress' ) );
		add_action( 'pre_get_posts', 					array( $this, 'checkForMainQuery' ) );
		add_filter( 'the_posts', 							array( $this, 'wpSearch' ) );
		add_action( 'add_meta_boxes', 				array( $this, 'documentContentMetaBox' ) );
		add_action( 'edit_attachment', 				array( $this, 'documentContentSave' ) );

		// index update triggers
		if( current_user_can( 'edit_posts' ) )
			add_action( 'save_post', array( $this, 'purgePostViaEdit' ), 999 );

		if( current_user_can( 'delete_posts' ) )
			add_action( 'before_delete_post', array( $this, 'purgePostViaEdit' ), 999 );

		// includes
		include( $this->dir . '/includes/class.debug.php' );
		include( $this->dir . '/includes/class.stemmer.php' );
		include( $this->dir . '/includes/class.indexer.php' );
		include( $this->dir . '/templates/tmpl.engine.config.php' );
		include( $this->dir . '/templates/tmpl.supplemental.config.php' );
		include( $this->dir . '/includes/class.search.php' );
		include( $this->dir . '/includes/class.upgrade.php' );

		if( !class_exists( 'PDF2Text' ) )
			include( $this->dir . '/includes/class.pdf2text.php' );

		if( !class_exists( 'pdf_readstream' ) )
			include( $this->dir . '/includes/class.pdfreadstream.php' );

		do_action( 'searchwp_log', '========== INIT ==========' );

		// check for upgrade
		new SearchWPUpgrade( $this->version );

		// trigger the index
		if( !$this->indexing && isset( $_GET['page'] ) && $_GET['page'] == 'searchwp' )
		{
			$this->indexing = true;
			$init = $this->triggerIndex();

			// if it was an error, the server doesn't like pseudo-loopback connections
			$hash = $init['hash'];
			$init = $init['request'];
			if( is_wp_error( $init ) )
			{
				$errorMessage = $init->get_error_message();
				do_action( 'searchwp_log', 'Index request failed ' . $hash );
				do_action( 'searchwp_log', $errorMessage );

				delete_transient( 'searchwp' );
				$hash = sha1( uniqid( 'searchwpindex' ) );
				set_transient( 'searchwp', $hash );
				do_action( 'searchwp_log', 'Request internal index ' . $hash );
				new SearchWPIndexer( $hash );
			}
		}

		// trigger background indexing
		if( !$this->indexing && isset( $_GET['swpnonce'] ) && get_transient( 'searchwp' ) === sanitize_text_field( $_GET['swpnonce'] ) )
		{
			$this->indexing = true;
			$hash = sanitize_text_field( $_GET['swpnonce'] );
			do_action( 'searchwp_log', 'Request background index ' . $hash );
			new SearchWPIndexer( $hash );
		}

		// reset short circuit check
		$this->indexing = false;

	}


	/**
	 * Set up and trigger background index call
	 *
	 * @return array
	 */
	function triggerIndex()
	{
		$hash = sha1( uniqid( 'searchwpindex' ) );
		set_transient( 'searchwp', $hash );

		do_action( 'searchwp_log', 'triggerIndex() ' . trailingslashit( site_url() ) . '?swpnonce=' . $hash );

		$request = wp_remote_get(
			trailingslashit( site_url() ) . '?swpnonce=' . $hash,
			array(
				'blocking' 		=> false,
				'user-agent' 	=> 'SearchWP'
			)
		);
		return array( 'hash' => $hash, 'request' => $request );
	}


	/**
	 * Checks to see if we're in the main query and stores result as isMainQuery property
	 *
	 * @param $query WP_Query Instance of WP_Query to check
	 * @since 1.0
	 */
	function checkForMainQuery( $query )
	{
		if( !is_admin() && $query->is_main_query() )
			$this->isMainQuery = true;
	}


	/**
	 * Perform a search query
	 *
	 * @param string $engine The search engine name to use when performing the search
	 * @param $terms string|array The search terms to include in the query
	 * @param int $page Results are paged, return this page (1 based)
	 * @return array Search results post IDs ordered by weight DESC
	 * @uses SearchWPSearch
	 * @since 1.0
	 */
	function search( $engine = 'default', $terms, $page = 1 )
	{
		global $wpdb;

		$this->active = true;

		// at the very least, our terms are the search query
		$terms = $originalQuery = is_array( $terms ) ? trim( implode( ' ', $terms ) ) : trim( (string) $terms );

		$sanitizeTerms = apply_filters( 'searchwp_sanitize_terms', true );
		if( !is_bool( $sanitizeTerms ) ) $sanitizeTerms = true;

		// facilitate filtering the actual terms
		$terms = apply_filters( 'searchwp_terms', $terms );

		// if we should still sanitize our terms, do it
		if( $sanitizeTerms )
			$terms = $this->sanitizeTerms( $terms );

		// set up our engine name
		$engine = $this->isValidEngine( $engine ) ? $engine : '';

		// make sure the search isn't overflowing with terms
		$maxSearchTerms = intval( apply_filters( 'searchwp_max_search_terms', 6 ) );
		$maxSearchTerms = intval( apply_filters( 'searchwp_max_search_terms_supplemental', $maxSearchTerms ) );
		$maxSearchTerms = intval( apply_filters( "searchwp_max_search_terms_{$engine}", $maxSearchTerms ) );

		if( count( $terms ) > $maxSearchTerms )
			$terms = array_slice( $terms, 0, $maxSearchTerms );

		// prep our args
		$args = array(
			'engine'			=> $engine,
			'terms' 			=> $terms,
			'page'				=> intval( $page ),
			'posts_per_page' 	=> apply_filters( 'searchwp_posts_per_page', intval( get_option( 'posts_per_page' ) ) )
		);

		$searchwp = new SearchWPSearch( $args );

		$this->foundPosts 	= intval( $searchwp->foundPosts );
		$this->maxNumPages	= intval( $searchwp->maxNumPages );

		// log this
		$wpdb->insert(
			$wpdb->prefix . SEARCHWP_DBPREFIX . 'log',
			array(
				'event' 		=> 'search',
				'query'			=> $originalQuery,
				'hits'			=> $this->foundPosts,
				'engine'		=> $engine,
				'wpsearch'	=> 0
			),
			array(
				'%s',
				'%s',
				'%d',
				'%s',
				'%d'
			)
		);

		$this->active = false;

		$results = apply_filters( 'searchwp_results', $searchwp->posts, array(
			'terms' 			=> $terms,
			'page'  			=> $args['page'],
			'order' 			=> 'DESC',
			'foundPosts' 	=> $this->foundPosts,
			'maxNumPages' => $this->maxNumPages,
			'engine'			=> $engine,
		) );

		return $results;
	}


	/**
	 * Determines if an engine name is considered valid (e.g. stored in the settings)
	 *
	 * @param $engineName string The engine name to check
	 * @return bool
	 */
	public function isValidEngine( $engineName )
	{
		$engineName = sanitize_key( $engineName );
		return is_string( $engineName ) && isset( $this->settings['engines'][$engineName] );
	}


	public function cleanTermString( $termString )
	{
		$punctuation = array( "(", ")", "·", "'", "´", "’", "‘", "”", "“", "„", "—", "–", "×", "…", "€", "\n", ".", "," );

		if( !is_string( $termString ) ) $termString = '';

		$termString = strtolower( $termString );
		$termString = stripslashes( $termString );

		// remove punctuation
		$termString = str_replace( $punctuation, ' ', $termString );
		$termString = preg_replace( "/[[:punct:]]/uiU", " ", $termString );

		// remove spaces
		$termString = preg_replace( "/[[:space:]]/uiU", " ", $termString );
		$termString = trim( $termString );

		return $termString;
	}


	/**
	 * Sanitizes terms; should be trimmed, single words.
	 *
	 * @param $terms string|array The terms to sanitize
	 * @return array Valid terms
	 */
	public function sanitizeTerms( $terms )
	{
		$validTerms = array();

		// always going to be a string when a search query is performed
		if( is_string( $terms ) )
		{
			// preprocess the string to strip out unwanted punctuation
			$terms = sanitize_text_field( trim( $terms ) ) . ' ' . $this->cleanTermString( $terms );

			$terms = ( strpos( $terms, ' ' ) !== false ) ? explode( ' ', $terms ) : array( $terms );
		}

		if( is_array( $terms ) )
			foreach( $terms as $key => $term )
			{
				// prep the term
				$term = $this->cleanTermString( $term );

				if( strpos( $term, ' ' ) )
				{
					// append the new broken down terms
					$terms = array_merge( $terms, explode( ' ', $term ) );
				}
				else
				{
					// proceed
					$excludeCommon = apply_filters( 'searchwp_exclude_common', true );
					if( !is_bool( $excludeCommon ) ) $excludeCommon = true;
					if( ( $excludeCommon && !in_array( $term, $this->common ) ) || !$excludeCommon )
						$validTerms[$key] = sanitize_text_field( trim( $term ) );
				}

			}

		// after removing punctuation we might have some empty keys
		$validTerms = array_filter( $validTerms, 'strlen' );

		// we also might have duplicates
		$validTerms = array_values( array_unique( $validTerms ) );

		return $validTerms;
	}


	/**
	 * Callback for the_posts filter. Hijacks WordPress searches and returns SearchWP results
	 *
	 * @param $posts array The original posts array from WordPress' query
	 * @return array The posts in the search results from SearchWP
	 * @uses SearchWPSearch
	 * @since 1.0
	 */
	function wpSearch( $posts )
	{
		global $wp_query, $wpdb;

		// make sure we're not in the admin, that we are searching, that it is the main query, and that SearchWP is not active
		$proceedIfInAdmin = apply_filters( 'searchwp_in_admin', false );
		if( is_admin() && !$proceedIfInAdmin )
			return $posts;

		if( !$wp_query->is_search || !$this->isMainQuery || $this->active  )
			return $posts;

		// a search is currently taking place, let's provide some wicked better results
		$this->active = true;
		$wpPaged 			= ( intval( $wp_query->query_vars['paged'] ) > 0 ) ? intval( $wp_query->query_vars['paged'] ) : 1;

		// at the very least, our terms are the search query
		$originalQuery = $wp_query->query_vars['s'];
		$terms = stripslashes( strtolower( trim( $wp_query->query_vars['s'] ) ) );

		// facilitate filtering the actual terms
		$terms = apply_filters( 'searchwp_terms', $terms );

		// handle sanitization
		$sanitizeTerms = apply_filters( 'searchwp_sanitize_terms', true );
		if( !is_bool( $sanitizeTerms ) ) $sanitizeTerms = true;

		// if we should still sanitize our terms, do it
		if( $sanitizeTerms )
			$terms = $this->sanitizeTerms( $terms );

		// determine the order from WP_Query
		$order = ( strtoupper( $wp_query->query_vars['order'] ) == 'DESC' ) ? 'DESC' : 'ASC';

		$args = array(
			'terms' 	=> $terms,
			'page'		=> $wpPaged,
			'order'		=> $order,
		);

		// make sure the search isn't overflowing with terms
		$maxSearchTerms = intval( apply_filters( 'searchwp_max_search_terms', 6 ) );
		if( count( $terms ) > $maxSearchTerms )
		{
			$terms = array_slice( $terms, 0, $maxSearchTerms );

			// need to tell $wp_query that we hijacked this
			$wp_query->query['s'] = $wp_query->query_vars['s'] = sanitize_text_field( implode( ' ', $terms ) );
		}

		if( !empty( $terms ) )
		{
			$searchwp = new SearchWPSearch( $args );

			$this->active = false;
			$this->isMainQuery = false;

			// we need to tell WP Query about everything that's different as per these better results
			$wp_query->found_posts 		= intval( $searchwp->foundPosts );
			$wp_query->max_num_pages	= intval( $searchwp->maxNumPages );

			// log this
			$wpdb->insert(
				$wpdb->prefix . SEARCHWP_DBPREFIX . 'log',
				array(
					'event' 		=> 'search',
					'query'			=> $originalQuery,
					'hits'			=> $wp_query->found_posts,
					'wpsearch'	=> 1
				),
				array(
					'%s',
					'%s',
					'%d',
					'%d'
				)
			);

			$results = apply_filters( 'searchwp_results', $searchwp->posts, array(
				'terms' 			=> $terms,
				'page'  			=> $wpPaged,
				'order' 			=> $order,
				'foundPosts' 	=> $wp_query->found_posts,
				'maxNumPages' => $wp_query->max_num_pages,
				'engine'			=> 'default',
			) );

			return $results;
		}
		else
		{
			return false;
		}
	}


	/**
	 * Callback for admin_menu action; adds SearchWP link to Settings menu in the WordPress admin
	 *
	 * @since 1.0
	 */
	function adminMenu()
	{
		add_options_page( $this->pluginName, __( $this->pluginName, $this->textDomain ), 'manage_options', $this->textDomain, array( $this, 'optionsPage' ) );
		add_dashboard_page( __( 'Search Statistics', $this->textDomain ), __( 'Search Stats', $this->textDomain ), 'read', $this->textDomain . '-stats', array( $this, 'statsPage' ) );
	}


	/**
	 * Callback for admin_enqueue_scripts. Enqueues our assets.
	 * @param $hook string
	 *
	 * @since 1.0
	 */
	function assets( $hook )
	{
		wp_register_style( 'select2', trailingslashit( $this->url ) . 'assets/vendor/select2/select2.css', null, '3.4.1', 'screen' );
		wp_register_style( 'swp_admin_css', trailingslashit( $this->url ) . 'assets/css/searchwp.css', false, $this->version );
		wp_register_style( 'swp_stats_css', trailingslashit( $this->url ) . 'assets/css/searchwp-stats.css', false, $this->version );

		wp_register_script( 'select2', trailingslashit( $this->url ) . 'assets/vendor/select2/select2.min.js', array( 'jquery' ), '3.4.1', false );
		wp_register_script( 'swp_admin_js', trailingslashit( $this->url ) . 'assets/js/searchwp.js', array( 'jquery', 'select2' ), $this->version );
		wp_register_script( 'swp_progress', trailingslashit( $this->url ) . 'assets/js/searchwp-progress.js', array( 'jquery' ), $this->version );

		// jqPlot
		wp_register_style( 'jqplotcss', trailingslashit( $this->url ) . 'assets/vendor/jqplot/jquery.jqplot.min.css', false, '1.0.8' );
		wp_register_script( 'jqplotjs', trailingslashit( $this->url ) . 'assets/vendor/jqplot/jquery.jqplot.min.js', array( 'jquery' ), '1.0.8' );
		wp_register_script( 'jqplotjs-barrenderer', trailingslashit( $this->url ) . 'assets/vendor/jqplot/plugins/jqplot.barRenderer.min.js', array( 'jqplotjs' ), '1.0.8' );
		wp_register_script( 'jqplotjs-canvastext', trailingslashit( $this->url ) . 'assets/vendor/jqplot/plugins/jqplot.canvasTextRenderer.min.js', array( 'jqplotjs' ), '1.0.8' );
		wp_register_script( 'jqplotjs-canvasaxis', trailingslashit( $this->url ) . 'assets/vendor/jqplot/plugins/jqplot.canvasAxisLabelRenderer.min.js', array( 'jqplotjs' ), '1.0.8' );
		wp_register_script( 'jqplotjs-axisrenderer', trailingslashit( $this->url ) . 'assets/vendor/jqplot/plugins/jqplot.categoryAxisRenderer.min.js', array( 'jqplotjs' ), '1.0.8' );
		wp_register_script( 'jqplotjs-pointlabels', trailingslashit( $this->url ) . 'assets/vendor/jqplot/plugins/jqplot.pointLabels.min.js', array( 'jqplotjs' ), '1.0.8' );

		// we only want our assets on our Settings page
		if( $hook == 'settings_page_searchwp' )
		{
			wp_enqueue_style( 'swp_admin_css' );
			wp_enqueue_style( 'select2' );

			wp_enqueue_script( 'underscore' );
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui-tooltip' );
			wp_enqueue_script( 'select2' );

			wp_enqueue_script( 'swp_admin_js' );
			wp_enqueue_script( 'swp_progress' );
			wp_localize_script( 'swp_progress', 'ajax_object',
				array(
					'ajax_url'	=> admin_url( 'admin-ajax.php' ),
					'nonce' 	=> wp_create_nonce( 'swpprogress' ) )
			);
		}

		if( 'dashboard_page_searchwp-stats' == $hook )
		{
			wp_enqueue_script( 'jqplotjs' );
			wp_enqueue_script( 'jqplotjs-canvastext' );
			wp_enqueue_script( 'jqplotjs-canvasaxis' );
			// wp_enqueue_script( 'jqplotjs-barrenderer' );
			wp_enqueue_script( 'jqplotjs-axisrenderer' );
			// wp_enqueue_script( 'jqplotjs-pointlabels' );
			wp_enqueue_style( 'jqplotcss' );
			wp_enqueue_style( 'swp_stats_css' );
		}
	}


	/**
	 * Determines what percentage of indexing is complete. Polled via AJAX when viewing SearchWP settings page
	 *
	 * @since 1.0
	 */
	function getIndexProgress()
	{
		$totalPostsToIndex 		= intval( get_option( SEARCHWP_PREFIX . 'total' ) );
		$remainingPostsToIndex 	= intval( get_option( SEARCHWP_PREFIX . 'remaining' ) );

		$percentIndexComplete 	= ( $totalPostsToIndex > 0 ) ? ( ( $totalPostsToIndex - $remainingPostsToIndex ) / $totalPostsToIndex ) * 100 : 0;

		if( !empty( $remainingPostsToIndex ) )
		{
			echo number_format( $percentIndexComplete, 2, '.', '' );
		}
		else
		{
			echo -1;
		}
		die();
	}


	/**
	 * Outputs the stats page and all stats
	 *
	 * @since 1.0
	 */
	function statsPage()
	{
		global $wpdb;

		?>
		<div class="wrap">

			<div id="icon-searchwp" class="icon32">
				<img src="<?php echo trailingslashit( $this->url ); ?>assets/images/searchwp@2x.png" alt="SearchWP" width="21" height="32" />
			</div>

			<h2><?php _e( 'Searches' ); ?></h2>

			<br />
			<div class="swp-searches-chart-wrapper">
				<div id="swp-searches-chart" style="width:100%;height:300px;"></div>
			</div>

			<script type="text/javascript">
				jQuery(document).ready(function($){

					<?php
						// generate stats for the past 30 days for each search engine
						$prefix = $wpdb->prefix;
						if( isset( $this->settings['engines'] ) && is_array( $this->settings['engines'] ) && count( $this->settings['engines'] ) )
						{
							$engineLabels = array();
							$searchCounts = array();
							$engineCount = 1;
							foreach( $this->settings['engines'] as $engine => $engineSettings )
							{
								$sql = $wpdb->prepare( "
									SELECT DAY({$prefix}swp_log.tstamp) AS day, MONTH({$prefix}swp_log.tstamp) AS month, count({$prefix}swp_log.tstamp) AS searches
									FROM {$prefix}swp_log
									WHERE tstamp > DATE_SUB(NOW(), INTERVAL 30 day)
									AND {$prefix}swp_log.event = 'search'
									AND {$prefix}swp_log.engine = %s
									AND {$prefix}swp_log.query <> ''
									GROUP BY TO_DAYS({$prefix}swp_log.tstamp)
									ORDER BY {$prefix}swp_log.tstamp DESC
									", $engine );

								$searchCounts = $wpdb->get_results(
									$sql, 'OBJECT_K'
								);

								// key our array
								$searchesPerDay = array();
								for($i = 0; $i < 30; $i++)
									$searchesPerDay[strtoupper(date( 'Md', strtotime( '-'. ( $i ) .' days' ) ))] = 0;

								if( is_array( $searchCounts ) && count( $searchCounts ) )
								{
									foreach( $searchCounts as $searchCount )
									{
										$count 		= intval( $searchCount->searches );
										$day 		= ( intval( $searchCount->day ) ) < 10 ? 0 . $searchCount->day : $searchCount->day;
										$month 		= ( intval( $searchCount->month ) ) < 10 ? 0 . $searchCount->month : $searchCount->month;
										$refdate 	= $month . '/01/' . date( 'Y' );
										$month 		= date( 'M', strtotime( $refdate ) );
										$key 		= strtoupper( $month . $day );

										$searchesPerDay[$key] = $count;
									}
								}

								$searchesPerDay = array_reverse( $searchesPerDay );

								echo 'var s' . $engineCount . ' = [';
								echo implode( ',', $searchesPerDay );
								echo '];';

								$engineLabel = "'";
								$engineLabel .= isset( $engineSettings['label'] ) ? $engineSettings['label'] : esc_attr__( 'Default', $this->textDomain );
								$engineLabel .= "'";
								$engineLabels[] = $engineLabel;

								$engineCount++;
							}
							$engineCount = 1;
							$engines = array();
							foreach( $this->settings['engines'] as $engine => $engineSettings )
							{
								$engines[] = 's' . $engineCount;
								$engineCount++;
							}
						?>
					plot = $.jqplot('swp-searches-chart', [<?php echo implode( ',', $engines ); ?>], {
						title:'<?php esc_attr_e( 'Searches Performed in the Past 30 Days', $this->textDomain ); ?>',
						stackSeries: false,
						captureRightClick: true,
						seriesDefaults:{
							renderer:$.jqplot.BarRenderer,
							rendererOptions: {
								barMargin: 20,
								highlightMouseDown: false,
								shadowOffset: 0,
								shadowDepth: 0,
								shadowAlpha: 0
							},
							pointLabels: {show: true},
							lineWidth: 2,
							shadow: false
						},
						grid: {
							drawGridlines: true,
							gridLineColor: '#f1f1f1',
							gridLineWidth: 1,
							borderWidth: 1,
							shadow: false,
							background: '#fafafa',
							borderColor: '#ffffff'
						},
						axes: {
							xaxis: {
								renderer: $.jqplot.CategoryAxisRenderer
							},
							yaxis: {
								padMin: 0
							}
						},
						legend: {
							show: true,
							location: 'nw',
							placement: 'inside',
							labels: [ <?php echo implode( ',', $engineLabels ); ?> ]
						}
					});

					<?php } ?>
				});
			</script>

			<div class="swp-group swp-stats swp-stats-4">

				<h2><?php _e( 'Popular Searches', $this->textDomain ); ?></h2>

				<div class="swp-stat postbox swp-meta-box metabox-holder">
					<h3 class="hndle"><span><?php _e( 'Today', $this->textDomain ); ?></span></h3>
					<div class="inside">
						<?php
						$sql = "
							SELECT {$prefix}swp_log.query, count({$prefix}swp_log.query) AS searchcount
							FROM {$prefix}swp_log
							WHERE tstamp > DATE_SUB(NOW(), INTERVAL 1 DAY)
							AND {$prefix}swp_log.event = 'search'
							AND {$prefix}swp_log.query <> ''
							GROUP BY {$prefix}swp_log.query
							ORDER BY searchcount DESC
							LIMIT 10
						";

						$searchCounts = $wpdb->get_results(
							$sql
						);
						?>
						<?php if( is_array( $searchCounts ) && !empty( $searchCounts ) ) : ?>
							<table>
								<thead>
								<tr>
									<th><?php _e( 'Query', $this->textDomain ); ?></th>
									<th><?php _e( 'Searches', $this->textDomain ); ?></th>
								</tr>
								</thead>
								<tbody>
								<?php foreach( $searchCounts as $searchCount ) : ?>
									<tr>
										<td><?php echo $searchCount->query; ?></td>
										<td><?php echo $searchCount->searchcount; ?></td>
									</tr>
								<?php endforeach; ?>
								</tbody>
							</table>
						<?php else: ?>
							<p><?php _e( 'There have been no searches today.', $this->textDomain ); ?></p>
						<?php endif; ?>
					</div>
				</div>

				<div class="swp-stat postbox swp-meta-box metabox-holder">
					<h3 class="hndle"><span><?php _e( 'Week', $this->textDomain ); ?></span></h3>
					<div class="inside">
						<?php
						$sql = "
							SELECT {$prefix}swp_log.query, count({$prefix}swp_log.query) AS searchcount
							FROM {$prefix}swp_log
							WHERE tstamp > DATE_SUB(NOW(), INTERVAL 7 DAY)
							AND {$prefix}swp_log.event = 'search'
							AND {$prefix}swp_log.query <> ''
							GROUP BY {$prefix}swp_log.query
							ORDER BY searchcount DESC
							LIMIT 10
						";

						$searchCounts = $wpdb->get_results(
							$sql
						);
						?>
						<?php if( is_array( $searchCounts ) && !empty( $searchCounts ) ) : ?>
							<table>
								<thead>
								<tr>
									<th><?php _e( 'Query', $this->textDomain ); ?></th>
									<th><?php _e( 'Searches', $this->textDomain ); ?></th>
								</tr>
								</thead>
								<tbody>
								<?php foreach( $searchCounts as $searchCount ) : ?>
									<tr>
										<td><?php echo $searchCount->query; ?></td>
										<td><?php echo $searchCount->searchcount; ?></td>
									</tr>
								<?php endforeach; ?>
								</tbody>
							</table>
						<?php else: ?>
							<p><?php _e( 'There have been no searches within the past 7 days.', $this->textDomain ); ?></p>
						<?php endif; ?>
					</div>
				</div>

				<div class="swp-stat postbox swp-meta-box metabox-holder">
					<h3 class="hndle"><span><?php _e( 'Month', $this->textDomain ); ?></span></h3>
					<div class="inside">
						<?php
						$sql = "
							SELECT {$prefix}swp_log.query, count({$prefix}swp_log.query) AS searchcount
							FROM {$prefix}swp_log
							WHERE tstamp > DATE_SUB(NOW(), INTERVAL 30 DAY)
							AND {$prefix}swp_log.event = 'search'
							AND {$prefix}swp_log.query <> ''
							GROUP BY {$prefix}swp_log.query
							ORDER BY searchcount DESC
							LIMIT 10
						";

						$searchCounts = $wpdb->get_results(
							$sql
						);
						?>
						<?php if( is_array( $searchCounts ) && !empty( $searchCounts ) ) : ?>
							<table>
								<thead>
								<tr>
									<th><?php _e( 'Query', $this->textDomain ); ?></th>
									<th><?php _e( 'Searches', $this->textDomain ); ?></th>
								</tr>
								</thead>
								<tbody>
								<?php foreach( $searchCounts as $searchCount ) : ?>
									<tr>
										<td><?php echo $searchCount->query; ?></td>
										<td><?php echo $searchCount->searchcount; ?></td>
									</tr>
								<?php endforeach; ?>
								</tbody>
							</table>
						<?php else: ?>
							<p><?php _e( 'There have been no searches within the past 30 days.', $this->textDomain ); ?></p>
						<?php endif; ?>
					</div>
				</div>

				<div class="swp-stat postbox swp-meta-box metabox-holder">
					<h3 class="hndle"><span><?php _e( 'Year', $this->textDomain ); ?></span></h3>
					<div class="inside">
						<?php
						$sql = "
							SELECT {$prefix}swp_log.query, count({$prefix}swp_log.query) AS searchcount
							FROM {$prefix}swp_log
							WHERE tstamp > DATE_SUB(NOW(), INTERVAL 365 DAY)
							AND {$prefix}swp_log.event = 'search'
							AND {$prefix}swp_log.query <> ''
							GROUP BY {$prefix}swp_log.query
							ORDER BY searchcount DESC
							LIMIT 10
						";

						$searchCounts = $wpdb->get_results(
							$sql
						);
						?>
						<?php if( is_array( $searchCounts ) && !empty( $searchCounts ) ) : ?>
							<table>
								<thead>
								<tr>
									<th><?php _e( 'Query', $this->textDomain ); ?></th>
									<th><?php _e( 'Searches', $this->textDomain ); ?></th>
								</tr>
								</thead>
								<tbody>
								<?php foreach( $searchCounts as $searchCount ) : ?>
									<tr>
										<td><?php echo $searchCount->query; ?></td>
										<td><?php echo $searchCount->searchcount; ?></td>
									</tr>
								<?php endforeach; ?>
								</tbody>
							</table>
						<?php else: ?>
							<p><?php _e( 'There have been no searches within the past year.', $this->textDomain ); ?></p>
						<?php endif; ?>
					</div>
				</div>

			</div>

			<div class="swp-group swp-stats swp-stats-4">

				<h2><?php _e( 'Failed Searches', $this->textDomain ); ?></h2>

				<div class="swp-stat postbox swp-meta-box metabox-holder">
					<h3 class="hndle"><span><?php _e( 'Past 30 Days', $this->textDomain ); ?></span></h3>
					<div class="inside">
						<?php
						$sql = "
							SELECT {$prefix}swp_log.query, count({$prefix}swp_log.query) AS searchcount
							FROM {$prefix}swp_log
							WHERE tstamp > DATE_SUB(NOW(), INTERVAL 30 DAY)
							AND {$prefix}swp_log.event = 'search'
							AND {$prefix}swp_log.query <> ''
							AND {$prefix}swp_log.hits = 0
							GROUP BY {$prefix}swp_log.query
							ORDER BY searchcount DESC
							LIMIT 10
						";

						$searchCounts = $wpdb->get_results(
							$sql
						);
						?>
						<?php if( is_array( $searchCounts ) && !empty( $searchCounts ) ) : ?>
							<table>
								<thead>
								<tr>
									<th><?php _e( 'Query', $this->textDomain ); ?></th>
									<th><?php _e( 'Searches', $this->textDomain ); ?></th>
								</tr>
								</thead>
								<tbody>
								<?php foreach( $searchCounts as $searchCount ) : ?>
									<tr>
										<td><?php echo $searchCount->query; ?></td>
										<td><?php echo $searchCount->searchcount; ?></td>
									</tr>
								<?php endforeach; ?>
								</tbody>
							</table>
						<?php else: ?>
							<p><?php _e( 'There have been no failed searches within the past 30 days.', $this->textDomain ); ?></p>
						<?php endif; ?>
					</div>
				</div>

			</div>

			<script type="text/javascript">
				jQuery(document).ready(function($){
					$('.swp-stats').each(function(){
						var tallest = 0;
						$(this).find('.swp-stat > .inside').each(function(){
							if($(this).height()>tallest){
								tallest = $(this).height();
							}
						}).height(tallest);
					});
				});
			</script>

		</div>
	<?php
	}


	/**
	 * Completely truncates all index tables, removes all index-related options
	 *
	 * @since 1.0
	 */
	function purgeIndex()
	{
		global $wpdb;

		$prefix = $wpdb->prefix . SEARCHWP_DBPREFIX;
		$tables = array( 'cf', 'index', 'log', 'tax', 'terms' );

		foreach( $tables as $table )
			$wpdb->query( "TRUNCATE TABLE {$prefix}{$table}" );

		// remove all metadata flags
		$wpdb->delete( $wpdb->prefix . 'postmeta', array( 'meta_key' => '_' . SEARCHWP_PREFIX . 'indexed' ) );
		$wpdb->delete( $wpdb->prefix . 'postmeta', array( 'meta_key' => '_' . SEARCHWP_PREFIX . 'attempts' ) );
		$wpdb->delete( $wpdb->prefix . 'postmeta', array( 'meta_key' => '_' . SEARCHWP_PREFIX . 'skip' ) );
		$wpdb->delete( $wpdb->prefix . 'postmeta', array( 'meta_key' => '_' . SEARCHWP_PREFIX . 'review' ) );
		$wpdb->delete( $wpdb->prefix . 'postmeta', array( 'meta_key' => SEARCHWP_PREFIX . 'content' ) );

		delete_option( SEARCHWP_PREFIX . 'running' );
		delete_option( SEARCHWP_PREFIX . 'total' );
		delete_option( SEARCHWP_PREFIX . 'remaining' );
		delete_option( SEARCHWP_PREFIX . 'done' );
		delete_option( SEARCHWP_PREFIX . 'last_activity' );
		delete_option( SEARCHWP_PREFIX . 'initial' );
		delete_transient( 'searchwp' );
	}


	/**
	 * Activate license
	 *
	 * @return bool Whether the license was activated
	 * @since 1.0
	 */
	function activateLicense()
	{
		// listen for our activate button to be clicked
		if( isset( $_POST['edd_license_activate'] ) ) {

			// run a quick security check
			if( ! check_admin_referer( 'edd_swp_nonce', 'edd_swp_nonce' ) )
				return; // get out if we didn't click the Activate button

			// retrieve the license from the database
			$license = trim( get_option( SEARCHWP_PREFIX . 'license_key' ) );


			// data to send in our API request
			$api_params = array(
				'edd_action'=> 'activate_license',
				'license' 	=> $license,
				'item_name' => urlencode( EDD_SEARCHWP_ITEM_NAME ) // the name of our product in EDD
			);

			// Call the custom API.
			$response = wp_remote_get( add_query_arg( $api_params, EDD_SEARCHWP_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

			// make sure the response came back okay
			if ( is_wp_error( $response ) )
				return false;

			// decode the license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			// $license_data->license will be either "active" or "inactive"
			update_option( SEARCHWP_PREFIX . 'license_status', $license_data->license );

			return true;
		}

		return false;
	}


	/**
	 * Deactivate license
	 *
	 * @return bool
	 * @since 1.0
	 */
	function deactivateLicense()
	{
		// listen for our activate button to be clicked
		if( isset( $_POST['edd_license_deactivate'] ) ) {

			// run a quick security check
			if( ! check_admin_referer( 'edd_swp_nonce', 'edd_swp_nonce' ) )
				return; // get out if we didn't click the Activate button

			// retrieve the license from the database
			$license = trim( get_option( SEARCHWP_PREFIX . 'license_key' ) );


			// data to send in our API request
			$api_params = array(
				'edd_action'=> 'deactivate_license',
				'license' 	=> $license,
				'item_name' => urlencode( EDD_SEARCHWP_ITEM_NAME ) // the name of our product in EDD
			);

			// Call the custom API.
			$response = wp_remote_get( add_query_arg( $api_params, EDD_SEARCHWP_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

			// make sure the response came back okay
			if ( is_wp_error( $response ) )
				return false;

			// decode the license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			// $license_data->license will be either "deactivated" or "failed"
			if( $license_data->license == 'deactivated' )
				delete_option( SEARCHWP_PREFIX . 'license_status' );

			return true;
		}

		return false;
	}


	/**
	 * Output the markup for the license-specific settings page
	 *
	 * @since 1.0
	 */
	function licenseSettings()
	{
		?>
		<div class="wrap">
			<div id="icon-searchwp" class="icon32">
				<img src="<?php echo trailingslashit( $this->url ); ?>assets/images/searchwp@2x.png" alt="SearchWP" width="21" height="32" />
			</div>
			<h2><?php echo $this->pluginName . ' ' . __( 'License' ); ?></h2>
			<h3><?php _e( 'License Key', $this->textDomain ); ?></h3>
			<p><?php _e( 'Your license key was included in your purchase receipt.', $this->textDomain ); ?></p>
			<form method="post" action="options.php">
				<?php settings_fields( SEARCHWP_PREFIX . 'license' ); ?>
				<table class="form-table">
					<tbody>
					<tr valign="top">
						<th scope="row" valign="top">
							<?php _e(  'License Key' ); ?>
						</th>
						<td>
							<input id="<?php echo SEARCHWP_PREFIX; ?>license_key" name="<?php echo SEARCHWP_PREFIX; ?>license_key" type="text" class="regular-text" value="<?php esc_attr_e( $this->license ); ?>" />
							<label class="description" for="<?php echo SEARCHWP_PREFIX; ?>license_key"><?php _e( 'Enter your license key', $this->textDomain ); ?></label>
						</td>
					</tr>
					<?php if( false !== $this->license ) { ?>
						<tr valign="top">
							<th scope="row" valign="top">
								<?php _e( 'Activate License', $this->textDomain ); ?>
							</th>
							<td>
								<?php if( $this->status !== false && $this->status == 'valid' ) { ?>
									<span style="color:green;"><?php _e( 'Active' ); ?></span>
									<?php wp_nonce_field( 'edd_swp_nonce', 'edd_swp_nonce' ); ?>
									<input type="submit" class="button-secondary" name="edd_license_deactivate" value="<?php _e( 'Deactivate License', $this->textDomain ); ?>"/>
								<?php } else {
									wp_nonce_field( 'edd_swp_nonce', 'edd_swp_nonce' ); ?>
									<input type="submit" class="button-secondary" name="edd_license_activate" value="<?php _e( 'Activate License', $this->textDomain ); ?>"/>
								<?php } ?>
							</td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
				<?php submit_button(); ?>
				<p><a href="options-general.php?page=searchwp"><?php _e( 'Back to SearchWP Settings', $this->textDomain ); ?></a></p>
			</form>
		</div>
		<?php
	}


	/**
	 * Output the markup for the advanced settings page
	 *
	 * @since 1.0
	 */
	function advancedSettings()
	{
		// do we need to purge the index?
		$purged = false;
		if(
			( isset( $_REQUEST['nonce'] ) && wp_verify_nonce( $_REQUEST['nonce'], 'swpadvanced') ) &&
			( isset( $_REQUEST['action'] ) && wp_verify_nonce( $_REQUEST['action'], 'swppurgeindex') )
			&& current_user_can( 'manage_options' )
		)
		{
			$this->purgeIndex();
			$purged = true;
		}

		$nonce = isset( $_REQUEST['nonce'] ) ? $_REQUEST['nonce'] : '0';

		?>
		<div class="wrap">
			<div id="icon-searchwp" class="icon32">
				<img src="<?php echo trailingslashit( $this->url ); ?>assets/images/searchwp@2x.png" alt="SearchWP" width="21" height="32" />
			</div>
			<h2><?php echo $this->pluginName . ' ' . __( 'Advanced Settings' ); ?></h2>
			<?php if( $purged ) : $this->purgeIndex(); ?>
				<div id="setting-error-settings_updated" class="updated settings-error">
					<p><strong><?php _e( 'Index purged.', $this->textDomain ); ?></strong> <a href="options-general.php?page=searchwp"><?php _e( 'Initiate reindex', $this->textDomain ); ?></a></p></div>
			<?php endif; ?>
			<h3><?php _e( 'Purge index', $this->textDomain ); ?></h3>
			<p><?php _e( 'If you would like to <strong>completely wipe out the index and start fresh</strong>, you can do so.', $this->textDomain ); ?></p>
			<p>
				<a class="button" id="swp-purge-index" href="options-general.php?page=searchwp&amp;nonce=<?php echo $nonce; ?>&amp;action=<?php echo wp_create_nonce( 'swppurgeindex' ); ?>"><?php _e( 'Purge Index', $this->textDomain ); ?></a>
			</p>
			<p style="padding-top:50px;">
				<a class="button-primary" href="options-general.php?page=searchwp"><?php _e( 'Back to Settings', $this->textDomain ); ?></a>
			</p>
			<script type="text/javascript">
				jQuery(document).ready(function($){
					$('#swp-purge-index').click(function(){
						if( confirm( '<?php echo _e( "Are you SURE you want to delete the entire SearchWP index?", $this->textDomain ); ?>' ) ) {
							return confirm( '<?php echo _e( "Are you completely sure? THIS CAN NOT BE UNDONE!", $this->textDomain ); ?>' );
						}
						return false;
					});
				});
			</script>
		</div>
	<?php }


	/**
	 * Callback for our implementation of add_options_page. Displays our options screen.
	 *
	 * @uses wpdb
	 * @uses get_option to get saved SearchWP settings
	 * @since 1.0
	 */
	function optionsPage()
	{
		global $wpdb;

		// check to see if we should show the license activation
		if( isset( $_REQUEST['nonce'] ) && wp_verify_nonce( $_REQUEST['nonce'], 'swpadvanced') && current_user_can( 'manage_options' ) )
		{
			$this->advancedSettings();
			return;
		}

		// check to see if we should show the advanced settings screen
		if( isset( $_REQUEST['activate'] ) && wp_verify_nonce( $_REQUEST['activate'], 'swpactivate') )
		{
			$this->licenseSettings();
			return;
		}

		// retrieve the most commonly used custom field keys to include in the Custom Fields weight table select
		$this->keys = $wpdb->get_col( "
				SELECT meta_key, COUNT($wpdb->postmeta.meta_key) AS usecount
				FROM $wpdb->postmeta
				GROUP BY meta_key
				HAVING meta_key NOT LIKE '\_%'
				AND meta_key != '_" . SEARCHWP_PREFIX . "indexed'
				AND meta_key != '" . SEARCHWP_PREFIX . "content'
				AND meta_key != '_" . SEARCHWP_PREFIX . "needs_remote'
				ORDER BY usecount DESC
				LIMIT 25
			" );

		if( $this->keys )
			natcasesort( $this->keys );
		else
			$this->keys = array();

		// allow devs to filter this list
		$this->keys = apply_filters( 'searchwp_custom_field_keys', $this->keys );
		?>
		<div class="wrap">
			<div id="icon-searchwp" class="icon32">
				<img src="<?php echo trailingslashit( $this->url ); ?>assets/images/searchwp@2x.png" alt="SearchWP" width="21" height="32" />
			</div>
			<h2>
				<?php echo $this->pluginName . ' ' . __( 'Settings' ); ?>
				<?php if( false == $this->license ) { ?>
					<a class="button button-primary swp-activate-license" href="options-general.php?page=searchwp&amp;activate=<?php echo wp_create_nonce( 'swpactivate' ); ?>"><?php _e( 'Activate License', $this->textDomain ); ?></a>
				<?php } else { ?>
					<a class="button swp-manage-license" href="options-general.php?page=searchwp&amp;activate=<?php echo wp_create_nonce( 'swpactivate' ); ?>"><?php _e( 'Manage License', $this->textDomain ); ?></a>
				<?php } ?>
			</h2>
			<form action="options.php" method="post">

				<div class="swp-wp-settings-api">
					<?php do_settings_sections( $this->textDomain ); ?>
					<?php settings_fields( SEARCHWP_PREFIX . 'settings' ); ?>
				</div>

				<?php if( get_option( SEARCHWP_PREFIX . 'initial' ) && false == get_option( SEARCHWP_PREFIX . 'initial_notified' ) ) : ?>
					<div class="updated">
						<p><?php _e( 'Initial index has been built', $this->textDomain ); ?></p>
					</div>
					<?php update_option( SEARCHWP_PREFIX . 'initial_notified', true ); ?>
				<?php endif; ?>

				<?php $remainingPostsToIndex = intval( get_option( SEARCHWP_PREFIX . 'remaining' ) ); ?>
				<div class="updated settings-error swp-in-progress<?php if( empty( $remainingPostsToIndex ) ) : ?> swp-in-progress-done<?php endif; ?>">
					<div class="swp-progress-wrapper">
						<p class="swp-label"><?php _e( 'Indexing is', $this->textDomain ); ?> <span><?php _e( 'almost', $this->textDomain ); ?></span> <?php _e( 'complete', $this->textDomain ); ?> <a class="swp-tooltip" href="#swp-tooltip-progress">?</a></p>
						<div class="swp-tooltip-content" id="swp-tooltip-progress">
							<?php _e( 'This process is running in the background. You can leave this page and the index will continue to be built until completion.', $this->textDomain ); ?>
						</div>
						<div class="swp-progress-track">
							<div class="swp-progress-bar"></div>
						</div>
					</div>
				</div>

				<script type="text/html" id="tmpl-swp-custom-fields">
					<tr>
						<td>
							<select name="<?php echo SEARCHWP_PREFIX; ?>settings[engines][{{ swp.engine }}][{{ swp.postType }}][weights][cf][{{ swp.arrayFlag }}][metakey]">
								<option value="searchwp cf default"><?php _e( 'Any', $this->textDomain ); ?></option>
								<?php if( !empty( $this->keys ) ) : foreach( $this->keys as $key ) : ?>
									<option value="<?php echo $key; ?>"><?php echo $key; ?></option>
								<?php endforeach; endif; ?>
							</select>
							<a class="swp-delete" href="#">X</a>
						</td>
						<td><input type="number" min="-1" step="1" class="small-text" name="<?php echo SEARCHWP_PREFIX; ?>settings[engines][{{ swp.engine }}][{{ swp.postType }}][weights][cf][{{ swp.arrayFlag }}][weight]" value="1" /></td>
					</tr>
				</script>

				<div class="postbox swp-meta-box swp-default-engine metabox-holder swp-jqueryui">

					<h3 class="hndle"><span><?php _e( 'Default Search Engine', $this->textDomain ); ?></span></h3>

					<div class="inside">

						<p><?php _e( 'These settings will override WordPress default searches. You can customize which post types are included in search results, attributing specific weights to various content types within each post type.', $this->textDomain ); ?><a class="swp-tooltip" href="#swp-tooltip-overview">?</a></p>
						<div class="swp-tooltip-content" id="swp-tooltip-overview">
							<?php _e( "Only checked post types will be included in search results. If a post type isn't displayed, ensure <code>exclude_from_search</code> is set to false when registering it.", $this->textDomain ); ?>
						</div>
						<?php searchwpEngineSettingsTemplate( 'default' ); ?>

					</div>

				</div>

				<div class="postbox swp-meta-box metabox-holder swp-jqueryui">

					<h3 class="hndle"><span><?php _e( 'Supplemental Search Engines', $this->textDomain ); ?></span></h3>

					<div class="inside">

						<p><?php _e( 'Here you can build supplemental search engines to use in specific sections of your site. When used, the default search engine settings are completely ignored.', $this->textDomain ); ?><a class="swp-tooltip" href="#swp-tooltip-supplemental">?</a></p>
						<div class="swp-tooltip-content" id="swp-tooltip-supplemental">
							<?php _e( "Only checked post types will be included in search results. If a post type isn't displayed, ensure <code>exclude_from_search</code> is set to false when registering it.", $this->textDomain ); ?>
						</div>

						<script type="text/html" id="tmpl-swp-engine">
							<?php searchwpEngineSettingsTemplate( '{{swp.engine}}' ); ?>
						</script>

						<script type="text/html" id="tmpl-swp-supplemental-engine">
							<?php searchwpSupplementalEngineSettingsTemplate( '{{swp.engine}}' ); ?>
						</script>

						<div class="swp-supplemental-engines-wrapper">
							<ul class="swp-supplemental-engines">
								<?php if( isset( $this->settings['engines'] ) && is_array( $this->settings['engines'] ) && count( $this->settings['engines'] ) ) : ?>
									<?php foreach( $this->settings['engines'] as $engineFlag => $engine ) : if( isset( $engine['label'] ) && !empty( $engine['label'] ) ) : ?>
										<?php searchwpSupplementalEngineSettingsTemplate( $engineFlag, $engine['label'] ); ?>
									<?php endif; endforeach; ?>
								<?php endif; ?>
							</ul>
							<p><a href="#" class="button swp-add-supplemental-engine"><?php _e( 'Add New Supplemental Engine', $this->textDomain ); ?></a></p>
						</div>

					</div>

				</div>

				<div class="swp-settings-footer swp-group">
					<?php if( current_user_can( 'manage_options' ) ) : ?>
						<p class="swp-settings-advanced"><a href="options-general.php?page=searchwp&amp;nonce=<?php echo wp_create_nonce( 'swpadvanced' ); ?>"><?php _e( 'Advanced', $this->textDomain ); ?></a></p>
					<?php endif; ?>
					<?php submit_button(); ?>
				</div>

			</form>
		</div>
	<?php }


	/**
	 * Register our settings with WordPress
	 *
	 * @uses add_settings_section as per the WordPress Settings API
	 * @uses add_settings_field as per the WordPress Settings API
	 * @uses register_setting as per the WordPress Settings API
	 * @since 1.0
	 */
	function initSettings()
	{
		add_settings_section(
			SEARCHWP_PREFIX . 'settings',
			'SearchWP Settings',
			array( $this, 'settingsCallback' ),
			$this->textDomain
		);

		add_settings_field(
			SEARCHWP_PREFIX . 'settings_field',
			'Settings',
			array( $this, 'settingsFieldCallback' ),
			$this->textDomain,
			SEARCHWP_PREFIX . 'settings'
		);

		register_setting(
			SEARCHWP_PREFIX . 'settings',
			SEARCHWP_PREFIX . 'settings',
			array( $this, 'validateSettings' )
		);

		// licensing
		register_setting(
			SEARCHWP_PREFIX . 'license',
			SEARCHWP_PREFIX . 'license_key',
			array( $this, 'sanitizeLicense' )
		);
	}


	/**
	 * Set up WP cron job for maintenance actions
	 *
	 * @since 1.0
	 */
	function scheduleMaintenance()
	{
		if( !wp_next_scheduled( 'swp_maintenance' ) )
			wp_schedule_event( time(), 'daily', 'swp_maintenance');

		if( !wp_next_scheduled( 'swp_indexer' ) )
			wp_schedule_event( time(), 'five_minutes', 'swp_cron_indexer' );
	}


	/**
	 * Too keep an eye on the initial index process, we're going to set up a five minute
	 * interval in WP cron
	 *
	 * @param $schedules
	 * @return mixed
	 * @since 1.0
	 */
	function addCustomCronInterval( $schedules )
	{
		// only add this interval if the initial index has not been completed
		if( !isset( $schedules['five_minutes'] ) && !get_option( SEARCHWP_PREFIX . 'initial' ) )
		{
			$schedules['five_minutes'] = array(
				'interval' => 60 * 5,
				'display' => __( 'Every five minutes' )
			);
		}
		return $schedules;
	}


	/**
	 * Callback to WordPress' hourly cron job
	 *
	 * @since 1.0
	 */
	function doCron()
	{
		// if the initial index hasn't been completed, we're going to ping the indexer
		if( !get_option( SEARCHWP_PREFIX . 'initial' ) )
		{
			// fire off a request to the index process
			update_option( SEARCHWP_PREFIX . 'running', false );
			do_action( 'searchwp_log', 'Request index (cron)' );
			$this->triggerIndex();
		}
	}


	/**
	 * Perform periodic maintenance
	 *
	 * @return bool
	 * @since 1.0
	 */
	function doMaintenance()
	{
		global $wp_version;

		$license = trim( get_option( SEARCHWP_PREFIX . 'license_key' ) );

		$api_params = array(
			'edd_action' 	=> 'check_license',
			'license' 		=> $license,
			'item_name' 	=> urlencode( EDD_SEARCHWP_ITEM_NAME )
		);

		$response = wp_remote_get( add_query_arg( $api_params, EDD_SEARCHWP_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

		if ( is_wp_error( $response ) )
			return false;

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		if( $license_data->license != 'valid' )
			delete_option( SEARCHWP_PREFIX . 'license_status' );

		return true;
	}


	/**
	 * Sanitize the license
	 *
	 * @param $new
	 * @return mixed
	 * @since 1.0
	 */
	function sanitizeLicense( $new )
	{
		$old = get_option( SEARCHWP_PREFIX . 'license_key' );

		if( $old && $old != $new )
			delete_option( SEARCHWP_PREFIX . 'license_status' ); // new license has been entered, so must reactivate

		return $new;
	}


	/**
	 * Callback from our call to register_setting() in $this->initSettings
	 *
	 * @param $input array The submitted $_POST data
	 * @return mixed array Validated array of settings
	 * @since 1.0
	 */
	function validateSettings( $input )
	{
		$validSettings = array();
		$validCategories = array( 'engines', );		// TODO: integrate license storage

		// make sure the input is an array
		if( is_array( $input ) )
		{
			// sift through our settings category
			foreach( $input as $category => $categorySettings )
			{
				// make sure the array key is sanitized
				$sanitizedCategory = sanitize_key( $category );
				$validSettings[$sanitizedCategory] = array();

				// only proceed if we have a valid settings category
				if( in_array( $sanitizedCategory, $validCategories ) )
				{
					// we're going to first handle any core settings
					switch( $sanitizedCategory )
					{
						case 'engines':
							foreach( $categorySettings as $engineName => $engineSettings )
							{
								$sanitizedEngineName = empty( $engineSettings['label'] ) ? sanitize_key( $engineName ) : str_replace( '-', '_', sanitize_title( $engineSettings['label'] ) );

								while( isset( $validSettings[$sanitizedCategory][$sanitizedEngineName] ) )
									$sanitizedEngineName .= '_copy';

								$validSettings[$sanitizedCategory][$sanitizedEngineName] = $this->sanitizeEngineSettings( $engineSettings );

								if( !empty( $engineSettings['label'] ) )
									$validSettings[$sanitizedCategory][$sanitizedEngineName]['label'] = sanitize_text_field( $engineSettings['label'] );
							}
							break;
					}

					// TODO: accommodate settings implemented by extensions
				}
			}
		}

		return $validSettings;
	}


	/**
	 * Make sure the submitted engine settings match expectations
	 *
	 * @param array $engineSettings
	 * @return array
	 * @since 1.0
	 */
	function sanitizeEngineSettings( $engineSettings = array() )
	{
		$validEngineSettings = array();

		if( is_array( $engineSettings ) )
		{
			foreach( $engineSettings as $postType => $postTypeSettings )
			{
				if( in_array( $postType, $this->postTypes ) )
				{
					$validEngineSettings[$postType] = array();

					// store a proper 'enabled' setting
					$validEngineSettings[$postType]['enabled'] = isset( $postTypeSettings['enabled'] ) && $postTypeSettings['enabled'] ? true : false;

					// store proper weights
					if( isset( $postTypeSettings['weights'] ) && is_array( $postTypeSettings['weights'] ) )
					{
						$validEngineSettings[$postType]['weights'] = array();
						foreach( $postTypeSettings['weights'] as $postTypeWeightKey => $weight )
						{
							if( in_array( $postTypeWeightKey, $this->validTypes ) )
							{
								if( !is_array( $weight ) )
								{
									$weight = intval( $weight );
									if( $weight < 0 ) $weight = 0;
									$validEngineSettings[$postType]['weights'][$postTypeWeightKey] = $weight;
								}
								else
								{
									// it's either a taxonomy or custom field, comprised of multiple weights
									$validEngineSettings[$postType]['weights'][$postTypeWeightKey] = array();
									foreach( $weight as $contentName => $subweight ) // could just check to see if $contentName is 'tax' or 'cf'...
									{
										if( !is_array( $subweight ) )
										{
											// taxonomy
											$weightKey = sanitize_key( $contentName );
											$subweight = intval( $subweight );
											if( $subweight < 0 ) $subweight = 0;
											$validEngineSettings[$postType]['weights'][$postTypeWeightKey][$weightKey] = $subweight;
										}
										else
										{
											// custom field
											$customFieldFlag = sanitize_key( $contentName );
											$weight = intval( $subweight['weight'] );
											if( $weight < 0 ) $weight = 0;
											if( isset( $subweight['metakey'] ) && isset( $subweight['weight'] ) )
											{
												$validEngineSettings[$postType]['weights'][$postTypeWeightKey][$customFieldFlag] = array(
													'metakey' 	=> sanitize_key( $subweight['metakey'] ),
													'weight'	=> $weight
												);
											}
										}
									}
								}
							}
						}
					}

					// dynamically add our taxonomies to valid options array
					$taxonomies = get_object_taxonomies( $postType );
						if( is_array( $taxonomies ) && count( $taxonomies ) )
							foreach( $taxonomies as $taxonomy )
							{
								$taxonomy = get_taxonomy( $taxonomy );
								$this->validOptions[] = 'exclude_' . $taxonomy->name;
							}

					// store proper options
					if( isset( $postTypeSettings['options'] ) && is_array( $postTypeSettings['options'] ) )
					{
						foreach( $postTypeSettings['options'] as $engineOptionName => $engineOptionValue )
						{
							if( in_array( $engineOptionName, $this->validOptions ) )
							{
								if( is_string( $engineOptionValue ) )
								{
									$validEngineSettings[$postType]['options'][$engineOptionName] = sanitize_text_field( $engineOptionValue );
								}
								elseif( is_array( $engineOptionValue ) )
								{
									$validEngineSettings[$postType]['options'][$engineOptionName] = sanitize_text_field( implode( ',', $engineOptionValue ) );
								}
								else
								{
									$validEngineSettings[$postType]['options'][$engineOptionName] = serialize( $engineOptionValue );
								}
							}
						}
					}
				}
			}
		}

		return $validEngineSettings;
	}


	/**
	 * Callback from our call to add_settings_section() in $this->initSettings
	 *
	 * @since 1.0
	 */
	function settingsCallback()
	{

	}


	/**
	 * Callback from our call to add_settings_field() in $this->initSettings. Outputs our (hidden) input field to
	 * accommodate the Settings API
	 *
	 * @since 1.0
	 */
	function settingsFieldCallback()
	{ ?>
		<input type="text" name="<?php echo SEARCHWP_PREFIX; ?>settings" id="<?php echo SEARCHWP_PREFIX; ?>settings" value="SearchWP" />
	<?php }


	function purgePostViaEdit( $post_id )
	{
		if( !isset( $this->purgeQueue[$post_id] ) )
		{
			$this->purgeQueue[$post_id] = $post_id;
			do_action( 'searchwp_log', 'purgePostViaEdit() ' . $post_id );
		}
		else
		{
			do_action( 'searchwp_log', 'Prevented duplicate purge purgePostViaEdit() ' . $post_id );
		}
	}


	/**
	 * Removes all record of a post and it's content from the index and triggers a reindex
	 *
	 * @param $post_id
	 * @return bool
	 */
	function purgePost( $post_id )
	{
		global $wpdb;

		// make sure current user is allowed to edit this post and that it's not a revision
		if( !current_user_can( 'edit_post', $post_id ) || wp_is_post_revision( $post_id ) ) return false;

		do_action( 'searchwp_log', 'purgePost() ' . $post_id );
		$this->purgeQueue[$post_id] = $post_id;

		$wpdb->delete( $wpdb->prefix . SEARCHWP_DBPREFIX . 'index', array( 'post_id' => $post_id ), array( '%d' ) );
		$wpdb->delete( $wpdb->prefix . SEARCHWP_DBPREFIX . 'tax', array( 'post_id' => $post_id ), array( '%d' ) );
		$wpdb->delete( $wpdb->prefix . SEARCHWP_DBPREFIX . 'cf', array( 'post_id' => $post_id ), array( '%d' ) );
		delete_post_meta( $post_id, '_' . SEARCHWP_PREFIX . 'indexed' );

		return true;
	}


	/**
	 * Callback for actions related to comments changing
	 *
	 * @uses $this->purgePost to clear out the post content from the index and trigger a reindex entirely
	 * @param $id
	 */
	function purgePostViaComment( $id )
	{
		$comment = get_comment( $id );
		$object_id = $comment->comment_post_ID;
		if( !isset( $this->purgeQueue[$object_id] ) )
		{
			$this->purgeQueue[$object_id] = $object_id;
			do_action( 'searchwp_log', 'purgePostViaComment() ' . $object_id );
		}
		else
		{
			do_action( 'searchwp_log', 'Prevented duplicate purge purgePostViaComment() ' . $object_id );
		}
	}


	function purgePostViaTerm( $object_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids )
	{
		// prevent repeated purging of the same post
		if( !isset( $this->purgeQueue[$object_id] ) )
		{
			$this->purgeQueue[$object_id] = $object_id;
			do_action( 'searchwp_log', 'purgePostViaTerm() ' . $object_id );
		}
		else
		{
			do_action( 'searchwp_log', 'Prevented duplicate purge purgePostViaTerm()' . $object_id );
		}
	}


	/**
	 * Trigger a reindex
	 */
	private function triggerReindex()
	{
		// check capabilities
		if(
			!current_user_can( 'edit_posts' ) &&
			!current_user_can( 'edit_pages' ) &&
			!current_user_can( 'manage_options' )
		)
		{
			return false;
		}

		do_action( 'searchwp_log', 'Request index (reindex)' );
		$this->triggerIndex();

		return true;
	}


	/**
	 * Enable SearchWP textdomain
	 *
	 * @since 1.0
	 */
	function textdomain()
	{
		$locale = apply_filters( 'searchwp', get_locale(), $this->textDomain );
		$mofile = WP_LANG_DIR . '/' . $this->textDomain . '/' . $this->textDomain . '-' . $locale . '.mo';

		if( file_exists( $mofile ) )
			load_textdomain( $this->textDomain, $mofile );
		else
			load_plugin_textdomain( $this->textDomain, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}


	/**
	 * Callback for plugin activation, outputs admin notice
	 *
	 * @since 1.0
	 */
	function activation()
	{
		if( false == get_option( SEARCHWP_PREFIX . 'activated' ) )
		{
			add_option( SEARCHWP_PREFIX . 'activated', 1 );
			?>
				<div class="updated">
					<p><?php _e( 'SearchWP has been activated and the index is now being built. <a href="options-general.php?page=searchwp">View progress and settings</a>', $this->textDomain ); ?></p>
				</div>
			<?php

			// trigger the initial indexing
			do_action( 'searchwp_log', 'Request index (activation)' );
			$this->triggerIndex();
		}
	}


	/**
	 * Register meta box for document content textarea
	 *
	 * @since 1.0
	 */
	function documentContentMetaBox()
	{
		add_meta_box(
			'searchwp_doc_content',
			__( 'SearchWP File Content', $this->textDomain ),
			array( $this, 'documentContentMetaBoxMarkup' ),
			'attachment'
		);
	}


	/**
	 * Output the markup for the document content meta box
	 *
	 * @param $post
	 * @since 1.0
	 */
	function documentContentMetaBoxMarkup( $post )
	{
		$existingContent = get_post_meta( $post->ID, SEARCHWP_PREFIX . 'content', true );
		wp_nonce_field( 'searchwpdoc', 'searchwp_doc_nonce' );

		$supportedMimeTypes = array(
			'text/plain',
			'text/csv',
			'text/tab-separated-values',
			'text/calendar',
			'text/richtext',
			'text/css',
			'text/html',
			'application/pdf',
			'application/msword',
			'application/vnd.ms-powerpoint',
			'application/vnd.ms-write',
			'application/vnd.ms-excel',
			'application/vnd.ms-access',
			'application/vnd.ms-project',
			'application/vnd.openxmlformats-officedocument.wordprocessingml. document',
			'application/vnd.ms-word.document.macroEnabled.12',
			'application/vnd.openxmlformats-officedocument.wordprocessingml. template',
			'application/vnd.ms-word.template.macroEnabled.12',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'application/vnd.ms-excel.sheet.macroEnabled.12',
			'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
			'application/vnd.ms-excel.template.macroEnabled.12',
			'application/vnd.ms-excel.addin.macroEnabled.12',
			'application/vnd.openxmlformats-officedocument.presentationml. presentation',
			'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
			'application/vnd.openxmlformats-officedocument.presentationml. slideshow',
			'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
			'application/vnd.openxmlformats-officedocument.presentationml.template',
			'application/vnd.ms-powerpoint.template.macroEnabled.12',
			'application/vnd.ms-powerpoint.addin.macroEnabled.12',
			'application/vnd.openxmlformats-officedocument.presentationml.slide',
			'application/vnd.ms-powerpoint.slide.macroEnabled.12',
			'application/onenote',
			'application/vnd.oasis.opendocument.text',
			'application/vnd.oasis.opendocument.presentation',
			'application/vnd.oasis.opendocument.spreadsheet',
			'application/vnd.oasis.opendocument.graphics',
			'application/vnd.oasis.opendocument.chart',
			'application/vnd.oasis.opendocument.database',
			'application/vnd.oasis.opendocument.formula',
			'application/wordperfect',
			'application/vnd.apple.keynote',
			'application/vnd.apple.numbers',
			'application/vnd.apple.pages',
		);

		if( in_array( $post->post_mime_type, $supportedMimeTypes ) ) : ?>
			<p><?php _e( 'The content below will be indexed for this file. If you are experiencing unexpected search results, ensure accuracy here.', $this->textDomain ); ?></p>
			<textarea style="display:block;width:100%;height:300px;" name="searchwp_doc_content"><?php if( $existingContent ) echo esc_textarea( $existingContent ); ?></textarea>
			<div style="display:none !important;overflow:hidden !important;">
				<textarea style="display:block;width:100%;height:300px;" name="searchwp_doc_content_original"><?php if( $existingContent ) echo esc_textarea( $existingContent ); ?></textarea>
			</div>
		<?php else: ?>
			<p><?php _e( 'Only plain text files, PDFs, and office documents are supported at this time.', $this->textDomain ); ?></p>
		<?php endif;
	}


	/**
	 * Callback fired when saving documents, saves document content
	 *
	 * @param $post_id
	 * @since 1.0
	 */
	function documentContentSave( $post_id )
	{
		// check capability
		if( 'attachment' == $_REQUEST['post_type'] )
		{
			if( ! current_user_can( 'edit_page', $post_id ) )
				return;
		}
		else
		{
			if( ! current_user_can( 'edit_post', $post_id ) )
				return;
		}

		// check intent
		if( !isset( $_POST['searchwp_doc_nonce'] ) || ! wp_verify_nonce( $_POST['searchwp_doc_nonce'], 'searchwpdoc' ) )
			return;

		$originalContent 	= isset( $_POST['searchwp_doc_content_original'] ) ? sanitize_text_field( $_POST['searchwp_doc_content_original'] ) : '';
		$editedContent 		= isset( $_POST['searchwp_doc_content'] ) ? sanitize_text_field( $_POST['searchwp_doc_content'] ) : '';
		$alreadySkipped 	= get_post_meta( $post_id, '_' . SEARCHWP_PREFIX . 'skip_doc_processing', true );

		// check to see if the doc content is different than what it was
		if( $alreadySkipped || ( $originalContent != $editedContent ) )
		{
			update_post_meta( $post_id, '_' . SEARCHWP_PREFIX . 'skip_doc_processing', true );
			update_post_meta( $post_id, SEARCHWP_PREFIX . 'content', $editedContent );
		}

	}

}


/**
 * Instantiator for SearchWP
 *
 * @return SearchWP SearchWP singleton
 * @since 1.0
 */
function searchWPinit()
{
	global $searchwp;

	$searchwp = SearchWP::instance();
	return $searchwp;
}

add_action( 'wp_loaded', 'searchWPinit' );
