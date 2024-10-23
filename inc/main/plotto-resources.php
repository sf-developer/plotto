<?php

namespace PLotto\Inc\Main;

defined( 'ABSPATH' ) || exit; // Prevent direct access

if( ! class_exists( 'PLottoResources' ) )
{
    class PLottoResources
    {

        public static function admin( $hook )
        {
            if ( ! is_admin() ) return; //make sure we are on the backend

            if( ! isset( $_GET['_plot_nonce'] ) || ! wp_verify_nonce( $_GET['_plot_nonce'], 'plot-dashboard' ) ) return;

            $current_screen = get_current_screen();

            if( $current_screen->post_type === 'plot-lottery' || str_contains( $hook, 'plot-' ) || ( isset( $_GET['action'] ) && str_contains( $_GET['action'], 'plot-' ) ) )
            {
                is_rtl() ? wp_register_style( 'bootstrap', PLotto_URL . 'assets/libs/bootstrap/css/bootstrap.rtl.min.css', array(), '5.3.1' ) : wp_register_style( 'bootstrap', PLotto_URL . 'assets/libs/bootstrap/css/bootstrap.min.css', array(), '5.3.1' );
                wp_register_style( 'bootstrap-select', PLotto_URL . 'assets/libs/bootstrap-select/css/bootstrap-select.min.css', array(), '1.13.1' );
                wp_register_style( 'fontawesome', PLotto_URL . 'assets/libs/fontawesome/css/all.min.css', array(), '6.4.2' );
                wp_register_style( 'datatables', PLotto_URL . 'assets/libs/datatables/datatables.min.css', array(), '1.13.6' );
                wp_register_style( 'plotto', PLotto_URL . 'assets/admin/style.css', array( 'bootstrap', 'fontawesome' ), '1.0.0' );

                wp_register_script( 'jquery', PLotto_URL . 'assets/libs/jquery/jquery.min.js', array(), '3.7.0', true );
                wp_register_script( 'jquery-repeater', PLotto_URL . 'assets/libs/jquery/jquery-repeater.min.js', array(), '1.2.1', true );
                wp_register_script( 'bootstrap', PLotto_URL . 'assets/libs/bootstrap/js/bootstrap.bundle.min.js', array( 'jquery' ), '5.3.1', true );
                wp_register_script( 'bootstrap-select', PLotto_URL . 'assets/libs/bootstrap-select/js/bootstrap-select.min.js', array( 'jquery', 'bootstrap' ), '1.14.0', true );
                wp_register_script( 'notiflix', PLotto_URL . 'assets/libs/notiflix/dist/notiflix-aio-3.2.6.min.js', array( 'jquery' ), '3.2.6', true );
                wp_register_script( 'datatables', PLotto_URL . 'assets/libs/datatables/datatables.min.js', array(), '1.13.6', true );
                wp_register_script( 'easy-number-separator', PLotto_URL . 'assets/libs/easy-number-separator/easy-number-separator.js', array(), '1.0.0', true );
                wp_register_script( 'plotto', PLotto_URL . 'assets/admin/script.js', array( 'jquery', 'bootstrap', 'notiflix' ), '1.0.0', true);

                wp_localize_script( 'plotto', 'plotto_ajax', array(
                    'url' => admin_url( 'admin-ajax.php' ),
                    'nonce' => wp_create_nonce( 'plotto-ajax-nonce' )
                ));
            }
        }

        public static function set_script_translations() {
            wp_set_script_translations( 'plotto', 'plotto', plugin_dir_path(__FILE__) . '/../../languages' );
            wp_set_script_translations( 'flipdown', 'plotto', plugin_dir_path(__FILE__) . '/../../languages' );
            wp_set_script_translations( 'dashboard-js', 'plotto', plugin_dir_path(__FILE__) . '/../../languages' );
        }

        public static function public()
        {
            is_rtl() ? wp_register_style( 'bootstrap', PLotto_URL . 'assets/libs/bootstrap/css/bootstrap.rtl.min.css', array(), '5.3.1' ) : wp_register_style( 'bootstrap', PLotto_URL . 'assets/libs/bootstrap/css/bootstrap.min.css', array(), '5.3.1' );
            wp_register_style( 'bootstrap-select', PLotto_URL . 'assets/libs/bootstrap-select/css/bootstrap-select.min.css', array(), '1.13.1' );
            wp_register_style( 'datatables', PLotto_URL . 'assets/libs/datatables/datatables.min.css', array(), '1.13.6' );
            wp_register_style( 'flipdown', PLotto_URL . 'assets/libs/flipdown/flipdown.css', array(), '1.0.0' );
            wp_register_style( 'plotto-grid', PLotto_URL . 'assets/public/plotto-grid.css', array(), '1.0.0' );
            wp_register_style( 'plotto-carousel', PLotto_URL . 'assets/public/plotto-carousel.css', array(), '1.0.0' );
            wp_register_style( 'plotto', PLotto_URL . 'assets/public/style.css', array(), '1.0.0' );
            wp_register_script( 'bootstrap', PLotto_URL . 'assets/libs/bootstrap/js/bootstrap.bundle.min.js', array( 'jquery' ), '5.3.1', true );
            wp_register_script( 'bootstrap-select', PLotto_URL . 'assets/libs/bootstrap-select/js/bootstrap-select.min.js', array( 'jquery', 'bootstrap' ), '1.14.0', true );
            wp_register_script( 'notiflix', PLotto_URL . 'assets/libs/notiflix/dist/notiflix-aio-3.2.6.min.js', array( 'jquery' ), '3.2.6', true );
            wp_register_script( 'datatables', PLotto_URL . 'assets/libs/datatables/datatables.min.js', array(), '1.13.6', true );
            wp_register_script( 'flipdown', PLotto_URL . 'assets/libs/flipdown/flipdown.js', array(), '1.0.0', true );
            wp_register_script( 'plotto-grid', PLotto_URL . 'assets/public/plotto-grid.js', array(), '1.0.0', true );
            wp_register_script( 'plotto-carousel', PLotto_URL . 'assets/public/plotto-carousel.js', array(), '1.0.0', true );
            wp_register_script( 'plotto', PLotto_URL . 'assets/public/script.js', array( 'jquery' ), '1.0.0', true );

            wp_localize_script( 'plotto', 'plotto_ajax', array(
                'url' => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'plotto-ajax-nonce' )
            ));

            wp_localize_script( 'plotto-carousel', 'plottoApiOptions', array(
                'pluginUrl' => PLotto_URL,
                'restUrl' => rest_url( 'plotto/v1' ),
                'nonce' => wp_create_nonce( 'wp_rest' )
            ));

            wp_localize_script( 'plotto-grid', 'plottoApiOptions', array(
                'pluginUrl' => PLotto_URL,
                'restUrl' => rest_url( 'plotto/v1' ),
                'nonce' => wp_create_nonce( 'wp_rest' )
            ));
        }
    }
}