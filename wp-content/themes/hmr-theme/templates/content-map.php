
<div id="map_holder">
	<div id="coverup"></div>
	<div id="map_nest"></div>
	<div class="container">
		<div class="row-fluid">
			<div class="span6 overlay">
				<h2><?php the_field('address',13);?></h2>
				<p><a target="_blank" href="<?php the_field('directions_url',13);?>"><em>Directions</em></a></p>
				<div class="row-fluid">
					<div class="span8">
						<p class="phone"><?php the_field('phone',13);?></p>
						<p class="email"><a href="mailto:<?php the_field('email',13);?>"><?php the_field('email',13);?></a></p>
						<?php if(is_page('employment')) { ?>
							<p class="form"><a class="scroll" href="#form">Employment Form</a></p>
						<?php } else { ?>
							<p class="form"><a class="scroll" href="#form">Event Info Form</a></p>
						<?php } ?>
					</div>
					<div class="span4">
						<ul class="social-icons">
							<li class="facebook"><a href="<?php the_field('facebook',13);?>">Facebook</a></li>
							<li class="instagram"><a href="<?php the_field('instagram_url',13);?>">Instagram</a></li>
							<li class="pinterest"><a href="<?php the_field('pinterest',13);?>">Pinterest</a></li>
							<li class="twitter"><a href="<?php the_field('twitter',13);?>">Twitter</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>