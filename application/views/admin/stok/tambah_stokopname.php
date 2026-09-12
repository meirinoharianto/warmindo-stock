<div class="clearfix"></div>
<?php
if (!empty($date)) {
    $tgl = date('Y-m-d', strtotime(str_replace('/', '-', $date)));
} else if (!empty($this->input->post('date'))) {
    $tgl = $this->input->post('date');
} else {
    $tgl = '';
}

if (!empty($id_cabang)) {
    $cabang = $id_cabang;
} else if (!empty($this->input->post('id_cabang'))) {
    $cabang = $this->input->post('id_cabang');
} else {
    $cabang = '';
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
                                <a href="<?= base_url('stok/import_stokopname'); ?>" class="btn btn-success btn-block mb-2">
                                    Import Stok Opname</a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="">Tanggal Opname</label>
                                    <input type="date" class="form-control" name="date" id="date" placeholder="" value="<?= $tgl ?>">
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="">Cabang</label>
                                    <select class="form-control" name="id_cabang" id="id_cabang">
                                        <option value="" disabled selected>- pilih -</option>
                                        <?php foreach ($cab as $r) {
                                            $selected = ($r->id == $cabang) ? "selected" : "";
                                        ?>
                                            <option value="<?= $r->id; ?>" <?= $selected; ?>><?= $r->nama_toko; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">Keterangan</label>
                                    <textarea class="form-control" name="keterangan" id="keterangan"
                                        placeholder=""></textarea>
                                </div>
                            </div>
                        </div>

                        <form id="barangForm">
                            <div class="row">
                                <div class="form-group col">
                                    <label for="kode_bahan">Kode Bahan :</label>
                                    <input type="text" class="form-control" id="id_bahan" hidden disabled>
                                    <input type="text" class="form-control" id="kode_bahan" readonly disabled>
                                </div>
                                <div class="form-group col-6">
                                    <label for="nama_bahan">Nama Bahan :</label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" id="nama_bahan" readonly disabled>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#bahanModal" id="searchBahanBtn" disabled><i class="fa fa-search"> </i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col">
                                    <label for="stok_sistem">Stok Sistem:</label>
                                    <input type="number" class="form-control" id="stok_sistem" readonly disabled>
                                </div>
                                <div class="form-group col">
                                    <label for="stok_fisik">Stok Fisik:</label>
                                    <input type="number" class="form-control" id="stok_fisik" disabled>
                                </div>

                                <div class="form-group col">
                                    <label for="quantity">Aksi :</label>
                                    <div class="btn-block">

                                        <button type="button"
                                            class="btn btn-secondary tambahkan"
                                            id="addToTemporaryTable"
                                            disabled>
                                            <i class="fa fa-plus"></i>
                                        </button>

                                        <button type="button"
                                            class="btn btn-info"
                                            id="importExcelBtn"
                                            disabled>
                                            <i class="fa fa-upload"></i>

                                        </button>
                                    </div>
                                    <!-- <button class="btn btn-secondary btn-block tambahkan" id="addToTemporaryTable" disabled>
                                        <i class="fa fa-plus"> </i>
                                    </button> -->
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
                                            <th>Stok Sistem</th>
                                            <th>Stok Fisik</th>
                                            <th>Selisih</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data akan dimuat di sini -->
                                    </tbody>
                                </table>
                                <!-- <button type="button" class="btn btn-primary" id="saveData">Simpan</button> -->
                                <button class="btn btn-warning delete-all"><b><i class="fa fa-trash"></i> Hapus Semua Bahan</b></button>

                            </div>
                        </div>
                    </div>

                </div>
                <div class="card-footer text-muted">
                    <div class="float-right">

                        <!-- <button type="submit" class="btn btn-primary btn-md">
                            <b><i class="fa fa-save"></i> Simpan</b></button> -->
                        <button class="btn btn-primary saveData" id="saveData"><b><i class="fa fa-save"></i> Simpan</b></button>

                        <!-- <button class="btn btn-warning delete-all"><b><i class="fa fa-trash"></i> Hapus Semua</b></button> -->

                        <a href="<?= base_url('stok/tambah_stokopname'); ?>" class="btn btn-danger btn-md">
                            <!-- <b><i class="fa fa-angle-double-left"></i> Kembali</b> -->
                            <b> Batal</b>
                        </a>
                        <a href="<?= base_url('stok/stokopname'); ?>" class="btn btn-danger btn-md">
                            <b><i class="fa fa-angle-double-left"></i> Kembali</b>
                        </a>
                    </div>
                </div>
            </div>
            <!-- </form> -->

        </div>
    </div>
</div>
</div>

<!-- Modal Import Excel -->
<div class="modal fade"
    id="importExcelModal"
    tabindex="-1"
    role="dialog">

    <div class="modal-dialog" role="document">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    Import Stok Fisik
                </h5>

                <button type="button"
                    class="close"
                    data-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>


            <div class="modal-body">

                <div class="alert alert-info">

                    <i class="fa fa-info-circle"></i>

                    Gunakan file hasil export stock opname
                    dengan format:

                    <br>

                    <strong>
                        Kode Bahan |
                        Nama Bahan |
                        Stok Sistem |
                        Stok Fisik
                    </strong>

                </div>


                <div class="form-group">

                    <label>
                        Pilih File
                    </label>

                    <input type="file"
                        class="form-control-file"
                        id="importExcelFile"
                        accept=".xlsx,.xls,.csv">

                </div>


                <div id="importProgress"
                    style="display:none;">

                    <div class="progress">

                        <div id="importProgressBar"
                            class="progress-bar progress-bar-striped progress-bar-animated"
                            style="width:0%">

                            0%

                        </div>

                    </div>

                    <small id="importStatus"
                        class="text-muted">
                    </small>

                </div>

            </div>


            <div class="modal-footer">

                <button type="button"
                    class="btn btn-secondary"
                    data-dismiss="modal">

                    Batal

                </button>


                <button type="button"
                    class="btn btn-primary"
                    id="processImportExcel">

                    <i class="fa fa-upload"></i>
                    Import

                </button>

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
                <div class="btn-group">
                    <button type="button"
                        class="btn btn-success"
                        id="exportExcel">
                        <i class="fa fa-file-excel-o"></i>
                        Excel
                    </button>

                    <button type="button"
                        class="btn btn-primary"
                        id="exportCSV">
                        <i class="fa fa-file-text-o"></i>
                        CSV
                    </button>
                </div>
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Kode Bahan</th>
                            <th>Nama Bahan</th>
                            <th>Stok Sistem</th>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
    // ======================================================
    // TOMBOL TAMBAHKAN MANUAL
    // ======================================================

    $('#addToTemporaryTable').on('click', function(e) {

        e.preventDefault();

        saveToTemporaryTable(function(success, message) {

            if (success) {

                console.log('Data berhasil disimpan');

            } else {

                console.log(
                    'Gagal:',
                    message
                );
            }

        });

    });


    // ======================================================
    // FUNCTION SAVE TEMPORARY
    // ======================================================

    function saveToTemporaryTable(callback) {

        const bahan_id =
            $('#id_bahan').val();

        const kode =
            $('#kode_bahan').val();

        const nama =
            $('#nama_bahan').val();

        const stoksistem =
            $('#stok_sistem').val();

        const stokfisik =
            $('#stok_fisik').val();


        console.log('==============================');
        console.log('SAVE TEMPORARY');
        console.log('bahan_id:', bahan_id);
        console.log('kode:', kode);
        console.log('nama:', nama);
        console.log('stok sistem:', stoksistem);
        console.log('stok fisik:', stokfisik);
        console.log('==============================');


        // ==========================================
        // VALIDASI
        // ==========================================

        if (!bahan_id) {

            console.error(
                'Bahan ID kosong'
            );

            if (typeof callback === 'function') {
                callback(false, 'Bahan ID kosong');
            }

            return;
        }


        // Stok fisik 0 tetap valid
        if (
            stokfisik === '' ||
            stokfisik === null ||
            stokfisik === undefined
        ) {

            console.error(
                'Stok fisik kosong'
            );

            if (typeof callback === 'function') {
                callback(false, 'Stok fisik kosong');
            }

            return;
        }


        // ==========================================
        // AJAX SAVE
        // ==========================================

        $.ajax({

            url: "<?= base_url('stok/save_stokopname_temp'); ?>",

            method: 'POST',

            dataType: 'json',

            data: {

                bahan_id: bahan_id,

                kode: kode,

                nama: nama,

                stoksistem: stoksistem,

                stokfisik: stokfisik

            },


            beforeSend: function() {

                console.log(
                    'Mengirim data ke server...'
                );

            },


            success: function(result) {

                console.log(
                    'Response save_stokopname_temp:',
                    result
                );


                // ==================================
                // EXIST
                // ==================================

                if (result.status === 'exists') {

                    console.warn(
                        'Data sudah ada:',
                        result.message
                    );


                    if (
                        typeof callback === 'function'
                    ) {

                        callback(
                            false,
                            'exists'
                        );
                    }

                    return;
                }


                // ==================================
                // ERROR
                // ==================================

                if (
                    result.status === 'error'
                ) {

                    console.error(
                        'Gagal:',
                        result.message
                    );


                    if (
                        typeof callback === 'function'
                    ) {

                        callback(
                            false,
                            result.message
                        );
                    }

                    return;
                }


                // ==================================
                // SUCCESS
                // ==================================

                if (
                    result.status === 'success'
                ) {

                    console.log(
                        'Berhasil menyimpan:',
                        kode
                    );


                    // Refresh temporary table
                    loadTemporaryTable();


                    // Reset form
                    $('#id_bahan').val('');

                    $('#kode_bahan').val('');

                    $('#nama_bahan').val('');

                    $('#stok_sistem').val('');

                    $('#stok_fisik').val('');


                    if (
                        typeof callback === 'function'
                    ) {

                        callback(
                            true,
                            'success'
                        );
                    }

                }

            },


            error: function(
                xhr,
                status,
                error
            ) {

                console.error(
                    'AJAX ERROR save_stokopname_temp'
                );

                console.error(
                    'HTTP:',
                    xhr.status
                );

                console.error(
                    'Status:',
                    status
                );

                console.error(
                    'Error:',
                    error
                );

                console.error(
                    'Response:',
                    xhr.responseText
                );


                if (
                    typeof callback === 'function'
                ) {

                    callback(
                        false,
                        xhr.responseText
                    );
                }

            }

        });

    }


    // =====================================================
    // BUKA MODAL IMPORT
    // =====================================================

    $('#importExcelBtn').on('click', function() {

        $('#importExcelFile').val('');

        $('#importProgress').hide();

        $('#importProgressBar')
            .css('width', '0%')
            .text('0%');

        $('#importStatus').text('');

        $('#importExcelModal').modal('show');
    });


    // =====================================================
    // PROSES FILE EXCEL
    // =====================================================

    $('#processImportExcel').on('click', function() {

        const fileInput =
            document.getElementById('importExcelFile');

        if (!fileInput.files.length) {

            alert(
                'Silakan pilih file Excel terlebih dahulu.'
            );

            return;
        }


        const file =
            fileInput.files[0];

        const fileName =
            file.name.toLowerCase();


        // Validasi extension
        const allowedExtensions = [
            '.xlsx',
            '.xls',
            '.csv'
        ];

        const validExtension =
            allowedExtensions.some(function(ext) {
                return fileName.endsWith(ext);
            });


        if (!validExtension) {

            alert(
                'Format file tidak valid.\n' +
                'Gunakan XLSX, XLS atau CSV.'
            );

            return;
        }


        $('#processImportExcel')
            .prop('disabled', true);

        $('#importProgress')
            .show();

        $('#importStatus')
            .text('Membaca file...');


        const reader =
            new FileReader();


        reader.onload = function(e) {

            try {

                const data =
                    new Uint8Array(
                        e.target.result
                    );


                const workbook =
                    XLSX.read(data, {
                        type: 'array'
                    });


                // Sheet pertama
                const sheetName =
                    workbook.SheetNames[0];


                const worksheet =
                    workbook.Sheets[sheetName];


                // Excel → JSON
                const rows =
                    XLSX.utils.sheet_to_json(
                        worksheet, {
                            defval: ''
                        }
                    );


                if (!rows.length) {

                    alert(
                        'File Excel tidak memiliki data.'
                    );

                    resetImport();

                    return;
                }


                console.log(
                    'Data import:',
                    rows
                );


                // Mulai proses
                processImportedRows(rows);

            } catch (error) {

                console.error(
                    'Import Error:',
                    error
                );

                alert(
                    'Gagal membaca file Excel.\n' +
                    error.message
                );

                resetImport();
            }
        };


        reader.onerror = function() {

            alert(
                'Gagal membaca file.'
            );

            resetImport();
        };


        reader.readAsArrayBuffer(file);
    });


    // =====================================================
    // PROSES DATA EXCEL SATU PER SATU
    // =====================================================

    function processImportedRows(rows) {

        let currentIndex = 0;

        const totalRows = rows.length;

        let successCount = 0;
        let failedCount = 0;
        let existsCount = 0;

        let errors = [];


        function processNext() {

            // ==========================================
            // SELESAI
            // ==========================================

            if (currentIndex >= totalRows) {

                $('#importProgressBar')
                    .css('width', '100%')
                    .text('100%');


                $('#importStatus').html(
                    'Import selesai.<br>' +
                    'Berhasil: <strong>' + successCount + '</strong><br>' +
                    'Sudah ada: <strong>' + existsCount + '</strong><br>' +
                    'Gagal: <strong>' + failedCount + '</strong>'
                );


                $('#processImportExcel')
                    .prop('disabled', false);


                console.log('================================');
                console.log('IMPORT SELESAI');
                console.log('Total      :', totalRows);
                console.log('Berhasil   :', successCount);
                console.log('Sudah ada  :', existsCount);
                console.log('Gagal      :', failedCount);
                console.log('Error list :', errors);
                console.log('================================');


                if (errors.length > 0) {

                    console.table(errors);
                }


                return;
            }


            const item = rows[currentIndex];


            // ==========================================
            // BACA EXCEL
            // ==========================================

            const kodeBahan = String(
                item['Kode Bahan'] ??
                item['kode_bahan'] ??
                ''
            ).trim();


            const stokFisik =
                item['Stok Fisik'] ??
                item['stok_fisik'] ??
                '';


            const stokSistem =
                item['Stok Sistem'] ??
                item['stok_sistem'] ??
                '';


            console.log('--------------------------------');
            console.log(
                'Processing row:',
                currentIndex + 2
            );

            console.log(
                'Kode:',
                kodeBahan
            );

            console.log(
                'Stok Fisik:',
                stokFisik
            );


            // ==========================================
            // VALIDASI
            // ==========================================

            if (!kodeBahan) {

                failedCount++;

                errors.push({
                    row: currentIndex + 2,
                    kode: '',
                    error: 'Kode bahan kosong'
                });

                currentIndex++;

                processNext();

                return;
            }


            if (
                stokFisik === '' ||
                stokFisik === null ||
                stokFisik === undefined
            ) {

                failedCount++;

                errors.push({
                    row: currentIndex + 2,
                    kode: kodeBahan,
                    error: 'Stok fisik kosong'
                });

                currentIndex++;

                processNext();

                return;
            }


            // ==========================================
            // UPDATE STATUS
            // ==========================================

            $('#importStatus').text(
                'Mencari bahan: ' + kodeBahan
            );


            // ==========================================
            // CARI BAHAN
            // ==========================================

            $.ajax({

                url: "<?= base_url('stok/get_bahan_by_kode'); ?>",

                method: 'POST',

                dataType: 'json',

                data: {
                    kode: kodeBahan
                },


                success: function(result) {

                    console.log(
                        'get_bahan_by_kode:',
                        result
                    );


                    // ==================================
                    // BAHAN TIDAK DITEMUKAN
                    // ==================================

                    if (!result.status) {

                        failedCount++;

                        errors.push({
                            row: currentIndex + 2,
                            kode: kodeBahan,
                            error: result.message ||
                                'Bahan tidak ditemukan'
                        });


                        currentIndex++;

                        processNext();

                        return;
                    }


                    const bahan = result.data;


                    console.log(
                        'Bahan ditemukan:',
                        bahan
                    );


                    // ==================================
                    // ISI FORM
                    // ==================================

                    $('#id_bahan')
                        .val(bahan.id);

                    $('#kode_bahan')
                        .val(bahan.kode_bahan);

                    $('#nama_bahan')
                        .val(bahan.nama_bahan);

                    $('#stok_sistem')
                        .val(stokSistem);

                    $('#stok_fisik')
                        .val(stokFisik);


                    // ==================================
                    // STATUS
                    // ==================================

                    $('#importStatus').text(
                        'Menyimpan: ' +
                        bahan.kode_bahan +
                        ' - ' +
                        bahan.nama_bahan
                    );


                    // ==================================
                    // SIMPAN
                    // ==================================

                    saveToTemporaryTable(
                        function(success, message) {

                            if (success) {

                                successCount++;

                                console.log(
                                    'BERHASIL:',
                                    kodeBahan
                                );

                            } else {

                                if (
                                    message &&
                                    message.toLowerCase()
                                    .includes('sudah')
                                ) {

                                    existsCount++;

                                } else {

                                    failedCount++;

                                    errors.push({
                                        row: currentIndex + 2,
                                        kode: kodeBahan,
                                        error: message ||
                                            'Gagal menyimpan'
                                    });
                                }
                            }


                            // ==================================
                            // PROGRESS
                            // ==================================

                            const progress =
                                Math.round(
                                    (
                                        (currentIndex + 1) /
                                        totalRows
                                    ) * 100
                                );


                            $('#importProgressBar')
                                .css(
                                    'width',
                                    progress + '%'
                                )
                                .text(
                                    progress + '%'
                                );


                            currentIndex++;


                            // Tunggu sebentar
                            setTimeout(
                                processNext,
                                100
                            );
                        }
                    );
                },


                error: function(xhr) {

                    console.error(
                        'ERROR get_bahan_by_kode'
                    );

                    console.error(
                        xhr.status
                    );

                    console.error(
                        xhr.responseText
                    );


                    failedCount++;

                    errors.push({
                        row: currentIndex + 2,
                        kode: kodeBahan,
                        error: 'AJAX get_bahan_by_kode gagal'
                    });


                    currentIndex++;

                    processNext();
                }

            });
        }


        processNext();
    }

    // =====================================================
    // RESET IMPORT
    // =====================================================

    function resetImport() {

        $('#processImportExcel')
            .prop('disabled', false);

        $('#importProgress')
            .hide();

        $('#importProgressBar')
            .css('width', '0%')
            .text('0%');

        $('#importStatus')
            .text('');
    }

    function checkOpnameExists(callback) {

        const dateVal = $('#date').val();
        const cabangVal = $('#id_cabang').val();

        $.ajax({
            url: "<?= base_url('stok/check_opname_exists'); ?>",
            type: "POST",
            dataType: "json",

            data: {
                tanggal_opname: dateVal,
                cabang_id: cabangVal
            },

            success: function(response) {

                if (response.exists) {

                    let message =
                        'Stok opname untuk tanggal ' +
                        response.tanggal_opname +
                        ' sudah tersedia.';

                    if (response.status === 'POSTED') {
                        message += ' Status: POSTED.';
                    }

                    Swal.fire({
                        icon: 'warning',
                        title: 'Opname Sudah Ada',
                        text: message,
                        confirmButtonText: 'OK'
                    });

                    callback(true);

                } else {

                    callback(false);
                }
            },

            error: function(xhr, status, error) {

                console.error(error);

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Tidak dapat mengecek data stok opname.'
                });

                callback(true);
            }
        });
    }

    function enableBahanForm() {

        $('#id_bahan').prop('disabled', false);
        $('#kode_bahan').prop('disabled', false);
        $('#nama_bahan').prop('disabled', false);
        $('#searchBahanBtn').prop('disabled', false);
        $('#stok_sistem').prop('disabled', false);
        $('#stok_fisik').prop('disabled', false);
        $('#addToTemporaryTable').prop('disabled', false);
        $('#importExcelBtn').prop('disabled', false);
    }


    function disableBahanForm() {

        $('#id_bahan').prop('disabled', true);
        $('#kode_bahan').prop('disabled', true);
        $('#nama_bahan').prop('disabled', true);
        $('#searchBahanBtn').prop('disabled', true);
        $('#stok_sistem').prop('disabled', true);
        $('#stok_fisik').prop('disabled', true);
        $('#addToTemporaryTable').prop('disabled', true);
        $('#importExcelBtn').prop('disabled', true);
    }

    // Fungsi untuk memeriksa dan menonaktifkan Tanggal/Cabang, serta mengaktifkan form bahan
    // function checkFormStatus() {
    //     const dateVal = $('#date').val();
    //     const cabangVal = $('#id_cabang').val();

    //     if (dateVal && cabangVal && cabangVal !== '- pilih -' && cabangVal !== '') {
    //         // Menonaktifkan Tanggal dan Cabang
    //         $('#date').prop('disabled', true);
    //         $('#id_cabang').prop('disabled', true);

    //         // Mengaktifkan semua input bahan dan tombol
    //         $('#id_bahan').prop('disabled', false);
    //         $('#kode_bahan').prop('disabled', false);
    //         $('#nama_bahan').prop('disabled', false);
    //         $('#searchBahanBtn').prop('disabled', false);
    //         $('#stok_sistem').prop('disabled', false);
    //         $('#stok_fisik').prop('disabled', false);
    //         $('#addToTemporaryTable').prop('disabled', false);

    //         // Muat data bahan di modal setelah form diaktifkan
    //         loadBarangTable();
    //     } else {
    //         // Pastikan input bahan dan tombol nonaktif jika salah satu belum dipilih
    //         $('#id_bahan').prop('disabled', true);
    //         $('#kode_bahan').prop('disabled', true);
    //         $('#nama_bahan').prop('disabled', true);
    //         $('#searchBahanBtn').prop('disabled', true);
    //         $('#stok_sistem').prop('disabled', true);
    //         $('#stok_fisik').prop('disabled', true);
    //         $('#addToTemporaryTable').prop('disabled', true);

    //         // Pastikan Tanggal dan Cabang aktif
    //         $('#date').prop('disabled', false);
    //         $('#id_cabang').prop('disabled', false);
    //     }
    // }
    function checkFormStatus() {

        const dateVal = $('#date').val();
        const cabangVal = $('#id_cabang').val();

        // Jika tanggal atau cabang belum dipilih
        if (
            !dateVal ||
            !cabangVal ||
            cabangVal === '- pilih -' ||
            cabangVal === ''
        ) {

            disableBahanForm();

            $('#date').prop('disabled', false);
            $('#id_cabang').prop('disabled', false);

            return;
        }

        // Cek apakah opname sudah ada
        checkOpnameExists(function(exists) {

            if (exists) {

                // Opname sudah ada
                disableBahanForm();

                // Tetap izinkan user mengganti tanggal/cabang
                $('#date').prop('disabled', false);
                $('#id_cabang').prop('disabled', false);

                return;
            }

            // Opname belum ada
            $('#date').prop('disabled', true);
            $('#id_cabang').prop('disabled', true);

            enableBahanForm();

            // Muat bahan
            loadBarangTable();
        });
    }

    function loadBarangTable(page = 1, query = '') {
        const limit = 10;
        const selectedDateString = $('#date').val();
        const selectedCabang = $('#id_cabang').val();

        // **PENTING: Jangan kirim request AJAX jika Tanggal/Cabang belum dipilih**
        if (!$('#date').prop('disabled')) {
            console.log('Tanggal atau Cabang belum dikunci/dipilih. Abort loadBarangTable.');
            $('#bahanTable').html('<tr><td colspan="4">Pilih Tanggal dan Cabang terlebih dahulu.</td></tr>');
            return;
        }

        let cabangValue = selectedCabang || "";
        let periodeValue = selectedDateString;

        if (selectedDateString) {
            const dateObj = new Date(selectedDateString);

            // Logika untuk periode bulan sebelumnya
            dateObj.setDate(1);
            // dateObj.setMonth(dateObj.getMonth() - 1);
            dateObj.setMonth(dateObj.getMonth());
            const year = dateObj.getFullYear();
            const month = String(dateObj.getMonth() + 1).padStart(2, '0');
            periodeValue = `${year}-${month}`;
        }

        // console.log('--- Parameter AJAX Dikirim ---');
        // console.log('Search Query:', query);
        // console.log('Halaman (Page):', page);
        // console.log('Limit:', limit);
        // console.log('Tanggal yang Dipilih Pengguna:', selectedDateString);
        // console.log('Periode Yang Dikirim (Bulan Sebelumnya):', periodeValue);
        // console.log('Cabang:', cabangValue);
        // console.log('------------------------------');

        $.ajax({
            url: "<?= base_url('stok/get_bahan_stokopname_temp'); ?>",
            method: 'GET',
            data: {
                search: query,
                page: page,
                limit: limit,
                periode: periodeValue,
                idcabang: cabangValue,
                tanggal: selectedDateString
            },
            success: function(response) {
                const result = JSON.parse(response);
                const bahan = result.data;
                const totalRows = result.totalRows;
                const perPage = result.perPage;
                const currentPage = result.currentPage;
                // console.log('--- Parameter Diterima CI ---');
                // console.log(result.periode);
                // console.log(result.idcabang);
                // console.log(result.lastquery);
                // console.log('------------------------------');
                let rows = '';
                if (bahan.length > 0) {
                    bahan.forEach(item => {
                        rows += `
                                <tr>
                                    <td>${item.kode_bahan}</td>
                                    <td>${item.nama_bahan}</td>
                                    <td>${item.stok_akhir}</td>
                                    <td>
                                        <button class="btn btn-success pilih-barang" data-id="${item.id}" data-kode="${item.kode_bahan}" data-nama="${item.nama_bahan}" data-stokakhir="${item.stok_akhir}">Pilih</button>
                                    </td>
                                </tr>
                            `;
                    });
                } else {
                    rows = '<tr><td colspan="4">Tidak ada data bahan.</td></tr>';
                }
                $('#bahanTable').html(rows);

                // Tampilkan pagination
                generateBarangPagination(totalRows, perPage, currentPage, query);
            },
            error: function(xhr) {
                console.error("Error:", xhr.responseText);
                alert("Gagal tampilkan data. Lihat konsol untuk detail.");
            }
        });
    }

    function generateBarangPagination(totalRows, perPage, currentPage, query = '') {
        // ... (Fungsi pagination tidak berubah)
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
        $('#barangPagination button').off('click').on('click', function() {
            const page = $(this).data('page');
            loadBarangTable(page, query);
        });
    }


    function loadTemporaryTable() {
        $.ajax({
            url: "<?= base_url('stok/get_stokopname_temp'); ?>",
            method: 'GET',
            success: function(response) {
                console.log("Response:", response);
                const temporaryBahan = JSON.parse(response);
                let rows = '';
                if (temporaryBahan.length > 0) {
                    temporaryBahan.forEach(item => {
                        rows += `
                        <tr>
                            <td>${item.kode_bahan}</td>
                            <td>${item.nama_bahan}</td>
                            <td>${item.stok_sistem}</td>
                            <td>${item.stok_fisik}</td>
                            <td>${item.selisih}</td>
                            <td>
                                <button class="btn btn-danger delete-row" data-id="${item.id}">Hapus</button>
                            </td>
                        </tr>
                    `;
                    });
                } else {
                    rows = '<tr><td colspan="5">Belum ada bahan yang ditambahkan.</td></tr>';
                }
                $('#temporaryTable tbody').html(rows);
            },
            error: function(xhr) {
                console.error("Error loading table:", xhr.responseText);
            }
        });
    }

    function getExportParams() {

        const selectedDateString = $('#date').val();
        const selectedCabang = $('#id_cabang').val();
        const search = $('#search').val() || '';

        let periodeValue = selectedDateString;

        if (selectedDateString) {

            const dateObj = new Date(selectedDateString);

            dateObj.setDate(1);

            // Bulan yang sama
            dateObj.setMonth(dateObj.getMonth());

            const year = dateObj.getFullYear();
            const month = String(dateObj.getMonth() + 1).padStart(2, '0');

            periodeValue = `${year}-${month}`;
        }

        return {
            search: search,
            periode: periodeValue,
            idcabang: selectedCabang || '',
            tanggal: selectedDateString || ''
        };
    }


    // ========================================
    // EXPORT EXCEL
    // ========================================
    $('#exportExcel').on('click', function() {

        if (!$('#date').prop('disabled')) {
            alert('Pilih Tanggal dan Cabang terlebih dahulu.');
            return;
        }

        const params = getExportParams();

        const url =
            "<?= base_url('stok/export_bahan_stokopname'); ?>" +
            "?format=excel" +
            "&search=" + encodeURIComponent(params.search) +
            "&periode=" + encodeURIComponent(params.periode) +
            "&idcabang=" + encodeURIComponent(params.idcabang) +
            "&tanggal=" + encodeURIComponent(params.tanggal);

        window.location.href = url;
    });


    // ========================================
    // EXPORT CSV
    // ========================================
    $('#exportCSV').on('click', function() {

        if (!$('#date').prop('disabled')) {
            alert('Pilih Tanggal dan Cabang terlebih dahulu.');
            return;
        }

        const params = getExportParams();

        const url =
            "<?= base_url('stok/export_bahan_stokopname'); ?>" +
            "?format=csv" +
            "&search=" + encodeURIComponent(params.search) +
            "&periode=" + encodeURIComponent(params.periode) +
            "&idcabang=" + encodeURIComponent(params.idcabang) +
            "&tanggal=" + encodeURIComponent(params.tanggal);

        window.location.href = url;
    });

    // Event handler untuk input pencarian di modal
    $('#search').off('keyup').on('keyup', function() {
        const query = $(this).val();
        loadBarangTable(1, query); // Mulai dari halaman pertama dengan pencarian
    });

    // Muat data barang saat modal dibuka
    $('#bahanModal').off('show.bs.modal').on('show.bs.modal', function() {
        loadBarangTable();
    });

    $(document).ready(function() {
        // 1. Jalankan pengecekan status saat halaman dimuat
        checkFormStatus();

        // 2. Tambahkan event listener untuk Tanggal dan Cabang
        $('#date, #id_cabang').on('change', function() {
            // Panggil fungsi status check setiap kali Tanggal atau Cabang berubah
            checkFormStatus();
        });

        // Muat tabel temporary saat halaman dimuat
        loadTemporaryTable();

        $(document).on('click', '.pilih-barang', function() {
            $('#id_bahan').val($(this).data('id'));
            $('#kode_bahan').val($(this).data('kode'));
            $('#nama_bahan').val($(this).data('nama'));
            $('#stok_sistem').val($(this).data('stokakhir'));
            $('#stok_fisik').val(''); // Kosongkan stok aktual
            $('#bahanModal').modal('hide');
        });

        // $('#addToTemporaryTable').on('click', function(e) {
        //     e.preventDefault();

        //     const bahan_id = $('#id_bahan').val();
        //     const kode = $('#kode_bahan').val();
        //     const nama = $('#nama_bahan').val();
        //     const stoksistem = $('#stok_sistem').val();
        //     const stokfisik = $('#stok_fisik').val();

        //     if (!bahan_id || !stokfisik) {
        //         alert('Silakan pilih Bahan dan masukkan Stok Aktual!');
        //         return;
        //     }

        //     $.ajax({
        //         url: "<?= base_url('stok/save_stokopname_temp'); ?>",
        //         method: 'POST',
        //         data: {
        //             bahan_id,
        //             kode,
        //             nama,
        //             stoksistem,
        //             stokfisik
        //         },
        //         dataType: 'json',
        //         success: function(result) {
        //             if (result.status === 'exists') {
        //                 // Tampilkan modal notifikasi bawaan (jika ada) atau alert
        //                 alert(result.message);
        //                 // $('#notificationModal').modal('show'); // jika mau pakai modal bawaan
        //             } else {
        //                 loadTemporaryTable();
        //                 // Reset form input bahan
        //                 $('#id_bahan').val('');
        //                 $('#kode_bahan').val('');
        //                 $('#nama_bahan').val('');
        //                 $('#stok_sistem').val('');
        //                 $('#stok_fisik').val('');
        //             }
        //         },
        //         error: function(xhr) {
        //             console.error("Error:", xhr.responseText);
        //             alert("Gagal menyimpan data. Lihat konsol untuk detail.");
        //         }
        //     });
        // });


        $(document).on('click', '.delete-row', function() {
            const id = $(this).data('id');
            $.ajax({
                url: "<?= base_url('stok/delete_stokopname_temp'); ?>",
                method: 'POST',
                data: {
                    "id": id
                },
                dataType: 'json',
                success: function(response) {
                    loadTemporaryTable();
                    // alert(response.message); // Komentar jika ingin lebih halus
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
                text: "Apakah anda yakin semua bahan dihapus? ",
                icon: "warning",
                showDenyButton: true,
                confirmButtonText: 'Ya',
                denyButtonText: 'Tidak',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "<?= base_url('stok/delete_stokopname_temp_all'); ?>",
                        method: 'POST',
                        dataType: 'json',
                        success: function(response) {
                            loadTemporaryTable();
                            Swal.fire('Berhasil!', response.message, 'success');
                        },
                        error: function() {
                            Swal.fire('Error!', 'Terjadi kesalahan. Bahan tidak dapat dihapus.', 'error');
                        }
                    });
                } else {
                    Swal.fire('Dibatalkan', 'Data Bahan Batal Dihapus', 'error')
                }
            });
        });

        $(document).on('click', '.saveData', function(e) {
            const date = $('#date').val();
            const id_cabang = $('#id_cabang').val();
            const keterangan = $('#keterangan').val();

            // Pastikan tanggal dan cabang sudah dipilih (walaupun seharusnya sudah di-disable jika sudah dipilih)
            if (!date || !id_cabang) {
                alert('Silakan lengkapi Tanggal dan Cabang!');
                return;
            }

            // Re-aktifkan input tanggal dan cabang sesaat sebelum kirim data POST
            $('#date').prop('disabled', false);
            $('#id_cabang').prop('disabled', false);


            e.preventDefault();

            swal.fire({
                title: 'Simpan Data Stok Opname! ',
                text: "Apakah anda yakin simpan data ini? ",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: 'Ya, Simpan',
                cancelButtonText: 'Tidak, Batalkan',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "<?= base_url('stok/save_stokopname'); ?>",
                        method: 'POST',
                        data: {
                            "date": date,
                            "id_cabang": id_cabang,
                            "keterangan": keterangan,
                        },
                        dataType: 'json',
                        beforeSend: function() {
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
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Data berhasil disimpan',
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = "<?= base_url('stok/stokopname'); ?>";
                                    }
                                });
                            } else {
                                Swal.fire('Gagal!', response.message + 'Terjadi kesalahan saat menyimpan', 'error');
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Terjadi kesalahan: ' + (xhr.responseJSON?.message || xhr.statusText || 'Unknown error'),
                                icon: 'error'
                            });
                            console.error(xhr);
                        },
                        complete: function() {
                            // Nonaktifkan kembali setelah selesai, kecuali jika ada redirect
                            if (!result.isConfirmed) {
                                $('#date').prop('disabled', true);
                                $('#id_cabang').prop('disabled', true);
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire('Dibatalkan', 'Data tidak disimpan', 'info');
                    // Nonaktifkan kembali input tanggal dan cabang
                    $('#date').prop('disabled', true);
                    $('#id_cabang').prop('disabled', true);
                }
            });
        });
    });
</script>