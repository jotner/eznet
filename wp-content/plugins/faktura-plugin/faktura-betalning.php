<?php
/*
Plugin Name: Faktura plugin
Description: Möjliggör betalning med faktura.
Author: André Ingman
Version: 1.0
*/

include_once('pnum.php');

add_filter( 'woocommerce_payment_gateways', 'add_your_gateway_class' );

function add_your_gateway_class( $methods ) {
    $methods[] = 'WC_Custom_PG';
    return $methods;
}


add_action( 'plugins_loaded', 'init_faktura_betalning' );

function init_faktura_betalning(){
    class WC_Custom_PG extends WC_Payment_Gateway {
        function __construct(){
            $this->id = 'fk_custom_pg';
            $this->method_title = 'Fakturabetalning';
            $this->title = 'Fakturabetalning';
            $this->has_fields = true;
            $this->method_description = 'Din beskrivning för betalningen.';

            //load the settings
            $this->init_form_fields();
            $this->init_settings();
            $this->enabled = $this->get_option('enabled');
            $this->title = $this->get_option( 'title' );
            $this->description = $this->get_option('description');

            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

        }
        public function init_form_fields(){
            $this->form_fields = array(
                'enabled' => array(
                    'title'         => 'På/Av',
                    'type'          => 'checkbox',
                    'label'         => 'Aktivera fakturabetalning',
                    'default'       => 'yes'
                ),
                'title' => array(
                    'title'         => 'Betalningsmetod titel',
                    'type'          => 'text',
                    'description'   => 'Detta kontrollerar titeln',
                    'default'       => 'Fakturabetalning',
                    'desc_tip'      => true,
                ),
                'description' => array(
                    'title'         => 'Kundmeddelande',
                    'type'          => 'textarea',
                    'css'           => 'width:500px;',
                    'default'       => 'Ange personnummer',
                    'description'   => 'Meddelandet visas på checkout sidan.',
                ),

            );
        }


        public function payment_fields(){

          if ( $description = $this->get_description() ) {
            echo wpautop( wptexturize( $description ) );
          }

          ?>
          <div id="custom_input">
            <p class="form-row form-row-wide">
              <input class="input" type="text" class="" name="personnummer" id="personnummer" placeholder="" value="">
            </p>
          </div>
          <?php
}

        function process_payment( $order_id ) {
            global $woocommerce;

            $order = new WC_Order( $order_id );

            /****
                custom api (om det behövs)
            ****/

            //update
            $order->update_status('processing','Additional data like transaction id or reference number');

            //reduce stock när payment gårigenom
            $woocommerce->cart->empty_cart();
            $order->reduce_order_stock();

            //returnar array och skickas till thankyoupage
            return array(
                'result' => 'success',
                'redirect' => $this->get_return_url( $order )
            );
        }


    }
}


  add_filter( 'woocommerce_payment_gateways', 'add_custom_gateway_class' );

    function add_custom_gateway_class( $methods ) {
      $methods[] = 'WC_Gateway_Custom';
      return $methods;
    }

  add_action('woocommerce_checkout_process', 'process_custom_payment');

    function process_custom_payment(){

      $pnr = $_POST['personnummer'];

      if($_POST['payment_method'] != 'fk_custom_pg')
        return;


      if(Pnum::check($pnr)) {
        echo "The personal number is correct";
      }
      else {
        wc_add_notice( __( 'Vänligen ange ett giltigt personnummer.'), 'error' );
      }

    }

/**
 * Uppdatera order meta med fält värde
 */
add_action( 'woocommerce_checkout_update_order_meta', 'custom_payment_update_order_meta' );
function custom_payment_update_order_meta( $order_id ) {

    if($_POST['payment_method'] != 'fk_custom_pg')
        return;


    // print_r($_POST['personnummer']);
    // exit();

    update_post_meta( $order_id, 'personnummer', $_POST['personnummer'] );
}

/**
 *  Visa fält värde på order page
 */

add_action( 'woocommerce_admin_order_data_after_billing_address', 'custom_checkout_field_display_admin_order_meta', 10, 1 );
function custom_checkout_field_display_admin_order_meta($order){
    $method = get_post_meta( $order->id, '_payment_method', true );
    if($method != 'fk_custom_pg')
        return;

    $personnummer = get_post_meta( $order->id, 'personnummer', true );

    echo '<p><strong>'.__( 'Personnummer för fakturering: ' ).':</strong> ' . $personnummer . '</p>';

}
