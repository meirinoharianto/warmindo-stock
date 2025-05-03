<div class="clearfix"></div>
<?php
$bulan_tes = array(
    '01' => "Januari",
    '02' => "Februari",
    '03' => "Maret",
    '04' => "April",
    '05' => "Mei",
    '06' => "Juni",
    '07' => "Juli",
    '08' => "Agustus",
    '09' => "September",
    '10' => "Oktober",
    '11' => "November",
    '12' => "Desember"
);
?>
<div id="home">
    <div class="container mt-5">
        <div class="row">
            <div class="col-sm-12">
                <?php
                if (!empty($this->session->flashdata('success'))) {
                    echo alert_success($this->session->flashdata('success'));
                }
                if (!empty($this->session->flashdata('failed'))) {
                    echo alert_failed($this->session->flashdata('failed'));
                }
                ?>
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-success btn-md mt-2 mr-2" data-toggle="modal" data-target="#modelId">
                    <i class="fa fa-plus"></i> Tambah Pengeluaran
                </button>
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-primary btn-md mt-2" data-toggle="modal" data-target="#modelIdFilter">
                    <i class="fa fa-search"></i> Pencarian
                </button>

                <!-- <a href="<?= $url_pdf; ?>" class="btn btn-success mt-2 btn-md ml-1">
                    <i class="fa fa-download"></i> File Excel
                </a> -->
                <!-- <a href="<?php echo $url_pdf ?>" class="btn btn-danger btn-md mt-2 ml-2" target="_blank">
                    <i class="fa fa-print"></i> Cetak PDF
                </a> -->
                <a href="<?php echo base_url('keuangan/pengeluaran') ?>" class="btn btn-success btn-md mt-2 ml-2">
                    <i class="fas fa-sync"></i> Refresh
                </a>
                <div class="clearfix"></div>
                <br>
                <div class="card card-rounded">
                    <div class="card-header bg-primary text-white">
                        <h5 class="pt-2">
                            <?php
                            if (!empty($this->input->get('a') && $this->input->get('b'))) { ?>
                                Periode
                                <?php echo time_explode_date($this->input->get('a'), 'id') ?>
                                s.d.
                                <?php echo time_explode_date($this->input->get('b'), 'id') ?>
                            <?php } else { ?>
                                Periode <?php echo time_explode_date(date('Y-m-d'), 'id') ?>
                            <?php } ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive-1">
                            <table class="table table-light table-striped w-100" id="dataTable1">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Shift</th>
                                        <th>Kategori</th>
                                        <th>No Bon</th>
                                        <th>Keterangan</th>
                                        <th>Jumlah</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="6">Total</th>
                                        <th>Rp<?php echo number_format($tot->jmltotal) ?></th>
                                        <th colspan="2"></th>
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
            <form method="GET" action="<?= base_url('keuangan/pengeluaran'); ?>">
                <div class="modal-body">
                    <!-- <div class="form-group">
                        <label for="">No Ledger</label>
                        <select name="no_ledger" style="width:100%;" class="form-control select2">
                            <option selected value="" disabled>- pilih ledger - </option>
                            <?php foreach ($ledger as $r) { ?>
                                <option value="<?= $r->no_ledger; ?>"><?= $r->no_ledger ?> - <?= $r->keterangan ?></option>
                            <?php } ?>
                        </select>
                    </div> -->
                    <div class="form-group">
                        <label for="">Tanggal Start</label>
                        <input type="date" required class="form-control" value="<?= $this->input->get('a') ?>" name="a" placeholder="">
                    </div>
                    <div class="form-group">
                        <label for="">Tanggal End</label>
                        <input type="date" required class="form-control" value="<?= $this->input->get('b') ?>" name="b" placeholder="">
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
<!-- Modal -->
<div class="modal fade" id="modelId" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-plus"></i> Tambah Pengeluaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="<?= base_url('keuangan/store_pengeluaran') ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="">Kategori</label>
                        <!-- <input type="text" class="form-control" name="kategori" id="kategori" placeholder=""> -->
                        <select class="form-control" name="kategori">
                            <option value="" selected>- pilih -</option>
                            <?php $kategori = $this->db->get('kategori_keluar')->result();
                            foreach ($kategori as $r) {
                            ?>
                                <!-- <option value="<?= $r->id; ?>"><?= $r->kategori_keluar; ?></option> -->
                                <option value="<?= $r->kategori_keluar; ?>"><?= $r->kategori_keluar; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">No Bon</label>
                        <input type="text" class="form-control" name="no_bon" id="no_bon" placeholder="">
                    </div>
                    <div class="form-group">
                        <label for="">Keterangan</label>
                        <input type="text" class="form-control" name="keterangan" id="keterangan" placeholder="">
                    </div>
                    <div class="form-group">
                        <label for="">Jumlah</label>
                        <input type="text" class="form-control" name="jumlah" id="jumlah" placeholder="">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    var tabel = null;
    $(document).ready(function() {
        tabel = $('#dataTable1').DataTable({
            "processing": true,
            'responsive': true,
            "serverSide": true,
            "ordering": true, // Set true agar bisa di sorting
            "order": [
                [0, 'desc']
            ], // Default sortingnya berdasarkan kolom / field ke 0 (paling pertama)
            "ajax": {
                "url": "<?= $url; ?>", // URL file untuk proses select datanya
                "type": "POST",
                "dataType": "JSON",
            },
            "deferRender": true,
            "aLengthMenu": [
                [5, 10, 50],
                [5, 10, 50]
            ], // Combobox Limit
            "columns": [{
                    "data": 'id',
                    "sortable": false,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'date'
                },
                {
                    "data": "shift_id",
                    "render": function(data, type, row, meta) {
                        if (row.shift_id == 1) {
                            return '<span class="badge badge-primary">PAGI</span>';
                        } else if (row.shift_id == 2) {
                            return '<span class="badge badge-secondary">SORE</span>';
                        } else if (row.shift_id == 3) {
                            return '<span class="badge badge-dark">MALAM</span>';
                        } else {
                            return '';
                        }
                    }
                },
                {
                    data: 'kategori_keluar'
                },
                {
                    data: 'no_bon'
                },
                {
                    data: 'keterangan'
                },
                {
                    data: 'jumlah',
                    render: $.fn.dataTable.render.number(',', '.', 0, 'Rp')
                },
                {
                    "data": "closing_id",
                    "render": function(data, type, row, meta) {
                        if (row.closing_id != 0) {
                            return '<span class="badge badge-success"><i class="fa fa-check">CLOSED</span>';
                        } else {
                            return '';
                        }
                    }
                },
                {
                    "data": "id",
                    "render": function(data, type, row, meta) {
                        if (row.closing_id != 0) {
                            return '';
                        } else {
                            var url_edit = '<?= base_url("keuangan/edit_pengeluaran") ?>/' + row.id;
                            var url_del = '<?= base_url("keuangan/delete_pengeluaran?id=") ?>' + row.id;
                            return `<a href="${url_edit}" class="btn btn-success btn-sm"> <i class="fa fa-edit" title="Edit Data Pengeluaran"></i> </a> 
                                <a href="${url_del}" onclick="javascript:return confirm('apakah data ingin dihapus ?');"
                                    class="delete_pengeluaran btn btn-danger btn-sm"> <i class="fa fa-trash" title="Delete Data Pengeluaran"></i> </a>`;

                        }

                    }
                },
            ],
        });
    });
    // $('#dataTable1 tbody').on('click', '.delete_keuangan', function() {
    //     var id = $(this).attr('data-id');
    //     var url_destroy = '<?= base_url("keuangan/delete") ?>/' + id;
    //     swal({
    //         title: 'Hapus Data ! ',
    //         text: "Apakah anda yakin data akan dihapus ? ",
    //         icon: "warning",
    //         buttons: true,
    //         dangerMode: true,
    //     }).then((result) => {
    //         if (result) {
    //             $.ajax({
    //                 url: url_destroy,
    //                 type: "POST",
    //                 timeout: 60000,
    //                 success: function(html) {
    //                     $('#dataTable1').DataTable().ajax.reload();
    //                 },
    //                 'error': function(xmlhttprequest, textstatus, message) {
    //                     if (textstatus === "timeout") {
    //                         alert("request timeout");
    //                     } else {
    //                         alert("request timeout");
    //                     }
    //                 }
    //             });
    //         } else {
    //             swal({
    //                 title: "Dibatalkan !",
    //                 icon: "success",
    //             })
    //         }
    //     });
    // });
</script>
<script>
    var rupiah1 = document.getElementById('jumlah');
    rupiah1.addEventListener('keyup', function(e) {
        // tambahkan 'Rp.' pada saat form di ketik
        // gunakan fungsi formatRupiah() untuk mengubah angka yang di ketik menjadi format angka
        rupiah1.value = formatRupiah(this.value, '');
    });


    /* Fungsi formatRupiah */
    function formatRupiah(angka, prefix) {
        var number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        // tambahkan titik jika yang di input sudah menjadi angka ribuan
        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? '' + rupiah : '');
    }
</script>