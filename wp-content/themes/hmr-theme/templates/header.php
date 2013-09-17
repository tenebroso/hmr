<div class="scrollTop">

  <header class="banner" role="banner">
    <div class="container">
      <a class="brand visible-phone" href="<?php echo home_url(); ?>/"><?php bloginfo('name'); ?></a>
      <nav id="navbar" class="nav-main hidden-phone" role="navigation">
        <?php
          if (has_nav_menu('primary_navigation')) :
            wp_nav_menu(array('theme_location' => 'primary_navigation', 'menu_class' => 'nav nav-pills'));
          endif;
        ?>
      </nav>
       <nav id="navbar-mobile" class="nav-main" role="navigation">
        <?php
          if (has_nav_menu('primary_navigation')) :
            wp_nav_menu(array('theme_location' => 'mobile', 'menu_class' => 'nav nav-pills'));
          endif;
        ?>
      </nav>
    </div>
  </header>



  

  <?php if(is_page(array('27','29','124','126','122','11','297','295','293','329','573','575')) || is_post_type_archive('team') ||  is_singular('team') || is_singular('capability') ) { get_template_part('templates/header', 'sub-nav'); } ?>

  <div class="clearfix"></div>

</div>