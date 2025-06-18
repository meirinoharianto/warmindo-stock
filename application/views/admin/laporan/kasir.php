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
                                        <form method="GET" action="<?= base_url('laporan/kasir') ?>" class="form-inline">
                                            <div class="d-flex flex-wrap align-items-center">


                                                <?php if (in_array($this->session->userdata('ses_level'), array('Admin', 'AdminKasir'))) { ?>
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
                                                    <a href="<?= base_url('laporan/pengeluaran') ?>" class="btn btn-success btn-sm">
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
                                                            <th class="all">No</th>
                                                            <th class="all">Cabang</th>
                                                            <th class="all">Tanggal</th>
                                                            <th class="all">Shift</th>
                                                            <th class="all">Kasir</th>
                                                            <th class="all">Total</th>
                                                            <th class="all">QRIS</th>
                                                            <th class="all">Online</th>
                                                            <th class="all">Cash</th>
                                                            <th class="all">Pengeluaran</th>
                                                            <th class="all">Sisa Cash</th>
                                                            <th class="all">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <th colspan="5">Total</th>
                                                            <th>Rp<?= number_format($total->qr + $total->ol + $total->pm ?? 0); ?></th>
                                                            <th>Rp<?= number_format($total->qr ?? 0); ?></th>
                                                            <th>Rp<?= number_format($total->ol ?? 0); ?></th>
                                                            <th>Rp<?= number_format($total->pm ?? 0); ?></th>
                                                            <th>Rp<?= number_format($total->pg ?? 0); ?></th>
                                                            <th>Rp<?= number_format($total->su ?? 0); ?></th>
                                                            <th></th>
                                                        </tr>
                                                    </tfoot>
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
                $url = base_url('laporan/data_closing?cabang=' . $cabang_id . '&shift=' . $shift_id . '&a=' . $this->input->get('a') . '&b=' . $this->input->get('b'));
            } else {
                $url = base_url('laporan/data_closing?cabang=' . $cabang_id);
            }
        } else {
            if (!empty($this->input->get('a'))) {
                $url = base_url('laporan/data_closing?cabang=' . $cabang_id . '&a=' . $this->input->get('a') . '&b=' . $this->input->get('b'));
            } else {
                $url = base_url('laporan/data_closing?cabang=' . $cabang_id);
            }
        }
    } else {
        if ($this->input->get('shift')) {
            $shift_id = $this->input->get('shift');

            if (!empty($this->input->get('a'))) {
                $url = base_url('laporan/data_closing?shift=' . $shift_id . '&a=' . $this->input->get('a') . '&b=' . $this->input->get('b'));
            } else {
                $url = base_url('laporan/data_closing?shift=' . $shift_id);
            }
        } else {
            if (!empty($this->input->get('a'))) {
                $url = base_url('laporan/data_closing?a=' . $this->input->get('a') . '&b=' . $this->input->get('b'));
            } else {
                $url = base_url('laporan/data_closing');
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
        $url = base_url('laporan/data_closing?kasir=' . $kasir_id . '&a=' . $this->input->get('a') . '&b=' . $this->input->get('b'));
    } else {
        $url = base_url('laporan/data_closing?kasir=' . $kasir_id);
    }
    // }
}





// if (!empty($this->input->get('a'))) {
//     if ($this->input->get('shift')) {
//         $url = base_url('laporan/data_order?shift=' . $ks . '&a=' . $this->input->get('a') . '&b=' . $this->input->get('b'));
//     } else {
//         $url = base_url('laporan/data_order?a=' . $this->input->get('a') . '&b=' . $this->input->get('b'));
//     }
// } else {
//     if ($this->input->get('shift')) {
//         if ($this->session->userdata('ses_level') == 'Admin') {
//             $url = base_url('laporan/data_order?shift=' . $ks);
//         } else {
//             // $url = base_url('laporan/data_order?shift=' . $ks);
//             $url = base_url('laporan/data_order?kasir=' . $this->session->userdata('ses_id'));
//         }
//     } else {
//         if ($this->session->userdata('ses_level') == 'Admin') {
//             $url = base_url('laporan/data_order');
//         } else {
//             // $url = base_url('laporan/data_order?shift=' . $ks);
//             $url = base_url('laporan/data_order?kasir=' . $this->session->userdata('ses_id'));
//         }
//     }
// }
// echo $url;
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
            "columns": [{
                    "data": 'id',
                    "sortable": false,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    'data': 'nama_cabang'
                },
                {
                    "data": 'date',
                    "render": function(data) {
                        var date = new Date(data);
                        var month = date.getMonth() + 1;
                        return date.getDate() + "-" + (month.toString().length > 1 ? month : "0" + month) + "-" + date.getFullYear();
                    }
                },
                {
                    'data': 'nama',
                    className: "text-center"
                },
                {
                    'data': 'nama_user'
                },
                {
                    'data': 'total',
                    render: $.fn.dataTable.render.number(',', '.', 0, 'Rp'),
                    className: "text-right"
                },
                {
                    'data': 'qris',
                    render: $.fn.dataTable.render.number(',', '.', 0, 'Rp'),
                    className: "text-right"
                },
                {
                    'data': 'online',
                    render: $.fn.dataTable.render.number(',', '.', 0, 'Rp'),
                    className: "text-right"
                }, {
                    'data': 'pemasukan',
                    render: $.fn.dataTable.render.number(',', '.', 0, 'Rp'),
                    className: "text-right"
                },
                {
                    'data': 'pengeluaran',
                    render: $.fn.dataTable.render.number(',', '.', 0, 'Rp'),
                    className: "text-right"
                },
                {
                    'data': 'sisa_uang',
                    render: $.fn.dataTable.render.number(',', '.', 0, 'Rp'),
                    className: "text-right"
                },
                {
                    "data": "id",
                    "render": function(data, type, row, meta) {

                        <?php if ($this->session->userdata('ses_level') == 'AdminKasir') { ?>
                            return `<div class="dropdown open">
                                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="triggerId" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                                <i class="fa fa-cog mr-1"></i> pilih aksi
                                            </button>
                                        <div class="dropdown-menu" aria-labelledby="triggerId">
                                        <button type="button" class="btn btn-primary btn-sm w-100" data-toggle="modal" data-target="#modelIdUbah${row.id}">
                                        <i class="fa fa-edit mr-1"></i> Ubah Closing
                </button>
                <!-- 
                                            <a href="${base_url}closing/edit/${row.id}" 
                                                class="dropdown-item" title="Ubah Closing" role="button">
                                                <i class="fa fa-edit mr-1"></i> Ubah Closing
                                            </a>
                                            <a href="${base_url}closing/delete?id=${row.id}" 
                                                onclick="javascript:return confirm('Apakah data ini di hapus ?');" 
                                                class="dropdown-item" title="Hapus Data Closing" role="button">
                                                <i class="fa fa-times mr-1"></i> Hapus Closing
                                            </a>
                                            -->
                                        </div>
                                    </div>


                                    <div class="modal fade" id="modelIdUbah${row.id}" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-search"></i> Ubah Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="<?= base_url('closing/ubah'); ?>" enctype="multipart/form-data">
                <div class="modal-body">
                    
                    <div class="form-group">
                    <input type="hidden" class="form-control" value="${row.id}" name="id" id="id"
                                    placeholder="" />
                        <input type="text" class="form-control" value="${row.nama_cabang}" name="cabang" id="cabang" placeholder="" readonly>

                       
                    </div>
                    <!-- 
                    <div class="form-group">
                        <label for="">Total</label>
                        <input type="number" class="form-control" required value="${row.total}" name="total" id="total" placeholder="" readonly>
                    </div>
                    -->
                    <div class="form-group">
                        <label for="">QRIS</label>
                        <input type="number" class="form-control" required value="${row.qris}" name="qris" id="qris" placeholder="">
                    </div>
                    <div class="form-group">
                        <label for="">Online</label>
                        <input type="number" class="form-control" required value="${row.online}" name="online" id="online" placeholder="">
                    </div>
                    <div class="form-group">
                        <label for="">Cash</label>
                        <input type="number" class="form-control" required value="${row.pemasukan}" name="pemasukan" id="pemasukan" placeholder="">
                    </div>
                    <div class="form-group">
                        <label for="">Pengeluaran</label>
                        <input type="number" class="form-control" required value="${row.pengeluaran}" name="pengeluaran" id="pengeluaran" placeholder="">
                    </div>
                    <!-- 
                    <div class="form-group">
                        <label for="">Sisa Cash</label>
                        <input type="number" class="form-control" required value="${row.sisa_uang}" name="sisa_uang" id="sisa_uang" placeholder="" readonly>
                    </div>
                    -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
                                    `;
                        <?php } else { ?>
                            return `
                                `;
                        <?php } ?>
                    }
                }
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