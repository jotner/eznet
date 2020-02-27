<?php
/*
Plugin Name: Fraktplugin - Bud
Description: Plugin för fraktalternativet "frakt med bud".
Version: 1.0.0
Author: Jonathan Eriksson
*/
if (!defined('WPINC')) {
    die;
}

/*
 * Kolla om WooCommerce är aktiverat
 */
 if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
     function request_a_shipping_quote_init()
     {
         if (! class_exists('Imp_WC_Shipping_Local_Pickup')) {
             class Imp_WC_Pickup_Shipping_Method extends WC_Shipping_Method
             {
               /**
                *
                * @var string Cost.
                */
               protected $fee_cost = '';
                 /**
                 * Constructor.
                 *
                 * @param int $instance_id
                 */
                 public function __construct($instance_id = 0)
                 {
                     $this->id           = 'imp_pickup_shipping_method';
                     $this->instance_id  = absint($instance_id);
                     $this->method_title = __("Bud", 'imp');
                     $this->method_description = __('Låter dig debitera en fast avgift för leverans med bud.', 'imp');
                     $this->supports     = array(
                       'shipping-zones',
                       'instance-settings',
                       'instance-settings-modal',
                   );
                     $this->init();
                 }

                 public function init()
                 {
                   $this->instance_form_fields = include 'frakt-settings.php';
                   $this->title                = $this->get_option( 'title' );
                   $this->tax_status           = $this->get_option( 'tax_status' );
                   $this->cost                 = $this->get_option( 'cost' );
                   $this->type                 = $this->get_option( 'type', 'class' );
                 }

                 /**
               	 *
               	 * @param  string $sum Sum of shipping.
               	 * @param  array  $args Args.
               	 * @return string
               	 */
               	protected function evaluate_cost( $sum, $args = array() ) {
               		$args           = apply_filters( 'woocommerce_evaluate_shipping_cost_args', $args, $sum, $this );
               		$locale         = localeconv();
               		$decimals       = array( wc_get_price_decimal_separator(), $locale['decimal_point'], $locale['mon_decimal_point'], ',' );
               		$this->fee_cost = $args['cost'];

               		add_shortcode( 'fee', array( $this, 'fee' ) );

               		$sum = do_shortcode(
               			str_replace(
               				array(
               					'[qty]',
               					'[cost]',
               				),
               				array(
               					$args['qty'],
               					$args['cost'],
               				),
               				$sum
               			)
               		);

               		remove_shortcode( 'fee', array( $this, 'fee' ) );

               		$sum = preg_replace( '/\s+/', '', $sum );
               		$sum = str_replace( $decimals, '.', $sum );
               		$sum = rtrim( ltrim( $sum, "\t\n\r\0\x0B+*/" ), "\t\n\r\0\x0B+-*/" );

               		return $sum ? WC_Eval_Math::evaluate( $sum ) : 0;
               	}

               	/**
               	 *
               	 * @param  array $atts Attributes.
               	 * @return string
               	 */
               	public function fee( $atts ) {
               		$atts = shortcode_atts(
               			array(
               				'percent' => '',
               				'min_fee' => '',
               				'max_fee' => '',
               			),
               			$atts,
               			'fee'
               		);

               		$calculated_fee = 0;

               		if ( $atts['percent'] ) {
               			$calculated_fee = $this->fee_cost * ( floatval( $atts['percent'] ) / 100 );
               		}

               		if ( $atts['min_fee'] && $calculated_fee < $atts['min_fee'] ) {
               			$calculated_fee = $atts['min_fee'];
               		}

               		if ( $atts['max_fee'] && $calculated_fee > $atts['max_fee'] ) {
               			$calculated_fee = $atts['max_fee'];
               		}

               		return $calculated_fee;
               	}

               	/**
               	 *
               	 * @param array $package Package of items from cart.
               	 */
                  public function calculate_shipping( $package = array() ) {
                		$rate = array(
                			'id'      => $this->get_rate_id(),
                			'label'   => $this->title,
                			'cost'    => 0,
                			'package' => $package,
                		);

                		$has_costs = false;
                		$cost      = $this->get_option( 'cost' );

                		if ( '' !== $cost ) {
                			$has_costs    = true;
                			$rate['cost'] = $this->evaluate_cost(
                				$cost,
                				array(
                					'qty'  => $this->get_package_item_qty( $package ),
                					'cost' => $package['contents_cost'],
                				)
                			);
                		}

                		$shipping_classes = WC()->shipping()->get_shipping_classes();

                		if ( ! empty( $shipping_classes ) ) {
                			$found_shipping_classes = $this->find_shipping_classes( $package );
                			$highest_class_cost     = 0;

                			foreach ( $found_shipping_classes as $shipping_class => $products ) {
                				$shipping_class_term = get_term_by( 'slug', $shipping_class, 'product_shipping_class' );
                				$class_cost_string   = $shipping_class_term && $shipping_class_term->term_id ? $this->get_option( 'class_cost_' . $shipping_class_term->term_id, $this->get_option( 'class_cost_' . $shipping_class, '' ) ) : $this->get_option( 'no_class_cost', '' );

                				if ( '' === $class_cost_string ) {
                					continue;
                				}

                				$has_costs  = true;
                				$class_cost = $this->evaluate_cost(
                					$class_cost_string,
                					array(
                						'qty'  => array_sum( wp_list_pluck( $products, 'quantity' ) ),
                						'cost' => array_sum( wp_list_pluck( $products, 'line_total' ) ),
                					)
                				);

                				if ( 'class' === $this->type ) {
                					$rate['cost'] += $class_cost;
                				} else {
                					$highest_class_cost = $class_cost > $highest_class_cost ? $class_cost : $highest_class_cost;
                				}
                			}

                			if ( 'order' === $this->type && $highest_class_cost ) {
                				$rate['cost'] += $highest_class_cost;
                			}
                		}

                		if ( $has_costs ) {
                			$this->add_rate( $rate );
                		}
                	}

                	/**
                	 *
                	 * @param  array $package Package of items from cart.
                	 * @return int
                	 */
                	public function get_package_item_qty( $package ) {
                		$total_quantity = 0;
                		foreach ( $package['contents'] as $item_id => $values ) {
                			if ( $values['quantity'] > 0 && $values['data']->needs_shipping() ) {
                				$total_quantity += $values['quantity'];
                			}
                		}
                		return $total_quantity;
                	}

                	/**
                	 *
                	 * @param mixed $package Package of items from cart.
                	 * @return array
                	 */
                	public function find_shipping_classes( $package ) {
                		$found_shipping_classes = array();

                		foreach ( $package['contents'] as $item_id => $values ) {
                			if ( $values['data']->needs_shipping() ) {
                				$found_class = $values['data']->get_shipping_class();

                				if ( ! isset( $found_shipping_classes[ $found_class ] ) ) {
                					$found_shipping_classes[ $found_class ] = array();
                				}

                				$found_shipping_classes[ $found_class ][ $item_id ] = $values;
                			}
                		}

                		return $found_shipping_classes;
                	}

                	/**
                	 *
                	 * @since 3.4.0
                	 * @param string $value Unsanitized value.
                	 * @return string
                	 */
                	public function sanitize_cost( $value ) {
                		$value = is_null( $value ) ? '' : $value;
                		$value = wp_kses_post( trim( wp_unslash( $value ) ) );
                		$value = str_replace( array( get_woocommerce_currency_symbol(), html_entity_decode( get_woocommerce_currency_symbol() ) ), '', $value );
                		return $value;
                	}
             }
         }
     }
     add_action('woocommerce_shipping_init', 'request_a_shipping_quote_init');

     function request_shipping_quote_shipping_method($methods)
     {
         $methods['imp_pickup_shipping_method'] = 'Imp_WC_Pickup_Shipping_Method';
         return $methods;
     }
     add_filter('woocommerce_shipping_methods', 'request_shipping_quote_shipping_method');
 }
