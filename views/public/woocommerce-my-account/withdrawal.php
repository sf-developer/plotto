<?php

defined( 'ABSPATH' ) || exit; // Prevent direct access

wp_enqueue_style( 'bootstrap' );
wp_enqueue_style( 'bootstrap-select' );
wp_enqueue_script( 'notiflix' );
wp_enqueue_script( 'bootstrap' );
wp_enqueue_script( 'bootstrap-select' );
wp_enqueue_script( 'datatables' );
wp_enqueue_script( 'plotto' );

?>

<button type="button" class="btn btn-primary mb-3 plot-request-withdrawal" data-bs-toggle="modal" data-bs-target="#requestWithdrawalModal"><?php _e( 'Request withdrawal', 'plotto' ); ?></button>
<div class="table-responsive">
    <table class="table table-striped" id="withdrawal">
        <thead>
            <tr>
                <th><?php _e( 'ID', 'plotto' ); ?></th>
                <th><?php _e( 'Date request', 'plotto' ); ?></th>
                <th><?php _e( 'Amount', 'plotto' ); ?></th>
                <th><?php _e( 'Wallet address', 'plotto' ); ?></th>
                <th><?php _e( 'Status', 'plotto' ); ?></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

<!-- Modal Body -->
<!-- if you want to close by clicking outside the modal, delete the last endpoint:data-bs-backdrop and data-bs-keyboard -->
<div
    class="modal fade"
    id="requestWithdrawalModal"
    tabindex="-1"
    data-bs-backdrop="static"
    data-bs-keyboard="false"

    role="dialog"
    aria-labelledby="requestWithdrawalModalTitle"
    aria-hidden="true"
>
    <div
        class="modal-dialog modal-dialog-scrollable modal-dialog-centered"
        role="document"
    >
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="requestWithdrawalModalTitle">
                    <?php _e( 'Request withdrawal', 'plotto' ); ?>
                </h5>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="<?php _e( 'Close', 'plotto' ); ?>"
                ></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="payment-type"><?php _e( 'Payment type', 'plotto' ); ?></label>
                            <select class="form-control selectpicker" id="payment-type" name="payment_type" data-live-search="true" data-title="<?php _e( 'Choose...', 'plotto' ); ?>">
                                <option value="tether" selected="selected"><?php _e( 'Tether', 'plotto'); ?></option>
                                <option value="perfect"><?php _e( 'Perfect money', 'plotto'); ?></option>
                                <option value="account"><?php _e( 'Account', 'plotto'); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="amount"><?php _e( 'Amount', 'plotto' ); ?></label>
                            <input type="text" class="form-control" id="amount" name="amount" value="0">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 if-tether">
                        <div class="form-group">
                            <label for="tether-wallet"><?php _e( 'Tether wallet', 'plotto' ); ?></label>
                            <input type="text" class="form-control" id="tether-wallet" name="tether_wallet" value="">
                        </div>
                    </div>
                    <div class="col-12 d-none if-perfect">
                        <div class="form-group">
                            <label for="perfect-account"><?php _e( 'Perfect account', 'plotto' ); ?></label>
                            <input type="text" class="form-control" id="perfect-account" name="perfect_account" value="">
                        </div>
                    </div>
                    <div class="col-12 d-none if-account">
                        <div class="form-group">
                            <label for="iban"><?php _e( 'IBAN', 'plotto' ); ?></label>
                            <input type="text" class="form-control" id="iban" name="iban" value="">
                        </div>
                        <div class="form-group">
                            <label for="card-no"><?php _e( 'Card No', 'plotto' ); ?></label>
                            <input type="text" class="form-control" id="card-no" name="card_no" value="">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal"
                >
                    <?php _e( 'Close', 'plotto' ) ?>
                </button>
                <button type="button" class="btn btn-primary plot-save-request-withdrawal"><?php _e( 'Save', 'plotto' ); ?></button>
            </div>
        </div>
    </div>
</div>
