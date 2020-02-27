<?php
/*
Plugin Name: On Sale
Description: Slider for on sale products.
Author: André Ingman, Jonathan Eriksson, Johan Westin
Version: 1.0
*/

function on_sale() {
  $args = array(
    'post_type' => 'product',
    'meta_query'     => array(
      'relation' => 'OR',
      array( // Simple products type
        'key'           => '_sale_price',
        'value'         => 0,
        'compare'       => '>',
        'type'          => 'numeric'
      ),
      array( // Variable products type
        'key'           => '_min_variation_sale_price',
        'value'         => 0,
        'compare'       => '>',
        'type'          => 'numeric'
      )
    ),
    'orderby' => 'meta_value_num',
    'posts_per_page' => 10,
  );
  $loop = new WP_Query( $args );
  ?>
  <div class="slideshow">
    <h2 class="best-seller-title title is-4">Reavaror</h2>
  <section class="best-seller swiper-container">
    <div class="swiper-wrapper">
    <?php
    while ( $loop->have_posts() ) : $loop->the_post();
      global $product;
      ?>
      <div class="swiper-slide">
        <a href="<?php the_permalink();?>"><figure class="card-image">
          <?php echo get_the_post_thumbnail( $loop->post->ID, 'shop_catalog' ); ?>
        </figure></a>
        <ul class="woocommerce">
          <li>
            <?php if ($average = $product->get_average_rating()) : ?>
            <?php echo '<div class="star-rating bs-rating" title=""><span style="width:'.( ( $average / 5 ) * 100 ) . '%"><strong itemprop="ratingValue" class="rating">'.$average.'</strong> '.__( 'out of 5', 'woocommerce' ).'</span></div>'; ?>
          <?php endif; ?>
          </li>
        </ul>
        <div class="card-content">
          <h2 class="best-seller-title">
            <a href="<?php the_permalink();?>"><?php echo wp_trim_words($product->get_title(),  $num_words = 5,  $more = null ); ?></a>
          </h2>
          <div class="bs-content">
            <p class="content"><?php echo wp_trim_words($product->get_description(),  $num_words = 5,  $more = null ); ?></p>
            <div class="bottom-content">
              <p><s><?php echo $product->get_regular_price(); ?>kr</s></p>
              <div class="price-tag">
                <p class="best-seller-price">Nu: <?php echo $product->get_sale_price(); ?>kr</p>
                <div class="arrow-right"></div>
              </div>
              <div class="link-container">
                <a class="best-seller-link" href="<?php the_permalink();?>"><p>Läs mer</p></a>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <?php
    endwhile;
    ?>
    </div>
    <!-- <div class="swiper-pagination"></div> -->
    <!-- Add Arrows -->
    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>
  </section>
  </div>
  <?php
  wp_reset_query();
  return null;
}

add_shortcode( 'onSale', 'on_sale' );

add_action( 'wp_enqueue_scripts', 'load_swiper_style', 100 );
function load_swiper_style() {
  $plugin_url = plugin_dir_url( __FILE__ );
    wp_enqueue_style( 'style', $plugin_url . 'assets/style.css' );
    wp_enqueue_style( 'swiper', $plugin_url . 'assets/swiper-5.2.1/package/css/swiper.min.css' );
}

add_action( 'wp_enqueue_scripts', 'load_swiper_scripts');
function load_swiper_scripts() {
  $plugin_url = plugin_dir_url( __FILE__ );
  wp_enqueue_script( 'swiper-js', $plugin_url . 'assets/swiper-5.2.1/package/js/swiper.min.js', array(), false ,true);
  wp_enqueue_script( 'index-js', $plugin_url . 'assets/index.js', array('swiper-js'), false ,true);
}
