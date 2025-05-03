<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Keuangan extends CI_Controller
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
        if ($this->session->userdata('masuk_sistem') != true) {
            $url = base_url('login');
            redirect($url);
        }
    }

    public function index()
    {
        if (!empty($this->input->get('id'))) {
            $id     = (int)$this->input->get('id');
            $edit   = $this->db->query('SELECT * FROM keuangan_ledger WHERE id = ?', [$id])->row();
        } else {
            $edit = 0;
        }

        $this->data = [
            'title_web' => 'Ledger',
            'keuangan_ledger' => $this->db->query('SELECT * FROM keuangan_ledger')->result(),
            'edit' => $edit
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/keuangan/ledger/index', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function store()
    {
        $this->form_validation->set_rules("no_ledger", "No ledger", "required");
        $this->form_validation->set_rules("jenis", "Jenis", "required");
        if ($this->form_validation->run() != false) {
            $data = [
                'no_ledger' => htmlspecialchars($this->input->post("no_ledger", true), ENT_QUOTES),
                'keterangan' => htmlspecialchars($this->input->post("keterangan", true), ENT_QUOTES),
                'jenis' => htmlspecialchars($this->input->post("jenis", true), ENT_QUOTES),
                'created_at' => date('Y-m-d H:i:s'),
            ];
            $this->db->insert("keuangan_ledger", $data);
            $this->session->set_flashdata("success", " Berhasil Insert Data ! ");
            redirect(base_url("keuangan"));
        } else {
            $this->session->set_flashdata("failed", " Gagal Insert Data ! " . validation_errors());
            redirect(base_url("keuangan"));
        }
    }

    public function update()
    {
        $id =  (int)$this->input->post("id"); // parameter yang mau di update
        $this->form_validation->set_rules("no_ledger", "No ledger", "required");
        $this->form_validation->set_rules("jenis", "Jenis", "required");
        if ($this->form_validation->run() != false) {
            $data = [
                'no_ledger' => htmlspecialchars($this->input->post("no_ledger", true), ENT_QUOTES),
                'keterangan' => htmlspecialchars($this->input->post("keterangan", true), ENT_QUOTES),
                'jenis' => htmlspecialchars($this->input->post("jenis", true), ENT_QUOTES),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $this->db->where("id", $id); // ubah id dan postnya
            $this->db->update("keuangan_ledger", $data);
            $this->session->set_flashdata("success", " Berhasil Update Data ! ");
            redirect(base_url("keuangan?id=" . $id));
        } else {
            $this->session->set_flashdata("failed", " Gagal Update Data ! " . validation_errors());
            redirect(base_url("keuangan?id=" . $id));
        }
    }

    public function delete()
    {
        $id = (int)$this->input->get("id");
        $cek = $this->db->get_where("keuangan_ledger", ["id" => $id]); // tulis id yang dituju
        if ($cek->num_rows() > 0) {
            $this->db->where("id", $id); // tulis id yang dituju
            $this->db->delete("keuangan_ledger");
            $this->session->set_flashdata("success", " Berhasil Delete Data ! ");
            redirect(base_url("keuangan"));
        } else {
            $this->session->set_flashdata("failed", " Gagal Delete Data ! " . validation_errors());
            redirect(base_url("keuangan"));
        }
    }

    // keuangan lainnya

    public function lain()
    {
        error_reporting(0);
        $sql = "SELECT SUM(jumlah_masuk) AS masuk, SUM(jumlah_keluar) AS keluar FROM keuangan_lainnya";
        if (!empty($this->input->get('a') && $this->input->get('b'))) {
            $a          = $this->input->get('a');
            $b          = $this->input->get('b');
            $dtb1       = $this->db->query($sql . ' WHERE no_ledger LIKE "%' . $this->input->get('no_ledger') . '%" AND date BETWEEN "' . $a . '" AND "' . $b . '"');
            $url        = base_url('keuangan/data?no_ledger=' .
                htmlentities($this->input->get('no_ledger')) . '&a=' .
                htmlentities($this->input->get('a')) . '&b=' .
                htmlentities($this->input->get('b')));
            $url_pdf    = base_url('keuangan/excel?no_ledger=' .
                htmlentities($this->input->get('no_ledger')) . '&a=' .
                htmlentities($this->input->get('a')) . '&b=' .
                htmlentities($this->input->get('b')));
            $data_ledger = $this->db->query('SELECT * FROM keuangan_ledger WHERE no_ledger = ?', [$this->input->get('no_ledger')])->row();
            $nm_ledger   = $data_ledger->keterangan;
        } else {
            $dtb1 = $this->db->query($sql . ' WHERE periode = ?', [date('Y-m')]);
            $url  = base_url('keuangan/data');
            $url_pdf = base_url('keuangan/excel');
            $nm_ledger   = '';
        }

        $ledger = $this->db->query('SELECT * FROM keuangan_ledger')->result();

        $this->data = [
            'title_web' => 'Keuangan Lainnya',
            'sidebar'   => 'keuangan',
            'tot'       => $dtb1->row(),
            'ledger'    => $ledger,
            'nm_ledger' => $nm_ledger,
            'url'       => $url,
            'url_pdf'   => $url_pdf,
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/keuangan/lain/index', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function edit()
    {
        $this->data = [
            'title_web' => 'Keuangan Lainnya',
            'sidebar'   => 'keuangan',
            'ledger' => $this->db->query('SELECT * FROM keuangan_ledger')->result(),
            'edit'      => $this->db->query('SELECT * FROM keuangan_lainnya WHERE id = ?', [(int)$this->uri->segment('3')])->row(),
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/keuangan/lain/edit', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function data()
    {
        if ($this->input->method(true) == 'POST') :
            $query = "SELECT keuangan_ledger.keterangan as ket, keuangan_lainnya.* 
                        FROM keuangan_lainnya 
                        LEFT JOIN keuangan_ledger 
                        ON keuangan_lainnya.no_ledger = keuangan_ledger.no_ledger";
            $search = array('keuangan_lainnya.no_ledger', 'keuangan_ledger.keterangan', 'keuangan_lainnya.nama_urusan', 'keuangan_lainnya.jenis');
            $where  = null;
            if (!empty($this->input->get('a') && $this->input->get('b'))) {
                $a = $this->input->get('a');
                $b = $this->input->get('b');
                $iswhere = ' keuangan_lainnya.no_ledger LIKE "%' . $this->input->get('no_ledger') . '%" AND keuangan_lainnya.date BETWEEN "' . $a . '" AND "' . $b . '"';
            } else {
                $iswhere = ' keuangan_lainnya.periode = "' . date('Y-m') . '"';
            }
            header('Content-Type: application/json');
            echo $this->M_Datatables->get_tables_query($query, $search, $where, $iswhere);
        endif;
    }

    public function excel()
    {
        $query = "SELECT keuangan_ledger.keterangan as ket, keuangan_lainnya.* 
                        FROM keuangan_lainnya 
                        LEFT JOIN keuangan_ledger 
                        ON keuangan_lainnya.no_ledger = keuangan_ledger.no_ledger";
        if (!empty($this->input->get('a') && $this->input->get('b'))) {
            $a = $this->input->get('a');
            $b = $this->input->get('b');
            $iswhere = ' WHERE keuangan_lainnya.no_ledger LIKE "%' . $this->input->get('no_ledger') . '%" AND keuangan_lainnya.date BETWEEN "' . $a . '" AND "' . $b . '"';
            $periode = 'PERIODE ' . time_explode_date($this->input->get('a'), 'id') . ' s.d. ' . time_explode_date($this->input->get('b'), 'id');
        } else {
            $iswhere = ' WHERE keuangan_lainnya.periode = "' . date('Y-m') . '"';
            $periode = 'PERIODE ' . bln('id') . ' ' . date('Y');
        }

        $transaksi = $this->db->query($query . $iswhere)->result();

        $this->data = [
            'transaksi' => $transaksi,
            'periode' => $periode,
        ];

        $this->load->view('admin/keuangan/lain/excel', $this->data);
    }

    public function store_lain()
    {
        $this->form_validation->set_rules("no_ledger", "No ledger", "required");
        $this->form_validation->set_rules("nama_urusan", "Nama urusan", "required");
        $this->form_validation->set_rules("jenis", "Jenis", "required");
        if ($this->form_validation->run() != false) {
            if ($this->input->post('jumlah_masuk') != 0) {
                $jumlah_masuk = preg_replace('/[^A-Za-z0-9_]/', '', $this->input->post('jumlah_masuk'));
            } else {
                $jumlah_masuk = 0;
            }
            if ($this->input->post('jumlah_keluar') != 0) {
                $jumlah_keluar = preg_replace('/[^A-Za-z0-9_]/', '', $this->input->post('jumlah_keluar'));
            } else {
                $jumlah_keluar = 0;
            }

            $data = [
                'no_ledger' => htmlspecialchars($this->input->post("no_ledger", true), ENT_QUOTES),
                'nama_urusan' => htmlspecialchars($this->input->post("nama_urusan", true), ENT_QUOTES),
                'jenis' => htmlspecialchars($this->input->post("jenis", true), ENT_QUOTES),
                'jumlah_masuk' => $jumlah_masuk,
                'jumlah_keluar' => $jumlah_keluar,
                'created_at' => date('Y-m-d H:i:s'),
                'date' => date('Y-m-d'),
                'periode' => date('Y-m'),
                'year' => date('Y'),
                'keterangan' => htmlspecialchars($this->input->post("keterangan", true), ENT_QUOTES),
                'user_id' => $this->session->userdata('ses_id'),
            ];

            $this->db->insert("keuangan_lainnya", $data);
            $this->session->set_flashdata("success", " Berhasil Insert Data ! ");
            redirect(base_url("keuangan/lain"));
        } else {
            $this->session->set_flashdata("failed", " Gagal Insert Data ! " . validation_errors());
            redirect(base_url("keuangan/lain"));
        }
    }

    public function update_lain()
    {
        $this->form_validation->set_rules("no_ledger", "No ledger", "required");
        $this->form_validation->set_rules("nama_urusan", "Nama urusan", "required");
        $this->form_validation->set_rules("jenis", "Jenis", "required");
        $this->form_validation->set_rules("id", "id", "required");
        if ($this->form_validation->run() != false) {
            $id = (int)$this->input->post('id');
            if ($this->input->post('jumlah_masuk') != 0) {
                $jumlah_masuk = preg_replace('/[^A-Za-z0-9_]/', '', $this->input->post('jumlah_masuk'));
            } else {
                $jumlah_masuk = 0;
            }
            if ($this->input->post('jumlah_keluar') != 0) {
                $jumlah_keluar = preg_replace('/[^A-Za-z0-9_]/', '', $this->input->post('jumlah_keluar'));
            } else {
                $jumlah_keluar = 0;
            }

            $data = [
                'no_ledger' => htmlspecialchars($this->input->post("no_ledger", true), ENT_QUOTES),
                'nama_urusan' => htmlspecialchars($this->input->post("nama_urusan", true), ENT_QUOTES),
                'jenis' => htmlspecialchars($this->input->post("jenis", true), ENT_QUOTES),
                'jumlah_masuk' => $jumlah_masuk,
                'jumlah_keluar' => $jumlah_keluar,
                'date' => date('Y-m-d'),
                'periode' => date('Y-m'),
                'year' => date('Y'),
                'keterangan' => htmlspecialchars($this->input->post("keterangan", true), ENT_QUOTES),
                'user_id' => $this->session->userdata('ses_id'),
            ];

            $this->db->where('id', $id);
            $this->db->update("keuangan_lainnya", $data);
            $this->session->set_flashdata("success", " Berhasil Update Data ! ");
            redirect(base_url("keuangan/edit/" . $id));
        } else {
            $id = (int)$this->input->post('id');
            $this->session->set_flashdata("failed", " Gagal Update Data ! " . validation_errors());
            redirect(base_url("keuangan/edit/" . $id));
        }
    }

    public function delete_lain()
    {
        $id = (int)$this->input->get("id");
        $cek = $this->db->get_where("keuangan_lainnya", ["id" => $id]); // tulis id yang dituju
        if ($cek->num_rows() > 0) {
            $this->db->where("id", $id); // tulis id yang dituju
            $this->db->delete("keuangan_lainnya");
            $this->session->set_flashdata("success", " Berhasil Delete Data ! ");
            redirect(base_url("keuangan/lain"));
        } else {
            $this->session->set_flashdata("failed", " Gagal Delete Data ! " . validation_errors());
            redirect(base_url("keuangan/lain"));
        }
    }

    public function data_pengeluaran()
    {
        $cabang_id = $this->session->userdata('ses_cabang_id');
        $kasir_id = $this->session->userdata('ses_id');

        if ($this->input->method(true) == 'POST') :
            $query = "SELECT * 
                        FROM transaksi_keluar";
            $search = array('no_bon', 'kasir_id', 'keterangan', 'date');
            // $where  = null;
            $where = array('transaksi_keluar.cabang_id' => $cabang_id, 'transaksi_keluar.kasir_id' => $kasir_id);
            if (!empty($this->input->get('a') && $this->input->get('b'))) {
                $a = $this->input->get('a');
                $b = $this->input->get('b');
                $iswhere = ' transaksi_keluar.date BETWEEN "' . $a . '" AND "' . $b . '"';
            } else {
                $iswhere = ' transaksi_keluar.date = "' . date('Y-m-d') . '"';
            }
            header('Content-Type: application/json');
            echo $this->M_Datatables->get_tables_query($query, $search, $where, $iswhere);
        // echo $this->M_Datatables->get_tables_query($query, $search, $where, $iswhere);
        endif;
    }

    public function pengeluaran()
    {
        error_reporting(0);
        $cabang_id = $this->session->userdata('ses_cabang_id');
        $kasir_id = $this->session->userdata('ses_id');

        $sql = "SELECT SUM(jumlah) AS jmltotal FROM transaksi_keluar ";
        if (!empty($this->input->get('a') && $this->input->get('b'))) {
            $a          = $this->input->get('a');
            $b          = $this->input->get('b');
            $dtb1       = $this->db->query($sql . ' WHERE kasir_id = ' . $kasir_id . ' AND cabang_id = ' . $cabang_id . ' AND date BETWEEN "' . $a . '" AND "' . $b . '"');
            $url        = base_url('keuangan/data_pengeluaran?a=' .
                htmlentities($this->input->get('a')) . '&b=' .
                htmlentities($this->input->get('b')));
            // $url_pdf    = base_url('keuangan/excel?no_ledger=' .
            //     htmlentities($this->input->get('no_ledger')) . '&a=' .
            //     htmlentities($this->input->get('a')) . '&b=' .
            //     htmlentities($this->input->get('b')));
            // $data_ledger = $this->db->query('SELECT * FROM keuangan_ledger WHERE no_ledger = ?', [$this->input->get('no_ledger')])->row();
            // $nm_ledger   = $data_ledger->keterangan;
        } else {
            $dtb1 = $this->db->query($sql . ' WHERE kasir_id = ' . $kasir_id . ' AND cabang_id = ' . $cabang_id . ' AND date = ?', [date('Y-m-d')]);
            $url  = base_url('keuangan/data_pengeluaran');
            //     $url_pdf = base_url('keuangan/excel');
            //     $nm_ledger   = '';
        }

        // $ledger = $this->db->query('SELECT * FROM transaksi_keluar')->result();

        $this->data = [
            'title_web' => 'Pengeluaran',
            'sidebar'   => 'pengeluaran',
            'tot'       => $dtb1->row(),
            // 'ledger'    => $ledger,
            // 'nm_ledger' => $nm_ledger,
            'url'       => $url,
            // 'url_pdf'   => $url_pdf,
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/keuangan/pengeluaran/index', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function store_pengeluaran()
    {
        // $shift_nilai = $this->db->query("SELECT MAX(open) as 'nilai_max', MIN(close) as 'nilai_min' FROM shift")->row();
        // $nilai_max = $shift_nilai->nilai_max;
        // $nilai_min = $shift_nilai->nilai_min;
        // // $shift = $this->db->query("SELECT * FROM shift WHERE '" . date('H:i') . "' BETWEEN open AND close ")->row();
        // $shift = $this->db->query("SELECT * FROM shift WHERE ('" . date('H:i') . "' BETWEEN open AND close) OR 
        // ((open>='" . $nilai_max . "' AND '" . date('H:i') . "' BETWEEN '" . $nilai_max . "' AND '24:00') OR 
        // (close<='" . $nilai_min . "' AND '" . date('H:i') . "' BETWEEN '00:00' AND '" . $nilai_min . "')) ")->row();

        $shift_now = $this->session->userdata('ses_shift');
        $tanggal = date('Y-m-d H:i:s');
        $waktu = date('H');
        $tgl = date_create($tanggal);
        if (($shift_now == 3) && ($waktu <= 8)) {
            date_sub($tgl, date_interval_create_from_date_string("1 days"));
        }

        // $this->form_validation->set_rules("no_bon", "No bon", "required");
        $this->form_validation->set_rules("keterangan", "Keterangan", "required");
        $this->form_validation->set_rules("jumlah", "Jumlah", "required");
        $this->form_validation->set_rules("kategori", "Kategori", "required");
        if ($this->form_validation->run() != false) {
            if ($this->input->post('jumlah') != 0) {
                $jumlah = preg_replace('/[^A-Za-z0-9_]/', '', $this->input->post('jumlah'));
            } else {
                $jumlah = 0;
            }

            $data = [
                'no_bon' => htmlspecialchars($this->input->post("no_bon", true), ENT_QUOTES),
                'jumlah' => $jumlah,
                'created_at' => date('Y-m-d H:i:s'),
                // 'date' => date('Y-m-d'),
                // 'periode' => date('Y-m'),
                // 'year' => date('Y'),
                'date' => date_format($tgl, 'Y-m-d'),
                'periode' => date_format($tgl, 'Y-m'),
                'year' => date_format($tgl, 'Y'),
                'keterangan' => htmlspecialchars($this->input->post("keterangan", true), ENT_QUOTES),
                'kasir_id' => $this->session->userdata('ses_id'),
                'cabang_id' => $this->session->userdata('ses_cabang_id'),
                // 'shift_id' => $shift->id,
                'shift_id' => $shift_now,
                'closing_id' => 0,
                'kategori_keluar' => htmlspecialchars($this->input->post("kategori", true), ENT_QUOTES)
            ];

            $this->db->insert("transaksi_keluar", $data);
            $this->session->set_flashdata("success", " Berhasil Insert Data ! ");
            redirect(base_url("keuangan/pengeluaran"));
        } else {
            $this->session->set_flashdata("failed", " Gagal Insert Data ! " . validation_errors());
            redirect(base_url("keuangan/pengeluaran"));
        }
    }

    public function edit_pengeluaran()
    {
        $this->data = [
            'title_web' => 'Pengeluaran',
            'sidebar'   => 'pengeluaran',
            'edit'      => $this->db->query('SELECT * FROM transaksi_keluar WHERE id = ?', [(int)$this->uri->segment('3')])->row(),
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/keuangan/pengeluaran/edit', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function delete_pengeluaran()
    {
        $id = (int)$this->input->get("id");
        $cek = $this->db->get_where("transaksi_keluar", ["id" => $id]); // tulis id yang dituju
        if ($cek->num_rows() > 0) {
            $this->db->where("id", $id); // tulis id yang dituju
            $this->db->delete("transaksi_keluar");
            $this->session->set_flashdata("success", " Berhasil Delete Data ! ");
            redirect(base_url("keuangan/pengeluaran"));
        } else {
            $this->session->set_flashdata("failed", " Gagal Delete Data ! " . validation_errors());
            redirect(base_url("keuangan/pengeluaran"));
        }
    }

    public function update_pengeluaran()
    {
        $this->form_validation->set_rules("no_bon", "No bon", "required");
        $this->form_validation->set_rules("keterangan", "Keterangan", "required");
        $this->form_validation->set_rules("jumlah", "Jumlah", "required");
        if ($this->form_validation->run() != false) {
            $id = (int)$this->input->post('id');
            if ($this->input->post('jumlah') != 0) {
                $jumlah = preg_replace('/[^A-Za-z0-9_]/', '', $this->input->post('jumlah'));
            } else {
                $jumlah = 0;
            }

            $data = [
                'no_bon' => htmlspecialchars($this->input->post("no_bon", true), ENT_QUOTES),
                'jumlah' => $jumlah,

                'keterangan' => htmlspecialchars($this->input->post("keterangan", true), ENT_QUOTES),

                'kategori_keluar' => htmlspecialchars($this->input->post("kategori", true), ENT_QUOTES)
            ];

            $this->db->where('id', $id);
            $this->db->update("transaksi_keluar", $data);
            $this->session->set_flashdata("success", " Berhasil Update Data ! ");
            redirect(base_url("keuangan/pengeluaran"));
        } else {
            $id = (int)$this->input->post('id');
            $this->session->set_flashdata("failed", " Gagal Update Data ! " . validation_errors());
            redirect(base_url("keuangan/pengeluaran"));
        }
    }

    public function ubah_lappengeluaran()
    {

        $id =  (int)$this->input->post("id"); // parameter yang mau di update

        $this->form_validation->set_rules("id", "Id", "required");
        $this->form_validation->set_rules("kategori", "Kategori", "required");
        $this->form_validation->set_rules("jumlah", "Jumlah", "required");
        $this->form_validation->set_rules("keterangan", "Keterangan", "required");

        if ($this->form_validation->run() != false) {

            $data = [
                'kategori_keluar' => $this->input->post("kategori"),
                'keterangan' => $this->input->post("kategori"),
                'no_bon' => $this->input->post("no_bon"),
                'jumlah' => (int)$this->input->post("jumlah")
            ];

            $this->db->where("id", $id); // ubah id dan postnya
            $this->db->update("transaksi_keluar", $data);
            $this->session->set_flashdata("success", " Berhasil Ubah Data Pengeluaran ! ");
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        } else {
            $this->session->set_flashdata("failed", " Gagal Ubah Data Pengeluaran ! " . validation_errors());
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        }
    }
}
