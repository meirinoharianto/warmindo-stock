<div class="clearfix"></div>

<?php if (!empty($this->input->get('cabang'))) {
    $idcabang = $this->input->get('cabang');
} else {
    $idcabang = 0;
} ?>
<?php if (!empty($this->input->get('shift'))) {
    $idshift = $this->input->get('shift');
} else {
    $idshift = 0;
} ?>


<div id="home" class="d-flex flex-column h-100">
    <div class="wrapper d-flex flex-grow-1">
        <div id="content" class="p-0 flex-grow-1">
            <div class="container-fluid h-100">
                <div class="row h-100">
                    <div class="col-12 mt-3 h-100">
                        <div class="card card-rounded h-100">
                            <div class="card-header bg-primary text-white">
                                <i class="fa fa-book"></i> <?= $title_web; ?>
                                <?= $periode; ?>
                            </div>
                            <div class="card-body pl-4 pr-4">
                                <!-- <div class="card-body text-center"> -->
                                <div class="row">
                                    <div class="col-12 mb-3 border w-100 rounded-lg p-2">

                                        <!-- FORM PENCARIAN -->
                                        <form method="GET" action="<?= base_url('laporan/kartustok_cabang') ?>" class="form-inline">
                                            <div class="d-flex flex-wrap align-items-center">

                                                <?php if (in_array($this->session->userdata('ses_level'), array('Admin', 'AdminKasir'))) { ?>
                                                    <div class="form-group mr-3 mb-2">
                                                        <label class="mr-2">Cabang</label>
                                                        <select name="cabang" class="form-control form-control-sm">
                                                            <option value="1"> PUSAT</option>
                                                            <?php
                                                            $this->db->order_by('length(nama_toko),nama_toko', 'asc');
                                                            $namacabang = $this->db->get_where('profil_toko', 'id<>1')->result();
                                                            foreach ($namacabang as $r) {
                                                            ?>
                                                                <option value="<?= $r->cabang_id; ?>" <?= ($idcabang == $r->cabang_id) ? 'selected' : '' ?>>
                                                                    <?= $r->nama_toko; ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>

                                                <?php } ?>

                                                <div class="form-group mr-3 mb-2">
                                                    <label class="mr-2">Tanggal Start</label>
                                                    <input type="date" class="form-control form-control-sm" required value="<?= $this->input->get('a') ?>" name="a">
                                                    <!-- <input type="text" class="form-control" value="<?= $this->input->get('cabang') ?>" name="cabang" hidden> -->
                                                </div>

                                                <div class="form-group mr-3 mb-2">
                                                    <label class="mr-2">Tanggal End</label>
                                                    <input type="date" class="form-control form-control-sm" required value="<?= $this->input->get('b') ?>" name="b">
                                                </div>

                                                <div class="form-group mb-2">
                                                    <button type="submit" class="btn btn-primary btn-sm mr-2">
                                                        <i class="fa fa-search"></i>
                                                    </button>
                                                    <a href="<?= base_url('laporan/kartustok') ?>" class="btn btn-success btn-sm">
                                                        <i class="fa fa-refresh"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 mb-3 border rounded-lg">
                                        <div class="clearfix"></div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table id="example1" class="table table-bordered table-sm table-striped" width="100%">
                                                    <thead>
                                                        <tr>
                                                            <th class="all">Kode Bahan</th>
                                                            <th class="all">Nama Bahan</th>
                                                            <th class="all">Saldo Awal</th>
                                                            <th class="all">Masuk</th>
                                                            <th class="all">Keluar</th>
                                                            <th class="all">Saldo Akhir</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>

                                                </table>
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

<?php


if (in_array($this->session->userdata('ses_level'), array('Admin', 'AdminKasir'))) {
    if ($this->input->get('cabang')) {
        $cabang_id = $this->input->get('cabang');

        if ($this->input->get('shift')) {
            $shift_id = $this->input->get('shift');

            if (!empty($this->input->get('a'))) {
                $url = base_url('laporan/data_kartustok?cabang=' . $cabang_id . '&shift=' . $shift_id . '&a=' . $this->input->get('a') . '&b=' . $this->input->get('b'));
            } else {
                $url = base_url('laporan/data_kartustok?cabang=' . $cabang_id);
            }
        } else {
            if (!empty($this->input->get('a'))) {
                $url = base_url('laporan/data_kartustok?cabang=' . $cabang_id . '&a=' . $this->input->get('a') . '&b=' . $this->input->get('b'));
            } else {
                $url = base_url('laporan/data_kartustok?cabang=' . $cabang_id);
            }
        }
    } else {
        if ($this->input->get('shift')) {
            $shift_id = $this->input->get('shift');

            if (!empty($this->input->get('a'))) {
                $url = base_url('laporan/data_kartustok?shift=' . $shift_id . '&a=' . $this->input->get('a') . '&b=' . $this->input->get('b'));
            } else {
                $url = base_url('laporan/data_kartustok?shift=' . $shift_id);
            }
        } else {
            if (!empty($this->input->get('a'))) {
                $url = base_url('laporan/data_kartustok?a=' . $this->input->get('a') . '&b=' . $this->input->get('b'));
            } else {
                $url = base_url('laporan/data_kartustok');
            }
        }
    }
} else {
    $kasir_id = $this->session->userdata('ses_level');

    if (!empty($this->input->get('a'))) {
        $url = base_url('laporan/data_kartustok?kasir=' . $kasir_id . '&a=' . $this->input->get('a') . '&b=' . $this->input->get('b'));
    } else {
        $url = base_url('laporan/data_kartustok?kasir=' . $kasir_id);
    }
}


?>
<script>
    var tabel = null;
    var base_url = "<?= base_url(''); ?>";
    $(document).ready(function() {
        $.fn.dataTable.ext.errMode = 'none';
        tabel = $('#example1').DataTable({
            // dom: 'Blfrtip',
            dom: '<"container-fluid"<"row"<"col"B><"col"l><"col"f>>>rtip',
            buttons: [{
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o"></i> Excel', // Add icon if using Font Awesome
                className: 'btn btn-primary btn-sm', // Add your CSS classes here
                exportOptions: {
                    modifier: {
                        page: 'current'
                    }
                }
            }],

            "processing": true,
            "serverSide": true,
            'responsive': true,
            "ordering": true, // Set true agar bisa di sorting
            "order": [
                [0, 'asc']
            ], // Default sortingnya berdasarkan kolom / field ke 0 (paling pertama)
            "ajax": {
                "url": "<?= $url; ?>", // URL file untuk proses select datanya
                "type": "POST"

            },
            "deferRender": true,
            "lengthMenu": [
                [100, 0],
                [100, 'Semua']
            ],

            "columns": [

                {
                    'data': 'kode_bahan'
                },
                {
                    'data': 'nama_bahan'
                },
                {
                    'data': 'awal'
                },
                {
                    'data': 'masuk'
                },
                {
                    'data': 'keluar'
                },
                {
                    'data': 'akhir'
                },
            ]
        });
    });
</script>