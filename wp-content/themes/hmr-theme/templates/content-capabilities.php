<div class="row-fluid first-row">
<?php wp_reset_query(); $args = array('posts_per_page' => 3, 'post_type' => 'capability' ); $the_query = new WP_Query( $args ); if ( $the_query->have_posts() ) : while ( $the_query->have_posts() ) : $the_query->the_post(); ?>

	<div class="span4 capability">

		<a href="<?php the_permalink();?>">

			<?php the_post_thumbnail('full'); ?>

			<h2><?php the_title(); ?></h2>

			<?php the_content();?>

			<div class="hover caps">View Gallery</div>

		</a>

	</div>

<?php endwhile; endif; wp_reset_postdata(); ?>

<?php wp_reset_query(); $args2 = array('posts_per_page' => 3, 'offset' => 3, 'post_type' => 'capability' ); $the_query2 = new WP_Query( $args2 ); if ( $the_query2->have_posts() ) : while ( $the_query2->have_posts() ) : $the_query2->the_post(); ?>

	<div class="span4 capability">

		<a href="<?php the_permalink();?>">

			<?php the_post_thumbnail('full'); ?>

			<h2><?php the_title(); ?></h2>

			<?php the_content();?>

			<div class="hover caps">View Gallery</div>

		</a>

	</div>

<?php endwhile; endif; wp_reset_postdata(); ?>
</div>