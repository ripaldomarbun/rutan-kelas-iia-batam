<?php
require_once __DIR__ . '/../backend/config/config.php';
require_once __DIR__ . '/../backend/includes/helpers.php';

startSession();
// Jika sudah login, langsung ke dashboard
if (!empty($_SESSION['admin_id'])) {
    header('Location: dashboard.php'); exit;
}

$error = '';

// Proses login POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$username || !$password) {
        $error = 'Username dan password wajib diisi.';
    } else {
        $db   = getDB();
        $stmt = $db->prepare("SELECT * FROM admin WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id']   = $admin['id'];
            $_SESSION['admin_nama'] = $admin['nama'];
            $_SESSION['admin_role'] = $admin['role'];
            session_regenerate_id(true);
            header('Location: dashboard.php'); exit;
        } else {
            sleep(1); // anti brute-force
            $error = 'Username atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login Admin – Rutan Kelas IIA Batam</title>
  <link rel="stylesheet" href="css/admin.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet"/>
</head>
<body>
<div class="login-page">
  <div class="login-bg-pattern"></div>
  <div class="login-card">
    <div class="login-header">
      <div class="login-logo">
        <img src="../images/logo.png" alt="Logo" onerror="this.parentElement.textContent='⚖️'"/>
      </div>
      <div class="login-title">Panel Admin</div>
      <div class="login-title" style="font-size:0.85rem; font-weight:600; color:var(--gold)">Rutan Kelas IIA Batam</div>
      <div class="login-sub">Masuk untuk mengelola konten website</div>
    </div>

    <?php if ($error): ?>
    <div class="admin-alert alert-danger">🔒 <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
      <div class="form-group">
        <label class="form-label">Username</label>
        <div class="input-icon-wrap">
          <span class="input-icon">👤</span>
          <input type="text" name="username" class="form-control"
            placeholder="Masukkan username" autocomplete="username"
            value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required/>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Password</label>
        <div class="input-icon-wrap">
          <span class="input-icon">🔑</span>
          <input type="password" name="password" class="form-control"
            placeholder="Masukkan password" autocomplete="current-password" required/>
          <button type="button" class="toggle-pw">👁️</button>
        </div>
      </div>
      <button type="submit" class="btn-login">Masuk ke Panel Admin</button>
    </form>

    <div class="login-footer">
      <a href="forgot-password.php" class="login-forgot-link">Lupa Password?</a>
    </div>
  </div>
</div>
<script src="js/admin.js"></script>
</body>
</html>
