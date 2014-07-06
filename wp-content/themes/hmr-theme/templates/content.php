<article <?php post_class(); ?>>
  <header>
    <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
    <?php get_template_part('templates/entry-meta'); ?>
  </header>
  <div class="entry-content">
    <?php the_excerpt(); ?>
    <?php the_post_thumbnail(); ?>
    <p><a class="btn btn-read-more" href="<?php the_permalink();?>">Read More &raquo;</a></p>
  </div>
  <footer>
    <?php the_tags('<ul class="entry-tags"><li>','</li><li>','</li></ul>'); ?>
  </footer>
</article>
