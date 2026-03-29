<?php
// ============================================================
// FILE: backend/includes/helpers.php
// Fungsi-fungsi pembantu yang dipakai di seluruh backend
// ============================================================

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// ─────────────────────────────────────
// RESPONSE JSON
// ─────────────────────────────────────

/**
 * Kirim response JSON dan hentikan eksekusi
 */
function jsonResponse(bool $success, string $message, array $data = []): void {
    header('Content-Type: application/json');
    echo json_encode([
        'status'  => $success ? 'success' : 'error',
        'message' => $message,
        'data'    => $data,
    ]);
    exit;
}

// ─────────────────────────────────────
// SESSION & AUTH
// ─────────────────────────────────────

function startSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_name(SESSION_NAME);
        session_set_cookie_params([
            'lifetime' => SESSION_LIFETIME,
            'path'     => '/',
            'secure'   => false, // ganti true jika pakai HTTPS
            'httponly' => true,
            'samesite' => 'Strict',
        ]);
        session_start();
    }
}

/**
 * Cek apakah admin sudah login.
 * Jika belum, redirect ke login atau kirim JSON error.
 */
function requireLogin(bool $apiMode = false): void {
    startSession();
    if (empty($_SESSION['admin_id'])) {
        if ($apiMode) {
            jsonResponse(false, 'Sesi habis. Silakan login kembali.', ['redirect' => '../login.html']);
        } else {
            header('Location: ../login.html');
            exit;
        }
    }
}

/**
 * Ambil data kontak dari database
 */
function getKontak(): array {
    $db = getDB();
    $stmt = $db->prepare("SELECT kode, label, nilai, icon FROM kontak_info WHERE aktif = 1 ORDER BY urutan ASC");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $result = [];
    foreach ($rows as $row) {
        $result[$row['kode']] = $row;
    }
    return $result;
}

/**
 * Buat token CSRF dan simpan di session
 */
function getCsrfToken(): string {
    startSession();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validasi token CSRF dari request
 */
function validateCsrf(string $token): bool {
    startSession();
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Wajib gunakan sebelum operasi POST
 */
function requireCsrf(): void {
    $token = $_POST['csrf_token'] ?? $_POST['csrf_token'] ?? '';
    if (!validateCsrf($token)) {
        jsonResponse(false, 'Token CSRF tidak valid. Refresh halaman dan coba lagi.');
    }
}

/**
 * Rate Limiting - Cegah brute force attack
 * Max 5 percobaan gagal dalam 15 menit
 */
function checkRateLimit(string $action, int $maxAttempts = 5, int $windowMinutes = 15): bool {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $cacheFile = sys_get_temp_dir() . '/rate_limit_' . md5($action . $ip);
    $now = time();
    $window = $windowMinutes * 60;
    
    $attempts = 0;
    if (file_exists($cacheFile)) {
        $data = json_decode(file_get_contents($cacheFile), true);
        if ($data && ($now - $data['time']) < $window) {
            $attempts = $data['attempts'];
        }
    }
    
    if ($attempts >= $maxAttempts) {
        return false; // Rate limit exceeded
    }
    
    $attempts++;
    file_put_contents($cacheFile, json_encode([
        'attempts' => $attempts,
        'time' => $now
    ]));
    
    return true;
}

/**
 * Reset rate limit setelah berhasil login
 */
function resetRateLimit(string $action): void {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $cacheFile = sys_get_temp_dir() . '/rate_limit_' . md5($action . $ip);
    if (file_exists($cacheFile)) {
        unlink($cacheFile);
    }
}

// ─────────────────────────────────────
// UPLOAD FILE
// ─────────────────────────────────────

/**
 * Upload gambar (JPEG/PNG/WEBP) dengan kompresi otomatis
 * Konversi ke WebP untuk ukuran lebih kecil
 *
 * @param array  $file      $_FILES['nama_field']
 * @param string $subfolder 'berita' | 'slider' | 'pejabat' | 'maklumat'
 * @param string|null $hapusLama  nama file lama untuk dihapus (opsional)
 */
function uploadGambar(array $file, string $subfolder, ?string $hapusLama = null): string|false {
    if ($file['error'] !== UPLOAD_ERR_OK) return false;
    if ($file['size'] > MAX_FILE_SIZE)    return false;
    if (!in_array($file['type'], ALLOWED_IMG)) return false;

    // Tentukan ekstensi - convert ke WebP untuk performa lebih baik
    $ext = 'webp'; // Selalu gunakan WebP untuk ukuran lebih kecil

    // Hapus file lama jika ada
    if ($hapusLama) {
        $pathLama = UPLOAD_PATH . $subfolder . '/' . $hapusLama;
        if (file_exists($pathLama)) unlink($pathLama);
    }

    // Buat nama file unik
    $namaFile = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $targetDir = UPLOAD_PATH . $subfolder . '/';
    $targetPath = $targetDir . $namaFile;

    if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

    // Kompres dan konversi ke WebP
    $tmpFile = $file['tmp_name'];
    $info = getimagesize($tmpFile);
    
    $img = null;
    if ($info['mime'] === 'image/jpeg') {
        $img = imagecreatefromjpeg($tmpFile);
    } elseif ($info['mime'] === 'image/png') {
        $img = imagecreatefrompng($tmpFile);
    } elseif ($info['mime'] === 'image/webp') {
        $img = imagecreatefromwebp($tmpFile);
    }
    
    if ($img) {
        // Simpan sebagai WebP dengan kualitas 80%
        imagewebp($img, $targetPath, 80);
        imagedestroy($img);
        return $namaFile;
    }
    
    // Fallback jika gd tidak tersedia
    if (move_uploaded_file($tmpFile, $targetPath)) {
        return $namaFile;
    }
    return false;
}

/**
 * Upload file PDF
 * Kembalikan nama file baru atau false jika gagal
 */
function uploadPDF(array $file, string $namaTarget): string|false {
    if ($file['error'] !== UPLOAD_ERR_OK) return false;
    if ($file['size'] > MAX_PDF_SIZE)     return false;
    if (!in_array($file['type'], ALLOWED_PDF)) return false;

    $targetPath = PDF_PATH . $namaTarget;

    if (!is_dir(PDF_PATH)) mkdir(PDF_PATH, 0755, true);

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $namaTarget;
    }
    return false;
}

// ─────────────────────────────────────
// STRING UTILITIES
// ─────────────────────────────────────

/**
 * Buat slug dari judul berita
 * "Apel Pagi Zona Integritas" → "apel-pagi-zona-integritas"
 */
function buatSlug(string $judul): string {
    // Huruf kecil
    $slug = strtolower($judul);
    // Ganti karakter Indonesia
    $slug = strtr($slug, [
        'á'=>'a','à'=>'a','ä'=>'a','â'=>'a',
        'é'=>'e','è'=>'e','ë'=>'e','ê'=>'e',
        'í'=>'i','ì'=>'i','ï'=>'i','î'=>'i',
        'ó'=>'o','ò'=>'o','ö'=>'o','ô'=>'o',
        'ú'=>'u','ù'=>'u','ü'=>'u','û'=>'u',
    ]);
    // Ganti spasi dan karakter non-alphanumeric dengan -
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug;
}

/**
 * Pastikan slug unik di tabel berita
 */
function slugUnik(PDO $db, string $slug, ?int $kecualiId = null): string {
    $base = $slug;
    $counter = 1;
    while (true) {
        $sql = "SELECT id FROM berita WHERE slug = ?";
        $params = [$slug];
        if ($kecualiId) {
            $sql .= " AND id != ?";
            $params[] = $kecualiId;
        }
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        if (!$stmt->fetch()) break;
        $slug = $base . '-' . $counter++;
    }
    return $slug;
}

/**
 * Nama bulan Indonesia
 */
function namaBulan(int $bulan): string {
    $daftar = [
        1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April',
        5=>'Mei',     6=>'Juni',     7=>'Juli',  8=>'Agustus',
        9=>'September',10=>'Oktober',11=>'November',12=>'Desember',
    ];
    return $daftar[$bulan] ?? '';
}

/**
 * Sanitasi string input (cegah XSS dasar)
 */
function bersihkan(string $input): string {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

/**
 * Mutu SKM otomatis berdasarkan nilai
 */
function mutuSKM(float $nilai): array {
    if ($nilai >= 88.31) return ['mutu' => 'A', 'kinerja' => 'Sangat Baik'];
    if ($nilai >= 76.61) return ['mutu' => 'B', 'kinerja' => 'Baik'];
    if ($nilai >= 65.00) return ['mutu' => 'C', 'kinerja' => 'Kurang Baik'];
    return                      ['mutu' => 'D', 'kinerja' => 'Tidak Baik'];
}

/**
 * Sanitasi HTML untuk output -CEGAH XSS
 */
function sanitizeHtml(string $html): string {
    $allowedTags = '<p><br><b><strong><i><em><u><h1><h2><h3><h4><h5><h6><ul><ol><li><a><img><table><thead><tbody><tr><th><td><blockquote><pre><code>';
    $config = LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOERROR;
    $doc = new DOMDocument();
    $doc->encoding = 'UTF-8';
    libxml_use_internal_errors(true);
    @$doc->loadHTML('<?xml encoding="UTF-8"?><html><body>' . $html . '</body></html>', $config);
    $body = $doc->getElementsByTagName('body')->item(0);
    if (!$body) return '';
    $clean = strip_tags($doc->saveHTML($body), $allowedTags);
    libxml_clear_errors();
    return $clean ?: $html;
}
