
<div class="sub-nav">

	<div class="container">

	<?php if(is_page(array('27')) || is_post_type_archive('team') ) { 
		wp_nav_menu( array('menu' => 'Who We Are Sub-Nav' ));
	} ?>

	<?php if(is_page(array('10','124','126','122'))) { 
		wp_nav_menu( array('menu' => 'What We Do Sub-Nav' ));
	} ?>

	</div>

</div>