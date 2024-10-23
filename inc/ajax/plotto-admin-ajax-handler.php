<?php

namespace PLotto\Inc\Ajax;

use PLotto\Inc\Helpers\PLottoHelper;
use PLotto\Inc\Helpers\PLottoWpdbHelper;
use PLotto\Inc\Helpers\SSP;

defined( 'ABSPATH' ) || exit; // Prevent direct access

if( ! class_exists( 'PLottoAdminAjaxHandler' ) )
{
    class PLottoAdminAjaxHandler
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
            add_action( 'wp_ajax_save_lottery', [ $this, 'ajax_handler' ] );
            add_action( 'wp_ajax_save_company', [ $this, 'ajax_handler' ] );
            add_action( 'wp_ajax_delete_prize', [ $this, 'ajax_handler' ] );
            add_action( 'wp_ajax_get_lotteries', [ $this, 'ajax_handler' ] );
            add_action( 'wp_ajax_get_companies', [ $this, 'ajax_handler' ] );
            add_action( 'wp_ajax_prepare_finish_modal', [ $this, 'ajax_handler' ] );
            add_action( 'wp_ajax_finish_lottery', [ $this, 'ajax_handler' ] );
            add_action( 'wp_ajax_prepare_renew_modal', [ $this, 'ajax_handler' ] );
            add_action( 'wp_ajax_renew_lottery', [ $this, 'ajax_handler' ] );
            add_action( 'wp_ajax_delete_lottery', [ $this, 'ajax_handler' ] );
            add_action( 'wp_ajax_delete_company', [ $this, 'ajax_handler' ] );
            add_action( 'wp_ajax_get_participants', [ $this, 'ajax_handler' ] );
            add_action( 'wp_ajax_approve_winner', [ $this, 'ajax_handler' ] );
            add_action( 'wp_ajax_reject_winner', [ $this, 'ajax_handler' ] );
            add_action( 'wp_ajax_get_withdrawal_requests', [ $this, 'ajax_handler' ] );
            add_action( 'wp_ajax_approve_withdrawal_request', [ $this, 'ajax_handler' ] );
            add_action( 'wp_ajax_reject_withdrawal_request', [ $this, 'ajax_handler' ] );
            add_action( 'wp_ajax_get_logs', [ $this, 'ajax_handler' ] );
            add_action( 'wp_ajax_get_weekly_sales_amount', [ $this, 'ajax_handler' ] );
            add_action( 'wp_ajax_get_weekly_sales', [ $this, 'ajax_handler' ] );
            add_action( 'wp_ajax_get_weekly_loosers', [ $this, 'ajax_handler' ] );
            add_action( 'wp_ajax_get_weekly_winners', [ $this, 'ajax_handler' ] );
            add_action( 'wp_ajax_get_each_month_sales', [ $this, 'ajax_handler' ] );
            add_action( 'wp_ajax_get_today_reports', [ $this, 'ajax_handler' ] );
        }

        public function ajax_handler()
        {
            if( ! is_admin() )
            {
                wp_send_json_error( [ 'message' => __( 'You are not admin.', 'plotto' ) ] );
            }

            if ( ! defined('DOING_AJAX') )
            {
                wp_redirect(home_url());
                exit();
            }

            // Handle ajax request and return response
            check_ajax_referer( 'plotto-ajax-nonce', 'security' );

            $action = isset( $_POST['action'] ) ? sanitize_text_field( wp_unslash( $_POST['action'] ) ) : '';
            switch( $action )
            {
                case 'save_lottery':
                    $this->save_lottery();
                    break;

                case 'save_company':
                    $this->save_company();
                    break;

                case 'delete_prize':
                    $this->delete_prize();
                    break;

                case 'get_lotteries':
                    $this->get_lotteries();
                    break;

                case 'get_companies':
                    $this->get_companies();
                    break;

                case 'prepare_finish_modal':
                    $this->prepare_finish_modal();
                    break;

                case 'finish_lottery':
                    $this->finish_lottery();
                    break;

                case 'prepare_renew_modal':
                    $this->prepare_renew_modal();
                    break;

                case 'renew_lottery':
                    $this->renew_lottery();
                    break;

                case 'delete_lottery':
                    $this->delete_lottery();
                    break;

                case 'delete_company':
                    $this->delete_company();
                    break;

                case 'get_participants':
                    $this->get_participants();
                    break;

                case 'approve_winner':
                    $this->approve_winner();
                    break;

                case 'reject_winner':
                    $this->reject_winner();
                    break;

                case 'get_withdrawal_requests':
                    $this->get_withdrawal_requests();
                    break;

                case 'approve_withdrawal_request':
                    $this->approve_withdrawal_request();
                    break;

                case 'reject_withdrawal_request':
                    $this->reject_withdrawal_request();
                    break;

                case 'get_logs':
                    $this->get_logs();
                    break;

                case 'get_weekly_sales_amount':
                    $this->get_weekly_sales_amount();
                    break;

                case 'get_weekly_sales':
                    $this->get_weekly_sales();
                    break;

                case 'get_weekly_loosers':
                    $this->get_weekly_loosers();
                    break;

                case 'get_weekly_winners':
                    $this->get_weekly_winners();
                    break;

                case 'get_each_month_sales':
                    $this->get_each_month_sales();
                    break;

                case 'get_today_reports':
                    $this->get_today_reports();
                    break;

                default:
                    wp_send_json_error( [ 'message' => __( 'Invalid action', 'plotto' ) ] );
                    break;
            }
        }

        private function save_lottery()
        {
            global $wpdb;
            $insert_result = $update_result = false;
            $current_user_id = get_current_user_id();
            $lottery_table = "{$wpdb->prefix}plot_lotteries";
            $prize_table = "{$wpdb->prefix}plot_prizes";
            $lottery_name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
            $lottery_content = isset( $_POST['content'] ) ? wp_kses_post( $_POST['content'] ) : '';
            $lottery_color = isset( $_POST['color'] ) ? $_POST['color'] : '';
            $lottery_company = isset( $_POST['company'] ) ? sanitize_text_field( wp_unslash( $_POST['company'] ) ) : '';
            $lottery_logo = isset( $_POST['logo'] ) ? sanitize_text_field( wp_unslash( $_POST['logo'] ) ) : 0;
            $lottery_block_count = isset( $_POST['blockCount'] ) ? sanitize_text_field( wp_unslash( $_POST['blockCount'] ) ) : '';
            $lottery_choosen_block_count = isset( $_POST['choosenBlockCount'] ) ? sanitize_text_field( wp_unslash( $_POST['choosenBlockCount'] ) ) : '';
            $lottery_bonuse_count = isset( $_POST['bonuseCount'] ) ? sanitize_text_field( wp_unslash( $_POST['bonuseCount'] ) ) : 0;
            $lottery_choosen_bonuse_count = isset( $_POST['choosenBonuseCount'] ) ? sanitize_text_field( wp_unslash( $_POST['choosenBonuseCount'] ) ) : 0;
            $lottery_total_price = isset( $_POST['totalPrice'] ) ? sanitize_text_field( wp_unslash( floatval( str_replace( ',', '', $_POST['totalPrice'] ) ) ) ) : 0;
            $lottery_prize_currency = isset( $_POST['prizeCurrency'] ) ? sanitize_text_field( wp_unslash( $_POST['prizeCurrency'] ) ) : '';
            $lottery_ticket_price = isset( $_POST['ticketPrice'] ) ? sanitize_text_field( wp_unslash( floatval( str_replace( ',', '', $_POST['ticketPrice'] ) ) ) ) : 0;
            $lottery_expire_time = isset( $_POST['expireTime'] ) ? sanitize_text_field( wp_unslash( $_POST['expireTime'] ) ) : '';
            $lottery_fake_participant = isset( $_POST['fakeParticipant'] ) ? sanitize_text_field( wp_unslash( intval( str_replace( ',', '', $_POST['fakeParticipant'] ) ) ) ) : 0;
            $lottery_prizes = isset( $_POST['prizes'] ) ? $_POST['prizes'] : [];

            include_once( PLotto_PATH . 'inc/helpers/plotto-wpdb-helper.php' );
            $wpdb_helper = new PLottoWpdbHelper();

            if( empty( $lottery_name ) )
                wp_send_json_error( [ 'message' => __( 'Lottery name is required', 'plotto' ) ] );

            if( empty( $lottery_company ) )
                wp_send_json_error( [ 'message' => __( 'Company is required', 'plotto' ) ] );

            if( empty( $lottery_color ) )
                wp_send_json_error( [ 'message' => __( 'Color is required', 'plotto' ) ] );

            if ( empty( $lottery_block_count ) )
                wp_send_json_error( [ 'message' => __( 'Block count is required', 'plotto' ) ] );

            if ( $lottery_block_count > 90 )
                wp_send_json_error( [ 'message' => __( 'Block count must be less than 90', 'plotto' ) ] );

            if ( empty( $lottery_choosen_block_count ) )
                wp_send_json_error( [ 'message' => __( 'Chosen block count is required', 'plotto' ) ] );

            if ( $lottery_choosen_block_count > 90 )
                wp_send_json_error( [ 'message' => __( 'Chosen block must be less than 90', 'plotto' ) ] );

            if ( $lottery_bonuse_count > 90 )
                wp_send_json_error( [ 'message' => __( 'Bonuse must be less than 90', 'plotto' ) ] );

            if ( $lottery_choosen_bonuse_count > 90 )
                wp_send_json_error( [ 'message' => __( 'Chosen bonuse count must be less than 90', 'plotto' ) ] );

            if( empty( $lottery_total_price ) )
                wp_send_json_error( [ 'message' => __( 'Total price is required', 'plotto' ) ] );

            if( empty( $lottery_prize_currency ) )
                wp_send_json_error( [ 'message' => __( 'Prize currency is required', 'plotto' ) ] );

            if( empty( $lottery_ticket_price ) )
                wp_send_json_error( [ 'message' => __( 'Ticket price is required', 'plotto' ) ] );

            if( $_POST['isUpdate'] === 'false' && ( empty( $lottery_expire_time ) || $lottery_expire_time <= wp_date( 'Y-m-d\TH:i' ) ) )
                wp_send_json_error( [ 'message' => __( 'Expire time is required', 'plotto' ) ] );

            if( empty( $lottery_prizes ) )
                wp_send_json_error( [ 'message' => __( 'Prizes is required', 'plotto' ) ] );

            // Save lottery data to database
            $lottery_data = array(
                'name' => $lottery_name,
                'content' => $lottery_content,
                'total_price' => $lottery_total_price,
                'prize_currency' => $lottery_prize_currency,
                'ticket_price' => $lottery_ticket_price,
                'fake_participant' => $lottery_fake_participant,
                'color' => $lottery_color,
                'company' => $lottery_company,
                'block_count' => $lottery_block_count,
                'choosen_block' => $lottery_choosen_block_count,
                'bonuse_count' => $lottery_bonuse_count,
                'choosen_bonuse' => $lottery_choosen_bonuse_count,
                'updater' => $current_user_id,
                'update_date' => current_time( 'mysql' )
            );
            if( $_POST['isUpdate'] === 'false' )
            {
                $lottery_data['expire_time'] = $lottery_expire_time;
                $lottery_data['registrar'] = $current_user_id;
                $lottery_data['creation_date'] = current_time( 'mysql' );
                $wpdb->insert(
                    $lottery_table, $lottery_data
                );

                $lottery_id = $wpdb->insert_id;

                if( $lottery_id )
                {
                    PLottoHelper::log( sprintf( __( 'Lottery created successfully. (ID: %d, name: "%s")', 'plotto' ) , $lottery_id,  $lottery_name) );
                    $args = array(
                        'post_author' => $current_user_id,
                        'post_content' => $lottery_content,
                        'post_status' => 'publish', // (draft | pending | publish)
                        'post_title' => $lottery_name,
                        'post_parent' => '',
                        'post_type' => 'product'
                    );
                    // Create a lottery WooCommerce product
                    $post_id = wp_insert_post( $args );
                    if( ! empty( $lottery_logo ) )
                    {
                        set_post_thumbnail( $post_id, $lottery_logo );
                    }

                    $where = [ 'ID' => $lottery_id ];
                    $result = $wpdb->update(
                        $lottery_table, [ 'wc_product_id' => $post_id ], $where
                    );
                    wp_set_object_terms( $post_id, 'lottery', 'product_type' );
                    update_post_meta( $post_id, '_price', $lottery_ticket_price );
                    update_post_meta( $post_id, '_regular_price', $lottery_ticket_price );
                    update_post_meta( $post_id, '_lottery_block', $lottery_block_count );
                    update_post_meta( $post_id, '_lottery_choosen_block', $lottery_choosen_block_count );
                    update_post_meta( $post_id, '_lottery_bonuse', $lottery_bonuse_count );
                    update_post_meta( $post_id, '_lottery_choosen_bonuse', $lottery_choosen_bonuse_count );
                    update_post_meta( $post_id, '_lottery_total_prize', $lottery_total_price );
                    update_post_meta( $post_id, '_lottery_total_prize_currency', $lottery_prize_currency );
                    update_post_meta( $post_id, '_lottery_fake_participant', $lottery_fake_participant );
                    update_post_meta( $post_id, '_lottery_color', $lottery_color );
                    update_post_meta( $post_id, '_lottery_company', $lottery_company );
                    update_post_meta( $post_id, '_lottery_expire_time', $lottery_expire_time );
                    update_post_meta( $post_id, '_lottery_id', $lottery_id );
                    PLottoHelper::log( sprintf( __( 'Lottery related product created successfully. (ID: %d, name: "%s")', 'plotto' ) , $post_id, $lottery_name ) );
                    $prize_data = [];
                    foreach( $lottery_prizes as $lottery_prize )
                    {
                        $prize_data[] = array(
                            'lottery' => $lottery_id,
                            'block_coordination' => $lottery_prize['blockCoordination'],
                            'bonuse_coordination' => $lottery_prize['bonuseCoordination'],
                            'amount' => floatval( str_replace( ',', '', $lottery_prize['prize'] ) ),
                            'registrar' => $current_user_id,
                            'updater' => $current_user_id,
                            'creation_date' => current_time( 'mysql' ),
                            'update_date' => current_time( 'mysql' )
                        );
                    }

                    if( ! empty( $prize_data ) )
                    {
                        $insert_result = $wpdb_helper->insert_multiple( $prize_table, $prize_data );

                        if( ! $insert_result )
                            wp_send_json_error( array( 'message' => __( 'Saving prizes data unsuccessfull!', 'plotto' ) ) );

                        PLottoHelper::log( sprintf( __( 'Prizes table created successfully for lottery "%s".', 'plotto' ), $lottery_name ) );
                    }
                    wp_send_json_success( [ 'message' => __( 'Lottery saved successfully', 'plotto' ), 'url' => add_query_arg( [ 'action' => 'plot-dashboard', 'p' => 'add-lottery', 'pid' => $lottery_id, '_plot_nonce' => wp_create_nonce( 'plot-dashboard' ) ], admin_url( 'admin.php' ) ) ] );
                }
            } else {
                $where = [ 'ID' => $_POST['lotteryId'] ];
                $result = $wpdb->update(
                    $lottery_table, $lottery_data, $where
                );
                if( $result )
                {
                    PLottoHelper::log( sprintf( __( 'Lottery updated successfully. (ID: %d, name: "%s")', 'plotto' ) , $_POST['lotteryId'], $lottery_name ) );
                    $post_id = $wpdb->get_var(
                        $wpdb->prepare(
                            "SELECT `wc_product_id` FROM `{$wpdb->prefix}plot_lotteries` WHERE `ID` = %d",
                            $_POST['lotteryId']
                        )
                    );
                    wp_update_post( [ 'ID' => $post_id, 'post_title' => $lottery_name, 'post_content' => $lottery_content ] );
                    update_post_meta( $post_id, '_price', $lottery_ticket_price );
                    update_post_meta( $post_id, '_regular_price', $lottery_ticket_price );
                    update_post_meta( $post_id, '_lottery_block', $lottery_block_count );
                    update_post_meta( $post_id, '_lottery_choosen_block', $lottery_choosen_block_count );
                    update_post_meta( $post_id, '_lottery_bonuse', $lottery_bonuse_count );
                    update_post_meta( $post_id, '_lottery_choosen_bonuse', $lottery_choosen_bonuse_count );
                    update_post_meta( $post_id, '_lottery_total_prize', $lottery_total_price );
                    update_post_meta( $post_id, '_lottery_total_prize_currency', $lottery_prize_currency );
                    update_post_meta( $post_id, '_lottery_fake_participant', $lottery_fake_participant );
                    update_post_meta( $post_id, '_lottery_color', $lottery_color );
                    update_post_meta( $post_id, '_lottery_company', $lottery_company );
                    update_post_meta( $post_id, '_lottery_expire_time', $lottery_expire_time );
                    PLottoHelper::log( sprintf( __( 'Lottery related product updated successfully. (ID: %d, name: "%s")', 'plotto' ) , $post_id, $lottery_name ) );
                    $prize_data = $update_queries = [];
                    foreach( $lottery_prizes as $lottery_prize )
                    {
                        if( $lottery_prize['id'] == 0 )
                        {
                            $prize_data[] = array(
                                'lottery' => $_POST['lotteryId'],
                                'block_coordination' => $lottery_prize['blockCoordination'],
                                'bonuse_coordination' => $lottery_prize['bonuseCoordination'],
                                'amount' => floatval( str_replace( ',', '', $lottery_prize['prize'] ) ),
                                'registrar' => $current_user_id,
                                'updater' => $current_user_id,
                                'creation_date' => current_time( 'mysql' ),
                                'update_date' => current_time( 'mysql' )
                            );
                        } else {
                            $update_queries[] = sprintf(
                                "UPDATE `{$wpdb->prefix}plot_prizes` SET `block_coordination` = '%s', `bonuse_coordination` = '%s', `amount` = %f, `updater` = %d, `update_date` = '%s' WHERE `ID` = %d;",
                                $lottery_prize['blockCoordination'],
                                $lottery_prize['bonuseCoordination'],
                                floatval( str_replace( ',', '', $lottery_prize['prize'] ) ),
                                $current_user_id,
                                current_time( 'mysql' ),
                                $lottery_prize['id']
                            );
                        }
                    }

                    if( ! empty( $prize_data ) )
                    {
                        $insert_result = $wpdb_helper->insert_multiple( $prize_table, $prize_data );

                        if( ! $insert_result )
                            wp_send_json_error( array( 'message' => __( 'Saving prizes data unsuccessfull!', 'plotto' ) ) );
                    }

                    if( ! empty( $update_queries ) )
                    {
                        $update_result = $wpdb_helper->update_multiple( $update_queries );

                        if( ! empty( $update_result ) )
                            wp_send_json_error( array( 'message' => __( 'Saving prizes data unsuccessfull!', 'plotto' ) ) );
                    }

                    if( $insert_result || $update_result )
                    {
                        PLottoHelper::log( sprintf( __( 'Prizes table updated successfully for lottery "%s".', 'plotto' ), $lottery_name ) );
                    }

                    $prizes = $wpdb->get_results(
                        $wpdb->prepare(
                            "SELECT `ID`, `lottery`, `block_coordination`, `bonuse_coordination`, `amount` FROM `{$wpdb->prefix}plot_prizes` WHERE `lottery` = %d",
                            $_POST['lotteryId']
                        )
                    );

                    $html = '';

                    if( ! empty( $prizes ) && is_array( $prizes ) ):
                        foreach( $prizes as $key => $prize ):
                            $hide = ( $key === 0 ) ? 'd-none' : '';
                            $html .= '<div data-repeater-item class="col-12 mb-3 repeater-prizes-item" data-id="' . $prize->ID . '" data-lid="' . $prize->lottery . '">
                                <div class="row">
                                    <div class="form-group col-md-4 col-sm-12">
                                        <label>' . __( 'Block coordination', 'plotto' ) . '</label>
                                        <input type="number" name="prizes[' . $key . '][block_coordination]" class="form-control" min="1" step="1" value="' . $prize->block_coordination . '" />
                                    </div>
                                    <div class="form-group col-md-4 col-sm-12">
                                        <label>' . __( 'Bonuse coordination', 'plotto' ) . '</label>
                                        <input type="number" name="prizes[' . $key . '][bonuse_coordination]" class="form-control" min="0" step="1" value="' . $prize->bonuse_coordination . '" />
                                    </div>
                                    <div class="form-group col-md-4 col-sm-12">
                                        <label>' . __( 'Prize', 'plotto' ) . '</label>
                                        <div class="input-group">
                                            <input type="text" name="prizes[' . $key . '][prize]" class="form-control number-separator" min="1" step="any" value="' . $prize->amount . '" />
                                            <span class="input-group-text">' . get_woocommerce_currency_symbol() . '</span>
                                        </div>
                                    </div>
                                    <div class="mt-3 ' . $hide . '">
                                        <span data-repeater-delete class="btn btn-danger w-100">
                                            <i class="fa-solid fa-trash"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>';
                        endforeach;
                        $html .= '<script>jQuery(function($){easyNumberSeparator({selector: \'.number-separator\',})})</script>';
                    endif;

                    wp_send_json_success( [ 'message' => __( 'Lottery updated successfully', 'plotto' ), 'html' => $html ] );
                }
            }

            wp_send_json_error( [ 'message' => __( 'Failed to save lottery', 'plotto' ) ] );
        }

        private function save_company()
        {
            global $wpdb;
            $current_user_id = get_current_user_id();
            $company_table = "{$wpdb->prefix}plot_companies";
            $company_name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
            $company_content = isset( $_POST['content'] ) ? wp_kses_post( $_POST['content'] ) : '';
            $company_logo = isset( $_POST['logo'] ) ? sanitize_text_field( wp_unslash( $_POST['logo'] ) ) : 0;

            if( empty( $company_name ) )
                wp_send_json_error( [ 'message' => __( 'Company name is required', 'plotto' ) ] );

            if( empty( $company_logo ) )
                wp_send_json_error( [ 'message' => __( 'Company logo is required', 'plotto' ) ] );

            // Save company data to database
            $company_data = array(
                'name' => $company_name,
                'description' => $company_content,
                'logo' => $company_logo,
                'updater' => $current_user_id,
                'update_date' => current_time( 'mysql' )
            );

            if( $_POST['isUpdate'] === 'false' )
            {
                $company_data['registrar'] = $current_user_id;
                $company_data['creation_date'] = current_time( 'mysql' );
                $wpdb->insert(
                    $company_table, $company_data
                );
                $company_id = $wpdb->insert_id;
                if( $company_id )
                {
                    PLottoHelper::log( sprintf( __( 'Company created successfully. (ID: %d, name: "%s")', 'plotto' ) , $company_id, $company_name ) );
                    wp_send_json_success( [ 'message' => __( 'Company saved successfully', 'plotto' ), 'url' => add_query_arg( [ 'action' => 'plot-dashboard', 'p' => 'add-company', 'cid' => $wpdb->insert_id, '_plot_nonce' => wp_create_nonce( 'plot-dashboard' ) ], admin_url( 'admin.php' ) ) ] );
                }
            } else {
                $where = [ 'ID' => $_POST['companyId'] ];
                $result = $wpdb->update(
                    $company_table, $company_data, $where
                );
                if( $result )
                {
                    PLottoHelper::log( sprintf( __( 'Company updated successfully. (ID: %d, name: "%s")', 'plotto' ) , $_POST['companyId'], $company_name ) );
                    wp_send_json_success( [ 'message' => __( 'Company updated successfully', 'plotto' ) ] );
                }
            }

            wp_send_json_error( [ 'message' => __( 'Failed to save company', 'plotto' ) ] );
        }

        private function delete_prize()
        {
            global $wpdb;
            $prize_table = "{$wpdb->prefix}plot_prizes";
            $prize_id = isset( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : 0;
            $lottery_id = isset( $_POST['lotteryId'] ) ? sanitize_text_field( wp_unslash( $_POST['lotteryId'] ) ) : 0;

            if( empty( $prize_id ) )
                wp_send_json_error( [ 'message' => __( 'Prize id is required', 'plotto' ) ] );

            if( empty( $lottery_id ) )
                wp_send_json_error( [ 'message' => __( 'Lottery id is required', 'plotto' ) ] );

            $result = $wpdb->delete(
                $prize_table,
                array(
                    'ID' => $prize_id
                )
            );

            if( ! $result )
                wp_send_json_error( array( 'message' => __( 'Deleting prize unsuccessfull!', 'plotto' ) ) );

            PLottoHelper::log( sprintf( __( 'Prize deleted successfully from lottery %d.', 'plotto' ) , $lottery_id ) );

            $prizes = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT `ID`, `lottery`, `block_coordination`, `bonuse_coordination`, `amount` FROM `{$wpdb->prefix}plot_prizes` WHERE `lottery` = %d",
                    $lottery_id
                )
            );

            $html = '';

            if( ! empty( $prizes ) && is_array( $prizes ) ):
                foreach( $prizes as $key => $prize ):
                    $hide = ( $key === 0 ) ? 'd-none' : '';
                    $html .= '<div data-repeater-item class="col-12 mb-3 repeater-prizes-item" data-id="' . $prize->ID . '" data-lid="' . $prize->lottery . '">
                        <div class="row">
                            <div class="form-group col-md-4 col-sm-12">
                                <label>' . __( 'Block coordination', 'plotto' ) . '</label>
                                <input type="number" name="prizes[' . $key . '][block_coordination]" class="form-control" min="1" step="1" value="' . $prize->block_coordination . '" />
                            </div>
                            <div class="form-group col-md-4 col-sm-12">
                                <label>' . __( 'Bonuse coordination', 'plotto' ) . '</label>
                                <input type="number" name="prizes[' . $key . '][bonuse_coordination]" class="form-control" min="0" step="1" value="' . $prize->bonuse_coordination . '" />
                            </div>
                            <div class="form-group col-md-4 col-sm-12">
                                <label>' . __( 'Prize', 'plotto' ) . '</label>
                                <input type="number" name="prizes[' . $key . '][prize]" class="form-control" min="1" step="any" value="' . $prize->amount . '" />
                            </div>
                            <div class="mt-3 ' . $hide . '">
                                <span data-repeater-delete class="btn btn-danger w-100">
                                    <i class="fa-solid fa-trash"></i>
                                </span>
                            </div>
                        </div>
                    </div>';
                endforeach;
            endif;

            wp_send_json_success( array( 'message' => __( 'Prize successfully deleted.', 'plotto' ), 'html' => $html ) );
        }

        private function get_lotteries()
        {
            global $wpdb;
            $table1 = "{$wpdb->prefix}plot_lotteries";
            $primary_key = 'ID';

            include_once( PLotto_PATH . 'inc/helpers/plotto-ssp.php' );

            $columns = array(
                array( 'db' => '`t1`.`ID`', 'as' => 'lottery_id', 'field' => 'lottery_id', 'dt' => 0 ),
                array( 'db' => '`t1`.`name`', 'as' => 'lottery_name', 'field' => 'lottery_name', 'dt' => 1 ),
                array( 'db' => '`t1`.`creation_date`', 'as' => 'lottery_creation_date', 'field' => 'lottery_creation_date', 'dt' => 2 ),
                array( 'db' => '`t1`.`answer`', 'as' => 'lottery_answer', 'field' => 'lottery_answer', 'dt' => 3,
                    'formatter' => function( $d, $row ) {
                        return empty( $d ) ? '-' : $d;
                    }
                ),
                array( 'db' => '`t1`.`answer_date`', 'as' => 'lottery_answer_date', 'field' => 'lottery_answer_date', 'dt' => 4,
                    'formatter' => function( $d, $row ) {
                        return empty( $d ) ? '-' : $d;
                    }
                ),
                array( 'db' => 'CONCAT(`t1`.`total_price`, \'||\', `t1`.`prize_currency`)', 'as' => 'total_amount', 'field' => 'total_amount', 'dt' => 5, 'field' => 'total_amount',
                    'formatter' => function( $d, $row ) {
                        $arr = explode( '||', $d );
                        return ! empty( $arr ) ? wc_price( $arr[0], array(
                            'currency' => $arr[1]
                        ) ) : '-';
                    }
                ),
                array( 'db' => '`t1`.`ticket_price`', 'as' => 'ticket_price', 'field' => 'ticket_price', 'dt' => 6,
                    'formatter' => function( $d, $row ) {
                        return wc_price( $d );
                    }
                ),
                array( 'db' => 'CONCAT(`t1`.`choosen_block`, \'/\', `t1`.`choosen_bonuse`)', 'as' => 'choosable_blocks_bonuses', 'field' => 'choosable_blocks_bonuses', 'dt' => 7 ),
                array( 'db' => '(SELECT COUNT(`ID`) FROM ' . $wpdb->prefix . 'plot_participants' . ' AS `t2` WHERE (`t2`.`lottery` = `t1`.`ID`))', 'as' => 'participants_count', 'field' => 'participants_count', 'dt' => 8 ),
                array( 'db' => '`t1`.`ID`', 'as' => 'lottery', 'field' => 'lottery', 'dt' => 9,
                    'formatter' => function( $d, $row ) {
                        return $row[12] === 1 ? '[previous-lotteries id="' . $row[13] . '"]' : '[previous-lotteries id="' . $d . '"]';
                    }
                ),
                array( 'db' => '`t1`.`status`', 'as' => 'lottery_status', 'field' => 'lottery_status', 'dt' => 10,
                    'formatter' => function( $d, $row ) {
                        if( $row[12] === 1 )
                        {
                            return __( 'Finished', 'plotto' );
                        } else {
                            switch( $d )
                            {
                                case 'active':
                                    return __( 'Active', 'plotto' );
                                    break;
                                case 'deactive':
                                    return __( 'Deactive', 'plotto' );
                                    break;
                                default:
                                    return __( 'Expired', 'plotto' );
                                    break;
                            }
                        }
                    }
                ),
                array( 'db' => '`t1`.`ID`', 'as' => 'lot_id', 'field' => 'lot_id', 'dt' => 11,
                    'formatter' => function( $d, $row ) {
                        if( $row[12] === 1 )
                        {
                            $actions = '<a href="' . add_query_arg( [ 'action' => 'plot-dashboard', 'p' => 'participants', 'lot_id' => $row[0], '_plot_nonce' => wp_create_nonce( 'plot-dashboard' ) ], admin_url( 'admin.php' ) ) . '" type"button" class="btn btn-sm btn-info w-100 mb-1">' . __( 'Participants', 'plotto' ) . '</a>';
                        } elseif( $row[10] === 'active' )
                        {
                            $actions = '<button type"button" class="btn btn-sm btn-danger w-100 mb-1 delete-lottery" data-id="' . $d . '">' . __( 'Delete', 'plotto' ) . '</button><a href="' . add_query_arg( [ 'action' => 'plot-dashboard', 'p' => 'add-lottery', 'pid' => $d, '_plot_nonce' => wp_create_nonce( 'plot-dashboard' ) ], admin_url() ) . '" class="btn btn-sm w-100 mb-1 btn-primary">' . __( 'Edit', 'plotto' ) . '</a><button type"button" class="btn btn-sm btn-info w-100 mb-1 finish-lottery" data-id="' . $d . '">' . __( 'Finish', 'plotto' ) . '</button>';
                        } else {
                            $actions = '<button type"button" class="btn btn-sm btn-danger w-100 mb-1 delete-lottery" data-id="' . $d . '">' . __( 'Delete', 'plotto' ) . '</button><a href="' . add_query_arg( [ 'action' => 'plot-dashboard', 'p' => 'add-lottery', 'pid' => $d, '_plot_nonce' => wp_create_nonce( 'plot-dashboard' ) ], admin_url() ) . '" class="btn btn-sm w-100 mb-1 btn-primary">' . __( 'Edit', 'plotto' ) . '</a><button type"button" class="btn btn-sm btn-success w-100 mb-1 renew-lottery" data-id="' . $d . '">' . __( 'Renew', 'plotto' ) . '</button>';
                        }

                        return $actions;
                    }
                ),
                array( 'db' => 'is_backup', 'as' => 'is_backup', 'field' => 'is_backup', 'dt' => 12 ),
                array( 'db' => 'lottery', 'as' => 'main_lottery', 'field' => 'main_lottery', 'dt' => 13 ),
            );

            // SQL server connection information
            $sql_details = array(
                'user' => DB_USER,
                'pass' => DB_PASSWORD,
                'db'   => DB_NAME,
                'host' => DB_HOST
            );

            $joinQuery = " FROM `$table1` AS `t1`";

            die (
                wp_json_encode(
                    SSP::simple( $_POST, $sql_details, $table1, $primary_key, $columns, $joinQuery )
                )
            );
        }

        private function get_companies()
        {
            global $wpdb;
            $table = "{$wpdb->prefix}plot_companies";
            $primary_key = 'ID';

            include_once( PLotto_PATH . 'inc/helpers/plotto-ssp.php' );

            $columns = array(
                array( 'db' => 'ID', 'dt' => 0 ),
                array( 'db' => 'name',  'dt' => 1 ),
                array( 'db' => 'logo', 'dt' => 2,
                    'formatter' => function( $d, $row ) {
                        return '<img src="' . wp_get_attachment_url( $d ) . '" width="150" alt="company logo">';
                    }
                ),
                array( 'db' => 'description', 'dt' => 3 )
            );

            // SQL server connection information
            $sql_details = array(
                'user' => DB_USER,
                'pass' => DB_PASSWORD,
                'db'   => DB_NAME,
                'host' => DB_HOST
            );

            die (
                wp_json_encode(
                    SSP::simple( $_POST, $sql_details, $table, $primary_key, $columns )
                )
            );
        }

        private function prepare_finish_modal()
        {
            global $wpdb;
            $lottery_id = isset( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';

            if( empty( $lottery_id ) ) {
                wp_send_json_error( [ 'message' => __( 'Lottery id is required', 'plotto' ) ] );
            } else {
                $result = $wpdb->get_results(
                    $wpdb->prepare(
                        "SELECT `bonuse_count` FROM `{$wpdb->prefix}plot_lotteries` WHERE `ID` = %d",
                        $lottery_id
                    )
                );

                if( empty( $result ) )
                    wp_send_json_error( [ 'message' => __( 'Lottery not found!', 'plotto' ) ] );

                $class = ! empty( $result[0]->bonuse_count ) ? 'col-md-6 col-sm-12' : 'col-12';
                $html = '<div class="row">
                    <div class="' . $class . '">
                        <div class="form-group">
                            <label for="win-block-code">' . __( 'Win block code', 'plotto' ) . '</label>
                            <input type="text" class="form-control" id="win-block-code" name="win_block_code">
                        </div>
                    </div>';

                if( ! empty( $result[0]->bonuse_count ) )
                {
                    $html .= '<div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label for="win-bonuse-code">' . __( 'Win bonuse code', 'plotto' ) . '</label>
                            <input type="text" class="form-control" id="win-bonuse-code" name="win_bonuse_code">
                        </div>
                    </div>';
                }

                $html .= '</div>';
                wp_send_json_success( [ 'html' => $html ] );
            }
        }

        private function finish_lottery()
        {
            global $wpdb;
            $table = "{$wpdb->prefix}plot_lotteries";
            $winners_table = "{$wpdb->prefix}plot_winners";
            $lottery_id = isset( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';
            $win_block_code = isset( $_POST['winBlockCode'] ) ? sanitize_text_field( wp_unslash( $_POST['winBlockCode'] ) ) : '';
            $win_bonuse_code = isset( $_POST['winBonuseCode'] ) ? sanitize_text_field( wp_unslash( $_POST['winBonuseCode'] ) ) : '';

            if( empty( $lottery_id ) ) {
                wp_send_json_error( [ 'message' => __( 'Lottery id is required', 'plotto' ) ] );
            } else {
                $results = $wpdb->get_results(
                    $wpdb->prepare(
                        "SELECT
                            `name`,
                            `block_count`,
                            `choosen_block`,
                            `bonuse_count`,
                            `choosen_bonuse`,
                            `wc_product_id`
                        FROM `{$wpdb->prefix}plot_lotteries`
                        WHERE `ID` = %d",
                        $lottery_id
                    )
                );

                $prizes_results = $wpdb->get_results(
                    $wpdb->prepare(
                        "SELECT
                            `ID`,
                            `block_coordination`,
                            `bonuse_coordination`,
                            `amount`
                        FROM `{$wpdb->prefix}plot_prizes`
                        WHERE `lottery` = %d ORDER BY `amount` DESC",
                        $lottery_id
                    )
                );

                $participant_results = $wpdb->get_results(
                    $wpdb->prepare(
                        "SELECT
                            `ID`,
                            `user_id`,
                            `block`,
                            `bonuse`
                        FROM `{$wpdb->prefix}plot_participants`
                        WHERE `lottery` = %d AND `status` = '%s'",
                        $lottery_id,
                        'undetermined'
                    )
                );

                if( empty( $results ) )
                    wp_send_json_error( [ 'message' => __( 'Lottery not found!', 'plotto' ) ] );

                $answer = $win_block_code;

                if( empty( $win_block_code ) )
                    wp_send_json_error( [ 'message' => __( 'Win block codes is required!', 'plotto' ) ] );

                $win_block_code_arr = explode( '-', $win_block_code );

                if( count( $win_block_code_arr ) != $results[0]->choosen_block )
                {
                    wp_send_json_error( [ 'message' => __( 'Win block codes is not valid!', 'plotto' ) ] );
                }

                foreach( $win_block_code_arr as $block )
                {
                    if( $results[0]->block_count < $block || $block <= 0 )
                    {
                        wp_send_json_error( [ 'message' => __( 'Win block codes is not valid!', 'plotto' ) ] );
                    }
                }

                $dups = [];
                foreach( array_count_values( $win_block_code_arr ) as $val => $c )
                {
                    if( $c > 1 ) $dups[] = $val;
                }

                if( ! empty( $dups ) )
                {
                    wp_send_json_error( [ 'message' => sprintf( __( 'Win block codes are duplicated! The duplicated value(s): "%s"', 'plotto' ), implode( ',', $dups ) ) ] );
                }

                if( ! empty( $results[0]->bonuse_count ) )
                {
                    if( empty( $win_bonuse_code ) )
                        wp_send_json_error( [ 'message' => __( 'Win bonuse codes is required!', 'plotto' ) ] );

                    $win_bonuse_code_arr = explode( '-', $win_bonuse_code );

                    if( count( $win_bonuse_code_arr ) != $results[0]->choosen_bonuse )
                    {
                        wp_send_json_error( [ 'message' => __( 'Win bonuse codes is not valid!', 'plotto' ) ] );
                    }

                    foreach( $win_bonuse_code_arr as $bonuse )
                    {
                        if( $results[0]->bonuse_count < $bonuse || $bonuse <= 0 )
                        {
                            wp_send_json_error( [ 'message' => __( 'Win bonuse codes is not valid!', 'plotto' ) ] );
                        }
                    }

                    $dups = [];
                    foreach( array_count_values( $win_bonuse_code_arr ) as $val => $c )
                    {
                        if( $c > 1 ) $dups[] = $val;
                    }

                    if( ! empty( $dups ) )
                    {
                        wp_send_json_error( [ 'message' => sprintf( __( 'Win bonuse codes are duplicated! The duplicated value(s): "%s"', 'plotto' ), implode( ',', $dups ) ) ] );
                    }

                    $answer .= '/' . $win_bonuse_code;
                }

                $update_result = $wpdb->update(
                    $table, [
                        'status' => 'expired',
                        'answer' => $answer,
                        'answer_date' => current_time( 'mysql' ),
                        'updater' => get_current_user_id(),
                        'update_date' => current_time( 'mysql' )
                    ],
                    [
                        'ID' => $lottery_id
                    ]
                );

                if( ! $update_result )
                    wp_send_json_error( [ 'message' => __( 'Updating lottery unsuccessfull!', 'plotto' ) ] );

                PLottoHelper::log( sprintf( __( 'Lottery finished successfully. (ID: %d, name: "%s")', 'plotto' ) , $lottery_id, $results[0]->name ) );

                $wpdb->get_results(
                    $wpdb->prepare(
                        "INSERT INTO `{$wpdb->prefix}plot_lotteries`( `is_backup`, `lottery`, `name`, `content`, `total_price`, `prize_currency`, `ticket_price`, `expire_time`, `fake_participant`, `color`, `company`, `block_count`, `choosen_block`, `bonuse_count`, `choosen_bonuse`, `wc_product_id`, `status`, `answer`, `answer_date`, `registrar`, `updater`, `creation_date`, `update_date` ) SELECT 1, `ID`, `name`, `content`, `total_price`, `prize_currency`, `ticket_price`, `expire_time`, `fake_participant`, `color`, `company`, `block_count`, `choosen_block`, `bonuse_count`, `choosen_bonuse`, `wc_product_id`, 'deactive', `answer`, `answer_date`, `registrar`, `updater`, `creation_date`, `update_date` FROM `{$wpdb->prefix}plot_lotteries` WHERE `ID` = %d",
                        $lottery_id
                    )
                );

                if( $wpdb->last_error )
                    wp_send_json_error( [ 'message' => sprintf( __( 'Wpdb error: "%s"', 'plotto' ), $wpdb->last_error ) ] );

                $bk_lottery_id = $wpdb->insert_id;

                $post_id = $results[0]->wc_product_id;
                wp_update_post( [ 'ID' => $post_id, 'post_status' => 'private' ] );
                PLottoHelper::log( sprintf( __( 'Status of product ID %d changed to private', 'plotto' ) , $post_id ) );

                $data = [];

                foreach( $participant_results as $participant )
                {
                    // $winner = false;
                    $participant_blocks = explode( '-', $participant->block );
                    $participant_bonuses = explode( '-', $participant->bonuse );

                    if( ! empty( $results[0]->bonuse_count ) )
                    {
                        $participant_match_bonuses = array_filter( $participant_bonuses, function( $bonuse ) use( $win_bonuse_code_arr ) {
                            return in_array( $bonuse, $win_bonuse_code_arr );
                        } );
                    }

                    $participant_match_blocks = array_filter( $participant_blocks, function( $block ) use( $win_block_code_arr ) {
                        return in_array( $block, $win_block_code_arr );
                    } );

                    foreach( $prizes_results as $prize )
                    {
                        if( ! empty( $results[0]->bonuse_count ) )
                        {
                            if( count( $participant_match_bonuses ) >= $prize->bonuse_coordination && count( $participant_match_blocks ) >= $prize->block_coordination )
                            {
                                // $winner = true;
                                $data[] = [
                                    'lottery' => $lottery_id,
                                    'backup_lottery' => $bk_lottery_id,
                                    'user_id' => $participant->user_id,
                                    'participant' => $participant->ID,
                                    'block' => implode( '-', $participant_blocks ),
                                    'bonuse' => implode( '-', $participant_bonuses ),
                                    'block_coordination' => $prize->block_coordination,
                                    'bonuse_coordination' => $prize->bonuse_coordination,
                                    'prize_id' => $prize->ID,
                                    'status' => 'pending',
                                    'registrar' => get_current_user_id(),
                                    'updater' => get_current_user_id(),
                                    'creation_date' => current_time( 'mysql' ),
                                    'update_date' => current_time( 'mysql' )
                                ];
                                break;
                            }
                        }else {
                            if( count( $participant_match_blocks ) >= $prize->block_coordination )
                            {
                                // $winner = true;
                                $data[] = [
                                    'lottery' => $lottery_id,
                                    'backup_lottery' => $bk_lottery_id,
                                    'user_id' => $participant->user_id,
                                    'participant' => $participant->ID,
                                    'block' => implode( '-', $participant_blocks ),
                                    'bonuse' => null,
                                    'block_coordination' => $prize->block_coordination,
                                    'bonuse_coordination' => $prize->bonuse_coordination,
                                    'prize_id' => $prize->ID,
                                    'status' => 'pending',
                                    'registrar' => get_current_user_id(),
                                    'updater' => get_current_user_id(),
                                    'creation_date' => current_time( 'mysql' ),
                                    'update_date' => current_time( 'mysql' )
                                ];
                                break;
                            }
                        }
                    }

                    $update_queries[] = sprintf(
                        "UPDATE `{$wpdb->prefix}plot_participants` SET `backup_lottery` = %d, `update_date` = '%s' WHERE `lottery` = %d AND `user_id` = %d AND `block` = '%s' AND `bonuse` = '%s'",
                        $bk_lottery_id,
                        current_time( 'mysql' ),
                        $lottery_id,
                        $participant->user_id,
                        implode( '-', $participant_blocks ),
                        implode( '-', $participant_bonuses )
                    );
                }

                include_once( PLotto_PATH . 'inc/helpers/plotto-wpdb-helper.php' );
                $wpdb_helper = new PLottoWpdbHelper();

                $update_result = $wpdb_helper->update_multiple( $update_queries );

                if( ! empty( $update_result ) )
                    wp_send_json_error( [ 'message' => __( 'Updating participant unsuccessfull!', 'plotto' ) ] );

                if( ! empty( $data ) )
                {
                    $insert_result = $wpdb_helper->insert_multiple( $winners_table, $data );
                    if( ! $insert_result )
                        wp_send_json_error( array( 'message' => __( 'Saving prizes data unsuccessfull!', 'plotto' ) ) );
                }

                wp_send_json_success( [ 'message' => __( 'Lottery successfully updated', 'plotto' ) ] );
            }
        }

        private function prepare_renew_modal()
        {
            $lottery_id = isset( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';

            if( empty( $lottery_id ) ) {
                wp_send_json_error( [ 'message' => __( 'Lottery id is required', 'plotto' ) ] );
            } else {

                $html = '<div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="expire-time">' . __( 'Expire time', 'plotto' ) . '</label>
                            <input type="datetime-local" class="form-control" id="expire-time" name="expire_time" min="' . wp_date( 'Y-m-d\TH:i' ) . '">
                        </div>
                    </div>
                </div>';
                wp_send_json_success( [ 'html' => $html ] );
            }
        }

        private function renew_lottery()
        {
            global $wpdb;
            $table = "{$wpdb->prefix}plot_lotteries";
            $lottery_id = isset( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';
            $expire_time = isset( $_POST['expireTime'] ) ? sanitize_text_field( wp_unslash( $_POST['expireTime'] ) ) : '';

            if( empty( $lottery_id ) )
                wp_send_json_error( [ 'message' => __( 'Lottery id is required', 'plotto' ) ] );

            if( $expire_time < wp_date( 'Y-m-d\TH:i:s' ) )
                wp_send_json_error( [ 'message' => __( 'The expiration date must be after the current date', 'plotto' ) ] );

            $lottery_results = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT `name`, `wc_product_id` FROM `{$wpdb->prefix}plot_lotteries` WHERE `ID` = %d",
                    $lottery_id
                )
            );

            if( empty( $lottery_results ) )
                wp_send_json_error( [ 'message' => __( 'Linked product not found!', 'plotto' ) ] );

            $post_id = $lottery_results[0]->wc_product_id;

            $result = $wpdb->update(
                $table, [
                    'expire_time' => $expire_time,
                    'status' => 'active',
                    'answer' => '',
                    'answer_date' => null,
                    'updater' => get_current_user_id(),
                    'update_date' => current_time( 'mysql' )
                ],
                [
                    'ID' => $lottery_id
                ]
            );

            if( ! $result )
                wp_send_json_error( [ 'message' => __( 'Updating lottery unsuccessfull!', 'plotto' ) ] );

            PLottoHelper::log( sprintf( __( 'Lottery successfully renewed. (ID: %d, name: "%s")', 'plotto' ) , $lottery_id, $lottery_results[0]->name ) );
            wp_update_post( [ 'ID' => $post_id, 'post_status' => 'publish' ] );
            update_post_meta( $post_id, '_lottery_expire_time', $expire_time );
            PLottoHelper::log( sprintf( __( 'Status of product ID %d changed to publish and expire date updated to "%s"', 'plotto' ) , $post_id, wp_date( 'Y-m-d H:i:s', strtotime( $expire_time ) ) ) );

            wp_send_json_success( [ 'message' => __( 'Lottery successfully updated', 'plotto' ) ] );
        }

        private function delete_lottery()
        {
            global $wpdb;
            $table = "{$wpdb->prefix}plot_lotteries";
            $lottery_id = isset( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';

            if( empty( $lottery_id ) ) {
                wp_send_json_error( [ 'message' => __( 'Lottery id is required', 'plotto' ) ] );
            } else {
                $lottery_results = $wpdb->get_results(
                    $wpdb->prepare(
                        "SELECT `name`, `wc_product_id` FROM `{$wpdb->prefix}plot_lotteries` WHERE `ID` = %d",
                        $lottery_id
                    )
                );

                $result = $wpdb->delete(
                    $table, [ 'ID' => $lottery_id ]
                );

                if( ! $result )
                    wp_send_json_error( [ 'message' => __( 'Deleting lottery unsuccessfull!', 'plotto' ) ] );

                PLottoHelper::log( sprintf( __( 'Lottery successfully deleted. (ID: %d, name: "%s")', 'plotto' ) , $lottery_id, $lottery_results[0]->name ) );

                $post_id = ! empty( $lottery_results ) ? $lottery_results[0]->wc_product_id : false;

                if( $post_id )
                    wp_delete_post( $post_id, true );

                PLottoHelper::log( sprintf( __( 'Lottery related product successfully deleted. (ID: %d)', 'plotto' ) , $post_id ) );

                wp_send_json_success( [ 'message' => __( 'Lottery successfully deleted', 'plotto' ) ] );
            }
        }

        private function delete_company()
        {
            global $wpdb;
            $table = "{$wpdb->prefix}plot_companies";
            $company_id = isset( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';

            if( empty( $company_id ) ) {
                wp_send_json_error( [ 'message' => __( 'Company id is required', 'plotto' ) ] );
            } else {
                $result = $wpdb->delete(
                    $table, [ 'ID' => $company_id ]
                );

                if( ! $result )
                    wp_send_json_error( [ 'message' => __( 'Deleting company unsuccessfull!', 'plotto' ) ] );

                PLottoHelper::log( sprintf( __( 'Company successfully deleted. (ID: %d)', 'plotto' ) , $company_id ) );
                wp_send_json_success( [ 'message' => __( 'Company successfully deleted', 'plotto' ) ] );
            }
        }

        private function get_participants()
        {
            global $wpdb;
            $table1 = "{$wpdb->prefix}plot_participants";
            $table2 = "{$wpdb->prefix}plot_winners";
            $table3 = "{$wpdb->prefix}users";
            $table4 = "{$wpdb->prefix}plot_prizes";
            $table5 = "{$wpdb->prefix}plot_lotteries";

            $primary_key = 'ID';

            include_once( PLotto_PATH . 'inc/helpers/plotto-ssp.php' );

            $columns = array(
                array( 'db' => '`t1`.`ID`', 'as' => 'participant_id', 'dt' => 'participant_id', 'field' => 'participant_id' ),
                array( 'db' => '`t3`.`ID`', 'as' => 'user_id', 'dt' => 'user_id', 'field' => 'user_id' ),
                array( 'db' => '`t3`.`user_login`', 'as' => 'username', 'dt' => 'username', 'field' => 'username' ),
                array( 'db' => '`t3`.`user_email`', 'as' => 'user_email', 'dt' => 'user_email', 'field' => 'user_email' ),
                array( 'db' => '`t1`.`lottery`', 'as' => 'lottery_id', 'dt' => 'lottery_id', 'field' => 'lottery_id' ),
                array( 'db' => '`t1`.`creation_date`', 'as' => 'buy_ticket_date', 'dt' => 'buy_ticket_date', 'field' => 'buy_ticket_date' ),
                array( 'db' => 'CONCAT(`t1`.`block`, \'/\', `t1`.`bonuse`)', 'as' => 'choosen_blocks_bonuses', 'dt' => 'choosen_blocks_bonuses', 'field' => 'choosen_blocks_bonuses' ),
                array( 'db' => '`t5`.`answer`', 'as' => 'lottery_answer', 'dt' => 'lottery_answer', 'field' => 'lottery_answer' ),
                array( 'db' => '`t5`.`answer_date`', 'as' => 'lottery_answer_date', 'dt' => 'lottery_answer_date', 'field' => 'lottery_answer_date' ),
                array( 'db' => '`t2`.`status`', 'as' => 'pay_to_user', 'dt' => 'pay_to_user', 'field' => 'pay_to_user',
                    'formatter' => function( $d, $row ) {
                        switch( $d )
                        {
                            case 'pending':
                                return '<span class="badge bg-warning show-winner-confirmation" data-winner_id="' . $row['winner_id'] . '" data-participant_id="' . $row['participant_id'] . '">' . __( 'Pending', 'plotto' ) . '</span>';
                            case 'approved':
                                return '<span class="badge bg-success">' . __( 'Approved', 'plotto' ) . '</span>';
                            case 'rejected':
                                return '<span class="badge bg-danger">' . __( 'Rejected', 'plotto' ) . '</span>';
                            case 'paid':
                                return '<span class="badge bg-info">' . __( 'Paid', 'plotto' ) . '</span>';
                            default:
                                return '-';
                        }
                    }
                ),
                array( 'db' => 'CASE WHEN `t2`.`ID` is not null THEN "yes" ELSE "no" END', 'as' => 'is_winner', 'dt' => 'is_winner', 'field' => 'is_winner',
                    'formatter' => function( $d, $row ) {
                        return $d === 'yes' ? '<i class="fa-solid fa-check text-success"></i>' : '<i class="fa-solid fa-xmark text-danger"></i>';
                    }
                ),
                array( 'db' => '`t4`.`amount`', 'as' => 'win_amount', 'dt' => 'win_amount', 'field' => 'win_amount',
                    'formatter' => function( $d, $row ) {
                        return ! empty( $d ) ? wc_price( $d ) : '-';
                    }
                ),
                array( 'db' => 'CONCAT(`t5`.`total_price`, \'||\', `t5`.`prize_currency`)', 'as' => 'total_amount', 'dt' => 'total_amount', 'field' => 'total_amount',
                    'formatter' => function( $d, $row ) {
                        $arr = explode( '||', $d );
                        return ! empty( $arr ) ? wc_price( $arr[0], array(
                            'currency' => $arr[1]
                        ) ) : '-';
                    }
                ),
                array( 'db' => '`t5`.`ticket_price`', 'as' => 'ticket_price', 'dt' => 'ticket_price', 'field' => 'ticket_price',
                    'formatter' => function( $d, $row ) {
                        return wc_price( $d );
                    }
                ),
                array( 'db' => '`t2`.`block_coordination`', 'as' => 'block_coordination', 'dt' => 'block_coordination', 'field' => 'block_coordination',
                    'formatter' => function( $d, $row ) {
                        return ! empty( $d ) ? $d : '-';
                    }
                ),
                array( 'db' => '`t2`.`bonuse_coordination`', 'as' => 'bonuse_coordination', 'dt' => 'bonuse_coordination', 'field' => 'bonuse_coordination',
                    'formatter' => function( $d, $row ) {
                        return ! empty( $d ) ? $d : '-';
                    }
                ),
                array( 'db' => '`t2`.`ID`', 'as' => 'winner_id', 'dt' => 'winner_id', 'field' => 'winner_id' )
            );

            // SQL server connection information
            $sql_details = array(
                'user' => DB_USER,
                'pass' => DB_PASSWORD,
                'db'   => DB_NAME,
                'host' => DB_HOST
            );

            $joinQuery = " FROM `$table1` AS `t1`
                LEFT JOIN `$table2` AS `t2` ON (`t2`.`participant` = `t1`.`ID`)
                LEFT JOIN `$table3` AS `t3` ON (`t3`.`ID` = `t1`.`user_id`)
                LEFT JOIN `$table4` AS `t4` ON (`t4`.`ID` = `t2`.`prize_id`)
                LEFT JOIN `$table5` AS `t5` ON (`t5`.`ID` = `t1`.`backup_lottery`)";

            die (
                wp_json_encode(
                    SSP::simple( $_POST, $sql_details, $table1, $primary_key, $columns, $joinQuery )
                )
            );
        }

        private function approve_winner()
        {
            global $wpdb;
            $participants_table = "{$wpdb->prefix}plot_participants";
            $winners_table = "{$wpdb->prefix}plot_winners";
            $winner_row_id = isset( $_POST['winner_id'] ) ? sanitize_text_field( wp_unslash( $_POST['winner_id'] ) ) : 0;
            $participant_row_id = isset( $_POST['participant_id'] ) ? sanitize_text_field( wp_unslash( $_POST['participant_id'] ) ) : 0;

            $result = $wpdb->update(
                $winners_table,
                [
                    'status' => 'approved',
                    'updater' => get_current_user_id(),
                    'update_date' => current_time( 'mysql' )
                ],
                [
                    'ID' => $winner_row_id
                ]
            );

            if( ! $result )
                wp_send_json_error( [ 'message' => __( 'Approving winner unsuccessfull!', 'plotto' ) ] );

            $result = $wpdb->update(
                $participants_table,
                [
                    'status' => 'win',
                    'update_date' => current_time( 'mysql' )
                ],
                [
                    'ID' => $participant_row_id
                ]
            );

            if( ! $result )
                wp_send_json_error( [ 'message' => __( 'Updating participant unsuccessfull!', 'plotto' ) ] );

            $winner = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT
                        `t1`.`lottery`,
                        `t1`.`user_id`,
                        `t2`.`display_name`,
                        `t4`.`amount`
                    FROM
                        `{$wpdb->prefix}plot_participants` AS `t1`
                    LEFT JOIN
                        `$wpdb->users` AS `t2`
                    ON
                        `t1`.`user_id` = `t2`.`ID`
                    LEFT JOIN
                        `{$wpdb->prefix}plot_winners` AS `t3`
                    ON
                        `t3`.`participant` = %d
                    LEFT JOIN `{$wpdb->prefix}plot_prizes` AS `t4`
                    ON
                        `t4`.`ID` = `t3`.`prize_id`
                    WHERE
                        `t1`.`ID` = %d",
                    $participant_row_id,
                    $participant_row_id
                )
            );

            if( class_exists( 'CaspianPayGateWay\CPayInit' ) )
            {
                $wallet_table = "{$wpdb->prefix}cpay_wallet";
                $wpdb->insert(
                    $wallet_table,
                    [
                        'user_id' => $winner[0]->user_id,
                        'transaction_type' => 'increase',
                        'amount' => $winner[0]->amount,
                        'transaction_id' => 'lottery_' . $winner[0]->lottery . 'win_id_' . $winner_row_id,
                        'gateway' => 'LOTTERY_WIN',
                        'status' => 'SUCCESS',
                        'registrar' => get_current_user_id(),
                        'creation_date' => current_time( 'mysql' )
                    ]
                );
            }

            wp_send_json_success( [ 'message' => __( 'Winner successfully approved', 'plotto' ) ] );
            PLottoHelper::log( sprintf( __( 'Winner successfully approved. (Winner: "%s", Updater: %d)', 'plotto' ) , $winner[0]->display_name, get_current_user_id() ) );
        }

        private function reject_winner()
        {
            global $wpdb;
            $participants_table = "{$wpdb->prefix}plot_participants";
            $winners_table = "{$wpdb->prefix}plot_winners";
            $winner_row_id = isset( $_POST['winner_id'] ) ? sanitize_text_field( wp_unslash( $_POST['winner_id'] ) ) : 0;
            $participant_row_id = isset( $_POST['participant_id'] ) ? sanitize_text_field( wp_unslash( $_POST['participant_id'] ) ) : 0;
            $note = isset( $_POST['note'] ) ? sanitize_text_field( wp_unslash( $_POST['note'] ) ) : '';

            $result = $wpdb->update(
                $winners_table,
                [
                    'status' => 'rejected',
                    'updater' => get_current_user_id(),
                    'update_date' => current_time( 'mysql' )
                ],
                [
                    'ID' => $winner_row_id
                ]
            );

            if( ! $result )
                wp_send_json_error( [ 'message' => __( 'Rejecting winner unsuccessfull!', 'plotto' ) ] );

            $result = $wpdb->update(
                $participants_table,
                [
                    'note' => $note,
                    'status' => 'rejected',
                    'update_date' => current_time( 'mysql' )
                ],
                [
                    'ID' => $participant_row_id
                ]
            );

            if( ! $result )
                wp_send_json_error( [ 'message' => __( 'Updating participant unsuccessfull!', 'plotto' ) ] );

            $winner = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT `display_name` FROM `{$wpdb->prefix}plot_participants` AS `t1` LEFT JOIN `$wpdb->users` AS `t2` ON `t1`.`user_id` = `t2`.`ID` WHERE `t1`.`ID` = %d",
                    $participant_row_id
                )
            );
            wp_send_json_success( [ 'message' => __( 'Winner successfully rejected', 'plotto' ) ] );
            PLottoHelper::log( sprintf( __( 'Winner successfully rejected. (Winner: "%s", Updater: %d)', 'plotto' ) , $winner, get_current_user_id() ) );
        }

        private function get_withdrawal_requests()
        {
            global $wpdb;
            $table1 = "{$wpdb->prefix}plot_withdrawals";
            $table2 = "{$wpdb->prefix}users";

            $primary_key = 'ID';

            include_once( PLotto_PATH . 'inc/helpers/plotto-ssp.php' );

            $columns = array(
                array( 'db' => '`t1`.`ID`', 'as' => 'request_id', 'dt' => 'request_id', 'field' => 'request_id' ),
                array( 'db' => '`t2`.`user_login`', 'as' => 'username', 'dt' => 'username', 'field' => 'username' ),
                array( 'db' => '`t1`.`creation_date`', 'as' => 'date', 'dt' => 'date', 'field' => 'date' ),
                array( 'db' => '`t1`.`amount`', 'as' => 'amount', 'dt' => 'amount', 'field' => 'amount' ),
                array( 'db' => '`t1`.`type`', 'as' => 'type', 'dt' => 'type', 'field' => 'type' ),
                array( 'db' => '`t1`.`wallet`', 'as' => 'account', 'dt' => 'account', 'field' => 'account' ),
                array( 'db' => '`t1`.`status`', 'as' => 'status', 'dt' => 'status', 'field' => 'status',
                    'formatter' => function( $d, $row ) {
                        switch( $d )
                        {
                            case 'pending':
                                return '<span class="badge bg-warning show-withdrawal-confirmation" data-request_id="' . $row['request_id'] . '">' . __( 'Pending', 'plotto' ) . '</span>';
                            case 'rejected':
                                return '<span class="badge bg-danger">' . __( 'Rejected', 'plotto' ) . '</span>';
                            case 'paid':
                                return '<span class="badge bg-success">' . __( 'Paid', 'plotto' ) . '</span>';
                            default:
                                return '-';
                        }
                    }
                )
            );

            // SQL server connection information
            $sql_details = array(
                'user' => DB_USER,
                'pass' => DB_PASSWORD,
                'db'   => DB_NAME,
                'host' => DB_HOST
            );

            $joinQuery = " FROM `$table1` AS `t1`
                LEFT JOIN `$table2` AS `t2` ON (`t2`.`ID` = `t1`.`user_id`)";

            die (
                wp_json_encode(
                    SSP::simple( $_POST, $sql_details, $table1, $primary_key, $columns, $joinQuery )
                )
            );
        }

        private function approve_withdrawal_request()
        {
            global $wpdb;
            $withdrawals_table = "{$wpdb->prefix}plot_withdrawals";
            $request_id = isset( $_POST['request_id'] ) ? sanitize_text_field( wp_unslash( $_POST['request_id'] ) ) : 0;

            $result = $wpdb->update(
                $withdrawals_table,
                [
                    'status' => 'paid',
                    'updater' => get_current_user_id(),
                    'update_date' => current_time( 'mysql' )
                ],
                [
                    'ID' => $request_id
                ]
            );

            if( ! $result )
                wp_send_json_error( [ 'message' => __( 'Approving withdrawal request unsuccessfull!', 'plotto' ) ] );

            wp_send_json_success( [ 'message' => __( 'Withdrawal request successfully approved', 'plotto' ) ] );
            PLottoHelper::log( sprintf( __( 'Withdrawal request successfully approved. (Request id: %d, Updater: %d)', 'plotto' ) , $request_id, get_current_user_id() ) );
        }

        private function reject_withdrawal_request()
        {
            global $wpdb;
            $withdrawals_table = "{$wpdb->prefix}plot_withdrawals";
            $request_id = isset( $_POST['request_id'] ) ? sanitize_text_field( wp_unslash( $_POST['request_id'] ) ) : 0;
            $note = isset( $_POST['note'] ) ? sanitize_text_field( wp_unslash( $_POST['note'] ) ) : '';

            $wallet_id = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT `wallet_id` FROM `$withdrawals_table` WHERE `ID` = %d",
                    $request_id
                )
            );

            $result = $wpdb->delete(
                "{$wpdb->prefix}cpay_wallet",
                [
                    'ID' => $wallet_id
                ]
            );

            if( ! $result )
                wp_send_json_error( [ 'message' => __( 'Removing balance from wallet unsuccessfull!', 'plotto' ) ] );

            $result = $wpdb->update(
                $withdrawals_table,
                [
                    'wallet_id' => 0,
                    'status' => 'rejected',
                    'note' => $note,
                    'updater' => get_current_user_id(),
                    'update_date' => current_time( 'mysql' )
                ],
                [
                    'ID' => $request_id
                ]
            );

            if( ! $result )
                wp_send_json_error( [ 'message' => __( 'Rejecting withdrawal request unsuccessfull!', 'plotto' ) ] );

            wp_send_json_success( [ 'message' => __( 'Withdrawal request successfully rejected', 'plotto' ) ] );
            PLottoHelper::log( sprintf( __( 'Withdrawal request successfully rejected. (Request id: %d, Updater: %d)', 'plotto' ) , $request_id, get_current_user_id() ) );
        }

        private function get_logs()
        {
            global $wpdb;
            $table = "{$wpdb->prefix}plot_logs";
            $primary_key = 'ID';

            include_once( PLotto_PATH . 'inc/helpers/plotto-ssp.php' );

            $columns = array(
                array( 'db' => 'ID', 'dt' => 0 ),
                array( 'db' => 'message',  'dt' => 1 ),
                array( 'db' => 'registrar', 'dt' => 2,
                    'formatter' => function( $d, $row ) {
                        $user = get_user_by( 'id', $d );
                        return $user->display_name;
                    }
                ),
                array( 'db' => 'creation_date', 'dt' => 3 )
            );

            // SQL server connection information
            $sql_details = array(
                'user' => DB_USER,
                'pass' => DB_PASSWORD,
                'db'   => DB_NAME,
                'host' => DB_HOST
            );

            die (
                wp_json_encode(
                    SSP::simple( $_POST, $sql_details, $table, $primary_key, $columns )
                )
            );
        }

        private function get_weekly_sales_amount()
        {
            global $wpdb;

            $sql = "SELECT
                -- Choose a few specific columns related to the order
                o.ID as order_id,
                o.post_date as order_created,
                oi.order_item_type as item_type,

                -- We have to subquery for specific values and alias them. This could also be done as a join
                (SELECT meta_value FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE order_item_id = oi.order_item_id AND meta_key = '_product_id' ANd oi.order_item_type = 'line_item') as pid,
                (SELECT meta_value FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE order_item_id = oi.order_item_id AND meta_key = '_line_total' AND oi.order_item_type = 'line_item') as total

            FROM {$wpdb->prefix}posts o
                LEFT JOIN {$wpdb->prefix}woocommerce_order_items oi ON oi.order_id = o.id
                LEFT JOIN {$wpdb->prefix}posts p ON p.ID = oi.order_item_id
            WHERE o.post_type = 'shop_order'";

            $results = $wpdb->get_results($sql);

            $d = strtotime( 'today' );
            $start_week = strtotime( 'last sunday midnight', $d );
            $end_week = strtotime( 'next saturday', $d );
            $start = gmdate( 'Y-m-d', $start_week );
            $end = gmdate( 'Y-m-d', $end_week );

            $total = $mon = $tue = $wed = $thu = $fri = $sat = $sun = 0;
            $data = [];

            if( ! empty( $results ) && is_array( $results ) )
            {
                foreach( $results as $result )
                {
                    if( gmdate( 'Y-m-d', strtotime( $result->order_created ) ) >= $start && gmdate( 'Y-m-d', strtotime( $result->order_created <= $end ) ) )
                    {
                        $total += $result->total;
                        switch( gmdate( 'D', strtotime( $result->order_created ) ) )
                        {
                            case 'Mon':
                                $mon += $result->total;
                                break;

                            case 'Tue':
                                $tue += $result->total;
                                break;

                            case 'Wed':
                                $wed += $result->total;
                                break;

                            case 'Thu':
                                $thu += $result->total;
                                break;

                            case 'Fri':
                                $fri += $result->total;
                                break;

                            case 'Sat':
                                $sat += $result->total;
                                break;

                            default:
                                $sun += $result->total;
                                break;
                        }
                    }
                }
                $data = [
                    'data' => [ $mon, $tue, $wed, $thu, $fri, $sat, $sun ],
                    'total' => wc_price( $total )
                ];
            }

            die (
                wp_json_encode(
                    $data
                )
            );
        }

        private function get_weekly_sales()
        {
            global $wpdb;

            $sql = "SELECT
                -- Choose a few specific columns related to the order
                o.ID as order_id,
                o.post_date as order_created,
                oi.order_item_type as item_type,

                -- We have to subquery for specific values and alias them. This could also be done as a join
                (SELECT meta_value FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE order_item_id = oi.order_item_id AND meta_key = '_product_id' ANd oi.order_item_type = 'line_item') as pid

            FROM {$wpdb->prefix}posts o
                LEFT JOIN {$wpdb->prefix}woocommerce_order_items oi ON oi.order_id = o.id
                LEFT JOIN {$wpdb->prefix}posts p ON p.ID = oi.order_item_id
            WHERE o.post_type = 'shop_order'";

            $results = $wpdb->get_results($sql);

            $d = strtotime( 'today' );
            $start_week = strtotime( 'last sunday midnight', $d );
            $end_week = strtotime( 'next saturday', $d );
            $start = gmdate( 'Y-m-d', $start_week );
            $end = gmdate( 'Y-m-d', $end_week );

            $total = $mon = $tue = $wed = $thu = $fri = $sat = $sun = 0;
            $data = [];

            if( ! empty( $results ) && is_array( $results ) )
            {
                foreach( $results as $result )
                {
                    if( gmdate( 'Y-m-d', strtotime( $result->order_created ) ) >= $start && gmdate( 'Y-m-d', strtotime( $result->order_created <= $end ) ) )
                    {
                        $total++;
                        switch( gmdate( 'D', strtotime( $result->order_created ) ) )
                        {
                            case 'Mon':
                                $mon++;
                                break;

                            case 'Tue':
                                $tue++;
                                break;

                            case 'Wed':
                                $wed++;
                                break;

                            case 'Thu':
                                $thu++;
                                break;

                            case 'Fri':
                                $fri++;
                                break;

                            case 'Sat':
                                $sat++;
                                break;

                            default:
                                $sun++;
                                break;
                        }
                    }
                }
                $data = [
                    'data' => [ $mon, $tue, $wed, $thu, $fri, $sat, $sun ],
                    'total' => $total
                ];
            }

            die (
                wp_json_encode(
                    $data
                )
            );
        }

        private function get_weekly_loosers()
        {
            global $wpdb;

            $sql = "SELECT
                update_date
            FROM {$wpdb->prefix}plot_participants
            WHERE status = 'lost'";

            $results = $wpdb->get_results($sql);

            $d = strtotime( 'today' );
            $start_week = strtotime( 'last sunday midnight', $d );
            $end_week = strtotime( 'next saturday', $d );
            $start = gmdate( 'Y-m-d', $start_week );
            $end = gmdate( 'Y-m-d', $end_week );

            $total = $mon = $tue = $wed = $thu = $fri = $sat = $sun = 0;
            $data = [];

            if( ! empty( $results ) && is_array( $results ) )
            {
                foreach( $results as $result )
                {
                    if( gmdate( 'Y-m-d', strtotime( $result->update_date ) ) >= $start && gmdate( 'Y-m-d', strtotime( $result->update_date <= $end ) ) )
                    {
                        $total++;
                        switch( gmdate( 'D', strtotime( $result->update_date ) ) )
                        {
                            case 'Mon':
                                $mon++;
                                break;

                            case 'Tue':
                                $tue++;
                                break;

                            case 'Wed':
                                $wed++;
                                break;

                            case 'Thu':
                                $thu++;
                                break;

                            case 'Fri':
                                $fri++;
                                break;

                            case 'Sat':
                                $sat++;
                                break;

                            default:
                                $sun++;
                                break;
                        }
                    }
                }
                $data = [
                    'data' => [ $mon, $tue, $wed, $thu, $fri, $sat, $sun ],
                    'total' => $total
                ];
            }

            die (
                wp_json_encode(
                    $data
                )
            );
        }

        private function get_weekly_winners()
        {
            global $wpdb;

            $sql = "SELECT
                update_date
            FROM {$wpdb->prefix}plot_participants
            WHERE status = 'win'";

            $results = $wpdb->get_results($sql);

            $d = strtotime( 'today' );
            $start_week = strtotime( 'last sunday midnight', $d );
            $end_week = strtotime( 'next saturday', $d );
            $start = gmdate( 'Y-m-d', $start_week );
            $end = gmdate( 'Y-m-d', $end_week );

            $total = $mon = $tue = $wed = $thu = $fri = $sat = $sun = 0;
            $data = [];

            if( ! empty( $results ) && is_array( $results ) )
            {
                foreach( $results as $result )
                {
                    if( gmdate( 'Y-m-d', strtotime( $result->update_date ) ) >= $start && gmdate( 'Y-m-d', strtotime( $result->update_date <= $end ) ) )
                    {
                        $total++;
                        switch( gmdate( 'D', strtotime( $result->update_date ) ) )
                        {
                            case 'Mon':
                                $mon++;
                                break;

                            case 'Tue':
                                $tue++;
                                break;

                            case 'Wed':
                                $wed++;
                                break;

                            case 'Thu':
                                $thu++;
                                break;

                            case 'Fri':
                                $fri++;
                                break;

                            case 'Sat':
                                $sat++;
                                break;

                            default:
                                $sun++;
                                break;
                        }
                    }
                }
                $data = [
                    'data' => [ $mon, $tue, $wed, $thu, $fri, $sat, $sun ],
                    'total' => $total
                ];
            }

            die (
                wp_json_encode(
                    $data
                )
            );
        }

        private function get_each_month_sales()
        {
            global $wpdb;
            $data = [];
            $jan = $feb = $mar = $apr = $may = $jun = $jul = $aug = $sep = $oct = $nov = $dec = 0;

            $sql = "SELECT
                -- Choose a few specific columns related to the order
                o.ID as order_id,
                DATE_FORMAT(o.post_date,'%b') AS month,
                oi.order_item_type as item_type,

                -- We have to subquery for specific values and alias them. This could also be done as a join
                (SELECT meta_value FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE order_item_id = oi.order_item_id AND meta_key = '_product_id' ANd oi.order_item_type = 'line_item') as pid

            FROM {$wpdb->prefix}posts o
                LEFT JOIN {$wpdb->prefix}woocommerce_order_items oi ON oi.order_id = o.id
                LEFT JOIN {$wpdb->prefix}posts p ON p.ID = oi.order_item_id
            WHERE o.post_type = 'shop_order'
                AND DATE_FORMAT(o.post_date,'%Y') = YEAR(curdate()) ORDER BY month DESC";

            $sales = $wpdb->get_results($sql);

            if( ! empty( $sales ) && is_array( $sales ) )
            {
                foreach( $sales as $sale )
                {
                    switch( $sale->month )
                    {
                        case 'Jan':
                            $jan++;
                            break;

                        case 'Feb':
                            $feb++;
                            break;

                        case 'Mar':
                            $mar++;
                            break;

                        case 'Apr':
                            $apr++;
                            break;

                        case 'May':
                            $may++;
                            break;

                        case 'Jun':
                            $jun++;
                            break;

                        case 'Jul':
                            $jul++;
                            break;

                        case 'Aug':
                            $aug++;
                            break;

                        case 'Sep':
                            $sep++;
                            break;

                        case 'Oct':
                            $oct++;
                            break;

                        case 'Nov':
                            $nov++;
                            break;

                        default:
                            $dec++;
                            break;
                    }
                }
            }

            $data = [
                $jan, $feb, $mar, $apr, $may, $jun, $jul, $aug, $sep, $oct, $nov, $dec
            ];

            die (
                wp_json_encode(
                    $data
                )
            );
        }

        private function get_today_reports()
        {
            global $wpdb;

            $total_sale = $total_users = $total_winners = 0;
            $data = [];
            $today = gmdate( 'Y-m-d' );

            $sql = "SELECT
                -- Choose a few specific columns related to the order
                o.ID as order_id,
                o.post_date as order_created,
                oi.order_item_type as item_type,

                -- We have to subquery for specific values and alias them. This could also be done as a join
                (SELECT meta_value FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE order_item_id = oi.order_item_id AND meta_key = '_product_id' ANd oi.order_item_type = 'line_item') as pid

            FROM {$wpdb->prefix}posts o
                LEFT JOIN {$wpdb->prefix}woocommerce_order_items oi ON oi.order_id = o.id
                LEFT JOIN {$wpdb->prefix}posts p ON p.ID = oi.order_item_id
            WHERE o.post_type = 'shop_order'";

            $sales = $wpdb->get_results($sql);

            if( ! empty( $sales ) && is_array( $sales ) )
            {
                foreach( $sales as $sale )
                {
                    if( gmdate( 'Y-m-d', strtotime( $sale->order_created ) ) === $today )
                    {
                        $total_sale++;
                    }
                }
            }

            $users_count = count_users();
            if( ! empty( $users_count ) )
            {
                foreach( $users_count['avail_roles'] as $role => $count )
                {
                    if( $role === 'customer' )
                    {
                        $total_users++;
                    }
                }
            }

            $sql = "SELECT
                update_date
            FROM {$wpdb->prefix}plot_participants
            WHERE status = 'win'";

            $winners = $wpdb->get_results($sql);

            if( ! empty( $winners ) )
            {
                foreach( $winners as $winner )
                {
                    if( gmdate( 'Y-m-d', strtotime( $winner->update_date ) ) === $today )
                    {
                        $total_winners++;
                    }
                }
            }

            $data = [
                'total_sale' => $total_sale,
                'total_users' => $total_users,
                'total_winners' => $total_winners
            ];

            die (
                wp_json_encode(
                    $data
                )
            );
        }
    }
}