<div class="clearfix"></div>
<?php
// Default values
$thn_monthly = !empty($this->input->post('thn_monthly')) ? $this->input->post('thn_monthly') : date('Y');
$thn_branch = !empty($this->input->post('thn_branch')) ? $this->input->post('thn_branch') : date('Y');
$bln_branch = !empty($this->input->post('bln_branch')) ? $this->input->post('bln_branch') : date('m');
$idcabang_monthly = !empty($this->input->post('idcabang_monthly')) ? $this->input->post('idcabang_monthly') : 0;
?>

<div id="adminkasir" class="d-flex flex-column h-100">
    <div class="wrapper d-flex flex-grow-1">
        <div id="content" class="p-0 flex-grow-1">
            <div class="container-fluid h-100">
                <div class="row h-100">
                    <div class="col-12 mt-3 h-100">
                        <div class="card card-rounded h-100">
                            <div class="card-header bg-primary text-white">
                                <i class="fa fa-bar-chart mr-1"></i> Grafik Penjualan
                            </div>
                            <div class="card-body pl-4 pr-4">
                                <!-- Chart 1: Sales by Month -->
                                <div class="row mb-4">
                                    <div class="col-12 border rounded-lg p-3">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5>Penjualan per Bulan</h5>
                                            <form method="post" action="<?= base_url('adminkasir') ?>" class="form-inline">
                                                <div class="d-flex align-items-center">
                                                    <div class="mr-2">
                                                        <select name="idcabang_monthly" class="form-control form-control-sm">
                                                            <option value="0">- Semua Cabang -</option>
                                                            <?php
                                                            $this->db->order_by('length(nama_toko),nama_toko', 'asc');
                                                            $namacabang = $this->db->get_where('profil_toko', 'id<>1')->result();
                                                            foreach ($namacabang as $r) {
                                                            ?>
                                                                <option value="<?= $r->cabang_id; ?>" <?= ($idcabang_monthly == $r->cabang_id) ? 'selected' : '' ?>>
                                                                    <?= $r->nama_toko; ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="mr-2">
                                                        <select name="thn_monthly" class="form-control form-control-sm">
                                                            <?php
                                                            $thn_skr = date('Y');
                                                            for ($x = $thn_skr; $x >= 2021; $x--) {
                                                            ?>
                                                                <option value="<?= $x; ?>" <?= ($thn_monthly == $x) ? 'selected' : '' ?>>
                                                                    <?= $x; ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <button type="submit" name="filter_monthly" class="btn btn-primary btn-sm">
                                                        <i class="fa fa-filter"></i> Filter
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                        <canvas id="line-chart" height="180" style="height: 300px;"></canvas>
                                    </div>
                                </div>

                                <!-- Chart 2: Sales by Branch -->
                                <div class="row">
                                    <div class="col-12 border rounded-lg p-3">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5>Penjualan per Cabang</h5>
                                            <form method="post" action="<?= base_url('adminkasir') ?>" class="form-inline">
                                                <div class="d-flex align-items-center">
                                                    <div class="mr-2">
                                                        <select name="bln_branch" class="form-control form-control-sm">
                                                            <?php
                                                            $bulan = array(
                                                                '01' => 'Januari',
                                                                '02' => 'Februari',
                                                                '03' => 'Maret',
                                                                '04' => 'April',
                                                                '05' => 'Mei',
                                                                '06' => 'Juni',
                                                                '07' => 'Juli',
                                                                '08' => 'Agustus',
                                                                '09' => 'September',
                                                                '10' => 'Oktober',
                                                                '11' => 'November',
                                                                '12' => 'Desember'
                                                            );
                                                            foreach ($bulan as $key => $value) {
                                                            ?>
                                                                <option value="<?= $key ?>" <?= ($bln_branch == $key) ? 'selected' : '' ?>>
                                                                    <?= $value ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="mr-2">
                                                        <select name="thn_branch" class="form-control form-control-sm">
                                                            <?php
                                                            $thn_skr = date('Y');
                                                            for ($x = $thn_skr; $x >= 2021; $x--) {
                                                            ?>
                                                                <option value="<?= $x; ?>" <?= ($thn_branch == $x) ? 'selected' : '' ?>>
                                                                    <?= $x; ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <button type="submit" name="filter_branch" class="btn btn-primary btn-sm">
                                                        <i class="fa fa-filter"></i> Filter
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                        <canvas id="branch-chart" height="180" style="height: 300px;"></canvas>
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
    // Chart 1: Sales by Month
    var linechart = document.getElementById('line-chart');
    var chart = new Chart(linechart, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'],
            datasets: [{
                label: "Total Transaksi",
                data: [
                    <?php
                    $cabang = $idcabang_monthly;
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
                        $period = $thn_monthly . '-' . str_pad($n, 2, '0', STR_PAD_LEFT);
                        if ($this->session->userdata('ses_level') == 'AdminKasir') {
                            $penjualan = $this->db->query(
                                'SELECT SUM(grandtotal) as qty FROM transaksi' . $suffix . ' WHERE cabang_id = ? AND periode LIKE ?',
                                [$cabang, $period . '%']
                            )->row();
                        } else {
                            $penjualan = $this->db->query('SELECT SUM(qty) as qty FROM transaksi_produk' . $suffix . ' 
                                WHERE periode LIKE ? AND kasir_id = ?', [$period . '%', $this->session->userdata('ses_id')])->row();
                        }
                        echo ($penjualan->qty ?? 0) . ',';
                    }
                    ?>
                ],
                borderColor: '#3c73a8',
                backgroundColor: '#3c73a8',
                borderWidth: 2,
            }],
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Penjualan Tahun <?= $thn_monthly ?>' + (<?= $idcabang_monthly ?> != 0 ? ' - Cabang Terpilih' : ' - Semua Cabang')
                }
            },
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
                        text: 'Bulan'
                    }
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
                    echo "'" . addslashes($branch->nama_toko) . "',";
                }
                ?>
            ],
            datasets: [{
                label: "Total Penjualan",
                data: [
                    <?php
                    $period = $thn_branch . '-' . $bln_branch;
                    foreach ($branches as $branch) {
                        $suffix = '';
                        $caricabang = $this->db->query('SELECT * FROM cabang WHERE id = ?', [$branch->cabang_id])->row();
                        if ($caricabang) {
                            $kode_cabang = $caricabang->kode_cabang;
                            $arr_kode_cabang = array("SN1", "SN2", "SN7");

                            if (!in_array($kode_cabang, $arr_kode_cabang)) {
                                $suffix = '_' . $kode_cabang;
                            }
                        }

                        $sales = $this->db->query('SELECT SUM(grandtotal) as qty FROM transaksi' . $suffix . ' 
                            WHERE periode LIKE ?', [$period . '%'])->row();
                        echo ($sales->qty ?? 0) . ',';
                    }
                    ?>
                ],
                backgroundColor: [
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                    '#5a5c69', '#858796', '#3a3b45', '#f8f9fc', '#5a5c69',
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Penjualan Bulan <?= $bulan[$bln_branch] ?? '' ?> <?= $thn_branch ?> - Semua Cabang'
                }
            },
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
                    },
                    ticks: {
                        autoSkip: false,
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            }
        }
    });
</script>