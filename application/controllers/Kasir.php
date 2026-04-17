<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kasir extends CI_Controller
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
            'title_web' => 'Kasir',
            'kat'       => $this->db->get('kategori')->result(),
            'no_bon'    => $kode_cabang . "/" . date('ym') . "/" . sprintf("%05s",  $no_bon_next),
            'atas_nama'    => $atas_nama,
            'pp'        => $this->db->get_where('profil_toko', ['cabang_id' => $cabang_id])->row(),
            'halperpage' => 12
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/kasir/index', $this->data);
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
            $row = $this->db->query('select * from menu where id = ?', [$isi['id_menu']])->row();
            $stok = $row->stok - $qty;
            // $stok = 0 - $qty;

            $up_stok[] = array(
                'id' => $isi['id_menu'],
                'stok' => $stok
            );

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
        $total_array = count($up_stok);
        if ($total_array != 0) {
            $this->db->update_batch('menu', $up_stok, 'id');
        }

        // insert penjualan
        $total_array = count($data_jual);
        if ($total_array != 0) {
            $this->db->insert_batch('transaksi_produk', $data_jual);
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

        $this->db->where('login_id', $this->session->userdata('ses_id'));
        $this->db->delete('keranjang');
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

        $this->load->view('admin/kasir/cetak', $this->data);
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
        $this->load->view('admin/kasir/print2', $this->data);
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

        $this->load->view('admin/kasir/print', $this->data);
    }


    public function print_android()
    {
        $no_bon = $this->input->get('id');
        $cetak  = $this->input->get('cetak');
        $idcabang  = $this->input->get('idcabang');

        if (in_array($this->session->userdata('ses_level'), array('Admin', 'AdminKasir', 'SuperAdmin'))) {
            if (!empty(htmlentities($this->input->get('idcabang', true)))) {
                $cabang_id = htmlentities($this->input->get('idcabang', true));

                if (!empty($cabang_id)) {
                    $caricabang = $this->db->query('SELECT * FROM cabang WHERE id = ? ', [$cabang_id])->row();
                    if ($caricabang) {
                        $kode_cabang = $caricabang->kode_cabang;
                        $arr_kode_cabang = array("SN1", "SN2", "SN7", "PU");
                        $suffix = in_array($kode_cabang, $arr_kode_cabang) ? '' : '_' . $kode_cabang;
                    }
                }
            }
        } else {
            $suffix = $this->session->userdata('ses_suffix');
        }
        // Ambil data transaksi
        $t = $this->db->get_where('transaksi' . $suffix, ['no_bon' => $no_bon])->row();
        if (!$t) {
            show_error('Data transaksi tidak ditemukan');
            return;
        }

        // Ambil data toko / pengaturan printer
        // $pp = $this->db->get('pengaturan')->row();
        $pp = $this->db->get_where('profil_toko', ['cabang_id' => $idcabang])->row();


        // Ambil detail item
        $tp = $this->db->get_where('transaksi_produk' . $suffix, ['no_bon' => $no_bon])->result();

        // Lebar karakter kira-kira untuk 58mm
        $lineWidth = 32;
        if ($cetak == '2') {
            $lineWidth = 42; // 80mm
        }

        $separator = str_repeat('-', $lineWidth);

        $text = '';

        // Beberapa app printer mendukung tag seperti ini

        /*
    |--------------------------------------------------------------------------
    | STRUK CUSTOMER
    |--------------------------------------------------------------------------
    */

        $text .= $this->center_text(strtoupper($pp->nama_toko), $lineWidth) . "\n";
        $text .= $this->center_text($pp->alamat_toko, $lineWidth) . "\n";
        $text .= $separator . "\n";

        $text .= "No     : {$t->no_bon}\n";
        $text .= "Nama   : {$t->atas_nama}\n";
        $text .= "Status : {$t->status}\n";
        $text .= "Shift  : {$t->shift_id}\n";
        $text .= "Tanggal: {$t->created_at}\n";
        $text .= $separator . "\n";

        $hr = 0;

        foreach ($tp as $r) {
            $namaMenu = trim($r->nama_menu);
            $qtyHarga = $r->qty . ' x ' . number_format($r->harga_jual);
            $subtotal = 'Rp' . number_format($r->qty * $r->harga_jual);

            $text .= $namaMenu . "\n";
            $text .= $this->format_left_right($qtyHarga, $subtotal, $lineWidth) . "\n";

            $hr += $r->harga_jual * $r->qty;
        }

        $text .= $separator . "\n";

        $diskon = $hr * $t->diskon / 100;
        $pajak  = $hr * $t->pajak / 100;
        $grd    = ($hr - $t->voucher - $diskon) + $pajak;

        $text .= $this->format_left_right('Total', 'Rp' . number_format($hr), $lineWidth) . "\n";

        if ($pp->diskon > 0 && $t->diskon > 0) {
            $text .= $this->format_left_right('Diskon', $t->diskon . '% / Rp' . number_format($diskon), $lineWidth) . "\n";
        }

        if ($pp->pajak > 0 && $t->pajak > 0) {
            $text .= $this->format_left_right('Pajak', 'Rp' . number_format($pajak), $lineWidth) . "\n";
        }

        if ($pp->voucher > 0 && $t->voucher > 0) {
            $text .= $this->format_left_right('Voucher', 'Rp' . number_format($t->voucher), $lineWidth) . "\n";
        }

        $text .= $this->format_left_right('Grand Total', 'Rp' . number_format($grd), $lineWidth) . "\n";
        $text .= $this->format_left_right('Dibayar', 'Rp' . number_format($t->dibayar), $lineWidth) . "\n";
        $text .= $this->format_left_right('Kembali', 'Rp' . number_format($t->dibayar - $grd), $lineWidth) . "\n";

        $text .= $separator . "\n";
        $text .= $this->center_text($pp->footer_struk, $lineWidth) . "\n";
        $text .= $this->center_text($t->created_at, $lineWidth) . "\n";
        $text .= "\n\n\n\n";

        /*
|--------------------------------------------------------------------------
| STRUK KITCHEN
|--------------------------------------------------------------------------
*/
        $text .= "\n";

        $text .= $this->escpos_align('center');
        $text .= $this->escpos_bold(true);
        $text .= $this->escpos_text_size(2, 2);
        $text .= "KITCHEN\n";

        $text .= $this->escpos_text_size(1, 1);
        $text .= "NO BON : " . $t->no_bon . "\n";
        $text .= "ATAS NAMA : " . $t->atas_nama . "\n";
        $text .= $t->created_at . "\n";

        $text .= $this->escpos_reset();
        $text .= $separator . "\n";

        $text .= $this->escpos_bold(true);
        $text .= $this->escpos_text_size(1, 2);

        foreach ($tp as $r) {
            $text .= $this->format_kitchen_item($r->nama_menu, $r->qty, $lineWidth) . "\n";
        }

        $text .= $this->escpos_reset();

        $text .= $separator . "\n";

        $text .= $this->escpos_align('center');
        $text .= $this->escpos_bold(true);
        $text .= $this->escpos_text_size(2, 1);
        $text .= "SELESAI\n";

        $text .= $this->escpos_reset();
        $text .= "\n\n\n";


        header('Content-Type: text/plain; charset=utf-8');
        echo $text;
    }

    private function format_left_right($left, $right, $width = 32)
    {
        $left  = trim((string)$left);
        $right = trim((string)$right);

        $leftLen  = strlen($left);
        $rightLen = strlen($right);

        $space = $width - ($leftLen + $rightLen);

        if ($space < 1) {
            return $left . "\n" . str_repeat(' ', max(0, $width - $rightLen)) . $right;
        }

        return $left . str_repeat(' ', $space) . $right;
    }

    private function center_text($text, $width = 32)
    {
        $text = strip_tags(trim((string)$text));

        // Pecah jadi beberapa baris sesuai lebar
        $lines = wordwrap($text, $width, "\n", true);
        $lines = explode("\n", $lines);

        $result = '';

        foreach ($lines as $line) {
            $line = trim($line);
            $len  = strlen($line);

            if ($len >= $width) {
                $result .= $line . "\n";
            } else {
                $leftPad = floor(($width - $len) / 2);
                $result .= str_repeat(' ', $leftPad) . $line . "\n";
            }
        }

        return rtrim($result);
    }

    private function format_kitchen_item($name, $qty, $width = 32)
    {
        $qtyText = 'x' . (int)$qty;
        $maxNameWidth = $width - strlen($qtyText) - 1;

        if ($maxNameWidth < 5) {
            $maxNameWidth = $width;
        }

        $lines = explode("\n", wordwrap(trim($name), $maxNameWidth, "\n", true));
        $result = '';

        foreach ($lines as $i => $line) {
            if ($i === 0) {
                $space = $width - strlen($line) - strlen($qtyText);
                if ($space < 1) {
                    $space = 1;
                }
                $result .= $line . str_repeat(' ', $space) . $qtyText . "\n";
            } else {
                $result .= $line . "\n";
            }
        }

        return rtrim($result);
    }

    private function escpos_text_size($width = 1, $height = 1)
    {
        $width  = max(1, min(8, (int)$width));
        $height = max(1, min(8, (int)$height));

        $n = (($width - 1) << 4) | ($height - 1);
        return chr(29) . chr(33) . chr($n);
    }

    private function escpos_align($align = 'left')
    {
        $map = [
            'left'   => 0,
            'center' => 1,
            'right'  => 2,
        ];

        $n = isset($map[$align]) ? $map[$align] : 0;
        return chr(27) . chr(97) . chr($n);
    }

    private function escpos_bold($on = true)
    {
        return chr(27) . chr(69) . chr($on ? 1 : 0);
    }

    private function escpos_reset()
    {
        return $this->escpos_align('left')
            . $this->escpos_bold(false)
            . $this->escpos_text_size(1, 1);
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
        $nameaddon = '(' . $this->input->post('nameaddon') . ')';
        $atasnama = $this->input->post('atas_nama');
        $hargajual = $this->input->post('hargajual');
        $this->session->set_userdata('ses_atas_nama', $atasnama);

        // (int)$this->input->post('id')

        // if ($atasnama == "") {
        //     echo json_encode(['status' => 'gagal_nama']);
        //     exit;
        // }

        $menu = $this->db->query('SELECT kategori.kategori, menu.* FROM menu LEFT JOIN kategori ON menu.id_kategori = kategori.id 
			WHERE menu.id="' . $id . '" AND menu.cabang_id=' . $cabang_id)->row();
        // $plusaddon = $menu->harga_jual + $addon;
        $hjual = $addon;
        $keranjang = $this->db->get_where('keranjang', ['id_menu' => $menu->id, 'harga_jual' => $hjual, 'login_id' => $this->session->userdata('ses_id')])->row();
        $item = array(
            'id_menu' => $menu->id,
            'kode_menu' => $menu->kode_menu,
            'kategori' => $menu->kategori,
            'nama' => $menu->nama . $nameaddon,
            'gambar' => $menu->gambar,
            'harga_beli'  => $menu->harga_pokok,
            // 'harga_jual'  => $menu->harga_jual + $addon,
            // 'harga_jual'  => $hjual,
            'harga_jual'  => $hargajual,
            'login_id'  => $this->session->userdata('ses_id')
        );
        $stok = $menu->stok - $menu->stok_minim;
        if ($menu->stok <= 0) {
            echo json_encode(['status' => 'gagal']);
        } else {
            if (!$keranjang) {
                $this->db->set('qty', 1);
                //tambah add on
                $this->db->insert('keranjang', $item);
                echo json_encode(['status' => 'sukses']);
            } else {
                $qty = $keranjang->qty + 1;
                if ($stok >= $qty) {
                    $this->db->set('qty', $keranjang->qty + 1);
                    $this->db->where('id_menu', $menu->id);
                    $this->db->where('login_id', $this->session->userdata('ses_id'));
                    $this->db->update('keranjang', $item);
                    echo json_encode(['status' => 'sukses']);
                } else {
                    echo json_encode(['status' => 'gagal']);
                }
            }
        }
    }

    public function cart()
    {
        $sql = "SELECT * FROM keranjang WHERE login_id = ? ORDER BY id ASC";
        $keranjang = $this->db->query($sql, [$this->session->userdata('ses_id')])->result_array();
        if (isset($keranjang)) {
            $this->data['items'] = $keranjang;
            $this->load->view('admin/kasir/keranjang', $this->data);
        } else {
            echo '<center><b class="text-danger">*** Belum ada item yang dipilih ***</b></center>';
        }
    }

    public function update_cart()
    {
        $id = (int)$this->input->get('id');
        $menu = $this->db->query('SELECT kategori.kategori, menu.* FROM menu LEFT JOIN kategori ON menu.id_kategori = kategori.id 
            WHERE menu.id="' . (int)$this->input->get('id') . '"')->row();
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
        $menu = $this->db->query('SELECT kategori.kategori, menu.* FROM menu LEFT JOIN kategori ON menu.id_kategori = kategori.id 
            WHERE menu.id="' . (int)$this->input->get('id') . '"')->row();

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
            $this->load->view('admin/kasir/table', $this->data);
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
