<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <title><?php wp_title('|', true, 'right'); ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <?php wp_head(); ?>
  <?php if(is_front_page()) { 

	$rows = get_field('homepage_slides');
	if($rows)
	{ $count = 1;
		?>

		<style type="text/css">
		<?php foreach($rows as $row) { ?>
			.slide<?php echo $count++;?> { background:url(<?php echo $row['text_image'] ?>) no-repeat left top;}
		<?php }?>
		</style>

		<script>
			jQuery(window).on("backstretch.show", function(e, instance) {
            	jQuery('.loading').fadeOut(100);
      		});
		</script>

	<?php } } ?>
  <link rel="stylesheet" type="text/css" href="//cloud.typography.com/7033652/633062/css/fonts.css" />
  <link rel="alternate" type="application/rss+xml" title="<?php echo get_bloginfo('name'); ?> Feed" href="<?php echo home_url(); ?>/feed/">
</head>
<?php if(is_front_page()) { ?>
<div class="loading"></div>
<?php } ?>
<div id="wrap">
