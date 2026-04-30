<div class="clearfix"></div>
<?php
// Default values
$thn_monthly = !empty($this->input->post('thn_monthly')) ? $this->input->post('thn_monthly') : date('Y');
$thn_branch = !empty($this->input->post('thn_branch')) ? $this->input->post('thn_branch') : date('Y');
$bln_branch = !empty($this->input->post('bln_branch')) ? $this->input->post('bln_branch') : date('m');
$idcabang_monthly = !empty($this->input->post('idcabang_monthly')) ? $this->input->post('idcabang_monthly') : 4;

// Check which form was submitted
$filter_monthly_submitted = !empty($this->input->post('filter_monthly'));


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

<div id="home" class="d-flex flex-column h-100">
    <div class="wrapper d-flex flex-grow-1">
        <div id="content" class="p-0 flex-grow-1">
            <div class="container-fluid h-100">
                <div class="row h-100">
                    <div class="col-12 mt-3 h-100">
                        <div class="card card-rounded h-100">
                            <div class="card-header bg-primary text-white">
                                <i class="fa fa-dashboard mr-1"></i> Grafik Penjualan Cabang
                            </div>
                            <div class="card-body pl-4 pr-4">

                                <!-- Chart 2: Sales by Month -->
                                <div class="row mb-4">
                                    <div class="col-12 border rounded-lg p-3">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5>Penjualan per Bulan</h5>
                                            <form method="post" action="<?= base_url('adminkasir/grafik_penjualan_cabang') ?>" class="form-inline" id="monthly-filter-form">
                                                <div class="d-flex align-items-center">
                                                    <input type="hidden" name="thn_branch" value="<?= $thn_branch ?>">
                                                    <input type="hidden" name="bln_branch" value="<?= $bln_branch ?>">
                                                    <div class="mr-2">
                                                        <select name="idcabang_monthly" class="form-control form-control-sm">
                                                            <!-- <option value="0">- Semua Cabang -</option> -->
                                                            <?php

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


    });
</script>