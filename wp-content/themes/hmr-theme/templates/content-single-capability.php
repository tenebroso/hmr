<?php while (have_posts()) : the_post(); ?>

  <div class="watermark"></div>

  <?php if(get_field('photos')): ?>

    <ul class="fullsize slideshow">

      <?php while(has_sub_field('photos')): ?>

            <?php $attachment_id = get_sub_field('photo');
            $big = wp_get_attachment_image_src($attachment_id, 'slideshow-lg'); ?>

        <li style="background-image:url(<?php echo $big[0]; ?>);"></li>

      <?php endwhile; ?>

    </ul>

    <ul class="gallery-footer">

          <li>
          
            <h4><?php bloginfo('title');?></h4>

          </li>

    </ul>

    <div class="gallery-title">

      <h4><?php the_title();?></h4>

    </div>

    <div class="photo-credit">

      <?php while(has_sub_field('photos')): ?>
    
      <?php $credit = get_sub_field('photo_credit'); $url = get_sub_field('photo_credit_url'); if ($credit) { ?>
        <p>Photography by: <?php if($url) { ?><a href="<?php echo $url; ?>"><?php } ?><?php echo $credit;?><?php if($url) { ?></a><?php } ?></p>
      <?php } else { ?>
        <p class="hidden">Photography by: No Photo Credit</p>
      <?php } ?>

       <?php endwhile; ?>

    </div>

  <?php endif; ?>

  <div class="meta-box hidden-phone">
    
    <h2><?php the_title();?></h2>
    <?php the_content();?>

  </div>

<?php endwhile; ?>
