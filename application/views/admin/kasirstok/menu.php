<?php foreach ($hasil as $r) { ?>
    <!--
     
 -->
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