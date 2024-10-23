<?php

namespace PLotto\Inc\Woocommerce;

defined( 'ABSPATH' ) || exit; // Prevent direct access

if ( ! class_exists( 'PLottoRegisterEndPoint' ) )
{
    class PLottoRegisterEndPoint
    {
        /**
         * Add new item endpoint
         *
         * @return void
         */
        public static function add_endpoint(): void
        {
            add_rewrite_endpoint( 'plotto-tickets', EP_ROOT | EP_PAGES );
            add_rewrite_endpoint( 'plotto-withdrawal', EP_ROOT | EP_PAGES );
            flush_rewrite_rules();
        }

        /**
         * Add tickets and withdrawal query var.
         *
         * @param array $vars vars.
         * @return array An array of items.
         */
        public static function query_vars( $vars )
        {
            $vars[] = 'plotto-tickets';
            $vars[] = 'plotto-withdrawal';
            return $vars;
        }

        /**
         * Add tabs in my account page.
         *
         * @param array $items myaccount Items.
         * @return array Items including New tab.
         */
        public static function add_item_tab( $items )
        {
            $download = $items['downloads'];
            $eidt_address = $items['edit-address'];
            $eidt_account = $items['edit-account'];
            $logout = $items['customer-logout'];
            unset( $items['downloads'] );
            unset( $items['edit-address'] );
            unset( $items['edit-account'] );
            unset( $items['customer-logout'] );
            $items['plotto-tickets'] = __( 'My tickets', 'plotto' );
            $items['plotto-withdrawal'] = __( 'Withdrawal', 'plotto' );
            $items['downloads'] = $download;
            $items['edit-address'] = $eidt_address;
            $items['edit-account'] = $eidt_account;
            $items['customer-logout'] = $logout;
            return $items;
        }

        /**
         * Add content to tickets tab.
         *
         * @return void
         */
        public static function tickets_item_content()
        {
            include_once( PLotto_PATH . 'views/public/woocommerce-my-account/tickets.php' );
        }

        /**
         * Add content to whitdrawal tab.
         *
         * @return void
         */
        public static function withdrawal_item_content()
        {
            include_once( PLotto_PATH . 'views/public/woocommerce-my-account/withdrawal.php' );
        }
    }
}