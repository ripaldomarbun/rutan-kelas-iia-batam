<?php
// FILE: backend/api/kontak.php
// API untuk CRUD informasi kontak

require_once __DIR__ . '/../includes/helpers.php';
requireLogin();
$db = getDB();

$action = $_GET['action'] ?? 'list';

header('Content-Type: application/json');

if ($action === 'list' || $action === 'all') {
    $stmt = $db->prepare("SELECT * FROM kontak_info WHERE aktif = 1 ORDER BY urutan ASC");
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($action === 'all') {
        echo json_encode(['status' => 'success', 'data' => $data]);
    } else {
        $formatted = [];
        foreach ($data as $row) {
            $formatted[$row['kode']] = [
                'label' => $row['label'],
                'nilai' => $row['nilai'],
                'icon' => $row['icon']
            ];
        }
        echo json_encode(['status' => 'success', 'data' => $formatted]);
    }
    exit;
}

if ($action === 'get') {
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $db->prepare("SELECT * FROM kontak_info WHERE id = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($data ? ['status' => 'success', 'data' => $data] : ['status' => 'error', 'message' => 'Data tidak ditemukan']);
    exit;
}

if ($action === 'tambah' || $action === 'edit') {
    $input = json_decode(file_get_contents('php://input'), true);
    $kode = trim($input['kode'] ?? '');
    $label = trim($input['label'] ?? '');
    $nilai = $input['nilai'] ?? '';
    $icon = trim($input['icon'] ?? '');
    $urutan = (int)($input['urutan'] ?? 0);
    
    if (!$kode || !$label) {
        echo json_encode(['status' => 'error', 'message' => 'Kode dan label wajib diisi']);
        exit;
    }
    
    if ($action === 'tambah') {
        $check = $db->prepare("SELECT id FROM kontak_info WHERE kode = ?");
        $check->execute([$kode]);
        if ($check->fetch()) {
            echo json_encode(['status' => 'error', 'message' => 'Kode sudah ada']);
            exit;
        }
        
        $stmt = $db->prepare("INSERT INTO kontak_info (kode, label, nilai, icon, urutan) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$kode, $label, $nilai, $icon, $urutan]);
        echo json_encode(['status' => 'success', 'message' => 'Data kontak berhasil ditambahkan', 'id' => $db->lastInsertId()]);
    } else {
        $id = (int)($input['id'] ?? 0);
        if (!$id) {
            echo json_encode(['status' => 'error', 'message' => 'ID tidak valid']);
            exit;
        }
        
        $stmt = $db->prepare("UPDATE kontak_info SET kode = ?, label = ?, nilai = ?, icon = ?, urutan = ? WHERE id = ?");
        $stmt->execute([$kode, $label, $nilai, $icon, $urutan, $id]);
        echo json_encode(['status' => 'success', 'message' => 'Data kontak berhasil diperbarui']);
    }
    exit;
}

if ($action === 'hapus') {
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) {
        echo json_encode(['status' => 'error', 'message' => 'ID tidak valid']);
        exit;
    }
    
    $stmt = $db->prepare("DELETE FROM kontak_info WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['status' => 'success', 'message' => 'Data kontak berhasil dihapus']);
    exit;
}

if ($action === 'toggle') {
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) {
        echo json_encode(['status' => 'error', 'message' => 'ID tidak valid']);
        exit;
    }
    
    $stmt = $db->prepare("UPDATE kontak_info SET aktif = NOT aktif WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['status' => 'success', 'message' => 'Status berhasil diubah']);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Action tidak valid']);
