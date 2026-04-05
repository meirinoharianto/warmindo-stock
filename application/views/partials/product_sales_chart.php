<?php
$idproduct = $this->input->post('idproduct_sales');
$idproduct = is_array($idproduct) ? $idproduct : [];

$stock_data = [];
$stock_labels = [];
$has_stock_data = false;
$cabang = (int)$idcabang_product_sales;

$this->db->order_by('nama', 'asc');
if (!empty($idproduct) && !in_array('0', $idproduct)) {
    $this->db->where_in('id', $idproduct);
}

$products = $this->db->get('menu_utama')->result();

$period_product_sales = $thn_product_sales . '-' . $bln_product_sales;

// ambil daftar cabang
$this->db->where_not_in('kode_cabang', 'PU');
$all_cabang = $this->db->get('cabang')->result();

foreach ($products as $product) {
    $total_qty = 0;

    // Jika pilih semua cabang
    if ($cabang == 0) {
        foreach ($all_cabang as $cbg) {
            $kode_cabang = $cbg->kode_cabang;
            $arr_kode_cabang = array("SN1", "SN2", "SN7");
            $suffix = in_array($kode_cabang, $arr_kode_cabang) ? '' : '_' . $kode_cabang;

            $table = 'transaksi_produk' . $suffix;

            // cek tabel ada
            if ($this->db->table_exists($table)) {
                $stock_out = $this->db->query(
                    "SELECT SUM(qty) as qty
                     FROM {$table}
                     WHERE cabang_id = ?
                     AND kode_menu = ?
                     AND periode LIKE ?",
                    [$cbg->id, $product->kode_menu, $period_product_sales . '%']
                )->row();

                $total_qty += (float)($stock_out->qty ?? 0);
                // $total_qty = 0;
?>
                <!-- trace hasil  -->

                <p> <?= $cbg->id . '-' . $table . '-' . $product->nama . ' - ' . $kode_cabang ?> <?= $total_qty ?></p>
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

        $table = 'transaksi_produk' . $suffix;

        if ($this->db->table_exists($table)) {
            $stock_out = $this->db->query(
                "SELECT SUM(qty) as qty
                 FROM {$table}
                WHERE cabang_id = ?
                     AND kode_menu = ?
                     AND periode LIKE ?",
                [$cabang, $product->kode_menu, $period_product_sales . '%']
            )->row();

            $total_qty = (float)($stock_out->qty ?? 0);
        }

        $judul_cabang = 'Cabang ' . $kode_cabang;
        ?>
        <p> <?= $cabang . '-' . $product->nama . ' - ' . $kode_cabang ?> <?= $total_qty ?></p>

    <?php
    }

    // $stock_data[] = $total_qty;
    // $stock_labels[] = $bahan->nama_bahan;

    $stock_data[] = $total_qty;

    // label + qty
    $label_with_qty = $product->nama . ' - (' . number_format($total_qty, 0, ',', '.') . ')';
    $stock_labels[] = $label_with_qty;


    if ($total_qty >= 0) {
        $has_stock_data = true;
    }
}

$jumlahLabel = count($stock_labels); // pastikan $labels tersedia
$minWidth = max(1200, $jumlahLabel * 80);
if ($has_stock_data): ?>

    <style>
        .product_sales-chart-scroll {
            width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
        }

        .product_sales-chart-inner {
            min-width: 1200px;
            /* sesuaikan */
            height: 400px;
        }

        .product_sales-chart-inner canvas {
            width: 100% !important;
            height: 100% !important;
        }
    </style>

    <div class="product_sales-chart-scroll">
        <div class="product_sales-chart-inner" style="min-width: <?= $minWidth ?>px;">
            <canvas id="product_sales-chart" height="180" style=" height: 300px;"></canvas>
        </div>
    </div>
    <!-- <canvas id="stock-chart" height="180" style="height: 300px;"></canvas> -->
    <script>
        var stockChart = document.getElementById('product_sales-chart');
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
                        text: 'Menu Terjual Bulan <?= $bulan[$bln_product_sales] ?? '' ?> <?= $thn_product_sales ?> - <?= $judul_cabang ?>'
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
                            text: 'Nama Menu'
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
        <p>Tidak ada data penjualan untuk bulan <?= $bulan[$bln_product_sales] ?? '' ?> <?= $thn_product_sales ?></p>
    </div>
<?php endif; ?>