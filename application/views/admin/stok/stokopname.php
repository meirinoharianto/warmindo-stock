<?php if (!empty($this->input->get('periode'))) {
    $periode = $this->input->get('periode');
} else {
    $periode = date('Y-m');
} ?>
<?php if (!empty($this->input->get('cabang'))) {
    $idcabang = $this->input->get('cabang');
} else {
    $idcabang = 0;
} ?>
<div class="clearfix"></div>

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
                                        <div class="d-flex justify-content-between align-items-center">

                                            <form method="GET" action="<?= base_url('stok/stokopname') ?>" class="form-inline">
                                                <div class="d-flex flex-wrap align-items-center">

                                                    <?php if (in_array($this->session->userdata('ses_level'), array('Admin', 'AdminKasir'))) { ?>
                                                        <div class="form-group mr-3">
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

                                                    <div class="form-group mr-3">
                                                        <label class="mr-2">Periode </label>
                                                        <input type="month" class="form-control form-control-sm" required value="<?= $periode ?>" name="periode">
                                                    </div>

                                                    <div class="form-group ">
                                                        <button type="submit" class="btn btn-primary btn-sm mr-2">
                                                            <i class="fa fa-search"></i>
                                                        </button>
                                                        <a href="<?= base_url('stok/stokopname') ?>" class="btn btn-success btn-sm">
                                                            <i class="fa fa-refresh"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </form>
                                            <div class="ml-auto">
                                                <a href="<?= base_url('stok/tambah_stokopname'); ?>" class="btn btn-primary btn-sm">
                                                    <i class="fa fa-plus"> </i> Tambah Stok Opname</a>
                                            </div>
                                        </div>
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
                                                            <!-- <th class="all">Tanggal</th> -->
                                                            <th class="all">Periode</th>
                                                            <th class="all">Keterangan</th>
                                                            <th class="all">Tanggal Opname</th>
                                                            <th class="all">Tanggal Input</th>
                                                            <th class="all">Aksi</th>

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
                "url": "<?= base_url('stok/data_stokopname'); ?>", // URL file untuk proses select datanya
                "type": "POST"
            },
            "deferRender": true,
            "aLengthMenu": [
                [10, 25, 50, 100, 150],
                [10, 25, 50, 100, 150]
            ], // Combobox Limit
            "columns": [
                // {
                //     "data": 'id',
                //     "sortable": false,
                //     render: function(data, type, row, meta) {
                //         return meta.row + meta.settings._iDisplayStart + 1;
                //     }
                // },

                {
                    'data': 'kode_cabang'
                },
                {
                    'data': 'periode'
                },
                {
                    'data': 'keterangan'
                },
                {
                    "data": "tanggal_opname",
                    // "render": function(data, type, row) {
                    //     if (!data) return '';
                    //     if (data == '0000-00-00 00:00:00') return 'Belum diterima';
                    //     const date = new Date(data);
                    //     return ('0' + date.getDate()).slice(-2) + '-' +
                    //         ('0' + (date.getMonth() + 1)).slice(-2) + '-' +
                    //         date.getFullYear();
                    // }
                },
                {
                    "data": "tanggal_input",
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
<a href="${base_url}stok/detail_stokopname/${row.id}" 
                                                class="dropdown-item" title="Lihat Detail" role="button">
                                                <i class="fa fa-eye mr-1"></i> Lihat Detail
                                            </a>

                                            
                                            
                                        </div>
                                    </div>
                                    `;
                        //                                     <a href="${base_url}stok/edit_stokopname/${row.id}" 
                        //                                                 class="dropdown-item" title="Edit Stok Opname" role="button">
                        //                                                 <i class="fa fa-edit mr-1"></i> Edit Stok Opname
                        //                                             </a>
                        // <a href="#" 
                        //                         class="dropdown-item btn-delete-stokopname" 
                        //                         data-id="${row.id}" 
                        //                         title="Hapus Stok Opname" role="button">
                        //                         <i class="fa fa-times mr-1"></i> Hapus Stok Opname
                        //                                             </a>


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