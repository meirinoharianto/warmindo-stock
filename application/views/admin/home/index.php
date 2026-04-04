<div class="clearfix"></div>
<?php
// Default values
$thn_monthly = !empty($this->input->post('thn_monthly')) ? $this->input->post('thn_monthly') : date('Y');
$thn_branch = !empty($this->input->post('thn_branch')) ? $this->input->post('thn_branch') : date('Y');
$bln_branch = !empty($this->input->post('bln_branch')) ? $this->input->post('bln_branch') : date('m');
$idcabang_monthly = !empty($this->input->post('idcabang_monthly')) ? $this->input->post('idcabang_monthly') : 4;

$thn_stock = !empty($this->input->post('thn_stock')) ? $this->input->post('thn_stock') : date('Y');
$bln_stock = !empty($this->input->post('bln_stock')) ? $this->input->post('bln_stock') : date('m');
$idcabang_stock = !empty($this->input->post('idcabang_stock')) ? $this->input->post('idcabang_stock') : 0;
$idbahan_stock = !empty($this->input->post('idbahan_stock')) ? $this->input->post('idbahan_stock') : 0;

$thn_product_sales = !empty($this->input->post('thn_product_sales')) ? $this->input->post('thn_product_sales') : date('Y');
$bln_product_sales = !empty($this->input->post('bln_product_sales')) ? $this->input->post('bln_product_sales') : date('m');
$idcabang_product_sales = !empty($this->input->post('idcabang_product_sales')) ? $this->input->post('idcabang_product_sales') : 0;
$idproduct_sales = !empty($this->input->post('idproduct_sales')) ? $this->input->post('idproduct_sales') : 0;

// Check which form was submitted
$filter_monthly_submitted = !empty($this->input->post('filter_monthly'));
$filter_branch_submitted = !empty($this->input->post('filter_branch'));
$filter_stock_submitted = !empty($this->input->post('filter_stock'));
$filter_product_sales_submitted = !empty($this->input->post('filter_product_sales'));
?>

<div id="home" class="d-flex flex-column h-100">
    <div class="wrapper d-flex flex-grow-1">
        <div id="content" class="p-0 flex-grow-1">
            <div class="container-fluid h-100">
                <div class="row h-100">
                    <div class="col-12 mt-3 h-100">
                        <div class="card card-rounded h-100">
                            <div class="card-header bg-primary text-white">
                                <i class="fa fa-bar-chart mr-1"></i> Grafik Penjualan
                            </div>
                            <div class="card-body pl-4 pr-4">

                                <!-- Chart 1: Sales by Month -->
                                <div class="row mb-4">
                                    <div class="col-12 border rounded-lg p-3">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5>Penjualan per Bulan</h5>
                                            <form method="post" action="<?= base_url('home') ?>" class="form-inline" id="monthly-filter-form">
                                                <div class="d-flex align-items-center">
                                                    <input type="hidden" name="thn_branch" value="<?= $thn_branch ?>">
                                                    <input type="hidden" name="bln_branch" value="<?= $bln_branch ?>">
                                                    <div class="mr-2">
                                                        <select name="idcabang_monthly" class="form-control form-control-sm">
                                                            <!-- <option value="0">- Semua Cabang -</option> -->
                                                            <?php
                                                            $this->db->order_by('length(nama_toko),nama_toko', 'asc');
                                                            // $namacabang = $this->db->get_where('profil_toko', 'id<>1')->result();
                                                            $namacabang = $this->db->where('id <> 1 AND cabang_id <> 99')
                                                                ->get('profil_toko')
                                                                ->result();
                                                            foreach ($namacabang as $r) {
                                                            ?>
                                                                <option value="<?= $r->cabang_id; ?>" <?= ($idcabang_monthly == $r->cabang_id) ? 'selected' : '' ?>>
                                                                    <?= $r->nama_toko; ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="mr-2">
                                                        <select name="thn_monthly" class="form-control form-control-sm">
                                                            <?php
                                                            $thn_skr = date('Y');
                                                            for ($x = $thn_skr; $x >= 2021; $x--) {
                                                            ?>
                                                                <option value="<?= $x; ?>" <?= ($thn_monthly == $x) ? 'selected' : '' ?>>
                                                                    <?= $x; ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <button type="submit" name="filter_monthly" class="btn btn-primary btn-sm">
                                                        <i class="fa fa-filter"></i> Filter
                                                    </button>
                                                </div>
                                            </form>
                                        </div>

                                        <div id="monthly-chart-container">
                                            <?php
                                            // Load monthly chart partial view
                                            $this->load->view('partials/monthly_chart', [
                                                'thn_monthly' => $thn_monthly,
                                                'idcabang_monthly' => $idcabang_monthly,
                                                'filter_monthly_submitted' => $filter_monthly_submitted
                                            ]);
                                            ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Chart 2: Sales by Branch -->
                                <div class="row">
                                    <div class="col-12 border rounded-lg p-3">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5>Penjualan per Cabang</h5>
                                            <form method="post" action="<?= base_url('home') ?>" class="form-inline" id="branch-filter-form">
                                                <div class="d-flex align-items-center">
                                                    <input type="hidden" name="thn_monthly" value="<?= $thn_monthly ?>">
                                                    <input type="hidden" name="idcabang_monthly" value="<?= $idcabang_monthly ?>">
                                                    <div class="mr-2">
                                                        <select name="bln_branch" class="form-control form-control-sm">
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
                                                            foreach ($bulan as $key => $value) {
                                                            ?>
                                                                <option value="<?= $key ?>" <?= ($bln_branch == $key) ? 'selected' : '' ?>>
                                                                    <?= $value ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="mr-2">
                                                        <select name="thn_branch" class="form-control form-control-sm">
                                                            <?php
                                                            $thn_skr = date('Y');
                                                            for ($x = $thn_skr; $x >= 2021; $x--) {
                                                            ?>
                                                                <option value="<?= $x; ?>" <?= ($thn_branch == $x) ? 'selected' : '' ?>>
                                                                    <?= $x; ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <button type="submit" name="filter_branch" class="btn btn-primary btn-sm">
                                                        <i class="fa fa-filter"></i> Filter
                                                    </button>
                                                </div>
                                            </form>
                                        </div>

                                        <div id="branch-chart-container">
                                            <?php
                                            // Load branch chart partial view
                                            $this->load->view('partials/branch_chart', [
                                                'thn_branch' => $thn_branch,
                                                'bln_branch' => $bln_branch,
                                                'filter_branch_submitted' => $filter_branch_submitted
                                            ]);
                                            ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Chart 3: Product Sales Chart by Branch by Month-->
                                <div class="row">
                                    <div class="col-12 border rounded-lg p-3">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5>Menu Terjual per Bulan</h5>
                                            <form method="post" action="<?= base_url('home') ?>" class="form-inline" id="product_sales-filter-form">
                                                <div class="d-flex align-items-center">
                                                    <input type="hidden" name="thn_product_sales" value="<?= $thn_product_sales ?>">
                                                    <div class="mr-2">
                                                        <select name="idcabang_product_sales" class="form-control form-control-sm">
                                                            <option value="0">- Semua Cabang -</option>
                                                            <?php
                                                            $this->db->order_by('length(nama_toko),nama_toko', 'asc');
                                                            // $namacabang = $this->db->get_where('profil_toko', 'id<>1')->result();
                                                            $namacabang = $this->db->where('id <> 1 AND cabang_id <> 99')
                                                                ->get('profil_toko')
                                                                ->result();
                                                            foreach ($namacabang as $r) {
                                                            ?>
                                                                <option value="<?= $r->cabang_id; ?>" <?= ($idcabang_product_sales == $r->cabang_id) ? 'selected' : '' ?>>
                                                                    <?= $r->nama_toko; ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="mr-2">
                                                        <select name="bln_product_sales" class="form-control form-control-sm">
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
                                                            foreach ($bulan as $key => $value) {
                                                            ?>
                                                                <option value="<?= $key ?>" <?= ($bln_product_sales == $key) ? 'selected' : '' ?>>
                                                                    <?= $value ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="mr-2">
                                                        <select name="thn_product_sales" class="form-control form-control-sm">
                                                            <?php
                                                            $thn_skr = date('Y');
                                                            for ($x = $thn_skr; $x >= 2021; $x--) {
                                                            ?>
                                                                <option value="<?= $x; ?>" <?= ($thn_product_sales == $x) ? 'selected' : '' ?>>
                                                                    <?= $x; ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="mr-2">
                                                        <select name="idproduct_sales[]" class="form-control form-control-sm" multiple size="6">
                                                            <option value="0">- Semua Menu -</option>
                                                            <?php
                                                            $this->db->order_by('nama', 'asc');
                                                            $namamenu = $this->db->get('menu_utama')->result();
                                                            foreach ($namamenu as $n) {
                                                            ?>
                                                                <option value="<?= $n->id; ?>"
                                                                    <?= (isset($idproduct_sales) && in_array($n->id, (array)$idproduct_sales)) ? 'selected' : '' ?>>
                                                                    <?= $n->nama; ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <button type="submit" name="filter_product_sales" class="btn btn-primary btn-sm">
                                                        <i class="fa fa-filter"></i> Filter
                                                    </button>
                                                </div>
                                            </form>
                                        </div>


                                        <div id="product_sales-chart-container">
                                            <?php
                                            // Load branch chart partial view
                                            $this->load->view('partials/product_sales_chart', [
                                                'thn_product_sales' => $thn_product_sales,
                                                'bln_product_sales' => $bln_product_sales,
                                                'idcabang_product_sales' => $idcabang_product_sales,
                                                'filter_product_sales_submitted' => $filter_product_sales_submitted
                                            ]);
                                            ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Chart 4: Stock Chart by Branch by Month-->
                                <div class="row">
                                    <div class="col-12 border rounded-lg p-3">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5>Stok Keluar Bahan per Cabang per Bulan</h5>
                                            <form method="post" action="<?= base_url('home') ?>" class="form-inline" id="stock-filter-form">
                                                <div class="d-flex align-items-center">
                                                    <input type="hidden" name="thn_stock" value="<?= $thn_stock ?>">
                                                    <div class="mr-2">
                                                        <select name="idcabang_stock" class="form-control form-control-sm">
                                                            <option value="0">- Semua Cabang -</option>
                                                            <?php
                                                            $this->db->order_by('length(nama_toko),nama_toko', 'asc');
                                                            // $namacabang = $this->db->get_where('profil_toko', 'id<>1')->result();
                                                            $namacabang = $this->db->where('id <> 1 AND cabang_id <> 99')
                                                                ->get('profil_toko')
                                                                ->result();
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
        $('#stock-filter-form').submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    // Extract just the branch chart container content from the response
                    var newContent = $(response).find('#stock-chart-container').html();
                    $('#stock-chart-container').html(newContent);
                }
            });
        });
    });
    // AJAX form submission for stock filter
    $('#product_sales-filter-form').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                // Extract just the branch chart container content from the response
                var newContent = $(response).find('#product_sales-chart-container').html();
                $('#product_sales-chart-container').html(newContent);
            }
        });
    });
</script>