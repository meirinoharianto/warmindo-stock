<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Login extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        //validasi jika user belum login
        $this->data['CI'] = &get_instance();
        $this->load->helper(array('form', 'url'));
        $this->load->model('M_login');
        $this->load->helper('tgl_default');
        $this->load->helper('alert');
    }

    public function index()
    {

        // $_IP_ADDRESS = $_SERVER['REMOTE_ADDR'];

        // echo BRANCHNAME . " IP Anda : " . $_IP_ADDRESS;
        // echo "'select * from cabang where nama_cabang = '" . BRANCHNAME . "'";
        // $macAddr = false;
        // $arp = `arp -n`;
        // $lines = explode("\n", $arp);

        // foreach ($lines as $line) {
        //     $cols = preg_split('/\s+/', trim($line));

        //     if ($cols[0] == $_SERVER['REMOTE_ADDR']) {
        //         $macAddr = $cols[2];
        //     }
        // }

        // echo " MAC 2 Anda : " . $macAddr;

        if ($this->session->userdata('masuk_sistem') == true) {
            $url = base_url('home');
            redirect($url);
        }

        $this->data['title_web'] = 'Login';
        $this->load->view('login/index', $this->data);
    }

    public function proses()
    {
        $shift_nilai = $this->db->query("SELECT MAX(open) as 'nilai_max', MIN(close) as 'nilai_min' FROM shift")->row();
        $nilai_max = $shift_nilai->nilai_max;
        $nilai_min = $shift_nilai->nilai_min;
        $shift_now = $this->db->query("SELECT * FROM shift WHERE ('" . date('H:i') . "' BETWEEN open AND close) OR 
           ((open>='" . $nilai_max . "' AND '" . date('H:i') . "' BETWEEN '" . $nilai_max . "' AND '24:00') OR 
           (close<='" . $nilai_min . "' AND '" . date('H:i') . "' BETWEEN '00:00' AND '" . $nilai_min . "')) ")->row();

        $user = htmlspecialchars($this->input->post('user', true), ENT_QUOTES);
        $pass = htmlspecialchars($this->input->post('pass', true), ENT_QUOTES);
        // auth
        $proses_login = $this->db->query("SELECT * FROM login WHERE user = ?", array($user));
        $row = $proses_login->num_rows();
        if ($row > 0) {
            $hasil_login = $proses_login->row_array();
            if (password_verify($pass, $hasil_login['pass'])) {
                if ($hasil_login['level'] == 'Kasir') {

                    if ($hasil_login['shift_id'] <> $shift_now->id) {

                        $shift_user = $this->db->query("SELECT * FROM shift WHERE id = " . $hasil_login['shift_id'])->row();
                        if (date("H", strtotime($shift_user->close)) == date("H", strtotime($shift_now->open))) {
                            if (date("H:i") < date("H:i", strtotime($shift_now->open) + 1800) || date("H:i") > date("H:i", strtotime($shift_now->open))) {
                            } else {
                                $this->session->set_flashdata('failed', '<strong>Login Gagal,</strong> Anda tidak diperkenankan login !');
                                // $this->session->sess_destroy();
                                redirect(base_url('login'));
                            }
                        } else {
                            $this->session->set_flashdata('failed', '<strong>Login Gagal,</strong> Anda tidak diperkenankan login !');
                            // $this->session->sess_destroy();
                            redirect(base_url('login'));
                        }

                        $this->session->set_flashdata('failed', '<strong>Login Gagal,</strong> Anda tidak diperkenankan login !');
                        // $this->session->sess_destroy();
                        redirect(base_url('login'));
                    }
                }
                // create session
                // print_r($hasil_login);
                $this->session->set_userdata('masuk_sistem', true);
                $this->session->set_userdata('ses_id', $hasil_login['id']);
                $this->session->set_userdata('ses_user', $hasil_login['user']);
                $this->session->set_userdata('ses_nama', $hasil_login['nama_user']);
                $this->session->set_userdata('ses_level', $hasil_login['level']);
                $this->session->set_userdata('ses_shift', $hasil_login['shift_id']);
                // $shift = $this->db->query("SELECT * FROM shift WHERE OPEN <= '" . date('H:i') . "' AND CLOSE >= '" . date('H:i') . "'")->row();
                // if (isset($shift)) {
                //     $this->session->set_userdata('ses_shift', $shift->id);
                // }
                // $closing = $this->db->query('select * from closing where status = "OPEN"')->row();
                // if (isset($closing)) {
                //     $this->session->set_userdata('ses_shift', $closing->shift_id);
                //     $this->session->set_userdata('ses_opening', $closing->id);
                // }
                // $cabang = $this->db->query("select * from cabang where nama_cabang = '" . BRANCHNAME . "'")->row();
                $cabang = $this->db->query("select * from cabang where id = " . $hasil_login['cabang_id'])->row();
                if (isset($cabang)) {
                    $this->session->set_userdata('ses_cabang_id', $cabang->id);
                    $this->session->set_userdata('ses_kode_cabang', $cabang->kode_cabang);
                    $profil_toko = $this->db->get_where('profil_toko', ['cabang_id' => $cabang->id])->row();
                    $this->session->set_userdata('ses_nama_toko', $profil_toko->nama_toko);
                }

                $this->session->set_flashdata('success', '<strong>Hai ' . $hasil_login['nama_user'] . '!</strong> Selamat datang Kembali ..');
                redirect(base_url('home'));
            } else {
                $this->session->set_flashdata('failed', '<strong>Login Gagal,</strong> Periksa Kembali Password Anda !');
                // $this->session->sess_destroy();
                redirect(base_url('login'));
            }
        } else {
            $this->session->set_flashdata('failed', '<strong>Login Gagal,</strong> Periksa Kembali Username dan Password Anda !');
            // $this->session->sess_destroy();
            redirect(base_url('login'));
        }
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect(base_url('login'));
    }
}
