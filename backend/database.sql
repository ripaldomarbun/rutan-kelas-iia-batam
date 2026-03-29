-- ============================================================
-- FILE: backend/database.sql
-- Database untuk Website Rutan Kelas IIA Batam
--
-- CARA IMPORT:
-- 1. Buka phpMyAdmin
-- 2. Buat database baru bernama: web-rutan
-- 3. Klik tab "Import" → pilih file ini → klik Go
-- ============================================================
-- ───────────────────────────────────────
-- TABEL: admin (akun login)
-- ────────────────────────────────────────
CREATE TABLE `admin` (
  `id`              INT AUTO_INCREMENT PRIMARY KEY,
  `username`        VARCHAR(50)  NOT NULL UNIQUE,
  `password`        VARCHAR(255) NOT NULL,          -- bcrypt hash
  `email`           VARCHAR(100) NOT NULL,
  `nama`            VARCHAR(100) NOT NULL,
  `role`            ENUM('superadmin','admin') DEFAULT 'admin',
  `reset_token`     VARCHAR(64) DEFAULT NULL,
  `reset_expired`   DATETIME DEFAULT NULL,
  `created_at`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Password default: admin123 (sudah di-hash bcrypt)
INSERT INTO `admin` (`username`, `password`, `email`, `nama`, `role`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@rutanbatam.id', 'Administrator', 'superadmin');

-- ────────────────────────────────────────
-- TABEL: berita
-- ────────────────────────────────────────
CREATE TABLE `berita` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `judul`       VARCHAR(300) NOT NULL,
  `slug`        VARCHAR(320) NOT NULL UNIQUE,  -- URL-friendly: apel-pagi-zona-integritas
  `ringkasan`   TEXT,
  `isi`         LONGTEXT NOT NULL,
  `gambar`      VARCHAR(255),                  -- nama file: berita/1234567890.jpg
  `kategori`    ENUM('kegiatan','pengumuman','prestasi','pembinaan') NOT NULL,
  `status`      ENUM('publish','draft') DEFAULT 'draft',
  `penulis`     VARCHAR(100) DEFAULT 'Humas Rutan Batam',
  `views`       INT DEFAULT 0,
  `tanggal`     DATE,
  `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Contoh data
INSERT INTO `berita` (`judul`, `slug`, `ringkasan`, `isi`, `kategori`, `status`, `penulis`, `views`, `tanggal`) VALUES
('Rutan Kelas IIA Batam Gelar Apel Pagi Zona Integritas',
 'apel-pagi-zona-integritas',
 'Rutan Kelas IIA Batam menggelar apel pagi dalam rangka penguatan komitmen Zona Integritas menuju WBK.',
 '<p>Rutan Kelas IIA Batam kembali menggelar apel pagi dalam rangka penguatan komitmen Zona Integritas menuju Wilayah Bebas dari Korupsi (WBK).</p><p>Kepala Rutan, Fajar Teguh Wibowo, menyampaikan bahwa zona integritas adalah cerminan perilaku nyata dalam melayani masyarakat.</p>',
  'kegiatan', 'publish', 'Humas Rutan Batam', 245, '2026-03-09');

-- ────────────────────────────────────────
-- TABEL: berita_fotos (multiple foto berita)
-- ────────────────────────────────────────
CREATE TABLE `berita_fotos` (
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `berita_id`  INT NOT NULL,
  `foto`       VARCHAR(255) NOT NULL,
  `caption`    VARCHAR(255),
  `urutan`     TINYINT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`berita_id`) REFERENCES `berita`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;
('Pengumuman Perubahan Jadwal Kunjungan Bulan Maret 2026',
 'perubahan-jadwal-kunjungan-maret-2026',
 'Terdapat perubahan jadwal kunjungan pada bulan Maret 2026.',
 '<p>Diberitahukan kepada seluruh masyarakat bahwa terdapat perubahan jadwal kunjungan pada bulan Maret 2026.</p>',
 'pengumuman', 'publish', 'Humas Rutan Batam', 312, '2026-03-05');

-- ────────────────────────────────────────
-- TABEL: survey_skm (Laporan SKM bulanan)
-- ────────────────────────────────────────
CREATE TABLE `survey_skm` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `bulan`       TINYINT NOT NULL,              -- 1–12
  `tahun`       YEAR    NOT NULL,
  `responden`   INT     NOT NULL DEFAULT 0,
  `nilai_skm`   DECIMAL(5,2) NOT NULL,
  `mutu`        ENUM('A','B','C','D') NOT NULL,
  `kinerja`     VARCHAR(50),
  `file_pdf`    VARCHAR(255),                  -- nama file: pdfs/skm-januari-2026.pdf
  `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uq_bulan_tahun` (`bulan`, `tahun`)
) ENGINE=InnoDB;

INSERT INTO `survey_skm` (`bulan`, `tahun`, `responden`, `nilai_skm`, `mutu`, `kinerja`, `file_pdf`) VALUES
(1, 2026, 127, 83.20, 'B', 'Baik', 'skm-januari-2026.pdf'),
(2, 2026, 114, 81.50, 'B', 'Baik', 'skm-februari-2026.pdf'),
(3, 2026, 131, 82.50, 'B', 'Baik', 'skm-maret-2026.pdf');

-- ────────────────────────────────────────
-- TABEL: slider (gambar slider beranda)
-- ────────────────────────────────────────
CREATE TABLE `slider` (
  `id`      INT AUTO_INCREMENT PRIMARY KEY,
  `gambar`  VARCHAR(255) NOT NULL,
  `urutan`  TINYINT DEFAULT 0,
  `aktif`   TINYINT(1) DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO `slider` (`gambar`, `urutan`, `aktif`) VALUES
('slide1.jpg', 1, 1),
('slide2.jpg', 2, 1),
('slide3.jpg', 3, 1);

-- ────────────────────────────────────────
-- TABEL: pejabat
-- ────────────────────────────────────────
CREATE TABLE `pejabat` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `kode`        VARCHAR(50) NOT NULL UNIQUE,   -- 'karutan', 'tata-usaha', dll
  `jabatan`     VARCHAR(150) NOT NULL,
  `nama`        VARCHAR(150),
  `nip`         VARCHAR(30),
  `pangkat`     VARCHAR(100),
  `pendidikan`  VARCHAR(100),
  `bio`         TEXT,
  `foto`        VARCHAR(255),
  `urutan`      TINYINT DEFAULT 0
) ENGINE=InnoDB;

INSERT INTO `pejabat` (`kode`, `jabatan`, `urutan`) VALUES
('karutan',             'Kepala Rumah Tahanan Negara',          1),
('tata-usaha',          'Kepala Sub Bagian Tata Usaha',         2),
('kasubsi-pengelolaan', 'Kepala Sub Seksi Pengelolaan',         3),
('kasubsi-kpr',         'Kepala Sub Seksi Keamanan dan Perawatan', 4),
('kasubsi-peltah',      'Kepala Sub Seksi Pelayanan Tahanan',   5),
('kasubsi-bimgiat',     'Kepala Sub Seksi Bimbingan Kegiatan',  6);

-- ────────────────────────────────────────
-- TABEL: komitmen (gambar maklumat)
-- ────────────────────────────────────────
CREATE TABLE `komitmen` (
  `id`        INT AUTO_INCREMENT PRIMARY KEY,
  `kode`      VARCHAR(30) NOT NULL UNIQUE,     -- 'maklumat-1', 'maklumat-2'
  `gambar`    VARCHAR(255),
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO `komitmen` (`kode`, `gambar`) VALUES
('maklumat-1', 'maklumat-1.jpg'),
('maklumat-2', 'maklumat-2.jpg');

-- ────────────────────────────────────────
-- TABEL: kunjungan_jadwal
-- ────────────────────────────────────────
CREATE TABLE `kunjungan_jadwal` (
  `id`        INT AUTO_INCREMENT PRIMARY KEY,
  `hari`      VARCHAR(30) NOT NULL,            -- 'Senin-Kamis', 'Jumat', dll
  `jam_buka`  TIME,
  `jam_tutup` TIME,
  `status`    ENUM('buka','tutup') DEFAULT 'buka',
  `urutan`    TINYINT DEFAULT 0
) ENGINE=InnoDB;

INSERT INTO `kunjungan_jadwal` (`hari`, `jam_buka`, `jam_tutup`, `status`, `urutan`) VALUES
('Senin – Kamis', '08:00:00', '12:00:00', 'buka',  1),
('Jumat',         '08:00:00', '11:00:00', 'buka',  2),
('Sabtu',         '08:00:00', '11:00:00', 'buka',  3),
('Minggu',        NULL,       NULL,        'tutup', 4);

-- ────────────────────────────────────────
-- TABEL: kunjungan_info (syarat, prosedur, pengumuman)
-- ────────────────────────────────────────
CREATE TABLE `kunjungan_info` (
  `id`      INT AUTO_INCREMENT PRIMARY KEY,
  `kode`    VARCHAR(30) NOT NULL UNIQUE,  -- 'syarat', 'prosedur', 'pengumuman', 'boleh', 'dilarang'
  `konten`  TEXT NOT NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO `kunjungan_info` (`kode`, `konten`) VALUES
('syarat',      'Membawa KTP / Kartu Identitas yang masih berlaku\nMenunjukkan surat pengantar dari RT/RW (bila diperlukan)\nBerpakaian sopan dan rapi\nTidak membawa barang terlarang\nMengisi formulir kunjungan di loket'),
('prosedur',    'Datang ke Rutan sesuai jadwal kunjungan\nLapor ke petugas jaga di pos penjagaan\nIsi formulir kunjungan dan tunjukkan identitas\nTitipkan barang bawaan di loker\nTunggu panggilan dari petugas'),
('pengumuman',  'Kunjungan dapat dibatasi atau ditutup sewaktu-waktu berdasarkan kebijakan Kepala Rutan. Pastikan konfirmasi terlebih dahulu sebelum berkunjung.'),
('boleh',       'Pakaian bersih dan layak pakai\nMakanan dalam kemasan tertutup\nObat-obatan dengan resep dokter\nAlat tulis dan buku\nUang tunai secukupnya'),
('dilarang',    'Handphone dan perangkat elektronik\nNarkoba dan minuman keras\nSenjata tajam / benda berbahaya\nObat terlarang tanpa resep\nBarang mewah berlebihan');

-- ────────────────────────────────────────
-- TABEL: faq (Frequently Asked Questions)
-- ────────────────────────────────────────
CREATE TABLE `faq` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `pertanyaan`  VARCHAR(255) NOT NULL,
  `jawaban`     TEXT NOT NULL,
  `kategori`    ENUM('kunjungan','umum','layanan') DEFAULT 'umum',
  `urutan`      INT DEFAULT 0,
  `aktif`       TINYINT(1) DEFAULT 1,
  `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Contoh data FAQ
INSERT INTO `faq` (`pertanyaan`, `jawaban`, `kategori`, `urutan`, `aktif`) VALUES
('Apa jadwal kunjungan Rutan Kelas IIA珠海?', 'Jadwal kunjungan:<br><br><strong>Senin - Kamis:</strong> 08.00 - 12.00 WIT<br><strong>Jumat:</strong> 08.00 - 11.00 WIT<br><strong>Sabtu:</strong> 08.00 - 11.00 WIT<br><strong>Minggu & Hari Libur:</strong> Tutup', 'kunjungan', 1, 1),
('Barang apa saja yang boleh dibawa saat kunjungan?', 'Barang yang boleh dibawa:<br>- Pakaian bersih dan layak<br>- Makanan dalam kemasan tertutup<br>- Obat-obatan dengan resep dokter<br>- Alat tulis dan buku<br>- Uang tunai secukupnya', 'kunjungan', 2, 1),
('Barang apa saja yang dilarang dibawa masuk?', 'Barang yang dilarang:<br>- Handphone dan perangkat elektronik<br>- Senjata tajam/benda berbahaya<br>- Nakotika dan minuman keras<br>- Obat terlarang tanpa resep<br>- Barang mewah berlebihan', 'kunjungan', 3, 1),
('Bagaimana prosedur pendaftaran kunjungan?', 'Prosedur:<br>1. Datang ke Rutan sesuai jadwal<br>2. Lapor ke petugas jaga<br>3. Isi formulir kunjungan & tunjukkan identitas<br>4. Titipkan barang di loker<br>5. Tunggu panggilan petugas', 'kunjungan', 4, 1),
('Berapa maksimal durasi kunjungan?', 'Durasi kunjungan maksimal adalah 30 menit setiap sesi. Petugas dapat memperpanjang atau memperpendek sesuai situasi dan kondisi.', 'kunjungan', 5, 1),
('Apa saja persyaratan pakaian bagi pengunjung?', 'Pengunjung wajib berpakaian sopan dan rapi. Dilarang mengenakan:<br>- Pakaian ketat atau transparan<br>- Celana pendek<br>- Baju tanpa lengan<br>- Sandal atau sepatu rumah', 'kunjungan', 6, 1),
('Apakah bisa mengirim surat ke tahanan?', 'Ya, surat dapat dikirim melalui pos atau diantar langsung ke Rutan. Surat akan diperiksa terlebih dahulu oleh petugas sebelum diberikan kepada tahanan.', 'umum', 7, 1),
('Bagaimana cara mengetahui status tahanan?', 'Anda dapat menghubungi pihak Rutan secara langsung melalui telepon atau datang ke lokasi. Siapkan identitas dan hubungan keluarga dengan tahanan.', 'umum', 8, 1);
