<div class="clearfix"></div>
<?php
// $thn_monthly = !empty($this->input->post('thn_monthly')) ? $this->input->post('thn_monthly') : date('Y');
// $thn_branch = !empty($this->input->post('thn_branch')) ? $this->input->post('thn_branch') : date('Y');
// $bln_branch = !empty($this->input->post('bln_branch')) ? $this->input->post('bln_branch') : date('m');
$idcabang = !empty($this->input->get('idcabang')) ? $this->input->get('idcabang') : 4;
?>
<div id="home">
    <div class="container-fluid mt-2">


        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary btn-md mt-2" data-toggle="modal" data-target="#modelIdFilter">
            <i class="fa fa-search"></i> Pencarian
        </button>
        <!-- <a href="<?= $urlexcel; ?>" class="btn btn-success mt-2 btn-md ml-1">
            <i class="fa fa-download"></i> File Excel
        </a> -->

        <?php if ($this->input->get('a')) { ?>
            <!-- <a href="<?= $urlexcel; ?>&cetak=print" target="_blank" class="btn btn-primary mt-2 btn-md ml-1">
                <i class="fa fa-print"></i> Cetak
            </a> -->
        <?php } else { ?>
            <!-- <a href="<?= $urlexcel; ?>?cetak=print" target="_blank" class="btn btn-primary mt-2 btn-md ml-1">
                <i class="fa fa-print"></i> Cetak
            </a> -->
        <?php } ?>
        <a href="<?php if ($this->session->userdata('ses_level') == 'Admin') {
                        echo base_url('laporan');
                    } else {
                        echo base_url('laporan?kasir=' . $this->session->userdata('ses_id'));
                    } ?>" class="btn btn-warning mt-2 btn-md ml-1">
            <i class="fa fa-refresh"></i> Refresh
        </a>

        <div class="clearfix"></div>
        <br>
        <?php
        if (!empty($this->session->flashdata('success'))) {
            echo alert_success($this->session->flashdata('success'));
        }
        if (!empty($this->session->flashdata('failed'))) {
            echo alert_failed($this->session->flashdata('failed'));
        }
        ?>
        <div class="card card-rounded">
            <div class="card-header bg-primary text-white">
                <i class="fa fa-cubes"></i> <?= $title_web; ?>
                <?= $periode; ?>
            </div>
            <div class="card-body">
                <!-- <form method="GET" action="<?= base_url('laporan'); ?>">



                </form> -->
                <div class="table-responsive">
                    <table id="example1" class="table table-bordered table-sm table-striped table" width="100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Cabang</th>
                                <th>No Bon</th>
                                <th>Atas Nama</th>
                                <th>Shift</th>
                                <th>Kasir</th>
                                <th>Tanggal</th>
                                <!-- <th>Jenis Order</th> -->
                                <th>Status</th>
                                <th>Qty</th>
                                <!-- <th>Grand Modal</th> -->
                                <th>Jumlah</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <th colspan="8">Total</th>
                                <th><?= $total->qty; ?></th>
                                <!-- <th>Rp<?= number_format($total->gm ?? 0); ?>,-</th> -->
                                <th>Rp<?= number_format($total->gr ?? 0); ?></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modelIdFilter" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-search"></i> Pencarian Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="GET" action="<?= base_url('laporan'); ?>">
                <div class="modal-body">
                    <?php if (in_array($this->session->userdata('ses_level'), array('Admin', 'AdminKasir', 'SuperAdmin'))) { ?>

                        <div class="form-group">
                            <label for="">Cabang</label>

                            <!-- <select class="form-control" name="cabang" onchange="this.form.submit()"> -->
                            <!-- <select class="form-control" name="cabang">
                                <option value="all" selected>- Semua Cabang -</option>
                                <?php
                                $cabang = $this->db->get_where('cabang', 'id>1')->result();
                                foreach ($cabang as $c) {
                                ?>
                                    <option value="<?= $c->id; ?>"><?= $c->kode_cabang; ?></option>
                                <?php } ?>
                            </select> -->
                            <select name="idcabang" class="form-control">
                                <!-- <option value="0">- Semua Cabang -</option> -->
                                <?php
                                $this->db->order_by('length(nama_toko),nama_toko', 'asc');
                                // $namacabang = $this->db->get_where('profil_toko', 'id<>1')->result();
                                $namacabang = $this->db->where('id <> 1 AND cabang_id <> 99')
                                    ->get('profil_toko')
                                    ->result();
                                foreach ($namacabang as $r) {
                                ?>
                                    <option value="<?= $r->cabang_id; ?>" <?= ($idcabang == $r->cabang_id) ? 'selected' : '' ?>>
                                        <?= $r->nama_toko; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    <?php } ?>
                    <div class="form-group">
                        <label for="">Shift <small class="text-danger mr-2">( opsional )</small></label>
                        <select class="form-control" name="shift">
                            <option value="" selected>- pilih -</option>
                            <?php
                            $shift = $this->db->get('shift')->result();
                            foreach ($shift as $r) {
                                if ($this->session->userdata('ses_level') == 'Admin') {
                            ?>
                                    <option value="<?= $r->id; ?>"><?= $r->nama . ' (' . $r->open . '-' . $r->close . ')'; ?></option>
                                    <?php } else {
                                    if ($this->session->userdata('ses_level') != 'Admin') { ?>
                                        <option value="<?= $r->id; ?>"><?= $r->nama . ' (' . $r->open . '-' . $r->close . ')'; ?></option>
                            <?php }
                                }
                            } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Tanggal Start</label>
                        <input type="date" class="form-control" required value="<?= $this->input->get('a') ?>" name="a" placeholder="">
                    </div>
                    <div class="form-group">
                        <label for="">Tanggal End</label>
                        <input type="date" class="form-control" required value="<?= $this->input->get('b') ?>" name="b" placeholder="">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Cari</button>
                </div>
            </form>
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


if (in_array($this->session->userdata('ses_level'), array('Admin', 'AdminKasir', 'SuperAdmin'))) {
    if ($this->input->get('shift')) {
        $shift_id = $this->input->get('shift');

        if (!empty($this->input->get('a'))) {
            $url = base_url('laporan/data_order?shift=' . $shift_id . '&a=' . $this->input->get('a') . '&b=' . $this->input->get('b') . '&idcabang=' . $this->input->get('idcabang'));
        } else {
            $url = base_url('laporan/data_order?shift=' . $shift_id . '&idcabang=' . $this->input->get('idcabang'));
        }
    } else if (!empty(htmlentities($this->input->get('periode', true)))) {
        if ($this->input->get('periode')) {
            $url = base_url('laporan/data_order?periode=' . $this->input->get('periode') . '&idcabang=' . $this->input->get('idcabang'));
        }
    } else {
        if (!empty($this->input->get('a'))) {
            $url = base_url('laporan/data_order?a=' . $this->input->get('a') . '&b=' . $this->input->get('b') . '&idcabang=' . $this->input->get('idcabang'));
        } else {
            $url = base_url('laporan/data_order?idcabang=' . $this->input->get('idcabang'));
        }
    }
    // } else if ($this->session->userdata('ses_level') == 'AdminKasir') {
    //     if ($this->input->get('cabang')) {
    //     } else {
    //         $url = base_url('laporan/data_order');
    //     }
} else {
    $kasir_id = $this->session->userdata('ses_level');

    if ($this->input->get('shift')) {
        $shift_id = $this->input->get('shift');

        if (!empty($this->input->get('a'))) {
            $url = base_url('laporan/data_order?kasir=' . $kasir_id . '&shift=' . $shift_id . '&a=' . $this->input->get('a') . '&b=' . $this->input->get('b'));
        } else {
            $url = base_url('laporan/data_order?kasir=' . $kasir_id . '&shift=' . $shift_id);
        }
    } else {
        if (!empty($this->input->get('a'))) {
            $url = base_url('laporan/data_order?kasir=' . $kasir_id . '&a=' . $this->input->get('a') . '&b=' . $this->input->get('b'));
        } else {
            $url = base_url('laporan/data_order?kasir=' . $kasir_id);
        }
    }
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
            // "searching": false,
            "processing": true,
            "serverSide": true,
            "responsive": false,
            "ordering": true, // Set true agar bisa di sorting

            "order": [
                [0, 'desc']
            ], // Default sortingnya berdasarkan kolom / field ke 0 (paling pertama)
            "ajax": {
                "url": "<?= $url; ?>", // URL file untuk proses select datanya
                "data": function(d) {

                },
                "type": "POST"
            },
            "deferRender": true,
            "aLengthMenu": [
                [10, 25, 50, 100, 150],
                [10, 25, 50, 100, 150]
            ], // Combobox Limit
            "columns": [{
                    "data": 'id',
                    "sortable": false,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    'data': 'nama_toko'
                },
                {
                    'data': 'no_bon'
                },
                {
                    'data': 'atas_nama'
                },
                {
                    // "data": 'shift_id'

                    "data": "shift_id",
                    "render": function(data, type, row, meta) {
                        // return "<?= $url; ?>"
                        if (row.shift_id == 0) {
                            return '-';
                        } else if (row.shift_id == 1) {
                            return 'PAGI';
                        } else if (row.shift_id == 2) {
                            return 'SORE';
                        } else if (row.shift_id == 3) {
                            return 'MALAM';
                        } else {
                            return '';
                        }
                    }
                },
                {
                    'data': 'nama_user'
                },
                {
                    'data': 'date'
                },
                // {
                // "render": function(data, type, row, meta) {
                //     return "<?= $url; ?>"

                // }
                // "data": "pesanan",
                // "render": function(data, type, row, meta) {
                //     if (row.pesanan == 'Ditempat') {
                //         return '<span class="badge badge-primary"><i class="fa fa-home"> </i> ' +
                //             row.pesanan +
                //             '</span>';
                //     }
                //     if (row.pesanan == 'Delivery') {
                //         return '<span class="badge badge-success"><i class="fa fa-motorcycle"> </i> ' +
                //             row.pesanan +
                //             '</span>';
                //     }
                //     if (row.pesanan == 'Booking') {
                //         return '<span class="badge badge-warning"><i class="fa fa-ticket"> </i> ' +
                //             row.pesanan +
                //             '</span>';
                //     }
                // }
                // },
                {
                    "data": "status",
                    "render": function(data, type, row, meta) {
                        if (row.status == 'Bayar Nanti') {
                            return '<span class="badge badge-danger"><i class="fa fa-info-circle"> </i> ' +
                                row.status +
                                '</span>';
                        } else if (row.status == 'Cash') {
                            return '<span class="badge badge-success"><i class="fa fa-check"> </i> ' +
                                row.status +
                                '</span>';
                        } else if (row.status == 'QRIS') {
                            return '<span class="badge badge-primary"><i class="fa fa-check"> </i> ' +
                                row.status +
                                '</span>';
                        } else {
                            return '<span class="badge badge-warning"><i class="fa fa-check"> </i> ' +
                                row.status +
                                '</span>';
                        }
                    }
                },
                {
                    'data': 'total_qty'
                },
                // {
                //     data: 'grandmodal',
                //     render: $.fn.dataTable.render.number(',', '.', 0, 'Rp')
                // },
                {
                    data: 'grandtotal',
                    render: $.fn.dataTable.render.number(',', '.', 0, 'Rp'),
                    className: 'text-right'
                },
                {
                    "data": "id",
                    "render": function(data, type, row, meta) {
                        <?php if ($this->session->userdata('ses_level') == 'Admin') { ?>
                            return `<center>
                                    <a href="${base_url}order/view/${row.cabang_id}/${row.id}" 
                                        class="btn btn-info btn-sm" title="Lihat Detail" role="button">
                                        <i class="fa fa-eye"></i> Lihat Detail
                                    </a>
                                </center>`;
                            // return `<center>
                            //         <a href="${base_url}order/edit/${row.id}" 
                            //             class="btn btn-info btn-sm" title="Detail Order" role="button">
                            //             <i class="fa fa-edit"></i> Detail Order
                            //         </a>
                            //         <a href="${base_url}order/hapus/${row.id}" 
                            //             onclick="javascript:return confirm('Apakah data ingin dihapus ?')" 
                            //             class="btn btn-danger btn-sm" title="Detail Order" role="button">
                            //             <i class="fa fa-trash"></i>
                            //         </a>
                            //     </center>`;
                        <?php } else if (in_array($this->session->userdata('ses_level'), array('Admin', 'AdminKasir', 'SuperAdmin'))) { ?>
                            return `<center>
                                    <a href="${base_url}order/view/${row.cabang_id}/${row.id}" 
                                        class="btn btn-info btn-sm" title="Lihat Detail" role="button">
                                        <i class="fa fa-eye"></i> Lihat Detail
                                    </a>
                                </center>`;
                        <?php } else { ?>
                            return `<center>
                                    <a href="${base_url}order/edit/${row.id}" 
                                        class="btn btn-info btn-sm" title="Detail Order" role="button">
                                        <i class="fa fa-edit"></i> Detail Order
                                    </a>
                                </center>`;
                        <?php } ?>
                    }
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

    $(document).ready(function() {

        tabel.draw();


    });
    // $('#table-filter').on('change', function() {
    //     tabel.search(this.value).draw();
    // });
</script>