<div class="clearfix"></div>
<div id="home">
    <div class="container mt-5">
        <?php
        if (!empty($this->session->flashdata('success'))) {
            echo alert_success($this->session->flashdata('success'));
        }
        if (!empty($this->session->flashdata('failed'))) {
            echo alert_failed($this->session->flashdata('failed'));
        }
        ?>
        <div class="row">
            <div class="col-sm-4">
                <form method="post" action="<?= $url; ?>">
                    <div class="card mt-4 card-rounded">
                        <div class="card-header bg-primary text-white">
                            <?php if (!empty($this->input->get('id'))) { ?>
                                <i class="fa fa-edit"></i> Kategori Bahan
                            <?php } else { ?>
                                <i class="fa fa-plus"></i> Kategori Bahan
                            <?php } ?>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($this->input->get('id'))) { ?>
                                <div class="form-group">
                                    <label for="">Kode Kategori Bahan</label>
                                    <input type="text" required class="form-control" value="<?= $edit->kode_kategori; ?>"
                                        name="kode_kategori" id="kode_kategori" placeholder="">
                                </div>
                                <input type="hidden" name="id" value="<?= $edit->id; ?>">
                                <div class="form-group">
                                    <label for="">Nama Kategori Bahan</label>
                                    <input type="text" required class="form-control" value="<?= $edit->nama_kategori; ?>"
                                        name="nama_kategori" id="nama_kategori" placeholder="">
                                </div>
                                <input type="hidden" name="id" value="<?= $edit->id; ?>">
                            <?php } else { ?>
                                <div class="form-group">
                                    <label for="">Kode Kategori Bahan</label>
                                    <input type="text" required class="form-control" name="kode_kategori" id="kode_kategori"
                                        placeholder="">
                                </div>
                                <div class="form-group">
                                    <label for="">Nama Kategori Bahan</label>
                                    <input type="text" required class="form-control" name="nama_kategori" id="nama_kategori"
                                        placeholder="">
                                </div>
                            <?php } ?>
                        </div>
                        <div class="card-footer text-muted">
                            <div class="float-right">
                                <button class="btn btn-primary btn-md"><i class="fa fa-save"></i> Save</button>
                                <?php if (!empty($this->input->get('id'))) { ?>
                                    <a href="<?= base_url('kategoribahan'); ?>" class="btn btn-danger btn-md">
                                        <i class="fa fa-angle-double-left"></i> Kembali</a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-sm-8">
                <div class="card mt-4 card-rounded">
                    <div class="card-header bg-primary text-white">
                        <i class="fa fa-tags"></i> Kategori Bahan
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="table1">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Kategori</th>
                                        <th>Nama Kategori</th>
                                        <th>#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($kat as $r) {
                                    ?>
                                        <tr>
                                            <td scope="row"><?= $no; ?></td>
                                            <td><?= $r->kode_kategori; ?></td>
                                            <td><?= $r->nama_kategori; ?></td>
                                            <td>
                                                <a href="<?= base_url('kategoribahan?id=' . $r->id); ?>"
                                                    class="btn btn-primary btn-sm">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <?php if ($r->id > 1) { ?>
                                                    <a href="<?= base_url('kategoribahan/delete?id=' . $r->id); ?>"
                                                        class="btn btn-danger btn-sm"
                                                        onclick="javascript:return confirm('Apakah Kategori Bahan ingin dihapus ?')">
                                                        <i class="fa fa-times"></i>
                                                    </a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php $no++;
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>