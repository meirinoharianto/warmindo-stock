<div class="clearfix"></div>
<div id="home">
    <div class="container mt-5">
        <div class="row">
            <div class="col-sm-7 mx-auto">
                <?php
                if (!empty($this->session->flashdata('success'))) {
                    echo alert_success($this->session->flashdata('success'));
                }
                if (!empty($this->session->flashdata('failed'))) {
                    echo alert_failed($this->session->flashdata('failed'));
                }
                ?>
                <form method="post" id="AddClosing">

                    <!-- <form method="POST" action="<?= base_url('closing/simpan'); ?>" enctype="multipart/form-data"> -->
                    <div class="card card-rounded">
                        <div class="card-header bg-primary text-white">
                            <i class="fa fa-plus"></i> <?= $title_web; ?>
                        </div>
                        <div class="card-body">

                            <!-- <div class="form-group">
                                <h4><label for="">Saldo Awal</label></h4>
                                <input type="text" class="form-control" name="saldo_awal" id="saldo_awal">
                            </div> -->
                            <div class="form-group">
                                <h4><label for="">Pemasukan</label></h4>
                                <!-- <input type="text" class="form-control" name="pemasukan" id="pemasukan" value="<?= 'Rp' . number_format($penjualan); ?>" readonly> -->
                            </div>
                            <table class="table table-bordered">
                                <tr>
                                    <td>Cash</td>
                                    <td align="right"><?= 'Rp' . number_format($penjualan_cash); ?></td>
                                </tr>
                                <tr>
                                    <td>QRIS</td>
                                    <td align="right"><?= 'Rp' . number_format($penjualan_qris); ?></td>
                                </tr>
                                <tr>
                                    <td>Online</td>
                                    <td align="right"><?= 'Rp' . number_format($penjualan_online); ?></td>
                                </tr>
                                <tr>
                                    <td><b>TOTAL PEMASUKAN</b></td>
                                    <td align="right"><b><?= 'Rp' . number_format($penjualan); ?></b></td>
                                </tr>
                            </table>
                            <input type="text" class="form-control" name="penjualan" id="penjualan" value="<?= $penjualan ?>" hidden>

                            <input type="text" class="form-control" name="penjualan_cash" id="penjualan_cash" value="<?= $penjualan_cash ?>" hidden>
                            <input type="text" class="form-control" name="penjualan_qris" id="penjualan_qris" value="<?= $penjualan_qris ?>" hidden>
                            <input type="text" class="form-control" name="penjualan_online" id="penjualan_online" value="<?= $penjualan_online ?>" hidden>

                            <br>
                            <div class="form-group">
                                <h4><label for="">Pengeluaran</label></h4>
                                <!-- <input type="text" class="form-control" name="pemasukan" id="pemasukan" value="<?= 'Rp' . number_format($penjualan); ?>" readonly> -->
                            </div>

                            <table class="table table-bordered">


                                <?php
                                $tot_pengeluaran = 0;

                                if ($pengeluaran > 0) {
                                    $transaksi_keluar = $this->db->query("SELECT * FROM transaksi_keluar WHERE  closing_id = 0 AND shift_id = $id_shift AND cabang_id = $id_cabang AND date='" . $tgltrans . "'")->result_array();
                                    foreach ($transaksi_keluar as $trans) {
                                ?>
                                        <tr>
                                            <td><?= $trans['keterangan'] ?></td>
                                            <td align="right"><?= 'Rp' . number_format($trans['jumlah']);  ?></td>
                                        </tr>

                                <?php
                                        $tot_pengeluaran += $trans['jumlah'];
                                    }
                                }
                                ?>
                                <tr>
                                    <td><b>TOTAL PENGELUARAN</b></td>
                                    <td align="right"><b><?= 'Rp' . number_format($tot_pengeluaran); ?></b>
                                        <input type="text" class="form-control" name="pengeluaran" id="pengeluaran" value="<?= $tot_pengeluaran ?>" hidden>
                                    </td>
                                </tr>
                            </table>

                            <br>
                            <div class="form-group">
                                <h4><label for="">Sisa Saldo</label></h4>
                                <!-- <input type="text" class="form-control" name="saldo" id="saldo"> -->
                                <input style="font-weight: bold;text-align:right;" type="text" class="form-control" name="saldo" id="saldo" value="<?= 'Rp' . number_format($penjualan_cash - $tot_pengeluaran); ?>" readonly>
                                <input type="text" class="form-control" name="sisa_saldo" id="sisa_saldo" value="<?= $penjualan_cash - $tot_pengeluaran ?>" hidden>

                            </div>
                            <div class="card-footer text-muted">
                                <div class="float-right">
                                    <!-- <button type="submit" id="prosesClosing" class="btn btn-primary btn-md btn-block mt-2">
                                        <i class="fa fa-save"></i> Simpan Closing
                                    </button> -->
                                    <?php if ($id_cabang > 0) {
                                    ?>
                                        <button type="submit" class="btn btn-primary btn-md">
                                            <b><i class="fa fa-save"></i> Simpan Closing</b></button>
                                    <?php } ?>
                                    <a href="<?= base_url('kasir'); ?>" class="btn btn-danger btn-md">
                                        <b><i class="fa fa-angle-double-left"></i> Kembali</b></a>
                                </div>
                            </div>
                        </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {
        $("#AddClosing").submit(function(e) {
            e.preventDefault();


            swal.fire({
                title: 'Simpan Closing ! ',
                text: "Apakah anda yakin data closing akan disimpan ? ",
                icon: "warning",
                showDenyButton: true,
                confirmButtonText: 'Ya',
                denyButtonText: 'Tidak',
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        url: "<?= base_url('closing/simpanClosing'); ?>",
                        data: new FormData(document.getElementById("AddClosing")),
                        processData: false,
                        contentType: false,
                        cache: false,
                        async: false,
                        // type: 'POST',
                        // processData: false,
                        // contentType: false,
                        // cache: false,
                        // async: false,
                        // type: 'POST',
                        // data: 'a=' + No1 + '&b=' + No2,
                        // timeout: 60000,
                        beforeSend: function() {
                            $('#prosesClosing').attr('disabled', true);
                            $('#prosesClosing').addClass('btn-success').removeClass('btn-primary');
                            $("#prosesClosing").html(
                                '<i class="fas fa-circle-notch fa-spin"></i> Loading');
                        },
                        success: function(result) {
                            // alert("data");
                            if (result == 'gagal') {
                                Swal.fire('Data closing gagal disimpan', '', 'error')
                            } else {
                                // if (result == 'Kurang') {
                                // Swal.fire(result, '', 'success');
                                Swal.fire('Data closing berhasil disimpan', '', 'success');
                                window.location = '<?= base_url('kasir'); ?>';
                                // alert('Pembayaran Anda Kurang Dari Total Bayar !');
                                //     $('#prosesTransaksi').attr('disabled', false);
                                //     $('#prosesTransaksi').addClass('btn-primary').removeClass(
                                //         'btn-success');
                                //     $("#prosesTransaksi").html(
                                //         '<i class="fa fa-save"></i> Simpan Transaksi');
                                // } else {

                                //     $('#AddKasir')[0].reset();
                                //     $('#example1').DataTable().ajax.reload();
                                //     $('#cart_keranjang').load('<?= base_url('kasir/cart'); ?>');
                                //     $('#cart_modal').load('<?= base_url('kasir/cart_table'); ?>');
                                //     var id = result;
                                //     var url_add = '<?= base_url('kasir/show?id='); ?>' + id;
                                //     $.ajax({
                                //         url: url_add,
                                //         timeout: 30000,
                                //         success: function(html) {
                                //             $('.modal').css('overflow-y', 'auto');
                                //             $('#cetak-edit').modal('show');
                                //             $("#edit-content").html(html);
                                //         },
                                //         'error': function(xmlhttprequest, textstatus, message) {
                                //             if (textstatus === "timeout") {
                                //                 alert("request timeout");
                                //             } else {
                                //                 alert("request timeout");
                                //             }
                                //         }
                                //     });
                                // }
                            }

                            $('#prosesClosing').attr('disabled', false);
                            $('#prosesClosing').addClass('btn-primary').removeClass('btn-success');
                            $("#prosesClosing").html('<i class="fa fa-save"></i> Simpan Closing');
                        },
                        error: function(xmlhttprequest, textstatus, message) {
                            if (textstatus === "timeout") {
                                alert("request timeout");
                            } else {
                                alert("request timeout");
                            }

                            $('#prosesClosing').attr('disabled', false);
                            $('#prosesClosing').addClass('btn-primary').removeClass('btn-success');
                            $("#prosesClosing").html('<i class="fa fa-save"></i> Simpan Closing');
                            // $('#AddKasir')[0].reset();
                        }
                    });


                } else {
                    Swal.fire('Data closing batal disimpan', '', 'error')
                }
            });




        });
    });
</script>