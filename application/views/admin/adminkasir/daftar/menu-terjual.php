<div class="clearfix"></div>
<?php
$thn_sales = !empty($this->input->post('thn_sales')) ? $this->input->post('thn_sales') : date('Y');
$bln_sales = !empty($this->input->post('bln_sales')) ? $this->input->post('bln_sales') : date('m');
// $idcabang_sales = !empty($this->input->post('idcabang_sales')) ? $this->input->post('idcabang_sales') : -1;
$idbahan_sales = !empty($this->input->post('idbahan_sales')) ? $this->input->post('idbahan_sales') : 0;
$idcabang_sales = !empty($this->input->post('idcabang_sales'))
    ? $this->input->post('idcabang_sales')
    : 'all';

// Check which form was submitted
$filter_sales_submitted = !empty($this->input->post('filter_sales'));

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
                                <i class="fa fa-dashboard mr-1"></i> Daftar Penjualan Menu
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

                                                <div class="col-md-2 mb-2 d-flex align-items-end">
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

        $('#sales-filter-form').submit(function(e) {

            e.preventDefault();

            let wrapper = $('#table-loading-wrapper');

            wrapper.html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary"></div>
                    <div class="mt-2">
                        Memuat data...
                    </div>
                </div>
            `);

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),

                success: function(response) {

                    let html = $(response)
                        .find('#sales-table-container')
                        .html();

                    $('#table-loading-wrapper').html(`
                        <div id="sales-table-container">
                            ${html}
                        </div>
                    `);

                },

                error: function() {

                    $('#table-loading-wrapper').html(`
                        <div class="alert alert-danger">
                            Gagal memuat data
                        </div>
                    `);

                }

            });

        });

    });
</script>