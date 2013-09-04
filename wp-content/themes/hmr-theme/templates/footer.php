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
			<p class="copyright">&copy; 2013 HMR Designs</p>
		</div>
		<div class="span6 text-center">
			<h4><a href="<?php bloginfo('url');?>"><?php bloginfo('title');?></a></h4>
		</div>
		<div class="span3 text-right">
			<ul class="social-icons">
				<li class="facebook"><a href="<?php the_field('facebook');?>">Facebook</a></li>
				<li class="twitter"><a href="<?php the_field('twitter');?>">Twitter</a></li>
				<li class="instagram"><a href="<?php the_field('instagram_url');?>">Instagram</a></li>
				<li class="pinterest"><a href="<?php the_field('pinterest');?>">Pinterest</a></li>
			</ul>
		</div>
	</div>
</div>
</footer>

<?php wp_footer(); ?>
