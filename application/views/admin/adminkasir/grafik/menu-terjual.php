<div class="clearfix"></div>
<?php
$thn_stock2 = !empty($this->input->post('thn_stock2')) ? $this->input->post('thn_stock2') : date('Y');
$bln_stock2 = !empty($this->input->post('bln_stock2')) ? $this->input->post('bln_stock2') : date('m');
$idcabang_stock2 = !empty($this->input->post('idcabang_stock2')) ? $this->input->post('idcabang_stock2') : -1;
$idbahan_stock2 = !empty($this->input->post('idbahan_stock2')) ? $this->input->post('idbahan_stock2') : 0;

// Check which form was submitted
$filter_stock2_submitted = !empty($this->input->post('filter_stock2'));


$this->db->order_by('length(nama_toko),nama_toko', 'asc');
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
                                <i class="fa fa-dashboard mr-1"></i> Penjualan Menu per Bulan
                            </div>
                            <div class="card-body pl-4 pr-4">
                                <div class="row mb-4">
                                    <div class="col-12">

                                        <!-- FILTER -->
                                        <form method="post"
                                            action="<?= base_url('adminkasir/daftar_menu_terjual') ?>"
                                            id="sales-filter-form">

                                            <div class="row">

                                                <div class="col-md-3 mb-2">
                                                    <label>Cabang</label>

                                                    <select name="idcabang_sales" class="form-control">

                                                        <option value="all"
                                                            <?= ($idcabang_sales == 'all') ? 'selected' : '' ?>>
                                                            - Semua Cabang -
                                                        </option>

                                                        <?php foreach ($namacabang as $r) : ?>

                                                            <option value="<?= $r->cabang_id; ?>"
                                                                <?= ((string)$idcabang_sales === (string)$r->cabang_id) ? 'selected' : '' ?>>

                                                                <?= $r->nama_toko; ?>

                                                            </option>

                                                        <?php endforeach; ?>

                                                    </select>


                                                </div>

                                                <div class="col-md-2 mb-2">
                                                    <label>Bulan</label>
                                                    <select name="bln_sales" class="form-control">
                                                        <?php foreach ($bulan as $key => $value) : ?>
                                                            <option value="<?= $key ?>"
                                                                <?= ($bln_sales == $key) ? 'selected' : '' ?>>
                                                                <?= $value ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <div class="col-md-2 mb-2">
                                                    <label>Tahun</label>
                                                    <select name="thn_sales" class="form-control">

                                                        <?php
                                                        $thn_skr = date('Y');
                                                        for ($x = $thn_skr; $x >= 2021; $x--) :
                                                        ?>

                                                            <option value="<?= $x; ?>"
                                                                <?= ($thn_sales == $x) ? 'selected' : '' ?>>
                                                                <?= $x; ?>
                                                            </option>

                                                        <?php endfor; ?>

                                                    </select>
                                                </div>

                                                <div class="col-md-3 mb-2">
                                                    <label>Menu</label>

                                                    <select name="idbahan_sales[]"
                                                        class="form-control"
                                                        multiple>
                                                        <option value="0">- Semua Menu -</option>


                                                        <?php
                                                        $this->db->select('id,nama');
                                                        $this->db->order_by('nama', 'asc');
                                                        $namabahan = $this->db->get('menu_utama')->result();

                                                        foreach ($namabahan as $m) :
                                                        ?>

                                                            <option value="<?= $m->id; ?>"
                                                                <?= (isset($idbahan_sales) && in_array($m->id, (array)$idbahan_sales)) ? 'selected' : '' ?>>

                                                                <?= $m->nama; ?>

                                                            </option>

                                                        <?php endforeach; ?>

                                                    </select>
                                                </div>

                                                <div class="col-md-2 mb-2 d-flex align-items-center">
                                                    <button type="submit" name="filter_sales"
                                                        class="btn btn-primary btn-block">

                                                        <i class="fa fa-search mr-1"></i>
                                                        Filter
                                                    </button>
                                                </div>

                                            </div>
                                        </form>

                                        <hr>

                                        <!-- RESULT -->
                                        <div id="table-loading-wrapper">

                                            <div id="sales-table-container">

                                                <?php
                                                $this->load->view('partials/table_menu_terjual', [
                                                    'thn_sales' => $thn_sales,
                                                    'bln_sales' => $bln_sales,
                                                    'idcabang_sales' => $idcabang_sales,
                                                    'idbahan_sales' => $idbahan_sales
                                                ]);
                                                ?>

                                            </div>

                                        </div>


                                    </div>
                                </div>
                                <!-- Chart 4: Menu Sales Chart by Branch by Month-->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <form method="post" action="<?= base_url('adminkasir/grafik_menu_terjual') ?>" class="form-inline" id="stock2-filter-form">

                                            <div class="row">

                                                <div class="col-md-3 mb-2">
                                                    <label>Cabang</label>
                                                    <input type="hidden" name="thn_stock2" value="<?= $thn_stock2 ?>">

                                                    <select name="idcabang_stock2" class="form-control">
                                                        <option value="all">- Semua Cabang -</option>
                                                        <?php

                                                        foreach ($namacabang as $r) {
                                                        ?>
                                                            <option value="<?= $r->cabang_id; ?>" <?= ($idcabang_stock2 == $r->cabang_id) ? 'selected' : '' ?>>
                                                                <?= $r->nama_toko; ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-2 mb-2">
                                                    <label>Bulan</label>
                                                    <select name="bln_stock2" class="form-control ">
                                                        <?php

                                                        foreach ($bulan as $key => $value) {
                                                        ?>
                                                            <option value="<?= $key ?>" <?= ($bln_stock2 == $key) ? 'selected' : '' ?>>
                                                                <?= $value ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-2 mb-2">
                                                    <label>Tahun</label>
                                                    <select name="thn_stock2" class="form-control">
                                                        <?php
                                                        $thn_skr = date('Y');
                                                        for ($x = $thn_skr; $x >= 2021; $x--) {
                                                        ?>
                                                            <option value="<?= $x; ?>" <?= ($thn_stock2 == $x) ? 'selected' : '' ?>>
                                                                <?= $x; ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-3 mb-2">
                                                    <label>Menu</label>
                                                    <select name="idbahan_stock2[]" class="form-control " multiple>
                                                        <option value="0">- Semua Menu -</option>
                                                        <?php
                                                        $this->db->order_by('nama', 'asc');
                                                        $namabahan = $this->db->get('menu_utama')->result();
                                                        foreach ($namabahan as $m) {
                                                        ?>
                                                            <option value="<?= $m->id; ?>"
                                                                <?= (isset($idbahan_stock2) && in_array($m->id, (array)$idbahan_stock2)) ? 'selected' : '' ?>>
                                                                <?= $m->nama; ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-2 mb-2 d-flex align-items-center">
                                                    <button type="submit" name="filter_stock2"
                                                        class="btn btn-primary btn-block">

                                                        <i class="fa fa-search mr-1"></i>
                                                        Filter
                                                    </button>
                                                </div>
                                            </div>
                                        </form>


                                        <div id="chart2-wrapper" class="chart-loading-box">
                                            <div class="chart-loading-overlay">
                                                <div class="chart-loading-spinner"></div>
                                                <div class="chart-loading-text">Memuat grafik...</div>
                                            </div>
                                            <div id="stock2-chart-container">
                                                <?php
                                                // Load branch chart partial view
                                                $this->load->view('partials/stock2_chart', [
                                                    'thn_stock2' => $thn_stock2,
                                                    'bln_stock2' => $bln_stock2,
                                                    'idcabang_stock2' => $idcabang_stock2,
                                                    'filter_stock2_submitted' => $filter_stock2_submitted
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
        // AJAX form submission for monthly filter
        $('#monthly-filter-form').submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    // Extract just the monthly chart container content from the response
                    var newContent = $(response).find('#monthly-chart-container').html();
                    $('#monthly-chart-container').html(newContent);
                }
            });
        });

        // AJAX form submission for branch filter
        $('#branch-filter-form').submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    // Extract just the branch chart container content from the response
                    var newContent = $(response).find('#branch-chart-container').html();
                    $('#branch-chart-container').html(newContent);
                }
            });
        });


        // AJAX form submission for stock filter
        // $('#stock-filter-form').submit(function(e) {
        //     e.preventDefault();
        //     $.ajax({
        //         url: $(this).attr('action'),
        //         type: 'POST',
        //         data: $(this).serialize(),
        //         success: function(response) {
        //             // Extract just the branch chart container content from the response
        //             var newContent = $(response).find('#stock-chart-container').html();
        //             $('#stock-chart-container').html(newContent);
        //         }
        //     });
        // });

        // AJAX form submission for stock filter
        $('#stock2-filter-form').submit(function(e) {
            // e.preventDefault();

            var wrapper = $('#chart2-wrapper');
            var result = $('#stock2-chart-container');

            wrapper.addClass('loading');

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                success: function(res) {
                    // result.html(res);
                    // Extract just the branch chart container content from the response
                    var newContent = $(response).find('#stock2-chart-container').html();
                    $('#stock2-chart-container').html(newContent);
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
        // $('#stock2-filter-form').submit(function(e) {
        //     e.preventDefault();
        //     $.ajax({
        //         url: $(this).attr('action'),
        //         type: 'POST',
        //         data: $(this).serialize(),
        //         success: function(response) {
        //             // Extract just the branch chart container content from the response
        //             var newContent = $(response).find('#stock2-chart-container').html();
        //             $('#stock2-chart-container').html(newContent);
        //         }
        //     });
        // });

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