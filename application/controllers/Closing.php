<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use SebastianBergmann\Environment\Console;

class Closing extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        //validasi jika user belum login
        $this->data['CI'] = &get_instance();
        $this->load->helper(array('form', 'url'));
        $this->load->model('M_Admin');
        $this->load->model('M_Datatables');
        $this->load->helper('tgl_default');
        $this->load->helper('alert');
        if ($this->session->userdata('masuk_sistem') != true) {
            $url = base_url('login');
            redirect($url);
        }
    }

    public function index()
    {
        // $shift_nilai = $this->db->query("SELECT MAX(open) as 'nilai_max', MIN(close) as 'nilai_min' FROM shift")->row();
        // $nilai_max = $shift_nilai->nilai_max;
        // $nilai_min = $shift_nilai->nilai_min;
        // $shift_now = $this->db->query("SELECT * FROM shift WHERE ('" . date('H:i') . "' BETWEEN open AND close) OR 
        //    ((open>='" . $nilai_max . "' AND '" . date('H:i') . "' BETWEEN '" . $nilai_max . "' AND '24:00') OR 
        //    (close<='" . $nilai_min . "' AND '" . date('H:i') . "' BETWEEN '00:00' AND '" . $nilai_min . "')) ")->row();


        $cabang_id = $this->session->userdata('ses_cabang_id');
        $kode_cabang = $this->session->userdata('ses_kode_cabang');
        $shift_ses = $this->session->userdata('ses_shift');
        $shift = $this->db->query("SELECT transaksi.*,shift.nama AS 'namaShift' FROM transaksi INNER JOIN shift ON shift.id=transaksi.shift_id WHERE transaksi.closing_id = 0 AND transaksi.cabang_id=" . $cabang_id . " AND transaksi.shift_id=" . $shift_ses . " ORDER BY transaksi.date LIMIT 1");
        $tgltrans = '';
        if ($shift->num_rows() > 0) {
            $ps = $shift->row();
            $shift_id = $ps->shift_id;
            $tgltrans = $ps->date;
            if ($shift_id <> $shift_ses) {
                $this->session->set_flashdata('failed', '[' . $shift_id . '<>' . $shift_ses . '] <strong>Proses Closing Gagal,</strong> Anda tidak diijinkan closing shift ini !');
                redirect(base_url('kasir'));
                exit;
            }
        } else {
            $this->session->set_flashdata('failed', '<strong>Closing Gagal,</strong> Belum ada data transaksi !');
            redirect(base_url('kasir'));
            exit;

            // $this->session->set_flashdata("failed", " Closing belum diijinkan ! ");

            // $this->load->view('layout/header', $this->data);
            // $this->load->view('admin/kasir/index', $this->data);
            // $this->load->view('layout/footer', $this->data);
        }

        // if ($shift_id == $shift_now->id) {
        if ($shift_id == $shift_ses) {
            if ($shift->num_rows() > 0) {
                // $ps = $shift->row();
                $shift_id = $ps->shift_id;
                $namaShift = $ps->namaShift;
                $tgltrans = $ps->date;

                $tanggal = $ps->date;
                // $waktu = date_format(date_create($ps->created_at), 'H');
                $tgl = date_create($tanggal);
                // if (($shift_id == 3) && ($waktu <= 7)) {
                //     date_sub($tgl, date_interval_create_from_date_string("1 days"));
                // }
                $tanggal = date_format($tgl, 'd-m-Y');

                $transaksi = $this->db->query("SELECT * FROM transaksi WHERE closing_id = 0 AND shift_id = $shift_id AND cabang_id='" . $cabang_id . "' AND date='" . $tgltrans . "'")->result_array();
                $tot_penjualan = 0;
                $tot_cash = 0;
                $tot_qris = 0;
                $tot_online = 0;
                foreach ($transaksi as $isi) {
                    $tot_penjualan += $isi['grandtotal'];
                    if ($isi['status'] == 'Cash') {
                        $tot_cash += $isi['grandtotal'];
                    } else if ($isi['status'] == 'QRIS') {
                        $tot_qris += $isi['grandtotal'];
                    } else if ($isi['status'] == 'Online') {
                        $tot_online += $isi['grandtotal'];
                    }
                }

                $pengeluaran = $this->db->query("SELECT * FROM transaksi_keluar WHERE closing_id = 0 AND shift_id = $shift_id AND cabang_id='" . $cabang_id . "' AND date='" . $tgltrans . "'")->result_array();
                $tot_pengeluaran = 0;
                foreach ($pengeluaran as $isipengeluaran) {
                    $tot_pengeluaran += $isipengeluaran['jumlah'];
                }

                $this->data = [
                    'title_web'  =>  'Closing Shift : ' . $tanggal . ' - ' . $namaShift . ' - ' . $kode_cabang,
                    'tgltrans'  =>  $tgltrans,
                    'penjualan' => $tot_penjualan,
                    'penjualan_cash' => $tot_cash,
                    'penjualan_qris' => $tot_qris,
                    'penjualan_online' => $tot_online,
                    'pengeluaran' => $tot_pengeluaran,
                    'id_shift' => $shift_id,
                    'id_cabang' => $cabang_id,
                ];
            } else {
                $this->data = [
                    'title_web'  => 'Data transaksi tidak ditemukan',
                    'penjualan' => 0,
                    'penjualan_cash' => 0,
                    'penjualan_qris' => 0,
                    'penjualan_online' => 0,
                    'pengeluaran' => 0,
                    'id_shift' => 0,
                    'id_cabang' => 0,
                ];
                // exit;
                // $this->session->set_flashdata('failed', '<strong>Closing Gagal,</strong> Belum Ada Transaksi yang Harus di Closing !');
                // redirect(base_url('home'));
            }
            $this->load->view('layout/header', $this->data);
            $this->load->view('admin/closing/index', $this->data);
            $this->load->view('layout/footer', $this->data);
        } else {
            // $this->session->set_flashdata('failed', 'id-' . $ps->shift_id . '-shift_now-' . $shift_now->id . '<strong>Closing Gagal,</strong> Belum diijinkan untuk shift ini !');
            $this->session->set_flashdata('failed', '<strong>Closing Gagal,</strong> Belum diijinkan untuk shift ini !');
            redirect(base_url('kasir'));
            exit;
        }
    }

    public function simpanClosing()
    {
        $cabang_id = $this->session->userdata('ses_cabang_id');
        $shift_ses = $this->session->userdata('ses_shift');
        $shift = $this->db->query("SELECT transaksi.*,shift.nama AS 'namaShift' FROM transaksi INNER JOIN shift ON shift.id=transaksi.shift_id WHERE transaksi.closing_id = 0 AND transaksi.cabang_id=" . $cabang_id . " AND transaksi.shift_id=" . $shift_ses . "  ORDER BY transaksi.date LIMIT 1");

        $tgltrans = '';

        if ($shift->num_rows() > 0) {
            $ps = $shift->row();
            $shift_id = $ps->shift_id;
            $tgltrans = $ps->date;


            $tanggal = $ps->date;
            // $waktu = date_format(date_create($ps->created_at), 'H');
            $tgl = date_create($tanggal);
            // if (($shift_id == 3) && ($waktu <= 7)) {
            //     date_sub($tgl, date_interval_create_from_date_string("1 days"));
            // }
            $cekclosing = $this->db->query("SELECT * FROM closing WHERE date='" . date_format($tgl, "Y-m-d") . "' AND cabang_id='" . $cabang_id . "' AND shift_id=" . $shift_ses);
            if ($cekclosing->num_rows() > 0) {
                $this->session->set_flashdata("failed", '<strong>Closing Gagal,</strong> Data tanggal ' . date_format($tgl, "Y-m-d") . ' sudah diclosing ! Silahkan coba closing lagi. ');
                echo 'gagal';
                exit;
            } else {

                $kode = $this->db->query("SELECT * FROM closing ORDER BY id DESC LIMIT 1");
                if ($kode->num_rows() > 0) {
                    $ps = $kode->row();
                    $kode_num = $ps->id + 1;
                } else {
                    $kode_num = 1;
                }

                $transaksi = $this->db->query("SELECT * FROM transaksi WHERE closing_id = 0 AND shift_id = $shift_id AND cabang_id='" . $cabang_id . "' AND date='" . $tgltrans . "'")->result_array();
                $tot_penjualan = 0;
                $tot_cash = 0;
                $tot_qris = 0;
                $tot_online = 0;
                foreach ($transaksi as $isi) {
                    $tot_penjualan += $isi['grandtotal'];
                    if ($isi['status'] == 'Cash') {
                        $tot_cash += $isi['grandtotal'];
                    } else if ($isi['status'] == 'QRIS') {
                        $tot_qris += $isi['grandtotal'];
                    } else if ($isi['status'] == 'Online') {
                        $tot_online += $isi['grandtotal'];
                    }
                }

                $pengeluaran = $this->db->query("SELECT * FROM transaksi_keluar WHERE closing_id = 0 AND shift_id = $shift_id AND cabang_id='" . $cabang_id . "' AND date='" . $tgltrans . "'")->result_array();
                $tot_pengeluaran = 0;
                foreach ($pengeluaran as $isipengeluaran) {
                    $tot_pengeluaran += $isipengeluaran['jumlah'];
                    $datapengeluaran = [
                        'closing_id' => $kode_num,
                    ];
                    $this->db->where("id", $isipengeluaran['id']); // ubah id dan postnya
                    $this->db->update("transaksi_keluar", $datapengeluaran);
                }

                $dataclosing = [
                    'kasir_id' => $this->session->userdata('ses_id'),
                    'shift_id' => $shift_id,
                    'status' => "CLOSE",
                    'saldo_awal' => 0,
                    'pemasukan' => $tot_cash,
                    'pengeluaran' => $tot_pengeluaran,
                    'sisa_uang' => $tot_cash - $tot_pengeluaran,
                    'total_qty_titipan' => 0,
                    'total_uang_titipan' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    // 'date' => date('Y-m-d'),
                    // 'periode' => date('Y-m'),
                    // 'year' => date('Y'),
                    'date' => date_format($tgl, "Y-m-d"),
                    'periode' => date_format($tgl, "Y-m"),
                    'year' => date_format($tgl, "Y"),
                    'cabang_id' => $cabang_id,
                    'qris' => $tot_qris,
                    'online' => $tot_online,

                ];

                $this->db->insert("closing", $dataclosing);

                foreach ($transaksi as $isi) {
                    $data = [
                        'closing_id' => $kode_num,
                    ];
                    $this->db->where("id", $isi['id']); // ubah id dan postnya
                    $this->db->update("transaksi", $data);
                }

                $this->session->set_flashdata("success", '<strong>Closing Berhasil,</strong> Data closing telah disimpan ! ');

                echo 'sukses';
            }
        } else {
            $this->session->set_flashdata("failed", '<strong>Closing Gagal,</strong> Data closing tidak tersedia ! Silahkan coba closing lagi. ');
            echo 'gagal';
        }

        // $closing = $this->db->query('select * from closing where status = "OPEN"')->row();
        // if (isset($closing)) {
        //     $this->session->set_userdata('ses_shift', $closing->shift_id);
        //     $this->session->set_userdata('ses_opening', $closing->id);
        // }
        // redirect(base_url("kasir"));

        // echo 'sukses';
        // echo 'gagal';
    }


    public function simpan()
    {
        $kode = $this->db->query("SELECT * FROM closing ORDER BY id DESC LIMIT 1");

        if ($kode->num_rows() > 0) {
            $ps = $kode->row();
            $kode_num = $ps->id + 1;
        } else {
            $kode_num = 1;
        }
        $no_closing = 'CL' . date('Y-m-d') . sprintf('%02d', intval($kode_num));

        $cabang_id = $this->session->userdata('ses_cabang_id');
        $shift = $this->db->query("SELECT * FROM transaksi WHERE closing_id = 0 AND cabang_id='" . $cabang_id . "' ORDER BY created_at LIMIT 1");

        if ($shift->num_rows() > 0) {
            $ps = $shift->row();
            $shift_id = $ps->shift_id;
        } else {
            $shift_id = 1;
        }

        // $this->form_validation->set_rules("saldo_awal", "Saldo Awal", "required");
        // if ($this->form_validation->run() != false) {
        $data = [
            'no_closing' => $no_closing,
            'kasir_id' => $this->session->userdata('ses_id'),
            'shift_id' => $shift_id,
            'status' => "CLOSE",
            'saldo_awal' => htmlspecialchars($this->input->post("saldo_awal", true), ENT_QUOTES),
            'pemasukan' => htmlspecialchars($this->input->post("penjualan", true), ENT_QUOTES),
            'pengeluaran' => 0,
            'sisa_uang' => 0,
            'total_qty_titipan' => 0,
            'total_uang_titipan' => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'date' => date('Y-m-d'),
            'periode' => date('Y-m'),
            'year' => date('Y'),
        ];

        //$this->db->insert("closing", $data);
        $this->session->set_flashdata("success", " Berhasil Insert Data ! " . htmlspecialchars($this->input->post("penjualan", true), ENT_QUOTES));
        // $closing = $this->db->query('select * from closing where status = "OPEN"')->row();
        // if (isset($closing)) {
        //     $this->session->set_userdata('ses_shift', $closing->shift_id);
        //     $this->session->set_userdata('ses_opening', $closing->id);
        // }
        redirect(base_url("kasir"));
        // } else {
        //     $this->session->set_flashdata("failed", " Gagal Insert Data ! " . validation_errors());
        //     redirect(base_url("kasir"));
        // }
    }
    public function ubah()
    {

        $id =  (int)$this->input->post("id"); // parameter yang mau di update

        $this->form_validation->set_rules("id", "Id", "required");
        $this->form_validation->set_rules("qris", "QRIS", "required");
        $this->form_validation->set_rules("online", "Online", "required");
        $this->form_validation->set_rules("pemasukan", "Cash", "required");
        $this->form_validation->set_rules("pengeluaran", "Pengeluaran", "required");

        if ($this->form_validation->run() != false) {

            $data = [
                'qris' => (int)$this->input->post("qris"),
                'online' => (int)$this->input->post("online"),
                'pemasukan' => (int)$this->input->post("pemasukan"),
                'pengeluaran' => (int)$this->input->post("pengeluaran"),
                'sisa_uang' => (int)$this->input->post("pemasukan") - (int)$this->input->post("pengeluaran")
            ];

            $this->db->where("id", $id); // ubah id dan postnya
            $this->db->update("closing", $data);
            $this->session->set_flashdata("success", " Berhasil Ubah Data Closing ! ");
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        } else {
            $this->session->set_flashdata("failed", " Gagal Ubah Data Closing ! " . validation_errors());
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        }
    }
}
