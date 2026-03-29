<?php
// ============================================================
// FILE: backend/api/slider.php
// Kelola gambar slider beranda
// ============================================================

require_once __DIR__ . '/../includes/helpers.php';

header('Content-Type: application/json');
$action = $_GET['action'] ?? '';
$db     = getDB();

if ($action === 'list') {
    $stmt = $db->prepare("SELECT * FROM slider WHERE aktif = 1 ORDER BY urutan ASC");
    $stmt->execute();
    $data = $stmt->fetchAll();
    foreach ($data as &$row) {
        $row['url'] = UPLOAD_URL . 'slider/' . $row['gambar'];
    }
    jsonResponse(true, 'OK', $data);
}

if ($action === 'tambah' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    requireLogin(true);
    requireCsrf();
    if (empty($_FILES['gambar']['name'])) jsonResponse(false, 'Pilih file gambar terlebih dahulu.');

    $namaFile = uploadGambar($_FILES['gambar'], 'slider');
    if (!$namaFile) jsonResponse(false, 'Upload gambar gagal.');

    // Urutan otomatis = terakhir + 1
    $maxUrutan = (int)$db->query("SELECT COALESCE(MAX(urutan),0) FROM slider")->fetchColumn();
    $db->prepare("INSERT INTO slider (gambar, urutan) VALUES (?, ?)")->execute([$namaFile, $maxUrutan + 1]);

    jsonResponse(true, 'Slide berhasil ditambahkan.', ['gambar' => $namaFile]);
}

if ($action === 'hapus' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    requireLogin(true);
    requireCsrf();
    $id = (int)($_POST['id'] ?? 0);
    if (!$id) jsonResponse(false, 'ID tidak valid.');

    $stmt = $db->prepare("SELECT gambar FROM slider WHERE id = ?");
    $stmt->execute([$id]);
    $gambar = $stmt->fetchColumn();
    if ($gambar) {
        $path = UPLOAD_PATH . 'slider/' . $gambar;
        if (file_exists($path)) unlink($path);
    }

    $db->prepare("DELETE FROM slider WHERE id = ?")->execute([$id]);
    jsonResponse(true, 'Slide berhasil dihapus.');
}

if ($action === 'urutan' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    requireLogin(true);
    requireCsrf();
    // Terima array urutan: [{"id":1,"urutan":1},{"id":3,"urutan":2},...]
    $data = json_decode(file_get_contents('php://input'), true);
    if (!is_array($data)) jsonResponse(false, 'Data tidak valid.');

    $stmt = $db->prepare("UPDATE slider SET urutan = ? WHERE id = ?");
    foreach ($data as $item) {
        $stmt->execute([(int)$item['urutan'], (int)$item['id']]);
    }
    jsonResponse(true, 'Urutan slide berhasil disimpan.');
}

jsonResponse(false, 'Action tidak valid.');
