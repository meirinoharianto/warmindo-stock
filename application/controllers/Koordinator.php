<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Koordinator extends CI_Controller
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

        if ($this->session->userdata('ses_level') != 'Koordinator') {
            $url = base_url('login');
            redirect($url);
        }
    }

    public function index()
    {
        $iduser =  $this->session->userdata('ses_id');

        $login_detail = $this->db->select('cabang_id')
            ->where('login_id', $iduser)
            ->get('login_detail')
            ->result_array();

        $list_cabang = array_column($login_detail, 'cabang_id');
        $this->data = [
            'title_web' => 'Dashboard Koordinator',
            'list_cabang'       => $list_cabang,

        ];

        $this->load->view('layout/headerkoordinator', $this->data);
        $this->load->view('admin/koordinator/index', $this->data);
        $this->load->view('layout/footer', $this->data);
    }
}
