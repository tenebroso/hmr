<?php

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

// Register Custom Taxonomy
function team_type_tax() {

    $labels = array(
        'name'                       => _x( 'Types', 'Taxonomy General Name', 'text_domain' ),
        'singular_name'              => _x( 'Type', 'Taxonomy Singular Name', 'text_domain' ),
        'menu_name'                  => __( 'Type', 'text_domain' ),
        'all_items'                  => __( 'All Types', 'text_domain' ),
        'parent_item'                => __( 'Parent Type', 'text_domain' ),
        'parent_item_colon'          => __( 'Parent Type:', 'text_domain' ),
        'new_item_name'              => __( 'New Type Name', 'text_domain' ),
        'add_new_item'               => __( 'Add New Type', 'text_domain' ),
        'edit_item'                  => __( 'Edit Type', 'text_domain' ),
        'update_item'                => __( 'Update Type', 'text_domain' ),
        'separate_items_with_commas' => __( 'Separate typess with commas', 'text_domain' ),
        'search_items'               => __( 'Search Types', 'text_domain' ),
        'add_or_remove_items'        => __( 'Add or remove types', 'text_domain' ),
        'choose_from_most_used'      => __( 'Choose from the most used types', 'text_domain' ),
        'not_found'                  => __( 'Not Found', 'text_domain' ),
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
    );
    register_taxonomy( 'type', array( 'team' ), $args );

}

// Hook into the 'init' action
add_action( 'init', 'team_type_tax', 0 );

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
        'menu_position' => 4,
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
   Web CPT
   ========================================================================== */


add_action( 'init', 'register_cpt_web' );

function register_cpt_web() {

    $labels = array( 
        'name' => _x( 'Web', 'web' ),
        'singular_name' => _x( 'Web', 'web' ),
        'add_new' => _x( 'Add New', 'web' ),
        'add_new_item' => _x( 'Add New Web Item', 'web' ),
        'edit_item' => _x( 'Edit Web Item', 'web' ),
        'new_item' => _x( 'New Web Item', 'web' ),
        'view_item' => _x( 'View Web', 'web' ),
        'search_items' => _x( 'Search Web items', 'web' ),
        'not_found' => _x( 'No Web Items found', 'web' ),
        'not_found_in_trash' => _x( 'No Web Items found in Trash', 'web' ),
        'parent_item_colon' => _x( 'Parent Web Item:', 'web' ),
        'menu_name' => _x( 'Web', 'web' ),
    );

    $args = array( 
        'labels' => $labels,
        'hierarchical' => false,
        'supports' => array( 'title', 'thumbnail' ),
        'taxonomies' => array(),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 4,
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => true,
        'capability_type' => 'post'
    );

    register_post_type( 'web', $args );
    
}

/* =============================================================================
   Videos CPT
   ========================================================================== */


add_action( 'init', 'register_cpt_videos' );

function register_cpt_videos() {

    $labels = array( 
        'name' => _x( 'Video', 'video' ),
        'singular_name' => _x( 'Video', 'video' ),
        'add_new' => _x( 'Add New', 'video' ),
        'add_new_item' => _x( 'Add New Video', 'video' ),
        'edit_item' => _x( 'Edit Video', 'video' ),
        'new_item' => _x( 'New Video', 'video' ),
        'view_item' => _x( 'View Video', 'video' ),
        'search_items' => _x( 'Search Videos', 'video' ),
        'not_found' => _x( 'No Videos found', 'video' ),
        'not_found_in_trash' => _x( 'No Videos found in Trash', 'video' ),
        'parent_item_colon' => _x( 'Parent Video:', 'video' ),
        'menu_name' => _x( 'Video', 'video' ),
    );

    $args = array( 
        'labels' => $labels,
        'hierarchical' => false,
        'supports' => array( 'title', 'thumbnail' ),
        'taxonomies' => array(),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 4,
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => true,
        'capability_type' => 'post'
    );

    register_post_type( 'video', $args );
    
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

    //register_post_type( 'testimonial', $args );
    
}

/* =============================================================================
   Register Additional Nav (Sub-navs)
   ========================================================================== */

register_nav_menus( array(
    'mobile' => 'Mobile Nav',
    'whoweare' => 'Who We Are Sub-Nav',
    'whatwedo' => 'What We Do Sub-Nav',
    'portfolio' => 'Portfolio Sub-Nav',
    'media' => 'Media Sub-Nav',
    'capabilities' => 'Capabilities Sub-Nav'
    ) 
);

/* =============================================================================
   Remove pages & cpt from search results
   ========================================================================== */

function exclude_pages_from_search($query) {
    if ($query->is_search) {
        $query->set('post_type', 'post');
    }
        return $query;
    }
//Using the Search WP Plugin instead
    //add_filter('pre_get_posts','exclude_pages_from_search');

/* =============================================================================
   Change Posts to say Blog Posts
   ========================================================================== */

class chg_Posts_to_Blog
{
    public static function init()
    {
        global $wp_post_types;
        $labels = &$wp_post_types['post']->labels;
        $labels->name = 'Blog Posts';
        $labels->singular_name = 'Blog Post';
        $labels->add_new = 'Add Blog Post';
        $labels->add_new_item = 'Add Blog Post';
        $labels->edit_item = 'Edit Blog Post';
        $labels->new_item = 'Blog Post';
        $labels->view_item = 'View Blog Post';
        $labels->search_items = 'Search Blog Posts';
        $labels->not_found = 'No Blog Posts found';
        $labels->not_found_in_trash = 'No Blog Posts found in trash';
        $labels->name_admin_bar = 'Blog Posts';
    }

    public static function admin_menu()
    {
        global $menu;
        global $submenu;
        $menu[5][0] = 'Blog Posts';
        $submenu['edit.php'][5][0] = 'Blog Posts';
        $submenu['edit.php'][10][0] = 'Add Blog Post';
    }
}

add_action( 'init', array ( 'chg_Posts_to_Blog', 'init' ) );
add_action( 'admin_menu', array ( 'chg_Posts_to_Blog', 'admin_menu' ) );

/* =============================================================================
   Cleanup Dashboard
   ========================================================================== */

function hide_admin_menu()
{
    global $current_user;
    get_currentuserinfo();
 
    if($current_user->user_login != 'jonbukiewicz')
    {
        global $wp_meta_boxes;
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_drafts']);
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
        remove_menu_page('upload.php'); //remove media
        remove_menu_page('tools.php'); //remove tools
        remove_menu_page('themes.php'); 
        remove_menu_page('plugins.php');
        remove_menu_page('options-general.php');
        remove_menu_page('edit-comments.php'); //remove comments
        remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=post_tag' ); // remove tags
        remove_action( 'admin_notices', 'update_nag', 3 );
        echo '<style type="text/css">#toplevel_page_edit-post_type-acf, #toplevel_page_gf_edit_forms {display:none;}</style>';
    }
}

add_action('admin_head', 'hide_admin_menu');

/* =============================================================================
   Ensure the Team Archive shows enough posts
   ========================================================================== */

function hmr_search_only_posts( $query ) {
 if ( is_post_type_archive('team') || is_post_type_archive('press')) {
    $query->set( 'posts_per_page', -1);
      return $query;
    }
}
add_filter( 'pre_get_posts', 'hmr_search_only_posts' );

/* =============================================================================
   Ensure the Search Results shows enough posts
   ========================================================================== */

function myCustomizePostsPerPage()
{
    return 5;
}
 
add_filter( 'searchwp_posts_per_page', 'myCustomizePostsPerPage' );

/* =============================================================================
   Custom Login Logo
   ========================================================================== */

function custom_logo() {
  echo '<style type="text/css">
    #login h1 a { background-image: url('.get_bloginfo('template_directory').'/assets/img/sprite.png) !important; background-position: -143px -697px;
background-size: auto;width: 142px;height: 84px;float: none;display: block;margin: 0 auto 20px auto; }
    </style>';
}

add_action('login_head', 'custom_logo');

/* =============================================================================
   Register Additional Thumbnail Size (Capabilities Landing Page)
   ========================================================================== */

//Removing this due to the use of specifically cropped thumbnails on the Capabilities landing page
   //add_image_size( 'capabilities-thumb', 270, 270, true );
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
    //remove_meta_box( 'postexcerpt', 'post', 'normal' );
    remove_meta_box( 'slugdiv', 'post', 'normal' );
    //remove_meta_box( 'postimagediv', 'post', 'side' );
}

/* =============================================================================
   Add Slider Gallery Shortcode
   ========================================================================== */

function slider_shortcode($atts, $content = null){
    global $post;
    $thePost = $post->ID;
    ob_start();

        if( have_rows('gallery_builder', $thePost) ):
        echo "<ul class='post-slider'>";
            while ( have_rows('gallery_builder') ) : the_row();
        echo "<li>";
        echo "<img src='".get_sub_field('image')."'>";
        echo "<div class='caption'>'".get_sub_field('caption')."'</div>";
        echo "</li>";
            endwhile;
        echo "</ul>";

    $acfData = ob_get_clean();
    return $acfData;
    else : endif;

}

add_shortcode('slider', 'slider_shortcode');