<!doctype html>
<html lang="en">

<head>
    <title><?= $title_web; ?> &mdash; SALASA RESTO</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="<?= base_url('assets/plugins/font-awesome-4.7.0/css/font-awesome.min.css'); ?>" />
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/plugins/magnific/magnific-popup.css'); ?>">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/plugins/bootstrap/css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/main.css?v=' . time()); ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/plugins/sweetalert2/sweetalert2.css'); ?>">
    <!-- <link rel="stylesheet" href="<?= base_url('assets/css/sidebarstyle.css'); ?>" /> -->
    <link rel="stylesheet" href="<?= base_url('assets/css/sidebar.css'); ?>" />

    <!-- Optional JavaScript -->
    <!-- DATATABLES BS 4-->
    <link rel="stylesheet" href="<?= base_url('assets/plugins/datatables/dataTables.bootstrap4.min.css'); ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/plugins/datatables/responsive.bootstrap4.min.css'); ?>" />


    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css"> -->
    <!-- jQuery -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="<?= base_url('assets/js/jquery-3.3.1.min.js'); ?>"></script>
    <script src="<?= base_url('assets/plugins/bootstrap/popper.min.js'); ?>"></script>
    <script src="<?= base_url('assets/plugins/bootstrap/js/bootstrap.min.js'); ?>"></script>
    <script type="text/javascript" src="<?= base_url('assets/plugins/magnific/jquery.magnific-popup.js'); ?>"></script>
    <script src="<?= base_url('assets/plugins/chart.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/js/jquery.twbsPagination.min.js'); ?>"></script>
    <script src="<?= base_url('assets/plugins/sweetalert2/sweetalert2.all.min.js'); ?>"></script>

    <style>
        .dropdown-submenu {
            position: relative;
        }

        .dropdown-submenu>.dropdown-menu {
            top: 0;
            left: 100%;
            margin-top: -6px;
            margin-left: -1px;
            -webkit-border-radius: 0 6px 6px 6px;
            -moz-border-radius: 0 6px 6px;
            border-radius: 0 6px 6px 6px;
        }

        .dropdown-submenu:hover>.dropdown-menu {
            display: block;
        }

        .dropdown-submenu>a:after {
            display: block;
            content: " ";
            float: right;
            width: 0;
            height: 0;
            border-color: transparent;
            border-style: solid;
            border-width: 5px 0 5px 5px;
            border-left-color: #ccc;
            margin-top: 5px;
            margin-right: -10px;
        }

        .dropdown-submenu:hover>a:after {
            border-left-color: #fff;
        }

        .dropdown-submenu.pull-left {
            float: none;
        }

        .dropdown-submenu.pull-left>.dropdown-menu {
            left: -100%;
            margin-left: 10px;
            -webkit-border-radius: 6px 0 6px 6px;
            -moz-border-radius: 6px 0 6px 6px;
            border-radius: 6px 0 6px 6px;
        }

        /* Loading chart */
        .chart-loading-box {
            position: relative;
            min-height: 320px;
        }

        .chart-loading-overlay {
            position: absolute;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            background: rgba(255, 255, 255, 0.45);
            backdrop-filter: blur(3px);
            -webkit-backdrop-filter: blur(3px);
            z-index: 20;
            border-radius: 8px;
        }

        .chart-loading-box.loading .chart-loading-overlay {
            display: flex;
        }

        .chart-loading-box.loading #product-sales-result {
            filter: blur(4px);
            pointer-events: none;
            user-select: none;
        }

        .chart-loading-spinner {
            width: 46px;
            height: 46px;
            border: 4px solid #dbeafe;
            border-top: 4px solid #2563eb;
            border-radius: 50%;
            animation: spinChartLoading 0.8s linear infinite;
            margin-bottom: 10px;
        }

        .chart-loading-text {
            font-size: 14px;
            color: #1f2937;
            font-weight: 600;
        }

        @keyframes spinChartLoading {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body class="">
    <!-- header -->
    <div id="header">
        <nav class="navbar navbar-expand-lg active navbar-light bg-light sticky-top">
            <div class="container-fluid">
                <a class="navbar-brand" href="<?= base_url('home'); ?>"><b><?= $this->session->userdata('ses_nama_toko'); ?></b></a>
                <button class="navbar-toggler d-lg-none" type="button" data-toggle="collapse" data-target="#collapsibleNavId" aria-controls="collapsibleNavId" aria-expanded="false" aria-label="Toggle navigation"><i class="fa fa-bars"></i></button>
                <div class="collapse navbar-collapse" id="collapsibleNavId">
                    <ul class="navbar-nav mr-auto mt-2 mt-lg-0">

                        <!-- MENU HOME -->
                        <li class="nav-item active">
                            <a class="nav-link" href="<?= base_url('home'); ?>">HOME <span class="sr-only">(current)</span></a>
                        </li>



                    </ul>

                    <?php
                    $shift_id = $this->session->userdata('ses_shift');
                    $profil = $this->db->get_where('login', ['id' => $this->session->userdata('ses_id')])->row();
                    // $shift = $this->db->get_where('shift', ['id' => $shift_id])->row();

                    // $closing = $this->db->get_where('closing', ['id' => $this->session->userdata('ses_opening')])->row();
                    // if (isset($closing)) {
                    //     $shift = $this->db->get_where('shift', ['id' => $this->session->userdata('ses_shift')])->row();
                    // } else {
                    //     $closing = "CLOSE";
                    // }
                    ?>
                    <ul class="navbar-nav ml-auto mr-4">
                        <!-- <div class="py-2 mr-2"><b> SHIFT <? // echo $shift->nama; 
                                                                ?></b></div> -->
                        <li class="nav-item dropdown active">
                            <a class="nav-link dropdown-toggle" href="#" id="dropdownId" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-user-circle"></i> <?= $profil->nama_user; ?>
                                <!-- <h6><?= $this->session->userdata('ses_level') ?></h6> -->
                            </a>
                            <div class="dropdown-menu" aria-labelledby="dropdownId">
                                <?php if ($this->session->userdata('ses_level') == 'Admin') { ?>
                                    <a class="dropdown-item" href="<?= base_url('info'); ?>">
                                        <i class="fa fa-cog"></i> Pengaturan Toko</a>
                                    <div class="dropdown-divider"></div>
                                <?php } ?>
                                <!-- <a class="dropdown-item" href="<?= base_url('user'); ?>">
                                    <i class="fa fa-edit"></i> Profil</a> -->
                                <a class="dropdown-item" href="<?= base_url('closing'); ?>">
                                    <i class="fa fa-handshake-o"></i> Closing</a>

                                <a class="dropdown-item" href="http://salasatekno.com/demo-saresto/RawBT_v5.0.2.apk"><i class="fa fa-download"></i> Download Driver</a>

                                <!-- <?php if ($closing != "CLOSE") { ?>
                                    <a class="dropdown-item" href="<?= base_url('closing'); ?>">
                                        <i class="fa fa-handshake-o"></i> Closing</a>
                                <?php } else { ?>
                                    <a class="dropdown-item" href="<?= base_url('opening'); ?>">
                                        <i class="fa fa-handshake-o"></i> Opening</a>
                                <?php } ?> -->
                                <div class="dropdown-divider"></div>

                                <a class="dropdown-item" href="<?= base_url('login/logout'); ?>">
                                    <i class="fa fa-sign-out"></i> Sign Out</a>
                            </div>
                        </li>
                        <li class="nav-item">
                            <!-- <button onclick="toggleFullScreen ();">Klik disini</button> -->
                            <a class="btn" href="#" id="fullscreenId">
                                <i class="fa fa-expand"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>


    </div>
    <!-- header -->