
<?php $image_url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'thumbnail'); $custom = get_field('custom_thumbnail');?>

<article class="span2">
	<a href="<?php the_permalink();?>">
		<span class="hidden-phone">
			
			<?php if($custom) { ?>
				<img src="<?php echo $custom; ?>" class="custom-thumb">
			<?php } else { ?>
				<?php the_post_thumbnail('thumbnail');?>
			<?php } ?>
		</span>
	   	<p>
			<span class="name"><?php the_title();?></span>
			<span class="title"><?php the_field('title');?></span>
		</p>
	</a>
</article>