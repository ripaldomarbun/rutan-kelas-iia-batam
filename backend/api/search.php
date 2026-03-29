<?php
// ============================================================
// FILE: backend/api/search.php
// Search API untuk berita
// ============================================================

require_once __DIR__ . '/../includes/helpers.php';

header('Content-Type: application/json');
$db = getDB();

$query = bersihkan($_GET['q'] ?? '');
$limit = min((int)($_GET['limit'] ?? 10), 20);

if (strlen($query) < 2) {
    jsonResponse(false, 'Kata kunci minimal 2 karakter.');
}

$stmt = $db->prepare("
    SELECT id, judul, slug, ringkasan, gambar, kategori, tanggal 
    FROM berita 
    WHERE status = 'publish' AND (judul LIKE ? OR ringkasan LIKE ? OR isi LIKE ?)
    ORDER BY tanggal DESC 
    LIMIT ?
");
$searchTerm = "%$query%";
$stmt->execute([$searchTerm, $searchTerm, $searchTerm, $limit]);
$results = $stmt->fetchAll();

foreach ($results as &$r) {
    $r['gambar_url'] = $r['gambar'] 
        ? BASE_URL . '/backend/uploads/berita/' . htmlspecialchars($r['gambar'])
        : BASE_URL . '/images/berita-1.jpg';
}

jsonResponse(true, 'OK', [
    'query' => $query,
    'total' => count($results),
    'results' => $results
]);