<?php

defined( 'ABSPATH' ) || exit; // Prevent direct access

use Plotto\Inc\Helpers\PLottoHelper;

wp_enqueue_style( 'fontawesome' );
wp_enqueue_style( 'bootstrap-select' );
wp_enqueue_script( 'jquery' );
wp_enqueue_script( 'jquery-repeater' );
wp_enqueue_script( 'bootstrap-select' );
wp_enqueue_script( 'easy-number-separator' );
wp_enqueue_script( 'plotto' );

$lottery_name =
$lottery_content =
$lottery_color =
$lottery_total_price =
$lottery_ticket_price =
$lottery_expire_time =
$lottery_fake_participant =
$lottery_company =
$lottery_block_count =
$lottery_choosen_block =
$lottery_bonuse_count =
$lottery_choosen_bonuse =
$prize_currency =
$lottery_status = '';

if( isset( $_GET['pid'] ) )
{
    global $wpdb;
    $lottery_table = "{$wpdb->prefix}plot_lotteries";
    $prize_table = "{$wpdb->prefix}plot_prizes";
    $results = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM `{$wpdb->prefix}plot_lotteries` WHERE `ID` = %d",
            intval( $_GET['pid'] )
        )
    );

    $prizes_results = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM `{$wpdb->prefix}plot_prizes` WHERE `lottery` = %d",
            intval( $_GET['pid'] )
        )
    );

    if( ! empty( $results ) && is_array( $results ) )
    {
        $result = $results[0];
        $lottery_name = $result->name;
        $lottery_content = $result->content;
        $lottery_color = $result->color;
        $lottery_total_price = $result->total_price;
        $lottery_ticket_price = $result->ticket_price;
        $lottery_expire_time = $result->expire_time;
        $lottery_fake_participant = $result->fake_participant;
        $lottery_company = $result->company;
        $lottery_block_count = $result->block_count;
        $lottery_choosen_block = $result->choosen_block;
        $lottery_bonuse_count = $result->bonuse_count;
        $lottery_choosen_bonuse = $result->choosen_bonuse;
        $lottery_status = $result->status;
        $prize_currency = $result->prize_currency;
    }
}
?>
<section class="section">
    <div class="card">
        <div class="card-body">
            <div class="form-group">
                <label for="lottery-name"><?php _e( 'Name', 'plotto' ); ?></label>
                <input type="text" class="form-control" id="lottery-name" name="lottery_name" value="<?php echo $lottery_name; ?>">
            </div>
            <div class="form-group">
                <label for="lottery-content"><?php _e( 'Content', 'plotto' ); ?></label>
                <?php echo wp_editor( $lottery_content, 'lottery-content', [
                    'textarea_name' => 'lottery_content',
                    'drag_drop_upload' => true,
                    'textarea_rows' => 10,
                    'tinymce' => [
                        'menubar' => false,
                        'plugins' => 'lists','link','image','charmap','preview','anchor','searchreplace','visualblocks',
                            'powerpaste','fullscreen','formatpainter','insertdatetime','media','table','help','wordcount',
                        'toolbar' => 'undo redo | a11ycheck casechange blocks | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist checklist outdent indent | removeformat | code table help'
                    ]
                ] ); ?>
            </div>
            <div class="row">
                <div class="col-md-6 com-sm-12">
                    <div class="form-group">
                        <label for="lottery-company"><?php _e( 'Company', 'plotto' ); ?></label>
                        <select class="form-control selectpicker" id="lottery-company" name="lottery_company" data-live-search="true" data-title="<?php _e( 'Choose...', 'plotto' ); ?>" data-style="plot-selectpicker w-100">
                            <?php
                            $companies = PLottoHelper::get_companies();
                            if( ! empty( $companies ) ):
                                foreach( $companies as $company ):
                            ?>
                                <option value="<?php echo $company->ID; ?>" data-content="<img src='<?php echo wp_get_attachment_url( $company->logo ); ?>' width='20' height='20' class='rounded-circle'> <?php echo $company->name; ?>" <?php echo selected( $lottery_company, $company->ID, true ); ?> data-logo="<?php echo $company->logo; ?>"><?php echo $company->name; ?></option>
                            <?php
                                endforeach;
                            endif;
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6 com-sm-12">
                    <div class="form-group">
                        <label for="lottery-expire-time"><?php _e( 'Expire time', 'plotto' ); ?></label>
                        <input type="datetime-local" class="form-control" id="lottery-expire-time" name="lottery_expire_time" min="<?php echo wp_date( 'Y-m-d\TH:i' ); ?>" value="<?php echo $lottery_expire_time; ?>" <?php echo isset( $_GET['pid'] ) ? 'readonly disabled="disabled"' : ''; ?>>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                        <label for="lottery-block-count"><?php _e( 'Block count', 'plotto' ); ?></label>
                        <input type="number" class="form-control" id="lottery-block-count" name="lottery_block_count" step="1" min="1" max="90" value="<?php echo $lottery_block_count; ?>">
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                        <label for="lottery-choosen-block"><?php _e( 'Choosen block', 'plotto' ); ?></label>
                        <input type="number" class="form-control" id="lottery-choosen-block" name="lottery_choosen_block" step="1" min="1" max="90" value="<?php echo $lottery_choosen_block; ?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                        <label for="lottery-bonuse-count"><?php _e( 'Bonuse count', 'plotto' ); ?></label>
                        <input type="number" class="form-control" id="lottery-bonuse-count" name="lottery_bonuse_count" step="1" min="0" value="<?php echo $lottery_bonuse_count; ?>">
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                        <label for="lottery-choosen-bonuse"><?php _e( 'Choosen bonuse', 'plotto' ); ?></label>
                        <input type="number" class="form-control" id="lottery-choosen-bonuse" name="lottery_choosen_bonuse" step="1" min="0" value="<?php echo $lottery_choosen_bonuse; ?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                        <label for="lottery-total-price"><?php _e( 'Total prize', 'plotto' ); ?></label>
                        <div class="input-group">
                            <input type="text" class="form-control number-separator" id="lottery-total-price" name="lottery_total_price" step="any" min="0" value="<?php echo $lottery_total_price; ?>">
                            <span class="input-group-text">
                                <select class="selectpicker" id="prize-currency" name="prize_currency" data-live-search="true" data-title="<?php _e( 'Choose...', 'plotto' ); ?>" data-style="plot-selectpicker w-100">
                                    <?php
                                    $currencies = get_woocommerce_currencies();
                                    if( ! empty( $currencies ) ):
                                        foreach( $currencies as $key => $value ):
                                        ?>
                                            <option value="<?php echo $key; ?>" <?php echo selected( $prize_currency, $key, true ); ?>><?php echo sprintf( '%1$s (%2$s)', $value, get_woocommerce_currency_symbol( $key ) ) ; ?></option>
                                        <?php
                                        endforeach;
                                    endif;
                                    ?>
                                </select>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                        <label for="lottery-ticket-price"><?php _e( 'Ticket price', 'plotto' ); ?></label>
                        <div class="input-group">
                            <input type="text" class="form-control number-separator" id="lottery-ticket-price" name="lottery_ticket_price" min="1" step="any" value="<?php echo $lottery_ticket_price; ?>" />
                            <span class="input-group-text"><?php echo get_woocommerce_currency_symbol(); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                        <label for="lottery-fake-participant"><?php _e( 'Fake participant', 'plotto' ); ?></label>
                        <input type="number" class="form-control" id="lottery-fake-participant" name="lottery_fake_participant" step="1" min="0" value="<?php echo $lottery_fake_participant; ?>">
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                        <label for="lottery-color"><?php _e( 'Lottery color', 'plotto' ); ?></label>
                        <select class="form-control selectpicker" id="lottery-color" name="lottery_color" data-live-search="true" data-title="<?php _e( 'Choose...', 'plotto' ); ?>" data-style="plot-selectpicker w-100">
                            <?php
                            $colors = PLottoHelper::get_colors();
                            if( ! empty( $colors ) ):
                                foreach( $colors as $key => $color ):
                                    $rgb = PLottoHelper::html_to_rgb( PLottoHelper::color_name_to_hex( strtolower( $color ) ) );
                                    $hsl = PLottoHelper::rgb_to_hsl( $rgb );
                                    $font_color = $hsl->lightness > 127 ? '#000' : '#fff';
                            ?>
                                <option value="<?php echo $key; ?>" <?php echo sprintf( 'style="background: %s; color: %s;"', strtolower( $color ), $font_color ); ?> <?php echo selected( $lottery_color, $key, true ); ?>><?php echo $color; ?></option>
                            <?php
                                endforeach;
                            endif;
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row border p-3 pb-1 m-0 mb-3 rounded">
                <div class="col-12">
                    <div class="row repeater-prizes">
                        <div data-repeater-list="prizes" class="row">
                            <?php
                            if( isset( $_GET['pid'] ) && ! empty( $prizes_results )  ):
                                foreach( $prizes_results as $key => $prizes_result ):
                            ?>
                                <div data-repeater-item class="col-12 mb-3 repeater-prizes-item" data-id="<?php echo $prizes_result->ID; ?>" data-lid="<?php echo $prizes_result->lottery; ?>">
                                    <div class="row">
                                        <div class="form-group col-md-4 col-sm-12">
                                            <label><?php _e( 'Block coordination', 'plotto' ); ?></label>
                                            <input type="number" name="prizes[<?php echo $key; ?>][block_coordination]" class="form-control" min="1" step="1" value="<?php echo $prizes_result->block_coordination; ?>" />
                                        </div>
                                        <div class="form-group col-md-4 col-sm-12">
                                            <label><?php _e( 'Bonuse coordination', 'plotto' ); ?></label>
                                            <input type="number" name="prizes[<?php echo $key; ?>][bonuse_coordination]" class="form-control" min="0" step="1" value="<?php echo $prizes_result->bonuse_coordination; ?>" />
                                        </div>
                                        <div class="form-group col-md-4 col-sm-12">
                                            <label><?php _e( 'Prize', 'plotto' ); ?></label>
                                            <div class="input-group">
                                                <input type="text" name="prizes[<?php echo $key; ?>][prize]" class="form-control number-separator" min="1" step="any" value="<?php echo $prizes_result->amount; ?>" />
                                                <span class="input-group-text"><?php echo get_woocommerce_currency_symbol(); ?></span>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <span data-repeater-delete class="btn btn-danger w-100">
                                                <i class="fa-solid fa-trash"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php
                                endforeach;
                            else:
                            ?>
                                <div data-repeater-item class="col-12 mb-3 repeater-prizes-item" data-id="0">
                                    <div class="row">
                                        <div class="form-group col-md-4 col-sm-12">
                                            <label><?php _e( 'Block coordination', 'plotto' ); ?></label>
                                            <input type="number" name="block_coordination" class="form-control" min="1" step="1" />
                                        </div>
                                        <div class="form-group col-md-4 col-sm-12">
                                            <label><?php _e( 'Bonuse coordination', 'plotto' ); ?></label>
                                            <input type="number" name="bonuse_coordination" class="form-control" min="0" step="1" />
                                        </div>
                                        <div class="form-group col-md-4 col-sm-12">
                                            <label><?php _e( 'Prize', 'plotto' ); ?></label>
                                            <div class="input-group">
                                                <input type="text" name="prize" class="form-control number-separator" min="1" step="any" />
                                                <span class="input-group-text"><?php echo get_woocommerce_currency_symbol(); ?></span>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <span data-repeater-delete class="btn btn-danger w-100">
                                                <i class="fa-solid fa-trash"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <span data-repeater-create class="btn btn-info">
                                <i class="fa-solid fa-plus"></i><?php _e( 'Add', 'cpay-gateway' ); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-primary save-lottery" data-pid="<?php echo isset( $_GET['pid'] ) ? $_GET['pid'] : '0'; ?>"><?php _e( 'Save', 'plotto' ); ?></button>
        </div>
    </div>
</section>