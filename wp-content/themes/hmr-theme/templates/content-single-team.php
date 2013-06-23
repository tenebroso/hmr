<?php while (have_posts()) : the_post(); $email = get_field('email'); ?>
  <article <?php post_class(); ?>>
    <div class="row-fluid">
      <div class="span4">
        <?php the_post_thumbnail('medium');?>
        <p class="back"><a href="../">&laquo; Back to Our Team</a></p>
      </div>
      <div class="span8">
    <header>
      <div class="row-fluid">
        <div class="span6">
          <h1 class="entry-title caps"><?php the_title(); ?></h1>
          <p class="meta"><em><?php the_field('title');?></em></p>
        </div>
        <div class="span6 text-right">
          <p class="meta match-height"><a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a></p>
          <p class="meta"><?php the_field('phone');?></p>
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
