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
    </style>
</head>

<body class="bg-dark">
    <!-- header -->
    <div id="header">
        <nav class="navbar active py-3 navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="<?= base_url('home'); ?>"><b><?= $this->session->userdata('ses_nama_toko'); ?></b></a>
                <button class="navbar-toggler d-lg-none" type="button" data-toggle="collapse" data-target="#collapsibleNavId" aria-controls="collapsibleNavId" aria-expanded="false" aria-label="Toggle navigation"><i class="fa fa-bars"></i></button>
                <div class="collapse navbar-collapse" id="collapsibleNavId">
                    <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                        <?php if ($this->session->userdata('ses_level') != 'AdminKasir') { ?>

                            <li class="nav-item active">
                                <a class="nav-link" href="<?= base_url('home'); ?>">HOME <span class="sr-only">(current)</span></a>
                            </li>

                            <?php
                            // if ($this->session->userdata('ses_level') == 'Admin') { 
                            if (in_array($this->session->userdata('ses_level'), array('Admin', 'SuperAdmin'))) {
                            ?>


                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdo wn-toggle" href="#" id="dropdownId" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">DATA MASTER</a>
                                    <div class="dropdown-menu" aria-labelledby="dropdownId">
                                        <a class="dropdown-item" href="<?= base_url('menuutama'); ?>">
                                            <i class="fa fa-cubes pr-1"></i> Menu Utama</a>
                                        <a class="dropdown-item" href="<?= base_url('bahan'); ?>">
                                            <i class="fa fa-cubes pr-1"></i> Bahan</a>
                                        <!-- <a class="dropdown-item" href="<?= base_url('bahanbaku'); ?>">
                                            <i class="fa fa-cubes pr-1"></i> Bahan Baku</a> -->
                                        <!-- <div class="dropdown-divider"></div> -->
                                        <!-- <a class="dropdown-item" href="<?= base_url('kategori'); ?>">
                                            <i class="fa fa-tags pr-1"></i> Kategori</a>
                                        <a class="dropdown-item" href="<?= base_url('kategoribahan'); ?>">
                                            <i class="fa fa-tags pr-1"></i> Kategori Bahan</a> -->
                                        <!-- <div class="dropdown-divider"></div> -->
                                        <!-- <a class="dropdown-item" href="<?= base_url('customer'); ?>">
                                    <i class="fa fa-users pr-1"></i> Customer</a> -->
                                        <?php if ($this->session->userdata('ses_level') == 'SuperAdmin') { ?>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="<?= base_url('users'); ?>">
                                                <i class="fa fa-user pr-1"></i> Pengguna</a>
                                        <?php } ?>
                                    </div>
                                </li>


                            <?php } ?>

                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="dropdownId" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">STOK</a>
                                <div class="dropdown-menu" aria-labelledby="dropdownId">
                                    <?php if ($this->session->userdata('ses_level') == 'Admin') { ?>

                                        <!-- <a class="dropdown-item" href="<?= base_url('bahan'); ?>">
                                            <i class="fa fa-cubes pr-1"></i> Tambah/Ubah Bahan</a> -->
                                        <a class="dropdown-item" href="<?= base_url('bahan/stok'); ?>"><i class="fa fa-cubes pr-1"></i> Daftar Stok Bahan</a>

                                        <a class="dropdown-item" href="<?= base_url('bahan/stokawal'); ?>"><i class="fa fa-cubes pr-1"></i> Stok Awal Bahan</a>

                                        <!-- <a class="dropdown-item" href="<?= base_url('bahanbaku/stok'); ?>">
                                            <i class="fa fa-cubes pr-1"></i> Entry Stok</a> -->

                                        <a class="dropdown-item" href="<?= base_url('bahan/transferstok'); ?>">
                                            <i class="fa fa-cubes pr-1"></i> Transfer Stok</a>
                                    <?php } else { ?>
                                        <a class="dropdown-item" href="<?= base_url('bahan/stok?id=' . $this->session->userdata('ses_cabang_id')); ?>"><i class="fa fa-cubes pr-1"></i> Daftar Stok Bahan</a>
                                    <?php } ?>
                                    <a class="dropdown-item" href="<?= base_url('bahan/transferstok_terima'); ?>">
                                        <i class="fa fa-cubes pr-1"></i> Terima Stok</a>


                                    <!-- <div class="dropdown-divider"></div> -->
                                    <!-- <a class="dropdown-item" href="<?= base_url('menu/persediaan'); ?>">
                                        <i class="fa fa-list pr-1"></i> Daftar Stok Menu</a> -->
                                </div>
                            </li>
                            <?php if ($this->session->userdata('ses_level') == 'Admin') { ?>

                                <li class="nav-item active">
                                    <a class="nav-link" href="<?= base_url('kasirstok'); ?>" id="btnKasirStok">KASIR STOK</a>
                                </li>
                            <?php } ?>
                            <?php if ($this->session->userdata('ses_level') == 'Kasir') { ?>
                                <?php if ((int)$this->session->userdata('ses_id') == 44) { ?>
                                    <li class="nav-item active">
                                        <a class="nav-link" href="<?= base_url('kasirstok'); ?>" id="btnKasirStok">KASIR STOK</a>
                                    </li>
                                <?php } else { ?>

                                    <li class="nav-item active">
                                        <a class="nav-link" href="<?= base_url('kasir'); ?>" id="btnKasir">KASIR</a>
                                    </li>
                                <?php } ?>


                                <!-- <li class="nav-item active"> -->
                                <!-- <a class="nav-link" href="http://salasatekno.com/demo-saresto/RawBT_v5.0.2.apk" id="btnKasir">Download</a> -->
                                <!-- </li> -->

                                <li class="nav-item">
                                    <a class="nav-link" href="<?= base_url('keuangan/pengeluaran'); ?>">PENGELUARAN</a>
                                </li>

                                <?php
                                $day = $this->db->get_where('transaksi', ['date'  => date('Y-m-d')])->num_rows();
                                // $co = $this->db->get_where('transaksi', ['status' => 'Bayar Nanti'])->num_rows();
                                $cdo = $this->db->get_where('transaksi', [
                                    // 'status' => 'Bayar Nanti',
                                    'status' => 'Cash',
                                    'date'  => date('Y-m-d')
                                ])->num_rows();
                                $cbo = $this->db->get_where('transaksi', [
                                    // 'status' => 'Bayar Nanti',
                                    'status' => 'QRIS',
                                    'date'  => date('Y-m-d')
                                ])->num_rows();
                                $clo = $this->db->get_where('transaksi', [
                                    // 'status' => 'Bayar Nanti',
                                    'status' => 'Online',
                                    'date'  => date('Y-m-d')
                                ])->num_rows();
                                ?>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="dropdownId" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">ORDER
                                        <span class="badge badge-danger"><? //echo $co; 
                                                                            ?></span></a>
                                    <div class="dropdown-menu " aria-labelledby="dropdownId">
                                        <!--<a class="dropdown-item" href="#">CEK ORDER</a>
                                <div class="dropdown-divider"></div>-->
                                        <a class="dropdown-item" href="<?= base_url('order'); ?>">All Order
                                            <span class="badge badge-secondary float-right"><?= $day; ?></span>
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="<?= base_url('order?jenis=1'); ?>">Cash
                                            <span class="badge badge-primary float-right"><?= $cdo; ?></span></a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="<?= base_url('order?jenis=2'); ?>">QRIS
                                            <span class="badge badge-warning float-right"><?= $cbo; ?></a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="<?= base_url('order?jenis=3'); ?>">Online
                                            <span class="badge badge-success float-right"><?= $clo; ?></a>
                                        <div class="dropdown-divider"></div>
                                        <!-- <a class="dropdown-item" href="<?= base_url('order?jenis=4'); ?>"> Blm Lunas
                                    <span class="badge badge-danger float-right"><? //echo $co; 
                                                                                    ?></a> -->
                                    </div>
                                </li>

                            <?php } ?>
                        <?php } ?>

                        <!-- MENU AKUNTANSI -->
                        <?php if ($this->session->userdata('ses_level') == 'Admin') { ?>
                            <!-- <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="dropdownId" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">AKUNTANSI</a>
                                <div class="dropdown-menu" aria-labelledby="dropdownId">
                                    <a class="dropdown-item" href="<?= base_url('keuangan'); ?>">Ledger</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="<?= base_url('keuangan/lain'); ?>">Keuangan Lainnya</a>
                                </div>
                            </li> -->
                        <?php } ?>

                        <!-- <div class="nav-item dropdown">
                            <a id="dLabel" role="button" data-toggle="dropdown" class="btn btn-primary" data-target="#" href="/page.html">
                                Dropdown <span class="caret"></span>
                            </a> 
                         <a class="nav-link dropdown-toggle" href="#" id="dropdownId" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">LAPORAN</a>

                            <ul class="dropdown-menu multi-level" role="menu" aria-labelledby="dropdownMenu">
                                <li><a href="#">Some action</a></li>
                                <li><a href="#">Some other action</a></li>
                                <li class="divider"></li>
                                <li class="dropdown-submenu">
                                    <a tabindex="-1" href="#">Hover me for more options</a>
                                    <ul class="dropdown-menu">
                                        <li><a tabindex="-1" href="#">Second level</a></li>
                                        <li class="dropdown-submenu">
                                            <a href="#">Even More..</a>
                                            <ul class="dropdown-menu">
                                                <li><a href="#">3rd level</a></li>
                                                <li><a href="#">3rd level</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="#">Second level</a></li>
                                        <li><a href="#">Second level</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div> -->

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="dropdownId" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">LAPORAN</a>

                            <div class="dropdown-menu" aria-labelledby="dropdownId">

                                <?php if (in_array($this->session->userdata('ses_level'), array('Admin', 'AdminKasir'))) { ?>
                                    <!-- <a class="dropdown-item" href="<?= base_url('laporan'); ?>">Transaksi Penjualan</a> -->
                                    <a class="dropdown-item" href="<?= base_url('laporan/closing'); ?>">Closing</a>
                                    <a class="dropdown-item" href="<?= base_url('laporan/pengeluaran'); ?>">Pengeluaran</a>
                                    <!-- <a class="dropdown-item" href="<?= base_url('laporan/penjualan'); ?>">Penjualan</a> -->
                                <?php } else { ?>
                                    <a class="dropdown-item" href="<?= base_url('laporan?kasir=' . $this->session->userdata('ses_id')); ?>">Transaksi per Kasir
                                        Penjualan</a>
                                    <a class="dropdown-item" href="<?= base_url('laporan/closing?kasir=' . $this->session->userdata('ses_id')); ?>">Closing per Kasir</a>
                                    <a class="dropdown-item" href="<?= base_url('laporan/lap_kartustok_pershift?shift='); ?>">Kartu Stok per Shift</a>
                                <?php } ?>
                                <!-- <div class="dropdown-divider"></div> -->
                                <!-- <a class="dropdown-item" href="<?= base_url('laporan/produk'); ?>">History Per Menu</a> -->
                                <?php if (in_array($this->session->userdata('ses_level'), array('Admin', 'AdminKasir'))) { ?>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="<?= base_url('laporan/kartustok'); ?>">Kartu Stok</a>

                                    <!-- <a class="dropdown-item" href="<?= base_url('laporan/cash'); ?>">Cash Flow</a> -->
                                <?php } ?>
                            </div>
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
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="dropdownId" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-user-circle"></i> <?= $profil->nama_user; ?>
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