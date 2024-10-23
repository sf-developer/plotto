<?php

namespace PLotto\Inc\Woocommerce;

use WC_Product;
use Plotto\Inc\Helpers\PLottoHelper;

defined( 'ABSPATH' ) || exit; // Prevent direct access

if( ! class_exists( 'PLottoCustomProductType' ) )
{
    class PLottoCustomProductType extends WC_Product
    {
        private static $instance = null;

        public string $product_type = 'lottery';

        public function __construct( $product )
        {
            $this->supports[]   = 'ajax_add_to_cart';
            parent::__construct( $product );
            $this->set_sold_individually( false );
            add_action( 'pre_get_posts', [ $this, 'remove_lotteries_from_shop_page' ] );
            add_filter( 'woocommerce_product_query_meta_query', [ $this, 'custom_product_query_meta_query' ], 10, 2 );
            add_filter( 'woocommerce_shortcode_products_query', [ $this, 'custom_product_query_meta_query' ], 10, 2 );
            add_filter( 'woocommerce_products_widget_query_args', [ $this, 'custom_product_query_meta_query' ], 10, 2 );
        }

        public static function instance()
        {

            if ( self::$instance == null )
            {
                self::$instance = new PLottoCustomProductType( null );
            }

            return self::$instance;
        }

        public static function custom_product_type_class( $classname, $product_type )
        {
            if ( $product_type === 'lottery' )
            {
                $classname = __CLASS__;
            }
            return $classname;
        }

        /**
         * Lottery Type
         *
         * @param array $types
         * @return void
         */
        public static function add_type( $types )
        {
            $types['lottery'] = __( 'Lottery', 'plotto' );
            return $types;
        }

        public static function hide_attributes_data_panel( $tabs )
        {
            // Other default values for 'attribute' are; general, inventory, shipping, linked_product, variations, advanced
            $tabs['attribute']['class'][] = 'hide_if_lottery';
            $tabs['inventory']['class'][] = 'hide_if_lottery';
            $tabs['shipping']['class'][] = 'hide_if_lottery';
            $tabs['variations']['class'][] = 'hide_if_lottery';

            return $tabs;
        }

        public static function lottery_product_type_show_price()
        {
            global $product_object;
            if ( $product_object )
            { ?>
                <div class='options_group show_if_lottery'>
                    <?php
                    woocommerce_wp_text_input(
                        array(
                            'id'          => '_lottery_block',
                            'label'       => __( 'Number of blocks', 'plotto' ),
                            'value'       => $product_object->get_meta( '_lottery_block', true ),
                            'default'     => 1,
                            'type'        => 'number',
                            'placeholder' => __( 'Determine lottery block count', 'plotto' ),
                            'data_type'   => 'price'
                        )
                    );
                    woocommerce_wp_text_input(
                        array(
                            'id'          => '_lottery_choosen_block',
                            'label'       => __( 'Number of choosen blocks', 'plotto' ),
                            'value'       => $product_object->get_meta( '_lottery_choosen_block', true ),
                            'default'     => 1,
                            'type'        => 'number',
                            'placeholder' => __( 'Determine lottery choosen block count', 'plotto' ),
                            'data_type'   => 'price'
                        )
                    );
                    woocommerce_wp_text_input(
                        array(
                            'id'          => '_lottery_bonuse',
                            'label'       => __( 'Number of bonuses', 'plotto' ),
                            'value'       => $product_object->get_meta( '_lottery_bonuse', true ),
                            'default'     => 0,
                            'type'        => 'number',
                            'placeholder' => __( 'Determine lottery bonuse count', 'plotto' ),
                            'data_type'   => 'price'
                        )
                    );
                    woocommerce_wp_text_input(
                        array(
                            'id'          => '_lottery_choosen_bonuse',
                            'label'       => __( 'Number of choosen bonuses', 'plotto' ),
                            'value'       => $product_object->get_meta( '_lottery_choosen_bonuse', true ),
                            'default'     => 0,
                            'type'        => 'number',
                            'placeholder' => __( 'Determine lottery choosen bonuse count', 'plotto' ),
                            'data_type'   => 'price'
                        )
                    );
                    woocommerce_wp_text_input(
                        array(
                            'id'          => '_lottery_total_prize',
                            'label'       => __( 'Total prize', 'plotto' ),
                            'value'       => $product_object->get_meta( '_lottery_total_prize', true ),
                            'default'     => 0,
                            'type'        => 'number',
                            'placeholder' => __( 'Determine lottery total prize', 'plotto' ),
                            'data_type'   => 'price'
                        )
                    );
                    woocommerce_wp_select(
                        array(
                            'id'          => '_lottery_total_prize_currency',
                            'label'       => __( 'Total prize currency', 'plotto' ),
                            'value'       => $product_object->get_meta( '_lottery_total_prize_currency', true ),
                            'options'     => get_woocommerce_currencies()
                        )
                    );
                    woocommerce_wp_text_input(
                        array(
                            'id'          => '_lottery_fake_participant',
                            'label'       => __( 'Fake participant', 'plotto' ),
                            'value'       => $product_object->get_meta( '_lottery_fake_participant', true ),
                            'default'     => 0,
                            'type'        => 'number',
                            'placeholder' => __( 'Determine lottery fake participant', 'plotto' ),
                            'data_type'   => 'price'
                        )
                    );
                    woocommerce_wp_text_input(
                        array(
                            'id'          => '_lottery_expire_time',
                            'label'       => __( 'Expire time', 'plotto' ),
                            'value'       => $product_object->get_meta( '_lottery_expire_time', true ),
                            'type' => 'datetime-local',
                            'custom_attributes' => [
                                'min' => wp_date( 'Y-m-d H:i' ),
                            ],
                            'data_type'   => 'price'
                        )
                    );
                    $colors = PLottoHelper::get_colors();
                    $colors_arr = array_merge( ['' => __( 'Select color', 'plotto' )], $colors );
                    woocommerce_wp_select(
                        array(
                            'id'          => '_lottery_color',
                            'label'       => __( 'Color', 'plotto' ),
                            'value'       => $product_object->get_meta( '_lottery_color', true ),
                            'options'     => $colors_arr
                        )
                    );
                    $compnaies = PLottoHelper::get_companies();
                    $companies_arr = ['' => __( 'Select company', 'plotto' )];
                    if( ! empty( $compnaies ) )
                    {
                        foreach( $compnaies as $company )
                        {
                            $companies_arr[$company->ID] = $company->name;
                        }
                    }
                    woocommerce_wp_select(
                        array(
                            'id'          => '_lottery_company',
                            'label'       => __( 'Company', 'plotto' ),
                            'value'       => $product_object->get_meta( '_lottery_company', true ),
                            'options'     => $companies_arr
                        )
                    );
                    ?>
                </div>
                <?php
            }
        }

        public static function enable_js_on_wc_product()
        {
            global $post, $product_object;

            if ( ! $post )
                return;

            if ( 'product' != $post->post_type )
                return;

            $is_lottery = $product_object && 'lottery' === $product_object->get_type() ? true : false;
            ?>
            <script type='text/javascript'>
                jQuery(document).ready(function () {
                    jQuery('.options_group.pricing').addClass('show_if_lottery').show();
                    jQuery('.general_options').show();
                    <?php if( $is_lottery ) {
                        ?>
                        jQuery('#general_product_data .show_if_lottery').show();
                    <?php } ?>
                });
             </script>
            <?php
        }

        public static function save_lottery_product_settings( $post_id )
        {
            // verify nonce
            if ( ! isset( $_POST[ 'woocommerce_meta_nonce' ] ) || ! wp_verify_nonce( $_POST[ 'woocommerce_meta_nonce' ], 'woocommerce_save_data' ) )
                return $post_id;

            global $wpdb;
            $lottery_table = "{$wpdb->prefix}plot_lotteries";
            $lottery_block = $_POST['_lottery_block'];
            $lottery_choosen_block = $_POST['_lottery_choosen_block'];
            $lottery_bonuse = $_POST['_lottery_bonuse'];
            $lottery_choosen_bonuse = $_POST['_lottery_choosen_bonuse'];
            $lottery_total_prize = $_POST['_lottery_total_prize'];
            $lottery_total_prize_currency = $_POST['_lottery_total_prize_currency'];
            $lottery_fake_participant = $_POST['_lottery_fake_participant'];
            $lottery_color = $_POST['_lottery_color'];
            $lottery_company = $_POST['_lottery_company'];
            $lottery_expire_time = $_POST['_lottery_expire_time'];

            if( ! empty( $lottery_block ) )
            {
                update_post_meta( $post_id, '_lottery_block', esc_attr( $lottery_block ) );
            }
            if( ! empty( $lottery_choosen_block ) )
            {
                update_post_meta( $post_id, '_lottery_choosen_block', esc_attr( $lottery_choosen_block ) );
            }
            if( ! empty( $lottery_bonuse ) )
            {
                update_post_meta( $post_id, '_lottery_bonuse', esc_attr( $lottery_bonuse ) );
            }
            if( ! empty( $lottery_choosen_bonuse ) )
            {
                update_post_meta( $post_id, '_lottery_choosen_bonuse', esc_attr( $lottery_choosen_bonuse ) );
            }
            if( ! empty( $lottery_total_prize ) )
            {
                update_post_meta( $post_id, '_lottery_total_prize', esc_attr( $lottery_total_prize ) );
            }
            if( ! empty( $lottery_total_prize_currency ) )
            {
                update_post_meta( $post_id, '_lottery_total_prize_currency', esc_attr( $lottery_total_prize_currency ) );
            }
            if( ! empty( $lottery_fake_participant ) )
            {
                update_post_meta( $post_id, '_lottery_fake_participant', esc_attr( $lottery_fake_participant ) );
            }
            if( ! empty( $lottery_color ) )
            {
                update_post_meta( $post_id, '_lottery_color', esc_attr( $lottery_color ) );
            }
            if( ! empty( $lottery_company ) )
            {
                update_post_meta( $post_id, '_lottery_company', esc_attr( $lottery_company ) );
            }
            if( ! empty( $lottery_expire_time ) && $lottery_expire_time > wp_date( 'Y-m-d H:i' ) )
            {
                update_post_meta( $post_id, '_lottery_expire_time', esc_attr( $lottery_expire_time ) );
            }

            $lottery_id = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT `ID` FROM {$wpdb->prefix}plot_lotteries WHERE `wc_product_id` = %d",
                    $post_id
                )
            );

            update_post_meta( $post_id, '_lottery_id', $lottery_id );

            $lottery_data = array(
                'name' => $_POST['post_title'],
                'content' => $_POST['post_content'],
                'total_price' => $lottery_total_prize,
                'prize_currency' => $lottery_total_prize_currency,
                'ticket_price' => $_POST['_regular_price'],
                'fake_participant' => $lottery_fake_participant,
                'color' => $lottery_color,
                'company' => $lottery_company,
                'block_count' => $lottery_block,
                'choosen_block' => $lottery_choosen_block,
                'bonuse_count' => $lottery_bonuse,
                'choosen_bonuse' => $lottery_choosen_bonuse,
                'updater' => get_current_user_id(),
                'update_date' => current_time( 'mysql' )
            );
            if( $lottery_expire_time > wp_date( 'Y-m-d H:i' ) )
                $lottery_data[ 'expire_time' ] = $lottery_expire_time;

            $where = [ 'ID' => $lottery_id ];
            $wpdb->update(
                $lottery_table, $lottery_data, $where
            );
        }

        public static function show_add_to_cart_button()
        {
            do_action( 'woocommerce_simple_add_to_cart' );
        }

        public static function add_to_cart_button_text()
        {
            return __('Add to cart', 'woocommerce');
        }

        public static function delete_lottery( $pid )
        {
            if( get_post_type( $pid ) !== 'product' )
                return;

            global $wpdb;
            $lottery_table = "{$wpdb->prefix}plot_lotteries";
            $lottery_id = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT `ID` FROM `{$wpdb->prefix}plot_lotteries` WHERE `wc_product_id` = %d",
                    $pid
                )
            );

            if ( $lottery_id )
            {
                $wpdb->delete( $lottery_table, [ 'ID' => $lottery_id ] );
            }
        }

        public static function update_lottery_status( $new_status, $old_status, $post )
        {
            if( get_post_type( $post->ID ) !== 'product' )
                return;

            $data = [];

            if( in_array( $new_status, [ 'pending', 'draft', 'private', 'trash' ] ) && $old_status === 'publish' )
            {
                $data = [ 'status' => 'deactive' ];
            }

            if( $new_status === 'publish' && in_array( $old_status, [ 'pending', 'draft', 'private', 'trash' ] ) )
            {
                $lottery_expire_time = get_post_meta( $post->ID, '_lottery_expire_time', true );

                if( empty( $lottery_expire_time ) || $lottery_expire_time < wp_date( 'Y-m-d H:i' ) )
                    return;

                $data = [ 'expire_time' => $lottery_expire_time, 'status' => 'active' ];
            }

            if( ! empty( $data ) )
            {
                global $wpdb;
                $lottery_table = "{$wpdb->prefix}plot_lotteries";
                $wpdb->update(
                    $lottery_table, $data, [ 'wc_product_id' => $post->ID ]
                );
            }
        }

        public function remove_lotteries_from_shop_page( $q )
        {
            if( ! $q->is_main_query() ) return;

            if( ! $q->is_post_type_archive() ) return;

            if ( ! is_admin() && is_shop() )
            {
                $args = array(
                    'limit' => -1,
                    'status' => 'publish',
                    'type' => 'lottery',
                    'return' => 'ids'
                );
                $products = wc_get_products( $args );

                $q->set( 'post__not_in', $products );
            }

            remove_action( 'pre_get_posts', [ $this, 'remove_lotteries_from_shop_page' ] );
        }

        public function custom_product_query_meta_query( $meta_query, $query )
        {
            if( ! is_admin() )
            {
                $args = array(
                    'limit' => -1,
                    'status' => 'publish',
                    'type' => 'lottery',
                    'return' => 'ids'
                );
                $products = wc_get_products( $args );

                $meta_query[] = array(
                    'post__not_in' => $products
                );
            }

            return $meta_query;
        }
    }
}