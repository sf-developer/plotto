<?php

namespace PLotto\Inc\Ajax;

use PLotto\Inc\Helpers\SSP;

defined( 'ABSPATH' ) || exit; // Prevent direct access

if( ! class_exists( 'PLottoPublicAjaxHandler' ) )
{
    class PLottoPublicAjaxHandler
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
            add_action( 'wp_ajax_get_tickets', [ $this, 'ajax_handler' ] );
            add_action( 'wp_ajax_get_withdrawals', [ $this, 'ajax_handler' ] );
            add_action( 'wp_ajax_get_previouse_lotteries', [ $this, 'ajax_handler' ] );
            add_action( 'wp_ajax_nopriv_get_previouse_lotteries', [ $this, 'ajax_handler' ] );
            add_action( 'wp_ajax_request_new_withdrawal', [ $this, 'ajax_handler' ] );
        }

        public function ajax_handler()
        {

            if( ! defined( 'DOING_AJAX' ) && ! is_user_logged_in() )
            {
                wp_redirect( home_url() );
                exit();
            }

            // Handle ajax request and return response
            check_ajax_referer( 'plotto-ajax-nonce', 'security' );

            $action = isset( $_POST['action'] ) ? sanitize_text_field( wp_unslash( $_POST['action'] ) ) : '';
            switch( $action )
            {
                case 'get_tickets':
                    $this->get_tickets();
                    break;

                case 'get_withdrawals':
                    $this->get_withdrawals();
                    break;

                case 'request_new_withdrawal':
                    $this->request_new_withdrawal();
                    break;

                case 'get_previouse_lotteries':
                    $this->get_previouse_lotteries();
                    break;

                default:
                    wp_send_json_error( [ 'message' => __( 'Invalid action', 'plotto' ) ] );
                    break;
            }
        }

        private function get_tickets()
        {
            global $wpdb;
            $table1 = "{$wpdb->prefix}plot_participants";
            $table2 = "{$wpdb->prefix}plot_winners";
            $table3 = "{$wpdb->prefix}plot_prizes";
            $table4 = "{$wpdb->prefix}plot_lotteries";
            $primary_key = 'ID';
            $current_user_id = get_current_user_id();

            include_once( PLotto_PATH . 'inc/helpers/plotto-ssp.php' );

            $columns = array(
                array( 'db' => '`t1`.`ID`', 'as' => 'participant_id', 'dt' => 0, 'field' => 'participant_id' ),
                array( 'db' => '`t1`.`lottery`', 'as' => 'lottery', 'dt' => 1, 'field' => 'lottery',
                    'formatter' => function( $d, $row ) {
                        return $this->get_lottery_data( $d );
                    }
                ),
                array( 'db' => '`t1`.`ticket_price`', 'as' => 'ticket_price', 'dt' => 2, 'field' => 'ticket_price',
                    'formatter' => function( $d, $row ) {
                        return wc_price( $d );
                    }
                ),
                array( 'db' => '`t1`.`creation_date`', 'as' => 'creation_date', 'dt' => 3, 'field' => 'creation_date' ),
                array( 'db' => '`t1`.`block`', 'as' => 'block', 'dt' => 4, 'field' => 'block' ),
                array( 'db' => '`t1`.`bonuse`', 'as' => 'bonuse', 'dt' => 5, 'field' => 'bonuse' ),
                array( 'db' => '`t3`.`amount`', 'as' => 'amount', 'dt' => 6, 'field' => 'amount',
                    'formatter' => function( $d, $row ) {
                        return empty( $d ) ? '-' : wc_price( $d );
                    }
                ),
                array( 'db' => '`t4`.`answer`', 'as' => 'answer', 'dt' => 7, 'field' => 'answer' ),
                array( 'db' => '`t1`.`status`', 'as' => 'status', 'dt' => 8, 'field' => 'status',
                    'formatter' => function( $d, $row ) {
                        return $d === 'unpaid' ? __( 'Unpaid', 'plotto' ) : ( $d === 'undetermined' ? __( 'Pending', 'plotto' ) : ( $d === 'lost' ? __( 'Lost', 'plotto' ) : ( $d === 'win' ? __( 'Win', 'plotto' ) : __( 'Rejected', 'plotto' ) ) ) );
                    }
                ),
                array( 'db' => '`t1`.`note`', 'as' => 'note', 'dt' => 9, 'field' => 'note',
                    'formatter' => function( $d, $row ) {
                        return empty( $d ) ? '-' : $d;
                    }
                ),
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
                LEFT JOIN `$table3` AS `t3` ON (`t3`.`ID` = `t2`.`prize_id` AND `t2`.`status` = 'approved')
                LEFT JOIN `$table4` AS `t4` ON (`t4`.`ID` = `t1`.`backup_lottery`)";

            $where = "`t1`.`user_id` = {$current_user_id}";

            die (
                wp_json_encode(
                    SSP::simple( $_POST, $sql_details, $table1, $primary_key, $columns, $joinQuery, $where )
                )
            );
        }

        private function get_withdrawals()
        {
            global $wpdb;
            $table = "{$wpdb->prefix}plot_withdrawals";
            $primary_key = 'ID';
            $current_user_id = get_current_user_id();

            include_once( PLotto_PATH . 'inc/helpers/plotto-ssp.php' );

            $columns = array(
                array( 'db' => 'ID', 'dt' => 0 ),
                array( 'db' => 'creation_date', 'dt' => 1 ),
                array( 'db' => 'amount', 'dt' => 2 ),
                array( 'db' => 'wallet', 'dt' => 3 ),
                array( 'db' => 'status', 'dt' => 4 )
            );

            // SQL server connection information
            $sql_details = array(
                'user' => DB_USER,
                'pass' => DB_PASSWORD,
                'db'   => DB_NAME,
                'host' => DB_HOST
            );

            $where = "`user_id` = {$current_user_id}";

            die (
                wp_json_encode(
                    SSP::simple( $_POST, $sql_details, $table, $primary_key, $columns, null, $where )
                )
            );
        }

        private function request_new_withdrawal()
        {
            $payment_type = $_POST['type'];
            $amount = $_POST['amount'];
            $wallet = $_POST['account'];
            $iban = '';

            if( $payment_type === 'account' )
            {
                $account = explode( '||', $wallet );
                $wallet = $account[0];
                $iban = $account[1];
            }

            global $wpdb;

            if( class_exists( 'CaspianPayGateWay\CPayInit' ) )
            {
                $wallet_table = "{$wpdb->prefix}cpay_wallet";
                $wpdb->insert(
                    $wallet_table,
                    [
                        'user_id' => get_current_user_id(),
                        'transaction_type' => 'decrease',
                        'amount' => $amount,
                        'transaction_id' => 'lottery_withdrawal_' . wp_generate_uuid4(),
                        'gateway' => 'LOTTERY_WITHDRAWAL',
                        'status' => 'SUCCESS',
                        'registrar' => get_current_user_id(),
                        'creation_date' => current_time( 'mysql' )
                    ]
                );
            }

            $result = $wpdb->insert(
                $wpdb->prefix . 'plot_withdrawals',
                [
                    'user_id' => get_current_user_id(),
                    'amount' => $amount,
                    'type' => $payment_type,
                    'wallet' => $wallet,
                    'wallet_id' => $wpdb->insert_id,
                    'iban' => $iban,
                    'status' => 'pending',
                    'note' => '',
                    'registrar' => get_current_user_id(),
                    'updater' => get_current_user_id(),
                    'creation_date' => current_time( 'mysql' ),
                    'update_date' => current_time( 'mysql' )
                ]
            );

            if( $result )
            {
                $message = __( 'Request save successfully', 'plotto' );
            } else {
                $message = __( 'Unfortunately, your request cannot be saved in the database', 'plotto' );
            }

            die (
                wp_json_encode(
                    [ 'message' => $message ]
                )
            );
        }

        private function get_previouse_lotteries()
        {
            global $wpdb;
            $lottery_id = $_POST['id'];
            $choosen_date = $_POST['date'];
            $results = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT
                        `t1`.`name`,
                        `t1`.`content`,
                        `t1`.`total_price`,
                        `t1`.`prize_currency`,
                        `t1`.`ticket_price`,
                        `t1`.`update_date`,
                        `t1`.`choosen_block`,
                        `t1`.`choosen_bonuse`,
                        `t1`.`answer`,
                        `t2`.`name` AS `company`,
                        `t2`.`logo`,
                        JSON_ARRAYAGG(`t3`.`block_coordination`) as `prize_blocks`,
                        JSON_ARRAYAGG(`t3`.`bonuse_coordination`) as `prize_bonuses`,
                        JSON_ARRAYAGG(`t3`.`amount`) as `prize_amount`,
                        `t4`.`status`,
                        `t4`.`expire_time`
                    FROM
                        `{$wpdb->prefix}plot_lotteries` AS `t1`
                    LEFT JOIN
                        `{$wpdb->prefix}plot_companies` AS `t2`
                    ON
                        `t1`.`company` = `t2`.`ID`
                    LEFT JOIN
                        `{$wpdb->prefix}plot_prizes` AS `t3`
                    ON
                        `t1`.`lottery` = `t3`.`lottery`
                    LEFT JOIN
                        `{$wpdb->prefix}plot_lotteries` AS `t4`
                    ON
                        `t1`.`lottery` = `t4`.`ID`
                    WHERE
                        `t1`.`lottery` = %d
                    GROUP BY `t1`.`update_date`
                    ORDER BY `t1`.`update_date` ASC",
                    $lottery_id,
                )
            );

            $answers = $prize_blocks = $prize_bonuses = $prize_amount = $prizes = [];

            if( ! empty( $results ) )
            {
                foreach( $results as $result )
                {
                    if( gmdate( 'Y-m-d', strtotime( $result->update_date ) ) === $choosen_date )
                    {
                        $answers = explode( '/', $result->answer );
                        $prize_blocks = json_decode( $result->prize_blocks );
                        $prize_bonuses = json_decode( $result->prize_bonuses );
                        $prize_amount = json_decode( $result->prize_amount );
                    }
                }
            }

            $blocks = isset( $answers[0] ) ? explode( '-', $answers[0] ) : '';
            $bonuses = isset( $answers[1] ) ? explode( '-', $answers[1] ) : '';

            if( ! empty( $prize_blocks ) )
            {
                foreach( $prize_blocks as $index => $prize_block )
                {
                    $prizes[] = [
                        'coordination' => sprintf(
                            __( '%d blocks coordination and %d bonuses coordination', 'plotto' ),
                            $prize_block,
                            $prize_bonuses[$index]
                        ),
                        'amount' => $prize_amount[$index]
                    ];
                }
            }

            $dropdown = '';
            foreach( $results as $key => $result ):
                $dropdown .= '<li class="dropdown-list-item">
                    <button class="dropdown-button list-button plot-load-previouse-lotteries" data-lot-id="' . $lottery_id . '">
                        <span class="text-truncate">' . gmdate( 'Y-m-d', strtotime( $result->update_date ) ) . '</span>
                    </button>
                </li>';
            endforeach;

            $blocks_html = '';
            if( ! empty( $blocks ) ):
                foreach( $blocks as $block ):
                    $blocks_html .= '<div class="plot-ball-wrap">
                        <div class="plot-ball-balls">
                            <div class="plot-ball-sm"></div>
                            <ul class="plot-ball-digital plot-ball-ani">
                                <li><span>' . $block . '</span></li>
                            </ul>
                            <div class="plot-ball-dark"></div>
                            <div class="plot-ball-light"></div>
                        </div>
                    </div>';
                endforeach;
            endif;

            $bonuses_html = '';
            if( ! empty( $bonuses ) ): __( 'Win bonuses', 'plotto' );
                foreach( $bonuses as $bonuse ):
                    $bonuses_html .= '<div class="plot-ball-wrap">
                        <div class="plot-ball-balls">
                            <div class="plot-ball-sm"></div>
                            <ul class="plot-ball-digital plot-ball-ani">
                                <li><span>' . $bonuse . '</span></li>
                            </ul>
                            <div class="plot-ball-dark"></div>
                            <div class="plot-ball-light"></div>
                        </div>
                    </div>';
                endforeach;
            endif;

            $prizes_html = '';
            if( ! empty( $prizes ) ):
                $prizes_html .= '<table class="table table-striped">
                    <tbody>';
                        foreach( $prizes as $prize ):
                            $prizes_html .= '<tr style="text-align:center;font-size:14px;">
                                <td style="font-size:14px">' . wc_price( $prize['amount'] ) . '</td>
                                <td style="font-size:14px">' . $prize['coordination'] . '</td>
                            </tr>';
                        endforeach;
                    $prizes_html .= '</tbody>
                </table>';
            endif;

            $timer_html = '';
            if( $results[0]->status === 'active' ):
                $timer_html .= '<p id="demo" style="text-align:center;font-size: 16px">';
                    $timer_html .= __( 'Remaining time of the lottery until expiration', 'plotto' );
                    $timer_html .= '<div id="plot-flipdown" class="flipdown" style="margin: 0 auto;" data-time="' . $results[0]->expire_time . '"></div>';
                $timer_html .= '</p><hr>';
            endif;

            $html = '<div class="row" style="margin-top:10px">';
            if( ! empty( $results ) ):
                $html .= '<div class="col-sm-12 col-md-6 col-xs-12 col-lg-6 ">
                    <div class="card">
                        <div class="card-body">
                            <div style="text-weight:normal;font-size:16px;margin-bottom:10px">' . __( 'Date', 'plotto' ) . '</div>
                            <div class="dropdown-container">
                                <button class="dropdown-button main-button">
                                    <span class="dropdown-title text-truncate">' . $choosen_date . '</span>
                                    <span class="dropdown-arrow">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
                                            <path d="M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z" />
                                        </svg>
                                    </span>
                                </button>
                                <div class="dropdown-list-container">
                                    <div class="dropdown-list-wrapper">
                                        <ul class="dropdown-list">
                                        ' . $dropdown . '
                                        </ul>
                                        <div class="floating-icon" aria-hidden="true"></div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            ' . __( 'Win blocks', 'plotto' ) . '
                            <ul style="text-align: center;padding: 5px 0;clear: both;">
                            ' . $blocks_html . '
                            </ul>
                            <ul style="text-align: center;padding: 5px 0;clear: both;">
                            ' . $bonuses_html . '
                            </ul>
                            <hr>
                            ' . $prizes_html . '
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6 col-xs-12 col-lg-6 ">
                    <div class="card">
                        <div class="card-body">
                            <div>
                                <span><img src="' . wp_get_attachment_url( $results[0]->logo ) . '" style="width:100px;"></span>
                                <br>
                                <span id="lname" name="' . $results[0]->company . '" style="font-size:20px;font-weight:bold;">' . ucfirst( $results[0]->company ) . '</span>
                            </div>
                            <div style="text-align:justify;padding:10px;">
                                ' . $results[0]->content . '
                            </div>
                            <hr>
                            <ul style="padding:0px 50px 0px 50px">
                                <li style="margin-bottom:10px">
                                    ' . sprintf( __( 'Ticket price: %s', 'plotto' ), wc_price( $results[0]->ticket_price ) ) . '
                                </li>
                                <li>
                                    ' . sprintf( __( 'Number of blocks: %d number', 'plotto' ), $results[0]->choosen_block ) . '
                                </li>
                                <li>
                                    ' . sprintf( __( 'Number of bonuses: %d bonuse', 'plotto' ), $results[0]->choosen_bonuse ) . '
                                </li>
                            </ul>
                            <hr>
                            <div style="text-align:center;">' . __( 'Grand prize', 'plotto' ) . '</div>
                            <h2 style="font-family:tahoma;text-align:center;">' . wc_price( $results[0]->total_price, [ 'currency' => $results[0]->prize_currency ] ) . '</h2>
                            <hr>
                            ' . $timer_html . '
                            <div style="text-align:center">
                                <a id="sellbtn" href="../client/buyticket/35" class="color-btn green buy" style="font-family:tahoma;font-size:16px">همین الان در این لاتاری بلیط داشته باشید</a>
                            </div>
                        </div>
                    </div>
                </div>';
            endif;
            $html .= '</div>';
            die (
                wp_json_encode( [ 'html' => $html ] )
            );
        }

        private function get_lottery_data( $lottery_id )
        {
            global $wpdb;
            return $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT `name` FROM `{$wpdb->prefix}plot_lotteries` WHERE `ID` = %d",
                    $lottery_id
                )
            );
        }
    }
}