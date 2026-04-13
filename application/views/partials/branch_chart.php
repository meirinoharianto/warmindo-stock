<?php
// Prepare branch data

$branch_data = [];
$has_branch_data = false;
$this->db->order_by('length(nama_toko),nama_toko', 'asc');
// $branches = $this->db->get_where('profil_toko', 'id<>1')->result();
if (!empty($list_branch)) {
    $this->db->where_in('cabang_id', $list_branch);
} else {
    $this->db->where('1=1');
}
$branches = $this->db->get('profil_toko')
    ->result();

$branch_labels = [];
$period_branch = $thn_branch . '-' . $bln_branch;

foreach ($branches as $branch) {
    $suffix = '';
    $caricabang = $this->db->query('SELECT * FROM cabang WHERE id = ?', [$branch->cabang_id])->row();
    if ($caricabang) {
        $kode_cabang = $caricabang->kode_cabang;
        $arr_kode_cabang = array("SN1", "SN2", "SN7");
        $suffix = in_array($kode_cabang, $arr_kode_cabang) ? '' : '_' . $kode_cabang;
    }

    $sales = $this->db->query('SELECT SUM(grandtotal) as qty FROM transaksi' . $suffix . ' 
        WHERE cabang_id = ' . $branch->cabang_id . ' AND periode LIKE ?', [$period_branch . '%'])->row();
    $branch_value = $sales->qty ?? 0;
    $branch_data[] = $branch_value;
    $branch_labels[] = $branch->nama_toko;
    if ($branch_value > 0) $has_branch_data = true;
}

if ($has_branch_data): ?>
    <canvas id="branch-chart" height="180" style="height: 300px;"></canvas>
    <script>
        var branchChart = document.getElementById('branch-chart');
        var chart2 = new Chart(branchChart, {
            type: 'bar',
            data: {
                labels: [<?= "'" . implode("','", array_map('addslashes', $branch_labels)) . "'" ?>],
                datasets: [{
                    label: "Total Penjualan",
                    data: [<?= implode(',', $branch_data) ?>],
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
<?php else: ?>
    <div class="alert alert-warning text-center py-4">
        <i class="fa fa-exclamation-triangle fa-2x mb-2"></i>
        <h5>Data tidak ditemukan</h5>
        <p>Tidak ada data penjualan untuk bulan <?= $bulan[$bln_branch] ?? '' ?> <?= $thn_branch ?></p>
    </div>
<?php endif; ?>