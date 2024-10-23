<?php

defined( 'ABSPATH' ) || exit; // Prevent direct access

wp_enqueue_style( 'bootstrap' );
wp_enqueue_script( 'notiflix' );
wp_enqueue_script( 'bootstrap' );
wp_enqueue_script( 'datatables' );
wp_enqueue_script( 'plotto' );

?>

<div class="table-responsive">
    <table class="display table table-striped nowrap" id="tickets">
        <thead>
            <tr>
                <th><?php _e( 'ID', 'plotto' ); ?></th>
                <th><?php _e( 'Lottery', 'plotto' ); ?></th>
                <th><?php _e( 'Ticket price', 'plotto' ); ?></th>
                <th><?php _e( 'Date participation', 'plotto' ); ?></th>
                <th><?php _e( 'Choosen blocks', 'plotto' ); ?></th>
                <th><?php _e( 'Choosen bonuses', 'plotto' ); ?></th>
                <th><?php _e( 'Amount', 'plotto' ); ?></th>
                <th><?php _e( 'Answer', 'plotto' ); ?></th>
                <th><?php _e( 'Status', 'plotto' ); ?></th>
                <th><?php _e( 'Note', 'plotto' ); ?></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>