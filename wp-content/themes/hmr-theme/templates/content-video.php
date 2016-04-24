
<?php 	
		$id = get_the_ID();
		$thumb_image_url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'press-thumb'); 
		//$large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'large');
		//$full_image_url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full');
		//$pdf = get_field('pdf');
		$url = get_field('url');
		?>
		
<article class="span3 hover">
	<a href="#modal-<?php echo $id; ?>" data-toggle="modal">
		<img src="<?php echo $thumb_image_url[0]; ?>" />
	</a>
	<div class="hidden">
		<p><a href="#modal-<?php echo $id; ?>" data-toggle="modal">View Video</a></p>
	</div>
   	<p class="circe">
   		<span class="caps"><?php the_title();?></span><br />
   			<em class="arvo"><?php the_field('publication_date');?></em>
   	</p>
   	<!--<p class="arvo view-article">View Video:<?php if($pdf) { ?> <a href="<?php echo $pdf; ?>">PDF</a><?php if ($url && $pdf) { ?> /<?php } ?><?php } if ($url) { ?> <a href="<?php echo $url; ?>">On the Web</a><?php } ?></p>-->
</article>

<div id="modal-<?php echo $id; ?>" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
  	</div>
	<div class='modal-body'>
		<?php echo $url; ?>
	</div>
	<div class="modal-footer">
		<!--<a class="btn btn-small circe caps" href="<?php echo $full_image_url[0]; ?>" target="_blank">View full size</a>-->
	</div>
</div>
