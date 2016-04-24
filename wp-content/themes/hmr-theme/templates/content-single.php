<img class="blog-header" src="<?php bloginfo('stylesheet_directory');?>/assets/img/blog-header.png">

<?php while (have_posts()) : the_post(); ?>
  <article <?php post_class(); ?>>
    <header>
      <h1 class="entry-title"><?php the_title(); ?></h1>
      <?php get_template_part('templates/entry-meta'); ?>
    </header>
    <div class="entry-content">
      <?php the_content(); ?>
    </div>
    <footer>
      <?php wp_link_pages(array('before' => '<nav class="page-nav"><p>' . __('Pages:', 'roots'), 'after' => '</p></nav>')); ?>
      <?php the_tags('<ul class="entry-tags"><li>','</li><li>','</li></ul>'); ?>
    </footer>
    <?php //comments_template('/templates/comments.php'); ?>

    <!-- AddThis Button BEGIN -->
    <div class="addthis_toolbox addthis_default_style">
    <a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
    <a class="addthis_button_tweet"></a>
    <a class="addthis_counter addthis_pill_style"></a>
    </div>
    <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=xa-51d491841090ecae"></script>
    <!-- AddThis Button END -->
    
  </article>

  <div class="row-fluid post-nav">
    <div class="span6 text-left"><?php previous_post('%','&laquo; Previous Post ', 'no'); ?></div>
    <div class="span6 text-right"><?php next_post('%','Next Post &raquo;', 'no'); ?></div>
  </div> <!-- end navigation -->

<?php endwhile; ?>
