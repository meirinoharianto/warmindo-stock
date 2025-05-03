<div class="clearfix"></div>
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
                <div class="card card-rounded">
                    <div class="card-header bg-primary text-white">
                        <i class="fa fa-eye"></i> <?= $title_web; ?>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>

                                    <tr>
                                        <td scope="row">Kategori Bahan</td>
                                        <td>
                                            <?php foreach ($kat as $r) { ?>
                                                <?php if ($r->id == $edit->id_kategori_bahan) {
                                                    echo $r->nama_kategori;
                                                } ?>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td scope="row">Kode Bahan</td>
                                        <td><?= $edit->kode_bahan; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Nama Bahan</td>
                                        <td><?= $edit->nama_bahan; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Stok</td>
                                        <td><?= $edit->stok; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Satuan</td>
                                        <td><?= $edit->konversi; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Harga Pokok</td>
                                        <td>Rp<?= number_format($edit->harga_pokok); ?></td>
                                    </tr>
                                    <!-- <tr>
                                        <td>Harga Jual</td>
                                        <td>Rp<?= number_format($edit->harga_jual); ?></td>
                                    </tr> -->
                                    <tr>
                                        <td>Keterangan</td>
                                        <td><?= $edit->keterangan; ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer text-muted">
                        <div class="float-right">
                            <a href="<?= base_url('bahan'); ?>" class="btn btn-danger btn-md">
                                <b><i class="fa fa-angle-double-left"></i> Kembali</b></a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>