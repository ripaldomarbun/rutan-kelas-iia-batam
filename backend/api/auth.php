<?php
// ============================================================
// FILE: backend/api/auth.php
// Login dan Logout admin
//
// POST /backend/api/auth.php?action=login
// POST /backend/api/auth.php?action=logout
// GET  /backend/api/auth.php?action=cek   → cek status login
// ============================================================

require_once __DIR__ . '/../includes/helpers.php';

header('Content-Type: application/json');
startSession();

$action = $_GET['action'] ?? '';

// ─── LOGIN ───────────────────────────
if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Rate Limiting Check
    if (!checkRateLimit('login_attempt')) {
        jsonResponse(false, 'Terlalu banyak percobaan login. Silakan coba lagi dalam 15 menit.');
    }

    $username = bersihkan($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$username || !$password) {
        jsonResponse(false, 'Username dan password wajib diisi.');
    }

    $db   = getDB();
    $stmt = $db->prepare("SELECT * FROM admin WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    // Verifikasi password dengan bcrypt
    if (!$admin || !password_verify($password, $admin['password'])) {
        // Tambahkan delay untuk mencegah brute-force
        sleep(1);
        jsonResponse(false, 'Username atau password salah.');
    }

    // Simpan ke session
    $_SESSION['admin_id']   = $admin['id'];
    $_SESSION['admin_nama'] = $admin['nama'];
    $_SESSION['admin_role'] = $admin['role'];
    session_regenerate_id(true); // cegah session fixation
    
    resetRateLimit('login_attempt'); // Reset rate limit after successful login

    jsonResponse(true, 'Login berhasil.', [
        'nama' => $admin['nama'],
        'role' => $admin['role'],
    ]);
}

// ─── LOGOUT ──────────────────────────
if ($action === 'logout') {
    $_SESSION = [];
    session_destroy();
    jsonResponse(true, 'Logout berhasil.');
}

// ─── CEK STATUS ──────────────────────
if ($action === 'cek') {
    if (!empty($_SESSION['admin_id'])) {
        jsonResponse(true, 'Sudah login.', [
            'nama' => $_SESSION['admin_nama'],
            'role' => $_SESSION['admin_role'],
        ]);
    } else {
        jsonResponse(false, 'Belum login.');
    }
}

// ─── GET CSRF TOKEN ──────────────────────
if ($action === 'csrf') {
    jsonResponse(true, 'OK', ['csrf_token' => getCsrfToken()]);
}

jsonResponse(false, 'Action tidak valid.');
