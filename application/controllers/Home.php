<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Home extends CI_Controller
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
        if ($this->session->userdata('ses_level') == 'Kasir') {
            if ((int)$this->session->userdata('ses_id') == 44) {
                redirect('kasirstok');
            } else {
                redirect('kasir');
            }
        } else if ($this->session->userdata('ses_level') == 'AdminKasir') {
            redirect('adminkasir');
        }
    }

    public function index()
    {
        // if ($this->session->userdata('ses_level') == 'Admin') {
        if (in_array($this->session->userdata('ses_level'), array('Admin', 'SuperAdmin'))) {
            $trx = $this->db->get('transaksi')->num_rows();
        } else if ($this->session->userdata('ses_level') == 'Kasir') {
            $trx = $this->db->get_where('transaksi', ['kasir_id', $this->session->userdata('ses_id')])->num_rows();
        } else {
        }

        $this->data = [
            'title_web' => 'Dashboard',
            'userx'     => $this->db->get_where('login', ['id' => $this->session->userdata('ses_id')])->row(),
            'ck'        => $this->db->get('kategori')->num_rows(),
            // 'cm'        => $this->db->get_where('menu', ['cabang_id' => $this->session->userdata('ses_cabang_id')])->num_rows(),
            'cc'        => $this->db->get('customer')->num_rows(),
            'ct'        => $trx,
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/home/index', $this->data);
        $this->load->view('layout/footer', $this->data);
    }
}
