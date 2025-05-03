-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 24, 2023 at 06:24 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `warmindo`
--

-- --------------------------------------------------------

--
-- Table structure for table `bahanbaku`
--

CREATE TABLE `bahanbaku` (
  `id` int(11) NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `kode_bahanbaku` varchar(255) DEFAULT NULL,
  `nama` varchar(255) DEFAULT NULL,
  `harga_pokok` int(11) NOT NULL,
  `stok` int(11) NOT NULL,
  `stok_minim` int(11) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `cabang`
--

CREATE TABLE `cabang` (
  `id` int(11) NOT NULL,
  `nama_cabang` varchar(255) DEFAULT NULL,
  `kode_cabang` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `cabang`
--

INSERT INTO `cabang` (`id`, `nama_cabang`, `kode_cabang`) VALUES
(1, 'PUSAT', 'PU'),
(2, 'warmindo-samndut3', 'SN3'),
(3, 'warmindo-samndut2', 'SN2'),
(4, 'warmindo-samndut1', 'SN1');

-- --------------------------------------------------------

--
-- Table structure for table `closing`
--

CREATE TABLE `closing` (
  `id` int(11) NOT NULL,
  `kasir_id` int(11) NOT NULL,
  `shift_id` int(11) NOT NULL,
  `status` varchar(255) DEFAULT NULL,
  `saldo_awal` int(11) NOT NULL,
  `pemasukan` int(11) NOT NULL,
  `pengeluaran` int(11) NOT NULL,
  `sisa_uang` int(11) NOT NULL,
  `total_qty_titipan` int(11) NOT NULL,
  `total_uang_titipan` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `date` date DEFAULT NULL,
  `periode` varchar(255) DEFAULT NULL,
  `year` varchar(255) DEFAULT NULL,
  `cabang_id` int(11) NOT NULL,
  `qris` int(11) NOT NULL,
  `online` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `closing`
--

INSERT INTO `closing` (`id`, `kasir_id`, `shift_id`, `status`, `saldo_awal`, `pemasukan`, `pengeluaran`, `sisa_uang`, `total_qty_titipan`, `total_uang_titipan`, `created_at`, `date`, `periode`, `year`, `cabang_id`, `qris`, `online`) VALUES
(1, 6, 3, 'CLOSE', 0, 16000, 30000, -14000, 0, 0, '2023-06-23 14:03:44', '2023-06-20', '2023-06', '2023', 2, 24000, 8000);

-- --------------------------------------------------------

--
-- Table structure for table `closing_detail`
--

CREATE TABLE `closing_detail` (
  `id` int(11) NOT NULL,
  `no_closing` varchar(255) NOT NULL,
  `nama_field_int` varchar(255) NOT NULL,
  `value_int` int(11) NOT NULL,
  `nama_field_var` varchar(255) NOT NULL,
  `value_var` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `id` int(11) NOT NULL,
  `kode_customer` varchar(255) DEFAULT NULL,
  `nama` varchar(255) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `hp` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id` int(11) NOT NULL,
  `kategori` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id`, `kategori`) VALUES
(1, 'Makanan'),
(2, 'Minuman'),
(3, 'Snack'),
(4, 'Rokok');

-- --------------------------------------------------------

--
-- Table structure for table `keranjang`
--

CREATE TABLE `keranjang` (
  `id` int(11) NOT NULL,
  `id_menu` int(11) NOT NULL,
  `kode_menu` varchar(255) DEFAULT NULL,
  `kategori` varchar(255) DEFAULT NULL,
  `nama` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `qty` int(11) NOT NULL,
  `harga_beli` int(11) NOT NULL,
  `harga_jual` int(11) NOT NULL,
  `login_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `keuangan_lainnya`
--

CREATE TABLE `keuangan_lainnya` (
  `id` int(11) NOT NULL,
  `no_ledger` varchar(255) DEFAULT NULL,
  `nama_urusan` varchar(255) DEFAULT NULL,
  `jenis` varchar(255) DEFAULT NULL,
  `jumlah_masuk` int(11) NOT NULL,
  `jumlah_keluar` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `date` date DEFAULT NULL,
  `periode` varchar(255) DEFAULT NULL,
  `year` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `shift_id` int(11) NOT NULL,
  `cabang_id` int(11) NOT NULL,
  `closing_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `keuangan_ledger`
--

CREATE TABLE `keuangan_ledger` (
  `id` int(11) NOT NULL,
  `no_ledger` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `jenis` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `keuangan_ledger`
--

INSERT INTO `keuangan_ledger` (`id`, `no_ledger`, `keterangan`, `jenis`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, '5.3', 'Pengeluaran Cabang Sam Ndut 3', 'Pengeluaran', '2023-06-01 19:18:40', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `id` int(11) NOT NULL,
  `user` varchar(255) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `nama_user` varchar(255) NOT NULL,
  `alamat` text NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `telepon` varchar(255) NOT NULL,
  `foto` text NOT NULL,
  `level` varchar(255) DEFAULT NULL,
  `tgl_bergabung` varchar(255) NOT NULL,
  `deleted_at` varchar(255) DEFAULT NULL,
  `cabang_id` int(11) NOT NULL,
  `shift_id` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`id`, `user`, `pass`, `nama_user`, `alamat`, `email`, `telepon`, `foto`, `level`, `tgl_bergabung`, `deleted_at`, `cabang_id`, `shift_id`) VALUES
(5, 'adminkasir', '$2y$10$x8qb6gUG/CK/LCn7nPhyj.eImVZrGVOz8tjmPgGZAq8UYVEeDj7ki', 'Admin Toko', 'Bekasi', 'dummy@gmail.com', '081234567890', 'user_1636966029.png', 'Admin', '2019-09-11', '2021-07-27 12:25:48', 1, 0),
(6, 'kasirsn3', '$2y$10$uNlAMbahg0wOTdXIMuNjjO7V6qzOgwk1riqWQ7UAqMrWfYYIfdpCW', 'Kasir SN3', 'Malang', 'dummy2@gmail.com', '081234567890', 'user_1689448337.jpg', 'Kasir', '2021-10-04', NULL, 2, 1),
(8, 'kasirsn2', '$2y$10$rnmFYQ4R1fqUWjuaP3VuA.swJW.s9BuFTi2icK3.qofPfYuzFuU0S', 'Kasir SN2', 'Malang', 'salasatekno@gmail.com', '123456781231', 'user_1687261259.png', 'Kasir', '2023-06-20', NULL, 3, 1),
(9, 'kasirsn1s1', '$2y$10$x8qb6gUG/CK/LCn7nPhyj.eImVZrGVOz8tjmPgGZAq8UYVEeDj7ki', 'Kasir SN1 Shift 1', 'Malang', 'salasatekno@gmail.com', '123456781231', 'user_1687261326.png', 'Kasir', '2023-06-20', NULL, 4, 1),
(10, 'kasirsn1s2', '$2y$10$6cQ94GjtXa0HWD3mk4hGqO2irxwlgqBYW1nDtFtvQ/WAwgVApNzaC', 'Kasir SN1 Shift 2', 'Alamat1', 'salasatekno@gmail.com', '123456781231', '-', 'Kasir', '2023-07-16', NULL, 4, 2),
(11, 'kasirsn1s3', '$2y$10$7j6brzAbVp2WNC6mT3hqUOpO3yDE3txQWImpzL2MSUhNtY5yU6/Xi', 'Kasir SN1 Shift 3', 'Alamat1', 'salasatekno@gmail.com', '123456781231', '-', 'Kasir', '2023-07-16', NULL, 4, 3);

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id` int(11) NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `kode_menu` varchar(255) DEFAULT NULL,
  `nama` varchar(255) DEFAULT NULL,
  `harga_pokok` int(11) NOT NULL,
  `harga_jual` int(11) NOT NULL,
  `stok` int(11) NOT NULL,
  `stok_minim` int(11) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `harga_sedang` int(11) NOT NULL,
  `harga_jumbo` int(11) NOT NULL,
  `cabang_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id`, `id_kategori`, `kode_menu`, `nama`, `harga_pokok`, `harga_jual`, `stok`, `stok_minim`, `keterangan`, `gambar`, `created_at`, `harga_sedang`, `harga_jumbo`, `cabang_id`) VALUES
(1, 1, 'MEN000001', 'INDOMIE NYEMEK BRUTAL', 8000, 13000, 8, 3, 'SUPER PEDAS', '-', '2021-12-16 12:34:06', 0, 0, 2),
(3, 1, 'MEN000003', 'PAKET SAMNDUT (NYEMEK + ES TEH JUMBO)', 2000, 15000, 1, 1, 'PAKET SUPER PEDAS', '-', '2022-02-13 21:03:05', 0, 0, 2),
(5, 1, 'MEN000005', 'INDOMIE GORENG', 5, 5, 1, 3, 'KERING', '-', '2023-02-17 08:04:27', 0, 0, 2),
(6, 1, 'MEN000007', 'INDOMIE GEPREK', 7, 8000, 0, 7, '7', '-', '2023-02-17 08:05:12', 0, 0, 2),
(7, 1, 'MEN000008', 'INDOMIE RENDANG', 8, 8000, 9, 3, 'KERING PEDAS', '-', '2023-02-17 08:06:07', 0, 0, 2),
(8, 1, 'MEN000009', 'INDOMIE CABE HIJAU', 9, 8000, 3, 9, 'KERING', '-', '2023-02-17 08:06:20', 0, 0, 2),
(9, 1, 'MEN000010', 'INDOMIE GORENG ACEH', 10, 8000, 0, 10, 'KERING PEDAS', '-', '2023-02-17 08:06:36', 0, 0, 2),
(10, 1, '1677588416', 'INDOMIE AYAM SPESIAL', 0, 8000, 1, 2, NULL, '-', '2023-02-28 19:46:56', 0, 0, 2),
(11, 1, 'MEN000011', 'INDOMIE AYAM BAWANG', 0, 8000, 3, 2, NULL, '-', '2023-02-28 19:46:56', 0, 0, 2),
(12, 1, 'MEN000012', 'INDOMIE AYAM POP', 0, 8000, 9, 2, NULL, '-', '2023-02-28 19:46:56', 0, 0, 2),
(13, 1, 'MEN000013', 'INDOMIE KARE AYAM', 0, 8000, 7, 2, NULL, '-', '2023-02-28 19:46:56', 0, 0, 2),
(14, 1, 'MEN000014', 'INDOMIE SEBLAK', 0, 8000, 4, 2, NULL, '-', '2023-02-28 19:46:56', 0, 0, 2),
(15, 1, 'MEN000015', 'INDOMIE SOTO SPESIAL', 0, 8000, 10, 2, NULL, '-', '2023-02-28 19:46:56', 0, 0, 2),
(16, 1, 'MEN000016', 'INDOMIE SOTO BANJAR', 0, 8000, 10, 2, NULL, '-', '2023-02-28 19:46:56', 0, 0, 2),
(17, 1, 'MEN000017', 'TELOR', 0, 4000, 10, 2, NULL, '-', '2023-02-28 19:46:56', 0, 0, 2),
(18, 1, 'MEN000018', 'SOSIS', 0, 2500, 10, 2, NULL, '-', '2023-02-28 19:46:56', 0, 0, 2),
(19, 1, 'MEN000019', 'EXTRA CABE RAWIT', 0, 1000, 10, 2, NULL, '-', '2023-02-28 19:46:56', 0, 0, 2),
(20, 2, 'MEN000021', 'TEH KAHURIPAN', 2000, 3000, 10, 2, NULL, '-', '2023-02-28 20:08:38', 4000, 5000, 2),
(21, 2, 'MEN000022', 'TEH SUSU', 2000, 6000, 9, 2, '', '-', '2023-02-28 20:08:38', 7000, 8000, 2),
(22, 2, 'MEN000023', 'ENERGEN', 2000, 5000, 9, 2, NULL, '-', '2023-02-28 20:08:38', 0, 0, 2),
(23, 2, 'MEN000024', 'GOOD DAY', 2000, 5000, 10, 2, NULL, '-', '2023-02-28 20:08:38', 6000, 7000, 2),
(24, 2, 'MEN000025', 'DRINK BENG-BENG', 2000, 5000, 10, 2, NULL, '-', '2023-02-28 20:08:38', 6000, 7000, 2),
(25, 2, 'MEN000026', 'MAX TEA TARIK', 2000, 5000, 10, 2, NULL, '-', '2023-02-28 20:08:38', 6000, 7000, 2),
(26, 4, 'MEN000027', 'HILO', 2000, 5000, 10, 2, NULL, '-', '2023-02-28 20:08:38', 6000, 7000, 2),
(27, 3, 'MEN000028', 'MILO', 2000, 5000, 10, 2, NULL, '-', '2023-02-28 20:08:38', 6000, 7000, 2),
(28, 2, 'MEN000029', 'CHOCOLATOS', 2000, 5000, 10, 2, NULL, '-', '2023-02-28 20:08:38', 6000, 7000, 2),
(29, 2, 'MEN000030', 'NUTRISARI', 2000, 4000, 10, 2, NULL, '-', '2023-02-28 20:08:38', 5000, 6000, 2),
(30, 2, 'MEN000031', 'TORA BIKA', 2000, 4000, 10, 2, NULL, '-', '2023-02-28 20:08:38', 5000, 6000, 2),
(31, 2, 'MEN000032', 'KOPI ABC', 2000, 4000, 10, 2, NULL, '-', '2023-02-28 20:08:38', 5000, 6000, 2),
(32, 2, 'MEN000033', 'KAPAL API', 2000, 4000, 10, 2, NULL, '-', '2023-02-28 20:08:38', 5000, 0, 2),
(33, 2, 'MEN000034', 'INDOMILK COKLAT / PUTIH', 2000, 4000, 10, 2, NULL, '-', '2023-02-28 20:08:38', 5000, 6000, 2),
(34, 2, 'MEN000035', 'POP ICE', 2000, 0, 10, 2, NULL, '-', '2023-02-28 20:08:38', 4000, 6000, 2),
(35, 2, 'MEN000036', 'SODA GEMBIRA', 2000, 0, 10, 2, NULL, '-', '2023-02-28 20:08:38', 0, 10000, 2),
(36, 2, 'MEN000037', 'EXTRA/ KUBI SUSU', 2000, 0, 10, 2, NULL, '-', '2023-02-28 20:08:38', 7000, 8000, 2),
(37, 2, 'MEN000038', 'ADEM SARI', 2000, 0, 10, 2, NULL, '-', '2023-02-28 20:08:38', 5000, 0, 2),
(38, 2, 'MEN000039', 'EXTRA JOSS / KUKU BIMA', 2000, 0, 10, 2, NULL, '-', '2023-02-28 20:08:38', 5000, 6000, 2),
(39, 2, 'MEN000040', 'KOPI HITAM RACIK', 2000, 5000, 9, 2, NULL, '-', '2023-02-28 20:08:38', 6000, 0, 2),
(40, 2, 'MEN000041', 'KOPI SUSU', 2000, 8000, 10, 2, NULL, '-', '2023-02-28 20:08:38', 9000, 0, 2),
(41, 2, 'MEN000042', 'JAHE BUBUK', 2000, 6000, 10, 2, NULL, '-', '2023-02-28 20:08:38', 7000, 0, 2),
(42, 2, 'MEN000043', 'KOPI JAHE', 2000, 9000, 9, 2, NULL, '-', '2023-02-28 20:08:38', 0, 0, 2),
(43, 2, 'MEN000044', 'KOPI JAHE SUSU', 2000, 11000, 10, 2, NULL, '-', '2023-02-28 20:08:38', 0, 0, 2),
(44, 2, 'MEN000045', 'JAHE SUSU', 2000, 8000, 10, 2, NULL, '-', '2023-02-28 20:08:38', 0, 0, 2),
(45, 2, 'MEN000046', 'CLUB 600 ML', 2000, 3000, 10, 2, 'tes update', '-', '2023-02-28 20:08:38', 4000, 5000, 2);

-- --------------------------------------------------------

--
-- Table structure for table `menu_stok`
--

CREATE TABLE `menu_stok` (
  `id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `stok_awal` int(11) NOT NULL,
  `stok_akhir` int(11) NOT NULL,
  `date` date NOT NULL,
  `periode` varchar(255) DEFAULT NULL,
  `cabang_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `menu_stok`
--

INSERT INTO `menu_stok` (`id`, `menu_id`, `stok_awal`, `stok_akhir`, `date`, `periode`, `cabang_id`) VALUES
(1, 1, 10, 10, '2023-03-11', '2023-03', 0),
(2, 46, 5, 0, '2023-05-19', '2023-05', 0),
(3, 46, 10, 0, '2023-05-19', '2023-05', 0);

-- --------------------------------------------------------

--
-- Table structure for table `menu_stok_cabang`
--

CREATE TABLE `menu_stok_cabang` (
  `id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `cabang_id` int(11) NOT NULL,
  `stok_minim` int(11) NOT NULL,
  `stok` int(11) NOT NULL,
  `harga_pokok` int(11) NOT NULL,
  `harga_jual` int(11) NOT NULL,
  `harga_sedang` int(11) NOT NULL,
  `harga_jumbo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `menu_stok_cabang`
--

INSERT INTO `menu_stok_cabang` (`id`, `menu_id`, `cabang_id`, `stok_minim`, `stok`, `harga_pokok`, `harga_jual`, `harga_sedang`, `harga_jumbo`) VALUES
(1, 1, 2, 10, 10, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `profil_toko`
--

CREATE TABLE `profil_toko` (
  `id` int(11) NOT NULL,
  `nama_toko` varchar(255) NOT NULL,
  `alamat_toko` text NOT NULL,
  `telepon_toko` varchar(25) DEFAULT NULL,
  `email_toko` varchar(255) DEFAULT NULL,
  `pemilik_toko` varchar(255) DEFAULT NULL,
  `website_toko` varchar(255) DEFAULT NULL,
  `tgl_update` datetime DEFAULT NULL,
  `os` int(11) DEFAULT NULL,
  `print` int(11) DEFAULT NULL,
  `print_default` int(11) DEFAULT NULL,
  `driver` varchar(255) DEFAULT NULL,
  `footer_struk` varchar(255) DEFAULT NULL,
  `pajak` int(11) NOT NULL,
  `voucher` int(11) NOT NULL,
  `diskon` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `cabang_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `profil_toko`
--

INSERT INTO `profil_toko` (`id`, `nama_toko`, `alamat_toko`, `telepon_toko`, `email_toko`, `pemilik_toko`, `website_toko`, `tgl_update`, `os`, `print`, `print_default`, `driver`, `footer_struk`, `pajak`, `voucher`, `diskon`, `user_id`, `cabang_id`) VALUES
(1, 'Warmindo Pusat', 'Jl. Ikan Kakap No. 1B ', '081234567890', 'warmindo@gmail.com', 'Hadi', 'warmindo.com', '2021-03-07 05:25:19', 1, 1, 1, 'logo_1652604576.jpeg', 'TERIMA KASIH\r\nATAS KUNJUNGAN ANDA', 0, 0, 0, 1, 1),
(2, 'Warmindo Sam Ndut 3', 'Jl. Ikan Kakap No. 1B ', '0987654321', 'warmindo@gmail.com', 'Hadi 2', 'warmindo.com', '2021-03-07 05:25:19', 1, 1, 1, 'logo_warmindo_sn3.jpeg', 'TERIMA KASIH\r\nATAS KUNJUNGAN ANDA', 0, 0, 0, 1, 2),
(3, 'Warmindo Sam Ndut 2', 'Jl. Ikan Kakap No. 2B ', '0987654321', 'warmindo@gmail.com', 'Hadi 2', 'warmindo.com', '2021-03-07 05:25:19', 1, 1, 1, 'logo_warmindo_sn3.jpeg', 'TERIMA KASIH\r\nATAS KUNJUNGAN ANDA', 0, 0, 0, 1, 3),
(4, 'Warmindo Sam Ndut 1', 'Jl. Ikan Kakap No. 2B ', '0987654321', 'warmindo@gmail.com', 'Hadi 2', 'warmindo.com', '2021-03-07 05:25:19', 1, 1, 1, 'logo_warmindo_sn3.jpeg', 'TERIMA KASIH\r\nATAS KUNJUNGAN ANDA', 0, 0, 0, 1, 4);

-- --------------------------------------------------------

--
-- Table structure for table `shift`
--

CREATE TABLE `shift` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) DEFAULT NULL,
  `open` varchar(10) DEFAULT NULL,
  `close` varchar(10) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `shift`
--

INSERT INTO `shift` (`id`, `nama`, `open`, `close`, `created_at`) VALUES
(1, 'PAGI', '07:01', '15:00', '2023-01-11 08:11:46'),
(2, 'SORE', '15:01', '23:00', '2023-01-11 08:11:46'),
(3, 'MALAM', '23:01', '07:00', '2023-01-11 08:11:46');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int(11) NOT NULL,
  `no_bon` varchar(255) DEFAULT NULL,
  `kasir_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `atas_nama` varchar(255) DEFAULT NULL,
  `pesanan` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `diskon` int(11) NOT NULL,
  `pajak` int(11) NOT NULL,
  `voucher` int(11) NOT NULL,
  `grandmodal` int(11) NOT NULL,
  `grandtotal` int(11) NOT NULL,
  `total_qty` int(11) NOT NULL,
  `dibayar` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `date` date DEFAULT NULL,
  `periode` varchar(255) DEFAULT NULL,
  `year` varchar(255) DEFAULT NULL,
  `shift_id` int(11) NOT NULL,
  `closing_id` int(11) NOT NULL,
  `cabang_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id`, `no_bon`, `kasir_id`, `customer_id`, `atas_nama`, `pesanan`, `status`, `diskon`, `pajak`, `voucher`, `grandmodal`, `grandtotal`, `total_qty`, `dibayar`, `created_at`, `date`, `periode`, `year`, `shift_id`, `closing_id`, `cabang_id`) VALUES
(1, 'SN3/2306/00001', 6, 0, 'wer', '', 'QRIS', 0, 0, 0, 9, 8000, 1, 8000, '2023-06-19 17:42:04', '2023-06-19', '2023-06', '2023', 2, 1, 2),
(2, 'SN3/2306/00002', 6, 0, 'tes2', '', 'Online', 0, 0, 0, 0, 16000, 2, 16000, '2023-06-19 19:46:31', '2023-06-19', '2023-06', '2023', 2, 1, 2),
(3, 'SN3/2306/00003', 6, 0, 'tes', '', 'QRIS', 0, 0, 0, 9, 8000, 1, 8000, '2023-06-19 21:11:52', '2023-06-19', '2023-06', '2023', 2, 1, 2),
(4, 'SN3/2306/00004', 6, 0, 'tes', '', 'Cash', 0, 0, 0, 0, 24000, 3, 25000, '2023-06-19 21:12:43', '2023-06-19', '2023-06', '2023', 2, 1, 2),
(5, 'SN3/2306/00005', 6, 0, 'tes', '', 'QRIS', 0, 0, 0, 9, 8000, 1, 8000, '2023-06-21 00:18:00', '2023-06-21', '2023-06', '2023', 3, 1, 2),
(6, 'SN3/2306/00006', 6, 0, 'online', '', 'Online', 0, 0, 0, 0, 8000, 1, 8000, '2023-06-21 18:21:54', '2023-06-21', '2023-06', '2023', 3, 1, 2),
(7, 'SN3/2306/00007', 6, 0, 'qris', '', 'QRIS', 0, 0, 0, 0, 16000, 2, 16000, '2023-06-21 18:22:12', '2023-06-21', '2023-06', '2023', 3, 1, 2),
(8, 'SN3/2306/00008', 6, 0, 'XCash', '', 'Cash', 0, 0, 0, 0, 16000, 2, 20000, '2023-06-21 18:22:29', '2023-06-21', '2023-06', '2023', 3, 1, 2),
(9, 'SN3/2306/00009', 6, 0, 'tes1', '', 'QRIS', 0, 0, 0, 0, 16000, 2, 16000, '2023-06-23 19:36:02', '2023-06-23', '2023-06', '2023', 2, 0, 2),
(10, 'SN3/2306/00010', 6, 0, 'shift', '', 'QRIS', 0, 0, 0, 0, 8000, 1, 8000, '2023-06-23 19:42:38', '2023-06-22', '2023-06', '2023', 2, 0, 2);

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_keluar`
--

CREATE TABLE `transaksi_keluar` (
  `id` int(11) NOT NULL,
  `no_bon` varchar(255) DEFAULT NULL,
  `kasir_id` int(11) NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `jumlah` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `date` date DEFAULT NULL,
  `periode` varchar(255) DEFAULT NULL,
  `year` varchar(255) DEFAULT NULL,
  `shift_id` int(11) NOT NULL,
  `closing_id` int(11) NOT NULL,
  `cabang_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `transaksi_keluar`
--

INSERT INTO `transaksi_keluar` (`id`, `no_bon`, `kasir_id`, `keterangan`, `jumlah`, `created_at`, `date`, `periode`, `year`, `shift_id`, `closing_id`, `cabang_id`) VALUES
(1, '01', 6, 'Beli Es Batu', 5000, '2023-06-19 21:12:16', '2023-06-19', '2023-06', '2023', 2, 1, 2),
(2, '123', 6, 'Beli Es batu', 30000, '2023-06-21 18:22:49', '2023-06-21', '2023-06', '2023', 3, 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_produk`
--

CREATE TABLE `transaksi_produk` (
  `id` int(11) NOT NULL,
  `no_bon` varchar(255) DEFAULT NULL,
  `kode_menu` varchar(255) DEFAULT NULL,
  `kategori` varchar(255) DEFAULT NULL,
  `nama_menu` varchar(255) DEFAULT NULL,
  `qty` int(11) NOT NULL,
  `harga_beli` int(11) NOT NULL,
  `harga_jual` int(11) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `pesan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `date` date DEFAULT NULL,
  `periode` varchar(255) DEFAULT NULL,
  `year` varchar(255) DEFAULT NULL,
  `cabang_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `transaksi_produk`
--

INSERT INTO `transaksi_produk` (`id`, `no_bon`, `kode_menu`, `kategori`, `nama_menu`, `qty`, `harga_beli`, `harga_jual`, `keterangan`, `pesan`, `created_at`, `date`, `periode`, `year`, `cabang_id`) VALUES
(1, 'SN3/2306/00001', 'MEN000005', 'Makanan', 'INDOMIE GORENG()', 1, 5, 5, NULL, NULL, '2023-06-19 15:14:49', '2023-06-19', '2023-06', '2023', 0),
(2, 'SN3/2306/00001', 'MEN000009', 'Makanan', 'INDOMIE CABE HIJAU()', 1, 9, 8000, NULL, NULL, '2023-06-19 17:42:04', '2023-06-19', '2023-06', '2023', 0),
(3, 'SN3/2306/00002', 'MEN000011', 'Makanan', 'INDOMIE AYAM BAWANG()', 2, 0, 8000, NULL, NULL, '2023-06-19 19:46:31', '2023-06-19', '2023-06', '2023', 0),
(4, 'SN3/2306/00003', 'MEN000009', 'Makanan', 'INDOMIE CABE HIJAU()', 1, 9, 8000, NULL, NULL, '2023-06-19 21:11:52', '2023-06-19', '2023-06', '2023', 0),
(5, 'SN3/2306/00004', 'MEN000011', 'Makanan', 'INDOMIE AYAM BAWANG', 3, 0, 8000, NULL, NULL, '2023-06-19 21:12:43', '2023-06-19', '2023-06', '2023', 0),
(6, 'SN3/2306/00005', 'MEN000009', 'Makanan', 'INDOMIE CABE HIJAU()', 1, 9, 8000, NULL, NULL, '2023-06-21 13:18:00', '2023-06-21', '2023-06', '2023', 0),
(7, 'SN3/2306/00006', 'MEN000011', 'Makanan', 'INDOMIE AYAM BAWANG()', 1, 0, 8000, NULL, NULL, '2023-06-21 18:21:54', '2023-06-21', '2023-06', '2023', 0),
(8, 'SN3/2306/00007', 'MEN000014', 'Makanan', 'INDOMIE SEBLAK()', 1, 0, 8000, NULL, NULL, '2023-06-21 18:22:12', '2023-06-21', '2023-06', '2023', 0),
(9, 'SN3/2306/00007', 'MEN000013', 'Makanan', 'INDOMIE KARE AYAM()', 1, 0, 8000, NULL, NULL, '2023-06-21 18:22:12', '2023-06-21', '2023-06', '2023', 0),
(10, 'SN3/2306/00008', 'MEN000012', 'Makanan', 'INDOMIE AYAM POP()', 1, 0, 8000, NULL, NULL, '2023-06-21 18:22:29', '2023-06-21', '2023-06', '2023', 0),
(11, 'SN3/2306/00008', '1677588416', 'Makanan', 'INDOMIE AYAM SPESIAL()', 1, 0, 8000, NULL, NULL, '2023-06-21 18:22:29', '2023-06-21', '2023-06', '2023', 0),
(12, 'SN3/2306/00009', 'MEN000014', 'Makanan', 'INDOMIE SEBLAK()', 1, 0, 8000, NULL, NULL, '2023-06-23 19:36:02', '2023-06-23', '2023-06', '2023', 0),
(13, 'SN3/2306/00009', 'MEN000011', 'Makanan', 'INDOMIE AYAM BAWANG()', 1, 0, 8000, NULL, NULL, '2023-06-23 19:36:02', '2023-06-23', '2023-06', '2023', 0),
(14, 'SN3/2306/00010', 'MEN000014', 'Makanan', 'INDOMIE SEBLAK()', 1, 0, 8000, NULL, NULL, '2023-06-23 19:42:38', '2023-06-22', '2023-06', '2023', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bahanbaku`
--
ALTER TABLE `bahanbaku`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cabang`
--
ALTER TABLE `cabang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `closing`
--
ALTER TABLE `closing`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `closing_detail`
--
ALTER TABLE `closing_detail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `keranjang`
--
ALTER TABLE `keranjang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `keuangan_lainnya`
--
ALTER TABLE `keuangan_lainnya`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `keuangan_ledger`
--
ALTER TABLE `keuangan_ledger`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_stok`
--
ALTER TABLE `menu_stok`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_stok_cabang`
--
ALTER TABLE `menu_stok_cabang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `profil_toko`
--
ALTER TABLE `profil_toko`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shift`
--
ALTER TABLE `shift`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaksi_keluar`
--
ALTER TABLE `transaksi_keluar`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaksi_produk`
--
ALTER TABLE `transaksi_produk`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bahanbaku`
--
ALTER TABLE `bahanbaku`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cabang`
--
ALTER TABLE `cabang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `closing`
--
ALTER TABLE `closing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `closing_detail`
--
ALTER TABLE `closing_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `keranjang`
--
ALTER TABLE `keranjang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=375;

--
-- AUTO_INCREMENT for table `keuangan_lainnya`
--
ALTER TABLE `keuangan_lainnya`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `keuangan_ledger`
--
ALTER TABLE `keuangan_ledger`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `login`
--
ALTER TABLE `login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `menu_stok`
--
ALTER TABLE `menu_stok`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `menu_stok_cabang`
--
ALTER TABLE `menu_stok_cabang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `profil_toko`
--
ALTER TABLE `profil_toko`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `shift`
--
ALTER TABLE `shift`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `transaksi_keluar`
--
ALTER TABLE `transaksi_keluar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `transaksi_produk`
--
ALTER TABLE `transaksi_produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
