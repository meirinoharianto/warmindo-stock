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
?>

<?php
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

$bahans2 = $this->db->get('menu_utama')->result();

$period_stock2 = $thn_stock2 . '-' . $bln_stock2;

// ambil daftar cabang
$this->db->where_not_in('kode_cabang', 'PU');
$all_cabang2 = $this->db->get('cabang')->result();

$chart_rows2 = [];
foreach ($bahans2 as $bahan2) {
    $total_qty2 = 0;

    // Jika pilih semua cabang
    if ($cabangStock2 == 0) {
        foreach ($all_cabang2 as $cbg2) {
            if ($cbg2->id == -1) {
                continue;
            }

            $kode_cabang2 = $cbg2->kode_cabang;
            $arr_kode_cabang2 = array("SN1", "SN2", "SN7");
            $suffix2 = in_array($kode_cabang2, $arr_kode_cabang2) ? '' : '_' . $kode_cabang2;

            $table2 = 'transaksi_produk' . $suffix2;

            // cek tabel ada
            if ($this->db->table_exists($table2)) {
                $stock2_out = $this->db->query(
                    "SELECT SUM(qty) as qty
                     FROM {$table2}
                     WHERE cabang_id = ?
                     AND kode_menu = ?
                     AND periode LIKE ?",
                    [$cbg2->id, $bahan2->kode_menu, $period_stock2 . '%']
                )->row();

                // START TRACING SQL 
                //                 $sql_debug = "
                // SELECT SUM(qty) as qty
                // FROM {$table2}
                // WHERE cabang_id = '{$cbg2->id}'
                // AND kode_menu = '{$bahan2->kode_menu}'
                // AND periode LIKE '{$period_stock2}%'
                // ";

                //                 echo "<pre>$sql_debug</pre>";
                // END TRACING SQL 

                $total_qty2 += (float)($stock2_out->qty ?? 0);
?>
                <!-- trace hasil  -->
                <!-- <p> <?= $kode_cabang2 ?> <?= $total_qty2 ?></p> -->
    <?php
            }
        }

        $judul_cabang2 = 'Semua Cabang';
    } else {
        if ($cabangStock2 != -1) {

            // seluruh query cabang di sini

            // Jika pilih 1 cabang saja
            $caricabang2 = $this->db->query('SELECT * FROM cabang WHERE id = ?', [$cabangStock2])->row();

            $suffix2 = '';
            $kode_cabang2 = '';

            if ($caricabang2) {
                $kode_cabang2 = $caricabang2->kode_cabang;
                $arr_kode_cabang2 = array("SN1", "SN2", "SN7");
                $suffix2 = in_array($kode_cabang2, $arr_kode_cabang2) ? '' : '_' . $kode_cabang2;
            }

            $table2 = 'transaksi_produk' . $suffix2;

            if ($this->db->table_exists($table2)) {
                // START TRACING SQL 
                //             $sql_debug = " Kedua
                // SELECT SUM(qty) as qty
                // FROM {$table2}
                // WHERE cabang_id = '{$cabangStock2}'
                // AND kode_menu = '{$bahan2->kode_menu}'
                // AND periode LIKE '{$period_stock2}%'
                // ";

                //             echo "<pre>$sql_debug</pre>";
                // END TRACING SQL 

                $stock2_out = $this->db->query(
                    "SELECT SUM(qty) as qty
                 FROM {$table2}
                 WHERE cabang_id = ?
                 AND kode_menu = ?
                 AND periode LIKE ?",
                    [$cabangStock2, $bahan2->kode_menu, $period_stock2 . '%']
                )->row();

                $total_qty2 = (float)($stock2_out->qty ?? 0);
            }

            $judul_cabang2 = 'Cabang ' . $kode_cabang2;
        }
    }

    // $stock_data[] = $total_qty;
    // $stock_labels[] = $bahan->nama_bahan;

    // $stock2_data[] = $total_qty;

    // label + qty
    // $label_with_qty = $bahan->nama . ' - (' . number_format($total_qty, 0, ',', '.') . ')';
    // $stock2_labels[] = $label_with_qty;

    $chart_rows2[] = [
        'nama' => $bahan2->nama,
        'qty'  => (float)$total_qty2
    ];

    if ($total_qty2 > 0) {
        $has_stock2_data = true;
    }
}
usort($chart_rows2, function ($a2, $b2) {
    return $b2['qty'] <=> $a2['qty'];
});

$stock2_data = [];
$stock2_labels = [];

foreach ($chart_rows2 as $row2) {
    $stock2_data[] = $row2['qty'];
    $stock2_labels[] = $row2['nama'] . ' - (' . number_format($row2['qty'], 0, ',', '.') . ')';
}

$jumlahLabel2 = count($stock2_labels); // pastikan $labels tersedia
$minWidth2 = max(1200, $jumlahLabel2 * 80);
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
        <div class="stock2-chart-inner" style="min-width: <?= $minWidth2 ?>px;">
            <canvas id="stock2-chart" height="180" style=" height: 300px;"></canvas>
        </div>
    </div>
    <!-- <canvas id="stock-chart" height="180" style="height: 300px;"></canvas> -->
    <!-- <pre> -->
    <?php
    // print_r($stock2_labels);
    // print_r($stock2_data);
    ?>
    <!-- </pre> -->
    <div class="alert alert-success text-center py-4">
        <i class="fa fa-exclamation-triangle fa-2x mb-2"></i>
        <h5>Data Ditemukan</h5>
        <!-- <p><?= $sql . $period_stock; ?></p> -->
        <p>Ada data penjualan untuk bulan <?= $bulan[$bln_stock2] ?? '' ?> <?= $thn_stock2 ?></p>
    </div>
    <script>

    </script>
<?php else: ?>
    <div class="alert alert-warning text-center py-4">
        <i class="fa fa-exclamation-triangle fa-2x mb-2"></i>
        <h5>Data tidak ditemukan</h5>
        <!-- <p><?= $sql . $period_stock; ?></p> -->
        <p>Tidak ada data penjualan untuk bulan <?= $bulan[$bln_stock2] ?? '' ?> <?= $thn_stock2 ?></p>
    </div>
<?php endif; ?>