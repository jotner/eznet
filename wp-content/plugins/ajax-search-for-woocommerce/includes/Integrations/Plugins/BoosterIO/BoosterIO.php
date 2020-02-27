<?php

namespace DgoraWcas\Integrations\Plugins\BoosterIO;

use DgoraWcas\Helpers;

class BoosterIO
{

    private $countriesGroups = array();
    private $currentCountry = '';

    public function init()
    {
        if ( ! class_exists('WC_Jetpack')) {
            return;
        }

        if ($this->isPricesAndCurrenciesByCountryEnabled()) {

            add_action('dgwt/wcas/readable_index/bg_processing/before_task', array($this, 'removePricingFilters'));


            add_action('template_redirect', array($this, 'setCurrentCountry'), 100);
            add_action('template_redirect', array($this, 'saveCurrentCountry'), 200);

            $this->setGroupedCountries();
            $this->formatHTMLPrice();
        }

    }

    /**
     * Module: [Prices and Currencies by Country]
     * Add extra separators to price
     *
     * @return void
     *
     */
    private function formatHTMLPrice()
    {
        add_filter('dgwt/wcas/product/html_price', function ($html, $productID) {

            $formated = '[booster.ioPricesByCountry:default]';
            $formated .= $html;
            $formated .= '[booster.ioPricesByCountry:end]';

            foreach ($this->countriesGroups as $groupID => $countries) {

                $p = new \WC_Product($productID);

                $regularPrice = get_post_meta($productID, '_wcj_price_by_country_regular_price_local_' . $groupID, true);
                $salePrice    = get_post_meta($productID, '_wcj_price_by_country_sale_price_local_' . $groupID, true);

                if (empty($regularPrice)) {
                    $regularPrice = $p->get_regular_price();
                }

                if (empty($salePrice)) {
                    $salePrice = $p->get_sale_price();
                }

                $regularPrice = wcj_price_by_country($regularPrice, $productID, $groupID);
                $salePrice    = wcj_price_by_country($salePrice, $productID, $groupID);


                $p->set_regular_price($regularPrice);
                $p->set_price($regularPrice);

                if ($salePrice) {
                    $p->set_sale_price($salePrice);
                    $p->set_price($salePrice);
                }


                $formated .= '[booster.ioPricesByCountry:group_' . implode('_', $countries) . ']';
                $formated .= $p->get_price_html();
                $formated .= '[booster.ioPricesByCountry:end]';

            }

            return $formated;
        }, 10, 2);
    }


    /**
     * Module: [Prices and Currencies by Country]
     * Check if module product by country is enabled
     *
     * @return bool
     */
    private function isPricesAndCurrenciesByCountryEnabled()
    {
        $enabled = false;

        $val = get_option('wcj_price_by_country_enabled');

        if ( ! empty($val) && $val === 'yes') {
            $enabled = true;
        }

        return $enabled;

    }

    /**
     * Module: [Prices and Currencies by Country]
     *
     * Get grouped countries
     */
    private function setGroupedCountries()
    {
        $groups       = array();
        $total_number = apply_filters('booster_option', 1, get_option('wcj_price_by_country_total_groups_number', 1));
        for ($i = 1; $i <= $total_number; $i++) {

            $countries = array();
            switch (get_option('wcj_price_by_country_selection', 'comma_list')) {
                case 'comma_list':
                    $countries = get_option('wcj_price_by_country_exchange_rate_countries_group_' . $i);
                    break;
                case 'multiselect':
                    $countries = ('' != ($group = get_option('wcj_price_by_country_countries_group_' . $i, '')) ? $group : array());
                    break;
                case 'chosen_select':
                    $countries = ('' != ($group = get_option('wcj_price_by_country_countries_group_chosen_select_' . $i, '')) ? $group : array());
                    break;
            }

            $groups[$i] = $countries;

        }

        $this->countriesGroups = $groups;
    }

    /**
     * Module: [Prices and Currencies by Country]
     *
     * Remove filters for indexer
     *
     * @return void;
     */
    public function removePricingFilters()
    {
        $priority = wcj_get_module_price_hooks_priority('price_by_country');
        Helpers::removeFiltersForAnonymousClass('woocommerce_product_get_price', 'WCJ_Price_by_Country_Core', 'change_price', $priority);
        Helpers::removeFiltersForAnonymousClass('woocommerce_product_get_sale_price', 'WCJ_Price_by_Country_Core', 'change_price', $priority);
        Helpers::removeFiltersForAnonymousClass('woocommerce_product_get_regular_price', 'WCJ_Price_by_Country_Core', 'change_price', $priority);

    }

    /**
     * Set current country group based on IP, shipping or billing address
     *
     * @return void
     */
    public function sdsetcurrentCountry()
    {

    }

    /**
     * Modified method get_customer_country_group_id of WCJ_Price_by_Country_Core class
     *
     * @see booster-plus-for-woocommerce\includes\price-by-country\class-wcj-price-by-country-core.php LINE 294
     * Original version 4.1.0
     */
    public function setCurrentCountry()
    {

        if (
            ! function_exists('WC')
            || 'yes' === get_option('wcj_price_by_country_revert', 'no') && is_checkout()
        ) {
            return;
        }

        if ( ! empty($this->currentCountry)) {
            return $this->currentCountry;
        }

        $country = '';

        if (is_user_logged_in()
            && 'no' != ($override_option = get_option('wcj_price_by_country_override_on_checkout_with_billing_country', 'no'))
            && (
                ('all' === get_option('wcj_price_by_country_override_scope', 'all'))
                || ('checkout' === get_option('wcj_price_by_country_override_scope', 'all') && is_checkout())
            )
            && isset(WC()->customer)
            && (('yes' === $override_option && '' != WC()->customer->get_billing_country()) || ('shipping_country' === $override_option && '' != WC()->customer->get_shipping_country()))
        ) {
            $country = ('yes' === $override_option) ? WC()->customer->get_billing_country() : WC()->customer->get_shipping_country();
        }

        if ( isset( $_REQUEST['wcj_country_selector'] ) ) {
            $country = sanitize_key($_REQUEST['wcj_country_selector']);
        }

        if(!empty($country) && strlen($country) === 2) {
            $this->currentCountry = strtoupper($country);
        }

    }

    /**
     * Save current country in WC session
     *
     * @return void
     */
    public function saveCurrentCountry()
    {

        if ( ! session_id()) {
            session_start();
        }
        $_SESSION['dgwt-wcas-boosterio-current-language'] = $this->currentCountry;

    }
}