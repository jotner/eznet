<?php get_header(); ?>

<?php
if (have_posts()):
  while (have_posts()):
    the_post();
    $thumbnail = wp_get_attachment_url(get_post_thumbnail_id($post->ID)); ?>

<article>
  <div class="news-single">
    <h1 class="news-title-single">
      <?php the_title(); ?>
    </h1>
    <ul class="news-fa-list">
      <li>
        <i class="far fa-user"></i> <span class="news-author"><?php the_author_posts_link(); ?></span> |
      </li>
      <li>
        <i class="far fa-clock"></i> <?php the_time(get_option('date_format'))?>
      </li>
    </ul>
    <hr>
    <figure class="image is-2by2 thumb"><?php the_post_thumbnail('array(800, 800)'); ?></figure>
    <br>
    <?php the_content(); ?>
  </div>
</article>
<?php endwhile;
  else : _e('Det finns ingen artikel att visa.', 'textdomain');
endif; ?>

</div>


<?php get_footer(); ?>
