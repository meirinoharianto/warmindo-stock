<?php foreach ($hasil as $r) { ?>

    <div class="<?= count($hasil) === 1 ? 'col-lg-8' : 'col-lg-4'; ?> col-md-6 col-sm-12 mb-3">
        <div class="card w-100">
            <!-- <img src="https://via.placeholder.com/300x150" class="card-img-top" alt="Gambar Kolom 1"> -->
            <?php
            if ($r->gambar !== '-') {
                if (file_exists(FCPATH . 'assets/image/produk/' . $r->gambar)) {
            ?>
                    <!-- <img src="<?= base_url('assets/image/produk/' . $r->gambar); ?>" class="img-fluid w-100 mb-2"  /> -->
                    <img src="<?= base_url('assets/image/produk/' . $r->gambar); ?>" class="img-fluid w-100" />

                <?php }
            } else { ?>
                <img src="<?= base_url('assets/image/no-image.png'); ?>" class="img-fluid w-100" />

                <!-- <i class="fa fa-image fa-4x"></i> -->
                <!-- <br>
                <b>Tidak Ada Gambar </b>
                <br> -->
            <?php } ?>
            <div class="card-body text-center">
                <h6><?= $r->nama; ?></h6>
            </div>
            <div class="button-container">
                <!-- <button class="btn btn-primary w-100">Klik Kolom 1</button> -->
                <?php
                if ($r->id_kategori == '2') {
                ?>
                    <?php if ($r->harga_jual !== '0') { ?>
                        <button class="btn btn-primary w-100 mb-2 pilih" data-id="<?= $r->id; ?>" data-addon="<?= $r->harga_jual; ?>" data-nameaddon="Panas/Ori">
                            Panas/Ori
                            <div><?= number_format($r->harga_jual); ?>,-</div>
                        </button>
                    <?php } ?>

                    <?php if ($r->harga_sedang !== '0') { ?>
                        <button class="btn btn-primary w-100 mb-2 pilih" data-id="<?= $r->id; ?>" data-addon="<?= $r->harga_sedang; ?>" data-nameaddon="Sedang">
                            Sedang
                            <div><?= number_format($r->harga_sedang); ?>,-</div>
                        </button>
                    <?php } ?>
                    <?php if ($r->harga_jumbo !== '0') { ?>
                        <button class="btn btn-primary w-100 mb-2 pilih" data-id="<?= $r->id; ?>" data-addon="<?= $r->harga_jumbo; ?>" data-nameaddon="Jumbo">
                            Jumbo
                            <div><?= number_format($r->harga_jumbo); ?>,-</d>
                        </button>
                    <?php } ?>
                <?php } else { ?>
                    <button class="btn btn-primary w-100 mb-2 pilih" data-id="<?= $r->id; ?>" data-addon="<?= $r->harga_jual; ?>" data-nameaddon="">
                        Tambahkan
                        <div><?= number_format($r->harga_jual); ?>,-</div>
                    </button>
                <?php } ?>
            </div>
        </div>
    </div>

<?php } ?>

<script>
    $('.pilih').on('click', function(e) {
        var id = $(this).attr('data-id');
        var addon = $(this).attr('data-addon');
        var hargajual = $(this).attr('data-addon');
        var nameaddon = $(this).attr('data-nameaddon');
        var atasnama = document.getElementById("atas_nama");
        // var hargajual = document.getElementById("hargajual");

        $.ajax({
            url: "<?= base_url('kasirstok/add_cart'); ?>",
            type: "POST",
            data: {
                "id": id,
                "addon": addon,
                "nameaddon": nameaddon,
                "atas_nama": atasnama.value,
                "hargajual": hargajual,
            },
            dataType: 'json',
            timeout: 6000,
            beforeSend: function() {

            },
            success: function(data) {
                if (data.status == 'gagal') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal ',
                        text: 'Menu telah mencapai stok limit !' + data.teks,
                    })
                } else if (data.status == 'gagal_open') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal ',
                        text: 'Silahkan lakukan Opening Store terlebih dahulu! ',
                    })
                } else if (data.status == 'gagal_nama') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal ',
                        text: 'Silahkan masukkan nama pembeli terlebih dahulu ',
                    })
                } else {
                    $('#cart_keranjang').load('<?= base_url('kasirstok/cart'); ?>');
                    $('#cart_modal').load('<?= base_url('kasirstok/cart_table'); ?>');

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil ',
                        // text: 'Menu telah ditambahkan ke keranjang ! \n' + data.teks,
                        html: 'Menu telah ditambahkan ke keranjang !' + data.teks,

                    })
                }
                // alert("Berhasil tambah keranjang !");
                $('#modalAddOn').modal('hide');
            },
            'error': function(xmlhttprequest, textstatus, message) {
                if (textstatus === "timeout") {
                    alert("request timeout");
                } else {
                    alert("request timeout");
                }
            },

        });
    });
</script>