<?php
// Prepare monthly data
$monthly_data = [];
$has_monthly_data = false;
$cabang = $idcabang_monthly;
$suffix = '';
$tes = 0;
$tes2 = '';

if ($cabang != 0) {
    $caricabang = $this->db->query('SELECT * FROM cabang WHERE id = ? ', [$cabang])->row();
    if ($caricabang) {
        $kode_cabang = $caricabang->kode_cabang;
        $arr_kode_cabang = array("SN1", "SN2", "SN7", "PU");
        $suffix = in_array($kode_cabang, $arr_kode_cabang) ? '' : '_' . $kode_cabang;
    }
}

for ($n = 1; $n <= 12; $n++) {
    $period = $thn_monthly . '-' . str_pad($n, 2, '0', STR_PAD_LEFT);
    // if ($this->session->userdata('ses_level') == 'AdminKasir') {
    if (in_array($this->session->userdata('ses_level'), array('Admin', 'AdminKasir', 'SuperAdmin', 'Koordinator'))) {

        $penjualan = $this->db->query(
            'SELECT SUM(grandtotal) as qty FROM transaksi' . $suffix . ' WHERE cabang_id = ? AND periode LIKE ?',
            [$cabang, $period . '%']
        )->row();
        // } else {
        //     $penjualan = $this->db->query('SELECT SUM(qty) as qty FROM transaksi_produk' . $suffix . ' 
        //         WHERE periode LIKE ? AND kasir_id = ?', [$period . '%', $this->session->userdata('ses_id')])->row();
    }
    $monthly_data[$n] = $penjualan->qty ?? 0;
    $tes = $tes + $monthly_data[$n];
    $tes2 = $tes2 . ' | ' . $period;
    if ($monthly_data[$n] > 0) $has_monthly_data = true;
}

if ($has_monthly_data): ?>
    <canvas id="line-chart" height="180" style="height: 300px;"></canvas>
    <script>
        var linechart = document.getElementById('line-chart');
        var chart = new Chart(linechart, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{
                    label: "Total Transaksi",
                    data: [<?= implode(',', $monthly_data) ?>],
                    borderColor: '#3c73a8',
                    backgroundColor: '#3c73a8',
                    borderWidth: 2,
                }],
            },
            options: {
                responsive: true,
                // --- Bagian yang ditambahkan untuk fungsionalitas klik ---
                onClick: function(e) {
                    // Mendapatkan elemen (bar) yang diklik
                    const activePoints = chart.getElementsAtEventForMode(e, 'nearest', {
                        intersect: true
                    }, true);

                    if (activePoints.length > 0) {
                        const firstPoint = activePoints[0];
                        const labelIndex = firstPoint.index;
                        const datasetIndex = firstPoint.datasetIndex;

                        const label = chart.data.labels[labelIndex];
                        const value = chart.data.datasets[datasetIndex].data[labelIndex];

                        // Mendapatkan bulan dalam format angka (1-12)
                        // Label bulan adalah 'Jan', 'Feb', dst.
                        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
                        const monthNumber = monthNames.indexOf(label) + 1; // 1-based month number

                        // Format periode YYYY-MM
                        const year = <?= json_encode($thn_monthly) ?>;
                        const period = year + '-' + String(monthNumber).padStart(2, '0');

                        // Aksi yang ingin dilakukan saat bar diklik
                        // Contoh: Menampilkan alert dengan detail
                        // alert(`Bar diklik!\nBulan: ${label} (${period})\nTotal Penjualan: ${value}`);

                        // Contoh: Mengarahkan pengguna ke halaman detail penjualan bulan tersebut
                        // Anda dapat mengganti ini dengan fungsi/URL yang sesuai di aplikasi Anda
                        // window.location.href = `halaman_detail_penjualan.php?periode=${period}&cabang=<?= $cabang ?>`;

                        window.location.href = `<?= base_url('laporan'); ?>?periode=${period}&idcabang=<?= $cabang ?>`;

                    }
                },
                // -----------------------------------------------------------
                plugins: {
                    title: {
                        display: true,
                        text: 'Penjualan Tahun <?= $thn_monthly ?>' + (<?= $idcabang_monthly ?> != 0 ? ' - Cabang Terpilih <?= $kode_cabang ?>' : ' - Semua Cabang')
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
    </script>
<?php else: ?>
    <div class="alert alert-warning text-center py-4">
        <i class="fa fa-exclamation-triangle fa-2x mb-2"></i>
        <h5>Data tidak ditemukan </h5>
        <p>Tidak ada data penjualan untuk filter yang dipilih</p>
    </div>
<?php endif; ?>