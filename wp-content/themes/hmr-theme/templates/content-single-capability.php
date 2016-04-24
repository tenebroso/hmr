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

      <?php while(has_sub_field('photos')): ?>

          <li class="credit">

            

            <?php $venue = get_sub_field('photo_venue'); if ($venue) { ?>

                <p class="venue"><?php echo $venue;?></p>
                
                <?php } else { ?>

                <p class="venue"></p>
              
              <?php } ?>
          
            <h4><a href="<?php bloginfo('url');?>"><?php bloginfo('title');?></a></h4>
            
              <?php $credit = get_sub_field('photo_credit'); $urls = get_sub_field('photo_credits'); if ($credit) { ?>

                <p class="photographer">Photo by: <?php if($urls) { ?><a href="<?php echo $urls; ?>" class="url-<?php echo $i; ?>"><?php } ?><?php echo $credit;?><?php if($urls) { ?></a><?php } ?></p>
                
                <?php } else { ?>

                <p class="photographer"></p>
              
              <?php } ?>


            

          </li>

        <?php endwhile; ?>
        

    </ul>

   <!--  <div class="gallery-title">

      <h4><?php the_title();?></h4>

    </div> -->

  <?php endif; ?>

  <div class="meta-box hidden-phone">
    
    <h1 style="margin: 0 0 10px 0;font-size: 24px;line-height: 1.2;color: #a6a6a6;text-transform: uppercase;"><?php the_title();?></h1>
    <?php the_content();?>

  </div>

  <div class="big_arrow left" data-dir="prev"></div>
  <div class="big_arrow right" data-dir="next"></div>

<?php endwhile; ?>
