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
                <form method="POST" action="<?= base_url('menuutama/update_penggunaanbahan'); ?>" enctype="multipart/form-data">
                    <div class="card card-rounded">
                        <div class="card-header bg-primary text-white">
                            <i class="fa fa-plus"></i> <?= $title_web; ?>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <input type="hidden" class="form-control" value="<?= $edit->id; ?>" name="id" id="id" placeholder="" />
                            </div>
                            <!-- <h5><b><?= $menu->nama; ?></b></h5> -->
                            <div class="form-group">
                                <label for="">Bahan</label>
                                <input type="text" class="form-control" value="<?= $edit->menu_id; ?>" name="id_menu"
                                    id="id_menu" placeholder="" hidden>

                                <select class="form-control" name="id_bahan">
                                    <option value="" disabled selected>- pilih -</option>
                                    <?php foreach ($kat as $r) { ?>
                                        <option value="<?= $r->id; ?>" <?php if ($r->id == $edit->bahan_id) {
                                                                            echo 'selected';
                                                                        } ?>>
                                            <?= $r->nama_bahan; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <!-- 
                            <div class="form-group">
                                <label for="">Nama Bahan</label>
                                <input type="text" class="form-control" name="nama_bahan" id="nama_bahan" placeholder="">
                            </div>
                            <div class="form-group">
                                <label for="">Satuan</label>
                                <input type="text" class="form-control" name="satuan" id="satuan" placeholder="">
                            </div> -->
                            <div class="form-group">
                                <label for="">Ori</label>
                                <input type="number" class="form-control" value="<?= $edit->jumlah; ?>" name="jumlah" id="jumlah"
                                    placeholder="">
                            </div>
                            <div class="form-group">
                                <label for="">Sedang</label>
                                <input type="number" class="form-control" value="<?= $edit->sedang; ?>" name="sedang" id="sedang"
                                    placeholder="">
                            </div>
                            <div class="form-group">
                                <label for="">Jumbo</label>
                                <input type="number" class="form-control" value="<?= $edit->jumbo; ?>" name="jumbo" id="jumbo"
                                    placeholder="">
                            </div>
                            <!-- <div class="form-group">
                                <label for="">Harga Jual</label>
                                <input type="number" class="form-control" name="harga_jual" id="harga_jual"
                                    placeholder="">
                            </div> -->
                            <!-- <div class="form-group">
                                <label for="">Keterangan</label>
                                <textarea class="form-control" name="keterangan" id="keterangan"
                                    placeholder=""></textarea>
                            </div>
                            <div class="form-group">
                                <label for="">Stok Minim</label>
                                <br>
                                <input type="number" name="stok_minim" value="" required placeholder="" />
                            </div> -->
                        </div>
                        <div class="card-footer text-muted">
                            <div class="float-right">
                                <button type="submit" class="btn btn-primary btn-md">
                                    <b><i class="fa fa-save"></i> Submit</b></button>
                                <a href="<?= base_url('menuutama/penggunaanbahan/' . $edit->menu_id); ?>" class="btn btn-danger btn-md">
                                    <b><i class="fa fa-angle-double-left"></i> Kembali</b></a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>