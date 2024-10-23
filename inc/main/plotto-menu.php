<?php

namespace PLotto\Inc\Main;

defined( 'ABSPATH' ) || exit; // Prevent direct access

if( ! class_exists( 'PLottoMenu' ) )
{
    class PLottoMenu
    {
        public static function add_menu()
        {
            // Dashboard menu
            add_menu_page(
                __( 'Dashboard', 'plotto' ),
                __( 'Lottery', 'plotto' ),
                'publish_posts',
                add_query_arg( [ 'action' => 'plot-dashboard', 'p' => 'dashboard', '_plot_nonce' => wp_create_nonce( 'plot-dashboard' ) ], admin_url( 'admin.php' ) ),
                null,
                'dashicons-tickets-alt',
                20
            );
        }
    }
}