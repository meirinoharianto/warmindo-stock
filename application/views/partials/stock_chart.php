<?php
// Prepare branch data
$stock_data = [];
$has_stock_data = false;
$cabang = $idcabang_stock;
$suffix = '';
// $namacabang = '';
$caricabang = $this->db->query('SELECT * FROM cabang WHERE id = ?', [$cabang])->row();
if ($caricabang) {
    $kode_cabang = $caricabang->kode_cabang;
    $arr_kode_cabang = array("SN1", "SN2", "SN7");
    $suffix = in_array($kode_cabang, $arr_kode_cabang) ? '' : '_' . $kode_cabang;
}

$this->db->order_by('nama_bahan', 'asc');
// $branches = $this->db->get_where('profil_toko', 'id<>1')->result();
$bahans = $this->db->where('nama_bahan like "%"')
    ->get('bahan')
    ->result();
$bahan_labels = [];
$period_stock = $thn_stock . '-' . $bln_stock;

foreach ($bahans as $bahan) {
    $sql = 'SELECT SUM(jumlah_perubahan*-1) as qty FROM bahan_kartustok' . $suffix . ' 
        WHERE tipe_transaksi LIKE "Penjualan%" AND cabang_id=' . $cabang . ' AND bahan_id=' . $bahan->id . ' AND periode LIKE ?';
    $stock_out = $this->db->query('SELECT SUM(jumlah_perubahan*-1) as qty FROM bahan_kartustok' . $suffix . ' 
        WHERE tipe_transaksi LIKE "Penjualan%" AND cabang_id=' . $cabang . ' AND bahan_id=' . $bahan->id . ' AND periode LIKE ?', [$period_stock . '%'])->row();
    $stock_value = $stock_out->qty ?? 0;
    $stock_data[] = $stock_value;
    $stock_labels[] = $bahan->nama_bahan;
    if ($stock_value > 0) $has_stock_data = true;
}
$jumlahLabel = count($stock_labels); // pastikan $labels tersedia
$minWidth = max(1200, $jumlahLabel * 80);
if ($has_stock_data): ?>
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
                        '#1f3a5f', // navy dark
                        '#14532d', // dark green
                        '#0f4c5c', // teal dark
                        '#7c2d12', // burnt orange
                        '#7f1d1d', // dark red
                        '#374151', // slate dark
                        '#4b5563', // gray medium
                        '#1e293b', // blue gray dark
                        '#3f3f46', // zinc dark
                        '#334155', // slate blue
                        '#1d4ed8', // deep blue
                        '#065f46', // emerald dark
                        '#0e7490', // cyan dark
                        '#92400e', // amber dark
                        '#991b1b' // red deep
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
        <p><?= $sql . $period_stock; ?></p>
        <p>Tidak ada data penjualan untuk bulan <?= $bulan[$bln_stock] ?? '' ?> <?= $thn_stock ?></p>
    </div>
<?php endif; ?>