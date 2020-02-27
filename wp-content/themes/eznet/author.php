<?php get_header(); ?>

<?php
$curauth = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author)); ?>

<div class="columns author-profile-card">
  <h2 class="author-role">
  </h2><br>
  <div class="column is-one-fifth author-photo">
    <?php echo get_avatar($curauth->user_email, '120'); ?>
  </div>
  <div class="column">
    <p>
      <h1 class="author-name"><?php echo $curauth->nickname; ?></h1>
      <br>
      <i><?php echo $curauth->user_description; ?></i>
    </p>
  </div>
</div>
<hr>

  <?php
  if (have_posts()):
    while (have_posts()):
      the_post(); ?>

      <article>
        <div class="columns news">
          <div class="column news-thumb">
              <figure class="image is-3by2"><?php the_post_thumbnail(); ?></figure>
          </div>
          <div class="column is-two-thirds">
            <h2 class="news-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
            <?php the_excerpt(); ?>
            <ul class="news-fa-list">
              <li>
                <i class="far fa-user"></i> <span class="news-author"><?php the_author_posts_link(); ?></span> |
              </li>
              <li>
                <i class="far fa-clock"></i> <?php the_time(get_option('date_format'))?>
              </li>
            </ul>
          </div>
        </div>
        <hr>
      </article>
    <?php endwhile;
      else : _e('Den här användaren har inte skrivit några inlägg.', 'textdomain');
      endif;
    ?>
    <div class="column paginavi">
    <?php the_posts_pagination(array(
      'mid_size'  => 2,
      'prev_text' => __('« Föregående', 'textdomain'),
      'next_text' => __('Nästa »', 'textdomain'),
    )); ?>
  </div>
</div>






<?php get_footer(); ?>
