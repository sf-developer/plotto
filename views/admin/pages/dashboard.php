<?php

defined( 'ABSPATH' ) || exit; // Prevent direct access

?>
<section class="row">
    <div class="col-12">
        <div class="row">
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card bg-dark-blue rounded-3">
                    <div class="card-body px-2 py-3">
                        <h6 class="text-white fw-normal px-4"><?php _e( 'Sales', 'plotto' ); ?></h6>
                        <h6 id="chart-sales-total" class="font-extrabold mb-0 text-white px-4"><?php echo wc_price(0); ?></h6>
                        <div id="chart-sales" class="px-0"></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card bg-light-blue rounded-3">
                    <div class="card-body px-2 py-3">
                        <h6 class="text-white fw-normal px-4"><?php _e( 'Tickets sold', 'plotto' ); ?></h6>
                        <h6 id="chart-sales-count-total" class="font-extrabold mb-0 text-white px-4">0</h6>
                        <div id="chart-ticket-sold" class="px-0"></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card bg-yellow rounded-3">
                    <div class="card-body px-2 py-3">
                        <h6 class="text-white fw-normal px-4"><?php _e( 'Loosers', 'plotto' ); ?></h6>
                        <h6 id="chart-loosers-count-total" class="font-extrabold mb-0 text-white px-4">0</h6>
                        <div id="chart-loosers" class="px-0"></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card bg-red rounded-3">
                    <div class="card-body px-2 py-3">
                        <h6 class="text-white fw-normal px-4"><?php _e( 'Winners', 'plotto' ); ?></h6>
                        <h6 id="chart-winners-count-total" class="font-extrabold mb-0 text-white px-4">0</h6>
                        <div id="chart-winners" class="px-0"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-9">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4><?php _e( 'Lottery sales', 'plotto' ); ?></h4>
                    </div>
                    <div class="card-body">
                        <div id="chart-profile-visit"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-3">
        <div class="card">
            <div class="card-header">
                <h4><?php _e( 'Today reports', 'plotto' ); ?></h4>
            </div>
            <div class="card-body">
                <div class="row my-3">
                    <div class="col-7">
                        <div class="d-flex align-items-center">
                            <svg class="bi text-primary" width="32" height="32" fill="blue" style="width:10px">
                                <use xlink:href="assets/static/images/bootstrap-icons.svg#circle-fill" />
                            </svg>
                            <h5 class="mb-0 ms-3"><?php _e( 'Sales', 'plotto' ) ?></h5>
                        </div>
                    </div>
                    <div class="col-5">
                        <h5 id="plot-total-sales" class="mb-0 text-end"></h5>
                    </div>
                    <!-- <div class="col-12">
                        <div id="chart-europe"></div>
                    </div> -->
                </div>
                <div class="row mb-3">
                    <div class="col-7">
                        <div class="d-flex align-items-center">
                            <svg class="bi text-success" width="32" height="32" fill="blue" style="width:10px">
                                <use xlink:href="assets/static/images/bootstrap-icons.svg#circle-fill" />
                            </svg>
                            <h5 class="mb-0 ms-3"><?php _e( 'Users', 'plotto' ) ?></h5>
                        </div>
                    </div>
                    <div class="col-5">
                        <h5 id="plot-total-users" class="mb-0 text-end"></h5>
                    </div>
                    <!-- <div class="col-12">
                        <div id="chart-america"></div>
                    </div> -->
                </div>
                <div class="row mb-3">
                    <div class="col-7">
                        <div class="d-flex align-items-center">
                            <svg class="bi text-danger" width="32" height="32" fill="blue" style="width:10px">
                                <use xlink:href="assets/static/images/bootstrap-icons.svg#circle-fill" />
                            </svg>
                            <h5 class="mb-0 ms-3"><?php _e( 'Winners', 'plotto' ) ?></h5>
                        </div>
                    </div>
                    <div class="col-5">
                        <h5 id="plot-total-winners" class="mb-0 text-end"></h5>
                    </div>
                    <!-- <div class="col-12">
                        <div id="chart-indonesia"></div>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
</section>