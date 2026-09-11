<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class Stok extends CI_Controller
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
            'title_web'  => 'Daftar Stok',
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/stok/index', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function stokadjustment()
    {
        $this->data = [
            'title_web'  => 'Adjustment Stok Periode',
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/stok/stokadjustment', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function data_stokadjustment()
    {
        $level =  $this->session->userdata('ses_level');
        $where = '';

        if ($this->input->method(true) == 'POST') :
            $query = "SELECT 
                cabang.kode_cabang AS kode_cabang
                stokadjustment.*
            FROM stokadjustment
            LEFT JOIN cabang  
                ON stokadjustment.cabang_id = cabang.id ";

            $search = array('cabang.kode_cabang', 'stokadjustment.periode');
            if ((int)$this->input->get('id')) {
                // $where  = array('cabang_id' => $cabang_id, 'id_kategori' => (int)$this->input->get('id'));
            } else {
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

    // Mengambil data tabel sementara
    public function get_bahan_temp()
    {
        $this->load->database();

        $search = $this->input->get('search') ?? ''; // Query pencarian
        $page = $this->input->get('page') ?? 1; // Halaman saat ini
        $limit = $this->input->get('limit') ?? 5; // Jumlah data per halaman
        $offset = ($page - 1) * $limit; // Hitung offset

        $periode_ym = $this->input->get('periode') ?? date('Y-m');
        // $periode_ym = $this->input->get('periode');
        $tanggal = $this->input->get('tanggal') ?? date('Y-m-d');

        // Variabel Suffix dan Cabang (Sesuaikan dengan kebutuhan nyata)
        $idcabang = $this->input->get('idcabang') ?? '1';
        $cabang = $this->db->query("select * from cabang where id = " . $idcabang)->row();
        $kode_cabang = $cabang->kode_cabang;
        $arr_kode_cabang = array("SN1", "SN2", "SN7", "PU");
        if (in_array($kode_cabang, $arr_kode_cabang)) {
            $suffix = '';
        } else {
            $suffix = '_' . $kode_cabang;
        }

        // $suffix = '_SN4';
        // $idcabang = 1;
        // ----------------------

        // Subquery untuk menghitung Stok Akhir (SUM jumlah_perubahan)
        // Akan digunakan sebagai kolom dalam SELECT utama
        // $stok_akhir_subquery = "(
        // SELECT 
        //     SUM(bk.jumlah_perubahan) 
        // FROM bahan_kartustok" . $suffix . " AS bk 
        // WHERE 
        //     bk.bahan_id = b.id AND 
        //     (bk.tanggal BETWEEN '" . $periode_ym . "-01' AND '" . $tanggal . "' )" .
        //     " AND bk.cabang_id = " . $idcabang . ")";
        $stok_akhir_subquery = "(
        SELECT 
            bk.total_stok
        FROM bahan_kartustok" . $suffix . " AS bk 
        WHERE 
            bk.bahan_id = b.id AND 
            bk.tanggal <= '" . $tanggal . "'" .
            " AND bk.cabang_id = " . $idcabang .
            " ORDER BY bk.tanggal DESC, bk.id DESC 
        LIMIT 1)";


        // --- 1. Hitung Total Rows (Pencarian) ---
        // Gunakan CI Query Builder untuk kondisi WHERE pencarian
        $this->db->select('b.id');
        $this->db->from('bahan b');

        if (!empty($search)) {
            // Tambahkan kondisi pencarian ke query total rows
            $this->db->like('b.nama_bahan', $search);
        }

        $totalRows = $this->db->count_all_results('', FALSE); // FALSE agar query builder tidak direset


        // --- 2. Ambil Data dengan Pagination dan Stok Akhir ---
        $this->db->select("
        b.kode_bahan, 
        b.nama_bahan, 
        COALESCE($stok_akhir_subquery, 0) AS stok_akhir
    ");
        // $this->db->from('bahan b'); // Mulai dari tabel bahan

        // Apply pencarian lagi untuk data utama
        if (!empty($search)) {
            $this->db->like('b.nama_bahan', $search);
        }

        // Terapkan pagination
        $this->db->limit($limit, $offset);

        // Eksekusi Query
        $query = $this->db->get();
        $data = $query->result();

        $lastquery = $this->db->last_query();
        // Reset CI Query Builder jika menggunakan FALSE sebelumnya
        $this->db->reset_query();

        echo json_encode([
            'data' => $data,
            'totalRows' => $totalRows,
            'perPage' => $limit,
            'currentPage' => $page,
            'periode' => $periode_ym, // Optional: untuk debugging
            'idcabang' => $idcabang, // Optional: untuk debugging
            'lastquery' => $lastquery

        ]);
    }

    public function get_stokadjustment_temp()
    {
        $this->load->database();
        $query = $this->db->get('stokadjustment_bahan_temp');
        echo json_encode($query->result());
    }

    public function tambah_stokadjustment()
    {
        $imp = $this->uri->segment('3');
        if ($imp <> 'import') {
            $login_id =  $this->session->userdata('ses_id');
            $this->db->delete('stokadjustment_bahan_temp', ['login_id' => $login_id]);
        }
        $temp_tanggal =  $this->session->userdata('ses_temp_tanggal');
        $temp_cabang =  $this->session->userdata('ses_temp_cabang');
        $cek = $this->db->get_where("profil_toko", ["nama_toko" => $temp_cabang]); // tulis id yang dituju
        if ($cek->num_rows() > 0) {
            $cabang = $cek->row();
            $temp_cabang = $cabang->cabang_id;
        }
        $this->session->set_userdata('ses_temp_tanggal', '');
        $this->session->set_userdata('ses_temp_cabang', '');
        $this->data = [
            'title_web' => 'Tambah Adjustment Stok',
            // 'kode'  	=> 'P000'.$kode_cus,
            // 'kode'      => 'BN' . sprintf('%06d', intval($kode_num)),
            'temptanggal' => $temp_tanggal,
            'tempcabang' => $temp_cabang,
            'kode'      => '',
            'cab'       => $this->db->query("SELECT profil_toko.nama_toko,cabang.* FROM cabang LEFT JOIN profil_toko ON profil_toko.cabang_id = cabang.id WHERE cabang.id > 1 ORDER BY length(kode_cabang),kode_cabang ASC")->result(),
            'bahan'       => $this->db->get('bahan')->result()
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/stok/tambah_stokadjustment', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function save_stokadjustment_temp()
    {

        $login_id =  $this->session->userdata('ses_id');
        $bahan_id = $this->input->post("bahan_id", true);
        $stoksistem = $this->input->post("stoksistem", true);
        $stok = $this->input->post("stok", true);
        $kode_bahan = $this->input->post("kode", true);
        $nama_bahan = $this->input->post("nama", true);

        $data = [
            'login_id'   => htmlspecialchars($login_id, ENT_QUOTES),
            'bahan_id'     => htmlspecialchars($bahan_id, ENT_QUOTES),
            'stoksistem' => $stoksistem,
            'stok' => $stok,
            'kode_bahan'     => htmlspecialchars($kode_bahan, ENT_QUOTES),
            'nama_bahan'     => htmlspecialchars($nama_bahan, ENT_QUOTES),
        ];
        $this->db->insert("stokadjustment_bahan_temp", $data);

        $this->session->set_flashdata("success", " Berhasil menambahkan data bahan !");
        echo json_encode(['status' => 'success', 'message' => 'Barang berhasil ditambahkan']);
    }

    public function delete_stokadjustment_temp()
    {
        $this->load->database();
        $id = $this->input->post("id", true);

        $this->db->delete('stokadjustment_bahan_temp', ['id' => $id]);
        echo json_encode(['status' => 'success', 'message' => 'Bahan berhasil dihapus']);
    }

    public function delete_stokadjustment_temp_all()
    {
        $this->load->database();
        // $id = $this->input->post("id", true);
        $login_id =  $this->session->userdata('ses_id');


        $this->db->delete('stokadjustment_bahan_temp', ['login_id' => $login_id]);
        echo json_encode(['status' => 'success', 'message' => 'Bahan berhasil dihapus']);
    }

    public function save_stokadjustment()
    {
        try {
            $this->form_validation->set_rules("date", "Tanggal", "required");
            $this->form_validation->set_rules("id_cabang", "Cabang", "required");
            $login_id = $this->session->userdata('ses_id');

            if ($this->form_validation->run() != false) {
                $this->load->database();

                // Initialize arrays
                $data_stokadjustment_bahan = array();
                $data_kartustok = array();

                $temporaryData = $this->db->get_where("stokadjustment_bahan_temp", ["login_id" => $login_id])->result_array();
                if (empty($temporaryData)) {
                    echo json_encode(['status' => 'error', 'message' => 'Tidak ada data di tabel sementara']);
                    return;
                }

                $tanggal = $this->input->post("date", true);
                $tgl = date_create($tanggal);
                if (!$tgl) {
                    throw new Exception("Invalid date format");
                }

                // $periodestokawal = date_format($tgl, 'Y-m');
                // $awalperiodewhere = ' bk.tanggal > "' . $periodestokawal . '-01"';

                // $tgl_sebelumnya = clone $tgl;
                // $tgl_sebelumnya->modify('last day of previous month');
                // $tgl_periode_sebelumnya = date_format($tgl_sebelumnya, 'Y-m-d'); // Format Tahun-Bulan (e.g., 2025-10)

                // 2. Format untuk mendapatkan nilai bulan (numerik) dan tahun
                // $periode_sebelumnya = date_format($tgl_sebelumnya, 'Y-m'); // Format Tahun-Bulan (e.g., 2025-10)
                // $awalwhere = ' bk.periode = "' .  $periode_sebelumnya . '"';
                // $awalwhere = ' bk.tanggal BETWEEN "' .  $periode_sebelumnya . '-01" AND "' . $tanggal . '"';

                $idcabang = $this->input->post("id_cabang", true);
                $cabang = $this->db->query("select * from cabang where id = " . $idcabang)->row();
                $kode_cabang = $cabang->kode_cabang;
                $arr_kode_cabang = array("SN1", "SN2", "SN7", "PU");
                if (in_array($kode_cabang, $arr_kode_cabang)) {
                    $suffix = '';
                } else {
                    $suffix = '_' . $kode_cabang;
                }

                $keterangan = $this->input->post("keterangan", true);

                $data_stokadjustment = array(
                    'cabang_id' => $idcabang,
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
                if (!$this->db->insert('stokadjustment', $data_stokadjustment)) {
                    throw new Exception("Failed to insert stokadjustment: " . $this->db->error()['message']);
                }

                $stokadjusment_id = $this->db->insert_id();

                // $query = " SELECT 
                //             $idcabang AS cabang_id,
                //             b.id, 
                //             b.kode_bahan, 
                //             b.nama_bahan, 
                //             IFNULL(SUM(bk.jumlah_perubahan),0) AS awal
                //             FROM bahan b 
                //             LEFT OUTER JOIN bahan_kartustok" . $suffix . " AS bk ON bk.bahan_id = b.id 
                //             AND $awalwhere 
                //             AND bk.cabang_id = " . $idcabang . "
                //             GROUP BY cabang_id,id,kode_bahan,nama_bahan";
                // $hasil_query = $this->db->query($query);
                // $data_stok_awal = $hasil_query->result_array();

                // $data_by_kode = [];
                // foreach ($data_stok_awal as $bahan) {
                //     $data_by_kode[$bahan['id']] = $bahan;
                // }

                // $data_bahan = $data_by_kode['16'];

                foreach ($temporaryData as $isi) {
                    $data_stokadjustment_bahan[] = array(
                        'stokadjustment_id' => $stokadjusment_id,
                        'bahan_id' => $isi['bahan_id'],
                        'stoksistem' => $isi['stoksistem'],
                        'stok' => $isi['stok']
                    );



                    // $this->db->where('bahan_id', $isi['bahan_id']);
                    // $this->db->where('cabang_id', 1);
                    // $querystok = $this->db->get('bahan_stok');

                    // $total_stok = 0;
                    // if ($querystok->num_rows() == 0) {
                    //     $data_stok = [
                    //         'cabang_id' => 1,
                    //         'bahan_id' => $isi['bahan_id'],
                    //         'jumlah_stok' => $isi['stok']
                    //     ];

                    //     if (!$this->db->insert("bahan_stok", $data_stok)) {
                    //         throw new Exception("Failed to insert bahan_stok: " . $this->db->error()['message']);
                    //     }
                    // } else {
                    //     $row = $querystok->row();
                    //     $total_stok = $row->jumlah_stok;

                    //     $this->db->set('jumlah_stok', 'jumlah_stok-' . $isi['qty'], false);
                    //     $this->db->where('bahan_id', $isi['bahan_id']);
                    //     $this->db->where('cabang_id', 1);
                    //     if (!$this->db->update("bahan_stok")) {
                    //         throw new Exception("Failed to update bahan_stok: " . $this->db->error()['message']);
                    //     }
                    // }

                    $data_kartustok[] = array(
                        'cabang_id' => $idcabang,
                        'bahan_id' => $isi['bahan_id'],
                        'tipe_transaksi' => "Penyesuaian Stok",
                        'transaksi_id' => $stokadjusment_id,
                        'jumlah_perubahan' => $isi['stoksistem'] - $isi['stok'],
                        'harga_beli' => 0,
                        'jumlah_harga' => 0,
                        'total_stok' => $isi['stok'],
                        'total_jumlah_harga' => 0,
                        'tanggal' => date_format($tgl, 'Y-m-d'),
                        'created_at' => date('Y-m-d H:i:s'),
                        'periode' => date_format($tgl, 'Y-m'),
                        'tahun' => date_format($tgl, 'Y'),
                        'shift_id' => 0,
                        'login_id' => $login_id
                    );
                }

                if (!empty($data_stokadjustment_bahan)) {
                    if (!$this->db->insert_batch('stokadjustment_bahan', $data_stokadjustment_bahan)) {
                        throw new Exception("Failed to insert batch stokadjustment_bahan: " . $this->db->error()['message']);
                    }
                }

                if (!empty($data_kartustok)) {
                    if (!$this->db->insert_batch('bahan_kartustok', $data_kartustok)) {
                        throw new Exception("Failed to insert batch bahan_kartustok: " . $this->db->error()['message']);
                    }
                }

                $this->db->delete('stokadjustment_bahan_temp', ['login_id' => $login_id]);

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
            log_message('error', 'Error in save_stokadjustment: ' . $e->getMessage());
            echo json_encode([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }


    // STOK OPNAME

    public function stokopname()
    {
        $this->data = [
            'title_web'  => 'Stok Opname',
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/stok/stokopname', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function data_stokopname()
    {
        $level =  $this->session->userdata('ses_level');
        $where = '';

        if ($this->input->method(true) == 'POST') :
            $query = "SELECT 
                cabang.kode_cabang AS kode_cabang,
                stokopname.*
            FROM stokopname
            LEFT JOIN cabang  
                ON stokopname.cabang_id = cabang.id ";
            // log_message('debug', $query);
            $search = array('cabang.kode_cabang', 'stokopname.periode');
            if ((int)$this->input->get('id')) {
                // $where  = array('cabang_id' => $cabang_id, 'id_kategori' => (int)$this->input->get('id'));
            } else {
                if ($level == 'Admin') {
                } else {
                    // $where  = array('cabang_id' => $cabang_id);
                }
            }
            $iswhere = null;

            header('Content-Type: application/json');
            echo $this->M_Datatables->get_tables_query($query, $search, $where, $iswhere);

        endif;
    }

    public function tambah_stokopname()
    {
        $imp = $this->uri->segment('3');
        if ($imp <> 'import') {
            $login_id =  $this->session->userdata('ses_id');
            $this->db->delete('stokopname_detail_temp', ['login_id' => $login_id]);
        }
        $temp_tanggal =  $this->session->userdata('ses_temp_tanggal');
        $temp_cabang =  $this->session->userdata('ses_temp_cabang');
        $cek = $this->db->get_where("profil_toko", ["nama_toko" => $temp_cabang]); // tulis id yang dituju
        if ($cek->num_rows() > 0) {
            $cabang = $cek->row();
            $temp_cabang = $cabang->cabang_id;
        }
        $this->session->set_userdata('ses_temp_tanggal', '');
        $this->session->set_userdata('ses_temp_cabang', '');
        $this->data = [
            'title_web' => 'Tambah Stok Opname',
            // 'kode'  	=> 'P000'.$kode_cus,
            // 'kode'      => 'BN' . sprintf('%06d', intval($kode_num)),
            'temptanggal' => $temp_tanggal,
            'tempcabang' => $temp_cabang,
            'kode'      => '',
            'cab'       => $this->db->query("SELECT profil_toko.nama_toko,cabang.* FROM cabang LEFT JOIN profil_toko ON profil_toko.cabang_id = cabang.id WHERE cabang.id > 1 ORDER BY length(kode_cabang),kode_cabang ASC")->result(),
            'bahan'       => $this->db->get('bahan')->result()
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/stok/tambah_stokopname', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function get_stokopname_temp()
    {
        $this->load->database();
        $query = $this->db->get('stokopname_detail_temp');
        echo json_encode($query->result());
    }

    public function get_bahan_stokopname_temp()
    {
        $this->load->database();

        $search = $this->input->get('search') ?? ''; // Query pencarian
        $page = $this->input->get('page') ?? 1; // Halaman saat ini
        $limit = $this->input->get('limit') ?? 5; // Jumlah data per halaman
        $offset = ($page - 1) * $limit; // Hitung offset

        $periode_ym = $this->input->get('periode') ?? date('Y-m');
        // $periode_ym = $this->input->get('periode');
        $tanggal = $this->input->get('tanggal') ?? date('Y-m-d');

        // Variabel Suffix dan Cabang (Sesuaikan dengan kebutuhan nyata)
        $idcabang = $this->input->get('idcabang') ?? '1';
        $cabang = $this->db->query("select * from cabang where id = " . $idcabang)->row();
        $kode_cabang = $cabang->kode_cabang;
        $arr_kode_cabang = array("SN1", "SN2", "SN7", "PU");
        if (in_array($kode_cabang, $arr_kode_cabang)) {
            $suffix = '';
        } else {
            $suffix = '_' . $kode_cabang;
        }

        $stok_akhir_subquery = "(
        SELECT 
            bk.total_stok
        FROM bahan_kartustok" . $suffix . " AS bk 
        WHERE 
            bk.bahan_id = b.id AND 
            bk.tanggal <= '" . $tanggal . "'" .
            " AND bk.cabang_id = " . $idcabang .
            " ORDER BY bk.tanggal DESC, bk.id DESC 
        LIMIT 1)";


        // --- 1. Hitung Total Rows (Pencarian) ---
        // Gunakan CI Query Builder untuk kondisi WHERE pencarian
        $this->db->select('b.id');
        $this->db->from('bahan b');

        if (!empty($search)) {
            // Tambahkan kondisi pencarian ke query total rows
            $this->db->like('b.nama_bahan', $search);
        }

        $totalRows = $this->db->count_all_results('', FALSE); // FALSE agar query builder tidak direset
        $this->db->reset_query();


        // --- 2. Ambil Data dengan Pagination dan Stok Akhir ---
        $this->db->select("
        b.kode_bahan, 
        b.nama_bahan, 
        COALESCE($stok_akhir_subquery, 0) AS stok_akhir");
        $this->db->from('bahan b'); // Mulai dari tabel bahan

        // Apply pencarian lagi untuk data utama
        if (!empty($search)) {

            $this->db->group_start();

            $this->db->like(
                'b.kode_bahan',
                $search
            );

            $this->db->or_like(
                'b.nama_bahan',
                $search
            );

            $this->db->group_end();
        }

        // Terapkan pagination
        $this->db->limit($limit, $offset);

        // Eksekusi Query
        $query = $this->db->get();
        $data = $query->result();

        $lastquery = $this->db->last_query();
        log_message('debug', $lastquery);

        // Reset CI Query Builder jika menggunakan FALSE sebelumnya
        $this->db->reset_query();

        echo json_encode([
            'data' => $data,
            'totalRows' => $totalRows,
            'perPage' => $limit,
            'currentPage' => $page,
            'periode' => $periode_ym, // Optional: untuk debugging
            'idcabang' => $idcabang, // Optional: untuk debugging
            'lastquery' => $lastquery

        ]);
    }

    public function export_bahan_stokopname()
    {
        $this->load->database();

        $search   = $this->input->get('search') ?? '';
        $periode  = $this->input->get('periode') ?? date('Y-m');
        $tanggal  = $this->input->get('tanggal') ?? date('Y-m-d');
        $idcabang = $this->input->get('idcabang') ?? '1';
        $format   = $this->input->get('format') ?? 'csv';


        // =====================================================
        // 1. CEK CABANG
        // =====================================================

        $cabang = $this->db
            ->where('id', $idcabang)
            ->get('cabang')
            ->row();

        if (!$cabang) {
            show_error('Cabang tidak ditemukan.');
            return;
        }

        $kode_cabang = $cabang->kode_cabang;


        // =====================================================
        // 2. TENTUKAN SUFFIX TABEL KARTU STOK
        // =====================================================

        $arr_kode_cabang = array(
            "SN1",
            "SN2",
            "SN7",
            "PU"
        );

        if (in_array($kode_cabang, $arr_kode_cabang)) {
            $suffix = '';
        } else {
            $suffix = '_' . $kode_cabang;
        }


        // =====================================================
        // 3. SUBQUERY STOK TERAKHIR
        // =====================================================

        $stok_akhir_subquery = "
        SELECT bk.total_stok
        FROM bahan_kartustok{$suffix} AS bk
        WHERE
            bk.bahan_id = b.id
            AND bk.tanggal <= " . $this->db->escape($tanggal) . "
            AND bk.cabang_id = " . $this->db->escape($idcabang) . "
        ORDER BY
            bk.tanggal DESC,
            bk.id DESC
        LIMIT 1
    ";


        // =====================================================
        // 4. QUERY DATA
        // =====================================================

        $this->db->select("
        b.id,
        b.kode_bahan,
        b.nama_bahan,
        COALESCE(
            ($stok_akhir_subquery),
            0
        ) AS stok_akhir
    ");

        $this->db->from('bahan b');


        // =====================================================
        // 5. SEARCH
        // =====================================================

        if (!empty($search)) {

            $this->db->group_start();

            $this->db->like(
                'b.kode_bahan',
                $search
            );

            $this->db->or_like(
                'b.nama_bahan',
                $search
            );

            $this->db->group_end();
        }


        // =====================================================
        // 6. SORTING
        // =====================================================

        $this->db->order_by(
            'b.kode_bahan',
            'ASC'
        );


        // =====================================================
        // 7. EKSEKUSI
        // =====================================================

        $query = $this->db->get();

        $data = $query->result_array();


        if (empty($data)) {

            show_error(
                'Tidak ada data bahan yang dapat diekspor.'
            );

            return;
        }


        // =====================================================
        // 8. EXPORT CSV
        // =====================================================

        if ($format === 'csv') {

            $filename =
                'daftar_bahan_' .
                $kode_cabang . '_' .
                date('Ymd_His') .
                '.csv';


            header(
                'Content-Type: text/csv; charset=utf-8'
            );

            header(
                'Content-Disposition: attachment; filename="' .
                    $filename .
                    '"'
            );

            header(
                'Cache-Control: no-cache, no-store, must-revalidate'
            );

            header(
                'Pragma: no-cache'
            );


            $output = fopen(
                'php://output',
                'w'
            );


            // BOM UTF-8
            fprintf(
                $output,
                chr(0xEF) .
                    chr(0xBB) .
                    chr(0xBF)
            );


            // Header CSV
            fputcsv(
                $output,
                array(
                    'Kode Bahan',
                    'Nama Bahan',
                    'Stok Sistem',
                    'Stok Fisik'
                )
            );


            // Data
            foreach ($data as $row) {

                fputcsv(
                    $output,
                    array(
                        $row['kode_bahan'],
                        $row['nama_bahan'],
                        $row['stok_akhir'],
                        ''
                    )
                );
            }


            fclose($output);

            exit;
        }


        // =====================================================
        // 9. EXPORT EXCEL
        // =====================================================

        if ($format === 'excel') {

            // Pastikan PhpSpreadsheet tersedia
            require_once FCPATH . 'vendor/autoload.php';


            $spreadsheet =
                new \PhpOffice\PhpSpreadsheet\Spreadsheet();


            $sheet =
                $spreadsheet->getActiveSheet();


            $sheet->setTitle(
                'Daftar Bahan'
            );


            // -------------------------------------------------
            // HEADER
            // -------------------------------------------------

            $sheet->setCellValue(
                'A1',
                'Kode Bahan'
            );

            $sheet->setCellValue(
                'B1',
                'Nama Bahan'
            );

            $sheet->setCellValue(
                'C1',
                'Stok Sistem'
            );

            $sheet->setCellValue(
                'D1',
                'Stok Fisik'
            );


            // -------------------------------------------------
            // DATA
            // -------------------------------------------------

            $rowNumber = 2;

            foreach ($data as $row) {

                $sheet->setCellValue(
                    'A' . $rowNumber,
                    $row['kode_bahan']
                );

                $sheet->setCellValue(
                    'B' . $rowNumber,
                    $row['nama_bahan']
                );

                $sheet->setCellValue(
                    'C' . $rowNumber,
                    $row['stok_akhir']
                );

                $sheet->setCellValue(
                    'D' . $rowNumber,
                    ''
                );

                $rowNumber++;
            }


            // -------------------------------------------------
            // AUTO WIDTH
            // -------------------------------------------------

            foreach (array('A', 'B', 'C', 'D') as $column) {

                $sheet
                    ->getColumnDimension($column)
                    ->setAutoSize(true);
            }


            // -------------------------------------------------
            // HEADER STYLE
            // -------------------------------------------------

            $sheet
                ->getStyle('A1:D1')
                ->getFont()
                ->setBold(true);


            // -------------------------------------------------
            // DOWNLOAD
            // -------------------------------------------------

            $filename =
                'daftar_bahan_' .
                $kode_cabang . '_' .
                date('Ymd_His') .
                '.xlsx';


            header(
                'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            );

            header(
                'Content-Disposition: attachment; filename="' .
                    $filename .
                    '"'
            );

            header(
                'Cache-Control: max-age=0'
            );


            $writer =
                new \PhpOffice\PhpSpreadsheet\Writer\Xlsx(
                    $spreadsheet
                );


            $writer->save(
                'php://output'
            );

            exit;
        }


        show_error(
            'Format export tidak valid.'
        );
    }

    public function check_opname_exists()
    {
        $tanggal = $this->input->post('tanggal_opname', true);
        $cabang_id = $this->input->post('cabang_id', true);

        if (empty($tanggal) || empty($cabang_id)) {
            echo json_encode([
                'exists' => false
            ]);
            return;
        }

        $this->db->where('cabang_id', $cabang_id);
        $this->db->where('tanggal_opname', $tanggal);
        $this->db->where('status !=', 'CANCELLED');

        $query = $this->db->get('stokopname');

        if ($query->num_rows() > 0) {

            $row = $query->row();

            echo json_encode([
                'exists' => true,
                'id' => $row->id,
                'status' => $row->status,
                'tanggal_opname' => $row->tanggal_opname
            ]);
        } else {

            echo json_encode([
                'exists' => false
            ]);
        }
    }

    public function save_stokopname_temp()
    {
        $login_id   = $this->session->userdata('ses_id');

        $bahan_id   = $this->input->post('bahan_id', true);
        $stoksistem = $this->input->post('stoksistem', true);
        $stokfisik  = $this->input->post('stokfisik', true);
        $kode_bahan = $this->input->post('kode', true);
        $nama_bahan = $this->input->post('nama', true);


        // ==========================================
        // VALIDASI
        // ==========================================

        if (empty($login_id)) {

            echo json_encode([
                'status' => 'error',
                'message' => 'Session login tidak ditemukan.'
            ]);

            return;
        }


        if (empty($bahan_id)) {

            echo json_encode([
                'status' => 'error',
                'message' => 'Bahan ID kosong.'
            ]);

            return;
        }


        if ($stokfisik === '' || $stokfisik === null) {

            echo json_encode([
                'status' => 'error',
                'message' => 'Stok fisik kosong.'
            ]);

            return;
        }


        // ==========================================
        // CEK DUPLICATE
        // ==========================================

        $exists = $this->db
            ->where('login_id', $login_id)
            ->where('bahan_id', $bahan_id)
            ->get('stokopname_detail_temp')
            ->row();


        if ($exists) {

            echo json_encode([
                'status' => 'exists',
                'message' =>
                'Bahan ' .
                    $kode_bahan .
                    ' sudah ada di temporary table.'
            ]);

            return;
        }


        // ==========================================
        // DATA
        // ==========================================

        $data = [
            'login_id'    => $login_id,
            'bahan_id'    => $bahan_id,
            'stok_sistem' => (float)$stoksistem,
            'stok_fisik'  => (float)$stokfisik,
            'selisih'     =>
            (float)$stokfisik -
                (float)$stoksistem,
            'kode_bahan'  => $kode_bahan,
            'nama_bahan'  => $nama_bahan
        ];


        // ==========================================
        // INSERT
        // ==========================================

        $insert = $this->db->insert(
            'stokopname_detail_temp',
            $data
        );


        // ==========================================
        // GAGAL INSERT
        // ==========================================

        if (!$insert) {

            $error = $this->db->error();

            log_message(
                'error',
                'Gagal insert stokopname_detail_temp: ' .
                    json_encode($error)
            );


            echo json_encode([
                'status' => 'error',
                'message' => 'Gagal menyimpan data.',
                'db_error' => $error
            ]);

            return;
        }


        // ==========================================
        // BERHASIL
        // ==========================================

        $this->session->set_flashdata(
            'success',
            'Berhasil menambahkan data opname bahan!'
        );


        echo json_encode([
            'status' => 'success',
            'message' => 'Barang berhasil ditambahkan',
            'insert_id' => $this->db->insert_id()
        ]);
    }

    public function delete_stokopname_temp()
    {
        $this->load->database();
        $id = $this->input->post("id", true);

        $this->db->delete('stokopname_detail_temp', ['id' => $id]);
        echo json_encode(['status' => 'success', 'message' => 'Bahan berhasil dihapus']);
    }

    public function delete_stokopname_temp_all()
    {
        $this->load->database();
        // $id = $this->input->post("id", true);
        $login_id =  $this->session->userdata('ses_id');

        $this->db->delete('stokopname_detail_temp', ['login_id' => $login_id]);
        echo json_encode(['status' => 'success', 'message' => 'Bahan berhasil dihapus']);
    }

    public function save_stokopname()
    {
        try {
            $this->form_validation->set_rules("date", "Tanggal", "required");
            $this->form_validation->set_rules("id_cabang", "Cabang", "required");
            $login_id = $this->session->userdata('ses_id');

            if ($this->form_validation->run() != false) {
                $this->load->database();

                // Initialize arrays
                $data_stokopname_detail = array();
                $data_kartustok = array();

                $temporaryData = $this->db->get_where("stokopname_detail_temp", ["login_id" => $login_id])->result_array();
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
                $cabang = $this->db->query("select * from cabang where id = " . $idcabang)->row();
                $kode_cabang = $cabang->kode_cabang;
                $arr_kode_cabang = array("SN1", "SN2", "SN7", "PU");
                if (in_array($kode_cabang, $arr_kode_cabang)) {
                    $suffix = '';
                } else {
                    $suffix = '_' . $kode_cabang;
                }

                $keterangan = $this->input->post("keterangan", true);

                $data_stokopname = array(
                    'cabang_id' => $idcabang,
                    'keterangan' => $keterangan,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'tanggal_opname' => date_format($tgl, 'Y-m-d'),
                    'tanggal_input' => date('Y-m-d'),
                    'periode' => date_format($tgl, 'Y-m'),
                    'year' => date_format($tgl, 'Y'),
                    'login_id' => $login_id,
                    'status' => 'POSTED',
                );

                $this->db->trans_start();

                // Insert main transfer record
                if (!$this->db->insert('stokopname', $data_stokopname)) {
                    throw new Exception("Failed to insert stokopname: " . $this->db->error()['message']);
                }

                $stokopname_id = $this->db->insert_id();

                foreach ($temporaryData as $isi) {
                    $data_stokopname_detail[] = array(
                        'stokopname_id' => $stokopname_id,
                        'bahan_id' => $isi['bahan_id'],
                        'stok_sistem' => $isi['stok_sistem'],
                        'stok_fisik' => $isi['stok_fisik'],
                        'selisih' => $isi['selisih'],
                        'harga_beli' => 0
                    );

                    $data_kartustok[] = array(
                        'cabang_id' => $idcabang,
                        'bahan_id' => $isi['bahan_id'],
                        'tipe_transaksi' => "OPNAME",
                        'transaksi_id' => $stokopname_id,
                        'jumlah_perubahan' => $isi['stok_fisik'] - $isi['stok_sistem'],
                        'harga_beli' => 0,
                        'jumlah_harga' => 0,
                        'total_stok' => $isi['stok_fisik'],
                        'total_jumlah_harga' => 0,
                        'tanggal' => date_format($tgl, 'Y-m-d'),
                        'created_at' => date('Y-m-d H:i:s'),
                        'periode' => date_format($tgl, 'Y-m'),
                        'tahun' => date_format($tgl, 'Y'),
                        'shift_id' => 0,
                        'login_id' => $login_id
                    );
                }

                if (!empty($data_stokopname_detail)) {
                    if (!$this->db->insert_batch('stokopname_detail', $data_stokopname_detail)) {
                        throw new Exception("Failed to insert batch stokopname_detail: " . $this->db->error()['message']);
                    }
                }

                if (!empty($data_kartustok)) {
                    if (!$this->db->insert_batch('bahan_kartustok' . $suffix, $data_kartustok)) {
                        throw new Exception("Failed to insert batch bahan_kartustok" . $suffix . ": " . $this->db->error()['message']);
                    }
                }

                $this->db->delete('stokopname_detail_temp', ['login_id' => $login_id]);

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
            log_message('error', 'Error in save_stokopname: ' . $e->getMessage());
            echo json_encode([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function get_bahan_by_kode()
    {
        $kode = $this->input->post('kode', true);

        if (empty($kode)) {
            echo json_encode([
                'status' => false,
                'message' => 'Kode bahan kosong.'
            ]);
            return;
        }

        $bahan = $this->db
            ->select('id, kode_bahan, nama_bahan')
            ->where('kode_bahan', $kode)
            ->get('bahan')
            ->row();

        if (!$bahan) {
            echo json_encode([
                'status' => false,
                'message' => 'Kode bahan "' . $kode . '" tidak ditemukan.'
            ]);
            return;
        }

        echo json_encode([
            'status' => true,
            'data' => [
                'id' => $bahan->id,
                'kode_bahan' => $bahan->kode_bahan,
                'nama_bahan' => $bahan->nama_bahan
            ]
        ]);
    }
}
