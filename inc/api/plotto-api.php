<?php

namespace PLotto\Inc\Api;

defined('ABSPATH') || exit; // Prevent direct access

use PLotto\Inc\Helpers\PLottoHelper;
use PLotto\Inc\Helpers\PLottoWpdbHelper;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use WP_Query;

if (!class_exists('PLottoApi')) {
    class PLottoApi
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
            add_action( 'rest_api_init', [ $this, 'routes' ] );
        }

        public function routes()
        {
            register_rest_route('plotto/v1', '/lotteries', array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => [ $this, 'get_lotteries' ],
                'args' => [
                    'limit' => [
                        'validate_callback' => function($param, $request, $key)
                        {
                            return is_numeric( $param );
                        }
                    ],
                    'offset' => [
                        'validate_callback' => function($param, $request, $key)
                        {
                            return is_numeric( $param );
                        }
                    ],
                    'orderby' => [
                        'validate_callback' => function($param, $request, $key)
                        {
                            return ! is_numeric( $param ) ? sanitize_text_field( $param ) : false;
                        }
                    ],
                    'order' => [
                        'validate_callback' => function($param, $request, $key)
                        {
                            return $param === 'asc' || $param === 'desc';
                        }
                    ]
                ],
                'permission_callback' => function ( WP_REST_Request $request )
                {
                    return true;
                }
            ));

            register_rest_route('plotto/v1', '/lottery/(?P<id>\d+)', array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => [ $this, 'get_lottery' ],
                'permission_callback' => function ( WP_REST_Request $request )
                {
                    return true;
                }
            ));

            register_rest_route('plotto/v1', '/check-user-login', array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => [ $this, 'check_user_login' ],
                'permission_callback' => function ( WP_REST_Request $request )
                {
                    return true;
                }
            ));

            register_rest_route('plotto/v1', '/get-currency', array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => [ $this, 'get_currency' ],
                'permission_callback' => function ( WP_REST_Request $request )
                {
                    return true;
                }
            ));

            register_rest_route('plotto/v1', '/add-participant', array(
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => [ $this, 'add_participant' ],
                'args' => [
                    'lottery' => [
                        'validate_callback' => function($param, $request, $key)
                        {
                            return is_numeric( $param );
                        }
                    ],
                    'blocks' => [
                        'validate_callback' => function($param, $request, $key)
                        {
                            return is_array( $param );
                        }
                    ],
                    'bonuses' => [
                        'validate_callback' => function($param, $request, $key)
                        {
                            return is_array( $param );
                        }
                    ]
                ],
                'permission_callback' => function ( WP_REST_Request $request )
                {
                    return true;
                }
            ));

            register_rest_route('plotto/v1', '/previous-lotteries', array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => [ $this, 'get_previous_lotteries' ],
                'args' => [
                    'lottery' => [
                        'validate_callback' => function($param, $request, $key)
                        {
                            return is_numeric( $param );
                        }
                    ]
                ],
                'permission_callback' => function ( WP_REST_Request $request )
                {
                    return true;
                }
            ));
        }

        public function get_lotteries( WP_REST_Request $request )
        {
            // if( null === $request->get_header( 'x_wp_nonce' ) || ! wp_verify_nonce( $request->get_header( 'x_wp_nonce' ), 'wp_rest' ) )
            // {
            //     return new WP_REST_Response( [
            //         'status' => 401,
            //         'message' => __( 'Unauthorized!', 'plotto' )
            //      ], 401 );
            // }

            $limit = isset( $_GET['limit'] ) ? $_GET['limit'] : 10;
            $offset = isset( $_GET['offset'] ) ? $_GET['offset'] : 0;
            $orderby = isset( $_GET['orderby'] ) ? $_GET['orderby'] : 'ID';
            $order = isset( $_GET['order'] ) ? $_GET['order'] : 'asc';

            global $wpdb;
            $lotteries = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM `{$wpdb->prefix}plot_lotteries` WHERE `status` = %s AND DATE(`expire_time`) > date(NOW())"
                    . " ORDER BY %s %s"
                    . " LIMIT %d OFFSET %d",
                    'active',
                    $orderby,
                    $order,
                    $limit,
                    $offset
                )
            );

            if( empty( $lotteries ) )
            {
                return new WP_REST_Response( [
                    'status' => 404,
                    'message' => __( 'No lotteries found', 'plotto' )
                ], 404 );
            }

            $lotteries_data = [];
            foreach( $lotteries as $lottery )
            {
                $company = $wpdb->get_row(
                    $wpdb->prepare(
                        "SELECT * FROM `{$wpdb->prefix}plot_companies` WHERE `ID` = %d",
                        $lottery->company
                    )
                );

                if( empty( $company ) )
                {
                    continue;
                }

                $real_participant = $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT COUNT(DISTINCT `user_id`) FROM `{$wpdb->prefix}plot_participants` WHERE `lottery` = %d AND `status` = %s",
                        $lottery->ID, 'undetermined'
                    )
                );

                $company_logo = wp_get_attachment_url( $company->logo );

                $user_id = get_current_user_id();

                if( empty( $user_id ) )
                {
                    $wallet_balance = 0;
                } else {
                    $wallet_balance = PLottoHelper::get_cpay_wallet_balance( $user_id );
                }

                $lotteries_data[] = [
                    'lottery' => [
                        'id' => $lottery->ID,
                        'name' => $lottery->name,
                        'description' => $lottery->content,
                        'total_prize' => $lottery->total_price,
                        'ticket_price' => $lottery->ticket_price,
                        'expire_time' => strtotime( $lottery->expire_time ) * 1000,
                        'fake_participant' => $lottery->fake_participant,
                        'real_participant' => $real_participant,
                        'bg' => $lottery->color,
                        'currency' => $lottery->prize_currency,
                        'has_wallet' => class_exists( 'CaspianPayGateWay\CPayInit' ),
                        'wallet_balance' => $wallet_balance,
                        'company' => [
                            'id' => $lottery->company,
                            'name' => $company->name,
                            'description' => $company->description,
                            'logo' => $company_logo
                        ],
                        'blocks' => [
                            'count' => $lottery->block_count,
                            'choosen' => $lottery->choosen_block
                        ],
                        'bonuses' => [
                            'count' => $lottery->bonuse_count,
                            'choosen' => $lottery->choosen_bonuse
                        ],
                        'status' => $lottery->status,
                        'answer' => [
                            'answer' => $lottery->answer,
                            'date' => $lottery->answer_date
                        ],
                        'creation_date' => $lottery->creation_date,
                        'update_date' => $lottery->update_date
                    ]
                ];

                unset( $company );
                unset( $company_logo );
                unset( $lottery );
            }

            return new WP_REST_Response( [
                'status' => 200,
                'data' => [
                    'lotteries' => $lotteries_data
                ]
            ], 200 );
        }

        public function get_lottery( WP_REST_Request $request )
        {

            // if( null === $request->get_header( 'x_wp_nonce' ) || ! wp_verify_nonce( $request->get_header( 'x_wp_nonce' ), 'wp_rest' ) )
            // {
            //     return new WP_REST_Response( [
            //         'status' => 401,
            //         'message' => __( 'Unauthorized!', 'plotto' )
            //      ], 401 );
            // }

            $lottery_id = $request['id'];

            global $wpdb;
            $lottery = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT * FROM `{$wpdb->prefix}plot_lotteries` WHERE `ID` = %d",
                    $lottery_id
                )
            );

            $real_participant = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(DISTINCT `user_id`) FROM `{$wpdb->prefix}plot_participants` WHERE `lottery` = %d AND `status` = %s",
                    $lottery_id, 'undetermined'
                )
            );

            if( empty( $lottery ) )
            {
                return new WP_REST_Response( [
                    'status' => 404,
                    'message' => __( 'Lottery not found', 'plotto' )
                ], 404 );
            }

            $company = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT * FROM `{$wpdb->prefix}plot_companies` WHERE `ID` = %d",
                    $lottery->company
                )
            );

            if( empty( $company ) )
            {
                return new WP_REST_Response( [
                    'status' => 404,
                    'message' => __( 'Company not found', 'plotto' )
                ], 404 );
            }

            $company_logo = wp_get_attachment_url( $company->logo );

            $user_id = get_current_user_id();

            if( empty( $user_id ) )
            {
                $wallet_balance = 0;
            } else {
                $wallet_balance = PLottoHelper::get_cpay_wallet_balance( $user_id );
            }

            return new WP_REST_Response( [
                'status' => 200,
                'data' => [
                    'lottery' => [
                        'id' => $lottery->ID,
                        'name' => $lottery->name,
                        'description' => $lottery->content,
                        'total_prize' => $lottery->total_price,
                        'ticket_price' => $lottery->ticket_price,
                        'expire_time' => strtotime( $lottery->expire_time ) * 1000,
                        'fake_participant' => $lottery->fake_participant,
                        'real_participant' => $real_participant,
                        'bg' => $lottery->color,
                        'currency' => $lottery->prize_currency,
                        'has_wallet' => class_exists( 'CaspianPayGateWay\CPayInit' ),
                        'wallet_balance' => $wallet_balance,
                        'company' => [
                            'id' => $lottery->company,
                            'name' => $company->name,
                            'description' => $company->description,
                            'logo' => $company_logo
                        ],
                        'blocks' => [
                            'count' => $lottery->block_count,
                            'choosen' => $lottery->choosen_block
                        ],
                        'bonuses' => [
                            'count' => $lottery->bonuse_count,
                            'choosen' => $lottery->choosen_bonuse
                        ],
                        'status' => $lottery->status,
                        'answer' => [
                            'answer' => $lottery->answer,
                            'date' => $lottery->answer_date
                        ]
                    ],
                ]
            ], 200 );
        }

        public function check_user_login( WP_REST_Request $request )
        {
            // if( null === $request->get_header( 'x_wp_nonce' ) || ! wp_verify_nonce( $request->get_header( 'x_wp_nonce' ), 'wp_rest' ) )
            // {
            //     return new WP_REST_Response( [
            //         'status' => 401,
            //         'message' => __( 'Unauthorized!', 'plotto' )
            //      ], 401 );
            // }

            $user_id = get_current_user_id();

            if( empty( $user_id ) )
            {
                return new WP_REST_Response( [
                    'status' => 403,
                    'message' => __( 'User not logged in', 'plotto' ),
                    'data' => [
                        'login_page' => get_permalink( get_option('woocommerce_myaccount_page_id') )
                    ]
                 ], 403 );
            }

            return new WP_REST_Response( [
                'status' => 200,
                'message' => __( 'User is logged in', 'plotto' ),
                'data' => [
                    'user_id' => $user_id
                ]
            ], 200 );
        }

        public function get_currency( WP_REST_Request $request )
        {
            // if( null === $request->get_header( 'x_wp_nonce' ) || ! wp_verify_nonce( $request->get_header( 'x_wp_nonce' ), 'wp_rest' ) )
            // {
            //     return new WP_REST_Response( [
            //         'status' => 401,
            //         'message' => __( 'Unauthorized!', 'plotto' )
            //      ], 401 );
            // }

            if( ! class_exists( 'WooCommerce' ) )
            {
                return new WP_REST_Response( [
                    'status' => 403,
                    'message' => __( 'Woocommerce plugin is not active', 'plotto' ),
                    'data' => [
                        'symbol' => '$',
                        'name' => __( 'dollar', 'plotto' )
                    ]
                 ], 403 );
            }

            return new WP_REST_Response( [
                'status' => 200,
                'message' => __( 'Default woocommerce currency', 'plotto' ),
                'data' => [
                    'symbol' => html_entity_decode( get_woocommerce_currency_symbol() ),
                    'name' => get_woocommerce_currency()
                ]
            ], 200 );
        }

        public function get_previous_lotteries( WP_REST_Request $request )
        {
            // if( null === $request->get_header( 'x_wp_nonce' ) || ! wp_verify_nonce( $request->get_header( 'x_wp_nonce' ), 'wp_rest' ) )
            // {
            //     return new WP_REST_Response( [
            //         'status' => 401,
            //         'message' => __( 'Unauthorized!', 'plotto' )
            //      ], 401 );
            // }

            $lottery_id = $request['lottery'];

            if( ! isset( $lottery_id ) )
            {
                return new WP_REST_Response( [
                    'status' => 400,
                    'message' => __( 'Lottery id is not set', 'plotto' )
                 ], 400 );
            }

            $pid = 0;
            $shortcode = ' id="' . $lottery_id . '"';
            $pattern = get_shortcode_regex( [ 'previous-lotteries' ] );

            $args = array(
                'post_type'=> 'page',
                'orderby'    => 'ID',
                'post_status' => 'publish',
                'order'    => 'DESC',
                'posts_per_page' => -1
            );
            $result = new WP_Query( $args );

            if( ! empty( $result->posts ) )
            {
                foreach( $result->posts as $post )
                {
                    if ( preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches )
                        && array_key_exists( 2, $matches )
                        && in_array( $shortcode, $matches[3] ) )
                    {
                        $pid = $post->ID;
                    }
                }
            }

            return new WP_REST_Response( [
                'status' => 200,
                'message' => __( 'Page id successfully retrieved.', 'plotto' ),
                'data' => [
                    'link' => !empty( $pid ) ? get_permalink( $pid ) : '#'
                ]
            ], 200 );
        }

        public function add_participant( WP_REST_Request $request )
        {
            // if( null === $request->get_header( 'x_wp_nonce' ) || ! wp_verify_nonce( $request->get_header( 'x_wp_nonce' ), 'wp_rest' ) )
            // {
            //     return new WP_REST_Response( [
            //         'status' => 401,
            //         'message' => __( 'Unauthorized!', 'plotto' )
            //      ], 401 );
            // }

            $user_id = get_current_user_id();

            if( empty( $user_id ) )
            {
                return new WP_REST_Response( [
                    'status' => 403,
                    'message' => __( 'User not logged in', 'plotto' ),
                    'data' => [
                        'login_page' => get_permalink( get_option('woocommerce_myaccount_page_id') )
                    ]
                 ], 403 );
            }

            $lottery_id = $request['lottery'];

            global $wpdb;

            $lottery = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT * FROM `{$wpdb->prefix}plot_lotteries` WHERE `ID` = %d",
                    $lottery_id
                )
            );

            if( empty( $lottery ) )
            {
                return new WP_REST_Response( [
                    'status' => 404,
                    'message' => __( 'Lottery not found', 'plotto' )
                ], 404 );
            }

            $lottery_status = $lottery->status;

            if( $lottery_status !== 'active' )
            {
                // Status 104 for not active lottery
                return new WP_REST_Response( [
                    'status' => 104,
                    'message' => __( 'Lottery is not active', 'plotto' )
                ], 104 );
            }

            $lottery_expire_time = $lottery->expire_time;

            $lottery_expire_time = strtotime( $lottery_expire_time );

            if( time() > $lottery_expire_time )
            {
                // Status 105 for expired lottery
                return new WP_REST_Response( [
                    'status' => 105,
                    'message' => __( 'Lottery is expired', 'plotto' )
                ], 105 );
            }

            $lottery_blocks = $request['blocks'];
            $lottery_bonuses = $request['bonuses'];
            $quantity = count( $lottery_blocks );

            if( empty( $lottery_blocks ) )
            {
                // Status 106 for empty lottery blocks
                return new WP_REST_Response( [
                    'status' => 106,
                    'message' => __( 'Lottery blocks is empty', 'plotto' )
                ], 106 );
            }

            foreach( $lottery_blocks as $block )
            {
                if( count( $block ) !== intval( $lottery->choosen_block ) )
                {
                    // Status 107 for not equal lottery blocks count and choosen blocks count
                    return new WP_REST_Response( [
                        'status' => 107,
                        'message' => __( 'Lottery blocks count is not equal to choosen blocks count', 'plotto' )
                    ], 107 );
                }
            }

            $table = "{$wpdb->prefix}plot_participants";
            $data = [];

            foreach( $lottery_blocks as $index => $block )
            {
                $data[] = [
                    'lottery' => $lottery_id,
                    'backup_lottery' => $lottery_id,
                    'user_id' => $user_id,
                    'block' => implode( '-', $block ),
                    'bonuse' => ! empty( $lottery_bonuses ) ? implode( '-', $lottery_bonuses[$index] ) : null,
                    'ticket_price' => $lottery->ticket_price,
                    'note' => '',
                    'status' => 'unpaid',
                    'creation_date' => current_time( 'mysql' ),
                    'update_date' => current_time( 'mysql' )
                ];
            }

            include_once( PLotto_PATH . 'inc/helpers/plotto-wpdb-helper.php' );
            $wpdb_helper = new PLottoWpdbHelper();
            $result = $wpdb_helper->insert_multiple( $table, $data );

            if( ! $result )
            {
                // Status 500 for adding participant unsuccessfull
                return new WP_REST_Response( [
                    'status' => 500,
                    'message' => __( 'Adding participant unsuccessfull!', 'plotto' )
                ], 500 );
            }

            $product_id = intval( $lottery->wc_product_id );

            return new WP_REST_Response( [
                'status' => 200,
                'message' => __( 'Participant successfully added', 'plotto' ),
                'data' => [
                    'url' => add_query_arg( [ 'add-to-cart' => $product_id, 'quantity' => $quantity ], wc_get_checkout_url() ),
                    'lottery_id' => $lottery_id,
                    'participant_id' => $wpdb->insert_id
                ]
            ], 200 );
        }
    }
}
