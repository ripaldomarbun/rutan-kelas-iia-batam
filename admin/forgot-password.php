<?php
require_once __DIR__ . '/../backend/config/config.php';
require_once __DIR__ . '/../backend/includes/helpers.php';
require_once __DIR__ . '/../backend/includes/mailer.php';

startSession();

$error   = '';
$success = '';
$debugLink = '';

// Proses form kirim email reset
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (!$email) {
        $error = 'Email wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } else {
        $db = getDB();
        $stmt = $db->prepare("SELECT id, nama, email FROM admin WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        if ($admin) {
            // Generate token reset
            $token = bin2hex(random_bytes(32));
            $expired = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Simpan token ke database
            $update = $db->prepare("UPDATE admin SET reset_token = ?, reset_expired = ? WHERE id = ?");
            $update->execute([$token, $expired, $admin['id']]);

            // Kirim email via PHPMailer
            $result = sendResetPasswordEmail($admin['email'], $admin['nama'], $token);
            
            if ($result['success']) {
                $success = $result['message'];
            } else {
                // Jika email gagal, tampilkan link untuk debugging
                $success = 'Terjadi masalah dengan pengiriman email.';
                $debugLink = $result['message']; // Berisi link reset
            }
        } else {
            // Jangan beri tahu email tidak ada (keamanan)
            $success = 'Jika email terdaftar, link reset password akan dikirim. Silakan cek email Anda.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Lupa Password – Admin Rutan Batam</title>
  <link rel="icon" type="image/png" href="../images/logo.png"/>
  <link rel="stylesheet" href="css/admin.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet"/>
</head>
<body>
<div class="login-page">
  <div class="login-bg-pattern"></div>
  <div class="login-card">
    <div class="login-header">
      <div class="login-logo">
        <img src="../images/logo.png" alt="Logo" onerror="this.parentElement.textContent='🔐'"/>
      </div>
      <div class="login-title">Lupa Password</div>
      <div class="login-sub">Masukkan email admin Anda untuk reset password</div>
    </div>

    <?php if ($error): ?>
    <div class="admin-alert alert-danger">⚠️ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
    <div class="admin-alert alert-success" data-auto-close="10">✅ <?= htmlspecialchars($success) ?></div>
    
    <?php if (isset($debugLink)): ?>
    <div style="margin-top:1rem; padding:1rem; background:#f0f9ff; border:1px solid #0ea5e9; border-radius:8px; font-size:0.85rem;">
      <strong>🔗 Link Reset (Development):</strong><br>
      <a href="<?= htmlspecialchars($debugLink) ?>" style="color:#0ea5e9; word-break:break-all;"><?= htmlspecialchars($debugLink) ?></a>
    </div>
    <?php endif; ?>
    
    <div style="text-align:center; margin-top:1.5rem;">
      <a href="login.php" class="btn-login" style="display:inline-block; text-decoration:none;">Kembali ke Login</a>
    </div>

    <?php else: ?>
    <form method="POST" action="forgot-password.php">
      <div class="form-group">
        <label class="form-label">Email Admin</label>
        <div class="input-icon-wrap">
          <span class="input-icon">📧</span>
          <input type="email" name="email" class="form-control"
            placeholder="admin@rutanbatam.id" autocomplete="email"
            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required/>
        </div>
      </div>
      <button type="submit" class="btn-login">Kirim Link Reset</button>
    </form>

    <div class="login-footer">
      <a href="login.php" class="login-forgot-link">← Kembali ke Login</a>
    </div>
    <?php endif; ?>
  </div>
</div>
<script src="js/admin.js"></script>
</body>
</html>
