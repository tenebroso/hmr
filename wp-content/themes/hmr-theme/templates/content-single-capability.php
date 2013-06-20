<?php while (have_posts()) : the_post(); ?>

  <?php if(get_field('photos')): ?>

    <ul class="fullsize slideshow">

      <?php while(has_sub_field('photos')): ?>

        <li style="background-image:url(<?php the_sub_field('photo'); ?>);"></li>

      <?php endwhile; ?>

    </ul>

    <ul class="footer">

       <?php while(has_sub_field('photos')): ?>

          <li>
          
            <h4><?php bloginfo('title');?></h4>
            <p>Photography by: <a href="#">Lorem Ipsum Photography</a><?php // the_sub_field('photo_credit');?></p>

          </li>

      <?php endwhile; ?>

    </ul>

    <div class="gallery-title">

      <h4><?php the_title();?></h4>

    </div>

  <?php endif; ?>

  <div class="meta-box">
    
    <h2><?php the_title();?></h2>
    <?php the_content();?>

  </div>

<?php endwhile; ?>
