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
                                        <form method="GET" action="<?= base_url('laporan/kartustok') ?>" class="form-inline">
                                            <div class="d-flex flex-wrap align-items-center">
                                                <div class="form-group mr-3 mb-2">
                                                    <label class="mr-2">Cabang</label>
                                                    <select name="cabang" class="form-control form-control-sm">
                                                        <option value="">- Pilih Cabang -</option>
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

                                                <?php if (in_array($this->session->userdata('ses_level'), array('Admin', 'AdminKasir'))) { ?>
                                                    <div class="form-group mr-3 mb-2">
                                                        <label class="mr-2">Shift <small class="text-danger">(opsional)</small></label>
                                                        <select class="form-control form-control-sm" name="shift">
                                                            <option value="" selected>- pilih -</option>
                                                            <?php $shift = $this->db->get('shift')->result();
                                                            foreach ($shift as $r) {
                                                            ?>
                                                                <option value="<?= $r->id; ?>" <?= ($idshift == $r->id) ? 'selected' : '' ?>><?= $r->nama . ' (' . $r->open . '-' . $r->close . ')'; ?></option>
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
                                                            <th class="all">Cabang</th>
                                                            <th class="all">Tanggal</th>
                                                            <th class="all">Shift</th>
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



<!-- <div id="home"> -->


<!-- Modal -->




<?php

// if ($this->session->userdata('ses_level') == 'Admin') {
//     $ks = $this->input->get('shift');
// } else {
//     $ks = $this->session->userdata('ses_id');
//     // $ks = $this->input->get('shift');
// }


// if ($this->session->userdata('ses_level') == 'Admin') {
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

    // if ($this->input->get('shift')) {
    //     $shift_id = $this->input->get('shift');

    //     if (!empty($this->input->get('a'))) {
    //         $url = base_url('laporan/data_closing?kasir=' . $kasir_id . '&shift=' . $shift_id . '&a=' . $this->input->get('a') . '&b=' . $this->input->get('b'));
    //     } else {
    //         $url = base_url('laporan/data_closing?kasir=' . $kasir_id . '&shift=' . $shift_id);
    //     }
    // } else {
    if (!empty($this->input->get('a'))) {
        $url = base_url('laporan/data_kartustok?kasir=' . $kasir_id . '&a=' . $this->input->get('a') . '&b=' . $this->input->get('b'));
    } else {
        $url = base_url('laporan/data_kartustok?kasir=' . $kasir_id);
    }
    // }
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
            // dom: 'Bfrtip',
            // buttons: [
            //     'excelHtml5'
            // ],

            // "pageLength": all,
            "processing": true,
            "serverSide": true,
            'responsive': true,
            "ordering": true, // Set true agar bisa di sorting
            "order": [
                [0, 'desc']
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
            // "aLengthMenu": [
            //     [10, 25, 50, 100, 150],
            //     [10, 25, 50, 100, 150]
            // ], 
            // Combobox Limit
            "columns": [
                // {
                //     "data": 'id',
                //     "sortable": false,
                //     render: function(data, type, row, meta) {
                //         return meta.row + meta.settings._iDisplayStart + 1;
                //     }
                // },
                {
                    'data': 'cabang'
                },
                {
                    "data": 'tanggal',
                    "render": function(data) {
                        var date = new Date(data);
                        var month = date.getMonth() + 1;
                        return date.getDate() + "-" + (month.toString().length > 1 ? month : "0" + month) + "-" + date.getFullYear();
                    }
                },
                {
                    'data': 'shift_id',
                    render: function(data, type, row) {
                        let shiftText, badgeClass;

                        // Determine text and color based on shift_id
                        switch (data) {
                            case '1':
                                shiftText = 'PAGI';
                                badgeClass = 'bg-success'; // Green for morning
                                break;
                            case '2':
                                shiftText = 'SORE';
                                badgeClass = 'bg-warning text-dark'; // Yellow for afternoon
                                break;
                            case '3':
                                shiftText = 'MALAM';
                                badgeClass = 'bg-dark text-light'; // Dark for night
                                break;
                            default:
                                shiftText = 'PUSAT'; // Fallback to raw value if not 1-3
                                badgeClass = 'bg-secondary text-light';
                        }

                        return `<span class="badge ${badgeClass}">${shiftText}</span>`;
                    }
                },
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
            ],
            "fnDrawCallback": function() {
                $('.portfolio-popup').magnificPopup({
                    type: 'image',
                    removalDelay: 300,
                    mainClass: 'mfp-fade',
                    gallery: {
                        enabled: true
                    },
                    zoom: {
                        enabled: true,
                        duration: 300,
                        easing: 'ease-in-out',
                        opener: function(openerElement) {
                            return openerElement.is('img') ? openerElement : openerElement
                                .find('img');
                        }
                    }
                });
            }
        });
    });
</script>