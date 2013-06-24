<?php
/*
Template Name: Connect
*/
?>
<?php get_template_part('templates/content', 'map'); ?>

<?php the_field('lead_form');?>

<hr />

<?php if(get_field('contact')): ?>

	<h2>Points of Contact</h2>
 
	<ul class="row-fluid">
 
	<?php while(has_sub_field('contact')): ?>
 
		<li class="span3"><?php the_sub_field('contact_information'); ?></li>
 
	<?php endwhile; ?>
 
	</ul>
 
<?php endif; ?>

<hr />


<?php $secondary = get_field('secondary_form'); if ($secondary) { echo $secondary; } ?>