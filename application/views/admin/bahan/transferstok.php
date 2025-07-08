<div class="clearfix"></div>
<div id="home">
    <div class="container mt-5">
        <a href="<?= base_url('bahan/tambah_transferstok'); ?>" class="btn btn-primary">
            <i class="fa fa-plus"> </i> Tambah Transfer Stok Bahan</a>
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
        // $sql = "SELECT nama_bahan FROM bahan WHERE stok <= stok_minim";
        // $cek = $this->db->query($sql)->num_rows();
        // if ($cek > 0) {
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
        <?php
        //  } 
        ?>
        <div class="card card-rounded">
            <div class="card-header bg-primary text-white">
                <i class="fa fa-cubes"></i> <?= $title_web; ?>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example1" class="table table-bordered table-striped table" width="100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>No Surat</th>
                                <!-- <th>Cabang Asal</th> -->
                                <th>Cabang Tujuan</th>
                                <th>Tgl Diterima</th>
                                <th>Aksi</th>

                                <!-- <th>Jumlah</th> -->
                                <!-- <th>Aksi</th> -->
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
                "url": "<?= base_url('bahan/data_transferstok'); ?>", // URL file untuk proses select datanya
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
                    "data": "date",
                    "render": function(data, type, row) {
                        if (!data) return '';
                        const date = new Date(data);
                        return ('0' + date.getDate()).slice(-2) + '-' +
                            ('0' + (date.getMonth() + 1)).slice(-2) + '-' +
                            date.getFullYear();
                    }
                },
                {
                    'data': 'no_surat'
                },
                {
                    'data': 'kode_cabang_tujuan'
                },
                {
                    "data": "diterima_tgl",
                    "render": function(data, type, row) {
                        if (!data) return '';
                        if (data == '0000-00-00 00:00:00') return 'Belum diterima';
                        const date = new Date(data);
                        return ('0' + date.getDate()).slice(-2) + '-' +
                            ('0' + (date.getMonth() + 1)).slice(-2) + '-' +
                            date.getFullYear();
                    }
                },
                {
                    "data": "id",
                    "render": function(data, type, row, meta) {
                        // <a href="${base_url}bahan/transferstok_detail/${row.id}" 
                        //                                                 class="dropdown-item" title="Detail Transfer Stok" role="button">
                        //                                                 <i class="fa fa-eye mr-1"></i> Detail Transfer Stok
                        //                                             </a>
                        return `<div class="dropdown open">
                                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="triggerId" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                                <i class="fa fa-cog mr-1"></i> pilih aksi
                                            </button>
                                        <div class="dropdown-menu" aria-labelledby="triggerId">

                                            <a href="${base_url}bahan/edit_transferstok/${row.id}" 
                                                class="dropdown-item" title="Edit Transfer Stok" role="button">
                                                <i class="fa fa-edit mr-1"></i> Edit Transfer Stok
                                            </a>
                                            <a href="${base_url}bahan/transferstok_delete?id=${row.id}" 
                                                onclick="javascript:return confirm('Apakah data ini di hapus ?');" 
                                                class="dropdown-item" title="Hapus Transfer Stok" role="button">
                                                <i class="fa fa-times mr-1"></i> Hapus Transfer Stok
                                            </a>
                                        </div>
                                    </div>
                                    `;

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
</script>