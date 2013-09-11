<?php

global $wp_filesystem;

if( !defined( 'ABSPATH' ) ) die();

include_once ABSPATH . 'wp-admin/includes/file.php';

/**
 * Class SearchWPIndexer is responsible for generating the search index
 */
class SearchWPIndexer
{
	/**
	 * @var object Stores post object during indexing
	 * @since 1.0
	 */
	private $post;

	/**
	 * @var bool Whether there are posts left to index
	 * @since 1.0
	 */
	private $unindexedPosts = false;

	/**
	 * @var int The maximum weight for a single term
	 * @since 1.0
	 */
	private $weightLimit = 500;

	/**
	 * @var bool Whether the indexer should index numbers
	 * @since 1.0
	 */
	private $indexNumbers = false;

	/**
	 * @var int Internal counter
	 * @since 1.0
	 */
	private $count = 0;

	/**
	 * @var array Common words
	 * @since 1.0
	 */
	private $common = array();

	/**
	 * @var int Maximum number of times we should try to index a post
	 */
	private $maxAttemptsToIndex = 3;

	/**
	 * @var array Character entities as specified by Ando Saabas in Sphider http://www.sphider.eu/
	 * @since 1.0
	 */
	private $entities = array(
		"&amp" => "&", "&apos" => "'", "&THORN;" => "Þ", "&szlig;" => "ß", "&agrave;" => "à", "&aacute;" => "á",
		"&acirc;" => "â", "&atilde;" => "ã", "&auml;" => "ä", "&aring;" => "å", "&aelig;" => "æ", "&ccedil;" => "ç",
		"&egrave;" => "è", "&eacute;" => "é", "&ecirc;" => "ê", "&euml;" => "ë", "&igrave;" => "ì", "&iacute;" => "í",
		"&icirc;" => "î", "&iuml;" => "ï", "&eth;" => "ð", "&ntilde;" => "ñ", "&ograve;" => "ò", "&oacute;" => "ó",
		"&ocirc;" => "ô", "&otilde;" => "õ", "&ouml;" => "ö", "&oslash;" => "ø", "&ugrave;" => "ù", "&uacute;" => "ú",
		"&ucirc;" => "û", "&uuml;" => "ü", "&yacute;" => "ý", "&thorn;" => "þ", "&yuml;" => "ÿ",
		"&Agrave;" => "à", "&Aacute;" => "á", "&Acirc;" => "â", "&Atilde;" => "ã", "&Auml;" => "ä",
		"&Aring;" => "å", "&Aelig;" => "æ", "&Ccedil;" => "ç", "&Egrave;" => "è", "&Eacute;" => "é", "&Ecirc;" => "ê",
		"&Euml;" => "ë", "&Igrave;" => "ì", "&Iacute;" => "í", "&Icirc;" => "î", "&Iuml;" => "ï", "&ETH;" => "ð",
		"&Ntilde;" => "ñ", "&Ograve;" => "ò", "&Oacute;" => "ó", "&Ocirc;" => "ô", "&Otilde;" => "õ", "&Ouml;" => "ö",
		"&Oslash;" => "ø", "&Ugrave;" => "ù", "&Uacute;" => "ú", "&Ucirc;" => "û", "&Uuml;" => "ü", "&Yacute;" => "ý",
		"&Yhorn;" => "þ", "&Yuml;" => "ÿ"
	);


	/**
	 * Constructor
	 *
	 * @param string $hash The key used to validate instantiation
	 * @since 1.0
	 */
	public function __construct( $hash = '' )
	{
		// make sure we've got a valid request to index
		if( get_transient( 'searchwp' ) !== $hash )
		{
			do_action( 'searchwp_log', 'Invalid index request ' . $hash );
			return;
		}

		$this->checkIfStalled();

		$searchwp = SearchWP::instance();

		$this->common = $searchwp->common;

		// check to see if indexer is already running
		if( !get_option( SEARCHWP_PREFIX . 'running' ) )
		{
			update_option( SEARCHWP_PREFIX . 'last_activity', current_time( 'timestamp' ) );
			update_option( SEARCHWP_PREFIX . 'running', true );

			$this->updateRunningCounts();

			if( $this->findUnindexedPosts() !== false )
			{
				$this->index();
				update_option( SEARCHWP_PREFIX . 'running', false );

				// reset the transient
				delete_transient( 'searchwp' );
				$hash = sha1( uniqid( 'searchwpindex' ) );
				set_transient( 'searchwp', $hash );

				do_action( 'searchwp_log', 'Request index (internal) ' . trailingslashit( site_url() ) . '?swpnonce=' . $hash );

				// recursive trigger
				wp_remote_get( trailingslashit( site_url() ) . '?swpnonce=' . $hash, array( 'blocking' => false, 'user-agent' => 'SearchWP' ) );
			}

			// done indexing
			do_action( 'searchwp_log', 'Indexing chunk complete' );
			update_option( SEARCHWP_PREFIX . 'initial', true );
			update_option( SEARCHWP_PREFIX . 'running', false );
			delete_option( SEARCHWP_PREFIX . 'last_activity' );
		}
	}


	/**
	 * Determine the number of posts left to index, total post count, and how many posts have been indexed already
	 *
	 * @since 1.0
	 */
	function updateRunningCounts()
	{
		$total 		= intval( $this->countTotalPosts() );
		$indexed 	= intval( $this->indexedCount() );
		$remaining  = intval( $total - $indexed );

		update_option( SEARCHWP_PREFIX . 'total', $total );
		update_option( SEARCHWP_PREFIX . 'remaining', $remaining );
		update_option( SEARCHWP_PREFIX . 'done', $indexed );

		do_action( 'searchwp_log', 'Updating counts: ' . $total . ' ' . $remaining . ' ' . $indexed );

		if( $remaining < 1 )
		{
			do_action( 'searchwp_log', 'Setting initial' );
			update_option( SEARCHWP_PREFIX . 'initial', true );
		}
	}


	/**
	 * Checks to see if the indexer has stalled with posts left to index
	 *
	 * @since 1.0
	 */
	function checkIfStalled()
	{
		do_action( 'searchwp_log', 'checkIfStalled()' );
		// if the last activity was over three minutes ago, let's reset and notify of an issue
		//		(it shouldn't take 3 minutes to index 10 posts)
		if( false !== get_option( SEARCHWP_PREFIX . 'last_activity' ) )
		{
			if( current_time( 'timestamp' ) > get_option( SEARCHWP_PREFIX . 'last_activity' ) + 180 )
			{
				do_action( 'searchwp_log', 'Stalled' );

				$searchwp = SearchWP::instance();

				// stalled
				update_option( SEARCHWP_PREFIX . 'running', false );
				delete_transient( 'searchwp' );
				$hash = sha1( uniqid( 'searchwpindex' ) );
				set_transient( 'searchwp', $hash );
				do_action( 'searchwp_log', 'Request index (from stalled) ' . trailingslashit( site_url() ) . '?swpnonce=' . $hash );
				wp_remote_get( trailingslashit( site_url() ) . '?swpnonce=' . $hash, array( 'blocking' => false, 'user-agent' => 'SearchWP' ) );

				// notify the admin
				// wp_mail( get_option( 'admin_email' ), '[' . $searchwp->pluginName . '] ' . __( 'Error: index timed out and has been restarted', $searchwp->textDomain ), __( 'The indexer timed out. The process has been restarted automatically.', $searchwp->textDomain ) );
			}
		}
	}


	/**
	 * Sets post property
	 *
	 * @param $post object WordPress Post object
	 * @since 1.0
	 */
	function setPost( $post )
	{
		$this->post = $post;

		// append Custom Field data
		$this->post->custom = get_post_custom( $post->ID );
	}


	/**
	 * Count the total number of posts in this WordPress installation
	 *
	 * @return int Total number of posts
	 * @since 1.0
	 */
	function countTotalPosts()
	{
		$args = array(
			'posts_per_page'	=> 1,
			'post_type' 			=> 'any',
			'post_status'			=> 'publish',
			'meta_query' 			=> array(
				array(
					'key' 				=> '_' . SEARCHWP_PREFIX . 'skip',
					'value' 			=> '',	// only want media that hasn't failed indexing multiple times
					'compare' 		=> 'NOT EXISTS',
					'type'				=> 'BINARY'
				)
			)
		);

		$totalPosts = new WP_Query( $args );

		// also check for media
		$args = array(
			'posts_per_page'	=> 1,
			'post_type' 			=> 'attachment',
			'post_status'			=> 'inherit',
			'meta_query' 			=> array(
				array(
					'key' 				=> '_' . SEARCHWP_PREFIX . 'skip',
					'value' 			=> '',	// only want media that hasn't failed indexing multiple times
					'compare' 		=> 'NOT EXISTS',
					'type'				=> 'BINARY'
				)
			)
		);

		$totalMedia = new WP_Query( $args );

		return intval( $totalPosts->found_posts + $totalMedia->found_posts );
	}


	/**
	 * Count the number of posts that have been indexed
	 *
	 * @return int Number of posts that have been indexed
	 * @since 1.0
	 */
	function indexedCount()
	{
		$args = array(
			'posts_per_page'	=> 1,
			'post_type' 			=> 'any',
			'post_status'			=> 'any',	// we can use this here because we're checking for our explicit meta tag
			'meta_query' 			=> array(
				'relation'			=> 'AND',
				array(
					'key' 				=> '_' . SEARCHWP_PREFIX . 'indexed',
					'value' 			=> true,
					'type'				=> 'BINARY'
				),
				array(
					'key' 				=> '_' . SEARCHWP_PREFIX . 'skip',
					'value' 			=> '',	// only want media that hasn't failed indexing multiple times
					'compare' 		=> 'NOT EXISTS',
					'type'				=> 'BINARY'
				)
			),
			// TODO: should we include 'exclude_from_search' for accuracy?
		);

		$indexed = new WP_Query( $args );

		return (int) $indexed->found_posts;
	}


	/**
	 * Query for posts that have not been indexed yet
	 *
	 * @return array|bool Posts (max 10) that have yet to be indexed
	 * @since 1.0
	 */
	function findUnindexedPosts()
	{
		// everything that's been indexed has a postmeta flag
		// so we'll use that to determine what's left

		// we're going to index everything regardless of 'exclude_from_search' because
		// no event fires if that changes over time, so we're going to offload that
		// to be taken into consideration at query time

		$indexChunk = apply_filters( 'searchwp_index_chunk_size', 8 );
		$args = array(
			'posts_per_page'	=> intval( $indexChunk ),
			'post_type' 			=> 'any',
			'post_status'			=> 'publish',
			'meta_query' 			=> array(
				'relation'			=> 'AND',
				array(
					'key' 				=> '_' . SEARCHWP_PREFIX . 'indexed',
					'value' 			=> '',	// http://core.trac.wordpress.org/ticket/23268
					'compare' 		=> 'NOT EXISTS',
					'type'				=> 'BINARY'
				),
//				array(
//					'key' 			=> '_' . SEARCHWP_PREFIX . 'skip',
//					'value' 		=> '',	// only want media that hasn't failed indexing multiple times
//					'compare' 	=> 'NOT EXISTS',
//					'type'			=> 'BINARY'
//				),
				array( // if a PDF was flagged during indexing, we don't want to keep trying
					'key' 				=> '_' . SEARCHWP_PREFIX . 'review',
					'value' 			=> '',
					'compare' 		=> 'NOT EXISTS',
					'type'				=> 'BINARY'
				)
			)
		);

		$unindexedPosts = get_posts( $args );

		// also check for media

		$indexChunk = apply_filters( 'searchwp_index_chunk_size', 2 );
		$mediaArgs = array(
			'posts_per_page'	=> intval( $indexChunk ),
			'post_type' 			=> 'attachment',
			'post_status'			=> 'inherit',
			'meta_query' 			=> array(
				'relation'			=> 'AND',
				array(
					'key' 				=> '_' . SEARCHWP_PREFIX . 'indexed',
					'value' 			=> '',	// http://core.trac.wordpress.org/ticket/23268
					'compare' 		=> 'NOT EXISTS',
					'type'				=> 'BINARY'
				),
				array(
					'key' 				=> '_' . SEARCHWP_PREFIX . 'skip',
					'value' 			=> '',	// only want media that hasn't failed indexing multiple times
					'compare' 		=> 'NOT EXISTS',
					'type'				=> 'BINARY'
				)
			)
		);

		$unindexedMedia = get_posts( $mediaArgs );

		$this->unindexedPosts = !empty( $unindexedPosts ) || !empty( $unindexedMedia ) ? array_merge( $unindexedPosts, $unindexedMedia ) : false;

		return $this->unindexedPosts;
	}


	/**
	 * Index posts stored in $this->unindexedPosts
	 *
	 * @since 1.0
	 */
	function index()
	{
		global $wp_filesystem, $wpdb;

		if( is_array( $this->unindexedPosts ) && count( $this->unindexedPosts ) )
			foreach( $this->unindexedPosts as $unindexedPost )
			{
				$this->setPost( $unindexedPost );

				// increment the attempt counter for attachments
				if( $this->post->post_type == 'attachment' )
				{
					$count = get_post_meta( $this->post->ID, '_' . SEARCHWP_PREFIX . 'attempts', true );
					if( $count == false )
						$count = 0;
					else
						$count = intval( $count );

					$count++;

					// increment our counter to prevent the indexer getting stuck on a gigantic PDF
					update_post_meta( $this->post->ID, '_' . SEARCHWP_PREFIX . 'attempts', $count );
				}

				// log this
				$wpdb->insert(
					$wpdb->prefix . SEARCHWP_DBPREFIX . 'log',
					array(
						'event' 		=> 'action',
						'query'			=> 'index ' . $this->post->ID,
						'hits'			=> 0,
						'wpsearch'	=> 0
					),
					array(
						'%s',
						'%s',
						'%d',
						'%d'
					)
				);

				// if we breached the attempts, flag it to skip
				if( $this->post->post_type == 'attachment' && intval( $count ) > $this->maxAttemptsToIndex )
				{
					// flag it to be skipped
					update_post_meta( $this->post->ID, '_' . SEARCHWP_PREFIX . 'skip', true );
				}
				else
				{
					// if it's an attachment, we want the permalink
					$slug = $this->post->post_type == 'attachment' ? str_replace( get_bloginfo( 'wpurl' ), '', get_permalink( $this->post->ID ) ) : '';

					// we allow users to override the extracted content from documents, if they have done so this flag is set
					$skipDocProcessing = get_post_meta( $this->post->ID, '_' . SEARCHWP_PREFIX . 'skip_doc_processing', true );
					$omitDocProcessing = apply_filters( 'searchwp_omit_document_processing', false );

					if( !$skipDocProcessing && !$omitDocProcessing )
					{
						// if it's a PDF we need to populate our Custom Field with it's content
						if( $this->post->post_mime_type == 'application/pdf' )
						{
							@ini_set( 'max_execution_time', 60 );
							if( 60 == ini_get( 'max_execution_time' ) )
							{
								$filename = get_attached_file( $this->post->ID );

								$pdfParser = new PDF2Text();
								$pdfParser->setFilename( $filename );
								$pdfParser->decodePDF();
								$pdfContent = $pdfParser->output();
								$pdfContent = preg_replace( '/[\x00-\x1F\x80-\xFF]/', ' ', $pdfContent );
								$pdfContent = trim( str_replace( "\n", " ", $pdfContent ) );

								// check to see if the first pass produced nothing or concatenated strings
								$fullContentLength	= strlen( $pdfContent );
								$numberOfSpaces 		= substr_count($pdfContent, ' ');;
								if( empty( $pdfContent ) || ( ( $numberOfSpaces / $fullContentLength ) * 100 < 10 ) )
								{
									WP_Filesystem();
									$filecontent = $wp_filesystem->exists( $filename ) ? $wp_filesystem->get_contents( $filename ) : '';

									if( false != strpos( $filecontent, 'trailer' ) )
									{
										$pdfContent 	= '';
										$pdf 					= new pdf( get_attached_file( $this->post->ID ) );
										$pages 				= $pdf->get_pages();
										if( !empty( $pages ) )
											while( list( $nr, $page ) = each( $pages ) )
												$pdfContent .= $page->get_text();
									}
									else
									{
										// empty out the content so wacky concatenations are not indexed
										$pdfContent = '';

										// flag it for further review
										update_post_meta( $this->post->ID, '_' . SEARCHWP_PREFIX . 'review', 1 );
									}
								}

								$pdfContent = trim( $pdfContent );

								if( !empty( $pdfContent ) )
									update_post_meta( $this->post->ID, SEARCHWP_PREFIX . 'content', $pdfContent );
							}
						}

						// if it's plain text, index it's content
						if( $this->post->post_mime_type == 'text/plain' )
						{
							WP_Filesystem();
							$filename = get_attached_file( $this->post->ID );
							$textContent = $wp_filesystem->exists( $filename ) ? $wp_filesystem->get_contents( $filename ) : '';
							$textContent = preg_replace( '/[\x00-\x1F\x80-\xFF]/', ' ', $textContent );
							$textContent = str_replace( "\n", " ", $textContent );
							if( !empty( $textContent ) )
								update_post_meta( $this->post->ID, SEARCHWP_PREFIX . 'content', $textContent );
						}
					}

					$postTerms 				= array();
					$postTerms['title'] 	= $this->indexTitle();
					$postTerms['slug'] 		= $this->indexSlug( str_replace( '/', ' ', $slug ) );
					$postTerms['content'] 	= $this->indexContent();
					$postTerms['excerpt'] 	= $this->indexExcerpt();

					// index comments
					$comments = get_comments( array(
						'status'	=> 'approve',
						'post_id'	=> $this->post->ID
					) );

					if( !empty( $comments ) )
						foreach( $comments as $comment )
							$postTerms['comments'][] = $this->indexComment( $comment );

					// index taxonomies
					$taxonomies = get_object_taxonomies( $this->post->post_type );
					if( !empty( $taxonomies ) )
						foreach( $taxonomies as $taxonomy )
						{
							$terms = get_the_terms( $this->post->ID, $taxonomy );
							if( !empty( $terms ) )
								$postTerms['taxonomy'][$taxonomy] = $this->indexTaxonomyTerms( $taxonomy, $terms );
						}

					// index custom fields
					$customFields = get_post_custom( $this->post->ID );
					if( !empty( $customFields ) )
						foreach( $customFields as $customFieldName => $customFieldValue )
							$postTerms['customfield'][$customFieldName] = $this->indexCustomField( $customFieldName, $customFieldValue );

					// we need to break out the terms from all of this content
					$termCountBreakout = array();

					if( is_array( $postTerms ) && count( $postTerms ) )
					{
						foreach( $postTerms as $type => $terms )
						{
							switch( $type )
							{
								case 'title':
								case 'slug':
								case 'content':
								case 'excerpt':
									if( is_array( $terms ) && count( $terms ) )
										foreach( $terms as $term )
											$termCountBreakout[$term['term']][$type] = $term['count'];
									break;

								case 'taxonomy':
								case 'customfield':
									if( is_array( $terms ) && count( $terms ) )
										foreach( $terms as $name => $nameTerms )
											if( is_array( $nameTerms ) && count( $nameTerms ) )
												foreach( $nameTerms as $nameTerm )
													$termCountBreakout[$nameTerm['term']][$type][$name] = $nameTerm['count'];
									break;

							}
						}
					}

					// we now have a multidimensional array of terms with counts per type in $termCountBreakout
					$this->recordPostTerms( $termCountBreakout );

					// flag the post as indexed
					$result = update_post_meta( $this->post->ID, '_' . SEARCHWP_PREFIX . 'indexed', true );
				}
			}
	}


	function preProcessTerms( $termsArray = array() )
	{
		global $wpdb;

		if( !is_array( $termsArray ) || empty( $termsArray ) )
			return array();

		// get our database vars prepped
		$termsTable = $wpdb->prefix . SEARCHWP_DBPREFIX . 'terms';

		/**
		 * Step 1: Retrieve existing term IDs
		 */

		// we need just the term list (vs. terms & counts)
		$termsArrayStripped = array();
		$termsString = "";

		foreach( $termsArray as $term => $types )
		{
			$termsArrayStripped[] = $term;
			$termsString .= $wpdb->prepare( '%s', $term ) . ',';
		}

		$termsString = substr( $termsString, 0, strlen( $termsString ) - 1 );	// remove trailing comma

		// check to see which terms (if any) are already present
		$existingTerms = $wpdb->get_col(
			"
			SELECT term
			FROM {$termsTable}
			WHERE term IN( {$termsString} )
			"
		);

		// we only need to filter existing terms if there were any in the first place
		if( !empty( $existingTerms ) )
		{
			/**
			 * Step 2: Remove existing terms from termArray
			 */
			$newTermsToAdd = array_values( array_diff( $termsArrayStripped, $existingTerms ) );
		}
		else
		{
			$newTermsToAdd = $termsArrayStripped;
		}


		/**
		 * Step 3: Add those unique terms to the database
		 */
		if( is_array( $newTermsToAdd ) && !empty( $newTermsToAdd ) )
		{
			$stemmer = new SearchWPStemmer();

			$newTermsSql = '';
			foreach( $newTermsToAdd as $newTermToAdd )
			{
				// the term itself
				$stdTerm = $wpdb->prepare( '%s', $newTermToAdd );

				// the reverse (UTF-8)
				preg_match_all( '/./us', $newTermToAdd, $contentr );
				$revTerm = join( '', array_reverse( $contentr[0] ) );

				// the stem
				$stem = $stemmer->stem( $newTermToAdd );

				// build the SQL
				$newTermsSql .= "(" . $stdTerm . "," . $wpdb->prepare( '%s', $revTerm ) . "," . $wpdb->prepare( '%s', $stem ) . "),";
			}

			$newTermsSql = substr( $newTermsSql, 0, strlen( $newTermsSql ) - 1 );	// remove trailing comma

			$result = $wpdb->query(
				"
					INSERT IGNORE INTO {$termsTable} (term,reverse,stem)
					VALUES {$newTermsSql}
					"
			);
		}


		/**
		 * Step 4: Retrieve IDs for all terms
		 */
		$termIDs = $wpdb->get_results(
			"
			SELECT id, term FROM {$termsTable}
			WHERE term IN( {$termsString} )
			", 'OBJECT_K'
		);



		/**
		 * Step 5: Match term IDs to original terms with counts
		 */
		if( is_array( $termIDs ) )
		{
			foreach( $termIDs as $termID => $termIDMeta )
			{
				// append the term ID to the original $termsArray
				foreach( $termsArray as $termsArrayTerm => $counts )
				{
					if( $termsArrayTerm == $termIDMeta->term )
					{
						$termsArray[$termsArrayTerm]['id'] = $termIDMeta->id;
						break;
					}
				}
			}
		}

		return $termsArray;
	}


	/**
	 * Insert terms with counts into the database
	 *
	 * @param array $termsArray The terms to insert
	 * @return bool Whether the insert was successful
	 * @since 1.0
	 */
	function recordPostTerms( $termsArray = array() )
	{
		global $wpdb;

		if( !is_array( $termsArray ) || empty( $termsArray ) )
			return false;

		$success = true;	// track whether or not the database insert went okay

		// get our database vars prepped
		$termsTable = $wpdb->prefix . SEARCHWP_DBPREFIX . 'terms';

		$termsArray = $this->preProcessTerms( $termsArray );

		/**
		 * Step 6: Insert terms into index
		 */
		foreach( $termsArray as $key => $term )
		{
			if( !empty( $term ) )
			{
				if( !isset( $term['id'] ) )
					$term['id'] = $wpdb->get_var( $wpdb->prepare( "SELECT term FROM " . $termsTable . " WHERE term = %s", $key ) );

				// insert the counts for our standard fields
				$wpdb->insert(
					$wpdb->prefix . SEARCHWP_DBPREFIX . 'index',
					array(
						'term' 				=> isset( $term['id'] ) ? intval( $term['id'] ) : 0,
						'content'			=> isset( $term['content'] ) ? intval( $term['content'] ) : 0,
						'title'				=> isset( $term['title'] ) ? intval( $term['title'] ) : 0,
						'comment'			=> isset( $term['comment'] ) ? intval( $term['comment'] ) : 0,
						'excerpt'			=> isset( $term['excerpt'] ) ? intval( $term['excerpt'] ) : 0,
						'slug'				=> isset( $term['slug'] ) ? intval( $term['slug'] ) : 0,
						'post_id' 			=> intval( $this->post->ID )
					),
					array(
						'%d',
						'%d',
						'%d',
						'%d',
						'%d',
						'%d',
						'%d'
					)
				);

				// insert our custom field counts
				if( isset( $term['customfield'] ) && is_array( $term['customfield'] ) && count( $term['customfield'] ) )
					foreach( $term['customfield'] as $customField => $customFieldCount )
					{
						$wpdb->insert(
							$wpdb->prefix . SEARCHWP_DBPREFIX . 'cf',
							array(
								'metakey' 			=> $customField,
								'term'				=> isset( $term['id'] ) ? intval( $term['id'] ) : 0,
								'count'				=> intval( $customFieldCount ),
								'post_id'			=> intval( $this->post->ID )
							),
							array(
								'%s',
								'%d',
								'%d',
								'%d'
							)
						);
					}

				// index our taxonomy counts
				if( isset( $term['taxonomy'] ) && is_array( $term['taxonomy'] ) && count( $term['taxonomy'] ) )
					foreach( $term['taxonomy'] as $taxonomyName => $taxonomyCount )
					{
						$wpdb->insert(
							$wpdb->prefix . SEARCHWP_DBPREFIX . 'tax',
							array(
								'taxonomy' 			=> $taxonomyName,
								'term'				=> isset( $term['id'] ) ? intval( $term['id'] ) : 0,
								'count'				=> intval( $taxonomyCount ),
								'post_id'			=> intval( $this->post->ID )
							),
							array(
								'%s',
								'%d',
								'%d',
								'%d'
							)
						);
					}
			}
		}

		return $success;
	}


	/**
	 * Determine keyword weights for a given string. Our 'weights' are not traditional, but instead simple counts
	 * so as to facilitate changing weights on the fly and not having to reindex. Actual weights are computed at
	 * query time.
	 *
	 * @param string $string The string from which to obtain weights
	 * @return array Terms and their correlating counts
	 * @since 1.0
	 */
	function getTermCounts( $string = '' )
	{
		$wordArray = array();

		if( is_string( $string ) && !empty( $string ) )
			$wordArray = $this->getWordCountFromArray( explode( " ", strtolower( $string ) ) );

		return $wordArray;
	}


	/**
	 * Remove accents from the submitted string
	 *
	 * Written by Ando Saabas in Sphider http://www.sphider.eu/
	 *
	 * @param string $string The string from which to remove accents
	 * @return string
	 * @since 1.0
	 */
	function removeAccents( $string )
	{
		return( strtr( $string, "ÀÁÂÃÄÅÆàáâãäåæÒÓÔÕÕÖØòóôõöøÈÉÊËèéêëðÇçÐÌÍÎÏìíîïÙÚÛÜùúûüÑñÞßÿý",
			"aaaaaaaaaaaaaaoooooooooooooeeeeeeeeecceiiiiiiiiuuuuuuuunntsyy" ) );
	}


	/**
	 * Determine a word count for the submitted array.
	 *
	 * Modified version of Sphider's unique_array() by Ando Saabas, http://www.sphider.eu/
	 *
	 * @param array $arr
	 * @return array
	 * @since 1.0
	 */
	function getWordCountFromArray( $arr = array() )
	{
		$newarr = array ();

		// set the minimum character length to count as a valid term
		$minLength = apply_filters( 'searchwp_minimum_word_length', 3 );

		foreach( $arr as $term )
		{
			if( !in_array( $term, $this->common ) && ( strlen( $term ) >= intval( $minLength ) ) )
			{
				$key = md5( $term );
				if( !isset( $newarr[$key] ) )
				{
					$newarr[$key] = array(
						'term' 		=> $term,
						'count'		=> 1
					);
				}
				else
				{
					$newarr[$key]['count'] = $newarr[$key]['count'] + 1;
				}
			}
		}

		$newarr = array_values( $newarr );

		return $newarr;
	}


	/**
	 * Retrieve only the term content from the submitted string
	 *
	 * Modified from Sphider by Ando Saabas, http://www.sphider.eu/
	 *
	 * @param string $content The source content, can include markup
	 * @return string The content without markup or character encoding
	 * @since 1.0
	 */
	function cleanContent( $content = '' )
	{
		if( !is_string( $content ) )
			$content = $this->parseVariableForTerms( $content );

		// buffer tags with spaces before removing them
		$content = preg_replace( "/<[\w ]+>/", "\\0 ", $content );
		$content = preg_replace( "/<\/[\w ]+>/", "\\0 ", $content );
		$content = strip_tags( $content );
		$content = preg_replace( "/&nbsp;/", " ", $content );

		$content = strtolower( $content );
		$content = stripslashes( $content );

		// remove punctuation
		$punctuation = array( "(", ")", "·", "'", "´", "’", "‘", "”", "“", "„", "—", "–", "×", "…", "€", "\n", ".", ",", "/", "\\", "|", "[", "]", "{", "}" );
		$content = str_replace( $punctuation, ' ', $content );
		$content = preg_replace( "/[[:punct:]]/uiU", " ", $content );
		$content = preg_replace( "/[[:space:]]/uiU", " ", $content );
		$content = trim( $content );

		return $content;
	}


	/**
	 * Get the term counts for a title
	 *
	 * @param string $title The title to index
	 * @return array|bool Terms and their associated counts
	 * @since 1.0
	 */
	function indexTitle( $title = '' )
	{
		$title = ( !is_string( $title ) || empty( $title ) ) && !empty( $this->post->post_title ) ? $this->post->post_title : $title;
		$title = $this->cleanContent( $title );

		if( !empty( $title ) && is_string( $title ) )
			return $this->getTermCounts( $title );
		else
			return false;
	}


	/**
	 * Index the filename itself
	 *
	 * @param string $filename The filename to index
	 * @return array|bool
	 */
	function indexFilename( $filename = '' )
	{
		$fullFilename = explode( '.', basename( $filename ) );
		if( isset( $fullFilename[0] ) )
			$filename = $fullFilename[0]; // don't care about extension

		if( !empty( $filename ) && is_string( $filename ) )
			return $this->getTermCounts( $filename );
		else
			return false;
	}


	/**
	 * Get the term counts for a filename
	 *
	 * @param string $filename The filename to index
	 * @return array|bool Terms and their associated counts
	 * @since 1.0
	 */
	function extractFilenameTerms( $filename = '' )
	{
		// try to retrieve keywords from filename, explode by '-' or '_'
		$fullFilename = explode( '.', basename( $filename ) );
		if( isset( $fullFilename[0] ) )
			$fullFilename = $fullFilename[0]; // don't care about extension

		// first explode by hyphen, then explode those pieces by underscore
		$filenamePieces = array();

		$filenameFirstPass = explode( '-', $fullFilename );
		if( is_array( $filenameFirstPass ) )
		{
			foreach( $filenameFirstPass as $filenameSegment )
				$filenamePieces[] = $filenameSegment;
		}
		else
		{
			$filenamePieces = array( $fullFilename );
		}

		foreach( $filenamePieces as $filenamePiece )
		{
			$filenameSecondPass = explode( '-', $filenamePiece );
			if( is_array( $filenameSecondPass ) )
			{
				foreach( $filenameSecondPass as $filenameSegment )
					$filenamePieces[] = $filenameSegment;
			}
			else
			{
				$filenamePieces[] = $filenamePiece;
			}
		}

		// if we found some pieces we'll put them back together, if not we'll use the original
		$filename = is_array( $filenamePieces ) ? implode( ' ', $filenamePieces ) : $filename;

		return $filename;
	}


	/**
	 * Get the term counts for a slug
	 *
	 * @param string $slug The slug to index
	 * @return array|bool Terms and their associated counts
	 * @since 1.0
	 */
	function indexSlug( $slug = '' )
	{
		$slug = ( !is_string( $slug ) || empty( $slug ) ) && !empty( $this->post->post_name ) ? $this->post->post_name : $slug;
		$slug = str_replace( '-', ' ', $slug );
		$slug = $this->cleanContent( $slug );

		if( !empty( $slug ) && is_string( $slug ) )
			return $this->getTermCounts( $slug );
		else
			return false;
	}


	/**
	 * Get the term counts for a content block
	 *
	 * @param string $content The content to index
	 * @return array|bool Terms and their associated counts
	 * @since 1.0
	 */
	function indexContent( $content = '' )
	{
		$content = ( !is_string( $content ) || empty( $content ) ) && !empty( $this->post->post_content ) ? $this->post->post_content : $content;
		$content = $this->cleanContent( $content );

		if( !empty( $content ) && is_string( $content ) )
			return $this->getTermCounts( $content );
		else
			return false;
	}


	/**
	 * Get the term counts for a comment
	 *
	 * @param null|object $comment The comment to index
	 * @return array Terms and their associated counts
	 * @since 1.0
	 */
	function indexComment( $comment = null )
	{
		// TODO: short circuit on pingback/trackback?

		$id  	 = isset( $comment->comment_ID ) && !empty( $comment->comment_ID ) ? $comment->comment_ID : null;
		$author  = isset( $comment->comment_author ) && !empty( $comment->comment_author ) ? $comment->comment_author : null;
		$email   = isset( $comment->comment_author_email ) && !empty( $comment->comment_author_email ) ? $comment->comment_author_email : null;

		$comment = isset( $comment->comment_content ) && !empty( $comment->comment_content ) ? $comment->comment_content : $comment;
		$comment = $this->cleanContent( $comment );

		$commentTerms = array( 'id' => $id );

		// insert the comment metadata
		if( !empty( $author ) && is_string( $author ) )
			$commentTerms['author'] = $this->getTermCounts( $author );

		if( !empty( $email ) && is_string( $email ) )
			$commentTerms['email'] =  $this->getTermCounts( $email );

		// insert the comment content
		if( !empty( $comment ) && is_string( $comment ) )
			$commentTerms['comment'] = $this->getTermCounts( $comment );

		return $commentTerms;
	}


	/**
	 * Index the terms within a taxonomy
	 *
	 * @param null|string $taxonomy The taxonomy name
	 * @param array $terms The terms to index
	 * @return array|bool Terms and their associated counts
	 * @since 1.0
	 */
	function indexTaxonomyTerms( $taxonomy = null, $terms = array() )
	{
		// get just the term strings
		$cleanTerms = array();
		if( is_array( $terms ) && !empty( $terms ) )
			foreach ( $terms as $term )
				$cleanTerms[] = $this->cleanContent( $term->name );

		$cleanTerms = trim( implode( ' ', $cleanTerms ) );

		if( !empty( $cleanTerms ) && is_string( $cleanTerms ) && !empty( $taxonomy ) && is_string( $taxonomy ) )
			return $this->getTermCounts( $cleanTerms );
		else
			return false;
	}


	/**
	 * Get the term counts for an excerpt
	 *
	 * @param string $excerpt The excerpt to index
	 * @return array|bool Terms and their associated counts
	 * @since 1.0
	 */
	function indexExcerpt( $excerpt = '' )
	{
		$excerpt = ( !is_string( $excerpt ) || empty( $excerpt ) ) && !empty( $this->post->post_excerpt ) ? $this->post->post_excerpt : $excerpt;
		$excerpt = $this->cleanContent( $excerpt );

		if( !empty( $excerpt ) && is_string( $excerpt ) )
			return $this->getTermCounts( $excerpt );
		else
			return false;
	}


	/**
	 * Index a Custom Field, no matter what format
	 *
	 * @param null $customFieldName Custom Field meta key
	 * @param mixed $customFieldValue Custom field value
	 * @return array|bool Terms and their associated counts
	 * @since 1.0
	 */
	function indexCustomField( $customFieldName = null, $customFieldValue )
	{
		// custom fields can be pretty much anything, so we need to make sure we're unserializing, json_decoding, etc.
		$customFieldValue = $this->parseVariableForTerms( $customFieldValue );

		// if it's an attachment we want to extract terms from the filename
		if( $customFieldName == '_wp_attached_file' && isset( $customFieldValue[0] ) )
			$customFieldValue = $this->extractFilenameTerms( $customFieldValue[0] );

		if( !empty( $customFieldName ) && is_string( $customFieldName ) && !empty( $customFieldValue ) && is_string( $customFieldValue ) )
			return $this->getTermCounts( $customFieldValue );
		else
			return false;
	}


	/**
	 * Retrieve terms from any kind of variable, even serialized and json_encode()ed values
	 *
	 * Modified from pods_sanitize() written by Scott Clark for Pods http://pods.io
	 *
	 * @param mixed $input Variable from which to obtain terms
	 * @return string Term list
	 * @since 1.0
	 */
	function parseVariableForTerms( $input )
	{
		$output = '';

		// check to see if it's encoded
		if( is_string( $input ) )
			if( is_null( $json_decoded_input = json_decode( $input ) ) )
				$input = maybe_unserialize( $input );
			else
				$input = $json_decoded_input;

		// proceed with decoded input
		if( is_string( $input ) )
		{
			$input = $this->cleanContent( $input );
			$output = $input;
		}
		elseif( is_object( $input ) )
		{
			$input = get_object_vars( $input );

			foreach( $input as $key => $val )
				$output .= ' ' . $this->parseVariableForTerms( $val );
		}
		elseif( is_array( $input ) )
		{
			foreach( $input as $key => $val )
				$output .= ' ' . $this->parseVariableForTerms( $val );
		}

		return $output;
	}

}
