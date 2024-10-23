<?php

defined( 'ABSPATH' ) || exit; // Prevent direct access

wp_enqueue_style( 'fontawesome' );
wp_enqueue_style( 'datatables' );
wp_enqueue_script( 'bootstrap' );
wp_enqueue_script( 'datatables' );
?>
<section class="section">
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <div class="container">
                    <div class="content mb-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="filter-wrapper">
                                    <input type="checkbox" class="filter-checkbox" value="yes"/> <?php _e( 'Winners', 'plotto' ) ?>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="btn-group submitter-group float-right w-100">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><?php _e( 'Status', 'plotto' ); ?></div>
                                    </div>
                                    <select class="form-control status-dropdown">
                                        <option value=""><?php _e( 'All', 'plotto' ); ?></option>
                                        <option value="pending"><?php _e( 'Pending', 'plotto' ); ?></option>
                                        <option value="approved"><?php _e( 'Approved', 'plotto' ); ?></option>
                                        <option value="rejected"><?php _e( 'Rejected', 'plotto' ); ?></option>
                                        <option value="paid"><?php _e( 'Paid', 'plotto' ); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table class="display table table-striped nowrap" id="participants">
                        <thead>
                            <tr>
                                <th><?php _e( 'Row', 'plotto' ); ?></th>
                                <th><?php _e( 'User ID', 'plotto' ); ?></th>
                                <th><?php _e( 'Username', 'plotto' ); ?></th>
                                <th><?php _e( 'Email', 'plotto' ); ?></th>
                                <th><?php _e( 'Lottery ID', 'plotto' ); ?></th>
                                <th><?php _e( 'Buy ticket date', 'plotto' ); ?></th>
                                <th><?php _e( 'Choosen blocks/bonuses', 'plotto' ); ?></th>
                                <th><?php _e( 'Win blocks/bonuses', 'plotto' ); ?></th>
                                <th><?php _e( 'Answer date', 'plotto' ); ?></th>
                                <th><?php _e( 'Pay to user', 'plotto' ); ?></th>
                                <th><?php _e( 'Is winner', 'plotto' ); ?></th>
                                <th><?php _e( 'Prize', 'plotto' ); ?></th>
                                <th><?php _e( 'Total prize', 'plotto' ); ?></th>
                                <th><?php _e( 'Ticket price', 'plotto' ); ?></th>
                                <th><?php _e( 'Blocks coordinations', 'plotto' ); ?></th>
                                <th><?php _e( 'Bonuses coordinations', 'plotto' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!--Winner Modal -->
<div class="modal fade text-<?php echo is_rtl() ? 'right' : 'left'; ?>" id="winner-modal" tabindex="-1" role="dialog"
    aria-labelledby="winner-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
        role="document">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title white" id="winner-modal-label">
                    <?php _e( 'Confirmation winner', 'plotto' ); ?>
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
                <button type="button" class="btn btn-danger reject-winner-modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block"><?php _e( 'Reject','plotto' ); ?></span>
                </button>
                <button type="button" class="btn btn-success ms-1 approve-winner-modal">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span class="d-none d-sm-block"><?php _e( 'Approve', 'plotto' ); ?></span>
                </button>
            </div>
        </div>
    </div>
</div>