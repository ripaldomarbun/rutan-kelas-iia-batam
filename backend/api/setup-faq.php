<?php
require_once __DIR__ . '/../includes/helpers.php';

$db = getDB();

$db->exec("CREATE TABLE IF NOT EXISTS faq (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pertanyaan VARCHAR(255) NOT NULL,
  jawaban TEXT NOT NULL,
  kategori ENUM('kunjungan','umum','layanan') DEFAULT 'umum',
  urutan INT DEFAULT 0,
  aktif TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB");

$check = $db->query("SELECT COUNT(*) FROM faq")->fetchColumn();
if ($check == 0) {
    $faqs = [
        ['Apa jadwal kunjungan Rutan Kelas IIA珠海?', 'Jadwal kunjungan:<br><br><strong>Senin - Kamis:</strong> 08.00 - 12.00 WIT<br><strong>Jumat:</strong> 08.00 - 11.00 WIT<br><strong>Sabtu:</strong> 08.00 - 11.00 WIT<br><strong>Minggu & Hari Libur:</strong> Tutup', 'kunjungan', 1],
        ['Barang apa saja yang boleh dibawa saat kunjungan?', 'Barang yang boleh dibawa:<br>- Pakaian bersih dan layak<br>- Makanan dalam kemasan tertutup<br>- Obat-obatan dengan resep dokter<br>- Alat tulis dan buku<br>- Uang tunai secukupnya', 'kunjungan', 2],
        ['Barang apa saja yang dilarang dibawa masuk?', 'Barang yang dilarang:<br>- Handphone dan perangkat elektronik<br>- Senjata tajam/benda berbahaya<br>- Nakotika dan minuman keras<br>- Obat terlarang tanpa resep<br>- Barang mewah berlebihan', 'kunjungan', 3],
        ['Bagaimana prosedur pendaftaran kunjungan?', 'Prosedur:<br>1. Datang ke Rutan sesuai jadwal<br>2. Lapor ke petugas jaga<br>3. Isi formulir kunjungan & tunjukkan identitas<br>4. Titipkan barang di loker<br>5. Tunggu panggilan petugas', 'kunjungan', 4],
        ['Berapa maksimal durasi kunjungan?', 'Durasi kunjungan maksimal adalah 30 menit setiap sesi. Petugas dapat memperpanjang atau memperpendek sesuai situasi dan kondisi.', 'kunjungan', 5],
        ['Apa saja persyaratan pakaian bagi pengunjung?', 'Pengunjung wajib berpakaian sopan dan rapi. Dilarang mengenakan:<br>- Pakaian ketat atau transparan<br>- Celana pendek<br>- Baju tanpa lengan<br>- Sandal atau sepatu rumah', 'kunjungan', 6],
        ['Apakah bisa mengirim surat ke tahanan?', 'Ya, surat dapat dikirim melalui pos atau diantar langsung ke Rutan. Surat akan diperiksa terlebih dahulu oleh petugas sebelum diberikan kepada tahanan.', 'umum', 7],
        ['Bagaimana cara mengetahui status tahanan?', 'Anda dapat menghubungi pihak Rutan secara langsung melalui telepon atau datang ke lokasi. Siapkan identitas dan hubungan keluarga dengan tahanan.', 'umum', 8]
    ];
    
    $stmt = $db->prepare("INSERT INTO faq (pertanyaan, jawaban, kategori, urutan) VALUES (?, ?, ?, ?)");
    foreach ($faqs as $faq) {
        $stmt->execute($faq);
    }
    echo "FAQ table created and populated!";
} else {
    echo "FAQ table already exists with " . $check . " records.";
}
