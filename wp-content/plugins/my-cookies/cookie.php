<?php
/*
Plugin Name: My Cookie Notice
Description: Add a custom cookie notice on your site.
Author: Jonathan Eriksson
Version: 1.0
*/

function cookie() {
  $text = get_option('cookie_text', 'This website uses cookies to ensure you get the best experience on our website.');
  $button = get_option('cookie_button', 'Got it!');
  $days = get_option('cookie_days', 60);
  $cookie_name = "newCookie";
  $cookie_value = "cookie";

  if (!isset($_COOKIE[$cookie_name])) { ?>
    <div id="cookie">
      <p><?php echo $text; ?><br>
        <button onClick="SetCookie('<?php echo $cookie_name; ?>','<?php echo $cookie_value; ?>','<?php echo $days; ?>')"><?php echo $button; ?></button>
      </p>
    </div>
  <?php }
}
add_action('wp_footer', 'cookie');

function cookie_style() {
  wp_register_style('cookie_style', plugins_url('style.css',__FILE__ ));
  wp_enqueue_style('cookie_style');
  wp_enqueue_script('script', plugins_url('cookie.js',__FILE__));
}
add_action( 'wp_enqueue_scripts','cookie_style');

function cookie_settings() {
  register_setting( 'general', 'cookie_text' );
  register_setting( 'general', 'cookie_button' );
  register_setting( 'general', 'cookie_days' );

  add_settings_section( 'cookie-notice',
    'Cookie Settings',
    'cookie_callback' ,
    'general'
  );

  add_settings_field( 'cookie_text',
    'Text',
    'cookie_text_callback',
    'general',
    'cookie-notice'
  );

  add_settings_field( 'cookie_button',
    'Button',
    'cookie_button_callback',
    'general',
    'cookie-notice'
  );

	add_settings_field( 'cookie_days',
    'Expiration Days',
    'cookie_days_callback',
    'general',
    'cookie-notice'
  );

	function cookie_callback() {
    echo 'Change the cookie notice text, button or expiration days.';
  }

  function cookie_text_callback(){
    $setting = get_option('cookie_text', 'This website uses cookies to ensure you get the best experience on our website.');
    ?>
    <textarea rows="5" cols="40" name="cookie_text" required><?= isset($setting) ? esc_attr($setting) : ''; ?></textarea>
    <?php
  }

  function cookie_button_callback(){
    $setting = get_option('cookie_button', 'Got it!');
    ?>
    <input type="text" name="cookie_button" value="<?= isset($setting) ? esc_attr($setting) : ''; ?>" required>
    <?php
  }

	function cookie_days_callback(){
    $setting = get_option('cookie_days', 60);
    ?>
    <input type="number" name="cookie_days" value="<?= isset($setting) ? esc_attr($setting) : ''; ?>" required>
    <?php
  }
}
add_action( 'admin_init', 'cookie_settings' );
?>
