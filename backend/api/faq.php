<?php
// ============================================================
// FILE: backend/api/faq.php
// API untuk FAQ (Frequently Asked Questions)
// ============================================================

require_once __DIR__ . '/../includes/helpers.php';

header('Content-Type: application/json');
$action = $_GET['action'] ?? '';
$db = getDB();

if ($action === 'list' || empty($action)) {
    $stmt = $db->prepare("SELECT id, pertanyaan, jawaban, kategori, urutan, aktif FROM faq WHERE aktif = 1 ORDER BY urutan ASC");
    $stmt->execute();
    $data = $stmt->fetchAll();
    jsonResponse(true, 'OK', $data);
}

if ($action === 'all') {
    $stmt = $db->prepare("SELECT * FROM faq ORDER BY urutan ASC");
    $stmt->execute();
    $data = $stmt->fetchAll();
    jsonResponse(true, 'OK', $data);
}

if ($action === 'tambah' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    requireLogin(true);
    requireCsrf();
    
    $pertanyaan = bersihkan($_POST['pertanyaan'] ?? '');
    $jawaban = bersihkan($_POST['jawaban'] ?? '');
    $kategori = in_array($_POST['kategori'] ?? '', ['kunjungan', 'umum', 'layanan']) ? $_POST['kategori'] : 'umum';
    
    if (empty($pertanyaan) || empty($jawaban)) {
        jsonResponse(false, 'Pertanyaan dan jawaban wajib diisi.');
    }
    
    $maxUrutan = (int)$db->query("SELECT COALESCE(MAX(urutan),0) FROM faq")->fetchColumn();
    $stmt = $db->prepare("INSERT INTO faq (pertanyaan, jawaban, kategori, urutan) VALUES (?, ?, ?, ?)");
    $stmt->execute([$pertanyaan, $jawaban, $kategori, $maxUrutan + 1]);
    
    jsonResponse(true, 'FAQ berhasil ditambahkan.', ['id' => $db->lastInsertId()]);
}

if ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    requireLogin(true);
    requireCsrf();
    
    $id = (int)($_POST['id'] ?? 0);
    $pertanyaan = bersihkan($_POST['pertanyaan'] ?? '');
    $jawaban = bersihkan($_POST['jawaban'] ?? '');
    $kategori = in_array($_POST['kategori'] ?? '', ['kunjungan', 'umum', 'layanan']) ? $_POST['kategori'] : 'umum';
    $aktif = isset($_POST['aktif']) ? 1 : 0;
    
    if (empty($id) || empty($pertanyaan) || empty($jawaban)) {
        jsonResponse(false, 'Data tidak lengkap.');
    }
    
    $stmt = $db->prepare("UPDATE faq SET pertanyaan = ?, jawaban = ?, kategori = ?, aktif = ? WHERE id = ?");
    $stmt->execute([$pertanyaan, $jawaban, $kategori, $aktif, $id]);
    
    jsonResponse(true, 'FAQ berhasil diperbarui.');
}

if ($action === 'hapus' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    requireLogin(true);
    requireCsrf();
    
    $id = (int)($_POST['id'] ?? 0);
    
    if (empty($id)) {
        jsonResponse(false, 'ID tidak valid.');
    }
    
    $stmt = $db->prepare("DELETE FROM faq WHERE id = ?");
    $stmt->execute([$id]);
    
    jsonResponse(true, 'FAQ berhasil dihapus.');
}

if ($action === 'urutkan' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    requireLogin(true);
    requireCsrf();
    
    $ids = $_POST['ids'] ?? [];
    
    foreach ($ids as $index => $id) {
        $stmt = $db->prepare("UPDATE faq SET urutan = ? WHERE id = ?");
        $stmt->execute([$index + 1, (int)$id]);
    }
    
    jsonResponse(true, 'Urutan FAQ diperbarui.');
}

jsonResponse(false, 'Action tidak valid.');
