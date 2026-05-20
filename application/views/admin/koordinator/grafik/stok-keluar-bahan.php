<div class="clearfix"></div>
<?php

$thn_stock = !empty($this->input->post('thn_stock')) ? $this->input->post('thn_stock') : date('Y');
$bln_stock = !empty($this->input->post('bln_stock')) ? $this->input->post('bln_stock') : date('m');
$idcabang_stock = !empty($this->input->post('idcabang_stock')) ? $this->input->post('idcabang_stock') : -1;
$idbahan_stock = !empty($this->input->post('idbahan_stock')) ? $this->input->post('idbahan_stock') : 0;

$filter_stock_submitted = !empty($this->input->post('filter_stock'));


$this->db->order_by('length(nama_toko),nama_toko', 'asc');

if (!empty($list_cabang)) {
    $this->db->where_in('cabang_id', $list_cabang);
} else {
    $this->db->where('1=1');
}
// $namacabang = $this->db->get_where('profil_toko', 'id<>1')->result();
$namacabang = $this->db->where('id <> 1 AND cabang_id <> 99')
    ->get('profil_toko')
    ->result();

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
?>

<div id="adminkasir" class="d-flex flex-column h-100">
    <div class="wrapper d-flex flex-grow-1">
        <div id="content" class="p-0 flex-grow-1">
            <div class="container-fluid h-100">
                <div class="row h-100">
                    <div class="col-12 mt-3 h-100">
                        <div class="card card-rounded h-100">
                            <div class="card-header bg-primary text-white">
                                <i class="fa fa-dashboard mr-1"></i> Grafik Stok Keluar Bahan
                            </div>
                            <div class="card-body pl-4 pr-4">

                                <!-- Chart 3: Stock Chart by Branch by Month-->
                                <div class="row mb-4">
                                    <div class="col-12 border rounded-lg p-3">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5>Stok Keluar Bahan per Cabang per Bulan</h5>
                                            <form method="post" action="<?= base_url('koordinator/grafik_stok_keluar_bahan') ?>" class="form-inline" id="stock-filter-form">
                                                <div class="d-flex align-items-center">
                                                    <input type="hidden" name="thn_stock" value="<?= $thn_stock ?>">
                                                    <div class="mr-2">
                                                        <select name="idcabang_stock" class="form-control form-control-sm">
                                                            <option value="all">- Semua Cabang -</option>
                                                            <?php

                                                            foreach ($namacabang as $r) {
                                                            ?>
                                                                <option value="<?= $r->cabang_id; ?>" <?= ($idcabang_stock == $r->cabang_id) ? 'selected' : '' ?>>
                                                                    <?= $r->nama_toko; ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="mr-2">
                                                        <select name="bln_stock" class="form-control form-control-sm">
                                                            <?php

                                                            foreach ($bulan as $key => $value) {
                                                            ?>
                                                                <option value="<?= $key ?>" <?= ($bln_stock == $key) ? 'selected' : '' ?>>
                                                                    <?= $value ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="mr-2">
                                                        <select name="thn_stock" class="form-control form-control-sm">
                                                            <?php
                                                            $thn_skr = date('Y');
                                                            for ($x = $thn_skr; $x >= 2021; $x--) {
                                                            ?>
                                                                <option value="<?= $x; ?>" <?= ($thn_stock == $x) ? 'selected' : '' ?>>
                                                                    <?= $x; ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="mr-2">
                                                        <select name="idbahan_stock[]" class="form-control form-control-sm" multiple size="6">
                                                            <option value="0">- Semua Bahan -</option>
                                                            <?php
                                                            $this->db->order_by('nama_bahan', 'asc');
                                                            $namabahan = $this->db->get('bahan')->result();
                                                            foreach ($namabahan as $n) {
                                                            ?>
                                                                <option value="<?= $n->id; ?>"
                                                                    <?= (isset($idbahan_stock) && in_array($n->id, (array)$idbahan_stock)) ? 'selected' : '' ?>>
                                                                    <?= $n->nama_bahan; ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <button type="submit" name="filter_stock" class="btn btn-primary btn-sm">
                                                        <i class="fa fa-filter"></i> Filter
                                                    </button>
                                                </div>
                                            </form>
                                        </div>

                                        <div id="chart-wrapper" class="chart-loading-box">
                                            <div class="chart-loading-overlay">
                                                <div class="chart-loading-spinner"></div>
                                                <div class="chart-loading-text">Memuat grafik...</div>
                                            </div>

                                            <div id="stock-chart-container">
                                                <?php
                                                // Load branch chart partial view
                                                $this->load->view('partials/stock_chart', [
                                                    'thn_stock' => $thn_stock,
                                                    'bln_stock' => $bln_stock,
                                                    'idcabang_stock' => $idcabang_stock,
                                                    'filter_stock_submitted' => $filter_stock_submitted
                                                ]);
                                                ?>
                                            </div>
                                        </div>


                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {

        $('#stock-filter-form').submit(function(e) {
            var wrapper = $('#chart-wrapper');
            var result = $('#stock-chart-container');

            wrapper.addClass('loading');

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                success: function(res) {
                    // result.html(res);
                    // Extract just the branch chart container content from the response
                    var newContent = $(response).find('#stock-chart-container').html();
                    $('#stock-chart-container').html(newContent);
                },
                error: function() {
                    result.html(`
                <div class="alert alert-danger text-center py-3">
                    Gagal memuat grafik.
                </div>
            `);
                },
                complete: function() {
                    wrapper.removeClass('loading');
                }
            });
        });

    });
</script>