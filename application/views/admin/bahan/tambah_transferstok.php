<div class="clearfix"></div>
<div id="home">
    <div class="container mt-5">
        <div class="row">
            <!-- <div class="col-sm-7 mx-auto"> -->
            <div class="mx-auto col">
                <?php
                if (!empty($this->session->flashdata('success'))) {
                    echo alert_success($this->session->flashdata('success'));
                }
                if (!empty($this->session->flashdata('failed'))) {
                    echo alert_failed($this->session->flashdata('failed'));
                }
                ?>
                <!-- <form method="POST" action="<?= base_url('bahan/store_stokawal'); ?>" enctype="multipart/form-data"> -->
                <!-- <form id="barangForm"> -->
                <div class="card card-rounded">
                    <div class="card-header bg-primary text-white">
                        <i class="fa fa-plus"></i> <?= $title_web; ?>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="">Tanggal</label>
                                    <input type="date" class="form-control" name="date" id="date"
                                        placeholder="">
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="">Cabang Tujuan</label>
                                    <select class="form-control" name="id_cabang" id="id_cabang">
                                        <option value="" disabled selected>- pilih -</option>
                                        <?php foreach ($cab as $r) { ?>
                                            <option value="<?= $r->id; ?>"><?= $r->nama_toko; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">No Surat</label>
                                    <input type="text" class="form-control" name="no_surat" id="no_surat" placeholder="">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">Keterangan</label>
                                    <textarea class="form-control" name="keterangan" id="keterangan"
                                        placeholder=""></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <a href="<?= base_url('bahan/import_transferstok'); ?>" class="btn btn-success btn-block mb-2">
                                    Import Transfer Stok Excel</a>
                            </div>
                        </div>

                        <form id="barangForm">
                            <div class="row">
                                <div class="form-group col-2">
                                    <label for="kode_bahan">Kode Bahan :</label>
                                    <input type="text" class="form-control" id="id_bahan" hidden>
                                    <input type="text" class="form-control" id="kode_bahan" readonly>
                                </div>
                                <div class="form-group col-6">
                                    <label for="nama_bahan">Nama Bahan :</label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" id="nama_bahan" readonly>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#bahanModal"><i class="fa fa-search"> </i></button>

                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-2">
                                    <label for="quantity">Jumlah :</label>
                                    <input type="number" class="form-control" id="quantity">
                                </div>
                                <div class="form-group col-2">
                                    <label for="quantity">Aksi :</label>
                                    <button class="btn btn-secondary btn-block tambahkan" id="addToTemporaryTable">
                                        <i class="fa fa-plus"> </i>
                                    </button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <!-- <button class="btn btn-secondary btn-sm pt-2 pb-2 btn-block tambahkan" id="addToTemporaryTable">
                                        Tambahkan
                                    </button> -->
                                </div>
                            </div>
                        </form>
                        <div class="row">
                            <div class="col-12">

                                <table class="table table-bordered table-sm" id="temporaryTable">
                                    <thead>
                                        <tr>
                                            <th>Kode Bahan</th>
                                            <th>Nama Bahan</th>
                                            <th>Kuantiti</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data akan dimuat di sini -->
                                    </tbody>
                                </table>
                                <!-- <button type="button" class="btn btn-primary" id="saveData">Simpan</button> -->
                            </div>

                        </div>
                    </div>

                </div>
                <div class="card-footer text-muted">
                    <div class="float-right">

                        <!-- <button type="submit" class="btn btn-primary btn-md">
                            <b><i class="fa fa-save"></i> Simpan</b></button> -->
                        <button class="btn btn-primary saveData" id="saveData"><b><i class="fa fa-save"></i> Simpan</b></button>

                        <button class="btn btn-warning delete-all"><b><i class="fa fa-trash"></i> Hapus Semua</b></button>

                        <a href="<?= base_url('bahan'); ?>" class="btn btn-danger btn-md">
                            <b><i class="fa fa-angle-double-left"></i> Kembali</b></a>
                    </div>
                </div>
            </div>
            <!-- </form> -->

        </div>
    </div>
</div>
</div>

<!-- Modal Pilih Barang -->
<div class="modal fade" id="bahanModal" tabindex="-1" role="dialog" aria-labelledby="bahanModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bahanModalLabel">Daftar Bahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="text" id="search" class="form-control mb-3" placeholder="Cari Bahan">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Kode Bahan</th>
                            <th>Nama Bahan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="bahanTable">
                        <!-- Data akan dimuat di sini -->
                    </tbody>
                </table>
                <div id="barangPagination" class="mt-3"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Notifikasi -->
<div class="modal fade" id="notificationModal" tabindex="-1" role="dialog" aria-labelledby="notificationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notificationModalLabel">Notifikasi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Barang sudah ada di tabel dan tidak bisa ditambahkan lagi.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    function loadBarangTable(page = 1, query = '') {
        const limit = 10; // Jumlah data per halaman
        // function loadBarangTable() {
        $.ajax({
            // url: 'bahan/get_bahan_temp',
            url: "<?= base_url('bahan/get_bahan_temp'); ?>",
            method: 'GET',
            data: {
                search: query,
                page: page,
                limit: limit
            },
            success: function(response) {
                const result = JSON.parse(response);
                const bahan = result.data;
                const totalRows = result.totalRows;
                const perPage = result.perPage;
                const currentPage = result.currentPage;

                let rows = '';
                bahan.forEach(item => {
                    rows += `
                            <tr>
                                <td>${item.kode_bahan}</td>
                                <td>${item.nama_bahan}</td>
                                <td>
                                    <button class="btn btn-success pilih-barang" data-id="${item.id}" data-kode="${item.kode_bahan}" data-nama="${item.nama_bahan}">Pilih</button>
                                </td>
                            </tr>
                        `;
                });
                $('#bahanTable').html(rows);

                // Tampilkan pagination
                generateBarangPagination(totalRows, perPage, currentPage, query);
            }
        });
    }

    function generateBarangPagination(totalRows, perPage, currentPage, query = '') {
        const totalPages = Math.ceil(totalRows / perPage);
        let paginationHtml = '';

        for (let i = 1; i <= totalPages; i++) {
            const activeClass = i === currentPage ? 'active' : '';
            paginationHtml += `
            <button class="btn btn-sm btn-primary ${activeClass}" data-page="${i}">
                ${i}
            </button>
        `;
        }

        $('#barangPagination').html(paginationHtml);

        // Tambahkan event handler untuk tombol pagination
        $('#barangPagination button').on('click', function() {
            const page = $(this).data('page');
            loadBarangTable(page, query);
        });
    }

    function loadTemporaryTable() {
        $.ajax({
            // url: 'bahan/get_transferstok_temp',
            url: "<?= base_url('bahan/get_transferstok_temp'); ?>",

            method: 'GET',
            success: function(response) {
                const temporaryBahan = JSON.parse(response);
                let rows = '';
                temporaryBahan.forEach(item => {
                    rows += `
                            <tr>
                                <td>${item.kode_bahan}</td>
                                <td>${item.nama_bahan}</td>
                                <td>${item.qty}</td>
                                <td>
                                    <button class="btn btn-danger delete-row" data-id="${item.id}">Hapus</button>
                                </td>
                            </tr>
                        `;
                });
                $('#temporaryTable tbody').html(rows);
            }
        });
    }

    // Event handler untuk input pencarian
    $('#search').on('keyup', function() {
        const query = $(this).val();
        loadBarangTable(1, query); // Mulai dari halaman pertama dengan pencarian
    });

    // // Muat data barang saat modal dibuka
    $('#bahanModal').on('show.bs.modal', function() {
        loadBarangTable();
    });

    $(document).ready(function() {
        loadBarangTable();
        loadTemporaryTable();
        // $('#search').on('keyup', function() {
        //     var keyword = $(this).val().toLowerCase();
        //     var filtered = barang.filter(function(item) {
        //         return item.nama.toLowerCase().includes(keyword);
        //     });
        //     loadBarangTable(1, filtered);
        // });

        $(document).on('click', '.pilih-barang', function() {
            $('#id_bahan').val($(this).data('id'));
            $('#kode_bahan').val($(this).data('kode'));
            $('#nama_bahan').val($(this).data('nama'));
            $('#bahanModal').modal('hide');
        });

        $('#addToTemporaryTable').on('click', function() {
            const bahan_id = $('#id_bahan').val();
            const kode = $('#kode_bahan').val();
            const nama = $('#nama_bahan').val();
            const quantity = $('#quantity').val();

            if (!bahan_id || !quantity) {
                alert('Silakan lengkapi data!');
                return;
            }

            $.ajax({
                // url: 'bahan/save_transferstok_temp',
                url: "<?= base_url('bahan/save_transferstok_temp'); ?>",

                method: 'POST',
                data: {
                    "bahan_id": bahan_id,
                    "kode": kode,
                    "nama": nama,
                    "quantity": quantity,
                },
                dataType: 'json',
                timeout: 6000,
                success: function(response) {
                    const result = JSON.parse(response);
                    if (result.status === 'exists') {
                        alert(result.message);
                    } else {
                        loadTemporaryTable();
                        $('#id_bahan').val('');
                        $('#kode_bahan').val('');
                        $('#nama_bahan').val('');
                        $('#quantity').val('');
                    }
                }
            });
        });

        $(document).on('click', '.delete-row', function() {
            const id = $(this).data('id');
            $.ajax({
                // url: `BarangController/deleteTemporaryBarang/${id}`,
                url: "<?= base_url('bahan/delete_transferstok_temp'); ?>",
                method: 'POST',
                data: {
                    "id": id
                },
                dataType: 'json',
                success: function(response) {
                    loadTemporaryTable();
                    alert(response.message);

                },
                // success: function (response) {
                //     if (response.status === 'success') {
                //         // Hapus baris dari tabel
                //         $('#row-' + id).remove();
                //         alert(response.message);
                //     } else {
                //         alert(response.message);
                //     }
                // },
                error: function() {
                    alert('Terjadi kesalahan. Bahan tidak dapat dihapus.');
                }
            });
        });

        $(document).on('click', '.delete-all', function(e) {
            e.preventDefault();

            swal.fire({
                title: 'Hapus Semua Bahan ! ',
                text: "Apakah anda yakin semua bahan dihapus ? ",
                icon: "warning",
                showDenyButton: true,
                confirmButtonText: 'Ya',
                denyButtonText: 'Tidak',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        // url: `BarangController/deleteTemporaryBarang/${id}`,
                        url: "<?= base_url('bahan/delete_transferstok_temp_all'); ?>",
                        method: 'POST',
                        data: {
                            // "id": id
                        },
                        dataType: 'json',
                        success: function(response) {
                            loadTemporaryTable();
                            alert(response.message);

                        },
                        error: function() {
                            alert('Terjadi kesalahan. Bahan tidak dapat dihapus.');
                        }
                    });
                } else {
                    Swal.fire('Data Bahan Batal Dihapus', '', 'error')
                }
            });
        });

        $(document).on('click', '.saveData', function(e) {
            const date = $('#date').val();
            const id_cabang = $('#id_cabang').val();
            const no_surat = $('#no_surat').val();
            const keterangan = $('#keterangan').val();

            if (!date || !id_cabang || !no_surat) {
                alert('Silakan lengkapi data!');
                return;
            }

            e.preventDefault();

            swal.fire({
                title: 'Simpan Transfer Stok ! ',
                text: "Apakah anda yakin simpan data ini ? ",
                icon: "warning",
                showDenyButton: true,
                confirmButtonText: 'Ya',
                denyButtonText: 'Tidak',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        // url: `BarangController/deleteTemporaryBarang/${id}`,
                        url: "<?= base_url('bahan/save_transferstok'); ?>",
                        method: 'POST',
                        data: {
                            "date": date,
                            "id_cabang": id_cabang,
                            "no_surat": no_surat,
                            "keterangan": keterangan,
                        },
                        dataType: 'json',
                        success: function(response) {
                            loadTemporaryTable();
                            $('#date').val('');
                            $('#id_cabang').val('');
                            $('#no_surat').val('');
                            $('#keterangan').val('');
                            alert(response.message);

                        },
                        // error: function(data) {
                        //     alert(data);
                        // }
                        error: function(xhr, ajaxOptions, thrownError) {
                            alert(xhr.status);
                        },
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