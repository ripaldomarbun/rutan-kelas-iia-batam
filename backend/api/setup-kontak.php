<?php
// FILE: backend/api/setup-kontak.php
// Setup tabel kontak_info jika belum ada

require_once __DIR__ . '/../includes/helpers.php';
$db = getDB();

$tableCheck = $db->query("SHOW TABLES LIKE 'kontak_info'")->fetch();
if ($tableCheck) {
    echo json_encode(['status' => 'info', 'message' => 'Tabel kontak_info sudah ada']);
    exit;
}

$sql = "CREATE TABLE `kontak_info` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `kode`        VARCHAR(30) NOT NULL UNIQUE,
  `label`       VARCHAR(100) NOT NULL,
  `nilai`       TEXT,
  `icon`        VARCHAR(50),
  `urutan`      TINYINT DEFAULT 0,
  `aktif`       TINYINT(1) DEFAULT 1,
  `updated_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB";

$db->exec($sql);

$stmt = $db->prepare("INSERT INTO `kontak_info` (`kode`, `label`, `nilai`, `icon`, `urutan`, `aktif`) VALUES (?, ?, ?, ?, ?, 1)");
$data = [
    ['alamat', 'Alamat', 'Jl. Raya Trans Barelang, Batam<br>Kepulauan Riau, Indonesia', '📍', 1],
    ['telepon', 'Telepon', '+62 778 393 497', '📞', 2],
    ['email', 'Email', 'humasrutanbatam@gmail.com', '✉️', 3],
    ['whatsapp', 'WhatsApp', '0822-1626-2626', '💬', 4],
    ['maps', 'Google Maps', 'https://maps.google.com/?q=Rutan+Kelas+IIA+Batam', '🗺️', 5],
    ['jam_operasional', 'Jam Operasional', 'Senin – Jumat: 08.00 – 16.00 WIB<br />Kunjungan: Sesuai jadwal yang berlaku', '🕐', 6]
];
foreach ($data as $d) {
    $stmt->execute($d);
}

echo json_encode(['status' => 'success', 'message' => 'Tabel kontak_info berhasil dibuat']);
