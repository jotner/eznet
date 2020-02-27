<?php
/**
 * Plugin Name: Leverans hämta i butik plugin
 * Description: Ger kunden ett val om de vill hämta ut produkt i butik.
 * Version: 1
 * Author: André Ingman, Johan Westin
 */

 add_action('woocommerce_before_order_notes', 'wps_add_select_checkout_field');
 function wps_add_select_checkout_field( $checkout ) {
    $butiker = get_posts( array(
        'post_type'      => 'butik',
        'post_status'    => 'publish',
        'posts_per_page' => -1
    ) );

    $butik_array = array();
    foreach ( $butiker as $butik ) :
        $butik_array[$butik->post_title] = $butik->post_title;
    endforeach;

	echo '<h2>'.__('Hämta i butik').'</h2>';
	woocommerce_form_field( 'valj_butik', array(
	    'type'          => 'select',
	    'class'         => array( 'wps-drop' ),
	    'label'         => __( 'Välj butik' ),
	    'options'       => $butik_array
    ),
	$checkout->get_value( 'valj_butik' ));
 }


  add_action('woocommerce_checkout_process', 'wps_select_checkout_field_process');
  function wps_select_checkout_field_process() {
     global $woocommerce;
     // Check if set, if its not set add an error.
     if ($_POST['valj_butik'] == "blank") {
       wc_add_notice( '<strong>Välj butik </strong>', 'error' );
     }
  }



  //* Update the order meta with field value
 add_action('woocommerce_checkout_update_order_meta', 'wps_select_checkout_field_update_order_meta');
 function wps_select_checkout_field_update_order_meta( $order_id ) {
   if ($_POST['valj_butik']) update_post_meta( $order_id, 'valj_butik', esc_attr($_POST['valj_butik']));
  //  if ($_POST['min_amount']) update_post_meta( $order_id, 'min_amount', esc_attr($_POST['min_amount']));
 }


 //* Display field value on the order edition page
add_action( 'woocommerce_admin_order_data_after_billing_address', 'wps_select_checkout_field_display_admin_order_meta', 10, 1 );
function wps_select_checkout_field_display_admin_order_meta($order){
	echo '<p><strong>'.__('Upphämtning').':</strong> <br>' . get_post_meta( $order->id, 'valj_butik', true ) . '</p>';
}
//* Add selection field value to emails
add_filter('woocommerce_email_order_meta_keys', 'wps_select_order_meta_keys');
function wps_select_order_meta_keys( $keys ) {
  $keys['Valj_butik:'] = 'valj_butik';
	return $keys;
  
}
// Add input fields to 
add_filter( 'woocommerce_shipping_settings', 'add_admin_shipping_fields' );
function add_admin_shipping_fields( $settings ) {

  $updated_settings = array();

  foreach ( $settings as $section ) {

      // at the bottom of the General Options section
    if ( isset( $section['id'] ) && 'shipping_options' == $section['id'] &&
      isset( $section['type'] ) && 'sectionend' == $section['type'] ) {

      $updated_settings[] = array(
          'name' => __( 'Hämta i butik', 'woocommerce' ),
          'id'   => 'pricing_options',
          'type' => 'h2',
          'desc' => __('The following options affect how prices are displayed on the frontend.', 'woocommerce'),
      );
      $updated_settings[] = array(
        'name'     => __( 'Minbelopp' ),
        'desc_tip' => __( 'Minsta beloppet för fri frakt.', 'wc_seq_order_numbers' ),
        'id'       => 'min_amount',
        'type'     => 'text',
        'css'      => 'min-width:300px;',
        'std'      => '0',  // WC < 2.0
        'default'  => '0',  // WC >= 2.0
        'desc'     => __( 'Minsta beloppet för fri frakt.', 'wc_seq_order_numbers' ),
      );
      $updated_settings[] = array(
        'name'     => __( 'Leveransavgift' ),
        'desc_tip' => __( 'Leveransavgift.', 'wc_seq_order_numbers' ),
        'id'       => 'shipping_cost',
        'type'     => 'text',
        'css'      => 'min-width:300px;',
        'std'      => '0',  // WC < 2.0
        'default'  => '0',  // WC >= 2.0
        'desc'     => __( 'Leveransavgift.', 'wc_seq_order_numbers' ),
      );
    }
    $updated_settings[] = $section;
  }

  return $updated_settings;
}

  //   add_filter('woocommerce_shipping_settings', 'general_settings_shop_phone');
// function general_settings_shop_phone($settings) {
//     $key = 0;

//     foreach( $settings as $values ){
//         $new_settings[$key] = $values;
//         $key++;

//         // Inserting array just after the post code in "Store Address" section
//         if($values['id'] == 'woocommerce_shipping_debug_mode'){
//             $new_settings[$key] = array(
//                   'name' => __( 'Hämta i butik', 'woocommerce' ),
//                   'type' => 'title',
//                   'desc' => __('The following options affect how prices are displayed on the frontend.', 'woocommerce'),
//                   'id'   => 'pricing_options'
//               );
//             $key++;
//         }
//     }
//     return $new_settings;
// }


add_filter('woocommerce_package_rates', 'local_pickup_cost', 12, 2);
function local_pickup_cost( $rates, $package ){
  if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
    return $rates;
  }
  $min_amount = get_option( 'min_amount', 1 );
  $shipping_cost = get_option( 'shipping_cost', 1 );
  $total_cost = WC()->cart->get_cart_contents_total();
  foreach ( $rates as $rate_key => $rate ){
    if( 'local_pickup' === $rate->method_id ){
      if( $total_cost <= $min_amount ) {
        $new_cost = $shipping_cost;
      }
      $rates[$rate_key]->cost = $new_cost;
    }
  }
  return $rates;
}