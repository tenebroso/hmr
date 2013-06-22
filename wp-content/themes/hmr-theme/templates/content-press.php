
<?php 	
		$id = get_the_ID();
		$thumb_image_url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'press-thumb'); 
		$large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'large');
		$full_image_url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full');?>

<article class="span3">
	<a href="#modal-<?php echo $id; ?>" data-toggle="modal">
		<img src="<?php echo $thumb_image_url[0]; ?>" />
	</a>
   	<p class="circe">
   		<span class="caps"><?php the_title();?></span><br />
   			<em class="arvo"><?php the_field('publication_date');?></em>
   		</a>
   	</p>
   	<p class="arvo view-article">View Article: <a href="#">PDF</a> / <a href="#">On the Web</a></p>
</article>

<div id="modal-<?php echo $id; ?>" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
  	</div>
	<div class='modal-body'>
		<img src="<?php echo $large_image_url[0]; ?>">
	</div>
	<div class="modal-footer">
		<a class="btn btn-small circe caps" href="<?php echo $thumb_image_url[0]; ?>" target="_blank">View full size</a>
	</div>
</div>