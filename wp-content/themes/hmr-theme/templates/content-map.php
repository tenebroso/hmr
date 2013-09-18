
<div id="map_holder">
	<div id="coverup"></div>
	<div id="map_nest"></div>
	<div class="container">
		<div class="row-fluid">
			<div class="span6 overlay">
				<h2><?php the_field('address');?></h2>
				<p><a target="_blank" href="<?php the_field('directions_url');?>"><em>Directions</em></a></p>
				<div class="row-fluid">
					<div class="span8">
						<p class="phone"><?php the_field('phone');?></p>
						<p class="email"><a href="mailto:<?php the_field('email');?>"><?php the_field('email');?></a></p>
						<p class="form"><a class="scroll" href="#form">Event Info Form</a></p>
					</div>
					<div class="span4">
						<ul class="social-icons">
							<li class="twitter"><a href="<?php the_field('twitter');?>">Twitter</a></li>
							<li class="facebook"><a href="<?php the_field('facebook');?>">Facebook</a></li>
							<li class="pinterest"><a href="<?php the_field('pinterest');?>">Pinterest</a></li>
							<li class="instagram"><a href="<?php the_field('instagram_url');?>">Instagram</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>