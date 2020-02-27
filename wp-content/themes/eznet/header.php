<!DOCTYPE html>
<html class="html" lang="en" dir="ltr">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
      <?php wp_title('-', true, 'right'); ?><?php bloginfo('title'); ?>
    </title>
    <?php wp_head(); ?>
  </head>

  <body <?php body_class(); ?>>

    <?php
    if (is_user_logged_in()) {
      $curauth = wp_get_current_user();
    } ?>

    <div>
      <nav class="navbar mobile-nav">
        <div class="navbar-brand">
          <a class="navbar-item" href="<?php echo home_url(); ?>">
            <img src="<?php echo get_template_directory_uri(); ?>/images/logo.png" alt="Logotyp">
          </a>
          <div class="navbar-burger burger" data-target="navbar-mobile">
            <span></span>
            <span></span>
            <span></span>
          </div>
        </div>
          <!-- <?php get_search_form(); ?> -->
          <?php echo do_shortcode('[wcas-search-form]'); ?>
          <div class="navbar-end">
            <a class="navbar-item menu-profile" href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>"><?php echo get_avatar($curauth->user_email, '60'); ?></a>
            <a class="navbar-item menu-cart" href="<?php echo wc_get_cart_url(); ?>"><i class="fas fa-shopping-cart menu-cart-i"></i><?php if (WC()->cart->get_cart_contents_count() > 0) { ?>
              <span class="cart-number"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
            <?php } ?></a>
          </div>
      </nav>

      <nav class="navbar navigation">
        <div class="navbar-brand">
          <div class="navbar-menu">
            <?php wp_nav_menu(array( 'theme_location' => 'nav-menu' )); ?>
          </div>
        </div>
      </nav>

      <nav id="navbar-mobile" class="navbar mobile">
          <?php wp_nav_menu(array( 'theme_location' => 'nav-menu' )); ?>
      </nav>
    </div>

    <div class="container wrap">
      <div class="columns">
        <div class ="column is-one-fifth">
          <?php dynamic_sidebar('product-sidebar'); ?>
        </div>
        <div class ="column is-four-fifths">
