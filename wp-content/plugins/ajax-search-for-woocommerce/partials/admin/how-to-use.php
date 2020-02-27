<?php

use DgoraWcas\Admin\Promo\Upgrade;

// Exit if accessed directly
if ( ! defined('ABSPATH')) {
    exit;
}

?>
<h4><?php _e('There are three easy ways to display the search box in your theme', 'ajax-search-for-woocommerce'); ?>: </h4>
<p>1. <?php printf(__('As a widget - go to the %s and choose "Woo AJAX Search"', 'ajax-search-for-woocommerce'), '<a href="' . admin_url('widgets.php') . '">' . __('Widgets Screen', 'ajax-search-for-woocommerce') . '</a>') ?>
<p>2. <?php printf(__('By shortcode - %s', 'ajax-search-for-woocommerce'),'<code>[wcas-search-form]</code>'); ?></p>
<p>3. <?php printf(__('By PHP - %s', 'ajax-search-for-woocommerce'),'<code>&lt;?php echo do_shortcode(\'[wcas-search-form]\'); ?&gt;</code>'); ?></p>

<?php if ( ! dgoraAsfwFs()->is_premium()): ?>
    <span class="dgwt-wcas-our-devs"><?php printf(__('Are there any difficulties? <a href="%s">Upgrade now</a> and our developers will do everything for you.', 'ajax-search-for-woocommerce'), Upgrade::getUpgradeUrl()); ?></span>
<?php endif; ?>