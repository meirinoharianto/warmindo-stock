<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class Lap_kartustok extends CI_Controller
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

    public function lap_kartustok_pershift()
    {
        $this->data = [
            'title_web'  => 'Laporan Kartu Stok per Shift',
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/laporan/lap_kartustok_pershift', $this->data);
        $this->load->view('layout/footer', $this->data);
    }
}
