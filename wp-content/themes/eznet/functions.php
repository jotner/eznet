<?php
// THUMBNAILS
// --------------------------------------------------
add_theme_support('post-thumbnails');

// WOOCOMMERCE
// --------------------------------------------------
function customtheme_add_woocommerce_support()
{
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'customtheme_add_woocommerce_support' );

// WOOCOMMERCE CART TEXT
// --------------------------------------------------
function woo_custom_cart_button_text() {
    return __('Köp', 'woocommerce');
}
add_filter('woocommerce_product_single_add_to_cart_text', 'woo_custom_cart_button_text');
add_filter( 'add_to_cart_text', 'woo_custom_cart_button_text');
add_filter( 'woocommerce_product_add_to_cart_text', 'woo_custom_cart_button_text');

// WOOCOMMERCE SALE PERCENTAGE TAG
// --------------------------------------------------
add_filter( 'woocommerce_sale_flash', 'add_percentage_to_sale_bubble', 20 );
function add_percentage_to_sale_bubble( $html ) {
    global $product;

    if ($product->is_type('simple')) {
      $percentage = round( ( ( $product->regular_price - $product->sale_price ) / $product->regular_price ) * 100 ).'%';
    } else {
      $percentage = get_variable_sale_percentage( $product );
    }

    $output =' <span class="onsale">-'.$percentage.'</span>';
    return $output;
}

function get_variable_sale_percentage( $product ) {
    $variation_min_regular_price    = $product->get_variation_regular_price('min', true);
    $variation_max_regular_price    = $product->get_variation_regular_price('max', true);
    $variation_min_sale_price       = $product->get_variation_sale_price('min', true);
    $variation_max_sale_price       = $product->get_variation_sale_price('max', true);
    $lower_percentage   = round( ( ( $variation_min_regular_price - $variation_min_sale_price ) / $variation_min_regular_price ) * 100 );
    $higher_percentage  = round( ( ( $variation_max_regular_price - $variation_max_sale_price ) / $variation_max_regular_price ) * 100 );
    $percentages = array($lower_percentage, $higher_percentage);
    sort($percentages);

    if ($percentages[0] != $percentages[1] && $percentages[0]) {
      return $percentages[0].'% - '.$percentages[1].'%';
    } else {
      return $percentages[1].'%';
    }
}

// WOOCOMMERCE WISHLIST
// --------------------------------------------------
function add_account_menu_items( $items ) {
 $items['wishlist'] = __( 'Önskelista', 'text_domain' );
 return $items;
}
add_filter( 'woocommerce_account_menu_items', 'add_account_menu_items', 10, 1);

function action_yith_wcwl_before_wishlist_form( $wishlist_meta ) {
    $previous = "javascript:history.go(-1)";
    if(isset($_SERVER['HTTP_REFERER'])) {
      $previous = $_SERVER['HTTP_REFERER'];
    }
    ?><a href="<?= $previous ;?>">Gå tillbaka</a><?
};

add_action( 'yith_wcwl_after_wishlist_form', 'action_yith_wcwl_before_wishlist_form', 10, 1 );

// STYLES AND SCRIPTS
// --------------------------------------------------
function my_theme_enqueue_style()
{
    wp_enqueue_style(
        'bulma',
        get_stylesheet_directory_uri() . '/css/bulma.css'
        );
    wp_enqueue_script(
        'jquery'
        );
    wp_enqueue_style(
        'stylesheet',
        get_stylesheet_uri()
        );
    wp_enqueue_style(
        'font-awesome',
        '//use.fontawesome.com/releases/v5.5.0/css/all.css'
        );
    wp_enqueue_script(
        'script',
        get_stylesheet_directory_uri() . '/js/script.js',
        array( 'jquery' ),
        false,
        true
        );
}
add_action('wp_enqueue_scripts', 'my_theme_enqueue_style');

// EXCERPT
// --------------------------------------------------
function wpdocs_custom_excerpt_length($length)
{
    return 30;
}
add_filter('excerpt_length', 'wpdocs_custom_excerpt_length', 999);

function new_excerpt_more($more)
{
    return '...';
}
add_filter('excerpt_more', 'new_excerpt_more', 21);

function the_excerpt_more_link($excerpt)
{
    $post = get_post();
    $excerpt .= '<a style=" font-size: 12px;" href="'. get_permalink($post->ID) . '">Läs mer »</a>';
    return $excerpt;
}
add_filter('the_excerpt', 'the_excerpt_more_link', 21);

// WIDGETS
// --------------------------------------------------
function el_widgets_init()
{
    register_sidebar(array(
        'name'          => __('Footer Left'),
        'id'            => 'footer-1',
        'description' 	=> 'Appears in the footer area.',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget'  => '</aside>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ));

    register_sidebar(array(
        'name'          => __('Footer Middle'),
        'id'            => 'footer-2',
        'description' 	=> 'Appears in the footer area.',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget'  => '</aside>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ));

    register_sidebar(array(
        'name'          => __('Footer Right'),
        'id'            => 'footer-3',
        'description' 	=> 'Appears in the footer area.',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget'  => '</aside>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ));

    register_sidebar(array(
        'name'          => __('Search'),
        'id'            => 'search',
        'description'	 	=> 'The standard searchform.',
    ));

    register_sidebar(array(
        'name'          => __('Product Sidebar'),
        'id'            => 'product-sidebar',
        'description'         => 'Sidebar for products.',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget'  => '</aside>',
    ));

    register_sidebar(array(
        'name'          => __('Social Media Sidebar'),
        'id'            => 'social-sidebar',
        'description'	 	=> 'Sidebar for social media.',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget'  => '</aside>',
    ));
}
add_action('widgets_init', 'el_widgets_init');

// MENUS
// --------------------------------------------------
function register_my_menus()
{
    register_nav_menus(
        array(
      'nav-menu' => __('Navigation Menu'),
    )
  );
}
add_action('init', 'register_my_menus');

function my_wp_nav_menu_args( $args = '' ) {
  if ( $args['theme_location'] == 'nav-menu' ) {
    if( is_user_logged_in() ) {
      $args['menu'] = 'Logged In';
    } else {
      $args['menu'] = 'Logged Out';
    }
  }
  return $args;
}
add_filter( 'wp_nav_menu_args', 'my_wp_nav_menu_args' );

// WP LOGO REMOVAL
// --------------------------------------------------
add_action('admin_bar_menu', 'remove_wp_logo', 999);
function remove_wp_logo($wp_admin_bar)
{
    $wp_admin_bar->remove_node('wp-logo');
}

// ADMIN PANEL WIDGETS
// --------------------------------------------------
function remove_dashboard_widgets()
{
    global $wp_meta_boxes;

    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_drafts']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity']);
}

add_action('wp_dashboard_setup', 'remove_dashboard_widgets');

function add_custom_dashboard_widgets()
  {
    wp_add_dashboard_widget(
        'welcome_dashboard_widget',
        'Välkommen!',
        'custom_dashboard_widget_content'
    );
  }

add_action('wp_dashboard_setup', 'add_custom_dashboard_widgets');
function custom_dashboard_widget_content()
  {
    echo "<p>Det här är adminpanelen för Eznet AB.</p>";
  }

// ADMIN PANEL FOOTER
// --------------------------------------------------
function change_admin_footer()
{
    echo '';
}
add_filter('admin_footer_text', 'change_admin_footer');

// CUSTOM POST TYPES
// --------------------------------------------------
function create_post_type()
{
    register_post_type(
      'butik',
      array(
        'labels' => array(
          'name' => __('Butiker'),
          'singular_name' => __('butik'),
          'rewrite' => array('slug' => 'butik','with_front' => false),
        ),
        'public' => true,
        'has_archive' => true,
        'add_new_item' => 'Lägg till ny butik',
        'add_new' => 'Ny butik',
        'new_item' => 'Ny butik',
        'menu_icon'	=> 'dashicons-admin-home',
        'menu_position' => 4,
        'supports' => array( 'title', 'editor', 'thumbnail' ),
      )
    );
}
add_action('init', 'create_post_type');

// ADMIN PANEL COLOR SCHEMES
// --------------------------------------------------
function set_default_admin_color($user_id) {
	$args = array(
		'ID' => $user_id,
		'admin_color' => 'Default'
	);
	wp_update_user( $args );
}
add_action('user_register', 'set_default_admin_color');

if ( !current_user_can('manage_options') )
remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );

add_filter('show_admin_bar', '__return_false');
?>
