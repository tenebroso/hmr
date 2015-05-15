<?php while (have_posts()) : the_post(); ?>

  <div class="watermark"></div>

  <div class="meta-box relative visible-phone">
    
    <h2><?php the_title();?></h2>
    <p><?php the_field('description');?></p>

    <a class="btn js-start-slideshow">Enter Gallery</a>

  </div>

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

            <?php $c=0; ?>

            <?php while(has_sub_field('photos')): ?>

            <?php $venue = get_sub_field('photo_venue'); if ($venue) { ?>

                <p class="venue idVenue-<?php echo $c; ?>"><?php echo $venue;?></p>
                
                <?php } else { ?>

                <p class="venue idVenue-<?php echo $c; ?>"></p>
              
              <?php } ?>

            <?php $c++; ?>

            <?php endwhile; ?>
          
            <h4><a href="<?php bloginfo('url');?>"><?php bloginfo('title');?></a></h4>

            <?php $i=0; $u=0?>

            <?php while(has_sub_field('photos')): ?>
            
              <?php $credit = get_sub_field('photo_credit'); $urls = get_sub_field('photo_credits'); if ($credit) { ?>

                <p class="photographer id-<?php echo $i; ?>">Photo by: <?php if($urls) { ?><a href="<?php echo $urls; ?>" class="url-<?php echo $i; ?>"><?php } ?><?php echo $credit;?><?php if($urls) { ?></a><?php } ?></p>
                
                <?php } else { ?>

                <p class="photographer id-<?php echo $i; ?>"></p>
              
              <?php } ?>

              <?php $i++; ?>

            <?php endwhile; ?>

          </li>

        

    </ul>

   <!--  <div class="gallery-title">

      <h4><?php the_title();?></h4>

    </div> -->

  <?php endif; ?>


  <div class="big_arrow left" data-dir="prev"></div>
  <div class="big_arrow right" data-dir="next"></div>


<?php endwhile; ?>