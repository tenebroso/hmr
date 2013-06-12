
<?php $image_url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'thumbnail'); ?>

<article class="span2">
	<div class="unhide" style="display:none">
  		<div style="background: url(<?php echo $image_url[0]; ?>)"><a href="<?php the_permalink();?>"></a></div>
	</div>
   	<p class="circe large">
   		<a href="<?php the_permalink();?>"><?php the_title();?><br />
   			<em class="arvo"><?php the_field('title');?></em>
   		</a></p>
</article>
