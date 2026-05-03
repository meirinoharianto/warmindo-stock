<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Adminkasir extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        //validasi jika user belum login
        $this->load->helper(array('form', 'url'));
        $this->load->model('M_Admin');
        $this->load->helper('tgl_default');
        $this->load->helper('alert');
        if ($this->session->userdata('masuk_sistem') != true) {
            $url = base_url('login');
            redirect($url);
        }
        // if ($this->session->userdata('ses_level') == 'Kasir') {
        //     redirect('kasir');
        // } else if ($this->session->userdata('ses_level') == 'AdminKasir') {
        //     redirect('adminkasir');
        // }
    }

    public function index()
    {
        if ($this->session->userdata('ses_level') == 'AdminKasir') {
            $this->db->db_debug = TRUE;
            $trx = $this->db->get('transaksi')->num_rows();
            // $trx = $this->db->get('transaksi')->num_rows();
        } else if ($this->session->userdata('ses_level') == 'Kasir') {
            $trx = $this->db->get_where('transaksi', ['kasir_id', $this->session->userdata('ses_id')])->num_rows();
        } else {
            $trx = $this->db->get('transaksi')->num_rows();
        }

        $this->data = [
            'title_web' => 'Dashboard',
            // 'userx'     => $this->db->get_where('login', ['id' => $this->session->userdata('ses_id')])->row(),
            // 'ck'        => $this->db->get('kategori')->num_rows(),
            // 'cm'        => $this->db->get_where('menu_utama', ['cabang_id' => $this->session->userdata('ses_cabang_id')])->num_rows(),
            // 'cc'        => $this->db->get('customer')->num_rows(),
            // 'ct'        => $trx,
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/adminkasir/index', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function grafik_penjualan_cabang()
    {

        $this->data = [
            'title_web' => 'Grafik Penjualan Cabang',
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/adminkasir/grafik/penjualan-cabang', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function grafik_stok_keluar_bahan()
    {

        $this->data = [
            'title_web' => 'Grafik Stok Keluar Bahan',
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/adminkasir/grafik/stok-keluar-bahan', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function grafik_menu_terjual()
    {

        $this->data = [
            'title_web' => 'Grafik Menu Terjual',
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/adminkasir/grafik/menu-terjual', $this->data);
        $this->load->view('layout/footer', $this->data);
    }
}
