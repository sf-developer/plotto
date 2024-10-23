<?php

namespace PLotto\Inc\Main;

defined( 'ABSPATH' ) || exit; // Prevent direct access

if( ! class_exists( 'PLottoCheckDependencies' ) )
{
    class PLottoCheckDependencies
    {
        protected $min_wc = '3.0.0';

        protected $plugin_name = 'PLotto';

        public function __construct()
        {
            add_action( 'admin_init', array( $this, 'check_dependencies' ) );
        }

        public function check_dependencies()
        {
            $this->check_wc();
            $this->check_extensions();
        }

        private function check_wc()
        {
            if (  ( defined( 'WC_VERSION' ) && ! version_compare( WC()->version, $this->min_wc, '>=' ) ) || ! class_exists( 'WooCommerce' ) )
            {
                $this->deactivate_plugin();
				/* translators: %s: error message */
                wp_die( sprintf( __( 'Could not be activated. %s', 'plotto' ), $this->missing_wc() ), 'Plotto', [ 'back_link' => true ] );
            }
        }

        private function check_extensions()
        {
            if( ! extension_loaded('pdo_mysql') )
            {
                $this->deactivate_plugin();
                wp_die( __( 'Could not be activated. This plugin requires pdo_mysql extension to activate. Please send content to your hosting provider.', 'plotto' ), 'Plotto', [ 'back_link' => true ] );
            }
        }

        private function deactivate_plugin()
        {
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            deactivate_plugins( plugin_basename( __FILE__ ) );
            if ( isset( $_GET['activate'] ) ) {
                unset( $_GET['activate'] );
            }
        }

        private function missing_wc() {
            return sprintf(
                __( "%1\$s requires WooCommerce version %2\$s or higher installed and active. You can download WooCommerce latest version %3\$s.", 'plotto' ),
                '<strong>' . $this->plugin_name . '</strong>',
                $this->min_wc,
                '<strong><a href="https://downloads.wordpress.org/plugin/woocommerce.latest-stable.zip">' . __( 'from here', 'plotto' ) . '</a></strong>'
            );
        }
    }
}