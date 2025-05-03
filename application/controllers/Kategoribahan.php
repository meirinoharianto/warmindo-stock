<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kategoribahan extends CI_Controller
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
    }

    public function index()
    {
        if (!empty($this->input->get('id'))) {
            $url = base_url('kategoribahan/update');
            $edit = $this->db->get_where('kategori_bahan', ['id' => (int)$this->input->get('id')])->row();
        } else {
            $url = base_url('kategoribahan/store');
            $edit = '';
        }
        $this->data = [
            'title_web' => 'Kategori Bahan',
            'url'       => $url,
            'edit'      => $edit,
            'kat'       => $this->db->get('kategori_bahan')->result()
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/kategoribahan/index', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function store()
    {
        $this->form_validation->set_rules("kode_kategori", "Kode Kategori", "required");
        $this->form_validation->set_rules("nama_kategori", "Nama Kategori", "required");
        if ($this->form_validation->run() != false) {
            $data = [
                'kode_kategori' => htmlspecialchars($this->input->post("kode_kategori", true), ENT_QUOTES),
                'nama_kategori' => htmlspecialchars($this->input->post("nama_kategori", true), ENT_QUOTES),
            ];
            $this->db->insert("kategori_bahan", $data);
            $this->session->set_flashdata("success", " Berhasil Insert Data ! ");
            redirect(base_url("kategoribahan"));
        } else {
            $this->session->set_flashdata("failed", " Gagal Insert Data ! " . validation_errors());
            redirect(base_url("kategoribahan"));
        }
    }

    public function update()
    {
        $id =  (int)$this->input->post("id"); // parameter yang mau di update
        $this->form_validation->set_rules("id", "Id", "required");
        $this->form_validation->set_rules("kode_kategori", "Kode Kategori", "required");
        $this->form_validation->set_rules("nama_kategori", "Nama Kategori", "required");
        if ($this->form_validation->run() != false) {
            $data = [
                'kode_kategori' => htmlspecialchars($this->input->post("kode_kategori", true), ENT_QUOTES),
                'nama_kategori' => htmlspecialchars($this->input->post("nama_kategori", true), ENT_QUOTES),
            ];

            $this->db->where("id", $id); // ubah id dan postnya
            $this->db->update("kategori_bahan", $data);
            $this->session->set_flashdata("success", " Berhasil Update Data ! ");
            redirect(base_url("kategoribahan?id=" . $id));
        } else {
            $this->session->set_flashdata("failed", " Gagal Update Data ! " . validation_errors());
            redirect(base_url("kategoribahan?id=" . $id));
        }
    }

    public function delete()
    {
        $id = (int)$this->input->get("id");
        $cek = $this->db->get_where("kategori_bahan", ["id" => $id]); // tulis id yang dituju
        if ($cek->num_rows() > 0) {
            $this->db->where("id", $id); // tulis id yang dituju
            $this->db->delete("kategori_bahan");
            $this->session->set_flashdata("success", " Berhasil Delete Data ! ");
            redirect(base_url("kategoribahan"));
        } else {
            $this->session->set_flashdata("failed", " Gagal Delete Data ! " . validation_errors());
            redirect(base_url("kategoribahan"));
        }
    }
}
