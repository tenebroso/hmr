<div class="row-fluid first-row">
<?php wp_reset_query(); $args = array('posts_per_page' => 4, 'post_type' => 'page', 'post__in' => array('11','293','295','297') ); $the_query = new WP_Query( $args ); if ( $the_query->have_posts() ) : while ( $the_query->have_posts() ) : $the_query->the_post(); ?>

	<div class="span3 capability">

		<a href="<?php the_permalink();?>">

			<img src="<?php the_field('featured_image'); ?>" />

			<h2><?php the_title(); ?></h2>

			<?php //the_content();?>

			<!--<div class="hover caps">View Gallery</div>-->

		</a>

	</div>

<?php endwhile; endif; wp_reset_postdata(); ?>

</div>