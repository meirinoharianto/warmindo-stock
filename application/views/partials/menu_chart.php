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

$idmenu = $this->input->post('idmenu');
$idmenu = is_array($idmenu) ? $idmenu : [];

$menu_data = [];
$menu_labels = [];
$has_menu_data = false;
$cabang = (int)$idcabang_menu;

$this->db->order_by('nama', 'asc');
if (!empty($idmenu) && !in_array('0', $idmenu)) {
    $this->db->where_in('id', $idmenu);
}

$menus0 = $this->db->get('menu_utama')->result();

$period_menu = $thn_menu . '-' . $bln_menu;

// ambil daftar cabang
$this->db->where_not_in('kode_cabang', 'PU');
$all_cabang = $this->db->get('cabang')->result();

foreach ($menus0 as $menu0) {
    $total_qty = 0;

    // Jika pilih semua cabang
    if ($cabang == 0) {
        foreach ($all_cabang as $cbg) {
            $kode_cabang = $cbg->kode_cabang;
            $arr_kode_cabang = array("SN1", "SN2", "SN7");
            $suffix = in_array($kode_cabang, $arr_kode_cabang) ? '' : '_' . $kode_cabang;

            $tableMenu = 'transaksi_produk' . $suffix;

            // cek tabel ada
            if ($this->db->table_exists($tableMenu)) {
                $menu_out = $this->db->query(
                    "SELECT SUM(qty) as qty
                     FROM {$tableMenu}
                     WHERE cabang_id = ?
                     AND bahan_id = ?
                     AND periode LIKE ?",
                    [$cbg->id, $menu0->id, $period_menu . '%']
                )->row();

                $total_qty += (float)($menu_out->qty ?? 0);
?>
                <!-- trace hasil  -->
                <p> <?= $this->db->last_query() ?>> <?= $kode_cabang ?> <?= $total_qty ?></p>
    <?php
            }
        }

        $judul_cabang = 'Semua Cabang';
    } else {
        // Jika pilih 1 cabang saja
        $caricabang = $this->db->query('SELECT * FROM cabang WHERE id = ?', [$cabang])->row();

        $suffix = '';
        $kode_cabang = '';

        if ($caricabang) {
            $kode_cabang = $caricabang->kode_cabang;
            $arr_kode_cabang = array("SN1", "SN2", "SN7");
            $suffix = in_array($kode_cabang, $arr_kode_cabang) ? '' : '_' . $kode_cabang;
        }

        $table = 'bahan_kartustok' . $suffix;

        if ($this->db->table_exists($table)) {
            $stock_out = $this->db->query(
                "SELECT SUM(jumlah_perubahan * -1) as qty
                 FROM {$table}
                 WHERE tipe_transaksi LIKE 'Penjualan%'
                 AND cabang_id = ?
                 AND bahan_id = ?
                 AND periode LIKE ?",
                [$cabang, $bahan->id, $period_menu . '%']
            )->row();

            $total_qty = (float)($stock_out->qty ?? 0);
        }

        $judul_cabang = 'Cabang ' . $kode_cabang;
    }

    // $stock_data[] = $total_qty;
    // $stock_labels[] = $bahan->nama_bahan;

    $menu_data[] = $total_qty;

    // label + qty
    $label_with_qty = $bahan->nama_bahan . ' - (' . number_format($total_qty, 0, ',', '.') . ')';
    $menu_labels[] = $label_with_qty;


    if ($total_qty > 0) {
        $has_menu_data = true;
    }
}

$jumlahLabel = count($menu_labels); // pastikan $labels tersedia
$minWidth = max(1200, $jumlahLabel * 80);
if ($has_menu_data): ?>

    <style>
        .menu-chart-scroll {
            width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
        }

        .menu-chart-inner {
            min-width: 1200px;
            /* sesuaikan */
            height: 400px;
        }

        .menu-chart-inner canvas {
            width: 100% !important;
            height: 100% !important;
        }
    </style>

    <div class="menu-chart-scroll">
        <div class="menu-chart-inner" style="min-width: <?= $minWidth ?>px;">
            <canvas id="menu-chart" height="180" style=" height: 300px;"></canvas>
        </div>
    </div>
    <!-- <canvas id="stock-chart" height="180" style="height: 300px;"></canvas> -->
    <script>
        var menuChart = document.getElementById('menu-chart');
        var chart4 = new Chart(menuChart, {
            type: 'bar',
            data: {
                labels: [<?= "'" . implode("','", array_map('addslashes', $menu_labels)) . "'" ?>],
                datasets: [{
                    label: "Keluar",
                    data: [<?= implode(',', $menu_data) ?>],
                    backgroundColor: [
                        '#2563eb',
                        '#16a34a',
                        '#0891b2',
                        '#ea580c',
                        '#dc2626',
                        '#4b5563',
                        '#6b7280',
                        '#1f2937',
                        '#52525b',
                        '#334155',
                        '#1d4ed8',
                        '#15803d',
                        '#0e7490',
                        '#b45309',
                        '#7f1d1d'
                    ],
                    // backgroundColor: [
                    //     '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                    //     '#5a5c69', '#858796', '#3a3b45', '#f8f9fc', '#5a5c69',
                    //     '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'
                    // ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Stok Keluar Bulan <?= $bulan[$bln_menu] ?? '' ?> <?= $thn_menu ?> - <?= $judul_cabang ?>'
                        // text: 'Stok Keluar Bulan <?= $bulan[$bln_stock] ?? '' ?> <?= $thn_stock ?> - Cabang <?= $kode_cabang ?>'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Total Stok Keluar'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Bahan'
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
<?php else: ?>
    <div class="alert alert-warning text-center py-4">
        <i class="fa fa-exclamation-triangle fa-2x mb-2"></i>
        <h5>Data tidak ditemukan</h5>
        <!-- <p><?= $sql . $period_stock; ?></p> -->
        <p>Tidak ada data penjualan untuk bulan <?= $bulan[$bln_menu] ?? '' ?> <?= $thn_menu ?></p>
    </div>
<?php endif; ?>