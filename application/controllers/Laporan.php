<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Laporan extends CI_Controller
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
        $cabang_id = $this->session->userdata('ses_cabang_id');
        $level = $this->session->userdata('ses_level');
        $kasir_id = $this->session->userdata('ses_id');
        $iswhere = '';
        $suffix = $this->session->userdata('ses_suffix');
        $nama_cabang = '';

        if (in_array($this->session->userdata('ses_level'), array('Admin', 'AdminKasir'))) {
            if (!empty(htmlentities($this->input->get('idcabang', true)))) {
                $cabang_id = htmlentities($this->input->get('idcabang', true));

                if (!empty($cabang_id)) {
                    $caricabang = $this->db->query('SELECT *, (SELECT nama_toko FROM profil_toko WHERE profil_toko.cabang_id = cabang.id) AS namacabang FROM cabang WHERE id = ? ', [$cabang_id])->row();
                    if ($caricabang) {
                        $kode_cabang = $caricabang->kode_cabang;
                        $arr_kode_cabang = array("SN1", "SN2", "SN7", "PU");
                        $suffix = in_array($kode_cabang, $arr_kode_cabang) ? '' : '_' . $kode_cabang;
                        $nama_cabang = 'Cabang: ' . $caricabang->namacabang;
                    }
                }
            }
        }

        if (in_array($level, array('Admin', 'AdminKasir'))) {

            if (!empty(htmlentities($this->input->get('a', true)))) {
                $a = htmlentities($this->input->get('a', true));
                $b = htmlentities($this->input->get('b', true));
                if ($this->input->get('shift')) {
                    $ks = $this->input->get('shift');
                    $shift = $this->db->get_where('shift', ['id' => $ks])->row();
                    $iswhere = ' WHERE (transaksi.date BETWEEN "' . $a . '" AND "' . $b . '") AND ( shift_id = ' . $shift->id . ' ) AND (transaksi.status != "Bayar Nanti") ' . ' AND transaksi.cabang_id = ' . $cabang_id;
                    $periode =  $nama_cabang . ' Shift : ' . $shift->nama . ' Periode ' . time_explode_date(htmlentities($this->input->get('a', true)), 'id') . ' s.d. ' . time_explode_date(htmlentities($this->input->get('b', true)), 'id');
                    $urlexcel = base_url('laporan/excel?shift=' . $ks . '&a=' . $a . '&b=' . $b);
                } else {
                    $iswhere = ' WHERE transaksi.date BETWEEN "' . $a . '" AND "' . $b . '" AND transaksi.status != "Bayar Nanti"' . ' AND transaksi.cabang_id = ' . $cabang_id;
                    $periode =  $nama_cabang . ' Periode ' . time_explode_date(htmlentities($this->input->get('a', true)), 'id') . ' s.d. ' . time_explode_date(htmlentities($this->input->get('b', true)), 'id');
                    $urlexcel = base_url('laporan/excel?a=' . $a . '&b=' . $b);
                }
            } else {
                if ($this->input->get('shift')) {
                    $ks = $this->input->get('shift');
                    $shift = $this->db->get_where('shift', ['id' => $ks])->row();
                    $iswhere = ' WHERE shift_id = ' . $ks . ' AND periode = "' . date('Y-m') . '" AND transaksi.status != "Bayar Nanti"' . ' AND transaksi.cabang_id = ' . $cabang_id;
                    $periode = $nama_cabang . ' Shift : ' . $shift->nama;
                    $urlexcel = base_url('laporan/excel?shift=' . $ks);
                } else {
                    $iswhere = ' WHERE periode = "' . date('Y-m') . '" AND transaksi.status != "Bayar Nanti"' . ' AND transaksi.cabang_id = ' . $cabang_id;
                    $periode = $nama_cabang . ' Periode ' . bln('id') . ' ' . date('Y');
                    $urlexcel = base_url('laporan/excel');
                }
            }
        } else {
            if (!empty(htmlentities($this->input->get('a', true)))) {
                $a = htmlentities($this->input->get('a', true));
                $b = htmlentities($this->input->get('b', true));
                if ($this->input->get('shift')) {
                    $ks = $this->input->get('shift');
                    $shift = $this->db->get_where('shift', ['id' => $ks])->row();
                    $iswhere = ' WHERE (kasir_id = ' . $kasir_id . ') AND (transaksi.date BETWEEN "' . $a . '" AND "' . $b . '") AND ( shift_id = ' . $shift->id . ' ) AND (transaksi.status != "Bayar Nanti")';
                    $periode = 'Shift : ' . $shift->nama . ' Periode ' . time_explode_date(htmlentities($this->input->get('a', true)), 'id') . ' s.d. ' . time_explode_date(htmlentities($this->input->get('b', true)), 'id');
                    $urlexcel = base_url('laporan/excel?shift=' . $ks . '&a=' . $a . '&b=' . $b);
                } else {
                    $iswhere = ' WHERE (kasir_id = ' . $kasir_id . ') AND transaksi.date BETWEEN "' . $a . '" AND "' . $b . '" AND transaksi.status != "Bayar Nanti"';
                    $periode = 'Periode ' . time_explode_date(htmlentities($this->input->get('a', true)), 'id') . ' s.d. ' . time_explode_date(htmlentities($this->input->get('b', true)), 'id');
                    $urlexcel = base_url('laporan/excel?a=' . $a . '&b=' . $b);
                }
            } else {
                if ($this->input->get('shift')) {
                    $ks = $this->input->get('shift');
                    $shift = $this->db->get_where('shift', ['id' => $ks])->row();
                    $iswhere = ' WHERE (kasir_id = ' . $kasir_id . ') AND shift_id = ' . $ks . ' periode = "' . date('Y-m') . '" AND transaksi.status != "Bayar Nanti"';
                    $periode = 'Shift : ' . $shift->nama;
                    $urlexcel = base_url('laporan/excel?shift=' . $ks);
                } else {
                    $iswhere = ' WHERE (kasir_id = ' . $kasir_id . ') AND periode = "' . date('Y-m') . '" AND transaksi.status != "Bayar Nanti"';
                    // $iswhere = ' WHERE closing_id = 0 AND transaksi.status != "Bayar Nanti" ';
                    $periode = 'Periode ' . bln('id') . ' ' . date('Y');
                    $urlexcel = base_url('laporan/excel');
                }
            }
        }
        // echo 'SELECT SUM(grandtotal) as gr, SUM(grandmodal) as gm, SUM(total_qty) as qty FROM transaksi' . $iswhere;
        $total = $this->db->query('SELECT SUM(grandtotal) as gr, SUM(grandmodal) as gm, SUM(total_qty) as qty FROM transaksi' . $suffix . ' AS transaksi ' . $iswhere)->row();
        $this->data = [
            'title_web' => 'Laporan',
            // 'title_web' =>  $iswhere,
            'periode' => $periode,
            'total' => $total,
            'urlexcel' => $urlexcel
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/laporan/index', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function data_order()
    {
        if (in_array($this->session->userdata('ses_level'), array('Admin', 'AdminKasir'))) {
            if (!empty(htmlentities($this->input->get('idcabang', true)))) {
                $cabang_id = htmlentities($this->input->get('idcabang', true));

                if (!empty($cabang_id)) {
                    $caricabang = $this->db->query('SELECT * FROM cabang WHERE id = ? ', [$cabang_id])->row();
                    if ($caricabang) {
                        $kode_cabang = $caricabang->kode_cabang;
                        $arr_kode_cabang = array("SN1", "SN2", "SN7", "PU");
                        $suffix = in_array($kode_cabang, $arr_kode_cabang) ? '' : '_' . $kode_cabang;
                    }
                }
            }
        } else {
            $suffix = $this->session->userdata('ses_suffix');
        }
        if ($this->input->method(true) == 'POST') :
            $query = "SELECT customer.nama, login.nama_user, profil_toko.nama_toko, transaksi.* FROM transaksi" . $suffix . " AS transaksi 
                LEFT JOIN customer ON transaksi.customer_id = customer.id 
                LEFT JOIN profil_toko ON transaksi.cabang_id = profil_toko.cabang_id 
                LEFT JOIN login ON transaksi.kasir_id=login.id";
            $search = [
                'nama',
                'nama_user',
                'no_bon',
                'atas_nama',
                'grandtotal',
                'date',
                'status',
                'pesanan'
            ];
            if (in_array($this->session->userdata('ses_level'), array('Admin', 'AdminKasir'))) {
                // if ($this->session->userdata('ses_level') == 'Admin') {
                $where = null;

                if (!empty(htmlentities($this->input->get('a', true)))) {
                    $a = htmlentities($this->input->get('a', true));
                    $b = htmlentities($this->input->get('b', true));

                    if ($this->input->get('shift')) {
                        $shift_id = $this->input->get('shift');
                        $iswhere = 'transaksi.date BETWEEN "' . $a . '" AND "' . $b . '" AND transaksi.shift_id = ' . $shift_id . ' AND transaksi.cabang_id = ' . $cabang_id;
                    } else {
                        $iswhere = 'transaksi.date BETWEEN "' . $a . '" AND "' . $b . '"' . ' AND transaksi.cabang_id = ' . $cabang_id;
                    }
                } else {
                    if ($this->input->get('shift')) {
                        $shift_id = $this->input->get('shift');
                        $iswhere = 'transaksi.shift_id = ' . $shift_id . '" AND periode = "' . date('Y-m') . '"' . ' AND transaksi.cabang_id = ' . $cabang_id;
                    } else {
                        $iswhere = 'periode = "' . date('Y-m') . '"' . ' AND transaksi.cabang_id = ' . $cabang_id;
                    }
                }
            } else {
                $where = array('transaksi.kasir_id' => $this->session->userdata('ses_id'));
                // $cabang_id = $this->session->userdata('ses_cabang_id');

                if (!empty(htmlentities($this->input->get('a', true)))) {
                    $a = htmlentities($this->input->get('a', true));
                    $b = htmlentities($this->input->get('b', true));
                    if ($this->input->get('shift')) {
                        $shift_id = $this->input->get('shift');
                        $iswhere = 'transaksi.date BETWEEN "' . $a . '" AND "' . $b . '" AND transaksi.shift_id = ' . $shift_id;
                    } else {
                        $iswhere = 'transaksi.date BETWEEN "' . $a . '" AND "' . $b . '"';
                    }
                } else {
                    if ($this->input->get('shift')) {
                        $shift_id = $this->input->get('shift');
                        $iswhere = 'transaksi.shift_id = ' . $shift_id . '" AND periode = "' . date('Y-m') . '"';
                    } else {
                        $iswhere = 'periode = "' . date('Y-m') . '"';
                    }
                }
            }


            header('Content-Type: application/json');
            echo $this->M_Datatables->get_tables_query($query, $search, $where, $iswhere);
        endif;
    }

    public function excel()
    {
        $suffix = $this->session->userdata('ses_suffix');

        $query = "SELECT customer.nama, login.nama_user, (SELECT nama FROM shift WHERE closing.shift_id = shift.id) AS 'nama_shift', transaksi.* FROM transaksi" . $suffix . " AS transaksi 
                LEFT JOIN customer ON transaksi.customer_id = customer.id 
                LEFT JOIN login ON transaksi.kasir_id=login.id
                LEFT JOIN closing ON transaksi.closing_id=closing.id";

        if (!empty(htmlentities($this->input->get('a', true)))) {
            $a = htmlentities($this->input->get('a', true));
            $b = htmlentities($this->input->get('b', true));
            if ($this->input->get('shift')) {
                $ks = $this->input->get('shift');
                $shift = $this->db->get_where('shift', ['id' => $ks])->row();
                $iswhere = ' WHERE transaksi.date BETWEEN "' . $a . '" AND "' . $b . '" AND transaksi.shift_id = ' . $shift->id . ' AND transaksi.status != "Bayar Nanti"';
                $periode = 'Shift : ' . $shift->nama . ' Periode ' . time_explode_date(htmlentities($this->input->get('a', true)), 'id') . ' s.d. ' . time_explode_date(htmlentities($this->input->get('b', true)), 'id');
            } else {
                $iswhere = ' WHERE transaksi.date BETWEEN "' . $a . '" AND "' . $b . '" AND transaksi.status != "Bayar Nanti"';
                $periode = 'Periode ' . time_explode_date(htmlentities($this->input->get('a', true)), 'id') . ' s.d. ' . time_explode_date(htmlentities($this->input->get('b', true)), 'id');
            }
        } else {
            if ($this->input->get('shift')) {
                $ks = $this->input->get('shift');
                $shift = $this->db->get_where('shift', ['id' => $ks])->row();
                $iswhere = ' WHERE kasir_id = ' . $ks . ' AND transaksi.status != "Bayar Nanti"';
                $periode = 'Shift : ' . $shift->nama;
            } else {
                $iswhere = ' WHERE periode = "' . date('Y-m') . '" AND transaksi.status != "Bayar Nanti"';
                $periode = 'Periode ' . bln('id') . ' ' . date('Y');
            }
        }
        $transaksi = $this->db->query($query . $iswhere)->result();

        $this->data = [
            'transaksi' => $transaksi,
            'periode' => $periode,
        ];

        $this->load->view('admin/laporan/excel', $this->data);
    }

    public function cash()
    {
        $suffix = $this->session->userdata('ses_suffix');

        if (!empty($this->input->get('m'))) {
            $m = $this->input->get('m');
            $y = $this->input->get('y');
            $iswhere = ' WHERE periode = "' . $y . '-' . $m . '" ';
            $periode = 'Periode ' . bulan($m, 'id') . ' ' . $y;
        } else {
            $iswhere = ' WHERE periode = "' . date('Y-m') . '" ';
            $periode = 'Periode ' . bln('id') . ' ' . date('Y');
        }

        $total = $this->db->query('SELECT SUM(grandtotal) as gr, SUM(grandmodal) as gm, SUM(total_qty) as qty FROM transaksi' . $suffix . ' AS transaksi ' . $iswhere . ' AND transaksi.status != "Bayar Nanti"')->row();
        $this->data = [
            'title_web' => 'Cash Flow ',
            'periode' => $periode,
            'total' => $total,
            'keuangan' => $this->db->query('SELECT keuangan_ledger.keterangan as ket, keuangan_lainnya.* 
                            FROM keuangan_lainnya 
                            LEFT JOIN keuangan_ledger 
                            ON keuangan_lainnya.no_ledger = keuangan_ledger.no_ledger ' . $iswhere)->result()
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/laporan/cash', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function pdf()
    {
        $suffix = $this->session->userdata('ses_suffix');

        // panggil library yang kita buat sebelumnya yang bernama pdfgenerator
        $this->load->library('pdfgenerator');
        if (!empty($this->input->get('m'))) {
            $m = $this->input->get('m');
            $y = $this->input->get('y');
            $iswhere = ' WHERE periode = "' . $y . '-' . $m . '"';
            $periode = 'Periode ' . bulan($m, 'id') . ' ' . $y;
        } else {
            $iswhere = ' WHERE periode = "' . date('Y-m') . '"';
            $periode = 'Periode ' . bln('id') . ' ' . date('Y');
        }
        $total = $this->db->query('SELECT SUM(grandtotal) as gr, SUM(grandmodal) as gm, SUM(total_qty) as qty FROM transaksi' . $suffix . ' AS transaksi ' . $iswhere . ' AND transaksi.status != "Bayar Nanti"')->row();
        // title dari pdf
        $this->data['title_pdf'] = 'Cash Flow ' . $periode;
        $this->data['keuangan'] = $this->db->query('SELECT keuangan_ledger.keterangan as ket, keuangan_lainnya.* 
                FROM keuangan_lainnya 
                LEFT JOIN keuangan_ledger 
                ON keuangan_lainnya.no_ledger = keuangan_ledger.no_ledger ' . $iswhere)->result();
        $this->data['periode'] = $periode;
        $this->data['total'] = $total;
        // filename dari pdf ketika didownload
        $file_pdf = 'laporan_cash_flow_' . date('Y-m-d');
        // setting paper
        $paper = 'A4';
        //orientasi paper potrait / landscape
        $orientation = "portrait";

        $html = $this->load->view('admin/laporan/pdf', $this->data, true);

        // run dompdf
        $this->pdfgenerator->generate($html, $file_pdf, $paper, $orientation);
    }

    // laporan perproduk
    public function produk()
    {
        $suffix = $this->session->userdata('ses_suffix');

        if (!empty($this->input->get('nama', true))) {
            $nama = ' AND nama_menu LIKE "%' . $this->input->get('nama', true) . '%"';
        } else {
            $nama = '';
        }

        if ($this->session->userdata('ses_level') == 'Admin') {
            $auth = '';
        } else {
            $uid = $this->session->userdata('ses_id');
            $auth = " AND transaksi.kasir_id = $uid";
        }

        if (!empty(htmlentities($this->input->get('a', true)))) {
            $a = htmlentities($this->input->get('a', true));
            $b = htmlentities($this->input->get('b', true));
            $iswhere = 'WHERE transaksi_produk.date BETWEEN "' . $a . '" AND "' . $b . '" ' . $nama . " " . $auth;
            $periode = 'Periode ' . time_explode_date(htmlentities($this->input->get('a', true)), 'id') . ' s.d. ' . time_explode_date(htmlentities($this->input->get('b', true)), 'id');
            $urlexcel = base_url('laporan/produk_excel?a=' . $a . '&b=' . $b);
        } else {
            $iswhere = ' WHERE transaksi_produk.periode = "' . date('Y-m') . '" ' . $nama . " " . $auth;
            $periode = 'Periode ' . bln('id') . ' ' . date('Y');
            $urlexcel = base_url('laporan/produk_excel');
        }

        $total = $this->db->query('SELECT SUM(transaksi_produk.harga_beli * qty) as hb, 
                        SUM(transaksi_produk.harga_jual* qty) as hj, 
                        SUM(transaksi_produk.qty) as qty,
                        transaksi.kasir_id  FROM transaksi_produk' . $suffix . ' AS  transaksi_produk
                        LEFT JOIN transaksi' . $suffix . ' AS transaksi ON transaksi_produk.no_bon=transaksi.no_bon ' . $iswhere)->row();
        $this->data = [
            'title_web' => 'Laporan',
            'periode' => $periode,
            'total' => $total,
            'urlexcel' => $urlexcel
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/laporan/produk', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function data_produk()
    {
        $suffix = $this->session->userdata('ses_suffix');

        if ($this->input->method(true) == 'POST') :
            $query = "SELECT customer.nama, login.nama_user, transaksi.atas_nama, 
                transaksi.pesanan,transaksi.status,transaksi.customer_id, 
                transaksi_produk.* FROM transaksi_produk" . $suffix . " AS transaksi_produk 
                LEFT JOIN transaksi" . $suffix . " AS transaksi ON transaksi_produk.no_bon=transaksi.no_bon 
                LEFT JOIN customer ON transaksi.customer_id = customer.id 
                LEFT JOIN login ON transaksi.kasir_id=login.id";
            $search = [
                'kode_menu',
                'nama',
                'nama_user',
                'transaksi_produk.no_bon',
                'atas_nama',
                'pesanan',
                'nama_menu',
                'kategori',
            ];

            if ($this->session->userdata('ses_level') == 'Admin') {
                $where = null;
            } else {
                $where = array('transaksi.kasir_id' => $this->session->userdata('ses_id'));
            }

            if (!empty(htmlentities($this->input->get('a', true)))) {
                $a = htmlentities($this->input->get('a', true));
                $b = htmlentities($this->input->get('b', true));
                $iswhere = 'transaksi_produk.date BETWEEN "' . $a . '" AND "' . $b . '"';
            } else {
                $iswhere = ' transaksi_produk.periode = "' . date('Y-m') . '"';
            }

            header('Content-Type: application/json');
            echo $this->M_Datatables->get_tables_query($query, $search, $where, $iswhere);
        endif;
    }

    public function produk_excel()
    {
        $suffix = $this->session->userdata('ses_suffix');

        $query = "SELECT customer.nama, login.nama_user, transaksi.atas_nama, 
            transaksi.pesanan,transaksi.status,transaksi.customer_id,
            transaksi_produk.* FROM transaksi_produk" . $suffix . " AS transaksi_produk 
            LEFT JOIN transaksi" . $suffix . " AS transaksi ON transaksi_produk.no_bon=transaksi.no_bon 
            LEFT JOIN customer ON transaksi.customer_id = customer.id 
            LEFT JOIN login ON transaksi.kasir_id=login.id";
        if (!empty(htmlentities($this->input->get('a', true)))) {
            $a = htmlentities($this->input->get('a', true));
            $b = htmlentities($this->input->get('b', true));
            $iswhere = ' WHERE transaksi_produk.date BETWEEN "' . $a . '" AND "' . $b . '"';
            $periode = 'PERIODE ' . time_explode_date(htmlentities($this->input->get('a', true)), 'id') . ' s.d. ' . time_explode_date(htmlentities($this->input->get('b', true)), 'id');
        } else {
            $iswhere = ' WHERE transaksi_produk.periode  = "' . date('Y-m') . '"';
            $periode = 'PERIODE ' . bln('id') . ' ' . date('Y');
        }

        $transaksi = $this->db->query($query . $iswhere)->result();

        $this->data = [
            'transaksi' => $transaksi,
            'periode' => $periode,
        ];

        $this->load->view('admin/laporan/produk_excel', $this->data);
    }

    public function terlaris()
    {
        if (!empty(htmlentities($this->input->get('a', true)))) {
            $a = htmlentities($this->input->get('a', true));
            $b = htmlentities($this->input->get('b', true));
            $periode = 'Periode ' . time_explode_date(htmlentities($this->input->get('a', true)), 'id') . ' s.d. ' . time_explode_date(htmlentities($this->input->get('b', true)), 'id');
            $urlexcel = base_url('laporan/laris_excel?a=' . $a . '&b=' . $b);
        } else {
            $periode = 'Periode ' . bln('id') . ' ' . date('Y');
            $urlexcel = base_url('laporan/laris_excel');
        }
        $this->data = [
            'title_web' => 'Laporan',
            'periode' => $periode,
            'urlexcel' => $urlexcel
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/laporan/laris', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function closing()
    {
        $cabang_id = htmlentities($this->input->get('cabang', true));

        // $cabang_id = $this->session->userdata('ses_cabang_id');
        $level = $this->session->userdata('ses_level');
        $kasir_id = $this->session->userdata('ses_id');
        $iswhere = '';
        $iscabang = '';
        $namacabang = '';

        // if ($level == 'Admin') {
        if (in_array($level, array('Admin', 'AdminKasir'))) {
            if (!empty(htmlentities($this->input->get('cabang', true)))) {
                $cabang_id = htmlentities($this->input->get('cabang', true));
                $iscabang = ' AND cabang_id = ' . $cabang_id;
                $namacabang = $this->db->query('SELECT nama_toko  FROM profil_toko where cabang_id = ' . $cabang_id)->row();
            }

            if (!empty(htmlentities($this->input->get('a', true)))) {
                $a = htmlentities($this->input->get('a', true));
                $b = htmlentities($this->input->get('b', true));
                if ($this->input->get('shift')) {
                    $ks = $this->input->get('shift');
                    $shift = $this->db->get_where('shift', ['id' => $ks])->row();
                    $iswhere = ' WHERE (closing.date BETWEEN "' . $a . '" AND "' . $b . '") AND ( shift_id = ' . $shift->id . ' ) AND (closing.status = "CLOSE")';
                    $periode = 'Shift : ' . $shift->nama . ' Periode ' . time_explode_date(htmlentities($this->input->get('a', true)), 'id') . ' s.d. ' . time_explode_date(htmlentities($this->input->get('b', true)), 'id');
                    // $urlexcel = base_url('laporan/excel?shift=' . $ks . '&a=' . $a . '&b=' . $b);
                } else {
                    $iswhere = ' WHERE closing.date BETWEEN "' . $a . '" AND "' . $b . '" AND closing.status = "CLOSE"';
                    $periode = 'Periode ' . time_explode_date(htmlentities($this->input->get('a', true)), 'id') . ' s.d. ' . time_explode_date(htmlentities($this->input->get('b', true)), 'id');
                    // $urlexcel = base_url('laporan/excel?a=' . $a . '&b=' . $b);
                }
            } else {
                if ($this->input->get('shift')) {
                    $ks = $this->input->get('shift');
                    $shift = $this->db->get_where('shift', ['id' => $ks])->row();
                    $iswhere = ' WHERE shift_id = ' . $ks . ' AND periode = "' . date('Y-m') . '" AND closing.status != "CLOSE"';
                    $periode = 'Shift : ' . $shift->nama;
                    $urlexcel = base_url('laporan/excel?shift=' . $ks);
                } else {
                    $iswhere = ' WHERE periode = "' . date('Y-m') . '"';
                    $periode = 'Periode ' . bln('id') . ' ' . date('Y');
                    $urlexcel = base_url('laporan/excel');
                }
            }
            $iswhere = $iswhere . $iscabang;
        } else {
            if (!empty(htmlentities($this->input->get('a', true)))) {
                $a = htmlentities($this->input->get('a', true));
                $b = htmlentities($this->input->get('b', true));
                if ($this->input->get('shift')) {
                    $ks = $this->input->get('shift');
                    $shift = $this->db->get_where('shift', ['id' => $ks])->row();
                    $iswhere = ' WHERE (kasir_id = ' . $kasir_id . ') AND (closing.date BETWEEN "' . $a . '" AND "' . $b . '") ';
                    $periode = 'Shift : ' . $shift->nama . ' Periode ' . time_explode_date(htmlentities($this->input->get('a', true)), 'id') . ' s.d. ' . time_explode_date(htmlentities($this->input->get('b', true)), 'id');
                    $urlexcel = base_url('laporan/excel?shift=' . $ks . '&a=' . $a . '&b=' . $b);
                } else {
                    $iswhere = ' WHERE (kasir_id = ' . $kasir_id . ') AND closing.date BETWEEN "' . $a . '" AND "' . $b . '"';
                    $periode = 'Periode ' . time_explode_date(htmlentities($this->input->get('a', true)), 'id') . ' s.d. ' . time_explode_date(htmlentities($this->input->get('b', true)), 'id');
                    $urlexcel = base_url('laporan/excel?a=' . $a . '&b=' . $b);
                }
            } else {
                if ($this->input->get('shift')) {
                    $ks = $this->input->get('shift');
                    $shift = $this->db->get_where('shift', ['id' => $ks])->row();
                    $iswhere = ' WHERE (kasir_id = ' . $kasir_id . ') AND shift_id = ' . $ks . ' AND periode = "' . date('Y-m') . '"';
                    $periode = 'Shift : ' . $shift->nama;
                    $urlexcel = base_url('laporan/excel?shift=' . $ks);
                } else {
                    $iswhere = ' WHERE (kasir_id = ' . $kasir_id . ') AND periode = "' . date('Y-m') . '"';
                    // $iswhere = ' WHERE closing_id = 0 AND transaksi.status != "Bayar Nanti" ';
                    $periode = 'Periode ' . bln('id') . ' ' . date('Y');
                    $urlexcel = base_url('laporan/excel');
                }
            }
        }
        // echo 'SELECT SUM(pemasukan) as pm, SUM(pengeluaran) as pg, SUM(sisa_uang) as su FROM closing ' . $iswhere;
        // echo $iswhere;
        $total = $this->db->query('SELECT SUM(pemasukan) as pm, SUM(pengeluaran) as pg, SUM(sisa_uang) as su, SUM(qris) as qr, SUM(online) as ol FROM closing ' . $iswhere)->row();

        if ($namacabang) {
            $this->data = [
                'title_web' => 'Laporan Closing',
                'cabangpilih' =>  $namacabang->nama_toko,
                'periode' => $periode,
                'total' => $total,
                'urlexcel' => '' //$urlexcel
            ];
        } else {
            $this->data = [
                'title_web' => 'Laporan Closing',
                'cabangpilih' =>  '',
                'periode' => $periode,
                'total' => $total,
                'urlexcel' => '' //$urlexcel
            ];
        }

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/laporan/closing', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function data_closing()
    {
        if ($this->input->method(true) == 'POST') :
            $query = "SELECT cabang.nama_cabang,shift.nama,login.nama_user,closing.pemasukan+closing.qris+closing.online as 'total',closing.* FROM closing
            LEFT JOIN cabang ON closing.cabang_id = cabang.id
            LEFT JOIN shift ON closing.shift_id = shift.id
            LEFT JOIN login ON closing.kasir_id = login.id";
            $search = [
                'nama_cabang',
                'nama',
                'nama_user',
                'date',
                'periode',
                'year'
            ];

            // if ($this->session->userdata('ses_level') == 'Admin') {
            if (in_array($this->session->userdata('ses_level'), array('Admin', 'AdminKasir'))) {
                if (!empty(htmlentities($this->input->get('cabang', true)))) {
                    $cabang_id = htmlentities($this->input->get('cabang', true));
                    $where = array('closing.cabang_id' => $cabang_id);
                } else {
                    $where = null;
                }
                if ($this->input->get('shift')) {
                    $shift_id = $this->input->get('shift');
                    // $where = 'closing.date BETWEEN "' . $a . '" AND "' . $b . '" AND closing.shift_id = ' . $shift_id;
                    $where += array('closing.shift_id' => $shift_id);
                }
            } else {
                $where = array('closing.kasir_id' => $this->session->userdata('ses_id'));
            }
            // $where = array('transaksi.kasir_id' => $this->session->userdata('ses_id'));
            // }
            // $cabang_id = $this->session->userdata('ses_cabang_id');

            // $shift = $this->db->query("SELECT * FROM transaksi WHERE closing_id = 0 AND cabang_id='" . $cabang_id . "' ORDER BY created_at LIMIT 1");

            // if ($shift->num_rows() > 0) {
            //     $ps = $shift->row();
            //     $shift_id = $ps->shift_id;
            // } else {
            //     $shift_id = 0;
            // }
            // if ($this->input->get('kasir')) {
            //     $kasir_id = $this->input->get('kasir');

            if (!empty(htmlentities($this->input->get('a', true)))) {
                $a = htmlentities($this->input->get('a', true));
                $b = htmlentities($this->input->get('b', true));
                // if ($this->input->get('shift')) {
                //     $shift_id = $this->input->get('shift');
                //     $iswhere = 'closing.date BETWEEN "' . $a . '" AND "' . $b . '" AND closing.shift_id = ' . $shift_id;
                // } else {
                $iswhere = 'closing.date BETWEEN "' . $a . '" AND "' . $b . '"';
                // }
            } else {
                // if ($this->input->get('shift')) {
                //     $shift_id = $this->input->get('shift');
                //     $iswhere = 'closing.shift_id = ' . $shift_id . '" AND periode = "' . date('Y-m') . '"';
                // } else {
                $iswhere = 'closing.periode = "' . date('Y-m') . '"';
                // }
            }
            // } else {
            // }

            header('Content-Type: application/json');
            // echo $this->M_Datatables->get_tables_query($query, $search, $where, $iswhere);
            echo $this->M_Datatables->get_tables_query($query, $search, $where, $iswhere);
        endif;
    }

    public function penjualan()
    {
        $cabang_id = $this->session->userdata('ses_cabang_id');
        $level = $this->session->userdata('ses_level');
        $kasir_id = $this->session->userdata('ses_id');
        $iswhere = '';


        // if ($level == 'Admin') {
        if (in_array($level, array('Admin', 'AdminKasir'))) {

            if (!empty(htmlentities($this->input->get('a', true)))) {
                $a = htmlentities($this->input->get('a', true));
                $b = htmlentities($this->input->get('b', true));
                if ($this->input->get('shift')) {
                    $ks = $this->input->get('shift');
                    $shift = $this->db->get_where('shift', ['id' => $ks])->row();
                    $iswhere = ' WHERE (closing.date BETWEEN "' . $a . '" AND "' . $b . '") AND ( shift_id = ' . $shift->id . ' ) AND (closing.status = "CLOSE")';
                    $periode = 'Shift : ' . $shift->nama . ' Periode ' . time_explode_date(htmlentities($this->input->get('a', true)), 'id') . ' s.d. ' . time_explode_date(htmlentities($this->input->get('b', true)), 'id');
                    // $urlexcel = base_url('laporan/excel?shift=' . $ks . '&a=' . $a . '&b=' . $b);
                } else {
                    $iswhere = ' WHERE closing.date BETWEEN "' . $a . '" AND "' . $b . '" AND closing.status = "CLOSE"';
                    $periode = 'Periode ' . time_explode_date(htmlentities($this->input->get('a', true)), 'id') . ' s.d. ' . time_explode_date(htmlentities($this->input->get('b', true)), 'id');
                    // $urlexcel = base_url('laporan/excel?a=' . $a . '&b=' . $b);
                }
            } else {
                if ($this->input->get('shift')) {
                    $ks = $this->input->get('shift');
                    $shift = $this->db->get_where('shift', ['id' => $ks])->row();
                    $iswhere = ' WHERE shift_id = ' . $ks . ' AND periode = "' . date('Y-m') . '" AND closing.status != "CLOSE"';
                    $periode = 'Shift : ' . $shift->nama;
                    $urlexcel = base_url('laporan/excel?shift=' . $ks);
                } else {
                    $iswhere = ' WHERE periode = "' . date('Y-m') . '"';
                    $periode = 'Periode ' . bln('id') . ' ' . date('Y');
                    $urlexcel = base_url('laporan/excel');
                }
            }
        } else {
            if (!empty(htmlentities($this->input->get('a', true)))) {
                $a = htmlentities($this->input->get('a', true));
                $b = htmlentities($this->input->get('b', true));
                if ($this->input->get('shift')) {
                    $ks = $this->input->get('shift');
                    $shift = $this->db->get_where('shift', ['id' => $ks])->row();
                    $iswhere = ' WHERE (kasir_id = ' . $kasir_id . ') AND (closing.date BETWEEN "' . $a . '" AND "' . $b . '") ';
                    $periode = 'Shift : ' . $shift->nama . ' Periode ' . time_explode_date(htmlentities($this->input->get('a', true)), 'id') . ' s.d. ' . time_explode_date(htmlentities($this->input->get('b', true)), 'id');
                    $urlexcel = base_url('laporan/excel?shift=' . $ks . '&a=' . $a . '&b=' . $b);
                } else {
                    $iswhere = ' WHERE (kasir_id = ' . $kasir_id . ') AND closing.date BETWEEN "' . $a . '" AND "' . $b . '"';
                    $periode = 'Periode ' . time_explode_date(htmlentities($this->input->get('a', true)), 'id') . ' s.d. ' . time_explode_date(htmlentities($this->input->get('b', true)), 'id');
                    $urlexcel = base_url('laporan/excel?a=' . $a . '&b=' . $b);
                }
            } else {
                if ($this->input->get('shift')) {
                    $ks = $this->input->get('shift');
                    $shift = $this->db->get_where('shift', ['id' => $ks])->row();
                    $iswhere = ' WHERE (kasir_id = ' . $kasir_id . ') AND shift_id = ' . $ks . ' AND periode = "' . date('Y-m') . '"';
                    $periode = 'Shift : ' . $shift->nama;
                    $urlexcel = base_url('laporan/excel?shift=' . $ks);
                } else {
                    $iswhere = ' WHERE (kasir_id = ' . $kasir_id . ') AND periode = "' . date('Y-m') . '"';
                    // $iswhere = ' WHERE closing_id = 0 AND transaksi.status != "Bayar Nanti" ';
                    $periode = 'Periode ' . bln('id') . ' ' . date('Y');
                    $urlexcel = base_url('laporan/excel');
                }
            }
        }
        // echo 'SELECT SUM(pemasukan) as pm, SUM(pengeluaran) as pg, SUM(sisa_uang) as su FROM closing ' . $iswhere;
        // echo $iswhere;
        $total = $this->db->query('SELECT SUM(pemasukan) as pm, SUM(pengeluaran) as pg, SUM(sisa_uang) as su, SUM(qris) as qr, SUM(online) as ol FROM closing ' . $iswhere)->row();
        $this->data = [
            'title_web' => 'Laporan Penjualan',
            // 'title_web' =>  $iswhere,
            'periode' => $periode,
            'total' => $total,
            'urlexcel' => '' //$urlexcel
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/laporan/penjualan', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function data_penjualan()
    {
        if ($this->input->method(true) == 'POST') :
            $query = "SELECT cabang.nama_cabang,shift.nama,login.nama_user,closing.pemasukan+closing.qris+closing.online as 'total',closing.* FROM closing
            LEFT JOIN cabang ON closing.cabang_id = cabang.id
            LEFT JOIN shift ON closing.shift_id = shift.id
            LEFT JOIN login ON closing.kasir_id = login.id";
            $search = [
                'nama_cabang',
                'nama',
                'nama_user',
                'date',
                'periode',
                'year'
            ];

            // if ($this->session->userdata('ses_level') == 'Admin') {
            if (in_array($this->session->userdata('ses_level'), array('Admin', 'AdminKasir'))) {

                $where = null;
            } else {
                $where = array('closing.kasir_id' => $this->session->userdata('ses_id'));
            }
            // $where = array('transaksi.kasir_id' => $this->session->userdata('ses_id'));
            // }
            $cabang_id = $this->session->userdata('ses_cabang_id');

            // $shift = $this->db->query("SELECT * FROM transaksi WHERE closing_id = 0 AND cabang_id='" . $cabang_id . "' ORDER BY created_at LIMIT 1");

            // if ($shift->num_rows() > 0) {
            //     $ps = $shift->row();
            //     $shift_id = $ps->shift_id;
            // } else {
            //     $shift_id = 0;
            // }
            // if ($this->input->get('kasir')) {
            //     $kasir_id = $this->input->get('kasir');

            if (!empty(htmlentities($this->input->get('a', true)))) {
                $a = htmlentities($this->input->get('a', true));
                $b = htmlentities($this->input->get('b', true));
                // if ($this->input->get('shift')) {
                //     $shift_id = $this->input->get('shift');
                //     $iswhere = 'closing.date BETWEEN "' . $a . '" AND "' . $b . '" AND closing.shift_id = ' . $shift_id;
                // } else {
                $iswhere = 'closing.date BETWEEN "' . $a . '" AND "' . $b . '"';
                // }
            } else {
                // if ($this->input->get('shift')) {
                //     $shift_id = $this->input->get('shift');
                //     $iswhere = 'closing.shift_id = ' . $shift_id . '" AND periode = "' . date('Y-m') . '"';
                // } else {
                $iswhere = 'closing.periode = "' . date('Y-m') . '"';
                // }
            }
            // } else {
            // }

            header('Content-Type: application/json');
            // echo $this->M_Datatables->get_tables_query($query, $search, $where, $iswhere);
            echo $this->M_Datatables->get_tables_query($query, $search, $where, $iswhere);
        endif;
    }

    public function pengeluaran()
    {
        $cabang_id = htmlentities($this->input->get('cabang', true));

        // $cabang_id = $this->session->userdata('ses_cabang_id');
        $level = $this->session->userdata('ses_level');
        $kasir_id = $this->session->userdata('ses_id');
        $iswhere = '';
        $iscabang = '';
        $namacabang = '';

        // if ($level == 'Admin') {
        if (in_array($level, array('Admin', 'AdminKasir'))) {
            if (!empty(htmlentities($this->input->get('cabang', true)))) {
                $cabang_id = htmlentities($this->input->get('cabang', true));
                $iscabang = ' AND cabang_id = ' . $cabang_id;
                $namacabang = $this->db->query('SELECT nama_toko  FROM profil_toko where cabang_id = ' . $cabang_id)->row();
            }

            if (!empty(htmlentities($this->input->get('a', true)))) {
                $a = htmlentities($this->input->get('a', true));
                $b = htmlentities($this->input->get('b', true));
                if ($this->input->get('shift')) {
                    $ks = $this->input->get('shift');
                    $shift = $this->db->get_where('shift', ['id' => $ks])->row();
                    $iswhere = ' WHERE (transaksi_keluar.date BETWEEN "' . $a . '" AND "' . $b . '") AND ( shift_id = ' . $shift->id . ' ) AND (transaksi_keluar.closing_id > 0)';
                    $periode = 'Shift : ' . $shift->nama . ' Periode ' . time_explode_date(htmlentities($this->input->get('a', true)), 'id') . ' s.d. ' . time_explode_date(htmlentities($this->input->get('b', true)), 'id');
                    // $urlexcel = base_url('laporan/excel?shift=' . $ks . '&a=' . $a . '&b=' . $b);
                } else {
                    $iswhere = ' WHERE transaksi_keluar.date BETWEEN "' . $a . '" AND "' . $b . '" AND transaksi_keluar.closing_id > 0';
                    $periode = 'Periode ' . time_explode_date(htmlentities($this->input->get('a', true)), 'id') . ' s.d. ' . time_explode_date(htmlentities($this->input->get('b', true)), 'id');
                    // $urlexcel = base_url('laporan/excel?a=' . $a . '&b=' . $b);
                }
            } else {
                if ($this->input->get('shift')) {
                    $ks = $this->input->get('shift');
                    $shift = $this->db->get_where('shift', ['id' => $ks])->row();
                    $iswhere = ' WHERE shift_id = ' . $ks . ' AND periode = "' . date('Y-m') . '" AND transaksi_keluar.closing_id > 0';
                    $periode = 'Shift : ' . $shift->nama;
                    $urlexcel = base_url('laporan/excel?shift=' . $ks);
                } else {
                    $iswhere = ' WHERE periode = "' . date('Y-m') . '"';
                    $periode = 'Periode ' . bln('id') . ' ' . date('Y');
                    $urlexcel = base_url('laporan/excel');
                }
            }
            $iswhere = $iswhere . $iscabang;
        } else {
            if (!empty(htmlentities($this->input->get('a', true)))) {
                $a = htmlentities($this->input->get('a', true));
                $b = htmlentities($this->input->get('b', true));
                if ($this->input->get('shift')) {
                    $ks = $this->input->get('shift');
                    $shift = $this->db->get_where('shift', ['id' => $ks])->row();
                    $iswhere = ' WHERE (kasir_id = ' . $kasir_id . ') AND (transaksi_keluar.date BETWEEN "' . $a . '" AND "' . $b . '") ';
                    $periode = 'Shift : ' . $shift->nama . ' Periode ' . time_explode_date(htmlentities($this->input->get('a', true)), 'id') . ' s.d. ' . time_explode_date(htmlentities($this->input->get('b', true)), 'id');
                    $urlexcel = base_url('laporan/excel?shift=' . $ks . '&a=' . $a . '&b=' . $b);
                } else {
                    $iswhere = ' WHERE (kasir_id = ' . $kasir_id . ') AND transaksi_keluar.date BETWEEN "' . $a . '" AND "' . $b . '"';
                    $periode = 'Periode ' . time_explode_date(htmlentities($this->input->get('a', true)), 'id') . ' s.d. ' . time_explode_date(htmlentities($this->input->get('b', true)), 'id');
                    $urlexcel = base_url('laporan/excel?a=' . $a . '&b=' . $b);
                }
            } else {
                if ($this->input->get('shift')) {
                    $ks = $this->input->get('shift');
                    $shift = $this->db->get_where('shift', ['id' => $ks])->row();
                    $iswhere = ' WHERE (kasir_id = ' . $kasir_id . ') AND shift_id = ' . $ks . ' AND periode = "' . date('Y-m') . '"';
                    $periode = 'Shift : ' . $shift->nama;
                    $urlexcel = base_url('laporan/excel?shift=' . $ks);
                } else {
                    $iswhere = ' WHERE (kasir_id = ' . $kasir_id . ') AND periode = "' . date('Y-m') . '"';
                    // $iswhere = ' WHERE closing_id = 0 AND transaksi.status != "Bayar Nanti" ';
                    $periode = 'Periode ' . bln('id') . ' ' . date('Y');
                    $urlexcel = base_url('laporan/excel');
                }
            }
        }
        // echo 'SELECT SUM(pemasukan) as pm, SUM(pengeluaran) as pg, SUM(sisa_uang) as su FROM closing ' . $iswhere;
        // echo $iswhere;
        $total = $this->db->query('SELECT SUM(jumlah) as jum FROM transaksi_keluar ' . $iswhere)->row();

        if ($namacabang) {
            $this->data = [
                'title_web' => 'Laporan Pengeluaran',
                'cabangpilih' =>  $namacabang->nama_toko,
                'periode' => $periode,
                'total' => $total,
                'urlexcel' => '' //$urlexcel
            ];
        } else {
            $this->data = [
                'title_web' => 'Laporan Pengeluaran',
                'cabangpilih' =>  '',
                'periode' => $periode,
                'total' => $total,
                'urlexcel' => '' //$urlexcel
            ];
        }

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/laporan/pengeluaran', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function data_pengeluaran()
    {
        if ($this->input->method(true) == 'POST') :
            $query = "SELECT cabang.nama_cabang,shift.nama,login.nama_user,transaksi_keluar.* 
            FROM transaksi_keluar
            LEFT JOIN cabang ON transaksi_keluar.cabang_id = cabang.id
            LEFT JOIN shift ON transaksi_keluar.shift_id = shift.id
            LEFT JOIN login ON transaksi_keluar.kasir_id = login.id";
            $search = [
                'nama_cabang',
                'nama',
                'nama_user',
                'date',
                'periode',
                'year'
            ];

            // if ($this->session->userdata('ses_level') == 'Admin') {
            if (in_array($this->session->userdata('ses_level'), array('Admin', 'AdminKasir'))) {
                if (!empty(htmlentities($this->input->get('cabang', true)))) {
                    $cabang_id = htmlentities($this->input->get('cabang', true));
                    if (!empty(htmlentities($this->input->get('shift', true)))) {
                        $shift_id = htmlentities($this->input->get('shift', true));
                        $where = array('transaksi_keluar.cabang_id' => $cabang_id, 'transaksi_keluar.shift_id' => $shift_id);
                    } else {
                        $where = array('transaksi_keluar.cabang_id' => $cabang_id);
                    }
                } else {
                    $where = null;
                }
            } else {
                $where = array('transaksi_keluar.kasir_id' => $this->session->userdata('ses_id'));
            }

            if (!empty(htmlentities($this->input->get('a', true)))) {
                $a = htmlentities($this->input->get('a', true));
                $b = htmlentities($this->input->get('b', true));
                $iswhere = 'transaksi_keluar.date BETWEEN "' . $a . '" AND "' . $b . '"';
            } else {

                $iswhere = 'transaksi_keluar.periode = "' . date('Y-m') . '"';
            }

            header('Content-Type: application/json');
            echo $this->M_Datatables->get_tables_query($query, $search, $where, $iswhere);
        // $result = $this->M_Datatables->get_tables_query($query, $search, $where, $iswhere);
        // echo "<pre>Last Query:\n" . $this->db->last_query() . "</pre>";
        endif;
    }

    // public function kartustok()
    // {
    //     $periode = '';

    //     $this->data = [
    //         'title_web'  => 'Laporan Kartu Stok',
    //         'periode' => $periode,
    //         // 'cab'       => $this->db->query("SELECT profil_toko.nama_toko,cabang.* FROM cabang LEFT JOIN profil_toko ON profil_toko.cabang_id = cabang.id ORDER BY length(kode_cabang),kode_cabang ASC")->result(),
    //     ];

    //     $this->load->view('layout/header', $this->data);
    //     $this->load->view('admin/laporan/kartustok', $this->data);
    //     $this->load->view('layout/footer', $this->data);
    // }

    public function kartustok()
    {
        $level = $this->session->userdata('ses_level');

        if (in_array($level, array('Admin', 'AdminKasir'))) {
            $cabang_id = htmlentities($this->input->get('cabang', true));
        } else {
            $cabang_id = $this->session->userdata('ses_cabang_id');
            $shift_id = $this->session->userdata('ses_shift');
        }
        $kasir_id = $this->session->userdata('ses_id');
        $iswhere = '';
        $iscabang = '';
        $namacabang = '';

        // if ($level == 'Admin') {
        if (in_array($level, array('Admin', 'AdminKasir'))) {
            if (!empty(htmlentities($this->input->get('cabang', true)))) {
                $cabang_id = htmlentities($this->input->get('cabang', true));
                $iscabang = ' AND cabang_id = ' . $cabang_id;
                $namacabang = $this->db->query('SELECT nama_toko  FROM profil_toko where cabang_id = ' . $cabang_id)->row();
            } else {
                $cabang_id = 1;
                $iscabang = ' AND cabang_id = ' . $cabang_id;
                $namacabang = $this->db->query('SELECT nama_toko  FROM profil_toko where cabang_id = ' . $cabang_id)->row();
            }

            if (!empty(htmlentities($this->input->get('a', true)))) {
                $a = htmlentities($this->input->get('a', true));
                $b = htmlentities($this->input->get('b', true));
                if ($this->input->get('shift')) {
                    $ks = $this->input->get('shift');
                    $shift = $this->db->get_where('shift', ['id' => $ks])->row();
                    $iswhere = ' WHERE (bahan_kartustok.tanggal BETWEEN "' . $a . '" AND "' . $b . '") AND ( shift_id = ' . $shift->id . ' ) ';
                    $periode = 'Shift : ' . $shift->nama . ' Periode ' . time_explode_date(htmlentities($this->input->get('a', true)), 'id') . ' s.d. ' . time_explode_date(htmlentities($this->input->get('b', true)), 'id');
                    // $urlexcel = base_url('laporan/excel?shift=' . $ks . '&a=' . $a . '&b=' . $b);
                } else {
                    $iswhere = ' WHERE bahan_kartustok.tanggal BETWEEN "' . $a . '" AND "' . $b . '" ';
                    $periode = 'Periode ' . time_explode_date(htmlentities($this->input->get('a', true)), 'id') . ' s.d. ' . time_explode_date(htmlentities($this->input->get('b', true)), 'id');
                    // $urlexcel = base_url('laporan/excel?a=' . $a . '&b=' . $b);
                }
            } else {
                if ($this->input->get('shift')) {
                    $ks = $this->input->get('shift');
                    $shift = $this->db->get_where('shift', ['id' => $ks])->row();
                    $iswhere = ' WHERE shift_id = ' . $ks . ' AND periode = "' . date('Y-m') . '" ';
                    $periode = 'Shift : ' . $shift->nama;
                    $urlexcel = base_url('laporan/excel?shift=' . $ks);
                } else {
                    $iswhere = ' WHERE periode = "' . date('Y-m') . '"';
                    $periode = 'Periode ' . bln('id') . ' ' . date('Y');
                    $urlexcel = base_url('laporan/excel');
                }
            }
            $iswhere = $iswhere . $iscabang;
        } else {
            if (!empty(htmlentities($this->input->get('a', true)))) {
                $a = htmlentities($this->input->get('a', true));
                $b = htmlentities($this->input->get('b', true));
                if ($shift_id) {
                    $ks = $shift_id;
                    $shift = $this->db->get_where('shift', ['id' => $ks])->row();
                    $iswhere = ' WHERE (kasir_id = ' . $kasir_id . ') AND (bahan_kartustok.tanggal BETWEEN "' . $a . '" AND "' . $b . '") ';
                    $periode = 'Shift : ' . $shift->nama . ' Periode ' . time_explode_date(htmlentities($this->input->get('a', true)), 'id') . ' s.d. ' . time_explode_date(htmlentities($this->input->get('b', true)), 'id');
                    $urlexcel = base_url('laporan/excel?shift=' . $ks . '&a=' . $a . '&b=' . $b);
                } else {
                    $iswhere = ' WHERE (kasir_id = ' . $kasir_id . ') AND bahan_kartustok.tanggal BETWEEN "' . $a . '" AND "' . $b . '"';
                    $periode = 'Periode ' . time_explode_date(htmlentities($this->input->get('a', true)), 'id') . ' s.d. ' . time_explode_date(htmlentities($this->input->get('b', true)), 'id');
                    $urlexcel = base_url('laporan/excel?a=' . $a . '&b=' . $b);
                }
            } else {
                if ($shift_id) {
                    $ks = $shift_id;
                    $shift = $this->db->get_where('shift', ['id' => $ks])->row();
                    $iswhere = ' WHERE (kasir_id = ' . $kasir_id . ') AND shift_id = ' . $ks . ' AND periode = "' . date('Y-m') . '"';
                    $periode = 'Shift : ' . $shift->nama;
                    $urlexcel = base_url('laporan/excel?shift=' . $ks);
                } else {
                    $iswhere = ' WHERE (kasir_id = ' . $kasir_id . ') AND periode = "' . date('Y-m') . '"';
                    // $iswhere = ' WHERE closing_id = 0 AND transaksi.status != "Bayar Nanti" ';
                    $periode = 'Periode ' . bln('id') . ' ' . date('Y');
                    $urlexcel = base_url('laporan/excel');
                }
            }
        }
        // echo 'SELECT SUM(pemasukan) as pm, SUM(pengeluaran) as pg, SUM(sisa_uang) as su FROM closing ' . $iswhere;
        // echo $iswhere;
        // $total = $this->db->query('SELECT SUM(jumlah) as jum FROM transaksi_keluar ' . $iswhere)->row();
        $total = 0;

        if ($namacabang) {
            $this->data = [
                'title_web' => 'Laporan Kartu Stok',
                'cabangpilih' =>  $namacabang->nama_toko,
                'periode' => $periode,
                'total' => $total,
                'urlexcel' => '' //$urlexcel
            ];
        } else {
            $this->data = [
                'title_web' => 'Laporan Kartu Stok',
                'cabangpilih' =>  '',
                'periode' => $periode,
                'total' => $total,
                'urlexcel' => '' //$urlexcel
            ];
        }

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/laporan/kartustok', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function data_kartustok_tgl()
    {
        $wcabang = "";
        if ($this->input->method(true) == 'POST') :
            $query = "SELECT *, (SELECT kode_cabang from cabang where id = cabang_id) AS cabang FROM (SELECT periode,login_id,shift_id, cabang_id , id,kode_bahan,nama_bahan,SUM(awal) AS awal,SUM(masuk) AS masuk,SUM(keluar) AS keluar,(SUM(awal+masuk-keluar)) AS akhir, tanggal FROM (
     SELECT bk.periode,bk.login_id,bk.shift_id,bk.cabang_id,b.id, b.kode_bahan, b.nama_bahan, 
         0 AS awal,
     IFNULL(IF(bk.jumlah_perubahan>0,bk.jumlah_perubahan,0),0) AS masuk,
     IFNULL(IF(bk.jumlah_perubahan<0,0-bk.jumlah_perubahan,0),0) AS keluar, bk.tanggal AS tanggal
    FROM bahan b 
    LEFT OUTER JOIN bahan_kartustok bk ON bk.bahan_id = b.id ) kartustok
    GROUP BY periode,login_id,shift_id,cabang_id,id,kode_bahan,nama_bahan,tanggal) kartustok2 ";
            $search = [
                'kode_bahan',
                'nama_bahan',
                'tanggal'
            ];

            // if ($this->session->userdata('ses_level') == 'Admin') {
            if (in_array($this->session->userdata('ses_level'), array('Admin', 'AdminKasir'))) {
                if (!empty(htmlentities($this->input->get('cabang', true)))) {
                    $cabang_id = htmlentities($this->input->get('cabang', true));
                    if (!empty(htmlentities($this->input->get('shift', true)))) {
                        $shift_id = htmlentities($this->input->get('shift', true));
                        $where = array('cabang_id' => $cabang_id, 'shift_id' => $shift_id);
                    } else {
                        $where = array('cabang_id' => $cabang_id);
                    }
                } else {
                    $where = null;
                }
            } else {
                $where = array('login_id' => $this->session->userdata('ses_id'));
            }

            if (!empty(htmlentities($this->input->get('a', true)))) {
                $a = htmlentities($this->input->get('a', true));
                $b = htmlentities($this->input->get('b', true));
                $iswhere = 'tanggal BETWEEN "' . $a . '" AND "' . $b . '"';
            } else {
                $iswhere = 'periode = "' . date('Y-m') . '"';
            }

            header('Content-Type: application/json');
            echo $this->M_Datatables->get_tables_query($query, $search, $where, $iswhere);
        // $result = $this->M_Datatables->get_tables_query($query, $search, $where, $iswhere);
        // echo "<pre>Last Query:\n" . $this->db->last_query() . "</pre>";
        endif;
    }

    public function data_kartustok()
    {
        // $suffix = $this->session->userdata('ses_suffix');
        $suffix = '';

        $where = null;
        $iswhere = null;
        $awalwhere = "";
        $tglwhere = "";
        $shift_id = "";
        if (!empty(htmlentities($this->input->get('a', true)))) {
            $a = htmlentities($this->input->get('a', true));
            $b = htmlentities($this->input->get('b', true));
            // $iswhere = 'tanggal BETWEEN "' . $a . '" AND "' . $b . '"';
            $awalwhere = ' bk.tanggal < "' . $a . '"';
            $tglwhere = ' bk.tanggal BETWEEN "' . $a . '" AND "' . $b . '"';
        } else {
            // $iswhere = ' periode = "' . date('Y-m') . '"';
            $awalwhere = ' bk.tanggal < "' . date('Y-m') . '-01"';
            $tglwhere = ' bk.periode = "' . date('Y-m') . '"';
        }

        $andwhere = "";
        if (in_array($this->session->userdata('ses_level'), array('Admin', 'AdminKasir'))) {
            if (!empty(htmlentities($this->input->get('cabang', true)))) {
                $cabang_id = htmlentities($this->input->get('cabang', true));
                $cabang = $this->db->query("select * from cabang where id = " . $cabang_id)->row();
                $kode_cabang = $cabang->kode_cabang;
                $arr_kode_cabang = array("SN1", "SN2", "SN7", "PU");
                if (in_array($kode_cabang, $arr_kode_cabang)) {
                    $suffix = '';
                } else {
                    $suffix = '_' . $kode_cabang;
                }

                if (!empty(htmlentities($this->input->get('shift', true)))) {
                    $shift_id = htmlentities($this->input->get('shift', true));
                    // $where = array('cabang_id' => $cabang_id, 'shift_id' => $shift_id);
                    $andwhere = " AND bk.cabang_id = " . $cabang_id . " AND bk.shift_id = " . $shift_id;
                } else {
                    // $where = array('cabang_id' => $cabang_id);
                    $andwhere = " AND bk.cabang_id = " . $cabang_id;
                }
            } else {
                // $where = null;
                $andwhere = " AND bk.cabang_id = 1";
            }
        } else {
            // $where = array('login_id' => $this->session->userdata('ses_id'));
            $login_id = $this->session->userdata('ses_id');
            $andwhere = " AND bk.login_id = " . $login_id;
        }

        if ($this->input->method(true) == 'POST') :
            $query = "SELECT *, (SELECT kode_cabang from cabang where id = cabang_id) AS cabang, '$shift_id' AS shift_id FROM 
(SELECT 
 		cabang_id , 
 		id, 
 		kode_bahan,
 		nama_bahan,
 		SUM(awal) AS awal,
 		SUM(masuk) AS masuk,
 		SUM(keluar) AS keluar,
 		(SUM(awal)+SUM(masuk)-SUM(keluar)) AS akhir
 FROM (
    SELECT 
     		bk.cabang_id,
     		b.id, 
     		b.kode_bahan, 
     		b.nama_bahan, 
         	SUM(bk.jumlah_perubahan) AS awal,
     		0 AS masuk,
     		0 AS keluar
    		FROM bahan b 
    		LEFT OUTER JOIN bahan_kartustok" . $suffix . " AS bk ON bk.bahan_id = b.id 
 			WHERE $awalwhere 
 			$andwhere
     GROUP BY cabang_id,id,kode_bahan,nama_bahan
     UNION ALL
     SELECT 
     		bk.cabang_id,
     		b.id, 
     		b.kode_bahan, 
     		b.nama_bahan, 
         	0 AS awal,
     		IFNULL(IF(bk.jumlah_perubahan>0,bk.jumlah_perubahan,0),0) AS masuk,
     		IFNULL(IF(bk.jumlah_perubahan<0,ABS(bk.jumlah_perubahan),0),0) AS keluar
    		FROM bahan b 
    		LEFT OUTER JOIN bahan_kartustok" . $suffix . " AS bk ON bk.bahan_id = b.id 
 			WHERE $tglwhere
 			$andwhere
 ) stok_perubahan
 
    		GROUP BY cabang_id,id,kode_bahan,nama_bahan) kartustok ";
            //         $query = "SELECT *, (SELECT kode_cabang from cabang where id = cabang_id) AS cabang FROM (SELECT periode,login_id,shift_id, cabang_id , id,kode_bahan,nama_bahan,SUM(awal) AS awal,SUM(masuk) AS masuk,SUM(keluar) AS keluar,(SUM(awal+masuk-keluar)) AS akhir, tanggal FROM (
            //  SELECT bk.periode,bk.login_id,bk.shift_id,bk.cabang_id,b.id, b.kode_bahan, b.nama_bahan, 
            //      0 AS awal,
            //  IFNULL(IF(bk.jumlah_perubahan>0,bk.jumlah_perubahan,0),0) AS masuk,
            //  IFNULL(IF(bk.jumlah_perubahan<0,0-bk.jumlah_perubahan,0),0) AS keluar, bk.tanggal AS tanggal
            // FROM bahan b 
            // LEFT OUTER JOIN bahan_kartustok bk ON bk.bahan_id = b.id ) kartustok
            // GROUP BY periode,login_id,shift_id,cabang_id,id,kode_bahan,nama_bahan,tanggal) kartustok2 ";
            $search = [
                'kode_bahan',
                'nama_bahan'
            ];
            // $search = [];

            // if ($this->session->userdata('ses_level') == 'Admin') {

            header('Content-Type: application/json');
            echo $this->M_Datatables->get_tables_query($query, $search, $where, $iswhere);
        // $result = $this->M_Datatables->get_tables_query($query, $search, $where, $iswhere);
        // echo "<pre>Last Query:\n" . $query . "</pre>";
        endif;
    }

    public function kartustok_cabang()
    {
        $level = $this->session->userdata('ses_level');

        if (in_array($level, array('Admin', 'AdminKasir'))) {
            $cabang_id = htmlentities($this->input->get('cabang', true));
        } else {
            $cabang_id = $this->session->userdata('ses_cabang_id');
        }
        $iswhere = '';
        $iscabang = '';
        $namacabang = '';

        // if ($level == 'Admin') {
        if (in_array($level, array('Admin', 'AdminKasir'))) {
            if (!empty(htmlentities($this->input->get('cabang', true)))) {
                $cabang_id = htmlentities($this->input->get('cabang', true));
                $iscabang = ' AND cabang_id = ' . $cabang_id;
                $namacabang = $this->db->query('SELECT nama_toko  FROM profil_toko where cabang_id = ' . $cabang_id)->row();
            }

            if (!empty(htmlentities($this->input->get('a', true)))) {
                $a = htmlentities($this->input->get('a', true));
                $b = htmlentities($this->input->get('b', true));
                if ($this->input->get('shift')) {
                    // $ks = $this->input->get('shift');
                    // $shift = $this->db->get_where('shift', ['id' => $ks])->row();
                    // $iswhere = ' WHERE (bahan_kartustok.tanggal BETWEEN "' . $a . '" AND "' . $b . '") AND ( shift_id = ' . $shift->id . ' ) ';
                    // $periode = 'Shift : ' . $shift->nama . ' Periode ' . time_explode_date(htmlentities($this->input->get('a', true)), 'id') . ' s.d. ' . time_explode_date(htmlentities($this->input->get('b', true)), 'id');
                    // $urlexcel = base_url('laporan/excel?shift=' . $ks . '&a=' . $a . '&b=' . $b);
                } else {
                    $iswhere = ' WHERE bahan_kartustok.tanggal BETWEEN "' . $a . '" AND "' . $b . '" ';
                    $periode = 'Periode ' . time_explode_date(htmlentities($this->input->get('a', true)), 'id') . ' s.d. ' . time_explode_date(htmlentities($this->input->get('b', true)), 'id');
                    // $urlexcel = base_url('laporan/excel?a=' . $a . '&b=' . $b);
                }
            } else {
                if ($this->input->get('shift')) {
                    // $ks = $this->input->get('shift');
                    // $shift = $this->db->get_where('shift', ['id' => $ks])->row();
                    // $iswhere = ' WHERE shift_id = ' . $ks . ' AND periode = "' . date('Y-m') . '" ';
                    // $periode = 'Shift : ' . $shift->nama;
                    // $urlexcel = base_url('laporan/excel?shift=' . $ks);
                } else {
                    $iswhere = ' WHERE periode = "' . date('Y-m') . '"';
                    $periode = 'Periode ' . bln('id') . ' ' . date('Y');
                    $urlexcel = base_url('laporan/excel');
                }
            }
            $iswhere = $iswhere . $iscabang;
        } else {
            if (!empty(htmlentities($this->input->get('a', true)))) {
                $a = htmlentities($this->input->get('a', true));
                $b = htmlentities($this->input->get('b', true));
                // if ($shift_id) {
                // $ks = $shift_id;
                // $shift = $this->db->get_where('shift', ['id' => $ks])->row();
                // $iswhere = ' WHERE (kasir_id = ' . $kasir_id . ') AND (bahan_kartustok.tanggal BETWEEN "' . $a . '" AND "' . $b . '") ';
                // $periode = 'Shift : ' . $shift->nama . ' Periode ' . time_explode_date(htmlentities($this->input->get('a', true)), 'id') . ' s.d. ' . time_explode_date(htmlentities($this->input->get('b', true)), 'id');
                // $urlexcel = base_url('laporan/excel?shift=' . $ks . '&a=' . $a . '&b=' . $b);
                // } else {
                $iswhere = ' WHERE bahan_kartustok.tanggal BETWEEN "' . $a . '" AND "' . $b . '"';
                $periode = 'Periode ' . time_explode_date(htmlentities($this->input->get('a', true)), 'id') . ' s.d. ' . time_explode_date(htmlentities($this->input->get('b', true)), 'id');
                $urlexcel = base_url('laporan/excel?a=' . $a . '&b=' . $b);
                // }
            } else {
                // if ($shift_id) {
                //     $ks = $shift_id;
                //     $shift = $this->db->get_where('shift', ['id' => $ks])->row();
                //     $iswhere = ' WHERE (kasir_id = ' . $kasir_id . ') AND shift_id = ' . $ks . ' AND periode = "' . date('Y-m') . '"';
                //     $periode = 'Shift : ' . $shift->nama;
                //     $urlexcel = base_url('laporan/excel?shift=' . $ks);
                // } else {
                $iswhere = ' WHERE periode = "' . date('Y-m') . '"';
                // $iswhere = ' WHERE closing_id = 0 AND transaksi.status != "Bayar Nanti" ';
                $periode = 'Periode ' . bln('id') . ' ' . date('Y');
                $urlexcel = base_url('laporan/excel');
                // }
            }
        }
        // echo 'SELECT SUM(pemasukan) as pm, SUM(pengeluaran) as pg, SUM(sisa_uang) as su FROM closing ' . $iswhere;
        // echo $iswhere;
        // $total = $this->db->query('SELECT SUM(jumlah) as jum FROM transaksi_keluar ' . $iswhere)->row();
        $total = 0;

        if ($namacabang) {
            $this->data = [
                'title_web' => 'Laporan Kartu Stok',
                'cabangpilih' =>  $namacabang->nama_toko,
                'periode' => $periode,
                'total' => $total,
                'urlexcel' => '' //$urlexcel
            ];
        } else {
            $this->data = [
                'title_web' => 'Laporan Kartu Stok',
                'cabangpilih' =>  '',
                'periode' => $periode,
                'total' => $total,
                'urlexcel' => '' //$urlexcel
            ];
        }

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/laporan/kartustok_cabang', $this->data);
        $this->load->view('layout/footer', $this->data);
    }
}
