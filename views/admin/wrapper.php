<?php

namespace PLotto\Views\Admin;

defined( 'ABSPATH' ) || exit; // Prevent direct access

if( ! isset( $_GET['_plot_nonce'] ) || ! wp_verify_nonce( $_GET['_plot_nonce'], 'plot-dashboard' ) )
    exit;

wp_enqueue_script( 'notiflix' );

global $wp_version;

$body_classes = [
	'plot-dashboard',
	'wp-version-' . str_replace( '.', '-', $wp_version ),
];

if ( is_rtl() ) {
	$body_classes[] = 'rtl';
}

$current_user = wp_get_current_user();
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title><?php echo esc_html__( 'PLotto Dashboard', 'plotto' ) . ' | ' . esc_html( get_bloginfo( 'title' ) ); ?></title>
	<?php wp_head(); ?>
    <script>
		var ajaxurl = '<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>';
	</script>
    <?php
    if( in_array( 'wordpress-seo/wp-seo.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
        do_action( 'wpseo_head' );
    ?>
</head>
<body class="<?php echo esc_attr( implode( ' ', $body_classes ) ); ?>">
<div id="app">
    <div id="sidebar">
        <div class="sidebar-wrapper active">
            <div class="sidebar-header position-relative">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="logo">
                        <a href="<?php echo admin_url(); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="2500" height="567" viewBox="0 0 540.001 122.523">
                                <path d="M313.19 48.227h-21.257v2.255c6.648 0 7.718 1.425 7.718 9.856V75.54c0 8.431-1.068 9.976-7.718 9.976-5.105-.713-8.55-3.444-13.3-8.67l-5.463-5.937c7.362-1.308 11.28-5.938 11.28-11.164 0-6.53-5.58-11.518-16.031-11.518H247.52v2.255c6.648 0 7.718 1.425 7.718 9.856V75.54c0 8.431-1.069 9.976-7.718 9.976v2.256h23.631v-2.256c-6.649 0-7.718-1.545-7.718-9.976v-4.273h2.018l13.183 16.505h34.557c16.98 0 24.344-9.024 24.344-19.832-.001-10.807-7.363-19.713-24.345-19.713zm-49.756 19.355V51.79h4.868c5.343 0 7.719 3.681 7.719 7.956 0 4.157-2.376 7.837-7.719 7.837l-4.868-.001zm50.113 16.508h-.832c-4.273 0-4.867-1.067-4.867-6.53V51.79h5.699c12.351 0 14.605 9.024 14.605 16.031 0 7.243-2.256 16.269-14.605 16.269zM181.378 71.978l8.194-24.228c2.376-7.006 1.307-9.023-6.293-9.023v-2.376h22.325v2.376c-7.481 0-9.262 1.78-12.231 10.449L179.834 89.79h-1.543l-12.113-37.17-12.35 37.17h-1.544l-13.181-40.613c-2.851-8.669-4.75-10.449-11.639-10.449v-2.376h26.363v2.376c-7.007 0-8.908 1.662-6.413 9.023l7.956 24.228 11.994-35.627h2.257l11.757 35.626zM221.752 89.314c-13.062 0-23.75-9.618-23.75-21.376 0-11.638 10.689-21.257 23.75-21.257 13.063 0 23.75 9.619 23.75 21.257 0 11.758-10.687 21.376-23.75 21.376zm0-38.949c-10.924 0-14.725 9.854-14.725 17.574 0 7.839 3.801 17.576 14.725 17.576 11.045 0 14.846-9.737 14.846-17.576-.001-7.72-3.801-17.574-14.846-17.574z" fill="#00749a"/>
                                <path d="M366.864 85.396v2.375H339.67v-2.375c7.957 0 9.383-2.019 9.383-13.896V52.502c0-11.877-1.426-13.775-9.383-13.775V36.35h24.581c12.231 0 19.002 6.294 19.002 14.727 0 8.194-6.771 14.606-19.002 14.606h-6.769V71.5c0 11.878 1.425 13.896 9.382 13.896zm-2.613-44.771h-6.769v20.664h6.769c6.651 0 9.738-4.631 9.738-10.212 0-5.7-3.087-10.452-9.738-10.452zM464.834 76.609l-.595 2.137c-1.067 3.919-2.376 5.344-10.807 5.344h-1.663c-6.174 0-7.243-1.425-7.243-9.855v-5.462c9.263 0 9.976.83 9.976 7.006h2.257V58.083h-2.257c0 6.175-.713 7.006-9.976 7.006V51.79h6.53c8.433 0 9.738 1.425 10.807 5.344l.596 2.256h1.898l-.83-11.162h-34.914v2.255c6.649 0 7.719 1.425 7.719 9.856V75.54c0 7.713-.907 9.656-6.15 9.934-4.983-.762-8.404-3.479-13.085-8.628l-5.463-5.937c7.362-1.308 11.282-5.938 11.282-11.164 0-6.53-5.582-11.518-16.031-11.518h-20.9v2.255c6.649 0 7.718 1.425 7.718 9.856V75.54c0 8.431-1.067 9.976-7.718 9.976v2.256h23.632v-2.256c-6.648 0-7.719-1.545-7.719-9.976v-4.273h2.019l13.182 16.505h48.806l.713-11.161-1.784-.002zm-62.938-9.027V51.79h4.868c5.344 0 7.72 3.681 7.72 7.956 0 4.157-2.376 7.837-7.72 7.837l-4.868-.001zM488.939 89.314c-4.75 0-8.907-2.493-10.688-4.038-.595.595-1.662 2.376-1.899 4.038h-2.257V72.927h2.375c.951 7.838 6.412 12.469 13.419 12.469 3.8 0 6.888-2.138 6.888-5.699 0-3.087-2.73-5.463-7.6-7.719l-6.77-3.206c-4.751-2.258-8.312-6.178-8.312-11.401 0-5.7 5.344-10.568 12.707-10.568 3.919 0 7.243 1.426 9.263 3.088.593-.476 1.188-1.782 1.544-3.208h2.256v14.014h-2.494c-.832-5.582-3.919-10.213-10.212-10.213-3.325 0-6.413 1.899-6.413 4.87 0 3.087 2.493 4.749 8.194 7.361l6.53 3.206c5.701 2.731 7.956 7.127 7.956 10.689.001 7.48-6.531 12.704-14.487 12.704zM525.514 89.314c-4.751 0-8.908-2.493-10.688-4.038-.594.595-1.662 2.376-1.898 4.038h-2.257V72.927h2.375c.95 7.838 6.411 12.469 13.419 12.469 3.8 0 6.888-2.138 6.888-5.699 0-3.087-2.731-5.463-7.601-7.719l-6.77-3.206c-4.75-2.258-8.312-6.178-8.312-11.401 0-5.7 5.344-10.568 12.707-10.568 3.919 0 7.242 1.426 9.263 3.088.593-.476 1.187-1.782 1.542-3.208h2.257v14.014h-2.493c-.832-5.582-3.919-10.213-10.212-10.213-3.325 0-6.414 1.899-6.414 4.87 0 3.087 2.494 4.749 8.195 7.361l6.53 3.206c5.701 2.731 7.956 7.127 7.956 10.689-.001 7.48-6.532 12.704-14.487 12.704z" fill="#464342"/>
                                <g fill="#464342">
                                    <path d="M8.708 61.26c0 20.803 12.089 38.779 29.619 47.299L13.259 39.872A52.355 52.355 0 0 0 8.708 61.26zM96.74 58.608c0-6.495-2.333-10.993-4.334-14.494-2.664-4.329-5.161-7.995-5.161-12.324 0-4.831 3.664-9.328 8.825-9.328.233 0 .454.029.681.042-9.35-8.565-21.807-13.796-35.488-13.796-18.36 0-34.514 9.42-43.91 23.688 1.232.037 2.395.062 3.382.062 5.497 0 14.006-.667 14.006-.667 2.833-.167 3.167 3.994.337 4.329 0 0-2.847.335-6.015.501L48.2 93.547l11.501-34.493-8.188-22.434c-2.83-.166-5.512-.501-5.512-.501-2.832-.166-2.5-4.496.332-4.329 0 0 8.68.667 13.844.667 5.495 0 14.006-.667 14.006-.667 2.835-.167 3.168 3.994.337 4.329 0 0-2.854.335-6.016.501l18.992 56.494 5.242-17.517c2.273-7.269 4.002-12.49 4.002-16.989z"/>
                                    <path d="M62.184 65.857l-15.769 45.818a52.523 52.523 0 0 0 14.847 2.142 52.523 52.523 0 0 0 17.451-2.979 4.615 4.615 0 0 1-.374-.724L62.184 65.857zM107.376 36.046c.227 1.674.354 3.472.354 5.404 0 5.333-.996 11.328-3.996 18.824l-16.053 46.413c15.624-9.111 26.133-26.038 26.133-45.427a52.268 52.268 0 0 0-6.438-25.214z"/>
                                    <path d="M61.262 0C27.483 0 0 27.481 0 61.26c0 33.783 27.483 61.264 61.263 61.264 33.777 0 61.265-27.48 61.265-61.264C122.526 27.481 95.04 0 61.262 0zm0 119.715c-32.23 0-58.453-26.223-58.453-58.455 0-32.229 26.222-58.45 58.453-58.45 32.229 0 58.449 26.221 58.449 58.45.001 32.232-26.22 58.455-58.449 58.455z"/>
                                </g>
                            </svg>
                        </a>
                    </div>
                    <div class="theme-toggle d-flex gap-2  align-items-center mt-2">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="iconify iconify--system-uicons" width="20" height="20" preserveAspectRatio="xMidYMid meet" viewBox="0 0 21 21">
                            <g fill="none" fill-rule="evenodd" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M10.5 14.5c2.219 0 4-1.763 4-3.982a4.003 4.003 0 0 0-4-4.018c-2.219 0-4 1.781-4 4c0 2.219 1.781 4 4 4zM4.136 4.136L5.55 5.55m9.9 9.9l1.414 1.414M1.5 10.5h2m14 0h2M4.135 16.863L5.55 15.45m9.899-9.9l1.414-1.415M10.5 19.5v-2m0-14v-2" opacity=".3"></path>
                                <g transform="translate(-210 -1)">
                                    <path d="M220.5 2.5v2m6.5.5l-1.5 1.5"></path>
                                    <circle cx="220.5" cy="11.5" r="4"></circle>
                                    <path d="m214 5l1.5 1.5m5 14v-2m6.5-.5l-1.5-1.5M214 18l1.5-1.5m-4-5h2m14 0h2"></path>
                                </g>
                            </g>
                        </svg>
                        <div class="form-check form-switch fs-6">
                            <input class="form-check-input  me-0" type="checkbox" id="toggle-dark" style="cursor: pointer">
                            <label class="form-check-label"></label>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="iconify iconify--mdi" width="20" height="20" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24">
                            <path fill="currentColor" d="m17.75 4.09l-2.53 1.94l.91 3.06l-2.63-1.81l-2.63 1.81l.91-3.06l-2.53-1.94L12.44 4l1.06-3l1.06 3l3.19.09m3.5 6.91l-1.64 1.25l.59 1.98l-1.7-1.17l-1.7 1.17l.59-1.98L15.75 11l2.06-.05L18.5 9l.69 1.95l2.06.05m-2.28 4.95c.83-.08 1.72 1.1 1.19 1.85c-.32.45-.66.87-1.08 1.27C15.17 23 8.84 23 4.94 19.07c-3.91-3.9-3.91-10.24 0-14.14c.4-.4.82-.76 1.27-1.08c.75-.53 1.93.36 1.85 1.19c-.27 2.86.69 5.83 2.89 8.02a9.96 9.96 0 0 0 8.02 2.89m-1.64 2.02a12.08 12.08 0 0 1-7.8-3.47c-2.17-2.19-3.33-5-3.49-7.82c-2.81 3.14-2.7 7.96.31 10.98c3.02 3.01 7.84 3.12 10.98.31Z">
                            </path>
                        </svg>
                    </div>
                    <div class="sidebar-toggler  x">
                        <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                    </div>
                </div>
            </div>
            <div class="sidebar-menu">
                <ul class="menu">
                    <li class="sidebar-title"><?php _e( 'Menu', 'plotto' ); ?></li>
                    <li class="sidebar-item <?php echo $_GET['p'] === 'dashboard' ? 'active' : ''; ?>">
                        <a href="<?php echo add_query_arg( [ 'action' => 'plot-dashboard', 'p' => 'dashboard', '_plot_nonce' => $_GET['_plot_nonce'] ], admin_url( 'admin.php' ) ); ?>" class='sidebar-link'>
                            <i class="bi bi-grid-fill"></i>
                            <span><?php _e( 'Dashboard', 'plotto' ); ?></span>
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo $_GET['p'] === 'lotteries' || $_GET['p'] === 'add-lottery' ? 'active' : ''; ?> has-sub">
                        <a href="#" class='sidebar-link'>
                            <i class="bi bi-ticket-detailed-fill"></i>
                            <span><?php _e( 'Lottery', 'plotto' ); ?></span>
                        </a>
                        <ul class="submenu">
                            <li class="submenu-item <?php echo $_GET['p'] === 'lotteries' ? 'active' : ''; ?>">
                                <a href="<?php echo add_query_arg( [ 'action' => 'plot-dashboard', 'p' => 'lotteries', '_plot_nonce' => $_GET['_plot_nonce'] ], admin_url( 'admin.php' ) ); ?>" class="submenu-link"><?php _e( 'Lotteries', 'plotto' ); ?></a>
                            </li>
                            <li class="submenu-item <?php echo $_GET['p'] === 'add-lottery' ? 'active' : ''; ?>">
                                <a href="<?php echo add_query_arg( [ 'action' => 'plot-dashboard', 'p' => 'add-lottery', '_plot_nonce' => $_GET['_plot_nonce'] ], admin_url( 'admin.php' ) ); ?>" class="submenu-link"><?php _e( 'Add lottery', 'plotto' ); ?></a>
                            </li>
                        </ul>
                    </li>
                    <li class="sidebar-item <?php echo $_GET['p'] === 'companies' || $_GET['p'] === 'add-company' ? 'active' : ''; ?> has-sub">
                        <a href="#" class='sidebar-link'>
                            <i class="bi bi-buildings"></i>
                            <span><?php _e( 'Companies', 'plotto' ); ?></span>
                        </a>
                        <ul class="submenu">
                            <li class="submenu-item <?php echo $_GET['p'] === 'companies' ? 'active' : ''; ?>">
                                <a href="<?php echo add_query_arg( [ 'action' => 'plot-dashboard', 'p' => 'companies', '_plot_nonce' => $_GET['_plot_nonce'] ], admin_url( 'admin.php' ) ); ?>" class="submenu-link"><?php _e( 'Companies', 'plotto' ); ?></a>
                            </li>
                            <li class="submenu-item <?php echo $_GET['p'] === 'add-company' ? 'active' : ''; ?>">
                                <a href="<?php echo add_query_arg( [ 'action' => 'plot-dashboard', 'p' => 'add-company', '_plot_nonce' => $_GET['_plot_nonce'] ], admin_url( 'admin.php' ) ); ?>" class="submenu-link"><?php _e( 'Add company', 'plotto' ); ?></a>
                            </li>
                        </ul>
                    </li>
                    <li class="sidebar-item <?php echo $_GET['p'] === 'participants' ? 'active' : ''; ?>">
                        <a href="<?php echo add_query_arg( [ 'action' => 'plot-dashboard', 'p' => 'participants', '_plot_nonce' => $_GET['_plot_nonce'] ], admin_url( 'admin.php' ) ); ?>" class='sidebar-link'>
                            <i class="bi bi-people"></i>
                            <span><?php _e( 'Participants', 'plotto' ); ?></span>
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo $_GET['p'] === 'withdrawal-requests' ? 'active' : ''; ?>">
                        <a href="<?php echo add_query_arg( [ 'action' => 'plot-dashboard', 'p' => 'withdrawal-requests', '_plot_nonce' => $_GET['_plot_nonce'] ], admin_url( 'admin.php' ) ); ?>" class='sidebar-link'>
                            <i class="bi bi-people"></i>
                            <span><?php _e( 'Withdrawal requests', 'plotto' ); ?></span>
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo $_GET['p'] === 'settings' ? 'active' : ''; ?>">
                        <a href="<?php echo add_query_arg( [ 'action' => 'plot-dashboard', 'p' => 'settings', '_plot_nonce' => $_GET['_plot_nonce'] ], admin_url( 'admin.php' ) ); ?>" class='sidebar-link'>
                            <i class="bi bi-gear-wide-connected"></i>
                            <span><?php _e( 'Settings', 'plotto' ); ?></span>
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo $_GET['p'] === 'reports' ? 'active' : ''; ?>">
                        <a href="<?php echo add_query_arg( [ 'action' => 'plot-dashboard', 'p' => 'reports', '_plot_nonce' => $_GET['_plot_nonce'] ], admin_url( 'admin.php' ) ); ?>" class='sidebar-link'>
                            <i class="bi bi-card-checklist"></i>
                            <span><?php _e( 'Reports', 'plotto' ); ?></span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div id="main">
        <header class="mb-3">
            <a href="#" class="burger-btn d-block d-xl-none">
                <i class="bi bi-justify fs-3"></i>
            </a>
        </header>
        <div class="row">
            <div class="col-md-9 col-sm-12">
                <h2>
                    <?php _e( 'Welcome!', 'plotto' ); ?>
                </h2>
                <div class="page-heading">
                    <h5><?php _e( ucwords( str_replace( '-', ' ', $_GET['p'] ) ), 'plotto' ); ?></h5>
                </div>
            </div>
            <div class="col-md-3 col-sm-12">
                <div class="card">
                    <div class="card-body py-4 px-4">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-xl">
                                <img src="<?php echo get_avatar_url( get_current_user_id() ); ?>" alt="Face 1">
                            </div>
                            <div class="ms-3 name">
                                <h5 class="font-bold"><?php echo $current_user->display_name; ?></h5>
                                <a href="<?php echo get_edit_user_link( $current_user->ID ); ?>"><h6 class="text-muted mb-0">@<?php echo $current_user->user_login ?> <i class="bi bi-box-arrow-up-right"></i></h6></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body py-4 px-4">
                        <i class="bi bi-calendar"></i> <?php echo wp_date('l j, m, Y'); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-content">
            <!-- Main content -->
            <?php
                include_once(
                    sprintf( PLotto_PATH . 'views/admin/pages/%s.php', $_GET['p'] )
                );
            ?>
        </div>
        <footer>
            <div class="footer clearfix mb-0 text-muted">
                <div class="float-start">
                    <p>2023 &copy; <?php echo get_bloginfo( 'name' ); ?></p>
                </div>
            </div>
        </footer>
    </div>
</div>
<?php
	wp_footer();
	/** This action is documented in wp-admin/admin-footer.php */
	do_action( 'admin_print_footer_scripts' );
?>
</body>
</html>