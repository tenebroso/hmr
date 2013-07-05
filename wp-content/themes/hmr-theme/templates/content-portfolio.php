<?php while (have_posts()) : the_post(); ?>

  <div class="watermark"></div>

<?php if(get_field('photos')): ?>

  <?php $i=0; ?>

    <ul class="thumb_nav">

      <?php while(has_sub_field('photos')):  ?>

      		<?php // Get the attachment ID, then assign that ID to both the thumbnail and full-size image
      		$attachment_id = get_sub_field('photo');
      		$thumb = wp_get_attachment_image_src($attachment_id, 'slideshow-thumb'); 
      		$big = wp_get_attachment_image_src($attachment_id, 'slideshow-lg'); ?>

        <li class="slide_thumb" data-img="<?php echo $big[0]?>" data-id="<?php echo $i; ?>" >
        	<img src="<?php echo $thumb[0]?>" />
        </li>

        <?php $i++; ?>

      <?php endwhile; ?>

    </ul>


    <ul class="gallery-footer">

          <li class="credit">
          
            <h4><a href="<?php bloginfo('url');?>"><?php bloginfo('title');?></a></h4>

            <?php $i=0; ?>

            <?php while(has_sub_field('photos')): ?>
            
              <?php $credit = get_sub_field('photo_credit'); $url = get_sub_field('photo_credit_url'); if ($credit) { ?>

                <p class="photographer id-<?php echo $i; ?>">Photography by: <?php if($url) { ?><a href="<?php echo $url; ?>"><?php } ?><?php echo $credit;?><?php if($url) { ?></a><?php } ?></p>
                
                <?php } else { ?>

                <p class="photographer id-<?php echo $i; ?>"></p>
              
              <?php } ?>

              <?php $i++; ?>

            <?php endwhile; ?>

          </li>

        

    </ul>

    <div class="gallery-title">

      <h4><?php the_title();?></h4>

    </div>

  <?php endif; ?>


  <div class="big_arrow left" data-dir="prev"></div>
  <div class="big_arrow right" data-dir="next"></div>


<?php endwhile; ?>