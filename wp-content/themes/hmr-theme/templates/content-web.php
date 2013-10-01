
<?php 	
		$id = get_the_ID();
		$thumb_image_url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'press-thumb'); 
		//$large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'large');
		//$full_image_url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full');
		//$pdf = get_field('pdf');
		$url = get_field('url');
		?>
		
<article class="span3 hover">
	<a href="<?php echo $url; ?>" target="_blank">
		<img src="<?php echo $thumb_image_url[0]; ?>" />
	</a>
	<!--<div class="hidden">
		<p><a href="#modal-<?php echo $id; ?>" data-toggle="modal">View Press</a></p>
	</div>-->
   	<p class="circe">
   		<span class="caps"><?php the_title();?></span>
   	</p>
   	<p class="arvo view-article"><a href="<?php echo $url; ?>" target="_blank">View Website</a></p>
</article>