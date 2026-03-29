<?php
// ============================================================
// FILE: backend/api/kunjungan.php
// Kelola jadwal dan informasi layanan kunjungan
// ============================================================

require_once __DIR__ . '/../includes/helpers.php';

header('Content-Type: application/json');
$action = $_GET['action'] ?? '';
$db     = getDB();

// ─── Ambil semua data kunjungan (jadwal + info teks) ─────
if ($action === 'get') {
    $jadwal = $db->query("SELECT * FROM kunjungan_jadwal ORDER BY urutan ASC")->fetchAll();
    $info   = $db->query("SELECT kode, konten FROM kunjungan_info")->fetchAll(PDO::FETCH_KEY_PAIR);
    jsonResponse(true, 'OK', ['jadwal' => $jadwal, 'info' => $info]);
}

// ─── Simpan jadwal hari ──────────────────────────────────
if ($action === 'simpan_jadwal' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    requireLogin(true);
    requireCsrf();

    // Terima JSON array jadwal: [{"id":1,"jam_buka":"08:00","jam_tutup":"12:00","status":"buka"}, ...]
    $data = json_decode(file_get_contents('php://input'), true);
    if (!is_array($data)) jsonResponse(false, 'Data tidak valid.');

    $stmt = $db->prepare("UPDATE kunjungan_jadwal SET jam_buka=?, jam_tutup=?, status=? WHERE id=?");
    foreach ($data as $item) {
        $stmt->execute([
            $item['jam_buka']  ?: null,
            $item['jam_tutup'] ?: null,
            $item['status']    === 'buka' ? 'buka' : 'tutup',
            (int)$item['id'],
        ]);
    }
    jsonResponse(true, 'Jadwal kunjungan berhasil disimpan.');
}

// ─── Simpan teks info (syarat/prosedur/pengumuman/dll) ───
if ($action === 'simpan_info' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    requireLogin(true);
    requireCsrf();

    $kodeValid = ['syarat','prosedur','pengumuman','boleh','dilarang'];
    $kode   = bersihkan($_POST['kode']   ?? '');
    $konten = trim($_POST['konten']      ?? '');

    if (!in_array($kode, $kodeValid)) jsonResponse(false, 'Kode tidak valid.');
    if (!$konten) jsonResponse(false, 'Konten tidak boleh kosong.');

    $db->prepare("UPDATE kunjungan_info SET konten = ? WHERE kode = ?")->execute([$konten, $kode]);
    jsonResponse(true, 'Informasi kunjungan berhasil disimpan.');
}

jsonResponse(false, 'Action tidak valid.');
