<?php

defined('ABSPATH') || exit; // Prevent direct access

global $wpdb;
$lottery_id = $atts['id'];
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
            `t1`.`status`,
            `t1`.`expire_time`
        FROM
            `{$wpdb->prefix}plot_lotteries` AS `t1`
        LEFT JOIN
            `{$wpdb->prefix}plot_companies` AS `t2`
        ON
            `t1`.`company` = `t2`.`ID`
        LEFT JOIN
            `{$wpdb->prefix}plot_prizes` AS `t3`
        ON
            `t1`.`ID` = `t3`.`lottery`
        WHERE
            `t1`.`lottery` = %d AND `t1`.`is_backup` = 1
        GROUP BY `t1`.`update_date`
        ORDER BY `t1`.`update_date` ASC",
        $lottery_id
    )
);

$answers = ! empty( $results && isset( $results[0]->answer ) ) ? explode( '/', $results[0]->answer ) : '';
$blocks = isset( $answers[0] ) ? explode( '-', $answers[0] ) : '';
$bonuses = isset( $answers[1] ) ? explode( '-', $answers[1] ) : '';
$prize_blocks = ! empty( $results && isset( $results[0]->prize_blocks ) ) ? json_decode( $results[0]->prize_blocks ) : [];
$prize_bonuses = ! empty( $results && isset( $results[0]->prize_bonuses ) ) ? json_decode( $results[0]->prize_bonuses ) : [];
$prize_amount = ! empty( $results && isset( $results[0]->prize_amount ) ) ? json_decode( $results[0]->prize_amount ) : [];
$prizes = [];

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
?>

<div id="plot-previous-lotteries" class="container">
    <div class="row" style="margin-top:10px">
        <?php if( ! empty( $results ) ): ?>
            <div class="col-sm-12 col-md-6 col-xs-12 col-lg-6 ">
                <div class="card">
                    <div class="card-body">
                        <div style="text-weight:normal;font-size:16px;margin-bottom:10px"><?php _e( 'Date', 'plotto' ) ?></div>
                        <div class="dropdown-container">
                            <button class="dropdown-button main-button">
                                <span class="dropdown-title text-truncate"><?php echo gmdate( 'Y-m-d', strtotime( $results[0]->update_date ) ); ?></span>
                                <span class="dropdown-arrow">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
                                        <path d="M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z" />
                                    </svg>
                                </span>
                            </button>
                            <div class="dropdown-list-container">
                                <div class="dropdown-list-wrapper">
                                    <ul class="dropdown-list">
                                    <?php foreach( $results as $key => $result ): ?>
                                        <li class="dropdown-list-item">
                                            <button class="dropdown-button list-button plot-load-previouse-lotteries" data-lot-id="<?php echo $lottery_id; ?>">
                                                <span class="text-truncate"><?php echo gmdate( 'Y-m-d', strtotime( $result->update_date ) ); ?></span>
                                            </button>
                                        </li>
                                    <?php endforeach; ?>
                                    </ul>
                                    <div class="floating-icon" aria-hidden="true"></div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <?php _e( 'Win blocks', 'plotto' ); ?>
                        <ul style="text-align: center;padding: 5px 0;clear: both;">
                            <?php
                            if( ! empty( $blocks ) ):
                                foreach( $blocks as $block ):
                            ?>
                                <div class="plot-ball-wrap">
                                    <div class="plot-ball-balls">
                                        <div class="plot-ball-sm"></div>
                                        <ul class="plot-ball-digital plot-ball-ani">
                                            <li><span><?php echo $block; ?></span></li>
                                        </ul>
                                        <div class="plot-ball-dark"></div>
                                        <div class="plot-ball-light"></div>
                                    </div>
                                </div>
                            <?php
                                endforeach;
                            endif;
                            ?>
                        </ul>
                        <?php
                        if( ! empty( $bonuses ) ): _e( 'Win bonuses', 'plotto' ); ?>
                            <ul style="text-align: center;padding: 5px 0;clear: both;">
                                <?php foreach( $bonuses as $bonuse ): ?>
                                    <div class="plot-ball-wrap">
                                        <div class="plot-ball-balls">
                                            <div class="plot-ball-sm"></div>
                                            <ul class="plot-ball-digital plot-ball-ani">
                                                <li><span><?php echo $bonuse; ?></span></li>
                                            </ul>
                                            <div class="plot-ball-dark"></div>
                                            <div class="plot-ball-light"></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                        <hr>
                        <?php if( ! empty( $prizes ) ): ?>
                            <table class="table table-striped">
                                <tbody>
                                    <?php
                                    foreach( $prizes as $prize ):
                                    ?>
                                        <tr style="text-align:center;font-size:14px;">
                                            <td style="font-size:14px"><?php echo wc_price( $prize['amount'] ); ?></td>
                                            <td style="font-size:14px"><?php echo $prize['coordination']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 col-xs-12 col-lg-6 ">
                <div class="card">
                    <div class="card-body">
                        <div>
                            <span><img src="<?php echo wp_get_attachment_url( $results[0]->logo ); ?>" style="width:100px;"></span>
                            <br>
                            <span id="lname" name="<?php echo $results[0]->company ?>" style="font-size:20px;font-weight:bold;"><?php echo ucfirst( $results[0]->company ); ?></span>
                        </div>
                        <div style="text-align:justify;padding:10px;">
                            <?php echo $results[0]->content; ?>
                        </div>
                        <hr>
                        <ul style="padding:0px 50px 0px 50px">
                            <li style="margin-bottom:10px">
                                <?php echo sprintf( __( 'Ticket price: %s', 'plotto' ), wc_price( $results[0]->ticket_price ) ); ?>
                            </li>
                            <li>
                                <?php echo sprintf( __( 'Number of blocks: %d number', 'plotto' ), $results[0]->choosen_block ); ?>
                            </li>
                            <li>
                                <?php echo sprintf( __( 'Number of bonuses: %d bonuse', 'plotto' ), $results[0]->choosen_bonuse ); ?>
                            </li>
                        </ul>
                        <hr>
                        <div style="text-align:center;"><?php _e( 'Grand prize', 'plotto' ); ?></div>
                        <h2 style="font-family:tahoma;text-align:center;"><?php echo wc_price( $results[0]->total_price, [ 'currency' => $results[0]->prize_currency ] ); ?></h2>
                        <hr>
                        <?php if( $results[0]->status === 'active' ): ?>
                            <p id="demo" style="text-align:center;font-size: 16px">
                                <?php _e( 'Remaining time of the lottery until expiration', 'plotto' ); ?>
                                <div id="plot-flipdown" class="flipdown" style="margin: 0 auto;" data-time="<?php echo $results[0]->expire_time; ?>"></div>
                            </p>
                            <hr>
                        <?php endif; ?>
                        <div style="text-align:center">
                            <a id="sellbtn" href="../client/buyticket/35" class="color-btn green buy" style="font-family:tahoma;font-size:16px">همین الان در این لاتاری بلیط داشته باشید</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>