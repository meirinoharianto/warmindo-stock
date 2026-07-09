<div class="clearfix"></div>
<?php
if (!empty($temptanggal)) {
    $tgl = date('Y-m-d', strtotime(str_replace('/', '-', $temptanggal)));
} else if (!empty($this->input->post('date'))) {
    $tgl = $this->input->post('date');
} else {
    $tgl = 0;
}

if (!empty($tempnosurat)) {
    $nosurat = $tempnosurat;
} else if (!empty($this->input->post('no_surat'))) {
    $nosurat = $this->input->post('no_surat');
} else {
    $nosurat = '';
}

if (!empty($temptujuan)) {
    $tujuan = $temptujuan;
} else if (!empty($this->input->post('id_cabang'))) {
    $tujuan = $this->input->post('id_cabang');
} else {
    $tujuan = '';
}
?>
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
                            <div class="col-12">
                                <a href="<?= base_url('bahan/import_transferstok_multi'); ?>" class="btn btn-success btn-block mb-2">
                                    Import Transfer Stok Multi Cabang Excel</a>
                            </div>
                        </div>

                        <div class="row">

                            <div class="col">
                                <div class="form-group">
                                    <label for="">Keterangan</label>
                                    <textarea class="form-control" name="keterangan" id="keterangan"
                                        placeholder=""></textarea>
                                </div>
                            </div>
                        </div>

                        <form id="barangForm">
                            <div class="row">

                                <div class="form-group col-2">
                                    <label for="date">Tanggal</label>
                                    <input type="date" class="form-control" id="date" readonly>
                                </div>
                                <div class="form-group col-2">
                                    <label for="">No Surat</label>
                                    <input type="text" class="form-control" name="no_surat" id="no_surat" placeholder="">
                                </div>
                                <div class="form-group">
                                    <label for="">Cabang Tujuan</label>
                                    <select class="form-control" name="id_cabang" id="id_cabang">
                                        <option value="" disabled selected>- pilih -</option>

                                        <?php foreach ($cab as $r) {
                                            $selected = ($r->id == $tujuan) ? "selected" : "";
                                        ?>
                                            <option value="<?= $r->id; ?>" <?= $selected; ?>><?= $r->nama_toko; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
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

                                            <th>Tanggal</th>
                                            <th>No Surat</th>
                                            <th>Cabang</th>
                                            <th>Kode</th>
                                            <th>Nama Bahan</th>
                                            <th>Qty</th>
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

                        <a href="<?= base_url('bahan/transferstok'); ?>" class="btn btn-danger btn-md">
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

            url: "<?= base_url('bahan/get_transferstok_bulk_temp'); ?>",

            method: "GET",

            success: function(response) {

                const temporaryBahan = JSON.parse(response);

                let rows = '';

                temporaryBahan.forEach(function(item) {

                    rows += `
                    <tr>
                        <td>${item.tanggal}</td>
                        <td>${item.no_surat}</td>
                        <td>${item.tujuan}</td>
                        <td>${item.kode_bahan}</td>
                        <td>${item.nama_bahan}</td>
                        <td class="text-center">${item.qty}</td>
                        <td>
                            <button
                                class="btn btn-danger delete-row"
                                data-id="${item.id}">
                                Hapus
                            </button>
                        </td>
                    </tr>
                `;

                });

                $("#temporaryTable tbody").html(rows);

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


        $(document).on('click', '.pilih-barang', function() {
            $('#id_bahan').val($(this).data('id'));
            $('#kode_bahan').val($(this).data('kode'));
            $('#nama_bahan').val($(this).data('nama'));
            $('#bahanModal').modal('hide');
        });

        $('#addToTemporaryTable').on('click', function(e) {
            e.preventDefault(); // Stop default behavior

            const bahan_id = $('#id_bahan').val();
            const kode = $('#kode_bahan').val();
            const nama = $('#nama_bahan').val();
            const quantity = $('#quantity').val();

            if (!bahan_id || !quantity) {
                alert('Silakan lengkapi data!');
                return;
            }

            $.ajax({
                url: "<?= base_url('bahan/save_transferstok_bulk_temp'); ?>",
                method: 'POST',
                data: {
                    bahan_id,
                    kode,
                    nama,
                    quantity
                },
                dataType: 'json',
                success: function(result) {
                    if (result.status === 'exists') {
                        alert(result.message);
                    } else {
                        loadTemporaryTable(); // Now it should work
                        $('#id_bahan').val('');
                        $('#kode_bahan').val('');
                        $('#nama_bahan').val('');
                        $('#quantity').val('');
                    }
                },
                error: function(xhr) {
                    console.error("Error:", xhr.responseText);
                    alert("Gagal menyimpan data. Lihat konsol untuk detail.");
                }
            });
        });

        $(document).on('click', '.delete-row', function() {
            const id = $(this).data('id');
            $.ajax({
                // url: `BarangController/deleteTemporaryBarang/${id}`,
                url: "<?= base_url('bahan/delete_transferstok_bulk_temp'); ?>",
                method: 'POST',
                data: {
                    "id": id
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
                        url: "<?= base_url('bahan/delete_transferstok_bulk_temp_all'); ?>",
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

            e.preventDefault();

            swal.fire({
                title: 'Simpan Transfer Stok ! ',
                text: "Apakah anda yakin simpan data ini ? ",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: 'Ya, Simpan',
                cancelButtonText: 'Tidak, Batalkan',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "<?= base_url('bahan/save_transferstok_multi'); ?>",
                        method: 'POST',
                        data: {
                            keterangan: $('#keterangan').val()
                        },
                        dataType: 'json',
                        beforeSend: function() {
                            // Show loading indicator
                            Swal.fire({
                                title: 'Menyimpan...',
                                html: 'Sedang memproses data',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading()
                                }
                            });
                        },
                        success: function(response) {
                            if (response.success) {
                                // Clear form fields
                                $('#id').val('');
                                $('#date').val('');
                                $('#id_cabang').val('');
                                $('#no_surat').val('');
                                $('#keterangan').val('');

                                // Reload table
                                loadTemporaryTable();

                                // Show success message with redirect confirmation
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Data berhasil disimpan',
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // Redirect after user clicks OK
                                        window.location.href = "<?= base_url('bahan/transferstok'); ?>";
                                    }
                                });
                            } else {
                                // Show error from server
                                Swal.fire('Gagal!', response.message + 'Terjadi kesalahan saat menyimpan', 'error');
                            }
                        },
                        error: function(xhr, status, error) {
                            // Show AJAX error
                            Swal.fire({
                                title: 'Error!',
                                text: 'Terjadi kesalahan: ' + (xhr.responseJSON?.message || xhr.statusText || 'Unknown error'),
                                icon: 'error'
                            });
                            console.error(xhr);
                        }

                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire('Dibatalkan', 'Data tidak disimpan', 'info');
                }
            });
        });
    });
</script>