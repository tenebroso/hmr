<?php

if( !defined( 'ABSPATH' ) ) die();

/**
 * Singleton reference
 */
global $searchwp;


/**
 * Class SearchWPSearch performs search queries on the index
 */
class SearchWPSearch
{
	/**
	 * @var string Search engine name
	 * @since 1.0
	 */
	private $engine;

	/**
	 * @var array Terms to search for
	 * @since 1.0
	 */
	private $terms;

	/**
	 * @var mixed|void Stored SearchWP settings
	 * @since 1.0
	 */
	private $settings;

	/**
	 * @var int The page of results to work with
	 * @since 1.0
	 */
	private $page;

	/**
	 * @var int The number of posts per page
	 * @since 1.0
	 */
	private $postsPer;

	/**
	 * @var string The order in which results should be returned
	 * @since 1.0
	 */
	private $order = 'DESC';

	public $foundPosts 	= 0;
	public $maxNumPages = 0;
	public $postIDs 	= array();
	public $posts;


	/**
	 * Constructor
	 *
	 * @param array $args
	 * @since 1.0
	 */
	function __construct( $args = array() )
	{
		$defaults = array(
			'terms' 					=> '',
			'engine' 					=> 'default',
			'page'						=> 1,
			'posts_per_page' 	=> intval( get_option( 'posts_per_page' ) ),
			'order'						=> $this->order,
			'load_posts'			=> true,
		);

		// process our arguments
		$args 			= wp_parse_args( $args, $defaults );
		$searchwp 	= SearchWP::instance();

		// if we have a valid engine, perform the query
		if( $searchwp->isValidEngine( $args['engine'] ) )
		{
			$terms 		= $searchwp->sanitizeTerms( $args['terms'] );
			$engine 	= $args['engine'];

			if( strtoupper( $args['order'] ) != 'DESC' && strtoupper( $args['order'] ) != 'ASC' )
				$args['order'] = 'DESC';

			// filter the terms just before querying
			$terms = apply_filters( 'searchwp_pre_search_terms', $terms );

			$this->terms 			= $terms;
			$this->engine 		= $engine;
			$this->settings 	= get_option( SEARCHWP_PREFIX . 'settings' );
			$this->page 			= intval( $args['page'] );
			$this->postsPer 	= intval( $args['posts_per_page'] );
			$this->order    	= $args['order'];
			$this->load_posts = is_bool( $args['load_posts'] ) ? $args['load_posts'] : true;

			// perform our query
			$this->posts = $this->query();
		}

	}


	/**
	 * Perform a query on the index
	 *
	 * @return array Posts returned by the query
	 * @since 1.0
	 */
	function query()
	{
		do_action( 'searchwp_before_query_index', array(
			'terms' 		=> $this->terms,
			'engine' 		=> $this->engine,
			'settings' 	=> $this->settings,
			'page' 			=> $this->page,
			'postsPer' 	=> $this->postsPer
		) );

		$this->queryForPostIDs();

		do_action( 'searchwp_after_query_index', array(
			'terms' 		=> $this->terms,
			'engine' 		=> $this->engine,
			'settings' 	=> $this->settings,
			'page' 			=> $this->page,
			'postsPer' 	=> $this->postsPer
		) );

		// facilitate filtration of returned results
		$this->postIDs = apply_filters( 'searchwp_query_results', $this->postIDs, array(
			'terms' 		=> $this->terms,
			'engine' 		=> $this->engine,
			'settings' 	=> $this->settings,
			'page' 			=> $this->page,
			'postsPer' 	=> $this->postsPer
		) );

		if( empty( $this->postIDs ) )
			return array();

		// our post IDs will have already been filtered based on the engine settings, so we want to query for
		// anything that matches our post IDs
		$args = array(
			'posts_per_page' 	=> count( $this->postIDs ),
			'post_type' 			=> 'any',
			'post_status' 		=> 'any',	// we've already filtered our post statuses in the original query
			'post__in' 				=> $this->postIDs,
			'orderby'					=> 'post__in'
		);

		if ( $this->load_posts && true === apply_filters( 'searchwp_load_posts', true ) )
			$posts = get_posts( $args );
		else
			$posts = $this->postIDs;

		return $posts;
	}


	/**
	 * Dynamically generate SQL query based on engine settings and retrieve a weighted, ordered list of posts
	 *
	 * @return bool|array Post IDs found in the index
	 * @since 1.0
	 */
	function queryForPostIDs()
	{
		global $wpdb;

		$stemmer = new SearchWPStemmer();

		// check to make sure there are settings for the current engine
		if( !isset( $this->settings['engines'][$this->engine] ) && is_array( $this->settings['engines'][$this->engine] ) )
			return false;

		// pull out our engine-specific settings
		$engineSettings = $this->settings['engines'][$this->engine];

		// allow filtration of settings at runtime
		$engineSettings = apply_filters( "searchwp_engine_settings_{$this->engine}", $engineSettings, $this->terms );

		// check to make sure that all post types in the settings are still in fact registered and active
		$searchwp = SearchWP::instance();
		if( is_array( $searchwp->postTypes ) )
			foreach( $engineSettings as $postType => $postTypeSettings )
				if( !in_array( $postType, $searchwp->postTypes ) )
					unset( $engineSettings[$postType] );

		// check to make sure that at least one post type is enabled for this engine
		$okToSearch = false;
		if( is_array( $engineSettings ) )
			foreach( $engineSettings as $postType => $postTypeWeights )
				if( isset( $postTypeWeights['enabled'] ) && $postTypeWeights['enabled'] == true )
					$okToSearch = true;

		if( !$okToSearch )
			return false;

		// let the fun begin
		$prefix = $wpdb->prefix;

		// we're going to exclude entered IDs for the query as a whole
		// need to get these IDs early because if an attributed post ID is excluded we need to omit it from
		// the query entirely
		$excludeIDs = array();
		foreach( $engineSettings as $postType => $postTypeWeights )
		{
			// store our exclude clause
			$postTypeExcludeIDs = ( isset( $postTypeWeights['options']['exclude'] ) && !empty( $postTypeWeights['options']['exclude'] ) ) ? explode( ',', $postTypeWeights['options']['exclude'] ) : array();

			if( !empty( $postTypeExcludeIDs ) )
				foreach( $postTypeExcludeIDs as $postTypeExcludeID )
					$excludeIDs[] = intval( $postTypeExcludeID );

		}

		// pull any excluded IDs based on taxonomy term
		foreach( $engineSettings as $postType => $postTypeWeights )
		{
			$taxonomies = get_object_taxonomies( $postType );
			if( is_array( $taxonomies ) && count( $taxonomies ) )
				foreach( $taxonomies as $taxonomy )
				{
					$taxonomy = get_taxonomy( $taxonomy );
					if( isset( $postTypeWeights['options']['exclude_' . $taxonomy->name] ) )
					{
						$excludedTerms = explode( ',', $postTypeWeights['options']['exclude_' . $taxonomy->name] );

						if( !is_array( $excludedTerms ) )
							$excludedTerms = array( intval( $excludedTerms ) );

						if( !empty( $excludedTerms ) )
							foreach( $excludedTerms as $excludedKey => $excludedValue )
								$excludedTerms[$excludedKey] = intval( $excludedValue );

						// determine which post(s) have this term
						$args = array(
							'posts_per_page' 	=> -1,
							'fields'					=> 'ids',
							'post_type' 			=> $postType,
							'tax_query' 			=> array(
								array(
									'taxonomy' 		=> $taxonomy->name,
									'field' 			=> 'id',
									'terms' 			=> $excludedTerms
								)
							)
						);

						$excludedByTerm = new WP_Query( $args );

						$excludeIDs = array_merge( $excludeIDs, $excludedByTerm->posts );
					}
				}
		}

		$excludeIDs = apply_filters( 'searchwp_exclude', $excludeIDs );

		$excludeSQL = ( !empty( $excludeIDs ) ) ? " AND {$prefix}posts.ID NOT IN (" . implode( ',', $excludeIDs ) . ")" : '';


		/**
		 * OPEN THE QUERY
		 */
		$sql = "SELECT SQL_CALC_FOUND_ROWS {$prefix}posts.ID AS post_id, {$prefix}posts.post_type AS post_type, {$prefix}posts.post_title AS post_title, \n";

		// sum our final weights per post type
		foreach( $engineSettings as $postType => $postTypeWeights )
		{
			if( isset( $postTypeWeights['enabled'] ) && $postTypeWeights['enabled'] == true )
			{
				$termCounter = 1;
				if( empty( $postTypeWeights['options']['attribute_to'] ) )
				{
					foreach( $this->terms as $term )
					{
						$sql .= "COALESCE(term{$termCounter}.`{$postType}weight`,0) + ";
						$termCounter++;
					}
				}
				else
				{
					foreach( $this->terms as $term )
					{
						$sql .= "COALESCE(term{$termCounter}.`{$postType}attr`,0) + ";
						$termCounter++;
					}
				}
				$sql = substr( $sql, 0, strlen( $sql ) - 2 );	// trim off the extra +
				$sql .= " AS `final{$postType}weight`, \n";
			}
		}


		// build our final, overall weight
		foreach( $engineSettings as $postType => $postTypeWeights )
		{
			if( isset( $postTypeWeights['enabled'] ) && $postTypeWeights['enabled'] == true )
			{
				$termCounter = 1;
				if( empty( $postTypeWeights['options']['attribute_to'] ) )
				{
					foreach( $this->terms as $term )
					{
						$sql .= "COALESCE(term{$termCounter}.`{$postType}weight`,0) + ";
						$termCounter++;
					}
				}
				else
				{
					foreach( $this->terms as $term )
					{
						$sql .= "COALESCE(term{$termCounter}.`{$postType}attr`,0) + ";
						$termCounter++;
					}
				}
			}
		}

		$sql = substr( $sql, 0, strlen( $sql ) - 2 );	// trim off the extra +
		$sql .= " AS finalweight FROM {$prefix}posts\n";

		/**
		 * BEGIN LOOP THROUGH EACH SUBMITTED TERM
		 */
		$termCounter = 1;
		foreach( $this->terms as $term )
		{
			$sql .= "LEFT JOIN (\n";

			// our final query cap
			$sql .= "SELECT {$prefix}posts.ID AS post_id, {$prefix}posts.post_type AS post_type, {$prefix}posts.post_title AS post_title \n";

			// implement our post type weight column
			foreach( $engineSettings as $postType => $postTypeWeights )
				if( isset( $postTypeWeights['enabled'] ) && $postTypeWeights['enabled'] == true && empty( $postTypeWeights['options']['attribute_to'] ) )
					$sql .= ", COALESCE(`{$postType}weight`,0) AS `{$postType}weight` \n";

			// implement our post type attributed weight column
			foreach( $engineSettings as $postType => $postTypeWeights )
				if( isset( $postTypeWeights['enabled'] ) && $postTypeWeights['enabled'] == true && !empty( $postTypeWeights['options']['attribute_to'] ) )
				{
					$attributedTo = intval( $postTypeWeights['options']['attribute_to'] );
					// make sure we're not excluding the attributed post id
					if( !in_array( $attributedTo, $excludeIDs ) )
						$sql .= ", COALESCE(`{$postType}attr`,0) as `{$postType}attr` \n";
				}

			$sql .= " , ";

			// concatenate our total weight with post type weight
			foreach( $engineSettings as $postType => $postTypeWeights )
				if( isset( $postTypeWeights['enabled'] ) && $postTypeWeights['enabled'] == true && empty( $postTypeWeights['options']['attribute_to'] ) )
					$sql .= " COALESCE(`{$postType}weight`,0) +";

			// concatenate our total weight with our attributed weight
			foreach( $engineSettings as $postType => $postTypeWeights )
				if( isset( $postTypeWeights['enabled'] ) && $postTypeWeights['enabled'] == true && !empty( $postTypeWeights['options']['attribute_to'] ) )
				{
					$attributedTo = intval( $postTypeWeights['options']['attribute_to'] );
					// make sure we're not excluding the attributed post id
					if( !in_array( $attributedTo, $excludeIDs ) )
						$sql .= " COALESCE(`{$postType}attr`,0) +";
				}


			$sql = substr( $sql, 0, strlen( $sql ) - 2 );	// trim off the extra +

			$sql .= " AS weight \n";
			$sql .= " FROM {$prefix}posts \n";

			// build our post type queries
			foreach( $engineSettings as $postType => $postTypeWeights )
			{
				if( isset( $postTypeWeights['enabled'] ) && $postTypeWeights['enabled'] == true )
				{
					// TODO: store our post format clause and integrate
					// TODO: store our post status clause and integrate

					$postTypeStatus = $postType == 'attachment' ? 'inherit' : 'publish';
					$statusSQL = "AND {$prefix}posts.post_status = '$postTypeStatus'";

					// determine whether we're stemming or not
					if( !isset( $postTypeWeights['options']['stem'] ) || empty( $postTypeWeights['options']['stem'] ) )
						$termWhere = " {$prefix}swp_terms.term = " . strtolower( $wpdb->prepare( '%s', $term ) );
					else
						$termWhere = " {$prefix}swp_terms.stem = " . strtolower( $wpdb->prepare( '%s', $stemmer->stem( $term ) ) );

					$titleWeight 	= isset( $postTypeWeights['weights']['title'] ) 	? intval( $postTypeWeights['weights']['title'] ) 	: 0;
					$slugWeight 	= isset( $postTypeWeights['weights']['slug'] ) 		? intval( $postTypeWeights['weights']['slug'] ) 	: 0;
					$contentWeight 	= isset( $postTypeWeights['weights']['content'] ) 	? intval( $postTypeWeights['weights']['content'] ) 	: 0;
					$commentWeight 	= isset( $postTypeWeights['weights']['comment'] ) 	? intval( $postTypeWeights['weights']['comment'] ) 	: 0;
					$excerptWeight 	= isset( $postTypeWeights['weights']['excerpt'] ) 	? intval( $postTypeWeights['weights']['excerpt'] ) 	: 0;

					$coalesceCustomFields = '0 +';
					if( isset( $postTypeWeights['weights']['cf'] ) && is_array( $postTypeWeights['weights']['cf'] ) && !empty( $postTypeWeights['weights']['cf'] ) )
					{
						$totalCustomFields = count( $postTypeWeights['weights']['cf'] );
						for( $i = 0; $i < $totalCustomFields; $i++ )
						{
							$coalesceCustomFields .= " COALESCE(cfweight" . $i . ",0) + ";
						}
					}
					$coalesceCustomFields = substr( $coalesceCustomFields, 0, strlen( $coalesceCustomFields) - 2 );

					$coalesceTaxonomies = '0 +';
					if( isset( $postTypeWeights['weights']['tax'] ) && is_array( $postTypeWeights['weights']['tax'] ) && !empty( $postTypeWeights['weights']['tax'] ) )
					{
						$totalTaxonomies = count( $postTypeWeights['weights']['tax'] );
						for( $i = 0; $i < $totalTaxonomies; $i++ )
						{
							$coalesceTaxonomies .= " COALESCE(taxweight" . $i . ",0) + ";
						}
					}
					$coalesceTaxonomies = substr( $coalesceTaxonomies, 0, strlen( $coalesceTaxonomies) - 2 );

					// do another left join if we're attributing
					if( isset( $postTypeWeights['options']['attribute_to'] ) && !empty( $postTypeWeights['options']['attribute_to'] ) )
					{
						$attributedTo = intval( $postTypeWeights['options']['attribute_to'] );

						// make sure we're not excluding the attributed post id
						if( !in_array( $attributedTo, $excludeIDs ) )
						{
							$sql .= "
							LEFT JOIN (
								SELECT {$prefix}posts.ID AS post_id,
									( {$prefix}swp_index.title * {$titleWeight} ) +
									( {$prefix}swp_index.slug * {$slugWeight} ) +
									( {$prefix}swp_index.content * {$contentWeight} ) +
									( {$prefix}swp_index.comment * {$commentWeight} ) +
									( {$prefix}swp_index.excerpt * {$excerptWeight} ) +
									{$coalesceCustomFields} +
									{$coalesceTaxonomies}
								AS `{$postType}attr`
								FROM {$prefix}swp_terms FORCE INDEX (termindex)
								LEFT JOIN {$prefix}swp_index ON {$prefix}swp_terms.id = {$prefix}swp_index.term
								LEFT JOIN {$prefix}posts ON {$prefix}swp_index.post_id = {$prefix}posts.ID
								WHERE {$termWhere}
								{$statusSQL}
								AND {$prefix}posts.post_type = '{$postType}'
								{$excludeSQL}
							) `attributed{$postType}` ON $attributedTo = {$prefix}posts.ID
							";
						}
					}
					else
					{
						// if it's an attachment and we want to attribute to the parent, we need to set that here
						$postColumn = isset( $postTypeWeights['options']['parent'] ) ? 'post_parent' : 'ID';
						$sql .= "
							LEFT JOIN (
								SELECT {$prefix}posts.{$postColumn} AS post_id,
									( {$prefix}swp_index.title * {$titleWeight} ) +
									( {$prefix}swp_index.slug * {$slugWeight} ) +
									( {$prefix}swp_index.content * {$contentWeight} ) +
									( {$prefix}swp_index.comment * {$commentWeight} ) +
									( {$prefix}swp_index.excerpt * {$excerptWeight} ) +
									{$coalesceCustomFields} +
									{$coalesceTaxonomies}
								AS `{$postType}weight`
								FROM {$prefix}swp_terms FORCE INDEX (termindex)
								LEFT JOIN {$prefix}swp_index ON {$prefix}swp_terms.id = {$prefix}swp_index.term
								LEFT JOIN {$prefix}posts ON {$prefix}swp_index.post_id = {$prefix}posts.ID
							";

						if( isset( $postTypeWeights['weights']['cf'] ) && is_array( $postTypeWeights['weights']['cf'] ) && !empty( $postTypeWeights['weights']['cf'] ) )
						{
							$i = 0;
							foreach( $postTypeWeights['weights']['cf'] as $postTypeCfRecord => $postTypeCf )
							{
								$cfWeight = intval( $postTypeCf['weight'] );
								$cfName = $postTypeCf['metakey'];

								$cfClause = '';
								if( $cfName != 'searchwp cf default' )
									$cfClause = " AND " . $prefix . "swp_cf.metakey = '" . $cfName . "' ";

								$sql .= "
									LEFT JOIN (
										SELECT {$prefix}swp_cf.post_id, SUM({$prefix}swp_cf.count * {$cfWeight}) AS cfweight{$i}
										FROM {$prefix}swp_terms FORCE INDEX (termindex)
										LEFT JOIN {$prefix}swp_cf ON {$prefix}swp_terms.id = {$prefix}swp_cf.term
										LEFT JOIN {$prefix}posts ON {$prefix}swp_cf.post_id = {$prefix}posts.ID
										WHERE {$termWhere}
										{$statusSQL}
										AND {$prefix}posts.post_type = '{$postType}'
										{$excludeSQL}
										{$cfClause}
										GROUP BY {$prefix}swp_cf.post_id
									) cfweights{$i} USING(post_id)
								";
								$i++;
							}
						}

						if( isset( $postTypeWeights['weights']['tax'] ) && is_array( $postTypeWeights['weights']['tax'] ) && !empty( $postTypeWeights['weights']['tax'] ) )
						{
							$i = 0;
							foreach( $postTypeWeights['weights']['tax'] as $postTypeTaxName => $postTypeTaxWeight )
							{
								$sql .= "
									LEFT JOIN (
										SELECT {$prefix}swp_tax.post_id, SUM({$prefix}swp_tax.count * {$postTypeTaxWeight}) AS taxweight{$i}
										FROM {$prefix}swp_terms FORCE INDEX (termindex)
										LEFT JOIN {$prefix}swp_tax ON {$prefix}swp_terms.id = {$prefix}swp_tax.term
										LEFT JOIN {$prefix}posts ON {$prefix}swp_tax.post_id = {$prefix}posts.ID
										WHERE {$termWhere}
										{$statusSQL}
										AND {$prefix}posts.post_type = '{$postType}'
										{$excludeSQL}
										AND {$prefix}swp_tax.taxonomy = '{$postTypeTaxName}'
										GROUP BY {$prefix}swp_tax.post_id
									) taxweights{$i} USING(post_id)
								";
								$i++;
							}
						}

						$sql .= "
								WHERE {$termWhere}
								{$statusSQL}
								AND {$prefix}posts.post_type = '{$postType}'
								{$excludeSQL}
								GROUP BY {$prefix}posts.ID

							) AS `{$postType}weights` ON `{$postType}weights`.post_id = {$prefix}posts.ID
						";
					}
				}
			}

			// make sure we're only getting posts with actual weight
			$sql .= " WHERE   ";

			foreach( $engineSettings as $postType => $postTypeWeights )
				if( isset( $postTypeWeights['enabled'] ) && $postTypeWeights['enabled'] == true && empty( $postTypeWeights['options']['attribute_to'] ) )
					$sql .= " COALESCE(`{$postType}weight`,0) +";

			foreach( $engineSettings as $postType => $postTypeWeights )
				if( isset( $postTypeWeights['enabled'] ) && $postTypeWeights['enabled'] == true && !empty( $postTypeWeights['options']['attribute_to'] ) )
				{
					$attributedTo = intval( $postTypeWeights['options']['attribute_to'] );
					// make sure we're not excluding the attributed post id
					if( !in_array( $attributedTo, $excludeIDs ) )
						$sql .= " COALESCE(`{$postType}attr`,0) +";
				}

			$sql = substr( $sql, 0, strlen( $sql ) - 2 );
			$sql .= " > 0 ";

			$sql .= "
					GROUP BY post_id
				";

			$sql .= " ) AS term{$termCounter} ON term{$termCounter}.post_id = {$prefix}posts.ID ";

			$termCounter++;
		}

		/**
		 * END LOOP THROUGH EACH SUBMITTED TERM
		 */


		// make sure we're only getting posts with actual weight
		$sql .= " WHERE   ";

		foreach( $engineSettings as $postType => $postTypeWeights )
		{
			if( isset( $postTypeWeights['enabled'] ) && $postTypeWeights['enabled'] == true )
			{
				$termCounter = 1;
				if( empty( $postTypeWeights['options']['attribute_to'] ) )
				{
					foreach( $this->terms as $term )
					{
						$sql .= "COALESCE(term{$termCounter}.`{$postType}weight`,0) + ";
						$termCounter++;
					}
				}
				else
				{
					foreach( $this->terms as $term )
					{
						$sql .= "COALESCE(term{$termCounter}.`{$postType}attr`,0) + ";
						$termCounter++;
					}
				}
			}
		}

		$sql = substr( $sql, 0, strlen( $sql ) - 2 );	// trim off the extra +
		$sql .= " > 0 ";

		$start = intval( ( $this->page - 1 ) * $this->postsPer );
		$total = intval( $this->postsPer );
		$order = $this->order;

		// accommodate a custom offset
		$start = absint( apply_filters( 'searchwp_query_limit_start', $start, $this->page ) );
		$total = absint( apply_filters( 'searchwp_query_limit_total', $total, $this->page ) );

		$extraWhere = apply_filters( 'searchwp_where', '' );
		$sql .= " " . $extraWhere . " ";

		$sql .= "
			GROUP BY {$prefix}posts.ID
			ORDER BY finalweight {$order}
		";

		if( $this->postsPer > 0 )
			$sql .= "LIMIT {$start}, {$total}";

		$sql = str_replace( "\n", " ", $sql );

		$postIDs = $wpdb->get_col( $sql );

		// retrieve how many total posts were found without the limit
		$this->foundPosts = $wpdb->get_var(
			apply_filters_ref_array(
				'found_posts_query',
				array( 'SELECT FOUND_ROWS()', &$wpdb )
			)
		);

		// store an accurate max_num_pages for $wp_query
		$this->maxNumPages = ceil( $this->foundPosts / $this->postsPer );

		// store our post IDs
		$this->postIDs = $postIDs;

		return true;
	}


	/**
	 * Returns the maximum number of pages of results
	 *
	 * @return int
	 * @since 1.0.5
	 */
	function getMaxNumPages()
	{
		return $this->maxNumPages;
	}


	/**
	 * Returns the number of found posts
	 *
	 * @return int
	 * @since 1.0.5
	 */
	function getFoundPosts()
	{
		return $this->foundPosts;
	}


	/**
	 * Returns the number of the current page of results
	 *
	 * @return int
	 * @since 1.0.5
	 */
	function getPage()
	{
		return $this->page;
	}

}
