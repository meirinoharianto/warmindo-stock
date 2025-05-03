<div class="clearfix"></div>
<div id="home">
    <div class="container mt-5">
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
                <i class="fa fa-cubes"></i> <?= $title_web ?>
                <input type="hidden" class="form-control" value="<?= $edit->id; ?>" name="id" id="id" placeholder="" />
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example1" class="table table-bordered table-striped table" width="100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Bahan</th>
                                <th>Nama Bahan</th>
                                <th>Jumlah</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-muted">
                <div class="float-right">

                    <!-- <button type="submit" class="btn btn-primary btn-md">
                            <b><i class="fa fa-save"></i> Simpan</b></button> -->
                    <?php
                    if ($edit->status == 0) {
                    ?>
                        <button class="btn btn-primary saveData" id="saveData"><b><i class="fa fa-save"></i> Terima Stok</b></button>
                    <?php } ?>
                    <a href="<?= base_url('bahan/transferstok_terima'); ?>" class="btn btn-danger btn-md">
                        <b><i class="fa fa-angle-double-left"></i> Kembali</b></a>
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
                "url": "<?= base_url('bahan/data_transferstok_terima_detail?id=' . $edit->id); ?>", // URL file untuk proses select datanya
                // "url": "<?= base_url('bahan/data_transferstok_terima_detail'); ?>", // URL file untuk proses select datanya
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
                    'data': 'kode_bahan'
                },
                {
                    'data': 'nama_bahan'
                },
                {
                    'data': 'jumlah'
                }
                // {
                //     'data': 'kode_cabang_tujuan'
                // },
                // {
                //     'data': 'diterima_tgl'
                // },
                // {
                //     "data": "id",
                //     "render": function(data, type, row, meta) {

                //         <?php if ($this->session->userdata('ses_level') == 'Admin') { ?>
                //             // return `<div class="dropdown open">
                //             //             <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="triggerId" data-toggle="dropdown" aria-haspopup="true"
                //             //                 aria-expanded="false">
                //             //                     <i class="fa fa-cog mr-1"></i> pilih aksi
                //             //                 </button>
                //             //             <div class="dropdown-menu" aria-labelledby="triggerId">
                //             return ` <a href="${base_url}bahan/transferstok_detail/${row.id}" 
                //                                 class="btn btn-primary btn-sm" title="Lihat Detail Transfer" role="button">
                //                                 <i class="fa fa-eye mr-1"></i> Lihat Detail Transfer
                //                             </a>`
                //             //                 <a href="${base_url}bahan/kartustok?idb=${row.id}" 
                //             //                     class="dropdown-item" title="Kartu Stok" role="button">
                //             //                     <i class="fa fa-eye mr-1"></i> Kartu Stok
                //             //                 </a>
                //             //                 <a href="${base_url}bahan/edit/${row.id}" 
                //             //                     class="dropdown-item" title="Edit Bahan" role="button">
                //             //                     <i class="fa fa-edit mr-1"></i> Edit Bahan
                //             //                 </a>
                //             // <a href="${base_url}bahan/delete?id=${row.id}" 
                //             //                     onclick="javascript:return confirm('Apakah data ini di terima ?');" 
                //             //                     class="btn btn-primary btn-sm" title="Lihat Detail Transfer" role="button">
                //             //                     <i class="fa fa-download mr-1"></i> Lihat Detail Transfer
                //             //                 </a>
                //             //     </div>
                //             // </div>
                //             ;
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
                // }

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


        $(document).on('click', '.saveData', function(e) {
            const id = $('#id').val();

            if (!id) {
                alert('Silakan lengkapi data!');
                return;
            }

            e.preventDefault();

            swal.fire({
                title: 'Simpan Terima Stok ! ',
                text: "Apakah anda yakin simpan data ini ? ",
                icon: "warning",
                showDenyButton: true,
                confirmButtonText: 'Ya',
                denyButtonText: 'Tidak',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        // url: `BarangController/deleteTemporaryBarang/${id}`,
                        url: "<?= base_url('bahan/save_terimastok'); ?>",
                        method: 'POST',
                        data: {
                            "id": id,
                        },
                        dataType: 'json',
                        // success: function(data) {
                        //     console.log(data); // Cek di console browser

                        // },
                        success: function(response) {
                            // $('#id').val('');
                            alert(response.message);

                        },
                        error: function(xhr, status, error) {
                            console.error("Terjadi kesalahan:", error);
                        }

                        // error: function(data) {
                        //     alert(data);
                        // }
                        // error: function(xhr, ajaxOptions, thrownError) {
                        //     alert(xhr.status);
                        // },
                        // error: function(response) {
                        //     alert(response.message);
                        // }
                    });
                } else {
                    Swal.fire('Data Batal Disimpan', '', 'error')
                }
            });
        });
    });
</script>