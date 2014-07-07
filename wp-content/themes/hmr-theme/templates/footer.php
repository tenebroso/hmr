</div><!-- end #wrap -->
<footer class="content-info" role="contentinfo">
  <div class="container">
	<!--<div class="row-fluid">
		<div class="span6 text-right border-right">

			<?php the_field('footer_-_left_content','options');?>

		</div>
		<div class="span6">

			<?php the_field('footer_-_right_content','options');?>

		</div>
	</div>-->
	<div class="row-fluid">
		<div class="span3 text-left">
			<p class="copyright">&copy; <?php the_time('Y');?> HMR Designs</p>
		</div>
		<div class="span6 text-center">
			<h4><a href="<?php bloginfo('url');?>"><?php bloginfo('title');?></a></h4>
		</div>
		<div class="span3 text-right">
			<ul class="social-icons">
				<li class="facebook"><a href="https://www.facebook.com/pages/HMR-Designs/111548372234921" target="_blank">Facebook</a></li>
				<li class="instagram"><a href="http://instagram.com/hmrdesigns/" target="_blank">Instagram</a></li>
				<li class="pinterest"><a href="http://pinterest.com/hmrdesigns/" target="_blank">Pinterest</a></li>
				<li class="twitter"><a href="https://twitter.com/HMR_Designs" target="_blank">Twitter</a></li>
			</ul>
		</div>
	</div>
</div>
</footer>

<?php wp_reset_query(); 

if(is_front_page()) { 

	$rows = get_field('homepage_slides');
	if($rows)
	{ $count = 1;
		?>

		<script>
		var items = [<?php foreach($rows as $row) { ?>{ img: "<?php echo $row['background_image'] ?>", words: ".slide<?php echo $count++;?>"},<?php }?>];
		</script>

<?php } } ?>

<?php wp_footer(); ?>