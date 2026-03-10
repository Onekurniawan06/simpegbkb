-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3307
-- Generation Time: Mar 10, 2026 at 08:17 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `simpegbkb`
--

-- --------------------------------------------------------

--
-- Table structure for table `berita`
--

CREATE TABLE `berita` (
  `id_pengumuman` bigint(20) UNSIGNED NOT NULL,
  `judul` varchar(255) DEFAULT NULL,
  `deskripsi_singkat` text DEFAULT NULL,
  `tanggal_posting` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `detail_pribadi`
--

CREATE TABLE `detail_pribadi` (
  `nomor_urut_pegawai` varchar(50) NOT NULL,
  `tempat_lahir` varchar(100) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `agama` varchar(50) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `nama_ibu` varchar(255) DEFAULT NULL,
  `nama_ayah` varchar(255) DEFAULT NULL,
  `pendidikan_terakhir` varchar(100) DEFAULT NULL,
  `jurusan` varchar(100) DEFAULT NULL,
  `no_telpon` varchar(20) DEFAULT NULL,
  `status_perkawinan` varchar(50) DEFAULT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') DEFAULT NULL,
  `dokumen_ktp` varchar(255) DEFAULT NULL,
  `dokumen_kk` varchar(255) DEFAULT NULL,
  `dokumen_npwp` varchar(255) DEFAULT NULL,
  `dokumen_buku_nikah` varchar(255) DEFAULT NULL,
  `dokumen_akta_cerai` varchar(255) DEFAULT NULL,
  `photo_selfie` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `detail_pribadi`
--

INSERT INTO `detail_pribadi` (`nomor_urut_pegawai`, `tempat_lahir`, `tanggal_lahir`, `agama`, `alamat`, `email`, `nama_ibu`, `nama_ayah`, `pendidikan_terakhir`, `jurusan`, `no_telpon`, `status_perkawinan`, `jenis_kelamin`, `dokumen_ktp`, `dokumen_kk`, `dokumen_npwp`, `dokumen_buku_nikah`, `dokumen_akta_cerai`, `photo_selfie`) VALUES
('1', 'Garut', '1972-05-02', NULL, 'Jl. Bojong Kaliki Kp. Rancabogo Wetan RT.002/014 Pataruman, Tarogong Kidul. Kabupaten Garut', NULL, 'Enah', NULL, 'S2', 'Ekonomi Manajemen', '087770011309', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('2', 'Depok', '1973-03-12', NULL, 'Jl. Situ Asih Rt 01/09 Kel. Rangkapan Jaya, Kec. Pancoran Mas, Depok', NULL, 'Ernawati', NULL, 'S1', 'Administrasi Publik/Negara', '087874832632', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('200182003', 'Bogor', '1982-02-09', NULL, 'Perumahan Mega Sentul Blok A/41 RT01/08 Ds. Pasir Laja Kec. Sukaraja Kab. Bogor', NULL, 'Elis', NULL, 'S2', 'Ekonomi Syariah', '081318636444', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('200480004', 'Bogor', '1980-05-08', NULL, 'Pagentongan Residence Blok B/9 Bogor', NULL, 'Halimah Tusadiah', NULL, 'S1', 'Ekonomi Manajemen', '085774740816', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('200779005', 'Bogor', '0000-00-00', NULL, 'Cilendek Indah Green Garden No. E5 Cilendek Barat Bogor', 'digitalloomcreations@gmail.com', 'IdaRT.i', NULL, 'S1', 'Pertanian Agribisnis', '085960663222', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('200781006', 'Bogor', '1981-04-12', NULL, 'Cimanggu Gg. Amil No. 12 RT. 01 RW. 04 Kel. Gedung Badak Kec. Tanah Sareal Bogor ', 'm.kurniawan.bondes@gmail.com', 'Patmi', NULL, 'S1', 'Ekonomi Manajemen', '087781714310', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('200783007', 'Bogor', '1983-03-22', NULL, 'Gang Kelor Rt 02/09 Menteng Bogor Barat', NULL, 'Chandra Sasih', NULL, 'S1', 'Ekonomi Manajemen', '081295005003', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('200877008', 'Padang', '1977-07-13', NULL, 'Jl. Sukasari III RT. 06/01 Kel. Sukasari Kec.Bogor Timur', NULL, 'Fauziah ', NULL, 'SMA', ' ', '087888307922', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('200879009', 'Bogor', '1979-09-11', NULL, 'Puri Nirwana 3 Blok AA No. 25 Karadenan Cibinong Bogor', NULL, 'Djuminten', NULL, 'S1', 'Ekonomi Manajemen', '08128452846', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('200885010', 'Bogor', '1985-12-06', NULL, 'Kota Bogor 16142', NULL, 'Anita Sukawati', NULL, 'S1', 'Ilmu Pemerintahan', '08111103301', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('200979011', NULL, NULL, NULL, 'Jl. Dr. Semeru Kelor Raya RT. 03/10 No. 33 Bogor 16111', 'saitokunjimmy@gmail.com', 'Entin', NULL, 'S1', 'Pertanian Agribisnis', '085773740755', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('200979012', 'Bogor', '1979-02-13', NULL, 'Jl. Cimanggu No.19 RT.01/02 Kel.Gedung Waringin, Kec. Tanah Sareal Bogor', NULL, 'Essy Herminah (Almh)', NULL, 'S1', 'Pertanian Agronomi', '08999522602', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('200984013', 'Bogor', '1980-07-16', NULL, 'Kp. Kalibata No. 36  RT.04 / RW.11 Kel. Bantarjati Bogor ', 'rideonexide@gmail.com', 'Praptiningsih', NULL, 'S1', 'Ekonomi dan Studi Pembangunan', '081912270879', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('201080014', 'Bogor', '1980-07-16', NULL, 'Jl. Pancasan Atas RT. 01/06 No. 37 Gg. H. Ahmad Dahlan Syah', NULL, 'Tuti Hasanah', NULL, 'SMA', ' ', '085716208313', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('201283015', 'Bogor', '1983-01-30', NULL, 'Babakan Sukamantri  Rt.003 Rw. 007  Kel. Pasir Kuda Kec. Bogor Barat  Kota Bogor', NULL, 'Ruminah ', NULL, 'SMK', 'Listrik Instalasi', '087860427887', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('201287016', 'Bogor', '1987-02-26', NULL, 'Jl. Kecubung No. 14 Blok A Pabaton Indah Rt.002 Rw.007', NULL, 'Kasiah', NULL, 'S1', 'Hukum ', '083806270672', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('201382017', 'Lombok', '1982-08-15', NULL, 'Jl. Ciomas Permai Blok C 28 No. 2', NULL, 'Asmani', NULL, 'Madrasah', 'Aliyah ', '087877302926', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('201384018', 'Bogor', '1984-07-14', NULL, 'Jl. Abesin Gg. Langgar Rt. 03/04', NULL, 'Sofia', NULL, 'S1', 'Hukum ', '087770041982', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('201481019', 'Bogor', '1981-06-22', NULL, 'Panggugah Ciomas No 45 Bogor Kp. Sukamulya ', NULL, 'Rohasih', NULL, 'S1', 'Hukum ', '085773733783', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('201578020', 'Bogor', '1978-04-25', NULL, 'Cimanggu Gg. Amil RT. 01/04 Bogor No. 13i', NULL, 'Rohyati', NULL, 'Madrasah', 'Aliyah ', '085710748002', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('201579021', 'Bogor', '1979-12-27', NULL, 'Jl. Bhayangkara Raya No. 19 Bogor', NULL, 'Eti Suhaeti', NULL, 'S1', 'Tehnik Arsitektur', '085693300021', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('201579022', 'Bogor', '1979-08-16', NULL, 'Kp. Salabenda  RT.01  RW.09  Kel.Curug Kec. Bogor Barat', NULL, 'Lilis mutiah', NULL, 'SMK', 'Administrasi Perkantoran - Sekretaris', '081219820352', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('201583023', 'Bogor', '1983-08-06', NULL, 'Jl.Raya Bojong Menteng RT 03 /RW 02 Kel.Pasir Mulya Kec.Bogor Barat. Kota Bogor', NULL, 'Hayati', NULL, 'S1', 'Ekonomi Manajemen', '081519607007', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('201584025', 'Bogor', '1984-10-08', NULL, 'Gg. Kelor No.37  RT.002  RW.009 Kel. Menteng Kec. Bogor  Barat ', NULL, 'Enay Minarni', NULL, 'D3', 'Manajemen Pemasaran', '081317729699', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('201585026', 'Jakarta', '1985-08-05', NULL, 'Jl. Duta Utama No. 15 Curug Mekar', NULL, 'SumaRT.ini', NULL, 'S1', 'Teknik Industri', '089614731933', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('201585027', 'Bogor', '1985-10-03', NULL, 'Jalan Sindang Barang Pengkolan Rt 06/04, Bogor', NULL, 'Rohayati', NULL, 'SMA', 'IPS ', '087809697653', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('201588028', 'Bogor', '1988-03-03', NULL, 'Jl. Danau Bogor Raya RT. 05/07 Ciheuleut', NULL, 'Sri Suryati Wahyuningsih', NULL, 'S1', 'Tehnik Informatika', '0895354568871', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('201589029', 'Bogor', '1989-11-30', NULL, 'Kp. Gegerbitung RT. 04/04 Cijeruk Kab. Bogor', NULL, 'Sukaesih', NULL, 'S1', 'Ekonomi Manajemen', '082260062006', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('201590030', 'Lebak', '1990-01-24', NULL, 'Bumi Pangkalan Endah A/16 RT. 02 RW.07 Kel. Kedung Halang', NULL, 'Siti kaniasari', NULL, 'S1', 'Ekonomi Manajemen', '0895627674434', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('201784031', 'Garut', '1984-07-11', NULL, 'Perum BTN Kebun Raya Blok A7 No.3 Rt 01/06 Bogor', NULL, 'Dewi Yanti', NULL, 'S1', 'Hukum ', '087889277006', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('201788032', 'Bogor', '1988-11-03', NULL, 'Ciwaringin GG Sukarna No.33  RT.004  RW.003 Kec. Bogor Tengah', NULL, 'SuheRT.i', NULL, 'S2', 'Manajemen SDM', '085885973749', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('201789033', 'Bogor', '1989-01-17', NULL, 'JL. Jenaka 4 No.16 RT.003   RW.015 Kel. Tegal Gundil', NULL, 'Een Siswati', NULL, 'S1', 'Sistem Informasi', '081211957416', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('201792034', 'Bogor', '1992-12-01', NULL, 'Bojong Neros Rt. 04/07 Paledang, Bogor', NULL, 'Aan S', NULL, 'S1', 'Komputer ', '087738003825', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('201793036', 'Bogor', '1993-11-06', NULL, 'Jl. Arwana 3 Blok AA 4 No. 8 RT.05/10 Kel Padasuka Kec. Ciomas', NULL, 'Cucu', NULL, ' D3 ', 'Manajemen Keuangan dan Perbankan', '081389744876', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('201890037', 'Bogor', '1990-05-20', NULL, 'Jl. Artzimar II No.1 Kelurahan Tegalgundil Kecamatan Bogor Utara, Kota Bogor.', NULL, 'Darlinah', NULL, ' S1 ', 'Komputer ', '089520280243', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('201894038', 'Bogor', '1994-12-28', NULL, 'Jl. Jambu III No. 22 RT. 08/06 Perumnas Bantarkemang Kel. Baranangsiang, Bogor.', NULL, 'Titi Maryati (Almarhum)', NULL, ' SMA ', ' ', '085777248725', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('202075040', 'Bogor', '1975-08-09', NULL, 'Jl. Rimba Bojong Menteng Rt. 05/06, Kel. Pasir Kuda, Kec. Bogor Barat, Kota Bogor', NULL, '', NULL, ' SMA ', ' ', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('202076041', 'Bogor', '1976-10-25', NULL, 'Kp. Palasari Rt. 04/02 Kel. Tanjungsari Kec. Cijeruk Kabupaten Bogor', NULL, 'Lies Hartuti', NULL, 'SMA', ' ', '08561004765', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('202091042', 'Bogor', '1991-09-28', NULL, 'Kavling Badak Putih I RT. 03/10 No. 3 Desa Kotabatu Kecamatan Ciomas, Kabupaten Bogor.', NULL, 'Ayanah', NULL, ' D3 ', 'Manajemen Informatika', '085723403320', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('202091043', 'Bogor', '1991-09-30', NULL, 'Jl. Curug RT. 01/01 No. 3 Kel. Curug Kecamatan Bogor Barat, Kota Bogor.', NULL, 'Asmunah', NULL, ' S1 ', 'Ekonomi Manajemen', '081517660809', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('202092045', 'Sleman', '1992-07-03', NULL, 'Bumi Ciluar Indah B.4 No. 6 RT. 02/08 Kecamatan Bogor Utara, Kota Bogor.', NULL, 'Lilis Haryati', NULL, ' S1 ', 'Ekonomi Manajemen', '08998589179', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('202092046', 'Bogor', '1992-08-07', NULL, 'Kedung Badak RT. 07/01 Kel. Kedung Badak, Kec.Tanah Sareal, Kota Bogor.', NULL, 'Ika Ritta', NULL, ' S1 ', 'Ekonomi Akuntansi', '088212297705', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('202094048', 'Cianjur', '1994-09-02', NULL, 'Jl. Swadaya 4 No. 13A RT. 06/08 Rawageni Kel. Ratu Jaya, Kec. Cipayung, Kota Depok.', NULL, 'Neneng Rosidah', NULL, ' S1 ', 'Administrasi Publik/Negara', '081280505185', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('202095049', 'Bogor', '1995-12-08', NULL, 'Kedung Halang Wates RT. 01/01 Kel. Sukaresmi, Kec. Tanah Sareal, Kota Bogor.', NULL, 'Prihtina Nugrahani', NULL, ' S1 ', 'Ekonomi Manajemen', '089639400686', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('202095050', 'Bogor', '1995-09-04', NULL, 'Jl. Sukadamai Indah Kp. Situpete RT. 02/08 No. 70 Kel. Sukadamai, Kec. Tanah Sareal', NULL, 'Yuyun', NULL, ' S1 ', 'Ekonomi Akuntansi', '081211733244', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('202095051', 'Bogor', '1995-11-16', NULL, 'Jl. Sitihasanah Babakan Sukamantri Rt. 02/07 Kel. Pasir Kuda, Kec. Bogor Barat, Kota Bogor.', NULL, 'Tuty', NULL, ' D3 ', 'Manajemen Informatika', '089513955338', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('202097052', 'Bogor', '1997-08-19', NULL, 'Sindangbarang Pengkolan RT. 03/04 Kel. Sindangbarang, Kec. Bogor Barat, Kota Bogor.', NULL, 'Nuryati', NULL, ' D3 ', 'Akuntansi ', '085775170300', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('202097053', 'Bogor', '1997-10-12', NULL, 'Sindangbarang Pengkolan RT. 03/04 Kel. Sindangbarang, Kec. Bogor Barat, Kota Bogor.', NULL, 'Kusmiyati', NULL, ' S1 ', 'Ekonomi ', '089621183844', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('202396054', 'Bogor', '1996-04-08', NULL, 'Bojong Menteng No. 10 RT. 04/06 Kel. Pasir Kuda, Kec. Bogor Barat, Kota Bogor', NULL, '', NULL, ' S1 ', 'Komputer ', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('202690055', 'Bogor', '1990-06-08', 'Islam', 'Karadenan Cibinong Bogor', 'triyantokurniawan3@gmail.com', 'Aaaa', 'Bbbbb', 'D3', 'Sistem Informasi', '010101010104', 'Belum Kawin', 'Laki-laki', 'KTP_202690055_1772681075.png', 'KK_202690055_1772681078.png', 'NPWP_202690055_1772681078.png', 'BUKU_NIKAH_202690055_1772681078.jpg', 'AKTA_CERAI_202690055_1772681078.png', 'uploads/selfie/KmuzuZXlzwtYFPtsip0kZpR5EHjeUL5Yh7xNqC9w.jpg'),
('3', 'Mandai', '1972-12-16', NULL, 'Apartemen H Residence Unit 1003 JL. Biru Laut X, Cawang Jakarta', NULL, 'Siti Sofiyah', NULL, 'S1', 'Sosial Politik', '081310413329', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('PKWT', 'Bogor', '1987-03-28', NULL, 'Jalan A Yani Blk 14 No. 01 Air Mancur', NULL, '', NULL, 'S1', 'Pendidikan Keguruan', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `divisi`
--

CREATE TABLE `divisi` (
  `id_divisi` int(11) NOT NULL,
  `kode_divisi` varchar(255) NOT NULL,
  `nama_divisi` varchar(255) NOT NULL,
  `tanggal_dibuat` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `divisi`
--

INSERT INTO `divisi` (`id_divisi`, `kode_divisi`, `nama_divisi`, `tanggal_dibuat`) VALUES
(1, 'DIV01', 'Umum', '2026-03-04 12:12:59'),
(2, 'DIV02', 'SKAI', '2026-03-04 12:12:59'),
(3, 'DIV03', 'Kredit', '2026-03-04 12:12:59'),
(4, 'DIV04', 'SKK & SKKMR', '2026-03-04 12:12:59'),
(5, 'DIV05', 'Dana & Jasa Layanan', '2026-03-04 12:12:59'),
(6, 'DIV06', 'Pelaporan & IT', '2026-03-04 12:12:59');

-- --------------------------------------------------------

--
-- Table structure for table `file_persyaratanpangkatgajitunjangan`
--

CREATE TABLE `file_persyaratanpangkatgajitunjangan` (
  `id` int(11) NOT NULL,
  `pengajuan_pangkatgajitunjangan_id` int(11) NOT NULL,
  `nomor_urut_pegawai` varchar(50) NOT NULL,
  `nama_file_asli` varchar(255) NOT NULL,
  `path_file_server` varchar(255) NOT NULL,
  `tipe_dokumen` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `file_persyaratanpangkatgajitunjangan`
--

INSERT INTO `file_persyaratanpangkatgajitunjangan` (`id`, `pengajuan_pangkatgajitunjangan_id`, `nomor_urut_pegawai`, `nama_file_asli`, `path_file_server`, `tipe_dokumen`, `created_at`) VALUES
(1, 1, '202690055', '14_7572_unv111_122019_pdf.pdf', 'dokumen_pangkat_gaji/tunjangan-keluarga-anak/202690055/Surat_Permohonan_202690055_1772961732.pdf', 'Surat_Permohonan', '2026-03-08 16:22:14'),
(2, 1, '202690055', '357919.pdf', 'dokumen_pangkat_gaji/tunjangan-keluarga-anak/202690055/Salinan_Akta_Kelahiran_202690055_1772961734.pdf', 'Salinan_Akta_Kelahiran', '2026-03-08 16:22:14'),
(3, 1, '202690055', 'AdminLTE 3  DataTables.pdf', 'dokumen_pangkat_gaji/tunjangan-keluarga-anak/202690055/Salinan_Kartu_Keluarga__KK__202690055_1772961735.pdf', 'Salinan_Kartu_Keluarga__KK_', '2026-03-08 16:22:15'),
(4, 2, '202690055', '14_7572_unv111_122019_pdf.pdf', 'dokumen_pangkat_gaji/tunjangan-keluarga-anak/202690055/Surat_Permohonan_202690055_1772962239.pdf', 'Surat_Permohonan', '2026-03-08 16:30:41'),
(5, 2, '202690055', '357919.pdf', 'dokumen_pangkat_gaji/tunjangan-keluarga-anak/202690055/Salinan_Akta_Kelahiran_202690055_1772962241.pdf', 'Salinan_Akta_Kelahiran', '2026-03-08 16:30:41'),
(6, 2, '202690055', 'AdminLTE 3  DataTables.pdf', 'dokumen_pangkat_gaji/tunjangan-keluarga-anak/202690055/Salinan_Kartu_Keluarga__KK__202690055_1772962241.pdf', 'Salinan_Kartu_Keluarga__KK_', '2026-03-08 16:30:41'),
(7, 1, '202690055', '14_7572_unv111_122019_pdf.pdf', 'dokumen_pangkat_gaji/tunjangan-keluarga-anak/202690055/Surat_Permohonan_202690055_1772963101.pdf', 'Surat_Permohonan', '2026-03-08 16:45:01'),
(8, 1, '202690055', '357919.pdf', 'dokumen_pangkat_gaji/tunjangan-keluarga-anak/202690055/Salinan_Akta_Kelahiran_202690055_1772963101.pdf', 'Salinan_Akta_Kelahiran', '2026-03-08 16:45:01'),
(9, 1, '202690055', 'AdminLTE 3  DataTables.pdf', 'dokumen_pangkat_gaji/tunjangan-keluarga-anak/202690055/Salinan_Kartu_Keluarga__KK__202690055_1772963101.pdf', 'Salinan_Kartu_Keluarga__KK_', '2026-03-08 16:45:01'),
(10, 2, '202690055', '14_7572_unv111_122019_pdf.pdf', 'dokumen_pangkat_gaji/tunjangan-keluarga-anak/202690055/Surat_Permohonan_202690055_1772964190.pdf', 'Surat_Permohonan', '2026-03-08 17:03:10'),
(11, 2, '202690055', '357919.pdf', 'dokumen_pangkat_gaji/tunjangan-keluarga-anak/202690055/Salinan_Akta_Kelahiran_202690055_1772964190.pdf', 'Salinan_Akta_Kelahiran', '2026-03-08 17:03:10'),
(12, 2, '202690055', 'AdminLTE 3  DataTables.pdf', 'dokumen_pangkat_gaji/tunjangan-keluarga-anak/202690055/Salinan_Kartu_Keluarga__KK__202690055_1772964190.pdf', 'Salinan_Kartu_Keluarga__KK_', '2026-03-08 17:03:10'),
(13, 3, '200781006', 'Technology and Crime_Kriminologi G.pdf', 'dokumen_pangkat_gaji/tunjangan-keluarga-anak/200781006/Surat_Permohonan_200781006_1772987255.pdf', 'Surat_Permohonan', '2026-03-08 23:27:35'),
(14, 3, '200781006', 'Petunjuk Pengajuan Layanan KTP  El  Baru Perekaman   Hilang   Rusak .pdf', 'dokumen_pangkat_gaji/tunjangan-keluarga-anak/200781006/Salinan_Akta_Kelahiran_200781006_1772987255.pdf', 'Salinan_Akta_Kelahiran', '2026-03-08 23:27:35'),
(15, 3, '200781006', 'jbptunikompp-gdl-suryalagar-28817-10-unikom_s-4.pdf', 'dokumen_pangkat_gaji/tunjangan-keluarga-anak/200781006/Salinan_Kartu_Keluarga__KK__200781006_1772987255.pdf', 'Salinan_Kartu_Keluarga__KK_', '2026-03-08 23:27:35'),
(16, 4, '200781006', '6.+JIKOSTIK_jurnal+ekohardi_QR+code_v1_fin_two+col_230821.pdf', 'dokumen_pangkat_gaji/tunjangan-keluarga-anak/200781006/Surat_Permohonan_200781006_1772988746.pdf', 'Surat_Permohonan', '2026-03-08 23:52:26'),
(17, 4, '200781006', 'Surat_Cuti_20251219052.pdf', 'dokumen_pangkat_gaji/tunjangan-keluarga-anak/200781006/Salinan_Akta_Kelahiran_200781006_1772988747.pdf', 'Salinan_Akta_Kelahiran', '2026-03-08 23:52:27'),
(18, 4, '200781006', '4982-9012-1-SM.pdf', 'dokumen_pangkat_gaji/tunjangan-keluarga-anak/200781006/Salinan_Kartu_Keluarga__KK__200781006_1772988747.pdf', 'Salinan_Kartu_Keluarga__KK_', '2026-03-08 23:52:27');

-- --------------------------------------------------------

--
-- Table structure for table `file_persyaratanpensiun`
--

CREATE TABLE `file_persyaratanpensiun` (
  `id` int(11) NOT NULL,
  `pengajuan_pensiun_id` int(11) NOT NULL,
  `nomor_urut_pegawai` varchar(50) NOT NULL,
  `nama_file_asli` varchar(255) NOT NULL,
  `path_file_server` varchar(255) NOT NULL,
  `tipe_dokumen` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `file_persyaratanpensiun`
--

INSERT INTO `file_persyaratanpensiun` (`id`, `pengajuan_pensiun_id`, `nomor_urut_pegawai`, `nama_file_asli`, `path_file_server`, `tipe_dokumen`, `created_at`) VALUES
(1, 1, '202690055', '357919.pdf', 'dokumen_pensiun/202690055/Surat_Permohonan_202690055_1772809970.pdf', 'Surat_Permohonan', '2026-03-06 15:12:55'),
(2, 1, '202690055', 'AdminLTE 3  DataTables.pdf', 'dokumen_pensiun/202690055/SK_Gaji_Pokok_Berkala_Terakhir_202690055_1772809975.pdf', 'SK_Gaji_Pokok_Berkala_Terakhir', '2026-03-06 15:12:55'),
(3, 1, '202690055', 'Bukti_Ujian_12200015_2.pdf', 'dokumen_pensiun/202690055/SK_Pengangkatan_Pertama_Pegawai_Tetap_202690055_1772809975.pdf', 'SK_Pengangkatan_Pertama_Pegawai_Tetap', '2026-03-06 15:12:55'),
(4, 1, '202690055', 'data_siswa_tglHariIni_2025-08-28.pdf', 'dokumen_pensiun/202690055/SK_Kenaikan_Pangkat_Terakhir_202690055_1772809975.pdf', 'SK_Kenaikan_Pangkat_Terakhir', '2026-03-06 15:12:55'),
(5, 1, '202690055', '14_7572_unv111_122019_pdf.pdf', 'dokumen_pensiun/202690055/Daftar_Penilaian_Kerja_Terakhir_202690055_1772809975.pdf', 'Daftar_Penilaian_Kerja_Terakhir', '2026-03-06 15:12:55'),
(6, 3, '200781006', 'Surat_Cuti_20251219052.pdf', 'dokumen_pensiun/200781006/Surat_Permohonan_200781006_1772985455.pdf', 'Surat_Permohonan', '2026-03-08 15:57:37'),
(7, 3, '200781006', '4982-9012-1-SM.pdf', 'dokumen_pensiun/200781006/SK_Gaji_Pokok_Berkala_Terakhir_200781006_1772985457.pdf', 'SK_Gaji_Pokok_Berkala_Terakhir', '2026-03-08 15:57:37'),
(8, 3, '200781006', 'landinfo.com-GeoData-Indonesia.pdf', 'dokumen_pensiun/200781006/SK_Pengangkatan_Pertama_Pegawai_Tetap_200781006_1772985457.pdf', 'SK_Pengangkatan_Pertama_Pegawai_Tetap', '2026-03-08 15:57:37'),
(9, 3, '200781006', 'Lampiran_I_Rincian_Formasi.pdf', 'dokumen_pensiun/200781006/SK_Kenaikan_Pangkat_Terakhir_200781006_1772985457.pdf', 'SK_Kenaikan_Pangkat_Terakhir', '2026-03-08 15:57:37'),
(10, 3, '200781006', 'app5_sebi143112_eng.pdf', 'dokumen_pensiun/200781006/Daftar_Penilaian_Kerja_Terakhir_200781006_1772985457.pdf', 'Daftar_Penilaian_Kerja_Terakhir', '2026-03-08 15:57:38'),
(11, 4, '200781006', '6.+JIKOSTIK_jurnal+ekohardi_QR+code_v1_fin_two+col_230821.pdf', 'dokumen_pensiun/200781006/Surat_Permohonan_200781006_1772986793.pdf', 'Surat_Permohonan', '2026-03-08 16:19:54'),
(12, 4, '200781006', 'Surat_Cuti_20251219052.pdf', 'dokumen_pensiun/200781006/SK_Gaji_Pokok_Berkala_Terakhir_200781006_1772986794.pdf', 'SK_Gaji_Pokok_Berkala_Terakhir', '2026-03-08 16:19:54'),
(13, 4, '200781006', '4982-9012-1-SM.pdf', 'dokumen_pensiun/200781006/SK_Pengangkatan_Pertama_Pegawai_Tetap_200781006_1772986795.pdf', 'SK_Pengangkatan_Pertama_Pegawai_Tetap', '2026-03-08 16:19:55'),
(14, 4, '200781006', 'landinfo.com-GeoData-Indonesia.pdf', 'dokumen_pensiun/200781006/SK_Kenaikan_Pangkat_Terakhir_200781006_1772986795.pdf', 'SK_Kenaikan_Pangkat_Terakhir', '2026-03-08 16:19:55'),
(15, 4, '200781006', 'app5_sebi143112_eng.pdf', 'dokumen_pensiun/200781006/Daftar_Penilaian_Kerja_Terakhir_200781006_1772986795.pdf', 'Daftar_Penilaian_Kerja_Terakhir', '2026-03-08 16:19:55'),
(16, 5, '200781006', '6.+JIKOSTIK_jurnal+ekohardi_QR+code_v1_fin_two+col_230821.pdf', 'dokumen_pensiun/200781006/Surat_Permohonan_200781006_1772988398.pdf', 'Surat_Permohonan', '2026-03-08 16:46:38'),
(17, 5, '200781006', 'Surat_Cuti_20251219052.pdf', 'dokumen_pensiun/200781006/SK_Gaji_Pokok_Berkala_Terakhir_200781006_1772988398.pdf', 'SK_Gaji_Pokok_Berkala_Terakhir', '2026-03-08 16:46:39'),
(18, 5, '200781006', '4982-9012-1-SM.pdf', 'dokumen_pensiun/200781006/SK_Pengangkatan_Pertama_Pegawai_Tetap_200781006_1772988399.pdf', 'SK_Pengangkatan_Pertama_Pegawai_Tetap', '2026-03-08 16:46:40'),
(19, 5, '200781006', 'landinfo.com-GeoData-Indonesia.pdf', 'dokumen_pensiun/200781006/SK_Kenaikan_Pangkat_Terakhir_200781006_1772988400.pdf', 'SK_Kenaikan_Pangkat_Terakhir', '2026-03-08 16:46:40'),
(20, 5, '200781006', 'app5_sebi143112_eng.pdf', 'dokumen_pensiun/200781006/Daftar_Penilaian_Kerja_Terakhir_200781006_1772988400.pdf', 'Daftar_Penilaian_Kerja_Terakhir', '2026-03-08 16:46:40');

-- --------------------------------------------------------

--
-- Table structure for table `jabatan`
--

CREATE TABLE `jabatan` (
  `jabatan_id` int(11) NOT NULL,
  `nama_jabatan` varchar(255) NOT NULL,
  `level_jabatan` varchar(50) DEFAULT NULL,
  `id_divisi` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jabatan`
--

INSERT INTO `jabatan` (`jabatan_id`, `nama_jabatan`, `level_jabatan`, `id_divisi`) VALUES
(1, 'Account Officer', NULL, NULL),
(2, 'Administrasi Umum Rumah Tangga / Logistik', NULL, NULL),
(3, 'Analis Manajemen Risiko, APU-PPT & Strategi Anti Fraud', NULL, NULL),
(4, 'Auditor Madya', NULL, NULL),
(5, 'Collector Dana', NULL, NULL),
(6, 'Credit Administration Officer', NULL, NULL),
(7, 'Credit Maintenance Officer (Remedial)', NULL, NULL),
(8, 'Credit Marketing', NULL, NULL),
(9, 'Customer Service', NULL, NULL),
(10, 'Direktur Kepatuhan', NULL, NULL),
(11, 'Direktur Operasional', NULL, NULL),
(12, 'Direktur Utama', NULL, NULL),
(13, 'Finance Officer', NULL, NULL),
(14, 'Funding & Service Banking Officer', NULL, NULL),
(15, 'Hardware IT Officer & Programer', NULL, NULL),
(16, 'Human Resources Officer, Legal, Analis Kepatuhan', NULL, NULL),
(17, 'Kepala Kantor Kas', NULL, NULL),
(18, 'Kepala Satker Kepatuhan & M.R.', NULL, NULL),
(19, 'Kepala SKAI', NULL, NULL),
(20, 'Manajer Dana & Jasa Layanan', NULL, NULL),
(21, 'Manajer Kredit', NULL, NULL),
(22, 'Manajer Pelaporan & IT', NULL, NULL),
(23, 'Manajer Umum', NULL, NULL),
(24, 'Teller', NULL, NULL),
(25, 'Treasury Officer', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `jenis_cuti`
--

CREATE TABLE `jenis_cuti` (
  `id` int(11) NOT NULL,
  `nama_cuti` varchar(100) NOT NULL COMMENT 'Nama jenis cuti, misal: Cuti Tahunan, Cuti Menikah',
  `durasi_hari` int(11) DEFAULT NULL COMMENT 'Durasi dalam hari. NULL jika durasi tidak spesifik atau bervariasi',
  `durasi_bulan` int(11) DEFAULT NULL COMMENT 'Durasi dalam bulan. NULL jika tidak berlaku',
  `deskripsi_periode` varchar(50) DEFAULT NULL COMMENT 'Deskripsi periode asli dari dokumen, misal: "12 Hari", "3 Bulan", "3 x"',
  `is_cuti_penting` tinyint(1) DEFAULT 0 COMMENT 'TRUE untuk cuti alasan penting, FALSE untuk cuti umum'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jenis_cuti`
--

INSERT INTO `jenis_cuti` (`id`, `nama_cuti`, `durasi_hari`, `durasi_bulan`, `deskripsi_periode`, `is_cuti_penting`) VALUES
(1, 'Cuti Tahunan', 12, NULL, '12 Hari', 0),
(2, 'Cuti Besar', NULL, 2, '2 Bulan', 0),
(3, 'Cuti Menikah', 5, NULL, '5 Hari', 0),
(4, 'Cuti Melahirkan', NULL, 3, '3 Bulan', 0),
(5, 'Cuti Sakit', 3, NULL, '3 Hari', 0),
(6, 'Cuti Hari Raya Keagamaan', NULL, NULL, '-', 0),
(7, 'Cuti Menunaikan Ibadah Keagamaan - Umrah', 12, NULL, '12 Hari', 0),
(8, 'Cuti Menunaikan Ibadah Keagamaan - Haji', 40, NULL, '40 Hari', 0),
(9, 'Cuti Alasan Penting dan Mendesak', 2, NULL, '2 Hari', 1),
(10, 'Izin Tidak Masuk Kerja', NULL, NULL, '-', 0);

-- --------------------------------------------------------

--
-- Table structure for table `jenjang_pendidikan`
--

CREATE TABLE `jenjang_pendidikan` (
  `id_jenjang` int(11) NOT NULL,
  `kode_jenjang` varchar(10) NOT NULL,
  `nama_jenjang` varchar(50) NOT NULL,
  `urutan` int(11) NOT NULL COMMENT 'Untuk sorting di dropdown: 10=SD, 20=SMP, dst'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `keluarga_pegawai`
--

CREATE TABLE `keluarga_pegawai` (
  `id` int(11) NOT NULL,
  `nomor_urut_pegawai` varchar(50) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `hubungan` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `level_jabatan`
--

CREATE TABLE `level_jabatan` (
  `level_id` int(11) NOT NULL,
  `nama_level` varchar(50) NOT NULL,
  `deskripsi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `level_jabatan`
--

INSERT INTO `level_jabatan` (`level_id`, `nama_level`, `deskripsi`) VALUES
(1, 'Pegawai', 'Jabatan entry-level dan operasional.'),
(2, 'Manajer', 'Jabatan tingkat menengah yang mengelola tim.'),
(3, 'Direktur', 'Jabatan eksekutif.'),
(4, 'Direktur Utama', 'Jabatan eksekutif tingkat atas.'),
(5, 'Administrator', 'Khusus HRO.');

-- --------------------------------------------------------

--
-- Table structure for table `log_persetujuan_cuti`
--

CREATE TABLE `log_persetujuan_cuti` (
  `id` int(11) NOT NULL,
  `nomor_urut_pegawai` varchar(50) NOT NULL,
  `tahap_persetujuan` varchar(100) DEFAULT NULL,
  `nomor_urut_pegawai_penyetuju` varchar(50) DEFAULT NULL,
  `status_pengajuan` varchar(50) DEFAULT NULL,
  `komentar` text DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `log_persetujuan_cuti`
--

INSERT INTO `log_persetujuan_cuti` (`id`, `nomor_urut_pegawai`, `tahap_persetujuan`, `nomor_urut_pegawai_penyetuju`, `status_pengajuan`, `komentar`, `updated_at`, `user_id`) VALUES
(8, '202690055', 'Pengajuan Awal', NULL, 'disetujui', 'Menunggu verifikasi.', '2026-03-06 22:06:50', NULL),
(9, '202690055', 'Manager', '200781006', 'disetujui', 'sdfsfsfsfdsdf', '2026-03-08 19:45:29', NULL),
(13, '200781006', 'Pengajuan Awal', NULL, 'diproses', 'Menunggu verifikasi.', '2026-03-08 21:36:48', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `log_persetujuan_lembur`
--

CREATE TABLE `log_persetujuan_lembur` (
  `id` int(11) NOT NULL,
  `lembur_id` int(11) NOT NULL,
  `nomor_urut_pegawai` varchar(50) NOT NULL,
  `tahap_persetujuan` varchar(100) DEFAULT NULL,
  `nomor_urut_pegawai_penyetuju` varchar(50) DEFAULT NULL,
  `status_persetujuan` varchar(50) DEFAULT NULL,
  `komentar` text DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `log_persetujuan_lembur`
--

INSERT INTO `log_persetujuan_lembur` (`id`, `lembur_id`, `nomor_urut_pegawai`, `tahap_persetujuan`, `nomor_urut_pegawai_penyetuju`, `status_persetujuan`, `komentar`, `updated_at`) VALUES
(3, 3, '202690055', 'Pengajuan Awal', NULL, 'diproses', 'Menunggu Verifikasi.', '2026-03-06 22:09:19'),
(5, 5, '200781006', 'Pengajuan Awal', NULL, 'diproses', 'Menunggu Verifikasi.', '2026-03-08 22:05:58');

-- --------------------------------------------------------

--
-- Table structure for table `log_persetujuan_pangkatgajitunjangan`
--

CREATE TABLE `log_persetujuan_pangkatgajitunjangan` (
  `id` int(11) NOT NULL,
  `id_pengajuan` int(11) NOT NULL,
  `nomor_urut_pegawai` varchar(50) NOT NULL,
  `tahap_persetujuan` varchar(100) DEFAULT NULL,
  `nomor_urut_pegawai_penyetuju` varchar(50) DEFAULT NULL,
  `status_persetujuan` varchar(50) DEFAULT NULL,
  `komentar` text DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `log_persetujuan_pangkatgajitunjangan`
--

INSERT INTO `log_persetujuan_pangkatgajitunjangan` (`id`, `id_pengajuan`, `nomor_urut_pegawai`, `tahap_persetujuan`, `nomor_urut_pegawai_penyetuju`, `status_persetujuan`, `komentar`, `updated_at`) VALUES
(4, 2, '202690055', 'Pengajuan Awal', NULL, 'diproses', 'Menunggu persetujuan.', '2026-03-08 17:03:10'),
(6, 4, '200781006', 'Pengajuan Awal', NULL, 'diproses', 'Menunggu persetujuan.', '2026-03-08 23:52:26');

-- --------------------------------------------------------

--
-- Table structure for table `log_persetujuan_pensiun`
--

CREATE TABLE `log_persetujuan_pensiun` (
  `id` int(11) NOT NULL,
  `id_pengajuan` int(11) NOT NULL,
  `nomor_urut_pegawai` varchar(50) NOT NULL,
  `tahap_persetujuan` varchar(100) DEFAULT NULL,
  `nomor_urut_pegawai_penyetuju` varchar(50) DEFAULT NULL,
  `status_persetujuan` varchar(50) DEFAULT NULL,
  `komentar` text DEFAULT NULL,
  `update_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `log_persetujuan_pensiun`
--

INSERT INTO `log_persetujuan_pensiun` (`id`, `id_pengajuan`, `nomor_urut_pegawai`, `tahap_persetujuan`, `nomor_urut_pegawai_penyetuju`, `status_persetujuan`, `komentar`, `update_at`) VALUES
(1, 1, '202690055', 'Pengajuan Awal', NULL, 'diproses', 'Menunggu persetujuan.', '2026-03-06 22:12:50'),
(4, 5, '200781006', 'Pengajuan Awal', NULL, 'diproses', 'Menunggu persetujuan.', '2026-03-08 23:46:38');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pegawai`
--

CREATE TABLE `pegawai` (
  `nomor_urut_pegawai` varchar(15) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `nik` varchar(18) DEFAULT NULL,
  `npwp` varchar(22) DEFAULT NULL,
  `level_id` int(11) DEFAULT NULL,
  `nama_level` varchar(50) DEFAULT NULL,
  `photo_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pegawai`
--

INSERT INTO `pegawai` (`nomor_urut_pegawai`, `nama`, `nik`, `npwp`, `level_id`, `nama_level`, `photo_path`) VALUES
('1', 'Tommy I Gunawan', '3205050205720003', '09.397.386.5-443.000', NULL, 'Direktur Utama', NULL),
('2', 'Anjas Asmara', '3276011203730011', '68.591.0218-412-000', NULL, 'Direktur Kepatuhan', NULL),
('200182003', 'Cece Sulaeman', '3201040902820006', '47.386.326.4.434.000', NULL, 'Manajer Pelaporan & IT', NULL),
('200480004', 'Lisda Meilina', '3271044805800014', '47.386.329.8.404.000', NULL, 'Manajer Dana & Jasa Layanan', NULL),
('200779005', 'Herry Sofiyanto', '3271022903790001', '68.669.438.1.404.000', NULL, 'Kepala SKAI', NULL),
('200781006', 'M. Hasan Basri', '3271061204810002', '68.669.436.5.404.000', 2, 'Manajer Umum', NULL),
('200783007', 'Iin Harlina', '3271046203830014', '68.669.437.3.404.000', NULL, 'Kepala Kantor Kas', NULL),
('200877008', 'Fauzi', '3271021307770002', '68.669.447.2.404.000', NULL, 'Credit Maintenance Officer (Remedial)', NULL),
('200879009', 'Priyatno', '3201011109790015', '68.669.440.7.403.000', NULL, 'Kepala Kantor Kas', NULL),
('200885010', 'Darus Salam', '3271040612850006', '68.669.446.4.404.000', NULL, 'Credit Maintenance Officer (Remedial)', NULL),
('200979011', 'Bambang Sulistyo', '3271042708790017', '68.669.445.6.404.000', NULL, 'Manajer Kredit', NULL),
('200979012', 'Arif Darmawan J.', '3213041302790001', '68.669.443.1.404.000', NULL, 'Credit Marketing ', NULL),
('200984013', 'Rika Dewi Kumalasari', '3271066207840003', '68.669.444.9.404.000', NULL, 'Kepala Satker Kepatuhan & M.R. ', NULL),
('201080014', 'Deni Prasetiya', '3271041607800019', '89.224.514.3.404.000', NULL, 'Funding & Service Banking Officer', NULL),
('201283015', 'Muliasari', '3271047001830015', '44.814.684.5.404.000', 1, 'Administrasi Umum Rumah Tangga / Logistik', NULL),
('201287016', 'Nurmedina', '3271066602870010', '44.814.594.6.404.000', NULL, 'Auditor Madya', NULL),
('201382017', 'Arif Budiman', '3201291508860004', '64.277.371.7.404.000', NULL, 'Credit Maintenance Officer (Remedial)', NULL),
('201384018', 'Akhirianto Soedewo', '3271031407840003', '64.277.364.2.404.000', NULL, 'Human Resources Officer , Legal, Analis Kepatuhan ', NULL),
('201481019', 'R. Harry Darmawan', '3201292206810001', '55.258.669.5.434.000', NULL, 'Collector Dana', NULL),
('201578020', 'Endang Jaya', '3271062504780023', '89.050.368.3.404.000', NULL, 'Credit Maintenance Officer (Remedial)', NULL),
('201579021', 'Irvy Sumiyati', '3271066712790002', '71.978.621.2.404.000', NULL, 'Funding & Service Banking Officer', NULL),
('201579022', 'Imam Munandar', '3271041608790024', '71.946.754.0.404.000', NULL, 'Collector Dana', NULL),
('201583023', 'Donni Alamsyah', '3271010608830013', '87.059.332.4.404.000', NULL, 'Kepala Kantor Kas', NULL),
('201584025', 'Shendy Surya Perdana', '3202110810840001', '26.813.578.7-405.000', NULL, 'Credit Marketing ', NULL),
('201585026', 'Adelia Ratnasari', '3174094508851001', '71.956.872.7.404.000', NULL, 'Credit Administration Officer', NULL),
('201585027', 'M. Nur Ichwan', '3271040310850013', '71.951.149.5.404.000', NULL, 'Credit Maintenance Officer (Remedial)', NULL),
('201588028', 'Evitarini', '3271054303880004', '67.457.240.9.404.000', NULL, 'Treasury Officer', NULL),
('201589029', 'Seni Mulyani', '3201287011890003', '71.955.029.5.434.000', NULL, 'Customer Service', NULL),
('201590030', 'Zaenal Arifin', '3201292401900006', '54.338.897.9.434.000', NULL, 'Credit Marketing ', NULL),
('201784031', 'Dindin Rusdinar K.', '3271011107840016', '72.517.651.5.404.000', NULL, 'Funding & Service Banking Officer', NULL),
('201788032', 'Yusuf Ginanjar Rekawiguna', '3201290311880001', '71.865.428.8.404.000', NULL, 'Credit Marketing ', NULL),
('201789033', 'Cipto Perdana', '3271031701890013', '95.764.462.8-404.000', NULL, 'Credit Marketing ', NULL),
('201792034', 'Ahmad Hadi Mahalli', '3271030112920001', '82.507.812.4.404.000', NULL, 'Account Officer', NULL),
('201793036', 'Endah Novita Susianti', '3201294611930006', '76.541.860.3.434.000', NULL, 'Credit Administration Officer', NULL),
('201890037', 'R. Achmad Juarsa', '3271052605900001', '71.394.386.8.404.000', NULL, 'Analis Manajemen Risiko, APU-PPT & Strategi Anti F', NULL),
('201894038', 'Siti Komala', '3271026812940005', '85.260.400.8.404.000', NULL, 'Teller', NULL),
('202075040', 'Engkus', '3271040908750017', '41.577.497.5-404.000', NULL, 'Funding & Service Banking Officer', NULL),
('202076041', 'Ujang Mukti', '3201282510760002', '41.552.539.3-434.000', NULL, 'Collector Dana', NULL),
('202091042', 'Agung Pangestu', '3201292809910001', '80.566.131.1-434.000', NULL, 'Credit Marketing ', NULL),
('202091043', 'Rizky Fauzan Munandar', '3271043009910006', '75.954.969.4-404.000', NULL, 'Credit Marketing ', NULL),
('202092045', 'Dina Yuliastuty', '3271054307920009', '86.969.168.3-404.000', 1, 'Finance Officer', NULL),
('202092046', 'Egi Gustian', '3271060708920002', '72.108.779.9-404.000', NULL, 'Credit Marketing ', NULL),
('202094048', 'Nenden Apisa', '3276064209940003', '46.981.325.7-412.000', NULL, 'Account Officer', NULL),
('202095049', 'Niluh Intan Permatasari', '3271064812950005', '82.789.680.4-404.000', NULL, 'Funding & Service Banking Officer', NULL),
('202095050', 'Leni Marlina', '3271064409950003', '85.273.377.3.404.000', NULL, 'Teller', NULL),
('202095051', 'Rizky Gumelar', '3271041611950013', '82.184.902.3-404-000', NULL, 'Hardware IT Officer & Programer', NULL),
('202097052', 'Fuji Astuti', '3271045908970009', '91.586.694.1-404.000', NULL, 'Teller', NULL),
('202097053', 'Nia Kaniyati', '3271045210970012', '73.237.097.8-404.000', NULL, 'Credit Administration Officer', NULL),
('202396054', 'Aprillia Purwanti', '3271044804960007', '94.991.740.5-404.000', NULL, 'Teller', NULL),
('202690055', 'Iwan Kurniawan', '01011044804960000', '94.991.111.1-000.000', 1, 'Finance Officer', NULL),
('3', 'Bhima Irsi Faliandri', '3275081612720019', '09.263.872.5-432.000', NULL, 'Direktur Operasional', NULL),
('PKWT', 'Wemphy Jeliansah', '3271032803870003', '46.189.083.2.404.000', NULL, 'Teller', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pekerjaan`
--

CREATE TABLE `pekerjaan` (
  `nomor_urut_pegawai` varchar(15) NOT NULL,
  `status_pegawai` enum('Pegawai Tetap','Pegawai Kontrak','Pegawai Harian Lepas/Honorer','Pegawai Bulanan','Pegawai Alih Daya') DEFAULT NULL,
  `jabatan` varchar(100) DEFAULT NULL,
  `id_divisi` int(11) DEFAULT NULL,
  `tmt_pegawai` date DEFAULT NULL,
  `masa_kerja` varchar(50) DEFAULT NULL,
  `pangkat` varchar(25) DEFAULT NULL,
  `grade` varchar(4) DEFAULT NULL,
  `no_rekening` varchar(30) DEFAULT NULL,
  `golongan_pajak` varchar(10) DEFAULT NULL,
  `periode_kenaikan_gapok` date DEFAULT NULL,
  `periode_kenaikan_grade` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pekerjaan`
--

INSERT INTO `pekerjaan` (`nomor_urut_pegawai`, `status_pegawai`, `jabatan`, `id_divisi`, `tmt_pegawai`, `masa_kerja`, `pangkat`, `grade`, `no_rekening`, `golongan_pajak`, `periode_kenaikan_gapok`, `periode_kenaikan_grade`) VALUES
('1', NULL, 'Direktur Utama', NULL, '0000-00-00', '-', '-', '-', NULL, NULL, '0000-00-00', '0000-00-00'),
('2', NULL, 'Direktur Kepatuhan', NULL, '0000-00-00', '-', '-', '-', NULL, NULL, '0000-00-00', '0000-00-00'),
('200182003', 'Pegawai Tetap', 'Manajer Pelaporan & IT', 6, '2001-12-31', ' 23tahun11bulan ', 'Pengarah Bank Muda', 'G.6', NULL, NULL, '2026-01-02', '0000-00-00'),
('200480004', 'Pegawai Tetap', 'Manajer Dana & Jasa Layanan', 5, '2004-01-02', ' 21tahun11bulan ', 'Pengarah Bank Madya', 'G.7', NULL, NULL, '2026-01-02', '0000-00-00'),
('200779005', 'Pegawai Tetap', 'Kepala SKAI', 2, '2007-08-01', ' 18tahun4bulan ', 'Pengarah Bank Madya', 'G.7', NULL, NULL, '2027-08-01', '0000-00-00'),
('200781006', 'Pegawai Tetap', 'Manajer Umum', 1, '2007-08-01', ' 18tahun4bulan ', 'Pengarah Bank Madya', 'G.7', NULL, NULL, '2027-08-01', '0000-00-00'),
('200783007', 'Pegawai Tetap', 'Kepala Kantor Kas', 5, '2007-08-01', ' 18tahun4bulan ', 'Pengarah Bank Muda', 'G.6', NULL, NULL, '2026-08-01', '0000-00-00'),
('200877008', 'Pegawai Tetap', 'Collector Dana', 5, '2008-11-01', ' 17tahun1bulan ', 'Pengarah Bank Pratama', 'G.5', NULL, NULL, '2027-01-02', '0000-00-00'),
('200879009', 'Pegawai Tetap', 'Kepala Kantor Kas', 5, '2008-11-01', ' 17tahun1bulan ', 'Pengarah Bank Muda', 'G.6', NULL, NULL, '2027-01-02', '0000-00-00'),
('200885010', 'Pegawai Tetap', 'Credit Maintenance Officer (Remedial)', 3, '2008-11-01', ' 17tahun1bulan ', 'Pengarah Bank Pratama', 'G.5', NULL, NULL, '2023-11-01', '0000-00-00'),
('200979011', 'Pegawai Tetap', 'Manajer Kredit', 3, '2009-05-01', ' 16tahun7bulan ', 'Pengarah Bank Muda', 'G.7', NULL, NULL, '2027-07-01', '0000-00-00'),
('200979012', 'Pegawai Tetap', 'Credit Marketing ', 3, '2009-05-01', ' 16tahun7bulan ', 'Pengarah Bank Pratama', 'G.5', NULL, NULL, '2025-05-01', '0000-00-00'),
('200984013', 'Pegawai Tetap', 'Kepala Satker Kepatuhan & M.R. ', 4, '2009-05-01', ' 16tahun7bulan ', 'Pengarah Bank Muda', 'G.7', NULL, NULL, '2027-05-01', '0000-00-00'),
('201080014', 'Pegawai Tetap', 'Funding & Service Banking Officer', 5, '2010-01-04', ' 15tahun11bulan ', 'Pelaksana Bank Utama', 'G.4', NULL, NULL, '2027-01-02', '0000-00-00'),
('201283015', 'Pegawai Tetap', 'Administrasi Umum Rumah Tangga / Logistik', 1, '2012-01-02', ' 13tahun11bulan ', 'Pelaksana Bank Utama', 'G.4', NULL, NULL, '2026-01-02', '0000-00-00'),
('201287016', 'Pegawai Tetap', 'Auditor Madya', 2, '2012-01-02', ' 13tahun11bulan ', 'Pengarah Bank Muda', 'G.6', NULL, NULL, '2026-01-02', '0000-00-00'),
('201382017', 'Pegawai Tetap', 'Credit Maintenance Officer (Remedial)', 3, '2013-01-02', ' 12tahun11bulan ', 'Pelaksana Bank Madya', 'G.3', NULL, NULL, '2024-01-02', '0000-00-00'),
('201384018', 'Pegawai Tetap', 'Human Resources Officer , Legal, Analis Kepatuhan dan Integritas Pelaporan Keuangan', 4, '2013-01-02', ' 12tahun11bulan ', 'Pengarah Bank Muda', 'G.6', NULL, NULL, '2027-01-02', '0000-00-00'),
('201481019', 'Pegawai Tetap', 'Collector Dana', 5, '2014-01-02', ' 11tahun11bulan ', 'Pengarah Bank Pratama', 'G.5', NULL, NULL, '2026-01-02', '0000-00-00'),
('201578020', 'Pegawai Tetap', 'Credit Maintenance Officer (Remedial)', 3, '2015-01-02', ' 10tahun11bulan ', 'Pelaksana Bank Muda', 'G.2', NULL, NULL, '2026-01-02', '0000-00-00'),
('201579021', 'Pegawai Tetap', 'Funding & Service Banking Officer', 5, '2015-01-02', ' 10tahun11bulan ', 'Pengarah Bank Pratama', 'G.5', NULL, NULL, '2027-01-02', '0000-00-00'),
('201579022', 'Pegawai Tetap', 'Collector Dana', 5, '2015-01-02', ' 10tahun11bulan ', 'Pelaksana Bank Muda', 'G.2', NULL, NULL, '2026-01-02', '0000-00-00'),
('201583023', 'Pegawai Tetap', 'Kepala Kantor Kas', 5, '2015-01-02', ' 10tahun11bulan ', 'Pengarah Bank Pratama', 'G.5', NULL, NULL, '2027-01-02', '0000-00-00'),
('201584025', 'Pegawai Tetap', 'Credit Marketing ', 3, '2016-01-04', ' 9tahun11bulan ', 'Pelaksana Bank Utama', 'G.4', NULL, NULL, '2026-01-04', '0000-00-00'),
('201585026', 'Pegawai Tetap', 'Credit Administration Officer', 3, '2015-01-02', ' 10tahun11bulan ', 'Pengarah Bank Pratama', 'G.5', NULL, NULL, '2027-01-02', '0000-00-00'),
('201585027', 'Pegawai Tetap', 'Credit Maintenance Officer (Remedial)', 3, '2015-01-02', ' 10tahun11bulan ', 'Pelaksana Bank Madya', 'G.3', NULL, NULL, '2027-01-02', '0000-00-00'),
('201588028', 'Pegawai Tetap', 'Treasury Officer', 5, '2015-01-02', ' 10tahun11bulan ', 'Pengarah Bank Pratama', 'G.5', NULL, NULL, '2027-01-02', '0000-00-00'),
('201589029', 'Pegawai Tetap', 'Customer Service', 5, '2015-01-02', ' 10tahun11bulan ', 'Pengarah Bank Pratama', 'G.5', NULL, NULL, '2027-01-02', '0000-00-00'),
('201590030', 'Pegawai Tetap', 'Credit Marketing ', 3, '2015-01-02', ' 10tahun11bulan ', 'Pengarah Bank Pratama', 'G.5', NULL, NULL, '2027-01-02', '0000-00-00'),
('201784031', 'Pegawai Tetap', 'Funding & Service Banking Officer', 5, '2017-01-03', ' 8tahun11bulan ', 'Pelaksana Bank Utama', 'G.4', NULL, NULL, '2024-01-03', '0000-00-00'),
('201788032', 'Pegawai Tetap', 'Credit Marketing ', 3, '2017-08-01', ' 8tahun4bulan ', 'Pengarah Bank Pratama', 'G.5', NULL, NULL, '2027-01-02', '0000-00-00'),
('201789033', 'Pegawai Tetap', 'Credit Marketing ', 3, '2017-08-01', ' 8tahun4bulan ', 'Pelaksana Bank Madya', 'G.3', NULL, NULL, '2027-01-02', '0000-00-00'),
('201792034', 'Pegawai Tetap', 'Account Officer', 3, '2017-08-01', ' 8tahun4bulan ', 'Pelaksana Bank Utama', 'G.4', NULL, NULL, '2027-08-01', '0000-00-00'),
('201793036', 'Pegawai Tetap', 'Credit Administration Officer', 3, '2017-08-15', ' 8tahun3bulan ', 'Pelaksana Bank Madya', 'G.3', NULL, NULL, '2027-08-01', '0000-00-00'),
('201890037', 'Pegawai Tetap', 'Analis Manajemen Risiko, APU-PPT & Strategi Anti Fraud', 4, '2018-09-19', ' 7tahun2bulan ', 'Pelaksana Bank Utama', 'G.4', NULL, NULL, '2027-01-02', '0000-00-00'),
('201894038', 'Pegawai Tetap', 'Teller', 5, '2018-02-05', ' 7tahun10bulan ', 'Pelaksana Bank Muda', 'G.2', NULL, NULL, '2026-02-05', '0000-00-00'),
('202075040', 'Pegawai Tetap', 'Funding & Service Banking Officer', 5, '2020-08-01', ' 5tahun4bulan ', 'Pelaksana Bank Pratama', 'G.1', NULL, NULL, '2024-08-01', '0000-00-00'),
('202076041', 'Pegawai Tetap', 'Collector Dana', 5, '2020-08-01', ' 5tahun4bulan ', 'Pelaksana Bank Pratama', 'G.1', NULL, NULL, '2026-01-01', '0000-00-00'),
('202091042', 'Pegawai Tetap', 'Credit Marketing ', 3, '2020-01-02', ' 5tahun11bulan ', 'Pelaksana Bank Muda', 'G.2', NULL, NULL, '2024-01-02', '0000-00-00'),
('202091043', 'Pegawai Tetap', 'Credit Marketing ', 3, '2020-01-02', ' 5tahun11bulan ', 'Pelaksana Bank Utama', 'G.4', NULL, NULL, '2026-01-02', '0000-00-00'),
('202092045', 'Pegawai Tetap', 'Finance Officer', 1, '2020-01-02', ' 5tahun11bulan ', 'Pelaksana Bank Utama', 'G.4', NULL, 'TER B-K/2', '2026-01-02', NULL),
('202092046', 'Pegawai Tetap', 'Credit Marketing ', 3, '2020-01-02', ' 5tahun11bulan ', 'Pelaksana Bank Utama', 'G.4', NULL, NULL, '2026-01-02', '0000-00-00'),
('202094048', 'Pegawai Tetap', 'Account Officer', 3, '2020-08-01', ' 5tahun4bulan ', 'Pelaksana Bank Utama', 'G.4', NULL, NULL, '2026-08-01', '0000-00-00'),
('202095049', 'Pegawai Tetap', 'Funding & Service Banking Officer', 5, '2020-01-02', ' 5tahun11bulan ', 'Pelaksana Bank Utama', 'G.4', NULL, NULL, '2026-01-02', '0000-00-00'),
('202095050', 'Pegawai Tetap', 'Teller', 5, '2020-01-02', ' 5tahun11bulan ', 'Pelaksana Bank Utama', 'G.4', NULL, NULL, '2026-01-02', '0000-00-00'),
('202095051', 'Pegawai Tetap', 'Hardware IT Officer & Programer', 6, '2020-08-01', ' 5tahun4bulan ', 'Pelaksana Bank Madya', 'G.3', NULL, NULL, '2026-08-01', '0000-00-00'),
('202097052', 'Pegawai Tetap', 'Teller', 5, '2020-01-02', ' 5tahun11bulan ', 'Pelaksana Bank Madya', 'G.3', NULL, NULL, '2026-01-02', '0000-00-00'),
('202097053', 'Pegawai Tetap', 'Credit Administration Officer', 3, '2020-08-01', ' 5tahun4bulan ', 'Pelaksana Bank Utama', 'G.4', NULL, NULL, '2026-08-01', '0000-00-00'),
('202396054', 'Pegawai Tetap', 'Teller', 5, '2023-01-02', ' 2tahun11bulan ', 'Pelaksana Bank Madya', 'G.3', NULL, NULL, '2025-01-02', '0000-00-00'),
('202690055', 'Pegawai Tetap', 'Finance Officer', 1, '2023-01-02', '2tahun11bulan', 'Pelaksana Bank Madya', 'G.3', NULL, NULL, '2025-01-02', NULL),
('3', NULL, 'Direktur Operasional', NULL, '0000-00-00', '-', '-', '-', NULL, NULL, '0000-00-00', '0000-00-00'),
('PKWT', 'Pegawai Kontrak', 'Teller', 5, '0000-00-00', ' 4tahun5bulan ', '-', '-', NULL, NULL, '0000-00-00', '0000-00-00');

-- --------------------------------------------------------

--
-- Table structure for table `pengajuan_cuti`
--

CREATE TABLE `pengajuan_cuti` (
  `nomor_urut_pegawai` varchar(50) NOT NULL,
  `jenis_cuti` varchar(100) DEFAULT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `jumlah_cuti` int(11) DEFAULT NULL,
  `jatah_periode_hari` int(11) DEFAULT NULL,
  `sisa_cuti` int(11) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `jalur_dokumen_pendukung` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `pengajuan_cuti`
--

INSERT INTO `pengajuan_cuti` (`nomor_urut_pegawai`, `jenis_cuti`, `tanggal_mulai`, `tanggal_selesai`, `jumlah_cuti`, `jatah_periode_hari`, `sisa_cuti`, `keterangan`, `jalur_dokumen_pendukung`, `created_at`, `updated_at`) VALUES
('200781006', 'Cuti Tahunan', '2026-03-26', '2026-03-31', 4, 12, 8, 'dfsdfsfsfs', NULL, '2026-03-08 14:36:48', '2026-03-08 14:36:48'),
('202690055', 'Cuti Tahunan', '2026-03-09', '2026-03-12', 4, 12, 8, 'dsfsfsdfdsfsfs', NULL, '2026-03-06 15:06:50', '2026-03-06 15:06:50');

-- --------------------------------------------------------

--
-- Table structure for table `pengajuan_lembur`
--

CREATE TABLE `pengajuan_lembur` (
  `id_lembur` int(11) NOT NULL,
  `nomor_urut_pegawai` varchar(50) NOT NULL,
  `tanggal_lembur` date NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `total_jam_lembur` varchar(50) DEFAULT NULL,
  `uraian_tugas` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `pengajuan_lembur`
--

INSERT INTO `pengajuan_lembur` (`id_lembur`, `nomor_urut_pegawai`, `tanggal_lembur`, `jam_mulai`, `jam_selesai`, `total_jam_lembur`, `uraian_tugas`, `created_at`, `updated_at`) VALUES
(3, '202690055', '2026-03-07', '09:00:00', '12:00:00', '3 jam 0 menit', 'sfsfsdfsdfsfsf', '2026-03-06 15:09:19', '2026-03-06 15:09:19'),
(5, '200781006', '2026-03-21', '09:00:00', '12:00:00', '3 jam 0 menit', 'asdasdasdadada', '2026-03-08 15:05:58', '2026-03-08 15:05:58');

-- --------------------------------------------------------

--
-- Table structure for table `pengajuan_pangkatgajitunjangan`
--

CREATE TABLE `pengajuan_pangkatgajitunjangan` (
  `id_pengajuan` int(11) NOT NULL,
  `nomor_urut_pegawai` varchar(50) NOT NULL,
  `pangkat` varchar(100) DEFAULT NULL,
  `grade` varchar(50) DEFAULT NULL,
  `jabatan` varchar(150) DEFAULT NULL,
  `unit_kerja` varchar(150) DEFAULT NULL,
  `status_pegawai` varchar(50) DEFAULT NULL,
  `tmt_pegawai` date DEFAULT NULL,
  `jenis_pengajuan` varchar(100) DEFAULT NULL,
  `masa_kerja` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `pengajuan_pangkatgajitunjangan`
--

INSERT INTO `pengajuan_pangkatgajitunjangan` (`id_pengajuan`, `nomor_urut_pegawai`, `pangkat`, `grade`, `jabatan`, `unit_kerja`, `status_pegawai`, `tmt_pegawai`, `jenis_pengajuan`, `masa_kerja`, `created_at`, `updated_at`) VALUES
(4, '200781006', 'Pengarah Bank Madya', 'G.7', 'Manajer Umum', 'Umum', 'Pegawai Tetap', '2007-08-01', 'Tunjangan Keluarga (Anak)', '18tahun4bulan', '2026-03-08 16:52:26', '2026-03-08 16:52:26'),
(2, '202690055', 'Pelaksana Bank Madya', 'G.3', 'Finance Officer', 'Umum', 'Pegawai Tetap', '2023-01-02', 'Tunjangan Keluarga (Anak)', '2tahun11bulan', '2026-03-08 10:03:10', '2026-03-08 10:03:10');

-- --------------------------------------------------------

--
-- Table structure for table `pengajuan_pensiun`
--

CREATE TABLE `pengajuan_pensiun` (
  `id_pengajuan` int(11) NOT NULL,
  `nomor_urut_pegawai` varchar(50) NOT NULL,
  `nama_pegawai` varchar(255) DEFAULT NULL,
  `pangkat` varchar(100) DEFAULT NULL,
  `grade` varchar(50) DEFAULT NULL,
  `jabatan` varchar(100) DEFAULT NULL,
  `unit_kerja` varchar(255) DEFAULT NULL,
  `status_pegawai` varchar(100) DEFAULT NULL,
  `tmt_pegawai` date DEFAULT NULL,
  `jenis_pengajuan` varchar(100) DEFAULT NULL,
  `masa_kerja` varchar(100) DEFAULT NULL,
  `tmt_pensiun` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `pengajuan_pensiun`
--

INSERT INTO `pengajuan_pensiun` (`id_pengajuan`, `nomor_urut_pegawai`, `nama_pegawai`, `pangkat`, `grade`, `jabatan`, `unit_kerja`, `status_pegawai`, `tmt_pegawai`, `jenis_pengajuan`, `masa_kerja`, `tmt_pensiun`, `created_at`) VALUES
(1, '202690055', 'Iwan Kurniawan', 'Pelaksana Bank Madya', 'G.3', 'Finance Officer', 'Umum', 'Pegawai Tetap', '2023-01-02', 'Pensiun Normal', '2tahun11bulan', '2081-01-01', '2026-03-06 15:12:50'),
(5, '200781006', 'M. Hasan Basri', 'Pengarah Bank Madya', 'G.7', 'Manajer Umum', 'Umum', 'Pegawai Tetap', '2007-08-01', 'Pensiun Normal', '18tahun4bulan', '2063-07-31', '2026-03-08 16:46:38');

-- --------------------------------------------------------

--
-- Table structure for table `punishment`
--

CREATE TABLE `punishment` (
  `id_punishment` int(11) NOT NULL,
  `nomor_urut_pegawai` varchar(50) NOT NULL,
  `jenis_punishment` varchar(100) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `tanggal_diberikan` date DEFAULT NULL,
  `diberikan_oleh` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reward`
--

CREATE TABLE `reward` (
  `id_reward` int(11) NOT NULL,
  `nomor_urut_pegawai` varchar(50) NOT NULL,
  `jenis_reward` varchar(100) DEFAULT NULL,
  `deskripsi_reward` text DEFAULT NULL,
  `tanggal_diberikan` date DEFAULT NULL,
  `diberikan_oleh` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles_mapping`
--

CREATE TABLE `roles_mapping` (
  `id` int(11) NOT NULL,
  `nup` varchar(50) DEFAULT NULL,
  `level_id` int(11) DEFAULT NULL,
  `id_divisi` int(11) DEFAULT NULL,
  `jabatan_id` int(11) DEFAULT NULL,
  `role_name` varchar(255) NOT NULL,
  `route_name` varchar(255) NOT NULL,
  `priority` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `roles_mapping`
--

INSERT INTO `roles_mapping` (`id`, `nup`, `level_id`, `id_divisi`, `jabatan_id`, `role_name`, `route_name`, `priority`, `created_at`, `updated_at`) VALUES
(1, NULL, 2, 5, 20, 'Manajer Dana & Jasa Layanan', 'manager.dashboardmanager', 1, NULL, NULL),
(2, NULL, 2, 3, 21, 'Manajer Kredit', 'manager.dashboardmanager', 1, NULL, NULL),
(3, NULL, 2, 6, 22, 'Manajer Pelaporan & IT', 'manager.dashboardmanager', 1, NULL, NULL),
(4, NULL, 2, 1, 23, 'Manajer Umum', 'manager.dashboardmanager', 1, NULL, NULL),
(5, NULL, 2, 2, 19, 'Kepala SKAI', 'manager.dashboardmanager', 1, NULL, NULL),
(6, NULL, 2, 4, 18, 'Kepala Satker Kepatuhan & M.R.', 'manager.dashboardmanager', 1, NULL, NULL),
(7, NULL, 1, NULL, NULL, 'Pegawai', 'pegawai.dashboard', 10, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('VS6PmrxKIOndodHxQCO3yFqkT8PcrDBBYBmlZTuV', 200781006, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiYTFWNGVvdHhCN05Ea3VZSTNsZmd3bnNadEVRNU1UWUYyY3JVbjhMWiI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjU0OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvbWFuYWdlci9wZWdhd2FpL2RldGFpbC8yMDI2OTAwNTUiO3M6NToicm91dGUiO3M6MjI6Im1hbmFnZXIucGVnYXdhaS5kZXRhaWwiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7czo5OiIyMDA3ODEwMDYiO30=', 1773126844),
('ZNMUxyfzkElY2qrmiZTnLSZjve60krveLf0MwpSF', 200781006, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiS3hobGdHQ0w2QjJ3bGtDOHUyOXdCU0xVNGVFeHBkMk5mbzFDdzFJWiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9tYW5hZ2VyL3BlZ2F3YWkiO3M6NToicm91dGUiO3M6MjE6Im1hbmFnZXIucGVnYXdhaWRpdmlzaSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtzOjk6IjIwMDc4MTAwNiI7czoyMjoiUEhQREVCVUdCQVJfU1RBQ0tfREFUQSI7YTowOnt9fQ==', 1773119073);

-- --------------------------------------------------------

--
-- Table structure for table `sub_jenis_cuti_penting`
--

CREATE TABLE `sub_jenis_cuti_penting` (
  `id` int(11) NOT NULL,
  `id_jenis_cuti` int(11) NOT NULL,
  `nama_sub_jenis` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sub_jenis_cuti_penting`
--

INSERT INTO `sub_jenis_cuti_penting` (`id`, `id_jenis_cuti`, `nama_sub_jenis`) VALUES
(1, 9, 'Menikahkan anak'),
(2, 9, 'Mengkhitankan anak'),
(3, 9, 'Merawat Anggota Keluarga Sakit'),
(4, 9, 'Istri Melahirkan'),
(5, 9, 'Keluarga Meninggal'),
(6, 9, 'Keluarga tertimpa musibah/Bencana Alam');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `nomor_urut_pegawai` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `level_akses` enum('administrator','dirut','direksi','manager','pegawai') NOT NULL DEFAULT 'pegawai',
  `remember_token` varchar(100) DEFAULT NULL,
  `id_divisi` int(11) DEFAULT NULL,
  `jabatan_id` int(11) DEFAULT NULL,
  `level_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`nomor_urut_pegawai`, `name`, `email`, `email_verified_at`, `password`, `level_akses`, `remember_token`, `id_divisi`, `jabatan_id`, `level_id`, `created_at`, `updated_at`) VALUES
('200781006', 'M. Hasan Basri', 'm.kurniawan.bondes@gmail.com', NULL, '$2y$12$kLlIHySiThhzRu.Es.KQAeY4GMUQ6sRbFkeC5F7oJ.naYsXOcRxY6', 'pegawai', NULL, 1, 23, 2, '2026-03-04 07:43:01', '2026-03-04 07:43:01'),
('200979011', 'Bambang Sulistyo', 'saitokunjimmy@gmail.com', NULL, '$2y$12$qla734TYMVG4pqZdW3uTNeDbnK7cvMHTqOefiuF21g7uTHuYlNUfy', 'pegawai', NULL, 3, 21, 2, '2026-03-04 08:57:20', '2026-03-04 08:57:20'),
('200984013', 'Rika Dewi Kumalasari', 'rideonexide@gmail.com', NULL, '$2y$12$bLdB/44hbL8lA0XEivJ4mO4QPHP7a2zuafS8XNk3dHmUKPfkgOMWC', 'pegawai', NULL, 4, 18, 2, '2026-03-04 07:51:16', '2026-03-04 07:51:16'),
('202690055', 'Iwan Kurniawan', 'triyantokurniawan3@gmail.com', NULL, '$2y$12$R7HggQVlx6xcbN7Y7.dD9u6Mj7NK7TFzV7pozaQTeGCEnD8rmKud.', 'pegawai', NULL, 1, 13, 1, '2026-03-04 06:49:25', '2026-03-04 06:49:25');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `berita`
--
ALTER TABLE `berita`
  ADD PRIMARY KEY (`id_pengumuman`);

--
-- Indexes for table `detail_pribadi`
--
ALTER TABLE `detail_pribadi`
  ADD PRIMARY KEY (`nomor_urut_pegawai`);

--
-- Indexes for table `divisi`
--
ALTER TABLE `divisi`
  ADD PRIMARY KEY (`id_divisi`),
  ADD UNIQUE KEY `kode_divisi` (`kode_divisi`);

--
-- Indexes for table `file_persyaratanpangkatgajitunjangan`
--
ALTER TABLE `file_persyaratanpangkatgajitunjangan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengajuan_pangkatgajitunjangan_id` (`pengajuan_pangkatgajitunjangan_id`),
  ADD KEY `nomor_urut_pegawai` (`nomor_urut_pegawai`);

--
-- Indexes for table `file_persyaratanpensiun`
--
ALTER TABLE `file_persyaratanpensiun`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengajuan_pensiun_id` (`pengajuan_pensiun_id`),
  ADD KEY `nomor_urut_pegawai` (`nomor_urut_pegawai`);

--
-- Indexes for table `jabatan`
--
ALTER TABLE `jabatan`
  ADD PRIMARY KEY (`jabatan_id`),
  ADD UNIQUE KEY `nama_jabatan` (`nama_jabatan`),
  ADD KEY `fk_level_jabatan` (`id_divisi`);

--
-- Indexes for table `jenis_cuti`
--
ALTER TABLE `jenis_cuti`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jenjang_pendidikan`
--
ALTER TABLE `jenjang_pendidikan`
  ADD PRIMARY KEY (`id_jenjang`),
  ADD UNIQUE KEY `kode_jenjang` (`kode_jenjang`);

--
-- Indexes for table `keluarga_pegawai`
--
ALTER TABLE `keluarga_pegawai`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nomor_urut_pegawai` (`nomor_urut_pegawai`);

--
-- Indexes for table `level_jabatan`
--
ALTER TABLE `level_jabatan`
  ADD PRIMARY KEY (`level_id`);

--
-- Indexes for table `log_persetujuan_cuti`
--
ALTER TABLE `log_persetujuan_cuti`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nomor_urut_pegawai` (`nomor_urut_pegawai`),
  ADD KEY `nomor_urut_pegawai_penyetuju` (`nomor_urut_pegawai_penyetuju`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `log_persetujuan_lembur`
--
ALTER TABLE `log_persetujuan_lembur`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lembur_id` (`lembur_id`),
  ADD KEY `nomor_urut_pegawai` (`nomor_urut_pegawai`),
  ADD KEY `nomor_urut_pegawai_penyetuju` (`nomor_urut_pegawai_penyetuju`);

--
-- Indexes for table `log_persetujuan_pangkatgajitunjangan`
--
ALTER TABLE `log_persetujuan_pangkatgajitunjangan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pengajuan` (`id_pengajuan`),
  ADD KEY `nomor_urut_pegawai` (`nomor_urut_pegawai`);

--
-- Indexes for table `log_persetujuan_pensiun`
--
ALTER TABLE `log_persetujuan_pensiun`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pengajuan` (`id_pengajuan`),
  ADD KEY `nomor_urut_pegawai` (`nomor_urut_pegawai`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `pegawai`
--
ALTER TABLE `pegawai`
  ADD PRIMARY KEY (`nomor_urut_pegawai`),
  ADD KEY `fk_level_id` (`level_id`);

--
-- Indexes for table `pekerjaan`
--
ALTER TABLE `pekerjaan`
  ADD PRIMARY KEY (`nomor_urut_pegawai`) USING BTREE,
  ADD KEY `fk_pekerjaan_divisi` (`id_divisi`),
  ADD KEY `id_divisi` (`id_divisi`);

--
-- Indexes for table `pengajuan_cuti`
--
ALTER TABLE `pengajuan_cuti`
  ADD PRIMARY KEY (`nomor_urut_pegawai`),
  ADD KEY `jenis_cuti` (`jenis_cuti`);

--
-- Indexes for table `pengajuan_lembur`
--
ALTER TABLE `pengajuan_lembur`
  ADD PRIMARY KEY (`id_lembur`),
  ADD KEY `nomor_urut_pegawai` (`nomor_urut_pegawai`);

--
-- Indexes for table `pengajuan_pangkatgajitunjangan`
--
ALTER TABLE `pengajuan_pangkatgajitunjangan`
  ADD PRIMARY KEY (`nomor_urut_pegawai`),
  ADD UNIQUE KEY `id_pengajuan` (`id_pengajuan`);

--
-- Indexes for table `pengajuan_pensiun`
--
ALTER TABLE `pengajuan_pensiun`
  ADD PRIMARY KEY (`id_pengajuan`),
  ADD KEY `nomor_urut_pegawai` (`nomor_urut_pegawai`);

--
-- Indexes for table `punishment`
--
ALTER TABLE `punishment`
  ADD PRIMARY KEY (`id_punishment`),
  ADD KEY `nomor_urut_pegawai` (`nomor_urut_pegawai`);

--
-- Indexes for table `reward`
--
ALTER TABLE `reward`
  ADD PRIMARY KEY (`id_reward`),
  ADD KEY `nomor_urut_pegawai` (`nomor_urut_pegawai`);

--
-- Indexes for table `roles_mapping`
--
ALTER TABLE `roles_mapping`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nup` (`nup`),
  ADD KEY `id_divisi` (`id_divisi`),
  ADD KEY `jabatan_id` (`jabatan_id`),
  ADD KEY `fk_roles_level` (`level_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `sub_jenis_cuti_penting`
--
ALTER TABLE `sub_jenis_cuti_penting`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_jenis_cuti` (`id_jenis_cuti`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`nomor_urut_pegawai`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `berita`
--
ALTER TABLE `berita`
  MODIFY `id_pengumuman` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `divisi`
--
ALTER TABLE `divisi`
  MODIFY `id_divisi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `file_persyaratanpangkatgajitunjangan`
--
ALTER TABLE `file_persyaratanpangkatgajitunjangan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `file_persyaratanpensiun`
--
ALTER TABLE `file_persyaratanpensiun`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `jabatan`
--
ALTER TABLE `jabatan`
  MODIFY `jabatan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `jenis_cuti`
--
ALTER TABLE `jenis_cuti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `jenjang_pendidikan`
--
ALTER TABLE `jenjang_pendidikan`
  MODIFY `id_jenjang` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `keluarga_pegawai`
--
ALTER TABLE `keluarga_pegawai`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `level_jabatan`
--
ALTER TABLE `level_jabatan`
  MODIFY `level_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `log_persetujuan_cuti`
--
ALTER TABLE `log_persetujuan_cuti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `log_persetujuan_lembur`
--
ALTER TABLE `log_persetujuan_lembur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `log_persetujuan_pangkatgajitunjangan`
--
ALTER TABLE `log_persetujuan_pangkatgajitunjangan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `log_persetujuan_pensiun`
--
ALTER TABLE `log_persetujuan_pensiun`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pengajuan_lembur`
--
ALTER TABLE `pengajuan_lembur`
  MODIFY `id_lembur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pengajuan_pangkatgajitunjangan`
--
ALTER TABLE `pengajuan_pangkatgajitunjangan`
  MODIFY `id_pengajuan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pengajuan_pensiun`
--
ALTER TABLE `pengajuan_pensiun`
  MODIFY `id_pengajuan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `punishment`
--
ALTER TABLE `punishment`
  MODIFY `id_punishment` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reward`
--
ALTER TABLE `reward`
  MODIFY `id_reward` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles_mapping`
--
ALTER TABLE `roles_mapping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `sub_jenis_cuti_penting`
--
ALTER TABLE `sub_jenis_cuti_penting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pekerjaan`
--
ALTER TABLE `pekerjaan`
  ADD CONSTRAINT `fk_pekerjaan_divisi` FOREIGN KEY (`id_divisi`) REFERENCES `divisi` (`id_divisi`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `roles_mapping`
--
ALTER TABLE `roles_mapping`
  ADD CONSTRAINT `fk_roles_divisi` FOREIGN KEY (`id_divisi`) REFERENCES `divisi` (`id_divisi`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_roles_jabatan` FOREIGN KEY (`jabatan_id`) REFERENCES `jabatan` (`jabatan_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_roles_level` FOREIGN KEY (`level_id`) REFERENCES `level_jabatan` (`level_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
