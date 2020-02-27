<?php get_header(); ?>

<?php $loop = new WP_Query( array( 'post_type' => 'butik', 'posts_per_page' => 10 ) ); ?>

<?php while ( $loop->have_posts() ) : $loop->the_post(); ?>

  <h2 class="title">
    <?php the_title(); ?>
  </h2>
  <div class="columns">
    <div class="column is-4">
      <?php the_content(); ?>
      <p class="subtitle">Öppettider</p>
      <?php echo get_field('oppettider'); ?>
    </div>
      <div class="column is-4">
        <p class="subtitle">Adress</p>
        <?php echo get_field('adress'); ?>
      </div>
    <div class="column is-4">
     <?php echo the_post_thumbnail(); ?>
    </div>
  </div>
  <div class="columns">
    <div class="column">
      <?php echo get_field('karta'); ?>
    </div>
  </div>
  <hr>

<?php endwhile; ?>

<?php get_footer(); ?>
