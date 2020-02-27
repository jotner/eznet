<?php get_header(); ?>

<section class="home-section">
  <?php
  echo do_shortcode('[metaslider id="40"]');
  echo do_shortcode('[bestSeller]');
  echo do_shortcode('[onSale]');
  ?>
  <h2 class="title is-4">Nyheter</h2>

  <div class="columns is-multiline">
    <?php
    if (have_posts()) :
      while (have_posts()) : the_post(); ?>
      <div class="column is-one-third">
        <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><figure class="image is-3by2"><?php the_post_thumbnail(); ?></figure></a>
        <div style="padding: 5px">
          <h2 class="home-news-title">
            <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
          </h2>
          <span class="home-news-date"><?php the_time(get_option('date_format'))?></span>
        </div>
      </div>
      <?php
      endwhile;
    endif; ?>
  </div>

<?php get_footer(); ?>
