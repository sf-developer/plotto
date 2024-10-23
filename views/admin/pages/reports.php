<?php

defined( 'ABSPATH' ) || exit; // Prevent direct access

wp_enqueue_style( 'datatables' );
wp_enqueue_script( 'bootstrap' );
wp_enqueue_script( 'datatables' );
?>
<section class="section">
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="reports">
                    <thead>
                        <tr>
                            <th><?php _e( 'ID', 'plotto' ); ?></th>
                            <th><?php _e( 'Message', 'plotto' ); ?></th>
                            <th><?php _e( 'Registrar', 'plotto' ); ?></th>
                            <th><?php _e( 'Date', 'plotto' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>