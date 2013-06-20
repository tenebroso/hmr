<?php while (have_posts()) : the_post(); ?>

  	<?php if(get_field('press_releases')): ?>
	 
		<ul class="row-fluid">
	 
		<?php while(has_sub_field('press_releases')): ?>
	 
			<li class="span3">
				<img src="<?php the_sub_field('thumbnail_image'); ?>">
				<p class="caps"><?php the_sub_field('title'); ?></p>
					<p><em><?php the_sub_field('date'); ?></em></p>
					<p>View Article: <a href="#">PDF</a> / <a href="#">On the Web</a></p>
			</li>
	 
		<?php endwhile; ?>
	 
		</ul>
	 
	<?php endif; ?>

<?php endwhile; ?>