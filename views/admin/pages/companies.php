<?php

defined( 'ABSPATH' ) || exit; // Prevent direct access

wp_enqueue_style( 'datatables' );
wp_enqueue_script( 'datatables' );
?>
<section class="section">
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="companies">
                    <thead>
                        <tr>
                            <th><?php _e( 'Row', 'plotto' ); ?></th>
                            <th><?php _e( 'Name', 'plotto' ); ?></th>
                            <th><?php _e( 'Logo', 'plotto' ); ?></th>
                            <th><?php _e( 'Content', 'plotto' ); ?></th>
                            <th><?php _e( 'Action', 'plotto' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>