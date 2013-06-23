<?php
/*
Template Name: Press Releases
*/
?>

<?php
$args=array(
    'orderby' => 'date',
    'order' => 'ASC',
    'post_type' => 'press',
    'posts_per_page' => 1,
    'caller_get_posts'=>1
);
$oldestpost =  get_posts($args);

$args=array(
    'orderby' => 'date',
    'order' => 'DESC',
    'post_type' => 'press',
    'posts_per_page' => 1,
    'caller_get_posts'=>1
);
$newestpost =  get_posts($args);

if ( !empty($oldestpost) && !empty($newestpost) ) {
  $oldest = mysql2date("Y", $oldestpost[0]->post_date);
  $newest = mysql2date("Y", $newestpost[0]->post_date);

  for ( $counter = intval($newest); $counter >= intval($oldest); $counter = $counter - 1) {

    $args=array(
      'year'     => $counter,
      'posts_per_page' => -1,
      'orderby' => 'date',
      'post_type' => 'press',
      'order' => 'DESC',
      'caller_get_posts'=>1
    );

    $my_query = new WP_Query($args);
    if( $my_query->have_posts() ) {
      echo '<h4 class="date_heading">' . $counter . '</h4><div class="row-fluid">';
      while ($my_query->have_posts()) : $my_query->the_post(); ?>
        <?php get_template_part('templates/content', 'press'); ?>
       <?php
        //the_content('Read the rest of this entry &raquo;');
      endwhile;
    } echo '</div>';
  wp_reset_query();  // Restore global post data stomped by the_post().
  }
}
?>
  


