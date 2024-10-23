<?php

namespace PLotto\Inc\Woocommerce;

use PLotto\Inc\Helpers\PLottoWpdbHelper;


defined( 'ABSPATH' ) || exit; // Prevent direct access

if( ! class_exists( 'PLottoCheckout' ) )
{
    class PLottoCheckout
    {
        private static $instance = null;

        public string $product_type = 'lottery';

        public function __construct( $product )
        {
            add_action( 'woocommerce_order_status_changed', array( $this, 'after_payment_completed' ), 10, 3 );
            add_filter( 'woocommerce_cart_item_quantity', array( $this, 'cart_item_quantity' ), 10, 3 );
            add_action( 'woocommerce_checkout_order_created', array( $this, 'update_order_id_after_place_order' ), 10 );
        }

        public static function instance()
        {

            if ( self::$instance == null )
            {
                self::$instance = new PLottoCheckout( null );
            }

            return self::$instance;
        }

        public function cart_item_quantity( $product_quantity, $cart_item_key, $cart_item )
        {
            if( is_cart() )
            {
              $product_id = $cart_item['product_id'];
              $product = wc_get_product($product_id);
              if( $product->is_type( 'lottery' ) )
              {
                  $product_quantity = $cart_item['quantity'];
              }
            }
            return $product_quantity;
        }

        public function update_order_id_after_place_order( $order )
        {
            global $wpdb;
            $update_queries = [];
            $items = $order->get_items();
            if( $items )
            {
                foreach( $items as $item )
                {
                    $item_type = $item->get_type(); // e.g. "line_item", "fee"
                    if( $item_type === 'line_item' )
                    {
                        $product = $item->get_product();
                        $lottery_id = get_post_meta( $item->get_product_id(), '_lottery_id', true );
                        if( $product->is_type( 'lottery' ) )
                        {
                            $update_queries[] = sprintf(
                                "UPDATE `{$wpdb->prefix}plot_participants` SET `order_id` = %d WHERE `lottery` = %d AND `order_id` = 0 AND `user_id` = %d AND `status` = 'unpaid';",
                                $order->get_id(),
                                $lottery_id,
                                get_current_user_id()
                            );
                        }
                        if( ! empty( $update_queries ) )
                        {
                            include_once( PLotto_PATH . 'inc/helpers/plotto-wpdb-helper.php' );
                            $wpdb_helper = new PLottoWpdbHelper();
                            $wpdb_helper->update_multiple( $update_queries );
                        }
                    }
                }
            }
        }

        public function after_payment_completed( $order_id, $old_status, $new_status )
        {
            $is_payment_completed = get_post_meta( $order_id, '_plot_payment_completed', true );

            if( $is_payment_completed )
                return;

            global $wpdb;
            $update_queries = [];
            $order = wc_get_order( $order_id );
            $items = $order->get_items();
            if( $items )
            {
                foreach( $items as $item )
                {
                    $product_id = $item->get_data()['product_id'];
                    $product = wc_get_product( $product_id );
                    if( $product->is_type( 'lottery' ) )
                    {
                        $lottery_id = get_post_meta( $product_id, '_lottery_id', true );
                        $update_queries[] = sprintf(
                            "UPDATE `{$wpdb->prefix}plot_participants` SET `status` = 'undetermined' WHERE `lottery` = %d AND `order_id` = %d AND `user_id` = %d AND `status` = 'unpaid';",
                            $lottery_id,
                            $order->get_id(),
                            get_current_user_id()
                        );
                    }
                }
            }

            if( ! empty( $update_queries ) )
            {
                include_once( PLotto_PATH . 'inc/helpers/plotto-wpdb-helper.php' );
                $wpdb_helper = new PLottoWpdbHelper();
                $wpdb_helper->update_multiple( $update_queries );
            }

            update_post_meta( $order_id, '_plot_payment_completed', true );
        }
    }
}