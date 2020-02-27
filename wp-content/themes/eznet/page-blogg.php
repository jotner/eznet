<?php get_header(); ?>

<h1><?php wp_title(''); ?></h1>

<?php $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$args = array( 'post_type' => 'post', 'posts_per_page' => 15, 'paged' => $paged, );
$loop = new WP_Query($args);
while ($loop->have_posts()) : $loop->the_post(); ?>

<div class="columns news is-full">
  <div class="column is-one-third news-thumb">
    <figure class="image is-3by2"><?php the_post_thumbnail(); ?></figure>
  </div>
  <div class="column">
    <h2 class="news-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
        <?php the_title(); ?></a></h2>
    <?php the_excerpt(); ?>
    <ul class="news-fa-list">
      <li>
        <i class="far fa-user"></i> <span class="news-author"><?php the_author_posts_link(); ?></span> |
      </li>
      <li>
        <i class="far fa-clock"></i> <?php the_time(get_option('date_format'))?> |
      </li>
      <li>
        <i class="far fa-comment"></i> <?php echo get_comments_number(); ?> Kommentarer
      </li>
    </ul>
  </div>
</div>
<hr>
<?php
endwhile; ?>
<div class="column paginavi">
  <?php the_posts_pagination(array(
    'mid_size'  => 2,
    'prev_text' => __('« Föregående', 'textdomain'),
    'next_text' => __('Nästa »', 'textdomain'),
  )); ?>
</div>
<?php wp_reset_postdata(); ?>

<?php get_footer(); ?>
