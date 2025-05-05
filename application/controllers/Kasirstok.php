<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kasirstok extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        //validasi jika user belum login
        $this->load->helper(array('form', 'url'));
        $this->load->model('M_Admin');
        $this->load->model('M_Datatables');
        $this->load->helper('tgl_default');
        $this->load->helper('alert');
        $this->load->library('pagination');
        if ($this->session->userdata('masuk_sistem') != true) {
            $url = base_url('login');
            redirect($url);
        }
    }

    public function index()
    {
        $cabang_id = $this->session->userdata('ses_cabang_id');
        $kode_cabang = $this->session->userdata('ses_kode_cabang');
        $last_trans = $this->db->query("SELECT * FROM transaksi WHERE cabang_id = $cabang_id AND no_bon like '" . $kode_cabang . "/" . date('ym') . "/%" . "' ORDER BY no_bon DESC LIMIT 1");
        $atas_nama = $this->session->userdata('ses_atas_nama');

        if ($last_trans->num_rows() > 0) {
            $last_trans_row = $last_trans->row();
            $no_bon_last = explode('/', $last_trans_row->no_bon);
            $no_bon_next = (int) $no_bon_last[2];
            $no_bon_next++;
        } else {
            $no_bon_next = 1;
        }

        $this->data = [
            'title_web' => 'Kasir Stok',
            'kat'       => $this->db->get('kategori')->result(),
            'no_bon'    => $kode_cabang . "/" . date('ym') . "/" . sprintf("%05s",  $no_bon_next),
            'atas_nama'    => $atas_nama,
            'pp'        => $this->db->get_where('profil_toko', ['cabang_id' => $cabang_id])->row(),
            'halperpage' => 12
        ];

        $this->load->view('layout/headerkasir', $this->data);
        $this->load->view('admin/kasirstok/index', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function store()
    {
        $shift_nilai = $this->db->query("SELECT MAX(open) as 'nilai_max', MIN(close) as 'nilai_min' FROM shift")->row();
        $nilai_max = $shift_nilai->nilai_max;
        $nilai_min = $shift_nilai->nilai_min;
        $shift_now = $this->db->query("SELECT * FROM shift WHERE ('" . date('H:i') . "' BETWEEN open AND close) OR 
           ((open>='" . $nilai_max . "' AND '" . date('H:i') . "' BETWEEN '" . $nilai_max . "' AND '24:00') OR 
           (close<='" . $nilai_min . "' AND '" . date('H:i') . "' BETWEEN '00:00' AND '" . $nilai_min . "')) ")->row();

        $shift_login = $this->session->userdata('ses_shift');

        if ($shift_login <> $shift_now->id) {

            $shift_user = $this->db->query("SELECT * FROM shift WHERE id = " . $shift_login)->row();
            if (date("H", strtotime($shift_user->close)) == date("H", strtotime($shift_now->open))) {
                if (date("H:i") < date("H:i", strtotime($shift_now->open) + 1800) || date("H:i") > date("H:i", strtotime($shift_now->open))) {
                } else {
                    // $this->session->sess_destroy();
                    echo 'NoAkses';
                    exit;
                }
            } else {
                // $this->session->sess_destroy();
                echo 'NoAkses';
                exit;
            }
            // $this->session->sess_destroy();
            echo 'NoAkses';
            exit;
        }

        $no_bon = $this->input->post('no_bon');

        $voucher    = $this->input->post('voucher');
        $grandtotal = $this->input->post('grandtotal');
        $kembali    = $this->input->post('kembaliBayar');
        $dibayar    = $this->input->post('dibayar');
        $cabang_id = $this->session->userdata('ses_cabang_id');

        if (!empty($grandtotal)) {
            $grandtotal = preg_replace('/[^a-zA-Z0-9\']/', '', $grandtotal);
        } else {
            $grandtotal = 0;
        }

        if (!empty($voucher)) {
            $voucher = preg_replace('/[^a-zA-Z0-9\']/', '', $voucher);
        } else {
            $voucher = 0;
        }

        if (!empty($kembali)) {
            $kembali = preg_replace('/[^a-zA-Z0-9\']/', '', $kembali);
        } else {
            $kembali = 0;
        }

        if (!empty($dibayar)) {
            $dibayar = preg_replace('/[^a-zA-Z0-9\']/', '', $dibayar);
        } else {
            $dibayar = 0;
        }

        if (in_array(
            htmlspecialchars($this->input->post("status", true), ENT_QUOTES),
            ['Cash', 'QRIS', 'Online']
        )) {
            if ($dibayar == 0) {
                echo 'Kurang';
                exit;
            } else {
                if ($dibayar < $grandtotal) {
                    echo 'Kurang';
                    exit;
                }
            }
        } else {
            echo 'Kurang';
            exit;
        }
        // $shift_nilai = $this->db->query("SELECT MAX(open) as 'nilai_max', MIN(close) as 'nilai_min' FROM shift")->row();
        // $nilai_max = $shift_nilai->nilai_max;
        // $nilai_min = $shift_nilai->nilai_min;
        // $shift = $this->db->query("SELECT * FROM shift WHERE ('" . date('H:i') . "' BETWEEN open AND close) OR 
        // ((open>='" . $nilai_max . "' AND '" . date('H:i') . "' BETWEEN '" . $nilai_max . "' AND '24:00') OR 
        // (close<='" . $nilai_min . "' AND '" . date('H:i') . "' BETWEEN '00:00' AND '" . $nilai_min . "')) ")->row();

        $shift_now = $this->session->userdata('ses_shift');
        $shift = $this->db->get_where('shift', ['id' => $shift_now])->row();
        $shift_nama = $shift->nama;
        // if (isset($cabang_id)) {
        // }
        $tanggal = date('Y-m-d H:i:s');
        $waktu = date('H');
        $tgl = date_create($tanggal);
        if (($shift_now == 3) && ($waktu <= 7)) {
            date_sub($tgl, date_interval_create_from_date_string("1 days"));
        }

        $cekclosing = $this->db->query("SELECT * FROM closing WHERE date='" . date_format($tgl, "Y-m-d") . "' AND cabang_id='" . $cabang_id . "' AND shift_id=" . $shift_now);
        if ($cekclosing->num_rows() > 0) {
            $this->session->set_flashdata("failed", '<strong>Transaksi gagal disimpan,</strong> Data tanggal ' . date_format($tgl, "Y-m-d") . ' sudah diclosing ! ');
            echo 'closed';
            exit;
        }


        $hasil_cart = $this->db->get_where('keranjang', ['login_id' => $this->session->userdata('ses_id')])->result_array();
        $total_qty = 0;
        $stok = 0;
        $grandmodal = 0;
        foreach ($hasil_cart as $isi) {

            $kode_menu = $isi['kode_menu'];
            $qty = $isi['qty'];
            $total_qty += $qty;
            // $row = $this->db->query('select * from menu_utama where id = ?', [$isi['id_menu']])->row();
            // $stok = $row->stok - $qty;

            // $up_stok[] = array(
            //     'id' => $isi['id_menu'],
            //     'stok' => $stok
            // );


            $data_jual[] = array(
                'no_bon' => $no_bon,
                'kode_menu' => $kode_menu,
                'nama_menu' => $isi['nama'],
                'kategori'  => $isi['kategori'],
                'qty' => $qty,
                'harga_beli' => $isi['harga_beli'],
                'harga_jual' => $isi['harga_jual'],
                'keterangan' => $isi['keterangan'],
                'created_at' => date('Y-m-d H:i:s'),
                'date' => date_format($tgl, 'Y-m-d'),
                'periode' => date_format($tgl, 'Y-m'),
                'year' => date_format($tgl, 'Y'),
                'cabang_id' => $cabang_id
            );
            $grandmodal += $isi['harga_beli'] * $qty;
        }

        // update stok
        // $total_array = count($up_stok);
        // if ($total_array != 0) {
        //     $this->db->update_batch('menu', $up_stok, 'id');
        // }

        // insert penjualan
        $this->db->trans_start();

        $total_array = count($data_jual);
        if ($total_array != 0) {
            $this->db->insert_batch('transaksi_produk', $data_jual);
        } else {
            exit;
        }

        // insert transaksi
        $data_trx = array(
            'no_bon' => $no_bon,
            'kasir_id' => $this->session->userdata('ses_id'),
            'customer_id' => htmlspecialchars($this->input->post("customer_id", true), ENT_QUOTES),
            'atas_nama' => htmlspecialchars($this->input->post("atas_nama", true), ENT_QUOTES),
            'pesanan' => htmlspecialchars($this->input->post("pesanan", true), ENT_QUOTES),
            'status' => htmlspecialchars($this->input->post("status", true), ENT_QUOTES),
            'diskon' => htmlspecialchars($this->input->post("diskon", true), ENT_QUOTES),
            'pajak' => htmlspecialchars($this->input->post("pajak", true), ENT_QUOTES),
            'voucher' => $voucher,
            'grandmodal' => $grandmodal,
            'grandtotal' => $grandtotal,
            'total_qty' => $total_qty,
            'dibayar' => $dibayar,
            'created_at' => date('Y-m-d H:i:s'),
            'date' => date_format($tgl, 'Y-m-d'),
            'periode' => date_format($tgl, 'Y-m'),
            'year' => date_format($tgl, 'Y'),
            'shift_id' => $shift_now,
            'cabang_id' => $cabang_id,
        );
        $this->db->insert('transaksi', $data_trx);
        $hasil_transaksi_id = $this->db->insert_id();

        // $hasil_transaksi = $this->db->get_where('transaksi', ['no_bon' => $no_bon])->result_array();

        //save kartu stok
        foreach ($hasil_cart as $isi) {
            $id_menu = $isi['id_menu'];
            $ket = $isi['keterangan'];

            $penggunaan_bahan = $this->db->get_where('menu_penggunaanbahan', ['menu_id' => $id_menu])->result_array();
            // $total_bahan = count($penggunaan_bahan);

            // if ($total_bahan != 0) {
            if (!empty($penggunaan_bahan)) {

                foreach ($penggunaan_bahan as $isi_bahan) {

                    if ($ket == 'Sedang') {
                        $jumlah_bahan = $isi_bahan['sedang'];
                    } else if ($ket == 'Jumbo') {
                        $jumlah_bahan = $isi_bahan['jumbo'];
                    } else {
                        $jumlah_bahan = $isi_bahan['jumlah'];
                    }

                    $this->db->where('bahan_id', $isi_bahan['bahan_id']);

                    $this->db->where('cabang_id', $cabang_id);

                    $query = $this->db->get('bahan_stok');
                    // echo $this->db->last_query();
                    $total_stok = 0;
                    // echo json_encode([
                    //     'status' => 'empty',
                    //     'message' => 'No stock data found'
                    // ]);
                    // exit;
                    if (!$query) {
                        echo json_encode([
                            'status' => 'empty',
                            'message' => 'Database kosong.'
                        ]);
                        exit;
                    }

                    if ($query->num_rows() == 0) {
                        echo json_encode([
                            'status' => 'empty',
                            'message' => 'No stock data found'
                        ]);
                        exit;
                    }


                    if ($query->num_rows() == 0) { // jika belum ada

                        $data_stok = [
                            'cabang_id'   => $cabang_id,
                            'bahan_id'     => $isi_bahan['bahan_id'],
                            'jumlah_stok'     => 0 - ($isi['qty'] * $jumlah_bahan)
                        ];

                        $this->db->insert("bahan_stok", $data_stok);
                    } else {
                        $querystok = $query->row_array();

                        $total_stok = $querystok['jumlah_stok'];

                        $data_stok = [
                            'cabang_id'   => $cabang_id,
                            'bahan_id'     => $isi_bahan['bahan_id']
                            // 'jumlah_stok'     => 'jumlah_stok' - $isi['qty']
                        ];
                        $this->db->set('jumlah_stok', 'jumlah_stok-' . ($isi['qty'] * $jumlah_bahan), false);
                        $this->db->where('bahan_id', $isi_bahan['bahan_id']);
                        $this->db->where('cabang_id', $cabang_id);
                        $this->db->update("bahan_stok", $data_stok);
                    }; // jika sudah ada

                    $data_kartustok[] = array(
                        'cabang_id'   => $cabang_id,
                        'bahan_id'     => $isi_bahan['bahan_id'],
                        'tipe_transaksi'     => "Penjualan " . $shift_nama,
                        'transaksi_id'     => $hasil_transaksi_id,
                        'jumlah_perubahan'     => 0 - ($isi['qty'] * $jumlah_bahan),
                        'harga_beli'   => 0,
                        'jumlah_harga'   => 0,
                        'total_stok'   => $total_stok - ($isi['qty'] * $jumlah_bahan),
                        'total_jumlah_harga'   => 0,
                        'tanggal' => date_format($tgl, 'Y-m-d'),
                        'created_at'    => date('Y-m-d H:i:s'),
                        'periode' => date_format($tgl, 'Y-m'),
                        'tahun' => date_format($tgl, 'Y'),
                        'shift_id'     => $shift_now,
                        'login_id'     => $this->session->userdata('ses_id')
                    );
                }
            }
        }

        // $total_kartustok = count($data_kartustok);
        // if ($total_kartustok != 0) {

        if (!empty($data_kartustok)) {
            $this->db->insert_batch('bahan_kartustok', $data_kartustok);
        }

        $this->db->where('login_id', $this->session->userdata('ses_id'));
        $this->db->delete('keranjang');

        $this->db->trans_complete();

        $this->session->set_userdata('ses_atas_nama', '');

        echo $no_bon;
    }

    public function show()
    {
        $cabang_id = $this->session->userdata('ses_cabang_id');

        $no_bon = $this->input->get('id');
        $t  = $this->db->query("SELECT customer.nama, customer.hp, transaksi.* FROM 
                transaksi LEFT JOIN customer ON
                transaksi.customer_id=customer.id 
                WHERE transaksi.no_bon = ?", [$no_bon])->row();

        $tp = $this->db->get_where("transaksi_produk", ['no_bon' => $no_bon])->result();
        $this->data = [
            't'  => $t,
            'tp' => $tp,
            'pp' => $this->db->get_where('profil_toko', ['cabang_id' => $cabang_id])->row()
        ];

        $this->load->view('admin/kasirstok/cetak', $this->data);
    }

    public function print2()
    {
        $cabang_id = $this->session->userdata('ses_cabang_id');
        $no_bon = $this->input->get('id');

        $t  = $this->db->query("SELECT customer.nama, transaksi.* FROM 
        transaksi LEFT JOIN customer ON
        transaksi.customer_id=customer.id 
        WHERE transaksi.no_bon = ?", [$no_bon])->row();

        $tp = $this->db->get_where("transaksi_produk", ['no_bon' => $no_bon])->result();

        $this->data = [
            't'  => $t,
            'tp' => $tp,
            'pp' => $this->db->get_where('profil_toko', ['cabang_id' => $cabang_id])->row()
        ];
        $this->load->view('admin/kasirstok/print2', $this->data);
    }
    public function print()
    {
        $cabang_id = $this->session->userdata('ses_cabang_id');

        $id = $this->input->post('id');
        $os = $this->input->post('os');
        $print = $this->input->post('print');
        $driver = $this->input->post('driver');
        $cetak = $this->input->post('cetak');

        $no_bon = $this->input->get('id');
        $t  = $this->db->query("SELECT customer.nama, transaksi.* FROM 
                transaksi LEFT JOIN customer ON
                transaksi.customer_id=customer.id 
                WHERE transaksi.no_bon = ?", [$no_bon])->row();

        $tp = $this->db->get_where("transaksi_produk", ['no_bon' => $no_bon])->result();
        $this->data = [
            't'  => $t,
            'tp' => $tp,
            'cetak' => $cetak,
            'os' => $os,
            'pp' => $this->db->get_where('profil_toko', ['cabang_id' => $cabang_id])->row()
        ];

        $this->load->view('admin/kasirstok/print', $this->data);
    }

    public function add_cart()
    {
        $cabang_id = $this->session->userdata('ses_cabang_id');

        // $closing = $this->db->query('select * from closing where status = "OPEN"')->row();
        // if (!$closing) {
        //     echo json_encode(['status' => 'gagal_open']);
        //     exit;
        // }
        $id = (int)$this->input->post('id');
        $addon = (int)$this->input->post('addon');
        $atasnama = $this->input->post('atas_nama');
        $hargajual = $this->input->post('hargajual');
        $hargacustom = $this->input->post('hargacustom');
        $nameaddon = $this->input->post('nameaddon');
        $addon_nama = '(' . $nameaddon . ')';

        $this->session->set_userdata('ses_atas_nama', $atasnama);
        // (int)$this->input->post('id')

        // if ($atasnama == "") {
        //     echo json_encode(['status' => 'gagal_nama']);
        //     exit;
        // }
        // echo json_encode(['status' => 'gagal', 'teks' => 'SELECT kategori.kategori, menu_utama.* FROM menu_utama LEFT JOIN kategori ON menu_utama.id_kategori = kategori.id 
        // 	WHERE menu_utama.id=' . $id]);
        // exit;

        $menu = $this->db->query('SELECT kategori.kategori, menu_utama.* 
        FROM menu_utama LEFT JOIN kategori ON menu_utama.id_kategori = kategori.id 
			WHERE menu_utama.id=' . $id)->row();

        // AWAL CEK STOK
        $stok = 0;
        $select = '';
        if ($nameaddon == 'Sedang') {
            $select = 'pb.sedang';
        } else if ($nameaddon == 'Jumbo') {
            $select = 'pb.jumbo';
            // } else if ($nameaddon == 'Non') {

        } else {
            $select = 'pb.jumlah';
        }

        // $menu = $this->db->query('SELECT bs.jumlah_stok as stok, ' . $select . ' as jumlah '.
        //                        'FROM menu_penggunaanbahan pb '.
        //                        'LEFT JOIN bahan b ON b.id = pb.bahan_id '.
        //                        'LEFT JOIN bahan_stok bs ON bs.id = pb.bahan_id '.
        //                        'WHERE p' )


        // $this->db->select('pb.bahan_id,bs.jumlah_stok,pb.jumlah,pb.sedang,pb.jumbo');
        $this->db->select('pb.bahan_id,bs.jumlah_stok,' . $select . ' as jumlah');
        $this->db->from('menu_penggunaanbahan pb');
        $this->db->join('bahan_stok bs', 'bs.bahan_id = pb.bahan_id');
        $this->db->where('pb.menu_id', $menu->id);
        $this->db->where('bs.cabang_id', $cabang_id);

        $qstok = $this->db->get()->result();


        if (!empty($qstok)) {
            $stock_list = [];
            foreach ($qstok as $row) {
                $stock_list[] = floor($row->jumlah_stok / $row->jumlah); // Maximum possible products
            }
            $stok = !empty($stock_list) ? min($stock_list) : 0; // Return lowest possible stock

        } else {
            $stok = 0;
        }
        // AKHIR CEK STOK
        // echo json_encode(['status' => 'gagal', 'teks' => $stok]);
        // exit;

        // $plusaddon = $menu->harga_jual + $addon;
        $hjual = $addon;
        $keranjang = $this->db->get_where('keranjang', ['id_menu' => $menu->id, 'harga_jual' => $hjual, 'login_id' => $this->session->userdata('ses_id')])->row();
        $item = array(
            'id_menu' => $menu->id,
            'kode_menu' => $menu->kode_menu,
            'kategori' => $menu->kategori,
            'nama' => $menu->nama . $addon_nama,
            'gambar' => $menu->gambar,
            'harga_beli'  => $menu->harga_pokok,
            'keterangan'  => $nameaddon,
            // 'harga_jual'  => $menu->harga_jual + $addon,
            // 'harga_jual'  => $hjual,
            'harga_jual'  => $hargajual,
            'login_id'  => $this->session->userdata('ses_id')
        );
        // $stok = $menu->stok - $menu->stok_minim;

        // $stok = 99;
        // $stok = $this->db->get_where('keranjang', ['id_menu' => $menu->id, 'harga_jual' => $hjual, 'login_id' => $this->session->userdata('ses_id')])->row();

        // if ($menu->stok <= 0) {
        // if ($stok <= 0) {
        //     echo json_encode(['status' => 'gagal']);
        // } else {

        $teks = '';
        if (!$keranjang) {
            if ($stok <= 0) {
                $teks = '<p style="color:red;"> Stok menu <b>' . $menu->nama . ' habis </b>. Silahkan tambahkan stok bahan </p>';
            }
            $this->db->set('qty', 1);
            //tambah add on
            $this->db->insert('keranjang', $item);
            echo json_encode(['status' => 'sukses', 'teks' => $teks]);
        } else {
            $qty = $keranjang->qty + 1;
            if ($stok <= 0) {
                $teks = '<p style="color:red;"> Stok menu <b>' . $menu->nama . ' habis </b>. Silahkan tambahkan stok bahan </p>';
            } else if ($stok < $qty) {
                $teks = '<p style="color:red;"> Stok menu <b>' . $menu->nama . ' kurang </b>. Silahkan tambahkan stok bahan </p>';
            }
            // if ($stok >= $qty) {
            $this->db->set('qty', $qty);
            $this->db->where('id_menu', $menu->id);
            $this->db->where('login_id', $this->session->userdata('ses_id'));
            $this->db->update('keranjang', $item);
            // echo json_encode(['status' => 'sukses']);
            echo json_encode(['status' => 'sukses', 'teks' => $teks]);

            // } else {
            //     echo json_encode(['status' => 'gagal']);
            // }
        }
        // }
    }



    public function cart()
    {
        $sql = "SELECT * FROM keranjang WHERE login_id = ? ORDER BY id ASC";
        $keranjang = $this->db->query($sql, [$this->session->userdata('ses_id')])->result_array();
        if (isset($keranjang)) {
            $this->data['items'] = $keranjang;
            $this->load->view('admin/kasirstok/keranjang', $this->data);
        } else {
            echo '<center><b class="text-danger">*** Belum ada item yang dipilih ***</b></center>';
        }
    }

    public function update_cart()
    {
        $id = (int)$this->input->get('id');
        $menu = $this->db->query('SELECT kategori.kategori, menu_utama.* FROM menu_utama LEFT JOIN kategori ON menu_utama.id_kategori = kategori.id 
            WHERE menu_utama.id="' . (int)$this->input->get('id') . '"')->row();
        $keranjang = $this->db->get_where('keranjang', ['id_menu' => $menu->id, 'login_id' => $this->session->userdata('ses_id')])->row();
        // $stok = $menu->stok - $menu->stok_minim;
        // if ($stok >= (int)$this->input->post('qt')) {
        if (isset($keranjang)) {
            $item = [
                'id_menu' => $menu->id,
                'kode_menu' => $menu->kode_menu,
                'kategori' => $menu->kategori,
                'nama' => $menu->nama,
                'gambar' => $menu->gambar,
                'keterangan' =>  $keranjang->keterangan,
                'harga_beli'  => $menu->harga_pokok,
                'harga_jual'  => $menu->harga_jual,
            ];
            if ($this->input->post('type') == 'minus') {
                if ((int)$this->input->post('qt') > 0) {
                    $this->db->set('qty', $keranjang->qty - 1);
                } else {
                    echo '<script>
                            Swal.fire({
                                icon: "error",
                                title: "Gagal !",
                                text: "Stok min ' . (int)$this->input->post('qt') . '  Product telah mencapai batas minim qty .",
                            })</script>';
                    exit;
                }
            } elseif ($this->input->post('type') == 'keyup') {
                if ((int)$this->input->post('qt') >= 1) {
                    $this->db->set('qty', $this->input->post('qt'));
                } else {
                    $this->db->set('qty', 1);
                }
            } else {
                $qty = $keranjang->qty + 1;
                // if ($stok >= $qty) {
                $this->db->set('qty', $keranjang->qty + 1);
                // } else {
                //     echo '<script>
                //             Swal.fire({
                //                 icon: "error",
                //                 title: "Gagal !",
                //                 text: "Stok Product telah mencapai batas minim qty .",
                //             })</script>';
                //     exit;
                // }
            }
        }

        $this->db->where('id_menu', $menu->id);
        $this->db->where('login_id', $this->session->userdata('ses_id'));
        $this->db->update('keranjang', $item);
        // } else {
        //     echo '<script>
        //     Swal.fire({
        //         icon: "error",
        //         title: "Gagal !",
        //         text: "Stok Product telah mencapai batas minim qty .",
        //     })</script>';
        // }
    }

    public function update_cart_stok()
    {
        $id = (int)$this->input->get('id');
        $menu = $this->db->query('SELECT kategori.kategori, menu_utama.* FROM menu_utama LEFT JOIN kategori ON menu_utama.id_kategori = kategori.id 
            WHERE menu_utama.id="' . (int)$this->input->get('id') . '"')->row();
        $keranjang = $this->db->get_where('keranjang', ['id_menu' => $menu->id, 'login_id' => $this->session->userdata('ses_id')])->row();
        $stok = $menu->stok - $menu->stok_minim;
        if ($stok >= (int)$this->input->post('qt')) {
            if (isset($keranjang)) {
                $item = [
                    'id_menu' => $menu->id,
                    'kode_menu' => $menu->kode_menu,
                    'kategori' => $menu->kategori,
                    'nama' => $menu->nama,
                    'gambar' => $menu->gambar,
                    'keterangan' =>  $keranjang->keterangan,
                    'harga_beli'  => $menu->harga_pokok,
                    'harga_jual'  => $menu->harga_jual,
                ];
                if ($this->input->post('type') == 'minus') {
                    $this->db->set('qty', $keranjang->qty - 1);
                } elseif ($this->input->post('type') == 'keyup') {
                    if ($this->input->post('qt') > 0) {
                        $this->db->set('qty', $this->input->post('qt'));
                    } else {
                        $this->db->set('qty', 1);
                    }
                } else {
                    $qty = $keranjang->qty + 1;
                    if ($stok >= $qty) {
                        $this->db->set('qty', $keranjang->qty + 1);
                    } else {
                        echo '<script>
                            Swal.fire({
                                icon: "error",
                                title: "Gagal !",
                                text: "Stok Product telah mencapai batas minim qty .",
                            })</script>';
                        exit;
                    }
                }
            }

            $this->db->where('id_menu', $menu->id);
            $this->db->where('login_id', $this->session->userdata('ses_id'));
            $this->db->update('keranjang', $item);
        } else {
            echo '<script>
            Swal.fire({
                icon: "error",
                title: "Gagal !",
                text: "Stok Product telah mencapai batas minim qty .",
            })</script>';
        }
    }

    public function updateket_cart()
    {
        $id = (int)$this->input->get('id');
        $menu = $this->db->query('SELECT kategori.kategori, menu_utama.* FROM menu_utama LEFT JOIN kategori ON menu.id_kategori = kategori.id 
            WHERE menu_utama.id="' . (int)$this->input->get('id') . '"')->row();

        $keranjang = $this->db->get_where('keranjang', ['id_menu' => $menu->id, 'login_id' => $this->session->userdata('ses_id')])->row();

        $item = [
            'keterangan' => $this->input->post('qt'),
        ];

        $this->db->where('id_menu', $menu->id);
        $this->db->where('login_id', $this->session->userdata('ses_id'));
        $this->db->update('keranjang', $item);
    }

    public function cart_table()
    {
        $keranjang = $this->db->get_where('keranjang', ['login_id' => $this->session->userdata('ses_id')])->result_array();

        if (isset($keranjang)) {
            $this->data['items'] = $keranjang;
            $this->load->view('admin/kasirstok/table', $this->data);
        } else {
            echo '<center><b class="text-danger">*** Belum ada item yang dipilih ***</b></center>';
        }
    }


    public function del_cart()
    {
        $id = $this->input->post('id_menu');
        $hrgjual = $this->input->post('hrgjual');
        $this->db->where('id_menu', $id);
        $this->db->where('harga_jual', $hrgjual);
        $this->db->where('login_id', $this->session->userdata('ses_id'));
        $this->db->delete('keranjang');
        // redirect('jual/tambah');
    }
}
