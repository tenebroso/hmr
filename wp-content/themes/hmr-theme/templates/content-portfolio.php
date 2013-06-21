<?php while (have_posts()) : the_post(); ?>

<?php if(get_field('photos')): ?>


    <ul class="thumb_nav">

      <?php while(has_sub_field('photos')): ?>

      		<?php 
      		$attachment_id = get_sub_field('photo');
      		$thumb = wp_get_attachment_image_src($attachment_id, 'slideshow-thumb'); 
      		$big = wp_get_attachment_image_src($attachment_id, 'full'); ?>

        <li class="slide_thumb" data-img="<?php echo $big[0]?>">
        	<img src="<?php echo $thumb[0]?>" />
        </li>

      <?php endwhile; ?>

    </ul>

    <ul class="gallery-footer">

       <?php while(has_sub_field('photos')): ?>

          <li>
          
            <h4><?php bloginfo('title');?></h4>
            
            <?php $credit = get_field('photo_credit'); $url = get_field('photo_url'); if ($credit) { ?>
            <p>Photography by: <?php if($url) { ?><a href="<?php echo $url; ?>"><?php } ?><?php echo $credit;?><?php if($url) { ?></a><?php } ?></p>
            <?php } ?>

          </li>

      <?php endwhile; ?>

    </ul>

    <div class="gallery-title">

      <h4><?php the_title();?></h4>

    </div>

  <?php endif; ?>


<?php endwhile; ?>