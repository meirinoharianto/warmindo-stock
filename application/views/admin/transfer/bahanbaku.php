<?php foreach ($hasil as $r) { ?>
    <div class="col-50 mb-3">
        <button class="btn btn-outline-secondary btn-sm pt-2 pb-2 btn-menu btn-block pilih" data-id="<?= $r->id; ?>">

            <b style="font-size:10pt;" class="text-primary"><?= $r->nama; ?></b>
            <br>
            (STOK : <?= $r->stok; ?>x / LIMIT: <?= $r->stok_minim; ?>x)
        </button>
    </div>
<?php } ?>

<script>
    $('.pilih').on('click', function(e) {
        var id = $(this).attr('data-id');
        $.ajax({
            url: "<?= base_url('transfer/add_cart'); ?>",
            type: "POST",
            data: {
                "id": id
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
                        text: 'Bahan baku telah mencapai stok limit !',
                    })
                } else if (data.status == 'gagal_open') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal ',
                        text: 'Silahkan lakukan Opening Store terlebih dahulu! ',
                    })
                } else {
                    $('#cart_keranjang').load('<?= base_url('transfer/cart'); ?>');
                    $('#cart_modal').load('<?= base_url('transfer/cart_table'); ?>');

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil ',
                        text: 'Bahan baku telah ditambahkan ke keranjang !',
                    })
                }
                // alert("Berhasil tambah keranjang !");
            },
            'error': function(xmlhttprequest, textstatus, message) {
                if (textstatus === "timeout") {
                    alert("request timeout 1");
                } else {
                    alert("request timeout 2" + message);
                }
            }
        });
    });
</script>