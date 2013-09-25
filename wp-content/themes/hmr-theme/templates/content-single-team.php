<?php while (have_posts()) : the_post(); $email = get_field('email'); $pinterest = get_field('pinterest_url'); ?>
  <article <?php post_class(); ?>>
    <div class="row-fluid">
      <div class="span4">
        <?php the_post_thumbnail('medium');?>
        <p class="back"><a href="../#full">&laquo; Back to Our Team</a></p>
      </div>
      <div class="span8">
    <header>
      <div class="row-fluid">
        <div class="span6">
          <h1 class="entry-title caps"><?php the_title(); ?></h1>
          <p class="meta"><?php the_field('title');?></p>
        </div>
        <div class="span6 text-right">
          <?php if($pinterest) { ?>
              <ul class="social-icons">
                <li class="pinterest"><a href="<?php echo $pinterest; ?>">Pinterest</a></li>
              </ul>
          <?php } ?>
        </div>
    </header>
    <hr />
    <div class="entry-content">
      <?php the_content(); ?>
    </div>
  </div>
</div>
  </article>
<?php endwhile; ?>
