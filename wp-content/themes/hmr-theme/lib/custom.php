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

//add_action('admin_head', 'hide_admin_menu');


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
        'parent_item_colon' => _x( 'Parent Press Release:', 'pressy' ),
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
   Register Additional Nav (Sub-navs)
   ========================================================================== */

register_nav_menus( array(
    'whoweare' => 'Who We Are Sub-Nav',
    'whatwedo' => 'What We Do Sub-Nav',
    'portfolio' => 'Portfolio Sub-Nav'
    ) 
);

/* =============================================================================
   Ensure the Team Archive shows enough posts
   ========================================================================== */

function namespace_add_custom_types( $query ) {
 if ( is_post_type_archive('team')) {
    $query->set( 'posts_per_page', -1);
      return $query;
    }
}
add_filter( 'pre_get_posts', 'namespace_add_custom_types' );

/* =============================================================================
   Register Additional Thumbnail Size (Capabilities Landing Page)
   ========================================================================== */

add_image_size( 'capabilities-thumb', 270, 270, true );
add_image_size( 'slideshow-thumb', 70, 40, true );