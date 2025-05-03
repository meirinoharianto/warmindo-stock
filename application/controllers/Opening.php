<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class Opening extends CI_Controller
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
        $this->data = [
            'title_web'  => 'Opening',
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/opening/index', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function simpan()
    {
        $kode = $this->db->query("SELECT * FROM closing ORDER BY id DESC LIMIT 1");

        if ($kode->num_rows() > 0) {
            $ps = $kode->row();
            $kode_closing = $ps->id + 1;
        } else {
            $kode_closing = 1;
        }

        $no_closing = "O" . $kode_closing;
        $shift = $this->db->query("SELECT * FROM shift WHERE OPEN >= '" . date('H:i') . "' AND CLOSE <= '" . date('H:i') . "'")->row();

        $this->form_validation->set_rules("saldo_awal", "Saldo Awal", "required");
        if ($this->form_validation->run() != false) {
            $data = [
                'no_closing' => $no_closing,
                'kasir_id' => $this->session->userdata('ses_id'),
                'shift_id' => $shift->id,
                'status' => "OPEN",
                'saldo_awal' => htmlspecialchars($this->input->post("saldo_awal", true), ENT_QUOTES),
                'pemasukan' => 0,
                'pengeluaran' => 0,
                'sisa_uang' => 0,
                'total_qty_titipan' => 0,
                'total_uang_titipan' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'date' => date('Y-m-d'),
                'periode' => date('Y-m'),
                'year' => date('Y'),
            ];

            $this->db->insert("closing", $data);
            $this->session->set_flashdata("success", " Berhasil Insert Data ! ");
            $closing = $this->db->query('select * from closing where status = "OPEN"')->row();
            if (isset($closing)) {
                $this->session->set_userdata('ses_shift', $closing->shift_id);
                $this->session->set_userdata('ses_opening', $closing->id);
            }
            redirect(base_url("kasir"));
        } else {
            $this->session->set_flashdata("failed", " Gagal Insert Data ! " . validation_errors());
            redirect(base_url("kasir"));
        }
    }
}
