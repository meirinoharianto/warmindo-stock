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
            <div class="col-sm-8 mx-auto">
                <?php
                if (!empty($this->session->flashdata('success'))) {
                    echo alert_success($this->session->flashdata('success'));
                }
                if (!empty($this->session->flashdata('failed'))) {
                    echo alert_failed($this->session->flashdata('failed'));
                }
                ?>
                <a href="<?php echo base_url('keuangan/pengeluaran') ?>" class="btn btn-danger btn-md mt-2">
                    <i class="fa fa-arrow-left mr-1"></i> Kembali
                </a>
                <div class="clearfix"></div>
                <br>
                <div class="card card-rounded">
                    <div class="card-header bg-primary text-white">
                        <h5 class="pt-2"> Edit Pengeluaran</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="<?= base_url('keuangan/update_pengeluaran') ?>">
                            <div class="form-group">
                                <label for="">Kategori</label>
                                <!-- <input type="text" class="form-control" name="kategori" id="kategori" placeholder=""> -->
                                <select class="form-control" name="kategori">
                                    <option value="" selected>- pilih -</option>
                                    <?php $kategori = $this->db->get('kategori_keluar')->result();
                                    foreach ($kategori as $r) {
                                        $varselected = '';
                                        if ($r->kategori_keluar == $edit->kategori_keluar) {
                                            $varselected = 'selected';
                                        }
                                    ?>
                                        <!-- <option value="<?= $r->id; ?>"><?= $r->kategori_keluar; ?></option> -->
                                        <option value="<?= $r->kategori_keluar; ?>" <?= $varselected; ?>><?= $r->kategori_keluar; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">No Bon</label>
                                <input type="text" class="form-control" name="no_bon" id="no_bon" value="<?= $edit->no_bon ?>" placeholder="">
                            </div>
                            <div class="form-group">
                                <label for="">Keterangan</label>
                                <input type="text" class="form-control" name="keterangan" id="keterangan" value="<?= $edit->keterangan ?>" placeholder="">
                            </div>
                            <div class="form-group">
                                <label for="">Jumlah</label>
                                <input type="text" class="form-control" name="jumlah" id="jumlah" value="<?= number_format($edit->jumlah, 0, '.', '.') ?>" placeholder="">
                            </div>

                            <div class="float-right">
                                <input type="hidden" name="id" value="<?= $edit->id ?>">
                                <button class="btn btn-primary" id="proses">
                                    <i class="fa fa-edit"></i> Edit</button>
                                <a href="<?= base_url('keuangan/pengeluaran') ?>" class="btn btn-danger">
                                    <i class="fa fa-angle-double-left"></i> Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('#noledger').on('change', function() {
        var vald = $(this).find(':selected').attr('data-val');
        var valj = $(this).find(':selected').attr('data-jen');
        if (valj == 'Pemasukan') {
            $('#jenis option[value="Pengeluaran"]').removeAttr("selected");
            $('#jenis option[value="' + valj + '"]').attr('selected', 'selected');
        } else if (valj == 'Pengeluaran') {
            $('#jenis option[value="Pemasukan"]').removeAttr("selected");
            $('#jenis option[value="' + valj + '"]').attr('selected', 'selected');
        } else {
            $('#jenis option[value="Pemasukan"]').removeAttr("selected");
            $('#jenis option[value="Pengeluaran"]').removeAttr("selected");
        }
    });
</script>
<script>
    var rupiah1 = document.getElementById('jumlah_masuk');
    rupiah1.addEventListener('keyup', function(e) {
        // tambahkan 'Rp.' pada saat form di ketik
        // gunakan fungsi formatRupiah() untuk mengubah angka yang di ketik menjadi format angka
        rupiah1.value = formatRupiah(this.value, '');
    });
    var rupiah2 = document.getElementById('jumlah_keluar');
    rupiah2.addEventListener('keyup', function(e) {
        // tambahkan 'Rp.' pada saat form di ketik
        // gunakan fungsi formatRupiah() untuk mengubah angka yang di ketik menjadi format angka
        rupiah2.value = formatRupiah(this.value, '');
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