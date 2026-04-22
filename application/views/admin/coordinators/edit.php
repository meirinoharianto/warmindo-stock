<?php if (! defined('BASEPATH')) exit('No direct script acess allowed'); ?>

<div class="clearfix"></div>
<?php
// Default values
// $login_cabang = !empty($this->input->post('login_cabang')) ? $this->input->post('login_cabang') : '';


?>

<div id="home">
    <div class="container mt-5">
        <?php if (!empty($this->session->flashdata('failed'))) { ?>
            <div class="alert alert-warning alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <?= $this->session->flashdata('failed'); ?>
            </div>
        <?php } ?>
        <?php if (!empty($this->session->flashdata('success'))) { ?>
            <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong><?= $this->session->flashdata('success'); ?></strong>
            </div>
        <?php } ?>
        <form action="<?php echo base_url('coordinators/upd'); ?>" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-sm-7">
                    <div class="card card-rounded">
                        <div class="card-header bg-primary text-white">
                            <i class="fa fa-edit"> </i> Edit Koordinator
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="form-group">
                                <label>Nama Koordinator</label>
                                <input type="text" class="form-control" value="<?= $user->nama_user; ?>" name="nama"
                                    required="required" placeholder="Nama Koordinator">
                            </div>
                            <div class="form-group">
                                <label>Telepon</label>
                                <input type="number" class="form-control" value="<?= $user->telepon; ?>" name="telepon"
                                    required="required" placeholder="Contoh : 089618173609">
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="pull-right">
                                <input type="hidden" class="form-control" value="<?= $user->id; ?>" name="id">
                                <button type="submit" class="btn btn-primary btn-md">
                                    <b><i class="fa fa-edit"></i> Simpan </b></button>
                                <a href="<?= base_url('coordinators'); ?>" class="btn btn-danger btn-md">
                                    <b><i class="fa fa-angle-double-left"></i> Kembali</b></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="card card-rounded">
                        <div class="card-header bg-primary text-white">
                            <i class="fa fa-image"> </i> Cabang
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="form-group">

                                <?php
                                $namacabang = $this->db->where('id <> 1 AND cabang_id <> 99')
                                    ->order_by('length(nama_toko),nama_toko', 'asc')
                                    ->get('profil_toko')
                                    ->result();

                                foreach ($namacabang as $r) {
                                ?>
                                    <div>
                                        <label>

                                            <input type="checkbox" name="login_cabang[]" value="<?= $r->cabang_id; ?>"
                                                <?= (isset($login_cabang) && in_array($r->cabang_id, (array)$login_cabang)) ? 'checked' : '' ?>>
                                            <?= $r->nama_toko; ?>
                                        </label>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>