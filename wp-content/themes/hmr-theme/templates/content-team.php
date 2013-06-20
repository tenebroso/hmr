
<?php $image_url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'thumbnail'); ?>

<article class="span2">
	<?php the_post_thumbnail('thumbnail');?>
   	<p class="circe large">
   		<a href="<?php the_permalink();?>"><?php the_title();?><br />
   			<em class="arvo"><?php the_field('title');?></em>
   		</a></p>
</article>
