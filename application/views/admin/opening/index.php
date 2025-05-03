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
                <form method="POST" action="<?= base_url('opening/simpan'); ?>" enctype="multipart/form-data">
                    <div class="card card-rounded">
                        <div class="card-header bg-primary text-white">
                            <i class="fa fa-plus"></i> <?= $title_web; ?>
                        </div>
                        <div class="card-body">

                            <div class="form-group">
                                <label for="">Saldo Awal</label>
                                <input type="number" class="form-control" name="saldo_awal" id="saldo_awal" placeholder="">
                            </div>

                            <div class="card-footer text-muted">
                                <div class="float-right">
                                    <button type="submit" class="btn btn-primary btn-md">
                                        <b><i class="fa fa-save"></i> Simpan</b></button>
                                    <a href="<?= base_url('menu'); ?>" class="btn btn-danger btn-md">
                                        <b><i class="fa fa-angle-double-left"></i> Kembali</b></a>
                                </div>
                            </div>
                        </div>
                </form>
            </div>
        </div>
    </div>
</div>