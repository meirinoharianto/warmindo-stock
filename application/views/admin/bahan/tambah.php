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
                <form method="POST" action="<?= base_url('bahan/store'); ?>" enctype="multipart/form-data">
                    <div class="card card-rounded">
                        <div class="card-header bg-primary text-white">
                            <i class="fa fa-plus"></i> <?= $title_web; ?>
                        </div>
                        <div class="card-body">
                            <!-- <div class="form-group">
                                <label for="">Kategori Bahan</label>
                                <select class="form-control" name="id_kategori_bahan">
                                    <option value="" disabled selected>- pilih -</option>
                                    <?php foreach ($kat as $r) { ?>
                                        <option value="<?= $r->id; ?>"><?= $r->nama_kategori; ?></option>
                                    <?php } ?>
                                </select>
                            </div> -->
                            <div class="form-group">
                                <label for="">Kode Bahan</label>
                                <input type="text" class="form-control" value="<?= $kode; ?>" name="kode_bahan"
                                    id="kode_bahan" placeholder="">
                            </div>

                            <div class="form-group">
                                <label for="">Nama Bahan</label>
                                <input type="text" class="form-control" name="nama_bahan" id="nama_bahan" placeholder="">
                            </div>
                            <div class="form-group">
                                <label for="">Konversi</label>
                                <input type="number" class="form-control" name="konversi" id="konversi" placeholder="">
                            </div>
                            <!-- <div class="form-group">
                                <label for="">Harga Pokok</label>
                                <input type="number" class="form-control" name="harga_pokok" id="harga_pokok"
                                    placeholder="">
                            </div> -->
                            <!-- <div class="form-group">
                                <label for="">Harga Jual</label>
                                <input type="number" class="form-control" name="harga_jual" id="harga_jual"
                                    placeholder="">
                            </div> -->
                            <div class="form-group">
                                <label for="">Keterangan</label>
                                <textarea class="form-control" name="keterangan" id="keterangan"
                                    placeholder=""></textarea>
                            </div>
                            <div class="form-group">
                                <label for="">Stok Minim</label>
                                <br>
                                <input type="number" name="stok_minim" value="" required placeholder="" />
                            </div>
                        </div>
                        <div class="card-footer text-muted">
                            <div class="float-right">
                                <button type="submit" class="btn btn-primary btn-md">
                                    <b><i class="fa fa-save"></i> Submit</b></button>
                                <a href="<?= base_url('bahan'); ?>" class="btn btn-danger btn-md">
                                    <b><i class="fa fa-angle-double-left"></i> Kembali</b></a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>