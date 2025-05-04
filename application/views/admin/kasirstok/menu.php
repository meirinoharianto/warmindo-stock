<?php foreach ($hasil as $r) { ?>
    <div class="col-4 align-content-center text-center pb-2">
        <div class="col-md-12 pt-2 pb-2" style="height:180px;background-color:beige;">
            <b style="font: size 12px;" class="text-primary "><?= $r->nama; ?></b>
            <?php
            if ($r->id_kategori == '2') {
            ?>
                <?php if ($r->harga_jual !== '0') { ?>
                    <button class="btn btn-outline-secondary btn-sm pt-2 pb-2 btn-menu btn-block pilih" data-id="<?= $r->id; ?>" data-addon="<?= $r->harga_jual; ?>" data-nameaddon="Panas/Ori">
                        Panas/Ori - <?= number_format($r->harga_jual); ?>,-
                    </button>
                <?php } ?>

                <?php if ($r->harga_sedang !== '0') { ?>
                    <button class="btn btn-outline-secondary btn-sm pt-2 pb-2 btn-menu btn-block pilih" data-id="<?= $r->id; ?>" data-addon="<?= $r->harga_sedang; ?>" data-nameaddon="Sedang">
                        Sedang - <?= number_format($r->harga_sedang); ?>,-
                    </button>
                <?php } ?>
                <?php if ($r->harga_jumbo !== '0') { ?>
                    <button class="btn btn-outline-secondary btn-sm pt-2 pb-2 btn-menu btn-block pilih" data-id="<?= $r->id; ?>" data-addon="<?= $r->harga_jumbo; ?>" data-nameaddon="Jumbo">
                        Jumbo - <?= number_format($r->harga_jumbo); ?>,-
                    </button>
                <?php } ?>
            <?php } else { ?>
                <!-- <button class="btn btn-outline-secondary btn-sm pt-2 pb-2 btn-menu btn-block pilih" data-id="<?= $r->id; ?>" data-addon="<?= $r->harga_jual; ?>" data-nameaddon=""> -->
                <button class="btn btn-secondary btn-sm mt-auto" data-id="<?= $r->id; ?>" data-addon="<?= $r->harga_jual; ?>" data-nameaddon="">
                    Tambahkan - <?= number_format($r->harga_jual); ?>,-
                </button>
            <?php } ?>
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