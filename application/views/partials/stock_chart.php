<?php
// Prepare branch data
$stock_data = [];
$has_stock_data = false;
$cabang = $idcabang_stock;
$suffix = '';
$caricabang = $this->db->query('SELECT * FROM cabang WHERE id = ?', [$cabang])->row();

if ($caricabang) {
    $kode_cabang = $caricabang->kode_cabang;
    $arr_kode_cabang = array("SN1", "SN2", "SN7");
    $suffix = in_array($kode_cabang, $arr_kode_cabang) ? '' : '_' . $kode_cabang;
} else {
    $kode_cabang = '';
}

$this->db->order_by('nama_bahan', 'asc');
$bahans = $this->db->where('nama_bahan like "%"')->get('bahan')->result();

$stock_labels = [];
$period_stock = $thn_stock . '-' . $bln_stock;
$sql = '';

foreach ($bahans as $bahan) {
    $sql = 'SELECT SUM(jumlah_perubahan*-1) as qty FROM bahan_kartustok' . $suffix . ' 
        WHERE tipe_transaksi LIKE "Penjualan%" AND cabang_id=' . $cabang . ' AND bahan_id=' . $bahan->id . ' AND periode LIKE ?';

    $stock_out = $this->db->query(
        'SELECT SUM(jumlah_perubahan*-1) as qty FROM bahan_kartustok' . $suffix . ' 
         WHERE tipe_transaksi LIKE "Penjualan%" AND cabang_id=' . $cabang . ' AND bahan_id=' . $bahan->id . ' AND periode LIKE ?',
        [$period_stock . '%']
    )->row();

    $stock_value = $stock_out->qty ?? 0;
    $stock_data[] = $stock_value;
    $stock_labels[] = $bahan->nama_bahan;

    if ($stock_value > 0) {
        $has_stock_data = true;
    }
}

$jumlahLabel = count($stock_labels);
$minWidth = max(1200, $jumlahLabel * 80);

// supaya id canvas tidak bentrok kalau view ini dirender ulang
$chartUniq = 'stock_' . uniqid();
$mainCanvasId = 'stock-chart-' . $chartUniq;
$yCanvasId = 'y-axis-chart-' . $chartUniq;
$scrollId = 'stock-scroll-' . $chartUniq;
?>

<style>
    .stock-chart-wrapper {
        display: flex;
        width: 100%;
        align-items: stretch;
    }

    .stock-chart-y-axis {
        width: 90px;
        min-width: 90px;
        max-width: 90px;
        flex-shrink: 0;
        background: #fff;
        position: sticky;
        left: 0;
        z-index: 2;
    }

    .stock-chart-scroll {
        overflow-x: auto;
        overflow-y: hidden;
        width: 100%;
        -webkit-overflow-scrolling: touch;
    }

    .stock-chart-inner {
        position: relative;
        height: 380px;
    }

    .stock-chart-y-axis canvas {
        width: 90px !important;
        height: 380px !important;
    }

    .stock-chart-inner canvas {
        height: 380px !important;
    }
</style>

<?php if ($has_stock_data): ?>
    <div class="stock-chart-wrapper">
        <div class="stock-chart-y-axis">
            <canvas id="<?= $yCanvasId ?>"></canvas>
        </div>

        <div class="stock-chart-scroll" id="<?= $scrollId ?>">
            <div class="stock-chart-inner" style="min-width: <?= $minWidth ?>px;">
                <canvas id="<?= $mainCanvasId ?>"></canvas>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const labels = [<?= "'" . implode("','", array_map('addslashes', $stock_labels)) . "'" ?>];
            const dataValues = [<?= implode(',', $stock_data) ?>];

            const barColors = [
                '#1f3a5f',
                '#14532d',
                '#0f4c5c',
                '#7c2d12',
                '#7f1d1d',
                '#374151',
                '#4b5563',
                '#1e293b',
                '#3f3f46',
                '#334155',
                '#1d4ed8',
                '#065f46',
                '#0e7490',
                '#92400e',
                '#991b1b'
            ];

            const expandedColors = labels.map((_, i) => barColors[i % barColors.length]);

            const mainCanvas = document.getElementById('<?= $mainCanvasId ?>');
            const yAxisCanvas = document.getElementById('<?= $yCanvasId ?>');

            // destroy chart lama kalau ada
            if (window['chart_main_<?= $chartUniq ?>']) {
                window['chart_main_<?= $chartUniq ?>'].destroy();
            }
            if (window['chart_y_<?= $chartUniq ?>']) {
                window['chart_y_<?= $chartUniq ?>'].destroy();
            }

            // CHART KIRI: hanya untuk Y axis
            window['chart_y_<?= $chartUniq ?>'] = new Chart(yAxisCanvas, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        data: dataValues,
                        backgroundColor: 'rgba(0,0,0,0)',
                        borderColor: 'rgba(0,0,0,0)',
                        borderWidth: 0,
                        barThickness: 30
                    }]
                },
                options: {
                    indexAxis: 'x',
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: false
                        },
                        title: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            display: false,
                            grid: {
                                display: false,
                                drawBorder: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            display: true,
                            title: {
                                display: true,
                                text: 'Total Stock Keluar'
                            },
                            grid: {
                                drawBorder: true
                            },
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });

            // CHART KANAN: chart utama, Y disembunyikan
            window['chart_main_<?= $chartUniq ?>'] = new Chart(mainCanvas, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Keluar',
                        data: dataValues,
                        backgroundColor: expandedColors,
                        borderWidth: 1,
                        barThickness: 30
                    }]
                },
                options: {
                    indexAxis: 'x',
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Stok Keluar Bulan <?= $bulan[$bln_stock] ?? '' ?> <?= $thn_stock ?> - Cabang <?= $kode_cabang ?>'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            display: false,
                            grid: {
                                drawBorder: false
                            },
                            ticks: {
                                precision: 0
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
        })();
    </script>
<?php else: ?>
    <div class="alert alert-warning text-center py-4">
        <i class="fa fa-exclamation-triangle fa-2x mb-2"></i>
        <h5>Data tidak ditemukan</h5>
        <p><?= $sql . $period_stock; ?></p>
        <p>Tidak ada data penjualan untuk bulan <?= $bulan[$bln_stock] ?? '' ?> <?= $thn_stock ?></p>
    </div>
<?php endif; ?>