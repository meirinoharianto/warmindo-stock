<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Transaksi extends CI_Controller
{

    public function index()
    {
        $this->data = [
            'title_web' => 'Dashboard',
            'userx'     => $this->db->get_where('login', ['id' => $this->session->userdata('ses_id')])->row(),
            'ck'        => $this->db->get('kategori')->num_rows(),
            'cm'        => $this->db->get_where('menu', ['cabang_id' => $this->session->userdata('ses_cabang_id')])->num_rows(),
            'cc'        => $this->db->get('customer')->num_rows(),
            // 'ct'        => $trx,
        ];

        $this->load->view('layout/header', $this->data);
        $this->load->view('admin/transaksi/index', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function cetak_struk()
    {
        // me-load library escpos
        $this->load->library('escpos');

        // membuat connector printer ke shared printer bernama "printer_a" (yang telah disetting sebelumnya)
        $connector = new Escpos\PrintConnectors\WindowsPrintConnector("printer_a");

        // membuat objek $printer agar dapat di lakukan fungsinya
        $printer = new Escpos\Printer($connector);


        /* ---------------------------------------------------------
     * Teks biasa | text()
     */
        $printer->initialize();
        $printer->text("Ini teks biasa \n");
        $printer->text("\n");

        /* ---------------------------------------------------------
     * Select print mode | selectPrintMode()
     */
        // Printer::MODE_FONT_A
        $printer->initialize();
        $printer->selectPrintMode(Escpos\Printer::MODE_FONT_A);
        $printer->text("teks dengan MODE_FONT_A \n");
        $printer->text("\n");

        // Printer::MODE_FONT_B
        $printer->initialize();
        $printer->selectPrintMode(Escpos\Printer::MODE_FONT_B);
        $printer->text("teks dengan MODE_FONT_B \n");
        $printer->text("\n");

        // Printer::MODE_EMPHASIZED
        $printer->initialize();
        $printer->selectPrintMode(Escpos\Printer::MODE_EMPHASIZED);
        $printer->text("teks dengan MODE_EMPHASIZED \n");
        $printer->text("\n");

        // Printer::MODE_DOUBLE_HEIGHT
        $printer->initialize();
        $printer->selectPrintMode(Escpos\Printer::MODE_DOUBLE_HEIGHT);
        $printer->text("teks dengan MODE_DOUBLE_HEIGHT \n");
        $printer->text("\n");

        // Printer::MODE_DOUBLE_WIDTH
        $printer->initialize();
        $printer->selectPrintMode(Escpos\Printer::MODE_DOUBLE_WIDTH);
        $printer->text("teks dengan MODE_DOUBLE_WIDTH \n");
        $printer->text("\n");

        // Printer::MODE_UNDERLINE
        $printer->initialize();
        $printer->selectPrintMode(Escpos\Printer::MODE_UNDERLINE);
        $printer->text("teks dengan MODE_UNDERLINE \n");
        $printer->text("\n");


        /* ---------------------------------------------------------
     * Teks dengan garis bawah  | setUnderline()
     */
        $printer->initialize();
        $printer->setUnderline(Escpos\Printer::UNDERLINE_DOUBLE);
        $printer->text("Ini teks dengan garis bawah \n");
        $printer->text("\n");

        /* ---------------------------------------------------------
     * Rata kiri, tengah, dan kanan (JUSTIFICATION) | setJustification()
     */
        // Teks rata kiri JUSTIFY_LEFT
        $printer->initialize();
        $printer->setJustification(Escpos\Printer::JUSTIFY_LEFT);
        $printer->text("Ini teks rata kiri \n");
        $printer->text("\n");

        // Teks rata tengah JUSTIFY_CENTER
        $printer->initialize();
        $printer->setJustification(Escpos\Printer::JUSTIFY_CENTER);
        $printer->text("Ini teks rata tengah \n");
        $printer->text("\n");

        // Teks rata kanan JUSTIFY_RIGHT
        $printer->initialize();
        $printer->setJustification(Escpos\Printer::JUSTIFY_RIGHT);
        $printer->text("Ini teks rata kanan \n");
        $printer->text("\n");


        /* ---------------------------------------------------------
     * Font A, B dan C | setFont()
     */
        // Teks dengan font A
        $printer->initialize();
        $printer->setFont(Escpos\Printer::FONT_A);
        $printer->text("Ini teks dengan font A \n");
        $printer->text("\n");

        // Teks dengan font B
        $printer->initialize();
        $printer->setFont(Escpos\Printer::FONT_B);
        $printer->text("Ini teks dengan font B \n");
        $printer->text("\n");

        // Teks dengan font C
        $printer->initialize();
        $printer->setFont(Escpos\Printer::FONT_C);
        $printer->text("Ini teks dengan font C \n");
        $printer->text("\n");

        /* ---------------------------------------------------------
     * Jarak perbaris 40 (linespace) | setLineSpacing()
     */
        $printer->initialize();
        $printer->setLineSpacing(40);
        $printer->text("Ini paragraf dengan \nline spacing sebesar 40 \ndi printer dotmatrix \n");
        $printer->text("\n");

        /* ---------------------------------------------------------
     * Jarak dari kiri (Margin Left) | setPrintLeftMargin()
     */
        $printer->initialize();
        $printer->setPrintLeftMargin(10);
        $printer->text("Ini teks berjarak 10 dari kiri (Margin left) \n");
        $printer->text("\n");

        /* ---------------------------------------------------------
     * membalik warna teks (background menjadi hitam) | setReverseColors()
     */
        $printer->initialize();
        $printer->setReverseColors(TRUE);
        $printer->text("Warna Teks ini terbalik \n");
        $printer->text("\n");


        /* ---------------------------------------------------------
     * Menyelesaikan printer
     */
        $printer->feed(4); // mencetak 2 baris kosong, agar kertas terangkat ke atas
        $printer->close();
    }
}

/* End of file Transaksi.php */
/* Location: ./application/controllers/Transaksi.php */
