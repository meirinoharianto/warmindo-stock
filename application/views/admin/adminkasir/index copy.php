<div class="clearfix"></div>
<?php if (!empty($this->input->post('thn'))) {
    $thn = $this->input->post('thn');
} else {
    $thn = date('Y');
} ?>
<?php if (!empty($this->input->post('idcabang'))) {
    $idcabang = $this->input->post('idcabang');
} else {
    $idcabang = 0;
} ?>

<div id="adminkasir" class="d-flex flex-column h-100">
    <div class="wrapper d-flex flex-grow-1">
        <div id="content" class="p-0 flex-grow-1">
            <div class="container-fluid h-100">
                <div class="row h-100">
                    <div class="col-12 mt-3 h-100">
                        <div class="card card-rounded h-100">
                            <div class="card-header bg-primary text-white">
                                <i class="fa fa-bar-chart mr-1"></i> Grafik Penjualan <?= $thn; ?>
                            </div>
                            <div class="card-body pl-4 pr-4">
                                <!-- <div class="card-body text-center"> -->
                                <div class="row">
                                    <div class="col-12 mb-3 border w-100 rounded-lg p-2">

                                        <!-- FORM PENCARIAN -->
                                        <form method="post" action="<?= base_url('adminkasir') ?>" class="form-inline">
                                            <div class="d-flex align-items-center flex-wrap">
                                                <div class="mr-2">
                                                    <select name="idcabang" class="form-control form-control-sm">
                                                        <option value="">- Pilih Cabang -</option>
                                                        <?php
                                                        $this->db->order_by('length(nama_toko),nama_toko', 'asc');
                                                        $namacabang = $this->db->get_where('profil_toko', 'id<>1')->result();
                                                        foreach ($namacabang as $r) {
                                                        ?>
                                                            <option value="<?= $r->cabang_id; ?>" <?= ($idcabang == $r->cabang_id) ? 'selected' : '' ?>>
                                                                <?= $r->nama_toko; ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>

                                                <div class="mr-2">
                                                    <select name="thn" class="form-control form-control-sm">
                                                        <option value="">- Pilih Tahun -</option>
                                                        <?php
                                                        $thn_skr = date('Y');
                                                        for ($x = $thn_skr; $x >= 2021; $x--) {
                                                        ?>
                                                            <option value="<?= $x; ?>" <?= ($thn == $x) ? 'selected' : '' ?>>
                                                                <?= $x; ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>

                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    <i class="fa fa-search"></i>
                                                </button>

                                                <a href="<?= base_url('home') ?>" class="btn btn-success btn-sm">
                                                    <i class="fa fa-refresh"></i>
                                                </a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 mb-3 border rounded-lg">
                                        <div class="clearfix"></div>
                                        <canvas id="line-chart" height="180" style="height: 300px;"></canvas>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var linechart = document.getElementById('line-chart');
    var chart = new Chart(linechart, {
        type: 'bar',
        data: {
            labels: [
                'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'
            ], // Merubah data tanggal menjadi format JSON
            datasets: [{
                label: "Total Transaksi",
                data: [
                    <?php
                    // php mencari produk
                    $cabang = $idcabang;
                    $suffix = '';
                    if ($cabang != 0) {
                        $caricabang = $this->db->query('SELECT * FROM cabang WHERE id = ? ', [$cabang])->row();
                        $kode_cabang = $caricabang->kode_cabang;
                        $arr_kode_cabang = array("SN1", "SN2", "SN7");

                        if (in_array($kode_cabang, $arr_kode_cabang)) {
                            $suffix = '';
                        } else {
                            $suffix = '_' . $kode_cabang;
                        }
                    }


                    for ($n = 1; $n <= 12; $n++) {
                        if ($n > 9) {
                            $period = $thn . '-' . $n;
                        } else {
                            $period = $thn . '-' . '0' . $n;
                        }
                        if ($this->session->userdata('ses_level') == 'AdminKasir') {
                            // $penjualan = $this->db->query('SELECT SUM(qty) as qty FROM transaksi_produk WHERE cabang_id = ?', [$cabang], ' AND periode = ?', [$period])->row();
                            $penjualan = $this->db->query('SELECT SUM(grandtotal) as qty FROM transaksi' . $suffix . ' AS transaksi WHERE cabang_id = ? AND periode = ?', [$cabang, $period])->row();
                        } else {
                            $penjualan = $this->db->query('SELECT SUM(qty) as qty FROM transaksi_produk' . $suffix . ' AS transaksi_produk
                                        WHERE periode = ? AND kasir_id = ?', [$period, $this->session->userdata('ses_id')])->row();
                        }
                    ?>
                        <?= $penjualan->qty; ?>,
                    <?php } ?>
                ],
                borderColor: '#3c73a8',
                backgroundColor: '#3c73a8',
                borderWidth: 4,
            }, ],
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    min: 0,
                    max: 200000000,
                }
            }
        },
    });

    // Chart 2: Sales by Branch
    var branchChart = document.getElementById('branch-chart');
    var chart2 = new Chart(branchChart, {
        type: 'bar',
        data: {
            labels: [
                <?php
                $this->db->order_by('length(nama_toko),nama_toko', 'asc');
                $branches = $this->db->get_where('profil_toko', 'id<>1')->result();
                foreach ($branches as $branch) {
                    echo "'" . $branch->nama_toko . "',";
                }
                ?>
            ],
            datasets: [{
                label: "Total Penjualan",
                data: [
                    <?php
                    $period = $thn . '-' . $bln;
                    foreach ($branches as $branch) {
                        $suffix = '';
                        $caricabang = $this->db->query('SELECT * FROM cabang WHERE id = ? ', [$branch->cabang_id])->row();
                        if ($caricabang) {
                            $kode_cabang = $caricabang->kode_cabang;
                            $arr_kode_cabang = array("SN1", "SN2", "SN7");

                            if (!in_array($kode_cabang, $arr_kode_cabang)) {
                                $suffix = '_' . $kode_cabang;
                            }
                        }

                        $sales = $this->db->query('SELECT SUM(grandtotal) as qty FROM transaksi' . $suffix . ' WHERE cabang_id = ? AND periode = ?', [$branch->cabang_id, $period])->row();
                        echo ($sales->qty ?? 0) . ',';
                    }
                    ?>
                ],
                backgroundColor: [
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                    '#5a5c69', '#858796', '#3a3b45', '#f8f9fc', '#5a5c69'
                ],
                borderColor: [
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                    '#5a5c69', '#858796', '#3a3b45', '#f8f9fc', '#5a5c69'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Total Penjualan'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Cabang'
                    }
                }
            }
        }
    });
</script>