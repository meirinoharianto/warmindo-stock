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

$idbahan_sales = $this->input->post('idbahan_sales');
$idbahan_sales = is_array($idbahan_sales)
    ? $idbahan_sales
    : [];

$period_sales = $thn_sales . '-' . $bln_sales;

/*
|--------------------------------------------------------------------------
| CABANG
|--------------------------------------------------------------------------
*/

if ($idcabang_sales != "all") {

    $cabangSales = (int)$idcabang_sales;

    $caricabang2 = $this->db
        ->query(
            "SELECT * FROM cabang WHERE id = ?",
            [$cabangSales]
        )
        ->row();

    $judul_cabang2 = $caricabang2
        ? 'Cabang ' . $caricabang2->kode_cabang
        : 'Cabang';
} else {

    $cabangSales = 0;

    $judul_cabang2 = 'Semua Cabang';
}

/*
|--------------------------------------------------------------------------
| AMBIL SEMUA TABEL TRANSAKSI
|--------------------------------------------------------------------------
*/

$tables = [];

$cabangs = $this->db
    ->where_not_in('kode_cabang', ['PU', 'SN99'])
    ->get('cabang')
    ->result();

foreach ($cabangs as $cbg) {

    if ($cbg->id == -1) {
        continue;
    }

    $arr_kode_cabang2 = ["SN1", "SN2", "SN7"];

    $suffix = in_array($cbg->kode_cabang, $arr_kode_cabang2)
        ? ''
        : '_' . $cbg->kode_cabang;

    $table = 'transaksi_produk' . $suffix;

    if ($this->db->table_exists($table)) {
        $tables[$table] = true;
    }
}

/*
|--------------------------------------------------------------------------
| BUILD UNION QUERY
|--------------------------------------------------------------------------
*/

$unionQueries = [];

foreach (array_keys($tables) as $table) {

    $sql = "
        SELECT
            kode_menu,
            qty,
            cabang_id,
            periode
        FROM {$table}
        WHERE periode LIKE '{$period_sales}%'
    ";

    if ($cabangSales != 0) {
        $sql .= " AND cabang_id = '{$cabangSales}' ";
    }

    $unionQueries[] = $sql;
}

/*
|--------------------------------------------------------------------------
| JIKA TIDAK ADA TABEL
|--------------------------------------------------------------------------
*/

if (empty($unionQueries)) {

    echo '
    <div class="alert alert-warning text-center py-4">
        Data transaksi tidak ditemukan
    </div>
    ';

    return;
}

/*
|--------------------------------------------------------------------------
| FINAL QUERY
|--------------------------------------------------------------------------
*/

$finalUnion = implode(' UNION ALL ', $unionQueries);

$sqlFinal = "
    SELECT
        mu.nama,
        mu.kode_menu,
        SUM(x.qty) as total_qty
    FROM
    (
        {$finalUnion}
    ) x

    JOIN menu_utama mu
        ON mu.kode_menu = x.kode_menu

    WHERE 1=1
";

/*
|--------------------------------------------------------------------------
| FILTER MENU
|--------------------------------------------------------------------------
*/

if (!empty($idbahan_sales) && !in_array('0', $idbahan_sales)) {

    $ids = implode(',', array_map('intval', $idbahan_sales));

    $sqlFinal .= "
        AND mu.id IN ({$ids})
    ";
}

/*
|--------------------------------------------------------------------------
| GROUPING
|--------------------------------------------------------------------------
*/

$sqlFinal .= "
    GROUP BY mu.kode_menu
    HAVING total_qty > 0
    ORDER BY total_qty DESC
";

/*
|--------------------------------------------------------------------------
| EXECUTE
|--------------------------------------------------------------------------
*/

$rows = $this->db->query($sqlFinal)->result();

?>

<style>
    .menu-table-card {
        border-radius: 14px;
        overflow: hidden;
        background: #fff;
    }

    .menu-table-card table {
        margin-bottom: 0;
    }

    .menu-table-card thead th {
        background: #2563eb;
        color: white;
        border: none;
        font-size: 13px;
        white-space: nowrap;
        padding: 14px;
    }

    .menu-table-card tbody td {
        padding: 14px;
        vertical-align: middle;
    }

    .menu-table-card tbody tr:hover {
        background: #f8fafc;
    }

    .qty-badge {
        background: #dbeafe;
        color: #1e40af;
        padding: 6px 14px;
        border-radius: 30px;
        font-weight: 600;
        display: inline-block;
        min-width: 90px;
    }

    .ranking-badge {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #2563eb;
        color: white;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }

    @media(max-width:768px) {

        .menu-table-card table {
            font-size: 12px;
        }

        .menu-table-card thead th,
        .menu-table-card tbody td {
            padding: 10px;
        }
    }

    /* STYLE TABLE  */

    table.dataTable thead th {
        cursor: pointer;
        position: relative;
    }

    table.dataTable thead th:hover {
        background: #1d4ed8 !important;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">

    <div>

        <h5 class="mb-1">
            Penjualan Menu Bulan
            <?= $bulan[$bln_sales] ?? '' ?>
            <?= $thn_sales ?>
        </h5>

        <small class="text-muted">
            <?= $judul_cabang2 ?>
        </small>

    </div>

    <div>

        <span class="badge badge-primary p-2">
            <?= count($rows) ?> Menu
        </span>

    </div>

</div>

<?php if (!empty($rows)) : ?>

    <div class="table-responsive menu-table-card border shadow-sm">

        <table id="menu-sales-table"
            class="table table-hover table-bordered">
            <thead>

                <tr>

                    <th width="5%" class="text-center">
                        #
                    </th>

                    <th>
                        Nama Menu
                    </th>

                    <th width="20%" class="text-center">
                        Qty Terjual
                    </th>

                </tr>

            </thead>

            <tbody>

                <?php
                $no = 1;

                foreach ($rows as $row) :
                ?>

                    <tr>

                        <td class="text-center">

                            <span class="ranking-badge">
                                <?= $no++; ?>
                            </span>

                        </td>

                        <td>
                            <?= $row->nama; ?>
                        </td>
                        <td class="text-center"
                            data-order="<?= $row->total_qty; ?>">

                            <span class="qty-badge">

                                <?= number_format(
                                    $row->total_qty,
                                    0,
                                    ',',
                                    '.'
                                ); ?>

                            </span>

                        </td>


                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    </div>

<?php else : ?>

    <div class="alert alert-warning text-center py-5">

        <i class="fa fa-exclamation-circle fa-3x mb-3"></i>

        <h5>
            Tidak Ada Data Penjualan
        </h5>

        <div>
            Tidak ditemukan transaksi pada periode ini
        </div>

    </div>

<?php endif; ?>

<script>
    function initSalesTable() {

        $('#menu-sales-table').DataTable({
            destroy: true,
            pageLength: 25,
            ordering: true,
            responsive: true,
            autoWidth: false,
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "Semua"]
            ],
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                paginate: {
                    previous: "‹",
                    next: "›"
                },
                zeroRecords: "Data tidak ditemukan",
                emptyTable: "Tidak ada data"
            },
            order: [
                [2, 'desc']
            ]
        });

    }

    $(document).ready(function() {
        initSalesTable();
    });
</script>