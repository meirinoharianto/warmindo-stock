<?php foreach ($hasil as $r) { ?>
    <div class="col-4 mb-3">
        <!-- <button class="btn btn-outline-secondary btn-sm pt-2 pb-2 btn-menu btn-block " data-id="<?= $r->id; ?>" data-toggle="modal" data-target="#modalAddOn"> -->
        <!-- <?php
                if ($r->gambar !== '-') {
                    if (file_exists(FCPATH . 'assets/image/produk/' . $r->gambar)) {
                ?>
                    <img src="<?= base_url('assets/image/produk/' . $r->gambar); ?>" class="img-fluid w-100 mb-2" style="height:120px;" />
                    <br>
                <?php }
                } else { ?>
                <i class="fa fa-image fa-4x"></i>
                <br>
                <b>Tidak Ada Gambar </b>
                <br>
            <?php } ?> -->
        <!-- ( <?= $r->kategori; ?> ) -->
        <br>
        <div class="row" style="padding-bottom: 100%;margin-bottom: -100%;display: flex;justify-content: center;align-items: center;">
            <b style="font-size:10pt;" class="text-primary"><?= $r->nama; ?></b>
        </div>
        <div class="row" style="padding-bottom: 100%;margin-bottom: -100%;margin-right: 1px;">
            <input type="text" class="form-control" name="hargajual" id="hargajual" placeholder="">

            <b style="font-size:10pt;" class="text-success">Rp<?= number_format($r->harga_jual); ?>,-</b>
            <b style="font-size:10pt;" class="text-primary">(STOK : <?= $r->stok; ?>)</b>



            <!-- (STOK : <?= $r->stok; ?>x / LIMIT: <?= $r->stok_minim; ?>x) -->
            <?php
            if ($r->id_kategori == '2') {
            ?>
                <?php if ($r->harga_jual !== '0') { ?>
                    <button class="btn btn-outline-secondary btn-sm pt-2 pb-2 btn-menu btn-block pilih" data-id="<?= $r->id; ?>" data-addon="<?= $r->harga_jual; ?>" data-nameaddon="Panas/Ori">
                        Panas/Ori
                    </button>
                <?php } ?>

                <?php if ($r->harga_sedang !== '0') { ?>
                    <button class="btn btn-outline-secondary btn-sm pt-2 pb-2 btn-menu btn-block pilih" data-id="<?= $r->id; ?>" data-addon="<?= $r->harga_sedang; ?>" data-nameaddon="Sedang">
                        Sedang
                    </button>
                <?php } ?>
                <?php if ($r->harga_jumbo !== '0') { ?>
                    <button class="btn btn-outline-secondary btn-sm pt-2 pb-2 btn-menu btn-block pilih" data-id="<?= $r->id; ?>" data-addon="<?= $r->harga_jumbo; ?>" data-nameaddon="Jumbo">
                        Jumbo
                    </button>
                <?php } ?>
            <?php } else { ?>
                <button class="btn btn-outline-secondary btn-sm pt-2 pb-2 btn-menu btn-block pilih" data-id="<?= $r->id; ?>" data-addon="<?= $r->harga_jual; ?>" data-nameaddon="">
                    Tambahkan
                </button>
            <?php } ?>
        </div>

    </div>
<?php } ?>

<script>
    $('.pilih').on('click', function(e) {
        var id = $(this).attr('data-id');
        var addon = $(this).attr('data-addon');
        var nameaddon = $(this).attr('data-nameaddon');
        var atasnama = document.getElementById("atas_nama");
        var hargajual = document.getElementById("hargajual");
        $.ajax({
            url: "<?= base_url('kasir/add_cart'); ?>",
            type: "POST",
            data: {
                "id": id,
                "addon": addon,
                "nameaddon": nameaddon,
                "atas_nama": atasnama.value,
                "hargajual": hargajual.value,
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
                        text: 'Menu telah mencapai stok limit !',
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
                    $('#cart_keranjang').load('<?= base_url('kasir/cart'); ?>');
                    $('#cart_modal').load('<?= base_url('kasir/cart_table'); ?>');

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil ',
                        text: 'Menu telah ditambahkan ke keranjang !',
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