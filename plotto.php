<?php
/**
 *
 * @package           Plotto
 * @author            Pana Websites
 * @copyright         2023 panawebsites.com
 * @license           GPL-2.0
 *
 * @wordpress-plugin
 * Plugin Name: Plotto
 * Plugin URI: https://panawebsites.com/
 * Description: Using this plugin, you can hold different lotteries
 * Version: 1.0.0
 * Tested up to: 6.4.2
 * Author: Pana Websites
 * Author URI: https://panawebsites.com/
 * Text Domain: plotto
 * Domain Path: /languages
 * License: GPL2
*/

namespace PLotto;

use PLotto\Inc\Main\PLottoCheckDependencies;
use PLotto\Inc\Main\PLottoDB;
use PLotto\Inc\Core\PLottoRegisterActions;
use PLotto\Inc\Ajax\PLottoAdminAjaxHandler;
use PLotto\Inc\Ajax\PLottoPublicAjaxHandler;
use PLotto\Inc\Api\PLottoApi;
use PLotto\Inc\Shortcodes\PLottoShortcodes;
use PLotto\Inc\Woocommerce\PLottoCheckout;

defined( 'ABSPATH' ) || exit; // Prevent direct access

if( ! class_exists( 'PLotto' ) )
{
    class PLotto
    {
        public function __construct()
        {
            register_activation_hook( __FILE__, [ $this, 'activate' ] );
            add_action( 'plugins_loaded', [ $this, 'i18n' ] );
            add_action( 'plugins_loaded', [ $this, 'init' ] );
        }

        public function activate()
        {
            include_once plugin_dir_path( __FILE__ ) . 'inc/main/check-dependecies.php';
            include_once plugin_dir_path( __FILE__ ) . 'inc/main/plotto-db.php';
            $check_dependencies = new PLottoCheckDependencies;
            $check_dependencies->check_dependencies();

            PLottoDB::add_tables();
            flush_rewrite_rules();
        }

        public function i18n()
        {
            load_plugin_textdomain( 'plotto', false, plugin_basename( __DIR__ ) . '/languages' );
        }

        public function init()
        {
            defined( 'PLotto_PATH' ) or define( 'PLotto_PATH', plugin_dir_path( __FILE__ ) ); // Plugin path
            defined( 'PLotto_URL' ) or define( 'PLotto_URL', plugin_dir_url( __FILE__ ) ); // Plugin url

            include_once( PLotto_PATH . 'inc/helpers/plotto-helper.php' );
            include_once( PLotto_PATH . 'inc/main/plotto-menu.php' );
            include_once( PLotto_PATH . 'inc/main/plotto-resources.php' );
            include_once( PLotto_PATH . 'inc/core/plotto-register-actions.php' );
            include_once( PLotto_PATH . 'inc/ajax/plotto-admin-ajax-handler.php' );
            include_once( PLotto_PATH . 'inc/ajax/plotto-public-ajax-handler.php' );
            include_once( PLotto_PATH . 'inc/shortcodes/plotto-shortcodes.php' );
            include_once( PLotto_PATH . 'inc/woocommerce/plotto-custom-product-type.php' );
            include_once( PLotto_PATH . 'inc/woocommerce/plotto-checkout.php' );
            include_once( PLotto_PATH . 'inc/woocommerce/plotto-register-endpoint.php' );
            include_once( PLotto_PATH . 'inc/api/plotto-api.php' );

            add_action( 'admin_enqueue_scripts', [ 'PLotto\Inc\Main\PLottoResources', 'admin' ] );
            add_action( 'admin_enqueue_scripts', [ 'PLotto\Inc\Main\PLottoResources', 'set_script_translations' ], 99 );
            add_action( 'wp_enqueue_scripts', [ 'PLotto\Inc\Main\PLottoResources', 'public' ] );
            add_action( 'wp_enqueue_scripts', [ 'PLotto\Inc\Main\PLottoResources', 'set_script_translations' ], 99 );
            add_action( 'admin_menu', [ 'PLotto\Inc\Main\PLottoMenu', 'add_menu' ] );

            // Adding custom tabs to woocommerce myaccount page
            add_action( 'init', array( 'PLotto\Inc\Woocommerce\PLottoRegisterEndPoint', 'add_endpoint' ) ); // Add custom endpoint to wc my account
            add_filter( 'query_vars', array( 'PLotto\Inc\Woocommerce\PLottoRegisterEndPoint', 'query_vars' ) );
            add_filter( 'woocommerce_account_menu_items', array( 'PLotto\Inc\Woocommerce\PLottoRegisterEndPoint', 'add_item_tab' ) );
            add_action( 'woocommerce_account_plotto-tickets_endpoint', array( 'PLotto\Inc\Woocommerce\PLottoRegisterEndPoint', 'tickets_item_content' ) );
            add_action( 'woocommerce_account_plotto-withdrawal_endpoint', array( 'PLotto\Inc\Woocommerce\PLottoRegisterEndPoint', 'withdrawal_item_content' ) );

            add_action( 'init', array( 'PLotto\Inc\Woocommerce\PLottoCustomProductType', 'instance' ) );
            add_action( 'woocommerce_product_options_general_product_data', array( 'PLotto\Inc\Woocommerce\PLottoCustomProductType', 'lottery_product_type_show_price' ) );
            add_action( 'admin_footer', array( 'PLotto\Inc\Woocommerce\PLottoCustomProductType', 'enable_js_on_wc_product' ) );
            add_action( 'woocommerce_process_product_meta', array( 'PLotto\Inc\Woocommerce\PLottoCustomProductType', 'save_lottery_product_settings' ) );
            add_action( 'woocommerce_package_add_to_cart', array( 'PLotto\Inc\Woocommerce\PLottoCustomProductType', 'show_add_to_cart_button' ) );
            add_action( 'transition_post_status', array( 'PLotto\Inc\Woocommerce\PLottoCustomProductType', 'update_lottery_status' ), 10, 3 );
            add_action( 'delete_post', array( 'PLotto\Inc\Woocommerce\PLottoCustomProductType', 'delete_lottery' ) );
            add_filter( 'product_type_selector', array( 'PLotto\Inc\Woocommerce\PLottoCustomProductType', 'add_type' ) );
            add_filter( 'woocommerce_product_class', array( 'PLotto\Inc\Woocommerce\PLottoCustomProductType', 'custom_product_type_class' ), 10, 2 );
            add_filter( 'woocommerce_product_data_tabs', array( 'PLotto\Inc\Woocommerce\PLottoCustomProductType', 'hide_attributes_data_panel' ), 10, 1 );
            add_filter( 'woocommerce_product_add_to_cart_text', array( 'PLotto\Inc\Woocommerce\PLottoCustomProductType', 'add_to_cart_button_text' ) );

            PLottoRegisterActions::instance();
            PLottoAdminAjaxHandler::instance();
            PLottoPublicAjaxHandler::instance();
            PLottoShortcodes::instance();
            PLottoCheckout::instance();
            PLottoApi::instance();
        }
    }
}
new PLotto;