<div class="clearfix"></div>
<div id="home">
    <!-- <div class="container mt-5"> -->
    <div class="container-fluid mt-2">
        <!-- <a href="<?= base_url('bahan/tambah'); ?>" class="btn btn-primary">
            <i class="fa fa-plus"> </i> Kartu Stok</a> -->
        <!-- <a href="<?= base_url('menu/import'); ?>" class="btn btn-success mr-2">
            <i class="fa fa-plus"> </i> Import Menu Excel</a> -->
        <div class="clearfix"></div>
        <br>
        <?php
        if (!empty($this->session->flashdata('success'))) {
            echo alert_success($this->session->flashdata('success'));
        }
        if (!empty($this->session->flashdata('failed'))) {
            echo alert_failed($this->session->flashdata('failed'));
        }
        $sql = "SELECT nama_bahan FROM bahan WHERE stok <= stok_minim";
        $cek = $this->db->query($sql)->num_rows();
        if ($cek > 0) {
        ?>
            <!-- <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong>
                    Ada <?= $cek; ?> Bahan yang dibawah Stok minim
                    <a href="<?= base_url('bahan/persediaan?cek=limit'); ?>" class="text-dark mr-2">Cek Disini
                    </a>
                </strong>
            </div> -->
        <?php } ?>
        <div class="card card-rounded">
            <div class="card-header bg-primary text-white">
                <i class="fa fa-cubes"></i> <?= $title_web . ' ' . $detailbahan->nama_bahan; ?>
            </div>
            <div class="card-body">
                <div class="dropdown open mb-3">
                    <button class="btn btn-secondary btn-block dropdown-toggle" type="button" id="triggerId" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php
                        if (!empty($this->input->get('nm'))) {
                            echo $this->input->get('nm');
                        } else {
                            echo 'Semua Cabang';
                        }
                        ?>
                    </button>
                    <div class="dropdown-menu" style="width:100%" aria-labelledby="triggerId">
                        <?php foreach ($cab as $r) { ?>
                            <a class="dropdown-item" href="<?= base_url('bahan/kartustok?idb=' . $detailbahan->id . '&id=' . $r->id . '&nm=' . $r->nama_toko); ?>">
                                <?= $r->nama_toko; ?></a>
                            <div class="dropdown-divider"></div>
                        <?php } ?>
                        <a class="dropdown-item" href="<?= base_url('bahan/kartustok?idb=' . $detailbahan->id); ?>">
                            Semua Cabang</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <?php
                    if ($this->input->get('id')) {
                        $wr = ' WHERE cabang_id = ' . (int)$this->input->get('id') . ' ';
                        $wr = $wr . ' AND bahan_id = ' . (int)$this->input->get('idb') . ' ';
                        $url   = base_url('bahan/data_kartustok?idb=' . $detailbahan->id . '&id=' . (int)$this->input->get('id'));
                    } else {
                        $wr = ' WHERE bahan_id = ' . (int)$this->input->get('idb') . ' ';
                        $url   = base_url('bahan/data_kartustok?idb=' . $detailbahan->id);
                    }
                    ?>
                    <table id="example1" class="table table-bordered table-striped table-sm " width="100%" style="white-space: nowrap;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Cabang</th>
                                <th>Tipe Transaksi</th>
                                <th>Masuk</th>
                                <th>Keluar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var tabel = null;
    var base_url = "<?= base_url(''); ?>";
    $(document).ready(function() {
        $.fn.dataTable.ext.errMode = 'none';
        tabel = $('#example1').DataTable({
            "processing": true,
            "serverSide": true,
            'responsive': true,
            "ordering": true, // Set true agar bisa di sorting
            "order": [
                [0, 'desc']
            ], // Default sortingnya berdasarkan kolom / field ke 0 (paling pertama)
            "ajax": {
                "url": "<?php echo $url; ?>",
                // "url": "<?= base_url('bahan/data_kartustok'); ?>", // URL file untuk proses select datanya
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
                    'data': 'tanggal'
                },
                {
                    'data': 'kode_cabang'
                },
                {
                    'data': 'tipe_transaksi'
                },
                {
                    "data": "jumlah_perubahan",
                    "searchable": false,
                    "orderable": false,
                    "render": function(data, type, row) {
                        if (data >= 0) {
                            return data;
                        } else {
                            return 0;
                        }
                    }
                },
                {
                    "data": "jumlah_perubahan",
                    "searchable": false,
                    "orderable": false,
                    "render": function(data, type, row) {
                        if (data < 0) {
                            return 0 - data;
                        } else {
                            return 0;
                        }
                    }
                }

                // {
                //     'data': 'jumlah_perubahan'
                // },
                // {
                //     'data': 'jumlah_perubahan'
                // }
                // {
                //     "data": "id",
                //     "render": function(data, type, row, meta) {

                //         <?php if ($this->session->userdata('ses_level') == 'Admin') { ?>
                //             return `<div class="dropdown open">
                //                         <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="triggerId" data-toggle="dropdown" aria-haspopup="true"
                //                             aria-expanded="false">
                //                                 <i class="fa fa-cog mr-1"></i> pilih aksi
                //                             </button>
                //                         <div class="dropdown-menu" aria-labelledby="triggerId">
                //                             <a href="${base_url}bahan/detail/${row.id}" 
                //                                 class="dropdown-item" title="Detail Bahan" role="button">
                //                                 <i class="fa fa-eye mr-1"></i> Detail Bahan
                //                             </a>
                //                             <a href="${base_url}bahan/edit/${row.id}" 
                //                                 class="dropdown-item" title="Edit Bahan" role="button">
                //                                 <i class="fa fa-edit mr-1"></i> Edit Bahan
                //                             </a>
                //                             <a href="${base_url}bahan/delete?id=${row.id}" 
                //                                 onclick="javascript:return confirm('Apakah data ini di hapus ?');" 
                //                                 class="dropdown-item" title="Hapus Data Bahan" role="button">
                //                                 <i class="fa fa-times mr-1"></i> Hapus Bahan
                //                             </a>
                //                         </div>
                //                     </div>
                //                     `;
                //         <?php } else { ?>
                //             return `<div class="dropdown open">
                //                     <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="triggerId" data-toggle="dropdown" aria-haspopup="true"
                //                         aria-expanded="false">
                //                             <i class="fa fa-cog mr-1"></i> pilih aksi
                //                         </button>
                //                     <div class="dropdown-menu" aria-labelledby="triggerId">
                //                         <a href="${base_url}menu/detail/${row.id}" 
                //                             class="dropdown-item" title="Detail Menu" role="button">
                //                             <i class="fa fa-eye mr-1"></i> Detail Menu
                //                         </a>
                //                         <a href="${base_url}menu/edit/${row.id}" 
                //                             class="dropdown-item" title="Edit Menu" role="button">
                //                             <i class="fa fa-edit mr-1"></i> Edit Menu
                //                         </a>
                //                         <a href="${base_url}menu/delete?id=${row.id}" 
                //                                 onclick="javascript:return confirm('Apakah data ini di hapus ?');" 
                //                                 class="dropdown-item" title="Hapus Data Menu" role="button">
                //                                 <i class="fa fa-times mr-1"></i> Hapus Menu
                //                             </a>
                //                     </div>
                //                 </div>
                //                 `;
                //         <?php } ?>
                //     }
                // },
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