
<div class="sub-nav">

	<div class="container">

	<?php if(is_page(array('27')) || is_post_type_archive('team') ) { 
		wp_nav_menu( array('menu' => 'Who We Are Sub-Nav' ));
	} ?>

	<?php if(is_page(array('10','124','126','122')) || is_singular('capability') ) { 
		wp_nav_menu( array('menu' => 'What We Do Sub-Nav' ));
	} ?>

	<?php if(is_page(array('11','297','295','293'))) { 
		wp_nav_menu( array('menu' => 'Portfolio Sub-Nav' ));
	} ?>

	<div class="slideToggle slideUp"></div>

	</div>

</div>