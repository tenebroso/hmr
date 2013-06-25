</div><!-- end #wrap -->
<footer class="content-info" role="contentinfo">
  <div class="container">
	<div class="row-fluid">
		<div class="span6 text-right border-right">

			<?php the_field('footer_-_left_content','options');?>

		</div>
		<div class="span6">

			<?php the_field('footer_-_right_content','options');?>

		</div>
	</div>
	<div class="row-fluid">
		<div class="span12 text-center">

			<h4><?php bloginfo('title');?></h4>

		</div>
	</div>
  </div>
</footer>

<?php wp_footer(); ?>
