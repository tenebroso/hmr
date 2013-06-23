<?php
/*
Template Name: Testimonials
*/
?>

<?php
    $args=array(
      'posts_per_page' => -1,
      'orderby' => 'date',
      'post_type' => 'testimonial',
      'order' => 'DESC'
    );

    $my_query = new WP_Query($args);
    if( $my_query->have_posts() ) {
      echo '<div class="row-fluid">';
      while ($my_query->have_posts()) : $my_query->the_post(); ?>
        <?php get_template_part('templates/content', 'testimonials'); ?>
       <?php
        //the_content('Read the rest of this entry &raquo;');
      endwhile;
    } echo '</div>';
  wp_reset_query();  // Restore global post data stomped by the_post().
?>