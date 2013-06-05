<?php

/* Optionally hide the Advanced Custom Fields Admin Menu */

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

/* Add an HR to the WP Post editor */

function enable_more_buttons($buttons) {
  $buttons[] = 'hr';
	$buttons[] = 'fontselect';
	  $buttons[] = 'sup';
  return $buttons;
}
add_filter("mce_buttons", "enable_more_buttons");

/* Custom Post Types */ 
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