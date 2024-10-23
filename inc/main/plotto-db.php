<?php

namespace PLotto\Inc\Main;

defined( 'ABSPATH' ) || exit; // Prevent direct access

if(! class_exists('PLottoDB')) {

    class PLottoDB
    {
        /**
         * Create database tables on plugin activation
         *
         * @return void
         */
        public static function add_tables(): void
        {

            if( ! is_admin() )
                return;

            global $wpdb;
            $table_prefix = $wpdb->prefix; // Get tables prefix
            $charset_collate = $wpdb->get_charset_collate(); // Get table charset collate
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php'); // For calling dbDelta function

            /********** Create plot_lotteries table **********/
            $plot_lotteries_table_name = $table_prefix . 'plot_lotteries';

            $plot_lotteries_table = "CREATE TABLE IF NOT EXISTS $plot_lotteries_table_name (

                `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                `is_backup` tinyint(1) NOT NULL DEFAULT 0,
                `lottery` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
                `name` varchar(255) NOT NULL,
                `content` text NOT NULL DEFAULT '',
                `total_price` decimal(65) UNSIGNED NOT NULL,
                `prize_currency` varchar(100) NOT NULL DEFAULT 'USD',
                `ticket_price` decimal(65) UNSIGNED NOT NULL,
                `expire_time` datetime NOT NULL,
                `fake_participant` int DEFAULT 0,
                `color` varchar(100) DEFAULT 'red',
                `company` varchar(255) NOT NULL,
                `block_count` int NOT NULL,
                `choosen_block` int NOT NULL,
                `bonuse_count` int DEFAULT 0,
                `choosen_bonuse` int DEFAULT 0,
                `wc_product_id` bigint(20) UNSIGNED DEFAULT 0,
                `status` enum('active', 'deactive', 'expired') NOT NULL DEFAULT 'active',
                `answer` varchar(255) DEFAULT NULL,
                `answer_date` datetime DEFAULT NULL,
                `registrar` bigint(20) UNSIGNED NOT NULL,
                `updater` bigint(20) UNSIGNED NOT NULL,
                `creation_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `update_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,

                PRIMARY KEY (`ID`)

            ) $charset_collate;";

            dbDelta($plot_lotteries_table); // Create plot_lotteries table

            /********** Create plot_backup_lotteries table **********/
            // $plot_backup_lotteries_table_name = $table_prefix . 'plot_backup_lotteries';

            // $plot_backup_lotteries_table = "CREATE TABLE IF NOT EXISTS $plot_backup_lotteries_table_name (
            //     `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            //     `lottery` bigint(20) UNSIGNED NOT NULL,
            //     `name` varchar(255) NOT NULL,
            //     `content` text NOT NULL DEFAULT '',
            //     `total_price` decimal(65) UNSIGNED NOT NULL,
            //     `prize_currency` varchar(100) NOT NULL DEFAULT 'USD',
            //     `ticket_price` decimal(65) UNSIGNED NOT NULL,
            //     `expire_time` datetime NOT NULL,
            //     `fake_participant` int DEFAULT 0,
            //     `color` varchar(100) DEFAULT 'red',
            //     `company` varchar(255) NOT NULL,
            //     `block_count` int NOT NULL,
            //     `choosen_block` int NOT NULL,
            //     `bonuse_count` int DEFAULT 0,
            //     `choosen_bonuse` int DEFAULT 0,
            //     `wc_product_id` bigint(20) UNSIGNED DEFAULT 0,
            //     `status` enum('active', 'deactive', 'expired') NOT NULL DEFAULT 'active',
            //     `answer` varchar(255) DEFAULT NULL,
            //     `answer_date` datetime DEFAULT NULL,
            //     `registrar` bigint(20) UNSIGNED NOT NULL,
            //     `updater` bigint(20) UNSIGNED NOT NULL,
            //     `creation_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            //     `update_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,

            //     PRIMARY KEY (`ID`)

            // ) $charset_collate;";

            // dbDelta($plot_backup_lotteries_table); // Create plot_backup_lotteries table

            /********** Create plot_winners table **********/
            $plot_winners_table_name = $table_prefix . 'plot_winners';

            $plot_winners_table = "CREATE TABLE IF NOT EXISTS $plot_winners_table_name (

                `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                `lottery` bigint(20) UNSIGNED NOT NULL,
                `backup_lottery` bigint(20) UNSIGNED NOT NULL,
                `user_id` bigint(20) UNSIGNED NOT NULL,
                `participant` bigint(20) UNSIGNED NOT NULL,
                `block` varchar(255) NOT NULL,
                `bonuse` varchar(255) DEFAULT NULL,
                `block_coordination` int UNSIGNED NOT NULL,
                `bonuse_coordination` int UNSIGNED DEFAULT NULL,
                `prize_id` bigint(20) UNSIGNED NOT NULL,
                `status` enum('pending', 'approved', 'rejected', 'paid') DEFAULT 'pending',
                `registrar` bigint(20) UNSIGNED NOT NULL,
                `updater` bigint(20) UNSIGNED NOT NULL,
                `creation_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `update_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,

                PRIMARY KEY (`ID`)

            ) $charset_collate;";

            dbDelta($plot_winners_table); // Create plot_winners table

            /********** Create plot_participants table **********/
            $plot_participants_table_name = $table_prefix . 'plot_participants';

            $plot_participants_table = "CREATE TABLE IF NOT EXISTS $plot_participants_table_name (

                `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                `lottery` bigint(20) UNSIGNED NOT NULL,
                `backup_lottery` bigint(20) UNSIGNED NOT NULL,
                `user_id` bigint(20) UNSIGNED NOT NULL,
                `block` varchar(255) NOT NULL,
                `bonuse` varchar(255) DEFAULT NULL,
                `ticket_price` decimal(65) UNSIGNED NOT NULL,
                `note` varchar(255) NOT NULL,
                `status` enum('unpaid', 'undetermined', 'lost', 'win', 'rejected') DEFAULT 'undetermined',
                `order_id` bigint(20) UNSIGNED DEFAULT 0,
                `creation_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `update_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,

                PRIMARY KEY (`ID`)

            ) $charset_collate;";

            dbDelta($plot_participants_table); // Create plot_participants table

            /********** Create plot_prizes table **********/
            $plot_prizes_table_name = $table_prefix . 'plot_prizes';

            $plot_prizes_table = "CREATE TABLE IF NOT EXISTS $plot_prizes_table_name (

                `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                `lottery` bigint(20) UNSIGNED NOT NULL,
                `block_coordination` int UNSIGNED NOT NULL,
                `bonuse_coordination` int UNSIGNED DEFAULT NULL,
                `amount` float NOT NULL,
                `registrar` bigint(20) UNSIGNED NOT NULL,
                `updater` bigint(20) UNSIGNED NOT NULL,
                `creation_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `update_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,

                PRIMARY KEY (`ID`)

            ) $charset_collate;";

            dbDelta($plot_prizes_table); // Create plot_prizes table

            /********** Create plot_companies table **********/
            $plot_companies_table_name = $table_prefix . 'plot_companies';

            $plot_companies_table = "CREATE TABLE IF NOT EXISTS $plot_companies_table_name (

                `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `description` text DEFAULT NULL,
                `logo` bigint(20) UNSIGNED NOT NULL,
                `registrar` bigint(20) UNSIGNED NOT NULL,
                `updater` bigint(20) UNSIGNED NOT NULL,
                `creation_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `update_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,

                PRIMARY KEY (`ID`)

            ) $charset_collate;";

            dbDelta($plot_companies_table); // Create plot_companies table

            /********** Create plot_logs table **********/
            $plot_logs_table_name = $table_prefix . 'plot_logs';

            $plot_logs_table = "CREATE TABLE IF NOT EXISTS $plot_logs_table_name (

                `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                `message` text NOT NULL,
                `registrar` bigint(20) UNSIGNED NOT NULL,
                `creation_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,

                PRIMARY KEY (`ID`)

            ) $charset_collate;";

            dbDelta($plot_logs_table); // Create plot_logs table

            /********** Create plot_withdrawals table **********/
            $plot_withdrawals_table_name = $table_prefix . 'plot_withdrawals';

            $plot_withdrawals_table = "CREATE TABLE IF NOT EXISTS $plot_withdrawals_table_name (

                `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                `user_id` bigint(20) UNSIGNED NOT NULL,
                `amount` float NOT NULL,
                `type` enum('tether', 'perfect', 'account') DEFAULT 'tether',
                `wallet` varchar(255) NOT NULL,
                `wallet_id` bigint(20) UNSIGNED NOT NULL,
                `iban` varchar(255) NOT NULL,
                `status` enum('pending', 'paid', 'rejected') DEFAULT 'pending',
                `note` varchar(255) NOT NULL,
                `registrar` bigint(20) UNSIGNED NOT NULL,
                `updater` bigint(20) UNSIGNED NOT NULL,
                `creation_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `update_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,

                PRIMARY KEY (`ID`)

            ) $charset_collate;";

            dbDelta($plot_withdrawals_table); // Create plot_withdrawals table

        }
    }
}