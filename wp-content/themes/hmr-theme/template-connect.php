<?php
/*
Template Name: Connect
*/
?>
<?php get_template_part('templates/content', 'map'); ?>

<?php the_field('lead_form');?>

<hr />
<!-- 
<?php //if(get_field('contact')): ?>

	<h2 class="caps text-center">Points of Contact</h2>

	<div class="container">
 
		<ul class="row-fluid pointsOfContact">
	 
		<?php //while(has_sub_field('contact')): ?>
	 
			<li class="span3 points"><?php //the_sub_field('contact_information'); ?></li>
	 
		<?php //endwhile; ?>
	 
		</ul>

	</div>
 
<?php //endif; ?>

<hr /> -->

<?php $secondary = get_field('secondary_form'); if ($secondary) { echo $secondary; } ?>

<hr />

<p class="credit caps text-center circe">Site by: <a href="http://wcst.com/">We Cant Stop Thinking</a></p>