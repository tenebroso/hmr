<?php while (have_posts()) : the_post(); ?>

  <?php if(get_field('photos')): ?>

    <ul class="fullsize slideshow">

      <?php while(has_sub_field('photos')): ?>

        <li style="background-image:url(<?php the_sub_field('photo'); ?>);"></li>

      <?php endwhile; ?>

    </ul>

  <?php endif; ?>

<?php endwhile; ?>
