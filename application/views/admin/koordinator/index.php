<div class="clearfix"></div>
<?php
// Default values
$thn_monthly = !empty($this->input->post('thn_monthly')) ? $this->input->post('thn_monthly') : date('Y');
$thn_branch = !empty($this->input->post('thn_branch')) ? $this->input->post('thn_branch') : date('Y');
$bln_branch = !empty($this->input->post('bln_branch')) ? $this->input->post('bln_branch') : date('m');
$idcabang_monthly = !empty($this->input->post('idcabang_monthly')) ? $this->input->post('idcabang_monthly') : 4;

// Check which form was submitted
$filter_monthly_submitted = !empty($this->input->post('filter_monthly'));
$filter_branch_submitted = !empty($this->input->post('filter_branch'));


$this->db->order_by('length(nama_toko),nama_toko', 'asc');

// $namacabang = $this->db->where('id <> 1 AND cabang_id <> 99')
//     ->get('profil_toko')
//     ->result();
$this->db->where_not_in('cabang_id', [1, 99]);

if (!empty($list_cabang)) {
    $this->db->where_in('cabang_id', $list_cabang);
} else {
    $this->db->where('1=1');
}

$namacabang = $this->db->get('profil_toko')->result();

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

<div id="koordinator" class="d-flex flex-column h-100">
    <div class="wrapper d-flex flex-grow-1">
        <div id="content" class="p-0 flex-grow-1">
            <div class="container-fluid h-100">
                <div class="row h-100">
                    <div class="col-12 mt-3 h-100">
                        <div class="card card-rounded h-100">
                            <div class="card-header bg-primary text-white">
                                <i class="fa fa-dashboard mr-1"></i> Dashboard Koordinator
                            </div>
                            <div class="card-body pl-4 pr-4">

                                <!-- Chart 1: Sales by Branch -->
                                <div class="row mb-4">
                                    <div class="col-12 border rounded-lg p-3">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5>Penjualan per Cabang</h5>
                                            <form method="post" action="<?= base_url('koordinator') ?>" class="form-inline" id="branch-filter-form">
                                                <div class="d-flex align-items-center">
                                                    <input type="hidden" name="thn_monthly" value="<?= $thn_monthly ?>">
                                                    <input type="hidden" name="idcabang_monthly" value="<?= $idcabang_monthly ?>">
                                                    <div class="mr-2">
                                                        <select name="bln_branch" class="form-control form-control-sm">
                                                            <?php

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
                                                'list_branch' => $list_cabang,
                                                'filter_branch_submitted' => $filter_branch_submitted
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


    });
</script>