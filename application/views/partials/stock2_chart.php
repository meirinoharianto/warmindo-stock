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

$idbahan_stock2 = $this->input->post('idbahan_stock2');
$idbahan_stock2 = is_array($idbahan_stock2) ? $idbahan_stock2 : [];

$stock2_data = [];
$stock2_labels = [];
$has_stock2_data = false;
if ($idcabang_stock2 != "all") {
    $cabangStock2 = (int)$idcabang_stock2;
} else {
    $cabangStock2 = 0;
}

$this->db->order_by('nama', 'asc');
if (!empty($idbahan_stock2) && !in_array('0', $idbahan_stock2)) {
    $this->db->where_in('id', $idbahan_stock2);
}

$bahans = $this->db->get('menu_utama')->result();

$period_stock2 = $thn_stock2 . '-' . $bln_stock2;

// ambil daftar cabang
$this->db->where_not_in('kode_cabang', 'PU');
$all_cabang = $this->db->get('cabang')->result();

foreach ($bahans as $bahan) {
    $total_qty = 0;

    // Jika pilih semua cabang
    if ($cabangStock2 == 0) {
        foreach ($all_cabang as $cbg) {
            $kode_cabang = $cbg->kode_cabang;
            $arr_kode_cabang = array("SN1", "SN2", "SN7");
            $suffix = in_array($kode_cabang, $arr_kode_cabang) ? '' : '_' . $kode_cabang;

            $table = 'bahan_kartustok' . $suffix;

            // cek tabel ada
            if ($this->db->table_exists($table)) {
                $stock2_out = $this->db->query(
                    "SELECT SUM(jumlah_perubahan * -1) as qty
                     FROM {$table}
                     WHERE tipe_transaksi LIKE 'Penjualan%'
                     AND cabang_id = ?
                     AND bahan_id = ?
                     AND periode LIKE ?",
                    [$cbg->id, $bahan->id, $period_stock2 . '%']
                )->row();

                $total_qty += (float)($stock2_out->qty ?? 0);
?>
                <!-- trace hasil  -->
                <!-- <p> <?= $kode_cabang ?> <?= $total_qty ?></p> -->
    <?php
            }
        }

        $judul_cabang = 'Semua Cabang';
    } else {
        // Jika pilih 1 cabang saja
        $caricabang = $this->db->query('SELECT * FROM cabang WHERE id = ?', [$cabangStock2])->row();

        $suffix = '';
        $kode_cabang = '';

        if ($caricabang) {
            $kode_cabang = $caricabang->kode_cabang;
            $arr_kode_cabang = array("SN1", "SN2", "SN7");
            $suffix = in_array($kode_cabang, $arr_kode_cabang) ? '' : '_' . $kode_cabang;
        }

        $table = 'transaksi_produk' . $suffix;

        if ($this->db->table_exists($table)) {
            $stock2_out = $this->db->query(
                "SELECT SUM(qty) as qty
                 FROM {$table}
                 WHERE cabang_id = ?
                 AND kode_menu = ?
                 AND periode LIKE ?",
                [$cabangStock2, $bahan->kode_menu, $period_stock2 . '%']
            )->row();

            $total_qty = (float)($stock2_out->qty ?? 0);
        }

        $judul_cabang = 'Cabang ' . $kode_cabang;
    }

    // $stock_data[] = $total_qty;
    // $stock_labels[] = $bahan->nama_bahan;

    $stock2_data[] = $total_qty;

    // label + qty
    $label_with_qty = $bahan->nama . ' - (' . number_format($total_qty, 0, ',', '.') . ')';
    $stock2_labels[] = $label_with_qty;


    if ($total_qty > 0) {
        $has_stock2_data = true;
    }
}

$jumlahLabel = count($stock2_labels); // pastikan $labels tersedia
$minWidth = max(1200, $jumlahLabel * 80);
if ($has_stock2_data): ?>

    <style>
        .stock2-chart-scroll {
            width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
        }

        .stock2-chart-inner {
            min-width: 1200px;
            /* sesuaikan */
            height: 400px;
        }

        .stock2-chart-inner canvas {
            width: 100% !important;
            height: 100% !important;
        }
    </style>

    <div class="stock2-chart-scroll">
        <div class="stock2-chart-inner" style="min-width: <?= $minWidth ?>px;">
            <canvas id="stock2-chart" height="180" style=" height: 300px;"></canvas>
        </div>
    </div>
    <!-- <canvas id="stock-chart" height="180" style="height: 300px;"></canvas> -->
    <script>
        var stock2Chart = document.getElementById('stock2-chart');
        var chart3 = new Chart(stock2Chart, {
            type: 'bar',
            data: {
                labels: [<?= "'" . implode("','", array_map('addslashes', $stock2_labels)) . "'" ?>],
                datasets: [{
                    label: "Keluar",
                    data: [<?= implode(',', $stock2_data) ?>],
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
                        text: 'Menu Terjual Bulan <?= $bulan[$bln_stock2] ?? '' ?> <?= $thn_stock2 ?> - <?= $judul_cabang ?>'
                        // text: 'Stok Keluar Bulan <?= $bulan[$bln_stock] ?? '' ?> <?= $thn_stock ?> - Cabang <?= $kode_cabang ?>'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Total Menu Terjual'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Menu'
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
        <p>Tidak ada data penjualan untuk cabang <?= $cabangStock2 ?>bulan <?= $bulan[$bln_stock2] ?? '' ?> <?= $thn_stock2 ?></p>
    </div>
<?php endif; ?>