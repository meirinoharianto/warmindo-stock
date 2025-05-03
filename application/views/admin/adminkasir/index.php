<div class="clearfix"></div>
<div id="adminkasir">
    <div class="container mt-5">
        <div class="row">

            <?php if (!empty($this->input->post('thn'))) {
                $thn = $this->input->post('thn');
            } else {
                $thn = date('Y');
            } ?>
            <?php if (!empty($this->input->post('idcabang'))) {
                $idcabang = $this->input->post('idcabang');
            } else {
                $idcabang = 0;
            } ?>
            <div class="col-sm-12">
                <div class="card card-rounded mb-4 mt-3">
                    <div class="card-header bg-primary text-white">
                        Laporan Penjualan <?= $thn; ?>
                    </div>
                    <div class="card-body text-center">
                        <div class="row">
                            <div class="col-sm-5">
                                <form method="post" action="<?= base_url('adminkasir') ?>">
                                    <div class="table-responsive">
                                        <table>
                                            <tr>
                                                <td>
                                                    <select name="idcabang" class="form-control">
                                                        <option value="">- Pilih Cabang -</option>
                                                        <?php
                                                        $this->db->order_by('length(nama_toko),nama_toko', 'asc');

                                                        $namacabang = $this->db->get_where('profil_toko', 'id<>1')->result();
                                                        foreach ($namacabang as $r) {
                                                        ?>
                                                            <!-- <a href="<?= base_url('laporan/closing?cabang=' . $r->cabang_id); ?>" class="btn btn-info btn-sm mb-1 w-100"><?= $r->nama_toko; ?></a> -->
                                                            <option value="<?= $r->cabang_id; ?>" <?php if ($idcabang == $r->cabang_id) { ?> selected
                                                                <?php } ?>><?= $r->nama_toko; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="thn" class="form-control">
                                                        <option value="">- Pilih Tahun Grafik -</option>
                                                        <?php
                                                        $thn_skr = date('Y');
                                                        for ($x = $thn_skr; $x >= 2021; $x--) {
                                                        ?>
                                                            <option value="<?= $x; ?>" <?php if ($thn == $x) { ?> selected
                                                                <?php } ?>><?= $x; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <button type="submit" class="btn btn-primary btn-md">
                                                        <i class="fa fa-search"></i></button>
                                                </td>
                                                <td>
                                                    <a href="<?= base_url('home') ?>" class="btn btn-success btn-md">
                                                        <i class="fa fa-refresh"></i> </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <canvas id="line-chart" height="180" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <script>
        var linechart = document.getElementById('line-chart');
        var chart = new Chart(linechart, {
            type: 'bar',
            data: {
                labels: [
                    'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'
                ], // Merubah data tanggal menjadi format JSON
                datasets: [{
                    label: "Total Transaksi",
                    data: [
                        <?php
                        // php mencari produk
                        $cabang = $idcabang;
                        for ($n = 1; $n <= 12; $n++) {
                            if ($n > 9) {
                                $period = $thn . '-' . $n;
                            } else {
                                $period = $thn . '-' . '0' . $n;
                            }
                            if ($this->session->userdata('ses_level') == 'AdminKasir') {
                                // $penjualan = $this->db->query('SELECT SUM(qty) as qty FROM transaksi_produk WHERE cabang_id = ?', [$cabang], ' AND periode = ?', [$period])->row();
                                $penjualan = $this->db->query('SELECT SUM(grandtotal) as qty FROM transaksi WHERE cabang_id = ? AND periode = ?', [$cabang, $period])->row();
                            } else {
                                $penjualan = $this->db->query('SELECT SUM(qty) as qty FROM transaksi_produk 
                                        WHERE periode = ? AND kasir_id = ?', [$period, $this->session->userdata('ses_id')])->row();
                            }
                        ?>
                            <?= $penjualan->qty; ?>,
                        <?php } ?>
                    ],
                    borderColor: '#3c73a8',
                    backgroundColor: '#3c73a8',
                    borderWidth: 4,
                }, ],
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        min: 0,
                        max: 200000000,
                    }
                }
            },
        });
    </script>