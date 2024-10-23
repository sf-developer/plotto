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
                <table class="table table-striped" id="withdrawal-requests">
                    <thead>
                        <tr>
                            <th><?php _e( 'Row', 'plotto' ); ?></th>
                            <th><?php _e( 'User', 'plotto' ); ?></th>
                            <th><?php _e( 'Request date', 'plotto' ); ?></th>
                            <th><?php _e( 'Amount', 'plotto' ); ?></th>
                            <th><?php _e( 'Payment type', 'plotto' ); ?></th>
                            <th><?php _e( 'Account', 'plotto' ); ?></th>
                            <th><?php _e( 'Status', 'plotto' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!--Action Modal -->
<div class="modal fade text-<?php echo is_rtl() ? 'right' : 'left'; ?>" id="action-modal" tabindex="-1" role="dialog"
    aria-labelledby="action-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
        role="document">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title white" id="action-modal-label">
                    <?php _e( 'Confirmation withdrawal request', 'plotto' ); ?>
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <?php _e( 'Please click on one of the following buttons. Approve or Reject', 'plotto' ); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger reject-withdrawal-modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block"><?php _e( 'Reject','plotto' ); ?></span>
                </button>
                <button type="button" class="btn btn-success ms-1 approve-withdrawal-modal">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span class="d-none d-sm-block"><?php _e( 'Approve', 'plotto' ); ?></span>
                </button>
            </div>
        </div>
    </div>
</div>