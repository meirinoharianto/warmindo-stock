<div class="clearfix"></div>
<div id="home">
    <div class="container mt-5">
        <!-- <a href="<?= base_url('bahan/tambah_transferstok'); ?>" class="btn btn-primary">
            <i class="fa fa-plus"> </i> Tambah Transfer Stok Bahan</a> -->
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
                                <!-- <th>Jumlah</th> -->
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
                "url": "<?= base_url('bahan/data_transferstok_terima'); ?>", // URL file untuk proses select datanya
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
                    'data': 'date'
                },
                {
                    'data': 'no_surat'
                },
                {
                    'data': 'kode_cabang_tujuan'
                },
                {
                    'data': 'diterima_tgl'
                },
                {
                    "data": "id",
                    "render": function(data, type, row, meta) {
                        // /${row.id}
                        <?php if ($this->session->userdata('ses_level') == 'Kasir') { ?>
                            return `
                            <a href="${base_url}bahan/transferstok_terima_detail/${row.id}" 
                                                class="btn btn-sm btn-primary" title="Lihat Detail" role="button">
                                                <i class="fa fa-eye mr-1"></i> Lihat Detail
                                            </a>
                                    `;

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
</script>