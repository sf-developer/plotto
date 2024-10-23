<?php

namespace PLotto\Inc\Core;

defined( 'ABSPATH' ) || exit; // Prevent direct access

if( ! class_exists( 'PLottoRegisterActions' ) )
{
    class PLottoRegisterActions
    {
        static $instance = null;

        public static function instance()
        {
            if( is_null( self::$instance ) )
            {
                self::$instance = new self();
            }
        }

        public function __construct()
        {
            add_action( 'admin_action_plot-dashboard', [ $this, 'dashboard' ] );
        }

        public function dashboard()
        {
            self::prepare();
            include_once( sprintf( '%sviews/admin/wrapper.php', PLotto_PATH ) ); // Include views
            die;
        }

        private function prepare()
        {
            // Send MIME Type header like WP admin-header.
            @header( 'Content-Type: ' . get_option( 'html_type' ) . '; charset=' . get_option( 'blog_charset' ) );
            add_filter( 'show_admin_bar', '__return_false' );

            // Remove all WordPress actions
            remove_all_actions( 'wp_head' );
            remove_all_actions( 'wp_print_styles' );
            remove_all_actions( 'wp_print_head_scripts' );
            remove_all_actions( 'wp_footer' );

            // Handle `wp_head`
            add_action( 'wp_head', 'wp_enqueue_scripts', 1 );
            add_action( 'wp_head', 'wp_print_styles', 8 );
            add_action( 'wp_head', 'wp_print_head_scripts', 9 );
            add_action( 'wp_head', 'wp_site_icon' );
            add_action( 'wp_head', [ $this, 'styles' ] );

            // add_action( 'wp_footer', 'wp_print_footer_scripts', 20 );
            add_action( 'wp_footer', 'wp_auth_check_html', 30 );
            add_action( 'wp_footer', [ $this, 'scripts' ] );

            // Handle `wp_enqueue_scripts`
            remove_all_actions( 'wp_enqueue_scripts' );

            add_action( 'wp_enqueue_scripts', [ 'PLotto\Inc\Main\PLottoResources', 'admin' ], 999999 );

            // Setup default heartbeat options
            add_filter( 'heartbeat_settings', function( $settings ) {
                $settings['interval'] = 15;
                return $settings;
            } );
        }

        public function styles()
        {
            wp_enqueue_style( 'wp-auth-check' );
            if( is_rtl() ) {
                wp_enqueue_style( 'dashboard-app', PLotto_URL . 'assets/libs/dashboard/css/app.rtl.min.css', array(), '1.0.0' );
                wp_enqueue_style( 'dashboard-app-dark', PLotto_URL . 'assets/libs/dashboard/css/app-dark.rtl.min.css', array(), '1.0.0' );
            } else {
                wp_enqueue_style( 'dashboard-app', PLotto_URL . 'assets/libs/dashboard/css/app.min.css', array(), '1.0.0' );
                wp_enqueue_style( 'dashboard-app-dark', PLotto_URL . 'assets/libs/dashboard/css/app-dark.min.css', array(), '1.0.0' );
            }
            wp_enqueue_style( 'dashboard-iconly', PLotto_URL . 'assets/libs/dashboard/css/iconly.min.css', array(), '1.0.0' );
            wp_enqueue_style( 'plotto' );
            wp_enqueue_script( 'dashboard-init-theme', PLotto_URL . 'assets/libs/dashboard/js/init-theme.js', array(), '1.0.0' );
        }

        public function scripts()
        {
            wp_enqueue_script( 'wp-auth-check' );
            wp_enqueue_script( 'dashboard-dark', PLotto_URL . 'assets/libs/dashboard/js/dark.js', array(), '1.0.0' );
            wp_enqueue_script( 'perfect-scrollbar', PLotto_URL . 'assets/libs/perfect-scrollbar/perfect-scrollbar.min.js', array(), '1.5.3' );
            wp_enqueue_script( 'dashboard-app', PLotto_URL . 'assets/libs/dashboard/js/app.js', array(), '1.0.0' );
            wp_enqueue_script( 'apexcharts', PLotto_URL . 'assets/libs/apexcharts/apexcharts.min.js', array(), '3.42.0' );
            wp_enqueue_script( 'dashboard-js', PLotto_URL . 'assets/libs/dashboard/js/dashboard.js', array(), '1.0.0' );

            wp_localize_script( 'dashboard-js', 'plotto_ajax', array(
                'url' => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'plotto-ajax-nonce' )
            ));
        }
    }
}