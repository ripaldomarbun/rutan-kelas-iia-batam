<?php
// ============================================================
// FILE: backend/api/komitmen.php
// Kelola gambar maklumat
// ============================================================

require_once __DIR__ . '/../includes/helpers.php';

header('Content-Type: application/json');
$action = $_GET['action'] ?? '';
$db     = getDB();

if ($action === 'list') {
    $stmt = $db->query("SELECT * FROM komitmen ORDER BY id ASC");
    $data = $stmt->fetchAll();
    foreach ($data as &$row) {
        $row['gambar_url'] = $row['gambar']
            ? UPLOAD_URL . 'maklumat/' . $row['gambar']
            : null;
    }
    jsonResponse(true, 'OK', $data);
}

if ($action === 'simpan' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    requireLogin(true);
    requireCsrf();

    $kode = bersihkan($_POST['kode'] ?? '');
    if (!in_array($kode, ['maklumat-1', 'maklumat-2'])) jsonResponse(false, 'Kode tidak valid.');
    if (empty($_FILES['gambar']['name'])) jsonResponse(false, 'Pilih file gambar terlebih dahulu.');

    // Ambil gambar lama
    $stmt = $db->prepare("SELECT gambar FROM komitmen WHERE kode = ?");
    $stmt->execute([$kode]);
    $gambarLama = $stmt->fetchColumn();

    $namaFile = uploadGambar($_FILES['gambar'], 'maklumat', $gambarLama ?: null);
    if (!$namaFile) jsonResponse(false, 'Upload gambar gagal.');

    $db->prepare("UPDATE komitmen SET gambar = ? WHERE kode = ?")->execute([$namaFile, $kode]);
    jsonResponse(true, 'Gambar maklumat berhasil diperbarui.', [
        'gambar_url' => UPLOAD_URL . 'maklumat/' . $namaFile
    ]);
}

jsonResponse(false, 'Action tidak valid.');
