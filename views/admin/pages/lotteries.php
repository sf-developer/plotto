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
                <table class="table table-striped" id="lotteries">
                    <thead>
                        <tr>
                            <th><?php _e( 'Row', 'plotto' ); ?></th>
                            <th><?php _e( 'Name', 'plotto' ); ?></th>
                            <th><?php _e( 'Createion date', 'plotto' ); ?></th>
                            <th><?php _e( 'Answer', 'plotto' ); ?></th>
                            <th><?php _e( 'Answer date', 'plotto' ); ?></th>
                            <th><?php _e( 'Prize', 'plotto' ); ?></th>
                            <th><?php _e( 'Ticket price', 'plotto' ); ?></th>
                            <th><?php _e( 'Block/bonuse count', 'plotto' ); ?></th>
                            <th><?php _e( 'Participants', 'plotto' ); ?></th>
                            <th><?php _e( 'Previous lotteries shortcode', 'plotto' ); ?></th>
                            <th><?php _e( 'Status', 'plotto' ); ?></th>
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

<!--Lottery Modal -->
<div class="modal fade text-<?php echo is_rtl() ? 'right' : 'left'; ?>" id="lottery-modal" tabindex="-1" role="dialog"
    aria-labelledby="lottery-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
        role="document">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title white" id="lottery-modal-label">

                </h5>
                <button type="button" class="close" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary"
                    data-bs-dismiss="modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block"><?php _e( 'Close','plotto' ); ?></span>
                </button>
                <button type="button" class="btn btn-success ms-1 save-lottery-modal">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span class="d-none d-sm-block"><?php _e( 'Save', 'plotto' ); ?></span>
                </button>
            </div>
        </div>
    </div>
</div>