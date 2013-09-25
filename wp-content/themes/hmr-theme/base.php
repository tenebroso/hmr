<?php get_template_part('templates/head'); ?>
<body <?php body_class(); ?>>

  <!--[if lt IE 7]><div class="alert">Welcome! Unfortunately, your browser is <em>very</em> old. Please <a href="http://browsehappy.com/">upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</div><![endif]-->

  <?php
    do_action('get_header');
    // Use Bootstrap's navbar if enabled in config.php
    if (current_theme_supports('bootstrap-top-navbar')) {
      get_template_part('templates/header-top-navbar');
    } else {
      get_template_part('templates/header');
    }
  ?>

  <?php if(is_page('connect')) { ?>

    <?php include roots_template_path(); ?>

  <?php } else { ?>

  <?php if (is_post_type_archive('team')) {  get_template_part('templates/content', 'team-hero'); } ?>
  <?php if(is_post_type_archive('team')) { ?> <div id="team-members"><?php } ?>

    <div class="wrap container" role="document">
      <div class="content row-fluid">
        <div class="main <?php echo roots_main_class(); ?>" role="main">
          <?php include roots_template_path(); ?>
        </div><!-- /.main -->
        <?php if (roots_display_sidebar()) : ?>
        <aside class="sidebar <?php echo roots_sidebar_class(); ?>" role="complementary">
          <?php include roots_sidebar_path(); ?>
        </aside><!-- /.sidebar -->
        <?php endif; ?>
      </div><!-- /.content -->
    </div><!-- /.wrap -->
    <?php if (is_post_type_archive('team')) {  ?></div></div><?php } ?>

  <?php } ?>

  <?php get_template_part('templates/footer'); ?>

</body>
</html>
