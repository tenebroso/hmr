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
            <p><?php the_sub_field('photo_credit');?></p>

          </li>

      <?php endwhile; ?>

    </ul>

  <?php endif; ?>

  <div class="meta-box">
    
    <h2><?php the_title();?></h2>
    <?php the_content();?>

  </div>

<?php endwhile; ?>
