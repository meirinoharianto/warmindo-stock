<div class="clearfix"></div>
<div id="home">
    <div class="container-fluid mt-3">
        <div class="row">
            <?php if ($this->session->userdata('ses_level') == 'AdminKasir') {
            ?>
                <div class="col-2">
                    <div class="card card-rounded">
                        <div class="card-header bg-primary text-white">
                            <i class="fa fa-cubes"></i> CABANG
                        </div>
                        <div class="card-body">

                            <a href="<?= base_url('laporan/pengeluaran'); ?>" class="btn btn-info btn-sm mb-1 w-100"> SEMUA CABANG</a>
                            <?php

                            $this->db->order_by('nama_toko', 'asc');

                            $namacabang = $this->db->get_where('profil_toko', 'id<>1')->result();
                            foreach ($namacabang as $r) {
                            ?>
                                <a href="<?= base_url('laporan/pengeluaran?cabang=' . $r->cabang_id); ?>" class="btn btn-info btn-sm mb-1 w-100"><?= $r->nama_toko; ?></a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="col-10">

                <?php } else { ?>
                    <div class="col">
                    <?php } ?>

                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-primary btn-md" data-toggle="modal" data-target="#modelIdFilter">
                        <i class="fa fa-search"></i> Pencarian
                    </button>
                    <!-- <a href="<?= $urlexcel; ?>" class="btn btn-success mt-2 btn-md ml-1">
            <i class="fa fa-download"></i> File Excel
        </a> -->

                    <!-- <?php if ($this->input->get('a')) { ?>
                        <a href="<?= $urlexcel; ?>&cetak=print" target="_blank" class="btn btn-primary btn-md ml-1">
                            <i class="fa fa-print"></i> Cetak
                        </a>
                    <?php } else { ?>
                        <a href="<?= $urlexcel; ?>?cetak=print" target="_blank" class="btn btn-primary btn-md ml-1">
                            <i class="fa fa-print"></i> Cetak
                        </a>
                    <?php } ?> -->
                    <a href="<?php if (in_array($this->session->userdata('ses_level'), array('Admin', 'AdminKasir'))) {
                                    echo base_url('laporan/pengeluaran');
                                } else {
                                    echo base_url('laporan/pengeluaran?kasir=' . $this->session->userdata('ses_id'));
                                } ?>" class="btn btn-warning btn-md ml-1">
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
                            <?= $periode; ?> <?= $cabangpilih; ?>
                        </div>
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
                                            <th class="all">Kategori</th>
                                            <th class="all">Keterangan</th>
                                            <th class="all">No Bon</th>
                                            <th class="all">Jumlah</th>
                                            <th class="all">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="8">Total</th>
                                            <th>Rp<?= number_format($total->jum ?? 0); ?></th>
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
                <form method="GET" action="<?= base_url('laporan/pengeluaran'); ?>">
                    <div class="modal-body">
                        <div class="form-group">

                            <?php
                            // if ($this->session->userdata('ses_level') == 'Admin') {
                            if (in_array($this->session->userdata('ses_level'), array('Admin', 'AdminKasir'))) {

                            ?>
                                <label for="">Shift <small class="text-danger mr-2">( opsional )</small></label>

                                <select class="form-control" name="shift">
                                    <option value="" selected>- pilih -</option>
                                    <?php $shift = $this->db->get('shift')->result();
                                    foreach ($shift as $r) {
                                    ?>
                                        <option value="<?= $r->id; ?>"><?= $r->nama . ' (' . $r->open . '-' . $r->close . ')'; ?></option>
                                    <?php } ?>
                                </select>
                            <?php
                            }
                            ?>

                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" value="<?= $this->input->get('cabang') ?>" name="cabang" placeholder="" hidden>

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


    // if ($this->session->userdata('ses_level') == 'Admin') {
    if (in_array($this->session->userdata('ses_level'), array('Admin', 'AdminKasir'))) {
        if ($this->input->get('cabang')) {
            $cabang_id = $this->input->get('cabang');

            if ($this->input->get('shift')) {
                $shift_id = $this->input->get('shift');

                if (!empty($this->input->get('a'))) {
                    $url = base_url('laporan/data_pengeluaran?cabang=' . $cabang_id . '&shift=' . $shift_id . '&a=' . $this->input->get('a') . '&b=' . $this->input->get('b'));
                } else {
                    $url = base_url('laporan/data_pengeluaran?cabang=' . $cabang_id);
                }
            } else {
                if (!empty($this->input->get('a'))) {
                    $url = base_url('laporan/data_pengeluaran?cabang=' . $cabang_id . '&a=' . $this->input->get('a') . '&b=' . $this->input->get('b'));
                } else {
                    $url = base_url('laporan/data_pengeluaran?cabang=' . $cabang_id);
                }
            }
        } else {
            if ($this->input->get('shift')) {
                $shift_id = $this->input->get('shift');

                if (!empty($this->input->get('a'))) {
                    $url = base_url('laporan/data_pengeluaran?shift=' . $shift_id . '&a=' . $this->input->get('a') . '&b=' . $this->input->get('b'));
                } else {
                    $url = base_url('laporan/data_pengeluaran?shift=' . $shift_id);
                }
            } else {
                if (!empty($this->input->get('a'))) {
                    $url = base_url('laporan/data_pengeluaran?a=' . $this->input->get('a') . '&b=' . $this->input->get('b'));
                } else {
                    $url = base_url('laporan/data_pengeluaran');
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
            $url = base_url('laporan/data_pengeluaran?kasir=' . $kasir_id . '&a=' . $this->input->get('a') . '&b=' . $this->input->get('b'));
        } else {
            $url = base_url('laporan/data_pengeluaran?kasir=' . $kasir_id);
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
                    text: 'Excel',
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
                            return (date.getDate().toString().length > 1 ? date.getDate() : "0" + date.getDate()) + "-" + (month.toString().length > 1 ? month : "0" + month) + "-" + date.getFullYear();
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
                        'data': 'kategori_keluar'
                    },
                    {
                        'data': 'keterangan'
                    },
                    {
                        'data': 'no_bon'
                    },
                    {
                        'data': 'jumlah',
                        render: $.fn.dataTable.render.number(',', '.', 0, 'Rp'),
                        className: "text-right"
                    },

                    {
                        "data": "id",
                        "render": function(data, type, row, meta) {
                            var date = new Date(row.date);
                            var month = date.getMonth() + 1;

                            <?php if ($this->session->userdata('ses_level') == 'AdminKasir') { ?>
                                return `<div class="dropdown open">
                                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="triggerId" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                                <i class="fa fa-cog mr-1"></i> pilih aksi
                                            </button>
                                        <div class="dropdown-menu" aria-labelledby="triggerId">
                                        <button type="button" class="btn btn-primary btn-sm w-100" data-toggle="modal" data-target="#modelIdUbah${row.id}">
                                        <i class="fa fa-edit mr-1"></i> Ubah Pengeluaran
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
            <form method="POST" action="<?= base_url('keuangan/ubah_lappengeluaran'); ?>" enctype="multipart/form-data">
                <div class="modal-body">
                    
                    <div class="form-group">
                    <input type="hidden" class="form-control" value="${row.id}" name="id" id="id"
                                    placeholder="" />
                        <input type="text" class="form-control" value="${row.nama_cabang}" name="cabang" id="cabang" placeholder="" readonly>

                       
                    </div>
                    <div class="form-group">
                        <label for="">Tanggal</label>
                        <input type="text" class="form-control" required value="${(date.getDate().toString().length > 1 ? date.getDate() : "0" + date.getDate()) + "-" + (month.toString().length > 1 ? month : "0" + month) + "-" + date.getFullYear()}" name="tanggal" id="tanggal" placeholder="" readonly>
                    </div>
                    <div class="form-group">
                        <label for="">Shift</label>
                        <input type="text" class="form-control" required value="${row.nama}" name="shift" id="shift" placeholder="" readonly>
                    </div>
                    <div class="form-group">
                        <label for="">Kasir</label>
                        <input type="text" class="form-control" required value="${row.nama_user}" name="kasir" id="kasir" placeholder="" readonly>
                    </div>
                    <div class="form-group">
                   
                        <label for="">Kategori</label>
                        <select class="form-control" name="kategori" id="kategori">
                            <option value="" >- pilih -</option>
                            <?php $kategori = $this->db->get('kategori_keluar')->result();
                                foreach ($kategori as $r) {
                            ?>
                                <option value="<?= $r->kategori_keluar; ?>" ><?= $r->kategori_keluar; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">No Bon</label>
                        <input type="text" class="form-control" name="no_bon" id="no_bon" value="${row.no_bon}" placeholder="">
                    </div>
                    <div class="form-group">
                        <label for="">Keterangan</label>
                        <input type="text" class="form-control" name="keterangan" id="keterangan" value="${row.keterangan}" placeholder="">
                    </div>
                    <div class="form-group">
                        <label for="">Jumlah</label>
                        <input type="number" class="form-control" required value="${row.jumlah}" name="jumlah" id="jumlah" placeholder="">
                    </div>
                    <!-- 
                    <div class="form-group">
                        <label for="">Total</label>
                        <input type="number" class="form-control" required value="${row.total}" name="total" id="total" placeholder="" readonly>
                    </div>
                   
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