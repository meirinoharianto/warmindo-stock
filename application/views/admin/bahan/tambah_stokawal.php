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
                <form method="POST" action="<?= base_url('bahan/store_stokawal'); ?>" enctype="multipart/form-data">
                    <div class="card card-rounded">
                        <div class="card-header bg-primary text-white">
                            <i class="fa fa-plus"></i> <?= $title_web; ?>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="">Cabang</label>
                                <select class="form-control" name="id_cabang">
                                    <option value="" disabled selected>- pilih -</option>
                                    <?php foreach ($cab as $r) { ?>
                                        <option value="<?= $r->id; ?>"><?= $r->kode_cabang; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">Bahan</label>
                                <select class="form-control" name="id_bahan">
                                    <option value="" disabled selected>- pilih -</option>
                                    <?php foreach ($bhn as $r) { ?>
                                        <option value="<?= $r->id; ?>"><?= $r->nama_bahan; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">Stok Awal</label>
                                <input type="number" class="form-control" name="stok_awal" id="stok_awal"
                                    placeholder="">
                            </div>
                            <!-- <div class="form-group">
                                <label for="">Harga Beli</label>
                                <input type="number" class="form-control" name="harga_beli" id="harga_beli"
                                    placeholder="">
                            </div> -->
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