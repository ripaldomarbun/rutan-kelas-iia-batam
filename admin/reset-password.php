<?php
require_once __DIR__ . '/../backend/config/config.php';
require_once __DIR__ . '/../backend/includes/helpers.php';

startSession();

$error   = '';
$success = '';
$token   = trim($_GET['token'] ?? '');
$valid   = false;
$admin   = null;

// Validasi token
if (!$token) {
    $error = 'Token tidak valid atau sudah kadaluarsa.';
} else {
    $db = getDB();
    $stmt = $db->prepare("SELECT id, nama, email, reset_expired FROM admin WHERE reset_token = ? LIMIT 1");
    $stmt->execute([$token]);
    $admin = $stmt->fetch();

    if (!$admin) {
        $error = 'Token tidak valid atau sudah kadaluarsa.';
    } elseif (strtotime($admin['reset_expired']) < time()) {
        $error = 'Token sudah kadaluarsa. Silakan request reset password baru.';
    } else {
        $valid = true;
    }
}

// Proses reset password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $valid) {
    $password        = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';

    if (!$password || !$passwordConfirm) {
        $error = 'Password dan konfirmasi password wajib diisi.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } elseif ($password !== $passwordConfirm) {
        $error = 'Password dan konfirmasi tidak cocok.';
    } else {
        // Update password dan hapus token
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $update = $db->prepare("UPDATE admin SET password = ?, reset_token = NULL, reset_expired = NULL WHERE id = ?");
        
        if ($update->execute([$hash, $admin['id']])) {
            $success = 'Password berhasil diubah! Anda bisa login dengan password baru.';
            $valid = false; // Sembunyikan form
        } else {
            $error = 'Gagal mengubah password. Silakan coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Reset Password – Admin Rutan Batam</title>
  <link rel="stylesheet" href="css/admin.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet"/>
</head>
<body>
<div class="login-page">
  <div class="login-bg-pattern"></div>
  <div class="login-card">
    <div class="login-header">
      <div class="login-logo">
        <img src="../images/logo.png" alt="Logo" onerror="this.parentElement.textContent='🔑'"/>
      </div>
      <div class="login-title">Reset Password</div>
      <?php if ($admin && $valid): ?>
      <div class="login-sub">Atur password baru untuk <?= htmlspecialchars($admin['nama']) ?></div>
      <?php else: ?>
      <div class="login-sub">Reset password admin panel</div>
      <?php endif; ?>
    </div>

    <?php if ($error): ?>
    <div class="admin-alert alert-danger">⚠️ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
    <div class="admin-alert alert-success">✅ <?= htmlspecialchars($success) ?></div>
    <div style="text-align:center; margin-top:1.5rem;">
      <a href="login.php" class="btn-login" style="display:inline-block; text-decoration:none;">Login Sekarang</a>
    </div>
    <?php endif; ?>

    <?php if ($valid && !$success): ?>
    <form method="POST" action="reset-password.php?token=<?= htmlspecialchars($token) ?>">
      <div class="form-group">
        <label class="form-label">Password Baru</label>
        <div class="input-icon-wrap">
          <span class="input-icon">🔒</span>
          <input type="password" name="password" class="form-control"
            placeholder="Minimal 6 karakter" autocomplete="new-password" required/>
          <button type="button" class="toggle-pw">👁️</button>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Konfirmasi Password</label>
        <div class="input-icon-wrap">
          <span class="input-icon">🔒</span>
          <input type="password" name="password_confirm" class="form-control"
            placeholder="Ulangi password baru" autocomplete="new-password" required/>
          <button type="button" class="toggle-pw">👁️</button>
        </div>
      </div>
      <button type="submit" class="btn-login">Simpan Password Baru</button>
    </form>
    <?php endif; ?>

    <?php if (!$valid && !$success): ?>
    <div style="text-align:center; margin-top:1.5rem;">
      <a href="forgot-password.php" class="btn-login" style="display:inline-block; text-decoration:none;">Request Reset Baru</a>
    </div>
    <?php endif; ?>

    <div class="login-footer">
      <a href="login.php" class="login-forgot-link">← Kembali ke Login</a>
    </div>
  </div>
</div>
<script src="js/admin.js"></script>
</body>
</html>
