<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class Bahanbaku extends CI_Controller
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
            'title_web'  => 'Daftar Bahan Baku',
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/bahanbaku/index', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function import()
    {
        $this->data = [
            'title_web'  => 'Daftar Bahan Baku',
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/bahanbaku/import', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function proses_import()
    {
        $file_mimes = array('application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        if (isset($_FILES['berkas_excel']['name']) && in_array($_FILES['berkas_excel']['type'], $file_mimes)) {
            $arr_file = explode('.', $_FILES['berkas_excel']['name']);
            $extension = end($arr_file);
            if ('csv' == $extension) {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
            } else {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            }
            $spreadsheet = $reader->load($_FILES['berkas_excel']['tmp_name']);
            $sheetData = $spreadsheet->getActiveSheet()->toArray();
            for ($i = 1; $i < count($sheetData); $i++) {
                if ($sheetData[$i]['2'] == null) {
                    $kategori = 1;
                } else {
                    $kategori = $sheetData[$i]['2'];
                }
                $cekid  =  cek_id('bahanbaku', 'kode_bahanbaku', $sheetData[$i]['1']);
                $data = [
                    'kode_bahanbaku' => $cekid,
                    'id_kategori' => $kategori,
                    'nama' => $sheetData[$i]['3'],
                    'harga_pokok' => $sheetData[$i]['4'],
                    'stok' => $sheetData[$i]['5'],
                    'stok_minim' => $sheetData[$i]['6'],
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                $this->db->insert("bahanbaku", $data);
            }

            $this->session->set_flashdata("success", " Berhasil Insert Data ! ");
            redirect(base_url("bahanbaku"));
        } else {
            $this->session->set_flashdata("failed", " Gagal Insert Data !  Berkas harus berextensi excel");
            redirect(base_url("bahanbaku"));
        }
    }
    public function dtbahanbaku()
    {
        $pageNumber = htmlspecialchars($this->input->post('pageHome', true), ENT_QUOTES);
        $halperpage = 12;
        $page = isset($pageNumber) ? (int)$pageNumber : 1;
        $mulai = ($page > 1) ? ($page * $halperpage) - $halperpage : 0;
        if ($this->input->get('id')) {
            $wr = ' WHERE id_kategori = ' . (int)$this->input->get('id') . ' ';
        } elseif ($this->input->get('cari')) {
            $wr = ' WHERE nama LIKE "%' . $this->input->get('cari') . '%" OR kategori.kategori LIKE "%' . $this->input->get('cari') . '%"';
        } else {
            $wr = '';
        }
        $query = "SELECT kategori.kategori, bahanbaku.* FROM bahanbaku LEFT JOIN kategori ON bahanbaku.id_kategori = kategori.id";
        $hasil = $this->db->query($query . " $wr ORDER BY nama ASC LIMIT $mulai, $halperpage")->result();
        $this->data['hasil'] = $hasil;
        $this->load->view('admin/transfer/bahanbaku', $this->data);
    }

    public function data_bahanbaku()
    {
        if ($this->input->method(true) == 'POST') :
            $query = "SELECT kategori.kategori, bahanbaku.* FROM bahanbaku LEFT JOIN kategori ON bahanbaku.id_kategori = kategori.id";
            $search = array('kode_bahanbaku', 'kategori.kategori', 'nama', 'harga_pokok', 'keterangan', 'gambar');
            if ((int)$this->input->get('id')) {
                $where  = array('id_kategori' => (int)$this->input->get('id'));
            } else {
                $where  = null;
            }
            if ($this->input->get('cek')) {
                $iswhere = " stok <= stok_minim ";
            } else {
                $iswhere = null;
            }
            header('Content-Type: application/json');
            echo $this->M_Datatables->get_tables_query($query, $search, $where, $iswhere);
        endif;
    }

    public function stok()
    {
        if ($this->input->get('id')) {
            $this->data = [
                'title_web'  => 'Entry Persediaan Bahan Baku',
                'tipe'       => 'edit',
                'edit'       => $this->db->query(
                    "SELECT * FROM bahanbaku WHERE bahanbaku.id=?",
                    array($this->input->get('id'))
                )->row(),
            ];
        } else {
            $this->data = [
                'title_web'  => 'Entry Persediaan Bahan Baku',
                'tipe'       => ''
            ];
        }

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/bahanbaku/stok', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function persediaan()
    {
        $this->data = [
            'title_web'  => 'Daftar Persediaan Bahan Baku',
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/bahanbaku/persediaan', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function get_bahanbaku()
    {
        $id =  (int)$this->input->post("id"); // parameter yang mau di update
        $cek = $this->db->query("SELECT * FROM bahanbaku WHERE bahanbaku.id=?", array($id)); // tulis id yang dituju
        if ($cek->num_rows() > 0) {
            $data = $cek->row();
            $result[] = array(
                'id' => $data->id,
                'nama' => $data->nama,
                'stok' => $data->stok,
                'stok_minim' => $data->stok_minim,
            );
            echo json_encode($result);
        } else {
            echo '';
        }
    }

    public function pasok()
    {
        $id =  (int)$this->input->post("id"); // parameter yang mau di update

        $this->form_validation->set_rules("id", "Id", "required");
        $this->form_validation->set_rules("stok", "Stok", "required");

        if ($this->form_validation->run() != false) {
            $data_r = [
                'menu_id' => $id,
                'stok_awal' => (int)$this->input->post("stok"),
                'stok_akhir' => (int)$this->input->post("stoka"),
                'date' => date('Y-m-d'),
                'periode' => date('Y-m')
            ];

            $this->db->insert("bahanbaku_stok", $data_r);

            $data = [
                'stok' => (int)$this->input->post("stok"),
                'stok_minim' => (int)$this->input->post("stok_minim"),
            ];

            $this->db->where("id", $id); // ubah id dan postnya
            $this->db->update("bahanbaku", $data);

            $this->session->set_flashdata("success", " Berhasil Update Data Stok  " . $this->input->post('nama_bahanbaku') . " !");
            redirect(base_url("bahanbaku/stok"));
        } else {
            $this->session->set_flashdata("failed", " Gagal Update Data Stok ! " . validation_errors());
            redirect(base_url("bahanbaku/stok"));
        }
    }

    public function tambah()
    {
        $kode = $this->db->query("SELECT * FROM bahanbaku ORDER BY id DESC LIMIT 1");

        if ($kode->num_rows() > 0) {
            $ps = $kode->row();
            $kode_num = $ps->id + 1;
        } else {
            $kode_num = 1;
        }

        $this->data = [
            'title_web' => 'Tambah Bahan Baku',
            // 'kode'  	=> 'P000'.$kode_cus,
            'kode'      => 'BB' . sprintf('%06d', intval($kode_num) + 1),
            'kat'       => $this->db->get('kategori')->result()
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/bahanbaku/tambah', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function store()
    {
        $this->form_validation->set_rules("id_kategori", "Id kategori", "required");
        $this->form_validation->set_rules("kode_bahanbaku", "Kode Bahan baku", "required");
        $this->form_validation->set_rules("nama", "Nama", "required");
        $this->form_validation->set_rules("harga_pokok", "Harga pokok", "required");
        if ($this->form_validation->run() != false) {
            $data = [
                'id_kategori'   => htmlspecialchars($this->input->post("id_kategori", true), ENT_QUOTES),
                'kode_bahanbaku'     => htmlspecialchars($this->input->post("kode_bahanbaku", true), ENT_QUOTES),
                'nama'          => htmlspecialchars($this->input->post("nama", true), ENT_QUOTES),
                'harga_pokok'   => htmlspecialchars($this->input->post("harga_pokok", true), ENT_QUOTES),
                'keterangan'    => htmlspecialchars($this->input->post("keterangan", true), ENT_QUOTES),
                'stok'          => 0,
                'stok_minim'    => (int)$this->input->post("stok_minim"),
                'created_at'    => date('Y-m-d H:i:s'),
            ];


            $this->db->insert("bahanbaku", $data);
            $this->session->set_flashdata("success", " Berhasil Insert Data ! ");
            redirect(base_url("bahanbaku"));
        } else {
            $this->session->set_flashdata("failed", " Gagal Insert Data ! " . validation_errors());
            redirect(base_url("bahanbaku"));
        }
    }

    public function detail()
    {
        $id = (int)$this->uri->segment('3');
        $cek = $this->db->get_where("bahanbaku", ["id" => $id]); // tulis id yang dituju
        if ($cek->num_rows() > 0) {
            $edit = $cek->row();
        } else {
            $this->session->set_flashdata("failed", " Tidak ditemukan data ID dari Menu ! ");
            redirect(base_url('bahanbaku'));
        }

        $this->data = [
            'title_web' => 'Detail Bahan Baku',
            'edit'        => $edit,
            'kat'       => $this->db->get('kategori')->result()
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/bahanbaku/detail', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function edit()
    {
        $id = (int)$this->uri->segment('3');
        $cek = $this->db->get_where("bahanbaku", ["id" => $id]); // tulis id yang dituju
        if ($cek->num_rows() > 0) {
            $edit = $cek->row();
        } else {
            $this->session->set_flashdata("failed", " Tidak ditemukan data ID dari Menu ! ");
            redirect(base_url('menu'));
        }

        $this->data = [
            'title_web' => 'Edit Bahan Baku',
            'edit'        => $edit,
            'kat'       => $this->db->get('kategori')->result()
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/bahanbaku/edit', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function update()
    {
        $id =  (int)$this->input->post("id"); // parameter yang mau di update

        $this->form_validation->set_rules("id", "Id", "required");
        $this->form_validation->set_rules("id_kategori", "Id kategori", "required");
        $this->form_validation->set_rules("kode_bahanbaku", "Kode Bahan Baku", "required");
        $this->form_validation->set_rules("nama", "Nama", "required");
        $this->form_validation->set_rules("harga_pokok", "Harga pokok", "required");

        if ($this->form_validation->run() != false) {
            $data = [
                'id_kategori' => htmlspecialchars($this->input->post("id_kategori", true), ENT_QUOTES),
                'kode_bahanbaku' => htmlspecialchars($this->input->post("kode_bahanbaku", true), ENT_QUOTES),
                'nama' => htmlspecialchars($this->input->post("nama", true), ENT_QUOTES),
                'harga_pokok' => htmlspecialchars($this->input->post("harga_pokok", true), ENT_QUOTES),
                'keterangan' => htmlspecialchars($this->input->post("keterangan", true), ENT_QUOTES),
                'stok_minim' => (int)$this->input->post("stok_minim"),
            ];

            $this->db->where("id", $id); // ubah id dan postnya
            $this->db->update("bahanbaku", $data);
            $this->session->set_flashdata("success", " Berhasil Update Data ! ");
            redirect(base_url("bahanbaku/edit/" . $id));
        } else {
            $this->session->set_flashdata("failed", " Gagal Update Data ! " . validation_errors());
            redirect(base_url("bahanbaku/edit/" . $id));
        }
    }

    public function delete()
    {
        $id = (int)$this->input->get("id");
        $cek = $this->db->get_where("bahanbaku", ["id" => $id]); // tulis id yang dituju
        if ($cek->num_rows() > 0) {
            $hasil = $cek->row();

            // if ($hasil->gambar !== '-') {
            //     if (file_exists(FCPATH.'assets/image/produk/'.$hasil->gambar)) {
            //         unlink(FCPATH.'assets/image/produk/'.$hasil->gambar);
            //     }
            // }

            $this->db->where("id", $id); // tulis id yang dituju
            $this->db->delete("bahanbaku");

            $this->session->set_flashdata("success", " Berhasil Delete Data ! ");
            redirect(base_url("bahanbaku"));
        } else {
            $this->session->set_flashdata("failed", " Gagal Delete Data ! " . validation_errors());
            redirect(base_url("bahanbaku"));
        }
    }
}
