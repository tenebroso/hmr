<footer class="content-info" role="contentinfo">
  <div class="container">
	<div class="row-fluid">

	</div>
	<div class="row-fluid">
		<div class="span12 text-center">
			<h4><?php bloginfo('title');?></h4>
		</div>
	</div>
  </div>
</footer>
<script src="http://localhost:35729/livereload.js"></script>
<?php wp_footer(); 

wp_reset_query(); 
	if(is_front_page()) { ?>

	<script>
		
	</script>
	<?php } ?>
