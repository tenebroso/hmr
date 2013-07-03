<?php

/* =============================================================================
   Optionally hide the Advanced Custom Fields Admin Menu
   Uncomment add_action('admin_head', 'hide_admin_menu'); when in production
   ========================================================================== */

function hide_admin_menu()
{
	global $current_user;
	get_currentuserinfo();
 
	if($current_user->user_login != 'admin')
	{
		echo '<style type="text/css">#toplevel_page_edit-post_type-acf{display:none;}</style>';
	}
}

add_action('admin_head', 'hide_admin_menu');


/* =============================================================================
   Add an HR to the WP Post editor
   ========================================================================== */

function enable_more_buttons($buttons) {
    $buttons[] = 'hr';
    return $buttons;
}

add_filter("mce_buttons", "enable_more_buttons");

/* =============================================================================
   Team Member CPT
   ========================================================================== */


add_action( 'init', 'register_cpt_hmr' );

function register_cpt_hmr() {

    $labels = array( 
        'name' => _x( 'Team Members', 'team' ),
        'singular_name' => _x( 'Team Member', 'team' ),
        'add_new' => _x( 'Add New', 'team' ),
        'add_new_item' => _x( 'Add New Team Member', 'team' ),
        'edit_item' => _x( 'Edit Team Member', 'team' ),
        'new_item' => _x( 'New Team Member', 'team' ),
        'view_item' => _x( 'View Team Member', 'team' ),
        'search_items' => _x( 'Search Team Members', 'team' ),
        'not_found' => _x( 'No Team Members found', 'team' ),
        'not_found_in_trash' => _x( 'No Team Members found in Trash', 'team' ),
        'parent_item_colon' => _x( 'Parent Team Member:', 'team' ),
        'menu_name' => _x( 'Team Members', 'team' ),
    );

    $args = array( 
        'labels' => $labels,
        'hierarchical' => false,
        'supports' => array( 'title', 'editor', 'thumbnail' ),
        'taxonomies' => array(),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 5,
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => true,
        'capability_type' => 'post'
    );

    register_post_type( 'team', $args );
    
}

/* =============================================================================
   Capabilities CPT
   ========================================================================== */


add_action( 'init', 'register_cpt_capabilities' );

function register_cpt_capabilities() {

    $labels = array( 
        'name' => _x( 'Capabilities', 'capability' ),
        'singular_name' => _x( 'Capability', 'capability' ),
        'add_new' => _x( 'Add New', 'capability' ),
        'add_new_item' => _x( 'Add New Capability', 'capability' ),
        'edit_item' => _x( 'Edit Capability', 'capability' ),
        'new_item' => _x( 'New Capability', 'capability' ),
        'view_item' => _x( 'View Capability', 'capability' ),
        'search_items' => _x( 'Search Capabilities', 'capability' ),
        'not_found' => _x( 'No Capabilities found', 'capability' ),
        'not_found_in_trash' => _x( 'No Capabilities found in Trash', 'capability' ),
        'parent_item_colon' => _x( 'Parent Capability:', 'capability' ),
        'menu_name' => _x( 'Capabilities', 'capability' ),
    );

    $args = array( 
        'labels' => $labels,
        'hierarchical' => false,
        'supports' => array( 'title', 'editor', 'thumbnail' ),
        'taxonomies' => array(),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 5,
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => true,
        'capability_type' => 'post'
    );

    register_post_type( 'capability', $args );
    
}

/* =============================================================================
   Press Release CPT
   ========================================================================== */


add_action( 'init', 'register_cpt_press' );

function register_cpt_press() {

    $labels = array( 
        'name' => _x( 'Press Releases', 'press' ),
        'singular_name' => _x( 'Press Release', 'press' ),
        'add_new' => _x( 'Add New', 'press' ),
        'add_new_item' => _x( 'Add New Press Release', 'press' ),
        'edit_item' => _x( 'Edit Press Release', 'press' ),
        'new_item' => _x( 'New Press Release', 'press' ),
        'view_item' => _x( 'View Press Release', 'press' ),
        'search_items' => _x( 'Search Press Releases', 'press' ),
        'not_found' => _x( 'No Press Releases found', 'press' ),
        'not_found_in_trash' => _x( 'No Press Releases found in Trash', 'press' ),
        'parent_item_colon' => _x( 'Parent Press Release:', 'press' ),
        'menu_name' => _x( 'Press Releases', 'press' ),
    );

    $args = array( 
        'labels' => $labels,
        'hierarchical' => false,
        'supports' => array( 'title', 'thumbnail' ),
        'taxonomies' => array(),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 5,
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => true,
        'capability_type' => 'post'
    );

    register_post_type( 'press', $args );
    
}

/* =============================================================================
   Testimonial CPT
   ========================================================================== */


add_action( 'init', 'register_cpt_testimonial' );

function register_cpt_testimonial() {

    $labels = array( 
        'name' => _x( 'Testimonials', 'testimonial' ),
        'singular_name' => _x( 'Testimonial', 'testimonial' ),
        'add_new' => _x( 'Add New', 'testimonial' ),
        'add_new_item' => _x( 'Add New Testimonial', 'testimonial' ),
        'edit_item' => _x( 'Edit Testimonial', 'testimonial' ),
        'new_item' => _x( 'New Testimonial', 'testimonial' ),
        'view_item' => _x( 'View Testimonial', 'testimonial' ),
        'search_items' => _x( 'Search Testimonials', 'testimonial' ),
        'not_found' => _x( 'No Testimonials found', 'testimonial' ),
        'not_found_in_trash' => _x( 'No Testimonials found in Trash', 'testimonial' ),
        'parent_item_colon' => _x( 'Parent Testimonial:', 'testimonial' ),
        'menu_name' => _x( 'Testimonials', 'testimonial' ),
    );

    $args = array( 
        'labels' => $labels,
        'hierarchical' => false,
        'supports' => array( 'title', 'thumbnail' ),
        'taxonomies' => array(),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 5,
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => true,
        'capability_type' => 'post'
    );

    register_post_type( 'testimonial', $args );
    
}

/* =============================================================================
   Register Additional Nav (Sub-navs)
   ========================================================================== */

register_nav_menus( array(
    'mobile' => 'Mobile Nav',
    'whoweare' => 'Who We Are Sub-Nav',
    'whatwedo' => 'What We Do Sub-Nav',
    'portfolio' => 'Portfolio Sub-Nav'
    ) 
);

/* =============================================================================
   Ensure the Team Archive shows enough posts
   ========================================================================== */

function namespace_add_custom_types( $query ) {
 if ( is_post_type_archive('team') || is_post_type_archive('press')) {
    $query->set( 'posts_per_page', -1);
      return $query;
    }
}
add_filter( 'pre_get_posts', 'namespace_add_custom_types' );

/* =============================================================================
   Register Additional Thumbnail Size (Capabilities Landing Page)
   ========================================================================== */

add_image_size( 'capabilities-thumb', 270, 270, true );
add_image_size( 'press-thumb', 233, 280, true );
add_image_size( 'slideshow-thumb', 70, 40, true );
add_image_size( 'slideshow-lg', 1800, 1800 );


/* =============================================================================
   Blog Pagination Courtesy: http://www.kriesi.at/archives/how-to-build-a-wordpress-post-pagination-without-plugin
   ========================================================================== */


function kriesi_pagination($pages = '', $range = 2)
{  
     $showitems = ($range * 2)+1;  

     global $paged;
     if(empty($paged)) $paged = 1;

     if($pages == '')
     {
         global $wp_query;
         $pages = $wp_query->max_num_pages;
         if(!$pages)
         {
             $pages = 1;
         }
     }   

     if(1 != $pages)
     {
         echo "<ul class='pagination hidden-phone'>";
         if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<li class='hidden'><a href='".get_pagenum_link(1)."'>&laquo;</a></li>";
         if($paged > 1 && $showitems < $pages) echo "<li class='prev'><a href='".get_pagenum_link($paged - 1)."'>&laquo;  Previous Page</a></li>";

         for ($i=1; $i <= $pages; $i++)
         {
             if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
             {
                 echo ($paged == $i)? "<li><span class='current'>".$i."</span></li>":"<li><a href='".get_pagenum_link($i)."' class='inactive' >".$i."</a></li>";
             }
         }

         if ($paged < $pages && $showitems < $pages) echo "<li class='next'><a href='".get_pagenum_link($paged + 1)."'>Next Page &raquo;</a></li>";  
         if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<li class='hidden'><a class='next' href='".get_pagenum_link($pages)."'></a></li>";
         echo "</ul>\n";
     }
}

/* =============================================================================
   Remove unncessary meta boxes from post edit screen courtesy: http://justintadlock.com/archives/2011/04/13/uncluttering-the-post-editing-screen-in-wordpress
   ========================================================================== */


add_action( 'add_meta_boxes', 'my_remove_post_meta_boxes' );

function my_remove_post_meta_boxes() {
    remove_meta_box( 'commentsdiv', 'post', 'normal' );
    remove_meta_box( 'tagsdiv-post_tag', 'post', 'side' );
    remove_meta_box( 'trackbacksdiv', 'post', 'normal' );
    remove_meta_box( 'postcustom', 'post', 'normal' );
    remove_meta_box( 'commentstatusdiv', 'post', 'normal' );
    remove_meta_box( 'postexcerpt', 'post', 'normal' );
    remove_meta_box( 'slugdiv', 'post', 'normal' );
    remove_meta_box( 'postimagediv', 'post', 'side' );
}