<?php
/*
Template Name: Order
*/
?>

	<img src="<?php the_field('header_image');?>" class="header-image">
	<div class="wrap container" role="document">
		<div class="content row-fluid">
			<div class="main <?php echo roots_main_class(); ?>" role="main">
				<?php get_template_part('templates/content', 'page'); ?>
			</div>
		</div>
	</div>
