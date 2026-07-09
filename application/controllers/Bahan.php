<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class Bahan extends CI_Controller
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
            'title_web'  => 'Daftar Bahan',
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/bahan/index', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function import()
    {
        $this->data = [
            'title_web'  => 'Daftar Bahan',
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/bahan/import', $this->data);
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
                $cekid  =  cek_id('menu', 'kode_menu', $sheetData[$i]['1']);
                $data = [
                    'kode_menu' => $cekid,
                    'id_kategori' => $kategori,
                    'nama' => $sheetData[$i]['3'],
                    'harga_pokok' => $sheetData[$i]['4'],
                    'harga_jual' => $sheetData[$i]['5'],
                    'stok' => $sheetData[$i]['6'],
                    'stok_minim' => $sheetData[$i]['7'],
                    'gambar' => '-',
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                $this->db->insert("bahan", $data);
            }

            $this->session->set_flashdata("success", " Berhasil Insert Data ! ");
            redirect(base_url("bahan"));
        } else {
            $this->session->set_flashdata("failed", " Gagal Insert Data !  Berkas harus berextensi excel");
            redirect(base_url("bahan"));
        }
    }

    public function dtbahan()
    {
        $cabang_id =  $this->session->userdata('ses_cabang_id');
        $pageNumber = htmlspecialchars($this->input->post('pageHome', true), ENT_QUOTES);
        $halperpage = 12;
        $page = isset($pageNumber) ? (int)$pageNumber : 1;
        $mulai = ($page > 1) ? ($page * $halperpage) - $halperpage : 0;
        if ($this->input->get('id')) {
            $wr = ' WHERE cabang_id=' . $cabang_id . ' AND id_kategori_bahan = ' . (int)$this->input->get('id') . ' ';
        } elseif ($this->input->get('cari')) {
            $wr = ' WHERE cabang_id=' . $cabang_id . ' AND nama_kategori LIKE "%' . $this->input->get('cari') . '%" OR kategori_bahan.nama_kategori LIKE "%' . $this->input->get('cari') . '%"';
        } else {
            $wr = 'WHERE cabang_id=' . $cabang_id;
        }
        $query = "SELECT kategori_bahan.nama_kategori, bahan.* FROM bahan LEFT JOIN kategori_bahan ON bahan.id_kategori_bahan = kategori_bahan.id";
        $hasil = $this->db->query($query . " $wr ORDER BY id ASC LIMIT $mulai, $halperpage")->result();
        $this->data['hasil'] = $hasil;
        $this->load->view('admin/bahanstokawal/bahan', $this->data);
    }

    public function data_bahan()
    {
        $cabang_id =  $this->session->userdata('ses_cabang_id');
        $level =  $this->session->userdata('ses_level');
        $where = '';

        if ($this->input->method(true) == 'POST') :
            $query = "SELECT kategori_bahan.nama_kategori, cabang.kode_cabang, bahan.* FROM bahan LEFT JOIN kategori_bahan ON bahan.id_kategori_bahan = kategori_bahan.id LEFT JOIN cabang ON bahan.cabang_id = cabang.id ";
            $search = array('kode_bahan', 'kategori_bahan.nama_kategori', 'nama_bahan', 'harga_pokok', 'harga_jual', 'keterangan', 'cabang.kode_cabang');
            if ((int)$this->input->get('id')) {
                // $where  = array('cabang_id' => $cabang_id, 'id_kategori' => (int)$this->input->get('id'));
            } else {
                if ($level == 'Admin') {
                } else {
                    // $where  = array('cabang_id' => $cabang_id);
                }
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

    public function stok_copy()
    {
        if ($this->input->get('id')) {
            $this->data = [
                'title_web'  => 'Entry Persediaan Bahan',
                'tipe'       => 'edit',
                'edit'       => $this->db->query(
                    "SELECT * FROM bahan WHERE bahan.id=?",
                    array($this->input->get('id'))
                )->row(),
            ];
        } else {
            $this->data = [
                'title_web'  => 'Entry Persediaan Bahan',
                'tipe'       => ''
            ];
        }

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/bahan/stok', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function persediaan()
    {
        $this->data = [
            'title_web'  => 'Daftar Persediaan Bahan',
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/bahan/persediaan', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function get_bahan()
    {
        $id =  (int)$this->input->post("id"); // parameter yang mau di update
        $cek = $this->db->query("SELECT * FROM bahan WHERE bahan.id=?", array($id)); // tulis id yang dituju
        if ($cek->num_rows() > 0) {
            $data = $cek->row();
            $result[] = array(
                'id' => $data->id,
                'nama_bahan' => $data->nama_bahan,
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

            $this->db->insert("menu_stok", $data_r);

            $data = [
                'stok' => (int)$this->input->post("stok"),
                'stok_minim' => (int)$this->input->post("stok_minim"),
            ];

            $this->db->where("id", $id); // ubah id dan postnya
            $this->db->update("menu", $data);

            $this->session->set_flashdata("success", " Berhasil Update Data Stok  " . $this->input->post('nama_menu') . " !");
            redirect(base_url("menu/persediaan"));
        } else {
            $this->session->set_flashdata("failed", " Gagal Update Data Stok ! " . validation_errors());
            redirect(base_url("menu/persediaan"));
        }
    }

    public function tambah()
    {
        $kode = $this->db->query("SELECT * FROM bahan ORDER BY id DESC LIMIT 1");

        if ($kode->num_rows() > 0) {
            $ps = $kode->row();
            $kode_num = $ps->id + 1;
        } else {
            $kode_num = 1;
        }

        $this->data = [
            'title_web' => 'Tambah Bahan',
            // 'kode'  	=> 'P000'.$kode_cus,
            // 'kode'      => 'BN' . sprintf('%06d', intval($kode_num)),
            'kode'      => '',
            'kat'       => $this->db->get('kategori_bahan')->result()
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/bahan/tambah', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function store()
    {
        // $this->form_validation->set_rules("id_kategori_bahan", "Kategori Bahan", "required");
        $this->form_validation->set_rules("kode_bahan", "Kode Bahan", "required");
        $this->form_validation->set_rules("nama_bahan", "Nama Bahan", "required");
        $this->form_validation->set_rules("konversi", "Konversi", "required");
        // $this->form_validation->set_rules("harga_pokok", "Harga pokok", "required");
        // $this->form_validation->set_rules("harga_jual", "Harga jual", "required");
        if ($this->form_validation->run() != false) {
            $data = [
                // 'id_kategori_bahan'   => htmlspecialchars($this->input->post("id_kategori_bahan", true), ENT_QUOTES),
                'id_kategori_bahan'   => 2,
                'kode_bahan'     => htmlspecialchars($this->input->post("kode_bahan", true), ENT_QUOTES),
                'nama_bahan'     => htmlspecialchars($this->input->post("nama_bahan", true), ENT_QUOTES),
                // 'harga_pokok'   => htmlspecialchars($this->input->post("harga_pokok", true), ENT_QUOTES),
                'harga_pokok'   => 0,
                // 'harga_jual'    => htmlspecialchars($this->input->post("harga_jual", true), ENT_QUOTES),
                'harga_jual'    => 0,
                'keterangan'    => htmlspecialchars($this->input->post("keterangan", true), ENT_QUOTES),
                'stok'          => 0,
                'stok_minim'    => (int)$this->input->post("stok_minim"),
                'created_at'    => date('Y-m-d H:i:s'),
                'cabang_id'    => 1,
                'konversi'     => (int)$this->input->post("konversi"),

            ];

            $this->db->insert("bahan", $data);
            $this->session->set_flashdata("success", " Berhasil Insert Data ! ");
            redirect(base_url("bahan"));
        } else {
            $this->session->set_flashdata("failed", " Gagal Insert Data ! " . validation_errors());
            redirect(base_url("bahan"));
        }
    }

    public function detail()
    {
        $id = (int)$this->uri->segment('3');
        $cek = $this->db->get_where("bahan", ["id" => $id]); // tulis id yang dituju
        if ($cek->num_rows() > 0) {
            $edit = $cek->row();
        } else {
            $this->session->set_flashdata("failed", " Tidak ditemukan data ID dari Bahan ! ");
            redirect(base_url('bahan'));
        }

        $this->data = [
            'title_web' => 'Detail Bahan',
            'edit'        => $edit,
            'kat'       => $this->db->get('kategori_bahan')->result()
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/bahan/detail', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function edit()
    {
        $id = (int)$this->uri->segment('3');
        $cek = $this->db->get_where("bahan", ["id" => $id]); // tulis id yang dituju
        if ($cek->num_rows() > 0) {
            $edit = $cek->row();
        } else {
            $this->session->set_flashdata("failed", " Tidak ditemukan data ID dari Bahan ! ");
            redirect(base_url('bahan'));
        }

        $this->data = [
            'title_web' => 'Edit Bahan',
            'edit'        => $edit,
            'kat'       => $this->db->get('kategori_bahan')->result()
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/bahan/edit', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function update()
    {
        $id =  (int)$this->input->post("id"); // parameter yang mau di update

        $this->form_validation->set_rules("id", "Id", "required");
        // $this->form_validation->set_rules("id_kategori_bahan", "Kategori Bahan", "required");
        $this->form_validation->set_rules("kode_bahan", "Kode Bahan", "required");
        $this->form_validation->set_rules("nama_bahan", "Nama Bahan", "required");
        $this->form_validation->set_rules("konversi", "Konversi", "required");
        // $this->form_validation->set_rules("harga_pokok", "Harga Pokok", "required");
        // $this->form_validation->set_rules("harga_jual", "Harga Jual", "required");

        if ($this->form_validation->run() != false) {
            $data = [
                // 'id_kategori_bahan'   => htmlspecialchars($this->input->post("id_kategori_bahan", true), ENT_QUOTES),
                'id_kategori_bahan'   => 2,
                'kode_bahan'     => htmlspecialchars($this->input->post("kode_bahan", true), ENT_QUOTES),
                'nama_bahan'     => htmlspecialchars($this->input->post("nama_bahan", true), ENT_QUOTES),
                // 'harga_pokok'   => htmlspecialchars($this->input->post("harga_pokok", true), ENT_QUOTES),
                'harga_pokok'   => 0,
                // 'harga_jual'    => htmlspecialchars($this->input->post("harga_jual", true), ENT_QUOTES),
                'harga_jual'    => 0,
                'keterangan'    => htmlspecialchars($this->input->post("keterangan", true), ENT_QUOTES),
                'stok'          => 0,
                'stok_minim'    => (int)$this->input->post("stok_minim"),
                'konversi'     => (int)$this->input->post("konversi"),
            ];

            $this->db->where("id", $id); // ubah id dan postnya
            $this->db->update("bahan", $data);
            $this->session->set_flashdata("success", " Berhasil Update Data ! ");
            // redirect(base_url("bahan/edit/" . $id));
            redirect(base_url("bahan"));
        } else {
            $this->session->set_flashdata("failed", " Gagal Update Data ! " . validation_errors());
            redirect(base_url("bahan/edit/" . $id));
        }
    }

    public function delete()
    {
        $id = (int)$this->input->get("id");
        $cek = $this->db->get_where("bahan", ["id" => $id]); // tulis id yang dituju
        if ($cek->num_rows() > 0) {
            $hasil = $cek->row();

            $this->db->where("id", $id); // tulis id yang dituju
            $this->db->delete("bahan");

            $this->session->set_flashdata("success", " Berhasil Delete Data ! ");
            redirect(base_url("bahan"));
        } else {
            $this->session->set_flashdata("failed", " Gagal Delete Data ! " . validation_errors());
            redirect(base_url("bahan"));
        }
    }

    //  STOK BAHAN
    public function stok()
    {
        $this->data = [
            'title_web'  => 'Daftar Stok Bahan',
            'cab'       => $this->db->query("SELECT profil_toko.nama_toko,cabang.* FROM cabang LEFT JOIN profil_toko ON profil_toko.cabang_id = cabang.id ORDER BY length(kode_cabang),kode_cabang ASC")->result(),
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/bahan/stok', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function data_stok_bahan()
    {

        // $cabang_id =  $this->session->userdata('ses_cabang_id');
        $level =  $this->session->userdata('ses_level');
        $where = '';

        if ($this->input->method(true) == 'POST') :
            // $query = "SELECT cabang.kode_cabang, bahan.nama_bahan,bahan.satuan,bahan_stokawal.* FROM bahan_stokawal LEFT JOIN bahan ON bahan_stokawal.bahan_id = bahan.id LEFT JOIN cabang ON bahan_stokawal.cabang_id = cabang.id ";
            $query = "SELECT cabang.kode_cabang,bahan.kode_bahan, bahan.nama_bahan,bahan.konversi,bahan_stok.* 
                FROM bahan_stok
                LEFT JOIN bahan ON bahan_stok.bahan_id = bahan.id 
                LEFT JOIN cabang ON bahan_stok.cabang_id = cabang.id ";
            $search = array('nama_bahan', 'kode_bahan', 'cabang.kode_cabang');
            if ((int)$this->input->get('id')) {
                $cabang_id = $this->input->get('id');

                $where  = array('bahan_stok.cabang_id' => $cabang_id);
            } else {
                if ($level == 'Admin') {
                } else {
                    // $where  = array('cabang_id' => $cabang_id);
                }
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

    public function stokawal()
    {
        $this->data = [
            'title_web'  => 'Stok Awal Bahan',
            'cab'       => $this->db->query("SELECT profil_toko.nama_toko,cabang.* FROM cabang LEFT JOIN profil_toko ON profil_toko.cabang_id = cabang.id ORDER BY length(kode_cabang),kode_cabang ASC")->result(),
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/bahan/stokawal', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function data_stokawal_bahan()
    {

        // $cabang_id =  $this->session->userdata('ses_cabang_id');
        $level =  $this->session->userdata('ses_level');
        $where = '';

        if ($this->input->method(true) == 'POST') :
            $query = "SELECT cabang.kode_cabang, bahan.nama_bahan,bahan.satuan,bahan_stokawal.* FROM bahan_stokawal LEFT JOIN bahan ON bahan_stokawal.bahan_id = bahan.id LEFT JOIN cabang ON bahan_stokawal.cabang_id = cabang.id ";
            $search = array('nama_bahan', 'cabang.kode_cabang');
            if ((int)$this->input->get('id')) {
                $cabang_id = $this->input->get('id');

                $where  = array('bahan_stokawal.cabang_id' => $cabang_id);
            } else {
                if ($level == 'Admin') {
                } else {
                    // $where  = array('cabang_id' => $cabang_id);
                }
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

    public function tambah_stokawal()
    {
        // $kode = $this->db->query("SELECT * FROM bahan_stokawal ORDER BY id DESC LIMIT 1");

        // if ($kode->num_rows() > 0) {
        //     $ps = $kode->row();
        //     $kode_num = $ps->id + 1;
        // } else {
        //     $kode_num = 1;
        // }

        $this->data = [
            'title_web' => 'Tambah Stok Awal Bahan',
            // 'kode'  	=> 'P000'.$kode_cus,
            // 'kode'      => 'BN' . sprintf('%06d', intval($kode_num)),
            'kode'      => '',
            'cab'       => $this->db->query("SELECT * FROM cabang ORDER BY length(kode_cabang),kode_cabang ASC")->result(),
            'bhn'       => $this->db->get('bahan')->result()
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/bahan/tambah_stokawal', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    // public function checkDuplicateStokAwal($bahan_id, $cabang_id)
    // {
    //     $this->db->where('bahan_id', $bahan_id);
    //     $this->db->where('cabang_id', $cabang_id);
    //     $this->db->where('tipe_transaksi', 'Saldo Awal');
    //     $query = $this->db->get('bahan_kartu_stok');

    //     return $query->num_rows() > 0; // True if a duplicate exists
    // }

    public function store_stokawal()
    {
        $this->form_validation->set_rules("id_cabang", "Cabang", "required");
        $this->form_validation->set_rules("id_bahan", "Bahan", "required");
        $this->form_validation->set_rules("stok_awal", "Stok Awal", "required");
        // $this->form_validation->set_rules("harga_beli", "Harga Beli", "required");

        $cabang_id = $this->input->post("id_cabang", true);
        $bahan_id = $this->input->post("id_bahan", true);

        $this->db->where('bahan_id', $bahan_id);
        $this->db->where('cabang_id', $cabang_id);
        $query = $this->db->get('bahan_stokawal');
        if ($query->num_rows() > 0) {
            $this->session->set_flashdata("failed", " Data stok awal bahan sudah ada! " . validation_errors());
            redirect(base_url("bahan/stokawal"));
        }

        $tanggal = date('Y-m-d H:i:s');
        $tgl = date_create($tanggal);

        if ($this->form_validation->run() != false) {

            $data = [
                'cabang_id'   => htmlspecialchars($cabang_id, ENT_QUOTES),
                'bahan_id'     => htmlspecialchars($bahan_id, ENT_QUOTES),
                'stok_awal'     => htmlspecialchars($this->input->post("stok_awal", true), ENT_QUOTES),
                // 'harga_beli'   => htmlspecialchars($this->input->post("harga_beli", true), ENT_QUOTES),
                'harga_beli'   => 0,
                'created_at'    => date('Y-m-d H:i:s'),
                'periode' => date_format($tgl, 'Y-m'),
                'tahun' => date_format($tgl, 'Y')
            ];

            $this->db->insert("bahan_stokawal", $data);

            $last_trans = $this->db->query("SELECT * FROM bahan_stokawal WHERE cabang_id = $cabang_id AND bahan_id = $bahan_id ORDER BY id DESC LIMIT 1");
            if ($last_trans->num_rows() > 0) {
                $last_trans_row = $last_trans->row();
                $last_id = $last_trans_row->id;
            } else {
                $last_id = 0;
            }
            $jumlah_harga    = $this->input->post('stok_awal') * $this->input->post('harga_beli');
            $data_kartustok = [
                'cabang_id'   => htmlspecialchars($cabang_id, ENT_QUOTES),
                'bahan_id'     => htmlspecialchars($bahan_id, ENT_QUOTES),
                'tipe_transaksi'     => "Saldo Awal",
                'transaksi_id'     => $last_id,
                'jumlah_perubahan'     => htmlspecialchars($this->input->post("stok_awal", true), ENT_QUOTES),
                'harga_beli'   => htmlspecialchars($this->input->post("harga_beli", true), ENT_QUOTES),
                'jumlah_harga'   => $jumlah_harga,
                'total_stok'   => htmlspecialchars($this->input->post("stok_awal", true), ENT_QUOTES),
                'total_jumlah_harga'   => $jumlah_harga,
                'tanggal' => date_format($tgl, 'Y-m-d'),
                'created_at'    => date('Y-m-d H:i:s'),
                'periode' => date_format($tgl, 'Y-m'),
                'tahun' => date_format($tgl, 'Y')
            ];

            $this->db->insert("bahan_kartustok", $data_kartustok);

            //cek stok_batch

            $this->db->where('bahan_id', $bahan_id);
            $this->db->where('cabang_id', $cabang_id);
            $query = $this->db->get('bahan_stok');
            if ($query->num_rows() == 0) { // jika belum ada
                $data_stok = [
                    'cabang_id'   => htmlspecialchars($cabang_id, ENT_QUOTES),
                    'bahan_id'     => htmlspecialchars($bahan_id, ENT_QUOTES),
                    'jumlah_stok'     => htmlspecialchars($this->input->post("stok_awal", true), ENT_QUOTES)
                ];

                $this->db->insert("bahan_stok", $data_stok);
            } else {
                $data_stok = [
                    'cabang_id'   => htmlspecialchars($cabang_id, ENT_QUOTES),
                    'bahan_id'     => htmlspecialchars($bahan_id, ENT_QUOTES),
                    'jumlah_stok'     => 'jumlah_stok' + $this->input->post("stok_awal", true)
                ];

                $this->db->where('bahan_id', $bahan_id);
                $this->db->where('cabang_id', $cabang_id);
                $this->db->update("bahan_stok", $data_stok);
            }; // jika sudah ada

            $this->session->set_flashdata("success", " Berhasil Insert Data Stok Awal ! ");
            redirect(base_url("bahan/stokawal"));
        } else {
            $this->session->set_flashdata("failed", " Gagal Insert Data Stok Awal! " . validation_errors());
            redirect(base_url("bahan/stokawal"));
        }
    }

    // Kartu Stok
    public function kartustok()
    {
        // $id = (int)$this->uri->segment('3');
        $bahan_id = $this->input->get('idb');

        $cekbahan = $this->db->get_where("bahan", ["id" => $bahan_id]); // tulis id yang dituju
        if ($cekbahan->num_rows() > 0) {
            $detailbahan = $cekbahan->row();

            $cekkartustok = $this->db->get_where("bahan_kartustok", ["bahan_id" => $bahan_id]); // tulis id yang dituju
            if ($cekkartustok->num_rows() > 0) {
                $kartustok = $cekkartustok->row();
            } else {
                $this->session->set_flashdata("failed", " Tidak ditemukan data kartu stok dari Bahan ! ");
                redirect(base_url('bahan'));
            }
        } else {
            $this->session->set_flashdata("failed", " Tidak ditemukan data ID dari Bahan ! ");
            redirect(base_url('bahan'));
        }


        $this->data = [
            'title_web'  => 'Kartu Stok Bahan ',
            'detailbahan'        => $detailbahan,
            'kartustok'        => $kartustok,
            'cab'       => $this->db->query("SELECT profil_toko.nama_toko,cabang.* FROM cabang LEFT JOIN profil_toko ON profil_toko.cabang_id = cabang.id ORDER BY length(kode_cabang),kode_cabang ASC")->result(),
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/bahan/kartustok', $this->data);
        $this->load->view('layout/footer', $this->data);

        // $id = (int)$this->uri->segment('3');
        // $cek = $this->db->get_where("bahan_kartustok", ["id" => $id]); // tulis id yang dituju
        // if ($cek->num_rows() > 0) {
        //     $edit = $cek->row();
        // } else {
        //     $this->session->set_flashdata("failed", " Tidak ditemukan Kartu Stok dari Bahan ! ");
        //     redirect(base_url('bahan'));
        // }

        // $this->data = [
        //     'title_web' => 'Kartu Stok',
        //     'edit'        => $edit,
        //     'kat'       => $this->db->get('kategori_bahan')->result()
        // ];

        // $this->load->view('layout/header', $this->data);
        // $this->load->view('admin/bahan/kartustok', $this->data);
        // $this->load->view('layout/footer', $this->data);
    }

    public function data_kartustok()
    {
        // $cabang_id =  $this->session->userdata('ses_cabang_id');
        $level =  $this->session->userdata('ses_level');
        $where = '';

        if ($this->input->method(true) == 'POST') :
            $query = "SELECT  cabang.kode_cabang, bahan_kartustok.* FROM bahan_kartustok LEFT JOIN cabang ON bahan_kartustok.cabang_id = cabang.id ";
            $search = array('cabang.kode_cabang');
            if ((int)$this->input->get('id')) {
                $cabang_id = $this->input->get('id');
                $bahan_id = $this->input->get('idb');

                $where  = array('bahan_kartustok.cabang_id' => $cabang_id, 'bahan_kartustok.bahan_id' => $bahan_id);
                // $where  = array('cabang_id' => $cabang_id, 'id_kategori' => (int)$this->input->get('id'));
            } else {
                $bahan_id = $this->input->get('idb');
                $where  = array('bahan_kartustok.bahan_id' => $bahan_id);

                if ($level == 'Admin') {
                } else {
                    // $where  = array('cabang_id' => $cabang_id);
                }
            }
            $iswhere = null;

            // if ($this->input->get('cek')) {
            //     $iswhere = " stok <= stok_minim ";
            // } else {
            //     $iswhere = null;
            // }
            header('Content-Type: application/json');
            echo $this->M_Datatables->get_tables_query($query, $search, $where, $iswhere);
        endif;
    }

    public function transferstok_multi()
    {
        $this->data = [
            'title_web'  => 'Transfer Stok Bahan',
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/bahan/transferstok_multi', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function transferstok()
    {
        $this->data = [
            'title_web'  => 'Transfer Stok Bahan',
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/bahan/transferstok', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function data_transferstok()
    {
        $level =  $this->session->userdata('ses_level');
        $where = '';

        if ($this->input->method(true) == 'POST') :
            $query = "SELECT 
                cabang_asal.kode_cabang AS kode_cabang_asal,
                cabang_tujuan.kode_cabang AS kode_cabang_tujuan,
                transferstok.*
            FROM transferstok
            LEFT JOIN cabang AS cabang_asal 
                ON transferstok.cabangasal_id = cabang_asal.id
            LEFT JOIN cabang AS cabang_tujuan 
                ON transferstok.cabangtujuan_id = cabang_tujuan.id ";

            $search = array('cabang_tujuan.kode_cabang', 'transferstok.no_surat');
            if ((int)$this->input->get('id')) {
                // $where  = array('cabang_id' => $cabang_id, 'id_kategori' => (int)$this->input->get('id'));
            } else {
                if ($level == 'Admin') {
                } else {
                    // $where  = array('cabang_id' => $cabang_id);
                }
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

    public function transferstok_terima()
    {
        $this->data = [
            'title_web'  => 'Terima Stok Bahan',
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/bahan/transferstok_terima', $this->data);
        $this->load->view('layout/footer', $this->data);
    }



    public function transferstok_terima_detail()
    {
        $id = (int)$this->uri->segment('3');

        $cek = $this->db->get_where("transferstok", ["id" => $id]); // tulis id yang dituju
        if ($cek->num_rows() > 0) {
            $edit = $cek->row();
        } else {
            $this->session->set_flashdata("failed", " Tidak ditemukan data transfer stok ! ");
            redirect(base_url('bahan/transferstok_terima'));
        }

        $this->data = [
            'title_web'  => 'Data Terima Stok bahan',
            'edit'        => $edit,

        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/bahan/transferstok_terima_detail', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function data_transferstok_terima()
    {
        $level =  $this->session->userdata('ses_level');
        $cabang_id =  $this->session->userdata('ses_cabang_id');
        $where = '';

        if ($this->input->method(true) == 'POST') :
            $query = "SELECT 
                cabang_asal.kode_cabang AS kode_cabang_asal,
                cabang_tujuan.kode_cabang AS kode_cabang_tujuan,
                transferstok.*
            FROM transferstok
            LEFT JOIN cabang AS cabang_asal 
                ON transferstok.cabangasal_id = cabang_asal.id
            LEFT JOIN cabang AS cabang_tujuan 
                ON transferstok.cabangtujuan_id = cabang_tujuan.id ";

            $search = array('cabang_tujuan.kode_cabang');
            if ((int)$this->input->get('id')) {
                // $where  = array('cabang_id' => $cabang_id, 'id_kategori' => (int)$this->input->get('id'));
            } else {
                if ($level == 'Admin') {
                } else {
                    // $where  = array('cabang_id' => $cabang_id);
                }
            }
            $where  = array('cabangtujuan_id' => $cabang_id);

            if ($this->input->get('cek')) {
                $iswhere = " stok <= stok_minim ";
            } else {
                $iswhere = null;
            }

            header('Content-Type: application/json');
            echo $this->M_Datatables->get_tables_query($query, $search, $where, $iswhere);

        endif;
    }

    public function data_transferstok_terima_detail()
    {
        $cabang_id =  $this->session->userdata('ses_cabang_id');
        $level =  $this->session->userdata('ses_level');
        $where = '';
        $iswhere = '';
        $search = array('kode_bahan', 'nama_bahan');


        if ($this->input->method(true) == 'POST') :
            $query = "SELECT 
                b.kode_bahan AS kode_bahan,
                b.nama_bahan AS nama_bahan,
                tb.qty*b.konversi AS jumlah,
                tb.*
            FROM transferstok_bahan tb
            LEFT JOIN bahan b ON b.id = tb.bahan_id";

            if ((int)$this->input->get('id')) {
                $where  = array('tb.transferstok_id' => (int)$this->input->get('id'));
            } else {
                if ($level == 'Admin') {
                } else {
                }
            }

            header('Content-Type: application/json');
            echo $this->M_Datatables->get_tables_query($query, $search, $where, $iswhere);

        endif;
    }

    public function tambah_transferstok()
    {
        // $kode = $this->db->query("SELECT * FROM bahan_stokawal ORDER BY id DESC LIMIT 1");

        // if ($kode->num_rows() > 0) {
        //     $ps = $kode->row();
        //     $kode_num = $ps->id + 1;
        // } else {
        //     $kode_num = 1;
        // }
        $imp = $this->uri->segment('3');
        if ($imp <> 'import') {
            $login_id =  $this->session->userdata('ses_id');
            $this->db->delete('transferstok_bahan_temp', ['login_id' => $login_id]);
        }
        $temp_tanggal =  $this->session->userdata('ses_temp_tanggal');
        $temp_nosurat =  $this->session->userdata('ses_temp_nosurat');
        $temp_tujuan =  $this->session->userdata('ses_temp_tujuan');
        $cek = $this->db->get_where("profil_toko", ["nama_toko" => $temp_tujuan]); // tulis id yang dituju
        if ($cek->num_rows() > 0) {
            $tujuan = $cek->row();
            $temp_tujuan = $tujuan->cabang_id;
            // } else {
            //     $this->session->set_flashdata("failed", " Tidak ditemukan data transfer stok ! ");
            //     redirect(base_url('bahan/transferstok_terima'));
        }
        $this->session->set_userdata('ses_temp_tanggal', '');
        $this->session->set_userdata('ses_temp_nosurat', '');
        $this->session->set_userdata('ses_temp_tujuan', '');
        $this->data = [
            'title_web' => 'Tambah Transfer Stok Bahan',
            // 'kode'  	=> 'P000'.$kode_cus,
            // 'kode'      => 'BN' . sprintf('%06d', intval($kode_num)),
            'temptanggal' => $temp_tanggal,
            'tempnosurat' => $temp_nosurat,
            'temptujuan' => $temp_tujuan,
            'kode'      => '',
            'cab'       => $this->db->query("SELECT profil_toko.nama_toko,cabang.* FROM cabang LEFT JOIN profil_toko ON profil_toko.cabang_id = cabang.id WHERE cabang.id > 1 ORDER BY length(kode_cabang),kode_cabang ASC")->result(),
            'bahan'       => $this->db->get('bahan')->result()
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/bahan/tambah_transferstok', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function tambah_transferstok_multi()
    {
        $login_id = $this->session->userdata('ses_id');

        $is_import = ($this->uri->segment(3) == 'import');

        // Jika bukan dari import, kosongkan temporary
        if (!$is_import) {
            $this->db->delete(
                'transferstok_bahan_bulk_temp',
                [
                    'login_id' => $login_id
                ]
            );
        }

        // Ambil seluruh data temporary
        $temp = $this->db
            ->select('
            transferstok_bahan_bulk_temp.*,
            profil_toko.nama_toko
        ')
            ->from('transferstok_bahan_bulk_temp')
            ->join(
                'profil_toko',
                'profil_toko.cabang_id=transferstok_bahan_bulk_temp.cabang_id',
                'left'
            )
            ->where(
                'transferstok_bahan_bulk_temp.login_id',
                $login_id
            )
            ->order_by('tanggal')
            ->order_by('no_surat')
            ->order_by('nama_toko')
            ->get()
            ->result();

        // Header import
        $header_import = [];

        if ($is_import) {

            $header_import = $this->db
                ->select("
                tanggal,
                no_surat,
                cabang_id,
                tujuan,
                COUNT(*) jumlah_item
            ")
                ->from("transferstok_bahan_bulk_temp")
                ->where("login_id", $login_id)
                ->group_by("
                tanggal,
                no_surat,
                cabang_id,
                tujuan
            ")
                ->order_by("tanggal")
                ->order_by("no_surat")
                ->get()
                ->result();
        }

        $this->data = [

            'title_web' => 'Tambah Transfer Stok Bahan Multi Cabang',

            'is_import' => $is_import,

            'header_import' => $header_import,

            'tempdata' => $temp,

            'kode' => '',

            'cab' => $this->db->query("
            SELECT
                profil_toko.nama_toko,
                cabang.*
            FROM cabang
            LEFT JOIN profil_toko
                ON profil_toko.cabang_id=cabang.id
            WHERE cabang.id>1
            ORDER BY LENGTH(kode_cabang),kode_cabang
        ")->result(),

            'bahan' => $this->db
                ->order_by('kode_bahan')
                ->get('bahan')
                ->result()

        ];

        $this->load->view(
            'layout/header',
            $this->data
        );

        $this->load->view(
            'admin/bahan/tambah_transferstok_multi',
            $this->data
        );

        $this->load->view(
            'layout/footer',
            $this->data
        );
    }

    public function edit_transferstok()
    {
        $id = (int)$this->uri->segment('3');
        $cek = $this->db->get_where("transferstok", ["id" => $id]); // tulis id yang dituju
        if ($cek->num_rows() > 0) {
            $edit = $cek->row();
            if ($edit->status == 0) {
                $transferstok_temp = $this->db->get_where("transferstok_bahan", ["transferstok_id" => $id]); // tulis id yang dituju
                // Check if any rows were returned
                if ($transferstok_temp->num_rows() > 0) {
                    // Get all rows as an array
                    $rows = $transferstok_temp->result_array();
                    $login_id =  $this->session->userdata('ses_id');
                    $this->db->delete('transferstok_bahan_temp', ['login_id' => $login_id]);

                    // Insert each row into the destination table
                    foreach ($rows as $row) {
                        $cari = $this->db->get_where("bahan", ["id" => $row['bahan_id']]); // tulis id yang dituju
                        $bahan = $cari->row();
                        $insert_data = [
                            'login_id' => htmlspecialchars($login_id, ENT_QUOTES),
                            'bahan_id' => $row['bahan_id'],
                            'qty' => $row['qty'],
                            'kode_bahan' => $bahan->kode_bahan,
                            'nama_bahan' => $bahan->nama_bahan
                        ];

                        $this->db->insert('transferstok_bahan_temp', $insert_data);
                    }
                }
            } else {
                $this->session->set_flashdata("failed", " Data Transfer Stok yang sudah diterima tidak bisa di edit! ");
                redirect(base_url('bahan/transferstok'));
            }
        } else {
            $this->session->set_flashdata("failed", " Tidak ditemukan data ID dari Transfer Stok ! ");
            redirect(base_url('bahan/transferstok'));
        }

        $this->data = [
            'title_web' => 'Edit Transfer Stok',
            'edit'        => $edit,
            'cab'       => $this->db->query("SELECT profil_toko.nama_toko,cabang.* FROM cabang LEFT JOIN profil_toko ON profil_toko.cabang_id = cabang.id WHERE cabang.id > 1 ORDER BY length(kode_cabang),kode_cabang ASC")->result(),

            // 'kat'       => $this->db->get('kategori_bahan')->result()
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/bahan/edit_transferstok', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function detail_transferstok()
    {
        $id = (int)$this->uri->segment('3');
        $cek = $this->db->get_where("transferstok", ["id" => $id]); // tulis id yang dituju
        if ($cek->num_rows() > 0) {
            $edit = $cek->row();
            // if ($edit->status == 0) {
            $transferstok_temp = $this->db->get_where("transferstok_bahan", ["transferstok_id" => $id]); // tulis id yang dituju
            // Check if any rows were returned
            if ($transferstok_temp->num_rows() > 0) {
                // Get all rows as an array
                $rows = $transferstok_temp->result_array();
                $login_id =  $this->session->userdata('ses_id');
                $this->db->delete('transferstok_bahan_temp', ['login_id' => $login_id]);

                // Insert each row into the destination table
                foreach ($rows as $row) {
                    $cari = $this->db->get_where("bahan", ["id" => $row['bahan_id']]); // tulis id yang dituju
                    $bahan = $cari->row();
                    $insert_data = [
                        'login_id' => htmlspecialchars($login_id, ENT_QUOTES),
                        'bahan_id' => $row['bahan_id'],
                        'qty' => $row['qty'],
                        'kode_bahan' => $bahan->kode_bahan,
                        'nama_bahan' => $bahan->nama_bahan
                    ];

                    $this->db->insert('transferstok_bahan_temp', $insert_data);
                }
            }
            // } else {
            //     $this->session->set_flashdata("failed", " Data Transfer Stok yang sudah diterima tidak bisa di edit! ");
            //     redirect(base_url('bahan/transferstok'));
            // }
        } else {
            $this->session->set_flashdata("failed", " Tidak ditemukan data ID dari Transfer Stok ! ");
            redirect(base_url('bahan/transferstok'));
        }

        $this->data = [
            'title_web' => 'Detail Transfer Stok',
            'edit'        => $edit,
            'cab'       => $this->db->query("SELECT profil_toko.nama_toko,cabang.* FROM cabang LEFT JOIN profil_toko ON profil_toko.cabang_id = cabang.id WHERE cabang.id > 1 ORDER BY length(kode_cabang),kode_cabang ASC")->result(),

            // 'kat'       => $this->db->get('kategori_bahan')->result()
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/bahan/detail_transferstok', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function transferstok_detail()
    {
        $id = (int)$this->uri->segment('3');

        $cek = $this->db->get_where("transferstok", ["id" => $id]); // tulis id yang dituju
        if ($cek->num_rows() > 0) {
            $edit = $cek->row();
        } else {
            $this->session->set_flashdata("failed", " Tidak ditemukan data transfer stok ! ");
            redirect(base_url('bahan/transferstok'));
        }

        $this->data = [
            'title_web'  => 'Data Transfer Stok bahan',
            'edit'        => $edit,

        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/bahan/transferstok_detail', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function transferstok_delete()
    {
        $login_id = $this->session->userdata('ses_id');
        $id = (int)$this->input->get("id");
        $transferstok_id = $id;
        $cek = $this->db->get_where("transferstok", ["id" => $id]); // tulis id yang dituju
        if ($cek->num_rows() > 0) {
            $edit = $cek->row();
            if ($edit->status == 0) {
                // $hasil = $cek->row();
                $tanggal = date('Y-m-d');
                $tgl = date_create($tanggal);
                if (!$tgl) {
                    throw new Exception("Invalid date format");
                }

                // RECURSIVE TRANSFER STOK BAHAN
                // START
                $beforeData = $this->db->get_where("transferstok_bahan", ["transferstok_id" => $transferstok_id])->result_array();
                if (empty($beforeData)) {
                    echo json_encode(['status' => 'error', 'message' => 'Tidak ada data di tabel detail transfer stok.' . $transferstok_id]);
                    return;
                }

                foreach ($beforeData as $isiData) {

                    $this->db->where('bahan_id', $isiData['bahan_id']);
                    $this->db->where('cabang_id', 1);
                    $querystok = $this->db->get('bahan_stok');

                    $total_stok = 0;
                    if ($querystok->num_rows() == 0) {
                        $data_stok = [
                            'cabang_id' => 1,
                            'bahan_id' => $isiData['bahan_id'],
                            'jumlah_stok' => $isiData['qty']
                        ];

                        if (!$this->db->insert("bahan_stok", $data_stok)) {
                            throw new Exception("Failed to insert bahan_stok: " . $this->db->error()['message']);
                        }
                    } else {
                        $row = $querystok->row();
                        $total_stok = $row->jumlah_stok;

                        $this->db->set('jumlah_stok', 'jumlah_stok+' . $isiData['qty'], false);
                        $this->db->where('bahan_id', $isiData['bahan_id']);
                        $this->db->where('cabang_id', 1);
                        if (!$this->db->update("bahan_stok")) {
                            throw new Exception("Failed to update bahan_stok: " . $this->db->error()['message']);
                        }
                    }

                    $data_kartustok[] = array(
                        'cabang_id' => 1,
                        'bahan_id' => $isiData['bahan_id'],
                        'tipe_transaksi' => "Cancelled Transfer Out",
                        'transaksi_id' => $transferstok_id,
                        'jumlah_perubahan' => $isiData['qty'],
                        'harga_beli' => 0,
                        'jumlah_harga' => 0,
                        'total_stok' => $total_stok + $isiData['qty'],
                        'total_jumlah_harga' => 0,
                        'tanggal' => date_format($tgl, 'Y-m-d'),
                        'created_at' => date('Y-m-d H:i:s'),
                        'periode' => date_format($tgl, 'Y-m'),
                        'tahun' => date_format($tgl, 'Y'),
                        'shift_id' => 0,
                        'login_id' => $login_id
                    );
                }
                if (!$this->db->delete('transferstok_bahan', ['transferstok_id' => $transferstok_id])) {
                    throw new Exception("Failed to delete transferstok_bahan: " . $this->db->error()['message']);
                }

                if (!empty($data_kartustok)) {
                    if (!$this->db->insert_batch('bahan_kartustok', $data_kartustok)) {
                        throw new Exception("Failed to insert batch bahan_kartustok: " . $this->db->error()['message']);
                    }
                }

                // END

                $this->db->where("transferstok_id", $id); // tulis id yang dituju
                $this->db->delete("transferstok_bahan");
                $this->db->where("id", $id); // tulis id yang dituju
                $this->db->delete("transferstok");


                $this->session->set_flashdata("success", " Berhasil Hapus Data ! ");
                redirect(base_url("bahan/transferstok"));
            } else {
                $this->session->set_flashdata("failed", " Data Transfer Stok yang sudah diterima tidak bisa di hapus! ");
                redirect(base_url('bahan/transferstok'));
            }
        } else {
            $this->session->set_flashdata("failed", " Gagal Hapus Data ! " . validation_errors());
            redirect(base_url("bahan/transferstok"));
        }
    }

    public function cari_bahan($term)
    {
        $this->db->like('nama_bahan', $term); // Assuming 'name' is the product name column
        $query = $this->db->get('bahan');
        return $query->result_array();
    }


    // Mengambil data tabel sementara
    public function get_bahan_temp()
    {
        $this->load->database();

        $search = $this->input->get('search'); // Query pencarian
        $page = $this->input->get('page') ?? 1; // Halaman saat ini
        $limit = $this->input->get('limit') ?? 5; // Jumlah data per halaman
        $offset = ($page - 1) * $limit; // Hitung offset

        // Hitung total data (dengan pencarian jika ada)
        if (!empty($search)) {
            $this->db->like('nama_bahan', $search);
        }
        $totalRows = $this->db->count_all_results('bahan', false);

        // Ambil data dengan pagination
        $this->db->limit($limit, $offset);
        $query = $this->db->get();
        $data = $query->result();

        echo json_encode([
            'data' => $data,
            'totalRows' => $totalRows,
            'perPage' => $limit,
            'currentPage' => $page,
        ]);
    }

    public function get_transferstok_multi_temp()
    {
        $login_id = $this->session->userdata('ses_id');

        $data = $this->db
            ->select("
            transferstok_bahan_bulk_temp.*,
            profil_toko.nama_toko
        ")
            ->from("transferstok_bahan_bulk_temp")
            ->join(
                "profil_toko",
                "profil_toko.cabang_id=transferstok_bahan_bulk_temp.cabang_id",
                "left"
            )
            ->where(
                "transferstok_bahan_bulk_temp.login_id",
                $login_id
            )
            ->order_by("tanggal", "ASC")
            ->order_by("no_surat", "ASC")
            ->order_by("nama_toko", "ASC")
            ->order_by("kode_bahan", "ASC")
            ->get()
            ->result();

        echo json_encode($data);
    }

    public function get_transferstok_temp()
    {
        $this->load->database();
        $query = $this->db->get('transferstok_bahan_temp');
        echo json_encode($query->result());
    }

    public function get_transferstok_bulk_temp()
    {
        $this->load->database();
        $query = $this->db->get('transferstok_bahan_bulk_temp');
        echo json_encode($query->result());
    }

    public function save_transferstok_bulk_temp()
    {

        $login_id =  $this->session->userdata('ses_id');
        $bahan_id = $this->input->post("bahan_id", true);
        $quantity = $this->input->post("quantity", true);
        $kode_bahan = $this->input->post("kode", true);
        $nama_bahan = $this->input->post("nama", true);
        $tanggal = $this->input->post("tanggal", true);
        $no_surat = $this->input->post("no_surat", true);
        $id_cabang = $this->input->post("id_cabang", true);

        $data = [
            'login_id'   => htmlspecialchars($login_id, ENT_QUOTES),
            'bahan_id'     => htmlspecialchars($bahan_id, ENT_QUOTES),
            'qty' => $quantity,
            'kode_bahan'     => htmlspecialchars($kode_bahan, ENT_QUOTES),
            'nama_bahan'     => htmlspecialchars($nama_bahan, ENT_QUOTES),
            'tanggal'     => htmlspecialchars($tanggal, ENT_QUOTES),
            'no_surat'     => htmlspecialchars($no_surat, ENT_QUOTES),
            'cabang_id'     => htmlspecialchars($id_cabang, ENT_QUOTES),
        ];
        $this->db->insert("transferstok_bahan_bulk_temp", $data);

        $this->session->set_flashdata("success", " Berhasil menambahkan data transfer stok bahan !");
        echo json_encode(['status' => 'success', 'message' => 'Barang berhasil ditambahkan']);
    }

    public function save_transferstok_temp()
    {
        // $this->form_validation->set_rules("id_bahan", "Bahan", "required");
        // $this->form_validation->set_rules("kode_bahan", "Kode Bahan", "required");
        // $this->form_validation->set_rules("nama_bahan", "Nama Bahan", "required");
        // $this->form_validation->set_rules("quantity", "Jumlah", "required");

        // if ($this->form_validation->run() != false) {
        $login_id =  $this->session->userdata('ses_id');
        $bahan_id = $this->input->post("bahan_id", true);
        $quantity = $this->input->post("quantity", true);
        $kode_bahan = $this->input->post("kode", true);
        $nama_bahan = $this->input->post("nama", true);

        $data = [
            'login_id'   => htmlspecialchars($login_id, ENT_QUOTES),
            'bahan_id'     => htmlspecialchars($bahan_id, ENT_QUOTES),
            'qty' => $quantity,
            'kode_bahan'     => htmlspecialchars($kode_bahan, ENT_QUOTES),
            'nama_bahan'     => htmlspecialchars($nama_bahan, ENT_QUOTES),
        ];
        $this->db->insert("transferstok_bahan_temp", $data);

        $this->session->set_flashdata("success", " Berhasil menambahkan data bahan !");
        echo json_encode(['status' => 'success', 'message' => 'Barang berhasil ditambahkan']);

        // redirect(base_url("bahan/stokawal"));
        // } else {
        // $this->session->set_flashdata("failed", " Gagal menambahkan data bahan! " . validation_errors());
        // redirect(base_url("bahan/stokawal"));
        // }

    }

    // Menghapus data dari tabel sementara
    public function delete_transferstok_temp()
    {
        $this->load->database();
        $id = $this->input->post("id", true);

        $this->db->delete('transferstok_bahan_temp', ['id' => $id]);
        echo json_encode(['status' => 'success', 'message' => 'Bahan berhasil dihapus']);
    }

    public function delete_transferstok_temp_all()
    {
        $this->load->database();
        // $id = $this->input->post("id", true);
        $login_id =  $this->session->userdata('ses_id');


        $this->db->delete('transferstok_bahan_temp', ['login_id' => $login_id]);
        echo json_encode(['status' => 'success', 'message' => 'Bahan berhasil dihapus']);
    }

    public function import_transferstok()
    {
        $this->data = [
            'title_web'  => 'Import Transfer Stok',
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/bahan/import_transferstok', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function import_transferstok_multi()
    {
        $this->data = [
            'title_web'  => 'Import Transfer Stok Multi Cabang',
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/bahan/import_transferstok_multi', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function proses_import_transferstok()
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

            $login_id =  $this->session->userdata('ses_id');
            $cekdata = '';
            $tanggal = $sheetData[1]['4'];
            $nosurat = $sheetData[1]['5'];
            $tujuan = $sheetData[1]['6'];
            $this->db->delete('transferstok_bahan_temp', ['login_id' => $login_id]);

            // for ($i = 1; $i < count($sheetData); $i++) {
            for ($i = 1; $i < count($sheetData); $i++) {
                // Skip if the entire row is empty
                if (empty(array_filter($sheetData[$i]))) {
                    continue;
                }
                // $bahan_id = $sheetData[$i]['2'];
                $quantity = $sheetData[$i]['3'];
                $old_str = $sheetData[$i]['1'];
                $kode_bahan = str_replace(' ', '', $old_str);

                // $nama_bahan = $sheetData[$i]['2'];
                // if ($sheetData[$i]['2'] == null) {
                //     $kategori = 1;
                // } else {
                //     $kategori = $sheetData[$i]['2'];
                // }
                // $bahan_id  =  cek_id('bahan', 'kode_bahan', $kode_bahan);
                $cek = $this->db->get_where("bahan", ["kode_bahan" => $kode_bahan]); // tulis id yang dituju
                if ($cek->num_rows() > 0) {
                    $cari = $cek->row();
                    $data = [
                        'login_id'   => htmlspecialchars($login_id, ENT_QUOTES),
                        'bahan_id'     => htmlspecialchars($cari->id, ENT_QUOTES),
                        'qty' => $quantity,
                        'kode_bahan'     => htmlspecialchars($kode_bahan, ENT_QUOTES),
                        'nama_bahan'     => htmlspecialchars($cari->nama_bahan, ENT_QUOTES),
                    ];
                    $this->db->insert("transferstok_bahan_temp", $data);
                    // $cekdata = $cekdata . $data['login_id'] . "," . $data['bahan_id'] . "," . $data['qty'] . "," . $data['kode_bahan'] . "," . $data['nama_bahan'] . ";";
                } else {
                    // $this->session->set_flashdata("failed", " Gagal Ambil Data !  Kode Bahan " . $kode_bahan . " tidak ditemukan. " . count($sheetData) . " First : '" . str_replace(' ', '', $old_str) . "' Query : " . $this->db->last_query());
                    $this->session->set_flashdata("failed", " Gagal Ambil Data !  Kode Bahan " . $kode_bahan . " tidak ditemukan. ");
                    redirect(base_url("bahan/tambah_transferstok"));
                }
            }


            $this->session->set_userdata('ses_temp_tanggal', $tanggal);
            $this->session->set_userdata('ses_temp_nosurat', $nosurat);
            $this->session->set_userdata('ses_temp_tujuan', $tujuan);

            $this->session->set_flashdata("success", " Berhasil Import Data !");
            redirect(base_url("bahan/tambah_transferstok/import"));
        } else {
            $this->session->set_flashdata("failed", " Gagal Import Data !  Berkas harus berextensi excel");
            redirect(base_url("bahan/tambah_transferstok"));
        }
    }

    public function proses_import_transferstok_multi()
    {
        $file_mimes = array(
            'application/octet-stream',
            'application/vnd.ms-excel',
            'application/x-csv',
            'text/x-csv',
            'text/csv',
            'application/csv',
            'application/excel',
            'application/vnd.msexcel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );

        if (
            !isset($_FILES['berkas_excel']['name']) ||
            !in_array($_FILES['berkas_excel']['type'], $file_mimes)
        ) {
            $this->session->set_flashdata("failed", "Berkas harus berupa file Excel");
            redirect(base_url("bahan/tambah_transferstok_multi"));
        }

        $arr_file = explode('.', $_FILES['berkas_excel']['name']);
        $extension = end($arr_file);

        if ($extension == 'csv') {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
        } else {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        }

        $spreadsheet = $reader->load($_FILES['berkas_excel']['tmp_name']);
        $sheetData = $spreadsheet->getActiveSheet()->toArray();

        $login_id = $this->session->userdata('ses_id');

        // kosongkan data temporary
        $this->db->delete('transferstok_bahan_bulk_temp', [
            'login_id' => $login_id
        ]);

        for ($i = 1; $i < count($sheetData); $i++) {

            if (empty(array_filter($sheetData[$i]))) {
                continue;
            }

            //------------------------------------
            // Ambil data excel
            //------------------------------------

            $kode_bahan = trim(str_replace(' ', '', $sheetData[$i][1]));
            $qty        = $sheetData[$i][3];
            $tanggal    = $sheetData[$i][4];
            $no_surat   = trim($sheetData[$i][5]);
            $tujuan     = trim($sheetData[$i][6]);

            //------------------------------------
            // validasi bahan
            //------------------------------------

            $cekBahan = $this->db
                ->get_where(
                    'bahan',
                    ['kode_bahan' => $kode_bahan]
                );

            if (!$cekBahan->num_rows()) {
                $this->session->set_flashdata(
                    'failed',
                    'Kode bahan ' . $kode_bahan . ' tidak ditemukan.'
                );

                redirect(base_url('bahan/tambah_transferstok_multi'));
            }

            $bahan = $cekBahan->row();

            //------------------------------------
            // validasi cabang
            //------------------------------------

            $cekCabang = $this->db
                ->get_where(
                    'profil_toko',
                    [
                        'nama_toko' => $tujuan
                    ]
                );

            if (!$cekCabang->num_rows()) {
                $this->session->set_flashdata(
                    'failed',
                    'Cabang ' . $tujuan . ' tidak ditemukan.'
                );

                redirect(base_url('bahan/tambah_transferstok_multi'));
            }

            $cabang = $cekCabang->row();

            //------------------------------------
            // insert temp
            //------------------------------------

            $this->db->insert(
                'transferstok_bahan_bulk_temp',
                [
                    'login_id' => $login_id,
                    'tanggal' => date(
                        'Y-m-d',
                        strtotime(
                            str_replace('/', '-', $tanggal)
                        )
                    ),
                    'no_surat' => $no_surat,
                    'tujuan' => $tujuan,
                    'cabang_id' => $cabang->cabang_id,
                    'bahan_id' => $bahan->id,
                    'kode_bahan' => $bahan->kode_bahan,
                    'nama_bahan' => $bahan->nama_bahan,
                    'qty' => $qty
                ]
            );
        }

        $this->session->set_flashdata(
            'success',
            'Import berhasil.'
        );

        redirect(base_url('bahan/tambah_transferstok_multi/import'));
    }

    public function save_transferstok()
    {
        try {
            $this->form_validation->set_rules("date", "Tanggal", "required");
            $this->form_validation->set_rules("id_cabang", "Cabang Tujuan", "required");
            $this->form_validation->set_rules("no_surat", "No Surat", "required");
            $login_id = $this->session->userdata('ses_id');

            if ($this->form_validation->run() != false) {
                $this->load->database();

                // Initialize arrays
                $data_transferstok_bahan = array();
                $data_kartustok = array();

                $temporaryData = $this->db->get_where("transferstok_bahan_temp", ["login_id" => $login_id])->result_array();
                if (empty($temporaryData)) {
                    echo json_encode(['status' => 'error', 'message' => 'Tidak ada data di tabel sementara']);
                    return;
                }

                $tanggal = $this->input->post("date", true);
                $tgl = date_create($tanggal);
                if (!$tgl) {
                    throw new Exception("Invalid date format");
                }

                $idcabang = $this->input->post("id_cabang", true);
                $no_surat = $this->input->post("no_surat", true);
                $keterangan = $this->input->post("keterangan", true);

                $data_transferstok = array(
                    'no_surat' => $no_surat,
                    'cabangasal_id' => 1,
                    'cabangtujuan_id' => $idcabang,
                    'keterangan' => $keterangan,
                    'created_at' => date('Y-m-d H:i:s'),
                    'date' => date_format($tgl, 'Y-m-d'),
                    'periode' => date_format($tgl, 'Y-m'),
                    'year' => date_format($tgl, 'Y'),
                    'login_id' => $login_id,
                    'status' => 0,
                );

                $this->db->trans_start();

                // Insert main transfer record
                if (!$this->db->insert('transferstok', $data_transferstok)) {
                    throw new Exception("Failed to insert transferstok: " . $this->db->error()['message']);
                }

                $transferstok_id = $this->db->insert_id();

                foreach ($temporaryData as $isi) {
                    $data_transferstok_bahan[] = array(
                        'transferstok_id' => $transferstok_id,
                        'bahan_id' => $isi['bahan_id'],
                        'qty' => $isi['qty']
                    );

                    $this->db->where('bahan_id', $isi['bahan_id']);
                    $this->db->where('cabang_id', 1);
                    $querystok = $this->db->get('bahan_stok');

                    $total_stok = 0;
                    if ($querystok->num_rows() == 0) {
                        $data_stok = [
                            'cabang_id' => 1,
                            'bahan_id' => $isi['bahan_id'],
                            'jumlah_stok' => 0 - $isi['qty']
                        ];

                        if (!$this->db->insert("bahan_stok", $data_stok)) {
                            throw new Exception("Failed to insert bahan_stok: " . $this->db->error()['message']);
                        }
                    } else {
                        $row = $querystok->row();
                        $total_stok = $row->jumlah_stok;

                        $this->db->set('jumlah_stok', 'jumlah_stok-' . $isi['qty'], false);
                        $this->db->where('bahan_id', $isi['bahan_id']);
                        $this->db->where('cabang_id', 1);
                        if (!$this->db->update("bahan_stok")) {
                            throw new Exception("Failed to update bahan_stok: " . $this->db->error()['message']);
                        }
                    }

                    $data_kartustok[] = array(
                        'cabang_id' => 1,
                        'bahan_id' => $isi['bahan_id'],
                        'tipe_transaksi' => "Transfer Out",
                        'transaksi_id' => $transferstok_id,
                        'jumlah_perubahan' => 0 - $isi['qty'],
                        'harga_beli' => 0,
                        'jumlah_harga' => 0,
                        'total_stok' => $total_stok - $isi['qty'],
                        'total_jumlah_harga' => 0,
                        'tanggal' => date_format($tgl, 'Y-m-d'),
                        'created_at' => date('Y-m-d H:i:s'),
                        'periode' => date_format($tgl, 'Y-m'),
                        'tahun' => date_format($tgl, 'Y'),
                        'shift_id' => 0,
                        'login_id' => $login_id
                    );
                }

                if (!empty($data_transferstok_bahan)) {
                    if (!$this->db->insert_batch('transferstok_bahan', $data_transferstok_bahan)) {
                        throw new Exception("Failed to insert batch transferstok_bahan: " . $this->db->error()['message']);
                    }
                }

                if (!empty($data_kartustok)) {
                    if (!$this->db->insert_batch('bahan_kartustok', $data_kartustok)) {
                        throw new Exception("Failed to insert batch bahan_kartustok: " . $this->db->error()['message']);
                    }
                }

                $this->db->delete('transferstok_bahan_temp', ['login_id' => $login_id]);

                if ($this->db->trans_status() === FALSE) {
                    throw new Exception("Transaction failed");
                } else {
                    $this->db->trans_commit();
                    // echo json_encode(['status' => 'success', 'message' => 'Data berhasil disimpan']);
                    echo json_encode(['success' => true, 'message' => 'Data berhasil disimpan']);
                }
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Data gagal disimpan',
                    'errors' => validation_errors()
                ]);
            }
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Error in save_transferstok: ' . $e->getMessage());
            echo json_encode([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function save_transferstok_multi()
    {
        try {

            $login_id = $this->session->userdata('ses_id');

            $temporaryData = $this->db
                ->get_where(
                    "transferstok_bahan_temp",
                    [
                        "login_id" => $login_id
                    ]
                )
                ->result_array();

            $groups = [];

            foreach ($temporaryData as $row) {

                $key =
                    $row['tanggal'] . "|" .
                    $row['no_surat'] . "|" .
                    $row['cabang_id'];

                $groups[$key][] = $row;
            }
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Error in save_transferstok: ' . $e->getMessage());
            echo json_encode([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function update_transferstok()
    {
        try {
            $this->form_validation->set_rules("date", "Tanggal", "required");
            $this->form_validation->set_rules("id_cabang", "Cabang Tujuan", "required");
            $this->form_validation->set_rules("no_surat", "No Surat", "required");
            $login_id = $this->session->userdata('ses_id');
            $transferstok_id = $this->input->post("id", true);
            $tanggal = $this->input->post("date", true);

            $tgl = date_create($tanggal);
            if (!$tgl) {
                throw new Exception("Invalid date format");
            }

            if ($this->form_validation->run() != false) {
                $this->load->database();

                // Initialize arrays
                $data_transferstok_bahan = array();
                $data_kartustok = array();


                // RECURSIVE TRANSFER STOK BAHAN
                // START
                $beforeData = $this->db->get_where("transferstok_bahan", ["transferstok_id" => $transferstok_id])->result_array();
                if (empty($beforeData)) {
                    echo json_encode(['status' => 'error', 'message' => 'Tidak ada data di tabel detail transfer stok.' . $transferstok_id]);
                    return;
                } else {

                    foreach ($beforeData as $isiData) {
                        // $data_transferstok_bahan[] = array(
                        //     'transferstok_id' => $transferstok_id,
                        //     'bahan_id' => $isiData['bahan_id'],
                        //     'qty' => $isiData['qty']
                        // );

                        $this->db->where('bahan_id', $isiData['bahan_id']);
                        $this->db->where('cabang_id', 1);
                        $querystok = $this->db->get('bahan_stok');

                        $total_stok = 0;
                        if ($querystok->num_rows() == 0) {
                            $data_stok = [
                                'cabang_id' => 1,
                                'bahan_id' => $isiData['bahan_id'],
                                'jumlah_stok' => $isiData['qty']
                            ];

                            if (!$this->db->insert("bahan_stok", $data_stok)) {
                                throw new Exception("Failed to insert bahan_stok: " . $this->db->error()['message']);
                            }
                        } else {
                            $row = $querystok->row();
                            $total_stok = $row->jumlah_stok;

                            $this->db->set('jumlah_stok', 'jumlah_stok+' . $isiData['qty'], false);
                            $this->db->where('bahan_id', $isiData['bahan_id']);
                            $this->db->where('cabang_id', 1);
                            if (!$this->db->update("bahan_stok")) {
                                throw new Exception("Failed to update bahan_stok: " . $this->db->error()['message']);
                            }
                        }

                        $data_kartustok[] = array(
                            'cabang_id' => 1,
                            'bahan_id' => $isiData['bahan_id'],
                            'tipe_transaksi' => "Cancelled Transfer Out",
                            'transaksi_id' => $transferstok_id,
                            'jumlah_perubahan' => $isiData['qty'],
                            'harga_beli' => 0,
                            'jumlah_harga' => 0,
                            'total_stok' => $total_stok + $isiData['qty'],
                            'total_jumlah_harga' => 0,
                            'tanggal' => date_format($tgl, 'Y-m-d'),
                            'created_at' => date('Y-m-d H:i:s'),
                            'periode' => date_format($tgl, 'Y-m'),
                            'tahun' => date_format($tgl, 'Y'),
                            'shift_id' => 0,
                            'login_id' => $login_id
                        );
                    }
                    if (!$this->db->delete('transferstok_bahan', ['transferstok_id' => $transferstok_id])) {
                        throw new Exception("Failed to delete transferstok_bahan: " . $this->db->error()['message']);
                    }

                    if (!empty($data_kartustok)) {
                        if (!$this->db->insert_batch('bahan_kartustok', $data_kartustok)) {
                            throw new Exception("Failed to insert batch bahan_kartustok: " . $this->db->error()['message']);
                        }
                    }
                }
                // END

                $temporaryData = $this->db->get_where("transferstok_bahan_temp", ["login_id" => $login_id])->result_array();

                if (empty($temporaryData)) {
                    echo json_encode(['status' => 'error', 'message' => 'Tidak ada data di tabel sementara.']);
                    return;
                }

                $idcabang = $this->input->post("id_cabang", true);
                $no_surat = $this->input->post("no_surat", true);
                $keterangan = $this->input->post("keterangan", true);

                $date_formatted = $tgl->format('Y-m-d'); // DATE format for MySQL
                $periode_formatted = $tgl->format('Y-m'); // VARCHAR format (e.g., "2023-12")
                $year_formatted = $tgl->format('Y');      // VARCHAR format (e.g., "2023")

                $this->db->where('id', $transferstok_id);
                $querystok = $this->db->get('transferstok');
                $this->db->trans_start();

                $this->db->set('no_surat', $no_surat);
                $this->db->set('cabangtujuan_id', $idcabang);
                $this->db->set('keterangan', $keterangan);
                $this->db->set('date', $date_formatted);
                $this->db->set('periode', $periode_formatted);
                $this->db->set('year', $year_formatted);
                $this->db->set('login_id', $login_id);

                $this->db->where('id', $transferstok_id);


                // update main transfer record
                if (!$this->db->update("transferstok")) {
                    throw new Exception("Failed to update transferstok: " . $transferstok_id . " pesan :" . $this->db->error()['message'] . " query: " . $this->db->last_query());
                }

                // $transferstok_id = $this->db->insert_id();
                $data_transferstok_bahan = array();
                $data_kartustok = array();

                foreach ($temporaryData as $isi) {
                    $data_transferstok_bahan[] = array(
                        'transferstok_id' => $transferstok_id,
                        'bahan_id' => $isi['bahan_id'],
                        'qty' => $isi['qty']
                    );

                    $this->db->where('bahan_id', $isi['bahan_id']);
                    $this->db->where('cabang_id', 1);
                    $querystok = $this->db->get('bahan_stok');

                    $total_stok = 0;
                    if ($querystok->num_rows() == 0) {
                        $data_stok = [
                            'cabang_id' => 1,
                            'bahan_id' => $isi['bahan_id'],
                            'jumlah_stok' => 0 - $isi['qty']
                        ];

                        if (!$this->db->insert("bahan_stok", $data_stok)) {
                            throw new Exception("Failed to insert bahan_stok: " . $this->db->error()['message']);
                        }
                    } else {
                        $row = $querystok->row();
                        $total_stok = $row->jumlah_stok;

                        $this->db->set('jumlah_stok', 'jumlah_stok-' . $isi['qty'], false);
                        $this->db->where('bahan_id', $isi['bahan_id']);
                        $this->db->where('cabang_id', 1);
                        if (!$this->db->update("bahan_stok")) {
                            throw new Exception("Failed to update bahan_stok: " . $this->db->error()['message']);
                        }
                    }

                    $data_kartustok[] = array(
                        'cabang_id' => 1,
                        'bahan_id' => $isi['bahan_id'],
                        'tipe_transaksi' => "Transfer Out",
                        'transaksi_id' => $transferstok_id,
                        'jumlah_perubahan' => 0 - $isi['qty'],
                        'harga_beli' => 0,
                        'jumlah_harga' => 0,
                        'total_stok' => $total_stok - $isi['qty'],
                        'total_jumlah_harga' => 0,
                        'tanggal' => date_format($tgl, 'Y-m-d'),
                        'created_at' => date('Y-m-d H:i:s'),
                        'periode' => date_format($tgl, 'Y-m'),
                        'tahun' => date_format($tgl, 'Y'),
                        'shift_id' => 0,
                        'login_id' => $login_id
                    );
                }

                if (!empty($data_transferstok_bahan)) {
                    if (!$this->db->insert_batch('transferstok_bahan', $data_transferstok_bahan)) {
                        throw new Exception("Failed to insert batch transferstok_bahan: " . $this->db->error()['message']);
                    }
                }

                if (!empty($data_kartustok)) {
                    if (!$this->db->insert_batch('bahan_kartustok', $data_kartustok)) {
                        throw new Exception("Failed to insert batch bahan_kartustok: " . $this->db->error()['message']);
                    }
                }

                $this->db->delete('transferstok_bahan_temp', ['login_id' => $login_id]);

                if ($this->db->trans_status() === FALSE) {
                    throw new Exception("Transaction failed");
                } else {
                    $this->db->trans_commit();
                    // echo json_encode(['status' => 'success', 'message' => 'Data berhasil disimpan']);
                    echo json_encode(['success' => true, 'message' => 'Data berhasil disimpan']);
                }
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Data gagal disimpan',
                    'errors' => validation_errors()
                ]);
            }
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Error in save_transferstok: ' . $e->getMessage());
            echo json_encode([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function save_terimastok()
    {
        $shift_now = $this->session->userdata('ses_shift');
        $cabang_id =  $this->session->userdata('ses_cabang_id');
        $suffix = $this->session->userdata('ses_suffix');

        // $this->form_validation->set_rules("date", "Tanggal", "required");
        $this->form_validation->set_rules("id", "ID", "required");
        // $this->form_validation->set_rules("no_surat", "No Surat", "required");
        $login_id =  $this->session->userdata('ses_id');


        if ($this->form_validation->run() != false) {
            $id = $this->input->post("id", true);

            $this->load->database();

            $this->db->join('transferstok t', 't.id = tb.transferstok_id');
            $this->db->join('bahan b', 'b.id = tb.bahan_id');
            $temporaryData = $this->db->get_where('transferstok_bahan tb', ['tb.transferstok_id' => $id, 't.cabangtujuan_id' => $cabang_id])->result_array();

            if (empty($temporaryData)) {
                echo json_encode(['status' => 'error', 'message' => 'Tidak ada data di tabel sementara']);
                exit;
            }
            $temp_transferstok = $this->db->get_where('transferstok', ['id' => $id])->row_array();
            // echo json_encode(temp_transferstok);
            // exit;
            $tgl = date_create($temp_transferstok['date']);

            $data_transferstok = array(
                // 'diterima_tgl' => date('Y-m-d H:i:s'),
                'diterima_tgl' => date_format($tgl, 'Y-m-d H:i:s'),
                'status' => 1,
            );

            $this->db->trans_start();
            $this->db->where("id", $id);
            $this->db->update("transferstok", $data_transferstok);

            $transferstok_id = $id;
            // $tgl = date_create(date('Y-m-d H:i:s'));
            foreach ($temporaryData as $isi) {

                $this->db->where('bahan_id', $isi['bahan_id']);
                $this->db->where('cabang_id', $cabang_id);
                $querystok = $this->db->get('bahan_stok');
                $total_stok = 0;
                if ($querystok->num_rows() == 0) { // jika belum ada
                    $data_stok = [
                        'cabang_id'   => $cabang_id,
                        'bahan_id'     => $isi['bahan_id'],
                        'jumlah_stok'     =>  $isi['qty'] * $isi['konversi']
                    ];

                    $this->db->insert("bahan_stok", $data_stok);
                } else {
                    // $total_stok = $querystok['jumlah_stok'];
                    $row = $querystok->row();
                    $total_stok = $row->jumlah_stok;

                    $data_stok = [
                        'cabang_id'   => $cabang_id,
                        'bahan_id'     => $isi['bahan_id']
                        // 'jumlah_stok'     => 'jumlah_stok' - $isi['qty']
                    ];
                    $this->db->set('jumlah_stok', 'jumlah_stok+' . ($isi['qty'] * $isi['konversi']), false);
                    $this->db->where('bahan_id', $isi['bahan_id']);
                    $this->db->where('cabang_id', $cabang_id);
                    $this->db->update("bahan_stok", $data_stok);
                }; // jika sudah ada

                $data_kartustok[] = array(
                    'cabang_id'   => $cabang_id,
                    'bahan_id'     => $isi['bahan_id'],
                    'tipe_transaksi'     => "Transfer In",
                    'transaksi_id'     => $transferstok_id,
                    'jumlah_perubahan'     => $isi['qty'] * $isi['konversi'],
                    'harga_beli'   => 0,
                    'jumlah_harga'   => 0,
                    'total_stok'   => $total_stok + ($isi['qty'] * $isi['konversi']),
                    'total_jumlah_harga'   => 0,
                    'tanggal' => date_format($tgl, 'Y-m-d'),
                    'created_at'    => date('Y-m-d H:i:s'),
                    'periode' => date_format($tgl, 'Y-m'),
                    'tahun' => date_format($tgl, 'Y'),
                    'shift_id' => $shift_now,
                    'login_id' => $login_id
                );
            }

            $this->db->insert_batch('bahan_kartustok' . $suffix, $data_kartustok);

            $this->db->trans_complete();

            // echo json_encode(['status' => 'success', 'message' => 'Data berhasil disimpan']);
            echo json_encode(['success' => true, 'message' => 'Data berhasil disimpan']);
            // $this->session->set_flashdata("success", " Berhasil Simpan Data ! ");
            // redirect(base_url("bahan/transferstok_terima"));
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Data gagal disimpan',
                'errors' => validation_errors()
            ]);
            // echo json_encode(['status' => 'error', 'message' => 'Data gagal disimpan']);
            // $this->session->set_flashdata("failed", " Gagal Simpan Data !");
            // redirect(base_url("bahan/tambah_transferstok"));
        }
    }
}
