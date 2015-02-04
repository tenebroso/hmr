
<?php //get_template_part('templates/content', 'team-hero'); 
	  // This template part now lives in base.php
?>
<h1 class="text-center page-title team-title caps">The HMR Designs Team</h1>
<div class="row-fluid">

  <div class="span4 border-left no-border">
    <h2 class="text-center page-title team-title caps">Sales &amp; Design</h2>
    <div class="row-fluid team-grid">
    <?php $args = array('post_type' => 'team','posts_per_page' => -1, 'type' => 'sales-design'); 
      $the_query = new WP_Query( $args ); 
      if ( $the_query->have_posts() ) : while ( $the_query->have_posts() ) : 
        $the_query->the_post(); 
          get_template_part('templates/content', 'team');
        endwhile; 
      endif; 
      wp_reset_postdata(); ?> 
    </div> 
  </div>
  
  <div class="span4 border-left">
    <h2 class="text-center page-title team-title caps">Creative Production</h2>
    <div class="row-fluid team-grid">
    <?php $args = array('post_type' => 'team','posts_per_page' => -1, 'type' => 'creative-production'); 
      $the_query = new WP_Query( $args ); 
      if ( $the_query->have_posts() ) : while ( $the_query->have_posts() ) : 
        $the_query->the_post(); 
          get_template_part('templates/content', 'team');
        endwhile; 
      endif; 
      wp_reset_postdata(); ?>
      </div>
  </div>

  <div class="span4 border-left">
    <h2 class="text-center page-title team-title caps">Operations &amp; Administrative</h2>
    <div class="row-fluid team-grid">
    <?php $args = array('post_type' => 'team','posts_per_page' => -1, 'type' => 'operations-administrative'); 
      $the_query = new WP_Query( $args ); 
      if ( $the_query->have_posts() ) : while ( $the_query->have_posts() ) : 
        $the_query->the_post(); 
          get_template_part('templates/content', 'team');
        endwhile; 
      endif; 
      wp_reset_postdata(); ?>
      </div>
  </div>

  

</div>

<?php //if ($wp_query->max_num_pages > 1) : ?>
 <!--  <nav class="post-nav">
    <ul class="pager">
      <li class="previous"><?php next_posts_link(__('&larr; Older posts', 'roots')); ?></li>
      <li class="next"><?php previous_posts_link(__('Newer posts &rarr;', 'roots')); ?></li>
    </ul>
  </nav> -->
<?php //endif; ?>
