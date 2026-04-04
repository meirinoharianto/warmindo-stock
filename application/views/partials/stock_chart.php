<?php
$idbahan_stock = $this->input->post('idbahan_stock');
$idbahan_stock = is_array($idbahan_stock) ? $idbahan_stock : [];

$stock_data[] = $total_qty;

// label + qty
$label_with_qty = $bahan->nama_bahan . ' (' . number_format($total_qty, 0, ',', '.') . ')';
$stock_labels[] = $label_with_qty;

$has_stock_data = false;
$cabang = (int)$idcabang_stock;

$this->db->order_by('nama_bahan', 'asc');
if (!empty($idbahan_stock) && !in_array('0', $idbahan_stock)) {
    $this->db->where_in('id', $idbahan_stock);
}

$bahans = $this->db->get('bahan')->result();

$period_stock = $thn_stock . '-' . $bln_stock;

// ambil daftar cabang
$this->db->where_not_in('kode_cabang', 'PU');
$all_cabang = $this->db->get('cabang')->result();

foreach ($bahans as $bahan) {
    $total_qty = 0;

    // Jika pilih semua cabang
    if ($cabang == 0) {
        foreach ($all_cabang as $cbg) {
            $kode_cabang = $cbg->kode_cabang;
            $arr_kode_cabang = array("SN1", "SN2", "SN7");
            $suffix = in_array($kode_cabang, $arr_kode_cabang) ? '' : '_' . $kode_cabang;

            $table = 'bahan_kartustok' . $suffix;

            // cek tabel ada
            if ($this->db->table_exists($table)) {
                $stock_out = $this->db->query(
                    "SELECT SUM(jumlah_perubahan * -1) as qty
                     FROM {$table}
                     WHERE tipe_transaksi LIKE 'Penjualan%'
                     AND cabang_id = ?
                     AND bahan_id = ?
                     AND periode LIKE ?",
                    [$cbg->id, $bahan->id, $period_stock . '%']
                )->row();

                $total_qty += (float)($stock_out->qty ?? 0);
?>
                <!-- trace hasil  -->
                <!-- <p> <?= $kode_cabang ?> <?= $total_qty ?></p> -->
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
                [$cabang, $bahan->id, $period_stock . '%']
            )->row();

            $total_qty = (float)($stock_out->qty ?? 0);
        }

        $judul_cabang = 'Cabang ' . $kode_cabang;
    }

    $stock_data[] = $total_qty;
    $stock_labels[] = $bahan->nama_bahan;

    if ($total_qty > 0) {
        $has_stock_data = true;
    }
}

$jumlahLabel = count($stock_labels); // pastikan $labels tersedia
$minWidth = max(1200, $jumlahLabel * 80);
if ($has_stock_data): ?>

    <style>
        .stock-chart-scroll {
            width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
        }

        .stock-chart-inner {
            min-width: 1200px;
            /* sesuaikan */
            height: 400px;
        }

        .stock-chart-inner canvas {
            width: 100% !important;
            height: 100% !important;
        }
    </style>

    <div class="stock-chart-scroll">
        <div class="stock-chart-inner" style="min-width: <?= $minWidth ?>px;">
            <canvas id="stock-chart" height="180" style=" height: 300px;"></canvas>
        </div>
    </div>
    <!-- <canvas id="stock-chart" height="180" style="height: 300px;"></canvas> -->
    <script>
        var stockChart = document.getElementById('stock-chart');
        var chart2 = new Chart(stockChart, {
            type: 'bar',
            data: {
                labels: [<?= "'" . implode("','", array_map('addslashes', $stock_labels)) . "'" ?>],
                datasets: [{
                    label: "Keluar",
                    data: [<?= implode(',', $stock_data) ?>],
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
                        text: 'Stok Keluar Bulan <?= $bulan[$bln_stock] ?? '' ?> <?= $thn_stock ?> - Cabang <?= $kode_cabang ?>'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Total Stock Keluar'
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
        <p>Tidak ada data penjualan untuk bulan <?= $bulan[$bln_stock] ?? '' ?> <?= $thn_stock ?></p>
    </div>
<?php endif; ?>