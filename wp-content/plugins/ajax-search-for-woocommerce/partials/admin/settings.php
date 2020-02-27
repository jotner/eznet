<?php
// Exit if accessed directly
if ( ! defined('ABSPATH')) {
    exit;
}

$advSettings = DGWT_WCAS()->settings->canSeeAdvSettings();
?>
<div class="wrap dgwt-wcas-settings<?php echo dgoraAsfwFs()->is_premium() ? ' dgwt-wcas-settings-pro' : ''; ?>">


    <h2 class="dgwt-wcas-settings__head">
        <span class="dgwt-wcas-settings__title">
        <?php
        if (dgoraAsfwFs()->is_premium()) {
            _e('AJAX Search for WooCommerce (PRO) Settings', 'ajax-search-for-woocommerce');
        } else {
            _e('AJAX Search for WooCommerce Settings', 'ajax-search-for-woocommerce');
        }
        ?>
        </span>
        <span class="dgwt-wcas-settings__advanced js-dgwt-wcas-settings__advanced">
            <span class="js-dgwt-wcas-adv-settings-toggle woocommerce-input-toggle woocommerce-input-toggle--<?php echo $advSettings ? 'enabled' : 'disabled'; ?>"><?php _e('Show advanced settings', 'ajax-search-for-woocommerce'); ?></span>
            <span class="dgwt-wcas-adv-settings-label"><?php _e('Show advanced settings', 'ajax-search-for-woocommerce'); ?></span>
         </span>
    </h2>


    <?php echo DGWT_WCAS()->backwardCompatibility->notice(); ?>


    <?php $settings->show_navigation(); ?>

    <div class="dgwt-wcas-settings-body js-dgwt-wcas-settings-body">
        <?php
        require DGWT_WCAS_DIR . 'partials/admin/search-preview.php';
        $settings->show_forms();
        ?>
    </div>

</div>