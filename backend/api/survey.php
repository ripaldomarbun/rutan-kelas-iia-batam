<?php
// ============================================================
// FILE: backend/api/survey.php
// CRUD Survey SKM
// ============================================================

require_once __DIR__ . '/../includes/helpers.php';

header('Content-Type: application/json');
$action = $_GET['action'] ?? '';
$db     = getDB();

if ($action === 'list') {
    $tahun = (int)($_GET['tahun'] ?? date('Y'));
    $stmt  = $db->prepare("SELECT * FROM survey_skm WHERE tahun = ? ORDER BY bulan ASC");
    $stmt->execute([$tahun]);
    $data = $stmt->fetchAll();

    // Tambahkan nama bulan dan URL PDF
    foreach ($data as &$row) {
        $row['nama_bulan'] = namaBulan($row['bulan']);
        $row['pdf_url']    = $row['file_pdf'] ? PDF_URL . $row['file_pdf'] : null;
    }
    jsonResponse(true, 'OK', $data);
}

if ($action === 'simpan' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    requireLogin(true);
    requireCsrf();

    $id        = (int)($_POST['id']        ?? 0);
    $bulan     = (int)($_POST['bulan']     ?? 0);
    $tahun     = (int)($_POST['tahun']     ?? date('Y'));
    $responden = (int)($_POST['responden'] ?? 0);
    $nilai     = (float)($_POST['nilai']   ?? 0);

    if (!$bulan || $bulan < 1 || $bulan > 12) jsonResponse(false, 'Bulan tidak valid.');
    if (!$nilai)  jsonResponse(false, 'Nilai SKM wajib diisi.');

    // Hitung mutu otomatis
    $mutu = mutuSKM($nilai);

    // Upload PDF
    $filePdf = null;
    if (!empty($_FILES['pdf']['name'])) {
        $namaBln = strtolower(namaBulan($bulan));
        $namaTarget = "skm-{$namaBln}-{$tahun}.pdf";
        $filePdf = uploadPDF($_FILES['pdf'], $namaTarget);
        if (!$filePdf) jsonResponse(false, 'Upload PDF gagal. Pastikan format dan ukuran sesuai.');
    }

    if ($id) {
        $sql = "UPDATE survey_skm SET bulan=?, tahun=?, responden=?, nilai_skm=?, mutu=?, kinerja=?, updated_at=NOW()";
        $params = [$bulan, $tahun, $responden, $nilai, $mutu['mutu'], $mutu['kinerja']];
        if ($filePdf) { $sql .= ", file_pdf=?"; $params[] = $filePdf; }
        $sql .= " WHERE id=?"; $params[] = $id;
        $db->prepare($sql)->execute($params);
    } else {
        $sql = "INSERT INTO survey_skm (bulan, tahun, responden, nilai_skm, mutu, kinerja, file_pdf)
                VALUES (?,?,?,?,?,?,?)
                ON DUPLICATE KEY UPDATE
                  responden=VALUES(responden), nilai_skm=VALUES(nilai_skm),
                  mutu=VALUES(mutu), kinerja=VALUES(kinerja),
                  file_pdf=COALESCE(VALUES(file_pdf), file_pdf),
                  updated_at=NOW()";
        $db->prepare($sql)->execute([$bulan, $tahun, $responden, $nilai, $mutu['mutu'], $mutu['kinerja'], $filePdf]);
    }

    jsonResponse(true, 'Laporan SKM berhasil disimpan.', $mutu);
}

if ($action === 'hapus' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    requireLogin(true);
    requireCsrf();
    $id = (int)($_POST['id'] ?? 0);
    if (!$id) jsonResponse(false, 'ID tidak valid.');

    // Hapus file PDF
    $stmt = $db->prepare("SELECT file_pdf FROM survey_skm WHERE id = ?");
    $stmt->execute([$id]);
    $filePdf = $stmt->fetchColumn();
    if ($filePdf && file_exists(PDF_PATH . $filePdf)) unlink(PDF_PATH . $filePdf);

    $db->prepare("DELETE FROM survey_skm WHERE id = ?")->execute([$id]);
    jsonResponse(true, 'Laporan SKM berhasil dihapus.');
}

jsonResponse(false, 'Action tidak valid.');
