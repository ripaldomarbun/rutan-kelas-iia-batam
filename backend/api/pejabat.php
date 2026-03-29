<?php
// ============================================================
// FILE: backend/api/pejabat.php
// Kelola data dan foto pejabat struktural
// ============================================================

require_once __DIR__ . '/../includes/helpers.php';

header('Content-Type: application/json');
$action = $_GET['action'] ?? '';
$db     = getDB();

if ($action === 'list') {
    $stmt = $db->prepare("SELECT * FROM pejabat ORDER BY urutan ASC");
    $stmt->execute();
    $data = $stmt->fetchAll();
    foreach ($data as &$row) {
        $row['foto_url'] = $row['foto']
            ? UPLOAD_URL . 'pejabat/' . $row['foto']
            : null;
    }
    jsonResponse(true, 'OK', $data);
}

if ($action === 'detail') {
    $kode = bersihkan($_GET['kode'] ?? '');
    if (!$kode) jsonResponse(false, 'Kode tidak valid.');

    $stmt = $db->prepare("SELECT * FROM pejabat WHERE kode = ?");
    $stmt->execute([$kode]);
    $row = $stmt->fetch();
    if (!$row) jsonResponse(false, 'Pejabat tidak ditemukan.');

    $row['foto_url'] = $row['foto'] ? UPLOAD_URL . 'pejabat/' . $row['foto'] : null;
    jsonResponse(true, 'OK', $row);
}

if ($action === 'simpan' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    requireLogin(true);
    requireCsrf();

    $kode       = bersihkan($_POST['kode']       ?? '');
    $nama       = bersihkan($_POST['nama']        ?? '');
    $nip        = bersihkan($_POST['nip']         ?? '');
    $pangkat    = bersihkan($_POST['pangkat']     ?? '');
    $pendidikan = bersihkan($_POST['pendidikan']  ?? '');
    $bio        = bersihkan($_POST['bio']         ?? '');

    if (!$kode) jsonResponse(false, 'Kode pejabat wajib ada.');

    // Upload foto baru jika ada
    $foto = null;
    if (!empty($_FILES['foto']['name'])) {
        // Ambil foto lama
        $r = $db->prepare("SELECT foto FROM pejabat WHERE kode = ?");
        $r->execute([$kode]);
        $fotoLama = $r->fetchColumn();

        $foto = uploadGambar($_FILES['foto'], 'pejabat', $fotoLama ?: null);
        if (!$foto) jsonResponse(false, 'Upload foto gagal.');
    }

    $sql = "UPDATE pejabat SET nama=?, nip=?, pangkat=?, pendidikan=?, bio=?";
    $params = [$nama, $nip, $pangkat, $pendidikan, $bio];
    if ($foto) { $sql .= ", foto=?"; $params[] = $foto; }
    $sql .= " WHERE kode=?"; $params[] = $kode;

    $affected = $db->prepare($sql);
    $affected->execute($params);

    jsonResponse(true, 'Profil pejabat berhasil disimpan.');
}

jsonResponse(false, 'Action tidak valid.');
