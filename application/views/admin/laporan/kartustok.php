<div class="clearfix"></div>
<div id="home">
    <div class="container mt-5">

        <div class="clearfix"></div>
        <?php
        if (!empty($this->session->flashdata('success'))) {
            echo alert_success($this->session->flashdata('success'));
        }
        if (!empty($this->session->flashdata('failed'))) {
            echo alert_failed($this->session->flashdata('failed'));
        }
        // $sql = "SELECT nama_bahan FROM bahan WHERE stok <= stok_minim";
        // $cek = $this->db->query($sql)->num_rows();
        // if ($cek > 0) {
        ?>
        <!-- <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong>
                    Ada <?= $cek; ?> Bahan yang dibawah Stok minim
                    <a href="<?= base_url('bahan/persediaan?cek=limit'); ?>" class="text-dark mr-2">Cek Disini
                    </a>
                </strong>
            </div> -->
        <?php
        // }
        ?>
        <div class="card card-rounded">
            <div class="card-header bg-primary text-white">
                <i class="fa fa-cubes"></i> <?= $title_web; ?>
            </div>
            <div class="card-body">
                <div class="dropdown open mb-3">
                    <button class="btn btn-secondary btn-block dropdown-toggle" type="button" id="triggerId" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php
                        if (!empty($this->input->get('nm'))) {
                            echo $this->input->get('nm');
                        } else {
                            echo 'Semua Cabang';
                        }
                        ?>
                    </button>
                    <button type="button" class="btn btn-primary btn-block btn-md" data-toggle="modal" data-target="#modelIdFilter">
                        <i class="fa fa-search"></i> Pencarian
                    </button>
                    <div class="dropdown-menu" style="width:100%" aria-labelledby="triggerId">
                        <?php foreach ($cab as $r) { ?>
                            <a class="dropdown-item" href="<?= base_url('laporan/kartustok?id=' . $r->id . '&nm=' . $r->nama_toko); ?>">
                                <?= $r->nama_toko; ?></a>
                            <div class="dropdown-divider"></div>
                        <?php } ?>
                        <a class="dropdown-item" href="<?= base_url('laporan/kartustok'); ?>">
                            Semua Cabang</a>
                    </div>

                    <?php
                    if (!empty($this->input->get('shift'))) {
                        $shift_kd = $this->input->get('shift');
                    ?>
                        <span class="badge badge-info">
                            <?php
                            $shift_nm = 'Shift : ';
                            switch ($shift_kd) {
                                case 1:
                                    $shift_nm = 'Shift : PAGI';
                                    break;
                                case 2:
                                    $shift_nm = 'Shift : SORE';
                                    break;
                                case 3:
                                    $shift_nm = 'Shift : MALAM';
                                    break;
                                default:
                                    $shift_nm = '';
                                    echo $shift_kd;
                            }
                            echo $shift_nm;
                            ?>
                        </span>
                    <?php
                    }
                    ?>

                    <?php
                    if (!empty($this->input->get('a'))) {
                        echo $this->input->get('a');
                    }
                    if (!empty($this->input->get('b'))) {
                        echo $this->input->get('b');
                    }
                    ?>
                </div>
                <div class="table-responsive">
                    <?php
                    // if ($this->input->get('id')) {
                    //     $wr = ' WHERE cabang_id = ' . (int)$this->input->get('id') . ' ';
                    //     $url   = base_url('laporan/data_kartustok?id=' . (int)$this->input->get('id'));
                    // } else {
                    //     $wr = '';
                    //     $url   = base_url('laporan/data_kartustok');
                    // }

                    //AWAL PENCARIAN
                    if (in_array($this->session->userdata('ses_level'), array('Admin', 'AdminKasir'))) {
                        if ($this->input->get('id')) {
                            $cabang_id = $this->input->get('id');
                            $wr = ' WHERE cabang_id = ' . (int)$cabang_id . ' ';
                            $url   = base_url('laporan/data_kartustok?id=' . (int)$cabang_id);
                            if (!empty($this->input->get('a'))) {
                                $url = base_url('laporan/data_kartustok?id=' . (int)$cabang_id . '&a=' . $this->input->get('a') . '&b=' . $this->input->get('b'));
                            } else {
                                $wr = '';
                                $url = base_url('laporan/data_kartustok');
                            }
                            // if ($this->input->get('shift')) {
                            //     $shift_id = $this->input->get('shift');

                            //     if (!empty($this->input->get('a'))) {
                            //         $url = base_url('laporan/data_kartustok?cabang=' . $cabang_id . '&shift=' . $shift_id . '&a=' . $this->input->get('a') . '&b=' . $this->input->get('b'));
                            //     } else {
                            //         $url = base_url('laporan/data_kartustok?cabang=' . $cabang_id);
                            //     }
                            // } else {
                            //     if (!empty($this->input->get('a'))) {
                            //         $url = base_url('laporan/data_kartustok?cabang=' . $cabang_id . '&a=' . $this->input->get('a') . '&b=' . $this->input->get('b'));
                            //     } else {
                            //         $url = base_url('laporan/data_kartustok?cabang=' . $cabang_id);
                            //     }
                            // }
                        } else {
                            if ($this->input->get('shift')) {
                                $shift_id = $this->input->get('shift');

                                if (!empty($this->input->get('a'))) {
                                    $url = base_url('laporan/data_kartustok?shift=' . $shift_id . '&a=' . $this->input->get('a') . '&b=' . $this->input->get('b'));
                                } else {
                                    $url = base_url('laporan/data_kartustok?shift=' . $shift_id);
                                }
                            } else {
                                if (!empty($this->input->get('a'))) {
                                    $url = base_url('laporan/data_kartustok?a=' . $this->input->get('a') . '&b=' . $this->input->get('b'));
                                } else {
                                    $wr = '';
                                    $url = base_url('laporan/data_kartustok');
                                }
                            }
                        }
                    } else {
                        $kasir_id = $this->session->userdata('ses_level');

                        // if ($this->input->get('shift')) {
                        //     $shift_id = $this->input->get('shift');

                        //     if (!empty($this->input->get('a'))) {
                        //         $url = base_url('laporan/data_closing?kasir=' . $kasir_id . '&shift=' . $shift_id . '&a=' . $this->input->get('a') . '&b=' . $this->input->get('b'));
                        //     } else {
                        //         $url = base_url('laporan/data_closing?kasir=' . $kasir_id . '&shift=' . $shift_id);
                        //     }
                        // } else {
                        if (!empty($this->input->get('a'))) {
                            $url = base_url('laporan/data_kartustok?kasir=' . $kasir_id . '&a=' . $this->input->get('a') . '&b=' . $this->input->get('b'));
                        } else {
                            $url = base_url('laporan/data_kartustok?kasir=' . $kasir_id);
                        }
                        // }
                    }
                    //AKHIR PENCARIAN
                    ?>
                    <table id="example1" class="table table-bordered table-striped table-sm" width="100%">
                        <thead>
                            <tr>
                                <th>Kode Bahan</th>
                                <th>Nama Bahan</th>
                                <th>Saldo Awal</th>
                                <th>Masuk</th>
                                <th>Keluar</th>
                                <th>Saldo Akhir</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modelIdFilter" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-search"></i> Pencarian Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="GET" action="<?= base_url('laporan/kartustok'); ?>">
                <div class="modal-body">
                    <div class="form-group">

                        <?php
                        // if ($this->session->userdata('ses_level') == 'Admin') {
                        if (in_array($this->session->userdata('ses_level'), array('Admin', 'AdminKasir'))) {

                        ?>
                            <label for="">Shift <small class="text-danger mr-2">( opsional )</small></label>

                            <select class="form-control" name="shift">
                                <option value="" selected>- pilih -</option>
                                <?php $shift = $this->db->get('shift')->result();
                                foreach ($shift as $r) {
                                ?>
                                    <option value="<?= $r->id; ?>"><?= $r->nama . ' (' . $r->open . '-' . $r->close . ')'; ?></option>
                                <?php } ?>
                            </select>
                        <?php
                        }
                        ?>

                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" value="<?= $this->input->get('id') ?>" name="id" placeholder="" hidden>
                        <input type="text" class="form-control" value="<?= $this->input->get('nm') ?>" name="nm" placeholder="" hidden>

                        <label for="">Tanggal Start</label>
                        <input type="date" class="form-control" required value="<?= $this->input->get('a') ?>" name="a" placeholder="">
                    </div>
                    <div class="form-group">
                        <label for="">Tanggal End</label>
                        <input type="date" class="form-control" required value="<?= $this->input->get('b') ?>" name="b" placeholder="">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Cari</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    var tabel = null;
    var base_url = "<?= base_url(''); ?>";
    $(document).ready(function() {
        // $.fn.dataTable.ext.errMode = 'none';
        tabel = $('#example1').DataTable({
            "processing": true,
            "serverSide": true,
            'responsive': true,
            "ordering": true, // Set true agar bisa di sorting
            "order": [
                [0, 'asc']
            ], // Default sortingnya berdasarkan kolom / field ke 0 (paling pertama)
            "ajax": {
                "url": "<?php echo $url; ?>",
                // "url": "<?= base_url('laporan/data_kartustok'); ?>", // URL file untuk proses select datanya
                "type": "POST",
                // success: function(response) {
                //     console.log("Response:", response);
                // },
                // error: function(xhr) {
                //     console.log("Error Status:", xhr.status);
                //     console.log("Error Response:", xhr.responseText);
                // }
            },
            "deferRender": true,
            "aLengthMenu": [
                [100, 150],
                [100, 150]
            ], // Combobox Limit
            "columns": [
                // {
                //     "data": 'id',
                //     "sortable": false,
                //     render: function(data, type, row, meta) {
                //         return meta.row + meta.settings._iDisplayStart + 1;
                //     }
                // },
                {
                    'data': 'kode_bahan'
                },
                {
                    'data': 'nama_bahan'
                },
                {
                    'data': 'awal'
                },
                {
                    'data': 'masuk'
                },
                {
                    'data': 'keluar'
                },
                {
                    'data': 'akhir'
                },
            ]
        });
    });
</script>