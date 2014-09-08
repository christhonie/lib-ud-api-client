<?php
/**
 * UD API Updater
 *
 * @namespace UsabilityDynamics
 *
 */
namespace UsabilityDynamics\UD_API {

  if( !class_exists( 'UsabilityDynamics\UD_API\Bootstrap' ) ) {

    /**
     * 
     * @author: peshkov@UD
     */
    class Bootstrap extends Scaffold {
    
      /**
       *
       */
      private $products = array();
      
      /**
       *
       */
      public $admin;
      
      /**
       *
       */
      public static $version = '0.1.0';

      /**
       *
       */
      public function __construct( $args = array() ) {
        parent::__construct( $args );
        
        if ( is_admin() ) {
          //** Load the admin. */
          $this->admin = new Admin( $args );
          //** Get queued plugin updates. */
          add_action( 'init', array( $this, 'load_queued_updates' ), 2 );
        }
        
        //echo "<pre>"; print_r( $this ); echo "</pre>";
      }
      
      /**
       * Add a product to await a license key for activation.
       *
       * Add a product into the array, to be processed with the other products.
       *
       * @since  0.1.0
       * @param string $file The base file of the product to be activated.
       * @param string $instance_key The unique ID of the product to be activated.
       * @return  void
       */
      public function add_product ( $file, $instance_key, $product_id, $errors_callback ) {
        if ( $file != '' && !isset( $this->products[ $file ] ) ) { 
          $this->products[ $file ] = array( 'instance_key' => $instance_key, 'product_id' => $product_id, 'errors_callback' => $errors_callback ); 
        }
      }
      
      /**
       * Return an array of the available product keys.
       * @since  1.0.0
       * @return array Product keys.
       */
      public function get_products () {
        return (array) $this->products;
      }
      
      /**
       * Add Product.
       *
       * @access public
       * @since 0.1.0
       * @return void
       */
      public function load_queued_updates() {
        global $_ud_queued_updates;
        if ( !empty( $_ud_queued_updates[ $this->plugin ] ) && is_array( $_ud_queued_updates[ $this->plugin ] ) ) {
          foreach ( $_ud_queued_updates[ $this->plugin ] as $plugin ) {
            if ( is_object( $plugin ) && ! empty( $plugin->file ) && ! empty( $plugin->instance_key ) && ! empty( $plugin->product_id ) ) {
              $errors_callback = isset( $plugin->errors_callback ) ? $plugin->errors_callback : false;
              $this->add_product( $plugin->file, $plugin->instance_key, $plugin->product_id, $plugin->errors_callback );
            }
          }
        }
      }
      
    }
  
  }
  
}