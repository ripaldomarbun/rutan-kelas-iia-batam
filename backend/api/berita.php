<?php
// ============================================================
// FILE: backend/api/berita.php
// CRUD Berita
//
// GET    ?action=list              → daftar berita (admin)
// GET    ?action=detail&id=5       → detail 1 berita (admin)
// GET    ?action=publik&slug=xxx   → detail 1 berita (publik, +1 view)
// GET    ?action=publik_list       → daftar berita publik (dengan filter)
// POST   ?action=simpan            → tambah/edit berita
// POST   ?action=hapus             → hapus berita
// ============================================================

require_once __DIR__ . '/../includes/helpers.php';

function regenerateSitemap(): void {
    $baseUrl = rtrim(BASE_URL, '/');
    $db = getDB();
    
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    
    $staticPages = [
        '' => '1.0', 'index.php' => '1.0', 'berita' => '0.9', 'detail-berita' => '0.8',
        'sejarah' => '0.8', 'visi-misi' => '0.8', 'struktur' => '0.8', 'tupoksi' => '0.8',
        'pejabat' => '0.8', 'kunjungan' => '0.9', 'survey' => '0.8', 'komitmen' => '0.8',
    ];
    foreach ($staticPages as $page => $priority) {
        $url = $baseUrl . '/' . $page;
        $xml .= '  <url>' . "\n";
        $xml .= '    <loc>' . htmlspecialchars($url) . '</loc>' . "\n";
        $xml .= '    <changefreq>weekly</changefreq>' . "\n";
        $xml .= '    <priority>' . $priority . '</priority>' . "\n";
        $xml .= '  </url>' . "\n";
    }
    
    try {
        $stmt = $db->prepare("SELECT slug, tanggal, updated_at FROM berita WHERE status = 'publish' ORDER BY tanggal DESC LIMIT 100");
        $stmt->execute();
        foreach ($stmt->fetchAll() as $b) {
            $lastmod = $b['updated_at'] && $b['updated_at'] !== '0000-00-00 00:00:00' 
                ? date('c', strtotime($b['updated_at'])) : date('c', strtotime($b['tanggal']));
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . $baseUrl . '/detail-berita?slug=' . urlencode($b['slug']) . '</loc>' . "\n";
            $xml .= '    <lastmod>' . $lastmod . '</lastmod>' . "\n";
            $xml .= '    <changefreq>weekly</changefreq>' . "\n";
            $xml .= '    <priority>0.7</priority>' . "\n";
            $xml .= '  </url>' . "\n";
        }
    } catch (PDOException $e) {}
    
    $xml .= '</urlset>';
    @file_put_contents(__DIR__ . '/../../sitemap.xml', $xml);
}

header('Content-Type: application/json');
$action = $_GET['action'] ?? '';
$db     = getDB();

// ─── DAFTAR BERITA (Admin) ────────────────────────────
if ($action === 'list') {
    requireLogin(true);

    $kat    = $_GET['kategori'] ?? '';
    $status = $_GET['status']   ?? '';
    $cari   = $_GET['cari']     ?? '';

    $sql    = "SELECT id, judul, kategori, status, penulis, views, tanggal FROM berita WHERE 1=1";
    $params = [];

    if ($kat)    { $sql .= " AND kategori = ?"; $params[] = $kat; }
    if ($status) { $sql .= " AND status = ?";   $params[] = $status; }
    if ($cari)   { $sql .= " AND judul LIKE ?";  $params[] = "%$cari%"; }

    $sql .= " ORDER BY created_at DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    jsonResponse(true, 'OK', $stmt->fetchAll());
}

// ─── DETAIL BERITA (Admin) ────────────────────────────
if ($action === 'detail') {
    requireLogin(true);

    $id = (int)($_GET['id'] ?? 0);
    if (!$id) jsonResponse(false, 'ID tidak valid.');

    $stmt = $db->prepare("SELECT * FROM berita WHERE id = ?");
    $stmt->execute([$id]);
    $berita = $stmt->fetch();

    if (!$berita) jsonResponse(false, 'Berita tidak ditemukan.');
    jsonResponse(true, 'OK', $berita);
}

// ─── DETAIL BERITA (Publik) ───────────────────────────
if ($action === 'publik') {
    $slug = bersihkan($_GET['slug'] ?? '');
    if (!$slug) jsonResponse(false, 'Slug tidak valid.');

    $stmt = $db->prepare("SELECT * FROM berita WHERE slug = ? AND status = 'publish'");
    $stmt->execute([$slug]);
    $berita = $stmt->fetch();

    if (!$berita) jsonResponse(false, 'Berita tidak ditemukan.');

    // Tambah views
    $db->prepare("UPDATE berita SET views = views + 1 WHERE id = ?")->execute([$berita['id']]);

    // URL gambar
    if ($berita['gambar']) {
        $berita['gambar_url'] = UPLOAD_URL . 'berita/' . $berita['gambar'];
    }

    jsonResponse(true, 'OK', $berita);
}

// ─── DAFTAR BERITA (Publik) ───────────────────────────
if ($action === 'publik_list') {
    $kat   = bersihkan($_GET['kategori'] ?? '');
    $limit = min((int)($_GET['limit'] ?? 6), 20);
    $page  = max((int)($_GET['page']  ?? 1), 1);
    $offset = ($page - 1) * $limit;

    $sql    = "SELECT id, judul, slug, ringkasan, gambar, kategori, penulis, views, tanggal
               FROM berita WHERE status = 'publish'";
    $params = [];

    if ($kat) { $sql .= " AND kategori = ?"; $params[] = $kat; }
    $sql .= " ORDER BY tanggal DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $list = $stmt->fetchAll();

    // Tambahkan URL gambar ke setiap berita
    foreach ($list as &$b) {
        $b['gambar_url'] = $b['gambar']
            ? UPLOAD_URL . 'berita/' . $b['gambar']
            : null;
    }

    // Total untuk paginasi
    $sqlTotal = "SELECT COUNT(*) FROM berita WHERE status = 'publish'" . ($kat ? " AND kategori = ?" : "");
    $stmtTotal = $db->prepare($sqlTotal);
    $stmtTotal->execute($kat ? [$kat] : []);
    $total = (int)$stmtTotal->fetchColumn();

    jsonResponse(true, 'OK', [
        'berita' => $list,
        'total'  => $total,
        'page'   => $page,
        'limit'  => $limit,
    ]);
}

// ─── SIMPAN BERITA (Tambah / Edit) ───────────────────
if ($action === 'simpan' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    requireLogin(true);
    requireCsrf();

    $id        = (int)($_POST['id'] ?? 0);
    $judul     = bersihkan($_POST['judul']    ?? '');
    $ringkasan = bersihkan($_POST['ringkasan']?? '');
    $isi       = $_POST['isi']       ?? '';   // HTML dari editor, tidak di-strip
    $kategori  = bersihkan($_POST['kategori'] ?? '');
    $status    = bersihkan($_POST['status']   ?? 'draft');
    $penulis   = bersihkan($_POST['penulis']  ?? 'Humas Rutan Batam');
    $tanggal   = bersihkan($_POST['tanggal']  ?? date('Y-m-d'));

    // Validasi
    if (!$judul)    jsonResponse(false, 'Judul berita wajib diisi.');
    if (!$isi)      jsonResponse(false, 'Isi berita wajib diisi.');
    if (!$kategori) jsonResponse(false, 'Kategori wajib dipilih.');
    if (!in_array($kategori, ['kegiatan','pengumuman','prestasi','pembinaan']))
        jsonResponse(false, 'Kategori tidak valid.');
    if (!in_array($status, ['publish','draft']))
        jsonResponse(false, 'Status tidak valid.');

    // Buat slug
    $slug = slugUnik($db, buatSlug($judul), $id ?: null);

    // Upload gambar (jika ada)
    $gambar = null;
    if (!empty($_FILES['gambar']['name'])) {
        // Ambil gambar lama untuk dihapus
        $gambarLama = null;
        if ($id) {
            $r = $db->prepare("SELECT gambar FROM berita WHERE id = ?");
            $r->execute([$id]);
            $gambarLama = $r->fetchColumn();
        }
        $gambar = uploadGambar($_FILES['gambar'], 'berita', $gambarLama);
        if (!$gambar) jsonResponse(false, 'Upload gambar gagal. Pastikan format dan ukuran file sesuai.');
    }

    if ($id) {
        // UPDATE
        $sql = "UPDATE berita SET judul=?, slug=?, ringkasan=?, isi=?, kategori=?, status=?, penulis=?, tanggal=?, updated_at=NOW()";
        $params = [$judul, $slug, $ringkasan, $isi, $kategori, $status, $penulis, $tanggal];
        if ($gambar) { $sql .= ", gambar=?"; $params[] = $gambar; }
        $sql .= " WHERE id=?";
        $params[] = $id;
        $db->prepare($sql)->execute($params);
        regenerateSitemap();
        jsonResponse(true, 'Berita berhasil diperbarui.', ['id' => $id, 'slug' => $slug]);
    } else {
        // INSERT
        $sql = "INSERT INTO berita (judul, slug, ringkasan, isi, gambar, kategori, status, penulis, tanggal)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$judul, $slug, $ringkasan, $isi, $gambar, $kategori, $status, $penulis, $tanggal]);
        $newId = (int)$db->lastInsertId();
        regenerateSitemap();
        jsonResponse(true, 'Berita berhasil disimpan.', ['id' => $newId, 'slug' => $slug]);
    }
}

// ─── HAPUS BERITA ─────────────────────────────────────
if ($action === 'hapus' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    requireLogin(true);
    requireCsrf();

    $id = (int)($_POST['id'] ?? 0);
    if (!$id) jsonResponse(false, 'ID tidak valid.');

    // Hapus file gambar
    $stmt = $db->prepare("SELECT gambar FROM berita WHERE id = ?");
    $stmt->execute([$id]);
    $gambar = $stmt->fetchColumn();
    if ($gambar) {
        $path = UPLOAD_PATH . 'berita/' . $gambar;
        if (file_exists($path)) unlink($path);
    }

    $db->prepare("DELETE FROM berita WHERE id = ?")->execute([$id]);
    regenerateSitemap();
    jsonResponse(true, 'Berita berhasil dihapus.');
}

jsonResponse(false, 'Action tidak valid.');
